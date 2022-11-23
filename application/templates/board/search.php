<style type="text/css">
	.select2-container--default .select2-selection--multiple .select2-selection__rendered .select2-selection__choice,
	.select2-container--default .select2-results__option.select2-results__option--highlighted
	{
		color:#222;
	}
    ul.select2-selection__rendered {
    width: 284px !important;
}
</style>

<script type="text/javascript">

    $(function() {
    $("#advanced_search_mainbox").find("#advanced_search_information").hide();
    $("#advanced_search_mainbox").click(function(){
        $("#basic_search_mainbox").find("#basic_search_information").hide();
    })

     $("#basic_search_mainbox").click(function(){
        $("#advanced_search_mainbox").find("#advanced_search_information").hide();
    })

    });

function basicSubmit() {
    $("#basic_search_form").submit();
}
function advancedSubmit() {
    $("#search_form").submit();
}
function originCitySearch(enable) {
    if (enable) {
        $("#origin_region").hide();
        $("#origin_region").val([]);
        $("#origin_city_search_table").show();
    } else {
        $("#origin_city_search_table").hide();
        $("#origin_city").val("");
        $("#origin_state").val("");
        $("#origin_zip").val("");
        $("#origin_region").show();
    }
}
function originBCitySearch(enable) {
    if (enable) {
        $("#b_origin_region").hide();
        $("#b_origin_region").val([]);
        $("#b_origin_city_search_table").show();
    } else {
        $("#b_origin_city_search_table").hide();
        $("#b_origin_city").val("");
        $("#b_origin_state").val("");
        $("#b_origin_zip").val("");
        $("#b_origin_region").show();
    }
}
function destinationCitySearch(enable) {
    if (enable) {
        $("#destination_region").hide();
        $("#destination_region").val([]);
        $("#destination_city_search_table").show();
    } else {
        $("#destination_city_search_table").hide();
        $("#destination_city").val("");
        $("#destination_state").val("");
        $("#destination_zip").val("");
        $("#destination_region").show();
    }
}
function destinationBCitySearch(enable) {
    if (enable) {
        $("#b_destination_region").hide();
        $("#b_destination_region").val([]);
        $("#b_destination_city_search_table").show();
    } else {
        $("#b_destination_city_search_table").hide();
        $("#b_destination_city").val("");
        $("#b_destination_state").val("");
        $("#b_destination_zip").val("");
        $("#b_destination_region").show();
    }
}
$(document).ready(function () {
    /*$("#search_forms").accordion({
        autoHeight:false,
        animated:'slide',
        collapsible:true,
        active: <?= (isset($this->results)) ? 'false' : '0' ?>
    }); */
    $("#search_forms h3").mouseout(function () {
        $(this).removeClass("ui-state-focus");
    });
    $("#origin_city_search").change(function () {
        originCitySearch($(this).is(':checked'));
    });
    $("#destination_city_search").change(function () {
        destinationCitySearch($(this).is(":checked"));
    });
    $("#b_origin_city_search").change(function () {
        originBCitySearch($(this).is(':checked'));
    });
    $("#b_destination_city_search").change(function () {
        destinationBCitySearch($(this).is(":checked"));
    });
    $("#vehicle_types").val([]);
    /*$("#vehicle_types").multiselect({
        noneSelectedText:'Select Type',
        selectedText:'# types selected',
        selectedList:1
    });*/
	
	$('#vehicle_types').select2({
		placeholder: "Select Type",
		allowClear: true
	});
	
});
function clearOriginCity() {
    $("#origin_city").val("");
    $("#origin_state").val("");
    $("#origin_zip").val("");
}
function clearDestinationCity() {
    $("#destination_city").val("");
    $("#destination_state").val("");
    $("#destination_zip").val("");
}
function clearBOriginCity() {
    $("#b_origin_city").val("");
    $("#b_origin_state").val("");
    $("#b_origin_zip").val("");
}
function clearBDestinationCity() {
    $("#b_destination_city").val("");
    $("#b_destination_state").val("");
    $("#b_destination_zip").val("");
}
function saveSearch() {
    var busy = false;
    $("#promptSearchName").modal();

}

