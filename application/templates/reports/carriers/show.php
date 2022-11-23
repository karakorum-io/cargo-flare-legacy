<style type="text/css">
    .form-group.row {
    margin-left: 3px!important;
}
</style>

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


function showDispatch($id) {
 
            
            Processing_show();
                $.ajax({
                    type: "POST",
                    url: BASE_PATH + "application/ajax/getreport.php",
                    dataType: "json",
                    data: {
                        action: "getcarrierDispatched",
                        id:  $id,
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
	
	function showinvoices($id) {
 
              Processing_show();

                $.ajax({
                    type: "POST",
                    url: BASE_PATH + "application/ajax/getreport.php",
                    dataType: "json",
                    data: {
                        action: "getcarrierinvoices",
                        id:  $id,
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
	
		function showpayments($id) {
 
                Processing_show();
                $.ajax({
                    type: "POST",
                    url: BASE_PATH + "application/ajax/getreport.php",
                    dataType: "json",
                    data: {
                        action: "getcarrierpayments",
                        id:  $id,
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
	
			function showTariff($id) {
 
              Processing_show();
                $.ajax({
                    type: "POST",
                    url: BASE_PATH + "application/ajax/getreport.php",
                    dataType: "json",
                    data: {
                        action: "getcarrierTariff",
                        id:  $id,
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
	
</script>

    <div  class="kt-portlet">
    <div class="kt-portlet__head">
    <div class="kt-portlet__head-label">
    Carrier Information Report

    </div>
    </div>
    <div  class="kt-portlet__body">

<?= formBoxStart() ?>
<form action="<?= getLink("reports", "carriers") ?>" method="post">
<div class="row">
    <div class="col-8">
        <div class="row">
            <div class="col-6">
                @company_name@
            </div>
            <div class="col-6 mt-4">
                <div class="row mt-2">
                    <div class="col-2">
                        <?= submitButtons("", "Submit") ?>
                    </div>
                    <div class="col-6">
                        <?= exportButton("Export to Excel",'btn-sm btn_light_green ') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
   <?= formBoxEnd() ?>







  <div  class="row mt-4">
    <table id="carrier_information"  class="table table-bordered">
        <thead>
        <tr >
            <th class="grid-head-left"><?= $this->order->getTitle("id", "ID") ?></th>
            <th style="white-space: nowrap;">
               <font size="2px"> <?= $this->order->getTitle("first_name", "Carrier Name") ?> </font>             
            </th>
            <th>
              <font size="2px">  <?= $this->order->getTitle("contact_name", "Contact Information") ?> </font>             
            </th>
            <th style="min-width:120px">
               <font size="2px"><?= $this->order->getTitle("fax", "Fax Number") ?></font>
            </th>           
            <th><font size="2px"><?= $this->order->getTitle("email", "Email Addresses") ?></font></th>
            <th><font size="2px"><?= $this->order->getTitle("dispatch", "Dispatches") ?>  </font>         </th>
            <th><font size="2px"><?= $this->order->getTitle("bills", "Open Bills") ?></font></th>
            <th><font size="2px"><?= $this->order->getTitle("payments", "Payments Made") ?></font></th>
            <th><font size="2px"><?= $this->order->getTitle("total_tariff", "Total Tariff") ?></font></th>            
        </tr>
    </thead>
        <? if (count($this->carriers) > 0) { ?>
            <? foreach ($this->carriers as $i => $carrier) { ?>
                <tr class="grid-body<?= ($i == 0 ? " " : "") ?>" id="row-<?= $carrier["id"] ?>">
                    <td class="grid-body-left"><?= $carrier["id"]; ?></td>
                    <td valign="center">
                        <?= htmlspecialchars($carrier["company_name"]); ?><br />
                        <?= htmlspecialchars($carrier["print_name"]); ?><br/>
						<?= htmlspecialchars($carrier["tax_id_num"]); ?><br/>
						<?= htmlspecialchars($carrier["insurance_iccmcnumber"]); ?>
                    </td>
                    <td valign="center">
                        <?= htmlspecialchars($carrier["contact_name1"]==""?$carrier["phone1"]:$carrier["contact_name1"]." ,".$carrier["phone1"]); ?><br />
                        <?= htmlspecialchars($carrier["contact_name2"]==""?$carrier["phone2"]:$carrier["contact_name2"]." ,".$carrier["phone2"]); ?><br/>
						<?= htmlspecialchars($carrier["mobile"]==""?"--":$carrier["mobile"]); ?>
                    </td>                  
                    <td valign="center"><?= htmlspecialchars($carrier["fax"]); ?></td>
                    <td valign="center"><a href="mailto:<?= htmlspecialchars($carrier["email"]); ?>"><?= htmlspecialchars($carrier["email"]); ?></a></td>
                    <td>                       
                        <a href="javascript:void(0);" onclick="showDispatch('<?php print $carrier["id"];?>');"><?= htmlspecialchars($carrier["orders"]); ?></a>
                    </td>
                    <td valign="center"><a href="javascript:void(0);" onclick="showinvoices('<?php print $carrier["id"];?>');"><?= htmlspecialchars($carrier["invoices"]); ?></a></td>
                    <td valign="center"><a href="javascript:void(0);" onclick="showpayments('<?php print $carrier["id"];?>');"><?= htmlspecialchars($carrier["payments"]); ?></a></td>
                    <td valign="center"><a href="javascript:void(0);" onclick="showTariff('<?php print $carrier["id"];?>');">$<?= htmlspecialchars($carrier["tariff"]); ?></a></td>					
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
    $('#carrier_information').DataTable({
    "lengthChange": false,
    "paging": false,
    "bInfo" : false,
    'drawCallback': function (oSettings) {
     
        $("#carrier_information_wrapper").children('.row:first').children('.col-sm-12:first').html('<div class="form-group row"><label class="col-form-label">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
        $("#carrier_information_wrapper").children('.row:nth-child(3)').children('.col-sm-12:first').html($('.pager').clone());
        $('.pages_div').remove();
        

    }
    });
} );
</script>