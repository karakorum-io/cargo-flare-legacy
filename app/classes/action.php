<?php

require_once DAFFNY_PATH . "libs/form.php";
require_once DAFFNY_PATH . "libs/pager_rewrite.php";
require_once DAFFNY_PATH . "libs/order_rewrite.php";
require_once ROOT_PATH . "libs/JSON.php";
require_once ROOT_PATH . "libs/phpmailer/class.phpmailer.php";
require_once ROOT_PATH . 'libs/recaptcha/recaptchalib.php';
require_once ROOT_PATH . "libs/anet/AuthorizeNet.php";
require_once ROOT_PATH . "libs/mdsip/mdsip.php";
require_once DAFFNY_PATH . "libs/payment/paypal/do_direct_payment.php";

require_once ROOT_PATH . "libs/QuickBooks.php";

class AppAction
{

    /**
     * Daffny object
     *
     * @var Daffny
     */
    public $daffny;

    protected $renderParentLayout = true;
    public $captcha_publickey = "6LeaJsUSAAAAAARn9Ym5Pv7hA26mlmbGWiQ_SvlC";
    public $captcha_privatekey = "6LeaJsUSAAAAACKxuwr17F2xMWS6wvTRRNuxzpPY";
    public $captcha_resp = null;
    # the error code from reCAPTCHA, if any
    public $captcha_error = null;

    /**
     * Pager object
     *
     * @var PagerRewrite
     */
    public $pager = null;

    /**
     * Order class object
     *
     * @var OrderRewrite
     */
    public $order = null;

    /**
     * Form object
     *
     * @var form
     */
    public $form;

    /**
     * JSON object
     *
     * @var object
     */
    public $json;

    /**
     * Action output
     *
     * @var string
     */
    public $out;

    /**
     * Action title
     *
     * @var string
     */
    public $title;

    /**
     * Action input
     *
     * @var mixed
     */
    public $input;

    /**
     * Action template name for render
     *
     * @var string
     */
    public $tplname;

    /**
     * Errors stack
     *
     * @var array
     */
    public $err = array();
    public $required = array();

    /**
     * States stack
     *
     * @var array
     */
    public $states = array();

    /**
     * Looks like constant for forms required mark
     *
     * @var string
     */
    public $requiredTxt = '<span class="required">*</span>';
    public $requiredTxtLeft = '&nbsp;<span style="color:red">*</span>';
    public $requiredTxtCompany = '<span id="shipper_rt" class="required">*</span>';

    /**
     * Users chmods
     *
     * @var array
     */
    public static $chmods = array(
        1 => array("Admin", "Admins")
        , 2 => array("Member", "Members"),
    );

    /**
     * Template name for user menu
     *
     * @var string
     */
    public $tplname_menu;

    /**
     * Should we show member menu?
     *
     * @var bool
     */
    public $show_menu = false;

    public $months = array(
        "01" => "01"
        , "02" => "02"
        , "03" => "03"
        , "04" => "04"
        , "05" => "05"
        , "06" => "06"
        , "07" => "07"
        , "08" => "08"
        , "09" => "09"
        , "10" => "10"
        , "11" => "11"
        , "12" => "12",
    );

    /**
     * Simple constructor
     *
     */
    public function __construct()
    {

        $this->form = new Form();
        $this->form->input = &$this->input;
        $this->json = new Services_JSON();
    }

    public function idx()
    {

    }

    public function init()
    {
        $this->DB = $this->daffny->DB;
    }

    /**
     * Required function for actions parent
     *
     * @return string
     */
    public function construct()
    {

        if (!isset($_SESSION['admin_here'])) {
            $_SESSION['admin_here'] = false;
        }

        if ($_SESSION['member_id'] > 0 && !$_SESSION["admin_here"]) {
            $m = new Member($this->daffny->DB);

            $m->load($_SESSION['member_id']);
            if (!$m->checkLoginRestrictions()) {
                $_SESSION["inf_message"] = "Sorry. User login times have been limited.";
                $this->daffny->auth->logout(SITE_IN . "user/signin");
            }
            //update session settings
            $m->reloadMemberSession();
        }

        $cont = "";
        if ($this->tplname != "") {
            $this->showMessage();
            $cont .= $this->daffny->tpl->build($this->tplname, $this->input);
        } else if (!is_array($this->input)) {
            $cont .= $this->input;
        }
        if (!$this->renderParentLayout) {
            echo $cont;
            exit();
        }
        if ($this->title != "") {
            $arr_cont = array(
                'title' => $this->title
                , 'content' => $cont,
            );

            $this->out .= $this->daffny->tpl->build("action", $arr_cont);
        } else {
            $this->out .= $cont;
        }
        return $this->out;
    }

