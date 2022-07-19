<?php

namespace App\Observers;

use App\Models\AdjustOrder;
use App\Utils\GetAutoNumber;
use Spatie\QueryBuilder\QueryBuilder;

class AdjustOrderObserver
{
    /**
     * Handle the adjust stock "created" event.
     *
     * @param  \App\AdjustOrder  $adjustOrder
     * @return void
     */
    public function created(AdjustOrder $adjustOrder)
    {
        $adjustOrder->adjust_ref_no = GetAutoNumber::getNextNumber('AO');
        $adjustOrder->save();
    }

    /**
     * Handle the adjust stock "updated" event.
     *
     * @param  \App\AdjustOrder  $adjustOrder
     * @return void
     */
    public function updated(AdjustOrder $adjustOrder)
    {
        $adjustItems = $adjustOrder->adjustItems;

        foreach($adjustItems as $adjustItem)
        {
            $adjustItem->status = "Cancelled";
            $adjustItem->save();
        }
    }

    /**
     * Handle the adjust stock "deleted" event.
     *
     * @param  \App\AdjustOrder  $adjustOrder
     * @return void
     */
    public function deleted(AdjustOrder $adjustOrder)
    {
        //
    }

    /**
     * Handle the adjust stock "restored" event.
     *
     * @param  \App\AdjustOrder  $adjustOrder
     * @return void
     */
    public function restored(AdjustOrder $adjustOrder)
    {
        //
    }

    /**
     * Handle the adjust stock "force deleted" event.
     *
     * @param  \App\AdjustOrder  $adjustOrder
     * @return void
     */
    public function forceDeleted(AdjustOrder $adjustOrder)
    {
        //
    }
}
