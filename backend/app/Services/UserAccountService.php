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
            $childIds = collect($validated['child_ids'] ?? [])
                ->map(fn ($id) => (int) $id)
                ->filter()
                ->values()
                ->all();

            $user->children()->sync($childIds);

            return;
        }

        $user->parentProfile()?->delete();
        $user->children()->detach();

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
        $user->parents()->detach();

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
