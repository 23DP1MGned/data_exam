<?php

namespace App\Services;

use App\Models\SessionTemplate;
use App\Models\TrainingSession;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SessionTemplateService
{
    public function ensureUpcomingSessionsGenerated(?Carbon $through = null): void
    {
        $this->syncAutomaticSessionStatuses();

        $rangeEnd = ($through ?: now()->copy()->addMonth())->endOfDay();

        SessionTemplate::query()
            ->where('is_active', true)
            ->get()
            ->each(fn (SessionTemplate $template) => $this->syncTemplateSessions($template, now()->startOfDay(), $rangeEnd));
    }

    public function syncAutomaticSessionStatuses(): void
    {
        $now = now();

        TrainingSession::query()
            ->where('status', 'planned')
            ->get()
            ->filter(fn (TrainingSession $session) => $this->sessionHasFinished($session, $now))
            ->each(function (TrainingSession $session) {
                $session->update([
                    'status' => 'completed',
                ]);
            });
    }

    public function createRecurring(array $validated): EloquentCollection
    {
        return DB::transaction(function () use ($validated) {
            $template = SessionTemplate::create([
                'group_id' => $validated['group_id'],
                'title' => $validated['title'],
                'weekdays' => $this->normalizeWeekdays($validated['weekdays'] ?? []),
                'excluded_dates' => [],
                'starts_on' => now()->toDateString(),
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'price' => $validated['price'] ?? 0,
                'is_active' => true,
            ]);

            return $this->syncTemplateSessions($template, now()->startOfDay(), now()->copy()->addMonth()->endOfDay());
        });
    }

    public function updateRecurring(TrainingSession $session, array $validated): EloquentCollection
    {
        return DB::transaction(function () use ($session, $validated) {
            $template = $session->sessionTemplate;

            if (! $template) {
                $template = SessionTemplate::create([
                    'group_id' => $validated['group_id'],
                    'title' => $validated['title'],
                    'weekdays' => $this->normalizeWeekdays($validated['weekdays'] ?? []),
                    'excluded_dates' => [],
                    'starts_on' => now()->toDateString(),
                    'start_time' => $validated['start_time'],
                    'end_time' => $validated['end_time'],
                    'price' => $validated['price'] ?? 0,
                    'is_active' => true,
                ]);

                if ($this->canRegenerateSession($session)) {
                    $session->delete();
                }
            } else {
                $template->update([
                    'group_id' => $validated['group_id'],
                    'title' => $validated['title'],
                    'weekdays' => $this->normalizeWeekdays($validated['weekdays'] ?? $template->weekdays ?? []),
                    'start_time' => $validated['start_time'],
                    'end_time' => $validated['end_time'],
                    'price' => $validated['price'] ?? 0,
                    'is_active' => true,
                ]);
            }

            $sessions = $this->syncTemplateSessions($template->fresh(), now()->startOfDay(), now()->copy()->addMonth()->endOfDay());

            if (array_key_exists('status', $validated) && $validated['status']) {
                $targetDate = $session->date?->toDateString();

                if ($targetDate) {
                    $generated = $sessions->first(fn (TrainingSession $generatedSession) => $generatedSession->date->toDateString() === $targetDate);
                    if ($generated) {
                        $generated->update(['status' => $validated['status']]);
                    } elseif ($session->exists) {
                        $session->update(['status' => $validated['status']]);
                    }
                }
            }

            return $template->sessions()
                ->with('group.coach', 'group.children', 'sessionTemplate')
                ->whereDate('date', '>=', now()->startOfDay()->toDateString())
                ->whereDate('date', '<=', now()->copy()->addMonth()->endOfDay()->toDateString())
                ->orderBy('date')
                ->orderBy('start_time')
                ->get();
        });
    }

    public function deleteSession(TrainingSession $session): void
    {
        DB::transaction(function () use ($session) {
            if ($session->sessionTemplate && $this->canRegenerateSession($session)) {
                $template = $session->sessionTemplate;
                $excludedDates = collect($template->excluded_dates ?? [])
                    ->push($session->date->toDateString())
                    ->unique()
                    ->values()
                    ->all();

                $template->update(['excluded_dates' => $excludedDates]);
            }

            $session->delete();
        });
    }

    private function syncTemplateSessions(SessionTemplate $template, Carbon $from, Carbon $through): EloquentCollection
    {
        $desiredDates = $this->resolveDates($template, $from, $through);
        $rangeStart = $from->copy()->startOfDay()->toDateString();
        $rangeEnd = $through->copy()->endOfDay()->toDateString();

        $existingSessions = $template->sessions()
            ->with(['attendanceRecords', 'paymentItems'])
            ->whereDate('date', '>=', $rangeStart)
            ->whereDate('date', '<=', $rangeEnd)
            ->orderBy('date')
            ->orderBy('id')
            ->get();

        $this->removeDuplicateSessions($existingSessions);

        $existingSessions = $template->sessions()
            ->with(['attendanceRecords', 'paymentItems'])
            ->whereDate('date', '>=', $rangeStart)
            ->whereDate('date', '<=', $rangeEnd)
            ->orderBy('date')
            ->orderBy('id')
            ->get()
            ->keyBy(fn (TrainingSession $session) => $session->date->toDateString());

        foreach ($desiredDates as $date) {
            $existing = $existingSessions->get($date);

            if ($existing) {
                if ($this->canRegenerateSession($existing)) {
                    $existing->update([
                        'group_id' => $template->group_id,
                        'title' => $template->title,
                        'start_time' => $template->start_time,
                        'end_time' => $template->end_time,
                        'price' => $template->price ?? 0,
                    ]);
                }

                continue;
            }

            TrainingSession::create([
                'group_id' => $template->group_id,
                'session_template_id' => $template->id,
                'title' => $template->title,
                'date' => $date,
                'start_time' => $template->start_time,
                'end_time' => $template->end_time,
                'price' => $template->price ?? 0,
                'status' => 'planned',
            ]);
        }

        $desiredLookup = collect($desiredDates)->flip();
        $existingSessions
            ->filter(function (TrainingSession $session, string $date) use ($desiredLookup) {
                return ! $desiredLookup->has($date) && $this->canRegenerateSession($session);
            })
            ->each
            ->delete();

        return $template->sessions()
            ->with('group.coach', 'group.children', 'sessionTemplate')
            ->whereDate('date', '>=', $rangeStart)
            ->whereDate('date', '<=', $rangeEnd)
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();
    }

    private function removeDuplicateSessions(EloquentCollection $sessions): void
    {
        $sessions
            ->groupBy(fn (TrainingSession $session) => $session->date->toDateString())
            ->filter(fn (EloquentCollection $sessionsOnDate) => $sessionsOnDate->count() > 1)
            ->each(function (EloquentCollection $sessionsOnDate) {
                $sessionToKeep = $sessionsOnDate->first(fn (TrainingSession $session) => ! $this->canRegenerateSession($session))
                    ?? $sessionsOnDate->sortBy('id')->first();

                $sessionsOnDate
                    ->reject(fn (TrainingSession $session) => $session->id === $sessionToKeep?->id)
                    ->each(function (TrainingSession $session) {
                        if ($this->canRegenerateSession($session)) {
                            $session->delete();
                        }
                    });
            });
    }

    private function resolveDates(SessionTemplate $template, Carbon $from, Carbon $through): array
    {
        $dates = [];
        $cursor = $from->copy()->startOfDay();
        $startsOn = $template->starts_on?->copy()->startOfDay();
        $excludedDates = collect($template->excluded_dates ?? []);

        while ($cursor->lte($through)) {
            if (
                (! $startsOn || $cursor->gte($startsOn))
                && in_array($this->formatWeekday($cursor), $template->weekdays ?? [], true)
                && ! $excludedDates->contains($cursor->toDateString())
            ) {
                $dates[] = $cursor->toDateString();
            }

            $cursor->addDay();
        }

        return $dates;
    }

    private function formatWeekday(Carbon $date): string
    {
        return match ($date->dayOfWeek) {
            Carbon::MONDAY => 'Mon',
            Carbon::TUESDAY => 'Tue',
            Carbon::WEDNESDAY => 'Wed',
            Carbon::THURSDAY => 'Thu',
            Carbon::FRIDAY => 'Fri',
            Carbon::SATURDAY => 'Sat',
            default => 'Sun',
        };
    }

    private function normalizeWeekdays(array $weekdays): array
    {
        $order = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

        return collect($weekdays)
            ->filter(fn ($weekday) => in_array($weekday, $order, true))
            ->unique()
            ->sortBy(fn ($weekday) => array_search($weekday, $order, true))
            ->values()
            ->all();
    }

    private function canRegenerateSession(TrainingSession $session): bool
    {
        $date = $session->date instanceof Carbon ? $session->date : Carbon::parse($session->date);

        return $session->status === 'planned'
            && $date->gte(now()->startOfDay())
            && ! $session->attendanceRecords()->exists()
            && ! $session->paymentItems()->exists();
    }

    private function sessionHasFinished(TrainingSession $session, Carbon $now): bool
    {
        $sessionDate = $session->date instanceof Carbon
            ? $session->date->copy()
            : Carbon::parse($session->date);

        $sessionEnd = $sessionDate->copy()->startOfDay();

        if ($session->end_time) {
            [$hours, $minutes, $seconds] = array_pad(explode(':', $session->end_time), 3, '0');
            $sessionEnd->setTime((int) $hours, (int) $minutes, (int) $seconds);
        } elseif ($session->start_time) {
            [$hours, $minutes, $seconds] = array_pad(explode(':', $session->start_time), 3, '0');
            $sessionEnd->setTime((int) $hours, (int) $minutes, (int) $seconds);
        } else {
            $sessionEnd->endOfDay();
        }

        return $now->gte($sessionEnd);
    }
}
