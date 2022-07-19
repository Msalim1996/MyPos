<?php

namespace App\Http\Controllers\Api;

use App\Events\SkatingAidTransaction\SkatingAidTransactionCreateEvent;
use App\Events\SkatingAidTransaction\SkatingAidTransactionDeleteEvent;
use App\Events\SkatingAidTransaction\SkatingAidTransactionUpdateEvent;
use Illuminate\Http\Request;
use App\Http\Resources\SkatingAidTransactionResource;
use App\Models\Barcode;
use App\Http\Controllers\Controller;
use App\Http\Requests\SkatingAidTransactionRequest as StoreRequest;
use App\Http\Requests\SkatingAidTransactionRequest as UpdateRequest;
use App\Models\SkatingAidTransaction;
use App\Events\SkatingAidTransactionEvent;
use App\Http\Common\Filter\FiltersDateRange;
use App\Http\Common\Filter\FiltersLimit;
use Carbon\Carbon;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Skating Aid CRUD and functionality
 *
 * @group Skating Aid CRUD and functionality
 */
class SkatingAidTransactionController extends Controller
{
    /**
    * Skating aid transaction GET all
    *
    * @authenticated
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
        $skatingAidTransactions = QueryBuilder::for(SkatingAidTransaction::class)
            ->allowedFilters([
                AllowedFilter::custom('start-between', new FiltersDateRange),
                AllowedFilter::custom('limit', new FiltersLimit),
            ])
            ->get();
        return SkatingAidTransactionResource::collection($skatingAidTransactions);
    }

    /**
    * Skating aid transaction POST
    *
    * @authenticated
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(StoreRequest $request)
    {
        $skatingAidTransaction = SkatingAidTransaction::create([
            'rent_start' => $request->rent_start,
            'rent_end' => $request->rent_end,
            'barcode_id' => $request->barcode_id,
            'skating_aid_id' => $request->skating_aid_id,
            'extended_time' => $request->extended_time,
            'reason' => $request->reason,
        ]);

        // trigger event
        event(new SkatingAidTransactionCreateEvent(array($skatingAidTransaction)));

        return new SkatingAidTransactionResource($skatingAidTransaction);
    }

    /**
    * Skating aid transaction GET
    *
    * @authenticated
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function show(SkatingAidTransaction $skatingAidTransaction)
    {
        return new SkatingAidTransactionResource($skatingAidTransaction);
    }

    /**
    * Skating aid transaction PUT
    *
    * @authenticated
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function update(UpdateRequest $request, $id)
    {
        $skatingAidTransaction = SkatingAidTransaction::where('id','=',$id)->firstOrFail();

        $skatingAidTransaction->update($request->only(['rent_start', 'rent_end', 'sales_order_ref_no', 'skating_aid_id', 'extended_time', 'reason']));

        // trigger event
        event(new SkatingAidTransactionUpdateEvent(array($skatingAidTransaction)));

        return new SkatingAidTransactionResource($skatingAidTransaction);
    }

    /**
    * Skating aid transaction DELETE
    *
    * @authenticated
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function destroy(SkatingAidTransaction $skatingAidTransaction)
    {
        $skatingAidTransaction->delete();

        // trigger event
        event(new SkatingAidTransactionDeleteEvent(array($skatingAidTransaction)));
        
        return response()->json(null, 204);
    }

    /**
    * Get today skating aid transaction
    *
    * @authenticated
    *
    * @return \Illuminate\Http\Response
    */
    public function getTodayTransactions()
    {
        return $this->getTransactionsByDate(Carbon::now());
    }

    /**
     * Get skating aid transaction by the given date
     *
     * @authenticated
     *
     * @queryParam date datetime required
     * @return \Illuminate\Http\Response
     */
    public function getTransactionsByDate(Carbon $date)
    {
        return SkatingAidTransactionResource::collection(SkatingAidTransaction::whereDate('created_at', $date->toDateString())->get());
    }
}
