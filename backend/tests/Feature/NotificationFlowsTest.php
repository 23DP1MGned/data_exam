<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Notification;
use App\Models\ParentProfile;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NotificationFlowsTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_child_and_parent_receive_notifications_when_attendance_is_marked(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-26 12:00:00'));

        [$coach, $parent, $child, $group] = $this->createCoachFamilyGroup();

        $session = TrainingSession::query()->create([
            'group_id' => $group->id,
            'title' => 'Evening Practice',
            'date' => '2026-03-26',
            'start_time' => '18:00',
            'end_time' => '19:00',
            'price' => 30,
            'status' => 'planned',
        ]);

        Sanctum::actingAs($coach);

        $this->postJson('/api/attendance/bulk', [
            'session_id' => $session->id,
            'records' => [
                [
                    'user_id' => $child->id,
                    'status' => 'absent',
                ],
            ],
        ])->assertOk();

        $this->assertDatabaseHas('notifications', [
            'user_id' => $child->id,
            'title' => 'Attendance updated',
            'type' => 'attendance',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $parent->id,
            'title' => 'Child attendance updated',
            'type' => 'attendance',
        ]);
    }

    public function test_child_and_parent_receive_notifications_when_completed_session_is_auto_marked_present(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-26 12:00:00'));

        [$admin, $coach, $parent, $child, $group] = $this->createAdminCoachFamilyGroup();

        $session = TrainingSession::query()->create([
            'group_id' => $group->id,
            'title' => 'Auto Completed Session',
            'date' => '2026-03-26',
            'start_time' => '10:00',
            'end_time' => '11:00',
            'price' => 30,
            'status' => 'planned',
        ]);

        Sanctum::actingAs($admin);

        $this->patchJson("/api/sessions/{$session->id}/status", [
            'status' => 'completed',
        ])->assertOk();

        $this->assertDatabaseHas('notifications', [
            'user_id' => $child->id,
            'title' => 'Attendance updated automatically',
            'type' => 'attendance',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $parent->id,
            'title' => 'Child attendance updated automatically',
            'type' => 'attendance',
        ]);
    }

    public function test_child_and_parent_receive_notifications_when_session_is_cancelled_and_restored(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-26 12:00:00'));

        [$coach, $parent, $child, $group] = $this->createCoachFamilyGroup();

        $session = TrainingSession::query()->create([
            'group_id' => $group->id,
            'title' => 'Morning Sprint',
            'date' => '2026-03-28',
            'start_time' => '10:00',
            'end_time' => '11:00',
            'price' => 30,
            'status' => 'planned',
        ]);

        Sanctum::actingAs($coach);

        $this->patchJson("/api/sessions/{$session->id}/status", [
            'status' => 'cancelled',
        ])->assertOk();

        $this->patchJson("/api/sessions/{$session->id}/status", [
            'status' => 'planned',
        ])->assertOk();

        $this->assertDatabaseHas('notifications', [
            'user_id' => $child->id,
            'title' => 'Training cancelled',
            'type' => 'session',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $child->id,
            'title' => 'Training restored',
            'type' => 'session',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $parent->id,
            'title' => 'Child training cancelled',
            'type' => 'session',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $parent->id,
            'title' => 'Child training restored',
            'type' => 'session',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $coach->id,
            'title' => 'Session cancelled',
            'type' => 'session',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $coach->id,
            'title' => 'Session restored',
            'type' => 'session',
        ]);
    }

    public function test_child_and_parent_receive_notifications_when_added_to_one_time_session(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-26 12:00:00'));

        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $coach = User::factory()->create([
            'role' => User::ROLE_COACH,
        ]);

        $group = Group::query()->create([
            'name' => 'Extra Session Group',
            'group_number' => 12,
            'age_category' => '10-12',
            'price' => 50,
            'coach_id' => $coach->id,
        ]);

        $parent = User::factory()->create([
            'role' => User::ROLE_PARENT,
        ]);

        ParentProfile::query()->create([
            'user_id' => $parent->id,
            'account_balance' => 100,
        ]);

        $child = User::factory()->create([
            'role' => User::ROLE_CHILD,
        ]);

        $parent->children()->attach($child->id);

        $session = TrainingSession::query()->create([
            'group_id' => $group->id,
            'title' => 'Guest Tryout',
            'date' => '2026-03-30',
            'start_time' => '17:00',
            'end_time' => '18:00',
            'price' => 25,
            'status' => 'planned',
        ]);

        Sanctum::actingAs($admin);

        $this->postJson("/api/sessions/{$session->id}/children", [
            'child_id' => $child->id,
        ])->assertOk();

        $this->assertDatabaseHas('notifications', [
            'user_id' => $child->id,
            'title' => 'Extra training added',
            'type' => 'session',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $parent->id,
            'title' => 'Child added to extra training',
            'type' => 'session',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $coach->id,
            'title' => 'Child added to session',
            'type' => 'session',
        ]);
    }

    public function test_parent_and_child_receive_payment_successful_and_refunded_notifications(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-26 12:00:00'));

        [$admin, , $parent, $child, $group] = $this->createAdminCoachFamilyGroup();

        $session = TrainingSession::query()->create([
            'group_id' => $group->id,
            'title' => 'Payment Session',
            'date' => '2026-03-29',
            'start_time' => '18:00',
            'end_time' => '19:00',
            'price' => 35,
            'status' => 'planned',
        ]);

        Sanctum::actingAs($parent);

        $paymentId = $this->postJson('/api/payments', [
            'child_id' => $child->id,
            'method' => 'Card',
            'items' => [
                [
                    'type' => 'session',
                    'session_id' => $session->id,
                ],
            ],
        ])->assertCreated()
            ->json('data.id');

        $this->assertDatabaseHas('notifications', [
            'user_id' => $parent->id,
            'title' => 'Payment successful',
            'type' => 'payment',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $parent->id,
            'title' => 'Child payment recorded',
            'type' => 'payment',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $child->id,
            'title' => 'Training payment added',
            'type' => 'payment',
        ]);

        Sanctum::actingAs($admin);

        $this->postJson("/api/payments/{$paymentId}/refund")
            ->assertOk();

        $this->assertDatabaseHas('notifications', [
            'user_id' => $parent->id,
            'title' => 'Payment refunded',
            'type' => 'payment',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $child->id,
            'title' => 'Training payment refunded',
            'type' => 'payment',
        ]);
    }

    public function test_parent_and_child_receive_cancellation_credit_notifications_when_paid_single_session_is_cancelled_and_restored(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-26 12:00:00'));

        [$admin, , $parent, $child, $group] = $this->createAdminCoachFamilyGroup();

        $session = TrainingSession::query()->create([
            'group_id' => $group->id,
            'title' => 'Cancellation Credit Session',
            'date' => '2026-03-29',
            'start_time' => '18:00',
            'end_time' => '19:00',
            'price' => 35,
            'status' => 'planned',
        ]);

        Sanctum::actingAs($parent);

        $this->postJson('/api/payments', [
            'child_id' => $child->id,
            'method' => 'Card',
            'items' => [
                [
                    'type' => 'session',
                    'session_id' => $session->id,
                ],
            ],
        ])->assertCreated();

        Sanctum::actingAs($admin);

        $this->patchJson("/api/sessions/{$session->id}/status", [
            'status' => 'cancelled',
        ])->assertOk();

        $this->assertDatabaseHas('notifications', [
            'user_id' => $parent->id,
            'title' => 'Cancelled training credit added',
            'type' => 'payment',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $child->id,
            'title' => 'Cancelled training refunded',
            'type' => 'payment',
        ]);

        $this->patchJson("/api/sessions/{$session->id}/status", [
            'status' => 'planned',
        ])->assertOk();

        $this->assertDatabaseHas('notifications', [
            'user_id' => $parent->id,
            'title' => 'Cancelled training credit reversed',
            'type' => 'payment',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $child->id,
            'title' => 'Training restored',
            'type' => 'payment',
        ]);
    }

    public function test_child_and_parent_receive_notifications_when_single_session_schedule_is_updated(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-26 12:00:00'));

        [$admin, , $parent, $child, $group] = $this->createAdminCoachFamilyGroup();

        $session = TrainingSession::query()->create([
            'group_id' => $group->id,
            'title' => 'Original Session',
            'date' => '2026-03-29',
            'start_time' => '18:00',
            'end_time' => '19:00',
            'price' => 35,
            'status' => 'planned',
        ]);

        Sanctum::actingAs($admin);

        $this->putJson("/api/sessions/{$session->id}", [
            'group_id' => $group->id,
            'title' => 'Updated Session',
            'date' => '2026-03-30',
            'start_time' => '19:00',
            'end_time' => '20:00',
            'price' => 40,
            'status' => 'planned',
        ])->assertOk();

        $this->assertDatabaseHas('notifications', [
            'user_id' => $child->id,
            'title' => 'Training schedule updated',
            'type' => 'session',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $parent->id,
            'title' => 'Child training schedule updated',
            'type' => 'session',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $group->coach_id,
            'title' => 'Schedule updated',
            'type' => 'session',
        ]);
    }

    public function test_child_and_parent_receive_notifications_when_recurring_schedule_is_updated(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-26 12:00:00'));

        [$admin, , $parent, $child, $group] = $this->createAdminCoachFamilyGroup();

        Sanctum::actingAs($admin);

        $this->postJson('/api/sessions', [
            'group_id' => $group->id,
            'title' => 'Weekly Session',
            'weekdays' => ['Thu'],
            'start_time' => '18:00',
            'end_time' => '19:00',
            'price' => 35,
            'status' => 'planned',
        ])->assertCreated();

        $session = TrainingSession::query()
            ->where('group_id', $group->id)
            ->whereNotNull('session_template_id')
            ->orderBy('date')
            ->firstOrFail();

        $this->putJson("/api/sessions/{$session->id}", [
            'group_id' => $group->id,
            'title' => 'Weekly Session Updated',
            'weekdays' => ['Fri'],
            'start_time' => '19:00',
            'end_time' => '20:00',
            'price' => 40,
            'status' => 'planned',
        ])->assertOk();

        $this->assertDatabaseHas('notifications', [
            'user_id' => $child->id,
            'title' => 'Training schedule updated',
            'type' => 'session',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $parent->id,
            'title' => 'Child training schedule updated',
            'type' => 'session',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $group->coach_id,
            'title' => 'Recurring schedule updated',
            'type' => 'session',
        ]);
    }

    public function test_coach_receives_notification_when_child_is_removed_from_one_time_session(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-26 12:00:00'));

        [$admin, $coach, $parent, , $group] = $this->createAdminCoachFamilyGroup();

        $extraChild = User::factory()->create([
            'role' => User::ROLE_CHILD,
        ]);

        $parent->children()->attach($extraChild->id);

        $session = TrainingSession::query()->create([
            'group_id' => $group->id,
            'title' => 'Extra Removal Session',
            'date' => '2026-03-30',
            'start_time' => '17:00',
            'end_time' => '18:00',
            'price' => 25,
            'status' => 'planned',
        ]);

        Sanctum::actingAs($admin);

        $this->postJson("/api/sessions/{$session->id}/children", [
            'child_id' => $extraChild->id,
        ])->assertOk();

        $this->deleteJson("/api/sessions/{$session->id}/children/{$extraChild->id}")
            ->assertOk();

        $this->assertDatabaseHas('notifications', [
            'user_id' => $coach->id,
            'title' => 'Child removed from session',
            'type' => 'session',
        ]);
    }

    public function test_coach_receives_notifications_when_group_is_assigned_and_child_is_added_to_group(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-26 12:00:00'));

        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $coach = User::factory()->create([
            'role' => User::ROLE_COACH,
        ]);

        $parent = User::factory()->create([
            'role' => User::ROLE_PARENT,
        ]);

        ParentProfile::query()->create([
            'user_id' => $parent->id,
            'account_balance' => 100,
        ]);

        $child = User::factory()->create([
            'role' => User::ROLE_CHILD,
        ]);

        $parent->children()->attach($child->id);

        Sanctum::actingAs($admin);

        $this->postJson('/api/groups', [
            'name' => 'Assigned Group',
            'group_number' => 42,
            'age_category' => '9-11',
            'schedule_days' => 'Mon, Wed',
            'default_time' => '18:00',
            'price' => 60,
            'coach_id' => $coach->id,
            'child_ids' => [$child->id],
        ])->assertCreated();

        $this->assertDatabaseHas('notifications', [
            'user_id' => $coach->id,
            'title' => 'New group assigned',
            'type' => 'group',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $coach->id,
            'title' => 'Child moved to your group',
            'type' => 'group',
        ]);
    }

    public function test_coach_receives_notification_when_group_schedule_is_updated(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-26 12:00:00'));

        [$admin, $coach, , , $group] = $this->createAdminCoachFamilyGroup();

        Sanctum::actingAs($admin);

        $this->putJson("/api/groups/{$group->id}", [
            'name' => $group->name,
            'group_number' => $group->group_number,
            'age_category' => $group->age_category,
            'schedule_days' => 'Tue / Thu',
            'default_time' => '19:45',
            'price' => $group->price,
            'coach_id' => $group->coach_id,
            'child_ids' => $group->children()->pluck('users.id')->all(),
        ])->assertOk();

        $this->assertDatabaseHas('notifications', [
            'user_id' => $coach->id,
            'title' => 'Recurring schedule updated',
            'type' => 'group',
        ]);
    }

    public function test_coach_receives_notification_when_child_is_removed_from_group(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-26 12:00:00'));

        [$admin, $coach, , $child, $group] = $this->createAdminCoachFamilyGroup();

        Sanctum::actingAs($admin);

        $this->putJson("/api/groups/{$group->id}", [
            'name' => $group->name,
            'group_number' => $group->group_number,
            'age_category' => $group->age_category,
            'schedule_days' => $group->schedule_days,
            'default_time' => $group->default_time,
            'price' => $group->price,
            'coach_id' => $group->coach_id,
            'child_ids' => [],
        ])->assertOk();

        $this->assertDatabaseHas('notifications', [
            'user_id' => $coach->id,
            'title' => 'Child removed from your group',
            'type' => 'group',
        ]);
    }

    public function test_parent_receives_due_soon_and_overdue_notifications(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-28 12:00:00'));

        [, $parent, $child, $group] = $this->createCoachFamilyGroup();

        TrainingSession::query()->create([
            'group_id' => $group->id,
            'title' => 'April Opener',
            'date' => '2026-04-03',
            'start_time' => '18:00',
            'end_time' => '19:00',
            'price' => 35,
            'status' => 'planned',
        ]);

        Sanctum::actingAs($parent);

        $this->getJson('/api/notifications')
            ->assertOk();

        $this->assertDatabaseHas('notifications', [
            'user_id' => $parent->id,
            'title' => 'Payment due soon',
            'type' => 'payment',
        ]);

        Carbon::setTestNow(Carbon::parse('2026-04-04 12:00:00'));

        $this->getJson('/api/notifications')
            ->assertOk();

        $this->assertDatabaseHas('notifications', [
            'user_id' => $parent->id,
            'title' => 'Payment overdue',
            'type' => 'payment',
        ]);

        $this->assertNotNull(
            Notification::query()
                ->where('user_id', $parent->id)
                ->where('title', 'Payment due soon')
                ->whereNotNull('unique_key')
                ->first()
        );
    }

    public function test_parent_due_and_overdue_notifications_are_not_duplicated_when_synced_multiple_times(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-28 12:00:00'));

        [, , $parent, $child, $group] = $this->createAdminCoachFamilyGroup();

        TrainingSession::query()->create([
            'group_id' => $group->id,
            'title' => 'No Duplicate Due Session',
            'date' => '2026-04-03',
            'start_time' => '18:00',
            'end_time' => '19:00',
            'price' => 35,
            'status' => 'planned',
        ]);

        Sanctum::actingAs($parent);

        $this->getJson('/api/notifications')->assertOk();
        $this->getJson('/api/notifications')->assertOk();

        $this->assertSame(
            1,
            Notification::query()
                ->where('user_id', $parent->id)
                ->where('title', 'Payment due soon')
                ->count()
        );

        Carbon::setTestNow(Carbon::parse('2026-04-04 12:00:00'));

        $this->getJson('/api/notifications')->assertOk();
        $this->getJson('/api/notifications')->assertOk();

        $this->assertSame(
            1,
            Notification::query()
                ->where('user_id', $parent->id)
                ->where('title', 'Payment overdue')
                ->count()
        );
    }

    public function test_admin_sees_all_notifications(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-28 12:00:00'));

        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        [, $parent, $child] = $this->createCoachFamilyGroup();

        Notification::query()->create([
            'user_id' => $parent->id,
            'title' => 'Parent notification',
            'message' => 'Parent event',
            'type' => 'payment',
        ]);

        Notification::query()->create([
            'user_id' => $child->id,
            'title' => 'Child notification',
            'message' => 'Child event',
            'type' => 'attendance',
        ]);

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/notifications')
            ->assertOk()
            ->json('data');

        $titles = collect($response)->pluck('title')->all();

        $this->assertContains('Parent notification', $titles);
        $this->assertContains('Child notification', $titles);
    }

    public function test_admin_can_mark_any_notification_as_read(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-28 12:00:00'));

        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        [, , $parent] = $this->createAdminCoachFamilyGroup();

        $notification = Notification::query()->create([
            'user_id' => $parent->id,
            'title' => 'Parent notification',
            'message' => 'Parent event',
            'type' => 'payment',
            'is_read' => false,
        ]);

        Sanctum::actingAs($admin);

        $this->patchJson("/api/notifications/{$notification->id}", [
            'is_read' => true,
        ])->assertOk()
            ->assertJsonPath('data.is_read', true);

        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'is_read' => true,
        ]);
    }

    public function test_parent_can_mark_all_own_notifications_as_read_without_affecting_others(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-28 12:00:00'));

        [, $parent, $child] = $this->createCoachFamilyGroup();

        $parentNotification = Notification::query()->create([
            'user_id' => $parent->id,
            'title' => 'Parent notification',
            'message' => 'Parent event',
            'type' => 'payment',
            'is_read' => false,
        ]);

        $childNotification = Notification::query()->create([
            'user_id' => $child->id,
            'title' => 'Child notification',
            'message' => 'Child event',
            'type' => 'attendance',
            'is_read' => false,
        ]);

        Sanctum::actingAs($parent);

        $this->patchJson('/api/notifications/mark-all')
            ->assertOk()
            ->assertJsonPath('data.updated_count', 1);

        $this->assertDatabaseHas('notifications', [
            'id' => $parentNotification->id,
            'is_read' => true,
        ]);

        $this->assertDatabaseHas('notifications', [
            'id' => $childNotification->id,
            'is_read' => false,
        ]);
    }

    public function test_admin_can_mark_all_notifications_as_read(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-28 12:00:00'));

        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        [, $parent, $child] = $this->createCoachFamilyGroup();

        $parentNotification = Notification::query()->create([
            'user_id' => $parent->id,
            'title' => 'Parent notification',
            'message' => 'Parent event',
            'type' => 'payment',
            'is_read' => false,
        ]);

        $childNotification = Notification::query()->create([
            'user_id' => $child->id,
            'title' => 'Child notification',
            'message' => 'Child event',
            'type' => 'attendance',
            'is_read' => false,
        ]);

        Sanctum::actingAs($admin);

        $this->patchJson('/api/notifications/mark-all')
            ->assertOk()
            ->assertJsonPath('data.updated_count', 2);

        $this->assertDatabaseHas('notifications', [
            'id' => $parentNotification->id,
            'is_read' => true,
        ]);

        $this->assertDatabaseHas('notifications', [
            'id' => $childNotification->id,
            'is_read' => true,
        ]);
    }

    private function createCoachFamilyGroup(): array
    {
        $coach = User::factory()->create([
            'role' => User::ROLE_COACH,
        ]);

        $parent = User::factory()->create([
            'role' => User::ROLE_PARENT,
        ]);

        ParentProfile::query()->create([
            'user_id' => $parent->id,
            'account_balance' => 200,
        ]);

        $child = User::factory()->create([
            'role' => User::ROLE_CHILD,
        ]);

        $parent->children()->attach($child->id);

        $group = Group::query()->create([
            'name' => 'Notification Group',
            'group_number' => 5,
            'age_category' => '9-11',
            'price' => 60,
            'coach_id' => $coach->id,
        ]);

        $group->children()->attach($child->id);

        return [$coach, $parent, $child, $group];
    }

    private function createAdminCoachFamilyGroup(): array
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        [$coach, $parent, $child, $group] = $this->createCoachFamilyGroup();

        return [$admin, $coach, $parent, $child, $group];
    }
}
