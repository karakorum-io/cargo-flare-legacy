<style type="text/css">
    .kt-portlet {
        padding-bottom: 0px !important;
        margin-bottom: 0px;
    }
    .new_btn_info_new_2 .btn {
        margin-top: -4px;
    }
    span.cke_wrapper.cke_ltr {
        background: white !important;
    }
    img.ui-datepicker-trigger {
        width: 18px;
    }
</style>

<script type="text/javascript">

    var anim_busy = false;
    $(document).ready(function () {
		$("#dispatch_date_value").datepicker();
    });

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

    function emailSelectedOrderFormNew() {


        form_id = $("#email_templates").val();
        if (form_id == "") {
           Swal.fire("Please choose email template");
        } else {

              Processing_show();
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

                        $("#maildivnew").modal();
                        $/*("#maildivnew").empty();*/

                        $('.add_one_more_field_').on('click',function(){
                            $('#mailexttra').css('display','block');
                            return false;
                        });
                        $('#singletop').on('click',function(){
                            $('#mailexttra').css('display','none');
                            $('.optionemailextra').val('');
                        });

                        $("#form_id").val(form_id);
                        $("#mail_to_new").val(res.emailContent.to);
                        $("#mail_subject_new").val(res.emailContent.subject);
                        $("#mail_body_new").val(res.emailContent.body);

                          CKEDITOR.instances['mail_body_new'].setData(res.emailContent.body)
                          //Calling CKEDITOR instance #Chetu
                          ckRefresher('new');

                          $("#mail_att_new").html(res.emailContent.att);

                            if(res.emailContent.atttype > 0){
                                $("#attachPdf").attr('checked', 'checked');
                            }else{
                                $("#attachHtml").attr('checked', 'checked');
                            }

                        } else {
                            Swal.fire("Can't send email. Try again later, please");
                        }
                    },
                    complete: function (res) {
                        KTApp.unblockPage();
                    }
                });


        }
    }

    function selectCarrierNewDispatch() {
        Processing_show();
        $("#acc_global_search_result_new_dispatch").html("");
        $("#acc_search_string").val("");
        acc_type = 1;
        $("#acc_search_dialog_new_dispatch").modal();
        KTApp.unblockPage();
    }

    function setStatus(status) {
        Swal.fire({
            title: 'Are you sure?',
            text: "Are you sure want to change order's status?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, change it!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    type: "POST",
                    url: "<?=SITE_IN?>application/ajax/entities.php",
                    dataType: "json",
                    data: {
                        action: 'setStatus',
                        entity_id: <?=$this->entity->id?>,
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
        })

    }

    function changeOrdersStateDetail(status) {

        Swal.fire({
            title: 'Are you sure?',
            text: "Are you sure want to change order's status?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
        if (result.value) {

            var entity_id = <?=$this->entity->id?>;
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
        })

    }

    function reassign() {

        var Assign_val =$("#Assign_id").val();
        if(Assign_val != '') {

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
                    $('#reassign_dialog').find(".error").html("Can't reassign order. Try again later, please.").css('display','block');
                    $("#reassign_dialog div.error").slideDown().delay(2000).slideUp();
                }
            })


        } else {
            $("#reassign_dialog").modal();
            var Assign = 'Assign';
            $("#Assign_id").val(Assign);
        }

        $("#Close").click(function(){
            $("#Assign_id").val('');
        })
    }

    function split() {
       $("#split_dialog").modal();
    }

    function Split_send() {

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
            }
        });
    }

    function sendDispatchLink(id) {
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
            }
        });
    }

    function alertOK(){
       $("#blockedEditAlertIdDetail").dialog("close");
    }

    function showBlockedMessage(blockedUserName){
        var alertMsg = "<p><b>" + blockedUserName + "</b> is editing this order at this moment, please try again later.</p>" ;

        $.ajax({
            type: "POST",
            url: BASE_PATH + "application/ajax/entities.php",
            dataType: "json",
            data: {
                action: "checkBlock",
                entity_id: <?php echo $this->entity->id; ?>
            },
            success: function (res) {
                if (res.success) {
                    window.location.href = "<?=SITE_IN?>application/orders/edit/id/<?php echo $this->entity->id; ?>";
                } else {
                    $engine.notify(alertMsg);
                }
            },
            complete: function (res) {
            }
        });
    }
