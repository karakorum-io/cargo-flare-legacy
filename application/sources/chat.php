<?php

require_once DAFFNY_PATH . "libs/upload.php";
require_once DAFFNY_PATH . "libs/cropper.php";

class ApplicationChat extends ApplicationAction
{
    public $title = "Chat";
    public $section = "Chat";

    public function construct()
    {

        if (!$this->check_access('status')) {
            $this->setFlashError('Access Denied.');
            redirect(getLink());
        }
        return parent::construct();
    }

    public function idx()
    {

    }

    /*
     * Function to get user details
     */
    public function getuserdetails()
    {
        $returnArray = array();
        $ids = $_GET['ids'];
        $sql = "SELECT * FROM members WHERE id IN ( " . $ids . " ) AND status = 'Active' ORDER by contactname ASC";
        $chatresults = $this->daffny->DB->selectRows($sql);
        foreach ($chatresults as $data) {
            $userId = $data['id'];
            $name = $data['contactname'];
            $username = str_replace(".", "-", (str_replace(" ", "_", str_replace("'", "", $name))));
            $onlinestatus = $data['online_status'];
            $returnArray[] = array('id' => $userId, 'name' => $name, 'chatname' => $username, 'onlinestatus' => $onlinestatus);
        }

        //Update DB status for off-line user
        $updateId = $_SESSION['member_id'];
        if ($updateId > 0) {
            $date = date("Y-m-d H:i:s");
            $currentDate = strtotime($date);
            $futureDate = $currentDate + (60 * 5);
            $online_heartbeat = date("Y-m-d H:i:s", $futureDate);
            $postdata = array('online_status' => '1', 'online_heartbeat' => $online_heartbeat);
            $done = $this->daffny->DB->update("members", $postdata, "id = '" . $updateId . "'");
        }
        echo JSON_encode($returnArray);
    }

    /*
     * Function to turn-off online_status
     */
    public function offlinePulse()
    {
        $date = date("Y-m-d H:i:s");
        $currentTime = strtotime($date);
        $ids = $_GET['ids'];
        $sql = "SELECT * FROM members WHERE id IN ( " . $ids . " )";
        $chatresults = $this->daffny->DB->selectRows($sql);
        foreach ($chatresults as $data) {
            $heartbeat_Time = strtotime($data['online_heartbeat']);
            if ($currentTime > $heartbeat_Time) {
                $date = date("Y-m-d H:i:s");
                $updateId = $data['id'];
                $postdata = array('online_status' => '0');
                $done = $this->daffny->DB->update("members", $postdata, "id = '" . $updateId . "'");
            }
        }
        echo "done";
        exit(0);
    }

    /*
     * Function for insert chat to DB
     */
    public function sendChat()
    {
        $from = $_SESSION['member']['contactname'];
        $from_id = $_SESSION['member']['id'];
        $to = $_GET['to'];
        $to_id = $_GET['userid'];
        $message = $_GET['message'];

        $array = array();
        $chatHistory[$to] = array();
        $openChatBoxes[$to] = array();
        $data_arr = array();

        if (!$_SESSION['openChatBoxes']) {
            $_SESSION['openChatBoxes'] = $array;
        }

        if (!$_SESSION['chatHistory']) {
            $_SESSION['chatHistory'] = $array;
        }

        $openChatBoxes[$to] = date('Y-m-d H:i:s', time());
        $_SESSION['openChatBoxes'] = $openChatBoxes;

        $messagesan = $this->sanitize($message);
        $time = date('h:i A', time());

        $chatHistory[$to] = '{"s":"1", "f": "' . $to . '", "m": "' . $messagesan . '", "t": "' . $time . '", "tid": "' . $to_id . '"}';
        $_SESSION['chatHistory'] = $chatHistory;

        unset($_SESSION['tsChatBoxes']);

        $now = date('Y-m-d H:i:s', time());

        $data_arr['from'] = $from;
        $data_arr['to'] = $to;
        $data_arr['from_id'] = $from_id;
        $data_arr['to_id'] = $to_id;
        $data_arr['message'] = $message;
        $data_arr['sent'] = $now;

        $last_insert_id = $this->daffny->DB->insert("chat", $data_arr);

        echo $time;
        exit(0);
    }

