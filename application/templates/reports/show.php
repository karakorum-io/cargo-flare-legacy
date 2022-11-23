<?php
/*$_SESSION['start_date'] = '2016-01-01 00:00:00';
    $_SESSION['end_date'] = '2016-01-01 00:00:00';
    $_SESSION['start_date2'] = '2016-01-01';
    $_SESSION['end_date2'] = '2016-01-01';
    $_SESSION['ptype'] = 1;
    $_SESSION['users_ids'] = '';*/
?>
<!----<h3>Dashboard &amp; Reports</h3>
<div style="float:left; width:800px;">
    <?= formBoxStart() ?>
    <div id="chart1"></div>
    <div id="loadme" style="color:#BB0000;">&nbsp;</div>
    <br />
    Date Range: <strong id="date_range"><?= date("M, Y") ?></strong>
    <?= formBoxEnd() ?>
</div>
<div style="float:right; width: 365px;">
    <h3>Graphically interpret Sales report</h3>
    <table cellspacing="5" cellpadding="5" border="0">
        <tr>
            <td><input type="radio" name="ptype" value="1" id="ptype1" checked="checked" /></td>
            <td style="white-space: nowrap;"><label for="ptype1">Time Period:</label></td>
            <td>@time_period@</td>
        </tr>
        <tr>
            <td><input type="radio" name="ptype" value="2" id="ptype2" /></td>
            <td><label for="ptype2">Date Range:</label></td>
            <td>@start_date@ - @end_date@</td>
        </tr>
        <tr>
            <td>@users_ids@</td>
        </tr>
        <tr>
            <td >
                @define_as@
            </td>
        </tr>
        <tr>
            <td ><?= functionButton("Rebuild", "showDashBoard()") ?></td>
        </tr>
    </table>
    
    <div class="fdnote">
        <strong>Note:</strong> If date ranges more then 12 months Graph will show full Years periods. 
    </div>
</div>


<div style="clear:both">&nbsp;</div>---->
<style type="text/css">
    a.reports_icon {
        color: white;
    }
</style>

