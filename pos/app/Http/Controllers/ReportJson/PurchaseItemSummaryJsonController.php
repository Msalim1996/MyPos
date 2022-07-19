<?php

namespace App\Http\Controllers\ReportJson;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Reports\SalesOrderSummaryRequest as QueryRequest;

class PurchaseItemSummaryJsonController extends Controller
{
    public function index(QueryRequest $request)
    {
        $data = [
            "start_date" => $request->filter['start_date'],
            "end_date" => $request->filter['end_date'],
        ];

        $purchaseItemsSummary = DB::select(DB::raw(
                "
                SELECT 
                    CONCAT(items.name, ' - ', items.sku) as item_fullname,
                    items.SKU as sku,
                    items.name as name,
                    items.uom as uom,
                    purchase_items.qty as quantity,
                    (purchase_items.qty * purchase_items.unit_price) as subtotal,
                    purchase_items.created_at as date
                FROM purchase_items 
                LEFT JOIN items ON purchase_items.item_id = items.id
                LEFT JOIN purchase_orders ON purchase_orders.id = purchase_items.purchase_order_id
                WHERE 
                    purchase_orders.ordered_at >= :purchase_orders_start_date
                    AND purchase_orders.ordered_at <= :purchase_orders_end_date
                "
            ), [
                "purchase_orders_start_date" => $request->filter['start_date'],
                "purchase_orders_end_date" => $request->filter['end_date'] . ' 23:59:59',
            ]);

            return response()->json([
                'data' => $data,
                'purchase_items' => $purchaseItemsSummary
            ]);
    }
}
