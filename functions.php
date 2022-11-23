<?php

/**
 * Generic function to pull lead source from the lead source table on the basis of lead source id
 * 
 * @author  Shahrukh Charlee
 * @version 1.0.0
 * @return Array
 */
function getLeadSourceByID($db, $id){
    $sql = "SELECT * FROM `app_leadsources` WHERE `id` = ".$id;
    $source = $db->selectRows($sql);
    return $source;
}

/**
 * Generic function to pull lead source from the lead source table on the basis of lead source id
 * 
 * @author  Shahrukh Charlee
 * @version 1.0.0
 * @return Array
 */
function getRefererByID($db, $id){
    return $db->select_one("name,salesrep", "app_referrers", "WHERE  id = '" . $id . "'");
}


/**
 * Developer function, created to work on production on emergency situations,
 * By passing Active users using web application not to see debugging logs.
 *
 * @author Chetu Inc.
 * @version 1.0
 * @return Boolean
 */
function debug_production()
{
    $chetu_ip = '182.156.245.130';
    $ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];

    if ($ip === $chetu_ip) {
        return true;
    }
}

/**
 * JSONResponse
 */
function JSONResponse($array){
    echo json_encode($array);
    die;
}

/**
 * Function to check access to edit dispatched orders
 *
 * @param integer $status
 * @param integer $privilige
 * @author Chetu Inc.
 * @version 1.0
 */
function checkDispatchOrderEditAccess($status, $privilige)
{

    /**
     * non accessible status
     */
    $statuses = array(5, 6, 7, 8, 9);

    /**
     * Checking access
     */
    if (($privilige == 0) && (in_array($status, $statuses))) {

        /**
         * Returning negative response
         */
        return false;
    } else {
        /**
         * Returning positive response
         */
        return true;
    }
}

function getLink()
{
    $link = SITE_IN;

    if (FILES_DIR != "app") {
        $link .= FILES_DIR;
    } else {
        $link = substr($link, 0, -1);
    }

    $params = func_get_args();

    if (!count($params)) {
        return $link . "/";
    }

    foreach ($params as $param) {
        $link .= sprintf('/%s', $param);
    }

    return $link;
}

function validate_id($id)
{
    return (isset($id) && ctype_digit((string) $id));
}

function _fff($field)
{
    return "DATE_FORMAT(" . $field . ", '%Y/%m/')";
}

function getHash()
{
    return md5(time() . "-" . rand());
}

function cutContent($content, $length)
{
    preg_match("/(.*?\s|\S){0," . $length . "}/s", $content, $regs);
    if ($content != trim($regs[0])) {
        $content = trim($regs[0]) . "...";
    }
    return $content;
}

function quote($value, $nl2br = false, $strip_tags = true, $escape = true)
{
    $value = ($strip_tags ? strip_tags($value) : $value);
    $value = trim($value);
    $value = ($nl2br ? nl2br($value) : $value);
    $value = (get_magic_quotes_gpc() ? stripslashes($value) : $value);
    $value = ($escape ? addslashes($value) : $value);

    return $value;
}

function one_line($value)
{
    return str_replace(array("\r\n", "\r", "\n"), " ", $value);
}

function get_file_ext($file_name)
{
    $file_info = pathinfo($file_name);
    return strtolower($file_info["extension"]);
}

function viewNews($newsData)
{
    $out = "";
    if (count($newsData) > 0) {
        foreach ($newsData as $key => $news) {
            $out .= '<li>'
            . $news['news_date'] . '<br />
            <a href="' . getLink('news', 'show', 'id', $news['id']) . '">' . htmlspecialchars($news['title']) . '</a>
        </li>';
        }
    } else {
        $out = '<li>
            No records found.
        </li>';
    }
    return $out;
}

function get_file_size($value)
{
    if (($value >= 0) && ($value < 1024)) {
        $value = $value . "B";
    } else if (($value >= 1024) && ($value < 1048576)) {
        $value = number_format($value / 1024, 1, ".", "") . "KB";
    } else if (($value >= 1048576) && ($value < 1073741824)) {
        $value = number_format($value / 1048576, 1, ".", "") . "MB";
    } else if (($value >= 1073741824) && ($value < 1099511627776)) {
        $value = number_format($value / 1073741824, 1, ".", "") . "GB";
    }

    return $value;
}

