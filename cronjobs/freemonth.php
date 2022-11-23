<?php

/* * ************************************************************************************************
 * Cron Earn a Free Month
 *
 * 	Each user can send a referral email to any company. If the company signs up
 * 	with FD and stays with FD for 3 months and obviously pays all their dues,
 * 	referrer account is credited with the amount of money this new customer is
 * 	paying to FD. Referral links should not expire and as long as Referrer is
 * 	still active with FD he gets credit after the Referral is with FD 3 months.
 *
 *
 * Client:		FreightDragon
 * Version:		1.0
 * Date:			2011-12-09
 * Author:		C.A.W., Inc. dba INTECHCENTER
 * Address:	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * E-mail:	techsupport@intechcenter.com
 * CopyRight 2011 FreightDragon. - All Rights Reserved
 * ************************************************************************************************** */
require_once("init.php");
require_once("../libs/phpmailer/class.phpmailer.php");

//select members with referer_id <> '' AND freemonth_payed = 0  and status "active"
$sql = "SELECT m1.*
            FROM members m1
						LEFT JOIN memebrs m2 ON m2.id = m1.referer_id
                WHERE m1.referer_id <> ''
                AND m1.is_freemonth_payed = 0
                AND m1.status = 'Active'
                AND DATE_ADD(m1.reg_date, INTERVAL 4 MONTH) <= '" . date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d") + 3 + 3 + 3 + 3 + 2, date("Y"))) . "' 
								AND m2.status = 'Active'
                AND DATE_ADD(m2.reg_date, INTERVAL 4 MONTH) <= '" . date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d") + 3 + 3 + 3 + 3 + 2, date("Y"))) . "' 
	";
$q = $daffny->DB->query($sql);
while ($row = $daffny->DB->fetch_row($q)) {
		printt($row); 
		$user4monthactive1 = false;	//for referral
		$user4monthactive2 = false;	//for referer
		//check referrer license status payments 4 month active
		$lm = new LicenseManager($daffny->DB);
		$user4monthactive1 = $lm->get4MonthActive($row["id"]);
		$user4monthactive2 = $lm->get4MonthActive($row["referer_id"]);

		if ($user4monthactive1 && $user4monthactive2) {

				$profile = new CompanyProfile($daffny->DB);
				$profile->getByOwnerId($row['id']);

				$billingManager = new BillingManager($daffny->DB);
				$billingManager->owner_id = $row['id'];

				//insert
				$ins = array(
						'owner_id' => $row['referer_id']
						, 'added' => date("Y-m-d H:i:s")
						, 'amount' => (float) $daffny->cfg['referral_payment']
						, 'type' => Billing::TYPE_PAYMENT
						, 'transaction_id' => "Referral Payment: " . $profile->companyname
						, 'description' => "Referral Payment"
				);

				$daffny->DB->transaction("start");
				try {
						$billing = new Billing($daffny->DB);
						$billing->create($ins);
						$member = new Member($daffny->DB);
						$member->load($row['id']);
						$member->update(array("is_freemonth_payed" => 1));
						$referer = new Member($daffny->DB);
						$referer->load($row['referer_id']);

						$ins['contactname'] = $referer->contactname;
						$ins['email'] = $referer->email;
						$daffny->DB->transaction("commit");
				} catch (Exception $e) {
						$daffny->DB->transaction("rollback");
				}
		} else {
				//Same conditions as in Example 1, but my friend cancelled his license on 10/01/2012 and then re-opened again on 12/01/2012 and stayed active until 02/01/2013. I will not receive a credit on 02/01/2013 as license must be kept active 4 continuous months.  

				$member = new Member($daffny->DB);
				$member->load($row['id']);
				$member->update(array("is_freemonth_payed" => 2)); // Referral Terms is not satisfied
		}
}
require_once("done.php");