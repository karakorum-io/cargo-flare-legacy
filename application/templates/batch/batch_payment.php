<!---
/***************************************************************************************************
* Transportation Management Software
*
* Client:			FreightDragon
* Version:			1.0
* Start Date:		2011-10-05
* Author:			Freight Genie LLC
* E-mail:			admin@freightdragon.com
*
* CopyRight 2011 FreightDragon. - All Rights Reserved
****************************************************************************************************/
--->
<div id="maildivnew">
    <table cellspacing="2" cellpadding="0" border="0">
        <tr>
           <td colspan="2" style="padding-left:20px;">
             <table cellspacing="2" cellpadding="0" border="0">
             <tbody id="skill_id">
               <tr>
                <td>@mail_to_new@</td>
                <td >
                              <div style="float:left;"><ul class="ul-tags"><li><a href="javascript:void(0);" style="font-size:12px; font-family:Verdana, Geneva, sans-serif; font-weight:bold; color:#000;" onclick="AddEmailExtra();"><b>Add More</b></a></li></ul></div>
                </td>
               </tr>
               
             </tbody>
           </table>
          </td>      
        </tr>
        <tr>
            <td>@mail_subject_new@</td>
        </tr>
        <tr>
            <td>@mail_body_new@</td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;<input type="hidden" name="form_id" id="form_id"  value=""/>
            <input type="hidden" name="entity_id" id="entity_id"  value=""/>
            <input type="hidden" name="skillCount" id="skillCount" value="1">
            </td>
        </tr>
        
    </table>
    
</div>
<?php 
/*
if(is_array($this->payentities) && sizeof($this->payentities)>0){
	 foreach ($this->payentities as $i => $entity)
	 {
		 if($entity->status == Entity::STATUS_POSTED || 
			$entity->status == Entity::STATUS_DISPATCHED ||
			$entity->status == Entity::STATUS_PICKEDUP ||
			$entity->status == Entity::STATUS_DELIVERED
			)
		 $tempPayEntities[] = $entity;
	 }
}
*/
?>


<?php
    $avail_title = Entity::TITLE_FIRST_AVAIL;
    if (isset($_GET['orders'])){
        if (in_array($_GET['orders'], array("notsigned","dispatched","pickedup","delivered","issues","archived"))){
            $avail_title = Entity::TITLE_PICKUP_DELIVERY;
        }
    }
	/*
	print "<pre>";
	print_r($this->entities);
	print "</pre>";
	*/
