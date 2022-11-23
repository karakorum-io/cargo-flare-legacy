<script type="text/javascript">
	$(document).ready(function() {
		var vehicleType = $("#add_vehicle_type");
		
		vehicleType.focus(function(){
			$(this).click();
			$(this).typeahead('search');
		});
		
		vehicleType.typeahead({
			minLength: 0,
			source: vehicle_type_data,
			autoFocus: true,
			showHintOnFocus:true,
		});

		var vehicleForm = $('#vehicle_form');
		vehicleForm.find("input.vehicle-make").typeahead({
			source: function(request, result) {
				$.ajax({
					url: '<?= SITE_IN ?>application/ajax/autocomplete.php',
					type: 'GET',
					dataType: 'json',
					data: {
						term: request.term,
						action: 'getVehicleMake'
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
			showHintOnFocus:true,
			change: function() {
				vehicleForm.find("input.vehicle-model").val('');
			}
		});

		vehicleForm.find("input.vehicle-model").typeahead({
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
			showHintOnFocus:true,
			autoFocus: true
		});
		
		vehicleForm.find('input.vehicle-make, input.vehicle-model, input.vehicle-type').focus(function() {
			var el = $(this);
			setTimeout(function() {
				if (el.val() == '') {
					console.log("log and searching");
					el.typeahead('search');
				}
			}, 300);
		});
	});
</script>

<!--begin::Modal-->
<div class="modal fade" id="vehicle_form" tabindex="-1" role="dialog" aria-labelledby="vehicle_form_model" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="vehicle_form_model">Add Vehicle</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i class="fa fa-times" aria-hidden="true"></i>
				</button>
			</div>
			<div class="modal-body">
				<div  class="new_input_height" >
					<div class="error" style="text-align: center; font-size: 15px;"></div>
					<div class="row form-group m_btm_10">
						<div class="col-12 col-sm-4">
							<label for="year"><span class="required">*</span>Year</label>/<label for="make">Make</label>
						</div>
						<div class="col-12 col-sm-8" id="make_data">
							<input type="text" class="form-box-textfield digit-only" name="year" id="year" maxlength="4" style="width:62px;" value=""/>
							<input type="text" class="form-box-textfield vehicle-make" name="make" id="make" maxlength="32" style="width:235px;;" value=""/>
						</div>
					</div>
					<div class="row form-group m_btm_10">
						<div class="col-12 col-sm-4">
							<label for="model"><span class="required">*</span>Model</label>
						</div>
						<div class="col-12 col-sm-8">
							<input type="text" class="form-box-textfield vehicle-model" name="model" id="model" maxlength="32" value=""/>
						</div>
					</div>
					<div class="row form-group m_btm_10">
						<div class="col-12 col-sm-4">
							<label for="add_vehicle_type"><span class="required">*</span>Type</label>
						</div>
						<div class="col-12 col-sm-8" id="someElem">
							<input type="text" class="form-box-textfield vehicle-type" name="type" maxlength="32" value="" id="add_vehicle_type"/>
						</div>
					</div>
					<div class="row form-group m_btm_10">
						<div class="col-12 col-sm-4">
							<label for="add_vehicle_vin">VIN #</label>
						</div>
						<div class="col-12 col-sm-8">
							<input type="text" class="form-box-textfield alphanum" name="vin" maxlength="20" value="" id="add_vehicle_vin"/>
						</div>
					</div>
					<div class="row form-group m_btm_10">
						<div class="col-12 col-sm-4">
							<label for="add_vehicle_color">Color</label>
						</div>
						<div class="col-12 col-sm-8">
							<input type="text" class="form-box-textfield" name="color" maxlength="32" value="" id="add_vehicle_color"/>
						</div>
					</div>
					<div class="row form-group m_btm_10">
						<div class="col-12 col-sm-4">
							<label for="add_vehicle_color">Plate #</label>
						</div>
						<div class="col-12 col-sm-8">
							<input type="text" class="form-box-textfield" name="plate" maxlength="32" value="" id="add_vehicle_plater"/>
						</div>
					</div>
					<div class="row form-group m_btm_10">
						<div class="col-12 col-sm-4">
							<label for="add_vehicle_state">State</label>
						</div>
						<div class="col-12 col-sm-8">
							<input type="text" class="form-box-textfield" name="state" maxlength="32" value="" id="add_vehicle_state"/>
						</div>
					</div>
					<div class="row form-group m_btm_10">
						<div class="col-12 col-sm-4">
							<label for="add_vehicle_lot">Lot #</label>
						</div>
						<div class="col-12 col-sm-8">
							<input type="text" class="form-box-textfield" name="lot" maxlength="32" value="" id="add_vehicle_lot"/>
						</div>
					</div>
					<div class="row form-group m_btm_10">
						<div class="col-12 col-sm-4">
							<label for="add_vehicle_inop">Inop</label>
						</div>
						<div class="col-12 col-sm-8">
							<select class="form-box-combobox" name="inop" id="add_vehicle_inop">
								<option value="0">No</option>
								<option value="1">Yes</option>
							</select>
						</div>
					</div>
					<div class="row form-group m_btm_10">
						<div class="col-12 col-sm-4">
							<label for="add_vehicle_carrier_pay"><span class="required">*</span>Total Pay</label>
						</div>
						<div class="col-12 col-sm-8">
							<input type="Number" class="form-box-textfield decimal" name="carrier_pay" maxlength="32" value="" id="add_vehicle_carrier_pay"/>
						</div>
					</div>
					<div class="row form-group m_btm_10">
						<div class="col-12 col-sm-4">
							<label for="add_vehicle_deposit"><span class="required">*</span>Deposit</label>
						</div>
						<div class="col-12 col-sm-8">
							<input type="Number" class="form-box-textfield decimal" name="deposit" maxlength="32" value="" id="add_vehicle_deposit"/>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn_dark_blue btn-sm" onclick=" AutoQuoteIndividual();" >AutoQuote</button>
				<button type="button" class="btn-dark btn-sm" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn_dark_green btn-sm" id="vehicleActionButton" onclick="saveVehicle(null)">Save</button>
			</div>
		</div>
	</div>
</div>
<!--end::Modal-->