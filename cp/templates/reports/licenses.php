<?php
/**
 * @version		1.0
 * @since		28.08.12
 * @author		Oleg Ilyushyn, C.A.W., Inc. dba INTECHCENTER
 * @address		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * @email		techsupport@intechcenter.com
 * @copyright	2012 Intechcenter. All Rights Reserved
 */
$baseUrl = getLink('reports/licenses').'/';
if (isset($_GET['order'])) {
	$baseUrl.="order/".urlencode($_GET['order']).'/';
}
if (isset($_GET['arrow'])) {
	$baseUrl.="arrow/".urlencode($_GET['arrow']).'/';
}
?>
@flash_message@
<script type="text/javascript">
	function getFilters() {
		var filter_string = '';
		$('#filter_form').find('input:not(:disabled), select').each(function() {
			if ($.trim($(this).val()) != '') {
				var val = $(this).hasClass('hasDatepicker')?$.trim($(this).val()).replace(/\//g, '_'):$.trim($(this).val());
				filter_string += $(this).attr('name')+'/'+val+'/';
			}
		});
		return filter_string;
	}
	function applyFilter() {

		document.location.href = '<?=$baseUrl?>'+getFilters();
	}
	function exportReport(type) {
		var filter_string = getFilters();
		if (type == 'selected') {
			var order_ids = [];
			$('.order-ch:checked').each(function() {
				order_ids.push($(this).val());
			});
			if (order_ids.length == 0) {
				alert('You should select at least one record to export');
				return;
			}
			filter_string += 'order_ids/'+order_ids.join(',')+'/';
		}
		filter_string += 'export/'+type+'/';
		window.open('<?=$baseUrl?>'+filter_string);
	}
	$(document).ready(function() {
		$('#select_all').click(function() {
			if ($('.license-ch:checked').size() == $('.license-ch').size()) {
				$('.license-ch').attr('checked', null);
				$(this).attr('checked', null);
			} else {
				$('.license-ch').attr('checked', 'checked');
				$(this).attr('checked', 'checked');
			}
		});
		$('.license-ch').click(function() {
			if ($('.order-ch:checked').size() == $('.license-ch').size()) {
				$('#select_all').attr('checked', 'checked');
			} else {
				$('#select_all').attr('checked', null);
			}
		});
		$('#period').change(function() {
			if($(this).val() == 'date_range') {
				$('.date-range').show();
				$('.date-range input').attr('disabled', null);
			} else {
				$('.date-range').hide();
				$('.date-range input').attr('disabled', 'disabled');
			}
		});
		$('#period').change();
	});
</script>
<br />
<form action="<?= getLink("reports/licenses")?>" method="post" id="filter_form">
	<?= formBoxStart("Filter") ?>
	<table cellspacing="5" cellpadding="5" border="0">
		<tr>
			<td>@period@</td>
			<td>@contactname@</td>
			<td>@member_id@</td>
		</tr>
		<tr>
			<td>@status@</td>
			<td colspan="4">&nbsp;</td>
		</tr>
		<tr class="date-range" style="display: none;">
			<td>Range:</td>
			<td colspan="5">@start_date@&nbsp;&nbsp;-&nbsp;&nbsp;@end_date@</td>
		</tr>
		<tr>
			<td colspan="6" style="text-align: left;"><?= functionButton("Apply Filter", 'applyFilter()') ?></td>
		</tr>
	</table>
	<?= formBoxEnd(); ?>
</form>
<br/>
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid">
	<tr class="grid-head">
		<th style="width:20px;" class="grid-head-left"><input type="checkbox" id="select_all"/></th>
		<th><?=$this->order->getTitle('p.name', 'Product Name')?></th>
		<th><?=$this->order->getTitle('l.users', '# of Users / Account #')?></th>
		<th><?=$this->order->getTitle('m.contactname', 'Contact Name')?></th>
		<th><?=$this->order->getTitle('m.companyname', 'Company Name')?></th>
		<th><?=$this->order->getTitle('l.created', 'Register Date')?></th>
		<th><?=$this->order->getTitle('l.expire', 'Expiration Date')?></th>
		<th>Status</th>
		<th class="grid-head-right"><?=$this->order->getTitle('ds.billing_autopay', 'Auto Renewal')?></th>
		<th colspan="4" class="grid-head-right">Actions</th>
	</tr>
	<?php if (count($this->licenses) > 0) { ?>
	<?php foreach ($this->licenses as $i => $license) { ?>
		<tr class="grid-body<?=($i==0)?' first-row':''?>">
			<td class="grid-body-left"><input type="checkbox" class="order-ch" value="<?=$license['id']?>"/></td>
			<td class="grid-body"><?=$license['product_name']?></td>
			<td class="grid-body"><?=$license['users']." / "?><a href="<?=getLink("members","edit", "id", $license['member_id'])?>"><?=$license['member_id']?></a></td>
			<td class="grid-body"><?=$license['contactname']?></td>
			<td class="grid-body"><?=$license['companyname']?></td>
			<td class="grid-body"><?=date('m/d/Y', strtotime($license['created']))?></td>
			<td class="grid-body"><?=date('m/d/Y', strtotime($license['expire']))?></td>
			<td class="grid-body"><?=($license['is_frozen']==1?"Closed":$license['status']);?></td>
			<td class="grid-body-right" align="center"><?=$license['billing_autopay']?'Yes':'No'?></td>
			<td style="width: 16px;"><?=renewIcon(getLink("members", "signas", "id", $license['member_id']))?></td>
			<td style="width: 16px;"><?=(($license['billing_autopay'])?cancelIcon(getLink("reports", "licenses", "id", $license['member_id'], "cancel")):"");?></td>
			<td style="width: 16px;" class="grid-body-right">
					<?=($license['is_frozen']==1?reactivateIcon(getLink("reports", "licenses", "id", $license['member_id'], "reactivate")):closeIcon(getLink("reports", "licenses", "id", $license['member_id'], "close")));?>
			</td>
		</tr>
		<?php } ?>
	<?php } else { ?>
	<tr class="grid-body first-row">
		<td class="grid-body-left grid-body-right" colspan="8" align="center"><i>No records.</i></td>
	</tr>
	<?php } ?>
</table>
@pager@
<table cellpadding="0" cellspacing="3" border="0">
	<tr>
		<td><?=functionButton('Export selected', "exportReport('selected')")?></td>
		<td><?=functionButton('Export all found', "exportReport('all')")?></td>
	</tr>
</table>