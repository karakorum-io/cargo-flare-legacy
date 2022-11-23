<?php

/**
 * Ajax handler file for handling all ajax related operations
 * from CargoFlare.com
 *
 * @author Shahrukh
 * @copyright CargoFlare
 */

require_once "init.php";

$memberId = (int) $_SESSION['member_id'];
$out = array('success' => false);

//ob_start();
if ($memberId > 0) {
    try {
        switch ($_POST['action']) {
            case "get":
                $entity = new Entity($daffny->DB);
                $entity->load($_POST['entity_id']);
                $notes = $entity->getNotes(false, " order by convert(created,datetime) desc ");
                $data = array();
                foreach ($notes[$_POST['notes_type']] as $key => $note) {
                    $data[$key]['id'] = $note->id;
                    $data[$key]['text'] = $note->getText();
                    $data[$key]['created'] = $note->getCreated("m/d/y h:i a");
                    $data[$key]['memberId'] = $memberId;
                    $data[$key]['sender_id'] = $note->sender_id;
                    $data[$key]['system_admin'] = $note->system_admin;
                    $data[$key]['priority'] = $note->priority;
                    $data[$key]['access_notes'] = $_SESSION['member']['access_notes'];
                    if ($_POST['notes_type'] != Note::TYPE_FROM) {
                        $data[$key]['sender'] = $note->getSender()->contactname;
                    }
                    $data[$key]['number'] = $entity->getNumber();
                    $data[$key]['discard'] = $note->discard;
                    $data[$key]['parent_id'] = $note->getSender()->parent_id;
                }
                $note_red_green = 0;
                if ($entity->id > 0 && $entity->assigned_id == $memberId) {
                    $q = $daffny->DB->update("app_notes", array('status' => 0), "entity_id='" . $entity->id . "' ");
                    $note_red_green = 1;
                }

                $entity->updateHeaderTable();
                $out = array('success' => true, 'data' => $data, 'color' => $note_red_green);
                break;
            case "add":
                $entity = new Entity($daffny->DB);
                $entity->load($_POST['entity_id']);

                $status = 0;
                if ($entity->id > 0) {
                    $status = 1;
                }

                $notesText = "";
                if ($_POST['priority'] == 2) {
                    $notesText .= "High Priority: ";
                }

                $notesText .= (rawurldecode($_POST['text']));

                $note_array = array(
                    "entity_id" => $entity->id,
                    "sender_id" => $memberId,
                    "type" => $_POST['notes_type'],
                    "priority" => $_POST['priority'],
                    "status" => $status,
                    "text" => $notesText);
                $note = new Note($daffny->DB);
                $note->create($note_array);
                $data = array();
                $note_manager = new NoteManager($daffny->DB);
                $notes = $note_manager->getNotes((int) $_POST['entity_id'], " order by convert(created,datetime) desc ");
                foreach ($notes[$_POST['notes_type']] as $key => $value) {
                    $data[$key]['id'] = $value->id;
                    $data[$key]['text'] = rawurlencode($value->getText());
                    $data[$key]['created'] = $value->getCreated("m/d/y h:i a");
                    $data[$key]['memberId'] = $memberId;
                    $data[$key]['sender_id'] = $value->sender_id;
                    $data[$key]['system_admin'] = $value->system_admin;
                    $data[$key]['access_notes'] = $_SESSION['member']['access_notes'];
                    $sender = $value->getSender();
                    $data[$key]['sender'] = $sender->contactname;
                    $data[$key]['email'] = $sender->email;
                    $data[$key]['priority'] = $value->priority;
                    $data[$key]['discard'] = $value->discard;
                    $data[$key]['parent_id'] = $note->getSender()->parent_id;

                }

                $showColor = 1; //red =1
                if ($entity->assigned_id == $memberId) {
                    $showColor = 0;
                    // Update Entity
                    $update_arr = array(
                        'last_activity_date' => date('Y-m-d H:i:s'),
                    );
                    $entity->update($update_arr);
                }

                $entity->updateHeaderTable();

                $out = array('success' => true, 'data' => $data, 'showColor' => $showColor);
                break;
            case "del":
                $entity = new Entity($daffny->DB);
                $entity->load($_POST['entity_id']);
                $note = new Note($daffny->DB);
                $note->delete($_POST['id']);
                $notes = $entity->getNotes(false, " order by convert(created,datetime) desc ");
                foreach ($notes[$_POST['notes_type']] as $key => $value) {
                    $data[$key]['id'] = $value->id;
                    $data[$key]['text'] = rawurlencode($value->getText());
                    $data[$key]['created'] = $value->getCreated("m/d/y h:i a");
                    $data[$key]['memberId'] = $memberId;
                    $data[$key]['sender_id'] = $value->sender_id;
                    $data[$key]['system_admin'] = $value->system_admin;
                    $data[$key]['access_notes'] = $_SESSION['member']['access_notes'];
                    $sender = $value->getSender();
                    $data[$key]['sender'] = $sender->contactname;
                    $data[$key]['email'] = $sender->email;
                    $data[$key]['priority'] = $value->priority;
                    $data[$key]['discard'] = $value->discard;
                    $data[$key]['parent_id'] = $note->getSender()->parent_id;
                }

                $entity->updateHeaderTable();
                $out = array('success' => true, 'data' => $data);
                break;
            case "update":
                $entity = new Entity($daffny->DB);
                $entity->load($_POST['entity_id']);
                $note = new Note($daffny->DB);
                $note->load((int) $_POST['id']);
                $note->update(array('text' => rawurldecode($_POST['text'])));
                $notes = $entity->getNotes(false, " order by convert(created,datetime) desc ");
                foreach ($notes[$_POST['notes_type']] as $key => $value) {
                    $data[$key]['id'] = $value->id;
                    $data[$key]['text'] = rawurlencode($value->getText());
                    $data[$key]['created'] = $value->getCreated("m/d/y h:i a");
                    $data[$key]['memberId'] = $memberId;
                    $data[$key]['sender_id'] = $value->sender_id;
                    $data[$key]['system_admin'] = $value->system_admin;
                    $data[$key]['access_notes'] = $_SESSION['member']['access_notes'];
                    $sender = $value->getSender();
                    $data[$key]['sender'] = $sender->contactname;
                    $data[$key]['email'] = $sender->email;
                    $data[$key]['priority'] = $value->priority;
                    $data[$key]['discard'] = $value->discard;
                    $data[$key]['parent_id'] = $note->getSender()->parent_id;
                }
                $entity->updateHeaderTable();
                $out = array('success' => true, 'data' => $data);
                break;
            case "discard":
                $entity = new Entity($daffny->DB);
                $entity->load($_POST['entity_id']);
                $note = new Note($daffny->DB);
                $note->load((int) $_POST['id']);
                $discardStatus = 0;
                if ($note->discard == 0) {
                    $discardStatus = 1;
                }

                $note->update(array('discard' => $discardStatus, "discard_member" => $memberId, "discard_date" => date('Y-m-d')));

                $notes = $entity->getNotes(false, " order by convert(created,datetime) desc ");
                foreach ($notes[$_POST['notes_type']] as $key => $value) {
                    $data[$key]['id'] = $value->id;
                    $data[$key]['text'] = rawurlencode($value->getText());
                    $data[$key]['created'] = $value->getCreated("m/d/y h:i a");
                    $data[$key]['memberId'] = $memberId;
                    $data[$key]['sender_id'] = $value->sender_id;
                    $data[$key]['system_admin'] = $value->system_admin;
                    $data[$key]['access_notes'] = $_SESSION['member']['access_notes'];
                    $sender = $value->getSender();
                    $data[$key]['sender'] = $sender->contactname;
                    $data[$key]['email'] = $sender->email;
                    $data[$key]['priority'] = $value->priority;
                    $data[$key]['discard'] = $value->discard;
                    $data[$key]['parent_id'] = $note->getSender()->parent_id;
                }
                $entity->updateHeaderTable();
                $out = array('success' => true, 'data' => $data);
                break;
            default:
                break;
        }
    } catch (FDException $e) {
        echo $e->getMessage();
    }
}
//ob_clean();
echo $json->encode($out);
require_once "done.php";
