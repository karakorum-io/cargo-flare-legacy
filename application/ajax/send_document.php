<?php

/**
 * Ajax handler file for handling all ajax related operations
 * from CargoFlare.com
 *
 * @author Shahrukh
 * @copyright CargoFlare
 */

require_once "init.php";
require_once "../../libs/phpmailer/class.phpmailer.php";
$memberId = (int) $_SESSION['member_id'];
$out = array('success' => false, 'message' => $memberId);
ob_start();
if ($memberId > 0) {
    try {

        $action = $_POST['action'];
        $to = $_POST['mail_to'];
        $subject = $_POST['mail_subject'];
        $body = $_POST['mail_body'];
        $file_id = $_POST['file_id'];

        $file = $daffny->DB->select_one("*", "app_uploads", "WHERE id = '" . $file_id . "' AND owner_id = '" . getParentId() . "'");

        if (!empty($file)) {

            $file_path = UPLOADS_PATH . $action . "/" . $file["name_on_server"];
            $file_name = $file["name_original"];
            $attachment = array();
            $attachment[] = array("path" => $file_path, "name" => $file_name);

            $member = new Member($daffny->DB);
            $member->load($_SESSION["member_id"]);

            $mail = new FdMailer(true);
            $mail->isHTML();
            $mail->Body = $body;
            $mail->Subject = $subject;
            $mail->AddAddress($to);
            $mail->SetFrom($member->email);
            $mail->AddAttachment($file_path, $file_name);

            if ($mail->Send()) {
                $out = array("success" => true, "message" => "File has been sent.");
            } else {
                $out = array("success" => false, "message" => "File has not been sent.");
            }
        } else {
            $out = array("success" => false, "message" => "File not found");
        }

    } catch (FDException $e) {
        echo $e->getMessage();
    }
}
ob_clean();
echo $json->encode($out);
require_once "done.php";
