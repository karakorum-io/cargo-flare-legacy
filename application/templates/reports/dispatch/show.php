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
        background: #C25C5C !important;
    }

    .grid-head-default{
        font-weight: normal;
        font-size: 12px;
        text-align: center;
        color: #333 !important;
    }

    .grid-head-default a{
        color: #333 !important;
    }

    .grid-head-red a {
        color: white !important;
    }

    .table-bordered th, .table-bordered td{
        border: 1px solid #CCC;
    }
</style>

<div class="kt-portlet">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                Dispatch Activity Report
            </h3>
        </div>
    </div>
    <div class="kt-portlet__body">
        <?=formBoxStart()?>
        <form action="<?=getLink("reports", "dispatchActivity")?>" method="post" />
            <div class="row">
                <div class="col-4">
                    <div class="row">
                        <div  class="col-12">
                            <div class="form-group">
                                <label for="ptype1" class="kt-radio kt-radio--brand">
                                    <input type="radio" name="ptype" value="1" id="ptype1" @ptype1ch@>Time Period:
                                    <span></span>
                                </label>
                                <div  class="row">
                                    <div class="col-12">
                                        @time_period@
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div  class="col-12">
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
                        <div  class="col-12">
                            <div class="form-group">
                                <label>Optional: Filter by user</label>
                                <div  class="row">
                                    <div class="col-12">
                                        @users_ids[]@
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div  class="col-12 mt-3">
                            <div  class="row">
                                <div class="col-6 text-right">
                                    <?=submitButtons("", "Generate")?>
                                </div>
                                <div class="col-6 text-left">
                                <?=exportButton("Export to Excel", 'btn-sm  btn_dark_green')?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="row">
                        <div  class="col-12">
                            <table width="100%">
                                
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <table width="100%" id="summary">
                        <tr>
                            <td><h4>COD:</h4></td>
                            <td><h4><value1>0000</value1></h4></td>
                        </tr>
                        <tr>
                            <td><h4>COP: </h4></td>
                            <td><h4><value2>0000</value2></h4></td>
                        </tr>
                        <tr>
                            <td><h4>Billing: </h4></td>
                            <td><h4><value3>0000</value3></h4></td>
                        </tr>
                        <tr>
                            <td><h4>Invoice: </h4></td>
                            <td><h4><value4>0000</value4></h4></td>
                        </tr>
                        <tr>
                            <td><h4>Total Dispatched: </h4></td>
                            <td><h4><value5>0000</value5></h4></td>
                        </tr>
                        <tr>
                            <td><h4>Total Reveune: </h4></td>
                            <td><h4 style="color:red;"><value6>0000</value6></h4></td>
                        </tr>
                        <tr>
                            <td><h4>&nbsp;</h4></td>
                            <td><h4>&nbsp;</h4></td>
                        </tr>
                        <?php
                            $fmt = numfmt_create( 'en_US', NumberFormatter::CURRENCY )
                        ?>
                        <tr>
                            <td><h4>Payment Received</h4></td><td><h4><?php echo $this->daffny->tpl->payment_sent;?></h4></td>
                        </tr>
                        <tr>
                            <td><h4>Amount Of Payment</h4></td><td><h4><?php echo numfmt_format_currency($fmt, $this->daffny->tpl->amount_sent, "USD");?></h4></td>
                        </tr>
                        <tr>
                            <td><h4>Unpaid Invoices</h4></td><td><h4><?php echo $this->daffny->tpl->unpaid_invoices;?></h4></td>
                        </tr>
                        <tr>
                            <td><h4>Payment Sent</h4></td><td><h4><?php echo $this->daffny->tpl->payment_received;?></h4></td>
                        </tr>
                        <tr>
                            <td><h4>Amount Of Payment</h4></td><td><h4><?php echo numfmt_format_currency($fmt, $this->daffny->tpl->amount_received, "USD");?></h4></td>
                        </tr>
                        <tr>
                            <td><h4>Unpaid Bills</h4></td><td><h4><?php echo $this->daffny->tpl->un_paid;?></h4></td>
                        </tr>
                    </table>
                </div>
            </div>
        </form>
        <?=formBoxEnd()?>
        <br/><br/>
        <hr/>
        <div  class="row">
            <div class="col-12">
                <h2 style="color: #374afb;">Dispatch Activity Report</h2>
                <strong>To view more detailed information please click on Generate / Export.</strong>
                <br>
                <br>
                <div style="overflow-x: scroll; overflow-y: scroll; height:400px;">
                    <table class="table table-bordered table-striped" id="payable">
                        <thead>
                            <tr>
                                <th colspan="6">&nbsp;</th>
                                <th colspan="10" align="center" class="grid-head-red">Carrier Payments</th>
                                <th colspan="12" align="center" class="grid-head-green">Customer Payments</th>
                                <th colspan="4" >GP%</th>
                            </tr>
                            <tr>
                                <th class="grid-head-default"><?=$this->order->getTitle("id", "ID");?></th>
                                <th class="grid-head-default"><?=$this->order->getTitle("conatctname", "Assigned To");?></th>
                                <th class="grid-head-default"><?=$this->order->getTitle("dispatch", "Dispatched On");?></th>
                                <th class="grid-head-default"><?=$this->order->getTitle("carrier", "Carrier Name");?></th>
                                <th class="grid-head-default"><?=$this->order->getTitle("order_type", "Order Type");?></th>
                                <th class="grid-head-default"><?=$this->order->getTitle("status", "Total Tariff / Deposite");?></th>
                                <th class="grid-head-red"><?=$this->order->getTitle("carrier_pay", "Carrier Pay");?></th>

                                <th class="grid-head-red">Paid On 1</th>
                                <th class="grid-head-red">Amount 1</th>
                                <th class="grid-head-red">Difference 1</th>

                                <th class="grid-head-red">Paid On 2</th>
                                <th class="grid-head-red">Amount 2</th>
                                <th class="grid-head-red">Difference 2</th>

                                <th class="grid-head-red">Paid On 3</th>
                                <th class="grid-head-red">Amount 3</th>
                                <th class="grid-head-red">Difference 3</th>

                                <th class="grid-head-green">Type 1</th>
                                <th class="grid-head-green">Date 1</th>
                                <th class="grid-head-green">Amount 1</th>
                                <th class="grid-head-green">TxnID / Check 1</th>

                                <th class="grid-head-green">Type 2</th>
                                <th class="grid-head-green">Date 2</th>
                                <th class="grid-head-green">Amount 2</th>
                                <th class="grid-head-green">TxnID / Check 2</th>

                                <th class="grid-head-green">Type 3</th>
                                <th class="grid-head-green">Date 3</th>
                                <th class="grid-head-green">Amount 3</th>
                                <th class="grid-head-green">TxnID / Check 3</th>

                                <th class="grid-head-default">Estimated</th>
                                <th class="grid-head-default">Final</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $cod = 0;
                                $cop = 0;
                                $billing = 0;
                                $invoice = 0;
                                
                                if (count($this->orders) > 0) { 
                                    foreach ($this->orders as $i => $o) {
                                        if(($i % 2) == 0){ $color="#F0F0F0;";} else { $color="#F0F0F0;";}
                            ?>
                            <tr>
                                <td><a href='<?php echo getLink("orders", "show", "id") . "/" . $o->id; ?>'><?=$o->prefix . "-" . $o->number?></a></td>
                                <td><?php try {echo $o->getAssigned()->contactname;} catch (EXCEPTION $e) {echo "---";} ?></td>
                                <td align="center"><?=$o->getDispatched("m/d/Y h:i A");?></td>
                                <td><?php print_r($o->getCarrier()->company_name);?></td>
                                <td>
                                    <?php
                                        if ($o->balance_paid_by == 2) {
                                            echo "COD - Cash/Certified Funds";
                                            $cod++;
                                        } else if ($o->balance_paid_by == 3) {
                                            echo "COD - Check";
                                            $cod++;
                                        } else if ($o->balance_paid_by == 8) {
                                            echo "COP - Cash/Certified Funds";
                                        } else if ($o->balance_paid_by == 9) {
                                            echo "COP - Check";
                                        } else if ($o->balance_paid_by == 12) {
                                            echo "Billing - Cash/Certified Funds";
                                        } else if ($o->balance_paid_by == 13) {
                                            echo "Billing - Check";
                                        } else if ($o->balance_paid_by == 20) {
                                            echo "Billing - Comcheck";
                                        } else if ($o->balance_paid_by == 21) {
                                            echo "Billing - QuickPay";
                                        } else if ($o->balance_paid_by == 24) {
                                            echo "Billing - ACH";
                                        } else if ($o->balance_paid_by == 14) {
                                            echo "Invoice - Cash/Certified Funds";
                                        } else if ($o->balance_paid_by == 15) {
                                            echo "Invoice - Check";
                                        } else if ($o->balance_paid_by == 22) {
                                            echo "Invoice - Comcheck";
                                        } else {
                                            echo "Invoice - QuickPay";
                                        }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                        if ($o->balance_paid_by == 2) {
                                            $cod_cash = (float) $o->total_tariff_stored - (float) $o->carrier_pay_stored;
                                            echo number_format($cod_cash, 2);
                                        } else if ($o->balance_paid_by == 3) {
                                            echo number_format($o->total_tariff_stored - $o->carrier_pay_stored, 2);
                                        } else if ($o->balance_paid_by == 8) {
                                            echo number_format($o->total_tariff_stored - $o->carrier_pay_stored, 2);
                                        } else if ($o->balance_paid_by == 9) {
                                            echo number_format($o->total_tariff_stored - $o->carrier_pay_stored, 2);
                                        } else {
                                            echo $o->total_tariff_stored;
                                        }
                                    ?>
                                </td>
                                <td align="right">
                                    <?php
                                        if ($o->balance_paid_by == 2) {
                                            echo "0";
                                        } else if ($o->balance_paid_by == 3) {
                                            echo "0";
                                        } else if ($o->balance_paid_by == 8) {
                                            echo "0";
                                        } else if ($o->balance_paid_by == 9) {
                                            echo "0";
                                        } else {
                                            echo $o->carrier_pay_stored;
                                        }
                                    ?>
                                </td>
                                <?php
                                    $qry = "SELECT date_received, amount, fromid, toid FROM app_payments WHERE fromid IN (1,2) AND toid = 3 AND Void = 0 AND deleted = 0 AND entity_id =" . $o->id;
                                    $data = $this->daffny->DB->query($qry);
                                    $carrier_payments = array();
                                    while ($row_data = mysqli_fetch_assoc($data)) {
                                        $carrier_payments[] = $row_data;
                                    }
                                ?>
                                <td><?php echo $carrier_payments[0]['date_received'] == "" ? "" : date('m/d/Y', strtotime($carrier_payments[0]['date_received']));?></td>
                                <td align="right"><?php print_r($carrier_payments[0]['amount']);?></td>
                                <td align="right"><?php $carrier_payments[0]['amount'] == 0 ? "" : print_r($o->carrier_pay_stored - $carrier_payments[0]['amount']);?></td>
                                <td><?php echo $carrier_payments[1]['date_received'] == "" ? "" : date('m/d/Y', strtotime($carrier_payments[1]['date_received']));?></td>
                                <td align="right"><?php print_r($carrier_payments[1]['amount']);?></td>
                                <td align="right"><?php $carrier_payments[1]['amount'] == 0 ? "" : print_r($o->carrier_pay_stored - $carrier_payments[1]['amount']);?></td>
                                <td><?php echo $carrier_payments[2]['date_received'] == "" ? "" : date('m/d/Y', strtotime($carrier_payments[2]['date_received']));?></td>
                                <td align="right"><?php print_r($carrier_payments[2]['amount']);?></td>
                                <td align="right"><?php $carrier_payments[2]['amount'] == 0 ? "" : print_r($o->carrier_pay_stored - $carrier_payments[2]['amount']);?></td>
                                <?php
                                    $methods = array('Personal Check', 'Company Check', 'Cashiers Check', 'Comchek', 'Cash', 'Electronic transfer', 'Other', 'Money Order', 'Credit Card');
                                    $qry = "SELECT method, date_received, transaction_id,  amount, `check`, fromid, toid FROM app_payments WHERE fromid IN (2) AND toid = 1 AND deleted = 0 AND entity_id =" . $o->id;
                                    $data = $this->daffny->DB->query($qry);
                                    $customer_payments = array();
                                    while ($row_data = mysqli_fetch_assoc($data)) {
                                        $customer_payments[] = $row_data;
                                    }
                                ?>
                                <td><?php echo $methods[$customer_payments[0]['method'] - 1]; ?></td>
                                <td align="center"><?php echo ($customer_payments[0]['date_received'] == "") ? "" : date('m/d/Y', strtotime($customer_payments[0]['date_received'])) ?></td>
                                <td align="right"><?php print_r($customer_payments[0]['amount']);?></td>
                                <td align="right"><?php echo $customer_payments[0]['transaction_id'] == 0 ? $customer_payments[0]['check'] : $customer_payments[0]['transaction_id'];?></td>
                                <td><?php echo $methods[$customer_payments[1]['method'] - 1]; ?></td>
                                <td align="center"><?php echo ($customer_payments[1]['date_received'] == "") ? "" : date('m/d/Y', strtotime($customer_payments[1]['date_received'])) ?></td>
                                <td align="right"><?php print_r($customer_payments[1]['amount']);?></td>
                                <td align="right"><?php echo $customer_payments[1]['transaction_id'] == 0 ? $customer_payments[1]['check'] : $customer_payments[1]['transaction_id'];?></td>
                                <td><?php echo $methods[$customer_payments[2]['method'] - 1]; ?></td>
                                <td align="center"><?php echo ($customer_payments[2]['date_received'] == "") ? "" : date('m/d/Y', strtotime($customer_payments[2]['date_received'])) ?></td>
                                <td align="right"><?php print_r($customer_payments[2]['amount']);?></td>
                                <td align="right"><?php echo $customer_payments[2]['transaction_id'] == 0 ? $customer_payments[2]['check'] : $customer_payments[2]['transaction_id'];?></td>
                                <?php
                                    // calculating GP%
                                    $GP_Percent = 0;
                                    $deposit = ($o->total_tariff_stored - $o->carrier_pay_stored);
                                    $GP_Percent = (($deposit * 100) / $o->total_tariff_stored);
                                    $GP_Percent = number_format((float) $GP_Percent, 2, '.', '');
                                ?>
                                <td align="right"><?php print_r($GP_Percent . "%");?></td>
                                <td>
                                <?php
                                    if ($o->balance_paid_by == 2) {
                                        echo $GP_Percent . "%";
                                    } else if ($o->balance_paid_by == 3) {
                                        echo $GP_Percent . "%";
                                    } else if ($o->balance_paid_by == 8) {
                                        echo $GP_Percent . "%";
                                    } else if ($o->balance_paid_by == 9) {
                                        echo $GP_Percent . "%";
                                    } else {
                                        echo ($carrier_payments[0]['amount'] + $carrier_payments[1]['amount'] + $carrier_payments[2]['amount']) == 0 ? "" : $GP_Percent . "%";
                                    }
                                ?>
                                </td>
                            </tr>
                            <?php
                                    }
                                }  else { 
                            ?>
                            <tr id="row-">
                                <td align="center" colspan="32">
                                    <?php if (isset($_POST['submit'])) { ?>
                                        No records found.
                                    <?php } else { ?>
                                        Generate report.
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php
                                } 
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-12">
                @pager@
            </div>
        </div>
        <br>
        <br>
        <strong>To view more detailed information please click on Generate / Export.</strong>
    </div>
</div>
<input type="hidden" name="cod" id="codvalue" value="<?php echo $cod; ?>">
<script>
    $(document).ready(function() {
        $('#payable').DataTable({
            "lengthChange": false,
            "paging": false,
            "bInfo" : false,
            'drawCallback': function (oSettings) {
                $("#payable_wrapper").children('.row:first').children('.col-sm-12:first').html('<div class="form-group row"><label class="col-form-label">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
                $("#payable_wrapper").children('.row:nth-child(3)').children('.col-sm-12:first').html($('.pager').clone());
                $('.pages_div').remove();
                $("#payable_wrapper").find('form-group row').css("margin-left", "1px");
            }
        });
    });
</script>
<script type="text/javascript">
	function setPagerLimit(val) {
		$.ajax({
			type: "POST",
			url: "https://cargoflare.com/application/ajax/member.php?action=setLimit",
			dataType: "json",
			data: "limit="+val,
			success: function(result) {
				if (result.success == true) {
					window.location.reload();
				}
			}
		});
	}
</script>
<script>

    $(document).ready(function(){
        $.fn.datepicker.defaults.format = "mm/dd/yyyy";
        
        $("#start_date").datepicker({
            endDate: "dateToday"
        });

        $("#end_date").datepicker({
            startDate: "dateToday"
        });

        $("#start_date, #end_date").click(function(){
            $("#ptype2").attr("checked", "checked");
        });

        $("#time_period").click(function(){
            $("#ptype1").attr("checked", "checked");
        });

        $('#users_ids').select2();

        $('#summaryMessage').show();
        $('#summary').hide();

        $.ajax({
            type: "POST",
            url: "<?=SITE_IN?>application/ajax/entities.php",
            dataType: "json",
            data: {
                action: 'getBalancePaidByCount',
                start: '<?php echo $_SESSION["start_date"] ?>',
                end: '<?php echo $_SESSION["end_date"] ?>',
                users_ids: '<?php echo json_encode($_SESSION["users_ids"]); ?>'
            },
            success: function(result) {
                $('#summaryMessage').hide();
                $('#summary').show();
                $("value1").html(result.cod);
                $("value2").html(result.cop);
                $("value3").html(result.billing);
                $("value4").html(result.invoice);
                $("value5").html(result.totalNumber);
                $("value6").html(" $"+result.tInvoice);
            },
            error: function(result) {
                alert("Failed to Load net COD/ COP/ Billing/ Invoice");
            }
        });
    });
</script>