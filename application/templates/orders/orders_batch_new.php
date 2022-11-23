<div id="carrierdiv">

          <div id="carrier_data"> </div>

</div>

<script language="javascript" type="text/javascript">

	

	$(document).ready(function(){

			
$('#add_order').keypress(function(e) {
								  //alert(e.which);
    if(e.which == 13) {
        //alert('You pressed enter!');
		var textOrder = $("#add_order").val();
        if(textOrder!="")
		  addSearchOrder(); 

	
		return false;
    }
});
		

		var createForm = $('#create_form');

		createForm.find("input.shipper_company-model").autocomplete({

			source: function(request, response) {

				$.ajax({

					url: '<?= SITE_IN ?>application/ajax/autocomplete.php',

					type: 'GET',

					dataType: 'json',

					data: {

						term: request.term,

						action: 'getCompanyDataBatchPayment'

					},

					success: function(data) {

						response(data);

					}

				})

			},

			minLength: 0,

			autoFocus: true,

			select: function( event, ui ) {

				$( "#shipper_company" ).val( ui.item.company_name);

				$( "#shipper_company_id" ).val( ui.item.value );

				

				         

				return false;

			 },

			change: function() {

			

			}

		});

		

	});


function redirect_to_func()
{
	document.location.href = "/application/orders/batch";
}


function addSearchOrder() {

	var textOrder = $("#add_order").val();

	var textOld = $("#batch_order_ids").val();

	

	if(textOrder !=''){

	  if(textOld == ''){

	     textOld = textOrder;

		 

	  }

	  else{

	     textOld += ','+textOrder;

		 

	  }

	  

	  $('#orders_list').append('<option value="'+textOrder+'" selected>'+textOrder+'</option>');

	  $("#batch_order_ids").val(textOld);

	  $("#add_order").val('');

	}

	   

}



function clear_batch_value()

{

		  

	$("#shipper_company").val('');

	$("#shipper_company_id").val('');

	$("#orders_list option:selected").remove();

	$("#batch_order_ids").val('');

}

function batchRemove(value)
{
  $("#orders_list option[value="+value+"]").remove();
  $("#batch_order_ids").val('');
  var textOld = '';
  $("#orders_list option").each(function()
	{
		// Add $(this).val() to your list
		textOld += $(this).val()+",";
	});
  
  $("#batch_order_ids").val(textOld);
  //$('#create_form').submit();
  //document.forms["create_form"].submit();
 // $("form[name='create_form']").submit();
  //document.create_form.submit();
  //$('#submit_button').submit();
  $("input[type=submit][clicked=true]").val()
}

$("#carrierdiv").dialog({

	modal: true,

	width: 700,

	height: 310,

	title: "Freight Dragon Results",

	hide: 'fade',

	resizable: false,

	draggable: false,

	autoOpen: false

});
function getCarrierData(entity_id,ocity,ostate,ozip,dcity,dstate,dzip) {

		

        if (entity_id == "") {

            alert("Order not found");

        } else {



              $("body").nimbleLoader('show');

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

                           $("#carrier_data").html(res.carrierData);

							  $("#carrierdiv").dialog({width: 600},'option', 'title', 'Carrier Data').dialog("open");

		                       } else {

                            alert("Try again later, please");

                        }

                    },

                    complete: function (res) {

                        $("body").nimbleLoader('hide');

                    }

                });
        }

    }


var notes = [];	notes[<?= Note::TYPE_TO ?>] = [];	

notes[<?= Note::TYPE_FROM ?>] = [];	

notes[<?= Note::TYPE_INTERNAL ?>] = [];	

var notesIntervalId = undefined;	

var add_entity_id;	

var add_notes_type;	

var add_busy = false;

</script>


<style>
div.form-box-buttons1 input{
  width: 180px;
  height: 23px;
  color: #fff;
  background-color:#06F;
  border: 0;
  font-size: 11px;
  font-family: Arial, Helvetica, sans-serif;
  cursor: pointer;
  font-weight: normal;
}
</style>
<br />

<br />

<table width="100%" cellspacing="0" cellpadding="0" border="0">
 <tr>
    <td>
                 <table width="80%" cellspacing="0" cellpadding="0" border="0">
                
                    <tbody><tr>
                       <td align="right">
                                  <div class="form-box-buttons1" style="text-align:right !important;">
                <span id="submit1_button-submit-btn" style="-webkit-user-select: none;text-align:right !important;"><input type="button" id="redirect_to" value="Go To Shipper Payment" onclick="redirect_to_func();" style="-webkit-user-select: none;"></span>
                </div>
                      </td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                   </tbody>
                   </table> 
 </td>
</tr>
                
