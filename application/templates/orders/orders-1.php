<link href="../assets/css/hint.css" rel="stylesheet" type="text/css"/>
<div id="maildivnew">
    <table cellspacing="2" cellpadding="0" border="0">
        <tr>
           <td colspan="2" style="padding-left:5px;">
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
<!-- sample it skill row start-->
<table id="testData" style="display:none; visibility:hidden;">
        <tr>
            <td>&nbsp;</td><td  colspan="2"><input type="text" name="email_extra" id="email_extra" value="" class="form-box-textfield"  maxlength="100" tooltip='E-mail' ></td>
        </tr>
    </table>
<div id="reassignCompandddyDiv">
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

function copyOrders(){
	if ($(".order-checkbox:checked").size() == 0) {
		$(".alert-message").empty();
		$(".alert-message").text("Order not selected");
		$(".alert-pack").show();
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
		$(".alert-message").empty();
		$(".alert-message").text("Error: You may copy one order at a time.");
		$(".alert-pack").show();
		return false;        
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

				document.location.reload();
				//alert("Order successfully undispatched.");

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
@pager@
<table cellspacing="0" cellpadding="0" width="100%" class="control-bar">
    <tr>
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
            <td valign="top"><?= functionButton('Post Loads', 'postToFB()') ?></td>
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
            <td valign="top"><?= functionButton('Dispatch', 'dispatch()') ?></td>
        <?php } ?>
        
            <td valign="top">
			<?php print functionButton('Reassign Order', 'reassignOrdersDialog()'); ?>
			<?php //print functionButton('Reassign Order', 'reassignOrders(\'top\')'); ?></td>
        <? } ?>
       
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
                                     print $this->order->getTitle("archived", "Archived");
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
	$i=0;
	if (count($this->entities) == 0): ?>
        <tr class="grid-body">
            <td colspan="9" align="center" class="grid-body-left grid-body-right"><i>No records</i></td>
        </tr>
    <?php endif; ?>
    <?php foreach ($this->entities as $i => $entity) : /* @var Entity $entity */ 
	       $i++;
		  $bgcolor = "#ffffff";
		  if($i%2==0)
		    $bgcolor = "#f4f4f4";
		  
	     $isColor = $entity->isPaidOffColor();
		// $isValue = $entity->isPaidOffValue();
	
	?>
        <tr id="order_tr_<?= $entity->id ?>" class="grid-body<?= ($i == 0 ? " first-row" : "") ?>">
            <td align="center" class="grid-body-left" bgcolor="<?= $bgcolor ?>">
                <?php if ($_GET['orders'] != 'all') { ?>
                    <?php //if (!$entity->readonly) : ?>
                        <!--input type="radio" name="order_id" value="<?= $entity->id ?>" class="order-checkbox"/-->
                        <input type="checkbox" name="order_id" value="<?= $entity->id ?>" class="order-checkbox"/><br/>
                    <?php //endif; ?>
                    <a href="<?= SITE_IN ?>application/orders/show/id/<?= $entity->id ?>"><?= $entity->getNumber() ?></a>
                    <br/>
                    <a href="<?= SITE_IN ?>application/orders/history/id/<?= $entity->id ?>">History</a>
					
					
					<?php if (isset($_POST['search_string'])) : 
					    print "<br /><b>Status</b><br>";
		                  
						     if ($entity->status == Entity::STATUS_ACTIVE) 
							    print "Active";
							 elseif($entity->status == Entity::STATUS_ONHOLD)
							    print "OnHold";
						     elseif($entity->status == Entity::STATUS_ARCHIVED)
							    print "Cancelled";	
						     elseif($entity->status == Entity::STATUS_POSTED)
							    print "Posted To FB";
							 elseif($entity->status == Entity::STATUS_NOTSIGNED)
							    print "Not Signed";
							elseif($entity->status == Entity::STATUS_DISPATCHED)
							    print "Dispatched";
							elseif($entity->status == Entity::STATUS_ISSUES)
							    print "Issues";	
							elseif($entity->status == Entity::STATUS_PICKEDUP)
							    print "Picked Up";	
						    elseif($entity->status == Entity::STATUS_DELIVERED)
							    print "Delivered";		
						  ?>
                    <?php endif; ?>
					
                     <?php //if ($this->status == Entity::STATUS_ARCHIVED) : ?>
                     <?php /*if ($entity->status == Entity::STATUS_ARCHIVED) : ?>
                      <br/> <br/> <a href="<?= SITE_IN ?>application/orders/unarchived/id/<?= $entity->id ?>">UnArchive</a>
                      <?php endif;*/ ?>
					
					
					
                <?php } else { ?>
                    <?= $entity->getNumber()
                    ; ?>
                <?php } ?>
            </td>
			<?php  $assigned = $entity->getAssigned(); ?>
            <td valign="top" style="white-space: nowrap;" bgcolor="<?= $bgcolor ?>">
			    <?php        if($entity->status == Entity::STATUS_ARCHIVED)
							    print $entity->getArchived("m/d/y h:i a");
						     elseif($entity->status == Entity::STATUS_DISPATCHED)
							    print $entity->getDispatched("m/d/y h:i a");
							 elseif($entity->status == Entity::STATUS_DELIVERED)
							    print $entity->getDelivered("m/d/y h:i a");
						    elseif($entity->status == Entity::STATUS_POSTED)
							    print date("m/d/y h:i a", strtotime($entity->posted));	
           					elseif($entity->status == Entity::STATUS_NOTSIGNED)
							    print date("m/d/y h:i a", strtotime($entity->not_signed));	
							elseif($entity->status == Entity::STATUS_ISSUES)
							    print date("m/d/y h:i a", strtotime($entity->issue_date));	
							elseif($entity->status == Entity::STATUS_ONHOLD)
							    print date("m/d/y h:i a", strtotime($entity->hold_date));
                            elseif($entity->status == Entity::STATUS_PICKEDUP)
							    print date("m/d/y h:i a", strtotime($entity->actual_pickup_date));								
							 else
                               	print $entity->getOrdered("m/d/y h:i a");	

 								
			      ?>
			<br />
            <?php if($entity->esigned==1){
				
				
				$files = $entity->getFiles($entity->id);
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
			  <?php }
			  elseif($entity->esigned==2){
				 
                  $files = $entity->getCommercialFiles($entity->id);
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
               <?php }
			   
			   /*else{
				  if($shipper->shipper_type == "Residential")
				  { 
					if($entity->esigned_date!=""){
						$date = strtotime(date("Y-m-d H:i:s"));
					    $str = strtotime('23 hours ago', $date);
					    $dateEsigned = strtotime($entity->esigned_date);
						//print date("Y-m-d H:i:s")."--". $str."--".$entity->esigned_date."==".$dateEsigned;
					     if($str >=  $dateEsigned){
							 ?>
                             <img src="<?= SITE_IN ?>images/icons/esignsend.png" /><br />
                             <?php
						 }
						 
					}
					else{
					  ?>
                      <img src="<?= SITE_IN ?>images/icons/esignnotsend.png" /><br />
                      <?php
			        }
			   }
			   
				if($shipper->shipper_type == "Commercial")
				  {
					if($entity->bsigned_date!=""){
						$date = strtotime(date("Y-m-d H:i:s"));
					    $str = strtotime('23 hours ago', $date);
					    $dateEsigned = strtotime($entity->bsigned_date);
						
					     if($str >=  $dateEsigned){
							 ?>
                             <br /><img src="<?= SITE_IN ?>images/icons/bsignnotsend.png" />
                             <?php
						 }
						 
					}
					else{
					  ?>
                      <img src="<?= SITE_IN ?>images/icons/bsignnotsend.png" /><br />
                      <?php
			        }
				  }
			   }*/
				   ?>
			   <br>Assigned to:<br/> <strong><?= $assigned->contactname ?></strong><br />
			</td>
            <td bgcolor="<?= $bgcolor ?>">
                <?php if ($_GET['orders'] != 'all') { ?>
                    <?php $notes = $entity->getNotes(); 
					      $notesNew = $entity->getNewNotes();
					      $countNewNotes = count($notesNew[Note::TYPE_INTERNALNEW]);
					?>
                    <?= notesIcon($entity->id, count($notes[Note::TYPE_FROM]), Note::TYPE_FROM, $entity->status == Entity::STATUS_ARCHIVED) ?>
                    <?= notesIcon($entity->id, count($notes[Note::TYPE_TO]), Note::TYPE_TO, $entity->status == Entity::STATUS_ARCHIVED) ?>
                    <?php //print notesIcon($entity->id, count($notes[Note::TYPE_INTERNAL]), Note::TYPE_INTERNAL, $entity->status == Entity::STATUS_ARCHIVED) ?>
                    <?= notesIcon($entity->id, count($notes[Note::TYPE_INTERNAL]), Note::TYPE_INTERNAL, $entity->status == Entity::STATUS_ARCHIVED,$countNewNotes)
				    ?>
                <?php } else { ?>
                    <?= $entity->getAssigned()->companyname
                    ; ?>
                <?php } ?>
            </td>
            <td bgcolor="<?= $bgcolor ?>">
			    
                <?php $shipper = $entity->getShipper();?>
                <?= $shipper->fname ?> <?= $shipper->lname ?><br/>
				<?php if($shipper->company!=""){?>
				<b><?= $shipper->company?></b><br />
				<?php }?>
                <?= formatPhone($shipper->phone1) ?><br/>
                <a href="mailto:<?= $shipper->email ?>"><?= $shipper->email ?></a><br>
				<?php if($entity->referred_by != ""){?>
				  Referred By <b><?= $entity->referred_by ?></b><br>
				<?php }?>
            </td>
            <td bgcolor="<?= $bgcolor ?>">
			
                <?php $vehicles = $entity->getVehicles();?>
                <?php $source = $entity->getSource(); ?>
                <?php if (count($vehicles) == 0) : ?>
                <?php elseif (count($vehicles) == 1) : ?>
                    <?php $vehicle = $vehicles[0]; ?>
                    <?= $vehicle->year; ?> <?= $vehicle->make; ?> <?= $vehicle->model; ?>&nbsp;<?php if($vehicle->inop){?>(<span style="color:red;weight:bold;"><?= "Inop" ?></span>)<?php }?><br/>
                    <?= $vehicle->type; ?>&nbsp;<?= imageLink($vehicle->year . " " . $vehicle->make . " " . $vehicle->model . " " . $vehicle->type) ?>
                    <?php if($vehicle->vin !=""){?>
					<br><span style="color:black;weight:bold;">VIN: <?= $vehicle->vin ?></span>
					<?php }?>
					<br/>
                <?php else : ?>
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
                             <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->year ?></td>
                             <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->make ?></td>
							 <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->model ?></td> 
                             <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->type ?></td>
                             <td bgcolor="#ffffff" style="padding:3px;"> <?php  print $vehicle->vin ?></td>
                             <td bgcolor="#ffffff" style="padding-left:5px;"> <?php  print $vehicle->inop==0?"No":"Yes"; ?></td>
                           </tr>
                        <?php endforeach; ?>
						</table>
                    </div>
                    <br/>
                <?php endif; ?>
				
				<span style="color:black;weight:bold;">Ship Via:  <span style="color:red;weight:bold;"><?= $entity->getShipVia() ?></span><br/>
                <strong>Source: </strong><?php print $source->company_name ?>
            </td>
            <?php $origin = $entity->getOrigin();?>
            <?php $destination = $entity->getDestination();?>
            <td bgcolor="<?= $bgcolor ?>">
                <span class="like-link"
                      onclick="window.open('<?= $origin->getLink() ?>', '_blank')"><?= $origin->getFormatted() ?></span> /<br/>
                <span class="like-link"
                      onclick="window.open('<?= $destination->getLink() ?>')"><?= $destination->getFormatted() ?></span><br/>
                <span class="like-link" onclick="mapIt(<?= $entity->id ?>);">Map it</span>
                <?php if (is_numeric($entity->distance) && ($entity->distance > 0)) : ?>
                    <br/><strong><?= number_format($entity->distance, 0, "", "") ?> mi
                        ($ <?= number_format(($entity->getCarrierPay(false) / $entity->distance), 2, ".", ",") ?>/mi)</strong>
                <?php endif; ?>
            </td>
			<?php
			             $Balance_Paid_By = "";
						   if(in_array($entity->balance_paid_by, array(2, 3 , 16 , 17)))   
							   $Balance_Paid_By = "<b>COD</b>";
							
							if(in_array($entity->balance_paid_by, array(8, 9 , 18 , 19)))   
							   $Balance_Paid_By = "COP";
							
							if(in_array($entity->balance_paid_by, array(12, 13 , 20 , 21)))   
							   $Balance_Paid_By = "Billing";
							 
							 if(in_array($entity->balance_paid_by, array(14, 15 , 22 , 23)))   
							   $Balance_Paid_By = "Billing";
			?>
            <!--td valign="top" align="center">
                <?php /*if ($avail_title == Entity::TITLE_PICKUP_DELIVERY){ ?>
                    <?php echo $entity->getLoadDateWithAbbr("m/d/y");?><br /><center>/</center>
                    <?php echo $entity->getDeliveryDateWithAbbr("m/d/y");?>
                    <?php }else{?>
                        Posting date<br /><?php if($entity->posted!="")echo date("m/d/y", strtotime($entity->posted));else print "N/A";?> <br /><center>/</center>
                        1st Avail<br /><?php echo $entity->getFirstAvail("m/d/y");?>
                <?php } ?>
				<br />Paid By <strong><font color="red"> <?php print $Balance_Paid_By; */?></font></strong>
            </td-->
            
                <?php if ($avail_title == Entity::TITLE_PICKUP_DELIVERY){ ?>
                <td valign="top" align="center" bgcolor="<?= $bgcolor ?>">
                    <?php echo $entity->getLoadDateWithAbbr("m/d/y");?>
                </td>
                <td valign="top" align="center" bgcolor="<?= $bgcolor ?>">    
                    <?php echo $entity->getDeliveryDateWithAbbr("m/d/y");?>
                </td>    
                    <?php }else{?>
                 <td valign="top" align="center" bgcolor="<?= $bgcolor ?>">
                        <?php echo $entity->getFirstAvail("m/d/y");?>
                 </td>       
                <?php } ?>
            <?php if ($avail_title != Entity::TITLE_PICKUP_DELIVERY){?>
            <td valign="top" align="center" bgcolor="<?= $bgcolor ?>">
            <?php echo $entity->getPostDate("m/d/y");?>
            </td>
            <?php } ?>
            <td bgcolor="<?= $bgcolor ?>">
            <strong>Payment Method:</strong>  <font color="red"><?php print $entity->getPaymentOption($entity->customer_balance_paid_by);?></font>
            <br /><strong>Carrier Paid By:</strong>  <font color="red"><?php print $Balance_Paid_By;?></font>
            <?php if($_SESSION['member']['access_payments']==1){?>
             <br /><br /><a href="javascript:void(0);" onclick="process_payment(<?php print $entity->id;?>);">Process Payment</a>    
             <br /><!--a href="javascript:void(0);" onclick="refund_payment(<?php print $entity->id;?>);">Refund</a-->      
                <?php }?>   
            </td>
			 <?php 
						   
						    if ($entity->type == Entity::TYPE_ORDER){
								
						      // $isColor = $entity->isPaidOffColor();
							   
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
            <td class="grid-body-right"  bgcolor="<?= $bgcolor ?>">
                <table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
                    <tr>
                        <td width="10"><img src="<?= SITE_IN ?>images/icons/dollar.png" alt="Total Tariff"
                                            title="Total Tariff" width="16" height="16"/></td>
                        <td style="white-space: nowrap;"><span class='<?= $Tcolor;?>'><?= $entity->getTotalTariff() ?></span></td>
                    </tr>
                    <tr>
                        <td width="10">
						     
						       
	                        <?php if ($ds = $entity->getDispatchSheet()) { ?>
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
			                        <?php $carrier = $entity->getCarrier();?>
			                        <?php if ($carrier instanceof Account) { ?>
				                        <strong>Hours of Operation: </strong><?= $carrier->hours_of_operation ?><br/>
			                        <?php } ?>
			                        <strong>Driver Name: </strong><?= $ds->carrier_driver_name ?><br/>
			                        <strong>Driver Phone: </strong><?= $ds->carrier_driver_phone ?><br/>
		                        </div>
	                        <?php } else { ?>
	                        <img src="<?= SITE_IN ?>images/icons/truck.png" alt="Carrier Pay" title="Carrier Pay" width="16" height="16"/>
	                        <?php } ?>
                        </td>
                        <td style="white-space: nowrap;"><span class='<?= $Ccolor;?>'><?= $entity->getCarrierPay() ?></span><br/></td>
                    </tr>
                    <tr>
                        <td width="10"><img src="<?= SITE_IN ?>images/icons/person.png" alt="Deposit    "
                                            title="Deposit" width="16" height="16"/></td>
                        <td style="white-space: nowrap;"><span class='<?= $Dcolor;?>'><?= $entity->getTotalDeposit() ?></span></td>
				  </tr>
                  <?php /*if($isValue['totalPayValue']>0){?>
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
<table cellspacing="0" cellpadding="0" width="100%" class="control-bar">
    <tr>
        <td width="100%">&nbsp;</td>
		      
        <?php if ($this->status != Entity::STATUS_ARCHIVED) { ?>
            
            <td valign="top">
			<?php print functionButton('Reassign Order', 'reassignOrdersDialog()'); ?>
			<?php  //print functionButton('Reassign Order', 'reassignOrders(\'bottom\')'); ?></td>
        <? } ?>
        <?php if (in_array($_GET['orders'], array("", "posted")) && $_SESSION['member']['access_dispatch']) { ?>
            <td  valign="top"><?= functionButton('Dispatch', 'dispatch()') ?></td>
        <?php } ?>

        <td valign="top">
            <?php if ($this->status == Entity::STATUS_ACTIVE) : ?>
                <?= functionButton('Place On Hold', 'placeOnHoldOrders()') ?>
            <?php elseif ($this->status == Entity::STATUS_ONHOLD) : ?>
                <?= functionButton('Restore', 'restoreOrders()') ?>
            <?php endif; ?>
        </td>
        <?php if ($_GET['orders'] != "archived") { ?>
            <td valign="top"><?= functionButton('Cancel Order', 'cancelOrders()') ?></td>
        <?php } ?>
        <?php if ($_GET['orders'] == "") { ?>
            <td valign="top"><?= functionButton('Post Loads', 'postToFB()') ?></td>
        <?php } elseif ($_GET['orders'] == "dispatched") { ?>
            <td valign="top"><?php print  functionButtonDate(Entity::STATUS_PICKEDUP,'Picked Up Date', 'setPickedUpStatusAndDate',false,'pickup_button1','yy-mm-dd');?></td>
        <?php } elseif ($_GET['orders'] == "pickedup") { ?>
            <td valign="top"><?php print  functionButtonDate(Entity::STATUS_DELIVERED,'Delivered Date', 'setPickedUpStatusAndDate',false,'delivered_button1','yy-mm-dd');?></td>
        <?php } elseif ($_GET['orders'] == 'posted') { ?>
            <td valign="top"><?= functionButton('Unpost', 'unpostFromFB()') ?></td>
        <?php } ?>
    </tr>
</table>
@pager@
