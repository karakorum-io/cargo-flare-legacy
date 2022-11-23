<?php

/***************************************************************************************************
* Settings CP Class                                                                 *
*                                                                              					   *
*                                                                                                  *
* Client: 	FreightDragon                                                                          *
* Version: 	1.0                                                                                    *
* Date:    	2011-10-03                                                                             *
* Author:  	C.A.W., Inc. dba INTECHCENTER                                                          *
* Address: 	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076                             *
* E-mail:	techsupport@intechcenter.com                                                           *
* CopyRight 2011 FreightDragon. - All Rights Reserved                                              *
****************************************************************************************************/

class CpSettings extends CpAction
{
    public $title = "Settings";
    public $tplname = "settings";

    /**
    * Index method
    *
    */
    public function idx()
    {
        if (isset($_POST['submit']))
        {
            $this->save();
        }

        $this->input = array();
        $tplVarNames = array();

        $settings = $this->daffny->DB->selectRows("*", "settings");
        foreach ($settings as $setting)
        {
            $tplVarNames[] = sprintf('@%s@', $setting['name']);
            $this->input[$setting['name']] = $setting['value'];

            switch ($setting['type'])
            {
                case 'textarea':
                    $this->form->TextArea($setting['name'], 50, 6, array(), $setting['title'], "<br />");
                    break;

                case 'textfield':
                    $this->form->TextField($setting['name'], 255, array(), $setting['title'], "<br />");
                    break;
            }
        }
        $this->daffny->tpl->tplVarNames = $tplVarNames;
    }

    /**
    * Save settings
    *
    */
    protected function save()
    {
        $omit = array("submit");

        foreach ($_POST as $k => $v)
        {
            if (in_array($k, $omit))
            {
                continue;
            }

            $this->daffny->DB->update("settings", array('value' => $v), "name = '{$k}'");
        }

        $this->setFlashInfo("Settings have been saved.");
        redirect(getLink("settings"));
    }
}

?>