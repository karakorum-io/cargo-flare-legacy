<?php

/***************************************************************************************************
* Newsletters subscribers CP Class                                                                 *
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

class CpSubscribers extends CpAction
{
    public $title = "Emails Management";

    /**
    * List all subscribers
    *
    */
    public function idx()
    {
        $this->title = "Emails Management";
        $this->tplname = "subscribers.list";
        $this->daffny->tpl->emptyText = "No records.";

        if (isset($_POST['category_id'])){
            $_SESSION['category_id'] = $_POST['category_id'];
            redirect(getLink("subscribers"));
        }
        if (!isset($_SESSION["category_id"])){
        	$_SESSION["category_id"] = "";
        }
        $where = "";
        if( isset($_SESSION['category_id']) && $_SESSION['category_id'] != "" )
		{
			$where = " WHERE f.category_id = '".$_SESSION['category_id']."' ";
		}

        $this->applyPager("subscribers f","", $where);
        $order = $this->applyOrder("subscribers");
        $order->setDefault("reg_date", "desc");
        $sql = "SELECT f.*
        			 , DATE_FORMAT(reg_date, '%m/%d/%Y') AS reg_date
        			 , CONCAT_WS(' ', f.first_name, f.last_name) AS sname
                     , c.name AS catname
                     , (CASE WHEN f.unsubscribed = '1' THEN 'Yes' ELSE 'No' END) AS unsubscribed
                  FROM subscribers f
                       LEFT JOIN subscribers_categories c ON f.category_id = c.id"
                     . $where
                     . $this->order->getOrder()
                     . $this->pager->getLimit();
        $records = $this->getGridData($sql, false);
        $this->daffny->tpl->data = $records;
        $this->input['category_id'] = $_SESSION["category_id"];
		$this->form->ComboBox("category_id", $this->getCategoriesSub("All"), array(), $this->requiredTxt."Category", "&nbsp;&nbsp;");
    }

    /**
    * Edit or add new subscriber
    *
    */
    public function edit()
    {
        $ID = (int)get_var("id");

        $this->title = ($ID > 0 ? "Edit" : "Create")." Email";
        $this->tplname = "subscribers.form";

        if (!isset($_POST['submit']) && $ID > 0)
        {
            $inputrow = $this->daffny->DB->selectRow("*", "subscribers", "WHERE id = '".$ID."'");

            foreach($inputrow as $key=>$value)
            {
            	$this->input[$key] = htmlspecialchars($value);
            }
        }
        else
        {
            $this->input = $this->SaveFormVars();
        }

        if (isset($_POST['submit']))
        {
            $sql_arr = $this->getTplPostValues();

            $this->isEmpty("category_id", "Category");
			/*
            $this->isEmpty("first_name", "First Name");
            $this->isEmpty("last_name", "Last Name");
            */
            if (!$this->isEmpty("email", "E-mail Address"))
            {
                if (!validate_email($_POST['email'])) {
                    $this->err[] = "Field <strong>E-mail Address</strong> is invalid.";
                }
            }

            $row = $this->daffny->DB->select_one("id", "subscribers", "WHERE email = '".mysqli_real_escape_string($this->daffny->DB->connection_id, $_POST['email'])."' AND id <> '".$ID."'");
	        if (!empty($row)) {
	            $this->err[] = "<strong>E-mail Address</strong> already exists.";
	        }

            if (!count($this->err))
            {
                $sql_arr = $this->daffny->DB->prepareSql("subscribers", $sql_arr);
                $sql_arr['unsubscribed'] = post_var("unsubscribed") == "1"?1:0;

                if ($ID > 0)
                {
                    $this->daffny->DB->update("subscribers", $sql_arr, "id = $ID");
                    $_SESSION['inf_message'] = "Subscriber has been updated.";
                }
                else
                {
                	$sql_arr['reg_date'] = date("Y-m-d");
                    $this->daffny->DB->insert("subscribers", $sql_arr);
                    $_SESSION['inf_message'] = "Subscriber has been created.";
                }

                redirect(getLink("subscribers"));
            }
        }

        $this->form->ComboBox("category_id", $this->getCategoriesSub(), array(), $this->requiredTxt."Category", "<br />");
        $this->form->TextField("first_name", 50, array(), "First Name", "<br />");
        $this->form->TextField("last_name", 50, array(), "Last Name", "<br />");
		$this->form->TextField("address", 255, array(), "Street Address", "<br />");
		$this->form->TextField("city", 100, array(), "City", "<br />");
		$this->form->ComboBox("state", $this->getStates(true), array(), "State", "<br />");
        $this->form->TextField("zip", 15, array('class' => 'width100'), "Postal/Zip Code", "<br />");
        if (trim($this->input['country']) == ""){
        	$this->input['country'] = "US";
        }
		$this->form->ComboBox("country", $this->getCountries(), array('class' => 'width100'), "Country", "<br />");
		$this->form->TextField("email", 255, array(), $this->requiredTxt."E-mail Address", "<br />");
        $this->form->TextField("phone", 15, array('class' => 'width100'), "Phone", "<br />");
        $this->form->CheckBox("unsubscribed", array(), "Unsubscribed", "&nbsp;");
    }

    /**
    * Delete subscriber
    *
    */
    public function delete()
    {
        $ID = $this->checkId();
    	$out = array('success' => false);
    	try {
	        $this->daffny->DB->delete("subscribers", "id = $ID");
	        if ($this->daffny->DB->isError){
						throw new Exception($this->getDBErrorMessage());
			}else{
				$out = array('success' => true);
			}
		} catch (FDException $e) {}
		die(json_encode($out));
    }

	public function export_excel() {
		$this->_exportOne("subscribers");
	}
    /**
    * Get list of categories for subscribers form
    *
    */
    public function export()
    {
    	$where = "";
        if( isset($_SESSION['category_id']) && $_SESSION['category_id'] != "" )
		{
			$where = " WHERE f.category_id = '".$_SESSION['category_id']."' ";
		}
        $this->applyPager("subscribers", $where);
        $order = $this->applyOrder("subscribers");
        $order->setDefault("reg_date", "desc");
        $sql = "SELECT f.*
        			 , DATE_FORMAT(reg_date, '%m/%d/%Y') AS reg_date
        			 , CONCAT_WS(' ', f.first_name, f.last_name) AS sname
                     , c.name AS catname
                     , (CASE WHEN f.unsubscribed = '1' THEN 'Yes' ELSE 'No' END) AS unsubscribed
                  FROM subscribers f
                       LEFT JOIN subscribers_categories c ON f.category_id = c.id"
                     . $where
                     . $this->order->getOrder()
                     . $this->pager->getLimit();
        $records = $this->getGridData($sql);


        $data = "";
        $this->daffny->DB->query($sql);
        foreach ($records as $key => $row){
			$data .= $row['first_name']."\t";
			$data .= $row['last_name']."\t";
			$data .= $row['email']."\t";
			$data .= $row['address']."\t";
			$data .= $row['city']."\t";
			$data .= $row['state']."\t";
			$data .= $row['zip']."\t";
			$data .= $row['phone']."\t";
			$data .= $row['country']."\t";
			$data .= $row['reg_date']."\t";
			$data .= $row['unsubscribed']."\r\n";
        }
        $data = substr($data, 0, -2);
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Length: ".strlen($data));
        header("Content-type: application/txt");
        header("Content-Disposition: attachment; filename=export_emails".date("YmdHis").".txt");
        echo $data;
        exit();
    }

    public function import()
    {
        require_once(DAFFNY_PATH."libs/upload.php");
        $upload = new upload();
        $upload->out_file_dir = ROOT_PATH."uploads/emails";
        $upload->max_file_size = "10000000";
        $upload->allowed_file_ext = array("csv", "txt");
        $upload->form_field = "fupload";

        $category_id = (int)post_var("category_id");

        if (isset($_POST['submit']))
        {

            $this->isEmpty("category_id", "Category");

            if (!count($this->err))
            {
                $upload->upload_process();

                if ($upload->error_no)
                {
                    switch($upload->error_no)
                    {
                        // No upload
                        case 1:
                            $this->err[] = "Select file to upload.";
                        break;

                        // Invalid file ext
                        case 2:
                        case 5:
                            $this->err[] = "Invalid File Extension.";
                        break;

                        // Too big...
                        case 3:
                            $this->err[] = "File too big.";
                        break;

                        // Cannot move uploaded file
                        case 4:
                            $this->err[] = "Could not move uploaded file, upload deleted";
                        break;
                    }
                }

                if (count($this->err) == 0)
                {
                    // Read File
                    $handle = fopen($upload->saved_upload_name, "rb");
                    $emails = fread($handle, filesize($upload->saved_upload_name));
                    fclose($handle);

                    $emails = str_replace(array("\r\n", "\r", "\n"), array(" ", " ", " "), $emails);
                    preg_match_all("/([-a-zA-Z0-9._]+@[-a-zA-Z0-9.]+(\.[-a-zA-Z0-9]+)+)/", $emails, $res);
                    if (!isset($res[1]) || count($res[1]) == 0) {
                        $this->err[] = "The correct E-mail addresses did not match.";
                    }

                    if (count($this->err) == 0)
                    {
                        $emails = $res[1];
                        foreach ($emails as $email)
                        {
                            if ($email == "" || !validate_email($email)) continue;
                            $email = strtolower($email);
                            $email = mysqli_real_escape_string($this->daffny->DB->connection_id, $email);

                            $this->daffny->DB->insert("subscribers", array('category_id'=>$category_id, 'email' => $email, "reg_date"=> date("Y-m-d H:i:s")));
                        }

                        $_SESSION['inf_message'] = "E-mails (".count($emails).") has been uploaded.";
                        redirect(getLink("subscribers", "import"));
                    }
                }
            }
        }

        $this->tplname = "subscribers.form_upload";
        $this->title .= " - Upload Emails";

        $this->input['category_id'] = $_SESSION["category_id"];
		$this->form->ComboBox("category_id", $this->getCategoriesSub(), array(), "Category", "</td><td>");
		$this->form->FileFiled("fupload", array(), "Select File (*.txt, csv)", "</td><td>");
    }
}

?>