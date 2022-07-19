<?php

namespace App\Enums;

use Spatie\Enum\Enum;

/**
 * @method static self stock()
 * @method static self nonStock()
 * @method static self service()
 * @method static self ticket()
 * @method static self skatingAid()
 */
class ItemType extends Enum
{
    const MAP_INDEX = [
        'stock' => 1,
        'nonStock' => 2,
        'service' => 3,
        'ticket' => 4,
        'skatingAid' => 5,
    ];

    const MAP_VALUE = [
        'stock' => 'Stock',
        'nonStock' => 'Non-stock',
        'service' => 'Service',
        'ticket' => 'Ticket',
        'skatingAid' => 'Skating aid'
    ];
}
