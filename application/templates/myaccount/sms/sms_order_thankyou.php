<? include(TPL_PATH . "myaccount/menu.php");
    $phoneNumbersSelected = $_SESSION['SMS']['phoneNumbersSelected'];
   $usersSelected        = $_SESSION['SMS']['usersSelected'];
   $credit               = $_SESSION['SMS']['credit'];
?>

<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?=getLink("billing")?>">&nbsp;Back to the list</a>
</div>
<form action="" method="post">
         
    <?= formBoxStart("Thank You , Your Order Processed.........") ?>
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
              </td>
            </tr>
           
        </table>
        <?= formBoxEnd() ?>
        <br />
    
</form>