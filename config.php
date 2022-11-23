<?php

define('SITE_IN', "https://cargoflare.com/");

$CONF = array();

$CONF['use_db_conf'] = true;

// database configs
$CONF['my_host'] = "localhost";
$CONF['my_user'] = "cargo_flare";
$CONF['my_pass'] = "+QXr[Ovg%5Qe_zx2QB";
$CONF['my_base'] = "cargo_flare_2";
$CONF['my_pref'] = "";
$CONF['auth_type'] = "email";

// check database connection
$conn = new mysqli($CONF['my_host'], $CONF['my_user'], $CONF['my_pass'],$CONF['my_base']);

if ($conn -> connect_errno) {
  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
  exit();
}

$CONF['CDSMTPAUTH'] = true;
$CONF['CDSMTPUSER'] =  'central@cargoflare.com';
$CONF['CDSMTPPWD'] =  '.sc~#Kn]*@}7';
$CONF['CDSMTPSERVER'] =  'mail.cargoflare.com';

$CONF['SMTPAUTH'] = true;
$CONF['SMTPUSER'] = "noreply@cargoflare.com";
$CONF['SMTPPWD'] = "5Xy-#XCT*ZgP";
$CONF['SMTPSERVER'] = "mail.cargoflare.com";
$CONF['SMTPPORT'] = 465;
$CONF['SMTPSecure'] = 'ssl';

$CONF['MAIL_HOST'] = 'americancartransporters-com.mail.protection.outlook.com';
$CONF['MAIL_PORT'] = '25';
$CONF['MAIL_AUTH'] = false;

$CONF['MAX_AUTO_QUOTE'] = 5;

$CONF['google_map_key'] = '111';
$CONF['debug'] = true;
$CONF['files'] = "uploads/files/";

$CONF['paypal_environment'] = "sandbox";
$CONF['anet_sandbox'] = false;

$CONF['security_salt'] = "sometext";

$CONF['cPanel_usr'] = 'cargo';
$CONF['cPanel_pwd'] = 'HMKYUS!$E!&CQPH9';
$CONF['cPanel_id'] = '66.175.236.177';

$CONF['MAILPWD'] =  'p@ssw0rdForAll01*';
$CONF['MAILSERVER'] =  'mail.cargoflare.com';
$CONF['MAILSTRING'] =  '{localhost:993/imap/ssl/novalidate-cert}INBOX';
$CONF['MAILDOMAIN'] =  'cargoflare.com';

$CONF['DES_ENCRYPT'] = false;
