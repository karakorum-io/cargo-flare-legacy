<? include_once("menu.php"); ?>

<style type="text/css">
	.shipper_detail h4 {
		text-align:left;
		font-size:15px;
		color:#222;
		height:40px;
		line-height:40px;
		padding-left:15px;
		background-color:#f7f8fa;
		border-bottom:1px solid #ebedf2;
	}
</style>
<div style="width:100%;padding-bottom:20px;">
	<div style="text-align: right;">
        <img src="<?= SITE_IN ?>images/icons/billing.png" alt="Billing" width="16" height="16" style="vertical-align:middle;" />&nbsp;<a href="<?= getLink("billing") ?>">Billing</a> &nbsp;&nbsp;&nbsp;
        <img src="<?= SITE_IN ?>images/icons/rating.png" alt="Rating" width="16" height="16" style="vertical-align:middle;" />&nbsp;<a href="<?= getLink("ratings") ?>">Ratings</a> &nbsp;&nbsp;&nbsp;
        <img src="<?= SITE_IN ?>images/icons/attach.png" alt="Documents" width="16" height="16" style="vertical-align:middle;" />&nbsp;<a href="<?= getLink("documents") ?>">Documents</a> &nbsp;&nbsp;&nbsp;        
        <img src="<?= SITE_IN ?>images/icons/earn.png" alt="Referral" width="16" height="16" style="vertical-align:middle;" />&nbsp;<a href="<?= getLink("freemonth") ?>">Referral</a>
    </div>
</div>

