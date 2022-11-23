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

	var busy = false;
    var interval_disp = 0;
function updateInternalNotes(data) {

		var rows = "";

		for (i in data) {

			

			var email = data[i].email;

			var contactname = data[i].sender;

			

			if(data[i].system_admin == 1){

			     email = "admin@freightdragon.com";

				 contactname = "FreightDragon";

			   }

			if ((data[i].access_notes == 0 )   

				    || data[i].access_notes == 1

					|| data[i].access_notes == 2

					)

			{

			rows += '<tr class="grid-body"><td class="grid-body-left">'+data[i].created+'</td><td id="note_'+data[i].id+'_text">'+decodeURIComponent(data[i].text)+'</td><td>';

			rows += '<a href="mailto:'+email+'">'+contactname+'</a></td><td style="white-space: nowrap;" class="grid-body-right">';

			

			<?php //if (!$this->entity->readonly) : ?>

			

				if ((data[i].access_notes == 0 ) ||

					  (data[i].access_notes == 1 && (data[i].sender_id == data[i].memberId))

					  || data[i].access_notes == 2

					)

					{

						

						

					 if(data[i].system_admin == 0 && data[i].access_notes != 0)

					 {

				

			   rows += '<img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit" width="16" height="16" class="action-icon edit-note" onclick="editInternalNote('+data[i].id+')"/>';

			   

			   rows += '<img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete" width="16" height="16" class="action-icon delete-note" onclick="delInternalNote('+data[i].id+')"/>';

					 }

				  }

			}

			<?php /*else : ?>rows += '&nbsp;';<?php endif;*/?>

			rows += '</td></tr>';

		  }

		

		$("#internal_notes_table tbody").html(rows);

	}





function addQuickNote() {

	var textOld = $("#internal_note").val();

	

	var str = textOld + " " + $("#quick_notes").val();

	$("#internal_note").val(str);

} 

	function addInternalNote() {

		if (busy) return;

		busy = true;

		var text = $.trim($("#internal_note").val());

		if (text == "") return;

		$("#internal_note").val("");

		$.ajax({

			type: "POST",

			url: "<?= SITE_IN ?>application/ajax/notes.php",

			dataType: "json",

			data: {

				action: 'add',

				text: encodeURIComponent(text),

				entity_id: <?= $this->entity->id ?>,

				notes_type: <?= Note::TYPE_INTERNAL ?>

			},

			success: function(result) {

				if (result.success == true) {

					updateInternalNotes(result.data);

				} else {

					$("#internal_note").val(text);

					alert("Can't save note. Try again later, please");

				}

				busy = false;

			},

			error: function(result) {

				$("#internal_note").val(text);

				alert("Can't save note. Try again later, please");

				busy = false;

			}

		});

	}

	

	function delInternalNote(id) {

		if (confirm("Are you sure whant to delete this note?")) {

			if (busy) return;

			busy = true;

			$.ajax({

				type: "POST",

				url: "<?= SITE_IN ?>application/ajax/notes.php",

				dataType: "json",

				data: {

					action: 'del',

					id: id,

					entity_id: <?= $this->entity->id ?>,

					notes_type: <?= Note::TYPE_INTERNAL ?>

				},

				success: function(result) {

					if (result.success == true) {

						updateInternalNotes(result.data);

					} else {

						alert("Can't delete note. Try again later, please");

					}

					busy = false;

				},

				error: function(result) {

					alert("Can't delete note. Try again later, please");

					busy = false;

				}

			});

		}

	}

	function editInternalNote(id) {

		var text = $.trim($("#note_"+id+"_text").text());

		$("#note_edit_form textarea").val(text);

		$("#note_edit_form").dialog({

			width: 400,

			modal: true,

			title: "Edit Internal Note",

			resizable: false,

			buttons: [{

				text: "Save",

				click: function() {

					if ($("#note_edit_form textarea").val() == text) {

						$(this).dialog("close");

					} else {

						if (busy) return;

						busy = true;

						text = encodeURIComponent($.trim($("#note_edit_form textarea").val()));

						$.ajax({

							type: "POST",

							url: "<?= SITE_IN ?>application/ajax/notes.php",

							dataType: "json",

							data: {

								action: 'update',

								id: id,

								text: text,

								entity_id: <?= $this->entity->id ?>,

								notes_type: <?= Note::TYPE_INTERNAL ?>

							},

							success: function(result) {

								if (result.success == true) {

									updateInternalNotes(result.data);

									$("#note_edit_form").dialog("close");

								} else {

									alert("Can't save note. Try again later, please");

								}

								busy = false;

							},

							error: function(result) {

								alert("Can't save note. Try again later, please");

								busy = false;

							}

						});

					}

				}

			},{

				text: "Cancel",

				click: function() {

					$(this).dialog("close");

					busy = false;

				}

			}]

		}).dialog("open");

	}





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

