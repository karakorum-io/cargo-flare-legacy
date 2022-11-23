

<script src="<?php
echo SITE_IN;
?>styles/new/assets/vendors/general/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script> 
<script type="text/javascript" src="<?php
echo SITE_IN;
?>/ckeditor/ckeditor.js"></script>
<style type="text/css">
 table.table.table-bordered th {
   font-size: 12px;
 }
 .bottom_bod {
   border: 1px solid #71749d12;
   margin-bottom: 15px;
 }
</style>
<script type="text/javascript">
 var busy = false;
 function updateInternalNotes(data) {
   var rows = "";

   for (i in data) {

     var email = data[i].email;
     var contactname = data[i].sender;

     if (data[i].system_admin == 2) {
       email = "admin@freightdragon.com";
       contactname = "FreightDragon";
     }
     if ((data[i].access_notes == 0)
       || data[i].access_notes == 1
       || data[i].access_notes == 2
       )
     {

       var discardStr = '';
       if (data[i].discard == 1)
         discardStr = ' style="text-decoration: line-through;" ';

       if (data[i].system_admin == 1 || data[i].system_admin == 2)
       {
         rows += '<tr class="grid-body"><td style="white-space:nowrap;" class="grid-body-left" >' + data[i].created + '</td><td id="note_' + data[i].id + '_text"  ' + discardStr + '><b>' + decodeURIComponent(data[i].text) + '</b></td><td>';
       } else if (data[i].priority == 2)
       rows += '<tr class="grid-body"><td class="grid-body-left" >' + data[i].created + '</td><td id="note_' + data[i].id + '_text"  ' + discardStr + '><b style="font-size:12px;color:red;">' + decodeURIComponent(data[i].text) + '</b></td><td>';
       else
         rows += '<tr class="grid-body"><td class="grid-body-left">' + data[i].created + '</td><td id="note_' + data[i].id + '_text"  ' + discardStr + '>' + decodeURIComponent(data[i].text) + '</td><td>';



       rows += '<a href="mailto:' + email + '">' + contactname + '</a></td><td style="white-space: nowrap;" class="grid-body-right"  >';

               <?php //if (!$this->entity->readonly) :  
               ?>

               if ((data[i].access_notes == 0) ||
                 (data[i].access_notes == 1 && (data[i].sender_id == data[i].memberId))
                 || data[i].access_notes == 2
                 )
               {
                 if (data[i].sender_id == data[i].memberId && data[i].system_admin == 0)
                   rows += '<img src="<?= SITE_IN ?>images/icons/strike.png" alt="Discard" title="Discard" width="16" height="16" class="action-icon edit-note" onclick="discardNote(' + data[i].id + ')"/>';

                 if (data[i].system_admin == 0 && data[i].access_notes != 0)
                 {
                   rows += '<img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit" width="16" height="16" class="action-icon edit-note" onclick="editInternalNote(' + data[i].id + ')"/>';

                   rows += '<img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete" width="16" height="16" class="action-icon delete-note" onclick="delInternalNote(' + data[i].id + ')"/>';
                 }
               }
             }
             <?php
             /* else : ?>rows += '&nbsp;';<?php endif; */
             ?>
             rows += '</td></tr>';
           }

           $("#internal_notes_table tbody").html(rows);
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
             success: function (result) {
               if (result.success == true) {
                 updateInternalNotes(result.data);
               } else {
                 alert("Can't discard note. Try again later, please");
               }
               busy = false;
             },
             error: function (result) {
               alert("Can't discard note. Try again later, please");
               busy = false;
             }
           });
         }

         function addInternalNote() {
           if (busy)
             return;
           busy = true;
           var text = $.trim($("#internal_note").val());
           if (text == "")
             return;
           $("#internal_note").val("");
           $.ajax({
             type: "POST",
             url: "<?= SITE_IN ?>application/ajax/notes.php",
             dataType: "json",
             data: {
               action: 'add',
               text: encodeURIComponent(text),
               entity_id: <?= $this->entity->id ?>,
               notes_type: <?= Note::TYPE_INTERNAL ?>
             },
             success: function (result) {
               if (result.success == true) {
                 updateInternalNotes(result.data);
               } else {
                 $("#internal_note").val(text);
                 alert("Can't save note. Try again later, please");
               }
               busy = false;
             },
             error: function (result) {
               $("#internal_note").val(text);
               alert("Can't save note. Try again later, please");
               busy = false;
             }
           });
         }
         function delInternalNote(id) {
           console.log("aaaa");
           if (confirm("Are you sure whant to delete this note?")) {
             if (busy)
               return;
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
               success: function (result) {
                 if (result.success == true) {
                   updateInternalNotes(result.data);
                 } else {
                   alert("Can't delete note. Try again later, please");
                 }
                 busy = false;
               },
               error: function (result) {
                 alert("Can't delete note. Try again later, please");
                 busy = false;
               }
             });
           }
         }
         function editInternalNote(id) {
           var text = $.trim($("#note_" + id + "_text").text());
           $("#note_edit_form textarea").val(text);
           $("#note_edit_form").dialog({
             width: 400,
             modal: true,
             title: "Edit Internal Note",
             resizable: false,
             buttons: [{
               text: "Save",
               click: function () {
                 if ($("#note_edit_form textarea").val() == text) {
                   $(this).dialog("close");
                 } else {
                   if (busy)
                     return;
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
                     success: function (result) {
                       if (result.success == true) {
                         updateInternalNotes(result.data);
                         $("#note_edit_form").dialog("close");
                       } else {
                         alert("Can't save note. Try again later, please");
                       }
                       busy = false;
                     },
                     error: function (result) {
                       alert("Can't save note. Try again later, please");
                       busy = false;
                     }
                   });
                 }
               }
             }, {
               text: "Cancel",
               click: function () {
                 $(this).dialog("close");
                 busy = false;
               }
             }]
           }).dialog("open");
         }

         function addQuickNote() {
           var textOld = $("#internal_note").val();

           var str = textOld + " " + $("#quick_notes").val();
           $("#internal_note").val(str);
         }

       </script>
       <div id="note_edit_form" style="display:none;">    
         <textarea style="width: 95%;height:100px;" class="form-box-textarea" name="note_text"></textarea>
       </div>
       <?php
       if (is_array($_SESSION['searchDataILead']) && $_SESSION['searchCountILead'] > 0) {
       //$_SESSION['searchShowCount'] = $_SESSION['searchShowCount'] + 1;

         $eid             = $_GET['id'];
         $indexSearchData = array_search($eid, $_SESSION['searchDataILead']);

         $nextSearch                       = $indexSearchData + 1;
         $_SESSION['searchShowCountILead'] = $indexSearchData;
         $prevSearch                       = $indexSearchData - 1;

         $entityPrev = $_SESSION['searchDataILead'][$prevSearch];
         $entityNext = $_SESSION['searchDataILead'][$nextSearch];
         ?>
         <div style="float:right; width:170px;">
           <div style="float:left;width:50px;">
            <?php
            if ($_SESSION['searchShowCountILead'] == 0) {
             ?>
             <img src="<?= SITE_IN ?>images/arrow-down-gray.png"   width="40" height="40"/>
             <?php
           } else {
             ?>
             <a href="<?= SITE_IN ?>application/leads/showimported/id/<?= $entityPrev ?>"><img src="<?= SITE_IN ?>images/arrow-down.png"   width="40" height="40"/></a>
             <?php
           }
           ?>
         </div>
         <div style="float:left;width:70px; text-align:center; padding-top:10px;">
          <h3><?php
           print $_SESSION['searchShowCountILead'] + 1;
           ?> - <?php
           print $_SESSION['searchCountILead'];
           ?></h3>
         </div>
         <div style="float:left;width:50px;">
          <?php
          if ($_SESSION['searchShowCountILead'] == ($_SESSION['searchCountILead'] - 1)) {
           ?>
           <img src="<?= SITE_IN ?>images/arrow-up-gray.png"    width="40" height="40"/>
           <?php
         } else {
           ?>
           <a href="<?= SITE_IN ?>application/leads/showimported/id/<?= $entityNext ?>"><img src="<?= SITE_IN ?>images/arrow-up.png"   width="40" height="40"/></a>
           <?php
         }
         ?>
       </div>
     </div>
     <?php
   }
   ?>  
   <?php
   include('lead_menu_imported.php');
   $estatus = 1;
   $strL    = "Quote";
   if ($this->entity->status == Entity::STATUS_ACTIVE || $this->entity->status == Entity::STATUS_LDUPLICATE || $this->entity->status == Entity::STATUS_UNREADABLE || $this->entity->status == Entity::STATUS_ONHOLD || $this->entity->status == Entity::STATUS_ARCHIVED) {
     $strL    = "Lead";
     $estatus = 0;
   }
   ?>
 </div>
 <!--Cards-->
 <div class="row">
  <div class="order-1">
   <!-- h3><?php
      print $strL;
      ?> #<?= $this->entity->number ?> Detail</h3> -->
      <h2 class="kt-font-boldest" >Current Status:&nbsp;<?php
        print "<span class='black'>" . $strL . "</span>";
        ?></h2>
      </div>
      <div  class="col-8">
       <div class="kt-portlet__body">
        <!--begin::Accordion-->
        <div class="accordion bottom_bod" id="accordionExample1">
         <div class="card">
          <div class="card-header" id="headingOne">
           <div class="card-title " data-toggle="collapse" data-target="#QuoteInformation" >
            <strong class="kt-link kt-link--state kt-link--success"> Quote Information </strong>
          </div>
        </div>
        <div id="QuoteInformation" class="collapse show"  style="">
         <div class="card-body">
          <?php
          $sourceName = "";

          if ($estatus == 0) {
           if ($this->entity->source_id > 0) {
             $source = new Leadsource($this->daffny->DB);
             $source->load($this->entity->source_id);
             $sourceName = $source->company_name;
           }
         } else {
           if ($this->entity->source_id > 0) {
             $source = new Leadsource($this->daffny->DB);
             $source->load($this->entity->source_id);
             $sourceName = $source->company_name;
           } elseif ($this->entity->referred_id > 0) {
             $sourceName = $this->entity->referred_by;
           }
         }
         $assigned    = $this->entity->getAssigned();
         $shipper     = $this->entity->getShipper();
         $origin      = $this->entity->getOrigin();
         $destination = $this->entity->getDestination();
         $vehicles    = $this->entity->getVehicles();
         ?>  
         <?php
         $source      = $this->entity->getSource();
         $assigned    = $this->entity->getAssigned();
         $shipper     = $this->entity->getShipper();
         $origin      = $this->entity->getOrigin();
         $destination = $this->entity->getDestination();
         $vehicles    = $this->entity->getVehicles();
         ?>
         <?php {
           $code         = substr($shipper->phone1, 0, 3);
           $areaCodeStr  = "";
           $areaCodeRows = $this->daffny->DB->selectRow("*", "app_area_code", "WHERE  AreaCode='" . $code . "'");

           if (!empty($areaCodeRows)) {
             $areaCodeStr = "<b>" . $areaCodeRows['StdTimeZone'] . "-" . $areaCodeRows['statecode'] . "</b>";
           }

           $code          = substr($shipper->phone2, 0, 3);
           $areaCodeStr2  = "";
           $areaCodeRows2 = $this->daffny->DB->selectRow("*", "app_area_code", "WHERE  AreaCode='" . $code . "'");

           if (!empty($areaCodeRows2)) {
             $areaCodeStr2 = "<b>" . $areaCodeRows2['StdTimeZone'] . "-" . $areaCodeRows2['statecode'] . "</b>";
           }
           $code          = substr($shipper->cell, 0, 3);
           $areaCodeStr3  = "";
           $areaCodeRows3 = $this->daffny->DB->selectRow("*", "app_area_code", "WHERE  AreaCode='" . $code . "'");

           if (!empty($areaCodeRows3)) {
             $areaCodeStr3 = "<b>" . $areaCodeRows3['StdTimeZone'] . "-" . $areaCodeRows3['statecode'] . "</b>";
           }
         }
         ?>
         <div  class="row">
           <div class="col-6">
            <table class="table table-bordered">
             <tr>
              <th><strong>Name</strong></th>
              <td  align="center"  class="kt-font-danger">:</td>
              <td align="left"  style="line-height:15px;"><?= $shipper->fname ?> <?= $shipper->lname ?></td>
            </tr>
            <tr>
              <th ><strong>Company</strong></th>
              <td align="center">:</td>
              <td ><strong><?= $shipper->company; ?></strong></td>
            </tr>
            <tr>
              <th style="line-height:15px;"><strong>Email</strong></th>
              <td  align="center">:</td>
              <td>
               <a href="mailto:<?= $shipper->email ?>"><?= $shipper->email ?></a>
             </td>
           </tr>
           <tr>
            <th ><strong>Phone 1</strong></th>
            <td  align="center">:</td>
            <td><?= formatPhone($shipper->phone1); ?> <?= $areaCodeStr ?></td>
          </tr>
          <tr>
            <th ><strong>Phone 2</strong></th>
            <td align="center">:</td>
            <td><?= formatPhone($shipper->phone2); ?> <?= $areaCodeStr2 ?></td>
          </tr>
          <tr>
            <th>Mobile</th>
            <td  align="center">:</td>
            <td><?= formatPhone($shipper->mobile); ?> <?= $areaCodeStr3 ?></td>
          </tr>
          <tr>
            <th ><strong>Fax</strong></th>
            <td  align="center">:</td>
            <td><?= formatPhone($shipper->fax); ?>
            </td>
          </tr>
        </table>
      </div>
      <div class="col-6">
        <table class=" table table-bordered" >
         <tr>
          <th>1st Avail. Pickup</th>
          <td  align="center"><b>:</b></td>
          <td><?= $this->entity->getShipDate("m/d/y") ?></td>
        </tr>
        <tr>
          <th class="th_head"><strong>Ship Via: </strong></th>
          <td  align="center"  style="line-height:15px;"><b>:</b></td>
          <td align="left"  style="line-height:15px;"><span style="color:red;weight:bold;"><?= $this->entity->getShipVia() ?></span></td>
        </tr>
        <?php
        if (is_numeric($this->entity->distance) && ($this->entity->distance > 0)):
          ?>
        <tr>
          <th  style="line-height:15px;"><strong>Mileage: </strong></th>
          <td  align="center"><b>:</b></td>
          <td>
           <?= number_format($this->entity->distance, 0, "", "") ?> mi($ <?= number_format(($this->entity->getCarrierPay(false) / $this->entity->distance), 2, ".", ",") ?>/mi)&nbsp;&nbsp;(<span class='red' onclick="mapIt(<?= $this->entity->id ?>);">MAP IT</span>)</strong>
         </td>
       </tr>
       <?php
       endif;
       ?>
       <tr>
        <th>
         <strong>Assigned to</strong>
       </th>
       <td  align="center"><b>:</b></td>
       <td><?= $assigned->contactname ?></td>
     </tr>
     <?php
     if ($this->entity->referred_by != "") {
      ?>
      <tr>
        <th style="line-height:15px;"><strong>Source</strong></th>
        <td  align="center"><b>:</b></td>
        <td><?= $sourceName ?>
        </td>
      </tr>
      <?php
    } else {
      $member = new Member($this->daffny->DB);
      $member->load($this->entity->assigned_id);

      if ($member->hide_lead_sources == 0) {
        ?>
        <tr>
          <th>
           <strong>Source</strong>
         </th>
         <td  align="center"><b>:</b></td>
         <td><?= $source->company_name ?></td>
       </tr>
       <?php
     }
   }
   ?>                  
 </table>