?>
<table cellspacing="0" cellpadding="0" border="0" class="grid" width="100%">
    <tbody>
    <tr class="grid-head">
        <td class="grid-head-left">
            <?php if (isset($this->order)) : ?>
                <?= $this->order->getTitle("id", "ID") ?>
            <?php else : ?>ID<?php endif; ?>
        </td>
        <td>
		    <?php          if($this->status == Entity::STATUS_ARCHIVED){
			                    if (isset($this->order))
                                     print $this->order->getTitle("archived", "Archived");
								else
                                     print "Archived";
							    
						     }elseif($this->status == Entity::STATUS_PICKEDUP){  
							    if (isset($this->order))
                                     print $this->order->getTitle("actual_pickup_date", "Picked Up");
								else
                                     print "Picked Up";
							  
							 }elseif($this->status == Entity::STATUS_DISPATCHED){
							    if (isset($this->order))
                                     print $this->order->getTitle("dispatched", "Dispatched");
								else
                                     print "Dispatched";
							  
							 }elseif($this->status == Entity::STATUS_DELIVERED){
							     if (isset($this->order))
                                     print $this->order->getTitle("delivered", "Delivered");
								else
                                     print "Delivered";
							    
							} elseif($this->status == Entity::STATUS_POSTED){
							    if (isset($this->order))
                                     print $this->order->getTitle("posted", "Posted");
								else
                                     print "Posted";
							    
							 }elseif($this->status == Entity::STATUS_NOTSIGNED){
							    if (isset($this->order))
                                     print $this->order->getTitle("not_signed", "Not Signed");
								else
                                     print "Not Signed";
							    
							 }elseif($this->status == Entity::STATUS_ISSUES){
							    if (isset($this->order))
                                     print $this->order->getTitle("issue_date", "Issue");
								else
                                     print "Issue";
							    
							 }elseif($this->status == Entity::STATUS_ONHOLD){
							    if (isset($this->order))
                                     print $this->order->getTitle("hold_date", "OnHold");
								else
                                     print "OnHold";
							    
							 }else{
                               	if (isset($this->order))
                                     print $this->order->getTitle("created", "Created");
								else
                                     print "Created";								
 							  }	
			      ?>
            
        </td>
        <?php if ($_GET['orders'] != 'all') { ?>
            <td>Notes</td>
        <?php } else { ?>
            <td>Broker</td>
        <?php } ?>
        <td>
	        <?php if (isset($this->order)) : ?>
		        <?= $this->order->getTitle("shipper", "Shipper") ?>
	        <?php else : ?>Shipper<?php endif; ?>
        </td>
        <td>Vehicle</td>
        <td width="15%">
	        <?php if (isset($this->order)) : ?>
		        <?= $this->order->getTitle("origin", "Origin") ?>
	        <?php else : ?>Origin<?php endif; ?>
	        /
	        <?php if (isset($this->order)) : ?>
		        <?= $this->order->getTitle("destination", "Destination") ?>
	        <?php else : ?>Destinations<?php endif; ?>
        </td>
        <?php if ($avail_title == Entity::TITLE_PICKUP_DELIVERY):?>
            <td>
              <?= $avail_title; ?>
            </td>
            <td>
               <?php print "Delivery"; ?>
            </td>
        <?php else : ?>
            <td><?= $avail_title; ?><?php //print $this->order->getTitle("avail", $avail_title) ?>
        <?php endif; ?>
        <?php if ($avail_title != Entity::TITLE_PICKUP_DELIVERY){?>
        <td>
	        <?php if (isset($this->order)) : ?>
		        <?= $this->order->getTitle("posted", "Posted") ?>
	        <?php else : ?>Posted<?php endif; ?>
        </td>
        <?php } ?>
        <td >
	        Payment Options
        </td>
        <td class="grid-head-right">
	        <?php if (isset($this->order)) : ?>
		        <?= $this->order->getTitle("tariff", "Tariff") ?>
	        <?php else : ?>tariff<?php endif; ?>
        </td>
    </tr>
    <?php 
	
	print "-----".count($this->entities);
	$i=0;
	if (count($this->entities) == 0): ?>
        <tr class="grid-body">
            <td colspan="9" align="center" class="grid-body-left grid-body-right"><i>No records</i></td>
        </tr>
    <?php endif; ?>
    <?php foreach ($this->entities as $i => $entity) : /* @var Entity $entity */ 
	       $i++;
		  $bgcolor = "#ffffff";
		  if($i%2==0)
		    $bgcolor = "#f4f4f4";
		 
	     $isColor = $entity->isPaidOffColor();
		// $isValue = $entity->isPaidOffValue();
	
	?>
        <tr id="order_tr_<?= $entity->id ?>" class="grid-body<?= ($i == 0 ? " first-row" : "") ?>">
            <td align="center" class="grid-body-left" bgcolor="<?= $bgcolor ?>">
                <?php if ($_GET['orders'] != 'all') { ?>
                    <?php //if (!$entity->readonly) : ?>
                        <input type="radio" name="order_id" value="<?= $entity->id ?>" class="order-checkbox"/><br/>
                    <?php //endif; ?>
                    <a href="<?= SITE_IN ?>application/orders/show/id/<?= $entity->id ?>"><?= $entity->getNumber() ?></a>
                    <br/>
                    <a href="<?= SITE_IN ?>application/orders/history/id/<?= $entity->id ?>">History</a>
					
					
					<?php if (isset($_POST['search_string'])) : 
					    print "<br /><b>Status</b><br>";
		                  
						     if ($entity->status == Entity::STATUS_ACTIVE) 
							    print "Active";
							 elseif($entity->status == Entity::STATUS_ONHOLD)
							    print "OnHold";
						     elseif($entity->status == Entity::STATUS_ARCHIVED)
							    print "Archived";	
						     elseif($entity->status == Entity::STATUS_POSTED)
							    print "Posted To FB";
							 elseif($entity->status == Entity::STATUS_NOTSIGNED)
							    print "Not Signed";
							elseif($entity->status == Entity::STATUS_DISPATCHED)
							    print "Dispatched";
							elseif($entity->status == Entity::STATUS_ISSUES)
							    print "Issues";	
							elseif($entity->status == Entity::STATUS_PICKEDUP)
							    print "Picked Up";	
						    elseif($entity->status == Entity::STATUS_DELIVERED)
							    print "Delivered";		
						  ?>
                    <?php endif; ?>
					
                     <?php //if ($this->status == Entity::STATUS_ARCHIVED) : ?>
                     <?php if ($entity->status == Entity::STATUS_ARCHIVED) : ?>
                      <br/> <br/> <a href="<?= SITE_IN ?>application/orders/unarchived/id/<?= $entity->id ?>">UnArchive</a>
                      <?php endif; ?>
					
					
					
                <?php } else { ?>
                    <?= $entity->getNumber()
                    ; ?>
                <?php } ?>
            </td>
			<?php  $assigned = $entity->getAssigned(); ?>
            <td valign="top" style="white-space: nowrap;" bgcolor="<?= $bgcolor ?>">
			    <?php        if($entity->status == Entity::STATUS_ARCHIVED)
							    print $entity->getArchived("m/d/y h:i a");
						     elseif($entity->status == Entity::STATUS_DISPATCHED)
							    print $entity->getDispatched("m/d/y h:i a");
							 elseif($entity->status == Entity::STATUS_DELIVERED)
							    print $entity->getDelivered("m/d/y h:i a");
						    elseif($entity->status == Entity::STATUS_POSTED)
							    print date("m/d/y h:i a", strtotime($entity->posted));	
           					elseif($entity->status == Entity::STATUS_NOTSIGNED)
							    print date("m/d/y h:i a", strtotime($entity->not_signed));	
							elseif($entity->status == Entity::STATUS_ISSUES)
							    print date("m/d/y h:i a", strtotime($entity->issue_date));	
							elseif($entity->status == Entity::STATUS_ONHOLD)
							    print date("m/d/y h:i a", strtotime($entity->hold_date));
                            elseif($entity->status == Entity::STATUS_PICKEDUP)
							    print date("m/d/y h:i a", strtotime($entity->actual_pickup_date));								
							 else
                               	print $entity->getOrdered("m/d/y h:i a");	

 								
			      ?>
			<br />
            <?php if($entity->esigned==1){
				
				
				$files = $entity->getFiles($entity->id);
				 if ( isset($files) && count($files) ) { 
				               foreach ($files as $file) { 
									
									$pos = strpos($file['name_original'], "Signed");
									if ($pos === false) {}
									else{?>
										<!--li id="file-<?php //print $file['id']; ?>"-->
											<a <?=strtolower($file['type'])=='pdf'?"target=\"_blank\"":""?> href="<?=getLink("orders", "getdocs", "id", $file['id'])?>"><img src="<?= SITE_IN ?>images/icons/esign_small.png" /></a>
										<!--/li-->
                                        <?
									 }
									
									?>
										
								<?php } ?>
						<?php }?>
			  <?php }
			  elseif($entity->esigned==2){
				 
                  $files = $entity->getCommercialFiles($entity->id);
				  //print "--".$entity->getAccount()->id;
				  //print_r($files);
				 if ( isset($files) && count($files) ) { 
				               foreach ($files as $file) { 
									
									$pos = strpos($file['name_original'], "B2B");
									if ($pos === false) {}
									else{?>
											<a <?=strtolower($file['type'])=='pdf'?"target=\"_blank\"":""?> href="<?=getLink("orders", "getdocs", "id", $file['id'])?>"><img src="<?= SITE_IN ?>images/icons/b2b.png" /></a>
										<?
									 }
									
									?>
										
								<?php } ?>
						<?php }?>
               <?php }
			   
			   /*else{
				  if($shipper->shipper_type == "Residential")
				  { 
					if($entity->esigned_date!=""){
						$date = strtotime(date("Y-m-d H:i:s"));
					    $str = strtotime('23 hours ago', $date);
					    $dateEsigned = strtotime($entity->esigned_date);
						//print date("Y-m-d H:i:s")."--". $str."--".$entity->esigned_date."==".$dateEsigned;
					     if($str >=  $dateEsigned){
							 ?>
                             <img src="<?= SITE_IN ?>images/icons/esignsend.png" /><br />
                             <?php
						 }
						 
					}
					else{
					  ?>
                      <img src="<?= SITE_IN ?>images/icons/esignnotsend.png" /><br />
                      <?php
			        }
			   }
			   
				if($shipper->shipper_type == "Commercial")
				  {
					if($entity->bsigned_date!=""){
						$date = strtotime(date("Y-m-d H:i:s"));
					    $str = strtotime('23 hours ago', $date);
					    $dateEsigned = strtotime($entity->bsigned_date);
						
					     if($str >=  $dateEsigned){
							 ?>
                             <br /><img src="<?= SITE_IN ?>images/icons/bsignnotsend.png" />
                             <?php
						 }
						 
					}
					else{
					  ?>
                      <img src="<?= SITE_IN ?>images/icons/bsignnotsend.png" /><br />
                      <?php
			        }
				  }
			   }*/
				   ?>
			   <br>Assigned to:<br/> <strong><?= $assigned->contactname ?></strong><br />
			</td>
            <td bgcolor="<?= $bgcolor ?>">
                <?php if ($_GET['orders'] != 'all') { ?>
                    <?php $notes = $entity->getNotes(); 
					      $notesNew = $entity->getNewNotes();
					      $countNewNotes = count($notesNew[Note::TYPE_INTERNALNEW]);
					?>
                    <?= notesIcon($entity->id, count($notes[Note::TYPE_FROM]), Note::TYPE_FROM, $entity->status == Entity::STATUS_ARCHIVED) ?>
                    <?= notesIcon($entity->id, count($notes[Note::TYPE_TO]), Note::TYPE_TO, $entity->status == Entity::STATUS_ARCHIVED) ?>
                    <?php //print notesIcon($entity->id, count($notes[Note::TYPE_INTERNAL]), Note::TYPE_INTERNAL, $entity->status == Entity::STATUS_ARCHIVED) ?>
                    <?= notesIcon($entity->id, count($notes[Note::TYPE_INTERNAL]), Note::TYPE_INTERNAL, $entity->status == Entity::STATUS_ARCHIVED,$countNewNotes)
				    ?>
                <?php } else { ?>
                    <?= $entity->getAssigned()->companyname
                    ; ?>
                <?php } ?>
            </td>
            <td bgcolor="<?= $bgcolor ?>">
			    
                <?php $shipper = $entity->getShipper();?>
                <?= $shipper->fname ?> <?= $shipper->lname ?><br/>
				<?php if($shipper->company!=""){?>
				<b><?= $shipper->company?></b><br />
				<?php }?>
                <?= formatPhone($shipper->phone1) ?><br/>
                <a href="mailto:<?= $shipper->email ?>"><?= $shipper->email ?></a><br>
				<?php if($entity->referred_by != ""){?>
				  Referred By <b><?= $entity->referred_by ?></b><br>
				<?php }?>
            </td>
            <td bgcolor="<?= $bgcolor ?>">
			
                <?php $vehicles = $entity->getVehicles();?>
                <?php $source = $entity->getSource(); ?>
                <?php if (count($vehicles) == 0) : ?>
                <?php elseif (count($vehicles) == 1) : ?>
                    <?php $vehicle = $vehicles[0]; ?>
                    <?= $vehicle->year; ?> <?= $vehicle->make; ?> <?= $vehicle->model; ?>&nbsp;<?php if($vehicle->inop){?>(<span style="color:red;weight:bold;"><?= "Inop" ?></span>)<?php }?><br/>
                    <?= $vehicle->type; ?>&nbsp;<?= imageLink($vehicle->year . " " . $vehicle->make . " " . $vehicle->model . " " . $vehicle->type) ?>
                    <?php if($vehicle->vin !=""){?>
					<br><span style="color:black;weight:bold;">VIN: <?= $vehicle->vin ?></span>
					<?php }?>
					<br/>
                <?php else : ?>
                    <span class="like-link multi-vehicles">Multiple Vehicles<b><span style="color:#000000;">(<?php print count($vehicles);?>)</span></b></span>
                    <div class="vehicles-info">
					<table width="100%"   cellpadding="0" cellspacing="1">
                         <tr>
                             <td  style="padding:3px;"><b><p>Year</p></b></td>
                             <td  style="padding:3px;"><b><p><?= Make ?></p></b></td>
							 <td  style="padding:3px;"><b><p><?= Model ?></p></b></td>
                             <td  style="padding:3px;"><b><p><?= Type ?></p></b></td> 
							 <td  style="padding:3px;"><b><p><?= Vin# ?></p></b></td>
                             <td  style="padding:3px;"><b><p><?= Inop ?></p></b></td>
						  </tr>
                        <?php foreach ($vehicles as $key => $vehicle) : ?>
                            <tr>
                             <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->year ?></td>
                             <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->make ?></td>
							 <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->model ?></td> 
                             <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->type ?></td>
                             <td bgcolor="#ffffff" style="padding:3px;"> <?php  print $vehicle->vin ?></td>
                             <td bgcolor="#ffffff" style="padding-left:5px;"> <?php  print $vehicle->inop==0?"No":"Yes"; ?></td>
                           </tr>
                        <?php endforeach; ?>
						</table>
                    </div>
                    <br/>
                <?php endif; ?>
				
				<span style="color:black;weight:bold;">Ship Via:  <span style="color:red;weight:bold;"><?= $entity->getShipVia() ?></span><br/>
                <strong>Source: </strong><?= $source->company_name ?>
            </td>
            <?php $origin = $entity->getOrigin();?>
            <?php $destination = $entity->getDestination();?>
            <td bgcolor="<?= $bgcolor ?>">
                <span class="like-link"
                      onclick="window.open('<?= $origin->getLink() ?>', '_blank')"><?= $origin->getFormatted() ?></span> /<br/>
                <span class="like-link"
                      onclick="window.open('<?= $destination->getLink() ?>')"><?= $destination->getFormatted() ?></span><br/>
                <span class="like-link" onclick="mapIt(<?= $entity->id ?>);">Map it</span>
                <?php if (is_numeric($entity->distance) && ($entity->distance > 0)) : ?>
                    <br/><strong><?= number_format($entity->distance, 0, "", "") ?> mi
                        ($ <?= number_format(($entity->getCarrierPay(false) / $entity->distance), 2, ".", ",") ?>/mi)</strong>
                <?php endif; ?>
            </td>
			<?php
			             $Balance_Paid_By = "";
						   if(in_array($entity->balance_paid_by, array(2, 3 , 16 , 17)))   
							   $Balance_Paid_By = "<b>COD</b>";
							
							if(in_array($entity->balance_paid_by, array(8, 9 , 18 , 19)))   
							   $Balance_Paid_By = "COP";
							
							if(in_array($entity->balance_paid_by, array(12, 13 , 20 , 21)))   
							   $Balance_Paid_By = "Billing";
							 
							 if(in_array($entity->balance_paid_by, array(14, 15 , 22 , 23)))   
							   $Balance_Paid_By = "Billing";
			?>
            <!--td valign="top" align="center">
                <?php /*if ($avail_title == Entity::TITLE_PICKUP_DELIVERY){ ?>
                    <?php echo $entity->getLoadDateWithAbbr("m/d/y");?><br /><center>/</center>
                    <?php echo $entity->getDeliveryDateWithAbbr("m/d/y");?>
                    <?php }else{?>
                        Posting date<br /><?php if($entity->posted!="")echo date("m/d/y", strtotime($entity->posted));else print "N/A";?> <br /><center>/</center>
                        1st Avail<br /><?php echo $entity->getFirstAvail("m/d/y");?>
                <?php } ?>
				<br />Paid By <strong><font color="red"> <?php print $Balance_Paid_By; */?></font></strong>
            </td-->
            
                <?php if ($avail_title == Entity::TITLE_PICKUP_DELIVERY){ ?>
                <td valign="top" align="center" bgcolor="<?= $bgcolor ?>">
                    <?php echo $entity->getLoadDateWithAbbr("m/d/y");?>
                </td>
                <td valign="top" align="center" bgcolor="<?= $bgcolor ?>">    
                    <?php echo $entity->getDeliveryDateWithAbbr("m/d/y");?>
                </td>    
                    <?php }else{?>
                 <td valign="top" align="center" bgcolor="<?= $bgcolor ?>">
                        <?php echo $entity->getFirstAvail("m/d/y");?>
                 </td>       
                <?php } ?>
            <?php if ($avail_title != Entity::TITLE_PICKUP_DELIVERY){?>
            <td valign="top" align="center" bgcolor="<?= $bgcolor ?>">
            <?php echo $entity->getPostDate("m/d/y");?>
            </td>
            <?php } ?>
            <td bgcolor="<?= $bgcolor ?>">
            <strong>Payment Method:</strong>  <font color="red"><?php print $entity->getPaymentOption($entity->customer_balance_paid_by);?></font>
            <br /><strong>Carrier Paid By:</strong>  <font color="red"><?php print $Balance_Paid_By;?></font>
            <?php if($_SESSION['member']['access_payments']==1){?>
             <br /><br /><a href="javascript:void(0);" onclick="process_payment(<?php print $entity->id;?>);">Process Payment</a>    
             <br /><!--a href="javascript:void(0);" onclick="refund_payment(<?php print $entity->id;?>);">Refund</a-->      
                <?php }?>   
            </td>
			 <?php 
						   
						    if ($entity->type == Entity::TYPE_ORDER){
								
						      // $isColor = $entity->isPaidOffColor();
							   
							   $Dcolor = "black";
							   $Ccolor = "black";
							   $Tcolor = "black";
							   
							   if($isColor['carrier']==1)
								$Ccolor = "green";
							   elseif($isColor['carrier']==2)	
							    $Ccolor = "red";
							   
							   if($isColor['deposit']==1)
								$Dcolor = "green";	
							   elseif($isColor['deposit']==2)	
							    $Dcolor = "red";
								
							   if($isColor['total']==1)
								$Tcolor = "green";	
							   elseif($isColor['total']==2)	
							    $Tcolor = "red";	
							   
							 }	
				?>
            <td class="grid-body-right"  bgcolor="<?= $bgcolor ?>">
                
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<table cellspacing="0" cellpadding="0" width="100%" class="control-bar">
    <tr>
        <td width="100%">&nbsp;</td>
		      
        <?php if ($this->status != Entity::STATUS_ARCHIVED) { ?>
            
            <td valign="top">
			<?php print functionButton('Reassign Order', 'reassignOrdersDialog()'); ?>
			<?php  //print functionButton('Reassign Order', 'reassignOrders(\'bottom\')'); ?></td>
        <? } ?>
        <?php if (in_array($_GET['orders'], array("", "posted")) && $_SESSION['member']['access_dispatch']) { ?>
            <td  valign="top"><?= functionButton('Dispatch', 'dispatch()') ?></td>
        <?php } ?>

        <td valign="top">
            <?php if ($this->status == Entity::STATUS_ACTIVE) : ?>
                <?= functionButton('Place On Hold', 'placeOnHoldOrders()') ?>
            <?php elseif ($this->status == Entity::STATUS_ONHOLD) : ?>
                <?= functionButton('Restore', 'restoreOrders()') ?>
            <?php endif; ?>
        </td>
        <?php if ($_GET['orders'] != "archived") { ?>
            <td valign="top"><?= functionButton('Cancel Order', 'cancelOrders()') ?></td>
        <?php } ?>
        <?php if ($_GET['orders'] == "") { ?>
            <td valign="top"><?= functionButton('Post to FB', 'postToFB()') ?></td>
        <?php } elseif ($_GET['orders'] == "dispatched") { ?>
            <td valign="top"><?php print  functionButtonDate(Entity::STATUS_PICKEDUP,'Picked Up Date', 'setPickedUpStatusAndDate',false,'pickup_button1','yy-mm-dd');?></td>
        <?php } elseif ($_GET['orders'] == "pickedup") { ?>
            <td valign="top"><?php print  functionButtonDate(Entity::STATUS_DELIVERED,'Delivered Date', 'setPickedUpStatusAndDate',false,'delivered_button1','yy-mm-dd');?></td>
        <?php } elseif ($_GET['orders'] == 'posted') { ?>
            <td valign="top"><?= functionButton('Unpost', 'unpostFromFB()') ?></td>
        <?php } ?>
    </tr>
</table>
@pager@
