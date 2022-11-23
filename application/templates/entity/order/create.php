<style>
    .order-select-shipper
	{
        background-color: #f2f2f2;
        border: 1px solid #cccccc;
        border-radius: 5px;
        float: left;
        line-height: 20px;
        padding: 10px;
        width:40%;
    }
	h3.details
	{
		padding:22px 0 0;
		width:100%;
		font-size:20px;
	}
</style>

<script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script>
<script type="text/javascript">

    var tempDisableBeforeUnload = false;
    $(window).bind('beforeunload', function () {

        if (($("#shipper_fname").val() != '' || $("#shipper_lname").val() != '' || $("#shipper_company").val() != '' || $("#shipper_email").val() != '') && !tempDisableBeforeUnload) {
            return 'Leaving this page will lose your changes. Are you sure?';
        } else {
            tempDisableBeforeUnload = false;
            return;
        }
    });

    function countChar(val) {
        var len = val.value.length;
        if (len >= 60) {
            val.value = val.value.substring(0, 60);
        } else {
            $('#charNum').text(' ' + (60 - len) + ' )');
        }
    }

    function formatPhoneNumber(s) {
        var s2 = ("" + s).replace(/\D/g, '');
        var m = s2.match(/^(\d{3})(\d{3})(\d{4})$/);
        return (!m) ? null : "" + m[1] + "-" + m[2] + "-" + m[3];
    }

    function applySearchLeads(num) {
        var acc_obj = acc_data.shipper_leads_data[num];
        switch (acc_type) {
            case <?=Account::TYPE_SHIPPER?>:

                $('#select-shipper-block').hide();
                $('#shipperDiv').show();
                $('#update_shipper_info').show();
                $('#save_shipper_info').hide();
                $('#save_shipper').removeAttr('checked');
                $("#shipper_fname").val(acc_obj.fName);
                $("#shipper_lname").val(acc_obj.lName);
                $("#shipper_company").val(acc_obj.company);
                $("#shipper_email").val(acc_obj.email);
                $("#shipper_phone1").val(formatPhoneNumber(acc_obj.phone1));
                $("#shipper_phone2").val(formatPhoneNumber(acc_obj.phone2));
                $("#shipper_mobile").val(formatPhoneNumber(acc_obj.cell));
                $("#shipper_fax").val(formatPhoneNumber(acc_obj.fax));
                $("#shipper_address1").val(acc_obj.address1);
                $("#shipper_address2").val(acc_obj.address2);
                $("#shipper_city").val(acc_obj.city);
                $("#shipper_country").val(acc_obj.country);
                if (acc_obj.country == "US") {
                    $("#shipper_state").val(acc_obj.state);
                } else {
                    $("#shipper_state2").val(acc_obj.state);
                }
                $("#shipper_zip").val(acc_obj.zip_code);
                $("#shipper_type").val(acc_obj.shipper_type);
                $("#shipper_hours").val(acc_obj.hours_of_operation);
                if (acc_obj.referred_by != '') {
                    $("#referred_by").empty(); // remove old options
                }
                $("#account_payble_contact").val(acc_obj.account_payble_contact);
                typeselected();
            break;
            case <?=Account::TYPE_TERMINAL?>:
                $("#" + acc_location + "_address1").val(acc_obj.address1);
                $("#" + acc_location + "_address2").val(acc_obj.address2);
                $("#" + acc_location + "_city").val(acc_obj.city);
                $("#" + acc_location + "_country").val(acc_obj.coutry);
                if (acc_obj.country == "US") {
                    $("#" + acc_location + "_state").val(acc_obj.state);
                } else {
                    $("#" + acc_location + "_state2").val(acc_obj.state);
                }
                $("#" + acc_location + "_zip").val(acc_obj.zip);
                $("#" + acc_location + "_contact_name").val(acc_obj.contact_name1);
                $("#" + acc_location + "_company_name").val(acc_obj.company_name);
                $("#" + acc_location + "_phone1").val(formatPhoneNumber(acc_obj.phone1));
                $("#" + acc_location + "_phone2").val(formatPhoneNumber(acc_obj.phone2));
                $("#" + acc_location + "_mobile").val(acc_obj.cell);
                $("#" + acc_location + "_type").val(acc_obj.location_type);
                $("#" + acc_location + "_hours").val(acc_obj.hours_of_operation);
                $("#" + acc_location + "_id").val(acc_obj.id);
            break;
        }
    }

    function applySearch(num) {
        if (acc_type == 3) {
            var acc_obj = acc_data[num];
        } else {
            var acc_obj = acc_data.shipper_data[num];
        }
        switch (acc_type) {
            case <?=Account::TYPE_SHIPPER?>:
                $('#select-shipper-block').hide();
                $('#shipperDiv').show();
                $('#update_shipper_info').show();
                $('#save_shipper_info').hide();
                $('#update_shipper').attr('checked', 'checked');
                $('#save_shipper').removeAttr('checked');
                $("#shipper_fname").val(acc_obj.first_name);
                $("#shipper_lname").val(acc_obj.last_name);
                $("#shipper_company").val(acc_obj.company_name);
                $("#shipper_email").val(acc_obj.email);
                $("#shipper_phone1").val(formatPhoneNumber(acc_obj.phone1));
                $("#shipper_phone2").val(formatPhoneNumber(acc_obj.phone2));
                $("#shipper_mobile").val(formatPhoneNumber(acc_obj.cell));
                $("#shipper_fax").val(formatPhoneNumber(acc_obj.fax));
                $("#shipper_address1").val(acc_obj.address1);
                $("#shipper_address2").val(acc_obj.address2);
                $("#shipper_city").val(acc_obj.city);
                $("#shipper_country").val(acc_obj.coutry);
                if (acc_obj.country == "US") {
                    $("#shipper_state").val(acc_obj.state);
                } else {
                    $("#shipper_state2").val(acc_obj.state);
                }
                $("#shipper_zip").val(acc_obj.zip_code);
                $("#shipper_type").val(acc_obj.shipper_type);
                $("#shipper_hours").val(acc_obj.hours_of_operation);
                if (acc_obj.referred_by != '') {
                    $("#referred_by").empty(); // remove old options
                    $("#referred_by").append($("<option></option>").attr("value", acc_obj.referred_id).text(acc_obj.referred_by));
                }
                $("#account_payble_contact").val(acc_obj.account_payble_contact);
                typeselected();
            break;
            case <?=Account::TYPE_TERMINAL?>:
                $("#" + acc_location + "_address1").val(acc_obj.address1);
                $("#" + acc_location + "_address2").val(acc_obj.address2);
                $("#" + acc_location + "_city").val(acc_obj.city);
                $("#" + acc_location + "_country").val(acc_obj.coutry);
                if (acc_obj.country == "US") {
                    $("#" + acc_location + "_state").val(acc_obj.state);
                } else {
                    $("#" + acc_location + "_state2").val(acc_obj.state);
                }
                $("#" + acc_location + "_zip").val(acc_obj.zip);
                $("#" + acc_location + "_contact_name").val(acc_obj.contact_name1);
                $("#" + acc_location + "_company_name").val(acc_obj.company_name);
                $("#" + acc_location + "_phone1").val(acc_obj.phone1);
                $("#" + acc_location + "_phone2").val(acc_obj.phone2);
                $("#" + acc_location + "_mobile").val(acc_obj.cell);
                $("#" + acc_location + "_type").val(acc_obj.location_type);
                $("#" + acc_location + "_hours").val(acc_obj.hours_of_operation);
                $("#" + acc_location + "_id").val(acc_obj.id);
            break;
        }
    }

    function createAndEmail() {
        $("#co_send_email").val("1");
        $("#submit_button").click();
    }

    function newShipper() {
        $('#update_shipper_info').hide();
        $('#save_shipper_info').show();
        $('#select-shipper-block').hide();
        $('#shipperDiv').show();
        $('#save_shipper').attr('checked', 'checked');
        $('#update_shipper').removeAttr('checked');
    }

    function typeselected() {
        if ($("#shipper_type").val() == "Commercial") {
            $('#shipper_company-span').show();
            $('#account_payble_contact_label_div').show();
            $('#account_payble_contact_div').show();
        } else {
            $('#shipper_company-span').hide();
            $('#account_payble_contact_label_div').hide();
            $('#account_payble_contact_div').hide();
        }

    }

    function paid_by_ach_selected() {
        if ($("#balance_paid_by").val() == 24) {
            $('#fee_type_label_div').show();
            $('#fee_type_div').show();
        } else {
            $('#fee_type_label_div').hide();
            $('#fee_type_div').hide();
        }
    }

    function origintypeselected() {
        if ($("#origin_type").val() == "Commercial") {
            $('#origin_company-span').show();
            $('#origin_hour').show();
        } else {
            $('#origin_company-span').hide();
            $('#origin_hour').hide();
        }

    }

    $(document).ready(function () {
        typeselected();
        origintypeselected();
        paid_by_ach_selected();

        $("#delivery_terminal_fee, #pickup_terminal_fee").change(function () {
            updatePricingInfo();
        });

        <?php if (empty($_POST)) {?>
            $('#shipperDiv').hide();
        <?php }?>

        $('#customer_balance_paid_by-block').hide();
        var createForm = $('#create_form');
             createForm.find("input.shipper_company-model").typeahead({
            source: function (request, response) {
            },
            minLength: 0,
            autoFocus: true,
            select: function (event, ui) {
                $("#shipper_company").val(ui.item.company_name);
                $("#shipper_company_id").val(ui.item.value);

                if (Object.keys(ui.item).length > 0) {

                    if (ui.item.first_name != 'N/A' && ui.item.first_name != '' && ui.item.first_name != null){
                        $("#shipper_fname").val(ui.item.first_name);
                    }
                    
                    if (ui.item.last_name != 'N/A' && ui.item.last_name != '' && ui.item.last_name != null) {
                        $("#shipper_lname").val(ui.item.last_name);
                    }

                    $("#shipper_email").val(ui.item.email);
                    $("#shipper_phone1").val(ui.item.phone1);
                    $("#shipper_phone2").val(ui.item.phone2);
                    $("#shipper_mobile").val(ui.item.cell);
                    $("#shipper_fax").val(ui.item.fax);
                    $("#shipper_address1").val(ui.item.address1);
                    $("#shipper_address2").val(ui.item.address2);
                    $("#shipper_city").val(ui.item.city);
                    $("#shipper_country").val(ui.item.country);
                    $("#shipper_state").val(ui.item.state);
                    $("#shipper_zip").val(ui.item.zip_code);
                    $("#shipper_type").val(ui.item.shipper_type);

                    if (ui.item.referred_id != 0 && ui.item.referred_by != '' && ui.item.referred_id != null) {

                        $("#referred_by").empty(); // remove old options
                        $("#referred_by").append($("<option></option>").attr("value", ui.item.referred_id).text(ui.item.referred_by));

                    } else {

                        $("#referred_by").empty(); // remove old options
                        $("#referred_by").append($("<option></option>").attr("value", '').text('Select One'));
                        <?php
                            foreach ($this->referrers_arr as $key => $referrer) {
                        ?>
                            $("#referred_by").append($("<option></option>").attr("value", '<?php print $key;?>').text('<?php print $referrer;?>'));
                        <?php
                            }
                        ?>
                    }
                } else {

                    $("#referred_by").empty(); // remove old options
                    $("#referred_by").append($("<option></option>").attr("value", '').text('Select One'));
                    <?php
                        foreach ($this->referrers_arr as $key => $referrer) {
                    ?>
                    $("#referred_by").append($("<option></option>").attr("value", '<?php print $key;?>').text('<?php print $referrer;?>'));
                    <?php
                        }
                    ?>
                }
                return false;
            },
            change: function () {
            }
        });
    });

    function setLocationSameAsShipperOrder(location) {

        if (confirm("Are you sure you want to overwrite location information?")) {

            if (location == 'e_cc') {
                $("input[name='" + location + "_fname']").val($("input[name='shipper_fname']").val());
                $("input[name='" + location + "_lname']").val($("input[name='shipper_lname']").val());
                $("input[name='" + location + "_address']").val($("input[name='shipper_address1']").val());
            } else {
                $("input[name='" + location + "_company_name']").val($("input[name='shipper_company']").val());
                $("input[name='" + location + "_auction_name']").val($("input[name='shipper_company']").val());
                $("input[name='" + location + "_address1']").val($("input[name='shipper_address1']").val());
                $("input[name='" + location + "_contact_name']").val($("input[name='shipper_fname']").val() + ' ' + $("input[name='shipper_lname']").val());
            }

            $("input[name='" + location + "_city']").val($("input[name='shipper_city']").val());
            $("select[name='" + location + "_state']").val($("select[name='shipper_state']").val());
            $("input[name='" + location + "_zip']").val($("input[name='shipper_zip']").val());
            $("select[name='" + location + "_country']").val($("select[name='shipper_country']").val());
            $("input[name='" + location + "_phone1']").val($("input[name='shipper_phone1']").val());
            $("input[name='" + location + "_phone1_ext']").val($("input[name='shipper_phone1_ext']").val());
            $("input[name='" + location + "_phone2']").val($("input[name='shipper_phone2']").val());
            $("input[name='" + location + "_phone2_ext']").val($("input[name='shipper_phone2_ext']").val());
            $("input[name='" + location + "_mobile']").val($("input[name='shipper_mobile']").val());
            $("input[name='" + location + "_fax']").val($("input[name='shipper_fax']").val());
            $("input[name='" + location + "_address2']").val($("input[name='shipper_address2']").val());
            $("select[name='" + location + "_type']").val($("select[name='shipper_type']").val());
            $("input[name='" + location + "_hours']").val($("input[name='shipper_hours']").val());
        }

    }

    function selectPayment() {
        var customer_balance_paid_by = $("#customer_balance_paid_by").val();
        if (customer_balance_paid_by == 3) {
            $('#customer_balance_paid_by-block').show();
        } else {
            $('#customer_balance_paid_by-block').hide();
        }

    }

    $(document).ready(function () {
        
        $("#avail_pickup_date").datepicker({
            dateFormat: 'mm/dd/yy',
            minDate: '+0'
        });

        $("#balance_paid_by").change(function () {
            var balance_paid_by = $("#balance_paid_by").val();
            $.ajax({
                type: "POST",
                url: "<?=SITE_IN?>application/ajax/entities.php",
                dataType: 'json',
                data: {
                    action: 'getTermMSG',
                    balance_paid_by: balance_paid_by
                },
                success: function (res) {
                    if (res.success) {
                        $("#payments_terms").html(res.terms_condition);
                    } else {
                        Swal.fire("Can't send email. Try again later, please");
                    }
                }
            });

        });
    });

