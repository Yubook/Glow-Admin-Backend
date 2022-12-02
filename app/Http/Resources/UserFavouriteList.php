<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use function App\Helpers\getUploadImage;

class UserFavouriteList extends JsonResource
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
            'barber_id' => $this->barber->id,
            'phone_code' => $this->barber->phone_code ?? '',
            'mobile' => (string)$this->barber->mobile ?? '',
            'name' => $this->barber->name ?? '',
            'profile' => isset($this->barber->profile) ? getUploadImage($this->barber->profile) : 'storage/no-image.jpg',
            'gender' => $this->barber->gender ?? '',
            'average_rating' => $this->barber->average_rating,
            'total_reviews' => $this->barber->total_reviews,
            'latitude' => $this->barber->latitude ?? '',
            'latest_latitude' => $this->barber->latest_latitude ?? '',
            'longitude' => $this->barber->longitude ?? '',
            'latest_longitude' => $this->barber->latest_longitude ?? '',
            'distance' => $this->barber->distance ?? 0.0,
            'services' => isset($this->services) ? BarberServicesOnly::collection($this->services) : [],
        ];
    }
}
