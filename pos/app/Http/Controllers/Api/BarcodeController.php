<?php

namespace App\Http\Controllers\Api;

use App\Events\GateRinkTicket\OnGoingTicketRinkInCreateEvent;
use App\Events\GateRinkTicket\OnGoingTicketRinkInDeleteEvent;
use App\Events\GateRinkTicket\OnGoingTicketRinkInUpdateEvent;
use App\Events\GateTicket\OnGoingTicketInCreateEvent;
use App\Events\GateTicket\OnGoingTicketInDeleteEvent;
use App\Events\GateTicket\OnGoingTicketInUpdateEvent;
use Illuminate\Http\Request;
use App\Http\Resources\BarcodeResource;
use App\Models\Barcode;
use App\Http\Controllers\Controller;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\BarcodeRequest as StoreRequest;
use App\Http\Requests\BarcodeRequest as UpdateRequest;
use App\Http\Resources\BarcodeTypeResource;
use App\Models\BarcodeType;
use Illuminate\Support\Facades\Validator;
use App\Models\ShoeTransaction;
use App\Events\ShoeTransactionEvent;

/**
 * @group Barcode CRUD & functionality
 */
class BarcodeController extends Controller
{
    /**
     * GET all
     *
     * @authenticated
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return BarcodeResource::collection(Barcode::all());
    }

    /**
     * POST
     *
     * @authenticated
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $barcode = Barcode::create([
            'barcode_id' => $request->barcode_id,
            'sales_order_id' => $request->sales_order_id,
            'active_on' => $request->active_on,
            'session_name' => $request->session_name,
            'session_day' => $request->session_day,
            'session_start_time' => $request->session_start_time,
            'session_end_time' => $request->session_end_time,
        ]);

        // trigger event
        event(new OnGoingTicketInCreateEvent(array($barcode)));
        event(new OnGoingTicketRinkInCreateEvent(array($barcode)));

        return new BarcodeResource($barcode);
    }

    /**
     * GET
     *
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Barcode $barcode)
    {
        return new BarcodeResource($barcode);
    }

    /**
     * PUT
     *
     * @authenticated
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Barcode $barcode)
    {
        $barcode->update($request->only(['barcode_id', 'sales_order_id', 'active_on', 'session_name', 'session_day', 'session_start_time', 'session_end_time']));

        // trigger event
        event(new OnGoingTicketInUpdateEvent(array($barcode)));
        event(new OnGoingTicketRinkInUpdateEvent(array($barcode)));

        return new BarcodeResource($barcode);
    }

    /**
     * DELETE
     *
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Barcode $barcode)
    {
        $barcode->delete();

        // trigger event
        event(new OnGoingTicketInDeleteEvent(array($barcode)));
        event(new OnGoingTicketRinkInDeleteEvent(array($barcode)));

        return response()->json(null, 204);
    }

    /**
     * Check if the barcode given has been activated
     *
     * @authenticated
     *
     * @bodyParam barcode_id string required
     * @return \Illuminate\Http\Response
     */
    public function isActivated(Request $request)
    {
        return response(array('result' => Barcode::where('barcode_id', '=', $request->barcode_id)->exists()));
    }

    /**
     * Universal barcode type checker
     *
     * In order to check ticket activation, use barcode/is-barcode-registered/ api
     *
     * @authenticated
     *
     * @bodyParam barcode_id string required
     * @return void
     */
    public function getType(Request $request)
    {
        $barcodeSplitArr = preg_split('/(?<=[\sa-zA-Z])(?=[0-9])/', $request->barcode_id);

        if (count($barcodeSplitArr) != 2 && preg_match('/[^A-Za-z]/', $barcodeSplitArr[0])) {
            return response()->json(['message' => 'Format barcode salah'], 404);
        }

        $prefix = $barcodeSplitArr[0];

        return new BarcodeTypeResource(BarcodeType::findOrFail($prefix));
    }

    /**
     * Check if barcode type allows user to rent shoe, solely by checking the type.
     *
     * In order to check ticket activation, use barcode/is-barcode-registered/ api
     *
     * @authenticated
     *
     * @bodyParam barcode_id string required
     * @return \Illuminate\Http\Response
     */
    public function isAllowedToRentShoe(Request $request)
    {
        $barcodeSplitArr = preg_split('/(?<=[\sa-zA-Z])(?=[0-9])/', $request->barcode_id);
        if (count($barcodeSplitArr) != 2 && preg_match('/[^A-Za-z]/', $barcodeSplitArr[0])) {
            return response()->json(['message' => 'Format barcode salah'], 404);
        }
        $prefix = $barcodeSplitArr[0];
        $barcodeType = BarcodeType::findOrFail($prefix);

        return response(array('result' => $barcodeType->is_allowed_to_rent_shoe));
    }

    /**
     * Multiple ticket activation
     *
     * @authenticated
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function activateBarcodes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            '*.barcode_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toArray(), 422);
        }

        $barcodesResult = [];
        for ($index = 0; $index < count($request->all()); $index++) {
            $barcode = Barcode::create([
                'barcode_id' => $request->input($index . '.barcode_id'),
                'sales_order_id' => $request->input($index . '.sales_order_id'),
                'active_on' => $request->input($index . '.active_on'),
                'session_name' => $request->input($index . '.session_name'),
                'session_day' => $request->input($index . '.session_day'),
                'session_start_time' => $request->input($index . '.session_start_time'),
                'session_end_time' => $request->input($index . '.session_end_time'),
            ]);
            array_push($barcodesResult, $barcode);
        }

        // trigger event
        event(new OnGoingTicketInCreateEvent($barcodesResult));
        event(new OnGoingTicketRinkInCreateEvent($barcodesResult));

        return response()->json(['data' => $barcodesResult], 201);
    }
}
