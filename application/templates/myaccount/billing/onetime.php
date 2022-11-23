<? include(TPL_PATH."myaccount/menu.php");?>
<div align="left" style="clear:both; padding-bottom:20px;">
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?=getLink("billing")?>">&nbsp;Back to the My Billing</a>
</div>

<div class="kt-portlet">
		
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<?=formBoxStart("One time payment")?>
		</div>
	</div>
	
	<div class="kt-portlet__body">
		
		<form action="<?=getLink("billing", "onetime")?>" method="post">
			<div class="kt-section__info m_btm_10">To make a payment, complete the form below and click 'Make Payment'.</div>
			<div class="row">
			
				<div class="col-2">
					<div class="form-group select_opt_new_info">
						<label>Balance</label>
						<strong>$@current_balance@</strong>
					</div>
				</div>
				
				<div class="col-5">
					<div class="form-group select_opt_new_info">
						@amount@
					</div>
				</div>
				
				<div class="col-5">
					<div class="form-group select_opt_new_info">
						@cc_id@
					</div>
				</div>
								
			</div>
			
			<div class="text-right m_btm_10">
				<?if ($this->pgw == 1){?>
					<img src="<?=SITE_IN?>images/icons/paypal_logo.png" width="75" height="21" alt="PayPal" />
				<?}?>
				<?if ($this->pgw == 2){?>
					<img src="<?=SITE_IN?>images/icons/anet_logo.png" width="102" height="16" alt="Authorize.net" />
				<?}?>
			</div>
			
			<div class="text-right m_top_20">
				<?php echo submitButtons(getLink("billing"), "Make payment"); ?>
			</div>
			
		</form>
		
	</div>
	
	<?=formBoxEnd()?>
	
</div>