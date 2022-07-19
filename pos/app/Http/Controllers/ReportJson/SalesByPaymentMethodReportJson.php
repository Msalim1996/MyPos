<?php

namespace App\Http\Controllers\ReportJson;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\SalesByPaymentMethodRequest as StoreRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesByPaymentMethodReportJson extends Controller
{
    public function index(StoreRequest $request)
    {
        $data = [
            "start_date" => $request->filter['start_date'],
            "end_date" => $request->filter['end_date'],
        ];

        $salesPayments = DB::select(DB::raw(
            "
            SELECT
                COUNT(sales_payments.type) as qty,
                sales_payments.type as type,
                SUM(sales_payments.amount) as total
            FROM sales_payments
            LEFT JOIN sales_orders on sales_payments.sales_order_id = sales_orders.id
            WHERE 
                sales_orders.ordered_at >= :sales_orders_start_date AND
                sales_orders.ordered_at <= :sales_orders_end_date
            GROUP BY sales_payments.type
            "
        ), [
            "sales_orders_start_date" => $request->filter['start_date'] . ' 00:00:00',
            "sales_orders_end_date" => $request->filter['end_date'] . ' 23:59:59',
        ]);

        return response()->json([
            'data' => $data,
            'sales_payments' => $salesPayments,
        ], 200);
    }
}
