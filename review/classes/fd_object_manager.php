<?php

	/**************************************************************************************************

	* FdObjectManager class																																		*

	* Abstract class with most common functions for object managers																				*

	*																																											*

	* Client:		FreightDragon																																	*

	* Version:		1.0																																					*

	* Date:			2011-10-17																																		*

	* Author:		C.A.W., Inc. dba INTECHCENTER																											*

	* Address:	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076																	*

	* E-mail:		techsupport@intechcenter.com																											*

	* CopyRight 2011 FreightDragon. - All Rights Reserved																								*

	***************************************************************************************************/



	abstract class FdObjectManager {

		/**

		 * @var mysql DB

		 */

		protected $db = null;

		protected $pager = null;

		const TABLE = null;



		public function __construct($param = null) {

			if ($param instanceof mysql) {

				$this->setDbHelper($param);

			}

		}



		public function setDbHelper($db = null) {

			if (!($db instanceof mysql)) throw new FDException(get_class($this) . "->setDbHelper: invalid DB helper");

			$this->db = $db;

		}



		public function getPager() {

			return $this->pager;

		}



		protected function get($order = null, $per_page = 50, $where = null) {
                        
			$where_str = "";

			if (!is_null($where)) $where_str .= $where;



			if (trim($where_str) != "" && stripos(strtoupper($where), "WHERE") === false ){

				$where_str = "WHERE ".$where_str;

			}

			$where_str_limit = "";

			if (!is_null($per_page) && ctype_digit((string)$per_page)) {

				$this->pager = new PagerRewrite($this->db);

				$this->pager->UrlStart = getLink();

				$this->pager->RecordsOnPage = $per_page;

				$this->pager->init(static::TABLE, "", trim($where_str));

				$where_str_limit = " ".$this->pager->getLimit()." ";

			}

			if (!is_null($order)) $where_str .= " ".$order." ".$where_str_limit;

			return $this->db->selectRows("`id`", static::TABLE, $where_str);

		}
		
protected function getAll($order = null, $per_page = 50, $where = null) {

			$where_str = "";

			if (!is_null($where)) $where_str .= $where;



			if (trim($where_str) != "" && stripos(strtoupper($where), "WHERE") === false ){

				$where_str = "WHERE ".$where_str;

			}

			$where_str_limit = "";

			if (!is_null($per_page) && ctype_digit((string)$per_page)) {

				$this->pager = new PagerRewrite($this->db);

				$this->pager->UrlStart = getLink();

				$this->pager->RecordsOnPage = $per_page;

				$this->pager->init(static::TABLE, "", trim($where_str));

				$where_str_limit = " ".$this->pager->getLimit()." ";

			}

			if (!is_null($order)) $where_str .= " ".$order." ".$where_str_limit;

			return $this->db->selectRows("*", static::TABLE, $where_str);

		}

	}

?>