function promptSearchName_save()
{
         var busy = false;
        if (busy) return;
            var searchName = $.trim($("#promptSearchName input").val());
            if (searchName == '') {
                $("#promptSearchName .error").html("<p>Search name required.</p>");
                $("#promptSearchName .error").slideDown(500).delay(2000).slideUp(500);
            } else {
                var formData = $("#search_form").serialize();
                busy = true;
                $.ajax({
                    type:"POST",
                    url:"<?= SITE_IN ?>application/ajax/vehicles.php",
                    dataType:'json',
                    data:{
                        action:'saveSearch',
                        name:encodeURIComponent(searchName),
                        data:encodeURIComponent(formData)
                    },
                    success:function (response) {
                        busy = false;
                        if (response.success == true) {
                            $("#promptSearchName").modal("hide");
                        } else {
                            $("#promptSearchName .error").html("<p>Save failed, try again later.</p>");
                            $("#promptSearchName .error").slideDown(500).delay(2000).slideUp(500);
                        }
                    },
                    error:function (response) {
                        busy = false;
                        $("#promptSearchName .error").html("<p>Save failed, try again later.</p>");
                        $("#promptSearchName .error").slideDown(500).delay(2000).slideUp(500);
                    }
                });
            }
}

function loadSearchForm(search_id) {
    // // $("body").nimbleLoader("show");
    $.ajax({
        type:"POST",
        url:"<?= SITE_IN ?>application/ajax/vehicles.php",
        dataType:'json',
        data:{
            action:'loadSearch',
            id:search_id
        },
        success:function (response) {
            // $("body").nimbleLoader("hide");
            if (response.success == true) {
                var val = [];
                if ((response.data['company'] != undefined)) {
                    if (response.data['company'] == "") {
                        $("#company_text").html("All Companies");
                    } else {
                        $("#companyResults").html("");
                        $.ajax({
                            type:"POST",
                            url:'<?= SITE_IN ?>application/ajax/vehicles.php',
                            dataType:'json',
                            data:{
                                action:'getCompanies',
                                ids:response.data['company']
                            },
                            success:function (response) {
                                if (response.success) {
                                    for (i in response.data) {
                                        $("#companyResults").append("<tr><td align='right'><input type='checkbox' checked='checked' value='" + response.data[i].id + "' id='company_" + response.data[i].id + "'/></td><td><label for='company_" + response.data[i].id + "'><strong>" + response.data[i].name + "</strong></label></td></tr>");
                                    }
                                    var companyIds = [];
                                    $("#companyResults input:checkbox:checked").each(function () {
                                        companyIds.push($(this).val());
                                    });
                                    if (companyIds.length == 0) {
                                        $("#company_text").html("All Companies");
                                    } else if (companyIds.length == 1) {
                                        $("#company_text").html($("#companyResults label[for='" + $("#companyResults input:checkbox:checked:first").attr("id") + "']").text());
                                    } else {
                                        $("#company_text").html(companyIds.length + " Companies");
                                    }
                                }
                            }
                        });
                    }
                }
                if (response.data['origin_city_search'] != undefined) {
                    $("#origin_city_search").attr("checked", true);
                    originCitySearch("checked");
                } else {
                    $("#origin_city_search").attr("checked", false);
                    originCitySearch(false);
                }
                if (response.data['destination_city_search'] != undefined) {
                    $("#destination_city_search").attr("checked", true);
                    destinationCitySearch("checked");
                } else {
                    $("#destination_city_search").attr("checked", false);
                    destinationCitySearch(false);
                }
                $("select[multiple]").val([]);
                for (i in response.data) {
                    if (typeof(response.data[i]) == "object") {
                        val = [];
                        for (j in response.data[i]) {
                            if (!in_array(response.data[i][j], val)) {
                                val.push(response.data[i][j]);
                            }
                        }
                        $("select[name='" + i + "[]']").val(val);
                    } else {
                        $("input:text[name='" + i + "']").val(response.data[i]);
                        $("input:checkbox[name='" + i + "']").attr("checked", true);
                        $("select[name='" + i + "']").val(response.data[i]);
                    }
                    $("#vehicle_types").select2("refresh");
                }
            } else {
                Swal.fire("Search load failed. Try again later, please");
            }
        },
        error:function (response) {
            // $("body").nimbleLoader("hide");
            Swal.fire("Search load failed. Try again later, please");
        }
    });
}

