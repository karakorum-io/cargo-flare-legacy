<script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script> 

<style>
	.dropdown-item{
		height:65px !important;
	}
</style>
<style type="text/css">
.shipper_detail
{
	text-align:left;
	font-size:15px;
	color:#222;
	height:40px;
	line-height:40px;
	padding-left:15px;
	background-color:#f7f8fa;
	border-bottom:1px solid #ebedf2;
}
.lead-edit .form-box-textfield
{
	width: 210px;
}
h3.details
{
    padding: 22px 0 0;
    width: 100%;
    font-size: 20px;
}
</style>

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
						
					 if(data[i].sender_id == data[i].memberId  && data[i].system_admin == 0 )
					    rows += '<img src="<?= SITE_IN ?>images/icons/strike.png" alt="Discard" title="Discard" width="16" height="16" class="action-icon edit-note" onclick="discardNote('+data[i].id+')"/>';
						
					 if(data[i].system_admin == 0 && data[i].access_notes != 0)
					 {
				
               //rows += '<img src="<?= SITE_IN ?>images/icons/strike.png" alt="Discard" title="Discard" width="16" height="16" class="action-icon edit-note" onclick="discardNote('+data[i].id+')"/>';
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
				Swal.fire("Can't discard note. Try again later, please");
			}
			busy = false;
		},
		error: function(result) {
			Swal.fire("Can't discard note. Try again later, please");
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
					Swal.fire("Can't save note. Try again later, please");
				}
				busy = false;
			},
			error: function(result) {
				$("#internal_note").val(text);
				Swal.fire("Can't save note. Try again later, please");
				busy = false;
			}
		});
	}
	function delInternalNote(id) {

			Swal.fire({
			title: 'Are you sure?',
			text: "Are you sure whant to delete this note?",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, delete it!'
			}).then((result) => {
			if (result.value) {
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
						Swal.fire("Can't delete note. Try again later, please");
					}
					busy = false;
				},
				error: function(result) {
					Swal.fire("Can't delete note. Try again later, please");
					busy = false;
				}
			});
			}
	    })
		
	}


	function editInternalNote(id) {
	 var text = $.trim($("#note_"+id+"_text").text());
	 $("#note_edit_form textarea").val(text);
     $("#edit_save").val(id);
	 $("#note_edit_form").modal();
}
	

function note_edit_form_send(id)
{

	console.log(id);
    var text = $.trim($("#note_"+id+"_text").text());
	if ($("#note_edit_form textarea").val() == text) {
	    $("#note_edit_form").modal('hide');

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
				$("#note_edit_form").modal('hide');
			} else {
				swal.fire("Can't save note. Try again later, please");
			}
			busy = false;
		},
		error: function(result) {
			swal.fire("Can't save note. Try again later, please");
			busy = false;
		}
	  });
	}
}

function addQuickNote() {
	var textOld = $("#internal_note").val();
	
	var str = textOld + " " + $("#quick_notes").val();
	$("#internal_note").val(str);
}
</script>


<!-- Modal -->
<div class="modal fade" id="note_edit_form" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle45" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLongTitle45">Edit Internal Note</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				</button>
			</div>
			<div class="modal-body">
				<textarea style="width: 95%;height:100px;" class="form-box-textarea" name="note_text"></textarea>
				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-dark btn-sm" data-dismiss="modal">Close</button>
				<button type="button" id="edit_save" value="" class="btn_dark_green btn-sm btn" onclick="note_edit_form_send(this.value)">Save </button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript" src="<?=SITE_IN?>jscripts/application/quotes/edit.js"></script>

<div>
	<?php include('lead_menu.php');  ?>
</div>


<div style="margin-top:-45px;margin-bottom:15px;">
	<h3 class="details">Edit Lead #<?= $this->entity->getNumber() ?></h3>
</div>

<div class="mb-4">
	Complete the form below and click Save Lead when finished. Required fields are marked with a <span style="color:red;">*</span>
</div>

<?php if (!$this->entity->isBlocked()) : ?>
<form action="<?= getLink('leads/edit/id/'.$this->entity->id) ?>" method="post" id="create_form">
<?php endif; ?>

<input type="hidden" name="lead_id" value="<?= $this->entity->id ?>"/>

