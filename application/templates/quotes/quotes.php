<div class="modal fade" id="maildivnew" tabindex="-1" role="dialog" aria-labelledby="maildivnew_modal" aria-hidden="true">
<div class="modal-dialog" role="document">
<div class="modal-content">
	<div class="modal-header">
		<h5 class="modal-title" id="maildivnew_modal">Email message</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		</button>
	</div>
	<div class="modal-body">
     <div class="row">
     	<div class="col-12">
     		<div class="form-group">
     			@mail_to_new@
     		</div>
     	</div>	
     </div>


      <div class="row">
     	<div class="col-12">
     		<div class="form-group">
              @mail_subject_new@
     		</div>
     	</div>
     </div>


      <div class="row">
     	<div class="col-12">
     		<div class="form-group">
     			@mail_body_new@
     		</div>
     	</div>
     </div>


		
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		<button type="button" class="btn btn-primary" onclick="maildivnew()">Submit</button>
	</div>
</div>
</div>
</div>


<div class="modal fade" id="reassignCompanyDiv" tabindex="-1" role="dialog" aria-labelledby="reassignCompanyDiv_modal" aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="reassignCompanyDiv_modal">Reassign Quote</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i class="fa fa-times" aria-hidden="true"></i>
				</button>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<select class="form-box-combobox form-control " id="company_members">
						<option value=""><?php print "Select One"; ?></option>
						<?php foreach($this->company_members as $member) : ?>
						<?php if($member->status == "Active"){?>
						<option value="<?= $member->id ?>"><?= $member->contactname ?></option>
						<?php }?>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancal</button>
				<button type="button" id="get_member_id" class="btn btn-primary" value="" onclick="reassignQuotes(this.value)" >Submit</button>
			</div>
		</div>
	</div>
</div>
<!--end::Modal-->

<script type="text/javascript">
	$(document).ready(function(){
		$("#followup_when").datepicker({
			minDate: '+1'
		});
	});
	function sendFollowUp() {
		if ($(".entity-checkbox:checked").length == 0) {
			Swal.fire("You have no selected quotes.");
			return;
		}
		if ($("#followup_when").val() == "") {
			Swal.fire("You must enter follow-up date.");
			$("#followup_when").focus();
			return;
		}
		var quote_ids = [];
		$(".entity-checkbox:checked").each(function(){
			quote_ids.push($(this).val());
		});
		$.ajax({
			type: "POST",
			url: '<?= SITE_IN ?>application/ajax/entities.php',
			dataType: 'json',
			data: {
				action: 'followup',
				quote_ids: quote_ids.join(','),
				followup_type: $("#followup_type").val(),
				followup_when: encodeURIComponent($("#followup_when").val())
			},
			success: function(response) {
				if (response.success != true) {
					swal.fire("Can't save follow-up. Try again later, plaese");
				} else {
					swal.fire("Follow-up saved.");
				}
			},
			error: function(response) {
				swal.fire("Can't save follow-up. Try again later, plaese");
			}
		});
	}

function emailSelectedQuoteForm() {

            
		if ($(".entity-checkbox:checked").length == 0) {
           swal.fire("You have no selected items.");
        } else {
			var entity_ids = [];
			$(".entity-checkbox:checked").each(function(){
				entity_ids.push($(this).val());
			});      
	   
	   
        form_id = $("#email_templates").val();
		
        if (form_id == "") {
            swal.fire("Please choose email template");
        } else {

            if (confirm("Are you sure want to send Email?")) {
               
                $.ajax({
                    type: "POST",
                    url: BASE_PATH + "application/ajax/entities.php",
                    dataType: "json",
                    data: {
                        action: "emailQuoteMultiple",
                        form_id: form_id,
                        entity_ids: entity_ids.join(',')   
                    },
                    success: function (res) {
                        if (res.success) {
                            swal.fire("Email was successfully sent");
                        } else {
                            swal.fire("Can't send email. Try again later, please");
                        }
                    },
                    complete: function (res) {
                        /*$("body").nimbleLoader('hide');*/
                    }
                });
            }

        }
    }	
}
	
function reassignQuoteDialog()
{
	  if ($(".entity-checkbox:checked").length == 0) 
		{            
		  Swal.fire('Quote not selected');       
		     return;        
		} 
	  $("#reassignCompanyDiv").modal();
		$('#company_members').on('change', function()
		{
		  var member_id = $("#company_members").val();
			$("#get_member_id").val(member_id);
			}
		);
}
	
