<?php
/***************************************************************************************************
* Aproove Documents CP Class
*
*
* Client: 	FreightDragon
* Version: 	1.0
* Date:    	2011-11-17
* Author:  	C.A.W., Inc. dba INTECHCENTER
* Address: 	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
* E-mail:	techsupport@intechcenter.com
* CopyRight 2011 FreightDragon. - All Rights Reserved
****************************************************************************************************/

class Cpdocuments extends CpAction
{
    public $title = "Documents";

    public function idx()
    {
        $this->tplname = "documents.list";
        //Show by member ID    
        $where = "";
        if (isset($_GET["member_id"]) && $_GET["member_id"]>0){
            $where = "WHERE d.owner_id = '".(int)$_GET["member_id"]."'";
            $this->title .= " (By Company)";
        }
        
        $this->applyPager("app_documents d", "", $where);
        $this->orderdef = true;
        
        $this->applyOrder("app_documents");

        $sql = "SELECT d.*
        			,p.companyname
        			, IF (d.status=1, 'Aproved', 'Pending') AS status
                  FROM app_documents d
                  	LEFT JOIN app_company_profile p ON p.owner_id = d.owner_id
                  "
                     . $where
                     . $this->order->getOrder()
                     . $this->pager->getLimit();
        $this->getGridData($sql, false);
    }

    public function delete(){
        $ID = $this->checkId();
    	$out = array('success' => false);
    	try {
	        $this->daffny->DB->delete("app_documents", "id = $ID");
	        if ($this->daffny->DB->isError){
						throw new Exception($this->getDBErrorMessage());
			}else{
				$out = array('success' => true);
			}
		} catch (FDException $e) {}
		die(json_encode($out));
    }

    public function status(){
    	$out = array('success' => false);
        $id = $this->checkId();
        $this->daffny->DB->transaction("start");
		try{
	        $this->daffny->DB->query("UPDATE app_documents SET status = (CASE WHEN status = '1' THEN '0' ELSE '1' END) WHERE id = '".$id."'");
	        $this->daffny->DB->transaction("commit");
	        $out = array('success' => true);
        }catch (Exception $e){
			$this->daffny->DB->transaction("rollback");
			$out = array('success' => false);
		}
        die( json_encode($out));
    }

    public function getdocs(){
    	$ID = (int)get_var("id");
    	$file = $this->daffny->DB->select_one("*", "app_documents", "WHERE id = '".$ID."'");
        if (!empty($file)){
			$row = $this->daffny->DB->select_one("*", "app_company_profile", "WHERE owner_id = '".$file['owner_id']."'");
			if (!empty($row) && $row['is_allowed'] == 1)
			{
				$file_path = UPLOADS_PATH."documents/".$file["name_on_server"];
	            $file_name = $file["name_original"];
	            $file_size = $file["size"];
	            if (file_exists($file_path)){
	                header("Content-Type: application; filename=\"".$file_name."\"");
	                header("Content-Disposition: attachment; filename=\"".$file_name."\"");
	                header("Content-Description: \"".$file_name."\"");
	                header("Content-length: ".$file_size);
	                header("Expires: 0");
	                header("Cache-Control: private");
	                header("Pragma: cache");
	                $fptr = @fopen($file_path, "r");
	                $buffer = @fread($fptr, filesize($file_path));
	                @fclose($fptr);
	                echo $buffer;
	                exit(0);
	            }
			}
        }
        header("HTTP/1.0 404 Not Found");
        exit(0);
    }
}
?>