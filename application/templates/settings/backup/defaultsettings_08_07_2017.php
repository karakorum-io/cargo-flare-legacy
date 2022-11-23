</br>
<? include_once("menu.php"); ?>
</br>
<div style="float:left; width:960px; padding-right:10px;">
<div style="text-align: right;">
	<img src="<?=SITE_IN?>images/icons/billing.png" alt="Billing" width="16" height="16" style="vertical-align:middle;" />&nbsp;<a href="<?=getLink("billing")?>">Billing</a> &nbsp;&nbsp;&nbsp;
	<img src="<?=SITE_IN?>images/icons/rating.png" alt="Rating" width="16" height="16" style="vertical-align:middle;" />&nbsp;<a href="<?=getLink("ratings")?>">Ratings</a> &nbsp;&nbsp;&nbsp;
	<img src="<?=SITE_IN?>images/icons/attach.png" alt="Documents" width="16" height="16" style="vertical-align:middle;" />&nbsp;<a href="<?=getLink("documents")?>">Documents</a> &nbsp;&nbsp;&nbsp;
	<!---<img src="<?=SITE_IN?>images/icons/contract.png" alt="Contract" width="16" height="16" style="vertical-align:middle;" />&nbsp;<a href="<?=getLink("companyprofile", "contract")?>">Contract</a> &nbsp;&nbsp;&nbsp;--->
	<img src="<?=SITE_IN?>images/icons/earn.png" alt="Referral" width="16" height="16" style="vertical-align:middle;" />&nbsp;<a href="<?=getLink("freemonth")?>">Referral</a>
