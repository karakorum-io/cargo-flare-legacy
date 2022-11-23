<script type="text/javascript">

var tempDisableBeforeUnload = false;
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

function applySearch(num) {
		var acc_obj = acc_data[num];
		switch (acc_type) {
			case <?=Account::TYPE_SHIPPER?>:
			
			    $('#select-shipper-block').hide();
			    $('#shipperDiv').show();
				$('#update_shipper_info').show();
		        $('#save_shipper_info').hide();
				
				$('#update_shipper').attr('checked','checked'); 
		        $('#save_shipper').removeAttr('checked');
				
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
				if (acc_obj.country == "US") {
					$("#shipper_state").val(acc_obj.state);
				} else {
					$("#shipper_state2").val(acc_obj.state);
				}
				$("#shipper_zip").val(acc_obj.zip);
				$("#shipper_type").val(acc_obj.shipper_type);
				$("#shipper_hours").val(acc_obj.hours_of_operation);
				if(acc_obj.referred_by !=''){
				   //$("#referred_by").val(acc_obj.referred_by);
				   //$('#referred_by').prop('disabled', true);
				            $("#referred_by").empty(); // remove old options
							$("#referred_by").append($("<option></option>")
									 .attr("value", acc_obj.referred_id).text(acc_obj.referred_by));
                  }
				  $("#account_payble_contact").val(acc_obj.account_payble_contact);
				  typeselected();
				break;
			case <?=Account::TYPE_TERMINAL?>:
				$("#"+acc_location+"_address1").val(acc_obj.address1);
				$("#"+acc_location+"_address2").val(acc_obj.address2);
				$("#"+acc_location+"_city").val(acc_obj.city);
				$("#"+acc_location+"_country").val(acc_obj.coutry);
				if (acc_obj.country == "US") {
					$("#"+acc_location+"_state").val(acc_obj.state);
				} else {
					$("#"+acc_location+"_state2").val(acc_obj.state);
				}
				$("#"+acc_location+"_zip").val(acc_obj.zip);
				$("#"+acc_location+"_contact_name").val(acc_obj.contact_name1);
				$("#"+acc_location+"_company_name").val(acc_obj.company_name);
				
				$("#"+acc_location+"_phone1").val(acc_obj.phone1);
				$("#"+acc_location+"_phone2").val(acc_obj.phone2);
				$("#"+acc_location+"_mobile").val(acc_obj.cell);
				$("#" + acc_location + "_type").val(acc_obj.location_type);
                $("#" + acc_location + "_hours").val(acc_obj.hours_of_operation);
				break;
		}
	}

	function createAndEmail() {
		$("#co_send_email").val("1");
		$("#submit_button").click();
	}

   function newShipper() {
	    $('#update_shipper_info').hide();
		$('#save_shipper_info').show();
		$('#select-shipper-block').hide();
		$('#shipperDiv').show();
		
		$('#save_shipper').attr('checked','checked'); 
		$('#update_shipper').removeAttr('checked');
		
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
	
	function origintypeselected() {
		if($("#origin_type").val() == "Commercial"){
	      $('#origin_company-span').show();
		  $('#origin_auction-span').show();
		}
		else
		{
		  $('#origin_company-span').hide();
		  $('#origin_auction-span').hide();
		}
		
	}
	
	$(document).ready(function(){
		typeselected();	
		origintypeselected();
							   
		$("#delivery_terminal_fee, #pickup_terminal_fee").change(function() {
			updatePricingInfo();
		});
		
		<?php if(empty($_POST)){?>
		    $('#shipperDiv').hide();
			
		<?php }?>
		
		$('#customer_balance_paid_by-block').hide();
		
		var createForm = $('#create_form');
		createForm.find("input.shipper_company-model").autocomplete({
			source: function(request, response) {
				$.ajax({
					url: '<?= SITE_IN ?>application/ajax/autocomplete.php',
					type: 'GET',
					dataType: 'json',
					data: {
						term: request.term,
						action: 'getCompanyData'
					},
					success: function(data) {
						response(data);
					}
				})
			},
			minLength: 0,
			autoFocus: true,
			select: function( event, ui ) {
				$( "#shipper_company" ).val( ui.item.company_name);
				$( "#shipper_company_id" ).val( ui.item.value );
				
				         if(Object.keys(ui.item).length > 0)
						 {
							if(ui.item.first_name!='N/A' && ui.item.first_name!='' && ui.item.first_name!=null)
							  $("#shipper_fname").val(ui.item.first_name);
							if(ui.item.last_name!='N/A' && ui.item.last_name!='' && ui.item.last_name!=null)  
				              $("#shipper_lname").val(ui.item.last_name);
							$("#shipper_email").val(ui.item.email);
							$("#shipper_phone1").val(ui.item.phone1);
							$("#shipper_phone2").val(ui.item.phone2);
							$("#shipper_mobile").val(ui.item.cell);
							$("#shipper_fax").val(ui.item.fax);
							$("#shipper_address1").val(ui.item.address1);
							$("#shipper_address2").val(ui.item.address2);
							$("#shipper_city").val(ui.item.city);
							$("#shipper_country").val(ui.item.country);
							//if (ui.item.country == "US") {
								$("#shipper_state").val(ui.item.state);
							//} else {
								//$("#shipper_state2").val(ui.item.state);
							//}
							$("#shipper_zip").val(ui.item.zip_code);
							$("#shipper_type").val(ui.item.shipper_type);
							if(ui.item.referred_id !=0 && ui.item.referred_by !='' && ui.item.referred_id !=null){
							  // $("#referred_by").val(ui.item.referred_id);
							   //$('#referred_by').prop('disabled', true);
							   
							    $("#referred_by").empty(); // remove old options
								$("#referred_by").append($("<option></option>")
									 .attr("value", ui.item.referred_id).text(ui.item.referred_by));
							  }
							  else
							  {
								  $("#referred_by").empty(); // remove old options
								  $("#referred_by").append($("<option></option>")
									               .attr("value", '').text('Select One'));
								  <?php 
								   foreach ($this->referrers_arr as $key=>$referrer) {
                                   ?>
												  $("#referred_by").append($("<option></option>")
									               .attr("value", '<?php print $key;?>').text('<?php print $referrer;?>'));
								<?php
                                     }
								  ?>
								  
							  }
							
						}
						else
						  {
							  $("#referred_by").empty(); // remove old options
									  $("#referred_by").append($("<option></option>")
													   .attr("value", '').text('Select One'));
									  <?php 
									   foreach ($this->referrers_arr as $key=>$referrer) {
									   ?>
													  $("#referred_by").append($("<option></option>")
													   .attr("value", '<?php print $key;?>').text('<?php print $referrer;?>'));
									<?php
										 }
									  ?>  
						  }
				return false;
			 },
			change: function() {
				/*
				//createForm.find("input.shipper_company-model").val('');
				var shipper_company = createForm.find("input.shipper_company-model").val();
				//alert(shipper_company);
				if(shipper_company !="")
				{
					$.ajax({
						url: '<?= SITE_IN ?>application/ajax/autocomplete.php',
						type: 'GET',
						dataType: 'json',
						data: {
							term: shipper_company,
							action: 'getCompanyValues'
						},
						success: function(data) {
							//response(data);
						 if(Object.keys(data).length > 0)
						 {
							if(data.first_name!='N/A' && data.first_name!='' && data.first_name!=null)
							  $("#shipper_fname").val(data.first_name);
							if(data.last_name!='N/A' && data.last_name!='' && data.last_name!=null)  
				              $("#shipper_lname").val(data.last_name);
							$("#shipper_email").val(data.email);
							$("#shipper_phone1").val(data.phone1);
							$("#shipper_phone2").val(data.phone2);
							$("#shipper_mobile").val(data.cell);
							$("#shipper_fax").val(data.fax);
							$("#shipper_address1").val(data.address1);
							$("#shipper_address2").val(data.address2);
							$("#shipper_city").val(data.city);
							$("#shipper_country").val(data.country);
							//if (data.country == "US") {
								$("#shipper_state").val(data.state);
							//} else {
								//$("#shipper_state2").val(data.state);
							//}
							$("#shipper_zip").val(data.zip_code);
							$("#shipper_type").val(data.shipper_type);
							if(data.referred_id !=0 && data.referred_by !='' && data.referred_id !=null){
							  // $("#referred_by").val(data.referred_id);
							   //$('#referred_by').prop('disabled', true);
							   
							    $("#referred_by").empty(); // remove old options
								$("#referred_by").append($("<option></option>")
									 .attr("value", data.referred_id).text(data.referred_by));
							  }
							  else
							  {
								  $("#referred_by").empty(); // remove old options
								  $("#referred_by").append($("<option></option>")
									               .attr("value", '').text('Select One'));
								  <?php 
								   foreach ($this->referrers_arr as $key=>$referrer) {
                                   ?>
												  $("#referred_by").append($("<option></option>")
									               .attr("value", '<?php print $key;?>').text('<?php print $referrer;?>'));
								<?php
                                     }
								  ?>
								  
							  }
							
						}
						else
						  {
							  $("#referred_by").empty(); // remove old options
									  $("#referred_by").append($("<option></option>")
													   .attr("value", '').text('Select One'));
									  <?php 
									   foreach ($this->referrers_arr as $key=>$referrer) {
									   ?>
													  $("#referred_by").append($("<option></option>")
													   .attr("value", '<?php print $key;?>').text('<?php print $referrer;?>'));
									<?php
										 }
									  ?>  
						  }
					  } //if data found
					 
					})
				}
				*/
			}
		});
		
		$('#acc_search_string').keydown(function(e) {
		   var key = e.which;
		   //alert(key);
			if (key == 13) {
			// As ASCII code for ENTER key is "13"
			   //$('#Search').submit(); // Submit form code
			   accountSearch();
			}
		});
		
	});
	
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

function selectPayment() {
	var customer_balance_paid_by = $("#customer_balance_paid_by").val();
	if(customer_balance_paid_by==3){
		$('#customer_balance_paid_by-block').show();
	}
	else
	{
	  $('#customer_balance_paid_by-block').hide();	
	}
	
}



    $(document).ready(function(){
        //$("#avail_pickup_date").datepicker({dateFormat: 'mm/dd/yy'});
		$("#avail_pickup_date").datepicker({
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

                            alert("Can't send email. Try again later, please");

                        }

                }
           });    

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
<?php include(ROOT_PATH.'application/templates/vehicles/create_js.php');?>
<?php include(ROOT_PATH.'application/templates/vehicles/form.php');?>
<div style="clear:both;"></div>
<br/>
<h3>Create Order</h3>
Complete the form below and click Save Order when finished. Required fields are marked with a <span style="color:red;">*</span><br/><br/>
<form action="<?= getLink('orders/create') ?>" method="post" onsubmit="javascript:tempDisableBeforeUnload = true;" id="create_form">

<div class="order-select-shipper" style="float:none;<?php if(!empty($_POST)){?>display:none;<?php }?>" id="select-shipper-block">
   <table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table order-edit" style="white-space:nowrap;">
			<tr>
				<td width="50%">
                  <table width="100%" cellpadding="1" cellspacing="1">
                   <tr>
                    <td align="center">
                     <img src="<?= SITE_IN ?>images/select-shipper.png" onclick="selectShipper();"  width="150" height="150"/>
                    </td>
                   </tr> 
                   <tr>
                    <td align="center">
                  <img src="<?= SITE_IN ?>images/select_shipper.png" onclick="selectShipper();" />
                    </td>
                   </tr> 
                  </table>  
                </td>
                <td style="border-left:1px solid #093;">
                   <table width="100%" cellpadding="1" cellspacing="1">
                   <tr>
                    <td align="center">
                     <img src="<?= SITE_IN ?>images/add-shipper.png"  onclick="newShipper();"  width="150" height="150"/>
                    </td>
                   </tr> 
                   <tr>
                    <td align="center">
                  <img src="<?= SITE_IN ?>images/new_shipper.png"  onclick="newShipper();"/>
                    </td>
                   </tr> 
                  </table>
                
                </td>
            </tr>
      </table>           
</div>    
<div class="order-info" style="float:none;" id="shipperDiv">
	<p class="block-title">Shipper Information</p>
	<div>
		<table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table order-edit" style="white-space:nowrap;">
			<tr>
				<td>
                
				<?php 
				    $checkedShipper = "";
				   if(!empty($_POST)){
					if($_POST['save_shipper']==1)
				     $checkedShipper = " checked=checked ";
				   }
				   //else
				     //$checkedShipper = " checked=checked ";
				?>
                <?php if(empty($_POST) || $_POST['save_shipper']==1){ ?>
                   <div id="save_shipper_info">
					<!--<input type="checkbox" name="save_shipper1" id="save_shipper1" value="1" <?php print $checkedShipper;?>/>-->
					<input type="hidden" name="save_shipper" id="save_shipper" value="1" />
					<label for="save_shipper">Create New Account</label>
                   </div>
                 <?php }?>  
				<?php 
				    $checkedShipper = "";
				   if(!empty($_POST)){
					if($_POST['update_shipper']==1)
				     $checkedShipper = " checked=checked ";
				   }
				   //else
				    // $checkedShipper = " checked=checked ";
				?>
                <?php if(empty($_POST) || $_POST['update_shipper']==1){ ?>
                 <div id="update_shipper_info">
					<!--<input type="checkbox" name="update_shipper1" id="update_shipper1" value="1" <?php print $checkedShipper;?>/>-->
					<input type="hidden" name="update_shipper" id="update_shipper" value="1" />
					<label for="save_shipper">Update Account Information</label>
                   </div>
                   <?php }?>  
				</td>
				<td>
					<?php // functionButton('Select Shipper', 'selectShipper()') ?>
				</td>
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
				<td>@shipper_company@
                <input type="hidden" name="shipper_company_id" id="shipper_company_id" />
                </td>
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
					<label for="save_location1">Save</label>
				</td>
				<td>
                 <table width="100%" cellpadding="1" cellspacing="1">
                  <tr>
                     <td><?= functionButton('Select Location', "selectTerminal('origin');") ?></td>
                     <td><b>OR</b>&nbsp;&nbsp;&nbsp;<span class="like-link" onclick="setLocationSameAsShipperOrder('origin')">same as shipper</span>&#8203;</td>
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
					<label for="save_location2">Save</label>
				</td>
				<td>
                    <table width="100%" cellpadding="1" cellspacing="1">
                      <tr>
                         <td><?= functionButton('Select Location', "selectTerminal('destination');") ?></td>
                         <td><b>OR</b>&nbsp;&nbsp;&nbsp;<span class="like-link" onclick="setLocationSameAsShipperOrder('destination')">same as shipper</span>&#8203;</td>
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
				<td rowspan="3" valign="top">
					<table cellspacing="0" cellpadding="0" border="0">
                       <tr>
							<td valign="top">@notes_from_shipper@<br/><i><strong>(Above notes will always appear on the dispatch sheet)</strong></i></td>
						</tr>
						<tr>
							<td valign="top">@notes_for_shipper@<br/><div style="float:left;"><div style="float:left;"><i><strong>(Maximum character allowed is <div id="charNum" style="float:right;">&nbsp;<font color="red">60</font> )</div></strong></i></div></td>
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
				<td valign="top">@shipping_ship_via@</td>
			</tr>
		</table>
	</div>
</div>
<br/>
<div class="order-info" style="float:none;">
	<p class="block-title">Vehicle Information</p>
	<div>
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="grid" style="white-space:nowrap;" id="vehicles-grid">
			<thead>
				<tr class="grid-head">
					<td class="grid-head-left">Year</td>
					<td>Make</td>
					<td>Model</td>
					<td>Type</td>
					<td>Vin #</td>
					<td>Lot #</td>
					<td>Inop</td>
					<td class="grid-head-right">Actions</td>
				</tr>
			</thead>
			<tbody>
			<?php if (isset($_POST['year'])) : ?>
			<?php foreach($_POST['year'] as $i => $year) : ?>
			<tr class="grid-body<?=($i%2)?' even':''?>" rel="<?= $i+1 ?>">
				<td class="grid-body-left"><input type="hidden" name="year[]" value="<?= $year ?>"/><?= $year ?></td>
				<td><input type="hidden" name="make[]" value="<?= $_POST['make'][$i] ?>"/><?= $_POST['make'][$i] ?></td>
				<td><input type="hidden" name="model[]" value="<?= $_POST['model'][$i] ?>"/><?= $_POST['model'][$i] ?></td>
				<td><input type="hidden" name="type[]" value="<?= $_POST['type'][$i] ?>"/><?= $_POST['type'][$i] ?></td>
				<td><input type="hidden" name="vin[]" value="<?= $_POST['vin'][$i] ?>"/><?= $_POST['vin'][$i] ?></td>
				<td><input type="hidden" name="lot[]" value="<?= $_POST['lot'][$i] ?>"/><?= $_POST['lot'][$i] ?></td>
				<td><input type="hidden" name="inop[]" value="<?= $_POST['inop'][$i] ?>"/><?= ($_POST['inop'][$i] == '1')?'Yes':'No' ?>
				
				<input type="hidden" name="color[]" value="<?= $_POST['color'][$i] ?>"/>
                <input type="hidden" name="plate[]" value="<?= $_POST['plate'][$i] ?>"/>
                <input type="hidden" name="state[]" value="<?= $_POST['state'][$i] ?>"/>
                <input type="hidden" name="carrier_pay[]" value="<?= $_POST['carrier_pay'][$i] ?>"/>
                <input type="hidden" name="tariff[]" value="<?= $_POST['tariff'][$i] ?>"/>
                <input type="hidden" name="deposit[]" value="<?= $_POST['deposit'][$i] ?>"/>
				</td>
				<td align="center" class="grid-body-right">
					<img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit" onclick="editVehicle(<?= $i+1 ?>)" class="action-icon"/>
					<img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete" onclick="deleteVehicle(<?= $i+1 ?>)" class="action-icon"/>
				</td>
			</tr>
			<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
		<br/>
		<!--div><?php //print functionButton('Add Vehicle', 'addVehicle()'); ?></div-->
		<table width="100%" cellpadding="1" cellspacing="1">
        <tr>
               <td width="5%" align="left"><div ><?= functionButton('Add Vehicle', 'addVehicle()') ?></div>
					
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
                            <td><span id="total_tariff">@total_tariff@</span>&nbsp;<span class="grey-comment">(Edit tariff under the "Vehicle Information" section)</span></td>
                            
                        </tr>
                        <tr>
                            <td>Required Deposit</td>
                            <td><span id="total_deposit">@total_deposit@</span>&nbsp;<span class="grey-comment">(Edit deposit under the "Vehicle Information" section)</span></td>
                        </tr>
                        <tr>
                            <td>Carrier Pay</td>
                            <td><span id="carrier_pay">@carrier_pay@</span>&nbsp;<span class="grey-comment">(Edit tariff and deposit under the "Vehicle Information" section)</span></td>
                        </tr>
                       
                        <tr>
                            <td>@pickup_terminal_fee@&nbsp;<span class="grey-comment">(Do not include fees paid directly from shipper to terminal)</span></td>
                        </tr>
                        <tr>
                            <td>@delivery_terminal_fee@&nbsp;<span class="grey-comment">(Do not include fees paid directly from shipper to terminal)</span></td>
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

<div class="order-info" style="float:none;" id="customer_balance_paid_by-block">
    <p class="block-title">Credit Card Information</p>
    <div>
        <table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table order-edit"
               style="white-space:nowrap;">
            <tr>
                <td colspan="2">
	                <table  width="100%" cellpadding="1" cellspacing="1">
		                <tr>
			                <td width="3%"><!--input type="checkbox" id="save_card" name="save_card" value="1"/--></td>
			                <td style="padding-bottom:5px"  width="3%"><label for="save_card"><!--Save--></label></td>
                            <td align="left" style="padding-left:5px" >
                              <span class="like-link" onclick="setLocationSameAsShipperOrder('e_cc')">same as shipper</span>&#8203;
                              </td>
                              <td align="left" style="padding-left:5px" >@auto_payment@</td>
                        </tr>
	                </table>
                </td>
                
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
            
        </table>
    </div>
</div>

<br />

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
<br /-->
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
<br />
<div style="float:right">
	<table cellpadding="0" cellspacing="0" border="0">
        <tr><td colspan="4">@match_carrier@</td></tr>
        <tr><td colspan="4">&nbsp;</td></tr>
		<tr>
			<td>
				<input type="hidden" name="send_email" value="0" id="co_send_email"/>
				<?= functionButton("Save &amp; Email", 'createAndEmail();'); ?>
			</td>
			<td style="padding-left: 15px;"><?= submitButtons(SITE_IN."application/orders", "Save") ?></td>
		</tr>
	</table>
</div>
</form>
<script>
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
			vehicles: []
		};
		$('input[name="type[]"]').each(function() {
			data.vehicles.push($(this).val());
		});
		if (data.vehicles.length == 0) {
			alert('No vehicles for quote');
			return;
		}
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
				var i = 0;
				var quoted = 0;
				$('input[name="carrier_pay[]"]').each(function() {
					if (parseFloat(res[i].carrier_pay) != 0) {
						$(this).val(res[i].carrier_pay);
						$(this).parent().next().find('input[name="deposit[]"]').val(res[i].deposit);
						quoted++;
					}
					i++;
					alert(quoted + ' vehicles quoted.');
				});
				updatePricingInfo();
			},
			error: function() {
				alert('Failed to calculate Quick Price');
			},
			complete: function() {
				// $("body").nimbleLoader("hide");
			}
		});
	}
</script>