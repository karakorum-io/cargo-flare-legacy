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
                action: "LeadtoOrderCreated",
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
<?php 
if ($this->status == Entity::STATUS_CARCHIVED || $this->status == Entity::STATUS_CDEAD){?>
	
	<table cellspacing="0" cellpadding="0" width="100%" class="control-bar">
	<tr>
		<td align="left">Check&nbsp;&nbsp;<span class="like-link" onclick="checkAllEntities()">All</span>&nbsp;&nbsp;|&nbsp;&nbsp;<span class="like-link" onclick="uncheckAllEntities()">None</span></td>
		<td width="100%">&nbsp;</td>
		<td><?php  //functionButton('Reassign Leads', 'reassign(\'top\')') ?>
             <?= functionButton('Reassign Leads', 'reassignOrdersDialog()') ?>
        </td>
		   <td><?= functionButton('Hold', 'changeStatusLeads('.Entity::STATUS_CONHOLD.')') ?></td>
         <?php 
              if ($this->status == Entity::STATUS_CDEAD)
			  {?>  
            <td><?= functionButton('Remove Do Not Call', 'changeStatusLeads('.Entity::STATUS_CACTIVE.')') ?></td>
            <td><?= functionButton('Cancel', 'changeStatusLeads('.Entity::STATUS_CARCHIVED.')') ?></td>
            <?php }else{?>
           <td valign="top"><?= functionButton('Uncancel', 'changeStatusLeads('.Entity::STATUS_CACTIVE.')') ?></td>
           <?php }?>
         
	</tr>
</table>
<?php
}
else //if ($this->status != Entity::STATUS_CARCHIVED || $_GET['leads']=="search") 
{ ?>
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
       <?php if ($_GET['leads'] == 'cquoted'  || $_GET['leads'] == 'cfollow'){?>
              <td><?= functionButton('Set Appointment', 'setAppointment()') ?></td>
       <?php }?>
              
        <?php /*if ($this->status == Entity::STATUS_ASSIGNED){?>
              <td><?= functionButton('Convert to Quotes', 'saveQuotes(0)') ?></td>
       <?php }*/?>
        <?php // if ($this->status != Entity::STATUS_CACTIVE && $this->status != Entity::STATUS_CASSIGNED && $this->status != Entity::STATUS_CQUOTED){?>
               <td><?= functionButton('Convert to Order', 'convertToOrder()') ?></td>
       <?php //}?>
       
         <?php if($_GET['leads'] == 'cpriority'){?>
         <td><?= functionButton('Remove Priority', 'changeStatusLeads('.Entity::STATUS_CASSIGNED.')') ?></td>
         <?php }else{?>
         <td><?= functionButton('Make Priority', 'changeStatusLeads('.Entity::STATUS_CPRIORITY.')') ?></td>
         <?php }?>
         
         <?php if($_GET['leads'] == 'conhold'){?>
         <td><?= functionButton('Remove Hold', 'changeStatusLeads('.Entity::STATUS_CASSIGNED.')') ?></td>
         <?php }else{?>
         <td><?= functionButton('Hold', 'changeStatusLeads('.Entity::STATUS_CONHOLD.')') ?></td>
         <?php }?>
         
         <td><?= functionButton('Do Not Call', 'changeStatusLeads('.Entity::STATUS_CDEAD.')') ?></td>
         <td><?= functionButton('Cancel', 'changeStatusLeads('.Entity::STATUS_CARCHIVED.')') ?></td>
		<!--td><?= functionButton('Cancel', 'cancel()') ?></td-->
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
				<?php if (isset($this->order)) : ?>
				<?=$this->order->getTitle("quoted", "Quoted")?>
				<?php else : ?>Quoted<?php endif; ?>
			</td>
			<td>Notes</td>
			<td>
				<?php if (isset($this->order)) : ?>
					<?=$this->order->getTitle("shipperfname", "Shipper Information")?>
				<?php else : ?>Shipper<?php endif; ?>
			</td>
			<td>Vehicle Information</td>
			<td>
				<?php if (isset($this->order)) : ?>
					<?=$this->order->getTitle("Origincity", "Origin")?>
				<?php else : ?>Origin<?php endif; ?>
				/
				<?php if (isset($this->order)) : ?>
					<?=$this->order->getTitle("Destinationcity", "Destination")?>
				<?php else : ?>Destination<?php endif; ?>
			</td>
			<td>
				<?php if (isset($this->order)) : ?>
					<?=$this->order->getTitle("tariff", "Transport Cost")?>
				<?php else : ?>Tariff<?php endif; ?>
			</td>
			<td class="grid-head-right">
				<?php if (isset($this->order)) : ?>
					<?=$this->order->getTitle("est_ship_date", "Est. Ship")?>
				<?php else : ?>Est. Ship<?php endif; ?>
			</td>
		</tr>
<?php if (count($this->entities) == 0): ?>
	<tr class="grid-body">
		<td colspan="8" align="center" class="grid-body-left grid-body-right"><i>No records</i></td>
	</tr>
<?php endif; ?>
<?php 
  $i=0;
/*     print "<pre>";

	print_r($this->entities);

	print "</pre>"; */
  $date_type_string = array(
        1 => "Estimated",
        2=> "Exactly",
        3 => "Not Earlier Than",
        4 => "Not Later Than"
    );

    $ship_via_string = array(
        1 => "Open",
        2 => "Enclosed",
        3 => "Driveaway"
    );
	
$searchData = array();

 foreach($this->entities as $i => $entity) :
   flush();
   $i++;
   
          $searchData[] = $entity['entityid'];
   
		  $bgcolor = "#ffffff";
		  if($i%2==0)
		    $bgcolor = "#f4f4f4";
			
	    $number = "";
        if (trim($entity['prefix']) != "") {
            $number .= $entity['prefix'] . "-";
        }
        $number .= $entity['number'];
?>
		<tr id="quote_tr_<?= $entity['entityid'] ?>" class="grid-body<?=($i == 0 ? " first-row" : "")?>">
			<td align="center" class="grid-body-left"  bgcolor="<?= $bgcolor ?>">
				<?php if (!$entity['readonly']) : ?>
				<input type="checkbox" value="<?= $entity['entityid'] ?>" class="entity-checkbox"/><br/>
				<?php endif; ?>
				<a href="<?= SITE_IN ?>application/leads/showcreated/id/<?= $entity['entityid'] ?>"><?= $number ?></a><br/>
				<a href="<?= SITE_IN ?>application/quotes/history/id/<?= $entity['entityid'] ?>">History</a><br/><br/>
				<?php /*if ($entity['status'] == Entity::STATUS_ARCHIVED) : ?>
                <a href="<?= SITE_IN ?>application/quotes/unarchived/id/<?= $entity['id'] ?>">UnArchive</a>
               <?php endif;*/ ?>
			</td>
			<?php  //$assigned = $entity->getAssigned(); ?>
		<td valign="top" style="white-space: nowrap;"  bgcolor="<?= $bgcolor ?>"><?= date("m/d/y h:i a", strtotime($entity['quoted'])) ?>
			 <br><br>Assigned to:<br/> <strong><?= $entity['AssignedName'] ?></strong><br />
			</td>
            <td  bgcolor="<?= $bgcolor ?>">
			<?php 
			             $notes = new NoteManager($this->daffny->DB);
						 $notesData = $notes->getNotesArrData($entity['entityid']);
					      
						 $countNewNotes = count($notesData[Note::TYPE_INTERNALNEW]);
						 $countInternalNotes = count($notesData[Note::TYPE_INTERNAL]) + $countNewNotes;
			//$notes = $entity->getNotes();?>
				<?//= notesIcon($entity['entityid'], count($notesData[Note::TYPE_FROM]), Note::TYPE_FROM, $entity['status'] == Entity::STATUS_ARCHIVED) ?>
				<?//= notesIcon($entity['entityid'], count($notesData[Note::TYPE_TO]), Note::TYPE_TO, $entity['status'] == Entity::STATUS_ARCHIVED) ?>
				<?//= notesIcon($entity['entityid'], $countInternalNotes, Note::TYPE_INTERNAL, $entity['status'] == Entity::STATUS_ARCHIVED) 
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
                    
                    <?= notesIcon($entity['entityid'], $NotesCount1, Note::TYPE_FROM, $entity['status'] == Entity::STATUS_ARCHIVED) ?>

                    <?= notesIcon($entity['entityid'], $NotesCount2, Note::TYPE_TO, $entity['status'] == Entity::STATUS_ARCHIVED) ?>

                    <?= notesIcon($entity['entityid'], $NotesCount3, Note::TYPE_INTERNAL, $entity['status'] == Entity::STATUS_ARCHIVED,$countNewNotes) ?>
			</td>
       <?php
				
			if(trim($entity['shipperphone1'])!="")
			{
				$arrArea = array();
				$arrArea = explode(")",formatPhone($entity['shipperphone1']));
				 
				$code     = str_replace("(","",$arrArea[0]);
				$areaCodeStr="";  
				//print "WHERE  AreaCode='".$code."'";
				$areaCodeRows = $this->daffny->DB->selectRow("*", "app_area_code", "WHERE  AreaCode='".$code."'");
				if (!empty($areaCodeRows)) {
					 $areaCodeStr = "<b>".$areaCodeRows['StdTimeZone']."-".$areaCodeRows['statecode']."</b>"; 
				}
			}
			
			
			?>
            <td bgcolor="<?= $bgcolor ?>">
                
                <?= $entity['shipperfname'] ?> <?= $entity['shipperlname'] ?><br/>
                <?php if($entity['shippercompany']!=""){?>
				<b><?= $entity['shippercompany']?></b><br />
				<?php }?>
                <?= formatPhone($entity['shipperphone1']) ?> <?= $areaCodeStr;?><br/>
                <a href="mailto:<?= $entity['shipperemail'] ?>"><?= $entity['shipperemail'] ?></a><br />
                <?php if($entity['referred_by'] != ""){?>
				  Referred By <b><?= $entity['referred_by'] ?></b><br>
				<?php }?>
                
            </td>
			<td bgcolor="<?= $bgcolor ?>" width="25%">
            <?php
			
			$vehicleManager = new VehicleManager($this->daffny->DB);
		    $vehicles = $vehicleManager->getVehiclesArrData($entity['entityid'], $entity['type']);
			?>
                 <?php if (count($vehicles) == 0) { ?>
                <?php }elseif (count($vehicles) == 1) { ?>
                    <?php $vehicle = $vehicles[0]; ?>
                    <?= $vehicle['make']; ?> <?= $vehicle['model']; ?><br/>
                    <?= $vehicle['year']; ?> <?= $vehicle['type']; ?>&nbsp;<?= imageLink($vehicle['year'] . " " . $vehicle['make'] . " " . $vehicle['model'] . " " . $vehicle['type']) ?>
                    <br/>
                <?php }else { ?>
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
                             <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle['year'] ?></td>
                             <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle['make'] ?></td>
							 <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle['model'] ?></td> 
                             <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle['type'] ?></td>
                             <td bgcolor="#ffffff" style="padding:3px;"> <?php  print $vehicle['vin'] ?></td>
                             <td bgcolor="#ffffff" style="padding-left:5px;"> <?php  print $vehicle['inop']==0?"No":"Yes"; ?></td>
                           </tr>
                        <?php endforeach; ?>
						</table>
                    </div>
                    <br/>
                <?php  } ?>
                <br><span style="color:black;weight:bold;">Ship Via:  </span><span style="color:red;weight:bold;"><?php print ($entity['ship_via'] != 0) ? $ship_via_string[$entity['ship_via']] : ""; ?></span><br/>
                <strong>Source: </strong><?php //print $source->company_name; ?>
            </td>
            
			<?php
			$o_link = "http://maps.google.com/maps?q=" . urlencode($entity['Orgincity'] . ",+" . $entity['Originstate']);
			$o_formatted = trim($entity['Orgincity'].', '.$entity['Originstate'].' '.$entity['Originzip'], ", ");
			
			$d_link = "http://maps.google.com/maps?q=" . urlencode($entity['Destinationcity'] . ",+" . $entity['Destinationstate']);
			$d_formatted = trim($entity['Destinationcity'].', '.$entity['Destinationstate'].' '.$entity['Destinationzip'], ", ");
			?>
            <td bgcolor="<?= $bgcolor ?>">
               <span class="like-link"
                      onclick="window.open('<?= $o_link ?>', '_blank')"><?= $o_formatted ?></span> /<br/>
                <span class="like-link"
                      onclick="window.open('<?= $d_link ?>')"><?= $d_formatted ?></span><br/>
                
                <?php if (is_numeric($entity['distance']) && ($entity['distance'] > 0)) { ?>
                    <?= number_format($entity['distance'], 0, "", "") ?> mi
                    <?php $cost = $entity['carrier_pay'] + $entity['pickup_terminal_fee'] + $entity['dropoff_terminal_fee'];
                          
                    ?>
                        ($ <?= number_format(($cost / $entity['distance']), 2, ".", ",") ?>/mi)
                <?php } ?>
                <span class="like-link" onclick="mapIt(<?= $entity['entityid'] ?>);">Map it</span>
            </td>
            
			<td width="11%"  bgcolor="<?= $bgcolor ?>" >
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
			</td>
			<td valign="top" align="center" class="grid-body-right"  bgcolor="<?= $bgcolor ?>" ><? print date("m/d/y", strtotime($entity['est_ship_date'])); ?></td>
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
<?php if ($this->status != Entity::STATUS_CARCHIVED) { ?>
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
           <?php if ($_GET['leads'] == 'cquoted'  || $_GET['leads'] == 'cfollow'){?>
              <td><?= functionButton('Set Appointment', 'setAppointment()') ?></td>
       <?php }?>
        <?php /* if ($this->status == Entity::STATUS_ASSIGNED){?>
              <td><?= functionButton('Convert to Quotes', 'saveQuotes(0)') ?></td>
       <?php }*/?>
        <?php //if ($this->status != Entity::STATUS_CACTIVE && $this->status != Entity::STATUS_CASSIGNED && $this->status != Entity::STATUS_CQUOTED){?>
               <td><?= functionButton('Convert to Order', 'convertToOrder()') ?></td>
       <?php //}?>
       
        <?php if($_GET['leads'] == 'cpriority'){?>
         <td><?= functionButton('Remove Priority', 'changeStatusLeads('.Entity::STATUS_CASSIGNED.')') ?></td>
         <?php }else{?>
         <td><?= functionButton('Make Priority', 'changeStatusLeads('.Entity::STATUS_CPRIORITY.')') ?></td>
         <?php }?>
         
         <?php if($_GET['leads'] == 'conhold'){?>
         <td><?= functionButton('Remove Hold', 'changeStatusLeads('.Entity::STATUS_CASSIGNED.')') ?></td>
         <?php }else{?>
         <td><?= functionButton('Hold', 'changeStatusLeads('.Entity::STATUS_CONHOLD.')') ?></td>
         <?php }?>
         
         <td><?= functionButton('Do Not Call', 'changeStatusLeads('.Entity::STATUS_CDEAD.')') ?></td>
         <td><?= functionButton('Cancel', 'changeStatusLeads('.Entity::STATUS_CARCHIVED.')') ?></td>
        
		
	</tr>
</table>
<?php }else{?>
<table cellspacing="0" cellpadding="0" width="100%" class="control-bar">
	<tr>
		<td align="left">Check&nbsp;&nbsp;<span class="like-link" onclick="checkAllEntities()">All</span>&nbsp;&nbsp;|&nbsp;&nbsp;<span class="like-link" onclick="uncheckAllEntities()">None</span></td>
		<td width="100%">&nbsp;</td>
		<td><?php  //functionButton('Reassign Leads', 'reassign(\'top\')') ?>
             <?= functionButton('Reassign Leads', 'reassignOrdersDialog()') ?>
        </td>
		   <td><?= functionButton('Hold', 'changeStatusLeads('.Entity::STATUS_CONHOLD.')') ?></td>
           <td valign="top"><?= functionButton('Uncancel', 'changeStatusLeads('.Entity::STATUS_CACTIVE.')') ?></td>
         
	</tr>
</table>
<?php }?>
@pager@

</div>