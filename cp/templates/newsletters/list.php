@flash_message@
<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/add.gif" alt="Add" width="16" height="16" /> <a href="<?=getLink("newsletters", "edit")?>">&nbsp;Add new</a>
</div>
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid">
    <tr class="grid-head">
        <td class="grid-head-left"><?=$this->order->getTitle("title", "Newsletter")?></td>
        <td>Content</td>
        <td width="100"><?=$this->order->getTitle("Created", "Date")?></td>
        <td class="grid-head-right" colspan="2">Actions</td>
    </tr>
	<?php if (is_array($this->data) && count($this->data) > 0) { ?>
    <?php foreach ($this->data as $i => $data): ?>
    <tr class="grid-body<?php echo ($i == 0 ? " first-row" : ""); ?>" id="row-<?php echo $data['id']; ?>">
        <td class="grid-body-left"><?php echo htmlspecialchars($data['title']); ?></td>
        <td><?php echo $data['content']; ?></td>
        <td align="center"><?php echo $data['Created']; ?></td>
        <td align="center" style="width: 20px;"><?php echo editIcon(getLink("newsletters", "edit", "id", $data['id'])); ?></td>
        <td class="grid-body-right" align="center" style="width: 20px;"><?php echo deleteIcon(getLink("newsletters", "delete", "id", $data['id']), "row-".$data['id']); ?></td>
    </tr>
    <?php endforeach; ?>
	<?php } else { ?>
	<tr class="grid-body first-row">
		<td class="grid-body-left grid-body-right" colspan="5" align="center">No records.</td>
	</tr>
	<?php } ?>
</table>
@pager@