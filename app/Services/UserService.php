<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserService
{
    /**
     * Validate & create a user.
     */
   public function create(Request $request): User
{
    $validated = $request->validate([
        'name'        => 'required|string|max:255',
        'email'       => 'required|string|email|max:255|unique:users',
        'password'    => ['required', 'confirmed', Rules\Password::defaults()],
        'roles'       => 'array',
        'roles.*'     => 'string|exists:roles,name',
        'permissions' => 'array',
        'permissions.*' => 'string|exists:permissions,name',
    ]);

    $user = User::create([
        'name'     => $validated['name'],
        'email'    => $validated['email'],
        'password' => Hash::make($validated['password']),
    ]);

    if (!empty($validated['roles'])) {
        $user->syncRoles($validated['roles']);
    }

    if (!empty($validated['permissions'])) {
        $user->syncPermissions($validated['permissions']);
    }

    return $user;
}

    /**
     * Validate & update an existing user.
     */
   public function update(Request $request, User $user): User
{
    $rules = [
        'name'        => 'required|string|max:255',
        'email'       => 'required|string|email|max:255|unique:users,email,' . $user->id,
        'roles'       => 'array',
        'roles.*'     => 'string|exists:roles,name',
        'permissions' => 'array',
        'permissions.*' => 'string|exists:permissions,name',
    ];

    if ($request->filled('password')) {
        $rules['password'] = ['required', 'confirmed', Rules\Password::defaults()];
    }

    $validated = $request->validate($rules);

    $user->name  = $validated['name'];
    $user->email = $validated['email'];

    if (!empty($validated['password'])) {
        $user->password = Hash::make($validated['password']);
    }

    $user->save();

    if (isset($validated['roles'])) {
        $user->syncRoles($validated['roles']);
    }

    if (isset($validated['permissions'])) {
        $user->syncPermissions($validated['permissions']);
    }

    return $user;
}

    /**
     * Safely delete a user.
     *
     * @throws \Exception
     */
    public function delete(User $user): void
    {
        if (auth()->id() === $user->id) {
            throw ValidationException::withMessages([
                'error' => 'You cannot delete your own account.',
            ]);
        }

        $user->delete();
    }
}
