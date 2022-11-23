<div id="checkdiv" style="width:100%;">

    <table cellspacing="2" cellpadding="0" border="0" width="100%">

        <tr>
          <td align="center">
             <b>Check# of file is</b>
          </td>      
        </tr>
        <tr>
          <td align="center">&nbsp;
             
          </td>      
        </tr>        <tr>
          <td align="center">
             <input type="text" style="font-size: 2.6em;font-weight: bold;border: none;width: 150px;" name="checkNumber" value="" id="checkNumber" />
             <input type="hidden" name="checkId" value="0" id="checkId" />
          </td>    
        </tr>
        
    </table>
</div>
<script type="text/javascript">
	function editPayment(payment_id) {
		//$('body').nimbleLoader('show');
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
					alert('Failed to load payment information');
				}
			},
			failed: function(res) {
				alert('Failed to load payment information');
			},
			complete: function(res) {
				////$//$('body').nimbleLoader('hide');;
			}
		});
	}

  function createBill() {
		//$('body').nimbleLoader('show');
		$.ajax({
			type: 'POST',
			url: BASE_PATH+'application/ajax/entities.php',
			dataType: 'json',
			data: {
				entity_id: <?=$this->entity->id?>,
				action: "createbill"
			},
			success: function(res) {
				if (res.success) {
					alert('Create Bill Request Success.');
				} else {
					alert('Create Bill Request Failed.');
				}
			},
			failed: function(res) {
				alert('Create Bill Request Failed.');
			},
			complete: function(res) {
				////$//$('body').nimbleLoader('hide');;
			}
		});
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

		//if (e_cc_fname == '') errors.push('First Name required.');
		//if (e_cc_lname == '') errors.push('Last Name required.');
		//if (e_cc_address == '') errors.push('Address required.');
		//if (e_cc_city == '') errors.push('City required.');
		//if (e_cc_state == '') errors.push('State required.');
		//if (e_cc_zip == '') errors.push('Zip Code required.');
		if (e_cc_number == '') errors.push('Number required.');
		//if (e_cc_cvv2 == '') errors.push('CVV required.');
		if (e_cc_type == '') errors.push('Type required.');
		if (e_cc_month == '') errors.push('Exp. Month required.');
		if (e_cc_year == '') errors.push('Exp. Year required.');

		if (errors.length > 0) {
			$(".entity-cc-info .error").html('<p>'+errors.join('</p><p>')+'</p>');
			$(".entity-cc-info .error").slideDown();
			return;
		}
		$(".entity-cc-info").nimbleLoader('show');
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
				$(".entity-cc-info").nimbleLoader('hide');
			}
		});
	}
    $(document).ready(function(){
		$(".entity-cc-info input, .entity-cc-info select,").focus(function() {
			$(".entity-cc-info .error").slideUp(500);
		});
    	function switch_pt(){
    		if ($("input:radio[name='payment_type_selector']:checked").val() == 'internally') {
                $("#table_gateway").hide();
				<?php if($this->is_carrier==1){ ?>
				  $("#table_terminal").hide();
				<?php }?>
				$("#table_carrier").hide();
                $("#table_internally").show();
            }
			
			else if($("input:radio[name='payment_type_selector']:checked").val() == 'carrier') 
			{
				$("#table_gateway").hide();
			    $("#table_internally").hide();
				<?php if($this->is_carrier==1){ ?>
				$("#table_terminal").hide();
				<?php }?>
				$("#table_carrier").show();
				
			}
			<?php if($this->is_carrier==1){ ?>
			else if($("input:radio[name='payment_type_selector']:checked").val() == 'terminal') 
			{
				$("#table_gateway").hide();
			    $("#table_internally").hide();
				$("#table_carrier").hide();
				$("#table_terminal").show();
			}
			<?php }?>
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
				  //alert('<?= $this->entity->getTotalDeposit() ?>');
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
			//alert($("#amount").val());
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
</script>
<script type="text/javascript">
	var busy = false;
	function updateInternalNotes(data) {
		var rows = "";
		for (i in data) {
			rows += '<tr class="grid-body"><td class="grid-body-left">'+data[i].created+'</td><td id="note_'+data[i].id+'_text">'+decodeURIComponent(data[i].text)+'</td><td>';
			rows += '<a href="mailto:'+data[i].email+'">'+data[i].email+'</a></td><td style="white-space: nowrap;" class="grid-body-right">';
			<?php if (!$this->entity->readonly) : ?>
			rows += '<img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit" width="16" height="16" class="action-icon edit-note" onclick="editInternalNote('+data[i].id+')"/>';
			rows += '<img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete" width="16" height="16" class="action-icon delete-note" onclick="delInternalNote('+data[i].id+')"/>';
			<?php else : ?>rows += '&nbsp;';<?php endif;?>
			rows += '</td></tr>';
		}
		$("#internal_notes_table tbody").html(rows);
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

$("#checkdiv").dialog({

	modal: true,
	width: 350,
	height: 170,
	title: "Check",
	hide: 'fade',
	resizable: false,
	draggable: false,
	autoOpen: false,
	buttons: {

		"Edit": function () {
			var checkId 	    = $("#checkId").val();
            var checkNumber 	= $("#checkNumber").val();
			
			if(checkNumber !='' && checkId >0)
		    {
			   //alert('Check edited: '+checkNumber);
			       //$('body').nimbleLoader('show');
					$.ajax({
						type: 'POST',
						url: BASE_PATH+'application/ajax/entities.php',
						dataType: 'json',
						data: {
							action: "update_check",
							checkId: checkId,
							checkNumber: checkNumber
							
						},
						success: function(res) {
							if (res.success) {
								alert('Check number changed.');
								document.location.reload();
							} else {
								alert('Failed to change Check number.');
							}
							
							
						},
						failed: function(res) {
							alert('Failed to change Check number.');
						},
						complete: function(res) {
							////$//$('body').nimbleLoader('hide');;
						}
					});
			}
			else
			  alert('Please fill check number.');

		},

		"Cancel": function () {
			$(this).dialog("close");
		}
	}
});	

function showCheck(check_id,check_number){
	
	$("#checkId").val(check_id);
	$("#checkNumber").val(check_number);
	$("#checkdiv").dialog("open");
	
}
</script>
<div id="note_edit_form" style="display:none;">
	<textarea style="width: 95%;height:100px;" class="form-box-textarea" name="note_text"></textarea>
</div>
<div style="padding-top:15px;">
<?php include('order_menu.php');  ?>
</div>
<br />
<h3>Order #<?= $this->entity->getNumber() ?> Payments</h3>
<p>Use "Record internally" option to record that a payment was received for this order for bookkeeping purposes.</p>
<p>Use "Process through gateway" option to send a given amount to Authorize.net, where you may enter the credit card information.</p>


<div class="order-info" style="width: 445px; margin-bottom: 10px;">
<?php
	$assigned = $this->entity->getAssigned();
	$shipper = $this->entity->getShipper();
	$origin = $this->entity->getOrigin();
	$destination = $this->entity->getDestination();
	$vehicles = $this->entity->getVehicles();
?>
	<p class="block-title">Order Information</p>
	<div>
		<table cellspacing="0" cellpadding="0" border="0" width="100%">
			<tr>
				<td style="vertical-align:top;" valign="top">
					<strong>Shipper Information</strong><br/>
					<strong>Name: </strong><?= $shipper->fname ?> <?= $shipper->lname ?><br/>
                    <strong>Company: </strong><b><?= $shipper->company; ?></b><br/>
				    <strong>Phone: </strong><?= $shipper->phone1; ?><br/>
                    <strong>Mobile: </strong><?= $shipper->mobile; ?><br/>
                    <strong>Fax: </strong><?= $shipper->fax; ?><br/>
					<a href="mailto:<?= $shipper->email ?>"><?= $shipper->email ?></a><br /><br/>
					
                    <strong>Assigned to: </strong><?= $assigned->contactname ?><br/>
                    <strong>Referred by: </strong><?= $this->entity->referred_by ?><br/>
				</td>
				<td style="vertical-align:top;">
					<strong>Route Information (<span class='red' onclick="mapIt(<?= $this->entity->id ?>);">Directions</span>)</strong><br/>
                       <strong>Origin: </strong> <span class="like-link"
                              onclick="window.open('<?= $origin->getLink() ?>', '_blank')"><?= $origin->getFormatted() ?></span> /<br/>
                        <strong>Destination: </strong><span class="like-link"
                              onclick="window.open('<?= $destination->getLink() ?>')"><?= $destination->getFormatted() ?></span><br/>
                        
                        <?php if (is_numeric($this->entity->distance) && ($this->entity->distance > 0)) : ?>
                           <strong>Mileage: </strong><?= number_format($this->entity->distance, 0, "", "") ?> mi
                                ($ <?= number_format(($this->entity->getCarrierPay(false) / $this->entity->distance), 2, ".", ",") ?>/mi)<br/>
                        <?php endif; ?>
                   
                    <br/>
					<strong>Vehicle Information </strong><br/>
					<?php if (count($vehicles) == 0) : ?>
					<?php elseif (count($vehicles) == 1) : ?>
						<?php $vehicle = $vehicles[0]; ?>
						
						<strong>Make/Model: </strong><?= $vehicle->year ?> <?= $vehicle->make ?> <?= $vehicle->model ?><?php if($vehicle->inop){?>(<span style="color:red;weight:bold;"><?= "Inop" ?></span>)<?php }?><br/>
                        <strong>VIN: </strong><?php  print $vehicle->vin ?><br/>
						<strong>Type: </strong><?= $vehicle->type ?><br/>
						<strong>Ship Via: </strong><span style="color:red;weight:bold;"><?= $this->entity->getShipVia() ?></span><br/>
					<?php else : ?>
						<span class="like-link multi-vehicles">Multiple Vehicles</span>
						<div class="vehicles-info">
                        <table width="100%"   cellpadding="0" cellspacing="1">
                         <tr>
                             <td  style="padding:3px;"><b><p>Year</p></b></td>
                             <td  style="padding:3px;"><b><p><?= Make ?></p></b></td>
							 <td  style="padding:3px;"><b><p><?= Model ?></p></b></td>
                             <td  style="padding:3px;"><b><p><?= Type ?></p></b></td> 
							 <td  style="padding:3px;"><b><p><?= Vin# ?></p></b></td>
							 <td  style="padding:3px;"><b><p><?= Lot# ?></p></b></td>
						  </tr>
						<?php foreach($vehicles as $vehicle) : ?>
                          <tr>
                             <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->year ?></td>
                             <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->make ?></td>
							 <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->model ?></td> 
                             <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->type ?></td>
                             <td bgcolor="#ffffff" style="padding:3px;"> <?php  print $vehicle->vin ?></td>
							 <td bgcolor="#ffffff" style="padding:3px;"> <?php  print $vehicle->lot ?></td>
                           </tr>
						<?php endforeach; ?>
                        </table>
						</div>
						<br/>
					<?php endif; ?>
				</td>
			</tr>
            
		</table>
	</div>
</div>
<?php
$isColor = $this->entity->isPaidOffColor();
       $Dcolor = "black";
	   $Ccolor = "black";
	   $Tcolor = "black";
	   
	   if($isColor['carrier']==1)
		$Ccolor = "green";
	   elseif($isColor['carrier']==2)	
		$Ccolor = "red";
	   
	   if($isColor['deposit']==1)
		$Dcolor = "green";	
	   elseif($isColor['deposit']==2)	
		$Dcolor = "red";
		
	   if($isColor['total']==1)
		$Tcolor = "green";	
	   elseif($isColor['total']==2)	
		$Tcolor = "red";
?>		
<div class="quote-info" style="width: 445px;float:left;margin-left:10px;">
	<p class="block-title">Payment Terms</p>
	<div>
		<img style="vertical-align: middle" src="<?= SITE_IN ?>images/icons/dollar.png" alt="Total Tariff" title="Total Tariff" width="16" height="16"/> <strong>Total Tariff amount: </strong><span class='<?= $Tcolor;?>'><?= $this->entity->getTotalTariff() ?></span><br />
		<img style="vertical-align: middle" src="<?= SITE_IN ?>images/icons/truck.png" alt="Tariff to Shipper" title="Tariff to Shipper" width="16" height="16"/> <strong>To Carrier: </strong><span class='<?= $Ccolor;?>'><?= $this->entity->getCarrierPay() ?></span><br />
		<img style="vertical-align: middle" src="<?= SITE_IN ?>images/icons/person.png" alt="Tariff by Customer" title="Tariff by Customer" width="16" height="16"/> <strong>Deposit amount: </strong><span class='<?= $Dcolor;?>'><?= $this->entity->getTotalDeposit() ?></span><br />
         <?php 
		 $payments_terms = $this->entity->payments_terms;
		 if(in_array($this->entity->balance_paid_by, array(2, 3 , 16 , 17 , 8,9,18,19))){   
			$payments_terms = "COD / COP";
		 }
		if($payments_terms!=""){?>
        <b>Payment Terms:</b> <?php print $payments_terms;?>
       <?php } ?>
	</div>
</div>
<div style="clear:left;"></div>


<p>The payment will appear in Freight Dragon once it's processed by the gateway.</p>
<table cellpadding="0" cellspacing="0" border="0" class="form-table">
    <tr>
        <td>@payment_type_selector@</td>
    </tr>
</table>
<br />
<div class="order-info" style="float:none;">
    <p class="block-title">Payment</p>
    <div>
    	<div class="msg-error" style="display: none;" id="payment_form_errors">
    		<ul class="msg-list"></ul>
		</div>
    	<form action="<?= getLink('orders/payments/id/'.$_GET['id']) ?>" method="post" id="internally_form">
    	<input type="hidden" name="payment_type" value="internally"/>
		<input type="hidden" name="payment_id" disabled="disabled"/>
        <table cellpadding="0" cellspacing="0" border="0" width="100%" class="form-table" id="table_internally">
            <tr>
                <td>@date_received@</td>
                <td>@method@</td>
            </tr>
            <tr>
                <td><label for="from_to"><span class="required">*</span>Payment From/To:</label></td>
                <td><!--@from_to_carrier@-->
                <select name="from_to" class="form-box-combobox" id="from_to"><option value="" selected="selected">Select One</option><option value="2-1" selected="selected">Shipper to Company</option><option value="1-2">Company to Shipper</option><option value="3-1">Carrier to Company</option></select>
                </td>
                <td width="112">@transaction_id@</td>
            </tr>
            <!--tr>
                <td>@from_to@</td>
                <td width="112">@transaction_id@</td>
            </tr-->
            <tr>
                <td valign="top">@amount@</td>
                <td valign="top" rowspan="2" colspan="2">
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
                </td>
            </tr>
            <!--tr>
                <td valign="top">@notes@</td>
            </tr-->
            <tr>
                <td colspan="2">
                    <?= submitButtons('', 'Record Payment') ?>
                </td>
				 <td colspan="2"></td>
            </tr>
        </table>
        </form>
        
        <form action="<?= getLink('orders/payments/id/'.$_GET['id']) ?>" method="post" id="carrier_form">
    	<input type="hidden" name="payment_type" value="carrier"/>
		<input type="hidden" name="payment_id" disabled="disabled"/>
        <table cellpadding="0" cellspacing="0" border="0" width="100%" class="form-table" id="table_carrier">
            <tr>
                <td>@date_received_carrier@</td>
                <td>@method@</td>
            </tr>
            <tr>
                <td><label for="from_to"><span class="required">*</span>Payment From/To:</label></td>
                <td><!--@from_to_carrier@-->
                <select name="from_to_carrier" class="form-box-combobox" id="from_to_carrier"><option value="" selected="selected">Select One</option><option value="1-3" selected="selected">Company to Carrier</option><option value="2-3">Shipper to Carrier</option></select>
                </td>
                <td width="112">@transaction_id@</td>
            </tr>
            <tr>
                <td valign="top">@amount_carrier@</td>
                <td valign="top" rowspan="2" colspan="2">
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
                </td>
            </tr>
            <!--tr>
                <td valign="top">@notes@</td>
            </tr-->
            <tr>
                <td colspan="4">
                    <?= submitButtons('', 'Record Payment') ?>
                </td>
                <td >
                    <?= functionButton('Printing Check', 'printCheckForm(\''.md5($_GET['id']).'\')') //makeNewWindow?><br />
                    <?= functionButton('Create Bill', 'createBill()') //makeNewWindow?>
                </td>
            </tr>
            <tr>
                <td align="left" colspan="8">
                   Once the payment has been recorded FreightDragon will notify the carrier via email on file &nbsp;&nbsp;<input type="checkbox" name="send_carrier_mail" id="send_carrier_mail" value="1" checked="checked"/>
                </td>
            </tr>
        </table>
        </form>
        
        <?php if($this->is_carrier==1){ ?>
        <form action="<?= getLink('orders/payments/id/'.$_GET['id']) ?>" method="post" id="terminal_form">
    	<input type="hidden" name="payment_type" value="terminal"/>
		<input type="hidden" name="payment_id" disabled="disabled"/>
        <table cellpadding="0" cellspacing="0" border="0" width="100%" class="form-table" id="table_terminal">
            <tr>
                <td>@date_received_terminal@</td>
                <td>@method@</td>
            </tr>
            <tr>
                <td>@from_to_terminal@</td>
                <td width="112">@transaction_id@</td>
            </tr>
            <tr>
                <td valign="top">@amount_terminal@</td>
                <td valign="top" rowspan="2" colspan="2">
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
                </td>
            </tr>
            <!--tr>
                <td valign="top">@notes@</td>
            </tr-->
            <tr>
                <td colspan="4">
                    <?= submitButtons('', 'Record Payment') ?>
                </td>
                
            </tr>
        </table>
        </form>
        <?php }?>
        
        <form action="<?= getLink('orders/payments/id/'.$_GET['id']) ?>" method="post" id="gateway_form">
    	<input type="hidden" name="payment_type" value="gateway"/>
	    	<div id="table_gateway" style="display: none;">

	    		<table cellpadding="0" cellspacing="0" border="0">
		        	<tr>
		        		<td valign="top">
		        			<table cellpadding="0" cellspacing="0" border="0" class="form-table">
					        	<tr>
					        		<td colspan = "2" valign="top"><span class="required">*</span>Amount</td>
								</tr>
					                @gw_pt_type@
					            <tr>
					                <td colspan="2">
					                    <?=submitButtons('', 'Submit') ?>
					                </td>
					            </tr>
					        </table>
		        		</td>
		        		<td valign="top" style="padding-left:30px;">
		        			<table cellspacing="2" cellpadding="0" border="0">
						        <tr><td>@cc_fname@</td></tr>
						        <tr><td>@cc_lname@</td></tr>
						        <tr><td>@cc_type@</td></tr>
						        <tr><td>@cc_number@</td></tr>
						        <tr><td>@cc_cvv2@ <img src="<?=SITE_IN?>images/icons/cards.gif" alt="Card Types" width="129" height="16" style="vertical-align:middle;" /></td></tr>
						        <tr><td>@cc_month@ / @cc_year@</td></tr>
								<tr><td>@cc_address@</td></tr>
						        <tr><td>@cc_city@</td></tr>
						        <tr><td>@cc_state@</td></tr>
						        <tr><td>@cc_zip@</td></tr>
						    </table>
		        		</td>
		        	</tr>
		        </table>
			</div>
        </form>
    </div>
</div>
<br />
<table cellpadding="0" cellspacing="0" border="0" width="100%">
    <tr>
        <td width="49%" valign="top">
			<h3>Balances</h3>
			<p>The balance of this order is to be paid by: <strong><?=Entity::$balance_paid_by_string[$this->entity->balance_paid_by]?></strong></p>
            <table cellpadding="0" cellspacing="0" border="0" width="100%" class="grid">
                <tr class="grid-head">
                    <td class="grid-head-left">We owe them</td>
                    <td class="grid-head-right" width="120">Balance</td>
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
			<table cellpadding="0" cellspacing="0" border="0" width="100%" class="grid">
				<tr class="grid-head">
					<td class="grid-head-left">They owe us</td>
					<td class="grid-head-right" width="120">Balance</td>
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
        </td>
        <td>&nbsp;</td>
        <td width="49%" valign="top" class="entity-cc-info">
			<h3>Credit Card Information</h3>
			<p>Only company administrator can see full Credit Card number</p>
			<div class="success" style="display: none"><strong>Information saved</strong></div>
			<div class="error" style="display: none"></div>
			<table cellspacing="2" cellpadding="0" border="0">
				<tr><td>@e_cc_fname@</td></tr>
				<tr><td>@e_cc_lname@</td></tr>
				<tr><td>@e_cc_type@</td></tr>
				<tr><td>@e_cc_number@</td></tr>
				<tr><td>@e_cc_cvv2@ <img src="<?=SITE_IN?>images/icons/cards.gif" alt="Card Types" width="129" height="16" style="vertical-align:middle;" /></td></tr>
				<tr><td>@e_cc_month@ / @e_cc_year@</td></tr>
				<tr><td>@e_cc_address@</td></tr>
				<tr><td>@e_cc_city@</td></tr>
				<tr><td>@e_cc_state@</td></tr>
				<tr><td>@e_cc_zip@</td></tr>
				<tr><td colspan="2" align="center"><?=functionButton('Save Credit Card Information', 'saveEntityCC()')?></td></tr>
			</table>
        </td>
    </tr>
</table>
<br />
<h3>Payments Made and/or Received</h3>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="grid">
    <tr class="grid-head">
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
        <td class="grid-body-left grid-body-right" align="center" colspan="8"><i>No Records</i></td>
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
@pager@

<br />
<h3>Print Check History</h3>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="grid">
    <tr class="grid-head">
        <td class="grid-head-left">#</td>
        <td width="70">Created</td>
        <td>From =&gt; To</td>
        <td>Amount</td>
        <td>Method</td>
        <td class="grid-head-right">Entered By</td>
        <!--td class="grid-head-right" width="80" colspan="2">Actions</td-->
    </tr>
    <?php  if (count($this->checks) == 0) : ?>
    <tr class="grid-body">
        <td class="grid-body-left grid-body-right" align="center" colspan="8"><i>No Records</i></td>
    </tr>
    <?php endif; ?>
    <?php  
	//print "<pre>";
	//print_r($this->checks);
	
	foreach ($this->checks as $check) : 
	        $entered_name = '';
	    	if ($check['entered_by'] == 0 || $check['entered_contactname'] == '')
				$entered_name = 'System';
		    else
			    $entered_name = $check['entered_contactname'];
			
	
	?>
   	<tr class="grid-body" id="row-<?=$check['id']?>">
        <td class="grid-body-left"><?=$check['id']?></td>
        <td><?= date("m/d/Y", strtotime($check['created']))?></td>
        <td>Company =&gt; Carrier</td>
        <td><?= "$ " . number_format($check['amount'], 2, '.', ','); ?></td>
        <td>
			Print check
           
            
            <span class="like-link" onclick="showCheck(<?=$check['id']?>,'<?= $check['check_number'] ?>');">[Preview]</span>
			
		</td>
        
        <td><?= $entered_name ?></td>
        <!--td style="width: 16px;"><?=editIcon('javascript:editCheck('.$payment->id.')')?></td>
		<td style="width: 16px;" class="grid-body-right"><?=deleteIcon(getLink('ajax', 'checks.php?action=delete&id='.$payment->id), "row-".$payment->id)?></td-->
    </tr>
    <?php endforeach; ?>
</table>
<br />

<table cellpadding="0" cellspacing="0" border="0" width="100%" >
<tr>
 <td>
<?php $notes = $this->notes; ?>
<div class="order-info" style="width: 950px;float: right;margin-top: 10px;">
	<p class="block-title">Internal Notes</p>
	<div>
	<?php //if ($this->entity->status != Entity::STATUS_ARCHIVED) : ?>
       <table cellpadding="0" cellspacing="0" border="0" width="100%" >
              <tr>
               <td align="center">
                 <textarea class="form-box-textarea" style="width: 460px; height: 50px;" maxlength="1000" id="internal_note"></textarea>
               </td>
             </tr>
             <tr>
               <td  align="center">
                 <?= functionButton('Add Note', 'addInternalNote()') ?>
               </td>
             </tr>
         </table>
		
		
		<div style="clear:both;"><br/></div>
	<?php //endif; ?>
		<table cellspacing="0" cellpadding="0" width="100%" border="0" class="grid" id="internal_notes_table">
			<thead>
			<tr class="grid-head">
				<td class="grid-head-left"><?=$this->order->getTitle('created', 'Date')?></td>
				<td>Note</td>
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
				
                <td id="note_<?= $note->id ?>_text" <?php if($note->priority==2){?>style="color:#FF0000"<?php }?>><?php if($note->system_admin == 1 || $note->system_admin == 2){?><b><?= $note->getText() ?></b><?php }elseif($note->priority==2){?><b style="font-size:12px;"><?= $note->getText() ?></b><?php }else{?><?= $note->getText() ?><?php }?></td>
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
</td>
</tr>
</table>