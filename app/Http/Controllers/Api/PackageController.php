<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Traits\ApiResponseTrait;
use App\Traits\PackageTrait;
use Illuminate\Http\Request;

/**
 * @group Wallet
 *
 * @subgroup Package
 */
class PackageController extends Controller
{
    use ApiResponseTrait, PackageTrait;

    public function index()
    {
        $packages = $this->indexPackage();

        return $this->sendSuccessResponse('Packages fetched successfully', $packages);
    }

    public function store(Request $request)
    {
        $this->storePackage($request);

        $user_id = auth('api')->id();
        ActivityLogHelper::save_activity($user_id, 'Added a new package.', 'PackageController', 'app');

        return $this->sendSuccessResponse('Package added successfully');
    }

    public function show(Request $request)
    {
        $package = Package::find($request->id);

        if (!$package) {
            return $this->sendErrorResponse('Package not found', [], 404);
        }

        $package->formatted_price = get_price($package->price);

        return $this->sendSuccessResponse('Package fetched successfully', $package);
    }

    public function add_paynow_transaction_id()
    {
        $transactionId = strtotime(now());
        session(['paynow_transaction_id' => $transactionId]);

        $user_id = auth('api')->id();
        ActivityLogHelper::save_activity($user_id, 'Generated and added PayNow transaction ID.', 'PackageController', 'app');

        return $this->sendSuccessResponse('Package transaction id added successfully', $transactionId);
    }

    public function buy(Request $request)
    {
        $user_id = auth('api')->id();
        ActivityLogHelper::save_activity($user_id, 'Attempted to buy a package.', 'PackageController', 'app');

        return $this->buyPackage($request);
    }
}