    public function startchatsession()
    {
        $this->tplname = "chat.startchatsession";

        $items = '';
        $data = array();

        if ($_SESSION['openChatBoxes']) {
            foreach ($_SESSION['openChatBoxes'] as $chatbox => $void) {
                $items .= $this->chatBoxSession($chatbox);
            }
        }

        if ($items != '') {
            $items = $items;
        }

        $data['username'] = $_SESSION['member']['contactname'];

        $this->daffny->tpl->data[] = $data;
    }

    /*
     * Function for chat-heart-beat: It will check if there is any new message by the user
     */
    public function chatheartbeat()
    {
        $this->tplname = "chat.chatheartbeat";
        $row = array();
        $row['username'] = '';
        $row['items'] = '';

        $numOfSms = 0;
        $sql = "SELECT COUNT( DISTINCT FromPhone ) AS num_sms FROM app_sms_logs WHERE view =1 and ToPhone='" . $_SESSION['phoneSMS'] . "'  ";
        $result = $this->daffny->DB->query($sql);

        if ($this->daffny->DB->num_rows() > 0) {
            while ($rowSms = $this->daffny->DB->fetch_row($result)) {
                $numOfSms = $rowSms['num_sms'];

            }
        }
        $row['sms'] = $numOfSms;

        $sql = "SELECT id,FromPhone,ToPhone,Message,status,notification,view,send_recieve FROM app_sms_logs WHERE view =1 and ToPhone='" . $_SESSION['phoneSMS'] . "' and notification=1 ";
        $result = $this->daffny->DB->query($sql);
        $smsData = array();
        if ($this->daffny->DB->num_rows() > 0) {
            while ($rowSms = $this->daffny->DB->fetch_row($result)) {

                $key = $rowSms['id'];
                $smsData[$key]['id'] = $rowSms['id'];
                $smsData[$key]['FromPhone'] = $rowSms['FromPhone'];
                $smsData[$key]['ToPhone'] = $rowSms['ToPhone'];
                $smsData[$key]['Message'] = $rowSms['Message'];
                $smsData[$key]['status'] = $rowSms['status'];
                $smsData[$key]['view'] = $rowSms['view'];
                $smsData[$key]['notification'] = $rowSms['notification'];
                $smsData[$key]['send_recieve'] = $rowSms['send_recieve'];

            }
        }

        $row['smsData'] = $smsData;

        $numOfChat = 0;
        $sql = "SELECT COUNT( DISTINCT from_id ) AS num_chat FROM chat WHERE VIEW =1 and to_id='" . $_SESSION['member']['id'] . "'  ";
        $result = $this->daffny->DB->query($sql);

        if ($this->daffny->DB->num_rows() > 0) {
            while ($rowChat = $this->daffny->DB->fetch_row($result)) {
                $numOfChat = $rowChat['num_chat'];

            }
        }
        $row['chat'] = $numOfChat;

        $sql = "SELECT * FROM chat WHERE VIEW =1 and notification=1 and to_id='" . $_SESSION['member']['id'] . "'  ";
        $result = $this->daffny->DB->query($sql);
        $chatData = array();
        if ($this->daffny->DB->num_rows() > 0) {
            while ($rowChat = $this->daffny->DB->fetch_row($result)) {

                $key = $rowChat['id'];
                $chatData[$key]['id'] = $rowChat['id'];
                $chatData[$key]['from'] = $rowChat['from'];
                $chatData[$key]['to'] = $rowChat['to'];
                $chatData[$key]['from_id'] = $rowChat['from_id'];
                $chatData[$key]['to_id'] = $rowChat['to_id'];
                $chatData[$key]['message'] = $rowChat['message'];
                $chatData[$key]['sent'] = $rowChat['sent'];
                $chatData[$key]['recd'] = $rowChat['recd'];
                $chatData[$key]['view'] = $rowChat['view'];
            }
        }
        $row['chatData'] = $chatData;

        $this->daffny->tpl->data[] = $row;
    }

