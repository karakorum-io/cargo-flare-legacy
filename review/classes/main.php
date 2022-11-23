<?php

class ReviewMain extends AppMain
{
    /**
    * put your comment there...
    *
    */
    public function run()
    {
        if (isGuest()) {
            redirect(SITE_IN."user/signin");
        } else if (!isset($_SESSION['owner'])) {
			$_SESSION['owner'] = $_SESSION['member_id'];
		}

		$this->daffny->load_config();

        require_once(ROOT_PATH."app/classes/action.php");
        $Action = $this->daffny->runAction();

        if ($this->daffny->action == "login")
        {
            print $Action->out;
            return;
        }
		//if (isset($_SESSION['is_frozen']) && $_SESSION['is_frozen'] && $this->daffny->action != "billing") {
		//	redirect(getLink("billing"));
		//}
                
                if (isset($_SESSION['logoutmetime']) && $_SESSION['logoutmetime'] < time()) {
                    
                    unset($_SESSION['logoutmetime']);
                    setcookie( 'email' );
                    setcookie( 'pass_hash' );
                    $_SESSION['err_message'] = "You have been logged out due to account settings 'Log out users after hrs. / min.'";
                    $this->daffny->auth->logout(SITE_IN."user/signin");
		}

        $title = $this->daffny->cfg['site_title'];
        if (isset($Action->title) && $Action->title != "") {
            $title .= " - ".$Action->title;
        }
		$section = "";
		if (isset($Action->section) && $Action->section != "") {
            $section = $Action->section;
        }
        $breadcrumbs = "";
        if (isset($Action->breadcrumbs) && $Action->breadcrumbs != "") {
            $breadcrumbs = $Action->breadcrumbs;
        }

        //$hello  = "Welcome, ". $_SESSION['member']['email'].".";
		$hello  = "Hello, ".$_SESSION['member']['contactname']."";
        $hello .= ' <strong>(<a href="'.SITE_IN.'user/signout">Logout</a>)</strong>';

        $tpl_arr = array (
			'title'			=> $title,
			'hello'			=> $hello,
			'section'		=> $section,
			'breadcrumbs'	=> $breadcrumbs,
			'content'		=> $Action->out,
			'task_minibox'	=> $this->daffny->tpl->build('task_minibox'),
			'flash_message' => $Action->input['flash_message']
        );
        if($section=="Chat")
			print $this->daffny->tpl->build("layout_chat", $tpl_arr);
		else	
			print $this->daffny->tpl->build("layout", $tpl_arr);
    }
}

?>