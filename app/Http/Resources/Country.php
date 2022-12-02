<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Country extends JsonResource
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
            'id' => $this->id ?? NULL,
            'name' => $this->name ?? "",
            'iso2' => $this->iso2 ?? "",
            'currency' => $this->currency ?? "",
            'phonecode' => $this->phonecode ?? "",
            'latitude' => $this->latitude ?? "",
            'longitude' => $this->longitude ?? "",
            'region' => $this->region ?? "",
            'emoji' => $this->emoji ?? "",
            'emojiU' => $this->emojiU ?? "",
            'active' => $this->active
        ];
    }
}
