<script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script> 

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
    .search_result_item{
        font-size: 0px;

    }
    

    tr.accrow td {
        color: #374afb !important;
    }
    li#acc_search_result_new_previous {
        display: none;
    }li#acc_search_result_new_next {
        display: none;
    }
    .row-white td {
        color: #fff !important;
    }
    #tabPanel{
        padding-top: 12px;
        padding-left: 12px;
        padding-bottom: 30px;
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

<!--select shipper modal starts-->
<div class="modal fade" id="acc_search_dialog" tabindex="-1" role="dialog" aria-labelledby="acc_search_dialog_model" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="acc_search_dialog_model">Select Shipper</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <div class="modal-body" style="padding:0px;">
            <?php
                if (strpos($_SERVER['REQUEST_URI'], "dispatchnew") !== false) {
                    $dispatchpopup = "yes";
                } else {
                    $dispatchpopup = "no";
                }
            ?>
            <?php
                if ( strpos($_SERVER['REQUEST_URI'], "create") !== false || strpos($_SERVER['REQUEST_URI'], "dispatchnew") !== false || strpos($_SERVER['REQUEST_URI'], "edit") !== false ) {
            ?>
            <!--Shipper popup UI and Inner popup UI-->
            <div id="outerPopup">
                <div class="tabWrapper">
                    <style>
                        .shipper-popup-tab {
                            margin-left: 10px !important;
                        }
                    </style>
                    <div class="tabMetronic shipper-popup-tab tab-shipper-main tab-shipper-main-1 tabMetronic-active" onclick="openNewTabs(1)">
                        <div class="wizard-label">
                            <h3 class="wizard-title tab-color tab-shipper-border tab-shipper-border-1 tab-color-active">
                                <span class="override-size">A</span>ccounts
                            </h3>
                        </div>
                    </div>
                    <div class="tabMetronic shipper-popup-tab tab-shipper-main tab-shipper-main-2" onclick="openNewTabs(2)">
                        <div class="wizard-label">
                            <h3 class="wizard-title tab-color tab-shipper-border tab-shipper-border-2">
                                <span class="override-size">L</span>eads
                            </h3>
                        </div>
                    </div>
                </div>
                <table class="table table-bordered">
                    <tr>
                        <?php
                        if (strpos($_SERVER['REQUEST_URI'], "dispatchnew") !== false) {
                        ?>
                        <td><input type="text" name="app_search_text" id="acc_search_string" style="width:85%" class="form-box-textfield" autocomplete="off"/></td>
                        <td><?= functionButton('Search', "accountSearch()",'',' btn_dark_blue btn-sm') ?>

                        <td align="right">&nbsp;<span class="like-link multi-vehicles"> <i class="fas fa-info-circle" title="Details" alt="Details"></i></span>
                        <?php } else { ?>
                        <td id="searchBar" style="width:80%"><input type="text" name="app_search_text" id="acc_search_string" style="width:85%" class="form-box-textfield" /></td>
                        <td align="left" id="search"><?= functionButton('Search', "accountSearch()",'','btn_dark_blue btn-sm') ?>
                        <?php } ?>
                        &nbsp;&nbsp;<i class="fas fa-info-circle" data-toggle="modal" data-target="#shipperSuggestion"></i>
                        <div class="search_help">
                            <?php
                            if (strpos($_SERVER['REQUEST_URI'], "dispatchnew") !== false) {
                                ?>
                                <p style="text-align: left;">
                                    Company Name<br>
                                    Email<br>
                                    Any Phone Number in Account Profile.<br>
                                    (Phone 1, Phone 2, Mobile and Fax)<br>
                                    Contact Name1<br>
                                    Contact Name2
                                </p>

                            <?php } else {
                                ?>
                                
                            <?php } ?>
                        </div>
                        </td>
                    </tr>
                </table>
                <table class="table table-bordered">
                    <tr>
                        <td>
                            <div >
                                <script>
                                    function openNewTabs(tabCount) {
                                        if (tabCount == 2) {
                                            $("#ul2").show();
                                            $("#ul1").hide();
                                            $(".tab-shipper-border").removeClass("tab-color-active");
                                            $(".tab-shipper-main").removeClass("tabMetronic-active");

                                        } else {
                                            $("#ul2").hide();
                                            $("#ul1").show();
                                            $(".tab-shipper-border").removeClass("tab-color-active");
                                            $(".tab-shipper-main").removeClass("tabMetronic-active");
                                        }

                                        $(".tab-shipper-border-"+tabCount).addClass("tab-color-active");
                                        $(".tab-shipper-main-"+tabCount).addClass("tabMetronic-active");
                                    }
                                    var dispatchedPopupUI = '<?php echo $dispatchpopup; ?>';
                                    if (dispatchedPopupUI == 'yes') {
                                        $(".dispatchPopup").html('');
                                    }
                                </script>
                                <ul id='ul1' style="display:block;">
                                    <table id="select_shipper"  class="table-bordered table">
                                        <thead class="dispatchPopup" id="shipperPopupTableHeader">
                                            <tr>
                                                <th>#</th> 
                                                <th>Assigned</th>
                                                <th>Shipper</th>
                                                <th>Address</th>
                                                <th>Last Order</th>
                                                <th>Contract</th>
                                                <th>Credit Limits</th>
                                            </tr>
                                        </thead>
                                        <tbody id="acc_search_result"></tbody>
                                    </table>                               
                                </ul>
                                <ul id='ul2' style='display:none;'>
                                    <table id="select_shipper_leads" class="table-bordered table">
                                        <thead class="dispatchPopup" >
                                            <tr>
                                                <th>#</th>
                                                <th>ID</th>
                                                <th>Created At</th>
                                                <th>Shipper</th>
                                                <th>Shipment Type</th>
                                                <th>Contract</th>
                                                <th>Last Activity Date</th>
                                            </tr>
                                        </thead>
                                        <tbody id="acc_search_result_leads"></tbody>
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
                <div>
                    <div style='padding-left:20px; overflow: hidden;'>
                        <table class="table table-bordered table-responsible">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th><a id="createdDate" class="order-desc" onclick="getSortingDataCreated();" style="color:#fff;">Created Date</a></th>
                                    <th>Vehicles</th>
                                    <th>Route</th>
                                    <th colspan="2" align="center"><a id="avail_pickup_dateOrderBy" class="order-desc" onclick="getSortingDataDate1();" style="color:#fff;">Dates</a></th>
                                    <th>Transport Cost</th>
                                </tr>
                            </thead>
                            <tbody id='orderInfo'></tbody>
                        </table>
                        <input type="hidden" id="shipperId">
                        <script>
                            var orderBy = 2;
                            var orderByDate1 = 2;
                            function getSortingDataCreated() {
                                var shipper = $("#shipperId").val();
                                if (orderBy == 1) {
                                    orderBy = 2;
                                    $("#createdDate").addClass('order-desc');
                                    $("#createdDate").removeClass('order-asc');
                                } else {
                                    orderBy = 1;
                                    $("#createdDate").removeClass('order-desc');
                                    $("#createdDate").addClass('order-asc');
                                }

                                innerPHandler(shipper, 1, orderBy);
                                $("#shipperId").val("");
                                $("#shipperinfo").html("");
                                $("#orderInfo").html("");
                                $("#loadingMeassage").show();
                            }
                            function getSortingDataDate1() {
                                var shipper = $("#shipperId").val();
                                if (orderByDate1 == 1) {
                                    orderByDate1 = 2;
                                    $("#avail_pickup_dateOrderBy").addClass('order-desc');
                                    $("#avail_pickup_dateOrderBy").removeClass('order-asc');
                                } else {
                                    orderByDate1 = 1;
                                    $("#avail_pickup_dateOrderBy").removeClass('order-desc');
                                    $("#avail_pickup_dateOrderBy").addClass('order-asc');
                                }

                                innerPHandler(shipper, 2, orderByDate1);
                                $("#shipperId").val("");
                                $("#shipperinfo").html("");
                                $("#orderInfo").html("");
                                $("#loadingMeassage").show();
                            }
                        </script>
                    </div>
                    <div class="loadingMeassage"><center><img src="https://thumbs.gfycat.com/ImpoliteLivelyGenet-size_restricted.gif" height="300"></center></div>
                </div>
                <hr>
                <div>
                    <style>
                        #chetuBack:hover{
                            color:#f6f6f6 !important;
                            background:#0073ea !important;
                        }
                    </style>
                    <button id= "chetuBack" class="ui-button-text" onclick="closeInnerPopup()" style="width: 100px; height:25px; color:#0073ea; background: #f6f6f6; border: 1px solid #dddddd; border-radius: 2px; float:right;">Back</button>
                </div>
            </div>
            <?php
                }
            ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn_dark_green btn-sm" onclick="$engine.applyShipperSearch(this)">Get Account Info</button>
            </div>
        </div>
    </div>
