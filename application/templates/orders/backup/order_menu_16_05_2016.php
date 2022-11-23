<script type="text/javascript">
    var anim_busy = false;
    $(document).ready(function () {
		$("#acc_search_dialog_new_dispatch").dialog({
			autoOpen: false,
			dialogClass: 'acc_search_dialog',
			modal: true,
			width: 400,
			resizable: false,
			draggable: true,
			buttons: [{
				text: 'Cancel',
				click: function(){
					$(this).dialog('close');
					$("#acc_search_result_new_dispatch").html('');
					$("#acc_search_dialog_new_dispatch input").val('');
				}
			},{
				text: 'Get Account Info',
				click: function(){
					if ($("input[name='acc_search_result_item']:checked").size() == 0) return;
					var order_id = <?=$this->entity->id?>;
					var acc_obj = acc_data[$("input[name='acc_search_result_item']:checked").val()];
					
					location.href =  BASE_PATH+"application/orders/dispatchnew/id/"+<?=$this->entity->id?>+"/acc/"+acc_obj.id;
				   
				}
			},{
				text: 'New Carrier',
				click: function(){
					
					var order_id = <?=$this->entity->id?>;
		   
					location.href =  BASE_PATH+"application/orders/dispatchnew/id/"+order_id;
					$(this).dialog('close');
				}
			}]
		});									
		
        $("#dispatch_date_dialog").dialog({
            autoOpen: false,
            modal: true,
            resizable: false,
            draggable: false,
            width: 100,
            buttons: [
                {
                    text: "Cancel",
                    click: function () {
                        $(this).dialog("close");
                    }
                },
                {
                    text: "Save",
                    click: function () {
                        var val = $.trim($("#dispatch_date_value").val());
                        if (val == "") return;
                        $(".loading").show();
                        $.ajax({
                            type: "POST",
                            url: "<?=SITE_IN?>application/ajax/entities.php",
                            dataType: "json",
                            data: {
                                action: 'setDispatchDate',
                                entity_id: <?= $this->entity->id ?>,
                                type: dispatch_date_type,
                                value: val
                            },
                            success: function (res) {
                                if (res.success) {
                                    $("#dispatch_date_dialog").dialog("close");
                                } else {
                                    alert("Can't save date");
                                }
                            },
                            error: function (res) {
                                alert("Can't save date");
                            },
                            complete: function (res) {
                                $(".loading").hide();
                            }
                        });
                    }
                }
            ]
        });
        $("#dispatch_date_value").datepicker();
    });
	
	function selectCarrierNewDispatch() {
		
			
		$("#acc_global_search_result_new_dispatch").html("");
		$("#acc_search_string").val("");
		acc_type = 1;
		$("#acc_search_dialog_new_dispatch").dialog({width: 650},{title:'Select Carrier'}).dialog('open');
	}
	
    function setStatus(status) {
        if (confirm("Are you sure want to change order's status?")) {
            $.ajax({
                type: "POST",
                url: "<?= SITE_IN ?>application/ajax/entities.php",
                dataType: "json",
                data: {
                    action: 'setStatus',
                    entity_id: <?= $this->entity->id ?>,
                    status: status
                },
                success: function (result) {
                    if (result.success == true) {
                        window.location.reload();
                    } else {
                        alert("Order action failed. Try again later, please.");
                    }
                },
                error: function (result) {
                    alert("Order action failed. Try again later, please.");
                }
            });
        }
    }
	