</div>
</div>
</br>
</br>
</br>
<form action="<?= getLink("defaultsettings") ?>" method="post">
    <div style="float:left; width:480px; padding-right:10px;">
    
        <?= formBoxStart("Default Settings") ?>
        
        <table cellspacing="5" cellpadding="5" border="0">
            <tr><td colspan="2"><tr>
                            <tr>
                            <tr><em>Enter new value and click the <strong>"Update Default Values"</strong> button.</em></td></tr>
            <tr><td>@lead_start_number@</td></tr>
            <tr><td>@order_deposit@ @order_deposit_type@</td></tr>
            <tr><td>@first_quote_followup@ days since quoted</td></tr>
            <tr><td>@mark_as_expired@ days since received / quoted / ordered</td></tr>
            <tr><td>@mark_assumed_delivered@ days since est. delivery</td></tr>
            <?/*<tr><td>-@assign_unverified_orders_id@</td></tr>*/?>
            <tr><td>@logout_h@ hrs. @logout_m@ min.</td></tr>
			<!-- <tr><td>@payments_terms@</td></tr>   -->
			<tr><td>@payments_terms_cod@</td></tr>
			<tr><td>@payments_terms_cop@</td></tr>
			<tr><td>@payments_terms_billing@</td></tr>
			<tr><td>@payments_terms_invoice@</td></tr>
            <tr><td>@zoom_level@</td></tr>
            <tr><td></td></tr>

            <?/*<tr><td>-@carrier_pmt_terms_id@</td></tr>
            <tr><td>-@carrier_pmt_terms_begin_id@</td></tr>
            <tr><td>-@carrier_pmt_method_id@</td></tr>*/?>
            <?/*<tr><td colspan="2">-@show_vehicle_pricing@</td></tr>*/?>
            <?/*<tr><td colspan="2">-@show_new_order@</td></tr>*/?>
            <?/*<tr><td colspan="2">-@allow_replace_cod@</td></tr>*/?>
            <tr><td colspan="2">@email_blind@</td></tr>
            <tr>
            <tr>
                            <tr>
                            <tr>
                <td colspan="2">
                
                    <em>Enter email address(es) which should receive a copy of all outgoing email. Separate multiple addresses with commas.</em>
                <tr>
                <tr>
                <tr>
                <tr>
                </td>
            </tr>
			<tr>
				<td colspan="2">@hide_orders@</td>
			</tr>
        </table>
        <?= formBoxEnd() ?>
       
        
        <br />
		<?= formBoxStart("CentralDispatch Integration") ?>
        <table width="100%" cellspacing="5" cellpadding="5" border="0">  
            <em>
                                Please enter your <strong>UID</strong>
                                information below. You can obtain this information from
                                <strong>Centraldispatch.com</strong> by clicking on <strong>Ship Vehicles</strong>
                                > <strong>Import Vehicle</strong> to request the <strong>UID</strong> or contact <strong>CentralDispatch.com</strong> at 
                                (858) 259-6084 and request your <strong>UID</strong> from a customer service agent.
                            </em>
                            <tr>
                            <tr>
                            <tr>
            <tr><td width="130">@central_dispatch_uid@</td></tr>
            <tr><td>@central_dispatch_post@</td></tr>
            <tr>
                <td colspan="2" align="right">
                    <br />
                    <img src="<?= SITE_IN ?>images/icons/cd.png" width="131" height="21" alt="Central Dispatch" />
                </td>
            </tr>
        </table>
        <?= formBoxEnd() ?>
		</br>
		<?= formBoxStart("SMTP Settings") ?>
        <table cellspacing="5" cellpadding="5" border="0">
        <em>
                                Please enter the outgoing mailserver information to send emails such as quotes, orders and dispatch sheets from your email/server provider:
                            </em>
                           
            <tr><td>@smtp_server_name@</td></tr>
            <tr><td>@smtp_server_port@</td></tr>
            <!--tr><td>@smtp_use_ssl@</td></tr-->
            <tr><td colspan="3">
              <table width="100%" cellpadding="1" cellspacing="1">
                
               <tr><td>SMTP Encryption</td><td>@smtp_use_ssl@</td></tr>
               
              </table> 
            <tr><td>@smtp_user_name@</td></tr>
            <tr><td>@smtp_user_password@</td></tr>
            <tr><td>@smtp_from_email@</td></tr>
            
        </table>
        <?= formBoxEnd() ?>
    </div>
    <div style="float:left; width:480px;">
        
        <!--/*chetu added Code*/-->
        <?= formBoxStart("Customer Review Plugin") ?>
        <table cellspacing="5" cellpadding="5" border="0">
            <tr>
                <td colspan="2" align="left"> 
                    <em>Please notify from email if star rating for an order less than or Equals</em>
                </td>
            </tr>  
            <tr>
                <td colspan="2" align="center">
                    <div id="rateyo" class="rateyo"></div>
                    @thresholdRating@
                </td>
                <script type="text/javascript" src="/jscripts/jquery.rateyo.js"></script>
                <link rel="stylesheet" href="/styles/jquery.rateyo.min.css"/>
                <script>
                    $(function () {
                        $("#rateyo").rateYo({rating    :$("#thresholdRating").val(),ratedFill: "#419111"}).on("rateyo.change", function (e, data) {
                            $("#thresholdRating").val(data.rating);
                        });
                    }); 
                </script>
            </tr>             
            <tr><td>@reviewNotificationEmail@</td></tr>
        </table>
        <?= formBoxEnd() ?>
        <br>
        <!--/*chetu added Code Ends*/-->
        
        <?= formBoxStart("QuickBooks Desktop Integration") ?>
        <table cellspacing="5" cellpadding="5" border="0">
        <em>
                                Please enter your QuickBooks web-connector ID below to sync FreightDragon with QuickBooks. If you would like to see a video on how to setup your web-connector please <a href="http://www.yahoo.com" target="_blank"><strong>click here</strong></a>
                            </em>
							<tr>
                            <tr>
                            <tr>
            <tr><td>@quickbooks_id@</td></tr>
            
            
        </table>
        <?= formBoxEnd() ?>
	</br>
	<?= formBoxStart("Customer Portal") ?>
        <table cellspacing="5" cellpadding="5" border="0">
        <em>
                                This tool will help you generate the code required to have a portal for your customers to access important information like active orders, past orders and up to date invoices.</a>
                            </em>
							<tr>
                            <tr>
                            <tr>
            <tr><td>"Click Here Button"</td></tr>
            
            
        </table>
        <?= formBoxEnd() ?>
	</br>
	<?= formBoxStart("Credit Card Gateway") ?>
        <div id="tabs">
            <ul>
                <li><a href="#tabs-1">PayPal</a></li>
                <li><a href="#tabs-2">Authorize.net</a></li>
                <li><a href="#tabs-3">Mdsip</a></li>
            </ul>
            <div id="tabs-1">
                <table width="100%" cellspacing="5" cellpadding="5" border="0">
                    <tr>
                        <td colspan="2"><em>Log in to <strong>PayPal</strong>, then click Profile under My Account. Click My selling tools. Click API Access. Click Request API Credentials. Check Request API signature and click Agree and<tr>
                            <tr>
                            <tr> 
                            <tr>
                            <tr>
                        </td>
                    </tr>
                    <tr>
                        <td>@paypal_api_username@</td>
                    </tr>
                    <tr>
                        <td>@paypal_api_password@</td>
                    </tr>
                    <tr>
                        <td>@paypal_api_signature@</td>
                    </tr>
                    <tr>
                        <td colspan="2" align="right">
                            <br />
                            <img src="<?= SITE_IN ?>images/icons/paypal_logo.png" width="75" height="21" alt="PayPal" />
                        </td>
                    </tr>
                </table>
            </div>
            <div id="tabs-2">
                <table width="100%" cellspacing="5" cellpadding="5" border="0">
                    <tr>
                        <td colspan="2">
                            <em>
                                Please enter your <strong>Authorize.net</strong>
                                information below. You can obtain this information from
                                your Authorize.net administration account by clicking on "Settings"
                                and then "API Login ID and Transaction Key".
                            </em>  
                            <tr>
                            <tr>
                            <tr>                         
                        </td>
                    </tr>
                    <tr>
                        <td>@anet_api_login_id@</td>
                    </tr>
                    <tr>
                        <td>@anet_trans_key@</td>
                    </tr>
                    <tr>
                        <td colspan="2" align="right">
                            <img src="<?= SITE_IN ?>images/icons/anet_logo.png" width="102" height="16" alt="Authorize.net" />
                        </td>
                    </tr>
                </table>
            </div>
            <div id="tabs-3">
                <table width="100%" cellspacing="5" cellpadding="5" border="0">
                    <tr>
                        <td colspan="2"><em>Log in to <strong>Mdsip</strong>, then click Profile under My Account. Click My selling tools. Click API Access. Click Request API Credentials. Check Request API signature and click Agree and<tr>
                            <tr>
                            <tr> 
                            <tr>
                            <tr>
                        </td>
                    </tr>
                    <tr>
                        <td>@gateway_api_username@</td>
                    </tr>
                    <tr>
                        <td>@gateway_api_password@</td>
                    </tr>
                    <tr>
                        <td>@gateway_api_signature@</td>
                    </tr>
                    
                </table>
            </div>
        </div>
        <table width="100%" cellspacing="5" cellpadding="5" border="0">
            <tr><td>&nbsp;</td></tr> 
			<tr>
                <td width="130">@current_gateway@</td>
            </tr>
            <tr>
                <td>@notify_email@</td>
            </tr>
        </table>
        <?= formBoxEnd() ?>
		<br />
         <?= formBoxStart("Credit Card Auto Processing") ?>
        <table cellspacing="5" cellpadding="5" border="0">
        <em>
                                Below you can set conditions to when FreightDragon will process payment automatically.
                            </em>
				 <tr><td>&nbsp;</td></tr> 
            <tr><td>@card_batch_payment@</td></tr>
            <tr><td>
               <table cellspacing="5" cellpadding="5" border="0">
                 <tr><td>@card_batch@</td></tr>
               </table>
              </td></tr>
            <tr><td>@card_payment_esigned@</td></tr>
         
        </table>
        <?= formBoxEnd() ?>
         <br />
         <?= formBoxStart("Payment Notification Settings") ?>
        <table cellspacing="5" cellpadding="5" border="0">
        <em>
                                Please enter the outgoing mailserver information to send emails such as quotes, orders and dispatch sheets from your email/server provider:
                            </em>
				 <tr><td>&nbsp;</td></tr> 			
            <tr><td colspan="2">Activated when order is</td></tr>             
            <tr><td>@mark_posted@</td></tr>
            <tr><td>@mark_dispatched@</td></tr>
            <tr><td>@mark_pickedup@</td></tr>
            <tr><td>@mark_deivered@</td></tr>
            <tr><td colspan="2">Send notification to</td></tr>  
            <tr><td>@send_customer@</td></tr>
            <tr><td>@send_admin@</td></tr>
        </table>
        <?= formBoxEnd() ?>
        <br />

    </div>
	<div style="float:left; width:970px; padding-right:10px;">
	<?= formBoxStart("Order Terms") ?>
        <table cellspacing="5" cellpadding="5" border="0">
       
			<tr><td colspan="2" align="left"> <em>
                                Please enter the contract customers will be required to e-Sign order forms:
                            </em></td></tr>  
              <tr><td colspan="2">&nbsp;</tr>							
             <tr><td colspan="2" align="left">Enter order terms below</td></tr>             
            <!--tr><td>@order_terms@</td></tr-->
            <tr><td>
            
              <? include("setting_menu.php"); ?>
              
            <div>
              <div> </div>
              <div id="tab1">
                @order_terms@
              </div>
              <div style="display:none;" id="tab2">
                @commercial_terms@
              </div>
            </div>
               
            
            </td></tr>
            
        </table>
        <?= formBoxEnd() ?>	
