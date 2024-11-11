<?php

namespace App\Http\Resources\Chat;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageNotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'chat_id' => $this->chat_id,
            'user_id' => $this->user_id,
            'message' => $this->message,
            'userName' => $this->user->name
        ];
    }
}
