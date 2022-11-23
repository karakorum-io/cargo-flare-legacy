<?php

class AppMain
{
    var $daffny;
    var $months = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");

    /**
    * put your comment there...
    *
    */
    public function init()
    {
        $cfg = $this->daffny->cfg;
        
        $this->daffny->DB = $this->daffny->load_lib("mysql");
        $this->daffny->DB->connect($cfg['my_host'], $cfg['my_user'], $cfg['my_pass'], $cfg['my_base'], $cfg['my_pref']);

        $this->daffny->html = $this->daffny->load_lib("html");

        $this->daffny->tpl  = $this->daffny->load_lib("template");
		$this->daffny->upload  = $this->daffny->load_lib("upload");
        $this->daffny->auth = $this->daffny->load_lib("auth");
        $this->daffny->auth->type = $cfg['auth_type'];

        $this->daffny->auth->authorise();

		$this->daffny->getAction();

        if ( isset($cfg["use_db_conf"]) && $cfg["use_db_conf"] ) {
            $this->daffny->load_config();
        }
    }


    /**
    * put your comment there...
    *
    */
    public function run()
    {

        
        if (defined('SITE_CLOSED')
        and SITE_CLOSED
        and !isset($_SESSION['owner'])
        and !$this->daffny->cfg['debug']
        and $this->daffny->action != "access_denied")
        {
            redirect(getLink("access-denied"));
        }

        $Action = $this->daffny->runAction();
        if (!preg_match("/127\.0\.0/i", $_SERVER['REMOTE_ADDR'])) {
        	//$this->ssl_redirect();
        }

        $title = "Cargo Flare ";
        if (isset($Action->title) && $Action->title != "") {
            $title .= " - ".strip_tags($Action->title);
        }

        $globalVars = array(
            'title'          => htmlspecialchars($title)
          , 'content'        => $Action->out
          , 'login_form'     => $this->daffny->tpl->build("login_form")
		  , 'top_menu_block' => $this->daffny->tpl->build("top_menu_block")
        );

        //echo $this->trimHtml($this->daffny->tpl->build("layout", $globalVars));
        echo $this->daffny->tpl->build("layout", $globalVars);
    }

    /**
    * put your comment there...
    *
    */
    public function done()
    {
        $this->daffny->done();
    }


    private function trimHtml($html)
    {
        $lines = explode("\n", $html);
        $linesTotal = count($lines);

        $newHtml = "";
        foreach ($lines as $i => $line)
        {
            $line = trim($line);
            if ($line == "") {
                continue;
            }

            $newHtml .= $line;
            if ($linesTotal > $i+1) {
                $newHtml .= "\n";
            }
        }

        return $newHtml;
    }

    private function ssl_redirect()
    {
        $act_arr = array(
            "sdocs", "getdocs"
        );

        $path = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

        // Redirect to 80 port
        if (!in_array($this->daffny->action, $act_arr) && $_SERVER['SERVER_PORT'] == 443) {
            //redirect("http://www.$path");
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: http://$path");
            exit();
        }


        if (!in_array($this->daffny->action, $act_arr) && $_SERVER['SERVER_PORT'] == 80) {
	        if ( !preg_match('/www/', $_SERVER['HTTP_HOST']) )
	        {
	            header("HTTP/1.1 301 Moved Permanently");
                header("Location: http://www.$path");
                exit();
	            //redirect("http://www.$path");
	        }
        }

        // Redirect to 443 port

        if (in_array($this->daffny->action, $act_arr) && $_SERVER['SERVER_PORT'] == 80) {
            //redirect("https://www.$path");
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: https://$path");
            exit();
        }
    }
}

?>