<div class="row">
	
	<div class="col-md-12">
		<div id="ShipperInformation1" class="mb-5" style="background:#fff;border:1px solid #ebedf2;">
		
			<div class="hide_show" id="headingOne">
				<h3 class="shipper_detail">Shipping Information</h3>
			</div>
			
			<div id="ShipperInformation" class="pt-3 pb-3" style="padding-left:20px;padding-right:20px;">

				<div class="row">
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							@shipper_fname@
						</div>
					</div>
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							@shipper_email@
						</div>
					</div>
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							@shipper_address1@
							<div id="suggestions-box-shipper" class="suggestions"></div>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							@shipper_lname@
						</div>
					</div>
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							@shipper_phone1@
						</div>
					</div>
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							@shipper_address2@
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							@shipper_company@
						</div>
					</div>
					
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							@shipper_phone2@
						</div>
					</div>
					<div class="col-12 col-sm-4">
						<div class="new_form-group new_form_group_2_input">
							@shipper_city@							
						</div>
					</div>
				</div>
				
				
				<div class="row">
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							@shipper_type@
						</div>
					</div>
					
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							@shipper_mobile@
						</div>
					</div>
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							<div id="shipper_state_div">
								@shipper_state@
							</div>
							@shipper_zip@
							<div id="notes_container"></div>							
						</div>
					</div>
				</div>
				
				
				<div class="row">
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							@shipper_hours@
						</div>
					</div>
					
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							@shipper_fax@
						</div>
					</div>
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							@shipper_country@
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							
						</div>
					</div>
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							@referred_by@
						</div>
					</div>
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							
						</div>
					</div>
				</div>
				
			</div>
			
		</div>
		
	</div>
</div>


<div class="row">
	
	<div class="col-md-12">

		<div id="AdditionalInformation1" style="background:#fff;border:1px solid #ebedf2;">

			<div class="hide_show" id="headingOne">
				<h3 class="shipper_detail" id="">Additional Information</h3>
			</div>
			
			<div class="pt-3 pb-3" style="padding-left:20px;padding-right:20px;">
			
				<div class="row">
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							@units_per_month@
						</div>
					</div>
					<div class="col-12 col-sm-8">
						<div class="new_form-group">
							@buysell[]@
						</div>
					</div>
				</div>
		
				<div class="row">
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							@shipment_type@
						</div>
					</div>
					<div class="col-12 col-sm-8">
						<div class="new_form-group">
							@buysell_days[]@
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-12 col-sm-4">
						<div class="new_form-group">
							@website@
						</div>
					</div>
					<div class="col-12 col-sm-8">
						<div class="new_form-group">
							@next_shipping_date@
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-12">
						<div class="new_form-group">
							<label>Notes</label>
							@note_to_shipper@
						</div>
					</div>
				</div>
				
			</div>
			
		</div>
	
	</div>

</div>

