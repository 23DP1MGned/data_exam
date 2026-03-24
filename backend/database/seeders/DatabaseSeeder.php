<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\ChildProfile;
use App\Models\CoachProfile;
use App\Models\Group;
use App\Models\Notification;
use App\Models\ParentProfile;
use App\Models\Payment;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin',
            'surname' => 'User',
            'email' => 'duhow@gmail.com',
            'password' => 'Maxutko99',
            'role' => User::ROLE_ADMIN,
        ]);

        $coach = User::factory()->create([
            'name' => 'Coach',
            'surname' => 'User',
            'email' => 'coach@example.com',
            'role' => User::ROLE_COACH,
        ]);

        CoachProfile::create([
            'user_id' => $coach->id,
            'phone' => '+37120000001',
            'specialization' => 'Football',
        ]);

        $parent = User::factory()->create([
            'name' => 'Parent',
            'surname' => 'User',
            'email' => 'parent@example.com',
            'role' => User::ROLE_PARENT,
        ]);

        ParentProfile::create([
            'user_id' => $parent->id,
            'phone' => '+37120000002',
            'birth_date' => '1988-04-11',
            'account_balance' => 1560,
        ]);

        $child = User::factory()->create([
            'name' => 'Child',
            'surname' => 'User',
            'email' => 'child@example.com',
            'role' => User::ROLE_CHILD,
        ]);

        ChildProfile::create([
            'user_id' => $child->id,
            'birth_date' => '2013-05-19',
            'personal_code' => 'CHILD-001',
        ]);

        $parent->children()->attach($child->id);

        $group = Group::create([
            'name' => 'Football',
            'group_number' => 1,
            'age_category' => '12-14',
            'schedule_days' => 'Mon / Wed',
            'default_time' => '17:00',
            'price' => 50,
            'coach_id' => $coach->id,
        ]);

        $group->children()->attach($child->id);

        $completedSession = TrainingSession::create([
            'group_id' => $group->id,
            'title' => 'Ball Control Training',
            'date' => Carbon::now()->subDays(2)->toDateString(),
            'start_time' => '17:00',
            'end_time' => '18:00',
            'status' => 'completed',
        ]);

        $plannedSession = TrainingSession::create([
            'group_id' => $group->id,
            'title' => 'Speed & Dribbling Session',
            'date' => Carbon::now()->addDay()->toDateString(),
            'start_time' => '17:30',
            'end_time' => '18:30',
            'status' => 'planned',
        ]);

        Attendance::create([
            'session_id' => $completedSession->id,
            'user_id' => $child->id,
            'status' => 'present',
            'comment' => 'On time',
        ]);

        $payment = Payment::create([
            'parent_id' => $parent->id,
            'child_id' => $child->id,
            'amount' => 25.00,
            'status' => 'paid',
            'method' => 'Card',
            'transaction_id' => 'txn-demo-001',
        ]);

        $payment->items()->create([
            'type' => 'session',
            'session_id' => $completedSession->id,
            'month' => null,
            'price' => 25.00,
        ]);

        $monthlyPayment = Payment::create([
            'parent_id' => $parent->id,
            'child_id' => $child->id,
            'amount' => 80.00,
            'status' => 'pending',
            'method' => 'Account balance',
            'transaction_id' => null,
        ]);

        $monthlyPayment->items()->create([
            'type' => 'month',
            'session_id' => null,
            'month' => Carbon::now()->format('Y-m'),
            'price' => 80.00,
        ]);

        foreach ([$admin, $coach, $parent, $child] as $user) {
            Notification::create([
                'user_id' => $user->id,
                'title' => 'Welcome to SportSystem',
                'message' => 'Your account is ready to use.',
                'type' => 'system',
                'is_read' => false,
            ]);
        }

        Notification::create([
            'user_id' => $parent->id,
            'title' => 'Payment successful',
            'message' => 'Your latest session payment has been recorded.',
            'type' => 'payment',
            'is_read' => false,
        ]);
    }
}
