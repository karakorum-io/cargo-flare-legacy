<script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.11.4/ckeditor.js"></script>

<?php if ($_GET['quotes'] == 'create') { ?>

<script type="text/javascript">
	var v = <?= (isset($_POST['year'])) ? count($_POST['year']) : 0 ?>;

	function createAndEmail() {
		$("#co_send_email").val("1");
		$("#submit_button").click();
	}

	var tempDisableBeforeUnload = false;

	$(window).bind('beforeunload', function () {

		if (($("#shipper_fname").val() != '' ||
				$("#shipper_lname").val() != '' ||
				$("#shipper_company").val() != '' ||
				$("#shipper_email").val() != '') && !tempDisableBeforeUnload)
		{
			return 'Leaving this page will lose your changes. Are you sure?';
		} else {
			tempDisableBeforeUnload = false;
			return;

		}
	});

	function typeselected() {
		if ($("#shipper_type").val() == "Commercial"){
			$("label[for='shipper_company']").html("<span class='text-danger'>*</span> Company:");
		} else {
			$("label[for='shipper_company']").html("Company:");
		}
	}

	$(document).ready(function () {
		$('#shipper_company-span').hide();
		typeselected();

		var createForm = $('#create_form');
		createForm.find("input.shipper_company-model").typeahead({
			source: function (request, result) {
				$.ajax({
					url: '<?= SITE_IN ?>application/ajax/autocomplete.php',
					type: 'GET',
					dataType: 'json',
					data: {
					term: request.term,
					action: 'getCompany'
					},
					success: function(data) {
				result($.map(data, function (item) {
						return item;
					}));
					}
					})
			},
			minLength: 0,
			autoFocus: true,
			change: function () {
				var shipper_company = createForm.find("input.shipper_company-model").val();
				if (shipper_company != "") {
					$.ajax({
						url: '<?= SITE_IN ?>application/ajax/autocomplete.php',
						type: 'GET',
						dataType: 'json',
						data: {
							term: shipper_company,
							action: ''
						},
						success: function (data) {
							if (Object.keys(data).length > 0) {
								if (data.first_name != 'N/A' && data.first_name != '' && data.first_name != null){
									$("#shipper_fname").val(data.first_name);
								}
								
								if (data.last_name != 'N/A' && data.last_name != '' && data.last_name != null){
									$("#shipper_lname").val(data.last_name);
								}

								$("#shipper_email").val(data.email);
								$("#shipper_phone1").val(data.phone1);
								$("#shipper_phone2").val(data.phone2);
								$("#shipper_mobile").val(data.cell);
								$("#shipper_fax").val(data.fax);
								$("#shipper_address1").val(data.address1);
								$("#shipper_address2").val(data.address2);
								$("#shipper_city").val(data.city);
								$("#shipper_country").val(data.country);
								$("#shipper_state").val(data.state);
								$("#shipper_zip").val(data.zip_code);
								$("#shipper_type").val(data.shipper_type);
								if (data.referred_id != 0 && data.referred_by != '' && data.referred_id != null) {
									$("#referred_by").empty();
									$("#referred_by").append($("<option></option>").attr("value", data.referred_id).text(data.referred_by));
								} else {
									$("#referred_by").empty();
									$("#referred_by").append($("<option></option>").attr("value", '').text('Select One'));
									<?php
										foreach ($this->referrers_arr as $key => $referrer) {
									?>
									$("#referred_by").append($("<option></option>").attr("value", '<?php print $key; ?>').text('<?php print $referrer; ?>'));
									<?php
										}
									?>
								}
							} else {
								$("#referred_by").empty();
								$("#referred_by").append($("<option></option>").attr("value", '').text('Select One'));
								<?php
									foreach ($this->referrers_arr as $key => $referrer) {
								?>
								$("#referred_by").append($("<option></option>").attr("value", '<?php print $key; ?>').text('<?php print $referrer; ?>'));
								<?php
									}
								?>
							}
						}
					});
				}
			}
		});
	});
</script>
<script type="text/javascript" src="<?= SITE_IN ?>jscripts/application/quotes/create.js"></script>

<?php } else { ?>

<script type="text/javascript">
	var entityBlocked = <?= ($this->entity->isBlocked()) ? 'true' : 'false' ?>;
	var entity_id = <?= $this->entity->id ?>;
	var entity_number = '<?= $this->entity->getNumber() ?>';
</script>
<script type="text/javascript" src="<?= SITE_IN ?>jscripts/application/quotes/edit.js"></script>

<?php } ?>

