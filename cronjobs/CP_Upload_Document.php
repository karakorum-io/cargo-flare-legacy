<?php
/* * ************************************************************************************************
 * Client:  CargoFalre
 * Version: 2.0
 * Date:    2011-04-26
 * Author:  CargoFlare Team
 * Address: 7252 solandra lane tamarac fl 33321
 * E-mail:  stefano.madrigal@gmail.com
 * CopyRight 2021 Cargoflare.com - All Rights Reserved
 * ************************************************************************************************** */
 
@session_start();

require_once "init.php";
require_once "../libs/phpmailer/class.phpmailer.php";
ob_start();

require_once "init.php";
$_SESSION['iamcron'] = true;

// Entity Document Upload Functionality Starts Here

$id = (int) $_POST["entityID"];

if($id === 0){
	echo json_encode(array('SUCCESS'=>false, 'Message'=> 'Invalid Entity ID'));die;
}

$account_id = (int) $_POST["account_id"];

if($account_id === 0){
    echo json_encode(array('SUCCESS'=>false, 'Message'=> 'Invalid Account ID'));die;
}

$upload = new upload();
$upload->out_file_dir = UPLOADS_PATH . "entity/";
$upload->max_file_size = 50 * 1024 * 1024;
$upload->form_field = "file";
$upload->make_script_safe = 1;
$upload->allowed_file_ext = array("pdf", "doc", "docx", "xls", "xlsx", "jpg", "jpeg", "png", "tiff", "wpd");
$upload->save_as_file_name = md5(time() . "-" . rand()) . time();
$response = $upload->upload_process();

$sql = "SELECT parentid FROM app_order_header WHERE entityid = ".$id;
$res = $daffny->DB->query($sql);

$owner_id = 0;
while($r = mysqli_fetch_assoc($res)){
	$owner_id = $r['parentid'];
}

if($owner_id === 0  || $owner_id === null){
	echo json_encode(array('SUCCESS'=>false, 'Message'=> 'Invalid Owner'));die;
}

switch ($upload->error_no) {
    case 0:
        {

            $sql_arr = array(
                'name_original' => $_FILES[$upload->form_field]['name'],
                'name_on_server' => $upload->save_as_file_name,
                'size' => $_FILES[$upload->form_field]['size'],
                'type' => $upload->file_extension,
                'date_uploaded' => "now()",
                'owner_id' => $owner_id,
                'status' => 0,
            );
            $ins_arr = $daffny->DB->PrepareSql("app_uploads", $sql_arr);
            $daffny->DB->insert("app_uploads", $ins_arr);
            $insid = $daffny->DB->get_insert_id();

            $daffny->DB->insert("app_entity_uploads", array("entity_id" => $id, "upload_id" => $insid));

            // adding note for document upload
            insert_notes($daffny->DB, $id, $account_id);

            // send email notification
            send_email_notification($daffny->DB,$id,$account_id);

            echo json_encode(array('SUCCESS'=>true, 'Message'=> 'File Uploaded!'));
        }
        break;
    case 1:
    	echo json_encode(array('SUCCESS'=>false, 'Message'=> 'File not selected or empty'));die;
    case 2:
    case 5:
    	echo json_encode(array('SUCCESS'=>false, 'Message'=> 'Inavlid File Extension'));die;
    case 3:
    	echo json_encode(array('SUCCESS'=>false, 'Message'=> 'File too big'));die;
    case 4:
    	echo json_encode(array('SUCCESS'=>false, 'Message'=> 'Cannot move uploaded file'));die;
}
exit;

// Entity Document Functionality Ends Here

$_SESSION['iamcron'] = false;
require_once "done.php";

// sending email after when notes are inserted
function send_email_notification($obj,$entity_id,$account_id){
    
    $account = new Account($obj);
    $account->load($account_id);

    $member = new Member($obj);
    $member->load($account->owner_id);

    $entity = new Entity($obj);
    $entity->load($entity_id);
    
    $mail = new FdMailer(true);
    $mail->isHTML();

    $body = $member->contactname." <br><br> Your customer ".$account->first_name." ".$account->last_name." ".$account->company_name." has uploaded files to order ID : ".$entity->prefix."-".$entity->number." <a href=''>Click Here to View Document</a>";
    $mail->Body = $body;
    $mail->Subject = "FreightDragon Notification";
    $mail->AddAddress('charlie@yopmail.com');
    $mail->setFrom('noreply@freightdragon.com');
    $mail->send();
}

// function to insert system notes after document is uploaded
function insert_notes($obj,$entity_id, $account_id){
    $account = new Account($obj);
    $account->load($account_id);

    $notes = "Document Uploaded From Customer Portal at ".date('m/d/y h:i A');

    $field_names = "(entity_id,sender_id,sender_customer_portal,type,text,system_admin,priority)";
    $field_values = "VALUES( {$entity_id}, ".$account->owner_id.", 0,3,'{$notes}',1,1 )";
    $sql = "INSERT INTO app_notes {$field_names} {$field_values}";
    $obj->query($sql);
    return $obj->get_insert_id();
}