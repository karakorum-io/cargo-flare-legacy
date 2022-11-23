<script type="text/javascript">
		var busy = false;
function updateInternalNotes(data) {
		var rows = "";
		for (i in data) {
			
			var email = data[i].email;
			var contactname = data[i].sender;
			
			if(data[i].system_admin == 1){
			     email = "admin@freightdragon.com";
				 contactname = "FreightDragon";
			   }
			if ((data[i].access_notes == 0 )   
				    || data[i].access_notes == 1
					|| data[i].access_notes == 2
					)
			{
			rows += '<tr class="grid-body"><td class="grid-body-left">'+data[i].created+'</td><td id="note_'+data[i].id+'_text">'+decodeURIComponent(data[i].text)+'</td><td>';
			rows += '<a href="mailto:'+email+'">'+contactname+'</a></td><td style="white-space: nowrap;" class="grid-body-right">';
			
			<?php //if (!$this->entity->readonly) : ?>
			
				if ((data[i].access_notes == 0 ) ||
					  (data[i].access_notes == 1 && (data[i].sender_id == data[i].memberId))
					  || data[i].access_notes == 2
					)
					{
						
						
					 if(data[i].system_admin == 0 && data[i].access_notes != 0)
					 {
				
			   rows += '<img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit" width="16" height="16" class="action-icon edit-note" onclick="editInternalNote('+data[i].id+')"/>';
			   
			   rows += '<img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete" width="16" height="16" class="action-icon delete-note" onclick="delInternalNote('+data[i].id+')"/>';
					 }
				  }
			}
			<?php /*else : ?>rows += '&nbsp;';<?php endif;*/?>
			rows += '</td></tr>';
		  }
		
		$("#internal_notes_table tbody").html(rows);
	}
	function addInternalNote() {
		if (busy) return;
		busy = true;
		var text = $.trim($("#internal_note").val());
		if (text == "") return;
		$("#internal_note").val("");
		$.ajax({
			type: "POST",
			url: "<?= SITE_IN ?>application/ajax/notes.php",
			dataType: "json",
			data: {
				action: 'add',
				text: encodeURIComponent(text),
				entity_id: <?= $this->entity->id ?>,
				notes_type: <?= Note::TYPE_INTERNAL ?>
			},
			success: function(result) {
				if (result.success == true) {
					updateInternalNotes(result.data);
				} else {
					$("#internal_note").val(text);
					alert("Can't save note. Try again later, please");
				}
				busy = false;
			},
			error: function(result) {
				$("#internal_note").val(text);
				alert("Can't save note. Try again later, please");
				busy = false;
			}
		});
	}
	function delInternalNote(id) {
		if (confirm("Are you sure whant to delete this note?")) {
			if (busy) return;
			busy = true;
			$.ajax({
				type: "POST",
				url: "<?= SITE_IN ?>application/ajax/notes.php",
				dataType: "json",
				data: {
					action: 'del',
					id: id,
					entity_id: <?= $this->entity->id ?>,
					notes_type: <?= Note::TYPE_INTERNAL ?>
				},
				success: function(result) {
					if (result.success == true) {
						updateInternalNotes(result.data);
					} else {
						alert("Can't delete note. Try again later, please");
					}
					busy = false;
				},
				error: function(result) {
					alert("Can't delete note. Try again later, please");
					busy = false;
				}
			});
		}
	}
	function editInternalNote(id) {
		var text = $.trim($("#note_"+id+"_text").text());
		$("#note_edit_form textarea").val(text);
		$("#note_edit_form").dialog({
			width: 400,
			modal: true,
			title: "Edit Internal Note",
			resizable: false,
			buttons: [{
				text: "Save",
				click: function() {
					if ($("#note_edit_form textarea").val() == text) {
						$(this).dialog("close");
					} else {
						if (busy) return;
						busy = true;
						text = encodeURIComponent($.trim($("#note_edit_form textarea").val()));
						$.ajax({
							type: "POST",
							url: "<?= SITE_IN ?>application/ajax/notes.php",
							dataType: "json",
							data: {
								action: 'update',
								id: id,
								text: text,
								entity_id: <?= $this->entity->id ?>,
								notes_type: <?= Note::TYPE_INTERNAL ?>
							},
							success: function(result) {
								if (result.success == true) {
									updateInternalNotes(result.data);
									$("#note_edit_form").dialog("close");
								} else {
									alert("Can't save note. Try again later, please");
								}
								busy = false;
							},
							error: function(result) {
								alert("Can't save note. Try again later, please");
								busy = false;
							}
						});
					}
				}
			},{
				text: "Cancel",
				click: function() {
					$(this).dialog("close");
					busy = false;
				}
			}]
		}).dialog("open");
	}

