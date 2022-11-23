<?php

/**
 * ajax.php Ajax handler file for handling all ajax related operations
 * from CargoFlare.com
 * 
 * @author Shahrukh
 * @copyright CargoFlare
 */

require_once("init.php");

$memberId = (int)$_SESSION['member_id'];
$out = array('success' => false);

ob_start();
/* @var Daffny $daffny */
if ($memberId > 0) {
	try {
		switch($_GET['action']) {
			case "updateView":
				if (ctype_digit((string)$_POST['id']) || $_POST['id'] == '-1') {
					$_SESSION['view_id'] = $_POST['id'];
					$out = array('success' => true);
				}
				break;
			case "getByZip":
				if (!isset($_POST['zip'])) break;
				$row = $daffny->DB->selectRow("`state`, `city`", "`zip_codes`", "WHERE `zip` LIKE '".mysqli_real_escape_string($daffny->DB->connection_id, $_POST['zip'])."'");
				if (!empty($row)) {
					$out = array(
						'success' => true,
						'data' => array(
							'state' => $row['state'],
							'city' => ucwords(strtolower($row['city'])),
						),
					);
				}
				break;
			default:
				break;
		}
	} catch (FDException $e) {
		echo $e->getMessage();
	}
}
ob_clean();
echo $json->encode($out);
require_once("done.php");
?>