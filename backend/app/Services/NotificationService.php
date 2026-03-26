<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\PaymentMonthCoverage;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class NotificationService
{
    public function send(
        User|int $user,
        string $title,
        string $message,
        string $type,
        ?string $uniqueKey = null,
        array $payload = []
    ): Notification {
        $userId = $user instanceof User ? $user->id : $user;

        if ($uniqueKey) {
            return Notification::query()->updateOrCreate(
                [
                    'user_id' => $userId,
                    'unique_key' => $uniqueKey,
                ],
                [
                    'title' => $title,
                    'message' => $message,
                    'type' => $type,
                    'payload' => $payload ?: null,
                    'is_read' => false,
                ]
            );
        }

        return Notification::query()->create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'payload' => $payload ?: null,
            'is_read' => false,
        ]);
    }

    public function notifyPaymentSuccessful(Payment $payment): void
    {
        $payment->loadMissing(['child', 'parent', 'items.session.group', 'items.monthCoverage.group']);

        $itemSummary = $this->paymentItemSummary($payment);
        $childName = $this->userDisplayName($payment->child);

        $this->send(
            $payment->parent_id,
            'Payment successful',
            "Payment received for {$itemSummary}.",
            'payment'
        );

        $this->send(
            $payment->child_id,
            'Training payment added',
            "A payment was added for {$itemSummary}.",
            'payment'
        );

        if ($payment->parent_id !== $payment->child_id && $childName !== '') {
            $this->send(
                $payment->parent_id,
                'Child payment recorded',
                "{$childName} now has a paid item for {$itemSummary}.",
                'payment'
            );
        }
    }

    public function notifyPaymentRefunded(Payment $payment): void
    {
        $payment->loadMissing(['child', 'parent', 'items.session.group', 'items.monthCoverage.group']);

        $itemSummary = $this->paymentItemSummary($payment);

        $this->send(
            $payment->parent_id,
            'Payment refunded',
            "A refund was issued for {$itemSummary} and returned to your balance.",
            'payment'
        );

        $this->send(
            $payment->child_id,
            'Training payment refunded',
            "A payment linked to {$itemSummary} was refunded.",
            'payment'
        );
    }

    public function notifyCancellationCreditIssued(TrainingSession $session, User $parent, User $child, float $amount): void
    {
        $summary = $this->sessionSummary($session);

        $this->send(
            $parent->id,
            'Cancelled training credit added',
            "EUR ".number_format($amount, 2)." was returned to your balance for {$summary}.",
            'payment'
        );

        $this->send(
            $child->id,
            'Cancelled training refunded',
            "A cancellation credit was issued for {$summary}.",
            'payment'
        );
    }

    public function notifyCancellationCreditReversed(TrainingSession $session, User $parent, User $child, float $amount): void
    {
        $summary = $this->sessionSummary($session);

        $this->send(
            $parent->id,
            'Cancelled training credit reversed',
            "EUR ".number_format($amount, 2)." was removed from your balance because {$summary} was restored.",
            'payment'
        );

        $this->send(
            $child->id,
            'Training restored',
            "The cancellation credit for {$summary} was reversed because the session is active again.",
            'payment'
        );
    }

    public function notifySessionStatusChanged(TrainingSession $session, string $status): void
    {
        $session->loadMissing(['group.coach', 'group.children.parents', 'extraChildren.parents']);

        [$childTitle, $childMessage, $parentTitle, $parentMessage, $type] = match ($status) {
            'cancelled' => [
                'Training cancelled',
                "{$this->sessionSummary($session)} has been cancelled.",
                'Child training cancelled',
                "{$this->sessionSummary($session)} has been cancelled for your child.",
                'session',
            ],
            default => [
                'Training restored',
                "{$this->sessionSummary($session)} is active again.",
                'Child training restored',
                "{$this->sessionSummary($session)} is active again for your child.",
                'session',
            ],
        };

        foreach ($session->effectiveChildren() as $child) {
            $this->send($child->id, $childTitle, $childMessage, $type);

            foreach ($child->parents as $parent) {
                $this->send($parent->id, $parentTitle, $parentMessage, $type);
            }
        }
    }

    public function notifyCoachSessionStatusChanged(TrainingSession $session, string $status): void
    {
        $session->loadMissing(['group.coach']);

        if (! $session->group?->coach_id || $session->group?->coach?->role !== User::ROLE_COACH) {
            return;
        }

        [$title, $message] = match ($status) {
            'cancelled' => [
                'Session cancelled',
                "{$this->sessionSummary($session)} has been cancelled.",
            ],
            default => [
                'Session restored',
                "{$this->sessionSummary($session)} is active again.",
            ],
        };

        $this->send(
            $session->group->coach_id,
            $title,
            $message,
            'session'
        );
    }

    public function notifySessionScheduleChanged(TrainingSession $session): void
    {
        $session->loadMissing(['group.coach', 'group.children.parents', 'extraChildren.parents']);
        $summary = $this->sessionSummary($session);

        foreach ($session->effectiveChildren() as $child) {
            $this->send(
                $child->id,
                'Training schedule updated',
                "{$summary} has updated date or time details.",
                'session'
            );

            foreach ($child->parents as $parent) {
                $this->send(
                    $parent->id,
                    'Child training schedule updated',
                    "{$summary} has updated date or time details for your child.",
                    'session'
                );
            }
        }
    }

    public function notifyCoachSessionScheduleChanged(TrainingSession $session): void
    {
        $session->loadMissing(['group.coach']);

        if (! $session->group?->coach_id || $session->group?->coach?->role !== User::ROLE_COACH) {
            return;
        }

        $this->send(
            $session->group->coach_id,
            'Schedule updated',
            "{$this->sessionSummary($session)} has updated date or time details.",
            'session'
        );
    }

    public function notifyRecurringScheduleChanged(Collection $sessions): void
    {
        $sessions = $sessions
            ->loadMissing(['group.coach', 'group.children.parents', 'extraChildren.parents'])
            ->filter()
            ->values();

        if ($sessions->isEmpty()) {
            return;
        }

        $groupLabel = $sessions->first()->group->display_name;
        $childIds = [];
        $parentIds = [];

        foreach ($sessions as $session) {
            foreach ($session->effectiveChildren() as $child) {
                $childIds[$child->id] = $child;

                foreach ($child->parents as $parent) {
                    $parentIds[$parent->id] = $parent;
                }
            }
        }

        foreach ($childIds as $child) {
            $this->send(
                $child->id,
                'Training schedule updated',
                "The weekly schedule for {$groupLabel} has been updated.",
                'session'
            );
        }

        foreach ($parentIds as $parent) {
            $this->send(
                $parent->id,
                'Child training schedule updated',
                "The weekly schedule for {$groupLabel} has been updated.",
                'session'
            );
        }
    }

    public function notifyCoachRecurringScheduleChanged(Collection $sessions): void
    {
        $sessions = $sessions
            ->loadMissing(['group.coach'])
            ->filter()
            ->values();

        if ($sessions->isEmpty()) {
            return;
        }

        $firstSession = $sessions->first();
        $coachId = $firstSession?->group?->coach_id;

        if (! $coachId || $firstSession?->group?->coach?->role !== User::ROLE_COACH) {
            return;
        }

        $this->send(
            $coachId,
            'Recurring schedule updated',
            "The weekly schedule for {$firstSession->group->display_name} has been updated.",
            'session'
        );
    }

    public function notifyChildAddedToSession(TrainingSession $session, User $child): void
    {
        $session->loadMissing(['group.coach', 'extraChildren.parents', 'group.children.parents']);
        $summary = $this->sessionSummary($session);

        $this->send(
            $child->id,
            'Extra training added',
            "You were added to {$summary}.",
            'session'
        );

        foreach ($child->parents as $parent) {
            $this->send(
                $parent->id,
                'Child added to extra training',
                "Your child was added to {$summary}.",
                'session'
            );
        }
    }

    public function notifyCoachChildAddedToSession(TrainingSession $session, User $child): void
    {
        $session->loadMissing(['group.coach']);

        if (! $session->group?->coach_id || $session->group?->coach?->role !== User::ROLE_COACH) {
            return;
        }

        $this->send(
            $session->group->coach_id,
            'Child added to session',
            "{$this->userDisplayName($child)} was added to {$this->sessionSummary($session)}.",
            'session'
        );
    }

    public function notifyCoachChildRemovedFromSession(TrainingSession $session, User $child): void
    {
        $session->loadMissing(['group.coach']);

        if (! $session->group?->coach_id || $session->group?->coach?->role !== User::ROLE_COACH) {
            return;
        }

        $this->send(
            $session->group->coach_id,
            'Child removed from session',
            "{$this->userDisplayName($child)} was removed from {$this->sessionSummary($session)}.",
            'session'
        );
    }

    public function notifyCoachGroupAssigned(\App\Models\Group $group): void
    {
        $group->loadMissing('coach');

        if (! $group->coach_id || $group->coach?->role !== User::ROLE_COACH) {
            return;
        }

        $this->send(
            $group->coach_id,
            'New group assigned',
            "You were assigned to {$group->display_name}.",
            'group'
        );
    }

    public function notifyCoachChildAddedToGroup(\App\Models\Group $group, User $child): void
    {
        $group->loadMissing('coach');

        if (! $group->coach_id || $group->coach?->role !== User::ROLE_COACH) {
            return;
        }

        $this->send(
            $group->coach_id,
            'Child moved to your group',
            "{$this->userDisplayName($child)} was added to {$group->display_name}.",
            'group'
        );
    }

    public function notifyCoachChildRemovedFromGroup(\App\Models\Group $group, User $child): void
    {
        $group->loadMissing('coach');

        if (! $group->coach_id || $group->coach?->role !== User::ROLE_COACH) {
            return;
        }

        $this->send(
            $group->coach_id,
            'Child removed from your group',
            "{$this->userDisplayName($child)} was removed from {$group->display_name}.",
            'group'
        );
    }

    public function notifyCoachGroupScheduleChanged(\App\Models\Group $group): void
    {
        $group->loadMissing('coach');

        if (! $group->coach_id || $group->coach?->role !== User::ROLE_COACH) {
            return;
        }

        $this->send(
            $group->coach_id,
            'Recurring schedule updated',
            "The weekly schedule for {$group->display_name} has been updated.",
            'group'
        );
    }

    public function notifyAttendanceUpdated(Attendance $attendance, bool $autoMarked = false): void
    {
        $attendance->loadMissing(['session.group.coach', 'user.parents']);
        $summary = $this->sessionSummary($attendance->session);
        $child = $attendance->user;
        $statusLabel = $attendance->status === 'present' ? 'present' : 'absent';

        $childTitle = $autoMarked ? 'Attendance updated automatically' : 'Attendance updated';
        $parentTitle = $autoMarked ? 'Child attendance updated automatically' : 'Child attendance updated';
        $childMessage = "Your attendance for {$summary} is marked as {$statusLabel}.";
        $parentMessage = "{$this->userDisplayName($child)} is marked as {$statusLabel} for {$summary}.";

        $this->send($child->id, $childTitle, $childMessage, 'attendance');

        foreach ($child->parents as $parent) {
            $this->send($parent->id, $parentTitle, $parentMessage, 'attendance');
        }
    }

    public function syncParentPaymentNotifications(?User $parent = null): void
    {
        $parents = $parent
            ? collect([$parent->loadMissing('children')])
            : User::query()->where('role', User::ROLE_PARENT)->with('children')->get();

        foreach ($parents as $currentParent) {
            $childIds = $currentParent->children->pluck('id');

            if ($childIds->isEmpty()) {
                continue;
            }

            $paidSessionKeys = PaymentItem::query()
                ->where('type', 'session')
                ->whereHas('payment', function ($builder) use ($childIds, $currentParent) {
                    $builder
                        ->where('status', 'paid')
                        ->where('parent_id', $currentParent->id)
                        ->whereIn('child_id', $childIds);
                })
                ->get()
                ->map(fn (PaymentItem $item) => $item->session_id.'-'.$item->payment->child_id)
                ->values()
                ->all();

            $paidMonthlyCoverageKeys = PaymentMonthCoverage::query()
                ->whereIn('child_id', $childIds)
                ->whereHas('payment', function ($builder) use ($currentParent) {
                    $builder
                        ->where('status', 'paid')
                        ->where('parent_id', $currentParent->id);
                })
                ->get()
                ->map(fn (PaymentMonthCoverage $coverage) => $coverage->child_id.'-'.$coverage->group_id.'-'.$coverage->month)
                ->unique()
                ->values()
                ->all();

            $paidSessionMonthKeys = PaymentItem::query()
                ->with('session')
                ->where('type', 'session')
                ->whereHas('payment', function ($builder) use ($childIds, $currentParent) {
                    $builder
                        ->where('status', 'paid')
                        ->where('parent_id', $currentParent->id)
                        ->whereIn('child_id', $childIds);
                })
                ->get()
                ->filter(fn (PaymentItem $item) => $item->session)
                ->map(fn (PaymentItem $item) => $item->payment->child_id.'-'.$item->session->group_id.'-'.$item->session->date->format('Y-m'))
                ->unique()
                ->values()
                ->all();

            $sessionDueItems = TrainingSession::query()
                ->with(['group.coach', 'group.children.parents', 'extraChildren.parents'])
                ->whereIn('status', ['planned', 'completed'])
                ->where(function ($builder) use ($childIds) {
                    $builder
                        ->whereHas('group.children', fn ($relation) => $relation->whereIn('users.id', $childIds))
                        ->orWhereHas('extraChildren', fn ($relation) => $relation->whereIn('users.id', $childIds));
                })
                ->get()
                ->flatMap(function (TrainingSession $session) use ($currentParent, $paidSessionKeys, $paidMonthlyCoverageKeys) {
                    $visibleFrom = $this->resolvePendingVisibleFrom($session);

                    if (now()->lt($visibleFrom)) {
                        return collect();
                    }

                    $deadline = $this->resolvePaymentDeadline($session);

                    return $session->effectiveChildren()
                        ->filter(fn (User $child) => $child->parents->contains('id', $currentParent->id))
                        ->reject(function (User $child) use ($session, $paidSessionKeys, $paidMonthlyCoverageKeys) {
                            return in_array($session->id.'-'.$child->id, $paidSessionKeys, true)
                                || in_array($child->id.'-'.$session->group_id.'-'.$session->date->format('Y-m'), $paidMonthlyCoverageKeys, true);
                        })
                        ->map(function (User $child) use ($session, $deadline) {
                            $status = now()->greaterThan($deadline) ? 'overdue' : 'pending';
                            $title = $status === 'overdue' ? 'Payment overdue' : 'Payment due soon';
                            $message = $status === 'overdue'
                                ? "Payment for {$this->sessionSummary($session)} is overdue."
                                : "Payment for {$this->sessionSummary($session)} is now available.";

                            return [
                                'key' => "{$status}-session-{$session->id}-{$child->id}",
                                'title' => $title,
                                'message' => $message,
                            ];
                        });
                });

            foreach ($sessionDueItems as $item) {
                $this->send($currentParent->id, $item['title'], $item['message'], 'payment', $item['key']);
            }

            $monthlyDueItems = TrainingSession::query()
                ->with(['group.coach', 'group.children.parents'])
                ->whereIn('status', ['planned', 'completed'])
                ->whereHas('group.children', fn ($builder) => $builder->whereIn('users.id', $childIds))
                ->get()
                ->flatMap(function (TrainingSession $session) use ($currentParent, $paidMonthlyCoverageKeys, $paidSessionMonthKeys) {
                    return $session->group->children
                        ->filter(fn (User $child) => $child->parents->contains('id', $currentParent->id))
                        ->map(fn (User $child) => [
                            'child' => $child,
                            'session' => $session,
                            'month' => $session->date->format('Y-m'),
                        ]);
                })
                ->groupBy(fn (array $item) => $item['child']->id.'-'.$item['session']->group_id.'-'.$item['month'])
                ->map(function (Collection $items, string $coverageKey) use ($paidMonthlyCoverageKeys, $paidSessionMonthKeys) {
                    if (in_array($coverageKey, $paidMonthlyCoverageKeys, true) || in_array($coverageKey, $paidSessionMonthKeys, true)) {
                        return null;
                    }

                    $first = $items->first();
                    /** @var User $child */
                    $child = $first['child'];
                    /** @var TrainingSession $session */
                    $session = $first['session'];
                    $monthStart = Carbon::createFromFormat('Y-m', $first['month'])->startOfMonth();
                    $visibleFrom = $this->resolveMonthlyPaymentVisibleFrom($monthStart);

                    if (now()->lt($visibleFrom)) {
                        return null;
                    }

                    $deadline = $this->resolveMonthlyPaymentDeadline($monthStart);
                    $status = now()->greaterThan($deadline) ? 'overdue' : 'pending';
                    $title = $status === 'overdue' ? 'Monthly payment overdue' : 'Monthly payment due soon';
                    $message = $status === 'overdue'
                        ? "Monthly payment for {$session->group->display_name} ({$monthStart->format('F Y')}) is overdue."
                        : "Monthly payment for {$session->group->display_name} ({$monthStart->format('F Y')}) is now available.";

                    return [
                        'key' => "{$status}-month-{$coverageKey}",
                        'title' => $title,
                        'message' => $message,
                    ];
                })
                ->filter();

            foreach ($monthlyDueItems as $item) {
                $this->send($currentParent->id, $item['title'], $item['message'], 'payment', $item['key']);
            }
        }
    }

    private function paymentItemSummary(Payment $payment): string
    {
        $firstItem = $payment->items->first();
        $groupName = $firstItem?->monthCoverage?->group?->display_name
            ?? $firstItem?->session?->group?->display_name
            ?? 'training';

        if ($firstItem?->type === 'month') {
            $month = $firstItem->monthCoverage?->month ?? $firstItem->month ?? '';
            $monthLabel = $month ? Carbon::createFromFormat('Y-m', $month)->format('F Y') : 'selected month';

            return "{$groupName} · {$monthLabel}";
        }

        return $firstItem?->session?->title
            ? $firstItem->session->title.' · '.$groupName
            : $groupName;
    }

    private function userDisplayName(?User $user): string
    {
        return trim(($user->name ?? '').' '.($user->surname ?? ''));
    }

    private function sessionSummary(TrainingSession $session): string
    {
        $title = $session->title ?: $session->group->display_name;
        $date = $session->date instanceof Carbon ? $session->date : Carbon::parse($session->date);

        return "{$title} on ".$date->format('M j')." {$session->start_time}-{$session->end_time}";
    }

    private function resolvePaymentDeadline(TrainingSession $session): Carbon
    {
        return $session->date->copy()->subDay()->endOfDay();
    }

    private function resolvePendingVisibleFrom(TrainingSession $session): Carbon
    {
        return $this->resolvePaymentDeadline($session)->copy()->subDays(7)->startOfDay();
    }

    private function resolveMonthlyPaymentDeadline(Carbon $monthStart): Carbon
    {
        return $monthStart->copy()->subDay()->endOfDay();
    }

    private function resolveMonthlyPaymentVisibleFrom(Carbon $monthStart): Carbon
    {
        return $monthStart->copy()->subDays(7)->startOfDay();
    }
}
