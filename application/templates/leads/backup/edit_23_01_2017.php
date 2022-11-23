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
</script>
<div id="note_edit_form" style="display:none;">
	<textarea style="width: 95%;height:100px;" class="form-box-textarea" name="note_text"></textarea>
</div>
<script type="text/javascript" src="<?=SITE_IN?>jscripts/application/quotes/edit.js"></script>
<div style="padding-top:15px;">
<?php include('lead_menu.php');  ?>
</div>
<style type="text/css">
	.lead-edit .form-box-textfield {
		width: 210px;
	}
</style>

<br/>
<h3>Edit Lead #<?= $this->entity->getNumber() ?></h3>
<div style="clear: both;"></div>
Complete the form below and click Save Lead when finished. Required fields are marked with a <span style="color:red;">*</span><br/><br/>
<?php if (!$this->entity->isBlocked()) : ?>
<form action="<?= getLink('leads/edit/id/'.$this->entity->id) ?>" method="post" id="create_form">
<?php endif; ?>
<input type="hidden" name="lead_id" value="<?= $this->entity->id ?>"/>
<div class="lead-info" style="float:none;">
	<p class="block-title">Shipper Information</p>
	<div>
		<table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table quote-edit" style="white-space:nowrap;">
			<tr>
				<td>&nbsp;</td><td><? //print ($_GET['leads'] == 'create')?functionButton('Select Shipper', 'selectShipper()'):'&nbsp;'?></td>
				<td>@shipper_email@</td>
				<td>@shipper_address1@</td>
			</tr>
			<tr>
				<td>@shipper_fname@</td>
				<td>@shipper_phone1@</td>
				<td>@shipper_address2@</td>
			</tr>
			<tr>
				<td>@shipper_lname@</td>
				<td>@shipper_phone2@</td>
				<td>@shipper_city@</td>
			</tr>
			<tr>
				<td>@shipper_company@</td>
				<td>@shipper_mobile@</td>
				<td>@shipper_state@@shipper_zip@</td><div id="notes_container"></div>
			</tr>
			<!--tr>
				<?php if ($_GET['quotes'] == 'create') { ?>
				<td align="right">@shipper_add@</td>
				<?php } else { ?>
				<td colspan="2">&#8203;</td>
				<?php } ?>
				<td>@shipper_fax@</td>
				<td>@shipper_country@</td>
			</tr-->
			
			<tr>
				<td>@shipper_type@</td>
				<td>@shipper_fax@</td>
				<td>@shipper_country@</td>
			</tr>
			<tr>
				<td>@shipper_hours@</td> 
				<td>@referred_by@</td> 
				<td>&nbsp;</td><td>&nbsp;</td>
			</tr>
			
            
            
		</table>
	</div>
</div>
<br/>
<div class="quote-info" style="float:none;">
	<p class="block-title">Additional Information</p>
	<div>
		<table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table quote-edit" style="white-space:nowrap;">
			
			
			<tr>
			<td>@units_per_month@</td>
			<td>@buysell[]@</td>
			
			</tr>
            <tr>
				<td>@shipment_type@</td>
				<td>@buysell_days[]@</td> 
				
			</tr>
            <tr>
				<td>@website@</td>
				<td>@next_shipping_date@</td> 
				
			</tr>
            
            
		</table>
	</div>
</div>
<br/>

<?php if (!$this->entity->isBlocked()) { ?>
<div style="float:right">
	<?= submitButtons(SITE_IN."application/leads/show/id/".$this->entity->id, "Save") ?>
</div>

<?php } ?>

<br />

<?php $notes = $this->notes; ?>
<div class="order-info" style="width:98%;float: right;margin-top: 10px;">
	<p class="block-title">Internal Notes</p>
	<div>
	<?php if ($this->entity->status != Entity::STATUS_ARCHIVED) : ?>
       <textarea class="form-box-textarea" style="width:100%; height: 53px;" maxlength="1000" id="internal_note" name="internal_note"></textarea>
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
		<div style="clear:both;"><br/></div>
		
		
		<div style="clear:both;"><br/></div>
	<?php endif; ?>
		<table cellspacing="0" cellpadding="0" width="100%" border="0" class="grid" id="internal_notes_table">
			<thead>
			<tr class="grid-head">
				<td class="grid-head-left">Date</td>
				<td>Note</td>
				<td>User</td>
				<td class="grid-head-right">Action</td>
			</tr>
			</thead>
			<tbody>
			<? if (count($notes[Note::TYPE_INTERNAL]) == 0) : ?>
			<tr class="grid-body">
				<td colspan="4" class="grid-body-left grid-body-right" align="center"><i>No notes available.</i></td>
			</tr>
			<? else : //print "<pre>";print_r($notes[Note::TYPE_INTERNAL]);?>
			<?php foreach($notes[Note::TYPE_INTERNAL] as $note) : ?>
			<?php $sender = $note->getSender(); 
			 
			   $email = $sender->email;
			   $contactname = $sender->contactname;
			   if($note->system_admin == 2){
			     $email = "admin@freightdragon.com";
				 $contactname = "FreightDragon";
			   }
			
			//print $_SESSION['member']['access_notes']."---".$note->sender_id ."==". (int)$_SESSION['member_id'];
			if (($_SESSION['member']['access_notes'] == 0 && ($note->sender_id == (int)$_SESSION['member_id'])) 
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
		</table>
	</div>
</div>
</form>
<script type="text/javascript">//<![CDATA[
    $("#buysell").multiselect({ // Build multiselect for users
        noneSelectedText: 'Select',
        selectedText: '# selected',
        selectedList: 1
    });
	
	$("#buysell_days").multiselect({ // Build multiselect for users
        noneSelectedText: 'Select',
        selectedText: '# selected',
        selectedList: 1
    });
	
    
   
    //]]></script>

