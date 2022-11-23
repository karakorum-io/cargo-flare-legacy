<div id="detaildiv">

          <div id="detail_data"> </div>

</div>
<script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.tablesorter.js"></script>
<script type="text/javascript">
$("#detaildiv").dialog({

	modal: true,

	width: 600,

	height: 410,

	title: "Freight Dragon Results",

	hide: 'fade',

	resizable: false,

	draggable: false,

	autoOpen: false

});

function showDetails($id,$startdate,$enddate) {
 
             $("body").nimbleLoader('show');

                $.ajax({
                    type: "POST",
                    url: BASE_PATH + "application/ajax/getreport.php",
                    dataType: "json",
                    data: {
                        action: "getsales",
                        id:  $id,
						start_date: $startdate,
						end_date : $enddate

                    },

                    success: function (res) {
						// alert(res.detailData);
                       if (res.success) {
						   //alert('----'+res.detailData);
							 $("#detail_data").html(res.detailData);
							  $("#detaildiv").dialog({width: 600},'option', 'title', 'Orders Data').dialog("open");

                        } else {

                            alert("Can't get data.");
                       }
                    },
					error: function (res) {                      
                            alert("Can't get data. Try again later, please1");                    
                    },
                    complete: function (res) {
                        $("body").nimbleLoader('hide');
                    }
                });
        
    }
	
function showTariffDetails($id,$startdate,$enddate) {
 
             $("body").nimbleLoader('show');

                $.ajax({
                    type: "POST",
                    url: BASE_PATH + "application/ajax/getreport.php",
                    dataType: "json",
                    data: {
                        action: "getsalesTariff",
                        id:  $id,
						start_date: $startdate,
						end_date : $enddate

                    },

                    success: function (res) {
						// alert(res.detailData);
                       if (res.success) {
						   //alert('----'+res.detailData);
							 $("#detail_data").html(res.detailData);
							  $("#detaildiv").dialog({width: 600},'option', 'title', 'Orders Data').dialog("open");

                        } else {

                            alert("Can't get data.");
                       }
                    },
					error: function (res) {                      
                            alert("Can't get data. Try again later, please1");                    
                    },
                    complete: function (res) {
                        $("body").nimbleLoader('hide');
                    }
                });
        
    }
	
	function showCarrierDetails($id,$startdate,$enddate) {
 
             $("body").nimbleLoader('show');

                $.ajax({
                    type: "POST",
                    url: BASE_PATH + "application/ajax/getreport.php",
                    dataType: "json",
                    data: {
                        action: "getsalesCarrier",
                        id:  $id,
						start_date: $startdate,
						end_date : $enddate

                    },

                    success: function (res) {
						// alert(res.detailData);
                       if (res.success) {
						   //alert('----'+res.detailData);
							 $("#detail_data").html(res.detailData);
							  $("#detaildiv").dialog({width: 600},'option', 'title', 'Orders Data').dialog("open");

                        } else {

                            alert("Can't get data.");
                       }
                    },
					error: function (res) {                      
                            alert("Can't get data. Try again later, please1");                    
                    },
                    complete: function (res) {
                        $("body").nimbleLoader('hide');
                    }
                });
        
    }
	
	function showDispatchedDetails($id,$startdate,$enddate) {
 
             $("body").nimbleLoader('show');

                $.ajax({
                    type: "POST",
                    url: BASE_PATH + "application/ajax/getreport.php",
                    dataType: "json",
                    data: {
                        action: "getsalesDispatched",
                        id:  $id,
						start_date: $startdate,
						end_date : $enddate

                    },

                    success: function (res) {
						// alert(res.detailData);
                       if (res.success) {
						   //alert('----'+res.detailData);
							 $("#detail_data").html(res.detailData);
							  $("#detaildiv").dialog({width: 600},'option', 'title', 'Orders Data').dialog("open");

                        } else {

                            alert("Can't get data.");
                       }
                    },
					error: function (res) {                      
                            alert("Can't get data. Try again later, please1");                    
                    },
                    complete: function (res) {
                        $("body").nimbleLoader('hide');
                    }
                });
        
    }
</script>
   


<div class="kt-portlet">
<div class="kt-portlet__head">
    <div class="kt-portlet__head-label">
        <h3 class="kt-portlet__head-title">
            Company Sales Report
        </h3>
    </div>
</div>
<div class="kt-portlet__body">
<?= formBoxStart() ?>
<form action="<?= getLink("reports", "sales") ?>" method="post" />
    <div class="row">
        <div class="col-10">  
           
             <div  class="row">
            <div class="col-6">
                

                <label for="ptype1" class="kt-radio kt-radio--brand">
                <input type="radio" name="ptype" value="1" id="ptype1" @ptype1ch@>Time Period:
                <span></span>
                </label>

                   @time_period@
              </div>
              <div class="col-6">

                <label for="ptype2" class="kt-radio kt-radio--brand">
                <input type="radio" name="ptype" value="2" id="ptype2" @ptype2ch@>Date Range:
                <span></span>
                </label>

             <div class="row">
                <div  class="col-6">
                     @start_date@   - 
                </div>
                 <div  class="col-6">
                    @end_date@
                </div>
             </div>

             </div>
            </div>
        </div>
    </div>



     <div class="row">
        <div class="col-10">  
          <div  class="row">
            <div class="col-6">
                  @users_ids[]@
              </div>
              <div class="col-6 mt-2" >

              <div class="row mt-4">
                <div  class="col-2">
                    <?= submitButtons("", "Generate") ?>
                </div>
                 <div  class="col-4">
                   <?= exportButton("Export to Excel",'btn-sm btn_dark_green') ?>
                </div>
             </div>

             </div>
            </div>
        </div>
    </div>
