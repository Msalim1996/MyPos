<?php

namespace App\Observers;

use App\Enums\ItemType;
use App\Models\PurchaseFulfillment;
use App\Models\Stock;
use App\Models\MovementHistory;

class PurchaseFulfillmentObserver
{
    /**
     * Handle the purchase fulfillment "created" event.
     *
     * @param  \App\PurchaseFulfillment  $purchaseFulfillment
     * @return void
     */
    public function created(PurchaseFulfillment $purchaseFulfillment)
    {
        $item = $purchaseFulfillment->purchaseItem->item;

        // only do calculation for stocked type
        if (ItemType::stock()->isEqual($item->type) || ItemType::ticket()->isEqual($item->type)) {

            // current stock -= purchase fulfillment qty
            // if stock is not found, create new stock
            $stock = Stock::firstOrCreate([
                'location_id' => $purchaseFulfillment->location->id,
                'item_id' => $item->id,
            ]);
            $stock->qty += $purchaseFulfillment->qty;
            $stock->save();
            $originalQty = $stock->qty - $purchaseFulfillment->qty;
            //directly make a new movement history
            $movementHistory = MovementHistory::create([
                'item_id' => $purchaseFulfillment->purchaseItem->item_id,
                'moveable_id' => $purchaseFulfillment->id,
                'moveable_type' => 'Purchase fulfillment',
                'original_qty' => $originalQty,
                'new_qty' => $stock->qty,
                'description' => $purchaseFulfillment->description,
            ]);
            $movementHistory->save();
        }
    }

    /**
     * Handle the purchase fulfillment "updated" event.
     *
     * @param  \App\PurchaseFulfillment  $purchaseFulfillment
     * @return void
     */
    public function updated(PurchaseFulfillment $purchaseFulfillment)
    {
        $item = $purchaseFulfillment->purchaseItem->item;

        // only do calculation for stocked type
        if (ItemType::stock()->isEqual($item->type) || ItemType::ticket()->isEqual($item->type)) {
            // original data is return in associative array
            // add original stock back
            $originalpurchaseFulfillment = $purchaseFulfillment->getOriginal();
            $originalStock = Stock::firstOrCreate([
                'location_id' => $originalpurchaseFulfillment['location_id'],
                'item_id' => $item->id,
            ]);
            $originalStock->qty -= $originalpurchaseFulfillment['qty'];
            $originalStock->save();

            // if stock is not found, create new stock
            // reduce selected stock
            $stock = Stock::firstOrCreate([
                'location_id' => $purchaseFulfillment->location->id,
                'item_id' => $item->id,
            ]);
            $stock->qty += $purchaseFulfillment->qty;
            $stock->save();
            //directly make a new movement history
            $movementHistory = MovementHistory::create([
                'item_id' => $purchaseFulfillment->purchaseItem->item_id,
                'moveable_id' => $purchaseFulfillment->id,
                'moveable_type' => 'Purchase fulfillment',
                'original_qty' => $originalStock->qty + $originalpurchaseFulfillment['qty'],
                'new_qty' => $stock->qty,
                'description' => $purchaseFulfillment->description,
            ]);
            $movementHistory->save();
        }
    }

    /**
     * Handle the purchase fulfillment "deleted" event.
     *
     * @param  \App\PurchaseFulfillment  $purchaseFulfillment
     * @return void
     */
    public function deleted(PurchaseFulfillment $purchaseFulfillment)
    {
        $item = $purchaseFulfillment->purchaseItem->item;

        // only do calculation for stocked type
        if (ItemType::stock()->isEqual($item->type) || ItemType::ticket()->isEqual($item->type)) {

            // current stock += purchase fulfillment qty
            // if stock is not found, create new stock
            $stock = Stock::firstOrCreate([
                'location_id' => $purchaseFulfillment->location->id,
                'item_id' => $item->id,
            ]);
            $stock->qty -= $purchaseFulfillment->qty;
            $stock->save();
            $originalQty = $stock->qty + $purchaseFulfillment->qty;
            //directly make a new movement history
            $movementHistory = MovementHistory::create([
                'item_id' => $purchaseFulfillment->purchaseItem->item_id,
                'moveable_id' => $purchaseFulfillment->id,
                'moveable_type' => 'Purchase fulfillment',
                'original_qty' => $originalQty,
                'new_qty' => $stock->qty,
                'description' => $purchaseFulfillment->description,
            ]);
            $movementHistory->save();
        }
    }

    /**
     * Handle the purchase fulfillment "restored" event.
     *
     * @param  \App\PurchaseFulfillment  $purchaseFulfillment
     * @return void
     */
    public function restored(PurchaseFulfillment $purchaseFulfillment)
    {
        //
    }

    /**
     * Handle the purchase fulfillment "force deleted" event.
     *
     * @param  \App\PurchaseFulfillment  $purchaseFulfillment
     * @return void
     */
    public function forceDeleted(PurchaseFulfillment $purchaseFulfillment)
    {
        //
    }
}
