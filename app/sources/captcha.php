<?php
require_once(ROOT_PATH.'libs/recaptcha/recaptchalib.php');

class AppCaptcha extends AppAction
{
	
    public function idx()
    {
        
		
		
		//$rand = strtoupper(randomkeys(6, 2));
        //$_SESSION['captcha_code'] = md5($rand);
		/*
        $image = imagecreate(63, 18);
        $bgColor = imagecolorallocate($image, 255, 255, 255); 
        $textColor = imagecolorallocate($image, 51, 102, 255); 
        imagestring($image, 6, 5, 2, $rand, $textColor); 
        
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 
        header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT"); 
        header("Cache-Control: no-store, no-cache, must-revalidate"); 
        header("Cache-Control: post-check=0, pre-check=0", false); 
        header("Pragma: no-cache"); 
        header('Content-type: image/jpeg');
        
        if (function_exists("imagejpeg"))
        {
            header("Content-Type: image/jpeg");
            imagejpeg($image, null, 15);
        }
        else if(function_exists("imagegif"))
        {
            header("Content-Type: image/gif");
            imagegif($image);
        }
        else if(function_exists("imagepng"))
        {
            header("Content-Type: image/x-png");
            imagepng($image);
        }
        
        imagedestroy($image);
        */
        exit();
    }
}

?>