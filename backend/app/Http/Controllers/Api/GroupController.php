<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Groups\StoreGroupRequest;
use App\Http\Requests\Groups\UpdateGroupRequest;
use App\Models\Group;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function __construct(
        private readonly NotificationService $notificationService
    ) {
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $query = Group::query()->with(['coach', 'children', 'sessions.attendanceRecords']);

        if ($user->role === User::ROLE_COACH) {
            $query->where('coach_id', $user->id);
        } elseif ($user->role === User::ROLE_PARENT) {
            $childIds = $user->children()->pluck('users.id');
            $query->whereHas('children', fn ($builder) => $builder->whereIn('users.id', $childIds));
        } elseif (in_array($user->role, [User::ROLE_CHILD, User::ROLE_ADULT], true)) {
            $query->whereHas('children', fn ($builder) => $builder->where('users.id', $user->id));
        }

        return $this->success($query->get()->map(fn (Group $group) => $this->formatGroup($group, $user))->values());
    }

    public function store(StoreGroupRequest $request)
    {
        $user = $request->user();

        if (! $user->hasRole([User::ROLE_ADMIN, User::ROLE_COACH])) {
            return $this->error('Forbidden.', [], 403);
        }

        $group = Group::create([
            'name' => $request->validated('name'),
            'group_number' => $request->validated('group_number') ?? $this->nextGroupNumber(),
            'age_category' => $request->validated('age_category'),
            'schedule_days' => $request->validated('schedule_days'),
            'default_time' => $request->validated('default_time'),
            'price' => $request->validated('price', 0),
            'coach_id' => $user->role === User::ROLE_COACH
                ? $user->id
                : ($request->validated('coach_id') ?? $user->id),
        ]);

        $group->children()->sync($request->validated('child_ids', []));

        if ($request->user()->role === User::ROLE_ADMIN) {
            $this->notificationService->notifyCoachGroupAssigned($group->fresh()->load('coach'));
        }

        $group->load('children');
        foreach ($group->children as $child) {
            $this->notificationService->notifyCoachChildAddedToGroup($group, $child);
        }

        return $this->success($this->formatGroup($group->load(['coach', 'children', 'sessions.attendanceRecords']), $request->user()), 'Group created.', 201);
    }

    public function show(Request $request, Group $group)
    {
        if (! $this->canViewGroup($request->user(), $group)) {
            return $this->error('Forbidden.', [], 403);
        }

        return $this->success($this->formatGroup($group->load(['coach', 'children', 'sessions.attendanceRecords']), $request->user()));
    }

    public function update(UpdateGroupRequest $request, Group $group)
    {
        if (! $this->canManageGroup($request->user(), $group)) {
            return $this->error('Forbidden.', [], 403);
        }

        $payload = $request->validated();
        $previousCoachId = $group->coach_id;
        $previousChildIds = $group->children()->pluck('users.id')->map(fn ($id) => (int) $id)->all();
        $before = $group->only(['name', 'schedule_days', 'default_time']);

        if ($request->user()->role === User::ROLE_COACH) {
            unset($payload['coach_id']);
        }

        $group->update([
            'name' => $payload['name'],
            'group_number' => $payload['group_number'] ?? $group->group_number,
            'age_category' => $payload['age_category'] ?? $group->age_category,
            'schedule_days' => $payload['schedule_days'] ?? $group->schedule_days,
            'default_time' => $payload['default_time'] ?? $group->default_time,
            'price' => $payload['price'] ?? $group->price,
            'coach_id' => $payload['coach_id'] ?? $group->coach_id,
        ]);

        if (array_key_exists('child_ids', $payload)) {
            $group->children()->sync($payload['child_ids'] ?? []);
        }

        $group->load(['coach', 'children']);

        if ($request->user()->role === User::ROLE_ADMIN && $group->coach_id && $group->coach_id !== $previousCoachId) {
            $this->notificationService->notifyCoachGroupAssigned($group);
        }

        if ($this->groupScheduleChanged($before, $group)) {
            $this->notificationService->notifyCoachGroupScheduleChanged($group);
        }

        if (array_key_exists('child_ids', $payload)) {
            $addedChildIds = collect($group->children->pluck('id')->all())
                ->diff($previousChildIds)
                ->values();
            $removedChildIds = collect($previousChildIds)
                ->diff($group->children->pluck('id')->all())
                ->values();

            foreach ($group->children->whereIn('id', $addedChildIds) as $child) {
                $this->notificationService->notifyCoachChildAddedToGroup($group, $child);
            }

            if ($removedChildIds->isNotEmpty()) {
                User::query()
                    ->whereIn('id', $removedChildIds)
                    ->get()
                    ->each(fn (User $child) => $this->notificationService->notifyCoachChildRemovedFromGroup($group, $child));
            }
        }

        return $this->success($this->formatGroup($group->load(['coach', 'children', 'sessions.attendanceRecords']), $request->user()), 'Group updated.');
    }

    public function destroy(Request $request, Group $group)
    {
        if (! $this->canManageGroup($request->user(), $group)) {
            return $this->error('Forbidden.', [], 403);
        }

        $group->delete();

        return $this->success([], 'Group deleted.');
    }

    private function canViewGroup(User $user, Group $group): bool
    {
        if ($user->role === User::ROLE_ADMIN) return true;
        if ($user->role === User::ROLE_COACH) return $group->coach_id === $user->id;
        if (in_array($user->role, [User::ROLE_CHILD, User::ROLE_ADULT], true)) return $group->children()->where('users.id', $user->id)->exists();
        if ($user->role === User::ROLE_PARENT) {
            return $group->children()->whereIn('users.id', $user->children()->pluck('users.id'))->exists();
        }

        return false;
    }

    private function canManageGroup(User $user, Group $group): bool
    {
        return $user->role === User::ROLE_ADMIN
            || ($user->role === User::ROLE_COACH && $group->coach_id === $user->id);
    }

    private function nextGroupNumber(): int
    {
        return ((int) Group::query()->max('group_number')) + 1;
    }

    private function groupScheduleChanged(array $before, Group $group): bool
    {
        return ($before['name'] ?? null) !== $group->name
            || ($before['schedule_days'] ?? null) !== $group->schedule_days
            || ($before['default_time'] ?? null) !== $group->default_time;
    }

    private function formatGroup(Group $group, User $viewer): array
    {
        $firstSession = $group->sessions->sortBy('date')->first();
        $groupAttendanceRecords = $group->sessions->flatMap->attendanceRecords;
        $groupAttendanceRate = $groupAttendanceRecords->count()
            ? (int) round(($groupAttendanceRecords->where('status', 'present')->count() / $groupAttendanceRecords->count()) * 100)
            : 0;
        $childAttendanceRates = $group->children
            ->mapWithKeys(function (User $child) use ($groupAttendanceRecords) {
                $childRecords = $groupAttendanceRecords->where('user_id', $child->id);
                $rate = $childRecords->count()
                    ? (int) round(($childRecords->where('status', 'present')->count() / $childRecords->count()) * 100)
                    : 0;

                return [$child->id => $rate];
            });
        $attendanceRate = in_array($viewer->role, [User::ROLE_CHILD, User::ROLE_ADULT], true)
            ? $childAttendanceRates->get($viewer->id, 0)
            : $groupAttendanceRate;

        return [
            'id' => $group->id,
            'section' => $group->display_name,
            'base_name' => $group->name,
            'group_number' => (int) ($group->group_number ?? $group->id),
            'group_code' => $group->group_code,
            'trainer' => trim(($group->coach->name ?? '').' '.($group->coach->surname ?? '')),
            'age_category' => $group->age_category,
            'days' => $group->schedule_days ?: $group->sessions->sortBy('date')->take(2)->map(fn ($session) => $session->date->format('D'))->implode(' / '),
            'time' => $group->default_time ?: ($firstSession ? substr($firstSession->start_time, 0, 5) : null),
            'students' => $group->children->count(),
            'attendance' => $attendanceRate,
            'child_attendance_rates' => $childAttendanceRates,
            'price' => (float) $group->price,
            'child_ids' => $group->children->pluck('id')->values(),
        ];
    }
}
