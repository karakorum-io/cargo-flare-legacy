<?php

/* * *************************************************************************************************
 * Orders CP Class                                                                 
 *                                                                              		
 *                                                                                 
 * Client: 	FreightDragon                                                         
 * Version: 	1.0                                                                   
 * Date:    	2012-09-27                                                            
 * Author:  	C.A.W., Inc. dba INTECHCENTER                                         
 * Address: 	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076            
 * E-mail:	techsupport@intechcenter.com                                            
 * CopyRight 2011 FreightDragon. - All Rights Reserved                             
 * ************************************************************************************************** */

class CpOrders extends CpAction {

		public $title = "Orders";

		/**
		 * List all 
		 *
		 */
		public function idx() {
				redirect(getLink("reports", "sales"));
		}

		public function show() {
				$id = (int) get_var("id");
				$this->tplname = "orders.show";
				$this->title .= " - Show";


				$sql = "SELECT o.*
										, DATE_FORMAT(o.register_date, '%m/%d/%Y %h:%i %p') AS register_date_format
										, c.code AS coupon_code
										, (SELECT COUNT(*) FROM orders_comments WHERE order_id = o.id) AS comment_total
										, m.phone
										, m.email
										, o.card_expire  AS card_expiration
										, o.id AS order_id
										, DATE_FORMAT(o.register_date, '%m/%d/%Y %h:%i %p') AS order_date
                      FROM orders o
											LEFT JOIN coupons c ON c.id = o.coupon_id
											LEFT JOIN members m ON m.id = o.member_id
                     WHERE o.id = '" . $id . "'";
				$row = $this->daffny->DB->selectRow($sql);
				if (empty($row)){
						redirect(getLink("reports", "sales"));
				}
				$row["status"] = Orders::getStatusLabel($row["status"]);
				$row["card_type"] = Creditcard::$cctype_name[$row["card_type_id"]];
				if ($_SESSION['member']['group_id'] != 2) {
						$row["card_number"] = "**** **** **** " . substr($row["card_number"], -4);
						$row["card_cvv2"] = "****";
				}
				$this->input = $row;
				$this->daffny->tpl->data = $this->input;

				//comments

				$this->input["comments"] = "";
				$sql = " SELECT a.* " .
								"      , DATE_FORMAT(a.register_date, '%m/%d/%Y %h:%i %p') AS register_date_format " .
								"      , CONCAT(b.first_name, ' ', b.last_name) AS owner_name " .
								"      , b.id AS owner_id " .
								"   FROM orders_comments a " .
								"        INNER JOIN administrators b ON b.id = a.administrator_id " .
								"  WHERE order_id = '" . quote($row["id"]) . "' " .
								"  ORDER BY register_date DESC ";
				$comments = $this->daffny->DB->query($sql);
				while ($comment = $this->daffny->DB->fetch_row($comments)) {
						$comment["is_delete_visible"] = "block";
						$this->input["comments"] .= $this->daffny->tpl->build("orders.comment", $comment);
				}


				$this->daffny->tpl->products = array();

				$sql_d = "
						SELECT
								  od.*
								, p.code AS item
								, p.name AS product
						FROM orders_details od
							LEFT JOIN products p ON p.id = od.product_id
						WHERE od.order_id = '" . quote((int) $_GET["id"]) . "'
				";

				$q_d = $this->daffny->DB->query($sql_d);
				while ($row_d = $this->daffny->DB->fetch_row($q_d)) {
						$row_d["price"] = number_format($row_d["price"], 2);
						$row_d["total"] = number_format($row_d["total"], 2);
						$this->daffny->tpl->products[] = $row_d;
				}
				
				if (isset($_GET["print"])){
						$this->tplname = "orders.print";
				}
				
		}


		public function delete() {
				$ID = $this->checkId();
				$out = array('success' => false);
				try {
						$this->daffny->DB->delete("orders", "id = $ID");
						if ($this->daffny->DB->isError) {
								throw new Exception($this->getDBErrorMessage());
						} else {
								$out = array('success' => true);
						}
				} catch (FDException $e) {
						
				}
				die(json_encode($out));
		}

}

?>