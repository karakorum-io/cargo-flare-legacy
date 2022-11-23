<?if (isset($_GET['id']) && $_GET['id'] > 0){
	include(TPL_PATH."accounts/accounts/menu_details.php");
}else{
	include(TPL_PATH."accounts/accounts/menu.php");
}?>

<div align="left" style="clear:both; padding-bottom:20px;">
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?=getLink("accounts")?>">&nbsp;Back to the list</a>
</div>

<form action="<?=getLink("accounts", "import")?>" method="post">
	
	<div class="kt-portlet">
		
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<?=formBoxStart("Account Information")?>
			</div>
		</div>
		
		
		<div class="kt-portlet__body">
		
			<div class="kt-section__info m_btm_10">Complete the form below and click "Save Account" when finished.</div>
			
			<input type="hidden" name="member_id" id="member_id" value=""/>
			
			<div class="row">
			
				<div class="col-3">
					<div class="form-group select_opt_new_info">
						<label>Import Carrier:</label>
						<?=functionButton("Search Account", "selectGlobalCarrier()",'','btn btn-sm btn_dark_blue')?>
					</div>
				</div>
				
				<div class="col-3">
					<div class="form-group select_opt_new_info">
						@company_name@
					</div>
				</div>
				
				<div class="col-3">
					<div class="form-group select_opt_new_info">
						@status@
					</div>
				</div>
				
				<div class="col-3">
					<div class="form-group">
						<label>&nbsp;</label>
						@donot_dispatch@
					</div>
				</div>
				
			</div>
			
			<div class="row">
				<div class="col-12">
					<div class="form-group input_wdh_100_per">					
						@notes@
					</div>
				</div>
			</div>
			
		</div>
		<?=formBoxEnd()?>
		
	</div>
	
	
	<div class="kt-portlet">
		
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				 <?=formBoxStart("Contact Information")?>
			</div>
		</div>
		
		<div class="kt-portlet__body">
			<div class="row">
			
				<div class="col-3">
					<div class="form-group">
						@contact_name@
					</div>
				</div>
				
				<div class="col-3">
					<div class="form-group">
						@phone1@
					</div>
				</div>
				
				<div class="col-3">
					<div class="form-group">
						@phone2@
					</div>
				</div>
				
				<div class="col-3">
					<div class="form-group">
						@cell@
					</div>
				</div>
				
			</div>
			<div class="row">
			
				<div class="col-3">
					<div class="form-group">
						@fax@
					</div>
				</div>
				
				<div class="col-3">
					<div class="form-group">
						@email@
					</div>
				</div>
				
				<div class="col-3">
					<div class="form-group">
						@address1@
					</div>
				</div>
				
				<div class="col-3">
					<div class="form-group">
						@address2@
					</div>
				</div>
				
			</div>
			<div class="row">
			
				<div class="col-3">
					<div class="form-group">
						@city@
					</div>
				</div>
				
				<div class="col-3">
					<div class="form-group">
						@state@
					</div>
				</div>
				
				<div class="col-3">
					<div class="form-group">
						@zip@
					</div>
				</div>
				
				<div class="col-3">
					<div class="form-group">
						@country@
					</div>
				</div>
				
			</div>
		</div>
		<?=formBoxEnd()?>
		
	</div>
	
	
	<div class="kt-portlet">
		
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<?=formBoxStart("Insurance Company Information")?>
			</div>
		</div>
		
		<div class="kt-portlet__body">
			<div class="row">
			
				<div class="col-4">
					<div class="form-group">
						@insurance_companyname@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@insurance_address@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@insurance_phone@
					</div>
				</div>
			</div>
				
			<div class="row">
				
				<div class="col-2 select_wdh_100_per">
					<div class="form-group">
						@insurance_holder@
					</div>
				</div>
				
				<div class="col-2 select_wdh_100_per">
					<div class="form-group">
						@insurance_insured@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@insurance_agentname@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@insurance_agentphone@
					</div>
				</div>
				
			</div>
			
			
			<div class="row">
			
				<div class="col-3">
					<div class="form-group">
						@insurance_policynumber@
					</div>
				</div>
				
				<div class="col-3">
					<div class="form-group input_wdh_100_per">
						@insurance_expirationdate@
					</div>
				</div>
				
				<div class="col-3">
					<div class="form-group select_wdh_100_per">
						@insurance_contract@
					</div>
				</div>
				
				<div class="col-3">
					<div class="form-group">
						@insurance_iccmcnumber@
					</div>
				</div>
				
			</div>
			
		</div>
		
		<?=formBoxEnd()?>
		
	</div>
	
	<div class="text-right">
		<?=submitButtons(getLink("accounts"), "Save")?>
	</div>
	
</form>

<script type="text/javascript">
//<![CDATA[
function applyGlobalSearch(num) {
	var acc_obj = acc_data[num];
	switch (acc_type) {
		case <?=Account::TYPE_CARRIER?>:
			$("#member_id").val(acc_obj.member_id);
			$("#company_name").val(acc_obj.company_name);
			$("#contact_name").val(acc_obj.contact_name);
			$("#phone1").val(acc_obj.dispatch_phone);
			$("#phone2").val(acc_obj.company_phone);
			$("#cell").val(acc_obj.company_cell);
			$("#fax").val(acc_obj.company_fax);
			$("#email").val(acc_obj.company_email);
			$("#address1").val(acc_obj.company_address1);
			$("#address2").val(acc_obj.company_address2);
			$("#state").val(acc_obj.company_state);
            $("#city").val(acc_obj.company_city);
			$("#zip").val(acc_obj.company_zip);
			$("#country").val(acc_obj.company_country);
			$("#insurance_companyname").val(acc_obj.insurance_companyname);
			$("#insurance_expirationdate").val(acc_obj.insurance_expirationdate);
			$("#insurance_iccmcnumber").val(acc_obj.insurance_iccmcnumber);
			$("#insurance_policynumber").val(acc_obj.insurance_policynumber);
			$("#insurance_agentname").val(acc_obj.insurance_agentname);
			$("#insurance_agentphone").val(acc_obj.insurance_agentphone);
			break;
	}
}
$(function(){

});
//]]>
</script>

