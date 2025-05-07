<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\PermissionType;
use Illuminate\Http\Request;
use App\Services\Slug;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    public function index()
    {

        if (Auth::user('admin')->role_name != 'super_admin') {
            abort(403, 'Unauthorized action.');
        }
        $data = [
            'breadcrumb_main' => 'Role Management',
            'breadcrumb' => 'Permission',
            'title' => 'Permissions',
            'permission_type' => PermissionType::get(),
            'data' => Permission::with(['permission_type'])->latest()->get(),
        ];
        return view('admin.permission.index')->with($data);
    }
    public function edit($id)
    {

        if (Auth::user('admin')->role_name != 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $data = [
            'breadcrumb_main' => 'Role Management',
            'breadcrumb' => 'Permission',
            'title' => 'Edit Permission',
            'edit' => Permission::hashidFind($id),
            'permission_type' => PermissionType::get(),
            'data' => Permission::with(['permission_type'])->latest()->get(),
        ];
        return view('admin.permission.index')->with($data);
    }
    public function delete($id)
    {

        if (Auth::user('admin')->role_name != 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $delete = Permission::find($id);
        $delete->delete();
        return response()->json([
            'success' => 'Record Delete Successfully',
            'redirect' => route('admin.permission.permission'),
        ]);
    }

    
    public function save(Request $request, Slug $slug)
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
            'permission_type' => 'required',
            'name' => 'required',
        ];
        $validator = validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        if (isset($request->id) && !empty($request->id)) {
            $permission = Permission::hashidFind($request->id);
            $permission->slug = $slug->createSlug('permissions', $request->name, $permission->id);
            $msg = [
                'success' => 'Permission updated successfully',
                'redirect' => route('admin.permission.permission'),
            ];
        } else {
            $permission = new Permission();
            $permission->slug = $slug->createSlug('permissions', $request->name);
            $msg = [
                'success' => 'Permission added successfully',
                'redirect' => route('admin.permission.permission'),
            ];
        }

        $permission->permission_type_id = $request->permission_type;
        $permission->name = $request->name;
        $permission->save();
        return response()->json($msg);
    }
}
