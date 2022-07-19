<?php

namespace App\Http\Controllers\ReportJson;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Reports\SalesOrderSummaryRequest as QueryRequest;

class PurchaseJsonController extends Controller
{
    public function index(QueryRequest $request)
    {
        $data = [
            "start_date" => $request->filter['start_date'],
            "end_date" => $request->filter['end_date'],
        ];

        $purchase = DB::select(DB::raw(
                "
                SELECT 
                    purchase_orders.id,
                    purchase_orders.purchase_order_ref_no,
                    purchase_orders.ordered_at,
                    purchase_orders.payment_status,
                    purchase_orders.fulfillment_status,
                    locations.name as location_name,
                    suppliers.name as supplier_name,
                    suppliers.phone,
                    purchase_orders.deleted_at,
                    purchase_items.position_index,
                    purchase_items.description,
                    purchase_items.qty,
                    purchase_items.unit_price,
                    purchase_items.item_id,
                    items.name as item_name,
                    items.sku,
                    items.uom,
                    (purchase_items.qty * purchase_items.unit_price) as purchase_item_subtotal
                FROM purchase_orders
                LEFT JOIN suppliers ON suppliers.id = purchase_orders.supplier_id
                LEFT JOIN locations ON locations.id = purchase_orders.location_id
                LEFT JOIN purchase_items on purchase_items.purchase_order_id = purchase_orders.id
                LEFT JOIN items ON items.id = purchase_items.item_id
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
                'purchase_orders' => $purchase
            ]);
    }
}