<form action="<?= getLink("defaultsettings") ?>" method="post">
	<div  style="background:#fff;border:1px solid #ebedf2;" class=" kt-portlet mb-5 mt-3">
	
		<div class=" hide_show ">
			<div class="shipper_detail">
				<?= formBoxStart("Default Settings") ?>
			</div>
		</div>
		
		<div class="kt-portlet__body body_div_box_show">
			
			<div class="row">
				<div class="col-12">
					<em>Enter new value and click the <strong>"Save"</strong> button.</em>
					<br/>
					<br/>
				</div>
			</div>
			
			
			<div class="row">
			
				<div class="col-12 col-sm-3">
				     <div class="form-group">
					<?= formBoxStart("Default System Settings") ?>
					</div>
				    <div class="form-group input_wdh_100_per">
						@lead_start_number@
					</div>
					<div class="form-group order_deposit_required_new_info">
						<div class="row">
							<div class="col-7 input_wdh_100_per select_opt_new_info">
								@order_deposit@
							</div>
							<div class="col-5 select_wdh_100_per select_opt_new_info">
								<label>&nbsp;</label>
								@order_deposit_type@
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-6">
							<div class="form-group input_styling_info select_opt_new_info">
								@logout_h@ <div style="margin-top:10px;margin-left:15px;" class="pull-left">hrs.</div>
							</div>
						</div>
						<div class="col-6">
							<div class="form-group input_styling_info select_opt_new_info">
								<label>&nbsp;</label>
								@logout_m@ <div style="margin-top:10px;margin-left:15px;" class="pull-left">min.</div>
							</div>
						</div>
					</div>
					<br>
					<div class="form-group select_wdh_100_per">
						@zoom_level@
					</div>
					<div class="form-group input_wdh_100_per">
						@email_blind@
						<em>Enter email address(es) which should receive a copy of all outgoing email. Separate multiple addresses with commas.</em>
					</div>
					<div class="col-12">
					@hide_orders@
				</div>
				</div>
	             &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  
				<div class="col-12 col-sm-3">
				    <div class="form-group">
					<?= formBoxStart("Default Status Updates") ?>
					</div>
				   <div class="form-group select_opt_new_info input_styling_info">
						@first_quote_followup@ <div style="margin-top:10px;margin-left:15px;" class="pull-left">days since quoted</div>
					</div>
					<br>
				    <div class="form-group select_opt_new_info input_styling_info">
						@mark_as_expired@ <div style="margin-top:10px;margin-left:15px;" class="pull-left">days since received / quoted / ordered</div>
					</div>
					<br>
					<div class="form-group input_styling_info select_opt_new_info">
						@mark_assumed_delivered@ <div style="margin-top:10px;margin-left:15px;" class="pull-left">days since est. delivery</div>
					</div>
				</div>
                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
				
				<div class="col-12 col-sm-5">
				    <div class="form-group">
						<!---<label><span>Default Dispatch Terms</span></label>---><?= formBoxStart("Default Dispatch Terms") ?>
					</div>
					<div class="form-group">
						@payments_terms_cod@
					</div>
					<div class="form-group">
						@payments_terms_cop@
					</div>
					<div class="form-group">
						@payments_terms_billing@
					</div>
					<div class="form-group">
						@payments_terms_invoice@
					</div>
					<br>
					<em>To update the information please click <strong>"save"</strong> below on the server. If you have more changes to make, please use the "save" button on the buttom of the page when completely done.</em><br/><br/>
					<div class="text-center">
                    <?php echo submitButtons(getLink("defaultsettings"), "Save"); ?>
	                </div>
				</div>
				
				
			</div>
			
		</div>
		
	
	</div>
	
	<script>
		$(document).ready(function(){
			$("#tabs-998").hide();
			
			$("#CD").click(function(){
				$(".api-tabs").removeClass("ui-state-active");
				$("#CD").addClass("ui-state-active");
				$("#tabs-custom .custom-tabs").hide();
				$("#tabs-999").show();
			});
			
			$("#TAQ").click(function(){
				$(".api-tabs").removeClass("ui-state-active");
				$("#TAQ").addClass("ui-state-active");
				$("#tabs-custom .custom-tabs").hide();
				$("#tabs-998").show();
			});                
		});
	</script>
	
	<script>
		$(document).ready(function(){
			$("#authorize_tab_info").hide();
			$("#mdsip_tab_info").hide();
			
			$("#authorize_tab").click(function(){
				$(".api-tabs").removeClass("ui-state-active");
				$("#authorize_tab").addClass("ui-state-active");
				$("#payment_tabs-custom_new .custom-tabs").hide();
				$("#authorize_tab_info").show();
			});
			
			$("#paypal_tab").click(function(){
				$(".api-tabs").removeClass("ui-state-active");
				$("#paypal_tab").addClass("ui-state-active");
				$("#payment_tabs-custom_new .custom-tabs").hide();
				$("#paypal_tab_info").show();
			});
			
			$("#mdsip_tab").click(function(){
				$(".api-tabs").removeClass("ui-state-active");
				$("#mdsip_tab").addClass("ui-state-active");
				$("#payment_tabs-custom_new .custom-tabs").hide();
				$("#mdsip_tab_info").show();
			}); 
			
			$("#easy_tab").click(function(){
				$(".api-tabs").removeClass("ui-state-active");
				$("#easy_tab").addClass("ui-state-active");
				$("#payment_tabs-custom_new .custom-tabs").hide();
				$("#easy_tab_info").show();
			}); 
		});
	</script>
	
	
	<style>
		.margin-bottom-20
		{
			margin-bottom: 20px;
			line-height: normal;
		}		
	</style>
	
	<div  class=" kt-portlet mb-5 mt-3" style="background:#red;border:1px solid #ebedf2;">
	
		<div class="hide_show">
			<div class="shipper_detail">
				<?= formBoxStart("Third Party APIs") ?>
			</div>
		</div>
		
		<div class="kt-portlet__body" style="padding-left:20px;padding-right:20px;">
			<div id="tabs-custom" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
				<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
					<li id="CD" class="api-tabs ui-state-default ui-corner-top ui-tabs-selected ui-state-active">
						<a>Central Dispatch</a>
					</li>
					<li id="TAQ" class="api-tabs ui-state-default ui-corner-top ui-tabs-selected">
						<a>TAQ</a>
					</li>
				</ul>
				<div id="tabs-999" class="custom-tabs ui-tabs-panel ui-widget-content ui-corner-bottom">
					<div class="kt-section__info">
					Please enter your <strong>UID</strong> information below. You can obtain this information from <strong>Centraldispatch.com</strong> by clicking on <strong>Ship Vehicles Import Vehicle</strong> to request the <strong>UID</strong> or contact <strong>CentralDispatch.com</strong> at (858) 259-6084 and request your <strong>UID</strong> from a customer service agent.</div>
					
					<div class="row">
						<div class="col-6">
							<div class="form-group">
								@central_dispatch_uid@
							</div>
						</div>
						<div class="col-6">
							<div class="form-group select_wdh_100_per">
								@central_dispatch_post@
							</div>
						</div>						
					</div>
					
					<div class="text-right">
						<img src="<?= SITE_IN ?>images/icons/cd.png" width="131" height="21" alt="Central Dispatch" /0>
					</div>
					
				</div>
				<div id="tabs-998" class="custom-tabs ui-tabs-panel ui-widget-content ui-corner-bottom">
					<div class="kt-section__info">Please enter your <strong>API KEY</strong> and <strong>API PIN</strong> below. Contact your Member in case you are not holding the required information.</div>
					<div class="row">
						<div class="col-6">
							<div class="form-group">
								@auto_quote_api_pin@
							</div>
						</div>
						<div class="col-6">
							<div class="form-group select_wdh_100_per">
								@auto_quote_api_key@
							</div>
						</div>						
					</div>
					
					<div class="row">
						<div class="col-12 text-right">
							<table>
								<tr>
									<td>@on_off_auto_quoting@</td>
								</tr>
								<tr>
									<td>@on_off_auto_quoting_email@</td>
								</tr>
								<tr>
									<td>@aq_email_template@</td>
								</tr>
							</table>
							<script>
								$(document).ready(()=>{
									if($("#on_off_auto_quoting_email").prop('checked') == true){
										$("#aq_email_template").show();
									} else {
										$("#aq_email_template").hide();
									}
								});
								$("#on_off_auto_quoting_email").change(() => {
									if($("#on_off_auto_quoting_email").prop('checked') == true){
										$("#aq_email_template").show();
									} else {
										$("#aq_email_template").hide();
									}
								});
							</script>
							<img src="<?= SITE_IN ?>images/additionals/taq.jpg" width="131" height="30" alt="Central Dispatch" />
						</div>
					</div>
					
				</div>  
			</div>
			<br>
		</div>
		<?= formBoxEnd() ?>
	</div>
	
	<div class="kt-portlet" style="background:#fff;border:1px solid #ebedf2; margin:0 auto;" class="mb-5 mt-3">
	
		<div class="hide_show">
			<div class="shipper_detail">
				<?= formBoxStart("SMTP Settings") ?>
			</div>
		</div>

		<div  class="kt-portlet__body" style="padding-left:20px;padding-right:20px;">
			<div class="kt-section__info">Please enter the outgoing mailserver information to send emails such as quotes, orders and dispatch sheets from your email/server provider:</div>
			<p></p>
			<div class="row">			
				<div class="col-4">
					<div class="form-group">
						@smtp_server_name@
					</div>
				</div>
				<div class="col-4">
					<div class="form-group input_wdh_100_per">
						@smtp_server_port@
					</div>
				</div>
				<div class="col-4">
					<div class="form-group select_wdh_100_per">
						@smtp_use_ssl@
					</div>
				</div>				
			</div>
			
			<div class="row">			
				<div class="col-4">
					<div class="form-group">
						@smtp_user_name@
					</div>
				</div>
				<div class="col-4">
					<div class="form-group">
						@smtp_user_password@
					</div>
				</div>
				<div class="col-4">
					<div class="form-group">
						@smtp_from_email@
					</div>

				</div>
				
			</div>
			</div>
			
		</div>
		<?= formBoxEnd() ?>
	</div>
	
	<script type="text/javascript" src="<?= SITE_IN ?>/jscripts/jquery.rateyo.js"></script>
	<link rel="stylesheet" href="<?= SITE_IN ?>/styles/jquery.rateyo.min.css"/>
	<script>
		$(function () {
			$("#rateyo").rateYo({rating: $("#thresholdRating").val(), ratedFill: "#afc323"}).on("rateyo.change", function (e, data) {
				$("#thresholdRating").val(data.rating);
			});
		});
	</script>
	
	<div class="kt-portlet" style="background:#fff;border:1px solid #ebedf2; " class="mb-5 mt-3">
	
		<div class="hide_show">
			<div class="shipper_detail">
				<?= formBoxStart("Customer Review Plugin") ?>
			</div>
		</div>
		
		<div class="kt-portlet__body" style="padding-left:20px;padding-right:20px;">
			<div class="kt-section__info">Please notify from email if star rating for an order less than or Equals</div>
			<div class="row">
			
				<div class="col-6">
					<div class="form-group">
						@reviewNotificationEmail@
					</div>
				</div>
				
				<div class="col-6">
					<div class="form-group">
						<label>&nbsp;</label>
						<div id="rateyo" class="rateyo"></div>
						@thresholdRating@
					</div>
				</div>
			</div>
		</div>
		<?= formBoxEnd() ?>
	</div>
	<!----
	<div class="kt-portlet" style="background:#fff;border:1px solid #ebedf2;" class="mb-5 mt-3">
	
		<div class="hide_show">
			<div class="shipper_detail">
				<?= formBoxStart("Daily Dispatch Report Settings Plugin") ?>
			</div>
		</div>
		
		<div class="kt-portlet__body"  style="padding-left:20px;padding-right:20px;">
			<div class="kt-section__info">Daily dispatch report daily settings</div>
			<div class="row">
				<div class="col-6">
					<div class="form-group">
						Notify After : <strong>1 hour</strong>
					</div>
				</div>
				<div class="col-6">
					Send Email To : <strong>This example@example.example</strong>
				</div>
			</div>
		</div>
		<?= formBoxEnd() ?>
	</div>----->
	

    <!----
	<div class="kt-portlet" style="background:#fff;border:1px solid #ebedf2;" class="mb-5 mt-3">
	
		<div class="hide_show">
			<div class="shipper_detail">
				<?= formBoxStart("QuickBooks Desktop Integration") ?>
			</div>
		</div>
		
		<div class="kt-portlet__body" style="padding-left:20px;padding-right:20px;">
			<div class="kt-section__info">Please enter your QuickBooks web-connector ID below to sync FreightDragon with QuickBooks. If you would like to see a video on how to setup your web-connector please <a href="http://www.yahoo.com" target="_blank"><strong>click here</strong></a></div>
			<div class="from-group">
				@quickbooks_id@
			</div>
		</div>
		
		<?= formBoxEnd() ?>
	</div>---->
	
	
	<div class="kt-portlet" style="background:#fff;border:1px solid #ebedf2;" class="mb-5 mt-3">
	
		<div class="hide_show">
			<div class="shipper_detail">
				<?= formBoxStart("Credit Card Gateway") ?>
			</div>
		</div>
		
		<div class="kt-portlet__body"  style="padding-left:20px;padding-right:20px;">
			<div id="payment_tabs-custom_new" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
				<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
					<li id="paypal_tab" class="api-tabs ui-state-default ui-corner-top ui-tabs-selected ui-state-active">
						<a href="javascript:void();">PayPal</a>
					</li>
					<li id="authorize_tab" class="api-tabs ui-state-default ui-corner-top ui-tabs-selected">
						<a href="javascript:void();">Authorize.net</a>
					</li>
					<li id="mdsip_tab" class="api-tabs ui-state-default ui-corner-top ui-tabs-selected">
						<a href="javascript:void();">Mdsip</a>
					</li>
					<li id="easy_tab" class="api-tabs ui-state-default ui-corner-top ui-tabs-selected">
						<a href="javascript:void();">Easy Pay</a>
					</li>
				</ul>
				
				<div id="paypal_tab_info" class="custom-tabs ui-tabs-panel ui-widget-content ui-corner-bottom">
					
					<div class="kt-section__info">Log in to <strong>PayPal</strong>, then click Profile under My Account. Click My selling tools. Click API Access. Click Request API Credentials. Check Request API signature and click Agree and</div>
						
					<div class="row m_top_20">
					
						<div class="col-4">
							<div class="form-group">
								@paypal_api_username@
							</div>
						</div>
						
						<div class="col-4">
							<div class="form-group">
								@paypal_api_password@
							</div>
						</div>
						
						<div class="col-4">
							<div class="form-group">
								@paypal_api_signature@
							</div>
						</div>
						
					</div>
					
					<div class="text-right">
						<img src="<?= SITE_IN ?>images/icons/paypal_logo.png" width="75" height="21" alt="PayPal" />
					</div>
				</div>
				
				<div id="authorize_tab_info" class="custom-tabs ui-tabs-panel ui-widget-content ui-corner-bottom">
					
					<div class="kt-section__info">
						Please enter your <strong>Authorize.net</strong> information below. You can obtain this information from your Authorize.net administration account by clicking on "Settings" and then "API Login ID and Transaction Key".
					</div>
					<div class="row m_top_20">
						<div class="col-6">
							<div class="form-group">
								@anet_api_login_id@
							</div>
						</div>
						<div class="col-6">
							<div class="form-group">
								@anet_trans_key@
							</div>
						</div>
					</div>
					<div class="text-right">
						<img src="<?= SITE_IN ?>images/icons/anet_logo.png" width="102" height="16" alt="Authorize.net" />
					</div>
					
				</div>
				
				<div id="mdsip_tab_info" class="custom-tabs ui-tabs-panel ui-widget-content ui-corner-bottom">
					<div class="kt-section__info">
						Log in to <strong>Mdsip</strong>, then click Profile under My Account. Click My selling tools. Click API Access. Click Request API Credentials. Check Request API signature and click Agree and
					</div>
					
					<div class="row m_top_20">
						<div class="col-4">
							<div class="form-group">
								@gateway_api_username@
							</div>
						</div>
						
						<div class="col-4">
							<div class="form-group">
								@gateway_api_password@
							</div>
						</div>
						
						<div class="col-4">
							<div class="form-group">
								@gateway_api_signature@
							</div>
						</div>
					</div>
					
				</div>

				<div id="easy_tab_info" class="custom-tabs ui-tabs-panel ui-widget-content ui-corner-bottom">
					<div class="kt-section__info">
						Login in your EasyPay account and create API key
					</div>
					
					<div class="row m_top_20">
						<div class="col-4">
							<div class="form-group">
								@easy_pay_key@
							</div>
						</div>
					</div>
					
				</div>
			</div>
				

			<div class="row m_top_20">
				<div class="col-6">
					<div class="form-group select_wdh_100_per input_wdh_100_per">
						@current_gateway@
					</div>
				</div>
				<div class="col-6">
					<div class="form-group ">
						@notify_email@
					</div>
				</div>
			</div>
				
		</div>
		<?= formBoxEnd() ?>
	</div>
	
	<div class="kt-portlet" style="background:#fff;border:1px solid #ebedf2;" class="mb-5 mt-3">
		<div class="hide_show">
			<div class="shipper_detail">
				<?= formBoxStart("Credit Card Processing System") ?>
			</div>
		</div>
		<div class="kt-portlet__body" style="padding-left:20px;padding-right:20px;">
		    <div class="kt-section__info">If you would like to activate the FreightDragon credit card processing system please follow the easy to use tool below to set it up to your preference:</div>
		    <br><br>
			<div class="row">
			
				<div class="col-12 col-sm-3">
				    <div class="form-group">
						<!---<label><span>Default Dispatch Terms</span></label>---><?= formBoxStart("Step 1") ?>
					</div>
					<div class="kt-section__info">Do you want to active the payment system?</div>
					<br/>
					<div class="form-group">
						@card_batch_payment@
					</div>
				</div>
	             &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  
				<div class="col-12 col-sm-3">
				    <div class="form-group">
						<!---<label><span>Default Dispatch Terms</span></label>---><?= formBoxStart("Step 2") ?>
					</div>
					<div class="kt-section__info">Do you require contract before processing?</div>
					<br/>
					<div class="form-group">
						@card_payment_esigned@
					</div>
				</div>
                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
				
				<div class="col-12 col-sm-5">
				    <div class="form-group">
						<!---<label><span>Default Dispatch Terms</span></label>---><?= formBoxStart("Step 3") ?>
					</div>
					<div class="kt-section__info">Please select the condition when you would like the credit card to be processed:</div>
					<br/>
					<div class="form-group">
					<table cellspacing="5" cellpadding="5" border="0">
                    <tr><td>@card_batch@</td></tr>
                    </table>
					</div>
				</div>
			</div>
			</div>
		<?= formBoxEnd() ?>
	</div>
	
	<div class="kt-portlet" style="background:#fff;border:1px solid #ebedf2;" class="mb-5 mt-3">
	
		<div class="hide_show">
			<div class="shipper_detail">
				<?= formBoxStart("Payment Notification System") ?>
			</div>
		</div>
		<div class="kt-portlet__body"  style="padding-left:20px;padding-right:20px;">
        <div class="kt-section__info">Please select the condition when you would like the credit card to be processed:</div>
			<div class="row m_top_20">
				<div class="col-6">
					<div class="form-group">
					<label>Activated when order is:</label>
					<table cellspacing="5" cellpadding="5" border="0">
                    <tr><td>&nbsp;@mark_posted@</td></tr>
                    <tr><td>&nbsp;@mark_dispatched@</td></tr>
                    <tr><td>&nbsp;@mark_pickedup@</td></tr>
                    <tr><td>&nbsp;@mark_deivered@</td></tr>
                    </table>
					</div>
				</div>
				<div class="col-6">
					<div class="form-group">
					    <label>Send notification to</label>
					<table cellspacing="5" cellpadding="5" border="0">
                    <tr><td>&nbsp;@send_customer@</td></tr>
                    <tr><td>&nbsp;@send_admin@</td></tr>
                    </table>
					</div>
				</div>
			</div>
				
		</div>
		<?= formBoxEnd() ?>
	</div>
	<div class="kt-portlet" style="background:#fff;border:1px solid #ebedf2;">
	
		<div class="hide_show">
			<div class="shipper_detail">
				<?= formBoxStart("Order Terms") ?>
			</div>
		</div>
		
		<div class="kt-portlet__body" style="padding-left:20px;padding-right:20px;" >
			<div class="kt-section__info m_btm_10">Please enter the contract customers will be required to e-Sign order forms:</div>
			<br/>
			<? include("setting_menu.php"); ?>
			<div>
				<div></div>
				<div id="tab1">
					@order_terms@
				</div>
				<div style="display:none;" id="tab2">                       
					<div style="text-align: left;padding:10px; font-weight: bold; font-size: 16px;">
						Latest version updated at: <?php echo $this->data['commercialTermsUpdatedAt']; ?> by <?php echo $this->data['commercialTermsUpdatedBy']; ?>
					</div>                       
					@commercial_terms@
				</div>
			</div>
		</div>

		<?= formBoxEnd() ?>
	</div>
	
	
		<div class="kt-portlet" style="background:#fff;border:1px solid #ebedf2;">
	
		<div class="hide_show">
			<div class="shipper_detail">
				<?= formBoxStart("Dispatch Terms") ?>
			</div>
		</div>
		
		<div class="kt-portlet__body" style="padding-left:20px;padding-right:20px; ">
			<div class="kt-section__info m_btm_10">Please enter the contract Carriers will be required to e-Sign dispatch sheets:</div>
			<br/>
			@dispatch_terms@
		</div>
		
		
		<?= formBoxEnd() ?>
	</div>
	<br>
	<div class="text-right">
		<?php echo submitButtons(getLink("defaultsettings"), "Save"); ?>
	</div>
	<br/><br/><br/>
	    
</form>

<script type="text/javascript">//<![CDATA[
    $(function () {
        $("#lead_start_number").mask("9?9999999999", {placeholder: ' '});
        $("#logout_h").mask("9?9", {placeholder: ' '});
        $("#logout_m").mask("9?9", {placeholder: ' '});
        $("#first_quote_followup").mask("9?99", {placeholder: ' '});
        $("#mark_as_expired").mask("9?99", {placeholder: ' '});
        $("#mark_assumed_delivered").mask("9?99", {placeholder: ' '});
        $(".batches").mask("9?99", {placeholder: ' '});
        $("#tabs").tabs();
    });

    resid_comm_tabs(1);

    function resid_comm_tabs(val)
    {

        if (val == 1) {
            $("#tab1").show();
            $("#tab2").hide();
            $('#resi').addClass(' first active');
            //$('#resi').removeClass('active').addClass('first');
            $('#comm').removeClass(' last active')

        } else {
            $("#tab1").hide();
            $("#tab2").show();
            $('#comm').addClass(' last active');
            $('#resi').removeClass(' first active');
        }

    }
    //]]></script>