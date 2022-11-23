<? include(TPL_PATH."myaccount/menu.php");?>
<script type="text/javascript" src="<?=SITE_IN?>jscripts/jquery.ajaxupload.js"></script>
<form action="<?=getLink("companyprofile", "contract")?>" method="post">
	<?=formBoxStart("My Contract")?>
		<table cellspacing="5" cellpadding="5" border="0">
			<tr>
				<td colspan="2">
					<em>
						If you post vehicles for shipment and have a pre-existing dispatch contract that you would like to use with your FreightDragon dispatch sheets, you may copy and paste it below. Once you have added your contract, each carrier will be required to sign your contract at the same time they sign the dispatch sheet. <strong>Please Note: Modifying your contract will NOT modify it for any dispatches that have been previously signed by the carrier.</strong>
					</em>
				</td>
			</tr>
			<tr><td valign="top">@contract@</td></tr>
		</table>
	<?=formBoxEnd()?>
	<br />
	<?php echo submitButtons(getLink("companyprofile"), "Save"); ?>
</form>