    /**
     * Create a Thumbnail
     *
     * @param int $fileID
     * @param     $fileHash
     * @param     $newWidth
     * @param     $newHeight
     *
     * @internal param int $width
     * @internal param int $height
     * @return mixed File path or false
     */
    public function createThumb($fileID, $fileHash, $newWidth, $newHeight)
    {

        // File not exists
        $file = $this->daffny->DB->select_one("*, " . _fff("register_date") . " AS file_folder", "files", "WHERE id = '" . quote($fileID) . "' and hash = '" . quote($fileHash) . "'");
        if (empty($file)) {
            return false;
        }

        $file_path = $this->daffny->cfg["files"] . $file["file_folder"] . $file["hash"];
        $file_name = $file["name"];

        $csize = getimagesize($file_path);
        $width = $csize[0];
        $height = $csize[1];

        $tar_width = $newWidth;
        $tar_height = $newHeight;

        // Generate thumb
        $thumb_error = false;

        $hw = $height / $width;
        $wh = $width / $height;
        $ext = get_file_ext($file_name);

        if ($width > $height) {
            $width = $tar_width;
            $height = $width * $hw;

            if ($height > $tar_height) {
                $height = $tar_height;
                $width = $height * $wh;
            }
        } else {
            $height = $tar_height;
            $width = $height * $wh;

            if ($width > $tar_width) {
                $width = $tar_width;
                $height = $width * $hw;
            }
        }

        $im = ImageCreateTrueColor($width, $height);

        switch ($ext) {
            case "jpg":
                $im_in = ImageCreateFromJPEG($file_path);
                break;
            case "gif":
                $im_in = ImageCreateFromGIF($file_path);
                break;
            case "png":
                $im_in = ImageCreateFromPNG($file_path);
                break;
            default:
                $im_in = ImageCreateFromJPEG($file_path);
        }

        ImageCopyResampled($im, $im_in, 0, 0, 0, 0, $width, $height, $csize[0], $csize[1]);

        $thumb_path = "";
        switch ($ext) {
            case "jpg":
                $result = ImageJPEG($im);
                break;
            case "gif":
                $result = ImageGIF($im);
                break;
            case "png":
                $result = ImagePNG($im);
                break;
            default:
                $result = ImageJPEG($im);
        }

        return $result;
    }

    /**
     * Saves form post variables
     *
     * @param mixed $method
     * @param mixed $tpl
     * @param array $ommited_val
     *
     * @return string
     */
    protected function SaveFormVars($method = "post", $tpl = "", $ommited_val = array())
    {
        if ($tpl == "") {
            $tpl = $this->tplname;
        }

        $vars = array();
        $keys = $this->daffny->tpl->get_var_names($tpl);

        switch ($method) {
            case 'get':
                {
                    foreach ($keys as $k) {
                        if (in_array($k, $ommited_val)) {
                            continue;
                        }

                        $vars[$k] = htmlspecialchars(@$_GET[$k]);
                    }

                    break;
                }

            case 'post':
                {
                    foreach ($keys as $k) {
                        if (in_array($k, $ommited_val)) {
                            continue;
                        }

                        if (isset($_POST[$k])) {
                            $vars[$k] = htmlspecialchars(@$_POST[$k]);
                        }
                    }

                    break;
                }

            default:
                trigger_error("Unknown method: &quot;$method&quot;", E_USER_ERROR);
                break;
        }

        return $vars;
    }

    public function getDBErrorMessage()
    {
        if ($this->daffny->cfg['debug']) {
            return $this->daffny->DB->errorStr;
        } else {
            return "An error occurred, please try again later.";
        }
    }

    /**
     * Return a list of post vars which exists in template
     *
     * @param string $tpl
     * @param bool $stripTags
     *
     * @return array
     */
    protected function getTplPostValues($tpl = "", $stripTags = true)
    {
        if ($tpl == "") {
            $tpl = $this->tplname;
        }

        $postValues = array();
        $tplVars = $this->daffny->tpl->get_var_names($tpl);

        if ($stripTags) {
            foreach ($tplVars as $tplVar) {
                $postValues[$tplVar] = strip_tags(trim(post_var($tplVar)));
            }
        } else {
            foreach ($tplVars as $tplVar) {
                $postValues[$tplVar] = trim(post_var($tplVar));
            }
        }

        return $postValues;
    }

