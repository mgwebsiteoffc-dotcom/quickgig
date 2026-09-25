<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = collect([
            ['id'=>1,'name'=>'Super Admin','email'=>'admin@quickcontent.in','role'=>'super_admin','is_active'=>true,'last_login'=>'2 hours ago'],
            ['id'=>2,'name'=>'Ananya Gupta','email'=>'ananya@quickcontent.in','role'=>'admin','is_active'=>true,'last_login'=>'1 day ago'],
            ['id'=>3,'name'=>'Vikram','email'=>'vikram@quickcontent.in','role'=>'manager','is_active'=>true,'last_login'=>'3 hours ago'],
            ['id'=>4,'name'=>'Sneha','email'=>'sneha@quickcontent.in','role'=>'support','is_active'=>true,'last_login'=>'5 hours ago'],
            ['id'=>5,'name'=>'Finance Team','email'=>'finance@quickcontent.in','role'=>'finance','is_active'=>true,'last_login'=>'1 week ago'],
        ]);
        if ($request->user() && !$request->user()->isSuperAdmin()) {
            // non-super cannot see super_admin
            $users = $users->where('role','!=','super_admin');
        }
        $roles = User::ROLES;
        return view('admin.users.index', compact('users','roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|string|max:80',
            'email'=>'required|email|unique:users,email',
            'role'=>'required|in:super_admin,admin,manager,support,finance,business,creator',
            'password'=>'required|min:8',
        ]);
        // User::create([... Hash::make ...])
        return back()->with('toast','User created: '.$request->email.' ('.$request->role.')');
    }

    public function updateRole(Request $request, string $id)
    {
        $request->validate(['role'=>'required|in:super_admin,admin,manager,support,finance,business,creator']);
        if ($request->user()->role !== 'super_admin' && $request->role === 'super_admin') {
            return back()->with('error','Only Super Admin can assign Super Admin.');
        }
        return back()->with('toast',"Role updated for #$id → ".$request->role);
    }

    public function toggleActive(string $id)
    {
        return back()->with('toast',"User #$id status toggled");
    }

    public function destroy(string $id)
    {
        return back()->with('toast',"User #$id deleted");
    }
}
