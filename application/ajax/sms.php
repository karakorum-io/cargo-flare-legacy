<?php

/**
 * Ajax handler file for handling all ajax related operations
 * from CargoFlare.com
 *
 * @author Shahrukh
 * @copyright CargoFlare
 */

/* @var Daffny $daffny */
require_once "init.php";
$memberId = (int) $_SESSION['member_id'];
$out = array('success' => false);
//ob_start();
if ($memberId > 0) {
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'reassignSmsUser':
                    $reassignId = $_POST['reassignId'];
                    $user_id = $_POST['member_id'];
                    $update_arr = array(
                        'user_id' => $user_id,

                    );
                    $smsUsers = new SmsUsers($daffny->DB);
                    $smsUsers->load($reassignId);
                    $smsUsers->update($update_arr);

                    $out = array('success' => true);

                    break;
                case 'getSMSData':
                    $phone = $_POST['phone']; //'19546681277';

                    if (!isset($phone)) {
                        throw new RuntimeException("Invalid Phone number");
                    }

                    $entity = new Entity($daffny->DB);
                    $entity->load((int) $_POST['entity_id']);
                    $shipper = $entity->getShipper();

                    $info = '<table cellspacing="2" cellpadding="0" border="0" width="100%">
								<tr>
								<td  align="left">Contact Name</td>
								<td  align="center">:</td>
								<td  align="left">' . $shipper->fname . ' ' . $shipper->lname . ' (<font color="#FF0000">' . $_POST['powner'] . '</font>)</td>
								</tr>
								<tr>
								<td  align="left">Company Name</td>
								<td  align="center">:</td>
								<td  align="left"><b>' . $shipper->company . '</b></td>
								</tr>
								<tr>
								<td  align="left">Phone Number</td>
								<td  align="center">:</td>
								<td  align="left"><b>' . $shipper->phone1 . '</b></td>
								</tr>
							</table> ';

                    $data = '<span class="sms-load"></span>';
                    //FromPhone,ToPhone,Message,entity_id,status,send_recieve,response
                    $sql = "select  *,date_format(SmsDate,'%m/%d/%Y') as SDate,date_format(SmsDate,'%h:%i %p') as STime from app_sms_logs WHERE FromPhone like '%" . $phone . "' or ToPhone like '%" . $phone . "' and status=1 order by SmsDate desc";
                    $result = $daffny->DB->query($sql);

                    if ($daffny->DB->num_rows() > 0) {
                        $tempDate = '';
                        $currentDate = '';
                        while ($row = $daffny->DB->fetch_row($result)) {

                            $currentDate = $row['SDate'];
                            if ($tempDate != $currentDate) {
                                $data .= '<div class="messageDateSeprateSms">---------- ' . $row['SDate'] . ' ----------</div>';
                            }

                            $smsboxmessageClass = 'sky';
                            $sendingtimeClass = 'right';
                            if ($row['send_recieve'] == 0) {
                                $smsboxmessageClass = 'white';
                                $sendingtimeClass = 'left';
                            }
                            $data .= '<div class="smscontainer"><div class="smsboxmessage ' . $smsboxmessageClass . '"><span class="smsboxmessagecontent">' . sanitize($row['Message']) . '</span></div><div class="sendingtime' . $sendingtimeClass . '">' . $row['STime'] . '</div></div>';

                            $tempDate = $currentDate;
                        }
                    }
                    $out = array('success' => true, 'data' => $data, 'info' => $info);

                    break;
                case 'sendSMS':
                    $FromPhone = $_SESSION['phoneSMS']; //"13309385668";
                    $ToPhone = $_POST['phone']; //'19546681277';

                    $strUrl = "?action=getSMSResponse&fromPhone=" . $FromPhone . "&toPhone=" . $ToPhone . "&rentity_id=" . $_POST['entity_id'] . "&rapp_sms=" . urlencode($_POST['app_sms']);
                    $getdata = file_get_contents('https://freightdragondb.com/sms/sms_response.php' . $strUrl);
                    $response = json_decode($getdata);
                    $data = array();

                    if (empty($response->error)) {
                        $sql = "INSERT INTO app_sms_logs (owner_id,member_id ,FromPhone,ToPhone,Message,entity_id,status,send_recieve,response)values
								('" . getParentId() . "','" . $_SESSION['member_id'] . "','" . $FromPhone . "','" . $ToPhone . "','" . mysqli_real_escape_string($daffny->DB->connection_id, $_POST['app_sms']) . "','" . $_POST['entity_id'] . "','1','0','" . $getdata . "')";
                        $result = $daffny->DB->query($sql);

                        $data = '<span class="sms-load"><!--img src="https://freightdragondb.com/images/chat-loader.gif"--></span>';
                        $sql = "select  *,date_format(SmsDate,'%m/%d/%Y') as SDate,date_format(SmsDate,'%h:%i %p') as STime from app_sms_logs WHERE FromPhone like '%" . $ToPhone . "' or ToPhone like '%" . $ToPhone . "' and status=1 order by SmsDate asc";
                        $result = $daffny->DB->query($sql);

                        if ($daffny->DB->num_rows() > 0) {
                            $tempDate = '';
                            $currentDate = '';
                            while ($row = $daffny->DB->fetch_row($result)) {

                                $currentDate = $row['SDate'];
                                if ($tempDate != $currentDate) {
                                    $data .= '<div class="messageDateSeprateSms">---------- ' . $row['SDate'] . ' ----------</div>';
                                }

                                $smsboxmessageClass = 'sky';
                                $sendingtimeClass = 'right';
                                if ($row['send_recieve'] == 0) {
                                    $smsboxmessageClass = 'white';
                                    $sendingtimeClass = 'left';
                                }
                                $data .= '<div class="smscontainer"><div class="smsboxmessage ' . $smsboxmessageClass . '"><span class="smsboxmessagecontent">' . sanitize($row['Message']) . '</span></div><div class="sendingtime' . $sendingtimeClass . '">' . $row['STime'] . '</div></div>';

                                $tempDate = $currentDate;
                            }
                        }

                        $out = array('success' => true, 'data' => $data);
                    } else {
                        $sql = "INSERT INTO app_sms_logs (owner_id,member_id ,FromPhone,ToPhone,Message,entity_id,status,send_recieve,response)values
								('" . $FromPhone . "','" . $_POST['phone'] . "','" . mysqli_real_escape_string($daffny->DB->connection_id, $_POST['app_sms']) . "','" . $_POST['entity_id'] . "','0','0','" . $getdata . "')";
                        $result = $daffny->DB->query($sql);

                        $out = array('success' => false, 'data' => $response->error);
                    }

                    break;
                case 'sendChatSMS':
                    $FromPhone = $_SESSION['phoneSMS']; //"13309385668";
                    $ToPhone = $_POST['phone']; //'19546681277';

                    $strUrl = "?action=getSMSResponse&fromPhone=" . $FromPhone . "&toPhone=" . $ToPhone . "&rentity_id=" . $_POST['entity_id'] . "&rapp_sms=" . urlencode($_POST['app_sms']);
                    $getdata = file_get_contents('https://freightdragondb.com/sms/sms_response.php' . $strUrl);

                    $response = json_decode($getdata);

                    $data = array();
                    $data_arr = array();
                    if (empty($response->error)) {
                        $sql = "INSERT INTO app_sms_logs (owner_id,member_id ,FromPhone,ToPhone,Message,entity_id,status,send_recieve,response)values
								('" . getParentId() . "','" . $_SESSION['member_id'] . "','" . $FromPhone . "','" . $ToPhone . "','" . mysqli_real_escape_string($daffny->DB->connection_id, $_POST['app_sms']) . "','" . $_POST['entity_id'] . "','1','0','" . $getdata . "')";
                        $result = $daffny->DB->query($sql);

                        $data = '<span class="sms-load"></span>';
                        $sql = "select  *,date_format(SmsDate,'%m/%d/%Y') as SDate,date_format(SmsDate,'%h:%i %p') as STime from app_sms_logs WHERE ((FromPhone like '%" . $FromPhone . "' and ToPhone like '%" . $ToPhone . "') or (ToPhone like '%" . $FromPhone . "' and  FromPhone like '%" . $ToPhone . "')) and status=1 and Message!='' order by SmsDate asc";
                        $result = $daffny->DB->query($sql);

                        if ($daffny->DB->num_rows() > 0) {
                            $tempDate = '';
                            $currentDate = '';
                            while ($row = $daffny->DB->fetch_row($result)) {

                                $currentDate = $row['SDate'];
                                if ($tempDate != $currentDate) {
                                    $data .= '<div class="messageDateSeprateSms">---------- ' . $row['SDate'] . ' ----------</div>';
                                }

                                $smsboxmessageClass = 'sky';
                                $sendingtimeClass = 'right';
                                if ($row['send_recieve'] == 0) {
                                    $smsboxmessageClass = 'white';
                                    $sendingtimeClass = 'left';
                                }
                                $data .= '<div class="smscontainer"><div class="smsboxmessage ' . $smsboxmessageClass . '"><span class="smsboxmessagecontent">' . sanitize($row['Message']) . '</span></div><div class="sendingtime' . $sendingtimeClass . '">' . $row['STime'] . '</div></div>';

                                $tempDate = $currentDate;
                            }
                        }
                        $out = array('success' => true, 'data' => $data);
                    } else {
                        $sql = "INSERT INTO app_sms_logs (owner_id,member_id ,FromPhone,ToPhone,Message,entity_id,status,send_recieve,response)values
								('" . getParentId() . "','" . $_SESSION['member_id'] . "','" . $FromPhone . "','" . $_POST['phone'] . "','" . mysqli_real_escape_string($daffny->DB->connection_id, $_POST['app_sms']) . "','" . $_POST['entity_id'] . "','0','0','" . $getdata . "')";
                        $result = $daffny->DB->query($sql);

                        $out = array('success' => false, 'data' => $response->error);
                    }

                    break;

                case 'getChatSMSData':

                    if ($_SESSION['phoneSMSflag'] == 1) {
                        $phone = $_POST['phone'];
                        $info = '';

                        if ($phone != "") {
                            $_SESSION['sms']['myphone_number'] = $phone;
                        } elseif ($_SESSION['sms']['myphone_number'] != "") {
                            $phone = $_SESSION['sms']['myphone_number'];
                        }

                        if (isset($_POST['changechatnumber']) && $_POST['changechatnumber'] == 2) {

                            if (!checkFirstChar($phone)) {
                                $phone = "1" . $phone;
                            }

                            $sql = "SELECT phone
								FROM app_sms_phone
								WHERE phone='$phone' and member_id = '" . $_SESSION['member_id'] . "' and status =1
								 ";
                            $result = $daffny->DB->query($sql);
                            if ($daffny->DB->num_rows() <= 0) {
                                $sql = "INSERT INTO app_sms_phone (member_id,owner_id,phone,tag_name)values
								('" . $_SESSION['member_id'] . "','" . getParentId() . "','" . $phone . "','" . $_POST['phoneSmsTag'] . "')";

                                $result = $daffny->DB->query($sql);

                            }
                        }

                        if (isset($_POST['changechatnumber']) && $_POST['changechatnumber'] == 1) {
                            $daffny->DB->update("app_sms_logs", array("view" => "0", "notification" => "0"), "FromPhone = '" . $phone . "' AND send_recieve=1 and view=1");
                        }

                        $numOfSms = array();
                        $sql = "SELECT count(*) as num_sms,FromPhone
								FROM app_sms_logs
								WHERE STATUS =1 and view=1 and send_recieve=1 and ToPhone='" . $_SESSION['phoneSMS'] . "' group by FromPhone
								 ";
                        $result = $daffny->DB->query($sql);

                        if ($daffny->DB->num_rows() > 0) {
                            while ($row = $daffny->DB->fetch_row($result)) {
                                $phoneTem = $row['FromPhone'];
                                $numOfSms[$phoneTem] = $row['num_sms'];
                            }
                        }

                        $info = '<table cellspacing="2" cellpadding="0" border="0" width="100%" bgcolor="#f4f4f4">';
                        $sql = "select phone as FromPhone,tag_name as FromName  from app_sms_phone where status=1 and member_id='" . $_SESSION['member_id'] . "' and owner_id='" . getParentId() . "'";
                        $result = $daffny->DB->query($sql);

                        if ($daffny->DB->num_rows() > 0) {
                            while ($row = $daffny->DB->fetch_row($result)) {
                                $FromPhone = $row['FromPhone'];
                                $FromPhoneShow = formatPhoneNew($row['FromPhone']);
                                $FromName = $row['FromName'];
                                if ($FromName == '') {
                                    $FromName = $FromPhoneShow;
                                }

                                $strNum = '';
                                $strOnline = '';
                                if (array_key_exists($FromPhone, $numOfSms)) {
                                    $strNum = '&nbsp;<font color="red"><b>(</b></font>' . $numOfSms[$FromPhone] . '<font color="red"><b>)</b></font>';
                                    $strOnline = '<div class="online-status-icon onlinedot">&nbsp;</div>';
                                } else {
                                    $strOnline = '<div class="online-status-icon offlinedot">&nbsp;</div>';
                                }

                                $strStyleTD = "";
                                if ($phone == $FromPhone) {
                                    $strStyleTD = ' style="background-color:#0066CC; color:#ffffff;"';
                                }

                                $info .= '<tr>
												   <td  align="left">' . $strOnline . '</td>
												   <td  align="left" title="' . $FromPhone . '" onclick="changeChat(\'' . $FromPhone . '\',1);" id="td-' . $FromPhone . '" ' . $strStyleTD . '><b title="' . $FromPhone . '">' . $FromName . "</b>" . $strNum . '</td>
												</tr>';
                            }
                        }
                        $info .= '</table> ';

                        if (isset($phone)) {

                            $data .= '<span class="chat-load"></span>';
                            $sql = "select  *,date_format(SmsDate,'%m/%d/%Y') as SDate,date_format(SmsDate,'%h:%i %p') as STime from app_sms_logs WHERE ((FromPhone like '%" . $_SESSION['phoneSMS'] . "' and ToPhone like '%" . $phone . "') or (ToPhone like '%" . $_SESSION['phoneSMS'] . "' and  FromPhone like '%" . $phone . "')) and status=1 and Message!='' order by SmsDate asc ";
                            $result = $daffny->DB->query($sql);

                            if ($daffny->DB->num_rows() > 0) {
                                $tempDate = '';
                                $currentDate = '';
                                while ($row = $daffny->DB->fetch_row($result)) {

                                    $currentDate = $row['SDate'];
                                    if ($tempDate != $currentDate) {
                                        $data .= '<div class="messageDateSeprateSms">---------- ' . $row['SDate'] . ' ----------</div>';
                                    }

                                    $smsboxmessageClass = 'sky';
                                    $sendingtimeClass = 'right';
                                    if ($row['send_recieve'] == 0) {
                                        $smsboxmessageClass = 'white';
                                        $sendingtimeClass = 'left';
                                    }
                                    $data .= '<div class="chatcontainer"><div class="chatboxmessage ' . $smsboxmessageClass . '"><span class="chatboxmessagecontent">' . sanitize($row['Message']) . '</span></div><div class="sendingtime' . $sendingtimeClass . '">' . $row['STime'] . '</div></div>';

                                    $tempDate = $currentDate;
                                }
                            }
                        } else {
                            $data = "Please select phone.";
                        }

                    } else {

                        $info = '<table cellspacing="2" cellpadding="0" border="0" width="100%" bgcolor="#f4f4f4">';
                        $info .= '<tr>
										<td  align="center" ><img src="https://www.freightdragon.com/images/icons/error.png" width="100" height="100"></td>
									   </tr>';
                        $info .= '</table> ';

                        $data .= '<b>You are not authorized to use this feature, please contact you administrator.</b>';

                    }

                    $out = array('success' => true, 'data' => $data, 'info' => $info);

                    break;
                case 'closeSMSNotification':
                    $daffny->DB->update("app_sms_logs", array("notification" => "0"), " id=" . $_POST['id']);
                    break;
                case 'getTagPhoneNumbers':

                    $sql = "SELECT id,phone,tag_name
						FROM app_sms_phone
						WHERE  member_id = '" . $_SESSION['member_id'] . "' and status =1  and owner_id='" . getParentId() . "'
						 ";
                    $result = $daffny->DB->query($sql);
                    if ($daffny->DB->num_rows() > 0) {
                        $info = '<table cellspacing="2" cellpadding="0" border="0" width="100%">
								   <tr>
								       <td  align="left" style="padding-left:15px;margin:5px;" bgcolor="#cccccc">Phone #</td>
									  <td  align="center" bgcolor="#cccccc">Tag Phone #</td>
									  <td  align="center" bgcolor="#cccccc">Action</td>
									</tr>
								 ';
                        while ($row = $daffny->DB->fetch_row($result)) {
                            $info .= '<tr>
									   <td  align="left" style="padding-left:15px;"><b>' . formatPhoneNew($row['phone']) . '</b></td>
									  <td  align="center"><input type="text" name="tag_name[' . $row['id'] . ']" value="' . $row['tag_name'] . '"></b></td>
									  <td  align="center"><font color="red"><b onclick="deleteManagePhone(' . $row['id'] . ')" style="cursor:pointer;">X</b></font></td>
									</tr>
								 ';
                        }
                    }

                    $info .= '</table>';
                    $out = array('success' => true, 'data' => $info);
                    break;
                case 'saveTagPhone':
                    $tagNameArr = $_POST['tag_name'];
                    $tagNameArrSize = sizeof($tagNameArr);
                    if (is_array($tagNameArr) && $tagNameArrSize > 0) {
                        foreach ($tagNameArr as $id => $tag) {
                            if (trim($tag) != '') {
                                $daffny->DB->update("app_sms_phone", array("tag_name" => $tag), "id = '" . $id . "' and status =1");
                            }
                        }
                    }
                    $out = array('success' => true);
                    break;
                case 'addManagePhone':
                    $tagName = $_POST['tag_name'];
                    $phone = str_replace("-", "", $_POST['phone']);
                    $info = "";

                    if (!checkFirstChar($phone)) {
                        $phone = "1" . $phone;
                    }

                    $sql = "SELECT phone
						FROM app_sms_phone
						WHERE phone='$phone' and member_id = '" . $_SESSION['member_id'] . "' and owner_id='" . getParentId() . "' and status =1
						 ";
                    $result = $daffny->DB->query($sql);
                    if ($daffny->DB->num_rows() <= 0) {

                        $sql = "INSERT INTO app_sms_phone (member_id,owner_id,phone,tag_name)values
						('" . $_SESSION['member_id'] . "','" . getParentId() . "','" . $phone . "','" . $tagName . "')";
                        $result = $daffny->DB->query($sql);
                        $info = "Phone number added.";
                    } else {
                        $info = "Phone number already exist.";
                    }

                    $out = array('success' => true, 'info' => $info);
                    break;
                case 'deleteManagePhone':
                    $daffny->DB->update("app_sms_phone", array("status" => "0"), " id=" . $_POST['id']);
                    $out = array('success' => true);
                    break;
                default:
                    break;
            }
        } elseif (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'getSMSResponse':
                    # Plivo AUTH ID
                    $AUTH_ID = 'MAZMU2NTAXNDKWM2Q1M2';
                    # Plivo AUTH TOKEN
                    $AUTH_TOKEN = 'NzFiOTM3MzM5YzQ5NDQ3MjEyNTk5Njc0N2NjOGJm';
                    # SMS sender ID.
                    $src = '13309385668';
                    # SMS destination number
                    $dst = '9189578510025';
                    # SMS text
                    $text = "Hi, Message from ' Plivo";
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
                    exit;
                    break;
                default:
                    break;
            }
        }
    } catch (FDException $e) {
        echo $e->getMessage();
    }
}
ob_clean();
echo $json->encode($out);
require_once "done.php";

function sanitize($text)
{
    $text = htmlspecialchars($text, ENT_QUOTES);
    $text = str_replace("\n\r", "\n", $text);
    $text = str_replace("\r\n", "\n", $text);
    $text = str_replace("\n", "<br>", $text);
    return $text;
}

function checkFirstChar($text)
{
    $firstChar = substr($text, 0, 1);
    if ($firstChar == "1") {
        return true;
    }

    return false;
}
