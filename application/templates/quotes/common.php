<style type="text/css">
  tr.Leads {
    font-size: 12px;
}
</style>

<script>
    $(document).ready(function(){
        $("#acc_search_string").keydown(function (e){
            if(e.keyCode == 13){
               accountSearch();
            }
        });
    });    
</script>

<?php
    if( strpos( $_SERVER['REQUEST_URI'], "create" ) !== false ) {    
?>
	<!--begin::Modal-->
	<div class="modal fade popup_" id="acc_search_dialog" tabindex="-1" role="dialog" aria-labelledby="acc_search_dialog_modal" aria-hidden="true" >
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="acc_search_dialog_modal">Select Shipper</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<i class="fa fa-times" aria-hidden="true"></i>
					</button>
				</div>
				<div class="modal-body">

					<div id="outerPopup" style="">
						<div class="tab-panel-container">
							<ul class="tab-panel dispatchPopup">
								<li class="tab"  id="tabLabel1" style="background:#dddddd">
									<span onclick="openNewTabs(1)" >Accounts</span>
								</li>
								<?php
									if($_SESSION['member']['parent_id'] != 1){
										$hideLeads = "display:none;";
									} else {
										$hideLeads = "display:block;";
									}
								?>
								<!--<li class="tab" id="tabLabel2" style="<?php echo $hideLeads;?>">
									<span onclick="openNewTabs(2)" >Leads</span>
								</li> -->
								<li class="tab" id="tabLabel2" >
									<span onclick="openNewTabs(2)" >Leads</span>
								</li>
							</ul>
							<div style="clear:both;"></div>
						</div>

						<table cellpadding="0" cellspacing="0" border="0" width="100%" class="form-table">
							<tr>
								<td width="37%"><input type="text" name="app_search_text" id="acc_search_string" style="width:98%" class="form-box-textfield" autocomplete="off"/></td>
								<td><?=functionButton('Search', "accountSearch()",'','btn-sm btn_dark_blue')?></td>
								<td align="right">
									<span class="like-link multi-vehicles">
										<i class="fa fa-info-circle" aria-hidden="true"></i><b></b>
									</span>
									<div class="search_help">
										<p style="text-align:left;">
										First Name<br>
										Last Name<br>
										First + Last Name<br>
										Company Name<br>
										Email<br>
										Any Phone Number in Account Profile.<br>
										(Phone 1, Phone 2, Mobile and Fax)<br>
										Address 1<br>
										Address 2<br>
										City<br>
										State<br>
										Zip<br>
										Any Combination of the Address<br>
										Address + City + State + FL, etc...<br>
										Order Id<br>
										</p>
									</div>
								</td>
							</tr>

							<tr>
								<td colspan="3">
									<div style="overflow-y:scroll; height:280px;">
										<script>
										function openNewTabs(tabCount){
										if(tabCount == 2){
										$("#ul2").show();
										$("#ul1").hide();
										$("#tabLabel2").css("background","#dddddd");
										$("#tabLabel1").css("background","#ffffff");
										} else {
										$("#tabLabel1").css("background","#dddddd");
										$("#tabLabel2").css("background","#ffffff");
										$("#ul2").hide();
										$("#ul1").show();
										}

										}
										var dispatchedPopupUI = '<?php echo $dispatchpopup;?>';
										if(dispatchedPopupUI == 'yes'){
										$(".dispatchPopup").html('');
										}
										</script>
										<ul id='ul1' style="display:block;">
											<table cellspacing="0" cellpadding="0" border="0" class="grid" width="100%">
												<thead class="dispatchPopup">
													<tr class="grid-head">
														<th>Select One</th>
														<th>Assigned To</th>
														<th>Shipper</th>
														<th>Address</th>
														<th>Last Order</th>
														<th>Credit Limits</th>
													</tr>
												</thead>
											<tbody id="acc_search_result"></tbody>
											</table>
										</ul>         
										<ul id='ul2' style='display:none;'>
											<table cellspacing="0" cellpadding="0" border="0" class="grid" width="100%">
												<thead class="dispatchPopup">
													<tr class="Leads">
														<th>Select One</th>
														<th>ID</th>
														<th>Created At</th>
														<th>Shipper</th>
														<th>Shipment Type</th>
														<th>Last Activity Date</th>
													</tr>
												</thead>
											<tbody id="acc_search_result_leads">

											</tbody>
											</table>
										</ul>
									</div>
								</td>
							</tr>
						</table>
					</div>

					<div id="innerPopup" style="display:none;">
						<div id='shipperinfo' style='padding-left:20px;  padding-top:20px;'></div>
						<hr>
						<div style="max-height:400px; overflow-y: auto;">
							<div style='padding-left:20px; overflow: hidden;'>
								<table cellspacing="0" cellpadding="0" border="0" class="grid" width="100%">
									<thead>
										<tr class="grid-head">
											<th>Order ID</th>
											<th><a id="createdDate" class="order-desc" onclick="getSortingDataCreated();" style="color:#fff;">Created Date</a></th>
											<th>Vehicles</th>
											<th>Route</th>
											<th colspan="2" align="center"><a id="avail_pickup_dateOrderBy" class="order-desc" onclick="getSortingDataDate1();" style="color:#fff;">Dates</a></th>
											<th>Transport Cost</th>
										</tr>
									</thead>
									<tbody id='orderInfo'>                    
									</tbody>
								</table>
								<input type="hidden" id="shipperId">
								<script>
									var orderBy = 2;
									var orderByDate1 =2;
									function getSortingDataCreated(){
										var shipper = $("#shipperId").val();
										if(orderBy == 1){
											orderBy = 2;
											$("#createdDate").addClass('order-desc');
											$("#createdDate").removeClass('order-asc');                            
										} else {
											orderBy = 1;
											$("#createdDate").removeClass('order-desc');
											$("#createdDate").addClass('order-asc');
										} 
										innerPHandler(shipper,1,orderBy);
										$("#shipperId").val("");
										$("#shipperinfo").html("");
										$("#orderInfo").html("");
										$("#loadingMeassage").show();
									}
									function getSortingDataDate1(){
										var shipper = $("#shipperId").val();
										if(orderByDate1 == 1){
											orderByDate1 = 2;
											$("#avail_pickup_dateOrderBy").addClass('order-desc');
											$("#avail_pickup_dateOrderBy").removeClass('order-asc');                            
										} else {
											orderByDate1 = 1;
											$("#avail_pickup_dateOrderBy").removeClass('order-desc');
											$("#avail_pickup_dateOrderBy").addClass('order-asc');
										}
										innerPHandler(shipper,2,orderByDate1);
										$("#shipperId").val("");
										$("#shipperinfo").html("");
										$("#orderInfo").html("");
										$("#loadingMeassage").show();
									}
								</script>
							</div>
							<div class="loadingMeassage">
								<center><img src="https://thumbs.gfycat.com/ImpoliteLivelyGenet-size_restricted.gif" height="300"></center>
							</div>
						</div>
						<hr>
						<div>
							<style>
							#chetuBack:hover
							{
								color:#f6f6f6 !important;
								background:#0073ea !important;
							}
							</style>
							<button id= "chetuBack" class="ui-button-text" onclick="closeInnerPopup()" style="width: 100px; height:25px; color:#0073ea; background: #f6f6f6; border: 1px solid #dddddd; border-radius: 2px; float:right;">Back</button>
						</div>
					</div>
				</div>
				
				<div class="modal-footer">
					<button type="button" class="btn btn-sm btn-dark" data-dismiss="modal">Close</button>
					<button type="button" data-dismiss="modal" onclick="$engine.applyShipperSearch(this)" class="btn btn-sm btn_light_green">Get Account Info</button>
				</div>
				
			</div>
		</div>
	</div>
<!--end::Modal-->

<?php
    }
