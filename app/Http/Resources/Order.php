<?php

namespace App\Http\Resources;

use App\GroupMst;
use App\Order as AppOrder;
use App\UserFavouriteBarber;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

use function App\Helpers\distance;
use function App\Helpers\getUploadImage;

class Order extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request)
    {
        // Check user type to give opposite user detail
        if (Auth::user()->role_id == 3) {
            // Normal user want barber details
            $barber = $this->barber;
            $user = NULL;
            $user_id = Auth::user()->id;
            $barber_id = $this->barber->id;
        } else if (Auth::user()->role_id == 2) {
            // Barber want normal user details
            $user = $this->user;
            $barber = NULL;
            $barber_id = Auth::user()->id;
            $user_id = $this->user->id;
        }

        // check chat enable or disable
        $now_time = Carbon::now('UTC')->format('H:i');
        $start_time = Carbon::now('UTC')->addMinute(60)->format('H:i');

        $already = AppOrder::with('deleted_service_timings.slot.time')->whereHas('deleted_service_timings', function ($q) use ($now_time, $start_time) {
            $q->whereHas('slot', function ($que) use ($now_time, $start_time) {
                $que->whereHas('time', function ($query) use ($now_time, $start_time) {
                    $query->whereBetween('time', [$now_time, $start_time]);
                });
            });
        })->where('id', $this->id)->first();
        if (!empty($already)) {
            $chat = TRUE;
            $group_info = GroupMst::where([['sender_id', '=', $barber_id], ['receiver_id', '=', $user_id]])->orWhere([['receiver_id', '=', $barber_id], ['sender_id', '=', $user_id]])->first();
            $group_id = $group_info->id;
        } else {
            $chat = False;
            $group_id = null;
        }

        // check user favourite barber
        $already = UserFavouriteBarber::where(['user_id' => $this->user->id, 'barber_id' => $this->barber->id])->first();
        if ($already) {
            $is_barber_favourite = TRUE;
        } else {
            $is_barber_favourite = False;
        }

        // give the distance between order location and barber latest location
        $distance = distance($this->barber->latest_latitude, $this->barber->latest_longitude, $this->latitude, $this->longitude, 'M');
        return [
            'id' => $this->id,
            'user' => isset($user) ? new UserWithoutToken($user) : (object)[],
            'barber' => isset($barber) ? new UserWithoutToken($barber) : (object)[],
            'service' => isset($this->deleted_service_timings) ? OrderServiceTime::collection($this->deleted_service_timings) : [],
            'transaction_number' => $this->transaction_number ?? '',
            'discount' => $this->discount,
            'amount' => (string)$this->amount ?? '',
            'latitude' => (string)$this->latitude ?? '',
            'longitude' => (string)$this->longitude ?? '',
            'address' => $this->address ?? '',
            'is_order_complete' => $this->is_order_complete,
            'review' => isset($this->review) ? Review::collection($this->review) : [],
            'created_at' => $this->created_at ?? '',
            'updated_at' => $this->updated_at ?? '',
            'is_barber_favourite' => $is_barber_favourite,
            'distance' => $distance,
            'chat' => $chat,
            'chat_group_id' => $group_id
        ];
    }
}
