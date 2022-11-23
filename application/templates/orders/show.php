<style>

    h3.details

    {

        padding:22px 0 0;

        width: 100%;

        font-size:20px;

    }

    h1.details {

        width: 100%;

        color: #3b67a6;

    }

    .shipper_detail

    {

        text-align:left;

        font-size:15px;

        color:#222;

        height:40px;

        line-height:40px;

        padding-left:15px;

        background-color:#f7f8fa;

        border-bottom:1px solid #ebedf2;

    }

    li#file-135438

    {

        padding:0px 11px;

    }

    .nav-tabs

    {

        margin:0 0 0px 0;

    }

    span.cke_wrapper.cke_ltr

    {

        background:white !important;

    }

    table.custom_table_new_info th,

    table.custom_table_new_info td

    {

        border: 0;

    }

    .new_form-group > label

    {

        color:#444;

    }

</style>

<?php

$mobileDevice = 0;

$mobileDevice = detectMobileDevice();

?>



<?php include 'search-carrier.php';?>



<div class="modal fade" id="maildiv" tabindex="-1" role="dialog" aria-labelledby="maildiv1" aria-hidden="true">

	<div class="modal-dialog modal-dialog-centered" role="document">

		<div class="modal-content">

			<div class="modal-header">

				<h5 class="modal-title" id="maildiv1">Email message with that document attachede</h5>

				<button type="button" class="close" data-dismiss="modal" aria-label="Close">

					<i class="fa fa-times" aria-hidden="true"></i>

				</button>

			</div>

			<div class="modal-body">

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

						<td id="mail_file_name"></td>

					</tr>

				</table>

			</div>

			<div class="modal-footer">

				<button type="button" class="btn btn-sm btn-dark" data-dismiss="modal">Cancal</button>

				<button type="button" class="btn btn_dark_green btn-sm " onclick="maildiv()">Submit</button>

			</div>

		</div>

	</div>

</div>



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

			<div class="modal-body">



				<div style="float: left;">

					<ul style="margin-top: 26px;">  

						<li style="margin-bottom: 14px;">Form Type&nbsp; <input value="1" id="attachPdf" name="attachTpe" type="radio"/><label for="attachPdf" style="margin-right: 2px; cursor:pointer;">&nbsp; PDF</label>&nbsp;<input value="0" id="attachHtml"  name="attachTpe" type="radio"/><label for="attachHtml" style="cursor:pointer">&nbsp; HTML</label></li>

						<li style="margin-bottom: 11px;"><strong>Attachment(s):&nbsp;&nbsp;</strong><span style="color:#24709F;" id="mail_att_new"></span></li>

					</ul>

				</div>

				<div style="text-align:right;">

					<div style="text-align:right;">

						<img src="/images/icons/add.gif" />

						<span style="margin-bottom: 3px;cursor:pointer; position: relative;bottom:4px; color:#24709F;" class="add_one_more_field_" >Add a Field</span>

						<ul>

							<li id="extraEmailsingle" style="margin-bottom:6px;">

								<span>Email:<span style="color:red">*</span></span> <input type="text" id="mail_to_new" name="mail_to_new" class="form-box-combobox" >

							</li>

							<li style="margin-bottom:6px;margin-top:6px;margin-left:292px;position:relative;display:none;" id="mailexttra">

								<input name="optionemailextra" class="form-box-combobox optionemailextra" type="text"><a href="#" style="position: absolute;margin-left:2px;margin-top: 8px;" class="remove_2sd_field"><img id="singletop" style="width: 12px;height: 12px;" src="/images/icons/delete.png"></a></li>

							<li style="margin-bottom: 6px;">

								<span style="margin-right: 18px;">CC:</span>

								<input type="text" id="mail_cc_new" name="mail_cc_new" class="form-box-combobox" >

							</li>

							<li style="margin-bottom: 12px;">

								<span style="margin-left: 9px;">BCC:</span>

								<input type="text" id="mail_bcc_new" name="mail_bcc_new" class="form-box-combobox"/>

							</li>

						</ul>

					</div>

					<div class="edit-mail-content" style="margin-bottom: 8px;">

						<div class="form-group" >

							<label>Subject:</label>

							<div class="form-group">

								<input type="text" id="mail_subject_new" class="form-box-textfield form-control" maxlength="255" name="mail_subject_new" >

							</div>

						</div>

						<div class="form-group">

							<label>Body:</label>

							<div class="form-group" >

								<textarea class="form-box-textfield form-group ckeditor"  name="mail_body_new" id="mail_body_new" style="height:150px" ></textarea>

							</div>

						</div>

					</div>

					<input type="hidden" name="form_id" id="form_id"  value=""/>

					<input type="hidden" name="entity_id" id="entity_id"  value=""/>

					<input type="hidden" name="skillCount" id="skillCount" value="1" />

				</div>

			</div>

			<div class="modal-footer">

				<button type="button" class="btn btn-sm btn-dark" data-dismiss="modal">Cancal</button>

				<button type="button" class="btn btn_dark_green btn-sm " onclick="maildivnew_submit()">Submit</button>

			</div>

		</div>

	</div>

</div>

<!--end::Modal-->



<script>

    CKEDITOR.replace( 'mail_body_new' );

</script>



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



