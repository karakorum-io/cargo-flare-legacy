<?php

require_once 'config.php';

session_start();

if($_GET['id'] && $_GET['hash']){
    $connection = mysqli_connect($CONF['my_host'], $CONF['my_user'], $CONF['my_pass'], $CONF['my_base']);
    $query = "UPDATE entity_email_log SET status = 1, updated_at = '".date('Y-m-d h:i:s')."' WHERE entity_id = ".$_GET['id']." AND hash = '".$_GET['hash']."'";
    $result = mysqli_query($connection, $query);

    echo "iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z/C/HgAGgwJ/lK3Q6wAAAABJRU5ErkJggg==";
}