<tr>

 <td align="center">

    <table width="60%" cellspacing="0" cellpadding="0" border="0">

    <tbody><tr>

    <td class="form-box-white-top-left"></td>

    <td class="form-box-white-top">&nbsp;</td>

    <td class="form-box-white-top-right"></td>

    </tr>

    <tr>

    <td class="form-box-white-content-left">&nbsp;</td>

    <td valign="top" class="form-box-white-content">

    <h4 style="color:#3B67A6">Carrier Batch Payment Processing</h4>

    

                <form method="post"  action="<?php print SITE_IN."application/orders/batchsubmitnew";?>" name="create_form" id="create_form">

                <table width="100%" cellspacing="1" cellpadding="1">

                 

                <tr>

                 <td width="40%">

                    <table cellspacing="2" cellpadding="0" border="0">

                        <tr>

                            <td><span class="required">*</span>Order ID:</td>

                            <td valign="top">

                            <input type="text" id="add_order" maxlength="10" name="add_order" tabindex="41" class="zip form-box-textfield" style="width:170px;margin-left:5px;" kl_virtual_keyboard_secure_input="on">

                            

                            <!--input type="text" name="add_order" value="" id="add_order" /-->&nbsp;&nbsp;<img src="<?= SITE_IN ?>images/icons/add.gif"  width="20" height="20" onclick="addSearchOrder();"/></td>

                        </tr>

                        <tr><td colspan="2">&nbsp;</td></tr>

                        <tr>

                        <td>&nbsp;</td>

                        <td >

                       

                          <select name='orders_list[]' id="orders_list" size=7 multiple style="width:200px;">

                            <?php  

							   $orders_list = $_POST['orders_list'];

							    $orders_list_size = count($_POST['orders_list']);

							   if(is_array($orders_list) && $orders_list_size > 0){

								  for($i=0;$i<$orders_list_size;$i++)

								  {

									 ?>

                                     <option value="<?php print $orders_list[$i];?>" selected><?php print $orders_list[$i];?></option>

                                     <?php 

								  }  

							   }

							 ?>

                          </select> 

                          <input type="hidden" name="batch_order_ids" id="batch_order_ids" value="<?php print $_POST['batch_order_ids'];?>"/>

                        </td></tr>

                        

                         <!--tr><td colspan="2">&nbsp;</td></tr>

                        

                        <tr>

                            <td>@batch_order_ids@</td>

                        </tr-->

                       

                        

                    </table>

                  </td>

                  <td width="10%" align="center">

                    <b>OR</b>

                  </td>

  <td width="45%" align="left" valign="top">

                    <table cellspacing="2" cellpadding="0" border="0">

                         <tr><td colspan="2">&nbsp;</td></tr>

                         <tr><td colspan="2">&nbsp;</td></tr> 

                        <tr>

                            <td>@shipper_company@

                                <input type="hidden" name="shipper_company_id" id="shipper_company_id" value="<?php print $_POST['shipper_company_id'];?>"/></td>

                        </tr>

                        

                        <tr><td colspan="2">&nbsp;</td></tr>

                         <tr>

                            <td colspan="2" align="center">

                            <table width="100%" cellpadding="1" cellspacing="1">

                              <tr>

                                <td width="80%">

                            <?php print submitButtons(SITE_IN."application/orders/batch", "Locate Orders") ?>

                            <!--input type="submit" name="submit" value="Start Processing" /-->

                            <input type="hidden" name="submit" value="Start Processing" />

                              </td>

                              <td align="left">

                                <div class="form-box-buttons">

<span id="submit_button-submit-btn" style="-webkit-user-select: none;"><input type="button" id="clear_batch" value="Clear" onclick="clear_batch_value();" style="-webkit-user-select: none;"></span>

</div>

                              </td>

                              </tr>

                              </table>

                            

                            </td>

                        </tr>

                    </table>

                  </td> 

                 </tr>

                

                  <tr><td colspan="2">&nbsp;</td></tr>

                        

                 </table>         

                </form>

    

    </td>

    <td class="form-box-white-content-right">&nbsp;</td>

    </tr>

    <tr>

    <td class="form-box-white-bottom-left"></td>

    <td class="form-box-white-bottom">&nbsp;</td>

    <td class="form-box-white-bottom-right"></td>

    </tr>

    </tbody></table>

 </td>

</tr>

</table>    







<div style="display:none" id="notes">notes</div>