function changeOrdersStateDetail(status) {
    if (confirm("Are you sure want to change order's status?")) {
		var entity_id = <?= $this->entity->id ?>;
		var entity_ids = [];
		entity_ids.push(entity_id);
    
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
            }
        }
    });
	
	}
}

    function reassign() {
        $("#reassign_dialog").dialog({
            modal: true,
            resizable: false,
            title: "Reassign Order",
            width: 300,
            buttons: [
                {
                    text: "Assign",
                    click: function () {
                        var assign_id = $("#reassign_dialog select").val();
                        $.ajax({
                            type: "POST",
                            url: "<?= SITE_IN ?>application/ajax/entities.php",
                            dataType: "json",
                            data: {
                                action: 'reassign',
                                entity_id: <?= $this->entity->id ?>,
                                assign_id: assign_id
                            },
                            success: function (result) {
                                if (result.success == true) {
                                    window.location.reload();
                                } else {
                                    $("#reassign_dialog div.error").html("<p>Can't reassign order. Try again later, please.</p>");
                                    $("#reassign_dialog div.error").slideDown().delay(2000).slideUp();
                                }
                            },
                            error: function (result) {
                                $("#reassign_dialog div.error").html("<p>Can't reassign order. Try again later, please.</p>");
                                $("#reassign_dialog div.error").slideDown().delay(2000).slideUp();
                            }
                        })
                    }
                },
                {
                    text: "Cancel",
                    click: function () {
                        $(this).dialog("close");
                    }
                }
            ]
        }).dialog("open");
    }

    function split() {
	    $("#split_dialog").dialog({
		    width: 400,
		    modal: true,
		    resizable: false,
		    title: "Split Order",
		    buttons: [
			    {
				    text: "Split",
				    click: function () {
					    if ($("#split_dialog .vehicle_ids:checked").size() == 0) {
						    $(this).dialog("close");
						    return;
					    }
					    var vehicle_ids = [];
					    $("#split_dialog .vehicle_ids:checked").each(function () {
						    vehicle_ids.push($(this).val());
					    });
					    $.ajax({
						    type: "POST",
						    url: "<?= SITE_IN ?>application/ajax/entities.php",
						    dataType: "json",
						    data: {
							    action: 'split',
							    entity_id: <?= $this->entity->id ?>,
							    vehicle_ids: vehicle_ids.join(',')
						    },
						    success: function (result) {
							    if (result.success == true) {
								    if ($("#split_dialog input[name='after_split']").val() == 1) {
									    window.location.href = "<?= SITE_IN ?>application/orders/show/id/" + result.data;
									    $(this).dialog("close");
								    } else {
									    window.location.reload();
								    }
							    } else {
								    alert("Split failed. Try again later, please.");
							    }
						    },
						    error: function (result) {
							    alert("Split failed. Try again later, please.");
						    }
					    });
					    $(this).dialog("close");
				    }
			    },
			    {
				    text: "Cancel",
				    click: function () {
					    $(this).dialog("close");
				    }
			    }
		    ]
	    }).dialog("open");
    }

    function sendOrderConfirmation() {
        if (confirm("Are you sure you want to send Order Confirmation Email to Shipper?")) {
            sendOrderEmail('sendConfirmation');
        }
    }

    function sendInvoiceEmail() {
        if (confirm("Are you sure you want to send Invoice to Shipper?")) {
            sendOrderEmail("sendInvoice");
        }
    }

    function sendOrderForm() {
        if (confirm("Are you sure you want to send Order Form to Shipper?")) {
            sendOrderEmail("sendOrderForm");
        }
    }

    function sendDispatchSheet() {
        if (confirm("Are you sure you want to send Dispatch Sheet to Carrier?")) {
            sendOrderEmail("sendDispatchSheet")
        }
    }

    function sendOrderEmail(action) {
        $("body").nimbleLoader('show');
        $.ajax({
            type: "POST",
            url: BASE_PATH + "application/ajax/entities.php",
            dataType: "json",
            data: {
                action: action,
                entity_id: <?=$this->entity->id?>
            },
            success: function (res) {
                if (res.success) {
                    alert("Email was successfully sent");
                } else {
                    alert("Can't send email. Try again later, please");
                }
            },
            complete: function (res) {
                $("body").nimbleLoader('hide');
            }
        });
    }
	
function sendDispatchLink(id) {
        $("body").nimbleLoader('show');
        $.ajax({
            type: "POST",
            url: BASE_PATH + "application/ajax/entities.php",
            dataType: "json",
            data: {
                action: "sendDispatchLink",
                id: id
            },
            success: function (res) {
                if (res.success) {
                    alert("Email was successfully sent");
                } else {
                    alert("Can't send email. Try again later, please");
                }
            },
            complete: function (res) {
                $("body").nimbleLoader('hide');
            }
        });
    }

function alertOK(){
	 $("#blockedEditAlertIdDetail").dialog("close"); 
	}	
function showBlockedMessage(blockedUserName)
{
	 var alertMsg = "<p><b>" + blockedUserName + "</b> is editing this order at this moment, please try again later.</p><div><input type='button' value='OK' onclick='alertOK()' style='margin-left: 40%; width: 65px; height: 29px;color: #008ec2;'></div>" ;
	 
	 $("body").nimbleLoader('show');
        $.ajax({
            type: "POST",
            url: BASE_PATH + "application/ajax/entities.php",
            dataType: "json",
            data: {
                action: "checkBlock",
                entity_id: <?php echo $this->entity->id;?>
            },
            success: function (res) {
                if (res.success) {
                    window.location.href = "<?= SITE_IN ?>application/orders/edit/id/<?php echo $this->entity->id;?>";
                } else {
                     $("#blockedEditAlertIdDetail").dialog({
						modal: true,
						width: 385,
						height: 130,
						title: "Freight Dragon",
						hide: 'fade',
						resizable: false,
						draggable: false,
						autoOpen: true
						});
					 
						$( "#blockedEditAlertIdDetail p" ).remove();
						$( "#blockedEditAlertIdDetail div" ).remove();
						$( "#blockedEditAlertIdDetail" ).append(alertMsg);
                }
            },
            complete: function (res) {
                $("body").nimbleLoader('hide');
            }
        });
	 
	
}
</script>
<div id="blockedEditAlertIdDetail" style="display: none;">
    
