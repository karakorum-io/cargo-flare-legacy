<? include(TPL_PATH."accounts/leadsources/menu.php"); ?>
<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?=getLink("leadsources")?>">&nbsp;Back to the list</a>
</div>
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid">
    <tr class="grid-head">
        <td class="grid-head-left"><?=$this->order->getTitle("id", "ID")?></td>
        <td><?=$this->order->getTitle("domain", "Domain")?></td>
        <td class="grid-head-right"><?=$this->order->getTitle("create_date", "Submited")?></td>
    </tr>
    <? if (count($this->leadsources)>0){?>
	    <? foreach ($this->leadsources as $i => $leadsource) { ?>
	    <tr class="grid-body<?=($i == 0 ? " first-row" : "")?>" id="row-<?=$leadsource->id?>">
	        <td align="center" class="grid-body-left"><a href="<?=getLink("leadsources", "details", "id", $leadsource->id);?>"><?=$leadsource->id;?></a></td>
	        <td align="center"><a href="http://<?=htmlspecialchars($leadsource->domain);?>" target="_blank"><?=htmlspecialchars($leadsource->domain);?></a></td>
			<td align="center"><?=$leadsource->create_date?></td>
	    </tr>
	    <? } ?>
	<?}else{?>
		<tr class="grid-body first-row" id="row-">
	        <td align="center" colspan="3">No records found.</td>
	    </tr>
	<? } ?>
</table>
@pager@
<?=backButton(getLink("leadsources"))?>