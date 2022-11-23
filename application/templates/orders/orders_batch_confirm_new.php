
<script language="javascript" type="text/javascript">



var notes = [];	notes[<?= Note::TYPE_TO ?>] = [];	
notes[<?= Note::TYPE_FROM ?>] = [];	
notes[<?= Note::TYPE_INTERNAL ?>] = [];	
var notesIntervalId = undefined;	
var add_entity_id;	
var add_notes_type;	
var add_busy = false;
</script>

<br />
<br />
    



<div style="display:none" id="notes">notes</div>
<br/>

<div id="notes_container"></div>
<div id="notes_add">	<p></p>	<textarea class="form-box-textarea" name="add_note_text" style="padding-left:3px;font-size:11px;line-height:14px;color:#555;"></textarea>	<div style="float:right;">		<table cellspacng="0" cellpadding="0" border="0">			<tr>	<td style="color:#00000;">Quick Notes&nbsp;</td><td><select name="quick_notes" id="quick_notes" onchange="addQuickNote();">
<option value="">--Select--</value>
<option value="Emailed: Prospect.">Emailed: Prospect.</value>
<option value="Emailed: Bad Email.">Emailed: Bad Email.</value>
<option value="Faxed: Prospect.">Faxed: Prospect.</value>
<option value="Faxed: Bad Fax.">Faxed: Bad Fax.</value>
<option value="Texted: Prospect.">Texted: Prospect.</value>
<option value="Texted: Bad Mobile.">Texted: Bad Mobile.</value>
<option value="Phoned: No Voicemail.">Phoned: No Voicemail.</value>
<option value="Phoned: Left Message.">Phoned: Left Message.</value>
<option value="Phoned: Spoke to Prospect.">Phoned: Spoke to Prospect.</value>
<option value="Phoned: Bad Number.">Phoned: Bad Number.</value>
<option value="Phoned: Not Intrested.">Phoned: Not Intrested.</value>
<option value="Phoned: Do Not Call.">Phoned: Do Not Call.</value>
</select></td>			<td><?= functionButton('Add Note', 'addNote()') ?></td>				<td><?= functionButton('Cancel', 'closeAddNotes()') ?></td>			</tr>		</table>	</div></div>

<?php
    $avail_title = Entity::TITLE_FIRST_AVAIL;
    if (isset($_GET['orders'])){
        if (in_array($_GET['orders'], array("notsigned","dispatched","pickedup","delivered","issues","archived"))){
            $avail_title = Entity::TITLE_PICKUP_DELIVERY;
        }
    }
	
	
			
