<style type="text/css">
	td.disabled.day {
		background: #efededde !important;
	}
</style>

<div class="modal fade" id="email-template-preview-modal" tabindex="-1" role="dialog" aria-labelledby="maildivnew_modal" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="maildivnew_model">Email message</h5>
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
						<img src="<?php echo SITE_IN;?>/images/icons/add.gif"> <span style="margin-bottom: 3px;cursor:pointer; position: relative;bottom:4px; color:#24709F;" class="add_one_more_field_" >Add a Field</span>
						<ul>
							<li id="extraEmailsingle" style="margin-bottom: 6px;"><span>Email:<span style="color:red">*</span></span> <input type="text" id="mail_to_new" name="mail_to_new" class="form-box-combobox" ></li>
							<li style="margin-bottom: 6px;margin-top: 6px;margin-left: 292px; position:relative; display: none;" id="mailexttra"><input name="optionemailextra" class="form-box-combobox optionemailextra" type="text"><a href="#" style="position: absolute;margin-left: 2px;margin-top: 8px;" class="remove_2sd_field"><img id="singletop" style="width: 12px;height: 12px;" src="<?php echo SITE_IN;?>/images/icons/delete.png"></a></li>
							<li style="margin-bottom: 6px;"><span style="margin-right: 18px;">CC:</span> <input type="text" id="mail_cc_new" name="mail_cc_new" class="form-box-combobox" ></li>
							<li style="margin-bottom: 12px;"><span style="margin-right: 9px;">BCC:</span> <input type="text" id="mail_bcc_new" name="mail_bcc_new" class="form-box-combobox" ></li>
						</ul>
					</div>
					<div class="edit-mail-content" style="margin-bottom: 8px;">
						<div class="edit-mail-row" style="margin-bottom: 8px;">
							
							<div class="form-group" >
								<label >Subject:<span>*</span></label>
								<input type="text" id="mail_subject_new" class="form-box-textfield" maxlength="255" name="mail_subject_new" ></div>
						</div>
						<div  style="width: 100%">
							<div class="form-group" >
								<label class="">Body:<span>*</span></label>
								<textarea class="form-box-textfield form-control" name="mail_body_new" id="mail_body_new"></textarea></div>
						</div>
					</div>
					<input type="hidden" name="form_id" id="form_id"  value=""/>
					<input type="hidden" name="entity_id" id="entity_id"  value=""/>
					<input type="hidden" name="skillCount" id="skillCount" value="1">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-dark btn-sm" data-dismiss="modal">Cancal</button>
				<button type="button" class="btn btn-primary btn-sm" onclick="maildivnew_send();">Submit</button>
			</div>
		</div>
	</div>
</div>

<!--begin::Modal-->
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

				<div class="error_reassing" style="display: block;"></div>
				<div class="form-group">
					<select class="form-box-combobox form-control" id="company_members">
						<option value=""><?php print "Select One"; ?></option>
						<?php foreach ($this->company_members as $member) : ?>

							<?php if ($member->status == "Active") {
								$activemember .= "<option value= '" . $member->id . "'>" . $member->contactname . "</option>";
							} else {
								$inactivemember .= "<option value= '" . $member->id . "'>" . $member->contactname . "</option>";
							}
							?>
						<?php endforeach; ?>
						<optgroup label="Active User">
							<?php echo $activemember; ?>
						</optgroup>
						<optgroup label="InActive User">
							<?php echo $inactivemember; ?>
						</optgroup>
					</select>


				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancal</button>
				<button type="button" id="get_member_id" class="btn btn-primary" value="" onclick="reassignQuotes(this.value)">Submit</button>
			</div>
		</div>
	</div>
</div>
<!--end::Modal-->

