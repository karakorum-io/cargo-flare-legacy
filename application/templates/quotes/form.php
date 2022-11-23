<script type="text/javascript">
	$(document).ready(function() {
		var vehicleType = $("#add_vehicle_type");
		vehicleType.focus(function(){
			$(this).click();
			$(this).autocomplete('search');
		});
		vehicleType.autocomplete({
			minLength: 0,
			source: vehicle_type_data,
			autoFocus: true
		});
		var vehicleForm = $('#vehicle_form');
		vehicleForm.find("input.vehicle-make").autocomplete({
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
				vehicleForm.find("input.vehicle-model").val('');
			}
		});

		vehicleForm.find("input.vehicle-model").autocomplete({
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
			minLength: 0,
			autoFocus: true
		});
		vehicleForm.find('input.vehicle-make, input.vehicle-model').focus(function() {
			var el = $(this);
			setTimeout(function() {
				if (el.val() == '') {
					el.autocomplete('search');
				}
			}, 300);
		});
	});
</script>
<div id="vehicle_form" style="display:none">
	<div class="error"></div>
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="form-table" style="white-space:nowrap;">
		<tr>
			<td>
				<label for="year"><span class="required">*</span>Year</label>/<label for="make">Make</label>
			</td>
			<td>
				<input type="text" class="form-box-textfield digit-only" name="year" id="year" maxlength="4" style="width:62px;" value=""/>
				<input type="text" class="form-box-textfield vehicle-make" name="make" id="make" maxlength="32" style="width:155px;" value=""/>
			</td>
		</tr>
		<tr>
			<td>
				<label for="model"><span class="required">*</span>Model</label>
			</td>
			<td>
				<input type="text" class="form-box-textfield vehicle-model" name="model" id="model" maxlength="32" value=""/>
			</td>
		</tr>
		<tr>
			<td>
				<label for="add_vehicle_type"><span class="required">*</span>Type</label>
			</td>
			<td>
				<input type="text" class="form-box-textfield vehicle-type" name="type" maxlength="32" value="" id="add_vehicle_type"/>
			</td>
		</tr>
		<tr>
			<td>
				<label for="add_vehicle_vin">VIN #</label>
			</td>
			<td>
				<input type="text" class="form-box-textfield alphanum" name="vin" maxlength="20" value="" id="add_vehicle_vin"/>
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
				<select class="form-box-combobox" name="inop" id="add_vehicle_inop">
					<option value="0">No</option>
					<option value="1">Yes</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<label for="add_vehicle_carrier_pay"><span class="required">*</span>Total Pay</label>
			</td>
			<td>
				<input type="number" class="form-box-textfield decimal" name="carrier_pay" maxlength="32" value="" id="add_vehicle_carrier_pay"/>
			</td>
		</tr>
		<tr>
			<td>
				<label for="add_vehicle_deposit"><span class="required">*</span>Deposit</label>
			</td>
			<td>
				<input type="number" class="form-box-textfield decimal" name="deposit" maxlength="32" value="" id="add_vehicle_deposit"/>
			</td>
		</tr>
	</table>
</div>