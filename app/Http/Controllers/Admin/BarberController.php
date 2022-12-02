<?php

namespace App\Http\Controllers\Admin;

use App\BarberSlot;
use App\BarberTermsPolicy;
use App\City;
use App\Country;
use App\Document;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBarberRequest;
use App\Http\Requests\UpdateBarberRequest;
use App\Order;
use App\State;
use Illuminate\Http\Request;
use App\Timing;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Validator;

use function App\Helpers\commonUploadImage;
use function App\Helpers\custom_number_format;
use function App\Helpers\deleteOldImage;

class BarberController extends Controller
{
    public function index()
    {
        try {
            $barbers = User::with('wallet', 'state', 'city')->where('role_id', 2)->count();

            $allBarbers = User::with('wallet', 'state', 'city')->addSelect([
                'Revenue' => Order::selectRaw('sum(barber_amount) as total_revenue')
                    ->whereColumn('barber_id', 'users.id')
                    ->where('is_order_complete', 1)
                    ->groupBy('barber_id')
            ])->where('role_id', '=', 2)->get()->toArray();

            foreach ($allBarbers as $key => $barber) {
                $barber_id = $barber['id'];
                $users = DB::table('orders')->where('barber_id', $barber_id)->distinct('user_id')->count('user_id');
                $total_orders = DB::table('orders')->where('barber_id', $barber_id)->where('is_order_complete', '!=', 2)->count();
                $completed_orders = DB::table('orders')->where('barber_id', $barber_id)->where('is_order_complete', 1)->count();
                $allBarbers[$key]['total_users'] = custom_number_format($users);
                $allBarbers[$key]['total_orders'] = custom_number_format($total_orders);
                $allBarbers[$key]['completed_orders'] = custom_number_format($completed_orders);
                $allBarbers[$key]['Revenue_new'] = custom_number_format($barber['Revenue']);
            }
            //dd($allBarbers);
            return view('admin.barber.index', compact('barbers', 'allBarbers'));
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    public function create()
    {
        try {
            $countries = Country::where('active', 1)->get();
            // $cities = City::where('active', 1)->get();
            return view('admin.barber.create', compact('countries'));
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    public function store(StoreBarberRequest $request)
    {
        DB::beginTransaction();
        try {
            $new_barber = new User();
            $new_barber->role_id = 2;
            $new_barber->name = $request->name;
            $new_barber->email = $request->email;
            $new_barber->mobile = $request->mobile;
            $new_barber->latitude = $request->latitude;
            $new_barber->longitude = $request->longitude;
            $new_barber->address_line_1 = $request->address_line_1;
            $new_barber->address_line_2 = $request->address_line_2;
            $new_barber->postal_code = $request->postal_code;
            $new_barber->country_id = $request->country;
            // $new_barber->state_id = $request->state;
            $new_barber->city_id = $request->city;
            $new_barber->gender = $request->gender;
            $new_barber->profile_approved = 1;

            if ($request->file('profile')) {
                $image = $request->file('profile');
                $storage_path = "user/profile";
                $userimage = commonUploadImage($storage_path, $image);
                $new_barber->profile = $userimage;
            }
            $new_barber->save();


            if ($request->file('document_1')) {
                $image1 = $request->file('document_1');
                $storage_path = "barber/document";
                $document1 = commonUploadImage($storage_path, $image1);
                $add_image = new Document();
                $add_image->galleryable_id = $new_barber->id;
                $add_image->document_name = $request->document_1_name;
                $add_image->path = $document1;
                $new_barber->documents()->save($add_image);
            }
            if ($request->file('document_2')) {
                $image2 = $request->file('document_2');
                $storage_path = "barber/document";
                $document2 = commonUploadImage($storage_path, $image2);
                $add_image = new Document();
                $add_image->galleryable_id = $new_barber->id;
                $add_image->document_name = $request->document_2_name;
                $add_image->path = $document2;
                $new_barber->documents()->save($add_image);
            }

            //If new user is barber then add default policy and terms

            // $addPolicy = new BarberTermsPolicy();
            // $addPolicy->barber_id =  $new_barber->id;
            // $addPolicy->type = "policy";
            // $addPolicy->content = "Please add your privacy policy for users";
            // $addPolicy->save();

            $addTerms = new BarberTermsPolicy();
            $addTerms->barber_id =  $new_barber->id;
            $addTerms->type = "terms";
            $addTerms->content = "Please add your terms and condition for users";
            $addTerms->save();

            DB::commit();
            return redirect()->route('barbers.index')->with('message', trans('message.barber.create'));
        } catch (\Exception $e) {
            DB::rollback();
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $barber = User::with('documents')->find($id);
            $countries = Country::where('active', 1)->get();
            // $states = State::where('active', 1)->get();
            //$cities = City::where('active', 1)->get();
            return view('admin.barber.edit', compact('barber', 'countries'));
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    public function update(UpdateBarberRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $new_barber = User::find($id);
            $new_barber->role_id = 2;
            $new_barber->name = $request->name;
            $new_barber->email = $request->email;
            $new_barber->mobile = $request->mobile;
            $new_barber->latitude = $request->latitude;
            $new_barber->longitude = $request->longitude;
            $new_barber->address_line_1 = $request->address_line_1;
            $new_barber->address_line_2 = $request->address_line_2;
            $new_barber->postal_code = $request->postal_code;
            $new_barber->country_id = $request->country;
            //$new_barber->state_id = $request->state;
            $new_barber->city_id = $request->city;
            $new_barber->gender = $request->gender;

            if ($request->file('profile')) {
                deleteOldImage($new_barber->profile);
                $image = $request->file('profile');
                $storage_path = "user/profile";
                $userimage = commonUploadImage($storage_path, $image);
                $new_barber->profile = $userimage;
            }
            $new_barber->save();

            if ($request->file('document_1')) {
                $image1 = $request->file('document_1');
                $storage_path = "barber/document";
                $document1 = commonUploadImage($storage_path, $image1);
                $add_image = new Document();
                $add_image->galleryable_id = $new_barber->id;
                $add_image->document_name = $request->document_1_name;
                $add_image->path = $document1;
                $new_barber->documents()->save($add_image);
            }
            if ($request->file('document_2')) {
                $image2 = $request->file('document_2');
                $storage_path = "barber/document";
                $document2 = commonUploadImage($storage_path, $image2);
                $add_image = new Document();
                $add_image->galleryable_id = $new_barber->id;
                $add_image->document_name = $request->document_2_name;
                $add_image->path = $document2;
                $new_barber->documents()->save($add_image);
            }

            DB::commit();
            return redirect()->route('barbers.index')->with('message', trans('message.barber.update'));
        } catch (\Exception $e) {
            DB::rollback();
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
            $barberProfile = User::with('wallet')->find($id);
            $timingslots = BarberSlot::with('time')->where('barber_id', $id)->orderBy('id', 'desc')->get();

            $barber_id = $id;
            $users = DB::table('orders')->where('barber_id', $barber_id)->distinct('user_id')->count('user_id');

            $total_orders = Order::with('user', 'barber', 'service_timings.service', 'service_timings.slot.time')->where('barber_id', $barber_id)->orderBy('id', 'desc')->get();
            $total_users = $users;

            $count_completed_orders = DB::table('orders')->where('barber_id', $barber_id)->where('is_order_complete', 1)->whereYear('created_at', date('Y'))->count();
            $count_cancel = DB::table('orders')->where('barber_id', $barber_id)->where('is_order_complete', 2)->whereYear('created_at', date('Y'))->count();

            $completed_orders = $this->completebarberOrderMonthWise($barber_id);
            $completed_order_array = [];
            $completed_month_array = [];
            foreach ($completed_orders as $key => $value) {
                $completed_order_array[] = $value['order_complete'];
                $completed_month_array[] = '"' . $value['month'] . '"';
            }
            $completed_order_data = implode(",", $completed_order_array);
            $completed_month_data = implode(",", $completed_month_array);

            $cancel_orders = $this->cancelbarberOrderMonthWise($barber_id);
            $cancel_order_array = [];
            $cancel_month_array = [];
            foreach ($cancel_orders as $key => $value) {
                $cancel_order_array[] = $value['order_cancel'];
                $cancel_month_array[] = '"' . $value['month'] . '"';
            }
            $cancel_order_data = implode(",", $cancel_order_array);
            $cancel_month_data = implode(",", $cancel_month_array);
            // dd($total_orders);
            return view('admin.barber.show', compact('barberProfile', 'timingslots', 'total_users', 'total_orders', 'count_completed_orders', 'count_cancel', 'completed_order_data', 'completed_month_data', 'cancel_order_data', 'cancel_month_data'));
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    // Add New Function out of resourse
    public function slotSwitchUpdate(Request $request)
    {
        try {
            $service = BarberSlot::find($request->ids);
            if (empty($service->is_active)) {
                $service->is_active = 1;
            } else {
                $service->is_active = 0;
            }
            $service->save();
            return response()->noContent();
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    // Add Slot for Barber(Driver)
    public function addBarberSlot(Request $request)
    {
        try {
            $drivers = User::where('role_id', 2)->where('is_active', 1)->get();
            $timeSlots = Timing::where('is_active', 1)->get();
            return view('admin.driver.addslotsBook', compact('drivers', 'timeSlots'));
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    // Book Slot for Barber(Driver)
    public function bookBarberSlot(Request $request)
    {
        try {
            $check_validation = array(
                'date' => 'required|string',
                'driver_id' => 'required|array',
                'timeslot' => 'required|array',
            );

            $validator = Validator::make($request->all(), $check_validation);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $date_string = $request->date;
            $dates = explode(",", $date_string);
            foreach ($request->driver_id as $singleDriver) {
                foreach ($dates as $singleDate) {
                    foreach ($request->timeslot as $key => $val) {
                        $date = Carbon::createFromFormat('m/d/Y', $singleDate)->format('Y-m-d');
                        $findAlready = BarberSlot::where(['driver_id' => $singleDriver, 'timing_id' => $val, 'date' => $date])->first();
                        if (!$findAlready) {
                            $add_slots = new BarberSlot();
                            $add_slots->driver_id = $singleDriver;
                            $add_slots->date = $date;
                            $add_slots->timing_id = $val;
                            $add_slots->save();
                        }
                    }
                }
            }

            return redirect()->back()->with('message', 'Slots Created Successfully');
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    public function getCities(Request $request)
    {
        $cities = City::where('country_id', $request->country_id)->get();
        $html = '';
        if (count($cities) > 0) {
            $html .= '<option value="">Choose City</option>';
            foreach ($cities as $value) {
                if ($request->old_city_id == $value->id) {
                    $html .= '<option value="' . $value->id . '" selected="">' . $value->name . '</option>';
                } else {
                    $html .= '<option value="' . $value->id . '">' . $value->name . '</option>';
                }
            }
        } else {
            $html .= '<option value="">No City Found</option>';
        }

        echo $html;
    }

    // All Driver Details Card Views
    /*    public function getAllDrivers(Request $request)
    {
        DB::beginTransaction();
        try {

              //  $drivers = DB::table('users')
              //  ->join('orders', 'users.id', '=', 'orders.driver_id')
              //  ->where('users.role_id', '=', 2)
              //  ->select('users.*', 'orders.*', \DB::raw('SUM(orders.amount) as revenue'))
              //  ->get(); 

            $drivers = User::addSelect([
                'Revenue' => Order::selectRaw('sum(amount) as total_revenue')
                    ->whereColumn('driver_id', 'users.id')
                    ->where('is_order_complete', 1)
                    ->groupBy('driver_id')
            ])->where('role_id', '=', 2)->get()->toArray();

            foreach ($drivers as $key => $driver) {
                $driver_id = $driver['id'];
                $users = DB::table('orders')->where('driver_id', $driver_id)->distinct('user_id')->count('user_id');
                $total_orders = DB::table('orders')->where('driver_id', $driver_id)->where('is_order_complete', '!=', 2)->count();
                $completed_orders = DB::table('orders')->where('driver_id', $driver_id)->where('is_order_complete', 1)->count();
                $drivers[$key]['total_users'] = $users;
                $drivers[$key]['total_orders'] = $total_orders;
                $drivers[$key]['completed_orders'] = $completed_orders;
            }

            dd($drivers);
            return view('admin.driver.addslotsBook', compact('drivers', 'timeSlots'));

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    } */

    private function completebarberOrderMonthWise($id)
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
        $barber_id = $id;

        $query = DB::table('orders')->whereYear('created_at', '=', $year)
            ->whereMonth('created_at', '=', $month)
            ->where('barber_id', $barber_id)
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

    private function cancelbarberOrderMonthWise($id)
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
                $result[] = $this->getcancelOrder($month, $startYear, $id);
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
                    $result[] = $this->getcancelOrder($M, $Y, $id);
                }
            }
        }
        return $result;
    }
    private function getcancelOrder($month, $year, $id)
    {
        $barber_id = $id;

        $query = DB::table('orders')->whereYear('created_at', '=', $year)
            ->whereMonth('created_at', '=', $month)
            ->where('barber_id', $barber_id)
            ->where('is_order_complete', 2)
            ->orderBy('created_at', 'asc');

        $order_cancel = $query->count();

        $data['order_cancel'] =  $order_cancel;
        $data['month'] =  (int)$month;
        //$month = intval(substr($input_date,4,2));
        $data['month'] = date('F', mktime(0, 0, 0, $data['month'], 10));
        $data['month'] = substr($data['month'], 0, 3);
        $data['year'] =  (int)$year;

        return $data;
    }
}
