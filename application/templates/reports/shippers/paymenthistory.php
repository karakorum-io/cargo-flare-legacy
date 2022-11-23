<div id="detaildiv">
<div id="detail_data"> </div>
</div>

<style type="text/css">
.header_option {
    padding: 15px;
}
    
</style>
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

function showDetails($id,$assigned_id,$source,$startdate,$enddate) {
 
             $("body").nimbleLoader('show');

                $.ajax({
                    type: "POST",
                    url: BASE_PATH + "application/ajax/getreport.php",
                    dataType: "json",
                    data: {
                        action: "getshipperorders",
                        id:  $id,
						assigned_id:  $assigned_id,
						source: $source,
						start_date: $startdate,
						end_date : $enddate
						//start_date: $startdate,
						//end_date : $enddate

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
                            alert("Can't get data. Try again later, please");                    
                    },
                    complete: function (res) {
                        $("body").nimbleLoader('hide');
                    }
                });
        
    }
	
	function showinvoices($id,$assigned_id,$source,$startdate,$enddate) {
 
             $("body").nimbleLoader('show');

                $.ajax({
                    type: "POST",
                    url: BASE_PATH + "application/ajax/getreport.php",
                    dataType: "json",
                    data: {
                        action: "getopeninvoices",
                        id:  $id,
						assigned_id:  $assigned_id,
						source: $source,
						start_date: $startdate,
						end_date : $enddate
						//start_date: $startdate,
						//end_date : $enddate

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
                            alert("Can't get data. Try again later, please");                    
                    },
                    complete: function (res) {
                        $("body").nimbleLoader('hide');
                    }
                });
        
    }
	
		function showpayments($id,$assigned_id,$source,$startdate,$enddate) {
 
             $("body").nimbleLoader('show');

                $.ajax({
                    type: "POST",
                    url: BASE_PATH + "application/ajax/getreport.php",
                    dataType: "json",
                    data: {
                        action: "getpayments",
                        id:  $id,
						assigned_id:  $assigned_id,
						source: $source,
						start_date: $startdate,
						end_date : $enddate
						//start_date: $startdate,
						//end_date : $enddate

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
                            alert("Can't get data. Try again later, please");                    
                    },
                    complete: function (res) {
                        $("body").nimbleLoader('hide');
                    }
                });
        
    }
</script>
<!--  -->
<div class="quote-info accordion_main_info_new">
    <div class="row">           
        <div class="col-12">
            <div class="kt-portlet ">
                <div class="kt-portlet__head" id="accordion_title">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                           Shippers
                        </h3>
                    </div>
                </div>
                <div class="kt-portlet__body accordion_info_content_new accordion_info_content_open">

                <div class="row">
                <div class="header_option">
                <?= formBoxStart("Options") ?>
                </div>
                </div>

                <div class="row">
                <form action="<?= getLink("reports", "shipperpayments") ?>" method="post">
                <div class="col-12 col-sm-12">
                <div class="row">

                <div class="col-12 col-sm-6">
                <div class="form-group">

               
                    <label  for="ptype1" class="kt-radio kt-radio--brand" >
                    <input type="radio" name="ptype" value="1" id="ptype1" @ptype1ch@  / >Time Period:
                    <span></span>
                    </label>
                    @time_period@
               

                </div>
                </div>
                <div class="col-12 col-sm-6">
                <div class="form-group">
               

                    <label  for="ptype2" class="kt-radio kt-radio--brand" >
                    <input type="radio" name="ptype" value="2" id="ptype2" @ptype2ch@  / >Date Range:
                    <span></span>
                    </label>

                  <div class="row">
                   <div class="col-12 col-sm-6">
                     @start_date@
                   </div>

                    <div class="col-12 col-sm-6">
                        @end_date@
                   </div>
                  </div>
              
                </div>
                </div>
                </div>

                <div class="row">
                    <div class="col-12 col-sm-4">
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <?= submitButtons("", "Generate") ?>
                            </div>

                            <div class="col-12 col-sm-6">
                                <?= exportButton("Export to Excel") ?>
                            </div>
                        </div>
                    </div>
                </div>



                 
                   </div>
                  </div>

                   <div class="row">
                    <div class="col-12 col-sm-12">
                        @pager@
                    </div>
                   </div>


                   <div class="row">
                    <div class="col-12 col-sm-12">
                         <table class="table table-bordered">
        <tr >
            <td "><?= $this->order->getTitle("id", "ID") ?></td>
            <td >
               <?= $this->order->getTitle("shipper_id", "Shipper Information") ?><br />               
            </td>
            
            <td><?= $this->order->getTitle("total_tariff", "Tariff") ?></td>
            <td><?= $this->order->getTitle("total_carrier_pay", "Carrier Pay") ?></td>
            <td><?= $this->order->getTitle("total_deposite", "Deposits") ?></td>
            <td><?= "PaidShipperAmount" ?></td>
            <td>
                <?= "LastOrderDate" ?>
            </td>
            <td>
               <?= "LastPaidDate" ?>
            </td>
            <td><?= "LastInvoiceDate" ?></td>
            <td><?= "orderCount" ?></td>
            <td><?= "TotalofDaysToPay" ?></td>
            <td><?= "AvgDayToPay" ?></td>
        </tr>
        <? if (count($this->shippers) > 0) { ?>
            <? foreach ($this->shippers as $i => $shipper) { ?>

                <tr class="grid-body<?= ($i == 0 ? " first-row" : "") ?>" id="row-<?= $shipper["account_id"]; ?>">
                    <td class="grid-body-left"><?= $shipper["account_id"]; ?></td>
                    <td valign="top">
                        <?= htmlspecialchars($shipper["shipperfname"]. " " . $shipper["shipperlname"]); ?><br />
                        <strong><?= htmlspecialchars($shipper["shippercompany"] == "" ? "Company not available" :$shipper["shippercompany"]); ?></strong><br/>
                         
                         <a href="mailto:<?= htmlspecialchars($shipper["shipperemail"]); ?>"><?= htmlspecialchars($shipper["shipperemail"]); ?></a>
                    </td>
                    <td valign="center">$<?= number_format((float)($shipper["total_tariff"]), 2, ".", ","); ?></td> 
                    <td valign="center">$<?= number_format((float)($shipper["total_carrier_pay"]), 2, ".", ","); ?></td>
                    <td valign="center">$<?= number_format((float)($shipper["total_deposite"]), 2, ".", ","); ?></td>
                   
                    <td valign="center">$<?= htmlspecialchars($shipper["PaidShipperAmount"]); ?></td> 
                    <td valign="center"><?= htmlspecialchars($shipper["LastOrderDate"]); ?></td>
                    <td valign="center"><?= htmlspecialchars($shipper["LastPaidDate"]); ?></td>
                    <td valign="center"><?= htmlspecialchars($shipper["LastInvoiceDate"]); ?></td>
                    <td valign="center"><?= htmlspecialchars($shipper["orderCount"]); ?></td> 
                    <td valign="center"><?= htmlspecialchars($shipper["TotalofDaysToPay"]); ?></td>
                    <td valign="center"><?= number_format((float)($shipper["AvgDayToPay"]), 0, ".", ","); ?></td>               
                </tr>
            <? } ?>
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
          </div>
         </div>


                     <div class="row">
                    <div class="col-12 col-sm-12">
                         @pager@
                    </div>
                   </div>




                </div>
            </div>              
        </div>
    </div>
</div>
    
    
    
    


<!--  -->
<script type="text/javascript">
$.fn.datepicker.defaults.format = "mm/dd/yyyy";
$('.kt_datepicker_1').datepicker({
    startDate: '-3d'
});
</script>