    protected function getContent($name, $type = 0)
    {
        if ($type == 1) {
            $arr = array("title" => "", "content" => "Пусто...");
            if ($name != "") {
                $arr = $this->daffny->DB->selectRow("title, content", "content", "WHERE name = '{$name}'");
            }
            return $arr;
        } else {
            if ($name != "") {
                return $this->daffny->DB->selectValue("content", "content", "WHERE name = '{$name}'");
            }
            return "Пусто...";
        }
    }

    /**
     * Check if empty value in post
     *
     * @param        $postKey
     * @param string $field
     * @param string $format
     *
     * @return bool
     * @internal param string $varname post key
     */
    protected function isEmpty($postKey, $field, $format = "Field <strong>%s</strong> can not be empty.")
    {
        if (trim(post_var($postKey)) == "") {
            $this->err[] = sprintf($format, $field);
            $this->required[] = $postKey;
            return true;
        }
        return false;
    }

    protected function isZero($postKey, $field, $format = "Field <strong>%s</strong> can not be empty.")
    {
        if (trim(post_var($postKey)) == 0) {
            $this->err[] = sprintf($format, $field);
            return true;
        }
        return false;
    }

    /**
     * Check email address
     *
     * @param $postKey
     * @param $fld
     *
     * @internal param string $email
     * @return bool
     */
    public function checkEmail($postKey, $fld)
    {
        //print "--".post_var($postKey)."--".
        if (trim(post_var($postKey)) != "") {
            if (preg_match("/^([-a-zA-Z0-9._]+@[-a-zA-Z0-9.]+(\.[-a-zA-Z0-9]+)+)*$/", trim(post_var($postKey)))) {
                return true;
            }
            $this->err[] = "Field <strong>" . $fld . "</strong> is not valid email address.";
            return false;
        }
        return true;
    }

    public function checkEmail2($val, $fld)
    {
        if (trim($val) != "") {
            if (preg_match("/^([-a-zA-Z0-9._]+@[-a-zA-Z0-9.]+(\.[-a-zA-Z0-9]+)+)*$/", $val)) {
                return true;
            }
            $this->err[] = "Field <strong>" . $fld . "</strong> is incorrect.";
            return false;
        }
        return true;
    }

    /**
     * put your comment there...
     *
     * @param mixed $val
     * @return mixed
     */
    protected function checkYesNo($val)
    {
        if ($val != "Yes" && $val != "No") {
            return "NULL";
        }
        return $val;
    }

    protected function showMessage()
    {
        if (count($this->err)) {
            $this->input['flash_message'] = $this->daffny->msg($this->err);
        } else if (isset($_SESSION['inf_message'])) {
            $this->input['flash_message'] = $this->daffny->msg($_SESSION['inf_message'], "apply");
            unset($_SESSION['inf_message']);
        } else if (isset($_SESSION['err_message'])) {
            $this->input['flash_message'] = $this->daffny->msg($_SESSION['err_message'], "error");
            unset($_SESSION['err_message']);
        } else {
            $this->input['flash_message'] = "";
        }
    }

    /**
     * @param $err
     */
    protected function setFlashError($err)
    {
        $_SESSION['err_message'] = $err;
    }

    /**
     * @param $info
     */
    protected function setFlashInfo($info)
    {
        $_SESSION['inf_message'] = $info;
    }

    /**
     * @return bool
     */
    protected function dbError()
    {
        if ($this->daffny->DB->isError) {
            /*
            $err = "<strong>Mysql error:</strong> ".$this->daffny->DB->errorStr;
            $err .= "<br />".$this->daffny->DB->errorQuery;
             */

            $err = "Can't save data to database. Please try again later.";

            $this->err[] = $err;

            return true;
        }

        return false;
    }

    /**
     * Check date
     *
     * @param string $val
     * @param string $fieldName
     * @return string correct date
     */
    protected function validateDate($val, $fieldName = "date")
    {
        if ($val != "") {
            $date_tmp = explode("/", $val);
            if (count($date_tmp) != 3 || !checkdate($date_tmp[0], $date_tmp[1], $date_tmp[2])) {
                $this->err[] = "<strong>$fieldName</strong> is incorrect. (Use format: dd/mm/yyyy)";
            } else {
                return $date_tmp[2] . "-" . $date_tmp[0] . "-" . $date_tmp[1];
            }
        }
        return "NULL";
    }

