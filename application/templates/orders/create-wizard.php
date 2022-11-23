<style>

    ul.typeahead.dropdown-menu li a {
        height: 40px !important;
    }

    .order-select-shipper {
        background-color: #f2f2f2;
        border: 1px solid #cccccc;
        border-radius: 5px;
        float: left;
        line-height: 20px;
        padding: 10px;
        width:40%;
    }

	h3.details {
		padding:22px 0 0;
		width:100%;
		font-size:20px;
	}

    ul.typeahead.dropdown-menu li a {
        padding-left: 10px !important;
        border-bottom: #CCC 1px solid !important;
        color: #222 !important;
        height: 55px !important;
    }

    .dropdown-menu {
        max-height:300px !important;
        overflow-y:scroll !important;
    }

    #summaryCost{
        border:none;
    }

    #summaryCost td{
        border:none;
    }

    .error{
        border:1px solid red !important;
    }

    label.error {
        display: none !important;
    }

    .form-steps {
        display: none;
    }

    .step {
        height: 15px;
        width: 15px;
        margin: 0 2px;
        background-color: #bbbbbb;
        border: none;
        border-radius: 50%;
        display: inline-block;
        opacity: 0.5;
    }

    .step.active {
        background-color: #008ec2;
    }

    .new_form-group label{
        margin-top:9px !important;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .switch input { 
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        height: 34px;
        width: 59px;
        bottom: 0;
        background-color: #edeef7;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: #2196F3;
    }

    input:focus + .slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }

    #swal2-title{
        font-size: 12px !important;
        line-height: 20px !important;
    }

    table.summary-table td{
        border: none;
        padding: 0px;
    }

    .weight600{
        font-weight:600;
    }
    
    .summary-tiles-small-height{
        min-height:125px !important;
    }

    .summary-tiles{
        float: left;
        margin-bottom: 40px;
    }

    .summary-tile-header {
        background: #edeef7;
        padding: 10px;
        font-weight: 600;
    }

    .summary-tile-body {
        border:1px solid #edeef7;
        padding: 10px;
    }

    #save_shipper_label{
        font-weight:600;
    }

    .new_form-group_4 label{
        font-size:12px;
    }

    #pickup_terminal_fee{
        margin-left:0px;
        margin-right:10px;
    }

    #delivery_terminal_fee{
        margin-left:0px;
        margin-right:10px;
    }

    .nextBtn{
        min-width: 125px;
        height:58px;
        border-radius:5px;
    }

    .previousBtn {
        color: #3699FF;
        background-color: #E1F0FF;
        border-color: transparent;
        min-width: 125px;
        height:58px;
        border-radius:5px;
    }

    .tabWrapper {
        width:100%; 
        height:115px;
    }

    .footerWizard{
        border-top:1px solid #edeef7;
        padding:30px;
    }

    .tabMetronic {
        margin-left: 30px;
        margin-top:30px;
        width:110px;
        float:left;
        border-bottom: 5px solid #edeef7;
        cursor: pointer;
    }

    .tabMetronic-active {
        border-bottom: 5px solid #44adf3 !important;
    }

    .tab-color{
        color: #edeef7;
        font-size:12px;
    }

    .tab-color-active {
        color: #44adf3 !important;
    }

    .override-size{
        font-size:30px;
    }

    .tabMetronicRight{
        margin-left: 30px;
        margin-top:40px;
        float:left;
    }

    #kt_body{
        padding-left: 15px !important;
        padding-right: 15px !important;
        margin-top: 30px;
    }

    #kt_content{
        background: #FFF !important;
        margin: 10px;
    }

    .kt-subheader{
        margin: 15px 10px 10px 10px !important;
    }

    .kt-subheader__main {
        height:50px;
        margin-left:15px;
    }

    .btn-adjustments{
        padding-right: 40px;
        padding-bottom: 10px;
    }
</style>

<!--tabs indicator starts-->
<div class="tabWrapper">
    <div class="tabMetronic tabMetronic-active tabM-1" onclick="selectStepTab(1)">
        <div class="wizard-label">
            <h3 class="wizard-title tab-color tab-color-active tabMT-1">
                <span class="override-size">1. </span>Shipper
            </h3>
        </div>
    </div>
    <div class="tabMetronic tabM-2" onclick="selectStepTab(2)">
        <div class="wizard-label">
            <h3 class="wizard-title tab-color tabMT-2">
                <span class="override-size">2. </span>Pickup
            </h3>
        </div>
    </div>
    <div class="tabMetronic tabM-3" onclick="selectStepTab(3)">
        <div class="wizard-label">
            <h3 class="wizard-title tab-color tabMT-3">
                <span class="override-size">3. </span>Delivery
            </h3>
        </div>
    </div>
    <div class="tabMetronic tabM-4" onclick="selectStepTab(4)">
        <div class="wizard-label">
            <h3 class="wizard-title tab-color tabMT-4">
                <span class="override-size">4. </span>Shipping
            </h3>
        </div>
    </div>
    <div class="tabMetronic tabM-5" onclick="selectStepTab(5)">
        <div class="wizard-label">
            <h3 class="wizard-title tab-color tabMT-5">
                <span class="override-size">5. </span>Vehicle
            </h3>
        </div>
    </div>
    <div class="tabMetronic tabM-6" onclick="selectStepTab(6)">
        <div class="wizard-label">
            <h3 class="wizard-title tab-color tabMT-6">
                <span class="override-size">6. </span>Pay. Terms
            </h3>
        </div>
    </div>
    <div class="tabMetronic tabM-7" onclick="selectStepTab(7)">
        <div class="wizard-label">
            <h3 class="wizard-title tab-color tabMT-7">
                <span class="override-size">7. </span>Notes
            </h3>
        </div>
    </div>
    <div class="tabMetronic tabM-8" onclick="selectStepTab(8)">
        <div class="wizard-label">
            <h3 class="wizard-title tab-color tabMT-8">
                <span class="override-size">8. </span>Summary
            </h3>
        </div>
    </div>
</div>
<!--tabs indicator ends-->

<!--tab steps starts-->
<div class="form-steps form-step-1">
    <div id="select-shipper-block">
        <div style="margin-bottom:20px;"></div>
        <table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table order-edit" style="white-space:nowrap;">
            <tbody>
                <tr>
                    <td width="50%" style="padding: 20px 20px 20px 40px;">
                        <table width="100%" cellpadding="1" cellspacing="1" style="border:1px solid #edeef7;">
                            <tbody>
                                <tr>
                                    <td align="center" style="padding-top: 20px; padding-bottom: 20px;">
                                        <img src="/images/select-shipper.png" onclick="chooseShipper(true);" width="150" height="150">
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding-bottom: 40px;">
                                        <div onclick="chooseShipper(true);" class="btn btn_dark_blue btn-sm">Select Shipper</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td width="50%" style="padding: 20px 40px 20px 20px;">
                        <table width="100%" cellpadding="1" cellspacing="1" style="border:1px solid #edeef7;">
                            <tbody>
                                <tr>
                                    <td align="center" style="padding-top: 20px; padding-bottom: 20px;">
                                        <img src="/images/add-shipper.png" onclick="chooseShipper(false);" width="150" height="150">
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding-bottom: 40px;">
                                        <div onclick="chooseShipper(false);" class="btn btn_dark_blue btn-sm">New Shipper</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    
                </tr>
            </tbody>
        </table>
    </div>
    <div style="display:none;" id="shipper-form" class="mb-5">
        <div style="border-bottom:1px solid #edeef7; margin-bottom:20px;"></div>
        <div class="row">
            <div class="col-12">
                <div class="kt-portlet__body pt-3 pb-3" style="padding-left:20px;padding-right:20px;">
                    <div class="kt-portlet__body">
                        <form id="create-order-1" action="#">
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <div>
                                        <input type="hidden" name="save_shipper" id="" value="1">
                                        <label for="save_shipper" id="save_shipper_label">Create New Account <br><a href='javascript:void(0)' onclick='chooseShipper(true)'>Choose Existing Shipper</a></label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="new_form-group">
                                        <label for="shipper_email">
                                            <span class="required">*</span>Email:
                                        </label>
                                        <input class="form-box-textfield form-control" tabindex="6" name="shipper_email" id="shipper_email" type="text">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="new_form-group">
                                        <label for="shipper_email">
                                            Address 1:
                                        </label>
                                        <input class="form-box-textfield form-control" name="shipper_address" tabindex="12" id="shipper_address" type="text">
                                        <div id="suggestions-box-shipper" class="suggestions"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <div class="new_form-group">
                                        <label for="shipper_email">
                                            <span class="required">*</span>First Name:
                                        </label>
                                        <input class="form-box-textfield form-control" tabindex="1" name="shipper_fname" id="shipper_fname" type="text">
                                    </div>
                                </div>
                                <div class="col-10 col-sm-4">
                                    <div class="new_form-group">
                                        <div class="row">
                                            <div class="col-10">
                                                <label>
                                                    <span class="required">*</span>Phone:
                                                </label>
                                                <input style="width:130px;" class="form-box-textfield form-control" tabindex="7" name="shipper_phone1" id="shipper_phone1" type="text">
                                            </div>
                                            <div class="col-2" style="width: 43px; margin: 0px">
                                                <input style="width:55px;margin-left:0px;" placeholder="Ext." class="form-box-textfield form-control" name="shipper_phone1_ext" id="shipper_phone1_ext" type="text"> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="new_form-group">
                                        <label for="shipper_email">
                                            Address 2:
                                        </label>
                                        <input class="form-box-textfield form-control" tabindex="13" name="shipper_address2" id="shipper_address2" type="text">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <div class="new_form-group">
                                        <label for="shipper_email">
                                            <span class="required">*</span>Last Name:
                                        </label>
                                        <input class="form-box-textfield form-control" tabindex="2" name="shipper_lname" id="shipper_lname" type="text">
                                    </div>
                                </div>
                                <div class="col-10 col-sm-4">
                                    <div class="new_form-group">
                                        <div class="row">
                                            <div class="col-10">
                                                <label>
                                                    Phone 2:
                                                </label>
                                                <input style="width:130px;" class="form-box-textfield form-control" tabindex="8" name="shipper_phone2" id="shipper_phone2" type="text">
                                            </div>
                                            <div class="col-2" style="width: 43px; margin: 0px">
                                                <input style="width:55px;margin-left:0px;" placeholder="Ext." class="form-box-textfield form-control" name="shipper_phone2_ext" id="shipper_phone2_ext" type="text"> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="new_form-group">
                                        <label for="shipper_email">
                                            City:
                                        </label>
                                        <input class="form-box-textfield form-control" tabindex="14" name="shipper_city" id="shipper_city" type="text">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <div class="new_form-group">
                                        <label for="shipper_email">
                                            Company:
                                        </label>
                                        <input class="form-box-textfield form-control" name="shipper_company" tabindex="3" id="shipper_company" type="text">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="new_form-group">
                                        <label for="shipper_email">
                                            Mobile:
                                        </label>
                                        <input class="form-box-textfield form-control" tabindex="9" name="shipper_mobile" id="shipper_mobile" type="text">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="new_form-group">
                                        <div class="row">
                                            <div class="col-9">
                                                @shipper_state@
                                            </div>
                                            <div class="col-2" style="width: 43px; margin: 0px">
                                                <input style="width:90px;margin-left:0px;" placeholder="ZipCode" class="form-box-textfield form-control" name="shipper_zip" id="shipper_zip" type="text">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <div class="new_form-group">
                                        @shipper_type@
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="new_form-group">
                                        <label for="shipper_fax">
                                            Fax:
                                        </label>
                                        <input class="form-box-textfield form-control" tabindex="10" name="shipper_fax" id="shipper_fax" type="text">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="new_form-group">
                                        @shipper_country@
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <div class="new_form-group">
                                        <label for="shipper_hours">
                                            Hours:
                                        </label>
                                        <input class="form-box-textfield form-control" tabindex="5" name="shipper_hours" id="shipper_hours" type="text">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="new_form-group">
                                        @referred_by@
                                    </div>
                                </div>
                                <div id="payable-info" class="row col-sm-12" style="display:none; border-top:1px solid #edeef7; margin-top:30px;">
                                    <br/>
                                    <br/>
                                    <hr>
                                    <div class="col-12 col-sm-12">
                                        <div class="new_form-group">
                                            <label for="account_payble_contact">Account Payable Info</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-12">
                                        <div class="new_form-group">
                                            <strong>
                                                <a href="javascript:void(0)" onclick="chooseShipperAccount(true)">Same as Shipper</a>
                                            </strong>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="new_form-group">
                                            <label for="account_payble_contact">
                                                First Name:
                                            </label>
                                            <input class="form-box-textfield form-control" tabindex="18" id="payable_first_name" name="payable_first_name"  id="payable_first_name" type="text">
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="new_form-group">
                                            <label for="account_payble_contact">
                                                Last Name:
                                            </label>
                                            <input class="form-box-textfield form-control" tabindex="19" id="payable_last_name" name="payable_last_name"  id="payable_last_name" type="text">
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="new_form-group">
                                            <label for="account_payble_contact">
                                                Email:
                                            </label>
                                            <input class="form-box-textfield form-control" tabindex="20" id="payable_email" name="payable_email"  id="payable_email" type="text">
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="new_form-group">
                                            <div class="row">
                                                <div class="col-10">
                                                    <label>
                                                        <span class="required">*</span>Phone:
                                                    </label>
                                                    <input style="width:130px;" class="form-box-textfield form-control" tabindex="21" name="payable_phone" id="payable_phone" type="text" placeholder="xxx-xxx-xxxx" maxlength="10">
                                                </div>
                                                <div class="col-2" style="width: 43px; margin: 0px">
                                                    <input style="width:55px;margin-left:0px;" tabindex="22" placeholder="Ext." class="form-box-textfield form-control" name="payable_phone_ext" id="payable_phone_ext" type="text"> 
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-8" id="account_payble_contact_div">
                                        <div class="new_form-group">
                                            <label for="account_payble_contact">
                                                Note:
                                            </label>
                                            <input class="form-box-textfield form-control" id="account_payble_contact" name="account_payble_contact" type="text">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row footerWizard">
            <div class="col-12 col-sm-12 text-center">
                <button type="button" class="btn btn-primary font-weight-bolder text-uppercase px-9 py-4 nextBtn" onclick="validateStep1()">NEXT</button>
            </div>
        </div>
    </div>
