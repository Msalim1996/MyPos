<?php

namespace App\Observers;

use App\Enums\ItemType;
use App\Enums\SalesItemType;
use App\Enums\PreType;
use App\Enums\BenefitType;
use App\Models\SalesRestock;
use App\Models\Stock;
use App\Models\MovementHistory;

class SalesRestockStockObserver
{
    /**
     * Handle the sales restock "created" event.
     *
     * @param  \App\SalesRestock  $salesRestock
     * @return void
     */
    public function created(SalesRestock $salesRestock)
    {
        // check for sales item type
        if (SalesItemType::promotion()->isEqual($salesRestock->salesItem->item_type)) {

            $promotion = $salesRestock->salesItem->promotion;
            // check for pre type
            if (PreType::item()->isEqual($promotion->pre_type) || PreType::ticket()->isEqual($promotion->pre_type)) {
                $item = $promotion->preItem;

                // only do calculation for stocked type
                if (ItemType::stock()->isEqual($item->type) || ItemType::ticket()->isEqual($item->type)) {

                    // current stock -= sales fulfillment qty
                    // if stock is not found, create new stock
                    $stock = Stock::firstOrCreate([
                        'location_id' => $salesRestock->location->id,
                        'item_id' => $item->id,
                    ]);
                    $stock->qty -= ($salesRestock->qty * $promotion->pre_qty);
                    $stock->save();
                    $originalQty = $stock->qty + ($salesRestock->qty * $promotion->pre_qty);
                    //directly make a new movement history
                    $movementHistory = MovementHistory::create([
                        'item_id' => $salesRestock->salesItem->item_id,
                        'moveable_id' => $salesRestock->id,
                        'moveable_type' => 'Sales restock',
                        'original_qty' => $originalQty,
                        'new_qty' => $stock->qty,
                        'description' => $salesRestock->description,
                    ]);
                    $movementHistory->save();
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
                        'location_id' => $salesRestock->location->id,
                        'item_id' => $item->id,
                    ]);
                    $stock->qty -= ($salesRestock->qty * $promotion->benefit_qty);
                    $stock->save();
                    $originalQty = $stock->qty + ($salesRestock->qty * $promotion->benefit_qty);
                    //directly make a new movement history
                    $movementHistory = MovementHistory::create([
                        'item_id' => $salesRestock->salesItem->item_id,
                        'moveable_id' => $salesRestock->id,
                        'moveable_type' => 'Sales restock',
                        'original_qty' => $originalQty,
                        'new_qty' => $stock->qty,
                        'description' => $salesRestock->description,
                    ]);
                    $movementHistory->save();
                }
            }
        } else if (SalesItemType::item()->isEqual($salesRestock->salesItem->item_type)) {
            $item = $salesRestock->salesItem->item;

            // only do calculation for stocked type
            if (ItemType::stock()->isEqual($item->type) || ItemType::ticket()->isEqual($item->type)) {

                // current stock -= sales restock qty
                // if stock is not found, create new stock
                $stock = Stock::firstOrCreate([
                    'location_id' => $salesRestock->location->id,
                    'item_id' => $item->id,
                ]);
                $stock->qty -= $salesRestock->qty;
                $stock->save();
                $originalQty = $stock->qty + $salesRestock->qty;
                //directly make a new movement history
                $movementHistory = MovementHistory::create([
                    'item_id' => $salesRestock->salesItem->item_id,
                    'moveable_id' => $salesRestock->id,
                    'moveable_type' => 'Sales restock',
                    'original_qty' => $originalQty,
                    'new_qty' => $stock->qty,
                    'description' => $salesRestock->description,
                ]);
                $movementHistory->save();
            }
        }
    }

    /**
     * Handle the sales restock "updated" event.
     *
     * @param  \App\SalesRestock  $salesRestock
     * @return void
     */
    public function updated(SalesRestock $salesRestock)
    {
        if (SalesItemType::promotion()->isEqual($salesRestock->salesItem->item_type)) {

            $promotion = $salesRestock->salesItem->promotion;
            // check for pre type
            if (PreType::item()->isEqual($promotion->pre_type) || PreType::ticket()->isEqual($promotion->pre_type)) {
                $item = $promotion->preItem;

                if (ItemType::stock()->isEqual($item->type) || ItemType::ticket()->isEqual($item->type)) {
                    // original data is return in associative array
                    // add original stock back
                    $originalsalesRestock = $salesRestock->getOriginal();
                    $originalStock = Stock::firstOrCreate([
                        'location_id' => $originalsalesRestock['location_id'],
                        'item_id' => $item->id,
                    ]);
                    $originalStock->qty += ($originalsalesRestock['qty'] * $promotion->pre_qty);
                    $originalStock->save();

                    // if stock is not found, create new stock
                    // reduce selected stock
                    $stock = Stock::firstOrCreate([
                        'location_id' => $salesRestock->location->id,
                        'item_id' => $item->id,
                    ]);
                    $stock->qty -= ($originalsalesRestock['qty'] * $promotion->pre_qty);
                    $stock->save();
                    //directly make a new movement history
                    $movementHistory = MovementHistory::create([
                        'item_id' => $salesRestock->salesItem->item_id,
                        'moveable_id' => $salesRestock->id,
                        'moveable_type' => 'Sales restock',
                        'original_qty' => $originalStock->qty - ($originalsalesRestock['qty'] * $promotion->pre_qty),
                        'new_qty' => $stock->qty,
                        'description' => $salesRestock->description,
                    ]);
                    $movementHistory->save();
                }
            }

            // check for benefit type
            if (BenefitType::freeItem()->isEqual($promotion->benefit_type) || BenefitType::itemDiscount()->isEqual($promotion->benefit_type)) {
                $item = $promotion->benefitItem;

                if (ItemType::stock()->isEqual($item->type) || ItemType::ticket()->isEqual($item->type)) {
                    // original data is return in associative array
                    // add original stock back
                    $originalsalesRestock = $salesRestock->getOriginal();
                    $originalStock = Stock::firstOrCreate([
                        'location_id' => $originalsalesRestock['location_id'],
                        'item_id' => $item->id,
                    ]);
                    $originalStock->qty += ($originalsalesRestock['qty'] * $promotion->benefit_qty);
                    $originalStock->save();

                    // if stock is not found, create new stock
                    // reduce selected stock
                    $stock = Stock::firstOrCreate([
                        'location_id' => $salesRestock->location->id,
                        'item_id' => $item->id,
                    ]);
                    $stock->qty -= ($originalsalesRestock['qty'] * $promotion->benefit_qty);
                    $stock->save();
                    //directly make a new movement history
                    $movementHistory = MovementHistory::create([
                        'item_id' => $salesRestock->salesItem->item_id,
                        'moveable_id' => $salesRestock->id,
                        'moveable_type' => 'Sales restock',
                        'original_qty' => $originalStock->qty - ($originalsalesRestock['qty'] * $promotion->benefit_qty),
                        'new_qty' => $stock->qty,
                        'description' => $salesRestock->description,
                    ]);
                    $movementHistory->save();
                }
            }
        } else if (SalesItemType::item()->isEqual($salesRestock->salesItem->item_type)) {
            $item = $salesRestock->salesItem->item;

            // only do calculation for stocked type
            if (ItemType::stock()->isEqual($item->type) || ItemType::ticket()->isEqual($item->type)) {
                // original data is return in associative array
                // add original stock back
                $originalSalesRestock = $salesRestock->getOriginal();
                $originalStock = Stock::firstOrCreate([
                    'location_id' => $originalSalesRestock['location_id'],
                    'item_id' => $item->id,
                ]);
                $originalStock->qty += $originalSalesRestock['qty'];
                $originalStock->save();

                // if stock is not found, create new stock
                // reduce selected stock
                $stock = Stock::firstOrCreate([
                    'location_id' => $salesRestock->location->id,
                    'item_id' => $item->id,
                ]);
                $stock->qty -= $salesRestock->qty;
                $stock->save();
                //directly make a new movement history
                $movementHistory = MovementHistory::create([
                    'item_id' => $salesRestock->salesItem->item_id,
                    'moveable_id' => $salesRestock->id,
                    'moveable_type' => 'Sales restock',
                    'original_qty' => $originalStock->qty - $originalSalesRestock['qty'],
                    'new_qty' => $stock->qty,
                    'description' => $salesRestock->description,
                ]);
                $movementHistory->save();
            }
        }
    }

    /**
     * Handle the sales restock "deleted" event.
     *
     * @param  \App\SalesRestock  $salesRestock
     * @return void
     */
    public function deleted(SalesRestock $salesRestock)
    {
        // check for sales item type
        if (SalesItemType::promotion()->isEqual($salesRestock->salesItem->item_type)) {

            $promotion = $salesRestock->salesItem->promotion;
            // check for pre type
            if (PreType::item()->isEqual($promotion->pre_type) || PreType::ticket()->isEqual($promotion->pre_type)) {
                $item = $promotion->preItem;

                // only do calculation for stocked type
                if (ItemType::stock()->isEqual($item->type) || ItemType::ticket()->isEqual($item->type)) {

                    // current stock -= sales fulfillment qty
                    // if stock is not found, create new stock
                    $stock = Stock::firstOrCreate([
                        'location_id' => $salesRestock->location->id,
                        'item_id' => $item->id,
                    ]);
                    $stock->qty += ($salesRestock->qty * $promotion->pre_qty);
                    $stock->save();
                    $originalQty = $stock->qty - ($salesRestock->qty * $promotion->pre_qty);
                    //directly make a new movement history
                    $movementHistory = MovementHistory::create([
                        'item_id' => $salesRestock->salesItem->item_id,
                        'moveable_id' => $salesRestock->id,
                        'moveable_type' => 'Sales restock',
                        'original_qty' => $originalQty,
                        'new_qty' => $stock->qty,
                        'description' => $salesRestock->description,
                    ]);
                    $movementHistory->save();
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
                        'location_id' => $salesRestock->location->id,
                        'item_id' => $item->id,
                    ]);
                    $stock->qty += ($salesRestock->qty * $promotion->benefit_qty);
                    $stock->save();
                    $originalQty = $stock->qty - ($salesRestock->qty * $promotion->benefit_qty);
                    //directly make a new movement history
                    $movementHistory = MovementHistory::create([
                        'item_id' => $salesRestock->salesItem->item_id,
                        'moveable_id' => $salesRestock->id,
                        'moveable_type' => 'Sales restock',
                        'original_qty' => $originalQty,
                        'new_qty' => $stock->qty,
                        'description' => $salesRestock->description,
                    ]);
                    $movementHistory->save();
                }
            }
        } else if (SalesItemType::item()->isEqual($salesRestock->salesItem->item_type)) {
            $item = $salesRestock->salesItem->item;

            // only do calculation for stocked type
            if (ItemType::stock()->isEqual($item->type) || ItemType::ticket()->isEqual($item->type)) {

                // current stock += sales fulfillment qty
                // if stock is not found, create new stock
                $stock = Stock::firstOrCreate([
                    'location_id' => $salesRestock->location->id,
                    'item_id' => $item->id,
                ]);
                $stock->qty += $salesRestock->qty;
                $stock->save();
                $originalQty = $stock->qty - $salesRestock->qty;
                //directly make a new movement history
                $movementHistory = MovementHistory::create([
                    'item_id' => $salesRestock->salesItem->item_id,
                    'moveable_id' => $salesRestock->id,
                    'moveable_type' => 'Sales restock',
                    'original_qty' => $originalQty,
                    'new_qty' => $stock->qty,
                    'description' => $salesRestock->description,
                ]);
                $movementHistory->save();
            }
        }
    }

    /**
     * Handle the sales restock "restored" event.
     *
     * @param  \App\SalesRestock  $salesRestock
     * @return void
     */
    public function restored(SalesRestock $salesRestock)
    {
        //
    }

    /**
     * Handle the sales restock "force deleted" event.
     *
     * @param  \App\SalesRestock  $salesRestock
     * @return void
     */
    public function forceDeleted(SalesRestock $salesRestock)
    {
        //
    }
}
