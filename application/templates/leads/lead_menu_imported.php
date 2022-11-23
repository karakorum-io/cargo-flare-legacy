<script type="text/javascript" src="<?php echo SITE_IN; ?>/ckeditor/ckeditor.js"></script>
<style type="text/css">
	h2.kt-font-boldest {
		color: #646c9a;
	}
	.ui-dialog .ui-dialog-buttonpane button {
		float: right;
		margin: .5em .4em .5em 0;
		cursor: pointer;
		padding: 0.65rem 1rem;
		line-height: 1.4em;
		width: auto;
		overflow: visible;
		font-weight: 400;
		color: white;
		background: #5578eb;
	}
	.cancelButton {
		background: red;
	}
</style>

<script type="text/javascript">
	
	$('.add_one_more_field_').on('click',function(){ 
		$('#mailexttra').css('display','block');
		return false;
	});

	$('#singletop').on('click',function(){
		$('#mailexttra').css('display','none');
		$('.optionemailextra').val('');
	});

	function validateEmail(sEmail) {
		var res="",res1="",i;
		var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
		for (i = 0; i < sEmail.length; i++){
			if (filter.test(sEmail[i])){
				res += sEmail[i]; 
			}else {
				res1 += sEmail[i];
			}
		}   
		if(res1!==''){
			return false;
		}
	}

	var anim_busy = false;
	 
	function setStatus(entity_id, status) {
		Processing_show();
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
				KTApp.unblockPage();
				if (result.success == true) {
					window.location.reload();
				} else {
					Swal.fire('Lead action failed. Try again later, please.')
				}
			},
			error: function(result) {
				Swal.fire('Lead action failed. Try again later, please.')
			}
		})
	}

	function reassign(entity_id) {
		$("#reassig_send").val(entity_id);
		$("#reassign_dialog").modal();
	}

	function reassig_send(entity_id) {
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

	function Processing_show() {
        KTApp.blockPage({
			overlayColor: '#000000',
			type: 'v2',
			state: 'primary',
			message: '.'
        });
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
					window.location.href = '<?= SITE_IN ?>application/leads/editimported/id/'+entity_id;
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
	
	function convertToQuoteNew(entity_id) {
		var tariff = $('#lead_tariff_'+entity_id).val();
		var deposit = $('#lead_deposit_'+entity_id).val();
		var ajData = [];
	   
        if(tariff>0) {
			ajData.push('{"entity_id":"'+entity_id+'","tariff":"'+tariff+'","deposit":"'+deposit+'"}');
		}

		if (ajData.length == 0) {
            Swal.fire('You have no quote data')
			return;
		}
		
		$.ajax({
			type: "POST",
			url: "<?= SITE_IN ?>application/ajax/entities.php",
			dataType: "json",
			data: {
				action: 'saveQuotesNew',
				entity_id: entity_id,
				data: "["+ajData.join(",")+"]"
			},
			success: function(result) {
				if (result.success == true) {
					window.location.href = '<?= SITE_IN ?>application/leads/editimported/id/'+entity_id;
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
            url: "<?= SITE_IN ?>application/ajax/entities.php",
            dataType: "json",
            data: {
                action: "LeadtoOrderNew",
                entity_ids: entity_ids.join(',')
            },
            success: function (result) {
                if (result.success == true) {
                  window.location.href = '<?= SITE_IN ?>application/orders/edit/id/'+entity_id;
				  
                } else {
                    Swal.fire("Can't convert to Order. Try again later, please");
                }
            },
            error: function (result) {
                Swal.fire("Can't convert to Order. Try again later, please");
            }
        });
	}
   
	function printSelectedQuoteForm() {
		
        form_id = $("#form_templates").val();
        if (form_id == "") {
            swal.fire("Please choose form template");
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
						$("#maildivnew").empty();
						var html='<div style="float: left;"><ul style="margin-top: 26px;"><li style="margin-bottom: 14px;">Form Type <input value="1" id="attachPdf" name="attachTpe" type="radio"/><label for="attachPdf" style="margin-right: 2px; cursor:pointer;"> PDF</label><input value="0" id="attachHtml"  name="attachTpe" type="radio"/><label for="attachHtml" style="cursor:pointer"> HTML</label></li><li style="margin-bottom: 11px;">Attachment(s): <span style="color:#24709F;" id="mail_att_new"></span></li></ul></div><div style="text-align: right;"><div style="text-align: right;"><img src="/images/icons/add.gif"> <span style="margin-bottom: 3px;cursor:pointer; position: relative;bottom:4px; color:#24709F;" class="add_one_more_field_" >Add a Field</span><ul><li id="extraEmailsingle" style="margin-bottom: 6px;"><span>Email:<span style="color:red">*</span></span> <input type="text" id="mail_to_new" name="mail_to_new" class="form-box-combobox" ></li><li style="margin-bottom: 6px;margin-top: 6px;margin-left: 292px; position:relative; display: none;" id="mailexttra"><input name="optionemailextra" class="form-box-combobox optionemailextra" type="text"><a href="#" style="position: absolute;margin-left: 2px;margin-top: 8px;" class="remove_2sd_field"><img id="singletop" style="width: 12px;height: 12px;" src="/images/icons/delete.png"></a></li><li style="margin-bottom: 6px;"><span style="margin-right: 18px;">CC:</span> <input type="text" id="mail_cc_new" name="mail_cc_new" class="form-box-combobox" ></li><li style="margin-bottom: 12px;"><span style="margin-right: 9px;">BCC:</span> <input type="text" id="mail_bcc_new" name="mail_bcc_new" class="form-box-combobox" ></li></ul></div><div class="edit-mail-content" style="margin-bottom: 8px;"><div class="edit-mail-row" style="margin-bottom: 8px;"><div class="edit-mail-label">Subject:<span>*</span></div><div class="edit-mail-field" style="width: 87%;"><input type="text" id="mail_subject_new" class="form-box-textfield" maxlength="255" name="mail_subject_new" style="width: 100%;"></div></div><div class="edit-mail-row"><div class="edit-mail-label">Body:<span>*</span></div><div class="edit-mail-field" style="width: 87%;"><textarea class="form-box-textfield" style="width: 100%;" name="mail_body_new" id="mail_body_new"></textarea></div></div></div><input type="hidden" name="form_id" id="form_id"  value=""/><input type="hidden" name="entity_id" id="entity_id"  value=""/><input type="hidden" name="skillCount" id="skillCount" value="1"></div>';
						$("#maildivnew").append(html);
						
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
						ckRefresher('new');
						$("#mail_att_new").html(res.emailContent.att);
					  	
						if(res.emailContent.atttype > 0){
							$("#attachPdf").attr('checked', 'checked');
						} else {
							$("#attachHtml").attr('checked', 'checked');
						}
					} else {
						Swal.fire("Can't send email. Try again later, please");
					}
				},
				complete: function (res) {
				}
			});
        }
    }
