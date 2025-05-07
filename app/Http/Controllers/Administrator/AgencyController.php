<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AgencyController extends Controller
{
    public function index()
    {
        if (Auth::user('admin')->can('agencies-read') != true) {
            abort(403, 'Unauthorized action.');
        }
        $data = [
            'breadcrumb_main' => 'All Agencies',
            'breadcrumb' => 'All Agencies',
            'title' => 'All Agencies',
            'agencies' => Agency::latest()->get(),
        ];

        return view('admin.agencies', $data);
    }

    public function edit($id)
    {
        if (Auth::user('admin')->can('agencies-update') != true) {
            abort(403, 'Unauthorized action.');
        }
        $data = [
            'breadcrumb_main' => 'All Agencies',
            'breadcrumb' => 'All Agencies',
            'title' => 'All Agencies',
            'agencies' => Agency::latest()->get(),
            'edit' => Agency::hashidFind($id),
        ];

        return view('admin.agencies', $data);
    }

    public function save(Request $request)
    {
        if (Auth::user('admin')->can('agencies-write') != true || Auth::user('admin')->can('agencies-update') != true) {
            abort(403, 'Unauthorized action.');
        }
        if (!empty($request->id)) {
            $rules = [
                'name' => 'required|unique:agencies,name,' . hashids_decode($request->id) . ',id,deleted_at,NULL',
                'address' => 'required',
            ];
        } else {
            $rules = [
                'name' => 'required|unique:agencies,name,NULL,id,deleted_at,NULL',
                'address' => 'required',
            ];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $agency = new Agency();
        if (!empty($request->id)) {
            $agency = $agency->findOrfail(hashids_decode($request->id));
            $msg = [
                'success' => 'Agency Added Successfully',
                'redirect' => route('admin.agency.all')
            ];
        } else {
            $msg = [
                'success' => 'Agency Update Successfully',
                'redirect' => route('admin.agency.all')
            ];
        }

        $agency->name = $request->name;
        $agency->address = $request->address;
        $agency->status = $request->status;
        $agency->save();

        return response()->json($msg);
    }
}
