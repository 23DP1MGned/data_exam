<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payments\StorePaymentRequest;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\TrainingSession;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentService $paymentService)
    {
    }

    public function index(Request $request)
    {
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
                return $session->group->children
                    ->whereIn('id', $relevantChildIds)
                    ->filter(fn ($child) => ! in_array($session->id.'-'.$child->id, $paidSessionKeys, true))
                    ->map(fn ($child) => [
                        'id' => $session->id.'-'.$child->id,
                        'session_id' => $session->id,
                        'child_id' => $child->id,
                        'child_name' => trim($child->name.' '.$child->surname),
                        'name' => $session->group->name,
                        'date' => $session->date->format('d M'),
                        'deadline' => $session->date->copy()->subDay()->format('d M'),
                        'category' => $session->group->name,
                        'group' => $session->group->name,
                        'trainer' => trim(($session->group->coach->name ?? '').' '.($session->group->coach->surname ?? '')),
                        'amount' => (float) ($session->group->price ?: 25),
                        'status' => now()->greaterThan($session->date->copy()->subDay()->endOfDay()) ? 'Overdue' : 'Pending',
                    ]);
            })
            ->values();

        $completedSessionActivity = TrainingSession::query()
            ->with(['group.coach', 'group.children'])
            ->where(function ($builder) {
                $builder
                    ->where('status', 'completed')
                    ->orWhere(function ($inner) {
                        $inner
                            ->where('status', 'planned')
                            ->where('date', '<', now()->toDateString());
                    });
            })
            ->whereHas('group.children', fn ($builder) => $builder->whereIn('users.id', $relevantChildIds))
            ->get()
            ->flatMap(function (TrainingSession $session) use ($relevantChildIds, $paidSessionKeys) {
                return $session->group->children
                    ->whereIn('id', $relevantChildIds)
                    ->map(function ($child) use ($session, $paidSessionKeys) {
                        $isPaid = in_array($session->id.'-'.$child->id, $paidSessionKeys, true);

                        return [
                            'id' => 'session-'.$session->id.'-'.$child->id,
                            'name' => $session->group->name,
                            'date' => $session->date->format('d M'),
                            'amount' => (float) ($session->group->price ?: 25),
                            'method' => '',
                            'status' => $isPaid ? 'Paid' : 'Missed',
                            'detail' => 'Completed training event',
                            'sort_value' => $session->date->copy()->endOfDay()->timestamp,
                        ];
                    });
            });

        $paymentActivity = $payments->map(fn (Payment $payment) => [
                'id' => 'payment-'.$payment->id,
                'name' => $payment->items->first()?->session?->group?->name ?? trim(($payment->child->name ?? '').' '.($payment->child->surname ?? '')),
                'date' => $payment->created_at->format('d M'),
                'amount' => (float) $payment->amount,
                'method' => $payment->method,
                'status' => ucfirst($payment->status),
                'detail' => 'Payment action',
                'sort_value' => $payment->created_at?->timestamp ?? 0,
            ]);

        $recentActivity = $completedSessionActivity
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
                'pending' => $payments->where('status', 'pending')->sum('amount'),
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

    private function formatPayment(Payment $payment): array
    {
        return [
            'id' => $payment->id,
            'parent_id' => $payment->parent_id,
            'child_id' => $payment->child_id,
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
                'session_name' => $item->session?->group?->name,
            ])->values(),
            'created_at' => $payment->created_at?->toISOString(),
        ];
    }
}
