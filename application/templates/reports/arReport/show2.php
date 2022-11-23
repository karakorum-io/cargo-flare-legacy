


<div  class="kt-portlet">
    <div class="kt-portlet__head">
    <div class="kt-portlet__head-label">
        <h3>Account Receivable / Payable Report</h3>
    </div>
    </div>

<div  class="kt-portlet__body">
<?php echo formBoxStart() ?>
<form action="<?php echo getLink("reports", "arReport") ?>" method="post" />
 <div class="row">
<div  class="col-10">
    <div  class="row">
        <div  class="col-6">
            <div class="form-group">
             <input type="radio" name="ptype" value="1" id="ptype1" @ptype1ch@ />
              <label for="ptype1">Time Period:</label>
              @time_period@
         </div>
        </div>
        <div  class="col-6">
        <div class="form-group">
           
        <div  class="row">
        
         <div  class="col-6">
        <input type="radio" name="ptype" value="2" id="ptype2" @ptype2ch@ />
        <label for="ptype2">Date Range:</label>
         @start_date@
         </div>

        <div  class="col-6 mt-4">
        @end_date@
        </div>
        </div>


        </div>
        </div>
    </div>
</div>
</div>




<div class="row">
<div  class="col-10">
    <div  class="row">
        <div  class="col-6">
            <div class="form-group">
            @ship_via@
         </div>
        </div>
        <div  class="col-6">
        <div class="form-group">
       <label for="status" > Order Status:  </label>
            <?php
            if (!empty($_SESSION)) {
                $status = $_SESSION['status'];
                $reportType = $_SESSION['reportType'];
                $groupBy = $_SESSION['groupBy'];
                if (in_array("6", $status)) {
                    $check1 = "checked";
                } else {
                    $check1 = "";
                }
                if (in_array("7", $status)) {
                    $check2 = "checked";
                } else {
                    $check2 = "";
                }
                if (in_array("8", $status)) {
                    $check3 = "checked";
                } else {
                    $check3 = "";
                }
                if ($reportType == 'ar') {
                    $rCheck1 = "checked";
                } else {
                    $rCheck2 = "checked";
                }
                if ($groupBy == 'date') {
                    $gCheck1 = "checked";
                } else {
                    $gCheck1 = "";
                }
            }
            ?>
         </br>
        <div class="chekcbox mt-3">
        <input type="radio" id="1" name="status[]" value="6" <?php echo $check1 ?> > Dispatched 
        <input type="radio" id="3" name="status[]" value="8" <?php echo $check3 ?> > Picked Up                          
        <input type="radio" id="2" name="status[]" value="7" <?php echo $check2 ?> > Pending Payments 
        </div>
        </div>
        </div>
    </div>
</div>
</div>



 <div class="row">
<div  class="col-10">
    <div  class="row">
        <div  class="col-6">
            <div class="form-group">

                <label for="Report Type" ></label>
                <input type="radio" name="reportType" value="ar" <?php echo $rCheck1 ?>> A/C Receivable
                <input type="radio" name="reportType" value="ap" <?php echo $rCheck2 ?>> A/C Payable
                
                
         </div>
        </div>
        <div  class="col-6">
        <div class="form-group">
           
        <div  class="row">
         <div  class="col-2">
        <?php echo submitButtons("", "Generate") ?>
         </div>

        <div  class="col-6 ">
      <?php echo exportButton("Export to Excel",'btn-sm  btn_light_green') ?>
        </div>
        </div>


        </div>
        </div>
    </div>
</div>
</div>
<?php echo formBoxEnd() ?>

