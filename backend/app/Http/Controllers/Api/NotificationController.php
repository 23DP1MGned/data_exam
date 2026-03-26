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
        if ($request->user()->role === User::ROLE_PARENT) {
            $this->notificationService->syncParentPaymentNotifications($request->user());
        }

        return $this->success(
            $request->user()->notifications()->latest()->get()->map(fn (Notification $notification) => [
                'id' => $notification->id,
                'title' => $notification->title,
                'text' => $notification->message,
                'type' => $notification->type,
                'payload' => $notification->payload,
                'time' => $notification->created_at->diffForHumans(),
                'is_read' => $notification->is_read,
                'unread' => ! $notification->is_read,
            ])->values()
        );
    }

    public function update(UpdateNotificationRequest $request, Notification $notification)
    {
        if ($notification->user_id !== $request->user()->id) {
            return $this->error('Forbidden.', [], 403);
        }

        $notification->update($request->validated());

        return $this->success([
            'id' => $notification->id,
            'is_read' => $notification->is_read,
        ], 'Notification updated.');
    }
}
