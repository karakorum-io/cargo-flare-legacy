<?php

require_once CLASS_PATH . "memberapp.php";

class AppUser extends Memberapp
{

    public $title;

    public function __construct()
    {
        parent::__construct();
    }

    public function idx()
    {
        $this->title = "My Profile";
        $this->tplname = "user.home";
        if (isGuest()) {
            redirect(getLink("user", "signin"));
        } else {
            redirect(getLink("user", "profile"));
        }
    }

    public function confirm()
    {
        $this->title = "Account Confirmation";
        $this->tplname = "user.confirm";
        $this->setFlashInfo('Confirmation complete');
        redirect(getLink('user', 'signin'));
    }

    /**
     * Login form and validation
     *
     */
    public function signin()
    {
        $this->title = "<h3>Login<h3>";
        $this->tplname = "user.login";

        if (isset($_POST['submit']) || isset($_POST['submit_x'])) {
            $email = trim(post_var("email"));
            $password = post_var("password");
            $this->isEmpty("email", "E-mail");
            $this->checkEmail($email, "E-mail");
            $this->isEmpty("password", "Password");

            //get member ID
            $member_id = $this->daffny->DB->selectValue("id", "members", "WHERE is_deleted = 0 AND email = '" . mysqli_real_escape_string($this->daffny->DB->connection_id, $email) . "'");
            //check login restrictions
            if (!count($this->err) && (int) $member_id > 0) {
                $m = new Member($this->daffny->DB);
                $m->load($member_id);
                if (!$m->checkLoginRestrictions()) {
                    $this->err[] = "Sorry. User login times have been limited.";
                }
            }

            if (!count($this->err)) {
                if (isset($_POST['saveme'])) {
                    $this->daffny->auth->saveme = true;

                }

                if ($member = $this->authorize($email, $password)) {

                    $companyProfile = new CompanyProfile($this->daffny->DB);
                    $companyProfile->getByOwnerId($_SESSION['member']['parent_id']);
                    $_SESSION['is_broker'] = ($companyProfile->is_broker == "1") ? true : false;
                    $_SESSION['is_carrier'] = ($companyProfile->is_carrier == "1") ? true : false;
                    $_SESSION['is_frozen'] = ($companyProfile->is_frozen == "1") ? true : false;
                    $_SESSION['timezone'] = $companyProfile->timezone;

                    //Update DB status for on-line user
                    $date = date("Y-m-d H:i:s");
                    $currentDate = strtotime($date);
                    $futureDate = $currentDate + (60 * 5);
                    $online_heartbeat = date("Y-m-d H:i:s", $futureDate);
                    $updateId = $member['id'];
                    $postdata = array('online_status' => '1', 'online_heartbeat' => $online_heartbeat);
                    $done = $this->daffny->DB->update("members", $postdata, "id = '" . $updateId . "'");

                    if ($done) {
                        if ($member['chmod'] == 1) { //admin login
                            $_SESSION["admin_here"] = true;
                            redirect(getLink("cp"));
                        } else {
                            /*echo getLink("application");
                            die;*/
                            redirect(getLink("application"));

                        }
                    }
                }
            }
        }

        $this->input = $this->SaveFormVars();
        $this->form->TextField("email", 100, array(), $this->requiredTxt . "E-mail", "<br />");
        $this->form->PasswordField("password", 25, array(), $this->requiredTxt . "Password", "<br />");
        $this->form->CheckBox("saveme", array(), '<label class="font-size-h6 font-weight-bolder text-dark pt-5">&nbsp;&nbsp;Remember me</label>', "");
    }

    /**
     * put your comment there...
     *
     */
    public function profile()
    {
        $this->checkaccess();
        $this->title = "Personal information";
        $this->tplname = "user.profile";
        $id = getMemberId();

        if (isset($_POST['submit'])) {
            $this->isEmpty("contactname", "Name");
            $this->isEmpty("username", "UserName");
            $this->isEmpty("phone", "Phone");
            $this->isEmpty("email", "E-mail");

            $sql_arr = array(
                "contactname" => post_var('contactname')
                , "username" => post_var('username')
                , "phone" => post_var('phone')
                , "email" => post_var('email')
                , "password" => post_var('password')
                , "password_confirm" => post_var('password_confirm'),
            );

            $this->validateMember($sql_arr, $id);
            if (!count($this->err)) {
                $upd_arr = $this->daffny->DB->PrepareSql("members", $sql_arr);
                unset($upd_arr['`password_confirm`']);
                if (isset($upd_arr['`password`'])) {
                    $upd_arr['`password`'] = md5($upd_arr['`password`']);
                } else {
                    unset($upd_arr['`password`']);
                }

                $this->daffny->DB->update("members", $upd_arr, "id = '" . $id . "'");
                $this->daffny->auth->updateMemberSession();
                if ($this->dbError()) {
                    return;
                } else {
                    $this->setFlashInfo("Information has been updated.");
                    redirect(getLink("user", "profile"));
                }
            } else {
                $inp = $sql_arr;
            }
        } else {
            $inp = $this->daffny->DB->selectRow("*", "members", "WHERE id='" . $id . "'");
        }

        $this->input = array(
            "contactname" => htmlspecialchars($inp['contactname'])
            , "username" => htmlspecialchars($inp['username'])
            , "phone" => htmlspecialchars($inp['phone'])
            , "email" => htmlspecialchars($inp['email']),
        );

        $this->form->TextField("contactname", 255, array(), $this->requiredTxt . "Name", "</td><td>");
        $this->form->TextField("username", 255, array(), $this->requiredTxt . "Username", "</td><td>");
        $this->form->TextField("phone", 255, array(), $this->requiredTxt . "Phone", "</td><td>");
        $this->form->TextField("email", 255, array(), $this->requiredTxt . "E-mail", "</td><td>");
        $this->form->PasswordField("password", 15, array(), "New Password", "</td><td>");
        $this->form->PasswordField("password_confirm", 15, array(), "Confirm password", "</td><td>");
    }

