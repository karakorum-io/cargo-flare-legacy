

<!--div id="acc_search_dialog">
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="form-table">
		<tr>
			<td width="100%"><input type="text" name="app_search_text" id="acc_search_string" style="width:98%" class="form-box-textfield"/></td>
			<td><?=functionButton('Search', "accountSearch()")?></td>
            <td>&nbsp;&nbsp;
                               
                               <span class="like-link multi-vehicles"><b>[?]</b></span>
                               <div class="search_help">
                               <p> 
                                 Company<br /> 
                                 Phone Number1 <br />
                                 Phone Number2<br /> 
                                 Contact Name1<br /> 
                                 Contact Name2<br /> 
                                 </p>
                               </div></td>
		</tr>
		<tr>
			<td colspan="3">
				<ul id="acc_search_result"></ul>
			</td>
		</tr>
	</table>
</div-->
<div id="maildivnew">
    <table cellspacing="2" cellpadding="0" border="0">
        <tr>
           <td colspan="2" style="padding-left:20px;">
             <table cellspacing="2" cellpadding="0" border="0">
             <tbody id="skill_id">
               <tr>
                <td>@mail_to_new@</td>
                <td >
                              <div style="float:left;"><ul class="ul-tags"><li><a href="javascript:void(0);" style="font-size:12px; font-family:Verdana, Geneva, sans-serif; font-weight:bold; color:#000;" onclick="AddEmailExtra();"><b>Add More</b></a></li></ul></div>
                </td>
               </tr>
               
             </tbody>
           </table>
          </td>      
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
    
</div>
<?php 
/*
if(is_array($this->payentities) && sizeof($this->payentities)>0){
	 foreach ($this->payentities as $i => $entity)
	 {
		 if($entity->status == Entity::STATUS_POSTED || 
			$entity->status == Entity::STATUS_DISPATCHED ||
			$entity->status == Entity::STATUS_PICKEDUP ||
			$entity->status == Entity::STATUS_DELIVERED
			)
		 $tempPayEntities[] = $entity;
	 }
}
*/
?>
<!-- sample it skill row start-->
<table id="testData1" style="display:none; visibility:hidden;">
        <tr>
            <td>&nbsp;</td><td  colspan="2"><input type="text" name="email_extra" id="email_extra" value="" class="form-box-textfield"  maxlength="100" tooltip='E-mail' style="width:280px;"></td>
        </tr>
</table>


<div id="reassigndddddCompanyDiv">
    <table cellspacing="2" cellpadding="0" border="0">
        <tr>
            <td valign="top">
                <select class="form-box-combobox" id="company_members">
                   <option value=""><?php print "Select One"; ?></option>
                    <?php foreach($this->company_members as $member) : ?>
                        <option value="<?= $member->id ?>"><?= $member->contactname ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
 </table>
