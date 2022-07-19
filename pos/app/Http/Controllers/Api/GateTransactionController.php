<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\GateTransactionResource;
use App\Models\GSTimeSchedule;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\GateTransactionRequest as StoreRequest;
use App\Http\Requests\GateTransactionRequest as UpdateRequest;
use App\Http\Resources\GateOnGoingTransactionResource;
use App\Models\Barcode;
use App\Models\GateTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Gate access control and transaction CRUD
 *
 * @group Gate (Main) access control & transaction CRUD
 */
class GateTransactionController extends Controller
{
    /**
     * Transaction GET
     *
     * @authenticated
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return GateTransactionResource::collection(GateTransaction::all());
    }

    /**
     * Transaction POST
     *
     * @authenticated
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $gateTransaction = GateTransaction::create([
            'barcode_id' => $request->barcode_id,
            'time_in' => $request->time_in,
            'time_out' => $request->time_out,
        ]);

        return new GateTransactionResource($gateTransaction);
    }

    /**
     * Transaction GET
     *
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(GateTransaction $gateTransaction)
    {
        return new GateTransactionResource($gateTransaction);
    }

    /**
     * Transaction PUT
     *
     * @authenticated
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, GateTransaction $gateTransaction)
    {
        $gateTransaction->update($request->only(['barcode_id', 'time_in', 'time_out']));

        return new GateTransactionResource($gateTransaction);
    }

    /**
     * Transaction DELETE
     *
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(GateTransaction $gateTransaction)
    {
        $gateTransaction->delete();

        return response()->json(null, 204);
    }

    /**
     * Main gate transaction with filter capability
     *
     * @authenticated
     *
     * @bodyParam fromdate datetime required start from, if value is not provided, then query from the begining
     * @bodyParam todate datetime required until date, if value is not provided, then query until the end
     * @return \Illuminate\Http\Response
     */
    public function filter(Request $request)
    {
        $query = GateTransaction::query();

        if ($request->has('fromdate') && $request->input('fromdate') != null) {
            $query->where('created_at', '>=', $request->input('fromdate'));
        }

        if ($request->has('todate') && $request->input('todate') != null) {
            $query->where('created_at', '<=', $request->input('todate'));
        }

        return GateTransactionResource::collection($query->get());
    }

    /**
     * Get list of ticket that has not checked in yet for today
     *
     * will return list of registered ticket which has not pass the gate yet
     *
     * @authenticated
     *
     * @return \Illuminate\Http\Response
     */
    public function todayOnGoingIn(Request $request)
    {
        $data = DB::table('barcodes')->select('id', 'barcode_id')
                ->whereDate('created_at', '=', Carbon::now())
                ->whereNotIn('barcode_id', function ($query) {
                    $query->select('barcode_id')->from('gate_transactions')->whereDate('created_at', '=', Carbon::now());
                })
                ->get();

        return GateOnGoingTransactionResource::collection($data);
    }

    /**
     * Get list of ticket that has checked in and not yet check out for today
     *
     * @authenticated
     *
     * @return \Illuminate\Http\Response
     */
    public function todayOnGoingOut(Request $request)
    {
        $data = DB::table('barcodes')->select('id', 'barcode_id')
                ->whereDate('created_at', '=', Carbon::now())
                ->whereIn('barcode_id', function ($query) {
                    $query->select('barcode_id')->from('gate_transactions')->whereNotNull('time_in')->whereNull('time_out')->whereDate('created_at', '=', Carbon::now());
                })
                ->get();

        return GateOnGoingTransactionResource::collection($data);
    }
}
