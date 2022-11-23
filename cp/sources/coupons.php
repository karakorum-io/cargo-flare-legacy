<?

/* * *************************************************************************************************
 * Control Panel - Coupons                                                                                 *
 *                                                                                                  *
 * Client:     PitBullTax                                                                             *
 * Version:     1.1                                                                                    *
 * Date:        2010-05-31                                                                             *
 * Author:      C.A.W., Inc. dba INTECHCENTER                                                          *
 * Address:     11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076                             *
 * E-mail:    techsupport@intechcenter.com                                                           *
 * CopyRight 2010-2011 NEGOTIATION TECHNOLOGIES, LLC. - All Rights Reserved                                 *
 * ************************************************************************************************** */

class CpCoupons extends CpAction {

		// Daffny Engine
		var $daffny;
		var $title;
		var $products = "";
		var $time_to_use = array(
				1 => "1"
				, 2 => "2"
				, 3 => "3"
				, 4 => "4"
				, 5 => "5"
				, 6 => "6"
				, 7 => "7"
				, 8 => "8"
				, 9 => "9"
				, 0 => "unlimited"
		);
		var $status = array(
				"active" => "Active"
				, "disabled" => "Disabled"
		);
		var $type = array(
				"0" => "Affiliate"
				, "1" => "Admin"
		);
		var $err;

		function init() {
				$_SESSION["errors"] = "";
		}

		function idx() {
				$this->title = "Coupons Management";
				$this->tplname = "coupons.list";

				if (isset($_POST["on_page"])) {
						$_SESSION["cp_onpage"] = post_var("on_page");
				}

				$this->input = array(
						"s_period" => $this->daffny->html->select("period", array("" => "All", "1" => "Current Month", "2" => "Current Year", "3" => "Date Range"), get_var("period"), array("id" => "period", "style" => "width:136px;"))
						, "s_date_from" => stripslashes(get_var("date_from"))
						, "s_date_to" => stripslashes(get_var("date_to"))
						, "s_code" => stripslashes(get_var("code"))
						, "s_company" => stripslashes(get_var("company"))
						, "s_status" => $this->daffny->html->select("status", array("" => "All", "active" => "Active", "expired" => "Expired", "disabled" => "Disabled"), get_var("status"), array("id" => "status", "style" => "width:136px;"))
				);

				// Limit
				$where = " WHERE is_delete = 0 ";
				if (get_var("code") != "") {
						$where .= $where == "" ? " WHERE " : " AND ";
						$where .= "code LIKE '%" . mysqli_real_escape_string($this->daffny->DB->connection_id, get_var("code")) . "%'";
				}
				if (get_var("first_name") != "") {
						$where .= $where == "" ? " WHERE " : " AND ";
						$where .= "first_name LIKE '%" . mysqli_real_escape_string($this->daffny->DB->connection_id, get_var("first_name")) . "%'";
				}
				if (get_var("last_name") != "") {
						$where .= $where == "" ? " WHERE " : " AND ";
						$where .= "last_name LIKE '%" . mysqli_real_escape_string($this->daffny->DB->connection_id, get_var("last_name")) . "%'";
				}
				if (get_var("company") != "") {
						$where .= $where == "" ? " WHERE " : " AND ";
						$where .= "company LIKE '%" . mysqli_real_escape_string($this->daffny->DB->connection_id, get_var("company")) . "%'";
				}
				if (get_var("status") != "") {
						$where .= $where == "" ? " WHERE " : " AND ";
						$where .= "(CASE WHEN status = 'active' AND is_never_expire = 0 AND expire_date < NOW() THEN 'expired' ELSE status END) = '" . mysqli_real_escape_string($this->daffny->DB->connection_id, get_var("status")) . "'";
				}
				switch (get_var("period")) {
						case "1": {
										$where .= $where == "" ? " WHERE " : " AND ";
										$where .= "register_date BETWEEN CAST(CONCAT(YEAR(NOW()), '-', MONTH(NOW()), '-01') AS DATE) AND DATE_ADD(CAST(CONCAT(YEAR(NOW()), '-', (MONTH(NOW()) + 1), '-01') AS DATE), INTERVAL -1 SECOND)";

										break;
								}
						case "2": {
										$where .= $where == "" ? " WHERE " : " AND ";
										$where .= "register_date BETWEEN CAST(CONCAT(YEAR(NOW()), '-01-01') AS DATE) AND DATE_ADD(CAST(CONCAT((YEAR(NOW()) + 1), '-01-01') AS DATE), INTERVAL -1 SECOND)";

										break;
								}
						case "3": {
										if (get_var("date_from") != "") {
												$where .= $where == "" ? " WHERE " : " AND ";
												$where .= "register_date >= '" . convertUSA2SQLDate(get_var("date_from")) . " 00:00:00'";
										}
										if (get_var("date_to") != "") {
												$where .= $where == "" ? " WHERE " : " AND ";
												$where .= "register_date <= '" . convertUSA2SQLDate(get_var("date_to")) . " 23:59:59'";
										}
										break;
								}
				}
				$this->applyPager(Coupon::TABLE, 'id', $where);
				$this->applyOrder();
				// Order
				$this->order->setDefault("id", "ASC");
				$this->input["id"] = $this->order->getTitle("id", "ID");
				$this->input["code"] = $this->order->getTitle("code", "Code");
				$this->input["time_to_use"] = $this->order->getTitle("time_to_use", "Time To Use");
				$this->input["expires"] = $this->order->getTitle("expire_date", "Expires");
				$this->input["company"] = $this->order->getTitle("company", "Company");
				$this->input["status"] = $this->order->getTitle("status", "Status");
				

				if (post_var("action") == "export_selected") {
						$where .= $where == "" ? " WHERE " : " AND ";
						$where .= " id IN (" . join(",", @$_POST["is_check"]) . ") ";
						$this->export($where . $this->order->getOrder());
				}
				if (get_var("action") == "export_all_found") {
						$this->export($where . $this->order->getOrder());
				}
				$cm = new CouponManager($this->daffny->DB);
				$this->daffny->tpl->coupons = $cm->get($this->order->getOrder(), $_SESSION['per_page'], $where);
				$this->input["errors"] = @$_SESSION["errors"];
		}

