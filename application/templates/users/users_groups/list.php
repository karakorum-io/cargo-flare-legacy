<? include(TPL_PATH."users/menu.php"); ?>
<div class="row">
    <div class="alert alert-light alert-elevate mt-4 " style="width: 100%">
        <div class="col-12">
            <div class="row mt-3 mb-3">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th><?=$this->order->getTitle("name", "Name")?></th>
                            <th><?=$this->order->getTitle("create_date", "Date Created")?></th>
                            <th><?=$this->order->getTitle("update_date", "Date Updated")?></th>
                            <th colspan="2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <? foreach ($this->data as $i => $data) { ?>
                            <tr id="row-<?=$data['id']?>">
                                <td><?=htmlspecialchars($data['name']);?></td>
                                <td><?=$data['create_date']?></td>
                                <td><?=$data['update_date']?></td>
                                <td><?=editIcon(getLink("users_groups", "edit", "id", $data['id']))?></td>
                                <td><?=deleteIcon(getLink("users_groups", "delete", "id", $data['id']), "row-".$data['id'])?></td>
                            </tr>
                        <? } ?>
                    </tbody>
                </table>
                @pager@
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('#users').DataTable({
			"lengthChange": false,
			"paging": false,
			"bInfo" : false,
			'drawCallback': function (oSettings) {
				$("#users_wrapper").children('.row:first').children('.col-sm-12:first').html('<div class="form-group row"><label class="col-form-label">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
				$("#users_wrapper").children('.row:nth-child(3)').children('.col-sm-12:first').html($('.pager').clone());
				$('.pages_div').remove();
			}
		});
	});
</script>