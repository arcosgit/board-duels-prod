<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'statistics' => $this->statistics,
            'freind_add_can' => $this->freind_add_can,
            'lobby_can' => $this->lobby_can,
            'message_can' => $this->message_can
        ];
    }
}
