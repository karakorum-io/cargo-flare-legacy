<style>
	.dropdown-item{
		height:65px !important;
	}
</style>	
<script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.11.4/ckeditor.js"></script>
<script type="text/javascript">
	var entityBlocked = <?=($this->entity->isBlocked())?'true':'false'?>;
	var entity_id = <?=$this->entity->id?>;
	function typeselected()
	{
	if($("#shipper_type").val() == "Commercial")
	  $('#shipper_company-span').show();
	else
	  $('#shipper_company-span').hide();

	}
	alert("working here");
</script>
<div style="padding-top:15px;">
<?php include('quote_menu.php');  ?>
</div>
<?php include(ROOT_PATH.'application/templates/vehicles/edit_js.php');?>
<?php include(ROOT_PATH.'application/templates/vehicles/form.php');?>
<div style="clear:both;"></div>
<br/>
<h3>Edit Quote #<?= $this->entity->getNumber() ?></h3>
Complete the form below and click Save Quote when finished. Required fields are marked with a <span style="color:red;">*</span><br/><br/>
<?php if (!$this->entity->isBlocked()) { ?>
<form action="<?= getLink('quotes/edit/id/'.$this->entity->id) ?>" method="post">
<?php } ?>
<input type="hidden" name="quote_id" value="<?= $this->entity->id ?>"/>
<div class="quote-info" style="float:none;">
	<p class="block-title">Shipper Information</p>
	<div>
		<table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table quote-edit" style="white-space:nowrap;">
			<tr>
				<td>@shipper@</td>
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
<div class="quote-info" style="float:none;">
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
<div class="quote-info" style="float:none;">
	<p class="block-title">Shipping Information</p>
	<div>
		<table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table" style="white-space:nowrap;">
			<tr>
				<td>@shipping_est_date@</td>
				<td rowspan="3" valign="top">@shipping_notes@</td>
			</tr>
			<tr>
				<td>@shipping_vehicles_run@</td>
			</tr>
			<tr>
				<td>@shipping_ship_via@</td>
			</tr>
		</table>
	</div>
