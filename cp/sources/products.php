<?

/***************************************************************************************************
 * Control Panel - Products                                                                                  *
 *                                                                                                  *
 * Client:     PitBullTax                                                                             *
 * Version:     1.1                                                                                    *
 * Date:        2010-05-31                                                                             *
 * Author:      C.A.W., Inc. dba INTECHCENTER                                                          *
 * Address:     11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076                             *
 * E-mail:    techsupport@intechcenter.com                                                           *
 * CopyRight 2010-2011 NEGOTIATION TECHNOLOGIES, LLC. - All Rights Reserved                                 *
 ****************************************************************************************************/


class CpProducts extends CpAction {
	/**
	 * @var Daffny $daffny
	 */
	var $daffny;
	var $title;
	var $periods = array();
	var $types = array();

	var $err;

	function init() {
		$_SESSION["errors"] = "";
	}

	function idx() {
		$this->title = "Products Management";
		$this->tplname = "products.list";

		// Order
		$this->applyPager(Product::TABLE);
		$this->applyOrder(Product::TABLE);
		$this->order->setDefault('code', 'ASC');
		$this->input["id"] = $this->order->getTitle("id", "ID");
		$this->input["code"] = $this->order->getTitle("code", "Code");
		$this->input["name"] = $this->order->getTitle("name", "Name");
		$this->input["price"] = $this->order->getTitle("price", "Price");
		$this->input["description"] = $this->order->getTitle("description", "Description");
		$this->input["period_id"] = $this->order->getTitle("period_id", "Period");
		$this->input["renewal"] = $this->order->getTitle("renewal_code", "Renewal Code");
		$this->input["type_id"] = $this->order->getTitle("type_id", "Type");
		$pm = new ProductManager($this->daffny->DB);
		$this->daffny->tpl->products = $pm->get($this->order->getOrder(), $_SESSION['per_page']);
		$this->input["errors"] = @$_SESSION["errors"];
	}

	function edit() {
		$this->title = "Edit Product";
		$this->tplname = "products.edit";
		try {
			if (validate_id($_GET["id"])) {
				$product = new Product($this->daffny->DB);
				$product->load(get_var('id'));
				$row = $product->getAttributes();
				$row["is_online"] = ($row["is_online"] ? "checked=\"checked\"" : "");
				if (isset($_GET["err"])) {
					$row = $_SESSION["upd"];
				}
				$this->input = $row;
				$this->input["errors"] = $_SESSION["errors"];
			} else if (isset($_GET["id"]) && $_GET["id"] == "new") {
				$this->title = "New Product";
				$new_arr = array(
					"id" => "new",
					"code" => "",
					"name" => "",
					"price" => "0.00",
					"description" => "",
					"period_id" => "1",
					"renewal_code" => "",
					"type_id" => "1",
				);
				$this->input = $new_arr;
				if (isset($_GET["err"])) {
					$this->input = $_SESSION["upd"];
				}

				$this->input["errors"] = $_SESSION["errors"];
			} else {
				redirect(getLink('products'));
			}
			$this->input['type'] = $this->daffny->html->select('type_id', Product::getTypes(), $this->input['type_id']);
			$this->input['period'] = $this->daffny->html->select('period_id', Product::getPeriods(), $this->input['period_id']);
			$this->daffny->tpl->data = &$this->input;
		} catch (FDException $e) {
			redirect(getLink('products'));
		}
	}

	function save() {
		try {
			if ((isset($_POST["submit"])) && isset($_POST["id"])) {
				$upd = array(
					"id" => post_var("id")
				, "code" => post_var("code")
				, "name" => post_var("name")
				, "price" => post_var("price")
				, "description" => post_var("description")
				, "period_id" => post_var("period_id")
				, "renewal_code" => post_var("renewal_code")
				, "type_id" => post_var("type_id")
				, "is_online" => post_var("is_online")
				);

				if (!post_var("code")) {
					$this->err[] = "<strong>Code</strong> is required.";
				}
				if (!post_var("name")) {
					$this->err[] = "<strong>Name</strong> is required.";
				}
				if (!post_var("price")) {
					$this->err[] = "<strong>Price</strong> is required.";
				}

				if ($this->err) {
					$_SESSION["upd"] = $upd;
					$this->setFlashError('<p>'.implode('</p><p>', $this->err).'</p>');
					redirect(getLink('products/edit/id'.post_var('id').'/err'));
				} else {
					if (post_var("id") == "new") {
						$ins = array(
							"code" => quote(post_var("code"))
						, "name" => quote(post_var("name"))
						, "price" => quote(post_var("price"))
						, "description" => quote(post_var("description"))
						, "period_id" => post_var("period_id")
						, "renewal_code" => (post_var("renewal_code") ? quote(post_var("renewal_code")) : 'NULL')
						, "type_id" => post_var("type_id")
						, "is_online" => (post_var("is_online") ? 1 : 0)
						, "is_delete" => 0
						, "register_date" => date('Y-m-d H:i:s')
						);
						$product = new Product($this->daffny->DB);
						$_SESSION["upd"] = $upd;
						$product->create($ins);
					} else {
						$update = array(
							"code" => quote(post_var("code"))
						, "name" => quote(post_var("name"))
						, "price" => quote(post_var("price"))
						, "description" => quote(post_var("description"))
						, "period_id" => post_var("period_id")
						, "renewal_code" => (post_var("renewal_code") ? quote(post_var("renewal_code")) : 'NULL')
						, "type_id" => post_var("type_id")
						, "is_online" => (post_var("is_online") ? 1 : 0)
						);

						$this->daffny->DB->transaction();
						$product = new Product($this->daffny->DB);
						$product->load(post_var('id'));
						$_SESSION["upd"] = $upd;
						$product->update($update);
						$this->daffny->DB->transaction('commit');
					}
					redirect(getLink("products"));
				}
			} else {
				$_SESSION["errors"] = $this->daffny->msg("Action N/A.");
				redirect(getLink("products"));
			}
		} catch (FDException $e) {
			$this->daffny->DB->transaction('rollback');
			$_SESSION["errors"] = $this->daffny->msg($this->err);
			redirect(getLink('products/edit/id/' . post_var('id') . '/err'));
		}
	}

	function delete() {
		try {
			if (validate_id($_GET["id"])) {
				$product = new Product($this->daffny->DB);
				$product->update(array('is_delete' => 1));
				$_SESSION["errors"] = $this->daffny->msg("Product <b>'#" . $_GET["id"] . "'</b> was deleted.", "info");
			} else {
				$_SESSION["errors"] = $this->daffny->msg("Action N/A.");
			}
		} catch (FDException $e) {
			$_SESSION['errors'] = $this->daffny->msg($this->getDBErrorMessage());
		}
		redirect(getLink("products"));
	}
}