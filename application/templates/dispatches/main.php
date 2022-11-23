<link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/signature_tool.css"/>
<script type="text/javascript" src="<?=SITE_IN?>jscripts/raphael.js"></script>
<script type="text/javascript" src="<?=SITE_IN?>jscripts/signature_tool.js"></script>
<script type="text/javascript">
    signature_type = "dispatch";
	$(document).ready(function() {
		$("#dispatch_sheet_dialog").dialog({
			autoOpen: false,
			width: 820,
			title: "DispatchSheet",
			resizable: false,
			draggable: true,
			modal: true,
			buttons: [{
				text: "Print",
				click: function() {
					var printWindow = window.open('', 'Dispatch Sheet', 'height=400,width=600');
					printWindow.document.write('<link href="<?=BASE_PATH?>styles/styles_print.css" rel="stylesheet" type="text/css" />');
					printWindow.document.write($(this).html());
					printWindow.print();
					printWindow.document.close();
				}
			},{
				text: "PDF",
				click: function() {
					var printWindow = window.open(BASE_PATH+'application/ajax/dispatch.php?action=getPDF&id='+dispatch_sheet_id, 'Dispatch Sheet', 'heaight=400,width=600');
				}
			}]
		});
	});
</script>
@signature_tool@
<div id="dispatch_sheet_dialog" style="display: none;"></div>
<div id="print_container" style="display:none"></div>
<div style="padding-top: 10px;">
    <?php include_once("menu.php"); ?>
    <div style="clear: both"></div>
    <br/>
    <table cellpadding="0" cellspacing="0" border="0" width="100%" class="grid">
        <tr class="grid-head">
            <td class="grid-head-left">Origination</td>
            <td>Destination</td>
            <td>Vehicle / Order ID</td>
            <td>Company / Pay</td>
            <td align="center">Ship On</td>
			<?php if (in_array($_GET['dispatches'], array('dispatched', 'pickedup'))) { ?>
            <td align="center">Change Status</td>
			<?php } ?>
            <td class="grid-head-right" align="center">Actions</td>
        </tr>
    <?php if (count($this->entities) == 0) : ?>
        <tr class="grid-body first-row">
            <td align="center" colspan="<?=(!in_array($_GET['dispatches'], array('dispatched', 'pickedup')))?'6':'7'?>" class="grid-body-left grid-body-right"><i>No records</i></td>
        </tr>
    <?php endif; ?>
    <?php foreach ($this->entities as $i => $entity) : ?>
	<?php $dispatch = $entity->getDispatchSheet(); ?>
        <tr class="grid-body<?=($i == 0 ? " first-row" : "")?>">
            <td class="grid-body-left">
                <span class="like-link" onclick="window.open('<?=$dispatch->getFromLink()?>', '_blank')">
                    <?=$dispatch->from_city?>, <?=trim($dispatch->from_state.' '.$dispatch->from_zip)?>
                </span>
            </td>
            <td>
                <span class="like-link" onclick="window.open('<?= $dispatch->getToLink() ?>', '_blank')">
                    <?=$dispatch->to_city?>, <?=trim($dispatch->to_state.' '.$dispatch->to_zip)?>
                </span>
            </td>
            <td>
				Order #<?=$dispatch->getOrder()->getNumber()?><br/>
				<?php $vehicles = $dispatch->getVehicles();?>
				<?php if (count($vehicles) == 1) : ?>
				<?php $vehicle = $vehicles[0]; ?>
				<?= $vehicle->make; ?> <?= $vehicle->model; ?><br/>
				<?= $vehicle->year; ?> <?= $vehicle->type; ?>&nbsp;<?=imageLink($vehicle->year." ".$vehicle->make." ".$vehicle->model." ".$vehicle->type)?><br/>
				<?php else : ?>
				<span class="like-link multi-vehicles">Multiple Vehicles</span>
				<div class="vehicles-info">
					<?php foreach($vehicles as $key => $vehicle) : ?>
					<div <?= ($key%2)?'style="background-color: #161616;padding: 5px;"':'style="background-color: #000;padding: 5px;"' ?>>
						<p><?= $vehicle->make ?> <?= $vehicle->model ?></p>
						<?= $vehicle->year ?> <?= $vehicle->type ?>&nbsp;<?=imageLink($vehicle->year." ".$vehicle->make." ".$vehicle->model." ".$vehicle->type)?>
						<br/>
					</div>
					<?php endforeach; ?>
				</div>
				<br/>
				<?php endif; ?>
			</td>
            <td><?=htmlspecialchars($dispatch->c_companyname)?><br/>$<?=$dispatch->getCarrierPay()?></td>
            <td align="center"><?=Entity::$date_type_string[$dispatch->entity_load_date_type]?>: <?=$dispatch->getPickupDate("m/d/Y")?></td>
			<?php if (in_array($_GET['dispatches'], array('dispatched', 'pickedup'))) { ?>
            <td align="center">
			<?php
				switch ($entity->status) {
					case Entity::STATUS_DISPATCHED:
						?><span class="like-link" onclick="setDispatchStatus(<?=$dispatch->id?>, 'pickedup')">Change to Picked Up</span><?
						break;
					case Entity::STATUS_PICKEDUP:
						?><span class="like-link" onclick="setDispatchStatus(<?=$dispatch->id?>, 'delivered')">Change to Delivered</span><?
						break;
				}
			?>
            </td>
			<?php } ?>
            <td class="grid-body-right" align="center" width="90">
			<?php if ($entity->status == Entity::STATUS_NOTSIGNED) { ?>
                <?=functionButton('Accept', 'acceptDispatchSheet('.$dispatch->id.')', 'width:80px;')?>
                <?=functionButton('Reject', 'rejectDispatchSheet('.$dispatch->id.')', 'width:80px;')?>
			<?php } ?>
				<?=functionButton('View', 'viewDispatchSheet('.$dispatch->id.')', 'width:80px;')?>
            </td>
        </tr>
    <?php endforeach; ?>
    </table>
</div>