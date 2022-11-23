<h3>Payments received</h3>
<em>This report can be additional filtered by Order ID, Shipper, Reference # and Transaction ID. </em>
<div style="text-align:right; clear:both; padding-bottom:5px; padding-top:5px;">
    <img src="<?= SITE_IN ?>images/icons/back.png" alt="Back" style="vertical-align:middle; width: 16px; height: 16px;" /> <a href="<?= getLink("reports") ?>">&nbsp;Back to the 'Reports'</a>
</div>
<form action="<?= getLink("reports", "payments") ?>" method="post" />
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
                    <td colspan="2">@ship_via@</td>
                </tr>
                <tr>
                    <td colspan="2">@order_id@</td>
                </tr>
                <tr>
                    <td colspan="2">@reference_no@</td>
                </tr>
                <tr>
                    <td colspan="2">@transaction_id@</td>
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
            <th class="grid-head-left"><?= $this->order->getTitle("date_received", "Date"); ?></th>
            <th><?= $this->order->getTitle("entity_id", "Order ID"); ?></th>
            <th>Shipper</th>
            <th><?= $this->order->getTitle("amount", "Amount"); ?></th>
            <th><?= $this->order->getTitle("method", "Payment Method"); ?></th>
            <th><?= $this->order->getTitle("entered_by", "Entered By"); ?></th>
            <th><?= $this->order->getTitle("number", "Reference #"); ?></th>
            <th>Notes</th>
            <th><?= $this->order->getTitle("check", "Check #"); ?></th>
            <th><?= $this->order->getTitle("cc_number", "Card #"); ?></th>
            <th><?= $this->order->getTitle("cc_type", "Card Type"); ?></th>
            <th><?= $this->order->getTitle("cc_exp", "Card Expiration"); ?></th>
            <th><?= $this->order->getTitle("cc_auth", "Authorization Code"); ?></th>
            <th class="grid-head-right"><?= $this->order->getTitle("transaction_id", "Transaction ID"); ?></th>
        </tr>
    <? if (count($this->payments) > 0) { ?>
            <? foreach ($this->payments as $i => $p) { ?>
                <tr class="grid-body<?= ($i == 0 ? " first-row" : "") ?>">
                    <td class="grid-body-left"><?= $p->getDate("M,d Y") ?></td>
                    <td><?= $p->entity_id ?></td>
                    <td><?= $p->getEntity()->getShipper()->fname." ".$p->getEntity()->getShipper()->lname ?></td>
                    <td style="text-align: right">$<?= number_format($p->amount, 2) ?></td>
                    <td><?= Payment::$method_name[$p->method] ?></td>
                    <td><?= htmlspecialchars($p->getEnteredBy()); ?></td>
                    <td><?= $p->number ?></td>
                    <td><?= htmlspecialchars($p->notes); ?></td>
                    <td><?= $p->check ?></td>
                    <td><?= hideCCNumber($p->cc_number, 2); ?></td>
                    <td><?= $p->cc_type ?></td>
                    <td><?= $p->getCCExp("m/Y") ?></td>
                    <td><?= $p->cc_auth?></td>
                    <td  style="text-align: right" class="grid-body-right"><?= $p->transaction_id ?></td>
                </tr>
            <? } ?>
    <? } else { ?>
        <tr class="grid-body first-row" id="row-">
            <td align="center" colspan="14">
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