</div>    
<div id="split_dialog" style="display:none">
	<p><strong>Select Vehicle(s) for new Order:</strong></p>
	<table cellspacing="0" cellpadding="0" border="0" class="grid" width="100%">
		<?php foreach ($this->entity->getVehicles() as $key => $vehicle) : ?>
			<tr class="grid-body">
				<td><label for="vehicle_ids_<?= $key ?>"><?= $vehicle->make ?> / <?= $vehicle->model ?>
						/ <?= $vehicle->year ?> / <?= $vehicle->type ?></label></td>
				<td width="20"><input type="checkbox" class="vehicle_ids" id="vehicle_ids_<?= $key ?>"
				                      name="vehicle_ids[<?= $key ?>]" value="<?= $vehicle->id ?>"/></td>
			</tr>
		<?php endforeach; ?>
	</table>
	<br/>

	<p><strong>After Split:</strong></p>
	<table cellspacing="0" cellpadding="0" border="0" class="grid">
		<tr>
			<td><input type="radio" name="after_split" value="1" id="go_new_order" checked="checked"/></td>
			<td><label for="go_new_order"><strong>Go to new Order</strong></label></td>
			<td><input type="radio" name="after_split" value="2" id="stay_here"/></td>
			<td><label for="stay_here"><strong>Stay Here</strong></label></td>
		</tr>
	</table>
</div>
<div id="reassign_dialog" style="display:none;">
    <div class="error" style="display:none;"></div>
    <strong>Assign to:</strong>
    <select class="form-box-combobox">
        <?php foreach ($this->company_members as $member) : ?>
            <?php if($member->status == "Active"){
                   $activemember .="<option value= '".$member->id."'>" .$member->contactname ."</option>";
			 }
			 /*else {
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
<div id="dispatch_date_dialog" style="display: none;">
    <br/>
    <input id="dispatch_date_value" size="10"/>
</div>
<div class="tab-panel-container">
    <ul class="tab-panel">
        <li class="tab first<?= (@$_GET['orders'] == 'show') ? " active" : "" ?>"><span
                onclick="location.href = '<?= SITE_IN ?>application/orders/show/id/<?= $this->entity->id ?>'">Order Details</span>
        </li>
       
        <?php if (($this->entity->status != Entity::STATUS_ARCHIVED)) : // && $this->entity->status != Entity::STATUS_DELIVERED && !$this->entity->readonly?>
        
         <?php /*if($_SESSION['member']['access_dispatch_orders'] ==1 
				  || $this->entity->status == Entity::STATUS_ACTIVE
				  || $this->entity->status == Entity::STATUS_ONHOLD
				  || $this->entity->status == Entity::STATUS_POSTED
				  || $this->entity->status == Entity::STATUS_NOTSIGNED
				 ){*/ 
		      $blockedBy = "";
			  $locationHref = "location.href = '".SITE_IN."application/orders/edit/id/".$this->entity->id."'";
			  $locationHrefDispatch = "selectCarrierNewDispatch()";
		      if ($this->entity->isBlocked()) 
			  {
				   $blockedBy = $this->entity->blockedByMember();
				  $locationHref = "showBlockedMessage('".$blockedBy."');";
				  $locationHrefDispatch = "showBlockedMessage('".$blockedBy."');";
				  
			  }
		     ?>
            <li class="tab<?= (@$_GET['orders'] == 'edit') ? " active" : "" ?>"><span
                    onclick="<?= $locationHref ?>">Edit</span>
            </li>
             <?php //} ?>
        <?php endif; ?>
       
        <li class="tab<?= (@$_GET['orders'] == 'history') ? " active" : "" ?>"><span
                onclick="location.href = '<?= SITE_IN ?>application/orders/history/id/<?= $this->entity->id ?>'">History</span>
        </li>
        <?php if ( $_SESSION['member']['access_payments']) : //$this->entity->status != Entity::STATUS_ARCHIVED ?>
            <li class="tab<?= (@$_GET['orders'] == 'payments') ? " active" : "" ?>"><span
                    onclick="location.href = '<?= SITE_IN ?>application/orders/payments/id/<?= $this->entity->id ?>'">Payments</span>
            </li>
        <?php endif; ?>
        <li class="tab<?= (@$_GET['orders'] == 'uploads') ? " active" : "" ?>"><span
                onclick="location.href = '<?= SITE_IN ?>application/orders/uploads/id/<?= $this->entity->id ?>'">Documents</span>
        </li>
        <?php if (!is_null($this->entity->dispatched) && (strtotime($this->entity->dispatched) != 0) && $_SESSION['member']['access_dispatch']) { ?>
            <li class="tab<?= (@$_GET['orders'] == 'dispatch') ? " active" : "" ?>"><span
                    onclick="location.href = '<?= SITE_IN ?>application/orders/dispatch/id/<?= $this->entity->id ?>'">Dispatch Sheet</span>
            </li>
        <?php } ?>
        <li class="tab<?= (@$_GET['orders'] == 'matchcarrier') ? " active" : "" ?>"><span
                onclick="location.href = '<?= SITE_IN ?>application/orders/matchcarrier/id/<?= $this->entity->id ?>'">Carrier Match</span>
        </li>
    </ul>
    <div style="clear:both;"></div>

