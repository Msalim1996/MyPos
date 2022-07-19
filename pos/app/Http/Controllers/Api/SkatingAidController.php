<?php

namespace App\Http\Controllers\Api;

use App\Events\SkatingAid\SkatingAidCreateEvent;
use App\Events\SkatingAid\SkatingAidUpdateEvent;
use App\Events\SkatingAid\SkatingAidDeleteEvent;
use App\Events\SkatingAidTransaction\SkatingAidTransactionUpdateEvent;
use Illuminate\Http\Request;
use App\Http\Resources\SkatingAidResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\SkatingAidStoreRequest as StoreRequest;
use App\Http\Requests\SkatingAidUpdateRequest as UpdateRequest;
use App\Models\SkatingAid;
use App\Events\SkatingAidTransactionEvent;
use App\Models\SalesOrder;
use App\Models\SkatingAidTransaction;
use Carbon\Carbon;

/**
 * Skating Aid CRUD and functionality
 *
 * @group Skating Aid CRUD and functionality
 */
class SkatingAidController extends Controller
{
    /**
     * Skating aid GET all
     *
     * @authenticated
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return SkatingAidResource::collection(SkatingAid::all());
    }

    /**
     * Skating aid POST
     *
     * @authenticated
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $skatingAid = SkatingAid::create([
            'skating_aid_code' => $request->input('skating_aid_code'),
            'stock' => $request->input('stock'),
            'rent' => $request->input('rent', 0),
            'description' => $request->input('description', ''),
        ]);

        // trigger event
        event(new SkatingAidCreateEvent(array($skatingAid)));

        return new SkatingAidResource($skatingAid);
    }

    /**
     * Skating aid GET
     *
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(SkatingAid $skatingAid)
    {
        return new SkatingAidResource($skatingAid);
    }

    /**
     * Skating aid PUT
     *
     * @authenticated
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, SkatingAid $skatingAid)
    {
        $skatingAid->update($request->only(['skating_aid_code', 'stock', 'rent', 'description']));

        // trigger event
        event(new SkatingAidUpdateEvent(array($skatingAid)));

        return new SkatingAidResource($skatingAid);
    }

    /**
     * Skating aid DELETE
     *
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(SkatingAid $skatingAid)
    {
        $skatingAid->delete();

        // trigger event
        event(new SkatingAidDeleteEvent(array($skatingAid)));

        return response()->json(null, 204);
    }

    /**
     * Restore
     * 
     * @authenticated
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore(Request $request, int $id)
    {
        $skatingAid = SkatingAid::withTrashed()->findOrFail($id);
        if ($skatingAid->trashed()) $skatingAid->restore();

        return new SkatingAidResource($skatingAid);
    }

    /**
     * Rent Skating_aid
     *
     * @authenticated
     *
     * @bodyParam sales_order_ref_no string
     * @bodyParam updated_skating_aid_transactions : [
     *  {
     *      id: integer
     *      skating_aid_code: string
     *  }, 
     *  {
     *      id: integer
     *      skating_aid_code: string
     *  },
     *  .
     *  .
     * ]
     */
    public function rentSkatingAid(Request $request)
    {
        $salesOrderId = SalesOrder::where('order_ref_no', $request->sales_order_ref_no)->firstOrFail()->id;
        $skatingAidTransactions = SkatingAidTransaction::where('sales_order_id', '=', $salesOrderId)->orderBy('created_at', 'desc')->get();

        if ($skatingAidTransactions == null) {
            return response()->json(['message' => 'Sales order ' . $request->barcode_id . ' tidak membeli skating aid'], 422);
        }

        // works like dictionary to count number of requested rent shoe, later will be used as validation
        // whether it matches the stock
        $list = [];

        foreach ($request->updated_skating_aid_transactions as $updatedSkatingAidTransaction) {
            $skatingAidTransaction = SkatingAidTransaction::findOrFail($updatedSkatingAidTransaction['id']);

            // checking the availibility of skating aid code
            $skatingAid = SkatingAid::where('skating_aid_code', $updatedSkatingAidTransaction['skating_aid_code'])->firstOrFail();

            if ($skatingAidTransaction->rent_start != null) {
                return response()->json(['message' => 'Skating aid sudah disewa, silahkan pilih transaksi skating aid lain yang belum disewa'], 404);
            }

            if (!isset($list[$updatedSkatingAidTransaction['skating_aid_code']]))
                $list[$updatedSkatingAidTransaction['skating_aid_code']] = 1;
            else
                $list[$updatedSkatingAidTransaction['skating_aid_code']]++;

            if ($list[$updatedSkatingAidTransaction['skating_aid_code']] + $skatingAid->rent > $skatingAid->stock)
                return response()->json(['message' => 'Skating aid sudah habis, silahkan pilih skating aid lain'], 404);
        }

        // declare array which only for sockets
        $skatingAidTransactionsForEvent = [];
        $skatingAidsForEvent = [];

        foreach ($request->updated_skating_aid_transactions   as $updatedSkatingAidTransaction) {
            $skatingAidTransaction = SkatingAidTransaction::find($updatedSkatingAidTransaction['id']);
            $skatingAid = SkatingAid::where('skating_aid_code', $updatedSkatingAidTransaction['skating_aid_code'])->first();

            // update skate transaction
            $skatingAidTransaction->rent_start = Carbon::Now();
            $skatingAidTransaction->skating_aid_id = $skatingAid->id;
            $skatingAidTransaction->save();

            // increase skate number of rent
            $skatingAid->rent += 1;
            $skatingAid->save();

            array_push($skatingAidTransactionsForEvent, $skatingAidTransaction);
            array_push($skatingAidsForEvent, $skatingAid);
        }

        event(new SkatingAidTransactionUpdateEvent($skatingAidTransactionsForEvent));
        event(new SkatingAidUpdateEvent($skatingAidsForEvent));
        return response()->json(null, 200);
    }