</div>
<script type="text/javascript">


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
function AddItSkillRow(testData ,skill_id ,skillCount){
	var skillCountObj=document.getElementById(skillCount);
	//var e=parseInt(skillCountObj.value);
	
	var skillCountValue=1;
	var skillIDObj=document.getElementById(skill_id);
	var testDataRows=document.getElementById(testData).getElementsByTagName("tr");
	var testDataRowsClone=testDataRows[0].cloneNode(true);
	testDataRowsClone.getElementsByTagName("input")[0].id="skill"+skillCountValue;
	skillIDObj.appendChild(testDataRowsClone); 
	
	skillCountObj.value=skillCountValue+1;
	return false
}

    function printSelectedOrderForm() {
		
		if ($(".order-checkbox:checked").size() == 0) {
		    alert("Order not selected");
		    return;
	    }
	   var order_id = $(".order-checkbox:checked").val();
		
        form_id = $("#form_templates").val();
        if (form_id == "") {
            alert("Please choose form template");
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

        if ($(".order-checkbox:checked").size() == 0) {
		    alert("Order not selected");
		    return;
	    }
	   var order_id = $(".order-checkbox:checked").val();
	   
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
                        action: "emailOrder",
                        form_id: form_id,
                        entity_id: order_id
                    },
                    success: function (res) {
                        if (res.success) {
                            alert("Email was successfully sent");
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
    }	
	 
	 
$("#maildivnew").dialog({
	modal: true,
	width: 400,
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
	
	
function reassignOrdersDialog()
{
	 if ($(".order-checkbox:checked").size() == 0) 
		{            
		   alert("Order not selected");            
		     return;        
		} 
	  $("#reassignCompanyDiv").dialog("open");
}
	
$("#reassignCompanyDiv").dialog({
	modal: true,
	width: 300,
	height: 140,
	title: "Reassign Order",
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
	
function copyOrders()
{
	if ($(".order-checkbox:checked").size() == 0) {
		    alert("Order not selected");
		    return;
	    }
	   var order_id = $(".order-checkbox:checked").val();
	   
	location.href =  "<?= SITE_IN ?>application/orders/duplicateOrder/id/"+order_id;
}
	 
function cancelDispatchSheet() {
	
     if ($(".order-checkbox:checked").length == 0) {
		    alert("Order not selected");
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

				//document.location.reload();
				swal.fire("Order successfully undispatched.");

			} else {

				swal.fire("Can't cancel Dispatch Sheet");

			}

		}

	});

}



</script>

<div style="display:none" id="notes">notes</div>
<br/>

<?php
    $avail_title = Entity::TITLE_FIRST_AVAIL;
    if (isset($_GET['orders'])){
        if (in_array($_GET['orders'], array("notsigned","dispatched","pickedup","delivered","issues","archived"))){
            $avail_title = Entity::TITLE_PICKUP_DELIVERY;
        }
    }
	
	
			
?>

<!-- ================Orders=======================-->
<br /><br />

<table cellspacing="0" cellpadding="0" border="0" class="grid" width="100%">
      <tr>
         <td>
         <h2>Number of Orders: <?php print count($this->entities['order']);?></h2>
         </td>
       </tr>
 </table>   
 <br /> 

<script language="javascript" type="text/javascript">
function searchAll(){
	document.form_search.submit();
}
</script>
<table cellspacing="0" cellpadding="0" width="100%" class="control-bar">
    <tr>
        <td>
        <form name="form_search" id="form_search" action="/application/orders/searchall" method="post">
           @orders_search_combo@
           <?php 
		      $search_type = $_POST['search_type'];
			  $search_type_size = sizeof($_POST['search_type']);
		   if(is_array($search_type) && $search_type_size >0){ 
		       for($j=0;$j<$search_type_size;$j++){
		   ?>
              <input type="hidden" name="search_type[]" value="<?php print $search_type[$j];?>" />
           <?php }}?>
            <input type="hidden" name="search_string" value="<?php print $_POST['search_string'];?>" />
        </form>
        </td>
        <td width="100%">&nbsp;</td>
		  <?php if ($_GET['orders'] == "issues") { ?>
           <td>
           <form id="issue_form" action="/application/orders/searchIssue" method="post">
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
               <td> <?= functionButton('Restore', 'restoreOrders()','','btn btn-sm btn_bright_blue') ?></td>
            <?php endif; ?>
         <?php if ($this->status != Entity::STATUS_ARCHIVED) { ?>
        
            <?php if (in_array($_GET['orders'], array("", "posted")) && $_SESSION['member']['access_dispatch']) { ?>
            <td valign="top"><?= functionButton('Dispatch', 'dispatch()','','btn  btn-sm btn_bright_blue') ?></td>
        <?php } ?>
        
            <td valign="top">
			<?php print functionButton('Reassign Order', 'reassignOrdersDialog()'); ?>
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
        
        <td valign="top"><?php print functionButton('Copy Order', 'copyOrders()'); ?></td>
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
            <td valign="top"><?= functionButton('Cancel', 'cancelOrders()') ?></td>
        <?php } ?>
    </tr>
</table>
<table cellspacing="0" cellpadding="0" border="0" class="grid" width="100%">
    <tbody>
    <tr class="grid-head">
        <td class="grid-head-left">
            <?php if (isset($this->order)) : ?>
                <?= $this->order->getTitle("id", "ID") ?>
            <?php else : ?>ID<?php endif; ?>
        </td>
        <td>
		    <?php          if($this->status == Entity::STATUS_ARCHIVED){
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
                                     print $this->order->getTitle("created", "Created");
								else
                                     print "Created";								
 							  }	
			      ?>
            
        </td>
        <?php if ($_GET['orders'] != 'all') { ?>
            <td>Notes</td>
        <?php } else { ?>
            <td>Broker</td>
        <?php } ?>
        <td>
	        <?php if (isset($this->order)) : ?>
		        <?= $this->order->getTitle("shipper", "Shipper") ?>
	        <?php else : ?>Shipper<?php endif; ?>
        </td>
        <td>Vehicle</td>
        <td width="15%">
	        <?php if (isset($this->order)) : ?>
		        <?= $this->order->getTitle("origin", "Origin") ?>
	        <?php else : ?>Origin<?php endif; ?>
	        /
	        <?php if (isset($this->order)) : ?>
		        <?= $this->order->getTitle("destination", "Destination") ?>
	        <?php else : ?>Destinations<?php endif; ?>
        </td>
        <?php if ($avail_title == Entity::TITLE_PICKUP_DELIVERY):?>
            <td>
              <?= $avail_title; ?>
            </td>
            <td>
               <?php print "Delivery"; ?>
            </td>
        <?php else : ?>
            <td><?= $avail_title; ?><?php //print $this->order->getTitle("avail", $avail_title) ?>
        <?php endif; ?>
        <?php if ($avail_title != Entity::TITLE_PICKUP_DELIVERY){?>
        <td>
	        <?php if (isset($this->order)) : ?>
		        <?= $this->order->getTitle("posted", "Posted") ?>
	        <?php else : ?>Posted<?php endif; ?>
        </td>
        <?php } ?>
        <td >
	        Payment Options
        </td>
        <td class="grid-head-right">
	        <?php if (isset($this->order)) : ?>
		        <?= $this->order->getTitle("tariff", "Tariff") ?>
	        <?php else : ?>tariff<?php endif; ?>
        </td>
    </tr>
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
	
	if (count($this->entities['order']) == 0): ?>
        <tr class="grid-body">
            <td colspan="9" align="center" class="grid-body-left grid-body-right"><i>No records</i></td>
        </tr>
    <?php endif; ?>
    <?php 
	 $issue_type = $_POST['issue_type'];
	
	foreach ($this->entities['order'] as $i => $entity) : /* @var Entity $entity */ 
	
	      $i++;
		  $bgcolor = "#ffffff";
		  if($i%2==0)
		    $bgcolor = "#f4f4f4";
			
		/*
	       $paymentDone = $entity->getPayments();
		   
		   $paymentDoneSize = count($paymentDone);
		   
		   
		 
	     $isColor = $entity->isPaidOffColor();
		 $isValue = $entity->isPaidOffValue();
		 
		$shipper = $entity->getShipper();
         */     
		 
		 $paymentManager = new PaymentManager($this->daffny->DB);
		$owe = 0;
		
		$isColor = array(
                'total' => 0,
                'carrier' => 0,
                'deposit' => 0
            );
		
		switch ($entity['balance_paid_by']) {
			case Entity::BALANCE_COP_TO_CARRIER_CASH:
			case Entity::BALANCE_COP_TO_CARRIER_CHECK:
			case Entity::BALANCE_COP_TO_CARRIER_COMCHECK:
			case Entity::BALANCE_COP_TO_CARRIER_QUICKPAY:
			case Entity::BALANCE_COD_TO_CARRIER_CASH:
			case Entity::BALANCE_COD_TO_CARRIER_CHECK:
			case Entity::BALANCE_COD_TO_CARRIER_COMCHECK:
			case Entity::BALANCE_COD_TO_CARRIER_QUICKPAY:
				$shipperPaid = $paymentManager->getFilteredPaymentsTotals($entity['id'], Payment::SBJ_SHIPPER, Payment::SBJ_COMPANY, false);
				
				$owe = ($entity['total_tariff_stored'] - $entity['carrier_pay_stored']) - $shipperPaid;
				if($owe <=0)
				  $isColor['deposit'] = 1;
				else
				  $isColor['deposit'] = 2;
				
				  
				break;
			case Entity::BALANCE_COMPANY_OWES_CARRIER_CASH:
			case Entity::BALANCE_COMPANY_OWES_CARRIER_CHECK:
			case Entity::BALANCE_COMPANY_OWES_CARRIER_COMCHECK:
			case Entity::BALANCE_COMPANY_OWES_CARRIER_QUICKPAY:
			case Entity::BALANCE_COMPANY_OWES_CARRIER_ACH:
				$shipperPaid = $paymentManager->getFilteredPaymentsTotals($entity['id'], Payment::SBJ_SHIPPER, Payment::SBJ_COMPANY, false);
				$carrierPaid = $paymentManager->getFilteredPaymentsTotals($entity['id'], Payment::SBJ_COMPANY, Payment::SBJ_CARRIER, false);
				//print $this->getCost(false)."-----------".$carrierPaid;
				//$owe = $this->getCost(false) - $carrierPaid;
				
				$cost = $entity['carrier_pay_stored'] + $entity['pickup_terminal_fee'] + $entity['dropoff_terminal_fee'];
				/*if($cost==0)
				  $cost = $entity['carrier_pay_stored'] + $this->getPickupTerminalFee(false) + $this->getDropoffTerminalFee(false);
				else
				   $cost = $this->getCost(false);
				 */  
				   $owe = $cost - $carrierPaid;
				
				if($owe <=0)
				  $isColor['carrier'] = 1;
				else
				  $isColor['carrier'] = 2; 
				  
				$owe = ($entity['total_tariff_stored'] - $entity['carrier_pay_stored']) - $shipperPaid;
				
				if($owe <=0)
				  $isColor['deposit'] = 1;
				else
				  $isColor['deposit'] = 2;  
				  
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
				$carrierPaid = $paymentManager->getFilteredPaymentsTotals($entity['id'], Payment::SBJ_CARRIER, Payment::SBJ_COMPANY, false);
				
				$owe = ($entity['total_tariff_stored'] - $entity['carrier_pay_stored']) - $carrierPaid;
				
				if($owe <=0)
				  $isColor['deposit'] = 1; 
				else
				  $isColor['deposit'] = 2;

				break;
		}
	
	
	   /************************************************/
		
		$number = "";
        if (trim($entity['prefix']) != "") {
            $number .= $entity['prefix'] . "-";
        }
        $number .= $entity['number'];
		 
	?>
        <tr id="order_tr_<?= $entity['id'] ?>" class="grid-body<?= ($i == 0 ? " first-row" : "") ?>">
           <td align="center" class="grid-body-left" bgcolor="<?= $bgcolor ?>">
                <?php if ($_GET['orders'] != 'all') { ?>
                    <?php if (!$entity['readonly']) : ?>
                        <?php /*?><input type="radio" name="order_id" value="<?= $entity['id'] ?>" class="order-checkbox"/><br/><?php */?>
						<input type="checkbox" name="order_id" value="<?= $entity['id'] ?>" class="order-checkbox"/><br/>
                    <?php endif; ?>
                     <a href="<?= SITE_IN ?>application/orders/show/id/<?= $entity['id'] ?>"><div class="order_id"><?= $number ?></div></a>
					 
                    <a href="<?= SITE_IN ?>application/orders/history/id/<?= $entity['id'] ?>">History</a>
					  
					
                    <?php if (isset($_POST['search_string'])) : 
					    print "<br /><b>Status</b><br>";
		                  
						     if ($entity['status'] == Entity::STATUS_ACTIVE) 
							    print "Active";
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
                      <br/> <br/> <a href="<?= SITE_IN ?>application/orders/unarchived/id/<?= $entity['id'] ?>">UnArchive</a>
                      <?php endif; */?>
					
                <?php } else { ?>
                    <?= $number
                    ; ?>
                <?php } ?>
            </td>
            <td valign="top" style="white-space: nowrap;" bgcolor="<?= $bgcolor ?>">
           
        <?php          if($entity['status'] == Entity::STATUS_ARCHIVED){
							   // print $entity->getArchived("m/d/y h:i a");
							   print date("m/d/y h:i a", strtotime($entity['archived']));
			                }
						    elseif($entity['status'] == Entity::STATUS_DISPATCHED){
							    //print $entity->getDispatched("m/d/y h:i a");
								print date("m/d/y h:i a", strtotime($entity['dispatched']));
							}
							elseif($entity['status'] == Entity::STATUS_DELIVERED){
							   // print $entity->getDelivered();
								print (is_null($entity['delivered'])) ? "" : date("m/d/y h:i a", strtotime($entity['delivered']));
							}
						    elseif($entity['status'] == Entity::STATUS_POSTED)
							    print date("m/d/y h:i a", strtotime($entity['posted']));	
           					elseif($entity['status'] == Entity::STATUS_NOTSIGNED)
							    print date("m/d/y h:i a", strtotime($entity['not_signed']));	
							elseif($entity['status'] == Entity::STATUS_ISSUES)
							    print date("m/d/y h:i a", strtotime($entity['issue_date']));	
							elseif($entity['status'] == Entity::STATUS_ONHOLD)
							    print date("m/d/y h:i a", strtotime($entity['hold_date']));
                            elseif($entity['status'] == Entity::STATUS_PICKEDUP)
							    print date("m/d/y h:i a", strtotime($entity['actual_pickup_date']));								
							 else{
                               	//print $entity->getOrdered("m/d/y h:i a");	
								$tz = isset($_SESSION['timezone']) ? $_SESSION['timezone'] : 'America/New_York';
                                $date = new DateTime($entity['ordered'], new DateTimeZone($tz));
								print (is_null($entity['ordered'])) ? "" : gmdate("m/d/y h:i a", $date->getTimestamp());
							 }

 								
			      ?>
            
            <br />
            <?php if($entity['esigned']==1){
				$sql = "SELECT u.id,u.type,u.name_original
                  FROM app_entity_uploads au
                  LEFT JOIN app_uploads u ON au.upload_id = u.id
                 WHERE au.entity_id = '" . $entity['id'] . "'
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
                 WHERE au.entity_id = '" . $entity['id'] . "'
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
               <br>Assigned to:<br/> <strong><?= $entity['contactname'] ?></strong><br />
            </td>
            <td bgcolor="<?= $bgcolor ?>">
                <?php 
				
				if ($_GET['orders'] != 'all') { ?>
                    <?php 
					
					      $notes = new NoteManager($this->daffny->DB);
						  
						  //print "==========".$entity['id'];
						  $notesData = $notes->getNotesArrData($entity['id']);
					      
						 $countNewNotes = count($notesData[Note::TYPE_INTERNALNEW]);
						 $countInternalNotes = count($notesData[Note::TYPE_INTERNAL]) + $countNewNotes;
						 
					?>
                    <?= notesIcon($entity['id'], count($notesData[Note::TYPE_FROM]), Note::TYPE_FROM, $entity['status'] == Entity::STATUS_ARCHIVED) ?>
                    <?= notesIcon($entity['id'], count($notesData[Note::TYPE_TO]), Note::TYPE_TO, $entity['status'] == Entity::STATUS_ARCHIVED) ?>
                    <?= notesIcon($entity['id'], $countInternalNotes, Note::TYPE_INTERNAL, $entity['status'] == Entity::STATUS_ARCHIVED,$countNewNotes)
				    ?>
                    
                <?php  } else { ?>
                    <? print  $entity['companyname']; ?>
                <?php } ?>
            </td>
            
            <td bgcolor="<?= $bgcolor ?>">
                
                 <div class="shipper_name"><?= $entity['s_fname'] ?> <?= $entity['s_lname'] ?><br/></div>
                <?php if($entity['s_company']!=""){?><div class="shipper_company"><b><?= $entity['s_company']?></b><br /></div><?php }?>
                <?php if($entity['s_phone1']!=""){?><div class="shipper_number"><?= formatPhone($entity['s_phone1']) ?><br/></div><?php }?>
                <?php if($entity['s_email']!=""){?><a href="mailto:<?= $entity['s_email'] ?>"><div class="shipper_email"><?= $entity['s_email'] ?><br/></div></a><?php }?>
                <div class="shipper_referred"><?php if($entity['referred_by'] != ""){?>
				  Referred By <b><?= $entity['referred_by'] ?></b><br>
				<?php }?></div>
                
            </td>
            
            
           <td bgcolor="<?= $bgcolor ?>" >
            <?php
			
			$vehicleManager = new VehicleManager($this->daffny->DB);
		    $vehicles = $vehicleManager->getVehiclesArrData($entity['id'], $entity['type']);
			/*print "<pre>";
			print_r($vehicles);
			print "</pre>";
			*/
			?>
                <?php // $vehicles = $entity1->getVehicles();?>
                <?php //$source = $entity1->getSource(); ?>
                <?php if (count($vehicles) == 0) { ?>
                <?php }elseif (count($vehicles) == 1) { ?>
                    <?php $vehicle = $vehicles[0]; ?>
                    <?= $vehicle['make']; ?> <?= $vehicle['model']; ?><br/>
                    <?= $vehicle['year']; ?> <?= $vehicle['type']; ?>&nbsp;<?= imageLink($vehicle['year'] . " " . $vehicle['make'] . " " . $vehicle['model'] . " " . $vehicle['type']) ?>
                    <br/>
                <?php }else { ?>
                    <span class="like-link multi-vehicles">Multiple Vehicles<b><span style="color:#000000;">(<?php print count($vehicles);?>)</span></b></span>
                    <!--div class="vehicles-info">
                        <?php foreach ($vehicles as $key => $vehicle) : ?>
                            <div <?= ($key % 2) ? 'style="background-color: #161616;padding: 5px;"' : 'style="background-color: #000;padding: 5px;"' ?>>
                                <p><?= $vehicle->make ?> <?= $vehicle->model ?><?php if($vehicle->inop){?>(<span style="color:red;weight:bold;"><?= "Inop" ?></span>)<?php }?></p>
                                <?= $vehicle->year ?> <?= $vehicle->type ?>
                                &nbsp;<?=imageLink($vehicle->year . " " . $vehicle->make . " " . $vehicle->model . " " . $vehicle->type)?>
                                <br/>
                            </div>
                        <?php endforeach; ?>
                    </div-->
                    <div class="vehicles-info">
					<table width="100%"   cellpadding="0" cellspacing="1">
                         <tr>
                             <td  style="padding:3px;"><b><p>Year</p></b></td>
                             <td  style="padding:3px;"><b><p><?= Make ?></p></b></td>
							 <td  style="padding:3px;"><b><p><?= Model ?></p></b></td>
                             <td  style="padding:3px;"><b><p><?= Type ?></p></b></td> 
							 <td  style="padding:3px;"><b><p><?= Vin# ?></p></b></td>
                             <td  style="padding:3px;"><b><p><?= Inop ?></p></b></td>
						  </tr>
                        <?php foreach ($vehicles as $key => $vehicle) : ?>
                            <tr>
                             <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle['year'] ?></td>
                             <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle['make'] ?></td>
							 <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle['model'] ?></td> 
                             <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle['type'] ?></td>
                             <td bgcolor="#ffffff" style="padding:3px;"> <?php  print $vehicle['vin'] ?></td>
                             <td bgcolor="#ffffff" style="padding-left:5px;"> <?php  print $vehicle['inop']==0?"No":"Yes"; ?></td>
                           </tr>
                        <?php endforeach; ?>
						</table>
                    </div>
                    <br/>
                <?php  } ?>
                <span style="color:red;weight:bold;"><?php print ($entity['ship_via'] != 0) ? $ship_via_string[$entity['ship_via']] : ""; ?></span><br/>
                <strong>Source: </strong><?php print "not available"; ?>
            </td>
            <?php
			$o_link = "http://maps.google.com/maps?q=" . urlencode($entity['o_city'] . ",+" . $entity['o_state']);
			$o_formatted = trim($entity['o_city'].', '.$entity['o_state'].' '.$entity['o_zip'], ", ");
			
			$d_link = "http://maps.google.com/maps?q=" . urlencode($entity['d_city'] . ",+" . $entity['d_state']);
			$d_formatted = trim($entity['d_city'].', '.$entity['d_state'].' '.$entity['d_zip'], ", ");
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
                <span class="like-link" onclick="mapIt(<?= $entity['id'] ?>);">Map it</span>
            </td>
            
            <?php
			             $Balance_Paid_By = "";
						   if(in_array($entity['balance_paid_by'], array(2, 3 , 16 , 17)))   
							   $Balance_Paid_By = "COD";
							
							if(in_array($entity['balance_paid_by'], array(8, 9 , 18 , 19)))   
							   $Balance_Paid_By = "COP";
							
							if(in_array($entity['balance_paid_by'], array(12, 13 , 20 , 21)))   
							   $Balance_Paid_By = "Billing";
							 
							 if(in_array($entity['balance_paid_by'], array(14, 15 , 22 , 23)))   
							   $Balance_Paid_By = "Billing";
			?>
            
               <?php if ($avail_title == Entity::TITLE_PICKUP_DELIVERY){ ?>
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
			?>
            <strong>Payment Method:</strong>  <font color="red"><?php print $optionStr;?></font>
            <br /><strong>Carrier Paid By:</strong>  <font color="red"><?php print $Balance_Paid_By;?></font>
            <?php if($_SESSION['member']['access_payments']==1){?>
             <br /><br /><a href="javascript:void(0);" onclick="process_payment(<?php print $entity['id'];?>);">Process Payment</a>    
             <!--br /><a href="javascript:void(0);" onclick="refund_payment(<?php //print $entity['id'];?>);">Refund</a-->      
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
            <td class="grid-body-right" bgcolor="<?= $bgcolor ?>">
                <table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
                    <tr>
                        <td width="10"><img src="<?= SITE_IN ?>images/icons/dollar.png" alt="Total Tariff"
                                            title="Total Tariff" width="16" height="16"/></td>
                        <td style="white-space: nowrap;"><span class='<?= $Tcolor;?>'><?= ("$ " . number_format((float)$entity['total_tariff_stored'], 2, ".", ",")) ?></span></td>
                    </tr>
                    <tr>
                        <td width="10">
                           <?php 
							 $dispatchSheetManager = new DispatchSheetManager($this->daffny->DB);
							 $dispatchSheet = new DispatchSheet($this->daffny->DB);
							 $dsId = $dispatchSheetManager->getDispatchSheetByOrderId($entity['id']);
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
									/*if ($entity['carrier_id'] != '0')
									{
										 $carrier = new Account($this->daffny->DB);			
				                         $carrierObj = $carrier->load($entity['carrier_id']);
										 if ($carrierObj instanceof Account) { ?>
				                                 <strong>Hours of Operation: </strong><?= $carrierObj->hours_of_operation ?><br/>
			                              <?php } 
									}*/
									//$carrier = $entity->getCarrier();
                                    ?>
			                        
			                        <strong>Driver Name: </strong><?= $ds->carrier_driver_name ?><br/>
			                        <strong>Driver Phone: </strong><?= $ds->carrier_driver_phone ?><br/>
		                        </div>
	                        <?php } else { ?>
	                        <img src="<?= SITE_IN ?>images/icons/truck.png" alt="Carrier Pay" title="Carrier Pay" width="16" height="16"/>
	                        <?php } ?>
                        </td>
                        <td style="white-space: nowrap;"><span class='<?= $Ccolor;?>'><?= ("$ " . number_format((float)$entity['carrier_pay_stored'], 2, ".", ",")) ?></span><br/></td>
                    </tr>
                    <tr>
                        <td width="10"><img src="<?= SITE_IN ?>images/icons/person.png" alt="Deposit    "
                                            title="Deposit" width="16" height="16"/></td>
                        <td style="white-space: nowrap;"><span class='<?= $Dcolor;?>'><?= ("$ " . number_format((float)($entity['total_tariff_stored'] - $entity['carrier_pay_stored']), 2, ".", ",")) ?></span></td>
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
    <?php endforeach; ?>

    </tbody>
</table>

<?php
if(is_array($this->tempPayEntities) && sizeof($this->tempPayEntities)>0)
		{
?>			
<script>
$("#order_payment_dialog").dialog({
				title: "Payment Required",
				width: 400,
				modal: true,
				resizable: false,
				draggable: true
			}).dialog('open');
</script>
<?php }?>



<!-- ================Quotes=======================-->
<br /><br />
<?php if (count($this->entities['quote']) > 0){ ?>

<div id="maildivnewQuotes">
    <table cellspacing="2" cellpadding="0" border="0">
        <tr>
            <td>@mail_to_new_quote@</td>
        </tr>
        <tr>
            <td>@mail_subject_new_quote@</td>
        </tr>
        <tr>
            <td>@mail_body_new_quote@</td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;<input type="hidden" name="form_id" id="form_id"  value=""/>
            <input type="hidden" name="entity_id" id="entity_id"  value=""/></td>
        </tr>
        
    </table>
</div>
<div id="reassignCompanyDivQuotes">
    <table cellspacing="2" cellpadding="0" border="0">
        <tr>
            <td valign="top">
                <select class="form-box-combobox" id="company_members_quotes">
                   <option value=""><?php print "Select One"; ?></option>
                    <?php foreach($this->company_members as $member) : ?>
                        <option value="<?= $member->id ?>"><?= $member->contactname ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
 </table>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$("#followup_when").datepicker({
			minDate: '+1'
		});
	});
	function sendFollowUp() {
		if ($(".entity-checkbox:checked").size() == 0) {
			alert("You have no selected quotes.");
			return;
		}
		if ($("#followup_when").val() == "") {
			alert("You must enter follow-up date.");
			$("#followup_when").focus();
			return;
		}
		var quote_ids = [];
		$(".entity-checkbox:checked").each(function(){
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
					alert("Can't save follow-up. Try again later, plaese");
				} else {
					alert("Follow-up saved.");
				}
			},
			error: function(response) {
				alert("Can't save follow-up. Try again later, plaese");
			}
		});
	}

function emailSelectedQuoteForm() {

            
		if ($(".entity-checkbox:checked").size() == 0) {
           alert("You have no selected items.");
        } else {
			var entity_ids = [];
			$(".entity-checkbox:checked").each(function(){
				entity_ids.push($(this).val());
			});      
	   
	   
        form_id = $("#email_templates_quotes").val();
		
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
                    success: function (res) {
                        if (res.success) {
                            alert("Email was successfully sent");
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
    }	
}
	
function reassignQuoteDialog()
{
	  if ($(".entity-checkbox:checked").size() == 0) 
		{            
		   alert("Quote not selected");            
		     return;        
		} 
	  $("#reassignCompanyDivQuotes").dialog("open");
}
	
$("#reassignCompanyDivQuotes").dialog({
	modal: true,
	width: 300,
	height: 140,
	title: "Reassign Quote",
	hide: 'fade',
	resizable: false,
	draggable: false,
	autoOpen: false,
	buttons: {
		"Submit": function () {
			var member_id = $("#company_members_quotes").val();	
			reassignQuotes(member_id);
		},
		"Cancel": function () {
			$(this).dialog("close");
		}
	}
});	 
	
function reassignQuotes(member) 
{		

        var member_id = 0;		
        member_id = member;		
		if ( member_id == 0 ) 
		{			
		  alert("You must select member to assign");			
		  return;		
		}        
		if ($(".entity-checkbox:checked").size() == 0) {
           alert("You have no selected items.");
        } else {
			var entity_ids = [];
			$(".entity-checkbox:checked").each(function(){
				entity_ids.push($(this).val());
			});      
		           
		$.ajax({            
			   type: 'POST',            
			   url: '<?= SITE_IN ?>application/ajax/entities.php',            
			   dataType: "json",            
			   data: {                
			     action: 'reassign',                
				 assign_id: member_id,                
				 entity_ids: entity_ids.join(',')            
				 },            
				 success: function(response) 
				 {               
				    if (response.success) {                    
					    window.location.reload();               
						} else {                   
						  alert("Reassign failed. Try again later, please.");                
						  }            
					},           
					error: function(response) {                
					   alert("Reassign failed. Try again later, please.");            
					   }        
					   });	
		}	
}

function printSelectedQuoteForm() {
		
		if ($(".entity-checkbox:checked").size() == 0) {
		    alert("Quote not selected");
		    return;
	    }
		
		if ($(".entity-checkbox:checked").size() > 1) {
		    alert("Please select one quote");
		    return;
	    }
	   var quote_id = $(".entity-checkbox:checked").val();
		
        form_id = $("#form_templates_quotes").val();
        if (form_id == "") {
            alert("Please choose form template");
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
	
	
$("#maildivnewQuotes").dialog({
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
					action: "emailQuoteNewSend",
					form_id: $('#form_id').val(),
					entity_id: $('#entity_id').val(),
					mail_to: $('#mail_to_new_quote').val(),
					mail_subject: $('#mail_subject_new_quote').val(),
					mail_body: $('#mail_body_new_quote').val()
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
						$("#maildivnewQuotes").dialog("close");
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


function emailSelectedQuoteFormNew() {
	
	   if ($(".entity-checkbox:checked").size() == 0) {
           alert("You have no selected items.");
		   return;
        } 
		
		if ($(".entity-checkbox:checked").size() >1) {
           alert("Select only one quote.");
		   return;
        }
		
		/*else {
			var entity_ids = [];
			$(".entity-checkbox:checked").each(function(){
				entity_ids.push($(this).val());
			});
		}*/
		
		var entity_ids = $(".entity-checkbox:checked").val();
		
        form_id = $("#email_templates_quotes").val();
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
                        entity_id: entity_ids
                    },
                    success: function (res) {
                        if (res.success) {
                            
							
							 $("#form_id").val(form_id);
							 $("#mail_to_new_quote").val(res.emailContent.to);
							 $("#mail_subject_new_quote").val(res.emailContent.subject);
							 $("#mail_body_new_quote").val(res.emailContent.body);
							 $("#entity_id").val(entity_ids);
							  //$("#mail_file_name").html(file_name);
							 $("#maildivnewQuotes").dialog("open");
							
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

function convertToOrder() {
	
	   if ($(".entity-checkbox:checked").size() == 0) {
           alert("You have no selected items.");
        } else {
			var entity_ids = [];
			$(".entity-checkbox:checked").each(function(){
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
            success: function (result) {
                if (result.success == true) {
                   document.location.reload();
				  
                } else {
                    alert("Can't convert Quote. Try again later, please");
                }
            },
            error: function (result) {
                alert("Can't convert Quote. Try again later, please");
            }
        });
    }
}

</script>



<table cellspacing="0" cellpadding="0" border="0" class="grid" width="100%">
      <tr>
         <td>
         <h2>Number of Quotes: <?php print count($this->entities['quote']);?></h2>
         </td>
       </tr>
 </table>   
 <br /> 

<?php if ($this->status != Entity::STATUS_ARCHIVED) : ?>
<table cellspacing="0" cellpadding="0" width="100%" class="control-bar">
	<tr>
		
		<td width="100%">&nbsp;</td>
		 <td><?= functionButton('Print', 'printSelectedQuoteForm()') ?></td>
           <td>@form_templates_quotes@</td>
		<td>
		   <?php //print functionButton('Email', 'emailSelectedQuoteForm()'); ?>
		   <?php print functionButton('Email', 'emailSelectedQuoteFormNew()'); ?>
		</td>		
        <td>@email_templates_quotes@</td>
        <td>
		    <?php if ($this->status == Entity::STATUS_ACTIVE) { ?>
               <?= functionButton('Convert to Order', 'convertToOrder()') ?>
            <?php }?>   
         </td>
		<td>
		<?php  print functionButton('Reassign Quotes', 'reassignQuoteDialog()'); ?>
		<?php // print functionButton('Reassign Quotes', 'reassign(\'top\')'); ?>
		</td>

		<td>
			<?php if ($this->status == Entity::STATUS_ACTIVE) : ?>
			<?= functionButton('Place On Hold', 'placeOnHold()') ?>
			<?php elseif ($this->status == Entity::STATUS_ONHOLD) : ?>
			<?= functionButton('Restore', 'restore()') ?>
			<?php endif; ?>
		</td>
		<td><?= functionButton('Cancel Quotes', 'cancel()') ?></td>
	</tr>
</table>
<?php endif; ?>
<table cellspacing="0" cellpadding="0" border="0" class="grid" width="100%">
	<tbody>
		<tr class="grid-head">
			<td class="grid-head-left">
				<?php if (isset($this->order)) : ?>
				<?=$this->order->getTitle("id", "ID")?>
				<?php else : ?>ID<?php endif; ?>
			</td>
			<td>
				<?php if (isset($this->order)) : ?>
				<?=$this->order->getTitle("quoted", "Quoted")?>
				<?php else : ?>Quoted<?php endif; ?>
			</td>
			<td>Notes</td>
			<td>
				<?php if (isset($this->order)) : ?>
					<?=$this->order->getTitle("shipper", "Shipper Information")?>
				<?php else : ?>Shipper<?php endif; ?>
			</td>
			<td>Vehicle Information</td>
			<td>
				<?php if (isset($this->order)) : ?>
					<?=$this->order->getTitle("origin", "Origin")?>
				<?php else : ?>Origin<?php endif; ?>
				/
				<?php if (isset($this->order)) : ?>
					<?=$this->order->getTitle("destination", "Destination")?>
				<?php else : ?>Destination<?php endif; ?>
			</td>
			<td>
				<?php if (isset($this->order)) : ?>
					<?=$this->order->getTitle("tariff", "Transport Cost")?>
				<?php else : ?>Tariff<?php endif; ?>
			</td>
			<td class="grid-head-right">
				<?php if (isset($this->order)) : ?>
					<?=$this->order->getTitle("avail", "Est. Ship")?>
				<?php else : ?>Est. Ship<?php endif; ?>
			</td>
		</tr>
<?php if (count($this->entities['quote']) == 0): ?>
	<tr class="grid-body">
		<td colspan="8" align="center" class="grid-body-left grid-body-right"><i>No records</i></td>
	</tr>
<?php endif; ?>
<?php 
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
	
$searchData = array();

 foreach($this->entities['quote'] as $i => $entity) :
   flush();
   $i++;
   
          $searchData[] = $entity['id'];
   
		  $bgcolor = "#ffffff";
		  if($i%2==0)
		    $bgcolor = "#f4f4f4";
			
	    $number = "";
        if (trim($entity['prefix']) != "") {
            $number .= $entity['prefix'] . "-";
        }
        $number .= $entity['number'];
?>
		<tr id="quote_tr_<?= $entity['id'] ?>" class="grid-body<?=($i == 0 ? " first-row" : "")?>">
			<td align="center" class="grid-body-left"  bgcolor="<?= $bgcolor ?>">
				<?php if (!$entity['readonly']) : ?>
				<input type="checkbox" value="<?= $entity['id'] ?>" class="entity-checkbox"/><br/>
				<?php endif; ?>
				<a href="<?= SITE_IN ?>application/quotes/show/id/<?= $entity['id'] ?>"><?= $number ?></a><br/>
				<a href="<?= SITE_IN ?>application/quotes/history/id/<?= $$entity['id'] ?>">History</a><br/><br/>
				<?php /*if ($entity['status'] == Entity::STATUS_ARCHIVED) : ?>
                <a href="<?= SITE_IN ?>application/quotes/unarchived/id/<?= $entity['id'] ?>">UnArchive</a>
               <?php endif;*/ ?>
			</td>
			<?php  //$assigned = $entity->getAssigned(); ?>
		<td valign="top" style="white-space: nowrap;"  bgcolor="<?= $bgcolor ?>"><?= date("m/d/y h:i a", strtotime($entity['quoted'])) ?>
			 <br><br>Assigned to:<br/> <strong><?= $entity['contactname'] ?></strong><br />
			</td>
            <td  bgcolor="<?= $bgcolor ?>">
			<?php 
			             $notes = new NoteManager($this->daffny->DB);
						 $notesData = $notes->getNotesArrData($entity['id']);
					      
						 $countNewNotes = count($notesData[Note::TYPE_INTERNALNEW]);
						 $countInternalNotes = count($notesData[Note::TYPE_INTERNAL]) + $countNewNotes;
			//$notes = $entity->getNotes();?>
				<?= notesIcon($entity['id'], count($notesData[Note::TYPE_FROM]), Note::TYPE_FROM, $entity['status'] == Entity::STATUS_ARCHIVED) ?>
				<?= notesIcon($entity['id'], count($notesData[Note::TYPE_TO]), Note::TYPE_TO, $entity['status'] == Entity::STATUS_ARCHIVED) ?>
				<?= notesIcon($entity['id'], $countInternalNotes, Note::TYPE_INTERNAL, $entity['status'] == Entity::STATUS_ARCHIVED) ?>
			</td>
            <td bgcolor="<?= $bgcolor ?>">
                
                <?= $entity['s_fname'] ?> <?= $entity['s_lname'] ?><br/>
                <?php if($entity['s_company']!=""){?>
				<b><?= $entity['s_company']?></b><br />
				<?php }?>
                <?= formatPhone($entity['s_phone1']) ?><br/>
                <a href="mailto:<?= $entity['s_email'] ?>"><?= $entity['s_email'] ?></a><br />
                <?php if($entity['referred_by'] != ""){?>
				  Referred By <b><?= $entity['referred_by'] ?></b><br>
				<?php }?>
                
            </td>
			<td bgcolor="<?= $bgcolor ?>" width="25%">
            <?php
			
			$vehicleManager = new VehicleManager($this->daffny->DB);
		    $vehicles = $vehicleManager->getVehiclesArrData($entity['id'], $entity['type']);
			?>
                 <?php if (count($vehicles) == 0) { ?>
                <?php }elseif (count($vehicles) == 1) { ?>
                    <?php $vehicle = $vehicles[0]; ?>
                    <?= $vehicle['make']; ?> <?= $vehicle['model']; ?><br/>
                    <?= $vehicle['year']; ?> <?= $vehicle['type']; ?>&nbsp;<?= imageLink($vehicle['year'] . " " . $vehicle['make'] . " " . $vehicle['model'] . " " . $vehicle['type']) ?>
                    <br/>
                <?php }else { ?>
                    <span class="like-link multi-vehicles">Multiple Vehicles<b><span style="color:#000000;">(<?php print count($vehicles);?>)</span></b></span>
                    
                    <div class="vehicles-info">
					<table width="100%"   cellpadding="0" cellspacing="1">
                         <tr>
                             <td  style="padding:3px;"><b><p>Year</p></b></td>
                             <td  style="padding:3px;"><b><p><?= Make ?></p></b></td>
							 <td  style="padding:3px;"><b><p><?= Model ?></p></b></td>
                             <td  style="padding:3px;"><b><p><?= Type ?></p></b></td> 
							 <td  style="padding:3px;"><b><p><?= Vin# ?></p></b></td>
                             <td  style="padding:3px;"><b><p><?= Inop ?></p></b></td>
						  </tr>
                        <?php foreach ($vehicles as $key => $vehicle) : ?>
                            <tr>
                             <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle['year'] ?></td>
                             <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle['make'] ?></td>
							 <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle['model'] ?></td> 
                             <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle['type'] ?></td>
                             <td bgcolor="#ffffff" style="padding:3px;"> <?php  print $vehicle['vin'] ?></td>
                             <td bgcolor="#ffffff" style="padding-left:5px;"> <?php  print $vehicle['inop']==0?"No":"Yes"; ?></td>
                           </tr>
                        <?php endforeach; ?>
						</table>
                    </div>
                    <br/>
                <?php  } ?>
                <br><span style="color:black;weight:bold;">Ship Via:  </span><span style="color:red;weight:bold;"><?php print ($entity['ship_via'] != 0) ? $ship_via_string[$entity['ship_via']] : ""; ?></span><br/>
                <strong>Source: </strong><?php //print $source->company_name; ?>
            </td>
            
			<?php
			$o_link = "http://maps.google.com/maps?q=" . urlencode($entity['o_city'] . ",+" . $entity['o_state']);
			$o_formatted = trim($entity['o_city'].', '.$entity['o_state'].' '.$entity['o_zip'], ", ");
			
			$d_link = "http://maps.google.com/maps?q=" . urlencode($entity['d_city'] . ",+" . $entity['d_state']);
			$d_formatted = trim($entity['o_city'].', '.$entity['d_state'].' '.$entity['d_zip'], ", ");
			?>
            <td bgcolor="<?= $bgcolor ?>">
               <span class="like-link"
                      onclick="window.open('<?= $o_link ?>', '_blank')"><?= $o_formatted ?></span> /<br/>
                <span class="like-link"
                      onclick="window.open('<?= $d_link ?>')"><?= $d_formatted ?></span><br/>
                
                <?php if (is_numeric($entity['distance']) && ($entity['distance'] > 0)) { ?>
                    <?= number_format($entity['distance'], 0, "", "") ?> mi
                    <?php $cost = $entity['carrier_pay'] + $entity['pickup_terminal_fee'] + $entity['dropoff_terminal_fee'];
                          
                    ?>
                        ($ <?= number_format(($cost / $entity['distance']), 2, ".", ",") ?>/mi)
                <?php } ?>
                <span class="like-link" onclick="mapIt(<?= $entity['id'] ?>);">Map it</span>
            </td>
            
			<td width="11%"  bgcolor="<?= $bgcolor ?>" >
				<table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
					<tr>
						<td width="10"><img src="<?= SITE_IN ?>images/icons/dollar.png" alt="Total Tariff" title="Total Tariff" width="16" height="16"/></td>
						<td><?= ("$ " . number_format((float)$entity['total_tariff_stored'], 2, ".", ",")) ?></td>
					</tr>
					<tr>
						<td width="10"><img src="<?= SITE_IN ?>images/icons/truck.png" alt="Carrier Pay" title="Carrier Pay" width="16" height="16"/></td>
						<td><?= ("$ " . number_format((float)$entity['carrier_pay_stored'], 2, ".", ",")) ?><br/></td>
					</tr>
					<tr>
						<td width="10"><img src="<?= SITE_IN ?>images/icons/person.png" alt="Deposit" title="Deposit" width="16" height="16"/></td>
						<td><?= ("$ " . number_format((float)($entity['total_tariff_stored'] - $entity['carrier_pay_stored']), 2, ".", ",")) ?></td>
					</tr>
				</table>
			</td>
			<td valign="top" align="center" class="grid-body-right"  bgcolor="<?= $bgcolor ?>" ><? print date("m/d/y", strtotime($entity['est_ship_date'])); ?></td>
		</tr>
<?php endforeach; ?>

	</tbody>
</table>
<?php } ?>

<!-- ================Leads=======================-->
<br /><br />

<?php if (count($this->entities['lead_created']) > 0){ ?>
<script type="text/javascript">
    function saveQuotes(email) {
        if ($(".entity-checkbox:checked").size() == 0) {
            alert("You should check at least one Lead to save Quotes");
            return;
        }
		
        var ajData = [];
        $(".entity-checkbox:checked").each(function(){
            if ($("#lead_tariff_"+$(this).val()).size() > 0) {
                ajData.push('{"entity_id":"'+$(this).val()+'","tariff":"'+$('#lead_tariff_'+$(this).val()).val()+'","deposit":"'+$('#lead_deposit_'+$(this).val()).val()+'"}');
            }
        });
		if (ajData.length == 0) {
			alert("You have no quote data");
			return;
		}
		$("body").nimbleLoader('show');
        $.ajax({
            type: "POST",
            url: "<?=SITE_IN?>application/ajax/entities.php",
            dataType: 'json',
            data: {
                action: 'saveQuotes',
                email: email,
                data: "["+ajData.join(",")+"]"
            },
			success: function(res) {
				if (res.success) {
					//document.location.href = document.location.href;
					document.location.reload();
				} else {
					alert("Can't save Quote(s)");
				}
			},
            complete: function(response) {
				$("body").nimbleLoader('hide');
            }
        });
    }
	
function convertToOrder() {
	
	   if ($(".entity-checkbox:checked").size() == 0) {
           alert("You have no selected items.");
        } else {
			var entity_ids = [];
			$(".entity-checkbox:checked").each(function(){
				entity_ids.push($(this).val());
			});      
	
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
                   document.location.reload();
				   
                } else {
                    alert("Can't convert Order. Try again later, please");
                }
            },
            error: function (result) {
                alert("Can't convert Order. Try again later, please");
            }
        });
    }
}	
</script>
<table cellspacing="0" cellpadding="0" border="0" class="grid" width="100%">
      <tr>
         <td>
         <h2>Number of Created Leads: <?php print count($this->entities['lead_created']);?></h2>
         </td>
       </tr>
 </table>   
 <br />  
 <table cellspacing="0" cellpadding="0" width="100%" class="control-bar">
	<tr>
		
		<td>
			<!--select class="form-box-combobox" id="company_members_top">
				<?php foreach($this->company_members as $member) : ?>
				<option value="<?= $member->id ?>"><?= $member->contactname ?></option>
				<?php endforeach; ?>
			</select-->
		</td>
		<!--td><?php  //functionButton('Reassign Leads', 'reassign(\'top\')') ?>
             <?= functionButton('Reassign Leads', 'reassignOrdersDialog()') ?>
        </td-->
		<!--td>
			<?php /*if ($this->status == Entity::STATUS_ACTIVE) : ?>
			<?= functionButton('Place On Hold', 'placeOnHold()') ?>
			<?php elseif ($this->status == Entity::STATUS_ONHOLD) : ?>
			<?= functionButton('Restore', 'restore()') ?>
			<?php endif;*/ ?>
		</td-->
        <td><?= functionButton('Convert to Quotes', 'saveQuotes(0)') ?></td>
        <td>
		    
               <?= functionButton('Convert to Order', 'convertToOrder()') ?>
          
         </td>
		<td><?= functionButton('Cancel', 'cancel()') ?></td>
		<?php if ($this->status == Entity::STATUS_ACTIVE) : ?>
		<td width="100%">&nbsp;</td>
		
		<!--td><?php //print functionButton('Save &amp; Email Quotes', 'saveQuotes(1)'); ?></td-->
		<?php else : ?>
		<td width="100%" colspan="3">&nbsp;</td>
		<?php endif; ?>
	</tr>
</table>  
<table cellspacing="0" cellpadding="0" border="0" class="grid" width="100%">
	<tbody>
        
		<tr class="grid-head">
			<td class="grid-head-left">
				ID
			</td>
			<td>
              <?php if($this->status == Entity::STATUS_ARCHIVED){?>
                 Received/Created
              <?php }else{?>
				<?php if (isset($this->order)) : ?>
				<?=$this->order->getTitle("created", "Created")?>
				<?php else : ?>Received<?php endif; ?>
              <?php }?>
              
			</td>
			<td>Notes</td>
			<td>
				<?php if (isset($this->order)) : ?>
					<?=$this->order->getTitle("shipper", "Shipper")?>
				<?php else : ?>Shipper<?php endif; ?>
			</td>
            
			 <td>Hours of Operations</td>
             <td>Shipment Types</td>
             <td>Units/month</td>
             <td>Hot Lead</td>
           
			
		</tr>
<?php if (count($this->entities['lead_created']) == 0): ?>
	<tr class="grid-body">
		<td colspan="8" align="center" class="grid-body-left grid-body-right"><i>No records</i></td>
	</tr>
<?php endif; ?>
<?php 
$searchData = array();
/*
print "<pre>";
print_r($this->entities['lead_created']);
print "</pre>";
*/
foreach($this->entities['lead_created'] as $i => $entity) :
   //$searchData[] = $entity->id;
?>
		<tr id="lead_tr_<?= $entity['id'] ?>" class="grid-body<?=($i == 0 ? " first-row" : "")?><?=($entity['duplicate'])?' duplicate':''?>">
			<td align="center" class="grid-body-left">
				<?php if (!$entity['readonly']) : ?>
				<input type="checkbox" value="<?= $entity['id'] ?>" class="entity-checkbox"/><br/>
				<?php endif; ?>
				<a href="<?= SITE_IN ?>application/leads/show/id/<?= $entity['id'] ?>"><?= $entity['number'] ?></a><br/>
				<a href="<?= SITE_IN ?>application/leads/history/id/<?= $entity['id'] ?>">History</a>
                <?php if($entity['status'] == Entity::STATUS_ARCHIVED){?>
						     <br/>Created
                        
                 <?php }?>       
			</td>
			<td valign="top" style="white-space: nowrap;">
             <?php if($this->status == Entity::STATUS_ARCHIVED){?>
						
                             <?= date("m/d/y h:i a", strtotime($entity['created']));?>
                       
                 <?php }else{?> 
							     <?= date("m/d/y h:i a", strtotime($entity['created']));?>
                               
                                <?php if ($entity['duplicate']) : ?>
                                <br/><span style="color: #F00;">Possible Duplicate</span>
                                <?php endif; ?>
                  <?php }?>  
                  
                  <?php //$assigned = $entity['duplicate']$entity->getAssigned();  ?>
              <br>Assigned to:<br/> <strong><?= $entity['contactname'] ?></strong><br />  
              <?php if($entity['creator_id']!=0){
					// $creator = $entity->getCreator();
			  ?>
                    <strong>Source: </strong><br /><?= $creator->contactname; ?><br/>
			<?php }?>             
			</td>
			<td>
			<?php 
			             $notes = new NoteManager($this->daffny->DB);
						 $notesData = $notes->getNotesArrData($entity['id']);
					      
						 $countNewNotes = count($notesData[Note::TYPE_INTERNALNEW]);
						 $countInternalNotes = count($notesData[Note::TYPE_INTERNAL]) + $countNewNotes;
			//$notes = $entity->getNotes();?>
				<?= notesIcon($entity['id'], count($notesData[Note::TYPE_FROM]), Note::TYPE_FROM, $entity['status'] == Entity::STATUS_ARCHIVED) ?>
				<?= notesIcon($entity['id'], count($notesData[Note::TYPE_TO]), Note::TYPE_TO, $entity['status'] == Entity::STATUS_ARCHIVED) ?>
				<?= notesIcon($entity['id'], $countInternalNotes, Note::TYPE_INTERNAL, $entity['status'] == Entity::STATUS_ARCHIVED) ?>
			</td>
			<td>
			<?= $entity['s_fname'] ?> <?= $entity['s_lname'] ?><br/>
                <?php if($entity['s_company']!=""){?>
				<b><?= $entity['s_company']?></b><br />
				<?php }?>
                <?= formatPhone($entity['s_phone1']) ?><br/>
                <a href="mailto:<?= $entity['s_email'] ?>"><?= $entity['s_email'] ?></a><br />
                <?php if($entity['referred_by'] != ""){?>
				  Referred By <b><?= $entity['referred_by'] ?></b><br>
				<?php }?>
			</td>
            
          <?php
				$shipment_type = "--";
				
			   if($entity['s_shipment_type']==1)
			      $shipment_type = "Full load";
			   elseif($entity['s_shipment_type']==2)
			      $shipment_type = "Singles";
			   elseif($entity['s_shipment_type']==3)
			      $shipment_type = "Both";	
				  
			?>
			 <td><? print $entity['s_shipper_hours']; ?></td>  
             <td><? print $shipment_type; ?></td>
             <td><? print $entity['s_units_per_month']; ?></td>
             <td><img src="<?=SITE_IN?>images/icons/hot.png" alt="Deposit" title="Hot Leads" >
                <input type="hidden" id="lead_tariff_<?=$entity['id']?>"  value="<?php print "0.00";?>" />
                <input type="hidden" id="lead_deposit_<?=$entity['id']?>" value="<?php print "0.00";?>" />
             </td>
            
		</tr>
<?php endforeach; ?>
 
	</tbody>
</table>

<?php }?>

<!-- ================Leads=======================-->
<br /><br />
<?php if (count($this->entities['lead_imported']) > 0){ ?>
<table cellspacing="0" cellpadding="0" border="0" class="grid" width="100%">
      <tr>
         <td>
         <h2>Number of Imported Leads: <?php print count($this->entities['lead_imported']);?></h2>
         </td>
       </tr>
 </table>   
 <br />    
<table cellspacing="0" cellpadding="0" border="0" class="grid" width="100%">
	<tbody>
        
		<tr class="grid-head">
			<td class="grid-head-left">
				ID
			</td>
			<td>
              
				<?php if (isset($this->order)) : ?>
				<?=$this->order->getTitle("created", "Created")?>
				<?php else : ?>Received<?php endif; ?>
              
              
			</td>
			<td>Notes</td>
			<td>
				<?php if (isset($this->order)) : ?>
					<?=$this->order->getTitle("shipper", "Shipper")?>
				<?php else : ?>Shipper<?php endif; ?>
			</td>
           
            
              <td>Vehicle</td>
              <td>
				<?php if (isset($this->order)) : ?>
					<?=$this->order->getTitle("origin", "Origin")?>
				<?php else : ?>Origin<?php endif; ?>
				/
				<?php if (isset($this->order)) : ?>
					<?=$this->order->getTitle("destination", "Destination")?>
				<?php else : ?>Destination<?php endif; ?>
			</td>
              
             
                <td>
                    <?php if (isset($this->order)) : ?>
                        <?=$this->order->getTitle("avail", "Est. Ship")?>
                    <?php else : ?>Est. Ship<?php endif; ?>
                </td>
          
			<td class="grid-head-right">
				Tariff
			</td>
           
			
		</tr>
<?php if (count($this->entities['lead_imported']) == 0): ?>
	<tr class="grid-body">
		<td colspan="8" align="center" class="grid-body-left grid-body-right"><i>No records</i></td>
	</tr>
<?php endif; ?>
<?php 
$searchData = array();
foreach($this->entities['lead_imported'] as $i => $entity) :
   //$searchData[] = $entity->id;
?>
		<tr id="lead_tr_<?= $entity['id'] ?>" class="grid-body<?=($i == 0 ? " first-row" : "")?><?=($entity['duplicate'])?' duplicate':''?>">
			<td align="center" class="grid-body-left">
				<?php if (!$entity['readonly']) : ?>
				<input type="checkbox" value="<?= $entity['id'] ?>" class="entity-checkbox"/><br/>
				<?php endif; ?>
				<a href="<?= SITE_IN ?>application/leads/show/id/<?= $entity['id'] ?>"><?= $entity['number'] ?></a><br/>
				<a href="<?= SITE_IN ?>application/leads/history/id/<?= $entity['id'] ?>">History</a>
                <?php if($entity['status'] == Entity::STATUS_ARCHIVED){?>
						
                             <br/>Imported
                        
                 <?php }?>       

			</td>
			<td valign="top" style="white-space: nowrap;">
             <?php if($this->status == Entity::STATUS_ARCHIVED){?>
						
                             <?= $entity['created']//$entity->getReceived("m/d/y h:i a") ?>
                        
                 <?php }else{?> 
							   
                                <?= $entity['received'] ?>
                              
                                <?php if ($entity['duplicate']) : ?>
                                <br/><span style="color: #F00;">Possible Duplicate</span>
                                <?php endif; ?>
                  <?php }?>  
                  
                  <?php //$assigned = $entity['duplicate']$entity->getAssigned();  ?>
              <br>Assigned to:<br/> <strong><?= $entity['contactname'] ?></strong><br />  
              <?php if( $entity['creator_id']!=0){
					 //$creator = $entity->getCreator();
			  ?>
                    <strong>Source: </strong><br /><?= $creator->contactname; ?><br/>
			<?php }?>             
			</td>
			<td>
			<?php 
			             $notes = new NoteManager($this->daffny->DB);
						 $notesData = $notes->getNotesArrData($entity['id']);
					      
						 $countNewNotes = count($notesData[Note::TYPE_INTERNALNEW]);
						 $countInternalNotes = count($notesData[Note::TYPE_INTERNAL]) + $countNewNotes;
			//$notes = $entity->getNotes();?>
				<?= notesIcon($entity['id'], count($notesData[Note::TYPE_FROM]), Note::TYPE_FROM, $entity['status'] == Entity::STATUS_ARCHIVED) ?>
				<?= notesIcon($entity['id'], count($notesData[Note::TYPE_TO]), Note::TYPE_TO, $entity['status'] == Entity::STATUS_ARCHIVED) ?>
				<?= notesIcon($entity['id'], $countInternalNotes, Note::TYPE_INTERNAL, $entity['status'] == Entity::STATUS_ARCHIVED) ?>
			</td>
			<td>
			<?= $entity['s_fname'] ?> <?= $entity['s_lname'] ?><br/>
                <?php if($entity['s_company']!=""){?>
				<b><?= $entity['s_company']?></b><br />
				<?php }?>
                <?= formatPhone($entity['s_phone1']) ?><br/>
                <a href="mailto:<?= $entity['s_email'] ?>"><?= $entity['s_email'] ?></a><br />
                <?php if($entity['referred_by'] != ""){?>
				  Referred By <b><?= $entity['referred_by'] ?></b><br>
				<?php }?>
			</td>
            
          
			<td bgcolor="<?= $bgcolor ?>" width="25%">
            <?php
			
			$vehicleManager = new VehicleManager($this->daffny->DB);
		    $vehicles = $vehicleManager->getVehiclesArrData($entity['id'], $entity['type']);
			?>
                 <?php if (count($vehicles) == 0) { ?>
                <?php }elseif (count($vehicles) == 1) { ?>
                    <?php $vehicle = $vehicles[0]; ?>
                    <?= $vehicle['make']; ?> <?= $vehicle['model']; ?><br/>
                    <?= $vehicle['year']; ?> <?= $vehicle['type']; ?>&nbsp;<?= imageLink($vehicle['year'] . " " . $vehicle['make'] . " " . $vehicle['model'] . " " . $vehicle['type']) ?>
                    <br/>
                <?php }else { ?>
                    <span class="like-link multi-vehicles">Multiple Vehicles<b><span style="color:#000000;">(<?php print count($vehicles);?>)</span></b></span>
                    
                    <div class="vehicles-info">
					<table width="100%"   cellpadding="0" cellspacing="1">
                         <tr>
                             <td  style="padding:3px;"><b><p>Year</p></b></td>
                             <td  style="padding:3px;"><b><p><?= Make ?></p></b></td>
							 <td  style="padding:3px;"><b><p><?= Model ?></p></b></td>
                             <td  style="padding:3px;"><b><p><?= Type ?></p></b></td> 
							 <td  style="padding:3px;"><b><p><?= Vin# ?></p></b></td>
                             <td  style="padding:3px;"><b><p><?= Inop ?></p></b></td>
						  </tr>
                        <?php foreach ($vehicles as $key => $vehicle) : ?>
                            <tr>
                             <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle['year'] ?></td>
                             <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle['make'] ?></td>
							 <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle['model'] ?></td> 
                             <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle['type'] ?></td>
                             <td bgcolor="#ffffff" style="padding:3px;"> <?php  print $vehicle['vin'] ?></td>
                             <td bgcolor="#ffffff" style="padding-left:5px;"> <?php  print $vehicle['inop']==0?"No":"Yes"; ?></td>
                           </tr>
                        <?php endforeach; ?>
						</table>
                    </div>
                    <br/>
                <?php  } ?>
                <br><span style="color:black;weight:bold;">Ship Via:  </span><span style="color:red;weight:bold;"><?php print ($entity['ship_via'] != 0) ? $ship_via_string[$entity['ship_via']] : ""; ?></span><br/>
                <strong>Source: </strong><?php //print $source->company_name; ?>
            </td>
            <td>
            		
                            <?php
								$o_link = "http://maps.google.com/maps?q=" . urlencode($entity['o_city'] . ",+" . $entity['o_state']);
								$o_formatted = trim($entity['o_city'].', '.$entity['o_state'].' '.$entity['o_zip'], ", ");
								
								$d_link = "http://maps.google.com/maps?q=" . urlencode($entity['d_city'] . ",+" . $entity['d_state']);
								$d_formatted = trim($entity['o_city'].', '.$entity['d_state'].' '.$entity['d_zip'], ", ");
							?>
                                  
                                   <span class="like-link"
                                          onclick="window.open('<?= $o_link ?>', '_blank')"><?= $o_formatted ?></span> /<br/>
                                    <span class="like-link"
                                          onclick="window.open('<?= $d_link ?>')"><?= $d_formatted ?></span><br/>
                                    
                                    <?php if (is_numeric($entity['distance']) && ($entity['distance'] > 0)) { ?>
                                        <?= number_format($entity['distance'], 0, "", "") ?> mi
                                        <?php $cost = $entity['carrier_pay'] + $entity['pickup_terminal_fee'] + $entity['dropoff_terminal_fee'];
                                              
                                        ?>
                                            ($ <?= number_format(($cost / $entity['distance']), 2, ".", ",") ?>/mi)
                                    <?php } ?>
                                    <span class="like-link" onclick="mapIt(<?= $entity['id'] ?>);">Map it</span>
                                
                     
                 
                 </td>
                 <td align="center">
			 		
                             <?php //print $entity->getShipDate("m/d/y"); ?>
                             <?php print $entity['est_ship_date'];?>
                       
                
			</td>
            <td style="white-space: nowrap;"  class="grid-body-right">
                <?php /*if ($entity['status'] == Entity::STATUS_ACTIVE) : ?>
					<?php if (count($vehicles) == 1) : ?>
						<?php if (!$entity->readonly) : ?>
                        <table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
                            <tr>
                                <td width="10"><img src="<?=SITE_IN?>images/icons/dollar.png" alt="Tariff" title="Tariff" width="16" height="16"></td>
                                <td style="white-space: nowrap;">$ <input type="text" id="lead_tariff_<?=$entity->id?>" class="form-box-textfield decimal" value="<?=number_format($vehicles[0]->tariff, 2, ".", "")?>" style="width: 50px;"/>&nbsp;<span class="small">tariff</span></td>
                            </tr>
                            <tr>
                                <td width="10"><img src="<?=SITE_IN?>images/icons/person.png" alt="Deposit" title="Deposit" width="16" height="16"></td>
                                <td style="white-space: nowrap;">$ <input type="text" id="lead_deposit_<?=$entity->id?>" class="form-box-textfield decimal" value="<?=number_format($vehicles[0]->deposit, 2, ".", "")?>" style="width: 50px;"/>&nbsp;<span class="small">deposit</span></td>
                            </tr>
						</table>
						<?php endif; ?>
					<?php else : ?>
					<?=simpleButton('Details', SITE_IN.'application/leads/show/id/'.$entity->id)?>
					<?php endif; ?>
				<?php elseif ($this->status == Entity::STATUS_UNREADABLE || $_GET['leads'] == "search") : ?>
					<?=simpleButton('Details', SITE_IN.'application/leads/show/id/'.$entity->id)?>
                <?php else : ?>
                    <?= $entity->getStatusUpdated("m/d/Y") ?>
                <?php endif; */ ?>
                
                <table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
					<tr>
						<td width="10"><img src="<?= SITE_IN ?>images/icons/dollar.png" alt="Total Tariff" title="Total Tariff" width="16" height="16"/></td>
						<td><?= ("$ " . number_format((float)$entity['total_tariff_stored'], 2, ".", ",")) ?></td>
					</tr>
					<tr>
						<td width="10"><img src="<?= SITE_IN ?>images/icons/truck.png" alt="Carrier Pay" title="Carrier Pay" width="16" height="16"/></td>
						<td><?= ("$ " . number_format((float)$entity['carrier_pay_stored'], 2, ".", ",")) ?><br/></td>
					</tr>
					<tr>
						<td width="10"><img src="<?= SITE_IN ?>images/icons/person.png" alt="Deposit" title="Deposit" width="16" height="16"/></td>
						<td><?= ("$ " . number_format((float)($entity['total_tariff_stored'] - $entity['carrier_pay_stored']), 2, ".", ",")) ?></td>
					</tr>
				</table>
			</td>     
                 
         
		</tr>
<?php endforeach; ?>
 
	</tbody>
</table>

<?php }?>