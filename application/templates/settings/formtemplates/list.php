</br>
<? include(TPL_PATH."settings/menu.php"); ?>

<div align="left" style="clear:both; padding-bottom:20px;">
	Below is a list of form Templates. Click on a pencil edit a template. To view a preview your form, click the envelope button.
	<div class="pull-right">
		<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/add.gif" alt="Add" width="16" height="16" /> <a href="<?=getLink("formtemplates", "edit")?>">Add New Template</a>
	</div>
</div>

<div class="kt-portlet">

	<div class="kt-portlet__body">
		
		<table id="from_templete" class="table table-bordered w-96">
			<thead>
				<tr>
					<th><?=$this->order->getTitle("name", "Name")?></th>
					<th><?=$this->order->getTitle("description", "Description")?></th>
					<th><?=$this->order->getTitle("usedfor", "Used for")?></th>
					<th >Actions</th>
				</tr>
			</thead>
			<tbody>
			<? if (count($this->data)>0){?>
				<? foreach ($this->data as $i => $data) { ?>
				<tr id="row-<?=$data['id']?>">
					<td><?=htmlspecialchars($data['name']);?></td>
					<td><?=htmlspecialchars($data['description'])?></td>
					<td><?=htmlspecialchars($data['usedfor'])?></td>
					<td >
						<div class="row">
							<div class="col-4">
								<?=editIcon(getLink("formtemplates", "edit", "id", $data['id']))?>
							</div>

							<div class="col-4">
								<?=previewIcon(getLink("formtemplates", "show", "id", $data['id']))?>
							</div>

							<div class="col-4">
								<? if ($data['is_system'] != 1){?>
								<?=deleteIcon(getLink("formtemplates", "delete", "id", $data['id']), "row-".$data['id'])?>
								<? } else { ?>
								 -
								<? } ?>

							</div>
						</div>

					</td>
				 
				</tr>
				<? } ?>
			<?}else{?>
				<tr id="row-">
					<td>&nbsp;</td>
					<td align="center" colspan="4">No records found.</td>
					<td>&nbsp;</td>
				</tr>
			<? } ?>
			</tbody>
		</table>
	</div>
</div>

<div class="col-12">
	@pager@
</div>


<script type="text/javascript">
    $(document).ready(function() {
    $('#from_templete').DataTable({
    "lengthChange": false,
    "paging": false,
    "bInfo" : false,
    'drawCallback': function (oSettings) {
		$("#from_templete_wrapper").children('.row:first').children('.col-sm-12:first').html('<div class="form-group"><label class="col-form-label pull-left">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
		$("#from_templete_wrapper").children('.row:nth-child(3)').children('.col-sm-12:first').html($('.pager').clone());
        $('.pages_div').remove();
        $("#from_templete_wrapper").find('form-group row').css("margin-left", "1px");
    }
    });
});
</script>