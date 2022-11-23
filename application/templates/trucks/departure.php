<script type="text/javascript">
	$(document).ready(function(){
		$("#date").datepicker({
			minDate: '+0',
			maxDate: '+14',
			dateFormat: 'mm/dd/yy'
		});
	});
</script>
<div style="padding-top:10px;">
<div style="clear: both"></div>
<h3>Departure Information</h3>
<p><strong>Truck: </strong><?= $this->truck->name ?></p>
<p><strong>Current Date/Time: </strong><?= gmdate("m/d/y h:i:s a", time()-28800) . " PST" ?></p>
<form method="post" action="<?= SITE_IN ?>application/trucks/departure/truck_id/<?= $_GET['truck_id'] ?>">
	<?=formBoxStart("")?>
	<?= (isset($_GET['id']))?'<input type="hidden" name="id" value="'.$_GET['id'].'"/>':'' ?>
	<?= (isset($_GET['truck_id']))?'<input type="hidden" name="truck_id" value="'.$_GET['truck_id'].'"/>':'' ?>
	<table cellpadding="0" cellspacing="0" border="0" class="form-table">
		<tr>
			<td colspan="2"><h3>Departing From</h3></td><td colspan="2"><h3>Final Destination</h3></td>
		</tr>
		<tr>
			<td>@from_city@</td><td>@final_city@</td>
		</tr>
		<tr>
			<td>@from_state@</td><td>@final_state@</td>
		</tr>
		<tr>
			<td>@from_country@</td><td>@final_country@</td>
		</tr>
		<tr>
			<td>@heading@</td><td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="4"><h3>Additional Information</h3></td>
		</tr>
		<tr>
			<td>@date@</td><td>@time@</td>
		</tr>
		<tr>
			<td>@spaces@</td>
		</tr>
	</table>
	<br/>
	<?=submitButtons(getLink("trucks"), "Save")?>
	<?=formBoxEnd()?>
</form>