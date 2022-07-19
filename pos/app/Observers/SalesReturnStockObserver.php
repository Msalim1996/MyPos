<?php

namespace App\Observers;

use App\Enums\ItemType;
use App\Enums\SalesItemType;
use App\Enums\PreType;
use App\Enums\BenefitType;
use App\Models\SalesReturn;
use App\Models\Stock;
use App\Models\MovementHistory;

class SalesReturnStockObserver
{
    /**
     * Handle the sales return "created" event.
     *
     * @param  \App\SalesReturn  $salesReturn
     * @return void
     */
    public function created(SalesReturn $salesReturn)
    {
        // check for sales item type
        if (SalesItemType::promotion()->isEqual($salesReturn->salesItem->item_type)) {
            $promotion = $salesReturn->salesItem->promotion;
            // check for pre type
            if (PreType::item()->isEqual($promotion->pre_type) || PreType::ticket()->isEqual($promotion->pre_type)) {
                //check if it is discarded or not
                if ($salesReturn->discard_stock = 0) {
                    $item = $promotion->preItem;
                    // only do calculation for stocked type
                    if (ItemType::stock()->isEqual($item->type) || ItemType::ticket()->isEqual($item->type)) {

                        // current stock -= sales fulfillment qty
                        // if stock is not found, create new stock
                        $stock = Stock::firstOrCreate([
                            'location_id' => $salesReturn->location->id,
                            'item_id' => $item->id,
                        ]);
                        $stock->qty += ($salesReturn->qty * $promotion->pre_qty);
                        $stock->save();
                        $originalQty = $stock->qty - ($salesReturn->qty * $promotion->pre_qty);
                        //directly make a new movement history
                        $movementHistory = MovementHistory::create([
                            'item_id' => $salesReturn->salesItem->item_id,
                            'moveable_id' => $salesReturn->id,
                            'moveable_type' => 'Sales return',
                            'original_qty' => $originalQty,
                            'new_qty' => $stock->qty,
                            'description' => $salesReturn->description,
                        ]);
                        $movementHistory->save();
                    }
                }
            }

            // check for benefit type
            if (BenefitType::freeItem()->isEqual($promotion->benefit_type) || BenefitType::itemDiscount()->isEqual($promotion->benefit_type)) {
                $item = $promotion->benefitItem;

                // only do calculation for stocked type
                if (ItemType::stock()->isEqual($item->type) || ItemType::ticket()->isEqual($item->type)) {

                    // current stock -= sales fulfillment qty
                    // if stock is not found, create new stock
                    $stock = Stock::firstOrCreate([
                        'location_id' => $salesReturn->location->id,
                        'item_id' => $item->id,
                    ]);
                    $stock->qty += ($salesReturn->qty * $promotion->benefit_qty);
                    $stock->save();
                    $originalQty = $stock->qty - ($salesReturn->qty * $promotion->benefit_qty);
                    //directly make a new movement history
                    $movementHistory = MovementHistory::create([
                        'item_id' => $salesReturn->salesItem->item_id,
                        'moveable_id' => $salesReturn->id,
                        'moveable_type' => 'Sales return',
                        'original_qty' => $originalQty,
                        'new_qty' => $stock->qty,
                        'description' => $salesReturn->description,
                    ]);
                    $movementHistory->save();
                }
            }
        } else if (SalesItemType::item()->isEqual($salesReturn->salesItem->item_type)) {
            $item = $salesReturn->salesItem->item;

            // only do calculation for stocked type and if item is not discarded
            if ((ItemType::stock()->isEqual($item->type) || ItemType::ticket()->isEqual($item->type)) && !$salesReturn->discard_stock) {
                // current stock += sales fulfillment qty
                // if stock is not found, create new stock
                $stock = Stock::firstOrCreate([
                    'location_id' => $salesReturn->location->id,
                    'item_id' => $item->id,
                ]);
                $stock->qty += $salesReturn->qty;
                $stock->save();
                $originalQty = $stock->qty - $salesReturn->qty;
                //directly make a new movement history
                $movementHistory = MovementHistory::create([
                    'item_id' => $salesReturn->salesItem->item_id,
                    'moveable_id' => $salesReturn->id,
                    'moveable_type' => 'Sales return',
                    'original_qty' => $originalQty,
                    'new_qty' => $stock->qty,
                    'description' => $salesReturn->description,
                ]);
                $movementHistory->save();
            }
        }
    }

