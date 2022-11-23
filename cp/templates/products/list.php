<?php
?><br />
<ul class="cp-top-actions">
    <li style="background: url(<?=SITE_IN?>images/icons/add.gif) 0 0 no-repeat;">
		<a href="<?=getLink('products/edit/id/new')?>">Add new product</a>
	</li>
</ul>
@flash_message@
<br />
<? if ($this->daffny->tpl->products) { ?>
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid">
    <tr class="grid-head">
        <th width="40" align="center" class="grid-head-left">@id@</th>
        <th align="center">@code@</th>
        <th align="center">@name@</th>
        <th align="center">@price@</th>
        <th align="center">@description@</th>
				<th align="center">Is Online</th>
        <th align="center">@period_id@</th>
        <th align="center">@renewal@</th>
        <th align="center">@type_id@</th>
        <th nowrap="nowrap" align="center" colspan="2" class="grid-head-right">Actions</th>
    </tr>
	<? foreach ($this->daffny->tpl->products as $i => $m) {
	/* @var Product $m */
	?>
	<tr class="grid-body <? if (!$m->is_online) { echo " disabled"; } if ($i == 0) { echo ' first-row'; } ?>">
        <td align="center" class="grid-body-left"><?=$m->id?></td>
        <td align="center"><?=$m->code?></td>
        <td><?=$m->name?></td>
        <td align="right">$<?=number_format($m->price, 2)?></td>
        <td width="300">
			<?php
				echo $m->getSmallDescription();
				if ($m->getRestDescription()) {
					?><a href="#" onclick="return showRestDescription(<?=$m->id ?>, this);" title="Click here to see the rest of text">...</a><span
						id="description-rest-<?=$m->id?>" style="display: none;"><?=$m->getRestDescription() ?></span><?
				}
			?>
		</td>
		    <td align="center"><?=($m->is_online)?"Yes":"<span style=\"color:blue\">No</span>"?></td>
        <td align="right"><?=$m->getPeriodLabel();?></td>
        <td align="center"><?=(is_null($m->renewal_code)?'None':$m->renewal_code)?></td>
        <td align="center"><?=$m->getTypeLabel();?></td>
        <td width="21" align="center"><a href="<?=getLink('products/edit/id/'.$m->id)?>"><img src="<?=SITE_IN?>images/icons/edit.png" border="0" alt="" title="Edit" width="16" height="16" /></a></td>
        <td width="21" align="center" class="grid-body-right"><a href="<?=getLink('products/delete/id/'.$m->id)?>" onclick="return are_you_sure();"><img src="<?=SITE_IN?>images/icons/delete.png" border="0" alt="" title="Delete" width="16" height="16" /></a></td>
    </tr>
    <? } ?>
</table>
<br />
@pager@
<script type="text/javascript">
function showRestDescription(id, linkObj) {
	$(linkObj).remove();
	$('#description-rest-'+id).fadeIn();
	return false;
}
</script>
<? } else { ?>
<p>Records not found.</p>
<? } ?>