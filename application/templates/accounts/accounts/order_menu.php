<script type="text/javascript">
    var anim_busy = false;
/*    $(document).ready(function () {
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
    })*/;
	
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
                        swal.fire("Order action failed. Try again later, please.");
                    }
                },
                error: function (result) {
                    swal.fire("Order action failed. Try again later, please.");
                }
            });
        }
    }




    function reassign() {

        var Assign_val =$("#Assign_id").val();
         if(Assign_val != '')
         {
             
            var assign_id = $("#reassign_dialog select").val();

            $.ajax({
                type: "POST",
                url: "<?=SITE_IN?>application/ajax/entities.php",
                dataType: "json",
                data: {
                    action: 'reassign',
                    entity_id: <?=$this->entity->id?>,
                    assign_id: assign_id
                },
                success: function (result) {
                    if (result.success == true) {
                        window.location.reload();
                    } else {

                         $('#reassign_dialog').find(".error").html("Can't reassign order. Try again later, please.").css('display','block');
                          $("#reassign_dialog div.error").slideDown().delay(2000).slideUp();
                    }
                },
                error: function (result) {
                    /*$("#reassign_dialog div.error").html("<p>Can't reassign order. Try again later, please.</p>");*/
                     $('#reassign_dialog').find(".error").html("Can't reassign order. Try again later, please.").css('display','block');

                    $("#reassign_dialog div.error").slideDown().delay(2000).slideUp();
                }
            })


         }else{
       
        $("#reassign_dialog").modal();
        var Assign = 'Assign';
        $("#Assign_id").val(Assign);

         }

         $("#Close").click(function(){
            $("#Assign_id").val('');
         })

    }



    function split() 
    {
       $("#split_dialog").modal();
    }

    function Split_send()
    {

    if ($("#split_dialog .vehicle_ids:checked").length == 0) {
           $("#split_dialog").modal("hide");
            return;
        }
        var vehicle_ids = [];
        $("#split_dialog .vehicle_ids:checked").each(function () {
            vehicle_ids.push($(this).val());
        });
        $.ajax({
            type: "POST",
            url: "<?=SITE_IN?>application/ajax/entities.php",
            dataType: "json",
            data: {
                action: 'split',
                entity_id: <?=$this->entity->id?>,
                vehicle_ids: vehicle_ids.join(',')
            },
            success: function (result) {
                if (result.success == true) {
                    if ($("#split_dialog input[name='after_split']").val() == 1) {
                        window.location.href = "<?=SITE_IN?>application/orders/show/id/" + result.data;
                        $("#split_dialog").modal("hide");
                    } else {
                        window.location.reload();
                    }
                } else {
                    swal.fire("Split failed. Try again later, please.");
                }
            },
            error: function (result) {
                swal.fire("Split failed. Try again later, please.");
            }
        });
        $("#split_dialog").modal('hide');

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
       Processing_show()
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
                    swal.fire("Email was successfully sent");
                } else {
                    swal.fire("Can't send email. Try again later, please");
                }
            },
            complete: function (res) {
               KTApp.unblockPage();
            }
        });
    }
	
function sendDispatchLink(id) {
      /*  $("body").nimbleLoader('show');*/
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
                    swal.fire("Email was successfully sent");
                } else {
                    swal.fire("Can't send email. Try again later, please");
                }
            },
            complete: function (res) {
              /*  $("body").nimbleLoader('hide');*/
            }
        });
    }
</script>

 
<!--begin::Modal-->
<div class="modal fade" id="split_dialog" tabindex="-1" role="dialog" aria-labelledby="split_dialog_model" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="split_dialog_model">split</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <div class="modal-body">


                <p><strong>Select Vehicle(s) for new Order:</strong></p>
                <table cellspacing="0" cellpadding="0" border="0" class="grid" width="100%">
                <?php foreach ($this->entity->getVehicles() as $key => $vehicle): ?>
                <tr class="grid-body">
                <td><label for="vehicle_ids_<?=$key?>"><?=$vehicle->make?> / <?=$vehicle->model?>
                / <?=$vehicle->year?> / <?=$vehicle->type?></label></td>
                <td width="20"><input type="checkbox" class="vehicle_ids" id="vehicle_ids_<?=$key?>"
                  name="vehicle_ids[<?=$key?>]" value="<?=$vehicle->id?>"/></td>
                </tr>
                <?php endforeach;?>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-dark btn-sm" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn_dark_blue btn-sm" onclick="Split_send()">Split</button>
            </div>
        </div>
    </div>
</div>




