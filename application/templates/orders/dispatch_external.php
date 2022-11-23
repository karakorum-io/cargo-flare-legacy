<link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/signature_tool.css"/>
<script type="text/javascript" src="<?= SITE_IN ?>jscripts/raphael.js"></script>
<script type="text/javascript" src="<?= SITE_IN ?>jscripts/signature_tool.js"></script>
<?php include_once(TPL_PATH . "signature_tool.php") ?>
<script type="text/javascript">
    signature_type = "dispatch";
</script>
<div style="padding-top:15px;">
    <?php include('order_menu.php');  ?>
</div>
<br/>
<h3>Dispatch Sheet for Order #<?= $this->entity->getNumber() ?></h3>
<script type="text/javascript" src="<?= SITE_IN ?>jscripts/application/orders/dispatch.js"></script>
<div id="dispatch_sheet_history_dialog" style="display: none;"></div>
<br/>
<div class="order-info">
    <?=$this->dispatch->getHtml($this);?>
</div>
<div class="dispatch_actions">
    <?=functionButton("Print", "printDispatchSheet($('.dispatch_table').outerHTML());")?>
    <?=functionButton("Send", "sendDispatchSheet();")?>
    <?php if ((int)$this->dispatch->carrier_id == 0 && $this->entity->status == Entity::STATUS_NOTSIGNED) { ?>
        <?= functionButton("Accept", "acceptDispatchSheet(" . $this->dispatch->id . ")") ?>
        <?= functionButton("Reject", "rejectDispatchSheet(" . $this->dispatch->id . ")") ?>
    <?php } ?>
    <?php if (in_array($this->entity->status, array(Entity::STATUS_NOTSIGNED, Entity::STATUS_DISPATCHED))) { ?>
        <?= functionButton("Cancel", "cancelDispatchSheet(" . $this->dispatch->id . ")") ?>
    <?php } ?>
    <?php if (isset($this->dispatch_history)) { ?>
        <h3 style="padding: 3px 0;margin: 0;">History:</h3>
        <?php foreach ($this->dispatch_history as $history_id => $history) { ?>
            <span class="like-link" onclick="dispatchSheetHistory('<?= $history_id ?>')"><?=$history?></span><br/>
        <?php } ?>
    <?php } ?>
</div>
<div class="clear"></div>