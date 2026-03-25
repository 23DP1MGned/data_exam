<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\SessionTemplate;
use App\Models\TrainingSession;
use App\Models\User;
use App\Services\SessionTemplateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SessionLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_weekly_training_and_generated_instances_are_visible(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);
        $group = $this->createManagedGroup();

        Sanctum::actingAs($admin);

        $weekdays = [
            now()->format('D'),
            now()->copy()->addDay()->format('D'),
        ];

        $createResponse = $this->postJson('/api/sessions', [
            'group_id' => $group->id,
            'title' => 'Sprint Mechanics',
            'weekdays' => $weekdays,
            'start_time' => '18:00',
            'end_time' => '19:30',
            'price' => 42,
        ]);

        $createResponse->assertCreated();
        $this->assertGreaterThan(0, (int) $createResponse->json('data.created_count'));

        $template = SessionTemplate::query()->firstWhere('title', 'Sprint Mechanics');

        $this->assertNotNull($template);
        $this->assertSame($group->id, $template->group_id);
        $this->assertSame($weekdays, $template->weekdays);

        $generatedSessions = TrainingSession::query()
            ->where('session_template_id', $template->id)
            ->orderBy('date')
            ->get();

        $this->assertGreaterThanOrEqual(2, $generatedSessions->count());
        $this->assertTrue($generatedSessions->every(fn (TrainingSession $session) => $session->status === 'planned'));

        $listResponse = $this->getJson('/api/sessions');
        $listResponse->assertOk();

        $listedSessions = collect($listResponse->json('data'))
            ->where('title', 'Sprint Mechanics')
            ->values();

        $this->assertNotEmpty($listedSessions);
        $this->assertSame($group->display_name, $listedSessions->first()['group']);
        $this->assertSame($weekdays, $listedSessions->first()['weekdays']);
    }

    public function test_finished_planned_sessions_auto_complete_while_future_and_cancelled_sessions_keep_expected_statuses(): void
    {
        $group = $this->createManagedGroup();

        $pastPlanned = TrainingSession::query()->create([
            'group_id' => $group->id,
            'title' => 'Past Planned Session',
            'date' => now()->copy()->subDay()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '10:00',
            'price' => 30,
            'status' => 'planned',
        ]);

        $futurePlanned = TrainingSession::query()->create([
            'group_id' => $group->id,
            'title' => 'Future Planned Session',
            'date' => now()->copy()->addDay()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '10:00',
            'price' => 30,
            'status' => 'planned',
        ]);

        $pastCancelled = TrainingSession::query()->create([
            'group_id' => $group->id,
            'title' => 'Past Cancelled Session',
            'date' => now()->copy()->subDay()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '10:00',
            'price' => 30,
            'status' => 'cancelled',
        ]);

        app(SessionTemplateService::class)->syncAutomaticSessionStatuses();

        $this->assertSame('completed', $pastPlanned->fresh()->status);
        $this->assertSame('planned', $futurePlanned->fresh()->status);
        $this->assertSame('cancelled', $pastCancelled->fresh()->status);
    }

    private function createManagedGroup(): Group
    {
        $coach = User::factory()->create([
            'role' => User::ROLE_COACH,
        ]);

        return Group::query()->create([
            'name' => 'Sprint Lab',
            'group_number' => 3,
            'age_category' => '12-14',
            'price' => 60,
            'coach_id' => $coach->id,
        ]);
    }
}
