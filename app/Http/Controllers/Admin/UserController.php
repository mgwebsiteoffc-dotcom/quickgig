<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->whereIn('role', User::ADMIN_ROLES)
            ->when(
                $request->user() && ! $request->user()->isSuperAdmin(),
                fn ($q) => $q->where('role', '!=', 'super_admin')
            )
            ->orderBy('name')
            ->get()
            // Portable role ordering (works on MySQL and SQLite alike)
            ->sortBy(fn (User $u) => array_search($u->role, User::ADMIN_ROLES, true))
            ->values()
            ->map(fn (User $u) => [
                'id'         => $u->id,
                'name'       => $u->name,
                'email'      => $u->email,
                'role'       => $u->role,
                'is_active'  => (bool) $u->is_active,
                'last_login' => $u->updated_at?->diffForHumans() ?? '—',
            ]);

        $roles = User::ROLES;

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:80',
            'email'    => 'required|email|max:120|unique:users,email',
            'role'     => 'required|in:'.implode(',', array_keys(User::ROLES)),
            'password' => ['required', Password::min(8)->letters()->numbers()],
        ]);

        if ($data['role'] === 'super_admin' && ! $request->user()->isSuperAdmin()) {
            return back()->with('error', 'Only Super Admin can create a Super Admin.');
        }

        $data['password']  = Hash::make($data['password']);
        $data['is_active'] = true;

        User::create($data);

        return back()->with('toast', 'User created: '.$data['email'].' ('.$data['role'].')');
    }

    public function updateRole(Request $request, string $id)
    {
        $request->validate(['role' => 'required|in:'.implode(',', array_keys(User::ROLES))]);

        $user = User::findOrFail($id);

        if (! $request->user()->isSuperAdmin() && ($request->role === 'super_admin' || $user->isSuperAdmin())) {
            return back()->with('error', 'Only Super Admin can manage Super Admin accounts.');
        }

        if ($user->id === $request->user()->id && $request->role !== $user->role) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $user->update(['role' => $request->role]);

        return back()->with('toast', "Role updated for {$user->name} → {$request->role}");
    }

    public function toggleActive(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You cannot disable your own account.');
        }

        if ($user->isSuperAdmin() && ! $request->user()->isSuperAdmin()) {
            return back()->with('error', 'Only Super Admin can disable a Super Admin.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('toast', "{$user->name} is now ".($user->is_active ? 'active' : 'disabled'));
    }

    public function destroy(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->isSuperAdmin() && User::where('role', 'super_admin')->count() <= 1) {
            return back()->with('error', 'Cannot delete the last Super Admin.');
        }

        $user->delete();

        return back()->with('toast', "User {$user->email} deleted");
    }
}
