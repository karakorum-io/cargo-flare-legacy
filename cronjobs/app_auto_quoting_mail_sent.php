<?php
/**
 * CronJob file to auto quote Imported leads in FreightDragon web application
 * from external AutoQuote API
 * 
 * @author Chetu Inc.
 * @version 1.0
 * @category CronJobs
 */
/**
 * Starting CronJob Session
 */
@session_start();

/**
 * including dependencies
 */
require_once("init.php");
require_once("../libs/phpmailer/class.phpmailer.php");
require_once ('../config.php');

ob_start();
ini_set('max_execution_time', 300);

$_SESSION['iamcron'] = true;

echo "<h3>CRON STARTED</h3>";
echo "Sent Auto Quote Email Cron: <b></b><br><br><br>";

$sql = "SELECT * FROM `app_auto_quoting_mails` WHERE `mail_status` = 0 ";
$result = $daffny->DB->query($sql);
$entities = []; 

while($row = mysqli_fetch_assoc($result)){
    /**
     * initializing classes
     */
    $entity = new Entity($daffny->DB);
    $emailTemplate = new EmailTemplate($daffny->DB);
    $entity->load($row['entity_id']);
    
    echo "Running for ".$entity->id."<br>";
    /**
     * Email dependent variables
     */
    $tpl = new template();
    $emailTemplate->setTemplateBuilder($tpl);
    $emailTemplate->loadTemplate(EmailTemplate::SYS_INIT_QUOTE, $entity->getAssigned()->parent_id, $entity, array(), true);

    if ($entity->getAssigned()->parent_id == 1){
        $fromName = $emailTemplate->getFromName();
        $from = $emailTemplate->getFromAddress();
    } else {
        $fromName = $emailTemplate->getFromName();
        $from = $entity->getAssigned()->getDefaultSettings()->smtp_from_email;            
    }       
    
    
    $to = $emailTemplate->getToAddress();
    $from = "admin@americancartransporters.com";        
    $subject = $emailTemplate->getSubject();
    $body = $emailTemplate->getBody();

    $response = sendInitQuoteEmail( $CONF['MAIL_HOST'], $CONF['MAIL_PORT'], $to, $from, $fromName, $subject, $body);
    $entities[] = $entity->id;   
    
}

$commaSeperated = implode(",",$entities);

$sql = "UPDATE `app_auto_quoting_mails` SET `mail_status` = 1  WHERE `entity_id` IN (".$commaSeperated.") ";
$result = $daffny->DB->query($sql);
die("CRON ENDED");

$_SESSION['iamcron'] = false;
require_once("done.php");

/**
 * Function to send email
 * 
 * @param $host Email host
 * @param $port Email port
 * @param $auth Email auth
 * @param $to Reciever email address
 * @param $from Sender email address
 * @param $fromName Sender Name
 * @param $subject Email subject
 * @param $body Email body
 * @author Chetu Inc.
 */
function sendInitQuoteEmail($host,$port,$to,$from,$fromname,$subject,$body){

    echo "MAIL FUNCTION IS SENDING EMAIL<br>";
    /**
     * Sending Email to shipper
     */
    $mail = new PHPMailer;            
    $mail->IsSMTP();
    $mail->Host = $host;
    $mail->Port = $port;
    $mail->SMTPAuth = false;    
    echo $mail->SetFrom("$from ", "".$fromname);
    echo $mail->AddAddress('update1@yopmail.com');
    $mail->IsHTML(true);
    echo $mail->Subject = $subject;
    echo $mail->Body = $body;
    $mail->Send();
   
}