    /**
     * @param string $table
     * @param string $field
     * @param string $where
     * @param string $tpl
     *
     * @return string
     */
    protected function applyPager($table, $field = "", $where = "", $tpl = "grid_pager")
    {

        if (is_null($this->pager)) {
            $this->pager = new PagerRewrite($this->daffny->DB);
            $this->pager->UrlStart = getLink();
            $this->pager->RecordsOnPage = (isset($_SESSION['per_page'])) ? $_SESSION['per_page'] : 20;
        }
        $this->pager->init($table, $field, $where);
        $this->setPager(null, $tpl);
    }

    protected function setPager($pager = null, $tpl = "grid_pager")
    {
        if ($pager instanceof PagerRewrite) {
            $this->pager = $pager;
        }
        $tpl_arr = array(
            'navigation' => $this->pager->getNavigation()
            , 'current_page' => $this->pager->CurrentPage
            , 'pages_total' => $this->pager->PagesTotal
            , 'records_total' => $this->pager->RecordsTotal,
        );
        $this->input['pager'] = $this->daffny->tpl->build($tpl, $tpl_arr);

        return $this->input['pager'];
    }

    /**
     * @return null|\OrderRewrite
     */
    protected function applyOrder()
    {
        if (is_null($this->order)) {
            $this->order = new OrderRewrite($this->daffny->DB);
            $this->order->UrlStart = getLink();
        }

        if (isset($this->orderdef)) {
            $this->order->setDefault("id", "DESC");
        }

        $tables = func_get_args();

        foreach ($tables as $table) {
            $this->daffny->DB->query("SHOW COLUMNS FROM $table");

            while ($row = $this->daffny->DB->fetch_row()) {
                $this->order->Fields[] = $row['Field'];
            }
        }

        $this->order->init();
        $this->daffny->tpl->order = $this->order;

        return $this->order;
    }

    /**
     * @param mixed $sql
     * @param bool $useNoRecords
     *
     * @return bool
     */
    protected function getGridData($sql, $useNoRecords = true, $is_cp = false)
    {
        $rows = $this->daffny->DB->selectRows($sql);

        if (!count($rows)) {
            if ($useNoRecords) {
                $this->tplname = "no_records";
            }

            return false;
        }

        if ($is_cp) {
            foreach ($rows as $row) {

                $license = new License($this->daffny->DB);
                $license->loadCurrentLicenseByMemberId($row["parent_id"]);
                $cur_space = $license->getCurrentStorageSpace();
                $used_space = $license->getUsedStorageSpace();

                $row["used_space"] = get_file_size($used_space);
                $row["storage_space"] = get_file_size($cur_space);
                $row["rest_space"] = get_file_size($cur_space - $used_space);

                $this->daffny->tpl->data[] = $row;
            }
            return $this->daffny->tpl->data;
        } else {
            $this->daffny->tpl->data = $rows;
            return $this->daffny->tpl->data;
        }

    }

    protected function get_grid_data($sql, $useNoRecords = true, $is_cp = false)
    {
        $rows = $this->daffny->DB->selectRows($sql);

        if ($is_cp){
            foreach($rows as $row){

                $license = new License($this->daffny->DB);
                $license->loadCurrentLicenseByMemberId($row["parent_id"]);
                $cur_space = $license->getCurrentStorageSpace();
                $used_space = $license->getUsedStorageSpace();

                $row["used_space"] = get_file_size($used_space);
                $row["storage_space"] = get_file_size($cur_space);
                $row["rest_space"] = get_file_size($cur_space - $used_space);

                $this->daffny->tpl->data[] = $row;
            }
            return $this->daffny->tpl->data;
        }else{
            $this->daffny->tpl->data = $rows;
            return $this->daffny->tpl->data;
        }

    }

    /**
     * check id
     *
     * @return int
     */
    protected function checkId()
    {
        $ID = (int) get_var("id");
        if ($ID <= 0) {
            die("Invalid id");
        }

        return $ID;
    }

