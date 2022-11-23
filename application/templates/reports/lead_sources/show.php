<script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.tablesorter.js"></script>
<style type="text/css">
    .form-box-buttons {
    display: inline-block;
}
table.table.table-bordered.qoute_report {
    font-size: 13px;
}
h2 {
    margin-bottom: 22px;
}
</style>


<div class="quote-info accordion_main_info_new">
    <div class="row">           
        <div class="col-12">
            <div class="kt-portlet kt-portlet--height-fluid">
                <div class="kt-portlet__head" id="accordion_title">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            Lead Sources Report
                        </h3>
                    </div>
                </div>
                <div class="kt-portlet__body accordion_info_content_new accordion_info_content_open">

                    <div class="row">
                        <?= formBoxStart() ?>

                        <form action="<?= getLink("reports", "lead_sources") ?>" method="post"  class="kt-form">
                            <div class="kt-portlet__body">
                               

                                <div class="form-group lead_sources select_opt_new_info">
                                    @ls_ids[]@
                                </div>

                                <div class="form-group">
                                    <label for="ptype1" class="kt-radio kt-radio--brand" >
                                        <input type="radio" name="ptype" value="1" id="ptype1" @ptype1ch@ />Time Period:
                                        <span></span>
                                    </label>
                                    @time_period@
                                </div>

                                <div class="form-group">
                                    <div class="row">

                                        <div class="col-md-2">
                                            <label  for="ptype2" class="kt-radio kt-radio--brand" >
                                                <input type="radio" name="ptype" value="2" id="ptype2" @ptype2ch@ />Date Range:
                                                <span></span>
                                            </label>
                                        </div>

                                        <div class="col-md-3">
                                            @start_date@
                                        </div>
                                        <div class="col-md-3">
                                            @end_date@
                                        </div>
                                    </div>
                                </div>


                                    <div class="row">
                                        <div class="col-md-8">
                                            <?= submitButtons("", "Generate") ?>
                                            <?= exportButton("Export to Excel",'btn-sm btn_dark_green') ?>
                                        </div>
                                    </div>
                                </form>
                                    <?= formBoxEnd() ?>
                                </div>

                            </div>

                            <div class="row">

                                <div class="kt-portlet__body">
                                    <div cclass="kt-section">
                                        <div class="kt-section__content">
                                            <table class="table table-bordered qoute_report">
                                                <thead>
                                                    <tr >
                                                        <th >Name</th>
                                                        <th>Leads</th>
                                                        <th>Quotes</th>
                                                        <th>Orders</th>
                                                        <th>Conv Rate</th>
                                                        <th>Dispatched</th>
                                                        <th>Tariffs</th>
                                                        <th>Carrier Pay</th>
                                                        <th>Terminal Fees</th>
                                                        <th>Gross Profit</th>
                                                        <th>Profit Margin</th>
                                                        <th >Average Profit per Order</th>
                                                    </tr>
                                                </thead>
                                                <? if (count($this->lss) > 0) { ?>
                                                    <tbody>
                                                        <? foreach ($this->lss as $i => $ls) { ?>
                                                            <tr class="grid-body<?= ($i == 0 ? " first-row" : "") ?>">
                                                                <td style="white-space: nowrap;" class="grid-body-left"><?= htmlspecialchars($ls['name']); ?></td>
                                                                <td ><?= $ls["leads"] ?></td>
                                                                <td ><?= $ls["quotes"] ?></td>
                                                                <td ><?= $ls["orders"] ?></td>
                                                                <td align="center"><?= number_format($ls["conv_rate"], 2); ?>%</td>
                                                                <td ><?= $ls["dispatched"] ?></td>
                                                                <td >$<?= number_format($ls["tariffs"], 2); ?></td>
                                                                <td >$<?= number_format($ls["carrier_pay"], 2); ?></td>
                                                                <td >$<?= number_format($ls["terminal_feesP"], 2); ?></td>
                                                                <td >$<?= number_format($ls["gross_profit"], 2); ?></td>
                                                                <td align="center"><?= number_format($ls["profit_margin"], 2); ?>%</td>
                                                                <td  class="grid-body-right">$<?= number_format($ls["average_profit"], 2); ?></td>
                                                            </tr>
                                                        <? } ?>
                                                    </tbody>
                                                    <? $t = $this->totals; ?>
                                                    <tr class="grid-body totals">
                                                        <td style="white-space: nowrap;" class="grid-body-left">TOTALS</td>
                                                        <td ><?= $t["leads"] ?></td>
                                                        <td ><?= $t["quotes"] ?></td>
                                                        <td ><?= $t["orders"] ?></td>
                                                        <td align="center"><?= number_format($t["conv_rate"], 2); ?>%</td>
                                                        <td ><?= $t["dispatched"] ?></td>
                                                        <td >$<?= number_format($t["tariffs"], 2); ?></td>
                                                        <td >$<?= number_format($t["carrier_pay"], 2); ?></td>
                                                        <td >$<?= number_format($t["terminal_fees"], 2); ?></td>
                                                        <td >$<?= number_format($t["gross_profit"], 2); ?></td>
                                                        <td ><?= number_format($t["profit_margin"], 2); ?>%</td>
                                                        <td  class="grid-body-right">$<?= number_format($t["average_profit"], 2); ?></td>
                                                    </tr>
                                                <? } else { ?>
                                                    <tr  id="row-">
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


                            </div>



                        </div>
                    </div>              
                </div>
            </div>
        </div>




<script type="text/javascript">
$.fn.datepicker.defaults.format = "mm/dd/yyyy";
$('#start_date,#end_date').datepicker({
});

$("#start_date").attr("autocomplete='off'")
</script>

<script type="text/javascript">
    $(document).ready(function() {
    $('#ls_ids').select2();
});
</script>

<script type="text/javascript">
     $(document).ready(function() {
     $('#ls_ids').multiselect();

});
</script>
<script type="text/javascript">//<![CDATA[
    
//    $("#ls_ids").each(function(){ // Select all users by default
//        $("#ls_ids option").attr("selected", "selected");
//    });
    $("#ls_ids").multiselect({ // Build multiselect for users
        noneSelectedText: 'Select Lead Source',
        selectedText: '# sources selected',
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