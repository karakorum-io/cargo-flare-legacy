<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<!-- Chetu Inc. Updates: Custom Loader-->
<style>
    #charliesOverLay{
        width:100%;
        height:100%;
        background:#fff;
        position: fixed;
        left:0px;
        top:0px;
        z-index:99999;
        opacity: 0.7;
        display:none;
    }
    #charliesLoader{
        display:none;
        width:500px;
        height:200px;
        background:#fff;
        position: fixed;
        left:0px;
        top:0px;
        z-index:999999;
        font-size: 16px;
        margin: 14% 33%;
        text-align: center;
        padding-top:20px;
    }
    .error_reassing
    {
        color: red;
        padding: 10px;
    }
    div#notes_container_new {
        /* text-align: center; */
        margin-left:15px;
    }
    button.ui-button.ui-widget.ui-state-default.ui-corner-all.ui-button-text-only {
        background: #5578eb;
        color: white;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 1em;
    }
    #kt_modal_4 .modal-header button.close:before{
        display: none;

    }
</style>
<div class='charliesLoaderContent' id="charliesOverLay"></div>
<div class='charliesLoaderContent' id="charliesLoader">
    <img src="<?php echo SITE_IN; ?>images/ajax-loader.gif"><br>
    <progressData>Processing Request</progressData>
</div>

