<? if (isset($_GET["member_id"]) && (int) get_var("member_id")>0){ ?>
    <? $m = new Member($this->daffny->DB); ?>
    <div style="text-align: right;">
        <h3 style="color:#000"><?=htmlspecialchars($m->getCompanyProfileById((int) get_var("member_id"))->companyname)?></h3>
        <a href="<?=getLink("members")?>">Back to the member's list</a>
    </div>
<? } ?>
@flash_message@

<form action="<?= getLink("documents") ?>" method="get">
    <?= formBoxStart("Filter") ?>
    <table cellspacing="5" cellpadding="5" border="0" class="cp-members-filters">
        <tr>
            <td>@company@</td>
            <td>@document@</td>
            <td>Date Range:</td>
            <td>@start_date@</td>
            <td> - </td>
            <td>@end_date@</td>
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
        <td class="grid-head-left"><?=$this->order->getTitle("owner_id", "Company")?></td>
        <td><?=$this->order->getTitle("name_original", "Document")?></td>
        <td style="width: 100px;"><?=$this->order->getTitle("date_uploaded", "Uploaded")?></td>
        <td class="grid-head-right" colspan="2"><?=$this->order->getTitle("status", "Actions")?></td>
    </tr>
    <? if (count($this->data) > 0){?>
        <? foreach ($this->data as $i => $data) { ?>
        <tr class="grid-body<?=($i == 0 ? " first-row" : "")?>" id="row-<?=$data['id']?>">
            <td class="grid-body-left"><?=htmlspecialchars($data['companyname'])?></td>
            <td><?=getFileImageByType($data['type'], "Download ".$data['name_original']);?> <a href="<?=getLink("documents", "getdocs", "id", $data['id'])?>"><?=$data['name_original']?></a></td>
            <td align="center"><?=$data['date_uploaded']?></td>
            <td style="width: 100px;" align="center" class="grid-body-left"><?=aprovedText(getLink("documents", "status", "id", $data['id']), $data['status'])?></td>
            <td style="width: 16px;" class="grid-body-right"><?=deleteIcon(getLink("documents", "delete", "id", $data['id']), "row-".$data['id'])?></td>
        </tr>
        <? } ?>
    <? }else{ ?>
     <tr>
        <td colspan="5">No documents</td>
    </tr>
    <? } ?>
</table>
@pager@