function get_bytes($value, $type = "MB")
{
    if ($type == "KB") {
        $value = $value * 1024;
    } else if ($type == "MB") {
        $value = $value * 1048576;
    } else if ($type == "GB") {
        $value = $value * 1073741824;
    }

    return $value;
}

function validate_folder($file_folder, $file_path)
{
    if (!file_exists($file_path . $file_folder)) {
        $folders = explode("/", $file_folder);
        foreach ($folders as $folder) {
            if ($folder != "") {
                $file_path .= $folder . "/";
                @mkdir($file_path, 0777);
            }
        }
    }
}

/**
 * put your comment there...
 *
 */
function isGuest()
{
    if ($_SESSION['member_chmod'] == 0 || $_SESSION['member_id'] == 0) {
        return true;
    }

    return false;
}

/**
 * Check if we are an admin
 *
 * @return boolean
 */
function isAdmin()
{
    if ($_SESSION['member_chmod'] == 1) {
        return true;
    }

    return false;
}

function isMember()
{
    if ($_SESSION['member_chmod'] == 2) {
        return true;
    }

    return false;
}

function getMemberId()
{
    return @intval($_SESSION['member_id']);
}

function getParentId()
{
    if ($_SESSION['parent_id'] == 0) {
        return @intval($_SESSION['member_id']);
    } else {
        return @intval($_SESSION['parent_id']);
    }
}

function getMemberChmod()
{
    return @intval($_SESSION['member_chmod']);
}

function getUserDir($chmod = 0)
{
    if ($chmod == 0) {
        $chmod = $_SESSION['member_chmod'];
    }

    switch ($chmod) {
        case 0:
            return "";
            break;

        default:
            return "user";
            break;
    }
}

function formBoxStart($title = "", $width = "", $class = "white")
{
    ob_start();
    ?>

    <?php echo ($title != "") ? "<h4 style=\"color:#3B67A6\">" . $title . "</h4>" : ""; ?>
    <?
    return ob_get_clean();
}

function formBoxEnd($class = "white")
{
    ob_start();
    ?>

    <?
    return ob_get_clean();
}

function getFileImageByType($type, $title = "", $imgPath = null)
{
    if (is_null($imgPath)) {
        $imgPath = SITE_IN;
    }

    switch ($type) {
        case 'pdf':
            $icon = "pdf.gif";
            $alt = "Portable Document Format";
        break;

        case 'doc':
        case 'docx':
            $icon = "word.gif";
            $alt = "Microsoft Office Word";
        break;

        case 'xls':
        case 'xlsx':
            $icon = "excel.gif";
            $alt = "Microsoft Office Excel";
        break;

        case 'ppt':
        case 'pptx':
            $icon = "ppt.gif";
            $alt = "Microsoft Office PowerPoint";
        break;

        default:
            $icon = "file.png";
            $alt = "File";
        break;
    }

    if ($icon == "") {
        return null;
    }

    return sprintf('<img style="vertical-align:middle;" src="%s" alt="%s" title="%s" />', $imgPath . "images/icons/" . $icon, $alt, $title);
}

function deleteIcon($Url, $RowId, $title = "Delete", $add = "false")
{
    ?><div src="<?=SITE_IN . "images/icons/delete.png"?>" title="<?php echo $title; ?>" alt="<?php echo $title; ?>"
    class="pointer" onclick="return deleteItem('<?=$Url?>', '<?=$RowId?>', '<?=$add?>');" width="16"
    height="16" /><i class="fa fa-trash" aria-hidden="true" style="color: red"></i></div><?
}

function editIcon($Url, $title = "Edit")
{
    ?><a href="<?=$Url?>"><div src="<?=SITE_IN . "images/icons/edit.png"?>" title="<?php echo $title; ?>"
    alt="<?php echo $title; ?>" width="16" height="16"/><i class="far fa-edit" aria-hidden="true" style="color: blue"></i></div></a>
    <?
}

