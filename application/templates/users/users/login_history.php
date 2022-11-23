<? include(TPL_PATH."users/menu_details.php"); ?>


 <div class="alert alert-light alert-elevate  ">
 	<div class="row w-100">
 		<div class="col-12">
    <table id="login_history" class="table table-bordered">
	<thead>
    <tr class="">
        <th class="grid-head-left"><?=$this->order->getTitle("logintime", "Time")?></th>
        <th class="grid-head-right"><?=$this->order->getTitle("ip", "IP")?></th>
    </tr>
    </thead>
    <? if (count($this->data)>0){?>
	    <? foreach ($this->data as $i => $data) { ?>
	    <tr class="grid-body<?=($i == 0 ? " " : "")?>" id="row-<?=$data['id']?>">
	        <td class="grid-body-left"><?=$data["logintime"]?></td>
	        <td class="grid-body-right"><?=$data["ip"]?></td>
	    </tr>
	    <? } ?>
    <? }else{ ?>
    	<tr class="grid-body " id="row-1">
	        <td class="grid-body-left">Records not found.</td>
	        <td class="grid-body-right">&nbsp;</td>
	    </tr>
    <? } ?>
</table>
</div>
</div>
</div>
@pager@
<br />
<?=backButton(getLink("users"))?>

 <script type="text/javascript">
        $(document).ready(function() {
        $('#login_history').DataTable({
        "lengthChange": false,
        "paging": false,
        "bInfo" : false,
        'drawCallback': function (oSettings) {

        $("#login_history_wrapper").children('.row:first').children('.col-sm-12:first').html('<div class="form-group row"><label class="col-form-label">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
        $("#login_history_wrapper").children('.row:nth-child(3)').children('.col-sm-12:first').html($('.pager').clone());
        $('.pages_div').remove();


        }
        });
        } );
        </script>