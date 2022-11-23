<?php

$mailbox = imap_open('{localhost:143/novalidate-cert}', 'stefanomadrigal3.freightdragondemoaccount@freightdragon.com', 'passwordforall01*');
var_dump($mailbox);
$check = imap_check($mailbox);
$qty_emails = $check->Recent;

echo $qty_emails;

imap_close($mailbox);


?>