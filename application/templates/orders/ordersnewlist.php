<!--Bulk Cancel Reason Popup Functionality Starts-->
<link href="<?php echo SITE_IN; ?>styles/new/assets/vendors/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
<script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/custom/datatables/datatables.bundle.js" type="text/javascript"></script>

<style type="text/css">
	.menus_{
		margin-bottom: 12px;
	}
	span.like-link {
		display: inline-block;
		margin: 0px 6px;
		/*padding: 0px 20px 0px 7px;*/
	}
	.form-box-buttons-new {
		display:none;
	}
	thead {
		font-size: 13px;
	}
	.pagination_datatable {
		height: 52px;
	}
	.cke_skin_kama .cke_wrapper {
		background: #2e556a !important;
	}
</style>

<script>
	function Proceed() {

		var cancel_reason = document.getElementById("cancel_reason").value;
		if( cancel_reason == "" ){
			Swal.fire("Please provide valid reason!");
		} else {
			var entity_ids = [];

			$(".order-checkbox:checked").each(function () {
				var entity_id = $(this).val();
				entity_ids.push(entity_id);
			});

            $("#bulk-cancellation-popup-pane").find('.modal-body').addClass('kt-spinner kt-spinner--lg kt-spinner--dark');

			$.ajax({
				type: 'POST',
				url: BASE_PATH + 'application/ajax/entities.php',
				dataType: 'json',
				data: {
					action: 'CancelTheseOrders',
					status: 3,
					entity_ids: entity_ids.join(","),
					cancel_reason: cancel_reason
				},
				success: function (response) {

					$("#bulk-cancellation-popup-pane").find('.modal-body').removeClass('kt-spinner kt-spinner--lg kt-spinner--dark');
					if (response.success == true) {
						window.location.reload();
					}
				}
			});
		}

	}

	function CancelTheseOrders(){

		if ($(".order-checkbox:checked").length == 0) {
			Swal.fire({
				type: 'error',
				title: 'Oops...',
				text: 'Order not selected',
			});
			return false;
		} else {
		 	$("#bulk-cancellation-popup-pane").modal();
		}
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

	$('.add_one_more_field_').on('click', function () {
		$('#mailexttra').css('display', 'block');
		return false;
	});

	$('#singletop').on('click', function () {
		$('#mailexttra').css('display', 'none');
		$('.optionemailextra').val('');
	});
</script>
<!--Bulk Cancel Reason Popup Functionality Ends-->

<div class="modal fade" id="bulk-cancellation-popup-pane" tabindex="-1" role="dialog" aria-labelledby="Modal_exampleModalLongTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id=">Modal_exampleModalLongTitle">Cancel This Taske</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i class="fa fa-times" aria-hidden="true"></i>
				</button>
			</div>
			<div class="modal-body">
			
			<h3>Why you want to cancel this order?</h3>
			<textarea class="form-box-textarea" id="cancel_reason" style="height:200px; "></textarea>
		
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn_light_blue btn-sm" onclick="Proceed()">Proceed</button>
			</div>
		</div>
	</div>
</div>

<?php
	$mobileDevice = 0;
	$mobileDevice = detectMobileDevice();
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.11.4/ckeditor.js"></script>
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
</style>

<!--begin::Modal-->
<div class="modal fade" id="maildivnew" tabindex="-1" role="dialog" aria-labelledby="maildivnew_model" aria-hidden="true">
	<div class="modal-dialog" role="document">
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
<!--end::Modal-->

<div class="modal fade" id="carrierdiv" tabindex="-1" role="dialog" aria-labelledby="carrierdiv1" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="carrierdiv1">Carrier Information</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i class="fa fa-times" aria-hidden="true"></i>
				</button>
			</div>
			<div class="modal-body">
				<div id="carrier_data"></div>
			</div>
			<div class="modal-footer"></div>
		</div>
	</div>
</div>

<?php
	$avail_title = Entity::TITLE_FIRST_AVAIL;
	if (isset($_GET['orders'])) {
		if (in_array($_GET['orders'], array("notsigned", "dispatched", "pickedup", "delivered", "issues", "archived")) || in_array($_GET['mtype'], array(Entity::STATUS_NOTSIGNED, Entity::STATUS_DISPATCHED, Entity::STATUS_PICKEDUP, Entity::STATUS_DELIVERED, Entity::STATUS_ISSUES, Entity::STATUS_ARCHIVED))) {
			$avail_title = Entity::TITLE_PICKUP_DELIVERY;
		}
	}
?>

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
							<tr>
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
								<td  style="width: 90px;">Attachment</td>
								<td class="grid-head-right" style="width: 29px;">Action</td>
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

<!-- sample it skill row start-->
<table id="testData" style="display:none; visibility:hidden;">
	<tr>
		<td>&nbsp;</td><td  colspan="2"><input type="text" name="email_extra" id="email_extra" value="" class="form-box-textfield"  maxlength="100" tooltip='E-mail' style="width:280px;"></td>
	</tr>
</table>

<!--begin::Modal-->
<div class="modal fade" id="reassignCompanyDiv" tabindex="-1" role="dialog" aria-labelledby="reassignCompanyDiv_model" aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="reassignCompanyDiv">Reassign Order(s)</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i class="fa fa-times" aria-hidden="true"></i>
				</button>
			</div>
			<div class="modal-body">
				<div id="reassignCompanyDiv">
					<div class="reassignCompanyDiverror" style="display: none; color: red;padding: 3px"></div>
					<select class="form-box-combobox" id="company_members">
						<option value=""><?php
						print "Select One";
						?></option>
						<?php
						foreach ($this->company_members as $member):
							?>
							<?php
							if ($member->status == "Active") {
								$activemember.= "<option value= '" . $member->id . "'>" . $member->contactname . "</option>";
							}
							?>
							<?php
						endforeach;
						?>
						<optgroup label="Active User">
							<?php
							echo $activemember;
							?>
						</optgroup>
					</select>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-dark btn-sm" data-dismiss="modal">Cancal</button>
				<button type="button" class="btn btn_dark_blue btn-sm " onclick="reassignOrders_submit()">Submit </button>
			</div>
		</div>
	</div>
</div>
<!--end::Modal-->

<script type="text/javascript">
	function makeActionType() {
		var issue_type = encodeURIComponent(document.getElementById('issue_type').value);
		var actionSTR = '';
		actionSTR += '/issue_type/' + issue_type;
		document.issue_form.action = document.issue_form.action + actionSTR;
		location.href = document.issue_form.action;
	}

	function printSelectedOrderForm() {
		if ($(".order-checkbox:checked").length == 0) {
			Swal.fire({
			type: 'error',
			title: 'Oops...',
			text: 'Order not selected.s',
			})
			return false;
		}
		if ($(".order-checkbox:checked").length > 1) {

			Swal.fire({
			type: 'error',
			title: 'Oops...',
			text: 'Error: You may print one order at a time',
			})
			
			return false;
		}
		var order_id = $(".order-checkbox:checked").val();
		form_id = $("#form_templates").val();
		if (form_id == "") {
			Swal.fire({
			type: 'error',
			title: 'Oops...',
			text: 'Please choose form template.',
			})
			return false;
		} else {
			$.ajax({
				url: BASE_PATH + 'application/ajax/entities.php',
				data: {
					action: "print_order",
					form_id: form_id,
					order_id: order_id
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
		if ($(".order-checkbox:checked").length == 0) {
				Swal.fire({
				type: 'error',
				title: 'Oops...',
				text: 'Order not selected!',
				})
			return false;
		}
		var order_id = $(".order-checkbox:checked").val();
		form_id = $("#email_templates").val();
		if (form_id == "") {
			$(".alert-message").empty();
			$(".alert-message").text("Please choose email template.");
			$(".alert-pack").show();
			return false;
		} else {

				Swal.fire({
				title: 'Send Email?',
				text: "Are you sure want to send Email?",
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '.btn btn_bright_blue btn-sm',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Yes, delete it!'
				}).then((result) => {
				if (result.value) {
					$.ajax({
					type: "POST",
					url: BASE_PATH + "application/ajax/entities.php",
					dataType: "json",
					data: {
						action: "emailOrder",
						form_id: form_id,
						entity_id: order_id
					},
					success: function (res) {
						if (res.success) {
							Swal.fire("Email was successfully sent");
						} else {
							Swal.fire("Can't send email. Try again later, please");
						}
					},
					complete: function (res) {
						/*$("body").nimbleLoader('hide');*/
					}
				});
			}
		})



		}
	}

 	function maildivnew_send() { 
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
				// $("body").nimbleLoader("hide");
				if (response.success == true) {
					// $("#maildivnew").dialog("close");
					clearMailForm();
				}

				$("#maildivnew").modal('hide');
			},
			complete: function () {
				// $("body").nimbleLoader("hide");
			}
		});
	}

	function reassignOrdersDialog() {

		if ($(".order-checkbox:checked").length == 0) {
			Swal.fire({
				type: 'error',
				title: 'Oops...',
				text: 'Order not selected',
			})
			return false;
		}

		$("#reassignCompanyDiv").modal();
	}

	function reassignOrders_submit() {
		var member_id = $("#company_members").val();
		reassignOrders(member_id);
	}

	function copyOrders() {
		if ($(".order-checkbox:checked").length == 0) {
				
			Swal.fire({
				type: 'error',
				title: 'Oops...',
				text: 'Order not selected',
			})

			return false;
		}

		var entity_ids = [];
		$(".order-checkbox:checked").each(function () {
			var entity_id = $(this).val();
			entity_ids.push(entity_id);
		});

		var entity_count = entity_ids.length;
		if (entity_count > 1) {
			$(".alert-message").empty();
			$(".alert-message").text("Error: You may copy one order at a time.");
			$(".alert-pack").show();
			return false;
		}

		var order_id = $(".order-checkbox:checked").val();
		location.href = "<?=SITE_IN ?>application/orders/duplicateOrder/id/" + order_id;
	}
    
    function changeOrdersState($val) {
        changeStatusOrders($val);
    }

    function getCarrierData(entity_id, ocity, ostate, ozip, dcity, dstate, dzip) {
    	if (entity_id == "") {
    		Swal.fire("Order not found");
    	} else {
    		
    		$.ajax({
    			type: "POST",
    			url: BASE_PATH + "application/ajax/getcarrier.php",
    			dataType: "json",
    			data: {
    				action: "getcarrier",
    				ocity: ocity,
    				ostate: ostate,
    				ozip: ozip,
    				dcity: dcity,
    				dstate: dstate,
    				dzip: dzip,
    				entity_id: entity_id
    			},
    			success: function (res) {
    				if (res.success) {
    					$("#carrier_data").html(res.carrierData);
    					  Processing_show();

    					  $("#carrierdiv").modal();
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

    function getCarrierDataRoute(entity_id, ocity, ostate, ozip, dcity, dstate, dzip) {
    	if (entity_id == "") {
    		Swal.fire("Order not found");
    	} else {

    	 $('#carrierdiv').find('.modal-body').addClass('kt-spinner kt-spinner--lg kt-spinner--dark');

    		var radius = $("#radius").val();
    		$.ajax({
    			type: "POST",
    			url: BASE_PATH + "application/ajax/getcarrier.php",
    			dataType: "json",
    			data: {
    				action: "getcarrierData",
    				ocity: ocity,
    				ostate: ostate,
    				ozip: ozip,
    				dcity: dcity,
    				dstate: dstate,
    				dzip: dzip,
    				entity_id: entity_id,
    				radius: radius
    			},
    			success: function (res) {
    				if (res.success) {
    					$("#routeCarrierDataDiv").html(res.carrierData);
    				} else {
    					Swal.fire("Can't send email. Try again later, please");
    				}
    			},
    			complete: function (res) {

    		 $('#carrierdiv').find('.modal-body').removeClass('kt-spinner kt-spinner--lg kt-spinner--dark');
    				KTApp.unblockPage();
    			}
    		});
    	}
    }

    function getVehicles(id) {
    	if ($("#vehicles-info-" + id).css('display') == 'block')
    	{
    		$("#vehicles-info-" + id).toggle();
    	} else
    	{
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
    					Swal.fire("Vehicles not found.");
    				}
    			}
    		});
    	}
    }

    function checkAllOrders() {
    	$(".order-checkbox").attr("checked", "checked");
    }

    function uncheckAllOrders() {
    	$(".order-checkbox").attr("checked", null);
    }

    function checkEditDispatch() {
    	var entity_id = $(".order-checkbox:checked").val();
    	$.ajax({
    		type: "POST",
    		url: "<?=SITE_IN ?>application/ajax/entities.php",
    		dataType: 'json',
    		data: {
    			action: 'checkEditDispatch',
    			entity_id: entity_id
    		},
    		success: function (response) {
    			if (response.success == false) {
    				Swal.fire("Someone editing this Order right now. You have access only for read.");
    				return false;
    			} else
    			{
    				setEditBlock(entity_id);
                    $("#acc_search_dialog_new_dispatch").modal();
                }
            }
        });
    }

    function setEditBlock(entity_id) {
    	$.ajax({
    		type: "POST",
    		url: "<?=SITE_IN ?>application/ajax/entities.php",
    		dataType: 'json',
    		data: {
    			action: 'setBlock',
    			entity_id: entity_id
    		},
    		success: function (response) {
    			if (response.success == false) {
                    //document.location.reload();
                }
            }
        });
    }

	const confirmOrder = () => {

		var entity_ids = [];
		$(".order-checkbox:checked").each(function () {
			var entity_id = $(this).val();
			entity_ids.push(entity_id);
		});

		var entity_count = entity_ids.length;
		if (entity_count > 1) {
			$(".alert-message").empty();
			$(".alert-message").text("Error: You may copy one order at a time.");
			$(".alert-pack").show();
			return false;
		}

		if (entity_count == 0) {
			$(".alert-message").empty();
			$(".alert-message").text("Error: No order selected.");
			$(".alert-pack").show();
			return false;
		}

		var entity_id = $(".order-checkbox:checked").val();
		$.ajax({
    		type: "POST",
    		url: "<?=SITE_IN ?>application/ajax/entities.php",
    		dataType: 'json',
    		data: {
    			action: 'CONFIRM_ORDER',
    			entity_id: entity_id
    		},
    		success: function (response) {
    			if (response.success) {
                    document.location.reload();
                }
            }
        });
	}
</script>

<div style="display:none" id="notes">notes</div>
<?php
//if($_GET['orders']!="search")
{
	?>

	<div  class="row">
		<div class="col-12">
			
			<form name="dispatch_date_form" id="dispatch_date_form" method="post">


				<table class="aaaa table table-bordered" <?php
				if ($_SESSION['member_id'] == 1) {
					?> <?php
				} else {
					?> <?php
				}
				?>>
				<tr>
					<?php
					if ($_SESSION['member_id'] == 1) {
						$deposit = $this->todayDispatched[0]['total_tariff_stored'] - $this->todayDispatched[0]['carrier'];
						$gp_per = 0;
						if ($this->todayDispatched[0]['total_tariff_stored'] != 0 && $deposit != 0) {
							$gp_per = ($deposit / $this->todayDispatched[0]['total_tariff_stored']) * 100;
						}
						?>
						<td>
							<span class="pull-left" style="margin-top:10px;margin-right:5px;"># Of Orders Dispatched For : </span> &nbsp; <span class="pull-left">@dispatch_pickup_date@</span>
							<span class="pull-left" style="margin-top:10px;margin-left:5px;"><b><?php print " (" . $this->todayDispatched[0]['todaydispatch'] . ")"; ?></b></span>
						</td>
							<script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script>
							<script type="text/javascript">
								$("#dispatch_pickup_date").datepicker({
									autoclose: true,
									startDate: '+1d'

								});

								$("#dispatch_pickup_date").on("change",function(){
								  var selected = $(this).val();
								  $('#dispatch_date_form').submit();
								});
							</script>

							<td><span >Generating A Total Revenue Of : <b><?=("$ " . number_format((float)$this->todayDispatched[0]['total_tariff_stored'], 2, ".", ",")) ?></b></td>
							<td>Gross Profit of : <b><?=("$ " . number_format((float)($deposit), 2, ".", ",")) ?></b></td>
							<td>GP% : <b><?=number_format((float)($gp_per), 2, ".", ",") ?></b></span></td>
							<?php
						}
						?>

						<td><img class="pull-left" src="<?=SITE_IN
							?>images/icons/dollar.png" alt="Total Tariff" title="Total Tariff" width="16" height="16" /><span ><b><div id="tariffDataTotal" style="width:90px;"></div></b></span></td>
							<?php //print ("$ " . number_format((float)$this->sumAmount[0]['carrier_pay_stored'], 2, ".", ","))

							?>

							<td><img class="pull-left" src="<?=SITE_IN
								?>images/icons/truck.png" alt="<?php
								print $rowCarrier;
								?> Carriers Found" title="<?php
								print $rowCarrier;
								?> Carriers Found" width="16" height="16" /> <span><b><div id="carrierDataTotal" style="width:90px;"></div></b></span></td>
							<?php //print ("$ " . number_format((float)$this->sumAmount[0]['carrier_pay_stored'], 2, ".", ","))

							?>

							<td><img class="pull-left" src="<?=SITE_IN
								?>images/icons/person.png" alt="Deposit" title="Deposit" width="16" height="16"/><span ><b><div id="depositDataTotal"></div></b></span></td>
							<?php //print ("$ " . number_format((float)($this->sumAmount[0]['deposit_stored'] - $entity['carrier_pay_stored']), 2, ".", ","))

							?>

						</tr>

					</table>
				</form>
			</div>
		</div>
<?php
	   }
?>

	<div class="header-one" id="headerOne">
		<div class="col-12">
			
			<div class="filter-custom ">
				<div class="row ">
					<div style="width:100%;margin-bottom:30px;margin-top:15px;">
						<div class="row menus_">
							<div class="col-12 new_btn_info_new_2 text-right">
								
								<?php
									if($this->status == 99){
								?>
								<?php echo functionButton('Confirm Order', 'confirmOrder()','','btn-sm btn_bright_blue') ?>
								<?php
									}
								?>

								<?=functionButton('Print', 'printSelectedOrderForm()','',' btn_bright_blue btn-sm') ?>
								@form_templates@
								<?=functionButton('Email', 'emailSelectedOrderForm()','','btn-dark btn-sm') ?>
								@email_templates@
							
								<?php
								if ($_GET['orders'] == "issues") {
									?>
									<form id="issue_form" style="display:inline-block;" name="issue_form" action="/application/orders/searchIssue" method="post">
										@issue_type@
									</form>
									<?php
								}
								?>
								<?php
								if ($_GET['orders'] == 'posted') {
									?>
									<?=functionButton('Unpost', 'unpostFromFB()','','btn_light_green btn-sm') ?>
									<?php
								} elseif ($_GET['orders'] == "") {
									?>
		                      <?=functionButton('Post Load', 'postToFBMultiple()','','btn_light_green btn-sm') ?>
									<?php
								} elseif ($_GET['orders'] == "dispatched") {
									?>
								<?php print functionButtonDate(Entity::STATUS_PICKEDUP, 'Picked Up Date', 'setPickedUpStatusAndDateMultiple', false, 'pickup_button', 'yy-mm-dd'); ?>
								<?php //print functionButton('Picked Up', 'setPickedUpStatus()'); setPickedUpStatusAndDate ?>
							<?php
						} elseif ($_GET['orders'] == "pickedup") {
							?>
							<?php

							  print functionButtonDate(Entity::STATUS_DELIVERED, 'Delivered Date', 'setPickedUpStatusAndDateMultiple', false, 'delivered_button', 'yy-mm-dd');
							?>
							<?php // print functionButton('Delivered', 'setDeliveredStatus()'); ?>
						<?php
						}
						?>
						<?php
						if (in_array($this->status, array(Entity::STATUS_NOTSIGNED, Entity::STATUS_PICKEDUP, Entity::STATUS_DISPATCHED))) {
							?>
							<button class="btn btn-sm btn_bright_blue" onclick="$operations.cancelDispatch()" type="button" style="" id="Undispatch">Undispatch</button>
							<?php
						}
						?>
						<?php
						if ($this->status == Entity::STATUS_ONHOLD):
							?>
						<?=functionButton('Restore', 'restoreOrders()','','btn-sm btn_bright_blue') ?>
						<?php
						endif;
						?>
						<?php
						if ($this->status != Entity::STATUS_ARCHIVED) {
							?>
							<?php
							if (in_array($_GET['orders'], array("", "posted")) && $_SESSION['member']['access_dispatch']) {
								?>
											<?php //print functionButton('Dispatch', 'dispatch()')

											?><?php
											print functionButton('Dispatch', 'searchCarrierWizard()','','btn_bright_blue btn-sm');
											?>
										<?php
									}
									?>
										<?php
										print functionButton('Reassign Order(s)', 'reassignOrdersDialog()','','btn_bright_blue btn-sm');
										?>
									<?php //print functionButton('Reassign Order', 'reassignOrders(\'top\')');

									?>
								<?php
							}
							?>
							<?php
							if ($this->status == Entity::STATUS_DELIVERED || $this->status == Entity::STATUS_PICKEDUP || $this->status == Entity::STATUS_ISSUES) {
								$stateOrders = Entity::STATUS_PICKEDUP;
								if ($this->status == Entity::STATUS_PICKEDUP) {
									$stateOrders = Entity::STATUS_DISPATCHED;
								} elseif ($this->status == Entity::STATUS_ISSUES) {
									$stateOrders = Entity::STATUS_PICKEDUP;
								}
								?>
									<?php
									print functionButton('Previous Status', 'changeOrdersState(' . $stateOrders . ')','','btn_bright_blue btn-sm');
									?>
									<?php //print functionButton('Reassign Order', 'reassignOrders(\'top\')');

									?>
								<?php
							}
							?>
							<?php
							if ($this->status == Entity::STATUS_ARCHIVED) {
								?>
								<?=functionButton('Uncancel', 'changeStatusOrders(1)','','btn-sm btn-dark') ?>
								<?php
							}
							?>
								<?php print functionButton('Copy Order', 'copyOrders()','','btn_bright_blue btn-sm');?>
								<?php
								if ($this->status == Entity::STATUS_ACTIVE || $_GET['orders'] == 'posted' || $_GET['orders'] == 'notsigned' || $_GET['orders'] == 'dispatched' || $_GET['orders'] == 'pickedup') { ?>
									<?=functionButton('Place On Hold', 'placeOnHoldOrders()','','btn_bright_blue btn-sm') ?>
								<?php } ?>
							<?php
								if ($_GET['orders'] != "archived" && $_GET['orders'] != "delivered" && $_GET['orders'] != "issues") {
									?>
							<?=functionButton('Cancel', 'CancelTheseOrders()','','btn-dark btn-sm') ?>
									<?php
								}
								?>

								
							</div>
						</div>
					</div>
				</div>
			</div>
		
		</div>
	</div>
	<div class="paging_information_new">
		
	</div>
	<table class="table table-bordered table_a_link_color" id="quotes_check_new" style="margin-bottom:0;">
 	
 	<script type="text/javascript">
	function setPagerLimit(val) {
		$.ajax({
			type: "POST",
			url: "<?= SITE_IN ?>application/ajax/member.php?action=setLimit",
			dataType: "json",
			data: "limit="+val,
			success: function(result) {
				if (result.success == true) {
					window.location.reload();
				}
			}
		});
	}
 	</script>

	<thead>
		<tr>
			<th class="text-center" width="6%">
				<label class="kt-checkbox kt-checkbox--bold kt-checkbox--success  kt-checkbox--all " style="margin-top:-15px;">
                <input type="checkbox" onchange="if($(this).is(':checked')){ checkAllEntities() }else{ uncheckAllEntities() }"><span></span></label>
				<?php
				if (isset($this->order)):
				?>
				<?=$this->order->getTitle("id", "ID") ?>
				<?php
				else:
					?>ID<?php
				endif;
				?>
			</th>
			<th width="10%" class="new_color_white">
				<?php
				if ($this->status == Entity::STATUS_ARCHIVED) {
					if (isset($this->order)) {
						print $this->order->getTitle("archived", "Cancelled");
					} else {
						print "Cancelled";
					}
				} elseif ($this->status == Entity::STATUS_PICKEDUP) {
					if (isset($this->order)) {
						print $this->order->getTitle("actual_pickup_date", "Picked Up");
					} else {
						print "Picked Up";
					}
				} elseif ($this->status == Entity::STATUS_DISPATCHED) {
					if (isset($this->order)) {
						print $this->order->getTitle("dispatched", "Dispatched");
					} else {
						print "Dispatched";
					}
				} elseif ($this->status == Entity::STATUS_DELIVERED) {
					if (isset($this->order)) {
						print $this->order->getTitle("delivered", "Delivered");
					} else {
						print "Delivered";
					}
				} elseif ($this->status == Entity::STATUS_POSTED) {
					if (isset($this->order)) {
						print $this->order->getTitle("posted", "Posted");
					} else {
						print "Posted";
					}
				} elseif ($this->status == Entity::STATUS_NOTSIGNED) {
					if (isset($this->order)) {
						print $this->order->getTitle("not_signed", "Not Signed");
					} else {
						print "Not Signed";
					}
				} elseif ($this->status == Entity::STATUS_ISSUES) {
					if (isset($this->order)) {
						print $this->order->getTitle("issue_date", "Issue");
					} else {
						print "Issue";
					}
				} elseif ($this->status == Entity::STATUS_ONHOLD) {
					if (isset($this->order)) {
						print $this->order->getTitle("hold_date", "OnHold");
					} else {
						print "OnHold";
					}
				} else {
					if (isset($this->order)) {
						print $this->order->getTitle("created", "Created");
					} else {
						print "Created";
					}
				}
				?>
			</th>
			<?php
			if ($_GET['orders'] != 'all') { ?>
			<th width="4%">Notes</th>
			<?php } else {  ?>
			<th width="4%">Broker</th>
			<?php } ?>
			<th width="16%">
				<?php
				if (isset($this->order)):
					?>
				<?=$this->order->getTitle("shipperfname", "Shipper") ?>
				<?php
				else:
					?>Shipper<?php
				endif;
				?>
			</th>
			<th width="13%">Vehicle</th>
			<th width="13%">
				<?php
				if (isset($this->order)):
					?>
				<?=$this->order->getTitle("Origincity", "Origin") ?>
				<?php
				else:
					?>Origin<?php
				endif;
				?>
				/
				<?php
				if (isset($this->order)):
					?>
				<?=$this->order->getTitle("Destinationcity", "Destination") ?>
				<?php
				else:
					?>Destinations<?php
				endif;
				?>
			</th>
			<?php
			if ($_GET['orders'] == "searchorders" && !in_array($_GET['mtype'], array(Entity::STATUS_ACTIVE, Entity::STATUS_POSTED, Entity::STATUS_NOTSIGNED, Entity::STATUS_DISPATCHED, Entity::STATUS_PICKEDUP, Entity::STATUS_DELIVERED, Entity::STATUS_ISSUES, Entity::STATUS_ARCHIVED))) { ?>
			<th colspan="2"  width="14%">
				<?="Dates"
				?>
			</th>
			<?php
			} else {
			?>
			<?php
			if ($avail_title == Entity::TITLE_PICKUP_DELIVERY):
				?>
			<th  width="7%">
			<?php
			print $this->order->getTitle("load_date", $avail_title);
			?>
			</th>
			<th  width="7%">
				<?php
				print $this->order->getTitle("delivery_date", "Delivery");
			?>
			</th>
			<?php else: ?>
			<th  width="7%"><?php
				print $this->order->getTitle("avail_pickup_date", $avail_title);
			?><?php //print  $avail_title;

			?><?php //print $this->order->getTitle("avail", $avail_title)

			?>
			<?php
			endif;
			?>
			<?php
			if ($avail_title != Entity::TITLE_PICKUP_DELIVERY) {
				?>
				<th  width="7%">
					<?php
					if (isset($this->order)):
						?>
					<?=$this->order->getTitle("posted", "Posted") ?>
					<?php
					else:
						?>Posted<?php
					endif;
					?>
				</th>
				<?php
			}
			?>
			<?php
			}
			?>
			</th>
			<th  width="14%">
				Payment Options
			</th>
			<th class="grid-head-right"  width="10%">
				<?php
				if (isset($this->order)):
					?>
				<?=$this->order->getTitle("total_tariff", "Tariff Test") ?>
				<?php
				else:
					?>tariff<?php
				endif;
				?>
			</th>
		</tr>
	</thead>
	<tbody>
	<?php
	$i = 0;
	$date_type_string = array(1 => "Estimated", 2 => "Exactly", 3 => "Not Earlier Than", 4 => "Not Later Than");
	$ship_via_string = array(1 => "Open", 2 => "Enclosed", 3 => "Driveaway");
	$totalDeposit = 0;
	$totalCarrier = 0;
	$totalTariff = 0;
	if (count($this->entities) == 0):
	?>
		<tr>
			<td colspan="9" align="center" class="grid-body-left grid-body-right"><i>No records</i></td>
		</tr>
		<?php
		endif;
		?>  
		<?php
		$searchData = array();
		$issue_type = $_POST['issue_type'];
		foreach ($this->entities as $i => $entity): /* @var Entity $entity */
			flush();
		$i++;
		$bgcolor = "#ffffff";
		if ($i % 2 == 0) {
			$bgcolor = "#f4f4f4";
		}
		if (($_GET['orders'] == "searchorders" && $_GET['mtype'] == Entity::STATUS_ISSUES) || ($_GET['orders'] == "issues" || $_GET['mtype'] == '' || $_GET['tab'] == 1)) {
		if ($entity['status'] == Entity::STATUS_ISSUES) {
			$delivery_load_date = date("m/d/y", strtotime($entity['issue_date']));
			$delivery_date_id = '';
			$curr_date = date("m/d/y");
			$diff = abs(strtotime($curr_date) - strtotime($delivery_load_date));
			$date_diff = floor($diff / (60 * 60 * 24));
			if (($date_diff >= 30) && ($date_diff < 45)) {
				$bgcolor = '#BCC8E4';
			} else if (($date_diff >= 45) && ($date_diff < 60)) {
				$bgcolor = '#ECCEF5';
			} else if ($date_diff >= 60 && $date_diff < 90) {
				$bgcolor = '#FFC6BC';
			} else if ($date_diff >= 90) {
				$bgcolor = '#cccccc';
			}
		}
	}
	$searchData[] = $entity['entityid'];
	/*             * ***************** isPaidOffColor ************** */
	$paymentManager = new PaymentManager($this->daffny->DB);
	$owe = 0;
	$isColor = array('total' => 0, 'carrier' => 0, 'deposit' => 0);
	if (!is_null($entity['FlagTarrif'])) {
		$isColor['total'] = $entity['FlagTarrif'];
	}
	if (!is_null($entity['FlagCarrier'])) {
		$isColor['carrier'] = $entity['FlagCarrier'];
	}
	if (!is_null($entity['FlagDeposite'])) {
		$isColor['deposit'] = $entity['FlagDeposite'];
	}
	switch ($entity['balance_paid_by']) {
		case Entity::BALANCE_COP_TO_CARRIER_CASH:
		case Entity::BALANCE_COP_TO_CARRIER_CHECK:
		case Entity::BALANCE_COP_TO_CARRIER_COMCHECK:
		case Entity::BALANCE_COP_TO_CARRIER_QUICKPAY:
		case Entity::BALANCE_COD_TO_CARRIER_CASH:
		case Entity::BALANCE_COD_TO_CARRIER_CHECK:
		case Entity::BALANCE_COD_TO_CARRIER_COMCHECK:
		case Entity::BALANCE_COD_TO_CARRIER_QUICKPAY:
		$shipperPaid = $paymentManager->getFilteredPaymentsTotals($entity['entityid'], Payment::SBJ_SHIPPER, Payment::SBJ_COMPANY, false);
		$owe = ($entity['total_tariff_stored'] - $entity['carrier_pay_stored']) - $shipperPaid;
		if ($owe <= 0) {
			$isColor['deposit'] = 1;
		} else {
			$isColor['deposit'] = 2;
			$totalDeposit+= $entity['total_tariff'] - $entity['total_carrier_pay'];
		}
		break;
		case Entity::BALANCE_COMPANY_OWES_CARRIER_CASH:
		case Entity::BALANCE_COMPANY_OWES_CARRIER_CHECK:
		case Entity::BALANCE_COMPANY_OWES_CARRIER_COMCHECK:
		case Entity::BALANCE_COMPANY_OWES_CARRIER_QUICKPAY:
		case Entity::BALANCE_COMPANY_OWES_CARRIER_ACH:
		$shipperPaid = $paymentManager->getFilteredPaymentsTotals($entity['entityid'], Payment::SBJ_SHIPPER, Payment::SBJ_COMPANY, false);
		$carrierPaid = $paymentManager->getFilteredPaymentsTotals($entity['entityid'], Payment::SBJ_COMPANY, Payment::SBJ_CARRIER, false);
            //print $this->getCost(false)."-----------".$carrierPaid;
            //$owe = $this->getCost(false) - $carrierPaid;
		$cost = $entity['carrier_pay_stored'] + $entity['pickup_terminal_fee'] + $entity['dropoff_terminal_fee'];
		$owe = $cost - $carrierPaid;
	if ($owe <= 0) {
		$isColor['carrier'] = 1;
	} else {
		$isColor['carrier'] = 2;
		$totalCarrier+= $entity['total_carrier_pay'];
	}
	$owe = ($entity['total_tariff_stored'] - $entity['carrier_pay_stored']) - $shipperPaid;
	if ($owe <= 0) {
		$isColor['deposit'] = 1;
	} else {
		$isColor['deposit'] = 2;
		$totalDeposit+= $entity['total_tariff_stored'] - $entity['total_carrier_pay'];
	}
	$owe = $cost + ($entity['total_tariff_stored'] - $entity['carrier_pay_stored']) - $shipperPaid;
	if ($owe <= 0) {
		$isColor['total'] = 1;
	} else {
		$isColor['total'] = 2;
	}
	break;
	case Entity::BALANCE_CARRIER_OWES_COMPANY_CASH:
	case Entity::BALANCE_CARRIER_OWES_COMPANY_CHECK:
	case Entity::BALANCE_CARRIER_OWES_COMPANY_COMCHECK:
	case Entity::BALANCE_CARRIER_OWES_COMPANY_QUICKPAY:
	$carrierPaid = $paymentManager->getFilteredPaymentsTotals($entity['entityid'], Payment::SBJ_CARRIER, Payment::SBJ_COMPANY, false);
	$owe = ($entity['total_tariff_stored'] - $entity['carrier_pay_stored']) - $carrierPaid;
	if ($owe <= 0) {
		$isColor['deposit'] = 1;
	} else {
		$isColor['deposit'] = 2;
		$totalDeposit+= $entity['total_tariff_stored'] - $entity['total_carrier_pay'];
	}
	break;
}
$totalTariff+= $entity['total_tariff'];
/*             * ********************************************* */
$number = "";
if (trim($entity['prefix']) != "") {
	$number.= $entity['prefix'] . "-";
}
$number.= $entity['number'];
    
?>
<tr id="order_tr_<?=$entity['entityid'] ?>" class="<?=($i == 0 ? " first-row" : "") ?>">
	<td align="center" class="grid-body-left" width="6%">
		<?php
		if ($_GET['orders'] != 'all') {
			?>
			<?php
			if (!$entity['readonly']):
				?>
			<?php
			/* ?><input type="radio" name="order_id" value="<?= $entity['entityid'] ?>" class="order-checkbox"/><br/><?php 
			
			<input type="checkbox" name="order_id" value="<?=$entity['entityid'] ?>" class="order-checkbox"/><br/>*/ ?>
			
			
			<label class="kt-checkbox kt-checkbox--bold kt-checkbox--success" style="width:18px;padding:0;">
			<input type="checkbox" name="order_id" value="<?=$entity['entityid'] ?>" class="order-checkbox entity-checkbox">&nbsp;<span></span></label>
			<?php
			endif;
			?>
			<a href="<?=SITE_IN?>application/orders/show/id/<?=$entity['entityid'] ?>"><div class="kt-badge kt-badge--info kt-badge--inline kt-badge--pill order_id"  style="margin:9px 3px"><?=$number ?></div></a>
			<a href="<?=SITE_IN ?>application/orders/history/id/<?=$entity['entityid'] ?>">History</a>
			<?php
			if (isset($_GET['search_string'])):
				print "<br /><b>Status</b><br>";
			if ($entity['status'] == Entity::STATUS_ACTIVE) {
				print "My Order";
			} elseif ($entity['status'] == Entity::STATUS_ONHOLD) {
				print "OnHold";
			} elseif ($entity['status'] == Entity::STATUS_ARCHIVED) {
				print "Cancelled";
			} elseif ($entity['status'] == Entity::STATUS_POSTED) {
				print "Posted To FB";
			} elseif ($entity['status'] == Entity::STATUS_NOTSIGNED) {
				print "Not Signed";
			} elseif ($entity['status'] == Entity::STATUS_DISPATCHED) {
				print "Dispatched";
			} elseif ($entity['status'] == Entity::STATUS_ISSUES) {
				print "Issues";
			} elseif ($entity['status'] == Entity::STATUS_PICKEDUP) {
				print "Picked Up";
			} elseif ($entity['status'] == Entity::STATUS_DELIVERED) {
				print "Delivered";
			}
			?>
			<?php
			endif;
			?>
			<?php
		} else {
			?>
			<?=$number; ?>
			<?php
		}
		?>
	</td>
	<td valign="top" width="10%" style="">
	<?php
	if (isset($_GET['search_string']) && !isset($_GET['mtype'])) { ?>
<span class="kt-font-warning">   
		<?php $tz = isset($_SESSION['timezone']) ? $_SESSION['timezone'] : 'America/New_York';
		$date = new DateTime($entity['created'], new DateTimeZone($tz)); 
		print (is_null($entity['created'])) ? "" : gmdate("m/d/y", $date->getTimestamp()); ?>
                                                                
		</span> 
   <?php	 } else {
		if ($entity['status'] == Entity::STATUS_ARCHIVED || $_GET['mtype'] == Entity::STATUS_ARCHIVED) {
            // print $entity->getArchived("m/d/y h:i a");
			print date("m/d/y", strtotime($entity['archived']));
		} elseif ($entity['status'] == Entity::STATUS_DISPATCHED || $_GET['mtype'] == Entity::STATUS_DISPATCHED) {
            //print $entity->getDispatched("m/d/y h:i a");
			print date("m/d/y h:i a", strtotime($entity['dispatched']));
		} elseif ($entity['status'] == Entity::STATUS_DELIVERED || $_GET['mtype'] == Entity::STATUS_DELIVERED) {
            // print $entity->getDelivered();
			print (is_null($entity['delivered'])) ? "" : date("m/d/y", strtotime($entity['delivered']));
		} elseif ($entity['status'] == Entity::STATUS_POSTED || $_GET['mtype'] == Entity::STATUS_POSTED) {
			print date("m/d/y", strtotime($entity['posted']));
		} elseif ($entity['status'] == Entity::STATUS_NOTSIGNED || $_GET['mtype'] == Entity::STATUS_NOTSIGNED) {
			print date("m/d/y", strtotime($entity['not_signed']));
		} elseif ($entity['status'] == Entity::STATUS_ISSUES || $_GET['mtype'] == Entity::STATUS_ISSUES) {
			print date("m/d/y", strtotime($entity['issue_date']));
		} elseif ($entity['status'] == Entity::STATUS_ONHOLD || $_GET['mtype'] == Entity::STATUS_ONHOLD) {
			print date("m/d/y", strtotime($entity['hold_date']));
		} elseif ($entity['status'] == Entity::STATUS_PICKEDUP || $_GET['mtype'] == Entity::STATUS_PICKEDUP) {
			print date("m/d/y", strtotime($entity['actual_pickup_date']));
		} else {
			print date("m/d/y h:i a", strtotime($entity['created']));
            
        }
    }
    ?>
    <br />
    <?php
    if ($entity['esigned'] == 1) {
    	$sql = "SELECT u.id,u.type,u.name_original
    	FROM app_entity_uploads au
    	LEFT JOIN app_uploads u ON au.upload_id = u.id
    	WHERE au.entity_id = '" . $entity['entityid'] . "'
    	AND u.owner_id = '" . getParentId() . "'
    	AND `name_original` LIKE  'Signed%'
    	ORDER BY u.date_uploaded Desc limit 0,1";
    	$files = $this->daffny->DB->selectRows($sql);
    	if (isset($files) && count($files)) {
    		foreach ($files as $file) {
    			$pos = strpos($file['name_original'], "Signed");
    			if ($pos === false) {
    			} else {
    				?>
				<!--li id="file-<?php //print $file['id'];
                    
				?>"-->
				<a <?=strtolower($file['type']) == 'pdf' ? "target=\"_blank\"" : "" ?> href="<?=getLink("orders", "getdocs", "id", $file['id']) ?>"><span style="font-weight: bold;" class="hint--bottom hint--rounded hint--bounce hint--success" data-hint="e-Sign Generated"><img src="<?=SITE_IN ?>images/icons/esign_small.png" /></span></a>
				<!--/li-->
				<?php
			}
			?>
			<?php
		}
		?>
		<?php
	}
	?>
	<?php
} elseif ($entity['esigned']==2) {
	$sql = "SELECT u.id,u.type,u.name_original
		FROM app_entity_uploads au
		LEFT JOIN app_uploads u ON au.upload_id = u.id
		WHERE au.entity_id = '" . $entity['entityid'] . "'
		AND u.owner_id = '" . getParentId() . "'
		AND `name_original` LIKE  'B2B%'
		ORDER BY u.date_uploaded Desc limit 0,1";
	$files = $this->daffny->DB->selectRows($sql);

	if (isset($files) && count($files)) {
		foreach ($files as $file) {
			$pos = strpos($file['name_original'], "B2B");
			if ($pos === false) {
				// nothing to do
			} else {
				?>
				<a <?=strtolower($file['type']) == 'pdf' ? "target=\"_blank\"" : "" ?> href="<?=getLink("orders", "getdocs", "id", $file['id']) ?>">
					<span style="font-weight: bold;" class="hint--bottom hint--rounded hint--bounce hint--success" data-hint="B2B Generated">
						<img src="<?=SITE_IN ?>images/icons/b2b.png" />
					</span>
				</a>
				<?php
			}
			?>
			<?php
		}
		?>
		<?php
	}
	?>
	<?php
}
?>
<?php
if ($entity['invoice_status'] == 1) {
	?>
	<span style="font-weight: bold;" class="hint--bottom hint--rounded hint--bounce hint--error" data-hint="Invoice Generated"><img src="<?=SITE_IN
		?>images/icons/invoice.png" /></span>
		<?php
	}
	?>
	<br>Assigned to:<br/> <strong class="kt-font-success"><?=$entity['AssignedName'] ?></strong><br />
</td>
<td width="4%">
	<?php
	if ($_GET['orders'] != 'all') {
		$NotesCount1 = 0;
		if (!is_null($entity['NotesCount1'])) {
			$NotesCount1 = $entity['NotesCount1'];
		}
		$NotesCount2 = 0;
		if (!is_null($entity['NotesCount2'])) {
			$NotesCount2 = $entity['NotesCount2'];
		}
		$NotesCount3 = 0;
		if (!is_null($entity['NotesCount3'])) {
			$NotesCount3 = $entity['NotesCount3'];
		}
		$countNewNotes = $entity['NotesFlagCount3'];
        $showColor = 1; //red =1
        //if($entity['assigned_id'] == $_SESSION['member_id'])
        //$showColor = 0;
        
        ?>
        <?php
        /* <?= notesIcon($entity['entityid'], $NotesCount1, Note::TYPE_FROM, $entity['status'] == Entity::STATUS_ARCHIVED) ?> */
        ?>
        <?php
        /* <?= notesIcon($entity['entityid'], $NotesCount2, Note::TYPE_TO, $entity['status'] == Entity::STATUS_ARCHIVED) ?> */
        ?>
        <?=notesIcon($entity['entityid'], $NotesCount3, Note::TYPE_INTERNAL, $entity['status'] == Entity::STATUS_ARCHIVED, $countNewNotes, $showColor) ?>
        <?php
    } else {
    	?>
    	<?php
    	print $entity['companyname'];
    	?>
    	<?php
    }
    ?>
</td>
<?php
if (trim($entity['shipperphone1']) != "") {
        /*
        $arrArea = array();
        $arrArea = explode(")",formatPhone($entity['shipperphone1']));
        $code     = str_replace("(","",$arrArea[0]);
        */
        $code = substr($entity['shipperphone1'], 0, 3);
        $areaCodeStr = "";
        //print "WHERE  AreaCode='".$code."'";
        $areaCodeRows = $this->daffny->DB->selectRow("*", "app_area_code", "WHERE  AreaCode='" . $code . "'");
        if (!empty($areaCodeRows)) {
        	$areaCodeStr = "<b>" . $areaCodeRows['StdTimeZone'] . "-" . $areaCodeRows['statecode'] . "</b>";
        }
    }
    if (trim($entity['shipperphone2']) != "") {
        /*
        $arrArea = array();
        $arrArea2 = explode(")",formatPhone($entity['shipperphone2']));
        $code     = str_replace("(","",$arrArea2[0]);
        */
        $code = substr($entity['shipperphone1'], 0, 3);
        $areaCodeStr2 = "";
        //print "WHERE  AreaCode='".$code."'";
        $areaCodeRows2 = $this->daffny->DB->selectRow("*", "app_area_code", "WHERE  AreaCode='" . $code . "'");
        if (!empty($areaCodeRows2)) {
        	$areaCodeStr2 = "<b>" . $areaCodeRows2['StdTimeZone'] . "-" . $areaCodeRows2['statecode'] . "</b>";
        }
    }
    ?>
    <?php
    
    $phone1 = "1" . $entity['shipperphone1'];
    $phone2 = "1" . $entity['shipperphone2'];
    $phone1_ext = '';
    $phone2_ext = '';
    if ($entity['shipperphone1_ext'] != '') {
    	$phone1_ext = " <b>X</b> " . $entity['shipperphone1_ext'];
    }
    if ($entity['shipperphone2_ext'] != '') {
    	$phone2_ext = " <b>X</b> " . $entity['shipperphone2_ext'];
    }
    ?>
    <td width="16%">
    <div class="shipper_name">
    	<span class="kt-font-bold"><?=$entity['shipperfname'] ?> <?=$entity['shipperlname'] ?></span><br/></div>
    <?php
    if ($entity['shippercompany'] != "") {
    	?><div class="shipper_company"><b><?=$entity['shippercompany'] ?></b><br /></div><?php
    }
    ?>
    
    <?php
    if ($entity['shipperphone1'] != "") {
    	?><div class="shipper_number">
    	<?php
    	if ($mobileDevice == 1) {
    		?>
    		<a href="tel:<?php
    		print $phone1;
    		?>" ><?=formatPhone($entity['shipperphone1']) ?></a>
    		<?php
    	} else {
    		?>
    		<a href="javascript:void(0);" onclick="customPhoneSms('<?=$phone1; ?>', '<?=$entity['shipperfname'] ?> <?=$entity['shipperlname'] ?>');"><?=formatPhone($entity['shipperphone1']) ?> </a>
    		<?php
    	}
    	?>
    	<?=$phone1_ext; ?> <?=$areaCodeStr; ?><br/></div>
    	<?php
    }
    ?>
    <?php
    if ($entity['shipperphone2'] != "") {
    	$phone2 = str_replace($words, $wordsReplace, $entity['shipperphone2']);
    	?><div class="shipper_number">
    	<?php
    	if ($mobileDevice == 1) {
    		?>
    		<a href="tel:<?php
    		print $phone2;
    		?>" ><?=formatPhone($entity['shipperphone2']) ?></a>
    		<?php
    	} else {
    		?>
    		<a href="javascript:void(0);" onclick="customPhoneSms('<?=$phone2; ?>', '<?=$entity['shipperfname'] ?> <?=$entity['shipperlname'] ?>');"><?=formatPhone($entity['shipperphone2']) ?> </a>
    		<?php
    	}
    	?>
    	<?=$phone2_ext; ?> <?=$areaCodeStr2; ?><br/></div><?php
    }
    ?>
    <?php
    if ($entity['shipperemail'] != "") {
    	?>
    	<?php
    	if (strlen($entity['shipperemail']) < 25) {
    		?>
    		<a href="mailto:<?=$entity['shipperemail'] ?>" TITLE="<?=$entity['shipperemail'] ?>"><div 
    			class=" kt-font-bold shipper_email shipper_email"><?=$entity['shipperemail'] ?><br/></div></a>
    		<?php
    	} else {
    		?>
    		<a href="mailto:<?=$entity['shipperemail'] ?>"  TITLE="<?=$entity['shipperemail'] ?>"><div 
    			class="  kt-font-bold shipper_email shipper_email" ><?=substr($entity['shipperemail'], 0, 25) ?><br/></div></a>
    		<?php
    	}
    	?>
    	<?php
    }
    ?>
    <div class="shipper_referred"><?php
    	if ($entity['referred_by'] != "") {
    		?>
    		Source: <b><?=$entity['referred_by'] ?></b><br>
    	</div>

    	<?php
    } else {
    	$member = new Member($this->daffny->DB);
    	$member->load($entity['assigned_id']);
    	if ($member->hide_lead_sources == 0) {
    		?>
    		<strong>Source: </strong><?php
    		print $entity['source_name'];
    		?>

    		<?php
    	}
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
                        $o_link = "https://maps.google.com/maps?q=" . urlencode($entity['Origincity'] . ",+" . $entity['Originstate']);
                        $o_formatted = trim($entity['Origincity'] . ', ' . $entity['Originstate'] . ' ' . $entity['Originzip'], ", ");
                        $d_link = "https://maps.google.com/maps?q=" . urlencode($entity['Destinationcity'] . ",+" . $entity['Destinationstate']);
                        $d_formatted = trim($entity['Destinationcity'] . ', ' . $entity['Destinationstate'] . ' ' . $entity['Destinationzip'], ", ");
                        ?>
                        <td width="13%">
                        <span class="kt-font-bold"
                        onclick="window.open('<?=$o_link
                        	?>', '_blank')"><?=$o_formatted
                        	?></span> /<br/>
                        	<span class="kt-font-bold"
                        	onclick="window.open('<?=$d_link
                        		?>')"><?=$d_formatted
                        		?></span><br/>
                        		<?php
                        		if (is_numeric($entity['distance']) && ($entity['distance'] > 0)) {
                        			?>
                        			<?=number_format($entity['distance'], 0, "", "") ?> mi
                        			<?php
                        			$cost = $entity['carrier_pay_stored'] + $entity['pickup_terminal_fee'] + $entity['dropoff_terminal_fee'];
                        			?>
                        			($ <?=number_format(($cost / $entity['distance']), 2, ".", ",") ?>/mi)
                        			<?php
                        		}
                        		?>
                        		<span class="kt-font-bold" onclick="mapIt(<?=$entity['entityid'] ?>);">Map it</span>
                        	</td>
                        	<?php
                        	$balance_paid_by_arr = array(Entity::BALANCE_COD_TO_CARRIER_CASH => 'Cash/Certified Funds', Entity::BALANCE_COD_TO_CARRIER_CHECK => 'Check', Entity::BALANCE_COP_TO_CARRIER_CASH => 'Cash/Certified Funds', Entity::BALANCE_COP_TO_CARRIER_CHECK => 'Check', Entity::BALANCE_COMPANY_OWES_CARRIER_CASH => 'Cash/Certified Funds', Entity::BALANCE_COMPANY_OWES_CARRIER_CHECK => 'Check', Entity::BALANCE_COMPANY_OWES_CARRIER_COMCHECK => 'Comcheck', Entity::BALANCE_COMPANY_OWES_CARRIER_QUICKPAY => 'QuickPay', Entity::BALANCE_COMPANY_OWES_CARRIER_ACH => 'ACH', Entity::BALANCE_CARRIER_OWES_COMPANY_CASH => 'Cash/Certified Funds', Entity::BALANCE_CARRIER_OWES_COMPANY_CHECK => 'Check', Entity::BALANCE_CARRIER_OWES_COMPANY_COMCHECK => 'Comcheck', Entity::BALANCE_CARRIER_OWES_COMPANY_QUICKPAY => 'QuickPay');
                        	$balance_paid_by_value = $entity['balance_paid_by'];
                        	$Balance_Paid_By = "";
                        	if (in_array($entity['balance_paid_by'], array(2, 3, 16, 17))) {
                        		$Balance_Paid_By = "COD";
                        	}
                        	if (in_array($entity['balance_paid_by'], array(8, 9, 18, 19))) {
                        		$Balance_Paid_By = "COP";
                        	}
                        	if (in_array($entity['balance_paid_by'], array(12, 13, 20, 21, 24))) {
                        		$Balance_Paid_By = "Broker:" . $balance_paid_by_arr[$balance_paid_by_value];
                        	}
                        	if (in_array($entity['balance_paid_by'], array(14, 15, 22, 23))) {
                        		$Balance_Paid_By = "Shipper:" . $balance_paid_by_arr[$balance_paid_by_value];
                        	}
                        	if ($_GET['orders'] == "searchorders") {
                        		$Date1 = "";
                        		$Date2 = "";
                        		if ($entity['status'] == Entity::STATUS_POSTED || $entity['status'] == Entity::STATUS_ACTIVE) {
                        			if (strtotime($entity['avail_pickup_date']) > 0) {
                        				$Date1 = "<b>1st avil:</b><br>" . date("m/d/y", strtotime($entity['avail_pickup_date'])) . "<br>";
                        			}
                        			if (strtotime($entity['posted']) > 0) {
                        				$Date2 = "<b>Posted:</b><br>" . date("m/d/y", strtotime($entity['posted']));
                        			}
                        		} elseif ($entity['status'] == Entity::STATUS_NOTSIGNED || $entity['status'] == Entity::STATUS_DISPATCHED) {
                        			if (strtotime($entity['load_date']) == 0) {
                        				$abbr = "N/A";
                        			} else {
                        				$abbr = $entity['load_date_type'] > 0 ? $date_type_string[(int)$entity['load_date_type']] : "";
                        				$Date1 = "<b>ETA Pickup:</b><br />" . $abbr . "<br />" . date("m/d/y", strtotime($entity['load_date']));
                        			}
                        			if (strtotime($entity['delivery_date']) == 0) {
                        				$abbr = "N/A";
                        			} else {
                        				$abbr = $entity['delivery_date_type'] > 0 ? $date_type_string[(int)$entity['delivery_date_type']] : "";
                        				$Date2 = "<b>ETA Delivery:</b><br />" . $abbr . "<br />" . date("m/d/y", strtotime($entity['delivery_date']));
                        			}
            //$Date2 = "<b>ETA Delivery:-</b><br>".$entity->getDeliveryDateWithAbbr("m/d/y");

                        		} elseif ($entity['status'] == Entity::STATUS_PICKEDUP) {
                        			if (strtotime($entity['actual_pickup_date']) > 0) {
                        				$Date1 = "<b>Pickup:</b><br>" . date("m/d/y", strtotime($entity['actual_pickup_date']));
                        			}
                        			if (strtotime($entity['delivery_date']) == 0) {
                        				$abbr = "N/A";
                        			} else {
                        				$abbr = $entity['delivery_date_type'] > 0 ? $date_type_string[(int)$entity['delivery_date_type']] : "";
                        				$Date2 = $abbr . "<br />" . date("m/d/y", strtotime($entity['delivery_date']));
                        			}
            //$Date2 = "<b>ETA Unload:-</b><br>".$entity->getDeliveryDateWithAbbr("m/d/y");

                        		} elseif ($entity['status'] == Entity::STATUS_ISSUES || $entity['status'] == Entity::STATUS_DELIVERED) {
                        			if (strtotime($entity['load_date']) == 0) {
                        				$abbr = "N/A";
                        			} else {
                        				$abbr = $entity['load_date_type'] > 0 ? $date_type_string[(int)$entity['load_date_type']] : "";
                        				$Date1 = "<b>ETA Pickup:</b><br />" . $abbr . "<br />" . date("m/d/y", strtotime($entity['load_date']));
                        			}
            //$Date1 = "<b>ETA Pickup:-</b><br>".$entity->getLoadDateWithAbbr("m/d/y")."<br>";
                        			if (strtotime($entity['delivery_date']) == 0) {
                        				$abbr = "N/A";
                        			} else {
                        				$abbr = $entity['delivery_date_type'] > 0 ? $date_type_string[(int)$entity['delivery_date_type']] : "";
                        				$Date2 = "<b>ETA Delivery:</b><br />" . $abbr . "<br />" . date("m/d/y", strtotime($entity['delivery_date']));
                        			}
        } /*
        elseif($entity['status'] == Entity::STATUS_DELIVERED){
        if ($entity['actual_pickup_date']!="")
        $Date1 = "<b>Pickup:-</b><br>".date("m/d/y h:i a", strtotime($entity['actual_pickup_date']));
        if(!is_null($entity['delivered']))
        $Date2 = "<b>Delivered:-</b><br>".date("m/d/y h:i a", strtotime($entity['delivered']));
    } */
    elseif ($entity['status'] == Entity::STATUS_ONHOLD) {
    	if (strtotime($entity['avail_pickup_date']) > 0) {
    		$Date1 = "<b>1st avil:</b><br>" . date("m/d/y", strtotime($entity['avail_pickup_date']));
    	}
    	if ($entity['hold_date'] != "") {
    		$Date2 = "<b>Hold:</b><br>" . date("m/d/y", strtotime($entity['hold_date']));
    	}
    } elseif ($entity['status'] == Entity::STATUS_ARCHIVED) {
    	if (strtotime($entity['avail_pickup_date']) > 0) {
    		$Date1 = "<b>1st avil:</b><br>" . date("m/d/y", strtotime($entity['avail_pickup_date']));
    	}
    	if ($entity['archived'] != "") {
    		$Date2 = "<b>Cancelled:</b><br>" . date("m/d/y", strtotime($entity['archived']));
    	}
    }
    ?>
    <td valign="top" align="center" width="7%">
    	<span class="">
    <?php
    echo $Date1;
    ?>
</span>
</td>
<td valign="top" align="center" width="7%">
<span class=""><?php
echo $Date2;
?>
</span>
</td>
<?php
} else {
	if ($avail_title == Entity::TITLE_PICKUP_DELIVERY) {
		?>
		<td valign="top" align="center" width="7%">
			<span class="">
		<?php
		if (strtotime($entity['load_date']) == 0) {
			$abbr = "N/A";
		} else {
			$abbr = $entity['load_date_type'] > 0 ? $date_type_string[(int)$entity['load_date_type']] : "";
			$abbr = $abbr . "<br />" . date("m/d/y", strtotime($entity['load_date']));
		}
		?>
		<?php
		echo $abbr;
		?>
	</span>
	</td>
	<td valign="top" align="center" width="7%">
		
	<?php
	if (strtotime($entity['delivery_date']) == 0) {
		$abbr = "N/A";
	} else {
		$abbr = $entity['delivery_date_type'] > 0 ? $date_type_string[(int)$entity['delivery_date_type']] : "";
		$abbr = $abbr . "<br />" . date("m/d/y", strtotime($entity['delivery_date']));
	}
	?>
	<?php
	echo $abbr;
	?>

</td>
<?php
} else {
	?>
	<td valign="top" align="center" width="7%">
		
	<?php
	if (strtotime($entity['avail_pickup_date']) == 0) {
		$avail_pickup_date = "";
	} else {
		$avail_pickup_date = date("m/d/y", strtotime($entity['avail_pickup_date']));
	}
	?>
	<span class="">
		<?php
		echo $avail_pickup_date;
		?>
</span>
</td>
<?php
}
?>
<?php
if ($avail_title != Entity::TITLE_PICKUP_DELIVERY) {
	?>
	<td valign="top" align="center" width="7%">
		
	<?php
	if (strtotime($entity['posted']) == 0) {
		$postDate = "";
	} else {
		$postDate = date("m/d/y", strtotime($entity['posted']));
	}
	?>
	<?php
	echo $postDate;
	?>

</td>
<?php
}
?>
<?php
}
?>
<td width="14%">
<?php
$optionStr = "";
if ($entity['customer_balance_paid_by'] == Entity::WIRE_TRANSFER) {
	$optionStr = "Wire - Transfer";
} elseif ($entity['customer_balance_paid_by'] == Entity::MONEY_ORDER) {
	$optionStr = "Money Order";
} elseif ($entity['customer_balance_paid_by'] == Entity::CREDIT_CARD) {
	$optionStr = "Credit Card";
} elseif ($entity['customer_balance_paid_by'] == Entity::PARSONAL_CHECK) {
	$optionStr = "Personal Check";
} elseif ($entity['customer_balance_paid_by'] == Entity::COMPANY_CHECK) {
	$optionStr = "Company Check";
} elseif ($entity['customer_balance_paid_by'] == Entity::ACH) {
	$optionStr = "ACH";
} else {
	$optionStr = "N/A";
}
if (in_array($entity['balance_paid_by'], array(14, 15, 22, 23))) {
	$optionStr = "Invoice Carrier";
}
?>
<strong>Customer Paying Us By:</strong>  <font color="red"><?php
print $optionStr;
?></font>
<br /><strong>Carrier Getting Paid By:</strong>  <font color="red"><?php
print $Balance_Paid_By;
?></font>
<?php
if ($_SESSION['member']['access_payments'] == 1) {
	?>
	<br /><br /><a href="javascript:void(0);" onclick="process_payment(<?php
		print $entity['entityid'];
		?>);">Process Payment</a>
				<!--br /><a href="javascript:void(0);" onclick="refund_payment(<?php //print $entity['entityid'];
        
				?>);">Refund</a-->
				<?php
			}
			?>
		</td>
		<?php
		$Dcolor = "black";
		$Ccolor = "black";
		$Tcolor = "black";
		if ($entity['type'] == Entity::TYPE_ORDER && $entity['status'] != Entity::STATUS_ARCHIVED) {
        //$isColor = $entity->isPaidOffColor();
			if ($isColor['carrier'] == 1) {
				$Ccolor = "green";
			} elseif ($isColor['carrier'] == 2) {
				$Ccolor = "red";
			}
			if ($isColor['deposit'] == 1) {
				$Dcolor = "green";
			} elseif ($isColor['deposit'] == 2) {
				$Dcolor = "red";
			}
			if ($isColor['total'] == 1) {
				$Tcolor = "green";
			} elseif ($isColor['total'] == 2) {
				$Tcolor = "red";
			}
		}
		?>
		<td class="grid-body-right" width="10%">
		<table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
			<tr>
				<td width="10">
					<span style="font-weight: bold;" class="hint--left hint--rounded hint--bounce" data-hint="Total Cost">
						<img src="<?=SITE_IN ?>images/icons/dollar.png" width="16" height="16"/>
					</span>
				</td>
				<td style="white-space: nowrap;">
					<span style="font-weight: bold;" class="hint--right hint--rounded hint--bounce" data-hint="Total Cost">
						<span class='<?=$Tcolor; ?>'><?=("$ " . number_format((float)$entity['total_tariff'], 2, ".", ",")) ?></span>
					</span>
				</td>
			</tr>
			<tr>
				<td width="10">
					<span style="font-weight: bold;" class="hint--left hint--rounded hint--bounce" data-hint="Carrier Information">
						<img src="<?=SITE_IN ?>images/icons/truck.png" alt="<?php
						print $rowCarrier;
						?>" title="<?php
						print $rowCarrier;
						?>" width="16" height="16" onclick="getCarrierData(<?php
							print $entity['entityid'];
							?>, '<?php
							print $entity['Origincity'];
							?>', '<?php
							print $entity['Originstate'];
							?>', '<?php
							print $entity['Originzip'];
							?>', '<?php
							print $entity['Destinationcity'];
							?>', '<?php
							print $entity['Destinationstate'];
							?>', '<?php
							print $entity['Destinationzip'];
							?>');"/>
						</span>
					</td>
					<td style="white-space: nowrap;">
						<span style="font-weight: bold;" class="hint--right hint--rounded hint--bounce" data-hint="Carrier Fee">
							<span class='<?=$Ccolor; ?>'>
								<?=("$ " . number_format((float)$entity['total_carrier_pay'], 2, ".", ",")) ?>
							</span>
						</span>
						<br/>
					</td>
				</tr>
				<tr>
					<td width="10">
						<span style="font-weight: bold;" class="hint--left hint--rounded hint--bounce" data-hint="Broker Fee">
							<img src="<?=SITE_IN
							?>images/icons/person.png" width="16" height="16"/>
						</span>
					</td>
					<td style="white-space: nowrap;">
						<span style="font-weight: bold;" class="hint--right hint--rounded hint--bounce" data-hint="Broker Fee">
							<span class='<?=$Dcolor; ?>'>
								<?=("$ " . number_format((float)($entity['total_tariff'] - $entity['total_carrier_pay']), 2, ".", ",")) ?>
							</span>
						</span>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<?php
	endforeach;
	?>
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


<div class="row">
<div class="col-12 text-right">
	<div class="pagination_datatable">

	</div>
</div>
</div>


<div class="col-12">
	@pager@
</div>

<?php
if (($_GET['orders'] == "issues") || ($_GET['orders'] == "searchorders" && $_GET['mtype'] == Entity::STATUS_ISSUES) || $_GET['mtype'] == '' || $_GET['tab'] == 1) {
	?>
	<div class="row" style="margin-top:20px;">
		<div class="col-3" style="margin-bottom:15px;">
			<span style="background-color:#BCC8E4;width:100%;" class="hint--bottom text-center" data-hint="Past due!"><b>30 Days in Issues</b></span>
		</div>
		
		<div class="col-3">
			<span style="background-color:#ECCEF5;width:100%;" class="hint--bottom text-center" data-hint="Kinda past due!"><b>45 Days in Issues</b></span>
		</div>
		
		<div class="col-3">
			<span style="background-color:#FFC6BC;width:100%;" class="hint--bottom text-center" data-hint="Pretty past due!"><b>60 Days in Issues</b></span>
		</div>
		
		<div class="col-3">
			<span style="background-color:#ccc;width:100%;" class="hint--bottom text-center" data-hint="Very past due!"><b>90 Days in Issues</b></span>
		</div>
	</div>

	
	<?php
}
?>

<?php
	$totalTariff = "$ " . number_format((float)($totalTariff), 2, ".", ",");
	$totalCarrier = "$ " . number_format((float)($totalCarrier), 2, ".", ",");
	$totalDeposit = "$ " . number_format((float)($totalDeposit), 2, ".", ",");
?>
<script type="text/javascript">

//    $(document).ready(function() {
// 		$('#quotes_check_new').DataTable({
// 		// "lengthChange": false,
// 		// "paging": false,
// 		// "bInfo" : false,
// 		'drawCallback': function (oSettings) {
// 			$('#quotes_check_new_wrapper').children('.row:first').children('.col-md-6:first').html('<div class="form-group row" style="margin-left:0;"><label class="col-form-label">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
// 			$('#quotes_check_new_wrapper').children('.row:last').children('.col-md-5').html($('.pager').clone()).addClass('text-left');
// 			$('#quotes_check_new_wrapper').children('.row:last').children('.col-md-7').html($('.table_b ').html()).addClass('text-right');
// 			$('.pages-div-custom').remove();
			
// 		}
// 		});

// 		// $('#quotes_check_new th:eq(1)').trigger('click');
// 		$(".records_per_page").val(<?php echo $_SESSION['per_page'];?>);
// 	});
</script>