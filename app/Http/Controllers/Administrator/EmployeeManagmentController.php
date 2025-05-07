<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EmployeeManagmentController extends Controller
{
    public function add()
    {
        if (Auth::user('admin')->can('user-management-write') != true) {
            abort(403, 'Unauthorized action.');
        }

        $data = [
            'breadcrumb_main' => 'User Management',
            'breadcrumb' => 'Add User',
            'title' => 'Add User',
            'roles' => Role::latest()->get(),
        ];
        return view('admin.user_management.add')->with($data);
    }

    public function save(Request $request)
    {
        if (Auth::user('admin')->can('user-management-write') != true || Auth::user('admin')->can('user-management-update') != true) {
            abort(403, 'Unauthorized action.');
        }

        if (isset($request->id) && !empty($request->id)) {
            if (Auth::user('admin')->can('user-update') != true) {
                abort(403, 'Unauthorized action.');
            }
        } else {
            if (Auth::user('admin')->can('user-write') != true) {
                abort(403, 'Unauthorized action.');
            }
        }


        $rules = [
            'name' => 'required',
            'username' => 'required|unique:admins,username,' . $request->id,
            'email' => 'required|unique:admins,email,' . $request->id,
        ];

        if ($request->user_type == 'normal') {
            $rules['role'] = 'required';
        }

        $request->merge([
            'username' => trim($request->username),
            'email' => trim($request->email),
        ]);

        if (isset($request->id) && empty($request->id)) {
            $rules['password'] = 'required|min:6|max:12';
            $rules['profile_image'] = 'image|mimes:jpg,png,jpeg';

            $rules['username'] = [
                'required',
                Rule::unique('admins')->where(function ($query) {
                    return $query->whereNull('deleted_at');
                })
            ];
            $rules['email'] = [
                'required',
                'email',
                Rule::unique('admins')->where(function ($query) {
                    return $query->whereNull('deleted_at');
                })
            ];
        } else {
            $rules['username'] = [
                'required',
                Rule::unique('admins')->where(function ($query) {
                    return $query->whereNull('deleted_at');
                })->ignore($request->id)
            ];
            $rules['email'] = [
                'required',
                'email',
                Rule::unique('admins')->where(function ($query) {
                    return $query->whereNull('deleted_at');
                })->ignore($request->id)
            ];
        }

        if ($request->hasFile('profile_image')) {
            $rules['profile_image'] = 'image|mimes:jpeg,png,jpg|max:2048|dimensions:width=400,height=400';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $user = new Admin();

        if (isset($request->id) && !empty($request->id)) {
            $user = $user->find($request->id);

            $msg = [
                'success' => 'User Updated Successfully',
                'redirect' => route('admin.user-management.view'),
            ];
        } else {
            $msg = [
                'success' => 'User Add Successfully',
                'redirect' => route('admin.user-management.view'),
            ];
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = $request->username;
        if ($request->hasFile('profile_image')) {
            $profile_img = uploadSingleFile($request->file('profile_image'), 'uploads/profile_images/');
            if (is_array($profile_img)) {
                return response()->json($profile_img);
            }
            if (file_exists($user->image)) {
                @unlink($user->image);
            }
            $user->image = $profile_img;
        }
        if (isset($request->password) && !empty($request->password)) {
            $user->password = bcrypt($request->password);
        }
        if ($request->user_type == 'normal' || $request->user_type == 'team_lead') {
            $role = Role::findOrfail($request->role);
            $user->role_id = $role->id;
            $user->role_name = $role->name;
            $user->user_permissions = $role->user_permissions;
        }
        // else{
        //     $role_id = 2;
        //     $user->role_id = $role_id;
        //     $role = Role::find($role_id);
        //     $user->role_name = $role->name;
        //     $user->user_permissions = $role->user_permissions;
        // }
        $user->user_type = $request->user_type;
        $user->added_by_id = auth('admin')->id();
        $user->save();
        return response()->json($msg);
    }

    public function view(Request $request)
    {

        if (Auth::user('admin')->can('user-management-read') != true) {
            abort(403, 'Unauthorized action.');
        }

        $data = [
            'breadcrumb_main' => 'User Management',
            'breadcrumb' => 'All Users',
            'title' => 'All Users',
            'allUsers' => Admin::where('id', '!=', auth('admin')->id())->where('user_type', '!=', 'admin')->latest()->paginate(10),
        ];

        return view('admin.user_management.allusers')->with($data);
    }

    public function edit($id)
    {

        if (Auth::user('admin')->can('user-management-update') != true) {
            abort(403, 'Unauthorized action.');
        }

        $data = [
            'breadcrumb_main' => 'User Management',
            'breadcrumb' => 'Edit User',
            'title' => 'Edit User',
            'roles' => Role::latest()->get(),
            'edit' => Admin::hashidFind($id),
        ];

        return view('admin.user_management.add')->with($data);
    }

    public function delete($id)
    {

        if (Auth::user('admin')->can('user-management-delete') != true) {
            abort(403, 'Unauthorized action.');
        }

        $user = Admin::find($id);
        if (file_exists($user->image)) {
            @unlink($user->image);
        }
        $user->delete();

        $msg = [
            'success' => 'User Deleted Successfully',
            'reload' => true,
        ];

        return response()->json($msg);
    }

    public function update_password(Request $request)
    {

        if (Auth::user('admin')->can('user-management-update') != true) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'password' => ['required', 'string', 'min:6','max:12', 'confirmed']
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $msg = [
            'success' => 'User password has been updated',
            'reload' => true,
        ];

        $user = Admin::hashidFind($request->user_id);
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json($msg);
    }
}
