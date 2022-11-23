

<!--begin::Modal-->
<div class="modal fade" id="detaildiv" tabindex="-1" role="dialog" aria-labelledby="detaildiv_model" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detaildiv_model">Freight Dragon Results</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="modal-body">
                <div id="detail_data"> </div>
                </div>
            </div>
            <div class="modal-footer">
               
            </div>
        </div>
    </div>
</div>
<!--end::Modal-->


<script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.tablesorter.js"></script>
<script type="text/javascript">


function showDetails($id,$assigned_id,$source,$startdate,$enddate) {
 
           Processing_show();
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

                    },

                    success: function (res) {
						// alert(res.detailData);
                       if (res.success) {
						  
                            $("#detail_data").html(res.detailData);
                            $("#detaildiv").find('.modal-title').html('Orders Data')
                            $("#detaildiv").modal();

                        } else {

                            swal.fire("Can't get data.");
                       }
                    },
					error: function (res) {                      
                            swal.fire("Can't get data. Try again later, please");                    
                    },
                    complete: function (res) {
                        KTApp.unblockPage();
                    }
                });
        
    }
	
	function showinvoices($id,$assigned_id,$source,$startdate,$enddate) {
 
               Processing_show();

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
                            $("#detaildiv").find('.modal-title').html('Orders Data')
                            $("#detaildiv").modal();

                        } else {

                            swal.fire("Can't get data.");
                       }
                    },
					error: function (res) {                      
                            swal.fire("Can't get data. Try again later, please");                    
                    },
                    complete: function (res) {
                        KTApp.unblockPage();
                    }
                });
        
    }
	
		function showpayments($id,$assigned_id,$source,$startdate,$enddate) {
 
             Processing_show();
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
                               console.log(res.detailData);
							   $("#detaildiv").find('.modal-title').html('Orders Data')
                               $("#detaildiv").modal();

                        } else {

                            swal.fire("Can't get data.");
                       }
                    },
					error: function (res) {                      
                            swal.fire("Can't get data. Try again later, please");                    
                    },
                    complete: function (res) {
                        KTApp.unblockPage();
                    }
                });
        
    }
	
</script>
<br>



    <div  class="kt-portlet">
    <div class="kt-portlet__head">
    <div class="kt-portlet__head-label">
    Shipper(s) Reveune Report

    </div>
    </div>
    <div  class="kt-portlet__body">

<?= formBoxStart() ?>
<form action="<?= getLink("reports", "shippers")."/order/".$_GET['order']."/arrow/".$_GET['arrow']."/" ?>" method="post">

    <div class="row">
        <div class="col-8">
            <div class="row">
                <div  class="col-6">
                    <div class="form-group">
                      

                        <label for="ptype1" class="kt-radio kt-radio--bold kt-radio--success">
                        <input type="radio" name="ptype" value="1" id="ptype1" @ptype1ch@> Time Period:
                        <span></span>
                        </label>


                        @time_period@
                    </div>
                </div>

                <div  class="col-6">
                    <div class="form-group">
                        

                        <label for="ptype2" class="kt-radio kt-radio--bold kt-radio--success">
                        <input type="radio" name="ptype" value="2" id="ptype2" @ptype2ch@> Date Range
                        <span></span>
                        </label>

                        <div class="row">
                            <div class="col-6">
                                @start_date@
                            </div>
                            <div class="col-6">
                                @end_date@
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-8">
            <div class="row">
                <div  class="col-6">
                    


                    <label for="ptype3" class="kt-radio kt-radio--bold kt-radio--success" >
                    <input type="radio" name="ptype" value="1" id="ptype3" @ptype1ch@> Date Range
                    <span></span>
                    </label>



                    @customers_name@
                </div>
                <div  class="col-6 mt-4">
                    <div class="row mt-3">
                        <div class="col-2">
                         <?= submitButtons("", "Submit") ?>
                        </div>
                        <div class="col-6">
                         <?= exportButton("Export to Excel",'btn-sm btn_light_green') ?>
                        </div>
                   </div>
                </div>
            </div>
        </div>
    </div>
