

<!--begin::Modal-->
<div class="modal fade" id="carrierdiv" tabindex="-1" role="dialog" aria-labelledby="carrierdiv_model" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="carrierdiv_model">Reassign Quote</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="carrier_data"> </div>
            </div>
            <div class="modal-footer">
               
            </div>
        </div>
    </div>
</div>
<!--end::Modal-->



<script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.tablesorter.js"></script>
<script type="text/javascript">

function getCarrierData(entity_id,ocity,ostate,ozip,dcity,dstate,dzip) {

		

        if (entity_id == "") {

            swal.fire("Order not found");

        } else {



              Processing_show();

                $.ajax({

                    type: "POST",

                    url: BASE_PATH + "application/ajax/getcarrier.php",

                    dataType: "json",

                    data: {

                        action: "getcarrier",
                        ocity: ocity,
						ostate: ostate,
						ozip: ozip,
						dcity: dcity,
						dstate: dstate,
						dzip: dzip,
                        entity_id: entity_id

                    },

                    success: function (res) {

						//alert('===='+res.success);

                        if (res.success) {

                          // alert(res.carrierData);

							 

							 $("#carrier_data").html(res.carrierData);

							  //$("#mail_file_name").html(file_name);

                                 $("#carrierdiv").find('.modal-title').html('Carrier Data')
                                 $("#carrierdiv").modal();

							  

							

                        } else {

                            swal.fire("Can't send email. Try again later, please");

                        }

                    },

                    complete: function (res) {
                        KTApp.unblockPage();

                    }

                });





        }

    }

function getCarrierDataRoute(entity_id,ocity,ostate,ozip,dcity,dstate,dzip) {

        if (entity_id == "") {

            swal.fire("Order not found");

        } else {

               Processing_show();
              var radius=$("#radius").val();
                $.ajax({

                    type: "POST",

                    url: BASE_PATH + "application/ajax/getcarrier.php",

                    dataType: "json",

                    data: {
                        action: "getcarrierData",
                        ocity: ocity,
						ostate: ostate,
						ozip: ozip,
						dcity: dcity,
						dstate: dstate,
						dzip: dzip,
                        entity_id: entity_id,
						radius:radius

                    },

                    success: function (res) {

						//alert('===='+res.success);

                        if (res.success) {
                               $("#routeCarrierDataDiv").html(res.carrierData);

                        } else {
                            swal.fire("Can't send email. Try again later, please");
                        }
                    },
                    complete: function (res) {
                       KTApp.unblockPage();
                      }

                });
        }

    }
	
function getVehicles(id) {

     $("#vehicles_info_model").modal();
	$.ajax({
		type: "POST",
		url: BASE_PATH+"application/ajax/vehicles.php",
		dataType: 'json',
		data: {
			action: 'getVehicles',
			id: id
		},
		success: function(res) {
			if (res.success) {
				$("#vehicles-info").html(res.data);
			} else {
				swal.fire("Vehicles not found.");
			}
		}
	});
   }
	
</script>


 <div  class="kt-portlet">
    <div class="kt-portlet__head">
    <div class="kt-portlet__head-label">
   <h2 class="kt-menu__link ">Carrier Performance Report</h2>

    </div>
    </div>
    <div  class="kt-portlet__body">

<?= formBoxStart() ?>
<form action="<?= getLink("reports", "on_time") ?>" method="post" />
<div class="row">
    <div class="col-8">
        <div class="row">
        <div class="col-6">
            <div class="form-group">
            <label for="ptype1" class="kt-radio kt-radio--bold kt-radio--brand">
            <input type="radio" name="ptype" value="1" id="ptype1" @ptype1ch@> Time Period:
            <span></span>
            </label>

            @time_period@
            </div>
        </div>
        <div class="col-6">
             <div class="form-group">

                <label for="ptype2" class="kt-radio kt-radio--bold kt-radio--brand">
                <input type="radio" name="ptype" value="2" id="ptype2" @ptype2ch@> Date Range:
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
         <div class="form-group">
        <div class="row">
        <div class="col-6">
            @status_id@
        </div>
        <div class="col-6">
            <div class="row">
               @users_ids[]@
            </div>
        </div>
        </div>
      </div>
    </div>
</div>

<div class="row">
    <div class="col-8">
        <div class="row">

        <div class="col-6">
           @order_id@
        </div>
        <div class="col-6">
            <div class="row">
              @ship_via@
            </div>
        </div>
        </div>
    </div>
</div>

    <div class="row">
        <div class="col-8">
            <div class="row">
            <div class="col-6">
                @carrier_name@
            </div>
            <div class="col-6">
                <div class="form-group">

                    
                <div class="row mt-4">
                    
                    <div class="col-3 mt-2">

                   <?= submitButtons("", "Generate") ?>
                    </div>

                    <div class="col-6 mt-2">
                   <?= exportButton("Export to Excel",'btn-sm btn_light_green') ?>
                    </div>
                </div>
              </div>
            </div>
            </div>
        </div>
    </div>
<?= formBoxEnd() ?>


<div class="row mt-4 ml-3 mr-3">
 <div  class="kt-portlet__body">
