<?php
/* * *************************************************************************************************
 * Show order template
 *                                                                              		
 *                                                                                 
 * Client: 	FreightDragon                                                         
 * Version: 	1.0                                                                   
 * Date:    	2012-09-27                                                            
 * Author:  	C.A.W., Inc. dba INTECHCENTER                                         
 * Address: 	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076            
 * E-mail:	techsupport@intechcenter.com                                            
 * CopyRight 2011 FreightDragon. - All Rights Reserved                             
 * ************************************************************************************************** */
?>
<script type="text/javascript">
		//<![CDATA[
    function saveComment()
    {
        if (jQuery.trim($('#comment').val()) == '') return false;

        $.ajax({
            url: BASE_PATH+'application/ajax/save_comment.php'
						, type: 'POST'
						, dataType: 'json'
						, data:
								{
                id: '@id@'
								, comment: $('#comment').val()
            }
						, success: function(response)
            {
                if (response != '')
                {
                    if (response.code == 0)
                    {
                        $('#comment').val('');
                        $('#comment_total').html(parseInt($('#comment_total').html()) + 1);
                        $('#comments_layer').html(response.message + $('#comments_layer').html());
                    }
                    else
                    {
                        alert(response.message);
                    }
                }
            }
						, async: false
        });

        return false;
    }

    function deleteComment(id)
    {
        if (!confirm('Are you sure you want to delete?')) return false;

        $.ajax({
            url: BASE_PATH+'application/ajax/delete_comment.php'
						, type: 'POST'
						, dataType: 'json'
						, data:
								{
                id: id
            }
						, success: function(response)
            {
                if (response != '')
                {
                    if (response.code == 0)
                    {
                        $('#comment_total').html(parseInt($('#comment_total').html()) - 1);
                        $('#comment_' + id).remove();
                    }
                    else
                    {
                        alert(response.message);
                    }
                }
            }
						, async: false
        });

        return false;
    }
		//]]>
