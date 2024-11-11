<?php

namespace App\Events\Game;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StartEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public $game_id, public $user1_id, public $user2_id)
    {
        //
    }

       /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('start-game.' . $this->user1_id),
            new Channel('start-game.' . $this->user2_id),
        ];
    }
    public function broadcastAs(): string
    {
        return 'start-game';
    }

    public function broadcastWith(): array
    {
        return [
            'start_game' => $this->game_id,
        ];
    }
}
