<style type="text/css">
	li#track_previous
	{
	 display: none;
	}
	li#track_next
	{
	   display: none;
	}
	table.custom_table_new_info th,
	table.custom_table_new_info td
	{
		border: 0;
	}
	#imgremove .ui-datepicker-trigger
	{
		margin:0 3px 0px 0;
	}
</style>

<?php
/**
 * HTML view file for track and trace functionality
 * 
 * @author Chetu Inc.
 * @version 1.0
 */

/**
 * Controller Variable Management
 */
$tracks = $this->daffny->tpl->tracks;

$oLat = $this->daffny->tpl->oLat;
$oLng = $this->daffny->tpl->oLng;
$dLat = $this->daffny->tpl->dLat;
$dLng = $this->daffny->tpl->dLng;

$route = array();

for ($i = 0; $i < count($tracks); $i++) {
    $route[$i]['lat'] = $tracks[$i]['lat'];
    $route[$i]['lng'] = $tracks[$i]['lng'];
}

$lastPointReached = $route[0];
$lastPointReached = json_encode($lastPointReached);
$lastPointReached = str_replace('"', "", $lastPointReached);

$route = json_encode($route);
$route = str_replace('"', "", $route);
?>

<!-- Loading external JS file-->
<script src="<?= SITE_IN ?>jscripts/track_n_trace.js"></script>
<!-- Loading external CSS file-->
<link rel = "stylesheet" type="text/css" 	href="<?= SITE_IN ?>styles/track_n_trace.css" />

<!-- Adding Order Menus-->
<div style="padding-top:15px;">
    <?php include('order_menu.php'); ?>
</div>
<div id="map-div">
    <center>
        <h3>Loading Map Data...!</h3>
    </center>
</div>

