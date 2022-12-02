<?php

namespace App\Http\Controllers\Admin;

use App\BarberSlot;
use App\Http\Controllers\Controller;
use App\Mail\BarberApproveReject;
use App\Order;
use Illuminate\Http\Request;
use App\Timing;
use App\User;
use Carbon\Carbon;
use DB;
use function App\Helpers\sendNotification;
use function App\Helpers\getUploadImage;
use function App\Helpers\custom_number_format;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $users =  User::where('role_id', 3)->count();
            $barbers = User::where('role_id', 2)->count();
            $admins = User::where('role_id', 1)->get();
            $all_users = User::orderBy('id', 'desc')->get();
            $new_barber_requests = User::with('state', 'city')->where(['role_id' => 2, 'profile_approved' => 0, 'admin_rejected' => 0])->orderBy('id', 'desc')->get();
            $rejected_barber_requests = User::with('state', 'city')->where(['role_id' => 2, 'profile_approved' => 0, 'admin_rejected' => 1])->orderBy('id', 'desc')->get();
            $all_orders = Order::with('user', 'barber', 'service_timings.service', 'service_timings.slot.time')->orderBy('id', 'desc')->get();
            // $all_orders = Order::with('user', 'driver', 'slot.timingSlot', 'service')->orderBy('id', 'desc')->get();

            $total_revenue = Order::where('is_order_complete', 1)->sum('amount');
            $admin_revenue = Order::where('is_order_complete', 1)->sum('admin_fee');
            $barber_revenue = Order::where('is_order_complete', 1)->sum('barber_amount');
            $total_orders_all = Order::count();
            $total_orders_all = custom_number_format($total_orders_all);
            $total_revenue = custom_number_format($total_revenue);
            $users = custom_number_format($users);
            $barbers = custom_number_format($barbers);
            $admin_revenue = custom_number_format($admin_revenue);
            $barber_revenue = custom_number_format($barber_revenue);

            $get_barbers = User::addSelect([
                'Revenue' => Order::selectRaw('sum(barber_amount) as total_revenue')
                    ->whereColumn('barber_id', 'users.id')
                    ->where('is_order_complete', 1)
                    ->groupBy('barber_id')
            ])->where('role_id', '=', 2)->get()->toArray();

            foreach ($get_barbers as $key => $barber) {
                $barber_id = $barber['id'];
                $user = DB::table('orders')->where('barber_id', $barber_id)->distinct('user_id')->count('user_id');
                $total_orders = DB::table('orders')->where('barber_id', $barber_id)->where('is_order_complete', '!=', 2)->count();
                $completed_orders = DB::table('orders')->where('barber_id', $barber_id)->where('is_order_complete', 1)->count();
                $get_barbers[$key]['total_users'] = $user;
                $get_barbers[$key]['total_orders'] = $total_orders;
                $get_barbers[$key]['completed_orders'] = $completed_orders;
            }

            // graph code start
            $count_completed_orders = DB::table('orders')->where('is_order_complete', 1)->whereYear('created_at', date('Y'))->count();
            $count_cancelled = DB::table('orders')->where('is_order_complete', 2)->whereYear('created_at', date('Y'))->count();

            $completed_orders = $this->completebarberOrderMonthWise();
            $completed_order_array = [];
            $completed_month_array = [];
            foreach ($completed_orders as $key => $value) {
                $completed_order_array[] = $value['order_complete'];
                $completed_month_array[] = '"' . $value['month'] . '"';
            }
            $completed_order_data = implode(",", $completed_order_array);
            $completed_month_data = implode(",", $completed_month_array);
            $cancelled_orders = $this->cancelledbarberOrderMonthWise();
            $cancelled_order_array = [];
            $cancelled_month_array = [];
            foreach ($cancelled_orders as $key => $value) {
                $cancelled_order_array[] = $value['order_cancelled'];
                $cancelled_month_array[] = '"' . $value['month'] . '"';
            }
            $cancelled_order_data = implode(",", $cancelled_order_array);
            $cancelled_month_data = implode(",", $cancelled_month_array);
            // graph code end

            $worldData = User::whereIn('role_id', [2, 3])->select('id', 'role_id', 'name', 'latitude', 'longitude', 'latest_latitude', 'latest_longitude')->get(); //users,barbers
            /* foreach ($some_users as $key => $user) {
                $role = ($user->role_id == 3) ? 'User' : 'Barber';
                $worldData[$key]['coords'] = [(float)$user->latitude, (float)$user->longitude];
                // $response[$key]['coords']['lng'] =$user->longitude;
                $worldData[$key]['name'] = $user->name . ' - ' . $role;
            } */

            return view('admin.admin-dashboard', compact('worldData', 'barbers', 'new_barber_requests', 'rejected_barber_requests', 'all_orders', 'get_barbers', 'total_orders_all', 'total_revenue', 'users', 'admins', 'all_users', 'completed_order_data', 'completed_month_data', 'cancelled_order_data', 'cancelled_month_data', 'count_completed_orders', 'count_cancelled', 'admin_revenue', 'barber_revenue'));
            //return view('admin.admin-dashboard', compact('barbers', 'users', 'admins', 'all_users'));
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    public function approvedOrReject(Request $request)
    {
        $barberUpdate = User::find($request->id);
        if ($request->type == 1) {
            $barberUpdate->profile_approved = 1;
            $barberUpdate->is_available = 1;
            $barberUpdate->admin_rejected = 0;
            $msg = 'Parlour Profile Approved Successfully';
        } else {
            $barberUpdate->profile_approved = 0;
            $barberUpdate->is_available = 0;
            $barberUpdate->admin_rejected = 1;
            $msg = 'Parlour Profile Rejected Successfully.';
        }
        $barberUpdate->save();

        // send Mail To Vendor Email
        $mailData = [
            'name' => $barberUpdate->name,
        ];
        Mail::to($barberUpdate->email)->queue(new BarberApproveReject($mailData));

        $content['status'] = 200;
        $content['message'] = $msg;
        return response()->json($content);
    }

    public function ajaxOrderModal(Request $request)
    {
        try {
            $getOrder = Order::with('user', 'barber', 'deleted_service_timings.service', 'deleted_service_timings.slot.time')->where('id', $request['order_id'])->first();
            //dd($getOrder);

            $html = '';
            $html .= '<p class="mb-2">Product id: <span class="text-secondary">#' . $getOrder->id . '</span></p>';
            $html .= '<p class="mb-2">Customer Name: <span class="text-secondary">' . $getOrder->user->name . '</span></p>';
            $html .= '<p class="mb-2">Parlour Name: <span class="text-secondary">' . $getOrder->barber->name . '</span></p>';
            $html .= '<p class="mb-2">Order Date: <span class="text-secondary">'  . date('d-m-Y', strtotime($getOrder->created_at)) . '</span></p>';

            $times = [];
            foreach ($getOrder->deleted_service_timings as $key => $serviceTime) {
                $times[] = $serviceTime->slot->time->time;
            }
            $str = implode(" , ", $times);

            $html .= '<p class="mb-2 mr-1">Slot Time : <span class="text-secondary mr-1">' . $str . '</span></p>';
            $html .= '<div class="table-responsive">
                <table class="table table-centered table-nowrap">
                    <thead>
                        <tr>
                            <th scope="col">Service Image</th>
                            <th scope="col">Service Name</th>
                            <th scope="col">Price</th>
                        </tr>
                    </thead>
                    <tbody>';
            foreach ($getOrder->deleted_service_timings as $SK => $service) {
                $html .= '<tr>
                        <th scope="row">
                            <div>
                                <img src="' . asset(\Storage::url($service->service->image)) . '" alt="' . $service->service->name . '" class="avatar-sm">
                            </div>
                        </th>
                        <td>
                            <div>
                                <h5 class="text-truncate font-size-14">' . $service->service->name . '
                                </h5>
                                <p class="text-muted mb-0">&#163; ' . $service->price . ' x 1</p>
                            </div>
                        </td>
                        <td>&#163; ' . $service->price . '</td>
                    </tr>';
            }
            $html .= '
                    <tr>
                        <td colspan="2">
                            <h6 class="m-0 text-right">Discount:</h6>
                        </td>
                        <td>
                        ' . $getOrder->discount . ' (%)
                        </td>
                    </tr>
                    <tr>
                    <td colspan="2">
                        <h6 class="m-0 text-right">Stripe Fee:</h6>
                    </td>
                    <td>
                    ' . $getOrder->stripe_fee . '
                    </td>
                </tr>
                <tr>
                <td colspan="2">
                    <h6 class="m-0 text-right">Admin Fee:</h6>
                </td>
                <td>
                ' . $getOrder->admin_fee . '
                </td>
            </tr>
            <tr>
            <td colspan="2">
                <h6 class="m-0 text-right">Barber Get:</h6>
            </td>
            <td>
            ' . $getOrder->barber_amount . '
            </td>
        </tr>
                    <tr>
                        <td colspan="2">
                            <h6 class="m-0 text-right">Total:</h6>
                        </td>
                        <td>
                        &#163; ' . $getOrder->amount . '
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>';

            return response()->json($html);
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    private function completebarberOrderMonthWise()
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
                $result[] = $this->getcompletedOrder($month, $startYear);
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
                    $result[] = $this->getcompletedOrder($M, $Y);
                }
            }
        }
        return $result;
    }
    private function getcompletedOrder($month, $year)
    {
        $query = DB::table('orders')->whereYear('created_at', '=', $year)
            ->whereMonth('created_at', '=', $month)
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

    private function cancelledbarberOrderMonthWise()
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
                $result[] = $this->getcancelledOrder($month, $startYear);
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
                    $result[] = $this->getcancelledOrder($M, $Y);
                }
            }
        }
        return $result;
    }
    private function getcancelledOrder($month, $year)
    {
        $query = DB::table('orders')->whereYear('created_at', '=', $year)
            ->whereMonth('created_at', '=', $month)
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

    public function worldChart()
    {
        DB::beginTransaction();
        try {
            $users = User::whereIn('role_id', [2, 3])->select('id', 'role_id', 'name', 'latitude', 'longitude', 'latest_latitude', 'latest_longitude')->get(); //users,drivers
            /*  foreach ($users as $key => $user) {
                $role = ($user->role_id == 3) ? 'User' : 'Driver';
                $response[$key]['coords'] = [(float)$user->latitude, (float)$user->longitude];
                // $response[$key]['coords']['lng'] =$user->longitude;
                $response[$key]['name'] = $user->name . ' - ' . $role;
            } */

            //dd($response);
            DB::commit();
            return response()->json($users);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function saveToken(Request $request)
    {
        if (isset($request)) {
            User::where('id', Auth::user()->id)->update(['device_token' => $request->token]);
        }
        return response()->noContent();
    }

    public function notify()
    {
        $title = "Glow";
        $message = 'new new new';
        $task_id = 1;
        $send = sendNotification($title, $message, $notification_type = 1, $task_id, 2);
        dd('done');
    }
}
