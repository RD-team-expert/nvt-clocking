<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserManagementController extends Controller
{
    public function __construct(private UserService $userService) {}

    /** Display the management page */
    public function index(): Response
    {
        return Inertia::render('settings/UserManagement', [
            'users' => User::all(),
        ]);
    }

    /** Store a new user */
    public function store(Request $request): RedirectResponse
    {
        $this->userService->create($request);
        return back();
    }

    /** Update an existing user */
    public function update(Request $request, string $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $this->userService->update($request, $user);
        return back();
    }

    /** Delete a user */
    public function destroy(string $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        try {
            $this->userService->delete($user);
            return back();
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }
}