</div>

<div class="form-steps form-step-2">
    <form id="create-order-2" action="#">
        <div style="border-bottom:1px solid #edeef7; margin-bottom:20px;"></div>
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
            <div class="row" style="padding-bottom:15px;">
                <div class="col-sm-9">&nbsp;</div>
                <div class="col-sm-1" style="padding-top:10px; padding-left:10px;">
                    <input type="checkbox" name="save_location1" id="save_location1" value="1" <?php print $checkedLocation1;?>/>
                    <input type="hidden" name="origin_id" id="origin_id" value="0" />
                    <label for="save_location1" style="font-weight:600;">Save</label>
                </div>
                <div class="col-sm-2" style="padding-left:15px;">
                    <select class="form-control" id="locationOrigin" onchange="openOptions('origin')">
                        <option value="0">Select Location</option>
                        <option value="1">Search Location</option>
                        <option value="2">Same as Shipper</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-sm-3">
                    
                    <div class="new_form-group_4"  style="margin: 0px" >
                        @origin_address1@
                        <div id="suggestions-box" class="suggestions"></div>
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
                        <div id="suggestions-box">
                        </div>
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
            <div class="row footerWizard">
                <div class="col-12 col-sm-6 text-right">
                    <button type="button" class="btn btn-light-primary font-weight-bolder text-uppercase px-9 py-4 previousBtn" onclick="previous(1)">PREVIOUS</button>
                </div>
                <div class="col-12 col-sm-6 text-left">
                    <button type="button" class="btn btn-primary font-weight-bolder text-uppercase px-9 py-4 nextBtn" onclick="validateStep2()">NEXT</button>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="form-steps form-step-3">
    <form id="create-order-3" action="#">
        <div style="border-bottom:1px solid #edeef7; margin-bottom:20px;"></div>
        <div class="mb-5">
            <div id="delivery_contact_location_information_info_1" class="pt-3" style="padding-left:20px;padding-right:20px;">
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
                <div class="row" style="padding-bottom:15px;">
                    <div class="col-sm-9">&nbsp;</div>
                    <div class="col-sm-1" style="padding-top:10px; padding-left:10px;">
                        <input type="checkbox" name="save_location2" id="save_location2" value="1" <?php print $checkedLocation1;?>/>
                        <input type="hidden" name="destination_id" id="destination_id" value="0" />
                        <label for="save_location2" style="font-weight:600;">Save</label>
                    </div>
                    <div class="col-sm-2" style="padding-left:15px;">
                        <select class="form-control" id="locationDestination" onchange="openOptions('destination')">
                            <option value="0">Select Location</option>
                            <option value="1">Search Location</option>
                            <option value="2">Same as Shipper</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-3">
                        <div class="new_form-group_4">
                            @destination_address1@
                            <div id="suggestions-box-destination" class="suggestions"></div>
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
                            <label>Address 2:</label>
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
                </div>
                <div class="row footerWizard">
                    <div class="col-12 col-sm-6 text-right">
                        <button type="button" class="btn btn-light-primary font-weight-bolder text-uppercase px-9 py-4 previousBtn" onclick="previous(2)">PREVIOUS</button>
                    </div>
                    <div class="col-12 col-sm-6 text-left">
                        <button type="button" class="btn btn-primary font-weight-bolder text-uppercase px-9 py-4 nextBtn" onclick="validateStep3()">NEXT</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="form-steps form-step-4">
    <form id="create-order-4" action="#">
        <div class="mb-5">
            <div style="border-bottom:1px solid #edeef7; margin-bottom:20px;"></div>
            <div id="shipping_information_info_1" class="pt-3" style="padding-left:20px;padding-right:20px;">
                <div class="row">
                    <div class="col-12 col-sm-4">
                        <div class="new_form-group">
                            @avail_pickup_date@
                        </div>
                    </div>
                    <div class="col-12 col-sm-8">
                        <i class="fas fa-info-circle" style="cursor:pointer;padding-top:10px; padding-left:0px;" data-toggle="tooltip" title="What text to enter here? please let me know"></i>
                    </div>
                    <div class="col-12 col-sm-4">
                        <div class="new_form-group ">
                            @shipping_ship_via@
                        </div>
                    </div>
                    <div class="col-12 col-sm-8">
                        <i class="fas fa-info-circle" style="cursor:pointer;padding-top:10px; padding-left:0px;" data-toggle="tooltip" title="What text to enter here? please let me know"></i>
                    </div>
                </div>
                <br/>
                <div class="row">
                    <div class="col-12 col-sm-12">
                        <div class="form-group ">
                            @notes_for_shipper@<br/>
                            <div class="text-right"><i><strong>(Maximum character allowed is <div id="charNum" style="float:right;">&nbsp;<font color="red">60</font> )</div></strong></i></div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12">
                        <div class="form-group">
                            @notes_from_shipper@
                            <div class="text-right"><i><strong>(Above notes will always appear on the dispatch sheet)</strong></i></div>
                        </div>
                    </div>
                </div>
                <div class="row footerWizard">
                    <div class="col-12 col-sm-6 text-right">
                        <button type="button" class="btn btn-light-primary font-weight-bolder text-uppercase px-9 py-4 previousBtn" onclick="previous(3)">PREVIOUS</button>
                    </div>
                    <div class="col-12 col-sm-6 text-left">
                        <button type="button" class="btn btn-primary font-weight-bolder text-uppercase px-9 py-4 nextBtn" onclick="validateStep4()">NEXT</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="form-steps form-step-5">
    <form id="create-order-5" action="#">
        <div style="border-bottom:1px solid #edeef7; margin-bottom:20px;"></div>
        <div class="mb-5">
            <div id="vehicle_information_info_1" class="pt-3 pb-3" style="padding-left:20px;padding-right:20px;">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive" id="vehicle-div">
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
        <div class="row footerWizard">
            <div class="col-12 col-sm-6 text-right">
                <button type="button" class="btn btn-light-primary font-weight-bolder text-uppercase px-9 py-4 previousBtn" onclick="previous(4)">PREVIOUS</button>
            </div>
            <div class="col-12 col-sm-6 text-left">
                <button type="button" class="btn btn-primary font-weight-bolder text-uppercase px-9 py-4 nextBtn" onclick="validateStep5()">NEXT</button>
            </div>
        </div>
    </form>
</div>

<div class="form-steps form-step-6">
    <form id="create-order-6" action="#">
        <div style="border-bottom:1px solid #edeef7; margin-bottom:20px;"></div>
        <div class="row">
            <div class="col-12 col-sm-9">
                <div class="mb-5">
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
                            <div class="col-2">
                                <div class="new_form-group">
                                    <label>Pickup Terminal Fees</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <input type="text" name="pickup_terminal_fee" id="pickup_terminal_fee" class="form-control">
                            </div>
                            <div class="col-8">
                                <div class="new_form-group">
                                    <span class="grey-comment">$ (Do not include fees paid directly from shipper to terminal)</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-2">
                                <div class="new_form-group">
                                    <label>Delivery Terminal Fees</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <input type="text" name="delivery_terminal_fee" id="delivery_terminal_fee" class="form-control">
                            </div>
                            <div class="col-8">
                                <div class="new_form-group">
                                    <span class="grey-comment">$ (Do not include fees paid directly from shipper to terminal)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-3">
                <div class="mb-5">
                    <div id="payment_information_info_1" class="pt-3 pb-4" style="padding-left:20px;padding-right:20px;">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    @balance_paid_by@
                                </div>
                            </div>
                        </div>
                        <!-- <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <span id="fee_type_label_div">@fee_type@</span>
                                </div>
                            </div>
                        </div> -->
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
            <input type="hidden" name="e_cc_fname" class="cc-hiddens">
            <input type="hidden" name="e_cc_type" class="cc-hiddens">
            <input type="hidden" name="e_cc_month" class="cc-hiddens">
            <input type="hidden" name="e_cc_year" class="cc-hiddens">
            <input type="hidden" name="e_cc_address" class="cc-hiddens">
            <input type="hidden" name="e_cc_state" class="cc-hiddens">
            <input type="hidden" name="e_cc_lname" class="cc-hiddens">
            <input type="hidden" name="e_cc_number" class="cc-hiddens">
            <input type="hidden" name="e_cc_cvv2" class="cc-hiddens">
            <input type="hidden" name="e_cc_city" class="cc-hiddens">
            <input type="hidden" name="e_cc_zip" class="cc-hiddens">

            <div class="col-12 col-sm-12" id="select-or-new-card"></div>
        </div>
        <div class="row footerWizard">
            <div class="col-12 col-sm-6 text-right">
                <button type="button" class="btn btn-light-primary font-weight-bolder text-uppercase px-9 py-4 previousBtn" onclick="previous(5)">PREVIOUS</button>
            </div>
            <div class="col-12 col-sm-6 text-left">
                <button type="button" class="btn btn-primary font-weight-bolder text-uppercase px-9 py-4 nextBtn" onclick="validateStep6()">NEXT</button>
            </div>
        </div>
    </form>
