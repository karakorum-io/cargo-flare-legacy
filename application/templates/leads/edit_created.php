<script type="text/javascript">

var tempDisableBeforeUnload = false;
var interval= 0;
$(window).bind('beforeunload', function(){

   if(($("#shipper_fname").val()!='' ||
	$("#shipper_lname").val()!='' ||
	$("#shipper_company").val()!='' ||
	$("#shipper_email").val()!='') && !tempDisableBeforeUnload)
	{
		  return 'Leaving this page will lose your changes. Are you sure?';
	}
	else{
	   tempDisableBeforeUnload = false;
	   return;

	}
});

function countChar(val) {
        var len = val.value.length;
        if (len >= 60) {
          val.value = val.value.substring(0, 60);
        } else {
          $('#charNum').text(' '+(60 - len)+' )');

        }

      };


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
			quote_id: $('#order_id').val()
		};
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
		// // $("body").nimbleLoader("show");
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: BASE_PATH+'application/ajax/autoquote.php',
			data: data,
			success: function(res) {
				$("#total_tariff").html(decodeURIComponent(res.total_tariff));
				$("#total_deposit").html(decodeURIComponent(res.total_deposit));
				$("#carrier_pay").html(decodeURIComponent(res.carrier_pay));
				swal.fire(res.message);
			},
			error: function() {
				swal.fire('Failed to calculate Quick Price');
			},
			complete: function() {
				// $("body").nimbleLoader("hide");
			}

		});
	}
    function setEditBlock() {
        $.ajax({
            type: "POST",
            url: "<?=SITE_IN?>application/ajax/entities.php",
            dataType: 'json',
            data: {
                action: 'setBlock',
                entity_id: <?= $this->entity->id ?>
            },
            success: function (response) {
                if (response.success == false) {
                    document.location.reload();
                }
            }
        });
    }
	function formatPhoneNumber(s) {
	  var s2 = (""+s).replace(/\D/g, '');
	  var m = s2.match(/^(\d{3})(\d{3})(\d{4})$/);
	  return (!m) ? null : "" + m[1] + "-" + m[2] + "-" + m[3];
	}
    function applySearch(num) {
		
        var acc_obj = acc_data[num];
        switch (acc_type) {
            case <?=Account::TYPE_SHIPPER?>:
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
                $("#shipper_state").val(acc_obj.state);
                $("#shipper_zip").val(acc_obj.zip);
				$("#shipper_type").val(acc_obj.shipper_type);
				$("#shipper_hours").val(acc_obj.hours_of_operation);

                break;
            case <?=Account::TYPE_TERMINAL?>:
                $("#" + acc_location + "_address1").val(acc_obj.address1);
                $("#" + acc_location + "_address2").val(acc_obj.address2);
                $("#" + acc_location + "_city").val(acc_obj.city);
                $("#" + acc_location + "_country").val(acc_obj.coutry);
                $("#" + acc_location + "_state").val(acc_obj.state);
                $("#" + acc_location + "_zip").val(acc_obj.zip);
                $("#" + acc_location + "_contact_name").val(acc_obj.contact_name1);
                $("#" + acc_location + "_company_name").val(acc_obj.company_name);
                $("#" + acc_location + "_phone1").val(acc_obj.phone1);
                $("#" + acc_location + "_phone2").val(acc_obj.phone2);
                $("#" + acc_location + "_mobile").val(acc_obj.cell);
				$("#" + acc_location + "_type").val(acc_obj.location_type);
                $("#" + acc_location + "_hours").val(acc_obj.hours_of_operation);

                break;
        }
    }


	function setLocationSameAsShipperOrder(location) {

    if (confirm("Are you sure you want to overwrite location information?")) {

		if(location == 'e_cc'){
			$("input[name='"+location+"_fname']").val($("input[name='shipper_fname']").val());
			$("input[name='"+location+"_lname']").val($("input[name='shipper_lname']").val());
			$("input[name='"+location+"_address']").val($("input[name='shipper_address1']").val());
		}
		else{
		   $("input[name='"+location+"_company_name']").val($("input[name='shipper_company']").val());
		   $("input[name='"+location+"_auction_name']").val($("input[name='shipper_company']").val());
		   $("input[name='"+location+"_address1']").val($("input[name='shipper_address1']").val());
		   $("input[name='"+location+"_contact_name']").val($("input[name='shipper_fname']").val()+' '+$("input[name='shipper_lname']").val());
		}

	    $("input[name='"+location+"_city']").val($("input[name='shipper_city']").val());

	    $("select[name='"+location+"_state']").val($("select[name='shipper_state']").val());

	    $("input[name='"+location+"_zip']").val($("input[name='shipper_zip']").val());

	    $("select[name='"+location+"_country']").val($("select[name='shipper_country']").val());

		$("input[name='"+location+"_phone1']").val($("input[name='shipper_phone1']").val());

	    $("input[name='"+location+"_phone2']").val($("input[name='shipper_phone2']").val());
	    $("input[name='"+location+"_mobile']").val($("input[name='shipper_mobile']").val());

        $("input[name='"+location+"_fax']").val($("input[name='shipper_fax']").val());

		$("input[name='"+location+"_address2']").val($("input[name='shipper_address2']").val());
		$("select[name='"+location+"_type']").val($("select[name='shipper_type']").val());
		$("input[name='"+location+"_hours']").val($("input[name='shipper_hours']").val());

    }

}

