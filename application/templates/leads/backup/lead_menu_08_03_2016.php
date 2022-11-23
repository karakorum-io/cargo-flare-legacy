<script type="text/javascript">
	var anim_busy = false;
	function setStatus(entity_id, status) {
		$.ajax({
			type: "POST",
			url: "<?= SITE_IN ?>application/ajax/entities.php",
			dataType: "json",
			data: {
				action: 'setStatus',
				entity_id: entity_id,
				status: status
			},
			success: function(result) {
				if (result.success == true) {
					window.location.reload();
				} else {
					alert("Lead action failed. Try again later, please.");
				}
			},
			error: function(result) {
				alert("Lead action failed. Try again later, please.");
			}
		})
	}
	function reassign(entity_id) {
		$("#reassign_dialog").dialog({
			modal: true,
			resizable: false,
			title: "Reassign Lead",
			width: 300,
			buttons: [{
				text: "Assign",
				click: function() {
					var assign_id = $("#reassign_dialog select").val();
					$.ajax({
						type: "POST",
						url: "<?= SITE_IN ?>application/ajax/entities.php",
						dataType: "json",
						data: {
							action: 'reassign',
							entity_id: entity_id,
							assign_id: assign_id
						},
						success: function(result) {
							if (result.success == true) {
								window.location.reload();
							} else {
								$("#reassign_dialog div.error").html("<p>Can't reassign lead. Try again later, please.</p>");
								$("#reassign_dialog div.error").slideDown().delay(2000).slideUp();
							}
						},
						error: function(result) {
							$("#reassign_dialog div.error").html("<p>Can't reassign lead. Try again later, please.</p>");
							$("#reassign_dialog div.error").slideDown().delay(2000).slideUp();
						}
					})
				}
			},{
				text: "Cancel",
				click: function() {
					$(this).dialog("close");
				}
			}]
		}).dialog("open");
	}
	
	function saveQuotes(email,entity_id) {
		
        var entity_ids = [];
		entity_ids.push('{"entity_id":"'+entity_id+'"}');
		
		
		$("body").nimbleLoader('show');
        $.ajax({
            type: "POST",
            url: "<?=SITE_IN?>application/ajax/entities.php",
            dataType: 'json',
            data: {
                action: 'saveQuotesNew',
                email: email,
                data: "["+entity_ids.join(',')+"]"
            },
			success: function(res) {
				if (res.success) {
					window.location.href = '<?= SITE_IN ?>application/leads/editcreatedquote/id/'+entity_id;
				} else {
					alert("Can't save Quote(s)");
				}
			},
            complete: function(response) {
				$("body").nimbleLoader('hide');
            }
        });
    }

function changeStatusLeadsData(status,entity_id) {
    if (entity_id == '') {
        alert("You have no order id.");
    } else {
        var entity_ids = [];
        //$(".entity-checkbox:checked").each(function(){
            entity_ids.push(entity_id);
       // });
		$("#nimble_dialog_button").nimbleLoader('show');
        $.ajax({
            type: 'POST',
            url: BASE_PATH+'application/ajax/entities.php',
            dataType: 'json',
            data: {
                action: 'changeStatus',
                status: status,
                entity_ids: entity_ids.join(",")
            },
            success: function(response) {
                if (response.success == true) {
                    window.location.reload();
					//alert('done');
                }
            },
			error: function(response) {
				$("#nimble_dialog_button").nimbleLoader('hide');
				alert("Try again later, please");
			},
			complete: function(response) {
				$("#nimble_dialog_button").nimbleLoader('hide');
			}
        });
    }
}	
	
	function convertToQuote(entity_id) {
		$.ajax({
			type: "POST",
			url: "<?= SITE_IN ?>application/ajax/entities.php",
			dataType: "json",
			data: {
				action: 'toQuote',
				entity_id: entity_id
			},
			success: function(result) {
				if (result.success == true) {
					window.location.href = '<?= SITE_IN ?>application/quotes/edit/id/'+entity_id;
				} else {
					if (result.reason != undefined) {
						alert(result.reason);
					} else {
						alert("Can't convert Lead. Try again later, please.");
					}
				}
			},
			error: function(result) {
				alert("Can't convert Lead. Try again later, please.");
			}
		});
	}
	