</script>
<br />
<div align="center">
		<table cellpadding="20" cellspacing="20" border="0">
				<tr>
						<td valign="top">
								<table width="450px" cellpadding="0" cellspacing="1" border="0" class="grid">
										<tr class="grid-head">
												<th>Order Info</th>
										</tr>
										<tr>
												<td bgcolor="#ffffff" align="left">
														<table cellpadding="0" cellspacing="1" border="0" width="100%">
																<tr class="grid-body">
																		<td><b>Order #<b/></td>
																		<td><span style="color:#FF0000">@id@</span></td>
																</tr>
																<tr class="grid-body">
																		<td><b>Date</b></td>
																		<td>@register_date_format@</td>
																</tr>
																<tr class="grid-body">
																		<td><b>Total Amount</b></td>
																		<td><span style="color:#FF0000">$@amount@</span></td>
																</tr>
																<tr class="grid-body">
																		<td><b>Coupon</b></td>
																		<td>@coupon_code@</td>
																</tr>
																<tr class="grid-body">
																		<td><b>Status</b></td>
																		<td>
																				@status@
																		</td>
																</tr>
														</table>
														<?php if ($this->data['did_any_help'] == 1): ?>
																<table cellpadding="0" cellspacing="1" border="0">
																		<tr>
																				<td><strong>Customer Service Representative help:</strong></td>
																				<td><?php echo $this->data['who_help']; ?></td>
																		</tr>
																</table>
														<?php endif; ?>
												</td>
										</tr>
								</table>
						</td>
						<td valign="top">
								<table width="450px" cellpadding="0" cellspacing="1" border="0" class="grid">
										<tr class="grid-head">
												<th>
														Customer Info
												</th>
										</tr>
										<tr>
												<td bgcolor="#ffffff" align="left">
														<table cellpadding="0" cellspacing="1" border="0" width="100%">
																<tr class="grid-body">
																		<td><b>Account #</b></td>
																		<td><a href="<?= getLink("/members/edit/id/@member_id@") ?>"><span style="color:#FF0000;text-decoration:none">@member_id@</span></a></td>
																</tr>
																<tr class="grid-body">
																		<td><b>First Name</b></td>
																		<td>@first_name@</td>
																</tr>
																<tr class="grid-body">
																		<td><b>Last Name</b></td>
																		<td>@last_name@</td>
																</tr>
																<tr class="grid-body">
																		<td><b>Company</b></td>
																		<td>@company@</td>
																</tr>
																<tr class="grid-body">
																		<td><b>Address</b></td>
																		<td>@address@</td>
																</tr>
																<tr class="grid-body">
																		<td><b>City</b></td>
																		<td>@city@</td>
																</tr>
																<tr class="grid-body">
																		<td><b>State</b></td>
																		<td>@state@</td>
																</tr>
																<tr class="grid-body">
																		<td><b>Zip</b></td>
																		<td>@zip@</td>
																</tr>
														</table>
												</td>
										</tr>
								</table>
						</td>
				</tr>
				<tr>
						<td valign="top">
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
												if (count($this->products) > 0) {
														foreach ($this->products as $i => $product) {
																?>
																<tr class="grid-body<?= ($i == 0) ? ' first-row' : '' ?>">
																		<td class="grid-body-left"><?= $product['item'] ?></td>
																		<td class="grid-body-left"><?= $product['product'] ?></td>
																		<td class="grid-body-left"><?= $product['quantity'] ?></a></td>
																		<td class="grid-body-left">$<?= number_format($product['price'], 2) ?></td>
																		<td class="grid-body-left grid-body-right">$<?= number_format($product['total'], 2) ?></td>
																</tr>
														<?php } ?>
														<?php if ($this->data["discount"] > 0) { ?>
																<tr class="grid-body">
																		<td colspan="4" align="center" class="grid-body-left">DISCOUNT</td>
																		<td class="grid-body-left grid-body-right"><strong>$<?= number_format($this->data["discount"], 2) ?></strong></td>
																</tr>
																<?php
														}
												} else {
														?>
														<tr class="grid-body">
																<td colspan="5" align="center">No records</td>
														</tr>
												<? } ?>
										</tbody>
								</table>
						</td>
						<td valign="top">
								<table width="450px" cellpadding="0" cellspacing="1" border="0" class="grid">
										<tr class="grid-head">
												<th>
														Card Info
												</th>
										</tr>
										<tr>
												<td bgcolor="#ffffff" align="left">
														<table cellpadding="0" cellspacing="1" border="0" width="100%">
																<tr class="grid-body">
																		<td><b>Holder Name</b></td>
																		<td>@card_first_name@ @card_last_name@</td>
																</tr>
																<tr class="grid-body">
																		<td><b>Type</b></td>
																		<td>@card_type@</td>
																</tr>
																<tr class="grid-body">
																		<td><b>Number</b></td>
																		<td>@card_number@</td>
																</tr>
																<tr class="grid-body">
																		<td><b>Expiration Date</b></td>
																		<td>@card_expire@</td>
																</tr>
																<tr class="grid-body">
																		<td><b>CVV</b></td>
																		<td>@card_cvv2@</td>
																</tr>
														</table>
												</td>
										</tr>
								</table>
						</td>
				</tr>
				<tr>
						<td align="center">
								<?= backButton(getLink("reports", "sales")); ?>
						</td>
						<td>
								<?= simpleButton("Print", getLink("orders", "show", "id", "@id@", "print")); ?>
						</td>
				</tr>
				<tr>
						<td valign="top" colspan="2">
								<table width="910px" cellpadding="0" cellspacing="1" border="0" class="grid">
										<tr>
												<th>
										<table width="100%" cellpadding="0" cellspacing="0" border="0" class="nogrid">
												<tr>
														<td>Comments (<span id="comment_total">@comment_total@</span>)</td>
												</tr>
										</table>
										</th>
				</tr>
				<tr>
						<td bgcolor="#ffffff" align="left">
								<table width="100%" cellpadding="0" cellspacing="0" border="0">
										<tr>
												<td style="padding:10px" valign="top">
														<div id="comments_layer">@comments@</div>
												</td>
												<td style="padding:10px" width="430px" valign="top" align="right">
														<textarea id="comment" name="comment" style="width:100%;height:100px"></textarea>
														<?= functionButton("Comment", "saveComment()"); ?>
												</td>
										</tr>
								</table>
						</td>
				</tr>
		</table>
</td>
</tr>
</table>
</div>
<br />
<br />