</div>
<div class="tab-panel-line"></div>
    <div style="float: right;line-height:23px;" class="order-actions">
        <div class="actions">
            <table cellspacing="5" cellpadding="5" border="0" width="100%">
                <tr>
                    
                    <td><?= functionButton('Print', 'printSelectedOrderForm()') ?></td>
                    <td><?=$this->form_templates; ?></td>
                    <td><?= functionButton('Email', 'emailSelectedOrderFormNew()') ?></td>
                    <td>@email_templates@</td>
					<?php if (($this->entity->status != Entity::STATUS_ARCHIVED) && !$this->entity->readonly) { ?>
                    
                            <!--
                            <td><?= functionButton('Send Confirmation', 'sendOrderConfirmation()') ?></td>
                            <td><?= functionButton('Send Invoice', 'sendInvoiceEmail()') ?></td>
                            <td><?= functionButton('Send Order', 'sendOrderForm()') ?></td>
                            -->
                            <?php if (is_null($this->entity->dispatched)) { ?>
                                <td><?= functionButton('Dispatch', $locationHrefDispatch) ?></td>
                            <?php } ?>
                            
                            <?php if($this->entity->status == Entity::STATUS_DELIVERED || $this->entity->status == Entity::STATUS_PICKEDUP || $this->entity->status == Entity::STATUS_ISSUES){
        
                                  $stateOrders = Entity::STATUS_PICKEDUP;
                    
                                   if($this->entity->status == Entity::STATUS_PICKEDUP)
                                     $stateOrders = Entity::STATUS_DISPATCHED;
                                   elseif($this->entity->status == Entity::STATUS_ISSUES)
                                     $stateOrders = Entity::STATUS_PICKEDUP;
                            ?>
                    
                                    <td>
                    
                                <?php print functionButton('Previous Status', 'changeOrdersStateDetail('.$stateOrders.')'); ?>
                    
                            <?php }?>
                            
                            <?php if (count($this->entity->getVehicles()) > 1) : ?>
                                    <td><?= functionButton('Split', 'split()') ?></td>
                            <?php endif; ?>
                            
                            <td><?= functionButton('Reassign', 'reassign()') ?></td>
                            
                            <?php if (in_array($this->entity->status, array(Entity::STATUS_NOTSIGNED,Entity::STATUS_PICKEDUP, Entity::STATUS_DISPATCHED))) { ?>
                            <?php $dispatchID =""; if($this->dispatchSheet->id!=""){ $dispatchID = $this->dispatchSheet->id;}else{$dispatchID = $this->dispatch->id;}?>
                           <td><?= functionButton("Undispatch", "cancelDispatchSheet(" . $dispatchID . ")") ?></td>
                           <?php } ?>
                            
                            <?php  if ($this->entity->status != Entity::STATUS_ARCHIVED) { ?>
                                <td><?= functionButton('Cancel', 'setStatus(' . Entity::STATUS_ARCHIVED . ')') ?></td>
                            <?php }?>
                            
                            <?php if ($this->entity->status == Entity::STATUS_ACTIVE) { ?>
                                <td><?= functionButton('On Hold', 'setStatus(' . Entity::STATUS_ONHOLD . ')') ?></td>
                            <?php } elseif ($this->entity->status == Entity::STATUS_ONHOLD) { ?>
                                <td><?= functionButton('Remove Hold', 'setStatus(' . Entity::STATUS_ACTIVE . ')') ?></td>
                            <?php } elseif ($this->entity->status == Entity::STATUS_PICKEDUP) { ?>
                                <td><?php //print functionButton('Picked Up Date', 'setDispatchedDate(\'actual_pickup_date\')'); ?>
                                <?php print  functionButtonDateByEntity(Entity::STATUS_DELIVERED,'Delivered Date', 'setPickedUpStatusAndDateByEntity',false,'delivered_button','yy-mm-dd',$this->entity->id);?>
                                </td>
                            <?php } elseif ($this->entity->status == Entity::STATUS_DISPATCHED) { ?>
                                <td>
                                    <?php print  functionButtonDateByEntity(Entity::STATUS_PICKEDUP,'Picked Up Date', 'setPickedUpStatusAndDateByEntity',false,'pickup_button1','yy-mm-dd',$this->entity->id);?>
                                </td>
                            <?php } elseif ($this->entity->status == Entity::STATUS_DELIVERED) { ?>
                                <td><?= functionButton('Delivered Date', 'setDispatchedDate(\'actual_ship_date\')') ?></td>
                            <?php } ?>
                            
                            <?php if($this->entity->status==Entity::STATUS_ACTIVE){ ?>
                              <td><?= functionButton('Post Load', 'postOrderToFB('.$this->entity->id.')') ?></td>
                           <?php }?>
                           
                            <?php if($this->entity->status==Entity::STATUS_POSTED){ ?>
                              <td><?= functionButton('Unpost', 'unpostOrderFromFB('.$this->entity->id.')') ?></td>
                            <?php }?>
                            
                     <?php }else{ ?>
                         <td><?= functionButton('Reassign', 'reassign()') ?></td>
						<?php   
                              $stateOrders = Entity::STATUS_PICKEDUP;			
                        ?>
			
							<td>
			
						<td><?php //print functionButton('Previous Status', 'changeOrdersStateDetail('.$stateOrders.')'); ?></td>
                        
                        <td><?php print functionButton('Uncancel', 'setStatus(' . Entity::STATUS_ACTIVE . ')'); ?></td>
                  
                    <?php }?>
                </tr>
            </table>
        </div>
    </div>