<br/>
<div id="notes_container" ></div><div id="notes_add"><div id="notes_add_title"></div><div id="notes_container_new" style="overflow-y:scroll;  max-height:280px; background-color:#ffffff; margin:5px; padding:5px;font-size:12px;"></div>	<br /><p></p>	<textarea class="form-box-textarea" name="add_note_text" style="padding-left:3px;font-size:11px;line-height:14px;color:#555;"></textarea>	<div style="float:right;">	<table cellspacing="0" cellpadding="0" border="0">			<tr>				<td style="color:#00000;">Quick Notes&nbsp;</td><td><select name="quick_notes" id="quick_notes" onchange="addQuickNote();">
<option value="">--Select--</value>
<option value="Emailed: Customer.">Emailed: Customer.</value>
<option value="Emailed: Bad e-mail.">Emailed: Bad e-mail.</value>
<option value="Faxed: e-Sign.">Faxed: e-Sign.</value>
<option value="Faxed: B2B.">Faxed: B2B.</value>
<option value="Faxed: Invoice.">Faxed: Invoice.</value>
<option value="Faxed: Recepit.">Faxed: Recepit.</value>
<option value="Phoned: Bad Mobile.">Phoned: Bad Number.</value>
<option value="Phoned: No Voicemail.">Phoned: No Voicemail.</value>
<option value="Phoned: Left Message.">Phoned: Left Message.</value>
<option value="Phoned: No Answer.">Phoned: No Answer.</value>
<option value="Phoned: Spoke to Customer.">Phoned: Spoke to Customer.</value>
<option value="Phoned: Spoke to carrier about pick-up.">Phoned: Spoke to carrier about pick-up.</value>
<option value="Phoned: NSpoke to carrier about drop-off.">Phoned: Spoke to carrier about drop-off.</value>
<option value="Phoned: Customer requested carrier info.">Phoned: Customer requested carrier info.</value>
<option value="Phoned: Customer requested damage.">Phoned: Customer requested damage.</value>
<option value="Phoned: Customer canceled, late pick-up.">Phoned: Customer canceled, late pick-up.</value>
<option value="Phoned: Customer canceled, no reason given.">Phoned: Customer canceled, no reason given.</value>
<option value="Phoned: Customer canceled, through e-Mail.">Phoned: Customer canceled, through e-Mail.</value>
<option value="Phoned: Customer was happy with transport.">Phoned: Customer was happy with transport.</value>
<option value="Phoned: Customer was un-happy with transport.">Phoned: Customer was un-happy with transport.</value>
<option value="Phoned: Customer want a refund.">Phoned: Customer want's a refund.</value>
<option value="Phoned: Not Interested.">Phoned: Not Interested.</value>
<option value="Phoned: Do Not Call.">Phoned: Do Not Call.</value>
</select></td><td><div style="float:left; padding:2px;">
&nbsp;&nbsp;&nbsp;Priority&nbsp;
</div>
<div style="float:left; padding:2px;"><select name="priority_notes" id="priority_notes" >
<option value="0">--Select--</option>
<option value="2">High</option>
<option value="1">Low</option>

</select>
</div></td><td><?= functionButton('Add Note', 'addNote()') ?></td>				<td><?= functionButton('Cancel', 'closeAddNotes()') ?></td>			</tr>		</table>		</div></div>
<?php

    $avail_title = Entity::TITLE_FIRST_AVAIL;

    if (isset($_GET['orders'])){

        if (in_array($_GET['orders'], array("notsigned","dispatched","pickedup","delivered","issues","archived"))){

            $avail_title = Entity::TITLE_PICKUP_DELIVERY;

        }

    }

?>



<br />

<table cellspacing="0" cellpadding="0" border="0" class="grid" width="100%">

    <tbody>

    <tr class="grid-head">

        <td class="grid-head-left">

            <?php if (isset($this->order)) : ?>

                <?= $this->order->getTitle("id", "ID") ?>

            <?php else : ?>ID<?php endif; ?>

        </td>

        <td>

            <?php if (isset($this->order)) : ?>

                <?= $this->order->getTitle("created", "Created") ?>

            <?php else : ?>Created<?php endif; ?>

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

        <td  width="17%">

	        <?php if (isset($this->order)) : ?>

		        <?= $this->order->getTitle("origin", "Origin") ?>

	        <?php else : ?>Origin<?php endif; ?>

	        /

	        <?php if (isset($this->order)) : ?>

		        <?= $this->order->getTitle("destination", "Destination") ?>

	        <?php else : ?>Destinations<?php endif; ?>

        </td>

        <td>

	        <?php if (isset($this->order)) : ?>

		        <?= $this->order->getTitle("avail", $avail_title) ?>

	        <?php else : ?><?= $avail_title; ?><?php endif; ?>

        </td>

        <td>

              <?php print "Pickup";  ?>

            </td>

            <td>

               <?php print "Delivery"; ?>

            </td>

        

        <td >

	        <?php if (isset($this->order)) : ?>

		        <?= $this->order->getTitle("tariff", "Tariff") ?>

	        <?php else : ?>tariff<?php endif; ?>

        </td>

        <td class="grid-head-right" width="20%">

	        Process Amount

        </td>

    </tr>

    <?php 

	/*print "<pre>";

	print_r($this->entities);

	print "</pre>";

	*/

	$i=0;

	

	if (count($this->entities) == 0): ?>

        <tr class="grid-body">

            <td colspan="9" align="center" class="grid-body-left grid-body-right"><i>No records</i></td>

        </tr>

    <?php endif; ?>

    <?php 