function selectCompany()
 {
    $("#searchCompany").modal();
 }


 function selectCompany_save()
  {
    var companySearchString = encodeURIComponent($.trim($("#searchCompany input:text").val()));
    if (companySearchString == "") return;
        $("#companyResults").html("");

      $("#searchCompany").find(".modal-body").addClass('kt-spinner kt-spinner--lg kt-spinner--dark');
        // $(".ui-dialog").nimbleLoader('show');
        $.ajax({
            type: "POST",
            url: '<?= SITE_IN ?>application/ajax/vehicles.php',
            dataType: 'json',
            data: {
                action: 'searchCompany',
                search: companySearchString
            },
            success: function (response) {
               $("#searchCompany").find(".modal-body").removeClass('kt-spinner kt-spinner--lg kt-spinner--dark');
                if (response.success) {
                    for (i in response.data) {
                        $("#companyResults").append("<tr><td align='right'><input type='checkbox' value='" + response.data[i].id + "' id='company_" + response.data[i].id + "'/></td><td><label for='company_" + response.data[i].id + "'><strong>" + response.data[i].name + "</strong></label></td></tr>");
                    }
                } else {
                    $("#searchComapny .error").html("<p>Search failed. Try again later, please</p>");
                    $("#searchComapny .error").slideDown(500).delay(2000).slideUp(500);
                }
            },
            error: function (response) {
                $(".ui-dialog").nimbleLoader('hide');
                $("#searchComapny .error").html("<p>Search failed. Try again later, please</p>");
                $("#searchComapny .error").slideDown(500).delay(2000).slideUp(500);
            }
        });
     }


    function selectCompany_ok()
    {

        var companyIds = [];
        $("#companyResults input:checkbox:checked").each(function () {
            companyIds.push($(this).val());
        });
        if (companyIds.length == 0) {
            $("#company_text").html("All Companies");
        } else if (companyIds.length == 1) {
            $("#company_text").html($("#companyResults label[for='" + $("#companyResults input:checkbox:checked:first").attr("id") + "']").text());
        } else {
            $("#company_text").html(companyIds.length + " Companies");
        }
          $("#company").val(companyIds.join(","));
          $("#searchCompany").modal('hide');
        
    }
