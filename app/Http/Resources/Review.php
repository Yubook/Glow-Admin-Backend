<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use function App\Helpers\getUploadImage;

class Review extends JsonResource
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
            'order_id' => (string)$this->order_id ?? '',
            'user_id' => $this->user_id,
            'barber_id' => $this->barber_id,
            'service' => $this->service ?? 0,
            'hygiene' => $this->hygiene ?? 0,
            'value' => $this->value ?? 0,
            'review_images' => isset($this->reviewImages) ? ReviewImage::collection($this->reviewImages) : [],
            //'updated_date' => $this->updated_at->diffForHumans()
        ];
    }
}
