<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SkateTransaction;
use App\Http\Resources\SkateTransactionResource;
use App\Models\BarcodeType;
use App\Models\GateTransaction;

/**
 * Skate CRUD and functionality
 *
 * @group Skate CRUD & functionality
 */
class SkateTransactionBarcodeController extends Controller
{
    /**
     * Get rent status by skate transaction id.
     *
     * will return either one of 'OPEN', 'RENT' or 'CLOSE'
     * 
     * @authenticated
     *
     * @queryParam skate_transaction_id integer
     * @return \Illuminate\Http\Response 
     */
    public function getRentStatus(Request $request, $skateTransactionId)
    {
        $skateTransaction = SkateTransaction::findOrFail($skateTransactionId);

        // if the user has exit the gate, skate transaction status must be CLOSE
        $skaterBarcodeId = $skateTransaction->barcode_id;
        if (GateTransaction::where('barcode_id', '=', $skaterBarcodeId)->exists()) {
            $gateTransaction = GateTransaction::where('barcode_id', '=', $skaterBarcodeId)
                ->orderBy('created_at', 'desc')->get()->first();
            if (!is_null($gateTransaction->time_out)) {
                return response(array('message' => 'CLOSE'));
            }
        }

        if (is_null($skateTransaction->rent_start)) {
            // start = null, user has not rent
            return response(array('message' => 'OPEN'));
        } else if (is_null($skateTransaction->rent_end)) {
            // start = X, end = null, user has rent, not return yet
            return response(array('message' => 'RENT'));
        } else {
            // start = X, end = Y, user has return.
            return response(array('message' => 'CLOSE'));
        }
    }

    /**
     * Get all skate transaction by the given barcode id.
     * 
     * @authenticated
     *
     * @queryParam barcode_id string
     * @return \Illuminate\Http\Response 
     */
    public function getByBarcodeId(Request $request, $barcodeId)
    {
        return SkateTransactionResource::collection(SkateTransaction::where('barcode_id', '=', $barcodeId)->orderBy('created_at', 'desc')->get());
    }
}
