<?php

namespace App\Events\SkateRental;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Http\Resources\SkateResource;
use App\Models\Skate;

class SkateRentalUpdateEvent implements ShouldBroadcast
{
    // use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;

    public function __construct(array $skates)
    {
        $result = [];
		foreach ($skates as $skate) {
			array_push($result, new SkateResource($skate));
        }
        $this->data = $result;
    }

    public function broadcastAs()
    {
        return 'skate.update';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('skate');
    }
}
