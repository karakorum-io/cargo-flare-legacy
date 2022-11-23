<?php

/* * *************************************************************************************************
 * Members CP Class                                                                 				   *
 *                                                                              					   *
 *                                                                                                  *
 * Client: 	FreightDragon                                                                          *
 * Version: 	1.0                                                                                    *
 * Date:    	2011-09-29                                                                             *
 * Author:  	C.A.W., Inc. dba INTECHCENTER                                                          *
 * Address: 	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076                             *
 * E-mail:	techsupport@intechcenter.com                                                           *
 * CopyRight 2011 FreightDragon. - All Rights Reserved                                              *
 * ************************************************************************************************** */

require_once(ROOT_PATH . "app/classes/memberapp.php");

class Cpmembers extends Memberapp {

    public $title = "Members";

    public function idx() {
        
        $this->tplname = "members.list";
        $this->daffny->tpl->emptyText = "Members not found.";

        $this->applyPager("members m", "", "WHERE m.chmod = 2 AND m.id = m.parent_id ");
        $this->applyOrder("members");
	    $where = array();
	    if (isset($_GET['username']) && trim($_GET['username']) != '') {
		    $where[] = "m.`username` LIKE '%".mysqli_real_escape_string($this->daffny->DB->connection_id, $_GET['username'])."%'";
		    $this->input['username'] = $_GET['username'];
	    }
	    if (isset($_GET['email']) && trim($_GET['email']) != '') {
		    $where[] = "m.`email` LIKE '%".mysqli_real_escape_string($this->daffny->DB->connection_id, $_GET['email'])."%'";
		    $this->input['email'] = $_GET['email'];
	    }
	    if (isset($_GET['company']) && trim($_GET['company']) != '') {
		    $where[] = "m.`companyname` LIKE '%".mysqli_real_escape_string($this->daffny->DB->connection_id, $_GET['company'])."%'";
		    $this->input['company'] = $_GET['company'];
	    }
	    if (isset($_GET['phone']) && trim($_GET['phone']) != '') {
		    $where[] = "m.`phone` LIKE '%".mysqli_real_escape_string($this->daffny->DB->connection_id, $_GET['phone'])."%'";
		    $this->input['phone'] = $_GET['phone'];
	    }
	    if (isset($_GET['account_type']) && trim($_GET['account_type']) != '') {
		    switch ($_GET['account_type']) {
			    case '1':
				    $where[] = "cp.`is_broker` = 1 AND cp.`is_carrier` = 0";
				    break;
			    case '2':
				    $where[] = "cp.`is_carrier` = 1 AND cp.`is_broker` = 0";
				    break;
			    case '3':
				    $where[] = "cp.`is_broker` = 1 AND cp.`is_carrier` = 1";
				    break;
		    }
		    $this->input['account_type'] = $_GET['account_type'];
	    }
	    $whereStr = "";
	    if (count($where)) {
		    $whereStr = ' AND '.implode(' AND ', $where);
	    }
        $sql = "SELECT m.*
                     , DATE_FORMAT(m.reg_date, '%m/%d/%Y %H:%i:%s') reg_date_show
                  FROM members m
                  JOIN app_company_profile cp ON cp.owner_id = m.parent_id
                 WHERE m.chmod = 2
                     AND m.id = m.parent_id
                     AND m.is_deleted = 0
                "
	            . $whereStr
                . $this->order->getOrder()
                . $this->pager->getLimit();

	    $this->form->TextField('username', 32, array(), "Username", "</td><td>");
	    $this->form->TextField('email', 32, array(), "Email", "</td><td>");
	    $this->form->TextField('company', 32, array(), "Company", "</td><td>");
	    $this->form->ComboBox('account_type', array(
		    null => '',
		    1 => 'Broker',
		    2 => 'Carrier',
		    3 => 'Broker & Carrier',
	    ), array(), "Account Type", "</td><td>");
	    $this->form->TextField('phone', 32, array(), "Phone Number", "</td><td>");

        $this->getGridData($sql, false, true);
    }