function convertToOrder(entity_id) {
		var entity_ids = [];
		entity_ids.push(entity_id);
			     
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
                  window.location.href = '<?= SITE_IN ?>application/orders/edit/id/'+entity_id;
				  
                } else {
                    alert("Can't convert to Order. Try again later, please");
                }
            },
            error: function (result) {
                alert("Can't convert to Order. Try again later, please");
            }
        });
   }


function setAppointmentDetail()
{
	  
	  $("#appointmentDiv").dialog("open");
}
	
	 

function setAppointmentDataDetail(app_date,app_time,notes) 
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
		        
		var entity_id = <?php  print $_GET['id'];?>;         
		var entity_ids = [];       
		//entity_ids.push(entity_id); 
		 //$(".entity-checkbox:checked").each(function(){
            entity_ids.push(entity_id);
        //});
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
			setAppointmentDataDetail(app_date,app_time,notes)
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

        
	});
</script>
<div id="reassign_dialog" style="display:none;">
	<div class="error" style="display:none;"></div>
	<strong>Assign to:</strong>
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
						<?php endforeach;?>
						<optgroup label="Active User">
						<?php echo $activemember; ?>
						</optgroup>
						<!--optgroup label="InActive User">
						<?php //echo $inactivemember; ?>
						</optgroup-->
                </select>
</div>
<div class="tab-panel-container">
	<ul class="tab-panel">
		
       <?php 
	    $url = "edit";
		$urlDetail = "show";
		$strL = "Lead";
		
		if($this->entity->status != Entity::STATUS_CACTIVE && 
		   $this->entity->status != Entity::STATUS_CASSIGNED &&
		   $this->entity->status != Entity::STATUS_CPRIORITY &&
	       $this->entity->status != Entity::STATUS_CONHOLD &&
	       $this->entity->status != Entity::STATUS_CDEAD
		   ){ 
		   
		      $url = "editcreatedquote";
		   //if($this->entity->status != Entity::STATUS_CASSIGNED ){
		      $urlDetail = "showcreated";
			 $strL = "Quote";  
		   //}
		}
		  
		  if($_GET['leads'] =="showimported" || $_GET['leads'] =="editimported"){?>
            <li class="tab first<?= (@$_GET['leads'] == 'showimported' )?" active":"" ?>"><span onclick="location.href = '<?= SITE_IN ?>application/leads/showimported/id/<?= $this->entity->id ?>'"><?PHP print $strL;?> Detail</span></li>
        <?php }else{?>
        <li class="tab first<?= (@$_GET['leads'] == 'show' || $_GET['leads'] == 'showcreated')?" active":"" ?>"><span onclick="location.href = '<?= SITE_IN ?>application/leads/<?PHP print $urlDetail;?>/id/<?= $this->entity->id ?>'"><?PHP print $strL;?> Detail</span></li>
        <?php }?>
        <?php if($_GET['leads'] =="showimported" || $_GET['leads'] =="editimported"){
			
			?>
		    <li class="tab<?= (@$_GET['leads'] == 'editimported')?" active":"" ?>"><span onclick="location.href = '<?= SITE_IN ?>application/leads/editimported/id/<?= $this->entity->id ?>'">Edit <?PHP print $strL;?></span></li>
        
         <?php }else{?>
         <?php if (($this->entity->status != Entity::STATUS_ARCHIVED) && !$this->entity->readonly) : ?>
		<li class="tab<?= (@$_GET['leads'] == 'edit'  || $_GET['leads'] == 'editcreatedquote')?" active":"" ?>"><span onclick="location.href = '<?= SITE_IN ?>application/leads/<?PHP print $url;?>/id/<?= $this->entity->id ?>'">Edit <?PHP print $strL;?></span></li>
         <?php endif; ?>
         <?php }?>
         
		
		<li class="tab<?= (@$_GET['leads'] == 'history')?" active":"" ?>"><span onclick="location.href = '<?= SITE_IN ?>application/leads/history/id/<?= $this->entity->id ?>'"><?PHP print $strL;?> History</span></li>
		<?php if (!is_null($this->entity->email_id) && ctype_digit((string)$this->entity->email_id)) : ?>
		<li class="tab last<?= (@$_GET['leads'] == 'email')?" active":"" ?>"><span onclick="location.href = '<?= SITE_IN ?>application/leads/email/id/<?= $this->entity->id ?>'">Original E-mail</span></li>
		<?php endif; ?>
	</ul>
