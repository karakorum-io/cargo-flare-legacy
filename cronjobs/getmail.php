<?php
/*
    set_time_limit(0);
    require_once("../libs/phpmailer/class.phpmailer.php");
    $mailbox = imap_open("{mail.domain.com:993/imap/ssl/novalidate-cert}INBOX", $email, $pwd);
    $check = imap_check($mailbox);
    $qty_emails = $check->Recent;

    printt("Number of recent messages : " . $qty_emails . "<br />");
    for ($e = 1; $e <= $qty_emails; $e++) {
	try {
	    $daffny->DB->query("START TRANSACTION");
	    echo "Get email #" . $e . "<br />";
	    $header = imap_header($mailbox, $e);
	    $body = imap_body($mailbox, $e);
	    $subject = $header->subject;
	    $to = $header->toaddress;
	    $from = $header->fromaddress;
	    $body = imap_body($mailbox, $e);
	    $upd_arr = array();
	    $upd_arr["body"] = $body;
	    $entity->update($upd_arr);
	    //10. Delete email from server
	    imap_delete($mailbox, $e);
	    $daffny->DB->query("COMMIT");
	} catch (Exception $exc) {
	    echo $exc->getTraceAsString();
	    $daffny->DB->query("ROLLBACK");
	}
    }
    imap_expunge($mailbox);
    imap_close($mailbox);
*/

