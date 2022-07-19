<?php

namespace App\Http\Controllers\Api;

use App\Events\SkateRental\SkateRentalCreateEvent;
use App\Events\SkateRental\SkateRentalDeleteEvent;
use Illuminate\Http\Request;
use App\Http\Resources\BarcodeResource;
use App\Models\Barcode;
use App\Http\Controllers\Controller;
use App\Http\Resources\SkateResource;
use App\Models\Skate;
use App\Models\SkateTransaction;
use App\Events\SkateRental\SkateRentalUpdateEvent;
use App\Events\SkateTransaction\SkateTransactionUpdateEvent;
use App\Models\BarcodeType;
use App\Models\GateTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Skate CRUD and functionality
 *
 * @group Skate CRUD & functionality
 */
class SkateController extends Controller
{
    /**
     * Skate GET all
     *
     * @authenticated
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return SkateResource::collection(Skate::all());
    }

    /**
     * Skate POST
     *
     * @authenticated
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $skate = Skate::create([
            'size' => $request->input('size'),
            'stock' => $request->input('stock'),
            'rent' => $request->input('rent', 0),
            'description' => $request->input('description', ''),
        ]);

        // trigger event
        event(new SkateRentalCreateEvent(array($skate)));

        return new SkateResource($skate);
    }

    /**
     * Skate GET
     *
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Skate $skate)
    {
        return new SkateResource($skate);
    }

    /**
     * Skate PUT
     *
     * @authenticated
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Skate $skate)
    {
        $skate->update($request->only(['size', 'stock', 'rent', 'description']));

        // trigger event
        event(new SkateRentalUpdateEvent(array($skate)));

        return new SkateResource($skate);
    }

    /**
     * Skate DELETE
     *
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Skate $skate)
    {
        $skate->delete();

        // trigger event
        event(new SkateRentalDeleteEvent(array($skate)));

        return response()->json(null, 204);
    }

    /**
     * Get skate by size
     *
     * @authenticated
     *
     * @bodyParam size string required
     * @return \Illuminate\Http\Response
     */
    public function getBySize(Request $request)
    {
        return Skate::where('size', '=', $request->size)->get()->first();
    }

