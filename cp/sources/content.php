<?php

/***************************************************************************************************
* Content Management CP Class                                                                 *
*                                                                              					   *
*                                                                                                  *
* Client: 	FreightDragon                                                                          *
* Version: 	1.0                                                                                    *
* Date:    	2011-09-29                                                                             *
* Author:  	C.A.W., Inc. dba INTECHCENTER                                                          *
* Address: 	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076                             *
* E-mail:	techsupport@intechcenter.com                                                           *
* CopyRight 2011 FreightDragon. - All Rights Reserved                                              *
****************************************************************************************************/

class CpContent extends CpAction
{
    public $title = "Content management";
    public $tplname = "content";

    /**
    * put your comment there...
    *
    */
    public function idx()
    {
        $this->edit();
    }

    public function edit()
    {
        $id = (int)get_var("id");

        $this->applyPager("content");

        $records = $this->getGridData("SELECT * FROM content ORDER BY title".$this->pager->getLimit());
        foreach ($records as $i => $record)
        {
            $record['content'] = trim(strip_tags($record['content']));

            if ($record['content'] == "") {
                continue;
            }

			$records[$i]['content'] = cutContent($record['content'], 20);
        }
        $this->daffny->tpl->data = $records;

        $this->daffny->tpl->show_form = true;

        if ($id <= 0)
        {
            $this->daffny->tpl->show_form = false;
            return;
        }

        if (isset($_POST['submit']))
        {
            $upd_arr = array(
                'title'   => post_var("title")
              , 'content' => post_var("content")
            );

            $this->daffny->DB->update("content", $upd_arr, "id = $id");

            $_SESSION['inf_message'] = "Information has been updated.";
            redirect(getLink("content"));
        }

        if (!isset($_POST['submit']))
        {
            $row = $this->daffny->DB->select_one("title, content", "content", "WHERE id = $id");
            $this->input['content'] = $row['content'];
            $this->input['title'] = $row['title'];
        }

		$this->form->TextField("title", 255, array("style"=>"width:490px;"), "Title", "</td><td>");
		$this->form->Editor('content', 900, 400);
    }
}

?>