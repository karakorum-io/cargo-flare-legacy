<style>
h3.details
{
    padding:22px 0 0;
    width:100%;
    font-size:20px;
}
</style>

<?php include('lead_menu.php');  ?>

<script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script>
<link href="<?php echo SITE_IN; ?>styles/new/assets/vendors/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
<script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/custom/datatables/datatables.bundle.js" type="text/javascript"></script>

<div style="margin-top:-55px;margin-bottom:15px;">
	<h3 class="details">Lead #<?= $this->entity->getNumber() ?>History</h3>
</div>

	<div class="col-12">
		<table id="center_hitory" class="table table-bordered" style="margin-bottom: 20px;margin-top: 20px" >
			<thead>
				<tr>
					<th><?= $this->order->getTitle("field_name", "Field Name") ?></th>
					<th>Old Value</th>
					<th>New Value</th>
					<th><?= $this->order->getTitle("change_date", "Date") ?></th>
					<th><?= $this->order->getTitle("changed_by", "Changed By") ?></th>
				</tr>
			</thead>
			<tbody>
			<?php if (count($this->history) == 0) : ?>
				<tr>
					<td colspan="5" align="center"><i>No records</i></td>
				</tr>
			<?php else : ?>
			<?php foreach ($this->history as $record) : ?>
				<tr>
					<td><?= $record->field_name ?></td>
					<td><?= $record->old_value ?></td>
					<td><strong><?= $record->new_value ?></strong></td>
					<td><?= $record->getDate() ?></td>
					<td><?= $record->getChangedBy() ?></td>
				</tr>
			<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>

	<div class="col-12">@pager@</div>

</div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
   $('#center_hitory').DataTable({
       "lengthChange": false,
       "paging": false,
       "bInfo" : false,
       'drawCallback': function (oSettings) {
           $('#center_hitory_wrapper').children('.row:first').children('.col-md-6:first').html('<div class="form-group"><label class="col-form-label pull-left">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
           $('#center_hitory_wrapper').children('.row:last').children('.col-md-5').html($('.pager').clone()).addClass('text-left');
           $('#center_hitory_wrapper').children('.row:last').children('.col-md-7').html($('.table_b ').html()).addClass('text-right');
           $('.pages-div-custom').remove();
           
      }
   });
} );
</script>
<script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery-ui.js"></script>