<script type="text/javascript">
	$(document).ready(function() {
		$("#followup_when").datepicker({
			startDate: '+1d'
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
		$(".entity-checkbox:checked").each(function() {
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
					Swal.fire("Can't save follow-up. Try again later, plaese");
				} else {
					/*$("#followupdiv").dialog("close");*/
					$("#followupdiv").modal('hide');
					Swal.fire("Follow-up saved.");
				}
			},
			error: function(response) {
				Swal.fire("Can't save follow-up. Try again later, plaese");
			}
		});
	}

	function emailSelectedQuoteForm() {

		if ($(".entity-checkbox:checked").size() == 0) {
			alert("You have no selected items.");
		} else {
			var entity_ids = [];
			$(".entity-checkbox:checked").each(function() {
				entity_ids.push($(this).val());
			});


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
							action: "emailQuoteMultiple",
							form_id: form_id,
							entity_ids: entity_ids.join(',')
						},
						success: function(res) {
							if (res.success) {
								alert("Email was successfully sent");
							} else {
								alert("Can't send email. Try again later, please");
							}
						},
						complete: function(res) {
							$("body").nimbleLoader('hide');
						}
					});
				}

			}
		}
	}

	function reassignQuoteDialog() {

		if ($(".entity-checkbox:checked").length == 0) {
			Swal.fire('Quote not selected');
			return;
		}
		$("#reassignCompanyDiv").modal();
		$('#company_members').on('change', function() {
			var member_id = $("#company_members").val();
			$("#get_member_id").val(member_id);
		});

	}

	function followupQuote() {
		if ($(".entity-checkbox:checked").length == 0) {
			Swal.fire('Quote not selected');
			return;
		}

		$("#followupdiv").modal();
		var member_id = $("#company_members").val();
	}

	function printSelectedQuoteForm() {

		if ($(".entity-checkbox:checked").length == 0) {
			Swal.fire("Quote not selected");
			return;
		}

		if ($(".entity-checkbox:checked").length > 1) {
			Swal.fire("Please select one quote");
			return;
		}
		var quote_id = $(".entity-checkbox:checked").val();

		form_id = $("#form_templates").val();
		if (form_id == "") {
			Swal.fire("Please choose form template");
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
				beforeSend: function() {},
				success: function(retData) {
					printOrder(retData.printform);
				}
			});
		}
	}

	function maildivnew_old() {

		if ($(".entity-checkbox:checked").length > 1) {
			Swal.fire("Please select one quote");
			return;
		}

		var entity_id = $(".entity-checkbox:checked").val();
		if (entity_id == "") {
			entity_id = $('#entity_id').val();
		}

		$.ajax({
			url: BASE_PATH + 'application/ajax/entities.php',
			data: {
				action: "emailQuoteNewSend",
				form_id: $('#form_templates').val(),
				entity_id: entity_id,
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

					$("#maild_id").addClass('kt-spinner kt-spinner--right kt-spinner--sm kt-spinner--light');
				}
			},
			success: function(response) {

				if (response.success == true) {
					$("#maildivnew").modal('hide');
					clearMailForm();
				} else {
					$("#maildivnew").modal('hide');
					Swal.fire("Email Not Send");
				}

			},
			complete: function() {
				$("#maild_id").removeClass('kt-spinner kt-spinner--right kt-spinner--sm kt-spinner--light');
			}

		});
	}

	function maildivnew() { 
		var sEmail = [$('#mail_to_new').val(), $('.optionemailextra').val(), $('#mail_cc_new').val(), $('#mail_bcc_new').val()];
		if (validateEmail(sEmail) == false) {
			Swal.fire('Invalid Email Address');
			return false;
		}
		if ($('#attachPdf').is(':checked')) {
			attach_type = $('#attachPdf').val();
		} else {
			attach_type = $('#attachHtml').val();
		}

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
					Swal.fire('Empty Field(s)');
					return false;
				}
				;
			},
			success: function (response) {
				if (response.success == true) {
					$("#maildivnew").modal('hide');
					clearMailForm();
				} else {
					$("#maildivnew").modal('hide');
					Swal.fire("Email Not Send");
				}
			},
			complete: function () {
				// $("body").nimbleLoader("hide");
			}
		});
	}

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

	function emailSelectedQuoteFormNew() {
		if ($(".entity-checkbox:checked").length == 0) {
			Swal.fire("You have no selected items.");
			return;
		}

		if ($(".entity-checkbox:checked").length > 1) {
			Swal.fire("Select only one quote.");
			return;
		}

		var entity_ids = $(".entity-checkbox:checked").val();
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
					action: "emailQuoteNew",
					form_id: form_id,
					entity_id: entity_ids
				},
				success: function(res) {
					console.log(res);
					if (res.success) {
						$("#form_id").val(form_id);
						$("#mail_to_new").val(res.emailContent.to);
						$("#mail_subject_new").val(res.emailContent.subject);

						$("#mail_body_new").val(res.emailContent.body);

						CKEDITOR.instances['mail_body_new'].setData(res.emailContent.body);
						$("#email-template-preview-modal").modal();
					} else {
						swal.fire("Can't send email. Try again later, please");
					}
				},
				complete: function(res) {
					KTApp.unblockPage();
				}
			});
		}
	}

	function maildivnew_send() {
		var entity_ids = $(".entity-checkbox:checked").val();
		$.ajax({
			url: BASE_PATH + 'application/ajax/entities.php',
			data: {
				action: "emailQuoteNewSend",
				form_id: $('#form_id').val(),
				entity_id: entity_ids,
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
					$('#maildivnew1').find(".modal-body").addClass('kt-spinner kt-spinner--lg kt-spinner--success');
					$("maildivnew_send_close").attr('disabled', 'disabled');
					$("maildivnew_send").attr('disabled', 'disabled');
				}
			},
			success: function(response) {
				$('#maildivnew1').find(".modal-body").removeClass('kt-spinner kt-spinner--lg kt-spinner--success');
				if (response.success == true) {
					clearMailForm();
					$("#maildivnew1").modal('hide');
				}
			},
			complete: function() {
				$('#email-template-preview-modal').find(".modal-body").removeClass('kt-spinner kt-spinner--lg kt-spinner--success');
				$("#email-template-preview-modal").modal('hide');
			}
		});
	}

	function convertToOrder() {

		if ($(".entity-checkbox:checked").length == 0) {
			Swal.fire("You have no selected items.");
		} else {
			var entity_ids = [];
			$(".entity-checkbox:checked").each(function() {
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
				success: function(result) {
					if (result.success == true) {

						console.log("dd");
						Swal.fire(
							'Convert Quote!',
							'Convert Quote!',
							'success'
						)

						Swal.showLoading()
						document.location.reload();

					} else {
						Swal.fire("Can't convert Quote. Try again later, please");
					}
				},
				error: function(result) {
					Swal.fire("Can't convert Quote. Try again later, please");
				}
			});
		}
	}