?>
<br />
<h3>Batch Payments Confirmation</h3>
<br />
<table cellspacing="0" cellpadding="0" border="0" class="grid" width="100%">
    <tbody>
    <tr class="grid-head">
        <td class="grid-head-left">
            <?php if (isset($this->order)) : ?>
                <?= $this->order->getTitle("id", "ID") ?>
            <?php else : ?>ID<?php endif; ?>
        </td>
        <td>
            <?php if (isset($this->order)) : ?>
                <?= $this->order->getTitle("created", "Created") ?>
            <?php else : ?>Created<?php endif; ?>
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
        <td  width="17%">
	        <?php if (isset($this->order)) : ?>
		        <?= $this->order->getTitle("origin", "Origin") ?>
	        <?php else : ?>Origin<?php endif; ?>
	        /
	        <?php if (isset($this->order)) : ?>
		        <?= $this->order->getTitle("destination", "Destination") ?>
	        <?php else : ?>Destinations<?php endif; ?>
        </td>
        <td>
	        <?php if (isset($this->order)) : ?>
		        <?= $this->order->getTitle("avail", $avail_title) ?>
	        <?php else : ?><?= $avail_title; ?><?php endif; ?>
        </td>
        <td>
              <?php print "Pickup";  ?>
            </td>
            <td>
               <?php print "Delivery"; ?>
            </td>
        
        <td >
	        <?php if (isset($this->order)) : ?>
		        <?= $this->order->getTitle("tariff", "Tariff") ?>
	        <?php else : ?>tariff<?php endif; ?>
        </td>
        <td class="grid-head-right" width="20%">
	        Process Amount
        </td>
    </tr>
    <?php 
	/*print "<pre>";
	print_r($this->entities);
	print "</pre>";
	*/
	$i=0;
	
	if (count($this->entities) == 0): ?>
        <tr class="grid-body">
            <td colspan="9" align="center" class="grid-body-left grid-body-right"><i>No records</i></td>
        </tr>
    <?php endif; ?>
    <?php 
	 $showTotal = 0;
	 $issue_type = $_POST['issue_type'];
	 $TotalAmount = 0;
	 $EntityIDArray = array();
	 //print_r($this->entities);
	foreach ($this->entities as $i => $entity) : /* @var Entity $entity */
	
	   $showOrder = false;
	   
	   /*
	   if($_POST['batch_order_ids']!=""){
		   $showOrder = true;
	   }
	   elseif($_POST['shipper_company_id']!="" && $entity->isPaidOffCarrier())
	   {
	      $showOrder = true;
	   }
	   */
	   //print $_POST['shipper_company_id']."--".$entity->isPaidOffCarrier()."<br>";
	 // if($showOrder)
	  {
	      $i++;
		  $showTotal++;
		  $bgcolor = "#ffffff";
		  if($i%2==0)
		    $bgcolor = "#f4f4f4";
			
	       $paymentDone = $entity->getPayments();
		   $paymentDoneSize = count($paymentDone);
		   
		   
		   if ($ds = $entity->getDispatchSheet()) {
		        $dispatchSheet = $ds;
	        }
		 
	     $isColor = $entity->isPaidOffColor();
		 $isValue = $entity->isPaidOffValue();
		 
		 $EntityIDArray[] = $entity->id;
		
	?>
        <tr id="order_tr_<?= $entity->id ?>" class="grid-body<?= ($i == 0 ? " first-row" : "") ?>">
            <td align="center" class="grid-body-left" bgcolor="<?= $bgcolor ?>">
                <?php if ($_GET['orders'] != 'all') { ?>
                    
                    <a href="<?= SITE_IN ?>application/orders/show/id/<?= $entity->id ?>"><?= $entity->getNumber() ?></a>
                    <br/>
                    <a href="<?= SITE_IN ?>application/orders/history/id/<?= $entity->id ?>">History</a>
					
					
                     <?php if ($this->status == Entity::STATUS_ARCHIVED) : ?>
                     <br/> <br/>
                       <a href="<?= SITE_IN ?>application/orders/unarchived/id/<?= $entity->id ?>">UnArchive</a>
                      <?php endif; ?>
                      
					
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
					
                <?php } else { ?>
                    <?= $entity->getNumber()
                    ; ?>
                <?php } ?>
            </td>
            <td valign="top" style="white-space: nowrap;" bgcolor="<?= $bgcolor ?>">
			<?= $entity->getOrdered("m/d/y h:i a") ?>
            <br />
            <?php if($entity->esigned==1){
				$files = $entity->getFiles($entity->id);
				 if ( isset($files) && count($files) ) { 
				               foreach ($files as $file) { 
									
									$pos = strpos($file['name_original'], "Signed");
									if ($pos === false) {}
									else{?>
										<!--li id="file-<?php //print $file['id']; ?>"-->
											<a <?=strtolower($file['type'])=='pdf'?"target=\"_blank\"":""?> href="<?=getLink("order", "getdocs", "id", $file['id'])?>"><img src="<?= SITE_IN ?>images/icons/esign_small.png" /></a>
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
										<!--li id="file-<?php //print $file['id']; ?>"-->
											<a <?=strtolower($file['type'])=='pdf'?"target=\"_blank\"":""?> href="<?=getLink("order", "getdocs", "id", $file['id'])?>"><img src="<?= SITE_IN ?>images/icons/b2b.png" /></a>
										<!--/li-->
                                        <?
									 }
									
									?>
										
								<?php } ?>
						<?php }?>
               <?php }?>
               <br>Assigned to:<br/> <strong><?= $entity->getAssigned()->contactname?></strong><br />
            </td>
            <td bgcolor="<?= $bgcolor ?>">
                <?php 
				
				if ($_GET['orders'] != 'all') { ?>
                    <?php 
					      $notes = $entity->getNotes();
					      $notesNew = $entity->getNewNotes();
					      $countNewNotes = count($notesNew[Note::TYPE_INTERNALNEW]);
					?>
                    <?= notesIcon($entity->id, count($notes[Note::TYPE_FROM]), Note::TYPE_FROM, $entity->status == Entity::STATUS_ARCHIVED) ?>
                    <?= notesIcon($entity->id, count($notes[Note::TYPE_TO]), Note::TYPE_TO, $entity->status == Entity::STATUS_ARCHIVED) ?>
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
               <b> <?= $shipper->company?></b><br />
                <?= formatPhone($shipper->phone1) ?><br/>
                <?php if($shipper->phone2!=""){?>
                <?= formatPhone($shipper->phone2) ?><br/>
                <?php }?>
                <a href="mailto:<?= $shipper->email ?>"><?= $shipper->email ?></a><br />
                Referred By <b><?= $entity->referred_by ?></b><br>
                
            </td>
            <td bgcolor="<?= $bgcolor ?>" width="25%">
                <?php $vehicles = $entity->getVehicles();?>
                <?php $source = $entity->getSource(); ?>
                <?php if (count($vehicles) == 0) : ?>
                <?php elseif (count($vehicles) == 1) : ?>
                    <?php $vehicle = $vehicles[0]; ?>
                    <?= $vehicle->make
                    ; ?> <?= $vehicle->model
                    ; ?><br/>
                    <?= $vehicle->year; ?> <?= $vehicle->type; ?>&nbsp;<?= imageLink($vehicle->year . " " . $vehicle->make . " " . $vehicle->model . " " . $vehicle->type) ?>
                    <br/>
                <?php else : ?>
                    <span class="like-link multi-vehicles">Multiple Vehicles<b><span style="color:#000000;">(<?php print count($vehicles);?>)</span></b></span>
                    <div class="vehicles-info">
                        <?php foreach ($vehicles as $key => $vehicle) : ?>
                            <div <?= ($key % 2) ? 'style="background-color: #161616;padding: 5px;"' : 'style="background-color: #000;padding: 5px;"' ?>>
                                <p><?= $vehicle->make ?> <?= $vehicle->model ?><?php if($vehicle->inop){?>(<span style="color:red;weight:bold;"><?= "Inop" ?></span>)<?php }?></p>
                                <?= $vehicle->year ?> <?= $vehicle->type ?>
                                &nbsp;<?=imageLink($vehicle->year . " " . $vehicle->make . " " . $vehicle->model . " " . $vehicle->type)?>
                                <br/>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <br/>
                <?php endif; ?>
                <span style="color:red;weight:bold;"><?= $entity->getShipVia() ?></span><br/>
                <strong>Source: </strong><?= $source->company_name ?>
            </td>
            <?php $origin = $entity->getOrigin();?>
            <?php $destination = $entity->getDestination();?>
            <td bgcolor="<?= $bgcolor ?>">
               <span class="like-link"
                      onclick="window.open('<?= $origin->getLink() ?>', '_blank')"><?= $origin->getFormatted() ?></span> /<br/>
                <span class="like-link"
                      onclick="window.open('<?= $destination->getLink() ?>')"><?= $destination->getFormatted() ?></span><br/>
                
                <?php if (is_numeric($entity->distance) && ($entity->distance > 0)) : ?>
                    <?= number_format($entity->distance, 0, "", "") ?> mi
                        ($ <?= number_format(($entity->getCarrierPay(false) / $entity->distance), 2, ".", ",") ?>/mi)
                <?php endif; ?>
                <span class="like-link" onclick="mapIt(<?= $entity->id ?>);">Map it</span>
            </td>
            <?php
			             $Balance_Paid_By = "";
						   if(in_array($entity->balance_paid_by, array(2, 3 , 16 , 17)))   
							   $Balance_Paid_By = "COD";
							
							if(in_array($entity->balance_paid_by, array(8, 9 , 18 , 19)))   
							   $Balance_Paid_By = "COP";
							
							if(in_array($entity->balance_paid_by, array(12, 13 , 20 , 21)))   
							   $Balance_Paid_By = "Billing";
							 
							 if(in_array($entity->balance_paid_by, array(14, 15 , 22 , 23)))   
							   $Balance_Paid_By = "Billing";
			?>
            <td valign="top" align="center" bgcolor="<?= $bgcolor ?>">
                <?php if ($avail_title == Entity::TITLE_PICKUP_DELIVERY){ ?>
                    <?php echo $entity->getLoadDateWithAbbr("m/d/y");?><br /><center>/</center>
                    <?php echo $entity->getDeliveryDateWithAbbr("m/d/y");?>
                    <?php }else{?>
                        <?php echo $entity->getFirstAvail("m/d/y");?>
                <?php } ?>
                
                       
            </td>
            
            <td valign="top" align="center" bgcolor="<?= $bgcolor ?>">
						<?php
                           if (strtotime($entity->load_date) == 0) $abbr = "N/A";
                           else
                           {
                             $abbr = $entity->load_date_type > 0 ? Entity::$date_type_string[(int)$entity->load_date_type] : "";
                             $abbr = $abbr . "<br />" . date("m/d/y", strtotime($entity->load_date));
                           }
                        ?>
                         <?php echo $abbr;?>
                </td>
                <td valign="top" align="center" bgcolor="<?= $bgcolor ?>"> 
					<?php
                       if (strtotime($entity->delivery_date) == 0) $abbr = "N/A";
                       else
                       {
                         $abbr = $entity->delivery_date_type > 0 ? Entity::$date_type_string[(int)$entity->delivery_date_type] : "";
                         $abbr = $abbr . "<br />" . date("m/d/y", strtotime($entity->delivery_date));
                       }
                    ?>   
                    <?php echo  $abbr;?>
                </td>    
            
            <?php 
						   
						    if ($entity->type == Entity::TYPE_ORDER){
								
						       //$isColor = $entity->isPaidOffColor();
							   
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
                            
            <td  bgcolor="<?= $bgcolor ?>">
                <table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
                    <tr>
                        <td width="10"><img src="<?= SITE_IN ?>images/icons/dollar.png" alt="Total Tariff"
                                            title="Total Tariff" width="16" height="16"/></td>
                        <td style="white-space: nowrap;"><span class='<?= $Tcolor;?>'><?= $entity->getTotalTariff() ?></span></td>
                    </tr>
                    <tr>
                        <td width="10">
                           
	                        <?php if ($ds = $entity->getDispatchSheet()) { ?>
		                        <span class="viewsample like-link" style="border-bottom: none">
			                        <img src="<?= SITE_IN ?>images/icons/truck.png" alt="Carrier Pay" title="Carrier Pay" width="16" height="16"/>
		                        </span>
		                        <div class="sample-info carrier-info-popup">
			                        <strong>Company Name: </strong><?= $ds->carrier_company_name ?><br/>
			                        <strong>Contact Name: </strong><?= $ds->carrier_contact_name ?><br/>
			                        <strong>Phone 1: </strong><?= $ds->carrier_phone_1 ?><br/>
			                        <strong>Phone 2: </strong><?= $ds->carrier_phone_2 ?><br/>
			                        <strong>Fax: </strong><?= $ds->carrier_fax ?><br/>
			                        <strong>Email: </strong><?= $ds->carrier_email ?><br/>
			                        <?php $carrier = $entity->getCarrier();?>
			                        <?php if ($carrier instanceof Account) { ?>
				                        <strong>Hours of Operation: </strong><?= $carrier->hours_of_operation ?><br/>
			                        <?php } ?>
			                        <strong>Driver Name: </strong><?= $ds->carrier_driver_name ?><br/>
			                        <strong>Driver Phone: </strong><?= $ds->carrier_driver_phone ?><br/>
		                        </div>
	                        <?php } else { ?>
	                        <img src="<?= SITE_IN ?>images/icons/truck.png" alt="Carrier Pay" title="Carrier Pay" width="16" height="16"/>
	                        <?php } ?>
                        </td>
                        <td style="white-space: nowrap;"><span class='<?= $Ccolor;?>'><?= $entity->getCarrierPay() ?></span><br/></td>
                    </tr>
                    <tr>
                        <td width="10"><img src="<?= SITE_IN ?>images/icons/person.png" alt="Deposit    "
                                            title="Deposit" width="16" height="16"/></td>
                        <td style="white-space: nowrap;"><span class='<?= $Dcolor;?>'><?= $entity->getTotalDeposit() ?></span></td>
                    </tr>
                    
                    
                </table>
            </td>
            <td class="grid-body-right" bgcolor="<?= $bgcolor ?>">
              $ <?= $entity->getTotalTariff(false) ?>
            </td>
        </tr>
       
       <?php
	     $TotalAmount = $TotalAmount + $entity->getTotalTariff(false);
	   ?>
       
       <?php }?>
    <?php endforeach; ?>
    
    <?php if($showTotal>0){?>
     <tr bgcolor="#FFFFFF">
         <td colspan="9">&nbsp;</td>
         <td align="center"><b>Total : </b></td>
         <td align="left">$ <b><?php print $TotalAmount;?></b></td>
        </tr> 
  <?php }?>
    </tbody>
</table>



