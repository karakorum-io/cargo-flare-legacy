<?php

/* * ************************************************************************************************
 * Get Leads
 *
 * Client:		FreightDragon
 * Version:		1.0
 * Date:			2011-12-14
 * Author:		C.A.W., Inc. dba INTECHCENTER
 * Address:	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * E-mail:	techsupport@intechcenter.com
 * CopyRight 2011 FreightDragon. - All Rights Reserved
 * ************************************************************************************************** */
set_time_limit(0);
require_once("init.php");
require_once("../libs/phpmailer/class.phpmailer.php");
require_once("../libs/xmlapi.php");

//get Members
$mm = new MembersManager($daffny->DB);
$members = $mm->getMembers("`status` = 'Active' AND chmod <> 1 AND id = parent_id");

$lsm = new LeadsourceManager($daffny->DB);

foreach ($members as $m => $member) {
	$leadsources = $lsm->get(null, null, "WHERE `owner_id` = '" . (int) $member->id . "'");
	foreach ($leadsources as $l => $ls) {
		$email = trim($ls->email_to);
		if (trim($email) == "") {
				continue;
		}
		$account = "freightd";
		$account_pass = "X6D8sEh4Nr[8";
		$ip = "127.0.0.1";

		$emailData = explode('@', $email);

		$email_user = $emailData[0];
		$email_password = $daffny->cfg['MAILPWD'];
		$email_domain = "freightdragon.com";
		$email_quota = "10";

		$xmlapi = new xmlapi($ip, $account, $account_pass);
		$xmlapi->set_output('xml');

		$result = $xmlapi->api2_query($account, "Email", "addpop", array($email_user, $email_password, $email_quota, $email_domain));
//		echo $email . "<br />";
//		$dir = explode("@", $email);
//		if (isset($dir[0]) && trim($dir[0]) != "") {
//			$path = "/home/freightd/mail/freightdragon.com/" . trim($dir[0]) . "/";
//			if (file_exists($path)) {
//					echo "Dir: " . $path . " is exist<br />";
//			} else {
//					echo "Dir: " . $path . " not exist<br />";
//					echo "Trying to create email account...<br />";
//					echo "system(\"/usr/local/cpanel/scripts/addpop --email=\"".$email."\" --password=\"".$daffny->cfg['MAILPWD'].");<br />";
//					$return_var = "";
//					system("/usr/local/cpanel/scripts/addpop --email=".$email." --password=".$daffny->cfg['MAILPWD'], $return_var );
//					var_dump($return_var);
//			}
//		} else {
//				echo "empty email: " . $email . "<br />";
//		}
	}
}
require_once("done.php");