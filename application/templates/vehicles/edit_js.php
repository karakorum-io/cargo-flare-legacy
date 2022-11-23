<script type="text/javascript">

function updateVehiclesTable(data,id) {
	var rows = '';
	var vehicle_tariff_total =0;
	var vehicle_deposit_total =0;
	var vehicle_carrier_total =0;
	for (i in data) {

		var vehicle_tariff = decodeURIComponent(data[i].tariff);
		var vehicle_deposit = decodeURIComponent(data[i].deposit);

		if(id != data[i].id)
		{
				var vehicle_tariff_object = $("#vehicle_tariff_"+decodeURIComponent(data[i].id));
				if(vehicle_tariff_object!='' && vehicle_tariff_object != null && vehicle_tariff_object != undefined)
				{
					if(!isNaN(parseFloat(vehicle_tariff_object.val())))
					  vehicle_tariff = vehicle_tariff_object.val();
					//alert(parseFloat(vehicle_tariff_object.val()));
				}

				var vehicle_deposit_object = $("input[name='vehicle_deposit["+decodeURIComponent(data[i].id)+"]']");

				if(vehicle_deposit_object!='' && vehicle_deposit_object != null && vehicle_deposit_object != 'undefined')
				{
					if(!isNaN(parseFloat(vehicle_deposit_object.val())))
					vehicle_deposit = vehicle_deposit_object.val();//vehicle_deposit_object.val();
					//alert(vehicle_deposit);
				}



		}
	            vehicle_tariff_total += parseFloat(vehicle_tariff);
				vehicle_deposit_total += parseFloat(vehicle_deposit);
		/*rows+='<tr class="grid-body"><td class="grid-body-left"><?=$this->entity->getNumber()?>-V'+(parseInt(i)+1)+'</td><td>'+decodeURIComponent(data[i].year)+'</td><td>'+decodeURIComponent(data[i].make)+'</td><td>'+decodeURIComponent(data[i].model)+'</td><td>'+decodeURIComponent(data[i].type)+'</td><td>'+decodeURIComponent(data[i].vin)+'</td><td>'+decodeURIComponent(data[i].lot)+'</td><td>'+((decodeURIComponent(data[i].inop) == '1')?'Yes':'No')+'</td><td align="center" class="grid-body-right">';
		*/
		/*rows+='<tr class="grid-body"><td class="grid-body-left"><?=$this->entity->getNumber()?>-V'+(parseInt(i)+1)+'</td><td>'+decodeURIComponent(data[i].year)+'</td><td>'+decodeURIComponent(data[i].make)+'</td><td>'+decodeURIComponent(data[i].model)+'</td><td>'+decodeURIComponent(data[i].type)+'</td><td><input type="text" name="vehicle_tariff['+decodeURIComponent(data[i].id)+']" value="'+vehicle_tariff+'" id="vehicle_tariff_'+decodeURIComponent(data[i].id)+'" /></td><td><input type="text" name="vehicle_deposit['+decodeURIComponent(data[i].id)+']" value="'+vehicle_deposit+'" id="vehicle_deposit1_'+decodeURIComponent(data[i].id)+'" /></td><td>'+((decodeURIComponent(data[i].inop) == '1')?'Yes':'No')+'</td><td align="center" class="grid-body-right">';
		*/
		rows+='<tr class="grid-body"><td class="grid-body-left"><?=$this->entity->getNumber()?>-V'+(parseInt(i)+1)+'</td><td>'+decodeURIComponent(data[i].year)+'</td><td>'+decodeURIComponent(data[i].make)+'</td><td>'+decodeURIComponent(data[i].model)+'</td><td>'+decodeURIComponent(data[i].type)+'</td><td><input type="text" name="vin['+decodeURIComponent(data[i].id)+']" value="'+decodeURIComponent(data[i].vin)+'" id="vin_'+decodeURIComponent(data[i].id)+'" /></td><td><input type="text" name="vehicle_tariff['+decodeURIComponent(data[i].id)+']" value="'+vehicle_tariff+'" id="vehicle_tariff_'+decodeURIComponent(data[i].id)+'" onkeyup="updatePricingInfo();"/></td><td><input type="text" name="vehicle_deposit['+decodeURIComponent(data[i].id)+']" value="'+vehicle_deposit+'" id="vehicle_deposit_'+decodeURIComponent(data[i].id)+'" onkeyup="updatePricingInfo();"/><input type="hidden" name="vehicle_id[]" value="'+decodeURIComponent(data[i].id)+'"  /></td><td>'+((decodeURIComponent(data[i].inop) == '1')?'Yes':'No')+'</td><td align="center" class="grid-body-right">';

		rows+='<img src="<?=SITE_IN?>images/icons/copy.png" alt="Copy" title="Copy" onclick="copyVehicle('+data[i].id+')" class="action-icon"/>';
		rows+='<img src="<?=SITE_IN?>images/icons/edit.png" alt="Edit" title="Edit" onclick="editVehicle('+data[i].id+')" class="action-icon"/>';
		rows+='<img src="<?=SITE_IN?>images/icons/delete.png" alt="Delete" title="Delete" onclick="deleteVehicle('+data[i].id+')" class="action-icon"/></td></tr>';
	}
	$("#vehicles-grid tbody").html(rows);
	zebra();

	$("#total_tariff").html(decodeURIComponent(vehicle_tariff_total.toFixed(2)));
	$("#total_deposit").html(decodeURIComponent(vehicle_deposit_total.toFixed(2)));
	$("#carrier_pay").html(decodeURIComponent((vehicle_tariff_total - vehicle_deposit_total).toFixed(2)));
}

