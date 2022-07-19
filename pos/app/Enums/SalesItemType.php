<?php

namespace App\Enums;

use Spatie\Enum\Enum;


/**
 * @method static self item()
 * @method static self promotion()
 * @method static self studentEnrollment()
 * @method static self skatingAid()
 */
class SalesItemType extends Enum
{
    const MAP_INDEX = [
        'item' => 1,
        'promotion' => 2,
        'studentEnrollment' => 3,
        'skatingAid' => 4,
    ];

    const MAP_VALUE = [
        'item' => 'Item',
        'promotion' => 'Promotion',
        'studentEnrollment' => 'Student enrollment',
        'skatingAid' => 'Skating aid',
    ];
}
