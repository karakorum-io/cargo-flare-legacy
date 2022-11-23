<style type="text/css">
	p.block-title {
		background: #181824;
		color: white;
		text-align: center;
		padding: 11px;
	}
	th {
		font-size: 11px;
	}
	.shipper_detail {
		text-align:left;
		font-size:15px;
		color:#222;
		height:40px;
		line-height:40px;
		padding-left:15px;
		background-color:#f7f8fa;
		border-bottom:1px solid #ebedf2;
	}
	#saved-cards .modal-lg {
		max-width:1080px;
	}
	.payment_option_new_label > label {
		width: calc(100% - 25px);
	}
	.payment_option_new_label > input[type="radio"] {
		float:left;
		margin-top:4px
	}
	h3.details {
		padding: 22px 0 0;
		width: 100%;
		font-size: 20px;
	}

	ul.typeahead.dropdown-menu li a{
        padding-left: 10px !important;
        border-bottom: #CCC 1px solid !important;
        color: #222 !important;
        height: 55px !important;
    }
</style>

<!--mail modal starts-->
<div class="modal fade" id="maildivnew" tabindex="-1" role="dialog" aria-labelledby="maildivnewmodel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="maildivnewmodel">Send Email</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i class="fa fa-times" aria-hidden="true"></i>
				</button>
			</div>
			<div class="modal-body">   
				<div style="float: left;">
					<ul style="margin-top: 26px;">  
						<li style="margin-bottom: 14px;">Form Type <input value="1" id="attachPdf" name="attachTpe" type="radio"/><label for="attachPdf" style="margin-right: 2px; cursor:pointer;"> PDF</label><input value="0" id="attachHtml"  name="attachTpe" type="radio"/><label for="attachHtml" style="cursor:pointer"> HTML</label></li>
						<li style="margin-bottom: 11px;">Attachment(s): <span style="color:#24709F;" id="mail_att_new"></span></li>
					</ul>
				</div>
				<div style="text-align: right;">
					<div style="text-align: right;">
						<img src="/images/icons/add.gif"> <span style="margin-bottom: 3px;cursor:pointer; position: relative;bottom:4px; color:#24709F;" class="add_one_more_field_" >Add a Field</span>
						<ul>
							<li id="extraEmailsingle" style="margin-bottom: 6px;"><span>Email:<span style="color:red">*</span></span> <input type="text" id="mail_to_new" name="mail_to_new" class="form-box-combobox" ></li>
							<li style="margin-bottom: 6px;margin-top: 6px;margin-left: 292px; position:relative; display: none;" id="mailexttra"><input name="optionemailextra" class="form-box-combobox optionemailextra" type="text"><a href="#" style="position: absolute;margin-left: 2px;margin-top: 8px;" class="remove_2sd_field"><img id="singletop" style="width: 12px;height: 12px;" src="/images/icons/delete.png"></a></li>
							<li style="margin-bottom: 6px;"><span style="margin-right: 18px;">CC:</span> <input type="text" id="mail_cc_new" name="mail_cc_new" class="form-box-combobox" ></li>
							<li style="margin-bottom: 12px;"><span style="margin-right: 9px;">BCC:</span> <input type="text" id="mail_bcc_new" name="mail_bcc_new" class="form-box-combobox" ></li>
						</ul>
					</div>
					<div class="edit-mail-content" style="margin-bottom: 8px;">
						<div class="form-group">
							<label>Subject</label>
							<input type="text" id="mail_subject_new" class="form-box-textfield form-control" maxlength="255" name="mail_subject_new" style="width: 100%;">
						</div>
						<div class="form-group">
							<div class="edit-mail-label">Body:<span>*</span></div>
							<div class="edit-mail-field" style="width: 100%;">
								<textarea class="form-box-textfield" style="width: 100%;" name="mail_body_new" id="mail_body_new"></textarea>
							</div>
						</div>
					</div>
					<input type="hidden" name="form_id" id="form_id"  value=""/>
					<input type="hidden" name="entity_id" id="entity_id"  value=""/>
					<input type="hidden" name="skillCount" id="skillCount" value="1">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-dark btn-sm" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn_dark_blue btn-sm" onclick="emailSelectedLeadFormNewsend()">Submit</button>
			</div>
		</div>
	</div>
</div>
<!--mail modal ends-->

<!--saved cards modal starts-->
<div class="modal fade" id="saved-cards" tabindex="-1" role="dialog" aria-labelledby="saved-cards_show" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="saved-cards_show">Select Saved Cards</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span style="display:block;" aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body">
				<h5 class="kt-widget14__title mb-4 mt-2">Saved Cards</h5>
				<table class="table table-bordered">
					<thead>
						<tr>
							<th></th>
							<th>Account ID</th>
							<th>Number</th>
							<th>Expiry Month</th>
							<th>Expiry Year</th>
							<th>CVV</th>
							<th>Type</th>
							<th>First Name</th>
							<th>Last Name</th>
							<th>Address</th>
							<th>Added On</th>
							<th>Updated On</th>
							<th>Recently Used</th>
							<th>Status</th>
						</tr>
					</thead>
					<tbody class="Cards-Data"></tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-dark btn-sm" data-dismiss="modal">Close</button>				
			</div>
		</div>
	</div>
</div>
<!--saved cards modal ends-->

<!--check modal starts-->
<div class="modal fade" id="checkdiv" tabindex="-1" role="dialog" aria-labelledby="checkdiv_model" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="checkdiv_model">Modal title</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				</button>
			</div>
			<div class="modal-body">
				<table cellspacing="2" cellpadding="0" border="0" width="100%">
					<tr>
						<td align="center">
							<b>Check# of file is</b>
						</td>
					</tr>
					<tr>
						<td align="center">&nbsp;</td>      
					</tr>
					<tr>
						<td align="center">
						<input type="text" style="font-size: 2.6em;font-weight: bold;border: none;width: 150px;" name="checkNumber" value="" id="checkNumber" />
						<input type="hidden" name="checkId" value="0" id="checkId" />
						</td>    
					</tr>
				</table>				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary">Save changes</button>
			</div>
		</div>
	</div>
</div>
<!--check modal starts-->

<!--begin::Modal-->
<div class="modal fade" id="checkalertdiv" tabindex="-1" role="dialog" aria-labelledby="checkalertdiv_model" aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="checkalertdiv_model">Print Check</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div id="checkalertdiv" style="width:100%;">
					<table cellspacing="2" cellpadding="0" border="0" width="100%">
						<tr>
							<td align="center">
								<b>There is payment already recorded for this order. Do you want to create new payment in QuickBook?</b>
							</td>      
						</tr>
						<tr>
							<td align="center">&nbsp;</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
				<button type="button" onclick="printCheck()" class="btn btn-primary">Ok</button>
			</div>
		</div>
	</div>
</div>
<!--end::Modal-->

<!--edit notes modal starts-->
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
<!--edit notes modal ends-->

<?php
	if (isset($this->dispatchSheet)) {
		$carrier_company_name = $this->dispatchSheet->carrier_company_name;
		$carrier_contact_name = $this->dispatchSheet->carrier_contact_name;
		$carrier_phone_1      = $this->dispatchSheet->carrier_phone_1;
		$carrier_phone_2      = $this->dispatchSheet->carrier_phone_2;
		$carrier_fax          = $this->dispatchSheet->carrier_fax;
		$carrier_email        = $this->dispatchSheet->carrier_email;

		$carrierData = $this->entity->getCarrier();
		
		if ($carrierData != null) {
			$carrier_address = $carrierData->address1;
			$carrier_address2 = $carrierData->address2;
			$carrier_city = $carrierData->city;
			$carrier_state = $carrierData->state;
			$carrier_zip = $carrierData->zip_code;
			
			if($carrierData->print_check == 1)
			{
				$carrier_address = $carrierData->print_address1;
				$carrier_address2 = $carrierData->print_address2;
				$carrier_city = $carrierData->print_city;
				$carrier_state = $carrierData->print_state;
				$carrier_zip = $carrierData->print_zip_code;
			}
		}
		
		$phone1_ext ='';
		$phone2_ext ='';
		if($this->dispatchSheet->carrier_phone1_ext!='')
		$phone1_ext = " <b>X</b> ".$this->dispatchSheet->carrier_phone1_ext;
		if($this->dispatchSheet->carrier_phone2_ext!='')
		$phone2_ext = " <b>X</b> ".$this->dispatchSheet->carrier_phone2_ext;
		
	}
	$check_number='';
?>

