<?php

namespace App\Enums;

use Spatie\Enum\Enum;


/**
 * @method static self item()
 * @method static self ticket()
 * @method static self member()
 */
class PreType extends Enum
{
    const MAP_INDEX = [
        'item' => 1,
        'ticket' => 2,
        'member' => 3,
    ];

    const MAP_VALUE = [
        'item' => 'Item',
        'ticket' => 'Ticket',
        'member' => 'Member',
    ];
}