function addQuickNote() {
	var textOld = $("#internal_note").val();
	
	var str = textOld + " " + $("#quick_notes").val();
	$("#internal_note").val(str);
} 

</script><div id="note_edit_form" style="display:none;">	<textarea style="width: 95%;height:100px;" class="form-box-textarea" name="note_text"></textarea></div>

<?php 

 if(is_array($_SESSION['searchDataILead']) && $_SESSION['searchCountILead']>0){
	 //$_SESSION['searchShowCount'] = $_SESSION['searchShowCount'] + 1;
	  
	 $eid = $_GET['id'];
	 $indexSearchData = array_search($eid,$_SESSION['searchDataILead']);
   
	   $nextSearch = $indexSearchData+1;
	   $_SESSION['searchShowCountILead'] = $indexSearchData;
	   $prevSearch = $indexSearchData-1;
	   
	 $entityPrev = $_SESSION['searchDataILead'][$prevSearch];
	 $entityNext = $_SESSION['searchDataILead'][$nextSearch];
?>
<div style="float:right; width:170px;">
  <div style="float:left;width:50px;">
  <?php if($_SESSION['searchShowCountILead']==0 ){?>
       <img src="<?= SITE_IN ?>images/arrow-down-gray.png"   width="40" height="40"/>
     <?php }else{?>
       <a href="<?= SITE_IN ?>application/leads/showimported/id/<?= $entityPrev ?>"><img src="<?= SITE_IN ?>images/arrow-down.png"   width="40" height="40"/></a>
       
     <?php }?>
  </div>
  <div style="float:left;width:70px; text-align:center; padding-top:10px;">
    <h3><?php print $_SESSION['searchShowCountILead']+1;?> - <?php print $_SESSION['searchCountILead'];?></h3>
  </div>
  <div style="float:left;width:50px;">
  <?php if($_SESSION['searchShowCountILead'] == ($_SESSION['searchCountILead']-1)){?>
         <img src="<?= SITE_IN ?>images/arrow-up-gray.png"    width="40" height="40"/>
  <?php }else{?>
       <a href="<?= SITE_IN ?>application/leads/showimported/id/<?= $entityNext ?>"><img src="<?= SITE_IN ?>images/arrow-up.png"   width="40" height="40"/></a>
     <?php }?>
     
  </div>
</div>
<?php }  ?>  
<div style="padding-top:15px;">

<?php include('lead_menu_imported.php'); 
$estatus = 1;
$strL = "Quote";
		if($this->entity->status == Entity::STATUS_ACTIVE || 
		   $this->entity->status == Entity::STATUS_LDUPLICATE ||
		   $this->entity->status == Entity::STATUS_UNREADABLE ||
		   $this->entity->status == Entity::STATUS_ONHOLD ||
		   $this->entity->status == Entity::STATUS_ARCHIVED
		   
		   ){
		   $strL = "Lead";
		   $estatus = 0;
		}
?></div><br/><h3><?php print $strL;?> #<?= $this->entity->number ?> Detail</h3><div style="clear: both;"></div>
<h1>Current Status:&nbsp;<?php print "<span class='black'>".$strL."</span>";?></h1>
<table>
	<tr> 
	<td valign="top" width="75%">
<div class="order-info" style="width:97%; margin-bottom: 10px;"> 
<?php
$sourceName = "";

