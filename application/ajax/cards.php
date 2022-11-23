<?php

/**
 * ajax.php Ajax handler file for handling all ajax related operations
 * from CargoFlare.com
 *
 * @author Shahrukh
 * @copyright CargoFlare
 */

require_once "init.php";

$memberId = (int) $_SESSION['member_id'];
$out = array('success' => false, 'message' => "", 'data' => '');

$ID = (int) post_var("id");
if ($memberId <= 0) {
    $out = array('success' => false);
} else {

    switch ($_POST['action']) {
        case "save":{
                //insert
                $err = array();
                $arr = array(
                    "owner_id" => getParentId()
                    , "cc_fname" => post_var("cc_fname")
                    , "cc_lname" => post_var("cc_lname")
                    , "cc_address" => post_var("cc_address")
                    , "cc_city" => post_var("cc_city")
                    , "cc_state" => post_var("cc_state")
                    , "cc_zip" => post_var("cc_zip")
                    , "cc_number" => post_var("cc_number")
                    , "cc_month" => post_var("cc_month")
                    , "cc_year" => post_var("cc_year")
                    , "cc_type" => (int) post_var("cc_type")
                    , "cc_cvv2" => post_var("cc_cvv2"),
                );
                if (!count($err)) {
                    if ($GLOBALS['CONF']['DES_ENCRYPT']) {
                        $arr['cc_number'] = "DES_ENCRYPT('" . $arr['cc_number'] . "', '" . $daffny->cfg['security_salt'] . "')";
                    }
                    $ins_arr = $daffny->DB->PrepareSql("app_creditcards", $arr);
                    try {
                        $daffny->DB->transaction("start");
                        if ($ID == 0) {
                            $q = $daffny->DB->insert("app_creditcards", $ins_arr);
                            $message = "Card has been added";
                        } else {
                            if (strpos($ins_arr['cc_number'], "*") !== false || strlen($ins_arr['cc_number']) == 4) {
                                unset($ins_arr['cc_number']);
                            }
                            if (strpos($ins_arr['cc_cvv2'], "*") !== false) {
                                unset($ins_arr['cc_cvv2']);
                            }
                            $q = $daffny->DB->update("app_creditcards", $ins_arr, "id='" . $ID . "' AND owner_id = '" . getParentId() . "'");
                            $message = "Card has been updated";
                        }
                        $daffny->DB->transaction("commit");
                        if ($GLOBALS['CONF']['DES_ENCRYPT']) {
                            $results = $daffny->DB->selectRows("*, DES_DECRYPT(cc_number, '" . $daffny->cfg['security_salt'] . "') AS cc_number", "app_creditcards", "WHERE owner_id='" . getParentId() . "'");
                        } else {
                            $results = $daffny->DB->selectRows("*, cc_number AS cc_number", "app_creditcards", "WHERE owner_id='" . getParentId() . "'");
                        }
                        $daffny->tpl->cards = array();
                        if (!empty($results)) {
                            foreach ($results as $key => $value) {
                                $value['cc_number'] = hideCCNumber($value['cc_number']);
                                $daffny->tpl->cards[] = $value;
                            }
                        }

                        $data = $daffny->tpl->build("myaccount.billing.cards", array());

                        $out = array(
                            'success' => true
                            , 'data' => $data
                            , 'message' => $message,
                        );
                    } catch (Exception $e) {
                        $daffny->DB->transaction("rollback");
                        $out = array(
                            'success' => false
                            , 'data' => ""
                            , 'message' => "Access denied.",
                        );
                    }
                } else {
                    $out = array(
                        'success' => false
                        , 'data' => ""
                        , 'message' => "Form is not valid. Please check.",
                    );
                }
            }
            break;
        case "load":{
                if ($GLOBALS['CONF']['DES_ENCRYPT']) {
                    $data = $daffny->DB->selectRow("*, DES_DECRYPT(cc_number, '" . $daffny->cfg['security_salt'] . "') AS cc_number", "app_creditcards", "WHERE owner_id='" . getParentId() . "' AND id='" . $ID . "'");
                } else {
                    $data = $daffny->DB->selectRow("*, cc_number AS cc_number", "app_creditcards", "WHERE owner_id='" . getParentId() . "' AND id='" . $ID . "'");
                }

                if (!empty($data)) {
                    $data['cc_number'] = (isset($_SESSION['admin_here']) && $_SESSION['admin_here'] === true) ? $data['cc_number'] : hideCCNumber($data['cc_number']);
                    if (strlen($data['cc_cvv2']) == 3) {
                        $data['cc_cvv2'] = "***";
                    } else {
                        $data['cc_cvv2'] = "****";
                    }

                    $out = $data;
                    $out['success'] = true;
                    $out['message'] = "Data has been loaded.";
                } else {
                    $out = array(
                        'success' => false
                        , 'data' => ""
                        , 'message' => "Access Denied.",
                    );
                }
            }
            break;
        case "getcombo":{
                if ($GLOBALS['CONF']['DES_ENCRYPT']) {
                    $data = $daffny->DB->selectRows("*, DES_DECRYPT(cc_number, '" . $daffny->cfg['security_salt'] . "') AS cc_number", "app_creditcards", "WHERE owner_id='" . getParentId() . "'");
                } else {
                    $data = $daffny->DB->selectRows("*, cc_number AS cc_number", "app_creditcards", "WHERE owner_id='" . getParentId() . "'");
                }
                $set = $daffny->DB->selectRow("id, billing_cc_id", "app_defaultsettings", "WHERE owner_id='" . getParentId() . "'");

                if (!empty($data) && !empty($set)) {

                    $selector = "<option value=\"\">--Select one--</option>";
                    foreach ($data as $key => $value) {
                        $s = ($value['id'] == $set['billing_cc_id']) ? ' selected="selected"' : "";
                        $selector .= "<option value=\"" . $value['id'] . "\" $s>" . hideCCNumber($value['cc_number']) . "</option>";
                    }

                    $out = array(
                        'success' => true
                        , 'data' => $selector
                        , 'message' => "Data has been loaded.",
                    );
                } else {
                    $out = array(
                        'success' => true
                        , 'data' => "<option value=\"\">--Select one--</option>"
                        , 'message' => "Empty.",
                    );
                }
            }
            break;
    }
}
echo json_encode($out);
require_once "done.php";