</script>

<div style="display:none" id="notes">notes</div>
<br />
<div id="headerOne">
	<div class="col-12">

	</div>
	<?php if ($this->status != Entity::STATUS_ARCHIVED) { ?>
		<div class="row">
			<div class="col-4 text-left buttion_mar new_btn_info_new_2">
				<?= functionButton('Print', 'printSelectedQuoteForm()', '', 'btn_bright_blue btn-sm margin_btm_0') ?>

				@form_templates@

				<?php print functionButton('Email', 'emailSelectedQuoteFormNew()', '', 'btn_bright_blue btn-sm'); ?>

				@email_templates@

			</div>
			<div class="col-8 text-right buttion_mar">
				<?php if ($this->status == Entity::STATUS_ACTIVE) { ?>
					<?= functionButton('Convert to Order', 'convertToOrder()', '', 'btn-sm btn_bright_blue') ?>
				<?php } ?>

				<?php if ($this->status == Entity::STATUS_ACTIVE) { ?>
					<?= functionButton('Send Follow-up', 'followupQuote()', '', 'btn-sm btn_bright_blue') ?>
				<?php } ?>

				<?php print functionButton('Reassign Quotes', 'reassignQuoteDialog()', '', 'btn-sm btn_bright_blue'); ?>

				<?php if ($this->status == Entity::STATUS_ACTIVE) : ?>
					<?= functionButton('Place On Hold', 'placeOnHold()', '', 'btn-sm btn_bright_blue') ?>
				<?php elseif ($this->status == Entity::STATUS_ONHOLD) : ?>
					<?= functionButton('Restore', 'restore()', '', 'btn-sm btn_bright_blue') ?>
				<?php endif; ?>


				<?= functionButton('Cancel Quotes', 'cancel()', '', 'btn-sm btn-dark') ?>
			</div>
		</div>

	<?php } else { ?>
		<div class="row">
			<div class="col-12 text-right buttion_mar new_btn_info_new_2">
				<?= functionButton('Uncancel', 'changeStatus(1)', '', 'btn-sm btn-dark') ?>
			</div>
		</div>
	<?php } ?>
