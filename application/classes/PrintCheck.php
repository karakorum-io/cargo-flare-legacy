<?php
require_once ROOT_PATH . "libs/phpmailer/class.phpmailer.php";

class PrintCheck extends FdObject
{
    const TABLE = "app_payments_check";

    public function load($id = null)
    {
        parent::load($id);
        
    }
}
