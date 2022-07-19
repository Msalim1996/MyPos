<?php

namespace App\Observers;

use App\Models\MovementHistory;
use App\Models\Stock;
use App\Models\TransferItem;
use App\Utils\GetAutoNumber;
use Carbon\Carbon;

class TransferItemObserver
{
    /**
     * Handle the transfer item "created" event.
     *
     * @param  \App\TransferItem  $transferItem
     * @return void
     */
    public function created(TransferItem $transferItem)
    {

        //get the first item's stock qty where the location is the same as location_id and decrease it
        // if stock is not found, create new stock
        $currentStock = Stock::firstOrCreate([
            'location_id' => $transferItem->current_location_id,
            'item_id' => $transferItem->item_id,
        ]);

        //add the decreased stocks to the destination location
        // if stock is not found, create new stock
        $destinationStock = Stock::firstOrCreate([
            'location_id' => $transferItem->destination_location_id,
            'item_id' => $transferItem->item_id,
        ]);

        //directly make a new movement history
        $movementHistory = MovementHistory::create([
            'item_id' => $transferItem->item_id,
            'moveable_id' => $transferItem->id,
            'moveable_type' => 'Transfer item',
            'original_qty' => $transferItem->item->stocks->where('location_id','=',$transferItem->destination_location_id)->first()->qty,
            'new_qty' => ($transferItem->item->stocks->where('location_id','=',$transferItem->destination_location_id)->first()->qty + $transferItem->qty),
            'description' => 'Transfer item created from location ' . $currentStock->location->name . ' to location ' . $destinationStock->location->name,
        ]);
        $movementHistory->save();
    }

    /**
     * Handle the transfer item "updated" event.
     *
     * @param  \App\TransferItem  $transferItem
     * @return void
     */
    public function updated(TransferItem $transferItem)
    {
        $originalTransferItem = $transferItem->getOriginal();
        // if stock is not found, create new stock
        $currentStock = Stock::firstOrCreate([
            'location_id' => $transferItem->current_location_id,
            'item_id' => $transferItem->item_id,
        ]);

        //add the decreased stocks to the destination location
        // if stock is not found, create new stock
        $destinationStock = Stock::firstOrCreate([
            'location_id' => $transferItem->destination_location_id,
            'item_id' => $transferItem->item_id,
        ]);

        $transferOrder = $transferItem->transferOrder;

        if ($originalTransferItem["status"] == 'In Transit')
        {
            //directly make a new movement history
            $movementHistory = MovementHistory::create([
                'item_id' => $transferItem->item_id,
                'moveable_id' => $transferItem->id,
                'moveable_type' => 'Transfer item',
                'original_qty' => $transferItem->item->stocks->where('location_id','=',$transferItem->current_location_id)->first()->qty,
                'new_qty' => $currentStock->qty + $originalTransferItem["qty"],
                'description' => 'Transfer item changed (stocks from current location reverted)',
            ]);
            $movementHistory->save();

            $currentStock->qty = $currentStock->qty + $originalTransferItem["qty"];
            $currentStock->save();
        }
        else if ($originalTransferItem["status"] == 'Completed')
        {
            $destinationStock->qty = $destinationStock->qty - $transferItem["qty"];
            $destinationStock->save();

            //directly make a new movement history
            $movementHistory = MovementHistory::create([
                'item_id' => $transferItem->item_id,
                'moveable_id' => $transferItem->id,
                'moveable_type' => 'Transfer item',
                'original_qty' => $transferItem->item->stocks->where('location_id','=',$transferItem->current_location_id)->first()->qty,
                'new_qty' => $currentStock->qty + $transferItem->qty,
                'description' => 'Current Stock = ' . $currentStock->qty . ', Destination Stock = ' . $destinationStock->qty,
            ]);
            $movementHistory->save();

            $currentStock->qty = $currentStock->qty + $transferItem->qty;
            $currentStock->save();
        }

        

        if ($transferItem->status == 'In Transit')
        {
            //directly make a new movement history
            $movementHistory = MovementHistory::create([
                'item_id' => $transferItem->item_id,
                'moveable_id' => $transferItem->id,
                'moveable_type' => 'Transfer item',
                'original_qty' => $transferItem->item->stocks->where('location_id','=',$transferItem->current_location_id)->first()->qty,
                'new_qty' => $currentStock->qty - $transferItem->qty,
                'description' => 'Transfer item changed (stocks from current location reverted)',
            ]);
            $movementHistory->save();

            $currentStock->qty = $currentStock->qty - $transferItem->qty;
            $currentStock->save();
        }
        else if ($transferItem->status == 'Completed')
        {
            $destinationStock->qty = $destinationStock->qty + $transferItem->qty;
            $destinationStock->save();

            //directly make a new movement history
            $movementHistory = MovementHistory::create([
                'item_id' => $transferItem->item_id,
                'moveable_id' => $transferItem->id,
                'moveable_type' => 'Transfer item',
                'original_qty' => $transferItem->item->stocks->where('location_id','=',$transferItem->current_location_id)->first()->qty,
                'new_qty' => $currentStock->qty - $transferItem->qty,
                'description' => 'Current Stock = ' . $currentStock->qty . ', Destination Stock = ' . $destinationStock->qty,
            ]);
            $movementHistory->save();

            $currentStock->qty = $currentStock->qty - $transferItem->qty;
            $currentStock->save();
        }
    }

    /**
     * Handle the transfer item "deleted" event.
     *
     * @param  \App\TransferItem  $transferItem
     * @return void
     */
    public function deleted(transferItem $transferItem)
    {
        //
    }

    /**
     * Handle the transfer item "restored" event.
     *
     * @param  \App\TransferItem  $transferItem
     * @return void
     */
    public function restored(transferItem $transferItem)
    {
        //
    }

    /**
     * Handle the transfer item "force deleted" event.
     *
     * @param  \App\TransferItem  $transferItem
     * @return void
     */
    public function forceDeleted(transferItem $transferItem)
    {
        //
    }
}
