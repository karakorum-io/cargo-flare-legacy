<? include_once("menu.php"); ?>
<div style="text-align: right;">
	<img src="<?=SITE_IN?>images/icons/billing.png" alt="Billing" width="16" height="16" style="vertical-align:middle;" />&nbsp;<a href="<?=getLink("billing")?>">Billing</a> &nbsp;&nbsp;&nbsp;
	<img src="<?=SITE_IN?>images/icons/rating.png" alt="Rating" width="16" height="16" style="vertical-align:middle;" />&nbsp;<a href="<?=getLink("ratings")?>">Ratings</a> &nbsp;&nbsp;&nbsp;
	<img src="<?=SITE_IN?>images/icons/attach.png" alt="Documents" width="16" height="16" style="vertical-align:middle;" />&nbsp;<a href="<?=getLink("documents")?>">Documents</a> &nbsp;&nbsp;&nbsp;
	<?/*<img src="<?=SITE_IN?>images/icons/contract.png" alt="Contract" width="16" height="16" style="vertical-align:middle;" />&nbsp;<a href="<?=getLink("companyprofile", "contract")?>">Contract</a> &nbsp;&nbsp;&nbsp;*/?>
	<img src="<?=SITE_IN?>images/icons/earn.png" alt="Referral" width="16" height="16" style="vertical-align:middle;" />&nbsp;<a href="<?=getLink("freemonth")?>">Referral</a>
