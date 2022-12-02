<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use function App\Helpers\getUploadImage;

class BarberPortfolio extends JsonResource
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
            'barber_id' => $this->barber_id,
            'path' => $this->path ? getUploadImage($this->path) : 'storage/no-image.jpg',
        ];
    }
}
