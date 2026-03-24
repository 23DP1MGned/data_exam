<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Models\User;
use App\Services\UserAccountService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private readonly UserAccountService $userAccountService)
    {
    }

    public function index(Request $request)
    {
        $search = strtolower(trim((string) $request->query('search', '')));

        $users = User::query()
            ->with(['parentProfile', 'childProfile', 'coachProfile', 'children', 'parents'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder
                        ->whereRaw('LOWER(name) like ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(surname) like ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(email) like ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(role) like ?', ["%{$search}%"]);
                });
            })
            ->latest()
            ->get();

        return $this->success($users->map(fn (User $user) => $this->formatUser($user))->values());
    }

    public function store(StoreUserRequest $request)
    {
        $user = $this->userAccountService->create($request->validated());

        return $this->success($this->formatUser($user), 'User created.', 201);
    }

    public function show(User $user)
    {
        return $this->success($this->formatUser(
            $user->load('parentProfile', 'childProfile', 'coachProfile', 'children', 'parents')
        ));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $updatedUser = $this->userAccountService->update($user, $request->validated());

        return $this->success($this->formatUser($updatedUser), 'User updated.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return $this->success([], 'User deleted.');
    }

    private function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'surname' => $user->surname,
            'full_name' => trim($user->name.' '.$user->surname),
            'email' => $user->email,
            'role' => $user->role,
            'phone' => $user->parentProfile?->phone ?? $user->coachProfile?->phone,
            'birth_date' => $user->parentProfile?->birth_date?->toDateString()
                ?? $user->childProfile?->birth_date?->toDateString()
                ?? $user->coachProfile?->birth_date?->toDateString(),
            'specialization' => $user->coachProfile?->specialization,
            'personal_code' => $user->childProfile?->personal_code,
            'account_balance' => (float) ($user->parentProfile?->account_balance ?? 0),
            'children' => $user->children->map(fn (User $child) => [
                'id' => $child->id,
                'name' => trim($child->name.' '.$child->surname),
                'email' => $child->email,
            ])->values(),
            'parents' => $user->parents->map(fn (User $parent) => [
                'id' => $parent->id,
                'name' => trim($parent->name.' '.$parent->surname),
                'email' => $parent->email,
            ])->values(),
        ];
    }
}
