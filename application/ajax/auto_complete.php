<?php

require_once "init.php";

$memberId = (int) $_SESSION['member_id'];
$out = array('success' => false);

try {
    switch ($_POST['action']) {
        case 'suggestions':
            $autoCorrect = new AutoCorrect();
            $results = $autoCorrect->run($_POST['address']);
            $out = array('success' => true, 'result' => $results);
            break;
        default:
            $out = array('success' => false);
            break;
    }
} catch(Exception $e){
    $out = array('success' => false, 'message'=>$e->getMessage());
}

echo $json->encode($out);
require_once "done.php";