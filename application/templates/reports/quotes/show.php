


<!--  -->
<style type="text/css">
    tr.table_views {
    font-size: 12px;
}

</style>
<div class="quote-info accordion_main_info_new">
    <div class="row">           
        <div class="col-12">
            <div class="kt-portlet kt-portlet--height-fluid">
                <div class="kt-portlet__head" id="accordion_title">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                           Quotes Report
                        </h3>
                    </div>
                </div>
                <div class="kt-portlet__body accordion_info_content_new accordion_info_content_open">
                    <div class="row">
                        <div class="col-12 col-sm-8">
        <form action="<?= getLink("reports", "quotes")?>" method="post" class="kt-form">

        <?= formBoxStart() ?>
          
                 
    <div class="form-group">
        @users_ids[]@
    </div>


    <div class="form-group">

        <label class="kt-radio kt-radio--brand" >
        <input type="radio" name="ptype" value="1" id="ptype1" @ptype1ch@ />Time Period:
        <span></span>
        </label>
         @time_period@
    </div>



    <div class="row">
        <div class="col-4 col-sm-2">

        <div class="form-group">
        <label class="kt-radio kt-radio--brand" >
        <input type="radio" name="ptype" value="2" id="ptype2" @ptype2ch@ / >Date Range:
        <span></span>
        </label>
        </div>

        </div>

        <div class="col-4 col-sm-4">
            @start_date@
        </div>
        <div class="col-4 col-sm-4">
            @end_date@
        </div>

    </div>

    <div class="row">
        <div class="col-2 mb-3">
            <?= submitButtons("", "Generate") ?>
        </div>
        <div class="col-3">
            <?= exportButton("Export to Excel",'btn-sm btn_dark_green') ?>
        </div>
    </div>
    </form>
<?= formBoxEnd() ?>

</div>
</div>

<div class="col-12 col-sm-12">
@pager@
</div>



         <div class="row">
         <div class="col-12 col-sm-12">

<div class="kt-portlet__body">
    <div class="kt-section">
<div class="kt-section__content">
   <table id="quote_id" class="table table-bordered" >
    <thead>
        <tr class="table_views">
            <th class="grid-head-left"><?= $this->order->getTitle("id", "ID"); ?></th>
            <th style="white-space: nowrap">
                <?= $this->order->getTitle("assigned_id", "Assigned To"); ?><br />
                (<?= $this->order->getTitle("before_assigned_id", "Before Quote"); ?>)
            </th>
            <th><?= $this->order->getTitle("quoted", "Quote<br />date"); ?></th>
            <th>
                <?= $this->order->getTitle("origin_id", "Pickup"); ?><br />
                <?= $this->order->getTitle("destination_id", "Dropoff"); ?>
            </th>
            <th><?= $this->order->getTitle("est_ship_date", "Estimated<br />Ship date"); ?></th>