<!--begin::Modal-->
<div class="modal fade" id="vehiclePopup" tabindex="-1" role="dialog" aria-labelledby="vehiclePopup_model" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="vehiclePopup_model">Vehicle Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <?php
                    if (strpos($_SERVER['REQUEST_URI'], "orders") !== false) {
                        $showOrderVehicleInformation = "display: block;";
                    } else {
                        $showOrderVehicleInformation = "display: none;";
                    }
                    ?>
                    <div id="orderVehicleInformation" style="<?php echo $showOrderVehicleInformation; ?>">
                        <div >
                            <span>Order ID: #</span><span id="orPrefix" class="kt-font-success"></span>-<span class="kt-font-success" id="orNumber"></span><br>
                            <span>Name: </span><span class="kt-font-success" id="shipName"></span><br>
                            <span> Company:</span> <span  class="kt-font-info" id="shipComp"></span><br>
                        <span>Email:</span> <span class="kt-font-danger" id="shipEmail"></span><br>
                        </div>

                    
                    <div class="row mt-4 mb-2">
                    <h2 class="kt-font-info" style="text-align: center; margin-left: 13px">Vehicle Information</h2>
                    </div>
                    </div>

                    <div id="vehicleNewLists">
                        <table id="vehicleTable" class="form-table table-bordered fixed-table-head">
                            <thead class="dispatchPopup">
                                <tr class="grid-head">
                                    <th class="tHeader"></th>
                                    <th class="tHeader"> S:No </th>
                                    <th class="tHeader"> Year </th>
                                    <th class="tHeader"> Make </th>
                                    <th class="tHeader">Model</th>
                                    <th class="tHeader">Type</th>
                                    <th class="tHeader">Vin#</th>                        
                                    <th class="tHeader">Inop</th>
                                    <th class="tHeader">Total Pay</th>
                                    <th class="tHeader">Deposit</th>
                                    <th class="tHeader">Action</th>
                                </tr>
                            </thead>
                            <tbody id="vehicleList"></tbody>
                        </table>
                        <br>
                        <div>                
                            <div class="text-right">                    
                                <button id="addNewVehicle" onclick="addVehicle()" class=" btn-sm btn btn_dark_blue m-r-10">Add Vehicle</button>
                                <button id="copyVehicle" onclick="copyOnScreen()" class=" btn-sm btn btn_light_green">Copy Vehicle</button>
                            </div>
                        </div>
                    
                    <div class="row mt-4 mb-2">
                    <h4 class="kt-font-info" style="margin-left: 13px">Total Tariff: <span id="netTariff">XXXXX</span> Total Carrier Pay: <span id="netCarrierPay" class="t-font-dark">XXXXX</span>  Total Deposite: <span id="netDeposite">YYYYYY</span></h4>
                    </div>       
                    </div>
                    
                    <div id="editVehicleForm" style="display: none;">
                        <div>
                            <br>
                            <h3 id="editAddTrigger" type="edit" class="kt-font-info" style="padding-left: 8px">Edit Vehicle Information</h3>
                            <input type="hidden" id="vehicleId">
                            <input type="hidden" id="entityId">
                            <input type="hidden" id="vehicleEntityId">
                            <input type="hidden" id="currentRow" value="">
                            <table cellpadding="0" cellspacing="0" border="0" width="100%" class="form-table" style="white-space:nowrap;">
                                <tbody>
                                    <tr>
                                        <td colspan="4"><strong>Fields marked (*) cannot be left empty</strong></td>
                                    </tr>
                                    <tr>
                                        <td >
                                            <label for="year">Year</label>/<label for="make">Make<span class="required">*</span></label>
                                        </td>
                                        <td style="position: relative;" >
                                            <input type="text" class="form-box-textfield digit-only" name="year" id="year" maxlength="4" style="width:56px;" value="">
                                            <input type="text" class="form-box-textfield ui-autocomplete-input" name="make" id="vehicleMake" maxlength="3ss2" style="width:155px;" value="" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true">
                                        </td>
                                        <td>
                                            <label for="model"><span class="required">*</span>Model</label>
                                        </td>
                                        <td style="position: relative;">
                                            <input type="text" class="form-box-textfield ui-autocomplete-input" name="model" id="vehicleModel" maxlength="32" value="" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="add_vehicle_type"><span class="required">*</span>Type</label>
                                        </td>
                                        <td style="position: relative;">
                                            <input type="text" class="form-box-textfield ui-autocomplete-input" name="type" maxlength="32" value="" id="vehicleType" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true">
                                        </td>
                                        <td>
                                            <label for="add_vehicle_vin">VIN #</label>
                                        </td>
                                        <td>
                                            <input type="text" class="form-box-textfield alphanum" name="vin" maxlength="20" value="" id="add_vehicle_vin">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="add_vehicle_color">Color</label>
                                        </td>
                                        <td>
                                            <input type="text" class="form-box-textfield" name="color" maxlength="32" value="" id="add_vehicle_color">
                                        </td>
                                        <td>
                                            <label for="add_vehicle_plater">Plate #</label>
                                        </td>
                                        <td>
                                            <input type="text" class="form-box-textfield" name="plate" maxlength="32" value="" id="add_vehicle_plater">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="add_vehicle_state">State</label>
                                        </td>
                                        <td>
                                            <input type="text" class="form-box-textfield" name="state" maxlength="32" value="" id="add_vehicle_state">
                                        </td>
                                        <td>
                                            <label for="add_vehicle_lot">Lot #</label>
                                        </td>
                                        <td>
                                            <input type="text" class="form-box-textfield" name="lot" maxlength="32" value="" id="add_vehicle_lot">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="add_vehicle_inop">Inop</label>
                                        </td>
                                        <td>
                                            <select class="form-box-combobox width-215" name="inop" id="add_vehicle_inop">
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                        </td>
                                        <td>
                                            <label for="add_vehicle_carrier_pay"><span class="required">*</span>Total Pay</label>
                                        </td>
                                        <td>
                                            <input type="Number" class="form-box-textfield decimal" name="carrier_pay" maxlength="32" value="" id="add_vehicle_carrier_pay">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="add_vehicle_deposit"><span class="required">*</span>Deposit</label>
                                        </td>
                                        <td colspan="3">
                                            <input type="Number" class="form-box-textfield decimal" name="deposit" maxlength="32" value="" id="add_vehicle_deposit">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                            <div>
                                <div class="text-right" >
                                    <button class="btn-sm btn btn-dark m-r-10" id="backVehiclePopup">Back</button>
                    <button class="btn btn-sm btn_dark_blue" type="button" id="addSave" onclick="addOnSave()" functionality="save" >Save</button>
                                </div>
                            </div>
                        </div>
                        <script>
                            //Delete on screen
                            $(document).ready(function () {
                                $("#backVehiclePopup").click(function () {
                                    $("#vehicleNewLists").show();
                                    $("#editVehicleForm").hide();
                                });
                            });

                            /* vehicle field auto complete functionality*/
                            var vehicleType = $("#vehicleType");
                        
                            vehicleType.focus(function () {
                                $(this).click();
                                $(this).typeahead('search');
                            });

                            vehicleType.typeahead({
                                minLength: 0,
                                source: vehicle_type_data,
                                autoFocus: true
                            });

                            $("#vehicleMake").typeahead({
                                source: function (request, result) {
                                    $.ajax({
                                        url: '<?= SITE_IN ?>application/ajax/autocomplete.php',
                                        type: 'GET',
                                        dataType: 'json',
                                        data: {
                                            term: request.term,
                                            action: 'getVehicleMake'
                                        },
                                        success: function (data) {
                                            result($.map(data, function (item) {
                                            return item;
                                            }));
                                        }
                                    })
                                },
                                minLength: 0,
                                autoFocus: true,
                                change: function () {
                                    $("#vehicleModel").val('');
                                }
                            });

                            $("#vehicleModel").typeahead({
                                source: function (request, result) {
                                    $.ajax({
                                        url: '<?= SITE_IN ?>application/ajax/autocomplete.php',
                                        type: 'GET',
                                        dataType: 'json',
                                        data: {
                                            term: request.term,
                                            action: 'getVehicleModel',
                                            make: $("#vehicleMake").val()
                                        },
                                        success: function (data) {
                                            result($.map(data, function (item) {
                                            return item;
                                            }));
                                        }
                                    })
                                },
                                minLength: 0,
                                autoFocus: true
                            });

                            $('#vehicleMake').focus(function () {
                                var el = $(this);
                                setTimeout(function () {
                                    if (el.val() == '') {
                                        el.typeahead('search');
                                    }
                                }, 300);
                            });

                            $('#vehicleModel').focus(function () {
                                var el = $(this);
                                setTimeout(function () {
                                    if (el.val() == '') {
                                        el.typeahead('search');
                                    }
                                }, 300);
                            });

                            function deleteOnScreen(i) {
                                /* Single vehicle delete prevention validation*/
                                if ($(".vehiclePopupRow").length < 2) {
                                    swal.fire("cannot delete single vehicle");
                                    return false;
                                }

                                var netDeposite = $("#netDeposite").html();
                                netDeposite = Number(netDeposite.substr(1));

                                var netCarrierPay = $("#netCarrierPay").html();
                                netCarrierPay = Number(netCarrierPay.substr(1));

                                var netTariff = $("#netTariff").html();
                                netTariff = Number(netTariff.substr(1));

                                var carrierPay = Number($("#tariff" + i).html());
                                var deposite = Number($("#deposite" + i).html());
                                var tariff = carrierPay + deposite;

                                netTariff = netTariff - tariff;
                                netDeposite = netDeposite - deposite;
                                netCarrierPay = netCarrierPay - carrierPay;

                                $("#netTariff").html("$" + netTariff);
                                $("#netDeposite").html("$" + netDeposite);
                                $("#netCarrierPay").html("$" + netCarrierPay);
                                $("#rowid" + i).remove();
                            }

                            function fillEditForm(row) {
                                $("#editVehicleForm").show();
                                $("#vehicleNewLists").hide();
                                $("#editAddTrigger").html("");
                                $("#editAddTrigger").html("Edit Information");
                                $("#year").val($("#year" + row).html());
                                $("#vehicleMake").val($("#make" + row).html());
                                $("#vehicleModel").val($("#model" + row).html());
                                $("#vehicleType").val($("#vType" + row).html());
                                $("#add_vehicle_vin").val($("#vin" + row).html());
                                $("#add_vehicle_carrier_pay").val($("#tariff" + row).html());
                                $("#add_vehicle_deposit").val($("#deposite" + row).html());
                                $("#add_vehicle_inop").val($("#inop" + row).val());
                                $("#addSave").attr("functionality", "save");
                                $("#addSave").html("Edit");
                                $("#currentRow").val(row);
                            }

                            function addOnSave() {
                                var functionality = $("#addSave").attr("functionality");
                                var year = $("#year").val();
                                var make = $("#vehicleMake").val();
                                var model = $("#vehicleModel").val();
                                var type = $("#vehicleType").val();
                                var vin = $("#add_vehicle_vin").val();
                                var color = $("#add_vehicle_color").val();
                                var plate = $("#add_vehicle_plater").val();
                                var state = $("#add_vehicle_state").val();
                                var lot = $("#add_vehicle_lot").val();
                                var carrier = $("#add_vehicle_carrier_pay").val();
                                var deposite = $("#add_vehicle_deposit").val();
                                var inop = $("#add_vehicle_inop").val() == 0 ? "No" : "Yes";

                                if (functionality == "add") {

                                    if (year == "" || make == "" || model == "" || type == "" || carrier == "" || deposite == "") {
                                        swal.fire("Mandiatory Fields are empty");
                                    } else {

                                        var numItems = $('.vehiclePopupRow').length;
                                        var lastRowNumber = 0;

                                        $('.vehiclePopupRow').each(function () {
                                            lastRowNumber = this.id[this.id.length - 1];
                                        });

                                        var vehicleId = $("#vehicleId").val();
                                        var entityId = $("#radio" + lastRowNumber).attr("entity");

                                        var newRow = 0;
                                        var LastRow = $(".vehiclePopupRow:last").attr("id")[$(".vehiclePopupRow:last").attr("id").length - 1];
                                        newRow = Number(LastRow) + 1;
                                        var html = "";
                                        $("#vehicleList").append("<tr class='vehiclePopupRow' id='rowid" + newRow + "'>\n\
                                                <td align='center'><input id='radio" + newRow + "' row='" + newRow + "' entity='" + entityId + "' name='vehicleId' type='radio' class='vehicleId' value=''></td>\n\
                                                <td id='year" + newRow + "'>" + year + "</td>\n\
                                                <td id='model" + newRow + "'>" + model + "</td>\n\
                                                <td id='make" + newRow + "'>" + make + "</td>\n\
                                                <td id='vType" + newRow + "'>" + type + "</td>\n\
                                                <td id='vin" + newRow + "'>" + vin + "</td>\n\
                                                <td id='inop" + newRow + "'>" + inop + "</td>\n\
                                                <td id='tariff" + newRow + "'>" + carrier + "</td>\n\
                                                <td id='deposite" + newRow + "'>" + deposite + "</td>\n\
                                                <td align='center'>\n\
                                                    <img onclick='fillEditForm(" + newRow + ")' src='/images/icons/edit.png' title='Edit' alt='Edit' width='16' height='16'>&nbsp;&nbsp;&nbsp;\n\
                                                    <img onclick='deleteOnScreen(" + newRow + ")' src='/images/icons/delete.png' title='Delete' alt='Delete' class='deleteVehicle' width='16' height='16'></td>\n\
                                                </tr>");

                                    }


                                } else {
                                    if (year == "" || make == "" || model == "" || type == "" || carrier == "" || deposite == "") {
                                        swal.fire("Mandiatory Fields are empty");
                                    } else {
                                        var row = $("#currentRow").val();
                                        $("#year" + row).html($("#year").val());
                                        $("#model" + row).html($("#vehicleModel").val());
                                        $("#make" + row).html($("#vehicleMake").val());
                                        $("#vType" + row).html($("#vehicleType").val());
                                        $("#vin" + row).html($("#add_vehicle_vin").val());
                                        $("#inop" + row).html($("#add_vehicle_inop").val() == 0 ? "No" : "Yes");
                                        $("#tariff" + row).html($("#add_vehicle_carrier_pay").val());
                                        $("#deposite" + row).html($("#add_vehicle_deposit").val());

                                    }
                                }
                                /* adjust the total tarrif and carrier pay*/
                                var netDeposite = $("#netDeposite").html();
                                netDeposite = Number(netDeposite.substr(1));

                                var netCarrierPay = $("#netCarrierPay").html();
                                netCarrierPay = Number(netCarrierPay.substr(1));

                                var netTariff = $("#netTariff").html();
                                netTariff = Number(netTariff.substr(1));

                                var carrierPay = Number($("#add_vehicle_carrier_pay").val());
                                var deposite = Number($("#add_vehicle_deposit").val());
                                var tariff = carrierPay + deposite;

                                netTariff = netTariff + tariff;
                                netDeposite = netDeposite + deposite;
                                netCarrierPay = netCarrierPay + carrierPay;

                                $("#netTariff").html("$" + netTariff);
                                $("#netDeposite").html("$" + netDeposite);
                                $("#netCarrierPay").html("$" + netCarrierPay);

                                $("#editVehicleForm").hide();
                                $("#vehicleNewLists").show();
                            }
                        </script>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                <button type="button" onclick="saveVehicleChanges()" class="btn btn_dark_blue">Save</button>
            </div>
        </div>
    </div>