    /**
     * Upgrade Skating Aid
     *
     * @authenticated
     *
     * @bodyParam sales_order_ref_no string
     * @bodyParam updated_skating_aid_transactions : [
     *  {
     *      id: integer
     *      old_skating_aid_transaction_id: integer
     *      new_skating_aid_code: string
     *  }, 
     *  {
     *      id: integer
     *      old_skating_aid_transaction_id: integer
     *      new_skating_aid_code: string
     *  },
     *  .
     *  .
     * ]
     */
    public function upgradeSkatingAid(Request $request)
    {
        $salesOrderId = SalesOrder::where('order_ref_no', $request->sales_order_ref_no)->firstOrFail()->id;
        $skatingAidTransactions = SkatingAidTransaction::where('sales_order_id', '=', $salesOrderId)->orderBy('created_at', 'desc')->get();

        if ($skatingAidTransactions == null) {
            return response()->json(['message' => 'Sales order ' . $request->barcode_id . ' tidak membeli skating aid'], 422);
        }

        // declare array which only for sockets
        $skatingAidTransactionsForEvent = [];
        $skatingAidsForEvent = [];

        // validations
        foreach ($request->updated_skating_aid_transactions as $updatedSkatingAidTransaction) {
            $requestedSkatingAid = SkatingAid::where('skating_aid_code', $updatedSkatingAidTransaction['new_skating_aid_code'])->first();
            if ($requestedSkatingAid == null) {
                return response()->json(['message' => 'Skating aid lama tidak ditemukan'], 404);
            }
            if ($requestedSkatingAid->rent >= $requestedSkatingAid->stock) {
                // currently number of rent is equals to number of stock, user unable to rent skate
                return response()->json(['message' => 'Skating aid sudah habis, silahkan pilih skating aid lain'], 404);
            }
            $oldSkatingAidId = SkatingAidTransaction::findOrFail($updatedSkatingAidTransaction['old_skating_aid_transaction_id'])->skating_aid_id;

            $oldSkatingAid = SkatingAid::findOrFail($oldSkatingAidId);

            // if the skate is the same, then return 200 and do nothing
            if ($requestedSkatingAid->id == $oldSkatingAidId) {
                return response()->json(['message' => 'Skating aid yang dipilih sama dengan skating aid sebelumnya'], 200);
            }
        }

        foreach ($request->updated_skating_aid_transactions as $updatedSkatingAidTransaction) {
            // close old skating aid transaction
            $oldSkatingAid->rent -= 1;
            $oldSkatingAid->save();

            $oldSkatingAidTransaction = SkatingAidTransaction::find($updatedSkatingAidTransaction['old_skating_aid_transaction_id']);
            $oldSkatingAidTransaction->rent_end = Carbon::Now();
            $oldSkatingAidTransaction->save();

            // start new skating aid transaction
            $requestedSkatingAid->rent += 1;
            $requestedSkatingAid->save();

            $requestedSkatingAidTransaction = SkatingAidTransaction::find($updatedSkatingAidTransaction['id']);
            
            $requestedSkatingAidTransaction->upgraded = 1;
            $requestedSkatingAidTransaction->skating_aid_id = $requestedSkatingAid->id;
            $requestedSkatingAidTransaction->rent_start = $oldSkatingAidTransaction->rent_start;
            $requestedSkatingAidTransaction->save();

            array_push($skatingAidTransactionsForEvent, $oldSkatingAidTransaction);
            array_push($skatingAidTransactionsForEvent, $requestedSkatingAidTransaction);
            array_push($skatingAidsForEvent, $oldSkatingAid);
            array_push($skatingAidsForEvent, $requestedSkatingAid);
        }

        event(new SkatingAidTransactionUpdateEvent($skatingAidTransactionsForEvent));
        event(new SkatingAidUpdateEvent($skatingAidsForEvent));
        return response()->json(null, 200);
    }

