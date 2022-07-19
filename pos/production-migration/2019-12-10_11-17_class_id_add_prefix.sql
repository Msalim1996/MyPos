-- This migration adds prefix for class_id

INSERT INTO db_number_counters
values(7,'C',NULL,NULL,NULL,1,NOW(),NOW());

INSERT INTO db_number_counters
values(8,'TMPS',NULL,NULL,NULL,1,NOW(),NOW());