</div>
</div>
</div>
</div>
</div>
</div>
<!--end::Accordion-->
</div>
</div>
<div  class="col-4">
 <div class="kt-portlet__body">
  <!--begin::Accordion-->
  <div class="accordion bottom_bod" id="accordionExample123">
   <div class="card">
    <div class="card-header" id="headingOne123">
     <div class="card-title collapsed" data-toggle="collapse" data-target="#QuoteEstimate" aria-expanded="false" aria-controls="collapseOne1">
      <strong class="kt-link kt-link--state kt-link--success">Quote Estimate </strong>
    </div>
  </div>
  <div id="QuoteEstimate" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample1" style="">
   <div class="card-body">
    <div  class="col-lg-12">
     <img style="vertical-align: middle" src="<?= SITE_IN ?>images/icons/dollar.png" alt="Total Tariff" title="Total Tariff" width="16" height="16"/>
     <strong>Total Amount: </strong><?= $this->entity->getTotalTariff() ?>
   </div>
   <div  class="col-lg-12">
     <img style="vertical-align: middle" src="<?= SITE_IN ?>images/icons/truck.png" alt="Tariff to Shipper" title="Tariff to Shipper" width="16" height="16"/>
     <strong>&nbsp;&nbsp;Carrier Fee: </strong><?= $this->entity->getCarrierPay() ?>
   </div>
   <div  class="col-lg-12">
     <img style="vertical-align: middle" src="<?= SITE_IN ?>images/icons/person.png" alt="Tariff to Shipper" title="Tariff to Shipper" width="16" height="16"/>
     <strong>&nbsp;&nbsp;Broker Fee: </strong><?= $this->entity->getTotalDeposit() ?>
     <input type="hidden" name="lead_tariff_<?= $this->entity->id ?>" id="lead_tariff_<?= $this->entity->id ?>" value="<?= $this->entity->getTotalTariff(false) ?>" />
     <input type="hidden" name="lead_deposit_<?= $this->entity->id ?>" id="lead_deposit_<?= $this->entity->id ?>" value="<?= $this->entity->getTotalDeposit(false) ?>" />
   </div>
 </div>