function setEditBlock(type) {
        $.ajax({
            type: "POST",
            url: "<?=SITE_IN?>application/ajax/entities.php",
            dataType: 'json',
            data: {
                action: 'setBlock',
                entity_id: <?= $this->entity->id ?>,
				type:type
            },
            success: function (response) {
                if (response.success == false) {
                    document.location.reload();
                }
            }
        });
    }
function expiringEditYes()
	{
	  setEditBlock(0); 
	   if(redirectUrl !=null)
	    clearInterval(redirectUrl); 
      interval = setInterval(function(){checkEditTimeDue()}, (60*7*1000));
	  $("#checkEditDueId").dialog("close"); 
	}
function expiringEditNo()
	{
		 
      clearInterval(interval); 
	  if(redirectUrl !=null)
	    clearInterval(redirectUrl); 
	  $("#checkEditDueId").dialog("close"); 
	  var curr_entityid = "<?= $this->entity->id ?>";
      window.location.href = "<?= SITE_IN ?>application/orders/show/id/" + curr_entityid; 
	}
function alertOK(){
	 $("#blockedEditAlertId").dialog("close"); 
	}	
function checkEditTimeDue()
  {
    $("#checkEditDueId").dialog({
	modal: true,

	width: 500,

	height: 170,

	title: "<p style='color: #f00;font-weight: bold;'>ALERT MESSAGE!!!</p>",

	hide: 'fade',

	resizable: false,

	draggable: false,

	autoOpen: true

	});
	redirectUrl = setInterval(function(){tempDisableBeforeUnload = true;window.location.href = "<?= SITE_IN ?>application/orders/show/id/<?php echo $this->entity->id;?>"; }, (60*3*1000));
  }
  
  
  $(document).ready(function(){
        var redirectUrl = null;						
		
		 var blockedMember = "<?php echo $this->entity->blockedByMember(); ?>";	
        var blockedTime = "<?php echo date("H:i:s", strtotime($this->entity->blocked_time)); ?>";
        <?php if (!$this->entity->isBlocked()) { ?>
           setEditBlock(1);
           //interval = setInterval(function(){checkEditTimeDue()}, (60*7*1000));
        <?php } else { ?>
        var alertMsg = "<p>" + blockedMember + " is editing this order at this moment, please try again later.</p><div><input type='button' value='OK' onclick='alertOK()' style='margin-left: 40%; width: 65px; height: 29px;color: #008ec2;'></div>" ;
                // alert("Someone editing this Order right now. You have access only for read.");
		$("#blockedEditAlertId").dialog({
		modal: true,

		width: 385,

		height: 130,

		title: "Freight Dragon",

		hide: 'fade',

		resizable: false,

		draggable: false,

		autoOpen: true

		});
		$( "#blockedEditAlertId p" ).remove();
		$( "#blockedEditAlertId" ).append(alertMsg);
		 window.location.href = "<?= SITE_IN ?>application/orders/show/id/<?php echo $this->entity->id;?>"; 
        <?php } ?>
        $("#avail_pickup_date,#order_load_date, #order_delivery_date").datepicker({

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

		

		createForm.find("#carrier_company").autocomplete({

			source: function(request, response) {

				$.ajax({

					url: '<?= SITE_IN ?>application/ajax/autocomplete.php',

					type: 'GET',

					dataType: 'json',

					data: {

						term: request.term,

						action: 'getCarrier'

					},

					success: function(data) {

						response(data);

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

		

    });

</script>

<style>

.order-select-shipper {

    background-color: #f2f2f2;

    border: 1px solid #cccccc;

    border-radius: 5px;

    float: left;

    line-height: 20px;

    padding: 10px;

    width:40%;

}

</style>

<?php include(ROOT_PATH.'application/templates/vehicles/edit_js.php');?>

<?php include(ROOT_PATH.'application/templates/vehicles/form.php');?>

<?php

$carrier_id = "";
//print "------".get_var('acc');
if(get_var('account_id')!="")

  $carrier_id = get_var('account_id');

elseif(get_var('acc')!="")

  $carrier_id = get_var('acc');

?>



<div style="clear:both;"></div>
<div id="checkEditDueId" style="display: none;">
    <br/>
    <p style="margin-left: 10%;font-size: 15px;">Are you still there? (session is about to expire in <span style="color: #f00;font-weight: bold;">60</span> seconds)</p><br/>
	<div>
	<input type="button" value="Yes" onclick="expiringEditYes()" style="margin-left: 35%; width: 65px; height: 29px;color: #008ec2;"><input type="button" value="No" onclick="expiringEditNo()" style="width: 65px; margin-left: 3%;height: 29px;color: #008ec2;">
	</div>	
</div>
<div id="blockedEditAlertId" style="display: none;">
    <p></p>
</div>

<div id="dispatch_form" >

    <div class="msg-error" onclick="$('#dispatch_dialog .msg-error').hide();" style="display: none"><ul class="msg-list"></ul></div>

    <!--form id="dispatch_form"-->

    <form id="save_order_form" action="<?= getLink('orders/dispatchnew/id/' . $this->entity->id.'/acc/'.get_var('acc')) ?>" method="post">

        

        <input type="hidden" name="carrier_id" id="carrier_id" value="<?php print $carrier_id;?>"/>

        <input type="hidden" name="account_id" id="account_id" value="<?php print $carrier_id;?>"/>

        

      <br />  

        

  <div class="order-info" style="float:none;">

    <p class="block-title">Carrier Information</p>



    <div>

        <table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table order-edit"

               style="white-space:nowrap;">

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

                            <!--td><input type="checkbox" name="save_carrier" id="save_carrier" /> <label for="save_carrier">Save</label></td-->
 <td><input type="hidden" name="save_carrier" id="save_carrier" value="1"/> </td>
                        </tr>

                    </table>

                </td>

                <td colspan="2">&nbsp;</td>

            </tr>

            <tr>

                <td>@carrier_print_name@</td>

                <td>@carrier_contact@</td>

                <td>@carrier_driver_name@</td>

                <!--td><label for="carrier_driver"><span class="required">*</span>Driver:</label></td>

                <td>

<input tabindex="130" name="carrier_driver" type="text" maxlength="255" class="form-box-textfield" id="carrier_driver" value="<?php print $_POST['carrier_driver'];?>">

                 </td-->



            </tr>

            <tr>
			

                <td>@carrier_insurance_iccmcnumber@</td>

                <td>@carrier_email@</td>

                <td>@carrier_driver_phone@</td>

                <!--td><label for="carrier_driver_phone"><span class="required">*</span>Driver Phone:</label></td>

                <td><input class="phone form-box-textfield" tabindex="140" name="carrier_driver_phone" type="text" maxlength="255" id="carrier_driver_phone" value="<?php print $_POST['carrier_driver_phone'];?>"></td-->

                

            </tr>

            <tr>

                <td>@carrier_address@</td>

                <td>@carrier_phone1@@carrier_phone1_ext@</td>

                <td colspan="2">&nbsp;</td>

            </tr>

            <tr>

                

                <td>@carrier_city@</td>

                <td>@carrier_phone2@@carrier_phone2_ext@</td>

                <td colspan="2">&nbsp;</td>

            </tr>

            <tr>

                <td>@carrier_state@@carrier_zip@</td>

                <td>@carrier_fax@</td>

                <td colspan="2">&nbsp;</td>

            </tr>

            <tr>

                <td>@carrier_country@</td>

                <td>@carrier_cell@</td>

                <td colspan="2">&nbsp;</td>

            </tr>

        </table>

    </div>

</div>

<br/>



<div class="order-info" style="float:none;">

    <p class="block-title">Pickup Contact &amp Location</p>



    <div>

        <table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table" style="white-space:nowrap;">

                          

            <tr>
				<td>@pickup_address1@</td>
				<td>@pickup_name@</td>
				<td>@pickup_phone1@@pickup_phone1_ext@</td>
                <td>@pickup_cell@</td>
                
			</tr>
			<tr>
				<td>@pickup_address2@</td>
				<td>@origin_contact_name2@</td>
				<td>@pickup_phone2@@pickup_phone2_ext@</td>
                <td>@pickup_cell2@</td>
			</tr>
			<tr>
				<td>@pickup_city@</td>
				<td>@pickup_company@</td>
				<td>@origin_phone3@@pickup_phone3_ext@</td>
                <td >@origin_fax@</td>
			</tr>
			<tr>
				<td>@pickup_state@@pickup_zip@</td><div id="notes_container"></div>
				<td>@origin_auction_name@</td>
				<td>@pickup_phone4@@pickup_phone4_ext@</td>
                <td >@pickup_fax2@</td>
			</tr>
			<tr>
				<td>@pickup_country@</td>
                <td>@from_booking_number@</td>
                <td colspan="2">&nbsp;</td>
                <td colspan="2">&nbsp;</td>
				
			</tr>
			<tr>
				<td>@origin_type@</td>
                <td>@from_buyer_number@</td>
				<td colspan="2">&nbsp;</td>
                <td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td>@origin_hours@</td>
                <td colspan="2">&nbsp;</td>
				<td colspan="2">&nbsp;</td>
			</tr>

        </table>

    </div>

</div>

<br/>



<div class="order-info" style="float:none;">

    <p class="block-title">Delivery Contact &amp Location</p>



    <div>

        <table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table" style="white-space:nowrap;">

                          

            <tr>
				<td>@deliver_address1@</td>
				<td>@deliver_name@</td>
				<td>@deliver_phone1@@deliver_phone1_ext@</td>
                <td>@deliver_cell@</td>
			</tr>
			<tr>
				<td>@deliver_address2@</td>
				<td>@destination_contact_name2@</td>
				<td>@deliver_phone2@@deliver_phone2_ext@</td>
                <td>@deliver_cell2@</td>
			</tr>
			<tr>
				<td>@deliver_city@</td>
				<td>@deliver_company@</td>
				<td>@destination_phone3@@deliver_phone3_ext@</td>
                <td>@destination_fax@</td>
			</tr>
			<tr>
				<td>@deliver_state@@deliver_zip@</td>
				<td >@destination_auction_name@</td>
				<td>@deliver_phone4@@deliver_phone4_ext@</td>
                <td>@deliver_fax2@</td>
			</tr>
			<tr>
				<td>@deliver_country@</td>
				<td>@to_booking_number@</td>
				<td colspan="2">&nbsp;</td>
                <td colspan="2">&nbsp;</td>
			</tr>
			
			<tr>
				<td>@destination_type@</td>
				<td>@to_buyer_number@</td>
				<td colspan="2">&nbsp;</td>
                <td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td>@destination_hours@</td>
				<td colspan="2"></td>
				<td colspan="2"></td>
                <td colspan="2">&nbsp;</td>
			</tr>

        </table>

    </div>

</div>

<br/>

<div class="order-info" style="float:none;">

    <p class="block-title">Shipping Information</p>



    <div>

        <table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table" style="white-space:nowrap;">
		<td><strong>Mileage: </strong><?= number_format($this->entity->distance, 0, "", "") ?> mi($ <?= number_format(($this->entity->getCarrierPay(false) / $this->entity->distance), 2, ".", ",") ?>/mi)&nbsp;&nbsp;(<span class='red' onclick="mapIt(<?= $this->entity->id ?>);">MAP IT</span>)</strong></td>

            <tr>
		

                <td>@avail_pickup_date@</td>

                <td rowspan="5" valign="top">

                    <table cellspacing="0" cellpadding="0" border="0">

                      

                        <tr>



                            <td valign="top"><label for="order_notes_from_shipper">Dispatch Instructions:</label></td>



<td><textarea style="width:580px;height:70px" tabindex="215" name="order_notes_from_shipper" cols="6" rows="7" class="form-box-textarea" id="order_notes_from_shipper"></textarea></td>

                        </tr>

                        <tr>

                            <td>&nbsp;</td>

                            <td><input type="hidden" name="order_include_shipper_comment" id="order_include_shipper_comment" value="1" /></td>

                        </tr>

                    </table>

                </td>

            </tr>

             

            <tr>

                <td valign="top">@order_load_date_type@ @order_load_date@</td>

            </tr>

            <tr>

                <td valign="top">@order_delivery_date_type@ @order_delivery_date@</td>

            </tr>

<!--            <tr>-->

<!--                <td valign="top">@shipping_vehicles_run@</td>-->

<!--            </tr>-->

            <tr>

                <td valign="top">@order_ship_via@</td>

            </tr>

        </table>

    </div>

</div>



        

        <br/>

  <div class="order-info" style="float:none;">

    <p class="block-title">Vehicle Information</p>



    <div>

        <table cellpadding="0" cellspacing="0" border="0" width="100%" class="grid" style="white-space:nowrap;"

               id="vehicles-grid">

            <thead>

            <tr class="grid-head">

                <td class="grid-head-left">ID</td>

                <td>Year</td>

                <td>Make</td>

                <td>Model</td>

                <td>Type</td>

                <!--td>Vin #</td>

                <td>Lot #</td-->

                <td align="center">Total Tariff</td>

                <td align="center">Deposit</td>

	            <td>Inop</td>

                <td class="grid-head-right">Actions</td>

            </tr>

            </thead>

            <tbody>

            <?php if (count($this->vehicles) > 0) : ?>

                <?php foreach ($this->vehicles as $i => $vehicle) : ?>

                    <tr class="grid-body<?= ($i % 2) ? ' even' : '' ?>">

                        <td class="grid-body-left"><?= $this->entity->id ?>-V<?= ($i + 1) ?></td>

                        <td><?= $vehicle->year ?></td>

                        <td><?= $vehicle->make ?></td>

                        <td><?= $vehicle->model ?></td>

                        <td><?= $vehicle->type ?></td>

                        

                        <td><input type="text" name="vehicle_tariff[<?php print $vehicle->id;?>]" value="<?= $vehicle->tariff ?>" id="vehicle_tariff_<?php print $vehicle->id;?>" onkeyup="updatePricingInfo();" />

                            

                        </td>

                        <td><input type="text" name="vehicle_deposit[<?php print $vehicle->id;?>]" value="<?= $vehicle->deposit ?>" id="vehicle_tariff_<?php print $vehicle->id;?>" onkeyup="updatePricingInfo();"/>

                        <input type="hidden" name="vehicle_id[]" value="<?php print $vehicle->id;?>"  />

                        </td>

	                    <td><?= ($vehicle->inop == '1')?'Yes':'No' ?></td>

                        <td align="center" class="grid-body-right" width="60">

                            <?php if (!$this->entity->isBlocked()) { ?>

                                <img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit"

                                     onclick="editVehicle(<?= $vehicle->id ?>)" class="action-icon"/>

                                <img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete"

                                     onclick="deleteVehicle(<?= $vehicle->id ?>)" class="action-icon"/>

                            <?php } else { ?>&nbsp; <?php } ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else : ?>

                <tr class="grid-body">

                    <td colspan="8" align="center" class="grid body-left grid-body-right"><i>No Vehicles</i></td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

        <br/>

		<table width="100%" cellpadding="1" cellspacing="1">

        <tr>

               <td width="5%" align="left">

			   <?php if (!$this->entity->isBlocked()) { ?>

                   <div><?= functionButton('Add Vehicle', 'addVehicle()') ?></div>

               <?php } ?>

			   </td>

                <td width="5%" align="left">

                <?php if ($this->isAutoQuoteAlowed) { ?>

						<div><?= functionButton('Quick Price', 'quickPrice()') ?></div>

					<?php } ?>

                </td>

                <td>&nbsp;</td>

            </tr>

            </table>

        

    </div>

</div>

<br/>

<div class="order-info1" style="float:none;">

<table width="100%" cellpadding="4" cellspacing="4">

 <tr>

   <td valign="top">

        <div class="order-info" style="float:none;">

            <p class="block-title">Pricing Information</p>

        

            <div>

                <table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table" style="white-space:nowrap;">

                    <tr>

                        <td>Total Tariff</td>

                        <td><span id="total_tariff">@total_tariff@</span>&nbsp;<span class="grey-comment">(Edit carrier pay and deposit under the "Vehicle Information" section)</span>

                        </td>

                        

                    </tr>

                    <tr>

                        <td>Required Deposit</td>

                        <td><span id="total_deposit">@total_deposit@</span>&nbsp;<span class="grey-comment">(Edit deposit under the "Vehicle Information" section)</span>

                        </td>

                    </tr>

                    <tr>

                        <td>Carrier Pay</td>

                        <td><span id="carrier_pay">@carrier_pay@</span>&nbsp;<span class="grey-comment">(Edit carrier pay under the "Vehicle Information" section)</span>

                        </td>

                    </tr>

                    

                    <tr>

                        <td>@pickup_terminal_fee@&nbsp;<span class="grey-comment">(Do not include fees paid directly from shipper to terminal)</span>

                        </td>

                    </tr>

                    <tr>

                        <td>@delivery_terminal_fee@&nbsp;<span class="grey-comment">(Do not include fees paid directly from shipper to terminal)</span>

                        </td>

                    </tr>

                    

                </table>

            </div>

        </div>

       </td>

    <td valign="top">        

        <div class="order-info" style="float:none;">

            <p class="block-title">Payment Information</p>

            <div>

                <table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table" style="white-space:nowrap;">

                    

                    <tr>

                        <td>@balance_paid_by@</td>

                    </tr>

                    <tr>

                        <td>@customer_balance_paid_by@</td>

                    </tr>

                     <tr>

                        <td>@payments_terms_carrier@</td>

                    </tr>

                   

                </table>

            </div>

        </div>

      </td>  

    </tr>

  </table>

</div>

<br/>



<?php $notes = $this->notes; ?>

<div class="order-info" style="width: 97%;float: left;margin-top: 10px;">

	<p class="block-title">Internal Notes</p>

	<div>

	<?php //if ($this->entity->status != Entity::STATUS_ARCHIVED) : ?>

		<textarea class="form-box-textarea" style="width: 1160px; height: 52px;" maxlength="1000" id="internal_note"></textarea>

        

		<div style="float:left; padding:2px;">

Quick Notes&nbsp;

</div>

         <div style="float:left; padding:2px;"><select name="quick_notes" id="quick_notes" onchange="addQuickNote();">

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



<div style="float:right;"><?= functionButton('Add Note', 'addInternalNote()') ?></div>

		<div style="clear:both;"><br/></div>

	<?php //endif; ?>

		<table cellspacing="0" cellpadding="0" width="100%" border="0" class="grid" id="internal_notes_table">

			<thead>

			<tr class="grid-head">

				<td class="grid-head-left"><?=$this->order->getTitle('created', 'Date')?></td>

				<td width="70%">Note</td>

				<td>User</td>

				<td class="grid-head-right">Action</td>

			</tr>

			</thead>

			<tbody>

			<? if (count($notes[Note::TYPE_INTERNAL]) == 0) : ?>

			<tr class="grid-body">

				<td colspan="4" class="grid-body-left grid-body-right" align="center"><i>No notes available.</i></td>

			</tr>

			<? else : ?>

			<?php foreach($notes[Note::TYPE_INTERNAL] as $note) : ?>

			<?php $sender = $note->getSender(); 

			 

			   $email = $sender->email;

			   $contactname = $sender->contactname;

			   if($note->system_admin == 2){

			     $email = "admin@freightdragon.com";

				 $contactname = "FreightDragon";

			   }

			

			if (($_SESSION['member']['access_notes'] == 0 ) 

				    || $_SESSION['member']['access_notes'] == 1

					|| $_SESSION['member']['access_notes'] == 2

					)

			{

			?>

			<tr class="grid-body">

				<td style="white-space:nowrap;" class="grid-body-left"><?= $note->getCreated("m/d/y h:i a") ?></td>

				<td id="note_<?= $note->id ?>_text" ><?php if($note->system_admin == 1){?><b><?= $note->getText() ?></b><?php }else{?><?= $note->getText() ?><?php }?></td>

				<td style="text-align: center;"><a href="mailto:<?= $email ?>"><?= $contactname ?></a></td>

				<td class="grid-body-right" style="white-space: nowrap;">

				  <?php   if (!$this->entity->readonly) : ?>

					

                    

                  <?php //if(($note->sender_id == (int)$_SESSION['member_id']) || ((int)$_SESSION['member_id']==1)){

					  //print $_SESSION['member']['access_notes']."---".$note->sender_id ."==". (int)$_SESSION['member_id']."--".$note->system_admin; 

					if (($_SESSION['member']['access_notes'] == 0 ) ||

					  ($_SESSION['member']['access_notes'] == 1 && ($note->sender_id == (int)$_SESSION['member_id']))

					  || $_SESSION['member']['access_notes'] == 2

					)

					{

						

						

					 if($note->system_admin == 0 && $_SESSION['member']['access_notes'] != 0){

					  ?>  

                      

                    <img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit" width="16" height="16" class="action-icon edit-note" onclick="editInternalNote(<?= $note->id ?>)"/>

                      

					<img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete" width="16" height="16" class="action-icon delete-note" onclick="delInternalNote(<?= $note->id ?>)"/>

                    

                     <?php 

						}

					 

					 }

					 

					 ?>

                    

					<?php else : ?>&nbsp;<?php endif; ?>

				</td>

			</tr>

            

            <?php } ?>

			<?php endforeach; ?>

			<?php endif ; ?>

			</tbody>

		</table>

	</div>

</div>

 <br />

 <br />



<br />





<div style="float:right;padding-top:20px;">

    

	    <div style="float:right">

		    <table cellpadding="0" cellspacing="0" border="0">

			    <tr>

				    <td>

					   

				    </td>

				    <td style="padding-left: 15px;"><?= submitButtons(SITE_IN."application/orders/show/id/", "Dispatch") ?></td>

			    </tr>

		    </table>

	    </div>

   

</div>

<div class="clear"></div>       

    </form>

</div>