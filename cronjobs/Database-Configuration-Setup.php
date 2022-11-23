<?php

require_once("init.php");
$sql = "SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))";
$res = $daffny->DB->query($sql);

echo "<pre>";
print_r($res);
echo "Configured!";
require_once("done.php");