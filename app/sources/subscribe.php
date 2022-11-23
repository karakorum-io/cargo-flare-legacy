<?php

class AppSubscribe extends AppAction
{
    public $title = "Subscribe";

    public function idx()
    {
        $this->tplname = "subscribe";

        if (isset($_POST['subscribe_email']))
        {
			$_SESSION['subscribe_email'] = $_POST['subscribe_email'];
			redirect(getLink("subscribe"));
        }


        if (isset($_POST['submit']))
        {
            $data = $this->getTplPostValues();
            foreach ($data as $k => $v)
            {
                $data[$k] = stripslashes(strip_tags($v));
            }

            $this->isEmpty("first_name", "First Name");
            $this->isEmpty("last_name", "Last Name");
            if (!$this->isEmpty("email", "E-mail Address"))
            {
                if (!validate_email($data['email'])) {
                    $this->err[] = "Field <strong>E-mail Address</strong> is invalid.";
                }
            }

            $row = $this->daffny->DB->select_one("id", "subscribers", "WHERE email = '".mysqli_real_escape_string($this->daffny->DB->connection_id, $data['email'])."'");
	        if (!empty($row)) {
	            $this->err[] = "<strong>E-mail Address</strong> already exists.";
	        }


			if (!$this->checkCaptcha()){
				$this->err[] = "<strong>Security code</strong> is invalid.";
			}


            if (!count($this->err))
            {
            	$sql_arr = array(
	                'first_name' => post_var("first_name")
	              , 'last_name'  => post_var("last_name")
	              , 'address'    => post_var("address")
	              , 'city'       => post_var("city")
	              , 'state'      => ((post_var("country") == "US")?post_var("state"):post_var("state2"))
	              , 'zip'        => post_var("zip")
	              , 'country'    => post_var("country")
	              , 'email'      => post_var("email")
	              , 'phone'      => post_var("phone")
	            );
	            $sql_arr['reg_date'] = "now()";

				$this->collectSubscribers("subscribers", $sql_arr);


                $subject = $this->daffny->cfg['site_title']." Subscribe";
                $headers = array(
                    'From' => $this->daffny->cfg['info_email']
                );

                $states = $this->getStates();
                $countries = $this->getCountries();
				$this->daffny->tpl->$data;
				$data['state'] = (isset($states[$sql_arr['state']]) ? $states[$sql_arr['state']] : $data['state2']);
				$data['country'] = (isset($countries[$data['country']]) ? $countries[$data['country']] : $data['country']);


				$data['address_show'] = ($data['address'] != "" ? $this->printTr('Address:', $data['address']) : "");
				$data['city_state_zip_show'] = ($data['city'] != "" || $data['zip'] != "" || $data['state'] != "" ? $this->printTr(($data['address_show'] == "" ? 'Address:' : ''), $data['city']." ".$data['state'].($data['zip'] != "" ? ", ".$data['zip'] : "")) : "");
				$data['country_show'] = ($data['country'] != "" ? $this->printTr(($data['city_state_zip_show'] == "" && $data['address_show'] == "" ? 'Address:' : ''), $data['country']) : "");
				$data['phone_show'] = ($data['phone'] != "" ? $this->printTr('Phone:', $data['phone']) : "");

				$data['letter_title'] = '';

				$this->sendEmail($data['first_name']." ".$data['last_name'], $data['email'], $subject, "subscribe", $data, "", $headers, true);
				$this->setFlashInfo("Thank you for joining our mailing list.");
				redirect(getLink("subscribe", "sent"));
            }
        }
        $this->input = $this->SaveFormVars();
        if (trim($this->input['country']) == ""){
        	$this->input['country'] = "US";
        }

        if (isset($_SESSION['subscribe_email']))
        {
			$this->input['email'] = htmlspecialchars($_SESSION['subscribe_email']);
			unset($_SESSION['subscribe_email']);
        }

        $this->form->TextField("first_name", 50, array(), $this->requiredTxt."First Name", "</td><td>");
        $this->form->TextField("last_name", 50, array(), $this->requiredTxt."Last Name", "</td><td>");
		$this->form->TextField("address", 255, array(), "Street Address", "</td><td>");
		$this->form->TextField("city", 100, array(), "City", "</td><td>");
		$this->form->ComboBox("state", $this->getStates(), array(), "State", "</td><td>");
		$this->form->TextField("state2", 100, array(), "State", "</td><td>");
        $this->form->TextField("zip", 15, array('class' => 'width100'), "Postal/Zip Code", "</td><td>");
		$this->form->ComboBox("country", $this->getCountries(), array('class' => 'width100', "onChange"=>"checkCountry();"), "Country", "</td><td>");
		$this->form->TextField("email", 255, array(), $this->requiredTxt."E-mail Address", "</td><td>");
        $this->form->TextField("phone", 15, array('class' => 'width100'), "Phone", "</td><td>");

        $this->input['captcha'] = $this->getCaptcha();
        $row = array();
        $row = $this->daffny->DB->selectRow("title, content", "content", "WHERE name = 'subscribe'");
        if (!empty($row)){
            $this->title = $row['title'];
            $this->input['content'] = $row['content'];
        }
    }
	function printTr($title, $data){
		return '<tr><td><strong>'.$title.'</strong></td><td>'.$data.'</td></tr>';
	}
}

?>