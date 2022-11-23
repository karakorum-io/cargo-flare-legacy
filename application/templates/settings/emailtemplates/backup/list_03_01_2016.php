<? include(TPL_PATH."settings/menu.php"); ?>

<div style="text-align: left; clear:both; padding-bottom:5px; padding-top:5px;">
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/add.gif" alt="Add" width="16" height="16" /> <a href="<?=getLink("emailtemplates", "edit")?>">&nbsp;&nbsp;Add New Template</a>
</div>
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid">
    <tr class="grid-head">
        <td class="grid-head-left"><?=$this->order->getTitle("name", "Name")?></td>
        <td><?=$this->order->getTitle("description", "Description")?></td>
        <td><?=$this->order->getTitle("send_type", "Send Email Using")?></td>
        <td class="grid-head-right" colspan="3">Actions</td>
    </tr>
    <? if (count($this->data)>0){?>
	    <? foreach ($this->data as $i => $data) { ?>
	    <tr class="grid-body<?=($i == 0 ? " first-row" : "")?>" id="row-<?=$data['id']?>">
	        <td class="grid-body-left"><?=htmlspecialchars($data['name']);?></td>
	        <td><?=htmlspecialchars($data['description'])?></td>
	        <td align="center"><?=$data['send_type']?></td>
	        <td style="width: 16px;"><?=editIcon(getLink("emailtemplates", "edit", "id", $data['id']))?></td>
	        <td style="width: 16px;"><?=previewIcon(getLink("emailtemplates", "show", "id", $data['id']))?></td>
	        <? if ($data['is_system'] != 1){?>
		        <td style="width: 16px;" class="grid-body-right"><?=deleteIcon(getLink("emailtemplates", "delete", "id", $data['id']), "row-".$data['id'])?></td>
	        <?}else{?>
				<td style="width: 16px;" class="grid-body-right">-</td>
	        <?}?>
	    </tr>
	    <? } ?>
	<?}else{?>
		<tr class="grid-body first-row" id="row-">
	        <td class="grid-body-left">&nbsp;</td>
	        <td align="center" colspan="4">No records found.</td>
			<td class="grid-body-right">&nbsp;</td>
	    </tr>
	<? } ?>
</table>
@pager@