function payIcon($Url, $title = "Payments")
{
    ?><a href="<?=$Url?>"><img src="<?=SITE_IN . "images/icons/billing.png"?>" title="<?php echo $title; ?>"
    alt="<?php echo $title; ?>" width="16" height="16"/></a><?
}

function docsIcon($Url, $title = "Documents")
{
    ?><a href="<?=$Url?>"><img src="<?=SITE_IN . "images/icons/contract.png"?>" title="<?php echo $title; ?>"
    alt="<?php echo $title; ?>" width="16" height="16"/></a><?
}

function loginIcon($Url, $title = "Login As User")
{
    ?><a href="<?=$Url?>"><img src="<?=SITE_IN . "images/icons/login.png"?>" title="<?php echo $title; ?>"
    alt="<?php echo $title; ?>" width="16" height="16"/></a><?
}

function renewIcon($Url, $title = "Renew")
{
    ?><a href="<?=$Url?>"><img src="<?=SITE_IN . "images/icons/renew.png"?>" title="<?php echo $title; ?>"
    alt="<?php echo $title; ?>" width="16" height="16"/></a><?
}

function cancelIcon($Url, $title = "Cancel")
{
    ?><a href="<?=$Url?>"><img src="<?=SITE_IN . "images/icons/cancel.png"?>" title="<?php echo $title; ?>"
    alt="<?php echo $title; ?>" width="16" height="16"/></a><?
}

function closeIcon($Url, $title = "Close")
{
    ?><a href="<?=$Url?>"><img src="<?=SITE_IN . "images/icons/control_stop.png"?>" title="<?php echo $title; ?>"
    alt="<?php echo $title; ?>" width="16" height="16"/></a><?
}

function reactivateIcon($Url, $title = "Reactivate")
{
    ?><a href="<?=$Url?>"><img src="<?=SITE_IN . "images/icons/control_play.png"?>" title="<?php echo $title; ?>"
    alt="<?php echo $title; ?>" width="16" height="16"/></a><?
}

function editClickIcon($func)
{
    ?><img src="<?=SITE_IN?>images/icons/edit.png" title="Edit" alt="Edit" width="16" height="16"
    onClick="<?=$func?>" style="cursor:pointer;" /><?
}

function previewIcon($Url, $title = "Preview")
{
    ?><a target="_blank" href="<?=$Url?>"><img src="<?=SITE_IN . "images/icons/preview.png"?>"
    title="<?php echo $title; ?>" alt="<?php echo $title; ?>" width="16"
    height="16"/></a><?
}

function infoIcon($Url, $title = "Info")
{
    ?><a href="<?=$Url?>"><i class="fa fa-info-circle" aria-hidden="true"></i></a><?
}

function statusText($Url, $Status)
{
    $st = $Status == 'Active' ? 'active' : 'inactive';
    ?><span class="pointer status-<?=$st?>" onclick="statusText('<?=$Url?>', this);"
    title="Click for change status"><?=$Status?></span><?
}

function aprovedText($Url, $Status)
{
    $st = $Status == 'Aproved' ? 'active' : 'inactive';
    ?><span class="pointer status-<?=$st?>" onclick="aprovedText('<?=$Url?>', this);"
    title="Click for change status"><?=$Status?></span><?
}

function moveUpIcon($Url)
{
    ?><a href="<?=$Url?>"><img src="<?=SITE_IN . "images/icons/moveup.png"?>" title="Move Up" alt="Move Up"
    width="16" height="16"/></a><?
}

function moveDownIcon($Url)
{
    ?><a href="<?=$Url?>"><img src="<?=SITE_IN . "images/icons/movedown.png"?>" title="Move Down" alt="Move Down"
    width="16" height="16"/></a><?
}

function addIcon($Url, $title = "Add")
{
    ?><a href="<?=$Url?>"><img src="<?=SITE_IN . "images/icons/add.png"?>" title="<?php echo $title; ?>"
    alt="<?php echo $title; ?>" width="16" height="16"/></a><?
}

