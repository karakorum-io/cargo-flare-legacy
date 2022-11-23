<div style="padding-top:15px;">
<?php include('lead_menu.php');  ?>
</div>
<br/>
<h3>Lead #<?= $this->entity->getNumber() ?> History</h3>
@pager@
<table cellspacing="0" cellpadding="0" border="0" width="100%" class="grid">
	<thead>
		<tr class="grid-head">
			<td class="grid-head-left"><?= $this->order->getTitle("field_name", "Field Name") ?></td>
			<td>Old Value</td>
			<td>New Value</td>
			<td><?= $this->order->getTitle("change_date", "Date") ?></td>
			<td class="grid-head-right"><?= $this->order->getTitle("changed_by", "Changed By") ?></td>
		</tr>
	</thead>
	<tbody>
	<?php if (count($this->history) == 0) : ?>
		<tr class="grid-body">
			<td class="grid-body-left grid-body-right" colspan="5" align="center"><i>No records</i></td>
		</tr>
	<?php else : ?>
	<?php foreach ($this->history as $record) : ?>
		<tr class="grid-body">
			<td class="grid-body-left"><?= $record->field_name ?></td>
			<td><?= $record->old_value ?></td>
			<td><?= $record->new_value ?></td>
			<td align="center"><?= $record->getDate() ?></td>
			<td class="grid-body-right" align="center"><?= $record->getChangedBy() ?></td>
		</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
</table>
@pager@