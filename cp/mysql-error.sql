--Wed, 09 Jun 2021 05:11:13 -0400
--Error:Table 'cargo_flare.id' doesn't exist
SHOW COLUMNS FROM id

--Fri, 11 Jun 2021 15:05:18 -0400
--Error:Table 'cargo_flare.product' doesn't exist
SELECT COUNT(l.id) cnt FROM licenses l
				, orders o
				,product p
				, `orders_details` od
				, member m
				, app_defaultsettings ds
				, app_company_profile cp  WHERE l.`order_id` = o.`id` AND l.`owner_id` = m.`id` AND od.`order_id` = o.`id` AND od.`product_id` = p.`id` AND cp.`owner_id` = m.`id` AND ds.`owner_id` = m.`id` AND p.`type_id` != 2 AND MONTH(l.`created`) = MONTH(NOW())

--Fri, 11 Jun 2021 15:05:18 -0400
--Error:Table 'cargo_flare.product' doesn't exist
SELECT l.`id`
				, p.`name` as `product_name`
				, l.`users`
				, m.`id` as `member_id`
				, m.`contactname`
				, m.`companyname`
				, l.`created`
				, l.`expire`
				, ds.`billing_autopay`
				, IF(l.`expire` > NOW(), 'Active', 'Expired') as status
				, cp.is_frozen
		 FROM licenses l
				, orders o
				,product p
				, `orders_details` od
				, member m
				, app_defaultsettings ds
				, app_company_profile cp  WHERE l.`order_id` = o.`id` AND l.`owner_id` = m.`id` AND od.`order_id` = o.`id` AND od.`product_id` = p.`id` AND cp.`owner_id` = m.`id` AND ds.`owner_id` = m.`id` AND p.`type_id` != 2 AND MONTH(l.`created`) = MONTH(NOW()) LIMIT 0, 100 

