<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct(){
       // $this->middleware('permsission:view_users')->only(['index','show']);
        //$this->middleware('permission:create_users')->only(['create', 'store']);
        //$this->middleware('permission:edit_users')->only(['edit', 'update']);
       // $this->middleware('permission:delete_users')->only(['destroy']);
    }


    public function index(Request $request){
        $query = User::query('roles');


    if ($request->filled('role')) {
        $query->whereHas('roles', function($q) use ($request) {
            $q->where('name', $request->role);
        });
    }
        $users = User::with('roles')->latest()->paginate(10);
        $roles = Role::all();


        if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhereHas('roles', function($q2) use ($search) {
                  $q2->where('name', 'like', "%{$search}%");
              });
        });
    }

    $users = $query->latest()->get();
        return view('users.index', compact('users','roles'));
    }

    public function create(){
        $roles = Role::all();
        return view('users.create',compact('roles'));
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
        ]);
        $user = User::Create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),

        ]);
        $user->assignRole($request->role);
        return redirect()->route('users.index')->with('success', 'User Created Successfully');

    }

    public function edit(User $user){
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));

    }

    public function update(Request $request, User $user){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' .$user->id,
            'role' => 'required|exists:roles,name',
            'password' => 'nullable|string|min:8|confirmed',

        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->filled('password') ? Hash::make($request->password) : $user->password,
        ]);
        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')->with('success', 'User Updated Successfully');
    }

    public function destroy(User $user){
        $user ->delete();
        return redirect()->route('users.index')->with('sucess', 'User Deeleted Sucessfully');
    }

}
