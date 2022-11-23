<script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script> 
<script type="text/javascript" src="<?php echo SITE_IN ?>ckeditor/ckeditor.js"></script>

<style>
    .cd-secondary-nav {
        position: static;
    }
    .cd-secondary-nav .is-visible {
        visibility: visible;
        transform: scale(1);
        transition: transform 0.3s, visibility 0s 0s;
    }
    .cd-secondary-nav.is-fixed {
        z-index: 9999;
        position: fixed;
        left: auto;
        top: 0;
        width: 1200px;
        background-color:#f4f4f4; 

    }
    button-text-only
    {
        color: white;
        background: #5578eb;    
    }
    .new_btn_info_new_2 .btn
    {
        margin-top:-4px;
    }
    #lead_check_wrapper
    {
        width:100%;
    }
    .edit-mail-action.update.btn-sm.btn.btn_dark_green {
        width: 82px;
        margin: -1px;
    }
    .edit-mail-action.cancel.btn-sm.btn-dark.btn {
        width: 100;
        /* padding-bottom: 7px; */
    }
    .edit-mail-action.send-mail-lead.btn.btn-sm.btn_dark_green {
        width: 100;
        margin-bottom: 14px;
    }
    span.cke_wrapper.cke_ltr {
        background: #033254 !important;
    }
</style>

<div id="appointmentDiv">
    <table cellspacing="2" cellpadding="0" border="0">
        <tr>
            <td valign="top">@app_date@</td>
        </tr>
        <tr>
            <td valign="top">@app_time@</td>
        </tr>
        <tr>
            <td valign="top">@app_note@</td>
        </tr>
    </table>
</div>

