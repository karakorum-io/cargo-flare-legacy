<?php

class ApplicationAction extends AppAction {
	public $breadcrumbs;

    public static $actionLinks = null;

    /**
     * put your comment there...
     *
     */
    public function __construct() {
		parent::__construct();
    }

    public function init() {
        parent::init();
        $this->initGlobals();
    }

    /**
     * put your comment there...
     *
     */
    public function construct() {

        //check expired license
        $license = new License($this->daffny->DB);
        $license->loadCurrentLicenseByMemberId(getParentId());
        if ((strtotime($license->expire) + 86400) < time()) {
            if (!in_array($this->daffny->action, array("billing","companyprofile", "defaultsettings")) ){
                $this->setFlashError("License has been expired.");
                redirect("/application/billing");
            }
        }

        $cont = "";
        if ($this->tplname != "") {
            $this->showMessage();

            $cont .= $this->daffny->tpl->build($this->tplname, $this->input);
        } else if (!is_array($this->input)) {
            $cont .= $this->input;
        }

        if ($this->title != "") {
            $arr_cont = array(
                'title' => $this->title
                , 'content' => $cont
            );

            $this->out .= $this->daffny->tpl->build("action", $arr_cont);
        } else {
            $this->out .= $cont;
        }
        return $this->out;
    }

    /**
     * put your comment there...
     *
     */
    protected function applyGridAction() {
        $this->input['grid_action'] = $this->daffny->tpl->build("grid_action");
        return $this->input['grid_action'];
    }

    protected function checkDuplicateCompanyEmail($email, $owner_id) {
        $row = $this->daffny->DB->select_one("id", "app_company_profile", "WHERE email = '" . quote($email) . "' AND owner_id <> '" . $owner_id . "'");
        if (!empty($row)) {
            return true;
        }
        return false;
    }

    protected function check_access($module = "", $action = "", $params = array()) {
        switch ($module) {
            case "user":
                //And need check CanI... ($module, $action)
                if (isset($params['id']) && $params['id'] > 0) {
                    $sql = "SELECT m.*
		                      FROM members m
							 WHERE m.parent_id = '" . getParentId() . "'
							   AND m.access_users = 1";
                    $row = $this->daffny->DB->selectRow($sql);

                    if (empty($row)) {
                        $this->setFlashError("Access Denied.");
                        redirect(getLink(""));
                    }
                }
                break;
			case "users":
				if (!$_SESSION['member']['access_users']) {
					$this->setFlashError("Access denied.");
					redirect(getLink());
				}
				break;
            case "users_groups":
                //And need check CanI... ($module, $action)
                if ($params['id'] > 0) {
                    $sql = "SELECT ug.*
		                      FROM app_members_groups ug
		                     WHERE ug.id = '" . $params['id'] . "' AND owner_id='" . getParentId() . "'";
                    $row = $this->daffny->DB->selectRow($sql);
                    if (empty($row)) {
                        $this->setFlashError("Bad group ID.");
                        redirect(getLink("users_groups"));
                    }
                }
                break;
            case "settings":
				if (isset($params['id']) && $params['id'] > 0) {
					$sql = "SELECT m.*
		                      FROM members m
		                     WHERE m.id = '" . $params['id'] . "'
							   AND m.parent_id = '" . getParentId() . "'
							   AND m.access_preferences = 1";
					$row = $this->daffny->DB->selectRow($sql);

					if (empty($row)) {
						$this->setFlashError("Access Denied.");
						redirect(getLink(""));
					}
				}
                break;
            case "formtemplates":
                //And need check CanI... ($module, $action)
                if (isset($params['id']) && $params['id'] > 0) {
                    $sql = "SELECT *
		                      FROM app_formtemplates
		                     WHERE id = '" . $params['id'] . "' AND owner_id='" . getParentId() . "'";
                    $row = $this->daffny->DB->selectRow($sql);

                    if (empty($row)) {
                        $this->setFlashError("Action N/A. Bad form template ID.");
                        redirect(getLink("companyprofile"));
                    }
                }
                break;
            case "emailtemplates":
                //And need check CanI... ($module, $action)
                if (isset($params['id']) && $params['id'] > 0) {
                    $sql = "SELECT *
		                      FROM app_emailtemplates
		                     WHERE id = '" . $params['id'] . "' AND owner_id='" . getParentId() . "'";
                    $row = $this->daffny->DB->selectRow($sql);

                    if (empty($row)) {
                        $this->setFlashError("Access denied. Bad email template ID.");
                        redirect(getLink("companyprofile"));
                    }
                }
                break;

            case "autoquoting":
                //And need check CanI... ($module, $action)
                if (isset($params['id']) && $params['id'] > 0) {
                    $sql = "SELECT *
		                      FROM app_autoquoting_seasons
		                     WHERE id = '" . $params['id'] . "' AND owner_id='" . getParentId() . "'";
                    $row = $this->daffny->DB->selectRow($sql);

                    if (empty($row)) {
                        $this->setFlashError("Access denied. Bad AQ ID.");
                        redirect(getLink("companyprofile"));
                    }
                }
                break;

            case "referrers":
                //And need check CanI... ($module, $action)
                if (isset($params['id']) && $params['id'] > 0) {
                    $sql = "SELECT *
		                      FROM app_referrers
		                     WHERE id = '" . $params['id'] . "' AND owner_id='" . getParentId() . "'";
                    $row = $this->daffny->DB->selectRow($sql);

                    if (empty($row)) {
                        $this->setFlashError("Access denied. Bad AQ ID.");
                        redirect(getLink("referrers"));
                    }
                }
                break;
            case "accounts":
				if (!$_SESSION['member']['access_accounts']) {
					$this->setFlashError("Access denied.");
					redirect(getLink());
				}
                //And need check CanI... ($module, $action)
                
                break;
			/*case "shippers":
				if (!$_SESSION['member']['access_accounts']) {
					$this->setFlashError("Access denied.");
					redirect(getLink());
				}
                //And need check CanI... ($module, $action)
                
                break;
             */
            case "leadsources":
				if (!$_SESSION['member']['access_lead_sources']) {
					$this->setFlashError("Access denied.");
					redirect(getLink());
				}
                //And need check CanI... ($module, $action)
                
                break;
			case "reports":
				if (!$_SESSION['member']['access_reports']) {
					$this->setFlashError("Access denied.");
					redirect(getLink());
				}
				break;
			case "preferences":
				if (!$_SESSION['member']['access_preferences']) {
					$this->setFlashError("Access denied.");
					redirect(getLink());
				}
				break;
			case "payments":
				if (!$_SESSION['member']['access_payments']) {
					$this->setFlashError("Access denied.");
					redirect(getLink());
				}
				break;
			case "dispatch":
				if (!$_SESSION['member']['access_dispatch']) {
					$this->setFlashError("Access denied.");
					redirect(getLink());
				}
				break;
			case "status":
				if ($_SESSION['member']['status']!='Active') {
					$this->setFlashError("Access denied.");
					redirect(getLink());
				}
				break;
            default:
				$this->setFlashError("Bad request.");
				redirect(SITE_IN);
				break;
        }
		return true;
    }

