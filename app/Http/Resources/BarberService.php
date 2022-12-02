<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BarberService extends JsonResource
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
            'service' => isset($this->service_id) ? new Service($this->service) : (object)[],
            'service_price' => (string)$this->price ?? '',
            //'service_time' => $this->time,
            'barber_details'  => isset($this->barber_id) ?  new UserWithoutToken($this->barber) : (object)[]
        ];
    }
}
