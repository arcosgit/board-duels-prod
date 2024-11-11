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

class PlayEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public $game_id, public $current, public $board, public $winner = null, public $isDraw = false)
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
            new Channel('game.' . $this->game_id),
        ];
    }
    public function broadcastAs(): string
    {
        return 'game';
    }

    public function broadcastWith(): array
    {
        return [
            'board' => $this->board,
            'current' => $this->current,
            'winner' => $this->winner,
            'isDraw' => $this->isDraw
        ];
    }
}
