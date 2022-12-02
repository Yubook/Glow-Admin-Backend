<?php

namespace App\Http\Controllers\API;

use App\BarberService;
use App\BarberSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Http\Resources\Order as ResourcesOrder;
use App\Order;
use App\OrderCancleReason;
use App\OrderServiceSlot;
use App\Timing;
use App\UserWallet;
use Carbon\Carbon;

use function App\Helpers\commonUploadImage;
use function App\Helpers\sendNotification;

class OrderController extends Controller
{
    public function checkBookAppointmentSlots(Request $request)
    {
        try {
            $check_validation = array(
                'barber_id' => 'required|integer|exists:users,id',
                'total_time' => 'required|integer', // in minutes
                'date' => 'required|date_format:Y-m-d'
            );

            $validator = Validator::make($request->all(), $check_validation);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->statusArr['validation']);
            }

            $knowTimeDiff = Timing::where('is_active', 1)->first();
            $needSlots = (int)round($request->total_time / $knowTimeDiff->time_diff);

            $response['slots_required'] = $needSlots;
            $query = BarberSlot::with('time')->where(['barber_id' => $request->barber_id, 'date' => $request->date, 'is_active' => 1]);
            if ($needSlots <= 1) {
                $response['available_slots'] = $query->get();
            } else {
                $data = $query->get();
                $availableSlots = [];
                $tempSlots = [];
                foreach ($data as $key => $slot) {
                    if (!$slot->is_booked) {
                        array_push($tempSlots, $slot);
                    } else {
                        if ($needSlots <= count($tempSlots)) {
                            $availableSlots = array_merge($availableSlots, $tempSlots);
                        }
                        $tempSlots = [];
                    }
                }
                if ($needSlots <= count($tempSlots)) {
                    $availableSlots = array_merge($availableSlots, $tempSlots);
                    $tempSlots = [];
                }
                $response['available_slots'] = $availableSlots;
            }

            return $this->sendResponse($response, "Available slots get successfully");
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function cancelOrder(Request $request)
    {
        DB::beginTransaction();
        try {
            $check_validation = array(
                'user_id' => 'required|integer|exists:users,id',
                'barber_id' => 'required|integer|exists:users,id',
                'order_id' => 'required|integer|exists:orders,id',
                'cancle_by' => 'required|in:user,barber'
            );
            if ($request->cancle_by == 'barber') {
                $check_validation['reason'] = 'required|max:200';
            }

            $validator = Validator::make($request->all(), $check_validation);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->statusArr['validation']);
            }

            $response = [];

            if ($request->cancle_by == 'user') {
                $cancle_by = $request->user_id;
            } else {
                $cancle_by = $request->barber_id;
                // If barber reject order then required reason
                $reasonAdd = new OrderCancleReason();
                $reasonAdd->user_id = $request->user_id;
                $reasonAdd->barber_id = $request->barber_id;
                $reasonAdd->cancle_by = $cancle_by;
                $reasonAdd->order_id = $request->order_id;
                $reasonAdd->reason = $request->reason;
                $reasonAdd->save();
            }

            $orderCancle = Order::where(['id' => $request->order_id, 'barber_id' => $request->barber_id, 'user_id' => $request->user_id])->first();
            $orderCancle->is_order_complete = 2;

            // Here we add only net amount which is (amount-stripe_fee) 
            $total_amount =  (floatval($orderCancle->net_order_price) / (1 - (floatval($orderCancle->discount) / 100)));
            $discount_amount = (floatval($total_amount) * floatval($orderCancle->discount)) / 100;

            //check user old wallet balance
            $oldWalletBalance = UserWallet::where('user_id', $request->user_id)->first();
            if ($oldWalletBalance) {
                $oldWalletBalance->save_amount = floatval($orderCancle->total_amount) + floatval($oldWalletBalance->save_amount);
                $oldWalletBalance->offer_amount = floatval($oldWalletBalance->offer_amount) + floatval($discount_amount);
                $oldWalletBalance->save();
            } else {
                $addAmountToWallet = new UserWallet();
                $addAmountToWallet->user_id = $request->user_id;
                $addAmountToWallet->save_amount = $orderCancle->amount;
                $addAmountToWallet->offer_amount = floatval($discount_amount);
                $addAmountToWallet->order_id = $request->order_id;
                $addAmountToWallet->save();
            }
            $orderCancle->save();

            $getBookedSlots = OrderServiceSlot::where('order_id', $request->order_id)->pluck('slot_id')->toArray();
            foreach ($getBookedSlots as $slot_id) {
                // free Booked slots for next users
                BarberSlot::where('id', $slot_id)->update(['user_id' => null, 'is_booked' => 0]);
            }
            // Delete Order slot for remove duplication of slots in next order
            OrderServiceSlot::where('order_id', $request->order_id)->delete();

            $title = "Fade";

            $task_id = 1;
            if ($request->cancle_by == 'user') {
                $message_barber = 'Order cancle by user ' .  Auth::user()->name;
                $send = sendNotification($title, $message_barber, $notification_type = 1, $task_id, $request->barber_id);
                $msg = "Successfully Cancle Order by User";
                $this->add_notification($request->barber_id, $title, $message_barber, $type = 1, $request->order_id, 2);
            } else {
                $message_user = 'Order cancle by parlour ' . Auth::user()->name . ' for this reason ' . $request->reason;
                $send = sendNotification($title, $message_user, $notification_type = 1, $task_id, $request->user_id);
                $msg = "Successfully Cancle Order by Parlour";
                $this->add_notification($request->user_id, $title, $message_user, $type = 1, $request->order_id, 2);
            }

            DB::commit();
            return $this->sendResponse($response, $msg);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function getUserPaymentHistory(Request $request)
    {
        try {
            $limit = (isset($request->limit)) ? $request->limit : 40;
            $response = [];

            $query = Order::with('review.reviewImages', 'barber', 'service_timings.service', 'service_timings.slot.time')->where('user_id', Auth::user()->id);
            $orders = $query->latest()->paginate($limit);

            $totalExpense = Order::where('is_order_complete', 1);
            $response['totalExpense'] = $totalExpense->where('user_id', Auth::user()->id)->sum('amount');

            if (!empty($orders)) {
                $response['order'] = ResourcesOrder::collection($orders)->response()->getData(true);
                $message = "Successfully get payment history of user";
            } else {
                $response['order'] = (object)[];
                $message = "No Data Found";
            }

            return $this->sendResponse($response, $message);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function getBarberPaymentHistory(Request $request)
    {
        try {
            $limit = (isset($request->limit)) ? $request->limit : 40;
            $response = [];

            $query = Order::with('review.reviewImages', 'user', 'service_timings.service', 'service_timings.slot.time')->where('barber_id', Auth::user()->id);
            $orders = $query->latest()->paginate($limit);

            $totalExpense = Order::where('is_order_complete', 1);
            $response['totalExpense'] = $totalExpense->where('barber_id', Auth::user()->id)->sum('amount');

            if (!empty($orders)) {
                $response['order'] = ResourcesOrder::collection($orders)->response()->getData(true);
                $message = "Successfully get payment history of parlour";
            } else {
                $response['order'] = (object)[];
                $message = "No Data Found";
            }

            return $this->sendResponse($response, $message);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }
}
