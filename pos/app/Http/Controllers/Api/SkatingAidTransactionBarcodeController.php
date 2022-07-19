<?php

namespace App\Http\Controllers\Api;

use App\Http\Common\Filter\FiltersUpgrade;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SkatingAidTransactionResource;
use App\Models\BarcodeType;
use App\Models\GateTransaction;
use App\Models\SalesOrder;
use App\Models\SkatingAidTransaction;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Skate CRUD and functionality
 *
 * @group Skate CRUD & functionality
 */
class SkatingAidTransactionBarcodeController extends Controller
{
    /**
     * Get rent status by skating aid transaction id.
     *
     * will return either one of 'OPEN', 'RENT' or 'CLOSE'
     * 
     * @authenticated
     *
     * @queryParam skate_transaction_id integer
     * @return \Illuminate\Http\Response 
     */
    public function getRentStatus(Request $request, $skatingAidTransactionId)
    {
        $skatingAidTransaction = SkatingAidTransaction::findOrFail($skatingAidTransactionId);
        // if the user has exit the gate, skate transaction status must be CLOSE
        $skaterBarcodeId = $skatingAidTransaction->barcode_id;
        if (GateTransaction::where('barcode_id', '=', $skaterBarcodeId)->exists()) {
            $gateTransaction = GateTransaction::where('barcode_id', '=', $skaterBarcodeId)
                ->orderBy('created_at', 'desc')->get()->first();
            if (!is_null($gateTransaction->time_out)) {
                return response(array('message' => 'CLOSE'));
            }
        }

        if (is_null($skatingAidTransaction->rent_start)) {
            // start = null, user has not rent
            return response(array('message' => 'OPEN'));
        } else if (is_null($skatingAidTransaction->rent_end)) {
            // start = X, end = null, user has rent, not return yet
            return response(array('message' => 'RENT'));
        } else {
            // start = X, end = Y, user has return.
            return response(array('message' => 'CLOSE'));
        }
    }

    /**
     * Get all skating aid transaction by the given barcode id.
     * 
     * @authenticated
     *
     * @queryParam barcode_id string
     * @return \Illuminate\Http\Response 
     */
    public function getBySalesOrderRefNo(Request $request, $salesOrderRefNo)
    {   
        $skatingAidTransactions = QueryBuilder::for(SkatingAidTransaction::class)
                                    ->allowedFilters([
                                        AllowedFilter::custom('upgraded', new FiltersUpgrade),
                                    ])
                                    ->where('sales_order_ref_no', $salesOrderRefNo)
                                    ->orderBy('created_at', 'desc')
                                    ->get();
        return SkatingAidTransactionResource::collection($skatingAidTransactions);
    }
}
