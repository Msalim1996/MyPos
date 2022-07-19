<?php

namespace App\Http\Controllers\ReportJson;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReprintJsonController extends Controller
{
    public function index(Request $request, $salesOrderId)
    {
        $salesItems = DB::select(DB::raw(
            "
            SELECT
                sales_orders.id,
                sales_orders.order_ref_no,
                sales_orders.ordered_at as ordered_at,
                promotions.benefit_type as promotion_type,
                items.id as item_id,
                items.sku as sku,
                items.name as item_name,
                sales_items.qty as qty,
                sales_items.description as description,
                sales_items.unit_price as unit_price,
                sales_items.discount_type,
                sales_items.discount_amount,
                sales_items.tax as item_tax,
                sales_items.dpp as dpp,
                IF (LOWER(sales_items.discount_type) LIKE 'percentage'
                    , sales_items.discount_amount
                    , (sales_items.discount_amount / sales_items.qty)
                ) as disc_per_unit,
                sales_payments.type as payment_type,
                '' as sales_item_types,
                CASE
                    WHEN LOWER(sales_items.item_type) = 'item' THEN items.name
                    WHEN LOWER(sales_items.item_type) = 'promotion' THEN promotions.name
                    WHEN LOWER(sales_items.item_type) = 'student enrollment' THEN CONCAT(members.member_id , ' - ', student_classes.class_id,' (',courses.name,')')
                    ELSE items.name
                END as sales_item_name,
                sales_items.item_type
            FROM sales_items
            LEFT JOIN sales_orders ON sales_orders.id = sales_items.sales_order_id
            LEFT JOIN items ON items.id = sales_items.item_id
            LEFT JOIN promotions on promotions.id = sales_items.item_id
            LEFT JOIN student_enrollments on student_enrollments.id = sales_items.item_id
            LEFT JOIN members on members.id = student_enrollments.member_id
            LEFT JOIN student_classes on student_classes.id = student_enrollments.student_class_id
            LEFT JOIN courses ON courses.id = student_classes.course_id
            LEFT JOIN sales_payments ON sales_payments.id = (
                SELECT
                    id
                FROM sales_payments
                WHERE sales_payments.sales_order_id = sales_orders.id
                LIMIT 1
            )
            WHERE sales_orders.id = :sales_item_sales_order_id
                            
            UNION ALL
            
            SELECT
                sales_orders.id,
                sales_orders.order_ref_no,
                sales_orders.ordered_at as ordered_at,
                promotions.benefit_type as promotion_type,
                items.id as item_id,
                items.sku as sku,
                items.name as item_name,
                sales_returns.qty * -1 as qty,
                sales_items.description as description,
                sales_items.unit_price as price,
                sales_items.discount_type,
                sales_items.discount_amount,
                sales_returns.tax_amount as amount_tax,
                sales_items.dpp / sales_items.qty * sales_returns.qty * -1 as dpp,
                IF (LOWER(sales_items.discount_type) LIKE 'percentage'
                    , sales_items.discount_amount
                    , (sales_items.discount_amount / sales_items.qty)
                ) as disc_per_unit,
                sales_payments.type as payment_type,
                'REFUND' as sales_item_types,
                CASE
                    WHEN LOWER(sales_items.item_type) = 'item' THEN items.name
                    WHEN LOWER(sales_items.item_type) = 'promotion' THEN promotions.name
                    WHEN LOWER(sales_items.item_type) = 'student enrollment' THEN CONCAT(members.member_id , ' - ', student_classes.class_id,' (',courses.name,')')
                    ELSE items.name
                END as sales_item_name,
                sales_items.item_type
            FROM sales_returns
            LEFT JOIN sales_orders ON sales_orders.id = sales_returns.sales_order_id
            LEFT JOIN sales_items ON sales_items.id = sales_returns.sales_item_id
            LEFT JOIN items ON items.id = sales_items.item_id
            LEFT JOIN promotions on promotions.id = sales_items.item_id
            LEFT JOIN student_enrollments on student_enrollments.id = sales_items.item_id
            LEFT JOIN members on members.id = student_enrollments.member_id
            LEFT JOIN student_classes on student_classes.id = student_enrollments.student_class_id
            LEFT JOIN courses ON courses.id = student_classes.course_id
            LEFT JOIN sales_payments ON sales_payments.id = (
                SELECT
                    id
                FROM sales_payments
                WHERE sales_payments.sales_order_id = sales_orders.id
                LIMIT 1
            )
            WHERE sales_orders.id = :sales_return_sales_order_id
            "
        ), [
            "sales_item_sales_order_id" => $salesOrderId,
            "sales_return_sales_order_id" => $salesOrderId
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
            'tickets' => $tickets,
            'members' => $members,
            'general_setting' => $generalSetting
        ], 200);
    }
}
