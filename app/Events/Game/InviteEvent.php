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

class InviteEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public $user_id, public $fromUserName, public $time, public $fromUserId)
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
            new Channel('game-invite.' . $this->user_id),
        ];
    }
    public function broadcastAs(): string
    {
        return 'game-invite';
    }

    public function broadcastWith(): array
    {
        return [
            'gameInvite' => [
                'name' => $this->fromUserName, 
                'time' => $this->time,
                'text' => 'Вас приглашает в игру',
                'id' => $this->fromUserId
            ],
        ];
    }
}