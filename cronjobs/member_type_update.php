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


set_time_limit(270);

require_once("init.php");

require_once("../libs/phpmailer/class.phpmailer.php");



//get Members

$mm = new MembersManager($daffny->DB);

$members = $mm->getMembers("`status` = 'Active' AND parent_id=1");


$check_number=1000;
$i=0;
foreach ($members as $m => $member) {

print $member->id."--".$member->contactname."---<br>";
                            $inp = $daffny->DB->selectRow("id,value", "members_type_value", "WHERE member_id='".(int)$member->id."' and type='QBPRINT'");
							if (empty($inp)) {
								
								$check_number = $check_number + 1000;
								
								$ins_arr = array(
												'member_id' => $member->id,
												'type' => 'QBPRINT',
												'value' => $check_number,
												
											);
			
								//$daffny->DB->insert("members_type_value",$ins_arr );
								$i++;
								print $i."---Inserted--".$check_number."<br>";
								
								
							}
                            else
							  print "---Updated--<br>";
}

require_once("done.php");