</div>
<table class="table table-bordered table_a_link_color" id="quotes_check_new" style="margin-bottom:0;">
	<thead>
		<tr>
			<th>
				<div class="kt-section__content kt-section__content--solid">
					<label class="kt-checkbox kt-checkbox--bold kt-checkbox--success kt-checkbox--all " style="margin-left:2px;float:left;  margin-top:2px">
						<input type="checkbox" onchange="if($(this).is(':checked')){ checkAllEntities() }else{ uncheckAllEntities() }" />
						<span></span>
					</label>
				</div>
				<?php if (isset($this->order)) : ?>
					<?= $this->order->getTitle("id", "ID") ?>
					<?php else : ?>ID<?php endif; ?>
			</th>
			<th>
				<?php if (isset($this->order)) : ?>
					<?= $this->order->getTitle("quoted", "Quoted") ?>
					<?php else : ?>Quoted<?php endif; ?>
			</th>
			<th>Notes</th>
			<th>
				<?php if (isset($this->order)) : ?>
					<?= $this->order->getTitle("shipperfname", "Shipper Information") ?>
					<?php else : ?>Shipper<?php endif; ?>
			</th>
			<th>Vehicle Information</th>
			<th>
				<?php if (isset($this->order)) : ?>
					<?= $this->order->getTitle("Origincity", "Origin") ?>
					<?php else : ?>Origin<?php endif; ?>
					/
					<?php if (isset($this->order)) : ?>
						<?= $this->order->getTitle("Destinationcity", "Destination") ?>
						<?php else : ?>Destination<?php endif; ?>
			</th>
			<th>
				<?php if (isset($this->order)) : ?>
					<?= $this->order->getTitle("est_ship_date", "Est. Ship") ?>
					<?php else : ?>Est. Ship<?php endif; ?>
			</th>
			<th>
				<?php if (isset($this->order)) : ?>
					<?= $this->order->getTitle("tariff", "Transport Cost") ?>
					<?php else : ?>Tariff<?php endif; ?>
			</th>
			
		</tr>
	</thead>
	<tbody>
		<?php if (count($this->entities) == 0) : ?>
			<tr class="grid-body">
				<td colspan="8" align="center" class="grid-body-left grid-body-right"><i>No records</i></td>
			</tr>
		<?php endif; ?>
		<?php
		$i = 0;
		$date_type_string = array(
			1 => "Estimated",
			2 => "Exactly",
			3 => "Not Earlier Than",
			4 => "Not Later Than"
		);

		$ship_via_string = array(
			1 => "Open",
			2 => "Enclosed",
			3 => "Driveaway"
		);

		$searchData = array();

		foreach ($this->entities as $i => $entity) :
			flush();
			$i++;

			$searchData[] = $entity['entityid'];

			$bgcolor = "#ffffff";
			if ($i % 2 == 0)
				//$bgcolor = "#f4f4f4";

				$number = "";
			if (trim($entity['prefix']) != "") {
				$number .= $entity['prefix'] . "-";
			}
			$number .= $entity['number'];
		?>
			<tr id="quote_tr_<?= $entity['entityid'] ?>" class="grid-body<?= ($i == 0 ? " first-row" : "") ?>">
				<td align="center" class="grid-body-left">
					<?php if (!$entity['readonly']) : ?>
						<div class="kt-section__content kt-section__content--solid text-center">
							<label class="kt-checkbox kt-checkbox--bold kt-checkbox--success  kt-checkbox--all" style="padding-left:20px;width:18px;height:18px">
								<input type="checkbox" value="<?= $entity['entityid'] ?>" class="entity-checkbox" />
								<span></span>
							</label>
						</div>
					<?php endif; ?>
					<br/>
					<a class="kt-badge kt-badge--info kt-badge--inline kt-badge--pill order_id" href="<?= SITE_IN ?>application/quotes/show/id/<?= $entity['entityid'] ?>">
						<?php
							if($entity['prefix']){
								echo $entity['prefix']."-".$entity['number']; 
							} else {
								echo $entity['number'];
							}
						?>
					</a>
					<br />
					<a class="kt-badge kt-badge--inline kt-badge--pill" href="<?= SITE_IN ?>application/quotes/history/id/<?= $entity['entityid'] ?>">History</a>
				</td>
				<td valign="top" style="white-space: nowrap;" bgcolor="<?= $bgcolor ?>">
					<span class=""><?= date("m/d/y h:i a", strtotime($entity['quoted'])) ?></span>
					<br>Assigned to:<br /> <strong class="kt-font-success"><?= $entity['AssignedName'] ?></strong><br />
				</td>
				<td bgcolor="<?= $bgcolor ?>">
					<?php
					$notes = new NoteManager($this->daffny->DB);
					$notesData = $notes->getNotesArrData($entity['entityid']);

					$countNewNotes = count($notesData[Note::TYPE_INTERNALNEW]);
					$countInternalNotes = count($notesData[Note::TYPE_INTERNAL]) + $countNewNotes;
					$NotesCount1 = 0;
					if (!is_null($entity['NotesCount1']))
						$NotesCount1 = $entity['NotesCount1'];

					$NotesCount2 = 0;
					if (!is_null($entity['NotesCount2']))
						$NotesCount2 = $entity['NotesCount2'];

					$NotesCount3 = 0;
					if (!is_null($entity['NotesCount3']))
						$NotesCount3 = $entity['NotesCount3'];

					$countNewNotes =  $entity['NotesFlagCount3'];
					?>

					<?= notesIcon($entity['entityid'], $NotesCount1, Note::TYPE_FROM, $entity['status'] == Entity::STATUS_ARCHIVED) ?>
					<?= notesIcon($entity['entityid'], $NotesCount2, Note::TYPE_TO, $entity['status'] == Entity::STATUS_ARCHIVED) ?>
					<?= notesIcon($entity['entityid'], $NotesCount3, Note::TYPE_INTERNAL, $entity['status'] == Entity::STATUS_ARCHIVED, $countNewNotes) ?>
				</td>
				<?php
				if (trim($entity['shipperphone1']) != "") {
					$arrArea = array();
					$arrArea = explode(")", formatPhone($entity['shipperphone1']));

					$code     = str_replace("(", "", $arrArea[0]);
					$areaCodeStr = "";
					//print "WHERE  AreaCode='".$code."'";
					$areaCodeRows = $this->daffny->DB->selectRow("*", "app_area_code", "WHERE  AreaCode='" . $code . "'");
					if (!empty($areaCodeRows)) {
						$areaCodeStr = "<b>" . $areaCodeRows['StdTimeZone'] . "-" . $areaCodeRows['statecode'] . "</b>";
					}
				}
				?>
				<td bgcolor="<?= $bgcolor ?>">
					<span class=""><?= $entity['shipperfname'] ?> <?= $entity['shipperlname'] ?></span><br />
					<?php if ($entity['shippercompany'] != "") { ?>
						<b><?= $entity['shippercompany'] ?></b><br />
					<?php } ?>
					<a href="javascript:void(0);"><?= formatPhone($entity['shipperphone1']) ?></a>
					<br/>
					<a class="kt-font-bold shipper_email" href="mailto:<?= $entity['shipperemail'] ?>"><?= $entity['shipperemail'] ?></a><br>
					<?php if ($entity['referred_id'] != "") { ?>Referred By <b><?php print_r(getRefererByID($this->daffny->DB, $entity['referred_id'])['name']) ?></b><br><?php } 
						else {
							echo "Source : <b>".getLeadSourceByID($this->daffny->DB, $entity['source_id'])[0]['company_name']."</b><br>";
						}
					?>
				</td>
				<td width="13%">
							<?php
							if (count($entity['TotalVehicle']) == 0) {
								?>
								<?php
							} elseif ($entity['TotalVehicle'] == 1) {
								?>
                            		
                            		<a  class="t-badge  kt-badge--warning kt-badge--inline" style="color:#008ec2; cursor: pointer;" onclick="vehiclePopupHandler(1, '<?php
                            			print $entity['entityid'];
                            			?>', '<?php
                            			print $entity['Vehicleid'];
                            			?>')"><?=$entity['Vehicleyear']; ?> <?=$entity['Vehiclemake']; ?> <?=$entity['Vehiclemodel']; ?> <?php
                            		if ($entity['Vehicleinop'] == 1) {
                            			?></a> <?php
                            			echo ("<span style=color:red;weight:bold;>(Inop)</span>");
                            			?> <?php
                            		}
                            		?> <br/>
                            		<?=$entity['Vehicletype']; ?>&nbsp;<?=imageLink($vehicle['Vehicleyear'] . " " . $entity['Vehiclemake'] . " " . $entity['Vehiclemodel'] . " " . $entity['type']) ?>
                            		<br/>
                            		<?php
                            		if ($entity['Vehiclevin'] != "") {
                            			?>
                            			<?=$entity['Vehiclevin']; ?>
                            			<br/>
                            			<?php
                            		}
                            		?>
                            		<?php
                            	} else {
                            		?>      <a class="kt-badge  kt-badge--info kt-badge--inline kt-badge--pill" onclick="vehiclePopupHandler(2, '<?php
                            			print $entity['entityid'];
                            			?>', '<?php
                            			print $entity['Vehicleid'];
                            			?>')">
                            		<span class="multi-vehicles-new" style="color: #ffffff">Multiple Vehicles<b>
                            			<span style="color:#ffffff;">(<?php
                            			print $entity['TotalVehicle'];
                            			?>)</span></b></span>
                            		<div class="vehicles-info" id="vehicles-info-<?php
                            		print $entity['entityid'];
                            		?>">
                            	</div>
                            </a>
                            <br/>
                            <?php
                        }
                        ?>
                        <span style="color:red;weight:bold;"><?php
                        	print ($entity['ship_via'] != 0) ? $ship_via_string[$entity['ship_via']] : "";
                        	?></span><br/>
                        </td>
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
						<?php $cost = $entity['carrier_pay'] + $entity['pickup_terminal_fee'] + $entity['dropoff_terminal_fee'];

						?>
						($ <?= number_format(($cost / $entity['distance']), 2, ".", ",") ?>/mi)
					<?php } ?>
					<span class="kt-font-bold" onclick="mapIt(<?= $entity['entityid'] ?>);">Map it</span>
				</td>
				<td valign="top" align="center" class="grid-body-right" bgcolor="<?= $bgcolor ?>">
					<span class=" kt-badge--warning kt-badge--inline kt-badge--pill">
						<?php
							if($entity['est_ship_date']){
								print date("m/d/y", strtotime($entity['est_ship_date'])); 
							} else {
								echo "N/A";
							}
							
						?>
					</span>
				</td>
				<td width="11%" bgcolor="<?= $bgcolor ?>">
					<table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
						<tr>
							<td width="10"  style="border:none;"><img src="<?= SITE_IN ?>images/icons/dollar.png" alt="Total Tariff" title="Total Tariff" width="16" height="16"/></td>
							<td style="border:none;"><?= ("$ " . number_format((float)$entity['total_tariff_stored'], 2, ".", ",")) ?></td>
						</tr>
						<tr>
							<td width="10"  style="border:none;"><img src="<?= SITE_IN ?>images/icons/truck.png" alt="Carrier Pay" title="Carrier Pay" width="16" height="16"/></td>
							<td style="border:none;"><?= ("$ " . number_format((float)$entity['carrier_pay_stored'], 2, ".", ",")) ?><br/></td>
						</tr>
						<tr>
							<td width="10"  style="border:none;"><img src="<?= SITE_IN ?>images/icons/person.png" alt="Deposit" title="Deposit" width="16" height="16"/></td>
							<td style="border:none;"><?= ("$ " . number_format((float)($entity['total_tariff_stored'] - $entity['carrier_pay_stored']), 2, ".", ",")) ?></td>
						</tr>
					</table>
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

