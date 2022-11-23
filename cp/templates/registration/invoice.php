<? if (isset($this->header) && $this->header) { ?>
		<? if (isset($this->print) && $this->print) { ?>
				<br /><br />
				<div align="right" style="text-align: right;">
						<?= functionButton("Print", "$('#printme').printArea();") ?>
				</div>
				<br /><br />
		<? } ?>
		<div id="printme">

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

						<br><hr><br>

						<h1 style="text-align: center;">INVOICE</h1>

						<br><br>

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
												<td>Processed</td>
										</tr>
								</tbody>
						</table>

						<hr />
				<? } ?>
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
												<td align="right" class="grid-body-left grid-body-right">$<?= number_format($product['total'], 2) ?></td>
										</tr>
										<?php
										$total += $product['total'];
								}
								?>
								<?php
								if (isset($this->discount)) {
										$total -= $this->discount;
										?>
										<tr class="grid-body">
												<td colspan="4" align="center" class="grid-body-left" style="color:green">DISCOUNT</td>
												<td align="right" class="grid-body-left grid-body-right"  style="color:green"><strong>$<?= number_format($this->discount, 2) ?></strong></td>
										</tr>
										<?php
								}
								?>
								<tr class="grid-body">
										<td colspan="4" align="center" class="grid-body-left">TOTAL</td>
										<td align="right" class="grid-body-left grid-body-right"><strong>$<?= number_format($total, 2) ?></strong></td>
								</tr>
						</tbody>
				</table>
				<?php if (!isset($this->short)) { ?>
						<br/><br/>
						<p>Payment will be charged to:</p>
						<strong>Card Holder Name</strong>: @card_first_name@ @card_last_name@<br/>
						<strong>Card Number</strong>: @card_number@<br/>
						<strong>Expiration Date</strong>: @card_expiration@<br/>
				<?php } ?>
				<? if (isset($this->header) && $this->header) { ?>

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
		</div>
		<? if (isset($this->print) && $this->print) { ?>
				<br />
				<div align="right" style="text-align: right;">
						<?= functionButton("Print", "$('#printme').printArea();") ?>
				</div>
				<br />
		<? } ?>
<? } ?>