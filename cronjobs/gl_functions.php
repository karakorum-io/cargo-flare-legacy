<?php

function split_name($name)
{
    $pos = strrpos($name, ' ');

    if ($pos === false) {
        return array(
            'fn' => $name,
            'ln' => ""
        );
    }

    $first_name = substr($name, 0, $pos + 1);
    $last_name = substr($name, $pos);

    return array(
        'fn' => trim($first_name),
        'ln' => trim($last_name)
    );
}

//"New York, NY 50805-2578"
function split_address($str)
{
    $str = trim($str);
    preg_match("/([^,]+),\s*(\w{2})\s*(\d{5}(?:-\d{4})?)/", $str, $matches);

    if (count($matches) > 0){
        list($arr['addr'], $arr['city'], $arr['state'], $arr['zip']) = $matches;
    }else{
        preg_match("/([^,]+),\s*(\w{2})/", $str, $matches);
        if (count($matches) > 0){
            list($arr['addr'], $arr['city'], $arr['state']) = $matches;
            $arr['zip'] = "";
        }else{
            $arr['city'] = $str;
            $arr['state'] = "";
            $arr['zip'] = "";
            $arr['addr'] = "";
        }
    }
    return $arr;
}

function state2format($state, $db){
    if (strlen($state)>2){
        $code = $db->selectValue("code", "states", "WHERE name='".mysqli_real_escape_string($db->connection_id, $state)."'");
        return $code;
    }else{
        return $state;
    }
}