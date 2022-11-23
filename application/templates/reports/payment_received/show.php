<div class="kt-portlet">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3>Detailed Payment Recieved</h3>
        </div>
    </div>
    <div class="kt-portlet__body">
        <?php echo formBoxStart() ?>
        <form action="<?php echo getLink("reports", "payment_received") ?>" method="post" />
        <div class="row">
            <div class="col-10">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <input type="radio" name="ptype" value="1" id="ptype1" @ptype1ch@ />
                            <label for="ptype1">Time Period:</label>
                            @time_period@
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">

                            <div class="row">
                                <div class="col-6">
                                    <input type="radio" name="ptype" value="2" id="ptype2" @ptype2ch@ />
                                    <label for="ptype2">Date Range:</label>
                                    @start_date@
                                </div>
                                <div class="col-6 mt-4">
                                    @end_date@
                                </div>
                            </div>
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
                                <div class="col-2">
                                    <?php echo submitButtons("", "Generate") ?>
                                </div>
                                <div class="col-6">
                                    <?php echo exportButton("Export to Excel",'btn-sm  btn_light_green') ?>
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
                    <thead>
                        <tr class="">
                            <th><?php echo $this->order->getTitle("id", "Order ID"); ?></th>
                            <th><?php echo $this->order->getTitle("date_received", "Date"); ?></th>    
                            <th><?php echo $this->order->getTitle("", "Customer / Company"); ?></th>
                            <th><?php echo $this->order->getTitle("", "Agent"); ?></th>
                            <th><?php echo $this->order->getTitle("", "Type"); ?></th>
                            <th><?php echo $this->order->getTitle("amount", "Charged"); ?></th>
                            <th><?php echo $this->order->getTitle("", "Deposit"); ?></th>
                            <th><?php echo $this->order->getTitle("", "Balance"); ?></th>
                            <th><?php echo $this->order->getTitle("", "Carrier Pay"); ?></th>
                            <th><?php echo $this->order->getTitle("", "Carrier Balance"); ?></th>
                            <th><?php echo $this->order->getTitle("method", "Method"); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $methods = [
                            1 => 'Personal Check',
                            2 => 'Company Check',
                            3 => 'Cashier Check',
                            4 => 'Comcheck',
                            5 => 'Cash',
                            6 => 'Electronic Transfer',
                            7 => 'Other',
                            8 => 'Money Order',
                            9 => 'Credit Card',
                            999 => 'Debit',
                            998 => 'Credit'
                        ];

                        $balance_paid_by = [
                            2 => 'COD - Cash/Certified Funds',
                            3 => 'COD - Check',
                            8 => 'COP - Cash/Certified Funds',
                            9 => 'COP - Check',
                            12 => 'Billing - Cash/Certified Funds',
                            13 => 'Billing - Check',
                            20 => 'Billing - Comcheck',
                            21 => 'Billing - QuickPay'
                        ];

                        $data = [];
                        foreach ($this->orders as $i => $p) {
                            $data[$p['payment_data']['entity_id']][$p['payment_data']['number']] = [
                                'order_id' => $p['payment_data']['order_id'],
                                'amount' => $p['payment_data']['amount'],
                                'date_received' => $p['payment_data']['date_received'],
                                'fromid' => $p['payment_data']['fromid'],
                                'toid' => $p['payment_data']['toid'],
                                'method' => $p['payment_data']['method'],
                                'number' => $p['payment_data']['number'],
                                'order_id' => $p['data']['order_id'],
                                'assigned_name' => $p['data']['assigned_name'],
                                'shipper' => $p['data']['shipper'],
                                'deposit' => $p['data']['deposit'],
                                'carrier_pay' => $p['data']['carrier_pay'],
                                'balance_paid_by' => $p['data']['balance_paid_by'],
                            ];
                        }

                        foreach ($data as $i => $p) {
                            if(count($p) > 1){
                                
                                $deposit = 0;
                                $c_pay = 0;

                                foreach ($p as $k => $v) {
                                    $deposit = str_replace(",","",substr($v['deposit'], 2));
                                    $c_pay = str_replace(",","",substr($v['carrier_pay'], 2));
                                }

                                $index = 0;
                                foreach ($p as $k => $v) {
                                    $amount = $v['amount'];
                                    $balance = 0;
                                    $c_balance = 0;

                                    if($amount > $deposit){
                                        $balance = 0;
                                    } else {
                                        $balance = $deposit - $amount;
                                    }

                                    if(!in_array($v['balance_paid_by'], [2,3,8,9])){
                                        // when billing

                                        if($index == 0){
                                            $c_balance = $c_pay - ($amount - $deposit);
                                        } else {
                                            if($balance != 0){
                                                $c_balance = $c_pay - ($amount - $deposit);
                                            } else {
                                                $c_balance = $c_pay - $amount;
                                            }
                                        }
                                        
                                        
                                        if($c_balance < 0){
                                            $c_balance = 0;
                                        }
                                    }

                                    $index++;
                        ?>
                            <tr>
                                <td>
                                    <a href="<?php echo SITE_IN ?>application/orders/show/id/<?php echo $i ?>" target="_blank">
                                        <?php echo $v['order_id'] ?>
                                    </a>
                                </td>
                                <td><?php echo date('m-d-Y', strtotime($v['date_received'])) ?></td>
                                <td><?php echo $v['shipper'] ?></td>
                                <td><?php echo $v['assigned_name'] ?></td>
                                <td><?php echo $balance_paid_by[$v['balance_paid_by']] ?></td>
                                <td align="right"><?php echo $amount; ?></td>
                                <td align="right"><?php echo $deposit; ?></td>
                                <td align="right"><?php echo $balance; ?></td>
                                <td align="right"><?php echo $c_pay; ?></td>
                                <td align="right"><?php echo $c_balance; ?></td>
                                <td><?php echo $methods[$v['method']]; ?></td>
                            </tr>
                        <?php
                                }
                            } else {
                                foreach ($p as $k => $v) {
                                    $amount = $v['amount'];
                                    $deposit = str_replace(",","",substr($v['deposit'], 2));
                                    $c_pay = str_replace(",","",substr($v['carrier_pay'], 2));
                                    $balance = 0;
                                    $c_balance = 0;

                                    if($amount > $deposit){
                                        $balance = 0;
                                    } else {
                                        $balance = $deposit - $amount;
                                    }

                                    if(in_array($v['balance_paid_by'], [2,3,8,9])){
                                        // nothing when COD
                                    } else {
                                        // when billing
                                        $c_balance = $c_pay - ($amount - $deposit);
                                        if($c_balance < 0){
                                            $c_balance = 0;
                                        }
                                    }
                        ?>
                            <tr>
                                <td>
                                    <a href="<?php echo SITE_IN ?>application/orders/show/id/<?php echo $i ?>" target="_blank">
                                        <?php echo $v['order_id'] ?>
                                    </a>
                                </td>
                                <td><?php echo date('m-d-Y', strtotime($v['date_received'])) ?></td>
                                <td><?php echo $v['shipper'] ?></td>
                                <td><?php echo $v['assigned_name'] ?></td>
                                <td><?php echo $balance_paid_by[$v['balance_paid_by']] ?></td>
                                <td align="right"><?php echo $amount; ?></td>
                                <td align="right"><?php echo $deposit; ?></td>
                                <td align="right"><?php echo $balance; ?></td>
                                <td align="right"><?php echo $c_pay; ?></td>
                                <td align="right"><?php echo $c_balance; ?></td>
                                <td><?php echo $methods[$v['method']]; ?></td>
                            </tr>
                        <?php
                                }
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#report').DataTable({
            "lengthChange": false,
            "paging": false,
            "bInfo": false,
            'drawCallback': function(oSettings) {
                $("#payable_wrapper").children('.row:first').children('.col-sm-12:first').html('<div class="form-group row"><label class="col-form-label">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
                $("#payable_wrapper").children('.row:nth-child(3)').children('.col-sm-12:first').html($('.pager').clone());
                $('.pages_div').remove();
                $("#payable_wrapper").find('form-group row').css("margin-left", "1px");
            }
        });
    });

    $.fn.datepicker.defaults.format = "mm/dd/yyyy";
    $('#start_date,#end_date').datepicker({});
    $("#start_date").attr("autocomplete='off'");
</script>

@pager@