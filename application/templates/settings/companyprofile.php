<? include_once("menu.php"); ?>

<style type="text/css">
 .shipper_detail h4
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
	.box_div{
		background:#fff;border:1px solid #ebedf2;"
	}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js"></script>

<div style="width:100%;padding-bottom:20px;">
	<div style="text-align: right;">
		<img src="<?=SITE_IN?>images/icons/billing.png" alt="Billing" width="16" height="16" style="vertical-align:middle;" />&nbsp;<a href="<?=getLink("billing")?>">Billing</a> &nbsp;&nbsp;&nbsp;
		<img src="<?=SITE_IN?>images/icons/rating.png" alt="Rating" width="16" height="16" style="vertical-align:middle;" />&nbsp;<a href="<?=getLink("ratings")?>">Ratings</a> &nbsp;&nbsp;&nbsp;
		<img src="<?=SITE_IN?>images/icons/attach.png" alt="Documents" width="16" height="16" style="vertical-align:middle;" />&nbsp;<a href="<?=getLink("documents")?>">Documents</a> &nbsp;&nbsp;&nbsp;
		<img src="<?=SITE_IN?>images/icons/earn.png" alt="Referral" width="16" height="16" style="vertical-align:middle;" />&nbsp;<a href="<?=getLink("freemonth")?>">Referral</a>
	</div>
</div>

<?php if (count($this->highlight) > 0) {?>
<script type="text/javascript">
	$(document).ready(function() {
		<?php foreach ($this->highlight as $field) {?>
		$("[name='<?=$field?>']").addClass('input-error');
		<?php }?>
		$(".input-error").focus(function() {
			$(this).removeClass('input-error');
		});
	});
</script>
<?php }?>

<script type="text/javascript" src="<?=SITE_IN?>jscripts/jquery.ajaxupload.js"></script>

