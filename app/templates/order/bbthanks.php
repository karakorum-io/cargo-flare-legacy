<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>Dispatch Sheet</title>
        <link rel="shortcut icon" href="<?php echo SITE_IN ?>styles/favicon.ico" />
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/styles.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/application.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/default.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/jquery-ui.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/jquery.ui.timepicker.css"/>
    </head>
    <body>
        <br/>
        <div style="padding:20px;width:840px;margin: 0 auto;">
            <h3>B2B shipping for Order #<?=$this->entity->getNumber()?></h3>
            <br/>
            <h3>Thank you for accepting <?php print $this->entity->getShipper()->company;?> B2B shipping order form.</h3>
            <div class="clear"></div>
        </div>
    </body>
</html>