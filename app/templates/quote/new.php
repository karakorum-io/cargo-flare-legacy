<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>@title@</title>
    <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/styles.css"/>
		<link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/application.css"/>
		<link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/default.css"/>
		<link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/jquery-ui.css"/>
		<link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/jquery.ui.timepicker.css"/>
		
		<script type="text/javascript">var BASE_PATH = '<?php echo SITE_IN ?>';</script>
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&language=en"></script>
		<script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.min.js"></script>
		<script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery-ui.js"></script>
		<script type="text/javascript" src="<?= SITE_IN ?>jscripts/ui.geo_autocomplete.js"></script>
		<script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.maskMoney.js"></script>
		<script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.nimble.loader.js"></script>
		<script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/functions.js"></script>
		<script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/application.js"></script>
		<script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.maskedinput-1.3.min.js"></script>
</head>
<body>
<script type="text/javascript">
	<?php if (isset($_POST['year'])) : ?>
	var v = <?= count($_POST['year']) ?>;
	<?php else : ?>
	var v = 0;
	<?php endif; ?>
	function deleteVehicle(id) {
		$("#vehicles-grid tbody tr[rel='"+id+"']").remove();
	}

	function saveVehicle(id) {
		var error = "";
		$("#vehicle_form input").each(function(){
			$(this).val($.trim($(this).val()));
			if ($(this).val() == "" && !in_array($(this).attr("name"), ['lot', 'vin', 'plate', 'state', 'color'])) {
				error+="<p>"+ucfirst($(this).attr("name"))+" value required.</p>";
			}
		});
		if (error != "") {
			$("#vehicle_form .error").html(error);
			$("#vehicle_form .error").slideDown().delay(3000).slideUp();
		} else {
			if (id == null) {
				v++;
				id = v;
			}
			var vehicle_row_body = '<tr class="grid-body" rel="'+id+'"><td class="grid-body-left" align="center">'
				+'<input type="hidden" name="year[]" value="'+$("#vehicle_form input[name='year']").val()+'"/>'
				+$("#vehicle_form input[name='year']").val()+'</td><td align="center">'
				+'<input type="hidden" name="make[]" value="'+$("#vehicle_form input[name='make']").val()+'"/>'
				+$("#vehicle_form input[name='make']").val()+'</td><td align="center">'
				+'<input type="hidden" name="model[]" value="'+$("#vehicle_form input[name='model']").val()+'"/>'
				+$("#vehicle_form input[name='model']").val()+'</td><td align="center">'
				+'<input type="hidden" name="type[]" value="'+$("#vehicle_form input[name='type']").val()+'"/>'
				+'<input type="hidden" name="lot[]" value="'+$("#vehicle_form input[name='lot']").val()+'"/>'
				+'<input type="hidden" name="vin[]" value="'+$("#vehicle_form input[name='vin']").val()+'"/>'
				+'<input type="hidden" name="plate[]" value="'+$("#vehicle_form input[name='plate']").val()+'"/>'
				+'<input type="hidden" name="state[]" value="'+$("#vehicle_form input[name='state']").val()+'"/>'
				+'<input type="hidden" name="color[]" value="'+$("#vehicle_form input[name='color']").val()+'"/>'
				+'<input type="hidden" name="inop[]" value="'+$("#vehicle_form select[name='inop']").val()+'"/>'
				+$("#vehicle_form input[name='type']").val()+'</td>'
				+'<td align="center">'+$("#vehicle_form input[name='vin']").val()+'</td>'
				+'<td align="center">'+$("#vehicle_form input[name='lot']").val()+'</td>'
				+'<td align="center">'+(($("#vehicle_form select[name='inop']").val() == '1')?'Yes':'No')+'</td>'
				+'<td align="center" class="grid-body-right">'
				+'<img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit" onclick="editVehicle('+id+')" class="action-icon"/>'
				+'<img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete" onclick="deleteVehicle('+id+')" class="action-icon"/></td></tr>';
			if ($("#vehicles-grid tbody tr[rel='"+id+"']").size() > 0) {
				$("#vehicles-grid tbody tr[rel='"+id+"']").replaceWith(vehicle_row_body);
			} else {
				$("#vehicles-grid tbody").append(vehicle_row_body);
			}

			zebra();
			$("#vehicle_form").dialog("close");
		}
	}
	
	function editVehicle(id) {
		$("#vehicle_form input").val("");
		$("#vehicle_form input[name='year']").val($("#vehicles-grid tbody tr[rel='"+id+"']").find("input[name='year[]']").val());
		$("#vehicle_form input[name='make']").val($("#vehicles-grid tbody tr[rel='"+id+"']").find("input[name='make[]']").val());
		$("#vehicle_form input[name='model']").val($("#vehicles-grid tbody tr[rel='"+id+"']").find("input[name='model[]']").val());
		$("#vehicle_form input[name='type']").val($("#vehicles-grid tbody tr[rel='"+id+"']").find("input[name='type[]']").val());
		$("#vehicle_form input[name='lot']").val($("#vehicles-grid tbody tr[rel='"+id+"']").find("input[name='lot[]']").val());
		$("#vehicle_form input[name='vin']").val($("#vehicles-grid tbody tr[rel='"+id+"']").find("input[name='vin[]']").val());
		$("#vehicle_form input[name='plate']").val($("#vehicles-grid tbody tr[rel='"+id+"']").find("input[name='plate[]']").val());
		$("#vehicle_form input[name='state']").val($("#vehicles-grid tbody tr[rel='"+id+"']").find("input[name='state[]']").val());
		$("#vehicle_form input[name='color']").val($("#vehicles-grid tbody tr[rel='"+id+"']").find("input[name='color[]']").val());
		$("#vehicle_form select[name='inop']").val($("#vehicles-grid tbody tr[rel='"+id+"']").find("input[name='inop[]']").val());
		$("#vehicle_form").dialog({
			width: 400,
			title: 'Add Vehicle',
			modal: true,
			resizable: false,
			buttons: [{
				text: "Save",
				click: function() {
					saveVehicle(id);
				}
			}]
		}).dialog("open");
	}
	
	function addVehicle() {
		
		$("#vehicle_form input").val("");
		$("#vehicle_form").dialog({
			width: 400,
			title: 'Add Vehicle',
			modal: true,
			resizable: false,
			buttons: [{
				text: "Save",
				click: function() {
					saveVehicle(null);
				}
			}, {
				text: "Cancel",
				click: function() {
					$(this).dialog("close");
				}
			}]
		}).dialog("open");
	}
	
	$(document).ready(function()
	{
		zebra();
		var cur_make = "";
		$(".datepicker").datepicker();
		$("#add_vehicle_type").focus(function(){
			$(this).click();
		});
		$("#add_vehicle_type").autocomplete({
			source: vehicle_type_data,
			autoFocus: true
		});
		
		$("#vehicle_form input.vehicle-make").autocomplete({
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
			autoFocus: true
		});
		
		$("#vehicle_form input.vehicle-model").autocomplete({
			source: function(request, response) {
				$.ajax({
					url: '<?= SITE_IN ?>application/ajax/autocomplete.php',
					type: 'GET',
					dataType: 'json',
					data: {
						term: request.term,
						action: 'getVehicleModel',
						make: $("input.vehicle-make").val()
					},
					success: function(data) {
						response(data);
					}
				})
			},
			autoFocus: true
		});
	});
