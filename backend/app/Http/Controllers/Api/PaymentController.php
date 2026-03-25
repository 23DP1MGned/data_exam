<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payments\StorePaymentRequest;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\PaymentMonthCoverage;
use App\Models\TrainingSession;
use App\Models\User;
use App\Services\PaymentService;
use App\Services\SessionTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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

        if (! in_array($user->role, [User::ROLE_ADMIN, User::ROLE_PARENT], true)) {
            return $this->error('Forbidden.', [], 403);
        }

        $paymentsQuery = Payment::query()->with(['parent', 'child', 'items.session.group', 'items.monthCoverage.group', 'monthCoverages.group']);

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

        $paidSessionKeys = $this->resolvePaidSessionKeys($relevantChildIds);
        $paidSessionMonthKeys = $this->resolvePaidSessionMonthKeys($relevantChildIds);
        $paidMonthlyCoverageKeys = $this->resolvePaidMonthlyCoverageKeys($relevantChildIds);

        $dueTrainings = $this->buildSessionDueItems($relevantChildIds, $paidSessionKeys, $paidMonthlyCoverageKeys);
        $dueMonthlyPayments = $this->buildMonthlyDueItems($relevantChildIds, $paidMonthlyCoverageKeys, $paidSessionMonthKeys);
        $allDueItems = $dueTrainings->merge($dueMonthlyPayments)->values();

        $dueActivity = $allDueItems->map(function (array $item) {
            $detail = $item['type'] === 'month'
                ? ($item['status'] === 'Overdue'
                    ? 'Monthly payment overdue · '.$item['group'].' · '.$item['month_label'].($item['parent_name'] ? ' · Parent: '.$item['parent_name'] : '')
                    : 'Monthly payment pending · '.$item['group'].' · '.$item['month_label'].($item['parent_name'] ? ' · Parent: '.$item['parent_name'] : ''))
                : ($item['status'] === 'Overdue'
                    ? 'Payment overdue · '.$item['name'].($item['parent_name'] ? ' · Parent: '.$item['parent_name'] : '')
                    : 'Waiting for payment · '.$item['name'].($item['parent_name'] ? ' · Parent: '.$item['parent_name'] : ''));

            return [
                'id' => 'due-'.$item['id'],
                'child_id' => $item['child_id'] ?? null,
                'name' => $item['child_name'] ?: ($item['type'] === 'month' ? $item['group'] : $item['name']),
                'date' => $item['date'],
                'amount' => (float) $item['amount'],
                'method' => '',
                'status' => $item['status'],
                'detail' => $detail,
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
            $monthCoverage = $firstItem?->monthCoverage;
            $itemLabel = $firstItem?->type === 'month'
                ? trim(($monthCoverage?->group?->display_name ?? 'Monthly payment').' · '.($monthCoverage?->month ?? ''))
                : ($firstItem?->session?->title
                    ?: $firstItem?->session?->group?->display_name
                    ?: 'Payment item');
            $activityTimestamp = $payment->updated_at ?? $payment->created_at;
            $detailPrefix = match ($payment->status) {
                'refunded' => 'Refund action',
                'failed' => 'Failed payment',
                'pending' => 'Pending payment',
                default => 'Payment action',
            };

            return [
                'id' => 'payment-'.$payment->id,
                'child_id' => $payment->child_id,
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
                        'category' => $item->type === 'month'
                            ? ($item->monthCoverage?->group?->name ?? 'Monthly payment')
                            : ($item->session?->group?->name ?? ucfirst((string) $item->type)),
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
                'pending' => $allDueItems->where('status', 'Pending')->sum('amount'),
                'overdue' => $allDueItems->where('status', 'Overdue')->sum('amount'),
            ],
            'account_balance' => (float) ($user->parentProfile?->account_balance ?? 0),
            'due_trainings' => $dueTrainings->values(),
            'due_monthly_payments' => $dueMonthlyPayments->values(),
            'recent_activity' => $recentActivity->values(),
            'spending_breakdown' => $spendingBreakdown->values(),
            'payments' => $payments->map(fn (Payment $payment) => $this->formatPayment($payment))->values(),
        ]);
    }

    public function store(StorePaymentRequest $request)
    {
        $user = $request->user();

        if ($user->role !== User::ROLE_PARENT) {
            return $this->error('Forbidden.', [], 403);
        }

        if (! $user->children()->where('users.id', $request->validated('child_id'))->exists()) {
            return $this->error('Forbidden.', [], 403);
        }

        $payment = $this->paymentService->create($request->validated(), $user->id);

        return $this->success($this->formatPayment($payment->load(['parent', 'child', 'items.session.group', 'items.monthCoverage.group', 'monthCoverages.group'])), 'Payment created.', 201);
    }

    public function show(Request $request, Payment $payment)
    {
        $user = $request->user();

        if (! in_array($user->role, [User::ROLE_ADMIN, User::ROLE_PARENT], true)) {
            return $this->error('Forbidden.', [], 403);
        }

        if (
            $user->role !== User::ROLE_ADMIN
            && $payment->parent_id !== $user->id
            && $payment->child_id !== $user->id
        ) {
            return $this->error('Forbidden.', [], 403);
        }

        return $this->success($this->formatPayment($payment->load(['parent', 'child', 'items.session.group', 'items.monthCoverage.group', 'monthCoverages.group'])));
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
            'payment_type' => $payment->items->first()?->type,
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
                'group_id' => $item->monthCoverage?->group_id ?? $item->session?->group_id,
                'group_name' => $item->monthCoverage?->group?->display_name ?? $item->session?->group?->display_name,
                'covered_sessions_count' => $item->monthCoverage?->covered_sessions_count,
            ])->values(),
            'created_at' => $payment->created_at?->toISOString(),
            'updated_at' => $payment->updated_at?->toISOString(),
        ];
    }

    private function buildSessionDueItems($relevantChildIds, array $paidSessionKeys, array $paidMonthlyCoverageKeys)
    {
        return TrainingSession::query()
            ->with(['group.coach', 'group.children.parents', 'extraChildren.parents'])
            ->whereIn('status', ['planned', 'completed'])
            ->where(function ($builder) use ($relevantChildIds) {
                $builder
                    ->whereHas('group.children', fn ($relation) => $relation->whereIn('users.id', $relevantChildIds))
                    ->orWhereHas('extraChildren', fn ($relation) => $relation->whereIn('users.id', $relevantChildIds));
            })
            ->get()
            ->flatMap(function (TrainingSession $session) use ($relevantChildIds, $paidSessionKeys, $paidMonthlyCoverageKeys) {
                if (! $this->shouldDisplayDueTraining($session)) {
                    return collect();
                }

                $deadline = $this->resolvePaymentDeadline($session);
                $visibleFrom = $this->resolvePendingVisibleFrom($session);

                return $session->effectiveChildren()
                    ->whereIn('id', $relevantChildIds)
                    ->reject(function ($child) use ($session, $paidSessionKeys, $paidMonthlyCoverageKeys) {
                        $sessionKey = $this->makeSessionPaymentKey($session->id, $child->id);
                        $coverageKey = $this->makeCoverageKey($child->id, $session->group_id, $session->date->format('Y-m'));

                        return in_array($sessionKey, $paidSessionKeys, true)
                            || in_array($coverageKey, $paidMonthlyCoverageKeys, true);
                    })
                    ->map(function ($child) use ($session, $deadline, $visibleFrom) {
                        $parent = $child->parents->first();

                        return [
                            'id' => $this->makeSessionPaymentKey($session->id, $child->id),
                            'type' => 'session',
                            'session_id' => $session->id,
                            'parent_id' => $parent?->id,
                            'parent_name' => $parent ? trim($parent->name.' '.$parent->surname) : null,
                            'child_id' => $child->id,
                            'child_name' => trim($child->name.' '.$child->surname),
                            'group_id' => $session->group_id,
                            'name' => $session->title ?: $session->group->name,
                            'date' => $session->date->format('d M'),
                            'month' => $session->date->format('Y-m'),
                            'month_label' => $session->date->format('F Y'),
                            'deadline' => $deadline->format('d M'),
                            'deadline_at' => $deadline->toISOString(),
                            'visible_from_at' => $visibleFrom->toISOString(),
                            'category' => $session->group->display_name,
                            'group' => $session->group->display_name,
                            'trainer' => trim(($session->group->coach->name ?? '').' '.($session->group->coach->surname ?? '')),
                            'amount' => (float) (($session->price ?: $session->group->price) ?: 25),
                            'status' => $this->resolveDueStatus($session),
                            'overdue_days' => now()->greaterThan($deadline) ? $deadline->diffInDays(now()) : 0,
                            'covered_sessions_count' => 1,
                        ];
                    });
            })
            ->values();
    }

    private function buildMonthlyDueItems($relevantChildIds, array $paidMonthlyCoverageKeys, array $paidSessionMonthKeys)
    {
        return TrainingSession::query()
            ->with(['group.coach', 'group.children.parents'])
            ->whereIn('status', ['planned', 'completed'])
            ->whereHas('group.children', fn ($builder) => $builder->whereIn('users.id', $relevantChildIds))
            ->get()
            ->flatMap(function (TrainingSession $session) use ($relevantChildIds) {
                return $session->group->children
                    ->whereIn('id', $relevantChildIds)
                    ->map(function ($child) use ($session) {
                        return [
                            'child' => $child,
                            'session' => $session,
                            'month' => $session->date->format('Y-m'),
                        ];
                    });
            })
            ->groupBy(fn (array $item) => $this->makeCoverageKey($item['child']->id, $item['session']->group_id, $item['month']))
            ->map(function ($items, string $coverageKey) use ($paidMonthlyCoverageKeys, $paidSessionMonthKeys) {
                if (in_array($coverageKey, $paidMonthlyCoverageKeys, true) || in_array($coverageKey, $paidSessionMonthKeys, true)) {
                    return null;
                }

                $first = $items->first();
                $child = $first['child'];
                /** @var TrainingSession $session */
                $session = $first['session'];
                $group = $session->group;
                $monthKey = $first['month'];
                $monthStart = Carbon::createFromFormat('Y-m', $monthKey)->startOfMonth();
                $deadline = $this->resolveMonthlyPaymentDeadline($monthStart);
                $visibleFrom = $this->resolveMonthlyPaymentVisibleFrom($monthStart);

                if (now()->lt($visibleFrom)) {
                    return null;
                }

                $parent = $child->parents->first();
                $uniqueSessions = collect($items)
                    ->pluck('session')
                    ->unique(fn (TrainingSession $candidate) => $candidate->id);

                return [
                    'id' => 'month-'.$coverageKey,
                    'type' => 'month',
                    'session_id' => null,
                    'parent_id' => $parent?->id,
                    'parent_name' => $parent ? trim($parent->name.' '.$parent->surname) : null,
                    'child_id' => $child->id,
                    'child_name' => trim($child->name.' '.$child->surname),
                    'group_id' => $group->id,
                    'name' => 'Monthly payment',
                    'date' => $monthStart->format('M Y'),
                    'month' => $monthKey,
                    'month_label' => $monthStart->format('F Y'),
                    'deadline' => $deadline->format('d M'),
                    'deadline_at' => $deadline->toISOString(),
                    'visible_from_at' => $visibleFrom->toISOString(),
                    'category' => $group->display_name,
                    'group' => $group->display_name,
                    'trainer' => trim(($group->coach->name ?? '').' '.($group->coach->surname ?? '')),
                    'amount' => (float) ($group->price ?? 0),
                    'status' => now()->greaterThan($deadline) ? 'Overdue' : 'Pending',
                    'overdue_days' => now()->greaterThan($deadline) ? $deadline->diffInDays(now()) : 0,
                    'covered_sessions_count' => $uniqueSessions->count(),
                ];
            })
            ->filter()
            ->sortBy(function (array $item) {
                return $item['status'] === 'Overdue'
                    ? ($item['deadline_at'] ?? '')
                    : ($item['visible_from_at'] ?? '');
            })
            ->values();
    }

    private function resolvePaidSessionKeys($relevantChildIds): array
    {
        return PaymentItem::query()
            ->where('type', 'session')
            ->whereHas('payment', function ($builder) use ($relevantChildIds) {
                $builder
                    ->where('status', 'paid')
                    ->whereIn('child_id', $relevantChildIds);
            })
            ->get()
            ->map(fn (PaymentItem $item) => $this->makeSessionPaymentKey($item->session_id, $item->payment->child_id))
            ->values()
            ->all();
    }

    private function resolvePaidSessionMonthKeys($relevantChildIds): array
    {
        return PaymentItem::query()
            ->with('session')
            ->where('type', 'session')
            ->whereHas('payment', function ($builder) use ($relevantChildIds) {
                $builder
                    ->where('status', 'paid')
                    ->whereIn('child_id', $relevantChildIds);
            })
            ->get()
            ->filter(fn (PaymentItem $item) => $item->session)
            ->map(fn (PaymentItem $item) => $this->makeCoverageKey(
                $item->payment->child_id,
                $item->session->group_id,
                $item->session->date->format('Y-m')
            ))
            ->unique()
            ->values()
            ->all();
    }

    private function resolvePaidMonthlyCoverageKeys($relevantChildIds): array
    {
        return PaymentMonthCoverage::query()
            ->whereIn('child_id', $relevantChildIds)
            ->whereHas('payment', fn ($builder) => $builder->where('status', 'paid'))
            ->get()
            ->map(fn (PaymentMonthCoverage $coverage) => $this->makeCoverageKey(
                $coverage->child_id,
                $coverage->group_id,
                $coverage->month
            ))
            ->unique()
            ->values()
            ->all();
    }

    private function makeSessionPaymentKey(?int $sessionId, ?int $childId): string
    {
        return $sessionId.'-'.$childId;
    }

    private function makeCoverageKey(int $childId, int $groupId, string $month): string
    {
        return $childId.'-'.$groupId.'-'.$month;
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

    private function resolveMonthlyPaymentDeadline(Carbon $monthStart)
    {
        return $monthStart->copy()->subDay()->endOfDay();
    }

    private function resolveMonthlyPaymentVisibleFrom(Carbon $monthStart)
    {
        return $monthStart->copy()->subDays(7)->startOfDay();
    }
}
