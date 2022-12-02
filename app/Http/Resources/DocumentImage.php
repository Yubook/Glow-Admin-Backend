<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use function App\Helpers\getUploadImage;

class DocumentImage extends JsonResource
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
            'id' => $this->galleryable_id ?? 0,
            'document_name' => $this->document_name ?? '',
            'path' => isset($this->path) ? getUploadImage($this->path) : '/storage/no-image.jpg'
        ];
    }
}
