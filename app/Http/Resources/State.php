<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use function App\Helpers\getUploadImage;

class State extends JsonResource
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
            'id' => $this->id ?? "",
            'name' => $this->name ?? "",
            'iso2' => $this->iso2 ?? "",
            'country_id' => $this->country_id ?? "",
            'latitude' => $this->latitude ?? "",
            'longitude' => $this->longitude ?? "",
            'active' => $this->active
        ];
    }
}
