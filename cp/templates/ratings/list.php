<? if (isset($_GET["member_id"]) && (int) get_var("member_id")>0){ ?>
    <? $m = new Member($this->daffny->DB); ?>
    <div style="text-align: right;"><h3 style="color:#000"><?=htmlspecialchars($m->getCompanyProfileById((int) get_var("member_id"))->companyname)?></h3>
        <a href="<?=getLink("members")?>">Back to the member's list</a>
    </div>
<? } ?>
@flash_message@

<form action="<?= getLink("ratings") ?>" method="get">
    <?= formBoxStart("Filter") ?>
    <table cellspacing="5" cellpadding="5" border="0" class="cp-members-filters">
        <tr>
            <td>Date Range:</td>
            <td>@start_date@</td>
            <td> - </td>
            <td>@end_date@</td>
            <td>@status@</td>
            <td>@from@</td>
            <td>@to@</td>
        </tr>
        <tr>
            <td><?= submitButtons("", "Submit") ?></td>
            <td colspan="9">&#8203;</td>
        </tr>
    </table>
    <?= formBoxEnd(); ?>
</form>
<br/>
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid">
    <tr class="grid-head">
        <td class="grid-head-left"><?= $this->order->getTitle("id", "ID") ?></td>
        <td><?=$this->order->getTitle("added","Date")?></td>
        <td><?=$this->order->getTitle("type","Rating")?></td>
        <td><?=$this->order->getTitle("status","Status")?></td>
        <td><?=$this->order->getTitle("from_id","From")?></td>
        <td><?=$this->order->getTitle("to_id","To")?></td>
        <td class="grid-head-right" colspan="2">Actions</td>
    </tr>
    <? if (count($this->data) > 0) { ?>
        <? foreach ($this->data as $i => $rating) { ?>
            <tr class="grid-body<?= ($i == 0 ? " first-row" : "") ?>" id="row-<?= $rating->id ?>">
                <td style="text-align: center;" class="grid-body-left"><?=$rating->id?></td>
                <td style="text-align: center;"><?=$rating->getDate("m/d/Y");?></td>
                <td style="text-align: center;"><?=$rating->getTypeImage();?></td>
                <td style="text-align: center;"><?=$rating->getStatus()?></td>
                <td><?=htmlspecialchars($rating->getFrom()->companyname);?></td>
                <td><?=htmlspecialchars($rating->getTo()->companyname);?></td>
                <td style="width: 16px;"><?= editIcon(getLink("ratings", "editrating", "id", $rating->id)) ?></td>
                <td style="width: 16px;" class="grid-body-right"><?= deleteIcon(getLink("ratings", "deleterating", "id", $rating->id), "row-" . $rating->id) ?></td>
            </tr>
        <? } ?>
    <? } else { ?>
        <tr class="grid-body first-row" id="row-">
            <td align="center" colspan="8">No records found.</td>
        </tr>
    <? } ?>
</table>
@pager@