    /**
     * Send an email
     *
     * @param string $toName
     * @param string $toEmail
     * @param string $subject
     * @param string $tplName
     * @param array $tplData
     * @param string $message
     * @param array $files
     *
     * @throws phpmailerException
     * @internal param array $headers
     * @return bool
     */
    public function sendEmail($toName, $toEmail, $subject, $tplName = "", $tplData = array(), $message = "", $files = array())
    {
        if (!empty($tplName)) {
            $tplData['system_phone'] = $this->daffny->DB->selectField('value', 'settings', "WHERE `name` = 'phone'");
            $tplData['info_email'] = $this->daffny->DB->selectField('value', 'settings', "WHERE `name` = 'info_email'");
            $tplData['site_title'] = $this->daffny->cfg['site_title'];
            $tplData['site_url'] = BASE_PATH;
            $this->daffny->tpl->path = ROOT_PATH . "app/templates/";
            $message = $this->daffny->tpl->build("email." . $tplName, $tplData);
        }

        $layout_vars = array(
            'letter_title' => $subject
            , 'content' => $message
            , 'site_title' => $this->daffny->cfg['site_title']
            , 'site_url' => BASE_PATH,
        );
        $message = $this->daffny->tpl->build("email.layout", $layout_vars);
        $from = $this->daffny->cfg['info_email'];
        $ret = "";
        try {
            $mail = new FdMailer(true);
            $mail->IsHTML();
            $mail->Body = $message;
            $mail->Subject = $this->daffny->cfg['site_title'] . ": " . $subject;
            if ($from != "") {
                $mail->SetFrom($from);
                $mail->AddReplyTo($from);
            }
            $mail->AddAddress($toEmail, $toName);
            // TODO: remove CC
            //$mail->AddCC("alexanderintech@gmail.com");

            if (!empty($files)) {
                foreach ($files as $file) {
                    $mail->AddAttachment($file['path'], $file['name']);
                }
            }

            ob_start();
            $ret = $mail->Send();
            $mailer_output = ob_get_clean();
            if (!$ret) {
                throw new Exception($mailer_output . "\n");
            } else {
                $ret = true;
            }

        } catch (phpmailerException $e) {
            $ret = $e->getMessage();
        } catch (Exception $e) {

        }
        return $ret;
    }

    protected function sendAdminNotify($type, $tplData = array(), $files = array())
    {
        $types = array(
            'feedback' => "FeedBack"
            , 'contactus' => "Contact Us"
            , 'welcome' => "Registration"
            , 'order' => "New order",
        );

        if (!array_key_exists($type, $types)) {
            return false;
        }

        $subject = $types[$type];
        $tplName = "{$type}";

        $sql = "SELECT email
                     , contactname AS name
                  FROM members
                 WHERE chmod = 1
                   AND status = 'Active'
                   AND email_notify = 1
                 ORDER BY reg_date";

        $admins = $this->daffny->DB->selectRows($sql);

        foreach ($admins as $i => $admin) {
            $this->sendEmail($admin['name'], $admin['email'], $subject, $tplName, $tplData, "", $files);
        }
        return true;
    }

    /**
     * Get captcha image
     *
     */
    protected function getCaptcha()
    {
        return '<div class="captcha_div"><script type="text/javascript">
			 var RecaptchaOptions = {
				theme : \'clean\'
			 };
			 </script>' . recaptcha_get_html($this->captcha_publickey, $this->captcha_error) . '</div>';
    }

    /**
     * Check captcha
     *
     * @internal param string $val
     * @return bool
     */
    protected function checkCaptcha()
    {
        $this->captcha_resp = recaptcha_check_answer($this->captcha_privatekey,
            $_SERVER["REMOTE_ADDR"],
            $_POST["recaptcha_challenge_field"],
            $_POST["recaptcha_response_field"]);

        if ($this->captcha_resp->is_valid) {
            return true;
        } else {
            $this->captcha_error = $this->captcha_resp->error;
            return false;
        }
    }

    /**
     * Check date
     *
     * @param $items
     *
     * @internal param string $val
     * @internal param string $fieldName
     * @return string correct date
     */
    public function getBreadCrumbs($items)
    {
        if (is_array($items)) {
            $this->daffny->tpl->crumbs = $items;
        } else {
            $this->daffny->tpl->crumbs = array('' => $items);
        }
        return $this->daffny->tpl->build("breadcrumbs");
    }

    public function checkaccess()
    {
        if (isGuest()) {
            redirect("user", "signin");
            exit;
        }

    }

    protected function getStates($empty = false)
    {
        if (!count($this->states)) {
            if ($empty) {
                $this->states[''] = "Select one";
            }

            $result = $this->daffny->DB->selectRows("code, name", "states", "ORDER BY name", "code");
            foreach ($result as $code => $values) {
                $this->states[$code] = $values['name'];
            }
        }

        return $this->states;
    }

    protected function getCanadaStates()
    {
        return array(
            'AB' => 'Alberta',
            'BC' => 'British Columbia',
            'MB' => 'Manitoba',
            'NB' => 'New Brunswick',
            'NL' => 'Newfoundland',
            'NT' => 'Northwest Territories',
            'NS' => 'Nova Scotia',
            'NU' => 'Nunavut',
            'ON' => 'Ontario',
            'PE' => 'Prince Edward Island',
            'QC' => 'Quebec',
            'SK' => 'Saskatchewan',
            'YT' => 'Yukon',
        );
    }