</div>
<!--end::Modal-->

<script type="text/javascript">	var notes = [];	notes[<?= Note::TYPE_TO ?>] = [];	notes[<?= Note::TYPE_FROM ?>] = [];	notes[<?= Note::TYPE_INTERNAL ?>] = [];
    var notesIntervalId = undefined;
    var add_entity_id;
    var add_notes_type;
    var add_busy = false;
    function printLeads(printWindow, entity_ids) {
        if (entity_ids.length > 0) {
            $.ajax({type: "POST", url: "<?= SITE_IN ?>application/ajax/entities.php", dataType: "json", data: {action: 'print', entity_ids: entity_ids}, success: function (response) {
                    if (response.success == true) {
                        printWindow.document.write('<html><head><title>Leads</title>');						printWindow.document.write('<link rel="stylesheet" href="<?= SITE_IN ?>styles/application_print.css" type="text/css" />');						printWindow.document.write('</head><body><table cellspacing="0" cellpadding="3" border="1" width="100%">');						printWindow.document.write('<tr><th>ID</th><th>Received</th><th>Shipper</th><th>Vehicle</th><th>Origin/Destination</th><th>Est. Ship</th></tr>');
                        for (i in response.data) {
                            printWindow.document.write('<tr>');
                            printWindow.document.write('<td class="nowrap">' + response.data[i].id + '</td>');
                            printWindow.document.write('<td>' + response.data[i].received + '</td>');
                            printWindow.document.write('<td>' + response.data[i].shipper + '</td>');
                            printWindow.document.write('<td>' + response.data[i].vehicle + '</td>');
                            printWindow.document.write('<td>' + response.data[i].origin_dest + '</td>');
                            printWindow.document.write('<td>' + response.data[i].est_ship + '</td>');							printWindow.document.write('</tr>');
                        }
                        printWindow.document.write('</table></body></html>');
                        printWindow.print();
                        printWindow.close();
                    } else {
                        printWindow.close();
                    }
                }});
        } else {
            printWindow.alert('You have no entities to print!');
            printWindow.close();
        }
    }
