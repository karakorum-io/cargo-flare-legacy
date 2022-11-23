<?php
$origin = $this->entity->getOrigin();
	$destination = $this->entity->getDestination();
?>
<table  width="100%" cellspacing ="0" cellpadding="0">
	<tr>
	<td valign="top" width="60%">
<div class="quote-info" style="width:97%; margin-bottom: 10px;">
	
	<div id="carrier_value">
    <table cellspacing="0" cellpadding="0" border="0" width="100%">
			<tr>
				<td style="vertical-align:top;" valign="top" width="50%">
                        <table width="100%" cellpadding="1" cellpadding="1">
                            <tr><td colspan="3"><div ><p class="block-title">Carrier Information</p></div></td></tr>
                            <tr><td width="23%"><strong>Company Name</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $this->dispatch->carrier_company_name ?></td></tr>
                            <tr><td width="23%"><strong>Contact Name</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $this->dispatch->carrier_contact_name ?></td></tr>
                            <tr><td width="23%"><strong>Phone 1</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $this->dispatch->carrier_phone_1 ?></td></tr>
                            <tr><td width="23%"><strong>Phone 2</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $this->dispatch->carrier_phone_2 ?></td></tr>
                            <tr><td width="23%"><strong>Fax</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $this->dispatch->carrier_fax ?></td></tr>
                            <tr><td width="23%"><strong>Email</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><a href="mailto:<?= $this->dispatch->carrier_email ?>"><?= $this->dispatch->carrier_email ?></a></td></tr>
                            
                            
                            <tr><td width="23%"><strong>Driver Name</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $this->dispatch->carrier_driver_name ?></td></tr>
                            <tr><td width="23%"><strong>Driver Phone</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $this->dispatch->carrier_driver_phone ?></td></tr>
                            </table>
                   </td>
                   <td style="vertical-align:top;">
                       <?php $vehicles = $this->entity->getVehicles();?>
                       <table width="100%" cellpadding="1" cellpadding="1">
                        <tr><td colspan="3"><div ><p class="block-title">Vehicle Information</p></div></td></tr>
					<?php if (count($vehicles) == 0) : ?>
					<?php elseif (count($vehicles) == 1) : ?>
						<?php $vehicle = $vehicles[0]; ?>
                        <tr><td width="15%"><strong>Make/Model </strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $vehicle->year ?> <?= $vehicle->make ?> <?= $vehicle->model ?><?php if($vehicle->inop){?>(<span style="color:red;weight:bold;"><?= "Inop" ?></span>)<?php }?></td></tr>
						<!----<tr><td width="15%"><strong>VIN: </strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?php  print $vehicle->vin ?></td></tr>---->
                        <tr><td width="15%"><strong>Type: </strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $vehicle->type ?></td></tr>
                        
                        
						
					<?php else : ?>
                    <tr><td colspan="3">
						<span class="like-link multi-vehicles" style="font-size:18px;">Multiple Vehicles <b>(<?php print count($vehicles);?>)</b></span>
                        
						<div class="vehicles-info">
                        <table width="100%"   cellpadding="0" cellspacing="1">
                         <tr>
                             <td  style="padding:3px;"><b><p>Year</p></b></td>
                             <td  style="padding:3px;"><b><p><?= Make ?></p></b></td>
							 <td  style="padding:3px;"><b><p><?= Model ?></p></b></td>
                             <td  style="padding:3px;"><b><p><?= Type ?></p></b></td> 
							 <td  style="padding:3px;"><b><p><?= Vin# ?></p></b></td>
							 <td  style="padding:3px;"><b><p><?= Lot# ?></p></b></td>
                             <td  style="padding:3px;"><b><p><?= Inop ?></p></b></td>
						  </tr>
						<?php foreach($vehicles as $vehicle) : ?>
                          <tr>
                             <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->year ?></td>
                             <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->make ?></td>
							 <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->model ?></td> 
                             <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->type ?></td>
                             <td bgcolor="#ffffff" style="padding:3px;"> <?php  print $vehicle->vin ?></td>
							 <td bgcolor="#ffffff" style="padding:3px;"> <?php  print $vehicle->lot ?></td>
                             <td bgcolor="#ffffff" style="padding-left:5px;"> <?php  print $vehicle->inop==0?"No":"Yes"; ?></td>
                           </tr>
						<?php endforeach; ?>
                        </table>
						</div>
						</td></tr>
					<?php endif; ?>
                    <tr><td width="15%"><strong>Ship Via: </strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><span style="color:red;weight:bold;"><?= $this->entity->getShipVia() ?></span></td></tr>
                      <tr><td width="15%"  style="line-height:15px;"><strong>Origin: </strong></td><td width="4%" align="center"><b>:</b></td><td><span class="like-link"
                              onclick="window.open('<?= $origin->getLink() ?>', '_blank')"><?= $origin->getFormatted() ?></span></td></tr>
                      <tr><td width="15%"  style="line-height:15px;"><strong>Destination: </strong></td><td width="4%" align="center"><b>:</b></td><td><span class="like-link"
                              onclick="window.open('<?= $destination->getLink() ?>')"><?= $destination->getFormatted() ?></span></td></tr>
                     <?php if (is_numeric($this->entity->distance) && ($this->entity->distance > 0)) : ?>
                      <tr><td width="15%"  style="line-height:15px;"><strong>Mileage: </strong></td><td width="4%" align="center"><b>:</b></td><td><?= number_format($this->entity->distance, 0, "", "") ?> mi
                                ($ <?= number_format(($this->entity->getCarrierPay(false) / $this->entity->distance), 2, ".", ",") ?>/mi)&nbsp;&nbsp;(<span class='red' onclick="mapIt(<?= $this->entity->id ?>);">MAP IT</span>)</strong></td></tr>
                     <?php endif; ?>
                    </table>
                   </td>
                   </tr>
                    
                    <tr>
                     <td colspan="2" align="left">
                     <div class="order-info1" style="width:97%;float: left; margin-top:10px;">
	<p class="block-title">Dispatch Instructions</p>
	<div>
	    
		<?php //foreach ($this->notes[Note::TYPE_FROM] as $note) : 
		$NoteData = "";
		  if(count($this->notes[Note::TYPE_FROM])>0){
			  
		?>
			<div style="margin-top:5px;border-bottom: 1px solid #CCCCCC;clear:both;">
				
                <?php $NoteData = $this->notes[Note::TYPE_FROM][0]->getText();?>
			</div>
		<?php 
		  }
		//endforeach; ?>
        
        <textarea cols="100" rows="5" readonly="readonly"><?php print $NoteData;?></textarea>
	</div>
