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
<script language="javascript" type="text/javascript">
 function process_payment(id) {
		
		var entity_id = id;
		
        $("#payment_dialog .msg-error").hide();
		$("#payment_form").each(function(){
            this.reset();
        });
        $("body").nimbleLoader('show');
        
		
		$.ajax({
            type: 'POST',
            url: '<?=SITE_IN;?>application/ajax/entities.php',
            dataType: 'json',
            data: {
                action: 'getPaymentData',
                entity_id: entity_id
            },
            complete: function(response) {
                $("body").nimbleLoader('hide');
            },
            success: function(response) {
                if (response.success) {
					
					$("#gw_pt_type_dvalue").html(response.data.depositRemains);   
                    //$("#gw_pt_type_bvalue").html(response.data.shipperRemains);
					//alert(response.data.type);
					if(response.data.type == 1 || response.data.type ==3){
					 $("#gw_pt_type_bvalue").html(response.data.depositRemains);
					 $("#tariff_pay").val(response.data.depositRemains);
					 
						 //$("#amount").val(response.data.shipperRemains);
						$("#amount").val(response.data.depositRemains);
						//$("#amount_carrier").val(response.data.depositRemains);
						
						
						$("#amount_terminal").val(response.data.depositRemains);
					 
					}
					else {
                     $("#gw_pt_type_bvalue").html(response.data.shipperRemains);
					 $("#tariff_pay").val(response.data.shipperRemains);
					 
						 //$("#amount").val(response.data.shipperRemains);
						$("#amount").val(response.data.shipperRemains);
						//$("#amount_carrier").val(response.data.depositRemains);
						//$("#amount_carrier").val(response.data.carrierRemains);
						
						$("#amount_terminal").val(response.data.shipperRemains);
					
					}
					$("#amount_carrier").val(response.data.carrierRemains);
					$("#descBlocks").html(response.data.blocks);
					//alert(response.data.carrierRemains);
					
					$("#deposit_pay").val(response.data.depositRemains);   
                   // $("#tariff_pay").val(response.data.shipperRemains);
					
					
					$("#cc_fname").val(response.data.payments.cc_fname);   
                    $("#cc_lname").val(response.data.payments.cc_lname);
					$("#cc_type_1").val(response.data.payments.cc_type);   
                    $("#cc_number").val(response.data.payments.cc_number);
					$("#cc_cvv2").val(response.data.payments.cc_cvv2);   
                    $("#cc_month").val(response.data.payments.cc_month);
					$("#cc_year").val(response.data.payments.cc_year);   
                    $("#cc_address").val(response.data.payments.cc_address);
					$("#cc_city").val(response.data.payments.cc_city);
					$("#cc_state").val(response.data.payments.cc_state);
					$("#cc_zip").val(response.data.payments.cc_zip);
					
					
					
                    $("#payment_dialog").dialog({
                        title: 'Payment Form',
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
                            text: 'Payment',
                            click: function(){
								var payment_type_selector =  $("input:radio[name='payment_type_selector']:checked").val();
								         
								
								var dispatchValues = "";
                                if(payment_type_selector=="internally")
				                  dispatchValues = $("#internally_form").serializeArray();
								else if(payment_type_selector=="carrier")
				                  dispatchValues = $("#carrier_form").serializeArray();
								else if(payment_type_selector=="terminal")
				                  dispatchValues = $("#terminal_form").serializeArray();
								else if(payment_type_selector=="gateway")
				                  dispatchValues = $("#payment_form").serializeArray();  
								  
                                dispatchValues.push({'name': 'action', 'value': 'payment'});
                                dispatchValues.push({'name': 'entity_id', 'value': entity_id});
								
				
                                $(".dispatch_form_dialog").nimbleLoader('show');
                                $.ajax({
                                    type: 'POST',
                                    url: '<?=SITE_IN?>application/ajax/entities.php',
                                    dataType: 'json',
                                    data: dispatchValues,
                                    success: function(response) {
                                        if (response.success) {
											alert(response.data.success);
                                            document.location.reload();
										   //alert('done');
                                        } else {
                                            if (response.errors != undefined) {
                                                $("#payment_dialog .msg-list").html('');
												
                                                for(i in response.errors) {
                                                    $("#payment_dialog .msg-list").append('<li>'+response.errors[i]+'</li>');
													//alert(response.errors[i]);
                                                }
                                                $("#payment_dialog .msg-error").show();
                                                //$("body").scrollTop(0);
                                            } else {
                                                alert("Payment not done. Try again later, please");
                                            }
                                        }
                                    },
                                    error: function(response) {
                                        alert("Payment not done. Try again later, please");
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
				$("#li_ch_numb").show();
            }
			
			else if($("input:radio[name='payment_type_selector']:checked").val() == 'carrier') 
			{
				$("#table_gateway").hide();
			    $("#table_internally").hide();
				<?php if($this->is_carrier==1){ ?>
				$("#table_terminal").hide();
				<?php }?>
				$("#table_carrier").show();
				$("#li_ch_numb_carrier").show();
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
				$('#gw_pt_type_balance').attr('checked',true);
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
			alert($("#amount").val());
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


<div id="payment_dialog" style="display: none; z-index:-1;">
<div id="descBlocks"></div>
<div style="clear:left;"></div>
<div style="clear:left;"></div>
    <div class="msg-error" onclick="$('#payment_dialog .msg-error').hide();" style="display: none"><ul class="msg-list"></ul></div>
    
    <table cellpadding="0" cellspacing="0" border="0" class="form-table">
    <tr>
        <td>@payment_type_selector@</td>
    </tr>
</table>
<br />
<form action="<?= getLink('orders/payments/id/'.$_GET['id']) ?>" method="post" id="internally_form">
    	<input type="hidden" name="payment_type" value="internally"/>
		<input type="hidden" name="payment_id" disabled="disabled"/>
        <table cellpadding="0" cellspacing="0" border="0" width="100%" class="form-table" id="table_internally">
            <tr>
                <td>@date_received@</td>
                <td>@method@</td>
            </tr>
            <tr>
                <!--td>@from_to@</td-->
                <td><label for="from_to"><span class="required">*</span>Payment From/To:</label></td>
                <td>
                <select name="from_to" class="form-box-combobox" id="from_to"><option value="" selected="selected">Select One</option><option value="2-1" selected="selected">Shipper to Company</option><option value="1-2">Company to Shipper</option><option value="3-1">Carrier to Company</option></select>
                </td>
                <!--td width="112">@transaction_id@</td-->
                
                <td  colspan="2"><input name="transaction_id" type="hidden" maxlength="32" class="form-box-textfield" id="transaction_id">
                   <ol class="payment_options">
                        <li id="li_ch_numb">
                            <label for="ch_numb" style="width:80px;">Check Number:</label>
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
            <tr>
                <td valign="top">@amount@</td>
                <td valign="top" rowspan="2" colspan="2">
                    
                </td>
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
                <!--td>@from_to_carrier@</td-->
                <td><label for="from_to"><span class="required">*</span>Payment From/To:</label></td>
                <td>
                <select name="from_to_carrier" class="form-box-combobox" id="from_to_carrier"><option value="" selected="selected">Select One</option><option value="1-3" selected="selected">Company to Carrier</option><option value="2-3">Shipper to Carrier</option></select>
                </td>
                <td  colspan="2"><!--@transaction_id@-->
                   <input name="transaction_id" type="hidden" maxlength="32" class="form-box-textfield" id="transaction_id">
                   <ol class="payment_options_carrier">
                        <li id="li_ch_numb">
                            <label for="ch_numb">Check Number:</label>
                            <input type="text" class="form-box-textfield" id="ch_numb" name="ch_number"/>
                        </li>
                      
                    </ol>
                </td>
            </tr>
            <tr>
                <td valign="top">@amount_carrier@</td>
                <td valign="top" rowspan="2" colspan="2">
                    
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
           
        </table>
        </form>
        <?php }?>
    <form action="<?= getLink('orders/payments/id/'.$_GET['id']) ?>" method="post" id="payment_form">
    	<input type="hidden" name="payment_type" value="gateway"/>
	    	<div id="table_gateway" >

	    		<table cellpadding="0" cellspacing="0" border="0">
		        	<tr>
		        		<td valign="top">
		        			<table cellpadding="0" cellspacing="0" border="0" class="form-table">
					        	<tr>
					        		<td colspan = "2" valign="top"><span class="required">*</span>Amount</td>
								</tr>
					                @gw_pt_type@
					            
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