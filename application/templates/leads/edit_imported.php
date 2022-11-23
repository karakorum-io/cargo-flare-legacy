<script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script>

<style type="text/css">
.col-8.order-2
{
	display: none;
}
.card-title
{
	background-color: #f7f8fa;
    color: #222;
    padding: 8px 20px !important;
    border-bottom: 1px solid #ebedf2;
}
.card-title .text_show
{
	color: #222;
}
select#shipper_state
{
	float:left;
	margin-right:15px;
}
.order-edit .form-box-textfield
{
	width: 210px;
}
.kt-link--success
{
	color:#32adf3;
}
h3.details
{
    padding:22px 0 0;
    width:100%;
    font-size:20px;
}
</style>
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
			Swal.fire('Invalid Origin Information');
			return;
		}
		if (data.destination_city == '' || data.destination_state == '' || data.destination_zip == '') {
			Swal.fire('Invalid Destination Information');
			return;
		}
		if (data.shipping_est_date == '') {
			Swal.fire('Invalid Shipping Date');
			return;
		}
		if (data.shipping_ship_via == '') {
			Swal.fire('You should specify "Ship Via" field');
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
				Swal.fire(res.message);
			},
			error: function() {
				Swal.fire('Failed to calculate Quick Price');
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
                    //document.location.reload();
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

     //   $(".datepicker").datepicker();

     $('.hasDatepicker').datepicker();


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

        				Swal.fire("Can't send email. Try again later, please");

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
					Swal.fire("Can't discard note. Try again later, please");
				}
				busy = false;
			},
			error: function(result) {
				Swal.fire("Can't discard note. Try again later, please");
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
					Swal.fire("Can't save note. Try again later, please");
				}
				busy = false;
			},
			error: function(result) {
				$("#internal_note").val(text);
				Swal.fire("Can't save note. Try again later, please");
				busy = false;
			}
		});
	}
	
/*	function delInternalNote(id) {
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
						Swal.fire("Can't delete note. Try again later, please");
					}
					busy = false;
				},
				error: function(result) {
					Swal.fire("Can't delete note. Try again later, please");
					busy = false;
				}
			});
		}
	}*/

	// 
		function delInternalNote(id)
	 {
			Swal.fire({
			title: 'Are you sure?',
			text: "Are you sure want to delete this note?!",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, delete it!'
			}).then((result) => {
			if (result.value) {
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
							swal.fire("Can't delete note. Try again later, please");
						}
						busy = false;
					},
					error: function(result) {
						swal.fire("Can't delete note. Try again later, please");
						busy = false;
					}
			});

		}
	})

}

// 


