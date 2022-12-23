<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authenticate;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Utility;
use App\Http\Controllers\Company;
use App\Http\Controllers\Banner;
use App\Http\Controllers\Category;
use App\Http\Controllers\Customer;
use App\Http\Controllers\FaqManagement;
use App\Http\Controllers\DeliveryManagement;
use App\Http\Controllers\ReviewManagement;
use App\Http\Controllers\DeliveryAgent;
use App\Http\Controllers\ProductDesign;
use App\Http\Controllers\CouponManagement;
use App\Http\Controllers\Settings;
use App\Http\Controllers\ContactUs;
use App\Http\Controllers\PushNotification;
use App\Http\Controllers\Reports;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Alteration;

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
        Route::match(['get', 'post'],'app-settings', [Settings::class, 'appSettings'])->name('app-settings');

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



// Route::post('forgot-password', [Authenticate::class, 'forgotPassword'])->name('forgot-password');
// Route::post('expire-otp', [Authenticate::class, 'otpExpire'])->name('otp-expire');
// Route::match(['get', 'post'],'validate-otp', [Authenticate::class, 'validateOtp'])->name('send-otp');

// Route::group(['middleware' => ['auth']], function () {
// 	Route::post('generic-status-change-delete', [Utility::class, 'genericDeleteAndStatusChange'])->name('company-details');
// 	Route::post('generic-approval-change', [Utility::class, 'genericApprovalChange'])->name('approval-change');
//    // Route::get('dashboard', [Dashboard::class, 'index'])->name('dashboard');


// 	Route::prefix('cms-management')->group(function()
// 	{
// 		/*company details*/
// 		Route::match(['get', 'post'],'/company-details', [Company::class, 'companyList'])->name('company-details');
// 		Route::post('ajax-cms-table', [Company::class, 'ajaxCmsDataTable'])->name('company-ajax-table');
// 		Route::get('/company-details/add', [Company::class, 'companyDetailsAdd'])->name('company-details.add');
// 		Route::post('/company-details/save', [Company::class, 'companyDetailSave'])->name('company-details.save');
// 		Route::get('/company-details/edit/{id}', [Company::class, 'companyDetailsAdd'])->name('company-details.edit');
// 		/*banner list*/
// 		Route::match(['get', 'post'],'/banner-list', [Banner::class, 'bannerList'])->name('banner-list');
// 		Route::get('/banner-add', [Banner::class, 'bannerAdd'])->name('banner-add');
// 		Route::get('/banner-edit/{id}', [Banner::class, 'bannerAdd'])->name('banner-edit');
// 		Route::post('/banner-save', [Banner::class, 'bannerSave'])->name('banner-save');
// 		Route::post('ajax-banner-table', [Banner::class, 'ajaxBannerDataTable'])->name('banner-ajax-table');
// 		/*contact-us*/
// 		Route::match(['get', 'post'],'/queries', [ContactUs::class, 'queries'])->name('queries');
// 		Route::match(['get', 'post'],'/contact-us', [ContactUs::class, 'contactUs'])->name('contact-us');
// 	});
// 	Route::prefix('customer-alteration-request')->group(function()
// 	{
// 		/*company details*/
// 		Route::match(['get', 'post'],'/list', [Alteration::class, 'alterationList'])->name('alteration-list');
// 		/*Route::post('ajax-cms-table', [Company::class, 'ajaxCmsDataTable'])->name('company-ajax-table');
// 		Route::get('/company-details/add', [Company::class, 'companyDetailsAdd'])->name('company-details.add');
// 		Route::post('/company-details/save', [Company::class, 'companyDetailSave'])->name('company-details.save');*/
// 		Route::get('/view-details/{id}', [Alteration::class, 'alterationListEdit'])->name('alteration-edit');
// 		/*banner list*/
// 		Route::match(['get', 'post'],'/banner-list', [Banner::class, 'bannerList'])->name('banner-list');
// 		Route::get('/banner-add', [Banner::class, 'bannerAdd'])->name('banner-add');
// 		Route::get('/banner-edit/{id}', [Banner::class, 'bannerAdd'])->name('banner-edit');
// 		Route::post('/banner-save', [Banner::class, 'bannerSave'])->name('banner-save');
// 		Route::post('ajax-banner-table', [Banner::class, 'ajaxBannerDataTable'])->name('banner-ajax-table');
// 		/*contact-us*/
// 		Route::match(['get', 'post'],'/queries', [ContactUs::class, 'queries'])->name('queries');
// 		Route::match(['get', 'post'],'/contact-us', [ContactUs::class, 'contactUs'])->name('contact-us');
// 	});

