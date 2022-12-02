<?php

namespace App\Http\Resources;

use App\BarberSlot;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use function App\Helpers\getUploadImage;

class BarberOnlyProfile extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request)
    {
        $document1_name = '';
        $document2_name = '';
        $document1_path = '';
        $document2_path = '';
        foreach ($this->documents as $key => $doc) {
            if ($key == 0) {
                $document1_name = $doc->document_name;
                $document1_path = isset($doc->path) ? getUploadImage($doc->path) : '/storage/no-image.jpg';
            } else if ($key == 1) {
                $document2_name = $doc->document_name;
                $document2_path = isset($doc->path) ? getUploadImage($doc->path) : '/storage/no-image.jpg';
            }
        }
        return [
            'id' => $this->id,
            'role_id' => (int)$this->role_id,
            'phone_code' => $this->phone_code ?? '',
            'mobile' => (string)$this->mobile ?? '',
            'name' => $this->name ?? '',
            'email' => $this->email ?? '',
            'address_line_1' => $this->address_line_1 ?? '',
            'address_line_2' => $this->address_line_2 ?? '',
            'postal_code' => $this->postal_code ?? '',
            'latitude' => $this->latitude ?? '',
            'longitude' => $this->longitude ?? '',
            'profile' => isset($this->profile) ? getUploadImage($this->profile) : 'storage/no-image.jpg',
            'document1_name' =>  $document1_name ?? '',
            'document1_path' => $document1_path,
            'document2_name' =>  $document2_name ?? '',
            'document2_path' => $document2_path,
            'state' => isset($this->state_id) ? new State($this->state) : (object)[],
            'city' => isset($this->city_id) ? new City($this->city) : (object)[],
            'gender' => $this->gender ?? ''
        ];
    }
}