function editInternalNote(id)
{
	var text = $.trim($("#note_"+id+"_text").text());
	 $("#note_edit_form textarea").val(text);
	 $("#edit_save").val(id);
	 $("#note_edit_form").modal();

}


	function note_edit_form_send(id)
	{

		console.log(id);
	    var text = $.trim($("#note_"+id+"_text").text());
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


</script>

<!-- <div id="note_edit_form" style="display:none;">
	<textarea style="width:95%;height:100px;" class="form-box-textarea" name="note_text"></textarea>
</div>   -->

<!-- Modal -->
<div class="modal fade" id="note_edit_form" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle45" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLongTitle45">Edit Internal Note</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				</button>
			</div>
			<div class="modal-body">
				<textarea style="width: 95%;height:100px;" class="form-box-textarea" name="note_text"></textarea>
				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-dark btn-sm" data-dismiss="modal">Close</button>
				<button type="button" id="edit_save" value="" class="btn_dark_green btn-sm btn" onclick="note_edit_form_send(this.value)">Save </button>
			</div>
		</div>
	</div>
</div>

<?php include('lead_menu_imported.php');  ?>

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

<div id="checkEditDueId" style="display:none;">
	<br/>
	<p style="margin-left:10%;font-size:15px;">Are you still there? (session is about to expire in <span style="color: #f00;font-weight: bold;">60</span> seconds)</p><br/>
	<div>
		<input type="button" value="Yes" onclick="expiringEditYes()" style="margin-left: 35%; width: 65px; height: 29px;color: #008ec2;"><input type="button" value="No" onclick="expiringEditNo()" style="width: 65px; margin-left: 3%;height: 29px;color: #008ec2;">
	</div>
</div>
<div id="blockedEditAlertId" style="display: none;">
	<p></p>
</div>


	<div class="col-sm-3" style="margin-top:-65px;margin-bottom:15px;">
		<h3 class="details">Edit Lead #<?= $this->entity->number ?> Detail</h3>
		<?php $assigned = $this->entity->getAssigned(); ?>
		<strong>Assigned to : <?= $assigned->contactname ?></strong>
	</div>
	
</div>
	
	<div class="mb-3">
		Complete the form below and click Save Order when finished. Required fields are marked with a <span style="color:red;">&nbsp*</span>
	</div>
	
	<?php if (!$this->entity->isBlocked()) { ?>
	<form id="save_order_form" action="<?= getLink('leads/editimported/id/' . $this->entity->id) ?>" method="post" onsubmit="javascript:tempDisableBeforeUnload = true;">
	<?php } ?>
	<input type="hidden" id="order_id" name="order_id" value="<?= $this->entity->id ?>"/>
	<input type="hidden" id="post_to_cd" name="post_to_cd" value="<?= $this->ask_post_to_cd ? "1" : "0" ?>"/>
	
		<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
			
			<div class="hide_show">
				<h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Shipper Information</h3>
			</div>
			
			<div id="shipper_information_info_1" class="pt-3" style="padding-left:20px;padding-right:20px;">
				<input type="hidden" id="save_shipper" name="save_shipper" value="1"/>
				<div class="row">
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							
						</div>
					</div>
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							@shipper_email@
						</div>
					</div>
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							@shipper_address1@
							<div id="suggestions-box-shipper" class="suggestions"></div>
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
						<div class="new_form-group">
							@shipper_phone1@
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
						<div class="new_form-group">
							@shipper_phone2@
							
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
						</div>
					</div>
	
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							@shipper_mobile@							
						</div>
					</div>
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							<div id="shipper_state_div">
								@shipper_state@
							</div>
							@shipper_zip@
							<div id="notes_container"></div>
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
							@shipper_mobile@
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
							@shipper_hours@
						</div>
					</div>
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							<?php  
							if( $this->entity->source_id !='' ||
							(
								$this->entity->type==1 && 
								$this->entity->status != Entity::STATUS_LQUOTED && 
								$this->entity->status != Entity::STATUS_LFOLLOWUP && 
								$this->entity->status != Entity::STATUS_LEXPIRED && 
								$this->entity->status != Entity::STATUS_LAPPOINMENT
							)){?>
								@source_id@
							<?php }else{?> 
								@referred_by@
							<?php }?>
						</div>
					</div>
					<div class="col-12 col-sm-4">
						<div class="new_form-group">

						</div>
					</div>
				</div>
			</div>
			
		</div>		
		
		
		<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
			
			<div class="hide_show">
				<h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Shipper Information</h3>
			</div>
			
			<div id="shipper_information_info_2" class="pt-3" style="padding-left:20px;padding-right:20px;">
				<div class="row">
					<div class="col-6">
						<div class="new_form-group">
							@est_ship_date@
						</div>
					</div>

					<div class="col-6">
						<div class="new_form-group">
							@shipping_ship_via@
						</div>
					</div>
				</div>
			</div>
			
		</div>
		
		
		<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
			
			<div class="hide_show">
				<h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Route Information</h3>
			</div>
			
			<div id="route_information_new_info" style="padding-left:20px;padding-right:20px;">
				<div class="row">
					<div class="col-6 col-sm-6">
						<h5 class="mb-3 mt-3">From</h5>
						<div class="new_form-group">
							@origin_city@
						</div>
					</div>
					<div class="col-6 col-sm-6">
						<h5 class="mb-3 mt-3">To</h5>
						<div class="new_form-group">
							@destination_city@
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-6 col-sm-6">
						<div class="new_form-group">
							<div class="row">
								<div class="col-8" id="origin_state_div">
									@origin_state@
								</div>
								<div class="col-4 input_wdh_100_per">
									@origin_zip@
								</div>
							</div>
							<div id="notes_container"></div>

						</div>
					</div>
					
					<div class="col-6 col-sm-6">
						<div class="new_form-group">
							<div class="row">
								<div class="col-8" id="destination_state_div">
									@destination_state@
								</div>
								<div class="col-4 input_wdh_100_per">
									@destination_zip@
								</div>
							</div>


						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-6 col-sm-6">
						<div class="new_form-group">
							@origin_country@
						</div>
					</div>
					<div class="col-6 col-sm-6">
						<div class="new_form-group">
							@destination_country@
						</div>
					</div>
				</div>
			</div>
			
		</div>
		
		

		<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
			
			<div class="hide_show">
				<h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Vehicle Information</h3>
			</div>
			
			<div id="route_information_new_info" class="pt-4 pb-4" style="padding-left:20px;padding-right:20px;">
				<table class="table table-bordered" id="vehicles-grid">
					<thead>
						<tr>
							<th>ID</th>
							<th>Year</th>
							<th>Make</th>
							<th>Model</th>
							<th>Type</th>
							<th>Vin #</th>
							<th>Total Tariff</th>
							<th>Deposit</th>
							<th>Inop</th>
							<th>Actions</th>
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
									<td><input type="text"  class="form-control" name="vin[<?php print $vehicle->id;?>]" value="<?= $vehicle->vin ?>" id="vin_<?php print $vehicle->id;?>"  /></td>
									<td><input type="text" name="vehicle_tariff[<?php print $vehicle->id;?>]" value="<?= $vehicle->tariff ?>" class="form-control"  id="vehicle_tariff_<?php print $vehicle->id;?>" onkeyup="updatePricingInfo();" />

									</td>
									<td><input type="text" class="form-control" name="vehicle_deposit[<?php print $vehicle->id;?>]" value="<?= $vehicle->deposit ?>" id="vehicle_tariff_<?php print $vehicle->id;?>" onkeyup="updatePricingInfo();"/>
										<input type="hidden" name="vehicle_id[]" value="<?php print $vehicle->id;?>"  />
									</td>
									<td><?= ($vehicle->inop == '1')?'Yes':'No' ?></td>

									<td align="center" class="grid-body-right" >
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
							<tr>
								<td colspan="8" align="center"><i>No Vehicles</i></td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>

				<?php if($accessEdit==1){ ?>
					<?php if (!$this->entity->isBlocked()) { ?>
						<?= functionButton('Add Vehicle', 'addVehicle()') ?>
					<?php } ?>

					<?php if ($this->isAutoQuoteAlowed) { ?>
						<?= functionButton('Quick Price', 'quickPrice()') ?>
					<?php } ?>

				<?php } else { ?>

					<?php if (!$this->entity->isBlocked()) { ?>
						<?= functionButton('Add Vehicle', '') ?>
					<?php } ?>

					<?php if ($this->isAutoQuoteAlowed) { ?>
						<?= functionButton('Quick Price', '') ?>
					<?php } ?>

				<?php } ?>
			</div>
		</div>
		
		
		<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
			
			<div class="hide_show">
				<h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Internal Notes</h3>
			</div>
			
			<div id="internal_notes_new_info" class="pt-4 pb-4" style="padding-left:20px;padding-right:20px;">
				
				<?php $notes = $this->notes; ?>
				<div class="row">
					<div  class=" col-lg-12">
						<label></label>
						<textarea class="form-box-textarea form-control" class="" maxlength="1000" id="internal_note"></textarea>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-4 quick_note_label_left_info_new">
						<label>Quick Note </label>
						<select name="quick_notes" class="form-control" id="quick_notes" onchange="addQuickNote();">
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

					<div class="col-lg-4 quick_note_label_left_info_new">
						<label>Priority</label>
						<select name="priority_notes" class="form-control" id="priority_notes"  >
							<option value="1">Low</option>
							<option value="2">High</option>
						</select>
					</div>
					<div class="col-lg-4 pull-right" style="margin-top: 12px">
						<?= functionButton('Add Note', 'addInternalNote()') ?>
					</div>
				</div>

				<div class="row">

					<table class="table table-bordered" id="internal_notes_table" style="margin-top: 20px">
						<thead>
							<tr>
								<th>Date</th>
								<th>Note</th>
								<th>User</th>
								<th>Action</th>
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
							){ ?>
							<tr>
								<td style="white-space:nowrap;"  class="grid-body-left" <?php if($note->priority==2){?> style="color:#FF0000"<?php }?>><?= $note->getCreated("m/d/y h:i a") ?></td>
								<td id="note_<?= $note->id ?>_text" style=" <?php if($note->discard==1){ ?>text-decoration: line-through;<?php }?><?php if($note->priority==2){?>color:#FF0000;<?php }?>"><?php if($note->system_admin == 1 || $note->system_admin == 2){?><b><?= $note->getText() ?></b><?php }elseif($note->priority==2){?><b style="font-size:12px;"><?= $note->getText() ?></b><?php }else{?><?= $note->getText() ?><?php }?></td>
								<td style="text-align: center;" <?php if($note->priority==2){?>style="color:#FF0000"<?php }?>><a href="mailto:<?= $email ?>"><?= $contactname ?></a></td>
								<td class="grid-body-right" style="white-space: nowrap;" <?php if($note->priority==2){?>style="color:#FF0000"<?php }?>>
									<?php if (!$this->entity->readonly) : ?>

									<?php 
									if (($_SESSION['member']['access_notes'] == 0 ) ||
									($_SESSION['member']['access_notes'] == 1 && ($note->sender_id == (int)$_SESSION['member_id']))
									|| $_SESSION['member']['access_notes'] == 2
									)
									{
									if($note->sender_id == (int)$_SESSION['member_id']  && $note->system_admin == 0 ){
									?>
									<img src="<?= SITE_IN ?>images/icons/strike.png" alt="Discard" title="Discard" width="16" height="16" class="action-icon edit-note" onclick="discardNote(<?= $note->id ?>)"/>	
									<?php } 
									if($note->system_admin == 0 && $_SESSION['member']['access_notes'] != 0 ){ ?>  
									<img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit" width="16" height="16" class="action-icon edit-note" onclick="editInternalNote(<?= $note->id ?>)"/>
									<img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete" width="16" height="16" class="action-icon delete-note" onclick="delInternalNote(<?= $note->id ?>)"/>

									<?php }
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

					<div class="col-lg-12" >
						<div class="row" style="float: right;">
							<?php
							if($accessEdit==1){
							if (!$this->entity->isBlocked()) { ?>
								<input type="hidden" name="send_email" value="0" id="co_send_email"/>
								<?= submitButtons(SITE_IN."application/leads/showimported/id/" . $this->entity->id, "Save") ?>
							</form>

							<?php 
							}
							} else {
							?>
							<table cellpadding="0" cellspacing="0" border="0">
								<tbody>
									<tr>
										<td>
											<input type="hidden" name="send_email" value="0" id="co_send_email">
											<div class="form-box-buttons-new  dsdd">
												<table cellspacing="0" cellpadding="0" border="0" style="-webkit-user-select: none;">
													<tbody style="-webkit-user-select: none;">
														<tr style="-webkit-user-select: none;">
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
													</tbody>
												</table>
											</div>
										</td>

										<td style="padding-left: 15px;">
											<div class="col-lg-12">
												<div class="form-box-buttons 444">
													<span id="submit_button-submit-btn" style="-webkit-user-select: none;">
														<input type="button" id="submit_button" value="Save" class="btn  btn-success" onclick="disableBtn();" style="-webkit-user-select: none;">
													</span>
													<input type="button" value="Cancel" onclick="document.location.href='/application/leads/showimported/id/<?php print $this->entity->id;?>'" style="-webkit-user-select: none;">
												</div>
											</div>
											<script type="text/javascript">//<![CDATA[
											function disableBtn(){setTimeout(function(){$('#submit_button-submit-btn').html('<input type="button" class="" value="Not Authorized..." disabled="disabled" />');},1);}
											//]]></script>
										</td>
									</tr>
								</tbody>
							</table>
							</form>
							<?php } ?>
						</div>
					</div>
				</div>
				
			</div>
			
		</div>
	
	</div>
</div>


<script type="text/javascript">
	$(document).ready(function () {
	//called when key is pressed in textbox
	$("#shipper_phone1,#shipper_phone2,#shipper_mobile,#shipper_fax").keypress(function (e) {
	//if the letter is not digit then display error and don't type anything
	if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
	//display error message
	$("#errmsg").html("Digits Only").show().fadeOut("slow");
	return false;
	}
	});

	$("#shipper_phone1,#shipper_phone2,#shipper_mobile,#shipper_fax").attr("placeholder", "xxx-xxx-xxxx");
	$("#shipper_phone1,#shipper_phone2,#shipper_mobile,#shipper_fax").attr('maxlength','10');
	$('#shipper_phone1,#shipper_phone2,#shipper_mobile,#shipper_fax').keypress(function() {

	function phoneFormat() {
	phone = phone.replace(/[^0-9]/g, '');
	phone = phone.replace(/(\d{3})(\d{3})(\d{4})/, "$1-$2-$3");
	return phone;
	}
	var phone = $(this).val();
	phone = phoneFormat(phone);
	$(this).val(phone);
	});
	});
</script>

<script type="text/javascript">
	function saveAndEmail() {
		$("#co_send_email").val("1");
		$("#submit_button").click();
	}

	function checkPostToCD() {
		<? if ($this->ask_post_to_cd){?>
		<? }else{  ?>
			$("#save_form").submit();
		<? } ?>
	}
	$('#est_ship_date').datepicker();
</script>
<script>
	$(document).ready(()=>{

		// address search API key
		let timer;
        const waitTime = 1000;

		document.querySelector('#shipper_address1').addEventListener('keyup', (e) => {
            const text = e.currentTarget.value;
            clearTimeout(timer);
            timer = setTimeout(() => {
                autoComplete($("#shipper_address1").val().trim(), 'shipper');
            }, waitTime);
        });

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
	});

	function applyAddressOrigin(address, city, state, zip){
		$(".suggestions").html("");
		$("#origin_address1").val(address);
		$("#origin_city").val(city);
		$("#origin_state").val(state);
		$("#origin_zip").val(zip);
		document.getElementById("suggestions-box").innerHTML = "";
	}

	function applyAddressShipper (address, city, state, zip) {
		$(".suggestions").html("");
		$("#shipper_address1").val(address);
		$("#shipper_city").val(city);
		$("#shipper_state").val(state);
		$("#shipper_zip").val(zip);
		document.getElementById("suggestions-box-shipper").innerHTML = "";
	}

	function applyAddressDestination (address, city, state, zip) {
		$(".suggestions").html("");
		$("#destination_address1").val(address);
		$("#destination_city").val(city);
		$("#destination_state").val(state);
		$("#destination_zip").val(zip);
		document.getElementById("suggestions-box-destination").innerHTML = "";
	}

	function applyAddressCC (address, city, state, zip) {
		$(".suggestions").html("");
		$("#e_cc_address").val(address);
		$("#e_cc_city").val(city);
		$("#e_cc_state").val(state);
		$("#e_cc_zip").val(zip);
		document.getElementById("suggestions-box-cc").innerHTML = "";
	}
</script>