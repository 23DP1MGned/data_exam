<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Groups\StoreGroupRequest;
use App\Http\Requests\Groups\UpdateGroupRequest;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Group::query()->with(['coach', 'children', 'sessions']);

        if ($user->role === User::ROLE_COACH) {
            $query->where('coach_id', $user->id);
        } elseif ($user->role === User::ROLE_PARENT) {
            $childIds = $user->children()->pluck('users.id');
            $query->whereHas('children', fn ($builder) => $builder->whereIn('users.id', $childIds));
        } elseif ($user->role === User::ROLE_CHILD) {
            $query->whereHas('children', fn ($builder) => $builder->where('users.id', $user->id));
        }

        return $this->success($query->get()->map(fn (Group $group) => $this->formatGroup($group))->values());
    }

    public function store(StoreGroupRequest $request)
    {
        $user = $request->user();

        if (! $user->hasRole([User::ROLE_ADMIN, User::ROLE_COACH])) {
            return $this->error('Forbidden.', [], 403);
        }

        $group = Group::create([
            'name' => $request->validated('name'),
            'age_category' => $request->validated('age_category'),
            'schedule_days' => $request->validated('schedule_days'),
            'default_time' => $request->validated('default_time'),
            'price' => $request->validated('price', 0),
            'coach_id' => $user->role === User::ROLE_COACH
                ? $user->id
                : ($request->validated('coach_id') ?? $user->id),
        ]);

        $group->children()->sync($request->validated('child_ids', []));

        return $this->success($this->formatGroup($group->load(['coach', 'children', 'sessions'])), 'Group created.', 201);
    }

    public function show(Request $request, Group $group)
    {
        if (! $this->canViewGroup($request->user(), $group)) {
            return $this->error('Forbidden.', [], 403);
        }

        return $this->success($this->formatGroup($group->load(['coach', 'children', 'sessions'])));
    }

    public function update(UpdateGroupRequest $request, Group $group)
    {
        if (! $this->canManageGroup($request->user(), $group)) {
            return $this->error('Forbidden.', [], 403);
        }

        $payload = $request->validated();
        if ($request->user()->role === User::ROLE_COACH) {
            unset($payload['coach_id']);
        }

        $group->update([
            'name' => $payload['name'],
            'age_category' => $payload['age_category'] ?? $group->age_category,
            'schedule_days' => $payload['schedule_days'] ?? $group->schedule_days,
            'default_time' => $payload['default_time'] ?? $group->default_time,
            'price' => $payload['price'] ?? $group->price,
            'coach_id' => $payload['coach_id'] ?? $group->coach_id,
        ]);

        if (array_key_exists('child_ids', $payload)) {
            $group->children()->sync($payload['child_ids'] ?? []);
        }

        return $this->success($this->formatGroup($group->load(['coach', 'children', 'sessions'])), 'Group updated.');
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
        if ($user->role === User::ROLE_CHILD) return $group->children()->where('users.id', $user->id)->exists();
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

    private function formatGroup(Group $group): array
    {
        $firstSession = $group->sessions->sortBy('date')->first();
        $attended = $group->sessions->flatMap->attendanceRecords;
        $attendanceRate = $attended->count()
            ? (int) round(($attended->where('status', 'present')->count() / $attended->count()) * 100)
            : 0;

        return [
            'id' => $group->id,
            'section' => $group->name,
            'trainer' => trim(($group->coach->name ?? '').' '.($group->coach->surname ?? '')),
            'age_category' => $group->age_category,
            'days' => $group->schedule_days ?: $group->sessions->sortBy('date')->take(2)->map(fn ($session) => $session->date->format('D'))->implode(' / '),
            'time' => $group->default_time ?: ($firstSession ? substr($firstSession->start_time, 0, 5) : null),
            'students' => $group->children->count(),
            'attendance' => $attendanceRate,
            'price' => (float) $group->price,
            'child_ids' => $group->children->pluck('id')->values(),
        ];
    }
}