</script>

<!--create page content starts-->

    <?php include ROOT_PATH . 'application/templates/vehicles/create_js.php';?>
    <?php include ROOT_PATH . 'application/templates/vehicles/form.php';?>

    <h3>Transport Order Form</h3>
    <label class="btn btn-bold btn-label-warning text-left" style="margin-bottom:15px;">
        Complete the form below and click&nbsp;&nbsp;
        <span style="display:inline-block;color:green;"><strong>Create Order</strong></span>
        &nbsp;&nbsp;when finished. Required fields are marked with a 
        <span style="display:inline-block;color:red;">&nbsp;&nbsp;*</span>
    </label>

    <!--create form starts-->
    <form action="<?=getLink('orders/create')?>" method="post" onsubmit="javascript:tempDisableBeforeUnload = true;" id="create_form"  class="kt-form">
        <div class="order-select-shipper" style="float:none;<?php if (!empty($_POST)) {?>display:none;<?php }?>" id="select-shipper-block">
            <table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table order-edit" style="white-space:nowrap;">
                <tr>
                    <td width="50%">
                        <table width="100%" cellpadding="1" cellspacing="1">
                            <tr>
                                <td align="center">
                                    <img src="<?=SITE_IN?>images/select-shipper.png" onclick="selectShipper();"  width="150" height="150"/>
                                </td>
                            </tr>
                            <tr>
                                <td align="center">
                                    <div  onclick="selectShipper();" class="btn btn_dark_blue btn-sm" >Select Shipper</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="border-left:1px solid #093;">
                        <table width="100%" cellpadding="1" cellspacing="1">
                            <tr>
                                <td align="center">
                                    <img src="<?=SITE_IN?>images/add-shipper.png"  onclick="newShipper();"  width="150" height="150"/>
                                </td>
                            </tr>
                            <tr>
                                <td align="center">
                                    <div  onclick="newShipper();"  class="btn btn_dark_blue btn-sm" >New Shipper</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
        <div id="shipperDiv" style="background:#fff;border:1px solid #ebedf2;" class="mb-5 mt-3">
		    <div class="row">
                <div class="col-12">
                    <div class="kt-portlet__head hide_show"  >
                        <div class="kt-portlet__head-label  " >
                            <h3 class="shipper_detail" id="Shipper_Information" >
                                Shipper Information
                            </h3>
                        </div>
                    </div>
                    <div class="kt-portlet__body pt-3 pb-3" style="padding-left:20px;padding-right:20px;">
	                    <div class="kt-portlet__body order-edit">        
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <?php
                                    $checkedShipper = "";
                                    if (!empty($_POST)) {
                                        if ($_POST['save_shipper'] == 1) {
                                            $checkedShipper = " checked=checked ";
                                        }
                                    }
                                    ?>
                                    <?php if (empty($_POST) || $_POST['save_shipper'] == 1) {?>
                                    <div id="save_shipper_info">
                                        <input type="hidden" name="save_shipper" id="save_shipper" value="1" />
                                        <label for="save_shipper">Create New Account</label>
                                    </div>
                                    <?php }?>
                                    <?php
                                    $checkedShipper = "";
                                    if (!empty($_POST)) {
                                        if ($_POST['update_shipper'] == 1) {
                                            $checkedShipper = " checked=checked ";
                                        }
                                    }
                                    ?>
                                    <?php if (empty($_POST) || $_POST['update_shipper'] == 1) {?>
                                    <div id="update_shipper_info">
                                        <input type="hidden" name="update_shipper" id="update_shipper" value="1" />
                                        <label for="save_shipper">Update Account Information</label>
                                    </div>
                                    <?php }?>
                                </div>
                                
                                <div class="col-12 col-sm-4">
                                    <div class="new_form-group">
                                        @shipper_email@
                                    </div>
                                </div>
                                
                                <div class="col-12 col-sm-4">
                                    <div class="new_form-group">
                                        @shipper_address1@
                                    </div>
                                </div>          
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <div class="new_form-group">
                                        @shipper_fname@
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="new_form-group ">
                                        <div class="row">
                                            <div class="col-10">
                                                @shipper_phone1@
                                            </div>
                                            <div class="col-2" style="width: 43px; margin: 0px">
                                                @shipper_phone1_ext@ 
                                            </div>
                                        </div>
                                        
                                        
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="new_form-group">
                                        @shipper_address2@
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <div class="new_form-group">
                                        @shipper_lname@
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="new_form-group ">
                                        <div class="row">
                                            <div class="col-10">
                                            @shipper_phone2@
                                            </div>
                                            <div class="col-2" style="width: 43px; margin: 0px">
                                            @shipper_phone2_ext@ 
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="new_form-group">
                                        @shipper_city@
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <div class="new_form-group">
                                        @shipper_company@
                                        <input type="hidden" name="shipper_company_id" id="shipper_company_id" />
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="new_form-group">
                                        @shipper_mobile@
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="new_form-group ">
                                        <div class="row">
                                            <div class="col-9">
                                                @shipper_state@
                                            </div>
                                            <div class="col-2" style="width: 43px; margin: 0px">
                                            @shipper_zip@
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <div class="new_form-group ">
                                        @shipper_type@
                                        <input type="hidden" name="shipper_company_id" id="shipper_company_id" />
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="new_form-group">
                                        @shipper_fax@
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="new_form-group ">
                                        @shipper_country@
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <div class="new_form-group">
                                        @shipper_hours@
                                        <input type="hidden" name="shipper_company_id" id="shipper_company_id" />
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="new_form-group ">
                                        @referred_by@
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="new_form-group">
                                        <div id="account_payble_contact_label_div">@account_payble_contact@</div>
                                    </div>
                                </div>
                            </div>
                        </div>
				    </div>
                </div>
            </div>
        </div>
        <div style="background:#fff;border:1px solid #ebedf2;" class="mb-5 mt-4">
            <div class="hide_show">
                <h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Pickup Contact &amp; Location</h3> 
            </div>
            <div id="pickup_contact_location_information_info_1" class="pt-3" style="padding-left:20px;padding-right:20px;">
                <?php
                $checkedLocation1 = "";
                if (!empty($_POST)) {
                    if ($_POST['save_location1'] == 1) {
                        $checkedLocation1 = " checked=checked ";
                    }
                } else {
                $checkedLocation1 = " checked=checked ";
                }
                ?>
            
                <div class="row">
                    <div class="col-2">				
                        <div class="new_form-group new_style_info pull-left" style="margin-top: 7px;margin-right: 15px;">
                            <input type="checkbox" name="save_location1" id="save_location1" value="1" <?php print $checkedLocation1;?>/>
                            <input type="hidden" name="origin_id" id="origin_id" value="0" />
                            <label for="save_location1">Save</label>
                        </div>
                    </div>
                    <div class="col-10">
                        <div class="new_form-group pull-left" style="margin-right:15px;max-width:125px;">
                            <?=functionButton('Select Location', "selectTerminal('origin')",'','btn_dark_blue btn-sm')?>
                        </div>
                        <div class="new_form-group pull-left" style="margin-top:5px;max-width:225px;">
                            <b>OR</b>&nbsp;&nbsp;&nbsp;<span class="like-link" onclick="setLocationSameAsShipperOrder('origin')">same as shipper</span>&#8203;
                        </div>
                        <br>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-3">
                        
                        <div class="new_form-group_4"  style="margin: 0px" >
                            @origin_address1@
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group_4 ">
                            @origin_contact_name@
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group_4">
                            <div class="row">
                                <div class="col-10">
                                @origin_phone1@
                                </div>
                                <div class="col-2" style="width: 43px; margin: 0px">
                                @origin_phone1_ext@
                                </div>
                            </div>
                        
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group_4 ">
                            @origin_mobile@
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group_4">
                            <label>Address 2:</label>
                            @origin_address2@
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group_4 ">
                            @origin_contact_name2@
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group_4 ">

                            <div class="row">
                                <div class="col-10">
                                @origin_phone2@
                                </div>
                                <div class="col-2" style="width: 43px; margin: 0px">
                                @origin_phone2_ext@
                                </div>
                            </div>
                        
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group_4 ">
                            @origin_mobile2@
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group_4">
                            @origin_city@
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group_4 ">
                            @origin_company_name@
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group_4 ">

                            <div class="row">
                                <div class="col-10">
                                @origin_phone3@
                                </div>
                                <div class="col-2" style="">
                                @origin_phone3_ext@
                                </div>
                            </div>
                        
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group_4 ">
                            @origin_fax@
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group_4 ">

                            <div class="row">
                                <div class="col-10">
                                @origin_state@
                                </div>
                                <div class="col-2 new_style_info">
                                @origin_zip@
                                </div>
                            </div>
                        
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group_4 ">
                            @origin_auction_name@
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group_4">

                            <div class="row">
                                <div class="col-10">
                                @origin_phone4@
                                </div>
                                <div class="col-2" style="">
                                @origin_phone4_ext@
                                </div>
                            </div>
                        
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group_4 ">
                            @origin_fax2@
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-3 col-sm-3">
                        <div class="new_form-group_4  ">
                            @origin_country@
                        </div>
                    </div>
                    <div class="col-3 col-sm-3">
                        <div class="new_form-group_4 ">
                            @origin_booking_number@
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-3 col-sm-3">
                        <div class="new_form-group_4  ">
                            @origin_type@
                        </div>
                    </div>
                    <div class="col-3 col-sm-3">
                        <div class="new_form-group_4 ">
                            @origin_buyer_number@
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-3 col-sm-3">
                        <div class="new_form-group_4 ">
                            @origin_hours@
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="new_form-group_4"></div>
                    </div>
                </div>
            </div>
        </div>
        <div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
            <div class="hide_show">
                <h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Delivery Contact Location</h3>
            </div>
            <div id="delivery_contact_location_information_info_1" class="pt-3" style="padding-left:20px;padding-right:20px;">
                <div class="row">
                    <div class="col-2">
                        <div class="new_form-group new_style_info pull-left" style="margin-top:7px;">                        
                            <?php
                            $checkedLocation2 = "";
                            if (!empty($_POST)) {
                                if ($_POST['save_location2'] == 1) {
                                    $checkedLocation2 = " checked=checked ";
                                }
                            } else {
                                $checkedLocation2 = " checked=checked ";
                            }
                            ?>
                            <input type="checkbox" name="save_location2" id="save_location2" value="1" <?php print $checkedLocation2;?>/>
                            <input type="hidden" name="destination_id" id="destination_id" value="0" />
                            <label for="save_location2">Save</label>
                        </div>
                    </div>
                    
                    <div class="col-10">
                        <div class="new_form-group pull-left" style="margin-right:15px;max-width:125px;">
                            <?=functionButton('Select Location', "selectTerminal('destination');",'','btn_dark_blue btn-sm')?>
                        </div>
                    
                        <div class="new_form-group pull-left" style="margin-top:5px;max-width:225px;">
                            <b>OR</b>&nbsp;&nbsp;&nbsp;<span class="like-link" onclick="setLocationSameAsShipperOrder('destination')">same as shipper</span>&#8203;
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group_4">
                            @destination_address1@
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group_4 ">
                            @destination_contact_name@
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group_4 ">

                            <div class="row">
                                <div class="col-10">
                                    @destination_phone1@
                                </div>
                                <div class="col-2" style="">
                                    @destination_phone1_ext@
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group_4 ">
                            @destination_mobile@
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group_4 ">
                            <label>&nbsp;</label>
                            @destination_address2@
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group_4 ">
                            @destination_contact_name2@
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group_4 ">
                            <div class="row">
                                <div class="col-10">
                                    @destination_phone2@
                                </div>
                                <div class="col-2" style="">
                                    @destination_phone2_ext@
                                </div>
                            </div>
                        
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group_4 ">
                            @destination_mobile2@
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group_4">
                            @destination_city@
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group_4 ">
                            @destination_company_name@
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group_4 ">

                    <div class="row">
                            <div class="col-10">
                                @destination_phone3@
                            </div>
                            <div class="col-2" style="">
                            @destination_phone3_ext@
                            </div>
                        </div>
                        
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group_4 ">
                            @destination_fax@
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group_4 ">

                            <div class="row">
                            <div class="col-10">
                                @destination_state@
                            </div>
                            <div class="col-2" style="">
                                @destination_zip@
                            </div>
                        </div>
                            
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group_4 ">
                            @destination_auction_name@
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group_4 ">
                            <div class="row">
                            <div class="col-10">
                                @destination_phone4@
                            </div>
                            <div class="col-2" style="">
                                @destination_phone4_ext@
                            </div>
                        </div>
                        
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group_4 ">
                            @destination_fax2@
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-3 col-sm-3">
                        <div class="new_form-group_4">
                            @destination_country@
                        </div>
                    </div>
                    <div class="col-3 col-sm-3">
                        <div class="new_form-group_4 ">
                            @destination_booking_number@
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-3 col-sm-3">
                        <div class="new_form-group_4">
                            @destination_type@
                        </div>
                    </div>
                    <div class="col-3 col-sm-3">
                        <div class="new_form-group_4 ">
                            @destination_buyer_number@
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-3 col-sm-3">
                        <div class="new_form-group_4">
                            @destination_hours@
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="new_form-group">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
            <div class="hide_show">
                <h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Shipping Information</h3>
            </div>
            <div id="shipping_information_info_1" class="pt-3" style="padding-left:20px;padding-right:20px;">
                <div class="row">
                    <div class="col-12 col-sm-4">
                        <div class="new_form-group">
                            @avail_pickup_date@
                        </div>
                    </div>
                    <div class="col-12 col-sm-8">
                        <div class="form-group">
                            @notes_from_shipper@
                            <div class="text-right"><i><strong>(Above notes will always appear on the dispatch sheet)</strong></i></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-4">
                        <div class="new_form-group ">
                            @shipping_ship_via@
                        </div>
                    </div>
                    <div class="col-12 col-sm-8">
                        <div class="form-group ">
                            @notes_for_shipper@<br/>
                            <div class="text-right"><i><strong>(Maximum character allowed is <div id="charNum" style="float:right;">&nbsp;<font color="red">60</font> )</div></strong></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
            <div class="hide_show">
                <h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Vehicle Information</h3>
            </div>
            <div id="vehicle_information_info_1" class="pt-3 pb-3" style="padding-left:20px;padding-right:20px;">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="vehicles-grid">
                                <thead>
                                    <tr>
                                        <th>Year</th>
                                        <th>Make</th>
                                        <th>Model</th>
                                        <th>Type</th>
                                        <th>Vin #</th>
                                        <th>Total Tariff</th>
                                        <th>Deposit</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($_POST['year'])): ?>
                                        <?php foreach ($_POST['year'] as $i => $year): ?>
                                            <tr class="grid-body<?=($i % 2) ? ' even' : ''?>" rel="<?=$i + 1?>">
                                                <td class="grid-body-left"><input type="hidden" name="year[]" value="<?=$year?>"/><?=$year?></td>
                                                <td><input type="hidden" name="make[]" value="<?=$_POST['make'][$i]?>"/><?=$_POST['make'][$i]?></td>
                                                <td><input type="hidden" name="model[]" value="<?=$_POST['model'][$i]?>"/><?=$_POST['model'][$i]?></td>
                                                <td><input type="hidden" name="type[]" value="<?=$_POST['type'][$i]?>"/><?=$_POST['type'][$i]?></td>
                                                <td align="center"><input type="text" name="vin[]" value="<?=$_POST['vin'][$i]?>"/></td>
                                                <td align="center"><input type="text" name="tariff[]" value="<?=$_POST['tariff'][$i]?>"  onkeyup="updatePricingInfo();"/></td>
                                                <td align="center"><input type="text" name="deposit[]" value="<?=$_POST['deposit'][$i]?>"  onkeyup="updatePricingInfo();"/>
                                                    <input type="hidden" name="color[]" value="<?=$_POST['color'][$i]?>"/>
                                                    <input type="hidden" name="plate[]" value="<?=$_POST['plate'][$i]?>"/>
                                                    <input type="hidden" name="state[]" value="<?=$_POST['state'][$i]?>"/>
                                                    <input type="hidden" name="carrier_pay[]" value="<?=$_POST['carrier_pay'][$i]?>"/>
                                                </td>
                                                <td align="center" class="grid-body-right">
                                                    <img src="<?=SITE_IN?>images/icons/copy.png" alt="Copy" title="Copy" onclick="copyVehicle(<?=$i + 1?>)" class="action-icon"/>

                                                    <img src="<?=SITE_IN?>images/icons/edit.png" alt="Edit" title="Edit" onclick="editVehicle(<?=$i + 1?>)" class="action-icon"/>
                                                    <img src="<?=SITE_IN?>images/icons/delete.png" alt="Delete" title="Delete" onclick="deleteVehicle(<?=$i + 1?>)" class="action-icon"/>
                                                </td>
                                            </tr>
                                        <?php endforeach;?>
                                    <?php endif;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-12">
                        <?=functionButton('Add Vehicle', 'addVehicle()','','btn_dark_blue btn-sm')?>
                        <?=functionButton('Get Rates', 'rates()','','btn_dark_blue btn-sm')?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-7">
                <div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
                    <div class="hide_show">
                        <h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Pricing Information</h3>
                    </div>
                    <div id="pricing_information_info_1" class="pt-3" style="padding-left:20px;padding-right:20px;">
                        <div class="row">
                            <div class="col-4">
                                <div class="new_form-group">
                                    <label>Total Tariff</label>
                                </div>
                            </div>
                            <div class="col-8">
                                <div class="new_form-group">
                                    <span id="total_tariff">@total_tariff@</span>&nbsp;<span class="grey-comment">(Edit tariff under the "Vehicle Information" section)</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <div class="new_form-group">
                                    <label>Required Deposit</label>
                                </div>
                            </div>
                            <div class="col-8">
                                <div class="new_form-group">
                                    <span id="total_deposit">@total_deposit@</span>&nbsp;<span class="grey-comment">(Edit deposit under the "Vehicle Information" section)</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <div class="new_form-group">
                                    <label>Carrier Pay</label>
                                </div>
                            </div>
                            <div class="col-8">
                                <div class="new_form-group">
                                    <span id="carrier_pay">@carrier_pay@</span>&nbsp;<span class="grey-comment">(Edit tariff and deposit under the "Vehicle Information" section)</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="new_form-group select_opt_new_info">
                                    @pickup_terminal_fee@
                                    <span class="grey-comment">(Do not include fees paid directly from shipper to terminal)</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="new_form-group select_opt_new_info">
                                    @delivery_terminal_fee@
                                    <span class="grey-comment">(Do not include fees paid directly from shipper to terminal)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-5">
                <div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
                    <div class="hide_show">
                        <h3 class="order_heading shipper_detail text-left" style="padding-left:15px;"> Payment Information</h3>
                    </div>
                    <div id="payment_information_info_1" class="pt-3 pb-4" style="padding-left:20px;padding-right:20px;">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    @balance_paid_by@
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <span id="fee_type_label_div">@fee_type@</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    @customer_balance_paid_by@
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="nform-group input_wdh_100_per">
                                    @payments_terms@
                                </div>
                            </div>
                        </div>
                    </div>
                </div>	
            </div>
        </div>
        <div style="background:#fff;border:1px solid #ebedf2;" class="mt-3" id="customer_balance_paid_by-block">
            <div class="hide_show">
                <h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Credit Card Information</h3>
            </div>
            <div id="credit_card_information_info_1" class="pt-3" style="padding-left:20px;padding-right:20px;">
                <div class="row">
                    <div class="col-12 col-sm-4">
                        <div class="new_form-group" style="margin-top:18px;">
                            <label for="save_card"><!--Save--></label>
                            <span style="margin-right:15px;" class="like-link" onclick="setLocationSameAsShipperOrder('e_cc')">same as shipper</span>&#8203;
                            @auto_payment@
                        </div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <div class="new_form-group">
                            @e_cc_fname@
                        </div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <div class="new_form-group">
                            @e_cc_lname@
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group ">
                            @e_cc_type@
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group">
                            @e_cc_number@
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group select_opt_new_info">
                            @e_cc_cvv2@ <img src="<?=SITE_IN?>images/icons/cards.gif" alt="Card Types" width="129" height="16" style="vertical-align:middle;" />
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group select_opt_new_info">
                            @e_cc_month@ / @e_cc_year@
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group">
                            @e_cc_address@
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group">
                            @e_cc_city@
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group select_opt_new_info select_wdh_100_per">
                            @e_cc_state@
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group select_opt_new_info input_wdh_100_per">
                            @e_cc_zip@
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="background:#fff;border:1px solid #ebedf2;" class="mt-3 mb-5" id="customer_balance_paid_by-block">
            <div class="hide_show">
                <h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Internal Notes</h3>
            </div>
            <div id="internal_notes_information_info_1" class="pt-3" style="padding-left:20px;padding-right:20px;">
                <div class="new_form-group ">
                    @note_to_shipper@
                </div>
            </div>
        </div>
        <div class="button_info_new">
            <div class="match_carrier_info_new pull-left">
                @match_carrier@
            </div>
            <table class="pull-right" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td>
                        <input type="hidden" name="send_email" value="0" id="co_send_email"/>
                        <?=functionButton("Save &amp; Email", 'createAndEmail();','','btn-sm btn_dark_blue');?>
                    </td>
                    <td style="padding-left: 15px;"><?=submitButtons(SITE_IN . "application/orders", "Create Order")?></td>
                </tr>
            </table>
        </div>
    </form>
    <!--create form ends-->

    <!--Unique shipper modal starts-->
    <div class="modal fade" id="uniqueShipper" tabindex="-1" role="dialog" aria-labelledby="uniqueShipper_model" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uniqueShipper_model">Email must be unique</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="shipperInfo">
                        <p><b style="color:red;">Email already registered, Please use different email</b></p>
                    </div>
                    <div id="shipperData" style="display:block;">
                        <table class="table table-bordered" >
                            <thead >
                                <tr>
                                    <th>Select</th>
                                    <th>Name</th>
                                    <th>Company</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th align="center">Quotes</th>
                                    <th align="center">Orders</th>
                                </tr>
                            </thead>
                            <tbody id="shipper-info"></tbody>
                        </table>
                    </div>
                    <div id="orderQuotesList" style="display: block; max-height:300px; overflow-y:scroll;"></div>
                    <div id="popupLoader" style="display:none;">
                        <center><img src="https://cdn.dribbble.com/users/24711/screenshots/2713076/bumpy_loader_2x.gif" style="width:30%; height:30%;"></center>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="back()">Back</button>
                    <button type="button" class="btn btn-primary" onclick="uniqueShipper()">OK</button>
                </div>
            </div>
        </div>
    </div>
    <!--Unique shipper modal ends-->

