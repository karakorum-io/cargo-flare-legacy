<div id="acc_search_dialog">
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="form-table">
		<tr>
			<td width="100%"><input type="text" name="app_search_text" id="acc_search_string" style="width:98%" class="form-box-textfield"/></td>
			<td><?=functionButton('Search', "accountSearch()")?></td>
		</tr>
		<tr>
			<td colspan="2">
				<ul id="acc_search_result"></ul>
			</td>
		</tr>
	</table>
</div>
<div id="reassignCompanyDiv">
    <table cellspacing="2" cellpadding="0" border="0">
        <tr>
            <td valign="top">
                <select class="form-box-combobox" id="company_members">
                   <option value=""><?php print "Select One"; ?></option>
                    <?php foreach($this->company_members as $member) : ?>
                        <option value="<?= $member->id ?>"><?= $member->contactname ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
 </table>
</div>
<div id="appointmentDiv">
    <table cellspacing="2" cellpadding="0" border="0">
        <tr>
            <td valign="top">@app_date@</td>
        </tr>
        <tr>
            <td valign="top">@app_time@</td>
        </tr>
        <tr>
            <td valign="top">@app_note@</td>
        </tr>
 </table>
</div>
<?php //if ($this->status == Entity::STATUS_ACTIVE || $_GET['leads']=="search") : ?>
<script type="text/javascript">
    function saveQuotes(email) {
		/*
        if ($(".entity-checkbox:checked").size() == 0) {
            alert("You should check at least one Lead to save Quotes");
            return;
        }
		*/
        var ajData = [];
        $(".entity-checkbox").each(function(){
            if ($("#lead_tariff_"+$(this).val()).val() > 0) {
                ajData.push('{"entity_id":"'+$(this).val()+'","tariff":"'+$('#lead_tariff_'+$(this).val()).val()+'","deposit":"'+$('#lead_deposit_'+$(this).val()).val()+'"}');
            }
        });
		if (ajData.length == 0) {
			alert("You have no quote data");
			return;
		}
		$("body").nimbleLoader('show');
        $.ajax({
            type: "POST",
            url: "<?=SITE_IN?>application/ajax/entities.php",
            dataType: 'json',
            data: {
                action: 'saveQuotesNew',
                email: email,
                data: "["+ajData.join(",")+"]"
            },
			success: function(res) {
				if (res.success) {
					document.location.href = document.location.href;
				} else {
					alert("Can't save Quote(s)");
				}
			},
            complete: function(response) {
				$("body").nimbleLoader('hide');
            }
        });
    }
	
function convertToOrder() {
	//alert('test');
	if ($(".entity-checkbox:checked").size() == 0) {

             alert("You have no selected items.");

			return false;        

        }

		if ($(".entity-checkbox:checked").size() > 1) {

             alert("Error: You may convert one lead at a time.");

			return false;        

        }
	/*
	   if ($(".entity-checkbox:checked").size() == 0) {
           alert("You have no selected items.");
        } else 
		*/
		{
			var entity_ids = [];
			$(".entity-checkbox:checked").each(function(){
				entity_ids.push($(this).val());
			});      
	
        $.ajax({
            type: "POST",
            url: "<?= SITE_IN ?>application/ajax/entities.php",
            dataType: "json",
            data: {
                action: "LeadtoOrderNew",
                entity_ids: entity_ids.join(',')
            },
            success: function (result) {
                if (result.success == true) {
                  // document.location.reload();
				  document.location.href = result.url;
				  
                } else {
                    alert("Can't convert Order. Try again later, please");
                }
            },
            error: function (result) {
                alert("Can't convert Order. Try again later, please");
            }
        });
    }
}	


function reassignOrdersDialog()
{
	  if ($(".entity-checkbox:checked").size() == 0) 
		{            
		   alert("Leads not selected");            
		     return;        
		} 
	  $("#reassignCompanyDiv").dialog("open");
}
	
	 

