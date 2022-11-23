<? if (isset($_GET["member_id"]) && (int) get_var("member_id")>0){ ?>
    <? $m = new Member($this->daffny->DB); ?>
    <div style="text-align: right;"><h3 style="color:#000"><?=htmlspecialchars($m->getCompanyProfileById((int) get_var("member_id"))->companyname)?></h3>
        <a href="<?=getLink("members")?>">Back to the member's list</a>
    </div>
    
<? } ?>
<form action="<?= getLink("payments", "member_id", get_var("member_id")) ?>" method="get">
    <?= formBoxStart("Filter") ?>
    <table cellspacing="5" cellpadding="5" border="0">
        <tr>
            <td>Date Range:</td>
            <td>@start_date@</td>
            <td> - </td>
            <td>@end_date@</td>
        </tr>
        <tr>
            <td>@type@</td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: left;"><?= submitButtons("", "Submit") ?></td>
        </tr>
    </table>
    <?= formBoxEnd(); ?>
</form>
@flash_message@
<br />
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid">
    <tr class="grid-head">
        <td class="grid-head-left">ID</td>
        <td><?= $this->order->getTitle("added", "Date") ?></td>
        <td><?= $this->order->getTitle("owner_id", "Company") ?></td>
        <td>Description</td>
        <td>Transaction ID</td>
        <td><?= $this->order->getTitle("type", "Type") ?></td>
        <td class="grid-head-right">Amount</td>
    </tr>
    <? if (count($this->transactions) > 0) { ?>
        <? foreach ($this->transactions as $i => $t) { ?>
            <tr class="grid-body<?= ($i == 0 ? " first-row" : "") ?>" id="row-<?= $t->id ?>">
                <td class="grid-body-left"><?=$t->id?></td>
                <td><?= $t->added ?></td>
                <td><?= htmlspecialchars($t->getCompany()->companyname) ?></td>
                <td><?= htmlspecialchars($t->description) ?></td>
                <td><?= htmlspecialchars($t->transaction_id) ?></td>
                <td><?= colorBillingType(Billing::$type_name[$t->type]) ?></td>
                <td align="right" class="grid-body-right">$<?= ($t->type == 2 ? "-" : "") ?><?= number_format($t->amount, 2, ".", ",") ?></td>
            </tr>
        <? } ?>
    <? } else { ?>
        <tr class="grid-body first-row" id="row-1">
            <td class="grid-body-left">&nbsp;</td>
            <td colspan="5" align="center">Records not found.</td>
            <td class="grid-body-right">&nbsp;</td>
        </tr>
    <? } ?>
</table>
@pager@