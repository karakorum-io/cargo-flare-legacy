@flash_message@
<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?=getLink("faq")?>">&nbsp;Back to the list</a>
</div>
<form action="<?=getLink("faq", "edit", "id", get_var("id"))?>" method="post">
    <?=formBoxStart()?>
    <div style="float: left; width: 650px;">
		<table cellpadding="0" cellspacing="5" border="0">
			<tr>
				<td>@question@</td>
			</tr>
			<tr>
				<td valign="top"><label for="answer"><span class="required">*</span>Ответ:</label></td>
				<td>@answer@</td>
			</tr>
		</table>
	</div>

    <?=formBoxEnd()?>
    <br />
    <?=submitButtons(getLink("faq"))?>
</form>