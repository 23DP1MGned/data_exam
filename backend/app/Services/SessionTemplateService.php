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
        $rangeEnd = ($through ?: now()->copy()->addMonth())->endOfDay();

        SessionTemplate::query()
            ->where('is_active', true)
            ->get()
            ->each(fn (SessionTemplate $template) => $this->syncTemplateSessions($template, now()->startOfDay(), $rangeEnd));
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
                ->whereBetween('date', [now()->startOfDay()->toDateString(), now()->copy()->addMonth()->endOfDay()->toDateString()])
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

        $existingSessions = $template->sessions()
            ->with(['attendanceRecords', 'paymentItems'])
            ->whereBetween('date', [$from->toDateString(), $through->toDateString()])
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
            ->whereBetween('date', [$from->toDateString(), $through->toDateString()])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();
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
}
