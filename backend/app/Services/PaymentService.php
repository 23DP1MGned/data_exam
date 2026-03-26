<?php

namespace App\Services;

use App\Models\Group;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\PaymentMonthCoverage;
use App\Models\SessionCancellationCredit;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function __construct(
        private readonly NotificationService $notificationService
    ) {
    }

    public function create(array $validated, int $parentId): Payment
    {
        return DB::transaction(function () use ($validated, $parentId) {
            $normalizedItems = $this->normalizeItems($validated, $parentId);
            $amount = collect($normalizedItems)->sum('price');
            $status = $validated['status'] ?? 'paid';

            $payment = Payment::create([
                'parent_id' => $parentId,
                'child_id' => $validated['child_id'],
                'amount' => $amount,
                'status' => $status,
                'method' => $validated['method'],
                'transaction_id' => $status === 'paid' ? Str::uuid()->toString() : null,
            ]);

            foreach ($normalizedItems as $item) {
                $paymentItem = $payment->items()->create([
                    'type' => $item['type'],
                    'session_id' => $item['session_id'] ?? null,
                    'month' => $item['month'] ?? null,
                    'price' => $item['price'],
                ]);

                if ($item['type'] === 'month') {
                    $payment->monthCoverages()->create([
                        'payment_item_id' => $paymentItem->id,
                        'child_id' => $validated['child_id'],
                        'group_id' => $item['group_id'],
                        'month' => $item['month'],
                        'covered_sessions_count' => $item['covered_sessions_count'],
                        'amount' => $item['price'],
                    ]);
                }
            }

            if ($payment->status === 'paid' && strtolower($payment->method) === 'account balance') {
                $parent = User::query()->with('parentProfile')->findOrFail($parentId);

                if (! $parent->parentProfile || (float) $parent->parentProfile->account_balance < $amount) {
                    throw ValidationException::withMessages([
                        'method' => ['Insufficient account balance.'],
                    ]);
                }

                $parent->parentProfile->decrement('account_balance', $amount);
            }

            if ($payment->status === 'paid') {
                $this->notificationService->notifyPaymentSuccessful($payment);
            }

            return $payment->load(['items.monthCoverage.group', 'monthCoverages.group']);
        });
    }

    public function refund(Payment $payment): Payment
    {
        return DB::transaction(function () use ($payment) {
            if ($payment->status !== 'paid') {
                throw ValidationException::withMessages([
                    'status' => ['Only paid payments can be refunded.'],
                ]);
            }

            $payment->update([
                'status' => 'refunded',
            ]);

            $parent = User::query()->with('parentProfile')->findOrFail($payment->parent_id);

            if ($parent->parentProfile) {
                $parent->parentProfile->increment('account_balance', $payment->amount);
            }

            $this->notificationService->notifyPaymentRefunded($payment);

            return $payment->fresh()->load(['parent', 'child', 'items.session.group', 'items.monthCoverage.group', 'monthCoverages.group']);
        });
    }

    public function creditCancelledSession(TrainingSession $session): void
    {
        if ($session->status !== 'cancelled') {
            return;
        }

        DB::transaction(function () use ($session) {
            $session->loadMissing(['group.children.parents', 'extraChildren.parents', 'paymentItems.payment', 'paymentItems.payment.parent']);

            foreach ($session->effectiveChildren() as $child) {
                $existingCredit = SessionCancellationCredit::query()
                    ->where('session_id', $session->id)
                    ->where('child_id', $child->id)
                    ->first();

                if ($existingCredit && ! $existingCredit->reversed_at) {
                    continue;
                }

                $singlePaymentItem = PaymentItem::query()
                    ->with(['payment.parent'])
                    ->where('type', 'session')
                    ->where('session_id', $session->id)
                    ->whereHas('payment', function ($builder) use ($child) {
                        $builder
                            ->where('status', 'paid')
                            ->where('child_id', $child->id);
                    })
                    ->latest('id')
                    ->first();

                if ($singlePaymentItem?->payment?->parent) {
                    $this->createCancellationCredit(
                        session: $session,
                        parent: $singlePaymentItem->payment->parent,
                        child: $child,
                        amount: (float) $singlePaymentItem->price,
                        sourceType: 'session',
                        payment: $singlePaymentItem->payment,
                        paymentItem: $singlePaymentItem,
                    );
                    continue;
                }

                $monthKey = $session->date->format('Y-m');
                $coverage = PaymentMonthCoverage::query()
                    ->with(['payment.parent', 'paymentItem'])
                    ->where('child_id', $child->id)
                    ->where('group_id', $session->group_id)
                    ->where('month', $monthKey)
                    ->whereHas('payment', fn ($builder) => $builder->where('status', 'paid'))
                    ->latest('id')
                    ->first();

                if (! $coverage?->payment?->parent) {
                    continue;
                }

                $coverageCredits = SessionCancellationCredit::query()
                    ->where('payment_month_coverage_id', $coverage->id)
                    ->orderBy('id')
                    ->get();

                if ($coverageCredits->count() >= $coverage->covered_sessions_count) {
                    continue;
                }

                $refundedSoFar = (float) $coverageCredits->sum('amount');
                $baseAmount = round((float) $coverage->amount / max(1, (int) $coverage->covered_sessions_count), 2);
                $isLastCoverageRefund = $coverageCredits->count() + 1 >= (int) $coverage->covered_sessions_count;
                $amount = $isLastCoverageRefund
                    ? round(max(0, (float) $coverage->amount - $refundedSoFar), 2)
                    : $baseAmount;

                if ($amount <= 0) {
                    continue;
                }

                $this->createCancellationCredit(
                    session: $session,
                    parent: $coverage->payment->parent,
                    child: $child,
                    amount: $amount,
                    sourceType: 'month',
                    payment: $coverage->payment,
                    paymentItem: $coverage->paymentItem,
                    monthCoverage: $coverage,
                );
            }
        });
    }

    public function reverseCancelledSessionCredit(TrainingSession $session): void
    {
        DB::transaction(function () use ($session) {
            $credits = SessionCancellationCredit::query()
                ->with('parent.parentProfile')
                ->where('session_id', $session->id)
                ->whereNull('reversed_at')
                ->orderBy('id')
                ->get();

            foreach ($credits as $credit) {
                $parent = $credit->parent;
                $balance = (float) ($parent?->parentProfile?->account_balance ?? 0);

                if (! $parent?->parentProfile || $balance < (float) $credit->amount) {
                    throw ValidationException::withMessages([
                        'status' => ['This session cannot be restored because the cancellation credit has already been used from the parent balance.'],
                    ]);
                }
            }

            foreach ($credits as $credit) {
                $parent = $credit->parent;
                $child = $credit->child;

                $parent?->parentProfile?->decrement('account_balance', (float) $credit->amount);
                $credit->update([
                    'reversed_at' => now(),
                ]);

                $this->notificationService->notifyCancellationCreditReversed(
                    $session,
                    $parent,
                    $child,
                    (float) $credit->amount
                );
            }
        });
    }

    private function normalizeItems(array $validated, int $parentId): array
    {
        $items = collect($validated['items'] ?? []);
        $types = $items->pluck('type')->filter()->unique()->values();

        if ($types->count() !== 1) {
            throw ValidationException::withMessages([
                'items' => ['Please pay monthly charges and training sessions separately.'],
            ]);
        }

        $parent = User::query()->findOrFail($parentId);

        if (! $parent->children()->where('users.id', $validated['child_id'])->exists()) {
            throw ValidationException::withMessages([
                'child_id' => ['You can only pay for linked children.'],
            ]);
        }

        return match ($types->first()) {
            'month' => $this->normalizeMonthItems($items, (int) $validated['child_id']),
            default => $this->normalizeSessionItems($items, (int) $validated['child_id']),
        };
    }

    private function normalizeSessionItems(Collection $items, int $childId): array
    {
        $sessionIds = $items
            ->pluck('session_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $sessions = TrainingSession::query()
            ->with(['group.children', 'extraChildren'])
            ->whereIn('id', $sessionIds)
            ->get()
            ->keyBy('id');

        if ($sessions->count() !== $sessionIds->count()) {
            throw ValidationException::withMessages([
                'items' => ['One or more selected trainings were not found.'],
            ]);
        }

        return $sessionIds->map(function (int $sessionId) use ($sessions, $childId) {
            /** @var TrainingSession $session */
            $session = $sessions->get($sessionId);

            if (! in_array($session->status, ['planned', 'completed'], true)) {
                throw ValidationException::withMessages([
                    'items' => ['Cancelled trainings cannot be paid.'],
                ]);
            }

            if (! $session->effectiveChildren()->contains('id', $childId)) {
                throw ValidationException::withMessages([
                    'items' => ['The selected child is not assigned to this training session.'],
                ]);
            }

            if (! $this->canPaySingleSession($session)) {
                throw ValidationException::withMessages([
                    'items' => ['This training is not available for payment yet.'],
                ]);
            }

            $monthKey = $session->date->format('Y-m');

            if ($this->hasPaidMonthlyCoverage($childId, $session->group_id, $monthKey)) {
                throw ValidationException::withMessages([
                    'items' => ['This training month is already covered by a monthly payment.'],
                ]);
            }

            if ($this->hasPaidSingleSession($childId, $session->id)) {
                throw ValidationException::withMessages([
                    'items' => ['This training has already been paid.'],
                ]);
            }

            return [
                'type' => 'session',
                'session_id' => $session->id,
                'month' => null,
                'group_id' => $session->group_id,
                'price' => (float) (($session->price ?: $session->group->price) ?: 0),
                'covered_sessions_count' => 0,
            ];
        })->all();
    }

    private function normalizeMonthItems(Collection $items, int $childId): array
    {
        if ($items->count() !== 1) {
            throw ValidationException::withMessages([
                'items' => ['Monthly payments must be created one group and month at a time.'],
            ]);
        }

        $item = $items->first();
        $groupId = (int) ($item['group_id'] ?? 0);
        $month = (string) ($item['month'] ?? '');
        $group = Group::query()->with('children')->findOrFail($groupId);

        if (! $group->children->contains('id', $childId)) {
            throw ValidationException::withMessages([
                'items' => ['The selected child is not linked to this group.'],
            ]);
        }

        [$monthStart, $monthEnd] = $this->resolveMonthRange($month);

        if (! $this->canPayMonthlyCharge($monthStart)) {
            throw ValidationException::withMessages([
                'items' => ['Monthly payment becomes available 7 days before the month starts.'],
            ]);
        }

        $sessions = TrainingSession::query()
            ->where('group_id', $groupId)
            ->whereIn('status', ['planned', 'completed'])
            ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->orderBy('date')
            ->get();

        if ($sessions->isEmpty()) {
            throw ValidationException::withMessages([
                'items' => ['There are no trainings available for this month in the selected group.'],
            ]);
        }

        if ($this->hasPaidMonthlyCoverage($childId, $groupId, $month)) {
            throw ValidationException::withMessages([
                'items' => ['This month is already covered by a monthly payment.'],
            ]);
        }

        if ($this->hasPaidSingleSessionsForMonth($childId, $groupId, $month, $sessions->pluck('id')->all())) {
            throw ValidationException::withMessages([
                'items' => ['Monthly payment is not available because some trainings in this month were already paid separately.'],
            ]);
        }

        return [[
            'type' => 'month',
            'session_id' => null,
            'month' => $month,
            'group_id' => $groupId,
            'price' => (float) ($group->price ?? 0),
            'covered_sessions_count' => $sessions->count(),
        ]];
    }

    private function resolveMonthRange(string $month): array
    {
        try {
            $monthStart = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        } catch (\Throwable) {
            throw ValidationException::withMessages([
                'items' => ['Month must use YYYY-MM format.'],
            ]);
        }

        return [$monthStart, $monthStart->copy()->endOfMonth()];
    }

    private function canPaySingleSession(TrainingSession $session): bool
    {
        $deadline = $session->date->copy()->subDay()->endOfDay();
        $visibleFrom = $deadline->copy()->subDays(7)->startOfDay();

        return now()->greaterThanOrEqualTo($visibleFrom);
    }

    private function canPayMonthlyCharge(Carbon $monthStart): bool
    {
        $visibleFrom = $monthStart->copy()->subDays(7)->startOfDay();

        return now()->greaterThanOrEqualTo($visibleFrom);
    }

    private function hasPaidMonthlyCoverage(int $childId, int $groupId, string $month): bool
    {
        return PaymentMonthCoverage::query()
            ->where('child_id', $childId)
            ->where('group_id', $groupId)
            ->where('month', $month)
            ->whereHas('payment', fn ($builder) => $builder->where('status', 'paid'))
            ->exists();
    }

    private function hasPaidSingleSession(int $childId, int $sessionId): bool
    {
        return PaymentItem::query()
            ->where('type', 'session')
            ->where('session_id', $sessionId)
            ->whereHas('payment', function ($builder) use ($childId) {
                $builder
                    ->where('status', 'paid')
                    ->where('child_id', $childId);
            })
            ->exists();
    }

    private function hasPaidSingleSessionsForMonth(int $childId, int $groupId, string $month, array $sessionIds): bool
    {
        if (empty($sessionIds)) {
            return false;
        }

        return PaymentItem::query()
            ->where('type', 'session')
            ->whereIn('session_id', $sessionIds)
            ->whereHas('payment', function ($builder) use ($childId) {
                $builder
                    ->where('status', 'paid')
                    ->where('child_id', $childId);
            })
            ->exists();
    }

    private function createCancellationCredit(
        TrainingSession $session,
        User $parent,
        User $child,
        float $amount,
        string $sourceType,
        Payment $payment,
        ?PaymentItem $paymentItem = null,
        ?PaymentMonthCoverage $monthCoverage = null,
    ): void {
        if ($amount <= 0) {
            return;
        }

        $credit = SessionCancellationCredit::query()
            ->where('session_id', $session->id)
            ->where('child_id', $child->id)
            ->first();

        if ($credit) {
            $credit->update([
                'parent_id' => $parent->id,
                'payment_id' => $payment->id,
                'payment_item_id' => $paymentItem?->id,
                'payment_month_coverage_id' => $monthCoverage?->id,
                'source_type' => $sourceType,
                'amount' => $amount,
                'credited_at' => now(),
                'reversed_at' => null,
            ]);
        } else {
            SessionCancellationCredit::query()->create([
                'session_id' => $session->id,
                'parent_id' => $parent->id,
                'child_id' => $child->id,
                'payment_id' => $payment->id,
                'payment_item_id' => $paymentItem?->id,
                'payment_month_coverage_id' => $monthCoverage?->id,
                'source_type' => $sourceType,
                'amount' => $amount,
                'credited_at' => now(),
            ]);
        }

        $parent->loadMissing('parentProfile');
        if ($parent->parentProfile) {
            $parent->parentProfile->increment('account_balance', $amount);
        }

        $this->notificationService->notifyCancellationCreditIssued($session, $parent, $child, $amount);
    }
}
