<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sessions\StoreSessionRequest;
use App\Http\Requests\Sessions\UpdateSessionRequest;
use App\Models\Group;
use App\Models\TrainingSession;
use App\Models\User;
use App\Services\AttendanceService;
use App\Services\PaymentService;
use App\Services\SessionTemplateService;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function __construct(
        private readonly SessionTemplateService $sessionTemplateService,
        private readonly AttendanceService $attendanceService,
        private readonly PaymentService $paymentService,
    )
    {
    }

    public function index(Request $request)
    {
        $this->sessionTemplateService->ensureUpcomingSessionsGenerated();

        $user = $request->user();
        $query = TrainingSession::query()->with(['group.coach', 'group.children', 'extraChildren', 'sessionTemplate']);

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->integer('group_id'));
        }

        if ($user->role === User::ROLE_COACH) {
            $query->whereHas('group', fn ($builder) => $builder->where('coach_id', $user->id));
        } elseif ($user->role === User::ROLE_PARENT) {
            $childIds = $user->children()->pluck('users.id');
            $query->where(function ($builder) use ($childIds) {
                $builder
                    ->whereHas('group.children', fn ($relation) => $relation->whereIn('users.id', $childIds))
                    ->orWhereHas('extraChildren', fn ($relation) => $relation->whereIn('users.id', $childIds));
            });
        } elseif ($user->role === User::ROLE_CHILD) {
            $query->where(function ($builder) use ($user) {
                $builder
                    ->whereHas('group.children', fn ($relation) => $relation->where('users.id', $user->id))
                    ->orWhereHas('extraChildren', fn ($relation) => $relation->where('users.id', $user->id));
            });
        }

        return $this->success($query->get()->map(fn (TrainingSession $session) => $this->formatSession($session))->values());
    }

    public function store(StoreSessionRequest $request)
    {
        $validated = $request->validated();
        $group = Group::findOrFail($validated['group_id']);

        if (! $this->canManageSessionGroup($request->user(), $group)) {
            return $this->error('Forbidden.', [], 403);
        }

        if (! empty($validated['weekdays'])) {
            $sessions = $this->sessionTemplateService->createRecurring($validated);

            return $this->success([
                'created_count' => $sessions->count(),
                'first_session' => $sessions->isNotEmpty() ? $this->formatSession($sessions->first()) : null,
            ], 'Recurring sessions created.', 201);
        }

        $session = TrainingSession::create($validated);

        return $this->success($this->formatSession($session->load(['group.coach', 'group.children', 'extraChildren', 'sessionTemplate'])), 'Session created.', 201);
    }

    public function show(Request $request, TrainingSession $session)
    {
        if (! $this->canViewSession($request->user(), $session)) {
            return $this->error('Forbidden.', [], 403);
        }

        return $this->success($this->formatSession($session->load(['group.coach', 'group.children', 'extraChildren', 'sessionTemplate'])));
    }

    public function update(UpdateSessionRequest $request, TrainingSession $session)
    {
        $validated = $request->validated();

        if (! $this->canManageSessionGroup($request->user(), $session->group)) {
            return $this->error('Forbidden.', [], 403);
        }

        $targetGroup = Group::findOrFail($validated['group_id']);

        if (! $this->canManageSessionGroup($request->user(), $targetGroup)) {
            return $this->error('Forbidden.', [], 403);
        }

        if (! empty($validated['weekdays']) || $session->session_template_id) {
            $sessions = $this->sessionTemplateService->updateRecurring($session->load('sessionTemplate'), $validated);

            return $this->success([
                'updated_count' => $sessions->count(),
                'first_session' => $sessions->isNotEmpty() ? $this->formatSession($sessions->first()) : null,
            ], 'Recurring sessions updated.');
        }

        $session->update($validated);

        return $this->success($this->formatSession($session->fresh()->load(['group.coach', 'group.children', 'extraChildren', 'sessionTemplate'])), 'Session updated.');
    }

    public function destroy(Request $request, TrainingSession $session)
    {
        if (! $this->canManageSessionGroup($request->user(), $session->group)) {
            return $this->error('Forbidden.', [], 403);
        }

        $this->sessionTemplateService->deleteSession($session->load('sessionTemplate'));

        return $this->success([], 'Session deleted.');
    }

    public function updateStatus(Request $request, TrainingSession $session)
    {
        if (! $this->canManageSessionGroup($request->user(), $session->group)) {
            return $this->error('Forbidden.', [], 403);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:planned,completed,cancelled'],
        ]);

        $session->update([
            'status' => $validated['status'],
        ]);

        if ($validated['status'] === 'cancelled') {
            $this->paymentService->creditCancelledSession($session->fresh()->load(['group.children.parents', 'extraChildren.parents', 'paymentItems.payment.parent']));
        }

        if ($validated['status'] === 'completed') {
            $this->attendanceService->ensureCompletedSessionAttendance(
                $session->fresh(['group.children', 'extraChildren', 'attendanceRecords'])
            );
        }

        return $this->success(
            $this->formatSession($session->fresh()->load(['group.coach', 'group.children', 'extraChildren', 'sessionTemplate'])),
            'Session status updated.'
        );
    }

    public function attachChild(Request $request, TrainingSession $session)
    {
        if (! $this->canManageSessionGroup($request->user(), $session->group)) {
            return $this->error('Forbidden.', [], 403);
        }

        $validated = $request->validate([
            'child_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $child = User::query()->findOrFail($validated['child_id']);

        if ($child->role !== User::ROLE_CHILD) {
            return $this->error('Only child accounts can be assigned to a session.', [], 422);
        }

        if ($session->group->children()->where('users.id', $child->id)->exists()) {
            return $this->error('This child already belongs to the session group.', [], 422);
        }

        if ($session->extraChildren()->where('users.id', $child->id)->exists()) {
            return $this->error('This child is already assigned to the selected session.', [], 422);
        }

        $session->extraChildren()->attach($child->id);

        return $this->success(
            $this->formatSession($session->fresh()->load(['group.coach', 'group.children', 'extraChildren', 'sessionTemplate'])),
            'Child added to session.'
        );
    }

    public function detachChild(Request $request, TrainingSession $session, User $child)
    {
        if (! $this->canManageSessionGroup($request->user(), $session->group)) {
            return $this->error('Forbidden.', [], 403);
        }

        if (! $session->extraChildren()->where('users.id', $child->id)->exists()) {
            return $this->error('This child is not assigned separately to the selected session.', [], 422);
        }

        $hasAttendance = $session->attendanceRecords()->where('user_id', $child->id)->exists();
        $hasPayments = $session->paymentItems()
            ->whereHas('payment', fn ($builder) => $builder->where('child_id', $child->id))
            ->exists();

        if ($hasAttendance || $hasPayments) {
            return $this->error('This child can no longer be removed because the session already has attendance or payment history.', [], 422);
        }

        $session->extraChildren()->detach($child->id);

        return $this->success(
            $this->formatSession($session->fresh()->load(['group.coach', 'group.children', 'extraChildren', 'sessionTemplate'])),
            'Child removed from session.'
        );
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
        if ($user->role === User::ROLE_CHILD) {
            return $session->group->children()->where('users.id', $user->id)->exists()
                || $session->extraChildren()->where('users.id', $user->id)->exists();
        }
        if ($user->role === User::ROLE_PARENT) {
            $childIds = $user->children()->pluck('users.id');

            return $session->group->children()->whereIn('users.id', $childIds)->exists()
                || $session->extraChildren()->whereIn('users.id', $childIds)->exists();
        }

        return false;
    }

    private function formatSession(TrainingSession $session): array
    {
        $students = $session->effectiveChildren()->map(function (User $child) use ($session) {
            $isSessionSpecific = ! $session->group->children->contains('id', $child->id)
                && $session->extraChildren->contains('id', $child->id);

            return [
                'id' => $child->id,
                'name' => trim($child->name.' '.$child->surname),
                'is_session_specific' => $isSessionSpecific,
            ];
        })->values();

        return [
            'id' => $session->id,
            'group_id' => $session->group_id,
            'template_id' => $session->session_template_id,
            'title' => $session->title ?: $session->group->name,
            'weekdays' => $session->sessionTemplate?->weekdays ?? [$session->date->format('D')],
            'group_code' => $session->group->group_code,
            'description' => $session->group->age_category ?? 'Training session',
            'trainer' => trim(($session->group->coach->name ?? '').' '.($session->group->coach->surname ?? '')),
            'start' => substr($session->start_time, 0, 5),
            'end' => substr($session->end_time, 0, 5),
            'price' => (float) ($session->price ?? $session->group->price ?? 0),
            'date' => $session->date->toDateString(),
            'status' => $session->status,
            'group' => $session->group->display_name,
            'students' => $students,
        ];
    }
}
