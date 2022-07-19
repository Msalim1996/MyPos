<?php

namespace App\Enums;

use Spatie\Enum\Enum;


/**
 * @method static self gateFirstRentSecond()
 * @method static self rentFirstGateSecond()
 */
class GateModeType extends Enum
{
    /**
     * Mode description:
     * - Gate first, Rent second
     *   This is the normal flow, after purchase, customer must go through the gate
     *   before proceeding into rental. If user started skate renting and try to go
     *   out from gate without returning skate, the gate will not be opened
     * 
     * - Rent first, Gate second
     *   Due to the map layout in Palembang, we need to change the gate strictness
     *   by allowing customer to go out through gate without returning (yet).
     *   This is because the skate rental is located outside from the rink
     */

    const MAP_INDEX = [
        'gateFirstRentSecond' => 1,
        'rentFirstGateSecond' => 2,
    ];

    const MAP_VALUE = [
        'gateFirstRentSecond' => 'Gate first, Rent second',
        'rentFirstGateSecond' => 'Rent first, Gate second',
    ];
}
