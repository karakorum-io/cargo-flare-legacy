<script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script> 

<style>
	.cd-secondary-nav
	{
		position:static;
	}
	.error_reassing
	{
		color:red;
		padding:10px;
	}
	.cd-secondary-nav .is-visible {
		visibility:visible;
		transform:scale(1);
		transition:transform 0.3s, visibility 0s 0s;
	}
	.cd-secondary-nav.is-fixed {
		z-index:9999;
		position:fixed;
		left:auto;
		top:0;
		width:1200px;
		background-color:#f4f4f4;
	}
	a.order-desc
	{
		color:#212529 !important;
	}
	.modal-content .modal-header .close:before
	{
		display:none;
	}
	.history_nos
	{
		margin-top:-12px;
		text-align:right;
		width: 89px;
	}
	#leads_created_info_new_wrapper
	{
		width:100%;
	}
	.dataTables_wrapper table#leads_created_info_new th .kt-checkbox
	{
		margin-top:-10px;
	}
	.dataTables_wrapper table#leads_created_info_new td .kt-checkbox
	{
		margin-top:0 !important;
	}
	table#leads_created_info_new .kt-checkbox > span
	{
		left:50%;
		transform: translateX(-50%);
		-webkit-transform: translateX(-50%);
		-moz-transform: translateX(-50%);
		-ms-transform: translateX(-50%);
	}
</style>

<script type="text/javascript">

	function saveQuotes(email) {
		if ($(".entity-checkbox:checked").length == 0) {
			Swal.fire({
				type: 'error',
				title: 'Oops...',
				html: 'You have no selected items!'
			});
            return false;
        }
        var ajData = [];

        $(".entity-checkbox:checked").each(function(){
           	ajData.push('{"entity_id":"'+$(this).val()+'"}');
        });
        
        if (ajData.length == 0) {
        	alert("You have no quote data");
        	return;
        }

        Processing_show();

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
        		KTApp.unblockPage();
        	}
        });
    }

    function Processing_show() {
    	KTApp.blockPage({
    		overlayColor: '#000000',
    		type: 'v2',
    		state: 'primary',
    		message: 'Processing...'
    	});
    }

    function convertToOrder() {

		if ($(".entity-checkbox:checked").length == 0) {
			Swal.fire('You have no selected items.');
			return false;
		}

		if ($(".entity-checkbox:checked").length > 1) {
			Swal.fire('Error: You may convert one lead at a time.');
			return false;
		}

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
					document.location.href = result.url;
				} else {
					Swal.fire("Can't convert Order. Try again later, please");
				}
			},
			error: function (result) {
				Swal.fire("Can't convert Order. Try again later, please");
			}
		});
	}	

    function reassignOrdersDialog() {

    	if ($(".entity-checkbox:checked").length == 0) {  
    		Swal.fire({
    			type: 'error',
    			title: 'Oops...',
    			text: 'Leads not selected!'
    		});
    	} else {
    		$("#reassignCompanyDiv").modal();
    	}
	}

	function reassignOrders(member) {
		
		var member_id = 0;
		member_id = member;

		if ( member_id == 0 ) {
			Swal.fire('You must select member to assign.');	
			return;
		}

		if ($(".entity-checkbox:checked").length == 0) { 
			Swal.fire('Leads not selected');
			return;
		}
		
		var entity_ids = [];
		$(".entity-checkbox:checked").each(function(){
			entity_ids.push($(this).val());
		});
		
		Processing_show()

		$.ajax({
			type: 'POST',            
			url: '<?= SITE_IN ?>application/ajax/entities.php',            
			dataType: "json",            
			data: {                
				action: 'reassign',                
				assign_id: member_id,                
				entity_ids: entity_ids.join(',')            
			},
			success: function(response) {               
				if (response.success) {
					window.location.reload();
				} else {
					Swal.fire('Reassign failed. Try again later, please.');
					KTApp.unblockPage();
				}
			},
			error: function(response) {
				Swal.fire('Reassign failed. Try again later, please.');
				KTApp.unblockPage();
			} ,
			complete: function (res) {
				KTApp.unblockPage();
			}
		});	
	}
	
	function setAppointment() {
		if ($(".entity-checkbox:checked").length == 0) {            
			alert("Leads not selected");
			return;
		}
	}
	
	$(document).ready(function(){
        $("#app_date").datepicker({
        	dateFormat: "yy-mm-dd",
        	minDate: '+0'
		});
        
        var secondaryNav = $('.cd-secondary-nav');
        if(secondaryNav.length){
        	secondaryNavTopPosition = secondaryNav.offset().top;
        	$(window).on('scroll', function(){
        		if($(window).scrollTop() > secondaryNavTopPosition ) {
        			secondaryNav.addClass('is-fixed');
        		} else {
        			secondaryNav.removeClass('is-fixed');
        		}
        	});
        }
    });

