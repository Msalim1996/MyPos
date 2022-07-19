<?php

namespace App\Http\Controllers\Api;

use App\Http\Common\Filter\FiltersSoftDelete;
use Illuminate\Http\Request;
use App\Models\Coach;
use App\Http\Resources\CoachResource;
use App\Http\Controllers\Controller;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\CoachStoreRequest as StoreRequest;
use App\Http\Requests\CoachUpdateRequest as UpdateRequest;
use App\Models\CoachCommission;
use Carbon\Carbon;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @group Coach CRUD
 */
class CoachController extends Controller
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
        $coaches = QueryBuilder::for(Coach::class)
            ->allowedFilters([
                AllowedFilter::custom('status', new FiltersSoftDelete),
            ])
            ->allowedIncludes(['coach_commissions'])
            ->get();
        return CoachResource::collection($coaches);
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
        $coach = Coach::create([
            'coach_id' => $request->coach_id,
            'name' => $request->name,
            'gender' => $request->gender,
            'level' => $request->level,
            'type' => $request->type,
            'category' => $request->category,
            'language' => $request->language,
            'address' => $request->address,
            'phone' => $request->phone,
            'remark' => $request->remark,
            'status' => $request->status,
            'private_rate' => $request->private_rate,
            'semi_private_rate' => $request->semi_private_rate,
            'group_rate' => $request->group_rate,
        ]);

        // if image is provided, also upload image
        if ($request->image && $request->input('image') != "") {
            $coach->addMediaFromBase64($request->image, ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'])
                ->usingFileName(Carbon::now()->format('Y-m-d_H-i') . '.tmp')
                ->toMediaCollection(Coach::$mediaCollectionPath);
        }

        if ($request->coach_commissions)
        {
            for ($index = 0; $index < count($request->coach_commissions); $index++) {
                CoachCommission::create([
                    'coach_id' => $coach->id,
                    'commission_class' => $request->input('coach_commissions.' . $index . '.commission_class'),
                    'commission_percentage' => $request->input('coach_commissions.' . $index . '.commission_percentage')
                ]);
            }
        }

        return new CoachResource($coach);
    }

    /**
     * GET
     * 
     * @authenticated
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $query = Coach::withTrashed()->where('id', $id);
        $coach = QueryBuilder::for($query)
            ->allowedIncludes(['coach_commissions'])
            ->firstOrFail();
        return new CoachResource($coach);
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
    public function update(UpdateRequest $request, Coach $coach)
    {
        $coach->update($request->only([
            'coach_id',
            'name',
            'gender',
            'level',
            'type',
            'category',
            'language',
            'address',
            'phone',
            'remark',
            'status',
            'private_rate',
            'semi_private_rate',
            'group_rate'
        ]));

        // if image is provided, also upload image
        if ($request->image) {
            $coach->clearMediaCollection(Coach::$mediaCollectionPath);

            if ($request->input('image') != "") {
                $coach->addMediaFromBase64($request->image, ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'])
                    ->usingFileName(Carbon::now()->format('Y-m-d_H-i') . '.tmp')
                    ->toMediaCollection(Coach::$mediaCollectionPath);
            }
        }

        //if image is not provided/has been removed
        if ($request->image == null) {
            $coach->clearMediaCollection(Coach::$mediaCollectionPath);
        }

        $coachCommissions = [];
        if ($request->coach_commissions)
        {
            for ($index = 0; $index < count($request->coach_commissions); $index++) {
                $coachCommission = CoachCommission::updateOrCreate([
                    'id' => $request->input('coach_commissions.' . $index . '.id')
                ], [
                    'coach_id' => $coach->id,
                    'commission_class' => $request->input('coach_commissions.' . $index . '.commission_class'),
                    'commission_percentage' => $request->input('coach_commissions.' . $index . '.commission_percentage')
                ]);

                array_push($coachCommissions, $coachCommission->id);
            }
        }
        // Remove one by one to make sure observer is called
        $tempCoachCommissions = $coach->coachCommissions()->whereNotIn('id', $coachCommissions)->get();
        foreach ($tempCoachCommissions as $tempCoachCommission) $tempCoachCommission->delete();

        return new CoachResource($coach);
    }

    /**
     * DELETE
     * 
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Coach $coach)
    {
        $coach->delete();

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
        $coach = Coach::withTrashed()->findOrFail($id);
        if ($coach->trashed()) $coach->restore();

        return new CoachResource($coach);
    }
}
