<?php

namespace App\Observers;

use App\Models\SalesOrder;
use App\Utils\GetAutoNumber;

class SalesOrderObserver
{
    /**
     * Handle the sales order "created" event.
     *
     * @param  \App\SalesOrder  $salesOrder
     * @return void
     */
    public function created(SalesOrder $salesOrder)
    {
        $salesOrder->order_ref_no = GetAutoNumber::getNextNumber('SO');
        $salesOrder->save();
    }

    /**
     * Handle the sales order "updated" event.
     *
     * @param  \App\SalesOrder  $salesOrder
     * @return void
     */
    public function updated(SalesOrder $salesOrder)
    {
        //
    }

    /**
     * Handle the sales order "deleted" event.
     *
     * @param  \App\SalesOrder  $salesOrder
     * @return void
     */
    public function deleted(SalesOrder $salesOrder)
    {
        //
    }

    /**
     * Handle the sales order "restored" event.
     *
     * @param  \App\SalesOrder  $salesOrder
     * @return void
     */
    public function restored(SalesOrder $salesOrder)
    {
        //
    }

    /**
     * Handle the sales order "force deleted" event.
     *
     * @param  \App\SalesOrder  $salesOrder
     * @return void
     */
    public function forceDeleted(SalesOrder $salesOrder)
    {
        //
    }
}