<div class="" id="order-information-div">
	<div class="row">
		<div class="col-4">
	
			<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
				
				<div id="headingOne " class="hide_show">
					<div class="card-title collapsed">
						<h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Carrier Information</h3>
					</div>
				</div>
				
				<div id="carrier__info" style="padding-left: 20px; padding-right: 20px;">
					
					<table class="table custom_table_new_info">
						<tbody>
							<tr>
								<td width="30%"><strong>Account Id</strong></td>
								<td width="4%" align="center"><b>:</b></td>
								<td align="left">
									<a target="_blank"href="/application/accounts/details/id/<?php echo $this->entity->carrier_id ?>">
										<?php echo $this->entity->carrier_id ?>
									</a>
								</td>
							</tr>
							
							<tr>
								<td><strong>MC Number</strong></td>
								<td width="4%" align="center"><b>:</b></td>
								<td align="left">
									<?php echo $this->dispatchSheet->carrier_insurance_iccmcnumber ?>
								</td>
							</tr>
							
							<tr>
								<td><strong>Company</strong></td>
								<td width="4%" align="center"><b>:</b></td>
								<td align="left">
									<?php echo $this->dispatchSheet->carrier_company_name ?>
								</td>
							</tr>
							
							<tr>
								<td><strong>Contact</strong></td>
								<td width="4%" align="center"><b>:</b></td>
								<td align="left">
									<?php echo $this->dispatchSheet->carrier_contact_name ?>
								</td>
							</tr>
							
							<tr>
								<td><strong>Phone 1</strong></td>
								<td width="4%" align="center"><b>:</b></td>
								<td align="left">
									<?php echo formatPhone($this->dispatchSheet->carrier_phone_1); ?><?php echo $phone1_ext ?>
								</td>
							</tr>
							
							<tr>
								<td><strong>Phone 2</strong></td>
								<td width="4%" align="center"><b>:</b></td>
								<td align="left">
									<?php echo formatPhone($this->dispatchSheet->carrier_phone_2); ?><?php echo $phone2_ext ?>
								</td>
							</tr>
							
							<tr>
								<td><strong>Fax</strong></td>
								<td width="4%" align="center"><b>:</b></td>
								<td align="left">
									<?php echo $this->dispatchSheet->carrier_fax ?>
								</td>
							</tr>
							
							<tr>
								<td><strong>Email</strong></td>
								<td width="4%" align="center"><b>:</b></td>
								<td align="left">
									<?php echo $this->dispatchSheet->carrier_email ?>
								</td>
							</tr>
							
							<tr>
								<td><strong>Driver</strong></td>
								<td width="4%" align="center"><b>:</b></td>
								<td align="left">
									<?php echo $this->dispatchSheet->carrier_driver_name ?>
								</td>
							</tr>
							
							<tr>
								<td><strong>Driver Name</strong></td>
								<td width="4%" align="center"><b>:</b></td>
								<td align="left">
									<?php echo formatPhone($this->dispatchSheet->carrier_driver_phone); ?>
								</td>
							</tr>
							
						</tbody>
					</table>
				
				</div>
			</div>
	
		</div>
		
		
		<?php
            $origin = $this->entity->getOrigin();
            $phone1_ext = '';
            $phone2_ext = '';
            $phone3_ext = '';
            $phone4_ext = '';
            if($origin->phone1_ext != '')
            $phone1_ext = " <b>X</b> ".$origin->phone1_ext;
            if($origin->phone2_ext != '')
            $phone2_ext = " <b>X</b> ".$origin->phone2_ext;
            if($origin->phone3_ext != '')
            $phone3_ext = " <b>X</b> ".$origin->phone3_ext;
            if($origin->phone4_ext != '')
            $phone4_ext = " <b>X</b> ".$origin->phone4_ext;
        ?>
		
		<div class="col-4">
		
			<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
				
				<div id="headingOne " class="hide_show">
					<div class="card-title collapsed">
						<h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Pickup Information</h3>
					</div>
				</div>
				
				<div id="pickup__info" style="padding-left: 20px; padding-right: 20px;">
					
					
					<table class="table custom_table_new_info">
						<tbody>
						
							<tr>
								<td width="30%"><strong>Address</strong></td>
								<td width="4%" align="center"><b>:</b></td>
								<td align="left">
									<?php echo $origin->address1; ?>,&nbsp;&nbsp;<?php echo $origin->address2; ?>
								</td>
							</tr>
							
							<tr>
								<td width="30%"><strong>City</strong></td>
								<td width="4%" align="center"><b>:</b></td>
								<td align="left">
									<span class="like-link"onclick="window.open('<?php echo $origin->getLink() ?>')">
										<?= $origin->getFormatted() ?>
									</span>
								</td>
							</tr>
							
							<tr>
								<td width="30%"><strong>Location Type</strong></td>
								<td width="4%" align="center"><b>:</b></td>
								<td align="left">
									<span class="like-link"onclick="window.open('<?php echo $origin->getLink() ?>')">
										<?php echo $origin->location_type; ?>
									</span>
								</td>
							</tr>
							
							<tr>
								<td width="30%"><strong>Hours</strong></td>
								<td width="4%" align="center"><b>:</b></td>
								<td align="left">
									<?php echo $origin->hours; ?>
								</td>
							</tr>
							
						</tbody>
					</table>					
					
				</div>
				
			</div>
						
		</div>
		
		<?php
            $destination = $this->entity->getDestination();
            $phone1_ext ='';
            $phone2_ext ='';
            $phone3_ext ='';
            $phone4_ext ='';
            if($destination->phone1_ext!='')
				$phone1_ext = " <b>X</b> ".$destination->phone1_ext;
            if($destination->phone2_ext!='')
				$phone2_ext = " <b>X</b> ".$destination->phone2_ext;
            if($destination->phone3_ext!='')
				$phone3_ext = " <b>X</b> ".$destination->phone3_ext;
            if($destination->phone4_ext!='')
				$phone4_ext = " <b>X</b> ".$destination->phone4_ext;
        ?>
		
		<div class="col-4">
			
			<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
				
				<div id="headingOne " class="hide_show">
					<div class="card-title collapsed">
						<h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Dropoff Information</h3>
					</div>
				</div>
				
				<div id="carrier__info" style="padding-left: 20px; padding-right: 20px;">
					
					<table class="table custom_table_new_info">
						<tbody>
						
							<tr>
								<td width="30%"><strong>Address</strong></td>
								<td width="4%" align="center"><b>:</b></td>
								<td align="left">
									<?php echo $destination->address1; ?>,&nbsp;&nbsp;<?php echo $destination->address2; ?>
								</td>
							</tr>
						
							<tr>
								<td><strong>City</strong></td>
								<td align="center"><b>:</b></td>
								<td align="left">
									<span class="like-link"onclick="window.open('<?php echo $destination->getLink() ?>')">
										<?php echo $destination->getFormatted() ?>
									</span>
								</td>
							</tr>
						
							<tr>
								<td><strong>Location Type</strong></td>
								<td align="center"><b>:</b></td>
								<td align="left">
									<?php echo $destination->location_type; ?>
								</td>
							</tr>
						
							<tr>
								<td><strong>Hours</strong></td>
								<td align="center"><b>:</b></td>
								<td align="left">
									<?php echo $destination->hours; ?>
								</td>
							</tr>
							
							
						</tbody>
					</table>
					
				</div>
				
			</div>

		</div>
		
	</div>
	
