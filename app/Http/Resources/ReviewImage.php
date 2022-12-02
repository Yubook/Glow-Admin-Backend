<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use function App\Helpers\getUploadImage;

class ReviewImage extends JsonResource
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
            'review_id' => $this->user_reviews_id,
            'image' => $this->path ? getUploadImage($this->path) : '/storage/no-image.jpg'
        ];
    }
}