</div>
<!--select shipper modal ends-->

<!--begin::Modal-->
<div class="modal fade" id="acc_search_dialog_new_dispatch" tabindex="-1" role="dialog" aria-labelledby="acc_search_dialog_new_dispatch_model" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="acc_search_dialog_new_dispatch_model">Select Carrier</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <div class="modal-body">
                <table cellpadding="0" cellspacing="0" border="0" width="100%" class="form-table">
                    <tr>
                        <td width="100%"><input type="text" name="app_search_text_new_dispatch" id="acc_search_string_new_dispatch" style="width:98%" class="form-box-textfield"/></td>
                        <td><?= functionButton('Search', "accountSearchNewDispatch()",'','  btn_dark_blue btn-sm') ?></td>
                        <td>&nbsp;<span class="like-link multi-vehicles"><b>[?]</b></span>
                            <div class="search_help">
                                <p>
                                    Company<br />
                                    Phone Number1 <br />
                                    Phone Number2<br />
                                    Contact Name1<br />
                                    Contact Name2<br />
                                </p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <div class="spinner-border" role="status" id="carrier" style="display: none;">
                                <span class="sr-only">Loading...</span>
                            </div>    
                        </td>
                    </tr>
                    <tr id="colorCod" style="display:none;">
                        <td colspan="3">
                            <table width="100%">
                                <tr><td bgcolor="#F0FF1A" width="10%" ></td> <td>Insurance is about to expire.</td></tr>
                                <tr><td bgcolor="#FF1A24" width="10%"></td> <td>Insurance expired.</td></tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <div id="acc_search_result_new_dispatch"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn_dark_green btn-sm" onclick="$operations.dispatchWithNewCarrier(<?php echo isset($_GET['id']) ? $_GET['id'] : null ?>)">New Carrier</button>
                <button type="button" class="btn btn_dark_blue btn-sm" onclick="$operations.dispatchWithExistingCarrier()">Get Account Info</button>
                <button type="button" class="btn btn-dark btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!--end::Modal-->

<div id="acc_entity_dispatch_dialog" style="display: none">
    <table cellspacing="2" cellpadding="0" border="0">
        <tr>
            <td><input type="radio" name="delivery_credit_select" id="delivery_credit_select_r" value="0" checked > <b>Regular</b></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td><h3>Speed Pay</h3></td>
        </tr>
        <tr>
            <td><input type="radio" name="delivery_credit_select" id="delivery_credit_select_a" value="1" > <b>Option A - Next Day Delivery 5% + $12.00</b></td>
        </tr>
        <tr>
            <td><input type="radio" name="delivery_credit_select" id="delivery_credit_select_b" value="2" > <b>Option B - Next Day Delivery 3% + $12.00</b></td>
        </tr>
    </table>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#acc_search_string_new_dispatch').keypress(function (e) {

            if (e.which == 13) {
                //alert('You pressed enter!');
                var textOrder = $("#add_order").val();
                if (textOrder != "")
                    accountSearchNewDispatch();
                return false;
            }
        });
    });

    $(document).ready(function () {
        $("#acc_search_string").keydown(function (e) {
            if (e.keyCode == 13) {
                accountSearch();
            }
        });
    });
</script>

<?php include 'search-carrier.php';?>