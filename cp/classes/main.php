<?php

/* * *************************************************************************************************
 * Main CP Class                                                                 				   *
 *                                                                              					   *
 *                                                                                                  *
 * Client: 	FreightDragon                                                                          *
 * Version: 	1.0                                                                                    *
 * Date:    	2011-09-29                                                                             *
 * Author:  	C.A.W., Inc. dba INTECHCENTER                                                          *
 * Address: 	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076                             *
 * E-mail:	techsupport@intechcenter.com                                                           *
 * CopyRight 2011 FreightDragon. - All Rights Reserved                                              *
 * ************************************************************************************************** */

class CpMain extends AppMain {

    /**
     * put your comment there...
     *
     */
    public function run() {

        if (!isset($_SESSION["admin_here"])) {

            $_SESSION["admin_here"] = FALSE;
        }

        if (!$_SESSION['admin_here'] && $this->daffny->action != "login") {
            redirect(getLink("login"));
        }

        $this->daffny->load_config();

        require_once(ROOT_PATH . "app/classes/action.php");
        
        $Action = $this->daffny->runAction();

        if ($this->daffny->action == "login") {
            print $Action->out;
            return;
        }

        $title = $this->daffny->cfg['site_title'];
        if (isset($Action->title) && $Action->title != "") {
            $title .= " - " . $Action->title;
        }

        $hello_admin = "Welcome, ".$_SESSION['member']['first_name'].' '.$_SESSION['member']['last_name'];
        $hello_admin .= ' (<a href="' . getLink('logout') . '">Logout</a>)';
        $tpl_arr = array(
            'title' => $title
            , 'hello_admin' => $hello_admin
            , 'content' => $Action->out
        );

        print $this->daffny->tpl->build("layout", $tpl_arr);
    }

}

?>