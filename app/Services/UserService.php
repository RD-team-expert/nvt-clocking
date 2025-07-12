<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules;
use Illuminate\Http\Request;

class UserService
{
    /**
     * Validate & create a user.
     */
    public function create(Request $request): User
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|lowercase|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        return User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
    }

    /**
     * Validate & update an existing user.
     */
    public function update(Request $request, User $user): User
    {
        $rules = [
            'name'  => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email,' . $user->id,
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
