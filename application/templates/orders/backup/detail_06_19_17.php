<!---
/***************************************************************************************************
* Transportation Management Software
*
* Client:			FreightDragon
* Version:			1.0
* Start Date:			2011-10-05
* Author:			Freight Genie LLC
* E-mail:			admin@freightdragon.com
*
* CopyRight 2011 FreightDragon. - All Rights Reserved
****************************************************************************************************/
--->
<?php
$mobileDevice = 0;
$mobileDevice = detectMobileDevice();
?>
<script type="text/javascript" src="/ckeditor/ckeditor.js"></script>
<div id="maildiv">
    <table cellspacing="2" cellpadding="0" border="0">
        <tr>
            <td>@mail_to@</td>
        </tr>
        <tr>
            <td>@mail_subject@</td>
        </tr>
        <tr>
            <td>@mail_body@</td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td>Attachment:&nbsp;</td>
            <td id="mail_file_name" style="font-weight: bold; color: #0052a4; ">&nbsp;
                
            </td>
        </tr>
    </table>
</div>

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
function validateEmaildetail(sEmail) {
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

<script type="text/javascript">//<![CDATA[
    $("#maildiv").dialog({
        modal: true,
        width: 400,
        height: 310,
        title: "Email message with that document attached.",
        hide: 'fade',
        resizable: false,
        draggable: false,
        autoOpen: false,
        buttons: {
            "Submit": function () {
                $.ajax({
                    url: BASE_PATH + 'application/ajax/send_document.php',
                    data: {
                        action: "entity",
                        file_id: mail_file_id,
                        mail_to: $('#mail_to').val(),
                        mail_subject: $('#mail_subject').val(),
                        mail_body: $('#mail_body').val()
                    },
                    type: 'POST',
                    dataType: 'json',
                    beforeSend: function () {
                        if (!validateMailForm()) {
                            return false;
                        } else {
                            // // $("body").nimbleLoader("show");
                        }
                    },
                    success: function (response) {
                        // $("body").nimbleLoader("hide");
                        if (response.success == true) {
                            $("#maildiv").dialog("close");
                            clearMailForm();
                        }
                        alert(response.message);
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
	
	
	$("#maildivnew").dialog({
	modal: true,
	width: 566,
	height: 360,
	title: "Email message",
	hide: 'fade',
	resizable: false,
	draggable: false,
	autoOpen: false,
	buttons: {
		"Submit": function () {
                    var sEmail =[$('#mail_to_new').val(),$('.optionemailextra').val(),$('#mail_cc_new').val(),$('#mail_bcc_new').val()];
                         if (validateEmaildetail(sEmail)== false) {
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
					action: "emailOrderNewSend",
					form_id: $('#form_id').val(),
					entity_id: <?=$this->entity->id;?>,
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

$(document).ready(function(){
    $("#mail_to").change(function(){
    var new_trimemail = $.trim($("#mail_to").val());
    $('#mail_to').val(new_trimemail)
    });
});
    </script>



<?php include_once("create_dispatch.php") ?>
<script type="text/javascript">
	var busy = false;
function updateInternalNotes(data) {
		var rows = "";
		
		for (i in data) {
			
			var email = data[i].email;
			var contactname = data[i].sender;
			
			if(data[i].system_admin == 2){
			     email = "admin@freightdragon.com";
				 contactname = "FreightDragon";
			   }
			if ((data[i].access_notes == 0 )   
				    || data[i].access_notes == 1
					|| data[i].access_notes == 2
					)
			{
				
              var discardStr = '';
				if(data[i].discard==1)
				   discardStr = ' style="text-decoration: line-through;" ';
				 
			if(data[i].system_admin == 1 || data[i].system_admin == 2)
			{
				rows += '<tr class="grid-body"><td style="white-space:nowrap;" class="grid-body-left" >'+data[i].created+'</td><td id="note_'+data[i].id+'_text"  '+discardStr+'><b>'+decodeURIComponent(data[i].text)+'</b></td><td>';	 
			}
			else if(data[i].priority==2)
			   rows += '<tr class="grid-body"><td class="grid-body-left" >'+data[i].created+'</td><td id="note_'+data[i].id+'_text"  '+discardStr+'><b style="font-size:12px;color:red;">'+decodeURIComponent(data[i].text)+'</b></td><td>';
			 else
			   rows += '<tr class="grid-body"><td class="grid-body-left">'+data[i].created+'</td><td id="note_'+data[i].id+'_text"  '+discardStr+'>'+decodeURIComponent(data[i].text)+'</td><td>';
			
		
			
			rows += '<a href="mailto:'+email+'">'+contactname+'</a></td><td style="white-space: nowrap;" class="grid-body-right"  >';
			
			<?php //if (!$this->entity->readonly) : ?>
			
				if ((data[i].access_notes == 0 ) ||
					  (data[i].access_notes == 1 && (data[i].sender_id == data[i].memberId))
					  || data[i].access_notes == 2
					)
					{
						
					  if(data[i].sender_id == data[i].memberId  && data[i].system_admin == 0 )
					    rows += '<img src="<?= SITE_IN ?>images/icons/strike.png" alt="Discard" title="Discard" width="16" height="16" class="action-icon edit-note" onclick="discardNote('+data[i].id+')"/>';
						
					 if(data[i].system_admin == 0 && data[i].access_notes != 0)
					 {
				
              // rows += '<img src="<?= SITE_IN ?>images/icons/strike.png" alt="Discard" title="Discard" width="16" height="16" class="action-icon edit-note" onclick="discardNote('+data[i].id+')"/>';
			   rows += '<img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit" width="16" height="16" class="action-icon edit-note" onclick="editInternalNote('+data[i].id+')"/>';
			   
			   rows += '<img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete" width="16" height="16" class="action-icon delete-note" onclick="delInternalNote('+data[i].id+')"/>';
					 }
				  }
			}
			<?php /*else : ?>rows += '&nbsp;';<?php endif;*/?>
			rows += '</td></tr>';
		  }
		
		$("#internal_notes_table tbody").html(rows);
	}


function addQuickNote() {
	var textOld = $("#internal_note").val();
	
	var str = textOld + " " + $("#quick_notes").val();
	$("#internal_note").val(str);
} 
	function addInternalNote() {
		if (busy) return;
		busy = true;
		var text = $.trim($("#internal_note").val());
		var priority = $.trim($("#priority_notes").val());
		if (text == "") return;
		$("#internal_note").val("");
		$.ajax({
			type: "POST",
			url: "<?= SITE_IN ?>application/ajax/notes.php",
			dataType: "json",
			data: {
				action: 'add',
				text: encodeURIComponent(text),
				entity_id: <?= $this->entity->id ?>,
				notes_type: <?= Note::TYPE_INTERNAL ?>,
				priority: priority
			},
			success: function(result) {
				if (result.success == true) {
					updateInternalNotes(result.data);
				} else {
					$("#internal_note").val(text);
					alert("Can't save note. Try again later, please");
				}
				busy = false;
			},
			error: function(result) {
				$("#internal_note").val(text);
				alert("Can't save note. Try again later, please");
				busy = false;
			}
		});
	}
	
	function delInternalNote(id) {
		if (confirm("Are you sure whant to delete this note?")) {
			if (busy) return;
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
				success: function(result) {
					if (result.success == true) {
						updateInternalNotes(result.data);
					} else {
						alert("Can't delete note. Try again later, please");
					}
					busy = false;
				},
				error: function(result) {
					alert("Can't delete note. Try again later, please");
					busy = false;
				}
			});
		}
	}
	function editInternalNote(id) {
		var text = $.trim($("#note_"+id+"_text").text());
		$("#note_edit_form textarea").val(text);
		$("#note_edit_form").dialog({
			width: 400,
			modal: true,
			title: "Edit Internal Note",
			resizable: false,
			buttons: [{
				text: "Save",
				click: function() {
					if ($("#note_edit_form textarea").val() == text) {
						$(this).dialog("close");
					} else {
						if (busy) return;
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
							success: function(result) {
								if (result.success == true) {
									updateInternalNotes(result.data);
									$("#note_edit_form").dialog("close");
								} else {
									alert("Can't save note. Try again later, please");
								}
								busy = false;
							},
							error: function(result) {
								alert("Can't save note. Try again later, please");
								busy = false;
							}
						});
					}
				}
			},{
				text: "Cancel",
				click: function() {
					$(this).dialog("close");
					busy = false;
				}
			}]
		}).dialog("open");
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
		success: function(result) {
			if (result.success == true) {
				updateInternalNotes(result.data);
			} else {
				alert("Can't discard note. Try again later, please");
			}
			busy = false;
		},
		error: function(result) {
			alert("Can't discard note. Try again later, please");
			busy = false;
		}
	});
}
	
	function cancelDispatchSheet(dispatch_id) {

	$.ajax({

		type: "POST",

		url: BASE_PATH+"application/ajax/dispatch.php",

		dataType: 'json',

		data: {

			action: 'cancel',

			id: dispatch_id

		},

		success: function(res) {

			if (res.success) {

				document.location.reload();

			} else {

				alert("Can't cancel Dispatch Sheet");

			}

		}

	});
}

function editCarrier(dispatch_id)
{
	$("#carrier_value").hide();
	$("#carrier_edit").show();
}


function cancelCarrier(dispatch_id)
{
	$("#carrier_value").show();
	$("#carrier_edit").hide();
}
function updateCarrier(dispatch_id)
{
	 $.ajax({
		type: "POST",
		url: BASE_PATH+"application/ajax/dispatch.php",
		dataType: 'json',
		data: {
			        
			action: 'editcarrier',
			carrier_company_name: $("#carrier_company_name").val(),
			carrier_contact_name: $("#carrier_contact_name").val(),
			carrier_phone_1: $("#carrier_phone_1").val(),
			carrier_phone_2: $("#carrier_phone_2").val(),
			carrier_fax: $("#carrier1_fax").val(),
			carrier_email: $("#carrier1_email").val(),
			hours_of_operation: $("#hours_of_operation").val(),
			carrier_driver_name: $("#carrier_driver_name").val(),
			carrier_driver_phone: $("#carrier1_driver_phone").val(),
			id: dispatch_id
		},
		success: function(res) {
			if (res.success) {
				document.location.reload();
			} else {
				alert("Can't cancel Dispatch Sheet");
			}
		}
     });
}
</script>
<div id="note_edit_form" style="display:none;">
	<textarea style="width: 95%;height:100px;" class="form-box-textarea" name="note_text"></textarea>
</div>
<?php 

 if(is_array($_SESSION['searchData']) && $_SESSION['searchCount']>0){
	 //$_SESSION['searchShowCount'] = $_SESSION['searchShowCount'] + 1;
	 
	 $eid = $_GET['id'];
	 $indexSearchData = array_search($eid,$_SESSION['searchData']);
   
	   $nextSearch = $indexSearchData+1;
	   $_SESSION['searchShowCount'] = $indexSearchData;
	   $prevSearch = $indexSearchData-1;
	   
	 $entityPrev = $_SESSION['searchData'][$prevSearch];
	 $entityNext = $_SESSION['searchData'][$nextSearch];
	 //print_r($_SESSION['searchData']);
?>
<div style="float:right; width:170px;">
  <div style="float:left;width:50px;">
  <?php if($_SESSION['searchShowCount']==0 ){?>
       <img src="<?= SITE_IN ?>images/arrow-down-gray.png"   width="40" height="40"/>
     <?php }else{?>
       <a href="<?= SITE_IN ?>application/orders/show/id/<?= $entityPrev ?>"><img src="<?= SITE_IN ?>images/arrow-down.png"   width="40" height="40"/></a>
       
     <?php }?>
  </div>
  <div style="float:left;width:70px; text-align:center; padding-top:10px;">
    <h3><?php print $_SESSION['searchShowCount']+1;?> - <?php print $_SESSION['searchCount'];?></h3>
  </div>
  <div style="float:left;width:50px;">
  <?php if($_SESSION['searchShowCount'] == ($_SESSION['searchCount']-1)){?>
         <img src="<?= SITE_IN ?>images/arrow-up-gray.png"    width="40" height="40"/>
  <?php }else{?>
       <a href="<?= SITE_IN ?>application/orders/show/id/<?= $entityNext ?>"><img src="<?= SITE_IN ?>images/arrow-up.png"   width="40" height="40"/></a>
     <?php }?>
     
  </div>
</div>
<?php }  ?>
<div style="padding-top:15px;">
<?php include('order_menu.php');  ?>
</div>
<br>
<h3>Order #<?= $this->entity->getNumber() ?><h1>Current Status:&nbsp;<?php  

	
			if($this->entity->status == Entity::STATUS_ARCHIVED){
                                     print "Cancelled";

						     }elseif($this->entity->status == Entity::STATUS_PICKEDUP){  
							        print "Picked Up";

							 }elseif($this->entity->status == Entity::STATUS_DISPATCHED){								 
                                     print "Dispatched";
									 
							 }elseif($this->entity->status == Entity::STATUS_DELIVERED){
                                     print "Delivered";
									 
							} elseif($this->entity->status == Entity::STATUS_POSTED){
							         print "Posted";

							 }elseif($this->entity->status == Entity::STATUS_NOTSIGNED){
							         print "Not Signed";
									 
							 }elseif($this->entity->status == Entity::STATUS_ISSUES){
							        print "Pending Payments";							    

							 }elseif($this->entity->status == Entity::STATUS_ONHOLD){								 
                                     print "OnHold";
									 
							 }else{
                                     print "My Order";		
 							  }	

			      ?></font></h1>
<table>
	<tr>
	<td valign="top" width="80%">
<div class="order-info" style="width:97%; margin-bottom: 10px;">
<?php
    
	$assigned = $this->entity->getAssigned();
	
	$shipper = $this->entity->getShipper();
	$origin = $this->entity->getOrigin();
	$destination = $this->entity->getDestination();
	$vehicles = $this->entity->getVehicles();
	
?>
<?php
			             $Balance_Paid_By = "";
						   if(in_array($this->entity->balance_paid_by, array(2, 3 , 16 , 17)))   
							   $Balance_Paid_By = "<b>COD</b>";
							
							if(in_array($this->entity->balance_paid_by, array(8, 9 , 18 , 19)))   
							   $Balance_Paid_By = "COP";
							
							if(in_array($this->entity->balance_paid_by, array(12, 13 , 20 , 21,24)))   
							   $Balance_Paid_By = "Billing";
							 
							 if(in_array($this->entity->balance_paid_by, array(14, 15 , 22 , 23)))   
							   $Balance_Paid_By = "Billing";
							   
			if(trim($shipper->phone1)!="")
			{
				/*
				$arrArea = array();
				$arrArea = explode(")",formatPhone($shipper->phone1));
				   
				$code     = str_replace("(","",$arrArea[0]);
					*/
				$code     = substr($shipper->phone1, 0, 3);
				$areaCodeStr1="";  
				
				$areaCodeRows = $this->daffny->DB->selectRow("*", "app_area_code", "WHERE  AreaCode='".$code."'");
				if (!empty($areaCodeRows)) {
					 $areaCodeStr1 = "<b>".$areaCodeRows['StdTimeZone']."-".$areaCodeRows['statecode']."</b>"; 
				}
			}	
			
			if(trim($shipper->phone2)!="")
			{
				/*
				$arrArea1 = array();
				$arrArea1 = explode(")",formatPhone($shipper->phone2));
				   
				$code     = str_replace("(","",$arrArea1[0]);
				*/
				$code     = substr($shipper->phone2, 0, 3);
				$areaCodeStr2="";  
				//print "WHERE  AreaCode='".$code."'";
				$areaCodeRows2 = $this->daffny->DB->selectRow("*", "app_area_code", "WHERE  AreaCode='".$code."'");
				if (!empty($areaCodeRows2)) {
					 $areaCodeStr2 = "<b>".$areaCodeRows2['StdTimeZone']."-".$areaCodeRows2['statecode']."</b>"; 
				}
			}	
			?>
  <?php
  /*
$words = array("+", "-", " ","(",")");
$wordsReplace   = array("", "", "", "", "");
$phone1 = "1".str_replace($words, $wordsReplace, $shipper->phone1);
$phone2 = "1".str_replace($words, $wordsReplace, $shipper->phone2);
*/
$phone1 = "1". $shipper->phone1;
$phone2 = "1".$shipper->phone2;

$phone1_ext ='';
$phone2_ext ='';
if($shipper->phone1_ext!='')
 $phone1_ext = " <b>X</b> ".$shipper->phone1_ext;
if($shipper->phone2_ext!='')
 $phone2_ext = " <b>X</b> ".$shipper->phone2_ext;


	
if($this->entity->source_id >0){
	   try{
		$source = new Leadsource($this->daffny->DB);
		$source->load($this->entity->source_id);
		$sourceName = $source->company_name;
	   }catch(FDException $e) 
	   {$sourceName = $this->entity->referred_by;}
	   
	}
	elseif($this->entity->referred_id >0){
		$sourceName = $this->entity->referred_by;
	}
	
?>
	<p class="block-title">Order Information</p>
	<div>
		
		<table cellspacing="0" cellpadding="0" border="0" width="100%">
			<tr>
				<td style="vertical-align:top;" valign="top" width="50%">
                   <table width="100%" cellpadding="1" cellpadding="1">
                   <tr><td width="20%"><strong><u>Shipper Details</u></strong></td></tr>
				   <tr><td style="line-height:15px;"><strong>Name</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $shipper->fname ?> <?= $shipper->lname ?></td></tr>
                   <tr> <td style="line-height:15px;"><strong>Company</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td  style="line-height:15px;"><strong><?= $shipper->company; ?></strong></td></tr>
                   <tr> <td style="line-height:15px;"><strong>Email</strong></td><td width="4%" align="center"><b>:</b></td><td><a href="mailto:<?= $shipper->email ?>"><?= $shipper->email ?></a></td></tr>
                   <tr> <td style="line-height:15px;"><strong>Phone 1</strong></td><td width="4%" align="center"><b>:</b></td><td>
                   <?php if($mobileDevice==1){?>
                        <a href="tel:<?php print $phone1;?>" ><?= formatPhone($shipper->phone1) ?></a>
                  <?php }else{?>
                         <a href="javascript:void(0);" onclick="customPhoneSms('<?= $phone1; ?>');"><?= formatPhone($shipper->phone1); ?></a>
				<?php }?>
                      <?= $phone1_ext;?> <?= $areaCodeStr1; ?>
                   </td></tr>
				   <tr> <td style="line-height:15px;"><strong>Phone 2</strong></td><td width="4%" align="center"><b>:</b></td><td>
                   <?php if($mobileDevice==1){?>
                        <a href="tel:<?php print $phone2;?>" ><?= formatPhone($shipper->phone2) ?></a>
                  <?php }else{?>
                         <a href="javascript:void(0);" onclick="customPhoneSms('<?= $phone2; ?>');"><?= formatPhone($shipper->phone2); ?></a>
                   <?php }?>
                   <?= $phone2_ext;?> <?= $areaCodeStr2; ?>
                   
                   </td></tr>
                   <tr> <td style="line-height:15px;"><strong>Mobile</strong></td><td width="4%" align="center"><b>:</b></td><td><?= formatPhone($shipper->mobile); ?></td></tr>
                   <tr> <td style="line-height:15px;"><strong>Fax</strong></td><td width="4%" align="center"><b>:</b></td><td><?= formatPhone($shipper->fax); ?></td></tr> 
                   </table>   
				</td>
				<td style="vertical-align:top;"> 
                   <table width="100%" cellpadding="1" cellpadding="1">
				   <tr> <td width="23%"><strong><u>Order Details</u></strong></td></tr>
				   <tr> <td width="23%"><strong>1st Avail. Pickup</strong></td><td width="4%" align="center"><b>:</b></td><td><?= $this->entity->getFirstAvail("m/d/y") ?></td></tr>
				   <tr> <td style="line-height:15px;"><strong>Payment Method</strong></td><td width="4%" align="center"><b>:</b></td><td><font color="red"><?= $this->entity->getPaymentOption($this->entity->customer_balance_paid_by); ?></font></td></tr>
                   <tr> <td style="line-height:15px;"><strong>Carrier Paid By</strong></td><td width="4%" align="center"><b>:</b></td><td><font color="red"><?= $Balance_Paid_By; ?></font></td></tr>	
                   <tr><td width="15%"><strong>Ship Via: </strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><span style="color:red;weight:bold;"><?= $this->entity->getShipVia() ?></span></td></tr>
                    <?php if (is_numeric($this->entity->distance) && ($this->entity->distance > 0)) : ?>
                    <tr><td width="15%"  style="line-height:15px;"><strong>Mileage: </strong></td><td width="4%" align="center"><b>:</b></td><td><?= number_format($this->entity->distance, 0, "", "") ?> mi($ <?= number_format(($this->entity->getCarrierPay(false) / $this->entity->distance), 2, ".", ",") ?>/mi)&nbsp;&nbsp;(<span class='red' onclick="mapIt(<?= $this->entity->id ?>);">MAP IT</span>)</strong></td></tr>
					
                    <?php endif; ?>
                    <tr><td style="line-height:15px;"><strong>Assigned to</strong></td><td width="4%" align="center"><b>:</b></td><td><?= $assigned->contactname ?></td></tr>
                    <?php if($this->entity->referred_by != ""){?>
                             <tr><td style="line-height:15px;"><strong>Source</strong></td><td width="4%" align="center"><b>:</b></td><td><?= $sourceName ?></td></tr>
				    <?php  } else { 
				        if($assigned->hide_lead_sources==0)
						{ 
				        ?>
                                 <tr><td style="line-height:15px;"><strong>Source</strong></td><td width="4%" align="center"><b>:</b></td><td><?= $sourceName ?></td></tr>      
                <?php  }
				    } 
				?>			
                   
                   </table> 					
				</td>
			</tr>
		</table>
	</div>
</div>
<div style="clear:left;"></div>
<table width="100%" cellpadding="1" cellpadding="1" border="0">
<tr>
<td width="49%" valign="top" >
<?php
$phone1_ext ='';
$phone2_ext ='';
$phone3_ext ='';
$phone4_ext ='';
if($origin->phone1_ext!='')
 $phone1_ext = " <b>X</b> ".$origin->phone1_ext;
if($origin->phone2_ext!='')
 $phone2_ext = " <b>X</b> ".$origin->phone2_ext;
if($origin->phone3_ext!='')
 $phone3_ext = " <b>X</b> ".$origin->phone3_ext;
if($origin->phone4_ext!='')
 $phone4_ext = " <b>X</b> ".$origin->phone4_ext;
?>
<div class="order-info"  style="width:95%; margin-bottom: 10px;">
	<p class="block-title">Pickup Information</p>
	<div>
		 <table width="100%" cellpadding="1" cellpadding="1" border="0"  >
		   <tr> <td width="23%"><strong>Address</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $origin->address1; ?>,&nbsp;&nbsp;<?= $origin->address2; ?></td></tr>
		   <tr> <td style="line-height:15px;"><strong>City</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><span class="like-link"onclick="window.open('<?= $origin->getLink() ?>')"><?= $origin->getFormatted() ?></span></td></tr>
		   <tr> <td style="line-height:15px;"><strong>Location Type</strong></td><td width="4%" align="center"><b>:</b></td><td><?= $origin->location_type; ?></td></tr>
		   <tr> <td style="line-height:15px;"><strong>Hours</strong></td><td width="4%" align="center"><b>:</b></td><td><?= $origin->hours; ?></td>
		   <tr><td>&nbsp;</td></tr>
		   <tr> <td style="line-height:15px;"><strong>Contact Name 1</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $origin->name; ?>&nbsp;&nbsp;<? if ($origin->phone1 <> '') {?><strong>P:</strong>&nbsp;<?= formatPhone($origin->phone1); ?><?= $phone1_ext?> <? } ?>&nbsp;&nbsp;<? if ($origin->phone_cell <> '') {?><strong>M:</strong>&nbsp;<?= formatPhone($origin->phone_cell); ?> <? } ?></td></tr>
		   <tr><td style="line-height:15px;"><strong>Contact Name 2</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $origin->name2; ?>&nbsp;&nbsp;<? if ($origin->phone2 <> '') {?><strong>P:</strong>&nbsp;<?= formatPhone($origin->phone2); ?><?= $phone2_ext?> <? } ?>&nbsp;&nbsp;<? if ($origin->phone_cell2 <> '') {?><strong>M:</strong>&nbsp;<?= formatPhone($origin->phone_cell2); ?> <? } ?></td></tr>
		   <tr> <td style="line-height:15px;"><strong>Company Name</strong></td><td width="4%" align="center"><b>:</b></td><td><?= $origin->company; ?></td></tr>
		   <tr> <td style="line-height:15px;"><strong>Company Phone</strong></td><td width="4%" align="center"><b>:</b></td><td><?= formatPhone($origin->phone3); ?><?= $phone3_ext?></td></tr>
		   <tr> <td style="line-height:15px;"><strong>Company Fax</strong></td><td width="4%" align="center"><b>:</b></td><td><?= formatPhone($origin->fax); ?></td></tr>
		   <tr> <td style="line-height:15px;"><strong>Auction Name</strong></td><td width="4%" align="center"><b>:</b></td><td><?= $origin->auction_name; ?></td></tr>
		   <tr> <td style="line-height:15px;"><strong>Auction Phone</strong></td><td width="4%" align="center"><b>:</b></td><td><?= formatPhone($origin->phone4); ?><?= $phone4_ext?></td></tr>
		   <tr> <td style="line-height:15px;"><strong>Auction Fax</strong></td><td width="4%" align="center"><b>:</b></td><td><?= formatPhone($origin->fax2); ?></td></tr>
		   <tr> <td style="line-height:15px;"><strong>Buyer Number</strong></td><td width="4%" align="center"><b>:</b></td><td><?= $origin->buyer_number; ?></td></tr>
		   <tr> <td style="line-height:15px;"><strong>Booking Number</strong></td><td width="4%" align="center"><b>:</b></td><td><?= $origin->booking_number; ?></td>				   
         </table>  
	</div>
</div> 
</td> 
<td width="1%" valign="top" >&nbsp;  </td>
<td width="49%" valign="top" >
<?php
$phone1_ext ='';
$phone2_ext ='';
$phone3_ext ='';
$phone4_ext ='';
if($destination->phone1_ext!='')
 $phone1_ext = " <b>X</b> ".$destination->phone1_ext;
if($destination->phone2_ext!='')
 $phone2_ext = " <b>X</b> ".$destination->phone2_ext;
if($destination->phone3_ext!='')
 $phone3_ext = " <b>X</b> ".$destination->phone3_ext;
if($destination->phone4_ext!='')
 $phone4_ext = " <b>X</b> ".$destination->phone4_ext;
?>
<div class="order-info"  style="width:95%; margin-bottom: 10px;">
	<p class="block-title">Dropoff Information</p>
	<div>
		 <table width="100%" cellpadding="1" cellpadding="1" border="0"  > 
                        <tr> <td width="23%"><strong>Address</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $destination->address1; ?>,&nbsp;&nbsp;<?= $destination->address2; ?></td></tr>
		   <tr> <td style="line-height:15px;"><strong>City</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><span class="like-link"onclick="window.open('<?= $destination->getLink() ?>')"><?= $destination->getFormatted() ?></span></td></tr>
		   <tr> <td style="line-height:15px;"><strong>Location Type</strong></td><td width="4%" align="center"><b>:</b></td><td><?= $destination->location_type; ?></td></tr>
		   <tr> <td style="line-height:15px;"><strong>Hours</strong></td><td width="4%" align="center"><b>:</b></td><td><?= $destination->hours; ?></td>
		   <tr><td>&nbsp;</td></tr>
		   <tr> <td style="line-height:15px;"><strong>Contact Name 1</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $destination->name; ?>&nbsp;&nbsp;<? if ($destination->phone1 <> '') {?><strong>P:</strong>&nbsp;<?= formatPhone($destination->phone1); ?><?= $phone1_ext?> <? } ?>&nbsp;&nbsp;<? if ($destination->phone_cell <> '') {?><strong>M:</strong>&nbsp;<?= formatPhone($destination->phone_cell); ?> <? } ?></td></tr>
		   <tr><td style="line-height:15px;"><strong>Contact Name 2</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $destination->name2; ?>&nbsp;&nbsp;<? if ($destination->phone2 <> '') {?><strong>P:</strong>&nbsp;<?= formatPhone($destination->phone2); ?><?= $phone2_ext?> <? } ?>&nbsp;&nbsp;<? if ($destination->phone_cell2 <> '') {?><strong>M:</strong>&nbsp;<?= formatPhone($destination->phone_cell2); ?> <? } ?></td></tr> 
		   <tr> <td style="line-height:15px;"><strong>Company Name</strong></td><td width="4%" align="center"><b>:</b></td><td><?= $destination->company; ?></td></tr>
		   <tr> <td style="line-height:15px;"><strong>Company Phone</strong></td><td width="4%" align="center"><b>:</b></td><td><?= formatPhone($destination->phone3); ?><?= $phone3_ext?></td></tr>
		   <tr> <td style="line-height:15px;"><strong>Company Fax</strong></td><td width="4%" align="center"><b>:</b></td><td><?= formatPhone($destination->fax); ?></td></tr>
		   <tr> <td style="line-height:15px;"><strong>Auction Name</strong></td><td width="4%" align="center"><b>:</b></td><td><?= $destination->auction_name; ?></td></tr>
		   <tr> <td style="line-height:15px;"><strong>Auction Phone</strong></td><td width="4%" align="center"><b>:</b></td><td><?= formatPhone($destination->phone4); ?><?= $phone4_ext?></td></tr>
		   <tr> <td style="line-height:15px;"><strong>Auction Fax</strong></td><td width="4%" align="center"><b>:</b></td><td><?= formatPhone($destination->fax2); ?></td></tr>
		   <tr> <td style="line-height:15px;"><strong>Buyer Number</strong></td><td width="4%" align="center"><b>:</b></td><td><?= $destination->buyer_number; ?></td></tr>
		   <tr> <td style="line-height:15px;"><strong>Booking Number</strong></td><td width="4%" align="center"><b>:</b></td><td><?= $destination->booking_number; ?></td>
		</table>
	</div>
</div> 
	</td>
	</tr>
</table>
<div class="order-info" style="width:97%; margin-bottom: 10px;">
	<p class="block-title">Vehicle(s) Information</p>
	<div>
                        <table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#C4C4C4">
                         <tr bgcolor="#297eaf" >
							 <td  style="padding:3px;"><b><center><font color="white">S.No.</font></center></b></td>
                             <td  style="padding:3px;"><b><center><font color="white"><?= Year ?></font></center></b></td>
                             <td  style="padding:3px;"><b><center><font color="white"><?= Make ?></font></center></b></td>
							 <td  style="padding:3px;"><b><center><font color="white"><?= Model ?></font></center></b></td>
							 <td  style="padding:3px;"><b><center><font color="white"><?= Inop ?></font></center></b></td>
                             <td  style="padding:3px;"><b><center><font color="white"><?= Type ?></font></center></b></td> 
							 <td  style="padding:3px;"><b><center><font color="white"><?= Vin# ?></font></center></b></td>
							 <td  style="padding:3px;"><b><center><font color="white"><?= Lot# ?></font></center></b></td>
							 <td  style="padding:3px;"><b><center><font color="white">Carrier Fee</font></center></b></td>
							 <td  style="padding:3px;"><b><center><font color="white">Loading Fee</font></center></b></td>
							 <td  style="padding:3px;"><b><center><font color="white">Total Cost</font></center></b></td>
						  </tr>
						<?php 
						
						$vehiclecounter = 0;
						foreach($vehicles as $vehicle) : 
						$vehiclecounter = $vehiclecounter + 1;
						?>
                          <tr>
							 <td align="center" bgcolor="#ffffff" style="padding:3px;"><?= $vehiclecounter ?></td>
							 <td align="center" bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->year ?></td>
                             <td align="center" bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->make ?></td>
							 <td align="center" bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->model ?></td> 
							 <td align="center" bgcolor="#ffffff" style="padding-left:5px;"> <?php  print $vehicle->inop==0?"No":"Yes"; ?></td>
                             <td align="center" bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->type ?></td>
                             <td align="center" bgcolor="#ffffff" style="padding:3px;"> <?php  print $vehicle->vin ?></td>
							 <td align="center" bgcolor="#ffffff" style="padding:3px;"> <?php  print $vehicle->lot ?></td>
							 <td align="center" bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->carrier_pay ?></td>
							 <td align="center" bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->deposit ?></td>
							 <td align="center" bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->tariff ?></td>
                           </tr>
						<?php endforeach; ?>
                        </table>
	</div>
</div>
<?php if (isset($this->dispatchSheet)) { 

$phone1_ext ='';
$phone2_ext ='';
if($this->dispatchSheet->carrier_phone1_ext!='')
 $phone1_ext = " <b>X</b> ".$this->dispatchSheet->carrier_phone1_ext;
if($this->dispatchSheet->carrier_phone2_ext!='')
 $phone2_ext = " <b>X</b> ".$this->dispatchSheet->carrier_phone2_ext;

?>
<div class="quote-info" style="width:97%; margin-bottom: 10px;">
	<div ><p class="block-title">Carrier Information<span style="float:right;color:#09C;" onclick="editCarrier('<?= $this->dispatchSheet->id ?>');">Edit</span></p></div>
	<div id="carrier_value">
    <table cellspacing="0" cellpadding="0" border="0" width="100%">
			<tr>
				<td style="vertical-align:top;" valign="top" width="50%">
                        <table width="100%" cellpadding="1" cellpadding="1">
                            
                            <tr><td width="23%"><strong>Company Name</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $this->dispatchSheet->carrier_company_name ?></td></tr>
                            <tr><td width="23%"><strong>Contact Name</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $this->dispatchSheet->carrier_contact_name ?></td></tr>
                            <tr><td width="23%"><strong>Phone 1</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= formatPhone($this->dispatchSheet->carrier_phone_1); ?><?= $phone1_ext ?></td></tr>
                            <tr><td width="23%"><strong>Phone 2</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= formatPhone($this->dispatchSheet->carrier_phone_2); ?><?= $phone2_ext ?></td></tr>
                            <tr><td width="23%"><strong>Fax</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $this->dispatchSheet->carrier_fax ?></td></tr>
                            <tr><td width="23%"><strong>Email</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><a href="mailto:<?= $this->dispatchSheet->carrier_email ?>"><?= $this->dispatchSheet->carrier_email ?></a></td></tr>
                            <?php $carrier = $this->entity->getCarrier();?>
                            <?php if ($carrier instanceof Account) { ?>
                            <tr><td width="23%"><strong>Hours of Operation</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $carrier->hours_of_operation ?></td></tr>
                            
                            <?php } ?>
                            
                            <tr><td width="23%"><strong>Driver Name</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $this->dispatchSheet->carrier_driver_name ?></td></tr>
                            <tr><td width="23%"><strong>Driver Phone</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= formatPhone($this->dispatchSheet->carrier_driver_phone); ?></td></tr>
                            </table>
                   </td>
                   <td style="vertical-align:top;">
                    <table width="100%" cellpadding="1" cellspacing="1">
                        <tr>
                          <td width="23%"><b>Payment Terms:</b> </td>
                          <td>
                   <?php 
						 $payments_terms = $this->entity->payments_terms;
						 if(in_array($this->entity->balance_paid_by, array(2, 3 , 16 , 17 , 8,9,18,19))){   
							$payments_terms = "COD / COP";
						 }
						if($payments_terms!=""){?>
						<?php print $payments_terms;?>
					   <?php } ?>
                       </td>
                       
                       </tr>
                      
                         <?php
						  if($this->entity->carrier_id >0){
							  
						    $carrier = new Account($this->daffny->DB);			
                            
							try
							{
								 if($this->entity->carrier_id !=0 && $this->entity->carrier_id!=''){
									$carrierObj = $carrier->load($this->entity->carrier_id);
									if ($carrier->insurance_doc_id >0) { 
									  $insurance_expirationdate = date("m/d/y", strtotime($carrier->insurance_expirationdate ));
									?>
										  <tr>
											  <td><b>Insurance Doc:</b> </td>
											 <td><a href="<?= SITE_IN?>application/accounts/getdocs/id/<?= $carrier->insurance_doc_id ?>/type/1" title="Expire Date: <?= $insurance_expirationdate?>"><img src="<?= SITE_IN?>images/ins_doc.png" width="40" height="40"></a></td>
										   </tr>
											<tr>
											  <td><b>Insurance Expire:</b> </td>
											 <td><?= $insurance_expirationdate?></td>
										   </tr>
												  
									<?php }
								 }
								
							} catch (FDException $e) {
								//continue;
							}	
							
                            
						  }
						 ?>
                         
                      
                       </table>
                   <td>
                   </tr>    
                </table>            
	</div>
    
    <div id="carrier_edit" style="display:none;">
    <table cellspacing="0" cellpadding="0" border="0" width="100%">
			<tr>
				<td style="vertical-align:top;" valign="top" width="50%">
                        <table width="100%" cellpadding="1" cellpadding="1">
                        
                        <tr><td width="23%"><strong>Company Name</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><input type="text" size="50" name="carrier_company_name" id="carrier_company_name" value="<?= $this->dispatchSheet->carrier_company_name ?>" /></td></tr>
                        <tr><td width="23%"><strong>Contact Name</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><input type="text" size="50" name="carrier_contact_name" id="carrier_contact_name" value="<?= $this->dispatchSheet->carrier_contact_name ?>" /></td></tr>
                        <tr><td width="23%"><strong>Phone 1</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><input type="text" size="40" name="carrier_phone_1" id="carrier_phone_1" value="<?= $this->dispatchSheet->carrier_phone_1 ?>" /></td></tr>
                        <tr><td width="23%"><strong>Phone 2</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><input type="text" size="40" name="carrier_phone_2" id="carrier_phone_2" value="<?= $this->dispatchSheet->carrier_phone_2 ?>" /></td></tr>
                        <tr><td width="23%"><strong>Fax</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><input type="text" size="40" name="carrier_fax" id="carrier1_fax" value="<?= $this->dispatchSheet->carrier_fax ?>" /></td></tr>
                        <tr><td width="23%"><strong>Email</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><input type="text" size="50" name="carrier_email" id="carrier1_email" value="<?= $this->dispatchSheet->carrier_email ?>" /></td></tr>
                        <?php $carrier = $this->entity->getCarrier();?>
                        <?php if ($carrier instanceof Account) { ?>
                        <tr><td width="23%"><strong>Hours of Operation</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><input type="text" size="50" name="hours_of_operation" id="hours_of_operation" value="<?= $this->dispatchSheet->hours_of_operation ?>" /></td></tr>
                        
                        <?php } ?>
                        
                        <tr><td width="23%"><strong>Driver Name</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><input type="text" size="40" name="carrier_driver_phone" id="carrier1_driver_phone" value="<?= $this->dispatchSheet->carrier_driver_phone ?>" /></td></tr>
                        <tr><td width="23%"><strong>Driver Phone</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><input type="text" size="50" name="carrier_driver_name" id="carrier_driver_name" value="<?= $this->dispatchSheet->carrier_driver_name ?>" /></td></tr>
                        <tr><td colspan="3" align="center"><input type="button" name="CarrierUpdate" value="Update"  onclick="updateCarrier('<?= $this->dispatchSheet->id ?>');"/>
                        &nbsp;&nbsp;&nbsp;<input type="button" name="CarrierCancel" value="Cancel"  onclick="cancelCarrier('<?= $this->dispatchSheet->id ?>');"/>
                        </td></tr>
                        </table>
		
		            </td>
                   <td style="vertical-align:top;">
                   <?php 
						 $payments_terms = $this->entity->payments_terms;
						 if(in_array($this->entity->balance_paid_by, array(2, 3 , 16 , 17 , 8,9,18,19))){   
							$payments_terms = "COD / COP";
						 }
						if($payments_terms!=""){?>
						<b>Payment Terms:</b> <?php print $payments_terms;?>
					   <?php } ?>
                   </td>
                   </tr>
                </table> 
		
	</div>
    
    
</div>
	<div style="clear:right;"></div>
<?php } ?>

<div style="clear:left;"></div>
<div class="order-info" style="width:97%;float: left;">
	<p class="block-title">Special Dispatch Instructions</p>
	<div>
	    
		<?php //foreach ($this->notes[Note::TYPE_FROM] as $note) : 
		  if(count($this->notes[Note::TYPE_FROM])>0){
		?>
			<div style="margin-top:5px;border-bottom: 1px solid #CCCCCC;clear:both;">
				<?= $this->notes[Note::TYPE_FROM][0]->getText() ?>
			</div>
		<?php 
		  }
		//endforeach; ?>
	</div>
</div>

<?php $notes = $this->notes; ?>
<div class="order-info" style="width: 97%;float: left;margin-top: 10px;">
	<p class="block-title">Internal Notes</p>
	<div>
	<?php //if ($this->entity->status != Entity::STATUS_ARCHIVED) : ?>
		<textarea class="form-box-textarea" style="width: 920px; height: 52px;" maxlength="1000" id="internal_note"></textarea>

<div style="float:left; padding:2px;"><strong>
Quick Notes&nbsp;</strong>
<select name="quick_notes" id="quick_notes" onchange="addQuickNote();">
<option value="">--Select--</option>
<option value="Document Upload: Release(s) attached.">Document Upload: Release(s) attached.</option>
<option value="Document Upload: Gate Pass(es) attached.">Document Upload: Gate Pass(es) attached.</option>
<option value="Document Upload: Dock Receipt attached.">Document Upload: Dock Receipt attached.</option>
<option value="Document Upload: Photos attached.">Document Upload: Photos attached.</option>
<option value="Phoned: Bad Mobile.">Phoned: Bad Number.</option>
<option value="Phoned: No Voicemail.">Phoned: No Voicemail.</option>
<option value="Phoned: Left Message.">Phoned: Left Message.</option>
<option value="Phoned: No Answer.">Phoned: No Answer.</option>
<option value="Phoned: Spoke to Customer.">Phoned: Spoke to Customer.</option>
<option value="Phoned: Spoke to carrier about pick-up.">Phoned: Spoke to carrier about pick-up.</option>
<option value="Phoned: NSpoke to carrier about drop-off.">Phoned: Spoke to carrier about drop-off.</option>
<option value="Phoned: Customer requested carrier info.">Phoned: Customer requested carrier info.</option>
<option value="Phoned: Customer reported  damage.">Phoned: Customer reported damage.</option>
<option value="Phoned: Customer canceled, late pick-up.">Phoned: Customer canceled, late pick-up.</option>
<option value="Phoned: Customer canceled, no reason given.">Phoned: Customer canceled, no reason given.</option>
<option value="Phoned: Customer canceled, through e-Mail.">Phoned: Customer canceled, through e-Mail.</option>
<option value="Phoned: Customer was happy with the transport.">Phoned: Customer was happy with the transport.</option>
<option value="Phoned: Customer was un-happy with the transport.">Phoned: Customer was un-happy with the transport.</option>
<option value="Phoned: Customer want a refund.">Phoned: Customer want's a refund.</option>
<option value="Phoned: Not Interested.">Phoned: Not Interested.</option>
<option value="Phoned: Do Not Call.">Phoned: Do Not Call.</option>
</select>
</div>
<div style="float:left; padding:2px;">
<strong>&nbsp;&nbsp;&nbsp;Priority&nbsp;</strong>
</div>
<div style="float:left; padding:2px;"><select name="priority_notes" id="priority_notes" >
<option value="1">Low</option>
<option value="2">High</option>

</select>
</div>

<div style="float:right;"><?= functionButton('Add Note', 'addInternalNote()') ?></div>
		<div style="clear:both;"><br/></div>
	<?php //endif; ?>
		<div id="" style="overflow: auto; height:300px;">
		<table cellspacing="0" cellpadding="0" width="100%" border="0" class="grid" id="internal_notes_table">
			<thead>
			<tr class="grid-head">
				<td class="grid-head-left"><?=$this->order->getTitle('created', 'Date')?></td>
				<td width="65%">Note</td>
				<td width="15%">User</td>
				<td class="grid-head-right">Action</td>	
			</tr>
			</thead>
			<tbody>
			<? if (count($notes[Note::TYPE_INTERNAL]) == 0) : ?>
			<tr class="grid-body">
				<td colspan="4" class="grid-body-left grid-body-right" align="center"><i>No notes available.</i></td>
			</tr>
			<? else : ?>
			<?php foreach($notes[Note::TYPE_INTERNAL] as $note) : ?>
			<?php $sender = $note->getSender(); 
			 
			   $email = $sender->email;
			   $contactname = $sender->contactname;
			   if($note->system_admin == 2){
			     $email = "admin@freightdragon.com";
				 $contactname = "FreightDragon";
			   }
			
			if (($_SESSION['member']['access_notes'] == 0 ) 
				    || $_SESSION['member']['access_notes'] == 1
					|| $_SESSION['member']['access_notes'] == 2
					)
			{
				//$color = "";
				//if($note->priority==2)
				// $color = "#39F";
			?>
			<tr class="grid-body" >
				 <td style="white-space:nowrap;"  class="grid-body-left" <?php if($note->priority==2){?> style="color:#FF0000"<?php }?>><?= $note->getCreated("m/d/y h:i a") ?></td>
				<td id="note_<?= $note->id ?>_text" style=" <?php if($note->discard==1){ ?>text-decoration: line-through;<?php }?><?php if($note->priority==2){?>color:#FF0000;<?php }?>"><?php if($note->system_admin == 1 || $note->system_admin == 2){?><b><?= $note->getText() ?></b><?php }elseif($note->priority==2){?><b style="font-size:12px;"><?= $note->getText() ?></b><?php }else{?><?= $note->getText() ?><?php }?></td>
				<td style="text-align: center;" <?php if($note->priority==2){?>style="color:#FF0000"<?php }?>><a href="mailto:<?= $email ?>"><?= $contactname ?></a></td>
				<td class="grid-body-right" style="white-space: nowrap;" <?php if($note->priority==2){?>style="color:#FF0000"<?php }?>>
				  <?php   //if (!$this->entity->readonly) : ?>
					
                    
                  <?php //if(($note->sender_id == (int)$_SESSION['member_id']) || ((int)$_SESSION['member_id']==1)){
					  //print $_SESSION['member']['access_notes']."---".$note->sender_id ."==". (int)$_SESSION['member_id']."--".$note->system_admin; 
					  //print $_SESSION['member']['access_notes']."---".$note->sender_id."==".$_SESSION['member_id']."---".$note->system_admin;
					if (($_SESSION['member']['access_notes'] == 0 ) ||
					  ($_SESSION['member']['access_notes'] == 1 && ($note->sender_id == (int)$_SESSION['member_id']))
					  || $_SESSION['member']['access_notes'] == 2
					)
					{
						
					if($note->sender_id == (int)$_SESSION['member_id']  && $note->system_admin == 0 ){
						?>
                        <img src="<?= SITE_IN ?>images/icons/strike.png" alt="Discard" title="Discard" width="16" height="16" class="action-icon edit-note" onclick="discardNote(<?= $note->id ?>)"/>	
                        <?php
						 }
						 
					 if($note->system_admin == 0 && $_SESSION['member']['access_notes'] != 0 ){
					  ?>  
                      
                    
                    
                    <img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit" width="16" height="16" class="action-icon edit-note" onclick="editInternalNote(<?= $note->id ?>)"/>
                      
					<img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete" width="16" height="16" class="action-icon delete-note" onclick="delInternalNote(<?= $note->id ?>)"/>
                    
                     <?php 
						}
					 
					 }
					 
					 ?>
                    
					<?php //else : ?>&nbsp;<?php //endif; ?>
				</td>
			</tr>
            
            <?php } ?>
			<?php endforeach; ?>
			<?php endif ; ?>
			</tbody>
		</table>
		</div>
	</div>
</div>
		</td><td valign="top" style="padding-left:5px;">
        
     
        <?php 

 
 if ($this->entity->type == Entity::TYPE_ORDER){
	 
   $isColor = $this->entity->isPaidOffColor();
       $Dcolor = "black";
	   $Ccolor = "black";
	   $Tcolor = "black";
	   
	   if($isColor['carrier']==1)
		$Ccolor = "green";
	   elseif($isColor['carrier']==2)	
		$Ccolor = "red";
	   
	   if($isColor['deposit']==1)
		$Dcolor = "green";	
	   elseif($isColor['deposit']==2)	
		$Dcolor = "red";
		
	   if($isColor['total']==1)
		$Tcolor = "green";	
	   elseif($isColor['total']==2)	
		$Tcolor = "red";	
 }
?>
 <div class="quote-info" style="width:94%;float: left; margin-bottom:10px;">
	<p class="block-title">Shipping Cost</p>
	<div>
	<table width="100%" cellpadding="1" cellpadding="1" border="0"  >
                     <tr><td width="40%"><strong>Total amount</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><span class='<?= $Tcolor;?>'><?= $this->entity->getTotalTariff() ?></span></td></tr>
                     <tr> <td style="line-height:15px;"><strong>Carrier Fee</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><span class='<?= $Ccolor;?>'><?= $this->entity->getCarrierPay() ?></span></td></tr>
                     <tr> <td style="line-height:15px;"><strong>Broker Fee</strong></td><td width="4%" align="center"><b>:</b></td><td><span class='<?= $Dcolor;?>'><?= $this->entity->getTotalDeposit() ?></span></td></tr>                     				 
	</table>    
	</div>
</div>

<div class="quote-info" style="width:94%;float: left; margin-bottom:10px;">
	<p class="block-title">Status &nbsp;&nbsp;</p><strong>Current Status:</strong>&nbsp;<?php  

	
			if($this->entity->status == Entity::STATUS_ARCHIVED){
                                     print "Cancelled";

						     }elseif($this->entity->status == Entity::STATUS_PICKEDUP){  
							        print "Picked Up";

							 }elseif($this->entity->status == Entity::STATUS_DISPATCHED){								 
                                     print "Dispatched";
									 
							 }elseif($this->entity->status == Entity::STATUS_DELIVERED){
                                     print "Delivered";
									 
							} elseif($this->entity->status == Entity::STATUS_POSTED){
							         print "Posted";

							 }elseif($this->entity->status == Entity::STATUS_NOTSIGNED){
							         print "Not Signed";
									 
							 }elseif($this->entity->status == Entity::STATUS_ISSUES){
							        print "Pending Payments";							    

							 }elseif($this->entity->status == Entity::STATUS_ONHOLD){								 
                                     print "OnHold";
									 
							 }else{
                                     print "My Order";		
 							  }	

			      ?>
	<div>
		<strong>New order: </strong><?= $this->entity->getOrdered("m/d/y h:i a") ?><br/>
		<?php if (!is_null($this->entity->dispatched)) { ?><strong>Dispatched: </strong><?= $this->entity->getDispatched("m/d/y h:i a") ?><br/><?php } ?>
		<?php if (!is_null($this->entity->archived)) { ?><strong>Archived: </strong><?= $this->entity->getArchived("m/d/y h:i a") ?><br/><?php } ?>
		<?php if (!is_null($this->entity->load_date)) { ?>
		<strong>Pickup <?=Entity::$date_type_string[$this->entity->load_date_type]?>: </strong><?= $this->entity->getLoadDate("m/d/Y") ?><br/>
		<?php } ?>
		<?php if (!is_null($this->entity->delivery_date)) { ?>
		   <strong>Delivery <?=Entity::$date_type_string[$this->entity->delivery_date_type]?>: </strong><?= $this->entity->getDeliveryDate("m/d/Y") ?><br/>
		<?php } ?>
		<?php if($this->entity->status == Entity::STATUS_ISSUES || $this->entity->status == Entity::STATUS_DELIVERED)
		       {
			      if (!is_null($this->entity->delivered)) { ?>
		              <strong>Actual Delivery Date: </strong><?= date("m/d/Y", strtotime($this->entity->delivered)) ?><br/>
		          <?php } ?>
		 <?php }?>
		
		<?php
		 if($this->entity->status == Entity::STATUS_PICKEDUP){
		      if (!is_null($this->entity->actual_pickup_date)) { ?>
		          <strong>Actual Pickup Date: </strong><?= $this->entity->getActualPickUpDate("m/d/Y") ?><br/>
		     <?php } ?>
		<?php } ?>
		<?php if (!is_null($this->entity->actual_ship_date)) { ?>
		<strong>Actual Ship Date: </strong><?= $this->entity->getActualDeliveryDate("m/d/Y") ?><br/>
		<?php } ?>
	</div>
</div>  
     
<div style="clear:left;"></div>



<div class="quote-info" style="width:94%;float: left; margin-bottom:10px;">
	<p class="block-title">Documents</p>
	<div>
		<ul class="files-list" id="cat">
			<?php if ( isset($this->files) && count($this->files) ) { ?>
				<? foreach ($this->files as $file) { 
				     $fileArr = explode("-",$file['name_original']);
					 
					 $fileName = substr($fileArr[0],0,-5);
					 
					 $fileDate = date("Y-m-d",strtotime($file['date_uploaded']));
				 ?>
					<li id="file-<?= $file['id'] ?>">
						<?=$file['img']?>
						<a href="<?=getLink("orders", "getdocs", "id", $file['id'])?>"><?=$fileName?></a>
						&nbsp;<b>(</b> <?= $fileDate//$file['size_formated']?> <b>)</b><br />
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=getLink("orders", "getdocs", "id", $file['id'])?>">Download</a>
                        
                        &nbsp;&nbsp;&nbsp;&nbsp;<a <?=strtolower($file['type'])=='pdf'?"target=\"_blank\"":""?> href="<?=getLink("orders", "getdocs", "id", $file['id'])?>">View</a>
						&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="sendFile('<?=$file['id'];?>', '<?=$file['name_original']?>')">Email</a>
						
					</li>
				<?php } ?>
			<?php } else { ?>
				<li id="nodocs">No documents.</li>
			<?php } ?>
		</ul>
	</div>
</div>
<div style="clear:right;"></div>

</td></tr></table>
<div style="clear:both;"></div>