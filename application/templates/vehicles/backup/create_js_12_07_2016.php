<script type="text/javascript">
	<?php if (isset($_POST['year'])) { ?>
	var v = <?= count($_POST['year']) ?>;
		<?php } else { ?>
	var v = 0;
		<?php } ?>
	var total_tariff = 0;
	var total_deposit = 0;
	function saveVehicle(id) {
		var error = "";
		var vehicleForm = $('#vehicle_form');
		vehicleForm.find("input").each(function(){
			$(this).val($.trim($(this).val()));
			if ($(this).val() == "" && !in_array($(this).attr("name"), ['vin', 'lot', 'state', 'plate', 'color'], undefined)) {
				error+="<p>"+ucfirst($(this).attr("name"))+" value required.</p>";
			}
		});
		if (error != "") {
			vehicleForm.find(".error").html(error).slideDown().delay(3000).slideUp();
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
				+$("#vehicle_form input[name='type']").val()+'</td><td align="center">'
				+'<input type="hidden" name="lot[]" value="'+$("#vehicle_form input[name='lot']").val()+'"/>'
				+'<input type="hidden" name="vin[]" value="'+$("#vehicle_form input[name='vin']").val()+'"/>'
				+'<input type="hidden" name="plate[]" value="'+$("#vehicle_form input[name='plate']").val()+'"/>'
				+'<input type="hidden" name="state[]" value="'+$("#vehicle_form input[name='state']").val()+'"/>'
				+'<input type="hidden" name="color[]" value="'+$("#vehicle_form input[name='color']").val()+'"/>'
				+'<input type="hidden" name="carrier_pay[]" value="'+$("#vehicle_form input[name='carrier_pay']").val()+'"/>'
				+'<input type="hidden" name="tariff[]" value="'+$("#vehicle_form input[name='carrier_pay']").val()+'"/>'
				+$("#vehicle_form input[name='vin']").val()+'</td><td align="center">'
				+'<input type="hidden" name="deposit[]" value="'+$("#vehicle_form input[name='deposit']").val()+'"/>'
				+$("#vehicle_form input[name='lot']").val()+'</td><td align="center">'
				+'<input type="hidden" name="inop[]" value="'+$("#vehicle_form select[name='inop']").val()+'"/>'
				+(($("#vehicle_form select[name='inop']").val() == '1')?'Yes':'No')+'</td>'
				+'<td align="center" class="grid-body-right">'
				+'<img src="'+BASE_PATH+'images/icons/edit.png" alt="Edit" title="Edit" onclick="editVehicle('+id+')" class="action-icon"/>'
				+'<img src="'+BASE_PATH+'images/icons/delete.png" alt="Delete" title="Delete" onclick="deleteVehicle('+id+')" class="action-icon"/></td></tr>';
			if ($("#vehicles-grid tbody tr[rel='"+id+"']").size() > 0) {
				$("#vehicles-grid tbody tr[rel='"+id+"']").replaceWith(vehicle_row_body);
			} else {
				$("#vehicles-grid tbody").append(vehicle_row_body);
			}
			updatePricingInfo();
			zebra();
			$("#vehicle_form").dialog("close");
		}
	}
	function editVehicle(id) {
		var vehicleForm = $('#vehicle_form');
		var vehiclesGridRow = $('#vehicles-grid').find("tbody tr[rel='"+id+"']");
		vehicleForm.find(" input").val("");
		vehicleForm.find("input[name='year']").val(vehiclesGridRow.find("input[name='year[]']").val());
		vehicleForm.find("input[name='make']").val(vehiclesGridRow.find("input[name='make[]']").val());
		vehicleForm.find("input[name='model']").val(vehiclesGridRow.find("input[name='model[]']").val());
		vehicleForm.find("input[name='type']").val(vehiclesGridRow.find("input[name='type[]']").val());
		vehicleForm.find("input[name='lot']").val(vehiclesGridRow.find("input[name='lot[]']").val());
		vehicleForm.find("input[name='vin']").val(vehiclesGridRow.find("input[name='vin[]']").val());
		vehicleForm.find("input[name='state']").val(vehiclesGridRow.find("input[name='state[]']").val());
		vehicleForm.find("input[name='plate']").val(vehiclesGridRow.find("input[name='plate[]']").val());
		vehicleForm.find("input[name='color']").val(vehiclesGridRow.find("input[name='color[]']").val());
		vehicleForm.find("select[name='inop']").val(vehiclesGridRow.find("select[name='inop[]']").val());
		vehicleForm.find("input[name='carrier_pay']").val(vehiclesGridRow.find("input[name='carrier_pay[]']").val());
		vehicleForm.find("input[name='deposit']").val(vehiclesGridRow.find("input[name='deposit[]']").val());
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
		var vehicleForm = $("#vehicle_form");
		vehicleForm.find("input, select").val("");
		vehicleForm.find("input[name='carrier_pay']").val('0');
		vehicleForm.find("input[name='deposit']").val('0');
		vehicleForm.dialog({
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

	function deleteVehicle(id) {
		$("#vehicles-grid").find("tbody tr[rel='"+id+"']").remove();
		updatePricingInfo();
		zebra();
	}


	function updatePricingInfo() {
		var vehiclesGrid = $("#vehicles-grid");
		var carrier_pay = 0;
		var total_deposit = 0;
		
		/*
		var delivery_terminal_fee = parseFloat($("#delivery_terminal_fee").val());
		if (isNaN(delivery_terminal_fee)) delivery_terminal_fee = 0;
		var pickup_terminal_fee = parseFloat($("#pickup_terminal_fee").val());
		if (isNaN(pickup_terminal_fee)) pickup_terminal_fee = 0;
		vehiclesGrid.find("input[name='carrier_pay[]']").each(function() {
			carrier_pay += (isNaN(parseFloat($(this).val()))?0:parseFloat($(this).val()));
		});
		vehiclesGrid.find("input[name='deposit[]']").each(function() {
			total_deposit += (isNaN(parseFloat($(this).val()))?0:parseFloat($(this).val()));
		});
		var total_tariff = carrier_pay + total_deposit + delivery_terminal_fee + pickup_terminal_fee;
		*/
		
		var total_tariff = 0;
		var delivery_terminal_fee = parseFloat($("#delivery_terminal_fee").val());
		if (isNaN(delivery_terminal_fee)) delivery_terminal_fee = 0;
		var pickup_terminal_fee = parseFloat($("#pickup_terminal_fee").val());
		if (isNaN(pickup_terminal_fee)) pickup_terminal_fee = 0;
		vehiclesGrid.find("input[name='carrier_pay[]']").each(function() {
			//carrier_pay += (isNaN(parseFloat($(this).val()))?0:parseFloat($(this).val()));
			total_tariff += (isNaN(parseFloat($(this).val()))?0:parseFloat($(this).val()));
			
		});
		vehiclesGrid.find("input[name='deposit[]']").each(function() {
			total_deposit += (isNaN(parseFloat($(this).val()))?0:parseFloat($(this).val()));
		});
		
		carrier_pay = total_tariff - total_deposit;
		//var total_tariff = carrier_pay + total_deposit + delivery_terminal_fee + pickup_terminal_fee;
		total_tariff  = total_tariff + delivery_terminal_fee + pickup_terminal_fee;
		
		$("#total_tariff").text('$ '+total_tariff.toFixed(2));
		$("#total_deposit").text('$ '+total_deposit.toFixed(2));
		$("#carrier_pay").text('$ '+carrier_pay.toFixed(2));
		zebra();
	}

</script>