function copyIcon($Url, $title = "Copy")
{
    ?><a href="<?=$Url?>"><img src="<?=SITE_IN . "images/icons/copy.png"?>" title="<?php echo $title; ?>"
    alt="<?php echo $title; ?>" width="16" height="16"/></a><?
}

function eventIcon($Url, $title = "Create an event")
{
    ?><a href="<?=$Url?>"><img src="<?=SITE_IN . "images/icons/event.png"?>" title="<?php echo $title; ?>"
    alt="<?php echo $title; ?>" width="16" height="16"/></a><?
}

function submitButtons($CancelUrl = "", $submitText = "OK", $btnid = "submit_button", $submit = "submit", $revertUrl = "",$class ="btn_dark_green")
{
    ob_start();
    ?>
    <div class="form-box-buttons">
        <input type="hidden" class="m-input btn-primary btn-block" name="<?=$submit?>"/>
        <input type="submit" id="<?=$btnid?>" class="btn btn-sm <?php echo $class; ?> " value="<?php echo $submitText; ?>" onclick="disableBtn();"/>
        <?php if (!empty($CancelUrl)): ?>
            &nbsp;&nbsp;&nbsp;
            <input type="button" class="m-input btn btn-dark btn-sm" value="Cancel" onclick="document.location.href='<?php echo $CancelUrl; ?>'"/>
        <?php endif;?>
        <? if (!empty($revertUrl)) { ?>
        <input type="button" class="btn btn-info btn-sm" value="Revert to Original" onclick="document.location.href='<?php echo $revertUrl; ?>'"/>
        <? }?>
    </div>




    <script type="text/javascript">//<![CDATA[
        function disableBtn() {
            setTimeout(function () {
                $('#<?=$btnid?>-submit-btn').html('<input type="button" class="btn btn-brand" value="Please wait..." disabled="disabled" />');
            }, 1);
        }
        //]]>
    </script>
    <?php

    return ob_get_clean();
}

function postToCDButtons($CancelUrl = "", $submitText = "OK", $btnid = "confirmsubmit_button", $submit = "submit_btn", $revertUrl = "")
{
    ob_start();
    ?>
    <div class="form-box-buttons">
        <input type="hidden" name="<?=$submit?>"/>
        <span id="<?=$btnid?>-submit-btn"><input type="button" id="<?=$btnid?>" value="<?php echo $submitText; ?>"
            onclick="checkPostToCD();"/></span>
            <?php if (!empty($CancelUrl)): ?>
                &nbsp;&nbsp;&nbsp;
                <input type="button" value="Cancel" onclick="document.location.href='<?php echo $CancelUrl; ?>'"/>
            <?php endif;?>
        </div>
        <?php

    return ob_get_clean();
}

function postToDispatchButtons($CancelUrl = "", $submitText = "OK", $btnid = "confirmsubmit_button", $submit = "submit_btn", $revertUrl = "")
{
    ob_start();
    ?>
        <div class="form-box-buttons">
            <input type="hidden" name="<?=$submit?>"/>
            <span id="<?=$btnid?>-submit-btn">
                <input type="button" id="<?=$btnid?>" value="<?php echo $submitText; ?>"
                onclick="checkDispatch();"/>
            </span>
            <?php if (!empty($CancelUrl)): ?>
                &nbsp;&nbsp;&nbsp;
                <input type="button" value="Cancel" onclick="document.location.href='<?php echo $CancelUrl; ?>'"/>
            <?php endif;?>
        </div>
        <?php

    return ob_get_clean();
}

function exportButton($submitText = "Export", $addclass = "btn-info")
{
    ob_start();
    ?>
        <div class="form-box-buttons">
            <span id="export-btn">

                <button type="button" name="export" id="exportbtn"
                value="<?php echo $submitText; ?>" class="btn <?php echo $addclass; ?>" onclick="disableBtnExp(event);"><?php echo $submitText; ?></button>
            </span>
        </div>
    <script type="text/javascript">//<![CDATA[
        function disableBtnExp(event) {


            var timestamp = $.now();
            Processing_show();
            var sourceform = $(event.target).closest('form');
            var d = sourceform.serializeArray();
            d.push({name: 'export', value: 'Yes'});
            d.push({name: 'token', value:timestamp});
            var url = sourceform.prop('action');
            $.post(url,d,function(data){

                KTApp.unblockPage();
                document.location = 'https://<?=$_SERVER['HTTP_HOST']?>/'+data;
            });
        }

        function Processing_show()
        {

            KTApp.blockPage({
                overlayColor: '#000000',
                type: 'v2',
                state: 'primary',
                message: ''
            });

        }
     //]]></script>
     <?php
    return ob_get_clean();
}

