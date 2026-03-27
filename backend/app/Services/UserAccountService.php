<?php

namespace App\Services;

use App\Models\AdultProfile;
use App\Models\ChildProfile;
use App\Models\CoachProfile;
use App\Models\ParentProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserAccountService
{
    public function create(array $validated): User
    {
        return DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'surname' => $validated['surname'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => $validated['role'],
            ]);

            $this->syncRoleProfiles($user, $validated);

            return $user->fresh()->load('parentProfile', 'childProfile', 'coachProfile', 'adultProfile', 'children');
        });
    }

    public function update(User $user, array $validated): User
    {
        return DB::transaction(function () use ($user, $validated) {
            $payload = [
                'name' => $validated['name'],
                'surname' => $validated['surname'],
                'email' => $validated['email'],
                'role' => $validated['role'],
            ];

            if (! empty($validated['password'])) {
                $payload['password'] = $validated['password'];
            }

            $user->update($payload);
            $this->syncRoleProfiles($user, $validated);

            return $user->fresh()->load('parentProfile', 'childProfile', 'coachProfile', 'adultProfile', 'children');
        });
    }

    private function syncRoleProfiles(User $user, array $validated): void
    {
        if ($user->role === User::ROLE_PARENT) {
            ParentProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'phone' => $validated['phone'] ?? null,
                    'birth_date' => $validated['birth_date'] ?? null,
                    'account_balance' => $validated['account_balance'] ?? $user->parentProfile?->account_balance ?? 0,
                ]
            );

            $user->childProfile()?->delete();
            $user->coachProfile()?->delete();
            $user->adultProfile()?->delete();

            if (! empty($validated['child_identifier'])) {
                $child = User::query()
                    ->whereIn('role', [User::ROLE_CHILD, User::ROLE_ADULT])
                    ->where(function ($query) use ($validated) {
                        $query->where('email', $validated['child_identifier'])
                            ->orWhereHas('childProfile', fn ($profile) => $profile->where('personal_code', $validated['child_identifier']));
                    })
                    ->first();

                if ($child) {
                    $user->children()->syncWithoutDetaching([$child->id]);
                }
            }

            return;
        }

        $user->parentProfile()?->delete();

        if ($user->role === User::ROLE_CHILD) {
            ChildProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'birth_date' => $validated['birth_date'] ?? null,
                    'personal_code' => $validated['personal_code'],
                ]
            );

            $user->coachProfile()?->delete();
            $user->adultProfile()?->delete();
            return;
        }

        $user->childProfile()?->delete();

        if ($user->role === User::ROLE_COACH) {
            CoachProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'phone' => $validated['phone'] ?? null,
                    'birth_date' => $validated['birth_date'] ?? null,
                    'specialization' => $validated['specialization'] ?? null,
                ]
            );

            $user->adultProfile()?->delete();
            return;
        }

        $user->coachProfile()?->delete();

        if ($user->role === User::ROLE_ADULT) {
            AdultProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'phone' => $validated['phone'] ?? null,
                    'birth_date' => $validated['birth_date'] ?? null,
                    'account_balance' => $validated['account_balance'] ?? $user->adultProfile?->account_balance ?? 0,
                ]
            );

            $user->children()->detach();
            $user->parents()->detach();
            return;
        }

        $user->adultProfile()?->delete();
    }
}
