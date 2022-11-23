@flash_message@
<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/add.gif" alt="Add" width="16" height="16" /> <a href="<?=getLink("sysmessages", "edit")?>">&nbsp;Add new</a>
</div>
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid">
    <tr class="grid-head">
        <td class="grid-head-left"><?=$this->order->getTitle("question", "Message")?></td>
        <td><?=$this->order->getTitle("added", "Added")?></td>
        <td class="grid-head-right" colspan="2">Actions</td>
    </tr>
	<? if (isset($this->data) && count($this->data)>0 ){?>
	    <? foreach ($this->data as $i => $data) { ?>
	    <tr class="grid-body<?=($i == 0 ? " first-row" : "")?>" id="row-<?=$data['id']?>">
	        <td class="grid-body-left"><?=$data['message']?></td>
	        <td class="grid-body-left"><?=$data['added']?></td>
	        <td style="width: 16px;"><?=editIcon(getLink("sysmessages", "edit", "id", $data['id']))?></td>
	        <td style="width: 16px;" class="grid-body-right"><?=deleteIcon(getLink("sysmessages", "delete", "id", $data['id']), "row-".$data['id'])?></td>
	    </tr>
	    <? } ?>
	<? }else{ ?>
		<tr class="grid-body">
			<td colspan="4" style="text-align: center;">No records.</td>
		</tr>
	<? } ?>
</table>
@pager@