    protected function getCompanyMembers() {
        $me = new Member($this->daffny->DB);
        $me->load($_SESSION['member_id']);
        $membersManager = new MembersManager($this->daffny->DB);
        if ($me->parent_id == 0) {
            $members = $membersManager->getMembers("`id` = " . (int) $me->id . " OR `parent_id` = " . (int) $me->id . " ORDER BY `contactname`");
        } else {
            $members = $membersManager->getMembers("(`id` = " . (int) $me->parent_id . " OR `parent_id` = " . (int) $me->parent_id . ") ORDER BY `contactname`");
        }
        return $members;
    }

    protected function getQty($a, $b) {
        $qty = array();
        for ($i = $a; $i <= $b; $i++) {
            $qty[$i] = $i;
        }
        return $qty;
    }

    protected function initGlobals() {
      $this->initializeQuickBook();
        $this->daffny->tpl->company_members = $this->getCompanyMembers();
        $this->daffny->tpl->search_type = (isset($_POST['search_type'])) ? $_POST['search_type'] : '';
        try {
            $taskManager = new TaskManager($this->daffny->DB);
            //$this->daffny->tpl->mytasks = $taskManager->getByMemberId($_SESSION['member_id'], Task::TYPE_TODAY);			$this->daffny->tpl->mytasks = $taskManager->getByMemberId($_SESSION['member_id'], Task::TYPE_REMINDER);
			  
		  $rows = $this->daffny->DB->selectRows("*,DATE_FORMAT(now(),'%M %d, %Y %H:%i:%s') as cdate,DATE_FORMAT(followup,'%M %d, %Y %H:%i:%s') as followupf,DATE_FORMAT(snooze_date,'%M %d, %Y %H:%i:%s') as snooze_datef", "app_followups", "WHERE `sender_id` = ".(int)$_SESSION['member_id']."  and DATE_ADD( NOW( ) , INTERVAL 1 HOUR ) >= followup
AND NOW() <= followup And app_time!='' AND status!=1"); 
		  $this->daffny->tpl->my_appointments = $rows;
		  
		  $AM_PM = 'AM';
			$TimeArr = array();
			for($i=0.15,$j=0.15,$m=0.15;$i<=24;$i+=0.15,$j+=0.15,$m+=0.15)
			{
				if($j==0.60)
				{
				  $i = (int)$i +1;
				  $j=0.0;
				} 
				if($i > 12)
				   $AM_PM = 'PM';
				
				$k = number_format((float)$i, 2, '.', '');
				$k = str_replace(".",":",$k);	
				$i = number_format((float)$i, 2, '.', '');
			    $TimeArr[$i."_".$AM_PM] = $k.' '.$AM_PM;
				
			}
			
			$this->daffny->tpl->my_appointments_time = $TimeArr;  
			
			//$results = $this->daffny->DB->selectRows("phone", "app_sms_account_users", "WHERE user_id='" . getParentId() . "' order by id desc limit 0,1");
			$results = $this->daffny->DB->selectRows("phone", "app_sms_account_users", "WHERE user_id='" . (int)$_SESSION['member_id'] . "' and status=1 order by id desc limit 0,1");
			if (!empty($results)) {
					foreach ($results as $key => $value) {
							$_SESSION['phoneSMSflag'] = 1;
							$_SESSION['phoneSMS'] = $value['phone'];
					}
			}
			else
			  $_SESSION['phoneSMSflag'] = 0;
		  //print_r($rows);
        } catch (FDException $e) {
            
        }
    }

}

?>