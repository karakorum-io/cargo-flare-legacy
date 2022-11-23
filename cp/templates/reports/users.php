<?php
/**
 * @version		1.0
 * @since		28.08.12
 * @author		Oleg Ilyushyn, C.A.W., Inc. dba INTECHCENTER
 * @address		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * @email		techsupport@intechcenter.com
 * @copyright	2012 Intechcenter. All Rights Reserved
 */
$baseUrl = getLink('reports/users').'/';
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
				filter_string += $(this).attr('name')+'/'+$.trim($(this).val())+'/';
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
			filter_string += 'ids/'+order_ids.join(',')+'/';
		}
		filter_string += 'export/'+type+'/';
		window.open('<?=$baseUrl?>'+filter_string);
	}
	$(document).ready(function() {
		$('#select_all').click(function() {
			if ($('.member-ch:checked').size() == $('.member-ch').size()) {
				$('.member-ch').attr('checked', null);
				$(this).attr('checked', null);
			} else {
				$('.member-ch').attr('checked', 'checked');
				$(this).attr('checked', 'checked');
			}
		});
		$('.member-ch').click(function() {
			if ($('.order-ch:checked').size() == $('.member-ch').size()) {
				$('#select_all').attr('checked', 'checked');
			} else {
				$('#select_all').attr('checked', null);
			}
		});
	});
</script>
<br />
<form action="<?= getLink("reports/users")?>" method="post" id="filter_form">
	<?= formBoxStart("Filter") ?>
	<table cellspacing="5" cellpadding="5" border="0">
		<tr>
			<td>@id@</td>
			<td>@contactname@</td>
			<td>@companyname@</td>
		</tr>
		<tr>
			<td>@zip@</td>
			<td>@email@</td>
			<td colspan="2">&#8203;</td>
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
		<th><?=$this->order->getTitle('id', 'Company ID')?></th>
		<th><?=$this->order->getTitle('contactname', 'Contact Name')?></th>
		<th><?=$this->order->getTitle('companyname', 'Company Name')?></th>
		<th><?=$this->order->getTitle('reg_date', 'Registered')?></th>
		<th><?=$this->order->getTitle('last_login', 'Date/Time of last login')?></th>
		<th class="grid-head-right"><?=$this->order->getTitle('status', 'Status')?></th>
		<th class="grid-head-right" colspan="4">Actions</th>
	</tr>
	<?php if (count($this->members) > 0) { ?>
	<?php foreach ($this->members as $i => $member) { ?>
		<tr class="grid-body<?=($i==0)?' first-row':''?>">
			<td class="grid-body-left"><input type="checkbox" class="member-ch" value="<?=$member['id']?>"/></td>
			<td class="grid-body"><?=$member['company_id']?></td>
			<td class="grid-body"><?=$member['contactname'];?></td>
			<td class="grid-body"><?=$member['companyname'];?></td>
			<td class="grid-body"><?=date('m/d/Y H:i:s', strtotime($member['reg_date']))?></td>
			<td class="grid-body"><?=$member['last_login']==""?"":date('m/d/Y H:i:s', strtotime($member['last_login']))?></td>
			<td class="grid-body-right"><?=$member['status']?></td>
			<td style="width: 16px;"><?=loginIcon(getLink("members", "signas", "id", $member['id']))?></td>
      <td style="width: 16px;"><?=docsIcon(getLink("documents", "member_id", $member['id']))?></td>
      <td style="width: 16px;"><?=payIcon(getLink("payments", "member_id", $member['id']))?></td>
      <td  class="grid-body-right" style="width: 16px;"><?=editIcon(getLink("members", "edit", "id", $member['id']))?></td>
		</tr>
		<?php } ?>
	<?php } else { ?>
	<tr class="grid-body first-row">
		<td class="grid-body-left grid-body-right" colspan="7" align="center"><i>No records.</i></td>
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