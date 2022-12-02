<?php

namespace App\Http\Controllers\API;

use App\BarberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Http\Resources\BarberService as ResourcesBarberService;
use App\Order;
use App\Service;
use App\User;

use function App\Helpers\commonUploadImage;
use function App\Helpers\sendNotification;

class ServiceController extends Controller
{
    public function addBarberService(Request $request)
    {
        try {
            $check_validation = array(
                'service_id' => 'required|array',
                'price' => 'required|array',
            );

            if ($request->service_id) {
                $check_validation['price'] = 'required|array|min:' . count($request->service_id) . '|max:' . count($request->service_id);
                $check_validation['price.*'] = 'numeric';
                $check_validation['service_id.*'] = 'exists:services,id';
            }

            $validator = Validator::make($request->all(), $check_validation);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->statusArr['validation']);
            }

            foreach ($request->service_id as $key => $service) {
                $addService = BarberService::where(['service_id' => $service, 'barber_id' => Auth::user()->id])->first();
                if (empty($addService)) {
                    $addService = new BarberService();
                    $addService->service_id = $service;
                    $addService->barber_id = Auth::user()->id;
                }
                $addService->price = $request->price[$key];
                $addService->is_active = 1;
                $addService->save();
            }

            $user_update_flag =  User::where('id', Auth::user()->id)->update(['is_service_added' => 1]); //update service added flag

            // $myServices =  BarberService::with('service', 'barber')->where('barber_id', Auth::user()->id)->get();

            return $this->sendResponse($response = [], "Parlour Service Add Successfully");
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function getBarberService()
    {
        try {
            $user_id = Auth::user()->id;
            $allServices = Service::with('category', 'subcategory')->where('is_active', 1)->get();
            $getServices =  $allServices->map(function ($item) use ($user_id) {
                $checkService = BarberService::where(['barber_id' => $user_id, 'service_id' => $item->id, 'is_active' => 1])->first();
                if ($checkService) {
                    $item->service_added = true;
                    $item->service_id = $checkService->id;
                    $item->price = $checkService->price;
                } else {
                    $item->service_added = false;
                    $item->service_id = null;
                    $item->price = null;
                }
                return $item;
            });

            return $this->sendResponse($response = $getServices, "Parlour services get successfully");
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function deleteBarberService(Request $request)
    {
        DB::beginTransaction();
        try {
            $check_validation = array(
                'id' => 'required|integer|exists:barber_services,id',
            );

            $validator = Validator::make($request->all(), $check_validation);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->statusArr['validation']);
            }

            $check = BarberService::where('barber_id', Auth::user()->id)->where('id', $request->id)->count();
            if ($check > 0) {

                $delete = BarberService::where('barber_id', Auth::user()->id)->where('id', $request->id)->update(['is_active' => 0]);
                $msg = "Parlour Service delete Successfully";

                $myServices =  BarberService::with('service', 'barber')->where(['barber_id' => Auth::user()->id, 'is_active' => 1])->get();

                $response = [];
                if (!empty($myServices) && $myServices->count()) {
                    $response = ResourcesBarberService::collection($myServices);
                }
            } else {
                $response = [];
                $msg = "You are not authorized parlour";
            }

            DB::commit();
            return $this->sendResponse($response, $msg);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }
}
