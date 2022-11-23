function formatPhoneNumber(s) {
    var s2 = ("" + s).replace(/\D/g, ''); 
    var m = s2.match(/^(\d{3})(\d{3})(\d{4})$/); 
    return (!m) ? null : "" + m[1] + "-" + m[2] + "-" + m[3]; 
}

function applySearch(num) {
    var acc_obj = acc_data.shipper_data[num];
    switch (acc_type) {
        case 2:
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
            $("#shipper_country").val(acc_obj.country);
            if (acc_obj.referred_by != '') {
                $("#referred_by").val(acc_obj.referred_by);
                $('#referred_by').prop('disabled', true);
                $("#referred_by").empty(); // remove old options
                $("#referred_by").append($("<option></option>").attr("value", acc_obj.referred_id).text(acc_obj.referred_by));
            }
            if (acc_obj.country == "US") {
                $("#shipper_state").val(acc_obj.state);
            } else {
                $("#shipper_state2").val(acc_obj.state);
            }
            $("#shipper_zip").val(acc_obj.zip_code);
            break;
    }
}

function applySearchLeads(num) {
    //console.log("Search Apply Leads Tab Remote function");
    var acc_obj = acc_data.shipper_leads_data[num];
    switch (acc_type) {
        case 2:

            $('#select-shipper-block').hide();
            $('#shipperDiv').show();
            $('#update_shipper_info').show();
            $('#save_shipper_info').hide();

            //$('#update_shipper').attr('checked', 'checked');
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
                //$("#referred_by").val(acc_obj.referred_by);
                //$('#referred_by').prop('disabled', true);
                $("#referred_by").empty(); // remove old options
                //$("#referred_by").append($("<option></option>").attr("value", acc_obj.referred_id).text(acc_obj.referred_by));
            }
            $("#account_payble_contact").val(acc_obj.account_payble_contact);
            //typeselected();
            break;
    }
}

function setLocationSameAsShipper(location) {
    if (confirm("Are you sure you want to overwrite location information?")) {
        $("input[name='" + location + "_city']").val($("input[name='shipper_city']").val());
        $("select[name='" + location + "_state']").val($("select[name='shipper_state']").val());
        $("input[name='" + location + "_zip']").val($("input[name='shipper_zip']").val());
        $("select[name='" + location + "_country']").val($("select[name='shipper_country']").val());
    }
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
        shipping_est_date: $('#shipping_est_date').val(),
        shipping_ship_via: $('#shipping_ship_via').val(),
        vehicles: []
    };
    $('input[name="type[]"]').each(function () {
        data.vehicles.push($(this).val());
    });
    if (data.vehicles.length == 0) {
        alert('No vehicles for quote');
        return;
    }
    if (data.origin_city == '' || data.origin_state == '' || data.origin_zip == '') {
        alert('Invalid Origin Information');
        return;
    }
    if (data.destination_city == '' || data.destination_state == '' || data.destination_zip == '') {
        alert('Invalid Destination Information');
        return;
    }
    if (data.shipping_est_date == '') {
        alert('Invalid Shipping Date');
        return;
    }
    if (data.shipping_ship_via == '') {
        alert('You should specify "Ship Via" field');
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
                alert(quoted + ' vehicles quoted.');
            });
            updatePricingInfo();
        },
        error: function () {
            alert('Failed to calculate Quick Price');
        },
        complete: function () {
            $("body").nimbleLoader("hide");
        }
    });
}