<table class="table table-bordered ml-4 mr-4" id="carrier_performance"  >
    <thead>
        <tr >
            <th class="grid-head-left"><?= $this->order->getTitle("id", "Order ID"); ?></th>                       
            <th><?= $this->order->getTitle("shipper_id", "Shipper"); ?></th>
            <th>Vehicles</th>
            <th><?= $this->order->getTitle("origin_id", "Origin"); ?></th>
            <th><?= $this->order->getTitle("destination_id", "Destination"); ?></th>
            <th><?= $this->order->getTitle("avail_pickup_date", "Est. Load date"); ?></th>
            <th><?= $this->order->getTitle("actual_pickup_date", "Actual Pick up date"); ?></th>           
            <th><?= $this->order->getTitle("est_ship_date", "Est. Delivery date"); ?></th>
            <th><?= $this->order->getTitle("actual_ship_date", "Actual Delivery date"); ?></th>            
            <th class="grid-head-right"></th>
        </tr>
        </thead>
    <? if (count($this->orders) > 0) { ?>
            <? foreach ($this->orders as $i => $o) { ?>
					
			<?php if($o->prefix){
				$id = $o->prefix ."-". $o->number;
				} else
				{
					$id =$o->number;
				} 				
				?>
                <tr class="grid-body<?= ($i == 0 ? " " : "") ?>" <?=(($o->getDeliveryDeviation()>3 || $o->getPickUpDeviation() >3)?"style=\"background-color:#ffd6d9\"":"" )?>>
                    <td style="white-space: nowrap;" class="grid-body-left"><a href="<?= SITE_IN ?>application/orders/show/id/<?= $o->id ?>"  target="_blank"><?= $id ?></a><br/>
                   Status: <?= Entity::$status_name[$o->status] ?></td>
                    <td><?= (int)$o->shipper_id > 0 ? htmlspecialchars($o->getShipper()->fname."  ".$o->getShipper()->lname)."<br/>":"" ?>
					<?= $o->getShipper()->company!='' ? "<b>".htmlspecialchars($o->getShipper()->company)."</b><br/>":"" ?>
					<?= $o->getShipper()->mobile !='' ? htmlspecialchars($o->getShipper()->mobile)."<br/>":"" ?>
					<a href="mailto:"><?= (int)$o->shipper_id > 0 ? htmlspecialchars($o->getShipper()->email):"" ?></a></td>
                    <td align="center">
                       <span class="like-link multi-vehicles-new" onclick="getVehicles('<?php print $o->id;?>');">Show Vehicles</span>

                    <!--  <div class="vehicles-info" id="vehicles-info-<?php print $o->id;?>">
                     </div> -->

                <!-- Modal -->
                <div class="modal fade" id="vehicles_info_model" tabindex="-1" role="dialog" aria-labelledby="vehicles-info_D" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="vehicles-info_D">Show Vehicles</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-times" aria-hidden="true"></i>
                                </button>
                            </div>
                            <div class="modal-body">
                             <div id="vehicles-info">
                            </div> 
                            </div>
                            <div class="modal-footer">
                                
                            </div>
                        </div>
                    </div>
                </div>
                </div>

                <!-- end:: Content -->
                </div>



                    </td>					
                    <td><?= $o->origin_id > 0 ? htmlspecialchars(formatAddress("", "", strtoupper($o->getOrigin()->city), $o->getOrigin()->state, $o->getOrigin()->zip)):"" ?></td>
                    <td><?= $o->destination_id > 0?htmlspecialchars(formatAddress("", "", strtoupper($o->getDestination()->city), $o->getDestination()->state, $o->getDestination()->zip)):""?></td>
                    <td style="text-align: center;"><?= $o->getEstLoadDate("m/d/Y"); ?></td>
                    <td style="text-align: center;"><?= $o->getActualPickUpDate("m/d/Y"); ?><br/>Deviation :<?= $o->getPickUpDeviation(); ?></td>
                    <td style="text-align: center;"><?= $o->getDeliveryDate("m/d/Y"); ?></td>
                    <td style="text-align: center;"><?= $o->getDeliveryDate("m/d/Y"); ?><br/>Deviation :<?= $o->getDeliveryDeviation(); ?></td>                    
                    <td class="grid-body-right"><a href="javascript:void(0);" onclick="getCarrierData(<?php print $o->id;?>,'<?php print $o->getOrigin()->city;?>','<?php print $o->getOrigin()->state;?>','<?php print $o->getOrigin()->zip;?>','<?php print $o->getDestination()->city;?>','<?php print $o->getDestination()->state;?>','<?php print $o->getDestination()->zip;?>');">View Carrier</a></td>
                </tr>
            <? } ?>
    <? } else { ?>
        <tr class="grid-body first-row" id="row-">
            <td align="center" colspan="14">
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
</div>
@pager@

<script type="text/javascript">//<![CDATA[
    $("#users_ids").select2({ // Build multiselect for users
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
        $(document).ready(function() {
        $('#carrier_performance').DataTable({
        "lengthChange": false,
        "paging": false,
        "bInfo" : false,
        'drawCallback': function (oSettings) {

        $("#carrier_performance_wrapper").children('.row:first').children('.col-sm-12:first').html('<div class="form-group row"><label class="col-form-label">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
        $("#carrier_performance_wrapper").children('.row:nth-child(3)').children('.col-sm-12:first').html($('.pager').clone());
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