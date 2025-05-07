<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\PermissionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PermissionTypeController extends Controller
{
    public function permissionType()
    {

        if (Auth::user('admin')->role_name != 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $data = [
            'breadcrumb_main' => 'Role Management',
            'breadcrumb' => 'Permission Types',
            'title' => 'Permission Types',
            'data' => PermissionType::latest()->get(),
        ];
        return view('admin.premission_type.index')->with($data);
    }
    public function edit($id)
    {

        if (Auth::user('admin')->role_name != 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $data = [
            'breadcrumb_main' => 'Role Management',
            'breadcrumb' => 'Permission Types',
            'title' => 'Edit Permission Type',
            'edit' => PermissionType::hashidFind($id),
            'data' => PermissionType::latest()->get(),
        ];
        return view('admin.premission_type.index')->with($data);
    }
    public function delete($id)
    {

        if (Auth::user('admin')->role_name != 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $delete = PermissionType::find($id);
        $delete->delete();

        return response()->json([
            'success' => 'Record Deleted Successfully',
            'redirect' => route('admin.permission_type.permission_type'),
        ]);
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
            'permission' => 'required',
            // 'description'  =>  'required',
        ];

        $validator = validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $permission_type = new PermissionType;
        if (isset($request->id) && !empty($request->id)) {
            $permission_type = $permission_type::hashidFind($request->id);

            $msg = [
                'success' => 'Permission Type Updated Successfully',
                'redirect' => route('admin.permission_type.permission_type'),
            ];
        } else {
            $msg = [
                'success' => 'Permission Type Added Successfully',
                'redirect' => route('admin.permission_type.permission_type'),
            ];
        }
        $permission_type->permission_type = $request->permission;
        $permission_type->description = $request->description;
        $permission_type->save();
        return response()->json($msg);
    }
}
