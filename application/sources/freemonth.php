<?php

class ApplicationFreemonth extends ApplicationAction
{

    public $title = "Refer a friend";
    public $section = "Refer a friend";
    public $tplname = "myaccount.freemonth.freemonth";

    public function construct()
    {
        if (!$this->check_access('preferences')) {
            $this->setFlashError('Access Denied.');
            redirect(getLink());
        }
        return parent::construct();
    }

    public function idx()
    {
        try {
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("companyprofile") => "My Account", '' => "Refer a friend"));
            $this->check_access("settings");
            $profile = new CompanyProfile($this->daffny->DB);
            $profile->getByOwnerId(getParentId());

            $sql_arr = array(
                "is_terms" => (post_var("is_terms") == "1" ? 1 : 0)
                , "friend_name" => post_var("friend_name")
                , "friend_email" => post_var("friend_email")
                , "your_name" => post_var("your_name")
                , "personal_message" => post_var("personal_message"),
            );

            if (isset($_POST['submit'])) {
                $this->isEmpty("friend_name", "Friend's Name");
                $this->isEmpty("friend_email", "Friend's Email");
                $this->checkEmail("friend_email", "Friend's Email");
                $this->isEmpty("your_name", "Your Name");
                $this->isEmpty("personal_message", "Personal Message");
                $this->isZero("is_terms", "Terms (must be checked to send)");

                if (!count($this->err)) {
                    $link = str_replace("application/", "", SITE_PATH) . "registration/id/" . $profile->ref_code;
                    $eml_arr = array(
                        "friend_name" => htmlspecialchars($sql_arr["friend_name"])
                        , "friend_email" => htmlspecialchars($sql_arr["friend_email"])
                        , "your_name" => htmlspecialchars($sql_arr["your_name"])
                        , "personal_message" => htmlspecialchars($sql_arr["personal_message"])
                        , "companyname" => htmlspecialchars($profile->companyname)
                        , "link" => "<a href=\"" . $link . "\">" . $link . "</a>",
                    );
                    $snd = $this->sendEmail($eml_arr["friend_name"], $eml_arr["friend_email"], $sql_arr["your_name"] . " has sent you a FreightDragon Referral", "freemonth", $eml_arr);
                    $this->daffny->tpl->path = ROOT_PATH . "application/templates/";
                    if ($snd === true) {
                        $this->setFlashInfo("The message has been sent.");
                        redirect(getLink("freemonth", "sent"));
                    } else {
                        $this->setFlashError("SMTP ERROR: " . $snd);
                    }
                }
            }

            foreach ($sql_arr as $key => $value) {
                $this->input[$key] = htmlspecialchars($value);
            }

            $this->form->TextField("friend_name", 255, array(), $this->requiredTxt . "Friend's Name", "</td><td>");
            $this->form->TextField("friend_email", 255, array(), $this->requiredTxt . "Friend's Email", "</td><td>");
            $this->form->TextField("your_name", 255, array(), $this->requiredTxt . "Your Name", "</td><td>");
            $this->form->TextArea("personal_message", 15, 10, array("style" => "height:100px; width:300px;"), $this->requiredTxt . "Personal Message<br /><em>(up to 500 characters)</em>", "</td><td>");
            $this->form->CheckBox("is_terms", array(), "<strong style=\"color:red\">Terms (must be checked to send):</strong>", "&nbsp;");

            $tplvars = array("friend_name" => "<strong>[Friend's Name]</strong>"
                , "your_name" => "<strong>[Your Name]</strong>"
                , "companyname" => htmlspecialchars($profile->companyname)
                , "personal_message" => "<strong>[Personal Message]</strong>"
                , "link" => "<strong>[Link will be shown here]</strong>",
            );
            $this->input['emailtpl'] = $this->daffny->tpl->build("myaccount.freemonth.emailtpl", $tplvars);
            $this->input['ref_code'] = $profile->ref_code;
        } catch (Exception $e) {
            $this->setFlashError("Reffer a Friend: Undefined error. Access denied.");
            redirect(getLink('companyprofile'));
        }
    }

    public function sent()
    {
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("companyprofile") => "My Account", getLink("freemonth") => "Referral", '' => "Sent"));
        $this->tplname = "myaccount.freemonth.sent";
    }

    public function coupons()
    {
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("companyprofile") => "My Account", getLink("freemonth") => "Referral", '' => "Print Coupons"));

        $profile = new CompanyProfile($this->daffny->DB);
        $profile->getByOwnerId(getParentId());
        $this->tplname = "myaccount.freemonth.coupons";

        $this->input = array(
            "ref_code" => $profile->ref_code
            , "companyname" => htmlspecialchars($profile->companyname),
        );
    }

    public function referrals()
    {
        try {
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("companyprofile") => "My Account", getLink("freemonth") => "Referral", '' => "Signed-Up Referrals"));
            $this->tplname = "myaccount.freemonth.referrals";
            $memberManager = new MembersManager($this->daffny->DB);
            $this->applyOrder(Member::TABLE);
            $where = "";
            $referrals = $memberManager->getReferrals($this->order->getOrder(), $_SESSION['per_page'], getParentId());
            $this->setPager($memberManager->getPager());
            $this->daffny->tpl->referrals = $referrals;
        } catch (FDException $e) {
            $this->setFlashError("Reffer a Friend: Undefined error. Access denied.");
            redirect(getLink("companyprofile"));
        }
    }

}