    public function edit() {

        $inp = array();
        try {
            $ID = (int) get_var("id");
            $this->tplname = "members.form";
            $this->title .= " - Edit";

            if (isset($_POST["submit"])) {
                //prepare arrays
                $sqlm_arr = array(
                    "username" => post_var("username")
                    , "contactname" => post_var("contactname")
                    , "phone" => post_var("phone")
                    , "email" => post_var("email")
                    , "password" => post_var("password")
                    , "password_confirm" => post_var("password_confirm")
                );

                $sqlc_arr = array(
                    "companyname" => post_var("companyname")
                    , "is_carrier" => (post_var("is_carrier") == "1" ? 1 : 0)
                    , "is_broker" => (post_var("is_broker") == "1" ? 1 : 0)
                    , "is_frozen" => post_var("is_frozen")
                );

                $this->isEmpty("username", "Username");
                $this->isEmpty("contactname", "Name");
                $this->isEmpty("phone", "Phone");
                $this->isEmpty("companyname", "Company Name");
                $this->validateMember($sqlm_arr, $ID);

                if (!count($this->err)) {
	                unset($sqlm_arr["password_confirm"]);
	                if ($sqlm_arr["password"] != "") {
		                $sqlm_arr["password"] = md5($sqlm_arr['password']);
	                } else {
		                unset($sqlm_arr["password"]);
	                }
                    $sql_arr1 = $this->daffny->DB->PrepareSql("members", $sqlm_arr);
                    $sql_arr2 = $this->daffny->DB->PrepareSql("app_company_profile", $sqlc_arr);

                    $this->daffny->DB->transaction("start");

                    $this->daffny->DB->update("members", $sql_arr1, "id = $ID");
                    $this->daffny->DB->update("app_company_profile", $sql_arr2, "owner_id = $ID");

                    if ($this->dbError()) {
                        $this->daffny->DB->transaction("rollback");
                        throw new Exception($this->getDBErrorMessage());
                    } else {
                        $this->daffny->DB->transaction("commit");
                        $this->setFlashInfo("Information has been updated.");
                        redirect(getLink("members"));
                    }
                }
                $inp = $sqlm_arr + $sqlc_arr;
            } else {
                //fill fields
                $sql = "SELECT 
                                    m.*
                                    ,cp.companyname
                                    ,cp.is_carrier
                                    ,cp.is_broker
                                    ,cp.is_frozen
                            FROM members m
                                LEFT JOIN app_company_profile cp ON m.id = cp.owner_id
                            WHERE m.id = $ID";
                $inp = $this->daffny->DB->selectRow($sql);
            }
            
            foreach ($inp as $key=>$value ){
                $this->input[$key] = htmlspecialchars($value);
            }

            $this->input['password'] = "";
            $this->input['password_confirm'] = "";
            //Personal Information    
            $this->form->TextField("contactname", 255, array(), $this->requiredTxt . "Name", "<br />");
            $this->form->TextField("username", 255, array(), $this->requiredTxt . "Username", "<br />");
            $this->form->TextField("phone", 255, array("class" => "phone"), $this->requiredTxt . "Phone", "<br />");
            //Company Information
            $this->form->TextField("companyname", 255, array(), $this->requiredTxt . "Company Name", "<br />");
            $this->form->ComboBox("is_frozen", array("0"=>"No", "1"=>"Yes"), array("style"=>"width:50px;"), $this->requiredTxt . "Frozen", "<br />");
            $this->form->CheckBox("is_broker", array(), "Broker/Dealership", "&nbsp;");
            $this->form->CheckBox("is_carrier", array(), "Carrier", "&nbsp;");
            // Login Information
            $this->form->TextField("email", 255, array(), $this->requiredTxt . "E-mail", "<br />");
            $this->form->PasswordField("password", 15, array(), ($ID > 0 ? "Change password" : $this->requiredTxt . "Password"), "<br />");
            $this->form->PasswordField("password_confirm", 15, array(), ($ID > 0 ? "Confirm password" : $this->requiredTxt . "Confirm password"), "<br />");
        } catch (Exception $e) {
            
        }
    }

