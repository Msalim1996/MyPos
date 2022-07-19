<?php

namespace App\Events\GateRinkTicket;

use App\Http\Controllers\Api\GateTransactionController;
use App\Http\Resources\GateOnGoingTransactionResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Http\Resources\ShoeResource;
use App\Models\GateTransaction;
use App\Models\Shoe;

class OnGoingTicketRinkInDeleteEvent implements ShouldBroadcast
{
    public $data;

    public function __construct(array $tickets)
    {
        $result = [];
        foreach ($tickets as $ticket) {
            array_push($result, new GateOnGoingTransactionResource($ticket));
        }
        $this->data = $result;
    }

    public function broadcastAs()
    {
        return 'on.going.ticket.rink.in.delete';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('gate.rink.ticket');
    }
}
