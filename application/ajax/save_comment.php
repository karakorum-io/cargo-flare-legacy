<?php

/**
 * Ajax handler file for handling all ajax related operations
 * from CargoFlare.com
 *
 * @author Shahrukh
 * @copyright CargoFlare
 */

require_once "init.php";

if (validate_id($_SESSION["member_id"]) && validate_id($_POST["id"])) {
    $insert = array(
        "order_id" => (int) $_POST["id"]
        , "administrator_id" => $_SESSION["member_id"]
        , "content" => $_POST["comment"]
        , "register_date" => date("Y-m-d H:i:s"),
    );
    if (!$daffny->DB->insert("orders_comments", $insert)) {
        $code = "1";
        $message = "DB error";
    } else {
        $comment_id = $daffny->DB->get_insert_id();
        $comment = $daffny->DB->select_one("a.*, DATE_FORMAT(a.register_date, '%m/%d/%Y %h:%i %p') AS register_date_format, CONCAT(b.first_name, ' ', b.first_name) AS owner_name", "orders_comments a INNER JOIN administrators b ON b.id = a.administrator_id", " WHERE a.id = '" . $comment_id . "'");
        $comment["is_delete_visible"] = "block";
        $code = "0";

        $daffny->tpl->path = ROOT_PATH . "cp/templates";
        $message = $daffny->tpl->build("orders.comment", $comment);
    }
    $return = array(
        "code" => $code
        , "message" => $message,
    );
    echo json_encode($return);
}

require_once "done.php";