function nextButtons($CancelUrl = "", $sbmt_text = "Next")
{
    ob_start();
    ?>
    <div class="form-box-buttons">
        <input type="hidden" name="submit"/>
        <input type="button" value="Back" onclick="document.location.href='<?php echo $CancelUrl; ?>'"/>
        &nbsp;&nbsp;&nbsp;
        <span id="submit-btn">
            <input type="submit" class="btn btn-info" id="submit_button" value="<?=$sbmt_text?>"
            onclick="disableBtn();"/></span>

        </div>
    <script type="text/javascript">//<![CDATA[
        function disableBtn() {
            setTimeout(function () {
                $('#submit-btn').html('<input type="button" class="btn btn-info" value="Loading.." disabled="disabled" />');
            }, 1);
        }
        //]]></script>
        <?php

    return ob_get_clean();
}

function backButton($Url = "")
{
    ob_start();
    ?>
        <div class="form-box-buttons">
            <input type="button" class="btn-sm btn-dark" value="Back" onclick="document.location.href='<?php echo $Url; ?>'"/>
        </div>
        <?php

    return ob_get_clean();
}

function simpleButton($val = "", $Url = "", $class = "btn btn_dark_blue")
{
    ob_start();
    ?>
        <div class="form-box-buttons">
            <input type="button" value="<?=$val?>" onclick="document.location.href='<?php echo $Url; ?>'" class="<?=$class?>"/>
        </div>
        <?php

    return ob_get_clean();
}

function functionButton($label = "", $function = "", $style = null, $classes = "btn_bright_blue btn-sm")
{
    ob_start();
    ?>
        <button class="btn <?=(!is_null($classes)) ? $classes : ''?>" onclick="<?=$function?>" type="button" <?=(!is_null($style)) ? ' style="' . $style . '"' : ''?> id="<?=$label?>"><?=$label?></button>
    <?php
    return ob_get_clean();
}

function functionButtonPlan($label = "", $function = "", $style = null)
{
    ?>
    <a class="btn-naw" href="javascript:void(0);"  onclick="<?=$function?>" title="Deluxe"><span><?=$label?></span></a>
    <?php

}

function notesIcon($entity_id, $count, $type, $readonly, $countNewNotes = 0)
{
    ob_start();
    $classUsed = "";
    $classUsed = ($count > 0) ? "green" : "grey";
    if ($countNewNotes > 0) {
        $classUsed = 'red"';
    }
    ?>
    <div id="notes_<?=$type?>_<?=$entity_id?>" class=" note note-<?=$classUsed?>" <?='onclick="openAddNotes(' . $entity_id . ', ' . $type . ');"'?> ><?=$count?></div>
    <?php
    return ob_get_clean();

}

function formatPhoneNew($phone)
{
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($phone) >= 10) {
        //$fphone = "(" . substr($phone, 0, 3) . ") " . substr($phone, 3, 3) . "-" . substr($phone, 6, strlen($phone - 6));
        $fphone = substr($phone, 0, 1) . "-" . substr($phone, 1, 3) . "-" . substr($phone, 4, 3) . "-" . substr($phone, 7, strlen($phone - 7));
    } else {
        $fphone = $phone;
    }
    return $fphone;
}

function formatPhone($phone)
{
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($phone) >= 10) {
        //$fphone = "(" . substr($phone, 0, 3) . ") " . substr($phone, 3, 3) . "-" . substr($phone, 6, strlen($phone - 6));
        $fphone = substr($phone, 0, 3) . "-" . substr($phone, 3, 3) . "-" . substr($phone, 6, strlen($phone - 6));
    } else {
        $fphone = $phone;
    }
    return $fphone;
}