// 	Route::prefix('category-management')->group(function()
// 	{
// 		Route::match(['get','post'],'list', [Category::class, 'categoryList']);
// 		Route::get('add', [Category::class, 'categoryAdd']);
// 		Route::get('export-category-list', [Category::class, 'exportFile'])->name('export-category-list');
// 		Route::post('ajax-table', [Category::class, 'ajaxDataTable'])->name('category-ajax-table');
// 		Route::get('edit/{id}', [Category::class, 'categoryAdd']);
// 	    Route::match(['get', 'post'],'measurement-list/{id}', [Category::class, 'measurementList'])->name('category-measurement-list');
//         Route::get('measurement-add/{productDesignId}', [Category::class, 'measurementAdd'])->name('category-measurement-add');
//         Route::post('measurement-delete', [Category::class, 'measurementDelete'])->name('category-measurement-delete');

// 	});
// 	Route::prefix('faq-management')->group(function()
// 	{
// 		Route::match(['get', 'post'],'/list', [FaqManagement::class, 'faqList'])->name('faq-list');
// 		Route::get('/add', [FaqManagement::class, 'faqAdd'])->name('faq-add');
// 		Route::get('/edit/{id}', [FaqManagement::class, 'faqAdd'])->name('faq-edit');
// 		Route::post('ajax-table', [FaqManagement::class, 'ajaxDataTable'])->name('faq-ajax-table');
// 	});
// 	Route::prefix('delivery-management')->group(function()
// 	{
// 		Route::match(['get', 'post'],'/list', [DeliveryManagement::class, 'deliveryList'])->name('delivery-list');
// 		Route::get('add', [DeliveryManagement::class, 'deliveryAdd'])->name('delivery-add');
// 		Route::get('edit/{id}', [DeliveryManagement::class, 'deliveryAdd'])->name('delivery-edit');
// 		Route::post('ajax-delivery-table', [DeliveryManagement::class, 'ajaxDeliveryDataTable'])->name('delivery-ajax-table');
// 	});
// 	Route::prefix('delivery-agent')->group(function()
// 	{
// 		Route::match(['get', 'post'],'list', [DeliveryAgent::class, 'deliveryAgentList'])->name('delivery-agents-list');
// 		Route::get('add', [DeliveryAgent::class, 'deliveryAgentAdd'])->name('delivery-agents-add');
// 		Route::get('edit/{id}', [DeliveryAgent::class, 'deliveryAgentAdd'])->name('delivery-agents-edit');
// 		Route::match(['get', 'post'],'change-password/{id?}', [DeliveryAgent::class, 'deliveryAgentChangePassword'])->name('delivery-agents-change-password');
// 		Route::post('ajax-table', [DeliveryAgent::class, 'ajaxDataTable'])->name('delivery-agent-ajax-table');
// 		Route::get('export-agent-list', [DeliveryAgent::class, 'exportFile'])->name('export-agent-list');
// 		Route::post('get-states-by-agent-country', [DeliveryAgent::class, 'getState']);
// 		Route::post('get-cities-by-agent-state', [DeliveryAgent::class, 'getCity']);
// 	});
// 	Route::prefix('orders')->group(function()
// 	{
// 		Route::match(['get', 'post'],'list', [OrderManagement::class, 'orderList'])->name('orders-list');
// 		/*Route::get('add', [OrderManagement::class, 'deliveryAgentAdd'])->name('delivery-agents-add');*/
// 		Route::get('invoice/{id}', [OrderManagement::class, 'CustomerOrderDetailsInvoice'])->name('orders-invoice');
// 	    Route::get('export-order-list', [OrderManagement::class, 'exportFile'])->name('export-order-list');
// 		Route::get('edit/{id}', [OrderManagement::class, 'orderAdd'])->name('orders-edit');
// 		Route::get('show/{id}', [OrderManagement::class, 'ordershow'])->name('orders-show');
// 		Route::post('ajax-order-table', [OrderManagement::class, 'ajaxOrderDataTable'])->name('orders-ajax-table');
// 		Route::get('export-transaction-list', [OrderManagement::class, 'exportTransactionFile'])->name('export-transaction-list');
// 	});
// 	Route::prefix('review-management')->group(function()
// 	{
// 		Route::match(['get', 'post'],'list', [ReviewManagement::class, 'reviewList'])->name('delivery-list');
// 		Route::get('add', [ReviewManagement::class, 'reviewAdd'])->name('review-add');
// 		Route::get('edit/{id}', [ReviewManagement::class, 'reviewAdd'])->name('review-edit');
// 	});
// 	Route::prefix('product-design-management')->group(function()
// 	{
// 		Route::match(['get', 'post'],'list', [ProductDesign::class, 'productDesignList'])->name('product-design-list');
// 		Route::post('ajax-product-design-table', [ProductDesign::class, 'ajaxDataTableProductDesign'])->name('ajax-product-design-table');
// 		Route::get('add', [ProductDesign::class, 'productDesignAdd'])->name('product-design-add');
// 		Route::get('edit/{id}', [ProductDesign::class, 'productDesignAdd'])->name('product-design-edit');
// 		Route::match(['get', 'post'],'view-gallery/{id}', [ProductDesign::class, 'productDesignGalleryView'])->name('product-design-gallery-view');
// 		Route::match(['get', 'post'],'addon-list/{id}', [ProductDesign::class, 'addonList'])->name('product-design-addon-list');
// 		/*Route::match(['get', 'post'],'addon-list/{id}', [ProductDesign::class, 'addonList'])->name('product-design-addon-list');
// 		Route::get('addon-add/{id}', [ProductDesign::class, 'addonAdd'])->name('product-design-addon-add');*/
// 		/*Route::match(['get', 'post'],'addon-list', [ProductDesign::class, 'addonList'])->name('product-design-addon-list');*/
// 		Route::get('addon-add/{productDesignId}', [ProductDesign::class, 'addonAdd'])->name('product-design-addon-add');
// 		Route::get('addon-edit/{productDesignId}/{id}', [ProductDesign::class, 'addonAdd'])->name('product-design-addon-edit');
// 	});
// 	Route::prefix('coupon-management')->group(function()
// 	{
// 		Route::match(['get', 'post'],'/list', [CouponManagement::class, 'couponList'])->name('coupon-list');
//                 Route::match(['get', 'post'],'/assign/{id?}', [CouponManagement::class, 'couponAssign'])->name('coupon-assign');
//                 Route::post('/assign-user', [CouponManagement::class, 'couponAssignAdd'])->name('coupon-assign-add');
//                 Route::get('/assign-user-delete/{id}', [CouponManagement::class, 'couponAssigndelete'])->name('coupon-assign-delete');
// 		Route::get('add', [CouponManagement::class, 'couponAdd'])->name('coupon-add');
// 		Route::get('edit/{id}', [CouponManagement::class, 'couponAdd'])->name('coupon-edit');
// 		Route::post('ajax-coupon-table', [CouponManagement::class, 'ajaxCouponDataTable'])->name('coupon-ajax-table');
// 			/*Route::get('export-c-list', [DeliveryAgent::class, 'exportFile'])->name('export-agent-list');*/
// 	});
// 	Route::prefix('reports')->group(function()
// 	{
// 		/*customer report*/
// 		Route::match(['get', 'post'],'customer-report', [Reports::class, 'customerReportList'])->name('customer-report');
// 		Route::post('ajax-customer-report-table', [Reports::class, 'ajaxCustomerReportDataTable'])->name('ajax-customer-report-table');
// 		/*agent report*/
// 		Route::match(['get', 'post'],'agent-report', [Reports::class, 'agentReportList'])->name('agent-report');
// 		Route::post('ajax-agent-report-table', [Reports::class, 'ajaxAgentReportDataTable'])->name('ajax-agent-report-table');
// 		Route::match(['get', 'post'],'orders-report', [Reports::class, 'ordersReportList'])->name('orders-report');
// 		Route::post('ajax-orders-report-table', [Reports::class, 'ajaxOrdersReportDataTable'])->name('ajax-orders-report-table');
// 		/*Route::get('/company-details/add', [Company::class, 'companyDetailsAdd'])->name('company-details.add');
// 		Route::post('/company-details/save', [Company::class, 'companyDetailSave'])->name('company-details.save');
// 		Route::get('/company-details/edit/{id}', [Company::class, 'companyDetailsAdd'])->name('company-details.edit');*/
// 		/*Route::match(['get', 'post'],'/banner-list', [Banner::class, 'bannerList'])->name('banner-list');
// 		Route::get('/banner-add', [Banner::class, 'bannerAdd'])->name('banner-add');
// 		Route::get('/banner-edit/{id}', [Banner::class, 'bannerAdd'])->name('banner-edit');
// 		Route::post('/banner-save', [Banner::class, 'bannerSave'])->name('banner-save');
// 		Route::post('ajax-banner-table', [Banner::class, 'ajaxBannerDataTable'])->name('banner-ajax-table');
// 		Route::match(['get', 'post'],'/queries', [ContactUs::class, 'queries'])->name('queries');
// 		Route::match(['get', 'post'],'/contact-us', [ContactUs::class, 'contactUs'])->name('contact-us');*/
// 		Route::match(['get', 'post'],'transaction-report', [Reports::class, 'transactionList'])->name('Transaction-report');
//         Route::post('ajax-transaction-report-table', [Reports::class, 'ajaxTransactionReportDataTable'])->name('ajax-transaction-report-table');

// 	});
// 	/*Route::prefix('push-notification')->group(function()
// 	{*/
// 		Route::match(['get','post'],'push-notification', [PushNotification::class, 'notification'])->name('notification');
// 		/*Route::get('export-c-list', [DeliveryAgent::class, 'exportFile'])->name('export-agent-list');*/
// 	/*});*/
// 	  Route::match(['get', 'post'],'notification-list', [PushNotification::class, 'notificationList'])->name('notification-list');
//                 Route::post('ajax-notification-list', [PushNotification::class, 'ajaxNotificationDataTable'])->name('ajax-notification-list');

// });

