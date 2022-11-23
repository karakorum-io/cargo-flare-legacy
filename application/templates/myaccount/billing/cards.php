<table class="table table-bordered">
	<thead>
		<tr>
			<th>Number</th>
			<th colspan="2">Actions</th>
		</tr>
	</thead>
	<tbody>
	<? if (isset($this->cards) && count($this->cards)>0){?>
		<? foreach ($this->cards as $i => $t) { ?>
			<tr id="row-<?=$t['id']?>">
				<td><?=$t["cc_number"]?></td>
				<td><?=editClickIcon("editcreditcard('".$t['id']."');");?></td>
		        <td><?=deleteIcon(getLink("billing", "deletecc", "id", $t['id']), "row-".$t['id'], "Delete", "updatecc")?></td>
			</tr>
		<? } ?>
	<? }else{ ?>
		<tr class="grid-body first-row" id="row-1">
			<td align="center" colspan="3">Records not found.</td>
		</tr>
	<? } ?>
	</tbody>
</table>