</script>
<style type="text/css">
	.quote-edit .form-box-textfield {
		width: 210px;
	}
</style>
<div id="vehicle_form" style="display:none">
	<div class="error"></div>
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="form-table" style="white-space:nowrap;">
		<tr>
			<td>
				<label for="year"><span class="required">*</span>Year</label>/<label for="make">Make</label>
			</td>
			<td>
				<input type="text" class="form-box-textfield digit-only" name="year" id="year" maxlength="4" style="width:50px;" value=""/>
				<input type="text" class="form-box-textfield vehicle-make" name="make" id="make" maxlength="32" style="width:155px;" value=""/>
			</td>
		</tr>
		<tr>
			<td>
				<label for="add_vehicle_model"><span class="required">*</span>Model</label>
			</td>
			<td>
				<input type="text" class="form-box-textfield vehicle-model" name="model" maxlength="32" value="" id="add_vehicle_model"/>
			</td>
		</tr>
		<tr>
			<td>
				<label for="add_vehicle_type"><span class="required">*</span>Type</label>
			</td>
			<td>
				<input type="text" class="form-box-textfield" name="type" maxlength="32" value="" id="add_vehicle_type"/>
			</td>
		</tr>
		<tr>
			<td>
				<label for="add_vehicle_vin">VIN #</label>
			</td>
			<td>
				<input type="text" class="form-box-textfield alphanum" name="vin" maxlength="17" value="" id="add_vehicle_vin"/>
			</td>
		</tr>
		<tr>
			<td>
				<label for="add_vehicle_color">Color</label>
			</td>
			<td>
				<input type="text" class="form-box-textfield" name="color" maxlength="32" value="" id="add_vehicle_color"/>
			</td>
		</tr>
		<tr>
			<td>
				<label for="add_vehicle_plater">Plate #</label>
			</td>
			<td>
				<input type="text" class="form-box-textfield" name="plate" maxlength="32" value="" id="add_vehicle_plater"/>
			</td>
		</tr>
		<tr>
			<td>
				<label for="add_vehicle_state">State</label>
			</td>
			<td>
				<input type="text" class="form-box-textfield" name="state" maxlength="32" value="" id="add_vehicle_state"/>
			</td>
		</tr>
		<tr>
			<td>
				<label for="add_vehicle_lot">Lot #</label>
			</td>
			<td>
				<input type="text" class="form-box-textfield" name="lot" maxlength="32" value="" id="add_vehicle_lot"/>
			</td>
		</tr>
		<tr>
			<td>
				<label for="add_vehicle_inop">Inop</label>
			</td>
			<td>
				<select name="inop" class="form-box-combobox" id="add_vehicle_inop">
					<option value="0">No</option>
					<option value="1">Yes</option>
				</select>
			</td>
		</tr>
	</table>
