<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\ChildProfile;
use App\Models\CoachProfile;
use App\Models\Group;
use App\Models\Notification;
use App\Models\ParentProfile;
use App\Models\Payment;
use App\Models\SessionTemplate;
use App\Models\TrainingSession;
use App\Models\User;
use App\Services\SessionTemplateService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    private int $paymentSequence = 1000;

    public function run(): void
    {
        $now = Carbon::now();

        $admin = User::factory()->create([
            'name' => 'Admin',
            'surname' => 'User',
            'email' => 'duhow@gmail.com',
            'password' => 'Maxutko99',
            'role' => User::ROLE_ADMIN,
        ]);

        $coaches = [
            'football' => $this->createCoach([
                'name' => 'Elena',
                'surname' => 'Rivera',
                'email' => 'coach@example.com',
                'phone' => '+37120000011',
                'birth_date' => '1987-03-14',
                'specialization' => 'Football',
            ]),
            'basketball' => $this->createCoach([
                'name' => 'Mark',
                'surname' => 'Jensen',
                'email' => 'coach2@example.com',
                'phone' => '+37120000012',
                'birth_date' => '1984-09-22',
                'specialization' => 'Basketball',
            ]),
            'athletics' => $this->createCoach([
                'name' => 'Sofia',
                'surname' => 'Novak',
                'email' => 'coach3@example.com',
                'phone' => '+37120000013',
                'birth_date' => '1990-01-08',
                'specialization' => 'Athletics',
            ]),
            'dance' => $this->createCoach([
                'name' => 'David',
                'surname' => 'Kim',
                'email' => 'coach4@example.com',
                'phone' => '+37120000014',
                'birth_date' => '1992-06-30',
                'specialization' => 'Dance',
            ]),
        ];

        $parents = [
            'sarah' => $this->createParent([
                'name' => 'Sarah',
                'surname' => 'Palmer',
                'email' => 'parent@example.com',
                'phone' => '+37120000021',
                'birth_date' => '1989-05-11',
                'account_balance' => 140,
            ]),
            'laura' => $this->createParent([
                'name' => 'Laura',
                'surname' => 'Stone',
                'email' => 'laura.parent@example.com',
                'phone' => '+37120000022',
                'birth_date' => '1990-08-17',
                'account_balance' => 95,
            ]),
            'emma' => $this->createParent([
                'name' => 'Emma',
                'surname' => 'Brooks',
                'email' => 'emma.parent@example.com',
                'phone' => '+37120000023',
                'birth_date' => '1986-02-03',
                'account_balance' => 60,
            ]),
            'daniel' => $this->createParent([
                'name' => 'Daniel',
                'surname' => 'Wright',
                'email' => 'daniel.parent@example.com',
                'phone' => '+37120000024',
                'birth_date' => '1984-12-19',
                'account_balance' => 180,
            ]),
            'olivia' => $this->createParent([
                'name' => 'Olivia',
                'surname' => 'Cole',
                'email' => 'olivia.parent@example.com',
                'phone' => '+37120000025',
                'birth_date' => '1991-07-25',
                'account_balance' => 130,
            ]),
        ];

        $children = [
            'ethan' => $this->createChild([
                'name' => 'Ethan',
                'surname' => 'Palmer',
                'email' => 'child@example.com',
                'birth_date' => '2013-05-19',
                'personal_code' => 'CHILD-001',
            ]),
            'oliver' => $this->createChild([
                'name' => 'Oliver',
                'surname' => 'Brooks',
                'email' => 'oliver.child@example.com',
                'birth_date' => '2012-11-03',
                'personal_code' => 'CHILD-002',
            ]),
            'mia' => $this->createChild([
                'name' => 'Mia',
                'surname' => 'Stone',
                'email' => 'mia.child@example.com',
                'birth_date' => '2012-09-03',
                'personal_code' => 'CHILD-003',
            ]),
            'leo' => $this->createChild([
                'name' => 'Leo',
                'surname' => 'Brooks',
                'email' => 'leo.child@example.com',
                'birth_date' => '2011-06-14',
                'personal_code' => 'CHILD-004',
            ]),
            'ivy' => $this->createChild([
                'name' => 'Ivy',
                'surname' => 'Brooks',
                'email' => 'ivy.child@example.com',
                'birth_date' => '2014-04-21',
                'personal_code' => 'CHILD-005',
            ]),
            'noah' => $this->createChild([
                'name' => 'Noah',
                'surname' => 'Wright',
                'email' => 'noah.child@example.com',
                'birth_date' => '2011-01-09',
                'personal_code' => 'CHILD-006',
            ]),
            'ava' => $this->createChild([
                'name' => 'Ava',
                'surname' => 'Cole',
                'email' => 'ava.child@example.com',
                'birth_date' => '2013-08-08',
                'personal_code' => 'CHILD-007',
            ]),
            'mila' => $this->createChild([
                'name' => 'Mila',
                'surname' => 'Cole',
                'email' => 'mila.child@example.com',
                'birth_date' => '2015-02-26',
                'personal_code' => 'CHILD-008',
            ]),
        ];

        $parents['sarah']->children()->attach([
            $children['ethan']->id,
            $children['oliver']->id,
        ]);
        $parents['laura']->children()->attach([
            $children['mia']->id,
        ]);
        $parents['emma']->children()->attach([
            $children['oliver']->id,
            $children['leo']->id,
            $children['ivy']->id,
        ]);
        $parents['daniel']->children()->attach([
            $children['noah']->id,
        ]);
        $parents['olivia']->children()->attach([
            $children['ava']->id,
            $children['mila']->id,
        ]);

        $groups = [
            'football' => $this->createGroup([
                'name' => 'Football Academy',
                'group_number' => 1,
                'age_category' => '10-12',
                'schedule_days' => 'Wed',
                'default_time' => '17:15',
                'price' => 48,
                'coach_id' => $coaches['football']->id,
            ], [$children['ethan'], $children['oliver']]),
            'basketball' => $this->createGroup([
                'name' => 'Street Basketball',
                'group_number' => 2,
                'age_category' => '11-14',
                'schedule_days' => 'Tue / Thu',
                'default_time' => '18:00',
                'price' => 52,
                'coach_id' => $coaches['basketball']->id,
            ], [$children['oliver'], $children['leo'], $children['ivy']]),
            'athletics' => $this->createGroup([
                'name' => 'Sprint Lab',
                'group_number' => 3,
                'age_category' => '10-13',
                'schedule_days' => 'Fri',
                'default_time' => '18:10',
                'price' => 64,
                'coach_id' => $coaches['athletics']->id,
            ], [$children['noah'], $children['ava']]),
            'dance' => $this->createGroup([
                'name' => 'Dance Flow',
                'group_number' => 4,
                'age_category' => '8-11',
                'schedule_days' => 'Mon',
                'default_time' => '16:45',
                'price' => 41,
                'coach_id' => $coaches['dance']->id,
            ], [$children['mia'], $children['mila']]),
        ];

        $templates = [
            'football' => SessionTemplate::create([
                'group_id' => $groups['football']->id,
                'title' => 'Football Rhythm',
                'weekdays' => ['Wed'],
                'excluded_dates' => [],
                'starts_on' => $now->copy()->subWeek()->toDateString(),
                'start_time' => '17:15',
                'end_time' => '18:25',
                'price' => 48,
                'is_active' => true,
            ]),
            'athletics' => SessionTemplate::create([
                'group_id' => $groups['athletics']->id,
                'title' => 'Sprint Mechanics',
                'weekdays' => ['Fri'],
                'excluded_dates' => [],
                'starts_on' => $now->copy()->subWeek()->toDateString(),
                'start_time' => '18:10',
                'end_time' => '19:10',
                'price' => 64,
                'is_active' => true,
            ]),
        ];

        app(SessionTemplateService::class)->ensureUpcomingSessionsGenerated($now->copy()->addMonth());

        $sessions = [
            'football_completed_old' => $this->createSession($groups['football'], 'Ball Control Basics', $now->copy()->subDays(9), '17:15', '18:15', 'completed', 45),
            'football_completed_recent' => $this->createSession($groups['football'], 'Press Resistance Drill', $now->copy()->subDays(3), '17:15', '18:20', 'completed', 48),
            'football_overdue' => $this->createSession($groups['football'], 'Finishing Challenge', $now->copy()->subDays(5), '17:15', '18:15', 'planned', 48),
            'football_future' => $this->createSession($groups['football'], 'Set Piece Workshop', $now->copy()->addDays(5), '17:15', '18:15', 'planned', 50),
            'football_cancelled' => $this->createSession($groups['football'], 'Friendly Match Prep', $now->copy()->addDays(9), '17:15', '18:15', 'cancelled', 48),

            'basketball_completed_old' => $this->createSession($groups['basketball'], 'Layup Circuit', $now->copy()->subDays(8), '18:00', '19:00', 'completed', 52),
            'basketball_completed_recent' => $this->createSession($groups['basketball'], 'Fast Break Clinic', $now->copy()->subDays(2), '18:00', '19:10', 'completed', 52),
            'basketball_overdue' => $this->createSession($groups['basketball'], 'Shot Selection Lab', $now->copy()->subDays(4), '18:00', '19:00', 'planned', 52),
            'basketball_future' => $this->createSession($groups['basketball'], 'Defensive Footwork', $now->copy()->addDays(4), '18:00', '19:00', 'planned', 55),
            'basketball_cancelled' => $this->createSession($groups['basketball'], 'Weekend Open Gym', $now->copy()->addDays(10), '18:00', '19:00', 'cancelled', 52),

            'athletics_completed_old' => $this->createSession($groups['athletics'], 'Sprint Technique Session', $now->copy()->subDays(10), '18:10', '19:10', 'completed', 64),
            'athletics_completed_recent' => $this->createSession($groups['athletics'], 'Reaction Speed Track', $now->copy()->subDays(1), '18:10', '19:10', 'completed', 64),
            'athletics_overdue' => $this->createSession($groups['athletics'], 'Endurance Circuit Session', $now->copy()->subDays(6), '18:10', '19:10', 'planned', 64),
            'athletics_future' => $this->createSession($groups['athletics'], 'Relay Practice Session', $now->copy()->addDays(3), '18:10', '19:10', 'planned', 64),
            'athletics_cancelled' => $this->createSession($groups['athletics'], 'Hill Sprint Lab', $now->copy()->addDays(11), '18:10', '19:10', 'cancelled', 64),

            'dance_completed_old' => $this->createSession($groups['dance'], 'Rhythm Foundations', $now->copy()->subDays(7), '16:45', '17:45', 'completed', 41),
            'dance_completed_recent' => $this->createSession($groups['dance'], 'Floor Pattern Session', $now->copy()->subDays(2), '16:45', '17:45', 'completed', 41),
            'dance_overdue' => $this->createSession($groups['dance'], 'Turn Technique Lab', $now->copy()->subDays(4), '16:45', '17:45', 'planned', 41),
            'dance_future' => $this->createSession($groups['dance'], 'Stage Presence Workshop', $now->copy()->addDays(6), '16:45', '17:45', 'planned', 44),
            'dance_cancelled' => $this->createSession($groups['dance'], 'Duet Rehearsal', $now->copy()->addDays(12), '16:45', '17:45', 'cancelled', 41),
        ];

        $this->seedAttendance($sessions, $groups);

        $footballRecurringSessions = TrainingSession::query()
            ->where('session_template_id', $templates['football']->id)
            ->orderBy('date')
            ->get();

        $athleticsRecurringSessions = TrainingSession::query()
            ->where('session_template_id', $templates['athletics']->id)
            ->orderBy('date')
            ->get();

        $this->createPayment(
            $parents['sarah'],
            $children['ethan'],
            'paid',
            'Card',
            [
                ['type' => 'session', 'session_id' => $sessions['football_completed_recent']->id, 'price' => 48],
            ],
            $now->copy()->subDays(2)->setTime(20, 15)
        );

        $this->createPayment(
            $parents['sarah'],
            $children['ethan'],
            'paid',
            'Card',
            [
                ['type' => 'session', 'session_id' => optional($footballRecurringSessions->first())->id, 'price' => 48],
            ],
            $now->copy()->subDay()->setTime(18, 40)
        );

        $this->createPayment(
            $parents['emma'],
            $children['ivy'],
            'paid',
            'Card',
            [
                ['type' => 'session', 'session_id' => $sessions['basketball_completed_recent']->id, 'price' => 52],
            ],
            $now->copy()->subDay()->setTime(21, 5)
        );

        $this->createPayment(
            $parents['daniel'],
            $children['noah'],
            'paid',
            'Account balance',
            [
                ['type' => 'month', 'month' => $now->format('Y-m'), 'price' => 128],
            ],
            $now->copy()->subDays(4)->setTime(9, 30)
        );

        $this->createPayment(
            $parents['daniel'],
            $children['noah'],
            'paid',
            'Card',
            [
                ['type' => 'session', 'session_id' => optional($athleticsRecurringSessions->first())->id, 'price' => 64],
            ],
            $now->copy()->subDays(3)->setTime(13, 10)
        );

        $this->createPayment(
            $parents['laura'],
            $children['mia'],
            'pending',
            'Card',
            [
                ['type' => 'month', 'month' => $now->format('Y-m'), 'price' => 82],
            ],
            $now->copy()->subDay()->setTime(10, 45)
        );

        $this->createPayment(
            $parents['emma'],
            $children['oliver'],
            'failed',
            'Card',
            [
                ['type' => 'month', 'month' => $now->format('Y-m'), 'price' => 90],
            ],
            $now->copy()->subDays(3)->setTime(8, 20)
        );

        $this->createPayment(
            $parents['olivia'],
            $children['ava'],
            'refunded',
            'Card',
            [
                ['type' => 'session', 'session_id' => $sessions['athletics_completed_old']->id, 'price' => 64],
            ],
            $now->copy()->subDays(6)->setTime(15, 55)
        );

        $this->createPayment(
            $parents['olivia'],
            $children['mila'],
            'paid',
            'Card',
            [
                ['type' => 'session', 'session_id' => $sessions['dance_future']->id, 'price' => 44],
            ],
            $now->copy()->subHours(18)
        );

        $allUsers = collect([$admin])
            ->merge(collect($coaches)->values())
            ->merge(collect($parents)->values())
            ->merge(collect($children)->values());

        foreach ($allUsers as $user) {
            Notification::create([
                'user_id' => $user->id,
                'title' => 'Welcome to SportSystem',
                'message' => 'Your demo workspace has been prepared with live test data.',
                'type' => 'system',
                'is_read' => false,
            ]);
        }

        foreach ([$parents['laura'], $parents['emma'], $parents['olivia']] as $parent) {
            Notification::create([
                'user_id' => $parent->id,
                'title' => 'Payment update',
                'message' => 'One or more recent payment records changed status in the demo environment.',
                'type' => 'payment',
                'is_read' => false,
            ]);
        }

        foreach ([$children['mia'], $children['oliver'], $children['ava']] as $child) {
            Notification::create([
                'user_id' => $child->id,
                'title' => 'Training reminder',
                'message' => 'Check your schedule and outstanding payments for upcoming sessions.',
                'type' => 'schedule',
                'is_read' => false,
            ]);
        }

        Notification::create([
            'user_id' => $admin->id,
            'title' => 'Demo dataset refreshed',
            'message' => 'The database was reseeded with mixed test scenarios for payments, attendance and recurring sessions.',
            'type' => 'system',
            'is_read' => false,
        ]);
    }

    private function createCoach(array $data): User
    {
        $user = User::factory()->create([
            'name' => $data['name'],
            'surname' => $data['surname'],
            'email' => $data['email'],
            'password' => 'password',
            'role' => User::ROLE_COACH,
        ]);

        CoachProfile::create([
            'user_id' => $user->id,
            'phone' => $data['phone'],
            'birth_date' => $data['birth_date'],
            'specialization' => $data['specialization'],
        ]);

        return $user;
    }

    private function createParent(array $data): User
    {
        $user = User::factory()->create([
            'name' => $data['name'],
            'surname' => $data['surname'],
            'email' => $data['email'],
            'password' => 'password',
            'role' => User::ROLE_PARENT,
        ]);

        ParentProfile::create([
            'user_id' => $user->id,
            'phone' => $data['phone'],
            'birth_date' => $data['birth_date'],
            'account_balance' => $data['account_balance'],
        ]);

        return $user;
    }

    private function createChild(array $data): User
    {
        $user = User::factory()->create([
            'name' => $data['name'],
            'surname' => $data['surname'],
            'email' => $data['email'],
            'password' => 'password',
            'role' => User::ROLE_CHILD,
        ]);

        ChildProfile::create([
            'user_id' => $user->id,
            'birth_date' => $data['birth_date'],
            'personal_code' => $data['personal_code'],
        ]);

        return $user;
    }

    private function createGroup(array $attributes, array $children): Group
    {
        $group = Group::create($attributes);
        $group->children()->attach(collect($children)->pluck('id')->all());

        return $group;
    }

    private function createSession(
        Group $group,
        string $title,
        Carbon $date,
        string $startTime,
        string $endTime,
        string $status,
        float $price
    ): TrainingSession {
        return TrainingSession::create([
            'group_id' => $group->id,
            'title' => $title,
            'date' => $date->toDateString(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'price' => $price,
            'status' => $status,
        ]);
    }

    private function seedAttendance(array $sessions, array $groups): void
    {
        $completedKeys = [
            'football_completed_old',
            'football_completed_recent',
            'basketball_completed_old',
            'basketball_completed_recent',
            'athletics_completed_old',
            'athletics_completed_recent',
            'dance_completed_old',
            'dance_completed_recent',
        ];

        foreach ($completedKeys as $index => $sessionKey) {
            $session = $sessions[$sessionKey];
            $children = $session->group->children()->orderBy('users.id')->get();

            foreach ($children as $childIndex => $child) {
                Attendance::create([
                    'session_id' => $session->id,
                    'user_id' => $child->id,
                    'status' => (($index + $childIndex) % 4 === 0) ? 'absent' : 'present',
                    'comment' => (($index + $childIndex) % 4 === 0) ? 'Missed demo session' : 'Checked in successfully',
                ]);
            }
        }
    }

    private function createPayment(
        User $parent,
        User $child,
        string $status,
        string $method,
        array $items,
        Carbon $createdAt
    ): Payment {
        $amount = collect($items)->sum('price');
        $payment = Payment::create([
            'parent_id' => $parent->id,
            'child_id' => $child->id,
            'amount' => $amount,
            'status' => $status,
            'method' => $method,
            'transaction_id' => $status === 'pending' ? null : $this->nextTransactionId(),
        ]);

        $payment->forceFill([
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ])->save();

        foreach ($items as $item) {
            $payment->items()->create([
                'type' => $item['type'],
                'session_id' => $item['session_id'] ?? null,
                'month' => $item['month'] ?? null,
                'price' => $item['price'],
            ]);
        }

        if ($status === 'paid' && strtolower($method) === 'account balance') {
            $parent->parentProfile?->decrement('account_balance', $amount);
        }

        if ($status === 'refunded') {
            $parent->parentProfile?->increment('account_balance', $amount);
        }

        $this->seedPaymentNotifications($payment);

        return $payment->fresh(['items']);
    }

    private function seedPaymentNotifications(Payment $payment): void
    {
        $parentMessages = [
            'paid' => 'A payment was completed successfully.',
            'pending' => 'A payment is waiting for confirmation.',
            'failed' => 'A payment attempt failed and needs attention.',
            'refunded' => 'A payment was refunded back to the account.',
        ];

        $childMessages = [
            'paid' => 'A new training payment was added to your activity.',
            'pending' => 'A payment for one of your trainings is still pending.',
            'failed' => 'A recent payment attempt for your activity failed.',
            'refunded' => 'One of your training payments was refunded.',
        ];

        Notification::create([
            'user_id' => $payment->parent_id,
            'title' => 'Payment '.ucfirst($payment->status),
            'message' => $parentMessages[$payment->status] ?? 'A payment record was updated.',
            'type' => 'payment',
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $payment->child_id,
            'title' => 'Payment '.ucfirst($payment->status),
            'message' => $childMessages[$payment->status] ?? 'A payment record was updated.',
            'type' => 'payment',
            'is_read' => false,
        ]);
    }

    private function nextTransactionId(): string
    {
        $this->paymentSequence++;

        return 'txn-demo-'.$this->paymentSequence;
    }
}