    /**
     * Ticket > is allowed to functionality
     *
     * allow to rent/exchange/return if :
     * 1. Barcode is registered
     * 2. Check if barcode type can rent skate
     * 3. Skate transaction exist (user has pass gate or allowed from cashier)
     * 4. after that, check skate transaction status for each mode
     *
     * @authenticated
     *
     * @queryParam mode string available mode "RENT", "EXCHANGE", "RETURN"
     * @bodyParam barcode_id string
     */
    public function isAllowedTo(Request $request, string $mode)
    {
        if (!((strcasecmp($mode, 'RENT') == 0) or (strcasecmp($mode, 'EXCHANGE') == 0) or (strcasecmp($mode, 'RETURN') == 0))) {
            return response(array('result' => false, 'message' => 'Mode salah, silahkan hubungi developer'), 200);
        }

        // 1. Check barcode registration
        $isBarcodeRegistered = Barcode::where('barcode_id', '=', $request->barcode_id)->exists();
        if (!$isBarcodeRegistered) {
            return response(array('result' => false, 'message' => 'Tiket ' . $request->barcode_id . ' belum di aktivasi'), 200);
        }

        // 2. Check if barcode type can rent skate
        $barcodeSplitArr = preg_split('/(?<=[\sa-zA-Z])(?=[0-9])/', $request->barcode_id);
        if (count($barcodeSplitArr) != 2 && preg_match('/[^A-Za-z]/', $barcodeSplitArr[0])) {
            return response()->json(['result' => false, 'message' => 'Format tiket salah'], 200);
        }
        $prefix = $barcodeSplitArr[0];
        $barcodeType = BarcodeType::find($prefix);

        if ($barcodeType == null) {
            return response()->json(['result' => false, 'message' => 'Tipe tiket ' . $prefix . ' tidak ditemukan'], 200);
        }
        if (!$barcodeType->is_allowed_to_rent_shoe) {
            return response()->json(['result' => false, 'message' => 'Tipe tiket ' . $prefix . ' tidak diperbolehkan untuk meminjam skate'], 200);
        }

        // 3. Check if skate transaction exist
        $skateTransaction = SkateTransaction::where('barcode_id', '=', $request->barcode_id)->orderBy('created_at', 'desc')->get()->first();
        if ($skateTransaction == null) {
            return response()->json(['result' => false, 'message' => 'Tiket ' . $request->barcode_id . ' belum check-in gate'], 200);
        }

        // 4. Check status
        $skateTransactionRentStatus = $this->_checkSkateTransactionRentalStatus($skateTransaction);
        switch ($mode) {
            // 4.a case for mode RENT
            case "RENT":
                if ($skateTransactionRentStatus == 'CLOSE') {
                    return response()->json(['result' => false, 'message' => 'Skater dengan tiket ' . $request->barcode_id . ' sudah keluar.'], 200);
                }
                if ($skateTransactionRentStatus == 'RENT') {
                    return response()->json(['result' => false, 'message' => 'Tiket ' . $request->barcode_id . ' sudah meminjam skate. Hanya diperbolehkan untuk mengganti atau mengembalikan skate'], 200);
                }
        
                // Let user rent skate
                return response()->json(['result' => true, 'message' => ''], 200);
                break;
            // 4.b & 4.c case for mode EXCHANGE & RETURN
            case "EXCHANGE":
            case "RETURN":
                if ($skateTransactionRentStatus == 'OPEN') {
                    return response()->json(['result' => false, 'message' => 'Tiket ' . $request->barcode_id . ' belum meminjam skate.'], 200);
                }
                if ($skateTransactionRentStatus == 'CLOSE') {
                    // TODO: Untuk yang palembang, sementara dapat mengembalikan sepatu meskipun setelah keluar dari gate
                    // return response()->json(['result' => false, 'message' => 'Tiket ' . $request->barcode_id . ' sudah mengembalikan sepatu atau sudah check-out gate'], 200);
                    return response()->json(['result' => true, 'message' => ''], 200);
                }

                // Let user exchange or return skate
                // special request from the front end guy, to return data which contain the skate id instead of the size
                $skate = Skate::where('size', '=', $skateTransaction->skate_size)->get()->first();
                return response()->json(['result' => true, 'message' => '', 'data' =>  ($skate == null)? -1 : $skate->id ], 200);
                break;
        }
    }

    /**
     * Given the skate transaction, it will return the status of the skate transction
     *
     * @return string either one of OPEN, CLOSE or RENT
     */
    private function _checkSkateTransactionRentalStatus($skateTransaction)
    {
        // if the user has exit the gate, skate transaction status must be CLOSE
        $skaterBarcodeId = $skateTransaction->barcode_id;
        if (GateTransaction::where('barcode_id', '=', $skaterBarcodeId)->exists()) {
            $gateTransaction = GateTransaction::where('barcode_id', '=', $skaterBarcodeId)
                ->orderBy('created_at', 'desc')->get()->first();
            if (!is_null($gateTransaction->time_out)) {
                return 'CLOSE';
            }
        }

        if (is_null($skateTransaction->rent_start)) {
            // start = null, user has not rent
            return 'OPEN';
        } elseif (is_null($skateTransaction->rent_end)) {
            // start = X, end = null, user has rent, not return yet
            return 'RENT';
        } else {
            // start = X, end = Y, user has return.
            return 'CLOSE';
        }
    }

