<?php
/***************************************************************************************************
* Search Class                                                                      *
*                                                                                                  *
* Client: 	FreightDragon                                                                    *
* Version: 	1.0                                                                                    *
* Date:    	2011-10-03                                                                             *
* Author:  	C.A.W., Inc. dba INTECHCENTER                                                          *
* Address: 	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076                             *
* E-mail:	techsupport@intechcenter.com                                                           *
* CopyRight 2011 FreightDragon. - All Rights Reserved                                        *
****************************************************************************************************/
class Appunsubscribe extends AppAction
{
    public function idx()
    {
    	$this->title = $this->getBreadCrumbs("Unsubscribe");
        $this->tplname = "unsubscribe";
        $this->title = $this->getBreadCrumbs("Unsubscribe");
		if (isset($_GET['email']) && $_GET['email'] !="" )
     	{
            $this->daffny->DB->update("subscribers", array("unsubscribed"=>1)," email='".quote($_GET['email'])."'");
            $this->setFlashInfo("The e-mail <strong>".htmlspecialchars(get_var("email"))."</strong> has been unsubscribed");
     	}else{
     		redirect(SITE_IN);
     	}
    }
}

?>