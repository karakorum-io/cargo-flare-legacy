<link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/signature_tool.css"/>
<script type="text/javascript" src="<?= SITE_IN ?>jscripts/raphael.js"></script>
<script type="text/javascript" src="<?= SITE_IN ?>jscripts/signature_tool_dispatch.js"></script>
<script type="text/javascript" src="<?= SITE_IN ?>jscripts/application/orders/dispatch.js"></script>

<?php include_once(TPL_PATH . "signature_tool_v2.php") ?>

<script type="text/javascript">signature_type = "dispatch";</script>

<div style="padding-top:15px;">
    <?php include('order_menu.php');  ?>
</div>

<br/>

<div class="modal bd-example-modal-xl fade" id="dispatch_sheet_history_dialog" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">History</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times" aria-hidden="true"></i></button>
            </div>
            <div class="modal-body">
                <div id="dispatch_sheet_history_dialog_content">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<br/>

<div class="row">
    <div class="col-12 col-sm-12">
        <h3>Dispatch Sheet for Order #<?= $this->entity->getNumber() ?></h3>
    </div>
	<div class="col-12 col-sm-9">
        <div class="content-wrapper-dispatch" style="padding:100px; border:1px solid #CCC;">
            <?=$this->dispatch->getHtml($this);?>
        </div>
    </div>
    <div class="col-12 col-sm-3 text-center">
        <?=functionButton("Print", "printDispatchSheet($('.dispatch_table').outerHTML());")?>
        <?php //functionButton("Send", "sendDispatchSheet();")?>
        <?php if (in_array($this->entity->status, array(Entity::STATUS_NOTSIGNED,Entity::STATUS_ACTIVE))) { ?>
            <?=functionButton("Send Link", "sendDispatchLink(" . $this->dispatch->id . ");")?>
        <?php }else{?>
            <?=functionButton("Send", "sendDispatchSheet();")?>
        <?php }?>
        <?php if ($this->entity->status == Entity::STATUS_NOTSIGNED) { //(int)$this->dispatch->carrier_id == 0 && ?>
            <?= functionButton("Accept", "acceptDispatchSheet(" . $this->dispatch->id . ")") ?>
            <?= functionButton("Reject", "rejectDispatchSheet(" . $this->dispatch->id . ")") ?>
        <?php } ?>
        <?php if (in_array($this->entity->status, array(Entity::STATUS_PICKEDUP, Entity::STATUS_DISPATCHED))) { //Entity::STATUS_NOTSIGNED,?>
            <?php
                $entityId = $_GET['id'];
            ?>
            <?= functionButton("Undispatch", '$operations.cancelDispatch('.$entityId.')') ?>
        <?php } ?>
        <br/><br/>
        <?php if (isset($this->dispatch_history)) { ?>
            <table class="table table-bordered">
                <tr><td>History</td></tr>
                <?php foreach ($this->dispatch_history as $history_id => $history) { ?>
                <tr><td><span class="like-link" onclick="dispatchSheetHistory('<?= $history_id ?>')"><?=$history?></span></td><tr>
                <?php } ?>
            </table>
        <?php } ?>
    </div>
</div>