<!--            <th>--><?//= $this->order->getTitle("vehicles_run", "Inop"); ?><!--</th>-->
            <th><?= $this->order->getTitle("ship_via", "Ship Via"); ?></th>
            <th><?= $this->order->getTitle("Shipper", "Shipper"); ?></th>
            <th><?= $this->order->getTitle("referred_by", "Referrer"); ?></th>
            <th><?= $this->order->getTitle("source_id", "Lead Source"); ?></th>
            <th>Vehicles</th>
            <th><?= $this->order->getTitle("tariff_total", "Tariff"); ?></th>
            <th class="grid-head-right"><?= $this->order->getTitle("total_deposit", "Deposit Required"); ?></th>
        </tr>
    </thead>
    <? if (count($this->quotes) > 0) { ?>
        <tbody>
            <? foreach ($this->quotes as $i => $q) { ?>
                <tr class="grid-body<?= ($i == 0 ? " " : "") ?>">
                    <td style="white-space: nowrap;" class="grid-body-left"><?= $q->id ?></td>
                    <td align="left">
                        <?= $q->getAssigned()->username ?><br />
                        <?= $q->before_assigned_id == "" ? "" : "(" . $q->getBeforeAssigned()->username . ")" ?>
                    </td>
                    <td align="center"><?= $q->getQuoted("m/d/Y h:i A"); ?></td>
                    <td align="left">
                        <?= formatAddress("", "", strtoupper($q->getOrigin()->city), $q->getOrigin()->state, $q->getOrigin()->zip) ?> <br />-><br /> 
                        <?= formatAddress("", "", strtoupper($q->getDestination()->city), $q->getDestination()->state, $q->getDestination()->zip) ?>
                    </td>
                    <td align="center"><?= $q->getShipDate("m/d/Y") ?></td>
                    <td align="center"><?= $q->getInopName() ?></td>
                    <td align="left">
                        <?= $q->getShipper()->fname ?>
                        <?= $q->getShipper()->lname ?><br />
                        <?= trim($q->getShipper()->company) == "" ? "" : $q->getShipper()->company . "<br />" ?>
                        <?= trim($q->getShipper()->email) == "" ? "" : $q->getShipper()->email . "<br />" ?>
                        <?= trim($q->getShipper()->phone1) == "" ? "" : "ph.: " . $q->getShipper()->phone1 . "<br />" ?>
                        <?= trim($q->getShipper()->phone2) == "" ? "" : "alt.: " . $q->getShipper()->phone2 . "<br />" ?>
                        <?= trim($q->getShipper()->mobile) == "" ? "" : "cell: " . $q->getShipper()->mobile . "<br />" ?>
                        <?= trim($q->getShipper()->fax) == "" ? "" : "fax: " . $q->getShipper()->fax . "<br />" ?>
                        <?= formatAddress($q->getShipper()->address1, $q->getShipper()->address2, $q->getShipper()->city, $q->getShipper()->state, $q->getShipper()->zip, $q->getShipper()->country); ?>
                    </td>
                    <td align="center"><?= $q->referred_by ?></td>
                    <td align="center"><?= $q->getSource()->name ?></td>
                    <td align="left"><?= $q->printVehicles() ?></td>
                    <td style="white-space: nowrap;" align="right"><?= $q->getTotalTariff(true) ?></td>
                    <td style="white-space: nowrap;" align="right" class="grid-body-right"><?= $q->getTotalDeposit() ?></td>
                </tr>
            <? } ?>
        </tbody>
    <? } else { ?>
        <tr class="grid-body " id="row-">
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
                    
                 
                    
                   
                 
                                       
                    

                    
                   
                </div>
            </div>              
        </div>
    </div>
</div>
    


<!--  -->



<script type="text/javascript">
    $(document).ready(function() {
    $('#users_ids').select2();
});
</script>

<script type="text/javascript">
$.fn.datepicker.defaults.format = "mm/dd/yyyy";
$('#start_date,#end_date').datepicker({
});

$("#start_date").attr({'autocomplete':'off', 'autocorrect': 'off','autocorrect': 'false'});
$("#end_date").attr({'autocomplete':'off', 'autocorrect': 'off','autocorrect': 'false'})
</script>
  
<script type="text/javascript">//<![CDATA[
    
    $("#start_date, #end_date").click(function(){
        $("#ptype2").attr("checked", "checked");
    });

    $("#time_period").click(function(){
        $("#ptype1").attr("checked", "checked");
    });
    //]]></script>

     <script type="text/javascript">
        $(document).ready(function() {
        $('#quote_id').DataTable({
        "lengthChange": false,
        "paging": false,
        "bInfo" : false,
        'drawCallback': function (oSettings) {

        $("#quote_id_wrapper").children('.row:first').children('.col-sm-12:first').html('<div class="form-group row"><label class="col-form-label">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
        $("#quote_id_wrapper").children('.row:nth-child(3)').children('.col-sm-12:first').html($('.pager').clone());
        $('.pages_div').remove();
       
        }
        });
        } );
        </script>