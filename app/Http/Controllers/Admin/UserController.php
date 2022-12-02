<?php

namespace App\Http\Controllers\Admin;

use App\BarberSlot;
use App\Http\Controllers\Controller;
use App\Order;
use Illuminate\Http\Request;
use App\Timing;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $users = User::where('role_id', 3)->orderBy('id', 'desc')->get();
            return view('admin.user.index', compact('users'));
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::find(request('ids'));
            $user->delete();
            DB::commit();
            return response()->noContent();
        } catch (\Exception $e) {
            DB::rollback();
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    public function switchUpdate(Request $request)
    {
        try {
            $user = User::find($request->ids);
            if (empty($user->is_active)) {
                $user->is_active = 1;
            } else {
                $user->is_active = 0;
            }
            $user->save();
            return response()->noContent();
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $userProfile = User::with('wallet', 'city', 'state')->find($id);
            $user_id = $id;

            $total_orders = Order::with('user', 'barber', 'deleted_service_timings.service', 'deleted_service_timings.slot.time')->where('user_id', $user_id)->orderBy('id', 'desc')->get();

            $count_completed_orders = DB::table('orders')->where('user_id', $user_id)->where('is_order_complete', 1)->whereYear('created_at', date('Y'))->count();
            $count_cancelled = DB::table('orders')->where('user_id', $user_id)->where('is_order_complete', 2)->whereYear('created_at', date('Y'))->count();

            $completed_orders = $this->completeUserOrderMonthWise($user_id);
            $completed_order_array = [];
            $completed_month_array = [];
            foreach ($completed_orders as $key => $value) {
                $completed_order_array[] = $value['order_complete'];
                $completed_month_array[] = '"' . $value['month'] . '"';
            }
            $completed_order_data = implode(",", $completed_order_array);
            $completed_month_data = implode(",", $completed_month_array);

            $cancelled_orders = $this->cancelledUserOrderMonthWise($user_id);
            $cancelled_order_array = [];
            $cancelled_month_array = [];
            foreach ($cancelled_orders as $key => $value) {
                $cancelled_order_array[] = $value['order_cancelled'];
                $cancelled_month_array[] = '"' . $value['month'] . '"';
            }
            $cancelled_order_data = implode(",", $cancelled_order_array);
            $cancelled_month_data = implode(",", $cancelled_month_array);

            return view('admin.user.show', compact('userProfile',  'total_orders', 'count_completed_orders', 'count_cancelled', 'completed_order_data', 'completed_month_data', 'cancelled_order_data', 'cancelled_month_data'));
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    private function completeUserOrderMonthWise($id)
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
    private function getcompletedOrder($month, $year, $id)
    {
        $user_id = $id;

        $query = DB::table('orders')->whereYear('created_at', '=', $year)
            ->whereMonth('created_at', '=', $month)
            ->where('user_id', $user_id)
            ->where('is_order_complete', 1)
            ->orderBy('created_at', 'asc');

        $order_complete = $query->count();

        $data['order_complete'] =  $order_complete;
        $data['month'] =  (int)$month;
        //$month = intval(substr($input_date,4,2));
        $data['month'] = date('F', mktime(0, 0, 0, $data['month'], 10));
        $data['month'] = substr($data['month'], 0, 3);
        $data['year'] =  (int)$year;

        return $data;
    }

    private function cancelledUserOrderMonthWise($id)
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
                $result[] = $this->getcancelledOrder($month, $startYear, $id);
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
                    $result[] = $this->getcancelledOrder($M, $Y, $id);
                }
            }
        }
        return $result;
    }
    private function getcancelledOrder($month, $year, $id)
    {
        $user_id = $id;
        $query = DB::table('orders')->whereYear('created_at', '=', $year)
            ->whereMonth('created_at', '=', $month)
            ->where('user_id', $user_id)
            ->where('is_order_complete', 2)
            ->orderBy('created_at', 'asc');

        $order_cancelled = $query->count();

        $data['order_cancelled'] =  $order_cancelled;
        $data['month'] =  (int)$month;
        //$month = intval(substr($input_date,4,2));
        $data['month'] = date('F', mktime(0, 0, 0, $data['month'], 10));
        $data['month'] = substr($data['month'], 0, 3);
        $data['year'] =  (int)$year;

        return $data;
    }
}