<div class="row" style="margin-top:20px;margin-bottom:15px">
	<div class="col-md-12" >
		<?php if (!$this->entity->isBlocked()) { ?>
			<div style="float:right">
				<?= submitButtons(SITE_IN."application/leads/show/id/".$this->entity->id, "Save") ?>
			</div>
		<?php } ?>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div style="background:#fff;border:1px solid #ebedf2;" >
			<div class="hide_show " id="headingOne">
				<?php $notes = $this->notes; ?>
				<h3 class="shipper_detail">Internal Notes</h3>	
			</div>
			
			<div class="pt-3 pb-3" style="padding-left:20px;padding-right:20px;">
			
				<div class="row">
					<div class="col-12 mb-3">
						<?php if ($this->entity->status != Entity::STATUS_ARCHIVED) : ?>
						<textarea class="form-box-textarea" style="width:100%; height: 53px;" maxlength="1000" id="internal_note" name="internal_note"></textarea>
					</div>
					<div class="col-md-6">
						<div class="new_form-group">
							<label>Quick Notes</label>
							<select class="form-control" name="quick_notes" id="quick_notes" onchange="addQuickNote();">
								<option value="">--Select--</option>
								<option value="Emailed: Prospect.">Emailed: Prospect.</option>
								<option value="Emailed: Bad Email.">Emailed: Bad Email.</option>
								<option value="Faxed: Prospect.">Faxed: Prospect.</option>
								<option value="Faxed: Bad Fax.">Faxed: Bad Fax.</option>
								<option value="Texted: Prospect.">Texted: Prospect.</option>
								<option value="Texted: Bad Mobile.">Texted: Bad Mobile.</option>
								<option value="Phoned: No Voicemail.">Phoned: No Voicemail.</option>
								<option value="Phoned: Left Message.">Phoned: Left Message.</option>
								<option value="Phoned: Spoke to Prospect.">Phoned: Spoke to Prospect.</option>
								<option value="Phoned: Bad Number.">Phoned: Bad Number.</option>
								<option value="Phoned: Not Intrested.">Phoned: Not Intrested.</option>
								<option value="Phoned: Do Not Call.">Phoned: Do Not Call.</option>
							</select>
							<?php endif; ?>
						</div>
					</div>
				</div>

				<div class="row" style="margin-top: 20px; margin-bottom: 15px">
					<div class="col-md-12">
						<?= functionButton('Add Note', 'addInternalNote()') ?>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<table class="table table-bordered" id="internal_notes_table">
							<thead>
								<tr>
									<th>Date</th>
									<th>Note</th>
									<th>User</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								<? if (count($notes[Note::TYPE_INTERNAL]) == 0) : ?>
								<tr>
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
								if (($_SESSION['member']['access_notes'] == 0 && ($note->sender_id == (int)$_SESSION['member_id'])) 
								|| $_SESSION['member']['access_notes'] == 1
								|| $_SESSION['member']['access_notes'] == 2
								){
								?>
								<tr class="grid-body">
									<td style="white-space:nowrap;"  class="grid-body-left" <?php if($note->priority==2){?> style="color:#FF0000"<?php }?>><?= $note->getCreated("m/d/y h:i a") ?></td>
									<td id="note_<?= $note->id ?>_text" style=" <?php if($note->discard==1){ ?>text-decoration: line-through;<?php }?><?php if($note->priority==2){?>color:#FF0000;<?php }?>"><?php if($note->system_admin == 1 || $note->system_admin == 2){?><b><?= $note->getText() ?></b><?php }elseif($note->priority==2){?><b style="font-size:12px;"><?= $note->getText() ?></b><?php }else{?><?= $note->getText() ?><?php }?></td>
									<td style="text-align:left;" <?php if($note->priority==2){?>style="color:#FF0000"<?php }?>><a href="mailto:<?= $email ?>"><?= $contactname ?></a></td>
									<td class="grid-body-right" style="white-space: nowrap;" <?php if($note->priority==2){?>style="color:#FF0000"<?php }?>>
									<?php if (!$this->entity->readonly) : ?>
									<?php
										if (($_SESSION['member']['access_notes'] == 0 ) ||
										  ($_SESSION['member']['access_notes'] == 1 && ($note->sender_id == (int)$_SESSION['member_id']))
										  || $_SESSION['member']['access_notes'] == 2
										)
										{
										if($note->sender_id == (int)$_SESSION['member_id']  && $note->system_admin == 0 ){ ?>
											<img src="<?= SITE_IN ?>images/icons/strike.png" alt="Discard" title="Discard" width="16" height="16" class="action-icon edit-note" onclick="discardNote(<?= $note->id ?>)"/>	
										<?php }
										if($note->system_admin == 0 && $_SESSION['member']['access_notes'] != 0 ){ ?>
										<img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit" width="16" height="16" class="action-icon edit-note" onclick="editInternalNote(<?= $note->id ?>)"/>
										  
										<img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete" width="16" height="16" class="action-icon delete-note" onclick="delInternalNote(<?= $note->id ?>)"/>										
										<?php } 
										} ?>
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
			</div>
		</div>
	</div>
</div>

</form>

<script type="text/javascript">
$(document).ready(function () {
//called when key is pressed in textbox
$("#shipper_phone1,#shipper_phone2,#shipper_mobile,#shipper_fax").keypress(function (e) {
//if the letter is not digit then display error and don't type anything
if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
//display error message
$("#errmsg").html("Digits Only").show().fadeOut("slow");
return false;
}
});

$("#shipper_phone1,#shipper_phone2,#shipper_mobile,#shipper_fax").attr("placeholder", "xxx-xxx-xxxx");
$("#shipper_phone1,#shipper_phone2,#shipper_mobile,#shipper_fax").attr('maxlength','10');
$('#shipper_phone1,#shipper_phone2,#shipper_mobile,#shipper_fax').keypress(function() {

function phoneFormat() {
phone = phone.replace(/[^0-9]/g, '');
phone = phone.replace(/(\d{3})(\d{3})(\d{4})/, "$1-$2-$3");
return phone;
}
var phone = $(this).val();
phone = phoneFormat(phone);
$(this).val(phone);
});
});
</script>

<script type="text/javascript">
$("#buysell,#buysell_days").select2({
});