</div>
                     </td>
                     </tr>
                </table>            
	</div>
</div>
  </td>
  <td valign="top">
  <div class="quote-info" style="width:97%; margin-bottom: 2px;margin-left:10px;">
	
	<div id="carrier_value">
    <table cellspacing="0" cellpadding="0" border="0" width="100%">
			
                   <tr>
                     <td colspan="2" align="left">
                     <?php
			             $Balance_Paid_By = "";
						   if(in_array($this->entity->balance_paid_by, array(2, 3 , 16 , 17)))   
							   $Balance_Paid_By = "<b>COD</b>";
							
							if(in_array($this->entity->balance_paid_by, array(8, 9 , 18 , 19)))   
							   $Balance_Paid_By = "COP";
							
							if(in_array($this->entity->balance_paid_by, array(12, 13 , 20 , 21,24)))   
							   $Balance_Paid_By = "Billing";
							 
							 if(in_array($this->entity->balance_paid_by, array(14, 15 , 22 , 23))) { 
							 
							 $paymentManager = new PaymentManager($this->daffny->DB);
			                 $shipperPaid = $paymentManager->getFilteredPaymentsTotals($this->entity->id, Payment::SBJ_SHIPPER, Payment::SBJ_COMPANY, false);
							   $Balance_Paid_By = "</span><span class='black'>Please collect </span><span class='red'>$ ".($this->entity->getTotalTariff(false)-$shipperPaid)."</span> <span class='black'>from the customer and</span> <span class='red'>".$this->dispatch->c_companyname."</span> will invoice your company <span class='red'>$ ".($this->entity->getTotalDeposit(false)-$shipperPaid)."</span>";
							   
							 }
			       ?>
                     <div class="quote-info1" style="width:94%;float: left; margin-bottom:10px;margin-top:0px;">
	<p class="block-title">Payment Information</p>
	<div>
		
		 <strong>Payment Amount: </strong><span class='red'><?= $this->entity->getCarrierPay() ?></span><br />
		 <strong>Balance Paid By: </strong><span class='red'><?= $Balance_Paid_By ?> </span><br />
        
        
        
	</div>
</div>
                     </td>
                    </tr> 
                    <tr>
                     <td colspan="2" align="left">
                     <div class="order-info1" style="width:97%;float: left; margin-top:10px;">
	<p class="block-title">Payment Terms</p>
	<div>
	    
		<?php 
						 $payments_terms = $this->entity->payments_terms;
						 if(in_array($this->entity->balance_paid_by, array(2, 3 , 16 , 17 , 8,9,18,19))){   
							$payments_terms = "COD / COP";
						 }
		?>
        
           <textarea cols="70" rows="3" readonly="readonly"><?php print $payments_terms;?></textarea>
		
	</div>
</div>
                     </td>
                     </tr>
                </table>            
	</div>
</div>

<div class="quote-info" style="width:97%; margin-bottom: 10px;margin-left:10px;">
	
	<div id="carrier_value">
    <table cellspacing="0" cellpadding="0" border="0" width="100%">
			 
                    <tr>
                     <td colspan="2" align="left">
                     <div class="order-info1" style="width:97%;float: left; margin-top:0px;">
	<p class="block-title">Terms & Conditions</p>
	<div style="width:100%;height:170px;overflow: scroll;background-color:#ffffff;padding:10px;">
	    
           <?php print $this->dispatch->dispatch_terms;?>
		
	</div>
</div>
                     </td>
                     </tr>
                </table>            
	</div>
</div>
     
  </td>
  </tr>
  
 <tr>
    <td>
     
  </td>
  </tr>
</table>  




