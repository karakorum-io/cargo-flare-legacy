<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?=getLink("forms", "contactus")?>">&nbsp;Back to the list</a>
</div>
<?=formBoxStart()?>
<table cellpadding="0" cellspacing="5" border="0">
	<tr>
		<td>Date:</td>
		<td>@reg_date_show@</td>
	</tr>
	<tr>
		<td>Company name:</td>
		<td>@contactname@</td>
	</tr>
	<tr>
		<td>Activity:</td>
		<td>@activity@</td>
	</tr>
	<tr>
		<td>Site URL:</td>
		<td>@url@</td>
	</tr>
    <tr>
		<td>Name:</td>
		<td>@contactname@</td>
	</tr>
	<tr>
		<td>Phone:</td>
		<td>@phone@</td>
	</tr>
	<tr>
		<td>E-mail:</td>
		<td>@email@</td>
	</tr>
</table>
<?=formBoxEnd()?>
<br /><?=backButton(getLink("forms/contactus"))?>