</div>
</div>
<!--end::Accordion-->
</div>
</div>
</div>
<!--  -->
<div  class="col-12">
 <div  class="row">
  <div class="col-6">
   <div class="kt-portlet__body">
    <div class="accordion bottom_bod" id="accordionExample1">
     <div class="card">
      <div class="card-header" id="headingOne">
       <div class="card-title collapsed" data-toggle="collapse" data-target="#PickupInformation" aria-expanded="false" aria-controls="collapseOne1">
        <strong class="kt-link kt-link--state kt-link--success">  Pickup Information </strong>
      </div>
    </div>
    <div id="PickupInformation" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample1" style="">
     <div class="card-body">
      <table class="table table-bordered">
       <tr>
        <th><strong>City</strong></th>
      </td>
      <td align="left" >
       <span class="like-link"onclick="window.open('<?= $origin->getLink() ?>')"><?= $origin->city ?></span>
     </td>
   </tr>
   <th ><strong>State</strong></th>
   <td align="left"  style="line-height:15px;"><?= $origin->state ?></td>
 </tr>
 <th ><strong>Zip</strong></th>
 <td align="left"  ><?= $origin->zip ?></td>
</table>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="col-6">
 <div class="kt-portlet__body">
  <div class="accordion" id="accordionExample1">
   <div class="card">
    <div class="card-header" id="headingOne">
     <div class="card-title collapsed" data-toggle="collapse" data-target="#Dropoff_Information" aria-expanded="false" aria-controls="collapseOne1">
      <strong class="kt-link kt-link--state kt-link--success"> Dropoff Information </strong>
    </div>
  </div>
  <div id="Dropoff_Information" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample1" style="">
   <div class="card-body">
    <table class="table table-bordered"  >
     <tr>
      <th  ><strong>City</strong></th>
      <td align="left" >
       <span class="like-link"onclick="window.open('<?= $destination->getLink() ?>')"><?= $destination->city ?></span>
     </td>
   </tr>
   <tr>
    <th>
     <strong>State</strong>
   </th>
   <td  align="left" ><?= $destination->state ?></td>
 </tr>
 <tr>
  <th ><strong>Zip</strong></th>
  <td align="left"  >
   <?= $destination->zip ?>
 </td>
