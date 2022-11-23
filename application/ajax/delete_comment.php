<?

/**
 * ajax.php Ajax handler file for handling all ajax related operations
 * from CargoFlare.com
 *
 * @author Shahrukh
 * @copyright CargoFlare
 */

require_once("init.php");

if (validate_id($_SESSION["member_id"]) && validate_id($_POST["id"])) {

		$daffny->DB->transaction("start");
		if (!$daffny->DB->delete("orders_comments", "id = '" . (int) $_POST["id"] . "'")) {
				$daffny->DB->transaction("rollback");
				$code = "1";
				$message = "DB error";
		} else {
				$daffny->DB->transaction("commit");
				$code = "0";
				$message = "";
		}

		$return = array(
				"code" => $code
				, "message" => $message
		);
		echo json_encode($return);
}

require_once("done.php");
