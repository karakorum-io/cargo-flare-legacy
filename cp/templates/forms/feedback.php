@flash_message@
<?php if (!empty($this->data)): ?>
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid">
    <tr class="grid-head">
        <td class="grid-head-left"><?php echo $this->order->getTitle("contactname", "Name") ?></td>
        <td><?php echo $this->order->getTitle("companyname", "Company name") ?></td>
		<td><?php echo $this->order->getTitle("email", "E-mail") ?></td>
        <td><?php echo $this->order->getTitle("phone", "Phone") ?></td>
        <td style="width: 70px"><?php echo $this->order->getTitle("reg_date", "Date") ?></td>
        <td class="grid-head-right" colspan="2">Actions</td>
    </tr>
    <?php foreach ($this->data as $i => $data): ?>
    <tr class="grid-body<?php echo ($i == 0 ? " first-row" : ""); ?>" id="row-<?php echo $data['id'] ?>">
        <td class="grid-body-left"><?php echo htmlspecialchars($data['contactname']) ?></td>
        <td><?php echo $data['companyname'] ?></td>
		<td><?php echo $data['email'] ?></td>
        <td><?php echo $data['phone'] ?></td>
        <td align="center"><?php echo $data['reg_date_show'] ?></td>
        <td align="center" style="width: 20px"><?php echo infoIcon(getLink("forms/feedback/info", $data['id'])); ?></td>
        <td class="grid-body-right" align="center" style="width: 20px"><?php echo deleteIcon(getLink("forms/feedback/delete", $data['id']), "row-".$data['id']); ?></td>
    </tr>
    <?php endforeach; ?>
</table>
@pager@
<?php else: ?>
<div style="padding: 50px 0; text-align: center"><?php include(TPL_PATH."no_records.php") ?></div>
<?php endif; ?>
