<style type="text/css">
	a.nav-link {
    padding: 11px 13px !important;
}
</style>

<!-- <div id="maildivnew">

    <table cellspacing="2" cellpadding="0" border="0">

        <tr>

           <td colspan="2" style="padding-left:5px;">

             <table cellspacing="2" cellpadding="0" border="0">

             <tbody id="skill_id">

               <tr>

                <td>@mail_to_new@</td>

                <td >

                              <div style="float:left;"><ul class="ul-tags"><li><a href="javascript:void(0);" style="font-length:12px; font-family:Verdana, Geneva, sans-serif; font-weight:bold; color:#000;" onclick="AddEmailExtra();"><b>Add More</b></a></li></ul></div>

                </td>

               </tr>

               

             </tbody>

           </table>

          </td>      

        </tr>

        <tr>

            <td>@mail_cc_new@</td>

        </tr>

        <tr>

            <td>@mail_subject_new@</td>

        </tr>

        <tr>

            <td>@mail_body_new@</td>

        </tr>

        <tr>

            <td colspan="2">&nbsp;<input type="hidden" name="form_id" id="form_id"  value=""/>

            <input type="hidden" name="entity_id" id="entity_id"  value=""/>

            <input type="hidden" name="skillCount" id="skillCount" value="1">

            </td>

        </tr>

        

    </table>

    

</div> -->

		<!-- Modal -->
	<div class="modal fade" id="carrierdiv" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					</button>
				</div>
				<div class="modal-body">
					<div id="carrier_data"> </div>
				</div>
				<div class="modal-footer">
					
				</div>
			</div>
		</div>
	</div>
<?php

    $avail_title = Entity::TITLE_FIRST_AVAIL;

    if (isset($_GET['orders'])){

        if (in_array($_GET['orders'], array("notsigned","dispatched","pickedup","delivered","issues","archived"))){

            $avail_title = Entity::TITLE_PICKUP_DELIVERY;

        }

    }

?>
<!-- 
<div id="listmails">

	<div class="mail-list-label">

		<table cellspacing="0" cellpadding="0" border="0" class="grid" width="100%">

			<tbody>

				<tr class="grid-head">

					<td class="grid-head-left id-column">

						<?php if (isset($this->order)) : ?>

						<?php echo $this->order->getTitle("id", "ID") ?>

						<?php else : ?>ID<?php endif; ?>

					</td>

					<td class="shipper-column">

						<?php 

						if (isset($this->order)): 

							echo $this->order->getTitle("shipper", "Shipper");

						else:

							echo "Shipper"; 

						endif;

						?>

					</td>

					<td class="grid-head-right">

						Action

					</td>

				</tr>

			</tbody>

		</table>	

	</div>

	<div class="repeat-column"></div>

	<div class="editmail"></div>

</div>
 -->


<!-- sample it skill row start-->

	<!-- <table id="testData" style="display:none; visibility:hidden;">

	<tr>

	<td>&nbsp;</td><td  colspan="2"><input type="text" name="email_extra" id="email_extra" value="" class="form-box-textfield"  maxlength="100" tooltip='E-mail' style="width:280px;"></td>

	</tr>

	</table> -->


		
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
function makeActionType(){
	
    var issue_type = encodeURIComponent(document.getElementById('issue_type').value);
	var actionSTR = 'freightdragon';
	
	actionSTR += '/issue_type/'+issue_type;
	document.issue_form.action = document.issue_form.action + actionSTR;
    location.href = document.issue_form.action;
}


	function reassignOrders_submit()
	{
		var member_id = $("#company_members").val();
		console.log(member_id);
		reassignOrders(member_id);
	}

var email_extra_status = 0;

function AddEmailExtra()

