<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Resources\SkateTransactionResource;
use App\Models\Barcode;
use App\Http\Controllers\Controller;

use App\Http\Requests\SkateTransactionRequest as StoreRequest;
use App\Http\Requests\SkateTransactionRequest as UpdateRequest;

use App\Models\SkateTransaction;
use App\Events\SkateTransactionEvent;
use App\Events\SkateTransaction\SkateTransactionCreateEvent;
use App\Events\SkateTransaction\SkateTransactionDeleteEvent;
use App\Events\SkateTransaction\SkateTransactionUpdateEvent;
use App\Http\Common\Filter\FiltersDateRange;
use App\Http\Common\Filter\FiltersLimit;
use Carbon\Carbon;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Skate CRUD and functionality
 *
 * @group Skate CRUD & functionality
 */
class SkateTransactionController extends Controller
{
    /**
     * Skate Transaction GET all
     *
     * @authenticated 
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $skateTransactions = QueryBuilder::for(SkateTransaction::class)
            ->allowedFilters([
                AllowedFilter::custom('start-between', new FiltersDateRange),
                AllowedFilter::custom('limit', new FiltersLimit),
            ])
            ->get();
        return SkateTransactionResource::collection($skateTransactions);
    }

    /**
     * Skate Transaction POST
     *
     * @authenticated 
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $skateTransaction = SkateTransaction::create([
            'barcode_id' => $request->barcode_id,
            'rent_start' => $request->rent_start,
            'rent_end' => $request->rent_end,
            'skate_size' => $request->skate_size,
            'username_start' => $request->username_start,
            'username_end' => $request->username_end,
        ]);

        // trigger event
        event(new SkateTransactionCreateEvent(array($skateTransaction)));

        return new SkateTransactionResource($skateTransaction);
    }

    /**
     * Skate Transaction GET
     *
     * @authenticated 
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(SkateTransaction $skateTransaction)
    {
        return new SkateTransactionResource($skateTransaction);
    }

    /**
     * Skate Transaction PUT
     *
     * @authenticated 
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SkateTransaction $skateTransaction)
    {
        $skateTransaction->update($request->only(['barcode_id', 'rent_start', 'rent_end', 'skate_size', 'username_start', 'username_end']));

        // trigger event
        event(new SkateTransactionUpdateEvent(array($skateTransaction)));

        return new SkateTransactionResource($skateTransaction);
    }

    /**
     * Skate Transaction DELETE
     *
     * @authenticated 
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(SkateTransaction $skateTransaction)
    {
        $skateTransaction->delete();

        // trigger event
        event(new SkateTransactionDeleteEvent(array($skateTransaction)));

        return response()->json(null, 204);
    }

     /**
     * Get today skate transaction
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
     * Get skate transaction by the given date
     *
     * @authenticated
     *
     * @queryParam date datetime required
     * @return \Illuminate\Http\Response
     */
    public function getTransactionsByDate(Carbon $date)
    {
        return SkateTransactionResource::collection(SkateTransaction::whereDate('created_at', $date->toDateString())->get());
    }
}
