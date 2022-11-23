<?php

require_once(ROOT_PATH . "libs/xmlapi.php");

/**
 * Class to handle server related operations like handling cPanel functionality and data
 * 
 * @author Chetu Inc.
 * @version 1.0
 */

class ServerHandler extends FdObject {
      
    const DEFAULT_EMAIL_PASSWORD = "Chetu2018?#Freightdragon";
    const TEST_LOG_FILE = "classes/log/server.handler.test.log";
    const CRONJOB_FILE = "get_leads_";
    const LEAD_CRON_FILE_BASE_PATH = "../cronjobs/";
    const EMAIL_QUOTA = 10;
    const cPANEL_PORT = 2083;
    const cPANEL_RESPONSE_TYPE = "json";
    
    private $server_user;
    private $server_password;
    private $server_domain;
    private $mail_domain;
    
    /**
     * Default constructor of the class processing connection parameters for cPanel
     * @param Strging $cpanel_user
     * @param String $cpanel_password
     * @param String $domain
     */
    public function __construct( $cpanel_user,  $cpanel_password, $domain, $email_domain ) {
        parent::__construct($param);
        $this->server_user = $cpanel_user;
        $this->server_password = $cpanel_password;
        $this->server_domain = $domain;
        $this->mail_domain = $email_domain;        
    }
    
    /**
     * Function to create a new email account on the hosting
     * 
     * @param String $email
     * @param String $password
     */
    public function add_email_account($email, $password=self::DEFAULT_EMAIL_PASSWORD){
        
        $this->server_user;
        $this->server_password;
        $this->server_domain;
        $this->mail_domain;
        
        $email = explode( "@" , $email );        
        $email_user = $email[0];
        $email_password = self::DEFAULT_EMAIL_PASSWORD;
        $email_domain = $this->mail_domain;
        
        $xmlapi = new xmlapi($this->server_domain);
        $xmlapi->password_auth($this->server_user,$this->server_password);
        $xmlapi->set_port(self::cPANEL_PORT); 
        $xmlapi->set_output(self::cPANEL_RESPONSE_TYPE);
        $xmlapi->set_debug(1);
        
        $response = $xmlapi->api1_query(
                $this->server_user,
                "Email",
                "addpop",
                array(
                    $email_user,
                    $email_password,
                    self::EMAIL_QUOTA,
                    $email_domain
                )
        );
        
        $response = json_decode($response);
        
        if($response->event->result == "1"){
            return 1;
        } else {
            return $response->event->reason;
        }     
    }
    
    /**
     * Function to create a Executable CRONJOB file for processing leads from email accounts
     * 
     * @param String $id
     * @param String $code
     */
    public function create_cronjob( $id, $lead_id, $code ){
        
        $location = self::LEAD_CRON_FILE_BASE_PATH;
        $file = self::CRONJOB_FILE.$lead_id.".php";
        $path = $location.$file;
        
        // creating parent id folder
        // if (!file_exists($location)) {
        //     mkdir($location, 0777, true);
        // }
        
        file_put_contents(
                $path,
                $code . PHP_EOL,
                FILE_APPEND | LOCK_EX
        );
        
        // configuring dynamically created cronjob
        $this->configure_cronjob( $id, $file );
    }
    
    /**
     * Function to configure CRON on cPanel
     * 
     * @return boolean
     */
    private function configure_cronjob($id,$file) {

        $xmlapi = new xmlapi($this->server_domain);
        $xmlapi->password_auth($this->server_user,$this->server_password);
        $xmlapi->set_port(self::cPANEL_PORT); 
        $xmlapi->set_output(self::cPANEL_RESPONSE_TYPE);
        $xmlapi->set_debug(1);
        
        $command = 'php /home/'.$this->server_user.'/public_html/cronjobs/'.$file.' >/dev/null 2>&1';
        
        $args = array(
            'command' => $command,
            'day' => '*',
            'hour' => '*',
            'minute' => '*',
            'month' => '*',
            'weekday' => '*',
        );
        
        $xmlapi->api2_query($this->server_user, 'Cron', 'add_line', $args);
    }

    /**
     * Functionality to delete CRON on cPanel
     * 
     * @return boolean
     */
    public function delete_cronjob($id){
        $cron_id = $this->get_cron_id($id);
        $args = array (
            'line' => $cron_id 
        );
        $xmlapi = new xmlapi($this->server_domain);
        $xmlapi->password_auth($this->server_user,$this->server_password);
        $xmlapi->set_port(self::cPANEL_PORT); 
        $xmlapi->set_output(self::cPANEL_RESPONSE_TYPE);
        $xmlapi->set_debug(1);
        $xmlapi->api2_query($this->server_user, 'Cron','remove_line', $args);        
    }
    
    /**
     * Functionality to get CRON id from server
     * 
     * @return Int
     */
    private function get_cron_id($id){
        $file = "get_leads_".$id.".php";
        $xmlapi = new xmlapi($this->server_domain);
        $xmlapi->password_auth($this->server_user,$this->server_password);
        $xmlapi->set_port(self::cPANEL_PORT); 
        $xmlapi->set_output(self::cPANEL_RESPONSE_TYPE);
        $xmlapi->set_debug(1);
        
        // getting all cronjob listing to get id of specific cronjob
        $cronxml = $xmlapi->api2_query($this->server_user, 'Cron','listcron');
        
        $array = json_decode($cronxml);        
        $command = 'php /home/'.$this->server_user.'/public_html/cronjobs/'.$file.' >/dev/null 2>&1';
        
        foreach ($array->cpanelresult->data as $value) {
            $value->command;
            if($value->command == $command){
                return $value->count;
            }
        }
    }
    
    /**
     * Functionality to delete email account from server
     */
    public function delete_email($email) {
        
        $email = explode("@",$email);
        $email_domain = $email[1];
        $email_account = $email[0];
                
        $args = array(
            'domain' => $email_domain,
            'email' => $email_account
        );

        $xmlapi = new xmlapi($this->server_domain);
        $xmlapi->password_auth($this->server_user,$this->server_password);
        $xmlapi->set_port(self::cPANEL_PORT); 
        $xmlapi->set_output(self::cPANEL_RESPONSE_TYPE);
        $xmlapi->set_debug(1);        
        $xmlapi->api2_query($this->server_user, "Email", "delpop", $args);
    }

    /**
     * Function to create a test log file for testing purpose nothing to do with the functionality
     * 
     * @param type $message
     * @return void
     */
    private function server_handler_test_log($message){
        file_put_contents(
                self::TEST_LOG_FILE, 
                $message . PHP_EOL,
                FILE_APPEND | LOCK_EX
        );
    }
    
}