function reassignOrders(member) 
{		
        var member_id = 0;		
        member_id = member;		
		if ( member_id == 0 ) 
		{			
		  alert("You must select member to assign");			
		  return;		
		}        
		if ($(".entity-checkbox:checked").size() == 0) 
		{            
		   alert("Leads not selected");            
		     return;        
		}        
		//var entity_id = $(".entity-checkbox:checked").val();        
		var entity_ids = [];       
		//entity_ids.push(entity_id); 
		 $(".entity-checkbox:checked").each(function(){
            entity_ids.push($(this).val());
        });
		$("#reassignCompanyDiv").nimbleLoader('show');
		$.ajax({            
			   type: 'POST',            
			   url: '<?= SITE_IN ?>application/ajax/entities.php',            
			   dataType: "json",            
			   data: {                
			     action: 'reassign',                
				 assign_id: member_id,                
				 entity_ids: entity_ids.join(',')            
				 },            
				 success: function(response) 
				 {               
				    if (response.success) {                    
					    window.location.reload();               
						} else {                   
						  alert("Reassign failed. Try again later, please.");   
						  $("#reassignCompanyDiv").nimbleLoader('hide');
						  }            
					},           
					error: function(response) {                
					   alert("Reassign failed. Try again later, please.");  
					   $("#reassignCompanyDiv").nimbleLoader('hide');
					   } ,
					   complete: function (res) {

                        $("#reassignCompanyDiv").nimbleLoader('hide');

                    }
			});	
	}
	
	$("#reassignCompanyDiv").dialog({
	modal: true,
	width: 300,
	height: 140,
	title: "Reassign Lead",
	hide: 'fade',
	resizable: false,
	draggable: false,
	autoOpen: false,
	buttons: {
		"Submit": function () {
			var member_id = $("#company_members").val();	
			reassignOrders(member_id);
		},
		"Cancel": function () {
			$(this).dialog("close");
		}
	}
});
	
function setAppointment()
{
	  if ($(".entity-checkbox:checked").size() == 0) 
		{            
		   alert("Leads not selected");            
		     return;        
		} 
	  $("#appointmentDiv").dialog("open");
}
	
	 

function setAppointmentData(app_date,app_time,notes) 
{		
        	
		if ( app_date == '') 
		{			
		  alert("You select appointment date.");			
		  return;		
		}  
		if ( app_time == '') 
		{			
		  alert("You select appointment time.");			
		  return;		
		}  
		if ($(".entity-checkbox:checked").size() == 0) 
		{            
		   alert("Leads not selected");            
		     return;        
		}        
		//var entity_id = $(".entity-checkbox:checked").val();        
		var entity_ids = [];       
		//entity_ids.push(entity_id); 
		 $(".entity-checkbox:checked").each(function(){
            entity_ids.push($(this).val());
        });
	$("#appointmentDiv").nimbleLoader('show');
		$.ajax({            
			   type: 'POST',            
			   url: '<?= SITE_IN ?>application/ajax/entities.php',            
			   dataType: "json",            
			   data: {                
			     action: 'setappointment', 
				 app_date:app_date,
				 app_time:app_time,
				 notes:notes,
				 entity_ids: entity_ids.join(',')            
				 },            
				 success: function(response) 
				 {               
				    if (response.success) {                    
					    window.location.reload();               
						} else {                   
						  alert("Set appointment failed. Try again later, please.");   
						  $("#appointmentDiv").nimbleLoader('hide');
						  }            
					},           
					error: function(response) {                
					   alert("Set appointment. Try again later, please.");  
					   $("#appointmentDiv").nimbleLoader('hide');
					   } ,
					   complete: function (res) {

                        $("#appointmentDiv").nimbleLoader('hide');

                    }
			});	
	}
	
$("#appointmentDiv").dialog({
	modal: true,
	width: 400,
	height: 240,
	title: "Set Appointment",
	hide: 'fade',
	resizable: false,
	draggable: false,
	autoOpen: false,
	buttons: {
		"Submit": function () {
			var app_date = $("#app_date").val();	
			var app_time = $("#app_time").val();	
			var notes    = $("#app_note").val();	
			setAppointmentData(app_date,app_time,notes)
		},
		"Cancel": function () {
			$(this).dialog("close");
		}
	}
});

$(document).ready(function(){
        //$("#avail_pickup_date").datepicker({dateFormat: 'mm/dd/yy'});
		$("#app_date").datepicker({
			dateFormat: "yy-mm-dd",
            minDate: '+0'
			//setDate: "2012-10-09",
			 
		});

        
	});

