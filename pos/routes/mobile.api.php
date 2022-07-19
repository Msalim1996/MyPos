<?php

use Illuminate\Http\Request;

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

Route::fallback(function () {
    return response()->json(['message' => 'API tidak ditemukan!'], 404);
});

Route::options('{any?}', function (){
    return response('',200);
})->where('any', '.*');

Route::group(['middleware' => ['json.response']], function () {
    /**
     * Authentication & Authorization
     */
    
    // public routes
    Route::post('/login', 'Api\AuthController@login')->name('login.api');
    
    // private routes
    Route::middleware('auth:api')->group(function () {
        Route::get('/check-token', 'Api\AuthController@checkToken');
        Route::get('/logout', 'Api\AuthController@logout')->name('logout');
    });
    
    /**
     * !! Legacy code for java 8 flap gate
     * Java 8 API
     * Gate controller
     */
    Route::get('j8/gate/check-in/{barcode_id}', 'Api\J8GateController@checkIn');
    Route::get('j8/gate/check-out/{barcode_id}', 'Api\J8GateController@checkOut');
    Route::get('j8/gate-rink/check-in/{barcode_id}', 'Api\J8GateRinkController@checkIn');
    Route::get('j8/gate-rink/check-out/{barcode_id}', 'Api\J8GateRinkController@checkOut');
    
    // Gate Access Control
    Route::post('/gate/check-in', 'Api\GateController@checkIn');
    Route::post('/gate/check-out', 'Api\GateController@checkOut');
    
    Route::post('/gate-rink/check-in', 'Api\GateRinkController@checkIn');
    Route::post('/gate-rink/check-out', 'Api\GateRinkController@checkOut');
    
    /**
     * Available API action / API event / Other resources
     */
    // private routes
    Route::middleware('auth:api')->group(function () {
        Route::get('/current-user', 'Api\UserController@getUserInfo');
        Route::get('/current-user/role', 'Api\UserController@getUserRole');
        Route::get('/current-user/permission', 'Api\UserController@getUserPermission');
        
        Route::post('/barcodes/activate', 'Api\BarcodeController@activateBarcodes');
        Route::post('/barcode/is-allowed-to-rent-skate', 'Api\BarcodeController@isAllowedToRentSkate');
        Route::post('/barcode/is-barcode-registered', 'Api\BarcodeController@isActivated');
        Route::post('/barcode/get-type', 'Api\BarcodeController@getType');
        
        Route::get('/cashier-orders/search/{id}', 'Api\CashierOrderController@searchOrder')->where('id', '.*');
        Route::get('/cashier-orders/recent-order/{amount?}', 'Api\CashierOrderController@getRecentOrder');
        
        Route::get('/class-schedules', 'Api\ClassScheduleController@index');
        
        Route::patch('/coaches/{id}/restore', 'Api\CoachController@restore');
        Route::patch('/courses/{id}/restore', 'Api\CourseController@restore');
        Route::patch('/customers/{id}/restore', 'Api\CustomerController@restore');
        
        Route::get('/courses/on-going-classes', 'Api\CourseController@getOnGoingClasses');
        
        Route::get('/discount-types', 'Api\SalesOrderController@getDiscountTypes');
        
        Route::post('/gate-transaction/filter', 'Api\GateTransactionController@filter');
        Route::get('/gate-transaction/today-on-going/in', 'Api\GateTransactionController@todayOnGoingIn');
        Route::get('/gate-transaction/today-on-going/out', 'Api\GateTransactionController@todayOnGoingOut');
        Route::post('/gate-rink-transaction/filter', 'Api\GateRinkTransactionController@filter');
        Route::get('/gate-rink-transaction/today-on-going/in', 'Api\GateRinkTransactionController@todayOnGoingIn');
        Route::get('/gate-rink-transaction/today-on-going/out', 'Api\GateRinkTransactionController@todayOnGoingOut');
        
        // only 1 general setting record exists for the application
        Route::get('/general-setting/gate-control-type', 'Api\GeneralSettingController@gateControlTypeList');
        Route::get('/general-setting', 'Api\GeneralSettingController@index');
        Route::put('/general-setting', 'Api\GeneralSettingController@update');
        
        Route::patch('/items/{id}/restore', 'Api\ItemController@restore');
        Route::get('/items-stock','Api\ItemController@getItemStocks');
        Route::get('/item-types', 'Api\ItemController@getItemTypes');
        
        Route::get('/language-list', 'Api\LanguageListController@index');
        Route::patch('/locations/{id}/restore', 'Api\LocationController@restore');
        
        Route::get('/members/member-id/{id}', 'Api\MemberController@getByMemberId');
        Route::post('/members/temporary-student','Api\MemberController@temporaryStudent');
        Route::patch('/members/{id}/restore', 'Api\MemberController@restore');

        Route::get('/payment-methods', 'Api\PaymentMethodController@index');
        Route::post('/payment-methods', 'Api\PaymentMethodController@batchUpdate');

        Route::put('/purchase-orders/{purchaseOrderId}/fulfillment', 'Api\PurchaseOrderController@fulfill');
        Route::put('/purchase-orders/{purchaseOrderId}/payment', 'Api\PurchaseOrderController@pay');

        Route::put('/sales-orders/{salesOrderId}/fulfillment', 'Api\SalesOrderController@fulfill');
        Route::put('/sales-orders/{salesOrderId}/payment', 'Api\SalesOrderController@pay');

        Route::get('/student-enrollments/search/{id}','Api\StudentEnrollmentController@searchStudentEnrollment');
        
        Route::get('/shortcut-items/today', 'Api\ShortcutProductController@getTodayItems');
        Route::get('/shortcut-items/date/{date}', 'Api\ShortcutProductController@getItemsByDate');

        Route::post('/skate/size', 'Api\SkateController@getBySize');
        Route::get('/skates/reset', 'Api\SkateController@resetAllSkates');

        Route::post('/skate-rental/is-allowed-to/{mode}', 'Api\SkateController@isAllowedTo');

        Route::post('/skate-rental/exchange', 'Api\SkateController@exchangeSkate');
        Route::post('/skate-rental/rent', 'Api\SkateController@rentSkate');
        Route::post('/skate-rental/return', 'Api\SkateController@returnSkate');

        Route::get('/skate-transaction/barcode/{barcodeId}', 'Api\SkateTransactionBarcodeController@getByBarcodeId');
        Route::get('/skate-transaction/date/{date}', 'Api\SkateTransactionController@getTransactionsByDate');
        Route::get('/skate-transaction/rent-status/{skateTransactionId}', 'Api\SkateTransactionBarcodeController@getRentStatus');
        Route::get('/skate-transaction/today', 'Api\SkateTransactionController@getTodayTransactions');

        Route::post('/skating-aid/rent', 'Api\SkatingAidController@rentSkatingAid');
        Route::post('/skating-aid/upgrade', 'Api\SkatingAidController@upgradeSkatingAid');
        Route::post('/skating-aid/return', 'Api\SkatingAidController@returnSkatingAid');

        Route::get('/skating-aid-transaction/sales-order-ref-no/{salesOrderRefNo}', 'Api\SkatingAidTransactionBarcodeController@getBySalesOrderRefNo')->where('salesOrderRefNo', '.*');
        Route::get('/skating-aid-transaction/date/{date}', 'Api\SkatingAidTransactionController@getTransactionsByDate');
        Route::get('/skating-aid-transaction/rent-status/{skatingAidTransactionId}', 'Api\SkatingAidTransactionBarcodeController@getRentStatus');
        Route::get('/skating-aid-transaction/today', 'Api\SkatingAidTransactionController@getTodayTransactions');

        Route::get('/stocks', 'Api\StockController@index');

        Route::get('/student-classes/member/{memberId}', 'Api\StudentClassController@getClassesByMemberId');

        Route::patch('/promotions/{id}/restore', 'Api\PromotionController@restore');
        Route::patch('/member-discounts/{id}/restore', 'Api\MemberDiscountController@restore');
        Route::patch('/users/{id}/restore', 'Api\UserController@restore');
        Route::patch('/suppliers/{id}/restore', 'Api\SupplierController@restore');
        Route::patch('/skating-aid-transactions/{id}/extend-time', 'Api\SkatingAidTransactionController@extendTime');
    });
    
    /**
     * Resources
     */
    // private routes
    Route::middleware('auth:api')->group(function () {
        
        Route::apiResource('/adjust-orders','Api\AdjustOrderController');
        Route::apiResource('/barcodes', 'Api\BarcodeController');
        Route::apiResource('/barcode-types', 'Api\BarcodeTypeController');
        Route::apiResource('/cashier-orders', 'Api\CashierOrderController');
        Route::apiResource('/coaches', 'Api\CoachController');
        Route::apiResource('/courses', 'Api\CourseController');
        Route::apiResource('/customers', 'Api\CustomerController');
        Route::apiResource('/customer-addresses', 'Api\CustomerAddressController');
        Route::apiResource('/db-number-counters', 'Api\DbNumberCounterController');
        Route::apiResource('/extended-shift-days', 'Api\ExtendedShiftDayController');
        Route::apiResource('/gate-rink-transactions', 'Api\GateRinkTransactionController');
        Route::apiResource('/gate-transactions', 'Api\GateTransactionController');
        Route::apiResource('/gs-time-schedules', 'Api\GSTimeScheduleController');
        Route::apiResource('/items', 'Api\ItemController');
        Route::apiResource('/level-groups', 'Api\LevelGroupController');
        Route::apiResource('/levels', 'Api\LevelController');
        Route::apiResource('/locations', 'Api\LocationController');
        Route::apiResource('/members', 'Api\MemberController');
        Route::apiResource('/member-discounts', 'Api\MemberDiscountController');
        Route::apiResource('/movement-histories','Api\MovementHistoryController');
        Route::apiResource('/promotions', 'Api\PromotionController');
        Route::apiResource('/purchase-orders', 'Api\PurchaseOrderController');
        Route::apiResource('/skates', 'Api\SkateController');
        Route::apiResource('/sales-orders', 'Api\SalesOrderController');
        Route::apiResource('/skate-transactions', 'Api\SkateTransactionController');
        Route::apiResource('/shifts', 'Api\ShiftController');
        Route::apiResource('/shortcut-day-types', 'Api\ShortcutDayTypeController');
        Route::apiResource('/skating-aids', 'Api\SkatingAidController');
        Route::apiResource('/skating-aid-transactions', 'Api\SkatingAidTransactionController');
        Route::apiResource('/student-classes', 'Api\StudentClassController');
        Route::apiResource('/student-enrollments', 'Api\StudentEnrollmentController');
        Route::apiResource('/suppliers', 'Api\SupplierController');
        Route::apiResource('/supplier-addresses', 'Api\SupplierAddressController');
        Route::apiResource('/transfer-orders','Api\TransferOrderController');

        // ACL Api resources
        Route::apiResource('/users', 'Api\UserController');
        Route::get('role-has-permissions', 'Api\RoleHasPermissionController@index');
    });
});
