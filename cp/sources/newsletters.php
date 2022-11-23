<?php

/***************************************************************************************************
* Newsletters templates CP Class                                                                 *
*                                                                              					   *
*                                                                                                  *
* Client: 	FreightDragon                                                                          *
* Version: 	1.0                                                                                    *
* Date:    	2011-10-03                                                                             *
* Author:  	C.A.W., Inc. dba INTECHCENTER                                                          *
* Address: 	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076                             *
* E-mail:	techsupport@intechcenter.com                                                           *
* CopyRight 2011 FreightDragon. - All Rights Reserved                                              *
****************************************************************************************************/


class CpNewsletters extends CpAction
{
    /**
    * Index method
    *
    */
    public function idx()
    {
        $this->title = "Newsletters Management";
        $this->tplname = "newsletters.list";
		$records = $this->getList(false);
		if (is_array($records)) {
			foreach ($records as $i => $record) {
				$record['content'] = trim(strip_tags($record['content']));

				if ($record['content'] == "") {
					continue;
				}
				if (trim($record['title']) == "") {
					$records[$i]['title'] = "...";
				}

				preg_match("/([\S]+\s*){0,50}/", $record['content'], $regs);
				$records[$i]['content'] = trim($regs[0]);

				if ($record['content'] != trim($regs[0])) {
					$records[$i]['content'] .= "...";
				}
			}
		}
        $this->daffny->tpl->data = $records;
    }

    /**
    * Edit or add new newsletter
    *
    */
    public function edit()
    {
        $id = (int)get_var("id");

        $this->title = ($id > 0) ? "Edit Newsletter" : "Create a Newsletter";
        $this->tplname = "newsletters.form";
        $this->input = $this->SaveFormVars();

        $this->getList(false);

        if (isset($_POST['submit']))
        {
            $sql_arr = array(
                'title'    => post_var("title")
              , 'content'  => post_var("content")
            );

            if (!count($this->err))
            {
                if ($id > 0)
                {
                    $this->daffny->DB->update("subscribers_newsletters", $sql_arr, "id = '".$id."'");
                    $_SESSION['inf_message'] = "Newsletter <strong>'".htmlspecialchars($sql_arr['title'])."'</strong> has been updated.";
                }
                else
                {
                    $sql_arr['Created'] = "now()";
                    $this->daffny->DB->insert("subscribers_newsletters", $sql_arr);
                    $_SESSION['inf_message'] = "Newsletter <strong>'".htmlspecialchars($sql_arr['title'])."'</strong> has been created.";
                    $id = $this->daffny->DB->get_insert_id();
                }
                redirect(getLink("newsletters"));
            }
        }
        else
        {
            if ($id > 0)
            {
                $row = $this->daffny->DB->selectRow("title, content", "subscribers_newsletters", "WHERE id = {$id}");
                $this->input['content'] = $row['content'];
                $this->input['title'] = htmlspecialchars($row['title']);
            }
            else
            {
                $this->input['content'] = $this->daffny->tpl->build("newsletters.template");
            }
        }

        $this->form->TextField("title", 255, array('style' => "width: 400px;"), "Title", "</td><td>");
		$this->form->Editor('content', 820, 400);
    }

    /**
    * Delete newsletter
    *
    */
    public function delete()
    {
        $ID = $this->checkId();
    	$out = array('success' => false);
    	try {
	        $this->daffny->DB->delete("subscribers_newsletters", "id = $ID");
	        if ($this->daffny->DB->isError){
						throw new Exception($this->getDBErrorMessage());
			}else{
				$out = array('success' => true);
			}
		} catch (FDException $e) {}
		die(json_encode($out));
    }

    /**
    * Get a list on newsletters
    *
    */
    protected function getList($useNoRecords = true)
    {
        $this->applyPager("subscribers_newsletters");
        $order = $this->applyOrder("subscribers_newsletters");
        $order->setDefault("Created", "desc");
        $sql = "SELECT *
        			, DATE_FORMAT(Created, '%m/%d/%Y') AS Created
                  FROM subscribers_newsletters"
                     . $this->order->getOrder()
                     . $this->pager->getLimit();

        return $this->getGridData($sql, $useNoRecords);
    }
}

?>