    /**
     * Logout from system
     *
     * @param mixed $url
     */
    public function signout($url = "")
    {
        //Update DB status for off-line user
        $date = date("Y-m-d H:i:s");
        $updateId = $_SESSION['member_id'];
        $postdata = array('online_status' => '0', 'online_heartbeat' => $date);
        $done = $this->daffny->DB->update("members", $postdata, "id = '" . $updateId . "'");

        $this->daffny->auth->logout(SITE_IN . $url);
    }

    /**
     * put your comment there...
     *
     */
    public function forgot_password()
    {
        $this->title = "Password recovery";
        if (!isGuest()) {
            redirect(getLink());
        }

        if (isset($_GET['sent'])) {
            $this->forgot_password_sent($_GET['sent']);
        } else if (isset($_GET['process'])) {
            $this->forgot_password_process();
        } else {
            $this->forgot_password_();
        }
    }

    /**
     * put your comment there...
     *
     */
    public function forgot_password_()
    {
        $this->tplname = "user.forgot_password";
        $this->title = "<h3>Password recovery</h3>";
        $this->input = $this->SaveFormVars();
        $this->form->TextField("email", 100, array(), $this->requiredTxt . "E-mail", "</td><td>");

        if (!isset($_POST['submit'])) {
            return;
        }

        $this->isEmpty("email", "E-mail");
        $this->checkEmail($_POST['email'], "E-mail");

        if (count($this->err)) {
            return;
        }

        if (!$member = $this->daffny->auth->getMemberByEmail(post_var("email"))) {
            $this->err[] = "E-mail not found.";

            return;
        }

        if ($member['chmod'] == 2) {
            $toName = $member['username'];
        }

        $now = time();
        $now_hash = md5($now);
        $p = substr($now_hash, 0, 16) . $member['id'] . substr($now_hash, -16);

        $this->daffny->auth->updateMemberData(array('pwd_recovery_date' => $now), $member['id']);

        $member['url_reset'] = substr(BASE_PATH, 0, -1) . getLink("user", "forgot-password", "process", $p);
        $this->sendForgotPasswordEmail($toName, $member['email'], "Change password", $member, "forgot_password");

        redirect(getLink("user", "forgot-password", "sent"));
    }

    /**
     * put your comment there...
     *
     * @param mixed $sent_what
     */
    public function forgot_password_sent($sent_what = "")
    {
        $this->tplname = "user.forgot_password_text";

        if ($sent_what == "pwd") {
            $this->input['text'] = "Your password has now been reset and emailed to you.<br /><br />Please check your email to find your new password.";
        } else {
            $this->input['text'] = "Details about how to reset your password have been sent to you by email.";
        }
    }

    /**
     * put your comment there...
     *
     */
    public function forgot_password_process()
    {
        $this->tplname = "user.forgot_password_text";
        $this->input['text'] = "Link has been expired.";

        if (!isset($_GET['process']) || trim($_GET['process']) == "") {
            redirect(getLink());
        }
        $hash = trim($_GET['process']);

        $member_id = substr($hash, 16, -16);
        $time_hash = substr($hash, 0, 16) . substr($hash, -16);

        if (!preg_match("/^[0-9]+$/", $member_id)) {
            redirect(getLink());
        }

        $member = $this->daffny->auth->getMemberById($member_id);

        if (!$member) {
            return;
        }

        if ($time_hash != md5($member['pwd_recovery_date']) && $member['pwd_recovery_date'] + 60 * 60 * 24 < time()) {
            return;
        }

        $toName = "";
        if ($member['chmod'] == 2) {
            $toName = $member['username'];
        }

        $new_pass = randomkeys(8, 2);
        $this->daffny->auth->updateMemberData(array('password' => md5($new_pass), 'pwd_recovery_date' => 0), $member_id);

        $member['pass'] = $new_pass;
        $this->sendForgotPasswordEmail($toName, $member['email'], "New Password", $member, "forgot_password2");

        redirect(getLink("user", "forgot-password", "sent", "pwd"));
    }

    public function orderstatus()
    {
        $this->checkaccess();
        $id = $this->checkId();
        $this->daffny->DB->query("UPDATE orders SET status = (CASE WHEN status = 'Active' THEN 'Inactive' ELSE 'Active' END) WHERE id = $id AND member_id='" . getMemberId() . "'");
        exit();
    }

}
