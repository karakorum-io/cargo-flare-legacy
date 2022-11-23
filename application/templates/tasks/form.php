<form action="<?=getLink("tasks", "edit", "id", get_var("id"))?>" method="post">
    <?=formBoxStart("")?>
		<table cellpadding="0" cellspacing="5" border="0">
			<tr>
				<td>@taskdate@</td>
			</tr>
			<tr>
				<td>@task@</td>
			</tr>
			<tr>
				<td>@member_id@</td>
			</tr>
		</table>
    <?=formBoxEnd()?>
    <br />
    <?=submitButtons(getLink("tasks"))?>
</form>