function typeselected() {
		if($("#shipper_type").val() == "Commercial")
	      $('#shipper_company-span').show();
		else
		  $('#shipper_company-span').hide();
		
	}
function expiringEditYes()
	{
	  setEditBlock(); 
      //interval = setInterval(function(){checkEditTimeDue()}, 240000);
	  $("#checkEditDueId").dialog("close"); 
	}
function expiringEditNo()
	{
      clearInterval(interval); 
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
  }
    $(document).ready(function () {
		typeselected();
		 var blockedMember = "<?php echo $this->entity->blockedByMember(); ?>";	
        var blockedTime = "<?php echo date("H:i:s", strtotime($this->entity->blocked_time)); ?>";
        <?php if (!$this->entity->isBlocked()) { ?>
        setEditBlock();
        //interval = setInterval(function(){checkEditTimeDue()}, 240000);
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
		$( "#blockedEditAlertId p" ).remove()
		$( "#blockedEditAlertId" ).append(alertMsg);
        <?php } ?>
        var cur_make = "";
        $(".datepicker").datepicker();


		$("#avail_pickup_date").datepicker({
			dateFormat: 'mm/dd/yy',
            minDate: '+0'
		});


        <?php /*if (!is_null($this->entity->dispatched)) {

            $("#order_edit_disabler").show();
            $("#order_edit_disabler").click(function() {
                alert("You can't edit dispatched order.");
            });
            $("#order_edit_form_block *").focus(function() {
                alert("You can't edit dispatched order.");
                return false;
            });-->
            } */ ?>
       $("#balance_paid_by").change(function(){
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

                            alert("Can't send email. Try again later, please");

                        }

                }
           });    
          });
	});
</script>

<script type="text/javascript">
	var busy = false;
