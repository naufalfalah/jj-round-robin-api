<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\User;

class LeadController extends Controller
{
    public function index()
    {
        $data = [
            'breadcrumb' => 'Client Leads',
            'title' => 'Client Leads',
            'users' => User::all(),
        ];

        return view('admin.lead.index')->with($data);
    }
}