</div>



<div class="add-location-form-div_new order-info_new">
	
	<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
	
		<div id="headingOne " class="hide_show">
			<div class="card-title collapsed">
				<h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Add Carrier Location</h3>
			</div>
		</div>
		
		<div id="add_carrier_location__info" class="pb-4" style="padding-left: 20px; padding-right: 20px;">
		
			<form method="POST" style="width:100%;">        
				<div class="row" id="location-form-table">
				
					<input type="hidden" name="lat" id='lat' value='1.22'>
					<input type="hidden" name="lng" id='lng' value='3.22'>
					
					<div class="col-4">
						<div class="new_form-group">
							<label for="state"><span class="required">*</span>State</label>
							<select name="state" class="form-box-textfield" id="state" required="true">
								<option>--SELECT STATE---</option>
								<option value="AK">Alaska</option>
								<option value="AL">Alabama</option>
								<option value="AR">Arkansas</option>
								<option value="AZ">Arizona</option>
								<option value="CA">California</option>
								<option value="CO">Colorado</option>
								<option value="CT">Connecticut</option>
								<option value="DC">District of Columbia</option>
								<option value="DE">Delaware</option>
								<option value="FL">Florida</option>
								<option value="GA">Georgia</option>
								<option value="HI">Hawaii</option>
								<option value="IA">Iowa</option>
								<option value="ID">Idaho</option>
								<option value="IL">Illinois</option>
								<option value="IN">Indiana</option>
								<option value="KS">Kansas</option>
								<option value="KY">Kentucky</option>
								<option value="LA">Louisiana</option>
								<option value="MA">Massachusetts</option>
								<option value="MD">Maryland</option>
								<option value="ME">Maine</option>
								<option value="MI">Michigan</option>
								<option value="MN">Minnesota</option>
								<option value="MO">Missouri</option>
								<option value="MS">Mississippi</option>
								<option value="MT">Montana</option>
								<option value="NC">North Carolina</option>
								<option value="ND">North Dakota</option>
								<option value="NE">Nebraska</option>
								<option value="NH">New Hampshire</option>
								<option value="NJ">New Jersey</option>
								<option value="NM">New Mexico</option>
								<option value="NV">Nevada</option>
								<option value="NY">New York</option>
								<option value="OH">Ohio</option>
								<option value="OK">Oklahoma</option>
								<option value="OR">Oregon</option>
								<option value="PA">Pennsylvania</option>
								<option value="RI">Rhode Island</option>
								<option value="SC">South Carolina</option>
								<option value="SD">South Dakota</option>
								<option value="TN">Tennessee</option>
								<option value="TX">Texas</option>
								<option value="UT">Utah</option>
								<option value="VA">Virginia</option>
								<option value="VT">Vermont</option>
								<option value="WA">Washington</option>
								<option value="WI">Wisconsin</option>
								<option value="WV">West Virginia</option>
								<option value="WY">Wyoming</option>
							</select>
						</div>
					</div>
					
					<div class="col-4">
						<div class="new_form-group">
							<label for="state"><span class="required">*</span>City</label>
							<input type="text" name="state" class="form-box-textfield" id="state" required="true">
						</div>
					</div>
					
					<div class="col-4">
						<div class="new_form-group">
							<label for="state"><span class="required">*</span>Zip Code</label>
							<input type="text" name="zip_code" class="form-box-textfield" id="zip">
						</div>
					</div>
					
					<div class="col-12">
						<div class="form-group">
							<textarea class="form-box-textfield" id="comment" name="comment" style="height:100px;width:100%"></textarea>
						</div>
					</div>
					
					<div class="col-12 text-right mb-4">
						<input type="submit" class="btn btn-sm btn_bright_blue" name="submit" id="submit" value="Add Location"/>
						<input type="button" class="btn btn-sm btn-dark" onclick="reset_form()" name="reset" id="reset" value="Clear Form"/>
						<a href="<?php echo getLink("orders", "track_n_trace_history", "id", $_GET['id']) ?>">
							<input type="button" class="btn btn-sm btn_light_green" id="view_history" value="See History">
						</a>
					</div>
					
				</div>
			</form>
			
			
			<table id="track" class="table table-bordered">
				<thead>
					<tr>
						<th>Track ID</th>
						<th>Carrier Id</th>
						<th>State</th>
						<th>City</th>
						<th>Zip Code</th>
						<th>Entered By</th>
						<th>Comment</th>
						<th>Date Time</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if (count($tracks) > 0) {
						for ($i = 0; $i < count($tracks); $i++) {
							?>
							<tr id="list-<?php echo $i; ?>">
								<td><?php echo $tracks[$i]['id'] ?></td>
								<td>
									<a href="<?php echo getLink("accounts", "details", "id", $tracks[$i]['carrier_id']); ?>" target="_blank">
										<?php echo $tracks[$i]['carrier_id'] ?>
									</a>
								</td>
								<td id="state-<?php echo $i; ?>"><?php echo $tracks[$i]['state'] ?></td>
								<td id="city-<?php echo $i; ?>"><?php echo $tracks[$i]['city'] ?></td>
								<td id="zip-code-<?php echo $i; ?>"><?php echo $tracks[$i]['zip_code'] ?></td>
								<td><?php echo $tracks[$i]['entered_by_name'] ?></td>
								<td><?php echo $tracks[$i]['entered_by_comment'] ?></td>
								<td><?php echo date("m/d/Y h:i:s a", strtotime($tracks[$i]['created_at'])); ?></td>
								<td align="center">
									<img src="/images/icons/edit.png" title="Edit" class="pointer" onclick="modify_location(<?php echo $tracks[$i]['id'] ?>,<?php echo $tracks[$i]['entity_id'] ?>,<?php echo $i; ?>)" width="16" height="16"/>
									<img src="/images/icons/delete.png" title="Delete" class="pointer" onclick="delete_location(<?php echo $tracks[$i]['id'] ?>,<?php echo $tracks[$i]['entity_id'] ?>,<?php echo $i; ?>)" width="16" height="16"/>
								</td>
							</tr>
							<?php
						}
					} else {
						echo "<tr class='grid-body'>"
						. "<td class='grid-body-left grid-body-right' colspan='9' align='center'>No Track Records Found!</td>"
						. "</tr>";
					}
					?>
				</tbody>
			</table>
			
		</div>
		
	</div>

