<?php

namespace App\Events\SkateTransaction;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Http\Resources\SkateResource;
use App\Http\Resources\SkateTransactionResource;
use App\Models\Skate;

class SkateTransactionUpdateEvent implements ShouldBroadcast
{
    // use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;

    public function __construct(array $skateTransactions)
    {
        $result = [];
		foreach ($skateTransactions as $skateTransaction) {
			array_push($result, new SkateTransactionResource($skateTransaction));
        }
        $this->data = $result;
    }

    public function broadcastAs()
    {
        return 'skate.transaction.update';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('skate.transaction');
    }
}
