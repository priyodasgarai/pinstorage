<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerWebservices;
use App\Http\Controllers\Api\AgentWebservices;
use App\Http\Controllers\Api\CommonServices;
use App\Http\Controllers\Api\PaymentController;

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
Route::prefix('auth-customer')->group(function() {
    
    Route::post('register', [CustomerWebservices::class, 'register']);
    Route::post('login', [CustomerWebservices::class, 'login']);
    /*Route::post('forget-password', [CustomerWebservices::class, 'forget_pass']);*/
    Route::post('validate-otp', [CustomerWebservices::class, 'validateOTP']);
    Route::middleware('auth:api')->post('resend-otp', [CustomerWebservices::class, 'resendOTP']);
    Route::middleware('auth:api')->post('change-password', [CustomerWebservices::class, 'changePassword']);
    Route::middleware('auth:api')->post('logout', [CustomerWebservices::class, 'logout']);
    Route::middleware('auth:api')->post('edit-profile', [CustomerWebservices::class, 'edit_profile']);
    /*Route::middleware('auth:api')->post('product-review', [CustomerWebservices::class, 'product_review']);*/
    Route::middleware('auth:api')->post('customer-query', [CustomerWebservices::class, 'customerQuery']);
    Route::middleware('auth:api')->post('customer-suggetions', [CustomerWebservices::class, 'customerSuggetions']);
    Route::middleware('auth:api')->post('callback-request', [CustomerWebservices::class, 'contactApiList']);
    Route::middleware('auth:api')->post('customer-review-add', [CustomerWebservices::class, 'customerReviewAdd']);
    Route::middleware('auth:api')->post('wishlist', [CustomerWebservices::class, 'wishlist']);
    Route::middleware('auth:api')->post('product-add-wishlist', [CustomerWebservices::class, 'ProductAddWishlist']);
    Route::middleware('auth:api')->post('product-remove-wishlist', [CustomerWebservices::class, 'wishlistRemove']);
    Route::middleware('auth:api')->post('customer-order-add', [CustomerWebservices::class, 'CustomerOrderAdd']);
    Route::middleware('auth:api')->post('customer-reorder', [CustomerWebservices::class, 'CustomerReorder']);
    Route::middleware('auth:api')->post('customer-alteration-order-add', [CustomerWebservices::class, 'CustomerAlterationOrderAdd']);
    Route::middleware('auth:api')->post('customer-my-order', [CustomerWebservices::class, 'CustomerMyOrder']);
    Route::middleware('auth:api')->post('customer-order-details', [CustomerWebservices::class, 'CustomerOrderDetails']);
     Route::get('download-invoice/{id}', [CustomerWebservices::class, 'CustomerOrderDetailsInvoice']);
    Route::middleware('auth:api')->post('add-to-cart', [CustomerWebservices::class, 'addToCart']);
    Route::middleware('auth:api')->post('cart', [CustomerWebservices::class, 'cart']);
    Route::middleware('auth:api')->post('cart-remove-product', [CustomerWebservices::class, 'cartRemove']);
    Route::middleware('auth:api')->post('adjustment-request', [CustomerWebservices::class, 'adjustmentRequest']);
    Route::middleware('auth:api')->post('order-date-reschedule', [CustomerWebservices::class, 'orderDateReschedule']);
    Route::middleware('auth:api')->post('order-cancel', [CustomerWebservices::class, 'orderCanceled']);
    Route::middleware('auth:api')->post('order-paymemt', [CustomerWebservices::class, 'orderPaymemt']);
    Route::middleware('auth:api')->post('order-track', [CustomerWebservices::class, 'orderTrack']);
    Route::middleware('auth:api')->post('alteration-request', [CustomerWebservices::class, 'customerAlterationRequest']);
    Route::middleware('auth:api')->post('alteration-request-list', [CustomerWebservices::class, 'customerAlterationRequestList']);
    Route::middleware('auth:api')->post('alteration-request-accept-denied', [CustomerWebservices::class, 'customerAlterationRequestAcceptDenied']);
    Route::middleware('auth:api')->post('alteration-request-denied', [CustomerWebservices::class, 'customerAlterationRequestDenied']);
    Route::middleware('auth:api')->post('search-history', [CustomerWebservices::class, 'searchHistory']);
     Route::middleware('auth:api')->post('search-coupon', [CustomerWebservices::class, 'searchCoupon']);
    /*Route::middleware('auth:api')->post('addon-list', [CustomerWebservices::class, 'addonList']);*/
    /*Route::post('addon-list', [CommonServices::class, 'addonList']);*/
    Route::middleware('auth:api')->post('wallet-balance', [CustomerWebservices::class, 'walletBalance']);
    Route::middleware('auth:api')->post('notification-list', [CustomerWebservices::class, 'notificationList']);
    Route::middleware('auth:api')->post('clearsearchapi', [CustomerWebservices::class, 'clearSearch']);
    Route::middleware('auth:api')->post('clear-notification', [CustomerWebservices::class, 'clearNotification']);
     Route::middleware('auth:api')->get('cart-wishlists-count', [CustomerWebservices::class, 'cartWishlistsCount']);

});
/*
    Routes: Common Service Routes
    Author : Somnath Bhunia
*/
Route::post('razor-pay', [PaymentController::class, 'razorPay']);
Route::post('get-razor-pay', [PaymentController::class, 'getRazorPay']);
Route::post('push-notification', [CommonServices::class, 'sendNotification']);
Route::post('delivery-services', [CommonServices::class, 'deliveryServiceList']);
Route::post('banner-list', [CommonServices::class, 'bannerList']);
Route::post('image-upload', [CommonServices::class, 'ImageUpload']);
Route::post('forget-password', [CommonServices::class, 'forget_pass']);
Route::post('forget-password-otp-validate', [CommonServices::class, 'forget_pass_otp_validate']);
Route::post('forget-password-resend-otp', [CommonServices::class, 'forget_pass_resend_otp']);
Route::get('user-story-banner', [CommonServices::class, 'user_story_banner']);
Route::get('get-category', [CommonServices::class, 'get_category']);
Route::post('get-sub-category', [CommonServices::class, 'get_sub_category']);
Route::post('product-list', [CommonServices::class, 'product_list']);
Route::post('product-list-top', [CommonServices::class, 'productToplist']);
Route::post('product-design-details', [CommonServices::class, 'productDesignDetails']);
Route::post('product-review', [CommonServices::class, 'product_review']);
//Route::post('product-list-trending', [CommonServices::class, 'product_list']);
Route::post('product-trending-list', [CommonServices::class, 'trendingList']);
Route::post('product-feature-list', [CommonServices::class, 'featuredList']);
//Route::post('product-feature-list', [CommonServices::class, 'featuredList']);
Route::post('product-search', [CommonServices::class, 'product_search']);
Route::get('company/{slug}', [CommonServices::class, 'cms']);
Route::post('page-list', [CommonServices::class, 'pageList']);
Route::post('catagory-variations', [CommonServices::class, 'catagoryVariation']);
Route::post('size-variations', [CommonServices::class, 'sizeByDetails']);
Route::post('country-list', [CommonServices::class, 'countryList']);
Route::post('state-list', [CommonServices::class, 'stateList']);
Route::post('city-list', [CommonServices::class, 'cityList']);
Route::post('addon-list', [CommonServices::class, 'addonList']);
Route::middleware('auth:api')->post('user-address-list', [CommonServices::class, 'userAddressList']);
Route::middleware('auth:api')->post('user-address-add', [CommonServices::class, 'userAddressAdd']);
Route::middleware('auth:api')->post('user-address-edit', [CommonServices::class, 'userAddressEdit']);
Route::post('settings', [CommonServices::class, 'settings']);
Route::post('agent-by-pincode', [CommonServices::class, 'agentByPincode']);


