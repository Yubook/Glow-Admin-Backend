<?php

namespace App\Http\Resources;

use App\BarberSlot;
use App\User as AppUser;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

use function App\Helpers\getUploadImage;

class User extends JsonResource
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

        // For check is there is slots are set or not for today
        if ($this->role_id == 2) {
            $availability = BarberSlot::where(['barber_id' => $this->id, 'date' => date('Y-m-d')])->first();
            if (!empty($availability)) {
                $available = 1;
            } else {
                $available = 0;
            }
            $docs = AppUser::with('documents')->find($this->id);
            foreach ($docs->documents as $key => $doc) {
                if ($key == 0) {
                    $document1_name = $doc->document_name;
                    $document1_path = isset($doc->path) ? getUploadImage($doc->path) : '/storage/no-image.jpg';
                } else if ($key == 1) {
                    $document2_name = $doc->document_name;
                    $document2_path = isset($doc->path) ? getUploadImage($doc->path) : '/storage/no-image.jpg';
                }
            }
        } else {
            $available = 0;
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
            'latest_latitude' => $this->latest_latitude ?? '',
            'longitude' => $this->longitude ?? '',
            'latest_longitude' => $this->latest_longitude ?? '',
            'profile' => isset($this->profile) ? getUploadImage($this->profile) : 'storage/no-image.jpg',
            'document1_name' =>  $document1_name ?? '',
            'document1_path' => $document1_path,
            'document2_name' =>  $document2_name ?? '',
            'document2_path' => $document2_path,
            'state' => isset($this->state_id) ? new State($this->state) : (object)[],
            'city' => isset($this->city_id) ? new City($this->city) : (object)[],
            'gender' => $this->gender ?? '',
            'profile_approved' => (int)$this->profile_approved,
            'is_active' => (int)$this->is_active,
            'is_barber_available' => $this->is_available,
            'is_service_added' => (int)$this->is_service_added,
            'is_availability' => $available,
            'min_radius' => (string)$this->min_radius ?? '',
            'max_radius' => (string)$this->max_radius ?? '',
            'average_rating' => $this->average_rating,
            'total_reviews' => $this->total_reviews,
            'token' =>  $this->token ?? ''
        ];
    }
}
