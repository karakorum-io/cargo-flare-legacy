<style type="text/css">
	.modal .modal-content .modal-header .close:before {
		display: none;
	}
	.nav-tabs.nav-tabs-line {
		margin-bottom:0;
	}
</style>

<script type="text/javascript">
	function validateEmail(sEmail) {
		var res="",res1="",i;
		var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
		for (i = 0; i < sEmail.length; i++){
			if (filter.test(sEmail[i])){
				res += sEmail[i];
			} else {
				res1 += sEmail[i];
			}
		}
		if(res1!==''){
			return false;
		}
	}

	var anim_busy = false;
	function setStatus(entity_id, status) {
		$.ajax({
			type: "POST",
			url: "<?=SITE_IN?>application/ajax/entities.php",
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
					Swal.fire("Lead action failed. Try again later, please.");
				}
			},
			error: function(result) {
				Swal.fire("Lead action failed. Try again later, please.");
			}
		})
	}

	function reassign(entity_id) {
		$("#reassign_dialog").modal();
		$("#entities").val(entity_id);
	}

	function reassign_status(entity_id) {
		var assign_id = $("#reassign_dialog select").val();

		$.ajax({
			type: "POST",
			url: "<?=SITE_IN?>application/ajax/entities.php",
			dataType: "json",
			data: {
				action: 'reassign',
				entity_id: entity_id,
				assign_id: assign_id
			},
			success: function(result) {
				  console.log(result);
				if (result.success == true) {
					window.location.reload();
				} else {
					Swal.fire("Can't reassign lead. Try again later");
				}
			},
			error: function(result) {
				Swal.fire("Can't reassign lead. Try again later please");
			}
		});
	}

	function Processing_show() {
        KTApp.blockPage({
			overlayColor: '#000000',
			type: 'v2',
			state: 'primary',
			message: '.'
        });
	}

	function saveQuotes(email,entity_id) {

		var entity_ids = [];
		entity_ids.push('{"entity_id":"'+entity_id+'"}');

		Processing_show();
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
					window.location.href = '<?=SITE_IN?>application/leads/showcreated/id/'+entity_id;
				} else {
					Swal.fire("Can't save Quote(s)");
				}
			},
            complete: function(response) {
				KTApp.unblockPage();
            }
        });
    }

	function changeStatusLeadsData(status,entity_id) {
		if (entity_id == '') {
			Swal.fire("You have no order id.");
		} else {
			var entity_ids = [];

			Processing_show();
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
				},
				error: function(response) {
					KTApp.unblockPage();
					Swal.fire("Try again later, please");
				},
				complete: function(response) {
					KTApp.unblockPage();
				}
			});
		}
	}

	function convertToQuote(entity_id) {
		$.ajax({
			type: "POST",
			url: "<?=SITE_IN?>application/ajax/entities.php",
			dataType: "json",
			data: {
				action: 'toQuote',
				entity_id: entity_id
			},
			success: function(result) {
				if (result.success == true) {
					window.location.href = '<?=SITE_IN?>application/quotes/edit/id/'+entity_id;
				} else {
					if (result.reason != undefined) {
						Swal.fire(result.reason);
					} else {
						Swal.fire("Can't convert Lead. Try again later, please.");
					}
				}
			},
			error: function(result) {
				Swal.fire("Can't convert Lead. Try again later, please.");
			}
		});
	}

	function convertToOrder(entity_id) {
		var entity_ids = [];
		entity_ids.push(entity_id);

       	$.ajax({
            type: "POST",
            url: "<?=SITE_IN?>application/ajax/entities.php",
            dataType: "json",
            data: {
                action: "LeadtoOrderCreated",
                entity_ids: entity_ids.join(',')
            },
            success: function (result) {
                if (result.success == true) {
                  window.location.href = '<?=SITE_IN?>application/orders/edit/id/'+entity_id;

                } else {
                    Swal.fire("Can't convert to Order. Try again later, please");
                }
            },
            error: function (result) {
                Swal.fire("Can't convert to Order. Try again later, please");
            }
        });
   	}

	function setAppointmentDetail() {
		$("#appointmentDiv").dialog("open");
	}

	function setAppointmentDataDetail(app_date,app_time,notes)
	{

		if ( app_date == '') {
		  alert("You select appointment date.");
		  return;
		}

		if ( app_time == '') {
		  alert("You select appointment time.");
		  return;
		}

		var entity_id = <?php print $_GET['id'];?>;
		var entity_ids = [];

		$.ajax({
			type: 'POST',
			url: '<?=SITE_IN?>application/ajax/entities.php',
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
				}
			},
			error: function(response) {
				alert("Set appointment. Try again later, please.");
			},
			complete: function (res) {
			}
		});
	}

	$(document).ready(function(){
		$("#app_date").datepicker({
			dateFormat: "yy-mm-dd",
            minDate: '+0'
		});

		$('.add_one_more_field_').on('click',function(){
			$('#mailexttra').css('display','block');
			return false;
		});

		$('#singletop').on('click',function(){
			$('#mailexttra').css('display','none');
			$('.optionemailextra').val('');
		});
	});
