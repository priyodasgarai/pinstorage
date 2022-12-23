<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authenticate;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Banner;
use App\Http\Controllers\Customer;
use App\Http\Controllers\PageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('index');
Route::get('login', [HomeController::class, 'login'])->name('login');
Route::get('sign-up', [HomeController::class, 'signUp'])->name('sign-up');
Route::get('contact-us', [HomeController::class, 'contactUs'])->name('contact.us');
Route::get('about-us', [HomeController::class, 'aboutUs'])->name('about-us');
Route::get('otp-verify/{id}', [HomeController::class, 'otpVerify'])->name('otp.verify');
Route::get('send-otp', [HomeController::class, 'sendOtp'])->name('send-otp');
Route::post('web-user-register', [Authenticate::class, 'webUserRegister'])->name('web-user-register');
Route::post('web-user-check', [Authenticate::class, 'webUserCheck'])->name('web-user-check');
Route::post('user-post-login', [Authenticate::class, 'userPostlogin'])->name('user.post.login');
Route::group(['middleware' => ['auth']], function () {
    Route::get('logout', [Authenticate::class, 'userLogout'])->name('user.logout');
});




Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/', [Authenticate::class, 'login'])->name('login');
    Route::post('user-check', [Authenticate::class, 'userCheck'])->name('user-check');
    Route::group(['middleware' => ['Admin']], function () {
        Route::get('logout', [Authenticate::class, 'logout'])->name('logout');
        Route::get('dashboard', [Dashboard::class, 'index'])->name('dashboard');
        Route::get('change-password', [Dashboard::class, 'changePassword'])->name('change-password');
        Route::post('password-update', [Dashboard::class, 'passwordUpdate'])->name('password-update');
        Route::get('edit-profile', [Dashboard::class, 'editProfile'])->name('edit-profile');
        Route::post('profile-update', [Dashboard::class, 'profileUpdate'])->name('profile-update');

        Route::group(['prefix' => 'customer-management', 'as' => 'customer-management.'],function()	{
            Route::match(['get', 'post'],'list', [Customer::class, 'customerList'])->name('list');
            Route::get('add', [Customer::class, 'customerAdd'])->name('add');
            Route::get('edit/{id}', [Customer::class, 'customerAdd'])->name('edit');
            Route::post('ajax-table', [Customer::class, 'ajaxDataTable'])->name('ajax-table');
            Route::get('export-customer-list', [Customer::class, 'exportFile'])->name('export-list');
            Route::post('status-change', [Customer::class, 'statusChange'])->name('status.change');
        });
        Route::group(['prefix' => 'slider-management', 'as' => 'slider-management.'], function () {
        Route::match(['get', 'post'],'list', [Banner::class, 'sliderList'])->name('list');
		Route::get('add', [Banner::class, 'sliderAdd'])->name('add');
		Route::get('edit/{id}', [Banner::class, 'sliderAdd'])->name('edit');
        Route::post('save', [Banner::class, 'sliderSave'])->name('save');
		Route::post('ajax-banner-table', [Banner::class, 'ajaxSliderDataTable'])->name('ajax-table');
        Route::post('status-change', [Banner::class, 'statusChange'])->name('status.change');
        });
        Route::group(['prefix' => 'page-management', 'as' => 'page-management.'], function () {
            Route::match(['get', 'post'],'list', [PageController::class, 'pageList'])->name('list');
            Route::get('add', [PageController::class, 'pageAdd'])->name('add');
            Route::get('edit/{id}', [PageController::class, 'pageAdd'])->name('edit');
            Route::post('save', [PageController::class, 'pageSave'])->name('save');
            Route::post('ajax-page-table', [PageController::class, 'ajaxPageDataTable'])->name('ajax-table');
            Route::post('status-change', [PageController::class, 'statusChange'])->name('status.change');
            });
    });
});
