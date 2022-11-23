@flash_message@
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid">
    <tr class="grid-head">
        <td class="grid-head-left"><?php echo $this->order->getTitle("companyname", "Company name") ?></td>
        <td><?php echo $this->order->getTitle("activity", "Activity") ?></td>
        <td><?php echo $this->order->getTitle("email", "E-mail") ?></td>
        <td style="width: 70px"><?php echo $this->order->getTitle("reg_date", "Date") ?></td>
        <td class="grid-head-right" colspan="2">Actions</td>
    </tr>
	<?php if (!empty($this->data)) { ?>
    <?php foreach ($this->data as $i => $data): ?>
    <tr class="grid-body<?php echo ($i == 0 ? " first-row" : ""); ?>" id="row-<?php echo $data['id'] ?>">
        <td class="grid-body-left"><?php echo htmlspecialchars($data['companyname']) ?></td>
        <td><?php echo htmlspecialchars($data['activity']) ?></td>
        <td><?php echo $data['email'] ?></td>
        <td align="center"><?php echo $data['reg_date_show'] ?></td>
        <td align="center" style="width: 20px;"><?php echo infoIcon(getLink("forms/contactus/info", $data['id'])) ?></td>
        <td class="grid-body-right" align="center" style="width: 20px;"><?php echo deleteIcon(getLink("forms/contactus/delete", $data['id']), "row-".$data['id'], true) ?></td>
    </tr>
    <?php endforeach; ?>
	<?php } else { ?>
	<tr class="grid-body first-row">
		<td class="grid-body-left grid-body-right" colspan="6" align="center">No records.</td>
	</tr>
	<?php } ?>
</table>
@pager@