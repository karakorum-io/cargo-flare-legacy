<?php
/**
 * @version		1.0
 * @since		07.08.12
 * @author		Oleg Ilyushyn, C.A.W., Inc. dba INTECHCENTER
 * @address		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * @email		techsupport@intechcenter.com
 * @copyright	2012 Intechcenter. All Rights Reserved
 */ 
class ProductManager extends FdObjectManager {
	const TABLE = Product::TABLE;

	/**
	 * @param null   $order
	 * @param int    $per_page
	 * @param string $where
	 *
	 * @return Product[]
	 */
	public function get($order = null, $per_page = 100, $where = '') {
		$rows = parent::get($order, $per_page, $where);
		$products = array();
		foreach ($rows as $row) {
			$product = new Product($this->db);
			$product->load($row['id']);
			$products[] = $product;
		}
		return $products;
	}
}
