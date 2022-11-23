<script language="javascript" type="text/javascript">
	
	$(document).ready(function(){
			
	$('#add_order').keypress(function(e) {
								  
    if(e.which == 13) {
        //alert('You pressed enter!');
		var textOrder = $("#add_order").val();
        if(textOrder!="")
		  addSearchOrder(); 
		  return false;
    }
});		
		var createForm = $('#create_form');
		createForm.find("input.shipper_company-model").typeahead({
			source: function(request, result) {
				$.ajax({
					url: '<?= SITE_IN ?>application/ajax/autocomplete.php',
					type: 'GET',
					dataType: 'json',
					data: {
						term: request.term,
						action: 'getCompanyDataBatchPayment'
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
				$( "#shipper_company" ).val( ui.item.company_name);
				$( "#shipper_company_id" ).val( ui.item.value );
				
				         
				return false;
			 },
			change: function() {
			
			}
		});
		
	});
	
function addSearchOrder() {
	var textOrder = $("#add_order").val();
	var textOld = $("#batch_order_ids").val();
	
	if(textOrder !=''){
	  if(textOld == ''){
	     textOld = textOrder;
		 
	  }
	  else{
	     textOld += ','+textOrder;
		 
	  }
	  
	  $('#orders_list').append('<option value="'+textOrder+'" selected>'+textOrder+'</option>');
	$("#batch_order_ids").val(textOld);
	$("#add_order").val('');
	}
	   
}
/*
function clear_batch_value()
{
		  
	$("#shipper_company").val('');
	$("#shipper_company_id").val('');
	$("#orders_list option:selected").remove();
	$("#batch_order_ids").val('');
}*/

function clear_batch_value()
{
		  
	$("#shipper_company").val('');
	$("#shipper_company_id").val('');
	//$("#orders_list option:selected").remove();
	
	$("#orders_list option:selected").each(function () {
	   var $this = $(this);
	   if ($this.length) {
		  var selText = $this.text();
		  //console.log(selText+'----'+$("#batch_order_ids").val()+'=='+$("#batch_order_ids").val().indexOf(','+selText));
		  if($("#batch_order_ids").val().indexOf(','+selText) !=-1)
		   $("#batch_order_ids").val( $("#batch_order_ids").val().replace(','+selText, "") );  
		  else if($("#batch_order_ids").val().indexOf(selText) !=-1){
		    $("#batch_order_ids").val( $("#batch_order_ids").val().replace(selText+',', "") );
			$("#batch_order_ids").val( $("#batch_order_ids").val().replace(selText, "") );
		  }
		//console.log(selText+'----'+$("#batch_order_ids").val());	
			$this.remove();
	   }
	});
	$('#orders_list option').prop('selected', true);
	//$("#orders_list option:selected").remove();
	//$("#batch_order_ids").val('');
}

function redirect_to_func()
{
	document.location.href = "/application/orders/batchnew";
}		
</script>
<style>
div.form-box-buttons1 input{
  width: 180px;
  height: 23px;
  color: #fff;
  background-color:#06F;
  border: 0;
  font-size: 11px;
  font-family: Arial, Helvetica, sans-serif;
  cursor: pointer;
  font-weight: normal;
}
</style>
<br /><br />
<? include(TPL_PATH."accounts/accounts/menu.php"); ?>


	<div class="kt-portlet">
		
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">Search Account</h3>
			</div>
		</div>
		
		<div class="kt-portlet__body">
			<form method="post"  action="<?php print SITE_IN."application/accounts/advancedDuplicateAccountsSubmit";?>" name="create_form" id="create_form">
			
				<input type="hidden" name="accountType" id="accountType" value="<?= $this->accountType;?>"/>
				
				<div class="row">
				
					<div class="col-12">
						<div class="form-group">
							<label><span class="required">*</span> Company:</label>
							<input type="text" id="add_order" maxlength="100" name="add_order" tabindex="41" class="zip form-box-textfield" style="margin-left:5px;width:calc(550px - 30px);" kl_virtual_keyboard_secure_input="on">
							<img src="<?= SITE_IN ?>images/icons/add.gif"  width="20" height="20" onclick="addSearchOrder();"/>
						</div>
						<div class="form-group">
							<select name='orders_list[]' id="orders_list" size=7 multiple style="width:100%;">
							</select>
							<input type="hidden" name="batch_order_ids" id="batch_order_ids" value=""/>
						</div>
					</div>
					
					<div class="col-12 text-right">
						<?php print submitButtons(SITE_IN."application/accounts/advancedDuplicateAccountsSubmit", "Locate Accounts") ?>
						<br/>
						<input type="hidden" name="submit" value="Start Processing" />
						<span id="submit_button-submit-btn" style="-webkit-user-select:none;float:right">
							<input class="btn btn-sm btn_dark_blue" type="button" id="clear_batch" value="Clear" onclick="clear_batch_value();" style="-webkit-user-select: none;" />
						</span>
					</div>
					
				</div>
			
			</form>
		</div>
		
	</div>
	