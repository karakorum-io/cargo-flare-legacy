<?php

/** FollowUp class 
* Class for working with follow-up * * Client:		FreightDragon * Version:		1.0 * Date:			2011-10-27 * Author:		C.A.W., Inc. dba INTECHCENTER * Address:	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076 * E-mail:		techsupport@intechcenter.com * CopyRight 2011 FreightDragon. - All Rights Reserved ***/

class FollowUp extends FdObject 
{    
const TABLE = "app_followups";    
public static $template_id = array(        2 => EmailTemplate::SYS_QUOTE_FL_PHONED_M        ,3=> EmailTemplate::SYS_QUOTE_FL_PHONED_S        ,4=> EmailTemplate::SYS_QUOTE_FL_EMAIL        ,5=> EmailTemplate::SYS_QUOTE_FL_FAX    );    public static function getTypes() {        return array(            EmailTemplate::SYS_QUOTE_FL_EMAIL => 'Email',            EmailTemplate::SYS_QUOTE_FL_PHONED_M => 'Phoned, Left Message',            EmailTemplate::SYS_QUOTE_FL_PHONED_S => 'Phoned, spoke to someone',            EmailTemplate::SYS_QUOTE_FL_FAX => 'Faxed',            0 => 'Set reminder only'        );    }    public function setFolowUp($type, $when, $quote_id, $sender_id = null) {        $insert_arr = array(            'type' => (int) $type,            'created' => date("Y-m-d H:i:s"),            'followup' => date('Y-m-d', strtotime($when)),            'entity_id' => (int) $quote_id,        );        if (is_null($sender_id)){            $insert_arr['sender_id'] = $_SESSION['member_id'];        }        $this->create($insert_arr);    }}
