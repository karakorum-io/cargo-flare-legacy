<?php

    /**
     * Including php mailer library
     */
    require '../../libs/phpmailer/class.phpmailer.php';

    /**
     * Class to send the email using the email settings in the config file at root
     * of web application
     * 
     * @author Chetu Inc.
     * @version 1.0
     */
    class WebMailer {

        const FROM_NAME = "RT_WAY";        
        const FROM_EMAIL = 'admin@americancartransporters.com';

        public $mail = array();

        /**
         * Constructor to load the libraries and other related classes at the time
         * of class initialisation
         */
        function __construct(){
            /**
             * Loading the configuration file
             */
            require_once '../../config.php';            
            $this->mail['host'] = $CONF['MAIL_HOST'];
            $this->mail['port'] = $CONF['MAIL_PORT'];
            $this->mail['auth'] = $CONF['MAIL_AUTH'];            
        }

        /**
         * This function is used to send the email without attachment
         * 
         * @return boolean
         * @author Chetu Inc.
         */
        function sendEmail($receiver,$mailBody,$subject){            
            $mail = new PHPMailer;            
            $mail->IsSMTP();
            $mail->Host = $this->mail['host'];
            $mail->Port = $this->mail['port'];
            $mail->SMTPAuth = $this->mail['auth'];
            $mail->FromName = self::FROM_NAME;
            $mail->SetFrom(self::FROM_EMAIL);
            $mail->AddAddress($receiver);
            $mail->IsHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $mailBody;
            try{
                $mail->Send();
                return true;
            } catch (Exception $e){
                return false;
            }
        }
    }
?>