</div>

<!--notes div-->
<div class="notes-div_new order-info_new">
	
	
	<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
	
		<div id="headingOne " class="hide_show">
			<div class="card-title collapsed">
				<h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Internal Notes</h3>
			</div>
		</div>
		
		<div id="internal_notes__info" class="pb-4" style="padding-left: 20px; padding-right: 20px;">
			
			<div class="form-group">
				<textarea style="100%;height:100px;" class="form-box-textfield" maxlength="1000" id="internal_note"></textarea>
			</div>
			
			<div class="row">
				<div class="col-5">
					<div class="new_form-group">
						<label>Quick Notes</label>
						<select name="quick_notes" class="form-control" id="quick_notes" onchange="addQuickNote();">
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
				<div class="col-5">
					<div class="new_form-group">
						<label>Priority</label>
						<select name="priority_notes" class="form-control" id="priority_notes">
							<option value="1">Low</option>
							<option value="2">High</option>
						</select>  
					</div>
				</div>
				<div class="col-2">
					<div class="form-group">
						<div class="form-box-buttons-new pull-right">
							<?= functionButton('Add Note', 'addInternalNote()') ?>
						</div> 
					</div>
				</div>
			</div>

			<!--Notes Listing UI-->
			<div id="notes-list-div">
				<table class="table table-bordered" id="internal_notes_table">
					<thead>
						<tr>
							<th><a href="/application/orders/show/id/47550/order/created/arrow/asc" class="order">Date</a></th>
							<th width="65%">Note</th>
							<th width="15%">User</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php $notes = $this->notes; ?>
						<?php if (count($notes[Note::TYPE_INTERNAL]) == 0) { ?>
							<tr>
								<td colspan="4" align="center"><i>No notes available.</i></td>
							</tr>
						<?php } else { ?>
							<?php
								foreach($notes[Note::TYPE_INTERNAL] as $note) {
								   
									$sender = $note->getSender();
									$email = $sender->email;
									$contactname = $sender->contactname;
									if ($note->system_admin == 2) {
										$email = "admin@cargoflare.com";
										$contactname = "System";
									}
									if (($_SESSION['member']['access_notes'] == 0 ) || $_SESSION['member']['access_notes'] == 1 || $_SESSION['member']['access_notes'] == 2) { ?>
									<tr>
										<td style="white-space:nowrap;" <?php if($note->priority == 2){ ?> style="color:#FF0000"<?php } ?>>
											<?= $note->getCreated("m/d/y h:i a") ?>
										</td>
										<td id="note_<?= $note->id ?>_text" style=" <?php if($note->discard == 1){ ?>text-decoration: line-through;<?php } ?><?php if($note->priority == 2){ ?>color:#FF0000;<?php } ?>"><?php if($note->system_admin == 1 || $note->system_admin == 2){ ?><b><?= $note->getText() ?></b><?php }elseif($note->priority == 2){ ?><b style="font-size:12px;"><?= $note->getText() ?></b><?php }else{ ?><?= $note->getText() ?><?php } ?></td>
										<td style="text-align: center;" <?php if($note->priority == 2){ ?>style="color:#FF0000"<?php } ?>>
											<a href="mailto:<?= $email ?>"><?= $contactname ?></a>
										</td>
										<td style="white-space: nowrap;" <?php if ($note->priority == 2) { ?>style="color:#FF0000"<?php } ?>>
											<?php
												if (($_SESSION['member']['access_notes'] == 0 ) || ($_SESSION['member']['access_notes'] == 1 && ($note->sender_id == (int) $_SESSION['member_id'])) || $_SESSION['member']['access_notes'] == 2 ) {
												if ($note->sender_id == (int) $_SESSION['member_id'] && $note->system_admin == 0) {
											?>
												<img src="<?= SITE_IN ?>images/icons/strike.png" alt="Discard" title="Discard" width="16" height="16" class="action-icon edit-note" onclick="discardNote(<?= $note->id ?>)"/>	
											<?php }
											if ($note->system_admin == 0 && $_SESSION['member']['access_notes'] != 0) { ?>
												<img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit" width="16" height="16" class="action-icon edit-note" onclick="editInternalNote(<?= $note->id ?>)"/>
												<img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete" width="16" height="16" class="action-icon delete-note" onclick="delInternalNote(<?= $note->id ?>)"/>
											<?php
												}
											}
											?>
										</td>
									</tr>
									<?php }?>
								<?php }?>
						<?php }?>
					</tbody>
				</table>
			</div>
			
		</div>
		
	</div>
	