<script type="text/javascript" src="<?= SITE_IN ?>jscripts/application/quotes/create_edit.js"></script>

<?php if ($_GET['quotes'] == 'create') { ?>

    <?php include(ROOT_PATH . 'application/templates/vehicles/create_js.php'); ?>

<?php } else { ?>

<div style="padding-top:15px;">
	<?php include('quote_menu.php'); ?>
</div>
<?php include(ROOT_PATH . 'application/templates/vehicles/edit_js.php'); ?>

<?php } ?>

<?php include(ROOT_PATH . 'application/templates/vehicles/form.php'); ?>
<br/>

<label class="btn btn-bold btn-label-warning text-left" style="margin-bottom:15px;">
	Complete the form below and click&nbsp;&nbsp;
	<span style="display:inline-block;color:green;"><strong>Save Quote</strong></span>
	&nbsp;&nbsp;when finished. Required fields &nbsp;&nbsp;<span style="display:inline-block;color:green;"><strong>Shipper type&nbsp;,&nbsp;First Name &nbsp;,&nbsp;Source</strong></span>&nbsp;&nbsp; are marked with a 
	<span style="display:inline-block;color:red;">&nbsp;&nbsp;* </span>
</label>

<div style="clear:both;"></div>
&nbsp;&nbsp; Please select a repeat customers profile here &nbsp;&nbsp;<?= ($_GET['quotes'] == 'create') ? functionButton('My Shippers', 'selectShipper()','','btn-sm btn_dark_blue ') : '&nbsp;' ?>

<input type="hidden" id="quote_id" value="<?= (isset($_GET['id']) ? $_GET['id'] : '') ?>"/>

