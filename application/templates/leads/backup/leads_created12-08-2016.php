<style>
.cd-secondary-nav {
  position: static;
 
}
.cd-secondary-nav .is-visible {
  visibility: visible;
  transform: scale(1);
  transition: transform 0.3s, visibility 0s 0s;
}
 .cd-secondary-nav.is-fixed {
    z-index: 9999;
    position: fixed;
    left: auto;
    top: 0;
    width: 1200px;
	background-color:#f4f4f4;   
  }
</style>
<!--div id="acc_search_dialog">
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
</div-->
<div id="reassignCompanyDiv">
    <table cellspacing="2" cellpadding="0" border="0">
        <tr>
            <td valign="top">
                <select class="form-box-combobox" id="company_members">
                   <option value=""><?php print "Select One"; ?></option>
                    <?php foreach($this->company_members as $member) : ?>
                        <?php if($member->status == "Active"){
                            $activemember .="<option value= '".$member->id."'>" .$member->contactname ."</option>";
			               }
			            /* else {
                               $inactivemember .="<option value= '".$member->id."'>" .$member->contactname ."</option>";
			              }*/
						?>
                    <?php endforeach; ?>
                    
                    
						<optgroup label="Active User">
						<?php echo $activemember; ?>
						</optgroup>
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
		
		if ($(".entity-checkbox:checked").size() == 0) {

             alert("You have no selected items.");

			return false;        

        }
var ajData = [];
		/*
         $(".entity-checkbox").each(function(){
            if ($("#lead_tariff_"+$(this).val()).val() > 0) {
                ajData.push('{"entity_id":"'+$(this).val()+'","tariff":"'+$('#lead_tariff_'+$(this).val()).val()+'","deposit":"'+$('#lead_deposit_'+$(this).val()).val()+'"}');
            }
			
        });
		*/
		$(".entity-checkbox:checked").each(function(){
           // if ($("#lead_tariff_"+$(this).val()).val() > 0) {
                ajData.push('{"entity_id":"'+$(this).val()+'"}');
            //}
			
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
		
	 var secondaryNav = $('.cd-secondary-nav'),
	 secondaryNavTopPosition = secondaryNav.offset().top;
 
$(window).on('scroll', function(){
	
	if($(window).scrollTop() > secondaryNavTopPosition ) {
		secondaryNav.addClass('is-fixed');	
		
		
	} else {
		secondaryNav.removeClass('is-fixed');
		
	}
});

        
	});
</script>
<?php //endif; ?>
<div style="display:none" id="notes">notes</div>
<br/>
<div id="nimble_dialog_button" >

<table cellspacing="0" cellpadding="0" border="0" class="cd-secondary-nav" width="100%">
<tr><td> 
@pager@
</td></tr>
<tr><td>
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
        <?php //if ($this->status == Entity::STATUS_CASSIGNED){?>
              <td><?= functionButton('Convert to Quotes', 'saveQuotes(0)') ?></td>
       <?php //}?>
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
		<!--td><?= functionButton('Cancel', 'cancel()') ?></td-->
	</tr>
</table>
<?php }?>
</td></tr>

<tr><td>
<table cellspacing="0" cellpadding="0" border="0" class="grid" width="100%">

    <tbody>

    <tr class="grid-head" >

        <td class="grid-head-left" width="10%">

            <?php if (isset($this->order)) : ?>
				<?=$this->order->getTitle("id", "ID")?>
				<?php else : ?>ID<?php endif; ?>

        </td>

        <td  width="10%">

		    <?php if($this->status == Entity::STATUS_ARCHIVED){?>
                 Received/Created
              <?php }else{?>
				<?php if (isset($this->order)) : ?>
				<?=$this->order->getTitle("assigned_date", "Created")?>
				<?php else : ?>Received<?php endif; ?>
              <?php }?>
        </td>
        <td  width="5%">Notes</td>
        <td width="20%">
                <?php if (isset($this->order)) : ?>
                    Shipper
					<?php //print $this->order->getTitle("shipper", "Shipper");?>
				<?php else : ?>Shipper<?php endif; ?>
        </td>
        <td   width="16%">Hours of Operations</td>
        <td width="16%">Shipment Types</td>
        <td width="10%">Units/month</td>
        <td   width="13%"  class="grid-head-right"><?php if (isset($this->order)) { ?>
          <?=$this->order->getTitle("last_activity_date", "Last Activity Date")?>
          <?php }?>
        </td>
        
      
    </tr>
    <tbody>
  </table>
 </td></tr>
