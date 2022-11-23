<?php
/**
 * @version		1.0
 * @since		28.08.12
 * @author		Oleg Ilyushyn, C.A.W., Inc. dba INTECHCENTER
 * @address		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * @email		techsupport@intechcenter.com
 * @copyright	2012 Intechcenter. All Rights Reserved
 */
$baseUrl = getLink('reports/sales') . '/';
if (isset($_GET['order'])) {
		$baseUrl.="order/" . urlencode($_GET['order']) . '/';
}
if (isset($_GET['arrow'])) {
		$baseUrl.="arrow/" . urlencode($_GET['arrow']) . '/';
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

				document.location.href = '<?= $baseUrl ?>'+getFilters();
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
				window.open('<?= $baseUrl ?>'+filter_string);
		}
	
		function invoiceSelected() {
				var order_ids = [];
				$('.order-ch:checked').each(function() {
						order_ids.push($(this).val());
				});
				if (order_ids.length == 0) {
						alert('You should select at least one record to print');
						return;
				}
				filter_string = '/ids/'+order_ids.join(',')+'/';
				window.open('<?= getLink('reports', 'printinvoices') ?>'+filter_string);
		}
		$(document).ready(function() {
				$('#select_all').click(function() {
						if ($('.order-ch:checked').size() == $('.order-ch').size()) {
								$('.order-ch').attr('checked', null);
								$(this).attr('checked', null);
						} else {
								$('.order-ch').attr('checked', 'checked');
								$(this).attr('checked', 'checked');
						}
				});
				$('.order-ch').click(function() {
						if ($('.order-ch:checked').size() == $('.order-ch').size()) {
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
<form action="<?= getLink("reports/sales") ?>" method="post" id="filter_form">
		<?= formBoxStart("Filter") ?>
		<table cellspacing="5" cellpadding="5" border="0">
				<tr>
						<td>@period@</td>
						<td>@member_id@</td>
						<td>@order_id@</td>
				</tr>
				<tr>
						<td>@item@</td>
						<td>@who_help@</td>
						<td colspan="2">&nbsp;</td>
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
				<th align="center" style="width:20px;" class="grid-head-left"><input type="checkbox" id="select_all"/></th>
				<th><?= $this->order->getTitle('register_date', 'Date, Time') ?></th>
				<th><?= $this->order->getTitle('id', 'Order #') ?></th>
				<th><?= $this->order->getTitle('status', 'Status') ?></th>
				<th>Customer's Name</th>
				<th>Product purchased</th>
				<th>Number of Users</th>
				<th class="grid-head-right"><?= $this->order->getTitle('amount', 'Order Amount') ?></th>
		</tr>
		<?php if (count($this->orders) > 0) {
				$total = 0;
				?>
				<?php foreach ($this->orders as $i => $order) {
						$total += $order['amount'];
						?>
						<tr class="grid-body<?= ($i == 0) ? ' first-row' : '' ?>">
								<td align="center" class="grid-body-left"><input type="checkbox" class="order-ch" value="<?= $order['id'] ?>"/></td>
								<td class="grid-body" align="center"><a href="<?= getLink("orders", "show", "id", $order['id']) ?>"><?= date('m/d/Y H:i:s', strtotime($order['register_date'])) ?></a></td>
								<td class="grid-body" align="center"><a href="<?= getLink("orders", "show", "id", $order['id']) ?>"><?= $order['id'] ?></a></td>
								<td class="grid-body" align="center"><?= Orders::getStatusLabel($order['status']) ?></td>
								<td class="grid-body"><a href="<?= getLink("orders", "show", "id", $order['id']) ?>"><?= $order['first_name'] ?> <?= $order['last_name']; ?></a></td>
								<td class="grid-body"><?= $order['product_name']; ?></td>
								<td class="grid-body" align="center"><?= $order['users']; ?></td>
								<td class="grid-body-right" align="right"><?= empty($order['amount'])?'':('$'.number_format($order['amount'], 2)) ?></td>
						</tr>
		<?php } ?>
				<tr class="grid-body">
						<td colspan="7" align="right" class="grid-body-left"><strong>TOTAL:</strong></td>
						<td class="grid-body-right" align="right"><strong>$<?= number_format($total, 2) ?></strong></td>
				</tr>
<?php } else { ?>
				<tr class="grid-body first-row">
						<td class="grid-body-left grid-body-right" colspan="8" align="center"><i>No records.</i></td>
				</tr>
<?php } ?>
</table>
@pager@
<table cellpadding="0" cellspacing="3" border="0">
		<tr>
				<td><?= functionButton('Export selected', "exportReport('selected')") ?></td>
				<td><?= functionButton('Export all found', "exportReport('all')") ?></td>
				<td><?= functionButton('Print Invoices', "invoiceSelected()") ?></td>

		</tr>
</table>