<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdsInvoice;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AdsReportController extends Controller
{
    public function index(Request $request)
    {
        $auth_id = auth('web')->id();
        if ($request->ajax()) {
            return DataTables::of(AdsInvoice::query()->where('client_id', $auth_id)->latest())
                ->addIndexColumn()
                ->addColumn('invoice_id', function ($data) {
                    return '00062' . $data->id ;
                })
                ->addColumn('invoice_date', function ($data) {
                    return $data->invoice_date;
                })
                ->addColumn('billing_id', function ($data) {
                    return convertNumberFormat($data->billing_id);
                })
                ->addColumn('customer_id', function ($data) {
                    return convertNumberFormat($data->client->customer_id);
                })
                ->addColumn('total_amount', function ($data) {
                    return get_price($data->total_amount);
                })
                ->addColumn('action', function ($data) {
                    return view('client.report.include.slip', ['data' => $data]);
                })
                ->filter(function ($query) {
                    if (request()->input('search')) {
                        $query->where(function ($search_query) {
                            $search_query->whereLike(['invoice_date','total_amount'], request()->input('search'));
                        });
                    }
                })
                ->orderColumn('DT_RowIndex', function ($q, $o) {
                    $q->orderBy('id', $o);
                })
            ->make(true);
        }
        $data = [
            'breadcrumb' => 'Report',
            'title' => 'Report',
        ];
        return view('client.report.index')->with($data);
    }

    public function slip($id)
    {
        $adsInvoice = AdsInvoice::hashidfind($id);
        if (!$adsInvoice->billing_id) {
            $randomNumber = generateRandomNumberString(12);
            $adsInvoice->billing_id = $randomNumber;
            $adsInvoice->save();
        }

        $data = [
            'breadcrumb' => 'Invoice',
            'title' => 'Invoice',
            'report' => $adsInvoice,
        ];
        return view('client.report.invoice')->with($data);
    }
}
