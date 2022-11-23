<?php

echo "CRON Execution started";

@session_start();

require_once "init.php";

$res = $daffny->DB->hardQuery("SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

print_r($res);

echo "<br>CRON Execution ended";

require_once "done.php";
