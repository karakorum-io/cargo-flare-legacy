<? include(TPL_PATH . "myaccount/menu.php"); ?>
<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
	<img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?= getLink("billing") ?>">&nbsp;Back to the My Billing</a>
</div>
<?= formBoxStart("Renew license") ?>
<form action="<?= getLink("billing", "renew") ?>" method="post">
	<table cellspacing="5" cellpadding="5" border="0">
		<tr>
			<td colspan="2">
				<table cellpadding="0" cellspacing="0" class="grid" width="100%">
					<thead>
						<tr class="grid-head">
							<th class="grid-head-left">Item #</th>
								<th>Product</th>
								<th>Quantity</th>
								<th>Price</th>
								<th class="grid-head-right">Total</th>
							</tr>
					</thead>
					<tbody>
						<?php
							$total = 0;
							foreach ($this->products as $i => $product) {
						?>
						<tr class="grid-body<?= ($i == 0) ? ' first-row' : '' ?>">
							<td class="grid-body-left"><?= $product['item'] ?></td>
							<td class="grid-body-left"><?= $product['product'] ?></td>
							<td class="grid-body-left"><?= $product['quantity'] ?></a></td>
							<td class="grid-body-left">$<?= number_format($product['price'], 2) ?></td>
							<td class="grid-body-left grid-body-right">$<?= number_format($product['total'], 2) ?></td>
						</tr>
						<?php
								$total += $product['total'];
							}
						?>
						<tr class="grid-body">
							<td colspan="4" align="right" class="grid-body-left">TOTAL</td>
							<td class="grid-body-left grid-body-right"><strong>$<?= number_format($total, 2) ?></strong></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td>@cc_id@</td>
		</tr>
		<tr>
			<td>
				<br />
				<? if ($this->pgw == 1) { ?>
				<img src="<?= SITE_IN ?>images/icons/paypal_logo.png" width="75" height="21" alt="PayPal" />
				<? } ?>
				<? if ($this->pgw == 2) { ?>
				<img src="<?= SITE_IN ?>images/icons/anet_logo.png" width="102" height="16" alt="Authorize.net" />
				<? } ?>
			</td>
		</tr>
	</table>
	<br />
	<?php echo submitButtons(getLink("billing"), "Renew"); ?>
</form>
<?=formBoxEnd()?>