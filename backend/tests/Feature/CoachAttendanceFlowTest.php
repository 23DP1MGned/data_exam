<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CoachAttendanceFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_coach_can_bulk_mark_attendance_for_own_group_session(): void
    {
        [$coach, $group, $children] = $this->createCoachGroupWithChildren();

        $session = TrainingSession::query()->create([
            'group_id' => $group->id,
            'title' => 'Sprint Mechanics',
            'date' => now()->toDateString(),
            'start_time' => '18:00',
            'end_time' => '19:00',
            'price' => 32,
            'status' => 'planned',
        ]);

        Sanctum::actingAs($coach);

        $response = $this->postJson('/api/attendance/bulk', [
            'session_id' => $session->id,
            'records' => [
                [
                    'user_id' => $children[0]->id,
                    'status' => 'present',
                ],
                [
                    'user_id' => $children[1]->id,
                    'status' => 'absent',
                ],
            ],
        ]);

        $response->assertOk()
            ->assertJsonCount(2, 'data.records')
            ->assertJsonPath('data.records.0.group_id', $group->id);

        $this->assertDatabaseHas('attendance', [
            'session_id' => $session->id,
            'user_id' => $children[0]->id,
            'status' => 'present',
        ]);

        $this->assertDatabaseHas('attendance', [
            'session_id' => $session->id,
            'user_id' => $children[1]->id,
            'status' => 'absent',
        ]);
    }

    public function test_attendance_index_can_be_filtered_by_group_for_coach(): void
    {
        [$coach, $firstGroup, $children] = $this->createCoachGroupWithChildren();

        $secondGroup = Group::query()->create([
            'name' => 'Rhythm Lab',
            'group_number' => 9,
            'age_category' => '9-12',
            'price' => 60,
            'coach_id' => $coach->id,
        ]);

        $secondGroup->children()->attach(
            User::factory()->create(['role' => User::ROLE_CHILD])->id
        );

        $firstSession = TrainingSession::query()->create([
            'group_id' => $firstGroup->id,
            'title' => 'First Group Session',
            'date' => now()->toDateString(),
            'start_time' => '18:00',
            'end_time' => '19:00',
            'price' => 32,
            'status' => 'planned',
        ]);

        $secondSession = TrainingSession::query()->create([
            'group_id' => $secondGroup->id,
            'title' => 'Second Group Session',
            'date' => now()->toDateString(),
            'start_time' => '19:30',
            'end_time' => '20:30',
            'price' => 32,
            'status' => 'planned',
        ]);

        $this->postJsonAsCoach($coach, '/api/attendance/bulk', [
            'session_id' => $firstSession->id,
            'records' => [
                [
                    'user_id' => $children[0]->id,
                    'status' => 'present',
                ],
            ],
        ])->assertOk();

        $this->postJsonAsCoach($coach, '/api/attendance/bulk', [
            'session_id' => $secondSession->id,
            'records' => [
                [
                    'user_id' => $secondGroup->children()->first()->id,
                    'status' => 'absent',
                ],
            ],
        ])->assertOk();

        Sanctum::actingAs($coach);

        $response = $this->getJson("/api/attendance?group_id={$firstGroup->id}");

        $response->assertOk()
            ->assertJsonCount(1, 'data.records')
            ->assertJsonPath('data.records.0.group_id', $firstGroup->id)
            ->assertJsonPath('data.records.0.training', 'First Group Session');
    }

    public function test_coach_cannot_mark_attendance_for_cancelled_session(): void
    {
        [$coach, $group, $children] = $this->createCoachGroupWithChildren();

        $session = TrainingSession::query()->create([
            'group_id' => $group->id,
            'title' => 'Cancelled Session',
            'date' => now()->toDateString(),
            'start_time' => '18:00',
            'end_time' => '19:00',
            'price' => 32,
            'status' => 'cancelled',
        ]);

        Sanctum::actingAs($coach);

        $this->postJson('/api/attendance/bulk', [
            'session_id' => $session->id,
            'records' => [
                [
                    'user_id' => $children[0]->id,
                    'status' => 'present',
                ],
            ],
        ])->assertStatus(422)
            ->assertJsonPath('message', 'Attendance cannot be marked for cancelled sessions.');

        $this->assertDatabaseMissing('attendance', [
            'session_id' => $session->id,
            'user_id' => $children[0]->id,
        ]);
    }

    public function test_coach_can_bulk_mark_large_group_session(): void
    {
        $coach = User::factory()->create([
            'role' => User::ROLE_COACH,
        ]);

        $group = Group::query()->create([
            'name' => 'Large Group Lab',
            'group_number' => 10,
            'age_category' => '10-14',
            'price' => 60,
            'coach_id' => $coach->id,
        ]);

        $children = User::factory()->count(30)->create([
            'role' => User::ROLE_CHILD,
        ]);

        $group->children()->attach($children->pluck('id'));

        $session = TrainingSession::query()->create([
            'group_id' => $group->id,
            'title' => 'Big Attendance Session',
            'date' => now()->toDateString(),
            'start_time' => '18:00',
            'end_time' => '19:00',
            'price' => 32,
            'status' => 'planned',
        ]);

        Sanctum::actingAs($coach);

        $response = $this->postJson('/api/attendance/bulk', [
            'session_id' => $session->id,
            'records' => $children->map(fn (User $child) => [
                'user_id' => $child->id,
                'status' => 'present',
            ])->all(),
        ]);

        $response->assertOk()
            ->assertJsonCount(30, 'data.records');

        foreach ($children as $child) {
            $this->assertDatabaseHas('attendance', [
                'session_id' => $session->id,
                'user_id' => $child->id,
                'status' => 'present',
            ]);
        }
    }

    public function test_group_filter_keeps_multiple_sessions_from_same_day_separate(): void
    {
        [$coach, $group, $children] = $this->createCoachGroupWithChildren();

        $firstSession = TrainingSession::query()->create([
            'group_id' => $group->id,
            'title' => 'Morning Session',
            'date' => now()->toDateString(),
            'start_time' => '10:00',
            'end_time' => '11:00',
            'price' => 32,
            'status' => 'planned',
        ]);

        $secondSession = TrainingSession::query()->create([
            'group_id' => $group->id,
            'title' => 'Evening Session',
            'date' => now()->toDateString(),
            'start_time' => '18:00',
            'end_time' => '19:00',
            'price' => 32,
            'status' => 'planned',
        ]);

        Sanctum::actingAs($coach);

        $this->postJson('/api/attendance/bulk', [
            'session_id' => $firstSession->id,
            'records' => [
                ['user_id' => $children[0]->id, 'status' => 'present'],
            ],
        ])->assertOk();

        $this->postJson('/api/attendance/bulk', [
            'session_id' => $secondSession->id,
            'records' => [
                ['user_id' => $children[1]->id, 'status' => 'absent'],
            ],
        ])->assertOk();

        $response = $this->getJson("/api/attendance?group_id={$group->id}");

        $records = collect($response->json('data.records'));

        $this->assertTrue($records->contains(fn (array $record) => $record['session_id'] === $firstSession->id && $record['training'] === 'Morning Session'));
        $this->assertTrue($records->contains(fn (array $record) => $record['session_id'] === $secondSession->id && $record['training'] === 'Evening Session'));
    }

    public function test_extra_session_child_can_be_marked_in_attendance(): void
    {
        [$coach, $group] = $this->createCoachGroupWithChildren();

        $extraChild = User::factory()->create([
            'role' => User::ROLE_CHILD,
        ]);

        $session = TrainingSession::query()->create([
            'group_id' => $group->id,
            'title' => 'One-off Guest Session',
            'date' => now()->toDateString(),
            'start_time' => '18:00',
            'end_time' => '19:00',
            'price' => 32,
            'status' => 'planned',
        ]);

        Sanctum::actingAs($coach);

        $response = $this->postJson("/api/sessions/{$session->id}/children", [
            'child_id' => $extraChild->id,
        ])->assertOk();

        $students = collect($response->json('data.students'));
        $this->assertTrue($students->contains(fn (array $student) => $student['id'] === $extraChild->id));

        $this->postJson('/api/attendance/bulk', [
            'session_id' => $session->id,
            'records' => [
                [
                    'user_id' => $extraChild->id,
                    'status' => 'present',
                ],
            ],
        ])->assertOk();

        $this->assertDatabaseHas('attendance', [
            'session_id' => $session->id,
            'user_id' => $extraChild->id,
            'status' => 'present',
        ]);
    }

    private function postJsonAsCoach(User $coach, string $uri, array $payload)
    {
        Sanctum::actingAs($coach);

        return $this->postJson($uri, $payload);
    }

    private function createCoachGroupWithChildren(): array
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

        $children = User::factory()->count(2)->create([
            'role' => User::ROLE_CHILD,
        ]);

        $group->children()->attach($children->pluck('id'));

        return [$coach, $group, $children->values()];
    }
}