$batch_idsarr = explode(",",$_POST['batch_order_ids']);
	$EntityIDArrayData = array();
	
	for ($i=0;$i<sizeof($batch_idsarr); $i++)
	{
		foreach ($this->entities as $j => $entityObj)
		{
			
			if($batch_idsarr[$i] == $entityObj->number)
			{
				$EntityIDArrayData[] = $entityObj;
				break;
			}
		}
		
	}
	 $showTotal = 0;

	 $issue_type = $_POST['issue_type'];

	 $TotalAmount = 0;

	 $EntityIDArray = array();

	 //print_r($this->entities);

	foreach ($EntityIDArrayData as $i => $entity) : /* @var Entity $entity */

	

	   $showOrder = false;

	   

	   /*

	   if($_POST['batch_order_ids']!=""){

		   $showOrder = true;

	   }

	   elseif($_POST['shipper_company_id']!="" && $entity->isPaidOffCarrier())

	   {

	      $showOrder = true;

	   }

	   */

	   //print $_POST['shipper_company_id']."--".$entity->isPaidOffCarrier()."<br>";

	 // if($showOrder)

	  {

	      $i++;

		  $showTotal++;

		  $bgcolor = "#ffffff";

		  if($i%2==0)

		    $bgcolor = "#f4f4f4";

			

	       $paymentDone = $entity->getPayments();

		   $paymentDoneSize = count($paymentDone);

		   

		   

		   if ($ds = $entity->getDispatchSheet()) {

		        $dispatchSheet = $ds;

	        }

		 

	     $isColor = $entity->isPaidOffColor();

		 $isValue = $entity->isPaidOffValue();

		 

		 $EntityIDArray[] = $entity->id;

		

	?>

        <tr id="order_tr_<?= $entity->id ?>" class="grid-body<?= ($i == 0 ? " first-row" : "") ?>">

            <td align="center" class="grid-body-left" bgcolor="<?= $bgcolor ?>">

                <?php if ($_GET['orders'] != 'all') { ?>

                    

                    <a href="<?= SITE_IN ?>application/orders/show/id/<?= $entity->id ?>"><?= $entity->getNumber() ?></a>

                    <br/>

                    <a href="<?= SITE_IN ?>application/orders/history/id/<?= $entity->id ?>">History</a>

					<!--a href="javascript:void(0);" onclick="batchRemove('<?php //print $entity->number; ?>');">Remove</a-->

					

                     <?php if ($this->status == Entity::STATUS_ARCHIVED) : ?>

                     <br/> <br/>

                       <a href="<?= SITE_IN ?>application/orders/unarchived/id/<?= $entity->id ?>">UnArchive</a>

                      <?php endif; ?>

                      

					

                    <?php if (isset($_POST['search_string'])) : 

					    print "<br /><b>Status</b><br>";

		                  

						     if ($entity->status == Entity::STATUS_ACTIVE) 

							    print "Active";

							 elseif($entity->status == Entity::STATUS_ONHOLD)

							    print "OnHold";

						     elseif($entity->status == Entity::STATUS_ARCHIVED)

							    print "Archived";	

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

					

                <?php } else { ?>

                    <?= $entity->getNumber()

                    ; ?>

                <?php } ?>

            </td>

            <td valign="top" style="white-space: nowrap;" bgcolor="<?= $bgcolor ?>">

			<?= $entity->getOrdered("m/d/y h:i a") ?>

            <br />

            <?php if($entity->esigned==1){

				$files = $entity->getFiles($entity->id);

				 if ( isset($files) && count($files) ) { 

				               foreach ($files as $file) { 

									

									$pos = strpos($file['name_original'], "Signed");

									if ($pos === false) {}

									else{?>

										<!--li id="file-<?php //print $file['id']; ?>"-->

											<a <?=strtolower($file['type'])=='pdf'?"target=\"_blank\"":""?> href="<?=getLink("order", "getdocs", "id", $file['id'])?>"><img src="<?= SITE_IN ?>images/icons/esign_small.png" /></a>

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

										<!--li id="file-<?php //print $file['id']; ?>"-->

											<a <?=strtolower($file['type'])=='pdf'?"target=\"_blank\"":""?> href="<?=getLink("order", "getdocs", "id", $file['id'])?>"><img src="<?= SITE_IN ?>images/icons/b2b.png" /></a>

										<!--/li-->

                                        <?

									 }

									

									?>

										

								<?php } ?>

						<?php }?>

               <?php }?>

               <br>Assigned to:<br/> <strong><?= $entity->getAssigned()->contactname?></strong><br />

            </td>

            <td bgcolor="<?= $bgcolor ?>">

                <?php 

				

				if ($_GET['orders'] != 'all') { ?>

                    <?php 

					      $notes = $entity->getNotes();

					      $notesNew = $entity->getNewNotes();

					      $countNewNotes = count($notesNew[Note::TYPE_INTERNALNEW]);

					?>

                    <?= notesIcon($entity->id, count($notes[Note::TYPE_FROM]), Note::TYPE_FROM, $entity->status == Entity::STATUS_ARCHIVED) ?>

                    <?= notesIcon($entity->id, count($notes[Note::TYPE_TO]), Note::TYPE_TO, $entity->status == Entity::STATUS_ARCHIVED) ?>

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
               <b> <?= $shipper->company?></b><br />
                <?= formatPhone($shipper->phone1) ?><br/>
                <?php if($shipper->phone2!=""){?>
                <?= formatPhone($shipper->phone2) ?><br/>
                <?php }?>
                <a href="mailto:<?= $shipper->email ?>"><?= $shipper->email ?></a><br />
                Referred By <b><?= $entity->referred_by ?></b><br>
                
            </td>

            <td bgcolor="<?= $bgcolor ?>" width="25%">

                <?php $vehicles = $entity->getVehicles();?>

                <?php $source = $entity->getSource(); ?>

                <?php if (count($vehicles) == 0) : ?>

                <?php elseif (count($vehicles) == 1) : ?>

                    <?php $vehicle = $vehicles[0]; ?>

                    <?= $vehicle->make

                    ; ?> <?= $vehicle->model

                    ; ?><br/>

                    <?= $vehicle->year; ?> <?= $vehicle->type; ?>&nbsp;<?= imageLink($vehicle->year . " " . $vehicle->make . " " . $vehicle->model . " " . $vehicle->type) ?>

                    <br/>

                <?php else : ?>

                    <span class="like-link multi-vehicles">Multiple Vehicles<b><span style="color:#000000;">(<?php print count($vehicles);?>)</span></b></span>

                    <div class="vehicles-info">

                        <?php foreach ($vehicles as $key => $vehicle) : ?>

                            <div <?= ($key % 2) ? 'style="background-color: #161616;padding: 5px;"' : 'style="background-color: #000;padding: 5px;"' ?>>

                                <p><?= $vehicle->make ?> <?= $vehicle->model ?><?php if($vehicle->inop){?>(<span style="color:red;weight:bold;"><?= "Inop" ?></span>)<?php }?></p>

                                <?= $vehicle->year ?> <?= $vehicle->type ?>

                                &nbsp;<?=imageLink($vehicle->year . " " . $vehicle->make . " " . $vehicle->model . " " . $vehicle->type)?>

                                <br/>

                            </div>

                        <?php endforeach; ?>

                    </div>

                    <br/>

                <?php endif; ?>

                <span style="color:red;weight:bold;"><?= $entity->getShipVia() ?></span><br/>

                <strong>Source: </strong><?= $source->company_name ?>

            </td>

            <?php $origin = $entity->getOrigin();?>

            <?php $destination = $entity->getDestination();?>

            <td bgcolor="<?= $bgcolor ?>">

               <span class="like-link"

                      onclick="window.open('<?= $origin->getLink() ?>', '_blank')"><?= $origin->getFormatted() ?></span> /<br/>

                <span class="like-link"

                      onclick="window.open('<?= $destination->getLink() ?>')"><?= $destination->getFormatted() ?></span><br/>

                

                <?php if (is_numeric($entity->distance) && ($entity->distance > 0)) : ?>

                    <?= number_format($entity->distance, 0, "", "") ?> mi

                        ($ <?= number_format(($entity->getCarrierPay(false) / $entity->distance), 2, ".", ",") ?>/mi)

                <?php endif; ?>

                <span class="like-link" onclick="mapIt(<?= $entity->id ?>);">Map it</span>

            </td>

            <?php

			             $Balance_Paid_By = "";

						   if(in_array($entity->balance_paid_by, array(2, 3 , 16 , 17)))   

							   $Balance_Paid_By = "COD";

							

							if(in_array($entity->balance_paid_by, array(8, 9 , 18 , 19)))   

							   $Balance_Paid_By = "COP";

							

							if(in_array($entity->balance_paid_by, array(12, 13 , 20 , 21)))   

							   $Balance_Paid_By = "Billing";

							 

							 if(in_array($entity->balance_paid_by, array(14, 15 , 22 , 23)))   

							   $Balance_Paid_By = "Billing";

			?>

            <td valign="top" align="center" bgcolor="<?= $bgcolor ?>">

                <?php if ($avail_title == Entity::TITLE_PICKUP_DELIVERY){ ?>

                    <?php echo $entity->getLoadDateWithAbbr("m/d/y");?><br /><center>/</center>

                    <?php echo $entity->getDeliveryDateWithAbbr("m/d/y");?>

                    <?php }else{?>

                        <?php echo $entity->getFirstAvail("m/d/y");?>

                <?php } ?>

                

                       

            </td>

            

            <td valign="top" align="center" bgcolor="<?= $bgcolor ?>">

						<?php

                           if (strtotime($entity->load_date) == 0) $abbr = "N/A";

                           else

                           {

                             $abbr = $entity->load_date_type > 0 ? Entity::$date_type_string[(int)$entity->load_date_type] : "";

                             $abbr = $abbr . "<br />" . date("m/d/y", strtotime($entity->load_date));

                           }

                        ?>

                         <?php echo $abbr;?>

                </td>

                <td valign="top" align="center" bgcolor="<?= $bgcolor ?>"> 

					<?php

                       if (strtotime($entity->delivery_date) == 0) $abbr = "N/A";

                       else

                       {

                         $abbr = $entity->delivery_date_type > 0 ? Entity::$date_type_string[(int)$entity->delivery_date_type] : "";

                         $abbr = $abbr . "<br />" . date("m/d/y", strtotime($entity->delivery_date));

                       }

                    ?>   

                    <?php echo  $abbr;?>

                </td>    

            

            <?php 

						   

						    if ($entity->type == Entity::TYPE_ORDER){

								

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

                            

            <td  bgcolor="<?= $bgcolor ?>">

                <table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">

                    <tr>

                        <td width="10"><img src="<?= SITE_IN ?>images/icons/dollar.png" alt="Total Tariff"

                                            title="Total Tariff" width="16" height="16"/></td>

                        <td style="white-space: nowrap;"><span class='<?= $Tcolor;?>'><?= $entity->getTotalTariff() ?></span></td>

                    </tr>

                    <tr>

                        <td width="10">

                           

	                        <?php /* if ($ds = $entity->getDispatchSheet()) { ?>

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

	                        <?php }*/ ?>
                            
                            <img src="<?= SITE_IN ?>images/icons/truck.png" alt="Carrier Pay" alt="Carrier Pay" width="16" height="16" onclick="getCarrierData(<?php print $entity->id;?>,'<?php print $origin->city;?>','<?php print $origin->state;?>','<?php print $origin->zip;?>','<?php print $destination->city;?>','<?php print $destination->state;?>','<?php print $destination->zip;?>');"/>

                        </td>

                        <td style="white-space: nowrap;"><span class='<?= $Ccolor;?>'><?= $entity->getCarrierPay() ?></span><br/></td>

                    </tr>

                    <tr>

                        <td width="10"><img src="<?= SITE_IN ?>images/icons/person.png" alt="Deposit    "

                                            title="Deposit" width="16" height="16"/></td>

                        <td style="white-space: nowrap;"><span class='<?= $Dcolor;?>'><?= $entity->getTotalDeposit() ?></span></td>

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

            <td class="grid-body-right" bgcolor="<?= $bgcolor ?>">

              $ <input type="text" name="entity_amount[]" id="entity_<?php print $entity->id;?>" value="<?= $entity->getCarrierPay(false) ?>" size="8" onkeyup="matchAmount();"/>

              &nbsp;<input type="checkbox" name="entity_amount_flag[]" checked="checked" id="entity_flag_<?php print $entity->id;?>" value="1" class="order-checkbox" onclick="matchAmount();"/>
              <br /><br />
              <input type="text" name="entity_check_num[]" id="entity_check_num_<?php print $entity->id;?>" value="" placeholder="Check Number" />

            </td>

        </tr>

       

       <?php

	     $TotalAmount = $TotalAmount + $entity->getCarrierPay(false);

	   ?>

       

       <?php }?>

    <?php endforeach; ?>

    

    <?php if($showTotal>0){?>

     <tr bgcolor="#FFFFFF">

         <td colspan="9">&nbsp;</td>

         <td align="center"><b>Total : </b></td>

         <td align="left">$ <input type="text" name="entity_amount_show[]" id="entity_show" value="<?php print $TotalAmount;?>" size="8"/></td>

        </tr> 

  <?php }?>

    </tbody>

</table>



<br />

<hr />

<br />



<!---------------------------------------------------------------->

<?php

$batch_order_ids_arr = $EntityIDArray;

$batch_order_ids_arrSize = sizeof($batch_order_ids_arr);

?>

<script language="javascript" type="text/javascript">

 function matchAmount() {

	 var amountTotal = 0;

		 <?php 

			 

			 if($batch_order_ids_arrSize > 0)

			 {

				 for($i=0;$i<$batch_order_ids_arrSize;$i++)

				 {

			?>

			       if($("#entity_flag_<?php print $batch_order_ids_arr[$i];?>:checked" ).val() ==1)

				     amountTotal =  amountTotal + parseInt($( "#entity_<?php print $batch_order_ids_arr[$i];?>" ).val());

				   

			<?php

				 }

			 }

			 ?>

			 

			 $( "#entity_show" ).val(amountTotal);

 }

	

function process_payment()

{    

		if ($(".order-checkbox:checked").size() == 0) {

		    alert("Orders for Process amount is not selected");

		    return;

	    }

		

	                            // var batch_order_ids = $( "#batch_order_ids" ).val();

								 var batch_order_ids = Array();

								 var arrAmount = Array();

								 var arrAmountFlag = Array();
								 
								 var checkPaymentFlag = Array();

								 

								 <?php 

								 

								 if($batch_order_ids_arrSize > 0)

								 {

									 for($i=0;$i<$batch_order_ids_arrSize;$i++)

									 {

								?>

								       batch_order_ids[<?php print $i;?>] =  '<?php print $batch_order_ids_arr[$i];?>';

								       arrAmount[<?php print $i;?>] =  $( "#entity_<?php print $batch_order_ids_arr[$i];?>" ).val();
									   
									   checkPaymentFlag[<?php print $i;?>] =   $( "#entity_check_num_<?php print $batch_order_ids_arr[$i];?>" ).val();
//alert($( "#entity_check_num_<?php print $batch_order_ids_arr[$i];?>" ).val());
									   if($("#entity_flag_<?php print $batch_order_ids_arr[$i];?>:checked" ).val() ==1)

									     arrAmountFlag[<?php print $i;?>] =  1;

									   else

									     arrAmountFlag[<?php print $i;?>] =  0;

								<?php

									 }

								 }else{

								 ?>

								  alert('Amount not found');

								 <?php }?>

								 

								var dispatchValues = "";

								dispatchValues = $("#internally_form").serializeArray();

                                

								  

                                dispatchValues.push({'name': 'action', 'value': 'paymentBatchNew'});

                                dispatchValues.push({'name': 'batch_order_ids', 'value': batch_order_ids});

								dispatchValues.push({'name': 'arrAmount', 'value': arrAmount});

								dispatchValues.push({'name': 'arrAmountFlag', 'value': arrAmountFlag});

								dispatchValues.push({'name': 'checkPaymentFlag', 'value': checkPaymentFlag});

                                $("#payment_dialog").nimbleLoader('show');

                                $.ajax({

                                    type: 'POST',

                                    url: '<?=SITE_IN?>application/ajax/payment_batch.php',

                                    dataType: 'json',

                                    data: dispatchValues,

                                    success: function(response) {

                                        if (response.success) {

											//alert("Payment successfully done.");

											alert(response.data.success);

											var str='';

                                          <?php 

											 if($batch_order_ids_arrSize > 0)

											 {

												  

												 for($i=0;$i<$batch_order_ids_arrSize;$i++)

												 {

											?>

												   if($("#entity_flag_<?php print $batch_order_ids_arr[$i];?>:checked" ).val() ==1)

													 str += '<?php print $batch_order_ids_arr[$i];?>,';

											<?php

												 }

											 }

											 ?>

											 if(str=='')

											   document.location.href = "<?=getLink("orders", "batchnew"); ?>";

											 else  

										      document.location.href = "<?=getLink("orders", "batchconfirmnew"); ?>/ids/"+str;

										   //alert('done');

                                        } else {

                                            if (response.errors != undefined) {

                                                $("#payment_dialog .msg-list").html('');

												

                                                for(i in response.errors) {

                                                    $("#payment_dialog .msg-list").append('<li>'+response.errors[i]+'</li>');

													//alert(response.errors[i]);

                                                }

                                                $("#payment_dialog .msg-error").show();

                                                //$("body").scrollTop(0);

                                            } else {

                                                alert("Payment not done. Try again later, please");

                                            }

                                        }

                                    },

                                    error: function(response) {

                                        alert("Payment not done. Try again later, please");

                                    },

                                    complete: function(response) {

                                        $("#payment_dialog").nimbleLoader('hide');

                                    }

                                });

								

                            	

		

    }

	

	

	 $(document).ready(function(){

		$(".entity-cc-info input, .entity-cc-info select,").focus(function() {

			$(".entity-cc-info .error").slideUp(500);

		});

    	function switch_pt(){

			$("#table_internally").show();

			/*

    		if ($("input:radio[name='payment_type_selector']:checked").val() == 'internally') {

                $("#table_gateway").hide();

				<?php if($this->is_carrier==1){ ?>

				  $("#table_terminal").hide();

				<?php }?>

				$("#table_carrier").hide();

                $("#table_internally").show();

            }

			

			else if($("input:radio[name='payment_type_selector']:checked").val() == 'carrier') 

			{

				$("#table_gateway").hide();

			    $("#table_internally").hide();

				<?php if($this->is_carrier==1){ ?>

				$("#table_terminal").hide();

				<?php }?>

				$("#table_carrier").show();

				

			}

			<?php if($this->is_carrier==1){ ?>

			else if($("input:radio[name='payment_type_selector']:checked").val() == 'terminal') 

			{

				$("#table_gateway").hide();

			    $("#table_internally").hide();

				$("#table_carrier").hide();

				$("#table_terminal").show();

			}

			<?php }?>

			else {

				<?php if($this->is_carrier==1){ ?>

				$("#table_terminal").hide();

				<?php }?>

                $("#table_carrier").hide();

			    $("#table_internally").hide();

				

				$("#table_gateway").show();

            }

			

			*/

		}



        $("#date_received").datepicker({dateFormat: 'mm/dd/yy'});

		$("#date_received_carrier").datepicker({dateFormat: 'mm/dd/yy'});

		$("#date_received_terminal").datepicker({dateFormat: 'mm/dd/yy'});

        $("input:radio[name='payment_type_selector']").change(function(){

            switch_pt();

        });

        $("ol.payment_options li").hide();

		$("#li_ch_numb").show();

        $("#method").change(function(){

        	$("ol.payment_options li").hide();

        	switch ($(this).val()) {

        		case "9":

        			$("#li_cc_numb").show();

        			$("#li_cc_type").show();

        			$("#li_cc_exp").show();

        			$("#li_cc_auth").show();

        			break;

       			case "1":

       			case "2":

       			case "3":

       			case "4":

       				$("#li_ch_numb").show();

       				break;

        	}

        });



        $("#cc_type").change(function(){

        	if ($(this).val() == "0") {

        		$("#cc_type_other").show();

        	} else {

        		$("#cc_type_other").hide();

        	}

        });



        $("#internally_form").submit(function(){

        	var form_errors = "";

        	if ($("#date_received").val() == "") form_errors += '<li><b>Date received</b> required</li>';

        	if ($("#from_to").val() == "") form_errors += '<li><b>Payment From\/To</b> required</li>';

        	if ($("#amount").val() == "") form_errors += '<li><b>Amount</b> required</li>';

        	if (form_errors != "") {

        		$("#payment_form_errors ul").html(form_errors);

        		$("#payment_form_errors").slideDown();

        		return false;

        	}

        	return true;

        });

		

		

        $("#payment_form_errors").click(function(){

        	$(this).slideUp();

        });

        $("#internally_form *").focus(function () {

			$("#payment_form_errors").slideUp();

		});



        switch_pt();

	});

	

</script>







<div id="payment_dialog" >

<div id="descBlocks"></div>

<div style="clear:left;"></div>

<div style="clear:left;"></div>

    <div class="msg-error" onclick="$('#payment_dialog .msg-error').hide();" style="display: none"><ul class="msg-list"></ul></div>

    

   <!--table cellpadding="0" cellspacing="0" border="0" class="form-table">

    <tr>

        <td>@payment_type_selector@</td>

    </tr>

</table-->



<form action="<?= getLink('orders/payments/id/'.$_GET['id']) ?>" method="post" id="internally_form">

    	<input type="hidden" name="payment_type" value="internally"/>

		<input type="hidden" name="payment_id" disabled="disabled"/>

        <table cellpadding="0" cellspacing="0" border="0" width="100%" class="form-table" id="table_internally">

            <tr>

                <td>@date_received@</td>

                <td>@method@</td>

            </tr>

            <tr>

                <!--td>@from_to@</td-->

                <td><label for="from_to"><span class="required">*</span>Payment From/To:</label></td>
                <td>
                <select name="from_to" class="form-box-combobox" id="from_to">
                <option value="" >Select One</option>
                <option value="1-3" selected="selected">Company to Carrier</option>
                <option value="2-3">Shipper to Carrier</option
                ></select></td>



                

                <!--td width="112">@transaction_id@</td-->

                <td valign="top" rowspan="2" colspan="2">

                    <ol class="payment_options">

                        <li id="li_ch_numb">

                            <!--label for="ch_numb">Check Number:</label>

                            <input type="text" class="form-box-textfield" id="ch_numb" name="ch_number"/-->

                        </li>

                        <li id="li_cc_numb">

                            <label for="cc_numb">CC# (last 4 digits):</label>

                            <input type="text" class="decimal form-box-textfield" id="cc_numb" name="cc_numb" style="width: 40px;" maxlength="4"/>

                        </li>

                        <li id="li_cc_type">

                        	<label for="cc_type">Credit Card Type:</label>

                        	<select class="form-box-combobox" id="cc_type" name="cc_type" style="width: 110px;">

                        		<option value="">Select One</option>

                        		<?php foreach(Payment::$cctype_name AS $value => $label) : ?>

                        		<option value="<?= $value ?>"><?= $label ?></option>

                        		<?php endforeach; ?>

							</select>

							<input type="text" class="form-box-textfield" id="cc_type_other" name="cc_type_other" style="display: none; width: 100px;" maxlength="64"/>

                        </li>

                        <li id="li_cc_exp">

                            <label for="cc_exp_month">Expiration Date:</label>

                            <select class="form-box-combobox" name="cc_exp_month" id="cc_exp_month" style="width: 110px;">

                            	<option value="">Select Month</option>

                            	<?php for ($i = 1; $i <= 12; $i++) : ?>

                            	<option value="<?= $i ?>"><?= date('F', mktime(0,0,0,$i,1)) ?></option>

                            	<?php endfor; ?>

							</select>

							<select class="form-box-combobox" name="cc_exp_year" style="width: 110px;">

								<option value="">Select Year</option>

								<?php for ($i = (int)date('Y'); $i <= (int)date('Y') + 20; $i++) : ?>

                            	<option value="<?= $i ?>"><?= $i ?></option>

                            	<?php endfor; ?>

							</select>

                        </li>

                        <li id="li_cc_auth">

                        	<label for="cc_auth">Authorization Code:</label>

                            <input type="text" class="form-box-textfield" id="cc_auth" name="cc_auth" />

                        </li>

                    </ol>

                </td>

            </tr>

            <tr>

                <td valign="top" colspan="2">&nbsp;</td>

                

            </tr>

            <tr>

                <td valign="top" colspan="4"><!--input type="button" name="make_payment" value="Make Payment" onclick="process_payment();"/-->

                <div class="form-box-buttons">



<span id="submit_button-submit-btn" style="-webkit-user-select: none;"><input type="button" id="make_payment" value="Make Payment" onclick="process_payment();" style="-webkit-user-select: none;" ></span>



</div>

                </td>

                

            </tr>

        </table>

        </form>
        

</div>