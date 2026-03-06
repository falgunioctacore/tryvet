<?php

namespace App\Http\Controllers\spatie;

use App\Http\Controllers\Controller;
use App\Http\Controllers\response\ResponseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends ResponseController
{
    public function __construct(){

        
    }
    public function createRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $role = Role::create(['name' => $request->name]);

        return response()->json($role, 201);
    }

    // Create a permission
    public function createPermission(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $permission = Permission::create(['name' => $request->name]);

        return response()->json($permission, 201);
    }

    // Assign permission to a role
    public function assignPermissionToRole(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission_id' => 'required|exists:permissions,id',
        ]);

        $role = Role::find($request->role_id);
        $permission = Permission::find($request->permission_id);

        $role->givePermissionTo($permission);

        return response()->json(['message' => 'Permission assigned to role successfully']);
    }

    // Assign role to a user
    public function assignRoleToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::find($request->user_id);
        $role = Role::find($request->role_id);

        $user->assignRole($role);

        return response()->json([
            'status'=>0,
            'message' => 'Role assigned to user successfully']);
    }
}
