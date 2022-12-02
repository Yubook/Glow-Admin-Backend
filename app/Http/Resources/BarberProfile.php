<?php

namespace App\Http\Resources;

use App\GroupMst;
use App\Order;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

use function App\Helpers\getUploadImage;

class BarberProfile extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request)
    {
        // check chat enable or disable
        $now_time = Carbon::now('UTC')->format('H:i');
        $start_time = Carbon::now('UTC')->addMinute(60)->format('H:i');

        $already = Order::with('deleted_service_timings.slot.time')->whereHas('deleted_service_timings', function ($q) use ($now_time, $start_time) {
            $q->whereHas('slot', function ($que) use ($now_time, $start_time) {
                $que->whereHas('time', function ($query) use ($now_time, $start_time) {
                    $query->whereBetween('time', [$now_time, $start_time]);
                });
            });
        })->where(['user_id' => Auth::user()->id, 'barber_id' => $request->barber_id, 'is_order_complete' => 0])->first();
        if (!empty($already)) {
            $chat = TRUE;
            $group_info = GroupMst::where([['sender_id', '=', $request->barber_id], ['receiver_id', '=', Auth::user()->id]])->orWhere([['receiver_id', '=', $request->barber_id], ['sender_id', '=', Auth::user()->id]])->first();
            $group_id = $group_info->id;
        } else {
            $chat = False;
            $group_id = null;
        }

        return [
            'barber_id' => $this->id,
            'phone_code' => $this->phone_code ?? '',
            'mobile' => (string)$this->mobile ?? '',
            'name' => $this->name ?? '',
            'profile' => isset($this->profile) ? getUploadImage($this->profile) : 'storage/no-image.jpg',
            'gender' => $this->gender ?? '',
            'average_rating' => $this->average_rating ?? 0,
            'total_reviews' => $this->total_reviews ?? 0,
            'fivestar' => $this->fivestar ?? 0,
            'fourstar' => $this->fourstar ?? 0,
            'threestar' => $this->threestar ?? 0,
            'twostar' => $this->twostar ?? 0,
            'onestar' => $this->onestar ?? 0,
            'services' => isset($this->barberServices) ? BarberServicesOnly::collection($this->barberServices) : [],
            'policy_and_term' => isset($this->policyAndTerm) ? $this->policyAndTerm : [],
            'portfolios' => isset($this->portfolios) ?  BarberPortfolio::collection($this->portfolios) : [],
            'reviews' => isset($this->getReviews) ? Review::collection($this->getReviews) : [],
            'is_favourite' => $this->is_favourite,
            'distance' => $this->distance ?? 0.0,
            'chat' => $chat,
            'chat_group_id' => $group_id
        ];
    }
}