</script>

<!--begin::Modal-->
<div class="modal fade" id="reassign_dialog" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Reassign Lead</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i class="fa fa-times" aria-hidden="true"></i>
				</button>
			</div>
			<div class="modal-body">
				<select class="form-box-combobox form-control" id="company_members">
					<option value=""><?php print "Select One";?></option>
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
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="button" id="entities" value="" onclick="reassign_status(this.value)" class="btn btn-primary">Assign</button>
			</div>
		</div>
	</div>
</div>
<!--end::Modal-->

<!--begin::Modal-->
<div class="modal fade" id="maildivnew" tabindex="-1" role="dialog" aria-labelledby="maildivnew_model" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="maildivnew_model">Email Send</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i class="fa fa-times" aria-hidden="true"></i>
				</button>
			</div>
			<div class="modal-body" id="email-template-results">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-sm btn-dark" data-dismiss="modal">Cancal</button>
				<button type="button" class="btn btn_dark_green btn-sm " onclick="maildivnew_submit()">Submit</button>
			</div>
		</div>
	</div>
</div>
<!--end::Modal-->

<div class="alert alert-light alert-elevate">
	<div class="row">
		<div class="col-12">
			<ul class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-success">
			   <?php
					$url = "edit";
					$urlDetail = "show";
					$strL = "Lead";

					if ($this->entity->status != Entity::STATUS_CACTIVE && $this->entity->status != Entity::STATUS_CASSIGNED && $this->entity->status != Entity::STATUS_CPRIORITY && $this->entity->status != Entity::STATUS_CONHOLD && $this->entity->status != Entity::STATUS_CDEAD ) {
						$url = "editcreatedquote";
						$urlDetail = "showcreated";
						$strL = "Quote";
					}

					if ($_GET['leads'] == "showimported" || $_GET['leads'] == "editimported") {
				?>
				<li class="nav-item">
					<a class="nav-link<?=(@$_GET['leads'] == 'showimported') ? " active" : ""?>"  href="<?=SITE_IN?>application/leads/showimported/id/<?=$this->entity->id?>'" role="tab"><?PHP print $strL;?> Detail</a>
				</li>
				<?php } else {?>
				<li class="nav-item">
					<a class="nav-link<?=(@$_GET['leads'] == 'show' || $_GET['leads'] == 'showcreated') ? " active" : ""?>"  href="<?=SITE_IN?>application/leads/<?PHP print $urlDetail;?>/id/<?=$this->entity->id?>" role="tab"><?PHP print $strL;?> Detail</a>
				</li>
				<?php }?>
				<?php 
					if ($_GET['leads'] == "showimported" || $_GET['leads'] == "editimported") {
				?>
				<li class="nav-item">
					<a class="nav-link<?=(@$_GET['leads'] == 'editimported') ? " active" : ""?>"  href="<?=SITE_IN?>application/leads/editimported/id/<?=$this->entity->id?>" role="tab">Edit <?PHP print $strL;?></a>
				</li>
				 <?php } else {?>
				 <?php if (($this->entity->status != Entity::STATUS_ARCHIVED) && !$this->entity->readonly): ?>

				<li class="nav-item">
					<a class="nav-link<?=(@$_GET['leads'] == 'edit' || $_GET['leads'] == 'editcreatedquote') ? " active" : ""?>"  href="<?=SITE_IN?>application/leads/<?PHP print $url;?>/id/<?=$this->entity->id?>" role="tab">Edit <?PHP print $strL;?></a>
				</li>
				<?php endif;?>
				<?php }?>
				<li class="nav-item">
					<a class="nav-link<?=(@$_GET['leads'] == 'createdhistory') ? " active" : ""?>"  href="<?=SITE_IN?>application/leads/createdhistory/id/<?=$this->entity->id?>" role="tab"><?PHP print $strL;?> History</a>
				</li>
				<?php if (!is_null($this->entity->email_id) && ctype_digit((string) $this->entity->email_id)): ?>
				<li class="nav-item">
					<a class="nav-link<?=(@$_GET['leads'] == 'email') ? " active" : ""?>"  href="<?=SITE_IN?>application/leads/email/id/<?=$this->entity->id?>" >Original E-mail</a>
			   	</li>
				<?php endif;?>
			</ul>
		</div>
	</div>
