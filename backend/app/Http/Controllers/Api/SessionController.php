<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sessions\StoreSessionRequest;
use App\Http\Requests\Sessions\UpdateSessionRequest;
use App\Models\Group;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = TrainingSession::query()->with(['group.coach', 'group.children']);

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->integer('group_id'));
        }

        if ($user->role === User::ROLE_COACH) {
            $query->whereHas('group', fn ($builder) => $builder->where('coach_id', $user->id));
        } elseif ($user->role === User::ROLE_PARENT) {
            $childIds = $user->children()->pluck('users.id');
            $query->whereHas('group.children', fn ($builder) => $builder->whereIn('users.id', $childIds));
        } elseif ($user->role === User::ROLE_CHILD) {
            $query->whereHas('group.children', fn ($builder) => $builder->where('users.id', $user->id));
        }

        return $this->success($query->get()->map(fn (TrainingSession $session) => $this->formatSession($session))->values());
    }

    public function store(StoreSessionRequest $request)
    {
        $group = Group::findOrFail($request->validated('group_id'));

        if (! $this->canManageSessionGroup($request->user(), $group)) {
            return $this->error('Forbidden.', [], 403);
        }

        $session = TrainingSession::create($request->validated());

        return $this->success($this->formatSession($session->load(['group.coach', 'group.children'])), 'Session created.', 201);
    }

    public function show(Request $request, TrainingSession $session)
    {
        if (! $this->canViewSession($request->user(), $session)) {
            return $this->error('Forbidden.', [], 403);
        }

        return $this->success($this->formatSession($session->load(['group.coach', 'group.children'])));
    }

    public function update(UpdateSessionRequest $request, TrainingSession $session)
    {
        if (! $this->canManageSessionGroup($request->user(), $session->group)) {
            return $this->error('Forbidden.', [], 403);
        }

        $session->update($request->validated());

        return $this->success($this->formatSession($session->fresh()->load(['group.coach', 'group.children'])), 'Session updated.');
    }

    public function destroy(Request $request, TrainingSession $session)
    {
        if (! $this->canManageSessionGroup($request->user(), $session->group)) {
            return $this->error('Forbidden.', [], 403);
        }

        $session->delete();

        return $this->success([], 'Session deleted.');
    }

    private function canManageSessionGroup(User $user, Group $group): bool
    {
        return $user->role === User::ROLE_ADMIN
            || ($user->role === User::ROLE_COACH && $group->coach_id === $user->id);
    }

    private function canViewSession(User $user, TrainingSession $session): bool
    {
        if ($user->role === User::ROLE_ADMIN) return true;
        if ($user->role === User::ROLE_COACH) return $session->group->coach_id === $user->id;
        if ($user->role === User::ROLE_CHILD) return $session->group->children()->where('users.id', $user->id)->exists();
        if ($user->role === User::ROLE_PARENT) {
            return $session->group->children()->whereIn('users.id', $user->children()->pluck('users.id'))->exists();
        }

        return false;
    }

    private function formatSession(TrainingSession $session): array
    {
        return [
            'id' => $session->id,
            'group_id' => $session->group_id,
            'title' => $session->group->name,
            'description' => $session->group->age_category ?? 'Training session',
            'trainer' => trim(($session->group->coach->name ?? '').' '.($session->group->coach->surname ?? '')),
            'start' => substr($session->start_time, 0, 5),
            'end' => substr($session->end_time, 0, 5),
            'date' => $session->date->toDateString(),
            'status' => $session->status,
            'group' => $session->group->name,
            'students' => $session->group->children->map(fn (User $child) => [
                'id' => $child->id,
                'name' => trim($child->name.' '.$child->surname),
            ])->values(),
        ];
    }
}
