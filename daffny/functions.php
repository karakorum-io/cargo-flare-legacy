<?php

/*
+---------------------------------------------------+
|                                                   |
|                   Daffny Engine                   |
|                                                   |
|                     Functions                     |
|                                                   |
|                by Alexey Kondakov                 |
|             (c)2006 - 2007 Daffny, Inc.           |
|                                                   |
|                  www.daffny.com                   |
|                                                   |
+---------------------------------------------------+
*/

/*------------------------------------*/
// Returns microtime for execution time checking
/*------------------------------------*/
if (!function_exists("getMicrotime"))
{
    function getMicrotime()
    {
        list($usec, $sec) = explode(" ", microtime());

        return ((float)$usec + (float)$sec);
    }
}

/*------------------------------------*/
// Patch for PHP < 4.3
/*------------------------------------*/
if (!function_exists("ob_get_clean"))
{
    function ob_get_clean()
    {
        $ob_contents = ob_get_contents();
        ob_end_clean();

        return $ob_contents;
    }
}

/*------------------------------------*/
// Size Format
/*------------------------------------*/
function size_format($bytes="")
{
    if ($bytes >= 1073741824) {
        return round($bytes / 1073741824 * 100) / 100 . "Gb";
    }

    if ($bytes >= 1048576) {
        return round($bytes / 1048576 * 100 ) / 100 . "Mb";
    }

    if ($bytes  >= 1024) {
        return round($bytes / 1024 * 100 ) / 100 . "Kb";
    }

    return $bytes . "Bytes";
}

/*------------------------------------*/
// GET variable
/*------------------------------------*/
function get_var($key)
{
    if (isset($_GET[$key])) {
        return $_GET[$key];
    }

    return;
}

/*------------------------------------*/
// POST variable
/*------------------------------------*/
function post_var($key)
{
    if (!isset($_POST[$key])) {
        return;
    }

    if (!is_array(@$_POST[$key]))
    {
        if (ini_get('magic_quotes_gpc') == 1) {
            return stripslashes(@$_POST[$key]);
        }

        return @$_POST[$key];
    }

    if (ini_get('magic_quotes_gpc') == 1)
    {
        $ret = array();

        foreach (@$_POST[$key] as $k => $v) {
            $ret[$k] = stripslashes($v);
        }

        return $ret;
    }

    return @$_POST[$key];
}

/*------------------------------------*/
// Get File Extension
/*------------------------------------*/
function get_ext($filename)
{
    return strtolower(substr(strrchr($filename, '.' ), 1));
}

/*------------------------------------*/
// Get Unique FileName
/*------------------------------------*/
function unique_filename($path, $filename)
{
    if (!file_exists($path.$filename)) {
        return $filename;
    }

    $ext = strtolower(substr(strrchr($filename, '.' ), 1));
    $file = str_replace(".".$ext, "", $filename);

    for ($i=0; $i<999; $i++)
    {
        $tmpname = $file."($i).".$ext;

        if (!file_exists($path.$tmpname))
        {
            $new_filename = $tmpname;
            break;
        }
    }

    return $new_filename;
}

/*------------------------------------*/
// Random key for Password or other
/*------------------------------------*/
function randomkeys($length = 8, $level = 0)
{
    $key = "";

    switch ($level)
    {
        case 1:
            $pattern = "abcdefghijklmnopqrstuvwxyz";
        break;

        case 2:
            $pattern = "1234567890abcdefghijklmnopqrstuvwxyz";
        break;

        case 3:
            $pattern = "1234567890abcdefghijklmnopqrstuvwxyz~!@#$%^&()_+;.,";
        break;

        default:
            $pattern = "1234567890";
        break;
    }

    for ($i=0; $i<$length; $i++) {
        $key .= $pattern{rand(0, strlen($pattern)-1)};
    }

    return $key;
}

/*------------------------------------*/
// Debug Print
/*------------------------------------*/
function printt($var = "Empty", $exit = false)
{
    echo "<pre>";
    print_r($var);
    echo "</pre>";

    if ($exit) {
        exit();
    }
}

/*------------------------------------*/
// Save POST variables for forms
/*------------------------------------*/
function save_vars($keys = array())
{
    $vars = array();
    foreach ($keys as $k) {
        $vars[$k] = htmlspecialchars(@$_POST[$k]);
    }

    return $vars;
}

/*------------------------------------*/
// Save necessary GET variables
/*------------------------------------*/
function save_url($keys = array(), $delimiter = "&amp;")
{
    if (count($keys) == 0) {
        return;
    }

    $url = "";
    foreach ($keys as $k)
    {
        if (!isset($_GET[$k])) continue;
        $url .= $delimiter.$k."=".$_GET[$k];
    }

    return $url;
}

/*------------------------------------*/
// Usual Redirect
/*------------------------------------*/
function redirect($url = "./")
{
	if (is_null($url)) return;
    header("Location: $url");
    exit();
}

/*------------------------------------*/
// Highlight any part of text
/*------------------------------------*/
function highlight($text, $phrase, $highlighter = '<span style="background-color:#ffff66">\1</span>')
{
    if (empty($phrase)) {
        return $text;
    }

    return preg_replace("|({$phrase})|i", $highlighter, $text);
}

/*------------------------------------*/
// Validate Email Address
/*------------------------------------*/
function validate_email($email)
{
    if (!preg_match("/^([-a-zA-Z0-9._]+@[-a-zA-Z0-9.]+(\.[-a-zA-Z0-9]+)+)*$/", $email)) {
        return false;
    }

    return true;
}

/*------------------------------------*/
// Check date
/*------------------------------------*/
function is_valid_date($value, $format = 'mm/dd/yyyy')
{
    if (strlen($value) >= 6 && strlen($format) == 10)
    {
        $separator_only = str_replace(array('m', 'd', 'y'), '', $format);
        $separator = $separator_only[0];

        if ($separator && strlen($separator_only) == 2)
        {
            $regexp = str_replace('mm', '(0?[1-9]|1[0-2])', $format);
            $regexp = str_replace('dd', '(0?[1-9]|[1-2][0-9]|3[0-1])', $regexp);
            $regexp = str_replace('yyyy', '(19|20)?[0-9][0-9]', $regexp);
            $regexp = str_replace($separator, "\\" . $separator, $regexp);

            if ($regexp != $value && preg_match('/'.$regexp.'\z/', $value))
            {
                $arr = explode($separator, $value);
                $day   = $arr[0];
                $month = $arr[1];
                $year  = $arr[2];

                if (@checkdate($month, $day, $year)) {
                    return true;
                }
            }
        }
    }

    return false;
}

function getIPAddress()
{
    $addrs = array();

    $addrs[] = @$_SERVER['HTTP_CLIENT_IP'];
    $addrs[] = @$_SERVER['REMOTE_ADDR'];
    $addrs[] = @$_SERVER['HTTP_PROXY_USER'];

    // Do we have one yet?
    foreach ($addrs as $ip) {
        if ($ip) break;
    }

    // Make sure we take a valid IP address
    $ip = preg_replace("/^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})/", "\\1.\\2.\\3.\\4", $ip);

    return $ip;
}

function ch($exp, $ifYes = "", $ifNo = "") {
	return $exp ? $ifYes : $ifNo;
}