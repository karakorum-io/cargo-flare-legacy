<style type="text/css">
	.shipper_detail {
		text-align: left;
		font-size: 15px;
		color: #222;
		height: 40px;
		line-height: 40px;
		padding-left: 15px;
		background-color: #f7f8fa;
		border-bottom: 1px solid #ebedf2;
	}

	table.custom_table_new_info th,
	table.custom_table_new_info td {
		border: 0;
	}

	h3.details {
		padding: 22px 0 0;
		width: 100%;
		font-size: 20px;
	}

	table.table.table-bordered th {
		font-size: 12px;
	}
</style>

<script type="text/javascript">
	function addQuickNote() {
		var textOld = $("#internal_note").val();
		var str = textOld + " " + $("#quick_notes").val();
		$("#internal_note").val(str);
	}

	function maildivnew_send() {
		$.ajax({
			url: BASE_PATH + 'application/ajax/entities.php',
			data: {
				action: "emailQuoteNewSend",
				form_id: $('#form_id').val(),
				entity_id: <?= $this->entity->id ?>,
				mail_to: $('#mail_to_new').val(),
				mail_subject: $('#mail_subject_new').val(),
				mail_body: $('#mail_body_new').val()
			},
			type: 'POST',
			dataType: 'json',
			beforeSend: function() {
				if (!validateMailFormNew()) {
					return false;
				} else {

				}
			},
			success: function(response) {
				if (response.success == true) {
					$("#maildivnew").hide();
					clearMailForm();
				}
			},
			complete: function() {
				// $("body").nimbleLoader("hide");
			}
		});
	}
</script>

<div class="modal fade" id="note_edit_form" tabindex="-1" role="dialog" aria-labelledby="note_edit_form_modal" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="note_edit_form_modal">Edit Internal Note</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				</button>
			</div>
			<div class="modal-body">
				<textarea class="form-box-textarea form-control" name="note_text"></textarea>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancal</button>
				<button type="button" class="btn btn-primary" id="note_edit_form_save" value="save" onclick="editInternalNote_send(this.value)">Save</button>
			</div>
		</div>
	</div>
</div>

<?php 
	include('quote_menu.php');  

	$assigned = $this->entity->getAssigned();
	$shipper = $this->entity->getShipper();
	$origin = $this->entity->getOrigin();
	$destination = $this->entity->getDestination();
	$vehicles = $this->entity->getVehicles();
	$notes = $this->notes; 
	$origin = $this->entity->getOrigin();
	$destination = $this->entity->getDestination();
?>

