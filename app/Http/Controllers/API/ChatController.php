<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\User;
use App\Helpers\CommonApiHelper;
use Illuminate\Http\Request;
use Hash, Input, Session, Redirect, Mail, URL, Str, Config, Response, View;
use App\GroupMsg;
use App\GroupMst;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\Order;

use function App\Helpers\getUploadImage;
use function App\Helpers\sendNotification;
use function App\Helpers\getApiDateFormat;
use function App\Helpers\generateRandomString;
use function App\Helpers\chat_notification;
use function App\Helpers\deleteOldImage;

use App\Http\Resources\Chat as ChatResource;
use App\Http\Resources\Order as ResourcesOrder;
use Illuminate\Support\Facades\Crypt;
use App\IsChatEnable;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{

    public function getInbox(Request $request)
    {
        try {
            $user_id = Auth::user()->id;
            $this->UpdateCurrentActive($user_id);

            $admin = User::where('role_id', 1)->select('id', 'name', 'profile', 'last_active_time', 'updated_at')->orderBy('updated_at', 'desc')->get();

            // check user type 
            if (Auth::user()->role_id == 2) {
                $type = 'barber';

                $userIds = IsChatEnable::where('barber_id', $user_id)->orderBy('id', 'desc')->pluck('user_id')->toArray();
                $users = User::whereIn('id', $userIds)->select('id', 'name', 'profile', 'last_active_time', 'updated_at')->get();
            } else if (Auth::user()->role_id == 3) {
                $type = 'user';

                $barberIds = IsChatEnable::where('user_id', $user_id)->orderBy('id', 'desc')->pluck('barber_id')->toArray();
                $barbers = User::whereIn('id', $barberIds)->select('id', 'name', 'profile', 'last_active_time', 'updated_at')->get();
            } else {
                $type = 'admin';

                $barbers = User::where('role_id', 2)->where('id', '!=', $user_id)->where('profile_approved', 1)->select('id', 'name', 'profile', 'last_active_time', 'updated_at')->orderBy('updated_at', 'desc')->get();
                $users = User::where('role_id', 3)->where('id', '!=', $user_id)->where('profile_approved', 1)->select('id', 'name', 'profile', 'last_active_time', 'updated_at')->orderBy('updated_at', 'desc')->get();
            }

            // give admin data to all user
            $admin->map(function ($item) use ($user_id) {
                if (!empty($item->id)) {
                    $receiver_id = $item->id;
                    $item->role = 'admin';
                    $item->unread_count = $this->getUnreadMessageCount($receiver_id);
                    //if(empty($item->last_active_time)){
                    $item->last_active_time = (string) $this->getLastActiveTime($item->updated_at, $item->id);
                    //}
                    $item->profile = getUploadImage($item->profile);
                    $item->group_id = $this->getOrCreateGroup($user_id, $receiver_id);
                    return $item;
                }
            });

            if ($type == 'barber') {
                $users->map(function ($item2) use ($user_id) {
                    if (!empty($item2->id)) {
                        $user_order = Order::with('review.reviewImages', 'user', 'service_timings.service', 'service_timings.slot.time')->where(['barber_id' => $user_id, 'user_id' => $item2->id])->whereIn('is_order_complete', [0])->orderBy('id', 'desc')->get();
                        if (count($user_order) > 0) {
                            $order_resource = ChatResource::collection($user_order);
                        } else {
                            $order_resource = [];
                        }

                        $item2->order = $order_resource;
                        $receiver_id = $item2->id;
                        $item2->role = 'user';
                        $item2->unread_count = $this->getUnreadMessageCount($receiver_id);
                        // if(empty($item2->last_active_time)){
                        $item2->last_active_time = (string) $this->getLastActiveTime($item2->updated_at, $item2->id);
                        //}
                        $item2->profile = getUploadImage($item2->profile);
                        $item2->unread_count = $this->getUnreadMessageCount($receiver_id);
                        $item2->group_id = $this->getOrCreateGroup($user_id, $receiver_id);
                        return $item2;
                    }
                });

                $barbers = [];
            } elseif ($type == 'user') {
                $barbers->map(function ($item1) use ($user_id) {
                    if (!empty($item1->id)) {
                        $barber_order = Order::with('review.reviewImages', 'user', 'service_timings.service', 'service_timings.slot.time')->where(['user_id' => $user_id, 'barber_id' => $item1->id])->whereIn('is_order_complete', [0])->orderBy('id', 'desc')->get();;

                        if (count($barber_order) > 0) {
                            $order_resource = ChatResource::collection($barber_order);
                        } else {
                            $order_resource = [];
                        }
                        $item1->order = $order_resource;
                        $receiver_id = $item1->id;
                        $item1->role = 'barber';
                        $item1->unread_count = $this->getUnreadMessageCount($receiver_id);
                        // if(empty($item1->last_active_time)){
                        $item1->last_active_time = (string) $this->getLastActiveTime($item1->updated_at, $item1->id);
                        //}
                        $item1->profile = getUploadImage($item1->profile);
                        $item1->group_id = $this->getOrCreateGroup($user_id, $receiver_id);
                        return $item1;
                    }
                });
                $users = [];
            } else {
                $barbers = User::where('role_id', 2)->where('profile_approved', 1)->select('id', 'name', 'profile', 'last_active_time', 'updated_at')->get();
                $barbers->map(function ($item1) use ($user_id) {
                    if (!empty($item1->id)) {
                        $receiver_id = $item1->id;
                        $item1->role = 'barber';
                        $item1->unread_count = $this->getUnreadMessageCount($receiver_id);
                        // if(empty($item1->last_active_time)){
                        $item1->last_active_time = (string) $this->getLastActiveTime($item1->updated_at, $item1->id);
                        //}
                        $item1->profile = getUploadImage($item1->profile);
                        $item1->group_id = $this->getOrCreateGroup($user_id, $receiver_id);
                        return $item1;
                    }
                });
                $users = User::where('role_id', 3)->where('profile_approved', 1)->select('id', 'name', 'profile', 'last_active_time', 'updated_at')->get();
                $users->map(function ($item2) use ($user_id) {
                    if (!empty($item2->id)) {
                        $receiver_id = $item2->id;
                        $item2->role = 'user';
                        $item2->unread_count = $this->getUnreadMessageCount($receiver_id);
                        // if(empty($item2->last_active_time)){
                        $item2->last_active_time = (string) $this->getLastActiveTime($item2->updated_at, $item2->id);
                        //}
                        $item2->profile = getUploadImage($item2->profile);
                        $item2->unread_count = $this->getUnreadMessageCount($receiver_id);
                        $item2->group_id = $this->getOrCreateGroup($user_id, $receiver_id);
                        return $item2;
                    }
                });
            }

            return response()->json(['success' => 1, 'admin' => $admin, 'barbers' => $barbers, 'users' => $users]);
        } catch (\Exception $e) {
            return response()->json(['success' => 0, 'error' => $e->getMessage()]);
        }
    }
    public function getMessages(Request $request)
    {
        try {
            $group_id = $request->group_id;
            $messageData = GroupMsg::with('user')->where(['group_id' => $group_id])->get();
            if ($messageData->isEmpty()) {
                $messageData = [];
            } else {
                GroupMsg::where('group_id', '=', $group_id)->update(['is_read' => 1]);
                $messageData->map(function ($item) {
                    $item->created_at = getApiDateFormat($item->created_at);
                    $item->updated_at = getApiDateFormat($item->updated_at);
                    $item->chat_time = $item->created_at->diffForHumans();
                    //$item->chat_time = date('h : i A | M d', strtotime($item->created_at));
                    $item->webfile = $item->filename;
                    $item->filename = env('CHAT_IMAGE_PATH') . 'storage/app/' . $item->filename;
                    return $item;
                });
            }
            return response()->json(['success' => 1, 'messageData' => $messageData]);
        } catch (\Exception $e) {
            return response()->json(['success' => 0, 'error' => $e->getMessage()]);
        }
    }
    public function sendMessage(Request $request)
    {
        try {
            // Encrypt data
            // $encrypted = Crypt::encryptString($request->message);
            $objGroupMsg = new GroupMsg();
            $objGroupMsg->user_id = Auth::user()->id;
            $objGroupMsg->group_id = $request->group_id;
            $objGroupMsg->message = $request->message;
            $objGroupMsg->type = $request->type;
            $objGroupMsg->save();

            $objGroupMsg->created_at = getApiDateFormat($objGroupMsg->created_at);
            $objGroupMsg->updated_at = getApiDateFormat($objGroupMsg->updated_at);
            $objGroupMsg->chat_time = date('h : i A | M d', strtotime($objGroupMsg->created_at));
            $objGroupMsg->webfile = '';
            $objGroupMsg->filename = '';
            $objGroupMsg->user->name = Auth::user()->name;

            // $receiver = GroupMst::find($request->group_id);

            $display_title =  "New Chat Message";
            $display_msg =  $request->message;
            $group_id =  $request->group_id;
            $user_id =  $request->receiver_id;
            $check = chat_notification($user_id, $display_title, $display_msg, $type = 2, $group_id);

            return response()->json(['success' => 1, 'result' => $objGroupMsg]);
        } catch (\Exception $e) {
            return response()->json(['success' => 0, 'error' => $e->getMessage()]);
        }
    }

    public function deleteMessage(Request $request)
    {
        try {
            $checkMessage = GroupMsg::find($request->message_id);
            if ($checkMessage->user_id == Auth::user()->id) {
                if ($checkMessage->type == 2) {
                    deleteOldImage($checkMessage->filename);
                    $checkMessage->filename = null;
                }
                $checkMessage->message = 'This message is deleted';
                $checkMessage->type = 1;
                $checkMessage->save();
            }

            return response()->json(['success' => 1, 'result' => $checkMessage]);
        } catch (\Exception $e) {
            return response()->json(['success' => 0, 'error' => $e->getMessage()]);
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////////
    public function getLocation(Request $request)
    {
        try {
            $order_id = $request->order_id;
            $orderData = Order::with('user', 'barber')->where(['id' => $order_id])->first();
            if (empty($orderData)) {
                $orderData = (object)[];
            } else {
                $orderData = $orderData;
            }
            return response()->json(['success' => 1, 'orderData' => $orderData]);
        } catch (\Exception $e) {
            return response()->json(['success' => 0, 'error' => $e->getMessage()]);
        }
    }
    public function updateLocation(Request $request)
    {
        try {
            $user = User::find($request->driver_id);
            if (empty($user)) {
                $user = (object)[];
            } else {
                $user->latest_latitude = $request->latest_latitude;
                $user->latest_longitude = $request->latest_longitude;
                $user->save();
            }

            return response()->json(['success' => 1, 'driverData' => $user]);
        } catch (\Exception $e) {
            return response()->json(['success' => 0, 'error' => $e->getMessage()]);
        }
    }
    ////////////////////////////////////////////////////////////////////////////////////////

    public function sendFile(Request $request)
    {
        try {
            $image_parts = explode(";base64,", $request['media']);
            $extension = explode('/', mime_content_type($request['media']))[1];
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];

            /*  if(strpos($image_parts[0], 'image') === true) { 
                $image_type_aux = explode("image/", $image_parts[0]);
                $request->type = 2;
            }elseif(strpos($image_parts[0], 'video') === true) {
                $image_type_aux = explode("video/", $image_parts[0]);
                $request->type = 3;
            } */

            $image_base64 = base64_decode($image_parts[1]);
            $fileName = 'public/files/chat/' . generateRandomString() . '.' . $extension;
            Storage::put($fileName, $image_base64);

            $objGroupMsg = new GroupMsg();
            $objGroupMsg->user_id = Auth::user()->id;
            $objGroupMsg->group_id = $request->group_id;
            $objGroupMsg->filename = $fileName;
            $objGroupMsg->type = $request->type;
            $objGroupMsg->save();

            $objGroupMsg->created_at = getApiDateFormat($objGroupMsg->created_at);
            $objGroupMsg->updated_at = getApiDateFormat($objGroupMsg->updated_at);
            $objGroupMsg->chat_time = date('h : i A | M d', strtotime($objGroupMsg->created_at));
            $objGroupMsg->webfile = $fileName;
            $objGroupMsg->filename =  env('CHAT_IMAGE_PATH') . 'storage/app/' . $fileName;

            $display_title =  "New Chat Image";
            $display_msg = 'Image';
            $group_id =  $request->group_id;
            $user_id =  $request->receiver_id;
            $check = chat_notification($user_id, $display_title, $display_msg, $type = 2, $group_id);

            return response()->json(['success' => 1, 'result' => $objGroupMsg]);
        } catch (\Exception $e) {
            return response()->json(['success' => 0, 'error' => $e->getMessage()]);
        }
    }
    // public function uploadChatFile(Request $request) {
    //     try {
    //         //return $request->file('chat_image');
    //         $file='';
    //         if ($request->hasFile('chat_image')) {
    //             $file = Storage::disk('local')->put('images', $request->file('chat_image'));
    //         }
    //         return response()->json(['success' => 1,'file' => $file]);
    //     }catch (\Exception $e) {
    //         return response()->json(['success' => 0,'error' => $e->getMessage()]);
    //     } 
    // }
    public  function setReadMessage1(Request $request)
    {
        try {
            $objGroupMsg = GroupMsg::find($request->msg_id);
            $objGroupMsg->is_read = 1;
            $objGroupMsg->is_notification = 1;
            $objGroupMsg->save();
            return response()->json(['success' => 1, 'result' => $objGroupMsg]);
        } catch (\Exception $e) {
            return response()->json(['success' => 0, 'error' => $e->getMessage()]);
        }
    }
    function getUnreadMessageCount($receiver_id)
    {
        $sender_id = Auth::user()->id;
        $receiver_id = $receiver_id;
        $group_info = GroupMst::where([['sender_id', '=', $sender_id], ['receiver_id', '=', $receiver_id]])->orWhere([['receiver_id', '=', $sender_id], ['sender_id', '=', $receiver_id]])->first();
        if (empty($group_info)) {
            $objGroupMst = new GroupMst;
            $objGroupMst->sender_id = $sender_id;
            $objGroupMst->receiver_id = $receiver_id;
            $objGroupMst->save();
            $group_id = $objGroupMst->id;
        }

        $user_id = $receiver_id;
        $messageCount = GroupMsg::where([['group_id', '=', $group_info->id], ['is_read', '=', 0], ['user_id', '!=', $sender_id]])->count();

        // $message = GroupMsg::where([['group_id','=',$group_info->id],['is_read','=',0],['is_notification','=',0],['user_id','!=',$sender_id]])->get();

        // foreach ($message as $key => $value) {
        // }
        return $messageCount;
    }
    function UpdateCurrentActive($user_id)
    {
        try {
            $objSetActive = User::find($user_id);
            $objSetActive->last_active_time = 1;
            $objSetActive->save();
            return response()->json(['success' => 1, 'result' => $objSetActive]);
        } catch (\Exception $e) {
            return response()->json(['success' => 0, 'error' => $e->getMessage()]);
        }
    }
    function getLastActiveTime($updateTime, $user_id)
    {
        try {
            $seconds  = strtotime(date('Y-m-d H:i:s')) - strtotime($updateTime);
            $secs = floor($seconds % 60);
            $objSetActive = User::find($user_id);
            $last_active_time = 1;
            if ($seconds > 20) {
                $objSetActive->last_active_time = 0;
                $objSetActive->save();
                //$last_active_time = date('h : i A | M d', strtotime($objSetActive->updated_at));
                $last_active_time = $objSetActive->updated_at->diffForHumans();
            }


            return $last_active_time;

            //return response()->json(['success' => 1,'result' => $objSetActive]);
        } catch (\Exception $e) {
            return response()->json(['success' => 0, 'error' => $e->getMessage()]);
        }
    }
    function getOrCreateGroup($sender_id, $receiver_id)
    {
        try {

            $sender_id = $sender_id;
            $receiver_id = $receiver_id;
            $group_id = GroupMst::where([['sender_id', '=', $sender_id], ['receiver_id', '=', $receiver_id]])->orWhere([['receiver_id', '=', $sender_id], ['sender_id', '=', $receiver_id]])->pluck('id')->first();

            if (empty($group_id)) {
                $objGroupMst = new GroupMst();
                $objGroupMst->sender_id = $sender_id;
                $objGroupMst->receiver_id = $receiver_id;
                $objGroupMst->save();
                $group_id = $objGroupMst->id;
            }
            return $group_id;

            //return response()->json(['success' => 1,'group_id' => $group_id]);
        } catch (\Exception $e) {
            return response()->json(['success' => 0, 'error' => $e->getMessage()]);
        }
    }
    // function calculate_time_span($date){
    //     $seconds  = strtotime(date('Y-m-d H:i:s')) - strtotime($date);

    //     $months = floor($seconds / (3600*24*30));
    //     $day = floor($seconds / (3600*24));
    //     $hours = floor($seconds / 3600);
    //     $mins = floor(($seconds - ($hours*3600)) / 60);
    //     $secs = floor($seconds % 60);

    //     if($seconds < 60)
    //         $time = $secs." seconds ago";
    //     else if($seconds < 60*60 )
    //         $time = $mins." min ago";
    //     else if($seconds < 24*60*60)
    //         $time = $hours." hours ago";
    //     else if($seconds < 24*60*60)
    //         $time = $day." day ago";
    //     else
    //         $time = $months." month ago";

    //     return $time;
    // }
    public function validateSocket(Request $request)
    {
        $user = Auth::user();
        $response->message = 'Valid User';
        $response->data = ['id' => $user->id, 'name' => $user->name];
        //$status = $this->statusArr['success'];
        //return $this->return_response();

        return response()->json(['success' => 1, 'result' => $objGroupMsg]);
    }
    public function sendUnreadMessageNotification(Request $request)
    {
        //$id = $request->id;
        // $check =  GroupMsg::where('id',$request->id)->where('is_read',0)->first();
        // if(!empty($check)){
        $display_title =  trans('lanKey.add_chat');
        $display_msg = $request->message;
        $task_id =  $request->group_id;

        //$user_id =  $request->receiver_id;
        $check = sendNotification($user_id = 12, $display_title, $display_msg, $notification_type = 4, $task_id);
        //}
        return response()->json(['success' => 1, 'result' => $request->message]);
    }
}
