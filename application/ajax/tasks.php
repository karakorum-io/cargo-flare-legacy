<?php

/**
 * Ajax handler file for Tasks functionality
 * @author Shahrukh
 * @version 2.0
 */
require_once('init.php');
$memberId = (int) $_SESSION['member_id'];
$out = array('success' => false);

if ($memberId > 0) {
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case "create":
                    $task = new Task($daffny->DB);

                    $reminder = rawurldecode($_POST['reminder']);

                    if ($reminder == 1) {
                        $reminder_time = rawurldecode($_POST['reminder_time']);
                        $time = date("H:i", strtotime($reminder_time));
                        $time = $time . ":00";
                    } else {
                        $time = "00:00:00";
                    }

                    $date = date("Y-m-d", strtotime(rawurldecode($_POST['date'])));
                    $reminder_date = date("Y-m-d", strtotime(rawurldecode($_POST['reminder_date']))) . " " . $time;

                    // parsing entity id
                    $message = rawurldecode($_POST['message']);

                    $task->create(
                            array(
                            'sender_id' => $memberId,
                            'date' => $date,
                            'entity_id' => $_POST['entity_id'],
                            'message' => $message,
                            'duedate' => date("Y-m-d", strtotime(rawurldecode($_POST['duedate']))),
                            'status' => rawurldecode($_POST['status']),
                            'priority' => rawurldecode($_POST['priority']),
                            'reminder' => $reminder,
                            'reminder_date' => $reminder_date,
                            'reminder_time' => $time,
                            'taskdata' => rawurldecode($_POST['taskdata'])
                        ), explode(",", $_POST['receivers'])
                    );

                    $out = array('success' => true);
                    break;
                case "mark_complete":
                    foreach ($_POST['task_ids'] as $task_id) {
                        $task = new Task($daffny->DB);
                        $task->load($task_id);
                        $task->update_custom(
                                array(
                                'completed' => 1,
                                'completed_date' => date('Y-m-d h:i:s'),
                                'completed_by' => $_SESSION['member']['id']
                            )
                        );
                        $out = array('success' => true);
                    }
                    break;
                case "mark_complete_single":
                    $task = new Task($daffny->DB);
                    $task->load($_POST['task_id']);
                    $task->update_custom(
                        array(
                            'completed' => 1,
                            'completed_date' => date('Y-m-d h:i:s'),
                            'completed_by' => $_SESSION['member']['id']
                        )
                    );
                    $out = array('success' => true);
                    break;
                case "mark_incomplete":
                    foreach ($_POST['task_ids'] as $task_id) {
                        $task = new Task($daffny->DB);
                        $task->load($task_id);
                        $task->update_custom(
                            array(
                                'completed' => 0,
                                'completed_date' => NULL,
                                'completed_by' => "",
                                'deleted' => 0,
                                'deleted_date' => NULL,
                                'deleted_by' => "",
                            )
                        );
                        $out = array('success' => true);
                    }
                    break;
                case "undelete":
                    foreach ($_POST['task_ids'] as $task_id) {
                        $task = new Task($daffny->DB);
                        $task->load($task_id);
                        $task->update_custom(
                            array(
                                'deleted' => 0,
                                'deleted_date' => NULL,
                                'deleted_by' => NULL
                            )
                        );
                        $out = array('success' => true);
                    }
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
                        $task->update_custom(array('completed' => 1));
                        $out = array('success' => true);
                    }
                    break;
                case "get":
                    $taskManager = new TaskManager($daffny->DB);
                    $tasks = $taskManager->getByMemberId($_SESSION['member_id'], Task::TYPE_REMINDER);
                    $data = array();
                    foreach ($tasks as $task) {
                        $data[] = array('id' => $task->id, 'message' => $task->message);
                    }
                    $out = array('success' => true, 'data' => $data);
                    break;
                case "today_n_reminder_tasks":

                    // fetching todays tasks
                    $taskManager = new TaskManager($daffny->DB);
                    $todaytasks = $taskManager->get_list_todays_task($_SESSION['member_id'],$_POST['search']);
                    $data = array();

                    $counter = 0;

                    $entity = new Entity($daffny->DB);

                    foreach ($todaytasks as $task) {

                        if($task->entity_id != 0){
                            $entity->load($task->entity_id);
                        }

                        $members = array();

                        foreach ($task->getMembers() as $member) {
                            $members[] = $member->contactname;
                        }

                        $data[$counter]['id'] = $task->id;
                        $data[$counter]['entity_id'] = $task->entity_id == 0 ? " " : "<a style='color:#008ec2;' target='_blank' href='" . getLink('orders', 'show', 'id', $task->entity_id) . "'>" .$entity->prefix."-".$entity->number. "</a>";
                        $data[$counter]['message'] = $task->message;
                        $data[$counter]['taskdata'] = $task->taskdata;
                        $data[$counter]['dtime'] = date('m-d-Y',strtotime($task->reminder_date)) . " " . date('H:i a',strtotime($task->reminder_time));
                        $data[$counter]['assigned'] = implode(', ', $members);
                        $data[$counter]['sender_id'] = $task->getSender()->contactname;
                        $data[$counter]['date'] = date('m-d-Y',strtotime($task->date));
                        
                        $counter++;
                    }

                    // fetching reminder tasks
                    $taskManager = new TaskManager($daffny->DB);
                    $remindertasks = $taskManager->get_notification_list_task($_SESSION['member_id'],$_POST['search']);
                    $reminder_data = array();

                    $counter = 0;
                    foreach ($remindertasks as $task) {

                        if($task->entity_id != 0){
                            $entity->load($task->entity_id);
                        }
                        
                        $members = array();

                        foreach ($task->getMembers() as $member) {
                            $members[] = $member->contactname;
                        }

                        $reminder_data[$counter]['id'] = $task->id;
                        $reminder_data[$counter]['entity_id'] = $task->entity_id == 0 ? " " : "<a style='color:#008ec2;' target='_blank' href='" . getLink('orders', 'show', 'id', $task->entity_id) . "'>" .$entity->prefix."-".$entity->number. "</a>";
                        $reminder_data[$counter]['message'] = $task->message;
                        $reminder_data[$counter]['taskdata'] = $task->taskdata;
                        $reminder_data[$counter]['dtime'] = date('m-d-Y',strtotime($task->reminder_date)) . " " . date('H:i a',strtotime($task->reminder_time));
                        $reminder_data[$counter]['assigned'] = implode(', ', $members);
                        $reminder_data[$counter]['sender_id'] = $task->getSender()->contactname;
                        $reminder_data[$counter]['date'] = $task->date;
                        $counter++;
                    }

                    $out = array('success' => true, 'today_tasks' => $data, 'reminder' => $reminder_data);
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

                    $rows = $daffny->DB->selectRow(" reminder_time, reminder_date ", "app_task_member", "WHERE task_id = ".$_POST['id']." AND member_id = ".$_SESSION['member_id']);

                    $reminder_time = rawurldecode($rows['reminder_time']);
                    $time = date("h:i A", strtotime($reminder_time));

                    $data = array(
                        'date' => $task->getDate(),
                        'entity_id' => $task->entity_id == 0? "" : $task->entity_id,
                        'message' => rawurlencode($task->message),
                        'assigned' => $assigned,
                        'duedate' => date("m/d/Y", strtotime($task->duedate)),
                        'status' => rawurlencode($task->status),
                        'priority' => rawurlencode($task->priority),
                        'reminder' => $task->reminder,
                        'reminder_date' => date("m/d/Y", strtotime($rows['reminder_date'])),
                        'reminder_time' => $time,
                        'taskdata' => rawurlencode($task->taskdata)
                    );

                    $out = array('success' => true, 'data' => $data);
                    break;
                case "editTask":
                    try{

                        $task = new Task($daffny->DB);
                        $task->load($_POST['id']);
                        if ($task->sender_id != $_SESSION['member_id']) {
                            $out = array('success' => false, 'error' => 'Access denied');
                            break;
                        }

                        // changes made
                        $reminder = rawurldecode($_POST['reminder']);
                        if ($reminder == 1) {
                            $reminder_time = rawurldecode($_POST['reminder_time']);
                            $time = date("H:i", strtotime($reminder_time));
                            $time = $time . ":00";
                        } else {
                            $time = "00:00:00";
                        }

                        $reminder_date = date("Y-m-d", strtotime(rawurldecode($_POST['reminder_date']))) . " " . $time;

                        // parsing entity id
                        $message = rawurldecode($_POST['message']);
                        $task->update(
                            array(
                                'entity_id' => $_POST['entity_id'],
                                'message' => $message,
                                'status' => rawurldecode($_POST['status']),
                                'priority' => rawurldecode($_POST['priority']),
                                'reminder' => $reminder,
                                'reminder_date' => $reminder_date,
                                'reminder_time' => $time,
                                'taskdata' => rawurldecode($_POST['taskdata'])), explode(",", $_POST['assigned']
                            )
                        );

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
                    } catch(Exception $e) {
                        print_r($e->getMessage());
                    }
                break;
                case "get_notifications":
                    $taskManager = new TaskManager($daffny->DB);
                    $tasks = $taskManager->get_notification_task($_SESSION['member_id']);
                    $data = array();

                    $member = new Member($daffny->DB);
                    foreach ($tasks as $task) {
                        $member->load($task->sender_id);
                        $data[] = array(
                            'id' => $task->id,
                            'message' => $task->message,
                            'description' => $task->taskdata,
                            'sender_id' => $member->contactname,
                            'reminder_date' => date("m/d/y", strtotime($task->reminder_date)),
                            'reminder_time' => date("h:i a", strtotime($task->reminder_time))
                        );
                    }

                    $out = array('success' => true, 'data' => $data);
                    break;
                case "snooze":
                    $taskManager = new TaskManager($daffny->DB);

                    for ($i = 0; $i < count($_POST['task_ids']); $i++) {
                        $task_id = $_POST['task_ids'][$i];
                        $taskManager->snooze($task_id, $_POST['snooze_time']);
                    }

                    $out = array('success' => true);
                    break;
                default:
                    break;
            }
        } catch (FDException $e) {
            print_r($e);
        }
    }
}

echo $json->encode($out);
require_once("done.php");
?>