</tr>
</table>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="col-12 col-sm-12">
 <div class="kt-portlet__body">
  <!--begin::Accordion-->
  <div class="accordion bottom_bod" id="accordionExample1Vehicle">
   <div class="card">
    <div class="card-header" id="headingOneVehicle">
     <div class="card-title collapsed" data-toggle="collapse" data-target="#Vehicle" aria-expanded="false" aria-controls="collapseOne1">
      <strong class="kt-link kt-link--state kt-link--success">Vehicle(s) Information </strong>
    </div>
  </div>
  <div id="Vehicle" class="collapse show"  style="">
   <div class="card-body">
    <table  class=" table table-bordered" >
     <th  >S.No.</th>
     <th  ><?= Year ?></th>
     <th  ><?= Make ?></th>
     <th  ><?= Model ?></th>
     <th  ><?= Inop ?></th>
     <th  ><?= Type ?></th>
                     <th  ><?= Vin #  
                      ?></th>
                     <th  ><?= Lot # 
                      ?></th>
                      <th  >Carrier Fee</th>
                      <th  >Broker Fee</th>
                      <th  >Total Amount</th>
                      <?php
                      $vehiclecounter = 0;
                      foreach ($vehicles as $vehicle):
                        $vehiclecounter = $vehiclecounter + 1;
                      ?>
                      <tr>
                        <td  ><?= $vehiclecounter ?></td>
                        <td  ><?= $vehicle->year ?></td>
                        <td  ><?= $vehicle->make ?></td>
                        <td  ><?= $vehicle->model ?></td>
                        <td  style="padding-left:5px;"> <?php
                         print $vehicle->inop == 0 ? "No" : "Yes";
                         ?></td>
                         <td  ><?= $vehicle->type ?></td>
                         <td  > <?php
                           print $vehicle->vin;
                           ?></td>
                           <td  > <?php
                             print $vehicle->lot;
                             ?></td>
                             <td  ><?= $vehicle->carrier_pay ?></td>
                             <td  ><?= $vehicle->deposit ?></td>
                             <td  ><?= $vehicle->tariff ?></td>
                           </tr>
                           <?php
                           endforeach;
                           ?>
                         </table>
                       </div>
                     </div>
                   </div>
                 </div>
               </div>
             </div>
             <div class="col-12">
               <div class="kt-portlet__body">
                <div class="accordion bottom_bod" id="accordionExample12345">
                 <div class="card">
                  <div class="card-header" id="headingOne">
                   <div class="card-title collapsed" data-toggle="collapse" data-target="#InternalNote"  aria-controls="collapseOne1">
                    <strong class="kt-link kt-link--state kt-link--success">  Internal Note</strong>
                  </div>
                </div>
                <div id="InternalNote" class="collapse show " aria-labelledby="headingOne" data-parent="#accordionExample1" style="">
                 <div class="card-body">
                  <?php
                     if (($this->entity->creator_id == (int) $_SESSION['member_id'] || $this->entity->assigned_id == (int) $_SESSION['member_id'] || $_SESSION['member_id'] == 1)): //if (!$this->entity->readonly) : 
                     ?>      
                     <div  class="row">
                       <div class="col-4"> 
                        <textarea class="form-box-textarea form-control"  maxlength="1000" id="internal_note"></textarea> 
                      </div>
                      <div class="col-4" ">
                        Quick Notes
                        <select name="quick_notes" id="quick_notes" class="form-control" onchange="addQuickNote();">
                         <option value="">--Select--</value>
                           <option value="Emailed: Prospect.">Emailed: Prospect.</value>
                             <option value="Emailed: Bad Email.">Emailed: Bad Email.</value>
                               <option value="Faxed: Prospect.">Faxed: Prospect.</value>
                                 <option value="Faxed: Bad Fax.">Faxed: Bad Fax.</value>
                                   <option value="Texted: Prospect.">Texted: Prospect.</value>
                                     <option value="Texted: Bad Mobile.">Texted: Bad Mobile.</value>
                                       <option value="Phoned: No Voicemail.">Phoned: No Voicemail.</value>
                                         <option value="Phoned: Left Message.">Phoned: Left Message.</value>
                                           <option value="Phoned: Spoke to Prospect.">Phoned: Spoke to Prospect.</value>
                                             <option value="Phoned: Bad Number.">Phoned: Bad Number.</value>
                                               <option value="Phoned: Not Intrested.">Phoned: Not Intrested.</value>
                                                 <option value="Phoned: Do Not Call.">Phoned: Do Not Call.</value>
                                                 </select>
                                               </div>
                                               <div class="col-4" style="padding-top: 18px">
                                                <?= functionButton('Add Note', 'addInternalNote()') ?>
                                              </div>
                                            </div>
                                            <?php
                                            endif;
                                            ?>   
                                            <table class=" table table-bordered" id="internal_notes_table" style="margin-top: 20px">
                                             <thead>
                                              <tr >
                                               <td class="grid-head-left"><?= $this->order->getTitle('created', 'Date') ?></td>
                                               <td >Note</td>
                                               <td>User</td>
                                               <td >Action</td>
                                             </tr>
                                           </thead>
                                           <tbody>
                                            <?
                                            if (count($notes[Note::TYPE_INTERNAL]) == 0):
                                             ?>
                                           <tr class="grid-body">
                                             <td colspan="4" class="grid-body-left grid-body-right" align="center"><i>No notes available.</i></td>
                                           </tr>
                                           <?
                                           else:
                                             ?>
                                           <?php
                                           foreach ($notes[Note::TYPE_INTERNAL] as $note):
                                             ?>
                                           <?php
                                           $sender = $note->getSender();

                                           $email       = $sender->email;
                                           $contactname = $sender->contactname;
                                           if ($note->system_admin == 2) {
                                             $email       = "admin@freightdragon.com";
                                             $contactname = "FreightDragon";
                                           }

                                           if (($_SESSION['member']['access_notes'] == 0) || $_SESSION['member']['access_notes'] == 1 || $_SESSION['member']['access_notes'] == 2) {
                                             ?>
                                             <tr class="grid-body" >
                                               <td style="white-space:nowrap;"  class="grid-body-left" <?php
                                               if ($note->priority == 2) {
                                                ?> style="color:#FF0000"<?php
                                              }
                                              ?>><?= $note->getCreated("m/d/y h:i a") ?></td>
                                              <td id="note_<?= $note->id ?>_text" style=" <?php
                                                if ($note->discard == 1) {
                                                  ?>text-decoration: line-through;<?php
                                                }
                                                ?><?php
                                                if ($note->priority == 2) {
                                                  ?>color:#FF0000;<?php
                                                }
                                                ?>"><?php
                                                if ($note->system_admin == 1 || $note->system_admin == 2) {
                                                  ?><b><?= $note->getText() ?></b><?php
                                                } elseif ($note->priority == 2) {
                                                  ?><b style="font-size:12px;"><?= $note->getText() ?></b><?php
                                                } else {
                                                  ?><?= $note->getText() ?><?php
                                                }
                                                ?></td>
                                                <td style="text-align: center;" <?php
                                                if ($note->priority == 2) {
                                                  ?>style="color:#FF0000"<?php
                                                }
                                                ?>><a href="mailto:<?= $email ?>"><?= $contactname ?></a></td>
                                                <td class="grid-body-right" style="white-space: nowrap;" <?php
                                                if ($note->priority == 2) {
                                                  ?>style="color:#FF0000"<?php
                                                }
                                                ?>>
                                                <?php
                                                if (!$this->entity->readonly):
                                                 ?>
                                               <?php
                                               if (($_SESSION['member']['access_notes'] == 0) || ($_SESSION['member']['access_notes'] == 1 && ($note->sender_id == (int) $_SESSION['member_id'])) || $_SESSION['member']['access_notes'] == 2) {

                                                 if ($note->sender_id == (int) $_SESSION['member_id'] && $note->system_admin == 0) {
                                                   ?>
                                                   <img src="<?= SITE_IN ?>images/icons/strike.png" alt="Discard" title="Discard" width="16" height="16" class="   action-icon edit-note" onclick="discardNote(<?= $note->id ?>)"/>    
                                                   <?php
                                                 }

                                                 if ($note->system_admin == 0 && $_SESSION['member']['access_notes'] != 0) {
                                                   ?>  
                                                   <img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit" width="16" height="16" class="action-icon edit-note" onclick="editInternalNote(<?= $note->id ?>)"/>
                                                   <img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete" width="16" height="16" class="action-icon delete-note" onclick="delInternalNote(<?= $note->id ?>)"/>
                                                   <?php
                                                 }
                                               }
                                               ?>
                                               <?php
                                               else:
                                                 ?>&nbsp;<?php
                                               endif;
                                               ?>
                                             </td>
                                           </tr>
                                           <?php
                                         }
                                         ?>
                                         <?php
                                         endforeach;
                                         ?>
                                         <?php
                                         endif;
                                         ?>
                                       </div>
                                     </div>
                                   </div>
                                 </div>
                                 <!--end::Accordion-->
                               </div>
                             </div>
                           </div>