<?= formBoxEnd() ?>


   <div class="row mt-4">
    <table id="reveune_report" class="table table-bordered">
        <thead>
        <tr>
            <th ><?= $this->order->getTitle("B.id", "ID") ?></th>
            <th style="white-space: nowrap;">
               <?= $this->order->getTitle("shipperfname", "Shipper Information") ?><br />               
            </th>
            <th width="5%"><?= $this->order->getTitle("source_name", "Source") ?></th>
            <th>
                <?= $this->order->getTitle("A.AssignedName", "Assigned To") ?>
			</th>
			<th>
               <?= $this->order->getTitle("orders", "Orders") ?>
            </th>
            <th><?= $this->order->getTitle("tariff", "Tariff") ?></th>
            <th><?= $this->order->getTitle("carrier", "Carrier Pay") ?></th>
            <th><?= $this->order->getTitle("deposit", "Deposits") ?></th>
            <th><?= $this->order->getTitle("invoices", "Open Invoices") ?></th>
            <th><?= $this->order->getTitle("payments", "Payments Processed") ?></th>
        </tr>
        </thead>
        <? if (count($this->shippers) > 0) { ?>
            <? foreach ($this->shippers as $i => $shipper) { ?>
                <tr class="<?= ($i == 0 ? " " : "") ?>" id="row-<?= $shipper["account_id"]; ?>">
                    <td class="grid-body-left"><?= $shipper["account_id"]; ?></td>
                    <td valign="top">
                        <?= htmlspecialchars($shipper["shipperfname"]. " " . $shipper["shipperlname"]); ?><br />
                        <strong><?= htmlspecialchars($shipper["shippercompany"] == "" ? "Company not available" :$shipper["shippercompany"]); ?></strong><br/>
                         <a href="tel:<?= htmlspecialchars($shipper["shipperphone1"]); ?>"><?= htmlspecialchars( $shipper["shipperphone1"]== "" ? "Phone not available" :$shipper["shipperphone1"]." ".$shipper["shipperphone1_ext"]); ?></a><br />
						 <a href="tel:<?= htmlspecialchars( $shipper["shipperphone2"]); ?>"><?= htmlspecialchars($shipper["shipperphone2"]== "" ? "Phone not available" :$shipper["shipperphone2"]." ".$shipper["shipperphone2_ext"]); ?></a><br />
						 <a href="mailto:<?= htmlspecialchars($shipper["shipperemail"]); ?>"><?= htmlspecialchars($shipper["shipperemail"]); ?></a>
                    </td>
                    <td valign="center">
                   <?= htmlspecialchars($shipper["source_name"] == "" ? "NONE" : $shipper["source_name"] ); ?>
                    </td>
                    <td valign="center">
                        <?= htmlspecialchars($shipper["AssignedName"]); ?>						
                    </td>
                    <td valign="center"><a href="javascript:void(0);" onclick="showDetails('<?php print $shipper["account_id"];?>','<?php print $shipper["assigned_id"];?>','<?php print $shipper["source_name"];?>','<?php print $this->start_date;?>','<?php print $this->end_date;?>');"> <?= htmlspecialchars($shipper["orders"]); ?></a></td>
                    <td valign="center">$ <?= htmlspecialchars($shipper["tariff"]); ?></td> 
                    <td valign="center">$ <?= htmlspecialchars($shipper["carrier"]); ?></td>
                    <td valign="center">$<?= htmlspecialchars($shipper["deposit"]); ?></td>
                    <td valign="center"> <a href="javascript:void(0);" onclick="showinvoices('<?php print $shipper["account_id"];?>','<?php print $shipper["assigned_id"];?>','<?php print $shipper["source_name"];?>','<?php print $this->start_date;?>','<?php print $this->end_date;?>');"><?= htmlspecialchars($shipper["invoices"]); ?></a></td>
                    <td valign="center"> <a href="javascript:void(0);" onclick="showpayments('<?php print $shipper["account_id"];?>','<?php print $shipper["assigned_id"];?>','<?php print $shipper["source_name"];?>','<?php print $this->start_date;?>','<?php print $this->end_date;?>');"><?= htmlspecialchars($shipper["payments"]); ?></a></td>			
                </tr>
            <? } ?>
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
</div>
 @pager@

<script type="text/javascript">
    $(document).ready(function() {
    $('#reveune_report').DataTable({
    "lengthChange": false,
    "paging": false,
    "bInfo" : false,
    'drawCallback': function (oSettings) {
     
        $("#reveune_report_wrapper").children('.row:first').children('.col-sm-12:first').html('<div class="form-group row"><label class="col-form-label">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
        $("#reveune_report_wrapper").children('.row:nth-child(3)').children('.col-sm-12:first').html($('.pager').clone());
        $('.pages_div').remove();
        

    }
    });
} );
</script>
<script type="text/javascript">
$.fn.datepicker.defaults.format = "mm/dd/yyyy";
$('#start_date,#end_date').datepicker({
});
$("#start_date").attr("autocomplete='off'");
</script>