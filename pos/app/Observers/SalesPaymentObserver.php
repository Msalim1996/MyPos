<?php

namespace App\Observers;

use App\Models\SalesPayment;
use App\Utils\GetAutoNumber;

class SalesPaymentObserver
{
    /**
     * Handle the sales payment "created" event.
     *
     * @param  \App\SalesPayment  $salesPayment
     * @return void
     */
    public function created(SalesPayment $salesPayment)
    {
        $salesPayment->payment_ref_no = GetAutoNumber::getNextNumber('INV');
        $salesPayment->save();
    }

    /**
     * Handle the sales payment "updated" event.
     *
     * @param  \App\SalesPayment  $salesPayment
     * @return void
     */
    public function updated(SalesPayment $salesPayment)
    {
        //
    }

    /**
     * Handle the sales payment "deleted" event.
     *
     * @param  \App\SalesPayment  $salesPayment
     * @return void
     */
    public function deleted(SalesPayment $salesPayment)
    {
        //
    }

    /**
     * Handle the sales payment "restored" event.
     *
     * @param  \App\SalesPayment  $salesPayment
     * @return void
     */
    public function restored(SalesPayment $salesPayment)
    {
        //
    }

    /**
     * Handle the sales payment "force deleted" event.
     *
     * @param  \App\SalesPayment  $salesPayment
     * @return void
     */
    public function forceDeleted(SalesPayment $salesPayment)
    {
        //
    }
}