<!-- <div class="lead-info"  style="width:94%;float: left; margin-bottom:10px;">
   <p class="block-title">Quote Estimate</p>    
   <div>
       <img style="vertical-align: middle" src="<?= SITE_IN ?>images/icons/dollar.png" alt="Total Tariff" title="Total Tariff" width="16" height="16"/>
       <strong>&nbsp;&nbsp;Total Amount: </strong><?= $this->entity->getTotalTariff() ?><br />
       <img style="vertical-align: middle" src="<?= SITE_IN ?>images/icons/truck.png" alt="Tariff to Shipper" title="Tariff to Shipper" width="16" height="16"/>
       <strong>&nbsp;&nbsp;Carrier Fee: </strong><?= $this->entity->getCarrierPay() ?><br />
       <img style="vertical-align: middle" src="<?= SITE_IN ?>images/icons/person.png" alt="Tariff by Customer" title="Tariff by Customer" width="16" height="16"/>
       <strong>&nbsp;&nbsp;Broker Fee: </strong><?= $this->entity->getTotalDeposit() ?>
       <input type="hidden" name="lead_tariff_<?= $this->entity->id ?>" id="lead_tariff_<?= $this->entity->id ?>" value="<?= $this->entity->getTotalTariff(false) ?>" />
       <input type="hidden" name="lead_deposit_<?= $this->entity->id ?>" id="lead_deposit_<?= $this->entity->id ?>" value="<?= $this->entity->getTotalDeposit(false) ?>" />
   </div>
 </div> -->
