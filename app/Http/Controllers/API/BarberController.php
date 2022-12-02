<?php

namespace App\Http\Controllers\API;

use App\BarberSlot;
use App\BarberTermsPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Http\Resources\BarberPortfolio;
use App\Http\Resources\BarberProfile;
use App\Http\Resources\Order as ResourcesOrder;
use App\Order;
use App\OrderCancleReason;
use App\OrderServiceSlot;
use App\ReviewImage;
use App\User;
use App\UserFavouriteBarber;
use App\UserWallet;
use Carbon\Carbon;
use DateTime;

use function App\Helpers\commonUploadImage;
use function App\Helpers\deleteOldImage;
use function App\Helpers\distance;
use function App\Helpers\sendNotification;

class BarberController extends Controller
{
    public function addPortfolioes(Request $request)
    {
        try {
            $check_validation = array(
                'images' => 'required|array',
                'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            );

            $validator = Validator::make($request->all(), $check_validation);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->statusArr['validation']);
            }

            $limit = (isset($request->limit)) ? $request->limit : 40;
            $response = [];

            $storage_path = "review/image";
            foreach ($request->file('images') as $image) {
                $file_path = commonUploadImage($storage_path, $image);
                $image = new ReviewImage();
                $image->barber_id = Auth::user()->id;
                $image->path = $file_path;
                $image->save();
            }

            $barberPortfolio = ReviewImage::where('user_reviews_id', null)->where('barber_id', Auth::user()->id)->latest()->paginate($limit);
            if (!empty($barberPortfolio)) {
                $response['barberPortfolio'] = BarberPortfolio::collection($barberPortfolio)->response()->getData(true);
            } else {
                $response['barberPortfolio'] = [];
            }

            return $this->sendResponse($response, $message = "Portfolio add successfully");
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function removePortfolio(Request $request)
    {
        try {
            $check_validation = array(
                'portfolio_id' => 'required|integer',
            );

            $validator = Validator::make($request->all(), $check_validation);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->statusArr['validation']);
            }

            $response = [];
            $image = ReviewImage::where(['id' => $request->portfolio_id, 'barber_id' => Auth::user()->id])->first();
            if ($image) {
                deleteOldImage($image->path);
                ReviewImage::where(['id' => $request->portfolio_id, 'barber_id' => Auth::user()->id])->delete();
            }
            return $this->sendResponse($response, $message = "Portfolio deleted successfully");
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function getPortfolio(Request $request)
    {
        try {
            $limit = (isset($request->limit)) ? $request->limit : 40;
            $response = [];
            $barberPortfolio = ReviewImage::where('user_reviews_id', null)->where('barber_id', Auth::user()->id)->latest()->paginate($limit);
            $reviewPortfolio = ReviewImage::where('user_reviews_id', '!=', null)->where('barber_id', Auth::user()->id)->latest()->paginate($limit);
            //dd($reviewPortfolio);

            if (!empty($barberPortfolio)) {
                $response['barberPortfolio'] = BarberPortfolio::collection($barberPortfolio)->response()->getData(true);
            } else {
                $response['barberPortfolio'] = [];
            }

            if (!empty($reviewPortfolio)) {
                $response['reviewPortfolio'] = BarberPortfolio::collection($reviewPortfolio)->response()->getData(true);
            } else {
                $response['reviewPortfolio'] = [];
            }
            return $this->sendResponse($response, $message = "Portfolio get successfully");
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function driverSelectRadius(Request $request)
    {
        try {
            $check_validation = array(
                'min_radius' => 'required|integer',
                'max_radius' => 'required|integer',
            );

            $validator = Validator::make($request->all(), $check_validation);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->statusArr['validation']);
            }

            $response = [];

            $updateRadius = User::where(['id' => Auth::user()->id])->update(['min_radius' => $request->min_radius, 'max_radius' => $request->max_radius]);

            return $this->sendResponse($response, $message = "Successfully update radius search paramters");
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function getDriverProfile(Request $request)
    {
        try {
            $check_validation = array(
                'barber_id' => 'required|integer'
            );

            $validator = Validator::make($request->all(), $check_validation);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->statusArr['validation']);
            }

            $getData = User::with('getReviews', 'barberServices.service', 'policyAndTerm', 'portfolios')->where(['id' => $request->barber_id])->first();

            // give barber favourite or not
            $getData['is_favourite'] = UserFavouriteBarber::where(['barber_id' => $request->barber_id, 'user_id' => Auth::user()->id])->count() ? true : false;
            // give distance of barber to user
            $getData['distance'] = distance($getData->latitude, $getData->longitude, Auth::user()->latest_latitude, Auth::user()->latest_longitude, 'M');

            $response = (object)[];
            if (!empty($getData)) {
                $response = new BarberProfile($getData);
            }
            return $this->sendResponse($response, $message = "Successfully get parlour profile");
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function updateTermsPolicy(Request $request)
    {
        try {
            $check_validation = array(
                'id' => 'required|integer|exists:barber_terms_policy,id',
                'type' => 'required|in:policy,terms',
                'content' => 'required'
            );

            $validator = Validator::make($request->all(), $check_validation);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->statusArr['validation']);
            }

            $getTerms = BarberTermsPolicy::find($request->id);
            $getTerms->barber_id = Auth::user()->id;
            $getTerms->content = $request->content;
            $getTerms->type = $request->type;
            $getTerms->save();

            return $this->sendResponse($getTerms, $message = "Successfully update policy and terms");
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function barberOnOff(Request $request)
    {
        try {
            $check_validation = array(
                'status' => 'required|integer|in:0,1', // 0 = off work , 1 = on work
            );

            $validator = Validator::make($request->all(), $check_validation);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->statusArr['validation']);
            }

            $update = User::find(Auth::user()->id);
            $update->is_available = $request->status;
            $update->save();

            return $this->sendResponse($response = (object)[], $message = "Status change successfully");
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function addBarberTime(Request $request)
    {
        DB::beginTransaction();
        try {
            $check_validation = array(
                'date' => 'required|array',
                'date.*' => 'date_format:d-m-Y',
                'time_id' => 'required|array',
                'time_id.*' => 'integer|exists:timings,id'
            );

            $validator = Validator::make($request->all(), $check_validation);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->statusArr['validation']);
            }

            foreach ($request->date as $dkey => $date) {
                foreach ($request->time_id as $key => $time) {
                    $check = BarberSlot::where(['barber_id' => Auth::user()->id, 'timing_id' => $time, 'date' => date('Y-m-d', strtotime($date))])->first();
                    if (!$check) {
                        $addSlots = new BarberSlot();
                        $addSlots->barber_id = Auth::user()->id;
                        $addSlots->timing_id = $time;
                        $addSlots->date = date('Y-m-d', strtotime($date));
                        $addSlots->save();
                    }
                }
            }

            DB::commit();
            return $this->sendResponse($response = [], "Time slots add successfully");
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function driverLatestLocation(Request $request)
    {
        try {
            $check_validation = array(
                'order_id' => 'required|integer'
            );

            $validator = Validator::make($request->all(), $check_validation);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->statusArr['validation']);
            }

            $response = [];

            $orderData = Order::with('barber:id,role_id,name,email,mobile,latitude,longitude,latest_latitude,latest_longitude')->where(['id' => $request->order_id, 'is_order_complete' => 0])->select('id', 'barber_id', 'user_id', 'latitude', 'longitude', 'address')->first();
            if (!empty($orderData)) {
                $response = $orderData;
                $msg = "Successfully get parlour location";
            } else {
                $response = (object)[];
                $msg = 'Order is not in pending status';
            }
            return $this->sendResponse($response, $message = $msg);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function getBarberBookings(Request $request)
    {
        try {
            $limit = (isset($request->limit)) ? $request->limit : 50;
            $response = [];

            $response['previous'] =  Order::with('review.reviewImages', 'user', 'deleted_service_timings.service', 'deleted_service_timings.slot.time')->whereHas('deleted_service_timings', function ($q) {
                $q->whereHas('slot', function ($query) {
                    $query->where('date', '<', Carbon::now()->format('Y-m-d'));
                });
            })->where('barber_id', Auth::user()->id)->latest()->paginate($limit);

            $response['today'] =  Order::with('review.reviewImages', 'user', 'deleted_service_timings.service', 'deleted_service_timings.slot.time')->whereHas('deleted_service_timings', function ($q) {
                $q->whereHas('slot', function ($query) {
                    $query->where('date', '=', Carbon::now()->format('Y-m-d'));
                });
            })->where('barber_id', Auth::user()->id)->latest()->paginate($limit);

            $response['next'] =  Order::with('review.reviewImages', 'user', 'deleted_service_timings.service', 'deleted_service_timings.slot.time')->whereHas('deleted_service_timings', function ($q) {
                $q->whereHas('slot', function ($query) {
                    $query->where('date', '>', Carbon::now()->format('Y-m-d'));
                });
            })->where('barber_id', Auth::user()->id)->latest()->paginate($limit);

            // $response['previous'] = DB::table('orders')
            //     ->join('driver_slots','driver_slots.id','=','orders.slot_id')
            //     ->where('orders.driver_id','=',$request->driver_id)
            //     ->where('driver_slots.user_id','!=', NULL)
            //     ->where('driver_slots.date','<', Carbon::now()->format('Y-m-d'))->get();

            if (!empty($response['previous']) && $response['previous']->count()) {
                $response['previous'] = ResourcesOrder::collection($response['previous'])->response()->getData(true);
            }

            if (!empty($response['today']) && $response['today']->count()) {
                $response['today'] = ResourcesOrder::collection($response['today'])->response()->getData(true);
            }

            if (!empty($response['next']) && $response['next']->count()) {
                $response['next'] = ResourcesOrder::collection($response['next'])->response()->getData(true);
            }

            return $this->sendResponse($response, $message = "Successfully Get Booking Details");
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function orderComplete(Request $request)
    {
        try {
            $check_validation = array(
                'order_id' => 'required|integer|exists:orders,id'
            );

            $validator = Validator::make($request->all(), $check_validation);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->statusArr['validation']);
            }

            $response = [];

            $order_update = Order::with('user', 'service_timings.service')->where(['id' => $request->order_id, 'barber_id' => Auth::user()->id])->first();
            if (!empty($order_update)) {
                $order_update->is_order_complete = 1;
                $order_update->save();

                $getWallet = UserWallet::where('user_id',  Auth::user()->id)->first();
                if (empty($getWallet)) {
                    $getWallet = new UserWallet();
                    $getWallet->user_id = Auth::user()->id;
                }
                $getWallet->save_amount += $order_update->barber_amount;
                $getWallet->save();
            } else {
                return $this->sendResponse($response, $message = "Unauthorized user");
            }

            $order_date = date('d-m-Y', strtotime($order_update->created_at));
            $title = "Glow order";
            $message_user = 'Your Order of date ' . $order_date . ' completed by parlour ' . Auth::user()->name . ' of order';
            $message_barber = 'Order of date ' . $order_date . ' completed of user ' . $order_update->user->name . ' of order';
            $task_id = 1;
            $send = sendNotification($title, $message_user, $notification_type = 1, $task_id, $order_update->user_id);
            $this->add_notification($order_update->user_id, $title, $message_user, $notification_type = 1, $request->order_id, 1);
            $this->add_notification(Auth::user()->id, $title, $message_barber, $notification_type = 1, $request->order_id, 1);

            return $this->sendResponse($response, $message = "Order Completed Successfully");
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function totalRevenueOfBarber(Request $request)
    {
        try {
            $limit = (isset($request->limit)) ? $request->limit : 15;
            $response = [];

            // $complete_order_ids = Order::where(['barber_id' => Auth::user()->id, 'is_order_complete' => 1])->pluck('id')->toArray();

            // $query = OrderServiceSlot::whereIn('order_id', $complete_order_ids)
            //     ->leftJoin('services', 'services.id', '=', 'order_service_slots.service_id')
            //     ->groupBy('order_service_slots.service_id')
            //     ->selectRaw('sum(order_service_slots.price) as total_revenue, services.name,order_service_slots.updated_at')
            //     ->get();

            $complete_order = Order::with('review.reviewImages', 'user', 'service_timings.service', 'service_timings.slot.time')->where(['barber_id' => Auth::user()->id, 'is_order_complete' => 1])->latest()->paginate($limit);

            if (!empty($complete_order)) {
                $response = ResourcesOrder::collection($complete_order)->response()->getData(true);
                $message = "Successfully Get Revenue of Parlour";
            } else {
                $response = [];
                $message = "No Data Found";
            }

            return $this->sendResponse($response, $message);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function revenueFilterMap(Request $request)
    {
        DB::beginTransaction();
        try {
            $check_validation = array(
                'barber_id' => 'required|integer|exists:users,id',
                'search' => 'string' // 1Week,1Month,3Month,6Month
            );

            $validator = Validator::make($request->all(), $check_validation);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->statusArr['validation']);
            }

            $response = [];

            $completed_orders = $this->completeBarberOrderMonthWise($request->barber_id);
            if (empty($request->search)) {
                $response['yearly'] = $completed_orders;
                $message = "Successfully Get Year Data";
            } else {
                if (!empty($request->search)) {
                    if ($request->search == '6Month' || $request->search == '3Month') {
                        $response['somemonths'] = $this->barberOrderMonthWiseFilter($request->barber_id, $request->search);
                        $message = "Successfully Get Month Wise Filter Data";
                    } elseif ($request->search == '1Month') {
                        $response['1month'] = $this->barberOrderMonthWiseFilter($request->barber_id, $request->search);
                        $message = "Successfully Get Month Wise Filter Data";
                    } elseif ($request->search == '1Week') {
                        $response['1Week'] = $this->barberOrderMonthWiseFilter($request->barber_id, $request->search);
                        $message = "Successfully Get Week Wise Filter Data";
                    }
                }
            }

            DB::commit();
            return $this->sendResponse($response, $message);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    private function completeBarberOrderMonthWise($id)
    {
        $start_date = date("d-m-Y", strtotime('first day of january this year'));
        $end_date =  date("d-m-Y", strtotime('last day of december this year'));

        $result = [];
        $startMonth = date('n', strtotime($start_date));
        $endMonth = date('n', strtotime($end_date));

        $startYear = date('Y', strtotime($start_date));
        $endYear = date('Y', strtotime($end_date));

        if ($startYear == $endYear) {
            for ($month = $startMonth; $month <= $endMonth; $month++) {
                $result[] = $this->getcompletedOrder($month, $startYear, $id);
            }
        } else {

            $gk = 0;
            for ($Y = $startYear; $Y <= $endYear; $Y++) {
                $gk++;


                if ($gk == 1) {
                    $endMonth = 12;
                } else if ($endYear == $Y) {
                    $startMonth = 1;
                    $endMonth = date('n', strtotime($end_date));
                } else {
                    $startMonth = 1;
                    $endMonth = 12;
                }

                for ($M = $startMonth; $M <= $endMonth; $M++) {
                    $result[] = $this->getcompletedOrder($M, $Y, $id);
                }
            }
        }
        return $result;
    }

    private function barberOrderMonthWiseFilter($id, $search)
    {
        if ($search == '6Month') {
            $start_date = Carbon::now()->subMonths(5)->startOfMonth();
            $end_date =  Carbon::now()->startOfMonth();
        } elseif ($search == '3Month') {
            $start_date = Carbon::now()->subMonths(2)->startOfMonth();
            $end_date =  Carbon::now()->startOfMonth();
        } elseif ($search == '1Month') {
            $start_date = Carbon::now()->subMonths(1)->startOfMonth();
            $end_date =  Carbon::now()->subMonths(1)->endOfMonth();
            $year = date('Y', strtotime($start_date));

            $startweekOfYear = $this->weekOfYear(strtotime($start_date));
            $endweekOfYear = $this->weekOfYear(strtotime($end_date));
            //dd($startweekOfYear . '-' . $endweekOfYear);

            for ($startweekOfYear; $startweekOfYear < $endweekOfYear; $startweekOfYear++) {
                $getStartAndEndDate = $this->getStartAndEndDate($startweekOfYear, $year);
                //dd($getStartAndEndDate['start_date']);
                $response[] = $this->getWeekWiseOrder($getStartAndEndDate['start_date'], $getStartAndEndDate['end_date'], $year, $id);
            }
            return $response;
        } elseif ($search == '1Week') {
            $start_date = Carbon::now();
            $end_date = Carbon::now()->subDay(7);
            //$date = Carbon::createFromFormat('Y-m-d', $end_date);
            // dd($end_date);
            $year = date('Y', strtotime($start_date));

            for ($i = 0; $i <= 6; $i++) {
                $response[] = $this->getWeekDayWiseOrder($end_date->copy()->addDays($i), $year, $id);
            }
            return $response;
        }

        $result = [];
        $startMonth = date('n', strtotime($start_date));
        $endMonth = date('n', strtotime($end_date));

        $startYear = date('Y', strtotime($start_date));
        $endYear = date('Y', strtotime($end_date));

        if ($startYear == $endYear) {
            for ($month = $startMonth; $month <= $endMonth; $month++) {
                $result[] = $this->getcompletedOrder($month, $startYear, $id);
            }
        } else {

            $gk = 0;
            for ($Y = $startYear; $Y <= $endYear; $Y++) {
                $gk++;


                if ($gk == 1) {
                    $endMonth = 12;
                } else if ($endYear == $Y) {
                    $startMonth = 1;
                    $endMonth = date('n', strtotime($end_date));
                } else {
                    $startMonth = 1;
                    $endMonth = 12;
                }

                for ($M = $startMonth; $M <= $endMonth; $M++) {
                    $result[] = $this->getcompletedOrder($M, $Y, $id);
                }
            }
        }
        return $result;
    }

    private function weekOfYear($date)
    {
        $weekOfYear = intval(date("W", $date));
        if (date('n', $date) == "1" && $weekOfYear > 51) {
            // It's the last week of the previos year.
            $weekOfYear = 0;
        }
        return $weekOfYear;
    }

    private function getStartAndEndDate($week, $year)
    {
        $dateTime = new DateTime();
        $dateTime->setISODate($year, $week);
        $result['start_date'] = $dateTime->format('Y-m-d H:i:s');
        $dateTime->modify('+6 days');
        $result['end_date'] = $dateTime->format('Y-m-d H:i:s');
        return $result;
    }


    private function getcompletedOrder($month, $year, $id)
    {
        $barber_id = $id;

        $query = DB::table('orders')->whereYear('created_at', '=', $year)
            ->whereMonth('created_at', '=', $month)
            ->where('barber_id', $barber_id)
            ->where('is_order_complete', 1)
            ->orderBy('created_at', 'asc');

        //  $revenue = $query->sum('amount');
        $revenue = $query->count();

        $data['revenue'] =  $revenue;
        $data['month'] =  (int)$month;
        //$month = intval(substr($input_date,4,2));
        $data['month'] = date('F', mktime(0, 0, 0, $data['month'], 10));
        $data['month'] = substr($data['month'], 0, 3);
        $data['year'] =  (int)$year;

        return $data;
    }


    private function getWeekWiseOrder($start_date, $end_date, $year, $id)
    {
        $barber_id = $id;

        $from =  Carbon::createFromFormat('Y-m-d H:i:s', $start_date)->format('d-m-Y');;
        $to =  Carbon::createFromFormat('Y-m-d H:i:s', $end_date)->format('d-m-Y');;

        $query = DB::table('orders')->whereYear('created_at', '=', $year)
            ->whereBetween('created_at', array($start_date, $end_date))
            ->where('barber_id', $barber_id)
            ->where('is_order_complete', 1);

        // $revenue = $query->sum('amount');
        $revenue = $query->count();

        $data['revenue'] =  $revenue;
        $data['start_week'] =  $from;
        $data['end_week'] = $to;
        $data['year'] =  (int)$year;

        return $data;
    }

    private function getWeekDayWiseOrder($date, $year, $id)
    {
        $barber_id = $id;
        $date1 = Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d-m-Y');
        $query = DB::table('orders')->whereYear('created_at', '=', $year)
            ->whereDate('created_at', $date)
            ->where('barber_id', $barber_id)
            ->where('is_order_complete', 1);

        //$revenue = $query->sum('amount');
        $revenue = $query->count();

        $data['revenue'] =  $revenue;
        $data['date'] = $date1;
        $data['year'] =  (int)$year;

        return $data;
    }
}