</div>
<div class="tab-panel-line"></div>
<?php if (($this->entity->status != Entity::STATUS_ARCHIVED) && !$this->entity->readonly) : ?>
<div style="float: right;line-height:23px;" class="lead-actions">
	<div class="actions" style="">
		<?php 
if ($this->entity->status == Entity::STATUS_CARCHIVED ){?>
	
	<table cellspacing="5" cellpadding="5" width="100%" >
	<tr>
		
		
		<td><?php  //functionButton('Reassign Leads', 'reassign(\'top\')') ?>
               <?php //print functionButton('Reassign Leads', 'reassignOrdersDialog()') ?>
        </td>
		   <td><?= functionButton('Hold', 'changeStatusLeadsData('.Entity::STATUS_CONHOLD.','.$this->entity->id.')') ?></td>
         <?php 
              if ($this->entity->status == Entity::STATUS_CDEAD)
			  {?>  
            <td><?= functionButton('Remove Do Not Call', 'changeStatusLeadsData('.Entity::STATUS_CACTIVE.','.$this->entity->id.')') ?></td>
            <td><?= functionButton('Cancel', 'changeStatusLeadsData('.Entity::STATUS_CARCHIVED.','.$this->entity->id.')') ?></td>
            <?php }else{?>
           <td valign="top"><?= functionButton('Uncancel', 'changeStatusLeadsData('.Entity::STATUS_CACTIVE.','.$this->entity->id.')') ?></td>
           <?php }?>
         
	</tr>
</table>
<?php
}
else //if ($this->status != Entity::STATUS_CARCHIVED || $_GET['leads']=="search") 
{ ?>
<table cellspacing="5" cellpadding="5" width="100%" >
	<tr>
		
		
		<td><?php  print functionButton('Reassign Leads', 'reassign('.$this->entity->id.')') ?>
               <?php //print functionButton('Reassign Leads', 'reassignOrdersDialog()') ?>
        </td>
         <?php if ($this->entity->status == Entity::STATUS_CFOLLOWUP || $this->entity->status  == Entity::STATUS_CQUOTED){?>
              <!--td><?= functionButton('Set Appointment', 'setAppointmentDetail()') ?></td-->
       <?php }?>
        <?php if ($this->entity->status != Entity::STATUS_CQUOTED){?>
              <td><?= functionButton('Convert to Quote', 'saveQuotes(\'0\','.$this->entity->id.')') ?></td>
       <?php }?>
        <?php //if ($this->status != Entity::STATUS_CACTIVE && $this->status != Entity::STATUS_CASSIGNED && $this->status != Entity::STATUS_CQUOTED){?>
               <td><?= functionButton('Convert to Order', 'convertToOrder('.$this->entity->id.')') ?></td>
       <?php //}?>
       
         <?php if($this->entity->status == Entity::STATUS_CPRIORITY){?>
         <td><?= functionButton('Remove Priority', 'changeStatusLeadsData('.Entity::STATUS_CASSIGNED.','.$this->entity->id.')') ?></td>
         <?php }else{?>
         <td><?= functionButton('Make Priority', 'changeStatusLeadsData('.Entity::STATUS_CPRIORITY.','.$this->entity->id.')') ?></td>
         <?php }?>
         
         <?php if($this->entity->status == Entity::STATUS_CONHOLD){?>
         <td><?= functionButton('Remove Hold', 'changeStatusLeadsData('.Entity::STATUS_CASSIGNED.','.$this->entity->id.')') ?></td>
         <?php }else{?>
         <td><?= functionButton('Hold', 'changeStatusLeadsData('.Entity::STATUS_CONHOLD.','.$this->entity->id.')') ?></td>
         <?php }?>
         
         <td><?= functionButton('Do Not Call', 'changeStatusLeadsData('.Entity::STATUS_CDEAD.','.$this->entity->id.')') ?></td>
         <td><?= functionButton('Cancel', 'changeStatusLeadsData('.Entity::STATUS_CARCHIVED.','.$this->entity->id.')') ?></td>
		<!--td><?= functionButton('Cancel', 'cancel()') ?></td-->
	</tr>
</table>
<?php }?>
	</div>
</div>
<?php endif; ?>