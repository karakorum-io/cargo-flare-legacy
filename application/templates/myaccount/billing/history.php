<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
	<div class="kt-subheader   kt-grid__item" id="kt_subheader">
		<div class="kt-subheader__main">
			<h3 class="kt-subheader__title">Billing History</h3>
			<span class="kt-subheader__separator kt-hidden"></span>
			<div class="kt-subheader__breadcrumbs">
				<span class="kt-subheader__breadcrumbs-separator"></span>
				<a href="<?=SITE_IN?>" class="kt-subheader__breadcrumbs-link">Home</a>
				<span class="kt-subheader__breadcrumbs-separator"></span>
				<a class="kt-subheader__breadcrumbs-link" href="<?=SITE_IN?>application/companyprofile">Profile</a>
			</div>
		</div>
	</div>
</div>

<? include(TPL_PATH."myaccount/menu.php");?>

<div style="clear:both; padding-bottom:20px;" align="left">
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?=getLink("billing")?>">&nbsp;Back to the 'My Billing'</a>
</div>

<table class="table table-bordered">
	<thead>
		<tr>
			<th><?=$this->order->getTitle("added", "Date")?></th>
			<th>Description</th>
			<th>Transaction ID</th>
			<th><?=$this->order->getTitle("type", "Type")?></th>
			<th>Amount</th>
		</tr>
	</thead>
	<tbody>
		<? if (count($this->transactions)>0){?>
			<? foreach ($this->transactions as $i => $t) { ?>
			<tr id="row-<?=$t->id?>">
				<td><?=$t->added?></td>
				<td><?=htmlspecialchars($t->description)?></td>
				<td><?=htmlspecialchars($t->transaction_id)?></td>
				<td><?=colorBillingType(Billing::$type_name[$t->type])?></td>
				<td>$<?=($t->type==2?"-":"")?><?=number_format($t->amount, 2, ".", ",")?></td>
			</tr>
			<? } ?>
		<? }else{ ?>
			<tr id="row-1">
				<td>&nbsp;</td>
				<td colspan="2" align="center">Records not found.</td>
				<td>&nbsp;</td>
			</tr>
		<? } ?>
	</tbody>
</table>

<div class="col-12">
	@pager@
</div>