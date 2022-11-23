<h3>Orders</h3>
<em>This report can be additional filtered by Order ID and Shipper and also exclude all cancelled orders.</em>
<div style="text-align:right; clear:both; padding-bottom:5px; padding-top:5px;">
    <img src="<?= SITE_IN ?>images/icons/back.png" alt="Back" style="vertical-align:middle; width: 16px; height: 16px;" /> <a href="<?= getLink("reports") ?>">&nbsp;Back to the 'Reports'</a>
</div>
<form action="<?= getLink("reports", "commission") ?>" method="post" />
<?= formBoxStart() ?>
<table cellspacing="0" cellpadding="0" border="0">
    <tr>
        <td valign="top">
            <table cellspacing="5" cellpadding="5" border="0">
               <tr>
                    <td colspan="2">@reports@</td>
                </tr>
                <tr>
                    <td colspan="2">@users_ids[]@</td>
                </tr>
                <tr>
                    <td><input type="radio" name="ptype" value="1" id="ptype1" @ptype1ch@ /></td>
                    <td><label for="ptype1">Time Period:</label></td>
                    <td colspan="3">@time_period@</td>
                </tr>
                <tr>
                    <td><input type="radio" name="ptype" value="2" id="ptype2" @ptype2ch@ /></td>
                    <td><label for="ptype2">Date Range:</label></td>
                    <td>@start_date@</td>
                    <td> - </td>
                    <td>@end_date@</td>
                </tr>
                <tr>
                    <td colspan="2"><?= submitButtons("", "Generate") ?></td>
                    <td colspan="3"><?= exportButton("Export to Excel") ?></td>
                </tr>
            </table>
        </td>        
    </tr>
