<?php

namespace App\Http\Controllers\ReportJson;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Reports\PB1Request as QueryRequest;

class PB1JsonController extends Controller
{
    public function index(QueryRequest $request)
    {
        $data = [
            "start_date" => $request->filter['start_date'],
            "end_date" => $request->filter['end_date'],
        ];

        $pb1 = DB::select(DB::raw(
            "
            SELECT
                sales_orders.id,
                sales_orders.order_ref_no as order_ref_no,
                DATE(sales_orders.ordered_at) as ordered_at,
                TIME(sales_orders.ordered_at) as order_time,
                sales_items.qty as qty,
                sales_items.unit_price as unit_price,
                sales_items.discount_type as discount_type,
                sales_items.discount_amount as discount_amount,
                sales_items.dpp as dpp,
                sales_items.tax as tax,
                sales_items.pb1_dpp as pb1_dpp,
                sales_items.pb1_tax as pb1_item_tax,
                sales_items.item_type as item_type,
                sales_items.description as description,
                items.id as item_id,
                CASE
                    WHEN LOWER(sales_items.item_type) = 'promotion' THEN ''
                    WHEN LOWER(sales_items.item_type) = 'item' THEN items.sku
                    ELSE items.sku
                END as item_sku,
                promotions.benefit_type as promotion_type,
                CASE
                    WHEN LOWER(sales_items.item_type) = 'promotion' THEN 'Promotion'
                    WHEN LOWER(sales_items.item_type) = 'student enrollment' THEN 'Student enrollment'
                    ELSE items.category
                END as category,
                audits.auditable_id as auditable_id,
                audits.id as audit_id,
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
                sales_items.is_pb1 = 1
                AND sales_orders.ordered_at >= :sales_items_start_date
                AND sales_orders.ordered_at <= :sales_items_end_date
                            
            UNION ALL
            
            SELECT
                sales_orders.id,
                sales_orders.order_ref_no,
                DATE(sales_orders.ordered_at) as ordered_at,
                TIME(sales_orders.ordered_at) as order_time,
                sales_returns.qty * -1 as qty,
                sales_items.unit_price as unit_price,
                sales_items.discount_type as discount_type,
                sales_items.discount_amount as discount_amount,
                sales_items.dpp / sales_items.qty * sales_returns.qty * -1 as dpp,
                sales_returns.tax_amount as tax,
                sales_items.pb1_dpp / sales_items.qty * sales_returns.qty * -1 as pb1_dpp,
                sales_items.pb1_tax / sales_items.qty * sales_returns.qty * -1 as pb1_item_tax,
                sales_items.item_type as item_type,
                sales_items.description as description,
                items.id as item_id,
                CASE
                    WHEN LOWER(sales_items.item_type) = 'promotion' THEN ''
                    WHEN LOWER(sales_items.item_type) = 'item' THEN items.sku
                    ELSE items.sku
                END as item_sku,
                promotions.benefit_type as promotion_type,
                CASE
                    WHEN LOWER(sales_items.item_type) = 'promotion' THEN 'Promotion'
                    WHEN LOWER(sales_items.item_type) = 'student enrollment' THEN 'Student enrollment'
                    ELSE items.category
                END as category,
                audits.auditable_id as auditable_id,
                audits.id as audit_id,
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
                sales_items.is_pb1 = 1
                AND sales_orders.ordered_at >= :sales_returns_start_date
                AND sales_orders.ordered_at <= :sales_returns_end_date
            "
        ), [
            "sales_items_start_date" => $request->filter['start_date'],
            "sales_items_end_date" => $request->filter['end_date'] . ' 23:59:59',
            "sales_returns_start_date" => $request->filter['start_date'],
            "sales_returns_end_date" => $request->filter['end_date'] . ' 23:59:59'
        ]);

        return response()->json([
            'data' => $data,
            'pb1' => $pb1,
        ], 200);
    }
}
