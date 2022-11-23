<?php

/**
 * Ajax handler file for handling all ajax related operations
 * from CargoFlare.com
 *
 * @author Shahrukh
 * @copyright CargoFlare
 */

require_once "init.php";
require_once "../../libs/anet/AuthorizeNet.php";

$memberId = (int) $_SESSION['member_id'];
$out = array('success' => false);

if ($memberId > 0) {
    try {

        if (isset($_POST['action'])) {
            switch ($_POST['action']) {

                case 'emailOrderSingle':

                    if (!isset($_POST['entity_id']) || !ctype_digit((string) $_POST['entity_id']) || !isset($_POST['form_id']) || !ctype_digit((string) $_POST['form_id'])) {
                        throw new RuntimeException("Invalid Entity ID");
                    }

                    $form_id = (int) $_POST['form_id'];
                    $entity = new Entity($daffny->DB);
                    $entity->load((int) $_POST['entity_id']);
                    $entity->updateHash();

                    $emailContentArr = array();
                    $emailContentArr = $entity->sendEmailWithoutForm($form_id);
                    $out = array('success' => "single", 'emailContent' => $emailContentArr);
                    break;
                case 'emailOrderBulk':
                    if (!isset($_POST['entity_id']) || !is_array($_POST['entity_id']) || !isset($_POST['form_id']) || !ctype_digit((string) $_POST['form_id'])) {
                        throw new RuntimeException("Invalid Entity ID");
                    }

                    $form_id = (int) $_POST['form_id'];
                    $entity_ids = $_POST['entity_id'];

                    $emailContentArr = array();
                    foreach ($entity_ids as $entity_id) {
                        $entity = new Entity($daffny->DB);
                        $entity->load((int) $entity_id);
                        $entity->updateHash();
                        $emailContentArr[] = $entity->sendEmailWithoutForm($form_id);

                    }
                    $countEmails = count($emailContentArr);
                    $out = array('success' => "countEmails", "countEmails" => $countEmails, "content" => $emailContentArr);
                    break;
                case 'emailOrderBulkSend':

                    try{
                        if (!isset($_POST['form_id']) || !ctype_digit((string) $_POST['form_id'])) {
                            throw new RuntimeException("Invalid form ID");
                        }

                        $form_id = (int) $_POST['form_id'];
                        $contents = $_POST['content'];
                        $entity_ids = $_POST['entity'];

                        $contents_count = count($contents);
                        $entity_count = count($entity_ids);

                        if ($contents_count == $entity_count) {
                            
                            for ($i = 0; $i < $entity_count; $i++) {
                                $entity_id = $entity_ids[$i];
                                $to = $contents[$i]['mail_to'];
                                $cc = $contents[$i]['mail_cc'];
                                $bcc = $contents[$i]['mail_bcc'];
                                $subject = $contents[$i]['mail_subject'];
                                $body = $contents[$i]['mail_body'];
                                $attach_type = $contents[$i]['attach_type'];
                                $combine = $contents[$i]['combine'];

                                $entity = new Entity($daffny->DB);
                                $entity->load((int) $entity_id);
                                $entity->updateHash();
                                $emailContentArr[] = $entity->sendEmailWithoutForm($form_id);

                                $emailArr = array();
                                $emailArr['to'] = $to;
                                $emailArr['mail_extra'] = "";
                                $emailArr['subject'] = $subject;
                                // $emailArr['body'] = $body;

                                $parsed = getMailHashEmbededInBody($body);
                                $emailArr['body'] = $parsed['body'];
                                $hash = $parsed['hash'];
                                $mailData = array(
                                    'entity_id' => $entity->id,
                                    'form_id' => $form_id,
                                    'member_id' => $_SESSION['member_id'],
                                    'fromAddress' => $email_from,
                                    'toAddress' => $to,
                                    'cc' => $cc,
                                    'bcc' => $bcc,
                                    'subject' => $subject,
                                    'body' => $body,
                                    'type' => 5,
                                    'attach_type' => $attach_type,
                                    'combine' => $combine,
                                    'sent' => 0,
                                );

                                $mail_log = array(
                                    'entity_id' =>$entity->id,
                                    'sent_by' => $_SESSION['member_id'],
                                    'hash' => $hash,
                                    'from_email' => "noreply@transportmasters.net",
                                    'to_email' => $to,
                                    'cc_email' => json_encode($cc) == "null" ? "" : json_encode($cc),
                                    'bcc_email' => json_encode($bcc) == "" ? "" : $bcc,
                                    'email_name' => $emailContentArr[0]['name'],
                                    'subject' =>  $subject,
                                    'body' =>  $body
                                );

                                log_mail($daffny->DB, $mail_log);

                                $ins_arr = $daffny->DB->PrepareSql("app_mail_sent", $mailData);
                                $daffny->DB->insert('app_mail_sent', $ins_arr);
                                
                                if ($form_id == 110) {
                                    $entity->update(array('esigned_date' => date('Y-m-d H:i:s')));
                                } elseif ($form_id == 677) {
                                    $entity->update(array('bsigned_date' => date('Y-m-d H:i:s')));
                                }

                                if ($form_id == 109) {
                                    $entity->update(array('invoice_status' => 1));
                                }

                                $entity->updateHeaderTable();
                            }
                        }
                        $out = array('success' => true, "message" => "Mails have been sent successfully.");
                    } catch(Exception $e) {
                        $out = array('success' => false, "message" => $e->getMessage());
                    }

                    break;

                case 'sendBulkMail':
                    if (!isset($_POST['form_id']) || !ctype_digit((string) $_POST['form_id'])) {
                        throw new RuntimeException("Invalid form ID");
                    }

                    $form_id = (int) $_POST['form_id'];
                    $entity_ids = $_POST['entity_id'];

                    $entity_count = count($entity_ids);

                    for ($i = 0; $i < $entity_count; $i++) {
                        $entity_id = $entity_ids[$i];
                        $entity = new Entity($daffny->DB);
                        $entity->load((int) $entity_id);
                        try {
                            $entity->sendSelectedQuoteTemplate($form_id);
                        } catch (FDException $e) {
                            print $e;
                        }
                    }

                    $out = array('success' => true, "message" => "Mails have been sent successfully.");
                    break;
                default:
                    break;
            }
        } elseif (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'getDoc':
                    if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
                        break;
                    }

                    $doc = new EntityDoc($daffny->DB);
                    $doc->load($_GET['id']);
                    $entity = new Entity($daffny->DB);
                    $entity->load($doc->entity_id);
                    $filePath = UPLOADS_PATH . "entities" . DIRECTORY_SEPARATOR . $doc->entity_id . DIRECTORY_SEPARATOR . $doc->filename;
                    if (file_exists($filePath)) {
                        header('Content-Type: application/pdf');
                        header("Content-Disposition: attachment; filename=\"" . $doc->name . ".pdf\"");
                        header("Content-Description: \"" . $doc->name . "\"");
                        header('Content-Length: ' . filesize($filePath));
                        header("Expires: 0");
                        header("Cache-Control: private");
                        header("Pragma: cache");
                        readfile($filePath);
                        exit;
                    }
                    break;
                default:
                    break;
            }
        }
    } catch (Exception $e) {
        if ($daffny->DB->isTransaction) {
            $daffny->DB->transaction('rollback');
        }
        $out['message'] = $e->getMessage();
    }
}

 /**
 * function to allocate any specific email sent from system to a hash embedded in mail body
 * @verison 1.3
 * @author Shahrukh
 * @return String
 */
function getMailHashEmbededInBody($body){
    $hash = rand(1000000000,9999999999);
    return [
        'body' => str_replace("REPLACE_WITH_HASH",$hash,$body),
        'hash' => $hash
    ];
}

function log_mail($db, $data){
    $db->insert('entity_email_log', $data);
}

function getAlmostUniqueHash($id, $number)
{
    return md5($id . "_" . $number . "_" . rand(100000000, 9999999999)) . uniqid() . time() . sha1(time());
}

//ob_clean();
echo $json->encode($out);