<script type="text/javascript">
    
    $('.add_one_more_field_').on('click', function () {
        $('#mailexttra').css('display', 'block');
        return false;
    });

    $('#singletop').on('click', function () {
        $('#mailexttra').css('display', 'none');
        $('.optionemailextra').val('');
    });

    function validateEmail(sEmail) {
        var res = "", res1 = "", i;
        var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
        for (i = 0; i < sEmail.length; i++) {
            if (filter.test(sEmail[i])) {
                res += sEmail[i];
            } else {
                res1 += sEmail[i];
            }
        }
        if (res1 !== '') {
            return false;
        }
    }

    function printSelectedQuoteForm() {
        if ($(".entity-checkbox:checked").length == 0) {
          Swal.fire('Quote not selected');
            return;
        }

        if ($(".entity-checkbox:checked").length > 1) {
            Swal.fire('Please select one quote');
            return;
        }

        var quote_id = $(".entity-checkbox:checked").val();
        form_id = $("#form_templates").val();

        if (form_id == "") {
            Swal.fire('Please choose form template');
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
    
    function saveQuotes(email) {
        var ajData = [];
        $("#lead_check tbody [type='checkbox']:checked").each(function(){
            if ($("#lead_tariff_" + $(this).val()).val() > 0)  {
                ajData.push('{"entity_id":"' + $(this).val() + '","tariff":"' + $('#lead_tariff_' + $(this).val()).val() + '","deposit":"' + $('#lead_deposit_' + $(this).val()).val() + '"}');
            }
        });
          
        var lead_check = $("#lead_check tbody [type='checkbox']:checked");
        
        if (lead_check.length == 0) {
            Swal.fire({
                type: 'error',
                title: 'Oops...',
                html: 'You have no quote data!'
            })
            return;
        } else {
            Processing_show()
            $.ajax({
                type: "POST",
                url: "<?= SITE_IN ?>application/ajax/entities.php",
                dataType: 'json',
                data: {
                    action: 'saveQuotesNew',
                    email: email,
                    data: "[" + ajData.join(",") + "]"
                },
                success: function (res) {
                    if (res.success) {
                        document.location.href = document.location.href;
                    } else {
                        Swal.fire("Can't save Quote(s)");
                    }
                },
                complete: function (response) {
                    KTApp.unblockPage();
                }
            });
        }
    }

    function convertToOrder() {
        
        if ($(".entity-checkbox:checked").length == 0) {
            Swal.fire('You have no selected items');
            return false;
        }

        if ($(".entity-checkbox:checked").length > 1) {
            Swal.fire({
                type: 'error',
                title: 'Oops...',
                text: 'Error: You may convert one lead at a time.',
            })
            return false;
        }

        var entity_ids = [];
        $(".entity-checkbox:checked").each(function () {
            entity_ids.push($(this).val());
        });

        Processing_show();
        $.ajax({
            type: "POST",
            url: "<?= SITE_IN ?>application/ajax/entities.php",
            dataType: "json",
            data: {
                action: "LeadtoOrderNew",
                entity_ids: entity_ids.join(',')
            },
            success: function (result) {
                    KTApp.unblockPage();
                if (result.success == true) {
                    document.location.href = result.url;
                } else {
                    Swal.fire("Can't convert Order. Try again later, please")
                }
            },
            error: function (result) {
                KTApp.unblockPage();
                Swal.fire("Can't convert Order. Try again later, please")
            }
        });
    }

    function getVehicles(id) {
        if ($("#vehicles-info-" + id).css('display') == 'block') {
            $("#vehicles-info-" + id).toggle();
        } else {
            $.ajax({
                type: "POST",
                url: BASE_PATH + "application/ajax/vehicles.php",
                dataType: 'json',
                data: {
                    action: 'getVehicles',
                    id: id
                },
                success: function (res) {
                    if (res.success) {
                        $("#vehicles-info-" + id).toggle();
                        $("#vehicles-info-" + id).html(res.data);
                    } else {
                        Swal("Vehicles not found.");
                    }
                }
            });
        }
    }

    function reassignOrdersDialog() {
        if ($(".entity-checkbox:checked").length == 0) {
            Swal.fire({
                type: 'error',
                title: 'Oops...',
                text: 'Leads not selected'
            });
            return;
        }

        $("#kt_modal_5").modal();
    }

    function Processing_show() {
        KTApp.blockPage({
            overlayColor: '#000000',
            type: 'v2',
            state: 'primary',
            message: '.'
        });
    }

    function reassignOrders_submit() {
		var member_id = $("#company_members").val();
		reassignOrders(member_id);
	}

    function reassignOrders(member) {

        $(".error_reassing").css('display','block');
        $('#kt_modal_5').find('.error_reassing').html('');
        
        var member_id = 0;
        member_id = member;

        if (member_id == 0) {
            $ (".error_reassing").css('display','block');
            $('#kt_modal_5').find('.error_reassing').html('You must select member to assign');
            return;
        }

        if ($(".entity-checkbox:checked").length == 0) {
            Swal.fire({
                type: 'warning',
                title: 'Oops...',
                text: 'Leads not selected'
            });
            return;
        }

        var entity_ids = [];

        $(".entity-checkbox:checked").each(function () {
            entity_ids.push($(this).val());
        });

        Processing_show();

        $.ajax({
            type: 'POST',
            url: '<?= SITE_IN ?>application/ajax/entities.php',
            dataType: "json",
            data: {
                action: 'reassign',
                assign_id: member_id,
                entity_ids: entity_ids.join(',')
            },
            success: function (response)
            {
                if (response.success) {
                    window.location.reload();
                } else {
                    $(".error_reassing").css('display','block');
                    $('#kt_modal_5').find('.error_reassing').html('Reassign failed. Try again later, please');
                    KTApp.unblockPage();
                }
            },
            error: function (response) {
                $(".error_reassing").css('display','block');
                $('#kt_modal_5').find('.error_reassing').html('Reassign failed. Try again later, please');
                KTApp.unblockPage();
            },
            complete: function (res) {
                KTApp.unblockPage();
            }
        });
    }

    function setAppointment() {
        if ($(".entity-checkbox:checked").length == 0) {
            Swal.fire("Leads not selected");
            return;
        }
        $("#appointmentDiv").dialog("open");
    }

    function setAppointmentData(app_date, app_time, notes) {

        if (app_date == '') {
            Swal.fire("You select appointment date.");
            return;
        }
        if (app_time == '') {
            Swal.fire("You select appointment time.");
            return;
        }
        if ($(".entity-checkbox:checked").length == 0) {
            Swal.fire("Leads not selected");
            return;
        }

        var entity_ids = [];
        $(".entity-checkbox:checked").each(function () {
            entity_ids.push($(this).val());
        });

        $.ajax({
            type: 'POST',
            url: '<?= SITE_IN ?>application/ajax/entities.php',
            dataType: "json",
            data: {
                action: 'setappointment',
                app_date: app_date,
                app_time: app_time,
                notes: notes,
                entity_ids: entity_ids.join(',')
            },
            success: function (response)
            {
                if (response.success) {
                    window.location.reload();
                } else {
                    swal.fire("Set appointment failed. Try again later, please.");
                }
            },
            error: function (response) {
                swal.fire("Set appointment. Try again later, please.");
            },
            complete: function (res) {
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
                var notes = $("#app_note").val();
                setAppointmentData(app_date, app_time, notes)
            },
            "Cancel": function () {
                $(this).dialog("close");
            }
        }
    });

    $(document).ready(function () {
        $("#app_date").datepicker({
            dateFormat: "yy-mm-dd",
            minDate: '+0'
            //setDate: "2012-10-09",
        });
    });

    function sendBulkMail() {

        if ($(".entity-checkbox:checked").length == 0) {
            Swal.fire('Please select order.');
            return false;
        }

        var entity_ids = [];
        $(".entity-checkbox:checked").each(function () {
            var entity_id = $(this).val();
            entity_ids.push(entity_id);
        });

        entity_ids.join(",");
        var form_id = $("#email_templates").val();
        
        if (form_id == "") {
            Swal.fire("Please choose email template.");
            return false;
        } else {
            $.ajax({
                type: 'POST',
                url: BASE_PATH + 'application/ajax/newentry.php',
                dataType: 'json',
                data: {
                    action: 'sendBulkMail',
                    form_id: form_id,
                    entity_id: entity_ids
                },
                success: function (response) {
                    if (response.success == true) {
                        Swal.fire('mails sent.');
                    }
                }
            });
        }
    }
</script>

<div style="display:none" id="notes">notes</div>

<div class="kt-portlet">
	<div class="kt-portlet__body">
		<div>
			<?php if ($this->status != Entity::STATUS_ARCHIVED || $_GET['leads'] == "search") { ?>
			<div class="row">
				<div class="col-lg-12 text-right buttion_mar new_btn_info_new_2">
					<?php
                        if ($_GET['leads'] == "quoted" || $_GET['leads'] == "follow"  || $_GET['leads'] == "appointment" || $_GET['leads'] == "expired" ) {
					?>  
					    <?php if ($_SESSION['member']['parent_id'] == 159) { ?>
					        <?= functionButton('Bulk Mail', 'sendBulkMail()') ?>
					    <?php } ?>
					    <?= functionButton('Print', 'printSelectedQuoteForm()','','btn_bright_blue btn-sm') ?>
					    @form_templates@
					    <?php print functionButton('Email', 'emailSelectedQuoteFormNew()','','btn_bright_blue btn-sm'); ?>
					    @email_templates@
					<?php
					    } else if($_GET['leads'] == "") {
					?>                           
					    <?php echo functionButton('Auto Quote', 'autoQuoteImportedLeads()','','btn_bright_blue  btn-sm'); ?>
					<?php
					    }
					?>
					<?= functionButton('Reassign Leads', 'reassignOrdersDialog()','','btn_bright_blue btn-sm btn-info') ?>
					<?php if ($this->status == Entity::STATUS_LFOLLOWUP && $_SESSION['parent_id'] == 1) { ?>
					<?= functionButton('Set Appointment', 'setAppointment()','','btn-sm btn_bright_blue') ?>
					<?php } ?>
					<?php
					if ($this->status != Entity::STATUS_LQUOTED &&
					$this->status != Entity::STATUS_LFOLLOWUP &&
					$this->status != Entity::STATUS_LEXPIRED &&
					$this->status != Entity::STATUS_LAPPOINMENT && $_GET['leads'] != 'onhold') {
					?>
					<?= functionButton('Convert to Quote(s)', 'saveQuotes(0)','',' btn_bright_blue btn-sm') ?>
					<?php } ?>
					<?php if ($_GET['leads'] != '' && $_GET['leads'] != 'onhold') { ?>
					
					<?= functionButton('Convert to Order', 'convertToOrder()','',' btn_bright_blue btn-sm') ?>

					
					<?php } ?>
					<?php if ($_GET['leads'] == 'onhold') { ?>
					<?= functionButton('Remove Hold', 'changeStatusLeads(' . Entity::STATUS_LFOLLOWUP . ')','','btn_bright_blue btn-sm') ?>
					<?php } else { ?>
					<?= functionButton('Hold', 'placeOnHold()','',' btn_bright_blue btn-sm') ?>
					<?php } ?>
					<?= functionButton('Cancel', 'cancel()','',' btn-dark btn-sm') ?>
					
					<?php } else { ?>
						
						<div class="row">
							<div class="col-lg-12 text-right">
								<div class="Uncancel">
									<?= functionButton('Uncancel', 'changeStatusLeads(1)','','btn-dark btn-sm') ?>
								</div>
							</div>
						</div>

					<?php } ?>
				</div>
			</div>

			<!-- Table start here -->

            <div class="kt-portlet__body">
			<table id="lead_check" class="table table-bordered">
				<thead>
					<tr>
						<th class="grid-head-left check_box">
						<div class="kt-section__content kt-section__content--solid">
							<label class="kt-checkbox kt-checkbox--bold kt-checkbox--success  kt-checkbox--all " style="margin-left: 2px; float: left;  margin-top: 2px">
								<input type="checkbox" onchange="if($(this).is(':checked')){ checkAllEntities() }else{ uncheckAllEntities() }"/>
								<span></span>
							</label>
						</div>
						<?php if (isset($this->order)) : ?>
						<?= $this->order->getTitle("entityid", "ID") ?>
						<?php else : ?>ID<?php endif; ?>
						</th>
						<th >
							<?php if ($this->status == Entity::STATUS_ARCHIVED) { ?>
								Received/Created
							<?php } else { ?>
								<?php if (isset($this->order)) : ?>
									<?= $this->order->getTitle("received", "Created!") ?>
								<?php else : ?>Received<?php endif; ?>
							<?php } ?>
						</th>
						<th >Notes</th>
						<th >
							<?php if (isset($this->order)) : ?>
								<?php print $this->order->getTitle("shipperfname", "Shipper"); ?>
							<?php else : ?>Shipper<?php endif; ?>
						</th>
						<?php  { ?>
							<th   style="">Vehicle</th>
							<th   >
								<?php if (isset($this->order)) : ?>
									<?= $this->order->getTitle("Origincity", "Origin") ?>
								<?php else : ?>Origin<?php endif; ?>
								/
								<?php if (isset($this->order)) : ?>
									<?= $this->order->getTitle("Destinationcity", "Destination") ?>
								<?php else : ?>Destination<?php endif; ?>
							</th>
						<?php } ?>
							<th >
								<?php if (isset($this->order)) : ?>
									<?= $this->order->getTitle("est_ship_date", "Est. Ship") ?>
								<?php else : ?>Est. Ship<?php endif; ?>
							</th>
						<th class="grid-head-right">Quote
						</th>
					</tr>
				</thead>
				<tbody>
					<?php if (count($this->entities) == 0): ?>
					<tr >
						<td colspan="8" align="center" class="grid-body-left "><i>No records</i></td>
					</tr>
					<?php endif; ?>
					<?php
					$words = array("+", "-", " ", "(", ")");
					$wordsReplace = array("", "", "", "", "");
					$searchData = array();
					foreach ($this->entities as $i => $entity) :
						$searchData[] = $entity['entityid'];
					?>
					<tr id="lead_tr_<?= $entity['entityid'] ?>" class="grid-body<?= ($i == 0 ? " " : "") ?><?= ($entity->duplicate) ? ' duplicate' : '' ?>">
						<td align="center" class="grid-body-left"  >
							<?php if (!$entity['readonly']) : ?>
								<label class="kt-checkbox kt-checkbox--bold kt-checkbox--success" style="padding-left:20px;width:18px;height:28px;"><input type="checkbox" value="<?= $entity['entityid'] ?>" class="entity-checkbox ">&nbsp;<span></span></label>
                            <?php endif; ?>
							<div class="clearfix"></div>
							<?php
                                $urlDetail = "show";
                                if ($_GET['lead_search_type'] == 1 || $_GET['leads'] == '' || $_GET['leads'] == 'assigned' || $_GET['leads'] == 'quoted' || $_GET['leads'] == 'follow' || $_GET['leads'] == 'appointment' || $_GET['leads'] == 'expired' || $_GET['leads'] == 'duplicate' || $_GET['leads'] == 'onhold' || $_GET['leads'] == 'archived' || $_GET['leads'] == 'unreadable' || $_GET['etype'] == 1) { 
                                    $urlDetail = "showimported";
							?>
							<a href="<?= SITE_IN ?>application/leads/showimported/id/<?= $entity['entityid'] ?>">
                                <div class=" kt-badge btn_bright_blue kt-badge--inline kt-badge--pill order_id"><?= $entity['number'] ?></div>
                            </a>
                            </br>
							<?php
							} else {
								if ($entity['status'] != Entity::STATUS_CACTIVE && $entity['status'] != Entity::STATUS_CASSIGNED && $entity['status'] != Entity::STATUS_CPRIORITY && $entity['status'] != Entity::STATUS_CONHOLD && $entity['status'] != Entity::STATUS_CDEAD) {
									$urlDetail = "showcreated";
								}
							?>
							<a class="kt-badge kt-badge--success kt-badge--inline" href="<?= SITE_IN ?>application/leads/<?= $urlDetail ?>/id/<?= $entity['entityid'] ?>">
								<div class="order_id"><?= $entity['number'] ?></div>
							</a>
							<?php } ?>
                            <br/>
							<a href="<?= SITE_IN ?>application/leads/history/id/<?= $entity['entityid'] ?>">History</a> <br/>
							<?php 
                                if ($this->status == Entity::STATUS_ARCHIVED) { 
                            ?>
                                    <?php if ($entity['type'] == 4) { ?>
                                        <br/>Created
                                    <?php } else { ?>
                                        <br/>Imported
                                    <?php } ?>
                            <?php
                                } else {
                                    if (isset($_GET['search_string'])) {
                                        print "<br /><b>Status</b><br>";
                                        if ($entity['status'] == Entity::STATUS_ACTIVE)
                                            print "Quote Requests";
                                        elseif ($entity['status'] == Entity::STATUS_ONHOLD)
                                            print "OnHold";
                                        elseif ($entity['status'] == Entity::STATUS_ARCHIVED)
                                            print "Cancelled";
                                        elseif ($entity['status'] == Entity::STATUS_LQUOTED)
                                            print "Today's Quotes";
                                        elseif ($entity['status'] == Entity::STATUS_LFOLLOWUP)
                                            print "Follow-ups";
                                        elseif ($entity['status'] == Entity::STATUS_LEXPIRED)
                                            print "Expired Quotes";
                                        elseif ($entity['status'] == Entity::STATUS_LDUPLICATE)
                                            print "Possible Duplicate";
                                        elseif ($entity['status'] == Entity::STATUS_UNREADABLE)
                                            print "Unreadable";
                                        ?>
                                        <?php
                                    }
                                }
							?>
						</td>
						<td valign="top" style="white-space: nowrap;"  >
						  
							<span><?php if ($entity['status'] == Entity::STATUS_ARCHIVED) { ?>
								!<?php if ($entity['lead_type'] == 1) { ?>
									<?= date("m/d/y h:i a", strtotime($entity['assigned_date'])); ?>
								<?php } else { ?>
									<?= date("m/d/y h:i a", strtotime($entity['received'])); ?>  
								<?php } ?>
								<?php } else { ?> 
								<?= date("m/d/y h:i a", strtotime($entity['received'])); ?>

								<?php if ($entity->duplicate) : ?>
									<br/><span style="color: #F00;">Possible Duplicate</span>
								<?php endif; ?>
							<?php } ?>
							</span> 
							<?php //$assigned = $entity->getAssigned();    ?>
							<br>Assigned to:<br/> <strong class="kt-font-success"><?= $entity['AssignedName'] ?></strong><br />  

						</td>
						<?php
						if (trim($entity['shipperphone1']) != "") {
							$code = substr($entity['shipperphone1'], 0, 3);
							$areaCodeStr = "";
							$areaCodeRows = $this->daffny->DB->selectRow("*", "app_area_code", "WHERE  AreaCode='" . $code . "'");
							if (!empty($areaCodeRows)) {
								$areaCodeStr = "<b>" . $areaCodeRows['StdTimeZone'] . "-" . $areaCodeRows['statecode'] . "</b>";
							}
						}
						if (trim($entity['shipperphone2']) != "") {

							$code = substr($entity['shipperphone2'], 0, 3);
							$areaCodeStr2 = "";
							$areaCodeRows2 = $this->daffny->DB->selectRow("*", "app_area_code", "WHERE  AreaCode='" . $code . "'");

							if (!empty($areaCodeRows2)) {
								$areaCodeStr2 = "<b>" . $areaCodeRows2['StdTimeZone'] . "-" . $areaCodeRows2['statecode'] . "</b>";
							}
						}
						if ($entity['shipperphone1_ext'] != '')
							$phone1_ext = " <b>X</b> " . $entity['shipperphone1_ext'];
						if ($entity['shipperphone2_ext'] != '')
							$phone2_ext = " <b>X</b> " . $entity['shipperphone2_ext'];
						?>                        
						<td  width="5%">
							<?php

							$NotesCount1 = 0;
							if (!is_null($entity['NotesCount1']))
								$NotesCount1 = $entity['NotesCount1'];

							$NotesCount2 = 0;
							if (!is_null($entity['NotesCount2']))
								$NotesCount2 = $entity['NotesCount2'];

							$NotesCount3 = 0;
							if (!is_null($entity['NotesCount3']))
								$NotesCount3 = $entity['NotesCount3'];

							$countNewNotes = $entity['NotesFlagCount3'];
							?>
							<?= notesIcon($entity['entityid'], $NotesCount3, Note::TYPE_INTERNAL, $entity['status'] == Entity::STATUS_ARCHIVED, $countNewNotes) ?>
						</td>
						<td  >

							<div class="shipper_name"><span class="kt-font-bold"><?= $entity['shipperfname'] ?> <?= $entity['shipperlname'] ?></span></div>


							<?php if ($entity['shippercompany'] != "") { ?><div class="shipper_company"><b><?= $entity['shippercompany'] ?></b><br /></div><?php } ?>

							<?php
							if ($entity['shipperphone1'] != "") {
								$phone1 = str_replace($words, $wordsReplace, $entity['shipperphone1']);
								?>
								<div class="shipper_number">
								   <span class="kt-font-bold"> <a href="javascript:void(0);" onclick="customPhoneSms('<?= $phone1; ?>');"><?= formatUsPhone($entity['shipperphone1']) ?></a></span>


								<?php } ?>
								<?= $phone1_ext; ?> 
								<?= $areaCodeStr; ?><br/></div>
							<?php
							if ($entity['shipperphone2'] != "") {
								$phone2 = str_replace($words, $wordsReplace, $entity['shipperphone2']);
								?>
								<div class="shipper_number"> <span class="kt-font-bold kt-font-success"><a href="javascript:void(0);" onclick="customPhoneSms('<?= $phone2; ?>');"><?= formatUsPhone($entity['shipperphone2']) ?></a> </span>
								<?php } ?>
								<?= $phone2_ext; ?> 
								<?= $areaCodeStr2; ?></div>
							<?php if ($entity['shipperemail'] != "") { ?>
								<?php if (strlen($entity['shipperemail']) < 25) { ?>
									<a href="mailto:<?= $entity['shipperemail'] ?>" TITLE="<?= $entity['shipperemail'] ?>"><div class=" kt-font-bold shipper_email"><?= $entity['shipperemail'] ?><br/></div></a>
								<?php } else { ?>
									<a href="mailto:<?= $entity['shipperemail'] ?>"  TITLE="<?= $entity['shipperemail'] ?>"><div class=" kt-font-bold shipper_email" ><?= substr($entity['shipperemail'], 0, 25) ?><br/></div></a>
								<?php } ?>
							<?php } ?>

							<?php if ($entity['referred_by'] != "") { ?>
								<div class="shipper_referred">
									Source: <b><?= $entity['referred_by'] ?></b><br>
								</div>
								<?php
							} else {
								$member = new Member($this->daffny->DB);
								$member->load($entity['assigned_id']);

								if ($member->hide_lead_sources == 0) {
									?>
									<strong>Source: </strong><?php print $entity['source_name']; ?>

									<?php
								}
							}
							?>
						</td>
						<td>

							<?php if ($this->status == Entity::STATUS_ARCHIVED) { ?>
								<?php if ($entity['lead_type'] == 1) { ?>
									<?= $entity['shipper_hours'] ?>

								<?php } else { ?>    
									<?php //$vehicles = $entity->getVehicles();?>

									<?php if ($entity['TotalVehicle'] == 0) : ?>
									<?php elseif ($entity['TotalVehicle'] == 1) : ?>
										<a class="kt-badge  kt-font-bold kt-font-dark "  onclick="vehiclePopupHandler(1, '<?php print $entity['entityid']; ?>', '<?php print $entity['Vehicleid']; ?>')">
											<?= $entity['Vehiclemake']; ?> 
											<?= $entity['Vehiclemodel']; ?><br/>
											<?= $entity['Vehicleyear']; ?> 
											<?= $entity['Vehicletype']; ?>
										</a>
										&nbsp;<?= imageLink($vehicle['Vehicleyear'] . " " . $entity['Vehiclemake'] . " " . $entity['Vehiclemodel'] . " " . $entity['type']) ?>

									<?php else : ?>
										<span class="kt-link" onclick="vehiclePopupHandler(2, '<?php print $entity['entityid']; ?>', '<?php print $entity['Vehicleid']; ?>')">Multiple Vehicles<b><span style="color:#000000;">(<?php print $entity['TotalVehicle']; ?>)</span></b></span>

										<div class="vehicles-info" id="vehicles-info-<?php print $entity['entityid']; ?>">

										</div>
										<br/>
									<?php endif; ?>

									<span style="color:red;weight:bold;"><?php print ($entity['ship_via'] != 0) ? $ship_via_string[$entity['ship_via']] : ""; ?></span><br/>
								<?php } ?>
							<?php }else { ?>
								<?php if ($entity['TotalVehicle'] == 0) : ?>
								<?php elseif ($entity['TotalVehicle'] == 1) : ?>
									<a class="kt-link" onclick="vehiclePopupHandler(1, '<?php print $entity['entityid']; ?>', '<?php print $entity['Vehicleid']; ?>')">
										<?= $entity['Vehiclemake']; ?> <?= $entity['Vehiclemodel']; ?><br/>
										<?= $entity['Vehicleyear']; ?> <?= $entity['Vehicletype']; ?>
									</a>
									&nbsp;<?= imageLink($vehicle['Vehicleyear'] . " " . $entity['Vehiclemake'] . " " . $entity['Vehiclemodel'] . " " . $entity['type']) ?>
								<?php else : ?>
									<span class=" kt-link" onclick="vehiclePopupHandler(2, '<?php print $entity['entityid']; ?>', '<?php print $entity['Vehicleid']; ?>')">Multiple Vehicles<b><span style="color:#000000;">(<?php print $entity['TotalVehicle']; ?>)</span></b></span>

									<div class="vehicles-info" id="vehicles-info-<?php print $entity['entityid']; ?>">

									</div>
									<br/>                                                                      
									<br/>
								<?php endif; ?>

								<span style="color:red;weight:bold;"><?php print ($entity['ship_via'] != 0) ? $ship_via_string[$entity['ship_via']] : ""; ?></span><br/>
							<?php } ?>
						</td>
						<?php
						    {
                        ?>
						<?php
                            $o_link = "http://maps.google.com/maps?q=" . urlencode($entity['Origincity'] . ",+" . $entity['Originstate']);
                            $o_formatted = trim($entity['Origincity'] . ', ' . $entity['Originstate'] . ' ' . $entity['Originzip'], ", ");
                            $d_link = "http://maps.google.com/maps?q=" . urlencode($entity['Destinationcity'] . ",+" . $entity['Destinationstate']);
                            $d_formatted = trim($entity['Destinationcity'] . ', ' . $entity['Destinationstate'] . ' ' . $entity['Destinationzip'], ", ");
						?>
						<td>
							<span onclick="window.open('<?= $o_link ?>', '_blank')"><?= $o_formatted ?></span> /<br/>
							<span onclick="window.open('<?= $d_link ?>')"><?= $d_formatted ?></span><br/>
							<?php if (is_numeric($entity['distance']) && ($entity['distance'] > 0)) { ?>
								<?= number_format($entity['distance'], 0, "", "") ?> mi
								<?php $cost = $entity['carrier_pay_stored'] + $entity['pickup_terminal_fee'] + $entity['dropoff_terminal_fee'];
								?> ($ <?= number_format(($cost / $entity['distance']), 2, ".", ",") ?>/mi)
							<?php } ?>
							<span onclick="mapIt(<?= $entity['entityid'] ?>);">Map it</span>
						</td>
                        <?php } ?>
						<td width="10%">
							<span><?= date("m/d/y", strtotime($entity['est_ship_date'])) ?></span>
						</td>
						<td style="white-space:nowrap;" class="grid-body-right" width="12%">
							
							<?php
							/**
							 * Chetu added udpate
							 * When importd leads are quoted than Show Amounts
							 * 
							 * @author Chetu Inc.
							 */
							
							if($entity['quoted'] == NULL){ 
							?>
								<notQuoted>
									<?php if ($this->status == Entity::STATUS_ACTIVE) : ?>
										<?php if (count($entity['TotalVehicle']) == 1) : ?>
											<?php if (!$entity['readonly']) : ?>


							<div class="row">
							<div class="col-12 col-sm-3">
							<img src="<?= SITE_IN ?>images/icons/dollar.png" class="imgr" alt="Tariff" title="Tariff">
							$           
							</div>
							<div class="col-12 col-sm-7">
							<input type="text" id="lead_tariff_<?= $entity['entityid'] ?>" class="form-box-textfield decimal form-control" value="<?= number_format($vehicles[0]->tariff, 2, ".", "") ?>" />
							</div>
							</div>

								<div class="row">
									<div class="col-12 col-sm-3">
										 <img src="<?= SITE_IN ?>images/icons/person.png" class="imgr" alt="Deposit" title="Deposit" >$  
									</div>
								<div class="col-12 col-sm-7">
							   
								<input type="text" id="lead_deposit_<?= $entity['entityid'] ?>" class="form-box-textfield decimal form-control" value="<?= number_format($vehicles[0]->deposit, 2, ".", "") ?>" />
							  
								</div>
								</div>

												
													   
										  

											<?php endif; ?>
										<?php else : ?>
											<?php if ($_GET['lead_search_type'] == 1) { ?>
												<?= simpleButton('Details', SITE_IN . 'application/leads/showimported/id/' . $entity['entityid']) ?>
											<?php } else { ?>
												<?= simpleButton('Details', SITE_IN . 'application/leads/' . $urlDetail . '/id/' . $entity['entityid']) ?>
											<?php } ?>
										<?php endif; ?>
									<?php elseif ($this->status == Entity::STATUS_UNREADABLE || $_GET['leads'] == "search") : ?>
										<?php if ($_GET['lead_search_type'] == 1) { ?>
											<?= simpleButton('Details', SITE_IN . 'application/leads/showimported/id/' . $entity['entityid']) ?>
										<?php } else { ?>
											<?= simpleButton('Details', SITE_IN . 'application/leads/' . $urlDetail . '/id/' . $entity['entityid']) ?>
										<?php } ?>
									<?php else : ?>
										<?php //$entity->getStatusUpdated("m/d/Y")    ?>

										 <div  class="row">
										 <div class="col-12 col-sm-12">
										 <img src="<?= SITE_IN ?>images/icons/dollar.png" alt="Total Tariff" title="Total Tariff" width="16" height="16"/>
										<?= ("$ " . number_format((float) $entity['total_tariff_stored'], 2, ".", ",")) ?>
										 </div>
										 </div>

										 <div  class="row">
											<div class="col-12 col-sm-12">
												<img src="<?= SITE_IN ?>images/icons/truck.png" alt="Carrier Pay" title="Carrier Pay" width="16" height="16"/>
												<?= ("$ " . number_format((float) $entity['carrier_pay_stored'], 2, ".", ",")) ?><br/>

											</div>
										</div>


										 <div  class="row">
											<div class="col-12 col-sm-12">
												<img src="<?= SITE_IN ?>images/icons/person.png" alt="Deposit" title="Deposit" width="16" height="16"/>
												<?= ("$ " . number_format((float) ($entity['total_tariff_stored'] - $entity['carrier_pay_stored']), 2, ".", ",")) ?>

											</div>
										</div>

									  
									   


									<?php endif; ?>
							</notquoted>
							<?php
							} else { ?>


								<div  class="row">
								<div  class="col-12 col-sm-12">
								<img src="<?= SITE_IN ?>images/icons/dollar.png" alt="Tariff" title="Tariff" width="16" height="16">
								$<?php echo $entity['total_tariff_stored']?>
								</div>
								</div>
								<div class="row">
								<div  class="col-12 col-sm-6">
								<img src="<?= SITE_IN ?>images/icons/person.png" alt="Deposit" title="Deposit" width="16" height="16">
								$ <?php echo $entity['total_tariff_stored'] - $entity['carrier_pay_stored']?>
								</div>
								</div>




							<?php }
							
							?>
							
						</td>




					</tr>
					<?php endforeach; ?>
					<?php
					$searchCount = count($searchData);
					if ($searchCount > 0) {
						$_SESSION['searchData'] = $searchData;
						$_SESSION['searchCount'] = $searchCount;
						$_SESSION['searchShowCount'] = 0;
					}
					?>
				</tbody>
			</table>   
        </div>
			@pager@
			<!-- table end here -->

		</div>
	</div>
</div>



<!--
Custom Loader UI
-->
<div class='charliesLoaderContent' id="charliesOverLay"></div>
<div class='charliesLoaderContent' id="charliesLoader">
   <br><br><br><img src="<?php echo SITE_IN ?>images/ajax-loader.gif">
</div>
<!--including auto quotes JavaScript library-->

    <!--begin::Modal-->
    <div class="modal fade" id="maildivnew" tabindex="-1" role="dialog" aria-labelledby="maildivnewmodel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="maildivnewmodel">Send Email</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="modal-body">

                            
                    <div style="float: left;">
                    <ul style="margin-top: 26px;">  
                    <li style="margin-bottom: 14px;">Form Type <input value="1" id="attachPdf" name="attachTpe" type="radio"/><label for="attachPdf" style="margin-right: 2px; cursor:pointer;"> PDF</label><input value="0" id="attachHtml"  name="attachTpe" type="radio"/><label for="attachHtml" style="cursor:pointer"> HTML</label></li>
                    <li style="margin-bottom: 11px;">Attachment(s): <span style="color:#24709F;" id="mail_att_new"></span></li>
                    </ul>
                    </div>
                    <div style="text-align: right;">
                    <div style="text-align: right;">
                    <img src="/images/icons/add.gif"> <span style="margin-bottom: 3px;cursor:pointer; position: relative;bottom:4px; color:#24709F;" class="add_one_more_field_" >Add a Field</span>
                    <ul>
                    <li id="extraEmailsingle" style="margin-bottom: 6px;"><span>Email:<span style="color:red">*</span></span> <input type="text" id="mail_to_new" name="mail_to_new" class="form-box-combobox" ></li>
                    <li style="margin-bottom: 6px;margin-top: 6px;margin-left: 292px; position:relative; display: none;" id="mailexttra"><input name="optionemailextra" class="form-box-combobox optionemailextra" type="text"><a href="#" style="position: absolute;margin-left: 2px;margin-top: 8px;" class="remove_2sd_field"><img id="singletop" style="width: 12px;height: 12px;" src="/images/icons/delete.png"></a></li>
                    <li style="margin-bottom: 6px;"><span style="margin-right: 18px;">CC:</span> <input type="text" id="mail_cc_new" name="mail_cc_new" class="form-box-combobox" ></li>
                    <li style="margin-bottom: 12px;"><span style="margin-right: 9px;">BCC:</span> <input type="text" id="mail_bcc_new" name="mail_bcc_new" class="form-box-combobox" ></li>
                    </ul>
                    </div>
                    <div class="edit-mail-content" style="margin-bottom: 8px;">
                    <div class="form-group">
                    <label>Subject</label>
                   
                        <input type="text" id="mail_subject_new" class="form-box-textfield form-control" maxlength="255" name="mail_subject_new" style="width: 100%;">
                    </div>
                    <div class="form-group">
                    <div class="edit-mail-label">Body:<span>*</span></div>
                    <div class="edit-mail-field" style="width: 100%;"><textarea class="form-box-textfield" style="width: 100%;" name="mail_body_new" id="mail_body_new"></textarea></div>
                    </div>
                    </div>
                    <input type="hidden" name="form_id" id="form_id"  value=""/>
                    <input type="hidden" name="entity_id" id="entity_id"  value=""/>
                    <input type="hidden" name="skillCount" id="skillCount" value="1">
                    </div>
               
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark btn-sm" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn_dark_blue btn-sm" onclick="emailSelectedLeadFormNewsend()">Submit</button>
                </div>
            </div>
        </div>
    </div>




    <!--begin::Modal-->
<div class="modal fade" id="listmails" tabindex="-1" role="dialog" aria-labelledby="listmails_model" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="listmails_model">Email List</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <div class="modal-body">
                
    <div class="mail-list-label">
        <div id="adv_option" >Advance Options</div>
        <div style="clear: both"></div>
        <div id="adv_option_toggle" style="display: none; max-height: 122px;"> 
            <div style="float: left;">
                <ul>
                    <li style="margin-bottom: 16px;padding-top: 5px; color: forestgreen;font-weight: bold">Sending Options</li>
                    <li style="margin-bottom: 14px;">Form Type <input id="PDF" name="attachType" value="1" type="radio"/>
                        <label for="PDF" style="margin-right: 2px;">PDF</label>
                        <input id="HTML" name="attachType" value="0" type="radio"/>
                        <label for="HTML">HTML</label>
                    </li>
                    <li style="margin-bottom: 11px;">

                        

                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand">
                            <input name="combine" id="combine" type="checkbox"> Combine to single email
                            <span></span>
                        </label>
                       

                         </li>
                </ul>
            </div>
            <div style="text-align: right;">  
                <img src="/images/icons/add.gif"> <span style="margin-bottom: 3px;cursor:pointer; position: relative;bottom:4px; " class="add_field_button" >Add a Field</span>
                <ul id="adf">
                    <li id="extraEmail" style="margin-bottom: 6px;"><span>Email:<span style="color:red">*</span></span> <input name="optionemail" class="form-box-combobox optionemail" type="text" ></li>


                    <li style="margin-bottom: 6px;margin-top: 6px;margin-left: 292px; position:relative; display: none;" id="mailexttramultiple"><input name="optionemailextramultiple" class="form-box-combobox optionemailextramultiple" type="text"><a href="#" style="position: absolute;margin-left: 2px;margin-top: 8px;" class="remove_2sd_field"><img id="singletopmult" style="width: 12px;height: 12px;" src="/images/icons/delete.png"></a></li>
                    <li style="margin-bottom: 6px;"><span style="margin-right: 18px;">CC:</span> <input name="optioncc" class="form-box-combobox optioncc" type="text"></li>
                    <li style="margin-bottom: 12px;"><span style="margin-right: 9px;">BCC:</span> <input name="optionbcc" class="form-box-combobox optionbcc" type="text"></li>
                </ul>
            </div>  
        </div>



        <script type="text/javascript">
            var atttypem = <?php
            $sql = "SELECT attach_type FROM app_emailtemplates WHERE owner_id =" . getParentId();
            $result = $this->daffny->DB->query($sql);
            $row = $this->daffny->DB->fetch_row($result);
            echo $row['attach_type'];
            ?>;
            if (atttypem > 0) {
                $("#PDF").attr('checked', 'checked');
            } else {
                $("#HTML").attr('checked', 'checked');
            }
        </script>            
        <script type="text/javascript">
            $('.add_field_button').on('click', function () {
                $('#mailexttramultiple').css('display', 'block');
                $('#adf').css('margin-bottom', '25px');
                return false;
            });
            $('#singletopmult').on('click', function () {
                $('#mailexttramultiple').css('display', 'none');
                $('.optionemailextramultiple').val('');
                $('#adf').css('margin-bottom', '4px');
            });
            $("#adv_option").click(function () {

                if ($('#adv_option_toggle').css('display') == 'none') {
                    if ($('.remove_field').length > 0) {
                        $('#adv_option_toggle').css('max-height', '320px').slideDown().finish();
                    } else {
                        $('#adv_option_toggle').css('max-height', '320px').slideDown().finish();
                    }
                } else {
                    $('#adv_option_toggle').slideUp();
                }

            });
        </script>             
        <table  class="table-bordered table" >

            <tbody>

                <tr >

                    <td class="grid-head-left id-column" style="width: 70px;">
                        <?php if (isset($this->order)) : ?>
                            <?php echo $this->order->getTitle("id", "ID") ?>
                        <?php else : ?>ID<?php endif; ?>
                    </td>
                    <td class="shipper-column" style="width: 229px;">
                        <?php
                        if (isset($this->order)):
                            echo $this->order->getTitle("shipper", "Shipper");
                        else :
                            echo "Shipper";

                        endif;
                        ?>

                    </td>
                    <td  style="width: 90px;">
                        Attachment
                    </td>
                    <td class="grid-head-right" style="width: 29px;">
                        Action

                    </td>

                </tr>

            </tbody>

        </table>    

    </div>

    <div class="repeat-column"></div>

    

            </div>
            <div class="modal-footer">

                <div class="editmail"></div>
                
            </div>
        </div>
    </div>
</div>

<!--end::Modal-->


<!-- <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.min.js"></script>   -->
 <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery-ui.js"></script> 
<script src="<?php echo SITE_IN ?>core/js/core.js"></script>
<link href="<?php echo SITE_IN; ?>styles/new/assets/vendors/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
<script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/custom/datatables/datatables.bundle.js" type="text/javascript"></script>


<script type="text/javascript">
    $(document).ready(function() {

    // $('.decimal ').keypress(function (event) {
    //     console.log("key press value", event.target.value);
    //     return isNaN(event.target.value)
    // });


   $('#lead_check').DataTable({
       "lengthChange": false,
       "paging": false,
       "bInfo" : false,
       'drawCallback': function (oSettings) {
           $('#lead_check_wrapper').children('.row:first').children('.col-md-6:first').html('<div class="form-group row" style="margin-left:0;"><label class="col-form-label">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
           $('#lead_check_wrapper').children('.row:last').children('.col-md-5').html($('.pager').clone()).addClass('text-left');
           $('#lead_check_wrapper').children('.row:last').children('.col-md-7').html($('.table_b ').html()).addClass('text-right');
           $('.pages-div-custom').remove();
           
      }
   });
} );
</script>

<script type="text/javascript">

</script>