<?php
/*
  +---------------------------------------------------+
  |                                                   |
  |                   Daffny Engine                   |
  |                                                   |
  |                     Auth Lib                      |
  |                                                   |
  |                by Alexey Kondakov                 |
  |             (c)2006 - 2007 Daffny, Inc.           |
  |                                                   |
  |                  www.daffny.com                   |
  |                                                   |
  +---------------------------------------------------+
 */
class auth {
    var $daffny;
    var $type = "login";
    var $login;
    var $email;
    var $password;
    var $saveme = false;
    var $redirect_url;
    var $use_redirect = true;
    var $where = "";
	public $tableName = 'members';
	
    function __construct() {
        @session_start();
        $this->redirect_url = @$_SERVER['REQUEST_URI'];
        if (!isset($_SESSION['member_id'])) {
            $_SESSION['member_id'] = 0;
        }
        if (!isset($_SESSION['member_chmod'])) {
            $_SESSION['member_chmod'] = 0;
        }
    }
    
	function authorise() {
		if ($_SESSION['member_id'] != 0 && $this->password == "") {
            return;
        }
        switch ($this->type) {
            case 'email': {
                    if ($this->email != "" && $this->password != "") {
                        return $this->load_member();
                    } else if (isset($_COOKIE['email']) && isset($_COOKIE['pass_hash'])) {
                        $this->email = $_COOKIE['email'];
                        $this->password = $_COOKIE['pass_hash'];
                        return $this->load_member();
                    }
                    break;
                }
            case 'login': {
                    if ($this->login != "" && $this->password != "") {
                        return $this->load_member();
                    } else if (isset($_COOKIE['login']) && isset($_COOKIE['pass_hash'])) {
                        $this->login = $_COOKIE['login'];
                        $this->password = $_COOKIE['pass_hash'];
                        return $this->load_member();
                    }
                    break;
                }
            default: {
                    trigger_error("Unknown Auth Type: " . $this->type, E_USER_ERROR);
                    break;
                }
        }
    }
    
	function logout($url = "./") {
        $this->unload_member();
        setcookie($this->type, "", time() - 60 * 60 * 24 * 365, "/");
        setcookie("pass_hash", "", time() - 60 * 60 * 24 * 365, "/");
        redirect($url);
    }
    function load_member() {
          $where = ($this->type == "email") ? "email = '" . $this->email . "'" : "login = '" . $this->login . "'";
        if ($this->where != "") {
            $where .= " AND " . $this->where;
        }
        $member = $this->daffny->DB->selectRow("*", $this->tableName, "WHERE is_deleted = 0 AND $where AND password='" . $this->password . "'");
        if (empty($member)) {
            return false;
        }
		$_SESSION = array();
        $_SESSION['member'] = $member;
        $_SESSION['member_id'] = $member['id'];
        $_SESSION['parent_id'] = $member['parent_id'];
        $_SESSION['member_chmod'] = $member['chmod'];
        $_SESSION['per_page'] = $member['records_per_page'];
        //Logout after
        if ($this->tableName != 'administrators') {
            $companyProfile = new CompanyProfile($this->daffny->DB);
            $companyProfile->getByOwnerId($_SESSION['member']['parent_id']);
            $dsettings = $companyProfile->getDefaultSettings();
            if (((int) $dsettings->logout_h * 60 + (int) $dsettings->logout_m) > 0) {
                $_SESSION['logoutmetime'] = mktime((int) date("H") + (int) $dsettings->logout_h, date("i") + (int) $dsettings->logout_m, date("s"), date("m"), date("d"), date("Y"));
            }
        }
        /* Login history */
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $IP = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $IP = $_SERVER['REMOTE_ADDR'];
        }
        $this->daffny->DB->update($this->tableName, array("last_login" => date("Y-m-d H:i:s")), "id='" . $member['id'] . "'");
        $this->daffny->DB->insert("app_members_login_history", array("logintime" => date("Y-m-d H:i:s"), "member_id" => $member['id'], "ip" => $IP));
        if ($this->saveme) {
            if ($this->type == "email") {
                setcookie("email", $this->email, time() + 60 * 60 * 24 * 365, "/");
            } else {
                setcookie("login", $this->login, time() + 60 * 60 * 24 * 365, "/");
            }
            setcookie("pass_hash", $this->password, time() + 60 * 60 * 24 * 365, "/");
        }
        if ($this->use_redirect) {
            redirect($this->redirect_url);
        }
        return $member;
    }
   
    public function unload_member() {
        $_SESSION['member_id'] = 0;
        $_SESSION['member_chmod'] = 0;
        unset($_SESSION['member']);
        unset($_SESSION['per_page']);
        unset($_SESSION['parent_id']);
        unset($_SESSION['admin_here']);
    }
    public function getMemberByEmail($email) {
        $email = stripslashes(trim($email));
        $row = $this->daffny->DB->selectRow("*", "members", "WHERE email = '" . mysqli_real_escape_string($this->daffny->DB->connection_id, $email) . "'");
        if (empty($row)) {
            return false;
        }
        return $row;
    }
    public function getMemberById($memberID = 0) {
        if ($memberID == 0) {
            $memberID = $_SESSION['member_id'];
        }
        $row = $this->daffny->DB->selectRow("*", "members", "WHERE id = $memberID");
        if (empty($row)) {
            return false;
        }
        return $row;
    }
    public function updateMemberSession() {
        $member = $this->daffny->DB->selectRow("*", "members", "WHERE id = " . $_SESSION['member_id']);
        if (empty($member)) {
            return false;
        }
        $_SESSION['member'] = $member;
        return $member;
    }
    public function updateMemberData($sql_arr, $memberID = 0) {
        if ($memberID == 0) {
            $memberID = $_SESSION['member_id'];
        }
        return $this->daffny->DB->update("members", $sql_arr, "id = " . $memberID);
    }
}
?>