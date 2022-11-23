@flash_message@
<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?=getLink("sysmessages")?>">&nbsp;Back to the list</a>
</div>
<form action="<?=getLink("sysmessages", "edit", "id", get_var("id"))?>" method="post">
    <?=formBoxStart()?>
    <div style="float: left; width: 650px;">
		<table cellpadding="0" cellspacing="5" border="0">
			<tr>
				<td>@message@</td>
			</tr>
		</table>
	</div>
    <?=formBoxEnd()?>
    <br />
    <?=submitButtons(getLink("sysmessages"))?>
</form>