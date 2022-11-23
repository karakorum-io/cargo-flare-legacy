<h3>Accounts Payable/Receivable</h3>
<em>Displays orders with outstanding balances between company and carriers, customers, and locations.</em>
<div style="text-align:right; clear:both; padding-bottom:5px; padding-top:5px;">
    <img src="<?= SITE_IN ?>images/icons/back.png" alt="Back" style="vertical-align:middle; width: 16px; height: 16px;" /> <a href="<?= getLink("reports") ?>">&nbsp;Back to the 'Reports'</a>
</div>
<form action="<?= getLink("reports", "accounts") ?>" method="post" />
<?= formBoxStart() ?>
<table cellspacing="0" cellpadding="0" border="0">
    <tr>
        <td valign="top">
            <table cellspacing="5" cellpadding="5" border="0">
                <tr>
                    <td><input type="radio" name="ptype" value="1" id="ptype1" @ptype1ch@ /></td>
                    <td><label for="ptype1">Time Period:</label></td>
                    <td colspan="3">@time_period@</td>
                </tr>
                <tr>
                    <td><input type="radio" name="ptype" value="2" id="ptype2" @ptype2ch@ /></td>
                    <td><label for="ptype2">Date Range:</label></td>
                    <td>@start_date@</td>
                    <td> - </td>
                    <td>@end_date@</td>
                </tr>
                <tr>
                    <td colspan="2">@users_ids[]@</td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                    <td colspan="3">@include_orders@</td>
                </tr>
                <tr>
                    <td colspan="2"><?= submitButtons("", "Generate") ?></td>
                    <td colspan="3"><?= exportButton("Export to Excel") ?></td>
                </tr>
            </table>
        </td>        
    </tr>
</table>
<?= formBoxEnd() ?>
<br />
<table style="width: 100%" cellpadding="0" cellspacing="0" border="0" class="grid">
        <tr class="grid-head">
            <th class="grid-head-left"><?= $this->order->getTitle("id", "ID"); ?></th>
            <th><?= $this->order->getTitle("ordered", "Order<br />date"); ?></th>
            <th><?= $this->order->getTitle("carrier_id", "Carrier"); ?></th>
            <th><?= $this->order->getTitle("shipper_id", "Shipper"); ?></th>
            <th><?= $this->order->getTitle("origin_id", "Origin"); ?></th>
            <th><?= $this->order->getTitle("destination_id", "Destination"); ?></th>
            <th><?= $this->order->getTitle("total_tariff", "Tariff"); ?></th>
            <th><?= $this->order->getTitle("carrier_pay", "Carrier Pay"); ?></th>
            <th>
                Terminal Fee<br />
                <?= $this->order->getTitle("pickup_terminal_fee", "Pickup"); ?><br />
                <?= $this->order->getTitle("dropoff_terminal_fee", "Drop-off"); ?>
            </th>
            <th>Deposit</th>
            <? //<th>COD</th>?>
            <th>Profit</th>
            <th>From Shipper</th>
            <th>From Broker</th>
            <th>From Carrier</th>
            <th>From Pickup Terminal</th>
            <th>From Drop-off Terminal</th>
            <th>To Shipper</th>
            <th>To Broker</th> 
            <th>To Carrier</th> 
            <th>To Pickup Terminal</th>
            <th class="grid-head-right">To Drop-off Terminal</th>
        </tr>
    <? if (count($this->orders) > 0) { ?>
        <? $pm = new PaymentManager($this->daffny->DB);?>
            <? foreach ($this->orders as $i => $o) { ?>
                <tr class="grid-body<?= ($i == 0 ? " first-row" : "") ?>">
                    <td style="white-space: nowrap;" class="grid-body-left"><?= $o->id ?></td>
                    <td align="center"><?= $o->getOrdered("m/d/Y h:i A"); ?></td>
                    <td><?= htmlspecialchars(trim($o->carrier_id) == "" ? "" : $o->getCarrier()->company_name) ?></td>
                    <td><?= htmlspecialchars(trim($o->shipper_id) == "" ? "" : $o->getShipper()->fname." ".$o->getShipper()->lname) ?></td>
                    <td><?= $o->origin_id == ""?"": htmlspecialchars(formatAddress("", "", strtoupper($o->getOrigin()->city), $o->getOrigin()->state, $o->getOrigin()->zip)) ?></td>
                    <td><?= $o->destination_id == ""?"": htmlspecialchars(formatAddress("", "", strtoupper($o->getDestination()->city), $o->getDestination()->state, $o->getDestination()->zip))?></td>
                    <td><?= $o->getTotalTariff()?></td>
                    <td><?= $o->getCarrierPay()?></td>
                    <td>
                        <?= $o->getPickupTerminalFee(true);?><br />
                        <?= $o->getDropoffTerminalFee(true);?>
                    </td>
                    <td><?= $o->getTotalDeposit()?></td>
                    <?/*<td><?= $o->getFilteredPayments("COD")?></td>*/?>
                    <td><?= number_format(($o->getTotalTariff(false) - $o->getCost(false)), 2) ?></td>
                    <td><?= $pm->getFilteredPaymentsTotals($o->id, Payment::SBJ_SHIPPER, null);?></td>
                    <td><?= $pm->getFilteredPaymentsTotals($o->id, Payment::SBJ_COMPANY, null);?></td>
                    <td><?= $pm->getFilteredPaymentsTotals($o->id, Payment::SBJ_CARRIER, null);?></td>
                    <td><?= $pm->getFilteredPaymentsTotals($o->id, Payment::SBJ_TERMINAL_P, null);?></td>
                    <td><?= $pm->getFilteredPaymentsTotals($o->id, Payment::SBJ_TERMINAL_D, null);?></td>
                    <td><?= $pm->getFilteredPaymentsTotals($o->id, null, Payment::SBJ_SHIPPER);?></td>
                    <td><?= $pm->getFilteredPaymentsTotals($o->id, null, Payment::SBJ_COMPANY);?></td> 
                    <td><?= $pm->getFilteredPaymentsTotals($o->id, null, Payment::SBJ_CARRIER);?></td> 
                    <td><?= $pm->getFilteredPaymentsTotals($o->id, null, Payment::SBJ_TERMINAL_P);?></td>
                    <td class="grid-body-right"><?= $pm->getFilteredPaymentsTotals($o->id, null, Payment::SBJ_TERMINAL_D);?></td>
                </tr>
            <? } ?>
    <? } else { ?>
        <tr class="grid-body first-row" id="row-">
            <td align="center" colspan="22">
                <? if (isset($_POST['submit'])) { ?>
                    No records found.
                <? } else { ?>
                    Generate report.
                <? } ?>
            </td>
        </tr>
    <? } ?>
</table>
@pager@

<script type="text/javascript">//<![CDATA[
    $("#users_ids").multiselect({ // Build multiselect for users
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