</div>
<div style="clear:both;"></div>
<br/>
<div class="apl_centering">
<div>
@header@
</div>
<h3>Quote Request</h3>
Complete the form below and click Request Quote when finished. Required fields are marked with a <span style="color:red;">*</span><br/><br/>
@flash_message@
<form action="<?= getLink('quote/hash/'.$_GET['hash']) ?>" method="post">
<div class="quote-info" style="float:none;">
	<p class="block-title">Shipper Information</p>
	<div>
		<table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table quote-edit" style="white-space:nowrap;">
			<tr>
				<td>&nbsp;</td><td>&nbsp;</td>
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
<div class="quote-info" style="float:none;">
	<p class="block-title">Vehicle Information</p>
	<div>
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="grid" style="white-space:nowrap;" id="vehicles-grid">
			<thead>
				<tr class="grid-head">
					<td class="grid-head-left">Year</td>
					<td>Make</td>
					<td>Model</td>
					<td>Type</td>
					<td>Vin #</td>
					<td>Lot #</td>
					<td>Inop</td>
					<td class="grid-head-right" width="50">Actions</td>
				</tr>
			</thead>
			<tbody>
			<?php if (isset($_POST['year'])) : ?>
			<?php foreach($_POST['year'] as $i => $year) : ?>
			<tr class="grid-body" rel="<?= $i+1 ?>">
				<td align="center" class="grid-body-left<?= ($i==0)?' first-row':'' ?>"><input type="hidden" name="year[]" value="<?= $year ?>"/><?= $year ?></td>
				<td align="center"><input type="hidden" name="make[]" value="<?= $_POST['make'][$i] ?>"/><?= $_POST['make'][$i] ?></td>
				<td align="center"><input type="hidden" name="model[]" value="<?= $_POST['model'][$i] ?>"/><?= $_POST['model'][$i] ?></td>
				<td align="center">
					<input type="hidden" name="type[]" value="<?= $_POST['type'][$i] ?>"/>
					<input type="hidden" name="lot[]" value="<?= $_POST['lot'][$i] ?>"/>
					<input type="hidden" name="vin[]" value="<?= $_POST['vin'][$i] ?>"/>
					<input type="hidden" name="plate[]" value="<?= $_POST['plate'][$i] ?>"/>
					<input type="hidden" name="state[]" value="<?= $_POST['state'][$i] ?>"/>
					<input type="hidden" name="color[]" value="<?= $_POST['color'][$i] ?>"/>
					<input type="hidden" name="inop[]" value="<?= $_POST['inop'][$i] ?>"/>
					<?= $_POST['type'][$i] ?>
				</td>
				<td align="center"><?php $_POST['vin'][$i] ?></td>
				<td align="center"><?php $_POST['lot'][$i] ?></td>
				<td align="center"><?php (($_POST['inop'][$i] == '1')?'Yes':'No') ?></td>
				<td class="grid-body-right" align="center">
					<img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit" onclick="editVehicle(<?= $i+1 ?>)" class="action-icon"/>
					<img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete" onclick="deleteVehicle(<?= $i+1 ?>)" class="action-icon"/>
				</td>
			</tr>
			<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
        <br/>
		<div><?= functionButton('Add Vehicle', 'addVehicle()') ?></div>
	</div>
</div>
<br/>
<input type="hidden" name="referred_by" value="7"/>
<div style="float:right">
	<td><?= submitButtons("", "Save") ?></td>
</div>
</form>
<div style="margin-top: 20px;">
@footer@
</div>
</div>
</body>
</html>