<div class="row">
	<div class="col-8">
		<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5 mt-3">
			<div class="hide_show">
				<h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Quote Information</h3>
			</div>
			<div id="shipper_information_info_1" class="pt-3" style="padding-left:20px;padding-right:20px;">
				<div class="row">
					<div class="col-12 col-sm-6">
						<div>
							<?php
							if (trim($shipper->phone1) != "") {
								$arrArea = array();
								$arrArea = explode(")", formatPhone($shipper->phone1));

								$code     = str_replace("(", "", $arrArea[0]);
								$areaCodeStr = "";
								//print "WHERE  AreaCode='".$code."'";
								$areaCodeRows = $this->daffny->DB->selectRow("*", "app_area_code", "WHERE  AreaCode='" . $code . "'");
								if (!empty($areaCodeRows)) {
									$areaCodeStr = "<b>" . $areaCodeRows['StdTimeZone'] . "-" . $areaCodeRows['statecode'] . "</b>";
								}
							}
							?>
							<table class="table custom_table_new_info">
								<tbody>
									<tr>
										<th><strong>Name</strong></th>
										<td align="center" class=""><b>:</b></td>
										<td align="left">
											<?= $shipper->fname ?> <?= $shipper->lname ?>
										</td>
									</tr>
									<tr>
										<th><strong>Company</strong></th>
										<td align="center" class=""><b>:</b></td>
										<td><?= $shipper->company; ?></td>
									</tr>
									<tr>
										<th><strong>Email</strong></th>
										<td align="center" class=""><b>:</b></td>
										<td><a href="mailto:<?= $shipper->email ?>"><?= $shipper->email ?></a></td>
									</tr>
									<tr>
										<th><strong>Phone</strong></th>
										<td align="center" class=""><b>:</b></td>
										<td><?= formatPhone($shipper->phone1); ?><?= $areaCodeStr; ?></td>
									</tr>
									<tr>
										<th><strong>Mobile</strong></th>
										<td align="center" class=""><b>:</b></td>
										<td><?= $shipper->mobile; ?></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="col-12 col-sm-6">
						<table class="table custom_table_new_info">
							<tbody>
								<tr>
									<th><strong>Fax</strong></th>
									<td align="center" class=""><b>:</b></td>
									<td><?= $shipper->fax; ?></td>
								</tr>
								<tr>
									<th><strong>Assigned to</strong></th>
									<td align="center" class=""><b>:</b></td>
									<td><?= $assigned->contactname ?></td>
								</tr>
								<tr>
									<th><strong>Referred by</strong></th>
									<td align="center" class=""><b>:</b></td>
									<td> <?= $this->entity->referred_id ?  getRefererByID($this->daffny->DB, $this->entity->referred_id)['name'] :  getLeadSourceByID($this->daffny->DB, $this->entity->source_id)[0]['company_name']?> </td>
								</tr>
								<tr>
									<th><strong>Ship Via: </strong></th>
									<td align="center" class=""><b>:</b></td>
									<td align="left" style="line-height:15px;"><span style="color:red;weight:bold;"><?= $this->entity->getShipVia() ?></span></td>
								</tr>

								<?php if (is_numeric($this->entity->distance) && ($this->entity->distance > 0)) : ?>
									<tr>
										<th><strong>Mileage: </strong></th>
										<td align="center" class=""><b>:</b></td>
										<td>
											<?= number_format($this->entity->distance, 0, "", "") ?> mi ($
											<?= number_format(($this->entity->getCarrierPay(false) / $this->entity->distance), 2, ".", ",") ?>/mi)&nbsp;&nbsp;(<span class='red' onclick="mapIt(<?= $this->entity->id ?>);">MAP IT</span>)</strong>
										</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div id="InternalNote1" style="background:#fff;border:1px solid #ebedf2; margin-bottom:40px;">
					<div class="hide_show" id="headingOne">
						<h3 class="shipper_detail">Pickup Information</h3>
						<div id="pickup_infomation_info_new_2" style="padding-left:20px;padding-right:20px;">
							<table class="table custom_table_new_info">
								<!-- <tr>
									<td width="35%"><strong>Address</strong></td>
									<td width="4%" align="center" style="line-height:15px;"><b>:</b></td>
									<td align="left" style="line-height:15px;"><?= $origin->address1; ?>&nbsp;&nbsp;<?= $origin->address2; ?></td>
								</tr> -->
								<tr>
									<td style="line-height:15px;"><strong>Location</strong></td>
									<td width="4%" align="center" style="line-height:15px;"><b>:</b></td>
									<td align="left" style="line-height:15px;">
										<span class="like-link" onclick="window.open('<?= $origin->getLink() ?>')"><?= $origin->getFormatted() ?></span>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div id="InternalNote1" style="background:#fff;border:1px solid #ebedf2; margin-bottom:40px;">
					<div class="hide_show" id="headingOne">
						<h3 class="shipper_detail">Dropoff Information</h3>
						<div id="pickup_infomation_info_new_2" style="padding-left:20px;padding-right:20px;">
							<table class="table custom_table_new_info">
								<!-- <tr>
									<td width="35%"><strong>Address</strong></td>
									<td width="4%" align="center" style="line-height:15px;"><b>:</b></td>
									<td align="left" style="line-height:15px;"><?= $destination->address1; ?>&nbsp;&nbsp;<?= $destination->address2; ?></td>
								</tr> -->
								<tr>
									<td style="line-height:15px;"><strong>Location</strong></td>
									<td width="4%" align="center" style="line-height:15px;"><b>:</b></td>
									<td align="left" style="line-height:15px;">
										<span class="like-link" onclick="window.open('<?= $destination->getLink() ?>')"><?= $destination->getFormatted() ?></span>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
		
		<div id="headingOne"  class="hide_show">
			<div class="card-title collapsed">
				<h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Vehicle(s) Information</h3>
			</div>
		</div>
		
		<div id="vehicle_info_new" class="collapse show"  style="padding-left:20px;padding-right:20px;padding-bottom:10px;">
			<div>
				<table class="table table-bordered">
					<thead>
						<tr>
							<th>S.No.</th>
							<th><?= Year ?></th>
							<th><?= Make ?></th>
							<th><?= Model ?></th>
							<th><?= Inop ?></th>
							<th><?= Type ?></th> 
							<th><?= Vin# ?></th>
							<th><?= Lot# ?></th>
							<th>Carrier Fee</th>
							<th>Loading Fee</th>
							<th>Total Cost</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						$vehiclecounter = 0;
						foreach($vehicles as $vehicle) : 
						$vehiclecounter = $vehiclecounter + 1;
						?>					
						<tr>
							<td><?= $vehiclecounter ?></td>
							<td><?= $vehicle->year ?></td>
							<td><?= $vehicle->make ?></td>
							<td><?= $vehicle->model ?></td> 
							<td><?php print $vehicle->inop==0?"No":"Yes"; ?></td>
							<td><?= $vehicle->type ?></td>
							<td> <?php print $vehicle->vin ?></td>
							<td> <?php print $vehicle->lot ?></td>
							<td><?= $vehicle->carrier_pay ?></td>
							<td><?= $vehicle->deposit ?></td>
							<td><?= $vehicle->tariff ?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
		<?php $notes = $this->notes; ?>
	<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
		
		<div id="headingOne " class="hide_show">
			<div class="card-title collapsed" >
				<h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Internal Notes</h3>
			</div>
		</div>
		
		<div id="internal_notes_info" class="collapse show" aria-labelledby="headingOne" style="padding-left:20px;padding-right:20px;padding-bottom:10px;">
			
			<div class="form-group">
				<textarea class="form-control form-box-textarea " maxlength="1000" id="internal_note"></textarea>
			</div>
			
			<div class="row">             
				<div class="col-5">
					<div class="new_form-group_4">
						<label>Quick Notes</label>
						<select class="form-control"  name="quick_notes" id="quick_notes" onchange="addQuickNote();">
							<option value="">--Select--</option>
							<option value="Document Upload: Release(s) attached.">Document Upload: Release(s) attached.</option>
							<option value="Document Upload: Gate Pass(es) attached.">Document Upload: Gate Pass(es) attached.</option>
							<option value="Document Upload: Dock Receipt attached.">Document Upload: Dock Receipt attached.</option>
							<option value="Document Upload: Photos attached.">Document Upload: Photos attached.</option>
							<option value="Phoned: Bad Mobile.">Phoned: Bad Number.</option>
							<option value="Phoned: No Voicemail.">Phoned: No Voicemail.</option>
							<option value="Phoned: Left Message.">Phoned: Left Message.</option>
							<option value="Phoned: No Answer.">Phoned: No Answer.</option>
							<option value="Phoned: Spoke to Customer.">Phoned: Spoke to Customer.</option>
							<option value="Phoned: Spoke to carrier about pick-up.">Phoned: Spoke to carrier about pick-up.</option>
							<option value="Phoned: NSpoke to carrier about drop-off.">Phoned: Spoke to carrier about drop-off.</option>
							<option value="Phoned: Customer requested carrier info.">Phoned: Customer requested carrier info.</option>
							<option value="Phoned: Customer reported  damage.">Phoned: Customer reported damage.</option>
							<option value="Phoned: Customer canceled, late pick-up.">Phoned: Customer canceled, late pick-up.</option>
							<option value="Phoned: Customer canceled, no reason given.">Phoned: Customer canceled, no reason given.</option>
							<option value="Phoned: Customer canceled, through e-Mail.">Phoned: Customer canceled, through e-Mail.</option>
							<option value="Phoned: Customer was happy with the transport.">Phoned: Customer was happy with the transport.</option>
							<option value="Phoned: Customer was un-happy with the transport.">Phoned: Customer was un-happy with the transport.</option>
							<option value="Phoned: Customer want a refund.">Phoned: Customer want's a refund.</option>
							<option value="Phoned: Not Interested.">Phoned: Not Interested.</option>
							<option value="Phoned: Do Not Call.">Phoned: Do Not Call.</option>
						</select>
					</div>
				</div>
				
				<div class="col-4">
					<div class="new_form-group_4">
						<label>Priority</label>
						<select  name="priority_notes"  class="form-control" id="priority_notes" >
							<option value="1">Low</option>
							<option value="2">High</option>
						</select>
					</div>
				</div>
				
				<div class="col-3">
					<div class="text-right">
						<?= functionButton('Add Note', 'addInternalNote()','','btn-sm btn_dark_green') ?>
					</div>
				</div>
				
			</div>
			
			
			<!---<table class="table custom_table_new_info" id="internal_notes_table">--->
			    <table class="table table-bordered" id="internal_notes_table">
				<thead>
					<tr>
						<td ><?= $this->order->getTitle('created', 'Date') ?></td>
						<td>Note</td>
						<td>User</td>
						<td>Action</td>	
					</tr>
				</thead>
				<tbody>
					<?php if (count($notes[Note::TYPE_INTERNAL]) == 0) : ?>
					<tr >
						<td colspan="4"><i>No notes available.</i></td>
					</tr>
					<?php else : ?>
					<?php foreach($notes[Note::TYPE_INTERNAL] as $note) : ?>
					<?php
					$sender = $note->getSender();

					$email = $sender->email;
					$contactname = $sender->contactname;
					
					if($note->system_admin == 2){
						$email = "admin@cargoflare.com";
						$contactname = "System";
					}

					if (($_SESSION['member']['access_notes'] == 0 )
					|| $_SESSION['member']['access_notes'] == 1
					|| $_SESSION['member']['access_notes'] == 2
					)
					{
					?>
					<tr >
						<td style="white-space:nowrap;"  class="grid-body-left" <?php if($note->priority == 2){ ?> style="color:#FF0000"<?php } ?>><?= $note->getCreated("m/d/y h:i a") ?></td>
						<td id="note_<?= $note->id ?>_text" style=" <?php if($note->discard == 1){ ?>text-decoration: line-through;<?php } ?><?php if($note->priority == 2){ ?>color:#FF0000;<?php } ?>"><?php if($note->system_admin == 1 || $note->system_admin == 2){ ?><b><?= $note->getText() ?></b><?php }elseif($note->priority == 2){ ?><b style="font-size:12px;"><?= $note->getText() ?></b><?php }else{ ?><?= $note->getText() ?><?php } ?></td>
						<td style="text-align: center;" <?php if($note->priority == 2){ ?>style="color:#FF0000"<?php } ?>><a href="mailto:<?= $email ?>"><?= $contactname ?></a></td>
						<td class="grid-body-right" style="white-space: nowrap;" <?php if($note->priority == 2){ ?>style="color:#FF0000"<?php } ?>>
							<?php //if (!$this->entity->readonly) :  ?>


							<?php
							if (($_SESSION['member']['access_notes'] == 0 ) ||
							($_SESSION['member']['access_notes'] == 1 && ($note->sender_id == (int) $_SESSION['member_id']))
							|| $_SESSION['member']['access_notes'] == 2
							)
							{

							if($note->sender_id == (int) $_SESSION['member_id'] && $note->system_admin == 0 ){
							?>
							<img src="<?= SITE_IN ?>images/icons/strike.png" alt="Discard" title="Discard" width="16" height="16" class="action-icon edit-note" onclick="discardNote(<?= $note->id ?>)"/>	
							<?php
							}

							if($note->system_admin == 0 && $_SESSION['member']['access_notes'] != 0 ){
							?>  



							<img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit" width="16" height="16" class="action-icon edit-note" onclick="editInternalNote(<?= $note->id ?>)"/>

							<img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete" width="16" height="16" class="action-icon delete-note" onclick="delInternalNote(<?= $note->id ?>)"/>

							<?php
							}

							}
							?>

							<?php //else :  ?>&nbsp;<?php //endif;  ?>
						</td>
					</tr>

					<?php } ?>
					<?php endforeach; ?>
				   <?php endif; ?>
				</tbody>
			</table>
			
		</div>
		
	</div>
	</div>
	<div class="col-4">
		<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5 mt-3">
			<div class="hide_show ">
				<h3 class="shipper_detail">Payment Terms</h3>
			</div>
			<div id="payment_terms_info_1" class="pt-3 pb-3" style="padding-left:20px;padding-right:20px;">
				<table class="table custom_table_new_info">
					<tbody>
						<tr>
							<th><strong>Total Tariff amount</strong></th>
							<td align="center" class=""><b>:</b></td>
							<td><?= $this->entity->getTotalTariff() ?></td>
						</tr>
						<tr>
							<th><strong>To Carrier</strong></th>
							<td align="center" class=""><b>:</b></td>
							<td><?= $this->entity->getCarrierPay() ?></td>
						</tr>
						<tr>
							<th><strong>Deposit amount</strong></th>
							<td align="center" class=""><b>:</b></td>
							<td><?= $this->entity->getTotalDeposit() ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5 mt-3">
			<div class="hide_show">
				<h3 class="shipper_detail">Dates</h3>
			</div>
			<div id="dates_payment_info_1" class="pt-3 pb-3" style="padding-left:20px;padding-right:20px;">
				<strong>Estimated Ship Date :</strong> <?= $this->entity->getShipDate("m/d/y") ?>
			</div>
		</div>
		<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5 mt-3">
			<div class="hide_show">
				<h3 class="shipper_detail">Status</h3>
			</div>
			<div id="new_quote_terms_info_1" class="pt-3 pb-3" style="padding-left:20px;padding-right:20px;">
				<strong>New quote: </strong><?php echo $this->entity->getQuoted("m/d/y h:i a") ?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	var busy = false;

	function addInternalNote() {
		if (busy) return;
		busy = true;

		var text = $.trim($("#internal_note").val());
		var priority = $.trim($("#priority_notes").val());

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
				notes_type: <?= Note::TYPE_INTERNAL ?>,
				priority: priority
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

	function editInternalNote(id) {

		var text = $.trim($("#note_" + id + "_text").text());
		$("#note_edit_form textarea").val(text);
		$("#note_edit_form_save").val(id);

		$("#note_edit_form").modal();

	}

	function editInternalNote_send(id) {

		var text = $.trim($("#note_" + id + "_text").text());
		if ($("#note_edit_form textarea").val() == text) {
			$("#note_edit_form textarea").val(text);
			//("#note_edit_form").modal('hide');
			$('#note_edit_form').modal('hide');
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

						$('#note_edit_form').modal('hide');
					} else {
						Swal.fire("Can't save note. Try again later, please");
					}
					busy = false;
				},
				error: function(result) {
					Swal.fire("Can't save note. Try again later, please");
					busy = false;
				}
			});
		}

	}

	function updateInternalNotes(data) {
		var rows = "";
		for (i in data) {

			var email = data[i].email;
			var contactname = data[i].sender;

			if (data[i].system_admin == 1) {
				email = "admin@cargoflare.com";
				contactname = "CargoFlare";
			}
			if ((data[i].access_notes == 0) ||
				data[i].access_notes == 1 ||
				data[i].access_notes == 2
			) {
				rows += '<tr class="grid-body"><td class="grid-body-left">' + data[i].created + '</td><td id="note_' + data[i].id + '_text">' + decodeURIComponent(data[i].text) + '</td><td>';
				rows += '<a href="mailto:' + email + '">' + contactname + '</a></td><td style="white-space: nowrap;" class="grid-body-right">';

				<?php //if (!$this->entity->readonly) : 
				?>

				if ((data[i].access_notes == 0) ||
					(data[i].access_notes == 1 && (data[i].sender_id == data[i].memberId)) ||
					data[i].access_notes == 2
				) {


					if (data[i].system_admin == 0 && data[i].access_notes != 0) {

						rows += '<img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit" width="16" height="16" class="action-icon edit-note" onclick="editInternalNote(' + data[i].id + ')"/>';

						rows += '<img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete" width="16" height="16" class="action-icon delete-note" onclick="delInternalNote(' + data[i].id + ')"/>';
					}
				}
			}
			<?php /*else : ?>rows += '&nbsp;';<?php endif;*/ ?>
			rows += '</td></tr>';
		}

		$("#internal_notes_table tbody").html(rows);
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
</script>