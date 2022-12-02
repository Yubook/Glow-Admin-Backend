<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use function App\Helpers\getUploadImage;

class Service extends JsonResource
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
            'name' => $this->name ?? '',
            'time' => $this->time,
            'category' => isset($this->category_id) ? new Category($this->category) : (object)[],
            'subcategory' => isset($this->subcategory_id) ? new Subcategory($this->subcategory) : (object)[],
            'image' => $this->image ? getUploadImage($this->image) : '/storage/no-image.jpg',
            'is_active' => $this->is_active,
        ];
    }
}