<div  class="row mt-3">
    <div  class="col-12">
        <table  id="payable" class="table table-bordered">
            <thead>
                <tr class="">

                    <th ><?php echo $this->order->getTitle("id", "Order ID"); ?></th>
                    <th><?php echo $this->order->getTitle("delivered", "Date"); ?></th>
                    <th><?php echo $this->order->getTitle("account_id", "Account Id"); ?></th>
                    <th><?php echo "Last Name"; ?></th> 
                    <th><?php echo "First Name"; ?></th> 
                    <th><?php echo "Company"; ?></th> 
                    <th><?php echo "Aging"; ?></th> 
                    <th><?php echo "Open Balance"; ?></th>       	                      
                </tr>
            </thead>
            <tbody>
                <?php if (count($this->orders) > 0) { 
                    $paymentManager = new PaymentManager($this->daffny->DB);
                ?>
                <?php
                    foreach ($this->orders as $i => $o) {
                    
                    if ($o->prefix) {
                        $id = $o->prefix . "-" . $o->number;
                    } else {
                        $id = $o->number;
                    }       
                    
                    $total = $o->total_tariff_stored;
                    $carrier_pay = $o->carrier_pay_stored;
                    $deposit = $total - $carrier_pay;
                    $openBalance = 0;
                    
                    switch ($o->balance_paid_by) {            
                            case Entity::BALANCE_COMPANY_OWES_CARRIER_CASH:
                            case Entity::BALANCE_COMPANY_OWES_CARRIER_CHECK:
                            case Entity::BALANCE_COMPANY_OWES_CARRIER_COMCHECK:
                            case Entity::BALANCE_COMPANY_OWES_CARRIER_QUICKPAY:
                            case Entity::BALANCE_COMPANY_OWES_CARRIER_ACH:
                                $carrierPaid = $paymentManager->getFilteredPaymentsTotals($o->id, Payment::SBJ_COMPANY, Payment::SBJ_CARRIER, false);
                                $shipperPaid = $paymentManager->getFilteredPaymentsTotals($o->id, Payment::SBJ_SHIPPER, Payment::SBJ_COMPANY, false);
                                ///$shipperPaid = $paymentManager->getFilteredPaymentsTotals($entity->id, Payment::SBJ_COMPANY, Payment::SBJ_SHIPPER, false); 
                                $balances['they_carrier'] = 0;
                                $balances['we_shipper'] = 0;
                                $balances['we_carrier'] = $o->getCarrierPay(false) + $o->getPickupTerminalFee(false) + $o->getDropoffTerminalFee(false) - $carrierPaid;
                                $balances['we_carrier_paid'] = $carrierPaid;
                                $balances['they_shipper'] = $o->getCost(false) + $o->getTotalDeposit(false) - $shipperPaid;
                                $balances['they_shipper_paid'] = $shipperPaid;

                                $carrierRemains = $o->getCarrierPay(false) + $o->getPickupTerminalFee(false) + $o->getDropoffTerminalFee(false) - $carrierPaid;
                                $depositRemains = $o->getTotalDeposit(false) - $shipperPaid;
                                $shipperRemains = $o->getCost(false) + $o->getTotalDeposit(false) - $shipperPaid;
                                $amountType =2;
                                if ($reportType == 'ar') {
                                    $company_name = $o->getAccountCustom(false, 'company_name');
                                    $openBalance = "$".number_format($shipperRemains,2);
                                } else {
                                    $company_name = $o->getCarrier()->company_name;
                                    $openBalance = "$".number_format(($balances['we_carrier']-$balances['they_shipper']),2);
                                }
                                
                                break;            
                            default:                
                                break;
                    }
                    
                    if ($o->balance_paid_by == 2 || $o->balance_paid_by == 3 || $o->balance_paid_by == 8 || $o->balance_paid_by == 9) {
                        $openBalance = $o->getDepositDue();
                    }
                    
                ?>
                <tr class="">
                    <td style="white-space: nowrap;" class="grid-body-left"><a href="<?php echo SITE_IN ?>application/orders/show/id/<?= $o->id ?>"  target="_blank"><?= $id ?></a></td>
                    <td align='center'><?php echo date("m/d/y h:i a", strtotime($o->delivered));?></td>
                    <td align='center'><?php echo $o->account_id;?></td>
                    <td><?php echo $o->getAccountCustom(false, 'first_name');?></td>
                    <td><?php echo $o->getAccountCustom(false, 'last_name');?></td>
                    <td><?php echo $company_name;?></td>
                    <td align='right'>
                    <?php 
                        if ($o->status == 7) {
                            $date1 = date('Y-m-d');
                            $date2 = $o->delivered;
                            $datetime1 = strtotime($date2);
                            $datetime2 = strtotime($date1);
                            $secs = $datetime2 - $datetime1;
                            echo $age = floor($secs / 86400)." Days";
                        } else {
                            echo "";
                        }
                    ?>
                    </td>
                    <td align='right'><?php echo $openBalance;?></td>
                </tr>
                <?php
                    }
                ?>    
                <?php }?>
            </tbody>
        </table>
    </div>
</div>

</div>
</div>

<script type="text/javascript">
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
} );
</script>


<script type="text/javascript">
$.fn.datepicker.defaults.format = "mm/dd/yyyy";
$('#start_date,#end_date').datepicker({
});
$("#start_date").attr("autocomplete='off'");
</script>

@pager@

