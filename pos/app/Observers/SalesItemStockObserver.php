<?php

namespace App\Observers;

use App\Models\SalesItem;

class SalesItemStockObserver
{
    /**
     * Handle the sales item "created" event.
     *
     * @param  \App\SalesItem  $salesItem
     * @return void
     */
    public function created(SalesItem $salesItem)
    {
        //
    }

    /**
     * Handle the sales item "updated" event.
     *
     * @param  \App\SalesItem  $salesItem
     * @return void
     */
    public function updated(SalesItem $salesItem)
    {
        //
    }

    /**
     * Handle the sales item "deleted" event.
     *
     * @param  \App\SalesItem  $salesItem
     * @return void
     */
    public function deleted(SalesItem $salesItem)
    {
        $salesOrder = $salesItem->salesOrder;
        // Remove one by one to make sure observer is called

        $tempSalesFulfillments = $salesOrder->salesFulfillments()->where('sales_item_id', $salesItem->id)->get();
        foreach ($tempSalesFulfillments as $tempSalesFulfillment) $tempSalesFulfillment->delete();

        $tempSalesReturns = $salesOrder->salesReturns()->where('sales_item_id', $salesItem->id)->get();
        foreach ($tempSalesReturns as $tempSalesReturn) $tempSalesReturn->delete();

        $tempSalesRestocks = $salesOrder->salesRestocks()->where('sales_item_id', $salesItem->id)->get();
        foreach ($tempSalesRestocks as $tempSalesRestock) $tempSalesRestock->delete();
    }

    /**
     * Handle the sales item "restored" event.
     *
     * @param  \App\SalesItem  $salesItem
     * @return void
     */
    public function restored(SalesItem $salesItem)
    {
        //
    }

    /**
     * Handle the sales item "force deleted" event.
     *
     * @param  \App\SalesItem  $salesItem
     * @return void
     */
    public function forceDeleted(SalesItem $salesItem)
    {
        //
    }
}