    public function delete() {
        $ID = $this->checkId();
        $out = array("success" => false);
        try {
            $this->daffny->DB->update("members", array("is_deleted" => 1, "status" => "Inactive"), "id = $ID");
            if ($this->daffny->DB->isError) {
                throw new Exception($this->getDBErrorMessage());
            } else {
                $out = array("success" => true);
            }
        } catch (Exception $e) {
            
        }
        die(json_encode($out));
    }

    public function status() {
        $out = array("success" => false);
        $id = $this->checkId();
        $this->daffny->DB->transaction("start");
        try {
            $this->daffny->DB->query("UPDATE members SET status = (CASE WHEN status = 'Active' THEN 'Inactive' ELSE 'Active' END) WHERE id = '" . $id . "'");
            $this->daffny->DB->transaction("commit");
            $out = array("success" => true);
        } catch (Exception $e) {
            $this->daffny->DB->transaction("rollback");
            $out = array("success" => false);
        }
        die(json_encode($out));
    }

    /**
     * Login As User and stay as Admin
     * $_SESSION["admin_here"];
     * 
     */
    public function signas() {
        
        $id = (int) get_var("id");
        if ($id == 0) {
            $_SESSION['err_message'] = "Member's ID is invalid.";
            redirect(getLink("members"));
        }

        $member = $this->daffny->DB->selectRow("*", "members", "WHERE id = '" . (int) $id . "'");
        if (empty($member)) {
            $_SESSION['err_message'] = "Customer doesn't exists.";
            redirect(getLink("members"));
        }

        $_SESSION['admin_here'] = true;
        $_SESSION['member'] = $member;
        $_SESSION['member_id'] = $member['id'];
        $_SESSION['parent_id'] = $member['parent_id'];
        $_SESSION['member_chmod'] = $member['chmod'];
        $_SESSION['per_page'] = $member['records_per_page'];
				
				$companyProfile = new CompanyProfile($this->daffny->DB);
				$companyProfile->getByOwnerId($_SESSION['member']['parent_id']);
				$_SESSION['is_broker'] = ($companyProfile->is_broker == "1") ? true : false;
				$_SESSION['is_carrier'] = ($companyProfile->is_carrier == "1") ? true : false;
				$_SESSION['is_frozen'] = ($companyProfile->is_frozen == "1") ? true : false;
                $_SESSION['timezone'] = $companyProfile->timezone;
				
				
        redirect("/application/");
    }
	
	
		
	function applied()
	{
		$this->tplname = "members.appliedlist";
        $this->daffny->tpl->emptyText = "Members not found.";

        $this->applyPager("members_applied", "", "WHERE is_deleted=0 ");
        $this->applyOrder("id");
		
		$this->form->TextField('username', 32, array(), "Username", "</td><td>");
	    $this->form->TextField('email', 32, array(), "Email", "</td><td>");
	    $this->form->TextField('company', 32, array(), "Company", "</td><td>");
	    $this->form->ComboBox('account_type', array(
		    null => '',
		    1 => 'Broker',
		    2 => 'Carrier',
		    3 => 'Broker & Carrier',
	    ), array(), "Account Type", "</td><td>");
	    $this->form->TextField('phone', 32, array(), "Phone Number", "</td><td>");
		
		 $sql = "SELECT 
		              id
		             , contactname
		             , companyname
					 , email
		             , phone
					 , mcnumber
		             , type
					 , message
                     , DATE_FORMAT(create_date, '%m/%d/%Y %H:%i:%s') as create_date
                  FROM members_applied 
                 
                 WHERE is_deleted = 0
                "
	            . $this->order->getOrder()
                . $this->pager->getLimit();
				
				$this->getGridData($sql, false, false);
	}
	
	
	public function deleteapplied() {
        $ID = $this->checkId();
        $out = array("success" => false);
        try {
            $this->daffny->DB->update("members_applied", array("is_deleted" => 1), "id = $ID");
            if ($this->daffny->DB->isError) {
                throw new Exception($this->getDBErrorMessage());
            } else {
                $out = array("success" => true);
            }
        } catch (Exception $e) {
            
        }
        die(json_encode($out));
    }
}

?>