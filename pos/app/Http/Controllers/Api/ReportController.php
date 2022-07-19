<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Resources\ReportResource;
use App\Models\Report;
use App\Http\Controllers\Controller;

use App\Http\Requests\ReportRequest as StoreRequest;
use App\Http\Requests\ReportRequest as UpdateRequest;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ReportResource::collection(Report::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $report = Report::create([
            'title' => $request->title,
            'path' => $request->path,
            'category' => $request->category,
            'permissions' => $request->permissions,
        ]);

        return new ReportResource($report);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Report $report)
    {
        return new ReportResource($report);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Report $report)
    {
        $report->update($request->only(['title', 'path', 'category', 'permissions']));

        return new ReportResource($report);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Report $report)
    {
        $report->delete();

        return response()->json(null, 204);
    }
}
