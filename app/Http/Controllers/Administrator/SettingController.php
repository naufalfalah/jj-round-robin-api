<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\ClientMessageTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\TaxCharge;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\WpMessageTemplate;
use App\Models\TopupSetting;

class SettingController extends Controller
{
    public function tex_vat_charges()
    {
        if (Auth::user('admin')->role_name != 'super_admin') {
            abort(403, 'Unauthorized action.');
        }
        $data = [
            'breadcrumb_main' => 'Settings',
            'breadcrumb' => 'Tax & Vat Charges Setting',
            'title' => 'Tax & Vat Charges Setting',
            'upd_chages' => TaxCharge::latest()->first(),
            'topup' => TopupSetting::latest()->first(),
        ];

        return view('admin.settings.tax_vat_charges.index', $data);
    }

    public function tax_store(Request $request)
    {
        if (Auth::user('admin')->role_name != 'super_admin') {
            abort(403, 'Unauthorized action.');
        }
        $rules = [
            'charges' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        // Start a database transaction
        DB::beginTransaction();

        try {

            $upd_tax_charges = new TaxCharge();

            if (isset($request->charges_upd) && !empty($request->charges_upd)) {
                $upd_tax_charges = $upd_tax_charges->find($request->charges_upd);
            }

            $upd_tax_charges->added_by_id = auth('admin')->id();
            $upd_tax_charges->charges = $request->charges;
            $upd_tax_charges->status = 'active';
            $upd_tax_charges->save();

            $msg = [
                'success' => 'Tax & Vat Charges Saved Successfully',
                'reload' => true
            ];

            // Commit the transaction
            DB::commit();

            return response()->json($msg);
        } catch (\Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollback();

            // Log or handle the exception as needed
            return response()->json(['error' => 'Error Tax & Vat charges: ' . $e->getMessage()], 500);
        }
    }

    public function whatsapp_temp()
    {
        if (Auth::user('admin')->role_name != 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $data = [
            'breadcrumb_main' => 'Settings',
            'breadcrumb' => 'WhatsApp Message Template',
            'title' => 'WhatsApp Message Template',
            'whatsapp_temp' => WpMessageTemplate::latest()->first(),
        ];

        return view('admin.settings.wp_message_temp.index', $data);
    }

    public function wp_message_store(Request $request)
    {
        if (Auth::user('admin')->role_name != 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $rules = [
            'wp_message' => 'required',
            'from_number' => 'nullable|integer'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        
        DB::beginTransaction();
        try {
            WpMessageTemplate::updateOrCreate([
                'id' => $request->id ?? 1,
            ], [
                'added_by_id' => auth('admin')->id(),
                'wp_message' => $request->wp_message,
                'from_number' => $request->from_number,
                'status' => $request->status,
            ]);

            DB::commit();
            return response()->json([
                'success' => 'WhatsApp Message Template Saved Successfully',
                'reload' => true
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'WhatsApp Message Template: ' . $e->getMessage()], 500);
        }
    }
    public function assign_template_to_clients()
    {

        $adminTemplate = WpMessageTemplate::latest()->first();

        if (!$adminTemplate) {
            return response()->json(['error' => 'No admin message template found'], 404);
        }

        $clients = User::all();

        if ($clients->isEmpty()) {
            return response()->json(['error' => 'No clients found'], 404);
        }

        ClientMessageTemplate::truncate();

        $clientTemplates = [];
        foreach ($clients as $client) {
            $clientTemplates[] = [
                'client_id' => $client->id,
                'message_template' => $adminTemplate->wp_message,
                'from_number' => $adminTemplate->from_number,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        $client_message_template = ClientMessageTemplate::insert($clientTemplates);
        if ($client_message_template) {
            return response()->json(['message' => 'Messages assigned to all users successfully.']);
        } else {
            return response()->json(['error' => 'Failed to assign admin message template: '], 500);
        }
    }

    // topup setting code
    public function topup_store(Request $request)
    {
        if (Auth::user('admin')->role_name != 'super_admin') {
            abort(403, 'Unauthorized action.');
        }
        $rules = [
            'min_topup' => 'required',
            'default_topup' => 'required',
            'toggle' => 'required|boolean',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }
        // Start a database transaction
        DB::beginTransaction();
        try {

            $topup_setting = new TopupSetting();

            if (isset($request->topup_upd) && !empty($request->topup_upd)) {
                $topup_setting = $topup_setting->find($request->topup_upd);
            }

            $topup_setting->added_by_id = auth('admin')->id();
            $topup_setting->min_topup = $request->min_topup;
            $topup_setting->default_topup = $request->default_topup;
            $topup_setting->is_fillable = $request->toggle;
            $topup_setting->save();

            $msg = [
                'success' => 'Topup Saved Successfully',
                'reload' => true
            ];

            // Commit the transaction
            DB::commit();

            return response()->json($msg);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(['error' => 'Topup: ' . $e->getMessage()], 500);
        }
    }
}