function updateInternalNotes(data) {
		var rows = "";
		
		for (i in data) {
			
			var email = data[i].email;
			var contactname = data[i].sender;
			
			if(data[i].system_admin == 2){
			     email = "admin@freightdragon.com";
				 contactname = "FreightDragon";
			   }
			if ((data[i].access_notes == 0 )   
				    || data[i].access_notes == 1
					|| data[i].access_notes == 2
					)
			{
				
              var discardStr = '';
				if(data[i].discard==1)
				   discardStr = ' style="text-decoration: line-through;" ';
				 
			if(data[i].system_admin == 1 || data[i].system_admin == 2)
			{
				rows += '<tr class="grid-body"><td style="white-space:nowrap;" class="grid-body-left" >'+data[i].created+'</td><td id="note_'+data[i].id+'_text"  '+discardStr+'><b>'+decodeURIComponent(data[i].text)+'</b></td><td>';	 
			}
			else if(data[i].priority==2)
			   rows += '<tr class="grid-body"><td class="grid-body-left" >'+data[i].created+'</td><td id="note_'+data[i].id+'_text"  '+discardStr+'><b style="font-size:12px;color:red;">'+decodeURIComponent(data[i].text)+'</b></td><td>';
			 else
			   rows += '<tr class="grid-body"><td class="grid-body-left">'+data[i].created+'</td><td id="note_'+data[i].id+'_text"  '+discardStr+'>'+decodeURIComponent(data[i].text)+'</td><td>';
			
		
			
			rows += '<a href="mailto:'+email+'">'+contactname+'</a></td><td style="white-space: nowrap;" class="grid-body-right"  >';
			
			<?php //if (!$this->entity->readonly) : ?>
			
				if ((data[i].access_notes == 0 ) ||
					  (data[i].access_notes == 1 && (data[i].sender_id == data[i].memberId))
					  || data[i].access_notes == 2
					)
					{
					if(data[i].sender_id == data[i].memberId  && data[i].system_admin == 0 )
					    rows += '<img src="<?= SITE_IN ?>images/icons/strike.png" alt="Discard" title="Discard" width="16" height="16" class="action-icon edit-note" onclick="discardNote('+data[i].id+')"/>';	
						
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

function discardNote(note_id) {

	$.ajax({
		type: "POST",
		url: "<?= SITE_IN ?>application/ajax/notes.php",
		dataType: 'json',
		data: {
			action: 'discard',
			id: note_id,
			entity_id: <?= $this->entity->id ?>,
			notes_type: <?= Note::TYPE_INTERNAL ?>
		},
		success: function(result) {
			if (result.success == true) {
				updateInternalNotes(result.data);
			} else {
				alert("Can't discard note. Try again later, please");
			}
			busy = false;
		},
		error: function(result) {
			alert("Can't discard note. Try again later, please");
			busy = false;
		}
	});
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
		var priority = $.trim($("#priority_notes").val());
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
				notes_type: <?= Note::TYPE_INTERNAL ?>,
				priority: priority
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

function editInternalNote(id)
 {
	var text = $.trim($("#note_"+id+"_text").text());
	$("#note_edit_form textarea").val(text);
	 $("#edit_save").val(id);
	 $("#note_edit_form").modal();

 }
	

function note_edit_form_send(id)
{

	if ($("#note_edit_form textarea").val() == text) {
	    $("#note_edit_form").modal('hide');

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
				$("#note_edit_form").modal('hide');
			} else {
				swal.fire("Can't save note. Try again later, please");
			}
			busy = false;
		},
		error: function(result) {
			swal.fire("Can't save note. Try again later, please");
			busy = false;
		}
	  });
	}
}


/*	function editInternalNote(id) {
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
	}*/



	</script>
    
 <div id="note_edit_form" style="display:none;">
	<textarea style="width: 95%;height:100px;" class="form-box-textarea" name="note_text"></textarea>
</div>  
<div style="padding-top:15px;">
    <?php include('lead_menu.php');  ?>
</div>
<style type="text/css">
    .order-edit .form-box-textfield {
        width: 210px;
    }
</style>
 <?php
 $accessEdit = 0;
if($_SESSION['member']['access_dispatch_orders'] ==1
				  || $this->entity->status == Entity::STATUS_ACTIVE
				  || $this->entity->status == Entity::STATUS_ONHOLD
				  || $this->entity->status == Entity::STATUS_POSTED
				  || $this->entity->status == Entity::STATUS_NOTSIGNED
				 ){
	$accessEdit = 1;
       }
?>
<?php include(ROOT_PATH . 'application/templates/vehicles/edit_js.php'); ?>
<?php include(ROOT_PATH . 'application/templates/vehicles/form.php'); ?>
<br/>
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
<h3>Edit Quote #<?= $this->entity->getNumber() ?></h3>
Complete the form below and click Save Order when finished. Required fields are marked with a <span
    style="color:red;">*</span><br/>

	<?php $assigned = $this->entity->getAssigned(); ?>
	Assigned to: <strong><?= $assigned->contactname ?></strong><br /><br/>
<div id="order_edit_form_block" style="position: relative;">
<div id="order_edit_disabler">&nbsp;</div>
<?php if (!$this->entity->isBlocked()) { ?>
<form id="save_order_form" action="<?= getLink('leads/editcreatedquote/id/' . $this->entity->id) ?>" method="post" onsubmit="javascript:tempDisableBeforeUnload = true;">
<?php } ?>
<input type="hidden" id="order_id" name="order_id" value="<?= $this->entity->id ?>"/>

<div class="order-info" style="float:none;">
    <p class="block-title">Shipper Information</p>

    <div>
        <table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table order-edit"
               style="white-space:nowrap;">
            <tr>
                <td>
	                <table class="no-padding">
		                <tr>
			                <td><!--<input type="checkbox" id="save_shipper" name="save_shipper" value="1"/>-->
			                <input type="hidden" id="save_shipper" name="save_shipper" value="1"/></td>
			                <td style="padding-bottom:5px"><!--<label for="save_shipper">Save</label>--></td>
		                </tr>
	                </table>
                </td>
                <td></td>
                <td>@shipper_email@</td>
                <td>@shipper_address1@</td>
            </tr>
            <tr>
                <td>@shipper_fname@</td>
                <td>@shipper_phone1@</td>
                <td>@shipper_address2@</td>
            </tr>
            <tr>
                <td>@shipper_lname@</td>
                <td>@shipper_phone2@</td>
                <td>@shipper_city@</td>
            </tr>
            <tr>
                <td>@shipper_company@</td>
                <td>@shipper_mobile@</td>
                <td>@shipper_state@@shipper_zip@</td>
            </tr>

			<tr>
				<td>@shipper_type@</td>
				<td>@shipper_fax@</td>
				<td>@shipper_country@</td>
			</tr>
            <tr>
				<td>@shipper_hours@</td>
				<td>@referred_by@</td>
				<td>&nbsp;</td><td>&nbsp;</td>
			</tr>
        </table>
    </div>
</div>
<br/>
<div class="order-info" style="float:none;">
    <p class="block-title">Shipping Information</p>

    <div>
        <table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table" style="white-space:nowrap;">
            <tr>
                <td>@est_ship_date@</td>
                <td valign="top">@shipping_ship_via@</td>
			</tr>

			
		</table>
	</div>
</div>
<br/>
<div class="order-info" style="float:none;">
    <p class="block-title">Route Information</p>

    <div>
    <table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table" style="white-space:nowrap;">
    <tr>
        <td>
        <table cellspacing="0" cellpadding="0" border="0" width="100%"  style="white-space:nowrap;">
            <tr>
                <td>
	                <b>FROM</b>
                </td>
                
            </tr>
            <!--tr>
				<td>@origin_address1@</td>
				<td>@origin_contact_name@</td>
				<td>@origin_phone1@</td>
			</tr>
			<tr>
				<td>@origin_address2@</td>
				<td>@origin_contact_name2@</td>
				<td>@origin_phone2@</td>
			</tr-->
			<tr>
				<td>@origin_city@</td>
				<!--td>@origin_company_name@</td>
				<td>@origin_phone3@</td-->
			</tr>
			<tr>
				<td>@origin_state@@origin_zip@</td><div id="notes_container"></div>
				<!--td>@origin_auction_name@</td>
				<td>@origin_mobile@</td-->
			</tr>
			<tr>
				<td>@origin_country@</td>
                <!--td>@origin_booking_number@</td>
				<td >@origin_fax@</td-->
			</tr>
			<!--tr>
				<td>@origin_type@</td>
                <td>@origin_buyer_number@</td>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td>@origin_hours@</td>
                <td colspan="2">&nbsp;</td>
				<td colspan="2">&nbsp;</td>
			</tr-->
        </table>
        </td>
        <td>
        <table cellspacing="0" cellpadding="0" border="0" width="100%"  style="white-space:nowrap;">
            <tr>
                <td>
	             <b>TO</b>   
                </td>
                
            </tr>
           <!--tr>
				<td>@destination_address1@</td>
				<td>@destination_contact_name@</td>
				<td>@destination_phone1@</td>
			</tr>
			<tr>
				<td>@destination_address2@</td>
				<td>@destination_contact_name2@</td>
				<td>@destination_phone2@</td>
			</tr-->
			<tr>
				<td>@destination_city@</td>
				<!--td>@destination_company_name@</td>
				<td>@destination_phone3@</td-->
			</tr>
			<tr>
				<td>@destination_state@@destination_zip@</td>
				<!--td >@destination_auction_name@</td>
				<td>@destination_mobile@</td-->
			</tr>
			<tr>
				<td>@destination_country@</td>
				<!--td>@destination_booking_number@</td>
				<td>@destination_fax@</td-->
			</tr>

			<!--tr>
				<td>@destination_type@</td>
				<td>@destination_buyer_number@</td>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td>@destination_hours@</td>
				<td colspan="2"></td>
				<td colspan="2"></td>
			</tr-->
        </table>
        </td>
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
                       <?php  if($accessEdit==1){?>
                            <?php if (!$this->entity->isBlocked()) { ?>
                                <img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit"
                                     onclick="editVehicle(<?= $vehicle->id ?>)" class="action-icon"/>
                                <img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete"
                                     onclick="deleteVehicle(<?= $vehicle->id ?>)" class="action-icon"/>
                            <?php } else { ?>&nbsp; <?php } ?>
                         <?php }else{ ?>
                               <img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit"
                                      class="action-icon"/>
                                <img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete"
                                      class="action-icon"/>
                         <?php }?>
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
        <?php
if($accessEdit==1){
?>
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
        <?php }else{ ?>
        <table width="100%" cellpadding="1" cellspacing="1">
        <tr>
               <td width="5%" align="left">
			   <?php if (!$this->entity->isBlocked()) { ?>
                   <div><?= functionButton('Add Vehicle', '') ?></div>
               <?php } ?>
			   </td>
                <td width="5%" align="left">
                <?php if ($this->isAutoQuoteAlowed) { ?>
						<div><?= functionButton('Quick Price', '') ?></div>
					<?php } ?>
                </td>
                <td>&nbsp;</td>
            </tr>
            </table>
        <?php }?>
    </div>
</div>
<br/>

<br/>


<?php $notes = $this->notes; ?>
<div class="order-info" style="width: 97%;float: left;margin-top: 10px;">
	<p class="block-title">Internal Notes</p>
	<div>
	<?php //if ($this->entity->status != Entity::STATUS_ARCHIVED) : ?>
		<textarea class="form-box-textarea" style="width: 865px; height: 52px;" maxlength="1000" id="internal_note"></textarea>
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
<div style="float:left; padding:2px;">
&nbsp;&nbsp;&nbsp;Priority&nbsp;
</div>
<div style="float:left; padding:2px;"><select name="priority_notes" id="priority_notes" >
<option value="1">Low</option>
<option value="2">High</option>

</select>
</div>

<div style="float:right;"><?= functionButton('Add Note', 'addInternalNote()') ?></div>
		<div style="clear:both;"><br/></div>
	<?php //endif; ?>
		<table cellspacing="0" cellpadding="0" width="100%" border="0" class="grid" id="internal_notes_table">
			<thead>
			<tr class="grid-head">
				<td class="grid-head-left">Date</td>
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
				//$color = "";
				//if($note->priority==2)
				// $color = "#39F";
			?>
			<tr class="grid-body" >
				 <td style="white-space:nowrap;"  class="grid-body-left" <?php if($note->priority==2){?> style="color:#FF0000"<?php }?>><?= $note->getCreated("m/d/y h:i a") ?></td>
				<td id="note_<?= $note->id ?>_text" style=" <?php if($note->discard==1){ ?>text-decoration: line-through;<?php }?><?php if($note->priority==2){?>color:#FF0000;<?php }?>"><?php if($note->system_admin == 1 || $note->system_admin == 2){?><b><?= $note->getText() ?></b><?php }elseif($note->priority==2){?><b style="font-size:12px;"><?= $note->getText() ?></b><?php }else{?><?= $note->getText() ?><?php }?></td>
				<td style="text-align: center;" <?php if($note->priority==2){?>style="color:#FF0000"<?php }?>><a href="mailto:<?= $email ?>"><?= $contactname ?></a></td>
				<td class="grid-body-right" style="white-space: nowrap;" <?php if($note->priority==2){?>style="color:#FF0000"<?php }?>>
				  <?php   if (!$this->entity->readonly) : ?>
					
                    
                  <?php //if(($note->sender_id == (int)$_SESSION['member_id']) || ((int)$_SESSION['member_id']==1)){
					  //print $_SESSION['member']['access_notes']."---".$note->sender_id ."==". (int)$_SESSION['member_id']."--".$note->system_admin; 
					  //print $_SESSION['member']['access_notes']."---".$note->sender_id."==".$_SESSION['member_id']."---".$note->system_admin;
					if (($_SESSION['member']['access_notes'] == 0 ) ||
					  ($_SESSION['member']['access_notes'] == 1 && ($note->sender_id == (int)$_SESSION['member_id']))
					  || $_SESSION['member']['access_notes'] == 2
					)
					{
						
					if($note->sender_id == (int)$_SESSION['member_id']  && $note->system_admin == 0 ){
						?>
                        <img src="<?= SITE_IN ?>images/icons/strike.png" alt="Discard" title="Discard" width="16" height="16" class="action-icon edit-note" onclick="discardNote(<?= $note->id ?>)"/>	
                        <?php
						 }	
					 if($note->system_admin == 0 && $_SESSION['member']['access_notes'] != 0 ){
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

<?php
if($accessEdit==1){
?>
<?php if (!$this->entity->isBlocked()) { ?>
<div style="float:right; padding-top:20px;">
    
	    <div style="float:right;">
		    <table cellpadding="0" cellspacing="0" border="0">
			    <tr>
				    <td>
					    <input type="hidden" name="send_email" value="0" id="co_send_email"/>
					    <?= functionButton("Save &amp; Email", 'saveAndEmail();'); ?>
				    </td>
				    <td style="padding-left: 15px;"><?= submitButtons(SITE_IN."application/leads/showimported/id/" . $this->entity->id, "Save") ?></td>
			    </tr>
		    </table>
	    </div>
    
</div>

<div class="clear"></div>
</form>
</div>
<?php } ?>
<?php }else{
	?>
    <div style="float:right;">
<div style="float:right;">
    <table cellpadding="0" cellspacing="0" border="0">
    <tbody><tr>
        <td>
        <input type="hidden" name="send_email" value="0" id="co_send_email">
            <div class="form-box-buttons-new">
                <table cellspacing="0" cellpadding="0" border="0" style="-webkit-user-select: none;">
                <tbody style="-webkit-user-select: none;"><tr style="-webkit-user-select: none;">
                    <td class="bg-left" style="-webkit-user-select: none;">
                       <div style="-webkit-user-select: none;">&#8203;</div>
                    </td>
                    <td align="center" class="bg-center" style="-webkit-user-select: none;">
                       <div  style="-webkit-user-select: none;">Save &amp; Email</div>
                    </td>
                    <td class="bg-right" style="-webkit-user-select: none;">
                       <div style="-webkit-user-select: none;">&#8203;</div>
                    </td>
                </tr>
                </tbody></table>
            </div>
        </td>
        <td style="padding-left: 15px;"> 

        <div class="form-box-buttons dd">
        <span id="submit_button-submit-btn" style="-webkit-user-select: none;"><input type="button" id="submit_button" value="Save" onclick="disableBtn();" style="-webkit-user-select: none;"></span>
        &nbsp;&nbsp;&nbsp;
           <input type="button" value="Cancel" onclick="document.location.href='/application/leads/showimported/id/<?php print $this->entity->id;?>'" style="-webkit-user-select: none;">
        </div>
        <script type="text/javascript">//<![CDATA[
             function disableBtn(){setTimeout(function(){$('#submit_button-submit-btn').html('<input type="button" value="Not Authorized..." disabled="disabled" />');},1);}
        //]]></script>
        </td>
    </tr>
    </tbody></table>
</div>
</div>
    <?php
}?>
<script type="text/javascript">
	function saveAndEmail() {
		$("#co_send_email").val("1");
		$("#submit_button").click();
	}

    function checkPostToCD() {
        <? if ($this->ask_post_to_cd){?>
        /*$("#acc_entity_dialog").dialog('option', 'title', 'Repost order?').dialog('open');*/
        <? }else{  ?>
        $("#save_form").submit();
        <? } ?>
    }
</script>