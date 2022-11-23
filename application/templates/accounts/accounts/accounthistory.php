<? include(TPL_PATH."accounts/accounts/menu_details.php");
$shipperAccess = 0;
if($_SESSION['member']['access_shippers']==1 ||
			   $_SESSION['member']['parent_id']==$_SESSION['member_id'] )
			{
				$shipperAccess = 1;
			}
?>
<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/back.png" alt="Back" width="16" height="16" />
    <?php
if ($_SESSION['member']['parent_id'] == $_SESSION['member_id']) {
    ?>
              <a href="<?=getLink("accounts")?>">&nbsp;Back to the list</a>
              <?php
} else {
    ?>
              <a href="<?=getLink("accounts", "shippers")?>">&nbsp;Back to the list</a>
            <?php
}
?>

</div>



<table id="account_history"   class="table table-bordered">
    <thead>
    <tr>
        <th class="grid-head-left"><?=$this->order->getTitle("commonname", "Field Name")?></th>
        <th>Old Value</th>
        <th>New Value</th>
        <th><?=$this->order->getTitle("change_date", "Date")?></th>
        <th class="grid-head-right"><?=$this->order->getTitle("changed_by", "Changed By")?></th>
    </tr>
    </thead>
    <? if (count($this->data) > 0){?>
	    <? foreach ($this->data as $i => $data) { ?>
	    <tr class="grid-body<?=($i == 0 ? " " : "")?>" id="row-<?=$data['id']?>">
	        <td class="grid-body-left"><?=$data["field_name"]?></td>
	        <td><?=htmlspecialchars($data["old_value"])?></td>
	        <td><?=htmlspecialchars($data["new_value"])?></td>
	        <td align="center"><?=$data["change_date"]?></td>
	        <td align="center" class="grid-body-right"><?=htmlspecialchars($data["changed_by_name"])?></td>
	    </tr>
	    <? } ?>
    <? }else{ ?>
    	<tr class="grid-body" id="row-1">
	        <td colspan="5" align="center">Records not found.</td>
	    </tr>
    <? } ?>
</table>
@pager@
<br />
<?=backButton(getLink("accounts"))?>




<script type="text/javascript">
    $(document).ready(function() {
   $('#account_history').DataTable({
       "lengthChange": false,
       "paging": false,
       "bInfo" : false,
       'drawCallback': function (oSettings) {
           $('#account_history_wrapper').children('.row:first').children('.col-md-6:first').html('<div class="form-group row"><label class="col-form-label">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
           $('#account_history_wrapper').children('.row:last').children('.col-md-5').html($('.pager').clone()).addClass('text-left');
           $('#account_history_wrapper').children('.row:last').children('.col-md-7').html($('.table_b ').html()).addClass('text-right');
           $('.pages-div-custom').remove();
           
      }
   });
} );
</script>