    protected function getAllStates()
    {
        $countries = $this->getCountries();
        $states = array();
        foreach ($countries as $k => $v) {
            $states[$v] = $this->getCountryStates($k);
        }
        return $states;
    }

    protected function getCountryStates($country_code)
    {
        switch ($country_code) {
            case 'US':
                return $this->getStates();
                break;
            case 'CA':
                return $this->getCanadaStates();
                break;
            default:
                return null;
        }
    }

    protected function getRegions()
    {
        return array(
            'northeast' => 'Northeast - ME,VT,NH,MA,RI,CT,NY,NJ,PA,DE',
            'southeast' => 'Southeast - MD,DC,VA,WV,KY,TN,NC,SC,AL,GA,FL',
            'midwest-plains' => 'Midwest/Plains - OH,IN,IL,MO,KS,WI,MI,MN,IA,NE,SD,ND',
            'south' => 'South - TX,OK,AR,LA,MS',
            'northwest' => 'Northwest - WA,OR,ID,MT,WY',
            'southwest' => 'Southwest - CA,NV,UT,AZ,CO,NM',
            'pacific' => 'Pacific - AK,HI',
        );
    }

    protected function getVehiclesTypes($empty = false)
    {
        $types = array();
        if ($empty) {
            $types[''] = "--Select one--";
        }
        $result = $this->daffny->DB->selectRows("name", "app_vehicles_types", "ORDER BY name", "name");
        foreach ($result as $code => $values) {
            $types[$code] = $values['name'];
        }
        return $types;
    }

    protected function getCountryByCode($code)
    {
        return $this->daffny->DB->selectField("name", "countries", "WHERE code = '" . mysqli_real_escape_string($this->daffny->DB->connection_id, $code) . "'");
    }

    /**
     * put your comment there...
     *
     */
    protected function getCountries()
    {
        $countries = $this->daffny->DB->selectRows("SELECT code,name FROM countries ORDER BY name");

        $morphedCountries[''] = [
            'US'=>'United States',
            'CA'=>'Canada'
        ];
        
        foreach($countries as $country){
            if($country['code'] != "US" || $country['code'] != "CA"){
                $morphedCountries['Other Nations'][$country['code']] = $country['name'];
            }
        }

        return $morphedCountries;
    }

    protected function collectSubscribers($cat, $sql_arr)
    {
        $cats = array("contactus" => 186
            , "feedback" => 184
            , "subscribers" => 179
            , "members" => 185,
        );

        if (isset($sql_arr['street_address'])) {
            $sql_arr['address'] = $sql_arr['street_address'];
        }
        if (isset($sql_arr['phone_d'])) {
            $sql_arr['phone'] = $sql_arr['phone_d'];
        }
        if (isset($sql_arr['first'])) {
            $sql_arr['first_name'] = $sql_arr['first'];
        }
        if (isset($sql_arr['last'])) {
            $sql_arr['last_name'] = $sql_arr['last'];
        }
        if (isset($sql_arr['your_name'])) {
            $sql_arr['first_name'] = $sql_arr['your_name'];
        }

        $ins_arr = array();
        $ins_arr['first_name'] = @$sql_arr['first_name'];
        $ins_arr['last_name'] = @$sql_arr['last_name'];
        $ins_arr['email'] = $sql_arr['email'];
        $ins_arr['address'] = @$sql_arr['address'];
        $ins_arr['city'] = @$sql_arr['city'];
        $ins_arr['state'] = @$sql_arr['state'];
        $ins_arr['zip'] = @$sql_arr['zip'];
        $ins_arr['phone'] = @$sql_arr['phone'];
        $ins_arr['country'] = @$sql_arr['country'];
        $ins_arr['category_id'] = $cats[$cat];
        $ins_arr['reg_date'] = date("Y-m-d H:i:s");
        $this->daffny->DB->insert("subscribers", $ins_arr);
    }

    protected function getTimeZones()
    {
        $timezones = array();
        $result = $this->daffny->DB->selectRows("php, label", "time_zones", "ORDER BY label", "php");
        foreach ($result as $code => $values) {
            $timezones[$code] = $values['label'];
        }
        return $timezones;
    }