<h3>Company Reports</h3>
<div class="container-fluid">
    <div class="row">

        <div class="col-md-3">

            <h5>Lead, Quote & Order Reports</h5>
            <?= formBoxStart() ?>

            <div class="kt-portlet kt-portlet--bordered">
                <div class="kt-portlet__body">

                    <span class="btn btn-success" data-container="body" data-toggle="kt-tooltip" data-placement="left" title="" data-original-title="Click Me" aria-describedby="tooltip446663"><a href="<?= getLink("reports", "lead_sources") ?>" class="reports_icon">Lead Sources</a></span>

                    <!---<center><img style="width:128px;height:128px;" src="<?= SITE_IN ?>images/order_icon_5.png "  /></center>--->
                    Displays sales conversion rates and sales amounts for each lead source.

                </div>
            </div>
            <?= formBoxEnd() ?>

            <?= formBoxStart() ?>
            <div class="kt-portlet kt-portlet--bordered">
                <div class="kt-portlet__body">

                    <span class="btn btn-success" data-container="body" data-toggle="kt-tooltip" data-placement="left" title="" data-original-title="Click Me" aria-describedby="tooltip446663"><a href="<?= getLink("reports", "quotes") ?>" class="reports_icon">Quotes</a></span>

                    This report displays all quotes. Generates a .csv file containing quote data for the selected time period.

                    <?= formBoxEnd(); ?>
                </div>
            </div>

            <?= formBoxStart() ?>
            <div class="kt-portlet kt-portlet--bordered">
                <div class="kt-portlet__body">

                    <span class="btn btn-success" data-container="body" data-toggle="kt-tooltip" data-placement="left" title="" data-original-title="Click Me" aria-describedby="tooltip446663">
                        <a href="<?= getLink("reports", "orders") ?>" class="reports_icon">Orders</a></span>

                    Displays orders that became a certain status during the selected time period.

                    <?= formBoxEnd() ?>
                </div>
            </div>
            <!-----#Chetu Added Code------>

            <?= formBoxStart() ?>
            <div class="kt-portlet kt-portlet--bordered">
                <div class="kt-portlet__body">

                    <span class="btn btn-success" data-container="body" data-toggle="kt-tooltip" data-placement="left" title="" data-original-title="Click Me" aria-describedby="tooltip446663"><a href="<?= getLink("reports", "qblogs") ?>" class="reports_icon">QuickBooks Logs</a></span>

                    Displays all the records and data status of QuickBooks application.

                </div>
            </div>
            <?= formBoxEnd() ?>
            <!-----#Chetu Added Code Ends here------>

            <?= formBoxStart() ?>
            <div class="kt-portlet kt-portlet--bordered">
                <div class="kt-portlet__body">

                    <a href="<?= getLink("reports", "shipperpayments") ?>" class=" reports_icon btn btn-success">Shipper Payments History</a>

                </div>
            </div>
            <?= formBoxEnd() ?>

            <?= formBoxStart() ?>
            <div class="kt-portlet kt-portlet--bordered">
                <div class="kt-portlet__body">
                    <span class="btn btn-success" data-container="body" data-toggle="kt-tooltip" data-placement="left" title="" data-original-title="Click Me" aria-describedby="tooltip446663"><a href="<?= getLink("reports", "cancelled_orders") ?>" class="reports_icon">Cancelled Orders</a></span>
                    Cancelled orders based on date range along with the reason for cancellation. And days it cancelled after order date.
                </div>
            </div>
            <?= formBoxEnd() ?>
        </div>

        <div class="col-md-3">
            <h5>Operation Reports</h5>
            <?= formBoxStart() ?>
            <div class="kt-portlet kt-portlet--bordered">
                <div class="kt-portlet__body">


                    <span class="btn btn-success" data-container="body" data-toggle="kt-tooltip" data-placement="left" title="" data-original-title="Click Me" aria-describedby="tooltip446663">
                        <a href="<?= getLink("reports", "print_check_report") ?>" class="reports_icon">Print Check Report</a></span>

                    Displays information for printed checks report based on standard filters.

                </div>
            </div>
            <?= formBoxEnd() ?>



            <?= formBoxStart() ?>

            <div class="kt-portlet kt-portlet--bordered">
                <div class="kt-portlet__body">

                    <span class="btn btn-success" data-container="body" data-toggle="kt-tooltip" data-placement="left" title="" data-original-title="Click Me" aria-describedby="tooltip446663"><a href="<?= getLink("reports", "sales") ?>" class="reports_icon">Company Sales</a></span>

                    Displays detailed information on sales made by users on selected time period.
                    <!---(showing orders that were created during selected time period and dispatched at any time).---->

                </div>
            </div>
            <?= formBoxEnd() ?>

            <?= formBoxStart() ?>
            <div class="kt-portlet kt-portlet--bordered">
                <div class="kt-portlet__body">


                    <!--<a href="<?php //print getLink("reports", "accounts") 
                                    ?>">Accounts Payable/Receivable</a>-->
                    <span class="btn btn-success" data-container="body" data-toggle="kt-tooltip" data-placement="left" title="" data-original-title="Click Me" aria-describedby="tooltip446663">
                        <a href="<?= getLink("reports", "paynew") ?>" class="reports_icon">Detailed Payments</a>
                    </span>

                </div>
            </div>
            <?= formBoxEnd() ?>

            <?= formBoxStart() ?>
            <div class="kt-portlet kt-portlet--bordered">
                <div class="kt-portlet__body">
                    <span class="btn btn-success" data-container="body" data-toggle="kt-tooltip" data-placement="left" title="" data-original-title="Click Me" aria-describedby="tooltip446663">
                        <a href="<?= getLink("reports", "payment_received") ?>" class="reports_icon">Payments Received</a>
                    </span>
                    Displays detailed information where payments have been received to the company.
                </div>
            </div>
            <?= formBoxEnd() ?>

            <?= formBoxStart() ?>

            <div class="kt-portlet kt-portlet--bordered">
                <div class="kt-portlet__body">

                    <span class="hint--left hint--rounded hint--bounce hint--error" data-hint="Coming soon"><? php/*<a href="<?= getLink() ?>">*/ ?>Credit Card Transactions <strong>
                            <font color="red">COMING SOON</font>
                        </strong></a></span>
                    Displays orders where credit cards have been processed with detail information.

                </div>
            </div>
            <?= formBoxEnd() ?>

            <?php echo formBoxStart() ?>

            <div class="kt-portlet kt-portlet--bordered">
                <div class="kt-portlet__body">

                    <span class="btn btn-success" data-container="body" data-toggle="kt-tooltip" data-placement="left" title="" data-original-title="Click Me" aria-describedby="tooltip446663">
                        <a href="<?= getLink("reports", "arReport") ?>" class="reports_icon">AR Report</a>
                    </span>

                    Displays the AR report and also exports the report to Excel.
                </div>
            </div>

            <?php echo formBoxEnd() ?>

        </div>


        <div class="col-md-3">
            <h5>Customer Review Reports</h5>
            <?php echo formBoxStart() ?>

            <div class="kt-portlet kt-portlet--bordered">
                <div class="kt-portlet__body">

                    <span class="btn btn-success" data-container="body" data-toggle="kt-tooltip" data-placement="left" title="" data-original-title="Click Me" aria-describedby="tooltip446663">
                        <a href="<?= getLink("reports", "review") ?>" class="reports_icon">Customer Reviews Report</a>
                    </span>

                </div>
            </div>

            <?php echo formBoxEnd() ?>
        </div>

        <div class="col-md-3">

            <h5>Shipper & Carrier Data Reports</h5>
            <?= formBoxStart() ?>
            <div class="kt-portlet kt-portlet--bordered">
                <div class="kt-portlet__body">

                    <span class="btn btn-success" data-container="body" data-toggle="kt-tooltip" data-placement="left" title="" data-original-title="Click Me" aria-describedby="tooltip446663">
                        <a href="<?= getLink("reports", "shippers", "order", "tariff", "arrow", "desc") ?>" class="reports_icon">Shipper Information</a></span>

                    Shippers report displays all customers up to date and can be additionally filtered by Customer's name.

                </div>
            </div>
            <?= formBoxEnd() ?>

            <?= formBoxStart() ?>
            <div class="kt-portlet kt-portlet--bordered">
                <div class="kt-portlet__body">
                    <span class="btn btn-success" data-container="body" data-toggle="kt-tooltip" data-placement="left" title="" data-original-title="Click Me" aria-describedby="tooltip446663"><a href="<?= getLink("reports", "carriers") ?>" class="reports_icon">Carrier Information</a></span>

                    Carriers report displays all vendors up to date and can be additionally filtered by Company's name.
                </div>
            </div>
            <?= formBoxEnd() ?>

            <?= formBoxStart() ?>
            <div class="kt-portlet kt-portlet--bordered">
                <div class="kt-portlet__body">

                    <span class="hint--left hint--rounded hint--bounce hint--success" data-hint="Click Here">
                        <a href="<?= getLink("reports", "on_time") ?>" style="padding-left: 50px; padding-right:64px" class=" reports_icon btn btn-success">Carrier Performance</a></span>
                    Displays detailed information on carriers loading & delivery .
                </div>
            </div>
            <?= formBoxEnd() ?>

            <?= formBoxStart() ?>
            <div class="kt-portlet kt-portlet--bordered">
                <div class="kt-portlet__body">
                    <span class="btn btn-success" data-container="body" data-toggle="kt-tooltip" data-placement="left" title="" data-original-title="Click Me" aria-describedby="tooltip446663">
                        <a href="<?= getLink("reports", "dispatchActivity") ?>" class="reports_icon">Dispatch Activity</a>
                    </span>

                    Displays information on dispatched orders to carriers on selected time period.
                </div>
            </div>
            <?= formBoxEnd() ?>

            <?= formBoxStart() ?>
            <div class="kt-portlet kt-portlet--bordered">
                <div class="kt-portlet__body">
                    <span class="btn btn-success" data-container="body" data-toggle="kt-tooltip" data-placement="left" title="" data-original-title="Click Me" aria-describedby="tooltip446663">
                        <a href="<?= getLink("reports", "daily_dispatch_hourly_report") ?>" class="reports_icon">Dispatch Dispatch Hourly Report</a>
                    </span>

                    Displays information on dispatched orders to carriers on selected time period.
                </div>
            </div>
            <?= formBoxEnd() ?>
        </div>

    </div>
