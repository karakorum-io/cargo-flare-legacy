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
		createForm.find("input.shipper_company-model").autocomplete({
			source: function(request, response) {
				$.ajax({
					url: '<?= SITE_IN ?>application/ajax/autocomplete.php',
					type: 'GET',
					dataType: 'json',
					data: {
						term: request.term,
						action: 'getCompanyDataBatchPayment'
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
function clear_batch_value()
{
		  
	$("#shipper_company").val('');
	$("#shipper_company_id").val('');
	$("#orders_list option:selected").remove();
	$("#batch_order_ids").val('');
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

<div class="row" id="reassignCompanyDiv">
	<div class="col-12 text-right">
		<div>
			<span id="submit1_button-submit-btn" style="-webkit-user-select: none;text-align:right !important;">
				<input class="btn btn-sm btn-brand" type="button" id="redirect_to" value="Go To Carrier Payment" onclick="redirect_to_func();" style="-webkit-user-select: none;">
			</span>
		</div>
	</div>
</div>

<div class="kt-portlet">
	<div class="kt-portlet__head ">
		<div class="kt-portlet__head-label">
			<h3 class="kt-portlet__head-title">Shipper Batch Payment Processing</h3>
			
		</div>
	</div>
	
	<div class="kt-portlet__body--fit">
		<div class="kt-widget14">
			<form method="post" action="<?php print SITE_IN."application/orders/batchsubmit";?>" name="create_form" id="create_form">
				<div class="row">
				
					<div class="col-5">
						<div class="form-group">
						
							<label for="add_order"><span class="required">*</span> Order ID :</label>
							<div class="input-group">
								<input type="text" id="add_order" maxlength="10" name="add_order" tabindex="41" class="zip form-control" kl_virtual_keyboard_secure_input="on" aria-describedby="basic-addon2"/>
								<div class="input-group-append" style="cursor:pointer;" onclick="addSearchOrder();" >
									<span class="input-group-text" id="basic-addon2" style="background:#fd397a;">
										<img class="pull-left" src="<?= SITE_IN ?>images/icons/add.gif" height="12"/>
									</span>
								</div>
							</div>
							
						</div>
						
						<div class="">
							<select name='orders_list[]' id="orders_list" size=7 multiple style="width:100%;"></select> 
							<input type="hidden" name="batch_order_ids" id="batch_order_ids" value=""/>
						</div>
					</div>
					
					<div class="col-2 text-center">
						<b class="btn btn-secondary btn-lg">OR</b>
					</div>
					
					<div class="col-5">
					
						<div class="form-group">
							@shipper_company@
							<input type="hidden" name="shipper_company_id" id="shipper_company_id" />
						</div>
						
						<div class="pull-left">
							<?php print submitButtons(SITE_IN."application/orders/batch", "Locate Orders") ?>
							<input type="hidden" name="submit" value="Start Processing" />
						</div>
						<input class="btn btn-secondary btn-sm" type="button" id="clear_batch" value="Clear" onclick="clear_batch_value();" style="margin-left:20px;" />
						
					</div>
				</div>
			</form>
		</div>
	</div>
	
</div>  