<?php

require_once ROOT_PATH . 'config.php';
require_once ROOT_PATH . 'libs/phpmailer/class.phpmailer.php';

class FdMailer extends PHPMailer
{
    public $SMTPDebug = 1;
    public function __construct($exception = false)
    {
        parent::__construct($exception);
        $this->isSMTP();
        $this->CharSet = 'utf-8';
        $this->Host = $GLOBALS['CONF']['SMTPSERVER'];
        $this->SMTPAuth = $GLOBALS['CONF']['SMTPAUTH'];
        if ($this->SMTPAuth) {
            $this->Username = $GLOBALS['CONF']['SMTPUSER'];
            $this->Password = $GLOBALS['CONF']['SMTPPWD'];
        }
    }

    public function Send()
    {
        if (isset($_SESSION['member']) && !isset($_SESSION['member']['IsCustomerService'])) {
            $member = new Member($GLOBALS['daffny']->DB);
            $member->load($_SESSION['member']['id']);
            $settings = $member->getDefaultSettings();
            $bccs = explode(",", $settings->email_blind);

            foreach ($bccs as $bcc) {
                if (trim($bcc) != "" && preg_match("/^([-a-zA-Z0-9._]+@[-a-zA-Z0-9.]+(\.[-a-zA-Z0-9]+)+)*$/", trim($bcc))) {
                    $this->AddBCC(trim($bcc));
                }
            }

            if ($settings->smtp_user_approved == 1) {
                if (trim($settings->smtp_server_name) != "" && trim($settings->smtp_user_name) != "" && trim($settings->smtp_user_password) != "") {
                    $this->Host = $settings->smtp_server_name;

                    if (trim($settings->smtp_server_port) != "") {
                        $this->Port = $settings->smtp_server_port;
                    }

                    if ($settings->smtp_use_ssl == 1) {
                        $this->SMTPSecure = 'ssl';
                    }

                    if ($this->SMTPAuth) {
                        $this->Username = $settings->smtp_user_name;
                        $this->Password = $settings->smtp_user_password;
                    }
                }
            }
        }

        if ($GLOBALS['CONF']['debug']) {

            $log = var_export(array(
                "Subject" => $this->Subject,
                "From" => $this->From,
                "FromName" => $this->FromName,
                "To" => $this->to,
                "CC" => $this->cc,
                "BCC" => $this->bcc,
                "Body" => $this->Body,
            ), true);
            file_put_contents(ROOT_PATH . "email.log", $log, FILE_APPEND);
        }

        return parent::Send();
    }

    public function SendToCD($mailData = array(), $type = 0)
    {

        $this->Host = $GLOBALS['CONF']['CDSMTPSERVER'];
        $this->SMTPAuth = $GLOBALS['CONF']['CDSMTPAUTH'];

        if ($this->SMTPAuth) {
            if ($_SERVER['REMOTE_ADDR'] == "182.156.245.130") {
                file_put_contents('chetuProductionIssue20022018.txt', print_r($mailData, true) . PHP_EOL, FILE_APPEND | LOCK_EX);
            }
            $this->Username = $GLOBALS['CONF']['CDSMTPUSER'];
            $this->Password = $GLOBALS['CONF']['CDSMTPPWD'];
            $this->Port = 25;
        }
        return parent::Send();
    }

    public function SendFromCron($member_id = 0)
    {

        if (isset($member_id) && $member_id > 0) {
            $member = new Member($GLOBALS['daffny']->DB);
            $member->load($member_id);
            $settings = $member->getDefaultSettings();
            $bccs = explode(",", $settings->email_blind);

            foreach ($bccs as $bcc) {
                if (trim($bcc) != "" && preg_match("/^([-a-zA-Z0-9._]+@[-a-zA-Z0-9.]+(\.[-a-zA-Z0-9]+)+)*$/", trim($bcc))) {
                    $this->AddBCC(trim($bcc));
                }
            }
			
            if ($settings->smtp_user_approved == 1) {
                if (trim($settings->smtp_server_name) != "" && trim($settings->smtp_user_name) != "" && trim($settings->smtp_user_password) != "") {
                    $this->Host = $settings->smtp_server_name;

                    if (trim($settings->smtp_server_port) != "") {
                        $this->Port = $settings->smtp_server_port;
                    }

                    if ($settings->smtp_use_ssl == 1) {
                        $this->SMTPSecure = 'ssl';
                    }

                    if ($this->SMTPAuth) {
                        $this->Username = $settings->smtp_user_name;
                        $this->Password = $settings->smtp_user_password;
                    }
                }
            }
        }

        if ($GLOBALS['CONF']['debug']) {
            $log = var_export(array(
                "Subject" => $this->Subject,
                "From" => $this->From,
                "FromName" => $this->FromName,
                "To" => $this->to,
                "CC" => $this->cc,
                "BCC" => $this->bcc,
                "Body" => $this->Body,
            ), true);

            file_put_contents(ROOT_PATH . "email.log", $log, FILE_APPEND);
        }

        return parent::Send();
    }
}