</div>


<script type="text/javascript">
    //<![CDATA[
    function showDashBoard() {
        /*Show loading*/
        $("#loadme").html("Loading...");
        /* Get vars */
        var ids = $("#users_ids").val(); /* Users IDs */
        var time_period = $("#time_period").val(); /* Time frames */
        var start_date = $("#start_date").val(); /* Start Date */
        var end_date = $("#end_date").val(); /* End Date */
        var define_as = $('input[name=define_as]:checked').val(); /* how to define Dispatched Orders */
        var ptype = $('input[name=ptype]:checked').val(); /* Tipe of date range/frames */
        /* Set default data */
        var leads = [0]; /* Qty leads X-axis */
        var quotes = [0]; /* Qty quotes X-axis */
        var orders = [0]; /* Qty orders X-axis */
        var ticks = ['<?= date("F Y") ?> (No report data found)']; /* Periods Y-axis*/
        var range = '<?= date("M, Y") ?>'; /* By default Current Month */
        /* Get data */
        $.ajax({
            url: '<?= getLink("reports", "graph") ?>',
            data: {
                action: "get",
                users_ids: ids,
                time_period: time_period,
                start_date: start_date,
                end_date: end_date,
                define_as: define_as,
                ptype: ptype
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {},
            success: function(retData) {
                if (retData.success && retData.error == "") {
                    /* Check errors */
                    leads = retData.leads;
                    quotes = retData.quotes;
                    orders = retData.orders;
                    ticks = [];
                    ticks = retData.ticks;
                    range = retData.range;
                    buildGraph(leads, quotes, orders, ticks, range); /* Build Graph */
                } else {
                    buildGraph(leads, quotes, orders, ticks, range);
                    if (retData.error != "") {
                        alert(retData.error); /* Show errors */
                    }
                }
                $("#loadme").html("&nbsp;");
            },
            complete: function() {}
        });
    }

    function buildGraph(leads, quotes, orders, ticks, range) {
        /* Setup Graph */
        $("#date_range").html(range);
        var plot1 = $.jqplot('chart1', [leads, quotes, orders], {
            /* Show Graph under chart1 DIV */
            title: 'Sales',
            seriesDefaults: {
                renderer: $.jqplot.BarRenderer,
                rendererOptions: {
                    fillToZero: true
                },
                pointLabels: {
                    show: true
                }
            },
            series: [{
                label: 'Leads'
            }, {
                label: 'Quotes'
            }, {
                label: 'Orders'
            }],
            legend: {
                show: true
            },
            axes: {
                xaxis: {
                    renderer: $.jqplot.CategoryAxisRenderer,
                    ticks: ticks
                },
                yaxis: {
                    pad: 1.1,
                    tickOptions: {
                        formatString: '%d'
                    }
                }
            }
        }).replot();
    }

    $(document).ready(function() {

        $("#start_date, #end_date").click(function() {
            /* Set type of date range/frames */
            $("#ptype2").attr("checked", "checked");
        });

        $("#time_period").click(function() {
            /* Set type of date range/frames */
            $("#ptype1").attr("checked", "checked");
        });

        $("#users_ids").each(function() {
            /* Select all users by default */
            $("#users_ids option").attr("selected", "selected");
        });

        $("#users_ids").select2({
            /* Build multiselect for users */
            noneSelectedText: 'Select User',
            selectedText: '# users selected',
            selectedList: 1
        });

        showDashBoard(); /* Build Graph */
    });
    //]]>
</script>