</div>
<br/>
<div class="quote-info" style="float:none;">
	<p class="block-title">Vehicle Information</p>
	<div id="vehicle_information_info" style="padding-left:20px;padding-right:20px;">
				
				<table class="table table-bordered" id="vehicles-grid">
					<thead>
						<tr>
							<th>ID</th>
							<th>Year</th>
							<th>Make</th>
							<th>Model</th>
							<th>Type</th>
							<th>Vin #</th>
							<th>Total Tariff</th>
							<th>Deposit</th>
							<th>Inop</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php if (count($this->vehicles) > 0) : ?>
							<?php foreach ($this->vehicles as $i => $vehicle) : ?>
								<tr class="grid-body<?= ($i % 2) ? ' even' : '' ?>">
									<td class="grid-body-left"><?= $this->entity->id ?>-V<?= ($i + 1) ?></td>
									<td id="v-year-<?php print $vehicle->id;?>"><?= $vehicle->year ?></td>
									<td id="v-make-<?php print $vehicle->id;?>"><?= $vehicle->make ?></td>
									<td id="v-model-<?php print $vehicle->id;?>"><?= $vehicle->model ?></td>
									<td id="v-type-<?php print $vehicle->id;?>"><?= $vehicle->type ?></td>

									<td id="v-vin-<?php print $vehicle->id;?>"><input type="text" class="form-control" name="vin[<?php print $vehicle->id;?>]" value="<?= $vehicle->vin ?>" id="vin_<?php print $vehicle->id;?>"  /></td>

									<td id="v-tariff-<?php print $vehicle->id;?>"><input type="text"  class="form-control" name="vehicle_tariff[<?php print $vehicle->id;?>]" value="<?= $vehicle->tariff ?>" id="vehicle_tariff_<?php print $vehicle->id;?>" onkeyup="updatePricingInfo();" />

									</td>
									<td id="v-deposit-<?php print $vehicle->id;?>"><input type="text"  class="form-control" name="vehicle_deposit[<?php print $vehicle->id;?>]" value="<?= $vehicle->deposit ?>" id="vehicle_deposit_<?php print $vehicle->id;?>" onkeyup="updatePricingInfo();"/>
										<input type="hidden" class="form-control" name="vehicle_id[]" value="<?php print $vehicle->id;?>"  />
									</td>
									<td id="v-inop-<?php print $vehicle->id;?>"><?= ($vehicle->inop == '1')?'Yes':'No' ?></td>

									<td align="center" class="grid-body-right" >
										<?php  if($accessEdit==1){?>
											<?php if (!$this->entity->isBlocked()) { ?>
												<img src="<?= SITE_IN ?>images/icons/copy.png" alt="Copy" title="Copy" onclick="copyVehicle(<?= $vehicle->id ?>)" class="action-icon"/>
												<img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit" onclick="editVehicle(<?= $vehicle->id ?>)" class="action-icon"/>
												<img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete" onclick="deleteVehicle(<?= $vehicle->id ?>)" class="action-icon"/>
											<?php } else { ?>
												&nbsp;
											<?php } ?>
										<?php }else{ ?>
											<img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit" class="action-icon"/>
											<img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete" class="action-icon"/>
										<?php }?>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php else : ?>
						<tr class="grid-body">
							<td  style="text-align: center;"><i>No Vehicles</i></td>
						</tr>
						<?php endif; ?>
					</tbody>
				</table>

				<?php if($accessEdit==1){ ?>
				<div class="row mb-3">
					<div class="col-12 text-right">
						<?php if (!$this->entity->isBlocked()) { ?>
						<div><?= functionButton('Add Vehicle', 'addVehicle()','','btn-sm btn_dark_blue') ?></div>
						<?php } ?>
					</div>
				</div>

				<div class="row mb-3">
					<div class="col-12">
						<?php if ($this->isAutoQuoteAlowed) { ?>
						<!----<div><?= functionButton('Quick Price', 'quickPrice()') ?></div>---->
						<?php } ?>					
					</div>
				</div>
				
				<?php }else{ ?>
					
				<div class="row mb-3">
					<div class="col-12 text-right">
						<?php if (!$this->entity->isBlocked()) { ?>
						<div><?= functionButton('Add Vehicle', '') ?></div>
						<?php } ?>
					</div>
				</div>

				<div class="row mb-3">
					<div class="col-12">
						<?php if ($this->isAutoQuoteAlowed) { ?>
						<div><?= functionButton('Quick Price', '') ?></div>
						<?php } ?>
					</div>
				</div>
				
				<?php }?>
				
			</div>
</div>
<br/>
<div class="quote-info" style="float:none;">
	<p class="block-title">Pricing Information</p>
	<div>
		<table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table" style="white-space:nowrap;">
			<tr>
				<td>Total Tariff</td>
				<td><span id="total_tariff">@total_tariff@</span></td>
				<td><span class="grey-comment">(Edit tariff under the "Vehicle Information" section)</span></td>
			</tr>
			<tr>
				<td>Required Deposit</td>
				<td><span id="required_deposit">@total_deposit@</span></td>
				<td><span class="grey-comment">(Edit deposit under the "Vehicle Information" section)</span></td>
			</tr>
		</table>
	</div>
</div>
<br/>
<div class="quote-info" style="float:none;">
	<p class="block-title">Additional Information</p>
	<div>
		<table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table" style="white-space:nowrap;">
			<tr>
				<td>@referred_by@</td>
			</tr>
		</table>
	</div>
</div>
<br/>
<?php if (!$this->entity->isBlocked()) { ?>
<div style="float:right">
	<td><?= submitButtons(SITE_IN."application/quotes/show/id/".$this->entity->id, "Save") ?></td>
</div>
</form>
<?php } ?>