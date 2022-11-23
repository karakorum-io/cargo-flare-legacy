<?php

/**************************************************************************************************

 * FdMailer class

 *

 * Client:		FreightDragon

 * Version:		1.0

 * Date:		2011-12-16																																		*

 * Author:		C.A.W., Inc. dba INTECHCENTER																											*

 * Address:		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076																	*

 * E-mail:		techsupport@intechcenter.com																											*

 * CopyRight 2011 FreightDragon. - All Rights Reserved																								*

 ***************************************************************************************************/

require_once(ROOT_PATH.'config.php');

require_once(ROOT_PATH.'libs/phpmailer/class.phpmailer.php');



class FdMailer extends PHPMailer {

	//public $SMTPDebug = 1;

	public function __construct($exception = false) {

		parent::__construct($exception);

		$this->isSMTP();

		$this->CharSet = 'utf-8';

		$this->Host = $GLOBALS['CONF']['SMTPSERVER'];

		$this->SMTPAuth = $GLOBALS['CONF']['SMTPAUTH'];

		if ($this->SMTPAuth) {

			$this->Username = $GLOBALS['CONF']['SMTPUSER'];

			$this->Password = $GLOBALS['CONF']['SMTPPWD'];
             //$this->Port          = 587;
		}
		
		
		



	}



	public function Send() {

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
			
			
			
			/*********  Changes for member smtp server settings *********/
			if($settings->smtp_user_approved==1)
			{
				
				if(trim($settings->smtp_server_name)!="" && trim($settings->smtp_user_name)!="" && trim($settings->smtp_user_password)!="" )
				{
					
					$this->Host = $settings->smtp_server_name;		
					//$this->SMTPAuth = $GLOBALS['CONF']['SMTPAUTH'];
					if(trim($settings->smtp_server_port) != "")
					   $this->Port          = $settings->smtp_server_port;	
					   
					 
					if($settings->smtp_from_email!=''){ 
					   //$this->setFrom($settings->smtp_from_email, 'Mailer'); 
					   //$this->AddReplyTo($settings->smtp_from_email, ''); 
					}
					   
					if($settings->smtp_use_ssl==1) 
					   $this->SMTPSecure = 'tls';
					   
					if ($this->SMTPAuth) {			
					  $this->Username = $settings->smtp_user_name;			
					  $this->Password = $settings->smtp_user_password;
					  
					 //$this->Port          = 25;
					}
				
	              //print $this->From ."-----".$this->Host."-".$this->Port."-".$this->SMTPSecure."-".$this->Username."-".$this->Password;
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

				"Body" => $this->Body

			), true);



			file_put_contents(ROOT_PATH."email.log", $log, FILE_APPEND);

		}
		
		
		//print $this->Host." | ".$this->Username." | " .$this->Password." | ".$this->Port;
		
		return parent::Send();

	}
	
	
public function SendToCD($mailData=array(),$type=0) {

		$this->Host = $GLOBALS['CONF']['CDSMTPSERVER'];

		$this->SMTPAuth = $GLOBALS['CONF']['CDSMTPAUTH'];

		if ($this->SMTPAuth) {

			$this->Username = $GLOBALS['CONF']['CDSMTPUSER'];

			$this->Password = $GLOBALS['CONF']['CDSMTPPWD'];
            $this->Port          = 25;
		}
		//print "<br><br>".$this->Host." | ".$this->Username." | " .$this->Password." | ".$this->Port."<br><br>";
		
		//$GLOBALS['daffny']->DB->insert('app_mail_sent', $mailData);
		
		return parent::Send();

	}
	
	
	public function SendFromCron($member_id=0) {

		if (isset($member_id) && $member_id >0) {

			$member = new Member($GLOBALS['daffny']->DB);

			$member->load($member_id);

			$settings = $member->getDefaultSettings();

			$bccs = explode(",", $settings->email_blind);

			foreach ($bccs as $bcc) {

				if (trim($bcc) != "" && preg_match("/^([-a-zA-Z0-9._]+@[-a-zA-Z0-9.]+(\.[-a-zA-Z0-9]+)+)*$/", trim($bcc))) {

					$this->AddBCC(trim($bcc));

				}

			}
			
			/*********  Changes for member smtp server settings *********/
			if($settings->smtp_user_approved==1)
			{
				
				if(trim($settings->smtp_server_name)!="" && trim($settings->smtp_user_name)!="" && trim($settings->smtp_user_password)!="" )
				{
					
					$this->Host = $settings->smtp_server_name;		
					//$this->SMTPAuth = $GLOBALS['CONF']['SMTPAUTH'];
					if(trim($settings->smtp_server_port) != "")
					   $this->Port          = $settings->smtp_server_port;	
					   
					 
					if($settings->smtp_from_email!=''){ 
					   //$this->setFrom($settings->smtp_from_email, 'Mailer'); 
					   //$this->AddReplyTo($settings->smtp_from_email, ''); 
					}
					   
					if($settings->smtp_use_ssl==1) 
					   $this->SMTPSecure = 'tls';
					   
					if ($this->SMTPAuth) {			
					  $this->Username = $settings->smtp_user_name;			
					  $this->Password = $settings->smtp_user_password;
					  
					 //$this->Port          = 25;
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

				"Body" => $this->Body

			), true);



			file_put_contents(ROOT_PATH."email.log", $log, FILE_APPEND);

		}
		
		
		//print "<br>".$this->From ."-----".$this->Host."-".$this->Port."-".$this->SMTPSecure."-".$this->Username."-".$this->Password."<br><br>";
		
		return parent::Send();

	}

}