</table>


<table cellspacing="0" cellpadding="0" border="0" class="grid" width="100%">
	<tbody>
		
		<?php if (count($this->entities) == 0): ?>
	   <tr class="grid-body">
		<td colspan="8" align="center" class="grid-body-left grid-body-right"><i>No records</i></td>
	   </tr>
       <?php endif; ?>
	<?php 
     $words = array("+", "-", " ","(",")");
	$wordsReplace   = array("", "", "", "", "");
	
	
    $searchData = array();
   foreach($this->entities as $i => $entity) :
    $searchData[] = $entity['entityid'];
	
	$urlDetail = "show";
	if($entity['status'] != Entity::STATUS_CACTIVE && 
	   $entity['status'] != Entity::STATUS_CASSIGNED && 
	   $entity['status'] != Entity::STATUS_CPRIORITY &&
	   $entity['status'] != Entity::STATUS_CONHOLD &&
	   $entity['status'] != Entity::STATUS_CDEAD 
	   ){ //
		   
		      $urlDetail = "showcreated";
		}
	?>
    <tr id="lead_tr_<?= $entity['entityid'] ?>" class="grid-body<?=($i == 0 ? " first-row" : "")?><?=($entity->duplicate)?' duplicate':''?>">
	<td align="center" class="grid-body-left" width="10%">
				<?php if (!$entity['readonly']) : ?>
				<input type="checkbox" value="<?= $entity['entityid'] ?>" class="entity-checkbox"/><br/>
				<?php endif; ?>
				<a href="<?= SITE_IN ?>application/leads/<?= $urlDetail ?>/id/<?= $entity['entityid'] ?>"><?= $entity['number'] ?></a><br/>
				<a href="<?= SITE_IN ?>application/leads/history/id/<?= $entity['entityid'] ?>">History</a>
                <?php if($this->status == Entity::STATUS_ARCHIVED){?>
						<?php if($entity['lead_type']==1){?>
                             <br/>Created
                        <?php }else{?>
                             <br/>Imported
                        <?php }?>
                 <?php }else{
					 if (isset($_GET['search_string'])){ 

					    print "<br /><b>Status</b><br>";//Entity::$status_name[$entity['status']];
						
						    if ($entity['status'] == Entity::STATUS_CACTIVE) 
                              print "Leads";
							 elseif($entity['status'] == Entity::STATUS_CONHOLD)
							    print "OnHold";
						     elseif($entity['status'] == Entity::STATUS_CARCHIVED)
							    print "Cancelled";	
						     elseif($entity['status'] == Entity::STATUS_CQUOTED)
							    print "Today's Quotes";
							 elseif($entity['status'] == Entity::STATUS_CFOLLOWUP)
							    print "Follow-ups";
							elseif($entity['status'] == Entity::STATUS_CEXPIRED)
							    print "Expired Quotes";
							elseif($entity['status'] == Entity::STATUS_CDUPLICATE)
							    print "Possible Duplicate";	
							elseif($entity['status'] == Entity::STATUS_CAPPOINMENT)
							    print "Appointments";	
						    elseif($entity['status'] == Entity::STATUS_CUNREADABLE)
							    print "Unreadable";
							elseif($entity['status'] == Entity::STATUS_CASSIGNED)
							    print "Assigned to";
							elseif($entity['status'] == Entity::STATUS_CDEAD)
							    print "Do Not Call";
							elseif($entity['status'] == Entity::STATUS_CPRIORITY)
							    print "Priority Leads";	
					 ?>
					 
					 <?php }}?>       
	</td>
	<td valign="top" style="white-space: nowrap;"  width="10%">
             <?php if($entity['status'] == Entity::STATUS_ARCHIVED){?>
						<?php if($entity['lead_type']==1){?>
                             <?= date("m/d/y h:i a", strtotime($entity['assigned_date']));?>
                        <?php }else{?>
                              <?= date("m/d/y h:i a", strtotime($entity['received']));?>  
                        <?php }?>
                 <?php }else{?> 
							       <?= date("m/d/y h:i a", strtotime($entity['assigned_date']));?>
                               
                                <?php if ($entity->duplicate) : ?>
                                <br/><span style="color: #F00;">Possible Duplicate</span>
                                <?php endif; ?>
                  <?php }?> 
				   <?php //$assigned = $entity->getAssigned();  ?>
                    <br>Assigned to:<br/> <strong><?= $entity['AssignedName'] ?></strong><br />  
                    
			</td>
	<td   width="5%">
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
					 $areaCodeStr = "".$areaCodeRows['StdTimeZone']."-".$areaCodeRows['statecode'].""; 
				}
			}
			
			if(trim($entity['shipperphone2'])!="")
			{
				$arrArea2 = array();
				$arrArea2 = explode(")",formatPhone($entity['shipperphone2']));
				   
				$code     = str_replace("(","",$arrArea2[0]);
				$areaCodeStr2="";  
				//print "WHERE  AreaCode='".$code."'";
				$areaCodeRows2 = $this->daffny->DB->selectRow("*", "app_area_code", "WHERE  AreaCode='".$code."'");
				if (!empty($areaCodeRows2)) {
					 $areaCodeStr2 = "".$areaCodeRows2['StdTimeZone']."-".$areaCodeRows2['statecode'].""; 
				}
			}
			?>
	        <td  width="20%">
            
			<?php //$shipper = $entity->getShipper();?>
				 <div class="shipper_name"><?= $entity['shipperfname'] ?> <?= $entity['shipperlname'] ?><br/></div>

                <?php if($entity['shippercompany']!=""){?><div class="shipper_company"><b><?= $entity['shippercompany']?></b><br /></div><?php }?>

               
               <?php if($entity['shipperphone1']!=""){ $phone1 = str_replace($words, $wordsReplace, $entity['shipperphone1']); ?><div class="shipper_number"><a href="javascript:void(0);" onclick="customPhoneSms('<?= $phone1; ?>');"><?= formatPhone($entity['shipperphone1']) ?> </a><?= $areaCodeStr;?><br/></div><?php }?>
                <?php if($entity['shipperphone2']!=""){  $phone2 = str_replace($words, $wordsReplace, $entity['shipperphone2']);  ?><div class="shipper_number"><a href="javascript:void(0);" onclick="customPhoneSms('<?= $phone1; ?>');"><?= formatPhone($entity['shipperphone2']) ?> </a><?= $areaCodeStr2;?><br/></div><?php }?>


                <?php if($entity['shipperemail']!=""){?><a href="mailto:<?= $entity['shipperemail'] ?>"><div class="shipper_email"><?= $entity['shipperemail'] ?><br/></div></a><?php }?>

                <div class="shipper_referred"><?php if($entity['referred_by'] != ""){?>

				  Source: <b><?= $entity['referred_by'] ?></b><br>

				</div>
				<?php }else{?>
                <strong>Source: </strong><?php print $entity['source_name']; ?>
                <?php }?>
			
			<?php 
				$shipment_type = "--";
			   if($entity['shippershipment_type']==1)
			      $shipment_type = "Full load";
			   elseif($entity['shippershipment_type']==2)
			      $shipment_type = "Singles";
			   elseif($entity['shippershipment_type']==3)
			      $shipment_type = "Both";	
             ?>   
              <td  width="16%"><?= $entity['shipper_hours'] ?></td>
             <td width="16%"><?= $shipment_type ?></td>
             <td width="10%"><?= $entity['shippershipment_type'] ?></td>
              
                <?php if (0) { ?>
						<td style="white-space: nowrap;"  class="grid-body-right"  width="7%">
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
				        </td>
                <?php }
				
				  elseif (    
						  $this->status == Entity::STATUS_CACTIVE || 
						  $this->status == Entity::STATUS_CASSIGNED || 
						  $this->status == Entity::STATUS_CPRIORITY || 
						  $this->status == Entity::STATUS_CONHOLD || 
						  $this->status == Entity::STATUS_CDEAD  || $_GET['etype'] == 4
						  
						  ) {?>
                  <!--td style="white-space: nowrap;"  class="grid-body-right">
                  <input type="hidden" id="lead_tariff_<?=$entity['entityid']?>" class="form-box-textfield decimal" value="<?=number_format($entity['total_tariff_stored'], 2, ".", "")?>" style="width: 50px;"/>
					<input type="hidden" id="lead_deposit_<?=$entity['entityid']?>" class="form-box-textfield decimal" value="<?=number_format($vehicles[0]->deposit, 2, ".", "")?>" style="width: 50px;"/> 
                    </td--> 
                    <td style="white-space: nowrap;"  class="grid-body-right"  width="13%">
				 <?= date("m/d/y h:i a", strtotime($entity['last_activity_date']))?></td>
				  <?php 
				  }else { ?>
                    <?php // $entity->getStatusUpdated("m/d/Y") ?>
                    <td style="white-space: nowrap;"  class="grid-body-right"  width="7%">
                    <table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
					<tr>
						<td width="10"><img src="<?= SITE_IN ?>images/icons/dollar.png" alt="Total Tariff" title="Total Tariff" width="16" height="16"/></td>
						<td><?= ("$ " . number_format((float)$entity['total_tariff_stored'], 2, ".", ",")) ?>
                        <input type="hidden" id="lead_tariff_<?=$entity['entityid']?>" class="form-box-textfield decimal" value="<?=number_format($entity['total_tariff_stored'], 2, ".", "")?>" style="width: 50px;"/>
                        </td>
					</tr>
					<tr>
						<td width="10"><img src="<?= SITE_IN ?>images/icons/truck.png" alt="Carrier Pay" title="Carrier Pay" width="16" height="16"/></td>
						<td><?= ("$ " . number_format((float)$entity['carrier_pay_stored'], 2, ".", ",")) ?><br/></td>
					</tr>
					<tr>
						<td width="10"><img src="<?= SITE_IN ?>images/icons/person.png" alt="Deposit" title="Deposit" width="16" height="16"/></td>
						<td><?= ("$ " . number_format((float)($entity['total_tariff_stored'] - $entity['carrier_pay_stored']), 2, ".", ",")) ?>
                        <input type="hidden" id="lead_deposit_<?=$entity['entityid']?>" class="form-box-textfield decimal" value="<?=number_format($vehicles[0]->deposit, 2, ".", "")?>" style="width: 50px;"/>
                        </td>
					</tr>
				</table>
                </td> 
                <?php } ?>
			 			 
			
	</tr>
<?php endforeach; ?>
<?php
	        $searchCount = count($searchData);
			if($searchCount>0){
			   $_SESSION['searchDataCLead'] = $searchData;
			   $_SESSION['searchCountCLead'] = $searchCount;
			   $_SESSION['searchShowCountCLead'] = 0;
			}
	?>
	</tbody>	
</table>		
<?php if ($this->status != Entity::STATUS_CARCHIVED) { ?>

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