<div class="action-links">
	<a href="<?php echo getLink("admins") ?>" class="back">Back to the list</a>
</div>
@flash_message@
<form action="<?php echo getLink("admins/edit/id", get_var("id")) ?>" method="post">
	<?php echo formBoxStart() ?>
	<table cellpadding="0" cellspacing="5" border="0" class="form-fields">
		<tr>
			<td>@first_name@</td>
			<td>@last_name@</td>
		</tr>
		<tr>
			<td>@email@</td>
			<td>@confirm_email@</td>
		</tr>
		<tr>
			<td>@password@</td>
			<td>@password_confirm@</td>
		</tr>
		<tr>
			<td colspan="2">@address@</td>
		</tr>
		<tr>
			<td>@city@</td>
			<td>@state@</td>
		</tr>
		<tr>
			<td>@zip@</td>
			<td>@cell_phone@</td>
		</tr>
		<tr>
			<td>@group_id@</td>
			<td>@status@</td>
		</tr>
		<tr>
			<td colspan="2">@IsCustomerService@</td>
		</tr>
		<!--
		<tr>
			<td>@email_notify@</td>
			<td></td>
		</tr>
		-->
	</table>
	<?php echo formBoxEnd() ?>
	<br>
	<?php echo submitButtons(getLink("admins")) ?>
</form>
