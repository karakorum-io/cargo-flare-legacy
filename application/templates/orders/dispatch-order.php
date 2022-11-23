<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js"></script>

<script type="text/javascript">



    function paid_by_ach_selected() {

        if ($("#balance_paid_by").val() == 24) {

            $('#fee_type_label_div').show();

            $('#fee_type_div').show();

        } else {

            $('#fee_type_label_div').hide();

            $('#fee_type_div').hide();

        }

    }



    var busy = false;

    var interval_disp = 0;



    function updateInternalNotes(data) {

        var rows = "";

        for (i in data) {

            var email = data[i].email;

            var contactname = data[i].sender;

            if (data[i].system_admin == 2) {

                email = "admin@freightdragon.com";

                contactname = "FreightDragon";

            }

            if ((data[i].access_notes == 0) || data[i].access_notes == 1 || data[i].access_notes == 2 ) {



                var discardStr = '';

                if (data[i].discard == 1) {

					discardStr = ' style="text-decoration: line-through;" ';

				}

                    

                if (data[i].system_admin == 1 || data[i].system_admin == 2) {

                    rows += '<tr class="grid-body"><td style="white-space:nowrap;" class="grid-body-left" >' + data[i].created + '</td><td id="note_' + data[i].id + '_text"  ' + discardStr + '><b>' + decodeURIComponent(data[i].text) + '</b></td><td>';

                } else if (data[i].priority == 2) {

					rows += '<tr class="grid-body"><td class="grid-body-left" >' + data[i].created + '</td><td id="note_' + data[i].id + '_text"  ' + discardStr + '><b style="font-size:12px;color:red;">' + decodeURIComponent(data[i].text) + '</b></td><td>';

				} else {

					rows += '<tr class="grid-body"><td class="grid-body-left">' + data[i].created + '</td><td id="note_' + data[i].id + '_text"  ' + discardStr + '>' + decodeURIComponent(data[i].text) + '</td><td>';

				}



                rows += '<a href="mailto:' + email + '">' + contactname + '</a></td><td style="white-space: nowrap;" class="grid-body-right"  >';

				

                if ((data[i].access_notes == 0) || (data[i].access_notes == 1 && (data[i].sender_id == data[i].memberId)) || data[i].access_notes == 2 ) {

                    if (data[i].sender_id == data[i].memberId && data[i].system_admin == 0) {

						rows += '<img src="<?= SITE_IN ?>images/icons/strike.png" alt="Discard" title="Discard" width="16" height="16" class="action-icon edit-note" onclick="discardNote(' + data[i].id + ')"/>';

					}



                    if (data[i].system_admin == 0 && data[i].access_notes != 0) {

                        rows += '<img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit" width="16" height="16" class="action-icon edit-note" onclick="editInternalNote(' + data[i].id + ')"/>';

                        rows += '<img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete" width="16" height="16" class="action-icon delete-note" onclick="delInternalNote(' + data[i].id + ')"/>';

                    }

                }

            }

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

            success: function (result) {

                if (result.success == true) {

                    updateInternalNotes(result.data);

                } else {

                    Swal.fire("Can't discard note. Try again later, please");

                }

                busy = false;

            },

            error: function (result) {

                Swal.fire("Can't discard note. Try again later, please");

                busy = false;

            }

        });

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

                notes_type: <?= Note::TYPE_INTERNAL ?>

            },

            success: function (result) {

                if (result.success == true) {

                    updateInternalNotes(result.data);

                } else {

                    $("#internal_note").val(text);

                   Swal.fire("Can't save note. Try again later, please");

                }

                busy = false;

            },

            error: function (result) {

                $("#internal_note").val(text);

               Swal.fire("Can't save note. Try again later, please");

                busy = false;

            }

        });

    }



    function delInternalNote(id) {

        if (confirm("Are you sure whant to delete this note?")) {

            if (busy)

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

                        updateInternalNotes(result.data);

                    } else {

                        Swal.fire("Can't delete note. Try again later, please");

                    }

                    busy = false;

                },

                error: function (result) {

                    Swal.fire("Can't delete note. Try again later, please");

                    busy = false;

                }

            });

        }

    }



    function editInternalNote(id) {

        var text = $.trim($("#note_" + id + "_text").text());

        $("#note_edit_form textarea").val(text);

        $("#note_edit_form").dialog({

            width: 400,

            modal: true,

            title: "Edit Internal Note",

            resizable: false,

            buttons: [{

                    text: "Save",

                    click: function () {

                        if ($("#note_edit_form textarea").val() == text) {

                            $(this).dialog("close");

                        } else {

                            if (busy) {

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

                                success: function (result) {

                                    if (result.success == true) {

                                        updateInternalNotes(result.data);

                                        $("#note_edit_form").dialog("close");

                                    } else {

                                        alert("Can't save note. Try again later, please");

                                    }

                                    busy = false;

                                },

                                error: function (result) {

                                    alert("Can't save note. Try again later, please");

                                    busy = false;

                                }

                            });

                        }

                    }

				}, {

                    text: "Cancel",

                    click: function () {

                        $(this).dialog("close");

                        busy = false;

                    }

                }

			]

        }).dialog("open");

    }



    function applySearch(num) {



        var acc_obj = acc_data[num];

        var carrier_dispatch_url = "<?= SITE_IN ?>application/orders/dispatchnew/id/<?= $_GET['id']; ?>/acc/" + acc_obj.id;

        window.location.href = carrier_dispatch_url;

        

    }



    function dispatch() {

		<?php if($_GET['orders'] == 'show') { ?>

        	var entity_id = <?= $_GET['id'] ?>;

		<?php } else { ?>

			if ($("input[name='order_id']:checked").size() == 0) {

				$(".alert-message").empty();

				$(".alert-message").text("Select Order to Dispatch");

				$(".alert-pack").show();

				return false;

			}

			if ($("input[name='order_id']:checked").size() > 1) {

				$(".alert-message").empty();

				$(".alert-message").text("Error: You may dispatch one order at a time.");

				$(".alert-pack").show();

				return false;

			}

			$("#dispatch_dialog .msg-error").hide();

			$("#dispatch_form").each(function () {

				this.reset();

			});

			var entity_id = $("input[name='order_id']:checked").val();

		<?php } ?>



        $("body").nimbleLoader('show');

        $.ajax({

            type: 'POST',

            url: '<?= SITE_IN; ?>application/ajax/entities.php',

            dataType: 'json',

            data: {

                action: 'getDispatchData',

                entity_id: entity_id

            },

            complete: function (response) {

                $("body").nimbleLoader('hide');

            },

            success: function (response) {

                if (response.success) {

                    $("#order_load_date").val(response.data.load_date);

                    $("#order_load_date_type").val(response.data.load_date_type);

                    $("#order_delivery_date").val(response.data.delivery_date);

                    $("#order_delivery_date_type").val(response.data.delivery_date_type);

                    $("#order_ship_via").val(response.data.ship_via);

                    $("#order_carrier_pay").val(response.data.carrier_pay);

                    $("#order_carrier_ondelivery").val(response.data.carrier_ondelivery);

                    $("#order_company_owes_carrier").val(response.data.company_owes_carrier);

                    $("#order_pickup_terminal_fee").val(response.data.pickup_terminal_fee);

                    $("#order_dropoff_terminal_fee").val(response.data.dropoff_terminal_fee);

                    $("#order_balance_paid_by").val(response.data.balance_paid_by);

                    $('#order_include_shipper_comment').prop('checked', response.data.include_shipper_comment == '1');

                    $('#order_notes_from_shipper').val(response.data.notes_from_shipper);

                    $('#payments_terms').val(response.data.payments_terms);

                    $("#pickup_name").val(response.data.pickup_name);

                    $("#pickup_company").val(response.data.pickup_company);

                    $("#pickup_phone1").val(response.data.pickup_phone1);

                    $("#pickup_phone2").val(response.data.pickup_phone2);

                    $("#pickup_cell").val(response.data.pickup_phone_cell);

                    $("#pickup_address1").val(response.data.pickup_address1);

                    $("#pickup_address2").val(response.data.pickup_address2);

                    $("#pickup_city").val(response.data.pickup_city);

                    $("#pickup_state").val(response.data.pickup_state);

                    $("#pickup_zip").val(response.data.pickup_zip);

                    $("#pickup_country").val(response.data.pickup_country);

                    $("#from_booking_number").val(response.data.from_booking_number);

                    $("#from_buyer_number").val(response.data.from_buyer_number);

                    $("#deliver_name").val(response.data.deliver_name);

                    $("#deliver_company").val(response.data.deliver_company);

                    $("#deliver_phone1").val(response.data.deliver_phone1);

                    $("#deliver_phone2").val(response.data.deliver_phone2);

                    $("#deliver_cell").val(response.data.deliver_phone_cell);

                    $("#deliver_address1").val(response.data.deliver_address1);

                    $("#deliver_address2").val(response.data.deliver_address2);

                    $("#deliver_city").val(response.data.deliver_city);

                    $("#deliver_state").val(response.data.deliver_state);

                    $("#deliver_zip").val(response.data.deliver_zip);

                    $("#deliver_country").val(response.data.deliver_country);

                    $("#to_booking_number").val(response.data.to_booking_number);

                    $("#to_buyer_number").val(response.data.to_buyer_number);

                    $("#entity_number").html(response.data.entity_number);



                    $("#dispatch_dialog").dialog({

                        title: 'Dispatch Form',

                        dialogClass: 'dispatch_form_dialog',

                        modal: true,

                        resizable: false,

                        draggable: true,

                        width: 800,

                        buttons: [{

                                text: 'Cancel',

                                click: function () {

                                    $(this).dialog('close');

                                }

                            }, {

                                text: 'Dispatch',

                                click: function () {

                                    var dispatchValues = $("#dispatch_form").serializeArray();

                                    dispatchValues.push({'name': 'action', 'value': 'dispatch'});

                                    dispatchValues.push({'name': 'entity_id', 'value': entity_id});

                                    $(".dispatch_form_dialog").nimbleLoader('show');

                                    $.ajax({

                                        type: 'POST',

                                        url: '<?= SITE_IN ?>application/ajax/entities.php',

                                        dataType: 'json',

                                        data: dispatchValues,

                                        success: function (response) {

                                            if (response.success) {

                                                document.location.reload();

                                            } else {

                                                if (response.errors != undefined) {

                                                    $("#dispatch_dialog .msg-list").html('');

                                                    for (i in response.errors) {

                                                        $("#dispatch_dialog .msg-list").append('<li>' + response.errors[i] + '</li>');

                                                    }

                                                    $("#dispatch_dialog .msg-error").show();

                                                    $("body").scrollTop(0);

                                                } else {

                                                    alert("Can't save dispatch data. Try again later, please");

                                                }

                                            }

                                        },

                                        error: function (response) {

                                            alert("Can't save dispatch data. Try again later, please");

                                        },

                                        complete: function (response) {

                                            $(".dispatch_form_dialog").nimbleLoader('hide');

                                        }

                                    });

                                }

                            }]

                    }).dialog('open');

                } else {

                    alert("Can't load Order data. Try again later, please");

                }

            },

            error: function (response) {

                alert("Can't load Order data. Try again later, please");

            }

        });

    }



    function setEditBlock(type) {

        $.ajax({

            type: "POST",

            url: "<?= SITE_IN ?>application/ajax/entities.php",

            dataType: 'json',

            data: {

                action: 'setBlock',

                entity_id: <?= $this->entity->id ?>,

                type: type

            },

            success: function (response) {

                if (response.success == false) {

                    document.location.reload();

                }

            }

        });

    }



    function expiringEditYes() {

        setEditBlock(0);

        if (redirectUrl != null)

            clearInterval(redirectUrl);

        interval = setInterval(function () {

            checkEditTimeDue()

        }, (60 * 7 * 1000));

        $("#checkEditDueId").dialog("close");

    }



    function expiringEditNo() {

        clearInterval(interval);

        if (redirectUrl != null)

            clearInterval(redirectUrl);

        $("#checkEditDueId").dialog("close");

        var curr_entityid = "<?= $this->entity->id ?>";

        window.location.href = "<?= SITE_IN ?>application/orders/show/id/" + curr_entityid;

    }



    function alertOK() {

        $("#blockedEditAlertId").dialog("close");

    }



    function checkEditTimeDue() {



        $("#checkEditDueId").dialog({

            modal: true,

            width: 500,

            height: 170,

            title: "<p style='color: #f00;font-weight: bold;'>ALERT MESSAGE!!!</p>",

            hide: 'fade',

            resizable: false,

            draggable: false,

            autoOpen: true

        });



        redirectUrl = setInterval(function () {

            tempDisableBeforeUnload = true;

            window.location.href = "<?= SITE_IN ?>application/orders/show/id/<?php echo $this->entity->id; ?>";

		}, (60 * 3 * 1000));

	}

	

	function showhide(element) {

		if ($('#' + element).css('display') == 'none') {

			$('#' + element).css("display", "block");

		} else {

			$('#' + element).css("display", "none");

		}

	}



    $(document).ready(function () {

		var redirectUrl = null;

		var blockedMember = "<?php echo $this->entity->blockedByMember(); ?>";

		var blockedTime = "<?php echo date("H:i:s", strtotime($this->entity->blocked_time)); ?>";

		<?php if (!$this->entity->isBlocked()) { ?>

		setEditBlock(1);

		<?php } else { ?>

		var alertMsg = "<p>" + blockedMember + " is editing this order at this moment, please try again later.</p><div><input type='button' value='OK' onclick='alertOK()' style='margin-left: 40%; width: 65px; height: 29px;color: #008ec2;'></div>";

		

		$("#blockedEditAlertId").dialog({

			modal: true,

			width: 385,

			height: 130,

			title: "Freight Dragon",

			hide: 'fade',

			resizable: false,

			draggable: false,

			autoOpen: true

		});



		$("#blockedEditAlertId p").remove();

		$("#blockedEditAlertId").append(alertMsg);

		window.location.href = "<?= SITE_IN ?>application/orders/show/id/<?php echo $this->entity->id; ?>";

		<?php } ?>

		$("#avail_pickup_date,#order_load_date, #order_delivery_date").datepicker({

			dateFormat: 'mm/dd/yy',

			minDate: '+0'

		});

		$('#carrier_ins_expire').datepicker(datepickerSettings);

		var createForm = $('#dispatch_form');

		createForm.find("#carrier_company").autocomplete({

			source: function (request, response) {},

			minLength: 0,

			autoFocus: true,

			select: function (event, ui) {

				$("#carrier_company").val(ui.item.company_name);

				$("#carrier_id").val(ui.item.id);

				$("#account_id").val(ui.item.id);

				$("#carrier_contact").val(ui.item.contact_name1);

				$("#carrier_type").val(ui.item.carrier_type);

				$("#carrier_phone1").val(ui.item.phone1);

				$("#carrier_phone2").val(ui.item.phone2);

				$("#carrier_fax").val(ui.item.fax);

				$("#carrier_cell").val(ui.item.cell);

				$("#carrier_address").val($.trim(ui.item.address1 + " " + ui.item.address2));

				$("#carrier_print_name").val(ui.item.print_name);

				$("#carrier_insurance_iccmcnumber").val(ui.item.insurance_iccmcnumber);

				$("#carrier_city").val(ui.item.city);

				$("#carrier_state").val(ui.item.state);

				$("#carrier_zip").val(ui.item.zip);

				$("#carrier_country").val(ui.item.country);

				$("#carrier_email").val(ui.item.email);

				return false;

			}

		});

		paid_by_ach_selected();

	});