if($estatus ==0){
	if($this->entity->source_id >0){
		$source = new Leadsource($this->daffny->DB);
		$source->load($this->entity->source_id);
		$sourceName = $source->company_name;
	}
}
else
{
	if($this->entity->source_id >0){
		$source = new Leadsource($this->daffny->DB);
		$source->load($this->entity->source_id);
		$sourceName = $source->company_name;
	}
	elseif($this->entity->referred_id >0){
		$sourceName = $this->entity->referred_by;
	}
}
$assigned = $this->entity->getAssigned();	
$shipper = $this->entity->getShipper();	
$origin = $this->entity->getOrigin();	
$destination = $this->entity->getDestination();	
$vehicles = $this->entity->getVehicles();?>	
<?php	$source = $this->entity->getSource();	$assigned = $this->entity->getAssigned();	$shipper = $this->entity->getShipper();	$origin = $this->entity->getOrigin();	$destination = $this->entity->getDestination();	$vehicles = $this->entity->getVehicles();?>

<p class="block-title">Quote Information</p>	
<div>				
<div>
		<table cellspacing="0" cellpadding="0" border="0" width="100%">
			<tr>
				<td style="vertical-align:top;" valign="top" width="50%">
                   <table width="100%" cellpadding="1" cellpadding="1">
                   <tr><td width="15%"><strong>Name</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $shipper->fname ?> <?= $shipper->lname ?></td></tr>
                   <tr> <td style="line-height:15px;"><strong>Company</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td  style="line-height:15px;"><strong><?= $shipper->company; ?></strong></td></tr>
                   <tr> <td style="line-height:15px;"><strong>Email</strong></td><td width="4%" align="center"><b>:</b></td><td><a href="mailto:<?= $shipper->email ?>"><?= $shipper->email ?></a></td></tr>
                   <tr> <td style="line-height:15px;"><strong>Phone 1</strong></td><td width="4%" align="center"><b>:</b></td><td><?= formatPhone($shipper->phone1); ?></td></tr>
				   <tr> <td style="line-height:15px;"><strong>Phone 2</strong></td><td width="4%" align="center"><b>:</b></td><td><?= formatPhone($shipper->phone2); ?></td></tr>
                   <tr> <td style="line-height:15px;"><strong>Mobile</strong></td><td width="4%" align="center"><b>:</b></td><td><?= formatPhone($shipper->mobile); ?></td></tr>
                   <tr> <td style="line-height:15px;"><strong>Fax</strong></td><td width="4%" align="center"><b>:</b></td><td><?= formatPhone($shipper->fax); ?></td></tr>
                   </table>   
				</td>
				<td style="vertical-align:top;"> 
                   <table width="100%" cellpadding="1" cellpadding="1">
				   <!--<tr> <td style="line-height:15px;"><strong>Hours</strong></td><td width="4%" align="center"><b>:</b></td><td><?= $shipper->shipper_hours; ?></td></tr>
				   <tr> <td width="23%"><strong>Address</strong></td><td width="4%" align="center"><b>:</b></td><td><?= $shipper->address1; ?><strong>,</strong>&nbsp;&nbsp;<?= $shipper->address2; ?></td></tr>
                   <tr><td style="line-height:15px;"><strong>City</strong></td><td width="4%" align="center"><b>:</b></td><td><?= $shipper->city ?></td></tr>
				   <tr><td style="line-height:15px;"><strong>State</strong></td><td width="4%" align="center"><b>:</b></td><td><?= $shipper->state ?></td></tr>
				   <tr><td style="line-height:15px;"><strong>Zip</strong></td><td width="4%" align="center"><b>:</b></td><td><?= $shipper->zip ?></td></tr>-->
				   <tr> <td width="23%"><strong>1st Avail. Pickup</strong></td><td width="4%" align="center"><b>:</b></td><td><?= $this->entity->getShipDate("m/d/y") ?></td></tr>
                   <tr><td width="15%"><strong>Ship Via: </strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><span style="color:red;weight:bold;"><?= $this->entity->getShipVia() ?></span></td></tr>
                   
                    <?php if (is_numeric($this->entity->distance) && ($this->entity->distance > 0)) : ?>
                    <tr><td width="15%"  style="line-height:15px;"><strong>Mileage: </strong></td><td width="4%" align="center"><b>:</b></td><td><?= number_format($this->entity->distance, 0, "", "") ?> mi($ <?= number_format(($this->entity->getCarrierPay(false) / $this->entity->distance), 2, ".", ",") ?>/mi)&nbsp;&nbsp;(<span class='red' onclick="mapIt(<?= $this->entity->id ?>);">MAP IT</span>)</strong></td></tr>
					
                    <?php endif; ?>
                    <tr><td style="line-height:15px;"><strong>Assigned to</strong></td><td width="4%" align="center"><b>:</b></td><td><?= $assigned->contactname ?></td></tr>					
                     <tr><td style="line-height:15px;"><strong>Source</strong></td><td width="4%" align="center"><b>:</b></td><td><?= $sourceName ?><?php print $entity['source_name']; ?></td></tr>
                   </table> 					
				</td>
			</tr>
		</table>
	</div>	
        
        </div></div>
        
 <div style="clear:left;"></div>