/*$("#reassignCompanyDiv").dialog({
	modal: true,
	width: 300,
	height: 140,
	title: "Reassign Quote",
	hide: 'fade',
	resizable: false,
	draggable: false,
	autoOpen: false,
	buttons: {
		"Submit": function () {
			var member_id = $("#company_members").val();	
			reassignQuotes(member_id);
		},
		"Cancel": function () {
			$(this).dialog("close");
		}
	}
});	 */
	
	
function printSelectedQuoteForm() {
		
		if ($(".entity-checkbox:checked").size() == 0) {
		    alert("Quote not selected");
		    return;
	    }
		
		if ($(".entity-checkbox:checked").size() > 1) {
		    alert("Please select one quote");
		    return;
	    }
	   var quote_id = $(".entity-checkbox:checked").val();
		
        form_id = $("#form_templates").val();
        if (form_id == "") {
            alert("Please choose form template");
        } else {

            $.ajax({
                url: BASE_PATH + 'application/ajax/entities.php',
                data: {
                    action: "print_quote",
                    form_id: form_id,
                    quote_id: quote_id
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function () {
                },
                success: function (retData) {
                    printOrder(retData.printform);
                }
            });
        }
    }
	
	
$("#maildivnew").dialog({
	modal: true,
	width: 500,
	height: 310,
	title: "Email message",
	hide: 'fade',
	resizable: false,
	draggable: false,
	autoOpen: false,
	buttons: {
		"Submit": function () {
			$.ajax({
				url: BASE_PATH + 'application/ajax/entities.php',
				data: {
					action: "emailQuoteNewSend",
					form_id: $('#form_id').val(),
					entity_id: $('#entity_id').val(),
					mail_to: $('#mail_to_new').val(),
					mail_subject: $('#mail_subject_new').val(),
					mail_body: $('#mail_body_new').val()
				},
				type: 'POST',
				dataType: 'json',
				beforeSend: function () {
					if (!validateMailFormNew()) {
						return false;
					} else {
						// // $("body").nimbleLoader("show");
					}
				},
				success: function (response) {
					// $("body").nimbleLoader("hide");
					if (response.success == true) {
						$("#maildivnew").dialog("close");
						clearMailForm();
						
                     }
					
				},
				complete: function () {
					// $("body").nimbleLoader("hide");
				}
			});
		},
		"Cancel": function () {
			$(this).dialog("close");
		}
	}
});	 


function emailSelectedQuoteFormNew() {
	
	   if ($(".entity-checkbox:checked").length == 0) {
           Swal.fire("You have no selected items.");
		   return;
        } 
		
		if ($(".entity-checkbox:checked").length >1) {
           Swal.fire("Select only one quote.");
		   return;
        }
		
		/*else {
			var entity_ids = [];
			$(".entity-checkbox:checked").each(function(){
				entity_ids.push($(this).val());
			});
		}*/
		
		var entity_ids = $(".entity-checkbox:checked").val();
		
        form_id = $("#email_templates").val();
        if (form_id == "") {
            Swal.fire("Please choose email template");
        } else {

              $("body").nimbleLoader('show');
                $.ajax({
                    type: "POST",
                    url: BASE_PATH + "application/ajax/entities.php",
                    dataType: "json",
                    data: {
                        action: "emailQuoteNew",
                        form_id: form_id,
                        entity_id: entity_ids
                    },
                    success: function (res) {
                        if (res.success) {
                            
							
							 $("#form_id").val(form_id);
							 $("#mail_to_new").val(res.emailContent.to);
							 $("#mail_subject_new").val(res.emailContent.subject);
							 $("#mail_body_new").val(res.emailContent.body);
							 CKEDITOR.instances['mail_body_new'].setData(res.emailContent.body)
							 $("#entity_id").val(entity_ids);
							  //$("#mail_file_name").html(file_name);
							 $("#maildivnew").dialog("open");
							
                        } else {
                            Swal.fire("Can't send email. Try again later, please");
                        }
                    },
                    complete: function (res) {
                        $("body").nimbleLoader('hide');
                    }
                });


        }
    }

function convertToOrder() {
	
	    if ($(".entity-checkbox:checked").length == 0) {

               Swal.fire("You have no selected items." ); 
			  return false;        

        }

		if ($(".entity-checkbox:checked").length > 1) {

             Swal.fire("Error: You may convert one quote at a time.");

			return false;        

        }
	/*
	   if ($(".entity-checkbox:checked").size() == 0) {
           alert("You have no selected items.");
        } else 
		*/ {
			var entity_ids = [];
			$(".entity-checkbox:checked").each(function(){
				entity_ids.push($(this).val());
			});      
	
        $.ajax({
            type: "POST",
            url: "<?= SITE_IN ?>application/ajax/entities.php",
            dataType: "json",
            data: {
                action: "toOrderNew",
                entity_ids: entity_ids.join(',')
            },
            success: function (result) {
                if (result.success == true) {
                  // document.location.reload();
				  document.location.href = result.url;
				  
                } else {
                    Swal.fire("Can't convert Quote. Try again later, please");
                }
            },
            error: function (result) {
                Swal.fire("Can't convert Quote. Try again later, please");
            }
        });
    }
}
</script>
<div style="display:none" id="notes">notes</div>
<br/>


<?php /* FOLLOW UP SECTION */?>
<?php //if ($this->status != Entity::STATUS_ARCHIVED) : ?>

<div class="row">
	<div class="col-12 text-right buttion_mar">
		<?php  print functionButton('Reassign Quotes', 'reassignQuoteDialog()','','btn_bright_blue btn-sm'); ?>
		
		<?php if ($this->status == Entity::STATUS_ACTIVE) : ?>
			<?= functionButton('Place On Hold', 'placeOnHold()','','btn_bright_blue btn-sm') ?>
		<?php elseif ($this->status == Entity::STATUS_ONHOLD) : ?>
			<?= functionButton('Restore', 'restore()','','btn-sm btn_bright_blue') ?>
		<?php endif; ?>
		
		<?= functionButton('Cancel Quotes', 'cancel()','','btn-sm btn-dark') ?>
	</div>
</div>

<table class="table table-bordered table_a_link_color" style="margin-bottom:0;" id="quotes_followup_new">
	<thead>
		<tr>
			<th>
				<div class="kt-section__content kt-section__content--solid">
					<label class="kt-checkbox kt-checkbox--bold kt-checkbox--success  kt-checkbox--all " style="margin-left:2px; float: left;  margin-top: 2px">
						<input type="checkbox" onchange="if($(this).is(':checked')){ checkAllEntities() }else{ uncheckAllEntities() }"/>
						<span></span>
					</label>
				</div>
				<?php if (isset($this->order)) : ?>
				<?=$this->order->getTitle("id", "ID")?>
				<?php else : ?>ID<?php endif; ?>
			</th>
			<th>
				<?php if (isset($this->order)) : ?>
				<?=$this->order->getTitle("quoted", "Quoted")?>
				<?php else : ?>Quoted<?php endif; ?>
			</th>
			<th>Notes</th>
			<th>
				<?php if (isset($this->order)) : ?>
					<?=$this->order->getTitle("shipper", "Shipper Information")?>
				<?php else : ?>Shipper<?php endif; ?>
			</th>
			<th>Vehicle Information</th>
			<th>
				<?php if (isset($this->order)) : ?>
					<?=$this->order->getTitle("origin", "Origin")?>
				<?php else : ?>Origin<?php endif; ?>
				/
				<?php if (isset($this->order)) : ?>
					<?=$this->order->getTitle("destination", "Destination")?>
				<?php else : ?>Destination<?php endif; ?>
			</th>
			<th class="grid-head-right">
				<?php if (isset($this->order)) : ?>
					<?=$this->order->getTitle("avail", "Est. Ship")?>
				<?php else : ?>Est. Ship<?php endif; ?>
			</th>
			<th>
				<?php if (isset($this->order)) : ?>
					<?=$this->order->getTitle("tariff", "Transport Cost")?>
				<?php else : ?>Tariff<?php endif; ?>
			</th>
		</tr>
	</thead>

	<tbody>
<?php if (count($this->entities) == 0): ?>
	<tr class="">
		<td colspan="8" align="center" class="grid-body-left grid-body-right"><i>No records</i></td>
	</tr>
<?php endif; ?>
<?php foreach($this->entities as $i => $entity) :?>
		<tr id="quote_tr_<?= $entity->id ?>">
			<td align="center" class="grid-body-left">
				<div class="kt-section__content kt-section__content--solid text-center">
					<label class="kt-checkbox kt-checkbox--bold kt-checkbox--success  kt-checkbox--all" style="padding-left:20px;width:18px;height:18px">
						<input type="checkbox" value="<?= $entity->id ?>" class="entity-checkbox" />
						<span></span>
					</label>
				</div>
				<br/>
				<a class="kt-badge kt-badge--info kt-badge--inline kt-badge--pill order_id" href="<?= SITE_IN ?>application/quotes/show/id/<?= $entity->id ?>" target="_blank"><?= $entity->getNumber() ?></a>
				<br/>
				<a class="kt-badge kt-badge--inline kt-badge--pill" href="<?= SITE_IN ?>application/quotes/history/id/<?= $entity->id ?>">History</a>
			</td>
			<?php  $assigned = $entity->getAssigned(); ?>
			<td valign="top" style="white-space: nowrap;">
				<span class=""><?= $entity->getQuoted("m/d/y h:i a") ?></span>
				<br>Assigned to:<br/> <strong class="kt-font-success"><?= $assigned->contactname ?></strong><br />
			</td>
			<td>
				<?php $notes = $entity->getNotes();?>
				<?= notesIcon($entity->id, count($notes[Note::TYPE_FROM]), Note::TYPE_FROM, $entity->readonly) ?>
				<?= notesIcon($entity->id, count($notes[Note::TYPE_TO]), Note::TYPE_TO, $entity->readonly) ?>
				<?= notesIcon($entity->id, count($notes[Note::TYPE_INTERNAL]), Note::TYPE_INTERNAL, $entity->readonly) ?>
			</td>
			<td>
			<?php $shipper = $entity->getShipper();?>
				<span class=""><?= $shipper->fname ?> <?= $shipper->lname ?></span><br/>
				<?php if ($shipper->company != ""){?>
				<?= $shipper->company?><br />
				<?php }?>
				<a href="javascript:void(0);"><?= formatPhone($shipper->phone1) ?></a>
				<br/>
				<a class="kt-font-bold shipper_email" href="mailto:<?= $shipper->email ?>"><?= $shipper->email ?></a><br>
				<?php if($entity->referred_by != ""){?>
				  Referred By <b><?= $entity->referred_by ?></b><br>
				<?php }?>
			</td>
			<td>
			<?php $vehicles = $entity->getVehicles();?>
			<?php //$source = $entity->getSource(); ?>
			<?php if (count($vehicles) == 0) : ?>
			<?php elseif (count($vehicles) == 1) : ?>
				<?php $vehicle = $vehicles[0]; ?>
				<?= $vehicle->make; ?> <?= $vehicle->model; ?><br/>
				<?= $vehicle->year; ?> <?= $vehicle->type; ?>&nbsp;<?=imageLink($vehicle->year." ".$vehicle->make." ".$vehicle->model." ".$vehicle->type)?>
			    <?php if($vehicle->vin !=""){?>
					<br><span style="color:black;weight:bold;">VIN: <?= $vehicle->vin ?></span>
				<?php }?>
			<?php else : ?>
				<span class="like-link multi-vehicles">Multiple Vehicles</span>
				<div class="vehicles-info">
				<?php foreach($vehicles as $key => $vehicle) : ?>
					<div <?= ($key%2)?'style="background-color: #161616;padding: 5px;"':'style="background-color: #000;padding: 5px;"' ?>>
					<p><?= $vehicle->make ?> <?= $vehicle->model ?></p>
					<?= $vehicle->year ?> <?= $vehicle->type ?>&nbsp;<?=imageLink($vehicle->year." ".$vehicle->make." ".$vehicle->model." ".$vehicle->type)?>
					<br/>
					</div>
				<?php endforeach; ?>
				</div>
				<br/>
			<?php endif; ?>
				<br><span style="color:black;weight:bold;"><span style="color:red;weight:bold;"><?= $entity->getShipVia() ?></span><br/>
			</td>
			<?php $origin = $entity->getOrigin();?>
			<?php $destination = $entity->getDestination();?>
			<td>
				<span class="kt-font-bold" onclick="window.open('<?= $origin->getLink() ?>', '_blank')"><?= $origin->getFormatted() ?></span> /<br/>
				<span class="kt-font-bold" onclick="window.open('<?= $destination->getLink() ?>')"><?= $destination->getFormatted() ?></span><br/>
				<?php if (is_numeric((string)$entity->distance) && ($entity->distance > 0)) : ?>
					<?= number_format($entity->distance, 0, "", "") ?> mi ($ <?= number_format(($entity->getCarrierPay(false)/$entity->distance), 2, ".", ",") ?>/mi)
				<?php endif; ?>
				<span class="kt-font-bold" onclick="mapIt(<?= $entity->id ?>);">Map it</span>
			</td>
			<td valign="top" align="center" class="grid-body-right"><?= $entity->getShipDate("m/d/y") ?></td>
			<td width="11%">
				<table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
					<tr>
						<td width="10"  style="border:none;"><img src="<?= SITE_IN ?>images/icons/dollar.png" alt="Total Tariff" title="Total Tariff" width="16" height="16"/></td>
						<td style="border:none;"><?= $entity->getTotalTariff() ?></td>
					</tr>
					<tr>
						<td width="10"  style="border:none;"><img src="<?= SITE_IN ?>images/icons/truck.png" alt="Carrier Pay" title="Carrier Pay" width="16" height="16"/></td>
						<td style="border:none;"><?= $entity->getCarrierPay() ?><br/></td>
					</tr>
					<tr>
						<td width="10"  style="border:none;"><img src="<?= SITE_IN ?>images/icons/person.png" alt="Deposit" title="Deposit" width="16" height="16"/></td>
						<td style="border:none;"><?= $entity->getTotalDeposit() ?></td>
					</tr>
				</table>
			</td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php //if ($this->status != Entity::STATUS_ARCHIVED) : ?>
<div class="row">
	<div class="col-12 text-right buttion_mar">
		<?= functionButton('Convert to Order', 'convertToOrder()','','btn_bright_blue btn-sm') ?>
		
		<?php  print functionButton('Reassign Quotes', 'reassignQuoteDialog()','','btn_bright_blue btn-sm'); ?>
		
		<?php if ($this->status == Entity::STATUS_ACTIVE) : ?>
			<?= functionButton('Place On Hold', 'placeOnHold()','','btn_bright_blue btn-sm') ?>
			<?php elseif ($this->status == Entity::STATUS_ONHOLD) : ?>
			<?= functionButton('Restore', 'restore()') ?>
		<?php endif; ?>
		
		<?= functionButton('Cancel Checked Quotes', 'cancel()','','btn-sm btn_bright_blue') ?>
	</div>
</div>

<?php /* ?>
<table cellspacing="0" cellpadding="0" width="100%" class="control-bar">
	<tr>
		
		<td width="100%">&nbsp;</td>
		
        <td>
		    <?php //if ($this->status == Entity::STATUS_ACTIVE) { ?>
               <?= functionButton('Convert to Order', 'convertToOrder()') ?>
            <?php //}?>   
         </td>
		<td>
		<?php  print functionButton('Reassign Quotes', 'reassignQuoteDialog()'); ?>
		<?php // print functionButton('Reassign Quotes', 'reassign(\'bottom\')'); ?></td>

		<td>
			<?php if ($this->status == Entity::STATUS_ACTIVE) : ?>
			<?= functionButton('Place On Hold', 'placeOnHold()') ?>
			<?php elseif ($this->status == Entity::STATUS_ONHOLD) : ?>
			<?= functionButton('Restore', 'restore()') ?>
			<?php endif; ?>
		</td>
		<td><?= functionButton('Cancel Checked Quotes', 'cancel()') ?></td>
	</tr>
</table> <?php */ ?>

<?php //endif; ?>
<?php if ($this->status == Entity::STATUS_ACTIVE) : ?>
<table cellpadding="0" cellspacing="0" width="100%" class="control-bar">
	<tr>
		<td colspan="8">
			<table cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td width="100%">&nbsp;</td>
					<td style="white-space: nowrap;">Follow-up:</td>
					<td>
						<select class="form-box-combobox" name="followup_type" id="followup_type" style="width: 150px;">
							<?php foreach(FollowUp::getTypes() as $key => $type) : ?>
							<option value="<?= $key ?>"><?= $type ?></option>
							<?php endforeach; ?>
						</select>
					</td>
					<td>Date:</td>
					<td>
						<input type="text" class="form-box-textfield" name="followup_when" id="followup_when" style="width: 80px;"/>
					</td>
					<td><?= functionButton('Send Follow-up', 'sendFollowUp();','','btn_bright_blue') ?></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?php endif; ?>
@pager@


<link href="<?php echo SITE_IN; ?>styles/new/assets/vendors/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
<script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/custom/datatables/datatables.bundle.js" type="text/javascript"></script>


<script type="text/javascript">
    $(document).ready(function() {
   $('#quotes_followup_new').DataTable({
		"order": [[1, 'desc']],
       "lengthChange": false,
       "paging": false,
       "bInfo" : false,
       'drawCallback': function (oSettings) {
           $('#quotes_followup_new_wrapper').children('.row:first').children('.col-md-6:first').html('<div class="form-group row" style="margin-left:0;"><label class="col-form-label">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
           $('#quotes_followup_new_wrapper').children('.row:last').children('.col-md-5').html($('.pager').clone()).addClass('text-left');
           $('#quotes_followup_new_wrapper').children('.row:last').children('.col-md-7').html($('.table_b ').html()).addClass('text-right');
           $('.pages-div-custom').remove();
           
      }
   });
} );
</script>