</div>

<div class="form-steps form-step-7">
    <form id="create-order-7" action="#">
        <div style="border-bottom:1px solid #edeef7; margin-bottom:20px;"></div>
        <div class="mt-3 mb-5">
            <div id="internal_notes_information_info_1" class="pt-3" style="padding-left:20px;padding-right:20px;">
                <div class="new_form-group ">
                    <label for="shipper_email">
                        Notes:
                    </label>
                    @note_to_shipper@
                    <input type="hidden" name="notes_prority" id="notes_prority">
                </div>
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <div class="new_form-group">
                            <?php $notes = $this->notes; ?>
                            <label>Quick Notes</label>
                            <select name="quick_notes" class="form-control" id="quick_notes" onchange="quickNotesAdd();">
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
                    
                    <div class="col-12 col-sm-6">
                        <div class="new_form-group">
                            <label>Priority</label>
                            <select name="priority_notes" class="form-control" onchange="quickNotesAdd()" id="priority_notes" >
                                <option value="1">Low</option>
                                <option value="2">High</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-12 text-right btn-adjustments">
                        @match_carrier@
                        <input type="hidden" name="send_email" value="0" id="co_send_email"/>
                    </div>
                </div>
                <div class="row footerWizard">
                    <div class="col-12 col-sm-6 text-right">
                        <button type="button" class="btn btn-light-primary font-weight-bolder text-uppercase px-9 py-4 previousBtn" onclick="previous(6)">PREVIOUS</button>
                    </div>
                    <div class="col-12 col-sm-6 text-left">
                        <button type="button" class="btn btn-primary font-weight-bolder text-uppercase px-9 py-4 nextBtn" onclick="validateStep7()">NEXT</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="form-steps form-step-8">
    <form id="create-order-8" action="#">
    <div class="row">
        <div class="col-sm-12">
            <h2 class="text-center">Double check your order details</h2>
            <br/><br/>
            <div class="row">
                <div class="col-sm-8">
                    <div class="col-sm-12 summary-tiles">
                        <div class="summary-tile-header">Pickup Information</div>
                        <div class="summary-tile-body">
                            <table class="table summary-table" id="pickup-summary">
                            </table>
                        </div>
                    </div>
                    <div class="col-sm-12 summary-tiles">
                        <div class="summary-tile-header">Delivery Information</div>
                        <div class="summary-tile-body">
                            <table class="table summary-table" id="delivery-summary">
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4" style="">
                    <div class="col-sm-12 summary-tiles">
                        <div class="summary-tile-header">Shipper Information</div>
                        <div class="summary-tile-body">
                            <table class="table summary-table" id="shipper-summary">
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12" style="">
                    <div class="col-sm-12 summary-tiles">
                        <div class="summary-tile-header">Vehicle Information</div>
                            <div class="summary-tile-body" id="vehicle-summary">
                            <table class="table table-bordered table-hover" id="vehicles-grid">
                            </table>                        
                            <table class="table table-bordered table-hover" id="summaryCost">
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 summary-tiles">
                    <div class="summary-tile-header">Shipping Information</div>
                    <div class="summary-tile-body summary-tiles-small-height">
                        <table class="table summary-table" id="shipping-summary">
                        </table>
                    </div>
                </div>
                <div class="col-sm-4 summary-tiles">
                    <div class="summary-tile-header">Payment Terms</div>
                    <div class="summary-tile-body summary-tiles-small-height">
                        <table class="table summary-table" id="payment-summary">
                        </table>
                    </div>
                </div>
                <div class="col-sm-4 summary-tiles">
                    <div class="summary-tile-header">Order Notes</div>
                    <div class="summary-tile-body summary-tiles-small-height">
                        <table class="table summary-table" id="notes-summary">
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row footerWizard col-sm-12">
        <div class="col-12 col-sm-6 text-right">
            <button type="button" class="btn btn-light-primary font-weight-bolder text-uppercase px-9 py-4 previousBtn" onclick="previous(7)">PREVIOUS</button>
        </div>
        <div class="col-12 col-sm-6 text-left">
            <button type="button" class="btn btn-primary font-weight-bolder text-uppercase px-9 py-4 nextBtn" onclick="validateStep8(this)">CREATE</button>
        </div>
    </div>
    </form>
</div>
<!--tabs steps ends-->

<input type="hidden" id="auto_quote_api_pin" value="">
<input type="hidden" id="auto_quote_api_key" value="">
<input type="hidden" id="order_deposit" value="">
<input type="hidden" id="order_deposit_type" value="">

<!--Unique shipper modal starts-->
<div class="modal fade" id="uniqueShipper" tabindex="-1" role="dialog" aria-labelledby="uniqueShipper_model" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uniqueShipper_model">Email must be unique</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="shipperInfo">
                    <p><b style="color:red;" id="ushipperheading">Email already registered, Please use different email</b></p>
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
                    <div class="col-sm-12 text-right">
                        <button type="button" class="btn btn-dark btn-sm" onclick="uniqueShipper()">OK</button>
                    </div>
                </div>
                <div id="shipperOrderQuotesListWrapper" style="display:none;">
                    <div id="orderQuotesList" style="display: block; max-height:300px; overflow-y:scroll;"></div>
                    <div id="popupLoader" style="display:none;">
                        <center><b>Loading ... </b></center>
                    </div>
                    <div class="col-sm-12 text-right">
                        <br/><br/>
                        <button type="button" class="btn btn-dark btn-sm" onclick="back()">Back</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Unique shipper modal ends-->

<!--Add Card Modal-->
<div class="modal fade" id="add-card-modal" tabindex="-1" role="dialog" aria-labelledby="add_card_modal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uniqueShipper_model">Add New Shipper CC</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="pt-3" style="padding-left:20px;padding-right:20px;">
                    <div class="row">
                        <div class="col-12 col-sm-12">
                            <div class="new_form-group">
                                <strong>
                                    <a href="javascript:void(0)" onclick="chooseShipperCard(true)">Same as Shipper</a>
                                </strong>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12">
                            <div class="new_form-group">
                                <label for="e_cc_fname">First Name:</label>
                                <input tabindex="70" type="text" maxlength="50" class="form-box-textfield form-control" value="" id="e_cc_fname">
                            </div>
                            <div class="new_form-group">
                                <label for="e_cc_lname">Last Name:</label>
                                <input tabindex="71" type="text" maxlength="50" class="form-box-textfield form-control" value="" id="e_cc_lname">
                            </div>
                            <div class="new_form-group ">
                                <label for="e_cc_type">Type:</label>
                                <select tabindex="72" class="form-box-combobox e_cc_type_existing" id="e_cc_type">
                                    <option value="" selected="selected">--Select--</option>
                                    <option value="1">Visa</option>
                                    <option value="2">MasterCard</option>
                                    <option value="3">Amex</option>
                                    <option value="4">Discover</option>
                                </select>
                            </div>
                            <div class="new_form-group">
                                <label for="e_cc_number">Card Number:</label>
                                <input tabindex="73" class="form-box-textfield form-control" type="text" maxlength="16" value="" id="e_cc_number">
                                <img src="https://cargoflare.dev/images/icons/cards.gif" alt="Card Types" width="129" height="16" style="vertical-align:middle;margin-top:8px;margin-left:10px;">
                                <br/>
                            </div>
                            <div class="new_form-group">
                                <label for="e_cc_month">Exp. Date:</label>
                                <select tabindex="75" style="width:37%;" class="form-box-combobox e_cc_month_existing" id="e_cc_month">
                                    <option value="" selected="selected">--</option>
                                    <option value="01">01</option>
                                    <option value="02">02</option>
                                    <option value="03">03</option>
                                    <option value="04">04</option>
                                    <option value="05">05</option>
                                    <option value="06">06</option>
                                    <option value="07">07</option>
                                    <option value="08">08</option>
                                    <option value="09">09</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                </select>
                                <select tabindex="76" style="width:38%;" class="form-box-combobox e_cc_year_existing" id="e_cc_year">
                                    <option value="" selected="selected">--</option>
                                    <option value="2022">2022</option>
                                    <option value="2023">2023</option>
                                    <option value="2024">2024</option>
                                    <option value="2025">2025</option>
                                    <option value="2026">2026</option>
                                    <option value="2027">2027</option>
                                    <option value="2028">2028</option>
                                    <option value="2029">2029</option>
                                    <option value="2030">2030</option>
                                    <option value="2031">2031</option>
                                    <option value="2032">2032</option>
                                    <option value="2033">2033</option>
                                    <option value="2034">2034</option>
                                    <option value="2035">2035</option>
                                    <option value="2036">2036</option>
                                    <option value="2037">2037</option>
                                    <option value="2038">2038</option>
                                    <option value="2039">2039</option>
                                    <option value="2040">2040</option>
                                    <option value="2041">2041</option>
                                    <option value="2042">2042</option>
                                </select>
                            </div>
                            <div class="new_form-group">
                                <label for="e_cc_cvv2">CVV:</label>
                                <input tabindex="74" class="form-box-textfield form-control" type="text" maxlength="4" value="" id="e_cc_cvv2">
                            </div>
                            <div class="new_form-group">
                                <label for="e_cc_address">Address:</label>
                                <input tabindex="77" type="text" maxlength="255" class="form-box-textfield form-control" value="" id="e_cc_address">
                                <div id="suggestions-box-cc" class="suggestions"></div>
                            </div>
                            <div class="new_form-group">
                                <label for="e_cc_city">City:</label>
                                <input tabindex="78" type="text" maxlength="100" class="form-box-textfield form-control" value="" id="e_cc_city">
                            </div>
                            <div class="new_form-group">
                                <label for="e_cc_state">State:</label>
                                <select tabindex="79" class="form-box-combobox e_cc_state_existing" id="e_cc_state">
                                    <option value="" selected="selected">Select State</option>
                                    <option value="AL">Alabama</option>
                                    <option value="AK">Alaska</option>
                                    <option value="AZ">Arizona</option>
                                    <option value="AR">Arkansas</option>
                                    <option value="BS">Bahamas</option>
                                    <option value="CA">California</option>
                                    <option value="CO">Colorado</option>
                                    <option value="CT">Connecticut</option>
                                    <option value="DE">Delaware</option>
                                    <option value="DC">District of Columbia</option>
                                    <option value="FL">Florida</option>
                                    <option value="GA">Georgia</option>
                                    <option value="HI">Hawaii</option>
                                    <option value="ID">Idaho</option>
                                    <option value="IL">Illinois</option>
                                    <option value="IN">Indiana</option>
                                    <option value="IA">Iowa</option>
                                    <option value="KS">Kansas</option>
                                    <option value="KY">Kentucky</option>
                                    <option value="LA">Louisiana</option>
                                    <option value="ME">Maine</option>
                                    <option value="MD">Maryland</option>
                                    <option value="MA">Massachusetts</option>
                                    <option value="MI">Michigan</option>
                                    <option value="MN">Minnesota</option>
                                    <option value="MS">Mississippi</option>
                                    <option value="MO">Missouri</option>
                                    <option value="MT">Montana</option>
                                    <option value="NE">Nebraska</option>
                                    <option value="NV">Nevada</option>
                                    <option value="NH">New Hampshire</option>
                                    <option value="NJ">New Jersey</option>
                                    <option value="NM">New Mexico</option>
                                    <option value="NY">New York</option>
                                    <option value="NC">North Carolina</option>
                                    <option value="ND">North Dakota</option>
                                    <option value="OH">Ohio</option>
                                    <option value="OK">Oklahoma</option>
                                    <option value="OR">Oregon</option>
                                    <option value="PA">Pennsylvania</option>
                                    <option value="PR">Puerto Rico</option>
                                    <option value="RI">Rhode Island</option>
                                    <option value="SC">South Carolina</option>
                                    <option value="SD">South Dakota</option>
                                    <option value="TN">Tennessee</option>
                                    <option value="TX">Texas</option>
                                    <option value="UT">Utah</option>
                                    <option value="VT">Vermont</option>
                                    <option value="VA">Virginia</option>
                                    <option value="WA">Washington</option>
                                    <option value="WV">West Virginia</option>
                                    <option value="WI">Wisconsin</option>
                                    <option value="WY">Wyoming</option>
                                </select>
                            </div>
                            <div class="new_form-group">
                                <label for="e_cc_zip">Zip Code:</label>
                                <input tabindex="80" class="form-box-textfield form-control" type="text" maxlength="11" value="" id="e_cc_zip">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn_dark_green btn-sm" onclick="addShipperCardFunction()">Save</button>
            </div>
        </div>
    </div>
