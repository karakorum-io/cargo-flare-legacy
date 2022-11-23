<style type="text/css">
	button.ui-button.ui-widget.ui-state-default.ui-corner-all.ui-button-text-only {
		background: #1228fa;
		color: white;
	}
  	
	.shipper_detail {
		text-align:left;
		font-size:15px;
		color:#222;
		height:40px;
		line-height:40px;
		padding-left:15px;
	}

	#origin_zip{
		width:60px;
	}

	#origin_state{
		width: 188px;
	}

	#destination_state{
		width: 188px;
	}
	input#destination_zip{
		margin-left: 10px;
		width:60px;
	}
</style>

<?php if ($_GET['leads'] == 'create') { ?>
<script type="text/javascript">
	var v = <?=(isset($_POST['year']))?count($_POST['year']):0?>;

    function createAndEmail() {
        $("#co_send_email").val("1");
        $("#submit_button").click();
    }
	
	var tempDisableBeforeUnload = false;
	$(window).bind('beforeunload', function(){
		if($("#shipper_fname").val()!='' &&
			$("#shipper_lname").val()!='' &&
			$("#shipper_company").val()!='' &&
			$("#shipper_email").val()!='' && !tempDisableBeforeUnload) {
				return 'Leaving this page will lose your changes. Are you sure?';
		} else {	
			tempDisableBeforeUnload = false;
			return;
		}
	});

	$(document).ready(function(){
		var createForm = $('#create_form');
		createForm.find("input.shipper_company-model").typeahead({
			source: function(request, result) {
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
			change: function() {
				var shipper_company = createForm.find("input.shipper_company-model").val();
				if(shipper_company !="") {
					$.ajax({
						url: '<?= SITE_IN ?>application/ajax/autocomplete.php',
						type: 'GET',
						dataType: 'json',
						data: {
							term: shipper_company,
							action: 'getCompanyValues'
						},
						success: function(data) {
						 	if(Object.keys(data).length > 0) {
								if(data.first_name!='N/A' && data.first_name!='' && data.first_name!=null)
									$("#shipper_fname").val(data.first_name);
								if(data.last_name!='N/A' && data.last_name!='' && data.last_name!=null)  
									$("#shipper_lname").val(data.last_name);
								
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

								if(data.referred_id !=0 && data.referred_by !='' && data.referred_id !=null){
									$("#referred_by").empty(); // remove old options
									$("#referred_by").append($("<option></option>")
										.attr("value", data.referred_id).text(data.referred_by));
								}  else {
									$("#referred_by").empty(); // remove old options
									$("#referred_by").append($("<option></option>").attr("value", '').text('Select One'));
									<?php 
										foreach ($this->referrers_arr as $key=>$referrer) {
									?>
									$("#referred_by").append($("<option></option>").attr("value", '<?php print $key;?>').text('<?php print $referrer;?>'));
									<?php
										}
									?>
								}
							} else {
								$("#referred_by").empty(); // remove old options
								$("#referred_by").append($("<option></option>").attr("value", '').text('Select One'));
									<?php 
										foreach ($this->referrers_arr as $key=>$referrer) {
									?>
									$("#referred_by").append($("<option></option>").attr("value", '<?php print $key;?>').text('<?php print $referrer;?>'));
									<?php
										}
									?>  
							}
						} //if data found
					})
				}
			}
		});
	});
		
	function addQuickNote() {
		var textOld = $("#note_to_shipper").val();
		var str = textOld + " " + $("#quick_notes").val();
		$("#note_to_shipper").val(str);
	}
</script>
<script type="text/javascript" src="<?=SITE_IN?>jscripts/application/quotes/create.js"></script>
<?php } else { ?>
<script type="text/javascript">
	var entityBlocked = <?=($this->entity->isBlocked())?'true':'false'?>;
	var entity_id = <?=$this->entity->id?>;
	var entity_number = '<?=$this->entity->getNumber()?>';
</script>
<script type="text/javascript" src="<?=SITE_IN?>jscripts/application/quotes/edit.js"></script>
<?php } ?>
<script type="text/javascript" src="<?=SITE_IN?>jscripts/application/quotes/create_edit.js"></script>

<?php if ($_GET['leads'] == 'create') { ?>
<?php include(ROOT_PATH.'application/templates/vehicles/create_js.php');?>
<?php } else { ?>
<div style="padding-top:15px;">
	<?php include('lead_menu.php');  ?>
</div>
<?php include(ROOT_PATH.'application/templates/vehicles/edit_js.php'); ?>
<?php } ?>
<?php include(ROOT_PATH.'application/templates/vehicles/form.php');?>
<br/>
<!---<h3>@title@</h3>---->
<label class="btn btn-bold btn-label-warning text-left" style="margin-bottom:15px;">
        Complete the form below and click&nbsp;&nbsp;
        <span style="display:inline-block;color:green;"><strong>Create Lead</strong></span>
        &nbsp;&nbsp;when finished. Required fields are marked with a 
        <span style="display:inline-block;color:red;">&nbsp;&nbsp;*</span>
    </label>
<!-- <h2><div class="btn btn_bright_blue btn-sm" onclick="selectShipper()" >Select Shipper</div></h2>--->
<div style="clear:both;"></div>
<!---Complete the form below and click Save when finished. Required fields are marked with a <span style="color:red;">*</span>--->
<input type="hidden" id="lead_id" value="<?=(isset($_GET['id'])?$_GET['id']:'')?>"/>
<form action="<?= ($_GET['leads'] == 'create')?getLink('leads/create'):getLink('leads/edit/id/'.$_GET['id']) ?><?php echo isset($_GET['convert'])?'?convert':'' ?>" method="post" onsubmit="javascript:tempDisableBeforeUnload = true;" id="create_form">
<?php if (isset($_GET['convert'])) { ?>
	<input type="hidden" name="convert" value="1"/>
<?php } ?>
	<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5 mt-3" >
		<div id="headingOne" class="hide_show">
			<h3 class="shipper_detail">Shipper Information</h3>
		</div>
		<div class="kt-portlet__body pt-3 pb-4" style="padding-left:20px;padding-right:20px;">
			<div class="row">
				<div class="col-12 col-sm-4">
					<div class="new_form-group">
						@shipper_fname@
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
						@shipper_lname@
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
						@shipper_company@
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
						@shipper_type@
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
	</div>

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
									<?php if ($_GET['leads'] == 'create') { ?><span class="kt-link" onclick="setLocationSameAsShipper('origin')">same as shipper</span><?php } ?>
								</div>
							</div>
						</div>
						<div class="col-12 col-sm-6">
							<div class="row form-group">
								<div class="col-12">
									<label>To:</label>
									<?php if ($_GET['leads'] == 'create') { ?><span class="kt-link" onclick="setLocationSameAsShipper('destination')">same as shipper</span><?php } ?>
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
	
	<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5 mt-3">
		<div class="kt-portlet__head hide_show">
			<div class="kt-portlet__head-label">
				<h3 class="shipper_detail">
					Additional Information
				</h3>
			</div>
		</div>
		<div class="kt-portlet__body pt-3" style="padding-left:20px;padding-right:20px;">
			
			<div class="row">
				<div class="col-12 col-sm-6">
					<div class="new_form-group">
						@units_per_month@
					</div>
				</div>
				<div class="col-12 col-sm-6">
					<div class="new_form-group">
						@buysell[]@
					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-12 col-sm-6">
					<div class="new_form-group  ">
						@shipment_type@
					</div>
				</div>
				<div class="col-12 col-sm-6">
					<div class="new_form-group">
						@buysell_days[]@
					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-12 col-sm-6">
					<div class="new_form-group">
						@website@
					</div>
				</div>
				<div class="col-12 col-sm-6">
					<div class="new_form-group">
							@avail_pickup_date@
						</div>
				</div>
			</div>
			
		</div>
	</div>
	
	<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5 mt-3">
		<div class="kt-portlet__head hide_show">
			<div class="kt-portlet__head-label">
				<h3 class="shipper_detail">
					Required Information
				</h3>
			</div>
		</div>
		<div class="kt-portlet__body pt-3" style="padding-left:20px;padding-right:20px;">
			
			<div class="row">
				<div class="col-12">
					<div class="new_form-group">
							<label for="calling_for"><span class="text-danger">*</span>Reason for call:</label>
							<textarea class="form-box-textfield form-control" name="calling_for" type="text" id="calling_for"></textarea>
						</div>
				</div>
			</div>
				
			<div class="row">
				<div class="col-12">
					<div class="new_form-group">
						<label><span class="text-danger">*</span>Notes</label>
						@note_to_shipper@
					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-12 col-sm-6">
					<div class="new_form-group">
						<label>Quick Notes</label>
						<select name="quick_notes" id="quick_notes" class="form-box-textfield" onchange="addQuickNote();">
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
		</div>
	</div>

	<div style="float:right">
		<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td>
					<input type="hidden" name="send_email" value="0" id="co_send_email"/>
				</td>
				<td style="padding-left: 15px;"><?= submitButtons(SITE_IN."application/quotes", "Create Lead") ?></td>
			</tr>
		</table>
	</div>
</form>

<script type="text/javascript">
	$(document).ready(function () {
		
		handleMandatory(false);

		$("label[for='calling_for'] span").html("*");

		if($("#shipper_type").val() == "Residential"){
			handleMandatory(false);
		}
		
		if($("#shipper_type").val() == "Commercial"){
			handleMandatory(true);
		}


		if($("#shipper_type").val() == ""){
			handleMandatory(false);
		}

		//called when key is pressed in textbox
		$("#shipper_phone1,#shipper_phone2,#shipper_mobile,#shipper_fax").keypress(function (e) {
			//if the letter is not digit then display error and don't type anything
			if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
				//display error message
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

    	$('#buysell,#buysell_days').select2();

		// configuring date picker fields
		$('#next_shipping_date').datepicker({});
		$("#avail_pickup_date").datepicker({});
	
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

						if(type == 'pickup'){
							h = document.getElementById("suggestions-box");
							h.innerHTML = "";
							functionName = 'applyAddressOrigin';
							html += '<ul class="typeahead dropdown-menu" role="listbox" style="top: 36px; width:350px; left: 133px; display: block;">';
							html += '<li><a href="javascript:void(0)" style="height:25px !important; padding-top:0px !important; font-size:10px;">Suggestions</a></li>';
						}

						if(type == 'shipper'){
							h = document.getElementById("suggestions-box-shipper");
							h.innerHTML = "";
							functionName = 'applyAddressShipper';
							html += '<ul class="typeahead dropdown-menu" role="listbox" style="top: 36px; width:350px; left: 133px; display: block;">';
							html += '<li><a href="javascript:void(0)" style="height:25px !important; padding-top:0px!important; font-size:10px;">Suggestions</a></li>';
						}

						if(type == 'destination'){
							h = document.getElementById("suggestions-box-destination");
							h.innerHTML = "";
							functionName = 'applyAddressDestination';
							html += '<ul class="typeahead dropdown-menu" role="listbox" style="top: 36px; width:350px; left: 133px; display: block;">';
							html += '<li><a href="javascript:void(0)" style="height:25px !important; padding-top:0px !important; font-size:10px;">Suggestions</a></li>';
						}


						if(type == 'cc'){
							h = document.getElementById("suggestions-box-cc");
							h.innerHTML = "";
							functionName = 'applyAddressCC';
							html += '<ul class="typeahead dropdown-menu" role="listbox" style="top: 36px; width:350px; left: 133px; display: block;">';
							html += '<li><a href="javascript:void(0)" style="height:25px !important; padding-top:0px !important; font-size:10px;">Suggestions</a></li>';
						}

						result.forEach( (element, index) => {

							let address = `<strong>${element.street}</strong>,<br>${element.city}, ${element.state} ${element.zip}`;
							
							html += `<li>
										<a class="dropdown-item" href="javascript:void(0)" onclick="${functionName}('${element.street}','${element.city}','${element.state}','${element.zip}')" role="option">
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

		// manage route information section UI
		$("#shipper_type").change(()=>{
			
			if($("#shipper_type").val() == "Commercial"){
				handleMandatory(true);
			}

			if($("#shipper_type").val() == "Residential"){
				handleMandatory(false);
			}
		});
	});

	function applyAddressOrigin(address, city, state, zip){
		$(".suggestions").html("");
		$("#origin_address1").val(address);
		$("#origin_city").val(city);
		$("#origin_state").val(state);
		$("#origin_zip").val(zip);
		document.getElementById("suggestions-box").innerHTML = "";
	}

	function applyAddressShipper (address, city, state, zip) {
		$(".suggestions").html("");
		$("#shipper_address1").val(address);
		$("#shipper_city").val(city);
		$("#shipper_state").val(state);
		$("#shipper_zip").val(zip);
		document.getElementById("suggestions-box-shipper").innerHTML = "";
	}

	function applyAddressDestination (address, city, state, zip) {
		$(".suggestions").html("");
		$("#destination_address1").val(address);
		$("#destination_city").val(city);
		$("#destination_state").val(state);
		$("#destination_zip").val(zip);
		document.getElementById("suggestions-box-destination").innerHTML = "";
	}

	function applyAddressCC (address, city, state, zip) {
		$(".suggestions").html("");
		$("#e_cc_address").val(address);
		$("#e_cc_city").val(city);
		$("#e_cc_state").val(state);
		$("#e_cc_zip").val(zip);
		document.getElementById("suggestions-box-cc").innerHTML = "";
	}

	function handleMandatory(flag){
		if(flag){
			$("label[for='shipper_company']").html("<span class='text-danger'>*</span>Country:");
		} else {
			$("label[for='shipper_company']").html("Country:");
		}

	}
</script>


