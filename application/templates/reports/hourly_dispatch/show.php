<style>
    #graph-div{
        width:100%;
        height:500px;
        border:1px solid #ccc;
    }
</style>

<div class="kt-portlet">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3>Daily dispatch hourly report</h3>
        </div>
    </div>
    <div class="kt-portlet__body">
        <?php echo formBoxStart() ?>
        <form action="<?php echo getLink("reports", "daily_dispatch_hourly_report") ?>" method="post" />
        <div class="row">
            <div class="col-10">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="ptype1">Select Month:</label>
                            @select_month@
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="ptype1">Select Year:</label>
                            @select_year@
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-10">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-4">
                                    <button type="submit" class="btn btn-warning" name="submit">Submit</button>
                                </div>
                                <div class="col-4">
                                    <button type="submit" class="btn btn-primary" name="export_csv">Export (CSV)</button>
                                </div>
                                <div class="col-4">
                                    <button type="submit" class="btn btn-danger" name="plot_graph">Plot Graph</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php echo formBoxEnd() ?>

        <div class="row mt-3">
            <div class="col-12">
                <table id="report" class="table table-bordered">
                </table>
            </div>
        </div>
    </div>
</div>

<div style="clear:both">&nbsp;</div>
<?php
    $months = array(
        '01'=>"January",
        '02'=>"Feburary",
        '03'=>"March",
        '04'=>"April",
        '05'=>"May",
        '06'=>"June",
        '07'=>"July",
        '08'=>"August",
        '09'=>"September",
        '10'=>"October",
        '11'=>"November",
        '12'=>"December",
    );
?>
<p>Report For: </p>
<h4>Month: <?php echo $months[$this->month];?> Year: <?php echo $this->year;?></h4>
<?php
    if($this->plot_graph){
?>
<link href="<?php echo getLink('templates','reports','hourly_dispatch')?>/css/default.css" rel="stylesheet">
<div class="chart-container">
    <canvas id="line-chartcanvas"></canvas>
</div>
<script src="<?php echo getLink('templates','reports','hourly_dispatch')?>/js/jquery.min.js"></script>
<script src="<?php echo getLink('templates','reports','hourly_dispatch')?>/js/Chart.min.js"></script>
<br/><br/>
<script>
    $(document).ready(function() {
        var ctx = $("#line-chartcanvas");
        var data = {
            labels : [
                <?php
                    for($i=1;$i<=12;$i++){
                        $from = $i;
                        $from = str_pad($from, 2, '0', STR_PAD_LEFT)." AM";

                        $to = $i + 1;
                        if($to > 12){
                            $to = 01;
                        }
                        $to = str_pad($to, 2, '0', STR_PAD_LEFT)." ".( ($i+1) == 13 ? "PM" : "AM");
                        echo "'".$from."-".$to."',";
                    }
                ?>
                <?php
                    for($i=1;$i<=12;$i++){
                        $from = $i;
                        $from = str_pad($from, 2, '0', STR_PAD_LEFT)." PM";

                        $to = $i + 1;
                        if($to > 12){
                            $to = 01;
                        }
                        $to = str_pad($to, 2, '0', STR_PAD_LEFT)." ".( ($i+1) == 13 ? "AM" : "PM");
                        echo "'".$from."-".$to."',";
                    }
                ?>
            ],
            datasets : [
                <?php
                    foreach($this->data as $key => $value){    
                ?>
                {
                    label : "<?php echo $this->month."-".$key."-".$this->year;?>",
                    data : [
                        <?php
                        foreach($value['hours'] as $k => $v){
                            echo $v.",";
                        }    
                        ?>
                    ],
                    <?php $color = str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT).str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT).str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);?>
                    backgroundColor : "#<?php echo $color;?>",
                    borderColor : "#<?php echo $color;?>",
                    fill : false,
                    lineTension : 0,
                    pointRadius : 3
                },
                <?php
                    }
                ?>
            ]
        };

        var options = {
            title : {
                display : true,
                position : "top",
                text : "Hourly dispatch Report for Month <?php echo $months[$this->month]?> and year <?php echo $this->year?> ",
                fontSize : 12,
                fontColor : "#111"
            },
            legend : {
                display : true,
                position : "bottom"
            }
        };

        var chart = new Chart( ctx, {
            type : "line",
            data : data,
            options : options
        } );

    });
