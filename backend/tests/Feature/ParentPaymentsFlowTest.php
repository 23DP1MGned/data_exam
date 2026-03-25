<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\ParentProfile;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ParentPaymentsFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_parent_can_pay_for_a_month_and_monthly_coverage_hides_single_training_dues(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-28 12:00:00'));

        [$parent, $child, $group] = $this->createLinkedFamily();

        $visibleSession = TrainingSession::query()->create([
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

        Sanctum::actingAs($parent);

        $beforePayment = $this->getJson('/api/payments')->assertOk();

        $beforePayment
            ->assertJsonCount(1, 'data.due_monthly_payments')
            ->assertJsonPath('data.due_monthly_payments.0.month', '2026-04')
            ->assertJsonPath('data.due_monthly_payments.0.group_id', $group->id);

        $visibleSingleDue = collect($beforePayment->json('data.due_trainings'))
            ->firstWhere('session_id', $visibleSession->id);

        $this->assertNotNull($visibleSingleDue);

        $paymentResponse = $this->postJson('/api/payments', [
            'child_id' => $child->id,
            'method' => 'Card',
            'items' => [
                [
                    'type' => 'month',
                    'group_id' => $group->id,
                    'month' => '2026-04',
                ],
            ],
        ]);

        $paymentResponse
            ->assertCreated()
            ->assertJsonPath('data.payment_type', 'month')
            ->assertJsonPath('data.items.0.group_id', $group->id)
            ->assertJsonPath('data.items.0.month', '2026-04');

        $afterPayment = $this->getJson('/api/payments')->assertOk();

        $afterPayment->assertJsonCount(0, 'data.due_monthly_payments');

        $remainingVisibleSingles = collect($afterPayment->json('data.due_trainings'))
            ->pluck('session_id')
            ->filter()
            ->values()
            ->all();

        $this->assertNotContains($visibleSession->id, $remainingVisibleSingles);
    }

    public function test_parent_cannot_pay_for_a_month_after_paying_a_single_training_in_that_group_month(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-28 12:00:00'));

        [$parent, $child, $group] = $this->createLinkedFamily();

        $session = TrainingSession::query()->create([
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

        $this->postJson('/api/payments', [
            'child_id' => $child->id,
            'method' => 'Card',
            'items' => [
                [
                    'type' => 'month',
                    'group_id' => $group->id,
                    'month' => '2026-04',
                ],
            ],
        ])->assertUnprocessable()
            ->assertJsonPath('errors.items.0', 'Monthly payment is not available because some trainings in this month were already paid separately.');

        $listing = $this->getJson('/api/payments')->assertOk();

        $listing->assertJsonCount(0, 'data.due_monthly_payments');
    }

    private function createLinkedFamily(): array
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
            'name' => 'Football Academy',
            'group_number' => 1,
            'age_category' => '8-10',
            'price' => 120,
            'coach_id' => $coach->id,
        ]);

        $group->children()->attach($child->id);

        return [$parent, $child, $group];
    }
}
