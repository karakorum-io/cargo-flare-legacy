<?php

/**
 * Ajax handler file for handling all ajax related operations
 * from CargoFlare.com
 *
 * @author Shahrukh
 * @copyright CargoFlare
 */

require_once "init.php";
$memberId = (int) $_SESSION['member_id'];
$out = array('success' => false);
ob_start();
if ($memberId > 0) {
    try {

        if(isset($_GET['action'])){
            switch ($_GET['action']) {
                case "setLimit":
                    if (isset($_POST['limit']) && ctype_digit((string) $_POST['limit'])) {
                        print_r($_POST);
                        $member = new Member($daffny->DB);
                        $member->load($_SESSION['member_id']);
                        $member->setRecordsPerPage((int) $_POST['limit']);
                        $_SESSION['per_page'] = (int) $_POST['limit'];
                        $out = array('success' => true);
                    }
                    break;
    
                case "VERIFY_DUPLICATE":
                    try{
                        if (isset($_POST['mc_number']) && $_POST['mc_number'] == "" ) {
                            throw new Exception("Invalid mcNumber");
                        }
    
                    } catch(Exception $e) {
                        $out = array('success' => false, 'message' => $e->getMessage());
                    }
                break;
                default:
                    $out = array("success" => false, "message" => "Invaid API Action");
                    break;
            }
        } else {
            switch ($_POST['action']) {
                case "VERIFY_DUPLICATE":
                    try{
                        if (isset($_POST['mc_number']) && $_POST['mc_number'] == "" ) {
                            throw new Exception("Invalid mcNumber");
                        }
                        
                        $sql = "SELECT * FROM app_company_profile WHERE mc_number = '".$_POST['mc_number']."'";
                        $res = $daffny->DB->query($sql);
                        $data = mysqli_fetch_assoc($res);

                        if($res->num_rows){
                            $out = array('success' =>true, 'exists'=>true, 'data' => $data);
                        } else {
                            $out = array('success' =>true, 'exists'=>false);
                        }
                        
                    } catch(Exception $e) {
                        $out = array('success' => false, 'message' => $e->getMessage());
                    }
                break;
                default:
                    $out = array("success" => false, "message" => "Invaid API Action");
                break;
            }
        }
        
    } catch (FDException $e) {
        echo $e->getMessage();
    }
}
ob_clean();
echo $json->encode($out);
require_once "done.php";
