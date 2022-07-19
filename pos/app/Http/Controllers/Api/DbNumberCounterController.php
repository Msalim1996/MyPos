<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\DbNumberCounterRequest as StoreRequest;
use App\Http\Requests\DbNumberCounterRequest as UpdateRequest;
use App\Http\Resources\DbNumberCounterResource;
use App\Http\Resources\StockResource;
use App\Models\DbNumberCounter;
use App\Models\Stock;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @group Stock CRUD
 */
class DbNumberCounterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * GET all
     * 
     * @authenticated
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return DbNumberCounterResource::collection(DbNumberCounter::all());
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
        $dbNumberCounter = DbNumberCounter::create([
            'type' => $request->type,
            'year' => $request->year,
            'month' => $request->month,
            'day' => $request->day,
            'number' => $request->number,
        ]);

        return new DbNumberCounterResource($dbNumberCounter);
    }

    /**
     * GET
     * 
     * @authenticated
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(DbNumberCounter $dbNumberCounter)
    {
        return new DbNumberCounterResource($dbNumberCounter);
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
    public function update(UpdateRequest $request, DbNumberCounter $dbNumberCounter)
    {
        $dbNumberCounter->update($request->only(['type', 'year', 'month', 'day', 'number']));

        return new DbNumberCounterResource($dbNumberCounter);
    }

    /**
     * DELETE
     * 
     * @authenticated
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DbNumberCounter $dbNumberCounter)
    {
        $dbNumberCounter->delete();

        return response()->json(null, 204);
    }
}