		function export($where = "") {
				$sql = " SELECT id " .
								"      , code " .
								"      , time_to_use " .
								"      , is_per_customer " .
								"      , expire_date " .
								"      , CASE WHEN expire_date IS NOT NULL AND is_never_expire = 0 THEN expire_date ELSE 'never' END AS expire_date " .
								"      , company " .
								"      , CASE WHEN status = 'active' AND is_never_expire = 0 AND expire_date < NOW() THEN 'expired' ELSE status END AS status " .
								"   FROM coupons " .
								$where;

				$header = array("ID", "Code", "Time To Use", "Per Customer", "Expires", "First Name", "Last Name", "Company", "Status", "Type");
				$buffer = exportCSVRecord($header);
				$result = $this->daffny->DB->query($sql);
				while ($row = $this->daffny->DB->fetch_row($result)) {
						$row["time_to_use"] = ($row["time_to_use"] ? $row["time_to_use"] : "unlimited");
						$row["is_per_customer"] = ($row["is_per_customer"] ? "Yes" : "No");
						$row["expire_date"] = ($row["expire_date"] == "never" ? $row["expire_date"] : convertSQL2USADate($row["expire_date"]));
						$row["status"] = ucfirst($row["status"]);
						

						$buffer .= exportCSVRecord($row);
				}

				$file_name = "Coupons.csv";
				header("Content-Type: application; filename=\"" . $file_name . "\"");
				header("Content-Disposition: attachment; filename=\"" . $file_name . "\"");
				header("Content-Description: \"" . $file_name . "\"");
				header("Expires: 0");
				header("Cache-Control: private");
				header("Pragma: cache");

				echo $buffer;
				exit();
		}