</div>

<div class="kt-portlet">
    <div class="kt-portlet__body">
        <div class="row">
			<div class="col-12 col-sm-3 text-left"></div>
			<div class="col-12 col-sm-9 new_btn_info_new_2 text-right">
				<?php if (($this->entity->status != Entity::STATUS_ARCHIVED) && !$this->entity->readonly): ?>
				<?php if ($this->entity->status == Entity::STATUS_CARCHIVED) {?>
				<?=functionButton('Hold', 'changeStatusLeadsData(' . Entity::STATUS_CONHOLD . ',' . $this->entity->id . ')', '', 'btn_bright_blue btn-sm')?>
				<?php if ($this->entity->status == Entity::STATUS_CDEAD) {?>
				<?=functionButton('Remove Do Not Call', 'changeStatusLeadsData(' . Entity::STATUS_CACTIVE . ',' . $this->entity->id . ')', '', 'btn-dark btn-sm ')?>
				<?=functionButton('Cancel', 'changeStatusLeadsData(' . Entity::STATUS_CARCHIVED . ',' . $this->entity->id . ')', 'btn-dark btn-sm')?>
				<?php } else {?>
				<?=functionButton('Uncancel', 'changeStatusLeadsData(' . Entity::STATUS_CACTIVE . ',' . $this->entity->id . ')', '', 'btn-danger')?>
				<?php }?>
				<?php
					} else  {
				?>
				<?php
					if ( $this->entity->status == Entity::STATUS_CQUOTED || $this->entity->status == Entity::STATUS_CFOLLOWUP || $this->entity->status == Entity::STATUS_CEXPIRED || $this->entity->status == Entity::STATUS_CAPPOINMENT || $this->entity->status == Entity::STATUS_LQUOTED ) {
				?>
				<?=functionButton('Print', 'printSelectedQuoteForm()')?>
				<?=$this->form_templates;?>
				<?=functionButton('Email', 'emailSelectedQuoteFormNew()')?>
					@email_templates@
				<?php }?>
				<?php print functionButton('Reassign Leads', 'reassign(' . $this->entity->id . ')', '', 'btn-sm btn_bright_blue ')?>
				<?php if ($this->entity->status == Entity::STATUS_CFOLLOWUP || $this->entity->status == Entity::STATUS_CQUOTED) {?>
				
				<?php }?>
				<?php 
					if ($this->entity->status != Entity::STATUS_CFOLLOWUP && $this->entity->status != Entity::STATUS_CQUOTED && $this->entity->status != Entity::STATUS_CEXPIRED && $this->entity->status != Entity::STATUS_CAPPOINMENT && $this->entity->status != Entity::STATUS_LQUOTED) {
				?>
				<?=functionButton('Convert to Quote', 'saveQuotes(\'0\',' . $this->entity->id . ')', '', 'btn-sm btn_bright_blue')?>
				<?php }?>
				<?=functionButton('Convert to Order', 'convertToOrder(' . $this->entity->id . ')', '', 'btn_bright_blue btn-sm')?>
				<?php if ($this->entity->status == Entity::STATUS_CPRIORITY) {?>
				<?=functionButton('Remove Priority', 'changeStatusLeadsData(' . Entity::STATUS_CASSIGNED . ',' . $this->entity->id . ')', '', 'btn-sm btn_bright_blue')?>
				<?php } else {?>
					<?=functionButton('Make Priority', 'changeStatusLeadsData(' . Entity::STATUS_CPRIORITY . ',' . $this->entity->id . ')', '', 'btn_bright_blue btn-sm')?>
				<?php }?>
				<?php if ($this->entity->status == Entity::STATUS_CONHOLD) {?>
				<?=functionButton('Remove Hold', 'changeStatusLeadsData(' . Entity::STATUS_CASSIGNED . ',' . $this->entity->id . ')', '', 'btn-sm btn_bright_blue')?>
				<?php } else {?>
				<?=functionButton('Hold', 'changeStatusLeadsData(' . Entity::STATUS_CONHOLD . ',' . $this->entity->id . ')', '', 'btn_bright_blue btn-sm')?>
				<?php }?>
				<?=functionButton('Do Not Call', 'changeStatusLeadsData(' . Entity::STATUS_CDEAD . ',' . $this->entity->id . ')', '', 'btn-sm btn_bright_blue')?>
				<?=functionButton('Cancel', 'changeStatusLeadsData(' . Entity::STATUS_CARCHIVED . ',' . $this->entity->id . ')', '', 'btn-dark btn-sm')?>
				<?php }?>
	    	</div>
		</div>
	</div>