</script>

	<div id="search_forms">

		<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5" id="basic_search_mainbox">
		
			<div id="basic_search_board" class="hide_show">
				<h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Basic Search</h3>
			</div>
			
			<div id="basic_search_information" class="pt-3 pb-3" style="padding-left:20px;padding-right:20px;">
			
				<div id="basic_search">
					<form method="post" action="<?= getLink('vehicles', 'search') ?>" id="basic_search_form">
						<input type="hidden" name="search_type" value="basic"/>
						
						<div class="row">
							<div class="col-6">
								<div class="new_form-group">
								
									<label>Origin:</label>
									<select multiple="multiple" size="5" class="form-box-combobox" style="height:150px;margin-left:0;" id="b_origin_region" name="b_origin_region[]">
										<option value="" selected="selected">All</option>
										<optgroup label="Regions">
											<?php foreach ($this->regions as $code => $region) : ?>
											<option value="<?= $code ?>"><?= $region ?></option>
											<?php endforeach; ?>
										</optgroup>
										<optgroup label="States">
											<?php foreach ($this->states as $code => $state) : ?>
											<option value="<?= $code ?>"><?= $state ?></option>
											<?php endforeach; ?>
										</optgroup>
										<optgroup label="Canada">
											<?php foreach ($this->canadaStates as $code => $state) : ?>
											<option value="<?= $code ?>"><?= $state ?></option>
											<?php endforeach; ?>
										</optgroup>
									</select>
									<table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-top:-19px;width:350px;display:none;" id="b_origin_city_search_table">
										<tr>
											<td>City:</td>
											<td colspan="4"><input type="text" class="form-box-textfield geo-city" name="b_origin_city" id="b_origin_city" style="width:275px;"/></td>
										</tr>
										<tr style="height:20px;">&nbsp;</tr>
										<tr>
											<td>State:</td>
											<td style="width:180px;">
												<select id="b_origin_state" name="b_origin_state" class="form-box-combobox" style="width:160px;">
													<option value="">Select One</option>
													<optgroup label="States">
														<?php foreach ($this->states as $code => $state) : ?>
														<option value="<?= $code ?>"><?= $state ?></option>
														<?php endforeach; ?>
													</optgroup>
													<optgroup label="Canada">
														<?php foreach ($this->canadaStates as $code => $state) : ?>
														<option value="<?= $code ?>"><?= $state ?></option>
														<?php endforeach; ?>
													</optgroup>
												</select>
											</td>
											<td>Zip:</td>
											<td>
												<input type="text" class="form-box-textfield zip" name="b_origin_zip" id="b_origin_zip" style="width:75px;"/>
											</td>
											<td><span class="like-link" onclick="clearBOriginCity();">clear</span></td>
										</tr>
									</table>
									
								</div>
								
								<div class="new_form-group pull-left" style="width:160px;"><label></label></div>
								
								<input type="checkbox" class="form-box-checkbox" name="b_origin_city_search" id="b_origin_city_search"/>
								<label for="b_origin_city_search">City Search</label>
									
							</div>
							
							<div class="col-6">
								<div class="new_form-group">
								
									<label>Destination:</label>
									
									<select multiple="multiple" size="5" class="form-box-combobox" style="height:150px;margin-left:0;" name="b_destination_region[]" id="b_destination_region">
										<option value="" selected="selected">All</option>
										<optgroup label="Regions">
											<?php foreach ($this->regions as $code => $region) : ?>
											<option value="<?= $code ?>"><?= $region ?></option>
											<?php endforeach; ?>
										</optgroup>
										<optgroup label="States">
											<?php foreach ($this->states as $code => $state) : ?>
											<option value="<?= $code ?>"><?= $state ?></option>
											<?php endforeach; ?>
										</optgroup>
										<optgroup label="Canada">
											<?php foreach ($this->canadaStates as $code => $state) : ?>
											<option value="<?= $code ?>"><?= $state ?></option>
											<?php endforeach; ?>
										</optgroup>
									</select>
									
									<table cellpadding="0" cellspacing="0" border="0" class="null-padding" style="margin-top:-19px;width:350px;display:none;" id="b_destination_city_search_table">
										<tr>
											<td>City:</td>
											<td colspan="4">
												<input type="text" class="form-box-textfield geo-city" name="b_destination_city" id="b_destination_city" style="width:275px;"/>
											</td>
										</tr>
										<tr style="height:20px;">&nbsp;</tr>
										<tr>
											<td>State:</td>
											<td>
												<select id="b_destination_state" name="b_destination_state" class="form-box-combobox" style="width:160px;">
													<option value="">Select One</option>
													<optgroup label="States">
														<?php foreach ($this->states as $code => $state) : ?>
														<option value="<?= $code ?>"><?= $state ?></option>
														<?php endforeach; ?>
													</optgroup>
													<optgroup label="Canada">
														<?php foreach ($this->canadaStates as $code => $state) : ?>
														<option value="<?= $code ?>"><?= $state ?></option>
														<?php endforeach; ?>
													</optgroup>
												</select>
											</td>
											<td>Zip:</td>
											<td>
												<input type="text" class="form-box-textfield zip" name="b_destination_zip" id="b_destination_zip" style="width:75px;"/>
											</td>
											<td><span class="like-link" onclick="clearBDestinationCity();">clear</span></td>
										</tr>
									</table>
									
								</div>
								
								<div class="new_form-group pull-left" style="width:160px;"><label></label></div>
								
								<input type="checkbox" class="form-box-checkbox" name="b_destination_city_search" id="b_destination_city_search"/>
								<label for="b_destination_city_search">City Search</label>
									
							</div>
						</div>
						
						<div class="text-right">
							<?= functionButton('Search', 'basicSubmit()') ?>
						</div>
						
					</form>
				</div>
			</div>
		</div>
		
		
		<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5"  id="advanced_search_mainbox">
		
			<div id="advanced_search_board" class="hide_show">
				<h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Advanced Search</h3>
			</div>
			
			<div id="advanced_search_information" class="pt-3 pb-3" style="padding-left:20px;padding-right:20px;display:none;">
				
				<div id="advanced_search">

					<!-- <div id="promptSearchName" style="display: none;">
						<div class="error" style="display:none;"></div>
						<br/>
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
							<tr>
								<td>Search Name:</td>
								<td><input type="text" class="form-box-textfield"/></td>
							</tr>
						</table>
					</div> -->


<!--  -->

<!--begin::Modal-->
        <div class="modal fade" id="promptSearchName" tabindex="-1" role="dialog" aria-labelledby="promptSearchName_modal" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="promptSearchName_modal">Search Name</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                             <i class="fa fa-times" aria-hidden="true"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="error" style="display:none;"></div>
                        <table cellpadding="0" cellspacing="0" border="0" width="100%">
                            <tr>
                                <td>Search Name:</td>
                                <td><input type="text" class="form-box-textfield"/></td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancal</button>
                        <button type="button" class="btn btn-primary btn-sm" onclick="promptSearchName_save()">Save</button>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Modal-->

