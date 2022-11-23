@flash_message@
<form action="<?= getLink("members") ?>" method="get">
	<?= formBoxStart("Filter") ?>
	<table cellspacing="5" cellpadding="5" border="0" class="cp-members-filters">
		<tr>
			<td>@username@</td>
			<td>@email@</td>
			<td>@company@</td>
			<td>@account_type@</td>
			<td>@phone@</td>
		</tr>
		<tr>
			<td><?= submitButtons("", "Submit") ?></td>
			<td colspan="9">&#8203;</td>
		</tr>
	</table>
	<?= formBoxEnd(); ?>
</form>

<br/>
<?php include('member_menu.php');  ?>
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid">
    <tr class="grid-head">
        <td><font color="#FFFFFF">ID</font></td>
        <td><font color="#FFFFFF">Name</font></td>
        <td><font color="#FFFFFF">E-mail</font></td>
        <td><font color="#FFFFFF">Company</font></td>
        <td style="width: 150px;"><font color="#FFFFFF"><?php print "Reg. date";?></font></td>
        <td style="width: 70px;"><font color="#FFFFFF">Phone</font></td>
        <td style="width: 50px;"><font color="#FFFFFF">MC Number</font></td>
        <td style="width: 150px;"><font color="#FFFFFF">Type</font></td>
        <td class="grid-head-right" colspan="5"><font color="#FFFFFF">Actions</font></td>
    </tr>
   
    <? foreach ($this->data as $i => $data) { ?>
    
    <tr class="grid-body<?=($i == 0 ? " first-row" : "")?>" id="row-<?=$data['id']?>">
        <td><?=$data['id']?></td>
        <td><?=$data['contactname']?></td>
        <td><?=$data['email']?></td>
        <td><?=  htmlspecialchars($data['companyname']);?></td>
        <td style="text-align: center;"> <?=$data['create_date']?> </td>
        <td style="text-align: center;"> <?=$data['phone']?> </td>
        <td style="text-align: center;"> <?=$data['mcnumber']?> </td>
        <td style="text-align: center;"> <?=$data['type']?> </td>
        <td style="width: 16px;"><?=editIcon(getLink("registration", "aid", $data['id']))?></td>
        <td style="width: 16px;" class="grid-body-right"><?=deleteIcon(getLink("members", "deleteapplied", "id", $data['id']), "row-".$data['id'])?></td>
    </tr>
    <? } ?>
</table>
@pager@
