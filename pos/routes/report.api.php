<?php

Route::fallback(function () {
    return response()->json(['message' => 'Report tidak ditemukan!'], 404);
});

/**
 * Return JSON data for further reporting purposes
 */
Route::middleware('auth:api')->group(function () {
    Route::get('/attendance-transactions.json', 'ReportJson\AttendanceTransactionJsonController@index');
    Route::get('/cashier-collection.json', 'ReportJson\CashierCollectionJsonController@index');
    Route::get('/cashier-collection-summary.json', 'ReportJson\CashierCollectionSummaryJsonController@index');
    Route::get('/cashier-receipt.json/{salesOrderId}', 'ReportJson\ReceiptJsonController@index');
    Route::post('/cashier-refund.json/{salesOrderId}', 'ReportJson\RefundJsonController@index');
    Route::get('/cashier-reprint.json/{salesOrderId}', 'ReportJson\ReprintJsonController@index');
    Route::get('/coach-commissions.json', 'ReportJson\CoachCommissionJsonController@index');
    Route::get('/coach-schedules.json', 'ReportJson\CoachScheduleJsonController@index');
    Route::get('/sales-by-payment-method.json', 'ReportJson\SalesByPaymentMethodReportJson@index');
    Route::get('/sales-order-summaries.json', 'ReportJson\SalesOrderSummaryReportJson@index');
    Route::get('/tickets.json', 'ReportJson\TicketListJsonController@index');
    Route::get('/tickets-summary.json', 'ReportJson\TicketListSummaryJsonController@index');
    Route::get('/members.json', 'ReportJson\MemberJsonController@index');
    Route::get('/month-to-date-sales.json', 'ReportJson\MonthToDateJsonController@index');
    Route::get('/purchase.json', 'ReportJson\PurchaseJsonController@index');
    Route::get('/purchase-items.json', 'ReportJson\PurchaseItemJsonController@index');
    Route::get('/purchase-items-summary.json', 'ReportJson\PurchaseItemSummaryJsonController@index');
    Route::get('/purchase-order.json/{purchaseOrderId}', 'ReportJson\PurchaseOrderDocJsonController@index');
    Route::get('/skating-aid-transactions-daily.json', 'ReportJson\SkatingAidDailyJsonController@index');
    Route::get('/class-schedules.json', 'ReportJson\StudentAttendanceJsonController@index');
    Route::get('/posisi-hutang-akademi.json', 'ReportJson\PosisiHutangAkademiJsonController@index');
});