<!--  -->


  <div class="modal fade" id="searchCompany" tabindex="-1" role="dialog" aria-labelledby="searchCompany_modal" aria-hidden="true">
                <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="searchCompany_modal">Select Company</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                             <i class="fa fa-times" aria-hidden="true"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="error" style="display: none;"></div>
                       <table cellpadding="0" cellspacing="0" border="0" width="100%" class="form-table">
                        <thead>
                            <tr>
                                <td>Company Name:</td>
                                <td><input type="text" class="form-box-textfield"/></td>
                            </tr>
                        </thead>
                        <tbody id="companyResults"></tbody>
                    </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancal</button>
                        <button type="button" class="btn btn-primary btn-sm" onclick="selectCompany_ok()" >Ok</button>
                        <button type="button" class="btn btn-primary btn-sm" onclick="selectCompany_save()" >Save</button>
                    </div>
                </div>
                </div>
                </div>

					
					<form method="post" action="<?= getLink('vehicles', 'search') ?>" id="search_form">
						<input type="hidden" name="search_type" value="advanced"/>
					
						<div class="row">
							<div class="col-6">
								<div class="new_form-group">
								
									<label>Origin:</label>
									
									<select multiple="multiple" size="5" class="form-box-combobox" style="margin-left:0;height:150px;" id="origin_region" name="origin_region[]">
										<option value="" selected="selected">All</option>
										<optgroup label="Regions">
											<?php foreach ($this->regions as $code => $region) : ?>
											<option value="<?= $code ?>"><?= $region ?></option>
											<?php endforeach; ?>
										</optgroup>
										<optgroup label="States">
											<?php foreach ($this->states as $code => $state) : ?>
											<option value="<?= $code ?>"><?= $state ?></option>
											<?php endforeach; ?>
										</optgroup>
										<optgroup label="Canada">
											<?php foreach ($this->canadaStates as $code => $state) : ?>
											<option value="<?= $code ?>"><?= $state ?></option>
											<?php endforeach; ?>
										</optgroup>
									</select>
									<table cellpadding="0" cellspacing="0" border="0" width="100%" class="null-padding" style="margin-top:-19px;width:350px;display:none;" id="origin_city_search_table">
										<tr>
											<td>City:</td>
											<td colspan="4"><input type="text" class="form-box-textfield geo-city" name="origin_city" id="origin_city" style="width:275px;"/></td>
										</tr>
										<tr style="height:20px;">&nbsp;</tr>
										<tr>
											<td>State:</td>
											<td>
												<select id="origin_state" name="origin_state" class="form-box-combobox" style="width:160px;">
													<option value="">Select One</option>
													<optgroup label="States">
														<?php foreach ($this->states as $code => $state) : ?>
														<option value="<?= $code ?>"><?= $state ?></option>
														<?php endforeach; ?>
													</optgroup>
													<optgroup label="Canada">
														<?php foreach ($this->canadaStates as $code => $state) : ?>
														<option value="<?= $code ?>"><?= $state ?></option>
														<?php endforeach; ?>
													</optgroup>
												</select>
											</td>
											<td>Zip:</td>
											<td><input type="text" class="form-box-textfield zip" name="origin_zip" id="origin_zip"
													   style="width:75px;"/></td>
											<td><span class="like-link" onclick="clearOriginCity();">clear</span></td>
										</tr>
									</table>
									
								</div>
								
								
								<div class="new_form-group pull-left" style="width:160px;"><label></label></div>
								
								<input type="checkbox" class="form-box-checkbox" name="origin_city_search" id="origin_city_search"/>
								<label for="origin_city_search">City Search</label>
								
								<div class="new_form-group mt-3">
									<label>Min. Pay</label>
									<input type="text" class="form-box-textfield decimal" name="min_pay" style="width:120px;margin-left:0;"/>
									<select class="form-box-combobox" name="min_pay_type" style="width: 150px;">
										<option value="M">per Vehicle/Mile</option>
										<option value="L">Listing Total Pay</option>
									</select>
								</div>
								
								<div class="new_form-group" >
									<label>Vehicle Type</label>
									<select name="vehicle_types[]" id="vehicle_types" multiple="multiple" class="form-box-combobox">
										<?php foreach ($this->vehicleTypes as $type) : ?>
										<option value="<?= $type ?>"><?= $type ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								
								<div class="new_form-group">
									<label>Vehicle Running</label>
									<select name="vehicle_condition" style="margin-left:0;" id="vehicle_condition" class="form-box-combobox">
										<?php foreach (Entity::$vehicles_run_string as $key => $value) : ?>
										<?php if ($key == "") continue; ?>
										<option value="<?= $key ?>"><?= $value ?></option>
										<?php endforeach; ?>
										<option value="" selected="selected">All</option>
									</select>
								</div>
								
								
								<div class="new_form-group">
									<label>Ready to Ship Within:</label>							
									<select name="timeframe" id="timeframe" class="form-box-combobox" style="margin-left:0;">
										<option value="0">0 Days (Today)</option>
										<option value="1">1 Day</option>
										<option value="2">2 Days</option>
										<option value="3">3 Days</option>
										<option value="4">4 Days</option>
										<option value="5">5 Days</option>
										<option value="6">6 Days</option>
										<option value="7">7 Days</option>
										<option value="10">10 Days</option>
										<option value="14">14 Days</option>
										<option value="30">30 Days</option>
										<option value="60" selected="selected">60 Days</option>
									</select>
								</div>


								<div class="new_form-group">
									<label>Sorty By:</label>
									<select name="sort1" id="sort1" class="form-box-combobox" style="margin-left:0;">
										<option value="origination">Origination</option>
										<option value="origination_area" selected="selected">Origination Metro Area</option>
										<option value="destination">Destination</option>
										<option value="destination_area">Destination Metro Area</option>
										<option value="ship_date">Ship Date</option>
										<option value="company_name">Company Name</option>
										<option value="fd_id">Freight Dragon ID</option>
										<option value="post_date">Post Date</option>
										<option value="price">Price</option>
										<option value="price_per_mile">Price Per Mile</option>
									</select>
								</div>
								
								
								<div class="new_form-group">
									<label>Posted By:</label>							
									<span id="company_text" style="font-weight: bold;">All Companies</span>
									<input type="hidden" name="company" id="company" value=""/>&nbsp;
									<span class="like-link" onclick="selectCompany()">Select a Posting Company</span>
								</div>


								<div class="new_form-group">
									<label>Highlight:</label>
									
									<div class="pull-left" style="width:calc(100% - 170px);margin-bottom:10px;">
										<input style="margin-top:10px;" type="checkbox" name="hl_0" id="hl_0"/>
										<label style="width:110px;" for="hl_0">No Highlighting</label>
									</div>
									
									<div class="pull-left" style="margin-right:20px;">
										<div class="highlight-tip highlight-green pull-left" style="margin-right:10px;">&nbsp;</div>
										1 day old
									</div>
									
									<div class="pull-left" style="margin-right:20px;">
										<div class="highlight-tip highlight-blue pull-left" style="margin-right:10px;">&nbsp;</div>
										2-3 days old
									</div>
									
									<div class="pull-left" style="margin-right:20px;">
										<div class="highlight-tip highlight-brown pull-left" style="margin-right:10px;">&nbsp;</div>
										4-6 days old
									</div>	
									
									<div class="pull-left" style="margin-right:20px;">
										<div class="highlight-tip highlight-red pull-left" style="margin-right:10px;">&nbsp;</div>
										1 week and older
									</div>
								</div>
								
							</div>
							
							<div class="col-6">
								<div class="new_form-group">
								
									<label>Destination:</label>								
									
									<select multiple="multiple" size="5" class="form-box-combobox" style="margin-left:0;height:150px;" name="destination_region[]" id="destination_region">
										<option value="" selected="selected">All</option>
										<optgroup label="Regions">
											<?php foreach ($this->regions as $code => $region) : ?>
											<option value="<?= $code ?>"><?= $region ?></option>
											<?php endforeach; ?>
										</optgroup>
										<optgroup label="States">
											<?php foreach ($this->states as $code => $state) : ?>
											<option value="<?= $code ?>"><?= $state ?></option>
											<?php endforeach; ?>
										</optgroup>
										<optgroup label="Canada">
											<?php foreach ($this->canadaStates as $code => $state) : ?>
											<option value="<?= $code ?>"><?= $state ?></option>
											<?php endforeach; ?>
										</optgroup>
									</select>
									<table cellpadding="0" cellspacing="0" border="0" class="null-padding" style="margin-top:-19px;display:none;" id="destination_city_search_table">
										<tr>
											<td>City:</td>
											<td colspan="4"><input type="text" class="form-box-textfield geo-city" name="destination_city" id="destination_city" style="width:275px;"/></td>
										</tr>
										<tr style="height:20px;">&nbsp;</tr>
										<tr>
											<td>State:</td>
											<td>
												<select id="destination_state" name="destination_state" class="form-box-combobox" style="width:160px;">
													<option value="">Select One</option>
													<optgroup label="States">
														<?php foreach ($this->states as $code => $state) : ?>
														<option value="<?= $code ?>"><?= $state ?></option>
														<?php endforeach; ?>
													</optgroup>
													<optgroup label="Canada">
														<?php foreach ($this->canadaStates as $code => $state) : ?>
														<option value="<?= $code ?>"><?= $state ?></option>
														<?php endforeach; ?>
													</optgroup>
												</select>
											</td>
											<td>Zip:</td>
											<td><input type="text" class="form-box-textfield zip" name="destination_zip" id="destination_zip" style="width:75px;margin-left:0;"/></td>
											<td><span class="like-link" onclick="clearDestinationCity();">clear</span></td>
										</tr>
									</table>

								</div>
								
								<div class="new_form-group pull-left" style="width:160px;"><label></label></div>
								
								<input type="checkbox" class="form-box-checkbox" name="destination_city_search" id="destination_city_search"/>
								<label for="destination_city_search">City Search</label>
								
								<div class="new_form-group mt-3">
									<label>Min # of Vehicles</label>
									<select class="form-box-combobox" name="min_num" style="width: 50px;">
										<option value="1">1</option>
										<option value="2">2</option>
										<option value="3">3</option>
										<option value="4">4</option>
										<option value="5">5</option>
										<option value="6">6</option>
										<option value="7">7</option>
										<option value="8">8</option>
										<option value="9">9</option>
										<option value="10">10</option>
									</select>
									<span style="font-weight:600;margin-left:15px;margin-top:7px;display: inline-block;">per listing<span>
								</div>
								
								<div class="new_form-group">
									<label>Trailer Type:</label>
									<select name="trailer_type" id="trailer_type" class="form-box-combobox">
										<?php foreach (Entity::$ship_via_string as $key => $value) : ?>
										<?php if ($key == "") continue; ?>
										<option value="<?= $key ?>"><?= $value ?></option>
										<?php endforeach; ?>
										<option value="" selected="selected">All</option>
									</select>
								</div>
								
								
								<div class="new_form-group">
									<label>Payment Type:</label>
									<select name="payment_type" id="payment_type" class="form-box-combobox">
										<option value="all" selected="selected">All</option>
										<option value="cc">COD/COP</option>
										<option value="qc">Prepayment/Invoice</option>
									</select>
								</div>
								
								
								<div class="new_form-group">
									<label></label>
								</div>
								<div class="new_form-group">
									<label></label>
								</div>

								<div class="new_form-group">
									<label>then</label>
									<select name="sort2" id="sort2" class="form-box-combobox">
										<option value="origination">Origination</option>
										<option value="origination_area">Origination Metro Area</option>
										<option value="destination">Destination</option>
										<option value="destination_area" selected="selected">Destination Metro Area</option>
										<option value="ship_date">Ship Date</option>
										<option value="company_name">Company Name</option>
										<option value="fd_id">Freight Dragon ID</option>
										<option value="post_date">Post Date</option>
										<option value="price">Price</option>
										<option value="price_per_mile">Price Per Mile</option>
									</select>
								</div>

								<div class="new_form-group">
									<label>Show:</label>
									<select name="show" id="show" class="form-box-combobox" style="width:70px;">
										<option value="50">50</option>
										<option value="100" selected="selected">100</option>
										<option value="200">200</option>
										<option value="300">300</option>
										<option value="500">500</option>
									</select>
									<span style="font-weight:600;margin-left:15px;margin-top:7px;display: inline-block;">listings at a time</span>
								</div>
								
							</div>
						</div>
						
						<div class="text-right">
							<?= functionButton('Save this Search', 'saveSearch()') ?>
							<?= functionButton('Search', 'advancedSubmit()') ?>
						</div>
						
					</form>
					
					<br/>
					
					<h3 class="shipper_detail" style="padding-left:15px;border:1px solid #ebedf2;border-bottom:0;margin-bottom:0px;">Saved Searches</h3>
					
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Name</th>
								<th>Origination</th>
								<th>Destination</th>
								<th colspan="2">Actions</th>
							</tr>
						</thead>
						<?php if (count($this->searches) == 0) : ?>
						<tr class="grid-body">
							<td class="grid-body-left grid-body-right" colspan="5" align="center"><i>You have no saved searches.</i></td>
						</tr>
						<?php endif; ?>
						<?php foreach ($this->searches as $i => $search) : ?>
						<tr class="grid-body<?=($i == 0 ? " first-row" : "")?>" id="row-<?=$search->id?>">
							<td class="grid-body-left"><?= $search->name ?></td>
							<td>
								<?php if (!is_null($search->get('origin_city_search'))) : ?>
								<?= $search->get('origin_city') ?>, <?= $search->get('origin_state') ?>, <?= $search->get('origin_zip') ?>
								<?php else : $regions = "";
								foreach ($search->get('origin_region') as $region) {
									$regions[] = strtoupper($region);
								} ?>
								<?= implode(", ", $regions) ?>
								<?php endif; ?>
							</td>
							<td>
								<?php if (!is_null($search->get('destination_city_search'))) : ?>
								<?= $search->get('destination_city') ?>, <?= $search->get('destination_state') ?>
								, <?= $search->get('destination_zip') ?>
								<?php else : $regions = "";
								foreach ($search->get('destination_region') as $region) {
									$regions[] = strtoupper($region);
								} ?>
								<?= implode(", ", $regions) ?>
								<?php endif; ?>
							</td>
							<td style="width: 16px;">
								<img class="action-icon" title="Load" src="<?= SITE_IN ?>images/icons/download.png" width="16" height="16" onclick="loadSearchForm(<?= $search->id ?>)"/>
							</td>
							<td style="width: 16px;"
								class="grid-body-right"><?=deleteIcon(getLink("vehicles", "search", "delete", $search->id), "row-" . $search->id)?></td>
						</tr>
						<?php endforeach; ?>
					</table>
					<?=formBoxEnd()?>
					</div>
				
				
				
			</div>
			
		</div>


	
	</div>