<?= formBoxEnd() ?>

 <div  class="row">
    <div class="col-12 mt-4">
<table  class="table table-bordered" id="lsTable">
    <thead>
        <tr class="">
            <th class="grid-head-left">User</th>           
            <th>Orders</th>        
            <th>Dispatched</th>
            <th>Tariffs</th>
            <th>Carrier Pay</th>
            <th>Gross Profit</th>
            <th>Profit Margin</th>
            <th class="grid-head-right">Average Profit per Order</th>
        </tr>
    </thead>
    <? if (count($this->sales) > 0) { ?>
        <tbody>
            <? foreach ($this->sales as $i => $ls) { ?>
                <tr class="<?= ($i == 0 ? " first-row" : "") ?>">
                    <td style="white-space: nowrap;" class="grid-body-left"><?= htmlspecialchars($ls['name']); ?></td>                   
                    <td align="right"><a href="javascript:void(0);" onclick="showDetails('<?php print $ls['id'];?>','<?php print $ls['start_date'];?>','<?php print $ls['end_date'];?>');"><?= $ls["orders"]?></a></td>                 
                    <td align="right">
					<a href="javascript:void(0);" onclick="showDispatchedDetails('<?php print $ls['id'];?>','<?php print $ls['start_date'];?>','<?php print $ls['end_date'];?>');"><?= $ls["dispatched"] ?></a>									
					</td>
                    <td align="right"><a href="javascript:void(0);" onclick="showTariffDetails('<?php print $ls['id'];?>','<?php print $ls['start_date'];?>','<?php print $ls['end_date'];?>');">$<?= number_format($ls["tariffs"], 2); ?></a></td>
                    <td align="right"><a href="javascript:void(0);" onclick="showCarrierDetails('<?php print $ls['id'];?>','<?php print $ls['start_date'];?>','<?php print $ls['end_date'];?>');">$<?= number_format($ls["carrier_pay"], 2); ?></a></td>                    
                    <td align="right">$<?= number_format($ls["gross_profit"], 2); ?></td>
                    <td align="center"><?= number_format($ls["profit_margin"], 2); ?>%</td>
                    <td align="right" class="grid-body-right">$<?= number_format($ls["average_profit"], 2); ?></td>
                </tr>
            <? } ?>
        </tbody>
        <? $t = $this->totals; ?>
        <tr class=" totals">
            <td style="white-space: nowrap;" >TOTALS</td>           
            <td align="right"><?= $t["orders"] ?></td>           
            <td align="right"><?= $t["dispatched"] ?></td>
            <td align="right">$<?= number_format($t["tariffs"], 2); ?></td>
            <td align="right">$<?= number_format($t["carrier_pay"], 2); ?></td>            
            <td align="right">$<?= number_format($t["gross_profit"], 2); ?></td>
            <td align="center"><?= number_format($t["profit_margin"], 2); ?>%</td>
            <td align="right" class="grid-body-right">$<?= number_format($t["average_profit"], 2); ?></td>
        </tr>
    <? } else { ?>
        <tr class="grid-body " id="row-">
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
   </div>
 </div>

 </form>
</div>

</div>

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
  
    //]]></script>

    <script type="text/javascript">
    $.fn.datepicker.defaults.format = "mm/dd/yyyy";
    $('#start_date,#end_date').datepicker({
    });
    $("#start_date,#end_date").attr({'autocomplete': 'off','autocorrect': 'off', 'spellcheck': 'false'})
    </script>

    <script type="text/javascript">
    $(document).ready(function() {
   $('#lsTable').DataTable({
       "lengthChange": false,
       "paging": false,
       "bInfo" : false,
       'drawCallback': function (oSettings) {
           $('#lsTable_wrapper').children('.row:first').children('.col-md-6:first').html('<div class="form-group row"><label class="col-form-label">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
           $('#lsTable_wrapper').children('.row:last').children('.col-md-5').html($('.pager').clone()).addClass('text-left');
           $('#lsTable_wrapper').children('.row:last').children('.col-md-7').html($('.table_b ').html()).addClass('text-right');
           $('.pages-div-custom').remove();
           $("#lsTable_wrapper").find('.row:first').css('margin-left','5px');
           
      }
   });
} );
</script>

<script type="text/javascript">
    $(document).ready(function() {
    $('#users_ids').select2();
});
</script>

