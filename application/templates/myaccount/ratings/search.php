<? include(TPL_PATH."myaccount/menu.php");?>
To find a company that responded to a vehicle you posted, or a company with posted vehicles, enter the company name below. Use this feature to also review a company's ratings and optionally rate the company after you have conducted business with them.
<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?=getLink("ratings")?>">&nbsp;Back to the My Rating</a>
</div>
<form action="<?=getLink("ratings", "search")?>" method="post">
	<?=formBoxStart("Company Name Search")?>
		<table cellpadding="0" cellspacing="5" border="0">
			<tr>
				<td>@searchq@</td>
				<td><?=submitButtons("", "Search")?></td>
			</tr>
		</table>
		<div style="padding:10px; background-color:#fffbd8">
			Enter <strong>W&amp;B</strong> for <em>"W&B Auto Transport"</em>
			, Enter <strong>W.B.</strong> or <strong>WB</strong> for <em>"W.B. Auto Transport"</em>
			, Enter <strong>Tanya</strong> for <em>"Tanya's Auto Transport"</em>
		</div>
	<?=formBoxEnd()?>
</form>
<br />
Click name to View / Rate a company. Search results for: <span style="color:#000; background-color:#edc300;">"<?=$_SESSION['searchq']?>"</span>
<br /><br />
	<table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid">
	    <tr class="grid-head">
	        <td class="grid-head-left"><?=$this->order->getTitle("companyname", "Company")?></td>
	        <td><?=$this->order->getTitle("address", "Address")?></td>
	        <td class="grid-head-right"><?=$this->order->getTitle("phone", "Phone")?></td>
	    </tr>
	    <? if (count($this->data) > 0){?>
		    <? foreach ($this->data as $i => $data) { ?>
		    <tr class="grid-body<?=($i == 0 ? " first-row" : "")?>" id="row-<?=$data['id']?>">
		        <td valign="top" class="grid-body-left"><a href="<?=getLink("ratings", "company", "id", $data['id'])?>"><?=htmlspecialchars($data['companyname']);?></td>
		        <td valign="top"><?=htmlspecialchars($data['address'])?></td>
			    <td class="grid-body-right"><?=$data['phone']?></td>
		    </tr>
		    <? } ?>
		<?}else{?>
			<tr class="grid-body first-row" id="row-">
		        <td class="grid-body-left">&nbsp;</td>
		        <td align="center">No records found.</td>
				<td class="grid-body-right">&nbsp;</td>
		    </tr>
		<? } ?>
	</table>
@pager@