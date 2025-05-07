<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RemainingBalanceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Validator::extend('remaining_balance_check', function ($attribute, $value, $parameters, $validator) {
        //     // Check if the amount is less than or equal to the remaining balance
        //     $remaining_balance = DB::table('transections')
        //             ->select('client_id', DB::raw('COALESCE(SUM(amount_in), 0) - COALESCE(SUM(amount_out), 0) AS remaining_balance'))
        //             ->where('client_id', auth('web')->id())
        //             ->where('deleted_at', null)
        //             ->groupBy('client_id')
        //             ->get();

        //     $remaining_amount = $remaining_balance->isEmpty() ? 0 : $remaining_balance[0]->remaining_balance;

        //     if ($remaining_amount <= 0) {
        //         return false;
        //     }

        //     return $value <= $remaining_amount;
        // });
    }
}
