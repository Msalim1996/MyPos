<?php

namespace App\Http\Controllers\Api;

use App\Http\Common\Filter\FiltersSoftDelete;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\CourseStoreRequest as StoreRequest;
use App\Http\Requests\CourseUpdateRequest as UpdateRequest;
use App\Http\Resources\CourseOnGoingClassesResource;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @group Coach CRUD
 */
class CourseController extends Controller
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
        $courses = QueryBuilder::for(Course::class)
            ->allowedFilters([
                AllowedFilter::custom('status', new FiltersSoftDelete),
            ])
            ->allowedIncludes(['level_group'])
            ->get();
        return CourseResource::collection($courses);
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
        $course = Course::create([
            'course_id' => $request->course_id,
            'name' => $request->name,
            'course_type' => $request->course_type,
            'day_type' => $request->day_type,
            'coach_type' => $request->coach_type,
            'description' => $request->description,
            'price' => $request->price,
            'discount_amount' => $request->discount_amount,
            'discount_type' => $request->discount_type,
            'number_of_students_from' => $request->number_of_students_from,
            'number_of_students_to' => $request->number_of_students_to,
            'number_of_lessons' => $request->number_of_lessons,
            'level_group_id' => $request->level_group_id,
        ]);

        return new CourseResource($course->load(['levelGroup']));
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
        $query = Course::withTrashed()->where('id', $id);
        $course = QueryBuilder::for($query)
            ->allowedIncludes(['level_group'])
            ->firstOrFail();
        return new CourseResource($course);
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
    public function update(UpdateRequest $request, Course $course)
    {
        $course->update($request->only([
            'course_id',
            'name',
            'course_type',
            'day_type',
            'coach_type',
            'description',
            'price',
            'number_of_students_from',
            'number_of_students_to',
            'number_of_lessons',
            'level_group_id',
            'discount_amount',
            'discount_type'
        ]));

        return new CourseResource($course->load(['levelGroup']));
    }

    /**
     * DELETE
     * 
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Course $course)
    {
        $course->delete();

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
        $course = Course::withTrashed()->findOrFail($id);
        if ($course->trashed()) $course->restore();

        return new CourseResource($course->load(['levelGroup']));
    }

    public function getOnGoingClasses(Request $request)
    {
        $courses = QueryBuilder::for(Course::class)
            ->allowedFilters([
                AllowedFilter::custom('status', new FiltersSoftDelete)
            ])
            ->allowedIncludes(['student_classes', 'level_group'])
            ->onGoingClasses()
            ->get();
        return CourseOnGoingClassesResource::collection($courses);
    }
}
