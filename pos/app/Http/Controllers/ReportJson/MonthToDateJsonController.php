<?php

namespace App\Http\Controllers\ReportJson;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Reports\MonthToDateRequest as QueryRequest;

class MonthToDateJsonController extends Controller
{
    public function index(QueryRequest $request)
    {
        $data = [
            "start_date" => $request->filter['start_date'],
            "end_date" => $request->filter['end_date'],
        ];

        $salesOrders = DB::select(DB::raw(
            "
			SELECT
                sales_orders.id,
                DATE(sales_orders.ordered_at) as ordered_at,
                sales_items.qty as qty,
                sales_items.dpp as dpp,
                sales_items.tax as item_tax,
                sales_items.unit_price as unit_price,
                sales_items.discount_amount as discount_amount,
                sales_items.discount_type as discount_type,
                sales_items.description as description,
                IF (LOWER(sales_items.discount_type) LIKE 'percentage'
                    , sales_items.discount_amount
                    , (sales_items.discount_amount / sales_items.qty)
                ) as disc_per_unit,
                CASE
                    WHEN LOWER(sales_items.item_type) = 'promotion' THEN 'Promotion'
                    WHEN LOWER(sales_items.item_type) = 'student enrollment' THEN 'Student enrollment'
                    ELSE items.category
                END as category
            FROM sales_items
            LEFT JOIN sales_orders ON sales_orders.id = sales_items.sales_order_id
            LEFT JOIN items ON items.id = sales_items.item_id
            WHERE 
                sales_orders.ordered_at >= :sales_items_start_date
                AND sales_orders.ordered_at <= :sales_items_end_date

            UNION ALL

            SELECT
                sales_orders.id,
                DATE(sales_orders.ordered_at) as ordered_at,
                sales_returns.qty * -1 as qty,
                sales_items.dpp as dpp,
                sales_returns.tax_amount as item_tax,
                sales_items.unit_price as unit_price,
                sales_items.discount_amount as discount_amount,
                sales_items.discount_type as discount_type,
                sales_items.description as description,
                IF (LOWER(sales_items.discount_type) LIKE 'percentage'
                    , sales_items.discount_amount
                    , (sales_items.discount_amount / sales_items.qty)
                ) as disc_per_unit,
                CASE
                    WHEN LOWER(sales_items.item_type) = 'promotion' THEN 'Promotion'
                    WHEN LOWER(sales_items.item_type) = 'student enrollment' THEN 'Student enrollment'
                    ELSE items.category
                END as category
            FROM sales_returns
            LEFT JOIN sales_orders ON sales_orders.id = sales_returns.sales_order_id
            LEFT JOIN sales_items ON sales_items.id = sales_returns.sales_item_id
            LEFT JOIN items ON items.id = sales_items.item_id
            WHERE
                sales_orders.ordered_at >= :sales_returns_start_date
                AND sales_orders.ordered_at <= :sales_returns_end_date
            "
        ), [
            "sales_items_start_date" => $request->filter['start_date'],
            "sales_items_end_date" => $request->filter['end_date'] . ' 23:59:59',
            "sales_returns_start_date" => $request->filter['start_date'],
            "sales_returns_end_date" => $request->filter['end_date'] . ' 23:59:59'
        ]);

        $generalSetting = DB::select(DB::raw(
            "
            SELECT
                general_settings.company_name as company_name,
                general_settings.company_email as company_email,
                general_settings.company_phone as company_phone,
                general_settings.company_address as company_address,
                general_settings.tax_payer as tax_payer,
                general_settings.tax_number as tax_number,
                general_settings.affirmation_date as affirmation_date,
                general_settings.tax_toggle as tax_toggle,
                general_settings.tax_amount as tax_amount
            FROM general_settings
            "
        ));

        return response()->json([
            'data' => $data,
            'sales_orders' => $salesOrders,
            'general_setting'=> $generalSetting
        ], 200);
    }
}
