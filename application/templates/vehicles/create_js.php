
<?php include "core.js";?>
<script type="text/javascript">
<?php if (isset($_POST['year'])) {?>
        var v = <?=count($_POST['year'])?>;
<?php } else {?>
        var v = 0;
<?php }?>
    var total_tariff = 0;
    var total_deposit = 0;

    function saveVehicle(id) {

        var error = "";
        var vehicleForm = $('#vehicle_form');
        vehicleForm.find("input").each(function () {
            $(this).val($.trim($(this).val()));
            if (($(this).val() == "" || $(this).val() == 0) && !in_array($(this).attr("name"), ['vin', 'lot', 'state', 'plate', 'color','vehiclePopupId'], undefined)) {
                error += "<p>" + $(this).attr("name") + " value required.</p>";
            }
        });
        if (error != "") {
            vehicleForm.find(".error").html(error).slideDown().delay(3000).slideUp();
        } else {
            if (id == null) {
                v++;
                id = v;
            }
            var carrier = ((isNaN(parseFloat($("#vehicle_form input[name='carrier_pay']").val())) ? 0 : parseFloat($("#vehicle_form input[name='carrier_pay']").val())) - (isNaN(parseFloat($("#vehicle_form input[name='deposit']").val())) ? 0 : parseFloat($("#vehicle_form input[name='deposit']").val())));
            var vehicle_row_body = '<tr id="added-veh-'+ id +'" class="grid-body" rel="' + id + '"><td class="grid-body-left" align="center">'
                    + '<input type="hidden" id="year'+id+'" name="year[]" value="' + $("#vehicle_form input[name='year']").val() + '"/>'
                    + $("#vehicle_form input[name='year']").val() + '</td><td align="center">'
                    + '<input type="hidden" id="make'+id+'" name="make[]" value="' + $("#vehicle_form input[name='make']").val() + '"/>'
                    + $("#vehicle_form input[name='make']").val() + '</td><td align="center">'
                    + '<input type="hidden" id="model'+id+'" name="model[]" value="' + $("#vehicle_form input[name='model']").val() + '"/>'
                    + $("#vehicle_form input[name='model']").val() + '</td><td align="center">'
                    + '<input type="hidden" name="type[]" value="' + $("#vehicle_form input[name='type']").val() + '"/>'
                    + $("#vehicle_form input[name='type']").val() + '</td><td align="center">'
                    + '<input type="hidden" name="lot[]" value="' + $("#vehicle_form input[name='lot']").val() + '"/>'
                    + '<input type="hidden" name="inop[]" value="' + $("#vehicle_form select[name='inop']").val() + '"/>'
                    + '<input type="hidden" name="plate[]" value="' + $("#vehicle_form input[name='plate']").val() + '"/>'
                    + '<input type="hidden" name="state[]" value="' + $("#vehicle_form input[name='state']").val() + '"/>'
                    + '<input type="hidden" name="color[]" value="' + $("#vehicle_form input[name='color']").val() + '"/>'
                    + '<input type="text" name="vin[]" value="' + $("#vehicle_form input[name='vin']").val() + '"/>'
                    + '</td><td align="center">'
                    + '<input type="text" id="tariff'+id+'" name="tariff[]" value="' + $("#vehicle_form input[name='carrier_pay']").val() + '"  onkeyup="updatePricingInfo();"/>'
                    + '<input type="hidden" name="carrier_pay[]" value="' + carrier + '" />'
                    + '</td><td align="center">'
                    + '<input type="text" id="deposite'+id+'" name="deposit[]" value="' + $("#vehicle_form input[name='deposit']").val() + '"  onkeyup="updatePricingInfo();"/>'
                    + '</td>'
                    + '<td align="center" class="grid-body-right">'
                    + '<img src="' + BASE_PATH + 'images/additionals/aq.png" width="20" alt="Auto Quote" title="Auto Quote" onclick="AutoQuoteIndividualFromGrid(' + id + ')" class="action-icon"/>'
                    + '<img src="' + BASE_PATH + 'images/icons/copy.png" alt="Copy" title="Copy" onclick="copyVehicle(' + id + ')" class="action-icon"/>'
                    + '<img src="' + BASE_PATH + 'images/icons/edit.png" alt="Edit" title="Edit" onclick="editVehicle(' + id + ')" class="action-icon"/>'
                    + '<img src="' + BASE_PATH + 'images/icons/delete.png" alt="Delete" title="Delete" onclick="deleteVehicle(' + id + ')" class="action-icon"/></td></tr>';
            if ($("#vehicles-grid tbody tr[rel='" + id + "']").length > 0) {
                $("#vehicles-grid tbody tr[rel='" + id + "']").replaceWith(vehicle_row_body);
            } else {
                $("#vehicles-grid tbody").append(vehicle_row_body);
            }

            updatePricingInfo();
            zebra();
            $("#vehicle_form").modal("hide");
        }
    }
    function editVehicle(id) {
        var vehicleForm = $('#vehicle_form');
        var vehiclesGridRow = $('#vehicles-grid').find("tbody tr[rel='" + id + "']");
        vehicleForm.find(" input").val("");
        vehicleForm.find("input[name='year']").val(vehiclesGridRow.find("input[name='year[]']").val());
        vehicleForm.find("input[name='make']").val(vehiclesGridRow.find("input[name='make[]']").val());
        vehicleForm.find("input[name='model']").val(vehiclesGridRow.find("input[name='model[]']").val());
        vehicleForm.find("input[name='type']").val(vehiclesGridRow.find("input[name='type[]']").val());
        vehicleForm.find("input[name='lot']").val(vehiclesGridRow.find("input[name='lot[]']").val());
        vehicleForm.find("input[name='vin']").val(vehiclesGridRow.find("input[name='vin[]']").val());
        vehicleForm.find("input[name='state']").val(vehiclesGridRow.find("input[name='state[]']").val());
        vehicleForm.find("input[name='plate']").val(vehiclesGridRow.find("input[name='plate[]']").val());
        vehicleForm.find("input[name='color']").val(vehiclesGridRow.find("input[name='color[]']").val());
        vehicleForm.find("select[name='inop']").val(vehiclesGridRow.find("select[name='inop[]']").val());
        vehicleForm.find("input[name='carrier_pay']").val(vehiclesGridRow.find("input[name='tariff[]']").val());
        vehicleForm.find("input[name='deposit']").val(vehiclesGridRow.find("input[name='deposit[]']").val());
        $('#vehicle_form').find('#vehicle_form_model').text('Edit Vehicle');
	$("#vehicleActionButton").attr('onclick','saveVehicle('+id+')');
        //$("#vehiclePopupButton").attr("onclick","updateVehicle("+id+")");
       $("#vehicle_form").modal();


    }

    /**
    * Function created to add vehicle and is modified by Chetu Inc. for adding
    * auto auoting functionality
    *
    * @author Chetu Inc.
    * @version 1.1
    * @requires void
    **/
    function addVehicle() {
        var vehicleForm = $("#vehicle_form");
        vehicleForm.find("input, select").val("");
        vehicleForm.find("input[name='carrier_pay']").val('0');
        vehicleForm.find("input[name='deposit']").val('0');
        $("#vehiclePopupButton").attr("onclick","saveVehicle()");
        $("#vehicle_form").modal({
            backdrop: 'static'
        })
    }

    function deleteVehicle(id) {
        $("#vehicles-grid").find("tbody tr[rel='" + id + "']").remove();
        updatePricingInfo();
        zebra();
    }

    function updateVehicle(id) {

        var error = "";
        var vehicleForm = $('#vehicle_form');
        vehicleForm.find("input").each(function () {
            $(this).val($.trim($(this).val()));
            if (($(this).val() == "" || $(this).val() == 0) && !in_array($(this).attr("name"), ['vin', 'lot', 'state', 'plate', 'color','vehiclePopupId'], undefined)) {
                error += "<p>" + $(this).attr("name") + " value required.</p>";
            }
        });
        if (error != "") {
            vehicleForm.find(".error").html(error).slideDown().delay(3000).slideUp();
        } else {
        var vehicleForm = $("#vehicle_form");
        var carrier = ((isNaN(parseFloat($("#vehicle_form input[name='carrier_pay']").val())) ? 0 : parseFloat($("#vehicle_form input[name='carrier_pay']").val())) - (isNaN(parseFloat($("#vehicle_form input[name='deposit']").val())) ? 0 : parseFloat($("#vehicle_form input[name='deposit']").val())));
        var vehicle_row_body = '<td class="grid-body-left" align="center">'
                + '<input type="hidden" id="year'+id+'" name="year[]" value="' + $("#vehicle_form input[name='year']").val() + '"/>'
                + $("#vehicle_form input[name='year']").val() + '</td><td align="center">'
                + '<input type="hidden" id="make'+id+'" name="make[]" value="' + $("#vehicle_form input[name='make']").val() + '"/>'
                + $("#vehicle_form input[name='make']").val() + '</td><td align="center">'
                + '<input type="hidden" id="model'+id+'" name="model[]" value="' + $("#vehicle_form input[name='model']").val() + '"/>'
                + $("#vehicle_form input[name='model']").val() + '</td><td align="center">'
                + '<input type="hidden" name="type[]" value="' + $("#vehicle_form input[name='type']").val() + '"/>'
                + $("#vehicle_form input[name='type']").val() + '</td><td align="center">'
                + '<input type="hidden" name="lot[]" value="' + $("#vehicle_form input[name='lot']").val() + '"/>'
                + '<input type="hidden" name="inop[]" value="' + $("#vehicle_form select[name='inop']").val() + '"/>'
                + '<input type="hidden" name="plate[]" value="' + $("#vehicle_form input[name='plate']").val() + '"/>'
                + '<input type="hidden" name="state[]" value="' + $("#vehicle_form input[name='state']").val() + '"/>'
                + '<input type="hidden" name="color[]" value="' + $("#vehicle_form input[name='color']").val() + '"/>'
                + '<input type="text" name="vin[]" value="' + $("#vehicle_form input[name='vin']").val() + '"/>'
                + '</td><td align="center">'
                + '<input type="text" id="tariff'+id+'" name="tariff[]" value="' + $("#vehicle_form input[name='carrier_pay']").val() + '"  onkeyup="updatePricingInfo();"/>'
                + '<input type="hidden" name="carrier_pay[]" value="' + carrier + '" />'
                + '</td><td align="center">'
                + '<input type="text" id="deposite'+id+'" name="deposit[]" value="' + $("#vehicle_form input[name='deposit']").val() + '"  onkeyup="updatePricingInfo();"/>'
                + '</td>'
                + '<td align="center" class="grid-body-right">'
                + '<img src="' + BASE_PATH + 'images/additionals/aq.png" width="20" alt="Auto Quote" title="Auto Quote" onclick="AutoQuoteIndividualFromGrid(' + id + ')" class="action-icon"/>'
                + '<img src="' + BASE_PATH + 'images/icons/copy.png" alt="Copy" title="Copy" onclick="copyVehicle(' + id + ')" class="action-icon"/>'
                + '<img src="' + BASE_PATH + 'images/icons/edit.png" alt="Edit" title="Edit" onclick="editVehicle(' + id + ')" class="action-icon"/>'
                + '<img src="' + BASE_PATH + 'images/icons/delete.png" alt="Delete" title="Delete" onclick="deleteVehicle(' + id + ')" class="action-icon"/></td>';
        
            $("#added-veh-" + id).html(vehicle_row_body);
            $("#vehicle_form").modal("hide");

    
        }
    }


    function updatePricingInfo() {
        var vehiclesGrid = $("#vehicles-grid");
        var carrier_pay = 0;
        var total_deposit = 0;

        /*
         var delivery_terminal_fee = parseFloat($("#delivery_terminal_fee").val());
         if (isNaN(delivery_terminal_fee)) delivery_terminal_fee = 0;
         var pickup_terminal_fee = parseFloat($("#pickup_terminal_fee").val());
         if (isNaN(pickup_terminal_fee)) pickup_terminal_fee = 0;
         vehiclesGrid.find("input[name='carrier_pay[]']").each(function() {
         carrier_pay += (isNaN(parseFloat($(this).val()))?0:parseFloat($(this).val()));
         });
         vehiclesGrid.find("input[name='deposit[]']").each(function() {
         total_deposit += (isNaN(parseFloat($(this).val()))?0:parseFloat($(this).val()));
         });
         var total_tariff = carrier_pay + total_deposit + delivery_terminal_fee + pickup_terminal_fee;
         */

        var total_tariff = 0;
        var delivery_terminal_fee = parseFloat($("#delivery_terminal_fee").val());
        if (isNaN(delivery_terminal_fee))
            delivery_terminal_fee = 0;
        var pickup_terminal_fee = parseFloat($("#pickup_terminal_fee").val());
        if (isNaN(pickup_terminal_fee))
            pickup_terminal_fee = 0;
        vehiclesGrid.find("input[name='tariff[]']").each(function () {
            //carrier_pay += (isNaN(parseFloat($(this).val()))?0:parseFloat($(this).val()));
            total_tariff += (isNaN(parseFloat($(this).val())) ? 0 : parseFloat($(this).val()));

        });
        vehiclesGrid.find("input[name='deposit[]']").each(function () {
            total_deposit += (isNaN(parseFloat($(this).val())) ? 0 : parseFloat($(this).val()));
        });

        carrier_pay = total_tariff - total_deposit;
        //var total_tariff = carrier_pay + total_deposit + delivery_terminal_fee + pickup_terminal_fee;
        total_tariff = total_tariff + delivery_terminal_fee + pickup_terminal_fee;

        $("#total_tariff").text('$ ' + total_tariff.toFixed(2));
        $("#total_deposit").text('$ ' + total_deposit.toFixed(2));
        $("#carrier_pay").text('$ ' + carrier_pay.toFixed(2));
        zebra();
    }



    function copyVehicle(id) {
        //var vehicleForm = $('#vehicle_form');
        //alert(id);
        var vehiclesGridRow = $('#vehicles-grid').find("tbody tr[rel='" + id + "']");

        v++;
        id = v;
        var carrier = ((isNaN(parseFloat(vehiclesGridRow.find("input[name='carrier_pay[]']").val())) ? 0 : parseFloat(vehiclesGridRow.find("input[name='carrier_pay[]']").val())) - (isNaN(parseFloat(vehiclesGridRow.find("input[name='deposit[]']").val())) ? 0 : parseFloat(vehiclesGridRow.find("input[name='deposit[]']").val())));

        var vehicle_row_body = '<tr class="grid-body" rel="' + id + '"><td class="grid-body-left" align="center">'
        + '<input type="hidden" name="year[]" id="year'+id+'" value="' + vehiclesGridRow.find("input[name='year[]']").val() + '"/>'
        + vehiclesGridRow.find("input[name='year[]']").val() + '</td><td align="center">'
        + '<input type="hidden" id="make'+id+'" name="make[]" value="' + vehiclesGridRow.find("input[name='make[]']").val() + '"/>'
        + vehiclesGridRow.find("input[name='make[]']").val() + '</td><td align="center">'
        + '<input type="hidden" id="model'+id+'" name="model[]" value="' + vehiclesGridRow.find("input[name='model[]']").val() + '"/>'
        + vehiclesGridRow.find("input[name='model[]']").val() + '</td><td align="center">'
        + '<input type="hidden" name="type[]" value="' + vehiclesGridRow.find("input[name='type[]']").val() + '"/>'
        + vehiclesGridRow.find("input[name='type[]']").val() + '</td><td align="center">'
        + '<input type="hidden" name="lot[]" value="' + vehiclesGridRow.find("input[name='lot[]']").val() + '"/>'
        + ''
        + '<input type="hidden" name="plate[]" value="' + vehiclesGridRow.find("input[name='plate[]']").val() + '"/>'
        + '<input type="hidden" name="state[]" value="' + vehiclesGridRow.find("input[name='state[]']").val() + '"/>'
        + '<input type="hidden" name="color[]" value="' + vehiclesGridRow.find("input[name='color[]']").val() + '"/>'
        + '<input type="hidden" name="inop[]" value="' + vehiclesGridRow.find("select[name='inop[]']").val() + '"/>'
        + ''
        + '<input type="text" name="vin[]" value="' + vehiclesGridRow.find("input[name='vin[]']").val() + '"/>'
        + '</td><td align="center">'
        + '<input type="text" id="tariff'+id+'" name="tariff[]" value="' + vehiclesGridRow.find("input[name='tariff[]']").val() + '" onkeyup="updatePricingInfo();"/>'
        + '<input type="hidden" name="carrier_pay[]" value="' + vehiclesGridRow.find("input[name='carrier_pay[]']").val() + '" />'
        + '</td>   <td align="center">'
        + '<input type="text"id="deposite'+id+'"  name="deposit[]" value="' + vehiclesGridRow.find("input[name='deposit[]']").val() + '" onkeyup="updatePricingInfo();"/>'
        + '</td>'
        + '<td align="center" class="grid-body-right">\n\
        <img src="' + BASE_PATH + 'images/additionals/aq.png" width="20" alt="Auto Quote" title="Auto Quote" onclick="AutoQuoteIndividualFromGrid(' + id + ')" class="action-icon"/>'
        + '<img src="' + BASE_PATH + 'images/icons/copy.png" alt="Copy" title="Copy" onclick="copyVehicle(' + id + ')" class="action-icon"/>'
        + '<img src="' + BASE_PATH + 'images/icons/edit.png" alt="Edit" title="Edit" onclick="editVehicle(' + id + ')" class="action-icon"/>'
        + '<img src="' + BASE_PATH + 'images/icons/delete.png" alt="Delete" title="Delete" onclick="deleteVehicle(' + id + ')" class="action-icon"/></td></tr>';

        /*if ($("#vehicles-grid tbody tr[rel='"+id+"']").size() > 0) {
         $("#vehicles-grid tbody tr[rel='"+id+"']").replaceWith(vehicle_row_body);
         } else {
         $("#vehicles-grid tbody").append(vehicle_row_body);
         }
         */
        $("#vehicles-grid tbody").append(vehicle_row_body);
        updatePricingInfo();
        zebra();
    }
</script>