<script type="text/javascript">

	$(document).ready(function(){

		$("#mail_to").change(function(){

		var new_trimemail = $.trim($("#mail_to").val());

		$('#mail_to').val(new_trimemail)

		});

	});



	function Processing_show()

	{

		KTApp.blockPage({

		overlayColor: '#000000',

		type: 'v2',

		state: 'primary',

		message: '.'

		});

	}





	function maildivnew_submit()

	{

		/* Processing_show();*/

		$(".modal-body").addClass('kt-spinner kt-spinner--v2 kt-spinner--lg kt-spinner--dark');



		var sEmail =[$('#mail_to_new').val(),$('.optionemailextra').val(),$('#mail_cc_new').val(),$('#mail_bcc_new').val()];

		if (validateEmaildetail(sEmail)== false) {

			swal.fire('Invalid Email Address');

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

					swal.fire('Empty Field(s)');

					return false;

				};

			},

			success: function (response) {

			$(".modal-body").removeClass('kt-spinner kt-spinner--v2 kt-spinner--lg kt-spinner--dark');

				// $("body").nimbleLoader("hide");

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

					Swal.fire("Can't save note. Try again later, please");

				}

				busy = false;

			},

			error: function(result) {

				$("#internal_note").val(text);

				Swal.fire("Can't save note. Try again later, please");

				busy = false;

			}

		});

	}

	

	function delInternalNote(id)

	 {

			Swal.fire({

			title: 'Are you sure?',

			text: "Are you sure want to delete this note?!",

			type: 'warning',

			showCancelButton: true,

			confirmButtonColor: '#3085d6',

			cancelButtonColor: '#d33',

			confirmButtonText: 'Yes, delete it!'

			}).then((result) => {

			if (result.value) {

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

							swal.fire("Can't delete note. Try again later, please");

						}

						busy = false;

					},

					error: function(result) {

						swal.fire("Can't delete note. Try again later, please");

						busy = false;

					}

			});



		}

	})



}





function editInternalNote(id) {

	 var text = $.trim($("#note_"+id+"_text").text());

	 $("#note_edit_form textarea").val(text);

     $("#edit_save").val(id);

	 $("#note_edit_form").modal();

}

	



function note_edit_form_send(id)

{



	console.log(id);

    var text = $.trim($("#note_"+id+"_text").text());

	if ($("#note_edit_form textarea").val() == text) {

	    $("#note_edit_form").modal('hide');



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

				$("#note_edit_form").modal('hide');

			} else {

				swal.fire("Can't save note. Try again later, please");

			}

			busy = false;

		},

		error: function(result) {

			swal.fire("Can't save note. Try again later, please");

			busy = false;

		}

	  });

	}

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

				swal.fire("Can't discard note. Try again later, please");

			}

			busy = false;

		},

		error: function(result) {

			swal.fire("Can't discard note. Try again later, please");

			busy = false;

		}

	});

}

	

	function cancelDispatchSheet(dispatch_id) {

	var entity_id = '<?php echo $_GET['id'];?>';

	$.ajax({



		type: "POST",



		url: BASE_PATH+"application/ajax/dispatch.php",



		dataType: 'json',



		 /* chetu added code */

		data: {

                    action: 'cancel',

                    id: dispatch_id,

                    entity_id: entity_id

		},



		success: function(res) {



			if (res.success) {



				document.location.reload();



			} else {



				swal.fire("Can't cancel Dispatch Sheet");



			}



		}



	});

}



function editCarrier(dispatch_id)

{



	 console.log("click");



	  $("#carrier_value").find('#carrier_edit_hide').hide();



	  $("#carrier_edit").show();

	

	$(".show_input_new").hide();

	$("#carrier_company_name").show();

	$("#carrier_contact_name").show();

	$("#carrier_phone_1").show();

	$("#carrier_phone_2").show();

	$("#carrier1_fax").show();

	$("#carrier1_email").show();

	$("#hours_of_operation").show();

	$("#carrier_driver_name").show();

	$("#carrier1_driver_phone").show();

	$("#carrier_update_btn_info_").show();

}





function cancelCarrier(dispatch_id)

{

	/*$("#carrier_value").show();*/

	$("#carrier_value").find('#carrier_edit_hide').show();

	$("#carrier_edit").hide();

	$(".show_input_new").show();

	

	$("#carrier_company_name").hide();

	$("#carrier_contact_name").hide();

	$("#carrier_phone_1").hide();

	$("#carrier_phone_2").hide();

	$("#carrier1_fax").hide();

	$("#carrier1_email").hide();

	$("#hours_of_operation").hide();

	$("#carrier_driver_name").hide();

	$("#carrier1_driver_phone").hide();

	$("#carrier_update_btn_info_").hide();

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

				swal.fire("Can't cancel Dispatch Sheet");

			}

		}

     });

}

</script>





<!-- Modal -->

<div class="modal fade" id="note_edit_form" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle45" aria-hidden="true">

	<div class="modal-dialog modal-dialog-centered" role="document">

		<div class="modal-content">

			<div class="modal-header">

				<h5 class="modal-title" id="exampleModalLongTitle45">Edit Internal Note</h5>

				<button type="button" class="close" data-dismiss="modal" aria-label="Close">

				</button>

			</div>

			<div class="modal-body">

				<textarea style="width: 95%;height:100px;" class="form-box-textarea" name="note_text"></textarea>

				

			</div>

			<div class="modal-footer">

				<button type="button" class="btn btn-dark btn-sm" data-dismiss="modal">Close</button>

				<button type="button" id="edit_save" value="" class="btn_dark_green btn-sm btn" onclick="note_edit_form_send(this.value)">Save </button>

			</div>

		</div>

	</div>

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



<!--<div style="float:right; width:170px;">

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

</div>-->

<?php }  ?>