</script>
<?php
    } else {
?>
<div id="" style="overflow: auto; height:400px;">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th rowspan="2" width="80px;">Day</th>
                <th colspan="12">AM</th>
                <th colspan="12">PM</th>
                <th rowspan="2" width="80px;">Rate</th>
                <th rowspan="2" width="80px;">Net</th>
            </tr>
            <tr>
                <?php
                    for($i=1;$i<=12;$i++){
                        $from = $i;
                        $from = str_pad($from, 2, '0', STR_PAD_LEFT);
                        $to = $i + 1;
                        if($to > 12){
                            $to = 01;
                        }
                        $to = str_pad($to, 2, '0', STR_PAD_LEFT);
                ?>
                <th><?php echo $from;?> - <?php echo $to;?></th>
                <?php
                    }
                ?>
                <?php
                    for($i=1;$i<=12;$i++){
                        $from = $i;
                        $from = str_pad($from, 2, '0', STR_PAD_LEFT);
                        $to = $i + 1;
                        if($to > 12){
                            $to = 01;
                        }
                        $to = str_pad($to, 2, '0', STR_PAD_LEFT);
                ?>
                <th><?php echo $from;?> - <?php echo $to;?></th>
                <?php
                    }
                ?>
            </tr>
        </thead>
        <?php
            $i = 0;
            $grand_total_dispatch = 0;
            $daily_average = 0;
            foreach($this->data as $key => $value){
                if (($i % 2) == 0) {$bgcolor = "#ffffff";} else { $bgcolor = "#cccccc";}
        ?>
        <tr>
            <td align="center"><?php echo $key;?></td>
            <?php
                $hourly_count = 0;
                foreach($value['hours'] as $k => $v){
                    if($v == 0){
                        // nothing to do
                    } else {
                        $hourly_count++;
                    }
            ?>
            <td align="center"><?php echo $v==0? "-" : $v;?></td>
            <?php
                }
            ?>
            <td align="right"><?php echo number_format(($value['count']/$hourly_count),2);?></td>
            <td align="right"><?php echo $value['count'];?></td>
        </tr>
        <?php
            $grand_total_dispatch = $grand_total_dispatch + $value['count'];
            $i++;
            }
        ?>
        <?php
            if (($i % 2) == 0) {$bgcolor = "#ffffff";} else { $bgcolor = "#cccccc";}
        ?>
        <tr>
            <td align="left"><b>Totals</b></td>
            <?php
                $net_day_total = 0;
                foreach($this->hourly_total as $key => $value){
                    $net_day_total = $net_day_total + $value;
            ?>
            <td align="center"><?php echo $value == 0 ? "-" : $value?></td>
            <?php
                }
            ?>
            <td colspan="2" align="right"><?php echo $net_day_total;?></td>
        </tr>
        <?php
            $i++;
            if (($i % 2) == 0) {$bgcolor = "#ffffff";} else { $bgcolor = "#cccccc";}
        ?>
        <tr>
            <td align="left"><b>Deposit</b></td>
            <?php
            $net_deposit = 0;
                foreach($this->hourly_deposit as $key => $value){
                    $net_deposit = $net_deposit + $value;
            ?>
            <td align="right"><?php echo $value == 0 ? "-" : "$".$value?></td>
            <?php
                }
            ?>
            <td colspan="2" align="right">$<?php echo $net_deposit;?></td>
        </tr>
        <?php
            $i++;
            if (($i % 2) == 0) {$bgcolor = "#ffffff";} else { $bgcolor = "#cccccc";}
        ?>
        <tr>
            <td colspan="24">&nbsp;</td>
            <td align="left" colspan="2"><b>Per Day</b></td>
            <td align="right"><?php echo $daily_average = number_format(($grand_total_dispatch/$this->last_day),2);?></td>
        </tr>
    </table>
</div>
<?php
    }
?>