    /*
     * Function for chat-history
     */
    public function chathistory()
    {
        $this->tplname = "chat.chatheartbeat";
        $row = array();
        $id = $_GET['id'];
        $limit = $_GET['limit'];
        $username = str_replace(".", "-", (str_replace(" ", "_", $_SESSION['member']['contactname'])));

        //Select query
        $my_id = $_SESSION['member']['id'];
        $sql = "SELECT * FROM (SELECT * FROM chat WHERE ((`to_id` ='" . $my_id . "' AND `from_id` ='" . $id . "') OR (`from_id` ='" . $my_id . "' AND `to_id` ='" . $id . "')) ORDER BY id DESC LIMIT " . $limit . ",10) AS chat ORDER by id ASC";

        $chatresults = $this->daffny->DB->selectRows($sql);

        $items = '';
        $chatBoxes = array();
        $totRes = count($chatresults);
        $counter = 1;

        foreach ($chatresults as $chat) {
            $sentdate = $chat['sent'];
            $sentdate = strtotime($sentdate);
            $time = date("h:i A", $sentdate);

            $date = $chat['sent'];
            $date = explode(" ", $date);
            $chatfrom = $chat['from'];
            $chatfromid = $chat['from_id'];
            $chattoid = $chat['to_id'];
            $chatfrom = $chat['from'];

            $chatmessage = $chat['message'];
            $openChatBoxes = $_SESSION['openChatBoxes'];
            $openChatBoxes[$chatfrom];

            $chatHistory = $_SESSION['chatHistory'];
            $chatHistory[$chatfrom];

            if (!isset($openChatBoxes[$chatfrom]) && isset($chatHistory[$chatfrom])) {
                $items = $chatHistory[$chatfrom];
            }
            $chatmessage = $this->sanitize($chatmessage);

            $items .= '{"s":"0", "f": "' . $chatfrom . '", "m": "' . $chatmessage . '", "t": "' . $time . '", "tid": "' . $chattoid . '", "date": "' . $date[0] . '"}';
            if ($counter < $totRes) {
                $items .= ",";
            }

            if (!isset($chatHistory[$chatfrom])) {
                $chatHistory[$chatfrom] = "";
            }

            $chatHistory[$chatfrom] .= '{"s":"0", "f": "' . $chatfrom . '", "m": "' . $chatmessage . '", "t": "' . $time . '", "tid": "' . $chattoid . '", "date": "' . $date[0] . '"}';

            $tsChatBoxes = $_SESSION['tsChatBoxes'];
            $tsChatBoxes[$chatfrom];
            unset($_SESSION[$tsChatBoxes[$chatfrom]]);

            $openChatBoxes[$chatfrom] = $chat['sent'];
            $counter++;
        }

        if ($_SESSION['openChatBoxes']) {
            foreach ($_SESSION['openChatBoxes'] as $chatbox => $time) {
                $tsChatBoxes = $_SESSION['tsChatBoxes'];
                if (!isset($tsChatBoxes[$chatbox])) {
                    $now = time() - strtotime($time);
                    $time = date('g:iA M dS', strtotime($time));
                    $message = "Sent at $time";
                    if ($now > 180 && $now < 182) {
                        $items .= '{"s":"2", "f": "' . $chatbox . '", "m": "' . $message . '"}';

                        $chatHistory = $_SESSION['chatHistory'];
                        if (!isset($chatHistory[$chatbox])) {
                            $chatHistory[$chatbox] = '';
                        }
                        $chatHistory[$chatbox] .= '{"s":"2", "f": "' . $chatbox . '", "m": "' . $message . '"}';
                        $tsChatBoxes[$chatbox] = 1;
                    }
                }
            }
        }

        if ($items != '') {
            $items = $items;
        }

        $row['username'] = $username;
        $row['items'] = $items;

        $this->daffny->tpl->data[] = $row;
    }

    public function sanitize($text)
    {
        $text = htmlspecialchars($text, ENT_QUOTES);
        $text = str_replace("\n\r", "\n", $text);
        $text = str_replace("\r\n", "\n", $text);
        $text = str_replace("\n", "<br>", $text);
        return $text;
    }

    public function chatBoxSession($chatbox)
    {
        $items = '';
        $chatHistory = $_SESSION['chatHistory'];
        $chatHistory[$chatbox];

        if ($chatHistory[$chatbox]) {
            $items = $chatHistory[$chatbox];
        }
        return $items;
    }

    public function closeChat()
    {
        $post = $_POST['chatbox'];
        $openChatBoxes = $_SESSION['openChatBoxes'];
        $this->session->unset_userdata($openChatBoxes[$post]);
        exit(0);
    }

}