    /**
     * Rent Skate
     *
     * @authenticated
     *
     * @bodyParam barcode_id string
     * @bodyParam skate_id integer
     */
    public function rentSkate(Request $request)
    {
        $skateTransaction = SkateTransaction::where('barcode_id', '=', $request->barcode_id)->orderBy('created_at', 'desc')->get()->first();
        if ($skateTransaction == null) {
            return response()->json(['message' => 'Tiket ' . $request->barcode_id . ' belum check-in gate'], 422);
        }
        
        // check of requested size
        $skate = Skate::where('id', '=', $request->skate_id)->get()->first();
        if ($skate == null) {
            return response()->json(['message' => 'Ukuran sepatu tidak ditemukan'], 404);
        }
        if ($skate->rent >= $skate->stock) {
            // currently number of rent is equals to number of stock, user unable to rent skate
            return response()->json(['message' => 'Skate sudah habis, silahkan pilih ukuran lain'], 404);
        }

        // update skate transaction
        $skateTransaction->rent_start = Carbon::Now();
        $skateTransaction->skate_size = $skate->size;
        $skateTransaction->save();

        // increase skate number of rent
        $skate->rent += 1;
        $skate->save();

        event(new SkateTransactionUpdateEvent(array($skateTransaction)));
        event(new SkateRentalUpdateEvent(array($skate)));
        return response()->json(null, 200);
    }

    /**
     * Exchange Skate
     *
     * @authenticated
     *
     * @bodyParam barcode_id string
     * @bodyParam skate_id integer
     */
    public function exchangeSkate(Request $request)
    {
        $skateTransaction = SkateTransaction::where('barcode_id', '=', $request->barcode_id)->orderBy('created_at', 'desc')->get()->first();
        if ($skateTransaction == null) {
            return response()->json(['message' => 'Tiket ' . $request->barcode_id . ' belum check-in gate'], 404);
        }
        
        $requestedSkate = Skate::where('id', '=', $request->skate_id)->get()->first();
        if ($requestedSkate == null) {
            return response()->json(['message' => 'Ukuran sepatu tidak ditemukan'], 404);
        }
        if ($requestedSkate->rent >= $requestedSkate->stock) {
            // currently number of rent is equals to number of stock, user unable to rent skate
            return response()->json(['message' => 'Skate sudah habis, silahkan pilih ukuran lain'], 404);
        }

        $oldSkate = Skate::where('size', '=', $skateTransaction->skate_size)->get()->first();
        if ($oldSkate == null) {
            return response()->json(['message' => 'Ukuran sepatu yang sedang dipakai tidak ditemukan, tidak bisa tukar sepatu'], 404);
        }

        // update skate transaction
        $skateTransaction->skate_size = $requestedSkate->size;
        $skateTransaction->save();

        // if the skate is the same, then return 200 and do nothing
        if ($requestedSkate->id == $oldSkate->id) {
            return response()->json(null, 200);
        }

        // decrease previously rented skate size
        $oldSkate->rent -= 1;
        $oldSkate->save();

        // increase skate number of rent
        $requestedSkate->rent += 1;
        $requestedSkate->save();

        event(new SkateTransactionUpdateEvent(array($skateTransaction)));
        event(new SkateRentalUpdateEvent(array($oldSkate, $requestedSkate)));
        return response()->json(null, 200);
    }

    /**
     * Return Skate
     *
     * @authenticated
     *
     * @bodyParam barcode_id string
     */
    public function returnSkate(Request $request)
    {
        $skateTransaction = SkateTransaction::where('barcode_id', '=', $request->barcode_id)->orderBy('created_at', 'desc')->get()->first();
        if ($skateTransaction == null) {
            return response()->json(['message' => 'Tiket ' . $request->barcode_id . ' belum check-in gate'], 200);
        }

        // take skate transaction size
        $skate = Skate::where('size', '=', $skateTransaction->skate_size)->get()->first();
        if ($skate == null) {
            return response()->json(['message' => 'Ukuran sepatu yang sedang dipakai tidak ditemukan, tidak bisa mengembalikan sepatu'], 404);
        }

        // update skate transaction
        $skateTransaction->rent_end = Carbon::Now();
        $skateTransaction->save();

        // decrease skate number of rent
        $skate->rent -= 1;
        $skate->save();

        event(new SkateTransactionUpdateEvent(array($skateTransaction)));
        event(new SkateRentalUpdateEvent(array($skate)));
        return response()->json(null, 200);
    }

    /**
     * Reset all rent value into 0
     * 
     * @authenticated
     */

    public function resetAllSkates() {
        Skate::where('rent', '!=', 0)
            ->update(['rent' => 0]);

        return response()->json(null, 200);
    }
}