/*
    Routes: Agent Service Routes
    Author : Sourav Bhowmik
*/

Route::prefix('auth-agent')->group(function() {
    //Route::post('register', [CustomerWebservices::class, 'register']);
    Route::post('login', [AgentWebservices::class, 'login']);
    Route::post('pincode-add', [AgentWebservices::class, 'pincodeAdd']);
    Route::middleware('auth:api')->post('validate-otp', [AgentWebservices::class, 'validateOTP']);
    Route::middleware('auth:api')->post('resend-otp', [AgentWebservices::class, 'resendOTP']);
    Route::middleware('auth:api')->post('change-password', [AgentWebservices::class, 'changePassword']);
    Route::middleware('auth:api')->post('update-agent-profile', [AgentWebservices::class, 'getProfileDetails']);
    Route::middleware('auth:api')->post('logout', [AgentWebservices::class, 'logout']);
    Route::middleware('auth:api')->post('measurement-list', [AgentWebservices::class, 'measurementList']);
    Route::middleware('auth:api')->post('order-list', [AgentWebservices::class, 'orderList']);
    Route::middleware('auth:api')->post('order-details', [AgentWebservices::class, 'orderDetails']);
    Route::middleware('auth:api')->post('usermeasurement-add', [AgentWebservices::class, 'UserMeasurementAdd']);
    Route::middleware('auth:api')->post('usermeasurement-list', [AgentWebservices::class, 'UserMeasurementList']);
    Route::middleware('auth:api')->post('measurement-update', [AgentWebservices::class, 'measurementUpdate']);
    Route::middleware('auth:api')->post('send-measurement-otp', [AgentWebservices::class, 'sendMeasurementOtp']);
     Route::middleware('auth:api')->post('ready-for-measurement', [AgentWebservices::class, 'readyForMeasurement']);
    Route::middleware('auth:api')->post('order-status-update', [AgentWebservices::class, 'orderStatusUpdate']);
    Route::middleware('auth:api')->post('order-measurement-list', [AgentWebservices::class, 'orderMeasurementList']);
    Route::middleware('auth:api')->post('verify-otp', [AgentWebservices::class, 'verifyOtp']);
    Route::middleware('auth:api')->get('order-count', [AgentWebservices::class, 'orderCount']);
    Route::middleware('auth:api')->post('order-paymemt', [AgentWebservices::class, 'orderPaymemt']);
    Route::middleware('auth:api')->post('reschedule-measurement', [AgentWebservices::class, 'rescheduleMeasurement']);
    Route::middleware('auth:api')->post('reschedule-deliverydate', [AgentWebservices::class, 'rescheduleDeliveryDate']);
    
});


//Route::get('trending-list', [CommonServices::class, 'trendingList']);

