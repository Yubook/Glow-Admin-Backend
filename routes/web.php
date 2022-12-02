<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::get('/password_recovery', function () {
    return view('auth.pwd_recover');
})->name('pwd_recovery');

Route::post('forgot_pwd_mail', 'EmailController@forgot_pwd_mail')->name('forgot_pwd_mail');
//Route::get('/notification', 'Admin\DashboardController@notify')->name('home.dashboard');

Auth::routes();
Route::group(['middleware' => ['auth', 'cors']], function () {
    Route::redirect('/', 'dashboard');
    Route::get('/dashboard', 'HomeController@index')->name('dashboard');
    Route::group(['namespace' => 'Admin'], function () {
        //State And City Import
        // Route::get('/importstatecity', 'AdminController@load_state_city_into_db');
        Route::post('approvedOrReject', 'DashboardController@approvedOrReject')->name('requestNewBarbers.approvedOrReject');

        // Dashboard
        Route::post('save_token', 'DashboardController@saveToken')->name('save_token'); //admin web notification token update
        Route::get('/dashboard', 'DashboardController@index')->name('home.dashboard');
        Route::get('/worldChart', 'DashboardController@worldChart')->name('world.chart'); // Dashboard   
        Route::post('/ajaxOrderModal', 'DashboardController@ajaxOrderModal')->name('home.ajaxOrderModal');

        Route::resource('adminProfile', 'AdminController');

        Route::get('chat/{user_temp_id?}', 'ChatController@getChatView')->name('chat.view');

        //Service resource
        Route::post('services/switchUpdate', 'ServiceController@switchUpdate')->name('services.switchUpdate');
        Route::resource('services', 'ServiceController');

        //Reason resource
        Route::post('reasons/switchUpdate', 'ReasonController@switchUpdate')->name('reasons.switchUpdate');
        Route::resource('reasons', 'ReasonController');

        //Timing resource
        Route::post('timings/switchUpdate', 'TimingController@switchUpdate')->name('timings.switchUpdate');
        Route::resource('timings', 'TimingController');

        //Terms & Condition resource
        Route::post('terms/switchUpdate', 'TermsPolicyController@switchUpdate')->name('terms.switchUpdate');
        Route::resource('terms', 'TermsPolicyController');

        //States resource
        Route::post('states/switchUpdate', 'StateController@switchUpdate')->name('states.switchUpdate');
        Route::resource('states', 'StateController');

        //Cities resource
        Route::post('cities/switchUpdate', 'CityController@switchUpdate')->name('cities.switchUpdate');
        Route::resource('cities', 'CityController');

        //Barber resource
        Route::post('barbers/switchUpdate', 'BarberController@switchUpdate')->name('barbers.switchUpdate');
        Route::resource('barbers', 'BarberController');
        Route::post('slot/switchUpdate', 'BarberController@slotSwitchUpdate')->name('slot.switchUpdate');
        // Route::get('barber/showBarberProfile/{id}', 'BarberController@showBarberProfile')->name('barber.profileShow');

        //User resource
        Route::post('users/switchUpdate', 'UserController@switchUpdate')->name('users.switchUpdate');
        Route::resource('users', 'UserController');

        //Category resource
        Route::post('categories/switchUpdate', 'CategoryController@switchUpdate')->name('categories.switchUpdate');
        Route::resource('categories', 'CategoryController');
        Route::post('getSubcategory/ajax', 'CategoryController@getSubcategory')->name('getSubcategory'); //Ajax for get subcategories

        //Sub-Category resource
        Route::post('subcategories/switchUpdate', 'SubcategoryController@switchUpdate')->name('subcategories.switchUpdate');
        Route::resource('subcategories', 'SubcategoryController');

        // ajax route
        Route::post('ajax/city', 'BarberController@getCities')->name('ajax.city');
    });
});
