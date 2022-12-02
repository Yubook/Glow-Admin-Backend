<?php

namespace App\Http\Controllers\API;

use App\BarberService;
use App\BarberSlot;
use App\BarberTermsPolicy;
use App\Device;
use App\Document;
use App\DriverSlot;
use App\GroupMst;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Http\Resources\BarberOnlyProfile;
use App\Http\Resources\BarberProfile;
use App\Http\Resources\GetNearByBarber;
use App\Http\Resources\Order as OrderResource;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\UserFavouriteList;
use App\Http\Resources\UserWithoutToken as UserWithoutTokenResource;
use App\Notification;
use App\Order;
use Illuminate\Support\Facades\Mail;

use App\TermsPolicy;
use App\User;
use App\UserFavouriteBarber;
use App\UserWallet;
use Carbon\Carbon;
use Laravel\Passport\HasApiTokens;

use function App\Helpers\commonUploadImage;
use function App\Helpers\deleteOldImage;
use function App\Helpers\distance;
use function App\Helpers\getUploadImage;
use function App\Helpers\web_notification;


class UserController extends Controller
{
    public function loginUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email'
            ]);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->statusArr['validation']);
            }

            $response = [];

            // Firebase testing static condition for playstore upload temporiry 
            if ($request->email == 'testaccount5025@gmail.com') {
                $response['otp'] = '123456';
                return $this->sendResponse($response, 'OTP sent to your email');
            }
            // Firebase testing static condition for playstore upload temporiry

            $user = User::where(['email' => $request->email])->first();

            // otp generate
            $otp = rand(101111, 999899);
            if (!empty($user) && $user->count()) {
                if ($user->is_active == 1) {
                    $user->otp = $otp;
                    $user->save();
                    // mail send
                    $data = [
                        'email' => $user->email,
                        'otp' => $otp
                    ];
                } else {
                    return $this->sendNotExists($response = (object)[], 'User Profile Blocked. Please Contact to Admin');
                }
            } else {
                $newUser = new User();
                $newUser->role_id = 3;
                $newUser->email = $request->email;
                $newUser->otp = $otp;
                $newUser->save();
                // mail send
                $data = [
                    'email' => $newUser->email,
                    'otp' => $otp
                ];
            }

            Mail::to($data['email'])->send(new \App\Mail\OTPMail($data));

            $response['otp'] = $otp;
            return $this->sendResponse($response, 'OTP sent to your email');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function loginUserOtpVerify(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'otp' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->statusArr['validation']);
            }

            $response = [];

            // temp code for playstore upload temporiry
            if ($request->email == 'testaccount5025@gmail.com' && $request->otp == '123456') {
                $user = User::where(['email' => $request->email])->first();
                $user['token'] = $user->createToken('fade')->accessToken;
                $response = new UserResource($user);
                return $this->sendResponse($response, 'Login Successfully');
            }
            // temp code for playstore upload
            $user = User::where(['email' => $request->email, 'is_active' => 1])->first();

            if (!empty($user) && $user->count()) {
                if ($request->otp == $user->otp) {
                    $user->otp = null;
                    $user->save();
                    $user['token'] = $user->createToken('glow')->accessToken;
                    $response = new UserResource($user);
                    return $this->sendResponse($response, 'Login Successfully');
                }
                return $this->sendNotExists($response = (object)[], 'Otp Not Match');
            } else {
                return $this->sendNotExists($response = (object)[], 'User Not Found. Please Register User Once');
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function addProfileUser(Request $request)
    {
        DB::beginTransaction();
        try {
            $check_validation = array(
                'name' => 'sometimes|string',
                'address' => 'sometimes',
                'country_id' => 'sometimes|numeric|exists:countries,id',
                'phone_code' => 'sometimes|string',
                'mobile'  => 'sometimes|numeric|unique:users',
                'latitude' => 'sometimes',
                'longitude' => 'sometimes',
                'profile' => 'sometimes|image|mimes:jpeg,png,jpg|max:1024',
            );

            $validator = Validator::make($request->all(), $check_validation);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->statusArr['validation']);
            }

            $user = User::find(Auth::user()->id);
            if (isset($request->name)) {
                $user->name = $request->name;
            }
            if (isset($request->address)) {
                $user->address_line_1 = $request->address;
            }
            if (isset($request->country_id)) {
                $user->country_id = $request->country_id;
            }
            if (isset($request->phone_code)) {
                $user->phone_code = $request->phone_code;
            }
            if (isset($request->mobile)) {
                $user->mobile = $request->mobile;
            }
            if (isset($request->latitude)) {
                $user->latitude = $request->latitude;
            }
            if (isset($request->longitude)) {
                $user->longitude = $request->longitude;
            }

            if ($request->file('profile')) {
                $attachment = $request->file('profile');
                $storage_path = 'user/profile';
                $imgpath = commonUploadImage($storage_path, $attachment);
                $user->profile = $imgpath;
            }

            $user->profile_approved = 1;
            $user->save();

            $response = new UserWithoutTokenResource($user);
            DB::commit();

            return $this->sendResponse($response, "Profile Submitted Successfully");
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'mobile' => 'required|numeric',
                'phone_code' => 'required|string',
            ]);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->statusArr['validation']);
            }

            $response = [];
            // $user = User::with('notification')->where(['mobile' => $request->mobile, 'is_active' => 1])->first();
            $user = User::where(['phone_code' => $request->phone_code, 'mobile' => $request->mobile, 'is_active' => 1])->first();

            if (!empty($user) && $user->count()) {
                if ($user->role_id == 2 && $user->profile_approved == 0) {
                    return $this->sendNotExists($response = (object)[], 'Profile Not Approved. Please Contact To Admin.');
                }
                $user['token'] = $user->createToken('glow')->accessToken;
                $response = new UserResource($user);
                return $this->sendResponse($response, 'Login Successfully');
            } else {
                return $this->sendNotExists($response = (object)[], 'User Not Found. Please Register User Once');
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->token()->revoke();
            return $this->sendResponse($response = [], 'Logout Successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function getProfile(Request $request)
    {
        try {
            $user = Auth::user();
            $response = new UserWithoutTokenResource($user);
            return $this->sendResponse($response, 'Profile Get Successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function addProfile(Request $request)
    {
        DB::beginTransaction();
        try {
            $check_validation = array(
                'name' => 'required',
                'role_id' => 'required|numeric|in:2,3', // 2=barber,3=user
                'address' => 'required',
                'country_id' => 'required|numeric|exists:countries,id',
                'phone_code' => 'required|string',
                'mobile'  => 'required|numeric|unique:users',
                'latitude' => 'required',
                'longitude' => 'required',
                'email' => 'required|email|unique:users',
                // 'gender' => 'required|in:male,female',
                //'image' => 'required|image|mimes:jpeg,png,jpg|max:1024',
                'profile' => 'image|mimes:jpeg,png,jpg|max:1024',
            );

            if ($request->role_id == 2) { //2-barber , 3-User
                $check_validation['gender'] = 'required|in:male,female';
            }

            $validator = Validator::make($request->all(), $check_validation);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->statusArr['validation']);
            }

            $user = new User();
            $user->name = $request->name;
            $user->role_id = $request->role_id;
            $user->country_id = $request->country_id;
            $user->phone_code = $request->phone_code;
            $user->mobile = $request->mobile;
            $user->address_line_1 = $request->address;
            $user->latitude = $request->latitude;
            $user->longitude = $request->longitude;
            $user->email = $request->email;

            if ($request->file('profile')) {
                $attachment = $request->file('profile');
                $storage_path = 'user/profile';
                $imgpath = commonUploadImage($storage_path, $attachment);
                $user->profile = $imgpath;
            }

            if ($request->role_id == 2) {
                $user->gender = $request->gender;
            }
            $user->profile_approved = 1;
            $user->save();

            //If new user is barber then add default policy and terms
            if ($request->role_id == 2) {
                // $addPolicy = new BarberTermsPolicy();
                // $addPolicy->barber_id = $user->id;
                // $addPolicy->type = "policy";
                // $addPolicy->content = "Please add your privacy policy for users";
                // $addPolicy->save();

                $addTerms = new BarberTermsPolicy();
                $addTerms->barber_id = $user->id;
                $addTerms->type = "terms";
                $addTerms->content = "Please add your terms and condition for users";
                $addTerms->save();
            }

            $user['token'] = $user->createToken('glow')->accessToken;
            $response = new UserResource($user);
            DB::commit();

            return $this->sendResponse($response, "Profile Submitted Successfully");
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function addBarberProfile(Request $request)
    {
        $check_validation = array(
            'name' => 'required',
            'address_line_1' => 'required',
            'address_line_2' => 'sometimes',
            'postal_code' => 'sometimes',
            'country_id' => 'required|numeric|exists:countries,id',
            'phone_code' => 'required|string',
            'mobile'  => 'required|numeric|unique:users',
            'latitude' => 'required',
            'longitude' => 'required',
            'email' => 'required|email|unique:users',
            'gender' => 'required|in:male,female',
            'document_1' => 'required|image|mimes:jpeg,png,jpg',
            'document_1_name' => 'required|string',
            'profile' => 'required|image|mimes:jpeg,png,jpg|max:1024',
            'city' => 'required|integer|exists:cities,id',
            // 'state' => 'required|integer|exists:states,id'
        );

        if ($request->has('document_2_name')) {
            $check_validation['document_2_name'] = 'required|string';
            $check_validation['document_2'] = 'required|image|mimes:jpeg,png,jpg';
        }

        $validator = Validator::make($request->all(), $check_validation);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), $this->statusArr['validation']);
        }

        DB::beginTransaction();
        try {
            $new_barber = new User();
            $new_barber->role_id = 2;
            $new_barber->name = $request->name;
            $new_barber->email = $request->email;
            $new_barber->phone_code = $request->phone_code;
            $new_barber->mobile = $request->mobile;
            $new_barber->country_id = $request->country_id;
            $new_barber->latitude = $request->latitude;
            $new_barber->longitude = $request->longitude;
            $new_barber->address_line_1 = $request->address_line_1;
            if (isset($request->address_line_2)) {
                $new_barber->address_line_2 = $request->address_line_2;
            }
            if (isset($request->postal_code)) {
                $new_barber->postal_code = $request->postal_code;
            }
            //$new_barber->state_id = $request->state;
            $new_barber->city_id = $request->city;
            $new_barber->gender = $request->gender;
            $new_barber->profile_approved = 0;
            $new_barber->is_available = 0;

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

            //$user['token'] = $user->createToken('glow')->accessToken;
            //$response = new UserResource($user);
            $response = (object)[];
            DB::commit();

            return $this->sendResponse($response, "Profile Submitted Successfully. You can log in after verify your profile by admin.");
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }


    public function editProfile(Request $request)
    {
        DB::beginTransaction();
        try {
            $check_validation = array(
                'name' => 'sometimes',
                'mobile' => 'sometimes|numeric|unique:users',
                'address' => 'sometimes',
                'latitude' => 'sometimes',
                'longitude' => 'sometimes',
                'email' => 'sometimes|unique:users',
                'profile' => 'sometimes|image|mimes:jpeg,png,jpg|max:1024',
            );

            if (Auth::user()->role_id == 2) { //2-Barber
                $check_validation['gender'] = 'sometimes|in:male,female';
            }

            $validator = Validator::make($request->all(), $check_validation);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->statusArr['validation']);
            }

            $user_id = Auth::user()->id;
            $user = User::where('id', $user_id)->first();

            if (isset($request->name)) {
                $user->name = $request->name;
            }
            if (isset($request->address)) {
                $user->address_line_1 = $request->address;
            }
            if (isset($request->latitude)) {
                $user->latitude = $request->latitude;
            }
            if (isset($request->longitude)) {
                $user->longitude = $request->longitude;
            }
            if (isset($request->mobile)) {
                $user->mobile = $request->mobile;
            }
            if (isset($request->email)) {
                $user->email = $request->email;
            }

            if (isset($request->gender)) {
                $user->gender = $request->gender;
            }

            if ($request->file('profile')) {
                if (isset($user->profile)) {
                    deleteOldImage($user->profile);
                }

                $attachment = $request->file('profile');
                $storage_path = 'user/profile';
                $imgpath = commonUploadImage($storage_path, $attachment);
                $user->profile = $imgpath;
            }
            $user->save();

            $response = new UserWithoutTokenResource($user);
            DB::commit();

            return $this->sendResponse($response, "Profile Save Successfully");
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function updateBarberProfile(Request $request)
    {
        $check_validation = array(
            'name' => 'sometimes',
            'address_line_1' => 'sometimes',
            'address_line_2' => 'sometimes',
            //'mobile'  => 'required|numeric|unique:users',
            'latitude' => 'sometimes',
            'longitude' => 'sometimes',
            'postal_code' => 'sometimes',
            //'email' => 'required|email|unique:users',
            'gender' => 'sometimes|in:male,female',
            //'document_1' => 'required|image|mimes:jpeg,png,jpg',
            // 'document_1_name' => 'required|string',
            'profile' => 'sometimes|image|mimes:jpeg,png,jpg|max:1024',
            'country_id' => 'sometimes|numeric|exists:countries,id',
            'city' => 'sometimes|integer|exists:cities,id',
            'state' => 'sometimes|integer|exists:states,id'
        );

        if ($request->has('document_2_name') && $request->has('document_2')) {
            $check_validation['document_2_name'] = 'required|string';
            $check_validation['document_2'] = 'required|image|mimes:jpeg,png,jpg';
        }

        $validator = Validator::make($request->all(), $check_validation);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), $this->statusArr['validation']);
        }

        DB::beginTransaction();
        try {
            $new_barber = User::find(Auth::user()->id);
            if (isset($request->name)) {
                $new_barber->name = $request->name;
            }
            if (isset($request->latitude)) {
                $new_barber->latitude = $request->latitude;
            }
            if (isset($request->longitude)) {
                $new_barber->longitude = $request->longitude;
            }
            if (isset($request->address_line_1)) {
                $new_barber->address_line_1 = $request->address_line_1;
            }
            if (isset($request->address_line_2)) {
                $new_barber->address_line_2 = $request->address_line_2;
            }
            if (isset($request->postal_code)) {
                $new_barber->postal_code = $request->postal_code;
            }
            if (isset($request->country_id)) {
                $new_barber->country_id = $request->country_id;
            }
            if (isset($request->state)) {
                $new_barber->state_id = $request->state;
            }
            if (isset($request->city)) {
                $new_barber->city_id = $request->city;
            }
            if (isset($request->gender)) {
                $new_barber->gender = $request->gender;
            }

            if ($request->file('profile')) {
                deleteOldImage($new_barber->profile);
                $image = $request->file('profile');
                $storage_path = "user/profile";
                $userimage = commonUploadImage($storage_path, $image);
                $new_barber->profile = $userimage;
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
            $new_barber->save();

            $response = (object)[];
            $profile = User::with('documents')->find($new_barber->id);
            if (isset($profile)) {
                $response = new BarberOnlyProfile($profile);
            }
            DB::commit();

            return $this->sendResponse($response, "Profile Updated Successfully");
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function checkUserExists(Request $request)
    {
        DB::beginTransaction();
        try {

            $check_validation = array(
                'mobile' => 'required|numeric',
                'phone_code' => 'required|string'
            );

            $validator = Validator::make($request->all(), $check_validation);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->statusArr['validation']);
            }

            $response = [];

            $checkUser = User::where('mobile', $request->mobile)->where('phone_code', $request->phone_code)->first();
            if ($checkUser) {
                $response = new UserWithoutTokenResource($checkUser);
                DB::commit();
                return $this->sendExists($response, 'User Exists');
            }

            DB::commit();
            return $this->sendNotExists($response, 'User not Exists');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function addDeviceIdAndToken(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'device_id' => 'required',
                'push_token' => 'required',
                'type' => 'required|min:1|max:2',  // 1=Ios, 2=Android
                'latest_latitude' => 'required',
                'latest_longitude' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->statusArr['validation']);
            }

            $already = Device::where(['user_id' => Auth::user()->id, 'device_id' => $request->device_id])->first();
            if ($already) {
                $already->push_token = $request->push_token;
                $already->type = $request->type;
                $already->save();
            } else {
                $device_Add = new Device();
                $device_Add->user_id = Auth::user()->id;
                $device_Add->device_id = $request->device_id;
                $device_Add->push_token = $request->push_token;
                $device_Add->type = $request->type;
                $device_Add->save();
            }

            $addLatestPosition = User::where('id', Auth::user()->id)->update(['latest_latitude' => $request->latest_latitude, 'latest_longitude' => $request->latest_longitude]);

            $user = User::with('notification')->where(['id' => Auth::user()->id, 'is_active' => 1])->first();
            $response['is_read'] = !empty($user->notification) && ($user->notification->is_read == 0) ? true : false;

            if (Auth::user()->role_id == 3) {
                $latitude  = $request->latest_latitude;
                $longitude = $request->latest_longitude;
                $radius = 5;  // set Miles

                $nearByBarbers = User::where(['users.is_available' => 1, 'users.is_active' => 1, 'users.is_service_added' => 1, 'users.role_id' => 2])->select(
                    "users.*",
                    DB::raw("3959 * acos(cos(radians(" . $latitude . "))
                                 * cos(radians(users.latitude)) * cos(radians(users.longitude) - radians(" . $longitude . "))
                                 + sin(radians(" . $latitude . ")) * sin(radians(users.latitude))) AS distance")
                );
                $nearByBarbers = $nearByBarbers->having('distance', '<', $radius)->orderBy('distance', 'asc')->get();

                $response['nearByBarbers'] = [];
                $filtered = $nearByBarbers->filter(function ($barber) {
                    if ($barber->distance <= $barber->max_radius) {
                        return $barber;
                    }
                });

                if (!empty($filtered)) {
                    $filtered = $filtered->map(function ($barber) use ($user) {
                        $barber->is_favourite = UserFavouriteBarber::where(['barber_id' => $barber->id, 'user_id' => $user->id])->count() ? true : false;
                        $barber->services = BarberService::with('service')->where('barber_id', $barber->id)->where('is_active', 1)->get();
                        return $barber;
                    });

                    if (count($filtered) > 0) {
                        $response['nearByBarbers'] = GetNearByBarber::collection($filtered);
                    }
                }
            } else {
                $availability = BarberSlot::where(['barber_id' => Auth::user()->id, 'date' => date('Y-m-d')])->first();
                if (!empty($availability)) {
                    $response['availability'] = 1;
                } else {
                    $response['availability'] = 0;
                }
            }
            // Get Admin and user chat group id
            $admin_id = User::where('role_id', 1)->first();
            $group_info = GroupMst::where([['sender_id', '=', $admin_id->id], ['receiver_id', '=', Auth::user()->id]])->orWhere([['receiver_id', '=', $admin_id->id], ['sender_id', '=', Auth::user()->id]])->first();
            if (empty($group_info)) {
                $group_info = new GroupMst();
                $group_info->sender_id = $admin_id->id;
                $group_info->receiver_id = Auth::user()->id;
                $group_info->save();
            }
            $response['customer_support_chat_id'] = $group_info->id;
            DB::commit();
            return $this->sendResponse($response, 'Successfully add details and get home screen data');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function getNearByBarberByLocations(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'latest_latitude' => 'required',
                'latest_longitude' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->statusArr['validation']);
            }

            $user = User::with('notification')->where(['id' => Auth::user()->id, 'is_active' => 1])->first();
            $response['is_read'] = !empty($user->notification) && ($user->notification->is_read == 0) ? true : false;

            if (Auth::user()->role_id == 3) {
                $latitude  = $request->latest_latitude;
                $longitude = $request->latest_longitude;
                $radius = 5;  //Miles

                // Save latest position of user
                $user->latest_latitude = $latitude;
                $user->latest_longitude = $longitude;
                $user->save();

                $nearByBarbers = User::where(['users.is_active' => 1, 'users.role_id' => 2])->select(
                    "users.*",
                    DB::raw("3959 * acos(cos(radians(" . $latitude . "))
                                 * cos(radians(users.latitude)) * cos(radians(users.longitude) - radians(" . $longitude . "))
                                 + sin(radians(" . $latitude . ")) * sin(radians(users.latitude))) AS distance")
                );
                $nearByBarbers = $nearByBarbers->having('distance', '<', $radius)->orderBy('distance', 'asc')->get();

                $response['nearByBarbers'] = [];
                $filtered = $nearByBarbers->filter(function ($barber) {
                    if ($barber->distance <= $barber->max_radius) {
                        return $barber;
                    }
                });

                if (!empty($filtered)) {
                    $filtered = $filtered->map(function ($barber) use ($user) {
                        $barber->is_favourite = UserFavouriteBarber::where(['barber_id' => $barber->id, 'user_id' => $user->id])->count() ? true : false;
                        $barber->services = BarberService::with('service')->where('barber_id', $barber->id)->where('is_active', 1)->get();
                        return $barber;
                    });

                    if (count($filtered) > 0) {
                        $response['nearByBarbers'] = GetNearByBarber::collection($filtered);
                    }
                }
            }

            DB::commit();
            return $this->sendResponse($response, 'Successfully get near by barbers');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function is_favourite(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer|exists:users,id',
                'barber_id' => 'required|integer|exists:users,id',
                'type' => 'required|integer|in:0,1' // 0 = not favourite , 1 = favourite
            ]);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->statusArr['validation']);
            }

            $already = UserFavouriteBarber::where(['user_id' => $request->user_id, 'barber_id' => $request->barber_id])->first();
            if ($already) {
                if ($request->type == 0) {
                    UserFavouriteBarber::where(['user_id' => $request->user_id, 'barber_id' => $request->barber_id])->delete();
                    $response['type'] = 0;
                    $msg = 'Remove from favourite successfully';
                } else {
                    $response['type'] = 1;
                    $msg = 'Already added in favourite';
                }
            } else {
                $new = new UserFavouriteBarber();
                $new->user_id = $request->user_id;
                $new->barber_id = $request->barber_id;
                $new->save();
                $response['type'] = 1;
                $msg = 'Favourite added successfully';
            }

            DB::commit();

            return $this->sendResponse($response, $msg);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function myFavouriteBarbers()
    {
        try {
            $response = [];
            $query = UserFavouriteBarber::with('barber')->where('user_id', Auth::user()->id)->orderBy('id', 'desc')->get();

            $latitude  = Auth::user()->latest_latitude;
            $longitude = Auth::user()->latest_longitude;

            $addData = $query->map(function ($barber) use ($latitude, $longitude) {
                $barber->services = BarberService::with('service')->where('barber_id', $barber->barber_id)->where('is_active', 1)->get();
                $barber->distance = distance($latitude, $longitude, $barber->barber->latitude, $barber->barber->longitude, $unit = 'M');
                return $barber;
            });

            if (!empty($addData) && $addData->count()) {
                $response = UserFavouriteList::collection($addData);
            }

            return $this->sendResponse($response, 'Favourite list get successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function getUserBookings(Request $request)
    {
        DB::beginTransaction();
        try {
            $limit = (isset($request->limit)) ? $request->limit : 30;
            $response = [];

            $response['previous'] =  Order::with('review.reviewImages', 'barber', 'deleted_service_timings.service', 'deleted_service_timings.slot.time')->whereHas('deleted_service_timings', function ($q) {
                $q->whereHas('slot', function ($query) {
                    $query->where('date', '<', Carbon::now()->format('Y-m-d'));
                });
            })->where('user_id', Auth::user()->id)->latest()->paginate($limit);

            $response['today'] =  Order::with('review.reviewImages', 'barber', 'deleted_service_timings.service', 'deleted_service_timings.slot.time')->whereHas('deleted_service_timings', function ($q) {
                $q->whereHas('slot', function ($query) {
                    $query->where('date', '=', Carbon::now()->format('Y-m-d'));
                });
            })->where('user_id', Auth::user()->id)->latest()->paginate($limit);

            $response['next'] =  Order::with('review.reviewImages', 'barber', 'deleted_service_timings.service', 'deleted_service_timings.slot.time')->whereHas('deleted_service_timings', function ($q) {
                $q->whereHas('slot', function ($query) {
                    $query->where('date', '>', Carbon::now()->format('Y-m-d'));
                });
            })->where('user_id', Auth::user()->id)->latest()->paginate($limit);

            if (!empty($response['previous']) && $response['previous']->count()) {
                $response['previous'] = OrderResource::collection($response['previous'])->response()->getData(true);
            }

            if (!empty($response['today']) && $response['today']->count()) {
                $response['today'] = OrderResource::collection($response['today'])->response()->getData(true);
            }

            if (!empty($response['next']) && $response['next']->count()) {
                $response['next'] = OrderResource::collection($response['next'])->response()->getData(true);
            }

            DB::commit();
            return $this->sendResponse($response, $message = "Successfully Get User Booking Details");
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function getNotification(Request $request)
    {
        try {
            //$limit=10; 
            //Notification::where('user_id', Auth::user()->id)->update(['is_read' => 1]);

            $query = Notification::where('user_id', Auth::user()->id);
            if (!empty($request->limit)) {
                $response = $query->latest()->paginate($request->limit);
            } else {
                $response = $query->latest()->get();
            }

            return $this->sendResponse($response, $message = "Notification get successfully");
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function read_notification(Request $request)
    {
        DB::beginTransaction();
        try {
            if (isset($request->notification_id)) {
                Notification::where('user_id', Auth::user()->id)->where('id', $request->notification_id)->update(['is_read' => 1]);
            } else {
                Notification::where('user_id', Auth::user()->id)->update(['is_read' => 1]);
            }

            DB::commit();
            $response = [];
            return $this->sendResponse($response, $message = "Successfully Flag Change of Notification");
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    public function getTermsPolicy(Request $request)
    {
        DB::beginTransaction();
        try {
            $response = [];

            $query = TermsPolicy::orderby('id', 'asc')->where('is_active', 1);

            if (isset($request->for)) {
                $response = $query->where('for', $request->for)->get();
            } else {
                $response = $query->get();
            }

            DB::commit();
            return $this->sendResponse($response, $message = "Successfully Get Terms and Policy");
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    }

    /* public function getUserWallet(Request $request)
    {
        DB::beginTransaction();
        try {
            $check_validation = array(
                'user_id' => 'required|numeric',
            );

            $validator = Validator::make($request->all(), $check_validation);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->statusArr['validation']);
            }

            $getWallet = UserWallet::where('user_id', $request->user_id)->first();


            if (!empty($getWallet) && $getWallet->count()) {
                $response = new UserWalletResource($getWallet);
                $message = "Successfully Get Wallet of User";
            } else {
                $response = [];
                $message = "No Wallet Found";
            }

            DB::commit();
            return $this->sendResponse($response, $message);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $this->statusArr['something_wrong']);
        }
    } */
}
