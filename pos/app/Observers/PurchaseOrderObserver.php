<?php

namespace App\Observers;

use App\Models\PurchaseOrder;
use App\Utils\GetAutoNumber;

class PurchaseOrderObserver
{
    /**
     * Handle the purchase order "created" event.
     *
     * @param  \App\PurchaseOrder  $purchaseOrder
     * @return void
     */
    public function created(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->purchase_order_ref_no = GetAutoNumber::getNextNumber('PO');
        $purchaseOrder->save();
    }

    /**
     * Handle the purchase order "updated" event.
     *
     * @param  \App\PurchaseOrder  $purchaseOrder
     * @return void
     */
    public function updated(PurchaseOrder $purchaseOrder)
    {
        //
    }

    /**
     * Handle the purchase order "deleted" event.
     *
     * @param  \App\PurchaseOrder  $purchaseOrder
     * @return void
     */
    public function deleted(PurchaseOrder $purchaseOrder)
    {
        //
    }

    /**
     * Handle the purchase order "restored" event.
     *
     * @param  \App\PurchaseOrder  $purchaseOrder
     * @return void
     */
    public function restored(PurchaseOrder $purchaseOrder)
    {
        //
    }

    /**
     * Handle the purchase order "force deleted" event.
     *
     * @param  \App\PurchaseOrder  $purchaseOrder
     * @return void
     */
    public function forceDeleted(PurchaseOrder $purchaseOrder)
    {
        //
    }
}
