<div id="maildivnew">
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
      <div class="edit-mail-row" style="margin-bottom: 8px;">
         <div class="edit-mail-label">Subject:<span>*</span></div>
         <div class="edit-mail-field" style="width: 87%;"><input type="text" id="mail_subject_new" class="form-box-textfield" maxlength="255" name="mail_subject_new" style="width: 100%;"></div>
      </div>
      <div class="edit-mail-row">
         <div class="edit-mail-label">Body:<span>*</span></div>
         <div class="edit-mail-field" style="width: 87%;"><textarea class="form-box-textfield" style="width: 100%;" name="mail_body_new" id="mail_body_new"></textarea></div>
      </div>
   </div>
<input type="hidden" name="form_id" id="form_id"  value=""/>
                <input type="hidden" name="entity_id" id="entity_id"  value=""/>
                <input type="hidden" name="skillCount" id="skillCount" value="1">
   </div>    
</div>
<script type="text/javascript">
$('.add_one_more_field_').on('click',function(){ 
   $('#mailexttra').css('display','block');
   return false;
});   
$('#singletop').on('click',function(){
     $('#mailexttra').css('display','none');
     $('.optionemailextra').val('');
});
</script>
<script type="text/javascript"> 
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
</script>
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
	
	
	function convertToQuoteNew(entity_id) {
		
		var tariff = $('#lead_tariff_'+entity_id).val();
		var deposit = $('#lead_deposit_'+entity_id).val();
		
		var ajData = [];
        if(tariff>0)
		{
            
                ajData.push('{"entity_id":"'+entity_id+'","tariff":"'+tariff+'","deposit":"'+deposit+'"}');
            
		}
		if (ajData.length == 0) {
			alert("You have no quote data");
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
                action: "LeadtoOrderNew",
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
   
   $("#maildivnew").dialog({
	modal: true,
	width: 600,
	height: 400,
	title: "Email message",
	hide: 'fade',
	resizable: false,
	draggable: false,
	autoOpen: false,
	buttons: {
		"Submit": function () {
                    var sEmail =[$('#mail_to_new').val(),$('.optionemailextra').val(),$('#mail_cc_new').val(),$('#mail_bcc_new').val()];
                         if (validateEmail(sEmail)== false) {
                             alert('Invalid Email Address');
                             return false;
                        }
                    if($('#attachPdf').is(':checked')){
                         attach_type=$('#attachPdf').val();
                    }else{
                         attach_type=$('#attachHtml').val();
                    };
			$.ajax({
				url: BASE_PATH + 'application/ajax/entities.php',
				data: {
					action: "emailQuoteNewSend",
					form_id: $('#form_id').val(),
					entity_id: <?=$this->entity->id?>,
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
                                        alert ('Empty Field(s)');
                                    return false;
                                    };
				},
				success: function (response) {
					// $("body").nimbleLoader("hide");
					if (response.success == true) {
						$("#maildivnew").dialog("close");
						clearMailForm();
						
                     }
					
				},
				complete: function () {
					// $("body").nimbleLoader("hide");
				}
			});
		},
		"Cancel": function () {
			$(this).dialog("close");
		}
	}
});
	
	function printSelectedQuoteForm() {
        form_id = $("#form_templates").val();
        if (form_id == "") {
            alert("Please choose form template");
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
            alert("Please choose email template");
        } else {

              $("body").nimbleLoader('show');
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
                            
							
							 $("#form_id").val(form_id);
							 $("#mail_to_new").val(res.emailContent.to);
							 $("#mail_subject_new").val(res.emailContent.subject);
							 $("#mail_body_new").val(res.emailContent.body);
							 
							  //$("#mail_file_name").html(file_name);
							 $("#maildivnew").dialog("open");
							
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
<div id="reassign_dialog" style="display:none;">
	<div class="error" style="display:none;"></div>
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
                    <?php endforeach; ?>
                    
                    
						<optgroup label="Active User">
						<?php echo $activemember; ?>
						</optgroup>
                </select>	</select>
</div>
<div class="tab-panel-container">
	<ul class="tab-panel">
		 <?php 
		$strL = "Quote";
		if($this->entity->status == Entity::STATUS_ACTIVE || 
		   $this->entity->status == Entity::STATUS_LDUPLICATE ||
		   $this->entity->status == Entity::STATUS_UNREADABLE ||
		   $this->entity->status == Entity::STATUS_ONHOLD ||
		   $this->entity->status == Entity::STATUS_ARCHIVED
		   
		   )
		   $strL = "Lead";
		   
		   if($_GET['leads'] =="showimported" || $_GET['leads'] =="editimported" || $_GET['leads'] =="email" || $_GET['leads'] =="history"){?>
            <li class="tab first<?= (@$_GET['leads'] == 'showimported' )?" active":"" ?>"><span onclick="location.href = '<?= SITE_IN ?>application/leads/showimported/id/<?= $this->entity->id ?>'"><?PHP print $strL;?> Detail</span></li>
        <?php }else{?>
        <li class="tab first<?= (@$_GET['leads'] == 'show' )?" active":"" ?>"><span onclick="location.href = '<?= SITE_IN ?>application/leads/show/id/<?= $this->entity->id ?>'"><?PHP print $strL;?> Detail</span></li>
        <?php }?>
        <?php if($_GET['leads'] =="showimported" || $_GET['leads'] =="editimported" || $_GET['leads'] =="email" || $_GET['leads'] =="history"){
			
			?>
		    <li class="tab<?= (@$_GET['leads'] == 'editimported')?" active":"" ?>"><span onclick="location.href = '<?= SITE_IN ?>application/leads/editimported/id/<?= $this->entity->id ?>'">Edit <?PHP print $strL;?></span></li>
        
         <?php }else{?>
         <?php if (($this->entity->status != Entity::STATUS_ARCHIVED) && !$this->entity->readonly) : ?>
		<li class="tab<?= (@$_GET['leads'] == 'edit')?" active":"" ?>"><span onclick="location.href = '<?= SITE_IN ?>application/leads/edit/id/<?= $this->entity->id ?>'">Edit <?PHP print $strL;?></span></li>
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
		<table cellspacing="5" cellpadding="5" border="0" width="100%">
			<tr>
                <?php 
				  if(
					 $this->entity->status == Entity::STATUS_LQUOTED ||
					 $this->entity->status == Entity::STATUS_LFOLLOWUP ||
					 $this->entity->status == Entity::STATUS_LAPPOINMENT ||
					 $this->entity->status == Entity::STATUS_LEXPIRED 
					 ){
				?>
					<td><?= functionButton('Print', 'printSelectedQuoteForm()') ?></td>
					<td><?=$this->form_templates; ?></td>
					<td>
					<?= functionButton('Email', 'emailSelectedQuoteFormNew()') ?>
					<?php // functionButton('Email', 'emailSelectedQuoteForm()'); ?></td>
					<td>@email_templates@</td>
				<?php }?>
                
				<td><?= functionButton('Reassign', 'reassign('.$this->entity->id.')') ?></td>
               
			   <?php if($this->entity->status != Entity::STATUS_ACTIVE){?>
                 <td><?= functionButton('Convert to Order', 'convertToOrder('.$this->entity->id.')') ?></td>
               <?php }?>
               
               <?php if(
					 $this->entity->status != Entity::STATUS_LQUOTED &&
					 $this->entity->status != Entity::STATUS_LFOLLOWUP &&
					 $this->entity->status != Entity::STATUS_LAPPOINMENT &&
					 $this->entity->status != Entity::STATUS_LEXPIRED 
					 ){?>
                      <td><?php print  functionButton('Convert to Quote', 'convertToQuoteNew('.$this->entity->id.')'); ?></td>
               <?php }?>
               
				<?php if ($this->entity->status == Entity::STATUS_LQUOTED ||
						  $this->entity->status == Entity::STATUS_ACTIVE ||
						  $this->entity->status == Entity::STATUS_LAPPOINMENT ||
						  $this->entity->status == Entity::STATUS_LDUPLICATE 
						  
						  ) : ?>
				<td></td>
                
				<td><?= functionButton('On Hold', 'setStatus('.$this->entity->id.', '.Entity::STATUS_ONHOLD.')') ?></td>
				<?php elseif ($this->entity->status == Entity::STATUS_ONHOLD) : ?>
				<td><?= functionButton('Remove Hold', 'setStatus('.$this->entity->id.', '.Entity::STATUS_ACTIVE.')') ?></td>
				<?php endif; ?>
				<?php if ($this->entity->status != Entity::STATUS_ARCHIVED) : ?>
				<td><?= functionButton('Cancel', 'setStatus('.$this->entity->id.', '.Entity::STATUS_ARCHIVED.')') ?></td>
				<?php endif; ?>
			</tr>
		</table>
	</div>
</div>
<?php endif; ?>