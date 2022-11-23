<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$hook['pre_controller'] = array(
        'class'    => 'Log',
        'function' => 'log_request',
        'filename' => 'Log.php',
        'filepath' => 'hooks',
        'params'   => ""
);