<?php if ($this->status != Entity::STATUS_ARCHIVED) : ?>
	<div class="row">
		<div class="col-12 text-right buttion_mar">
			<?php print functionButton('Reassign Quotes', 'reassignQuoteDialog()', '', 'btn-sm btn_bright_blue'); ?>

			<?php if ($this->status == Entity::STATUS_ACTIVE) : ?>
				<?= functionButton('Place On Hold', 'placeOnHold()', '', 'btn-sm btn_bright_blue') ?>
			<?php elseif ($this->status == Entity::STATUS_ONHOLD) : ?>
				<?= functionButton('Restore', 'restore()', '', 'btn-sm btn_bright_blue') ?>
			<?php endif; ?>

			<?= functionButton('Cancel Quotes', 'cancel()', '', 'btn-sm btn-dark') ?>
		</div>
	</div>
<?php endif; ?>
@pager@

<div class="modal fade" id="maildivnew" tabindex="-1" role="dialog" aria-labelledby="maildivnew_modal" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="maildivnew_modal">Email message</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i class="fa fa-times" aria-hidden="true"></i>
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
				<button type="button" id="maild_id" class="btn btn-primary" onclick="maildivnew()">Submit</button>
			</div>
		</div>
	</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.11.4/ckeditor.js"></script>
