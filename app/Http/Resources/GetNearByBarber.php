<?php

namespace App\Http\Resources;

use App\DriverService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;
use App\Review;
use Illuminate\Support\Facades\DB;
use function App\Helpers\getUploadImage;
use App\Http\Resources\Service as ServiceResource;
use App\Service;
use App\User;

class GetNearByBarber extends JsonResource
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
            'barber_id' => $this->id,
            'phone_code' => $this->phone_code ?? '',
            'mobile' => (string)$this->mobile ?? '',
            'name' => $this->name ?? '',
            'email' => $this->email ?? '',
            'address_line_1' => $this->address_line_1 ?? '',
            'address_line_2' => $this->address_line_2 ?? '',
            'latitude' => $this->latitude ?? '',
            'latest_latitude' => $this->latest_latitude ?? '',
            'longitude' => $this->longitude ?? '',
            'latest_longitude' => $this->latest_longitude ?? '',
            'distance' => $this->distance ?? 0.0,
            'profile' => isset($this->profile) ? getUploadImage($this->profile) : 'storage/no-image.jpg',
            'state' => isset($this->state_id) ? new State($this->state) : (object)[],
            'city' => isset($this->city_id) ? new City($this->city) : (object)[],
            'gender' => $this->gender ?? '',
            'is_favourite' => $this->is_favourite,
            'services' => isset($this->services) ? BarberServicesOnly::collection($this->services) : [],
            'average_rating' => $this->average_rating,
            'total_reviews' => $this->total_reviews,
            'is_available' => $this->is_available
        ];
    }
}
