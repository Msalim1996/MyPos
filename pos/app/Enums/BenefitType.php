<?php

namespace App\Enums;

use Spatie\Enum\Enum;


/**
 * @method static self freeItem()
 * @method static self itemDiscount()
 * @method static self discount()
 */
class BenefitType extends Enum
{
    const MAP_INDEX = [
        'freeItem' => 1,
        'itemDiscount' => 2,
        'discount' => 3,
    ];

    const MAP_VALUE = [
        'freeItem' => 'Free item',
        'itemDiscount' => 'Item and discount',
        'discount' => 'Discount',
    ];
}
