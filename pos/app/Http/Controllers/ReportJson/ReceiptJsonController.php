<?php

namespace App\Http\Controllers\ReportJson;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceiptJsonController extends Controller
{
    public function index(Request $request, $salesOrderId)
    {
        $salesItems = DB::select(DB::raw(
            "
            SELECT 
                sales_orders.id,
                sales_orders.order_ref_no,
                sales_orders.ordered_at,
                promotions.benefit_type as promotion_type,
                sales_items.tax as item_tax,
                sales_items.position_index,
                sales_items.description,
                sales_items.item_type,
                sales_items.qty,
                sales_items.dpp,
                sales_items.unit_price,
                sales_items.discount_type,
                sales_items.discount_amount,
                IF (LOWER(sales_items.discount_type) LIKE 'percentage'
                    , sales_items.discount_amount
                    , (sales_items.discount_amount / sales_items.qty)
                ) as disc_per_unit,
                CASE
                    WHEN LOWER(sales_items.item_type) = 'item' THEN items.name
                    WHEN LOWER(sales_items.item_type) = 'promotion' THEN promotions.name
                    WHEN LOWER(sales_items.item_type) = 'student enrollment' THEN CONCAT(members.member_id , ' - ', student_classes.class_id,' (',courses.name,')')   
                    ELSE items.name
                END as item_name
            FROM sales_orders
            LEFT JOIN sales_items on sales_items.sales_order_id = sales_orders.id
            LEFT JOIN items ON items.id = sales_items.item_id
            LEFT JOIN promotions ON promotions.id = sales_items.item_id
            LEFT JOIN student_enrollments ON student_enrollments.id = sales_items.item_id
            LEFT JOIN members ON members.id = student_enrollments.member_id
            LEFT JOIN student_classes ON student_classes.id = student_enrollments.student_class_id
            LEFT JOIN courses ON courses.id = student_classes.course_id
            WHERE sales_orders.id = :sales_order_id
            ORDER BY sales_items.position_index
            "
        ), [
            "sales_order_id" => $salesOrderId
        ]);

        $salesPayments = DB::select(DB::raw(
            "
            SELECT 
                sales_payments.description,
                sales_payments.payment_date,
                sales_payments.amount,
                sales_payments.type
            FROM sales_payments
            WHERE sales_payments.sales_order_id = :sales_order_id
            ORDER BY sales_payments.payment_date
            "
        ), [
            "sales_order_id" => $salesOrderId
        ]);

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
            'sales_payments' => $salesPayments,
            'tickets' => $tickets,
            'members' => $members,
            'general_setting' => $generalSetting
        ], 200);
    }
}
