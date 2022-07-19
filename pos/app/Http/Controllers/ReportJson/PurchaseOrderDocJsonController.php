<?php

namespace App\Http\Controllers\ReportJson;

use App\Http\Controllers\Controller;
use App\Utils\NumberToAmountConverter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderDocJsonController extends Controller
{
    public function index(Request $request, $purchaseOrderId)
    {
        $dataArr = DB::select(DB::raw(
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
                supplier_addresses.name as supp_addr_name,
                supplier_addresses.street as supp_addr_street,
                supplier_addresses.city as supp_addr_city,
                supplier_addresses.state as supp_addr_state,
                supplier_addresses.zip as supp_addr_zip,
                supplier_addresses.country as supp_addr_country,
                supplier_addresses.remark as supp_addr_remark,
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
            LEFT JOIN supplier_addresses ON supplier_addresses.id = purchase_orders.supplier_address_id
            LEFT JOIN locations ON locations.id = purchase_orders.location_id
            LEFT JOIN purchase_items on purchase_items.purchase_order_id = purchase_orders.id
            LEFT JOIN items ON items.id = purchase_items.item_id
            WHERE purchase_orders.id = :purchase_order_id
            ORDER BY purchase_orders.ordered_at, purchase_items.position_index
            "
        ), [
            "purchase_order_id" => $purchaseOrderId
        ]);

        // total is used for the final total "terbilang"
        $total = 0;
        foreach($dataArr as $record) {
            $terbilang = ($record->purchase_item_subtotal == null ? "" : NumberToAmountConverter::terbilang($record->purchase_item_subtotal));
            $record->terbilang_subtotal = $terbilang;
            $total += $record->purchase_item_subtotal;
        }

        foreach($dataArr as $record) {
            // for each record, repeat "terbilang total" to be used in report
            $record->terbilang_total = NumberToAmountConverter::terbilang($total);
        }

        return response()->json(
            $dataArr
        , 200);
    }
}