</script>
	
<!--begin::Modal-->
<div class="modal fade" id="reassign_dialog" tabindex="-1" role="dialog" aria-labelledby="reassign_dialog_model" aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="reassign_dialog_model">Reassign Leade</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i class="fa fa-times" aria-hidden="true"></i>
				</button>
			</div>
			<div class="modal-body">
				<div class="error" style="display:none;"></div>
				<select class="form-box-combobox" id="company_members">
					<option value=""><?php print "Select One"; ?></option>
					<?php foreach($this->company_members as $member) : ?>
					<?php 
						if($member->status == "Active"){
							$activemember .="<option value= '".$member->id."'>" .$member->contactname ."</option>";
						}
					?>
					<?php endforeach; ?>
					<optgroup label="Active User">
					<?php echo $activemember; ?>
					</optgroup>
				</select>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-dark btn-sm" data-dismiss="modal">Cancal</button>
				<button type="button" class="btn btn_dark_green btn-sm" id="reassig_send" value="" onclick="reassig_send(this.value)">Assign</button>
			</div>
		</div>
	</div>
</div>
<!--end::Modal-->

<div class="alert alert-light alert-elevate">
	<div class="row">
		<div class="col-12">
			<ul class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-success mb-0" >
				<?php
					$strL = "Quote";
					if($this->entity->status == Entity::STATUS_ACTIVE ||  $this->entity->status == Entity::STATUS_LDUPLICATE || $this->entity->status == Entity::STATUS_UNREADABLE || $this->entity->status == Entity::STATUS_ONHOLD || $this->entity->status == Entity::STATUS_ARCHIVED) {
						$strL = "Lead";
					}

					if($_GET['leads'] =="showimported" || $_GET['leads'] =="editimported" || $_GET['leads'] =="email" || $_GET['leads'] =="history"){
				?>
					<li class="nav-item">
						<a class="nav-link <?= (@$_GET['leads'] == 'showimported' )?" active":"" ?>" href="<?= SITE_IN ?>application/leads/showimported/id/<?= $this->entity->id ?>" ><?PHP print $strL;?> Detail</a>
					</li>
				<?php } else {?>
					<li class="nav-item">
						<a class="nav-link <?= (@$_GET['leads'] == 'show' )?" active":"" ?>" href="<?= SITE_IN ?>application/leads/show/id/<?= $this->entity->id ?>" ><?PHP print $strL;?> Detail</a>
					</li>
				<?php }?>
				<?php 
					if($_GET['leads'] =="showimported" || $_GET['leads'] =="editimported" || $_GET['leads'] =="email" || $_GET['leads'] =="history"){
				?>
					<li class="nav-item">
						<a class="nav-link <?= (@$_GET['leads'] == 'editimported')?" active":"" ?>" href="<?= SITE_IN ?>application/leads/editimported/id/<?= $this->entity->id ?>" >Edit <?PHP print $strL;?></a>
					</li>
					<?php } else {?>
						<?php if (($this->entity->status != Entity::STATUS_ARCHIVED) && !$this->entity->readonly) : ?>
							<li class="nav-item">
								<a class="nav-link <?= (@$_GET['leads'] == 'edit')?" active":"" ?>" href="<?= SITE_IN ?>application/leads/edit/id/<?= $this->entity->id ?>" >Edit <?PHP print $strL;?></a>
							</li>
						<?php endif; ?>
					<?php }?>
					<li class="nav-item">
						<a class="nav-link<?= (@$_GET['leads'] == 'history')?" active":"" ?>" href="<?= SITE_IN ?>application/leads/history/id/<?= $this->entity->id ?>" > <?PHP print $strL;?> History</a>
					</li>
					<?php if (!is_null($this->entity->email_id) && ctype_digit((string)$this->entity->email_id)) : ?>
					<li class="nav-item ">
						<a class="nav-link<?= (@$_GET['leads'] == 'email')?" active":"" ?>" href="<?= SITE_IN ?>application/leads/email/id/<?= $this->entity->id ?>" >Original E-mail</a>
					</li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</div>

