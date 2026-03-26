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
}