</script>

<div id="print_container" style="display:none"></div>
<div id="notes_container" ></div>

<!--reassign modal-->
<div class="modal fade" id="kt_modal_5" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Reassign Lead</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i class="fa fa-times" aria-hidden="true"></i>
				</button>
			</div>
			<div class="modal-body">
				<div class="error_reassing" style="display: none;"></div>
				<select class="form-box-combobox form-control" id="company_members">
                    <option value=""><?php print "Select One"; ?></option>
                    <?php foreach ($this->company_members as $member) : ?>
                    <?php
                        if ($member->status == "Active") {
                            $activemember .= "<option value= '" . $member->id . "'>" . $member->contactname . "</option>";
                        }
                    ?>
                    <?php endforeach; ?>
                    <optgroup label="Active User">
                        <?php echo $activemember; ?>
                    </optgroup>
				</select>
			</div>
			 <div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="button" onclick="reassignOrders_submit()" class="btn btn-primary">Save</button>
			</div> 
		</div>
	</div>
</div>

<div style="padding-top: 10px;">
	<? include_once("menu.php"); ?>
	<div style="clear: both"></div>
	@content@
</div>

<!--begin::Modal-->
<div id="notes_add1">
	<div class="modal fade" id="kt_modal_4" tabindex="-1" role="dialog" aria-labelledby="notes_add12" style="display: none;" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">

				<div class="modal-header">
					<h5 class="modal-title" id="notes_add12">
						<div id="notes_add_title"> </div>
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<i class="fa fa-times" aria-hidden="true"></i>
					</button>
				</div>

				<div class="modal-body">
					 
					<div class="form-group" style="max-height:300px;overflow:auto;">
						<div id="notes_container_new" class="notes_container_new_info"> </div>
					</div>

					<div class="form-group">
						<label for="message-text" class="form-control-label">Add Internal Note:</label>
						<textarea class="form-control"  class="form-box-textarea" name="add_note_text" ></textarea>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								 <label for="message-text" class="form-control-label">Quick Notes:</label>
								<select name="quick_notes" class="form-control" id="quick_notes" onchange="addQuickNote();">
									<option value="">--Select--</option>
									<option value="Emailed: Customer.">Emailed: Customer.</option>
									<option value="Emailed: Bad e-mail.">Emailed: Bad e-mail.</option>
									<option value="Faxed: e-Sign.">Faxed: e-Sign.</option>
									<option value="Faxed: B2B.">Faxed: B2B.</option>
									<option value="Faxed: Invoice.">Faxed: Invoice.</option>
									<option value="Faxed: Recepit.">Faxed: Recepit.</option>
									<option value="Phoned: Bad Mobile.">Phoned: Bad Number.</option>
									<option value="Phoned: No Voicemail.">Phoned: No Voicemail.</option>
									<option value="Phoned: Left Message.">Phoned: Left Message.</option>
									<option value="Phoned: No Answer.">Phoned: No Answer.</option>
									<option value="Phoned: Spoke to Customer.">Phoned: Spoke to Customer.</option>
									<option value="Phoned: Spoke to carrier about pick-up.">Phoned: Spoke to carrier about pick-up.</option>
									<option value="Phoned: NSpoke to carrier about drop-off.">Phoned: Spoke to carrier about drop-off.</option>
									<option value="Phoned: Customer requested carrier info.">Phoned: Customer requested carrier info.</option>
									<option value="Phoned: Customer requested damage.">Phoned: Customer requested damage.</option>
									<option value="Phoned: Customer canceled, late pick-up.">Phoned: Customer canceled, late pick-up.</option>
									<option value="Phoned: Customer canceled, no reason given.">Phoned: Customer canceled, no reason given.</option>
									<option value="Phoned: Customer canceled, through e-Mail.">Phoned: Customer canceled, through e-Mail.</option>
									<option value="Phoned: Customer was happy with transport.">Phoned: Customer was happy with transport.</option>
									<option value="Phoned: Customer was un-happy with transport.">Phoned: Customer was un-happy with transport.</option>
									<option value="Phoned: Customer want a refund.">Phoned: Customer want's a refund.</option>
									<option value="Phoned: Not Interested.">Phoned: Not Interested.</option>
									<option value="Phoned: Do Not Call.">Phoned: Do Not Call.</option>
								</select>
							</div>

						</div>

						<div class="col-md-6">
							<div  class="form-group">
							   <label for="message-text" class="form-control-label">Priority:</label>
								<select name="priority_notes"  class="form-control" id="priority_notes" >
									<option value="0">--Select--</option>
									<option value="2">High</option>
									<option value="1">Low</option>
								</select>
							</div>
						</div>
					</div>      

					<?= functionButton('Add Note', 'addNote()') ?>
					<?= functionButton('Cancel', 'closeAddNotes()') ?>
				</div>
			</div>
		</div>
	</div>
</div>
<!--end::Modal-->