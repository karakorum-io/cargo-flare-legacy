<link rel="shortcut icon" href="<?php echo SITE_IN ?>styles/favicon.ico" />
@form@
<link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/jquery-ui.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/signature_tool.css"/>
<script type="text/javascript">var BASE_PATH = '<?=SITE_PATH?>';</script>
<script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery-ui.js"></script>
<script type="text/javascript" src="<?=SITE_IN?>jscripts/signature_tool_ordersigncommercial.js"></script>
<script type="text/javascript">
   signature_type = "order";
	sg_id = <?=$this->entity->id?>;
   <?php if($this->errorMsg!=""){?>
      document.getElementById('errorMsg').style.display = 'block';
      document.getElementById('errorMsg').innerHTML = '<ul class="msg-list"><?php print $this->errorMsg;?></ul>';
   <?php }?>
   <?php if($this->successMsg!=""){?>
      document.getElementById('successMsg').style.display = 'block';
      document.getElementById('successMsg').innerHTML = '<ul class="msg-list"><?php print $this->successMsg;?></ul>';
      document.getElementById('signature_tool').style.display = 'none';
   <?php }?>
</script>