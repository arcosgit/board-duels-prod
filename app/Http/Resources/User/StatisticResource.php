<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatisticResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'count_game' => $this->count_games,
            'count_wins' => $this->count_wins,
            'count_loss' => $this->count_games - $this->count_wins,
        ];
    }
}
