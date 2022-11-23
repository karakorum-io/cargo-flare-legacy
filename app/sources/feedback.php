<?php
class Appfeedback extends AppAction
{
    public $title = "<h3>Feedback<h3>";
    public $tplname = "feedback";

    public function idx()
    {
        if (isset($_POST['submit']))
        {
            $data = $this->getTplPostValues();
            foreach ($data as $k => $v)
            {
                $data[$k] = stripslashes(strip_tags($v));
            }
            $this->isEmpty("contactname", "Name");
            $this->isEmpty("companyname", "Company name");
            if (!$this->isEmpty("email", "E-mail"))
            {
                if (!validate_email($data['email'])) {
                    $this->err[] = "Field <strong>E-mail</strong> is incorrect.";
                }
            }


			if (!$this->checkCaptcha()){
				$this->err[] = "<strong>Code</strong> is incorrect.";
			}

            if (!count($this->err))
            {
            	$sql_arr = array(
	                'contactname'  => post_var("contactname")
	              , 'companyname'  => post_var("companyname")
	              , 'email'        => post_var("email")
	              , 'phone'        => post_var("phone")
	              , 'comments'     => post_var("comments")
	            );
	            $sql_arr['reg_date'] = "now()";
				$this->daffny->DB->insert("feedback", $sql_arr);
				/* Send notifications */
				$sql_arr['comments'] = nl2br($sql_arr['comments']);

				if ($this->sendEmail('Support', 'support@freightdragon.com', 'Support Services', 'feedback', $sql_arr, "", array())) {
					$this->setFlashInfo("Information has been sent.");
	            } else {
					$this->setFlashError('Failed to send your information. Try again later, please.');
				}
				redirect(getLink("feedback", "sent"));
            }
        }

        $this->input = $this->SaveFormVars();
        $this->form->TextField("contactname", 255, array(), $this->requiredTxt."Your name", "</td><td>");
        $this->form->TextField("companyname", 255, array(), $this->requiredTxt."Company name", "</td><td>");
		$this->form->TextField("email", 255, array(), $this->requiredTxt."E-mail", "</td><td>");
        $this->form->TextField("phone", 15, array('class' => 'width100'), "Phone", "</td><td>");
        $this->form->TextArea("comments", 15, 10, array(), "Comments", "</td><td>");
        $this->input['captcha'] = $this->getCaptcha();
        $row = $this->daffny->DB->selectRow("title, content", "content", "WHERE name = 'feedback'");
        if (!empty($row))
        {
            $this->input['content'] = $row['content'];
            $this->title = $row['title'];
        }
    }
}
?>