<form action="<?=getLink("companyprofile")?>" method="post">
	<div style="background:#fff;border:1px solid #ebedf2;" >
		<div class="kt-portlet__head hide_show" id="accordion_title">
			<div class="shipper_detail">
				<?=formBoxStart("Company Information")?>
			</div>
		</div>
		<div  class="kt-portlet__body" style="padding-left:20px;padding-right:20px;" >
			<div class="row">
				<div class="col-12">
					<div class="form-group">
					    <br/>
						<label>
							@companyname@ &nbsp;<em>To edit your company name, call support at <strong><?=$this->daffny->cfg['phone']?></strong></em>.
						</label>
					</div>
				</div>
			</div>
			<div class="row">
			    <div class="col-6 col-sm-3">
					<div class="form-group">
						<label><span class="required">*</span>Type:</label> &nbsp;@is_broker@ @is_carrier@
					</div>
				</div>
				<div class="col-6 col-sm-8">
					<div class="form-group">
					<label>@sync@</label> &nbsp;<em>Companies in the Freight Dragon Network will always have current contact information with this option activated</em>
					</div>
				</div>
			</div>
			<div class="row">
			    <div class="col-6 col-sm-3">
					<div class="form-group">
						@hours_or_operation@
					</div>
				</div>
					<div class="col-6 col-sm-3">
					<div class="form-group">
						@timezone@
					</div>
				</div>
					<div class="col-6 col-sm-3">
					<div class="form-group">
						@site@
					</div>
				</div>
				<div class="col-6 col-sm-3">
					<div class="form-group">
						@image@
						<div style="height: 20px;"><img id="upload_process" src="<?=SITE_IN?>images/uploading_file.gif" alt="uploading..." style="display: none;" /></div>
						<div id="cat"><?=$this->image_file?></div>
					</div>
				</div>
			</div>
			<div class="row">
					<div class="col-6 col-sm-3">
					<div class="form-group">
					   @mc_number@
					</div>
				</div>
				<div class="col-6 col-sm-3">
					<div class="form-group">
						@owner@
					</div>
				</div>
				<div class="col-6 col-sm-3">
					<div class="form-group">
						@contactname@
					</div>
				</div>
				<div class="col-6 col-sm-3">
					<div class="form-group">
						@email@
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-6 col-sm-3">
				    <div class="form-group">
						@phone@
					</div>
					<div class="form-group">
						@phone_local@
					</div>
					 <div class="form-group">
						@fax@
					</div>
				</div>
				<div class="col-6 col-sm-3">
					<div class="form-group">
						@phone_tollfree@
					</div>
					<div class="form-group">
						@phone_cell@
					</div>
					<div class="form-group">
						@preferred_contact_method@
					</div>
				</div>
				<div class="col-6 col-sm-3">
					<div class="form-group">
						@address1@
					</div>
					<div class="form-group">
						@city@
					</div>
					<div class="form-group">
						@zip_code@
					</div>
				</div>
				<div class="col-6 col-sm-3">
					<div class="form-group">
					<label>Address 2</label>
						@address2@
					</div>
                    <div class="form-group">
						@state@
					</div>
					<div class="form-group">
						@country@
					</div>
				</div>
			</div>
			<?=formBoxEnd()?>
		</div>
	</div>
	<div class="box_div mt-3">
		<div class="kt-portlet__head hide_show" id="accordion_title">
			<div class="shipper_detail">
				<?=formBoxStart("Operating Authority")?>
			</div>
		</div>
		<div class="kt-portlet__body" style="padding-left:20px;padding-right:20px;">
		    <br>
			<em>Please provide the Operating Authority Number provide by the <strong>FMCSA</strong></em>
			<div class="form-group">
				<br/>
				@icc_mc_number@
			</div>
		</div>
		<?=formBoxEnd()?>
	</div>
	<div class="box_div mt-3" >
		<div class="kt-portlet__head hide_show" id="accordion_title">
			<div class="shipper_detail">
				<?=formBoxStart("Insurance Company Information")?>
			</div>
		</div>
		<div class="kt-portlet__body" style="padding-left:20px;padding-right:20px;">
			<div class="row">
				<div class="col-12">
				    <br>
					<em>Please provide Insurance information to better manage claims and issues with damaged cargo.</em>
					<br/>
					<br/>
				</div>
			</div>
			<div class="row">
				<div class="col-6 col-sm-3">
					<div class="form-group">
						@insurance_company@
					</div>
				</div>
				<div class="col-6 col-sm-3">
					<div class="form-group">
						@insurance_policy_number@
					</div>
				</div>
				<div class="col-6 col-sm-3">
					<div class="form-group">
						@insurance_agent_name@
					</div>
				</div>
				<div class="col-6 col-sm-3">
					<div class="form-group">
						@insurance_agent_phone@
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-6 col-sm-3">
					<div class="form-group">
						@liability_amount@
					</div>
				</div>
				<div class="col-6 col-sm-3">
					<div class="form-group">
						@insurance_coverage@
					</div>
				</div>
				<div class="col-6 col-sm-3">
					<div class="form-group">
						@cargo_deductible@
					</div>
				</div>
				<div class="col-6 col-sm-3">
					<div class="form-group input_wdh_100_per">
						@insurance_expdate@
					</div>
				</div>
			</div>
		</div>
		<?=formBoxEnd()?>
	</div>
	<div class="box_div mt-3" >
		<div class="kt-portlet__head hide_show" id="accordion_title">
			<div class="shipper_detail">
				<?=formBoxStart("Broker Bond")?>
			</div>
		</div>
		<div class="kt-portlet__body" style="padding-left:20px;padding-right:20px;" >
		    <br/>
			<div class="row">
				<div class="col-12 col-sm-6">
					<div class="form-group">
						@brocker_bond_name@
					</div>
				</div>
				<div class="col-12 col-sm-6">
					<div class="form-group">
						@brocker_bond_phone@
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="box_div mt-3">
		<div class="kt-portlet__head hide_show" id="accordion_title">
			<div class="shipper_detail">
				<?=formBoxStart("Reference Information")?>
			</div>
		</div>
		<div class="kt-portlet__body" style="padding-left:20px;padding-right:20px;">
		<br/>
			<div class="row">
				<div class="col-12 col-sm-3">
					<div class="form-group">
						@established@
					</div>
				</div>
				<div class="col-12 col-sm-3">
					<div class="form-group">
						@ref1_name@
					</div>
				</div>
				<div class="col-12 col-sm-3">
					<div class="form-group">
						@ref1_phone@
					</div>
				</div>
				<div class="col-12 col-sm-3">
					<div class="form-group">
						@ref2_name@
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-12 col-sm-3">
					<div class="form-group">
						@ref2_phone@
					</div>
				</div>
				<div class="col-12 col-sm-3">
					<div class="form-group">
						@ref3_name@
					</div>
				</div>
				<div class="col-12 col-sm-3">
					<div class="form-group">
						@ref3_phone@
					</div>
				</div>
				<div class="col-12 col-sm-3">
					<div class="form-group input_wdh_100_per">
						@description@
					</div>
				</div>
			</div>
		</div>
		<?=formBoxEnd()?>
	</div>
	<div class="box_div mt-3">
		<div class="kt-portlet__head hide_show" id="accordion_title">
			<div class="shipper_detail">
				<?=formBoxStart("Sales information")?>
			</div>
		</div>
		<div class="kt-portlet__body" style="padding-left:20px;padding-right:20px;">
			<br/>	
			<div class="row">
				<div class="col-12 col-sm-4">
					<div class="form-group">
						@sales_phone@
					</div>
				</div>
				<div class="col-12 col-sm-4">
					<div class="form-group">
						@sales_fax@
					</div>
				</div>
				<div class="col-12 col-sm-4">
					<div class="form-group">
						@sales_email@
						<br/>
						<br/>
						@sales_email_bcc@
					</div>
				</div>
			</div>
		</div>
		<?formBoxEnd()?>
	</div>
	<div class="box_div mt-3">
		<div class="kt-portlet__head hide_show" id="accordion_title">
			<div class="shipper_detail">
				<?=formBoxStart("Dispatch information")?>
			</div>
		</div>
		<div class="kt-portlet__body" style="padding-left:20px;padding-right:20px;">
			<br/>	
			<div class="row">
				<div class="col-12 col-sm-4">
					<div class="form-group">
						@dispatch_contact@
					</div>
				</div>
				<div class="col-12 col-sm-4">
					<div class="form-group">
						@dispatch_email@
					</div>
				</div>
				<div class="col-12 col-sm-4">
					<div class="form-group">
						@dispatch_phone@
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-12 col-sm-4">
					<div class="form-group">
						@dispatch_fax@
					</div>
				</div>
				<div class="col-12 col-sm-4">
					<div class="form-group">
						@dispatch_accounting_fax@
					</div>
				</div>
				<div class="col-12 col-sm-4">
					<div class="form-group">
						@delivery_confirmation_mail@
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="box_div mt-3">
		<div class="kt-portlet__head hide_show" id="accordion_title">
			<div class="shipper_detail">
				<?=formBoxStart("Customer Service")?>
			</div>
		</div>
		<div class="kt-portlet__body" style="padding-left:20px;padding-right:20px;">
			<br/>
			<div class="row">
				<div class="col-12 col-sm-4">
					<div class="form-group">
						@support_phone@
					</div>
				</div>
				<div class="col-12 col-sm-4">
					<div class="form-group">
						@support_fax@
					</div>
				</div>
				<div class="col-12 col-sm-4">
					<div class="form-group">
						@support_email@
					</div>
				</div>
			</div>
		</div>
		<?=formBoxEnd()?>
	</div>
	<br/><br/>
	<div class="row">
		<div class="col-10 text-right">
			<strong><font color="red">IMPORTANT</font>:</strong> THIS WILL UPDATE YOUR COMPANY PROFILE IN THE FREIGHTDRAGON NETWORK.
		</div>
		<div class="col-2 text-right">
			<?php echo submitButtons(getLink("companyprofile"), "Save"); ?>
		</div>
	</div>
	<br/><br/>
