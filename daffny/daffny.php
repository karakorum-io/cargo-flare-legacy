<?php

/*
+---------------------------------------------------+
|                                                   |
|                   Daffny Engine                   |
|                                                   |
|                by Alexey Kondakov                 |
|             (c)2006 - 2007 Daffny, Inc.           |
|                                                   |
|                  www.daffny.com                   |
|                                                   |
+---------------------------------------------------+
*/

class Daffny
{
	/**
	 * @var mysql DB
	 */
	var $DB;

	/**
	 * @var template tpl
	 */
	var $tpl;

	/**
	 * @var auth authorize
	 */
	var $auth;

	/**
	 * @var html $html
	 */
	var $html;

    public $cfg = array();
    public $img_path = "./";

    public $action = "home";
    public $action_arr = array();

    public $action_method = "idx";
    public $action_method_arr = array("show", "view", "add", "edit", "delete", "status");
    public $action_method_reserverd_arr = array("construct", "__construct", "__deconstruct");

    /**
    * put your comment there...
    *
    * @param mixed $path
    * @param mixed $file_name
    * @param mixed $class_name
    * @param mixed $params
    * @return mixed
    */
    function load_class($path, $file_name, $class_name = "", $params = "")
    {
        // Buld full file path
        $file_path = $path.$file_name;

        // File exists?
        if (!file_exists($file_path)) {
            $this->error("File <b>$file_name</b> not exists.", "It must be here: $path");
        }

        // Require file
        require_once ($file_path);

        // Empty Class Name?
        if ($class_name == "") {
            $class_name = str_replace(".php", "", $file_name);
        }

        // Class exists?
        if (!class_exists($class_name)) {
            $this->error("Class <b>$class_name</b> not exists.", "It must be here: $file_path");
        }

        // Init Lib
        $obj = new $class_name($params);

        $obj->daffny =& $this;

        return $obj;
    }

    //--------------------------------
    // Load Daffny Lib
    //--------------------------------
    function load_lib($lib, $params = "")
    {
        return $this->load_class(DAFFNY_PATH."libs/", "$lib.php", "", $params);
    }

    public function getAction()
    {
        if (isset($_GET['url']))
        {
            if (substr($_GET['url'], -1) == "/") {
                $_GET['url'] = substr($_GET['url'], 0, -1);
            }

            $params_arr = explode("/", $_GET['url']);

            $action_tmp = str_replace("-", "_", $params_arr[0]);
			
            if (in_array($action_tmp, $this->action_arr))
            {
                $this->action = $action_tmp;
            }
        }

        return $this->action;
    }

    public function runAction()
    {
        require_once(CLASS_PATH."action.php");


        $Action = $this->load_class(SOURCE_PATH, $this->action.".php", ucfirst(FILES_DIR).ucfirst($this->action));
        $ActionMethods = array_diff(get_class_methods($Action), $this->action_method_reserverd_arr);

        if (!in_array("idx", $ActionMethods))
        {
            $this->error("Class <strong>".$this->action."</strong> haven't idx() method.", "Please create idx() method");
        }

        if (isset($_GET['url']))
        {
            $params_arr = explode("/", $_GET['url']);

            if (isset($params_arr[1]))
            {
                $action_method_tmp = str_replace("-", "_", $params_arr[1]);
            }
        }
        /*
        echo $action_method_tmp."<br />";
        printt($ActionMethods);
        */
        if (isset($action_method_tmp) && in_array($action_method_tmp, $ActionMethods))
        {
            $this->action_method = $action_method_tmp;
        }

        $_GET[$this->action] = $this->action_method != "idx" ? $this->action_method : "";
        if (isset($params_arr))
        {
            for ($i = 0; $i < count($params_arr); $i++)
            {
                if ($i <= 1)
                {
                    $tmp = str_replace("-", "_", $params_arr[$i]);

                    if (($i == 0 && $tmp == $this->action) || ($i == 1 && $tmp == $this->action_method))
                    {
                        continue;
                    }
                }

                $i++;

                $_GET[$params_arr[$i-1]] = (isset($params_arr[$i])) ? $params_arr[$i] : "";
            }
        }


        $Action->init();
        $Action->{$this->action_method}();
        $Action->construct();
        return $Action;
    }

    //--------------------------------
    // Message
    //--------------------------------
    function msg($messages, $type = "error")
    {
        if ((is_array($messages) && !count($messages)) || $messages == "") {
            return;
        }

        $msgs = is_array($messages) ? $messages : (array)$messages;

        $out = "";
        foreach ($msgs as $msg) {
            $out .= sprintf('<li>%s</li>', $msg);
        }

        return sprintf('<div class=" alert alert-%s " onclick="hideAndRemove(this);"><strong>%s</strong></div>', $type, $out)
             . '<script type="text/javascript">function hideAndRemove(id){var obj=$(id);obj.slideUp("normal",function(){obj.remove();});}</script>';
    }

    //--------------------------------
    // My Deconstructor
    //--------------------------------
    function done()
    {
        // Disconnect from DB
        if (class_exists("DB")) {
            $this->DB->disconnect();
        }
    }

    //--------------------------------
    // Fatal Error
    //--------------------------------
    static function error($err = "", $debug = "")
    {
        echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">"
            ."<html><head><title>Daffny Error</title></head><body>"
            ."<strong style=\"font-size: 20px;\">Daffny Error</strong><br />";

        if ($err != "") {
            echo "<strong>Error:</strong> $err<br />";
        }

        if ($debug != "") {
            echo "<strong>Debug:</strong> $debug";
        }

        echo "</body></html>";
        exit();
    }

    //--------------------------------
    // Load Config From DataBase
    //--------------------------------
    function load_config()
    {
        $q = $this->DB->select("name, value", "settings", "");
        while ($row = $this->DB->fetch_row($q)){
            $this->cfg[$row['name']] = $row['value'];
        }
    }
}

?>