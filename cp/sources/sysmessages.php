<?php
/***************************************************************************************************
*  System messages CP Class                                                                 	   *
*  Any message posted by Super Administrator to be visible to all users.                           *
*                                                                                                  *
* Client: 	FreightDragon                                                                          *
* Version: 	1.0                                                                                    *
* Date:    	2011-09-28                                                                             *
* Author:  	C.A.W., Inc. dba INTECHCENTER                                                          *
* Address: 	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076                             *
* E-mail:	techsupport@intechcenter.com                                                           *
* CopyRight 2011 FreightDragon. - All Rights Reserved                                              *
****************************************************************************************************/

class CpSysmessages extends CpAction
{
    public $title = "System messages";

    /**
    * List all Attorneys
    *
    */
    public function idx()
    {
        $this->tplname = "sysmessages.list";
        $this->daffny->tpl->emptyText = "No records.";
        $this->applyPager("app_sysmessages", "", "");
        $this->applyOrder("app_sysmessages");


        $sql = "SELECT *
                  FROM app_sysmessages"
                     . $this->order->getOrder()
                     . $this->pager->getLimit();
        $this->getGridData($sql, false);
    }

    /**
    * Edit an business
    *
    */
    public function edit()
    {
        $id = (int)get_var("id");
        $this->tplname = "sysmessages.form";
        $this->title .= ($id > 0 ? " - Edit" : " - Add");

        if (!isset($_POST['submit']) && $id > 0)
        {
            $sql = "SELECT *
                      FROM app_sysmessages
                     WHERE id = $id";
            $this->input = $this->daffny->DB->selectRow($sql);
        }
        else
        {
            $this->input = $this->SaveFormVars();
			$this->input['id'] = $id;
        }

        if (isset($_POST['submit']))
        {
            $sql_arr = $this->daffny->DB->PrepareSql('app_sysmessages', $this->getTplPostValues());
            $this->isEmpty("message", "Message");

            if (!count($this->err))
            {
				if ($id > 0){
                    $this->daffny->DB->update("app_sysmessages", $sql_arr, "id = $id");
                    $this->setFlashInfo("Information has been updated.");
                }else{

                	$sql_arr['added'] = date("Y-m-d H:i:s");
                    $this->daffny->DB->insert("app_sysmessages", $sql_arr);
					$this->setFlashInfo("Information has been added.");
                    $id = $this->daffny->DB->get_insert_id();
                }

                if ($this->dbError()){
                    return;
                }
                redirect(getLink("sysmessages"));
            }
        }

        $this->form->TextField("message", 255, array("style"=>"width:478px;"), $this->requiredTxt."Message", "</td><td>");
    }


    public function delete()
    {
        $ID = $this->checkId();
    	$out = array('success' => false);
    	try {
	        $this->daffny->DB->delete("app_sysmessages", "id = $ID");
	        if ($this->daffny->DB->isError){
						throw new Exception($this->getDBErrorMessage());
			}else{
				$out = array('success' => true);
			}
		} catch (FDException $e) {}
		die(json_encode($out));
    }
}
?>