</div>
<!--Add Card Modal-->

<?php include ROOT_PATH . 'application/templates/vehicles/create_js.php';?>
<?php include ROOT_PATH . 'application/templates/vehicles/form.php';?>

<script>

    $("#shipper_email").blur(function () {
        if ($("#shipper_email").val() !== "") {
            checkUniqueShipperData('email',$("#shipper_email").val());
        }
    });

    $(document).click(()=>{
        $(".suggestions").html("");
    });

    $(document).ready(()=>{

        $(".suggestions").html("");

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

        $("#avail_pickup_date").datepicker({
            dateFormat: 'mm/dd/yy',
            minDate: '+0'
        });

        $('#customer_balance_paid_by-block').hide();

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

        $("#origin_phone1,#origin_phone2,#origin_phone3,#origin_phone4,#origin_mobile,#origin_mobile2,#origin_fax,#origin_fax2,#shipper_phone1,#shipper_phone2,#shipper_mobile,#shipper_fax,#destination_phone1,#destination_phone2,#destination_phone3,#destination_phone4,#destination_mobile,#destination_phone4,#destination_mobile2,#destination_fax,#destination_fax2, #payable_phone").attr("placeholder", "xxx-xxx-xxxx");
        $('#origin_phone1,#origin_phone2,#origin_phone3,#origin_phone4,#origin_mobile,#origin_mobile2,#origin_fax,#origin_fax2,#shipper_phone1,#shipper_phone2,#shipper_mobile,#shipper_fax,#destination_phone1,#destination_phone2,#destination_phone3,#destination_phone4,#destination_mobile,#destination_phone4,#destination_mobile2,#destination_fax,#destination_fax2, #payable_phone').keyup(function() {

            function phoneFormat(phone) {

                phone = phone.replace(/[^0-9]/g, '');
                phone = phone.replace(/(\d{3})(\d{3})(\d{4})/, "$1-$2-$3");

                if(phone.length > 12){
                    return phone.substring(0,12);
                }

                return phone;
            }

            var phone = $(this).val();
            phone = phoneFormat(phone);

            $(this).val(phone);
        });

        jQuery('#pickup_terminal_fee').keyup(function () { 
            this.value = this.value.replace(/[^0-9\.]/g,''); 
        });

        $(".form-step-1").show();
        $(".step-1").addClass('active');

        $("#account_payble_contact_div").hide();

        $("#e_cc_number").attr("placeholder", "xxxx-xxxx-xxxx-xxxx");
        $("#e_cc_number").keyup(function(){
            function phoneFormat(card) {
                card = card.replace(/[^0-9]/g, '');
                card = card.replace(/(\d{4})(\d{4})(\d{4})(\d{4})/, "$1-$2-$3-$4");

                return card;
            }

            var card = $(this).val();
            card = phoneFormat(card);

            $(this).val(card);
        });
        
    });

    let tab = 1;

    let validateStep1 = (desired=null) => {
        
        let error = false;
        let errorMessage = "";
        
        $(".form-control").removeClass('error');
        $(".form-box-combobox").removeClass('error');

        if($("#shipper_fname").val() == ""){
            errorMessage += "Shipper first name is required<br>";
            error = true;
            $("#shipper_fname").addClass("error");
        }

        if($("#shipper_lname").val() == ""){
            errorMessage += "Shipper last name is required<br>";
            error = true;
            $("#shipper_lname").addClass("error");
        }

        if($("#shipper_type").val() == ""){
            errorMessage += "Shipper type is required<br>";
            error = true;
            $("#shipper_type").addClass("error");
        }

        if($("#shipper_email").val() == ""){
            errorMessage += "Shipper email is required<br>";
            error = true;
            $("#shipper_email").addClass("error");
        }

        if($("#shipper_phone1").val() == ""){
            errorMessage += "Shipper phone1 is required<br>";
            error = true;
            $("#shipper_phone1").addClass("error");
        }

        if($("#referred_by").val() == "" || $("#referred_by").val() == null){
            errorMessage += "Source is required<br>";
            error = true;
            $("#referred_by").addClass("error");
        }

        if($("#shipper_type").val() == "Commercial"){

            if($("#account_payble_contact").val() == ""){
                errorMessage += "Payable contact required for commerical shippers<br>";
                error = true;
                $("#account_payble_contact").addClass("error");
            }

            if($("#shipper_hours").val() == ""){
                errorMessage += "Hours required for commerical shippers<br>";
                error = true;
                $("#shipper_hours").addClass("error");
            }

            if($("#shipper_company").val() == ""){
                errorMessage += "Company required for commerical shippers<br>";
                error = true;
                $("#shipper_company").addClass("error");
            }

            if($("#payable_first_name").val() == ""){
                errorMessage += "Payable First Name required for commerical shippers<br>";
                error = true;
                $("#payable_first_name").addClass("error");
            }

            if($("#payable_last_name").val() == ""){
                errorMessage += "Payable Last Name required for commerical shippers<br>";
                error = true;
                $("#payable_last_name").addClass("error");
            }
            
            if($("#payable_phone").val() == ""){
                errorMessage += "Payable Phone required for commerical shippers<br>";
                error = true;
                $("#payable_phone").addClass("error");
            }

            if($("#payable_email").val() == ""){
                errorMessage += "Payable Email required for commerical shippers<br>";
                error = true;
                $("#payable_email").addClass("error");
            }
        }

        if(error == true){
            $engine.notify(errorMessage);
            return false;
        }

        if(desired != null){
            next(desired);
        } else {
            next(2); 
        }
    }

    let validateStep2 = (desired=null) => {

        let error = false;
        let errorMessage = "";

        $(".form-control").removeClass('error');
        $(".form-box-combobox").removeClass('error');

        if($("#origin_city").val() == ""){
            errorMessage += "Origin city is required<br>";
            error = true;
            $("#origin_city").addClass("error");
        }

        if($("#origin_state").val() == ""){
            errorMessage += "Origin state is required<br>";
            error = true;
            $("#origin_state").addClass("error");
        }

        if($("#origin_zip").val() == ""){
            errorMessage += "Origin zip is required<br>";
            error = true;
            $("#origin_zip").addClass("error");
        }

        if($("#origin_type").val() == "" || $("#origin_type").val() == null){
            errorMessage += "Origin type is required<br>";
            error = true;
            $("#origin_type").addClass("error");
        }

        if($("#origin_type").val() == "Commercial"){
            if($("#origin_hours").val() == ""){
                errorMessage += "Hours required for commerical shippers<br>";
                error = true;
                $("#origin_hours").addClass("error");
            }

            if($("#origin_company_name").val() == ""){
                errorMessage += "Company required for commercial shippers<br>";
                error = true;
                $("#origin_company_name").addClass("error");
            }
        }

        if(error == true){
            $engine.notify(errorMessage);
            return false;
        }

        if(desired != null){
            next(desired);
        } else {
            next(3); 
        }
    }

    let validateStep3 = (desired=null) => {

        let error = false;
        let errorMessage = "";

        $(".form-control").removeClass('error');
        $(".form-box-combobox").removeClass('error');

        if($("#destination_city").val() == ""){
            errorMessage += "Destination city is required<br>";
            error = true;
            $("#destination_city").addClass("error");
        }

        if($("#destination_state").val() == "" || $("#destination_state").val() == null){
            errorMessage += "Destination state is required<br>";
            error = true;
            $("#destination_state").addClass("error");
        }

        if($("#destination_zip").val() == ""){
            errorMessage += "Destination zipcode is required<br>";
            error = true;
            $("#destination_zip").addClass("error");
        }

        if($("#destination_type").val() == "" || $("#destination_type").val() == null){
            errorMessage += "Destination type is required<br>";
            error = true;
            $("#destination_type").addClass("error");
        }

        if($("#destination_type").val() == "Commercial"){
            if($("#destination_hours").val() == ""){
                errorMessage += "Hours required for commercial shippers<br>";
                error = true;
                $("#destination_hours").addClass("error");
            }

            if($("#destination_company_name").val() == ""){
                errorMessage += "Company required for commerical shippers<br>";
                error = true;
                $("#destination_company_name").addClass("error");
            }
        }

        if(error == true){
            $engine.notify(errorMessage);
            return false;
        }

        if(desired != null){
            next(desired);
        } else {
            next(4); 
        }
    }

    let validateStep4 = (desired=null) => {

        let error = false;
        let errorMessage = "";

        $(".form-control").removeClass('error');
        $(".form-box-combobox").removeClass('error');
        
        if($("#avail_pickup_date").val() == ""){
            errorMessage += "Avail pickup date is required<br>";
            error = true;
            $("#avail_pickup_date").addClass("error");
        }

        if($("#shipping_ship_via").val() == "" || $("#shipping_ship_via").val() == null){
            errorMessage += "Ship via is required<br>";
            error = true;
            $("#shipping_ship_via").addClass("error");
        }
        
        if(error == true){
            $engine.notify(errorMessage);
            return false;
        }
        
        if(desired != null){
            next(desired);
        } else {
            next(5); 
        }
    }

    let validateStep5 = (desired=null) => {

        if( ($("#vehicles-grid tr").length - 1) < 1 ){
            swal.fire('Add atleast on vehicle');
            return false;
        }

        if(desired != null){
            next(desired);
        } else {
            next(6); 
        }
    }

    let validateStep6 = (desired=null) => {

        let error = false;
        let errorMessage = "";

        $(".form-control").removeClass('error');
        $(".form-box-combobox").removeClass('error');

        if($("#balance_paid_by").val() == "" || $("#balance_paid_by").val() == null){
            errorMessage += "Balance paid by is required<br>";
            error = true;
            $("#balance_paid_by").addClass("error");
        }

        if($("#customer_balance_paid_by").val() == "" || $("#customer_balance_paid_by").val() == null){
            errorMessage += "Customer balance paid by is required<br>";
            error = true;
            $("#customer_balance_paid_by").addClass("error");
        }

        if($("#payments_terms").val() == ""){
            errorMessage += "Payment terms is required<br>";
            error = true;
            $("#payments_terms").addClass("error");
        }

        if(error == true){
            $engine.notify(errorMessage);
            return false;
        }
        
        if(desired != null){
            next(desired);
        } else {
            next(7); 
        }
    }

    let validateStep7 = (desired=null) => {

        let html = `
            <tbody>
                <tr>
                    <td class="weight600">First Name</td><td>${$("#shipper_fname").val()}</td> 
                </tr>
                <tr>
                    <td class="weight600">Last Name</td><td>${$("#shipper_lname").val()}</td> 
                </tr>
                <tr>
                    <td class="weight600">Company Name</td><td>${$("#shipper_company").val()}</td> 
                </tr>
                <tr>
                    <td class="weight600">Shipper Type</td><td>${$("#shipper_type").val()}</td>
                </tr>
                <tr>
                    <td class="weight600">Hours</td><td>${$("#shipper_hours").val()}</td>
                </tr>
                <tr>
                    <td class="weight600">Email</td><td>${$("#shipper_email").val()}</td>
                </tr>
                <tr>
                    <td class="weight600">Phone</td><td>${$("#shipper_phone1").val()}</td> 
                </tr>
                <tr>
                    <td class="weight600">Phone2</td><td>${$("#shipper_phone2").val()}</td>
                </tr>
                <tr>
                    <td class="weight600">Mobile</td><td>${$("#shipper_mobile").val()}</td> 
                </tr>
                <tr>
                    <td class="weight600">Fax</td><td>${$("#shipper_fax").val()}</td> 
                </tr>
                <tr>
                    <td class="weight600">Source</td><td>${$("#referred_by option:selected").text()}</td> 
                </tr>
                <tr>
                    <td class="weight600">Address</td><td>${$("#shipper_address").val()}</td>
                </tr>
                <tr>
                    <td class="weight600">Address2</td><td>${$("#shipper_address2").val()}</td> 
                </tr>
                <tr>
                    <td class="weight600">City</td><td>${$("#shipper_city").val()}</td> 
                </tr>
                <tr>
                    <td class="weight600">State</td><td>${$("#shipper_state option:selected").text()}</td> 
                </tr>
                <tr>
                    <td class="weight600">ZipCode</td><td>${$("#shipper_zip").val()}</td>
                </tr>
                <tr>
                    <td class="weight600">Country</td><td>${$("#shipper_country option:selected").text()}</td>
                </tr>
                <tr>
                    <td class="weight600">Payable Name</td><td>${$("#payable_first_name").val()} ${$("#payable_last_name").val()}</td>
                </tr>
                <tr>
                    <td class="weight600">Payable Phone</td><td>${$("#payable_phone").val()} ${$("#payable_phone_ext").val()}</td>
                </tr>
                <tr>
                    <td class="weight600">Payable Email</td><td>${$("#payable_email").val()}</td>
                </tr>
            </tbody>
        `;
        $("#shipper-summary").html(html);

        html = `
            <tbody>
                <tr>
                    <td class="weight600">Address</td><td>${$("#origin_address1").val()}</td> 
                    <td class="weight600">Address 2</td><td>${$("#origin_address2").val()}</td> 
                    <td class="weight600">City</td><td>${$("#origin_city").val()}</td> 
                </tr>
                <tr>
                    <td class="weight600">State</td><td>${$("#origin_state option:selected").text()}</td>
                    <td class="weight600">Zipcode</td><td>${$("#origin_zip").val()}</td> 
                    <td class="weight600">Country</td><td>${$("#origin_country option:selected").val()}</td> 
                </tr>
                <tr>
                    <td class="weight600">Location</td><td>${$("#origin_type option:selected").text()}</td> 
                    <td class="weight600">Hours</td><td>${$("#origin_hours").val()}</td>
                    <td class="weight600">Contact Name</td><td>${$("#origin_contact_name").val()}</td> 
                </tr>
                <tr>
                    <td class="weight600">Contact Name2</td><td>${$("#origin_contact_name2").val()}</td> 
                    <td class="weight600">Company Name</td><td>${$("#origin_company_name").val()}</td> 
                    <td class="weight600">Auction Name</td><td>${$("#origin_auction_name").val()}</td>
                </tr>
                <tr>
                    <td class="weight600">Booking No.</td><td>${$("#origin_booking_number").val()}</td> 
                    <td class="weight600">Buyer No.</td><td>${$("#origin_buyer_number").val()}</td> 
                    <td class="weight600">Phone1</td><td>${$("#origin_phone1").val()}</td> 
                </tr>
                <tr>    
                    <td class="weight600">Phone2</td><td>${$("#origin_phone2").val()}</td>
                    <td class="weight600">Phone3</td><td>${$("#origin_phone3").val()}</td> 
                    <td class="weight600">Phone4</td><td>${$("#origin_phone4").val()}</td> 
                </tr>
                <tr>
                    <td class="weight600">Mobile</td><td>${$("#origin_mobile").val()}</td> 
                    <td class="weight600">Mobile2</td><td>${$("#origin_mobile2").val()}</td>
                    <td class="weight600">Fax</td><td>${$("#origin_fax").val()}</td> 
                </tr>
                <tr>
                    <td class="weight600">Fax2</td><td>${$("#origin_fax2").val()}</td>
                </tr>
            </tbody>
        `;
        $("#pickup-summary").html(html);

        html = `
            <tbody>
                <tr>
                    <td class="weight600">Address</td><td>${$("#destination_address1").val()}</td> 
                    <td class="weight600">Address 2</td><td>${$("#destination_address2").val()}</td> 
                    <td class="weight600">City</td><td>${$("#destination_city").val()}</td> 
                </tr>
                <tr>
                    <td class="weight600">State</td><td>${$("#destination_state option:selected").text()}</td>
                    <td class="weight600">Zipcode</td><td>${$("#destination_zip").val()}</td> 
                    <td class="weight600">Country</td><td>${$("#destination_country option:selected").val()}</td> 
                </tr>
                <tr>
                    <td class="weight600">Location</td><td>${$("#destination_type option:selected").text()}</td> 
                    <td class="weight600">Hours</td><td>${$("#destination_hours").val()}</td>
                    <td class="weight600">Contact Name</td><td>${$("#destination_contact_name").val()}</td> 
                </tr>
                <tr>
                    <td class="weight600">Contact Name2</td><td>${$("#destination_contact_name2").val()}</td> 
                    <td class="weight600">Company Name</td><td>${$("#destination_company_name").val()}</td> 
                    <td class="weight600">Auction Name</td><td>${$("#destination_auction_name").val()}</td>
                </tr>
                <tr>
                    <td class="weight600">Booking No.</td><td>${$("#destination_booking_number").val()}</td> 
                    <td class="weight600">Buyer No.</td><td>${$("#destination_buyer_number").val()}</td> 
                    <td class="weight600">Phone1</td><td>${$("#destination_phone1").val()}</td> 
                </tr>
                <tr>
                    <td class="weight600">Phone2</td><td>${$("#destination_phone2").val()}</td>
                    <td class="weight600">Phone3</td><td>${$("#destination_phone3").val()}</td> 
                    <td class="weight600">Phone4</td><td>${$("#destination_phone4").val()}</td> 
                </tr>
                <tr>
                    <td class="weight600">Mobile</td><td>${$("#destination_mobile").val()}</td> 
                    <td class="weight600">Mobile2</td><td>${$("#destination_mobile2").val()}</td>
                    <td class="weight600">Fax</td><td>${$("#destination_fax").val()}</td> 
                </tr>
                <tr>
                    <td class="weight600">Fax2</td><td>${$("#destination_fax2").val()}</td>
                </tr>
            </tbody>
        `;
        $("#delivery-summary").html(html);

        html = `
            <tbody>
                <tr>
                    <td class="weight600">1st Avail</td><td>${$("#avail_pickup_date").val()}</td>
                <tr>
                </tr>
                    <td class="weight600">Ship Via</td><td>${$("#shipping_ship_via option:selected").text()}</td> 
                <tr>
                </tr>
                    <td class="weight600">Dispatch Instructions</td><td>${$("#notes_from_shipper").val()}</td> 
                <tr>
                </tr>
                    <td class="weight600">Special Note</td><td>${$("#notes_for_shipper").val()}</td>
                </tr>
            </tbody>
        `;
        $("#shipping-summary").html(html);

        html = $("#vehicle-div").html();

        let allTariff = 0;
        for(var i = 0; i < $('input[name="tariff[]"]').length; i++){
            allTariff += Number($('input[name="tariff[]"]')[i].value);
        };

        let allDeposit = 0;
        for(var i = 0; i < $('input[name="deposit[]"]').length; i++){
            allDeposit += Number($('input[name="deposit[]"]')[i].value);
        };

        html += `
            <table class="table table-bordered table-hover" id="summaryCost">
                <tbody>
                    <tr>
                        <td colspan="8" align="right">Total Tariff : $${ (allTariff/2).toFixed(2) }</td>
                    </tr>
                    <tr>
                        <td colspan="8" align="right">Total Carrier Pay : $${ ((allTariff/2) - (allDeposit/2)).toFixed(2) }</td>
                    </tr>
                    <tr>
                        <td colspan="8" align="right">Total Deposit : $${ (allDeposit/2).toFixed(2) }</td>
                    </tr>
                </tbody>
            </table>
        `;
        $("#vehicle-summary").html(html);

        html = `
            <tbody>
                <tr>
                    <td class="weight600">Carrier Paid By </td><td>${$("#balance_paid_by option:selected").text()}</td>
                </tr>
                <tr>
                    <td class="weight600">Customer Paid By </td><td>${$("#customer_balance_paid_by option:selected").text()}</td>
                </tr>
                <tr>
                    <td class="weight600">Payment Terms </td><td>${$("#payments_terms").val()}</td>
                </tr>
            </tbody>
        `;
        $("#payment-summary").html(html);

        html = `
            <tbody>
                <tr>
                    <td class="weight600">Order Notes</td><td>${$("#note_to_shipper").val()}</td>
                </tr>
            </tbody>
        `;
        $("#notes-summary").html(html);

        if(desired != null){
            next(desired);
        } else {
            next(8);
        }
    }

    let validateStep8 = (ref) => {
        let step1 = JSON.stringify(jQuery('#create-order-1').serializeArray());
        let step2 = JSON.stringify(jQuery('#create-order-2').serializeArray());
        let step3 = JSON.stringify(jQuery('#create-order-3').serializeArray());
        let step4 = JSON.stringify(jQuery('#create-order-4').serializeArray());
        let step5 = JSON.stringify(jQuery('#create-order-5').serializeArray());
        let step6 = JSON.stringify(jQuery('#create-order-6').serializeArray());
        let step7 = JSON.stringify(jQuery('#create-order-7').serializeArray());

        let data = {
            step1 : step1,
            step2 : step2,
            step3 : step3,
            step4 : step4,
            step5 : step5,
            step6 : step6,
            step7 : step7,
        };

        ref.innerHTML = "PROCESSING PLEASE WAIT... !";
        ref.setAttribute("disabled", true);

        $.ajax({
            type: 'POST',
            url: BASE_PATH + 'application/orders/processOrder',
            dataType: 'json',
            data: data,
            success: function (r) {
                if(r.success){
                    
                    ref.innerHTML = "CREATE";
                    ref.removeAttribute("disabled");

                    $engine.notify("Order created successfully!");
                    location.href = BASE_PATH + "/application/orders/show/id/"+r.id;
                } else {
                    $engine.notify("Something went wrong!");
                }
            }
        });
    }

    let disableTabs = () => {
        $(".form-steps").hide();
        $(".tabMetronic").removeClass('tabMetronic-active');
        $(".tab-color").removeClass('tab-color-active');
    }

    let selectStepTab = (desired) => {
        let currentTab = tab;
        
        if(currentTab < desired){
            switch(currentTab){
                case 1:
                    validateStep1(desired);
                break;
                case 2:
                    validateStep2(desired);
                break;
                case 3:
                    validateStep3(desired);
                break;
                case 4:
                    validateStep4(desired);
                break;
                case 5:
                    validateStep5(desired);
                break;
                case 6:
                    validateStep6(desired);
                break;
                case 7:
                    validateStep7(desired);
                break;
                case 8:
                break;
                default:
                    $engine.notify("Please completed the steps first");
                break;
            }
        } else {
            previous(desired);
        }
    }

    let next = (step) => {
        disableTabs();
        $(".form-step-"+step).show();
        $(".tabM-"+step).addClass('tabMetronic-active');
        $(".tabMT-"+step).addClass('tab-color-active');
        
        tab++;
    }

    let previous = (step) => {
        disableTabs();
        $(".form-step-"+step).show();
        $(".tabM-"+step).addClass('tabMetronic-active');
        $(".tabMT-"+step).addClass('tab-color-active');

        tab--;
    }

    let chooseShipper = (existing) => {
        $("#acc_search_dialog_model").html("Select Shipper");
        $("#outerPopup .tabWrapper").show();

        if(existing){
            // open shipper popup
            selectShipper();
        } else {
            $("#select-shipper-block").hide();
            $("#shipper-form").show();
            $("save_shipper_label").html("Create New Account");
        }
    }

    let applySearchLeads = (num) => {
        selectedShipper = acc_data.shipper_leads_data[num];
        populateAccountForm('shipper', acc_data.shipper_leads_data[num]);

        $("#select-shipper-block").hide();
        $("#shipper-form").show();
        $("#save_shipper_label").html("Update Shipper Information <br><a href='javascript:void(0)' onclick='chooseShipper(true)'>Choose Another</a>");
    }

    let applySearch = (num) => {
        if(acc_data.shipper_data){
            selectedShipper = acc_data.shipper_data[num];
            populateAccountForm('shipper', acc_data.shipper_data[num]);
            $("#referred_by").val( acc_data.shipper_data[num].referred_id);
            typeselected();
            
        } else {
            selectedShipper = acc_data[num];
            $("#referred_by").val( acc_data[num].referred_id);
            if(tab == 2){
                populateAccountForm('origin', acc_data[num]);
            }

            if(tab == 3){
                populateAccountForm('destination', acc_data[num]);
            }
        }
        
        $("#select-shipper-block").hide();
        $("#shipper-form").show();
        $("#save_shipper_label").html("Update Shipper Information <br><a href='javascript:void(0)' onclick='chooseShipper(true)'>Choose Another</a>");
    }

    let populateAccountForm = (type, data) => {
        
        $(`#${type}_fname`).val(data.first_name);
        $(`#${type}_lname`).val(data.last_name);
        $(`#${type}_company`).val(data.company_name);
        $(`#${type}_email`).val(data.email);
        $(`#${type}_phone1`).val(formatPhoneNumber(data.phone1));
        $(`#${type}_phone2`).val(formatPhoneNumber(data.phone2));
        $(`#${type}_mobile`).val(formatPhoneNumber(data.cell));
        $(`#${type}_fax`).val(formatPhoneNumber(data.fax));
        $(`#${type}_address1`).val(data.address1);
        $(`#${type}_address2`).val(data.address2);
        $(`#${type}_city`).val(data.city);
        $(`#${type}_country`).val(data.country);
        if (data.country == "US") {
            $(`#${type}_state`).val(data.state);
        } else {
            $(`#${type}_state2`).val(data.state);
        }
        $(`#${type}_zip`).val(data.zip_code);
        $(`#${type}_type`).val(data.shipper_type);
        $(`#${type}_hours`).val(data.hours_of_operation);
        if (data.referred_by != '') {
            $("#referred_by").val(data.referred_by);
        }
        $("#account_payble_contact").val(data.account_payble_contact);
    }

    let chooseShipperCard = () => {
        $("#e_cc_fname").val($("#shipper_fname").val());
        $("#e_cc_lname").val($("#shipper_lname").val());
        $("#e_cc_address").val($("#shipper_address").val());
        $("#e_cc_city").val($("#shipper_city").val());
        $("#e_cc_state").val($("#shipper_state").val());
        $("#e_cc_zip").val($("#shipper_zip").val());
    }

    let chooseShipperAccount = () => {
        $("#payable_first_name").val($("#shipper_fname").val());
        $("#payable_last_name").val($("#shipper_lname").val());
        $("#payable_email").val($("#shipper_email").val());
        $("#payable_phone").val($("#shipper_phone1").val());
        $("#payable_phone_ext").val($("#shipper_phone1_ext").val());
    }

    let origintypeselected = () => {
        if ($("#origin_type").val() == "Commercial") {
            $('#origin_company-span').show();
            $('#origin_hour').show();
        } else {
            $('#origin_company-span').hide();
            $('#origin_hour').hide();
        }

    }

    let selectedShipper = null;
    let selectPayment = () => {
        console.log("Opening Modal");
        var customer_balance_paid_by = $("#customer_balance_paid_by").val();
        if (customer_balance_paid_by == 3) {
            let shipperId  = null;
            if(selectedShipper != null){
                shipperId = selectedShipper.id;
            }

            if(existingShipper != null){
                shipperId = existingShipper.id;
            }
            
            if(shipperId != null){
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: BASE_PATH + 'application/ajax/accounts.php',
                    data: {
                        action : 'AllCards',
                        AccountID : shipperId
                    },
                    success: function (res) {
                        if(res.success){

                            $("#cc-modal-body").html("");
                            let html = "";
                            if(res.Cards.length > 0){
                                var cardType = ['other','visa','master','amex','discover'];
                                let rows = ``;
                                res.Cards.forEach(element => {
                                    rows += `
                                        <tr class="cc-row">
                                            <td><input type="checkbox" value="${element.CardId}" onchange="setUsedCard(this)" class="selectedCard"></td>
                                            <td>${element.Number}</td>
                                            <td>${element.ExpiryMonth}/${element.ExpiryYear} ${element.CVV}</td>
                                            <td>${cardType[element.Type]}</td>
                                            <td>${element.FirstName}</td>
                                            <td>${element.LastName}</td>
                                            <td>${element.Address.trim() =="" ? "" : element.Address+","} ${element.City.trim() == "" ? "" : element.City+","} ${element.State.trim() == "" ? "" : element.State }<br/>${element.Zipcode}</td>
                                            <td>${element.Created}</td>
                                            <td>${element.Updated == null ? "" : element.Updated}</td>
                                            <td>${element.Status == 1 ? "Active" : "In Active"}</td>
                                        </tr>
                                    `;
                                });
                                html += `
                                    <div class="pt-3" style="padding-left:20px;padding-right:20px;">
                                        <div class="row">
                                            <div class="col-12 col-sm-12">
                                                <h4>Credit Card Information</h4>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-sm-12">
                                                    <a class="btn btn-primary" style="color:#FFFFFF;" data-toggle="modal" data-backdrop="static" data-target="#add-card-modal">Add Card</a>
                                                    <br/><br/>
                                                    <table class="table table-bordered table-striped" style="min-width: 1300px;">
                                                        <thead>
                                                            <tr>
                                                                <th>Select</th>
                                                                <th>Card Number</th>
                                                                <th>Expiry / CVV</th>
                                                                <th>Type</th>
                                                                <th>First Name</th>
                                                                <th>Last Name</th>
                                                                <th>Address</th>
                                                                <th>Added On</th>
                                                                <th>Updated On</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="shipper-cards">${rows}</tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;

                                $("#select-or-new-card").html(html);
                            } else {
                                html += `
                                    <div class="pt-3" style="padding-left:20px;padding-right:20px;">
                                        <div class="row">
                                            <div class="col-12 col-sm-12">
                                                <h4>Credit Card Information</h4>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-sm-12">
                                                    <a class="btn btn-primary" style="color:#FFFFFF;" data-toggle="modal" data-target="#add-card-modal">Add Card</a>
                                                    <br/><br/>
                                                    <table class="table table-bordered table-striped" style="min-width: 1300px;">
                                                        <thead>
                                                            <tr>
                                                                <th>Select</th>
                                                                <th>Card Number</th>
                                                                <th>Expiry / CVV</th>
                                                                <th>Type</th>
                                                                <th>First Name</th>
                                                                <th>Last Name</th>
                                                                <th>Address</th>
                                                                <th>Added On</th>
                                                                <th>Updated On</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="shipper-cards"><tr class="cc-row-empty"><td class='col-sm-12 text-center' colspan="10">No Cards</td></tr></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;

                                $("#select-or-new-card").html(html);
                            }
                        }
                    }
                });
            }
        } else {
            $("#credit-card-modal").modal('hide');
            $("#select-or-new-card").html("");
            $("#cc-modal-body").html("");
        }
    }

    let usedCard = null;
    let setUsedCard = (ref) => {
        $(".selectedCard").prop('checked', false);
        ref.checked = true;
        isNewCard = false;
        usedCard = ref.value;

        $.ajax({
            type: 'POST',
            url: BASE_PATH + 'application/ajax/accounts.php',
            dataType: 'json',
            data: {
                action: 'GetSavedCards',
                CardId: usedCard
            },
            success: function (response) {
                if(response.success){

                    $(".cc-hiddens").val("");

                    $('input[name=e_cc_fname]').val(response.data.FirstName);
                    $('input[name=e_cc_type]').val(response.data.Type);
                    $('input[name=e_cc_month]').val(response.data.ExpiryMonth);
                    $('input[name=e_cc_year]').val(response.data.ExpiryYear);
                    $('input[name=e_cc_address]').val(response.data.Address);
                    $('input[name=e_cc_state]').val(response.data.State);
                    $('input[name=e_cc_lname]').val(response.data.LastName);
                    $('input[name=e_cc_number]').val(response.data.Number);
                    $('input[name=e_cc_cvv2]').val(response.data.CVV);
                    $('input[name=e_cc_city]').val(response.data.City);
                    $('input[name=e_cc_zip]').val(response.data.Zipcode);
                }
            }
        });
    }

    let paid_by_ach_selected = () => {
        // if ($("#balance_paid_by").val() == 24) {
        //     $('#fee_type_label_div').show();
        //     $('#fee_type_div').show();
        // } else {
        //     $('#fee_type_label_div').hide();
        //     $('#fee_type_div').hide();
        // }
    }

    let quickNotesAdd = () => {
        $("#note_to_shipper").val($("#quick_notes").val());
        $("#notes_prority").val($("#priority_notes").val());
    }

    let setLocationSameAsShipperOrder = (location) => {
        
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

    let openOptions = (location) => {

        $("#acc_search_dialog_model").html("Select Location");
        $("#outerPopup .tabWrapper").hide();

        if(location == 'origin'){
            
            if($("#locationOrigin").val() == 1){
                selectTerminal('origin');
            }

            if($("#locationOrigin").val() == 2){
                setLocationSameAsShipperOrder('origin')
            }
        }

        if(location == 'destination'){
            if($("#locationDestination").val() == 1){
                selectTerminal('origin');
            }

            if($("#locationDestination").val() == 2){
                setLocationSameAsShipperOrder('destination');
            }
        }
        
    }

    let typeselected = () => {
        if ($("#shipper_type").val() == "Commercial") {
            $('#shipper_company-span').show();
            $('#account_payble_contact_div').show();
            $("#payable-info").show();
        } else {
            $('#shipper_company-span').hide();
            $('#account_payble_contact_div').hide();
            $("#payable-info").hide();
        }
    }

    let rates = () => {
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

    let quickPrice = () => {
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
            }
        });
    }

    let existingShipper = null;
    let isNewCard = true;
    let checkUniqueShipperData = (key, value) => {

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

                    function phoneFormat(phone) {
                        phone = phone.replace(/[^0-9]/g, '');
                        phone = phone.replace(/(\d{3})(\d{3})(\d{4})/, "$1-$2-$3");

                        if(phone.length > 12){
                            return phone.substring(0,12);
                        }

                        return phone;
                    }

                    var phone = response.phone1;
                    phone = phoneFormat(phone);
            
                    var html = '<tr>\n\
                                    <td><input type="checkbox" name="selectdShipper" style="font-size:0px" value="' + response.id + '" checked="false"></td>\n\
                                    <td>' + response.first_name + " " + response.last_name + '</td>\n\
                                    <td>' + response.company_name + '</td>\n\
                                    <td><a style="color:#008ec2" href="mailto:' + response.email + '" title="' + response.email + '">' + response.email + '</a></td>\n\
                                    <td style="color:#008ec2">' + phone + '</td>\n\
                                    <td style="width:250px;">' + (response.address1 ? response.address1+"," : "" ) + ' ' + (response.address2 ? response.address2+"<br/>" : "") +  (response.city ? response.city+", " : "")  + (response.state ? response.state+", " : "") + (response.country ? response.country+", " : "") + response.zip_code + '</td>\n\
                                    <td align="center"><input onclick="getShipperQuotes()" type="checkbox" class="shipperCheckbox" style="font-size:0px"></td>\n\
                                    <td align="center"><input onclick="getShipperOrders()" type="checkbox" class="shipperCheckbox" style="font-size:0px"></td>\n\
                                </tr>';

                    $("#shipper-info").html(html);
                    $("#uniqueShipper").modal();

                    existingShipper = response;
                }
            }
        });
    }

    var cType = "";
    var cMonth = "";
    var cYear = "";
    var cState = "";
    let useCard = () => {
        if(isNewCard){
            swal.fire('No Card Selected');
            return false;
        } else {

            $.ajax({
                type: 'POST',
                url: BASE_PATH + 'application/ajax/accounts.php',
                dataType: 'json',
                data: {
                    action: 'GetSavedCards',
                    CardId: usedCard
                },
                success: function (response) {
                    if(response.success){
                        var cardType = ['other','visa','master','amex','discover'];
                        let html = `
                            <div class="pt-3 footerWizard" style="padding-left:20px;padding-right:20px;">
                                <div class="row">

                                    <div class="col-12 col-sm-4">
                                    
                                        <div class="shipper_do_not_process mb-4" style="margin-top:7px;">
                                            <input name="auto_payment" type="checkbox" class="form-check-input123 " id="auto_payment" value="1">&nbsp;<label for="auto_payment">Do not process Automatically</label>
                                        </div>
                                        
                                        <div class="new_form-group">
                                            <label for="e_cc_fname">First Name:</label>
                                            <input tabindex="70" name="e_cc_fname" type="text" maxlength="50" class="form-box-textfield form-control" value="${response.data.FirstName}" id="e_cc_fname" readonly>
                                        </div>
                                        
                                        <div class="new_form-group">
                                            <label for="e_cc_lname">Last Name:</label>
                                            <input tabindex="71" name="e_cc_lname" type="text" maxlength="50" class="form-box-textfield form-control" value="${response.data.LastName}" id="e_cc_lname" readonly>
                                        </div>
                                        
                                    </div>
                                    
                                    <div class="col-12 col-sm-4">
                                        <div class="new_form-group ">
                                            <label for="e_cc_type">Type:</label>
                                            <select tabindex="72" style="width:150px;" name="e_cc_type" class="form-box-combobox e_cc_type_existing" id="e_cc_type" readonly>
                                                <option value="" selected="selected">--Select--</option>
                                                <option value="1">Visa</option>
                                                <option value="2">MasterCard</option>
                                                <option value="3">Amex</option>
                                                <option value="4">Discover</option>
                                            </select>
                                        </div>
                                        
                                        <div class="new_form-group">
                                            <label for="e_cc_number">Card Number:</label>
                                            <input tabindex="73" class="form-box-textfield form-control" name="e_cc_number" type="text" maxlength="16" value="${response.data.Number}" id="e_cc_number" readonly>
                                        </div>
                                        
                                        <div class="new_form-group">
                                            <label for="e_cc_cvv2">CVV:</label>
                                            <input tabindex="74" class="form-box-textfield form-control" style="width:75px;" name="e_cc_cvv2" type="text" maxlength="4" value="${response.data.CVV}" id="e_cc_cvv2" readonly> <img src="https://cargoflare.dev/images/icons/cards.gif" alt="Card Types" width="129" height="16" style="vertical-align:middle;margin-top:8px;margin-left:10px;">
                                        </div>
                                        
                                        <div class="new_form-group">
                                            <label for="e_cc_month">Exp. Date:</label>
                                            <select tabindex="75" style="width:75px;" name="e_cc_month" class="form-box-combobox e_cc_month_existing" id="e_cc_month" readonly>
                                                <option value="" selected="selected">--</option>
                                                <option value="01">01</option>
                                                <option value="02">02</option>
                                                <option value="03">03</option>
                                                <option value="04">04</option>
                                                <option value="05">05</option>
                                                <option value="06">06</option>
                                                <option value="07">07</option>
                                                <option value="08">08</option>
                                                <option value="09">09</option>
                                                <option value="10">10</option>
                                                <option value="11">11</option>
                                                <option value="12">12</option>
                                            </select>
                                            <span class="pull-left" style="margin:7px 5px 0 15px;">/</span>
                                            <select tabindex="76" style="width:75px;" name="e_cc_year" class="form-box-combobox e_cc_year_existing" id="e_cc_year" readonly>
                                                <option value="" selected="selected">--</option>
                                                <option value="2022">2022</option>
                                                <option value="2023">2023</option>
                                                <option value="2024">2024</option>
                                                <option value="2025">2025</option>
                                                <option value="2026">2026</option>
                                                <option value="2027">2027</option>
                                                <option value="2028">2028</option>
                                                <option value="2029">2029</option>
                                                <option value="2030">2030</option>
                                                <option value="2031">2031</option>
                                                <option value="2032">2032</option>
                                                <option value="2033">2033</option>
                                                <option value="2034">2034</option>
                                                <option value="2035">2035</option>
                                                <option value="2036">2036</option>
                                                <option value="2037">2037</option>
                                                <option value="2038">2038</option>
                                                <option value="2039">2039</option>
                                                <option value="2040">2040</option>
                                                <option value="2041">2041</option>
                                                <option value="2042">2042</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="new_form-group">
                                            <label for="e_cc_address">Address:</label>
                                            <input tabindex="77" name="e_cc_address" type="text" maxlength="255" class="form-box-textfield form-control" value="${response.data.Address}" id="e_cc_address" readonly>
                                        </div>
                                        <div class="new_form-group">
                                            <label for="e_cc_city">City:</label>
                                            <input tabindex="78" name="e_cc_city" type="text" maxlength="100" class="form-box-textfield form-control" value="${response.data.City}" id="e_cc_city" readonly>
                                        </div>
                                        <div class="new_form-group">
                                            <label for="e_cc_state">State:</label>
                                            <select tabindex="79" style="width:150px;" name="e_cc_state" class="form-box-combobox e_cc_state_existing" id="e_cc_state" readonly>
                                                <option value="" selected="selected">Select State</option>
                                                <option value="AL">Alabama</option>
                                                <option value="AK">Alaska</option>
                                                <option value="AZ">Arizona</option>
                                                <option value="AR">Arkansas</option>
                                                <option value="BS">Bahamas</option>
                                                <option value="CA">California</option>
                                                <option value="CO">Colorado</option>
                                                <option value="CT">Connecticut</option>
                                                <option value="DE">Delaware</option>
                                                <option value="DC">District of Columbia</option>
                                                <option value="FL">Florida</option>
                                                <option value="GA">Georgia</option>
                                                <option value="HI">Hawaii</option>
                                                <option value="ID">Idaho</option>
                                                <option value="IL">Illinois</option>
                                                <option value="IN">Indiana</option>
                                                <option value="IA">Iowa</option>
                                                <option value="KS">Kansas</option>
                                                <option value="KY">Kentucky</option>
                                                <option value="LA">Louisiana</option>
                                                <option value="ME">Maine</option>
                                                <option value="MD">Maryland</option>
                                                <option value="MA">Massachusetts</option>
                                                <option value="MI">Michigan</option>
                                                <option value="MN">Minnesota</option>
                                                <option value="MS">Mississippi</option>
                                                <option value="MO">Missouri</option>
                                                <option value="MT">Montana</option>
                                                <option value="NE">Nebraska</option>
                                                <option value="NV">Nevada</option>
                                                <option value="NH">New Hampshire</option>
                                                <option value="NJ">New Jersey</option>
                                                <option value="NM">New Mexico</option>
                                                <option value="NY">New York</option>
                                                <option value="NC">North Carolina</option>
                                                <option value="ND">North Dakota</option>
                                                <option value="OH">Ohio</option>
                                                <option value="OK">Oklahoma</option>
                                                <option value="OR">Oregon</option>
                                                <option value="PA">Pennsylvania</option>
                                                <option value="PR">Puerto Rico</option>
                                                <option value="RI">Rhode Island</option>
                                                <option value="SC">South Carolina</option>
                                                <option value="SD">South Dakota</option>
                                                <option value="TN">Tennessee</option>
                                                <option value="TX">Texas</option>
                                                <option value="UT">Utah</option>
                                                <option value="VT">Vermont</option>
                                                <option value="VA">Virginia</option>
                                                <option value="WA">Washington</option>
                                                <option value="WV">West Virginia</option>
                                                <option value="WI">Wisconsin</option>
                                                <option value="WY">Wyoming</option>
                                            </select>
                                        </div>
                                        <div class="new_form-group">
                                            <label for="e_cc_zip">Zip Code:</label>
                                            <input tabindex="80" class="form-box-textfield form-control" style="width:100px;" name="e_cc_zip" type="text" maxlength="11" value="${response.data.Zipcode}" id="e_cc_zip" readonly>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        `;

                        cType = response.data.Type;
                        cMonth = response.data.ExpiryMonth;
                        cYear = response.data.ExpiryYear;
                        cState = response.data.State;

                        $("#select-or-new-card").html(html);
                        $("#credit-card-modal").modal('hide');

                        setTimeout(function () {
                            $(".e_cc_type_existing").val(cType);
                            $(".e_cc_month_existing").val(cMonth);
                            $(".e_cc_year_existing").val(cYear);
                            $(".e_cc_state_existing").val(cState);
                        }, 1500);
                    }
                }
            });
        }
    }

    let uniqueShipper = () => {
        var selectedShipper = $("#selectedShipper").val();

        /* applying exisisting shipper to the form */
        $("#shipperid").val(selectedShipper);
        $("#shipper_fname").val(existingShipper.first_name);
        $("#shipper_lname").val(existingShipper.last_name);
        $("#shipper_company").val(existingShipper.company_name);
        $("#shipper_type").val(existingShipper.shipper_type);
        $("#shipper_hours").val(existingShipper.hours_of_operation);
        $("#shipper_email").val(existingShipper.email);
        
        function phoneFormat(phone) {
            phone = phone.replace(/[^0-9]/g, '');
            phone = phone.replace(/(\d{3})(\d{3})(\d{4})/, "$1-$2-$3");

            if(phone.length > 12){
                return phone.substring(0,12);
            }

            return phone;
        }

        var phone = existingShipper.phone1;
        phone = phoneFormat(phone);

        $("#shipper_phone1").val(phone);
        $("#shipper_phone1_ext").val(existingShipper.phone1_ext);
        $("#shipper_phone2").val(existingShipper.phone2);
        $("#shipper_phone2_ext").val(existingShipper.phone2_ext);
        $("#shipper_mobile").val(existingShipper.cell);
        $("#shipper_fax").val(existingShipper.fax1);
        $("#referred_by").val(existingShipper.referred_id);
        $("#shipper_address").val(existingShipper.address1);
        $("#shipper_address2").val(existingShipper.address2);
        $("#shipper_city").val(existingShipper.city);
        $("#shipper_state").val(existingShipper.state);
        $("#shipper_zip").val(existingShipper.zip_code);
        $("#shipper_country").val(existingShipper.country);
        typeselected();
        $("#uniqueShipper").modal('hide');
        $("#save_shipper_label").html("Update Shipper Information <br><a href='javascript:void(0)' onclick='chooseShipper(true)'>Choose Another</a>");
    }

    let back = () => {
        $("#shipperOrderQuotesListWrapper").hide();
        $("#ushipperheading").html("Email already registered, Please use different email");
        $(".shipperCheckbox").prop("checked", false);
        $("#orderQuotesList").html("");
        $("#orderQuotesList").hide();
        $("#shipperData").show();

    }

    let addShipperCard = (ref) => {
        $("#add-card-modal").modal('show');
        return false;
    }

    let addShipperCardFunction = () => {

        let error = false;
        let errorMessage = "";
        
        $(".form-control").removeClass('error');
        $(".form-box-combobox").removeClass('error');

        if($("#e_cc_fname").val() == ""){
            errorMessage += "Credit Card first name is required<br>";
            error = true;
            $("#e_cc_fname").addClass("error");
        }

        if($("#e_cc_lname").val() == ""){
            errorMessage += "Credit Card last name is required<br>";
            error = true;
            $("#e_cc_lname").addClass("error");
        }

        if($("#e_cc_number").val() == ""){
            errorMessage += "Credit Card Number is required<br>";
            error = true;
            $("#e_cc_number").addClass("error");
        } else {
            if(($("#e_cc_number").val().length != 16) && Number($("#e_cc_number").val()) ){
                errorMessage += "Credit Card Number is Invalid<br>";
                error = true;
                $("#e_cc_number").addClass("error");
            }
        }

        if($("#e_cc_type").val() == ""){
            errorMessage += "Credit Card Type is required<br>";
            error = true;
            $("#e_cc_type").addClass("error");
        }

        if($("#e_cc_month").val() == ""){
            errorMessage += "Credit Card Month is required<br>";
            error = true;
            $("#e_cc_month").addClass("error");
        }

        if($("#e_cc_year").val() == ""){
            errorMessage += "Credit Card Year is required<br>";
            error = true;
            $("#e_cc_year").addClass("error");
        }

        if($("#e_cc_cvv2").val() == ""){
            errorMessage += "Credit Card CVV is required<br>";
            error = true;
            $("#e_cc_cvv2").addClass("error");
        }

        if(error == true){
            $engine.notify(errorMessage);
            return false;
        }

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: BASE_PATH + 'application/ajax/accounts.php',
            data: {
                action : 'AddCards',
                AccountId : selectedShipper.id,
                Number : $("#e_cc_number").val(),
                FirstName : $("#e_cc_fname").val(),
                LastName : $("#e_cc_lname").val(),
                ExpiryMonth : $("#e_cc_month").val(),
                ExpiryYear : $("#e_cc_year").val(),
                CVV : $("#e_cc_cvv2").val(),
                Type : $("#e_cc_type").val(),
                Address : $("#e_cc_address").val(),
                City : $("#e_cc_city").val(),
                State : $("#e_cc_state").val(),
                Zipcode : $("#e_cc_zip").val()
            },
            success: function (res) {
                if(res.success){
                    
                    var cardType = ['other','visa','master','amex','discover'];

                    let html = `
                        <tr class="cc-row">
                            <td><input type="checkbox" value="${res.data.CardId}" onchange="setUsedCard(this)" class="selectedCard"></td>
                            <td>${res.data.Number}</td>
                            <td>${res.data.ExpiryMonth}/${res.data.ExpiryYear} ${res.data.CVV}</td>
                            <td>${cardType[res.data.Type]}</td>
                            <td>${res.data.FirstName}</td>
                            <td>${res.data.LastName}</td>
                            <td>${res.data.Address!="" ? res.data.Address+"," : ""} ${res.data.City!="" ? res.data.City+"," : ""} ${res.data.State!="" ? res.data.State : "" }<br/>${res.data.Zipcode}</td>
                            <td>${res.data.Created}</td>
                            <td>${res.data.Updated == null ? "" : res.data.Updated}</td>
                            <td>${res.data.Status == 1 ? "Active" : "In Active"}</td>
                        </tr>
                    `;

                    $(".cc-row-empty").remove();
                    $("#shipper-cards").append(html);

                    $("#add-card-modal").modal('hide');
                }
            }
        });
    }

    function autoComplete(address, type) {
        if(address.trim() != ""){
            $.ajax({
                type: 'POST',
                url: BASE_PATH + 'application/ajax/auto_complete.php',
                dataType: 'json',
                data: {
                    action: 'suggestions',
                    address: address
                },
                success: function (response) {
                    let result = response.result;
                    let html = ``;
                    let h = null;
                    let functionName = null;
                    if(type == 'pickup'){
                        h = document.getElementById("suggestions-box");
                        h.innerHTML = "";
                        functionName = 'applyAddressOrigin';
                        html += '<ul class="typeahead dropdown-menu" role="listbox" style="top: 36px; width:350px; left: 133px; display: block;">';
                        html += '<li><a href="javascript:void(0)" style="height:25px !important; padding-top:0px !important; font-size:10px;">Suggestions</a></li>';
                    }
                    if(type == 'shipper'){
                        h = document.getElementById("suggestions-box-shipper");
                        h.innerHTML = "";
                        functionName = 'applyAddressShipper';
                        html += '<ul class="typeahead dropdown-menu" role="listbox" style="top: 36px; width:350px; left: 133px; display: block;">';
                        html += '<li><a href="javascript:void(0)" style="height:25px !important; padding-top:0px!important; font-size:10px;">Suggestions</a></li>';
                    }
                    if(type == 'destination'){
                        h = document.getElementById("suggestions-box-destination");
                        h.innerHTML = "";
                        functionName = 'applyAddressDestination';
                        html += '<ul class="typeahead dropdown-menu" role="listbox" style="top: 36px; width:350px; left: 133px; display: block;">';
                        html += '<li><a href="javascript:void(0)" style="height:25px !important; padding-top:0px !important; font-size:10px;">Suggestions</a></li>';
                    }
                    if(type == 'cc'){
                        h = document.getElementById("suggestions-box-cc");
                        h.innerHTML = "";
                        functionName = 'applyAddressCC';
                        html += '<ul class="typeahead dropdown-menu" role="listbox" style="top: 36px; width:350px; left: 133px; display: block;">';
                        html += '<li><a href="javascript:void(0)" style="height:25px !important; padding-top:0px !important; font-size:10px;">Suggestions</a></li>';
                    }
                    result.forEach( (element, index) => {
                        let address = `<strong>${element.street}</strong>,<br>${element.city}, ${element.state} ${element.zip}`;
                        
                        html += `<li>
                                    <a class="dropdown-item" href="javascript:void(0)" onclick="${functionName}('${element.street}','${element.city}','${element.state}','${element.zip}')" role="option">
                                        <p>${address}</p>
                                    </a>
                                </li>`;
                    });
                    html += `<li>
                                <a href="javascript:void(0)" style="height: 29px !important;font-size:10px;padding: 0px !important;padding-left: 10px !important; padding-top:10px !important;">Powered by
                                    &nbsp;&nbsp;&nbsp;<img alt="Cargo Flare" src="https://cargoflare.com/styles/cargo_flare/logo.png" style="width:auto;">
                                </a>
                            </li>`;
                    html += `</ul>`;
                    h.innerHTML = html;
                }
            });
        }
    }
    
    function applyAddressOrigin(address, city, state, zip){
        $("#suggestions-box").html("");
        $("#origin_address1").val(address);
        $("#origin_city").val(city);
        $("#origin_state").val(state);
        $("#origin_zip").val(zip);
        document.getElementById("suggestions-box").innerHTML = "";
    }

    function applyAddressShipper (address, city, state, zip) {
        $("#suggestions-box").html("");
        $("#shipper_address").val(address);
        $("#shipper_city").val(city);
        $("#shipper_state").val(state);
        $("#shipper_zip").val(zip);
        document.getElementById("suggestions-box-shipper").innerHTML = "";
    }

    function applyAddressDestination (address, city, state, zip) {
        $("#suggestions-box").html("");
        $("#destination_address1").val(address);
        $("#destination_city").val(city);
        $("#destination_state").val(state);
        $("#destination_zip").val(zip);
        document.getElementById("suggestions-box-destination").innerHTML = "";
    }

    function applyAddressCC (address, city, state, zip) {
        $("#suggestions-box").html("");
        $("#e_cc_address").val(address);
        $("#e_cc_city").val(city);
        $("#e_cc_state").val(state);
        $("#e_cc_zip").val(zip);
        document.getElementById("suggestions-box-cc").innerHTML = "";
    }

    $(document).ready(function(){
        
        // address search API key
        let timer;
        const waitTime = 1000;
        document.querySelector('#origin_address1').addEventListener('keyup', (e) => {
            const text = e.currentTarget.value;
            clearTimeout(timer);
            timer = setTimeout(() => {
                autoComplete($("#origin_address1").val().trim(), 'pickup');
            }, waitTime);
        });
        document.querySelector('#shipper_address').addEventListener('keyup', (e) => {
            const text = e.currentTarget.value;
            clearTimeout(timer);
            timer = setTimeout(() => {
                autoComplete($("#shipper_address").val().trim(), 'shipper');
            }, waitTime);
        });
        document.querySelector('#destination_address1').addEventListener('keyup', (e) => {
            const text = e.currentTarget.value;
            clearTimeout(timer);
            timer = setTimeout(() => {
                autoComplete($("#destination_address1").val().trim(), 'destination');
            }, waitTime);
        });
        document.querySelector('#e_cc_address').addEventListener('keyup', (e) => {
            const text = e.currentTarget.value;
            clearTimeout(timer);
            timer = setTimeout(() => {
                autoComplete($("#e_cc_address").val().trim(), 'cc');
            }, waitTime);
        });
    });
</script>