<?php

namespace App\Observers;

use App\Models\AdjustItem;
use App\Models\MovementHistory;
use App\Models\Stock;
use App\Utils\GetAutoNumber;
use Spatie\QueryBuilder\QueryBuilder;

class AdjustItemObserver
{
    /**
     * Handle the adjust stock "created" event.
     *
     * @param  \App\AdjustItem  $adjustItem
     * @return void
     */
    public function created(AdjustItem $adjustItem)
    {
        // if stock is not found, create new stock
        $newStock = Stock::firstOrCreate([
            'location_id' => $adjustItem->location_id,
            'item_id' => $adjustItem->item_id,
        ]);
        $location = $adjustItem->location;
        
        //directly make a new movement history
        $movementHistory = MovementHistory::create([
            'item_id' => $adjustItem->item_id,
            'moveable_id' => $adjustItem->id,
            'moveable_type' => 'Adjust item',
            'original_qty' => $newStock->qty ? $newStock->qty : 0,
            'new_qty' => $adjustItem->old_qty + $adjustItem->difference,
            'description' => 'Adjust item where location is ' . $location->name,
        ]);
        $movementHistory->save();
        
        $newStock->qty = ($newStock->qty ? $newStock->qty : 0) + $adjustItem->difference;
        $newStock->save();
    }

    /**
     * Handle the adjust stock "updated" event.
     *
     * @param  \App\AdjustItem  $adjustItem
     * @return void
     */
    public function updated(AdjustItem $adjustItem)
    {
        //search the location 
        $originalAdjustItem = $adjustItem->getOriginal();
        // if stock is not found, create new stock
        $stock = Stock::firstOrCreate([
            'location_id' => $adjustItem->location_id,
            'item_id' => $adjustItem->item_id,
            ]);
        $location = $adjustItem->location;
        if ($originalAdjustItem["status"] == 'Completed')
        {
            //directly make a new movement history
            $movementHistory = MovementHistory::create([
                'item_id' => $adjustItem->item_id,
                'moveable_id' => $adjustItem->id,
                'moveable_type' => 'Adjust item',
                'original_qty' => ($stock->qty ? $stock->qty : 0),
                'new_qty' => ($stock->qty ? $stock->qty : 0) - $originalAdjustItem["difference"],
                'description' => 'Adjust item completed where location is ' . $location->name,
            ]);
            $movementHistory->save();

            $stock->qty = ($stock->qty ? $stock->qty : 0) - $originalAdjustItem["difference"];
        }

        if ($adjustItem->status == 'Completed')
        {
            //directly make a new movement history
            $movementHistory = MovementHistory::create([
                'item_id' => $adjustItem->item_id,
                'moveable_id' => $adjustItem->id,
                'moveable_type' => 'Adjust item',
                'original_qty' => ($stock->qty ? $stock->qty : 0),
                'new_qty' => ($stock->qty ? $stock->qty : 0) + $adjustItem->difference,
                'description' => 'Adjust item reverted where location is ' . $location->name,
            ]);
            $movementHistory->save();

            $stock->qty = ($stock->qty ? $stock->qty : 0) + $adjustItem->difference;
        }
        $stock->save();
    }

    /**
     * Handle the adjust stock "deleted" event.
     *
     * @param  \App\AdjustItem  $adjustItem
     * @return void
     */
    public function deleted(AdjustItem $adjustItem)
    {
        //
    }

    /**
     * Handle the adjust stock "restored" event.
     *
     * @param  \App\AdjustItem  $adjustItem
     * @return void
     */
    public function restored(AdjustItem $adjustItem)
    {
        //
    }

    /**
     * Handle the adjust stock "force deleted" event.
     *
     * @param  \App\AdjustItem  $adjustItem
     * @return void
     */
    public function forceDeleted(AdjustItem $adjustItem)
    {
        //
    }
}