<!--create page content ends-->

<input type="hidden" id="auto_quote_api_pin" value="">
<input type="hidden" id="auto_quote_api_key" value="">
<input type="hidden" id="order_deposit" value="">
<input type="hidden" id="order_deposit_type" value="">

<!--including auto quotes JavaScript library-->
<script src="<?php echo SITE_IN ?>core/js/core.js"></script>

<script>

    function rates() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'https://www.centraldispatch.com/protected/cargo/sample-prices-lightbox',
            data: {
                'num_vehicles': 2,
                'ozip': 10005,
                'dzip': 10001,
                'enclosed': 0,
                'inop': 0,
                'vehicle_types': 'SUV',
                'miles': 0
            },
            success: function (res) {
            swal.fire(res);
            },
            error: function () {
                swal.fire('Failed to get response from central dispatch');
            },
            complete: function () {
                Swal.fire('Please wait')
                Swal.showLoading()

            }
        });
    }

    function quickPrice() {
        var data = {
            origin_city: $('#origin_city').val(),
            origin_state: $('#origin_state').val(),
            origin_zip: $('#origin_zip').val(),
            origin_country: $('#origin_country').val(),
            destination_city: $('#destination_city').val(),
            destination_state: $('#destination_state').val(),
            destination_zip: $('#destination_zip').val(),
            destination_country: $('#destination_country').val(),
            shipping_est_date: $('#avail_pickup_date').val(),
            shipping_ship_via: $('#shipping_ship_via').val(),
            vehicles: []
        };
        $('input[name="type[]"]').each(function () {
            data.vehicles.push($(this).val());
        });
        if (data.vehicles.length == 0) {
            swal.fire('No vehicles for quote');
            return;
        }
        if (data.origin_city == '' || data.origin_state == '' || data.origin_zip == '') {
            swal.fire('Invalid Origin Information');
            return;
        }
        if (data.destination_city == '' || data.destination_state == '' || data.destination_zip == '') {
            swal.fire('Invalid Destination Information');
            return;
        }
        if (data.shipping_est_date == '') {
            swal.fire('Invalid Shipping Date');
            return;
        }
        if (data.shipping_ship_via == '') {
            swal.fire('You should specify "Ship Via" field');
            return;
        }
        
        $("body").nimbleLoader("show");
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: BASE_PATH + 'application/ajax/autoquote.php',
            data: data,
            success: function (res) {
                var i = 0;
                var quoted = 0;
                $('input[name="carrier_pay[]"]').each(function () {
                    if (parseFloat(res[i].carrier_pay) != 0) {
                        $(this).val(res[i].carrier_pay);
                        $(this).parent().next().find('input[name="deposit[]"]').val(res[i].deposit);
                        quoted++;
                    }
                    i++;
                    swal.fire(quoted + ' vehicles quoted.');
                });
                updatePricingInfo();
            },
            error: function () {
                Swal.fire('Failed to calculate Quick Price');
            },
            complete: function () {
                $("body").nimbleLoader("hide");
            }
        });
    }

    $("#shipper_email").blur(function () {
        if ($("#shipper_email").val() !== "") {
            checkUniqueShipperData('email',$("#shipper_email").val());
        }
    });

    function checkUniqueShipperData(key, value) {

        $("#shipperData").show();
        $("#orderQuotesList").html("");
        $("#orderQuotesList").hide();
        $.ajax({
            type: 'POST',
            url: BASE_PATH + 'application/ajax/shipper.php',
            dataType: 'json',
            data: {
                action: 'validateUniqueShipper',
                key: key,
                value: value
            },
            success: function (response) {
            
                if (response.exists > 0) {
                    $("#shipper_email").val("");
                    var html = '<tr>\n\
                                    <td><input type="radio" id="selectedShipper" style="font-size:0px" value="' + response.id + '"></td>\n\
                                    <td>' + response.first_name + " " + response.last_name + '</td>\n\
                                    <td>' + response.company_name + '</td>\n\
                                    <td><a style="color:#008ec2" href="mailto:' + response.email + '" title="' + response.email + '">' + response.email + '</a></td>\n\
                                    <td style="color:#008ec2">' + response.phone1 + '</td>\n\
                                    <td>' + response.address1 + '<br>' + response.city + '<br>' + response.state + '<br>' + response.country + '<br>' + response.zip_code + '</td>\n\
                                    <td><img onclick="getShipperQuotes()" src="<?php echo SITE_IN; ?>/images/icons/info.png" title="Info" alt="Info" width="16" height="16"></td>\n\
                                    <td><img onclick="getShipperOrders()" src="<?php echo SITE_IN; ?>/images/icons/info.png" title="Info" alt="Info" width="16" height="16"></td>\n\
                                </tr>';

                    $("#shipper-info").html(html);
                    $("#uniqueShipper").modal();
                }
            }
        });
    }

    function  checkUniqueShipperData_ok() {

        if ((document.getElementById("selectedShipper").checked) == true) {
            var selectedShipper = $("#selectedShipper").val();
            $("#shipperid").val(selectedShipper);
            $("#shipper_fname").val(response.first_name);
            $("#shipper_lname").val(response.last_name);
            $("#shipper_company").val(response.company_name);
            $("#shipper_type").val(response.shipper_type);
            $("#shipper_hours").val(response.hours_of_operation);
            $("#shipper_email").val(response.email);
            $("#shipper_phone1").val(response.phone1);
            $("#shipper_phone1_ext").val(response.phone1_ext);
            $("#shipper_phone2").val(response.phone2);
            $("#shipper_phone2_ext").val(response.phone2_ext);
            $("#shipper_mobile").val(response.cell);
            $("#shipper_fax").val(response.fax1);
            $("#referred_by").val(response.referred_id);
            $("#shipper_address1").val(response.address1);
            $("#shipper_address2").val(response.address2);
            $("#shipper_city").val(response.city);
            $("#shipper_state").val(response.state);
            $("#shipper_zip").val(response.zip_code);
            $("#shipper_country").val(response.country);
            $(this).modal('hide');
        } else {
            $(this).modal('hide');
        }
    }

    function back() {

        $("#orderQuotesList").html("");
        $("#orderQuotesList").hide();
        $("#shipperData").show();

    }

    $(document).ready(function(){

        $.ajax({
            type: 'POST',
            url: BASE_PATH + 'application/ajax/accounts.php',
            dataType: 'json',
            data: {
                action: 'getOrderDeposite',
                owner_id: '<?php echo $_SESSION['member']['parent_id']; ?>'
            },
            success: function (response) {
                $("#order_deposit").val(response.response.order_deposit);
                $("#order_deposit_type").val(response.response.order_deposit_type);
                $("#auto_quote_api_pin").val(response.response.auto_quote_api_pin);
                $("#auto_quote_api_key").val(response.response.auto_quote_api_key);
            }
        });

        $("#add_vehicle_deposit,#add_vehicle_carrier_pay,#shipper_phone1,#shipper_phone2,#shipper_mobile,#shipper_fax,#origin_phone1,#origin_phone2,#origin_phone3,#origin_phone4,#origin_mobile,#origin_mobile2,#origin_fax,#origin_fax2,#destination_phone1,#destination_phone2,#destination_phone3,#destination_phone4,#destination_mobile,#destination_mobile2,#destination_fax,#destination_fax2").keypress(function (e) {
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                $("#errmsg").html("Digits Only").show().fadeOut("slow");
                return false;
            }
        });

        $("#origin_phone1,#origin_phone2,#origin_phone3,#origin_phone4,#origin_mobile,#origin_mobile2,#origin_fax,#origin_fax2,#shipper_phone1,#shipper_phone2,#shipper_mobile,#shipper_fax,#destination_phone1,#destination_phone2,#destination_phone3,#destination_phone4,#destination_mobile,#destination_phone4,#destination_mobile2,#destination_fax,#destination_fax2").attr("placeholder", "xxx-xxx-xxxx");
        $("#origin_phone1,#origin_phone2,#origin_phone3,#origin_phone4,#origin_mobile,#origin_mobile2,#origin_fax,#origin_fax2,#shipper_phone1,#shipper_phone2,#shipper_mobile,#shipper_fax,#destination_phone1,#destination_phone2,#destination_phone3,#destination_phone4,#destination_mobile,#destination_phone4,#destination_mobile2,#destination_fax,#destination_fax2").attr('maxlength','10');
        $('#origin_phone1,#origin_phone2,#origin_phone3,#origin_phone4,#origin_mobile,#origin_mobile2,#origin_fax,#origin_fax2,#shipper_phone1,#shipper_phone2,#shipper_mobile,#shipper_fax,#destination_phone1,#destination_phone2,#destination_phone3,#destination_phone4,#destination_mobile,#destination_phone4,#destination_mobile2,#destination_fax,#destination_fax2').keyup(function() {

        function phoneFormat() {
            phone = phone.replace(/[^0-9]/g, '');
            phone = phone.replace(/(\d{3})(\d{3})(\d{4})/, "$1-$2-$3");
            return phone;
        }

        var phone = $(this).val();
            phone = phoneFormat(phone);
            $(this).val(phone);
        });

        jQuery('#pickup_terminal_fee').keyup(function () { 
            this.value = this.value.replace(/[^0-9\.]/g,''); 
        });

    });
</script>