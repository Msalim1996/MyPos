<?php

namespace App\Http\Controllers\ReportJson;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Reports\CashierCollectionRequest as QueryRequest;

class CashierCollectionJsonController extends Controller
{
    public function index(QueryRequest $request)
    {
        $data = [
            "username" => $request->filter['username'],
            "start_date" => $request->filter['start_date'],
            "end_date" => $request->filter['end_date'],
        ];

        $salesOrders = DB::select(DB::raw(
            "
            SELECT
                sales_orders.id,
                sales_orders.order_ref_no,
                DATE(sales_orders.ordered_at) as ordered_at,
                TIME(sales_orders.ordered_at) as order_time,
                promotions.benefit_type as promotion_type,
                items.id as item_id,
                CASE
                    WHEN LOWER(sales_items.item_type) = 'promotion' THEN ''
                    WHEN LOWER(sales_items.item_type) = 'item' THEN items.sku
                    ELSE items.sku
                END as item_sku,
                items.name,
                sales_items.dpp as dpp,
                sales_items.tax as item_tax,
                CASE
                    WHEN LOWER(sales_items.item_type) = 'promotion' THEN 'Promotion'
                    WHEN LOWER(sales_items.item_type) = 'student enrollment' THEN 'Student enrollment'
                    ELSE items.category
                END as category,
                sales_items.qty as qty,
                sales_items.discount_amount as discount_amount,
                sales_items.discount_type as discount_type,
                sales_items.item_type as item_type,
                sales_items.description as description,
                users.id as user_id,
                users.username as username,
                users.name as user_name,
                audits.auditable_id as auditable_id,
                audits.id as audit_id,
                IF (LOWER(sales_items.discount_type) LIKE 'percentage'
                    , sales_items.discount_amount
                    , (sales_items.discount_amount / sales_items.qty)
                ) as disc_per_unit,
                sales_items.unit_price as unit_price,
                sales_payments.type as payment_type,
                '' as sales_item_types,
                CASE
                    WHEN LOWER(sales_items.item_type) = 'item' THEN items.name
                    WHEN LOWER(sales_items.item_type) = 'promotion' THEN promotions.name
                    WHEN LOWER(sales_items.item_type) = 'student enrollment' THEN CONCAT(members.member_id , ' - ', student_classes.class_id,' (',courses.name,')')
                    ELSE items.name
                END as sales_item_name
            FROM sales_items
            LEFT JOIN sales_orders ON sales_orders.id = sales_items.sales_order_id
            LEFT JOIN items ON items.id = sales_items.item_id
            LEFT JOIN sales_payments ON sales_payments.sales_order_id = sales_orders.id AND sales_payments.type NOT IN ('Refund', 'Change')
            LEFT JOIN audits ON audits.auditable_id = sales_orders.id AND audits.auditable_type LIKE '%SalesOrder' AND audits.event = 'created'
            LEFT JOIN users ON users.id = audits.user_id
            LEFT JOIN promotions on promotions.id = sales_items.item_id
            LEFT JOIN student_enrollments on student_enrollments.id = sales_items.item_id
            LEFT JOIN members on members.id = student_enrollments.member_id
            LEFT JOIN student_classes on student_classes.id = student_enrollments.student_class_id
            LEFT JOIN courses ON courses.id = student_classes.course_id
            WHERE 
                sales_orders.ordered_at >= :sales_items_start_date
                AND sales_orders.ordered_at <= :sales_items_end_date
                AND users.username LIKE :sales_items_username
                            
            UNION ALL
            
            SELECT
                sales_orders.id,
                sales_orders.order_ref_no,
                DATE(sales_orders.ordered_at) as ordered_at,
                TIME(sales_orders.ordered_at) as order_time,
                promotions.benefit_type as promotion_type,
                items.id as item_id,
                CASE
                    WHEN LOWER(sales_items.item_type) = 'promotion' THEN ''
                    WHEN LOWER(sales_items.item_type) = 'item' THEN items.sku
                    ELSE items.sku
                END as item_sku,
                items.name,
                sales_items.dpp / sales_items.qty * sales_returns.qty * -1 as dpp,
                sales_returns.tax_amount as amount_tax,
                CASE
                    WHEN LOWER(sales_items.item_type) = 'promotion' THEN 'Promotion'
                    WHEN LOWER(sales_items.item_type) = 'student enrollment' THEN 'Student enrollment'
                    ELSE items.category
                END as category,
                sales_returns.qty * -1 as qty,
                sales_items.discount_amount as discount_amount,
                sales_items.discount_type as discount_type,
                sales_items.item_type as item_type,
                sales_items.description as description,
                users.id as user_id,
                users.username as username,
                users.name as user_name,
                audits.auditable_id as auditable_id,
                audits.id as audit_id,
                IF (LOWER(sales_items.discount_type) LIKE 'percentage'
                    , sales_items.discount_amount
                    , (sales_items.discount_amount / sales_items.qty)
                ) as disc_per_unit,
                sales_items.unit_price as unit_price,
                sales_payments.type as payment_type,
                'REFUND' as sales_item_types,
                CASE
                    WHEN LOWER(sales_items.item_type) = 'item' THEN items.name
                    WHEN LOWER(sales_items.item_type) = 'promotion' THEN promotions.name
                    WHEN LOWER(sales_items.item_type) = 'student enrollment' THEN CONCAT(members.member_id , ' - ', student_classes.class_id,' (',courses.name,')')
                    ELSE items.name
                END as sales_item_name
            FROM sales_returns
            LEFT JOIN sales_orders ON sales_orders.id = sales_returns.sales_order_id
            LEFT JOIN sales_items ON sales_items.id = sales_returns.sales_item_id
            LEFT JOIN items ON items.id = sales_items.item_id
            LEFT JOIN sales_payments ON sales_payments.sales_order_id = sales_orders.id AND sales_payments.type NOT IN ('Refund', 'Change')
            LEFT JOIN audits ON audits.auditable_id = sales_orders.id AND audits.auditable_type LIKE '%SalesOrder' AND audits.event = 'created'
            LEFT JOIN users ON users.id = audits.user_id
            LEFT JOIN promotions on promotions.id = sales_items.item_id
            LEFT JOIN student_enrollments on student_enrollments.id = sales_items.item_id
            LEFT JOIN members on members.id = student_enrollments.member_id
            LEFT JOIN student_classes on student_classes.id = student_enrollments.student_class_id
            LEFT JOIN courses ON courses.id = student_classes.course_id
            WHERE
                sales_orders.ordered_at >= :sales_returns_start_date
                AND sales_orders.ordered_at <= :sales_returns_end_date
                AND users.username LIKE :sales_returns_username
            "
        ), [
            "sales_items_start_date" => $request->filter['start_date'],
            "sales_items_end_date" => $request->filter['end_date'] . ' 23:59:59',
            "sales_items_username" => $request->filter['username'],
            "sales_returns_start_date" => $request->filter['start_date'],
            "sales_returns_end_date" => $request->filter['end_date'] . ' 23:59:59',
            "sales_returns_username" => $request->filter['username'],
        ]);

        $tickets = DB::select(DB::raw(
            "
            SELECT
                barcode_id,
                session_name,
                session_day,
                session_start_time,
                session_end_time,
                sales_order_id
            FROM barcodes
            LEFT JOIN sales_orders on sales_orders.id = barcodes.sales_order_id
            WHERE
                sales_orders.ordered_at >= :tickets_start_date
                AND sales_orders.ordered_at <= :tickets_end_date
            "
        ), [
            "tickets_start_date" => $request->filter['start_date'],
            "tickets_end_date" => $request->filter['end_date'] . ' 23:59:59'
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
            'tickets' => $tickets,
            'general_setting'=> $generalSetting
        ], 200);
    }
}
