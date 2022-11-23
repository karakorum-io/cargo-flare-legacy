<?php

/**
 * Ajax handler file for handling all ajax related operations
 * from CargoFlare.com
 *
 * @author Shahrukh
 * @copyright CargoFlare
 */

require_once "init.php";

$cities = "";
$list = "";

if (isset($_POST['state']) && trim($_POST['state']) != "" && isset($_POST['type'])) {
    $q = $daffny->DB->select("*", "cities", "WHERE state='" . mysqli_real_escape_string($daffny->DB->connection_id, trim($_POST['state'])) . "'");
    $fl = false;
    while ($row = $daffny->DB->fetch_row($q)) {
        $fl = true;
        if ($_POST['type'] == "destination") {
            $list .= "<tr>
						<td class=\"grid-body-left\"><input type=\"checkbox\" name=\"destination[" . $row['id'] . "]\" id=\"destination_" . $row['id'] . "\" checked=\"checked\" /></td>
						<td><label for=\"destination_" . $row['id'] . "\">" . htmlspecialchars($row['city']) . "</label></td>
						<td align=\"center\">" . $row['state'] . "</td>
						<td align=\"right\" class=\"grid-body-right\">$ <input type=\"text\" class=\"form-box-textfield money\" name=\"d_surcharge[" . $row['id'] . "]\" value=\"0.00\" /></td>
					</tr>";
        } else {
            $list .= "<tr>
						<td class=\"grid-body-left\"><input type=\"checkbox\" name=\"origin[" . $row['id'] . "]\" id=\"origin_" . $row['id'] . "\" checked=\"checked\" /></td>
						<td><label for=\"origin_" . $row['id'] . "\">" . htmlspecialchars($row['city']) . "</label></td>
						<td align=\"center\">" . $row['state'] . "</td>
						<td align=\"right\" class=\"grid-body-right\">$ <input type=\"text\" class=\"form-box-textfield money\" name=\"o_surcharge[" . $row['id'] . "]\" value=\"0.00\" /></td>
					</tr>";
        }
    }

    if ($fl) {
        $cities = "<tr><td colspan=\"4\" style=\"background:#e1e1e1;\"><strong>" . trim($_POST['state']) . "</strong></td></tr>";
        $cities .= $list;
    }
}
$out = array('success' => true, 'cities' => $cities);
echo $json->encode($out);
require_once "done.php";
