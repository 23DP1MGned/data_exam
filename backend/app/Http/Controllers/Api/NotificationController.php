<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notifications\UpdateNotificationRequest;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        private readonly NotificationService $notificationService
    ) {
    }

    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === User::ROLE_PARENT) {
            $this->notificationService->syncParentPaymentNotifications($user);
        }

        $notifications = $user->role === User::ROLE_ADMIN
            ? Notification::query()->with('user')->latest()->get()
            : $user->notifications()->latest()->get();

        return $this->success(
            $notifications->map(fn (Notification $notification) => [
                'id' => $notification->id,
                'title' => $notification->title,
                'text' => $notification->message,
                'type' => $notification->type,
                'payload' => $notification->payload,
                'user_name' => $notification->user
                    ? trim($notification->user->name.' '.$notification->user->surname)
                    : null,
                'time' => $notification->created_at->diffForHumans(),
                'is_read' => $notification->is_read,
                'unread' => ! $notification->is_read,
            ])->values()
        );
    }

    public function update(UpdateNotificationRequest $request, Notification $notification)
    {
        if ($request->user()->role !== User::ROLE_ADMIN && $notification->user_id !== $request->user()->id) {
            return $this->error('Forbidden.', [], 403);
        }

        $notification->update($request->validated());

        return $this->success([
            'id' => $notification->id,
            'is_read' => $notification->is_read,
        ], 'Notification updated.');
    }

    public function markAllRead(Request $request)
    {
        $user = $request->user();

        $query = $user->role === User::ROLE_ADMIN
            ? Notification::query()
            : $user->notifications();

        $updatedCount = $query
            ->where('is_read', false)
            ->update([
                'is_read' => true,
            ]);

        return $this->success([
            'updated_count' => $updatedCount,
        ], 'All notifications marked as read.');
    }
}
