<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
}); */

Route::group(['namespace' => 'API'], function () {
    Route::group(['prefix' => '/mobile'], function () {
        // api for user temp
        Route::post('loginUser', 'UserController@loginUser'); // check email and send otp
        Route::post('loginUserOtpVerify', 'UserController@loginUserOtpVerify'); // otp verify and create token
        // end api for user temp

        Route::get('getCountries', 'GeneralController@getCountries'); // Get all countries
        Route::get('getStates', 'GeneralController@getStates'); // Get all states
        Route::get('getCities', 'GeneralController@getCities'); // Get all cities
        Route::post('login', 'UserController@login');
        Route::get('getTimes', 'GeneralController@getTimes'); // Get all available times
        Route::get('getServices', 'GeneralController@getServices'); // Get all available service
        Route::get('getAllCategories', 'GeneralController@getAllCategories');
        Route::get('getAllSubCategories', 'GeneralController@getAllSubCategories');
        Route::get('cancelReasons', 'GeneralController@cancelReasons');
        Route::post('addProfile', 'UserController@addProfile'); // Normal User Profile
        Route::post('addBarberProfile', 'UserController@addBarberProfile'); // New Barber Profile Register
        Route::get('getTermsPolicy', 'UserController@getTermsPolicy');
        Route::group(['middleware' => 'auth:api'], function () {
            Route::post('logout', 'UserController@logout');
            Route::post('editProfile', 'UserController@editProfile');
            Route::post('updateBarberProfile', 'UserController@updateBarberProfile'); // Barber Profile update
            Route::get('checkUserExists', 'UserController@checkUserExists');
            Route::post('barberOnOff', 'BarberController@barberOnOff'); // Barber status on off
            Route::post('addBarberTime', 'BarberController@addBarberTime');
            Route::get('getBarberService', 'ServiceController@getBarberService');
            Route::get('driverLatestLocation', 'BarberController@driverLatestLocation'); // track Barber (driver) location

            Route::post('addBarberService', 'ServiceController@addBarberService'); // add edit service price
            Route::post('deleteBarberService', 'ServiceController@deleteBarberService');

            Route::post('addPortfolioes', 'BarberController@addPortfolioes'); // add barber portfolios
            Route::post('removePortfolio', 'BarberController@removePortfolio'); // remove own portfolio
            Route::get('getPortfolio', 'BarberController@getPortfolio'); // get all portfolios ( own + users_reviews)

            Route::post('driverSelectRadius', 'BarberController@driverSelectRadius'); //update barber search area km
            Route::post('addDeviceIdAndToken', 'UserController@addDeviceIdAndToken'); // Register Device ID and get home screen data
            Route::post('getNearByBarberByLocations', 'UserController@getNearByBarberByLocations'); // Get near by barbers by locations

            Route::get('getNotification', 'UserController@getNotification'); //get all notification
            Route::get('read_notification', 'UserController@read_notification'); //read all notification

            Route::post('is_favourite', 'UserController@is_favourite'); //is_favourite
            Route::get('myFavouriteBarbers', 'UserController@myFavouriteBarbers');
            Route::post('addReview', 'ReviewController@addReview');

            Route::get('getService', 'ServiceController@getService');
            Route::post('addService', 'ServiceController@addService'); // not for mobile side use

            Route::get('getDriverProfile', 'BarberController@getDriverProfile'); // Get barber full profile
            Route::post('updateTermsPolicy', 'BarberController@updateTermsPolicy'); // Update terms and policy barber

            Route::post('checkBookAppointmentSlots', 'OrderController@checkBookAppointmentSlots'); // check slots and give slots for booking

            Route::get('createPaymentToken', 'PaymentController@generateClientSecret'); // stripe payment token create
            Route::post('bookAppointment', 'PaymentController@bookAppointment');

            Route::get('getUserPaymentHistory', 'OrderController@getUserPaymentHistory');
            Route::get('getBarberPaymentHistory', 'OrderController@getBarberPaymentHistory');
            Route::post('orderComplete', 'BarberController@orderComplete'); //Order Complete By barber

            Route::get('getUserBookings', 'UserController@getUserBookings');
            Route::get('getBarberBookings', 'BarberController@getBarberBookings');

            Route::get('totalRevenueOfBarber', 'BarberController@totalRevenueOfBarber'); //revenue of barber 
            Route::get('revenueFilterMap', 'BarberController@revenueFilterMap'); // Revenue map filter

            Route::post('cancelOrder', 'OrderController@cancelOrder'); // Order Cancel by user or barber





            Route::get('getReview', 'ReviewController@getReview');
            Route::get('getOrderReview', 'ReviewController@getOrderReview'); // specific order review
            Route::post('addUserService', 'ServiceController@addUserService');
            Route::get('getUserService', 'ServiceController@getUserService');
            Route::post('bookService', 'ServiceController@bookService');
            Route::get('getNearestDriver', 'ServiceController@getNearestDriver');
            Route::post('searchDriver', 'ServiceController@searchDriver');
            Route::get('getDriverSlots', 'ServiceController@getDriverSlots');
            Route::post('bookUserSlot', 'ServiceController@bookUserSlot');
            Route::get('createPaymentToken', 'PaymentController@generateClientSecret'); // stripe payment token create
            Route::post('paymentProcess', 'PaymentController@paymentProcess');
            Route::get('getOrder', 'UserController@getOrder');
            Route::post('rejectAndBookUserSlot', 'ServiceController@rejectAndBookUserSlot'); // reject and rebook
            Route::get('getDriverBookings', 'ServiceController@getDriverBookings'); //Driver all bookings 
            Route::get('getDriverOrder', 'DriverController@getDriverOrder'); // Driver completed order
            Route::get('onOffSlot', 'DriverController@onOffSlot'); //driver on off slot
            Route::get('cancelReason', 'DriverController@cancelReason'); // cancle reasons
            //Route::get('getUserWallet', 'UserController@getUserWallet'); // already covered in addDeviceIdAndToken api
            Route::post('orderCancleByDriver', 'DriverController@orderCancleByDriver');
            Route::post('bookUserSlotByWallet', 'ServiceController@bookUserSlotByWallet'); //payment By wallet
            Route::get('mapLocationDriver', 'DriverController@mapLocationDriver'); // get user driver location  
            Route::post('updateLatestLocation', 'DriverController@updateLatestLocation'); //Update latest driver location


            Route::post('validatesocketuser', 'ChatController@validateSocket');
            Route::post('getInbox', 'ChatController@getInbox');
            Route::post('getMessages', 'ChatController@getMessages');
            Route::post('sendMessage', 'ChatController@sendMessage');
            Route::post('sendFile', 'ChatController@sendFile');
            Route::post('uploadChatFile', 'ChatController@uploadChatFile')->name('upload.chat.file');
            Route::post('setReadMessage1', 'ChatController@setReadMessage1');
            Route::post('deleteMessage', 'ChatController@deleteMessage');

            // Map
            Route::post('getLocation', 'ChatController@getLocation');
            Route::post('updateLocation', 'ChatController@updateLocation');
        });
    });
});

/* Route::group(['namespace' => 'Admin'], function () {
    Route::group(['prefix' => '/mobile'], function () {
        Route::group(['middleware' => 'auth:api'], function () {
            //Chat
            Route::get('contacts', 'ContactsController@get');
            Route::get('conversation/{id}', 'ContactsController@getMessagesFor');
            Route::post('conversation/send', 'ContactsController@send');
        });
    });
}); */