<br/>
<?php if (isset($this->results)) : ?>
	<?= formBoxStart("Search Results") ?>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="grid">
    <tr class="grid-head">
        <td class="grid-head-left">Origin</td>
        <td>Destination</td>
        <td>Vehicle</td>
        <td>Contact</td>
        <td class="grid-head-right"><span style="white-space: nowrap;">Ship On</span>/Posted</td>
    </tr>
	<?php if (count($this->results) == 0) : ?>
    <tr class="grid-body first-row">
        <td class="grid-body-right grid-body-left" colspan="5" align="center"><i>Your search have no results.</i></td>
    </tr>
	<?php endif; ?>
	<?php foreach ($this->results as $i => $result) : ?>
	<?php
	$highlight = "";
	if (!isset($_POST['hl_0'])) {
		$posted = round((time() - strtotime($result[0]['posted'])) / 86400);
		if ($posted <= 1) {
			$highlight = " highlight-green";
		} elseif ($posted <= 3) {
			$highlight = " highlight-blue";
		} elseif ($posted <= 6) {
			$highlight = " highlight-brown";
		} else {
			$highlight = " highlight-red";
		}
	}
	?>
    <tr class="grid-body<?= ($i == 0) ? " first-row" : "" ?><?=$highlight?>">
        <td class="grid-body-left"><?= $result[0]['origin_city'] ?>, <?= $result[0]['origin_state'] ?>
            , <?= $result[0]['origin_country'] ?></td>
        <td><?= $result[0]['destination_city'] ?>, <?= $result[0]['destination_state'] ?>
            , <?= $result[0]['destination_country'] ?></td>
        <td>
			<?php foreach ($result as $vehicle) : ?>
            <div><?= $vehicle['vehicle_year'] ?> <?= $vehicle['vehicle_make'] ?> <?= $vehicle['vehicle_model'] ?>
                &nbsp;<?=imageLink($vehicle['vehicle_year'] . " " . $vehicle['vehicle_make'] . " " . $vehicle['vehicle_model'] . " " . $vehicle['vehicle_type'])?></div>
			<?php endforeach; ?>
        </td>
        <td>
            <a href="<?=getLink("ratings/company/id/" . $result[0]['company_id'])?>"><?= $result[0]['company_name'] ?></a><br/>
			<?= $result[0]['company_phone'] ?><br/>
			<?= $result[0]['company_fax'] ?><br/>
            Rating: <?= ($result[0]['company_score'] != 0) ? number_format($result[0]['company_score'], 1, '.', '') . "%" : 'N/A' ?>
            &nbsp;
            Recv'd: <?= $result[0]['ratings'] ?><br/>
            Member: <?= date('m/y', strtotime($result[0]['company_registered'])) ?>
        </td>
        <td class="grid-body-right">
			<?= date('m/d/Y', strtotime($result[0]['ship_on'])) ?><br/>
			<?= date('m/d/Y', strtotime($result[0]['posted'])) ?>
        </td>
    </tr>
	<?php endforeach; ?>
</table>
	<?= formBoxEnd() ?>
<?php endif; ?>