<!-- <?php
   if ($this->entity->type == 4) {
   ?>
   <div class="lead-info"  style="width:94%;float: left; margin-bottom:10px;"> <p class="block-title">Status<?php
      print "<span class='black'>: Quote from a Created Lead</span>";
      ?></p>  <div>       <strong>New Quote: </strong><?php
      print date("m/d/y h:i a", strtotime($this->entity->created));
      ?>  </div></div>
   <?php
      } elseif ($this->entity->type == 1) {
      ?>
   <div class="quote-info" style="width:94%;float: left; margin-bottom:10px;">
       <p class="block-title">Status<?php
      print "<span class='black'>: Lead</span>";
      ?></p>
       <div>
           <strong>New Lead: </strong><?= $this->entity->getReceived("m/d/y h:i a") ?>
       </div>
   </div> 
   <?php
      } else {
      ?>
   <div class="quote-info" style="width:94%;float: left; margin-bottom:10px;">
       <p class="block-title">Status<?php
      print "<span class='black'>: Lead</span>";
      ?></p>
       <div>
           <strong>New Lead: </strong><?= $this->entity->getReceived("m/d/y h:i a") ?>
       </div>
   </div> 
   <?php
      }
      ?>
   <?php
      if ($this->entity->lead_type == 1) {
      ?>
   <div style="clear:left;"></div>
   <?php
      }
      ?>     
    -->
  </tr>
