<?php

@session_start();
require_once "init.php";
require_once "../libs/phpmailer/class.phpmailer.php";
ob_start();

$_SESSION['iamcron'] = true;

function getAlmostUniqueHash($id, $number)
{
    return md5($id . "_" . $number . "_" . rand(100000000, 9999999999)) . uniqid() . time() . sha1(time());
}

$where = " type = 3 AND status =6  AND   date_format(load_date,'%Y-%m-%d') >=  DATE_FORMAT(DATE_ADD(NOW(), INTERVAL - 3 DAY) ,'%Y-%m-%d') AND  date_format(load_date,'%Y-%m-%d') <=  DATE_FORMAT(DATE_ADD(NOW(), INTERVAL - 0 DAY) ,'%Y-%m-%d') AND id not in (SELECT  entity_id FROM  app_mail_sent WHERE TYPE IN ( 6 ) AND sent = 0)";

$rows = $daffny->DB->selectRows('id', Entity::TABLE, "WHERE " . $where);

if (!empty($rows)) {
    $i = 1;
    foreach ($rows as $row) {
        $entity = new Entity($daffny->DB);
        $entity->load($row['id']);

        try {
            $update_arr = array(
                'pickup_hash' => getAlmostUniqueHash($entity->id, $entity->getNumber()),
            );

            $entity->update($update_arr);

            $entity->sendOrderPickupEmail(EmailTemplate::BULK_MAIL_TYPE_PICKUP, EmailTemplate::SYS_ORDER_PICKUP_MAIL, array(), true, $entity->parentid);
            $i++;
			
        } catch (Exception $exc) {
			echo "<br>".$daffny->DB->errorQuery."<br>";
            echo "<br>Exception:" . $exc->getMessage()."<br>";
        }

        fflush();
    }

    $numRows = sizeof($rows);
    print "<br>Number of Rows : " . $numRows;
} else {
	echo "<br> No Emails to send";
}

$_SESSION['iamcron'] = false;

require_once "done.php";
