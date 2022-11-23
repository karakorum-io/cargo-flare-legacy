<script type="text/javascript">

function updateVehiclesTable(data) {
	var rows = '';
	for (i in data) {
		/*rows+='<tr class="grid-body"><td class="grid-body-left"><?= $this->entity->getNumber() ?>-V'+(parseInt(i)+1)+'</td><td>'+decodeURIComponent(data[i].year)+'</td><td>'+decodeURIComponent(data[i].make)+'</td><td>'+decodeURIComponent(data[i].model)+'</td><td>'+decodeURIComponent(data[i].type)+'</td><td>'+decodeURIComponent(data[i].vin)+'</td><td>'+decodeURIComponent(data[i].lot)+'</td><td>'+((decodeURIComponent(data[i].inop) == '1')?'Yes':'No')+'</td><td align="center" class="grid-body-right">';
		*/
		rows+='<tr class="grid-body"><td class="grid-body-left"><?= $this->entity->getNumber() ?>-V'+(parseInt(i)+1)+'</td><td>'+decodeURIComponent(data[i].year)+'</td><td>'+decodeURIComponent(data[i].make)+'</td><td>'+decodeURIComponent(data[i].model)+'</td><td>'+decodeURIComponent(data[i].type)+'</td><td><input type="text" name="vehicle_tariff['+decodeURIComponent(data[i].id)+']" value="'+decodeURIComponent(data[i].tariff)+'" id="" /></td><td><input type="text" name="vehicle_deposit['+decodeURIComponent(data[i].id)+']" value="'+decodeURIComponent(data[i].deposit)+'" id="" /></td><td>'+((decodeURIComponent(data[i].inop) == '1')?'Yes':'No')+'</td><td align="center" class="grid-body-right">';
		rows+='<img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit" onclick="editVehicle('+data[i].id+')" class="action-icon"/>';
		rows+='<img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete" onclick="deleteVehicle('+data[i].id+')" class="action-icon"/></td></tr>';
	}
	$("#vehicles-grid tbody").html(rows);
	zebra();
}