<script type="text/javascript">

	function editPayment(payment_id) {
		Processing_show();

		$.ajax({
			type: 'POST',
			url: BASE_PATH+'application/ajax/payments.php?action=get',
			dataType: 'json',
			data: {
				entity_id: <?=$this->entity->id?>,
				payment_id: payment_id
			},
			success: function(res) {
				if (res.success) {
					$("#submit_button").val('Update Payment');
					for (i in res.data) {
						$("[name='"+i+"']").val(res.data[i]);
					}
					$("#internally_form input[name='payment_id']").val(payment_id).attr("disabled", null);
					$("#method").change();
				} else {
					$engine.notify('Failed to load payment information');
				}
			},
			failed: function(res) {
				$engine.notify('Failed to load payment information');
			},
			complete: function(res) {
				KTApp.unblockPage();
			}
		});
	}
	
	function Processing_show() {
		KTApp.blockPage({
			overlayColor: '#000000',
			type: 'v2',
			state: 'primary',
			message: '.'
		});
		
		setTimeout(function() {
			KTApp.unblockPage();
		}, 2000);
	}
	
	function createBill() {
		
		Processing_show();
		$.ajax({
			type: 'POST',
			url: BASE_PATH+'application/ajax/entities.php',
			dataType: 'json',
			data: {
				entity_id: <?=$this->entity->id?>,
				action: "createbill"
			},
			success: function(res) {
				console.log(res)
				console.log("ddd");
				if (res.success) {
					if(res.msg =='')
					$engine.notify('Create Bill Request Success.');
					else
					$engine.notify(res.msg);
				} else {
					$engine.notify('Create Bill Request Failed.');
				}
			},
			failed: function(res) {
				$engine.notify('Create Bill Request Failed.');
			},
			complete: function(res) {
				KTApp.unblockPage();
			}
		});
	}
	
	function createBillWithFix() {
		Processing_show();
		$.ajax({
			type: 'POST',
			url: BASE_PATH+'application/ajax/entities.php',
			dataType: 'json',
			data: {
				entity_id: <?=$this->entity->id?>,
				action: "createbillfix"
			},
			success: function(res) {
				KTApp.unblockPage();
				if (res.success) {
					if(res.msg =='')
					$engine.notify('Create Bill Request Success.');
					else
					$engine.notify(res.msg);
				} else {
					$engine.notify('Create Bill Request Failed.');
				}
			},
			failed: function(res) {
				KTApp.unblockPage();
				$engine.notify('Create Bill Request Failed.');
			},
			complete: function(res) {
				KTApp.unblockPage();
			}
		});
	}
	
    function createPayment() {
		Processing_show();
		$.ajax({
			type: 'POST',
			url: BASE_PATH+'application/ajax/entities.php',
			dataType: 'json',
			data: {
				entity_id: <?=$this->entity->id?>,
				action: "createPayment"
			},
			success: function(res) {
				if (res.success) {
					if(res.msg =='')
					$engine.notify('Create Payment Request Success.');
					else
					$engine.notify(res.msg);
				} else {
					$engine.notify('Create Payment Request Failed.');
				}
			},
			failed: function(res) {
				$engine.notify('Create Payment Request Failed.');
			},
			complete: function(res) {
				KTApp.unblockPage();
				
			}
		});
	}

	function createInvoice() {
		
		Processing_show();
		$.ajax({
			type: 'POST',
			url: BASE_PATH+'application/ajax/entities.php',
			dataType: 'json',
			data: {
				entity_id: <?=$this->entity->id?>,
				action: "createInvoice"
			},
			success: function(response) {
				
				if (response.success) {
					KTApp.unblockPage();
					if(response.msg =='')
					$engine.notify('Sync Invoice Request Success.');
					else
					$engine.notify(response.msg);
				} else {
					$engine.notify('Sync Invoice Request Failed.');
				}
			},
			failed: function(res) {
				KTApp.unblockPage();
				$engine.notify('Sync Invoice Request Failed.');
			},
			complete: function(res) {
				KTApp.unblockPage();
				
			}
		});
	}
	
	function fixCreateInvoice() {
		Processing_show();
		$.ajax({
			type: 'POST',
			url: BASE_PATH+'application/ajax/entities.php',
			dataType: 'json',
			data: {
				entity_id: <?=$this->entity->id?>,
				action: "fixCreateInvoice"
			},
			success: function(res) {
				if (res.success) {
					if(res.msg =='')
					$engine.notify('Fix Invoice Request Success.');
					else
					$engine.notify(res.msg);
				} else {
					$engine.notify('Fix Invoice Request Failed.');
				}
			},
			failed: function(res) {
				$engine.notify('Fix Invoice Request Failed.');
			},
			complete: function(res) {
				KTApp.unblockPage();
			}
		});
	}
	
	function syncReceivedPayment(pay_id) {
		
		Processing_show();
		$.ajax({
			type: 'POST',
			url: BASE_PATH+'application/ajax/entities.php',
			dataType: 'json',
			data: {
				payment_id: pay_id,
				action: "syncReceivedPayment"
			},
			success: function(res) {
				if (res.success) {
					if(res.msg =='')
					$engine.notifye('Create Payment Request Success.');
					else
					$engine.notify(res.msg);
				} else {
					$engine.notify('Create Payment Request Failed.');
				}
			},
			failed: function(res) {
				$engine.notify('Create Payment Request Failed.');
			},
			complete: function(res) {
				KTApp.unblockPage();
			}
		});
	}
	
	function printCheckFormPreview(entity) {
		window.open("<?php print SITE_IN;?>external/reprint_check.php?ent="+entity,"","toolbar=yes,scrollbars=yes, resizable=yes,HEIGHT=700,WIDTH=800")
	}
	
	function saveEntityCC() {

		$(".entity-cc-info .error").html('');
		$(".entity-cc-info .error").hide();
		var errors = [];
		var e_cc_fname = $.trim($("#e_cc_fname").val());
		var e_cc_lname = $.trim($("#e_cc_lname").val());
		var e_cc_address = $.trim($("#e_cc_address").val());
		var e_cc_city = $.trim($("#e_cc_city").val());
		var e_cc_state = $.trim($("#e_cc_state").val());
		var e_cc_zip = $.trim($("#e_cc_zip").val());
		var e_cc_number = $.trim($("#e_cc_number").val());
		var e_cc_cvv2 = $.trim($("#e_cc_cvv2").val());
		var e_cc_type = $.trim($("#e_cc_type").val());
		var e_cc_month = $.trim($("#e_cc_month").val());
		var e_cc_year = $.trim($("#e_cc_year").val());

		if (e_cc_number == '') errors.push('Number required.');
		if (e_cc_type == '') errors.push('Type required.');
		if (e_cc_month == '') errors.push('Exp. Month required.');
		if (e_cc_year == '') errors.push('Exp. Year required.');
		
		if (errors.length > 0) {
			$(".entity-cc-info .error").html('<p>'+errors.join('</p><p>')+'</p>');
			$(".entity-cc-info .error").slideDown();
			return;
		}

		Processing_show();
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: BASE_PATH+'application/ajax/payments.php?action=saveEntityCreditCard',
			data: {
				entity_id: <?=$this->entity->id?>,
				fname: e_cc_fname,
				lname: e_cc_lname,
				address: e_cc_address,
				city: e_cc_city,
				state: e_cc_state,
				zip: e_cc_zip,
				number: e_cc_number,
				cvv2: e_cc_cvv2,
				type: e_cc_type,
				month: e_cc_month,
				year: e_cc_year
			},
			success: function(res) {
				if (res.success) {
					$(".entity-cc-info .success").slideDown(300).delay(1000).slideUp(300);
				}
			},
			complete: function(res) {
				KTApp.unblockPage();
			}
		});
	}

    $(document).ready(function(){

    	function switch_pt(){

    		if ($("input:radio[name='payment_type_selector']:checked").val() == 'internally') {
                $("#table_gateway").hide();
				<?php if($this->is_carrier==1){ ?>
				$("#table_terminal").hide();
				<?php }?>
				$("#table_carrier").hide();
                $("#table_internally").show();
			} else if($("input:radio[name='payment_type_selector']:checked").val() == 'carrier') {
				$("#table_gateway").hide();
			    $("#table_internally").hide();
				<?php if($this->is_carrier==1){ ?>
				$("#table_terminal").hide();
				<?php }?>
				$("#table_carrier").show();
				
			}

			<?php 
				if($this->is_carrier==1) { 
			?>

			else if($("input:radio[name='payment_type_selector']:checked").val() == 'terminal') {
				$("#table_gateway").hide();
				$("#table_internally").hide();
				$("#table_carrier").hide();
				$("#table_terminal").show();
			}

			<?php 
				}
			?>

			else {
				<?php if($this->is_carrier==1){ ?>
				$("#table_terminal").hide();
				<?php }?>
                $("#table_carrier").hide();
			    $("#table_internally").hide();	
				$("#table_gateway").show();
			}
		}
		
        $("#date_received").datepicker({dateFormat: 'mm/dd/yy'});
		$("#date_received_carrier").datepicker({dateFormat: 'mm/dd/yy'});
		$("#date_received_terminal").datepicker({dateFormat: 'mm/dd/yy'});

        $("input:radio[name='payment_type_selector']").change(function(){
            switch_pt();
		});

        $("ol.payment_options li").hide();
        
		$("#method").change(function(){
        	$("ol.payment_options li").hide();
        	switch ($(this).val()) {
        		case "9":
					$("#amount").val('<?= $this->entity->getTotalDeposit(false) ?>')
					$("#li_cc_numb").show();
					$("#li_cc_type").show();
					$("#li_cc_exp").show();
					$("#li_cc_auth").show();
				break;
       			case "1":
       			case "2":
       			case "3":
       			case "4":
					$("#li_ch_numb").show();
				break;
			}
		});
		
        $("#cc_type").change(function(){
        	if ($(this).val() == "0") {
        		$("#cc_type_other").show();
			} else {
        		$("#cc_type_other").hide();
			}
		});
		
        $("#internally_form").submit(function(){
        	var form_errors = "";
        	if ($("#date_received").val() == "") form_errors += '<li><b>Date received</b> required</li>';
        	if ($("#from_to").val() == "") form_errors += '<li><b>Payment From\/To</b> required</li>';
        	if ($("#amount").val() == "") form_errors += '<li><b>Amount</b> required</li>';
        	if (form_errors != "") {
        		$("#payment_form_errors ul").html(form_errors);
        		$("#payment_form_errors").slideDown();
        		return false;
			}
        	return true;
		});
		
		$("#carrier_form").submit(function(){
        	var form_errors = "";
        	if ($("#date_received_carrier").val() == "") form_errors += '<li><b>Date received</b> required</li>';
        	if ($("#from_to_carrier").val() == "") form_errors += '<li><b>Payment From\/To</b> required</li>';
        	if ($("#amount_carrier").val() == "") form_errors += '<li><b>Amount</b> required</li>';
        	if (form_errors != "") {
        		$("#payment_form_errors ul").html(form_errors);
        		$("#payment_form_errors").slideDown();
        		return false;
			}
        	return true;
		});
		
		<?php if($this->is_carrier==1){ ?>
			$("#terminal_form").submit(function(){
				var form_errors = "";

				if ($("#date_received_terminal").val() == "") form_errors += '<li><b>Date received</b> required</li>';
				if ($("#from_to_terminal").val() == "") form_errors += '<li><b>Payment From\/To</b> required</li>';
				if ($("#amount_terminal").val() == "") form_errors += '<li><b>Amount</b> required</li>';
				
				if (form_errors != "") {
					$("#payment_form_errors ul").html(form_errors);
					$("#payment_form_errors").slideDown();
					return false;
				}
				return true;
			});
		<?php }?>
		
        $("#payment_form_errors").click(function(){
        	$(this).slideUp();
		});

        $("#internally_form *").focus(function () {
			$("#payment_form_errors").slideUp();
		});
		
        switch_pt();
	});
	
	var busy = false;
	function updateInternalNotes(data) {

		var rows = "";
		for (i in data) {
			var email = data[i].email;
			var contactname = data[i].sender;
			
			if(data[i].system_admin == 2){
				email = "admin@cargoflare.com";
				contactname = "CargoFlare";
			}

			if ((data[i].access_notes == 0 ) || data[i].access_notes == 1 || data[i].access_notes == 2 ) {
				
				var discardStr = '';
				if(data[i].discard==1) {
					discardStr = ' style="text-decoration: line-through;" ';
				}
				
				if(data[i].system_admin == 1 || data[i].system_admin == 2) {
					rows += '<tr class="grid-body"><td style="white-space:nowrap;" class="grid-body-left" >'+data[i].created+'</td><td id="note_'+data[i].id+'_text"  '+discardStr+'><b>'+decodeURIComponent(data[i].text)+'</b></td><td>';	 
				} else if(data[i].priority==2) {
					rows += '<tr class="grid-body"><td class="grid-body-left" >'+data[i].created+'</td><td id="note_'+data[i].id+'_text"  '+discardStr+'><b style="font-size:12px;color:red;">'+decodeURIComponent(data[i].text)+'</b></td><td>';
				} else {
					rows += '<tr class="grid-body"><td class="grid-body-left">'+data[i].created+'</td><td id="note_'+data[i].id+'_text"  '+discardStr+'>'+decodeURIComponent(data[i].text)+'</td><td>';
				}
				
				rows += '<a href="mailto:'+email+'">'+contactname+'</a></td><td style="white-space: nowrap;" class="grid-body-right"  >';
				
				if ((data[i].access_notes == 0 ) || (data[i].access_notes == 1 && (data[i].sender_id == data[i].memberId)) || data[i].access_notes == 2 ) {
					
					if(data[i].sender_id == data[i].memberId  && data[i].system_admin == 0 ) {
						rows += '<img src="<?= SITE_IN ?>images/icons/strike.png" alt="Discard" title="Discard" width="16" height="16" class="action-icon edit-note" onclick="discardNote('+data[i].id+')"/>';
					}
					
					if(data[i].system_admin == 0 && data[i].access_notes != 0) {
						rows += '<img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit" width="16" height="16" class="action-icon edit-note" onclick="editInternalNote('+data[i].id+')"/>';
						rows += '<img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete" width="16" height="16" class="action-icon delete-note" onclick="delInternalNote('+data[i].id+')"/>';
					}
				}
			}
			
			rows += '</td></tr>';
		}
		
		$("#quick_notes_use").find('tbody').html(rows);
	}
	
	function addQuickNote() {
		var textOld = $("#internal_note").val();
		var str = textOld + " " + $("#quick_notes").val();
		$("#internal_note").val(str);
	} 
	
	function addInternalNote() {

		var text = $.trim($("#internal_note").val());
		var priority = $.trim($("#priority_notes").val());
		
		if (text == "") {
			return;
		}

		$("#internal_note").val("");
		$engine.asyncPost('<?php echo SITE_IN; ?>application/ajax/notes.php', {
			action: 'add',
			text: encodeURIComponent(text),
			entity_id: <?= $this->entity->id ?>,
			notes_type: <?= Note::TYPE_INTERNAL ?>,
			priority: priority
		},(response)=>{
			if (response.success == true) {
				updateInternalNotes(response.data);
			} else {
				$("#internal_note").val(text);
				$engine.notify("Can't save note. Try again later, please");
			}
		});
	}

	function editInternalNote(id) {
		var text = $.trim($("#note_"+id+"_text").text());
		$("#note_edit_form textarea").val(text);
		$("#edit_save").val(id);
		$("#note_edit_form").modal();
	}

	function delInternalNote(id) {
		$engine.confirm( "Are you sure want to delete this note?",(action)=>{
			if(action === "confirmed"){
				$engine.asyncPost('<?php echo SITE_IN; ?>application/ajax/notes.php', {
					action: 'del',
					id: id,
					entity_id: <?php echo $this->entity->id; ?>,
					notes_type: <?php echo Note::TYPE_INTERNAL; ?>
				},(response)=>{
					if (response.success == true) {
						updateInternalNotes(response.data);
					} else {
						$engine.notify("Can't delete note. Try again later, please");
					}
				});
			}
		});
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
	
	function showCheck(check_id,check_number){
		
		$("#checkId").val(check_id);
		$("#checkNumber").val(check_number);
		$("#checkdiv").modal();
		
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

	function validatePrintCheck() {

		Processing_show();
		$.ajax({
			type: 'POST',
			url: BASE_PATH+'application/ajax/entities.php',
			dataType: 'json',
			data: {
				action: "validate_print_check_new",
				order_id: '<?=$_GET["id"];?>'
				
			},
			success: function(res) {
				if (res.success) {
					$("#carrier_check_number").val(res.check_number);
					printCheck();
					} else {
					if(res.type==2){
						$("#carrier_check_number").val(res.check_number);
						$("#checkalertdiv").modal();
					}
					else {
						$("#carrier_check_number").val(res.check_number);
						$engine.notify(res.printform);
					}
				}
			},
			failed: function(res) {
				$engine.notify('Failed to print Check.');
			},
			complete: function(res) {
				KTApp.unblockPage();
			}
		});
	}
	
	function printCheck(){
		$("#checkalertdiv").modal("hide");
	}
</script>

<div>
	<?php include('order_menu.php');  ?>
</div>

<div style="margin-top:-77px;margin-bottom:52px; margin-left:30px;">
	<h3 class="details">Order #<?php echo $this->entity->getNumber() ?> Payments</h3>
</div>

<?php
	$isColor = $this->entity->isPaidOffColor();
	$Dcolor = "black";
	$Ccolor = "black";
	$Tcolor = "black";
	
	if($isColor['carrier']==1) {
		$Ccolor = "green";
	} elseif($isColor['carrier']==2) {
		$Ccolor = "red";
	}
	
	if($isColor['deposit']==1) {
		$Dcolor = "green";
	} elseif($isColor['deposit']==2) {
		$Dcolor = "red";
	}
	
	if($isColor['total']==1) {
		$Tcolor = "green";
	} elseif($isColor['total']==2) {
		$Tcolor = "red";
	}

	$assigned = $this->entity->getAssigned();
	$shipper = $this->entity->getShipper();
	$origin = $this->entity->getOrigin();
	$destination = $this->entity->getDestination();
	$vehicles = $this->entity->getVehicles();

	$Balance_Paid_By = "";
	if(in_array($this->entity->balance_paid_by, array(2, 3 , 16 , 17))) {
		$Balance_Paid_By = "<b>COD</b>";
	}
	
	if(in_array($this->entity->balance_paid_by, array(8, 9 , 18 , 19))) {
		$Balance_Paid_By = "COP";
	}

	if(in_array($this->entity->balance_paid_by, array(12, 13 , 20 , 21,24))) {
		$Balance_Paid_By = "Billing";
	}
	
	if(in_array($this->entity->balance_paid_by, array(14, 15 , 22 , 23))) {
		$Balance_Paid_By = "Billing";
	}
	
	if(trim($shipper->phone1)!="") {
		$code     = substr($shipper->phone1, 0, 3);
		$areaCodeStr1="";  
		
		$areaCodeRows = $this->daffny->DB->selectRow("*", "app_area_code", "WHERE  AreaCode='".$code."'");
		if (!empty($areaCodeRows)) {
			$areaCodeStr1 = "<b>".$areaCodeRows['StdTimeZone']."-".$areaCodeRows['statecode']."</b>"; 
		}
	}

	if(trim($shipper->phone2)!="") {
		$code     = substr($shipper->phone2, 0, 3);
		$areaCodeStr2="";
		$areaCodeRows2 = $this->daffny->DB->selectRow("*", "app_area_code", "WHERE  AreaCode='".$code."'");
		if (!empty($areaCodeRows2)) {
			$areaCodeStr2 = "<b>".$areaCodeRows2['StdTimeZone']."-".$areaCodeRows2['statecode']."</b>"; 
		}
	}	

	$phone1 = "1". $shipper->phone1;
	$phone2 = "1".$shipper->phone2;
	
	$phone1_ext ='';
	$phone2_ext ='';
	if($shipper->phone1_ext!='')
	$phone1_ext = " <b>X</b> ".$shipper->phone1_ext;
	if($shipper->phone2_ext!='')
	$phone2_ext = " <b>X</b> ".$shipper->phone2_ext;
	
	if($this->entity->source_id >0){
		try{
			$source = new Leadsource($this->daffny->DB);
			$source->load($this->entity->source_id);
			$sourceName = $source->company_name;
		} catch(FDException $e) {
			$sourceName = $this->entity->referred_by;
		}
	} elseif($this->entity->referred_id >0) {
		$sourceName = $this->entity->referred_by;
	}
?>

<div class="row" style="padding-left: 16px;">
	<div class="col-6 col-sm-12">
		<div class="form-group" style="margin-bottom:15px;">
			<label>The payment will appear in CargoFlare once it's processed by the gateway. </label>
			<div class="kt-radio-inline">
				@payment_type_selector@
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-12 col-sm-8">
		<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
			<div id="headingOne" class="hide_show">
				<div class="card-title">
					<h3 class="shipper_detail">Payment</h3>
				</div>
			</div>
			<div id="order_Payment_info" style="padding-left:20px;padding-right:20px;">
				<div class="msg-error" style="display: none;" id="payment_form_errors">
					<ul class="msg-list"></ul>
				</div>
				<form action="<?= getLink('orders/payments/id/'.$_GET['id']) ?>" method="post" id="internally_form">
					<input type="hidden" name="payment_type" value="internally"/>
					<input type="hidden" name="payment_id" disabled="disabled"/>
					<div class="record_internally">
						<div class="" id="table_internally">
							<div class="row">
								<div class="col-12 col-sm-6">
									<div class="new_form-group">
										@date_received@
									</div>
								</div>
								<div class="col-12 col-sm-6">
									<div class="new_form-group">
										@method@
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-12 col-sm-6">
									<div class="new_form-group">
										<label for="from_to"><span class="required">*</span>Payment From/To:</label>
										<select name="from_to" class="form-box-combobox" id="from_to"><option value="" selected="selected">Select One</option><option value="2-1" selected="selected">Shipper to Company</option><option value="1-2">Company to Shipper</option><option value="3-1">Carrier to Company</option></select>
									</div>
								</div>
								<div class="col-12 col-sm-6">
									<div class="new_form-group">
										@transaction_id@
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-12 col-sm-6">
									<div class="new_form-group">
										@amount@
									</div>
								</div>
								<div class="col-12 col-sm-6">
									<div class="new_form-group">
										<ol class="payment_options">
											<li id="li_ch_numb">
												<label for="ch_numb">Check Number:</label>
												<input type="text" class="form-box-textfield" id="ch_numb" name="ch_number"/>
											</li>
											<li id="li_cc_numb">
												<label for="cc_numb">CC# (last 4 digits):</label>
												<input type="text" class="decimal form-box-textfield" id="cc_numb" name="cc_numb" style="width: 40px;" maxlength="4"/>
											</li>
											<li id="li_cc_type">
												<label for="cc_type">Credit Card Type:</label>
												<select class="form-box-combobox" id="cc_type" name="cc_type" style="width: 110px;">
													<option value="">Select One</option>
													<?php foreach(Payment::$cctype_name AS $value => $label) : ?>
													<option value="<?= $value ?>"><?= $label ?></option>
													<?php endforeach; ?>
												</select>
												<input type="text" class="form-box-textfield" id="cc_type_other" name="cc_type_other" style="display: none; width: 100px;" maxlength="64"/>
											</li>
											<li id="li_cc_exp">
												<label for="cc_exp_month">Expiration Date:</label>
												<select class="form-box-combobox" name="cc_exp_month" id="cc_exp_month" style="width: 110px;">
													<option value="">Select Month</option>
													<?php for ($i = 1; $i <= 12; $i++) : ?>
													<option value="<?= $i ?>"><?= date('F', mktime(0,0,0,$i,1)) ?></option>
													<?php endfor; ?>
												</select>
												<select class="form-box-combobox" name="cc_exp_year" style="width: 110px;">
													<option value="">Select Year</option>
													<?php for ($i = (int)date('Y'); $i <= (int)date('Y') + 20; $i++) : ?>
													<option value="<?= $i ?>"><?= $i ?></option>
													<?php endfor; ?>
												</select>
											</li>
											<li id="li_cc_auth">
												<label for="cc_auth">Authorization Code:</label>
												<input type="text" class="form-box-textfield" id="cc_auth" name="cc_auth" />
											</li>
										</ol>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-12 col-sm-4">
									<div class="new_form-group">
										<?= submitButtons('', 'Record Payment','','btn-sm btn_dark_blue') ?>
									</div>
								</div>
								<div class="col-12 col-sm-4"></div>
							</div>
						</div>
					</div>
				</form>
				<form action="<?= getLink('orders/payments/id/'.$_GET['id']) ?>" method="post" id="carrier_form">
					<input type="hidden" name="payment_type" value="carrier"/>
					<input type="hidden" name="payment_id" disabled="disabled"/>
					<div class="" id="table_carrier">
						<div class="row">
							<div class="col-12">
								<div class="form-group">
									<label for="send_carrier_mail">
										Once the payment has been recorded CargoFlare will notify the carrier via email on file &nbsp;&nbsp;<input type="checkbox" name="send_carrier_mail" id="send_carrier_mail" value="1" checked="checked"/>
									</label>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-12 col-sm-6">
								<div class="new_form-group">
									@date_received_carrier@
								</div>
							</div>
							<div class="col-12 col-sm-6">
								<div class="new_form-group">
									@method@
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-12 col-sm-6">
								<div class="new_form-group">
									<label for="from_to"><span class="required">*</span>Payment From/To:</label></td>
									<select name="from_to_carrier" class="form-box-combobox" id="from_to_carrier"><option value="" selected="selected">Select One</option><option value="1-3" selected="selected">Company to Carrier</option><option value="2-3">Shipper to Carrier</option></select>
								</div>
							</div>
							<div class="col-12 col-sm-6">
								<div class="new_form-group">
									@transaction_id@
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-12 col-sm-6">
								<div class="new_form-group">
									@amount_carrier@
								</div>
							</div>
							<div class="col-12 col-sm-6">
								<div class="form-group">
									<ol class="payment_options">
										<li id="li_ch_numb">
											<label for="ch_numb">Check Number:</label>
											<input type="text" class="form-box-textfield" id="ch_numb" name="ch_number"/>
										</li>
										<li id="li_cc_numb">
											<label for="cc_numb">CC# (last 4 digits):</label>
											<input type="text" class="decimal form-box-textfield" id="cc_numb" name="cc_numb" style="width: 40px;" maxlength="4"/>
										</li>
										<li id="li_cc_type">
											<label for="cc_type">Credit Card Type:</label>
											<select class="form-box-combobox" id="cc_type" name="cc_type" style="width: 110px;">
												<option value="">Select One</option>
												<?php foreach(Payment::$cctype_name AS $value => $label) : ?>
												<option value="<?= $value ?>"><?= $label ?></option>
												<?php endforeach; ?>
											</select>
											<input type="text" class="form-box-textfield" id="cc_type_other" name="cc_type_other" style="display: none; width: 100px;" maxlength="64"/>
										</li>
										<li id="li_cc_exp">
											<label for="cc_exp_month">Expiration Date:</label>
											<select class="form-box-combobox" name="cc_exp_month" id="cc_exp_month" style="width: 110px;">
												<option value="">Select Month</option>
												<?php for ($i = 1; $i <= 12; $i++) : ?>
												<option value="<?= $i ?>"><?= date('F', mktime(0,0,0,$i,1)) ?></option>
												<?php endfor; ?>
											</select>
											<select class="form-box-combobox" name="cc_exp_year" style="width: 110px;">
												<option value="">Select Year</option>
												<?php for ($i = (int)date('Y'); $i <= (int)date('Y') + 20; $i++) : ?>
												<option value="<?= $i ?>"><?= $i ?></option>
												<?php endfor; ?>
											</select>
										</li>
										<li id="li_cc_auth">
											<label for="cc_auth">Authorization Code:</label>
											<input type="text" class="form-box-textfield" id="cc_auth" name="cc_auth" />
										</li>
									</ol>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-12 col-sm-6">
								<div class="form-group">
									<?= submitButtons('', 'Record Payment') ?>
								</div>
							</div>
						</div>
					</div>
				</form>
				<?php 
					if($this->is_carrier==1){ 
				?>
				<form action="<?= getLink('orders/payments/id/'.$_GET['id']) ?>" method="post" id="terminal_form">
					<input type="hidden" name="payment_type" value="terminal"/>
					<input type="hidden" name="payment_id" disabled="disabled"/>
					<div class="" id="table_terminal">
						<div class="row">
							<div class="col-12 col-sm-6">
								<div class="new_form-group">
									@date_received_terminal@
								</div>
							</div>
							<div class="col-12 col-sm-6">
								<div class="new_form-group">
									@method@
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-12 col-sm-6">
								<div class="new_form-group">
									@from_to_terminal@
								</div>
							</div>
							<div class="col-12 col-sm-6">
								<div class="new_form-group">
									@transaction_id@
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-12 col-sm-6">
								<div class="new_form-group">
									@amount_terminal@
								</div>
							</div>
							<div class="col-12 col-sm-6">
								<div class="new_form-group">
									<ol class="payment_options">
										<li id="li_ch_numb">
											<label for="ch_numb">Check Number:</label>
											<input type="text" class="form-box-textfield" id="ch_numb" name="ch_number"/>
										</li>
										<li id="li_cc_numb">
											<label for="cc_numb">CC# (last 4 digits):</label>
											<input type="text" class="decimal form-box-textfield" id="cc_numb" name="cc_numb" style="width: 40px;" maxlength="4"/>
										</li>
										<li id="li_cc_type">
											<label for="cc_type">Credit Card Type:</label>
											<select class="form-box-combobox" id="cc_type" name="cc_type" style="width: 110px;">
												<option value="">Select One</option>
												<?php foreach(Payment::$cctype_name AS $value => $label) : ?>
												<option value="<?= $value ?>"><?= $label ?></option>
												<?php endforeach; ?>
											</select>
											<input type="text" class="form-box-textfield" id="cc_type_other" name="cc_type_other" style="display: none; width: 100px;" maxlength="64"/>
										</li>
										<li id="li_cc_exp">
											<label for="cc_exp_month">Expiration Date:</label>
											<select class="form-box-combobox" name="cc_exp_month" id="cc_exp_month" style="width: 110px;">
												<option value="">Select Month</option>
												<?php for ($i = 1; $i <= 12; $i++) : ?>
												<option value="<?= $i ?>"><?= date('F', mktime(0,0,0,$i,1)) ?></option>
												<?php endfor; ?>
											</select>
											<select class="form-box-combobox" name="cc_exp_year" style="width: 110px;">
												<option value="">Select Year</option>
												<?php for ($i = (int)date('Y'); $i <= (int)date('Y') + 20; $i++) : ?>
												<option value="<?= $i ?>"><?= $i ?></option>
												<?php endfor; ?>
											</select>
										</li>
										<li id="li_cc_auth">
											<label for="cc_auth">Authorization Code:</label>
											<input type="text" class="form-box-textfield" id="cc_auth" name="cc_auth" />
										</li>
									</ol>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-12 col-sm-4">
								<div class="new_form-group">
									<?= submitButtons('', 'Record Payment') ?>
								</div>
							</div>
						</div>
					</div>
				</form>
				<?php 
					}
				?>
				<form action="<?= getLink('orders/payments/id/'.$_GET['id']) ?>" method="post" id="gateway_form">
					<input type="hidden" name="payment_type" value="gateway"/>
					<div id="table_gateway" style="display:none;">		
						<div class="row">
							<div class="col-5">
								<h5 class="kt-widget14__title mb-4 mt-2"><span class="required">*</span> Amount</h5>
								<div class="form-group payment_option_new_label">
									@gw_pt_type@
								</div>
								<div class="form-group text-center">
									<button type="submit" class="btn btn-primary">Submit</button>
								</div>
							</div>
							<div class="col-7">
								<div class="new_form-group">
									@cc_fname@
								</div>
								
								<div class="new_form-group">
									@cc_lname@
								</div>
								
								<div class="new_form-group">
									@cc_type@
								</div>
								
								<div class="new_form-group">
									@cc_number@
								</div>
								<div class="new_form-group">
									@cc_cvv2@ <img src="<?=SITE_IN?>images/icons/cards.gif" alt="Card Types" width="129" height="16" style="vertical-align:middle;margin-top:8px;margin-left:10px;" />
								</div>
								<div class="new_form-group">
									@cc_month@ <span class="pull-left" style="margin:7px 5px 0 15px;">/</span> @cc_year@
								</div>
								<div class="new_form-group">
									@cc_address@
									<div id="suggestions-box-cc" class="suggestions" style="position:absolute; left:49px;"></div>
								</div>
								<div class="new_form-group">
									@cc_city@
								</div>
								<div class="new_form-group">
									@cc_state@
								</div>
								<div class="new_form-group">
									@cc_zip@
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="col-4 col-sm-4">
		<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
			<div id="headingOne" class="hide_show">
				<div class="card-title collapsed" data-toggle="collapse" data-target="#shipper_information_new_info" aria-expanded="false" aria-controls="shipper_information_new_info">
					<h3 class="shipper_detail">Payment Terms</h3>
				</div>
			</div>
			<div id="shipper_information_new_info" class="mb-3" style="padding-left:20px;padding-right:20px;">
				<table class="table table-bordered">
					<tr>
						<td>
							<img style="vertical-align: middle" src="<?= SITE_IN ?>images/icons/dollar.png" alt="Total Tariff" title="Total Tariff" width="16" height="16"/> <strong>Total Amount: </strong>
						</td>
						<td>
							<span class='<?= $Tcolor;?>'><?= $this->entity->getTotalTariff() ?></span>
						</td>
					</tr>
					<tr>
						<td>
							<img style="vertical-align: middle" src="<?= SITE_IN ?>images/icons/truck.png" alt="Tariff to Shipper" title="Tariff to Shipper" width="16" height="16"/> <strong>Carrier Fee: </strong>
						</td>
						<td>
							<span class='<?= $Ccolor;?>'><?= $this->entity->getCarrierPay() ?></span>
						</td>
					</tr>
					<tr>
						<td>
							<img style="vertical-align: middle" src="<?= SITE_IN ?>images/icons/person.png" alt="Tariff by Customer" title="Tariff by Customer" width="16" height="16"/> <strong>Broker Fee: </strong>
						</td>
						<td>
							<span class='<?= $Dcolor;?>'><?= $this->entity->getTotalDeposit() ?></span>
						</td>
					</tr>
				</table>
				<?php 
					$payments_terms = $this->entity->payments_terms;
					
					if(in_array($this->entity->balance_paid_by, array(2, 3 , 16 , 17 , 8,9,18,19))){   
						$payments_terms = "COD / COP";
					}

					if($payments_terms!="") {
				?>
				<p class="text-justify">
					<b>Payment Terms:</b><br/> <?php print $payments_terms;?>
				</p>
				<?php 
					} 
				?>
			</div>
		</div>
	</div>
</div>

<?php
	// including invoice uploader plugin When have access
	//if($_SESSION['member']['pay_check_system_access'] != 0){
		include("InvoicePlugin.php");
	//}
?>

<div class="row">
	<div class="col-12 col-sm-8">
		<div class="row">
			<div class="col-6">
				<?php
					$phone1_ext = '';
					$phone2_ext = '';
					$phone3_ext = '';
					$phone4_ext = '';
					if ($origin->phone1_ext != '')
					$phone1_ext = " <b>X</b> " . $origin->phone1_ext;
					if ($origin->phone2_ext != '')
					$phone2_ext = " <b>X</b> " . $origin->phone2_ext;
					if ($origin->phone3_ext != '')
					$phone3_ext = " <b>X</b> " . $origin->phone3_ext;
					if ($origin->phone4_ext != '')
					$phone4_ext = " <b>X</b> " . $origin->phone4_ext;
				?>
				
				<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
					
					<div id="headingOne" class="hide_show">
						<div class="card-title collapsed" data-toggle="collapse" data-target="#shipper_information_new_info" aria-expanded="false" aria-controls="shipper_information_new_info">
							<h3 class="shipper_detail">Customer Details</h3>
						</div>
					</div>
					
					<div id="customer_details_new_info" style="padding-left:20px;padding-right:20px;">
						<div>
							<table class="table table-bordered">
								<tr>
									<td>
										<strong>Account ID</strong>
									</td>
									
									<td width="4%" align="center"><b>:</b></td>
									
									<td align="left">
										<a href="/application/accounts/details/id/<?= $this->entity->account_id ?>" target="_blank">
											<?= $this->entity->account_id ?>
										</a>
									</td>
								</tr>
								<tr>
									<td>
										<strong>Contact</strong>
									</td>
									<td width="4%" align="center"><b>:</b></td>
									<td><?= $shipper->fname ?> <?= $shipper->lname ?></strong></td>
								</tr>
								<tr>
									<td><strong>Company</strong></td>
									<td width="4%" align="center"><b>:</b></td>
									<td><?= $shipper->company; ?></td>
								</tr>
								<tr>
									<td><strong>Address</strong></td>
									<td width="4%" align="center"><b>:</b></td>
									<td><?= $shipper->address1; ?> <?= $shipper->address2; ?></td>
								</tr>
								<tr>
									<td>
										<strong>City</strong>
									</td>
									<td width="4%" align="center"><b>:</b></td>
									<td><?= $shipper->city; ?></td>
								</tr>
								<tr>
									<td>
										<strong>State/Zip</strong>
									</td>
									<td width="4%" align="center"><b>:</b></td>
									<td><?= $shipper->state; ?> <?= $shipper->zip_code; ?></td>
								</tr>
								<tr>
									<td>
										<strong>Phone 1</strong>
									</td>
									<td width="4%" align="center"><b>:</b></td>
									<td>
									<?php if ($mobileDevice == 1) { ?>
										<a href="tel:<?php print $phone1; ?>" ><?= formatPhone($shipper->phone1) ?></a>
										<?php } else { ?>
										<a href="javascript:void(0);" onclick="customPhoneSms('<?= $phone1; ?>');"><?= formatPhone($shipper->phone1); ?></a>
									<?php } ?>
									<?= $phone1_ext; ?> <?= $areaCodeStr1; ?>
									</td>
								</tr>
								<tr>
									<td><strong>Phone 2</strong></td>
									<td width="4%" align="center"><b>:</b></td>
									<td>
										<?php if ($mobileDevice == 1) { ?>
										<a href="tel:<?php print $phone2; ?>" ><?= formatPhone($shipper->phone2) ?></a>
										<?php } else { ?>
										<a href="javascript:void(0);" onclick="customPhoneSms('<?= $phone2; ?>');"><?= formatPhone($shipper->phone2); ?></a>
										<?php } ?>
										<?= $phone2_ext; ?> <?= $areaCodeStr2; ?>								
									</td>
								</tr>
								<tr>
									<td><strong>Mobile</strong></td>
									<td><b>:</b></td>
									<td><?= formatPhone($shipper->mobile); ?></td>
								</tr>
								<tr>
									<td><strong>Fax</strong></td>
									<td><b>:</b></td>
									<td><?= formatPhone($shipper->fax); ?></td>
								</tr> 
								<tr>
									<td><strong>Email</strong></td>
									<td><b>:</b></td>
									<td><a href="mailto:<?= $shipper->email ?>"><?= $shipper->email ?></a></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="col-6">
				<?php
					if (isset($this->dispatchSheet)) {
						
						$carrier_company_name = $this->dispatchSheet->carrier_company_name;
						$carrier_contact_name = $this->dispatchSheet->carrier_contact_name;
						$carrier_phone_1 = $this->dispatchSheet->carrier_phone_1;
						$carrier_phone_2 = $this->dispatchSheet->carrier_phone_2;
						$carrier_fax = $this->dispatchSheet->carrier_fax;
						$carrier_email = $this->dispatchSheet->carrier_email;
						$carrier_address = $this->dispatchSheet->carrier_address;
						$carrier_state = $this->dispatchSheet->carrier_state;
						$carrier_city = $this->dispatchSheet->carrier_city;
						$carrier_zip = $this->dispatchSheet->carrier_zip;
						
						$phone1_ext = '';
						$phone2_ext = '';
						if ($this->dispatchSheet->carrier_phone1_ext != '')
						$phone1_ext = " <b>X</b> " . $this->dispatchSheet->carrier_phone1_ext;
						if ($this->dispatchSheet->carrier_phone2_ext != '')
						$phone2_ext = " <b>X</b> " . $this->dispatchSheet->carrier_phone2_ext;
					}
				?>
		
				<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
							
					<div class="hide_show">
						<div class="card-title collapsed" data-toggle="collapse" data-target="#customer_details_new_info" aria-expanded="false" aria-controls="customer_details_new_info">
							<h3 class="shipper_detail">Carrier Details</h3>
						</div>
					</div>
					
					<div id="customer_details_new_info" style="padding-left:20px;padding-right:20px;">
						<table class="table table-bordered " >
							<tr><td width="23%"><strong>Account ID</strong></td><td width="4%" align="center"  ><b>:</b></td><td align="left"  ><a target="_blank"href="/application/accounts/details/id/<?= $this->entity->carrier_id ?>"><?= $this->entity->carrier_id ?></a></td></tr>
							<tr><td width="23%"><strong>MC Number</strong></td><td width="4%" align="center"  ><b>:</b></td><td align="left"  ><?= $this->dispatchSheet->carrier_insurance_iccmcnumber ?></td></tr>
							
							<tr><td width="30%"><strong>Contact</strong></td><td width="4%" align="center"  ><b>:</b></td><td><?= $carrier_contact_name ?></td></tr>
							<tr><td ><strong>Company</strong></td><td width="4%" align="center" ><b>:</b></td><td><?= $carrier_company_name ?></td></tr>
							<tr><td ><strong>Address</strong></td><td width="4%" align="center"  ><b>:</b></td><td><?= $carrier_address ?></td></tr>
							<tr><td ><strong>City</strong></td><td width="4%" align="center"  ><b>:</b></td><td><?= $carrier_city ?></td></tr> 
							<tr><td ><strong>State/Zip</strong></td><td width="4%" align="center"  ><b>:</b></td>
								<td>
									<?php echo $carrier_state; ?> <?php echo str_pad($carrier_zip, 5, '0', STR_PAD_LEFT);// echo "Count:".strlen($carrier_zip);?> 
								</td>
							</tr>
							<tr><td ><strong>Phone 1</strong></td><td width="4%" align="center"><b>:</b></td><td><?= formatPhone($carrier_phone_1); ?><?= $phone1_ext ?></td></tr>
							<tr><td ><strong>Phone 2</strong></td><td width="4%" align="center"><b>:</b></td><td><?= formatPhone($carrier_phone_2); ?><?= $phone2_ext ?></td></tr>
							<tr><td ><strong>Fax</strong></td><td width="4%" align="center"><b>:</b></td><td><?= $carrier_fax ?></td></tr>
							<tr><td ><strong>Email</strong></td><td width="4%" align="center"><b>:</b></td><td><a href="mailto:<?= $carrier_email ?>"><?= $carrier_email ?></a></td></tr>
							<?php $carrier = $this->entity->getCarrier(); ?>
							<?php if ($carrier instanceof Account) { ?>
								<tr><td ><strong>Hours of Operation</strong></td><td width="4%" align="center"  ><b>:</b></td><td><?= $carrier->hours_of_operation ?></td></tr> 
							<?php } ?>
						</table>  
					</div>
				</div>
			</div>
		</div>
	</div> 
</div>

<div class="row">
	<div class="col-12 col-sm-8">
		<div class="form-group" style="background:#fff;">
			<h3 class="shipper_detail" style="border:1px solid #ebedf2;">Balances</h3>
			<p style="padding-left:23px; padding-right: 23px;">The balance of this order is to be paid by: <strong><?=Entity::$balance_paid_by_string[$this->entity->balance_paid_by]?></strong></p>
			
			<div class="row" style="padding-left:23px; padding-right: 23px;">
				<div class="col-lg-6 col-sm-12">
					<div class="form-group">
						<table  class="table table-bordered">
							<tr >
								<td class="grid-head-left" >We owe them</td>
								<td class="grid-head-right" width="250">Balance</td>
							</tr>
							<tr class="grid-body">
								<td class="grid-body-left">Carrier</td>
								<td class="grid-body-right">@we_carrier_paid@ @we_carrier@</td>
							</tr>
							<tr class="grid-body">
								<td class="grid-body-left">Shipper</td>
								<td class="grid-body-right">@we_shipper_paid@ @we_shipper@</td>
							</tr>
						</table>
						
					</div>
				</div>
				
				<div class="col-lg-6 col-sm-12">
					<div class="form-group">
						<table  class="table table-bordered">
							<tr >
								<td class="grid-head-left">They owe us</td>
								<td class="grid-head-right" width="250">Balance</td>
							</tr>
							<tr class="grid-body">
								<td class="grid-body-left">Carrier</td>
								<td class="grid-body-right">@they_carrier_paid@ @they_carrier@</td>
							</tr>
							<tr class="grid-body">
								<td class="grid-body-left">Shipper</td>
								<td class="grid-body-right">@they_shipper_paid@ @they_shipper@</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row mb-5">
	<div class="col-12 col-sm-8">
		<h3 class="shipper_detail" style="border:1px solid #ebedf2;margin-bottom:-1px;">Payments Made and/or Received</h3>
		<div style="padding:22px 23px 12px 23px; background:#FFF;">
			<table class="table table-bordered">
				<tr>
					<td><?= $this->order->getTitle('number', '#') ?></td>
					<td width="70"><?= $this->order->getTitle('date_received', 'Date') ?></td>
					<td>From =&gt; To</td>
					<td><?= $this->order->getTitle('amount', 'Amount') ?></td>
					<td><?= $this->order->getTitle('method', 'Method') ?></td>
					<td><?= $this->order->getTitle('entered_by', 'Entered By') ?></td>
					<td  width="80" colspan="2">Actions</td>
					<td class="grid-head-right" width="80" >Sync Payment</td>
				</tr>
				<?php  if (count($this->payments) == 0) : ?>
				<tr class="grid-body">
					<td class="grid-body-left grid-body-right" align="center" colspan="9"><i>No Records</i></td>
				</tr>
				<?php endif; ?>
				<?php  foreach ($this->payments as $payment) : ?>
				<tr class="grid-body" id="row-<?=$payment->id?>">
					<td class="grid-body-left"><?= $payment->getNumber() ?></td>
					<td><?= $payment->getDate() ?></td>
					<td><?= $payment->getFrom() ?> =&gt; <?= $payment->getTo() ?></td>
					<td><?= $payment->getAmount() ?></td>
					<td>
						<?= $payment->getMethod() ?>
						<?php if ($payment->method == Payment::M_CC) : ?>
						<span class="like-link payment-info-trigger">[Info]</span>
						<div class="payment-info">
							<span class="label">Auth code: </span><?= $payment->cc_auth ?><br />
							<span class="label">Transaction ID: </span><?= $payment->transaction_id ?>
						</div>
						<?php elseif (($payment->method == Payment::M_CA_CHECK) || ($payment->method == Payment::M_CO_CHECK) || ($payment->method == Payment::M_PE_CHECK) || ($payment->method == Payment::M_COMCHEK)) : ?>
						<span class="like-link payment-info-trigger">[Info]</span>
						<div class="payment-info">
							<span class="label">Check number: </span><?= $payment->check ?>
						</div>
						<?php endif ;?>
					</td>
					<td><?= $payment->getEnteredBy() ?></td>
					<td style="width: 16px;"><?=editIcon('javascript:editPayment('.$payment->id.')')?></td>
					<td style="width: 16px;" >
						<div title="Delete" alt="Delete" class="pointer" onclick="deleteItemReloaded(<?php echo $payment->id;?>)" width="16" height="16">
							<i class="fa fa-trash" aria-hidden="true" style="color: red;"></i>
						</div>
					</td>
					<td style="width: 16px;" class="grid-body-right">
						<?php
							if(date('Y-m-d',strtotime($payment->date_received)) == date('Y-m-d')){
						?>
						<button onclick="$operations.void(<?php echo $payment->gateway?>, <?php echo $payment->transaction_id?>, <?php echo $payment->amount?>)" class="btn btn-danger">Void</button>
						<?php
							} else {
						?>
						<button onclick="$operations.refund(<?php echo $payment->gateway?>, <?php echo $payment->transaction_id?>, <?php echo $payment->amount?>)" class="btn btn-warning">Refund</button>
						<?php
							}
						?>
					</td>
				</tr>
				<?php endforeach; ?>	
			</table>
		</div>
	</div>
</div>

<div class="row mb-5">
	<div class="col-12 col-sm-8">
		<?php if($_SESSION['parent_id'] ==1){?>
		<h3 class="shipper_detail" style="border:1px solid #ebedf2;">Invoices and Bills</h3>
		<div style="padding:22px 23px 12px 23px; background:#FFF;">
			<div style="float:left;">
			<?= functionButton('Create Vendor Bill', 'createBill()' ,'','btn-sm btn_dark_blue')  ?> </div>
			<div style="float:left;margin-left:30px;margin-bottom:15px;">
			<?= functionButton('Fix Vendor Bill Issues', 'createBillWithFix()','','btn_dark_blue btn-sm') ?>  </div>
			<div style="float:left; margin-left:30px;margin-bottom:15px;"><?= functionButton('Sync Customer Invoice', 'createInvoice()' ,'','btn-sm btn_light_green') ?></div>
			<div style="float:left;margin-left:30px;margin-bottom:15px;"><?= functionButton('Fix Customer Invoice', 'fixCreateInvoice()','','btn-sm btn_dark_blue') ?></div>
			<table class="table table-bordered">
				<tr >
					<td class="grid-head-left"><?= $this->order->getTitle('number', '#') ?></td>
					<td width="70"><?= $this->order->getTitle('date_received', 'Date') ?></td>
					<td>From =&gt; To</td>
					<td><?= $this->order->getTitle('amount', 'Amount') ?></td>
					<td><?= $this->order->getTitle('method', 'Method') ?></td>
					<td><?= $this->order->getTitle('entered_by', 'Entered By') ?></td>
					<td class="grid-head-right" width="80" colspan="2">Actions</td>
				</tr>
				<?php  if (count($this->payments) == 0) : ?>
				<tr class="grid-body">
					<td class="grid-body-left grid-body-right" align="center" colspan="9"><i>No Records</i></td>
				</tr>
				<?php endif; ?>
				<?php  foreach ($this->payments as $payment) : ?>
				<tr class="grid-body" id="row-<?=$payment->id?>">
					<td class="grid-body-left"><?= $payment->getNumber() ?></td>
					<td><?= $payment->getDate() ?></td>
					<td><?= $payment->getFrom() ?> =&gt; <?= $payment->getTo() ?></td>
					<td><?= $payment->getAmount() ?></td>
					<td>
						<?= $payment->getMethod() ?>
						<?php if ($payment->method == Payment::M_CC) : ?>
						<span class="like-link payment-info-trigger">[Info]</span>
						<div class="payment-info">
							<span class="label">Auth code: </span><?= $payment->cc_auth ?><br />
							<span class="label">Transaction ID: </span><?= $payment->transaction_id ?>
						</div>
						<?php elseif (($payment->method == Payment::M_CA_CHECK) || ($payment->method == Payment::M_CO_CHECK) || ($payment->method == Payment::M_PE_CHECK) || ($payment->method == Payment::M_COMCHEK)) : ?>
						<span class="like-link payment-info-trigger">[Info]</span>
						<div class="payment-info">
							<span class="label">Check number: </span><?= $payment->check ?>
							</div>
						<?php endif ;?>
					</td>
					<td><?= $payment->getEnteredBy() ?></td>
					<td style="width: 16px;"><?=editIcon('javascript:editPayment('.$payment->id.')')?></td>
					<td style="width: 16px;" class="grid-body-right"><?=deleteIcon(getLink('ajax', 'payments.php?action=delete&id='.$payment->id), "row-".$payment->id)?></td>
				</tr>
				<?php endforeach; ?>
			</table>
		</div>
	</div>
</div>

<div class="row mb-5">
	<div class="col-12 col-sm-8">
		<h3 class="shipper_detail" style="border:1px solid #ebedf2;">Print Check History</h3>
		<div style="padding:22px 23px 12px 23px; background:#FFF;">
			<?php print  functionButton('Print Check', 'validatePrintCheck(\''.md5($_GET['id']).'\')','','btn-sm btn_light_green');  ?>
			<table class="table table-bordered" style="margin-top:20px;">
				<tr>
					<td class="grid-head-left">#</td>
					<td width="70">Created</td>
					<td>From =&gt; To</td>
					<td>Amount</td>
					<td>Method</td>
					<td class="grid-head-right">Entered By</td>
					<td class="grid-head-right" width="80" colspan="2">Actions</td>						
				</tr>
				<?php  if (count($this->checks) == 0) : ?>
				<tr class="grid-body">
					<td class="grid-body-left grid-body-right" align="center" colspan="8"><i>No Records</i></td>
				</tr>
				<?php endif; ?>
				<?php
					foreach ($this->checks as $check) : 
					$entered_name = '';
					if ($check['entered_by'] == 0  || $check['entered_contactname'] == '')
					$entered_name = 'System';
					else
					$entered_name = $check['entered_contactname'];
				?>
				<tr class="grid-body" id="row-<?=$check['id']?>">
					<td class="grid-body-left"><?=$check['id']?></td>
					<td><?= date("m/d/Y", strtotime($check['created']))?></td>
					<td>Company =&gt; Carrier</td>
					<td><?= "$ " . number_format($check['amount'], 2, '.', ','); ?></td>
					<td>Print check <span class="like-link" onclick="showCheck(<?=$check['id']?>,'<?= $check['check_number'] ?>');">[Preview]</span>
					</td>						
					<td><?= $entered_name ?></td>
					<td style="width: 32px;" class="grid-body-right"><?=deleteIcon(getLink('ajax', 'checks.php?action=delete&id='.$check['id']), "row-".$check['id'])?></td>
					<td style="width: 16px;"><?php print  functionButton('RePrint', 'printCheckFormPreview(\''.md5($check['id']).'\')');?></td>
				</tr>
				<?php endforeach; ?>
			</table>
		</div>
		<?php }?>
	</div>
</div>

<div class="row mb-5">
	<div class="col-12 col-sm-8">
		<h3 class="shipper_detail" style="border:1px solid #ebedf2;">Internal Notes</h3>
		<div style="padding:22px 23px 12px 23px; background:#FFF;">
			<div class="form-group">
				<?php $notes = $this->notes; ?>			
				<textarea class="form-control"  maxlength="1000" id="internal_note"></textarea>			
			</div>
			<div class="new_form-group">
				<div class="col-6">
					<label>Quick Notes&nbsp;</label>
					<select name="quick_notes" id="quick_notes" class="form-control" onchange="addQuickNote();">
						<option value="">--Select--</option>
						<option value="Document Upload: Release(s) attached.">Document Upload: Release(s) attached.</option>
						<option value="Document Upload: Gate Pass(es) attached.">Document Upload: Gate Pass(es) attached.</option>
						<option value="Document Upload: Dock Receipt attached.">Document Upload: Dock Receipt attached.</option>
						<option value="Document Upload: Photos attached.">Document Upload: Photos attached.</option>
						<option value="Phoned: Bad Mobile.">Phoned: Bad Number.</option>
						<option value="Phoned: No Voicemail.">Phoned: No Voicemail.</option>
						<option value="Phoned: Left Message.">Phoned: Left Message.</option>
						<option value="Phoned: No Answer.">Phoned: No Answer.</option>
						<option value="Phoned: Spoke to Customer.">Phoned: Spoke to Customer.</option>
						<option value="Phoned: Spoke to carrier about pick-up.">Phoned: Spoke to carrier about pick-up.</option>
						<option value="Phoned: NSpoke to carrier about drop-off.">Phoned: Spoke to carrier about drop-off.</option>
						<option value="Phoned: Customer requested carrier info.">Phoned: Customer requested carrier info.</option>
						<option value="Phoned: Customer reported  damage.">Phoned: Customer reported damage.</option>
						<option value="Phoned: Customer canceled, late pick-up.">Phoned: Customer canceled, late pick-up.</option>
						<option value="Phoned: Customer canceled, no reason given.">Phoned: Customer canceled, no reason given.</option>
						<option value="Phoned: Customer canceled, through e-Mail.">Phoned: Customer canceled, through e-Mail.</option>
						<option value="Phoned: Customer was happy with the transport.">Phoned: Customer was happy with the transport.</option>
						<option value="Phoned: Customer was un-happy with the transport.">Phoned: Customer was un-happy with the transport.</option>
						<option value="Phoned: Customer want a refund.">Phoned: Customer want's a refund.</option>
						<option value="Phoned: Not Interested.">Phoned: Not Interested.</option>
						<option value="Phoned: Do Not Call.">Phoned: Do Not Call.</option>
					</select>
					<br/>
					<br/>
					<label>Priority</label>
					<select name="priority_notes" id="priority_notes" class="form-control" >
						<option value="1">Low</option>
						<option value="2">High</option>
					</select>
					<br/>
					<br/>
					<?= functionButton('Add Note', 'addInternalNote()') ?>
				</div>
			</div>
			<hr/>
			<table class=" table table-bordered" id="quick_notes_use">
				<thead>
					<tr >
						<td class="grid-head-left"><?=$this->order->getTitle('created', 'Date')?></td>
						<td width="65%">Note</td>
						<td width="15%">User</td>
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
							$email = "admin@cargoflare.com";
							$contactname = "System";
						}
						
						if (($_SESSION['member']['access_notes'] == 0 ) || $_SESSION['member']['access_notes'] == 1 || $_SESSION['member']['access_notes'] == 2 ) {
					?>
					<tr class="grid-body" >
						<td style="white-space:nowrap;"  class="grid-body-left" <?php if($note->priority==2){?> style="color:#FF0000"<?php }?>><?= $note->getCreated("m/d/y h:i a") ?></td>
						<td id="note_<?= $note->id ?>_text" style=" <?php if($note->discard==1){ ?>text-decoration: line-through;<?php }?><?php if($note->priority==2){?>color:#FF0000;<?php }?>"><?php if($note->system_admin == 1 || $note->system_admin == 2){?><b><?= $note->getText() ?></b><?php }elseif($note->priority==2){?><b style="font-size:12px;"><?= $note->getText() ?></b><?php }else{?><?= $note->getText() ?><?php }?></td>
						<td style="text-align: center;" <?php if($note->priority==2){?>style="color:#FF0000"<?php }?>><a href="mailto:<?= $email ?>"><?= $contactname ?></a></td>
						<td class="grid-body-right" style="white-space: nowrap;" <?php if($note->priority==2){?>style="color:#FF0000"<?php }?>>
							
							<?php
								if (($_SESSION['member']['access_notes'] == 0 ) ||($_SESSION['member']['access_notes'] == 1 && ($note->sender_id == (int)$_SESSION['member_id']))|| $_SESSION['member']['access_notes'] == 2){
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
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.11.4/ckeditor.js"></script>
<script>

	CKEDITOR.replace('mail_body_new');

	$("#pt_gateway").change(function(){
		$operations.getSavedCards(<?php echo $this->entity->account_id;?>);
	});
	
	$(".methods").change(function(){
		var isCreditCard = $(this).val();
		if( isCreditCard == 9 ){
			$operations.getSavedCards(<?php echo $this->entity->account_id;?>);
		}
	});

	function deleteItemReloaded(id){
		Swal.fire({
			title: 'Are you sure?',
			text: "Are you sure you want to delete this record?!",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, delete it!'
		}).then((result) => {
			if (result.value) {
				$.ajax({
					url: '<?php echo getLink('ajax', 'payments.php?action=delete&id=');?>'+id,
					data: {},
					type: 'GET',
					dataType: 'json',
					success: function(response) {
						if (response.success == true) {
							location.reload();
						} else {
							alert("Can't delete item.");
						}
					}
				});
		    }
		});
	}
	
	function GetCards(){
		
		var AccountID = "<?php echo $this->entity->account_id;?>";
		$.ajax({
            type: 'POST',
            url: BASE_PATH + 'application/ajax/accounts.php',
            dataType: 'json',
            data: {
                action: "AllCards",
                AccountID: AccountID
			},
            success: function (response) {
				var cards = response.Cards;
				var ResponseView = "";
				var html = "";
				
				for(i=0 ;i<response.Cards.length;i++){
					
					var Updated, Recent, Status, Type;
					
					if(cards[i].Updated == null){
						Updated = "Never Updated";
						} else {
						Updated = cards[i].Updated;
					}
					if(cards[i].Recent == 1){
						Recent = "Recently Used";
						} else {
						Recent = "";
					}
					if(cards[i].Status == 1){
						Status = "Active";
						} else {
						Status = "In-Active";
					}
					
					if( cards[i].Type == 0 ){
						Type = "Others";
						} else if ( cards[i].Type == 1 ) {
						Type = "Visa";
						} else if ( cards[i].Type == 2 ) {
						Type = "MasterCard";
						} else if ( cards[i].Type == 3 ) {
						Type = "AMEX";
						} else {
						Type = "Discover";
					}
					
					html += "<tr>";
					html += "<td><input value='"+cards[i].CardId+"' onclick='ApplyCardDetails("+i+")' id='CrCard-"+i+"' name='selected-card' type='radio' class='form-box-radio selected-card'></td>";
					html += "<td id='AccountId-"+i+"'>"+cards[i].AccountID+"</td>";
					html += "<td id='Number-"+i+"'>"+cards[i].Number+"</td>";
					html += "<td id='ExpMonth-"+i+"'>"+cards[i].ExpiryMonth+"</td>";
					html += "<td id='ExpYear-"+i+"'>"+cards[i].ExpiryYear+"</td>";
					html += "<td id='CVV-"+i+"'>"+cards[i].CVV+"</td>";
					html += "<td id='Type-"+i+"' val='"+cards[i].Type+"'>"+Type+"</td>";
					html += "<td id='FName-"+i+"'>"+cards[i].FirstName+"</td>";
					html += "<td id='LName-"+i+"'>"+cards[i].LastName+"</td>";
					html += "<td id='Address-"+i+"' city='"+cards[i].City+"' state='"+cards[i].State+"' zip='"+cards[i].Zipcode+"'>"+cards[i].Address+"</td>";
					html += "<td id='Created-"+i+"'>"+cards[i].Created+"</td>";
					html += "<td id='Updated-"+i+"'>"+Updated+"</td>";
					html += "<td id='Recent-"+i+"'>"+Recent+"</td>";
					html += "<td id='Status-"+i+"'>"+Status+"</td>";
					html += "</tr>";
				}
				$(".Cards-Data").html(html);
				$("#saved-cards").modal();
			}
		});		
	}
	
	function ApplyCardDetails(i){
		$("#cc_fname").val($("#FName-"+i).html());
		$("#cc_lname").val($("#LName-"+i).html());
		$("#cc_number").val($("#Number-"+i).html());
		$("#cc_cvv2").val($("#CVV-"+i).html());
		$("#cc_month").val($("#ExpMonth-"+i).html());
		$("#cc_year").val($("#ExpYear-"+i).html());
		$(".cc_year").val($("#ExpYear-"+i).html());
		$("#cc_type").val($("#Type-"+i).attr("val"));
		$(".cc_type").val($("#Type-"+i).attr("val"));
		$("#cc_address").val($("#Address-"+i).html());
		$("#cc_city").val($("#Address-"+i).attr("city"));
		$("#cc_state").val($("#Address-"+i).attr("state"));
		$("#cc_zip").val($("#Address-"+i).attr("zip"));
		$("#saved-cards").modal();
	}
	
	function emailSelectedOrderFormNew() {
        form_id = $("#email_templates").val();
        if (form_id == "") {
			$engine.notify("Please choose email template");
        } else {
			Processing_show();
			$.ajax({
				type: "POST",
				url: BASE_PATH + "application/ajax/entities.php",
				dataType: "json",
				data: {
					action: "emailOrderNew",
					form_id: form_id,
					entity_id: <?=$this->entity->id?>
				},
				success: function (res) {
					if (res.success) {
						
						$("#maildivnew").modal();
						
						$('.add_one_more_field_').on('click',function(){
							$('#mailexttra').css('display','block');
							return false;
						});

						$('#singletop').on('click',function(){
							$('#mailexttra').css('display','none');
							$('.optionemailextra').val('');
						});

						$("#form_id").val(form_id);
						$("#mail_to_new").val(res.emailContent.to);
						$("#mail_subject_new").val(res.emailContent.subject);
						$("#mail_body_new").val(res.emailContent.body);
						$("#mail_att_new").html(res.emailContent.att);

						if(res.emailContent.atttype > 0){
							$("#attachPdf").attr('checked', 'checked');
						} else {
							$("#attachHtml").attr('checked', 'checked');
						}

					} else {
						$engine.notify("Can't send email. Try again later, please");
					}
				},
				complete: function (res) {
					KTApp.unblockPage();
				}
			});
        }
    }

 	function emailSelectedLeadFormNewsend() {

		var sEmail = [$('#mail_to_new').val(), $('.optionemailextra').val(), $('#mail_cc_new').val(), $('#mail_bcc_new').val()];

		if (validateEmail(sEmail) == false) {
		    $engine.notify('Invalid Email Address');
		    return false;
		}

		if ($('#attachPdf').is(':checked')) {
		    attach_type = $('#attachPdf').val();
		} else {
		    attach_type = $('#attachHtml').val();
		}

		$.ajax({
		    url: BASE_PATH + 'application/ajax/entities.php',
		    data: {
		        action: "emailQuoteNewSend",
		        form_id: $('#form_id').val(),
		        entity_id: $('#entity_id').val(),
		        mail_to: $('#mail_to_new').val(),
		        mail_cc: $('#mail_cc_new').val(),
		        mail_bcc: $('#mail_bcc_new').val(),
		        mail_extra: $('.optionemailextra').val(),
		        mail_subject: $('#mail_subject_new').val(),
		        mail_body: $('#mail_body_new').val(),
		        attach_type: attach_type
		    },
		    type: 'POST',
		    dataType: 'json',
		    beforeSend: function () {
				$("#maildivnew").find('.modal-body').addClass('kt-spinner kt-spinner--lg kt-spinner--dark');

		        if ($('#mail_to_new').val() == "" || $('#mail_subject_new').val() == "" || $('#mail_body_new').val() == "") {
		            $engine.notify('Empty Field(s)');
		            return false;
		        }
		    },
		    success: function (response) {
		        if (response.success == true) {
		            $("#maildivnew").modal('hide');
					clearMailForm();
		        }
		    },
		    complete: function () {
				$("#maildivnew").find('.modal-body').removeClass('kt-spinner kt-spinner--lg kt-spinner--dark');
				$("#maildivnew").modal('hide');
		    }
		});
	}

	function validateEmail(sEmail) {
        var res = "", res1 = "", i;
        var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
        
		for (i = 0; i < sEmail.length; i++) {
            if (filter.test(sEmail[i])) {
                res += sEmail[i];
            } else {
                res1 += sEmail[i];
            }
        }

        if (res1 !== '') {
            return false;
        }
    }

	$(document).ready(()=>{
		$('option[value="999"]').show();
		$('option[value="998"]').hide();

		$('#pt_internally').click(()=>{
			$('option[value="998"]').show();
			$('option[value="999"]').hide();
		});

		$('#pt_carrier').click(()=>{
			$('option[value="999"]').show();
			$('option[value="998"]').hide();
		});

		$("#cc_address").blur(()=>{
            autoComplete($("#cc_address").val(), 'cc')
        });

		$(document).click(()=>{
            $(".suggestions").html("");
        });
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

					if(type == 'cc'){
						h = document.getElementById("suggestions-box-cc");
						h.innerHTML = "";
						html += '<ul class="typeahead dropdown-menu" role="listbox" style="top: 36px; width:350px; left: 133px; display: block;">';
						html += '<li><a href="javascript:void(0)" style="height:25px !important; padding-top:0px !important; font-size:10px;">Suggestions</a></li>';
					}

					result.forEach( (element, index) => {

						let address = `<strong>${element.street}</strong>,<br>${element.city}, ${element.state} ${element.zip}`;
						
						html += `<li>
									<a class="dropdown-item" href="javascript:void(0)" onclick="applyAddressCC('${element.street}','${element.city}','${element.state}','${element.zip}')" role="option">
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

	function applyAddressCC (address, city, state, zip) {
		$("#suggestions-box").html("");
		$("#cc_address").val(address);
		$("#cc_city").val(city);
		$("#cc_state").val(state);
		$("#cc_zip").val(zip);
		document.getElementById("suggestions-box-cc").innerHTML = "";
	}

	$(document).ready(()=>{
        $(document).click(()=>{
            $(".suggestions").html("");
        });
    });
</script>