<div class="kt-portlet">
    <div class="kt-portlet__body">
        <div class="row">
			<div class="col-12 text-right">
				<?php if (($this->entity->status != Entity::STATUS_ARCHIVED) && !$this->entity->readonly) : ?>
				<?php 
					if( $this->entity->status == Entity::STATUS_LQUOTED || $this->entity->status == Entity::STATUS_LFOLLOWUP || $this->entity->status == Entity::STATUS_LAPPOINMENT || $this->entity->status == Entity::STATUS_LEXPIRED ){
				?>
				<?= functionButton('Print', 'printSelectedQuoteForm()') ?>
				<?=$this->form_templates; ?>
				@email_templates@
				<?php }?>
				<?= functionButton('AutoQuote', 'autoQuoteFromDetailLeads('.$this->entity->id.')','','btn-sm btn_bright_blue') ?>
				<?= functionButton('Reassign', 'reassign('.$this->entity->id.')','','btn_bright_blue btn-sm') ?>
				<?php if($this->entity->status != Entity::STATUS_ACTIVE){?>
				<?= functionButton('Convert to Order', 'convertToOrder('.$this->entity->id.')','','btn-sm btn_bright_blue') ?>
				<?php } ?>
				<?php 
					if($this->entity->status != Entity::STATUS_LQUOTED && $this->entity->status != Entity::STATUS_LFOLLOWUP && $this->entity->status != Entity::STATUS_LAPPOINMENT && $this->entity->status != Entity::STATUS_LEXPIRED){
				?>
				<?php print functionButton('Convert to Quote', 'convertToQuoteNew('.$this->entity->id.')','','btn-sm btn_bright_blue'); ?>
				<?php 
					}
				?>
				<?php 
					if ($this->entity->status == Entity::STATUS_LQUOTED || $this->entity->status == Entity::STATUS_ACTIVE || $this->entity->status == Entity::STATUS_LAPPOINMENT || $this->entity->status == Entity::STATUS_LDUPLICATE) : ?>
				<?= functionButton('On Hold', 'setStatus('.$this->entity->id.', '.Entity::STATUS_ONHOLD.')','','btn-sm btn_bright_blue') ?>
				<?php elseif ($this->entity->status == Entity::STATUS_ONHOLD) : ?>
				<?= functionButton('Remove Hold', 'setStatus('.$this->entity->id.', '.Entity::STATUS_ACTIVE.')','','btn-sm btn-dager') ?>
				<?php endif; ?>
				<?php if ($this->entity->status != Entity::STATUS_ARCHIVED) : ?>
				<?= functionButton('Cancel', 'setStatus('.$this->entity->id.', '.Entity::STATUS_ARCHIVED.')','','btn-sm btn-dark') ?>
				<?php endif; ?>
				<?php endif; ?>

				<?php if ($this->entity->status == Entity::STATUS_ARCHIVED) : ?>
				<?= functionButton('Uncancel', 'setStatus('.$this->entity->id.', '.Entity::STATUS_CACTIVE.')','','btn-danger') ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript" src="<?php echo SITE_IN ?>core/js/core.js" ></script>
