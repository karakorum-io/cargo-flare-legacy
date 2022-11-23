<?php
/**
 * @version		1.0
 * @since		28.08.12
 * @author		Oleg Ilyushyn, C.A.W., Inc. dba INTECHCENTER
 * @address		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * @email		techsupport@intechcenter.com
 * @copyright	2012 Intechcenter. All Rights Reserved
 */

/**
 * @var Daffny $daffny
 * @var AppAction $action
 */

require_once("init.php");
require_once("../libs/phpmailer/class.phpmailer.php");

$systemPhone = $daffny->DB->selectField('value', 'settings', "WHERE `name` = 'phone'");
$infoEmail = $daffny->DB->selectField('value', 'settings', "WHERE `name` = 'info_email'");
//$where = "WHERE owner_id = 76";
$where = "WHERE IF (DATE_ADD(l.`created`, INTERVAL 2 MONTH) > NOW(), DATE_SUB(l.`expire`, INTERVAL 3 DAY), DATE_SUB(l.`expire`, INTERVAL 30 DAY)) = DATE_FORMAT(NOW(), '%Y-%m-%d')";
$memberIds = $daffny->DB->selectRows('l.owner_id as id', License::TABLE." l", $where);
if (empty($memberIds)) {
	exit;
}
foreach ($memberIds as $row) {
	$member = new Member($daffny->DB);
	$member->load($row['id']);
	echo "Member: ".$row['id']."<br/>";
	$settings = $member->getDefaultSettings();
	if (!$settings->billing_autopay) {
		// AutoPay disabled
		continue;
	}
	$creditCard = new Creditcard($daffny->DB);
	$creditCard->key = $daffny->cfg['security_salt'];
	try {
		$creditCard->load($settings->billing_cc_id, $member->id);
	} catch (FDException $e) {
		continue;
	}
	$row = $daffny->DB->selectRow('l.`users`
															 , l.`renewal_users`
															 , p.`renewal_code`
															 , p.`period_id`
															 , l.`expire`
															 , l.`renewal_product_id` ',
												License::TABLE." l
												, ".Orders::TABLE." o
												, ".Product::TABLE." p
												, `orders_details` od" ,
												"WHERE l.`owner_id` = ".$member->id." 
												AND l.`order_id` = o.`id` 
												AND od.`order_id` = o.`id` 
												AND od.`product_id` = p.`id` 
												AND p.`type_id` = ".Product::TYPE_INITIAL." 
														ORDER BY l.`expire` DESC");
	$renewalId = $daffny->DB->selectRow('id, period_id', Product::TABLE, "WHERE `id` = '".mysqli_real_escape_string($daffny->DB->connection_id, $row['renewal_product_id'])."' AND `is_online` = 1 AND `is_delete` = 0");
	$product = new Product($daffny->DB);
	$product->load($renewalId["id"]);
	$amount = $product->price;
	if ($row['users'] > 0) {
		$additionalId = $daffny->DB->selectField('id', Product::TABLE, "WHERE `period_id` = ".$renewalId['period_id']." AND `type_id` = ".Product::TYPE_ADDITIONAL." AND `is_online` = 1 AND `is_delete` = 0");
		$additional = new Product($daffny->DB);
		$additional->load($additionalId);
		$amount += $additional->price * (int)$row['renewal_users'];
	}


	$tplData = array(
		'first_name' => $creditCard->cc_fname,
		'last_name' => $creditCard->cc_lname,
		'amount' => '$'.$amount,
		'card_number' => "**** **** **** ".substr($creditCard->cc_number, -4),
		'expire' => date('m/d/Y', strtotime($row['expire'])),
		'system_phone' => $systemPhone,
		'info_email' => $infoEmail,
	);
	$action->sendEmail($member->contactname, $member->email, "Renewal reminder", "renewal_reminder", $tplData);
}