<?php

namespace App\Http\Controllers\ReportJson;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RefundJsonController extends Controller
{
    /**
     * 
     *!! RECEIVE DATA by POST REQUEST
     * {
     *  "sales_returns": [
     *      {
     *          "location_id": 1,
     *          "qty": 2.000000,
     *          "sales_return_id": 7
     *      },
     *      {
     *          "location_id": 1,
     *          "qty": 1.000000,
     *          "sales_return_id": 6
     *      }
     *  ],
     *  "sales_payment": {
     *      "amount": -27000.00000000
     *  },
     *  "tickets": [
     *      {
     *          "id": 10,
     *          "barcode_id": "P00000002",
     *          "session_name": "Sesi 2",
     *          "session_day": "Tuesday",
     *          "session_start_time": "13:00:00",
     *          "session_end_time": "15:30:00",
     *          "active_on": "2019-11-12 13:00:01"
     *      }
     *   ]
     *  }
     * 
     *!! OUTPUT INTO
     * 
     * {
     *     "cashier": "IT Admin",
     *     "sales_items": [
     *         {
     *             "id": 7,
     *             "order_ref_no": "SO/2019/10/3",
     *             "ordered_at": "2019-10-24 15:34:51",
     *             "position_index": 1,
     *             "description": "",
     *             "qty": "159.000000",
     *             "item_name": "Ticket ku app",
     *             "price": "100000.00",
     *             "sku": null,
     *             "uom": null
     *         },
     *         {
     *             "id": 7,
     *             "order_ref_no": "SO/2019/10/3",
     *             "ordered_at": "2019-10-24 15:34:51",
     *             "position_index": 2,
     *             "description": "",
     *             "qty": "1.000000",
     *             "item_name": "Ticket",
     *             "price": "100000.00",
     *             "sku": null,
     *             "uom": null
     *         }
     *     ],
     *     "sales_payments": [
     *         {
     *             "description": null,
     *             "payment_date": "2019-10-24 15:34:51",
     *             "amount": "10000.00",
     *             "type": "Cash"
     *         },
     *         {
     *             "description": "change",
     *             "payment_date": "2019-10-24 15:34:51",
     *             "amount": "-5000.00",
     *             "type": "Change"
     *         }
     *     ],
     *     "tickets": [
     *         {
     *             "barcode_id": "P001",
     *             "active_on": "2019-10-24 04:00:01",
     *             "session_name": "Sesi 3",
     *             "session_day": "Thursday",
     *             "session_start_time": "04:00:00",
     *             "session_end_time": "06:30:00"
     *         },
     *         {
     *             "barcode_id": "P002",
     *             "active_on": "2019-10-24 04:00:01",
     *             "session_name": "Sesi 3",
     *             "session_day": "Thursday",
     *             "session_start_time": "04:00:00",
     *             "session_end_time": "06:30:00"
     *         },
     *         {
     *             "barcode_id": "P003",
     *             "active_on": "2019-10-24 04:00:01",
     *             "session_name": "Sesi 3",
     *             "session_day": "Thursday",
     *             "session_start_time": "04:00:00",
     *             "session_end_time": "06:30:00"
     *         }
     *     ],
     *     "members": [
     *         {
     *             "member_id": "M12345678"
     *         },
     *         {
     *             "member_id": "M12345678"
     *         },
     *         {
     *             "member_id": "M12345678"
     *         },
     *         {
     *             "member_id": "M12345678"
     *         },
     *         {
     *             "member_id": "M12345678"
     *         },
     *         {
     *             "member_id": "M12345678"
     *         }
     *     ]
     * }
     */
    public function index(Request $request, $salesOrderId)
    {
        $now = Carbon::now();
        
        $salesReturnIds = [];
        array_push($salesReturnIds, $salesOrderId);
        if ($request->sales_returns) {
            for ($index = 0; $index < count($request->sales_returns); $index++) {
                array_push($salesReturnIds, $request->input('sales_returns.' . $index . '.sales_item_id'));
            }
        }
        $salesItems = DB::select(DB::raw(
            "
            SELECT 
                sales_orders.id,
                sales_orders.order_ref_no,
                sales_orders.ordered_at,
                promotions.benefit_type as promotion_type,
                sales_items.position_index,
                sales_items.discount_type,
                sales_items.discount_amount,
                sales_items.description,
                sales_items.tax as item_tax,
                IF (LOWER(sales_items.discount_type) LIKE 'percentage'
                    , sales_items.discount_amount
                    , (sales_items.discount_amount / sales_items.qty)
                ) as disc_per_unit,
                sales_returns.qty,
                items.name as item_name,
                items.price,
                items.sku,
                items.uom
            FROM sales_orders
            LEFT JOIN sales_returns on sales_returns.sales_order_id = sales_orders.id
            LEFT JOIN sales_items on sales_items.id = sales_returns.sales_item_id
            LEFT JOIN items ON items.id = sales_items.item_id
            WHERE sales_orders.id = ?
                AND sales_returns.id IN (" . implode(',', array_fill(0, count($salesReturnIds) - 1, '?')) . ")
            ORDER BY sales_items.position_index
            "
        ), 
            $salesReturnIds
        );

        $salesPayment = [
            'description' => 'Refund',
            'payment_date' => $now,
            'amount' => $request->input('sales_payment.amount'),
            'type' => 'Refund',
            'sales_order_id' => $salesOrderId,
        ];

        $tickets = DB::select(DB::raw(
            "
            SELECT 
                barcodes.barcode_id,
                barcodes.active_on,
                barcodes.session_name,
                barcodes.session_day,
                barcodes.session_start_time,
                barcodes.session_end_time
            FROM barcodes
            WHERE barcodes.sales_order_id = :sales_order_id
            ORDER BY barcodes.barcode_id
            "
        ), [
            "sales_order_id" => $salesOrderId
        ]);

        $members = DB::select(DB::raw(
            "
            SELECT 
                members.member_id
            FROM sales_order_members
            JOIN members ON members.id = sales_order_members.member_id
            WHERE sales_order_members.sales_order_id = :sales_order_id
            ORDER BY sales_order_members.member_id
            "
        ), [
            "sales_order_id" => $salesOrderId
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

        // Retrieve cashier username from SO audits
        $userId = SalesOrder::where('id', $salesOrderId)->firstOrFail()->audits()->first()->user_id;
        $user = User::where('id', $userId)->firstOrFail();

        return response()->json([
            'cashier' => $user->name,
            'sales_items' => $salesItems,
            'sales_payment' => $salesPayment,
            'tickets' => $tickets,
            'members' => $members,
            'general_setting' => $generalSetting
        ], 200);
    }
}
