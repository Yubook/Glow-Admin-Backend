<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use function App\Helpers\getUploadImage;

class OrderServiceTime extends JsonResource
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
            'service_name' => $this->service->name ?? '',
            'service_price' => $this->price,
            'service_time' => $this->service->time,
            'category' => isset($this->service->category_id) ? new Category($this->service->category) : (object)[],
            'subcategory' => isset($this->service->subcategory_id) ? new Subcategory($this->service->subcategory) : (object)[],
            'image' => isset($this->service->image) ? getUploadImage($this->service->image) : '/storage/no-image.jpg',
            'slot_time' => $this->slot->time->time,
            'slot_date' => $this->slot->date,
        ];
    }
}
