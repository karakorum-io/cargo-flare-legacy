<style type="text/css">
	th {
		font-size: 12px;
	}
	div#LeadInformation1 {
		border: 1px solid #ddeaea;
		margin-bottom: 15px;

	}
	.border_all{
		border: 1px solid #e2f1ef;
		margin-bottom: 15px;
	}
	tr.grid-body {
		border-left: 1px solid black !important;
	}
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
	table.custom_table_new_info th,
	table.custom_table_new_info td
	{
		border:0;
	}
	h3.details
	{
		padding: 22px 0 0;
		width: 100%;
		font-size: 20px;
	}
</style>

<script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script> 
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
			text: "Are you sure want to delete this note?!",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, delete it!'
		}).then((result) => {
			if (result.value) {
				if (busy) {
					return;
				}
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
							swal.fire("Can't delete note. Try again later, please");
						}
						busy = false;
					},
					error: function(result) {
						swal.fire("Can't delete note. Try again later, please");
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

	function note_edit_form_send(id) {
		var text = $.trim($("#note_"+id+"_text").text());
		if ($("#note_edit_form textarea").val() == text) {
			$("#note_edit_form").modal('hide');

		} else {
			if (busy){
				return;
			}
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

<?php
	if(is_array($_SESSION['searchDataCLead']) && $_SESSION['searchCountCLead']>0){
		$eid = $_GET['id'];
		$indexSearchData = array_search($eid,$_SESSION['searchDataCLead']);
		$nextSearch = $indexSearchData+1;
		$_SESSION['searchShowCountCLead'] = $indexSearchData;
		$prevSearch = $indexSearchData-1;
		$entityPrev = $_SESSION['searchDataCLead'][$prevSearch];
		$entityNext = $_SESSION['searchDataCLead'][$nextSearch];
	}
?>  

<div><?php include('lead_menu.php'); ?></div>

<div style="margin-top:-45px;margin-bottom:15px;">
	<h3 class="details">Lead #<?= $this->entity->number ?> Detail</h3>		
</div>

<div class="row">
	<div class="col-md-8">
		<div id="LeadInformation1" style="background:#fff;border:1px solid #ebedf2;" class="mb-5 mt-3">
			<div class="hide_show" id="headingOne">
				<h3 class="shipper_detail" id="Shipper_Information" >Lead Information</h3>
			</div>
			<div style="padding-left:20px;padding-right:20px;">
				<?php
					// $source = $this->entity->getSource();
					$assigned = $this->entity->getAssigned();
					$shipper = $this->entity->getShipper();
					$origin = $this->entity->getOrigin();
					$destination = $this->entity->getDestination();
					$vehicles = $this->entity->getVehicles();

					if(trim($shipper->phone1)!="") {
						$arrArea = array();
						$arrArea = explode(")",formatPhone($shipper->phone1));
						$code     = str_replace("(","",$arrArea[0]);
						$areaCodeStr="";
						$areaCodeRows = $this->daffny->DB->selectRow("*", "app_area_code", "WHERE  AreaCode='".$code."'");
						if (!empty($areaCodeRows)) {
							$areaCodeStr = "<b>".$areaCodeRows['StdTimeZone']."-".$areaCodeRows['statecode']."</b>"; 
						}
					}

					$code     = substr($shipper->phone1, 0, 3);
					$areaCodeStr="";                
					$areaCodeRows = $this->daffny->DB->selectRow("*", "app_area_code", "WHERE  AreaCode='".$code."'");

					if (!empty($areaCodeRows)) {
						$areaCodeStr = "<b>".$areaCodeRows['StdTimeZone']."-".$areaCodeRows['statecode']."</b>";
					}
				?>
				<div class="row">
					<div class="col-md-6">
						<table class="table custom_table_new_info">
							<tr>
								<th>
									<strong>Name</strong>
								</th>
								<td align="center">
									<b>:</b>
								</td>
								<td align="left">
									<?= $shipper->fname ?> <?= $shipper->lname ?>
								</td>
							</tr>
							<tr> 
								<th>
									<strong>Company</strong>
								</th>
								<td align="center"><b>:</b></td>
								<td><b><?= $shipper->company; ?></b></td>
							</tr>
							<tr>
								<th><strong>Email</strong></th>
								<td align="center"><b>:</b></td>
								<td><a href="mailto:<?= $shipper->email ?>"><?= $shipper->email ?></a></td>
							</tr>
							<tr>
								<th><strong>Phone</strong></th>
								<td align="center"><b>:</b></td>
								<td><?= formatPhone($shipper->phone1); ?> <?=$areaCodeStr ?> </td>
							</tr>
							<?php                                                   
								$code     = substr($shipper->phone2, 0, 3);
								$areaCodeStr="";                
								$areaCodeRows = $this->daffny->DB->selectRow("*", "app_area_code", "WHERE  AreaCode='".$code."'");

								if (!empty($areaCodeRows)) {
									$areaCodeStr = "<b>".$areaCodeRows['StdTimeZone']."-".$areaCodeRows['statecode']."</b>";
								}
							?>
							<tr>
							<th>
								<strong>Phone 2</strong></th>
								<td align="center"><b>:</b></td>
								<td><?= formatPhone($shipper->phone2); ?> <?=$areaCodeStr ?></td>
							</tr>
							<?php
								$code     = substr($shipper->mobile, 0, 3);
								$areaCodeStr="";                
								$areaCodeRows = $this->daffny->DB->selectRow("*", "app_area_code", "WHERE  AreaCode='".$code."'");

								if (!empty($areaCodeRows)) {
									$areaCodeStr = "<b>".$areaCodeRows['StdTimeZone']."-".$areaCodeRows['statecode']."</b>";
								}
							?>
							<tr> 
								<th><strong>Mobile</strong></th>
								<td  align="center"><b>:</b></td>
								<th><?= formatPhone($shipper->mobile); ?> <?=$areaCodeStr ?></th>
							</tr>
							<tr>
								<th><strong>Fax</strong></th>
								<td align="center"><b>:</b></td>
								<td class=""><?= formatPhone($shipper->fax); ?></td>
							</tr>
							<tr>
								<th style="line-height:15px;"><strong>Assigned to</strong></th>
								<td  align="center"><b>:</b></td>
								<td class=""><?= $assigned->contactname ?></td>
							</tr>
							<tr>
								<th ><strong>Source</strong></th>
								<td  align="center"><b>:</b></td>
								<td> <?= $this->entity->referred_by ?  $this->entity->referred_by :  getLeadSourceByID($this->daffny->DB, $this->entity->source_id)[0]['company_name']?> </td>
							</tr>
						</table>
					</div>

					<div class="col-md-6">
						<table class="table custom_table_new_info">
							<tr>
								<td colspan="3">
									<strong class="table-brand" >What auction do they buysell from: </strong>
								</td>
							</tr>
							<tr>
								<td colspan="3">
									<?php
										$buysell =  json_decode($this->entity->buysell);
										for($i=0;$i<sizeof($buysell);$i++){
											print $buysell[$i].",";
										}
									?>
								</td>
							</tr>
							<tr>
								<td colspan="3"><strong class="table-brand">What days do they normally buysell vehicles: </strong></td>
							</tr>
							<tr>
								<td colspan="3">
									<?php
										$days = array("1"=>"Mon","2"=>"Tue","3"=>"Wed","4"=>"Thu","5"=>"Fri","6"=>"Sat","7"=>"Sun");
										$buysell_days =  json_decode($this->entity->buysell_days);
										for($i=0;$i<sizeof($buysell_days);$i++){
											$index = $buysell_days[$i];
											print $days[$index].",";
										}
									?>
								</td>
							</tr>
							<tr>
								<td colspan="3"><strong class="table-brand">Next possible shipping date: </strong></td>
							</tr>
							<tr>
								<td colspan="3"><?php if($this->entity->next_shipping_date != "0000-00-00 00:00:00")print $this->entity->next_shipping_date;?></td>
							</tr>
							<tr>
								<td width="40%"><strong>Website: </strong></td>
								<td width="4%" align="center"  style="line-height:15px;"><b>:</b></td>
								<td align="left"  style="line-height:15px;">
									<span style="weight:bold;"><b><?= $this->entity->website; ?></b></span>
								</td>
							</tr>
							<?php 
								if($this->entity->lead_type==1){
									$shipment_type = "--";

									if($shipper->shipment_type==1)
										$shipment_type = "Full load";
									elseif($shipper->shipment_type==2)
										$shipment_type = "Singles";
									elseif($shipper->shipment_type==3)
										$shipment_type = "Both";
							?>
							<tr>
								<td><strong>Hours: </strong></td>
								<td width="4%" align="center"  style="line-height:15px;"><b>:</b></td>
								<td align="left"  style="line-height:15px;"><span style="weight:bold;"><?=  $shipper->shipper_hours; ?></span></td>
							</tr>
							<tr>
								<td><strong>Shipment Type: </strong></td>
								<td width="4%" align="center"  style="line-height:15px;"><b>:</b></td>
								<td style="color: white" ><?= $shipment_type; ?></td>
							</tr>
							<tr>
								<td><strong>Units/Month: </strong></td>
								<td width="4%" align="center"  style="line-height:15px;"><b>:</b></td>
								<td align="left"><span style="weight:bold;"><?= $shipper->units_per_month; ?></span></td>
							</tr>
							<?php
								}
							?>
						</table>  
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-4">
		<div id="Status1" style="background:#fff;border:1px solid #ebedf2;" class="mb-5 mt-3">
			<div class="hide_show" id="headingOne">
				<h3 class="shipper_detail">
					<?php if($this->entity->lead_type==1){?> Status<?php print "<span class='black'>: Created Lead</span>";?>
				</h3>
			</div>
			<div id="Status" class="collapse show" aria-labelledby="headingOne" data-parent="#Status1">
				<div class="pt-3 pb-3" style="padding-left:20px;padding-right:20px;">
					<strong>New Leads</strong>	<?php print date("m/d/y h:i a", strtotime($this->entity->created));?>	
					<?php }else{?>
					Status<?php print "<span class='black'>: Imported Lead</span>";?>		
					<strong class="kt-font-success">New lead: </strong><?= $this->entity->getReceived("m/d/y h:i a") ?>	
					Dates<strong>Estimated Ship Date: </strong><?= $this->entity->getShipDate("m/d/y") ?>
					<?php }?>
				</div>
			</div>
		</div>
		<div class="accordion border_all " id="AdditionalInformation1" style="background:#fff;border:1px solid #ebedf2;">
			<div class="hide_show" id="headingOne" >
				<?php if($this->entity->lead_type==1){?>
				<h3 class="shipper_detail" id="">Additional Information</h3>
			</div>
	     	<div class="pt-3 pb-3" style="padding-left:20px;padding-right:20px;">
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
				<strong>First Avail.: </strong><?= $this->entity->getFirstAvail("m/d/y") ?><br/>
				<strong>Called For: </strong><?= $this->entity->calling_for; ?><br/>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
<?php
	$origin = $this->entity->getOrigin();
	$destination = $this->entity->getDestination();
?>
<div class="row">
	<div class="col-md-4">
		<div id="InternalNote1" style="background:#fff;border:1px solid #ebedf2; margin-bottom:40px;">
			<div class="hide_show" id="headingOne">
				<h3 class="shipper_detail">Pickup Information</h3>
				<div id="pickup_infomation_info_new_2" style="padding-left:20px;padding-right:20px;">
					<table class="table custom_table_new_info">
						<tr>
							<td width="35%"><strong>Address</strong></td>
							<td width="4%" align="center"  style="line-height:15px;"><b>:</b></td>
							<td align="left"  style="line-height:15px;"><?= $origin->address1; ?>&nbsp;&nbsp;<?= $origin->address2; ?></td>
						</tr>
						<tr>
							<td style="line-height:15px;"><strong>City / State / Zip</strong></td>
							<td width="4%" align="center"  style="line-height:15px;"><b>:</b></td>
							<td align="left"  style="line-height:15px;">
								<span class="like-link"onclick="window.open('<?= $origin->getLink() ?>')"><?= $origin->getFormatted() ?></span>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div id="InternalNote1" style="background:#fff;border:1px solid #ebedf2; margin-bottom:40px;">
			<div class="hide_show" id="headingOne">
				<h3 class="shipper_detail">Dropoff Information</h3>
				<div id="pickup_infomation_info_new_2" style="padding-left:20px;padding-right:20px;">
					<table class="table custom_table_new_info">
						<tr>
							<td width="35%"><strong>Address</strong></td>
							<td width="4%" align="center"  style="line-height:15px;"><b>:</b></td>
							<td align="left"  style="line-height:15px;"><?= $destination->address1; ?>&nbsp;&nbsp;<?= $destination->address2; ?></td>
						</tr>
						<tr>
							<td style="line-height:15px;"><strong>City / State / Zip</strong></td>
							<td width="4%" align="center"  style="line-height:15px;"><b>:</b></td>
							<td align="left"  style="line-height:15px;">
								<span class="like-link"onclick="window.open('<?= $destination->getLink() ?>')"><?= $destination->getFormatted() ?></span>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-8">
		<div id="InternalNote1" style="background:#fff;border:1px solid #ebedf2;">
			<div class="hide_show" id="headingOne">
				<?php $notes = $this->notes; ?> 
				<h3 class="shipper_detail">Internal Note</h3>
			</div>
			
			<div id="InternalNote" class="collapse show" aria-labelledby="headingOne" data-parent="#InternalNote1">
				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<label>.</label>	
							<textarea class="form-box-textarea form-control"  maxlength="1000" id="internal_note"></textarea>		
						</div>
						<div class="col-md-4">
							<label>Quick Notes&nbsp;</label>
							<select name="quick_notes" id="quick_notes" class="form-control" onchange="addQuickNote();">
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
						<div class="col-md-4">
							&nbsp;
						</div>
						<div class="col-md-4">
							<div class="text-right">
								<br/>
								<?= functionButton('Add Note', 'addInternalNote()') ?> 
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="row">
								<table class="table table-bordered" id="internal_notes_table" style="margin: 10px">
									<thead>
										<tr>
											<th><?=$this->order->getTitle('created', 'Date')?></th>
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
										<? else : ?>
										<?php foreach($notes[Note::TYPE_INTERNAL] as $note) : ?>
										<?php $sender = $note->getSender(); 

										$email = $sender->email;
										$contactname = $sender->contactname;
										if($note->system_admin == 2){
											$email = "admin@freightdragon.com";
											$contactname = "FreightDragon";
										}

										if (($_SESSION['member']['access_notes'] == 0 )  || $_SESSION['member']['access_notes'] == 1 || $_SESSION['member']['access_notes'] == 2 ){
										?>
										<tr class="grid-body" >
											<td <?php if($note->priority==2){?> style="color:#FF0000"<?php }?>>
												<?= $note->getCreated("m/d/y h:i a") ?>
											</td>
											<td id="note_<?= $note->id ?>_text" style=" <?php if($note->discard==1){ ?>text-decoration: line-through;<?php }?><?php if($note->priority==2){?>color:#FF0000;<?php }?>">
												<?php if($note->system_admin == 1 || $note->system_admin == 2){?><b><?= $note->getText() ?></b><?php }elseif($note->priority==2){?><b style="font-size:12px;"><?= $note->getText() ?></b><?php }else{?><?= $note->getText() ?><?php } ?>
											</td>
											<td style="text-align:left;" <?php if($note->priority==2){?>style="color:#FF0000"<?php }?>>
												<a href="mailto:<?= $email ?>"><?= $contactname ?></a>
											</td>
											<td class="grid-body-right" style="white-space: nowrap;" <?php if($note->priority==2){?>style="color:#FF0000"<?php }?>>
												<?php if (!$this->entity->readonly) : ?>
												<?php

												if (($_SESSION['member']['access_notes'] == 0 ) || ($_SESSION['member']['access_notes'] == 1 && ($note->sender_id == (int)$_SESSION['member_id'])) || $_SESSION['member']['access_notes'] == 2 ) {
												if($note->sender_id == (int)$_SESSION['member_id']  && $note->system_admin == 0 ){ ?>
													<img src="<?= SITE_IN ?>images/icons/strike.png" alt="Discard" title="Discard" width="16" height="16" class="action-icon edit-note" onclick="discardNote(<?= $note->id ?>)"/>	
													<?php }	 if($note->system_admin == 0 && $_SESSION['member']['access_notes'] != 0 ){ ?>  
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
					</div>
				</div>
			</div>
		</div>
	</div>
</div>