<?php include('order_menu.php');  ?>



	<div class="col-3" style="margin-top:-90px;margin-bottom:41px; margin-left: 24px">

		<h3 class="details">Order #<?= $this->entity->getNumber() ?>

			<h6 class="details" >Current Status : <?php  

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

			</h6>

		</h3>

	</div>



	<div class="col-12" style="margin-bottom:30px;">

        <div class="row">

        <div class="col-12 col-sm-9">       

	<?php

		$assigned = $this->entity->getAssigned();

		$shipper = $this->entity->getShipper();

		$origin = $this->entity->getOrigin();

		$destination = $this->entity->getDestination();

		$vehicles = $this->entity->getVehicles();

	?>

	<?php $Balance_Paid_By = "";

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

		

		$code = str_replace("(","",$arrArea[0]); */

		$code = substr($shipper->phone1, 0, 3);

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

		$code = substr($shipper->phone2, 0, 3);

		$areaCodeStr2="";  

		//print "WHERE  AreaCode='".$code."'";

		$areaCodeRows2 = $this->daffny->DB->selectRow("*", "app_area_code", "WHERE  AreaCode='".$code."'");

		if (!empty($areaCodeRows2)) {

			$areaCodeStr2 = "<b>".$areaCodeRows2['StdTimeZone']."-".$areaCodeRows2['statecode']."</b>"; 

		}

	}

	?>

	<?php	

	$phone1 = "1". $shipper->phone1;

	$phone2 = "1".$shipper->phone2;

	$phone1_ext ='';

	$phone2_ext ='';

	if($shipper->phone1_ext!='')

	$phone1_ext = " <b>X</b> ".$shipper->phone1_ext;

	if($shipper->phone2_ext!='')

	$phone2_ext = " <b>X</b> ".$shipper->phone2_ext;

	

	if($this->entity->source_id >0){

		try {

			$source = new Leadsource($this->daffny->DB);

			$source->load($this->entity->source_id);

			$sourceName = $source->company_name;

		} catch(FDException $e) {

			$sourceName = $this->entity->referred_by;

		}

	}

	elseif($this->entity->referred_id >0) {

		$sourceName = $this->entity->referred_by;

	}

	?>

	<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">

		

		<div id="headingOne " class="hide_show">

			<div class="card-title collapsed" >

				<h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Order Information</h3>

			</div>

		</div>

		

		<div id="order_info" style="padding-left:20px;padding-right:20px;">

			<div class="row">

				<div class="col-6">

					<h5 class="mb-3 mt-3">Shipper Details</h5>

					<table class="table custom_table_new_info" >

						<tr>

							<td><strong>Account ID</strong></td>

							<td width="4%" align="center"  ><b>:</b></td>

							<td align="left"  ><a href="<?php echo SITE_IN; ?>application/accounts/details/id/<?= $this->entity->account_id?>" target="_blank"><?= $this->entity->account_id?></a></td>

						</tr> 

						<tr>

							<td><strong>Name</strong></td>

							<td width="4%" align="center"><b>:</b></td>

							<td align="left"><?= $shipper->fname ?> <?= $shipper->lname ?></td>

						</tr>

						<tr>

							<td><strong>Company</strong></td>

							<td width="4%" align="center"  ><b>:</b></td>

							<td><strong><?= $shipper->company; ?></strong></td>

						</tr>

						<tr>

							<td><strong>Email</strong></td>

							<td width="4%" align="center"><b>:</b></td>

							<td><a href="mailto:<?= $shipper->email ?>"><?= $shipper->email ?></a></td>

						</tr>

						<tr>

							<td><strong>Phone 1</strong></td>

							<td width="4%" align="center"><b>:</b></td>

							<td>

								<?php if($mobileDevice==1){?>

								<a href="tel:<?php print $phone1;?>" ><?= formatPhone($shipper->phone1) ?></a>

								<?php }else{?>

								<a href="javascript:void(0);" onclick="customPhoneSms('<?= $phone1; ?>');"><?= formatPhone($shipper->phone1); ?></a>

								<?php }?>

								<?= $phone1_ext;?> <?= $areaCodeStr1; ?>

							</td>

						</tr>

						<tr>

							<td style="line-height:15px;"><strong>Phone 2</strong></td>

							<td width="4%" align="center"><b>:</b></td>

							<td>

								<?php if($mobileDevice==1){?>

								<a href="tel:<?php print $phone2;?>" ><?= formatPhone($shipper->phone2) ?></a>

								<?php }else{?>

								<a href="javascript:void(0);" onclick="customPhoneSms('<?= $phone2; ?>');"><?= formatPhone($shipper->phone2); ?></a>

								<?php }?>

								<?= $phone2_ext;?> <?= $areaCodeStr2; ?>

							</td>

						</tr>

						<tr>

							<td style="line-height:15px;"><strong>Mobile</strong></td>

							<td width="4%" align="center"><b>:</b></td>

							<td><?= formatPhone($shipper->mobile); ?></td>

						</tr>

						<tr>

							<td style="line-height:15px;"><strong>Fax</strong></td>

							<td width="4%" align="center"><b>:</b></td>

							<td><?= formatPhone($shipper->fax); ?></td>

						</tr>

					</table> 

				</div>



				<div class="col-6">

					<h5 class="mb-3 mt-3">Order Details</h5>

					<table class="table custom_table_new_info">

						<tr>

							<td><strong>1st Avail. Pickup</strong></td>

							<td width="4%" align="center"><b>:</b></td>

							<td><?= $this->entity->getFirstAvail("m/d/y") ?></td>

						</tr>

						<tr>

							<td><strong>Payment Method</strong></td>

							<td width="4%" align="center"><b>:</b></td>

							<td><font color="red"><?= $this->entity->getPaymentOption($this->entity->customer_balance_paid_by); ?></font></td>

						</tr>

						<tr>

							<td><strong>Carrier Paid By</strong></td>

							<td width="4%" align="center"><b>:</b></td>

							<td><font color="red"><?= $Balance_Paid_By; ?></font></td>

						</tr>	

						<tr>

							<td><strong>Ship Via: </strong></td>

							<td width="4%" align="center"  style="line-height:15px;"><b>:</b></td>

							<td align="left"  style="line-height:15px;">

								<span style="color:red;weight:bold;"><?= $this->entity->getShipVia() ?></span>

							</td>

						</tr>

						<?php if (is_numeric($this->entity->distance) && ($this->entity->distance > 0)) : ?>

						<tr>

							<td><strong>Mileage: </strong></td>

							<td width="4%" align="center"><b>:</b></td>

							<td><?= number_format($this->entity->distance, 0, "", "") ?> mi($ <?= number_format(($this->entity->getCarrierPay(false) / $this->entity->distance), 2, ".", ",") ?>/mi)&nbsp;&nbsp;(<span class='red' onclick="mapIt(<?= $this->entity->id ?>);">MAP IT</span></td>

						</tr>

						<?php endif; ?>

						<tr>

							<td style="line-height:15px;"><strong>Assigned to</strong></td>

							<td width="4%" align="center"><b>:</b></td>

							<td><?= $assigned->contactname ?></td>

						</tr>

						<?php if($this->entity->referred_by != ""){?>

						<tr>

							<td style="line-height:15px;"><strong>Source</strong></td>

							<td width="4%" align="center"><b>:</b></td>

							<td><?= $sourceName ?></td>

						</tr>

						<?php  } else { 

							if($assigned->hide_lead_sources==0) {

						?>

						<tr>

							<td style="line-height:15px;"><strong>Source</strong></td>

							<td width="4%" align="center"><b>:</b></td>

							<td><?= $sourceName ?></td>

						</tr>

						<?php  }

						}

						?>

					</table>

				</div>



			</div>

		</div>

	</div>

	

	<div class="row">

		<div class="col-6">

			<?php

			$phone1_ext = '';

			$phone2_ext = '';

			$phone3_ext = '';

			$phone4_ext = '';

			if($origin->phone1_ext != '')

			$phone1_ext = " <b>X</b> ".$origin->phone1_ext;

			if($origin->phone2_ext != '')

			$phone2_ext = " <b>X</b> ".$origin->phone2_ext;

			if($origin->phone3_ext != '')

			$phone3_ext = " <b>X</b> ".$origin->phone3_ext;

			if($origin->phone4_ext != '')

			$phone4_ext = " <b>X</b> ".$origin->phone4_ext;

			?>

			

			<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">

		

				<div id="headingOne " class="hide_show">

					<div class="card-title collapsed" >

						<h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Pickup Information</h3>

					</div>

				</div>

				

				<div id="pickup_infomation_info_new_2" style="padding-left:20px;padding-right:20px;">

					<table class="table custom_table_new_info">

						<tr>

							<td width="35%"><strong>Address</strong></td>

							<td width="4%" align="center"  style="line-height:15px;"><b>:</b></td>

							<td align="left"  style="line-height:15px;"><?= $origin->address1; ?>,&nbsp;&nbsp;<?= $origin->address2; ?></td>

						</tr>

						<tr>

							<td style="line-height:15px;"><strong>City</strong></td>

							<td width="4%" align="center"  style="line-height:15px;"><b>:</b></td>

							<td align="left"  style="line-height:15px;">

								<span class="like-link"onclick="window.open('<?= $origin->getLink() ?>')"><?= $origin->getFormatted() ?></span>

							</td>

						</tr>

						<tr>

							<td style="line-height:15px;"><strong>Location Type</strong></td>

							<td width="4%" align="center"><b>:</b></td>

							<td><?= $origin->location_type; ?></td>

						</tr>

						<tr>

							<td style="line-height:15px;"><strong>Hours</strong></td>

							<td width="4%" align="center"><b>:</b></td>

							<td><?= $origin->hours; ?></td>

						</tr>

						<tr>

							<td style="line-height:15px;"><strong>Contact Name 1</strong></td>

							<td width="4%" align="center"  style="line-height:15px;"><b>:</b></td>

							<td align="left" style="line-height:15px;">

								<?= $origin->name; ?>&nbsp;&nbsp;<? if ($origin->phone1 <> '') {?><strong>P:</strong>&nbsp;<?= formatPhone($origin->phone1); ?><?= $phone1_ext ?> <? } ?>&nbsp;&nbsp;<? if ($origin->phone_cell <> '') {?><strong>M:</strong>&nbsp;<?= formatPhone($origin->phone_cell); ?> <? } ?>

							</td>

						</tr>

						<tr>

							<td style="line-height:15px;"><strong>Contact Name 2</strong></td>

							<td width="4%" align="center"  style="line-height:15px;"><b>:</b></td>

							<td align="left"  style="line-height:15px;">

								<?= $origin->name2; ?>&nbsp;&nbsp;<? if ($origin->phone2 <> '') {?><strong>P:</strong>&nbsp;<?= formatPhone($origin->phone2); ?><?= $phone2_ext ?> <? } ?>&nbsp;&nbsp;<? if ($origin->phone_cell2 <> '') {?><strong>M:</strong>&nbsp;<?= formatPhone($origin->phone_cell2); ?> <? } ?>

							</td>

						</tr>

						<tr>

							<td style="line-height:15px;"><strong>Company Name</strong></td>

							<td width="4%" align="center"><b>:</b></td>

							<td><?= $origin->company; ?></td>

						</tr>

						<tr>

							<td style="line-height:15px;"><strong>Company Phone</strong></td>

							<td width="4%" align="center"><b>:</b></td>

							<td><?= formatPhone($origin->phone3); ?><?= $phone3_ext ?></td>

						</tr>

						<tr>

							<td style="line-height:15px;"><strong>Company Fax</strong></td>

							<td width="4%" align="center"><b>:</b></td>

							<td><?= formatPhone($origin->fax); ?></td>

						</tr>

						<tr>

							<td style="line-height:15px;"><strong>Auction Name</strong></td>

							<td width="4%" align="center"><b>:</b></td>

							<td><?= $origin->auction_name; ?></td>

						</tr>

						<tr>

							<td style="line-height:15px;"><strong>Auction Phone</strong></td>

							<td width="4%" align="center"><b>:</b></td>

							<td><?= formatPhone($origin->phone4); ?><?= $phone4_ext ?></td>

						</tr>

						<tr>

							<td style="line-height:15px;"><strong>Auction Fax</strong></td>

							<td width="4%" align="center"><b>:</b></td>

							<td><?= formatPhone($origin->fax2); ?></td>

						</tr>

						<tr>

							<td style="line-height:15px;"><strong>Buyer Number</strong></td>

							<td width="4%" align="center"><b>:</b></td>

							<td><?= $origin->buyer_number; ?></td>

						</tr>

						<tr>

							<td style="line-height:15px;"><strong>Booking Number</strong></td>

							<td width="4%" align="center"><b>:</b></td>

							<td><?= $origin->booking_number; ?></td>

						</tr>

					</table>

				</div>



			</div>

		</div>

		

		

		

		<div class="col-6">			

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

			

			<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">

		

				<div id="headingOne " class="hide_show">

					<div class="card-title collapsed" >

						<h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Dropoff Information</h3>

					</div>

				</div>

				

				<div id="dropoff_infomation_info_new_2" style="padding-left:20px;padding-right:20px;">

					<table class="table custom_table_new_info">

						<tr>

							<td width="35%"><strong>Address</strong></td>

							<td width="4%" style="line-height:15px;"><b>:</b></td>

							<td style="line-height:15px;">

								<?= $destination->address1; ?>,&nbsp;&nbsp;<?= $destination->address2; ?>

							</td>

						</tr>

						<tr>

							<td style="line-height:15px;"><strong>City</strong></td>

							<td width="4%" align="center"  style="line-height:15px;"><b>:</b></td>

							<td align="left"  style="line-height:15px;">

								<span class="like-link"onclick="window.open('<?= $destination->getLink() ?>')"><?= $destination->getFormatted() ?></span>

							</td>

						</tr>

						<tr>

							<td style="line-height:15px;"><strong>Location Type</strong></td>

							<td width="4%" align="center"><b>:</b></td>

							<td><?= $destination->location_type; ?></td>

						</tr>

						<tr>

						   <td style="line-height:15px;"><strong>Hours</strong></td>

						   <td width="4%" align="center"><b>:</b></td>

						   <td><?= $destination->hours; ?></td>

						</tr>

						<tr>

							<td style="line-height:15px;"><strong>Contact Name 1</strong></td>

							<td width="4%" align="center"  style="line-height:15px;"><b>:</b></td>

							<td align="left"  style="line-height:15px;">

								<?= $destination->name; ?>&nbsp;&nbsp;<? if ($destination->phone1 <> '') {?><strong>P:</strong>&nbsp;<?= formatPhone($destination->phone1); ?><?= $phone1_ext?> <? } ?>&nbsp;&nbsp;<? if ($destination->phone_cell <> '') {?><strong>M:</strong>&nbsp;<?= formatPhone($destination->phone_cell); ?> <? } ?>

							</td>

						</tr>

						<tr>

							<td style="line-height:15px;"><strong>Contact Name 2</strong></td>

							<td width="4%" align="center"  style="line-height:15px;"><b>:</b></td>

							<td align="left"  style="line-height:15px;">

								<?= $destination->name2; ?>&nbsp;&nbsp;<? if ($destination->phone2 <> '') {?><strong>P:</strong>&nbsp;<?= formatPhone($destination->phone2); ?><?= $phone2_ext?> <? } ?>&nbsp;&nbsp;<? if ($destination->phone_cell2 <> '') {?><strong>M:</strong>&nbsp;<?= formatPhone($destination->phone_cell2); ?> <? } ?>

							</td>

						</tr>

						<tr>

							<td style="line-height:15px;"><strong>Company Name</strong></td>

							<td width="4%" align="center"><b>:</b></td>

							<td><?= $destination->company; ?></td>

						</tr>

						<tr>

							<td style="line-height:15px;"><strong>Company Phone</strong></td>

							<td width="4%" align="center"><b>:</b></td>

							<td><?= formatPhone($destination->phone3); ?><?= $phone3_ext?></td>

						</tr>

						<tr>

							<td style="line-height:15px;"><strong>Company Fax</strong></td>

							<td width="4%" align="center"><b>:</b></td>

							<td><?= formatPhone($destination->fax); ?></td>

						</tr>

						<tr>

							<td style="line-height:15px;"><strong>Auction Name</strong></td>

							<td width="4%" align="center"><b>:</b></td>

							<td><?= $destination->auction_name; ?></td>

						</tr>

						<tr>

							<td style="line-height:15px;"><strong>Auction Phone</strong></td>

							<td width="4%" align="center"><b>:</b></td>

							<td><?= formatPhone($destination->phone4); ?><?= $phone4_ext?></td>

						</tr>

						<tr>

							<td style="line-height:15px;"><strong>Auction Fax</strong></td>

							<td width="4%" align="center"><b>:</b></td>

							<td><?= formatPhone($destination->fax2); ?></td>

						</tr>

						<tr>

							<td style="line-height:15px;"><strong>Buyer Number</strong></td>

							<td width="4%" align="center"><b>:</b></td>

							<td><?= $destination->buyer_number; ?></td>

						</tr>

						<tr>

							<td style="line-height:15px;"><strong>Booking Number</strong></td>

							<td width="4%" align="center"><b>:</b></td>

							<td><?= $destination->booking_number; ?></td>

						</tr>

					</table>

				</div>

				

			</div>

			

		</div>

	</div>

	

	

	<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">

		

		<div id="headingOne"  class="hide_show">

			<div class="card-title collapsed">

				<h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Vehicle(s) Information</h3>

			</div>

		</div>

		

		<div id="vehicle_info_new" class="collapse show"  style="padding-left:20px;padding-right:20px;padding-bottom:10px;">

			<div>

				<table class="table table-bordered">

					<thead>

						<tr>

							<th>S.No.</th>

							<th><?= Year ?></th>

							<th><?= Make ?></th>

							<th><?= Model ?></th>

							<th><?= Inop ?></th>

							<th><?= Type ?></th> 

							<th><?= Vin# ?></th>

							<th><?= Lot# ?></th>

							<th>Carrier Fee</th>

							<th>Loading Fee</th>

							<th>Total Cost</th>

						</tr>

					</thead>

					<tbody>

						<?php 

						$vehiclecounter = 0;

						foreach($vehicles as $vehicle) : 

						$vehiclecounter = $vehiclecounter + 1;

						?>					

						<tr>

							<td><?= $vehiclecounter ?></td>

							<td><?= $vehicle->year ?></td>

							<td><?= $vehicle->make ?></td>

							<td><?= $vehicle->model ?></td> 

							<td><?php print $vehicle->inop==0?"No":"Yes"; ?></td>

							<td><?= $vehicle->type ?></td>

							<td> <?php print $vehicle->vin ?></td>

							<td> <?php print $vehicle->lot ?></td>

							<td><?= $vehicle->carrier_pay ?></td>

							<td><?= $vehicle->deposit ?></td>

							<td><?= $vehicle->tariff ?></td>

						</tr>

						<?php endforeach; ?>

					</tbody>

				</table>

			</div>

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

	<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">

	

		<span style="float:right;color:#09C;margin-top:10px;margin-right:15px;" onclick="editCarrier('<?= $this->dispatchSheet->id ?>');">Edit</span>

		

		<div id="headingOne"  class="hide_show">

			<h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Carrier Information</h3>

		</div>



		<div id="carrier_value" style="padding-left:20px;padding-right:20px;padding-bottom:10px;">

			<div class="row">

				<div class="col-6"  id="carrier_edit_hide">

					<table class="table custom_table_new_info">

						<tr>

							<td width="40%"><strong>Account ID</strong></td>

							<td width="4%" align="center"  style="line-height:15px;"><b>:</b></td>

							<td align="left"  style="line-height:15px;">

								<a target="_blank"href="/application/accounts/details/id/<?= $this->entity->carrier_id ?>"><?= $this->entity->carrier_id ?></a>

							</td>

						</tr>

						<tr>

							<td width="23%"><strong>MC Number</strong></td>

							<td width="4%" align="center"  style="line-height:15px;"><b>:</b></td>

							<td align="left"  style="line-height:15px;"><?= $this->dispatchSheet->carrier_insurance_iccmcnumber ?></td>

						</tr>

						<tr>

							<td width="23%"><strong>Company Name</strong></td>

							<td width="4%" align="center"  style="line-height:15px;"><b>:</b></td>

							<td align="left"  style="line-height:15px;"><?= $this->dispatchSheet->carrier_company_name ?></td>

						</tr>

						<tr>

							<td width="23%"><strong>Contact Name</strong></td>

							<td width="4%" align="center"  style="line-height:15px;"><b>:</b></td>

							<td align="left"  style="line-height:15px;"><?= $this->dispatchSheet->carrier_contact_name ?></td>

						</tr>

						<tr>

							<td width="23%"><strong>Phone 1</strong></td>

							<td width="4%" align="center"  style="line-height:15px;"><b>:</b></td>

							<td align="left"  style="line-height:15px;"><?= formatPhone($this->dispatchSheet->carrier_phone_1); ?><?= $phone1_ext ?></td>

						</tr>

						<tr>

							<td width="23%"><strong>Phone 2</strong></td>

							<td width="4%" align="center"  style="line-height:15px;"><b>:</b></td>

							<td align="left"  style="line-height:15px;"><?= formatPhone($this->dispatchSheet->carrier_phone_2); ?><?= $phone2_ext ?></td>

						</tr>

						<tr>

							<td width="23%"><strong>Fax</strong></td>

							<td width="4%" align="center"  style="line-height:15px;"><b>:</b></td>

							<td align="left" style="line-height:15px;"><?= $this->dispatchSheet->carrier_fax ?></td>

						</tr>

						<tr>

							<td width="23%"><strong>Email</strong></td>

							<td width="4%" align="center"  style="line-height:15px;"><b>:</b></td>

							<td align="left"  style="line-height:15px;">

								<a href="mailto:<?= $this->dispatchSheet->carrier_email ?>"><?= $this->dispatchSheet->carrier_email ?></a>

							</td>

						</tr>

						<?php $carrier = $this->entity->getCarrier(); ?>

						<?php if ($carrier instanceof Account) { ?>

						<tr>

							<td width="23%"><strong>Hours of Operation</strong></td>

							<td width="4%" align="center"  style="line-height:15px;"><b>:</b></td>

							<td align="left"  style="line-height:15px;"><?= $carrier->hours_of_operation ?></td>

						</tr>

						<?php } ?>

						<tr>

							<td width="23%"><strong>Driver Name</strong></td>

							<td width="4%" align="center"  style="line-height:15px;"><b>:</b></td>

							<td align="left"  style="line-height:15px;"><?= $this->dispatchSheet->carrier_driver_name ?></td>

						</tr>

						<tr>

							<td width="23%"><strong>Driver Phone</strong></td>

							<td width="4%" align="center"  style="line-height:15px;"><b>:</b></td>

							<td align="left"  style="line-height:15px;"><?= formatPhone($this->dispatchSheet->carrier_driver_phone); ?></td>

						</tr>

					</table>

				</div>



				<!--  -->

				<div id="carrier_edit" class="col-6" style="display:none;">

					

					<div class="new_form-group">

						<label>Company Name</label>

						<input type="text" size="50" class="form-control" name="carrier_company_name" id="carrier_company_name" value="<?= $this->dispatchSheet->carrier_company_name ?>" />

					</div>

					

					

					<div class="new_form-group">

						<label>Contact Name</label>

						<input type="text" size="50" class="form-control" name="carrier_contact_name" id="carrier_contact_name" value="<?= $this->dispatchSheet->carrier_contact_name ?>" />

					</div>

					

					

					<div class="new_form-group">

						<label>Phone 1</label>

						<input type="text" size="40" class="form-control" name="carrier_phone_1" id="carrier_phone_1" value="<?= $this->dispatchSheet->carrier_phone_1 ?>" />

					</div>

					

					<div class="new_form-group">

						<label>Phone 2</label>

						<input type="text" size="40" class="form-control" name="carrier_phone_2" id="carrier_phone_2" value="<?= $this->dispatchSheet->carrier_phone_2 ?>" />

					</div>

					

					<div class="new_form-group">

						<label>Fax</label>

						<input type="text" size="40" class="form-control" name="carrier_fax" id="carrier1_fax" value="<?= $this->dispatchSheet->carrier_fax ?>" />

					</div>

					

					<div class="new_form-group">

						<label>Email</label>

						<input type="text" size="50" class="form-control" name="carrier_email" id="carrier1_email" value="<?= $this->dispatchSheet->carrier_email ?>" />

					</div>

					

					<?php $carrier = $this->entity->getCarrier();?>

					<?php if ($carrier instanceof Account) { ?>

					<div class="new_form-group">

						<label>Hours of Operation</label>

						<input type="text" size="50" class="form-control" name="hours_of_operation" id="hours_of_operation" value="<?= $this->dispatchSheet->hours_of_operation ?>" />

					</div>

					<?php } ?>

					

					<div class="new_form-group">

						<label>Driver Name</label>

						<input type="text" size="40" class="form-control" name="carrier_driver_phone" id="carrier1_driver_phone" value="<?= $this->dispatchSheet->carrier_driver_phone ?>" />

					</div>

									

					<div class="new_form-group">

						<label>Driver Phone</label>

						<input type="text" size="40" class="form-control" name="carrier_driver_name" id="carrier_driver_name" value="<?= $this->dispatchSheet->carrier_driver_name ?>" />

					</div>

					

					

					<div class="new_form-group text-right">

						<input type="button" class="btn btn-sm btn_bright_blue" name="CarrierUpdate" value="Update" onclick="updateCarrier('<?= $this->dispatchSheet->id ?>');"/>

						<input type="button" class="btn btn-sm btn_bright_blue" name="CarrierCancel" value="Cancel" onclick="cancelCarrier('<?= $this->dispatchSheet->id ?>');"/>

					</div>



				</div>



				<!--  -->

				<div class="col-6">

					<table class="table custom_table_new_info">

						<tr>

							<td width="30%"><b>Payment Terms:</b> </td>

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

										<td>

											<a href="<?= SITE_IN?>application/accounts/getdocs/id/<?= $carrier->insurance_doc_id ?>/type/1" title="Expire Date: <?= $insurance_expirationdate?>"><img src="<?= SITE_IN?>images/ins_doc.png" width="40" height="40"></a>

										</td>

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

				</div>

			</div>

		</div>







	</div>



	

	

	<?php } ?>





	<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">

		<div id="headingOne " class="hide_show">

			<div class="card-title collapsed" >

				<h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Special Dispatch Instructions</h3>

			</div>

		</div>

		

		<div id="special_dispatch_info" class="collapse show" aria-labelledby="headingOne" style="padding-left:20px;padding-right:20px;padding-bottom:10px;">

			<?php if(count($this->notes[Note::TYPE_FROM])>0){ ?>

			<div style="margin-top:5px;clear:both;margin-bottom:15px;">

				<?= $this->notes[Note::TYPE_FROM][0]->getText() ?>

			</div>

			<?php } ?>

		</div>		

	</div>

	

	<?php $notes = $this->notes; ?>

	<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">

		

		<div id="headingOne " class="hide_show">

			<div class="card-title collapsed" >

				<h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Internal Notes</h3>

			</div>

		</div>

		

		<div id="internal_notes_info" class="collapse show" aria-labelledby="headingOne" style="padding-left:20px;padding-right:20px;padding-bottom:10px;">

			

			<div class="form-group">

				<textarea class="form-control form-box-textarea " maxlength="1000" id="internal_note"></textarea>

			</div>

			

			<div class="row">             

				<div class="col-5">

					<div class="new_form-group_4">

						<label>Quick Notes</label>

						<select class="form-control"  name="quick_notes" id="quick_notes" onchange="addQuickNote();">

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

				</div>

				

				<div class="col-4">

					<div class="new_form-group_4">

						<label>Priority</label>

						<select  name="priority_notes"  class="form-control" id="priority_notes" >

							<option value="1">Low</option>

							<option value="2">High</option>

						</select>

					</div>

				</div>

				

				<div class="col-3">

					<div class="text-right">

						<?= functionButton('Add Note', 'addInternalNote()','','btn-sm btn_dark_green') ?>

					</div>

				</div>

				

			</div>

			

			

			<!---<table class="table custom_table_new_info" id="internal_notes_table">--->

			    <table class="table table-bordered" id="internal_notes_table">

				<thead>

					<tr>

						<td ><?= $this->order->getTitle('created', 'Date') ?></td>

						<td>Note</td>

						<td>User</td>

						<td>Action</td>	

					</tr>

				</thead>

				<tbody>

					<?php if (count($notes[Note::TYPE_INTERNAL]) == 0) : ?>

					<tr >

						<td colspan="4"><i>No notes available.</i></td>

					</tr>

					<?php else : ?>

					<?php foreach($notes[Note::TYPE_INTERNAL] as $note) : ?>

					<?php

					$sender = $note->getSender();



					$email = $sender->email;

					$contactname = $sender->contactname;

					

					if($note->system_admin == 2){

						$email = "admin@cargoflare.com";

						$contactname = "System";

					}



					if (($_SESSION['member']['access_notes'] == 0 )

					|| $_SESSION['member']['access_notes'] == 1

					|| $_SESSION['member']['access_notes'] == 2

					)

					{
					?>
					<tr>
						<td style="white-space:nowrap;"  class="grid-body-left" <?php if($note->priority == 2){ ?> style="color:#FF0000"<?php } ?>><?= $note->getCreated("m/d/y h:i a") ?></td>
						<td id="note_<?= $note->id ?>_text" style=" <?php if($note->discard == 1){ ?>text-decoration: line-through;<?php } ?><?php if($note->priority == 2){ ?>color:#FF0000;<?php } ?>"><?php if($note->system_admin == 1 || $note->system_admin == 2){ ?><b><?= $note->getText() ?></b><?php }elseif($note->priority == 2){ ?><b style="font-size:12px;"><?= $note->getText() ?></b><?php }else{ ?><?= $note->getText() ?><?php } ?></td>
						<td style="text-align: center;" <?php if($note->priority == 2){ ?>style="color:#FF0000"<?php } ?>><a href="mailto:<?= $email ?>"><?= $contactname ?></a></td>
						<td class="grid-body-right" style="white-space: nowrap;" <?php if($note->priority == 2){ ?>style="color:#FF0000"<?php } ?>>
							<?php
								if (($_SESSION['member']['access_notes'] == 0 ) || ($_SESSION['member']['access_notes'] == 1 && ($note->sender_id == (int) $_SESSION['member_id'])) || $_SESSION['member']['access_notes'] == 2	)
								{
									if($note->sender_id == (int) $_SESSION['member_id'] && $note->system_admin == 0 ){
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
						</td>
					</tr>
					<?php } ?>
					<?php endforeach; ?>
				   <?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
	<!--Including Task Manager UI-->
	<? include(TPL_PATH . "task_manager.php"); ?>
</div>
		<div class="col-12 col-sm-3">
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

			<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
				<div id="headingOne " class="hide_show">
					<div class="card-title collapsed" >
						<h3 class="shipping_cost_new shipper_detail text-left" style="padding-left:15px;">Shipping Cost</h3>
					</div>
				</div>
				<div id="shipping_cost_new" class="collapse show" aria-labelledby="headingOne" style="padding-left:20px;padding-right:20px;padding-bottom:10px;">
					<table class="table custom_table_new_info" >
						<tr>
							<td>
								<strong>Total amount</strong>
							</td>
							<td>
								<b>:</b>
							</td>
							<td>
								<span class='<?= $Tcolor;?>'><?= $this->entity->getTotalTariff() ?></span>
							</td>
						</tr>
						<tr>
							<td style="line-height:15px;"><strong>Carrier Fee</strong></td>
							<td width="4%" align="center"  style="line-height:15px;"><b>:</b></td>
							<td align="left"  style="line-height:15px;"><span class='<?= $Ccolor;?>'><?= $this->entity->getCarrierPay() ?></span></td>
						</tr>
						<tr>
							<td style="line-height:15px;"><strong>Broker Fee</strong></td>
							<td width="4%" align="center"><b>:</b></td>
							<td><span class='<?= $Dcolor;?>'><?= $this->entity->getTotalDeposit() ?></span></td>
						</tr>
					</table>
				</div>
			</div>
			<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
				<div id="headingOne " class="hide_show">
					<div class="card-title collapsed" >
						<h3 class="status_new shipper_detail text-left" style="padding-left:15px;">Status</h3>
					</div>
				</div>

				<div id="status_new" class="collapse show" aria-labelledby="headingOne" style="padding:20px;">
					<strong>Current Status:</strong>&nbsp;
					<?php
						if($this->entity->status == Entity::STATUS_ARCHIVED){
							print "Cancelled";
						} elseif($this->entity->status == Entity::STATUS_PICKEDUP){  
							print "Picked Up";
						} elseif($this->entity->status == Entity::STATUS_DISPATCHED){
							print "Dispatched";
						} elseif($this->entity->status == Entity::STATUS_DELIVERED){
							print "Delivered";
						} elseif($this->entity->status == Entity::STATUS_POSTED){
							print "Posted";
						} elseif($this->entity->status == Entity::STATUS_NOTSIGNED){
							print "Not Signed";
						} elseif($this->entity->status == Entity::STATUS_ISSUES){
							print "Pending Payments";
						} elseif($this->entity->status == Entity::STATUS_ONHOLD){
							print "OnHold";
						} else {
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

						<?php if($this->entity->status == Entity::STATUS_ISSUES || $this->entity->status == Entity::STATUS_DELIVERED){
							if (!is_null($this->entity->delivered)) { ?>
								<strong>Actual Delivery Date: </strong><?= date("m/d/Y", strtotime($this->entity->delivered)) ?><br/>
							<?php } ?>
						<?php } ?>
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
			</div>
			<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
				<div id="headingOne " class="hide_show">
					<div class="card-title collapsed" data-toggle="collapse" data-target="#documents_new" aria-expanded="false" aria-controls="documents_new">
						<h3 class="documents_new shipper_detail text-left" style="padding-left:15px;">Documents</h3>
					</div>
				</div>
				<div id="documents_new" class="collapse show" aria-labelledby="headingOne" style="padding-left:20px;padding-right:20px;">
					<!--chetu added sectiopn to display new B2B-->
					<div id="B2BNEW"></div>
					<div class="files_list_new_info">
						<ul class="files-list" id="cat">
							<?php if ( isset($this->files) && count($this->files) ) { ?>
								<? foreach ($this->files as $file) {
									$fileArr = explode("-",$file['name_original']);
									$fileName = substr($fileArr[0],0,-5);
									$fileDate = date("Y-m-d",strtotime($file['date_uploaded']));

								?>
								<li id="file-<?= $file['id'] ?>">
									<?=$file['img']?>
									<a href="<?=getLink("orders", "getdocs", "id", $file['id'])?>"><?=$fileName?></a> <b>(<?= $fileDate?>)</b><br />
									<a class="kt-link" href="<?=getLink("orders", "getdocs", "id", $file['id'])?>">Download</a>
									<a class="kt-link" <?=strtolower($file['type'])=='pdf'?"target=\"_blank\"":""?> href="<?=getLink("orders", "getdocs", "id", $file['id'])?>">View</a>
									<a class="kt-link" href="#" onclick="sendFile('<?=$file['id'];?>', '<?=$file['name_original']?>')">Email</a>
								</li>
								<?php } ?>
							<?php } else { ?>
								<li id="nodocs">No documents.</li>
							<?php } ?>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div> 
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('#taskdate').datepicker({
			startDate: dateToday,
			autoclose : true
		});
	});

	$('#taskdate').datepicker({
		startDate: dateToday,
		autoclose : true
	});
</script>

<link href="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css" rel="stylesheet" type="text/css" />
<script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script> 

