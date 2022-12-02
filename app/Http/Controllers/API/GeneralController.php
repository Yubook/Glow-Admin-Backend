<?php

namespace App\Http\Controllers\API;

use App\Category;
use App\City;
use App\Http\Controllers\Controller;
use App\Http\Resources\Category as CategoryResources;
use App\Http\Resources\City as ResourcesCity;
use App\Http\Resources\Service as ServiceResources;
use App\Http\Resources\State as ResourcesState;
use App\Http\Resources\Subcategory as SubcategoryResources;
use App\Http\Resources\Time as TimeResources;
use App\Reason;
use App\Service;
use App\State;
use App\Subcategory;
use App\Timing;
use Illuminate\Http\Request;

use function App\Helpers\commonUploadImage;
use function App\Helpers\sendNotification;

class GeneralController extends Controller
{
    public function getTimes()
    {
        try {
            $response = [];

            $data = Timing::where('is_active', 1)->get();

            if (!empty($data) && $data->count()) {
                $response = TimeResources::collection($data);
            }

            return $this->sendResponse($response, $message = "Successfully get times");
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function getStates()
    {
        try {
            $response = [];

            $data = State::where('is_active', 1)->orderBy('name', 'asc')->get();

            if (!empty($data) && $data->count()) {
                $data = $data->map(function ($singleData) {
                    $singleData->name = str_replace("\n", '', $singleData->name);
                    return $singleData;
                });
                $response = ResourcesState::collection($data);
            }

            return $this->sendResponse($response, $message = "Successfully get state list");
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function getCities()
    {
        try {
            $response = [];

            $data = City::where('is_active', 1)->orderBy('name', 'asc')->get();

            if (!empty($data) && $data->count()) {
                $data = $data->map(function ($singleData) {
                    $singleData->name = str_replace("\n", '', $singleData->name);
                    return $singleData;
                });
                $response = ResourcesCity::collection($data);
            }

            return $this->sendResponse($response, $message = "Successfully get city list");
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function getServices()
    {
        try {
            $response = [];

            $query = Service::with('category', 'subcategory')->where('is_active', 1)->get();

            if (!empty($query) && $query->count()) {
                $response = ServiceResources::collection($query);
            }

            return $this->sendResponse($response, $message = "Successfully get services");
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function getAllCategories()
    {
        try {
            $response = [];

            $query = Category::where('is_active', 1)->get();

            if (!empty($query) && $query->count()) {
                $response = CategoryResources::collection($query);
            }

            return $this->sendResponse($response, $message = "Successfully get categories");
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function getAllSubCategories()
    {
        try {
            $response = [];

            $query = Subcategory::with('category')->where('is_active', 1)->get();

            if (!empty($query) && $query->count()) {
                $response = SubcategoryResources::collection($query);
            }

            return $this->sendResponse($response, $message = "Successfully get sub categories");
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function cancelReasons()
    {
        try {
            $response = [];

            $reasons = Reason::where(['is_active' => 1])->get();
            if (!empty($reasons) && $reasons->count()) {
                $response = $reasons;
                $message = "Successfully Get Reasons";
            } else {
                $message = "No Reasons Found";
            }

            return $this->sendResponse($response, $message);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }
}
