<?php

namespace App\Http\Controllers\API;

use App\BarberService;
use App\BarberSlot;
use App\GroupMsg;
use App\GroupMst;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\IsChatEnable;
use App\Jobs\GetOrderData;
use App\Order;
use App\OrderServiceSlot;
use App\Service;
use App\User;

use function App\Helpers\sendNotification;

class PaymentController  extends Controller
{
    public function __construct()
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
    }

    public function generateClientSecret(Request $request)
    {
        try {
            return response()->json(['status' => 'success', 'msg' => 'success', 'secret_key' => env('STRIPE_SECRET_KEY')], 200);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function bookAppointment(Request $request)
    {
        try {
            $check_validation = array(
                'user_id' => 'required|integer|exists:users,id',
                'barber_id' => 'required|integer|exists:users,id',
                'slot_ids' => 'required|array',
                'slot_ids.*' => 'integer|exists:barber_slots,id',
                'service_ids' => 'required|array',
                'service_ids.*' => 'integer|exists:services,id',
                'discount' => 'integer',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'address' => 'required|string',
                'amount' => 'required|numeric',
                'card_token' => 'required|string',
                'payment_type' => 'required|integer|in:1,2', // 1 = online card , 2 = Fade coin (Future)
            );

            $validator = Validator::make($request->all(), $check_validation);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->statusArr['validation']);
            }
            DB::beginTransaction();
            // If payment type card via stripe

            //Here We can transfer 5 % of amount to admin stripe account and after remaining amount pay to barber( stripe fees minus in barber) 
            if ($request->payment_type == 1) {
                $amount = !empty($request->amount) ? floatval($request->amount) : 0.00;

                $stripe =  \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

                $customer_create = $this->create_customer();

                $payment = \Stripe\Charge::create([
                    "amount" => $amount,
                    "currency" => env('glow_default_currency'),
                    "source" => $request->card_token,
                    "description" => "Glow Booking Payment"
                ]);

                $status = $payment->status == 'succeeded' ? 'success' : 'fail';

                if ($status == 'success') {
                    // Book all slots
                    foreach ($request->slot_ids as $slot_id) {
                        $bookSlot = BarberSlot::find($slot_id);
                        $bookSlot->user_id = $request->user_id;
                        $bookSlot->is_booked = 1;
                        $bookSlot->save();
                    }

                    $bookOrder = new Order();
                    $bookOrder->user_id = $request->user_id;
                    $bookOrder->barber_id = $request->barber_id;
                    $bookOrder->stripe_key = $payment->id;
                    $bookOrder->transaction_number = $payment->balance_transaction;
                    $bookOrder->latitude = $request->latitude;
                    $bookOrder->longitude = $request->longitude;
                    $bookOrder->address = $request->address;
                    if (isset($request->discount)) {
                        $bookOrder->discount = $request->discount;
                    }
                    $bookOrder->amount = $request->amount / 100;
                    $bookOrder->stripe_response = json_encode($payment);
                    $bookOrder->save();

                    // Send Queue job for retriving order all Data
                    // Also set server side supervisor
                    GetOrderData::dispatch($payment->balance_transaction, $bookOrder);

                    foreach ($request->service_ids as $service_key => $service_id) {
                        $add_order_slot = new OrderServiceSlot();
                        $add_order_slot->order_id = $bookOrder->id;
                        $add_order_slot->service_id = $service_id;
                        $add_order_slot->slot_id = $request->slot_ids[$service_key];
                        $add_order_slot->price = BarberService::where(['service_id' => $service_id, 'barber_id' => $request->barber_id])->value('price');
                        $add_order_slot->save();
                    }

                    // Create a chat between user and barber 
                    $sender_id = $request->barber_id;
                    $receiver_id = $request->user_id;
                    $group = GroupMst::where([['sender_id', '=', $sender_id], ['receiver_id', '=', $receiver_id]])->orWhere([['receiver_id', '=', $sender_id], ['sender_id', '=', $receiver_id]])->first();
                    if (empty($group)) {
                        $create_group = new GroupMst();
                        $create_group->sender_id = $sender_id;
                        $create_group->receiver_id = $receiver_id;
                        $create_group->save();
                    }

                    // Check chat is enabled or not
                    $check = IsChatEnable::where([['user_id', '=',  $receiver_id], ['barber_id', '=', $sender_id]])->first();
                    if (empty($check)) {
                        $create_chat_enable = new IsChatEnable();
                        $create_chat_enable->user_id = $receiver_id;
                        $create_chat_enable->barber_id = $sender_id;
                        $create_chat_enable->save();
                    }

                    $title = "Blow New Booking";
                    $message_barber = 'New order received from ' . Auth::user()->name;
                    $message_user = 'Your booking is confirmed';
                    // $message_user = 'Order book successfully with transaction number ' . $payment->balance_transaction;
                    $task_id = 1;
                    $send = sendNotification($title, $message_barber, $notification_type = 1, $task_id, $request->barber_id);
                    $this->add_notification($request->barber_id, $title, $message_barber, $notification_type = 1, $bookOrder->id, $order_status = 0);

                    $this->add_notification($request->user_id, $title, $message_user, $notification_type = 1, $bookOrder->id, $order_status = 0);
                    $response = $payment;
                    $message_res = "Successfully Payment Received";
                } else {
                    $response = (object)[];
                    $message_res = "Payment Failed";
                }
            }
            DB::commit();
            return $this->sendResponse($response, $message_res);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    private function create_customer()
    {
        $customer = \Stripe\Customer::create(array(
            'name' => Auth::user()->name,
            'description' => 'Fade Booking',
            'email' => Auth::user()->email,
            // 'source' => $token,
            "address" => ["line1" => Auth::user()->address]
        ));

        $response['status']     = 'success';
        $response['message']    = 'Customer added successfully!';
        $response['data']       =  $customer;

        return $response;
    }
}
