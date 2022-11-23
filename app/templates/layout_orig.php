<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>@title@</title>
    <link rel="stylesheet" href="<?php echo SITE_IN; ?>styles/styles.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo SITE_IN; ?>styles/default.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo SITE_IN; ?>styles/jquery-ui.css" type="text/css" />
    <script type="text/javascript">
		var BASE_PATH = '<?php echo SITE_IN; ?>';
	</script>
	<script type="text/javascript" src="<?php echo SITE_IN; ?>jscripts/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo SITE_IN; ?>jscripts/jquery.scrollTo.js"></script>
	<script type="text/javascript" src="<?php echo SITE_IN; ?>jscripts/jquery.maskMoney.js"></script>
	<script type="text/javascript" src="<?php echo SITE_IN; ?>jscripts/jquery.maskedinput-1.3.min.js"></script>
    <script type="text/javascript" src="<?php echo SITE_IN; ?>jscripts/jquery-ui.js"></script>
    <script type="text/javascript" src="<?php echo SITE_IN; ?>jscripts/functions.js"></script>
    <script type="text/javascript" src="<?php echo SITE_IN; ?>jscripts/app.js"></script>
    
	<script type="text/javascript" src="<?php echo SITE_IN; ?>jscripts/filters.js"></script>
	<script type="text/javascript" src="<?php echo SITE_IN; ?>jscripts/jquery.jBreadCrumb.1.1.js"></script>
	<link rel="stylesheet" href="<?php echo SITE_IN; ?>styles/menu_style.css" type="text/css" />
	<link href="<?php echo SITE_IN; ?>styles/BreadCrumb.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="<?php echo SITE_IN; ?>jscripts/jquery.printarea.js"></script>
<!-- 	<script type="text/javascript">
		$(document).ready(function(){
			jQuery("#breadCrumb").jBreadCrumb();
		});
	</script> -->
</head>
<body>
<div class="centering">
	<div class="toll">Toll free: <span class="green"><?=$this->daffny->cfg['phone']?></span></div>
    <div class="logo"><a href="#" title="FreightDragon"><img src="<?=SITE_IN?>images/logo.png" alt="FreightDragon" width="350" height="125" /></a></div>
    <div class="follow">
		<span style="padding-right: 10px;">
		<!-- Поместите этот тег туда, где должна отображаться кнопка +1. -->
		<g:plusone size="medium" annotation="none"></g:plusone>

		<!-- Поместите этот вызов функции отображения в соответствующее место. -->
		<script type="text/javascript">
			(function() {
				var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
				po.src = 'https://apis.google.com/js/plusone.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
			})();
		</script>
		</span>
	    <a href="http://www.facebook.com/freightdragon.social" target="_blank"><img src="<?=SITE_IN?>images/icons/facebook.png" alt="Facebook" width="32" height="32" /></a>
    	<a target="_blank" href="https://twitter.com/#!/Freight_Dragon"><img src="<?=SITE_IN?>images/icons/twitter.png" alt="Twitter" width="32" height="32" /></a>
	    <a target="_blank" href="http://www.youtube.com/user/FreightDragon"><img src="<?=SITE_IN?>images/icons/youtube.png" alt="You Tube" width="32" height="32" /></a>
    </div>
    <div class="clear">&nbsp;</div>
    @top_menu_block@
    <div class="clear"></div>
    <div class="content">
		@content@
	</div>
	<br /><br />
</div>
<div class="footer">
	<div class="centering">
    	<table width="100%" cellpadding="0" cellspacing="0" border="0">
        	<tr>
            	<td class="copyright" valign="top">
					&copy; Copyright  FreightDragon.com  <?=date("Y")==2011?'2011':'2011-'.date("Y")?>. All Rights Reserved.<br />
					<br />
					<a class="green" href="<?=getLink("terms")?>">Terms of Use</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<a class="green" href="<?=getLink("privacy")?>">Privacy Policy</a>
	            </td>
            	<td class="foot-logo" valign="top">
	            	<img src="<?=SITE_IN?>images/iphone.gif" alt="Freight Dragon on Iphone" width="245" height="90" />
            	</td>
            	<td class="foot-right" valign="top">
	            	<img src="<?=SITE_IN?>images/cards.gif" alt="Cards" width="194" height="24" />
	            	<br /><br />
	            	<img src="<?=SITE_IN?>images/godaddy.gif" alt="Cards" width="126" height="61" />
	            </td>
            </tr>
        </table>
    </div>
</div>
<script>
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	ga('create', 'UA-47691867-1', 'freightdragon.com');
	ga('send', 'pageview');

</script>
<script type="text/javascript">
var LHCChatOptions = {};
LHCChatOptions.opt = {widget_height:140,widget_width:300,popup_height:520,popup_width:500};
(function() {
var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
var refferer = (document.referrer) ? encodeURIComponent(document.referrer) : '';
var location  = (document.location) ? encodeURIComponent(document.location) : '';
po.src = '//freightdragon.com/livehelperchat/lhc_web/index.php/chat/getstatus/(click)/internal/(position)/bottom_right/(hide_offline)/true/(check_operator_messages)/true/(top)/350/(units)/pixels?r='+refferer+'&l='+location;
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
})();
</script>
</body>
</html>