</form>

<script type="text/javascript">
	
	$(function(){
		new AjaxUpload('#image', {
			action: '<?=getLink("companyprofile", "upload-file")?>',
			name: 'image',
			onChange: function(file, extension){
				this.setData({});
			},
			onSubmit: function(file , ext){
				if (!(ext && /^(jpg|gif|png|jpeg)$/.test(ext))){
					alert('Invalid file extension.\nAllowed file extensions: *.jpg, gif, png, jpeg');
					return false;
				}
				$('#submit_button').val('Please wait...');
				$('#submit_button').attr('disabled', 'disabled');
				$('#upload_process').fadeIn();
			},
			onComplete : function(file, response){
				$('#upload_process').fadeOut();
				$('#upload_process').hide();
				$('#submit_button').removeAttr('disabled');
				$('#submit_button').val('OK');
				if (response.indexOf('ERROR:') != -1) {
					alert('Cant\' upload file.\n'+response);
					return false;
				}
				$('#cat').html(response);
			}
		});
	});

	$(function(){
		function checkCountry(){
			if ($("#country").val() == "US"){
				$("#st_other").hide();
				$("#st").show();
			}else{
				$("#st").hide();
				$("#st_other").show();
			}
		}
		$("#country").change(function(){
			checkCountry();
		});
		checkCountry();
	});

	$(function(){
	    $('#insurance_expdate').datepicker(datepickerSettings);
	});

	$("#phone, #phone_local, #phone_tollfree, #phone_cell, #fax, #brocker_bond_phone,  #ref1_phone, #ref2_phone, #ref3_phone, #sales_phone, #sales_fax,  #dispatch_fax, #dispatch_phone, #dispatch_fax,  #dispatch_accounting_fax, #support_phone, #support_fax").attr("placeholder", "xxx-xxx-xxxx");
	$("#liability_amount,#insurance_coverage,#cargo_deductible").attr("placeholder", "0.00");

	jQuery(function($){

		$("#sales_phone").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});
		$("#brocker_bond_phone").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});
		$("#fax").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});
		$("#ref1_phone").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});
		$("#ref2_phone").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});
		$("#ref3_phone").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});
		$("#phone").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});
		$("#phone_local").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});
		$("#phone_tollfree").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});
		$("#phone_cell").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});
		$("#preferred_contact_method").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});
		$("#sales_fax").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});
		$("#dispatch_phone").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});
		$("#dispatch_fax").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});
		$("#dispatch_accounting_fax").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});
		$("#support_phone").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});
		$("#support_fax").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});
		// $('#liability_amount').mask("99.99",{placeholder:"0.00"});
		// $('#insurance_coverage').mask("999999999.99",{placeholder:"0.00"});
		// $('#cargo_deductible').mask("999999999.99",{placeholder:"0.00"});
  	});

	$('#liability_amount').keypress(function(event) {
		if ((event.which != 46 || $(this).val().indexOf('.00') != -1) && (event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
	});

</script>



