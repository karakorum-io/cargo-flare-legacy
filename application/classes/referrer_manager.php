<?php	/**************************************************************************************************	* ReferrerManager class																																			*	* Class for work with referrers																																*	*																																											*	* Client:		FreightDragon																																	*	* Version:		1.0																																					*	* Date:			2011-11-01																																		*	* Author:		C.A.W., Inc. dba INTECHCENTER																											*	* Address:	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076																	*	* E-mail:		techsupport@intechcenter.com																											*	* CopyRight 2011 FreightDragon. - All Rights Reserved																								*	***************************************************************************************************/		class ReferrerManager extends FdObjectManager {		const TABLE = Referrer::TABLE;				public function get($order = null, $per_page = 100, $where = '') {			$rows = parent::get($order, $per_page, $where);			$referrers = array();			foreach ($rows as $row) {				$referrer = new Referrer($this->db);				$referrer->load($row['id']);				$referrers[] = $referrer;			}			return $referrers;		}	}?>