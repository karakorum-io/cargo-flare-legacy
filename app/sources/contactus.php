<?php

class Appcontactus extends AppAction
{
    public $title = "";

    public function idx()
    {
        $this->tplname = "contactus";

        if (isset($_POST['submit']))
        {
            $data = $this->getTplPostValues();
            foreach ($data as $k => $v)
            {
                $data[$k] = stripslashes(strip_tags($v));
            }
            $this->isEmpty("companyname", "Company Name");
            $this->isEmpty("contactname", "Name");
            $this->isEmpty("activity", "Activity");
            if (!$this->isEmpty("email", "E-mail"))
            {
                if (!validate_email($data['email'])) {
                    $this->err[] = "Field <strong>E-mail</strong> is incorrect.";
                }
            }

			if (!$this->checkCaptcha())
			{
				$this->err[] = "<strong>Security Code</strong> is incorrect.";
			}

            if (!count($this->err))
            {
				$sql_arr = $this->daffny->DB->PrepareSql("contactus", $data);
				$sql_arr['reg_date'] = 'now()';
				$this->daffny->DB->insert("contactus", $sql_arr);
				$this->collectSubscribers("contactus", $sql_arr);
				$this->sendAdminNotify("contactus", $sql_arr);
				$this->setFlashInfo("Thank You.");
                redirect(getLink("contactus", "sent"));
            }
        }
        $this->input = $this->SaveFormVars();
        $this->form->TextField("companyname", 255, array(), $this->requiredTxt."Company name", "</td><td>");
        $this->form->TextField("activity", 255, array(), $this->requiredTxt."Activity", "</td><td>");
        $this->form->TextField("contactname", 255, array(), $this->requiredTxt."Name", "</td><td>");
		$this->form->TextField("url", 255, array(), "Site URL", "</td><td>");
		$this->form->TextField("email", 255, array(), $this->requiredTxt."E-mail", "</td><td>");
        $this->form->TextField("phone", 255, array('class' => 'width100'), "Phone", "</td><td>");
        $this->input['captcha'] = $this->getCaptcha();
        $row = array();
        $row = $this->daffny->DB->selectRow("title, content", "content", "WHERE name = 'contactus'");
        if (!empty($row)){
			$this->title = $row['title'];
            $this->input['content'] = $row['content'];
        }
    }
}
?>