    protected function checkDuplicateEmail($email, $notId = 0)
    {
        $where_add = $notId > 0 ? " AND id != " . $notId : "";
        if ($notId == 0) {
            $where_add = !isGuest() ? " AND id != " . getMemberId() : "";
        }

        $row = $this->daffny->DB->select_one("id", "members", "WHERE email = '" . mysqli_real_escape_string($this->daffny->DB->connection_id, $email) . "'" . $where_add);
        if (!empty($row)) {
            return true;
        }

        return false;
    }

    protected function checkDuplicateUsername($un, $notId = 0)
    {
        $where_add = $notId > 0 ? " AND id != " . $notId : "";
        if ($notId == 0) {
            $where_add = !isGuest() ? " AND id != " . getMemberId() : "";
        }

        $row = $this->daffny->DB->select_one("id", "members", "WHERE username = '" . mysqli_real_escape_string($this->daffny->DB->connection_id, $un) . "'" . $where_add);
        if (!empty($row)) {
            return true;
        }

        return false;
    }

    public function getFormattedDate($val)
    {
        if ($val != "") {
            $val = @substr($val, 0, 10);
            $date_tmp = @explode("-", $val);
            if (count($date_tmp) != 3 || !checkdate($date_tmp[1], $date_tmp[2], $date_tmp[0])) {
                return "";
            } else {
                return $date_tmp[1] . "/" . $date_tmp[2] . "/" . $date_tmp[0];
            }
        }
        return "";
    }

    protected function getYesNo($value)
    {
        $val_arr = array("1" => "Yes", "0" => "No");
        if (isset($val_arr[$value])) {
            return $val_arr[$value];
        }
        return "";
    }

    protected function getCCTypes()
    {
        return array("1" => "Visa"
            , "2" => "MasterCard"
            , "3" => "Amex"
            , "4" => "Discover",
        );
    }

    protected function getCCYears()
    {
        $years = array();
        for ($i = date("Y"); $i <= date("Y") + 20; $i++) {
            $years[$i] = $i;
        }
        return $years;
    }

    public function processAuthorize($pay)
    {
        $api_login = $pay['anet_api_login_id'];
        $api_pwd = $pay['anet_trans_key'];
        $api_amount = $pay['amount'];

        $pay_success = false;
        $pay_reason = "";
        $transaction_id = "";

        $transaction = new AuthorizeNetAIM($api_login, $api_pwd);
        $transaction->setSandbox($this->daffny->cfg['anet_sandbox']);

        $transaction->setFields(
            array(
                'amount' => $api_amount
                , 'card_num' => $pay['cc_number']
                , 'exp_date' => $pay['cc_month'] . "/" . $pay['cc_year']
                , 'card_code' => $pay['cc_cvv2']
                , 'first_name' => $pay['cc_fname']
                , 'last_name' => $pay['cc_lname']
                , 'address' => $pay['cc_address']
                , 'city' => $pay['cc_city']
                , 'state' => $pay['cc_state']
                , 'zip' => $pay['cc_zip']
                , 'description' => "Freight Dragon: Order#" . $pay['order_number']
                , 'invoice_num' => $pay['order_number'],
            )
        );
        $response = $transaction->authorizeAndCapture();
        if ($response->approved) {
            return array("success" => true
                , "transaction_id" => $response->transaction_id,
            );
        } else {
            return array("success" => false
                , "error" => $response->response_reason_text,
            );
        }
    }

    public function processPayPal($pay)
    {
        $paypal = new DoDirectPayment();
        $paypal->Environment = $this->daffny->cfg['paypal_environment'];
        $paypal->apiUserName = $pay['paypal_api_username'];
        $paypal->apiPassword = $pay['paypal_api_password'];
        $paypal->apiSignature = $pay['paypal_api_signature'];

        $paypal->creditCardType = $pay["cc_type_name"];
        $paypal->creditCardNumber = $pay["cc_number"];
        $paypal->expDate = $pay["cc_month"] . $pay["cc_year"];
        $paypal->CVV2 = $pay["cc_cvv2"];

        $paypal->firstName = $pay["cc_fname"];
        $paypal->lastName = $pay["cc_lname"];
        $paypal->street = $pay["cc_address"];
        $paypal->city = $pay["cc_city"];
        $paypal->state = $pay['cc_state'];
        $paypal->countryCode = "US";
        $paypal->zip = $pay['cc_zip'];

        $paypal->amount = number_format($pay['amount'], 2, '.', ',');

        $response = $paypal->sendRequest();
        if ($response['ACK'] != "Success") {
            return array("success" => false, "error" => @$response['L_ERRORCODE0'] . " " . @$response['L_SHORTMESSAGE0'] . " " . @$response['L_LONGMESSAGE0'],
            );
        }

        return array("success" => true
            , "transaction_id" => @$response['TRANSACTIONID'],
        );
    }

