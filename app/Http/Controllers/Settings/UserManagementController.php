<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class UserManagementController extends Controller
{
    public function __construct(private UserService $userService) {}

    /** Display the management page */
    public function index(): Response
    {
        return Inertia::render('settings/UserManagement', [
            'users' => User::all(),
            'roles' => Role::with('permissions')->get(),
            'permissions' => Auth::user()->getAllPermissions(),
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

    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array',
        ]);

        $role = Role::create(['name' => $request->name]);

        $role->syncPermissions($request->permissions);

        return redirect()->route('user-management')->with('success', 'Role created successfully.');
    }

    public function updateRole(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'array',
        ]);

        $role->update(['name' => $request->name]);

        $role->syncPermissions($request->permissions);

        return redirect()->route('user-management')->with('success', 'Role updated successfully.');
    }
    public function destroyRole(Role $role)
    {
        $role->delete();
        return redirect()->route('user-management')->with('success', 'Role deleted.');
    }

}
