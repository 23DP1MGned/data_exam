<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payments\StorePaymentRequest;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\TrainingSession;
use App\Models\User;
use App\Services\PaymentService;
use App\Services\SessionTemplateService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly SessionTemplateService $sessionTemplateService
    )
    {
    }

    public function index(Request $request)
    {
        $this->sessionTemplateService->ensureUpcomingSessionsGenerated();

        $user = $request->user();
        $paymentsQuery = Payment::query()->with(['parent', 'child', 'items.session.group']);

        if ($user->role === User::ROLE_PARENT) {
            $paymentsQuery->where('parent_id', $user->id);
        } elseif ($user->role === User::ROLE_CHILD) {
            $paymentsQuery->where('child_id', $user->id);
        }

        $payments = $paymentsQuery->get();

        $relevantChildIds = match ($user->role) {
            User::ROLE_PARENT => $user->children()->pluck('users.id'),
            User::ROLE_CHILD => collect([$user->id]),
            default => User::query()->where('role', User::ROLE_CHILD)->pluck('id'),
        };

        $paidSessionKeys = PaymentItem::query()
            ->where('type', 'session')
            ->whereHas('payment', function ($builder) use ($relevantChildIds) {
                $builder
                    ->where('status', 'paid')
                    ->whereIn('child_id', $relevantChildIds);
            })
            ->get()
            ->map(fn (PaymentItem $item) => $item->session_id.'-'.$item->payment->child_id)
            ->all();

        $dueSessions = TrainingSession::query()
            ->with(['group.coach', 'group.children'])
            ->where('status', 'planned')
            ->whereHas('group.children', fn ($builder) => $builder->whereIn('users.id', $relevantChildIds))
            ->get();

        $dueTrainings = $dueSessions
            ->flatMap(function (TrainingSession $session) use ($relevantChildIds, $paidSessionKeys) {
                if (! $this->shouldDisplayDueTraining($session)) {
                    return collect();
                }

                return $session->group->children
                    ->whereIn('id', $relevantChildIds)
                    ->filter(fn ($child) => ! in_array($session->id.'-'.$child->id, $paidSessionKeys, true))
                    ->map(function ($child) use ($session) {
                        $parent = $child->parents()->first();
                        $deadline = $this->resolvePaymentDeadline($session);
                        $visibleFrom = $this->resolvePendingVisibleFrom($session);

                        return [
                            'id' => $session->id.'-'.$child->id,
                            'session_id' => $session->id,
                            'parent_id' => $parent?->id,
                            'parent_name' => $parent ? trim($parent->name.' '.$parent->surname) : null,
                            'child_id' => $child->id,
                            'child_name' => trim($child->name.' '.$child->surname),
                            'name' => $session->title ?: $session->group->name,
                            'date' => $session->date->format('d M'),
                            'deadline' => $deadline->format('d M'),
                            'deadline_at' => $deadline->toISOString(),
                            'visible_from_at' => $visibleFrom->toISOString(),
                            'category' => $session->group->display_name,
                            'group' => $session->group->display_name,
                            'trainer' => trim(($session->group->coach->name ?? '').' '.($session->group->coach->surname ?? '')),
                            'amount' => (float) (($session->price ?: $session->group->price) ?: 25),
                            'status' => $this->resolveDueStatus($session),
                            'overdue_days' => now()->greaterThan($deadline) ? $deadline->diffInDays(now()) : 0,
                        ];
                    });
            })
            ->values();

        $dueActivity = $dueTrainings->map(function (array $item) {
            return [
                'id' => 'due-'.$item['id'],
                'name' => $item['child_name'] ?: $item['name'],
                'date' => $item['date'],
                'amount' => (float) $item['amount'],
                'method' => '',
                'status' => $item['status'],
                'detail' => $item['status'] === 'Overdue'
                    ? 'Payment overdue · '.$item['name'].($item['parent_name'] ? ' · Parent: '.$item['parent_name'] : '')
                    : 'Waiting for payment · '.$item['name'].($item['parent_name'] ? ' · Parent: '.$item['parent_name'] : ''),
                'sort_value' => strtotime(
                    $item['status'] === 'Overdue'
                        ? ($item['deadline_at'] ?? 'now')
                        : ($item['visible_from_at'] ?? 'now')
                ),
            ];
        });

        $paymentActivity = $payments->map(function (Payment $payment) {
            $childName = trim(($payment->child->name ?? '').' '.($payment->child->surname ?? ''));
            $parentName = trim(($payment->parent->name ?? '').' '.($payment->parent->surname ?? ''));
            $firstItem = $payment->items->first();
            $itemLabel = $firstItem?->session?->title
                ?: $firstItem?->session?->group?->display_name
                ?: ($firstItem?->type === 'month' ? 'Monthly payment' : 'Payment item');
            $activityTimestamp = $payment->updated_at ?? $payment->created_at;
            $detailPrefix = match ($payment->status) {
                'refunded' => 'Refund action',
                'failed' => 'Failed payment',
                'pending' => 'Pending payment',
                default => 'Payment action',
            };

            return [
                'id' => 'payment-'.$payment->id,
                'name' => $childName ?: $parentName ?: $itemLabel,
                'date' => $activityTimestamp?->format('d M') ?? now()->format('d M'),
                'amount' => (float) $payment->amount,
                'method' => $payment->method,
                'status' => ucfirst($payment->status),
                'detail' => $detailPrefix.' · '.$itemLabel.($parentName ? ' · Parent: '.$parentName : ''),
                'sort_value' => $activityTimestamp?->timestamp ?? 0,
            ];
        });

        $recentActivity = $dueActivity
            ->merge($paymentActivity)
            ->sortByDesc('sort_value')
            ->map(function (array $item) {
                unset($item['sort_value']);
                return $item;
            })
            ->values();

        $paidItems = $payments
            ->where('status', 'paid')
            ->flatMap(function (Payment $payment) {
                return $payment->items->map(function ($item) {
                    return [
                        'category' => $item->session?->group?->name ?? ucfirst((string) $item->type),
                        'price' => (float) $item->price,
                    ];
                });
            });

        $paidTotal = $paidItems->sum('price');
        $spendingBreakdown = $paidItems
            ->groupBy('category')
            ->map(function ($items, $category) use ($paidTotal) {
                $amount = $items->sum('price');

                return [
                    'category' => $category,
                    'amount' => $amount,
                    'percentage' => $paidTotal > 0 ? round(($amount / $paidTotal) * 100).'%' : '0%',
                ];
            })
            ->sortByDesc('amount')
            ->values();

        return $this->success([
            'summary' => [
                'total_paid' => $payments->where('status', 'paid')->sum('amount'),
                'pending' => $dueTrainings->where('status', 'Pending')->sum('amount'),
                'overdue' => $dueTrainings->where('status', 'Overdue')->sum('amount'),
            ],
            'account_balance' => (float) ($user->parentProfile?->account_balance ?? 0),
            'due_trainings' => $dueTrainings,
            'recent_activity' => $recentActivity->values(),
            'spending_breakdown' => $spendingBreakdown->values(),
            'payments' => $payments->map(fn (Payment $payment) => $this->formatPayment($payment))->values(),
        ]);
    }

    public function store(StorePaymentRequest $request)
    {
        $user = $request->user();

        if (! $user->hasRole([User::ROLE_ADMIN, User::ROLE_PARENT])) {
            return $this->error('Forbidden.', [], 403);
        }

        if ($user->role === User::ROLE_PARENT && ! $user->children()->where('users.id', $request->validated('child_id'))->exists()) {
            return $this->error('Forbidden.', [], 403);
        }

        $payment = $this->paymentService->create($request->validated(), $user->id);

        return $this->success($this->formatPayment($payment->load(['parent', 'child', 'items.session.group'])), 'Payment created.', 201);
    }

    public function show(Request $request, Payment $payment)
    {
        $user = $request->user();

        if (
            $user->role !== User::ROLE_ADMIN
            && $payment->parent_id !== $user->id
            && $payment->child_id !== $user->id
        ) {
            return $this->error('Forbidden.', [], 403);
        }

        return $this->success($this->formatPayment($payment->load(['parent', 'child', 'items.session.group'])));
    }

    public function refund(Request $request, Payment $payment)
    {
        if ($request->user()->role !== User::ROLE_ADMIN) {
            return $this->error('Forbidden.', [], 403);
        }

        $payment = $this->paymentService->refund($payment);

        return $this->success($this->formatPayment($payment), 'Payment refunded.');
    }

    private function formatPayment(Payment $payment): array
    {
        return [
            'id' => $payment->id,
            'parent_id' => $payment->parent_id,
            'child_id' => $payment->child_id,
            'parent_name' => trim(($payment->parent->name ?? '').' '.($payment->parent->surname ?? '')),
            'child_name' => trim(($payment->child->name ?? '').' '.($payment->child->surname ?? '')),
            'amount' => (float) $payment->amount,
            'status' => $payment->status,
            'method' => $payment->method,
            'transaction_id' => $payment->transaction_id,
            'items' => $payment->items->map(fn ($item) => [
                'id' => $item->id,
                'type' => $item->type,
                'session_id' => $item->session_id,
                'month' => $item->month,
                'price' => (float) $item->price,
                'session_name' => $item->session?->title ?: $item->session?->group?->display_name,
            ])->values(),
            'created_at' => $payment->created_at?->toISOString(),
        ];
    }

    private function resolveDueStatus(TrainingSession $session): string
    {
        return now()->greaterThan($this->resolvePaymentDeadline($session))
            ? 'Overdue'
            : 'Pending';
    }

    private function resolvePaymentDeadline(TrainingSession $session)
    {
        return $session->date->copy()->subDay()->endOfDay();
    }

    private function shouldDisplayDueTraining(TrainingSession $session): bool
    {
        $visibleFrom = $this->resolvePendingVisibleFrom($session);

        return now()->greaterThanOrEqualTo($visibleFrom);
    }

    private function resolvePendingVisibleFrom(TrainingSession $session)
    {
        return $this->resolvePaymentDeadline($session)->copy()->subDays(7)->startOfDay();
    }
}