</div>
<?php endif;?>

<script type="text/javascript">
    function emailSelectedQuoteForm() {
        form_id = $("#email_templates").val();
        if (form_id == "") {
            Swal.fire("Please choose email template");
        } else {
            if (confirm("Are you sure want to send Email?")) {
                $.ajax({
                    type: "POST",
                    url: BASE_PATH + "application/ajax/entities.php",
                    dataType: "json",
                    data: {
                        action: "emailQuote",
                        form_id: form_id,
                        entity_id: <?=$this->entity->id?>
                    },
                    success: function (res) {
                        if (res.success) {
                            Swal.fire("Email was successfully sent");
                        } else {
                            Swal.fire("Can't send email. Try again later, please");
                        }
                    },
                    complete: function (res) {
                    }
                });
            }
        }
    }

    function printSelectedQuoteForm() {
        form_id = $("#form_templates").val();
        if (form_id == "") {
            Swal.fire("Please choose form template");
        } else {

            $.ajax({
                url: BASE_PATH + 'application/ajax/entities.php',
                data: {
                    action: "print_quote",
                    form_id: form_id,
                    quote_id: '<?=$_GET["id"];?>',
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function () {
                },
                success: function (retData) {
                    printQuote(retData.printform);
                }
            });
        }
    }

    function createAndEmail() {
        $("#co_send_email").val("1");
        $("#submit_button").click();
    }

	function emailSelectedQuoteFormNew() {
        form_id = $("#email_templates").val();
        if (form_id == "") {
            Swal.fire("Please choose email template");
        } else {
			$.ajax({
				type: "POST",
				url: BASE_PATH + "application/ajax/entities.php",
				dataType: "json",
				data: {
					action: "emailQuoteNew",
					form_id: form_id,
					entity_id: <?=$this->entity->id?>
				},
				success: function (res) {
					if (res.success) {
						$("#email-template-results").empty();

						var html=`<div style="float: left;">
									<ul>
										<li style="margin-bottom: 14px;">Form Type <input value="1" id="attachPdf" name="attachTpe" type="radio"/>
											<label for="attachPdf" style="margin-right: 2px; cursor:pointer;"> PDF</label>
											<input value="0" id="attachHtml"  name="attachTpe" type="radio"/>
											<label for="attachHtml" style="cursor:pointer"> HTML</label>
										</li>
										<li style="margin-bottom: 11px;">Attachment(s): <span style="color:#24709F;" id="mail_att_new"></span></li>
									</ul>
								</div>
								<div style="">
									<div style="">
										<img src="/images/icons/add.gif" style="float:right;">
										<span style="margin-bottom: 3px;cursor:pointer; bottom:4px; color:#24709F; float:right;" class="add_one_more_field_" >Add a Field</span>
										<ul style="margin-top: 75px;">
											<li id="extraEmailsingle" style="margin-bottom: 6px;">
												<span>Email:<span style="color:red">*</span></span> 
												<input type="text" id="mail_to_new" name="mail_to_new" class="form-control" >
											</li>
											<li style="margin-bottom: 6px;margin-top: 6px;margin-left: 292px; position:relative; display: none;" id="mailexttra">
												<input name="optionemailextra" class="form-control optionemailextra" type="text">
												<a href="#" style="position: absolute;margin-left: 2px;margin-top: 8px;" class="remove_2sd_field">
													<img id="singletop" style="width: 12px;height: 12px;" src="/images/icons/delete.png">
												</a>
											</li>
											<li style="margin-bottom: 6px;">
												<span style="margin-right: 18px;">CC:</span> 
												<input type="text" id="mail_cc_new" name="mail_cc_new" class="form-control" >
											</li>
											<li style="margin-bottom: 12px;">
												<span style="margin-right: 9px;">BCC:</span> 
												<input type="text" id="mail_bcc_new" name="mail_bcc_new" class="form-control" >
											</li>
										</ul>
									</div>
									<div class="edit-mail-content" style="margin-bottom: 8px;">
										<div class="edit-mail-row" style="margin-bottom: 8px;">
											<div class="edit-mail-label">Subject:<span>*</span></div>
											<div class="">
												<input type="text" id="mail_subject_new" class="form-control" maxlength="255" name="mail_subject_new" style="width: 100%;">
											</div>
										</div>
										<div class="edit-mail-row">
											<div class="edit-mail-label">Body:<span>*</span></div>
											<br/>
											<div class="">
												<textarea class="form-control" name="mail_body_new" id="mail_body_new"></textarea>
											</div>
										</div>
									</div>
									<input type="hidden" name="form_id" id="form_id"  value=""/>
									<input type="hidden" name="entity_id" id="entity_id"  value=""/>
									<input type="hidden" name="skillCount" id="skillCount" value="1">
								</div>`;
						
						$("#email-template-results").append(html);

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
						$("#mail_att_new").html(res.emailContent.att);

						if(res.emailContent.atttype > 0){
							$("#attachPdf").attr('checked', 'checked');
						}else{
							$("#attachHtml").attr('checked', 'checked');
						}

						$("#mail_body_new").ckeditor();
						$("#maildivnew").modal("show");

					} else {
						alert("Can't send email. Try again later, please");
					}
				},
				complete: function (res) {
					/*$("body").nimbleLoader('hide');*/
				}
			});
        }
    }

	function maildivnew_submit() {
		Processing_show();
		$(".modal-body").addClass('kt-spinner kt-spinner--v2 kt-spinner--lg kt-spinner--dark');

		var sEmail =[$('#mail_to_new').val(),$('.optionemailextra').val(),$('#mail_cc_new').val(),$('#mail_bcc_new').val()];
		
		if (validateEmaildetail(sEmail)== false) {
			swal.fire('Invalid Email Address');
			return false;
		}

		if($('#attachPdf').is(':checked')){
			attach_type=$('#attachPdf').val();
		} else {
			attach_type=$('#attachHtml').val();
		}

		$.ajax({
			url: BASE_PATH + 'application/ajax/entities.php',
			data: {
				action: "emailOrderNewSend",
				form_id: $('#form_id').val(),
				entity_id: <?php echo $this->entity->id; ?>,
				mail_to: $('#mail_to_new').val(),
				mail_cc: $('#mail_cc_new').val(),
				mail_bcc: $('#mail_bcc_new').val(),
				mail_extra: $('.optionemailextra').val(),
				mail_subject: $('#mail_subject_new').val(),
				mail_body: $('#mail_body_new').val(),
				attach_type:attach_type
			},
			type: 'POST',
			dataType: 'json',
			beforeSend: function () {
				if ($('#mail_to_new').val() == ""|| $('#mail_subject_new').val() == ""||$('#mail_body_new').val() == "") {
					swal.fire('Empty Field(s)');
					return false;
				};
			},
			success: function (response) {
			$(".modal-body").removeClass('kt-spinner kt-spinner--v2 kt-spinner--lg kt-spinner--dark');
				if (response.success == true) {
					$("#maildivnew").modal('hide');
					clearMailForm();
				}
			},
			complete: function () {
				$(".modal-body").removeClass('kt-spinner kt-spinner--v2 kt-spinner--lg kt-spinner--dark');
				KTApp.unblockPage();
			}
		});
	}

	function validateEmaildetail(sEmail) {
		var res="",res1="",i;
		var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
		for (i = 0; i < sEmail.length; i++) {
			if (filter.test(sEmail[i])) {
				res += sEmail[i]; 
			} else {
				res1 += sEmail[i];
			}
		}
		if(res1!=='') {
			return false;
		}
	}
</script>
