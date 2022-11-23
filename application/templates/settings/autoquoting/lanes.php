</br>
<? include(TPL_PATH . "settings/menu.php"); ?>

<div class="row m_btm_10">
	<div class="col-6 m_btm_10">
		<img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/add.gif" alt="Add" width="16" height="16" /> &nbsp;<a href="<?= getLink("autoquoting", "editlane", "id", 0, "sid", (int) get_var("sid")); ?>">Add New Lane</a>
		&nbsp;&nbsp;&nbsp;
		<img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/import.png" alt="Import" width="16" height="16" /> &nbsp;<a href="<?= getLink("autoquoting", "import", "sid", (int) get_var("sid")); ?>">Import Lanes</a>
	</div>
	<div class="col-6 text-right m_btm_10">
		<img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/back.png" alt="Back" width="16" height="16" /> &nbsp;<a href="<?= getLink("autoquoting"); ?>">Back to the seasons list</a>
	</div>
</div>

<div style="text-align: left; clear:both; padding-bottom:5px; padding-top:5px;">
	<table width="100%" border="0">
		<tr>
			<td align="left">
				
			</td>
			<td align="right"></td>
		</tr>
	</table>
</div>


<div class="kt-portlet">
	
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h3>@season_name@ <span style="color:#444; font-size:12px; font-weight:normal">(@season_start_date@ - @season_end_date@)</span></h3>
		</div>
	</div>
	
	<div class="kt-portlet__body">
		
		<table id="normal_summer" class="table table-bordered">
			<thead>
				<tr>
					<th><?= $this->order->getTitle("name", "Lane") ?></th>
					<th>Base/CPM price</th>
					<th><?= $this->order->getTitle("modified", "Last Modified") ?></th>
					<th>Status</th>
					<th>Actions</th>
				</tr>
			</thead>
			<? if (count($this->data) > 0) { ?>
				<? foreach ($this->data as $i => $lane) {
					/** @var $lane AutoQuotingLane */
					?>
					<tr class="grid-body<?= ($i == 0 ? " " : "") ?>" id="row-<?= $lane->id ?>">
						<td class="grid-body-left"><?= htmlspecialchars($lane->name); ?></td>
						<td>$<?= $lane->getBaseOrCPMPrice() ?></td>
						<td><?= $lane->getModified() ?></td>
						<td><?= statusText(getLink("autoquoting", "statusseason", "id", $lane->id), $lane->status, "sid", (int) get_var("sid")) ?></td>


						<td style="width: 16px;">
						<div class="row">
						<div class="col-4">
						<?= editIcon(getLink("autoquoting", "editlane", "id", $lane->id, "sid", (int) get_var("sid"))) ?>
						</div>
						<div class="col-4">
						<?= copyIcon(getLink("autoquoting", "copylane", "id", $lane->id, "sid", (int) get_var("sid"))) ?>
						</div>
						<div class="col-4">
						<?= deleteIcon(getLink("autoquoting", "deletelane", "id", $lane->id, "sid", (int) get_var("sid")), "row-" . $lane->id) ?>
						</div>

						</div>
						</td>

					
					</tr>
				<? } ?>
			<? } else { ?>
				<tr class="grid-body " id="row-">
					<td align="center" colspan="7" class="grid-body-left grid-action-right"><i>No records found.</i></td>
				</tr>
			<? } ?>
		</table>

		@pager@
	
	</div>
</div>


    <script type="text/javascript">
        $(document).ready(function() {
        $('#normal_summer').DataTable({
        "lengthChange": false,
        "paging": false,
        "bInfo" : false,
        'drawCallback': function (oSettings) {

        $("#normal_summer_wrapper").children('.row:first').children('.col-sm-12:first').html('<div class="form-group"><label class="col-form-label pull-left">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
        $("#normal_summer_wrapper").children('.row:nth-child(3)').children('.col-sm-12:first').html($('.pager').clone());
        $('.pages_div').remove();


        }
        });
        } );
        </script>