<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Time extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'time' => $this->time ?? '',
            'is_active' => $this->is_active,
        ];
    }
}
