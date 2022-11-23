</br>
<? include(TPL_PATH . "settings/menu.php"); ?>

<div align="right" style="clear:both; padding-bottom:15px;">
    <img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?= getLink("autoquoting") ?>">&nbsp;Back to the list</a>
</div>

<div class="kt-portlet">
	
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			 <h3>AQ Settings</h3>
		</div>
	</div>
	
	<div class="kt-portlet__body">

		<div class="row">
			<div class="col-12">
			
				<form action="<?= getLink("autoquoting", "settings") ?>" method="post">

					<div class="row mt-3">
						<div class="col-6">
							<?= formBoxStart("Automated Quoting Settings") ?>
							<div class="is_enabled ml-4">
								@is_enabled@
							</div>
							<?= formBoxEnd() ?>
						</div>
						
						<div class="col-6">
							<?= formBoxStart("Vehicles Quoted") ?>
							<table cellspacing="5" cellpadding="5" border="0">
								<tr>
									<td>Today:</td>
									<td class="totalv">@today@</td>
								</tr>
								<tr>
									<td>This month:</td>
									<td class="totalv">@this_month@</td>
								</tr>
								<tr>
									<td>Last month:</td>
									<td class="totalv">@last_month@</td>
								</tr>
							</table>
							<?= formBoxEnd() ?>
						</div>
					</div>
		  
				<?php echo submitButtons(getLink("autoquoting"), "Save"); ?>
				</form>
				
			</div>
		</div>
		
	</div>
	
</div>