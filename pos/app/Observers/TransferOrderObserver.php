<?php

namespace App\Observers;

use App\Models\MovementHistory;
use App\Models\TransferOrder;
use App\Utils\GetAutoNumber;
use Carbon\Carbon;

class TransferOrderObserver
{
    /**
     * Handle the transfer order "created" event.
     *
     * @param  \App\TransferOrder  $transferOrder
     * @return void
     */
    public function created(TransferOrder $transferOrder)
    {
        $transferOrder->transfer_ref_no = GetAutoNumber::getNextNumber('TO');
        $transferOrder->save();
    }

    /**
     * Handle the transfer order "updated" event.
     *
     * @param  \App\TransferOrder  $transferOrder
     * @return void
     */
    public function updated(TransferOrder $transferOrder)
    {
        $transferItems = $transferOrder->transferItems;

        if ($transferOrder->status != 'Cancelled'){
            foreach($transferItems as $transferItem)
            {
                $transferItem->status = $transferOrder->status;
                $transferItem->save();
            }
        }
    }

    /**
     * Handle the transfer order "deleted" event.
     *
     * @param  \App\TransferOrder  $transferOrder
     * @return void
     */
    public function deleted(TransferOrder $transferOrder)
    {
        //
    }

    /**
     * Handle the transfer order "restored" event.
     *
     * @param  \App\TransferOrder  $transferOrder
     * @return void
     */
    public function restored(TransferOrder $transferOrder)
    {
        //
    }

    /**
     * Handle the transfer order "force deleted" event.
     *
     * @param  \App\TransferOrder  $transferOrder
     * @return void
     */
    public function forceDeleted(TransferOrder $transferOrder)
    {
        //
    }
}