</table> 
<!-- <script>
   /**
    * Fetching deposite percentage and amount from app_defaultsettings
    */
   $(document).ready(function(){
       /**
        * Auto Quote button adjustments
        */
       var Tariff = '<?php
      echo $this->entity->getTotalTariff();
      ?>';
           if(Tariff !== "$ 0.00"){
               $("#quoteButtonTD").hide();
           }
       
      $.ajax({
           type: 'POST',
           url: BASE_PATH + 'application/ajax/accounts.php',
           dataType: 'json',
           data: {
               action: 'getOrderDeposite',
               owner_id: '<?php
      echo $_SESSION['member']['parent_id'];
      ?>'
           },
           success: function (response) {
               $("#order_deposit").val(response.response.order_deposit);
               $("#order_deposit_type").val(response.response.order_deposit_type);
               $("#auto_quote_api_pin").val(response.response.auto_quote_api_pin);
               $("#auto_quote_api_key").val(response.response.auto_quote_api_key);
           }
       });
   });
 </script> -->
 <input type="hidden" id="auto_quote_api_pin" value="">
 <input type="hidden" id="auto_quote_api_key" value="">
 <input type="hidden" id="order_deposit" value="">
 <input type="hidden" id="order_deposit_type" value="">
 <!--including auto quotes JavaScript library-->
 <script src="<?php
 echo SITE_IN;
 ?>/core/js/core.js"></script>

