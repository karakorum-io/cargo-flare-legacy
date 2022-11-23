<style>
	.form_box_btn_new_info
	{
		margin-top:20px;
	}
	.form_box_btn_new_info .form-box-buttons
	{
		display:inline-block;
	}
</style>
<? include(TPL_PATH."myaccount/menu.php");?>

<div style="display:inline-block;width:100%;clear:both; padding-bottom:20px;">
	<div class="pull-left">
		<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?=getLink("companyprofile")?>">&nbsp;Company profile</a>
	</div>
	<div class="pull-right">
		<img src="<?=SITE_IN?>images/icons/referrals.png" alt="Referrals" width="16" height="16" style="vertical-align:middle;" />&nbsp;<a href="<?=getLink("freemonth", "referrals")?>">View Signed-Up Referrals</a>
	</div>
</div>
<br />
<form action="<?=getLink("freemonth")?>" method="post">
	
	<div class="kt-portlet">
	
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<?=formBoxStart("Refer a Friend");?>
				<div style="margin-left:20px;">Your referral code is: <strong>@ref_code@</strong></div>
			</div>
		</div>
		
		<div class="kt-portlet__body">
			<div class="row">
				<div class="col-4">
					<div class="form-group">
						@friend_name@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@friend_email@
						<em>Emails will never be used for any other marketing purpose.</em>
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@your_name@
					</div>
				</div>
				
				<div class="col-12">
					<div class="form-group input_wdh_100_per">
						@personal_message@
					</div>
				</div>
				
				<div class="col-12">
					<div style="width:100%;padding:10px;background-color:#fffbd8;">
						@is_terms@<br/>By sending this email you certify that you are a friend or business associate of the referral. You will receive one 1-month credit to your account for each referral that is approved and signs up as a paid user to FreightDragon and 4 continuous months after both accounts have been active. Referrals must follow the link in the email for credit to be properly tracked and applied.
					</div>
				</div>
				
				<div class="col-12 text-right form_box_btn_new_info">
					<?php echo submitButtons("", "Send"); ?> <?=functionButton("Review Email", "reviewEmail();",'','btn-sm btn_dark_green');?>
				</div>
				
			</div>
		</div>
		
		<?=formBoxEnd();?>
	</div>
	
	<div id="emailtpl">@emailtpl@</div>

	
</form>

<script type="text/javascript">//<![CDATA[
	$(document).ready(function () {
		$('#emailtpl').hide();
	});
$("#emailtpl").dialog({
		modal:true,
		width:400,
		height:380,
		title:"Review Email",
		hide:'fade',
		resizable:false,
		draggable:false,
		autoOpen:false,
		buttons:{
			"Close": function() {
				$( this ).dialog( "close" );
			}
		}
	});

function reviewEmail() {
	$("#emailtpl").dialog("open");
}
//]]></script>