    /**
     * Handle the sales return "updated" event.
     *
     * @param  \App\SalesReturn  $salesReturn
     * @return void
     */
    public function updated(SalesReturn $salesReturn)
    {
        if (SalesItemType::promotion()->isEqual($salesReturn->salesItem->item_type)) {

            //check if it is discarded or not
            if (!$salesReturn->discard_stock) {
                $promotion = $salesReturn->salesItem->promotion;

                // check for pre type
                if (PreType::item()->isEqual($promotion->pre_type) || PreType::ticket()->isEqual($promotion->pre_type)) {
                    $item = $promotion->preItem;

                    // only do calculation for stocked type
                    if (ItemType::stock()->isEqual($item->type) || ItemType::ticket()->isEqual($item->type)) {
                        // original data is return in associative array
                        // add original stock back
                        $originalSalesReturn = $salesReturn->getOriginal();
                        // Only proceed if discard_stock is false
                        if (!$originalSalesReturn['discard_stock'] || !$salesReturn->discard_stock) {
                            $originalStock = Stock::firstOrCreate([
                                'location_id' => $originalSalesReturn['location_id'],
                                'item_id' => $item->id,
                            ]);
                            $originalStock->qty -= ($originalSalesReturn['qty'] * $promotion->pre_qty);
                            $originalStock->save();

                            // if stock is not found, create new stock
                            // reduce selected stock
                            $stock = Stock::firstOrCreate([
                                'location_id' => $salesReturn->location->id,
                                'item_id' => $item->id,
                            ]);
                            $stock->qty += ($originalSalesReturn['qty'] * $promotion->pre_qty);
                            $stock->save();
                            //directly make a new movement history
                            $movementHistory = MovementHistory::create([
                                'item_id' => $salesReturn->salesItem->item_id,
                                'moveable_id' => $salesReturn->id,
                                'moveable_type' => 'Sales return',
                                'original_qty' => $originalStock->qty + ($originalSalesReturn['qty'] * $promotion->pre_qty),
                                'new_qty' => $stock->qty,
                                'description' => $salesReturn->description,
                            ]);
                            $movementHistory->save();
                        }
                    }
                }

                // check for benefit type
                if (BenefitType::freeItem()->isEqual($promotion->benefit_type) || BenefitType::itemDiscount()->isEqual($promotion->benefit_type)) {
                    $item = $promotion->benefitItem;

                    // only do calculation for stocked type
                    if (ItemType::stock()->isEqual($item->type) || ItemType::ticket()->isEqual($item->type)) {
                        // original data is return in associative array
                        // add original stock back
                        $originalSalesReturn = $salesReturn->getOriginal();
                        // Only proceed if discard_stock is false
                        if (!$originalSalesReturn['discard_stock'] || !$salesReturn->discard_stock) {
                            $originalStock = Stock::firstOrCreate([
                                'location_id' => $originalSalesReturn['location_id'],
                                'item_id' => $item->id,
                            ]);
                            $originalStock->qty -= ($originalSalesReturn['qty'] * $promotion->benefit_qty);
                            $originalStock->save();

                            // if stock is not found, create new stock
                            // reduce selected stock
                            $stock = Stock::firstOrCreate([
                                'location_id' => $salesReturn->location->id,
                                'item_id' => $item->id,
                            ]);
                            $stock->qty += ($originalSalesReturn['qty'] * $promotion->benefit_qty);
                            $stock->save();
                            //directly make a new movement history
                            $movementHistory = MovementHistory::create([
                                'item_id' => $salesReturn->salesItem->item_id,
                                'moveable_id' => $salesReturn->id,
                                'moveable_type' => 'Sales return',
                                'original_qty' => $originalStock->qty + ($originalSalesReturn['qty'] * $promotion->benefit_qty),
                                'new_qty' => $stock->qty,
                                'description' => $salesReturn->description,
                            ]);
                            $movementHistory->save();
                        }
                    }
                }
            }
        } else if (SalesItemType::item()->isEqual($salesReturn->salesItem->item_type)) {
            $item = $salesReturn->salesItem->item;

            // only do calculation for stocked type
            if (ItemType::stock()->isEqual($item->type) || ItemType::ticket()->isEqual($item->type)) {
                // original data is return in associative array
                // add original stock back
                $originalSalesReturn = $salesReturn->getOriginal();
                // Only proceed if discard_stock is false
                if (!$originalSalesReturn['discard_stock'] || !$salesReturn->discard_stock) {
                    $originalStock = Stock::firstOrCreate([
                        'location_id' => $originalSalesReturn['location_id'],
                        'item_id' => $item->id,
                    ]);
                    $originalStock->qty -= $originalSalesReturn['qty'];
                    $originalStock->save();

                    // if stock is not found, create new stock
                    // reduce selected stock
                    $stock = Stock::firstOrCreate([
                        'location_id' => $salesReturn->location->id,
                        'item_id' => $item->id,
                    ]);
                    $stock->qty += $salesReturn->qty;
                    $stock->save();
                    //directly make a new movement history
                    $movementHistory = MovementHistory::create([
                        'item_id' => $salesReturn->salesItem->item_id,
                        'moveable_id' => $salesReturn->id,
                        'moveable_type' => 'Sales return',
                        'original_qty' => $originalStock->qty + $originalSalesReturn['qty'],
                        'new_qty' => $stock->qty,
                        'description' => $salesReturn->description,
                    ]);
                    $movementHistory->save();
                }
            }
        }
    }

