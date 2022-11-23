<script type="text/javascript">
		var busy = false;
function updateInternalNotes(data) {
		var rows = "";
		
		for (i in data) {
			
			var email = data[i].email;
			var contactname = data[i].sender;
			
			if(data[i].system_admin == 2){
			     email = "admin@freightdragon.com";
				 contactname = "FreightDragon";
			   }
			if ((data[i].access_notes == 0 )   
				    || data[i].access_notes == 1
					|| data[i].access_notes == 2
					)
			{
				
              var discardStr = '';
				if(data[i].discard==1)
				   discardStr = ' style="text-decoration: line-through;" ';
				 
			if(data[i].system_admin == 1 || data[i].system_admin == 2)
			{
				rows += '<tr class="grid-body"><td style="white-space:nowrap;" class="grid-body-left" >'+data[i].created+'</td><td id="note_'+data[i].id+'_text"  '+discardStr+'><b>'+decodeURIComponent(data[i].text)+'</b></td><td>';	 
			}
			else if(data[i].priority==2)
			   rows += '<tr class="grid-body"><td class="grid-body-left" >'+data[i].created+'</td><td id="note_'+data[i].id+'_text"  '+discardStr+'><b style="font-size:12px;color:red;">'+decodeURIComponent(data[i].text)+'</b></td><td>';
			 else
			   rows += '<tr class="grid-body"><td class="grid-body-left">'+data[i].created+'</td><td id="note_'+data[i].id+'_text"  '+discardStr+'>'+decodeURIComponent(data[i].text)+'</td><td>';
			
		
			
			rows += '<a href="mailto:'+email+'">'+contactname+'</a></td><td style="white-space: nowrap;" class="grid-body-right"  >';
			
			<?php //if (!$this->entity->readonly) : ?>
			
				if ((data[i].access_notes == 0 ) ||
					  (data[i].access_notes == 1 && (data[i].sender_id == data[i].memberId))
					  || data[i].access_notes == 2
					)
					{
						
						
					 if(data[i].system_admin == 0 && data[i].access_notes != 0)
					 {
				
               rows += '<img src="<?= SITE_IN ?>images/icons/strike.png" alt="Discard" title="Discard" width="16" height="16" class="action-icon edit-note" onclick="discardNote('+data[i].id+')"/>';
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
	
function discardNote(note_id) {

	$.ajax({
		type: "POST",
		url: "<?= SITE_IN ?>application/ajax/notes.php",
		dataType: 'json',
		data: {
			action: 'discard',
			id: note_id,
			entity_id: <?= $this->entity->id ?>,
			notes_type: <?= Note::TYPE_INTERNAL ?>
		},
		success: function(result) {
			if (result.success == true) {
				updateInternalNotes(result.data);
			} else {
				alert("Can't discard note. Try again later, please");
			}
			busy = false;
		},
		error: function(result) {
			alert("Can't discard note. Try again later, please");
			busy = false;
		}
	});
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

 if(is_array($_SESSION['searchDataCLead']) && $_SESSION['searchCountCLead']>0){
	 //$_SESSION['searchShowCount'] = $_SESSION['searchShowCount'] + 1;
	  
	 $eid = $_GET['id'];
	 $indexSearchData = array_search($eid,$_SESSION['searchDataCLead']);
   
	   $nextSearch = $indexSearchData+1;
	   $_SESSION['searchShowCountCLead'] = $indexSearchData;
	   $prevSearch = $indexSearchData-1;
	   
	 $entityPrev = $_SESSION['searchDataCLead'][$prevSearch];
	 $entityNext = $_SESSION['searchDataCLead'][$nextSearch];
?>
<div style="float:right; width:170px;">
  <div style="float:left;width:50px;">
  <?php if($_SESSION['searchShowCountCLead']==0 ){?>
       <img src="<?= SITE_IN ?>images/arrow-down-gray.png"   width="40" height="40"/>
     <?php }else{?>
       <a href="<?= SITE_IN ?>application/leads/show/id/<?= $entityPrev ?>"><img src="<?= SITE_IN ?>images/arrow-down.png"   width="40" height="40"/></a>
       
     <?php }?>
  </div>
  <div style="float:left;width:70px; text-align:center; padding-top:10px;">
    <h3><?php print $_SESSION['searchShowCountCLead']+1;?> - <?php print $_SESSION['searchCountCLead'];?></h3>
  </div>
  <div style="float:left;width:50px;">
  <?php if($_SESSION['searchShowCountCLead'] == ($_SESSION['searchCountCLead']-1)){?>
         <img src="<?= SITE_IN ?>images/arrow-up-gray.png"    width="40" height="40"/>
  <?php }else{?>
       <a href="<?= SITE_IN ?>application/leads/show/id/<?= $entityNext ?>"><img src="<?= SITE_IN ?>images/arrow-up.png"   width="40" height="40"/></a>
     <?php }?>
     
  </div>
</div>
<?php }  ?>  
<div style="padding-top:15px;">

<?php include('lead_menu.php');  ?></div><br/><h3>Lead #<?= $this->entity->number ?> Detail</h3><div style="clear: both;"></div>
<table>
	<tr>
	<td valign="top" width="75%">
<div class="order-info" style="width:97%; margin-bottom: 10px;"><?php	$source = $this->entity->getSource();	$assigned = $this->entity->getAssigned();	$shipper = $this->entity->getShipper();	$origin = $this->entity->getOrigin();	$destination = $this->entity->getDestination();	$vehicles = $this->entity->getVehicles();?>	

<p class="block-title">Lead Information</p>	<div>				
<?php
				
			if(trim($shipper->phone1)!="")
			{
				$arrArea = array();
				$arrArea = explode(")",formatPhone($shipper->phone1));
				 
				$code     = str_replace("(","",$arrArea[0]);
				$areaCodeStr="";  
				//print "WHERE  AreaCode='".$code."'";
				$areaCodeRows = $this->daffny->DB->selectRow("*", "app_area_code", "WHERE  AreaCode='".$code."'");
				if (!empty($areaCodeRows)) {
					 $areaCodeStr = "<b>".$areaCodeRows['StdTimeZone']."-".$areaCodeRows['statecode']."</b>"; 
				}
			}
			
			
			?>
        <?php
{                                                       
	$code     = substr($shipper->phone1, 0, 3);
	$areaCodeStr="";                
	$areaCodeRows = $this->daffny->DB->selectRow("*", "app_area_code", "WHERE  AreaCode='".$code."'");

		if (!empty($areaCodeRows)) {
			$areaCodeStr = "<b>".$areaCodeRows['StdTimeZone']."-".$areaCodeRows['statecode']."</b>";
		}
}
?>	
        <?php
{                                                       
	$code     = substr($shipper->phone1, 0, 3);
	$areaCodeStr="";                
	$areaCodeRows = $this->daffny->DB->selectRow("*", "app_area_code", "WHERE  AreaCode='".$code."'");

		if (!empty($areaCodeRows)) {
			$areaCodeStr = "<b>".$areaCodeRows['StdTimeZone']."-".$areaCodeRows['statecode']."</b>";
		}
}
?>
<table cellspacing="0" cellpadding="0" border="0" width="100%">
			<tr>
				<td style="vertical-align:top;" valign="top" width="50%">
                  <table width="100%" cellpadding="1" cellpadding="1">
                   <tr><td width="23%"><strong>Name</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $shipper->fname ?> <?= $shipper->lname ?></td></tr>
                   <tr> <td style="line-height:15px;"><strong>Company</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td  style="line-height:15px;"><b><?= $shipper->company; ?></b></td></tr>
                   <tr> <td style="line-height:15px;"><strong>Email</strong></td><td width="4%" align="center"><b>:</b></td><td><a href="mailto:<?= $shipper->email ?>"><?= $shipper->email ?></a></td></tr>
                   <tr> <td style="line-height:15px;"><strong>Phone</strong></td><td width="4%" align="center"><b>:</b></td><td><?= formatPhone($shipper->phone1); ?> <?=$areaCodeStr ?> </td></tr>
				   <?php
{                                                       
	$code     = substr($shipper->phone2, 0, 3);
	$areaCodeStr="";                
	$areaCodeRows = $this->daffny->DB->selectRow("*", "app_area_code", "WHERE  AreaCode='".$code."'");

		if (!empty($areaCodeRows)) {
			$areaCodeStr = "<b>".$areaCodeRows['StdTimeZone']."-".$areaCodeRows['statecode']."</b>";
		}
}
?>
				   <tr> <td style="line-height:15px;"><strong>Phone 2</strong></td><td width="4%" align="center"><b>:</b></td><td><?= formatPhone($shipper->phone2); ?> <?=$areaCodeStr ?> </td></tr>
				   <?php
{                                                       
	$code     = substr($shipper->mobile, 0, 3);
	$areaCodeStr="";                
	$areaCodeRows = $this->daffny->DB->selectRow("*", "app_area_code", "WHERE  AreaCode='".$code."'");

		if (!empty($areaCodeRows)) {
			$areaCodeStr = "<b>".$areaCodeRows['StdTimeZone']."-".$areaCodeRows['statecode']."</b>";
		}
}
?>
                   <tr> <td style="line-height:15px;"><strong>Mobile</strong></td><td width="4%" align="center"><b>:</b></td><td><?= formatPhone($shipper->mobile); ?> <?=$areaCodeStr ?></td></tr>
                   <tr> <td style="line-height:15px;"><strong>Fax</strong></td><td width="4%" align="center"><b>:</b></td><td><?= formatPhone($shipper->fax); ?></td>
                   
				   
					<tr><td style="line-height:15px;"><strong>Assigned to</strong></td><td width="4%" align="center"><b>:</b></td><td><?= $assigned->contactname ?></td></tr>
				   <tr><td style="line-height:15px;"><strong>Source</strong></td><td width="4%" align="center"><b>:</b></td><td><?= $this->entity->referred_by ?></td></tr>
                   
                 </table>   
				</td>
				<td style="vertical-align:top;">
					<table width="100%" cellpadding="1" cellpadding="1">
                       <tr><td colspan="3"><strong>What auction do they buysell from: </strong></td></tr>
                       <tr><td colspan="3">
                       <?php
					   
					   
					    $buysell =  json_decode($this->entity->buysell);
					   for($i=0;$i<sizeof($buysell);$i++){
						   print $buysell[$i].",";
					   }
					   ?>
                       </td></tr>
                       
                       <tr><td  colspan="3"><strong>What days do they normally buysell vehicles: </strong></td></tr>
                       <tr><td colspan="3">
                       <?php
					   $days = array("1"=>"Mon","2"=>"Tue","3"=>"Wed","4"=>"Thu","5"=>"Fri","6"=>"Sat","7"=>"Sun");
					    $buysell_days =  json_decode($this->entity->buysell_days);
					   for($i=0;$i<sizeof($buysell_days);$i++){
						  // print $buysell_days[$i].",";
						   $index = $buysell_days[$i];
						   print $days[$index].",";
					   }
					   ?>
                       </td></tr>
                       
                      <tr><td colspan="3"><strong>Next possible shipping date: </strong></td></tr>
                      <tr><td colspan="3"><?php if($this->entity->next_shipping_date != "0000-00-00 00:00:00")print $this->entity->next_shipping_date;?></td></tr>
                      
                      <tr><td width="25%"><strong>Website: </strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><span style="weight:bold;"><b><?= $this->entity->website; ?></b></span></td></tr>
                      <?php if($this->entity->lead_type==1){
						  
						  $shipment_type = "--";
					   if($shipper->shipment_type==1)
						  $shipment_type = "Full load";
					   elseif($shipper->shipment_type==2)
						  $shipment_type = "Singles";
					   elseif($shipper->shipment_type==3)
						  $shipment_type = "Both";
						  ?>
                      <tr><td width="25%"><strong>Hours: </strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><span style="weight:bold;"><?=  $shipper->shipper_hours; ?></span></td></tr>
                      <tr><td width="25%"><strong>Shipment Type: </strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><span style="weight:bold;"><?= $shipment_type; ?></span></td></tr>
                      <tr><td width="25%"><strong>Units/Month: </strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><span style="weight:bold;"><?= $shipper->units_per_month; ?></span></td></tr>
                       <?php }?>
                      
  <div style="clear:left;"></div>
   
 
                          
                    </table>  
                     
				</td>
			</tr>
		</table>	
        
        </div></div>
        
        
       
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
			   if($note->system_admin == 2){
			     $email = "admin@freightdragon.com";
				 $contactname = "FreightDragon";
			   }
			
			if (($_SESSION['member']['access_notes'] == 0 ) 
				    || $_SESSION['member']['access_notes'] == 1
					|| $_SESSION['member']['access_notes'] == 2
					)
			{
				//$color = "";
				//if($note->priority==2)
				// $color = "#39F";
			?>
			<tr class="grid-body" >
				 <td style="white-space:nowrap;"  class="grid-body-left" <?php if($note->priority==2){?> style="color:#FF0000"<?php }?>><?= $note->getCreated("m/d/y h:i a") ?></td>
				<td id="note_<?= $note->id ?>_text" style=" <?php if($note->discard==1){ ?>text-decoration: line-through;<?php }?><?php if($note->priority==2){?>color:#FF0000;<?php }?>"><?php if($note->system_admin == 1 || $note->system_admin == 2){?><b><?= $note->getText() ?></b><?php }elseif($note->priority==2){?><b style="font-size:12px;"><?= $note->getText() ?></b><?php }else{?><?= $note->getText() ?><?php }?></td>
				<td style="text-align: center;" <?php if($note->priority==2){?>style="color:#FF0000"<?php }?>><a href="mailto:<?= $email ?>"><?= $contactname ?></a></td>
				<td class="grid-body-right" style="white-space: nowrap;" <?php if($note->priority==2){?>style="color:#FF0000"<?php }?>>
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
                      
                    <img src="<?= SITE_IN ?>images/icons/strike.png" alt="Discard" title="Discard" width="16" height="16" class="action-icon edit-note" onclick="discardNote(<?= $note->id ?>)"/>	
                    
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
      
     <?php if($this->entity->lead_type==1){?>
        <div class="lead-info"  style="width:94%;float: left; margin-bottom:10px;">	<p class="block-title">Status<?php print "<span class='black'>: Created Lead</span>";?></p>	<div>		<strong>New lead: </strong><?php print date("m/d/y h:i a", strtotime($this->entity->created));?>	</div></div>
        <?php }else{?>
       
         <div class="lead-info" style="width:94%;float: left; margin-bottom:10px;">	<p class="block-title">Status<?php print "<span class='black'>: Imported Lead</span>";?></p>	<div>		<strong>New lead: </strong><?= $this->entity->getReceived("m/d/y h:i a") ?>	</div></div><div class="lead-info" style="width:220px;float:right;margin-right: 10px;">	<p class="block-title">Dates</p>	<div><strong>Estimated Ship Date: </strong><?= $this->entity->getShipDate("m/d/y") ?>	</div></div>
        
        <?php }?>
        
          
  <?php if($this->entity->lead_type==1){?>
  <div style="clear:left;"></div>
<div class="lead-info"  style="width:94%;float: left; margin-bottom:10px;">	
<p class="block-title">Additional Information</p>	<div>
 <?php 
                  
						$shipment_type = "--";
					   if($shipper->shipment_type==1)
						  $shipment_type = "Full load";
					   elseif($shipper->shipment_type==2)
						  $shipment_type = "Singles";
					   elseif($shipper->shipment_type==3)
						  $shipment_type = "Both";
						  
				?>		  
                    <strong>Hours: </strong><?= $shipper->shipper_hours; ?><br/>
                    <strong>Shipment Type: </strong><?= $shipment_type; ?><br/>
                    <strong>Units/Month: </strong><?= $shipper->units_per_month; ?><br/>
             

</div></div>    
 <?php }?>         
         </td></tr></table> 
        