<script>
	CKEDITOR.replace('mail_body_new');
</script>

<?php if ($this->status == Entity::STATUS_ACTIVE) : ?>
	<!-- followupdiv  -->
	<div class="modal fade" id="followupdiv" tabindex="-1" role="dialog" aria-labelledby="followupdiv_model" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="followupdiv_model">Followup Quote</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<i class="fa fa-times" aria-hidden="true"></i>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-12">
							<div class="form-group">
								<label>Follow-up:</label>
								<select class="form-box-combobox form-control" name="followup_type" id="followup_type">
									<?php foreach (FollowUp::getTypes() as $key => $type) : ?>
										<option value="<?= $key ?>"><?= $type ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-12">
							<div class="form-group">
								<label>Date:</label>
								<input type="text" class="form-box-textfield" name="followup_when" id="followup_when" />
							</div>
						</div>
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
					<button type="button" class="btn btn-primary" onclick="sendFollowUp()">Submit</button>
				</div>
			</div>
		</div>
	</div>
	<!--  -->
<?php endif; ?>

<link href="<?php echo SITE_IN; ?>styles/new/assets/vendors/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
<script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/custom/datatables/datatables.bundle.js" type="text/javascript"></script>

<script type="text/javascript">
	$(document).ready(function() {
		$('#quotes_check_new').DataTable({
			"order": [[1, 'desc']],
			"lengthChange": false,
			"paging": false,
			"bInfo": false,
			'drawCallback': function(oSettings) {
				$('#quotes_check_new_wrapper').children('.row:first').children('.col-md-6:first').html('<div class="form-group row" style="margin-left:0;"><label class="col-form-label">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
				$('#quotes_check_new_wrapper').children('.row:last').children('.col-md-5').html($('.pager').clone()).addClass('text-left');
				$('#quotes_check_new_wrapper').children('.row:last').children('.col-md-7').html($('.table_b ').html()).addClass('text-right');
				$('.pages-div-custom').remove();

			}
		});
	});
</script>