		function edit() {
				$this->title = "Edit Coupon";
				$this->tplname = "coupons.edit";
				try {
						$coupon = new Coupon($this->daffny->DB);
						if (validate_id($_GET["id"])) {
								$coupon->load(get_var('id'));
								$row = $coupon->getAttributes();
								$row["is_per_customer"] = ($row["is_per_customer"] ? "checked=\"checked\"" : "");
								$row["expire_date"] = ($row["expire_date"] ? preg_replace('/^([0-9]{4}).?([0-9]{2}).?([0-9]{2})$/', '\\3/\\2/\\1', $row["expire_date"]) : "");
								$row["is_never_expire"] = ($row["is_never_expire"] ? "checked=\"checked\"" : "");
								if (isset($_GET["err"])) {
										$row = $_SESSION["upd"];
								}
								$this->input = $row;
								$this->input["errors"] = $_SESSION["errors"];
						} else if (isset($_GET["id"]) && $_GET["id"] == "new") {
								$this->title = "New Coupon";
								$new_arr = array(
										"id" => "new"
										, "code" => ""
										, "time_to_use" => ""
										, "expire_date" => ""
										, "is_never_expire" => "1"
										, "company" => ""
										, "status" => "active"
								);
								$this->input = $new_arr;
								if (isset($_GET["err"])) {
										$this->input = $_SESSION["upd"];
								}
								$this->input["errors"] = $_SESSION["errors"];
						} else {
								throw new FDException('Action N/A');
						}

						$this->daffny->DB->select("p.id, p.code, p.name, cd.is_percent_discount, cd.discount", "products p LEFT OUTER JOIN coupon_details cd ON cd.product_id = p.id AND cd.coupon_id = '" . get_var('id') . "'", " WHERE p.is_delete <> 1");
						while ($products_row = $this->daffny->DB->fetch_row()) {
								$this->products .= "<tr>";
								$this->products .= "    <td align=\"center\">" . $products_row["code"] . "</td>";
								$this->products .= "    <td>" . $products_row["name"] . "</td>";
								$this->products .= "    <td><input type=\"text\" name=\"discount[]\" value=\"" . $products_row["discount"] . "\"  style=\"width:65px; text-align:right;\" /><input type=\"hidden\" name=\"product_id[]\" value=\"" . $products_row["id"] . "\" /></td>";
								$this->products .= "    <td><input type=\"checkbox\" name=\"is_percent_discount[" . $products_row["id"] . "]\" id=\"is_percent_discount_" . $products_row["id"] . "\" " . ($products_row["is_percent_discount"] ? "checked=\"checked\"" : "") . " value=\"1\" style=\"vertical-align: middle;\" /> <label for=\"is_percent_discount" . $products_row["id"] . "\">Is Percent</label></td>";
								$this->products .= "</tr>";
						}
						$this->input["products"] = $this->products;
						$this->input["time_to_use"] = $this->daffny->html->select("time_to_use", $this->time_to_use, $this->input["time_to_use"], array("id" => "time_to_use", "style" => "width: 100px;"));
						$this->input["status"] = $this->daffny->html->select("status", $this->status, $this->input["status"], array("id" => "status", "style" => "width: 100px;"));

						$this->daffny->tpl->data = &$this->input;
				} catch (FDException $e) {
						$_SESSION["errors"] = $this->err;
						redirect('coupons');
				}
		}

