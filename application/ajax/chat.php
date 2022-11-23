<?php

/**
 * ajax.php Ajax handler file for handling all ajax related operations
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

                    $phone = $_POST['phone'];

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

                    $data = '<span class="sms-load"><!--img src="https://cargoflare.com/images/chat-loader.gif"--></span>';

                    $sql = "select  *,date_format(SmsDate,'%m/%d/%Y') as SDate,date_format(SmsDate,'%h:%i %p') as STime from app_sms_logs WHERE FromPhone like '%" . $phone . "' or ToPhone like '%" . $phone . "' and status=1 order by SmsDate desc";
                    $result = $daffny->DB->query($sql);

                    if ($daffny->DB->num_rows() > 0) {
                        $tempDate = '';
                        $currentDate = '';
                        while ($row = $daffny->DB->fetch_row($result)) {

                            $currentDate = $row['SDate'];
                            if ($tempDate != $currentDate) {
                                $data .= '<div class="messageDateSeprate">---------- ' . $row['SDate'] . ' ----------</div>';
                            }

                            $smsboxmessageClass = 'sky';
                            $sendingtimeClass = 'right';
                            if ($row['send_recieve'] == 0) {
                                $smsboxmessageClass = 'white';
                                $sendingtimeClass = 'left';
                            }
                            $data .= '<div class="smscontainer"><div class="smsboxmessage ' . $smsboxmessageClass . '"><span class="smsboxmessagecontent">' . $row['Message'] . '</span></div><div class="sendingtime' . $sendingtimeClass . '">' . $row['STime'] . '</div></div>';

                            $tempDate = $currentDate;
                        }
                    }

                    $out = array('success' => true, 'data' => $data, 'info' => $info);

                    break;
                case 'sendSMS':
                    $FromPhone = $_SESSION['phoneSMS']; //"13309385668";
                    $ToPhone = $_POST['phone']; //'19546681277';

                    $strUrl = "?action=getSMSResponse&fromPhone=" . $FromPhone . "&toPhone=" . $ToPhone . "&rentity_id=" . $_POST['entity_id'] . "&rapp_sms=" . urlencode($_POST['app_sms']);
                    $getdata = file_get_contents('https://cargoflare.com/sms/sms_response.php' . $strUrl);
                    $response = json_decode($getdata);
                    $data = array();

                    if (empty($response->error)) {
                        $sql = "INSERT INTO app_sms_logs (owner_id,member_id ,FromPhone,ToPhone,Message,entity_id,status,send_recieve,response) values ('" . getParentId() . "','" . $_SESSION['member_id'] . "','" . $FromPhone . "','" . $ToPhone . "','" . $_POST['app_sms'] . "','" . $_POST['entity_id'] . "','1','0','" . $getdata . "')";
                        $result = $daffny->DB->query($sql);
                        $data = '<span class="sms-load"><!--img src="https://cargoflare.com/images/chat-loader.gif"--></span>';
                        $sql = "select  *,date_format(SmsDate,'%m/%d/%Y') as SDate,date_format(SmsDate,'%h:%i %p') as STime from app_sms_logs WHERE FromPhone like '%" . $ToPhone . "' or ToPhone like '%" . $ToPhone . "' and status=1 order by SmsDate desc";
                        $result = $daffny->DB->query($sql);

                        if ($daffny->DB->num_rows() > 0) {
                            $tempDate = '';
                            $currentDate = '';
                            while ($row = $daffny->DB->fetch_row($result)) {

                                $currentDate = $row['SDate'];
                                if ($tempDate != $currentDate) {
                                    $data .= '<div class="messageDateSeprate">---------- ' . $row['SDate'] . ' ----------</div>';
                                }

                                $smsboxmessageClass = 'sky';
                                $sendingtimeClass = 'right';
                                if ($row['send_recieve'] == 0) {
                                    $smsboxmessageClass = 'white';
                                    $sendingtimeClass = 'left';
                                }
                                $data .= '<div class="smscontainer"><div class="smsboxmessage ' . $smsboxmessageClass . '"><span class="smsboxmessagecontent">' . $row['Message'] . '</span></div><div class="sendingtime' . $sendingtimeClass . '">' . $row['STime'] . '</div></div>';

                                $tempDate = $currentDate;
                            }
                        }
                        $out = array('success' => true, 'data' => $data);
                    } else {
                        $sql = "INSERT INTO app_sms_logs (FromPhone,ToPhone,Message,entity_id,status,send_recieve,response)values
								('" . $FromPhone . "','" . $_POST['phone'] . "','" . $_POST['app_sms'] . "','" . $_POST['entity_id'] . "','0','0','" . $getdata . "')";
                        $result = $daffny->DB->query($sql);

                        $out = array('success' => false, 'data' => $response->error);
                    }

                    break;
                case 'sendChatSMS':

                    $from = $_SESSION['member']['contactname'];
                    $from_id = $_SESSION['member']['id'];
                    $toUserName = $_POST['toUserName'];
                    $toUserid = $_POST['toUserid'];
                    $message = $_POST['app_chat'];
                    if ($toUserid != 0 && $toUserid != '') {
                        $data = '';
                        $data_arr = array();

                        $now = date('Y-m-d H:i:s', time());

                        $data_arr['from'] = $from;
                        $data_arr['to'] = $toUserName;
                        $data_arr['from_id'] = $from_id;
                        $data_arr['to_id'] = $toUserid;
                        $data_arr['message'] = $message;
                        $data_arr['sent'] = $now;
                        $data_arr['view'] = 1;
                        $data_arr['notification'] = 1;

                        $last_insert_id = $daffny->DB->insert("chat", $data_arr);

                        $limit = 0;

                        $data .= '';
                        $sql = "SELECT *,date_format(sent,'%m/%d/%Y') as SDate,date_format(sent,'%h:%i %p') as STime  FROM chat WHERE (`to_id` ='" . $toUserid . "' AND `from_id` ='" . $from_id . "') OR (`from_id` ='" . $toUserid . "' AND `to_id` ='" . $from_id . "') ORDER BY id  LIMIT " . $limit . ",20";
                        $result = $daffny->DB->query($sql);

                        if ($daffny->DB->num_rows() > 0) {

                            $tempDate = '';
                            $currentDate = '';
                            while ($row = $daffny->DB->fetch_row($result)) {

                                $currentDate = $row['SDate'];
                                if ($tempDate != $currentDate) {
                                    $data .= '<div class="messageDateSeprateChat">------- ' . $row['SDate'] . ' -------</div>';
                                }

                                $smsboxmessageClass = 'sky';
                                $sendingtimeClass = 'right';
                                if ($row['from_id'] == $toUserid) {
                                    $smsboxmessageClass = 'white';
                                    $sendingtimeClass = 'left';
                                }
                                $data .= '<div class="chatcontainer"><div class="chatboxmessage ' . $smsboxmessageClass . '"><span class="chatboxmessagecontent">' . $row['message'] . '</span></div><div class="sendingtime' . $sendingtimeClass . '">' . $row['STime'] . '</div></div>';

                                $tempDate = $currentDate;
                            }
                        }
                    } else {
                        $data = "Please select user.";
                    }
                    $out = array('success' => true, 'data' => $data);

                    break;

                case 'getChatUserData':
                    $my_id = $_SESSION['member']['id'];
                    $postUserId = $_POST['id'];
                    $info = '';

                    $postUserName = $_POST['name'];

                    if ($postUserId != "") {
                        $_SESSION['chatnew']['to_id'] = $postUserId;
                        $_SESSION['chatnew']['to_name'] = $postUserName;
                    } elseif ($_SESSION['chatnew']['to_id'] != "") {
                        $postUserId = $_SESSION['chatnew']['to_id'];
                        $postUserName = $_SESSION['chatnew']['to_name'];
                    }

                    if (isset($_POST['changechatnumber']) && $_POST['changechatnumber'] == 1) {
                        $daffny->DB->update("chat", array("view" => "0", "notification" => "0"), "from_id = '" . $postUserId . "' AND  view=1");
                    }

                    $numOfChat = array();

                    $sql = "SELECT count(*) as num_chat,from_id  FROM (SELECT * FROM chat WHERE   view=1 and to_id='" . $my_id . "') AS chat group by from_id"; 
                    $result = $daffny->DB->query($sql);

                    if ($daffny->DB->num_rows() > 0) {
                        while ($row = $daffny->DB->fetch_row($result)) {
                            $phoneTem = $row['from_id'];
                            $numOfChat[$phoneTem] = $row['num_chat'];
                        }
                    }
                    $info = '<table cellspacing="2" cellpadding="0" border="0" width="100%" bgcolor="#f4f4f4">';
                    $idsUser = array();

                    $sql = "SELECT m.*, DATE_FORMAT(m.reg_date, '%m/%d/%Y %H:%i:%s') reg_date_show FROM members m WHERE is_deleted = 0 AND m.parent_id='" . getParentID() . "' AND status = 'Active'  ORDER by contactname ASC";
                    
                    $result = $daffny->DB->query($sql);

                    if ($daffny->DB->num_rows() > 0) {
                        $onlineUsers = '';
                        $offlineUsers = '';
                        while ($row = $daffny->DB->fetch_row($result)) {

                            $idsUser[] = $row['id'];

                            $UserId = $row['id'];

                            if ($UserId != $my_id) {
                                $strNum = '';
                                $strOnline = '';
                                if (array_key_exists($UserId, $numOfChat)) {
                                    $strNum = '&nbsp;<span class="blink_me"><font color="red"><b>(</b></font>' . $numOfChat[$UserId] . '<font color="red"><b>)</b></font></span>';
                                }

                                if ($row['online_status'] == '1') {

                                    $strOnline = '<div class="online-status-icon onlinedot">&nbsp;</div>';
                                } else {
                                    $strOnline = '<div class="online-status-icon offlinedot">&nbsp;</div>';
                                }

                                $strStyleTD = "";
                                if ($postUserId == $UserId) {
                                    $strStyleTD = ' style="background-color:#0066CC; color:#ffffff;cursor: pointer;line-height: 20px;"';
                                } else {
                                    $strStyleTD = ' style="cursor: pointer;line-height: 20px;"';
                                }

                                if ($row['online_status'] == '1') {
                                    $onlineUsers .= '<tr>
												   <td  align="left">' . $strOnline . '</td>
												   <td  align="left"  onclick="changeChatNew(\'' . $UserId . '\',\'' . $row['contactname'] . '\');" id="td-' . $UserId . '" ' . $strStyleTD . '><b>' . $row['contactname'] . "</b>" . $strNum . '</td>
												</tr>';

                                } else {
                                    $offlineUsers .= '<tr>
												   <td  align="left">' . $strOnline . '</td>
												   <td  align="left"  onclick="changeChatNew(\'' . $UserId . '\',\'' . $row['contactname'] . '\');" id="td-' . $UserId . '" ' . $strStyleTD . '><b>' . $row['contactname'] . "</b>" . $strNum . '</td>
												</tr>';
                                }
                            }

                        }
                        if ($offlineUsers != '') {
                            $divider = '<tr>
												   <td  align="left" colspan="2"><div class="chatboxdivider">-- OFFLINE USERS --</div></td></tr>';
                        }

                        $info .= $onlineUsers . $divider . $offlineUsers;
                    }
                    $info .= '</table> ';

                    if (isset($postUserId) && $postUserId != '') {

                        $limit = 0;
                        $sql = "SELECT *,date_format(sent,'%m/%d/%Y') as SDate,date_format(sent,'%h:%i %p') as STime  FROM (SELECT * FROM chat WHERE ((`to_id` ='" . $my_id . "' AND `from_id` ='" . $postUserId . "') OR (`from_id` ='" . $my_id . "' AND `to_id` ='" . $postUserId . "')) ORDER BY id DESC LIMIT " . $limit . ",20) AS chat ORDER by id";
                        
                        $data .= '';
                        $result = $daffny->DB->query($sql);

                        if ($daffny->DB->num_rows() > 0) {

                            $tempDate = '';
                            $currentDate = '';
                            while ($row = $daffny->DB->fetch_row($result)) {

                                $currentDate = $row['SDate'];
                                if ($tempDate != $currentDate) {
                                    $data .= '<div class="messageDateSeprateChat">------- ' . $row['SDate'] . ' -------</div>';
                                }

                                $smsboxmessageClass = 'sky';
                                $sendingtimeClass = 'right';
                                if ($row['from_id'] == $postUserId) {
                                    $smsboxmessageClass = 'white';
                                    $sendingtimeClass = 'left';
                                }
                                $data .= '<div class="chatcontainer"><div class="chatboxmessage ' . $smsboxmessageClass . '"><span class="chatboxmessagecontent">' . $row['message'] . '</span></div><div class="sendingtime' . $sendingtimeClass . '">' . $row['STime'] . '</div></div>';

                                $tempDate = $currentDate;
                            }
                        }
                    } else {
                        $data = "Please select user.";
                    }

                    $out = array('success' => true, 'data' => $data, 'info' => $info, 'name' => $postUserName);

                    break;
                case 'closeChatNotification':
                    $daffny->DB->update("chat", array("notification" => "0"), " id=" . $_POST['id']);
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
