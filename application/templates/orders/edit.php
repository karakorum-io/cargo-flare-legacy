<script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.11.4/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo SITE_IN; ?>/jscripts/jquery.rateyo.js"></script>

<style type="text/css">
	
	ul.typeahead.dropdown-menu li a{
        padding-left: 10px !important;
        border-bottom: #CCC 1px solid !important;
        color: #222 !important;
        height: 55px !important;
    }

	td input
	{
		font-size: 12px;
		height: 42px;
		padding-left: 3px;
		width: 100%;
		height: calc(1.5em + 1.3rem + 2px);
		padding: 0.65rem 1rem;
		font-size: 1rem;
		font-weight: 400;
		line-height: 1.5;
		color: #495057;
		background-color: #fff;
		background-clip: padding-box;
		border: 1px solid #ebedf2;
		border-radius: 4px;
		-webkit-transition: border-color 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;
		transition: border-color 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;
		transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
		transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;
	}
	button.ui-button.ui-widget.ui-state-default.ui-corner-all.ui-button-text-only
	{
		background: #1228fa;
		color: white;
	}
	.col-6.text-right
	{
		display: none;
	}
	.col-6.new_btn_info_new_2.text-left
	{
		display: none;
	}
	.col-12.col-sm-9.new_btn_info_new_2.text-right {
	display: none;
	}
	input[type="button"]
	{
		display: inline-block;
		font-weight: normal;
		color: #212529;
		text-align: center;
		vertical-align: middle;
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;
		background-color: transparent;
		border: 1px solid transparent;
		padding: 0.65rem 1rem;
		font-size: 1rem;
		line-height: 1.5;
		border-radius: 0.25rem;
		-webkit-transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;
		transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;
		transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
		transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;
		background: #2487cc;
		color: white;
	}
	.shipper_detail
	{
		text-align:left;
		font-size:15px;
		color:#222;
		height:40px;
		line-height:40px;
		padding-left:15px;
		background-color:#f7f8fa;
		border-bottom:1px solid #ebedf2;
	}
</style>

