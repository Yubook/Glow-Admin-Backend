<?php

namespace App\Http\Controllers\Admin;

use App\City;
use App\DriverSlot;
use App\Http\Controllers\Controller;
use App\Order;
use App\State;
use Illuminate\Http\Request;
use App\Timing;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Validator;
use function App\Helpers\commonUploadImage;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $admin = User::where('id', $id)->first();
            return view('admin.admin-profile.edit', compact('admin'));
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'password' => 'same:password__confirmation',
                'name' => 'required',
                'email' => 'required',
                'mobile' => 'required|numeric',
                'address' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
                'image' => 'file|mimes:jpeg,jpg,png'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $admin = user::find($id);
            $admin->name = $request->name;
            $admin->email = $request->email;
            $admin->mobile = $request->mobile;
            $admin->latitude = $request->latitude;
            $admin->longitude = $request->longitude;
            $admin->address_line_1 = $request->address;
            if (isset($request->password)) {
                $admin->password = Hash::make($request->password);
            } else {
                $admin->password = $admin->password;
            }

            if ($request->file('image')) {
                $image = $request->file('image');
                $storage_path = "user/profile";
                $userimage = commonUploadImage($storage_path, $image);
                $admin->profile = $userimage;
            } else {
                $admin->profile = $admin->profile;
            }

            $admin->save();

            DB::commit();
            return redirect()->route('home.dashboard')->with('message', 'Admin Profile Update Successfully');
        } catch (\Exception $e) {
            DB::rollback();
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $admin = User::where('role_id', 1)->first();

            return view('admin.admin-profile.show', compact('admin'));
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    public function load_state_city_into_db(Request $request)
    {
        try {
            set_time_limit(0);
            $path = public_path('state.csv');
            $data = [];

            $lines = file($path);
            foreach ($lines as $key => $line) {
                //$values = str_getcsv($line, ',');
                array_push($data, $line);
            }
            //$data = array_map('str_getcsv', file($path));

            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $state = new State();
                    $state->name = $value;
                    $state->save();
                }
            }

            $path1 = public_path('city.csv');
            $data = [];

            $lines = file($path1);
            foreach ($lines as $key => $line1) {
                //  $values = str_getcsv($line1, ',');
                array_push($data, $line1);
            }
            //$data = array_map('str_getcsv', file($path));

            if (!empty($data)) {
                foreach ($data as $key => $value1) {
                    $city = new City();
                    $city->name = $value1;
                    $city->save();
                }
            }
            return "States and Cities imports successfully";
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }
}
