<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>        
<meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>@title@</title>
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/styles.css"/>
        <link type="text/css" rel="stylesheet" id="arrowchat_css" media="all" href="/arrowchat/external.php?type=css" charset="utf-8" />
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/application.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/menu_style.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/default.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/jquery-ui.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/jquery.autocomplete.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/jquery.multiselect.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/jquery.ui.timepicker.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN; ?>styles/BreadCrumb.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN; ?>styles/chat.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN; ?>styles/sms.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN; ?>application/assets/css/hint.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN; ?>application/assets/navmenu/css/menu/core.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN; ?>application/assets/navmenu/css/menu/styles/navblue.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN; ?>review/assets/css/responsive.css"/>
        <!--[if (gt IE 9)|!(IE)]><!-->
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN; ?>application/assets/navmenu/css/menu/effects/navfade.css"/>
        <!--<![endif]-->

        <!-- This piece of code, makes the CSS3 effects available for IE -->
        <!--[if lte IE 9]>
                <script src="js/jquery-1.7.1.min.js" type="text/javascript" charset="utf-8"></script>
                <script src="js/menu.min.js" type="text/javascript" charset="utf-8"></script>
                <script type="text/javascript" charset="utf-8">
                        $(function() {
                                $("#menu").menu({'effect' : 'fade'});
                        });
                </script>
        <![endif]-->
        <script>
            var BASE_PATH = '<?php echo SITE_IN ?>';
        </script>
        <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery-ui.js"></script>
        <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.maskMoney.js"></script>
        <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.cookie.js"></script>
        <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.multiselect.min.js"></script>
        <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.nimble.loader.js"></script>
        <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/functions.js"></script>
        <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/application.js"></script>
        <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/notify.js"></script>
        <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/filters.js"></script>
        <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.maskedinput-1.3.min.js"></script>
        <script type="text/javascript" src="<?php echo SITE_IN; ?>jscripts/jquery.jBreadCrumb.1.1.js"></script>
        <script type="text/javascript" src="<?php echo SITE_IN; ?>jscripts/jquery.printarea.js"></script>
        <script type="text/javascript" src="<?php echo SITE_IN; ?>jscripts/input-mask.js" ></script>
    </head>
    <body style="zoom:<?= $zoom_level ?>%;">
        <div id="fd_alert" style="display:none;"></div>
        <div class="wrapper">
        <div <?= (isset($_GET['home'])) ? 'class="apl_centering"' : 'class="apl_centering_home container"' ?> >
            <div class="top-head-sec">
                <?php
                    $path =  SITE_IN.'uploads/company/'.$this->entity->parentid.'.jpg';                    
                ?>
              <!---<a href="<?php echo SITE_IN;?>">                  
                   <img id="headerImage" src="<?php echo $path; ?>" alt="<?php echo $this->daffny->cfg['site_title']; ?>" width="200" height="50"/>
              </a> --->               
          
            </div>
            <div id="sysmessage" class="sysmessage">
            </div>
            <div style="clear: left"></div>      
            @flash_message@
            @content@                     
        </div>
    </div>
    </body>
</html>