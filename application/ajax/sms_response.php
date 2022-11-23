<?php

/**
 * Ajax handler file for handling all ajax related operations
 * from CargoFlare.com
 *
 * @author Shahrukh
 * @copyright CargoFlare
 */

/* @var Daffny $daffny */

$memberId = (int) $_SESSION['member_id'];

//ob_start();
if ($memberId > 0) {

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {

            case 'sendSMS':
                $strUrl = "?action=getSMSResponse&phone=" . $_POST['phone'] . "&entity_id=" . $_POST['entity_id'] . "&app_sms=" . $_POST['app_sms'];
                print 'https://freightdragondb.com/application/leads/sendSMS/' . $strUrl;
                print $getdata = file_get_contents('https://freightdragondb.com/application/leads/sendSMS/' . $strUrl);
                $response = json_decode($getdata);

                print_r($response);
                exit;
                $out = array(
                    'success' => true,
                );
                break;
            default:
                break;
        }
    } elseif (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'getSMSResponse':
                print "0000000000000000";
                # Plivo AUTH ID
                $AUTH_ID = 'MAZMU2NTAXNDKWM2Q1M2';
				# Plivo AUTH TOKEN
                $AUTH_TOKEN = 'NzFiOTM3MzM5YzQ5NDQ3MjEyNTk5Njc0N2NjOGJm';
				# SMS sender ID.
                $src = '13309385668';
				# SMS destination number
                $dst = '9189578510027';
				# SMS text
                $text = 'Hi, Message from Plivo';
                $url = 'https://api.plivo.com/v1/Account/' . $AUTH_ID . '/Message/';
                $data = array("src" => "$src", "dst" => "$dst", "text" => "$text");
                $data_string = json_encode($data);
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
                curl_setopt($ch, CURLOPT_USERPWD, $AUTH_ID . ":" . $AUTH_TOKEN);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                $response = curl_exec($ch);
                curl_close($ch);
                break;
            default:
                break;
        }
    }

}
exit;