function formatUsPhone($phone)
{
    $numbers_only = preg_replace("/[^\d]/", "", $phone);
    return preg_replace("/^1?(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $numbers_only);
}

function GetDomain($url)
{
    $domain = @parse_url($url);
    if (!empty($domain["host"])) {
        return $domain["host"];
    } else {
        return str_replace(array("http://", "https://", "ftp://", "/", "\\"), "", $url);
    }
}

function mapLink($location)
{
    $link = "http://maps.google.com/maps?q=" . urlencode($location);
    return "<a href='{$link}' target='_blank' title='Map It'/>[Map It]</a>";
}

function routeLink($origin, $destination)
{
    $link = "http://maps.google.com/maps?saddr=" . urlencode($origin) . "&daddr=" . urlencode($destination);
    return "<a href='{$link}' target='_blank' title='Map It'/>[Route Map]</a>";
}

function imageLink($carName = "")
{
    $link = "http://www.google.com/search?tbm=isch&amp;hl=en&amp;q=" . urlencode($carName);
    return "<a href='{$link}' onclick=\"window.open(this.href); return false;\" title='Show It'>[Show It]</a>";
}

function colorRate($rating = "Not Rated")
{
    switch ($rating) {
        case 'Positive':
            return "<span style=\"color:green\">Positive</span>";
            break;
        case 'Neutral':
            return "<span style=\"color:#0052a4\">Neutral</span>";
            break;
        case 'Negative':
            return "<span style=\"color:red\">Negative</span>";
            break;
        case 'Pending':
            return "<span style=\"color:#0052a4\">Pending</span>";
            break;
        case 'Approved':
            return "<span style=\"color:green\">Approved</span>";
            break;
        default:
            return $rating;
            break;
    }
}

function colorBillingType($bt = "")
{
    switch ($bt) {
        case 'Payment':
            return "<span style=\"color:green\">Payment</span>";
            break;
        case 'Charge':
            return "<span style=\"color:#0052a4\">Charge</span>";
            break;
        default:
            return $bt;
    }
}

function hideCCNumber($number, $type = false)
{
    if ($type == 2) {
        return (string) @substr($number, -4);
    }
    if ($type) {
        return "****" . substr($number, -4);
    } else {
        return "**** **** **** " . substr($number, -4);
    }
}

function distance($lat1, $lon1, $lat2, $lon2)
{
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    return $miles;
}

function geo_distance($dist, $l)
{
    return $dist / (60 * 1.1515 * cos(deg2rad($l)));
}

/**
 *
 * @param string $address
 * @param string $city
 * @param string $state
 * @param string $zip
 * @return formatted string
 */

function formatAddress($address1 = "", $address2 = "", $city = "", $state = "", $zip = "", $country = "")
{
    $address = trim($address1 . " " . $address2);
    $out = $address;
    $out .= (!empty($address) && !empty($city)) ? ", " : "";
    $out .= $city;
    $out .= (!empty($city) && !empty($state)) ? ", " : "";
    $out .= $state;
    $out .= " ";
    $out .= $zip;
    $out .= " ";
    $out .= $country;
    return trim($out);
}

function exportCSVRecord($row)
{
    $delim = "";
    $record = "";
    foreach ($row as $v) {
        $record .= $delim . "\"" . $v . "\"";
        $delim = ",";
    }
    return $record . "\n";
}

function convertUSA2SQLDate($date)
{
    return preg_replace("/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/", "\\3-\\1-\\2", $date);
}

function convertSQL2USADate($date)
{
    return preg_replace("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$/", "\\2/\\3/\\1", $date);
}

function validate_cc_number($cc_number)
{
    /* Validate; return value is card type if valid. */
    $false = false;
    $card_type = "";
    $card_regexes = array(
        "/^4\d{12}(\d\d\d){0,1}$/" => Orders::TYPE_VISA,
        "/^5[12345]\d{14}$/" => Orders::TYPE_MASTERCARD,
        "/^3[47]\d{13}$/" => Orders::TYPE_AMERICAN_EXPRESS,
        "/^6011\d{12}$/" => Orders::TYPE_DISCOVER,
        "/^30[012345]\d{11}$/" => "diners",
        "/^3[68]\d{12}$/" => "diners",
    );

    foreach ($card_regexes as $regex => $type) {
        if (preg_match($regex, $cc_number)) {
            $card_type = $type;
            break;
        }
    }

    if (!$card_type) {
        return $false;
    }

    /*  mod 10 checksum algorithm  */
    $revcode = strrev($cc_number);
    $checksum = 0;

    for ($i = 0; $i < strlen($revcode); $i++) {
        $current_num = intval($revcode[$i]);
        if ($i & 1) { /* Odd  position */
            $current_num *= 2;
        }
        /* Split digits and add. */
        $checksum += $current_num % 10;
        if
        ($current_num > 9
        ) {
            $checksum += 1;
        }
    }

    if ($checksum % 10 == 0) {
        return $card_type;
    } else {
        return $false;
    }
}

function makePath($path, $withFileName = true)
{
    $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
    $pathParts = explode(DIRECTORY_SEPARATOR, $path);
    if ($withFileName) {
        $fileName = array_pop($pathParts[count($pathParts) - 1]);
    }
    $createdPath = $pathParts[0];
    unset($pathParts[0]);
    foreach ($pathParts as $pathPart) {
        $createdPath .= DIRECTORY_SEPARATOR . $pathPart;
        if (!file_exists($createdPath)) {
            mkdir($createdPath);
        }
    }
    if ($withFileName) {
        $createdPath .= DIRECTORY_SEPARATOR . $fileName;
    }
    return $createdPath;
}

function functionButtonDate($ostatus = "", $label = "", $function = "", $style = null, $id = null, $format = "mm/dd/yy")
{
    ob_start();
    ?>

    <div class="btn btn_bright_blue btn-sm" id="datevalue">
        <input type="text" id="<?=$id?>" value="<?php print date("Y-m-d");?>" maxlength="100" name="<?=$id?>"  style="opacity: 0;width: 0;" >
        <?php echo $label ?>
        <i class="fa fa-calendar" onclick="$('#<?=$id?>').focus();" aria-hidden="true"></i>
    </div>

    <script language="javascript">
        var datevalue = $("#datevalue").find('input').val();
        $('#<?=$id?>').datepicker({dateFormat: '<?=$format?>' }).on('changeDate', function (ev) {
            $('#<?=$id?>').change();
            var dateval = $('#date-daily').val();
            <?=$function?>('<?=$ostatus?>',datevalue);
        });

    </script>
    <?php
    return ob_get_clean();
}

function functionButtonDateByEntity($ostatus = "", $label = "", $function = "", $style = null, $id = null, $format = "mm/dd/yy", $entity_id)
{
    ob_start();?>
    <div class="btn btn_bright_blue btn-sm" id="datevalue">
        <input type="text" onclick="$('#<?=$id?>').focus();" id="<?=$id?>" value="<?php print date("Y-m-d");?>" maxlength="100" name="<?=$id?>"  style="opacity: 0;width: 0;" >
        <?php echo $label ?>
        <i class="fa fa-calendar" onclick="$('#<?=$id?>').focus();" aria-hidden="true"></i>
    </div>

    <script language="javascript">
        var datevalue = $("#datevalue").find('input').val();
        $('#<?=$id?>').datepicker({dateFormat: '<?=$format?>',"setDate": new Date()}).on('changeDate', function (ev) {
            $('#<?=$id?>').change();
            var dateval = $('#date-daily').val();
            <?=$function?>('<?=$ostatus?>',datevalue,'<?=$entity_id?>');
        });

    </script>

    <?php return ob_get_clean();
}

function detectMobileDevice()
{
    $mobileDevice = 0;
    $useragent = $_SERVER['HTTP_USER_AGENT'];
    if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {
        $mobileDevice = 1;
    }

    return $mobileDevice;
}

function getTagData($formData, $tag){
    if (array_key_exists($tag, $formData)) {
        return $formData[$tag];
    }
    return null;
}

?>