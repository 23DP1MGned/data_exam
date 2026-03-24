<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\ChildProfile;
use App\Models\CoachProfile;
use App\Models\ParentProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = User::query()->where('email', $request->validated('email'))->first();

        if (! $user || ! Hash::check($request->validated('password'), $user->password)) {
            return $this->error('Invalid credentials.', [
                'email' => ['The provided credentials are incorrect.'],
            ], 422);
        }

        $token = $user->createToken('frontend')->plainTextToken;

        return $this->success([
            'token' => $token,
            'user' => $this->formatUser($user->fresh()->load('parentProfile', 'childProfile', 'coachProfile')),
        ], 'Login successful.');
    }

    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'surname' => $validated['surname'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
        ]);

        if ($user->role === User::ROLE_PARENT) {
            ParentProfile::create([
                'user_id' => $user->id,
                'phone' => $validated['phone'] ?? null,
                'birth_date' => $validated['birth_date'] ?? null,
            ]);

            if (! empty($validated['child_identifier'])) {
                $child = User::query()
                    ->where('role', User::ROLE_CHILD)
                    ->where(function ($query) use ($validated) {
                        $query->where('email', $validated['child_identifier'])
                            ->orWhereHas('childProfile', fn ($profile) => $profile->where('personal_code', $validated['child_identifier']));
                    })
                    ->first();

                if ($child) {
                    $user->children()->syncWithoutDetaching([$child->id]);
                }
            }
        }

        if ($user->role === User::ROLE_CHILD) {
            ChildProfile::create([
                'user_id' => $user->id,
                'birth_date' => $validated['birth_date'] ?? null,
                'personal_code' => $validated['personal_code'],
            ]);
        }

        if ($user->role === User::ROLE_COACH) {
            CoachProfile::create([
                'user_id' => $user->id,
                'phone' => $validated['phone'] ?? null,
                'specialization' => $validated['specialization'] ?? null,
            ]);
        }

        $token = $user->createToken('frontend')->plainTextToken;

        return $this->success([
            'token' => $token,
            'user' => $this->formatUser($user->fresh()->load('parentProfile', 'childProfile', 'coachProfile')),
        ], 'Registration successful.', 201);
    }

    public function me(Request $request)
    {
        return $this->success($this->formatUser(
            $request->user()->load('parentProfile', 'childProfile', 'coachProfile', 'children')
        ));
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return $this->success([], 'Logout successful.');
    }

    private function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'surname' => $user->surname,
            'email' => $user->email,
            'role' => $user->role,
            'profile' => [
                'phone' => $user->parentProfile?->phone ?? $user->coachProfile?->phone,
                'birth_date' => $user->parentProfile?->birth_date?->toDateString() ?? $user->childProfile?->birth_date?->toDateString(),
                'specialization' => $user->coachProfile?->specialization,
                'personal_code' => $user->childProfile?->personal_code,
                'account_balance' => (float) ($user->parentProfile?->account_balance ?? 0),
            ],
            'children' => $user->children->map(fn (User $child) => [
                'id' => $child->id,
                'name' => $child->name,
                'surname' => $child->surname,
                'email' => $child->email,
            ])->values(),
        ];
    }
}