</script>

<style>

	#carrier_phone1_ext,

	#carrier_phone2_ext

	{

		width:100% !important;

		margin-left:0 !important;

	}

	#payments_terms_carrier

	{

		width:100% !important;

	}

</style>



<?php include(ROOT_PATH.'application/templates/vehicles/edit_js.php'); ?>

<?php include(ROOT_PATH.'application/templates/vehicles/form.php'); ?>

<?php

	$carrier_id = "";

	if(trim($_POST['carrier_id']) != "") {

		$carrier_id = $_POST['carrier_id'];

	} elseif(get_var('account_id') != "") {

		$carrier_id = get_var('account_id');

	} elseif(get_var('acc') != "") {

		$carrier_id = get_var('acc');

	}

?>

<div style="clear:both;"></div>

<div id="checkEditDueId" style="display: none;">

    <br/>

    <p style="margin-left: 10%;font-size: 15px;">Are you still there? (session is about to expire in <span style="color: #f00;font-weight: bold;">60</span> seconds)</p><br/>

    <div>

        <input type="button" value="Yes" onclick="expiringEditYes()" style="margin-left: 35%; width: 65px; height: 29px;color: #008ec2;"><input type="button" value="No" onclick="expiringEditNo()" style="width: 65px; margin-left: 3%;height: 29px;color: #008ec2;">

    </div>	