</script>
<?php //endif; ?>
<div style="display:none" id="notes">notes</div>
<br/>
<div id="nimble_dialog_button" >
@pager@
<?php if ($this->status != Entity::STATUS_ARCHIVED || $_GET['leads']=="search") { ?>
<table cellspacing="0" cellpadding="0" width="100%" class="control-bar">
	<tr>
		<td align="left">Check&nbsp;&nbsp;<span class="like-link" onclick="checkAllEntities()">All</span>&nbsp;&nbsp;|&nbsp;&nbsp;<span class="like-link" onclick="uncheckAllEntities()">None</span></td>
		<td width="100%">&nbsp;</td>
		<td><?php  //functionButton('Reassign Leads', 'reassign(\'top\')') ?>
             <?= functionButton('Reassign Leads', 'reassignOrdersDialog()') ?>
        </td>
		<!--td>
			<?php /*if ($this->status == Entity::STATUS_ACTIVE) : ?>
			<?= functionButton('Place On Hold', 'placeOnHold()') ?>
			<?php elseif ($this->status == Entity::STATUS_ONHOLD) : ?>
			<?= functionButton('Restore', 'restore()') ?>
			<?php endif;*/ ?>
		</td-->
       <?php if ($this->status == Entity::STATUS_LFOLLOWUP){?>
              <td><?= functionButton('Set Appointment', 'setAppointment()') ?></td>
       <?php }?>
        <td><?= functionButton('Convert to Quote(s)', 'saveQuotes(0)') ?></td>
        <td>
		
               <?= functionButton('Convert to Order', 'convertToOrder()') ?>
             
         </td>
         <?php if($_GET['leads'] == 'onhold'){?>
         <td><?= functionButton('Remove Hold', 'changeStatusLeads('.Entity::STATUS_ACTIVE.')') ?></td>
         <?php }else{?>
           <td><?= functionButton('Hold', 'placeOnHold()') ?></td>
         <?php }?>
         <td><?= functionButton('Cancel', 'cancel()') ?></td>
        
	</tr>
</table>
<?php }else{ ?>
<table cellspacing="0" cellpadding="0" width="100%" class="control-bar">
	<tr>
		
		<td width="100%">&nbsp;</td>
   <td valign="top"><?= functionButton('Uncancel', 'changeStatusLeads(1)') ?></td>
</tr>
</table>

<?php }?>

