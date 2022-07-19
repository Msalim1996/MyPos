<?php

namespace App\Observers;

use App\Enums\ItemType;
use App\Enums\SalesItemType;
use App\Enums\PreType;
use App\Enums\BenefitType;
use App\Models\SalesFulfillment;
use App\Models\Stock;
use App\Models\MovementHistory;

class SalesFulfillmentStockObserver
{
    /**
     * Handle the sales fulfillment "created" event.
     *
     * @param  \App\SalesFulfillment  $salesFulfillment
     * @return void
     */
    public function created(SalesFulfillment $salesFulfillment)
    {
        // check for sales item type
        if (SalesItemType::promotion()->isEqual($salesFulfillment->salesItem->item_type)) {

            $promotion = $salesFulfillment->salesItem->promotion;
            // check for pre type
            if (PreType::item()->isEqual($promotion->pre_type) || PreType::ticket()->isEqual($promotion->pre_type)) {
                $item = $promotion->preItem;

                // only do calculation for stocked type
                if (ItemType::stock()->isEqual($item->type) || ItemType::ticket()->isEqual($item->type)) {

                    // current stock -= sales fulfillment qty
                    // if stock is not found, create new stock
                    $stock = Stock::firstOrCreate([
                        'location_id' => $salesFulfillment->location->id,
                        'item_id' => $item->id,
                    ]);
                    $stock->qty -= ($salesFulfillment->qty * $promotion->pre_qty);
                    $stock->save();
                    //directly make a new movement history
                    $originalQty = $stock->qty + ($salesFulfillment->qty * $promotion->pre_qty);
                    $movementHistory = MovementHistory::create([
                        'item_id' => $item->id,
                        'moveable_id' => $salesFulfillment->id,
                        'moveable_type' => 'Sales fulfillment',
                        'original_qty' => $originalQty,
                        'new_qty' => $stock->qty,
                        'description' => $salesFulfillment->description,
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
                        'location_id' => $salesFulfillment->location->id,
                        'item_id' => $item->id,
                    ]);
                    $stock->qty -= ($salesFulfillment->qty * $promotion->benefit_qty);
                    $stock->save();
                    //directly make a new movement history
                    $originalQty = $stock->qty + ($salesFulfillment->qty * $promotion->benefit_qty);
                    $movementHistory = MovementHistory::create([
                        'item_id' => $item->id,
                        'moveable_id' => $salesFulfillment->id,
                        'moveable_type' => 'Sales fulfillment',
                        'original_qty' => $originalQty,
                        'new_qty' => $stock->qty,
                        'description' => $salesFulfillment->description,
                    ]);
                    $movementHistory->save();
                }
            }
        } else if (SalesItemType::item()->isEqual($salesFulfillment->salesItem->item_type)) {
            $item = $salesFulfillment->salesItem->item;

            // only do calculation for stocked type
            if (ItemType::stock()->isEqual($item->type) || ItemType::ticket()->isEqual($item->type)) {

                // current stock -= sales fulfillment qty
                // if stock is not found, create new stock
                $stock = Stock::firstOrCreate([
                    'location_id' => $salesFulfillment->location->id,
                    'item_id' => $item->id,
                ]);
                $stock->qty -= $salesFulfillment->qty;
                $stock->save();

                // If stock is in created mode, the qty will be null. The solution is to save the stock first
                // and retrieve the qty + the fullfillment to get the original qty.
                $originalQty = $stock->qty + $salesFulfillment->qty;

                //directly make a new movement history
                $movementHistory = MovementHistory::create([
                    'item_id' => $item->id,
                    'moveable_id' => $salesFulfillment->id,
                    'moveable_type' => 'Sales fulfillment',
                    'original_qty' => $originalQty,
                    'new_qty' => $stock->qty,
                    'description' => $salesFulfillment->description,
                ]);
                $movementHistory->save();
            }
        }
    }