$('.hasDatepicker').datepicker();
$('#next_shipping_date').datepicker();
</script>
<script type="text/javascript">
	$(document).ready(function(){
		
		$(document).click(()=>{
            $(".suggestions").html("");
        });

		$("#Shipper_Information").click(function() {
			$("#Shipper_Information_show").toggle();
		});

		$("#origin_address1").blur(()=>{
            autoComplete($("#origin_address1").val().trim(), 'pickup')
        });

        $("#shipper_address1").blur(()=>{
            autoComplete($("#shipper_address1").val().trim(), 'shipper')
        });

        $("#destination_address1").blur(()=>{
            autoComplete($("#destination_address1").val().trim(), 'destination')
        });

        $("#e_cc_address").blur(()=>{
            autoComplete($("#e_cc_address").val().trim(), 'cc')
        });

		function autoComplete(address, type) {

			if(address.trim() != ""){
				$.ajax({
					type: 'POST',
					url: BASE_PATH + 'application/ajax/auto_complete.php',
					dataType: 'json',
					data: {
						action: 'suggestions',
						address: address
					},
					success: function (response) {
						let result = response.result;
						let html = ``;
						let h = null;
						let functionName = null;

						if(type == 'pickup'){
							h = document.getElementById("suggestions-box");
							h.innerHTML = "";
							functionName = 'applyAddressOrigin';
							html += '<ul class="typeahead dropdown-menu" role="listbox" style="top: 36px; width:350px; left: 133px; display: block;">';
							html += '<li><a href="javascript:void(0)" style="height:25px !important; padding-top:0px !important; font-size:10px;">Suggestions</a></li>';
						}

						if(type == 'shipper'){
							h = document.getElementById("suggestions-box-shipper");
							h.innerHTML = "";
							functionName = 'applyAddressShipper';
							html += '<ul class="typeahead dropdown-menu" role="listbox" style="top: 36px; width:350px; left: 133px; display: block;">';
							html += '<li><a href="javascript:void(0)" style="height:25px !important; padding-top:0px!important; font-size:10px;">Suggestions</a></li>';
						}

						if(type == 'destination'){
							h = document.getElementById("suggestions-box-destination");
							h.innerHTML = "";
							functionName = 'applyAddressDestination';
							html += '<ul class="typeahead dropdown-menu" role="listbox" style="top: 36px; width:350px; left: 133px; display: block;">';
							html += '<li><a href="javascript:void(0)" style="height:25px !important; padding-top:0px !important; font-size:10px;">Suggestions</a></li>';
						}


						if(type == 'cc'){
							h = document.getElementById("suggestions-box-cc");
							h.innerHTML = "";
							functionName = 'applyAddressCC';
							html += '<ul class="typeahead dropdown-menu" role="listbox" style="top: 36px; width:350px; left: 133px; display: block;">';
							html += '<li><a href="javascript:void(0)" style="height:25px !important; padding-top:0px !important; font-size:10px;">Suggestions</a></li>';
						}

						result.forEach( (element, index) => {

							let address = `<strong>${element.street}</strong>,<br>${element.city}, ${element.state} ${element.zip}`;
							
							html += `<li>
										<a class="dropdown-item" href="javascript:void(0)" onclick="${functionName}('${element.street}','${element.city}','${element.state}','${element.zip}')" role="option">
											<p>${address}</p>
										</a>
									</li>`;
						});

						html += `<li>
									<a href="javascript:void(0)" style="height: 29px !important;font-size:10px;padding: 0px !important;padding-left: 10px !important; padding-top:10px !important;">Powered by
										&nbsp;&nbsp;&nbsp;<img alt="Cargo Flare" src="https://cargoflare.com/styles/cargo_flare/logo.png" style="width:auto;">
									</a>
								</li>`;
						html += `</ul>`;
						h.innerHTML = html;
					}
				});
			}
		}

		function applyAddressOrigin(address, city, state, zip){
			$("#suggestions-box").html("");
			$("#origin_address1").val(address);
			$("#origin_city").val(city);
			$("#origin_state").val(state);
			$("#origin_zip").val(zip);
			document.getElementById("suggestions-box").innerHTML = "";
		}

		function applyAddressShipper (address, city, state, zip) {
			$("#suggestions-box").html("");
			$("#shipper_address1").val(address);
			$("#shipper_city").val(city);
			$("#shipper_state").val(state);
			$("#shipper_zip").val(zip);
			document.getElementById("suggestions-box-shipper").innerHTML = "";
		}

		function applyAddressDestination (address, city, state, zip) {
			$("#suggestions-box").html("");
			$("#destination_address1").val(address);
			$("#destination_city").val(city);
			$("#destination_state").val(state);
			$("#destination_zip").val(zip);
			document.getElementById("suggestions-box-destination").innerHTML = "";
		}

		function applyAddressCC (address, city, state, zip) {
			$("#suggestions-box").html("");
			$("#e_cc_address").val(address);
			$("#e_cc_city").val(city);
			$("#e_cc_state").val(state);
			$("#e_cc_zip").val(zip);
			document.getElementById("suggestions-box-cc").innerHTML = "";
		}
	});
</script>