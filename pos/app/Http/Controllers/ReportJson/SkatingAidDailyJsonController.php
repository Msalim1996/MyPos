<?php

namespace App\Http\Controllers\ReportJson;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Reports\SkatingAidDailyRequest as QueryRequest;

class SkatingAidDailyJsonController extends Controller
{
    public function index(QueryRequest $request)
    {
        $data = [
            "start_date" => $request->filter['start_date'],
            "end_date" => $request->filter['end_date'],
        ];

        $skatingAidDaily = DB::select(DB::raw(
            "
            SELECT 
                sales_orders.id,
                sales_orders.order_ref_no,
                DATE(sales_orders.ordered_at) as ordered_at,
                TIME(sales_orders.ordered_at) as order_time,
                skating_aids.skating_aid_code as skating_aid_code,
                skating_aid_transactions.rent_start as rent_start,
                skating_aid_transactions.rent_end as rent_end,
                skating_aid_transactions.upgraded as upgraded,
                skating_aid_transactions.upgraded_name as upgraded_name,
                skating_aid_transactions.description as description,
                skating_aid_transactions.reason as reason,
                skating_aid_transactions.extended_time as extended_time
            FROM skating_aids
            LEFT JOIN skating_aid_transactions ON skating_aids.id = skating_aid_transactions.skating_aid_id
            LEFT JOIN sales_orders ON skating_aid_transactions.sales_order_id = sales_orders.id
            WHERE
                skating_aid_transactions.created_at >= :skating_aid_transactions_start_date
                AND skating_aid_transactions.created_at <= :skating_aid_transactions_end_date
            ORDER BY skating_aid_transactions.created_at ASC
            "
        ), [
            "skating_aid_transactions_start_date" => $request->filter['start_date'],
            "skating_aid_transactions_end_date" => $request->filter['end_date'] . ' 23:59:59',
        ]);

        return response()->json([
            'data' => $data,
            'skating_aid_daily' => $skatingAidDaily,
        ], 200);
    }
}
