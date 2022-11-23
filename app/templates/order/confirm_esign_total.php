<link rel="shortcut icon" href="<?php echo SITE_IN ?>styles/favicon.ico" />

@form@

<link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/jquery-ui.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/signature_tool.css"/>
<script type="text/javascript">var BASE_PATH = '<?=SITE_PATH?>';</script>
<script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.nimble.loader.js"></script>
<script type="text/javascript" src="<?=SITE_IN?>jscripts/raphael.js"></script>
<script type="text/javascript" src="<?=SITE_IN?>jscripts/signature_tool_ordersign.js"></script>
<script type="text/javascript">
    signature_type = "order_esign_total";
	sg_id = <?=$this->entity->id?>;
    hashvalue = '<?php print $_GET['hash'];?>';
	
    $(document).ready(function() {
        $('#sign_button').button().click(function() {
            $('#signature_tool').dialog('open');
            return false;
        });
    });
</script>