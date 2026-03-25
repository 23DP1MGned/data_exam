<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Attendance;
use App\Models\SessionTemplate;
use App\Models\TrainingSession;
use App\Models\User;
use App\Services\SessionTemplateService;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SessionLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

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
        Carbon::setTestNow(Carbon::parse('2026-03-25 12:00:00'));

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

    public function test_finished_session_auto_marks_missing_children_present(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-25 12:00:00'));

        $group = $this->createManagedGroup(withChildren: true);
        $children = $group->children()->orderBy('users.id')->get();

        $session = TrainingSession::query()->create([
            'group_id' => $group->id,
            'title' => 'Auto Attendance Session',
            'date' => '2026-03-24',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'price' => 30,
            'status' => 'planned',
        ]);

        app(SessionTemplateService::class)->syncAutomaticSessionStatuses();

        $this->assertSame('completed', $session->fresh()->status);

        foreach ($children as $child) {
            $this->assertDatabaseHas('attendance', [
                'session_id' => $session->id,
                'user_id' => $child->id,
                'status' => 'present',
            ]);
        }
    }

    public function test_existing_manual_attendance_is_preserved_when_defaults_are_auto_created(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-25 12:00:00'));

        $group = $this->createManagedGroup(withChildren: true);
        $children = $group->children()->orderBy('users.id')->get();

        $session = TrainingSession::query()->create([
            'group_id' => $group->id,
            'title' => 'Mixed Attendance Session',
            'date' => '2026-03-24',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'price' => 30,
            'status' => 'planned',
        ]);

        Attendance::query()->create([
            'session_id' => $session->id,
            'user_id' => $children[0]->id,
            'status' => 'absent',
            'comment' => 'Coach marked absent before auto-fill.',
        ]);

        app(SessionTemplateService::class)->syncAutomaticSessionStatuses();

        $this->assertDatabaseHas('attendance', [
            'session_id' => $session->id,
            'user_id' => $children[0]->id,
            'status' => 'absent',
        ]);

        $this->assertDatabaseHas('attendance', [
            'session_id' => $session->id,
            'user_id' => $children[1]->id,
            'status' => 'present',
        ]);
    }

    private function createManagedGroup(bool $withChildren = false): Group
    {
        $coach = User::factory()->create([
            'role' => User::ROLE_COACH,
        ]);

        $group = Group::query()->create([
            'name' => 'Sprint Lab',
            'group_number' => 3,
            'age_category' => '12-14',
            'price' => 60,
            'coach_id' => $coach->id,
        ]);

        if ($withChildren) {
            $children = User::factory()->count(2)->create([
                'role' => User::ROLE_CHILD,
            ]);

            $group->children()->attach($children->pluck('id'));
        }

        return $group;
    }
}
