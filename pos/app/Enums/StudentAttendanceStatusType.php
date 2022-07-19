<?php

namespace App\Enums;

use Spatie\Enum\Enum;


/**
 * @method static self notStarted()
 * @method static self attended()
 * @method static self absent()
 * @method static self cancelled()
 */
class StudentAttendanceStatusType extends Enum
{
    const MAP_INDEX = [
        'notStarted' => 1,
        'attended' => 2,
        'absent' => 3,
        'cancelled' => 4
    ];

    const MAP_VALUE = [
        'notStarted' => 'Not started',
        'attended' => 'Attended',
        'absent' => 'Absent',
        'cancelled' => 'Cancelled'
    ];
}
