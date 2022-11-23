@flash_message@
<div align="right">
	<img src="<?=SITE_IN?>images/icons/excel.gif" width="15" height="16" align="absmiddle" alt="Export to Excel." /> <a href="<?=getLink("subscribers","export_excel")?>">Export to Excel</a>&nbsp;&nbsp;&nbsp;
	<img src="<?=SITE_IN?>images/icons/csv.gif" width="15" height="16" align="absmiddle" alt="Export with Tab-delimited format." /> <a href="<?=getLink("subscribers","export")?>">Export to CSV</a>&nbsp;&nbsp;&nbsp;
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/add.gif" alt="Add" width="16" height="16" /> <a href="<?=getLink("subscribers", "import")?>">&nbsp;Import</a>&nbsp;&nbsp;&nbsp;
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/add.gif" alt="Add" width="16" height="16" /> <a href="<?=getLink("subscribers", "edit")?>">&nbsp;Add new</a>
</div>
<br />

<?=formBoxStart()?>
<form action="<?=getLink("subscribers")?>" method="post" name="filter" style="margin: 0px;">
    <table border="0" cellspacing="5" cellpadding="0">
		<tr>
			<td>@category_id@</td>
			<td><?=submitButtons("", "Apply")?></td>
		</tr>
	</table>
</form>
<?=formBoxEnd()?>
<br />
	<table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid">
	    <tr class="grid-head">
	        <td style="width: 50px;" class="grid-head-left"><?=$this->order->getTitle("id", "ID")?></td>
	        <td><?=$this->order->getTitle("sname", "Name")?></td>
	        <td><?=$this->order->getTitle("email", "Email")?></td>
	        <td><?=$this->order->getTitle("catname", "Category Name")?></td>
	        <td><?=$this->order->getTitle("unsubscribed", "Unsubscribed")?></td>
	        <td><?=$this->order->getTitle("reg_date", "Added")?></td>
	        <td class="grid-head-right" colspan="2">Actions</td>
	    </tr>
			<?php if (!empty($this->data)) { ?>
		    <? foreach ($this->data as $i => $data) { ?>
		    <tr class="grid-body<?=($i == 0 ? " first-row" : "")?>" id="row-<?=$data['id']?>">
		        <td class="grid-body-left"><?=$data['id']?></td>
		        <td><?=htmlspecialchars($data['sname'])?></td>
		        <td><?=htmlspecialchars($data['email'])?></td>
		        <td align="center"><?=htmlspecialchars($data['catname'])?></td>
		        <td align="center"><?=$data['unsubscribed']?></td>
		        <td align="center"><?=$data['reg_date']?></td>
		        <td align="center" style="width: 16px;"><?=editIcon(getLink("subscribers", "edit", "id", $data['id']))?></td>
		        <td align="center" style="width: 16px;" class="grid-body-right"><?=deleteIcon(getLink("subscribers", "delete", "id", $data['id']), "row-".$data['id'])?></td>
		    </tr>
		    <? } ?>
			<? }else{ ?>
			<tr class="grid-body first-row">
				<td colspan="8" align="center" class="grid-body-left grid-body-right">No records.</td>
			</tr>
			<? } ?>
	</table>
	@pager@