<div style="clear:both;">&nbsp;</div>
<?= formBoxStart("Dispatch Terms") ?>
        <table cellspacing="5" cellpadding="5" border="0">
		   <tr><td colspan="2" align="left"> <em>
                                Please enter the contract Carriers will be required to e-Sign dispatch sheets:
                            </em></td></tr>  
              <tr><td colspan="2">&nbsp;</tr>							
             <tr><td colspan="2" align="left">Enter dispatch terms below</td></tr>             
            <tr><td>@dispatch_terms@</td></tr>
       
        </table>
<?= formBoxEnd() ?>
<div style="clear:both;">&nbsp;</div>
<div style="clear:both;">&nbsp;</div>
        <?php echo submitButtons(getLink("defaultsettings"), "Save"); ?>
</div>	
    <div style="clear:both;">&nbsp;</div>
</form>
<script type="text/javascript">//<![CDATA[
    $(function(){
        $("#lead_start_number").mask("9?9999999999",{placeholder:' '});
        $("#logout_h").mask("9?9",{placeholder:' '});
        $("#logout_m").mask("9?9",{placeholder:' '});
        $("#first_quote_followup").mask("9?99",{placeholder:' '});
        $("#mark_as_expired").mask("9?99",{placeholder:' '});
        $("#mark_assumed_delivered").mask("9?99",{placeholder:' '});
        $(".batches").mask("9?99",{placeholder:' '});
        $("#tabs").tabs();
    });
	
resid_comm_tabs(1);

function resid_comm_tabs(val)
{
   
		if (val==1){
			$("#tab1").show();
			$("#tab2").hide();
			$('#resi').addClass(' first active');
			//$('#resi').removeClass('active').addClass('first');
			$('#comm').removeClass(' last active')
			
		}else{
			$("#tab1").hide();
			$("#tab2").show();
			$('#comm').addClass(' last active');
			$('#resi').removeClass(' first active');
		}
       
}
    //]]></script>