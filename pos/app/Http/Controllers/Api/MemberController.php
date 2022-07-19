<?php

namespace App\Http\Controllers\Api;

use App\Http\Common\Filter\FiltersSoftDelete;
use Illuminate\Http\Request;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use App\Http\Controllers\Controller;

use App\Http\Requests\MemberStoreRequest as StoreRequest;
use App\Http\Requests\MemberUpdateRequest as UpdateRequest;
use App\Http\Requests\TemporaryStudentStoreRequest as TemporaryStoreRequest;
use App\Utils\GetAutoIncrementMember;
use Carbon\Carbon;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @group Member CRUD
 */
class MemberController extends Controller
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
        $members = QueryBuilder::for(Member::class)
            ->allowedFilters([
                AllowedFilter::custom('status', new FiltersSoftDelete),
                'member_id'
            ])
            ->allowedIncludes(['student_enrollments','student_enrollments.student_class','student_enrollments.student_class.class_schedules'])
            ->get();
        return MemberResource::collection($members);
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
        $member = Member::create([
            'member_id' => $request->member_id,
            'email' => $request->email,
            'name' => $request->name,
            'birthdate' => $request->birthdate,
            'gender' => $request->gender,
            'start_date' => $request->start_date,
            'expiration_date' => $request->expiration_date,
            'address' => $request->address,
            'phone' => $request->phone,
            'remark' => $request->remark,
        ]);

        // if image is provided, also upload image
        if ($request->image && $request->input('image') != "") {
            $member->addMediaFromBase64($request->image, ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'])
                ->usingFileName(Carbon::now()->format('Y-m-d_H-i') . '.tmp')
                ->toMediaCollection(Member::$mediaCollectionPath);
        }

        return new MemberResource($member);
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
        $query = Member::withTrashed()->where('id', $id);
        $member = QueryBuilder::for($query)
        ->allowedIncludes(['student_enrollments','student_enrollments.student_class','student_enrollments.student_class.course','student_enrollments.student_class.coach','student_enrollments.student_class.level','student_enrollments.student_class.class_schedules'])
        ->firstOrFail();
        return new MemberResource($member);
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
    public function update(UpdateRequest $request, Member $member)
    {
        $member->update($request->only([
            'member_id',
            'email',
            'name',
            'birthdate',
            'gender',
            'start_date',
            'expiration_date',
            'address',
            'phone',
            'remark',
        ]));

        // if image is provided, also upload image
        if ($request->image) {
            $member->clearMediaCollection(Member::$mediaCollectionPath);

            if ($request->input('image') != "") {
                $member->addMediaFromBase64($request->image, ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'])
                    ->usingFileName(Carbon::now()->format('Y-m-d_H-i') . '.tmp')
                    ->toMediaCollection(Member::$mediaCollectionPath);
            }
        }

        //if image is not provided/has been removed
        if ($request->image == null) {
            $member->clearMediaCollection(Member::$mediaCollectionPath);
        }

        return new MemberResource($member);
    }

    /**
     * DELETE
     * 
     * @authenticated
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Member $member)
    {
        $member->delete();

        return response()->json(null, 204);
    }

    /**
     * GET member by member id (barcode)
     * 
     * @authenticated
     * 
     * @queryParam member_id int
     * @return \Illuminate\Http\Response
     */
    public function getByMemberId($memberId)
    {
        $member = Member::where('member_id', '=', $memberId)->get()->first();
        if ($member == null) return response()->json(['message' => 'Member dengan id ' . $memberId . ' tidak ditemukan'], 422);
        if (Carbon::now() > $member->expiration_date) return response()->json(['message' => 'Member dengan id ' . $memberId . ' sudah expired. Silahkan ke Akademi.'], 422);
        return new MemberResource($member);
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
        $member = Member::withTrashed()->findOrFail($id);
        if ($member->trashed()) $member->restore();

        return new MemberResource($member);
    }

    public function temporaryStudent(TemporaryStoreRequest $request) 
    {
        $member = Member::create([
            'member_id' => GetAutoIncrementMember::getNextNumber('TMPS'),
            'email' => $request->email,
            'name' => $request->name,
            'birthdate' => $request->birthdate,
            'gender' => $request->gender,
            'start_date' => $request->start_date,
            'expiration_date' => $request->expiration_date,
            'address' => $request->address,
            'phone' => $request->phone,
            'remark' => $request->remark,
        ]);
        

        // if image is provided, also upload image
        if ($request->image && $request->input('image') != "") {
            $member->addMediaFromBase64($request->image, ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'])
                ->usingFileName(Carbon::now()->format('Y-m-d_H-i') . '.tmp')
                ->toMediaCollection(Member::$mediaCollectionPath);
        }

        return new MemberResource($member);
    }
}
