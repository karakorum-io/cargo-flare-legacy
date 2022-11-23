<div class="action-links">
	<a href="<?php echo getLink("admins/edit") ?>" class="add">Add new</a>
</div>
@flash_message@
<?php if ($this->data): ?>
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid">
    <tr class="grid-head">
        <td style="width: 70px" class="grid-head-left"><?php echo $this->order->getTitle("status", "Status") ?></td>
        <td><?php echo $this->order->getTitle("fullname", "Name") ?></td>
        <td><?php echo $this->order->getTitle("email", "E-mail") ?></td>
        <td><?php echo $this->order->getTitle("cell_phone", "Cell Phone") ?></td>
        <td><?php echo $this->order->getTitle("groupname", "Group") ?></td>
        <td style="width: 115px"><?php echo $this->order->getTitle("last_login", "Last Login") ?></td>
        <td class="grid-head-right" colspan="2">Actions</td>
    </tr>
    <?php foreach ($this->data as $i => $data): ?>
    <tr class="grid-body<?php echo ($i == 0 ? " first-row" : "")?>" id="row-<?php echo $data['id'] ?>">
	    <td align="center" class="grid-body-left"><?php echo statusText(getLink("admins", "status", "id", $data['id']), $data['status']) ?></td>
        <td><?php echo $data['fullname'] ?></td>
        <td><?php echo $data['email'] ?></td>
        <td><?php echo $data['cell_phone'] ?></td>
        <td align="center"><?php echo $data['groupname'] ?></td>
        <td align="center"><?php echo $data['last_login_show'] ?></td>
        <td align="center" style="width: 25px"><?php echo editIcon(getLink("admins/edit/id", $data['id'])) ?></td>
        <td align="center" style="width: 25px" class="grid-body-right"><?php echo deleteIcon(getLink("admins/delete/id", $data['id']), "row-".$data['id']) ?></td>
    </tr>
    <?php endforeach; ?>
</table>
@pager@
<?php else: ?>
<?php echo $this->build("no_records") ?>
<?php endif; ?>
