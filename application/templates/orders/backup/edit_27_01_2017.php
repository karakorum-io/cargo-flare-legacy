<script type="text/javascript">

var tempDisableBeforeUnload = false;
<?php if ($this->entity->isBlocked()) { ?>
 tempDisableBeforeUnload = true;
<?php }?>
var interval= 0;
$(window).bind('beforeunload', function(){

   if(($("#shipper_fname").val()!='' ||
	$("#shipper_lname").val()!='' ||
	$("#shipper_company").val()!='' ||
	$("#shipper_email").val()!='') && !tempDisableBeforeUnload)
	{
		  return 'Important: You will lose any changes you have made.';
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
				alert(res.message);
			},
			error: function() {
				alert('Failed to calculate Quick Price');
			},
			complete: function() {
				// $("body").nimbleLoader("hide");
			}

		});
	}
    

    function applySearch(num) {
        var acc_obj = acc_data[num];
        switch (acc_type) {
            case <?=Account::TYPE_SHIPPER?>:
                $("#shipper_fname").val(acc_obj.first_name);
                $("#shipper_lname").val(acc_obj.last_name);
                $("#shipper_company").val(acc_obj.company_name);
                $("#shipper_email").val(acc_obj.email);
                $("#shipper_phone1").val(acc_obj.phone1);
                $("#shipper_phone2").val(acc_obj.phone2);
                $("#shipper_mobile").val(acc_obj.cell);
                $("#shipper_fax").val(acc_obj.fax);
                $("#shipper_address1").val(acc_obj.address1);
                $("#shipper_address2").val(acc_obj.address2);
                $("#shipper_city").val(acc_obj.city);
                $("#shipper_country").val(acc_obj.coutry);
                $("#shipper_state").val(acc_obj.state);
                $("#shipper_zip").val(acc_obj.zip);
				$("#shipper_type").val(acc_obj.shipper_type);
				$("#shipper_hours").val(acc_obj.hours_of_operation);
                $("#account_payble_contact").val(acc_obj.account_payble_contact);
				  typeselected();
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
$("#" + acc_location + "_id").val(acc_obj.id);

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
		$("input[name='"+location+"_phone1_ext']").val($("input[name='shipper_phone1_ext']").val());

	    $("input[name='"+location+"_phone2']").val($("input[name='shipper_phone2']").val());
		$("input[name='"+location+"_phone2_ext']").val($("input[name='shipper_phone2_ext']").val());
		
	    $("input[name='"+location+"_mobile']").val($("input[name='shipper_mobile']").val());

        $("input[name='"+location+"_fax']").val($("input[name='shipper_fax']").val());

		$("input[name='"+location+"_address2']").val($("input[name='shipper_address2']").val());
		$("select[name='"+location+"_type']").val($("select[name='shipper_type']").val());
		$("input[name='"+location+"_hours']").val($("input[name='shipper_hours']").val());

    }

}

function typeselected() {
		if($("#shipper_type").val() == "Commercial"){
	      $('#shipper_company-span').show();
		  $('#account_payble_contact_label_div').show();
		  $('#account_payble_contact_div').show();
		}
		else{
		  $('#shipper_company-span').hide();
		  $('#account_payble_contact_label_div').hide();
		  $('#account_payble_contact_div').hide();
		}
		
	}
	