function addVehicle() {
	var vehicleForm = $("#vehicle_form");
	vehicleForm.find("input, select").val("");
	vehicleForm.find("input[name='carrier_pay']").val('0');
	vehicleForm.find("input[name='deposit']").val('0');
	vehicleForm.dialog({
		width: 400,
		title: 'Edit Vehicle',
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

function saveVehicle(id) {
	var vehicleForm = $('#vehicle_form');
	var delivery_terminal_fee = parseFloat($("#delivery_terminal_fee").val());
	if (isNaN(delivery_terminal_fee)) delivery_terminal_fee = 0;
	var pickup_terminal_fee = parseFloat($("#pickup_terminal_fee").val());
	if (isNaN(pickup_terminal_fee)) pickup_terminal_fee = 0;
	$.ajax({
		type: "POST",
		url: "<?= SITE_IN ?>application/ajax/vehicles.php",
		dataType: 'json',
		data: {
			action: 'save',
			id: id,
			entity_id: <?= $this->entity->id ?>,
			year: encodeURIComponent(vehicleForm.find("input[name='year']").val()),
			make: encodeURIComponent(vehicleForm.find("input[name='make']").val()),
			model: encodeURIComponent(vehicleForm.find("input[name='model']").val()),
			type: encodeURIComponent(vehicleForm.find("input[name='type']").val()),
			vin: encodeURIComponent(vehicleForm.find("input[name='vin']").val()),
			lot: encodeURIComponent(vehicleForm.find("input[name='lot']").val()),
			plate: encodeURIComponent(vehicleForm.find("input[name='plate']").val()),
			state: encodeURIComponent(vehicleForm.find("input[name='state']").val()),
			color: encodeURIComponent(vehicleForm.find("input[name='color']").val()),
			inop: encodeURIComponent(vehicleForm.find("select[name='inop']").val()),
			carrier_pay: encodeURIComponent(vehicleForm.find("input[name='carrier_pay']").val()),
			deposit: encodeURIComponent(vehicleForm.find("input[name='deposit']").val()),
			delivery_terminal_fee: delivery_terminal_fee,
			pickup_terminal_fee: pickup_terminal_fee
		},
		success: function(result) {
			var vehicleForm = $("#vehicle_form");
			var vehicleFormError = vehicleForm.find('.error');
			if (result.success == true) {
				updateVehiclesTable(result.data);
				vehicleForm.dialog("close");
				$("#total_tariff").html(decodeURIComponent(result.total_tariff));
				$("#total_deposit").html(decodeURIComponent(result.total_deposit));
				$("#carrier_pay").html(decodeURIComponent(result.carrier_pay));
			} else {
				if (result.data != undefined) {
					var error = "";
					for (i in result.data) {
						error += "<p>"+decodeURIComponent(result.data[i])+"</p>";
					}
					vehicleFormError.html(error);
				} else {
					vehicleFormError.html("<p>Can't save vehicle. Try again later, please.</p>");
				}
				vehicleFormError.slideDown().delay(3000).slideUp();
			}
		},
		error: function() {
			$("#vehicle_form").find(".error").html("<p>Connection error. Try again later, please.</p>").slideDown().delay(3000).slideUp();
		}
	});
}

function editVehicle(id) {
	$.ajax({
		type: "POST",
		url: "<?= SITE_IN ?>application/ajax/vehicles.php",
		dataType: 'json',
		data: {
			action: 'get',
			id: id
		},
		success: function(result) {
			var vehicleForm = $("#vehicle_form");
			if (result.success == true) {
				vehicleForm.find("input[name='year']").val(decodeURIComponent(result.data['year']));
				vehicleForm.find("input[name='make']").val(decodeURIComponent(result.data['make']));
				vehicleForm.find("input[name='model']").val(decodeURIComponent(result.data['model']));
				vehicleForm.find("input[name='type']").val(decodeURIComponent(result.data['type']));
				vehicleForm.find("input[name='vin']").val(decodeURIComponent(result.data['vin']));
				vehicleForm.find("input[name='lot']").val(decodeURIComponent(result.data['lot']));
				vehicleForm.find("input[name='plate']").val(decodeURIComponent(result.data['plate']));
				vehicleForm.find("input[name='state']").val(decodeURIComponent(result.data['state']));
				vehicleForm.find("input[name='color']").val(decodeURIComponent(result.data['color']));
				vehicleForm.find("select[name='inop']").val(decodeURIComponent(result.data['inop']));
				vehicleForm.find("input[name='carrier_pay']").val(decodeURIComponent(result.data['carrier_pay']));
				vehicleForm.find("input[name='deposit']").val(decodeURIComponent(result.data['deposit']));
				vehicleForm.dialog({
					width: 400,
					title: 'Edit Vehicle',
					modal: true,
					resizable: false,
					buttons: [{
						text: "Save",
						click: function() {
							saveVehicle(id);
						}
					}, {
						text: "Cancel",
						click: function() {
							$(this).dialog("close");
						}
					}]
				}).dialog("open");
			}
		}
	});
}

function deleteVehicle(id) {
	if ($("#vehicles-grid").find("tbody tr").size() < 2) {
		alert("You can't delete the last vehicle.");
		return;
	}
	if (confirm("Are you sure want to remove this Vehicle?")) {
		$.ajax({
			type: "POST",
			url: BASE_PATH+"application/ajax/vehicles.php",
			dataType: "json",
			data: {
				action: 'del',
				id: id,
				entity_id: <?=$this->entity->id?>
			},
			success: function(result) {
				if (result.success == true) {
					updateVehiclesTable(result.data);
				} else {
					alert("Can't delete vehicle, Try again later, please.");
				}
			},
			error: function() {
				$("#vehicle_form").find(".error").html("<p>Connection error. Try again later, please.</p>").slideDown().delay(3000).slideUp();
			}
		})
	}
}

function updatePricingInfo() {
	//alert('test');
		var vehiclesGrid = $("#vehicles-grid");
		var carrier_pay = 0;
		var total_deposit = 0;
		var total_tariff = 0;
		var delivery_terminal_fee = parseFloat($("#delivery_terminal_fee").val());
		if (isNaN(delivery_terminal_fee)) delivery_terminal_fee = 0;
		var pickup_terminal_fee = parseFloat($("#pickup_terminal_fee").val());
		if (isNaN(pickup_terminal_fee)) pickup_terminal_fee = 0;
		
		vehiclesGrid.find("input[name='vehicle_id[]']").each(function() {
		
		     vehiclesGrid.find("input[name='vehicle_tariff["+$(this).val()+"]']").each(function() {
			  total_tariff += (isNaN(parseFloat($(this).val()))?0:parseFloat($(this).val()));
			 });
			
			vehiclesGrid.find("input[name='vehicle_deposit["+$(this).val()+"]']").each(function() {
			   total_deposit += (isNaN(parseFloat($(this).val()))?0:parseFloat($(this).val()));
		    });
			
			
		});
		
		
		//var total_tariff = carrier_pay + total_deposit + delivery_terminal_fee + pickup_terminal_fee;
		carrier_pay = total_tariff - total_deposit;
		total_tariff  = total_tariff + delivery_terminal_fee + pickup_terminal_fee;
		
		
		$("#total_tariff").text('$ '+total_tariff.toFixed(2));
		$("#total_deposit").text('$ '+total_deposit.toFixed(2));
		$("#carrier_pay").text('$ '+carrier_pay.toFixed(2));
		
		//$("#total_tariff_all").val(total_tariff.toFixed(2));
		//$("#total_deposit_all").val(total_deposit.toFixed(2));
		//$("#carrier_pay_all").val(carrier_pay.toFixed(2));
		
		  
		zebra();
	}
</script>