<form action="<?= ($_GET['quotes'] == 'create') ? getLink('quotes/create') : getLink('quotes/edit/id/' . $_GET['id']) ?><?php echo isset($_GET['convert']) ? '?convert' : '' ?>" method="post" onsubmit="javascript:tempDisableBeforeUnload = true;"  id="create_form">
    <?php if (isset($_GET['convert'])) { ?>
        <input type="hidden" name="convert" value="1"/>
    <?php } ?>

	<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5 mt-3">
		<div class="row">			
			<div class="col-12">
				<div class="kt-portlet__head hide_show"  >
					<div class="kt-portlet__head-label  " >
						<h3 class="shipper_detail" id="Shipper_Information" >
							Shipper Information
						</h3>
					</div>
				</div>
				<div class="kt-portlet__body pt-3 pb-4" style="padding-left:20px;padding-right:20px;">
			<div class="row">
				<div class="col-12 col-sm-4">
					<div class="new_form-group">
						@shipper_type@
					</div>
				</div>
				<div class="col-12 col-sm-4">
					<div class="new_form-group">
						@shipper_email@
					</div>
				</div>
				<div class="col-12 col-sm-4">
					<div class="new_form-group">
						@shipper_address1@
						<div id="suggestions-box-shipper" class="suggestions"></div>
					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-12 col-sm-4">
					<div class="new_form-group">
						@shipper_fname@
					</div>
				</div>
				<div class="col-12 col-sm-4">
					<div class="new_form-group">
						@shipper_phone1@
					</div>
				</div>
				<div class="col-12 col-sm-4">
					<div class="new_form-group">
						@shipper_address2@
					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-12 col-sm-4">
					<div class="new_form-group">
						@shipper_lname@
					</div>
				</div>
				<div class="col-12 col-sm-4">
					<div class="new_form-group">
						@shipper_phone2@						
					</div>
				</div>
				<div class="col-12 col-sm-4">
					<div class="new_form-group ">
						@shipper_city@						
					</div>
				</div>
			</div>		
			
			<div class="row">
				<div class="col-12 col-sm-4">
					<div class="new_form-group">
						@shipper_company@
					</div>
				</div>
				<div class="col-12 col-sm-4">
					<div class="new_form-group pl-1">
						@shipper_mobile@						
					</div>
				</div>
				<div class="col-12 col-sm-4">
					<div class="new_form-group">
						<div id="shipper_state_div">
							@shipper_state@
						</div>
						@shipper_zip@
						<div id="notes_container"></div>
					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-12 col-sm-4">
					<div class="new_form-group">
						@shipper_hours@
					</div>
				</div>
				<div class="col-12 col-sm-4">
					<div class="new_form-group">
						@shipper_fax@						
					</div>
				</div>
				<div class="col-12 col-sm-4">
					<div class="new_form-group">
						@shipper_country@
					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-12 col-sm-4">
					<div class="new_form-group_1 new_form_info_new">
						@shipper_add@
					</div>
				</div>
				<div class="col-12 col-sm-4">
					<div class="new_form-group">
						@referred_by@
					</div>
				</div>
			</div>
		</div>
				<!---
				<div class="kt-portlet__body pt-3 pb-3" style="padding-left:20px;padding-right:20px;">
					<div class="row">
						<div class="col-12 col-sm-4 pb-2" style="margin-top:12px;">
							<?= ($_GET['quotes'] == 'create') ? functionButton('Select Shipper', 'selectShipper()','','btn-sm btn_dark_blue ') : '&nbsp;' ?>
						</div>
						<div class="col-12 col-sm-4">
							<div class="new_form-group">
								@shipper_email@
							</div>
						</div>
						<div class="col-12 col-sm-4">
							<div class="new_form-group">
								@shipper_address1@
								<div id="suggestions-box-shipper" class="suggestions"></div>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-12 col-sm-4">
							<div class="new_form-group">
								@shipper_fname@
							</div>
						</div>
						<div class="col-12 col-sm-4">
							<div class="new_form-group">
								 @shipper_phone1@
							</div>
						</div>
						<div class="col-12 col-sm-4">
							<div class="new_form-group">
								@shipper_address2@
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-12 col-sm-4">
							<div class="new_form-group">
								@shipper_lname@
							</div>
						</div>
						<div class="col-12 col-sm-4">
							<div class="new_form-group">
								 @shipper_phone2@ 
							</div>
						</div>
						<div class="col-12 col-sm-4">
							<div class="new_form-group">
								@shipper_city@
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-12 col-sm-4">
							<div class="new_form-group">
								@shipper_company@
							</div>
						</div>
						<div class="col-12 col-sm-4">
							<div class="new_form-group">
							 @shipper_mobile@ 
							</div>
						</div>
						<div class="col-12 col-sm-4">
							<div class="new_form-group">
								<div id="shipper_state_div">
									@shipper_state@
								</div>
								@shipper_zip@
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-12 col-sm-4">
							<div class="new_form-group ">
								@shipper_type@
							</div>
						</div>
						<div class="col-12 col-sm-4">
							<div class="new_form-group">
								@shipper_fax@
							</div>
						</div>
						<div class="col-12 col-sm-4">
							<div class="new_form-group ">
								@shipper_country@
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-12 col-sm-4">
							<div class="new_form-group">
								@shipper_hours@
							</div>
						</div>
						<div class="col-12 col-sm-4 ">
							<div class="new_form-group ">
								@referred_by@
							</div>
						</div>
						<div class="col-12 col-sm-4">
							
						</div>
					</div>

				</div>	---->						
			</div>
		</div>
    </div>

	<label class="btn btn-bold btn-label-warning text-left" style="margin-bottom:15px;">
        Required fields &nbsp;&nbsp;<span style="display:inline-block;color:green;"><strong>City&nbsp;,&nbsp;State&nbsp;,&nbsp;Zip</strong></span>&nbsp;&nbsp; are marked with a 
        <span style="display:inline-block;color:red;">&nbsp;&nbsp;* </span>
    </label>
    <div style="clear:both;"></div>
    &nbsp;&nbsp; If the prospect does not know zip code information you can over-ride the requirement with <span style="display:inline-block;color:green;"><strong>00000</strong></span>
	<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5 mt-3" >
		<div class="row">			
			<div class="col-12">
			
				<div class="kt-portlet__head hide_show" id="accordion_title">
					<div class="kt-portlet__head-label">
						<h3 class="shipper_detail">Origin and Destination</h3>
					</div>
				</div>
				
				<div class="pt-3 pb-3" style="padding-left:20px;padding-right:20px;" >
					
					<div class="row">
						<div class="col-12 col-sm-6">
							<div class="row form-group">
								<div class="col-12">
									<label>From:</label>
									<?php if ($_GET['quotes'] == 'create') { ?><span class="kt-link" onclick="setLocationSameAsShipper('origin')">same as shipper</span><?php } ?>
								</div>
							</div>
						</div>
						<div class="col-12 col-sm-6">
							<div class="row form-group">
								<div class="col-12">
									<label>To:</label>
									<?php if ($_GET['quotes'] == 'create') { ?><span class="kt-link" onclick="setLocationSameAsShipper('destination')">same as shipper</span><?php } ?>
								</div>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-12 col-sm-6">
							<div class="new_form-group">
								@origin_city@
							</div>
						</div>
						<div class="col-12 col-sm-6">
							<div class="new_form-group">
								@destination_city@
							</div>
						</div>
					</div>
					
					
					<div class="row">
						<div class="col-12 col-sm-6">
						<div class="new_form-group ">
							<div class="row">
								<div class="col-10">
									<div id="origin_state_div">
										@origin_state@
									</div>	
								</div>

								<div class="col-2 " style="margin-left: -22px">
									 @origin_zip@
								</div>
							</div>
							
						</div>
						</div>
						<div class="col-12 col-sm-6">
							<div class="new_form-group ">

								<div class="row">
								<div class="col-10">
									<div id="destination_state_div">
										@destination_state@
									</div>
								</div>

								<div class="col-2">
									@destination_zip@
								</div>
							</div>
								
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-12 col-sm-6">
							<div class="new_form-group">
								@origin_country@
							</div>
						</div>
						<div class="col-12 col-sm-6">
							<div class="new_form-group ">
								@destination_country@
							</div>
						</div>
					</div>
					
				</div>
				
			</div>
		</div>
	</div>
	
	<label class="btn btn-bold btn-label-warning text-left" style="margin-bottom:15px;">
        Required fields &nbsp;&nbsp;<span style="display:inline-block;color:green;"><strong>Estimated Ship Date&nbsp;,&nbsp;Ship Via </strong></span>&nbsp;&nbsp; are marked with a 
        <span style="display:inline-block;color:red;">&nbsp;&nbsp;* </span>
    </label>
    
	<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
		<div class="row">			
			<div class="col-12">
				
					<div class="kt-portlet__head hide_show" id="">
						<div class="kt-portlet__head-label">
							<h3 class="shipper_detail">
								Shipping Information
							</h3>
						</div>
					</div>
					
			   <div  class="kt-portlet__body pt-3 pb-3" style="padding-left:20px;padding-right:20px;" >
						<div class="row">
							<div class="col-12 col-sm-4">
								<div class="new_form-group">
									@shipping_est_date@
								</div>							
								<div class="new_form-group ">
									@shipping_ship_via@
								</div>
							</div>
							<div class="col-12 col-sm-8  shipping_notes_textarea_new">
								<div class="new_form-group">
									@shipping_notes@
								</div>
							</div>
						</div>				
					</div>
					
						
			</div>
		</div>
	</div>

	<label class="btn btn-bold btn-label-warning text-left" style="margin-bottom:15px;">
        Required fields &nbsp;&nbsp;<span style="display:inline-block;color:green;"><strong>Year&nbsp;,&nbsp;Make&nbsp;,&nbsp;Model&nbsp;,&nbsp;Type&nbsp;,&nbsp;Total Pay&nbsp;,&nbsp;Deposit  </strong></span>&nbsp;&nbsp; are marked with a 
        <span style="display:inline-block;color:red;">&nbsp;&nbsp;* </span>
    </label>
    
	<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
		<div class="row">			
			<div class="col-12">
				
				<div class="kt-portlet__head hide_show" id="">
					<div class="kt-portlet__head-label">
						<h3 class="shipper_detail">Vehicle Information</h3>
					</div>
				</div>
				
				<div class="kt-portlet__body pt-3 pb-3" style="padding-left:20px;padding-right:20px;">
					<div class="table-responsive">
						<?php if ($_GET['quotes'] == 'create') { ?>
						<table class="table table-bordered table-hover" id="vehicles-grid">
							<thead>
								<tr>
									<th>Year</th>
									<th>Make</th>
									<th>Model</th>
									<th>Type</th>
									<th>Vin #</th>
									<th>Total Tariff</th>
									<th>Deposit</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php if (isset($_POST['year'])) : ?>
									<?php foreach ($_POST['year'] as $i => $year) : ?>
										<tr class="grid-body" rel="<?= $i + 1 ?>">
											<td class="grid-body-left" align="center"><input type="hidden" name="year[]" class="form-box-combobox" value="<?= $year ?>"/><?= $year ?></td>
											<td align="center"><input type="hidden" name="make[]" value="<?= $_POST['make'][$i] ?>" class="form-box-combobox" /><?= $_POST['make'][$i] ?></td>
											<td align="center"><input type="hidden" name="model[]" value="<?= $_POST['model'][$i] ?>" class="form-box-combobox" /><?= $_POST['model'][$i] ?></td>
											<td align="center"><input type="hidden"class="form-box-combobox"  name="type[]" value="<?= $_POST['type'][$i] ?>"/><?= $_POST['type'][$i] ?>
												<input type="hidden"  class="" name="state[]" value="<?= $_POST['state'][$i] ?>"/>
												<input type="hidden"   name="plate[]" value="<?= $_POST['plate'][$i] ?>"/>
												<input type="hidden"   name="color[]" value="<?= $_POST['color'][$i] ?>"/>
												<input type="hidden"  class="form-box-combobox" name="carrier_pay[]" value="<?= $_POST['carrier_pay'][$i] ?>"/>
												<input type="hidden" class="form-box-combobox" name="inop[]" value="<?= $_POST['inop'][$i] ?>"/>
											<!--input type="hidden" name="tariff[]" value="<?= $_POST['tariff'][$i] ?>"/>
											<input type="hidden" name="deposit[]" value="<?= $_POST['deposit'][$i] ?>"/-->
											</td>
											<td align="center"><input type="text" class="form-box-combobox" name="vin[]" value="<?= $_POST['vin'][$i] ?>"  /></td>
											<td align="center"><input type="text" class="form-box-combobox" name="tariff[]" value="<?= $_POST['tariff'][$i] ?>"  onkeyup="updatePricingInfo();"/></td>
											<td align="center"><input type="text" class="form-box-combobox" name="deposit[]" value="<?= $_POST['deposit'][$i] ?>"  onkeyup="updatePricingInfo();"/>
												<!--td align="center"><input type="hidden" name="vin[]" value="<?= $_POST['vin'][$i] ?>"/><?= $_POST['vin'][$i] ?></td-->
												<!--td align="center"><input type="hidden" name="lot[]" value="<?= $_POST['lot'][$i] ?>"/><?= $_POST['lot'][$i] ?></td-->
												<!--td align="center"><?= ($_POST['inop'][$i]) ? 'Yes' : 'No' ?></td-->
											<td class="grid-body-right" align="center">
												<img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit" onclick="editVehicle(<?= $i + 1 ?>)" />
												<img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete" onclick="deleteVehicle(<?= $i + 1 ?>)" />
											</td>
										</tr>
									<?php endforeach; ?>
								<?php endif; ?>
							</tbody>
						</table>
						<?php } else { ?>
						<table class="table table-bordered table-hover" id="vehicles-grid">
							<thead>
								<tr>
									<td>ID</td>
									<td>Year</td>
									<td>Make</td>
									<td>Model</td>
									<td>Type</td>
									<td>Vin #</td>
									<td>Total Tariff</td>
									<td>Deposit</td>
									<td>Inop</td>
									<td>Actions</td>
								</tr>
							</thead>
							<tbody>
								<?php if (count($this->vehicles) > 0) : ?>
									<?php foreach ($this->vehicles as $i => $vehicle) : ?>
										<tr>
											<td><?= $this->entity->getNumber() ?>-V<?= ($i + 1) ?></td>
											<td><?= $vehicle->year ?></td>
											<td><?= $vehicle->make ?></td>
											<td><?= $vehicle->model ?></td>
											<td><?= $vehicle->type ?></td>
											<td align="center"><input type="text" name="vin[]" value="<?= $_POST['vin'][$i] ?>" class="form-box-combobox" /></td>
											<td align="center"><input type="text" name="tariff[]" value="<?= $_POST['tariff'][$i] ?>" class="form-box-combobox"  onkeyup="updatePricingInfo();"/></td>
											<td align="center"><input type="text" class="form-box-combobox" name="deposit[]" value="<?= $_POST['deposit'][$i] ?>"  onkeyup="updatePricingInfo();"/>
											<td><?= $vehicle->inop ? 'Yes' : 'No' ?></td>
											<td align="center" class="grid-body-right">
												<?php if (!$this->entity->isBlocked()) { ?>
													<img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit" onclick="editVehicle(<?= $vehicle->id ?>)" />
													<img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete" onclick="deleteVehicle(<?= $vehicle->id ?>)" />
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
						<?php } ?>
					</div>
					<div class="text-right"><?= functionButton('Add Vehicle', 'addVehicle()','','btn-sm btn_dark_blue') ?></div>
				</div>						
			</div>
		</div>
	</div>

	<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
		<div class="row">			
			<div class="col-12">

				<div class="kt-portlet__head hide_show" id="">
					<div class="kt-portlet__head-label">
						<h3 class="shipper_detail">Pricing Information</h3>
					</div>
				</div>
				
				<div class="kt-portlet__body pt-3 pb-3" style="padding-left:20px;padding-right:20px;">
					<div class="row">
						<div class="col-12 col-sm-4">
							Total Cost
							<span id="total_tariff" class="kt-font-bold">@total_tariff@</span>
							<br/>
							<span class="grey-comment">(Edit carrier pay and deposit under the "Vehicle Information" section)</span>
						</div>
						
						<div class="col-12 col-sm-4">
							Required Deposit
							<span id="total_deposit" class="kt-font-bold">@total_deposit@</span>
							<br/>
							<span class="grey-comment">(Edit deposit under the "Vehicle Information" section)</span>
						</div>
						
						<div class="col-12 col-sm-4">
							Carrier Pay
							<span id="carrier_pay" class="kt-font-bold">@carrier_pay@</span>
							<br/>
							<span class="grey-comment">(Edit carrier pay under the "Vehicle Information" section)</span>
						</div>
					</div>
				</div>
				
			</div>
		</div>
	</div>
	
	<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5 mt-3">
		<div class="kt-portlet__head hide_show" id="">
			<h3  class="shipper_detail">
			    Additional Information
			</h3>
		</div>

		<div class="kt-portlet__body pt-3" style="padding-left:20px;padding-right:20px;">
			<div class="new_form-group">
				@note_to_shipper@	
			</div>
		</div>

		<div class="kt-portlet__body pt-3" style="padding-left:20px;padding-right:20px;">
			<div class="new_form-group">
				<label>Quick Notes</label>
				<select name="quick_notes" id="quick_notes" class="form-box-textfield" onchange="addQuickNote();" style="width:200px;">
					<option value="">--Select--</value>
					<option value="Emailed: Prospect.">Emailed: Prospect.</value>
					<option value="Emailed: Bad Email.">Emailed: Bad Email.</value>
					<option value="Faxed: Prospect.">Faxed: Prospect.</value>
					<option value="Faxed: Bad Fax.">Faxed: Bad Fax.</value>
					<option value="Texted: Prospect.">Texted: Prospect.</value>
					<option value="Texted: Bad Mobile.">Texted: Bad Mobile.</value>
					<option value="Phoned: No Voicemail.">Phoned: No Voicemail.</value>
					<option value="Phoned: Left Message.">Phoned: Left Message.</value>
					<option value="Phoned: Spoke to Prospect.">Phoned: Spoke to Prospect.</value>
					<option value="Phoned: Bad Number.">Phoned: Bad Number.</value>
					<option value="Phoned: Not Intrested.">Phoned: Not Intrested.</value>
					<option value="Phoned: Do Not Call.">Phoned: Do Not Call.</value>
				</select>
			</div>
		</div>
	</div>
	
    <div style="float:right">
        <table cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td style="padding-left: 15px;"><?= submitButtons(SITE_IN . "application/quotes", "Save Quote") ?></td>
            </tr>
        </table>
    </div>
	
</form>

<!--unique-shipper-popup-starts-->
<div class="modal fade" id="uniqueShipper" tabindex="-1" role="dialog" aria-labelledby="uniqueShipper_model" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="uniqueShipper_model">Email must be unique</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i class="fa fa-times" aria-hidden="true"></i>
				</button>
			</div>
			<div class="modal-body">
				<div id="shipperInfo">
					<p><b style="color:red;">Email already registered, Please use different email</b></p>
				</div>
				<div id="shipperData" style="display:block;">
					<table class="table table-bordered" >
						<thead >
							<tr>
								<th>Select</th>
								<th>Name</th>
								<th>Company</th>
								<th>Email</th>
								<th>Phone</th>
								<th>Address</th>
								<th align="center">Quotes</th>
								<th align="center">Orders</th>
							</tr>
						</thead>
						<tbody id="shipper-info"></tbody>
					</table>
				</div>
				<div id="orderQuotesList" style="display: block; max-height:300px; overflow-y:scroll;"></div>
				<div id="popupLoader" style="display:none;">
					<center>
						<img src="https://cdn.dribbble.com/users/24711/screenshots/2713076/bumpy_loader_2x.gif" style="width:30%; height:30%;">
					</center>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" onclick="back()">Back</button>
				<button type="button" class="btn btn-primary" onclick="uniqueShipper()">OK</button>
			</div>
		</div>
	</div>
</div>
<!--unique-shipper-popup-ends-->

<script>
    $("#shipper_email").blur(function () {
        if ($("#shipper_email").val() !== "") {
            //checkUniqueShipperData('email', $("#shipper_email").val());
        }
    });

	function checkUniqueShipperData(key, value) {	   
		$("#shipperData").show();
		$("#orderQuotesList").html("");
		$("#orderQuotesList").hide();

		$.ajax({
			type: 'POST',
			url: BASE_PATH + 'application/ajax/shipper.php',
			dataType: 'json',
			data: {
				action: 'validateUniqueShipper',
				key: key,
				value: value
			},
			success: function (response) {
				if (response.exists > 0) {
					$("#shipper_email").val("");
					var html = '<tr>\n\
						<td><input type="radio" id="selectedShipper" style="font-size:0px" value="' + response.id + '"></td>\n\
						<td>' + response.first_name + " " + response.last_name + '</td>\n\
						<td>' + response.company_name + '</td>\n\
						<td><a style="color:#008ec2" href="mailto:' + response.email + '" title="' + response.email + '">' + response.email + '</a></td>\n\
						<td style="color:#008ec2">' + response.phone1 + '</td>\n\
						<td>' + response.address1 + '<br>' + response.city + '<br>' + response.state + '<br>' + response.country + '<br>' + response.zip_code + '</td>\n\
						<td><img onclick="getShipperQuotes()" src="<?php echo SITE_IN; ?>/images/icons/info.png" title="Info" alt="Info" width="16" height="16"></td>\n\
						<td><img onclick="getShipperOrders()" src="<?php echo SITE_IN; ?>/images/icons/info.png" title="Info" alt="Info" width="16" height="16"></td>\n\
					</tr>';
					$("#shipper-info").html(html);
					$("#uniqueShipper").modal();
				}
			}
		});
	}

	function  checkUniqueShipperData_ok() {

		if ((document.getElementById("selectedShipper").checked) == true) {
			var selectedShipper = $("#selectedShipper").val();
		
			$("#shipperid").val(selectedShipper);
			$("#shipper_fname").val(response.first_name);
			$("#shipper_lname").val(response.last_name);
			$("#shipper_company").val(response.company_name);
			$("#shipper_type").val(response.shipper_type);
			$("#shipper_hours").val(response.hours_of_operation);
			$("#shipper_email").val(response.email);
			$("#shipper_phone1").val(response.phone1);
			$("#shipper_phone1_ext").val(response.phone1_ext);
			$("#shipper_phone2").val(response.phone2);
			$("#shipper_phone2_ext").val(response.phone2_ext);
			$("#shipper_mobile").val(response.cell);
			$("#shipper_fax").val(response.fax1);
			$("#referred_by").val(response.referred_id);
			$("#shipper_address1").val(response.address1);
			$("#shipper_address2").val(response.address2);
			$("#shipper_city").val(response.city);
			$("#shipper_state").val(response.state);
			$("#shipper_zip").val(response.zip_code);
			$("#shipper_country").val(response.country);
			$(this).modal('hide');
		} else {
			$(this).modal('hide');
		}
	}

	function back() {
		$("#orderQuotesList").html("");
		$("#orderQuotesList").hide();
		$("#shipperData").show();
	}

	$(document).ready(function(){
       $.ajax({
            type: 'POST',
            url: BASE_PATH + 'application/ajax/accounts.php',
            dataType: 'json',
            data: {
                action: 'getOrderDeposite',
                owner_id: '<?php echo $_SESSION['member']['parent_id'];?>'
            },
            success: function (response) {
                $("#order_deposit").val(response.response.order_deposit);
                $("#order_deposit_type").val(response.response.order_deposit_type);
                $("#auto_quote_api_pin").val(response.response.auto_quote_api_pin);
                $("#auto_quote_api_key").val(response.response.auto_quote_api_key);
            }
        });
    });
</script>

<script type="text/javascript">
	$(document).ready(function () {
		$("#shipper_phone1,#shipper_phone2,#shipper_mobile,#shipper_fax").keypress(function (e) {
			if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
				$("#errmsg").html("Digits Only").show().fadeOut("slow");
				return false;
			}
		});

		$("#shipper_phone1,#shipper_phone2,#shipper_mobile,#shipper_fax").attr("placeholder", "xxx-xxx-xxxx");
		$("#shipper_phone1,#shipper_phone2,#shipper_mobile,#shipper_fax").attr('maxlength','10');
		$('#shipper_phone1,#shipper_phone2,#shipper_mobile,#shipper_fax').keypress(function() {

			function phoneFormat() {
				phone = phone.replace(/[^0-9]/g, '');
				phone = phone.replace(/(\d{3})(\d{3})(\d{4})/, "$1-$2-$3");
				return phone;
			}

			var phone = $(this).val();
			phone = phoneFormat(phone);

			$(this).val(phone);
		});
	});