</script>

<div style="display:none" id="notes">notes</div>

<div class="kt-portlet">
	<div class="kt-portlet__body">
		<div class="row">
			<div class="col-lg-12 col-sm-12">
				@pager@
			</div>
		</div>
		<?php
		if ($this->status == Entity::STATUS_CARCHIVED || $this->status == Entity::STATUS_CDEAD){  ?>
		<div class="row">
			<div class="col-lg-12 text-right buttons_div buttion_mar">
				<?= functionButton('Reassign Leads', 'reassignOrdersDialog()' ,'','btn-info btn-sm btn_bright_blue') ?> 

				<?= functionButton('Hold', 'changeStatusLeads('.Entity::STATUS_CONHOLD.')','','btn btn-sm btn_bright_blue') ?>
				<?php 
				if ($this->status == Entity::STATUS_CDEAD){
				?>  
				<?= functionButton('Remove Do Not Call', 'changeStatusLeads('.Entity::STATUS_CACTIVE.')','','btn btn-sm btn-danger') ?>
				<?= functionButton('Cancel', 'changeStatusLeads('.Entity::STATUS_CARCHIVED.')','','btn-dark btn-sm') ?>
				<?php }else{?>
				<?= functionButton('Uncancel', 'changeStatusLeads('.Entity::STATUS_CACTIVE.')','','btn-sm btn-dark') ?>
				<?php }?>
			</div>
		</div>
		<?php
		} else  { ?>
		<div class="row">
			<div class="col-md-12 text-right mb-4">	
			<?= functionButton('Reassign Leads', 'reassignOrdersDialog()','','btn-info btn-sm btn_bright_blue') ?>
			<?= functionButton('Convert to Quotes', 'saveQuotes(0)','','btn-sm btn_bright_blue') ?>
			<?= functionButton('Convert to Order', 'convertToOrder()','','btn-sm btn_bright_blue') ?>
			<?php if($_GET['leads'] == 'cpriority'){?>
			<?= functionButton('Remove Priority', 'changeStatusLeads('.Entity::STATUS_CASSIGNED.')','','btn-sm btn-dark ') ?>
			<?php }else{?>
			<?= functionButton('Make Priority', 'changeStatusLeads('.Entity::STATUS_CPRIORITY.')','','btn-sm btn_bright_blue') ?>
			<?php }?>
			<?php if($_GET['leads'] == 'conhold'){?>
			<?= functionButton('Remove Hold', 'changeStatusLeads('.Entity::STATUS_CASSIGNED.')','','btn btn-sm btn-danger') ?>
			<?php }else{?>
			<?= functionButton('Hold', 'changeStatusLeads('.Entity::STATUS_CONHOLD.')','','btn btn-sm btn_bright_blue') ?>
			<?php }?>
			<?= functionButton('Do Not Call', 'changeStatusLeads('.Entity::STATUS_CDEAD.')','','btn btn-sm btn-danger') ?>
			<?= functionButton('Cancel', 'changeStatusLeads('.Entity::STATUS_CARCHIVED.')','','btn-dark btn-sm') ?>				
			</div>
		</div>
		<?php }?>

		<table class="table table-bordered" id="leads_created_info_new">
			<thead>
				<tr>
					<th>
						<label class="kt-checkbox kt-checkbox--bold kt-checkbox--success  kt-checkbox--all ">
						<input type="checkbox" onchange="if($(this).is(':checked')){ checkAllEntities() }else{ uncheckAllEntities() }">
							<span style="margin-top: -5px;"></span>
						</label>
						<?php if (isset($this->order)) : ?>
							<?=$this->order->getTitle("id", "ID")?>
						<?php else : ?>ID<?php endif; ?> 
					</th>
					<th>
						<?php if($this->status == Entity::STATUS_ARCHIVED){?>
							Received/Created
						<?php }else{?>
							<?php if (isset($this->order)) : ?>
								<?=$this->order->getTitle("assigned_date", "Created")?>
							<?php else : ?>Received<?php endif; ?>
						<?php }?>
					</th>
					<th>Notes</th>
					<th>
					<?php if (isset($this->order)) : ?>
						Shipper
					<?php else : ?>Shipper<?php endif; ?>
					</th>
					<th>Hours of Operations</th>
					<th>Shipment Types</th>
					<th>Origin / Destination</th>
					<th>Units/month</th>
					<th><?php if (isset($this->order)) { ?>
						<?=$this->order->getTitle("avail_pickup_date", "First Avail Date")?>
						<?php }?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if (count($this->entities) == 0): ?>
				<tr>
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
						if($entity['status'] != Entity::STATUS_CACTIVE && $entity['status'] != Entity::STATUS_CASSIGNED && $entity['status'] != Entity::STATUS_CPRIORITY && $entity['status'] != Entity::STATUS_CONHOLD && $entity['status'] != Entity::STATUS_CDEAD){
							$urlDetail = "showcreated";
						}
				?>
				<tr id="lead_tr_<?= $entity['entityid'] ?>" class="<?=($i == 0 ? " " : "")?><?=($entity->duplicate)?' duplicate':''?>">
					<td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check checkbox_1" >
						<?php if (!$entity['readonly']) : ?>										
							<div class="checkbox_wirth" style="width:100%;height:24px;text-align:center;">
								<label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
									<input type="checkbox" value="<?= $entity['entityid'] ?>" class="entity-checkbox">
									<span></span>
								</label>
							</div>
							<div style="width:100%;display:inline-block;"></div>
						<?php endif; ?>
							<div class="history_nos" style="width:100%;margin-top:0;text-align:center;">					
								<a href="<?= SITE_IN ?>application/leads/<?= $urlDetail ?>/id/<?= $entity['entityid'] ?>" class=" kt-badge  kt-badge--info kt-badge--inline kt-badge--pill order_id"><?= $entity['number'] ?></a>
								<br/>
								<a href="<?= SITE_IN ?>application/leads/history/id/<?= $entity['entityid'] ?>" style="margin-top: 4px; ">History</a>
							</div>	
						<?php if($this->status == Entity::STATUS_ARCHIVED){?>
							<?php if($entity['lead_type']==1){?>
							<br/>Created
							<?php } else {?>
							<br/>Imported
							<?php }?>
							<?php }else{
								if (isset($_GET['search_string'])){ 
									print "<br /><b>Status</b><br>";
									
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
								}
							}
						?>      
					</td>
					<td valign="top" style="white-space: nowrap;">
						<span>
							<?php if($entity['status'] == Entity::STATUS_ARCHIVED){?>
								<?php if($entity['lead_type']==1){?>
								<?= date("m/d/y h:i a", strtotime($entity['assigned_date']));?>
								<?php }else{?>
								<?= date("m/d/y h:i a", strtotime($entity['received']));?>  
								<?php }?>
								<?php } else { ?>
								<?= date("m/d/y h:i a", strtotime($entity['assigned_date']));?>
								<?php if ($entity->duplicate) : ?>
									<br/><span style="color: #F00;">Possible Duplicate</span>
								<?php endif; ?>
							<?php }?> 
						</span>
						<br>Assigned to:<br/> 
						<strong class="kt-font-success"><?= $entity['AssignedName'] ?></strong><br />
					</td>
					<td width="5%">
			 			<?php		
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
						if(trim($entity['shipperphone1'])!="") {
							$code     = substr($entity['shipperphone1'], 0, 3);
							$areaCodeStr="";
							$areaCodeRows = $this->daffny->DB->selectRow("*", "app_area_code", "WHERE  AreaCode='".$code."'");
							if (!empty($areaCodeRows)) {
								$areaCodeStr = "<b>".$areaCodeRows['StdTimeZone']."-".$areaCodeRows['statecode']."</b>";
							}
						}

						if(trim($entity['shipperphone2'])!="") {				
							$code = substr($entity['shipperphone2'], 0, 3);
							$areaCodeStr2="";                
							$areaCodeRows2 = $this->daffny->DB->selectRow("*", "app_area_code", "WHERE  AreaCode='".$code."'");
							if (!empty($areaCodeRows2)) {
								$areaCodeStr2 = "<b>".$areaCodeRows2['StdTimeZone']."-".$areaCodeRows2['statecode']."</b>";
							}
						}

						if($entity['shipperphone1_ext']!='') $phone1_ext = " <b>X</b> ".$entity['shipperphone1_ext'];
						if($entity['shipperphone2_ext']!='') $phone2_ext = " <b>X</b> ".$entity['shipperphone2_ext'];
					?>
					<td width="20%">
						<div class="shipper_name">
							<span class="kt-font-bold">
								<?= $entity['shipperfname'] ?> <?= $entity['shipperlname'] ?>
							</span>
						</div>
						<?php if($entity['shippercompany']!=""){?>
							<div class="shipper_company"><b><?= $entity['shippercompany']?></b><br /></div><?php }?>
							<?php if($entity['shipperphone1']!=""){ $phone1 = str_replace($words, $wordsReplace, $entity['shipperphone1']); ?>
							<div class="shipper_number">
								<a href="javascript:void(0);" onclick="customPhoneSms('<?= $phone1; ?>');"><?= formatPhone($entity['shipperphone1']) ?> </a><?php }?>
								<?= $phone1_ext;?> 
								<?= $areaCodeStr;?><br/>
							</div>
							<?php if($entity['shipperphone2']!=""){  $phone2 = str_replace($words, $wordsReplace, $entity['shipperphone2']);  ?>
							<div class="shipper_number">
								<a href="javascript:void(0);" onclick="customPhoneSms('<?= $phone1; ?>');"><?= formatPhone($entity['shipperphone2']) ?> </a><?php }?>
								<?= $phone2_ext;?> 
								<?= $areaCodeStr2;?>
							</div>
							<?php if($entity['shipperemail']!=""){?><a href="mailto:<?= $entity['shipperemail'] ?>">
								<div class="kt-font-bold shipper_email">
									<?= $entity['shipperemail'] ?><br/>
								</div>
							</a><?php }?>
							<div class="shipper_referred"><?php if($entity['referred_by'] != ""){?>
								Source: <b><?= $entity['referred_by'] ?></b><br>
							</div>
						<?php } else {?>
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
						<td class="highlight" title="<?= $entity['shipper_hours'] ?>" style="font-weight:bold;"><?= $entity['shipper_hours'] ?></td>
						<td class="highlight" title="Over $400,000 and below $600,000" style="font-weight:bold;"><?= $shipment_type ?></td>
						<?php
							$o_link = "http://maps.google.com/maps?q=" . urlencode($entity['Orgincity'] . ",+" . $entity['Originstate']);
							$o_formatted = trim($entity['Orgincity'] . ', ' . $entity['Originstate'] . ' ' . $entity['Originzip'], ", ");
							$d_link = "http://maps.google.com/maps?q=" . urlencode($entity['Destinationcity'] . ",+" . $entity['Destinationstate']);
							$d_formatted = trim($entity['Destinationcity'] . ', ' . $entity['Destinationstate'] . ' ' . $entity['Destinationzip'], ", ");
						?>
						<td bgcolor="<?= $bgcolor ?>">
							<span class="kt-font-bold" onclick="window.open('<?= $o_link ?>', '_blank')"><?= $o_formatted ?></span> /<br/>
							<span class="kt-font-bold" onclick="window.open('<?= $d_link ?>')"><?= $d_formatted ?></span><br />
							<?php if (is_numeric($entity['distance']) && ($entity['distance'] > 0)) { ?>
								<?= number_format($entity['distance'], 0, "", "") ?> mi
								<?php $cost = $entity['carrier_pay'] + $entity['pickup_terminal_fee'] + $entity['dropoff_terminal_fee']; ?>
								($ <?= number_format(($cost / $entity['distance']), 2, ".", ",") ?>/mi)
							<?php } ?>
							<span class="kt-font-bold" onclick="mapIt(<?= $entity['entityid'] ?>);">Map it</span>
						</td>
						<td ><?= $entity['shipperunits_per_month'] ?></td>
						<?php if (0) { ?>
						<td style="white-space: nowrap;"  class="grid-body-right">
							<table class="table table-bordered">
								<tr>
									<td width="10"></td>
									<td style="white-space: nowrap;">$ <input type="text" id="lead_tariff_<?=$entity['entityid']?>" class="form-box-textfield decimal" value="<?=number_format($vehicles[0]->tariff, 2, ".", "")?>" style="width: 50px;"/>&nbsp;<span class="small">tariff</span></td>
								</tr>
								<tr>
									<td width="10"><img src="<?=SITE_IN?>images/icons/person.png" alt="Deposit" title="Deposit" width="16" height="16"></td>
									<td style="white-space: nowrap;">$ <input type="text" id="lead_deposit_<?=$entity['entityid']?>" class="form-box-textfield decimal" value="<?=number_format($vehicles[0]->deposit, 2, ".", "")?>" style="width: 50px;"/>&nbsp;<span class="small">deposit</span></td>
								</tr>
							</table>
						</td>
						<?php 
						} elseif ( $this->status == Entity::STATUS_CACTIVE || $this->status == Entity::STATUS_CASSIGNED || $this->status == Entity::STATUS_CPRIORITY || $this->status == Entity::STATUS_CONHOLD || $this->status == Entity::STATUS_CDEAD  || $_GET['etype'] == 4) {?>
						<td class="grid-body-right"  width="13%">
							<span class=""><?= date("m/d/y", strtotime($entity['avail_pickup_date']))?></span>
						</td>
						<?php 
						} else { ?>
						<td>
							<div class="row">
								<div  class="col-lg-12">
									<img src="<?= SITE_IN ?>images/icons/dollar.png" alt="Total Tariff" title="Total Tariff" width="16" height="16"/>
									<?= ("$ " . number_format((float)$entity['total_tariff_stored'], 2, ".", ",")) ?>
									<input type="hidden" id="lead_tariff_<?=$entity['entityid']?>" class="form-box-textfield decimal" value="<?=number_format($entity['total_tariff_stored'], 2, ".", "")?>" />
								</div>

								<div  class="col-lg-12">
									<img src="<?= SITE_IN ?>images/icons/truck.png" alt="Carrier Pay" title="Carrier Pay" width="16" height="16"/>
									<?= ("$ " . number_format((float)$entity['carrier_pay_stored'], 2, ".", ",")) ?>
								</div>


								<div  class="col-lg-12">
									<img src="<?= SITE_IN ?>images/icons/person.png" alt="Deposit" title="Deposit" width="16" height="16"/>
									<?= ("$ " . number_format((float)($entity['total_tariff_stored'] - $entity['carrier_pay_stored']), 2, ".", ",")) ?>
									<input type="hidden" id="lead_deposit_<?=$entity['entityid']?>" class="form-box-textfield decimal" value="<?=number_format($vehicles[0]->deposit, 2, ".", "")?>" />
								</div>
							</div>
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
		<?php } else{ ?>
			<div class="col-lg-12">
				<div class="row">
					<div class="col-lg-12 text-right buttion_mar">
						<?= functionButton('Reassign Leads', 'reassignOrdersDialog()','','btn-sm btn_bright_blue') ?>
						<?= functionButton('Hold', 'changeStatusLeads('.Entity::STATUS_CONHOLD.')','','btn btn-sm btn_bright_blue') ?>
						<?= functionButton('Uncancel', 'changeStatusLeads('.Entity::STATUS_CACTIVE.')','','btn-sm btn-dark') ?>
					</div>
				</div>
			</div>
		<?php } ?>
		</div>
	</div>

	<div class="modal fade" id="reassignCompanyDiv" tabindex="-1" role="dialog" aria-labelledby="reassignCompanyDiv" aria-hidden="true">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Reassign Lead</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<i class="fa fa-times" aria-hidden="true"></i>
					</button>
				</div>
				<div class="modal-body">
					<select class="form-box-combobox" class="company_members" id="company_memberz">
						<option value=""><?php print "Select One"; ?></option>
						<?php foreach($this->company_members as $member) : ?>
							<?php if($member->status == "Active"){
								$activemember .="<option value= '".$member->id."'>" .$member->contactname ."</option>";
							}
							endforeach;
						?>
						<optgroup label="Active User">
							<?php echo $activemember; ?>
						</optgroup>
					</select>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="button" onclick="reassignOrders($('#company_memberz').val())" class="btn btn-primary">Save</button>
				</div> 
			</div>
		</div>
	</div>
</div>

<link href="<?php echo SITE_IN; ?>styles/new/assets/vendors/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
<script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/custom/datatables/datatables.bundle.js" type="text/javascript"></script>

<script type="text/javascript">
	$(document).ready(function() {
		$('#leads_created_info_new').DataTable({
			"lengthChange": false,
			"paging": false,
			"bInfo" : false,
			'drawCallback': function (oSettings) {
				$('#leads_created_info_new_wrapper').children('.row:first').children('.col-md-6:first').html('<div class="form-group row" style="margin-left:0;margin-bottom:0;"><label class="col-form-label">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
				$('#leads_created_info_new_wrapper').children('.row:last').children('.col-md-5').html($('.pager').clone()).addClass('text-left');
				$('#leads_created_info_new_wrapper').children('.row:last').children('.col-md-7').html($('.table_b ').html()).addClass('text-right');
				$('.pages-div-custom').remove();
				
			}
		});
	} );
</script>