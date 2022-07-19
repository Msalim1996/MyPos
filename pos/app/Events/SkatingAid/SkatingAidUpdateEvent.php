<?php

namespace App\Events\SkatingAid;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Http\Resources\SkatingAidResource;
use App\Models\SkatingAid;

class SkatingAidUpdateEvent implements ShouldBroadcast
{
    // use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;

    public function __construct(array $skatingAids)
    {
        $result = [];
		foreach ($skatingAids as $skatingAid) {
			array_push($result, new SkatingAidResource($skatingAid));
        }
        $this->data = $result;
    }

    public function broadcastAs()
    {
        return 'skating.aid.update';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('skating.aid');
    }
}