{

	if(email_extra_status==0){

		var skillIDObj=document.getElementById('skill_id');

		var testDataRows=document.getElementById('testData').getElementsByTagName("tr");

		var testDataRowsClone=testDataRows[0].cloneNode(true);

		testDataRowsClone.getElementsByTagName("input")[0].id="email_extra1";

		skillIDObj.appendChild(testDataRowsClone); 

		email_extra_status = 1;

	}

	

	return false 

}

    function printSelectedOrderForm() {

		if ($(".order-checkbox:checked").lenght == 0) {

			Swal.fire('Order not selected.')
			return false; 

	    }

		if ($(".order-checkbox:checked").length > 1) {

			Swal.fire('Error: You may print one order at a time.');

			return false;        

        }

	    var order_id = $(".order-checkbox:checked").val();


        form_id = $("#form_templates").val();

        if (form_id == "") {

			Swal.fire('Please choose form template.')

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

			Swal.fire('Order not selected.')
			return false; 

	    }

	   	var order_id = $(".order-checkbox:checked").val();

	   

        form_id = $("#email_templates").val();

        if (form_id == "") {

			Swal.fire('Please choose email template.')
			return false; 

        } else {



            if (confirm("Are you sure want to send Email?")) {

                Processing_show()

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





$("#maildivnew").dialog({ 

	modal: true,

	width: 500,

	height: 310,

	title: "Email message",

	hide: 'fade',

	resizable: false,

	draggable: false,

	autoOpen: false,

	buttons: {

		"Submit": function () {

			$.ajax({

				url: BASE_PATH + 'application/ajax/entities.php',

				data: {

					action: "emailOrderNewSend",

					form_id: $('#form_id').val(),

					entity_id: $('#entity_id').val(),

					mail_to: $('#mail_to_new').val(),
					
					mail_cc: $('#mail_cc_new').val(),
					
					//mail_bcc: $('#mail_bcc_new').val(),

					mail_extra: $('#email_extra1').val(),

					mail_subject: $('#mail_subject_new').val(),

					mail_body: $('#mail_body_new').val()

				},

				type: 'POST',

				dataType: 'json',

				beforeSend: function () {

					if (!validateMailFormNew()) {

						return false;

					} else {

						// // $("body").nimbleLoader("show");

					}

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

$("#listmails").dialog({

	modal: true,

	width: 500,

	height: 310,

	title: "Email List",

	hide: 'fade',

	resizable: false,

	draggable: false,

	autoOpen: false

});	

	

	
	function reassignOrdersDialog() 
		{

			if ($(".order-checkbox:checked").length == 0)
			{
			Swal.fire({
			type: 'error',
			title: 'Oops...',
			text: 'Order not selected',
			})
			return false;
			}

			$("#reassignCompanyDiv").modal();
		}
	

$("#reassignCompanyDiv").dialog({

	modal: true,

	width: 300,

	height: 140,

	title: "Reassign Order(s)",

	hide: 'fade',

	resizable: false,

	draggable: false,

	autoOpen: false,

	buttons: {

		"Submit": function () {

			var member_id = $("#company_members").val();	

			reassignOrders(member_id);

		},

		"Cancel": function () {

			$(this).dialog("close");

		}

	}

});	 



function copyOrders(){

	if ($(".order-checkbox:checked").length == 0) {

        Swal.fire('Order not selected')
		//alert("Order not selected");
		return false;

	}

	var entity_ids = [];



	$(".order-checkbox:checked").each(function(){

		var entity_id = $(this).val();

		entity_ids.push(entity_id);

    });

	

	var entity_count = entity_ids.length;

	if(entity_count>1){

		Swal.fire('Error: You may copy one order at a time.')
		return false;        

	}



	var order_id = $(".order-checkbox:checked").val();

	location.href =  "<?= SITE_IN ?>application/orders/duplicateOrder/id/"+order_id;

}

	 

function cancelDispatchSheet() {

	

     if ($(".order-checkbox:checked").lenght == 0) {

		    swal.fire("Order not selected");

		    return;

	    }

	   var order_id = $(".order-checkbox:checked").val();

	   

	   

	$.ajax({



		type: "POST",



		url: BASE_PATH+"application/ajax/dispatch.php",



		dataType: 'json',



		data: {



			action: 'cancelNew',



			id: order_id



		},



		success: function(res) {



			if (res.success) {



				document.location.reload();

				//alert("Order successfully undispatched.");



			} else {



				swal.fire("Can't cancel Dispatch Sheet");



			}



		}



	});



}	  



function changeOrdersState($val) {

	changeStatusOrders($val); // Change state

}







function getCarrierData(entity_id,ocity,ostate,ozip,dcity,dstate,dzip) {

		

        if (entity_id == "") {

            swal.fire("Order not found");

        } else {



            Processing_show()

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

						//alert('===='+res.success);

                        if (res.success) {

                          // alert(res.carrierData);

							 

							 $("#carrier_data").html(res.carrierData);

							  //$("#mail_file_name").html(file_name);

							  $("#carrierdiv").find('.modal-title').html('Carrier Data');

							  $("#carrierdiv").modal();
							

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

function getVehicles(id) {

   if($("#vehicles-info-"+id).css('display') == 'block')
   {
	   
	  $("#vehicles-info-"+id).toggle(); 
   }
   else
   {
	$.ajax({
		type: "POST",
		url: BASE_PATH+"application/ajax/vehicles.php",
		dataType: 'json',
		data: {
			action: 'getVehicles',
			id: id
		},
		success: function(res) {
			if (res.success) {
				$("#vehicles-info-"+id).toggle();
				$("#vehicles-info-"+id).html(res.data);
				
			} else {
				swal.fire("Vehicles not found.");
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
function checkEditDispatch()
{
   
    var entity_id = $(".order-checkbox:checked").val();
    $.ajax({
            type: "POST",
            url: "<?=SITE_IN?>application/ajax/entities.php",
            dataType: 'json',
            data: {
                action: 'checkEditDispatch',
                entity_id: entity_id
            },
            success: function (response) {
                if (response.success == false) {
		     swal.fire("Someone editing this Order right now. You have access only for read.");
                     return false;
                }
		else
		{
		    setEditBlock(entity_id);
            //interval = setInterval(function(){ checkEditTimeDuration(); }, 240000);
            $("#acc_search_dialog_new_dispatch").modal();
		}
    }
    });
}
function setEditBlock(entity_id) {
        $.ajax({
            type: "POST",
            url: "<?=SITE_IN?>application/ajax/entities.php",
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
	
$(document).ready(function(){
        //$("#avail_pickup_date").datepicker({dateFormat: 'mm/dd/yy'});
		$("#dispatch_pickup_date").datepicker({
			dateFormat: "yy-mm-dd",
            //minDate: '+0',
			//setDate: "2012-10-09",
			 onSelect: function( selectedDate ) {
			   //alert(selectedDate);
			   $('#dispatch_date_form').submit();
		
	      }
		});

        
	});

</script>



<div style="display:none" id="notes">notes</div>

<?php
//if($_GET['orders']!="search")
{
	?>
    <form name="dispatch_date_form" id="dispatch_date_form" method="post">

	<table <?php if($_SESSION['member_id']==1){ ?>width="100%"<?php }else{?> width="530"<?php }?>cellpadding="0" cellspacing="0" align="right">

	<tr>
	<?php if($_SESSION['member_id']==1){ ?>
	<td align="right" width="70%" bgcolor="#FFFFFF"># of Orders Dispatched for @dispatch_pickup_date@:</td>

	<td style="white-space: nowrap;" align="left" width="100"  bgcolor="#FFFFFF"><span ><b><?php print " (".$this->todayDispatched[0]['todaydispatch'].")"; ?></b>&nbsp;&nbsp;&nbsp;&nbsp;generating a Total Revenue of : <b><?= ("$ " . number_format((float)$this->todayDispatched[0]['total_tariff_stored'], 2, ".", ",")) ?></b>&nbsp;&nbsp;&nbsp;&nbsp;Gross Profit of : <b><?= ("$ " . number_format((float)($this->todayDispatched[0]['total_tariff_stored'] - $this->todayDispatched[0]['carrier']), 2, ".", ",")) ?></b>&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	<?php }?>

	<td>&nbsp;</td>
	<td width="40" align="center"  bgcolor="#FFFFFF"><img src="<?= SITE_IN ?>images/icons/dollar.png" alt="Total Tariff" title="Total Tariff" width="16" height="16" /></td>

	<td style="white-space: nowrap;"  width="90" bgcolor="#FFFFFF"><span ><b><div id="tariffDataTotal" style="width:90px;"></div></b></span></td>
	<?php //print ("$ " . number_format((float)$this->sumAmount[0]['carrier_pay_stored'], 2, ".", ",")) ?>

	<td>&nbsp;</td>
	<td width="40" align="center"  bgcolor="#FFFFFF"><img src="<?= SITE_IN ?>images/icons/truck.png" alt="<?php print $rowCarrier;?> Carriers Found" title="<?php print $rowCarrier;?> Carriers Found" width="16" height="16" /></td>

	<td style="white-space: nowrap;"  width="90" bgcolor="#FFFFFF"><span ><b><div id="carrierDataTotal" style="width:90px;"></div></b></span></td>
	<?php //print ("$ " . number_format((float)$this->sumAmount[0]['carrier_pay_stored'], 2, ".", ",")) ?>

	<td>&nbsp;</td>

	<td width="40" align="center"  bgcolor="#FFFFFF"><img src="<?= SITE_IN ?>images/icons/person.png" alt="Deposit    "

	title="Deposit" width="16" height="16"/></td>

	<td style="white-space: nowrap;"  width="90" bgcolor="#FFFFFF"><span ><b><div id="depositDataTotal"></div></b></span></td>
	<?php //print ("$ " . number_format((float)($this->sumAmount[0]['deposit_stored'] - $entity['carrier_pay_stored']), 2, ".", ",")) ?>

	</tr>

	</table>
   </form>                 
<?php }?>


<table cellspacing="0" cellpadding="0" width="100%" class="control-bar">

    <tr>
      
          
		  <?php if ($_GET['orders'] == "issues" || $_GET['orders'] == "searchIssue") { ?>

           <td>

           <form id="issue_form" name="issue_form" action="/application/orders/searchIssue" method="post">

           @issue_type@

           </form></td>

           <?php }?>

		 <td><?= functionButton('Print', 'printSelectedOrderForm()') ?></td>

         <td>@form_templates@</td>

         <td><?= functionButton('Email', 'emailSelectedOrderForm()') ?></td>

         <td>@email_templates@</td>        

		

         <?php if ($_GET['orders'] == 'posted') { ?>

            <td valign="top"><?= functionButton('Unpost', 'unpostFromFB()') ?></td>

        <?php }elseif ($_GET['orders'] == "") { ?>

            <td valign="top"><?= functionButton('Post to FB', 'postToFB()') ?></td>

        <?php } elseif ($_GET['orders'] == "dispatched") { ?>

            <td valign="top">

			<?php print  functionButtonDate(Entity::STATUS_PICKEDUP,'Picked Up Date', 'setPickedUpStatusAndDate',false,'pickup_button','yy-mm-dd');?>

			<?php //print functionButton('Picked Up', 'setPickedUpStatus()'); ?></td>

        <?php } elseif ($_GET['orders'] == "pickedup") { ?>

            <td valign="top">

			<?php print  functionButtonDate(Entity::STATUS_DELIVERED,'Delivered Date', 'setPickedUpStatusAndDate',false,'delivered_button','yy-mm-dd');?>

			<?php // print functionButton('Delivered', 'setDeliveredStatus()'); ?></td>

        <?php } ?>

        		

       

       

        

        <?php if (in_array($this->status, array(Entity::STATUS_NOTSIGNED,Entity::STATUS_PICKEDUP, Entity::STATUS_DISPATCHED))) { ?>

                       

                      <td><?= functionButton("Undispatch", "cancelDispatchSheet()") ?></td>

        <?php } ?>

        

        <?php if ($this->status == Entity::STATUS_ONHOLD) : ?>

               <td> <?= functionButton('Restore', 'restoreOrders()') ?></td>

            <?php endif; ?>

         <?php if ($this->status != Entity::STATUS_ARCHIVED) { ?>

        

            <?php if (in_array($_GET['orders'], array("", "posted")) && $_SESSION['member']['access_dispatch']) { ?>

            <td valign="top"><?php //print functionButton('Dispatch', 'dispatch()') ?><?php print functionButton('Dispatch', 'selectCarrierNewDispatch()','','btn-sm btn_dark_blue') ?></td>

        <?php } ?>

        

            <td valign="top">

			<?php print functionButton('Reassign Order(s)', 'reassignOrdersDialog()','','btn-sm btn_dark_green'); ?>

			<?php //print functionButton('Reassign Order', 'reassignOrders(\'top\')'); ?></td>

        <? } ?>

       <?php if($this->status == Entity::STATUS_DELIVERED || $this->status == Entity::STATUS_PICKEDUP){

			  $stateOrders = Entity::STATUS_PICKEDUP;

		       if($this->status == Entity::STATUS_PICKEDUP)

			     $stateOrders = Entity::STATUS_DISPATCHED;

		?>

                <td  valign="top">

			<?php print functionButton('Previous Status', 'changeOrdersState('.$stateOrders.')'); ?>

			<?php //print functionButton('Reassign Order', 'reassignOrders(\'top\')'); ?></td>

        <?php }?>

        <?php if ($this->status == Entity::STATUS_ARCHIVED) { ?>

                     <td valign="top"><?= functionButton('Uncancel', 'changeStatusOrders(1)') ?></td>

        <?php } ?>

        

 <td valign="top"><?php print functionButton('Copy Order', 'copyOrders()','','btn-sm btn_light_blue'); ?></td>

         <td valign="top">

            <?php if ($this->status == Entity::STATUS_ACTIVE 

					  || $_GET['orders'] == 'posted' 

					  || $_GET['orders'] == 'notsigned'

					  || $_GET['orders'] == 'dispatched'

					  || $_GET['orders'] == 'pickedup'

					  ) { ?>

                <?= functionButton('Place On Hold', 'placeOnHoldOrders()') ?>

            <?php }?>

        </td>

        <?php if ($_GET['orders'] != "archived" && $_GET['orders'] != "delivered" && $_GET['orders'] != "issues") { ?>

            <td valign="top"><?= functionButton('Cancel', 'cancelOrders()','btn-sm btn_dark_blue') ?></td>

        <?php } ?>

    </tr>

	

</table>

<table class="table table-bordered mt-4" id="issuetype">

	<thead>

	<tr >

	<th class="grid-head-left">
	
    

	<?php if (isset($this->order)) : ?>

	<?= $this->order->getTitle("id", "ID") ?>

	<?php else : ?>ID<?php endif; ?>
	 <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
     <input type="checkbox" onchange="if($(this).is(':checked')){ checkAllOrders() }else{ uncheckAllOrders() }"><span style="margin-top: -5px;"></span></label>

	</th>

	<th>

	<?php  


	if($this->status == Entity::STATUS_ARCHIVED){

	if (isset($this->order))

	print $this->order->getTitle("archived", "Cancelled");

	else

	print "Cancelled";



	}elseif($this->status == Entity::STATUS_PICKEDUP){  

	if (isset($this->order))

	print $this->order->getTitle("actual_pickup_date", "Picked Up");

	else

	print "Picked Up";



	}elseif($this->status == Entity::STATUS_DISPATCHED){

	if (isset($this->order))

	print $this->order->getTitle("dispatched", "Dispatched");

	else

	print "Dispatched";



	}elseif($this->status == Entity::STATUS_DELIVERED){

	if (isset($this->order))

	print $this->order->getTitle("delivered", "Delivered");

	else

	print "Delivered";



	} elseif($this->status == Entity::STATUS_POSTED){

	if (isset($this->order))

	print $this->order->getTitle("posted", "Posted");

	else

	print "Posted";



	}elseif($this->status == Entity::STATUS_NOTSIGNED){

	if (isset($this->order))

	print $this->order->getTitle("not_signed", "Not Signed");

	else

	print "Not Signed";



	}elseif($this->status == Entity::STATUS_ISSUES){

	if (isset($this->order))

	print $this->order->getTitle("issue_date", "Issue");

	else

	print "Issue";



	}elseif($this->status == Entity::STATUS_ONHOLD){

	if (isset($this->order))

	print $this->order->getTitle("hold_date", "OnHold");

	else

	print "OnHold";



	}else{

	if (isset($this->order))

	print $this->order->getTitle("issue_date", "Issue");

	else

	print "Issue";								

	}	

	?>



	</th>

	<?php if ($_GET['orders'] != 'all') { ?>

	<th>Notes</th>

	<?php } else { ?>

	<th>Broker</th>

	<?php } ?>

	<th>

	<?php if (isset($this->order)) : ?>

	<?= $this->order->getTitle("shipperfname", "Shipper") ?>

	<?php else : ?>Shipper<?php endif; ?>

	</th>

	<th>Vehicle</th>

	<th width="15%">

	<?php if (isset($this->order)) : ?>

	<?= $this->order->getTitle("Origincity", "Origin") ?>

	<?php else : ?>Origin<?php endif; ?>

	/

	<?php if (isset($this->order)) : ?>

	<?= $this->order->getTitle("Destinationcity", "Destination") ?>

	<?php else : ?>Destinations<?php endif; ?>

	</th>
	<?php 
	if($_GET['orders']=="search"){
	?>
	<th colspan="2">

	<?= "Dates" ?>

	</th>
	<?php
	}
	else
	{
	?>	 
	<?php if ($avail_title == Entity::TITLE_PICKUP_DELIVERY):?>

	<th>

	<?= $avail_title; ?>

	</th>

	<th>

	<?php print "Delivery"; ?>

	</th>

	<?php else : ?>

	<th><?= $avail_title; ?><?php //print $this->order->getTitle("avail", $avail_title) ?>

	<?php endif; ?>

	<?php if ($avail_title != Entity::TITLE_PICKUP_DELIVERY){?>

	<th>

	<?php if (isset($this->order)) : ?>

	<?= $this->order->getTitle("posted", "Posted") ?>

	<?php else : ?>Posted<?php endif; ?>

	</th>

	<?php } ?>
	<?php } ?>
	<th >

	Payment Options

	</th>

	<th class="grid-head-right">

	<?php if (isset($this->order)) : ?>

	<?= $this->order->getTitle("total_tariff_stored", "Tariff") ?>

	<?php else : ?>tariff<?php endif; ?>

	</th>

	</tr>
	</thead>

    <?php 

	/*print "<pre>";

	print_r($this->entities);

	print "</pre>";

	*/

	$i=0;

	

	$date_type_string = array(

        1 => "Estimated",

        2=> "Exactly",

        3 => "Not Earlier Than",

        4 => "Not Later Than"

    );



    $ship_via_string = array(

        1 => "Open",

        2 => "Enclosed",

        3 => "Driveaway"

    );

$totalDeposit = 0;
$totalCarrier = 0;
$totalTariff = 0;

	if (count($this->entities) == 0): ?>

        <tr class="grid-body">

            <td colspan="9" align="center" class="grid-body-left grid-body-right"><i>No records</i></td>

        </tr>

    <?php endif; ?>

    <?php 

	$searchData = array();

	 $issue_type = $_GET['issue_type'];
	  
	 $show_result = false;
	 $j=1;

	foreach ($this->entities as $i => $entity) : /* @var Entity $entity */ 

	flush();

	      $i++;


 /******************* isPaidOffColor ***************/

	   $paymentManager = new PaymentManager($this->daffny->DB);

		$owe = 0;

		

		$isColor = array(

                'total' => 0,

                'carrier' => 0,

                'deposit' => 0

            );

		if(!is_null($entity['FlagTarrif']))
		  $isColor['total'] = $entity['FlagTarrif'];
		if(!is_null($entity['FlagCarrier']))
		  $isColor['carrier'] = $entity['FlagCarrier'];
		if(!is_null($entity['FlagDeposite']))
		  $isColor['deposit'] = $entity['FlagDeposite'];  
		  
	/*	 

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

				if($owe <=0)
				  $isColor['deposit'] = 1;
				
				else{
				  $isColor['deposit'] = 2;
				  $totalDeposit += $entity['total_tariff'] - $entity['total_carrier_pay'];
				}

				

				  

				break;

			case Entity::BALANCE_COMPANY_OWES_CARRIER_CASH:

			case Entity::BALANCE_COMPANY_OWES_CARRIER_CHECK:

			case Entity::BALANCE_COMPANY_OWES_CARRIER_COMCHECK:

			case Entity::BALANCE_COMPANY_OWES_CARRIER_QUICKPAY:

				$shipperPaid = $paymentManager->getFilteredPaymentsTotals($entity['entityid'], Payment::SBJ_SHIPPER, Payment::SBJ_COMPANY, false);

				$carrierPaid = $paymentManager->getFilteredPaymentsTotals($entity['entityid'], Payment::SBJ_COMPANY, Payment::SBJ_CARRIER, false);

				//print $this->getCost(false)."-----------".$carrierPaid;

				//$owe = $this->getCost(false) - $carrierPaid;

				

				$cost = $entity['carrier_pay_stored'] + $entity['pickup_terminal_fee'] + $entity['dropoff_terminal_fee'];

				 

				   $owe = $cost - $carrierPaid;

				

				if($owe <=0)

				  $isColor['carrier'] = 1;

				else{
				  $isColor['carrier'] = 2; 
				  $totalCarrier += $entity['total_carrier_pay'];
				}

				  

				$owe = ($entity['total_tariff_stored'] - $entity['carrier_pay_stored']) - $shipperPaid;

				

				if($owe <=0)

				  $isColor['deposit'] = 1;

				else{

				  $isColor['deposit'] = 2;  
				  $totalDeposit += $entity['total_tariff_stored'] - $entity['total_carrier_pay'];
				}
                  

				 $owe = $cost + ($entity['total_tariff_stored'] - $entity['carrier_pay_stored']) - $shipperPaid;

				 if($owe <=0)

				  $isColor['total'] = 1;

				 else

				  $isColor['total'] = 2;

				  

				break;

			case Entity::BALANCE_CARRIER_OWES_COMPANY_CASH:

			case Entity::BALANCE_CARRIER_OWES_COMPANY_CHECK:

			case Entity::BALANCE_CARRIER_OWES_COMPANY_COMCHECK:

			case Entity::BALANCE_CARRIER_OWES_COMPANY_QUICKPAY:

				$carrierPaid = $paymentManager->getFilteredPaymentsTotals($entity['entityid'], Payment::SBJ_CARRIER, Payment::SBJ_COMPANY, false);

				

				$owe = ($entity['total_tariff_stored'] - $entity['carrier_pay_stored']) - $carrierPaid;

				

				if($owe <=0)

				  $isColor['deposit'] = 1; 

				else{

				  $isColor['deposit'] = 2;
				  $totalDeposit += $entity['total_tariff_stored'] - $entity['total_carrier_pay'];
				}



				break;

		}

	*/

	   /************************************************/
	 //if($issue_type > 0)
	 {
		 /*
	   $show_result = false;
		if($issue_type==1){
			
			if(in_array($entity['balance_paid_by'], array(2, 3 , 16 , 17 , 8, 9 , 18 , 19))) {  // COD AND COP
				if($isColor['deposit']==2)
		            $show_result = true;
			}
			
			if(in_array($entity['balance_paid_by'], array(12, 13 , 20 , 21,14, 15 , 22 , 23))) {
				if($isColor['deposit']==2 && $isColor['total']==2)
		            $show_result = true;
			}
			 
		   //if($isColor['deposit']==2 && $isColor['carrier'] == 0 && $isColor['total']==0)
		     // $show_result = true;
		}
		elseif($issue_type==2){
		   if($isColor['carrier'] == 2 )
		      $show_result = true;
		}
		elseif($issue_type==3){
		   if($isColor['total']==2)
		      $show_result = true;
		}
		*/
	 }

		  $bgcolor = "#ffffff";

		  if($i%2==0)
		    $bgcolor = "#f4f4f4";
		
		
        if (($_GET['orders'] == "searchIssue") || ($_GET['orders'] == "issues")){ 
			if ($entity['status'] == Entity::STATUS_ISSUES){
				$delivery_load_date = date("m/d/y", strtotime($entity['issue_date'])); 
				$delivery_date_id = ''; 
				$curr_date = date("m/d/y");	
				$diff = abs(strtotime($curr_date) - strtotime($delivery_load_date));
				$date_diff = floor($diff / (60*60*24));
				if(($date_diff >=30 ) && ($date_diff < 45 ))
				{ 
				  $bgcolor = '#BCC8E4';
				}
				else if(($date_diff >=45 ) && ($date_diff < 60 ))
				{
				  $bgcolor = '#ECCEF5';
				}
				else if($date_diff >=60  &&  $date_diff <90)
				{
				 $bgcolor = '#FFC6BC';
				}
				else if($date_diff >=90  )
				{
				 $bgcolor = '#cccccc';
				}
				else{
				}
			}
        }   

      $searchData[] = $entity['entityid'];
			  

	  
		

		$number = "";

        if (trim($entity['prefix']) != "") {

            $number .= $entity['prefix'] . "-";

        }

        $number .= $entity['number'];
       /* if ($_GET['orders'] == "issues"){ 
		if ($avail_title == Entity::TITLE_PICKUP_DELIVERY){
			$delivery_load_date = date("m/d/y", strtotime($entity['issue_date'])); 
            $delivery_date_id = ''; 
            $curr_date = date("m/d/y");	
			$diff = abs(strtotime($curr_date) - strtotime($delivery_load_date));
            $date_diff = floor($diff / (60*60*24));
		if(($date_diff >=30 ) && ($date_diff < 45 ))
		{ 
		  $bgcolor = '#BCC8E4';
		}
		else if(($date_diff >=45 ) && ($date_diff < 60 ))
		{
		  $bgcolor = '#ECCEF5';
		}
		else if($date_diff >=60 )
		{
		 $bgcolor = '#FFC6BC';
		}
		else{
		}
		}
              }   */

	?>
  <?php //if($show_result)
  {	?>
        <tr id="order_tr_<?= $entity['entityid'] ?>" class="grid-body<?= ($i == 0 ? " first-row" : "") ?>">

           <td align="center" class="grid-body-left" bgcolor="<?= $bgcolor ?>">

                <?php if ($_GET['orders'] != 'all') { ?>

                    <?php if (!$entity['readonly']) : ?>

                        <?php /*?><input type="radio" name="order_id" value="<?= $entity['entityid'] ?>" class="order-checkbox"/><br/><?php */?>


						<label class="kt-checkbox kt-checkbox--bold kt-checkbox--success" style=" margin: 5px 5px 21px 12px;">
						<input  type="checkbox" name="order_id" value="<?= $entity['entityid'] ?>" class="order-checkbox"> 
						<span></span>
						</label>

                    <?php endif; ?>

                     <a href="<?= SITE_IN ?>application/orders/show/id/<?= $entity['entityid'] ?>"><div class="order_id"><?= $number ?></div></a>

					 

                    <a href="<?= SITE_IN ?>application/orders/history/id/<?= $entity['entityid'] ?>">History</a>

					  

					

                    <?php if (isset($_GET['search_string'])) : 

					    print "<br /><b>Status</b><br>";

		                  

						     if ($entity['status'] == Entity::STATUS_ACTIVE) 

							    print "My Order";

							 elseif($entity['status'] == Entity::STATUS_ONHOLD)

							    print "OnHold";

						     elseif($entity['status'] == Entity::STATUS_ARCHIVED)

							    print "Cancelled";	

						     elseif($entity['status'] == Entity::STATUS_POSTED)

							    print "Posted To FB";

							 elseif($entity['status'] == Entity::STATUS_NOTSIGNED)

							    print "Not Signed";

							elseif($entity['status'] == Entity::STATUS_DISPATCHED)

							    print "Dispatched";

							elseif($entity['status'] == Entity::STATUS_ISSUES)

							    print "Issues";	

							elseif($entity['status'] == Entity::STATUS_PICKEDUP)

							    print "Picked Up";	

						    elseif($entity['status'] == Entity::STATUS_DELIVERED)

							    print "Delivered";		

						  ?>

                    <?php endif; ?>

                    

					  <?php //if ($this->status == Entity::STATUS_ARCHIVED) : ?>

                     <?php /*if ($entity['status'] == Entity::STATUS_ARCHIVED) : ?>

                      <br/> <br/> <a href="<?= SITE_IN ?>application/orders/unarchived/id/<?= $entity['entityid'] ?>">UnArchive</a>

                      <?php endif; */?>

					

                <?php } else { ?>

                    <?= $number

                    ; ?>

                <?php } ?>

            </td>

            <td valign="top" style="white-space: nowrap;" bgcolor="<?= $bgcolor ?>">

           

        <?php          if($entity['status'] == Entity::STATUS_ARCHIVED){

							   // print $entity->getArchived("m/d/y h:i a");

							   print date("m/d/y", strtotime($entity['archived']));

			                }

						    elseif($entity['status'] == Entity::STATUS_DISPATCHED){

							    //print $entity->getDispatched("m/d/y h:i a");

								print date("m/d/y h:i a", strtotime($entity['dispatched']));

							}

							elseif($entity['status'] == Entity::STATUS_DELIVERED){

							   // print $entity->getDelivered();

								print (is_null($entity['delivered'])) ? "" : date("m/d/y", strtotime($entity['delivered']));

							}

						    elseif($entity['status'] == Entity::STATUS_POSTED)

							    print date("m/d/y", strtotime($entity['posted']));	

           					elseif($entity['status'] == Entity::STATUS_NOTSIGNED)

							    print date("m/d/y", strtotime($entity['not_signed']));	

							elseif($entity['status'] == Entity::STATUS_ISSUES)

							    print date("m/d/y", strtotime($entity['issue_date']));	

							elseif($entity['status'] == Entity::STATUS_ONHOLD)

							    print date("m/d/y", strtotime($entity['hold_date']));

                            elseif($entity['status'] == Entity::STATUS_PICKEDUP)

							    print date("m/d/y", strtotime($entity['actual_pickup_date']));								

							 else{
                                 print date("m/d/y h:i a", strtotime($entity['created']));
                               	//print $entity->getOrdered("m/d/y h:i a");	
                               /*
								$tz = isset($_SESSION['timezone']) ? $_SESSION['timezone'] : 'America/New_York';

                                $date = new DateTime($entity['ordered'], new DateTimeZone($tz));

								print (is_null($entity['ordered'])) ? "" : gmdate("m/d/y", $date->getTimestamp());
								*/

							 }



 								

			      ?>

            

            <br />

            <?php if($entity['esigned']==1){

				$sql = "SELECT u.id,u.type,u.name_original

                  FROM app_entity_uploads au

                  LEFT JOIN app_uploads u ON au.upload_id = u.id

                 WHERE au.entity_id = '" . $entity['entityid'] . "'

                    AND u.owner_id = '" . getParentId() . "'

					AND `name_original` LIKE  'Signed%'

                 ORDER BY u.date_uploaded Desc limit 0,1";

				$files = $this->daffny->DB->selectRows($sql);

				

				 if ( isset($files) && count($files) ) { 

				               foreach ($files as $file) { 

									

									$pos = strpos($file['name_original'], "Signed");

									if ($pos === false) {}

									else{?>

										<!--li id="file-<?php //print $file['id']; ?>"-->

											<a <?=strtolower($file['type'])=='pdf'?"target=\"_blank\"":""?> href="<?=getLink("orders", "getdocs", "id", $file['id'])?>"><img src="<?= SITE_IN ?>images/icons/esign_small.png" /></a>

										<!--/li-->

                                        <?

									 }

									

									?>

										

								<?php } ?>

						<?php }?>

			  <?php  }

			  elseif($entity['esigned']==2){

				 $sql = "SELECT u.id,u.type,u.name_original

                  FROM app_entity_uploads au

                  LEFT JOIN app_uploads u ON au.upload_id = u.id

                 WHERE au.entity_id = '" . $entity['entityid'] . "'

                    AND u.owner_id = '" . getParentId() . "'

					AND `name_original` LIKE  'B2B%'

                 ORDER BY u.date_uploaded Desc limit 0,1";

                  $files = $this->daffny->DB->selectRows($sql);

                  //$files = $entity->getCommercialFiles($entity->id);

				  //print "--".$entity->getAccount()->id;

				  //print_r($files);

				 if ( isset($files) && count($files) ) { 

				               foreach ($files as $file) { 

									

									$pos = strpos($file['name_original'], "B2B");

									if ($pos === false) {}

									else{?>

										<a <?=strtolower($file['type'])=='pdf'?"target=\"_blank\"":""?> href="<?=getLink("orders", "getdocs", "id", $file['id'])?>"><img src="<?= SITE_IN ?>images/icons/b2b.png" /></a>

                                        <?

									 }

									

									?>

										

								<?php } ?>

						<?php }?>

               <?php  }?>
               <?php  if($entity['invoice_status']==1){ ?>
                        <span style="font-weight: bold;" class="hint--bottom hint--rounded hint--bounce hint--error" data-hint="Invoice Generated"><img src="<?= SITE_IN ?>images/icons/invoice.png" /></span>
                 <?php  } ?>
               <br>Assigned to:<br/> <strong><?= $entity['AssignedName'] ?></strong><br />

               <?php /*if($entity['creator_id']!=0){

				     $entity1 = new Entity($this->daffny->DB);

                     $entity1->load($entity['entityid']);

					 $creator = $entity1->getCreator();

			  ?>

                    <strong>Source: </strong><br /><?php print $creator->contactname; ?><br/>

			<?php }*/?>

            </td>

            <td bgcolor="<?= $bgcolor ?>">

                <?php 

				

				if ($_GET['orders'] != 'all') { ?>

                    <?php 

					

					      //$notes = new NoteManager($this->daffny->DB);

						  

						  //print "==========".$entity['entityid'];

						  //$notesData = $notes->getNotesArrData($entity['entityid']);

					      

						 //$countNewNotes = count($notesData[Note::TYPE_INTERNALNEW]);

						// $countInternalNotes = count($notesData[Note::TYPE_INTERNAL]) + $countNewNotes;

						//print $entity['NotesCount1']."--".$entity['NotesCount2']."--".$entity['NotesCount3']; 

					?>

                    <?php //print notesIcon($entity['entityid'], count($notesData[Note::TYPE_FROM]), Note::TYPE_FROM, $entity['status'] == Entity::STATUS_ARCHIVED); ?>

                    <?php //print  notesIcon($entity['entityid'], count($notesData[Note::TYPE_TO]), Note::TYPE_TO, $entity['status'] == Entity::STATUS_ARCHIVED); ?>

                    <?php //print  notesIcon($entity['entityid'], $countInternalNotes, Note::TYPE_INTERNAL, $entity['status'] == Entity::STATUS_ARCHIVED,$countNewNotes)
                        
						$NotesCount1 = 0;
						if(!is_null($entity['NotesCount1']))
						   $NotesCount1 = $entity['NotesCount1'];
						
						$NotesCount2 = 0;
						if(!is_null($entity['NotesCount2']))
						   $NotesCount2 = $entity['NotesCount2'];
						   
						 $NotesCount3 = 0;
						if(!is_null($entity['NotesCount3']))
						   $NotesCount3 = $entity['NotesCount3'];  
						   
						 $countNewNotes =  $entity['NotesFlagCount3']; 
				    ?>
                    
                    <?= notesIcon($entity['entityid'], $NotesCount1, Note::TYPE_FROM, $entity['status'] == Entity::STATUS_ARCHIVED) ?>

                    <?= notesIcon($entity['entityid'], $NotesCount2, Note::TYPE_TO, $entity['status'] == Entity::STATUS_ARCHIVED) ?>

                    <?= notesIcon($entity['entityid'], $NotesCount3, Note::TYPE_INTERNAL, $entity['status'] == Entity::STATUS_ARCHIVED,$countNewNotes)

				    ?>

                    

                <?php  } else { ?>

                    <? print  $entity['companyname']; ?>

                <?php } ?>

            </td>

            

            <td bgcolor="<?= $bgcolor ?>">

                

                 <div class="shipper_name"><?= $entity['shipperfname'] ?> <?= $entity['shipperlname'] ?><br/></div>

                <?php if($entity['shippercompany']!=""){?><div class="shipper_company"><b><?= $entity['shippercompany']?></b><br /></div><?php }?>

                <?php if($entity['shipperphone1']!=""){?><div class="shipper_number"><?= formatPhone($entity['shipperphone1']) ?><br/></div><?php }?>
                <?php if($entity['shipperphone2']!=""){?><div class="shipper_number"><?= formatPhone($entity['shipperphone2']) ?><br/></div><?php }?>

                <?php if($entity['shipperemail']!=""){?><a href="mailto:<?= $entity['shipperemail'] ?>"><div class="shipper_email"><?= $entity['shipperemail'] ?><br/></div></a><?php }?>

                <div class="shipper_referred"><?php if($entity['referred_by'] != ""){?>

				  Referred By <b><?= $entity['referred_by'] ?></b><br>

				<?php }?></div>

                

            </td>

            

            <td bgcolor="<?= $bgcolor ?>" >

            <?php

			

			//$vehicleManager = new VehicleManager($this->daffny->DB);

		    //$vehicles = $vehicleManager->getVehiclesArrData($entity['entityid'], $entity['type']);

			/*print "<pre>";

			print_r($vehicles);

			print "</pre>";

			*/

			?>

                <?php // $vehicles = $entity1->getVehicles();?>

                <?php //$source = $entity1->getSource(); ?>

                <?php if (count($entity['TotalVehicle']) == 0) { ?>

                <?php }elseif ($entity['TotalVehicle'] == 1) { ?>

                    <?php //$vehicle = $vehicles[0]; ?>

                    <?= $entity['Vehicleyear']; ?> <?= $entity['Vehiclemake']; ?> <?= $entity['Vehiclemodel']; ?> <?php if ($entity['Vehicleinop'] == 1) { ?>  <?php echo("<span style=color:red;weight:bold;>(Inop)</span>"); ?> <?php }?> <br/>

                    <?= $entity['Vehicletype']; ?>&nbsp;<?= imageLink($vehicle['Vehicleyear'] . " " . $entity['Vehiclemake'] . " " . $entity['Vehiclemodel'] . " " . $entity['type']) ?>

                    <br/>
                    <?php if($entity['Vehiclevin']!=""){?>
                      <?= $entity['Vehiclevin']; ?>
                    <br/>
                    <?php }?>
                <?php }else { ?>

                    <span class="like-link multi-vehicles-new" onclick="getVehicles('<?php print $entity['entityid'];?>');">Multiple Vehicles<b><span style="color:#000000;">(<?php print $entity['TotalVehicle'];?>)</span></b></span>

                    <div class="vehicles-info" id="vehicles-info-<?php print $entity['entityid'];?>">

                    </div>

                    <br/>

                <?php  } ?>

                <span style="color:red;weight:bold;"><?php print ($entity['ship_via'] != 0) ? $ship_via_string[$entity['ship_via']] : ""; ?></span><br/>

                <strong>Source: </strong><?php print "not available"; ?>

            </td>

            <?php

			$o_link = "http://maps.google.com/maps?q=" . urlencode($entity['Origincity'] . ",+" . $entity['Originstate']);

			$o_formatted = trim($entity['Origincity'].', '.$entity['Originstate'].' '.$entity['Originzip'], ", ");

			

			$d_link = "http://maps.google.com/maps?q=" . urlencode($entity['Destinationcity'] . ",+" . $entity['Destinationstate']);

			$d_formatted = trim($entity['Destinationcity'].', '.$entity['Destinationstate'].' '.$entity['Destinationzip'], ", ");

			?>

            <td bgcolor="<?= $bgcolor ?>">

               <span class="like-link"

                      onclick="window.open('<?= $o_link ?>', '_blank')"><?= $o_formatted ?></span> /<br/>

                <span class="like-link"

                      onclick="window.open('<?= $d_link ?>')"><?= $d_formatted ?></span><br/>

                

                <?php if (is_numeric($entity['distance']) && ($entity['distance'] > 0)) { ?>

                    <?= number_format($entity['distance'], 0, "", "") ?> mi

                    <?php $cost = $entity['carrier_pay_stored'] + $entity['pickup_terminal_fee'] + $entity['dropoff_terminal_fee'];

                          

                    ?>

                        ($ <?= number_format(($cost / $entity['distance']), 2, ".", ",") ?>/mi)

                <?php } ?>

                <span class="like-link" onclick="mapIt(<?= $entity['entityid'] ?>);">Map it</span>

            </td>

            

            <?php
			
		 $balance_paid_by_arr = array(
            
                Entity::BALANCE_COD_TO_CARRIER_CASH => 'Cash/Certified Funds',
                Entity::BALANCE_COD_TO_CARRIER_CHECK => 'Check',
                Entity::BALANCE_COP_TO_CARRIER_CASH => 'Cash/Certified Funds',
                Entity::BALANCE_COP_TO_CARRIER_CHECK => 'Check',
                Entity::BALANCE_COMPANY_OWES_CARRIER_CASH => 'Cash/Certified Funds',
                Entity::BALANCE_COMPANY_OWES_CARRIER_CHECK => 'Check',
                Entity::BALANCE_COMPANY_OWES_CARRIER_COMCHECK => 'Comcheck',
                Entity::BALANCE_COMPANY_OWES_CARRIER_QUICKPAY => 'QuickPay',
				Entity::BALANCE_COMPANY_OWES_CARRIER_ACH => 'ACH',
                Entity::BALANCE_CARRIER_OWES_COMPANY_CASH => 'Cash/Certified Funds',
                Entity::BALANCE_CARRIER_OWES_COMPANY_CHECK => 'Check',
                Entity::BALANCE_CARRIER_OWES_COMPANY_COMCHECK => 'Comcheck',
                Entity::BALANCE_CARRIER_OWES_COMPANY_QUICKPAY => 'QuickPay',
           
        );
                         $balance_paid_by_value = $entity['balance_paid_by'];
			             $Balance_Paid_By = "";

						   if(in_array($entity['balance_paid_by'], array(2, 3 , 16 , 17)))   

							   $Balance_Paid_By = "COD";

							

							if(in_array($entity['balance_paid_by'], array(8, 9 , 18 , 19)))   

							   $Balance_Paid_By = "COP";

							

							if(in_array($entity['balance_paid_by'], array(12, 13 , 20 , 21)))   

							   $Balance_Paid_By = "Broker:".$balance_paid_by_arr[$balance_paid_by_value];

							 

							 if(in_array($entity['balance_paid_by'], array(14, 15 , 22 , 23)))   

							   $Balance_Paid_By = "Shipper:".$balance_paid_by_arr[$balance_paid_by_value];

			

          if($_GET['orders']=="search")
		    {
				
				
				   $Date1 = "";
				   $Date2 = "";
				        if($entity['status'] == Entity::STATUS_POSTED || $entity['status'] == Entity::STATUS_ACTIVE){
							    if (strtotime($entity['avail_pickup_date']) > 0)
							       $Date1 = "<b>1st avil:-</b><br>".date("m/d/y", strtotime($entity['avail_pickup_date']))."<br>";
								if(strtotime($entity['posted']) > 0)   
							       $Date2 = "<b>Posted:-</b><br>".date("m/d/y", strtotime($entity['posted']));
						}
						elseif($entity['status'] == Entity::STATUS_NOTSIGNED || $entity['status'] ==  Entity::STATUS_DISPATCHED){
							if (strtotime($entity['load_date']) == 0) 
							    $abbr = "N/A";
                            else
                            {
                              $abbr = $entity['load_date_type'] > 0 ? $date_type_string[(int)$entity['load_date_type']] : "";
                              $Date1 = "<b>ETA Pickup:</b><br />".$abbr . "<br />" . date("m/d/y", strtotime($entity['load_date']));
                            }
							    //$Date1 = "<b>ETA Pickup:-</b><br>".$entity->getLoadDateWithAbbr("m/d/y")."<br>";
						    if (strtotime($entity['delivery_date']) == 0) 
							     $abbr = "N/A";
                            else
		                    {
								 $abbr = $entity['delivery_date_type'] > 0 ? $date_type_string[(int)$entity['delivery_date_type']] : "";		
								 $Date2 = "<b>ETA Delivery:</b><br />".$abbr . "<br />" . date("m/d/y", strtotime($entity['delivery_date']));
		                    }
							    //$Date2 = "<b>ETA Delivery:-</b><br>".$entity->getDeliveryDateWithAbbr("m/d/y");
						}
						elseif($entity['status'] == Entity::STATUS_PICKEDUP ){
							     if (strtotime($entity['actual_pickup_date']) > 0)
							       $Date1 = "<b>Pickup:-</b><br>".date("m/d/y", strtotime($entity['actual_pickup_date']));
							    
								    if (strtotime($entity['delivery_date']) == 0) 
							           $abbr = "N/A";
									else
									{
										 $abbr = $entity['delivery_date_type'] > 0 ? $date_type_string[(int)$entity['delivery_date_type']] : "";		
										 $Date2 = $abbr . "<br />" . date("m/d/y", strtotime($entity['delivery_date']));
									}
							       //$Date2 = "<b>ETA Unload:-</b><br>".$entity->getDeliveryDateWithAbbr("m/d/y");
						}
						elseif($entity['status'] == Entity::STATUS_ISSUES || $entity['status'] == Entity::STATUS_DELIVERED){
						   if (strtotime($entity['load_date']) == 0) 
							    $abbr = "N/A";
                            else
                            {
                              $abbr = $entity['load_date_type'] > 0 ? $date_type_string[(int)$entity['load_date_type']] : "";
                              $Date1 = "<b>ETA Pickup:</b><br />".$abbr . "<br />" . date("m/d/y", strtotime($entity['load_date']));
                            }
							    //$Date1 = "<b>ETA Pickup:-</b><br>".$entity->getLoadDateWithAbbr("m/d/y")."<br>";
						    if (strtotime($entity['delivery_date']) == 0) 
							     $abbr = "N/A";
                            else
		                    {
								 $abbr = $entity['delivery_date_type'] > 0 ? $date_type_string[(int)$entity['delivery_date_type']] : "";		
								 $Date2 = "<b>ETA Delivery:</b><br />".$abbr . "<br />" . date("m/d/y", strtotime($entity['delivery_date']));
		                    }	
						}
						/*
						elseif($entity['status'] == Entity::STATUS_DELIVERED){
							if ($entity['actual_pickup_date']!="")
							       $Date1 = "<b>Pickup:-</b><br>".date("m/d/y h:i a", strtotime($entity['actual_pickup_date']));
							if(!is_null($entity['delivered']))
							    $Date2 = "<b>Delivered:-</b><br>".date("m/d/y h:i a", strtotime($entity['delivered']));
						}*/
						elseif($entity['status'] == Entity::STATUS_ONHOLD ){
							    if (strtotime($entity['avail_pickup_date']) > 0)
							       $Date1 = "<b>1st avil:-</b><br>".date("m/d/y", strtotime($entity['avail_pickup_date']));
								if($entity['hold_date']!="")   
							       $Date2 = "<b>Hold:-</b><br>".date("m/d/y", strtotime($entity['hold_date']));
							    
						}
						elseif($entity['status'] == Entity::STATUS_ARCHIVED){
							    if (strtotime($entity['avail_pickup_date']) > 0)
							       $Date1 = "<b>1st avil:-</b><br>".date("m/d/y", strtotime($entity['avail_pickup_date']));
								if($entity['archived']!="")   
							       $Date2 = "<b>Cancelled:-</b><br>".date("m/d/y", strtotime($entity['archived']));
							    
						}
						
                        		 
				?>
           <td valign="top" align="center" bgcolor="<?= $bgcolor ?>">

                    <?php echo $Date1;?>

                </td>

                <td valign="top" align="center" bgcolor="<?= $bgcolor ?>">    

                    <?php echo $Date2;?>

                </td>   
         <?php
		     }
            else
            {    
				if ($avail_title == Entity::TITLE_PICKUP_DELIVERY){ ?>

                <td valign="top" align="center" bgcolor="<?= $bgcolor ?>">

						<?php

                           if (strtotime($entity['load_date']) == 0) $abbr = "N/A";

                           else

                           {

                             $abbr = $entity['load_date_type'] > 0 ? $date_type_string[(int)$entity['load_date_type']] : "";

                             $abbr = $abbr . "<br />" . date("m/d/y", strtotime($entity['load_date']));

                           }

                        ?>

                         <?php echo $abbr;?>

                </td>

                <td valign="top" align="center" bgcolor="<?= $bgcolor ?>"> 

					<?php

                       if (strtotime($entity['delivery_date']) == 0) $abbr = "N/A";

                       else

                       {

                         $abbr = $entity['delivery_date_type'] > 0 ? $date_type_string[(int)$entity['delivery_date_type']] : "";

                         $abbr = $abbr . "<br />" . date("m/d/y", strtotime($entity['delivery_date']));

                       }

                    ?>   

                    <?php echo  $abbr;?>

                </td>    

                <?php }else{?>

                 <td valign="top" align="center" bgcolor="<?= $bgcolor ?>">

                 <?php

				    if (strtotime($entity['avail_pickup_date']) == 0) $avail_pickup_date = "";

					else

                    $avail_pickup_date = date("m/d/y", strtotime($entity['avail_pickup_date']));

				 ?>

                        <?php echo $avail_pickup_date;?>

                 </td>       

                <?php } ?>

            <?php if ($avail_title != Entity::TITLE_PICKUP_DELIVERY){?>

            <td valign="top" align="center" bgcolor="<?= $bgcolor ?>">

            <?php

			  if (strtotime($entity['posted']) == 0) $postDate = "";

			  else

               $postDate = date("m/d/y", strtotime($entity['posted']));

			?>

            <?php echo $postDate;?>

            </td>

            <?php } ?>

         <?php }?>   

            <td bgcolor="<?= $bgcolor ?>">

            <?php

			$optionStr = "";	

				if($entity['customer_balance_paid_by'] == Entity::WIRE_TRANSFER)

				  $optionStr = "Wire - Transfer";

				elseif($entity['customer_balance_paid_by'] == Entity::MONEY_ORDER)

				  $optionStr = "Money Order";

				elseif($entity['customer_balance_paid_by'] == Entity::CREDIT_CARD)

				  $optionStr = "Credit Card";

				elseif($entity['customer_balance_paid_by'] == Entity::PARSONAL_CHECK)

				  $optionStr = "Personal Check";

				elseif($entity['customer_balance_paid_by'] == Entity::COMPANY_CHECK)

				  $optionStr = "Company Check";

				elseif($entity['customer_balance_paid_by'] == Entity::ACH)

				  $optionStr = "ACH";

				else

				  $optionStr = "N/A";
				  
				if(in_array($entity['balance_paid_by'], array(14, 15 , 22 , 23)))   
		            $optionStr = "Invoice Carrier";

			?>

            <strong>Customer Paying Us By:</strong>  <font color="red"><?php print $optionStr;?></font>

            <br /><strong>Carrier Getting Paid By:</strong>  <font color="red"><?php print $Balance_Paid_By;?></font>

            <?php if($_SESSION['member']['access_payments']==1){?>

             <br /><br /><a href="javascript:void(0);" onclick="process_payment(<?php print $entity['entityid'];?>);">Process Payment</a>    

             <!--br /><a href="javascript:void(0);" onclick="refund_payment(<?php //print $entity['entityid'];?>);">Refund</a-->      

                <?php }?>   

            </td>

            

             <?php 

						   

						    if ($entity['type'] == Entity::TYPE_ORDER){

								

						       //$isColor = $entity->isPaidOffColor();

							   

							   $Dcolor = "black";

							   $Ccolor = "black";

							   $Tcolor = "black";

							   


							   if($isColor['carrier']==1)

								$Ccolor = "green";

							   elseif($isColor['carrier']==2){	

							    $Ccolor = "red";
								$totalCarrier += $entity['total_carrier_pay'];
							   }

							   

							   if($isColor['deposit']==1)

								$Dcolor = "green";	

							   elseif($isColor['deposit']==2){	

							    $Dcolor = "red";
								$totalDeposit += ($entity['total_tariff'] - $entity['total_carrier_pay']);
							   }
								

							   if($isColor['total']==1)

								$Tcolor = "green";	

							   elseif($isColor['total']==2){	

							    $Tcolor = "red";	
                                $totalTariff += $entity['total_tariff'];
							   }

							 }	

							 

							

							 

							?>

            <td class="grid-body-right" bgcolor="<?= $bgcolor ?>">

                <table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">

                    <tr>

                        <td width="10"><img src="<?= SITE_IN ?>images/icons/dollar.png" alt="Total Tariff"

                                            title="Total Tariff" width="16" height="16"/></td>

                        <td style="white-space: nowrap;"><span class='<?= $Tcolor;?>'><?= ("$ " . number_format((float)$entity['total_tariff'], 2, ".", ",")) ?></span></td>

                    </tr>

                    <tr>

                        <td width="10">

                           <?php 

						  // if($this->status == Entity::STATUS_POSTED){

							   //$entityManager = new EntityManager($this->daffny->DB);

						       //$rowCarrier =  $entityManager->getCarrierDataCount($entity['entityid']);

							   ?>

                               <img src="<?= SITE_IN ?>images/icons/truck.png" alt="<?php print $rowCarrier;?> Carriers Found" title="<?php print $rowCarrier;?> Carriers Found" width="16" height="16" onclick="getCarrierData(<?php print $entity['entityid'];?>,'<?php print $entity['Origincity'];?>','<?php print $entity['Originstate'];?>','<?php print $entity['Originzip'];?>','<?php print $entity['Destinationcity'];?>','<?php print $entity['Destinationstate'];?>','<?php print $entity['Destinationzip'];?>');"/>

                               <?php

						  // }

						   /*

						   else

						   {

							 $dispatchSheetManager = new DispatchSheetManager($this->daffny->DB);

							 $dispatchSheet = new DispatchSheet($this->daffny->DB);

							 $dsId = $dispatchSheetManager->getDispatchSheetByOrderId($entity['entityid']);

								//$this->memberObjects['dispatchSheet'] = (is_null($dsId)) ? null : 

							 

							 if (!is_null($dsId)) { 

							   $ds = $dispatchSheet->load($dsId);

							   

							 ?>

		                        <span class="viewsample like-link" style="border-bottom: none">

			                        <img src="<?= SITE_IN ?>images/icons/truck.png" alt="Carrier Pay" title="Carrier Pay" width="16" height="16"/>

		                        </span>

		                        <div class="sample-info carrier-info-popup">

			                        <strong>Company Name: </strong><?= $ds->carrier_company_name ?><br/>

			                        <strong>Contact Name: </strong><?= $ds->carrier_contact_name ?><br/>

			                        <strong>Phone 1: </strong><?= $ds->carrier_phone_1 ?><br/>

			                        <strong>Phone 2: </strong><?= $ds->carrier_phone_2 ?><br/>

			                        <strong>Fax: </strong><?= $ds->carrier_fax ?><br/>

			                        <strong>Email: </strong><?= $ds->carrier_email ?><br/>

			                        <?php 

									//if ($entity['carrier_id'] != '0')

									//{

										// $carrier = new Account($this->daffny->DB);			

				                        // $carrierObj = $carrier->load($entity['carrier_id']);

										 //if ($carrierObj instanceof Account) { ?>

				                                 //<strong>Hours of Operation: </strong><?= $carrierObj->hours_of_operation ?><br/>

			                              <?php //} 

									//}

									//$carrier = $entity->getCarrier();

                                    ?>

			                        

			                        <strong>Driver Name: </strong><?= $ds->carrier_driver_name ?><br/>

			                        <strong>Driver Phone: </strong><?= $ds->carrier_driver_phone ?><br/>

		                        </div>

	                        <?php } else { ?>

	                        <img src="<?= SITE_IN ?>images/icons/truck.png" alt="Carrier Pay" title="Carrier Pay" width="16" height="16"/>

	                        <?php } ?>

                           <?php }*/ ?>  

                        </td>

                        <td style="white-space: nowrap;"><span class='<?= $Ccolor;?>'><?= ("$ " . number_format((float)$entity['total_carrier_pay'], 2, ".", ",")) ?></span><br/></td>

                    </tr>

                    <tr>

                        <td width="10"><img src="<?= SITE_IN ?>images/icons/person.png" alt="Deposit    "

                                            title="Deposit" width="16" height="16"/></td>

                        <td style="white-space: nowrap;"><span class='<?= $Dcolor;?>'><?= ("$ " . number_format((float)($entity['total_tariff'] - $entity['total_carrier_pay']), 2, ".", ",")) ?></span></td>

                    </tr>

                    <?php /* if($isValue['totalPayValue']>0){?>

                    <tr>

                        <td width="10"><img src="<?= SITE_IN ?>images/icons/circle-black.png" alt="Payment Recieved"

                                            title="Payment Recieved" width="16" height="16"/></td>

                        <td style="white-space: nowrap;"><span class='black' title="Payment Recieved"><b><?= ("$ " . number_format((float)$isValue['totalPayValue'], 2, ".", ",")) ?></b></span></td>

                    </tr>

                    <?php }*/?>

                    

                </table>

            </td>

        </tr>
   <?php }	?>

    <?php endforeach; ?>

    

    <?php

	        $searchCount = count($searchData);

			if($searchCount>0){

			   $_SESSION['searchData'] = $searchData;

			   $_SESSION['searchCount'] = $searchCount;

			   $_SESSION['searchShowCount'] = 0;

			}

	?>

  

    </tbody>
</table>


<p style="margin-top: 10px !important"> Dispatched Today</p>
<?php if (($_GET['orders'] == "issues") ||($_GET['orders'] == "searchIssue")){ ?>
<table cellspacing="0" cellpadding="0" width="100%" class="control-bar mt-4">
 <tr>
       <td colspan="6" style="height=10px;"></td>   
</tr>	   
 <tr>
       <td width="20%" bgcolor="#BCC8E4" align="center"><b>30 Days in Issues</b></td>   	   
       <td width="10%" >&nbsp;</td>  
       <td width="20%" bgcolor="#ECCEF5" align="center"><b>45 Days in Issues</b></td> 
       <td width="10%" >&nbsp;</td>
       <td width="20%" bgcolor="#FFC6BC" align="center"><b>60 Days in Issues</b></td>  
       <td width="10%" >&nbsp;</td>
       <td width="20%" bgcolor="#cccccc" align="center"><b>90 Days in Issues</b></td>  
       <td width="10%" >&nbsp;</td>
</tr>
<tr>
       <td colspan="6" style="height=10px;"></td>   
</tr>	   
</table>
<?php } ?>
<table cellspacing="0" cellpadding="0" width="50%" class="control-bar">

    <tr>
      
        <?php if ($this->status != Entity::STATUS_ARCHIVED) { ?>

            

            <td valign="top">

			<?php print functionButton('Reassign Order(s)', 'reassignOrdersDialog()','',' btn-sm btn_dark_green'); ?>

			<?php  //print functionButton('Reassign Order', 'reassignOrders(\'bottom\')'); ?></td>

        <? } ?>

        <?php if (in_array($_GET['orders'], array("", "posted")) && $_SESSION['member']['access_dispatch']) { ?>

            <!--td  valign="top"><?php //print functionButton('Dispatch', 'dispatch()'); ?></td-->

            <td  valign="top"><?php print functionButton('Dispatch', 'selectCarrierNewDispatch()','','btn_dark_blue btn-sm'); ?></td>

        <?php } ?>



        <td valign="top">

            <?php if ($this->status == Entity::STATUS_ACTIVE) : ?>

                <?= functionButton('Place On Hold', 'placeOnHoldOrders()','','btn_dark_green') ?>

            <?php elseif ($this->status == Entity::STATUS_ONHOLD) : ?>

                <?= functionButton('Restore', 'restoreOrders()','','btn_dark_green btn-sm') ?>

            <?php endif; ?>

        </td>

        <?php if ($_GET['orders'] != "archived") { ?>

            <td valign="top"><?= functionButton('Cancel Order', 'cancelOrders()', '', 'btn-dark btn-sm') ?></td>

        <?php } ?>

        <?php if ($_GET['orders'] == "") { ?>

            <td valign="top"><?= functionButton('Post to FB', 'postToFB()','',' btn-sm btn_light_blue') ?></td>

        <?php } elseif ($_GET['orders'] == "dispatched") { ?>

            <td valign="top"><?php print  functionButtonDate(Entity::STATUS_PICKEDUP,'Picked Up Date', 'setPickedUpStatusAndDate',false,'pickup_button1','yy-mm-dd');?></td>

        <?php } elseif ($_GET['orders'] == "pickedup") { ?>

            <td valign="top"><?php print  functionButtonDate(Entity::STATUS_DELIVERED,'Delivered Date', 'setPickedUpStatusAndDate',false,'delivered_button1','yy-mm-dd');?></td>

        <?php } elseif ($_GET['orders'] == 'posted') { ?>

            <td valign="top"><?= functionButton('Unpost', 'unpostFromFB()') ?></td>

        <?php } ?>

    </tr>

</table>
<?php
$totalTariff = "$ " . number_format((float)($totalTariff), 2, ".", ",");
$totalCarrier = "$ " . number_format((float)($totalCarrier), 2, ".", ",");
$totalDeposit = "$ " . number_format((float)($totalDeposit), 2, ".", ",");
?>
@pager@
<script language="javascript" type="text/javascript">


$("#tariffDataTotal").html('<?php print $totalTariff;?>');
$("#carrierDataTotal").html('<?php print $totalCarrier;?>');
$("#depositDataTotal").html('<?php print $totalDeposit;?>');
</script>


<script type="text/javascript">
    $(document).ready(function() {
   $('#issuetype').DataTable({
       "lengthChange": false,
       "paging": false,
       "bInfo" : false,
       'drawCallback': function (oSettings) {
           $('#issuetype_wrapper').children('.row:first').children('.col-md-6:first').html('<div class="form-group row" style="margin-left:0;"><label class="col-form-label">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
           $('#issuetype_wrapper').children('.row:last').children('.col-md-5').html($('.pager').clone()).addClass('text-left');
           $('#issuetype_wrapper').children('.row:last').children('.col-md-7').html($('.table_b ').html()).addClass('text-right');
           $('.pages-div-custom').remove();
           
      }
   });


} );
</script>

<script>
  $( function() {
    $( "#dispatch_pickup_date" ).datepicker();
  } );
  </script>