		function save() {
				try {
						if (!((isset($_POST["submit"])) && isset($_POST["id"])))
								throw new FDException('Action N/A');
						$upd = array(
								"id" => post_var("id")
								, "code" => post_var("code")
								, "time_to_use" => post_var("time_to_use")
								, "is_per_customer" => post_var("is_per_customer")
								, "expire_date" => post_var("expire_date")
								, "is_never_expire" => post_var("is_never_expire")
								, "company" => post_var("company")
								, "status" => post_var("status")
						);

						if (trim(@$_POST["expire_date"]) == "" && empty($_POST["is_never_expire"])) {
								$this->err[] = "<strong>Expires</strong> is required, or check <strong>Never expires</strong>.";
						}



						if (!count($this->err)){
								$coupon = new Coupon($this->daffny->DB);
								if (post_var("id") == "new") {
										$ins = array(
												"code" => quote($coupon->generateCoupon())
												, "time_to_use" => quote(post_var("time_to_use") > 0 ? post_var("time_to_use") : "NULL")
												, "is_per_customer" => (post_var("is_per_customer") ? 1 : 0)
												, "expire_date" => quote(post_var("expire_date") ? preg_replace('/^([0-9]{2}).?([0-9]{2}).?([0-9]{4})$/', '\\3-\\2-\\1', post_var("expire_date")) : 'NULL')
												, "is_never_expire" => (post_var("is_never_expire") ? 1 : 0)
												, "company" => quote(post_var("company"))
												, "status" => quote(post_var("status"))
												, "is_delete" => 0
												, "register_date" => date('Y-m-d H:i:s')
										);
										$new_coupon_id = $coupon->create($ins);
										$discount = post_var("discount");
										$is_percent_discount = post_var("is_percent_discount");
										foreach (post_var("product_id") as $k => $v) {
												if ($discount[$k]) {
														$this->daffny->DB->query("REPLACE coupon_details SET coupon_id = \"" . $new_coupon_id . "\",  product_id = \"" . $v . "\",  is_percent_discount = \"" . (@$is_percent_discount[$v] ? "1" : "0") . "\",  discount = \"" . $discount[$k] . "\"");
												}
										}
								} else {
										$update = array(
												"code" => quote(post_var("code"))
												, "time_to_use" => quote(post_var("time_to_use") > 0 ? post_var("time_to_use") : "NULL")
												, "is_per_customer" => (post_var("is_per_customer") ? 1 : 0)
												, "expire_date" => quote(post_var("expire_date") ? preg_replace('/^([0-9]{2}).?([0-9]{2}).?([0-9]{4})$/', '\\3-\\2-\\1', post_var("expire_date")) : 'NULL')
												, "is_never_expire" => (post_var("is_never_expire") ? 1 : 0)
												, "company" => quote(post_var("company"))
												, "status" => quote(post_var("status"))
										);
										$coupon->load(post_var('id'));
										$coupon->update($update);
										$discount = post_var("discount");
										$is_percent_discount = post_var("is_percent_discount");

										$this->daffny->DB->delete("coupon_details", "coupon_id = \"" . post_var("id") . "\"");

										foreach (post_var("product_id") as $k => $v) {
												if ($discount[$k]) {
														$this->daffny->DB->query("REPLACE coupon_details SET coupon_id = \"" . post_var("id") . "\",  product_id = \"" . $v . "\",  is_percent_discount = \"" . (@$is_percent_discount[$v] ? "1" : "0") . "\",  discount = \"" . $discount[$k] . "\"");
												}
										}
								}
								$this->setFlashInfo('Coupon saved');
								redirect(getLink('coupons'));
						} else {
								$_SESSION["upd"] = $upd;
								$this->setFlashError($this->err);
								redirect(getLink('coupons/edit/id/'.post_var('id').'/err'));
						}
				} catch (FDException $e) {
						$this->setFlashError('Failed to save Coupon');
						redirect(getLink('coupons'));
				}
		}

		function delete() {
				try {
						if (!validate_id($_GET["id"]))
								throw new FDException('Action N/A');
						$coupon = new Coupon($this->daffny->DB);
						$coupon->load($_GET['id']);
						$coupon->update(array('is_delete' => 1));
						redirect(getLink('coupons'));
				} catch (FDException $e) {
						$_SESSION["errors"] = "Action N/A.";
						redirect(getLink('coupons'));
				}
		}

}