?>

<!--begin::Modal-->
<div class="modal fade" id="vehiclePopup" tabindex="-1" role="dialog" aria-labelledby="vehiclePopup_model" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="vehiclePopup_model">VehicleInformation <span class="replies" ></span> </h5>
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
                    <div>
                        <div class=" kt-font-bold kt-link" onclick="entity_id()"> <span ><b>Order ID :</b> #</span><span id="orPrefix"></span>-<span class="kt-font-bold "   id="orNumber"></span> </div><br>
                            <span><b>Name :</b> </span> <span class="" id="shipName"></span><br>
                            <span><b>Company :</b> </span> <span id="shipComp"></span><br>
                            <span><b>Email :</b> </span> <span  id="shipEmail"></span><br>
                        </div>
                        
                        <div class="row mt-4 mb-2">
                            <h3 style="text-align: center; margin-left: 13px">Vehicle Information</h3>
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
                                        <td>
                                            <label for="year">Year</label>/<label for="make">Make<span class="required">*</span></label>
                                        </td>
                                        <td style="position: relative;" >
                                            <input type="text" class="form-box-textfield digit-only" name="year" id="year" maxlength="4" style="width:56px;" value="">
                                            <input type="text" class="form-box-textfield ui-autocomplete-input" name="make" id="vehicleMake" maxlength="3ss2" style="width:155px;" value="" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true">
                                        </td>
                                        <td>
                                            <label for="model"><span class="required">*</span>Model</label>
                                        </td>
                                        <td style="position: relative;" >
                                            <input type="text" class="form-box-textfield ui-autocomplete-input" name="model" id="vehicleModel" maxlength="32" value="" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true">
                                        </td>
                                    </tr>
                                    <tr  > 
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
                            
                            $('#vehicleMake').typeahead({
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
                                            console.log("Fetched Data");
                                            result($.map(data, function (item) {
                                                return item;
                                            }));
                                        }
                                    });
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
                                    Swal.fire("cannot delete single vehicle");
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
                                        Swal.fire("Mandiatory Fields are empty");
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

                                    } else
                                    {
                                    if(year == "" || make == "" || model == "" || type == "" || carrier == "" || deposite == "")
                                    {
                                        Swal.fire("Mandiatory Fields are empty");
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
                                var netDeposite      = $("#netDeposite").html();
                                netDeposite          = Number(netDeposite.substr(1));
                                var netCarrierPay    = $("#netCarrierPay").html();
                                netCarrierPay        = Number(netCarrierPay.substr(1));
                                var netTariff        = $("#netTariff").html();
                                netTariff            = Number(netTariff.substr(1));
                                var carrierPay       = Number($("#add_vehicle_carrier_pay").val());
                                var deposite         = Number($("#add_vehicle_deposit").val());
                                var tariff           = carrierPay + deposite;
                                netTariff            = netTariff + tariff;
                                netDeposite          = netDeposite + deposite;
                                netCarrierPay        = netCarrierPay + carrierPay;

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