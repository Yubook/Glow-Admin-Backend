<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use function App\Helpers\getUploadImage;

class UserWallet extends JsonResource
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
            'amount' => $this->save_amount ?? '',
            'offer_amount' => $this->offer_amount ?? '',
            'user_id' => $this->user_id ?? '',
            'order' => $this->order ? new Order($this->order) : (object)[],
            'user' => $this->user ? new UserWithoutToken($this->user) : (object)[],
        ];
    }
}
