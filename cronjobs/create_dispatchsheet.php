<?php

@session_start();

require_once "init.php";
require_once "../libs/phpmailer/class.phpmailer.php";

ob_start();

require_once "init.php";

$_SESSION['iamcron'] = true; // Says I am cron for Full Access

set_time_limit(80000000);
ini_set('memory_limit', '3500M');
ini_set('upload_max_filesize', '128M');
ini_set('post_max_size', '128M');
ini_set('max_input_time', 80000000);

function getAlmostUniqueHash($id, $number)
{
    return md5($id . "_" . $number . "_" . rand(100000000, 9999999999)) . uniqid() . time() . sha1(time());
}

$where = " `type` =3 AND `balance_paid_by`=24 and status>=6 and parentid=1 order by id desc limit 45,5";
print $where;

$rows = $daffny->DB->selectRows('id', Entity::TABLE, "WHERE " . $where);
if (!empty($rows)) {
    $i = 1;
    foreach ($rows as $row) {

        $entity = new Entity($daffny->DB);
        $entity->load($row['id']);

        print "<br>" . $i . "--" . $entity->id . "--" . $entity->number . "--" . $entity->status . "==" . $entity->parentid;
        print "<br>";

        try {

            $fname = md5(mt_rand() . " " . time() . " " . $entity->id);
            $path = ROOT_PATH . "uploads/entity/" . $fname;
            $DispatchSheet = $entity->getDispatchSheet();
            $DispatchSheet->getPdfNew("F", $path);

            $ins_arr = array(
                'name_original' => "Dispatch sheet " . date("Y-m-d H-i-s") . ".pdf",
                'name_on_server' => $fname,
                'size' => filesize($path),
                'type' => "pdf",
                'date_uploaded' => date("Y-m-d H:i:s"),
                'owner_id' => $entity->getAssigned()->parent_id,
                'status' => 0,
            );

            $daffny->DB->insert("app_uploads", $ins_arr);
            $ins_id = $daffny->DB->get_insert_id();

            $daffny->DB->insert("app_entity_uploads", array("entity_id" => $entity->id, "upload_id" => $ins_id));
            print "--Dispatchsheet Created.<br>";
            $mail = new FdMailer(true);
            $mail->isHTML();
            
			$body = "Dear $DispatchSheet->carrier_company_name<br><br>We have noticed an error on your original dispatch sheet for Order id $DispatchSheet->order_number. we have attached correct dispatch for your convenience.<br><br>For any question please contact our dispatch department.";
			$body .= "<br><br>Sincerely,<br>".$entity->getAssigned()->contactname;
			$body .= "<br>".$entity->getAssigned()->email;
			$body .= "<br>".$entity->getAssigned()->phone;
			$mail->Body = $body;

            $mail->Subject = "Corrected dispatch sheet for Order id $DispatchSheet->order_number";
            $mail->AddAddress($DispatchSheet->carrier_email, $DispatchSheet->carrier_contact_name);
            $mail->AddCC("central@cargoflare.com", "CENTRAL DISPATCH");
            $mail->setFrom($entity->getAssigned()->getDefaultSettings()->smtp_from_email);

            $mail->AddAttachment($path, "Dispatch Sheet ID $DispatchSheet->order_number accepted on " . date("m-d-y") . ".pdf");
            $mail->send();

        } catch (Exception $exc) {
            echo "<br><br>-----" . $exc . "<br><br>";
            print "--Dispatchsheet Not Created.<br>";
        }
        fflush();
    }
    $numRows = sizeof($rows);

}

print "numRows : " . $numRows;

$_SESSION['iamcron'] = false;
require_once "done.php";
