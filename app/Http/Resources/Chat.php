<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Chat extends JsonResource
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
            'order_id' => $this->id,
            'user_id' => $this->user_id ?? '',
            'barber_id' => $this->barber_id ?? '',
            'service' => isset($this->service_timings) ? OrderServiceTime::collection($this->service_timings) : [],
            'transaction_number' => $this->transaction_number ?? '',
            'discount' => $this->discount,
            'amount' => (string)$this->amount ?? '',
            'latitude' => (string)$this->latitude ?? '',
            'longitude' => (string)$this->longitude ?? '',
            'address' => $this->address ?? '',
            'is_order_complete' => $this->is_order_complete,
            'review' => isset($this->review) ? Review::collection($this->review) : [],
            'updated_at' => $this->updated_at ?? ''
        ];
    }
}