</div>
<!--notes div functionality UI over-->

<!--JQuery UI dialogue for Notes Editi-->

	<!-- Modal -->
	<div class="modal fade" id="note_edit_form" tabindex="-1" role="dialog" aria-labelledby="note_edit_form_model" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="note_edit_form_model">Edit Internal Note</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<i class="fa fa-times" aria-hidden="true"></i>
					</button>
				</div>
				<div class="modal-body">
					 <textarea style="width: 95%;height:100px;" class="form-box-textarea" name="note_text"></textarea>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-dark btn-sm" data-dismiss="modal" onclick="edit_back()">back</button>
					<button type="button" class=" btn btn_light_green btn-sm" id="updteintermal" onclick="editInternalNote_update(this.value)">update</button>
				</div>
			</div>
		</div>
	</div>






<!-- Modal -->
<div class="modal fade" id="edit-location" tabindex="-1" role="dialog" aria-labelledby="edit-location_modal" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
<div class="modal-content">
	<div class="modal-header">
		<h5 class="modal-title" id="edit-location_modal">Edit Location Information</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		</button>
	</div>
		<div class="modal-body">
		<br>
		<label>State:</label><br>
		<input type="text" name="state" class="form-box-textfield" id="edit-state" required="true">
		<br><br>
		<label>City:</label><br>
		<input type="text" name="city" class="form-box-textfield" id="edit-city" required="true">
		<br><br>
		<label>Zip Code:</label><br>
		<input type="text" name="zip_code" class="form-box-textfield" id="edit-zip-code" required="true">
		<br><br>
		<label>Zip Code:</label><br>
		<input type="text" name="zip_code" class="form-box-textfield" id="edit-comment" required="true">
	     <input type="hidden" id="id_location" value="" >
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancal</button>
		<button type="button" class="btn btn-primary" id="edit_location"  onclick="edit_loction_update(this.value)">Edit Location</button>
	</div>