</div>

<div id="blockedEditAlertId" style="display: none;">

    <p></p>

</div>

<div id="dispatch_form" >

    <div class="msg-error" onclick="$('#dispatch_dialog .msg-error').hide();" style="display: none"><ul class="msg-list"></ul></div>

    <form id="save_order_form" action="<?= getLink('orders/dispatchnew/id/' . $this->entity->id.'/acc/'.get_var('acc')) ?>" method="post" enctype="multipart/form-data">

	

        <input type="hidden" name="carrier_id" id="carrier_id" value="<?php print $carrier_id; ?>"/>

        <input type="hidden" name="account_id" id="account_id" value="<?php print $carrier_id; ?>"/>

        <input type="hidden" name="delivery_credit" id="delivery_credit" value="0"/>     



		<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">

		

			<div id="headingOne " class="hide_show">

				<div class="card-title collapsed">

					<h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Carrier Information</h3>

				</div>

			</div>

			

			<div id="carrier_information" class="pt-3 pb-3" style="padding-left:20px;padding-right:20px;">

				<div class="row">

					<div class="col-4">					

						<div class="new_form-group">

							@carrier_company@

						</div>

					</div>

					<div class="col-4">					

						<div class="new_form-group">

							<label for="carrier_type"><span class="required">*</span>Carrier Type:</label>

							@carrier_type@									

						</div>

					</div>

					<div class="col-4">

						<?= functionButton('Search Carrier', 'selectCarrier()','','btn btn-sm btn_dark_green'); ?>

						<input type="hidden" name="save_carrier" id="save_carrier"  value="0"/>

						<input type="hidden" name="update_carrier" id="update_carrier"  value="0"/>

					</div>

				</div>

					

				<div class="row">

				

					<div class="col-4">					

						<div class="new_form-group">

							@carrier_print_name@

						</div>

					</div>

				

					<div class="col-4">					

						<div class="new_form-group">

							@carrier_contact@

						</div>

					</div>

					

					<div class="col-4">					

						<div class="new_form-group">

							@carrier_driver_name@

						</div>

					</div>

					

				</div>

				

				<div class="row">

					

					<div class="col-4">					

						<div class="new_form-group">

							@carrier_insurance_iccmcnumber@

						</div>

					</div>

				

					<div class="col-4">					

						<div class="new_form-group">

							@carrier_email@

						</div>

					</div>

				

					<div class="col-4">					

						<div class="new_form-group">

							@carrier_driver_phone@

						</div>

					</div>

					

				</div>

				

				<div class="row">

				

					<div class="col-4">					

						<div class="new_form-group">

							@carrier_address@

						</div>

					</div>

				

					<div class="col-4">					

						<div class="new_form-group">

							<div class="row">

								<div class="col-10">

									@carrier_phone1@

								</div>

								<div class="col-2">

									@carrier_phone1_ext@									

								</div>	

							</div>	

						</div>	

					</div>

				

					<div class="col-4">					

						<div class="new_form-group">

							<?php if (isset($this->filesCargo) && count($this->filesCargo)) { ?>									

								<? foreach ($this->filesCargo as $file) { ?>										

									<label>

										<?php print Account::$ins_tupe_name[$file['insurance_type']].": "; ?>

									</label>	

									<ul class="files-list" id="cat">

										<li id="file-<?= $file['id'] ?>">

											<?= $file['img'] ?>

											<!--a href="<?= getLink("accounts", "getdocs", "id", $file['id'], "type", 1) ?>"><?= $file['name_original'] ?></a-->

											<a href="<?= getLink("accounts", "getdocs", "id", $file['id'], "type", 1) ?>"><b>View</b> <?= date("m/d/y", strtotime($file['insurance_expirationdate'])) ?></a>

											&nbsp;&nbsp;&nbsp;

											<a href="#" onclick="return deleteFile('<?php echo getLink("accounts", "delete-file"); ?>', <?php echo $file['id']; ?>);">

												<img src="<?= SITE_IN ?>images/icons/delete.png" alt="delete" style="vertical-align:middle;" width="16" height="16"/>

											</a>

										</li>

									</ul>												

								<?php } ?>

							<?php } ?>

						</div>

					</div>

				</div>



				<div class="row">

				

					<div class="col-4">					

						<div class="new_form-group">

							@carrier_city@									

						</div>

					</div>

				

					<div class="col-4">					

						<div class="new_form-group">

							<div class="row">

								<div class="col-10">

									@carrier_phone2@

								</div>

								<div class="col-2">

									@carrier_phone2_ext@									

								</div>

							</div>

						</div>

					</div>

				

					<div class="col-4">					

						<div class="new_form-group">

							@insurance_type@								

						</div>

					</div>

					

				</div>

				

				<div class="row">

				

					<div class="col-4">					

						<div class="new_form-group new_form_group_2_input">

							@carrier_state@@carrier_zip@

						</div>

					</div>

				

					<div class="col-4">					

						<div class="new_form-group">

							@carrier_fax@

						</div>

					</div>

				

					<div class="col-4">					

						<div class="new_form-group">

							@carrier_ins_doc@

						</div>

					</div>

				</div>

				

				<div class="row">

				

					<div class="col-4">

						<div class="new_form-group">

							@carrier_country@

						</div>

					</div>

					

					<div class="col-4">

						<div class="new_form-group">

							@carrier_cell@									

						</div>

					</div>

					

					<div class="col-4">

						<div class="new_form-group">

							@carrier_ins_expire@						

						</div>

					</div>

				</div>

			</div>

		</div>

		<?php if(isset($_GET['acc'])){ ?>

		<script>

			$(document).ready(function () {

				//initial values

				var carrier_company = $('#carrier_company').val();

				var carrier_print_name = $('#carrier_print_name').val();

				var carrier_insurance_iccmcnumber = $('#carrier_insurance_iccmcnumber').val();

				var carrier_address = $('#carrier_address').val();

				var carrier_city = $('#carrier_city').val();

				var carrier_state = $('#carrier_state').val();

				var carrier_zip = $('#carrier_zip').val();

				var carrier_country = $('#carrier_country').val();

				var carrier_contact = $('#carrier_contact').val();

				var carrier_email = $('#carrier_email').val();

				var carrier_phone1 = $('#carrier_phone1').val();

				var carrier_phone1_ext = $('#carrier_phone1_ext').val();

				var carrier_phone2 = $('#carrier_phone2').val();

				var carrier_phone2_ext = $('#carrier_phone2_ext').val();

				var carrier_fax = $('#carrier_fax').val();

				var carrier_cell = $('#carrier_cell').val();

				$('#carrier_company').blur(function () {

					if ($('#carrier_company').val() != carrier_company) {

						changeHidden();

					}

				});

				$('#carrier_print_name').blur(function () {

					if ($('#carrier_print_name').val() != carrier_print_name) {

						changeHidden();

					}

				});

				$('#carrier_insurance_iccmcnumber').blur(function () {

					if ($('#carrier_insurance_iccmcnumber').val() != carrier_insurance_iccmcnumber) {

						changeHidden();

					}

				});

				$('#carrier_address').blur(function () {

					if ($('#carrier_address').val() != carrier_address) {

						changeHidden();

					}

				});

				$('#carrier_city').blur(function () {

					if ($('#carrier_city').val() != carrier_city) {

						changeHidden();

					}

				});

				$('#carrier_state').blur(function () {

					if ($('#carrier_state').val() != carrier_state) {

						changeHidden();

					}

				});

				$('#carrier_zip').blur(function () {

					if ($('#carrier_zip').val() != carrier_zip) {

						changeHidden();

					}

				});

				$('#carrier_country').blur(function () {

					if ($('#carrier_country').val() != carrier_country) {

						changeHidden();

					}

				});

				$('#carrier_contact').blur(function () {

					if ($('#carrier_contact').val() != carrier_contact) {

						changeHidden();

					}

				});

				$('#carrier_email').blur(function () {

					if ($('#carrier_email').val() != carrier_email) {

						changeHidden();

					}

				});

				$('#carrier_phone1').blur(function () {

					if ($('#carrier_phone1').val() != carrier_phone1) {

						changeHidden();

					}

				});

				$('#carrier_phone1_ext').blur(function () {

					if ($('#carrier_phone1_ext').val() != carrier_phone1_ext) {

						changeHidden();

					}

				});

				$('#carrier_phone2').blur(function () {

					if ($('#carrier_phone2').val() != carrier_phone2) {

						changeHidden();

					}

				});

				$('#carrier_phone2_ext').blur(function () {

					if ($('#carrier_phone2_ext').val() != carrier_phone2_ext) {

						changeHidden();

					}

				});

				$('#carrier_fax').blur(function () {

					if ($('#carrier_fax').val() != carrier_fax) {

						changeHidden();

					}

				});

				$('#carrier_cell').blur(function () {

					if ($('#carrier_cell').val() != carrier_cell) {

						changeHidden();

					}

				});

			});

			function changeHidden() {

				$("#save_carrier").val(1);

				var acc = '<?php echo $_GET['acc']; ?>';

				var update = $("#update_carrier").val();

				if (update != 1) {

					if (acc != '') {



						$("updateCarrier").modal();

					

					}

				}

			}

		</script>			

		<?php } ?>

		<script>

			$(document).ready(function(){

				$("#YES").click(function(){

					$("#update_carrier").val(1);

					$("#save_carrier").val(1);

					$("#updateCarrier").modal('hide');

				});

				$("#NO").click(function(){

					$("#update_carrier").val(1);

					$("#save_carrier").val(1);

					$("#updateCarrier").modal('hide');

				});

			});

		</script>

		<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">

		

			<div id="headingOne " class="hide_show">

				<div class="card-title collapsed">

					<h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Pickup Contact & Location</h3>

				</div>

			</div>

			

			<div id="carrier_information" class="pt-3 pb-3" style="padding-left:20px;padding-right:20px;">

			

				<div class="row">

				

					<div class="col-3">

						<div class="new_form-group_4">

							@pickup_address1@

						</div>

					</div>

					

					<div class="col-3">

						<div class="new_form-group_4">

							@pickup_name@

						</div>

					</div>

					

					<div class="col-3">

						<div class="new_form-group_4 new_form_group_2_input">

							<div class="row">

								<div class="col-10">

									@pickup_phone1@

								</div>

								<div class="col-2">

									@pickup_phone1_ext@

								</div>

							</div>

						</div>

					</div>

					

					<div class="col-3">

						<div class="new_form-group_4">

							@pickup_cell@

						</div>

					</div>

				

				</div>

				

				<div class="row">

				

					<div class="col-3">

						<div class="new_form-group_4">

							@pickup_address2@

						</div>

					</div>

					

					<div class="col-3">

						<div class="new_form-group_4">

							@origin_contact_name2@

						</div>

					</div>

					

					<div class="col-3">

						<div class="new_form-group_4 new_form_group_2_input">

							<div class="row">

								<div class="col-10">

									@pickup_phone2@

								</div>

								<div class="col-2">	

									@pickup_phone2_ext@

								</div>

							</div>

						</div>

					</div>

					

					<div class="col-3">

						<div class="new_form-group_4 ">

							@pickup_cell2@

						</div>

					</div>

					

				</div>

				

				<div class="row">

				

					<div class="col-3">

						<div class="new_form-group_4">

							@pickup_city@

						</div>

					</div>

					 

					<div class="col-3">

						<div class="new_form-group_4">

							@pickup_company@

						</div>

					</div>

					 

					<div class="col-3">

						<div class="new_form-group_4 new_form_group_2_input">

							<div class="row">

								<div class="col-10">

									@origin_phone3@

								</div>

								<div class="col-2">

									@pickup_phone3_ext@

								</div>

							</div>

						</div>

					</div>

					

					<div class="col-3">

						<div class="new_form-group_4">

							@origin_fax@

						</div>

					</div>

					

				</div>

				

				<div class="row"> 

				

					<div class="col-3">

						<div class="new_form-group_4 new_form_group_2_input">

							<div class="row">

								<div class="col-10">

									@pickup_state@

								</div>

								<div class="col-2">

									@pickup_zip@

								</div>

							</div> 

						</div>

					</div>

					

					<div class="col-3">

						<div class="new_form-group_4">

							@origin_auction_name@

						</div>

					</div>

					

					<div class="col-3">

						<div class="new_form-group_4 new_form_group_2_input">

							<div class="row">

								<div class="col-10">

									@pickup_phone4@

								</div>

								<div class="col-2">

									@pickup_phone4_ext@

								</div>

							</div>

						</div>

					</div>

					

					<div class="col-3">

						<div class="new_form-group_4">

							@pickup_fax2@

						</div>

					</div>

					

				</div>

				

				<div class="row">

				

					<div class="col-3">

						<div class="new_form-group_4">

							@pickup_country@

						</div>

					</div>

					

					<div class="col-3">

						<div class="new_form-group_4">

							@from_booking_number@

						</div>

					</div>

					

					<div class="col-3">

						<div class="new_form-group_4">

							

						</div>

					</div>

					

					<div class="col-3">

						<div class="new_form-group_4">

							

						</div>

					</div>

					

				</div>

				

				<div class="row">

				

					<div class="col-3">

						<div class="new_form-group_4">

							@origin_type@

						</div>

					</div>

					

					<div class="col-3">

						<div class="new_form-group_4">

							@from_buyer_number@

						</div>

					</div>

					

					<div class="col-3">

						<div class="new_form-group_4">

							

						</div>

					</div>

					

				</div>

				

				<div class="row">

				

					<div class="col-3">

						<div class="new_form-group_4">

							@origin_hours@

						</div>

					</div>

					

					<div class="col-3">

						<div class="new_form-group_4">

							

						</div>

					</div>

					

					<div class="col-3">

						<div class="new_form-group_4">

							

						</div>

					</div>

					

				</div>

	

			</div>

		</div>		

		<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">

			<div id="headingOne " class="hide_show">

				<div class="card-title collapsed">

					<h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Delivery Contact & Location</h3>

				</div>

			</div>

			<div id="carrier_information" class="pt-3 pb-3" style="padding-left:20px;padding-right:20px;">

				<div class="row">

					

					<div class="col-3">

						<div class="new_form-group_4">

							@deliver_address1@

						</div>

					</div>

					

					<div class="col-3">

						<div class="new_form-group_4">

							@deliver_name@

						</div>

					</div>

					

					<div class="col-3">

						<div class="new_form-group_4 new_form_group_2_input">

							<div class="row">

								<div class="col-10">

									@deliver_phone1@

								</div>

								<div class="col-2">

									@deliver_phone1_ext@

								</div>

							</div>

						</div>

					</div>

					

					<div class="col-3">

						<div class="new_form-group_4">

							@deliver_cell@

						</div>

					</div>

					

				</div>

				<div class="row">

					

					<div class="col-3">

						<div class="new_form-group_4">

							@deliver_address2@

						</div>

					</div>

					

					<div class="col-3">

						<div class="new_form-group_4">

							@destination_contact_name2@

						</div>

					</div>

					 

					<div class="col-3">

						<div class="new_form-group_4 new_form_group_2_input">

							<div class="row">

								<div class="col-10">

									@deliver_phone2@

								</div>

								<div class="col-2">

									@deliver_phone2_ext@

								</div>

							</div>

						</div>

					</div>

					

					<div class="col-3">

						<div class="new_form-group_4">

							@deliver_cell2@

						</div>

					</div>

					

				</div>

				<div class="row">					

					<div class="col-3">

						<div class="new_form-group_4">

							@deliver_city@

						</div>

					</div>

					

					<div class="col-3">

						<div class="new_form-group_4">

							@deliver_company@

						</div>

					</div>

					

					<div class="col-3">

						<div class="new_form-group_4 new_form_group_2_input">

							<div class="row">

								<div class="col-10">

									@destination_phone3@

								</div>

								<div class="col-2">

									@deliver_phone3_ext@

								</div>

							</div>

						</div>

					</div>

					

					<div class="col-3">

						<div class="new_form-group_4">

							@destination_fax@

						</div>

					</div>					

				</div>

				<div class="row">

					<div class="col-3">

						<div class="new_form-group_4 new_form_group_2_input">

							<div class="row">

								<div class="col-10">

									@deliver_state@

								</div>

								<div class="col-2">

									@deliver_zip@

								</div>

							</div>

						</div>

					</div>

					

					<div class="col-3">

						<div class="new_form-group_4">

							@destination_auction_name@

						</div>

					</div>

					<div class="col-3">

						<div class="new_form-group_4 new_form_group_2_input">

							<div class="row">

								<div class="col-10">

									@deliver_phone4@

								</div>

								<div class="col-2">

									@deliver_phone4_ext@

								</div>

							</div>

						</div>

					</div>

					<div class="col-3">

						<div class="new_form-group_4">

							@deliver_fax2@

						</div>

					</div>					

				</div>

				<div class="row">

					<div class="col-3">

						<div class="new_form-group_4">

							@deliver_country@

						</div>

					</div>

					<div class="col-3">

						<div class="new_form-group_4">

							@to_booking_number@

						</div>

					</div>

					<div class="col-3">

						<div class="new_form-group_4">

							

						</div>

					</div>

					<div class="col-3">

						<div class="new_form-group_4">

							

						</div>

					</div>

				</div>

				<div class="row">

					<div class="col-3">

						<div class="new_form-group_4">

							@destination_type@							

						</div>

					</div>

					<div class="col-3">

						<div class="new_form-group_4">

							@to_buyer_number@

						</div>

					</div>

				</div>

				<div class="row">

					<div class="col-3">

						<div class="new_form-group_4">

							@destination_hours@

						</div>

					</div>

				</div>

			</div>

		</div>

		<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">

			<div id="headingOne " class="hide_show">

				<div class="card-title collapsed">

					<h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Shipping Information</h3>

				</div>

			</div>

			<div id="shipping_information" class="pt-3 pb-3" style="padding-left:20px;padding-right:20px;">

				<div class="row">

					<div class="col-12">

						<div class="form-group">

							<strong>Mileage: </strong><?= number_format($this->entity->distance, 0, "", "") ?> mi($ <?= number_format(($this->entity->getCarrierPay(false) / $this->entity->distance), 2, ".", ",") ?>/mi)&nbsp;&nbsp;(<span class='red' onclick="mapIt(<?= $this->entity ->id ?>);">MAP IT</span>)

						</div>

					</div>

				</div>

				<div class="row">

					<div class="col-4">

						<div class="new_form-group">

							@avail_pickup_date@

						</div>

						<div class="new_form-group">

							@order_load_date_type@ @order_load_date@							

						</div>

						<div class="new_form-group new_form_group_2_input">

							@order_delivery_date_type@ @order_delivery_date@

						</div>

						<div class="new_form-group">

							@order_ship_via@

						</div>

					</div>

					<div class="col-8">

						<div class="form-group input_wdh_100_per mb-0">

							@order_notes_from_shipper@

							<input type="hidden" name="order_include_shipper_comment" id="order_include_shipper_comment" value="1" />

						</div>

					</div>

				</div>

			</div>			

		</div>

		<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">

			<div id="headingOne " class="hide_show">

				<div class="card-title collapsed">

					<h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Vehicle Information</h3>

				</div>

			</div>

			<div id="vehicle_information" class="pt-3 pb-3" style="padding-left:20px;padding-right:20px;">

				<table class="table table-bordered" id="vehicles-grid">

                    <thead>

                        <tr>

                            <td>ID</td>

                            <td>Year</td>

                            <td>Make</td>

                            <td>Model</td>

                            <td>Type</td>

                            <td>Vin #</td>

                            <td>Total Tariff</td>

                            <td>Deposit</td>

                            <td>Inop</td>

                            <td>Actions</td>

                        </tr>

                    </thead>

                    <tbody>

                        <?php if (count($this->vehicles) > 0) : ?>

                            <?php foreach ($this->vehicles as $i => $vehicle) : ?>

                                <tr>

                                    <td><?= $this->entity->id ?>-V<?= ($i + 1) ?></td>

                                    <td><?= $vehicle->year ?></td>

                                    <td><?= $vehicle->make ?></td>

                                    <td><?= $vehicle->model ?></td>

                                    <td><?= $vehicle->type ?></td>

									<td><input type="text" name="vin[<?php print $vehicle->id; ?>]" value="<?= $vehicle->vin ?>" class="form-control" id="vin_<?php print $vehicle->id; ?>"  /></td>

                                    <td>

										<input type="text" name="vehicle_tariff[<?php print $vehicle->id; ?>]"  class="form-control" value="<?= $vehicle->tariff ?>" id="vehicle_tariff_<?php print $vehicle->id; ?>" onkeyup="updatePricingInfo();" />

                                    </td>

                                    <td>

										<input type="text" class="form-control" name="vehicle_deposit[<?php print $vehicle->id; ?>]" value="<?= $vehicle->deposit ?>" id="vehicle_tariff_<?php print $vehicle->id; ?>" onkeyup="updatePricingInfo();"/>

                                        <input type="hidden" name="vehicle_id[]" value="<?php print $vehicle->id; ?>"  />

                                    </td>

                                    <td><?= ($vehicle->inop == '1') ? 'Yes' : 'No' ?></td>

                                    <td align="center" class="grid-body-right" width="60">

                                        <?php if (!$this->entity->isBlocked()) { ?>

                                            <img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit"

                                                 onclick="editVehicle(<?= $vehicle->id ?>)" class="action-icon"/>

                                            <img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete"

                                                 onclick="deleteVehicle(<?= $vehicle->id ?>)" class="action-icon"/>

                                        <?php } else { ?>&nbsp; <?php } ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else : ?>

                            <tr>

                                <td colspan="10" align="center"><i>No Vehicles</i></td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

				<div class="form-box-buttons text-right">

					<?php if (!$this->entity->isBlocked()) { ?>

						<?= functionButton('Add Vehicle', 'addVehicle()','','btn btn-sm btn_dark_blue') ?>

					<?php } ?>		   

					<?php if ($this->isAutoQuoteAlowed) { ?>

						<?= functionButton('Quick Price', 'quickPrice()','','btn btn-sm btn_dark_green') ?>

					<?php } ?>

				</div>

			</div>

		</div> 

		<div class="row">

			<div class="col-6">

				<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">

					<div id="headingOne " class="hide_show">

						<div class="card-title collapsed">

							<h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Pricing Information</h3>

						</div>

					</div> 

					<div id="pricing__information" class="pt-3 pb-3" style="padding-left:20px;padding-right:20px;">

						<table class="table pricing_table custom_table_new_info">

							<tr>

								<td>Total Tariff</td>

								<td><span id="total_tariff">@total_tariff@</span>&nbsp;<span class="grey-comment">(Edit carrier pay and deposit under the "Vehicle Information" section)</span>

								</td>

							</tr>

							<tr>

								<td>Required Deposit</td>

								<td><span id="total_deposit">@total_deposit@</span>&nbsp;<span class="grey-comment">(Edit deposit under the "Vehicle Information" section)</span>

								</td>

							</tr>

							<tr>

								<td>Carrier Pay</td>

								<td><span id="carrier_pay">@carrier_pay@</span>&nbsp;<span class="grey-comment">(Edit carrier pay under the "Vehicle Information" section)</span>

								</td>

							</tr>

							<tr>

								<td>@pickup_terminal_fee@&nbsp;<span class="grey-comment">(Do not include fees paid directly from shipper to terminal)</span>

								</td>

							</tr>

							<tr>

								<td>@delivery_terminal_fee@&nbsp;<span class="grey-comment">(Do not include fees paid directly from shipper to terminal)</span>

								</td>

							</tr>

						</table>

					</div>

				</div>

			</div>

			<div class="col-6">

				<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">

					<div id="headingOne " class="hide_show">

						<div class="card-title collapsed">

							<h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Payment Information</h3>

						</div>

					</div>

					<div id="pricing__information" class="pt-3 pb-3" style="padding-left:20px;padding-right:20px;">

						<table class="table custom_table_new_info">

							<tr>

								<td>@balance_paid_by@</td>

							</tr>

							<tr>

								<td id="fee_type_label_div">@fee_type@</td>

							</tr>

							<tr>

								<td>@customer_balance_paid_by@</td>

							</tr>

							<tr>

								<td class="input_wdh_100_per">@payments_terms_carrier@</td>

							</tr>

						</table>

					</div>	

				</div>

			</div>

		</div>

		<?php $notes = $this->notes; ?>

		<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">

			<div id="headingOne " class="hide_show">

				<div class="card-title collapsed">

					<h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Internal Notes</h3>

				</div>

			</div>

			<div id="internal_note__information" class="pt-3 pb-3" style="padding-left:20px;padding-right:20px;">

				<div class="row">

					<div class="col-12">						

						<div class="form-group">

							<textarea class="form-control" width="100%" rows="5" maxlength="1000" id="internal_note"></textarea>

						</div>

					</div>

					<div class="col-12">

						<div class="new_form-group" style="display:inline-block;width:100%;">

							<label>Quick Notes</label>

							<select name="quick_notes" class="form-control pull-left" id="quick_notes" onchange="addQuickNote();" style="max-width:580px;">

								<option value="">--Select--</option>

								<option value="Emailed: Customer.">Emailed: Customer.</option>

								<option value="Emailed: Bad e-mail.">Emailed: Bad e-mail.</option>

								<option value="Faxed: e-Sign.">Faxed: e-Sign.</option>

								<option value="Faxed: B2B.">Faxed: B2B.</option>

								<option value="Faxed: Invoice.">Faxed: Invoice.</option>

								<option value="Faxed: Recepit.">Faxed: Recepit.</option>

								<option value="Phoned: Bad Mobile.">Phoned: Bad Number.</option>

								<option value="Phoned: No Voicemail.">Phoned: No Voicemail.</option>

								<option value="Phoned: Left Message.">Phoned: Left Message.</option>

								<option value="Phoned: No Answer.">Phoned: No Answer.</option>

								<option value="Phoned: Spoke to Customer.">Phoned: Spoke to Customer.</option>

								<option value="Phoned: Spoke to carrier about pick-up.">Phoned: Spoke to carrier about pick-up.</option>

								<option value="Phoned: NSpoke to carrier about drop-off.">Phoned: Spoke to carrier about drop-off.</option>

								<option value="Phoned: Customer requested carrier info.">Phoned: Customer requested carrier info.</option>

								<option value="Phoned: Customer requested damage.">Phoned: Customer requested damage.</option>

								<option value="Phoned: Customer canceled, late pick-up.">Phoned: Customer canceled, late pick-up.</option>

								<option value="Phoned: Customer canceled, no reason given.">Phoned: Customer canceled, no reason given.</option>

								<option value="Phoned: Customer canceled, through e-Mail.">Phoned: Customer canceled, through e-Mail.</option>

								<option value="Phoned: Customer was happy with transport.">Phoned: Customer was happy with transport.</option>

								<option value="Phoned: Customer was un-happy with transport.">Phoned: Customer was un-happy with transport.</option>

								<option value="Phoned: Customer want a refund.">Phoned: Customer want's a refund.</option>

								<option value="Phoned: Not Interested.">Phoned: Not Interested.</option>

								<option value="Phoned: Do Not Call.">Phoned: Do Not Call.</option>

							</select>

							<div style="float:right;"><?= functionButton('Add Note', 'addInternalNote()','','btn btn-sm btn_dark_green') ?></div>

						</div>

					</div>

					<div class="col-12">					

						<table class="table table-bordered" id="internal_notes_table">

							<thead>

								<tr>

									<td><?= $this->order->getTitle('created', 'Date') ?></td>

									<td width="70%">Note</td>

									<td>User</td>

									<td>Action</td>

								</tr>

							</thead>

							<tbody>

								<? if (count($notes[Note::TYPE_INTERNAL]) == 0) : ?>

								<tr>

									<td colspan="4" class="grid-body-left grid-body-right" align="center"><i>No notes available.</i></td>

								</tr>

								<? else : ?>

								<?php foreach ($notes[Note::TYPE_INTERNAL] as $note) : ?>

									<?php

										$sender = $note->getSender();

										$email = $sender->email;

										$contactname = $sender->contactname;

										if ($note->system_admin == 2) {

											$email = "admin@cargoflare.com";

											$contactname = "System";

										}

										if (($_SESSION['member']['access_notes'] == 0 ) || $_SESSION['member']['access_notes'] == 1 || $_SESSION['member']['access_notes'] == 2) {

									?>

									<tr>

										<td style="white-space:nowrap;" <?php if ($note->priority == 2) { ?> style="color:#FF0000"<?php } ?>><?= $note->getCreated("m/d/y h:i a") ?></td>

										<td id="note_<?= $note->id ?>_text" style=" <?php if ($note->discard == 1) { ?>text-decoration: line-through;<?php } ?><?php if ($note->priority == 2) { ?>color:#FF0000;<?php } ?>"><?php if ($note->system_admin == 1 || $note->system_admin == 2) { ?><b><?= $note->getText() ?></b><?php } elseif ($note->priority == 2) { ?><b style="font-size:12px;"><?= $note->getText() ?></b><?php } else { ?><?= $note->getText() ?><?php } ?></td>

										<td style="text-align:left;" <?php if ($note->priority == 2) { ?>style="color:#FF0000"<?php } ?>><a href="mailto:<?= $email ?>"><?= $contactname ?></a></td>

										<td style="white-space: nowrap;" <?php if ($note->priority == 2) { ?>style="color:#FF0000"<?php } ?>>

											<?php if (!$this->entity->readonly) : ?>

											<?php

												if (($_SESSION['member']['access_notes'] == 0 ) ||

														($_SESSION['member']['access_notes'] == 1 && ($note->sender_id == (int) $_SESSION['member_id'])) || $_SESSION['member']['access_notes'] == 2

												) {

													if ($note->sender_id == (int) $_SESSION['member_id'] && $note->system_admin == 0) {

														?>

														<img src="<?= SITE_IN ?>images/icons/strike.png" alt="Discard" title="Discard" width="16" height="16" class="action-icon edit-note" onclick="discardNote(<?= $note->id ?>)"/>	

														<?php

													}

													if ($note->system_admin == 0 && $_SESSION['member']['access_notes'] != 0) {

														?>  

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

								<?php endif; ?>

							</tbody>

						</table>

					</div>

				</div>

			</div>

		</div>

		<div class="text-right">

			<?php print submitButtons(SITE_IN . "application/orders/show/id/", "Dispatch"); ?>

        </div>

        <div class="clear"></div>

    </form>

</div>



<?php if ($_SESSION['parent_id'] == 1) { ?>

<script type="text/javascript">

	function checkDispatch() {

		$("#acc_entity_dispatch_dialog").dialog('option', 'title', 'Payments Terms').dialog('open');

	}

</script>

<?php } ?>



<!--begin::Modal-->

<div class="modal fade" id="updateCarrier" tabindex="-1" role="dialog" aria-labelledby="updateCarrier_model" aria-hidden="true">

	<div class="modal-dialog" role="document">

		<div class="modal-content">

			<div class="modal-header">

				<h5 class="modal-title" id="updateCarrier_model">Update Account Information</h5>

				<button type="button" class="close" data-dismiss="modal" aria-label="Close">

				</button>

			</div>

			<div class="modal-body">

				<p>The carrier information has been modified, would you like to update the current carrier profile?</p> 

			</div>

			<div class="modal-footer">

				<button type="button" class="btn btn-dark btn-sm" id="NO" data-dismiss="modal">No</button>

				<button type="button" id="YES" class="btn btn-sm btn_dark_green">Yes</button>

			</div>

		</div>

	</div>

</div>

<!--fetch carrier detials modal-->
<div class="modal fade" id="carrierFetch" tabindex="-1" role="dialog" aria-labelledby="updateCarrier_model" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="updateCarrier_model">Carriers Found</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				</button>
			</div>
			<div class="modal-body">
				<div class="row" id="searchedCarrier">
                </div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-dark btn-sm" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

	$("#carrier_phone1,#carrier_phone2,#carrier_driver_phone,#carrier_fax,#carrier_cell,#pickup_phone1,#pickup_phone2,#origin_phone3,#pickup_phone4,#pickup_cell,#pickup_cell2,#deliver_phone1,#deliver_phone2,#deliver_phone3,#deliver_phone4,#deliver_cell,#deliver_cell2,#destination_fax,#origin_fax,#pickup_fax2,#destination_phone3,#deliver_fax2").attr("placeholder", "xxx-xxx-xxxx");

    jQuery(function($){



		$("#carrier_phone1").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});

		$("#carrier_phone2").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});

		//$("#carrier_contact").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});



		$("#carrier_cell").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});

		$("#pickup_phone1").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});

		$("#pickup_phone2").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});



		$("#origin_phone3").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});

		$("#pickup_phone4").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});

		$("#pickup_cell").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});



		$("#pickup_cell2").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});

		$("#deliver_phone1").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});

		$("#deliver_phone2").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});



		$("#deliver_phone3").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});

		$("#deliver_phone4").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});

		$("#deliver_cell").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});



		$("#deliver_cell").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});

		$("#deliver_cell2").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});

		$("#destination_fax").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});



		$("#deliver_phone3").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});

		$("#deliver_phone4").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});

		$("#deliver_cell").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});



		$("#origin_fax").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});

		$("#pickup_fax2").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});

		$("#pickup_fax2").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});



		$("#destination_phone3").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});

		$("#deliver_fax2").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});

		$("#pickup_fax2").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});

		$("#carrier_driver_phone").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});

		$("#carrier_fax").mask("999-999-9999",{placeholder:"xxx-xxx-xxxx"});

  	});

    $("#carrier_insurance_iccmcnumber").blur(()=>{
        let mcInsurance = $("#carrier_insurance_iccmcnumber").val();
		<?php
			if(!isset($_GET['acc']) && !isset($_GET['member']) && !isset($_GET['fsmca'])){
		?>
		noDuplicatePolicy(mcInsurance);
		<?php
			}
		?>
    });

	let noDuplicatePolicy = (mcNumber) => {
		$engine.asyncPost(BASE_PATH + "application/ajax/member.php", {
			mc_number: mcNumber,
			action: 'VERIFY_DUPLICATE'
		}, (response) => {
            if (response.success) {
				if(response.exists){
					$engine.confirm("Carrier with this MC-Number exists, use it?", action => {
						if (action === "confirmed") {
							applyExistingCarrier(response.data);
							$("#mcModal").modal('hide');
						} else {
							$("#carrier_insurance_iccmcnumber").val("");
						}
					});
				} else {
					fetchCarrierFromAPI(mcNumber);
				}
            } else {
                $engine.notify("Something went wrong!");
            }
        });
	}

	let fetchCarrierFromAPI = (mcNumber) => {
		$.ajax({
			type: "GET",
			url: "https://saferwebapi.com/v2/mcmx/snapshot/"+ mcNumber,
			dataType: "JSON",
			beforeSend: function(xhr){
				xhr.setRequestHeader('x-api-key', '169ebdecadf6464f9aa24b49638877d4');
			},
			success: function(result) {

				if(result.message){
					$("#mcModal").modal('hide');
					if($("#mcNumberSearch").val().trim() != ""){
						$("#carrier_insurance_iccmcnumber").val($("#mcNumberSearch").val().trim());
					}
				} else {
					let html = `
						<table class="table table-bordered">
							<thead>
								<th>Copmany</th>
								<th>Email</th>
								<th>Phone</th>
								<th>Address</th>
								<th>Action</th>
							</thead>
							<tbody>
					`;

					html += `
					<tr>
						<td>${result.legal_name}</td>
						<td>${result.email ? result.email : ""}</td>
						<td>${result.phone}</td>
						<td>${result.mailing_address}</td>
						<td>
							<button class="btn btn-warning" onclick="applySearchedCarrier('${result.legal_name}', '${result.email ? result.email : ""}', '${result.phone}', '${result.mailing_address}')">
								Use
							</button>
						</td>
					</tr>
					`;
					html += `
							</tbody>
						</table>
					`;

					$("#searchedCarrier").html("").html(html);
					$("#carrierFetch").modal('show');
					$("#mcModal").modal('hide');
				}
			}
		});
	}

	let applyExistingCarrier = (data) => {
		$("#carrier_company").val(data.companyname);
		$("#carrier_print_name").val(data.companyname);
		$("#carrier_contact").val(data.contactname);
		$("#carrier_email").val(data.email);
		$("#carrier_address").val(data.address1 ? data.address1 : "" +" "+ data.address2 ? data.address2 : "");
		$("#carrier_city").val(data.city);
		$("#carrier_state").val(data.state);
		$("#carrier_zip").val(data.zip_code);
		$("#carrier_country").val(data.country);
		$("#carrier_phone1").val(data.phone);
		$("#carrier_phone2").val(data.phone_local);
		$("#carrier_fax").val(data.fax);
		$("#carrier_cell").val(data.phone_cell);
	}

    let applySearchedCarrier = (company, email, phone, fullAddress) => {
        $("#carrier_company").val(company);
        $("#carrier_print_name").val(company);
        $("#carrier_phone1").val(phone);
        $("#carrier_email").val(email);

        let exploded = [];
        exploded = fullAddress.split(" ");
        let fullZip = exploded[exploded.length -1];
        let zip = fullZip.split("-")[0];
        let address = fullAddress.split(",")[0];

        $("#carrier_address").val(address);
        $("#carrier_zip").val(zip);
        $("#carrier_zip").trigger('blur');
        $("#carrierFetch").modal('hide');
    }

	let resetForm = () => {
		// TODO: clear all fields in the form for carrier information. and use it before appying searched and exitisng carrier
	}

</script>

<?php
	/*if(!isset($_GET['acc'])){
?>
	<div class="modal fade" id="mcModal" role="dialog" data-backdrop="static" aria-hidden="true">
		<div class="modal-dialog modal-sm modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<center>
						<p>Please Type in MC Number</p>
						<input type="text" class="form-control" id="mcNumberSearch" placeholder="XXXXXXX">
						<br/>
						<button id="mcNumberSearchButton" class="btn btn-primary">Okay</button>
					</center>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		$("#mcModal").modal('show');

		$("#mcNumberSearch").keyup((event)=>{
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13'){
				if($("#mcNumberSearch").val().trim() != ""){
					noDuplicatePolicy($("#mcNumberSearch").val().trim());
				}
			}
		});

		$("#mcNumberSearchButton").click(()=>{
			if($("#mcNumberSearch").val().trim() != ""){
				noDuplicatePolicy($("#mcNumberSearch").val().trim());
			}
		});
	</script>
<?php
	}*/
?>