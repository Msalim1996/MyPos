-- This migration updates sales_order_ref_no based on sales_order_id on skating_aid_transactions_table

SET SQL_SAFE_UPDATES = 0;

UPDATE
	skating_aid_transactions SAT,
    sales_orders SO
SET
    SAT.sales_order_ref_no = SO.order_ref_no
WHERE
    SAT.sales_order_id = SO.id;