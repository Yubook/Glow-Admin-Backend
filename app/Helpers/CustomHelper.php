<?php

namespace App\Helpers;

use App\Device;
use App\User;
use App\Users;
use File;
use Illuminate\Support\Facades\Storage;
use Str;
use Edujugon\PushNotification\PushNotification;

function chat_notification($receiver_id, $title, $message, $type = 2, $group_id)
{
    $push = new PushNotification('fcm');

    $devices = Device::where('user_id', $receiver_id)->get();

    if (count($devices) > 0) {
        foreach ($devices as $deviceType) {
            //1 for IOS, 2= Android
            $push_token = $deviceType->push_token;
            if ($deviceType->type == 1) {

                $push->setMessage([
                    'notification' => [
                        'notification_type' => $type,
                        'title' => $title,
                        'body' => $message,
                        'group_id' => $group_id
                    ],
                    'data' =>
                    [
                        'notification_type' => $type,
                        'title' => $title,
                        'body' => $message,
                        'group_id' => $group_id
                    ]
                ])
                    ->setDevicesToken([$push_token])
                    ->send()
                    ->getFeedback();
            } else {

                $push->setMessage([
                    'data' => [
                        'notification_type' => $type,
                        'title' => $title,
                        'body' => $message,
                        'group_id' => $group_id
                    ]
                ])
                    ->setDevicesToken([$push_token])
                    ->send()
                    ->getFeedback();
            }
        }
    }
}

function sendNotification($title, $message, $notification_type, $task_id = 1, $user_id)
{
    $push = new PushNotification('fcm');

    $devices = Device::where('user_id', $user_id)->get();
    if (count($devices) > 0) {
        foreach ($devices as $deviceType) {
            //1 for IOS, 2= Android
            $push_token = $deviceType->push_token;
            if ($deviceType->type == 1) {

                $push->setMessage([
                    'notification' => [
                        'notification_type' => $notification_type, // 1 = order ,2 = chat
                        'title' => $title,
                        'body' => $message,
                        'task_id' => $task_id
                    ],
                    'data' =>
                    [
                        'notification_type' => $notification_type,
                        'title' => $title,
                        'body' => $message,
                        'task_id' => $task_id
                    ]
                ])
                    ->setDevicesToken([$push_token])
                    ->send()
                    ->getFeedback();
            } else {

                $push->setMessage([
                    'data' => [
                        'notification_type' => $notification_type,
                        'title' => $title,
                        'body' => $message,
                        'id' => $task_id
                    ]
                ])
                    ->setDevicesToken([$push_token])
                    ->send()
                    ->getFeedback();
            }
        }
    }
}

function web_notification($title, $message, $user_id)
{
    $firebaseToken = User::where('id', $user_id)->whereNotNull('device_token')->pluck('device_token')->all();

    $SERVER_API_KEY = 'AAAAFcK2xkQ:APA91bFBhnBzrE_kjbvdz8Vs_pMAJXeVw-YNLbsdpojIZ6W0eytLmEtsGAPfiyKgyBb4gBhGgc22E6CRb0kmzNeCx1zIo9xCN-QT5oyrXRpC9E6lO8GbZCE8UyIcfQbpMLywA5SgT2GR';

    $data = [
        "registration_ids" => $firebaseToken,
        "notification" => [
            "title" => $title,
            "body" => $message,
        ]
    ];
    $dataString = json_encode($data);

    $headers = [
        'Authorization: key=' . $SERVER_API_KEY,
        'Content-Type: application/json',
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

    $response = curl_exec($ch);
    dd($response);
    return true;
}


function generate_api_token($mobile_number)
{
    return md5(time() . Str::random(30)) . md5($mobile_number) . md5(Str::random(30) . time());
}

function commonUploadImage($storage_path, $file_path)
{
    $storage_path = 'public/' . $storage_path;
    $file_store_path = Storage::disk('local')->put($storage_path, $file_path);
    return $file_store_path;
}

function deleteOldImage($file_path)
{
    if ($file_path != "product_image/no-image.jpg") {
        $file_delete = Storage::disk('local')->delete($file_path);
    }
    return true;
}

function getUploadImage($storage_path)
{
    return Storage::url($storage_path);
}

function get_file_extension($file_name)
{
    return substr(strrchr($file_name, '.'), 1);
}

function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return strtoupper($randomString);
}

function is_json($string, $return_data = false)
{
    $data = json_decode($string, true);
    return (json_last_error() == JSON_ERROR_NONE) ? ($return_data ? $data : TRUE) : FALSE;
}

function is_arary_json($string, $return_data = false)
{
    $data = json_decode($string, true);
    return (json_last_error() == JSON_ERROR_NONE) ? ($return_data ? $data : TRUE) : FALSE;
}

function file_exists_check($path)
{
    return file_exists($path);
}

function add_prefix_path($n, $image_path) // function for delete images
{
    return ($image_path . $n);
}

function empty_dir($dir_path)
{
    array_map('unlink', glob($dir_path . '*'));
}

function sort_by_order($a, $b)
{
    return $a['i_order'] - $b['i_order'];
}

function remove_dir($dir_path)
{
    File::deleteDirectory($dir_path);
}

/**Date format */
function getApiDateFormat($date)
{
    //return $last_active_time = $objSetActive->updated_at->diffForHumans();
    return gmdate('Y-m-d h:i:s', strtotime($date));
}

/**image path  */
function getApiImagePath($path)
{
    return  asset('storage/app/' . $path);
}

/** 12 Hours Time  */
function getApiTimeFormat($time)
{
    return gmdate('h:i A', strtotime($time));
}

function distance($lat1, $lon1, $lat2, $lon2, $unit)
{
    if (($lat1 == $lat2) && ($lon1 == $lon2)) {
        return 0;
    } else {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else if ($unit == "M") {
            return $miles;
        }
    }
}


function custom_number_format($n, $precision = 2)
{
    if ($n < 900) {
        // Default
        $n_format = number_format($n);
    } else if ($n < 900000) {
        // Thausand
        $n_format = number_format($n / 1000, $precision) . 'K';
    } else if ($n < 900000000) {
        // Million
        $n_format = number_format($n / 1000000, $precision) . 'M';
    } else if ($n < 900000000000) {
        // Billion
        $n_format = number_format($n / 1000000000, $precision) . 'B';
    } else {
        // Trillion
        $n_format = number_format($n / 1000000000000, $precision) . 'T';
    }
    return $n_format;
}