<!--begin::Modal-->
    <div class="modal fade" id="reassign_dialog" tabindex="-1" role="dialog" aria-labelledby="reassign_dialog_model" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="reassign_dialog_model">Reassign Order(s)</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <i class="fa fa-times" aria-hidden="true"></i>
        </button>
    </div>
    <div class="modal-body">
    <div class="error" style="display:none;"></div>
    <label><strong>Assign to:</strong></label>
    <select class="form-box-combobox">
    <?php foreach ($this->company_members as $member): ?>
    <?php if ($member->status == "Active") {
    $activemember .= "<option value= '" . $member->id . "'>" . $member->contactname . "</option>";
    }
    ?>
    <?php endforeach;?>
    <optgroup label="Active User">
    <?php echo $activemember; ?>
    </optgroup>
    </select>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-dark btn-sm" id="Close" data-dismiss="modal">Close</button>
        <button type="button" id="Assign_id" value="" onclick="reassign()" class="btn btn_dark_blue btn-sm">Assign</button>
    </div>
    </div>
    </div>
    </div>
    <!--end::Modal-->



<div id="dispatch_date_dialog" style="display: none;">
    <br/>
    <input id="dispatch_date_value" size="10"/>
</div>

<div class="tab-panel-container">
    <ul class="tab-panel">
        <li class="tab first<?= (@$_GET['orders'] == 'show') ? " active" : "" ?>"><span
                onclick="location.href = '<?= SITE_IN ?>application/orders/show/id/<?= $this->entity->id ?>'">Order Details</span>
        </li>
       
        <?php if (($this->entity->status != Entity::STATUS_ARCHIVED) && $this->entity->status != Entity::STATUS_DELIVERED && !$this->entity->readonly) : ?>
        
         <?php /*if($_SESSION['member']['access_dispatch_orders'] ==1 
				  || $this->entity->status == Entity::STATUS_ACTIVE
				  || $this->entity->status == Entity::STATUS_ONHOLD
				  || $this->entity->status == Entity::STATUS_POSTED
				  || $this->entity->status == Entity::STATUS_NOTSIGNED
				 ){*/ ?>
            <li class="tab<?= (@$_GET['orders'] == 'edit') ? " active" : "" ?>"><span
                    onclick="location.href = '<?= SITE_IN ?>application/orders/edit/id/<?= $this->entity->id ?>'">Edit Order</span>
            </li>
             <?php //} ?>
        <?php endif; ?>
       
        <li class="tab<?= (@$_GET['orders'] == 'history') ? " active" : "" ?>"><span
                onclick="location.href = '<?= SITE_IN ?>application/orders/history/id/<?= $this->entity->id ?>'">Order History</span>
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
    </ul>
    <div style="clear:both;"></div>

</div>
<div class="tab-panel-line"></div>
    <div style="float: right;line-height:23px;" class="order-actions">
        <div class="actions">
            <table cellspacing="5" cellpadding="5" border="0" width="100%" >
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
                        <td><?= functionButton('Dispatch', 'dispatch()') ?></td>
                    <?php } ?>
		            <?php if (count($this->entity->getVehicles()) > 1) : ?>
			                <td><?= functionButton('Splitss', 'split()') ?></td>
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
                      <td><?= functionButton('Post to FB', 'postOrderToFB('.$this->entity->id.')') ?></td>
                   <?php }?>
                   
                    <?php if($this->entity->status==Entity::STATUS_POSTED){ ?>
                      <td><?= functionButton('Unpost', 'unpostOrderFromFB('.$this->entity->id.')') ?></td>
                    <?php }?>
					
	                <?php }else{ ?>
                    <td><?= functionButton('Reassign', 'reassign()') ?></td>
                    
                        <td><?= functionButton('Uncancel', 'setStatus(' . Entity::STATUS_ACTIVE . ')') ?></td>
                  
                    <?php }?>
                </tr>
            </table>
        </div>
    </div>
<script type="text/javascript">
    function printSelectedOrderForm() {
        form_id = $("#form_templates").val();
        if (form_id == "") {
            swal.fire("Please choose form template");
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
            swal.fire("Please choose email template");
        } else {

            Swal.fire({
            title: 'Send Email?',
            text: "Are you sure want to send Email?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: 'btn btn_bright_blue btn-sm',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Ok!'
            }).then((result) => {
            if (result.value) {

                Processing_show()
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
                            swal.fire("Email was successfully sent");
                        } else {
                            swal.fire("Can't send email. Try again later, please");
                        }
                    },
                    complete: function (res) {
                        KTApp.unblockPage();
                    }
                })
            
              }
            })

          

        }
    }
	
	function emailSelectedOrderFormNew() {

        form_id = $("#email_templates").val();
        if (form_id == "") {
            swal.fire("Please choose email template");
        } else {

             /* $("body").nimbleLoader('show');*/
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
                            
							 /*$("#maildivnew").dialog("open")*/;
							 $("#form_id").val(form_id);
							 $("#mail_to_new").val(res.emailContent.to);
							 $("#mail_subject_new").val(res.emailContent.subject);
							 $("#mail_body_new").val(res.emailContent.body);
							 
							  //$("#mail_file_name").html(file_name);
							 
							
                        } else {
                            swal.fire("Can't send email. Try again later, please");
                        }
                    },
                    complete: function (res) {
                       /* $("body").nimbleLoader('hide');*/
                    }
                });


        }
    }
</script>