function paid_by_ach_selected() {
		if($("#balance_paid_by").val() == 24){
	      $('#fee_type_label_div').show();
		  $('#fee_type_div').show();
		}
		else{
		  $('#fee_type_label_div').hide();
		  $('#fee_type_div').hide();
		}
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
                    //document.location.reload();
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
    $(document).ready(function () {
		var redirectUrl = null;						
		typeselected();
		paid_by_ach_selected();
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
function flipLocation() {

    if (confirm("Are you sure you want to flip location information?")) {
		
		var origin_address1 = $("input[name='origin_address1']").val();
		var origin_address2 = $("input[name='origin_address2']").val();
		var origin_city = $("input[name='origin_city']").val();
	    var origin_state = $("select[name='origin_state']").val();
	    var origin_zip = $("input[name='origin_zip']").val();
	    var origin_country = $("select[name='origin_country']").val();
		var origin_type = $("select[name='origin_type']").val();
		var origin_hours = $("input[name='origin_hours']").val();
		var origin_contact_name = $("input[name='origin_contact_name']").val();
		var origin_contact_name2 = $("input[name='origin_contact_name2']").val();
		var origin_company_name = $("input[name='origin_company_name']").val();
		var origin_auction_name = $("input[name='origin_auction_name']").val();
		
		var origin_booking_number = $("input[name='origin_booking_number']").val();

	    var origin_buyer_number = $("input[name='origin_buyer_number']").val(); 

		
		var origin_phone1 = $("input[name='origin_phone1']").val();
		var origin_phone1_ext = $("input[name='origin_phone1_ext']").val();
        var origin_phone2 = $("input[name='origin_phone2']").val();
		var origin_phone2_ext = $("input[name='origin_phone2_ext']").val();
		var origin_phone3 = $("input[name='origin_phone3']").val();
		var origin_phone3_ext = $("input[name='origin_phone3_ext']").val();
        var origin_phone4 = $("input[name='origin_phone4']").val();
		var origin_phone4_ext = $("input[name='origin_phone4_ext']").val();
		
	    var origin_mobile = $("input[name='origin_mobile']").val();
		var origin_mobile2 = $("input[name='origin_mobile2']").val();

        var origin_fax = $("input[name='origin_fax']").val();
		var origin_fax2 = $("input[name='origin_fax2']").val();
		
		
		
		//------------flip destination------------
		
		
		$("input[name='origin_address1']").val($("input[name='destination_address1']").val());
		$("input[name='origin_address2']").val($("input[name='destination_address2']").val());
		$("input[name='origin_city']").val($("input[name='destination_city']").val());
	    $("select[name='origin_state']").val($("select[name='destination_state']").val());
	    $("input[name='origin_zip']").val($("input[name='destination_zip']").val());
	    $("select[name='origin_country']").val($("select[name='destination_country']").val());
		$("select[name='origin_type']").val($("select[name='destination_type']").val());
		$("input[name='origin_hours']").val($("input[name='destination_hours']").val());
		$("input[name='origin_contact_name']").val($("input[name='destination_contact_name']").val());
		$("input[name='origin_contact_name2']").val($("input[name='destination_contact_name2']").val());
		$("input[name='origin_company_name']").val($("input[name='destination_company_name']").val());
		$("input[name='origin_auction_name']").val($("input[name='destination_auction_name']").val());
		
		$("input[name='origin_booking_number']").val($("input[name='destination_booking_number']").val());

	    $("input[name='origin_buyer_number']").val($("input[name='destination_buyer_number']").val()); 

		
		$("input[name='origin_phone1']").val($("input[name='destination_phone1']").val());
		$("input[name='origin_phone1_ext']").val($("input[name='destination_phone1_ext']").val());
        $("input[name='origin_phone2']").val($("input[name='destination_phone2']").val());
		$("input[name='origin_phone2_ext']").val($("input[name='destination_phone2_ext']").val());
		$("input[name='origin_phone3']").val($("input[name='destination_phone3']").val());
		$("input[name='origin_phone3_ext']").val($("input[name='destination_phone3_ext']").val());
        $("input[name='origin_phone4']").val($("input[name='destination_phone4']").val());
		$("input[name='origin_phone4_ext']").val($("input[name='destination_phone4_ext']").val());
		
	    $("input[name='origin_mobile']").val($("input[name='destination_mobile']").val());
		$("input[name='origin_mobile2']").val($("input[name='destination_mobile2']").val());

        $("input[name='origin_fax']").val($("input[name='destination_fax']").val());
		$("input[name='origin_fax2']").val($("input[name='destination_fax2']").val());

       //------------flip origin------------
	
		$("input[name='destination_address1']").val(origin_address1);
		$("input[name='destination_address2']").val(origin_address2);
		$("input[name='destination_city']").val(origin_city);
	    $("select[name='destination_state']").val(origin_state);
	    $("input[name='destination_zip']").val(origin_zip);
	    $("select[name='destination_country']").val(origin_country);
		$("select[name='destination_type']").val(origin_type);
		$("input[name='destination_hours']").val(origin_hours);
		$("input[name='destination_contact_name']").val(origin_contact_name);
		$("input[name='destination_contact_name2']").val(origin_contact_name2);
		$("input[name='destination_company_name']").val(origin_company_name);
		$("input[name='destination_auction_name']").val(origin_auction_name);
		
		$("input[name='destination_booking_number']").val(origin_booking_number);

	    $("input[name='destination_buyer_number']").val(origin_buyer_number); 
		
		$("input[name='destination_phone1']").val(origin_phone1);
		$("input[name='destination_phone1_ext']").val(origin_phone1_ext);
        $("input[name='destination_phone2']").val(origin_phone2);
		$("input[name='destination_phone2_ext']").val(origin_phone2_ext);
		$("input[name='destination_phone3']").val(origin_phone3);
		$("input[name='destination_phone3_ext']").val(origin_phone3_ext);
        $("input[name='destination_phone4']").val(origin_phone4);
		$("input[name='destination_phone4_ext']").val(origin_phone4_ext);
		
	    $("input[name='destination_mobile']").val(origin_mobile);
		$("input[name='destination_mobile2']").val(origin_mobile2);

        $("input[name='destination_fax']").val(origin_fax);
		$("input[name='destination_fax2']").val(origin_fax2);
	
    }

}
	</script>
    
 <div id="note_edit_form" style="display:none;">
	<textarea style="width: 95%;height:100px;" class="form-box-textarea" name="note_text"></textarea>
</div>  
<div style="padding-top:15px;">
    <?php include('order_menu.php');  ?>
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
    <p style="margin-left: 10%;font-size: 15px;">Are you still there? (session is about to expire in <span style="color: #f00;font-weight: bold;">180</span> seconds)</p><br/>
	<div>
	<input type="button" value="Yes" onclick="expiringEditYes()" style="margin-left: 35%; width: 65px; height: 29px;color: #008ec2;"><input type="button" value="No" onclick="expiringEditNo()" style="width: 65px; margin-left: 3%;height: 29px;color: #008ec2;">
	</div>
	
</div>
<div id="blockedEditAlertId" style="display: none;">
    <p></p>
</div>
<h3>Edit Order #<?= $this->entity->getNumber() ?></h3>
Complete the form below and click Save Order when finished. Required fields are marked with a <span
    style="color:red;">*</span><br/>

	<?php $assigned = $this->entity->getAssigned(); ?>
	Assigned to: <strong><?= $assigned->contactname ?></strong><br /><br/>
<div id="order_edit_form_block" style="position: relative;">
<div id="order_edit_disabler">&nbsp;</div>
<?php if (!$this->entity->isBlocked()) { ?>
<form id="save_order_form" action="<?= getLink('orders/edit/id/' . $this->entity->id) ?>" method="post" onsubmit="javascript:tempDisableBeforeUnload = true;">
<?php } ?>
<input type="hidden" id="order_id" name="order_id" value="<?= $this->entity->id ?>"/>
<input type="hidden" id="post_to_cd" name="post_to_cd" value="<?= $this->ask_post_to_cd ? "1" : "0" ?>"/>

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
                <td><?php if($accessEdit==1){?><?= functionButton('Select Shipper', 'selectShipper()') ?><?php }else{?><?= functionButton('Select Shipper', '') ?><?php }?></td>
                <td>@shipper_email@</td>
                <td>@shipper_address1@</td>
            </tr>
            <tr>
                <td>@shipper_fname@</td>
                <td>@shipper_phone1@@shipper_phone1_ext@</td>
                <td>@shipper_address2@</td>
            </tr>
            <tr>
                <td>@shipper_lname@</td>
                <td>@shipper_phone2@@shipper_phone2_ext@</td>
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
				<?php if($this->entity->source_id !=''){?>
				   <td>@source_id@</td>
               <?php }else{?> 
                  <td>@referred_by@</td>
				<?php }?>
				<td id="account_payble_contact_label_div">@account_payble_contact@</td>
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
              <td colspan="6">
                  <table width="500" cellpadding="1" cellspacing="1">
                    <tr>
                      <td>
				  <?php 
						$checkedLocation1 = "";
					   if(!empty($_POST)){
						if($_POST['save_location1']==1)
						 $checkedLocation1 = " checked=checked ";
					   }
					   else
						 $checkedLocation1 = " checked=checked ";
				  ?>
					<input type="checkbox" name="save_location1" id="save_location1" value="1" <?php print $checkedLocation1;?>/>
                    <input type="hidden" name="origin_id" id="origin_id" value="0" />
					<label for="save_location1">Save</label>
				</td>
				<td>
                 <table width="100%" cellpadding="1" cellspacing="1">
                  <tr>
                     <td><?= functionButton('Select Location', "selectTerminal('origin');") ?></td>
                     <td><b>OR</b>&nbsp;&nbsp;&nbsp;<span class="like-link" onclick="setLocationSameAsShipperOrder('origin')">same as shipper</span>&#8203;</td>
                     <td><?= functionButton('Flip Location', "flipLocation();") ?></td>
                  </tr>
                 </table> 
                 </td>
               </tr>
              </table>
             </td>    
			</tr>
			<tr>
				<td>@origin_address1@</td>
				<td>@origin_contact_name@</td>
				<td>@origin_phone1@@origin_phone1_ext@</td>
                <td>@origin_mobile@</td>
                
			</tr>
			<tr>
				<td>@origin_address2@</td>
				<td>@origin_contact_name2@</td>
				<td>@origin_phone2@@origin_phone2_ext@</td>
                <td>@origin_mobile2@</td>
			</tr>
			<tr>
				<td>@origin_city@</td>
				<td>@origin_company_name@</td>
				<td>@origin_phone3@@origin_phone3_ext@</td>
                <td >@origin_fax@</td>
			</tr>
			<tr>
				<td>@origin_state@@origin_zip@</td><div id="notes_container"></div>
				<td>@origin_auction_name@</td>
				<td>@origin_phone4@@origin_phone4_ext@</td>
                <td >@origin_fax2@</td>
			</tr>
			<tr>
				<td>@origin_country@</td>
                <td>@origin_booking_number@</td>
                <td colspan="2">&nbsp;</td>
                <td colspan="2">&nbsp;</td>
				
			</tr>
			<tr>
				<td>@origin_type@</td>
                <td>@origin_buyer_number@</td>
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
				<td colspan="6">
                  <table width="500" cellpadding="1" cellspacing="1">
                    <tr>
                      <td>
				  <?php 
						$checkedLocation2 = "";
					   if(!empty($_POST)){
						if($_POST['save_location2']==1)
						 $checkedLocation2 = " checked=checked ";
					   }
					   else
						 $checkedLocation2 = " checked=checked ";
				  ?>
					<input type="checkbox" name="save_location2" id="save_location2" value="1" <?php print $checkedLocation2;?>/>
                    <input type="hidden" name="destination_id" id="destination_id" value="0" />
					<label for="save_location2">Save</label>
				</td>
				<td>
                    <table width="100%" cellpadding="1" cellspacing="1">
                      <tr>
                         <td><?= functionButton('Select Location', "selectTerminal('destination');") ?></td>
                         <td><b>OR</b>&nbsp;&nbsp;&nbsp;<span class="like-link" onclick="setLocationSameAsShipperOrder('destination')">same as shipper</span>&#8203;</td>
                         <td><?= functionButton('Flip Location', "flipLocation();") ?></td>
                      </tr>
                     </table>
                </td>
               </tr>
             </table>
            </td>  
			</tr>
			<tr>
				<td>@destination_address1@</td>
				<td>@destination_contact_name@</td>
				<td>@destination_phone1@@destination_phone1_ext@</td>
                <td>@destination_mobile@</td>
			</tr>
			<tr>
				<td>@destination_address2@</td>
				<td>@destination_contact_name2@</td>
				<td>@destination_phone2@@destination_phone2_ext@</td>
                <td>@destination_mobile2@</td>
			</tr>
			<tr>
				<td>@destination_city@</td>
				<td>@destination_company_name@</td>
				<td>@destination_phone3@@destination_phone3_ext@</td>
                <td>@destination_fax@</td>
			</tr>
			<tr>
				<td>@destination_state@@destination_zip@</td>
				<td >@destination_auction_name@</td>
				<td>@destination_phone4@@destination_phone4_ext@</td>
                <td>@destination_fax2@</td>
			</tr>
			<tr>
				<td>@destination_country@</td>
				<td>@destination_booking_number@</td>
				<td colspan="2">&nbsp;</td>
                <td colspan="2">&nbsp;</td>
			</tr>
			
			<tr>
				<td>@destination_type@</td>
				<td>@destination_buyer_number@</td>
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
            <tr>
                <td>@avail_pickup_date@</td>
                <td rowspan="5" valign="top">
                    <table cellspacing="0" cellpadding="0" border="0">
                       <tr>
							<td valign="top">@notes_from_shipper@<br/><i><strong>(Above notes will always appear on the dispatch sheet)</strong></i></td>
						</tr>
						<tr>
							<td valign="top">@notes_for_shipper@<br/><div style="float:left;"><div style="float:left;"><i><strong>(Maximum character allowed is <div id="charNum" style="float:right;">&nbsp;60)</div></i></div></td>
						</tr>

						<tr>
							<td>&nbsp;</td>
							<td><!--@include_shipper_comment@-->  <input type="hidden" name="include_shipper_comment" value="1" /></td>
						</tr>
					</table>
				</td>
			</tr>
<!--			<tr>-->
<!--				<td valign="top">@shipping_vehicles_run@</td>-->
<!--			</tr>-->
			<tr>
                <td valign="top">@load_date_type@ @load_date@</td>
            </tr>
            <tr>
                <td valign="top">@delivery_date_type@ @delivery_date@</td>
            </tr>
<!--            <tr>-->
<!--                <td valign="top">@shipping_vehicles_run@</td>-->
<!--            </tr>-->
            <tr>
                <td valign="top">@shipping_ship_via@</td>
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
                <td>Vin #</td>
                <!--td>Lot #</td-->
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

<td><input type="text" name="vin[<?php print $vehicle->id;?>]" value="<?= $vehicle->vin ?>" id="vin_<?php print $vehicle->id;?>"  /></td>
                        
                        <td><input type="text" name="vehicle_tariff[<?php print $vehicle->id;?>]" value="<?= $vehicle->tariff ?>" id="vehicle_tariff_<?php print $vehicle->id;?>" onkeyup="updatePricingInfo();" />

                        </td>
                        <td><input type="text" name="vehicle_deposit[<?php print $vehicle->id;?>]" value="<?= $vehicle->deposit ?>" id="vehicle_tariff_<?php print $vehicle->id;?>" onkeyup="updatePricingInfo();"/>
                        <input type="hidden" name="vehicle_id[]" value="<?php print $vehicle->id;?>"  />
                        </td>
	                    <td><?= ($vehicle->inop == '1')?'Yes':'No' ?></td>

                        <td align="center" class="grid-body-right" width="60">
                       <?php  if($accessEdit==1){?>
                            <?php if (!$this->entity->isBlocked()) { ?>
                               <img src="<?= SITE_IN ?>images/icons/copy.png" alt="Copy" title="Copy"
                                     onclick="copyVehicle(<?= $vehicle->id ?>)" class="action-icon"/>
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
<div class="order-info" style="float:none;">
    <p class="block-title">Payment Information</p>

    <div>
	<table width="1200px">
    <tr>
	    <td>Total Tariff </td>
        <td><span id="total_tariff">@total_tariff@</span>&nbsp;<span class="grey-comment">(Edit carrier pay and deposit under the "Vehicle Information" section)</span></td>
        <td>@balance_paid_by@</td>
    </tr>
	<tr>
                        <td colspan="2"></td>
						<td id="fee_type_label_div" >@fee_type@</td>
    </tr>
    <tr>
	    <td>Required Deposit</td>
        <td><span id="total_deposit">@total_deposit@</span>&nbsp;<span class="grey-comment">(Edit deposit under the "Vehicle Information" section)</span></td>
        <td>@customer_balance_paid_by@</td>
    </tr>
    <tr>
	    <td valign="top">Carrier Pay</td>
        <td valign="top"><span id="carrier_pay">@carrier_pay@</span>&nbsp;<span class="grey-comment">(Edit carrier pay under the "Vehicle Information" section)</span></td>
        <td valign="top">@payments_terms@</td>
    </tr>
</table>
       
	</div>
</div>
<br/>
<!----
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
					<tr></tr>

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
                        <td>@payments_terms@</td>
                    </tr>

                </table>
            </div>
        </div>
      </td>
    </tr>
  </table>
</div>
<br/>
---->
<div class="order-info" style="float:none;">
    <p class="block-title">Credit Card Information</p>
    <div>
        <table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table order-edit"
               style="white-space:nowrap;">
            <tr>
                <td>
	                <table class="no-padding">
		                <tr>
			                <td><!--input type="checkbox" id="save_card" name="save_card" value="1"/--></td>
			                <td style="padding-bottom:5px"><label for="save_shipper"><!--Save--></label></td>
                            <td align="left" style="padding-left:5px" >@auto_payment@</td>
		                </tr>
	                </table>
                </td>
                <td>&nbsp;</td>
                <td>@e_cc_type@</td>
                <td>@e_cc_address@</td>

            </tr>




            <tr>
                <td>@e_cc_fname@</td>
                <td>@e_cc_number@</td>
                <td>@e_cc_city@</td>
            </tr>


            <tr>
                <td>@e_cc_lname@</td>
                <td>@e_cc_cvv2@ <img src="<?=SITE_IN?>images/icons/cards.gif" alt="Card Types" width="129" height="16" style="vertical-align:middle;" /></td>
                <td>@e_cc_state@</td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
                <td>@e_cc_month@ / @e_cc_year@</td>
                <td>@e_cc_zip@</td>
            </tr>
            <!--tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>@shipper_fax@</td>
                <td>@shipper_country@</td>
            </tr-->
        </table>
    </div>
</div>
<br/>



<!--div class="order-info" style="float:none;">
    <p class="block-title">Additional Information</p>

    <div>
        <table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table" style="white-space:nowrap;">
            <tr>
                <td>@referred_by@</td>
            </tr>
        </table>
    </div>
</div>
<br/>

<div class="order-info" style="float:none;">
	<p class="block-title">Internal Notes</p>
	<div>
		<table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table" style="white-space:nowrap;">
			<tr>
				<td align="center">@note_to_shipper@</td>
			</tr>
		</table>
	</div>
</div>
-->
<?php $notes = $this->notes; ?>
<div class="order-info" style="width: 97%;float: left;margin-top: 10px;">
	<p class="block-title">Internal Notes</p>
	<div>
	<?php //if ($this->entity->status != Entity::STATUS_ARCHIVED) : ?>
		<textarea class="form-box-textarea" style="width: 1160px; height: 52px;" maxlength="1000" id="internal_note"></textarea>

         <div style="float:left; padding:2px;">
		 Quick Notes&nbsp;
		 <select name="quick_notes" id="quick_notes" onchange="addQuickNote();">
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
    <? if ($this->ask_post_to_cd) { ?>
  <div style="float:right;">
		    <table cellpadding="0" cellspacing="0" border="0">
                <tr><td colspan="4">@match_carrier@</td></tr>
                <tr><td colspan="4">&nbsp;</td></tr>
			    <tr>
				    
				    <td style="padding-left: 15px;"><?= postToCDButtons(SITE_IN . "application/orders/show/id/" . $this->entity->id, "Save") ?></td>
			    </tr>
		    </table>
	    </div>
        <?php //print postToCDButtons(SITE_IN . "application/orders/show/id/" . $this->entity->id, "Save") ?>
    <? } else { ?>
	    <div style="float:right;">
		    <table cellpadding="0" cellspacing="0" border="0">
                <tr><td colspan="4">@match_carrier@</td></tr>
                <tr><td colspan="4">&nbsp;</td></tr>
			    <tr>
				    <td>
					    <!--input type="hidden" name="send_email" value="0" id="co_send_email"/-->
					    <?php //print functionButton("Save &amp; Email", 'saveAndEmail();'); ?>
				    </td>
				    <td style="padding-left: 15px;"><?= submitButtons(SITE_IN."application/orders/show/id/" . $this->entity->id, "Save") ?></td>
			    </tr>
		    </table>
	    </div>
    <? } ?>
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
    <tbody>
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr>
        <td>
        <!--input type="hidden" name="send_email" value="0" id="co_send_email">
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
            </div-->
        </td>
        <td style="padding-left: 15px;"> <div class="form-box-buttons">

        <span id="submit_button-submit-btn" style="-webkit-user-select: none;"><input type="button" id="submit_button" value="Save" onclick="disableBtn();" style="-webkit-user-select: none;"></span>
        &nbsp;&nbsp;&nbsp;
           <input type="button" value="Cancel" onclick="document.location.href='/application/orders/show/id/<?php print $this->entity->id;?>'" style="-webkit-user-select: none;">
        </div>
        <script type="text/javascript">//<![CDATA[
             function disableBtn(){setTimeout(function(){$('#submit_button-submit-btn').html('<input type="button" value="Not Authorized..." disabled="disabled" />');},1);}
        //]]></script>
        </td>
    </tr>
    
     <tr><td colspan="2">&nbsp;</td></tr>
      <tr><td colspan="2">&nbsp;</td></tr>
    
    </tbody></table>
</div>
</div>
<div class="clear"></div>

    <?php
}?>
<script type="text/javascript">
	function saveAndEmail() {
		$("#co_send_email").val("1");
		$("#submit_button").click();
	}

    function checkPostToCD() {
        <? if ($this->ask_post_to_cd){?>
        $("#acc_entity_dialog").dialog('option', 'title', 'Repost order?').dialog('open');
        <? }else{  ?>
        $("#save_form").submit();
        <? } ?>
    }
</script>