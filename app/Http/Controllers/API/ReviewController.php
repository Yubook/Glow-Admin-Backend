<?php

namespace App\Http\Controllers\API;

use App\CheckReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Http\Resources\User as UserWithTokenResource;
use App\Http\Resources\UserWithoutToken as UserResource;
use App\Http\Resources\Service as ServiceResource;
use App\Http\Resources\Subscription as SubscriptionResource;
use App\Http\Resources\Review as ReviewResource;
use App\Service;
use App\Subscription;
use App\User;
use App\Review;
use App\ReviewImage;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

use function App\Helpers\commonUploadImage;

class ReviewController extends Controller
{
    public function addReview(Request $request)
    {
        DB::beginTransaction();
        try {
            $check_validation = array(
                'user_id' => 'required|integer|exists:users,id',
                'barber_id' => 'required|integer|exists:users,id',
                'order_id' => 'required|integer|exists:orders,id',
                'service' => 'required|integer|between:1,5',
                'hygiene' => 'required|integer|between:1,5',
                'value' => 'required|integer|between:1,5',
                'image' => 'array',
                'image.*' => 'image|mimes:jpeg,png,jpg|max:1024',
            );

            $validator = Validator::make($request->all(), $check_validation);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->statusArr['validation']);
            }

            $addReview = new Review();
            $addReview->user_id = $request->user_id;
            $addReview->barber_id = $request->barber_id;
            $addReview->service = $request->service;
            $addReview->hygiene = $request->hygiene;
            $addReview->value = $request->value;
            $addReview->order_id = $request->order_id;
            $addReview->save();

            if ($request->file('image')) {
                $storage_path = "review/image";
                foreach ($request->file('image') as $image) {
                    $file_path = commonUploadImage($storage_path, $image);
                    $image = new ReviewImage();
                    $image->user_reviews_id = $addReview->id;
                    $image->barber_id = $request->barber_id;
                    $image->path = $file_path;
                    $image->save();
                }
            }

            $average = ($request->service + $request->hygiene + $request->value) / 3;
            $add_rating = User::find($request->barber_id);
            $add_rating->average_rating = round(($add_rating->average_rating + (int)round($average)) / ($add_rating->total_reviews + 1));
            $add_rating->total_reviews += 1;
            if ($request->service == 1) {
                $add_rating->onestar += 1;
            } elseif ($request->service == 2) {
                $add_rating->twostar += 1;
            } elseif ($request->service == 3) {
                $add_rating->threestar += 1;
            } elseif ($request->service == 4) {
                $add_rating->fourstar += 1;
            } elseif ($request->service == 5) {
                $add_rating->fivestar += 1;
            }

            if ($request->hygiene == 1) {
                $add_rating->onestar += 1;
            } elseif ($request->hygiene == 2) {
                $add_rating->twostar += 1;
            } elseif ($request->hygiene == 3) {
                $add_rating->threestar += 1;
            } elseif ($request->hygiene == 4) {
                $add_rating->fourstar += 1;
            } elseif ($request->hygiene == 5) {
                $add_rating->fivestar += 1;
            }

            if ($request->value == 1) {
                $add_rating->onestar += 1;
            } elseif ($request->value == 2) {
                $add_rating->twostar += 1;
            } elseif ($request->value == 3) {
                $add_rating->threestar += 1;
            } elseif ($request->value == 4) {
                $add_rating->fourstar += 1;
            } elseif ($request->value == 5) {
                $add_rating->fivestar += 1;
            }

            $add_rating->save();
            DB::commit();

            return $this->sendResponse($response = [], "Review Add Successfully");
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function getReview(Request $request)
    {
        DB::beginTransaction();
        try {

            $response = [];

            $query = Review::with('fromIdUser', 'toIdUser', 'reviewImages')->latest()->get();

            if (!empty($query) && $query->count()) {
                $response = ReviewResource::collection($query);
                $message = "Successfully Get Reviews";
            } else {
                $response = [];
                $message = "No Review Found";
            }

            DB::commit();
            return $this->sendResponse($response, $message);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function getOrderReview(Request $request)
    {
        DB::beginTransaction();
        try {
            $check_validation = array(
                'order_id' => 'required|integer',
                'from_id' => 'required|integer',
                'to_id' => 'required|integer'
            );

            $validator = Validator::make($request->all(), $check_validation);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->statusArr['validation']);
            }

            $query = Review::with('fromIdUser', 'toIdUser', 'order', 'reviewImages')->where(['from_id' => $request->from_id, 'to_id' => $request->to_id, 'order_id' => $request->order_id])->first();

            if (!empty($query) && $query->count()) {
                $response = new ReviewResource($query);
                $message = "Successfully Get Review for this Order";
            } else {
                $response = (object)[];
                $message = "No Review Found";
            }

            DB::commit();
            return $this->sendResponse($response, $message);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }
}
