<?php

/**
 * block_status.php Ajax handler file for handling all ajax related operations
 * from CargoFlare.com
 *
 * @author Shahrukh
 * @copyright CargoFlare
 */

require_once "init.php";

$memberId = (int) $_SESSION['member_id'];
$out = array('success' => false);

if ($memberId > 0) {
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'managePageBlocks':
                    //Update DB status for off-line user
                    $updateId = $_SESSION['member_id'];
                    if ($updateId > 0) {
                        $date = date("Y-m-d H:i:s");
                        $currentDate = strtotime($date);
                        $futureDate = $currentDate + (60 * 5);
                        $online_heartbeat = date("Y-m-d H:i:s", $futureDate);
                        $postdata = array('online_status' => '1', 'online_heartbeat' => $online_heartbeat);
                        $done = $daffny->DB->update("members", $postdata, "id = '" . $updateId . "'");
                    }

                    $date = date("Y-m-d H:i:s");
                    $currentTime = strtotime($date);
                    $sql = "SELECT * FROM members WHERE is_deleted = 0 AND parent_id='" . getParentID() . "'";

                    $chatresults = $daffny->DB->selectRows($sql);
                    foreach ($chatresults as $data1) {
                        $heartbeat_Time = strtotime($data1['online_heartbeat']);
                        if ($currentTime > $heartbeat_Time) {
                            $date = date("Y-m-d H:i:s");
                            $updateId = $data1['id'];
                            $postdata = array('online_status' => '0');
                            $done = $daffny->DB->update("members", $postdata, "id = '" . $updateId . "'");

                        }
                    }

                    $heartBeatIncriment = 12 * 1;
                    $ordersQueueArr = $_SESSION['page_blocked']['orders_queue'];

                    $sql = "SELECT id, entity_id FROM  `member_blocked_page` where owner_id='" . getParentId() . "' and status=1 and page_heartbeat < NOW( )";
                    $pageresults = $daffny->DB->selectRows($sql);
                    foreach ($pageresults as $data1) {
                        $updateId = $data1['id'];
                        $entity_id = $data1['entity_id'];
                        $postdata = array('status' => '0', 'page_heartbeat' => 'NULL');
                        $done = $daffny->DB->update("member_blocked_page", $postdata, "id = '" . $updateId . "'");

                        $postdataEntity = array('blocked_by' => 'NULL', 'blocked_time' => 'NULL');
                        $doneEntity = $daffny->DB->update("app_entities", $postdataEntity, "id = '" . $entity_id . "' ");

                        if (is_array($ordersQueueArr) && sizeof($ordersQueueArr) > 0) {
                            if (($key = array_search($entity_id, $ordersQueueArr)) !== false) {
                                unset($ordersQueueArr[$key]);
                                $_SESSION['page_blocked']['orders_queue'] = $ordersQueueArr;
                            }
                        }

                    } // loop

                    $url = $_SERVER['HTTP_REFERER'];
                    if ($url != '') {
                        $urlArray = explode("/", $url);
                        $sizeUrl = sizeof($urlArray);
                        if (is_array($urlArray) && ($sizeUrl == 8 || $sizeUrl == 10)) {
                            if ($urlArray[4] == "orders" && $urlArray[5] == "edit" && $urlArray[6] == "id") {

                                $entity_id = $urlArray[7];
                                if (!in_array($entity_id, $ordersQueueArr)) {
                                    $_SESSION['page_blocked']['orders_queue'][] = $entity_id;
                                }
                                if ($entity_id > 0) {
                                    $date = date("Y-m-d H:i:s");
                                    $currentDate = strtotime($date);
                                    $futureDate = $currentDate + $heartBeatIncriment;
                                    $page_heartbeat = date("Y-m-d H:i:s", $futureDate);
                                    $postdata = array('page_heartbeat' => $page_heartbeat);

                                    $daffny->DB->update("member_blocked_page", $postdata, "owner_id = '" . getParentId() . "' and member_id = '" . $_SESSION['member_id'] . "' and entity_id='" . $entity_id . "' and status=1");
                                    print "<br>==heartbeat updated";

                                }
                            } elseif ($urlArray[4] == "orders" && $urlArray[5] == "dispatchnew" && $urlArray[6] == "id") {
                                $entity_id = $urlArray[7];
                                if (!in_array($entity_id, $ordersQueueArr)) {
                                    $_SESSION['page_blocked']['orders_queue'][] = $entity_id;
                                }

                                if ($entity_id > 0) {
                                    $date = date("Y-m-d H:i:s");
                                    $currentDate = strtotime($date);
                                    $futureDate = $currentDate + $heartBeatIncriment;
                                    $page_heartbeat = date("Y-m-d H:i:s", $futureDate);
                                    $postdata = array('page_heartbeat' => $page_heartbeat);

                                    $daffny->DB->update("member_blocked_page", $postdata, "owner_id = '" . getParentId() . "' and member_id = '" . $_SESSION['member_id'] . "' and entity_id='" . $entity_id . "' and status=1");
                                }
                            } else {
                                
                            }
                        } else {
                            print "<br>Other Page <br>";  
                        }
                    }
                    $out = array('success' => true, 'data' => $data);

                    break;

                default:
                    break;
            }
        } elseif (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'test':

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