    /**
     * Return Skating_aid
     *
     * @authenticated
     *
     * @bodyParam sales_order_ref_no string
     * @bodyParam updated_skating_aid_transactions : [
     *  {
     *      id: integer
     *      skating_aid_code: string
     *  }, 
     *  {
     *      id: integer
     *      skating_aid_code: string
     *  },
     *  .
     *  .
     * ]
     */
    public function returnSkatingAid(Request $request)
    {
        $salesOrderId = SalesOrder::where('order_ref_no', $request->sales_order_ref_no)->firstOrFail()->id;
        $skatingAidTransactions = SkatingAidTransaction::where('sales_order_id', '=', $salesOrderId)->orderBy('created_at', 'desc')->get();

        if ($skatingAidTransactions == null) {
            return response()->json(['message' => 'Sales order ' . $request->barcode_id . ' tidak membeli skating aid'], 422);
        }

        // works like dictionary to count number of requested rent shoe, later will be used as validation
        // whether it matches the stock
        $list = [];

        foreach ($request->updated_skating_aid_transactions as $updatedSkatingAidTransaction) {
            $skatingAidTransaction = SkatingAidTransaction::findOrFail($updatedSkatingAidTransaction['id']);

            // checking the availibility of skating aid code
            $skatingAid = SkatingAid::where('skating_aid_code', $updatedSkatingAidTransaction['skating_aid_code'])->firstOrFail();

            if ($skatingAidTransaction->rent_start == null) {
                return response()->json(['message' => 'Skating aid belum disewa, silahkan menyewa skating aid terlebih dahulu'], 404);
            }

            if ($skatingAidTransaction->rent_end != null) {
                return response()->json(['message' => 'Skating aid sudah dikembalikan, silahkan pilih transaksi skating aid lain yang belum dikembalikan'], 404);
            }
        }

        // declare array which only for sockets
        $skatingAidTransactionsForEvent = [];
        $skatingAidsForEvent = [];

        foreach ($request->updated_skating_aid_transactions   as $updatedSkatingAidTransaction) {
            $skatingAidTransaction = SkatingAidTransaction::find($updatedSkatingAidTransaction['id']);
            $skatingAid = SkatingAid::where('skating_aid_code', $updatedSkatingAidTransaction['skating_aid_code'])->first();

            // update skate transaction
            $skatingAidTransaction->rent_end = Carbon::Now();
            $skatingAidTransaction->skating_aid_id = $skatingAid->id;
            $skatingAidTransaction->save();

            // increase skate number of rent
            $skatingAid->rent -= 1;
            $skatingAid->save();

            array_push($skatingAidTransactionsForEvent, $skatingAidTransaction);
            array_push($skatingAidsForEvent, $skatingAid);
        }

        event(new SkatingAidTransactionUpdateEvent($skatingAidTransactionsForEvent));
        event(new SkatingAidUpdateEvent($skatingAidsForEvent));
        return response()->json(null, 200);
    }

    /**
     * Reset all rent value into 0
     * 
     * @authenticated
     */

    public function resetAllSkatingAid() {
        SkatingAid::where('rent', '!=', 0)
            ->update(['rent' => 0]);

        return response()->json(null, 200);
    }
}
