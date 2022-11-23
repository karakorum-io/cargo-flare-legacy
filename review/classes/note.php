<?php

	/**************************************************************************************************

	* Note class																																							*

	* This class represent one note																																*

	*																																											*

	* Client:		FreightDragon																																	*

	* Version:		1.0																																					*

	* Date:			2011-09-28																																		*

	* Author:		C.A.W., Inc. dba INTECHCENTER																											*

	* Address:	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076																	*

	* E-mail:		techsupport@intechcenter.com																											*

	* CopyRight 2011 FreightDragon. - All Rights Reserved																								*

	***************************************************************************************************/

	class Note extends FdObject {

		const TABLE = "app_notes";

		

		const TYPE_TO = 1;

		const TYPE_FROM = 2;

		const TYPE_INTERNAL = 3;

		const TYPE_INTERNALNEW = 10;

		/* GETTERS */

		

		public function getCreated($format = "Y-m-d H:i:s") {

			return date($format, strtotime($this->created));

		}

		

		public function getSender() {
			
			 try{

					$sender = new Member($this->db);
		
					$sender->load($this->sender_id);
		
					return $sender;
             } catch (FDException $e) {
			    return null;
			}
		}

		

		public function getText() {

			return htmlentities(stripslashes($this->text));

		}

	}

?>