</script>

<script type="text/javascript">
	$(document).ready(function() {
		$('#buysell,#buysell_days').select2();
	});
</script>

<script type="text/javascript">
	$('#shipping_est_date').datepicker({});
</script>

<input type="hidden" id="auto_quote_api_pin" value="">
<input type="hidden" id="auto_quote_api_key" value="">
<input type="hidden" id="order_deposit" value="">
<input type="hidden" id="order_deposit_type" value="">

<script src="<?php echo SITE_IN ?>core/js/core.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		
		$(document).click(()=>{
            $(".suggestions").html("");
        });

		$("#Shipper_Information").click(function() {
			$("#Shipper_Information_show").toggle();
		});

		// address search API key
        let timer;
        const waitTime = 1000;

		document.querySelector('#shipper_address1').addEventListener('keyup', (e) => {
            const text = e.currentTarget.value;
            clearTimeout(timer);
            timer = setTimeout(() => {
                autoComplete($("#shipper_address1").val().trim(), 'shipper');
            }, waitTime);
        });

		function autoComplete(address, type) {

			if(address.trim() != ""){
				$.ajax({
					type: 'POST',
					url: BASE_PATH + 'application/ajax/auto_complete.php',
					dataType: 'json',
					data: {
						action: 'suggestions',
						address: address
					},
					success: function (response) {
						let result = response.result;
						let html = ``;
						let h = null;
						let functionName = null;

						html += '<ul class="typeahead dropdown-menu" role="listbox" style="top: 36px; width:350px; left: 133px; display: block;">';
						html += '<li><a href="javascript:void(0)" style="height:25px !important; padding-top:0px !important; font-size:10px;">Suggestions</a></li>';

						h = document.getElementById("suggestions-box-shipper");
						h.innerHTML = "";

						result.forEach( (element, index) => {

							let address = `<strong>${element.street}</strong>,<br>${element.city}, ${element.state} ${element.zip}`;
							
							html += `<li>
										<a class="dropdown-item" href="javascript:void(0)" onclick="applyAddress('${element.street}','${element.city}','${element.state}','${element.zip}')" role="option">
											<p>${address}</p>
										</a>
									</li>`;
						});

						html += `<li>
									<a href="javascript:void(0)" style="height: 29px !important;font-size:10px;padding: 0px !important;padding-left: 10px !important; padding-top:10px !important;">Powered by
										&nbsp;&nbsp;&nbsp;<img alt="Cargo Flare" src="https://cargoflare.com/styles/cargo_flare/logo.png" style="width:auto;">
									</a>
								</li>`;
						html += `</ul>`;
						h.innerHTML = html;
					}
				});
			}
		}
	});

	function applyAddress (address, city, state, zip) {
		$("#suggestions-box-shipper").html("");
		$("#shipper_address1").val(address);
		$("#shipper_city").val(city);
		$("#shipper_state").val(state);
		$("#shipper_zip").val(zip);
		document.getElementById("suggestions-box-shipper").innerHTML = "";
	}

	function addQuickNote() {
		var textOld = $("#note_to_shipper").val();
		var str = textOld + " " + $("#quick_notes").val();
		$("#note_to_shipper").val(str);
	}

	$(document).ready(()=>{
		$("#origin_country").change(()=>{
			console.log("Workingsex");
		});

		$("#destination_country").change(()=>{
			if($("#destination_country").val() == "US" || $("#destination_country").val() == "CA") {
				$("label[for='destination_city']").html("<span class='text-danger'>*</span> City:");
				$("label[for='destination_state']").html("<span class='text-danger'>*</span>State/Zip:");
			} else {
				$("label[for='destination_city']").html("City:");
				$("label[for='destination_state']").html("State/Zip:");
			}
		});
	})
</script>