<br /><br />
<? if(!isset($this->hideprint)){?>
<div align="right" style="text-align: right;">
		<?= functionButton("Print", "$('#printme').printArea();") ?>
</div>
<br /><br />

<div id="printme">
<?}?>


		<div style="width: 550px;">
				<h2 style="font-size: 18px;"><?= $this->daffny->cfg["site_title"] ?></h2>
				<table width="100%" cellpadding="5" cellspacing="0" border="0">
						<tbody><tr valign="top">
										<td>
												<?= $this->daffny->cfg["address"] ?>
										</td>
										<td align="right">
												Ph. <?= $this->daffny->cfg["phone"] ?><br>
										</td>
								</tr>
								<tr valign="top">
										<td><a href="http://freightdragon.com">www.freightdragon.com</a></td>
										<td align="right"><a href="mailto:<?= $this->daffny->cfg["email"] ?>"><?= $this->daffny->cfg["email"] ?></a></td>
								</tr>
						</tbody>
				</table>


				<h1 style="text-align: center;">INVOICE</h1>

				<table cellpadding="5" cellspacing="0" border="0">
						<tbody><tr>
										<td width="100px"><strong>Invoice #:</strong></td>
										<td>@order_id@</td>
								</tr>
								<tr>
										<td><strong>Date &amp; Time:</strong></td>
										<td>@order_date@</td>
								</tr>
								<tr>
										<td nowrap="nowrap"><strong>Order Status:</strong></td>
										<td>@status@</td>
								</tr>
						</tbody>
				</table>

				<hr />

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
								foreach ($this->products as $i => $product) {
										?>
										<tr class="grid-body<?= ($i == 0) ? ' first-row' : '' ?>">
												<td class="grid-body-left"><?= $product['item'] ?></td>
												<td class="grid-body-left"><?= $product['product'] ?></td>
												<td align="center"  class="grid-body-left"><?= $product['quantity'] ?></a></td>
												<td align="right" class="grid-body-left">$<?= number_format($product['price'], 2) ?></td>
												<td align="right" class="grid-body-left grid-body-right">$<?= number_format($product['price'] * $product['quantity'], 2) ?></td>
										</tr>
										<?php
								}
								?>
								<?php
								if ( $this->data["discount"] > 0 ) {
										?>
										<tr class="grid-body">
												<td colspan="4" align="center" class="grid-body-left">DISCOUNT</td>
												<td align="right" class="grid-body-left grid-body-right"><strong>$<?= number_format($this->data["discount"], 2) ?></strong></td>
										</tr>
										<?php
								}
								?>
								<tr class="grid-body">
										<td colspan="4" align="center" class="grid-body-left">TOTAL</td>
										<td align="right" class="grid-body-left grid-body-right"><strong>$<?= number_format($this->data["amount"], 2) ?></strong></td>
								</tr>
						</tbody>
				</table>



				<br /><br /><br />
				<table width="100%" cellpadding="5" cellspacing="0" border="0">
						<tbody><tr valign="top">
										<td width="15%">
												<strong>Billed to:</strong>
										</td>
										<td width="35%">
												@first_name@ @last_name@<br>
												@company@<br>
												<br>
												@address@<br>
												Ph. @phone@<br>
												<a href="mailto:@email@">@email@</a>
										</td>
										<td width="15%">
												<strong>Paid by:</strong>
										</td>
										<td width="35%">
												<strong>Card Holder Name</strong>: @card_first_name@ @card_last_name@<br/>
												<strong>Card Number</strong>: @card_number@<br/>
												<strong>Exp. Date</strong>: @card_expiration@<br/>
										</td>
								</tr>
						</tbody>
				</table>
		</div>
<? if(!isset($this->hideprint)){?>
</div>
<?= backButton(getLink("orders", "show", "id", $_GET["id"])); ?>
<? } ?>