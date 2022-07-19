-- This migration updates ref no name for adjust order type

SET SQL_SAFE_UPDATES = 0;

UPDATE
	db_number_counters DNC
SET
    DNC.type = 'AO'
WHERE
    DNC.id = 5;