<?php

/**
 * signature.php Ajax handler file for handling all ajax related operations
 * from CargoFlare.com
 *
 * @author Shahrukh
 * @copyright CargoFlarea
 */

require_once "init.php";
require_once ROOT_PATH . "libs/signature/signature.php";
require_once ROOT_PATH . "libs/anet/AuthorizeNet.php";
require_once ROOT_PATH . "/libs/mpdf/mpdf.php";
$memberId = (int) $_SESSION['member_id'];
$out = array('success' => false);
try {
    if (count($_POST) > 0) {
        $type = "post";
    } else {
        $type = "get";
    }
    switch ($type) {
        case 'get':
            if (!isset($_GET['id'])) {
                throw new FDException("Invalid ID");
            }

            switch ($_GET['signType']) {
                case 'dispatch':
                    $dispatchSheet = new DispatchSheet($daffny->DB);
                    $dispatchSheet->load($_GET['id']);
                    if (is_null($dispatchSheet->signature)) {
                        exit();
                    }

                    ob_end_clean();
                    header('Content-Length: ' . strlen($dispatchSheet->signature));
                    header('Content-Type: image/png');
                    echo $dispatchSheet->signature;
                    exit;
                    break;
                case 'order':
                    $entityDoc = new EntityDoc($daffny->DB);
                    $entityDoc->load($_GET['id']);
                    if (is_null($entityDoc->signature)) {
                        exit;
                    }

                    header('Content-Length: ' . strlen($entityDoc->signature));
                    header('Content-Type: image/png');
                    echo $entityDoc->signature;
                    exit;
                    break;
            }
            break;
        case 'post':

            if (!isset($_POST['id'])) {
                throw new FDException("Invalid ID");
            }

            if (!isset($_POST['signType'])) {
                throw new FDException("Invalid Signature Type");
            }

            if (!isset($_POST['width']) || !isset($_POST['height']) || !isset($_POST['data']) || !isset($_POST['type'])) {
                throw new FDException("Invalid parameters");
            }

            $signature = new Signature();
            $signature->setFontFile(ROOT_PATH . "libs/signature/jenna_sue.ttf");
            if ($sign = $signature->create($_POST['width'], $_POST['height'], $_POST['type'], $_POST['data'])) {
                switch ($_POST['signType']) {
                    case 'dispatch':
                        $dispatchSheet = new DispatchSheet($daffny->DB);
                        $dispatchSheet->load($_POST['id']);

                        $dispatchSheet->update(array(
                            'signature' => $sign,
                            'sign_by' => $_POST['data'],
                            'signed' => date('Y-m-d H:i:s'),
                        ));
                        $upload_pdf_id = $dispatchSheet->acceptNew();

                        $order = new Entity($daffny->DB);
                        $order->load($dispatchSheet->entity_id);

                        // Create Internal Notes

                        $note = new Note($daffny->DB);
                        $note->create(array('entity_id' => $dispatchSheet->entity_id, 'text' => "Dispatch sheet signed.", 'sender_id' => $order->getAssigned()->parent_id, "status" => 1, "system_admin" => 2, 'type' => Note::TYPE_INTERNAL));

                        // Create Internal Notes
                        if (trim($_POST['notes']) != "") {

                            $note = new Note($daffny->DB);
                            $note->create(array('entity_id' => $dispatchSheet->entity_id, 'text' => "Notes are added by Carrier : " . trim($_POST['notes']), 'sender_id' => $order->getAssigned()->parent_id, "status" => 1, "system_admin" => 2, 'type' => Note::TYPE_INTERNAL));
                        }
                        $order->make_payment();
                        $order->updateHeaderTable();

                        $out = array('success' => true, 'url' => SITE_IN . "order/dispatchthanks/eid/" . $dispatchSheet->entity_id . "&id=" . $upload_pdf_id);
                    break;
                    case 'dispatch_new':
                        $dispatchSheet = new DispatchSheet($daffny->DB);
                        $dispatchSheet->load($_POST['id']);

                        $dispatchSheet->update(array(
                            'signature' => $sign,
                            'sign_by' => $_POST['data'],
                            'signed' => date('Y-m-d H:i:s'),
                        ));
                        $upload_pdf_id = $dispatchSheet->acceptNew();

                        $order = new Entity($daffny->DB);
                        $order->load($dispatchSheet->entity_id);

                        // Create Internal Notes

                        $note = new Note($daffny->DB);
                        $note->create(array('entity_id' => $dispatchSheet->entity_id, 'text' => "Dispatch sheet signed.", 'sender_id' => $order->getAssigned()->parent_id, "status" => 1, "system_admin" => 2, 'type' => Note::TYPE_INTERNAL));

                        // Create Internal Notes
                        if (trim($_POST['notes']) != "") {

                            $note = new Note($daffny->DB);
                            $note->create(array('entity_id' => $dispatchSheet->entity_id, 'text' => "Notes are added by Carrier : " . trim($_POST['notes']), 'sender_id' => $order->getAssigned()->parent_id, "status" => 1, "system_admin" => 2, 'type' => Note::TYPE_INTERNAL));
                        }
                        $order->make_payment();
                        $order->updateHeaderTable();

                        $out = array('success' => true, 'url' => SITE_IN . "order/dispatchthanks/eid/" . $dispatchSheet->entity_id . "&id=" . $upload_pdf_id);
                    break;
                    case 'order':
                    case 'order_esign_total':
                        
                        try{
                            $order = new Entity($daffny->DB);
                            $order->load($_POST['id']);

                            $order->update([
                                'hash' => md5($order->id.date('Ymdhis')),
                                'expired_hash' => $_POST['hash'],
                                'esign_agreed' => 1,
                                'esigned_date' => date('Y-m-d h:i:s')
                            ]);
                            
                            $entityDoc = new EntityDoc($daffny->DB);

                            do {
                                $fileName = md5(mt_rand() . time());
                                $filePath = UPLOADS_PATH . 'entities/' . $order->id . '/' . $fileName;
                            } while (file_exists($filePath));

                            $formTemplate = new FormTemplate($daffny->DB);
                            
                            if ($_POST['signType'] == 'order') {
                                $formTemplate->loadByOwnerId(FormTemplate::SYS_ORDER, $order->getAssigned()->parent_id);
                            } else {
                                $formTemplate->loadByOwnerId(FormTemplate::SYS_ORDER_ESIGN_TOTAL, $order->getAssigned()->parent_id);
                            }

                            // read PDF file Formatting
                            //$pdfBody = file_get_contents(UPLOADS_PATH.'form-templates/order-confirmation-form-template.html');
                            $formData = EmailTemplate::fillParams($order, array_fill_keys($daffny->tpl->get_vars($formTemplate->body), ''), EmailTemplate::SEND_TYPE_HTML, false, $sign);
                            
                            $form = $daffny->tpl->get_parsed_from_array($formTemplate->body, $formData);
                            $form = str_replace(array("\t", "\n", "\r"), '', $form);

                            $pdf = new mPDF('utf-8', 'A4', '8', 'DejaVuSans', 10, 10, 7, 7, 10, 10);
                            $pdf->SetAuthor($order->getShipper()->fname . ' ' . $order->getShipper()->lname);
                            $pdf->SetSubject("Order Confirmation");
                            $pdf->SetTitle("e-Signed Document");
                            $pdf->SetCreator("CargoFlare.com");

                            if (preg_match('|<style type="text/css">(.*)</style>|mi', $form, $match)) {
                                $form = str_replace($match[1], '', $form);
                                $match[1] = 'body {font-family: "Trebuchet MS", Arial, Helvetica, sans-serif; font-size: 13px; color: #444; margin: 3px; padding: 0px;}.wrapper {width: 860px;margin: auto;padding: 1em 0;}.left { float: left; padding-right: 10px; }.right { float: left; }.underline, .underline_strong { border-bottom: 1px dotted #32adf3; font-size: 14px; }.underline_strong { font-weight: bold; }.label, .label_strong { text-align: right; font-size: 12px; font-weight: bold; white-space: nowrap; }.label_strong { color: #000; }.form-header {font-weight: bold;font-size: 220%;color: #f26e21;text-align: center;padding: .5em;}dl.form-list {list-style: none;margin: 0;padding: .5em 0;}dl.form-list dt {display: block;clear: both;margin: 1em 0;padding: .4em .8em;font-weight: bold;font-size: 120%;color: #fff;border-radius: 5px;background: #f26e21;}dl.form-list dd {display: block;margin: 0;padding: 0 0 2em;}dl.form-list dd:before, dl.form-list dd:after { content: ""; display: table; }dl.form-list dd:after { clear: both; }dl.form-list dd { *zoom: 1; }.form-fields {width: 100%;}.form-fields td {padding: .2em 1em;}.print-section {border-radius: 5px;border: 1px solid #e5e7e0;}.print_section_header2 {text-align: center;font-weight: bold;font-style: italic;font-size: 120%;color: #f26e21;}.form_header { width: 670px; font-weight: bold; font-size: 16px; text-align: center; }.company_address_info { width: 420px; float: left; vertical-align: top; padding-bottom: 0px; }.company_name { font-size: 150%; font-weight: bold; vertical-align: top; }.sales_contact_info { width: 260px; padding-bottom: 0px; }.opening_info { padding: 1em; }.customer_info { width: 401px; float: left; vertical-align: top; }.customer_info td { text-align: left; vertical-align: middle; }.customer_info .right { padding-left: 10px; }.customer_info .left .underline { width: 120px }.customer_info .right .underline { width: 130px; }.price_and_ship { width: 290px; float: right; vertical-align: top; }.price_and_ship td { width: 115px; vertical-align: middle; }.price_and_ship .right .underline { width: 90px; }.payments { padding-top: 0px; padding-bottom: 0px; }.transit_directives td { vertical-align: middle; }.transit_directives .right { padding-left: 50px; }.transit_directives .left .underline, .transit_directives .right .underline { width: 200px; }.vehicle-table { border-collapse: collapse }.vehicle-table td, .vehicle-table th { border: 1px solid #f26e21; }.vehicle-table td { background-color: #fff; text-align: left; padding: 3px 5px }.vehicle-table th { background-color: #eee; text-align: center; font-weight: bold; padding: 3px 20px }.deposit .left .underline { width: 230px; }.deposit .right .underline { width: 200px; }.agree .underline { width: 200px; }.agree .underline_long { width: 350px; border-bottom: 1px solid #000000; }';
                                $pdf->writeHTML('<style>' . $match[1] . '</style>', 1);
                            }
                            
                            $pdf->writeHTML(html_entity_decode($form, ENT_NOQUOTES, 'UTF-8'), 2);
                            makePath(UPLOADS_PATH . 'entities/' . $order->id, false);
                            $pdf->Output($filePath, 'F');

                            $entityDoc->create(array(
                                'entity_id' => $order->id,
                                'name' => 'Signed Order Form (' . date('m/d/Y') . ')',
                                'filename' => $fileName,
                                'signature' => $sign,
                            ));

                            //
                            // Put to the Documents Tab
                            //
                            do {
                                $fname = md5(mt_rand() . " " . time() . " " . $order->id);
                                $path = ROOT_PATH . "uploads/entity/" . $fname;
                            } while (file_exists($path));
                            $pdf->Output($path, 'F');

                            $ins_arr = array(
                                'name_original' => "Signed Order Form " . date("Y-m-d H-i-s") . ".pdf",
                                'name_on_server' => $fname,
                                'size' => filesize($path),
                                'type' => "pdf",
                                'date_uploaded' => date("Y-m-d H:i:s"),
                                'owner_id' => $order->getAssigned()->parent_id,
                                'status' => 0,
                            );

                            $daffny->DB->insert("app_uploads", $ins_arr);
                            $ins_id = $daffny->DB->get_insert_id();

                            $daffny->DB->insert("app_entity_uploads", array("entity_id" => $order->id, "upload_id" => $ins_id));

                            // Update Entity
                            $update_arr = array(
                                'esigned' => 1,
                            );
                            $order->update($update_arr);

                            // Create Internal Notes
                            $note = new Note($daffny->DB);
                            $note->create(array('entity_id' => $order->id, 'text' => "Order form e-signed.", 'sender_id' => $order->getAssigned()->parent_id, "status" => 1, "system_admin" => 2, 'type' => Note::TYPE_INTERNAL));

                            // Create Internal Notes
                            if (trim($_POST['notes']) != "") {

                                $note = new Note($daffny->DB);
                                $note->create(array('entity_id' => $order->id, 'text' => "Notes from customer : " . trim($_POST['notes']), 'sender_id' => $order->getAssigned()->parent_id, "status" => 1, "system_admin" => 2, 'type' => Note::TYPE_INTERNAL));
                            }

                            $order->update([
                                'esigned_doc' => $ins_id,
                            ]);

                            $order->make_payment();
                            $order->updateHeaderTable();

                            $mail = new FdMailer(true);
                            $mail->isHTML();
                            
                            $body = "Thank you for e-signing the transportation agreement, we have included a copy of your signed agreement in this email for your records.";
                            $body .= "<br><br>Sincerely,<br>".$order->getAssigned()->contactname;
                            $body .= "<br>".$order->getAssigned()->email;
                            $body .= "<br>".$order->getAssigned()->phone;
                            $mail->Body = $body;
                            $mail->Subject = 'Signed Order ';
                            
                            $mail->AddAddress($order->getShipper()->email, $order->getShipper()->fname . ' ' . $order->getShipper()->lname);
                            //$mail->AddAddress("shahrukhusmaani@live.com", $order->getShipper()->fname . ' ' . $order->getShipper()->lanme);
                            $mail->AddCC($order->getAssigned()->email, $order->getAssigned()->contactname);
                            $mail->setFrom('noreply@transportmasters.net');
                            
                            $mail->AddAttachment($filePath, 'Shipping Agreement.pdf');
                            $mail->send();
                            $out = array(
                                'success' => true,
                                'url' => SITE_IN . "order/esignthanks/hash/" . $order->hash . "&id=" . $ins_id,
                            );
                        } catch(Exception $e) {
                            $out['message'] = $e->getMessage();
                            $out['QueryString'] = $daffny->DB->errorQuery;
                        }
                    break;
                }
            }
        break;
    }
} catch (FDException $e) {
    $out['message'] = $e->getMessage();
}
echo $json->encode($out);
require_once "done.php";
