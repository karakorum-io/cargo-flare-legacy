<? include(TPL_PATH . "myaccount/menu.php");
   $phoneNumbersSelected = $_SESSION['SMS']['phoneNumbersSelected'];
   $usersSelected        = $_SESSION['SMS']['usersSelected'];
   $credit               = $_SESSION['SMS']['credit'];
   

?>

<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?=getLink("billing")?>">&nbsp;Back to the list</a>
</div>
<form action="<?=getLink("billing", "sms_order_confirmation")?>" method="post">
    
    <?= formBoxStart("Sms Order Confirmation") ?>
        <table cellspacing="5" cellpadding="5" border="0" width="80%">
       
            <tr>
              <td>
              
              <div id="order">				
                <table cellpadding="0" cellspacing="0" class="grid" width="100%">
						<thead>
                             
								<tr class="grid-head">
										<th class="grid-head-left">Phone Number #</th>
										<th>User</th>
										<th>Quantity</th>
										<th>Price</th>
                                        <th class="grid-head-right">Total</th>
								</tr>
                               
						</thead>
						<tbody>
                                <?php  
								   $total =0;
								   $numberPrice = 4.99;
								  foreach($phoneNumbersSelected as $key=>$value){ 
								      $totalTemp = $numberPrice ;
									  
									  $total = $total + $totalTemp;
									  
									  $memberID = $usersSelected[$key];
								?>
										<tr class="grid-body first-row">
												<td class="grid-body-left"><?php print $value; ?></td>
												<td class="grid-body-left"><?php print $_SESSION['SMS']['assigns'][$memberID]['contactname']; ?></td>
												<td class="grid-body-left">1</td>
												<td class="grid-body-left">$<?php print $numberPrice;?></td>
                                                <td align="right" class="grid-body-left grid-body-right">$<?php print $totalTemp;?></td>
										</tr>
									<?php  } 
									$total += $credit;
									?> 
                                    
                                <tr class="grid-body">
										<td colspan="4" align="center" class="grid-body-left">CREDIT AMOUNT</td>
                                        
										<td align="right" class="grid-body-left grid-body-right"><strong>$<?php print $credit;?></strong></td>
								</tr>											
								<tr class="grid-body">
										<td colspan="4" align="center" class="grid-body-left">TOTAL</td>
                                        
										<td align="right" class="grid-body-left grid-body-right"><strong>$<?php print $total;?></strong></td>
								</tr>
						</tbody>
				</table>
										<br><br>
						<p>Payment will be charged to:</p>
						  <? 
							 
							 if (isset($this->cards) && count($this->cards)>0){?>
								<? foreach ($this->cards as $i => $t) { ?>
                                
                                   <strong>Card Holder Name</strong>: <?=$t["cc_fname"]?> <?=$t["cc_lname"]?><br>
                                    <strong>Card Number</strong>: <?=$t["cc_number"]?><br>
                                    <strong>Expiration Date</strong>: <?=$t["cc_month"]?>/<?=$t["cc_year"]?><br>
                                  <? } ?>
                            <? }else{ ?>
                                <tr class="grid-body first-row" id="row-1">
                                    <td align="center" colspan="3">Records not found.</td>
                                </tr>
                            <? } ?>
                          </div>
              </td>
            </tr>
            <tr>
              <td align="center">
              <?= submitButtons( "",  "Process Order", "submit_button", "submit", "")?>
            <!--input type="submit" id="select_button" value="Process Sms Orders"  onclick="showNumbers();" style="-webkit-user-select: none;">
                                       <input type="hidden" name="submit" value="processsmsorders"  /-->
              </td>
            </tr>
        </table>
        <?= formBoxEnd() ?>
        <br />
    
</form>