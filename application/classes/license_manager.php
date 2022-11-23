<?php

/**
 * @version		1.0
 * @since		21.08.12
 * @author		Oleg Ilyushyn, C.A.W., Inc. dba INTECHCENTER
 * @address		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * @email		techsupport@intechcenter.com
 * @copyright	2012 Intechcenter. All Rights Reserved
 */
class LicenseManager extends FdObjectManager {

		const TABLE = License::TABLE;

		public function get($order = null, $per_page = 100, $where = null) {
				$rows = parent::get($order, $per_page, $where);
				$licenses = array();
				foreach ($rows as $row) {
						$license = new License($this->db);
						$license->load($row['id']);
						$licenses[] = $license;
				}
				return $licenses;
		}

		/**
		 * Check if customer 4 month have active license
		 * @param type $owner_id 
		 * @return true / false
		 */
		final public function get4MonthActive($owner_id) {
				if (is_null($this->db))
						throw new FDException(get_class($this) . "->load: DB helper not set");
				if (!ctype_digit((string) $owner_id))
						throw new FDException(get_class($this) . "->load: invalid owner_id");

				//check if not frozen
				$member = new Member($daffny->DB);
				$member->load($owner_id);
				$profile = $member->getCompanyProfile();
				if (!$profile->is_frozen) {
						//check if was not breaks
								$q = $this->db->query("SELECT * 
																		FROM `" . License::TABLE . "` 
																		WHERE `owner_id` = '" . $owner_id . "'
																		ORDER BY id ASC
								");
								//check if
								$fl = false; //no one licenses
								$i = 0;
								$created = "";
								$expire = "";
								while ($row = $this->db->fetch_row($q)) {
										$fl = true;
										if ($i > 0){
												//if new created time more then 3 days of last expire
												if(strtotime($row["created"]) > $expire+86400*4){
														return false;
												}
										}
										$created = strtotime($row["created"]);
										$expire = strtotime($row["expire"]);
										$i++;
								}
								
								if ($expire <= time()){ //if last license expired
										return false;
								}
								return $fl;
				} else {
						return false;
				}
		}

}
