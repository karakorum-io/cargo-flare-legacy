<?php

class AppAccess_denied extends AppAction
{
    /**
    * put your comment there...
    *
    */
    public function idx()
    {
        if (!defined('SITE_CLOSED') || !SITE_CLOSED)
        {
            redirect(getLink());
        }

        $this->getForm();
        $this->construct();

        die($this->out);
    }

    /**
    * put your comment there...
    *
    */
    protected function getForm()
    {
        $this->tplname = "access_denied";

        $this->input = $this->SaveFormVars();
        $this->input['title'] = $this->daffny->cfg['site_title']." Login";
        $this->form->TextField("login", 50, array('style' => "width: 180px;"), "Login", "</td><td>");
        $this->form->PasswordField("password", 20, array('style' => "width: 180px;"), "Password", "</td><td>");

        if (!isset($_POST['submit']))
        {
            return;
        }

        $login = trim(post_var("login"));
        $password = trim(post_var("password"));

        if ($login != SITE_CLOSED_LOGIN && $password != SITE_CLOSED_PASSWORD)
        {
            $this->input['error'] = "Login or Password is invalid.";
            return;

        }

        $_SESSION['owner'] = 1;
        redirect(getLink());
    }
}

?>