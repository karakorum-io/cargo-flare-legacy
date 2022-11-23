@flash_message@
<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/add.gif" alt="Add" width="16" height="16" /> <a href="<?=getLink("faq", "edit")?>">&nbsp;Add new</a>
</div>
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid">
    <tr class="grid-head">
        <td class="grid-head-left"><?=$this->order->getTitle("question", "Question")?></td>
        <td class="grid-head-left"><?=$this->order->getTitle("answer", "Answer")?></td>
        <td class="grid-head-right" colspan="2">Actions</td>
    </tr>
    <? foreach ($this->data as $i => $data) { ?>
    <tr class="grid-body<?=($i == 0 ? " first-row" : "")?>" id="row-<?=$data['id']?>">
        <td class="grid-body-left"><?=$data['question']?></td>
        <td class="grid-body-left"><?=cutContent(trim(strip_tags($data['answer'])),10)?></td>
        <td style="width: 16px;"><?=editIcon(getLink("faq", "edit", "id", $data['id']))?></td>
        <td style="width: 16px;" class="grid-body-right"><?=deleteIcon(getLink("faq", "delete", "id", $data['id']), "row-".$data['id'])?></td>
    </tr>
    <? } ?>
</table>
@pager@