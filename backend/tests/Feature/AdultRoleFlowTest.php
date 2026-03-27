<?php

namespace Tests\Feature;

use App\Models\AdultProfile;
use App\Models\Attendance;
use App\Models\Group;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdultRoleFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_adult_can_pay_for_own_single_session_but_not_for_other_participants(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-28 12:00:00'));

        [$adult, $group] = $this->createAdultParticipant();
        $otherAdult = $this->createAdultUser('other.adult@example.com');

        $session = TrainingSession::query()->create([
            'group_id' => $group->id,
            'title' => 'Adult Single Session',
            'date' => '2026-04-03',
            'start_time' => '18:00',
            'end_time' => '19:00',
            'price' => 35,
            'status' => 'planned',
        ]);

        Sanctum::actingAs($adult);

        $this->getJson('/api/payments')
            ->assertOk()
            ->assertJsonPath('data.due_trainings.0.child_id', $adult->id)
            ->assertJsonPath('data.due_trainings.0.session_id', $session->id);

        $this->postJson('/api/payments', [
            'child_id' => $otherAdult->id,
            'method' => 'Card',
            'items' => [
                [
                    'type' => 'session',
                    'session_id' => $session->id,
                ],
            ],
        ])->assertForbidden();

        $this->postJson('/api/payments', [
            'child_id' => $adult->id,
            'method' => 'Card',
            'items' => [
                [
                    'type' => 'session',
                    'session_id' => $session->id,
                ],
            ],
        ])->assertCreated()
            ->assertJsonPath('data.parent_id', $adult->id)
            ->assertJsonPath('data.child_id', $adult->id)
            ->assertJsonPath('data.payment_type', 'session');
    }

    public function test_adult_can_pay_monthly_for_own_group_membership(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-28 12:00:00'));

        [$adult, $group] = $this->createAdultParticipant(price: 64);

        TrainingSession::query()->create([
            'group_id' => $group->id,
            'title' => 'April Opener',
            'date' => '2026-04-03',
            'start_time' => '18:00',
            'end_time' => '19:00',
            'price' => 35,
            'status' => 'planned',
        ]);

        TrainingSession::query()->create([
            'group_id' => $group->id,
            'title' => 'April Second Week',
            'date' => '2026-04-10',
            'start_time' => '18:00',
            'end_time' => '19:00',
            'price' => 35,
            'status' => 'planned',
        ]);

        Sanctum::actingAs($adult);

        $this->getJson('/api/payments')
            ->assertOk()
            ->assertJsonCount(1, 'data.due_monthly_payments')
            ->assertJsonPath('data.due_monthly_payments.0.child_id', $adult->id)
            ->assertJsonPath('data.due_monthly_payments.0.group_id', $group->id)
            ->assertJsonPath('data.due_monthly_payments.0.month', '2026-04');

        $this->postJson('/api/payments', [
            'child_id' => $adult->id,
            'method' => 'Card',
            'items' => [
                [
                    'type' => 'month',
                    'group_id' => $group->id,
                    'month' => '2026-04',
                ],
            ],
        ])->assertCreated()
            ->assertJsonPath('data.parent_id', $adult->id)
            ->assertJsonPath('data.child_id', $adult->id)
            ->assertJsonPath('data.payment_type', 'month');

        $this->getJson('/api/payments')
            ->assertOk()
            ->assertJsonCount(0, 'data.due_monthly_payments');
    }

    public function test_cancelled_paid_adult_session_returns_credit_to_own_balance_and_restore_reverses_it(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-28 12:00:00'));

        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        [$adult, $group] = $this->createAdultParticipant(balance: 120);

        $session = TrainingSession::query()->create([
            'group_id' => $group->id,
            'title' => 'Paid Adult Session',
            'date' => '2026-04-03',
            'start_time' => '18:00',
            'end_time' => '19:00',
            'price' => 35,
            'status' => 'planned',
        ]);

        Sanctum::actingAs($adult);

        $this->postJson('/api/payments', [
            'child_id' => $adult->id,
            'method' => 'Card',
            'items' => [
                [
                    'type' => 'session',
                    'session_id' => $session->id,
                ],
            ],
        ])->assertCreated();

        $this->assertSame(120.0, (float) $adult->fresh()->adultProfile->account_balance);

        Sanctum::actingAs($admin);

        $this->patchJson("/api/sessions/{$session->id}/status", [
            'status' => 'cancelled',
        ])->assertOk();

        $this->assertSame(155.0, (float) $adult->fresh()->adultProfile->account_balance);

        $this->patchJson("/api/sessions/{$session->id}/status", [
            'status' => 'planned',
        ])->assertOk();

        $this->assertSame(120.0, (float) $adult->fresh()->adultProfile->account_balance);
    }

    public function test_adult_is_visible_in_coach_roster_and_can_have_attendance_marked(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-26 12:00:00'));

        $coach = User::factory()->create([
            'role' => User::ROLE_COACH,
        ]);

        [$adult, $group] = $this->createAdultParticipant(coach: $coach);

        $session = TrainingSession::query()->create([
            'group_id' => $group->id,
            'title' => 'Coach Attendance Session',
            'date' => '2026-03-27',
            'start_time' => '18:00',
            'end_time' => '19:00',
            'price' => 25,
            'status' => 'planned',
        ]);

        Sanctum::actingAs($coach);

        $this->getJson('/api/sessions')
            ->assertOk()
            ->assertJsonFragment([
                'id' => $adult->id,
                'name' => trim($adult->name.' '.$adult->surname),
            ]);

        $this->postJson('/api/attendance/bulk', [
            'session_id' => $session->id,
            'records' => [
                [
                    'user_id' => $adult->id,
                    'status' => 'absent',
                ],
            ],
        ])->assertOk();

        $this->assertDatabaseHas('attendance', [
            'session_id' => $session->id,
            'user_id' => $adult->id,
            'status' => 'absent',
        ]);
    }

    public function test_adult_can_access_own_dashboard_groups_sessions_and_attendance_only(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-28 12:00:00'));

        $coach = User::factory()->create([
            'role' => User::ROLE_COACH,
        ]);

        [$adult, $group] = $this->createAdultParticipant(coach: $coach);
        [$otherAdult, $otherGroup] = $this->createAdultParticipant();

        $ownSession = TrainingSession::query()->create([
            'group_id' => $group->id,
            'title' => 'Own Adult Session',
            'date' => '2026-03-29',
            'start_time' => '18:00',
            'end_time' => '19:00',
            'price' => 35,
            'status' => 'planned',
        ]);

        $otherSession = TrainingSession::query()->create([
            'group_id' => $otherGroup->id,
            'title' => 'Other Adult Session',
            'date' => '2026-03-29',
            'start_time' => '19:00',
            'end_time' => '20:00',
            'price' => 35,
            'status' => 'planned',
        ]);

        Attendance::query()->create([
            'session_id' => $ownSession->id,
            'user_id' => $adult->id,
            'status' => 'present',
        ]);

        Attendance::query()->create([
            'session_id' => $otherSession->id,
            'user_id' => $otherAdult->id,
            'status' => 'absent',
        ]);

        Sanctum::actingAs($adult);

        $this->getJson('/api/dashboard')
            ->assertOk()
            ->assertJsonPath('data.mode', 'standard')
            ->assertJsonFragment(['id' => $ownSession->id, 'title' => 'Own Adult Session'])
            ->assertJsonMissing(['id' => $otherSession->id, 'title' => 'Other Adult Session']);

        $this->getJson('/api/groups')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $group->id);

        $this->getJson('/api/sessions')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $ownSession->id)
            ->assertJsonPath('data.0.title', 'Own Adult Session');

        $this->getJson('/api/attendance')
            ->assertOk()
            ->assertJsonCount(1, 'data.records')
            ->assertJsonPath('data.records.0.session_id', $ownSession->id)
            ->assertJsonPath('data.records.0.user_id', $adult->id);
    }

    public function test_adult_added_only_to_one_time_session_sees_single_payment_without_group_monthly_access(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-28 12:00:00'));

        $coach = User::factory()->create([
            'role' => User::ROLE_COACH,
        ]);

        $adult = $this->createAdultUser('one.time.adult@example.com');

        $group = Group::query()->create([
            'name' => 'One Time Group',
            'group_number' => 91,
            'age_category' => '18+',
            'schedule_days' => 'Wed',
            'default_time' => '18:00',
            'price' => 58,
            'coach_id' => $coach->id,
        ]);

        $session = TrainingSession::query()->create([
            'group_id' => $group->id,
            'title' => 'Guest Adult Session',
            'date' => '2026-04-03',
            'start_time' => '18:00',
            'end_time' => '19:00',
            'price' => 35,
            'status' => 'planned',
        ]);

        $session->extraChildren()->attach($adult->id);

        Sanctum::actingAs($adult);

        $this->getJson('/api/groups')
            ->assertOk()
            ->assertJsonCount(0, 'data');

        $this->getJson('/api/sessions')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $session->id);

        $this->getJson('/api/payments')
            ->assertOk()
            ->assertJsonCount(1, 'data.due_trainings')
            ->assertJsonCount(0, 'data.due_monthly_payments')
            ->assertJsonPath('data.due_trainings.0.session_id', $session->id)
            ->assertJsonPath('data.due_trainings.0.child_id', $adult->id);
    }

    public function test_adult_receives_notifications_for_attendance_sessions_and_self_payments(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-28 12:00:00'));

        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $coach = User::factory()->create([
            'role' => User::ROLE_COACH,
        ]);

        [$adult, $group] = $this->createAdultParticipant(coach: $coach, balance: 90);

        $attendanceSession = TrainingSession::query()->create([
            'group_id' => $group->id,
            'title' => 'Adult Attendance Session',
            'date' => '2026-03-28',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'price' => 25,
            'status' => 'planned',
        ]);

        $refundedSession = TrainingSession::query()->create([
            'group_id' => $group->id,
            'title' => 'Adult Refunded Session',
            'date' => '2026-04-03',
            'start_time' => '18:00',
            'end_time' => '19:00',
            'price' => 35,
            'status' => 'planned',
        ]);

        $creditedSession = TrainingSession::query()->create([
            'group_id' => $group->id,
            'title' => 'Adult Credited Session',
            'date' => '2026-04-04',
            'start_time' => '18:00',
            'end_time' => '19:00',
            'price' => 40,
            'status' => 'planned',
        ]);

        Sanctum::actingAs($coach);

        $this->postJson('/api/attendance/bulk', [
            'session_id' => $attendanceSession->id,
            'records' => [
                [
                    'user_id' => $adult->id,
                    'status' => 'absent',
                ],
            ],
        ])->assertOk();

        Sanctum::actingAs($adult);

        $this->getJson('/api/notifications')
            ->assertOk()
            ->assertJsonFragment(['title' => 'Attendance updated'])
            ->assertJsonFragment(['title' => 'Payment due soon']);

        $refundedPaymentId = $this->postJson('/api/payments', [
            'child_id' => $adult->id,
            'method' => 'Card',
            'items' => [
                [
                    'type' => 'session',
                    'session_id' => $refundedSession->id,
                ],
            ],
        ])->assertCreated()->json('data.id');

        $creditedPaymentId = $this->postJson('/api/payments', [
            'child_id' => $adult->id,
            'method' => 'Card',
            'items' => [
                [
                    'type' => 'session',
                    'session_id' => $creditedSession->id,
                ],
            ],
        ])->assertCreated()->json('data.id');

        Sanctum::actingAs($admin);

        $this->postJson("/api/payments/{$refundedPaymentId}/refund")->assertOk();

        $this->patchJson("/api/sessions/{$creditedSession->id}/status", [
            'status' => 'cancelled',
        ])->assertOk();

        $this->patchJson("/api/sessions/{$creditedSession->id}/status", [
            'status' => 'planned',
        ])->assertOk();

        Sanctum::actingAs($adult);

        $this->getJson('/api/notifications')
            ->assertOk()
            ->assertJsonFragment(['title' => 'Payment successful'])
            ->assertJsonFragment(['title' => 'Payment refunded'])
            ->assertJsonFragment(['title' => 'Cancelled training credit added'])
            ->assertJsonFragment(['title' => 'Cancelled training credit reversed'])
            ->assertJsonFragment(['title' => 'Training cancelled'])
            ->assertJsonFragment(['title' => 'Training restored']);

        $unreadBeforeMarkAll = Notification::query()
            ->where('user_id', $adult->id)
            ->where('is_read', false)
            ->count();

        $this->patchJson('/api/notifications/mark-all')
            ->assertOk()
            ->assertJsonPath('data.updated_count', $unreadBeforeMarkAll);

        $this->assertDatabaseMissing('notifications', [
            'user_id' => $adult->id,
            'is_read' => false,
        ]);
    }

    private function createAdultParticipant(?User $coach = null, float $price = 50, float $balance = 0, ?string $email = null): array
    {
        $coach ??= User::factory()->create([
            'role' => User::ROLE_COACH,
        ]);

        $adult = $this->createAdultUser($email ?? ('adult.'.uniqid().'.example@example.com'), $balance);

        $group = Group::query()->create([
            'name' => 'Adult Flow Group',
            'group_number' => ((int) Group::query()->max('group_number')) + 1,
            'age_category' => '18+',
            'schedule_days' => 'Mon / Thu',
            'default_time' => '18:00',
            'price' => $price,
            'coach_id' => $coach->id,
        ]);

        $group->children()->attach($adult->id);

        return [$adult, $group];
    }

    private function createAdultUser(string $email, float $balance = 0): User
    {
        $adult = User::factory()->create([
            'email' => $email,
            'role' => User::ROLE_ADULT,
        ]);

        AdultProfile::query()->create([
            'user_id' => $adult->id,
            'phone' => '+37120000000',
            'birth_date' => '1998-01-10',
            'account_balance' => $balance,
        ]);

        return $adult;
    }
}