</script>

<div id="blockedEditAlertIdDetail" style="display: none;"></div>

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
                <button type="button" class="btn btn_bright_blue btn-sm" onclick="Split_send()">Split</button>
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
                    <?php 
                    if ($member->status == "Active") {
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
                <button type="button" id="Assign_id" value="" onclick="reassign()" class="btn btn_bright_blue btn-sm">Assign</button>
            </div>
        </div>
    </div>
</div>
<!--end::Modal-->

<div id="dispatch_date_dialog" style="display: none;">
    <br/>
    <input id="dispatch_date_value" size="10"/>
</div>

<div class="alert alert-light alert-elevate" style="margin-left: 11px;margin-right: 11px;margin-bottom: 20px; padding-bottom: 0px !important;">
    <div class="row">
		<div class="col-12">
			<ul class="nav nav-tabs nav-tabs-line nav-tabs-line-3x nav-tabs-line-success" role="tablist" style="margin-bottom:0">
				<li class="nav-item">
					<a class="nav-link <?=(@$_GET['orders'] == 'show') ? " active" : ""?>" href="<?=SITE_IN?>application/orders/show/id/<?=$this->entity->id?>">Order Details</a>
				</li>
				<?php if (($this->entity->status != Entity::STATUS_ARCHIVED)): ?>
				<?php
                    $blockedBy = "";
                    $locationHref = "location.href = '" . SITE_IN . "application/orders/edit/id/" . $this->entity->id . "'";
                    $locationHrefDispatch = "searchCarrierWizard()";
                    if ($this->entity->isBlocked()) {
                        $blockedBy = $this->entity->blockedByMember();
                        $locationHref = "showBlockedMessage('" . $blockedBy . "');";
                        $locationHrefDispatch = "showBlockedMessage('" . $blockedBy . "');";
                    }
                ?>
				<li class="nav-item">
					<a class="nav-link <?=(@$_GET['orders'] == 'edit') ? " active" : ""?>" href="<?=SITE_IN?>application/orders/edit/id/<?=$this->entity->id?>">Edit Order</a>
				</li>
				<?php endif;?>
				<li class="nav-item">
					<a class="nav-link <?=(@$_GET['orders'] == 'history') ? " active" : ""?>" href="<?=SITE_IN?>application/orders/history/id/<?=$this->entity->id?>">History</a>
				</li>
				<?php 
                    if ($_SESSION['member']['access_payments']): //$this->entity->status != Entity::STATUS_ARCHIVED ?>
                    <li class="nav-item">
                        <a class="nav-link <?=(@$_GET['orders'] == 'payments') ? " active" : ""?>" href="<?=SITE_IN?>application/orders/payments/id/<?=$this->entity->id?>">Payments</a>
                    </li>
                <?php endif;?>
				<li class="nav-item">
					<a class="nav-link <?=(@$_GET['orders'] == 'uploads') ? " active" : ""?>" href="<?=SITE_IN?>application/orders/uploads/id/<?=$this->entity->id?>">Documents</a>
				</li>
				<?php if (!is_null($this->entity->dispatched) && (strtotime($this->entity->dispatched) != 0) && $_SESSION['member']['access_dispatch']) {?>
				<li class="nav-item">
					<a class="nav-link <?=(@$_GET['orders'] == 'dispatch') ? " active" : ""?>" href="<?=SITE_IN?>application/orders/dispatch/id/<?=$this->entity->id?>">Dispatch Sheet</a>
				</li>
				<?php }?>
				<li class="nav-item">
					<a class="nav-link <?=(@$_GET['orders'] == 'matchcarrier') ? " active" : ""?>" href="<?=SITE_IN?>application/orders/matchcarrier/id/<?=$this->entity->id?>">Carrier Match</a>
				</li>
				<li class="nav-item">
					<a class="nav-link <?=(@$_GET['orders'] == 'review') ? " active" : ""?>" href="<?php echo SITE_IN; ?>application/orders/review/id/<?=$this->entity->id;?>">Review</a>
				</li>
                <?php
                    if ($this->entity->status == 6 || $this->entity->status == 7 || $this->entity->status == 8 || $this->entity->status == 9) {
                ?>
                <li class="nav-item">
                    <a href="<?php echo SITE_IN; ?>application/orders/track_n_trace/id/<?=$this->entity->id;?>" class="nav-link <?=(@$_GET['orders'] == 'track_n_trace') ? " active" : ""?>">Track & Trace</a>
                </li>
                <?php
                    }
                ?>
                <li class="nav-item">
					<a class="nav-link <?=(@$_GET['orders'] == 'mail_history') ? " active" : ""?>" href="<?php echo SITE_IN; ?>application/orders/mail_history/id/<?=$this->entity->id;?>">Mail History</a>
				</li>
			</ul>
		</div>
    </div>
</div>

<div class="kt-portlet" style="margin-left: 11px;margin-right: 11px;margin-bottom: 0px; padding-bottom: 0px !important;">
    <div class="kt-portlet__body">
        <div class="row">
            <div class="col-12 col-sm-3 text-left"></div>

			<div class="col-12 col-sm- new_btn_info_new_2 text-right">
                <?php
                    if($this->entity->status == 99){
                ?>
                <?php echo functionButton('Confirm Order', 'confirmOrder('.$this->entity->id.')', '', 'btn  btn_bright_blue btn-sm')?>
                <?php
                    }
                ?>
                
                <?=functionButton('Print', 'printSelectedOrderForm()', '', 'btn  btn_bright_blue btn-sm')?>
                <?php echo $this->form_templates; ?>
                <?=functionButton('Email', 'emailSelectedOrderFormNew()', '', 'btn_bright_blue btn-sm')?>
                @email_templates@
                
                <?php if (($this->entity->status != Entity::STATUS_ARCHIVED) && !$this->entity->readonly) {?>

					<?php if (is_null($this->entity->dispatched)) {?>
						<?php echo functionButton('Dispatch', $locationHrefDispatch, '', 'btn_bright_blue btn-sm')?>
					<?php }?>

					<?php
                        if ($this->entity->status == Entity::STATUS_DELIVERED || $this->entity->status == Entity::STATUS_PICKEDUP || $this->entity->status == Entity::STATUS_ISSUES) {

                            $stateOrders = Entity::STATUS_PICKEDUP;
                            if ($this->entity->status == Entity::STATUS_PICKEDUP) {
                                $stateOrders = Entity::STATUS_DISPATCHED;
                            } elseif ($this->entity->status == Entity::STATUS_ISSUES) {
                                $stateOrders = Entity::STATUS_PICKEDUP;
                            }
                    ?>

					<?php print functionButton('Previous Status', 'changeOrdersStateDetail(' . $stateOrders . ')');?>

					<?php }?>

					<?=functionButton('Reassign', 'reassign()', '', 'btn_bright_blue btn-sm')?>

					<?php if (in_array($this->entity->status, array(Entity::STATUS_NOTSIGNED, Entity::STATUS_PICKEDUP, Entity::STATUS_DISPATCHED))) {?>
						<?php $dispatchID = "";if ($this->dispatchSheet->id != "") {$dispatchID = $this->dispatchSheet->id;} else { $dispatchID = $this->dispatch->id;}?>
						<button class="btn btn_bright_blue btn-sm" onclick="$operations.cancelDispatch(<?php echo $_GET['id']; ?>)" type="button" style="" id="Undispatch">Undispatch</button>
					<?php }?>

					<?php if ($this->entity->status == Entity::STATUS_ACTIVE) {?>
						<?=functionButton('On Hold', 'setStatus(' . Entity::STATUS_ONHOLD . ')', '', 'btn_dark_green btn-sm')?>
					<?php } elseif ($this->entity->status == Entity::STATUS_ONHOLD) {?>
						<?=functionButton('Remove Hold', 'setStatus(' . Entity::STATUS_ACTIVE . ')')?>
					<?php } elseif ($this->entity->status == Entity::STATUS_PICKEDUP) {?>

                        <?php
                            if($this->entity->status == Entity::STATUS_PICKEDUP) {
                        ?>
                        <div class="btn btn_bright_blue btn-sm" onclick="$('#delivered_button').focus();">
                            <input type="text" id="delivered_button" value="" name="delivered_button" style="opacity: 0;width: 0;" >
                            Delivered Date
                            <i class="fa fa-calendar" onclick="$('#delivered_button').focus();" aria-hidden="true"></i>
                        </div>
                        <script>
                            $('#delivered_button').datepicker().on('changeDate', function (ev) {
                                $(this).datepicker('hide');
                                setPickedUpStatusAndDateByEntity(9, $('#delivered_button').val(), <?php echo $this->entity->id;?>);
                            });
                        </script>
                        <?php
                            }
                        ?>
					<?php } elseif ($this->entity->status == Entity::STATUS_DISPATCHED) {?>
                        <div class="btn btn_bright_blue btn-sm" onclick="$('#pickup_button').focus();">
                            <input type="text" id="pickup_button" value="" name="pickup_button" style="opacity: 0;width: 0;" >
                            Picked Up Date
                            <i class="fa fa-calendar" onclick="$('#pickup_button').focus();" aria-hidden="true"></i>
                        </div>
                        <script>
                            $('#pickup_button').datepicker().on('changeDate', function (ev) {
                                $(this).datepicker('hide');
                                setPickedUpStatusAndDateByEntity(8, $('#pickup_button').val(), <?php echo $this->entity->id;?>);
                            });
                        </script>

						<?php } elseif ($this->entity->status == Entity::STATUS_DELIVERED) {?>
							<?=functionButton('Delivered Date', 'setDispatchedDate(\'actual_ship_date\')')?>
						<?php }?>

						<?php if ($this->entity->status == Entity::STATUS_ACTIVE) {?>
							<?=functionButton('Post Load', 'postOrderToFB(' . $this->entity->id . ')', '', 'btn  btn_bright_blue btn-sm')?>
						<?php }?>

						<?php if ($this->entity->status == Entity::STATUS_POSTED) {?>
							<?=functionButton('Repost', 'repostOrderFromFB(' . $this->entity->id . ')', '', 'btn_bright_blue btn-sm')?>
							<?=functionButton('Unpost', 'unpostOrderFromFB(' . $this->entity->id . ')', '', 'btn_bright_blue btn-sm')?>
						<?php }?>

                    <?php } else {?>
                        <?=functionButton('Reassign', 'reassign()', '', 'btn_bright_blue btn-sm')?>
                        
                        <?php
                            $stateOrders = Entity::STATUS_PICKEDUP;
                        ?>

                        <?php 
                            if($this->entity->status != Entity::STATUS_DELIVERED){
                                print functionButton('Uncancel', 'setStatus(1)', '', 'btn-dark btn-sm');
                            }
                        ?>
                            
                    <?php }?>

					<?php if ($this->entity->status != Entity::STATUS_ARCHIVED && $this->entity->status != Entity::STATUS_ISSUES && $this->entity->status != Entity::STATUS_DELIVERED) {?>
						<?=functionButton('Cancel', 'CancelThisOrder(' . Entity::STATUS_ARCHIVED . ')', '', 'btn-dark btn-sm')?>
					<?php }?>

                    <?php
                        if ($this->entity->status == Entity::STATUS_POSTED) {
                    ?>
                    <select id="pending-dispatch" onchange="$operations.handlePendingDispatch(this, <?php echo $_GET['id']?>)" style="width:150px; height:32px;border-color: #ccc; padding-left:5px;">
                        <option value="0">Pending Dispatch</option>
                        <option value="add">Add</option>
                        <option value="remove">Remove All</option>
                    </select>
                    <?php }?>
                    <?php 
                        $vchls = $this->entity->getVehicles();
						$vcntr = 0;
						foreach($vchls as $v) {
                            $vcntr = $vcntr + 1;
                        }

                        if($vcntr > 1){
                    ?>
                    <?=functionButton('Split Order', 'split()', '', 'btn-dark btn-sm')?>
                    <?php
                        }
                    ?>

			</div>
        </div>
    </div>
</div>

<!--Pending dispatch modal-->
<div class="modal fade" id="pending-dispatch-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Mark Pending Dispatch</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times" aria-hidden="true"></i></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <input type="text" id="pdName" placeholder="Name" class="form-control">
                    </div>
                    <div class="col-12 col-sm-6">
                        <input type="text" id="pdContact" placeholder="Contact" class="form-control">
                    </div>
                </div>
                <br/>
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <input type="text" id="pdPhone" placeholder="Phone" class="form-control">
                    </div>
                    <div class="col-12 col-sm-6">
                        <input type="text" id="pdEmail" placeholder="Email" class="form-control">
                    </div>
                </div>
                <br/>
                <div class="row">
                    <div class="col-12 col-sm-12">
                        <textarea type="text" id="pdComment" placeholder="Comment" class="form-control" row="5"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark btn-sm" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn_light_green btn-sm " onclick="$operations.addToPendingDispatch(<?php echo $_GET['id']?>)">Add</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

	function emailSelectedOrderForm() {
		console.log('email');

		form_id = $("#email_templates").val();
		if (form_id == "") {
			swal.fire("Please choose email template");
			} else {

			if (confirm("Are you sure want to send Email?")) {
			 Processing_show();
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
				});
			}
		}
	}

	function ckRefresher(id){

		if (CKEDITOR.instances["mail_body_" + id]){

			CKEDITOR.instances["mail_body_" + id].destroy();
		}
		CKEDITOR.replace("mail_body_" + id, {
		toolbar:
			[{name: 'document', groups: ['mode'], items: ['Source'] },
			{ name: 'colors', items: ['TextColor', 'BGColor'] },
			{ name: 'basicstyles', groups: ['basicstyles', 'cleanup'], items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat'] },
			]
		});
	}

	function maildivnew_send(){

		var sEmail = [$('#mail_to_new').val(), $('.optionemailextra').val(), $('#mail_cc_new').val(), $('#mail_bcc_new').val()];
		console.log("dd",Email);
		if (validateEmail(sEmail) == false) {
			swal.fire('Invalid Email Address');
			return false;
		}
		if ($('#attachPdf').is(':checked')) {
			attach_type = $('#attachPdf').val();
		} else {
			attach_type = $('#attachHtml').val();
		}

		$("#maildivnew").find('.modal-body').addClass('kt-spinner kt-spinner--v2 kt-spinner--lg kt-spinner--dark');
		$.ajax({
			url: BASE_PATH + 'application/ajax/entities.php',
			data: {
				action: "emailOrderNewSend",
				form_id: $('#form_id').val(),
				entity_id: $('#entity_id').val(),
				mail_to: $('#mail_to_new').val(),
				mail_cc: $('#mail_cc_new').val(),
				mail_bcc: $('#mail_bcc_new').val(),
				mail_extra: $('.optionemailextra').val(),
				mail_subject: $('#mail_subject_new').val(),
				mail_body: $('#mail_body_new').val(),
				attach_type: attach_type
			},
			type: 'POST',
			dataType: 'json',
			beforeSend: function () {
				if ($('#mail_to_new').val() == "" || $('#mail_subject_new').val() == "" || $('#mail_body_new').val() == "") {
					swal.fire('Empty Field(s)');
					return false;
				}
				;
			},
			success: function (response) {
				$("#maildivnew").removeClass('kt-spinner kt-spinner--v2 kt-spinner--lg kt-spinner--dark');
				// $("body").nimbleLoader("hide");
				if (response.success == true) {
					// $("#maildivnew").dialog("close");
					$("#maildivnew").modal('hide');
					clearMailForm();
				}
			},
			complete: function () {
				$("#maildivnew").find('.modal-body').removeClass('kt-spinner kt-spinner--v2 kt-spinner--lg kt-spinner--dark');
			}
		});
	}

	function printCheckForm(entity) {
		$.ajax({
			url: BASE_PATH + 'application/ajax/entities.php',
			data: {
				action: "print_check",
				order_id: '<?=$_GET["id"];?>'
			},
			type: 'POST',
			dataType: 'json',
			beforeSend: function () {
			},
			success: function (retData) {
				window.open("<?php print SITE_IN;?>external/print_check.php?ent="+entity,"","toolbar=yes,scrollbars=yes, resizable=yes,HEIGHT=700,WIDTH=800")
			}
		});
	}
</script>

<script type="text/javascript">
    var imgpath = '<?php print SITE_IN;?>images/date_picker_798622.png';
    $('#imgremove').find('img').attr('src',imgpath);
</script>

<!--Including CancellationPopup UI-->
<? include(TPL_PATH . "orders/CancellationPopup.php"); ?>