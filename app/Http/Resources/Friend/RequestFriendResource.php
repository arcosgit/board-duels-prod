<?php

namespace App\Http\Resources\Friend;

use App\Http\Resources\User\FindedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestFriendResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => FindedResource::make($this->user)->resolve()
        ];
    }
}