<script type="text/javascript">
    function printSelectedOrderForm() {
        form_id = $("#form_templates").val();
        if (form_id == "") {
            alert("Please choose form template");
        } else {

            $.ajax({
                url: BASE_PATH + 'application/ajax/entities.php',
                data: {
                    action: "print_order",
                    form_id: form_id,
                    order_id: '<?=$_GET["id"];?>'
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

    function emailSelectedOrderForm() {

        form_id = $("#email_templates").val();
        if (form_id == "") {
            alert("Please choose email template");
        } else {

            if (confirm("Are you sure want to send Email?")) {
                $("body").nimbleLoader('show');
                $.ajax({
                    type: "POST",
                    url: BASE_PATH + "application/ajax/entities.php",
                    dataType: "json",
                    data: {
                        action: "emailOrder",
                        form_id: form_id,
                        entity_id: <?=$this->entity->id?>
                    },
                    success: function (res) {
                        if (res.success) {
                            alert("Email was successfully sent");
                        } else {
                            alert("Can't send email. Try again later, please");
                        }
                    },
                    complete: function (res) {
                        $("body").nimbleLoader('hide');
                    }
                });
            }

        }
    }
	
	function emailSelectedOrderFormNew() {

        form_id = $("#email_templates").val();
        if (form_id == "") {
            alert("Please choose email template");
        } else {

              $("body").nimbleLoader('show');
                $.ajax({
                    type: "POST",
                    url: BASE_PATH + "application/ajax/entities.php",
                    dataType: "json",
                    data: {
                        action: "emailOrderNew",
                        form_id: form_id,
                        entity_id: <?=$this->entity->id?>
                    },
                    success: function (res) {
                        if (res.success) {
                            
							 $("#maildivnew").dialog("open");
							 $("#form_id").val(form_id);
							 $("#mail_to_new").val(res.emailContent.to);
							 $("#mail_subject_new").val(res.emailContent.subject);
							 $("#mail_body_new").val(res.emailContent.body);
							 
							  //$("#mail_file_name").html(file_name);
							 
							
                        } else {
                            alert("Can't send email. Try again later, please");
                        }
                    },
                    complete: function (res) {
                        $("body").nimbleLoader('hide');
                    }
                });


        }
    }
</script>