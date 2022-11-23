<div style="padding-top:10px;">
<div style="clear: both"></div>
<?= functionButton('Add a New Truck', "document.location.href = '".addslashes(getLink('trucks/edit'))."';") ?>
<br/>
@pager@
<table class="table table-bordered table-striped">
	<tr class="grid-head">
		<td class="grid-head-left">Truck Profile</td>
		<td>Departing From</td>
		<td>Departure Date/Time</td>
		<td>Destination</td>
		<td>Heading</td>
		<td>Open Sp.</td>
		<td class="grid-head-right">Actions</td>
	</tr>
	<?php if (count($this->trucks) == 0) : ?>
	<tr>
		<td colspan="7" align="center">You have no Trucks</td>
	</tr>
	<?php endif; ?>
	<?php foreach ($this->trucks as $truck) : ?>
	<tr>
		<td rowspan="<?= ((count($truck->getDepartures()))?count($truck->getDepartures()):1)+1 ?>" width="200">
			<p><strong>Truck Name: </strong><?= $truck->name ?></p>
			<p><strong>Trailer: </strong><?= Truck::$trailer_string[$truck->trailer] ?></p>
			<p><strong>Dispatch Phone: </strong><?= $truck->phone ?></p>
			<?= functionButton('Edit Truck Profile', "document.location.href = '".addslashes(getLink('trucks/edit/id/'.$truck->id))."';", "width: 100px;") ?>
			<?= functionButton('Delete Truck', "deleteItem('".getLink('trucks/delete/id/'.$truck->id)."', '', true);", "width: 100px;") ?>
		</td>
		<?php if (count($truck->getDepartures()) == 0) : ?>
		<td colspan="6"><i>This truck does not have any departures.</i></td>
		<?php endif; ?>
		<?php foreach ($truck->getDepartures() as $i => $departure) : ?>
		<td><?= $departure->getFrom() ?></td>
		<td><?= $departure->getDate() ?>&nbsp;<?= $departure->time ?></td>
		<td><?= $departure->getDestnation() ?></td>
		<td><?= $departure->getHeading() ?></td>
		<td><?= $departure->spaces ?></td>
		<td align="center" class="grid-body-right">
			<?= editIcon(getLink('trucks/departure/id/'.$departure->id.'/truck_id/'.$truck->id)) ?>
			<?= deleteIcon(getLink('trucks/departure/id/'.$departure->id.'/delete'), 'Delete', '', 'true') ?>
		</td>
		<?php if (($i != (count($truck->getDepartures()) - 1)) && (count($truck->getDepartures()) > 1)) : ?>
		</tr><tr class="grid-body">
		<?php endif; ?>
		<?php endforeach; ?>
	</tr>
	<tr>
		<td colspan="6" class="grid-body-right"><?= functionButton('Add a New Departure', "document.location.href = '".SITE_IN.'application/trucks/departure/truck_id/'.$truck->id."'") ?></td>
	</tr>
	<?php endforeach; ?>
</table>
@pager@
<br/>
<?= functionButton('Add a New Truck', "document.location.href = '".addslashes(getLink('trucks/edit'))."';") ?>
</div>