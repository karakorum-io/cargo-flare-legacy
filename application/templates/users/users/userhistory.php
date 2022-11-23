<? include(TPL_PATH."users/menu_details.php"); ?>

<div class="row">
<div class="col-12">
<table id="user_hit" class="table table-bordered" >
	<thead>
    <tr >
        <th class="grid-head-left"><?=$this->order->getTitle("commonname", "Field Name")?></th>
        <th>Old Value</th>
        <th>New Value</th>
        <th><?=$this->order->getTitle("change_date", "Date")?></th>
        <th class="grid-head-right"><?=$this->order->getTitle("changed_by", "Changed By")?></th>
    </tr>
    </thead>
    <tbody>
    <? if (count($this->data)>0){?>
	    <? foreach ($this->data as $i => $data) { ?>
	    <tr class="grid-body<?=($i == 0 ? " " : "")?>" id="row-<?=$data['id']?>">
	        <td class="grid-body-left"><?=$data["field_name"]?></td>
	        <td><?=htmlspecialchars($data["old_value"])?></td>
	        <td><?=htmlspecialchars($data["new_value"])?></td>
	        <td align="center"><?=$data["change_date"]?></td>
	        <td align="center" class="grid-body-right"><?=htmlspecialchars($data["changed_by"])?></td>
	    </tr>
	    </tbody>
	    <? } ?>
    <? }else{ ?>
    	<tr class="grid-body " id="row-1">
	        <td colspan="4" class="grid-body-left">Records not found.</td>
	        <td class="grid-body-right">&nbsp;</td>
	    </tr>
    <? } ?>
</table>
</div>
</div>
@pager@
<br />


<?=backButton(getLink("users"))?>

<script type="text/javascript">
$(document).ready(function() {
$('#user_hit').DataTable({
"lengthChange": false,
"paging": false,
"bInfo" : false,
'drawCallback': function (oSettings) {

$("#user_hit_wrapper").children('.row:first').children('.col-sm-12:first').html('<div class="form-group row"><label class="col-form-label">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
$("#user_hit_wrapper").children('.row:nth-child(3)').children('.col-sm-12:first').html($('.pager').clone());
$('.pages_div').remove();


}
});
} );
</script>