<script type="text/javascript">
	
	$('.add_one_more_field_').on('click',function(){
		$('#mailexttra').css('display','block');
		return false;
	});

	$('#singletop').on('click',function(){
		$('#mailexttra').css('display','none');
		$('.optionemailextra').val('');
	});

	function validateEmaildetail(sEmail) {
		var res="",res1="",i;
		var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
		for (i = 0; i < sEmail.length; i++){
			if (filter.test(sEmail[i])){
				res += sEmail[i]; 
			}else {
				res1 += sEmail[i];
			}
		}
		if(res1!==''){
			return false;
		}
	}

	$(document).ready(function(){
		$("#mail_to").change(function(){
			var new_trimemail = $.trim($("#mail_to").val());
			$('#mail_to').val(new_trimemail)
		});
	});

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
			$engine.notify('Invalid Origin Information');
			return;
		}
		if (data.destination_city == '' || data.destination_state == '' || data.destination_zip == '') {
			$engine.notify('Invalid Destination Information');
			return;
		}
		if (data.shipping_est_date == '') {
			$engine.notify('Invalid Shipping Date');
			return;
		}
		if (data.shipping_ship_via == '') {
			$engine.notify('You should specify "Ship Via" field');
			return;
		}
		/*// // $("body").nimbleLoader("show");*/
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: BASE_PATH+'application/ajax/autoquote.php',
			data: data,
			success: function(res) {
				$("#total_tariff").html(decodeURIComponent(res.total_tariff));
				$("#total_deposit").html(decodeURIComponent(res.total_deposit));
				$("#carrier_pay").html(decodeURIComponent(res.carrier_pay));
				$engine.notify(res.message);
			},
			error: function() {
				$engine.notify('Failed to calculate Quick Price');
			},
			complete: function() {
				/*// $("body").nimbleLoader("hide");*/
			}
		});
	}
	
	function formatPhoneNumber(s) {
		var s2 = (""+s).replace(/\D/g, '');
		var m = s2.match(/^(\d{3})(\d{3})(\d{4})$/);
		return (!m) ? null : "" + m[1] + "-" + m[2] + "-" + m[3];
	}

	function applySearch(num) {
		
		if(acc_type == 3){
			var acc_obj = acc_data[num];
		} else {
			var acc_obj = acc_data.shipper_data[num];
		}

		switch (acc_type) {
			case <?= Account::TYPE_SHIPPER ?>:

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
					$("#referred_by").append($("<option></option>").attr("value", acc_obj.referred_id).text(acc_obj.referred_by));
				}

				$("#account_payble_contact").val(acc_obj.account_payble_contact);
				typeselected();
			break;
			case <?= Account::TYPE_TERMINAL ?>:
				$("#" + acc_location + "_address1").val(acc_obj.address1);
				$("#" + acc_location + "_address2").val(acc_obj.address2);
				$("#" + acc_location + "_city").val(acc_obj.city);
				$("#" + acc_location + "_country").val(acc_obj.country);

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
	
	function applySearchLeads(num) {
		var acc_obj = acc_data.shipper_leads_data[num];
		switch (acc_type) {
			case <?= Account::TYPE_SHIPPER ?>:
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
					$("#referred_by").empty();
				}

				$("#account_payble_contact").val(acc_obj.account_payble_contact);
				typeselected();
			break;
			case <?= Account::TYPE_TERMINAL ?>:
				$("#" + acc_location + "_address1").val(acc_obj.address1);
				$("#" + acc_location + "_address2").val(acc_obj.address2);
				$("#" + acc_location + "_city").val(acc_obj.city);
				$("#" + acc_location + "_country").val(acc_obj.country);
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
	
	function setLocationSameAsShipperOrder(location) {

		$engine.confirm( "Are you sure you want to overwrite location information?",(action)=>{
			if(action === "confirmed"){
				if(location == 'e_cc'){
					$("input[name='"+location+"_fname']").val($("input[name='shipper_fname']").val());
					$("input[name='"+location+"_lname']").val($("input[name='shipper_lname']").val());
					$("input[name='"+location+"_address']").val($("input[name='shipper_address1']").val());
				} else {
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
		});
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
    	} else {
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
                }
            }
        });
    }
    
    function expiringEditYes() {
    	setEditBlock(0); 
    	if(redirectUrl !=null)
    		clearInterval(redirectUrl); 
    	interval = setInterval(function(){checkEditTimeDue()}, (60*7*1000));
    	$("#checkEditDueId").dialog("close"); 
    }

    function expiringEditNo() {
    	
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

    function checkEditTimeDue() {
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
		<?php } else { ?>

		var alertMsg = "<p>" + blockedMember + " is editing this order at this moment, please try again later.</p><div><input type='button' value='OK' onclick='alertOK()' style='margin-left: 40%; width: 65px; height: 29px;color: #008ec2;'></div>" ;
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

		$("#delivery_date").datepicker({
			dateFormat: 'mm/dd/yy',
			minDate: '+0'
		});

		$("#load_date").datepicker({
			dateFormat: 'mm/dd/yy',
			minDate: '+0'
		});

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
						$engine.notify("Can't send email. Try again later, please");
					}
				}
			});    
		});
    });

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
					$engine.notify("Can't discard note. Try again later, please");
				}
				busy = false;
			},
			error: function(result) {
				$engine.notify("Can't discard note. Try again later, please");
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

	function delInternalNote(id) {
		$engine.confirm( "Are you sure want to delete this note?",(action)=>{
			if(action === "confirmed"){
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
							$engine.notify("Can't delete note. Try again later, please");
						}
						busy = false;
					},
					error: function(result) {
						$engine.notify("Can't delete note. Try again later, please");
						busy = false;
					}
				});
			}
		});
	}

	function editInternalNote(id) {
		var text = $.trim($("#note_"+id+"_text").text());
		$("#note_edit_form textarea").val(text);
		$("#edit_save").val(id);
		$("#note_edit_form").modal();
	}
	
	function note_edit_form_send(id) {

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
						$engine.notify("Can't save note. Try again later, please");
					}
					busy = false;
				},
				error: function(result) {
					$engine.notify("Can't save note. Try again later, please");
					busy = false;
				}
			});
		}
	}
	
	function flipLocation() {
		$engine.confirm( "Are you sure want to flip location information?",(action)=>{
			if(action === "confirmed"){
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
		});
	}
</script>

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

<div>
	<?php include('order_menu.php');  ?>
</div>

<style type="text/css">
	.order-edit .form-box-textfield
	{
		width: 210px;
	}
	h3.details
	{
		padding: 22px 0 0;
		width: 100%;
		font-size: 20px;
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

<div style="margin-top:-108px;margin-bottom:41px; margin-left: 24px">
	<h3 class="details">Edit Order #<?= $this->entity->getNumber() ?></h3>
	<?php $assigned = $this->entity->getAssigned(); ?>
	<strong>Assigned to: <?= $assigned->contactname ?></strong>
</div>

<label class="btn btn-bold btn-label-warning text-left" style="margin-bottom:15px;">
	Complete the form below and click Save Order when finished. Required fields are marked with a <span style="display:inline-block;color:red;">*</span>
</label>

<div id="kt-portlet__body">
	<?php if (!$this->entity->isBlocked()) { ?>
	<form id="save_order_form" action="<?= getLink('orders/edit/id/'.$this->entity->id) ?>" method="post"  onsubmit="javascript:tempDisableBeforeUnload = true;">
	<?php } ?>

		<input type="hidden" id="order_id" name="order_id" value="<?= $this->entity->id ?>" />
		<input type="hidden" id="post_to_cd" name="post_to_cd" value="<?= $this->ask_post_to_cd ? "1" : "0" ?>" />

		<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5 mt-3">
			
			<div id="headingOne" class="hide_show">
				<div class="card-title collapsed" data-toggle="collapse" data-target="#shipper_information_new_info" aria-expanded="false" aria-controls="shipper_information_new_info">
					<h3 class="shipper_detail">Shipper Information</h3>
				</div>
			</div>
			
			<div id="shipper_information_new_info" style="padding-left:20px;padding-right:20px;">
				
				<div class="row">
					
					<div class="col-12 col-sm-2">
						<?php if($accessEdit==1){?><?= functionButton('Select Shipper', 'selectShipper()','','btn_dark_blue btn-sm ') ?>
						<?php } else{?><?= functionButton('Select Shipper', '','btn_dark_blue btn-sm') ?><?php }?>
					</div>
					
					<div class="col-12 col-sm-2">
						<div class="new_form-group_4">
							<input type="checkbox" id="save_shipper" name="save_shipper" value="1" style="margin-top:10px;"/>
							<input type="hidden" id="edit_shipper_id" name="edit_shipper_id" value="<?= $this->entity->account_id ?>" />
							<label for="save_shipper">shipper update</label>
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
						<div class="new_form-group new_form_group_2_input">
							@shipper_phone1@@shipper_phone1_ext@ 
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
						<div class="new_form-group new_form_group_2_input">
							@shipper_phone2@@shipper_phone2_ext@ 
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
						<div class="new_form-group ">
							@shipper_mobile@ 
						</div>
					</div>
					<div class="col-12 col-sm-4">
						<div class="new_form-group new_form_group_2_input">
							<div id="shipper_state_div">@shipper_state@</div>
							@shipper_zip@
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
						<div class="new_form-group ">
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
						</div>
					</div>
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							@referred_by@
						</div>
					</div>
					<div class="col-12 col-sm-4">
						<div class="new_form-group " id="account_payble_contact_label_div">
							@account_payble_contact@
						</div>
					</div>
				</div>                

			</div>
		</div>
		
		<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
			
			<div id="headingOne" class="hide_show">
				<div class="card-title collapsed" data-toggle="collapse" data-target="#Pickup_Contact" aria-expanded="false" aria-controls="Pickup_Contact">
					<h3 class="shipper_detail">Pickup Contact & Location</h3>
				</div>
			</div>
			
			<div id="Pickup_Contact" style="padding-left:20px;padding-right:20px;">
				
				<div class="new_form-group">
					<div class="row">
						<div class="col-12 col-sm-2">
							<?php 
							$checkedLocation1 = "";
							if(!empty($_POST)){
								if($_POST['save_location1']==1)
									$checkedLocation1 = " checked=checked ";
							}
							else
								$checkedLocation1 = " checked=checked ";
							?>
							<input type="checkbox" name="save_location1" id="save_location1" value="1" <?php print $checkedLocation1;?> style="margin-top:10px;"/>
							<input type="hidden" name="origin_id" id="origin_id" value="0" />
							<label for="save_location1" style="width:43px;">Save</label>
						</div>
						<div class="col-12 col-sm-10">
							<?= functionButton('Select Location', "selectTerminal('origin');",'','btn_dark_blue btn-sm') ?>
							<b>OR</b><span   style="padding: 8px;" class="like-link" onclick="setLocationSameAsShipperOrder('origin')">same as shipper</span>
							<?= functionButton('Flip Location', "flipLocation();",'','btn_dark_blue btn-sm') ?>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-12 col-sm-3">
						<div class="new_form-group_4">
							@origin_address1@
							<div id="suggestions-box" class="suggestions"></div>
						</div>
					</div>
					<div class="col-12 col-sm-3">
						<div class="new_form-group_4">
							@origin_contact_name@
						</div>
					</div>
					<div class="col-12 col-sm-3 ">
						<div class="new_form-group_4 new_form_group_2_input">
							@origin_phone1@@origin_phone1_ext@
						</div>
					</div>

					<div class="col-12 col-sm-3">
						<div class="new_form-group_4">
							@origin_mobile@
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-12 col-sm-3">
						<div class="new_form-group_4">
							<label>&nbsp;</label>
							@origin_address2@
						</div>
					</div>
					<div class="col-12 col-sm-3">
						<div class="new_form-group_4">
							@origin_contact_name2@
						</div>
					</div>
					<div class="col-12 col-sm-3">
						<div class="new_form-group_4 new_form_group_2_input">
							@origin_phone2@@origin_phone2_ext@
						</div>
					</div>

					<div class="col-12 col-sm-3">
						<div class="new_form-group_4">
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
						<div class="new_form-group_4">
							@origin_company_name@
						</div>
					</div>
					<div class="col-12 col-sm-3 ">
						<div class="new_form-group_4 new_form_group_2_input">
							@origin_phone3@@origin_phone3_ext@
						</div>
					</div>

					<div class="col-12 col-sm-3">
						<div class="new_form-group_4">
							@origin_fax@
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-12 col-sm-3">
						<div class="new_form-group_4 ">
                            <div class="row">
                                <div class="col-10" id="origin_state_div">
                                	@origin_state@
                                </div>
                                <div class="col-2 new_style_info">
                                @origin_zip@
                                </div>
                            </div>
                        </div>
					</div>
					<div class="col-12 col-sm-3">
						<div class="new_form-group_4">
							@origin_auction_name@
						</div>
					</div>
					<div class="col-12 col-sm-3">
						<div class="new_form-group_4 new_form_group_2_input">
							@origin_phone4@@origin_phone4_ext@
						</div>
					</div>
					<div class="col-12 col-sm-3">
						<div class="new_form-group_4">
							@origin_fax2@
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-12 col-sm-3">
						<div class="new_form-group_4">
							@origin_country@
						</div>
					</div>
					<div class="col-12 col-sm-3">
						<div class="new_form-group_4">
							@origin_booking_number@
						</div>
					</div>
					<div class="col-12 col-sm-3"></div>
					<div class="col-12 col-sm-3"></div>
				</div>


				<div class="row">
					<div class="col-12 col-sm-3">
						<div class="new_form-group_4">
							@origin_type@
						</div>
					</div>
					<div class="col-12 col-sm-3">
						<div class="new_form-group_4">
							@origin_buyer_number@
						</div>
					</div>
					<div class="col-12 col-sm-3"></div>
					<div class="col-12 col-sm-3"></div>
				</div>


				<div class="row">
					<div class="col-12 col-sm-3">
						<div class="new_form-group_4">
							@origin_hours@
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
			
			<div id="headingOne" class="hide_show">
				<div class="card-title">
					<h3 class="shipper_detail">Delivery Contact & Location</h3>
				</div>
			</div>
			
			<div id="delivery_contact" style="padding-left:20px;padding-right:20px;">

				<div class="new_form-group">
					<div class="row">
						<div class="col-12 col-sm-2">
							<?php $checkedLocation2 = "";
							if(!empty($_POST)){
								if($_POST['save_location2']==1)
									$checkedLocation2 = " checked=checked ";
							} else {
								$checkedLocation2 = " checked=checked ";
							} ?>
							<input type="checkbox" name="save_location2" id="save_location2" value="1" <?php print $checkedLocation2;?> style="margin-top:10px;" />
							<input type="hidden" name="destination_id" id="destination_id" value="0" />
							<label for="save_location2" style="width:43px;">Save</label>
						</div>
						<div class="col-12 col-sm-10">
							<?= functionButton('Select Location', "selectTerminal('origin');",'','btn_dark_blue btn-sm
							') ?>
							<b>OR</b>
							<span style="padding: 8px;" class="like-link" onclick="setLocationSameAsShipperOrder('origin')">same as shipper</span>
							<?= functionButton('Flip Location', "flipLocation();",'',' btn-sm btn_dark_blue') ?>
						</div>
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
						<div class="new_form-group_4">
							@destination_contact_name@
						</div>
					</div>
					<div class="col-12 col-sm-3">
						<div class="new_form-group_4 new_form_group_2_input">
							@destination_phone1@@destination_phone1_ext@
						</div>
					</div>
					<div class="col-12 col-sm-3">
						<div class="new_form-group_4">
							@destination_mobile@
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-12 col-sm-3">
						<div class="new_form-group_4">
							<label>&nbsp;</label>
							@destination_address2@
						</div>
					</div>
					
					<div class="col-12 col-sm-3">
						<div class="new_form-group_4">
							@destination_contact_name2@
						</div>
					</div>
					<div class="col-12 col-sm-3">
						<div class="new_form-group_4 new_form_group_2_input">
							@destination_phone2@@destination_phone2_ext@
						</div>
					</div>
					<div class="col-12 col-sm-3">
						<div class="new_form-group_4">
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
						<div class="new_form-group_4">
							@destination_company_name@
						</div>
					</div>
					<div class="col-12 col-sm-3">
						<div class="new_form-group_4 new_form_group_2_input">
							@destination_phone3@@destination_phone3_ext@
						</div>
					</div>
					<div class="col-12 col-sm-3">
						<div class="new_form-group_4">
							@destination_fax@
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-12 col-sm-3">
						<div class="new_form-group_4 ">
                            <div class="row">
                                <div class="col-10" id="destination_state_div">
                                @destination_state@
                                </div>
                                <div class="col-2 new_style_info">
                                @destination_zip@
                                </div>
                            </div>
                        </div>
					</div>
					<div class="col-12 col-sm-3">
						<div class="new_form-group_4">
							@destination_auction_name@
						</div>
					</div>
					<div class="col-12 col-sm-3">
						<div class="new_form-group_4 new_form_group_2_input">
							@destination_phone4@@destination_phone4_ext@
						</div>
					</div>
					<div class="col-12 col-sm-3">
						<div class="new_form-group_4">
							@destination_fax2@
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-12 col-sm-3">
						<div class="new_form-group_4">
							@destination_country@
						</div>
					</div>
					<div class="col-12 col-sm-3">
						<div class="new_form-group_4">
							@destination_booking_number@
						</div>
					</div>
					<div class="col-12 col-sm-3"></div>
					<div class="col-12 col-sm-3"></div>
				</div>

				<div class="row">
					<div class="col-12 col-sm-3">
						<div class="new_form-group_4">
							@destination_type@
						</div>
					</div>
					<div class="col-12 col-sm-3">
						<div class="new_form-group_4">
							@destination_buyer_number@
						</div>
					</div>
					<div class="col-12 col-sm-3"></div>
					<div class="col-12 col-sm-3"></div>
				</div>

				<div class="row">
					<div class="col-12 col-sm-3">
						<div class="new_form-group_4">
							@destination_hours@
						</div>
					</div>
					<div class="col-12 col-sm-3"></div>
					<div class="col-12 col-sm-3"></div>
					<div class="col-12 col-sm-3"></div>
				</div>
				
			</div>
		</div>

		<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
			
			<div id="headingOne" class="hide_show">
				<div class="card-title">
					<h3 class="shipper_detail">Shipping Information</h3>
				</div>
			</div>
			
			<div id="shipping_information_info" style="padding-left:20px;padding-right:20px;">
				
				<div class="row">
				
					<div class="col-12 col-sm-4">
					
						<div class="new_form-group">
							@avail_pickup_date@
						</div>
						
						<div class="new_form-group new_form_group_2_input">
							@load_date_type@ @load_date@
						</div>
						
						<div class="new_form-group new_form_group_2_input">
							@delivery_date_type@ @delivery_date@
						</div>						
						
						<div class="new_form-group">
							@shipping_ship_via@
						</div>
						
					</div>
					
					<div class="col-12 col-sm-8 ">
					
						<div class="new_form-group_1 mb-3">
							@notes_from_shipper@
							<div class="text-right mt-1"><i><strong>(Above notes will always appear on the dispatch sheet)</strong></i></div>
						</div>
						
						<div class="new_form-group_1">
							@notes_for_shipper@<br/>
							<div class="text-right mt-1"><i><strong>(Maximum character allowed is <div id="charNum" style="float:right;">&nbsp;<font color="red">60</font> )</div></strong></i></div>
						</div>
						
					</div>
					
				</div>
				
			</div>
			
		</div>

		<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
			
			<div id="headingOne" class="hide_show">
				<div class="card-title">
					<h3 class="shipper_detail">Vehicle Information</h3>
				</div>
			</div>
			
			<div id="vehicle_information_info" style="padding-left:20px;padding-right:20px;">
				
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
									<td id="v-year-<?php print $vehicle->id;?>"><?= $vehicle->year ?></td>
									<td id="v-make-<?php print $vehicle->id;?>"><?= $vehicle->make ?></td>
									<td id="v-model-<?php print $vehicle->id;?>"><?= $vehicle->model ?></td>
									<td id="v-type-<?php print $vehicle->id;?>"><?= $vehicle->type ?></td>

									<td id="v-vin-<?php print $vehicle->id;?>"><input type="text" class="form-control" name="vin[<?php print $vehicle->id;?>]" value="<?= $vehicle->vin ?>" id="vin_<?php print $vehicle->id;?>"  /></td>

									<td id="v-tariff-<?php print $vehicle->id;?>"><input type="text"  class="form-control" name="vehicle_tariff[<?php print $vehicle->id;?>]" value="<?= $vehicle->tariff ?>" id="vehicle_tariff_<?php print $vehicle->id;?>" onkeyup="updatePricingInfo();" />

									</td>
									<td id="v-deposit-<?php print $vehicle->id;?>"><input type="text"  class="form-control" name="vehicle_deposit[<?php print $vehicle->id;?>]" value="<?= $vehicle->deposit ?>" id="vehicle_deposit_<?php print $vehicle->id;?>" onkeyup="updatePricingInfo();"/>
										<input type="hidden" class="form-control" name="vehicle_id[]" value="<?php print $vehicle->id;?>"  />
									</td>
									<td id="v-inop-<?php print $vehicle->id;?>"><?= ($vehicle->inop == '1')?'Yes':'No' ?></td>

									<td align="center" class="grid-body-right" >
										<?php  if($accessEdit==1){?>
											<?php if (!$this->entity->isBlocked()) { ?>
												<img src="<?= SITE_IN ?>images/icons/copy.png" alt="Copy" title="Copy" onclick="copyVehicle(<?= $vehicle->id ?>)" class="action-icon"/>
												<img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit" onclick="editVehicle(<?= $vehicle->id ?>)" class="action-icon"/>
												<img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete" onclick="deleteVehicle(<?= $vehicle->id ?>)" class="action-icon"/>
											<?php } else { ?>
												&nbsp;
											<?php } ?>
										<?php }else{ ?>
											<img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit" class="action-icon"/>
											<img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete" class="action-icon"/>
										<?php }?>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php else : ?>
						<tr class="grid-body">
							<td  style="text-align: center;"><i>No Vehicles</i></td>
						</tr>
						<?php endif; ?>
					</tbody>
				</table>

				<?php if($accessEdit==1){ ?>
				<div class="row mb-3">
					<div class="col-12 text-right">
						<?php if (!$this->entity->isBlocked()) { ?>
						<div><?= functionButton('Add Vehicle', 'addVehicle()','','btn-sm btn_dark_blue') ?></div>
						<?php } ?>
					</div>
				</div>

				<div class="row mb-3">
					<div class="col-12">
						<?php if ($this->isAutoQuoteAlowed) { ?>
						<!----<div><?= functionButton('Quick Price', 'quickPrice()') ?></div>---->
						<?php } ?>					
					</div>
				</div>
				
				<?php }else{ ?>
					
				<div class="row mb-3">
					<div class="col-12 text-right">
						<?php if (!$this->entity->isBlocked()) { ?>
						<div><?= functionButton('Add Vehicle', '') ?></div>
						<?php } ?>
					</div>
				</div>

				<div class="row mb-3">
					<div class="col-12">
						<?php if ($this->isAutoQuoteAlowed) { ?>
						<div><?= functionButton('Quick Price', '') ?></div>
						<?php } ?>
					</div>
				</div>
				
				<?php }?>
				
			</div>
		</div>

		<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
			
			<div id="headingOne" class="hide_show">
				<div class="card-title">
					<h3 class="shipper_detail">Payment Information</h3>
				</div>
			</div>
			
			<div id="payment_information_info" style="padding-left:20px;padding-right:20px;">
				
				<div class="row">
					<div class="col-12 col-sm-6">
						<div class="new_form-group mt-2">
							<label style="width:120px;margin-top:0;">Total Tariff</label>
							<span id="total_tariff">@total_tariff@</span>
							<span class="grey-comment">(Edit carrier pay and deposit under the "Vehicle Information" section)</span>
						</div>
					</div>
					<div class="col-12 col-sm-6">
						<div class="new_form-group_1">
							@balance_paid_by@
						</div>						
					</div>
				</div>
				
				<div class="row">
					<div class="col-12 col-sm-6">
						<div class="new_form-group mt-2">
							<label style="width:120px;margin-top:0;">Required Deposit</label>
							<span id="total_deposit">@total_deposit@</span>
							<span class="grey-comment">(Edit deposit under the "Vehicle Information" section)</span>
						</div>
					</div>
					<div class="col-12 col-sm-6">
						<div class="new_form-group_1">
							@customer_balance_paid_by@
						</div>						
					</div>
				</div>
				
				<div class="row mb-3">
					<div class="col-12 col-sm-6">
						<div class="new_form-group mt-2">
							<label style="width:120px;margin-top:0;">Carrier Pay</label>
							<span id="carrier_pay">@carrier_pay@</span>
							<span class="grey-comment">(Edit carrier pay under the "Vehicle Information" section)</span>
						</div>
					</div>
					<div class="col-12 col-sm-6">
						<div class="new_form-group_1">
							@payments_terms@
						</div>						
					</div>
				</div>
				
			</div>
		</div>
		<!-- 
		<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
			
			<div id="headingOne" class="hide_show">
				<div class="card-title">
					<h3 class="shipper_detail">Credit Card Information</h3>
				</div>
			</div>
			
			<div id="credit_card_info" style="padding-left:20px;padding-right:20px;">
			
				<div class="row">
				
					<div class="col-12 col-sm-4">
						<div class="new_form-group new_style_info" style="margin-top:5px;">
							@auto_payment@
						</div>
					</div>
					
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							@e_cc_type@							
						</div>
					</div>
					
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							@e_cc_address@
							<div id="suggestions-box-cc" class="suggestions"></div>
						</div>
					</div>
					
				</div>
				
				<div class="row">
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							@e_cc_fname@
						</div>
					</div>
					
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							@e_cc_number@
						</div>
					</div>
					
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							@e_cc_city@							
						</div>
					</div>
					
				</div>
				
				<div class="row">
				
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							@e_cc_lname@
						</div>
					</div>
					
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							@e_cc_cvv2@ <img src="<?=SITE_IN?>images/icons/cards.gif" alt="Card Types" width="129" height="16" style="vertical-align:middle;margin-top:8px;margin-left:10px;" />
						</div>
					</div>
					
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							<div id="e_cc_state_div">
							    @e_cc_state@
                            </div>
                            @e_cc_zip@
						</div>
					</div>
					
				</div>
				
				<div class="row">
					
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							@e_cc_month@ <span class="pull-left" style="margin:7px 5px 0 15px;">/</span> @e_cc_year@
						</div>
					</div>
					
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							@e_cc_country@
						</div>
					</div>
					
				</div>
			</div>
		</div> 
		-->
		<div style="background:#fff;border:1px solid #ebedf2;" class="mt-3">
            <div class="hide_show">
                <h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Credit Card Information</h3>
            </div>
            <div id="credit_card_information_info_1" class="pt-3" style="padding-left:20px;padding-right:20px;">

				<div class="row">
	
					<div class="col-12 col-sm-4">
					
						<div class="shipper_do_not_process mb-4" style="margin-top:7px;">
							<span style="margin-right:15px;" class="like-link pull-left" onclick="setLocationSameAsShipperOrder('e_cc')">same as shipper</span>&#8203;
							@auto_payment@
						</div>
						
						<div class="new_form-group">
							@e_cc_fname@
						</div>
						
						<div class="new_form-group">
							@e_cc_lname@
						</div>
						
					</div>
					
					<div class="col-12 col-sm-4">
						<div class="new_form-group ">
							@e_cc_type@
						</div>
						
						<div class="new_form-group">
							@e_cc_number@
						</div>
						
						<div class="new_form-group">
							@e_cc_cvv2@ <img src="<?=SITE_IN?>images/icons/cards.gif" alt="Card Types" width="129" height="16" style="vertical-align:middle;margin-top:8px;margin-left:10px;">
						</div>
						 
						<div class="new_form-group">
							@e_cc_month@ <span class="pull-left" style="margin:7px 5px 0 15px;">/</span> @e_cc_year@
						</div>
						
					</div>
					
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							@e_cc_address@
                            <div id="suggestions-box-cc" class="suggestions"></div>
						</div>
						<div class="new_form-group">
							@e_cc_city@
						</div>
						<div class="new_form-group">
                            <div id="e_cc_state_div">
							    @e_cc_state@
                            </div>
                            @e_cc_zip@
						</div>
						<div class="new_form-group">
							@e_cc_country@
						</div>
					</div>
				</div>
            </div>
        </div>
		
		<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
			
			<div id="headingOne" class="hide_show">
				<div class="card-title">
					<h3 class="shipper_detail">Internal Notes</h3>
				</div>
			</div>
			
			<div id="internal_notes_info" class="pt-3 pb-3" style="padding-left:20px;padding-right:20px;">
			
				<div class="row">
					<div class="col-12 col-sm-12">
						<div class="form-group">
							<textarea class="form-control form-box-textarea"  maxlength="1000" id="internal_note"></textarea>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-12 col-sm-5">
						<div class="new_form-group">
							<?php $notes = $this->notes; ?>
							<label>Quick Notes</label>
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
					</div>
					
					<div class="col-12 col-sm-5">
						<div class="new_form-group">
							<label>Priority</label>
							<select name="priority_notes" class="form-control" id="priority_notes" >
								<option value="1">Low</option>
								<option value="2">High</option>
							</select>
						</div>
					</div>
					
					<div class="col-12 col-sm-2">
						<div class="new_form-group select_opt_new_info select_wdh_100_per text-right">
							<?= functionButton('Add Note', 'addInternalNote()','','btn_dark_blue btn-sm') ?>
						</div>
					</div>

					<div class="col-12 col-sm-12">
						<hr/>
					</div>
				</div>
				
				<div class="row">
					<div class="col-12 col-sm-12">
						<table class="table table-bordered" id="internal_notes_table">
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
								<tr>
									<td colspan="4" class="grid-body-left grid-body-right" align="center"><i>No notes available.</i></td>
								</tr>
								<? else : ?>
								<?php foreach($notes[Note::TYPE_INTERNAL] as $note) : ?>
								<?php $sender = $note->getSender(); 
								$email = $sender->email;
								$contactname = $sender->contactname;
								if($note->system_admin == 2){
									$email = "admin@cargoflare.com";
									$contactname = "System";
								}
								if (($_SESSION['member']['access_notes'] == 0 ) 
								|| $_SESSION['member']['access_notes'] == 1
								|| $_SESSION['member']['access_notes'] == 2
								){
								?>
								<tr class="grid-body" >
									<td style="white-space:nowrap;"  class="grid-body-left" <?php if($note->priority==2){?> style="color:#FF0000"<?php }?>><?= $note->getCreated("m/d/y h:i a") ?></td>
									<td id="note_<?= $note->id ?>_text" style=" <?php if($note->discard==1){ ?>text-decoration: line-through;<?php }?><?php if($note->priority==2){?>color:#FF0000;<?php }?>"><?php if($note->system_admin == 1 || $note->system_admin == 2){?><b><?= $note->getText() ?></b><?php }elseif($note->priority==2){?><b style="font-size:12px;"><?= $note->getText() ?></b><?php }else{?><?= $note->getText() ?><?php }?></td>
									<td style="text-align: center;" <?php if($note->priority==2){?>style="color:#FF0000"<?php }?>><a href="mailto:<?= $email ?>"><?= $contactname ?></a></td>
									<td class="grid-body-right" style="white-space: nowrap;" <?php if($note->priority==2){?>style="color:#FF0000"<?php }?>>
									<?php 
									if (($_SESSION['member']['access_notes'] == 0 ) ||
									($_SESSION['member']['access_notes'] == 1 && ($note->sender_id == (int)$_SESSION['member_id']))
									|| $_SESSION['member']['access_notes'] == 2
									) {
										if($note->sender_id == (int)$_SESSION['member_id']  && $note->system_admin == 0 ){
									?>
									<img src="<?= SITE_IN ?>images/icons/strike.png" alt="Discard" title="Discard" width="16" height="16" class="action-icon edit-note" onclick="discardNote(<?= $note->id ?>)"/>   
									<?php
									}  
									if($note->system_admin == 0 && $_SESSION['member']['access_notes'] != 0 ){
									?>
									<img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit" width="16" height="16" class="action-icon edit-note" onclick="editInternalNote(<?= $note->id ?>)"/>
									<img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete" width="16" height="16" class="action-icon delete-note" onclick="delInternalNote(<?= $note->id ?>)"/>
									<?php }
									} ?>
									</td>
								</tr>
								<?php } ?>
								<?php endforeach; ?>
								<?php endif ; ?>
							</tbody>
						</table>
					</div>
				</div>

				<div class="row">
					<?php
					if($accessEdit==1){
						?>
						<?php if (!$this->entity->isBlocked()) { ?>

						<div class="col-12 text-right">
							<? if ($this->ask_post_to_cd) { ?>
							@match_carrier@
								<?php //echo postToCDButtons(SITE_IN . "application/orders/show/id/" . $this->entity->id, "Save",'','btn_bright_blue btn-sm') ?>
								<button class="btn btn-default btn-sm" onclick="document.location.href='/application/orders/show/id/<?php print $this->entity->id;?>'" type="button">Cancel</button>
								<input type="submit" class="btn_bright_blue btn-sm" id="submit_button" name="submit" value="Save" style="-webkit-user-select: none;">
							<? } else { ?>
							@match_carrier@
							<br/>
							<button class="btn btn-default btn-sm" onclick="document.location.href='/application/orders/show/id/<?php print $this->entity->id;?>'" type="button">Cancel</button>
							<input type="submit" class="btn_bright_blue btn-sm" id="submit_button" name="submit" value="Save" style="-webkit-user-select: none;">
							<? } ?>
						</div>

					
				</div>
				<?php } ?>
				<?php } else { ?>

				<div class="text-right">
					<div class="col-12 text-right">
						<button class="btn btn-default btn-sm" onclick="document.location.href='/application/orders/show/id/<?php print $this->entity->id;?>'" type="button">Cancel</button>
						<input type="submit" class="btn_bright_blue btn-sm" id="submit_button" name="submit" value="Save2" style="-webkit-user-select: none;">
					</div>
					<script type="text/javascript">//<![CDATA[
						function disableBtn(){setTimeout(function(){$('#submit_button-submit-btn').html('<input type="button" value="Not Authorized..." disabled="disabled" />');},1);}
					//]]></script>
				</div>
				
			</div>
			<?php } ?>
		</div>
	</form>
</div>

<script>
	$(document).ready(function () {
		$("#add_vehicle_deposit,#add_vehicle_carrier_pay,#shipper_phone1,#shipper_phone2,#shipper_mobile,#shipper_fax,#origin_phone1,#origin_phone2,#origin_phone3,#origin_phone4,#origin_mobile,#origin_mobile2,#origin_fax,#origin_fax2,#destination_phone1,#destination_phone2,#destination_phone3,#destination_phone4,#destination_mobile,#destination_mobile2,#destination_fax,#destination_fax2").keypress(function (e) {
			// if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
			// 	$("#errmsg").html("Digits Only").show().fadeOut("slow");
			// 	return false;
			// }
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
	});

	function saveAndEmail() {
		$("#co_send_email").val("1");
		$("#submit_button").click();
	}

	function checkPostToCD() {
		<?php if ($this->ask_post_to_cd){?>
			$("#acc_entity_dialog").dialog('option', 'title', 'Repost order?').dialog('open');
		<?php } else {  ?>
			$("#save_form").submit();
		<?php } ?>
	}

	if(<?php echo $this->entity->parentid;?> == <?php echo $this->entity->assigned_id;?>){
		// nothing to do
	} else {
		//$("#referred_by").css("pointer-events","none");
	}
</script>

<script>
	$(document).ready(()=>{
		// check for bloack status in every 6 seconds!
		setInterval(() => {
			checkBlocked();
		}, 6000);
	});

	let checkBlocked = () => {
		console.log("Checking if Order is free...");
		let myId = "<?php echo $_SESSION['member']['id']?>";
		// $.ajax({
		// 	type: "POST",
		// 	url: "<?= SITE_IN ?>application/orders/checkBlocked",
		// 	dataType: "json",
		// 	data: {
		// 		id: '<?php echo $_GET['id'];?>'
		// 	},
		// 	success: function(response) {
		// 		if(response.status && (response.code == 200)){
		// 			console.log(myId);
		// 			console.log(response.data.blocked_by);
		// 			if(myId != response.data.blocked_by){
		// 				$engine.notify('Someone is already editing this order now. Try Again later! \n Redirecting to Order details page.');
		// 				setTimeout(() => {
		// 					location.href= "<?php echo getLink('orders/show/id/'.$_GET['id'])?>"
		// 				}, 5000);
		// 			}
		// 		}
		// 	}
		// });
	}
</script>
<script type="text/javascript">
	$(document).ready(function(){
		
		$(document).click(()=>{
            $(".suggestions").html("");
        });

		$("#Shipper_Information").click(function() {
			$("#Shipper_Information_show").toggle();
		});

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

        document.querySelector('#shipper_address1').addEventListener('keyup', (e) => {
            const text = e.currentTarget.value;
            clearTimeout(timer);
            timer = setTimeout(() => {
                autoComplete($("#shipper_address1").val().trim(), 'shipper');
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
		$("#suggestions-box").html("");
		$("#origin_address1").val(address);
		$("#origin_city").val(city);
		$("#origin_state").val(state);
		$("#origin_zip").val(zip);
		document.getElementById("suggestions-box").innerHTML = "";
	}

	function applyAddressShipper (address, city, state, zip) {
		$("#suggestions-box").html("");
		$("#shipper_address1").val(address);
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
</script>