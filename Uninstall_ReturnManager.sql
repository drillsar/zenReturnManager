SELECT @t4:=configuration_group_id
FROM configuration_group
WHERE configuration_group_title= 'Return Manager';
DELETE FROM configuration WHERE configuration_group_id = @t4 AND configuration_group_id != 0;
DELETE FROM configuration_group WHERE configuration_group_id = @t4 AND configuration_group_id !=0;

DELETE FROM admin_pages WHERE page_key = 'configReturnMan' LIMIT 1;
DELETE FROM admin_pages WHERE page_key = 'ReturnManager' LIMIT 1;

DELETE FROM orders_status WHERE orders_status_name = 'RMA# Issued' LIMIT 1;
DELETE FROM orders_status WHERE orders_status_name = 'Cancel Item' LIMIT 1;

DROP TABLE order_return_manager;

