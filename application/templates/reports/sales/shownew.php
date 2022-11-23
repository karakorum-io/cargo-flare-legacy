<div id="detaildiv">

          <div id="detail_data"> </div>

</div>
<script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.tablesorter.js"></script>
<script type="text/javascript">
$("#detaildiv").dialog({

	modal: true,

	width: 700,

	height: 310,

	title: "Freight Dragon Results",

	hide: 'fade',

	resizable: false,

	draggable: false,

	autoOpen: false

});

function showDetails($ddate) {

    var user_id = "";  
	var userStr = "";
	var user_ids = [];       
	//alert($("#users_ids option:selected").size());
	if($("#users_ids option:selected").size()>0){
		
	$("#users_ids option:selected").each(function(){
		user_id = $(this).val();
		user_ids.push(user_id);  
	});
	
	userStr = user_ids.join(",");
	}
	/*
	var ptype = '';
	//alert($("#ptype1:checked").val());
	if($("#ptype1:checked").val()==1)
	  ptype = $("#ptype1").val();
	  //alert('---'+$("#ptype2:checked").val());
	if($("#ptype2:checked").val()==2)
	  ptype = $("#ptype2").val();
	  */
             $("body").nimbleLoader('show');

                $.ajax({
                    type: "POST",
                    url: BASE_PATH + "application/ajax/getreport.php",
                    dataType: "json",
                    data: {

                        action: "getsales",
                        //time_period:  $("#time_period").val(),
						start_date:  $ddate,
						//end_date:  $("#end_date").val(),
						//define_as:  $("#define_as").val(),
						//ptype:  ptype,
						 user_ids: userStr

                    },

                    success: function (res) {
                       if (res.success) {
						   //alert('----'+res.detailData);
							 $("#detail_data").html(res.detailData);
							  $("#detaildiv").dialog({width: 600},'option', 'title', 'Carrier Data').dialog("open");

                        } else {

                            alert("Can't get data. Try again later, please");
                       }
                    },
                    complete: function (res) {
                        $("body").nimbleLoader('hide');
                    }
                });
        
    }
</script>
<h3>Sales</h3>
<em>Showing orders that were created during selected time period and dispatched at any time</em>
<div style="text-align:right; clear:both; padding-bottom:5px; padding-top:5px;">
    <img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?= getLink("reports") ?>">&nbsp;Back to the 'Reports'</a>
</div>

<?= formBoxStart() ?>
<form action="<?= getLink("reports", "salesnew") ?>" method="post" />
<table cellspacing="0" cellpadding="0" border="0">
    <tr>
        <td valign="top">
            <table cellspacing="5" cellpadding="5" border="0">
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
                    <td colspan="2">@users_ids[]@</td>
                </tr>
                <tr>
                    <td colspan="5">
                        @define_as@
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><?= submitButtons("", "Generate") ?></td>
                    <td colspan="3"><?= exportButton("Export to Excel") ?></td>
                </tr>
            </table>
        </td>
        <td valign="top" style="padding-left:30px;">
            <div style="padding:10px; background-color:#fffbd8; border:#000 1px dashed;">
                <strong>Conversion Rate</strong> - The percentage of quotes, created during this time period, converted to orders during the same time period.<br />
                <strong>Terminal Fees</strong> - A zero ($0) indicates no pickup or delivery terminal fees. If two numbers are displayed, the top number is the pickup terminal fee and the bottom number is the delivery terminal fee.<br />
                <strong>Profit Margin</strong> - Gross Profit divided by Tariffs.<br />
                Number represents an average, not a total.<br />
                Note: All dollar figures are rounded to the nearest whole dollar.<br />
            </div>
        </td>
    </tr>
</table>
<?= formBoxEnd() ?>
<br />
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid" id="lsTable">
    <thead>
        <tr class="grid-head">
            <th class="grid-head-left">Dispatch Date</th>
            <th>Day</th>
            <th>Total Loads Dispatched</th>
            <th>Total Tariff</th>
            <th>Carrier Pay</th>
            <th>GP</th>
            <th>GP%</th>
            
        </tr>
    </thead>
    <? if (count($this->sales) > 0) { ?>
        <tbody>
            <? 
			$TotalCount = 0;							
			$totalTariff =0;
			$carrierPay=0;
			$gpTotal=0;
			$GPPercentage =0;
			foreach ($this->sales as $i => $ls) { ?>
                <tr class="grid-body<?= ($i == 0 ? " first-row" : "") ?>">
                    <td style="white-space: nowrap;" class="grid-body-left"><?= htmlspecialchars($ls['dispatched']); ?></td>
                    <td align="right"><?= $ls["day"] ?></td>
                    <td align="right"><a href="javascript:void(0);" onclick="showDetails('<?php print $ls['dispatched'];?>');"><?= $ls["NoOfShipment"] ?></a></td>
                    <td align="center">$<?= number_format($ls["Tariff"], 2); ?></td>
                    <td align="right">$<?= number_format($ls["CarrierPay"], 2); ?></td>
                    <td align="right">$<?= number_format($ls["GP"], 2); ?></td>
                     <td align="right"><?= number_format($ls["GPPercentage"], 2); ?>%</td>
                    
                </tr>
            <? 
			        $TotalCount += $ls['NoOfShipment'];
			        $totalTariff +=$ls['Tariff'];
                    $carrierPay +=$ls['CarrierPay'];
					$gpTotal +=$ls['GP'];
					$GPPercentage +=$ls['GPPercentage'];
			
			} ?>
        </tbody>
        <? $t = $this->totals; ?>
        <tr class="grid-body totals">
            <td style="white-space: nowrap;" class="grid-body-left">TOTALS</td>
            <td align="right">&nbsp;</td>
            <td align="right"><?= number_format($TotalCount, 2); ?></td>
            <td align="right"><?= number_format($totalTariff, 2); ?></td>
            <td align="center"><?= number_format($carrierPay, 2); ?></td>
            <td align="right"><?= $gpTotal ?></td>
            <td align="right">$<?= $GPPercentage; ?>%</td>
            
        </tr>
    <? } else { ?>
        <tr class="grid-body first-row" id="row-">
            <td align="center" colspan="17">
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
    
//    $("#users_ids").each(function(){ // Select all users by default
//        $("#users_ids option").attr("selected", "selected");
//    });
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
    $(document).ready(function()
    {
        $("#lsTable").tablesorter();
    });
    //]]></script>

