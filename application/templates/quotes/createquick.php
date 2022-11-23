<?php if ($_GET['quotes'] == 'createquick') { ?>
<script type="text/javascript">
	var v = <?=(isset($_POST['year']))?count($_POST['year']):0?>;
    function createAndEmail() {
        $("#co_send_email").val("1");
        $("#submit_button").click();
    }
var tempDisableBeforeUnload = false;
$(window).bind('beforeunload', function(){
									
   if(($("#shipper_fname").val()!='' ||
	$("#shipper_lname").val()!='' ||
	$("#shipper_company").val()!='' ||
	$("#shipper_email").val()!='') && !tempDisableBeforeUnload)
	{
		  return 'Leaving this page will lose your changes. Are you sure?';
	}
	else{	
	   tempDisableBeforeUnload = false;
	   return;
	   
	}
});



	$(document).ready(function(){
		
		
		var createForm = $('#create_form');
		createForm.find("input.shipper_company-model").autocomplete({
			source: function(request, response) {
				$.ajax({
					url: '<?= SITE_IN ?>application/ajax/autocomplete.php',
					type: 'GET',
					dataType: 'json',
					data: {
						term: request.term,
						action: 'getCompany'
					},
					success: function(data) {
						response(data);
					}
				})
			},
			minLength: 0,
			autoFocus: true,
			change: function() {
				//createForm.find("input.shipper_company-model").val('');
				var shipper_company = createForm.find("input.shipper_company-model").val();
				//alert(shipper_company);
				if(shipper_company !="")
				{
					$.ajax({
						url: '<?= SITE_IN ?>application/ajax/autocomplete.php',
						type: 'GET',
						dataType: 'json',
						data: {
							term: shipper_company,
							action: 'getCompanyValues'
						},
						success: function(data) {
							//response(data);
						 if(Object.keys(data).length > 0)
						 {
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
							//if (data.country == "US") {
								$("#shipper_state").val(data.state);
							//} else {
								//$("#shipper_state2").val(data.state);
							//}
							$("#shipper_zip").val(data.zip_code);
							$("#shipper_type").val(data.shipper_type);
							if(data.referred_id !=0 && data.referred_by !='' && data.referred_id !=null){
							  // $("#referred_by").val(data.referred_id);
							   //$('#referred_by').prop('disabled', true);
							   
							    $("#referred_by").empty(); // remove old options
								$("#referred_by").append($("<option></option>")
									 .attr("value", data.referred_id).text(data.referred_by));
							  }
							  else
							  {
								  $("#referred_by").empty(); // remove old options
								  $("#referred_by").append($("<option></option>")
									               .attr("value", '').text('Select One'));
								  <?php 
								   foreach ($this->referrers_arr as $key=>$referrer) {
                                   ?>
												  $("#referred_by").append($("<option></option>")
									               .attr("value", '<?php print $key;?>').text('<?php print $referrer;?>'));
								<?php
                                     }
								  ?>
								  
							  }
							
						}
						else
						  {
							  $("#referred_by").empty(); // remove old options
									  $("#referred_by").append($("<option></option>")
													   .attr("value", '').text('Select One'));
									  <?php 
									   foreach ($this->referrers_arr as $key=>$referrer) {
									   ?>
													  $("#referred_by").append($("<option></option>")
													   .attr("value", '<?php print $key;?>').text('<?php print $referrer;?>'));
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
<?php if ($_GET['quotes'] == 'createquick') { ?>
<?php include(ROOT_PATH.'application/templates/vehicles/create_quick_js.php');?>
<?php } else { ?>
<div style="padding-top:15px;">
	<?php include('quote_menu.php');  ?>
</div>
<?php include(ROOT_PATH.'application/templates/vehicles/edit_js.php'); ?>
<?php } ?>
<?php //include(ROOT_PATH.'application/templates/vehicles/form.php');?>

<script type="text/javascript">
	$(document).ready(function() {
		
		var vehicleForm = $('#vehicle_form');
		<?php for($i=0;$i<=10;$i++){?>
		//var vehicleType = $("#add_vehicle_type");
		$("#add_vehicle_type-"+<?=$i?>).focus(function(){
			$(this).click();
			$(this).autocomplete('search');
		});
		$("#add_vehicle_type-"+<?=$i?>).autocomplete({
			minLength: 0,
			source: vehicle_type_data,
			autoFocus: true
		});
		
		vehicleForm.find("input.vehicle-make-"+<?=$i?>).autocomplete({
			source: function(request, response) {
				$.ajax({
					url: '<?= SITE_IN ?>application/ajax/autocomplete.php',
					type: 'GET',
					dataType: 'json',
					data: {
						term: request.term,
						action: 'getVehicleMake'
					},
					success: function(data) {
						response(data);
					}
				})
			},
			minLength: 0,
			autoFocus: true,
			change: function() {
				vehicleForm.find("input.vehicle-model-"+<?=$i?>).val('');
			}
		});

		vehicleForm.find("input.vehicle-model-"+<?=$i?>).autocomplete({
			source: function(request, response) {
				$.ajax({
					url: '<?= SITE_IN ?>application/ajax/autocomplete.php',
					type: 'GET',
					dataType: 'json',
					data: {
						term: request.term,
						action: 'getVehicleModel',
						make: $("input.vehicle-make-"+<?=$i?>).val()
					},
					success: function(data) {
						response(data);
					}
				})
			},
			minLength: 0,
			autoFocus: true
		});
		vehicleForm.find('input.vehicle-make-'+<?=$i?>+', input.vehicle-model-'+<?=$i?>).focus(function() {
			var el = $(this);
			setTimeout(function() {
				if (el.val() == '') {
					el.autocomplete('search');
				}
			}, 300);
		});
		<?php }?>
	});
	
	var vehCount=0;
	function addVehicleQuickNew(){
		//var vehicleForm = $('#vehicle_form');
		$('#tr-vehicle-'+vehCount).css('display', 'block');
		if(vehCount == 0)
		$('#headveh').css('display', 'block');
		vehCount++;
	}
	
	function deleteVehicleNew(id) {

		
		var retVal = confirm("Do you want to continue ?");
	   if( retVal == true ){
			$('#tr-vehicle-'+id).css('display', 'none');
			vehCount--;
			$("#carrier_pay-"+id).val('');
			$("#deposit-"+id).val('');
			updatePricing(id);
			
			$("#year-"+id).val('');
			$("#make-"+id).val('');
			$("#model-"+id).val('');
			$("#add_vehicle_type-"+id).val('');
		
		  return true;
	   }else{
		 // alert("User does not want to continue!");
		  return false;
	   }
		
		
	}
	
	function updatePricing(count) {
		
		var vehiclesGrid = $("#vehicles-grid");
		var carrier_pay = 0;
		var total_deposit = 0;
		
		var total_tariff = 0;
		var total_tariff_td = 0;
		vehiclesGrid.find("input[name='carrier_pay[]']").each(function() {
			
			carrier_pay += (isNaN(parseFloat($(this).val()))?0:parseFloat($(this).val()));
			
		});
		vehiclesGrid.find("input[name='deposit[]']").each(function() {
			total_deposit += (isNaN(parseFloat($(this).val()))?0:parseFloat($(this).val()));
		});
		 
		total_tariff_td = (isNaN(parseFloat($("#carrier_pay-"+count).val()))?0:parseFloat($("#carrier_pay-"+count).val()))  + (isNaN(parseFloat($("#deposit-"+count).val()))?0:parseFloat($("#deposit-"+count).val()));
		total_tariff = carrier_pay + total_deposit;
		
		$("#total_tariff1").text('$ '+total_tariff.toFixed(2));
		$("#total_deposit1").text('$ '+total_deposit.toFixed(2));
		$("#carrier_pay1").text('$ '+carrier_pay.toFixed(2));
		
		$("#total_"+count).text('$ '+total_tariff_td.toFixed(2));
		
	}
	
function getDistance() {
         
    var ocity = $.trim($("#origin_city").val());
	var ostate = $.trim($("#origin_state").val());
	
	var dcity = $.trim($("#destination_city").val());
	var dstate = $.trim($("#destination_state").val());
	
    if (ocity == '' || ostate == '' || dcity == '' || dstate == ''  ) return;
   // $(".acc_search_dialog").nimbleLoader('show');
    $.ajax({
        type: 'POST',
        url: BASE_PATH+'application/ajax/getdistance.php',
        dataType: 'json',
        data: {
            action: 'getdistance',
            ocity: ocity,
            ostate: ostate,
			dcity: dcity,
            dstate: dstate
        },
        success: function(response){
            if (response.success) {
                acc_data = response.data;

				if (response.data.length != 0) {
					  $("#distanceData").text(response.data[0].distance);
					  
					  var map_url = "http://maps.google.com/maps?";
					  var saddr = ocity+","+ostate+",US";
					  var daddr = dcity+","+dstate+",US";
					  map_url +='saddr="'+saddr+'"&daddr="'+daddr+'"';
					  $("#distanceViewMap").text('Map It');
					  $("#distanceViewMapHref").attr("href", map_url);
					} else {
					
				}
            } else {
                alert("Can't load account data. Try again later, please");
            }
        },
        error: function(response){
            alert("Can't load account data. Try again later, please");
        },
        complete: function(response){
            //$(".acc_search_dialog").nimbleLoader('hide');
        }
    });
}

</script>



<br/>
<h3>@title@</h3>
<div style="clear:both;"></div>
Complete the form below and click Save when finished. Required fields are marked with a <span style="color:red;">*</span><br/><br/>
<input type="hidden" id="quote_id" value="<?=(isset($_GET['id'])?$_GET['id']:'')?>"/>
<form action="<?= ($_GET['quotes'] == 'createquick')?getLink('quotes/createquick'):getLink('quotes/edit/id/'.$_GET['id']) ?><?php echo isset($_GET['convert'])?'?convert':'' ?>" method="post" onsubmit="javascript:tempDisableBeforeUnload = true;"  id="create_form">
<?php if (isset($_GET['convert'])) { ?>
	<input type="hidden" name="convert" value="1"/>
<?php } ?>
<div class="quote-info" style="float:none;">
	<div>
		<table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table quote-edit" style="white-space:nowrap;">
			<tr>
				<td colspan="5">
					<p class="block-title">Shipper Information</p>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td><td><?=($_GET['quotes'] == 'createquick')?functionButton('Select Shipper', 'selectShipper()'):'&nbsp;'?></td>
				<td></td>
				<td><input type="hidden" name="shipper_address1"  id="shipper_address1" value=""/></td>
			</tr>
			<tr>
				<td>@shipper_fname@</td>
				<td>@shipper_email@</td>
				<td><input type="hidden" name="shipper_address2"  id="shipper_address2" value=""/></td>
			</tr>
			<tr>
				<td>@shipper_lname@</td>
				<td>@shipper_phone1@ </td>
				<td>
                <input type="hidden" name="shipper_phone2"  id="shipper_phone2" value=""/>
                <input type="hidden" name="shipper_city"  id="shipper_city" value=""/>
                </td>
			</tr>
			<tr>
				<td>@shipper_company@</td>
				<td>@referred_by@</td>
				<td>
                <input type="hidden" name="shipper_mobile"  id="shipper_mobile" value=""/>
                <input type="hidden" name="shipper_state"  id="shipper_state" value=""/>
                <input type="hidden" name="shipper_zip"  id="shipper_zip" value=""/>
               </td><div id="notes_container"></div>
			</tr>
			</table>
			</div>
</div>
			</br>
			
			<div class="quote-info" style="float:none;">
	<div>
		<table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table quote-edit" style="white-space:nowrap;">
			<tr>
				<td colspan="5">
					<p class="block-title">Route Information</p>
				</td>
			</tr>
			<tr>
				<td><strong>From:</strong></td>
				<td></td>
				<td><strong>To:</strong></td>
				<td></td>
			</tr>
			<!--tr>
				<td>@origin_city@</td> 
				<td>@destination_city@</td>
			</tr-->
            <tr>
				<td><label for="origin_city"><span class="required">*</span>City:</label></td><td><input class="geo-city form-box-textfield" tabindex="18" name="origin_city" type="text" maxlength="32" id="origin_city" onblur="getDistance();"></td> 
				<td><label for="destination_city"><span class="required">*</span>City:</label></td><td><input class="geo-city form-box-textfield" tabindex="22" name="destination_city" type="text" maxlength="32" id="destination_city"  onblur="getDistance();"></td>
			</tr>
            <tr>
				<td><label for="origin_state"><span class="required">*</span>State/Zip:</label></td><td><select style="width:140px;" tabindex="19" name="origin_state" class="form-box-combobox" id="origin_state"  onblur="getDistance();"><option value="" selected="selected">Select One</option><optgroup label="United States"><option value="AL">Alabama</option><option value="AK">Alaska</option><option value="AZ">Arizona</option><option value="AR">Arkansas</option><option value="CA">California</option><option value="CO">Colorado</option><option value="CT">Connecticut</option><option value="DE">Delaware</option><option value="DC">District of Columbia</option><option value="FL">Florida</option><option value="GA">Georgia</option><option value="HI">Hawaii</option><option value="ID">Idaho</option><option value="IL">Illinois</option><option value="IN">Indiana</option><option value="IA">Iowa</option><option value="KS">Kansas</option><option value="KY">Kentucky</option><option value="LA">Louisiana</option><option value="ME">Maine</option><option value="MD">Maryland</option><option value="MA">Massachusetts</option><option value="MI">Michigan</option><option value="MN">Minnesota</option><option value="MS">Mississippi</option><option value="MO">Missouri</option><option value="MT">Montana</option><option value="NE">Nebraska</option><option value="NV">Nevada</option><option value="NH">New Hampshire</option><option value="NJ">New Jersey</option><option value="NM">New Mexico</option><option value="NY">New York</option><option value="NC">North Carolina</option><option value="ND">North Dakota</option><option value="OH">Ohio</option><option value="OK">Oklahoma</option><option value="OR">Oregon</option><option value="PA">Pennsylvania</option><option value="RI">Rhode Island</option><option value="SC">South Carolina</option><option value="SD">South Dakota</option><option value="TN">Tennessee</option><option value="TX">Texas</option><option value="UT">Utah</option><option value="VT">Vermont</option><option value="VA">Virginia</option><option value="WA">Washington</option><option value="WV">West Virginia</option><option value="WI">Wisconsin</option><option value="WY">Wyoming</option></optgroup><optgroup label="Canada"><option value="AB">Alberta</option><option value="BC">British Columbia</option><option value="MB">Manitoba</option><option value="NB">New Brunswick</option><option value="NL">Newfoundland</option><option value="NT">Northwest Territories</option><option value="NS">Nova Scotia</option><option value="NU">Nunavut</option><option value="ON">Ontario</option><option value="PE">Prince Edward Island</option><option value="QC">Quebec</option><option value="SK">Saskatchewan</option><option value="YT">Yukon</option></optgroup></select><input style="width:70px;margin-left:5px;" class="zip form-box-textfield" tabindex="20" name="origin_zip" type="text" maxlength="64" id="origin_zip" ></td>
				<td><label for="destination_state"><span class="required">*</span>State/Zip:</label></td><td><select style="width:140px;" tabindex="23" name="destination_state" class="form-box-combobox" id="destination_state"  onblur="getDistance();"><option value="" selected="selected">Select One</option><optgroup label="United States"><option value="AL">Alabama</option><option value="AK">Alaska</option><option value="AZ">Arizona</option><option value="AR">Arkansas</option><option value="CA">California</option><option value="CO">Colorado</option><option value="CT">Connecticut</option><option value="DE">Delaware</option><option value="DC">District of Columbia</option><option value="FL">Florida</option><option value="GA">Georgia</option><option value="HI">Hawaii</option><option value="ID">Idaho</option><option value="IL">Illinois</option><option value="IN">Indiana</option><option value="IA">Iowa</option><option value="KS">Kansas</option><option value="KY">Kentucky</option><option value="LA">Louisiana</option><option value="ME">Maine</option><option value="MD">Maryland</option><option value="MA">Massachusetts</option><option value="MI">Michigan</option><option value="MN">Minnesota</option><option value="MS">Mississippi</option><option value="MO">Missouri</option><option value="MT">Montana</option><option value="NE">Nebraska</option><option value="NV">Nevada</option><option value="NH">New Hampshire</option><option value="NJ">New Jersey</option><option value="NM">New Mexico</option><option value="NY">New York</option><option value="NC">North Carolina</option><option value="ND">North Dakota</option><option value="OH">Ohio</option><option value="OK">Oklahoma</option><option value="OR">Oregon</option><option value="PA">Pennsylvania</option><option value="RI">Rhode Island</option><option value="SC">South Carolina</option><option value="SD">South Dakota</option><option value="TN">Tennessee</option><option value="TX">Texas</option><option value="UT">Utah</option><option value="VT">Vermont</option><option value="VA">Virginia</option><option value="WA">Washington</option><option value="WV">West Virginia</option><option value="WI">Wisconsin</option><option value="WY">Wyoming</option></optgroup><optgroup label="Canada"><option value="AB">Alberta</option><option value="BC">British Columbia</option><option value="MB">Manitoba</option><option value="NB">New Brunswick</option><option value="NL">Newfoundland</option><option value="NT">Northwest Territories</option><option value="NS">Nova Scotia</option><option value="NU">Nunavut</option><option value="ON">Ontario</option><option value="PE">Prince Edward Island</option><option value="QC">Quebec</option><option value="SK">Saskatchewan</option><option value="YT">Yukon</option></optgroup></select><input style="width:70px;margin-left:5px;" class="zip form-box-textfield" tabindex="24" name="destination_zip" type="text" maxlength="64" id="destination_zip" ></td>
			</tr>
			<!--tr>
				<td>@origin_state@@origin_zip@</td>
				<td>@destination_state@@destination_zip@</td>
			</tr-->
			<tr>
				<td>@origin_country@</td>
				<td>@destination_country@</td>
			</tr>
			<tr>
				<td>@shipping_est_date@</td>
				<td>@shipping_ship_via@</td>
			</tr>

			<tr>
				<td></td>
                
			</tr>
			<tr>
				<td><input type="hidden" name="shipper_type"  id="shipper_type" value=""/></td>
				<td><input type="hidden" name="shipper_fax"  id="shipper_fax" value=""/></td>
				<td><input type="hidden" name="shipper_country"  id="shipper_country" value="US"/></td>
			</tr>
			<tr>
				<td><input type="hidden" name="shipping_notes"  id="shipping_notes" value=""/></td>
				<td><input type="hidden" name="shipper_hours"  id="shipper_hours" value=""/></td> 
				<td></td><td></td>
			</tr>
            <tr>
				<td><input type="hidden" name="shipping_notes"  id="shipping_notes" value=""/></td>
				<td><input type="hidden" name="shipper_hours"  id="shipper_hours" value=""/></td> 
                <td><b>Mileage:</b></td>
				<td > <div style="float:left;"><div id="distanceData" style="float:left;font-weight:bold">0</div> <div  style="float:left;">&nbsp;&nbsp;&nbsp;<b>Miles</b>&nbsp;&nbsp; </div><div  style="float:left;"><a href="" id="distanceViewMapHref" target="_blank"><div id="distanceViewMap"></div></a></div></div></td>
			</tr>
			<tr>
			<?php if ($_GET['quotes'] == 'createquick') { ?>
							<td align="right"><input type="hidden" name="shipper_add"  id="shipper_add" value="0"/></td>
							<?php } else { ?>
							<td colspan="2">&#8203;</td>
							<?php } ?>
			<td><input type="hidden" name="note_to_shipper"  id="note_to_shipper" value=""/></td><td>&nbsp;</td>
			<td>&nbsp;</td><td>&nbsp;</td>
			</tr>
		</table>
	</div>
</div>
<br/>




<div class="quote-info" style="float:none;">
	<p class="block-title">Vehicle Information</p>
	<div id="vehicle_form" >
		<?php if ($_GET['quotes'] == 'createquick') { $i=1;?>
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="grid" style="white-space:nowrap;" id="vehicles-grid">
			
			<?php //if (isset($_POST['year'])) : ?>
			<?php //foreach($_POST['year'] as $i => $year) : 
			 for($i=0;$i<=20;$i++){
				 $class = "";
				 if($i%2)
				  $class = "even";
			?>
            <?php if($i==0) {?>
            <thead>
				<tr class="grid-head" style="display:none;" id="headveh">
					<td class="grid-head-left" width="100">Year</td>
					<td width="150">Make</td>
					<td width="146">Model</td>
					<td width="148">Type</td>
					<!--td width="100">Vin #</td>
					<td width="100">Lot #</td-->
					 <td width="146">Carrier</td>
					<td width="146">Deposit</td>
                    <td width="96">Total</td>
					<td class="grid-head-right"  width="85">Actions</td>
				</tr>
			</thead>
			<tbody>
            <?php }?>  
			<tr class="grid-body<?php print " ".$class;?>" id="tr-vehicle-<?= $i ?>" rel="<?= $i ?>" <?php //if($i!=0){?>style="display:none;"<?php //}?>>
				<td class="grid-body-left" align="center" width="100"><input type="text" style="width:90px;" class="form-box-textfield digit-only" id="year-<?=$i?>" maxlength="4"  name="year[]" value="<?= $year ?>"/><?= $year ?></td>
				<td align="center" width="150"><input type="text" name="make[]" style="width:120px;" class="form-box-textfield vehicle-make-<?=$i?>" id="make-<?=$i?>" maxlength="32"  value="<?= $_POST['make'][$i] ?>"/><?= $_POST['make'][$i] ?></td>
				<td align="center" width="150"><input type="text" style="width:90px;"  class="form-box-textfield vehicle-model-<?=$i?>" id="model-<?=$i?>" maxlength="32"   name="model[]" value="<?= $_POST['model'][$i] ?>"/><?= $_POST['model'][$i] ?></td>
				<td align="center" width="150"><input type="text" name="type[]" style="width:120px;"  class="form-box-textfield vehicle-type-<?=$i?>" id="add_vehicle_type-<?=$i?>" maxlength="32" value="<?= $_POST['type'][$i] ?>"/><?= $_POST['type'][$i] ?>
					<input type="hidden" name="state[]" value="<?= $_POST['state'][$i] ?>"/>
					<input type="hidden" name="plate[]" value="<?= $_POST['plate'][$i] ?>"/>
					<input type="hidden" name="color[]" value="<?= $_POST['color'][$i] ?>"/>
                    <!--input type="hidden" name="carrier_pay[]" value="<?= $_POST['carrier_pay'][$i] ?>"/-->
                <input type="hidden" name="tariff[]" value="<?= $_POST['tariff'][$i] ?>"/>
                <!--input type="hidden" name="deposit[]" value="<?= $_POST['deposit'][$i] ?>"/-->
                <input type="hidden" name="inop[]" value="<?= $_POST['inop'][$i] ?>"/><?php //print ($_POST['inop'][$i])?'Yes':'No'; ?>
				</td>
				<!--td align="center" width="100"><input type="text" name="vin[]" style="width:90px;" class="form-box-textfield alphanum" maxlength="20" id="add_vehicle_vin-<?=$i?>" value="<?= $_POST['vin'][$i] ?>"/><?= $_POST['vin'][$i] ?></td>
				<td align="center" width="100"><input type="text" name="lot[]" style="width:90px;" id="add_vehicle_lot-<?=$i?>" class="form-box-textfield alphanum" maxlength="20"  value="<?= $_POST['lot'][$i] ?>"/><?= $_POST['lot'][$i] ?></td-->
				
                <td align="center" width="150"><input type="text" name="carrier_pay[]" style="width:90px;" class="form-box-textfield alphanum" maxlength="20" id="carrier_pay-<?=$i?>" value="<?= $_POST['carrier_pay'][$i] ?>" onblur="updatePricing('<?=$i?>');"/><?= $_POST['carrier_pay'][$i] ?></td>
				<td align="center" width="150"><input type="text" name="deposit[]" style="width:90px;" id="deposit-<?=$i?>" class="form-box-textfield alphanum" maxlength="20"  value="<?= $_POST['deposit'][$i] ?>" onblur="updatePricing('<?=$i?>');"/><?= $_POST['deposit'][$i] ?></td>
                <td align="center" width="100">  
                  <div id="total_<?=$i?>">$ 0.00</div>
                </td>
				<td class="grid-body-right" align="center">
					<!--img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit" onclick="editVehicle(<?= $i+1 ?>)" class="action-icon"/-->
					<!--img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete" onclick="deleteVehicle(<?= $i+1 ?>)" class="action-icon"/-->
                    <a href="javascript:void(0);" style="color:#F30;" title="Delete" onclick="deleteVehicleNew(<?= $i ?>)"><b>Remove Vehicle</b></a>
				</td>
			</tr>
			<?php //endforeach; ?>
			<?php //endif; ?>
            <?php } ?>
			</tbody>
		</table>
		<?php } else { ?>
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="grid" style="white-space:nowrap;" id="vehicles-grid">
			<thead>
			<tr class="grid-head">
				<td class="grid-head-left">ID</td>
				<td >Year</td>
				<td>Make</td>
				<td>Model</td>
				<td>Type</td>
				<td>Vin #</td>
				<td>Lot #</td>
				<td>Inop</td>
				<td class="grid-head-right" width="60"><!--Actions--></td>
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
					<td><?= $vehicle->inop?'Yes':'No' ?></td>
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
		<?php } ?>
		<br/>
		<div><?= functionButton('Add Vehicle', 'addVehicleQuickNew()') ?></div>
	</div>
    <div style="text-align:right;">
		<table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table" style="white-space:nowrap;">
			<tr>
                <td width="30%">&nbsp;</td>
				
				<td  width="8%"><span id="carrier_pay1"><b>@carrier_pay@</b></span></td>
                <td  width="5%"><span id="total_deposit1"><b>@total_deposit@</b></span></td>
                <td width="2%" align="right"><b>Quoted Price:</b></td>
				<td  width="5%"><b><span id="total_tariff1">@total_tariff@</span></b></td>
			</tr>
			
		</table>
	</div>
</div>


<br/>
<div style="float:right">
	<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td>
				<input type="hidden" name="send_email" value="0" id="co_send_email"/>
				<?= functionButton("Save &amp; Email", 'createAndEmail();'); ?>
			</td>
			<td style="padding-left: 15px;"><?= submitButtons(SITE_IN."application/quotes", "Save") ?></td>
		</tr>
	</table>
</div>
</form>