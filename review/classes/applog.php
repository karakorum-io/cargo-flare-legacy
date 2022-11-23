<?php



/* * ************************************************************************************************

 * AppLog class																																						*

 * Class for working with shipper																																*

 * 																																											*

 * Client:		FreightDragon																																	*

 * Version:		1.0																																					*

 * Date:			2011-09-28																																		*

 * Author:		C.A.W., Inc. dba INTECHCENTER																											*

 * Address:	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076																	*

 * E-mail:		techsupport@intechcenter.com																											*

 * CopyRight 2011 FreightDragon. - All Rights Reserved																								*

 * ************************************************************************************************* */



class Applog extends FdObject {
	
    const TABLE = "app_log";
	
	const TYPE_INFORMATION = 0;
    const TYPE_EXCEPTION = 1;
    const TYPE_MYSQL = 2;

  public function createInformation($data = null)
	{
		$ins_arr = array(
            'description' => addslashes($data)
            , 'user_id' => $_SESSION['member']['id']
            , 'type' => self::TYPE_INFORMATION
            
            );
		
		$data = $this->db->PrepareSql(self::TABLE, $ins_arr);
		$this->db->insert(self::TABLE, $data);
	}
	
    public function createException($data = null)
	{
		$ins_arr = array(
            'description' => addslashes($data)
            , 'user_id' => $_SESSION['member']['id']
            , 'type' => self::TYPE_EXCEPTION
            
            );
		//print_r($ins_arr);
		//$id = parent::create($ins_arr);
		$data = $this->db->PrepareSql(self::TABLE, $ins_arr);
		$this->db->insert(self::TABLE, $data);

	}
	
	
	
  public function createMysql($data = null)
	{
		$ins_arr = array(
            'description' => $data
            , 'user_id' => $_SESSION['member']['id']
            , 'type' => self::TYPE_MYSQL
            
            );
		
		$data = $this->db->PrepareSql(self::TABLE, $ins_arr);
		$this->db->insert(self::TABLE, $data);
	}

    public function update($data = null) {
       parent::update($data);
   }
    
}

?>