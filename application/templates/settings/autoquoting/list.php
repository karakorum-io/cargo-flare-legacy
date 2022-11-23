<? include(TPL_PATH . "settings/menu.php"); ?>

<div class="row m_btm_10">
	<div class="col-9 m_btm_10">
		<strong>Below is a list of quoting seasons. Click on a season to view or edit details. To change your automated quoting settings, click the button below.</strong>
	</div>
	<div class="col-3 text-right m_btm_10">
		<img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/add.gif" alt="Add" width="16" height="16" /> &nbsp;
		<a href="<?= getLink("autoquoting", "editseason") ?>">Add New Season</a>
		<img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/build.png" alt="AC Settings" width="16" height="16" /> &nbsp;
		<a href="<?= getLink("autoquoting", "settings") ?>">AQ Settings</a>
	</div>
</div>


<div class="kt-portlet">
	
	<div class="kt-portlet__body">
		
		<table id="Below" class="table table-bordered">
			<thead>
				<tr>
					<th><?= $this->order->getTitle("name", "Name") ?></th>
					<th>Lanes</th>
					<th><?= $this->order->getTitle("start_date", "Starts") ?></th>
					<th><?= $this->order->getTitle("end_date", "Ends") ?></th>
					<th>Status</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
			<? if (count($this->data) > 0) { ?>
				<? foreach ($this->data as $i => $season) { ?>
					<tr id="row-<?= $season->id ?>">
						<td><?= htmlspecialchars($season->name); ?></td>
						<td><?= $season->getLanesCount() ?></td>
						<td><?= $season->getStartDate() ?></td>
						<td><?= $season->getEndDate() ?></td>
						<td><?= statusText(getLink("autoquoting", "statusseason", "id", $season->id), $season->status) ?></td>

						<td>
						 <div class="row">
							<div class="col-3">
								<?= infoIcon(getLink("autoquoting", "lanes", "sid", $season->id)) ?>
							</div>
							<div class="col-3">
								<?= editIcon(getLink("autoquoting", "editseason", "id", $season->id)) ?>
							</div>
							<div class="col-3">
								<?= copyIcon(getLink("autoquoting", "copyseason", "id", $season->id)) ?>
							</div>
							<div class="col-3">
								<?= deleteIcon(getLink("autoquoting", "deleteseason", "id", $season->id), "row-" . $season->id) ?>
							</div>
						 </div>
					 </td>
					  
					</tr>
				<? } ?>
				<? } else { ?>
				<tr id="row">
					<td align="center" colspan="8">No records found.</td>
				</tr>
			<? } ?>
			</tbody>
		</table>

		<div class="col-12">
			@pager@
		</div>
		
	</div>
	
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('#Below').DataTable({
			"lengthChange": false,
			"paging": false,
			"bInfo" : false,
			'drawCallback': function (oSettings) {
				$("#Below_wrapper").children('.row:first').children('.col-sm-12:first').html('<div class="form-group"><label class="col-form-label pull-left">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
				$("#Below_wrapper").children('.row:nth-child(3)').children('.col-sm-12:first').html($('.pager').clone());
				$('.pages_div').remove();
			}
		});
	});
</script>
