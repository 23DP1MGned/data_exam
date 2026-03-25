<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\StoreBulkAttendanceRequest;
use App\Http\Requests\Attendance\StoreAttendanceRequest;
use App\Http\Requests\Attendance\UpdateAttendanceRequest;
use App\Models\Attendance;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Attendance::query()->with(['session.group.coach', 'user']);

        if ($request->filled('session_id')) {
            $query->where('session_id', $request->integer('session_id'));
        }

        if ($request->filled('group_id')) {
            $query->whereHas('session', fn ($builder) => $builder->where('group_id', $request->integer('group_id')));
        }

        if ($user->role === User::ROLE_COACH) {
            $query->whereHas('session.group', fn ($builder) => $builder->where('coach_id', $user->id));
        } elseif ($user->role === User::ROLE_PARENT) {
            $query->whereIn('user_id', $user->children()->pluck('users.id'));
        } elseif ($user->role === User::ROLE_CHILD) {
            $query->where('user_id', $user->id);
        }

        $records = $query->get();
        $totalMinutes = $records->where('status', 'present')->sum(function (Attendance $attendance) {
            $start = strtotime((string) $attendance->session->start_time);
            $end = strtotime((string) $attendance->session->end_time);

            return $start && $end && $end > $start ? (int) round(($end - $start) / 60) : 0;
        });

        return $this->success([
            'records' => $records->map(fn (Attendance $attendance) => $this->formatAttendance($attendance))->values(),
            'summary' => [
                'total_sessions' => $records->count(),
                'attended_sessions' => $records->where('status', 'present')->count(),
                'missed_sessions' => $records->where('status', 'absent')->count(),
                'attendance_rate' => $records->count() > 0
                    ? round(($records->where('status', 'present')->count() / $records->count()) * 100)
                    : 0,
                'total_training_time_minutes' => $totalMinutes,
            ],
        ]);
    }

    public function bulkStore(StoreBulkAttendanceRequest $request)
    {
        $session = TrainingSession::query()
            ->with(['group.children', 'group.coach'])
            ->findOrFail($request->integer('session_id'));

        if (! $this->canManageSession($request->user(), $session)) {
            return $this->error('Forbidden.', [], 403);
        }

        $allowedChildIds = $session->group->children->pluck('id')->all();
        $records = collect($request->validated('records'))
            ->map(function (array $record) use ($allowedChildIds, $session) {
                if (! in_array($record['user_id'], $allowedChildIds, true)) {
                    throw ValidationException::withMessages([
                        'records' => ['Selected child does not belong to this training group.'],
                    ]);
                }

                return Attendance::updateOrCreate(
                    [
                        'session_id' => $session->id,
                        'user_id' => $record['user_id'],
                    ],
                    [
                        'session_id' => $session->id,
                        'user_id' => $record['user_id'],
                        'status' => $record['status'],
                        'comment' => $record['comment'] ?? null,
                    ]
                );
            })
            ->map(fn (Attendance $attendance) => $this->formatAttendance($attendance->load(['session.group.coach', 'user'])))
            ->values();

        return $this->success([
            'session_id' => $session->id,
            'records' => $records,
        ], 'Attendance saved.');
    }

    public function store(StoreAttendanceRequest $request)
    {
        $attendance = Attendance::updateOrCreate(
            [
                'session_id' => $request->validated('session_id'),
                'user_id' => $request->validated('user_id'),
            ],
            $request->validated()
        );

        return $this->success($this->formatAttendance($attendance->load(['session.group', 'user'])), 'Attendance saved.', 201);
    }

    public function update(UpdateAttendanceRequest $request, Attendance $attendance)
    {
        $attendance->update($request->validated());

        return $this->success($this->formatAttendance($attendance->fresh()->load(['session.group', 'user'])), 'Attendance updated.');
    }

    private function formatAttendance(Attendance $attendance): array
    {
        return [
            'id' => $attendance->id,
            'session_id' => $attendance->session_id,
            'group_id' => $attendance->session->group_id,
            'user_id' => $attendance->user_id,
            'child_name' => trim($attendance->user->name.' '.$attendance->user->surname),
            'training' => $attendance->session->title ?: $attendance->session->group->display_name,
            'group' => $attendance->session->group->display_name,
            'trainer' => trim(($attendance->session->group->coach->name ?? '').' '.($attendance->session->group->coach->surname ?? '')),
            'date' => $attendance->session->date->toDateString(),
            'start_time' => substr((string) $attendance->session->start_time, 0, 5),
            'end_time' => substr((string) $attendance->session->end_time, 0, 5),
            'status' => $attendance->status,
            'comment' => $attendance->comment,
        ];
    }

    private function canManageSession(User $user, TrainingSession $session): bool
    {
        return $user->role === User::ROLE_ADMIN
            || ($user->role === User::ROLE_COACH && $session->group->coach_id === $user->id);
    }
}
