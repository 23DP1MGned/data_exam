<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\TrainingSession;
use App\Models\User;
use App\Services\SessionTemplateService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private readonly SessionTemplateService $sessionTemplateService)
    {
    }

    public function index(Request $request)
    {
        $this->sessionTemplateService->ensureUpcomingSessionsGenerated();

        $user = $request->user();
        $notifications = $user->notifications()->latest()->take(6)->get();

        if ($user->role === User::ROLE_ADMIN) {
            $latestGroups = Group::query()
                ->with(['coach', 'children'])
                ->latest()
                ->take(5)
                ->get();

            $recentSessions = TrainingSession::query()
                ->with(['group.coach'])
                ->orderBy('date')
                ->orderBy('start_time')
                ->take(5)
                ->get();

            $recentPayments = Payment::query()
                ->with(['parent', 'child'])
                ->latest()
                ->take(5)
                ->get();

            return $this->success([
                'mode' => 'admin',
                'overview_stats' => [
                    ['label' => 'Total users', 'value' => User::query()->count()],
                    ['label' => 'Coaches', 'value' => User::query()->where('role', User::ROLE_COACH)->count()],
                    ['label' => 'Groups', 'value' => Group::query()->count()],
                    ['label' => 'Pending payments', 'value' => Payment::query()->where('status', 'pending')->count()],
                ],
                'latest_groups' => $latestGroups->map(fn (Group $group) => [
                    'id' => $group->id,
                    'name' => $group->display_name,
                    'group_number' => (int) ($group->group_number ?? $group->id),
                    'group_code' => $group->group_code,
                    'coach' => trim(($group->coach->name ?? '').' '.($group->coach->surname ?? '')),
                    'age_category' => $group->age_category,
                    'students' => $group->children->count(),
                ])->values(),
                'recent_sessions' => $recentSessions->map(fn (TrainingSession $session) => [
                    'id' => $session->id,
                    'title' => $session->title ?: $session->group->name,
                    'group_code' => $session->group->group_code,
                    'group' => $session->group->display_name,
                    'trainer' => trim(($session->group->coach->name ?? '').' '.($session->group->coach->surname ?? '')),
                    'date' => $session->date->toDateString(),
                    'start' => substr($session->start_time, 0, 5),
                    'end' => substr($session->end_time, 0, 5),
                    'status' => $session->status,
                ])->values(),
                'recent_payments' => $recentPayments->map(fn (Payment $payment) => [
                    'id' => $payment->id,
                    'parent' => trim(($payment->parent->name ?? '').' '.($payment->parent->surname ?? '')),
                    'child' => trim(($payment->child->name ?? '').' '.($payment->child->surname ?? '')),
                    'amount' => (float) $payment->amount,
                    'status' => $payment->status,
                    'method' => $payment->method,
                    'created_at' => $payment->created_at?->diffForHumans(),
                ])->values(),
                'user' => [
                    'name' => trim($user->name.' '.$user->surname),
                    'email' => $user->email,
                ],
                'notifications' => $notifications->map(fn (Notification $notification) => [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'text' => $notification->message,
                    'time' => $notification->created_at->diffForHumans(),
                    'unread' => ! $notification->is_read,
                ])->values(),
            ]);
        }

        $sessions = TrainingSession::query()->with(['group.coach', 'group.children', 'extraChildren'])->get();
        $attendanceQuery = \App\Models\Attendance::query();
        $linkedChildren = collect();
        $childIds = collect();

        if ($user->role === User::ROLE_COACH) {
            $sessions = $sessions->filter(fn ($session) => $session->group->coach_id === $user->id)->values();
            $attendanceQuery->whereHas('session.group', fn ($builder) => $builder->where('coach_id', $user->id));
        } elseif ($user->role === User::ROLE_PARENT) {
            $linkedChildren = $user->children()
                ->orderBy('users.name')
                ->orderBy('users.surname')
                ->get(['users.id', 'users.name', 'users.surname', 'users.email']);
            $childIds = $linkedChildren->pluck('id');
            $sessions = $sessions->filter(fn ($session) => $session->effectiveChildren()->whereIn('id', $childIds)->isNotEmpty())->values();
            $attendanceQuery->whereIn('user_id', $childIds);
        } elseif ($user->role === User::ROLE_CHILD) {
            $childIds = collect([$user->id]);
            $sessions = $sessions->filter(fn ($session) => $session->effectiveChildren()->contains('id', $user->id))->values();
            $attendanceQuery->where('user_id', $user->id);
        }

        $attendanceRecords = $attendanceQuery->get();
        $attendanceRate = $attendanceRecords->count() > 0
            ? round(($attendanceRecords->where('status', 'present')->count() / $attendanceRecords->count()) * 100).'%'
            : '0%';

        $upcoming = $sessions->sortBy(fn ($session) => $session->date->format('Y-m-d').' '.$session->start_time)->take(5);
        $roleSpecificOverviewStat = $user->role === User::ROLE_CHILD
            ? ['label' => 'My groups', 'value' => $user->childGroups()->count()]
            : ['label' => 'Pending payments', 'value' => Payment::query()->where('status', 'pending')->count()];

        return $this->success([
            'mode' => 'standard',
            'overview_stats' => [
                ['label' => 'Trainings in 3 days', 'value' => $sessions->whereBetween('date', [now()->startOfDay(), now()->copy()->addDays(2)->endOfDay()])->count()],
                ['label' => 'Next training countdown', 'value' => $upcoming->isNotEmpty() ? 'Upcoming' : 'No upcoming'],
                $roleSpecificOverviewStat,
                ['label' => 'Attendance rate', 'value' => $attendanceRate],
            ],
            'next_trainings' => $upcoming->map(fn ($session) => [
                'id' => $session->id,
                'title' => $session->title ?: $session->group->name,
                'group_code' => $session->group->group_code,
                'group' => $session->group->display_name,
                'trainer' => trim(($session->group->coach->name ?? '').' '.($session->group->coach->surname ?? '')),
                'date' => $session->date->toDateString(),
                'start' => substr($session->start_time, 0, 5),
                'end' => substr($session->end_time, 0, 5),
                'child_ids' => $session->effectiveChildren()
                    ->whereIn('id', $childIds)
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id)
                    ->values(),
            ])->values(),
            'linked_children' => $linkedChildren->map(fn (User $child) => [
                'id' => $child->id,
                'name' => trim($child->name.' '.$child->surname),
                'email' => $child->email,
            ])->values(),
            'user' => [
                'name' => trim($user->name.' '.$user->surname),
                'email' => $user->email,
            ],
            'notifications' => $notifications->map(fn (Notification $notification) => [
                'id' => $notification->id,
                'title' => $notification->title,
                'text' => $notification->message,
                'time' => $notification->created_at->diffForHumans(),
                'unread' => ! $notification->is_read,
            ])->values(),
        ]);
    }
}
