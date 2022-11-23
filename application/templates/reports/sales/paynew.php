<div class="modal fade" id="detaildiv" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">CargoFlare</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times" aria-hidden="true"></i></button>
            </div>
            <div class="modal-body">
                <div id="detail_data"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.tablesorter.js"></script>

<style>
    .grid-head-green {
        font-weight: normal;
        text-align: center;
        color: white !important;
        border: none;
        border-right: 1px solid #3D9B44;
        font-size: 12px;
        white-space: nowrap;
        background: #3D9B44 !important;
    }

    .grid-head-red {
        font-weight: normal;
        text-align: center;
        color: white !important;
        border: none;
        border-right: 1px solid #C25C5C;
        font-size: 12px;
        background: #C25C5C;
    }
</style>

<div class="kt-portlet">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                Payments Report
            </h3>
        </div>
    </div>
    <div class="kt-portlet__body">
        <?=formBoxStart()?>
        <form action="<?=getLink("reports", "paynew")?>" method="post" />
            <div class="row">
                <div class="col-10">
                    <div class="row">
                        <div  class="col-9">
                            <div class="form-group">
                                <label for="ptype1" class="kt-radio kt-radio--brand">
                                    <input type="radio" name="ptype" value="1" id="ptype1" @ptype1ch@>Time Period:
                                    <span></span>
                                </label>
                                @time_period@
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-10">
                    <div class="row">
                        <div  class="col-6">
                            <div class="form-group">
                                <label for="ptype2" class="kt-radio kt-radio--brand">
                                    <input type="radio" name="ptype" value="2" id="ptype2" @ptype2ch@>Date Range:
                                    <span></span>
                                </label>
                                <div  class="row">
                                    <div class="col-6">
                                        @start_date@
                                    </div>
                                    <div class="col-6">
                                        @end_date@
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div  class="col-6 mt-3">
                            <div  class="row  mt-4">
                                <div class="col-3">
                                    <?=submitButtons("", "Generate")?>
                                </div>
                                <div class="col-6">
                                <?=exportButton("Export to Excel", 'btn-sm  btn_dark_green')?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?=formBoxEnd()?>
        <div  class="row">
            <div class="col-12">
                <h2 style="color: #374afb;">Detailed Payments Report</h2>
                <strong>To view more detailed information please click on "Payment's In" or "Payment's Out" amounts.</strong>
                <br>
                <br>
                <table class="table table-bordered table-striped" id="lsTable">
                    <thead>
                        <tr>
                            <th rowspan="2"  align="center" class="grid-head-left">Date Received</th>
                            <th rowspan="2"   align="center" >Received Day</th>
                            <th colspan="5"   align="center" class="grid-head-green">Account Recieveable</th>
                            <th colspan="3"   align="center" class="grid-head-red">Account Payable</th>
                            <th rowspan="2"   align="right" >Difference</th>
                        </tr>
                        <tr>
                            <th align="right" width="10%" class="grid-head-green" >Number of Payments</th>
                            <th align="right" width="10%"  class="grid-head-green">GP%</th>
                            <th align="right" width="10%"  class="grid-head-green">Payment(s) In</th>
                            <th align="right" width="10%"  class="grid-head-green">Broker Fee(s)</th>
                            <th align="right" width="10%"  class="grid-head-green">Carrier Fee(s)</th>

                            <th align="right" width="10%" class="grid-head-red">Number of Payments</th>
                            <th align="right" width="10%" class="grid-head-red">Carrier(s) Payments</th>
                            <th align="right" width="10%" class="grid-head-red">Refund(s) Processed</th>
                        </tr>
                    </thead>
                    <? if (count($this->sales) > 0) { ?>
                    <tbody>

                        <?
                            $AR_Pay_In = 0;
                            $AP_Pay_Out =0;
                            $difference=0;

                            foreach ($this->sales as $i => $ls) { 
                        ?>
                        
                        <tr class="grid-body<?=($i == 0 ? " " : "")?>">
                            <td style="white-space: nowrap;" class="grid-body-left" align="center"><?=htmlspecialchars($ls['date_received']);?></td>
                            <td align="center"><?=$ls["date_received_day"]?></td>
                            
                            <?php
                                if ($ls['AR_Pay_InTotalTariff'] != 0 && $ls['AR_Pay_InTotalDeposit'] != 0) {
                                    $gp_per = ($ls['AR_Pay_InTotalDeposit'] / $ls['AR_Pay_InTotalTariff']) * 100;
                                }
                            ?>

                            <td align="right"><?=$ls['AR_NoOfPayment'];?></td>
                            <td align="right"><?=number_format($gp_per, 2);?></td>
                            <td align="right"><a href="javascript:void(0);" onclick="showDetails('<?php print $ls['date_received'];?>','In');">$<?=number_format($ls["AR_Pay_In"], 2);?></a></td>
                            <td align="right">$<?=number_format(($ls['AR_Pay_InTotalDeposit']), 2);?></td>
                            <td align="right">$<?=number_format(($ls['AR_Pay_InCarrier_pay']), 2);?></td>
                            <td align="right"><?=$ls['AP_NoOfPayment'];?></td>
                            <td align="right"><a href="javascript:void(0);" onclick="showDetails('<?php print $ls['date_received'];?>','Out');">$<?=number_format($ls["AP_Pay_OutCarrier_pay"], 2);?></a></td>
                            <td align="right">$0.00<?php //print number_format(($ls['AP_Pay_OutTotalDeposit']), 2); ?></td>
                            <td align="right">$<?=number_format(($ls['AR_Pay_In'] - $ls['AP_Pay_Out']), 2);?></td>
                        </tr>
                        <?
                            $AR_Pay_In += $ls['AR_Pay_In'];
                            $Vehicle_In += $ls['AR_NoOfPayment'];
                            $Deposit_In += $ls['AR_Pay_InTotalDeposit'];
                            $Carrier_In += $ls['AR_Pay_InCarrier_pay'];
                            $AP_Pay_Out +=$ls['AP_Pay_Out'];
                            $Vehicle_Out += $ls['AP_NoOfPayment'];
                            $Carrier_Out += $ls['AP_Pay_OutCarrier_pay'];
                            $difference +=($ls['AR_Pay_In'] - $ls['AP_Pay_Out']);
                            } 
                        ?>
                    </tbody>
                    <? $t = $this->totals; ?>
                    <tr class="totals">
                        <td align="center" style="white-space: nowrap;" class="grid-body-left">Totals</td>
                        <td align="right">&nbsp;</td>
                        <td align="right"><?=$Vehicle_In;?></td>
                        <td align="right"></td>
                        <td align="right">$<?=number_format($AR_Pay_In, 2);?> </td>
                        <td align="right">$<?=number_format($Deposit_In, 2);?> </td>
                        <td align="right">$<?=number_format($Carrier_In, 2);?> </td>
                        <td align="right"><?=$Vehicle_Out;?></td>
                        <td align="right">$<?=number_format($AP_Pay_Out, 2);?></td>

                        <td align="right">$0.00<?php //print number_format($Carrier_Out, 2); ?></td>
                        <td align="right">$<?=number_format($difference, 2);?></td>
                    </tr>
                    <? } else { ?>
                    <tr class="" id="row-">
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
        <br>
        <br>
        <strong>To view more detailed information please click on "Payment's In" or "Payment's Out" amounts.</strong>
    </div>
</div>

<script type="text/javascript">

    // $("#users_ids").multiselect({
    //     noneSelectedText: 'Select User',
    //     selectedText: '# users selected',
    //     selectedList: 1
    // });

    $("#start_date, #end_date").click(function(){
        $("#ptype2").attr("checked", "checked");
    });

    $("#time_period").click(function(){
        $("#ptype1").attr("checked", "checked");
    });

    $.fn.datepicker.defaults.format = "mm/dd/yyyy";

    $("#start_date").datepicker({
        endDate: "dateToday"
    });

    $("#end_date").datepicker({
        startDate: "dateToday"
    });

    $("#start_date,#end_date").attr({'autocomplete': 'off','autocorrect': 'off', 'spellcheck': 'false'})
    
    $(document).ready(function() {
        $('#lsTable').DataTable({});
    });

    function showDetails($ddate,ptype) {

        var user_id = "";
        var userStr = "";
        var user_ids = [];

        if($("#users_ids option:selected").length > 0){

            $("#users_ids option:selected").each(function(){
                user_id = $(this).val();
                user_ids.push(user_id);
            });

            userStr = user_ids.join(",");
        }

        $.ajax({
            type: "POST",
            url: BASE_PATH + "application/ajax/getreport.php",
            dataType: "json",
            data: {
                action: "getpay",
                start_date:  $ddate,
                ptype:  ptype,
                user_ids: userStr
            },
            success: function (res) {
                if (res.success) {
                    $("#detail_data").html(res.detailData);
                    $("#detaildiv").modal('show');
                } else {
                    swal.fire("Can't get data. Try again later, please");
                }
            },
            complete: function (res) {
            }
        });
    }

</script>