function addVehicle() {
	var vehicleForm = $("#vehicle_form");
	vehicleForm.find("input, select").val("");
	vehicleForm.find("input[name='carrier_pay']").val('0');
	vehicleForm.find("input[name='deposit']").val('0');
	$("#vehiclePopupButton").attr("onclick","saveVehicle(null)");
	$("#vehicle_form").modal();
}

function saveVehicle(id) {
	var vehicleForm = $('#vehicle_form');

	console.log(vehicleForm);
	console.log(vehicleForm.find("input[name='lot']").val());
	
	var delivery_terminal_fee = parseFloat($("#delivery_terminal_fee").val());
	
	if (isNaN(delivery_terminal_fee)) {
		delivery_terminal_fee = 0;
	} 
	
	var pickup_terminal_fee = parseFloat($("#pickup_terminal_fee").val());
	
	if (isNaN(pickup_terminal_fee)) {
		pickup_terminal_fee = 0;
	}

	$.ajax({
		type: "POST",
		url: "<?=SITE_IN?>application/ajax/vehicles.php",
		dataType: 'json',
		data: {
			action: 'save',
			id: id,
			entity_id: <?=$this->entity->id?>,
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
				console.log(result.data);
				console.log(id);
				updateVehiclesTable(result.data,id);
				$("#vehicle_form").modal('hide');

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
		url: "<?=SITE_IN?>application/ajax/vehicles.php",
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
				$("#vehiclePopupId").val(id);
				$("#vehicle_form").modal();
				$("#vehicleActionButton").attr("onclick","saveVehicle("+id+")");
				//$("#vehiclePopupButton").attr("onclick","updateVehicle()");
				$("#vehiclePopupId").val(id);
			}
		}
	});
}

function updateVehicle() {

	var error = "";
        var vForm = $('#vehicle_form');
        vForm.find("input").each(function () {
            $(this).val($.trim($(this).val()));
            if (($(this).val() == "" || $(this).val() == 0) && !in_array($(this).attr("name"), ['vin', 'lot', 'state', 'plate', 'color','vehiclePopupId'], undefined)) {
                error += "<p>" + $(this).attr("name") + " value required.</p>";
            }
        });
        if (error != "") {
            vForm.find(".error").html(error).slideDown().delay(3000).slideUp();
        } else {
			var vehicleForm = $("#vehicle_form");
			let id = $("#vehiclePopupId").val();
			
			$.ajax({
				type: "POST",
				url: "<?=SITE_IN?>application/ajax/vehicles.php",
				dataType: 'json',
				data: {
					action: 'save',
					id: id,
					entity_id: <?=$this->entity->id?>,
					year: vehicleForm.find("input[name='year']").val(),
					make: vehicleForm.find("input[name='make']").val(),
					model: vehicleForm.find("input[name='model']").val(),
					type: vehicleForm.find("input[name='type']").val(),
					inop: vehicleForm.find("select[name='inop']").val(),
					vin: vehicleForm.find("input[name='vin']").val(),
					carrier_pay: vehicleForm.find("input[name='carrier_pay']").val(),
					deposit: vehicleForm.find("input[name='deposit']").val()

				},
				success: function(result) {
					
					if (result.success == true) {
						$("#vehicle_form").modal('hide');
						alert("Vehicle Modified");
						//updateVehiclesTable(result.data,id);
						//location.reload();
					} else {
						alert(result.data);
					}
				},
				error: function() {
					alert("Connection error. Try again later, please.");
				}
			});
		}
}

function deleteVehicle(id) {

	if ($("#vehicles-grid").find("tbody tr").length < 2) {
		swal.fire("You can't delete the last vehicle.");
		return;
	}

		Swal.fire({
		title: 'Are you sure?',
		text: "You won't be able to revert this!",
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Yes, delete it!'
		}).then((result) => {
		if (result.value) {

			$.ajax({
			type: "POST",
			url:  BASE_PATH+"application/ajax/vehicles.php",
			cache: false,
			dataType: "json",
			data: {
				action: 'del',
				id: id,
				entity_id: <?=$this->entity->id?>
			},
			success: function(result){
				 console.log(result);
				 location.reload(true);

				if(result.success == false) {
					swal.fire("Can't delete vehicle, Try again later, please.");
				} else {
					updateVehiclesTable(result.data);

				}
			},
			error: function() {
				$("#vehicle_form").find(".error").html("<p>Connection error. Try again later, please.</p>").slideDown().delay(3000).slideUp();
			}
		})

		}
		})



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

function copyVehicle(id) {
	//var vehicleForm = $('#vehicle_form');

	$.ajax({
		type: "POST",
		url: "<?=SITE_IN?>application/ajax/vehicles.php",
		dataType: 'json',
		data: {
			action: 'copy',
			id: id,
			entity_id: <?=$this->entity->id?>,
			vin: encodeURIComponent($('#vin_'+id).val()),
			tariff: encodeURIComponent($('#vehicle_tariff_'+id).val()) ,
			deposit: encodeURIComponent($('#vehicle_deposit_'+id).val())

		},
		success: function(result) {
			if (result.success == true) {
				updateVehiclesTable(result.data,id);
			} else {
				alert("Can't copy vehicle. Try again later, please.");
			}
		},
		error: function() {
			alert("Connection error. Try again later, please.");
		}
	});

}
</script>
