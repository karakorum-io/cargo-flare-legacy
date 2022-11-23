<?php
/**
 * @version		1.0
 * @since		26.09.12
 * @author		Oleg Ilyushyn, C.A.W., Inc. dba INTECHCENTER
 * @address		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * @email		techsupport@intechcenter.com
 * @copyright	2012 Intechcenter. All Rights Reserved
 */
?>
<script type="text/javascript">
    function applySearch(num) {
        var acc_obj = acc_data[num];
        switch (acc_type) {
            case <?=Account::TYPE_CARRIER?>:
                $("#carrier_contact").val(acc_obj.contact_name1);
                $("#carrier_type").val(acc_obj.carrier_type);
                $("#carrier_company").val(acc_obj.company_name);
                $("#carrier_phone1").val(acc_obj.phone1);
                $("#carrier_phone2").val(acc_obj.phone2);
                $("#carrier_fax").val(acc_obj.fax);
                $("#carrier_cell").val(acc_obj.cell);
                $("#carrier_address").val($.trim(acc_obj.address1 + " " + acc_obj.address2));
                $("#carrier_print_name").val(acc_obj.print_name);
                $("#carrier_insurance_iccmcnumber").val(acc_obj.insurance_iccmcnumber);
                $("#carrier_city").val(acc_obj.city);
                $("#carrier_state").val(acc_obj.state);
                $("#carrier_zip").val(acc_obj.zip);
                $("#carrier_country").val(acc_obj.country);
                $("#carrier_email").val(acc_obj.email);
                $("#carrier_id").val(acc_obj.member_id);
                $("#account_id").val(acc_obj.id);
                break;
        }
    }

    function dispatch() {
		<?php if($_GET['orders'] == 'show') { ?>
		var entity_id = <?=$_GET['id']?>;
		<?php } else { ?>
        if ($("input[name='order_id']:checked").size() == 0) {
            $(".alert-message").empty();
			$(".alert-message").text("Select Order to Dispatch");
			$(".alert-pack").show();
			return false;        
        }
		if ($("input[name='order_id']:checked").size() > 1) {
            $(".alert-message").empty();
			$(".alert-message").text("Error: You may dispatch one order at a time.");
			$(".alert-pack").show();
			return false;        
        }
        $("#dispatch_dialog .msg-error").hide();
        $("#dispatch_form").each(function(){
            this.reset();
        });
        var entity_id = $("input[name='order_id']:checked").val();
		<?php } ?>
        $("body").nimbleLoader('show');
        $.ajax({
            type: 'POST',
            url: '<?=SITE_IN;?>application/ajax/entities.php',
            dataType: 'json',
            data: {
                action: 'getDispatchData',
                entity_id: entity_id
            },
            complete: function(response) {
                $("body").nimbleLoader('hide');
            },
            success: function(response) {
                if (response.success) {
                    $("#order_load_date").val(response.data.load_date);
                    $("#order_load_date_type").val(response.data.load_date_type);
                    $("#order_delivery_date").val(response.data.delivery_date);
                    $("#order_delivery_date_type").val(response.data.delivery_date_type);
                    $("#order_ship_via").val(response.data.ship_via);
//                    $("#order_vehicles_run").val(response.data.vehicles_run);
                    //$("#order_price_fb").val(response.data.price_fb);
                    $("#order_carrier_pay").val(response.data.carrier_pay);
                    $("#order_carrier_ondelivery").val(response.data.carrier_ondelivery);
                    $("#order_company_owes_carrier").val(response.data.company_owes_carrier);
                    //$("#order_booking_number").val(response.data.booking_number);
                    //$("#order_buyer_number").val(response.data.buyer_number);

                    $("#order_pickup_terminal_fee").val(response.data.pickup_terminal_fee);
                    $("#order_dropoff_terminal_fee").val(response.data.dropoff_terminal_fee);
                    $("#order_balance_paid_by").val(response.data.balance_paid_by);

					$('#order_include_shipper_comment').prop('checked', response.data.include_shipper_comment == '1');

	                $('#order_notes_from_shipper').val(response.data.notes_from_shipper);
					$('#payments_terms').val(response.data.payments_terms);
					

                    $("#pickup_name").val(response.data.pickup_name);
                    $("#pickup_company").val(response.data.pickup_company);
                    $("#pickup_phone1").val(response.data.pickup_phone1);
                    $("#pickup_phone2").val(response.data.pickup_phone2);
                    $("#pickup_cell").val(response.data.pickup_phone_cell);
                    $("#pickup_address1").val(response.data.pickup_address1);
                    $("#pickup_address2").val(response.data.pickup_address2);
                    $("#pickup_city").val(response.data.pickup_city);
                    $("#pickup_state").val(response.data.pickup_state);
                    $("#pickup_zip").val(response.data.pickup_zip);
                    $("#pickup_country").val(response.data.pickup_country);
					$("#from_booking_number").val(response.data.from_booking_number);
                    $("#from_buyer_number").val(response.data.from_buyer_number);

                    $("#deliver_name").val(response.data.deliver_name);
                    $("#deliver_company").val(response.data.deliver_company);
                    $("#deliver_phone1").val(response.data.deliver_phone1);
                    $("#deliver_phone2").val(response.data.deliver_phone2);
                    $("#deliver_cell").val(response.data.deliver_phone_cell);
                    $("#deliver_address1").val(response.data.deliver_address1);
                    $("#deliver_address2").val(response.data.deliver_address2);
                    $("#deliver_city").val(response.data.deliver_city);
                    $("#deliver_state").val(response.data.deliver_state);
                    $("#deliver_zip").val(response.data.deliver_zip);
                    $("#deliver_country").val(response.data.deliver_country);
					$("#to_booking_number").val(response.data.to_booking_number);
                    $("#to_buyer_number").val(response.data.to_buyer_number);
					
					$("#entity_number").html(response.data.entity_number);

                    $("#dispatch_dialog").dialog({
                        title: 'Dispatch Form',
                        dialogClass: 'dispatch_form_dialog',
                        modal: true,
                        resizable: false,
                        draggable: true,
                        width: 800,
                        buttons: [{
                            text: 'Cancel',
                            click: function(){
                                $(this).dialog('close');
                            }
                        },{
                            text: 'Dispatch',
                            click: function(){
                                var dispatchValues = $("#dispatch_form").serializeArray();
                                dispatchValues.push({'name': 'action', 'value': 'dispatch'});
                                dispatchValues.push({'name': 'entity_id', 'value': entity_id});
                                $(".dispatch_form_dialog").nimbleLoader('show');
                                $.ajax({
                                    type: 'POST',
                                    url: '<?=SITE_IN?>application/ajax/entities.php',
                                    dataType: 'json',
                                    data: dispatchValues,
                                    success: function(response) {
                                        if (response.success) {
                                            document.location.reload();
                                        } else {
                                            if (response.errors != undefined) {
                                                $("#dispatch_dialog .msg-list").html('');
                                                for(i in response.errors) {
                                                    $("#dispatch_dialog .msg-list").append('<li>'+response.errors[i]+'</li>');
                                                }
                                                $("#dispatch_dialog .msg-error").show();
                                                $("body").scrollTop(0);
                                            } else {
                                                alert("Can't save dispatch data. Try again later, please");
                                            }
                                        }
                                    },
                                    error: function(response) {
                                        alert("Can't save dispatch data. Try again later, please");
                                    },
                                    complete: function(response) {
                                        $(".dispatch_form_dialog").nimbleLoader('hide');
                                    }
                                });
                            }
                        }]
                    }).dialog('open');
                } else {
                    alert("Can't load Order data. Try again later, please");
                }
            },
            error: function(response) {
                alert("Can't load Order data. Try again later, please");
            }
        });
    }

    $(document).ready(function(){
        $("#order_load_date, #order_delivery_date").datepicker({
            dateFormat: 'mm/dd/yy',
            minDate: '+0'
        });
        $("#carrier_company").change(function() {
            if ($("#account_id").val() != "") {
                if (confirm("Carrier information will be unlinked from selected Carrier's FD account.")) {
                    $("#carrier_id").val("");
                    $("#account_id").val("");
                }
            }
        });
		
		var createForm = $('#dispatch_form');
		
		createForm.find("#carrier_company").typeahead({
			source: function(request, result) {
				$.ajax({
					url: '<?= SITE_IN ?>application/ajax/autocomplete.php',
					type: 'GET',
					dataType: 'json',
					data: {
						term: request.term,
						action: 'getCarrier'
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
			select: function( event, ui ) {
				$("#carrier_company" ).val( ui.item.company_name);
				$("#carrier_id").val(ui.item.member_id);
				$("#account_id").val(ui.item.id);
				
				            $("#carrier_contact").val(ui.item.contact_name1);
							$("#carrier_type").val(ui.item.carrier_type);
							$("#carrier_phone1").val(ui.item.phone1);
							$("#carrier_phone2").val(ui.item.phone2);
							$("#carrier_fax").val(ui.item.fax);
							$("#carrier_cell").val(ui.item.cell);
							$("#carrier_address").val($.trim(ui.item.address1 + " " + ui.item.address2));
							$("#carrier_print_name").val(ui.item.print_name);
							$("#carrier_insurance_iccmcnumber").val(ui.item.insurance_iccmcnumber);
							$("#carrier_city").val(ui.item.city);
							$("#carrier_state").val(ui.item.state);
							$("#carrier_zip").val(ui.item.zip);
							$("#carrier_country").val(ui.item.country);
							$("#carrier_email").val(ui.item.email);
				        
				return false;
			 }
		
		});
		
		$('#acc_search_string').keydown(function(e) {
		   var key = e.which;
		   
			if (key == 13) {
			// As ASCII code for ENTER key is "13"
			    accountSearch();
			}
		});
		
    });
</script>
<div id="dispatch_dialog" style="display: none;">
    <div class="msg-error" onclick="$('#dispatch_dialog .msg-error').hide();" style="display: none"><ul class="msg-list"></ul></div>
    <form id="dispatch_form">
        <div><b>ID: <span id="entity_number" class="black"></span></b></div>
        <input type="hidden" name="carrier_id" id="carrier_id" value=""/>
        <input type="hidden" name="account_id" id="account_id" value=""/>
        <div class="dispatch-info" style="float: none;">
            <p class="block-title">Carrier Information</p>
            <div>
                <table cellspacing="0" cellpadding="0" border="0" class="form-table">
                    <tr>
                        <td>@carrier_company@</td>
                        <td>
                            <label for="carrier_type"><span class="required">*</span>Carrier Type:</label>
                        </td>
                        <td>
                            <table cellpadding="0" cellspacing="0" style="padding-left:0px;">
                                <tr>
                                    <td style="padding-left: 0px;">@carrier_type@</td>
                                    <td><?=functionButton('Search Carrier', 'selectCarrier()');?></td>
                                    <td><input type="checkbox" name="save_carrier" id="save_carrier" /> <label for="save_carrier">Save</label></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>@carrier_print_name@</td>
                        <td>@carrier_contact@</td>
                    </tr>
                    <tr>
                        <td>@carrier_insurance_iccmcnumber@</td>
                        <td>@carrier_phone1@</td>
                    </tr>
                    <tr>
                        <td>@carrier_address@</td>
                        <td>@carrier_phone2@</td>
                    </tr>
                    <tr>
                        <td>@carrier_city@</td>
                        <td>@carrier_fax@</td>
                    </tr>
                    <tr>
                        <td>@carrier_state@@carrier_zip@</td>
                        <td>@carrier_cell@</td>
                    </tr>
                    <tr>
                        <td>@carrier_country@</td>
                        <td>@carrier_driver@</td>
                    </tr>
                    <tr>
                        <td>@carrier_email@</td>
                        <td>@carrier_driver_phone@</td>
                    </tr>
                </table>
            </div>
        </div>
        <br/>
        <div class="dispatch-info" style="float: none;">
            <p class="block-title">Order Information</p>
            <div>
                <table cellpadding="0" cellspacing="0" border="0" class="form-table">
                    <tr>
                        <td>@order_load_date_type@ @order_load_date@</td>
                        <!--<td>@order_price_fb@</td>-->
                        <td>@order_company_owes_carrier@</td>
                    </tr>
                    <tr>
                        <td>@order_delivery_date_type@ @order_delivery_date@</td>
                        <td>@order_carrier_ondelivery@</td>
                    </tr>
                    <tr>
                        <td>@order_ship_via@</td>
                        <td>@order_carrier_pay@</td>
                    </tr>
                    <tr>
                        <!--<td>@order_vehicles_run@</td>-->
                        
                        <td>@order_balance_paid_by@</td>
                        <!--td>@order_booking_number@</td-->
                        <td>@order_pickup_terminal_fee@</td>
                    </tr>
                    <tr>
                        
                        <td>@payments_terms@</td>
                        <td  valign='top'>@order_dropoff_terminal_fee@</td>
                    </tr>
                    <tr>
                        <td >@order_notes_from_shipper@</td>
                        <td></td>
                    </tr>
	                <tr>
		                <td>&#8203;</td>
		                <td>@order_include_shipper_comment@</td>
		                <td colspan="2">&#8203;</td>
	                </tr>
					<tr>
		                <td></td>
		                <td>&#8203;</td>
		                <td colspan="2">&#8203;</td>
	                </tr>
					
                </table>
            </div>
        </div>
        <br/>
        <div class="dispatch-info" style="float:left;width: 355px;">
            <p class="block-title">Pickup From</p>
            <div>
                <table cellpadding="0" cellspacing="0" border="0" class="form-table">
                    <tr>
                        <td>@pickup_name@</td>
                    </tr>
                    <tr>
                        <td>@pickup_company@</td>
                    </tr>
                    <tr>
                        <td>@pickup_address1@</td>
                    </tr>
                    <tr>
                        <td>@pickup_address2@</td>
                    </tr>
                    <tr>
                        <td>@pickup_city@</td>
                    </tr>
                    <tr>
                        <td>@pickup_state@@pickup_zip@</td>
                    </tr>
                    <tr>
                        <td>@pickup_country@</td>
                    </tr>
                    <tr>
                        <td>@pickup_phone1@</td>
                    </tr>
                    <tr>
                        <td>@pickup_phone2@</td>
                    </tr>
                    <tr>
                        <td>@pickup_cell@</td>
                    </tr>
                    <tr>
                        <td>@from_booking_number@</td>
                    </tr>
                    <tr>
                        <td>@from_buyer_number@</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="dispatch-info" style="float:right;width: 355px;">
            <p class="block-title">Deliver To</p>
            <div>
                <table cellpadding="0" cellspacing="0" border="0" class="form-table">
                    <tr>
                        <td>@deliver_name@</td>
                    </tr>
                    <tr>
                        <td>@deliver_company@</td>
                    </tr>
                    <tr>
                        <td>@deliver_address1@</td>
                    </tr>
                    <tr>
                        <td>@deliver_address2@</td>
                    </tr>
                    <tr>
                        <td>@deliver_city@</td>
                    </tr>
                    <tr>
                        <td>@deliver_state@@deliver_zip@</td>
                    </tr>
                    <tr>
                        <td>@deliver_country@</td>
                    </tr>
                    <tr>
                        <td>@deliver_phone1@</td>
                    </tr>
                    <tr>
                        <td>@deliver_phone2@</td>
                    </tr>
                    <tr>
                        <td>@deliver_cell@</td>
                    </tr>
                    <tr>
                        <td>@to_booking_number@</td>
                    </tr>
                    <tr>
                        <td>@to_buyer_number@</td>
                    </tr>
                </table>
            </div>
        </div>
    </form>
</div>