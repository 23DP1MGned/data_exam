<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\TrainingSession;

class AttendanceService
{
    public function __construct(
        private readonly NotificationService $notificationService
    ) {
    }

    public function ensureCompletedSessionAttendance(TrainingSession $session): void
    {
        $session->loadMissing(['group.children', 'extraChildren', 'attendanceRecords']);

        if ($session->status !== 'completed') {
            return;
        }

        $existingChildIds = $session->attendanceRecords->pluck('user_id')->all();

        $session->effectiveChildren()
            ->reject(fn ($child) => in_array($child->id, $existingChildIds, true))
            ->each(function ($child) use ($session) {
                $attendance = Attendance::query()->firstOrCreate(
                    [
                        'session_id' => $session->id,
                        'user_id' => $child->id,
                    ],
                    [
                        'status' => 'present',
                        'comment' => 'Auto-marked present after session completion.',
                    ]
                );

                if ($attendance->wasRecentlyCreated) {
                    $this->notificationService->notifyAttendanceUpdated(
                        $attendance->loadMissing(['session.group.coach', 'user.parents']),
                        true
                    );
                }
            });
    }

    public function backfillCompletedSessionsAttendance(): void
    {
        TrainingSession::query()
            ->with(['group.children', 'extraChildren', 'attendanceRecords'])
            ->where('status', 'completed')
            ->get()
            ->each(fn (TrainingSession $session) => $this->ensureCompletedSessionAttendance($session));
    }
}
