<?php

/***************************************************************************************************
* Newsletter Blaster CP Class                                                                 *
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


class CpBlast extends CpAction
{
    public $title = "E-mail Blast";
	public $categories = array();
	/**
    * Index method
    *
    */
	public function idx()
    {
    	$this->input = array();
		if (isset($_POST['submit']) && $this->__send())
		{
			redirect(getLink("blast", "done"));
		}

		$this->tplname = "blast.form";
		$this->input = array(
			'from_name'		=> htmlspecialchars(post_var("from_name")),
			'subject'		=> htmlspecialchars(post_var("subject")),
			'template_id'   => htmlspecialchars(post_var("template_id")),
			'category_id'	=> htmlspecialchars(post_var("category_id"))

		);

		$this->form->TextField("from_name", 255, array(), $this->requiredTxt."From", "</td><td>");
		$this->form->TextField("subject", 255, array(), $this->requiredTxt."Subject", "</td><td>");
		$this->form->ComboBox("category_id", $this->getCategoriesSub("--Select One--"), array(), $this->requiredTxt."Emails", "</td><td>");
		$this->form->ComboBox("template_id", $this->getTemplatesSub("--Select One--"), array(), $this->requiredTxt."Template", "</td><td>");
	}


	public function done(){
		$this->tplname = "blast.done";
	}

	private function __send() {
		$category_id	= (int)post_var("category_id");
		$template_id= (int)post_var("template_id");
		$from_name	= trim(post_var("from_name"));
		$subject	= trim(post_var("subject"));

		$this->isEmpty("category_id", "Emails");
		$this->isEmpty("template_id", "Template");
		$this->isEmpty("from_name", "From");
		$this->isEmpty("subject", "Subject");

		if (!empty($this->err)) {
			return false;
		}

		$this->daffny->DB->query("START TRANSACTION");

		$sql = "INSERT INTO subscribers_letters (email_id, template_id, from_name, subject)
					SELECT e.id
						 , {$template_id}
						 , '".mysqli_real_escape_string($this->daffny->DB->connection_id, $from_name)."'
						 , '".mysqli_real_escape_string($this->daffny->DB->connection_id, $subject)."'
					  FROM subscribers e
					 WHERE category_id = {$category_id}
					   AND unsubscribed <> 1
					   AND (SELECT COUNT(*) FROM subscribers WHERE email = e.email AND unsubscribed = 1) = 0
					 GROUP BY e.email";

		if (!$this->daffny->DB->query($sql)) {
			$this->daffny->DB->query("ROLLBACK");
			$this->err[] = "DB error occured.";
			return false;
		}
		$this->daffny->DB->query("COMMIT");
		return true;
	}
}