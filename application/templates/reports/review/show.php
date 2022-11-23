<script type="text/javascript" src="/jscripts/jquery.rateyo.js"></script>
<link rel="stylesheet" href="/styles/jquery.rateyo.min.css"/>
<style type="text/css">
    .form-group.row {
    margin-left: 12px;
}
</style>

<div  class="kt-portlet">
    <div class="kt-portlet__head">
    <div class="kt-portlet__head-label">
      <h2>Customer Reviews Report</h2>

    </div>
    </div>

<div  class="kt-portlet__body">
<?= formBoxStart() ?>
    <form action="<?= getLink("reports", "review") ?>" method="post" />
      <div  class="row">
    <div  class="col-8">
    <div class="form-group">

    <label for="ptype1" class="kt-radio kt-radio--bold kt-radio--brand">
    <input type="radio" name="ptype" value="1" id="ptype1" @ptype1ch@> Brand state
    <span></span>
    </label>



     @time_period@
      </div>
    </div>

    <div class="col-4 text-right">
    <h2>Average Order: <div class="avgOrder"></div></h2> 
    <h2>Average Carrier: <div class="avgCarrier"></div></h2> 
    </div>
</div>


<div  class="row">
    <div  class="col-8">
    <div class="form-group">
        <div  class="row">
                        
            <div class="col-6">
                <div class="row">
                    <div class="col-6">
                        <label for="ptype2" class="kt-radio kt-radio--bold kt-radio--brand">
                            <input type="radio" name="ptype" value="2" id="ptype2" @ptype2ch@ >Date Range:
                            <span></span>
                        </label>
                        @start_date@
                    </div>
                    <div class="col-6 mt-4">
                       @end_date@
                   </div>
               </div>
           </div>

            <div class="col-6 mt-4" >
            <div class="row">
                <div class="col-3">
                    <?php echo submitButtons("", "Generate"); ?>
                </div>
                <div class="col-6">
                  <?php echo exportButton("Export to Excel",'btn-sm btn_dark_blue'); ?>
               </div>
            </div>

            </div>
        <?= formBoxEnd() ?>
        </div>
      </div>
    </div>
</div>



<div  class="row mt-3">
<table id="customer" class="table table-bordered" >
    <thead>
    <tr>
        <th class="grid-head-left"><?php echo $this->order->getTitle("orderId", "Order ID"); ?></th>
        <th><?php echo $this->order->getTitle("assignedName", "Assigned To"); ?></th>
        <th><?php echo $this->order->getTitle("ratings", "Order Rating"); ?></th>
        <th><?php echo $this->order->getTitle("comment", "Order Comment"); ?></th>        
        <th><?php echo $this->order->getTitle("car_rating", "Carrier Rating"); ?></th> 
        <th><?php echo $this->order->getTitle("car_comment", "Carrier Comment"); ?></th>
        <th><?php echo $this->order->getTitle("created_at", "Rated At"); ?></th>
        <th><?php echo $this->order->getTitle("shipper_id", "Carrier Information"); ?></th> 
    </tr>
    </thead>
    <?php  
        $count =1;
        if (count($this->review) > 0) { 
    ?>
    <?php
        foreach ($this->review as $i => $o) {         
    ?>    
    <tr class="grid-body<?= ($i == 0 ? " first-row" : "") ?>">
        <td align="center"> 
            <a href="<?php echo getLink("orders","review","id",$o->entity_id); ?>">
                <?php echo $o->orderId; ?>
            </a>
	</td>
        <td align="center">
            <?php echo $o->assignedName;?>
        </td>
        <td align="center">
            <div class="rateyo<?php echo $count;?>"></div>
            <script>
                $(function () {
                    $(".rateyo<?php echo $count;?>").rateYo(
                        { 
                            starWidth: "20px", 
                            rating  : <?php echo $o->ratings;?>, 
                            ratedFill: "#419111", 
                            readOnly: true,
                            multiColor: { 
                                "startColor": "#FF0000", //RED
                                "endColor"  : "#419111"  //GREEN
                            }
                        }
                    )
                });
            </script>
            <?php //echo $o->ratings;?>
        </td>
        <td align="left" width="500">
           <?php echo $o->comment;?>
        </td>        
        <td align="center">
            <div class="rateCar<?php echo $count;?>"></div>
            <script>
                 $(function () {
                    $(".rateCar<?php echo $count;?>").rateYo(
                        { 
                            starWidth: "20px", 
                            rating  : <?php echo $o->car_rating;?>, 
                            ratedFill: "#419111", 
                            readOnly: true,
                            multiColor: { 
                                "startColor": "#FF0000", //RED
                                "endColor"  : "#419111"  //GREEN
                            }
                        }
                    )
                });
            </script>           
        </td>
        <td align="left" width="500">
           <?php echo $o->car_comment;?>
        </td>
        <td align="center" width="250"> 
            <?php echo date("m/d/Y h:i:s a", strtotime($o->created_at)); ?>
        </td>
        <td align="center">
            <!--<a href="<?php echo getLink("accounts","details","id",$o->carrier_id); ?>">Click Here</a>-->
            <img src="/images/icons/truck.png" alt="" title="" width="16" height="16" onclick="getCarrierData(<?php echo $o->entity_id;?>);">
        </td>
    </tr>
    <?php  $count++; } ?>
    <?php } else { ?>
    <tr class="grid-body first-row" id="row-">
        <td align="center" colspan="12">
            <? if (isset($_POST['submit'])) { ?>
            No records found.
            <? } else { ?>
            Generate report.
            <? } ?>
        </td>
    </tr>
    <?php       
        }
    ?>
