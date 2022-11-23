<?php



/* * ************************************************************************************************

 * Cron RepostToCd

 *

 * Client:		FreightDragon

 * Version:		1.0

 * Date:			2011-04-26

 * Author:		C.A.W., Inc. dba INTECHCENTER

 * Address:	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076

 * E-mail:		techsupport@intechcenter.com

 * CopyRight 2011 FreightDragon. - All Rights Reserved

 * ************************************************************************************************** */

@session_start();



ob_start();

//set_time_limit(800);

//error_reporting(E_ALL | E_NOTICE);
//print "------";
require_once("init.php");
//print "-------";
$_SESSION['iamcron'] = true; // Says I am cron for Full Access



set_time_limit(800000);
ini_set('memory_limit', '3500M');
ini_set('upload_max_filesize', '128M');
ini_set('post_max_size', '128M');
ini_set('max_input_time', 800000);

$ID = (int)$_GET["id"];
//print "--".$ID;
        $file = $daffny->DB->select_one("*", "app_uploads", "WHERE id = '" . $ID . "' ");
        if (!empty($file)) {

            $file_path = UPLOADS_PATH . "entity/" . $file["name_on_server"];
            $file_name = $file["name_original"];
            $file_size = $file["size"];
            if (file_exists($file_path)) {
                if ( strtolower($file["type"]) == "pdf" ){
                    header("Content-Type: application/pdf; filename=\"" . $file_name . "\"");
                    //header("Content-Disposition: attachment; filename=\"" . $file_name . "\"");
                }else{
                    header("Content-Type: application; filename=\"" . $file_name . "\"");
                    header("Content-Disposition: attachment; filename=\"" . $file_name . "\"");
                }
                header("Content-Description: \"" . $file_name . "\"");
                header("Content-length: " . $file_size);
                header("Expires: 0");
                header("Cache-Control: private");
                header("Pragma: cache");
                $fptr = @fopen($file_path, "r");
                $buffer = @fread($fptr, filesize($file_path));
                @fclose($fptr);
                echo $buffer;
                exit(0);
            }
        }
        header("HTTP/1.0 404 Not Found");
        exit(0);



$_SESSION['iamcron'] = false;


		
//send mail to Super Admin

    //require_once("done.php");

?>