</div>
<br />
<?php if (count($this->highlight) > 0) { ?>
<script type="text/javascript">
	$(document).ready(function() {
		<?php foreach ($this->highlight as $field) { ?>
		$("[name='<?=$field?>']").addClass('input-error');
		<?php } ?>
		$(".input-error").focus(function() {
			$(this).removeClass('input-error');
		});
	});
</script>
<?php } ?>
<script type="text/javascript" src="<?=SITE_IN?>jscripts/jquery.ajaxupload.js"></script>
<form action="<?=getLink("companyprofile")?>" method="post">
	<div style="float:left; width:480px; padding-right:10px;">
		<?=formBoxStart("Contact information")?>
	    <table cellspacing="5" cellpadding="5" border="0">
			<tr><td colspan="2" style="font-size:20px;" class="lightblue">@companyname@</td></tr>
			<tr><td colspan="2"><em>To edit your company name, call support at <strong><?=$this->daffny->cfg['phone']?></strong></em>.</td>
			<tr>
                        <tr>
                        <tr>
                        </tr>
		    <tr><td>&nbsp;</td><td>@sync@</br><em>Companies in the Freight Dragon Network will always have current contact information with this option activated</em></td>
		    <tr>
                    <tr>
                    <tr>
                    </tr>
			<tr><td><span class="required">*</span>Type:</td><td>@is_broker@ @is_carrier@<td></tr>
	        <tr><td>@owner@</td></tr>
	        <tr><td>@address1@</td></tr>
	        <tr><td>@address2@</td></tr>
	        <tr><td>@city@</td></tr>
	        <tr id="st"><td>@state@</td></tr>
	        <tr style="display:none;" id="st_other"><td>@state_other@</td></tr>
	        <tr><td>@zip_code@</td></tr>
	        <tr><td>@country@</td></tr>
	        <tr><td>@timezone@</td></tr>
	        <tr><td>@contactname@</td></tr>
	        <tr><td>@phone@</td></tr>
	        <tr><td>@phone_local@</td></tr>
	        <tr><td>@phone_tollfree@</td></tr>
	        <tr><td>@phone_cell@</td></tr>
	        <tr><td>@fax@</td></tr>
	        <tr><td>@hours_or_operation@</td></tr>
	        <tr><td>@email@</td></tr>
	        <tr><td>@preferred_contact_method@</td></tr>
	        <tr><td>@site@</td></tr>
	        <tr><td>@mc_number@</td></tr>
			<tr><td>@image@</td></tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<div style="height: 20px;"><img id="upload_process" src="<?=SITE_IN?>images/uploading_file.gif" alt="uploading..." style="display: none;" /></div>
					<div id="cat"><?=$this->image_file?></div>
				</td>
			</tr>
	    </table>
		<?=formBoxEnd()?>
		<br />
		<?=formBoxStart("Operating Authority")?>
		<table cellspacing="5" cellpadding="5" border="0">
		<em>Please provide the Operating Authority Number provide by the <strong>FMCSA</strong></em>
		<tr>
                <tr>
                <tr>
			<tr><td>@icc_mc_number@</td></tr>
		</table>
		<?=formBoxEnd()?>
		<br/>
		<?=formBoxStart("Insurance Company Information")?>
	    <table cellspacing="5" cellpadding="5" border="0">
	    <em>Please provide Insurance information to better manage claims and issues with damaged cargo.</em>
	    <tr>
            <tr>
            <tr>
	        <tr><td>@insurance_company@</td></tr>
	        <tr><td>@insurance_policy_number@</td></tr>
	        <tr><td>@insurance_agent_name@</td></tr>
	        <tr><td>@insurance_agent_phone@</td></tr>
	        <tr><td>@liability_amount@</td></tr>
	        <tr><td>@insurance_coverage@</td></tr>
	        <tr><td>@cargo_deductible@</td></tr>
	        <tr><td>@insurance_expdate@</td></tr>
	    </table>
	    <?=formBoxEnd()?>
	    <br />
		<?php echo submitButtons(getLink("companyprofile"), "Save"); ?>
	</div>
	<div style="float:left; width:480px;">
		<?=formBoxStart("Broker Bond")?>
		    <table cellspacing="5" cellpadding="5" border="0">
		        <tr><td>@brocker_bond_name@</td></tr>
		        <tr><td>@brocker_bond_phone@</td></tr>
		    </table>
	    <?=formBoxEnd()?>
	    <br />
		<?=formBoxStart("Reference Information")?>
		    <table cellspacing="5" cellpadding="5" border="0">
		        <tr><td>@established@</td></tr>
		        <tr><td>@ref1_name@</td></tr>
		        <tr><td>@ref1_phone@</td></tr>
		        <tr><td>@ref2_name@</td></tr>
		        <tr><td>@ref2_phone@</td></tr>
		        <tr><td>@ref3_name@</td></tr>
		        <tr><td>@ref3_phone@</td></tr>
		        <tr><td valign="top">@description@</td></tr>
		    </table>
		<?=formBoxEnd()?>
			<br />
		<?=formBoxStart("Sales information")?>
		    <table cellspacing="5" cellpadding="5" border="0">
		        <tr><td>@sales_phone@</td></tr>
		        <tr><td>@sales_fax@</td></tr>
		        <tr><td>@sales_email@</td></tr>
		        <tr><td colspan="2">@sales_email_bcc@</td></tr>
		    </table>
		<?=formBoxEnd()?>
		<br />
		<?=formBoxStart("Dispatch information")?>
		    <table cellspacing="5" cellpadding="5" border="0">
		        <tr><td>@dispatch_contact@</td></tr>
		        <tr><td>@dispatch_email@</td></tr>
		        <tr><td>@dispatch_phone@</td></tr>
		        <tr><td>@dispatch_fax@</td></tr>
                <tr><td>@dispatch_accounting_fax@</td></tr>
                <tr><td>@delivery_confirmation_mail@</td></tr>
		    </table>
		<?=formBoxEnd()?>
		<br />
		<?=formBoxStart("Customer Service")?>
		    <table cellspacing="5" cellpadding="5" border="0">
		        <tr><td>@support_phone@</td></tr>
		        <tr><td>@support_fax@</td></tr>
		        <tr><td>@support_email@</td></tr>
		    </table>
		<?=formBoxEnd()?>
	</div>
	<div style="clear:both;">&nbsp;</div>
</form>
<script type="text/javascript">//<![CDATA[
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
//]]></script>