<table cellspacing="0" cellpadding="0" border="0" class="grid" width="100%">
	<tbody>
		<tr class="grid-head">
			<td class="grid-head-left">
				<?php if (isset($this->order)) : ?>
				<?=$this->order->getTitle("id", "ID")?>
				<?php else : ?>ID<?php endif; ?>
			</td>
			<td>
              <?php if($this->status == Entity::STATUS_ARCHIVED){?>
                 Received/Created
              <?php }else{?>
				<?php if (isset($this->order)) : ?>
				<?=$this->order->getTitle("assigned_date", "Created")?>
				<?php else : ?>Received<?php endif; ?>
              <?php }?>
              
			</td>
			<td>Notes</td>
			<td>   
				<?php if (isset($this->order)) : ?>
                    Shipper
					<?php //print $this->order->getTitle("shipper", "Shipper");?>
				<?php else : ?>Shipper<?php endif; ?>
			</td>
			 
            <?php if($this->status == Entity::STATUS_ARCHIVED){?>
               <td>Vehicle/Hours of Operations</td>
               <td>
				Origin/Destination/Shipment Types
			</td>
            <?php }else{?>
              <td>Vehicle</td>
              <td>
				<?php if (isset($this->order)) : ?>
					<?=$this->order->getTitle("origin", "Origin")?>
				<?php else : ?>Origin<?php endif; ?>
				/
				<?php if (isset($this->order)) : ?>
					<?=$this->order->getTitle("destination", "Destination")?>
				<?php else : ?>Destination<?php endif; ?>
			</td>
            <?php }?>  
             <?php if($this->status == Entity::STATUS_ARCHIVED){?> 
                 <td>
                    Est. Ship/Units/month
                </td> 
             <?php }else{?>
                <td>
                    <?php if (isset($this->order)) : ?>
                        <?=$this->order->getTitle("avail", "Est. Ship")?>
                    <?php else : ?>Est. Ship<?php endif; ?>
                </td>
            <?php }?>  
			<td class="grid-head-right">
				<?php if ($this->status == Entity::STATUS_ACTIVE) : ?>
					Quote
				<?php elseif ($this->status == Entity::STATUS_ONHOLD) : ?>
					<?=$this->order->getTitle("status_update", "Holded")?>
				<?php elseif ($this->status == Entity::STATUS_ARCHIVED && $_GET['leads'] != "search") : ?>
					<?php if (isset($this->order)) : ?>
					<?=$this->order->getTitle("status_update", "Archived")?>
					<?php else : ?>Archived<?php endif; ?>
				<?php elseif ($this->status == Entity::STATUS_UNREADABLE || $_GET['leads'] == "search") : ?>
					Details
				<?php endif; ?>
			</td>
            
		</tr>
		<?php if (count($this->entities) == 0): ?>
	   <tr class="grid-body">
		<td colspan="8" align="center" class="grid-body-left grid-body-right"><i>No records</i></td>
	   </tr>
       <?php endif; ?>
	<?php 
    
    $searchData = array();
   foreach($this->entities as $i => $entity) :
    $searchData[] = $entity['entityid'];
	?>
    <tr id="lead_tr_<?= $entity['entityid'] ?>" class="grid-body<?=($i == 0 ? " first-row" : "")?><?=($entity->duplicate)?' duplicate':''?>">
	<td align="center" class="grid-body-left">
				<?php if (!$entity['readonly']) : ?>
				<input type="checkbox" value="<?= $entity['entityid'] ?>" class="entity-checkbox"/><br/>
				<?php endif; ?>
				
                <?php if($_GET['lead_search_type']==1 || $_GET['leads'] == '' || 
		  $_GET['leads'] == 'assigned' ||
		  $_GET['leads'] == 'quoted' ||
		  $_GET['leads'] == 'follow' ||
		  $_GET['leads'] == 'appointment' ||
		  $_GET['leads'] == 'expired' ||
		  $_GET['leads'] == 'duplicate' ||
		  $_GET['leads'] == 'onhold' ||
		  $_GET['leads'] == 'archived' || 
		  $_GET['leads'] == 'unreadable'){?>
                           <a href="<?= SITE_IN ?>application/leads/showimported/id/<?= $entity['entityid'] ?>"><?= $entity['number'] ?></a>
                     <?php }else { ?>
					      <a href="<?= SITE_IN ?>application/leads/show/id/<?= $entity['entityid'] ?>"><?= $entity['number'] ?></a>
                     <?php } ?>
                <br/>
				<a href="<?= SITE_IN ?>application/leads/history/id/<?= $entity['entityid'] ?>">History</a>
                <?php if($this->status == Entity::STATUS_ARCHIVED){?>
						<?php if($entity['lead_type']==1){?>
                             <br/>Created
                        <?php }else{?>
                             <br/>Imported
                        <?php }?>
                 <?php }?>       
	</td>
	<td valign="top" style="white-space: nowrap;">
             <?php if($entity['status'] == Entity::STATUS_ARCHIVED){?>
						<?php if($entity['lead_type']==1){?>
                             <?= date("m/d/y h:i a", strtotime($entity['assigned_date']));?>
                        <?php }else{?>
                              <?= date("m/d/y h:i a", strtotime($entity['received']));?>  
                        <?php }?>
                 <?php }else{?> 
							    <?= date("m/d/y h:i a", strtotime($entity['received']));?>
                          
                                <?php if ($entity->duplicate) : ?>
                                <br/><span style="color: #F00;">Possible Duplicate</span>
                                <?php endif; ?>
                  <?php }?> 
				   <?php //$assigned = $entity->getAssigned();  ?>
                    <br>Assigned to:<br/> <strong><?= $entity['AssignedName'] ?></strong><br />  
                    
			</td>
	<td>
			 <?php //print  notesIcon($entity['entityid'], $countInternalNotes, Note::TYPE_INTERNAL, $entity['status'] == Entity::STATUS_ARCHIVED,$countNewNotes)
                        
						$NotesCount1 = 0;
						if(!is_null($entity['NotesCount1']))
						   $NotesCount1 = $entity['NotesCount1'];
						
						$NotesCount2 = 0;
						if(!is_null($entity['NotesCount2']))
						   $NotesCount2 = $entity['NotesCount2'];
						   
						 $NotesCount3 = 0;
						if(!is_null($entity['NotesCount3']))
						   $NotesCount3 = $entity['NotesCount3'];  
						   
						 $countNewNotes =  $entity['NotesFlagCount3']; 
				    ?>
			<?php //$notes = $entity->getNotes();?>
				<?//= notesIcon($entity['entityid'], count($notes[Note::TYPE_FROM]), Note::TYPE_FROM, $entity['readonly']) ?>
				<?//= notesIcon($entity['entityid'], count($notes[Note::TYPE_TO]), Note::TYPE_TO, $entity['readonly']) ?>
				<?//= notesIcon($entity['entityid'], count($notes[Note::TYPE_INTERNAL]), Note::TYPE_INTERNAL, $entity['readonly']) ?>
				
				<?= notesIcon($entity['entityid'], $NotesCount1, Note::TYPE_FROM, $entity['status'] == Entity::STATUS_ARCHIVED) ?>
                <?= notesIcon($entity['entityid'], $NotesCount2, Note::TYPE_TO, $entity['status'] == Entity::STATUS_ARCHIVED) ?>
                <?= notesIcon($entity['entityid'], $NotesCount3, Note::TYPE_INTERNAL, $entity['status'] == Entity::STATUS_ARCHIVED,$countNewNotes) ?>
			</td>
	        <td>
                          <?php 
						  /*
								$sourceName = "not available";
								if($entity['source_id'] >0){
								    $source = new Leadsource($this->daffny->DB);
                                    $source->load($entity['source_id']);
									$sourceName = $source->company_name;
								}
								*/
								?>
			<?php //$shipper = $entity->getShipper();?>
				 <div class="shipper_name"><?= $entity['shipperfname'] ?> <?= $entity['shipperlname'] ?><br/></div>

                <?php if($entity['shippercompany']!=""){?><div class="shipper_company"><b><?= $entity['shippercompany']?></b><br /></div><?php }?>

                <?php if($entity['shipperphone1']!=""){?><div class="shipper_number"><?= formatPhone($entity['shipperphone1']) ?><br/></div><?php }?>
                <?php if($entity['shipperphone2']!=""){?><div class="shipper_number"><?= formatPhone($entity['shipperphone2']) ?><br/></div><?php }?>

                <?php if($entity['shipperemail']!=""){?><a href="mailto:<?= $entity['shipperemail'] ?>"><div class="shipper_email"><?= $entity['shipperemail'] ?><br/></div></a><?php }?>

                <?php /*if($entity['referred_by'] != ""){?>
<div class="shipper_referred">
				  Referred By <b><?= $entity['referred_by'] ?></b><br>
</div>
				<?php }*/?>
                <strong>Source: </strong><?php print $entity['source_name']; ?>
			</td>
		
			<td>
                             
               <?php if($this->status == Entity::STATUS_ARCHIVED){?>
                            <?php if($entity['lead_type']==1){?>
                                  <?= $entity['shipper_hours'] ?>
                                  
                            <?php }else{?>    
                                <?php //$vehicles = $entity->getVehicles();?>
                                
                                <?php if (count($entity['TotalVehicle']) == 0) : ?>
									<?php elseif (count($entity['TotalVehicle']) == 1) : ?>
                                        <?php //$vehicle = $vehicles[0]; ?>
                                        <?= $entity['Vehiclemake']; ?> <?= $entity['Vehiclemodel']; ?><br/>
                                         <?= $entity['Vehicleyear']; ?> <?= $entity['Vehicletype']; ?>&nbsp;<?= imageLink($vehicle['Vehicleyear'] . " " . $entity['Vehiclemake'] . " " . $entity['Vehiclemodel'] . " " . $entity['type']) ?>
                                    <?php else : ?>
                                        <span class="like-link multi-vehicles-new" onclick="getVehicles('<?php print $entity['entityid'];?>');">Multiple Vehicles<b><span style="color:#000000;">(<?php print $entity['TotalVehicle'];?>)</span></b></span>

                                           <div class="vehicles-info" id="vehicles-info-<?php print $entity['entityid'];?>">

                                         </div>
                                        <br/>
                                    <?php endif; ?>
                                       
										<span style="color:red;weight:bold;"><?php print ($entity['ship_via'] != 0) ? $ship_via_string[$entity['ship_via']] : ""; ?></span><br/>
                                         
                            <?php }?>
               <?php }else{?>               
			              <?php //$vehicles = $entity->getVehicles();?>
                                <?php //$source = $entity->getSource();?>
                                <?php if (count($entity['TotalVehicle']) == 0) : ?>
									<?php elseif (count($entity['TotalVehicle']) == 1) : ?>
                                        <?php //$vehicle = $vehicles[0]; ?>
                                        <?= $entity['Vehiclemake']; ?> <?= $entity['Vehiclemodel']; ?><br/>
                                         <?= $entity['Vehicleyear']; ?> <?= $entity['Vehicletype']; ?>&nbsp;<?= imageLink($vehicle['Vehicleyear'] . " " . $entity['Vehiclemake'] . " " . $entity['Vehiclemodel'] . " " . $entity['type']) ?>
                                    <?php else : ?>
                                        <span class="like-link multi-vehicles-new" onclick="getVehicles('<?php print $entity['entityid'];?>');">Multiple Vehicles<b><span style="color:#000000;">(<?php print $entity['TotalVehicle'];?>)</span></b></span>

                                           <div class="vehicles-info" id="vehicles-info-<?php print $entity['entityid'];?>">

                                         </div>

                                          <br/>                                                                      
                                        <br/>
                                    <?php endif; ?>
                                       
										<span style="color:red;weight:bold;"><?php print ($entity['ship_via'] != 0) ? $ship_via_string[$entity['ship_via']] : ""; ?></span><br/>
                                       
               <?php }?>  
			
			
			
			</td>
            <?php //if($this->status == Entity::STATUS_ARCHIVED)
			{?>
						
                             <?php //$origin = $entity->getOrigin();?>
								<?php //$destination = $entity->getDestination();?>
						<?php		$o_link = "http://maps.google.com/maps?q=" . urlencode($entity['Origincity'] . ",+" . $entity['Originstate']);

			$o_formatted = trim($entity['Origincity'].', '.$entity['Originstate'].' '.$entity['Originzip'], ", ");

			

			$d_link = "http://maps.google.com/maps?q=" . urlencode($entity['Destinationcity'] . ",+" . $entity['Destinationstate']);

			$d_formatted = trim($entity['Destinationcity'].', '.$entity['Destinationstate'].' '.$entity['Destinationzip'], ", ");

			?>

                                <td>
                                
                                <span class="like-link"

                      onclick="window.open('<?= $o_link ?>', '_blank')"><?= $o_formatted ?></span> /<br/>

                <span class="like-link"

                      onclick="window.open('<?= $d_link ?>')"><?= $d_formatted ?></span><br/>

                                    <?php if (is_numeric($entity['distance']) && ($entity['distance'] > 0)) { ?>

                              <?= number_format($entity['distance'], 0, "", "") ?> mi

                              <?php $cost = $entity['carrier_pay_stored'] + $entity['pickup_terminal_fee'] + $entity['dropoff_terminal_fee'];                         

             ?>

                        ($ <?= number_format(($cost / $entity['distance']), 2, ".", ",") ?>/mi)

                <?php } ?>

                <span class="like-link" onclick="mapIt(<?= $entity['entityid'] ?>);">Map it</span>
                                </td>
                        
                 <?php }?>  
                 
                
			
			<td align="center">
			  <?= date("m/d/y", strtotime($entity['est_ship_date'])) ?>
			</td>
			<td style="white-space: nowrap;"  class="grid-body-right">
                <?php if ($this->status == Entity::STATUS_ACTIVE) : ?>
					<?php if (count($entity['TotalVehicle']) == 1) : ?>
						<?php if (!$entity['readonly']) : ?>
                        <table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
                            <tr>
                                <td width="10"><img src="<?=SITE_IN?>images/icons/dollar.png" alt="Tariff" title="Tariff" width="16" height="16"></td>
                                <td style="white-space: nowrap;">$ <input type="text" id="lead_tariff_<?=$entity['entityid']?>" class="form-box-textfield decimal" value="<?=number_format($vehicles[0]->tariff, 2, ".", "")?>" style="width: 50px;"/>&nbsp;<span class="small">tariff</span></td>
                            </tr>
                            <tr>
                                <td width="10"><img src="<?=SITE_IN?>images/icons/person.png" alt="Deposit" title="Deposit" width="16" height="16"></td>
                                <td style="white-space: nowrap;">$ <input type="text" id="lead_deposit_<?=$entity['entityid']?>" class="form-box-textfield decimal" value="<?=number_format($vehicles[0]->deposit, 2, ".", "")?>" style="width: 50px;"/>&nbsp;<span class="small">deposit</span></td>
                            </tr>
						</table>
						<?php endif; ?>
					<?php else : ?>
					  <?php if($_GET['lead_search_type']==1){?>
                           <?=simpleButton('Details', SITE_IN.'application/leads/showimported/id/'.$entity['entityid'])?>
                     <?php }else { ?>
					      <?=simpleButton('Details', SITE_IN.'application/leads/show/id/'.$entity['entityid'])?>
                     <?php } ?>
					<?php endif; ?>
				<?php elseif ($this->status == Entity::STATUS_UNREADABLE || $_GET['leads'] == "search") : ?>
                  <?php if($_GET['lead_search_type']==1){?>
                           <?=simpleButton('Details', SITE_IN.'application/leads/showimported/id/'.$entity['entityid'])?>
                     <?php }else { ?>
					      <?=simpleButton('Details', SITE_IN.'application/leads/show/id/'.$entity['entityid'])?>
                     <?php } ?>
                <?php else : ?>
                    <?php // $entity->getStatusUpdated("m/d/Y") ?>
                    <table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
					<tr>
						<td width="10"><img src="<?= SITE_IN ?>images/icons/dollar.png" alt="Total Tariff" title="Total Tariff" width="16" height="16"/></td>
						<td><?= ("$ " . number_format((float)$entity['total_tariff_stored'], 2, ".", ",")) ?></td>
					</tr>
					<tr>
						<td width="10"><img src="<?= SITE_IN ?>images/icons/truck.png" alt="Carrier Pay" title="Carrier Pay" width="16" height="16"/></td>
						<td><?= ("$ " . number_format((float)$entity['carrier_pay_stored'], 2, ".", ",")) ?><br/></td>
					</tr>
					<tr>
						<td width="10"><img src="<?= SITE_IN ?>images/icons/person.png" alt="Deposit" title="Deposit" width="16" height="16"/></td>
						<td><?= ("$ " . number_format((float)($entity['total_tariff_stored'] - $entity['carrier_pay_stored']), 2, ".", ",")) ?></td>
					</tr>
				</table>
                <?php endif; ?>
			</td>
            
	</tr>
