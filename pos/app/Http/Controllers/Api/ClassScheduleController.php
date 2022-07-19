<?php

namespace App\Http\Controllers\Api;

use App\Http\Common\Filter\FiltersClassScheduleSessionDateTime;
use App\Http\Controllers\Controller;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\CoachStoreRequest as StoreRequest;
use App\Http\Requests\CoachUpdateRequest as UpdateRequest;
use App\Http\Resources\ClassScheduleResource;
use App\Models\ClassSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @group Coach CRUD
 */
class ClassScheduleController extends Controller
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
        $classSchedules = QueryBuilder::for(ClassSchedule::class)
            ->allowedFilters([
                AllowedFilter::custom('start-between', new FiltersClassScheduleSessionDateTime),
                AllowedFilter::exact('student_class_id'),
                'status', 'student_class.coach_id'
            ])
            ->allowedIncludes(['student_class', 'student_class.coach', 'student_class.course', 'student_class.level', 'student_class.student_enrollments', 'student_class.student_enrollments.member','student_attendances','student_attendances.member'])
            ->get();
        return ClassScheduleResource::collection($classSchedules);
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
        $query = ClassSchedule::where('id','=',$id);
        $classSchedule = QueryBuilder::for($query)
            ->allowedIncludes(['student_class', 'student_class.coach', 'student_class.course', 'student_class.level', 'student_class.student_enrollments', 'student_class.student_enrollments.member','student_attendances','student_attendances.member'])
            ->firstOrFail();
        return new ClassScheduleResource($classSchedule);
    }
}
