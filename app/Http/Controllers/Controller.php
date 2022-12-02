<?php

namespace App\Http\Controllers;

use App\Notification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /* Start Api Side Commons */
    public $response = array('message' => '', 'data' => null);
    public $status = 412;
    public $paginate = 10;
    public $statusArr = [
        'success' => 200,
        'not_found' => 404,
        'unauthorised' => 412,
        'already_exist' => 409,
        'validation' => 422,
        'disabled' => 423,
        'something_wrong' => 405,
        'forbidden' => 403,
        'unauthenticated' => 401,
    ];

    public function ApiValidator($fields, $rules)
    {
        $validator = Validator::make($fields, $rules);

        if ($validator->fails()) {
            $this->response['message'] = array_shift((array_values($validator->errors()->messages())[0]));

            return false;
        }
    }

    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'message' => $message,
            'result'    => $result,
        ];
        return response()->json($response, 200);
    }
    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error,  $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];
        return response()->json($response, $code);
    }

    public function sendExists($result, $message)
    {
        $response = [
            'success' => true,
            'message' => $message,
            'result'  => $result,
        ];
        return response()->json($response, 409);
    }

    public function sendNotExists($result, $message)
    {
        $response = [
            'success' => false,
            'message' => $message,
            'result'  => $result,
        ];
        return response()->json($response, 200);
    }

    public function return_response()
    {
        return response()->json($this->response, $this->status);
    }
    /* End Api Side Commons */

    public function add_notification($user_id, $title, $message, $type, $order_id,$order_status)
    {
        try {
            // order_status 0 = pending, 1=completed, 2= rejected
            $notification = new Notification();
            if (isset($order_id)) {
                $notification->order_id = $order_id;
            } else {
                $notification->order_id = null;
            }
            $notification->type = $type;
            $notification->user_id = $user_id;
            $notification->title = $title;
            $notification->message = $message;
            if (isset($order_status)) {
                $notification->order_status = $order_status;
            }
            $notification->save();

            return true;
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }
}