<?php endforeach; ?>
<?php
	        $searchCount = count($searchData);
			if($searchCount>0){
			   $_SESSION['searchData'] = $searchData;
			   $_SESSION['searchCount'] = $searchCount;
			   $_SESSION['searchShowCount'] = 0;
			}
	?>
	</tbody>	
</table>		
<?php if ($this->status != Entity::STATUS_ARCHIVED) : ?>
<table cellspacing="0" cellpadding="0" width="100%" class="control-bar">
	<tr>
		<td>Check&nbsp;&nbsp;<span class="like-link" onclick="checkAllEntities()">All</span>&nbsp;&nbsp;|&nbsp;&nbsp;<span class="like-link" onclick="uncheckAllEntities()">None</span></td>
		
		<td width="100%">&nbsp;</td>
		<td><?php  //functionButton('Reassign Leads', 'reassign(\'top\')') ?>
             <?= functionButton('Reassign Leads', 'reassignOrdersDialog()') ?>
        </td>
		<!--td>
			<?php /*if ($this->status == Entity::STATUS_ACTIVE) : ?>
			<?= functionButton('Place On Hold', 'placeOnHold()') ?>
			<?php elseif ($this->status == Entity::STATUS_ONHOLD) : ?>
			<?= functionButton('Restore', 'restore()') ?>
			<?php endif;*/ ?>
		</td-->
        <td><?= functionButton('Convert to Quote(s)', 'saveQuotes(0)') ?></td>
        <!--td>
		
               <?= functionButton('Convert to Order', 'convertToOrder()') ?>
             
         </td-->
         
          <?php if($_GET['leads'] == 'onhold'){?>
         <td><?= functionButton('Remove Hold', 'changeStatusLeads('.Entity::STATUS_ACTIVE.')') ?></td>
         <?php }else{?>
           <td><?= functionButton('Hold', 'placeOnHold()') ?></td>
         <?php }?>
         <td><?= functionButton('Cancel', 'cancel()') ?></td>
	</tr>
</table>
<?php endif; ?>
@pager@

</div>