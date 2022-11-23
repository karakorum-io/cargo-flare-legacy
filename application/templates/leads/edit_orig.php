<script type="text/javascript">
	var entityBlocked = <?=($this->entity->isBlocked())?'true':'false'?>;
	var entity_id = <?=$this->entity->id?>;
	var entity_number = '<?=$this->entity->getNumber()?>';
</script>
<script type="text/javascript" src="<?=SITE_IN?>jscripts/application/quotes/edit.js"></script>
<div style="padding-top:15px;">
<?php include('lead_menu.php');  ?>
</div>
<style type="text/css">
	.lead-edit .form-box-textfield {
		width: 210px;
	}
</style>
<?php include(ROOT_PATH.'application/templates/vehicles/edit_js.php');?>
<?php include(ROOT_PATH.'application/templates/vehicles/form.php');?>
<br/>
<h3>Edit Lead #<?= $this->entity->getNumber() ?></h3>
<div style="clear: both;"></div>
Complete the form below and click Save Lead when finished. Required fields are marked with a <span style="color:red;">*</span><br/><br/>
<?php if (!$this->entity->isBlocked()) : ?>
<form action="<?= getLink('leads/edit/id/'.$this->entity->id) ?>" method="post">
<?php endif; ?>
<input type="hidden" name="lead_id" value="<?= $this->entity->id ?>"/>
<div class="lead-info" style="float:none;">
	<p class="block-title">Shipper Information</p>
	<div>
		<table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table lead-edit" style="white-space:nowrap;">
			<tr>
				<td colspan="2">&#8203;</td>
				<td>@shipper_email@</td>
				<td>@shipper_address1@</td>
			</tr>
			<tr>
				<td>@shipper_fname@</td>
				<td>@shipper_phone1@</td>
				<td>@shipper_address2@</td>
			</tr>
			<tr>
				<td>@shipper_lname@</td>
				<td>@shipper_phone2@</td>
				<td>@shipper_city@</td>
			</tr>
			<tr>
				<td>@shipper_company@</td>
				<td>@shipper_mobile@</td>
				<td>@shipper_state@@shipper_zip@</td>
			</tr>
			<tr>
				<td>&nbsp;</td><td>&nbsp;</td>
				<td>@shipper_fax@</td>
				<td>@shipper_country@</td>
			</tr>
		</table>
	</div>
</div>
<br/>
<div class="lead-info" style="float:none;">
	<p class="block-title">Origin and Destination</p>
	<div>
		<table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table" style="white-space:nowrap;">
			<tr>
				<td><strong>From:</strong></td>
				<td>&nbsp;</td>
				<td><strong>To:</strong></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>@origin_city@</td>
				<td>@destination_city@</td>
			</tr>
			<tr>
				<td>@origin_state@@origin_zip@</td>
				<td>@destination_state@@destination_zip@</td>
			</tr>
			<tr>
				<td>@origin_country@</td>
				<td>@destination_country@</td>
			</tr>
		</table>
	</div>
</div>
<br/>
<div class="lead-info" style="float:none;">
	<p class="block-title">Shipping Information</p>
	<div>
		<table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table" style="white-space:nowrap;">
			<tr>
				<td>@shipping_est_date@</td>
				<td rowspan="3" valign="top">@shipping_notes@</td>
			</tr>
<!--			<tr>-->
<!--				<td>@shipping_vehicles_run@</td>-->
<!--			</tr>-->
			<tr>
				<td>@shipping_ship_via@</td>
			</tr>
		</table>
	</div>
</div>
<br/>
<div class="lead-info" style="float:none;">
	<p class="block-title">Vehicle Information</p>
	<div>
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="grid" style="white-space:nowrap;" id="vehicles-grid">
			<thead>
			<tr class="grid-head">
				<td class="grid-head-left">ID</td>
				<td>Year</td>
				<td>Make</td>
				<td>Model</td>
				<td>Type</td>
				<td>Vin #</td>
				<td>Lot #</td>
				<td>Inop</td>
				<td class="grid-head-right" width="60">Actions</td>
			</tr>
			</thead>
			<tbody>
			<?php if (count($this->vehicles) > 0) : ?>
				<?php foreach ($this->vehicles as $i => $vehicle) : ?>
				<tr class="grid-body<?=($i%2)?' even':''?>">
					<td class="grid-body-left"><?= $this->entity->getNumber() ?>-V<?= ($i+1) ?></td>
					<td><?= $vehicle->year ?></td>
					<td><?= $vehicle->make ?></td>
					<td><?= $vehicle->model ?></td>
					<td><?= $vehicle->type ?></td>
					<td><?= $vehicle->vin ?></td>
					<td><?= $vehicle->lot ?></td>
					<td><?= ($vehicle->inop == '1')?'Yes':'No' ?></td>
					<td align="center" class="grid-body-right">
						<?php if (!$this->entity->isBlocked()) { ?>
						<img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit" onclick="editVehicle(<?= $vehicle->id ?>)" class="action-icon"/>
						<img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete" onclick="deleteVehicle(<?= $vehicle->id ?>)" class="action-icon"/>
						<?php } else { ?>&nbsp;<?php } ?>
					</td>
				</tr>
					<?php endforeach; ?>
				<?php else : ?>
			<tr class="grid-body">
				<td colspan="8" align="center" class="grid-body-left grid-body-right"><i>No Vehicles</i></td>
			</tr>
				<?php endif; ?>
			</tbody>
		</table>
        <br />
		<?php if (!$this->entity->isBlocked()) : ?>
		<div><?= functionButton('Add Vehicle', 'addVehicle()') ?></div>
		<?php endif; ?>
	</div>
</div>
<br/>
<?php if (!$this->entity->isBlocked()) { ?>
<div style="float:right">
	<?= submitButtons(SITE_IN."application/leads/show/id/".$this->entity->id, "Save") ?>
</div>
</form>
<?php } ?>