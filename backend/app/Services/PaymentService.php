<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function create(array $validated, int $parentId): Payment
    {
        return DB::transaction(function () use ($validated, $parentId) {
            $amount = collect($validated['items'])->sum('price');
            $status = $validated['status'] ?? 'paid';

            $payment = Payment::create([
                'parent_id' => $parentId,
                'child_id' => $validated['child_id'],
                'amount' => $amount,
                'status' => $status,
                'method' => $validated['method'],
                'transaction_id' => $status === 'paid' ? Str::uuid()->toString() : null,
            ]);

            foreach ($validated['items'] as $item) {
                $payment->items()->create([
                    'type' => $item['type'],
                    'session_id' => $item['session_id'] ?? null,
                    'month' => $item['month'] ?? null,
                    'price' => $item['price'],
                ]);
            }

            if ($payment->status === 'paid' && strtolower($payment->method) === 'account balance') {
                $parent = User::query()->with('parentProfile')->findOrFail($parentId);

                if (! $parent->parentProfile || (float) $parent->parentProfile->account_balance < $amount) {
                    throw ValidationException::withMessages([
                        'method' => ['Insufficient account balance.'],
                    ]);
                }

                $parent->parentProfile->decrement('account_balance', $amount);
            }

            if ($payment->status === 'paid') {
                Notification::create([
                    'user_id' => $payment->parent_id,
                    'title' => 'Payment successful',
                    'message' => 'Your payment was processed successfully.',
                    'type' => 'payment',
                    'is_read' => false,
                ]);

                Notification::create([
                    'user_id' => $payment->child_id,
                    'title' => 'Training payment added',
                    'message' => 'A payment was added to your account activity.',
                    'type' => 'payment',
                    'is_read' => false,
                ]);
            }

            return $payment->load('items');
        });
    }

    public function refund(Payment $payment): Payment
    {
        return DB::transaction(function () use ($payment) {
            if ($payment->status !== 'paid') {
                throw ValidationException::withMessages([
                    'status' => ['Only paid payments can be refunded.'],
                ]);
            }

            $payment->update([
                'status' => 'refunded',
            ]);

            $parent = User::query()->with('parentProfile')->findOrFail($payment->parent_id);

            if ($parent->parentProfile) {
                $parent->parentProfile->increment('account_balance', $payment->amount);
            }

            Notification::create([
                'user_id' => $payment->parent_id,
                'title' => 'Payment refunded',
                'message' => 'A payment was refunded and returned to your account balance.',
                'type' => 'payment',
                'is_read' => false,
            ]);

            Notification::create([
                'user_id' => $payment->child_id,
                'title' => 'Training payment refunded',
                'message' => 'A payment linked to your training activity was refunded.',
                'type' => 'payment',
                'is_read' => false,
            ]);

            return $payment->fresh()->load(['parent', 'child', 'items.session.group']);
        });
    }
}