</table>
<?= formBoxEnd() ?>
<br />
<table style="width: 100%" cellpadding="0" cellspacing="0" border="0" class="grid">
        <tr class="grid-head">
                <td width="20%">OrderID</td>
				<td width="20%">Total</td>
                <td width="14%">Deposite</td>
                <td width="14%">Created</td>
                <td width="20%">Creator</td>
                <td width="20%">Assigned</td>
                <td width="20%">Reffered By</td>
                <td width="20%">Company</td>
			    <td width="14%">%Commission</td>
                <td width="14%">Commission</td>
                <td >Type</td>
				<td class="grid-head-right">Action</td>
			</tr>
    <? if (count($this->orders) > 0) { 
                $totalValue = 0;
                $depositeValue = 0;
                $commissionValue = 0;
             foreach ($this->orders as $i => $commission) { 
			         $totalValue 		+= $commission['total_tariff_stored'];
					 $depositeValue 	+= $commission['deposit'];
					 $commission_got_amount 	+= $commission['commission_got_amount'];
					 
					 $bgcolor = "";
					 //print $commission['commission_payed']."--".$commission['commission_payed_assigned'];
					 if($commission['commission_payed']==1 && $commission['created_assigned']==1)
					    $bgcolor = "#339999";
					 elseif($commission['commission_payed_assigned']==1 && $commission['created_assigned']==2)
					    $bgcolor = "#339999";	
						 
			 ?>
			<tr class="grid-body" > 
               
               <td style="white-space:nowrap;" class="grid-body-left" width="14%" bgcolor="<?php print $bgcolor;?>"><?= $commission['number'] ?></td>
               <td style="text-align: center;" width="14%" bgcolor="<?php print $bgcolor;?>">$<?= number_format($commission['total_tariff_stored'], 2, ".", "") ?></td>
               <td style="text-align: center;" width="20%" bgcolor="<?php print $bgcolor;?>">$<?= $commission['deposit'] ?></td>
               <td style="text-align: center;" width="14%" bgcolor="<?php print $bgcolor;?>"><?= date("m/d/y", strtotime($commission['created'])) ?></td>
               <td style="text-align: center;" width="14%" bgcolor="<?php print $bgcolor;?>"><?php print $commission['creator_name']; ?></td>
               <td style="text-align: center;" width="14%" bgcolor="<?php print $bgcolor;?>"><?php print $commission['assign_name']; ?></td>
               <td style="text-align: center;" width="14%" bgcolor="<?php print $bgcolor;?>"><?php print $commission['reffered_by']; ?></td>
               <td style="text-align: center;" width="14%" bgcolor="<?php print $bgcolor;?>"><?php print $commission['company_name']; ?></td>
               <?php if($commission['created_assigned']==2){?>
                  
                  <td style="text-align: center;" width="20%" bgcolor="<?php print $bgcolor;?>"><?= $commission['commission'] ?>%</td>
                  <td style="text-align: center;" width="20%" bgcolor="<?php print $bgcolor;?>">$<?= number_format($commission['commission_got_amount'], 2, ".", "") ?> </td>
                  <td style="text-align: center;" width="20%" class="grid-body-right" bgcolor="<?php print $bgcolor;?>">Assigned</td>
                  <?php if($commission['commission_payed_assigned']==0){?>
                    <td style="text-align: center;" width="20%" class="grid-body-right" ><a href="javascript:void(0);" onclick="commissionpay('<?= $commission['id'] ?>','<?= $commission['assigned_id'] ?>',<?= $commission['created_assigned'] ?>);">Pay</a></td>
                  <?php }else{?>
                    <td style="text-align: center;" width="20%" class="grid-body-right" ></td>
                  <?php }?>
               <?php }elseif($commission['created_assigned']==1){?>
                  
                  <td style="text-align: center;" width="20%" bgcolor="<?php print $bgcolor;?>"><?= $commission['commission_got'] ?>%</td>
                  <td style="text-align: center;" width="20%" bgcolor="<?php print $bgcolor;?>">$<?= number_format($commission['commission_got_amount'], 2, ".", "") ?> </td>
                  <td style="text-align: center;" width="20%" class="grid-body-right" bgcolor="<?php print $bgcolor;?>">Created</td>
                  
                  <?php if($commission['commission_payed']==0){?>
                    <td style="text-align: center;" width="20%" class="grid-body-right"><a href="javascript:void(0);" onclick="commissionpay('<?= $commission['id'] ?>','<?= $commission['creator_id'] ?>',<?= $commission['created_assigned'] ?>);">Pay</a></td>
                  <?php }else{?>
                    <td style="text-align: center;" width="20%" class="grid-body-right" ></td>
                  <?php }?>
                  
               <?php }?>   
              
			</tr>
			<?php } if($totalValue>0){
			?>
                <tr class="grid-body"> 
                   
                   
                   <td style="text-align: right;"><b>Total</b></td>
                   <td style="text-align: center;"><b>$<?= number_format($totalValue, 2, ".", "") ?></b></td>
                   
                   <td style="text-align: center;"><b>$<?= $depositeValue ?></b></td>
                   <td style="text-align: center;">&nbsp;</td>
                   <td style="text-align: center;">&nbsp;</td>
                   <td style="text-align: center;">&nbsp;</td>
                   <td style="text-align: center;">&nbsp;</td>
                   <td style="text-align: center;">&nbsp;</td>
                   <td style="text-align: center;">&nbsp;</td>
                   <td style="text-align: center;"><b>$<?= number_format($commission_got_amount, 2, ".", "") ?></b></td>
                   <td style="text-align: center;">&nbsp;</td>
                  
                </tr>
            
			<?php 
			  } ?>
    <? } else { ?>
        <tr class="grid-body first-row" id="row-">
            <td align="center" colspan="12">
                <? if (isset($_POST['submit'])) { ?>
                    No records found.
                <? } else { ?>
                    Generate report.
                <? } ?>
            </td>
        </tr>
    <? } ?>
</table>


<script type="text/javascript">//<![CDATA[
    $("#users_ids").multiselect({ // Build multiselect for users
        noneSelectedText: 'Select User',
        selectedText: '# users selected',
        selectedList: 1
    });
    
    $("#start_date, #end_date").click(function(){
        $("#ptype2").attr("checked", "checked");
    });

    $("#time_period").click(function(){
        $("#ptype1").attr("checked", "checked");
    });
    //]]></script>
 <script language="javascript">
function commissionpay(id,user_id,comm_type)
{
	    $.ajax({
                url: BASE_PATH + 'application/ajax/entities.php',
                data: {
                    action: "payCommission",
					user_id:user_id,
					comm_type:comm_type,
                    comm_id: id
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function () {
                },
                success: function (response) {
                   if (response.success == true) {
						swal.fire("Order commission successfully paid.");
						window.location.reload();
					} else {
						swal.fire("Commission not paid. Please try again.");
					}
				   //$("#referrer_commission").html(data);
                }
            });
}
</script>