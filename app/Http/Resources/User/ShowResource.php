<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'created' => $this->created_at->format('d.m.Y'),
            'statistics' => StatisticResource::make($this->statistic[0])->resolve(),
            'settings' => SettingResource::make($this->setting[0])->resolve()
        ];
    }
}
