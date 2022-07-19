<?php

namespace App\Observers;

use App\Models\StudentClass;
use App\Utils\GetAutoIncrementNumber;

class StudentClassObserver
{
    /**
     * Handle the student class "created" event.
     *
     * @param  \App\StudentClass  $studentClass
     * @return void
     */
    public function created(StudentClass $studentClass)
    {
        $studentClass->class_id = GetAutoIncrementNumber::getNextNumber('C');
        $studentClass->save();
    }

    /**
     * Handle the student class "updated" event.
     *
     * @param  \App\StudentClass  $studentClass
     * @return void
     */
    public function updated(StudentClass $studentClass)
    {
        //
    }

    /**
     * Handle the student class "deleted" event.
     *
     * @param  \App\StudentClass  $studentClass
     * @return void
     */
    public function deleted(StudentClass $studentClass)
    {
        //
    }

    /**
     * Handle the student class "restored" event.
     *
     * @param  \App\StudentClass  $studentClass
     * @return void
     */
    public function restored(StudentClass $studentClass)
    {
        //
    }

    /**
     * Handle the student class "force deleted" event.
     *
     * @param  \App\StudentClass  $studentClass
     * @return void
     */
    public function forceDeleted(StudentClass $studentClass)
    {
        //
    }
}
