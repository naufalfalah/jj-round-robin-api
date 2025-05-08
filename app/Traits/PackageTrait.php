<?php

namespace App\Traits;

use App\Http\Requests\PackageRequest;
use App\Models\Ads;
use App\Models\Package;
use App\Models\PackageMenu;
use App\Models\SubAccount;
use App\Models\UserSubAccount;
use App\Models\WalletTopUp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

trait PackageTrait
{
    public function indexPackage()
    {
        $packages = Package::latest()->get()->map(function ($package) {
            $package->formatted_price = get_price($package->price);

            return $package;
        });

        return $packages;
    }

    public function storePackage(PackageRequest $request)
    {
        DB::beginTransaction();

        try {
            if (!$request->id) {
                $subAccount = SubAccount::create([
                    'sub_account_name' => $request->name,
                    'sub_account_url' => $request->url,
                ]);
            } else {
                $subAccount = SubAccount::where('package_id', $request->id)
                    ->first();
            }

            $package = Package::updateOrCreate(
                ['id' => $request->id],
                [
                    'sub_account_id' => $subAccount->id,
                    'name' => $request->name,
                    'description' => $request->description,
                    'price' => $request->price,
                    'url' => $request->url,
                ]
            );

            if ($request->id) {
                PackageMenu::where('package_id', $package->id)
                    ->delete();
            }

            $menus = $validated['menus'];
            foreach ($menus as $menu) {
                PackageMenu::create([
                    'package_id' => $package->id,
                    'menu' => $menu,
                ]);
            }
            // $package->menus()->sync($validated['menus']);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save package: '.$e->getMessage(),
            ], 500);
        }
    }

    public function getPackageById($id)
    {
        $package = Package::where('id', $id)
            ->first();

        return $package;
    }

    public function buyPackage(Request $request)
    {
        $rules = [
            'package_id' => 'required|integer',
            'deposit_slip' => 'required|array',
            'deposit_slip.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $package = Package::find($request->package_id);
        if (!$package) {
            return response()->json([
                'error' => 'Package Not Found',
                'reload' => true,
            ], 404);
        }

        DB::beginTransaction();

        try {
            $clientId = auth('web')->id() ?? auth('api')->id();

            $userSubAccount = new UserSubAccount;
            $userSubAccount->client_id = $clientId;
            $userSubAccount->transaction_id = $request->paynow_transaction_id;
            $userSubAccount->sub_account_id = $package->sub_account_id;
            $userSubAccount->package_id = $package->id;
            $userSubAccount->save();

            $wallet = new WalletTopUp;
            if ($request->hasFile('deposit_slip')) {
                $deposit_slips = [];
                foreach ($request->file('deposit_slip') as $file) {
                    $deposit_slip = uploadSingleFile($file, 'uploads/client/profile_images/', 'png,jpeg,jpg');
                    if (is_array($deposit_slip)) {
                        return response()->json($deposit_slip, 422);
                    }
                    $deposit_slips[] = $deposit_slip;
                }

                $wallet->proof = implode(',', $deposit_slips);
            }

            $wallet->client_id = $clientId;
            $wallet->sub_account_id = $package->sub_account_id;
            $wallet->user_sub_account_id = $userSubAccount->id;
            $wallet->transaction_id = $request->paynow_transaction_id;
            $wallet->ad_id = $request->ad_id;
            $wallet->topup_type = 'manual';
            $wallet->topup_amount = $request->amount;
            $wallet->status = 'pending';
            $wallet->added_by = 'client';
            $wallet->added_by_id = '-';
            $wallet->save();

            $ads_add = new Ads;
            $ads_add->client_id = $clientId;
            $ads_add->sub_account_id = $package->sub_account_id;
            $ads_add->user_sub_account_id = $userSubAccount->id;
            $ads_add->adds_title = $request->title ?? '';
            $ads_add->daily_budget = $request->spend_amount ?? '';
            $ads_add->spend_amount = 0;
            $ads_add->domain_name = $request->domain_name ?? '';
            $ads_add->spend_type = $request->spend_type ?? '';
            $ads_add->status = 'running';
            $ads_add->domain_is = $request->domain_is ?? '';
            $ads_add->hosting_is = $request->hosting_is ?? '';
            $ads_add->hosting_details = $request->hosting_name ?? '';
            $ads_add->save();

            session()->forget('paynow_transaction_id');

            DB::commit();

            return response()->json([
                'success' => 'User Sub Account Added Successfully',
                'reload' => true,
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'error' => 'Error TopUp: '.$e->getMessage(),
            ], 500);
        }
    }

    // TEMP: Add default package
    public function setDefaultPackage($clientId)
    {
        $package = Package::where('name', 'Default Package')->first();

        $userSubAccount = new UserSubAccount;
        $userSubAccount->client_id = $clientId;
        $userSubAccount->sub_account_id = $package->sub_account_id;
        $userSubAccount->package_id = $package->id;
        $userSubAccount->save();

        $wallet = new WalletTopUp;
        $wallet->client_id = $clientId;
        $wallet->sub_account_id = $package->sub_account_id;
        $wallet->user_sub_account_id = $userSubAccount->id;
        $wallet->topup_type = 'manual';
        $wallet->status = 'pending';
        $wallet->added_by = 'client';
        $wallet->added_by_id = '-';
        $wallet->save();

        $ads_add = new Ads;
        $ads_add->client_id = $clientId;
        $ads_add->sub_account_id = $package->sub_account_id;
        $ads_add->user_sub_account_id = $userSubAccount->id;
        $ads_add->adds_title = $request->title ?? '';
        $ads_add->daily_budget = $request->spend_amount ?? '';
        $ads_add->spend_amount = 0;
        $ads_add->domain_name = $request->domain_name ?? '';
        $ads_add->spend_type = $request->spend_type ?? '';
        $ads_add->status = 'running';
        $ads_add->domain_is = $request->domain_is ?? '';
        $ads_add->hosting_is = $request->hosting_is ?? '';
        $ads_add->hosting_details = $request->hosting_name ?? '';
        $ads_add->save();
    }
}