</table>
</div>

</div>
</div>


<div class="ui-dialog ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-carrierdiv" style="display: block; z-index: 1002; outline: 0px; height: auto; width: 700px; top: -95735.5px; left: 321px;">
    <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix" style="user-select: none;">
        <span class="ui-dialog-title" id="ui-dialog-title-carrierdiv" style="user-select: none;">Carrier Information</span>
        <a href="#" class="ui-dialog-titlebar-close ui-corner-all" role="button" style="user-select: none;">
            <span class="ui-icon ui-icon-closethick" style="user-select: none;">close</span>
        </a>
    </div>
    <div id="carrierdiv" class="ui-dialog-content ui-widget-content" style="width: auto; min-height: 0px; height: 365.28px;">
        <div id="carrier_data">
        </div>
    </div>
</div>



@pager@


<script type="text/javascript">
    $(document).ready(function() {
    $('#customer').DataTable({
    "lengthChange": false,
    "paging": false,
    "bInfo" : false,
    'drawCallback': function (oSettings) {
     
        $("#customer_wrapper").children('.row:first').children('.col-sm-12:first').html('<div class="form-group row"><label class="col-form-label">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
        $("#customer_wrapper").children('.row:nth-child(3)').children('.col-sm-12:first').html($('.pager').clone());
        $('.pages_div').remove();
        $("#customer_wrapper").find('form-group row').css("margin-left", "1px");

    }
    });
} );
</script>

<script type="text/javascript">//<![CDATA[
    $("#users_ids").multiselect({// Build multiselect for users
        noneSelectedText: 'Select User',
        selectedText: '# users selected',
        selectedList: 1
    });

    $("#start_date, #end_date").click(function () {
        $("#ptype2").attr("checked", "checked");
    });

    $("#time_period").click(function () {
        $("#ptype1").attr("checked", "checked");
    });
    
    $(document).ready(function(){
        $.ajax({
            type: "POST",
            url: "<?php echo SITE_IN; ?>review/ajax/entities.php",
            dataType: "json",
            data: {
                action: 'getAverageRating',               
            },
            success: function (res) { 
                var num1 = Number(res.data['orderRatings']);
                var orderRatings = num1.toFixed(2);
                var num2 = Number(res.data['carrierRatings']);
                var carrierRatings = num2.toFixed(2);
                $(".avgOrder").rateYo(
                    { 
                        starWidth: "40px", 
                        rating  : orderRatings, 
                        ratedFill: "#419111", 
                        readOnly: true,
                        multiColor: { 
                            "startColor": "#FF0000", //RED
                            "endColor"  : "#419111"  //GREEN
                        }
                    }
                )
                $(".avgCarrier").rateYo(
                    { 
                        starWidth: "40px", 
                        rating  : carrierRatings, 
                        ratedFill: "#419111", 
                        readOnly: true,
                        multiColor: { 
                            "startColor": "#FF0000", //RED
                            "endColor"  : "#419111"  //GREEN
                        }
                    }
                )                
            }
        });
    });
    function getCarrierData(entityId){
        $("body").nimbleLoader('show');
        $.ajax({
                type: "POST",
                url: BASE_PATH + "application/ajax/getcarrier.php",
                dataType: "json",
                data: {
                    action: "getcarrierForReview",
                    entity_id: entityId
                },
                success: function (res) {
                   if (res.success) {
                        $("#carrier_data").html(res.carrierData);
                        $("#carrierdiv").dialog({width: 700}, 'option', 'title', 'Carrier Data').dialog("open");
                    } else {
                        alert("Can't send email. Try again later, please");
                    }
                },
                complete: function (res) {
                    $("body").nimbleLoader('hide');
                }
            });
    }
    $("#carrierdiv").dialog({
        modal: true,
        width: 900,
        height: 410,
        title: "Carrier Information",
        hide: 'fade',
        resizable: false,
        draggable: false,
        autoOpen: false,
    });   
    //]]></script>

<script type="text/javascript">
$.fn.datepicker.defaults.format = "mm/dd/yyyy";
$('#start_date,#end_date').datepicker({
});
$("#start_date").attr("autocomplete='off'");
</script>
