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
        <td class="grid-head-left"><?=$this->order->getTitle("username", "Username")?></td>
        <td><?=$this->order->getTitle("contactname", "Name")?></td>
        <td>License</td>
        <td><?=$this->order->getTitle("email", "E-mail")?></td>
        <td><?=$this->order->getTitle("companyname", "Company")?></td>
        <td style="width: 150px;"><?=$this->order->getTitle("reg_date", "Reg. date")?></td>
        <td style="width: 70px;"><?=$this->order->getTitle("status", "Status")?></td>
        <td style="width: 50px;">Frozen</td>
        <td class="grid-head-right" colspan="5">Actions</td>
    </tr>
    <?
        $cp = new CompanyProfile($this->daffny->DB);
        
    ?>
    <? foreach ($this->data as $i => $data) { ?>
    <? $cp->getByOwnerId($data["id"]); ?>
    
    <tr class="grid-body<?=($i == 0 ? " first-row" : "")?>" id="row-<?=$data['id']?>">
        <td class="grid-body-left"><?=$data['username']?></td>
        <td><?=$data['contactname']?></td>
        <td nowrap="nowrap">
            <?=htmlspecialchars($cp->getCompanyType());?>
            <br />
            Users:<?=$cp->getAdditionalUsers();?><br />
            Storage: <?=$data['storage_space']?><br />
            Used: <span style="color: green;"><?=$data['used_space']?></span><br />
            Rest: <span style="color: #BB0000;"><?=$data['rest_space']?></span>
        </td>
        <td><?=$data['email']?></td>
        <td><?=  htmlspecialchars($cp->companyname);?></td>
        <td style="text-align: center;"> <?=$data['reg_date_show']?> </td>
        <? $data['status'] = ($data['status']=='Active'?"Active":"Inactive"); ?>
        <td style="text-align: center;"><?=statusText(getLink("members", "status", "id", $data['id']), $data['status'])?></td>
        <td style="text-align: center;">
            <?=$cp->getAccountStatus();?>
        </td>

        <td style="width: 16px;"><?=loginIcon(getLink("members", "signas", "id", $data['id']))?></td>
        <td style="width: 16px;"><?=docsIcon(getLink("documents", "member_id", $data['id']))?></td>
        <td style="width: 16px;"><?=payIcon(getLink("payments", "member_id", $data['id']))?></td>
        <td style="width: 16px;"><?=editIcon(getLink("members", "edit", "id", $data['id']))?></td>
        <td style="width: 16px;" class="grid-body-right"><?=deleteIcon(getLink("members", "delete", "id", $data['id']), "row-".$data['id'])?></td>
    </tr>
    <? } ?>
</table>
@pager@