<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\PermissionType;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function index()
    {
        // if (Auth::user('admin')->can('role-read') != TRUE) {
        //     abort(403, 'Unauthorized action.');
        // }
        if (Auth::user('admin')->role_name != 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $data = [
            'breadcrumb_main' => 'Role Management',
            'breadcrumb' => 'Role',
            'title' => 'All Roles',
            'data' => Role::latest()->get(),
        ];

        return view('admin.role.index')->with($data);
    }

    public function add()
    {
        // if (Auth::user('admin')->can('role-write') != TRUE) {
        //     abort(403, 'Unauthorized action.');
        // }
        if (Auth::user('admin')->role_name != 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $data = [
            'breadcrumb_main' => 'Role Management',
            'breadcrumb' => 'Add Role',
            'title' => 'Add Role',
            'permission_types' => PermissionType::with('permissions')->get()
        ];

        return view('admin.role.add')->with($data);
    }

    public function save(Request $request)
    {
        if (isset($request->id) && !empty($request->id)) {
            if (Auth::user('admin')->role_name != 'super_admin') {
                abort(403, 'Unauthorized action.');
            }
        } else {
            if (Auth::user('admin')->role_name != 'super_admin') {
                abort(403, 'Unauthorized action.');
            }
        }

        $rules = [
            'name' => ['required'],
            'permission' => ['required'],
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }
        $role = new Role();
        $permissions = [];
        if ($request->role_id) {
            $role = $role::find($request->role_id);
            $msg = [
                'success' => 'Role has been updated successfully',
                'redirect' => route('admin.role.all'),
            ];

            foreach ($request->permission as $permission) {
                $permissions[] = ['name' => $permission];
            }
            $role->name = $request->name;
            $role->user_permissions = $permissions;
            $role->save();

            $affected = DB::table('admins')
                ->where('role_id', $role->id)
                ->update(['user_permissions' => $permissions,'role_name' => $role->name]);
        } else {
            $msg = [
                'success' => 'Role has been added successfully',
                'redirect' => route('admin.role.all'),
            ];

            foreach ($request->permission as $permission) {
                $permissions[] = ['name' => $permission];
            }
            $role->name = $request->name;
            $role->user_permissions = $permissions;
            $role->save();

            $affected = DB::table('admins')
                ->where('role_id', $role->id)
                ->update(['user_permissions' => $permissions,'role_name' => $role->name]);
        }


        return response()->json($msg);
    }

    public function edit($id)
    {
        // if (Auth::user('admin')->can('role-update') != TRUE) {
        //     abort(403, 'Unauthorized action.');
        // }
        if (Auth::user('admin')->role_name != 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $editRole = Role::hashidFind($id);
        $data = [
            'breadcrumb_main' => 'Role Management',
            'breadcrumb' => 'Edit Role',
            'title' => 'Edit Role',
            'edit' => $editRole,
            'permission_types' => PermissionType::with('permissions')->get(),
            'user_permissions' => $editRole->user_permissions,
        ];

        return view('admin.role.add')->with($data);
    }

    public function delete($id)
    {
        // if (Auth::user('admin')->can('role-delete') != TRUE) {
        //     abort(403, 'Unauthorized action.');
        // }
        if (Auth::user('admin')->role_name != 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $role = Role::Find($id)->delete();
        return response()->json([
            'success' => 'Role deleted successfully',
            'remove_tr' => true
        ]);
    }
}
