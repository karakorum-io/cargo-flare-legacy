<?php

/**
 * Cronjob to expire quotes older than 30 days
 */

require_once("init.php");

$daffny->DB->query("START TRANSACTION");

$sql = "UPDATE app_entities SET status = 23 WHERE type = 2 AND status = 1 AND deleted = 0 AND est_ship_date <= CURRENT_DATE - INTERVAL 30 DAY";
$daffny->DB->query($sql);

$sql = "UPDATE app_order_header SET status = 23 WHERE type = 2 AND status = 1 AND deleted = 0 AND est_ship_date <= CURRENT_DATE - INTERVAL 30 DAY";
$daffny->DB->query($sql);

$daffny->DB->query("COMMIT");

echo "CRON ENDED!";
require_once("done.php");