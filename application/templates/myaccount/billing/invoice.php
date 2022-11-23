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
	foreach ($this->products as $i => $product) { ?>
		<tr class="grid-body<?=($i==0)?' first-row':''?>">
			<td class="grid-body-left"><?=$product['item']?></td>
			<td class="grid-body-left"><?=$product['product']?></td>
			<td class="grid-body-left"><?=$product['quantity']?></a></td>
			<td class="grid-body-left">$<?=number_format($product['price'], 2)?></td>
			<td class="grid-body-left grid-body-right">$<?=number_format($product['total'], 2)?></td>
		</tr>
	<?php
		$total += $product['total'];
	} ?>
	<tr class="grid-body">
		<td colspan="4" align="center" class="grid-body-left">TOTAL</td>
		<td class="grid-body-left grid-body-right"><strong>$<?=number_format($total, 2)?></strong></td>
	</tr>
	</tbody>
</table>