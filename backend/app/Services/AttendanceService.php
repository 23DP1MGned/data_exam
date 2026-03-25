<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\TrainingSession;

class AttendanceService
{
    public function ensureCompletedSessionAttendance(TrainingSession $session): void
    {
        $session->loadMissing(['group.children', 'attendanceRecords']);

        if ($session->status !== 'completed') {
            return;
        }

        $existingChildIds = $session->attendanceRecords->pluck('user_id')->all();

        $session->group->children
            ->reject(fn ($child) => in_array($child->id, $existingChildIds, true))
            ->each(function ($child) use ($session) {
                Attendance::query()->firstOrCreate(
                    [
                        'session_id' => $session->id,
                        'user_id' => $child->id,
                    ],
                    [
                        'status' => 'present',
                        'comment' => 'Auto-marked present after session completion.',
                    ]
                );
            });
    }

    public function backfillCompletedSessionsAttendance(): void
    {
        TrainingSession::query()
            ->with(['group.children', 'attendanceRecords'])
            ->where('status', 'completed')
            ->get()
            ->each(fn (TrainingSession $session) => $this->ensureCompletedSessionAttendance($session));
    }
}