    /**
     * Handle the sales fulfillment "updated" event.
     *
     * @param  \App\SalesFulfillment  $salesFulfillment
     * @return void
     */
    public function updated(SalesFulfillment $salesFulfillment)
    {
        if (SalesItemType::promotion()->isEqual($salesFulfillment->salesItem->item_type)) {

            $promotion = $salesFulfillment->salesItem->promotion;
            // check for pre type
            if (PreType::item()->isEqual($promotion->pre_type) || PreType::ticket()->isEqual($promotion->pre_type)) {
                $item = $promotion->preItem;

                if (ItemType::stock()->isEqual($item->type) || ItemType::ticket()->isEqual($item->type)) {
                    // original data is return in associative array
                    // add original stock back
                    $originalSalesFulfillment = $salesFulfillment->getOriginal();
                    $originalStock = Stock::firstOrCreate([
                        'location_id' => $originalSalesFulfillment['location_id'],
                        'item_id' => $item->id,
                    ]);
                    $originalStock->qty += ($originalSalesFulfillment['qty'] * $promotion->pre_qty);
                    $originalStock->save();

                    // if stock is not found, create new stock
                    // reduce selected stock
                    $stock = Stock::firstOrCreate([
                        'location_id' => $salesFulfillment->location->id,
                        'item_id' => $item->id,
                    ]);
                    $stock->qty -= ($originalSalesFulfillment['qty'] * $promotion->pre_qty);
                    $stock->save();

                    //directly make a new movement history
                    $movementHistory = MovementHistory::create([
                        'item_id' => $item->id,
                        'moveable_id' => $salesFulfillment->id,
                        'moveable_type' => 'Sales fulfillment',
                        'original_qty' => $originalStock->qty - ($originalSalesFulfillment['qty'] * $promotion->pre_qty),
                        'new_qty' => $stock->qty,
                        'description' => $salesFulfillment->description,
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
                    $originalSalesFulfillment = $salesFulfillment->getOriginal();
                    $originalStock = Stock::firstOrCreate([
                        'location_id' => $originalSalesFulfillment['location_id'],
                        'item_id' => $item->id,
                    ]);
                    $originalStock->qty += ($originalSalesFulfillment['qty'] * $promotion->benefit_qty);
                    $originalStock->save();

                    // if stock is not found, create new stock
                    // reduce selected stock
                    $stock = Stock::firstOrCreate([
                        'location_id' => $salesFulfillment->location->id,
                        'item_id' => $item->id,
                    ]);
                    $stock->qty -= ($originalSalesFulfillment['qty'] * $promotion->benefit_qty);
                    $stock->save();
                    //directly make a new movement history
                    $movementHistory = MovementHistory::create([
                        'item_id' => $item->id,
                        'moveable_id' => $salesFulfillment->id,
                        'moveable_type' => 'Sales fulfillment',
                        'original_qty' => $originalStock->qty - ($originalSalesFulfillment['qty'] * $promotion->benefit_qty),
                        'new_qty' => $stock->qty,
                        'description' => $salesFulfillment->description,
                    ]);
                    $movementHistory->save();
                }
            }
        } else if (SalesItemType::item()->isEqual($salesFulfillment->salesItem->item_type)) {
            $item = $salesFulfillment->salesItem->item;

            // only do calculation for stocked type
            if (ItemType::stock()->isEqual($item->type) || ItemType::ticket()->isEqual($item->type)) {
                // original data is return in associative array
                // add original stock back
                $originalSalesFulfillment = $salesFulfillment->getOriginal();
                $originalStock = Stock::firstOrCreate([
                    'location_id' => $originalSalesFulfillment['location_id'],
                    'item_id' => $item->id,
                ]);
                $originalStock->qty += $originalSalesFulfillment['qty'];
                $originalStock->save();

                // if stock is not found, create new stock
                // reduce selected stock
                $stock = Stock::firstOrCreate([
                    'location_id' => $salesFulfillment->location->id,
                    'item_id' => $item->id,
                ]);
                $stock->qty -= $salesFulfillment->qty;
                $stock->save();
                //directly make a new movement history
                $movementHistory = MovementHistory::create([
                    'item_id' => $item->id,
                    'moveable_id' => $salesFulfillment->id,
                    'moveable_type' => 'Sales fulfillment',
                    'original_qty' => $originalStock->qty - $originalSalesFulfillment['qty'],
                    'new_qty' => $stock->qty,
                    'description' => $salesFulfillment->description,
                ]);
                $movementHistory->save();
            }
        }
    }

    /**
     * Handle the sales fulfillment "deleted" event.
     *
     * @param  \App\SalesFulfillment  $salesFulfillment
     * @return void
     */
    public function deleted(SalesFulfillment $salesFulfillment)
    {
        // check for sales item type
        if (SalesItemType::promotion()->isEqual($salesFulfillment->salesItem->item_type)) {

            $promotion = $salesFulfillment->salesItem->promotion;
            // check for pre type
            if (PreType::item()->isEqual($promotion->pre_type) || PreType::ticket()->isEqual($promotion->pre_type)) {
                $item = $promotion->preItem;

                // only do calculation for stocked type
                if (ItemType::stock()->isEqual($item->type) || ItemType::ticket()->isEqual($item->type)) {

                    // current stock -= sales fulfillment qty
                    // if stock is not found, create new stock
                    $stock = Stock::firstOrCreate([
                        'location_id' => $salesFulfillment->location->id,
                        'item_id' => $item->id,
                    ]);
                    $stock->qty += ($salesFulfillment->qty * $promotion->pre_qty);
                    $stock->save();
                    $originalQty = $stock->qty - ($salesFulfillment->qty * $promotion->pre_qty);
                    //directly make a new movement history
                    $movementHistory = MovementHistory::create([
                        'item_id' => $item->id,
                        'moveable_id' => $salesFulfillment->id,
                        'moveable_type' => 'Sales fulfillment',
                        'original_qty' => $originalQty,
                        'new_qty' => $stock->qty,
                        'description' => $salesFulfillment->description,
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
                        'location_id' => $salesFulfillment->location->id,
                        'item_id' => $item->id,
                    ]);
                    $stock->qty += ($salesFulfillment->qty * $promotion->benefit_qty);
                    $stock->save();
                    $originalQty = $stock->qty - ($salesFulfillment->qty * $promotion->benefit_qty);
                    //directly make a new movement history
                    $movementHistory = MovementHistory::create([
                        'item_id' => $item->id,
                        'moveable_id' => $salesFulfillment->id,
                        'moveable_type' => 'Sales fulfillment',
                        'original_qty' => $originalQty,
                        'new_qty' => $stock->qty,
                        'description' => $salesFulfillment->description,
                    ]);
                    $movementHistory->save();
                }
            }
        } else if (SalesItemType::item()->isEqual($salesFulfillment->salesItem->item_type)) {
            $item = $salesFulfillment->salesItem->item;

            // only do calculation for stocked type
            if (ItemType::stock()->isEqual($item->type) || ItemType::ticket()->isEqual($item->type)) {

                // current stock += sales fulfillment qty
                // if stock is not found, create new stock
                $stock = Stock::firstOrCreate([
                    'location_id' => $salesFulfillment->location->id,
                    'item_id' => $item->id,
                ]);
                $stock->qty += $salesFulfillment->qty;
                $stock->save();
                $originalQty = $stock->qty - $salesFulfillment->qty;
                //directly make a new movement history
                $movementHistory = MovementHistory::create([
                    'item_id' => $item->id,
                    'moveable_id' => $salesFulfillment->id,
                    'moveable_type' => 'Sales fulfillment',
                    'original_qty' => $originalQty,
                    'new_qty' => $stock->qty,
                    'description' => $salesFulfillment->description,
                ]);
                $movementHistory->save();
            }
        }
    }

    /**
     * Handle the sales fulfillment "restored" event.
     *
     * @param  \App\SalesFulfillment  $salesFulfillment
     * @return void
     */
    public function restored(SalesFulfillment $salesFulfillment)
    {
        //
    }

    /**
     * Handle the sales fulfillment "force deleted" event.
     *
     * @param  \App\SalesFulfillment  $salesFulfillment
     * @return void
     */
    public function forceDeleted(SalesFulfillment $salesFulfillment)
    {
        //
    }
}
