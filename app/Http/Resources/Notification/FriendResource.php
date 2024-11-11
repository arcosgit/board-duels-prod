<?php

namespace App\Http\Resources\Notification;

use App\Http\Resources\User\FindedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FriendResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id,
            'user' => FindedResource::make($this->user)->resolve(),
            'text' => 'Предложение дружбы от',
        ];
    }
}