    public function processMDSIP($pay)
    {
        $api_login = $pay['gateway_api_username'];
        $api_pwd = $pay['gateway_api_password'];
        $api_amount = $pay['amount'];

        $pay_success = false;
        $pay_reason = "";
        $transaction_id = "";

        $gw = new gwapi;
        $gw->setLogin($api_login, $api_pwd);

        $gw->setBilling(
            $pay['cc_fname'],
            $pay['cc_lname'],
            $pay['company'],
            $pay['cc_address'],
            $pay['address2'],
            $pay['cc_city'],
            $pay['cc_state'],
            $pay['cc_zip'],
            "US",
            $pay['phone1'],
            $pay['phone2'],
            $pay['email'],
            "www.freightdragon.com");

        $gw->setShipping($pay['cc_fname'],
            $pay['cc_lname'],
            $pay['company'],
            $pay['cc_address'],
            $pay['address2'],
            $pay['cc_city'],
            $pay['cc_state'],
            $pay['cc_zip'],
            "US",
            $pay['email'],
            "www.freightdragon.com");

        $gw->setOrder($pay['orderid'], $pay['orderdescription'], $pay['tax'], $pay['shipping'], $pay['cc_zip'], $pay['ipaddress']);

        $r = $gw->doSale($api_amount, $pay["cc_number"], $pay["cc_month"] . $pay["cc_year"], $pay["cc_cvv2"]);
        $response = $gw->responses['responsetext'];

        if ($response == "APPROVED") {
            return array(
                "success" => true, 
                "transaction_id" => $gw->responses['transactionid'],
            );
        } else {
            return array(
                "success" => false, 
                "error" => $gw->responses['responsetext'],
            );
        }
    }

    protected function postToInput()
    {
        foreach ($_POST as $key => $val) {
            if (!is_array($val)) {
                $this->input[$key] = htmlspecialchars($val);
            } else {
                foreach ($val as $key2 => $val2) {
                    $this->input[$key][$key2] = htmlspecialchars($val2);
                }
            }
        }
    }

    public function getCustomerServiceNames()
    {
        $results = array();
        $rows = $this->daffny->DB->selectRows("CONCAT(first_name, ' ', last_name) AS name",
            "administrators",
            "WHERE IsCustomerService = 'Yes' AND status = 'Active'");
        foreach ($rows as $row) {
            $results[$row['name']] = $row['name'];
        }

        return $results;
    }

    final public function getFormTemplates($used = "quotes")
    {

        $sql = "SELECT id, name
                  FROM " . FormTemplate::TABLE . "
                  WHERE owner_id = '" . getParentID() . "'
                  AND usedfor ='" . $used . "'";

        $rows = $this->daffny->DB->selectRows($sql);
        $ret = array();
        if (count($rows) > 0) {
            foreach ($rows as $template) {
                $ret[$template["id"]] = $template["name"];
            }
        }

        return $ret;
    }

    final public function getEmailTemplates($used = "quotes")
    {

        $sql = "SELECT id, name
                  FROM " . EmailTemplate::TABLE . "
                  WHERE owner_id = '" . getParentID() . "'
                  AND usedfor ='" . $used . "'";

        if ($used == "orders") {
            $sql .= " AND (sys_id IS NULL OR (sys_id <> '" . EmailTemplate::SYS_ORDER_THANKS . "' AND  sys_id <> '" . EmailTemplate::SYS_ORDER_DISPATCH_LINK . "' ))";
        }

        $rows = $this->daffny->DB->selectRows($sql);
        $ret = array();
        if (count($rows) > 0) {
            foreach ($rows as $template) {
                $ret[$template["id"]] = $template["name"];
            }
        }
        return $ret;
    }

    public function initializeQuickBook()
    {
        // $this->daffny->cfg['dsn'];
        if (!QuickBooks_Utilities::initialized($this->daffny->cfg['dsn'])) {
            // Initialize creates the neccessary database schema for queueing up requests and logging
            QuickBooks_Utilities::initialize($this->daffny->cfg['dsn']);

            // This creates a username and password which is used by the Web Connector to authenticate
            QuickBooks_Utilities::createUser($this->daffny->cfg['dsn'], $this->daffny->cfg['qbwc_user'], $this->daffny->cfg['qbwc_pass']);

        }
    }
}
