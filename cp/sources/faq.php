<?php
/***************************************************************************************************
* Faq CP Class                                                                 *
*                                                                              					   *
*                                                                                                  *
* Client: 	FreightDragon                                                                          *
* Version: 	1.0                                                                                    *
* Date:    	2011-09-28                                                                             *
* Author:  	C.A.W., Inc. dba INTECHCENTER                                                          *
* Address: 	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076                             *
* E-mail:	techsupport@intechcenter.com                                                           *
* CopyRight 2011 FreightDragon. - All Rights Reserved                                              *
****************************************************************************************************/

class Cpfaq extends CpAction
{
    public $title = "F.A.Q.";

    /**
    * List all Attorneys
    *
    */
    public function idx()
    {
        $this->tplname = "faq.list";
        $this->daffny->tpl->emptyText = "No records.";
        $this->applyPager("faq", "", "");
        $this->applyOrder("faq");


        $sql = "SELECT *
                  FROM faq"
                     . $this->order->getOrder()
                     . $this->pager->getLimit();
        if (!$records = $this->getGridData($sql)){
			return;
        }

    }

    /**
    * Edit an business
    *
    */
    public function edit()
    {
        $id = (int)get_var("id");
        $this->tplname = "faq.form";
        $this->title .= ($id > 0 ? " - Edit" : " - Add");

        if (!isset($_POST['submit']) && $id > 0)
        {
            $sql = "SELECT *
                      FROM faq
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
            $sql_arr = $this->daffny->DB->PrepareSql('faq', $this->getTplPostValues());
            $this->isEmpty("question", "Question");
            $this->isEmpty("answer", "Answer");

            if (!count($this->err))
            {
				if ($id > 0){
                    $this->daffny->DB->update("faq", $sql_arr, "id = $id");
                    $this->setFlashInfo("Information has been updated.");
                }else{
                    $this->daffny->DB->insert("faq", $sql_arr);
					$this->setFlashInfo("Information has been added.");
                    $id = $this->daffny->DB->get_insert_id();
                }

                if ($this->dbError()){
                    return;
                }
                redirect(getLink("faq"));
            }
        }

        $this->form->TextField("question", 200, array("style"=>"width:478px;"), $this->requiredTxt."Question", "</td><td>");
        $this->form->Editor('answer', 500, 200);
    }


    public function delete(){
        $ID = $this->checkId();
    	$out = array('success' => false);
    	try {
	        $this->daffny->DB->delete("faq", "id = $ID");
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