    /**
     * Handle the sales return "deleted" event.
     *
     * @param  \App\SalesReturn  $salesReturn
     * @return void
     */
    public function deleted(SalesReturn $salesReturn)
    {
        // check for sales item type
        if (SalesItemType::promotion()->isEqual($salesReturn->salesItem->item_type)) {

            $promotion = $salesReturn->salesItem->promotion;
            // check for pre type
            if (PreType::item()->isEqual($promotion->pre_type) || PreType::ticket()->isEqual($promotion->pre_type)) {

                //check if it is discarded or not
                if ($salesReturn->discard_stock = 0) {
                    $item = $promotion->preItem;

                    // only do calculation for stocked type
                    if (ItemType::stock()->isEqual($item->type) || ItemType::ticket()->isEqual($item->type)) {

                        // current stock -= sales fulfillment qty
                        // if stock is not found, create new stock
                        $stock = Stock::firstOrCreate([
                            'location_id' => $salesReturn->location->id,
                            'item_id' => $item->id,
                        ]);
                        $stock->qty -= ($salesReturn->qty * $promotion->pre_qty);
                        $stock->save();
                        $originalQty = $stock->qty + ($salesReturn->qty * $promotion->pre_qty);
                        //directly make a new movement history
                        $movementHistory = MovementHistory::create([
                            'item_id' => $salesReturn->salesItem->item_id,
                            'moveable_id' => $salesReturn->id,
                            'moveable_type' => 'Sales return',
                            'original_qty' => $originalQty,
                            'new_qty' => $stock->qty,
                            'description' => $salesReturn->description,
                        ]);
                        $movementHistory->save();
                    }
                }
            }

            // check for benefit type
            if (BenefitType::freeItem()->isEqual($promotion->benefit_type) || BenefitType::itemDiscount()->isEqual($promotion->benefit_type)) {
                $item = $promotion->benefitItem;

                // only do calculation for stocked type
                if (ItemType::stock()->isEqual($item->type) || ItemType::ticket()->isEqual($item->type)) {
                    // current stock -= sales fulfillment qty
                    // if stock is not found, create new stock
                    $stock = Stock::firstOrCreate([
                        'location_id' => $salesReturn->location->id,
                        'item_id' => $item->id,
                    ]);
                    $stock->qty -= ($salesReturn->qty * $promotion->benefit_qty);
                    $stock->save();
                    $originalQty = $stock->qty + ($salesReturn->qty * $promotion->benefit_qty);
                    //directly make a new movement history
                    $movementHistory = MovementHistory::create([
                        'item_id' => $salesReturn->salesItem->item_id,
                        'moveable_id' => $salesReturn->id,
                        'moveable_type' => 'Sales return',
                        'original_qty' => $originalQty,
                        'new_qty' => $stock->qty,
                        'description' => $salesReturn->description,
                    ]);
                    $movementHistory->save();
                }
            }
        } else if (SalesItemType::item()->isEqual($salesReturn->salesItem->item_type)) {
            $item = $salesReturn->salesItem->item;

            // only do calculation for stocked type
            if ((ItemType::stock()->isEqual($item->type) || ItemType::ticket()->isEqual($item->type))
                && !$salesReturn->discard_stock
            ) {

                // current stock += sales fulfillment qty
                // if stock is not found, create new stock
                $stock = Stock::firstOrCreate([
                    'location_id' => $salesReturn->location->id,
                    'item_id' => $item->id,
                ]);
                $stock->qty -= $salesReturn->qty;
                $stock->save();
                $originalQty = $stock->qty + $salesReturn->qty;
                //directly make a new movement history
                $movementHistory = MovementHistory::create([
                    'item_id' => $salesReturn->salesItem->item_id,
                    'moveable_id' => $salesReturn->id,
                    'moveable_type' => 'Sales return',
                    'original_qty' => $originalQty,
                    'new_qty' => $stock->qty,
                    'description' => $salesReturn->description,
                ]);
                $movementHistory->save();
            }
        }
    }

    /**
     * Handle the sales return "restored" event.
     *
     * @param  \App\SalesReturn  $salesReturn
     * @return void
     */
    public function restored(SalesReturn $salesReturn)
    {
        //
    }

    /**
     * Handle the sales return "force deleted" event.
     *
     * @param  \App\SalesReturn  $salesReturn
     * @return void
     */
    public function forceDeleted(SalesReturn $salesReturn)
    {
        //
    }
}