<table width="100%" cellpadding="1" cellpadding="1" border="0">
<tr>
<td width="49%" valign="top" >
<div class="order-info"  style="width:95%; margin-bottom: 10px;">
	<p class="block-title">Pickup Information</p>
	<div>
		 <table width="100%" cellpadding="1" cellpadding="1" border="0" >
		   <tr> <td style="line-height:15px;"  width="10%"><strong>City</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><span class="like-link"onclick="window.open('<?= $origin->getLink() ?>')"><?= $origin->city ?></span></td></tr>
		    <tr> <td style="line-height:15px;"><strong>State</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $origin->state ?></td></tr>
            <tr> <td style="line-height:15px;"><strong>Zip</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $origin->zip ?></td></tr>
         </table>  
	</div>
</div> 
</td> 
<td width="1%" valign="top" >&nbsp;  </td>
<td width="49%" valign="top" >
<div class="order-info"  style="width:95%; margin-bottom: 10px;">
	<p class="block-title">Dropoff Information</p>
	<div>
		 <table width="100%" cellpadding="1" cellpadding="1" border="0"  >
                     <tr> <td style="line-height:15px;"  width="10%"><strong>City</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><span class="like-link"onclick="window.open('<?= $destination->getLink() ?>')"><?= $destination->city ?></span></td></tr>
                     <tr> <td style="line-height:15px;"><strong>State</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $destination->state ?></td></tr>
            <tr> <td style="line-height:15px;"><strong>Zip</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $destination->zip ?></td></tr>
		</table>
	</div>
</div> 
	</td>
	</tr>
</table>
<div class="order-info" style="width:97%; margin-bottom: 10px;">
	<p class="block-title">Vehicle(s) Information</p>
	<div>
                        <table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#C4C4C4">
                         <tr bgcolor="#297eaf" >
							 <td  style="padding:3px;"><b><center><font color="white">S.No.</font></center></b></td>
                             <td  style="padding:3px;"><b><center><font color="white"><?= Year ?></font></center></b></td>
                             <td  style="padding:3px;"><b><center><font color="white"><?= Make ?></font></center></b></td>
							 <td  style="padding:3px;"><b><center><font color="white"><?= Model ?></font></center></b></td>
							 <td  style="padding:3px;"><b><center><font color="white"><?= Inop ?></font></center></b></td>
                             <td  style="padding:3px;"><b><center><font color="white"><?= Type ?></font></center></b></td> 
							 <td  style="padding:3px;"><b><center><font color="white"><?= Vin# ?></font></center></b></td>
							 <td  style="padding:3px;"><b><center><font color="white"><?= Lot# ?></font></center></b></td>
							 <td  style="padding:3px;"><b><center><font color="white">Carrier Fee</font></center></b></td>
							 <td  style="padding:3px;"><b><center><font color="white">Broker Fee</font></center></b></td>
							 <td  style="padding:3px;"><b><center><font color="white">Total Amount</font></center></b></td>
						  </tr>
						<?php 
						
						$vehiclecounter = 0;
						foreach($vehicles as $vehicle) : 
						$vehiclecounter = $vehiclecounter + 1;
						?>
                          <tr>
							 <td align="center" bgcolor="#ffffff" style="padding:3px;"><?= $vehiclecounter ?></td>
							 <td align="center" bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->year ?></td>
                             <td align="center" bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->make ?></td>
							 <td align="center" bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->model ?></td> 
							 <td align="center" bgcolor="#ffffff" style="padding-left:5px;"> <?php  print $vehicle->inop==0?"No":"Yes"; ?></td>
                             <td align="center" bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->type ?></td>
                             <td align="center" bgcolor="#ffffff" style="padding:3px;"> <?php  print $vehicle->vin ?></td>
							 <td align="center" bgcolor="#ffffff" style="padding:3px;"> <?php  print $vehicle->lot ?></td>
							 <td align="center" bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->carrier_pay ?></td>
							 <td align="center" bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->deposit ?></td>
							 <td align="center" bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->tariff ?></td>
                           </tr>
						<?php endforeach; ?>
                        </table>
	</div>
