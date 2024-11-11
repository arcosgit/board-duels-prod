<?php

namespace App\Events\Notification;


use App\Http\Resources\Chat\MessageNotificationResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessageEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public $newMessage, public $forUser)
    {
        
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('notification-message.' . $this->forUser),
        ];
    }
    public function broadcastAs(): string
    {
        return 'notification-message';
    }

    public function broadcastWith(): array
    {
        return [
            'notificationMessage' => MessageNotificationResource::make($this->newMessage)->resolve(),
        ];
    }
}