</div>
</div>
</div>






<div class="location-sugg-div" id="location_suggestion_overlay" style="display: none;"></div>
<div class="location-sugg-div" id="location_suggestion" style="display: none; overflow-y: auto;"></div>
<script>
    /**
     * Detech blur on zipcode while adding
     */
    $("#zip").blur(function () {
        populate_location("", $("#zip").val());
    });

    $("#edit-zip-code").blur(function () {
        populate_location_edit("", $("#edit-zip-code").val());
    });

    /**
     * Geolocation Google API adjustments
     */

    /**
     * Function to initialize the google map and making its starting configurations
     * 
     * @returns void
     */
    function initMap() {

        var directionsService = new google.maps.DirectionsService;
        var directionsDisplay = new google.maps.DirectionsRenderer;
        var trafficLayer = new google.maps.TrafficLayer();

        var track = <?php echo $route; ?>;

        /* Map configurations*/
        var map = new google.maps.Map(document.getElementById('map-div'), {
            zoom: 4,
            center: {lat: 37.090240, lng: -95.712891},
            mapTypeId: 'terrain'
        });

        /* Map adjustments */
        trafficLayer.setMap(map);
        directionsDisplay.setMap(map);
        calculateAndDisplayRoute(directionsService, directionsDisplay);

        /* Path travelled */
        var path = new google.maps.Polyline({
            path: track,
            geodesic: true,
            strokeColor: '#2574a3',
            strokeOpacity: 1.0,
            strokeWeight: 2
        });

        path.setMap(map);

        /* Path remaining */
        var path_left = new google.maps.Polyline({
            path: [<?php echo $lastPointReached; ?>, {lat:<?php echo $dLat; ?>, lng:<?php echo $dLng; ?>}],
            geodesic: true,
            strokeColor: '#CB3E22',
            strokeOpacity: 1.0,
            strokeWeight: 2
        });

        path_left.setMap(map);

        /**
         * Plotting markers on the map
         */
        for (var i = 0; i < track.length; i++) {

            var label = '';
            var index = 0;
            var latitude = track[i].lat;
            var longitude = track[i].lng;
            if (i != track.length) {
                var markers = new google.maps.Marker({
                    position: {lat: latitude, lng: longitude},
                    map: map,
                    label: label[index++ % label.length],
                    title: 'Checkpoint '
                });
            }
        }
    }

    /**
     * Function to calculate and dispaly route on the map between 2 points
     * 
     * @param  directionsService
     * @param  directionsDisplay
     * @returns void     
     */
    function calculateAndDisplayRoute(directionsService, directionsDisplay) {
        directionsService.route({
            origin: {lat: <?php echo $oLat; ?>, lng: <?php echo $oLng; ?>},
            destination: {lat: <?php echo $dLat; ?>, lng: <?php echo $dLng; ?>},
            travelMode: 'DRIVING'
        }, function (response, status) {
            if (status === 'OK') {
                directionsDisplay.setDirections(response);
            } else {
                window.alert('GOOGLE API ERROR :  ' + status);
            }
        });
    }

    /**
     * disable submit button on click
     */
    $("#submit").click(function () {
    	  console.log("DDDsubmit")
        if (($("#zip").val() == "") || ($("#city").val() == "") || ($("#state").val() == "")) {
            $("#submit").attr("disable", "true");
        }
    });
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB6dx80YTn7l6imjRElosj-yAH7LsXBmrU&callback=initMap">
</script>
<script type="text/javascript">
    var busy = false;
    function updateInternalNotes(data) {
        var rows = "";

        for (i in data) {

            var email = data[i].email;
            var contactname = data[i].sender;

            if (data[i].system_admin == 2) {
                email = "admin@freightdragon.com";
                contactname = "FreightDragon";
            }
            if ((data[i].access_notes == 0)
                    || data[i].access_notes == 1
                    || data[i].access_notes == 2
                    )
            {

                var discardStr = '';
                if (data[i].discard == 1)
                    discardStr = ' style="text-decoration: line-through;" ';

                if (data[i].system_admin == 1 || data[i].system_admin == 2)
                {
                    rows += '<tr class="grid-body"><td style="white-space:nowrap;" class="grid-body-left" >' + data[i].created + '</td><td id="note_' + data[i].id + '_text"  ' + discardStr + '><b>' + decodeURIComponent(data[i].text) + '</b></td><td>';
                } else if (data[i].priority == 2)
                    rows += '<tr class="grid-body"><td class="grid-body-left" >' + data[i].created + '</td><td id="note_' + data[i].id + '_text"  ' + discardStr + '><b style="font-size:12px;color:red;">' + decodeURIComponent(data[i].text) + '</b></td><td>';
                else
                    rows += '<tr class="grid-body"><td class="grid-body-left">' + data[i].created + '</td><td id="note_' + data[i].id + '_text"  ' + discardStr + '>' + decodeURIComponent(data[i].text) + '</td><td>';



                rows += '<a href="mailto:' + email + '">' + contactname + '</a></td><td style="white-space: nowrap;" class="grid-body-right"  >';

<?php //if (!$this->entity->readonly) :  ?>

                if ((data[i].access_notes == 0) ||
                        (data[i].access_notes == 1 && (data[i].sender_id == data[i].memberId))
                        || data[i].access_notes == 2
                        )
                {

                    if (data[i].sender_id == data[i].memberId && data[i].system_admin == 0)
                        rows += '<img src="<?= SITE_IN ?>images/icons/strike.png" alt="Discard" title="Discard" width="16" height="16" class="action-icon edit-note" onclick="discardNote(' + data[i].id + ')"/>';

                    if (data[i].system_admin == 0 && data[i].access_notes != 0)
                    {

                        // rows += '<img src="<?= SITE_IN ?>images/icons/strike.png" alt="Discard" title="Discard" width="16" height="16" class="action-icon edit-note" onclick="discardNote('+data[i].id+')"/>';
                        rows += '<img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit" width="16" height="16" class="action-icon edit-note" onclick="editInternalNote(' + data[i].id + ')"/>';

                        rows += '<img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete" width="16" height="16" class="action-icon delete-note" onclick="delInternalNote(' + data[i].id + ')"/>';
                    }
                }
            }
<?php /* else : ?>rows += '&nbsp;';<?php endif; */ ?>
            rows += '</td></tr>';
        }

        $("#internal_notes_table tbody").html(rows);
    }
    function addQuickNote() {
        var textOld = $("#internal_note").val();

        var str = textOld + " " + $("#quick_notes").val();
        $("#internal_note").val(str);
    }
    function addInternalNote() {
        if (busy)
            return;
        busy = true;
        var text = $.trim($("#internal_note").val());
        var priority = $.trim($("#priority_notes").val());
        if (text == "")
            return;
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
            success: function (result) {
                if (result.success == true) {
                    updateInternalNotes(result.data);
                } else {
                    $("#internal_note").val(text);
                    swal.fire("Can't save note. Try again later, please");
                }
                busy = false;
                //location.reload();
            },
            error: function (result) {
                $("#internal_note").val(text);
                swal.fire("Can't save note. Try again later, please");
                busy = false;
            }
        });
    }
    function delInternalNote(id) {

		Swal.fire({
		title: 'Are you sure?',
		text: "You won't be able to revert this!",
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Yes, delete it!'
		}).then((result) => {
		if (result.value) {

			if(busy)
				    return;
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
				    success: function (result) {
				        if (result.success == true) {
				        	console.log()
				            updateInternalNotes(result.data);
				        } else {
				            swal.fire("Can't delete note. Try again later, please");
				        }
				        busy = false;
				    },
				    error: function (result) {
				        swal.fire("Can't delete note. Try again later, please");
				        busy = false;
				    }
				});

		     }
		})

    }
    function editInternalNote(id)
	{
		var text = $.trim($("#note_" + id + "_text").text());
		$("#note_edit_form textarea").val(text);
		$("#updteintermal").val(id);
		$("#note_edit_form").modal()
	}

    function edit_back()
    {
		busy = false;
	    $("#note_edit_form").modal('hide');
    }


    function editInternalNote_update(id)
    {

	    var text = $.trim($("#note_" + id + "_text").text());
		if ($("#note_edit_form textarea").val() == text) {
		     $("#note_edit_form").modal('hide');
		} else {
		    if (busy)
		        return;
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
		        success: function (result) {
		            if (result.success == true) {
		                updateInternalNotes(result.data);
		                $("#note_edit_form").modal('hide');
		            } else {
		                swal.fire("Can't save note. Try again later, please");
		            }
		            busy = false;
		        },
		        error: function (result) {
		            swal.fire("Can't save note. Try again later, please");
		            busy = false;
		        }
		    });
		  }

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
            success: function (result) {
                if (result.success == true) {
                    updateInternalNotes(result.data);
                } else {
                    swal.fire("Can't discard note. Try again later, please");
                }
                busy = false;
            },
            error: function (result) {
                swal.fire("Can't discard note. Try again later, please");
                busy = false;
            }
        });
    }
    function cancelDispatchSheet(dispatch_id) {
        var entity_id = '<?php echo $_GET['id']; ?>';
        $.ajax({

            type: "POST",

            url: BASE_PATH + "application/ajax/dispatch.php",

            dataType: 'json',

            /* chetu added code */
            data: {
                action: 'cancel',
                id: dispatch_id,
                entity_id: entity_id
            },

            success: function (res) {

                if (res.success) {

                    document.location.reload();

                } else {

                    swal.fire("Can't cancel Dispatch Sheet");

                }

            }

        });
    }
    function editCarrier(dispatch_id)  {
        $("#carrier_value").hide();
        $("#carrier_edit").show();
    }
    function cancelCarrier(dispatch_id)  {
        $("#carrier_value").show();
        $("#carrier_edit").hide();
    }
    function updateCarrier(dispatch_id)  {
        $.ajax({
            type: "POST",
            url: BASE_PATH + "application/ajax/dispatch.php",
            dataType: 'json',
            data: {

                action: 'editcarrier',
                carrier_company_name: $("#carrier_company_name").val(),
                carrier_contact_name: $("#carrier_contact_name").val(),
                carrier_phone_1: $("#carrier_phone_1").val(),
                carrier_phone_2: $("#carrier_phone_2").val(),
                carrier_fax: $("#carrier1_fax").val(),
                carrier_email: $("#carrier1_email").val(),
                hours_of_operation: $("#hours_of_operation").val(),
                carrier_driver_name: $("#carrier_driver_name").val(),
                carrier_driver_phone: $("#carrier1_driver_phone").val(),
                id: dispatch_id
            },
            success: function (res) {
                if (res.success) {
                    document.location.reload();
                } else {
                    swal.fire("Can't cancel Dispatch Sheet");
                }
            }
        });
    }
</script>
<script type="text/javascript">
    $(document).ready(function() {
     $('#track').DataTable();
    });
</script>