</div>       
       
        <div style="clear:right;"></div>
     <!--div class="lead-info"  style="width:97%;float: left;">	<p class="block-title">Dispatch Instructions</p>	<div>		<?php //foreach ($notes[Note::TYPE_FROM] as $note) : ?>		<div style="margin-top:5px;border-bottom: 1px solid #CCCCCC;clear:both;">		<?php //print $note->getText() ?>		</div>		<?php //endforeach; ?>	</div></div-->
           
              
	<div style="clear:left;"></div>
   <?php $notes = $this->notes; ?> 
        <div class="lead-info" style="width: 97%;float: left;margin-top: 10px;">	<p class="block-title">Internal Note</p>	<div>	
		<?php if( ($this->entity->creator_id == (int)$_SESSION['member_id'] 
			|| $this->entity->assigned_id == (int)$_SESSION['member_id'] || $_SESSION['member_id']==1) 
			)://if (!$this->entity->readonly) : ?>		
        <textarea class="form-box-textarea" style="width: 865px; height: 52px;" maxlength="1000" id="internal_note"></textarea>		
             <div style="float:left; padding:2px;">
Quick Notes&nbsp;
</div>
         <div style="float:left; padding:2px;"><select name="quick_notes" id="quick_notes" onchange="addQuickNote();">
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
</select>
</div>
	
           <div style="float:right;"><?= functionButton('Add Note', 'addInternalNote()') ?></div>  
             <div style="clear:both;"><br/>
             </div>	
	<?php endif; ?>		
    <table cellspacing="0" cellpadding="0" width="100%" border="0" class="grid" id="internal_notes_table">
			<thead>
			<tr class="grid-head">
				<td class="grid-head-left"><?=$this->order->getTitle('created', 'Date')?></td>
				<td width="70%">Note</td>
				<td>User</td>
				<td class="grid-head-right">Action</td>
			</tr>
			</thead>
			<tbody>
			<? if (count($notes[Note::TYPE_INTERNAL]) == 0) : ?>
			<tr class="grid-body">
				<td colspan="4" class="grid-body-left grid-body-right" align="center"><i>No notes available.</i></td>
			</tr>
			<? else : ?>
			<?php foreach($notes[Note::TYPE_INTERNAL] as $note) : ?>
			<?php $sender = $note->getSender(); 
			 
			   $email = $sender->email;
			   $contactname = $sender->contactname;
			   if($note->system_admin == 1){
			     $email = "admin@freightdragon.com";
				 $contactname = "FreightDragon";
			   }
			
			if (($_SESSION['member']['access_notes'] == 0 ) 
				    || $_SESSION['member']['access_notes'] == 1
					|| $_SESSION['member']['access_notes'] == 2
					)
			{
			?>
			<tr class="grid-body">
				<td style="white-space:nowrap;" class="grid-body-left"><?= $note->getCreated("m/d/y h:i a") ?></td>
				<td id="note_<?= $note->id ?>_text" ><?php if($note->system_admin == 1){?><b><?= $note->getText() ?></b><?php }else{?><?= $note->getText() ?><?php }?></td>
				<td style="text-align: center;"><a href="mailto:<?= $email ?>"><?= $contactname ?></a></td>
				<td class="grid-body-right" style="white-space: nowrap;">
				  <?php   if (!$this->entity->readonly) : ?>
					
                    
                  <?php //if(($note->sender_id == (int)$_SESSION['member_id']) || ((int)$_SESSION['member_id']==1)){
					  //print $_SESSION['member']['access_notes']."---".$note->sender_id ."==". (int)$_SESSION['member_id']."--".$note->system_admin; 
					  //print $_SESSION['member']['access_notes']."---".$note->sender_id."==".$_SESSION['member_id']."---".$note->system_admin;
					if (($_SESSION['member']['access_notes'] == 0 ) ||
					  ($_SESSION['member']['access_notes'] == 1 && ($note->sender_id == (int)$_SESSION['member_id']))
					  || $_SESSION['member']['access_notes'] == 2
					)
					{
						
						
					 if($note->system_admin == 0 && $_SESSION['member']['access_notes'] != 0 ){
					  ?>  
                      
                    <img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit" width="16" height="16" class="action-icon edit-note" onclick="editInternalNote(<?= $note->id ?>)"/>
                      
					<img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete" width="16" height="16" class="action-icon delete-note" onclick="delInternalNote(<?= $note->id ?>)"/>
                    
                     <?php 
						}
					 
					 }
					 
					 ?>
                    
					<?php else : ?>&nbsp;<?php endif; ?>
				</td>
			</tr>
            
            <?php } ?>
			<?php endforeach; ?>
			<?php endif ; ?>
			</tbody>
		</table>	</div></div>
        
        
        
      
      </td>
   <td valign="top" style="padding-left:5px;"> 
      
    
        <div class="lead-info"  style="width:94%;float: left; margin-bottom:10px;">
           <p class="block-title">Quote Estimate</p>    
			 <div>
			 <img style="vertical-align: middle" src="<?= SITE_IN ?>images/icons/dollar.png" alt="Total Tariff" title="Total Tariff" width="16" height="16"/> 
			 <strong>&nbsp;&nbsp;Total Amount: </strong><?= $this->entity->getTotalTariff() ?><br />        
			 <img style="vertical-align: middle" src="<?= SITE_IN ?>images/icons/truck.png" alt="Tariff to Shipper" title="Tariff to Shipper" width="16" height="16"/> 
			 <strong>&nbsp;&nbsp;Carrier Fee: </strong><?= $this->entity->getCarrierPay() ?><br />        
			 <img style="vertical-align: middle" src="<?= SITE_IN ?>images/icons/person.png" alt="Tariff by Customer" title="Tariff by Customer" width="16" height="16"/> 
			 <strong>&nbsp;&nbsp;Broker Fee: </strong><?= $this->entity->getTotalDeposit() ?>
             <input type="hidden" name="lead_tariff_<?= $this->entity->id ?>" id="lead_tariff_<?= $this->entity->id ?>" value="<?= $this->entity->getTotalTariff(false) ?>" />
             <input type="hidden" name="lead_deposit_<?= $this->entity->id ?>" id="lead_deposit_<?= $this->entity->id ?>" value="<?= $this->entity->getTotalDeposit(false) ?>" />     
			 
			 </div>
        </div> 
		
     <?php if($this->entity->type==4){?>
        <div class="lead-info"  style="width:94%;float: left; margin-bottom:10px;">	<p class="block-title">Status<?php print "<span class='black'>: Quote from a Created Lead</span>";?></p>	<div>		<strong>New Quote: </strong><?php print date("m/d/y h:i a", strtotime($this->entity->created));?>	</div></div>
        <?php }
		    elseif($this->entity->type==1){?>
			<div class="quote-info" style="width:94%;float: left; margin-bottom:10px;">
            	<p class="block-title">Status<?php print "<span class='black'>: Lead</span>";?></p>
            <div>		<strong>New Lead: </strong><?= $this->entity->getReceived("m/d/y h:i a") ?>	</div>
        </div> 
			<?php }else{?>
        <div class="quote-info" style="width:94%;float: left; margin-bottom:10px;">
            	<p class="block-title">Status<?php print "<span class='black'>: Lead</span>";?></p>
            <div>		<strong>New Lead: </strong><?= $this->entity->getReceived("m/d/y h:i a") ?>	</div>
        </div> 


        <?php }?>
      
          
  <?php if($this->entity->lead_type==1){?>
  <div style="clear:left;"></div>
  
   
 <?php }?>         
         </td></tr></table> 
        