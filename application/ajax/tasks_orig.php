<?php

/**
 * Ajax handler file for handling all ajax related operations
 * from CargoFlare.com
 *
 * @author Shahrukh
 * @copyright CargoFlare
 */

require_once 'init.php';
$memberId = (int) $_SESSION['member_id'];
$out = array('success' => false);
if ($memberId > 0) {
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case "create":
                    $task = new Task($daffny->DB);
                    $task->create(array('sender_id' => $memberId, 'date' => date("Y-m-d", strtotime(rawurldecode($_POST['date']))), 'message' => rawurldecode($_POST['message'])), explode(",", $_POST['receivers']));
                    $out = array('success' => true);
                    break;
                case "done":
                    $task = new Task($daffny->DB);
                    $task->load($_POST['id']);
                    $allow = false;
                    foreach ($task->getMembers() as $member) {
                        if ($member->id == $_SESSION['member']['id']) {
                            $allow = true;
                        }
                    }
                    if ($allow) {
                        $task->update(array('completed' => 1));
                        $out = array('success' => true);
                    }
                    break;
                case "get":
                    $taskManager = new TaskManager($daffny->DB);
                    $tasks = $taskManager->getByMemberId($_SESSION['member_id'], Task::TYPE_TODAY);
                    $data = array();
                    foreach ($tasks as $task) {
                        $data[] = array('id' => $task->id, 'message' => $task->message);
                    }
                    $out = array('success' => true, 'data' => $data);
                    break;
                case "getTask":
                    $task = new Task($daffny->DB);
                    $task->load($_POST['id']);
                    if ($task->sender_id != $_SESSION['member_id']) {
                        $out = array('success' => false, 'error' => 'Access denied');
                        break;
                    }
                    $assigned = array();
                    foreach ($task->getMembers() as $member) {
                        $assigned[] = (int) $member->id;
                    }
                    $data = array('date' => $task->getDate(), 'message' => rawurlencode($task->message), 'assigned' => $assigned);
                    $out = array('success' => true, 'data' => $data);
                    break;
                case "editTask":
                    $task = new Task($daffny->DB);
                    $task->load($_POST['id']);
                    if ($task->sender_id != $_SESSION['member_id']) {
                        $out = array('success' => false, 'error' => 'Access denied');
                        break;
                    }
                    $task->update(array('date' => date("Y-m-d", strtotime($_POST['date'])), 'message' => rawurldecode($_POST['message'])), explode(",", $_POST['assigned']));
                    $data = "";
                    $data .= "<td class='grid-body-left'>" . $task->getDate() . "</td>";
                    $data .= "<td>" . htmlspecialchars($task->message) . "</td>";
                    $assigned = array();
                    foreach ($task->getMembers() as $member) {
                        $assigned[] = $member->contactname;
                    }
                    $data .= "<td>" . implode(", ", $assigned) . "</td>";
                    $data .= "<td>" . $task->getSender()->contactname . "</td>";
                    $data .= "<td>" . Task::$status_name[$task->completed] . "</td>";
                    $data .= "<td style='width: 16px;'><a href=\"javascript:editTask({$task->id})\"><img src=\"" . SITE_IN . "<?= SITE_IN ?>images/icons/edit.png\" title=\"Edit\" alt=\"Edit\" width=\"16\" height=\"16\" /></a></td>";
                    $data .= "<td style='width: 16px;' class='grid-body-right'><img src=\"" . SITE_IN . "images/icons/delete.png\" title=\"Delete\" alt=\"Delete\" class=\"pointer\" onclick=\"return deleteItem('" . getLink("tasks", "delete", "id", $task->id) . "', 'row-" . $task->id . "', false);\" width=\"16\" height=\"16\" /></td>";
                    $out = array('success' => true, 'data' => $data);
                    break;
                default:
                    break;
            }
        } catch (FDException $e) {}
    }
}
echo $json->encode($out);
require_once "done.php";
