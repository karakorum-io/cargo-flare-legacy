<script type="text/javascript">
	$(document).ready(function(){
		$("#date").datepicker({
			minDate: '+0',
			dateFormat: 'mm/dd/yy'
		});
		$(".see-matched").click(function(){
			$(this).next().toggle();
		});
	});
</script>
<div style="padding-top:10px;">
<div style="clear: both"></div>
<h3>Search Truck Space</h3>
<form method="post" action="<?= SITE_IN ?>application/trucks/search">
	<?=formBoxStart("")?>
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="form-table">
		<tr>
			<td>@carrier_name@</td>
			<td>@date@</td>
		</tr>
		<tr>
			<td>@carrier_id@</td>
			<td>@spaces@</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
			<td>@inops@</td>
		</tr>
		<tr>
			<td colspan="2"><h4 style="margin: 0;">Origin</h4></td>
			<td colspan="2"><h4 style="margin: 0;">Destination</h4></td>
		</tr>
		<tr>
			<td>@origin_city@</td>
			<td>@destination_city@</td>
		</tr>
		<tr>
			<td>@origin_state@</td>
			<td>@destination_state@</td>
		</tr>
		<tr>
			<td>@origin_country@</td>
			<td>@destination_country@</td>
		</tr>
		<tr>
			<td>@heading@</td>
			<td colspan="2">&nbsp;</td>
		</tr>
	</table>
	<br />
	<?=submitButtons(getLink("trucks"), "Search")?>
	<?=formBoxEnd()?>
</form>
<br/>
<table class="table table-bordered table-striped">
	<tr>
		<td>Origin</td>
		<td>Destination</td>
		<td><?= $this->order->getTitle('heading', 'Heading') ?></td>
		<td><?= $this->order->getTitle('departure_date', 'Departs') ?></td>
		<td><?= $this->order->getTitle('spaces', '# Spaces') ?></td>
		<td><?= $this->order->getTitle('trailer', 'Trailer Type') ?></td>
		<td><?= $this->order->getTitle('inops', 'Inops') ?></td>
		<td><?= $this->order->getTitle('company', 'Contact') ?></td>
		<td><?= $this->order->getTitle('matcher', 'Match') ?></td>
	</tr>
	<?php if (count($this->results) == 0) : ?>
	<tr>
		<td colspan="9" align="center" class="grid-body-left grid-body-right"><i>No trucks found.</i></td>
	</tr>
	<?php endif; ?>
	<?php foreach ($this->results as $i => $result) : ?>
	<tr id="row-<?=$result[0]['truck_id']?>">
		<td>
			<?= $result[0]['from_city'] ?>, <?= $result[0]['from_state'] ?>, <?= $result[0]['from_country'] ?><br />
			<?= mapLink($result[0]['from_city'].', '.$result[0]['from_state'].', '.$result[0]['from_country']) ?>
		</td>
		<td>
			<?= $result[0]['final_city'] ?>, <?= $result[0]['final_state'] ?>, <?= $result[0]['final_country'] ?><br />
			<?= mapLink($result[0]['final_city'].', '.$result[0]['final_state'].', '.$result[0]['final_country']) ?>
		</td>
		<td>
			<?= Departure::$directions[$result[0]['heading']] ?><br />
			<?= routeLink($result[0]['from_city'].', '.$result[0]['from_state'].', '.$result[0]['from_country'], $result[0]['final_city'].', '.$result[0]['final_state'].', '.$result[0]['final_country']) ?>
		</td>
		<td><?= date("m/d/Y", strtotime($result[0]['departure_date'])) ?> <?= $result[0]['departure_time'] ?></td>
		<td align="center"><?= $result[0]['spaces'] ?></td>
		<td align="center"><?= Truck::getTrailerType($result[0]['trailer']) ?></td>
		<td align="center"><?= ($result[0]['inops'] == 1)?'No':'Yes' ?></td>
		<td>
			<a href="<?=getLink("ratings/company/id/".$result[0]['company_id'])?>"><?= $result[0]['company'] ?></a><br/>
			<?= $result[0]['phone'] ?><br/>
			<a href="mailto:<?= $result[0]['email'] ?>"><?= $result[0]['email'] ?></a></td>
		<td class="" align="center">
			<?php if ($result[0]['matcher'] > 0) { ?>
			<?= $result[0]['matcher'] ?>%<br />
			<span class="like-link see-matched">See Matched Vehicles</span>
			<div class="box-toggler">
				<?php foreach ($result as $j => $order) : ?>
				<div <?= ($j%2)?'class="odd"':'' ?>>
					<p>Lead # <?= $order['order']->getNumber() ?></p>
					<?php foreach ($order['order']->getVehicles() as $vehicle) : ?>
					<div align="left"><?= $vehicle->year ?>&nbsp;<?= $vehicle->make ?>&nbsp;<?= $vehicle->model ?></div>
					<?php endforeach; ?>
				</div>
				<?php endforeach?>
			</div>
			<?php } else { echo 'No Match'; } ?>
		</td>
	</tr>
	<?php endforeach; ?>
</table>