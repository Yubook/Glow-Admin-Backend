<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use function App\Helpers\getUploadImage;

class UserService extends JsonResource
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
            'service_price' => $this->service->price ?? '',
            'service_time'  => $this->service->required_time ?? '',
            'service_image' => $this->service->image ? getUploadImage($this->service->image) : '/storage/no-image.jpg',
            'created_at'  => $this->created_at ?? '',
            'updated_at'  => $this->updated_at ?? '',
            'service_user' => $this->user ? new  UserWithoutToken($this->user) : (object)[],
            'service_driver' => $this->driver ? new  UserWithoutToken($this->driver) : (object)[],
        ];
    }
}
