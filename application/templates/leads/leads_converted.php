<script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script> 

<style type="text/css">
	  a.order-desc {
    color: #212529 !important;
}
</style>

<?php //if ($this->status == Entity::STATUS_ACTIVE || $_GET['leads']=="search") : ?>
<script type="text/javascript">
    function saveQuotes(email) {

        if ($(".entity-checkbox:checked").length == 0) {
            Swal.fire("You should check at least one Lead to save Quotes");
            return;
        }
		
        var ajData = [];
        $(".entity-checkbox:checked").each(function(){
            if ($("#lead_tariff_"+$(this).val()).size() > 0) {
                ajData.push('{"entity_id":"'+$(this).val()+'","tariff":"'+$('#lead_tariff_'+$(this).val()).val()+'","deposit":"'+$('#lead_deposit_'+$(this).val()).val()+'"}');
            }
        });
		if (ajData.length == 0) {
			Swal.fire("You have no quote data");
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
					document.location.href = document.location.href;
				} else {
					Swal.fire("Can't save Quote(s)");
				}
			},
            complete: function(response) {
				$("body").nimbleLoader('hide');
            }
        });
    }
	
function convertToOrder() {
	//Swal.fire('test');
	if ($(".entity-checkbox:checked").length == 0) {

             Swal.fire("You have no selected items.");

			return false;        

        }

		if ($(".entity-checkbox:checked").length > 1) {

             Swal.fire("Error: You may convert one lead at a time.");

			return false;        

        }
	/*
	   if ($(".entity-checkbox:checked").size() == 0) {
           Swal.fire("You have no selected items.");
        } else 
		*/
		{
			var entity_ids = [];
			$(".entity-checkbox:checked").each(function(){
				entity_ids.push($(this).val());
			});      
	
        $.ajax({
            type: "POST",
            url: "<?= SITE_IN ?>application/ajax/entities.php",
            dataType: "json",
            data: {
                action: "LeadtoOrderCreated",
                entity_ids: entity_ids.join(',')
            },
            success: function (result) {
                if (result.success == true) {
                  // document.location.reload();
				  document.location.href = result.url;
				  
                } else {
                    Swal.fire("Can't convert Order. Try again later, please");
                }
            },
            error: function (result) {
                Swal.fire("Can't convert Order. Try again later, please");
            }
        });
    }
}	


function reassignOrdersDialog()
{
	  if ($(".entity-checkbox:checked").length == 0) 
		{            
		   Swal.fire("Leads not selected");            
		     return;        
		} 
	  $("#reassignCompanyDiv").dialog("open");
}
	
	 

function reassignOrders(member) 
{		
        var member_id = 0;		
        member_id = member;		
		if ( member_id == 0 ) 
		{			
		  Swal.fire("You must select member to assign");			
		  return;		
		}        
		if ($(".entity-checkbox:checked").size() == 0) 
		{            
		   Swal.fire("Leads not selected");            
		     return;        
		}        
		//var entity_id = $(".entity-checkbox:checked").val();        
		var entity_ids = [];       
		//entity_ids.push(entity_id); 
		 $(".entity-checkbox:checked").each(function(){
            entity_ids.push($(this).val());
        });
		$("#reassignCompanyDiv").nimbleLoader('show');
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
						  Swal.fire("Reassign failed. Try again later, please.");   
						  $("#reassignCompanyDiv").nimbleLoader('hide');
						  }            
					},           
					error: function(response) {                
					   Swal.fire("Reassign failed. Try again later, please.");  
					   $("#reassignCompanyDiv").nimbleLoader('hide');
					   } ,
					   complete: function (res) {

                        $("#reassignCompanyDiv").nimbleLoader('hide');

                    }
			});	
	}
	
	$("#reassignCompanyDiv").dialog({
	modal: true,
	width: 300,
	height: 140,
	title: "Reassign Lead",
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
</script>
<?php //endif; ?>
<div style="display:none" id="notes">notes</div>
<br/>


    
  <div class="kt-portlet">
  	<div class="kt-portlet__body">

		<div class="row">
			<div  class="col-12">
				<?php if ($this->status != Entity::STATUS_ARCHIVED || $_GET['leads']=="search") { ?>
				<?php }else{ ?>
				<?= functionButton('Uncancel', 'changeStatusLeads(1)') ?>
				<?php } ?>
			</div>
		</div>



<div id="nimble_dialog_button" >



<table class="table table-bordered" id="leads_convert">
	<tbody>      
		<tr>
			<th class="grid-head-left">
			<?php if (isset($this->order)) : ?>
				<?=$this->order->getTitle("id", "ID")?>
				<?php else : ?>ID<?php endif; ?>
			</th>
			<th>
				<?php if($this->status == Entity::STATUS_ARCHIVED){?>
					Received/Created
				<?php }else{?>
				<?php if (isset($this->order)) : ?>
					<?=$this->order->getTitle("date_converted", "Created")?>
					<?php else : ?>Received<?php endif; ?>
				<?php }?>
			</th>
			<th>Notes</th>
			<th>   
				<?php if (isset($this->order)) : ?>
				Shipper
				<?php //print $this->order->getTitle("shipper", "Shipper");?>
				<?php else : ?>Shipper<?php endif; ?>
			</th>
			<th>Hours of Operations</th>
			<th>Shipment Types</th>
			<th>Units/month</th>
			<th>Converted To</th>
		</tr>
		<?php if (count($this->entities) == 0): ?>
	   <tr class="grid-body">
		<td colspan="8" align="center" class="grid-body-left grid-body-right"><i>No records</i></td>
	   </tr>
       <?php endif; ?>
	<?php 
    
    $searchData = array();
   foreach($this->entities as $i => $entity) :
    $searchData[] = $entity['entityid'];
	
	if($entity['type']==2)
			   $urlRedirect = "quotes";
	 elseif($entity['type']==3)
			   $urlRedirect = "orders";
	
	?>
    <tr id="lead_tr_<?= $entity['entityid'] ?>" class="grid-body<?=($i == 0 ? " " : "")?><?=($entity->duplicate)?' duplicate':''?>">
	<td align="center" class="grid-body-left">
				<?php if (!$entity['readonly']) : ?>
				<label class="kt-checkbox kt-checkbox--success">
				<input type="checkbox" value="<?= $entity['entityid'] ?>" class="entity-checkbox"> 
				<span></span>
				</label>

				<?php endif; ?>
<a href="<?= SITE_IN ?>application/<?=$urlRedirect?>/show/id/<?= $entity['entityid'] ?>" class=" kt-badge  kt-badge--info kt-badge--inline kt-badge--pill order_id"  ><?= $entity['number'] ?></a><br/>
				<a href="<?= SITE_IN ?>application/<?=$urlRedirect?>/history/id/<?= $entity['entityid'] ?>" class="kt-badge  kt-badge--success kt-badge--inline kt-badge--pill" style="margin-left: 20px; margin-top: 4px">History</a>
                <?php if($this->status == Entity::STATUS_ARCHIVED){?>
						<?php if($entity['lead_type']==1){?>
                             <br/>Created
                        <?php }else{?>
                             <br/>Imported
                        <?php }?>
                 <?php }?>       
	</td>
	<td valign="top" style="white-space: nowrap;">
          <span> <?= date("m/d/y", strtotime($entity['cdate_converted']));?> </span>
				   <?php //$assigned = $entity->getAssigned();  ?>
                    <br>Assigned to:<br/> <strong class="kt-font-success"><?= $entity['AssignedName'] ?></strong><br />  
                     
			</td>
<?php
            if(trim($entity['shipperphone1'])!="")
            {                   
                $code     = substr($entity['shipperphone1'], 0, 3);
                $areaCodeStr="";
                $areaCodeRows = $this->daffny->DB->selectRow("*", "app_area_code", "WHERE  AreaCode='".$code."'");
                    if (!empty($areaCodeRows)) {
                        $areaCodeStr = "<b>".$areaCodeRows['StdTimeZone']."-".$areaCodeRows['statecode']."</b>";
                    }
            }
            if(trim($entity['shipperphone2'])!="")
            {
                                                        
                $code     = substr($entity['shipperphone2'], 0, 3);
                $areaCodeStr2="";                
                $areaCodeRows2 = $this->daffny->DB->selectRow("*", "app_area_code", "WHERE  AreaCode='".$code."'");

                    if (!empty($areaCodeRows2)) {
                        $areaCodeStr2 = "<b>".$areaCodeRows2['StdTimeZone']."-".$areaCodeRows2['statecode']."</b>";
                    }

            }
            if($entity['shipperphone1_ext']!='') $phone1_ext = " <b>X</b> ".$entity['shipperphone1_ext'];
            if($entity['shipperphone2_ext']!='') $phone2_ext = " <b>X</b> ".$entity['shipperphone2_ext'];
?> 
	<td>
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
			<?php //$notes = $entity->getNotes();?>
				<?//= notesIcon($entity['entityid'], count($notes[Note::TYPE_FROM]), Note::TYPE_FROM, $entity['readonly']) ?>
				<?//= notesIcon($entity['entityid'], count($notes[Note::TYPE_TO]), Note::TYPE_TO, $entity['readonly']) ?>
				<?//= notesIcon($entity['entityid'], count($notes[Note::TYPE_INTERNAL]), Note::TYPE_INTERNAL, $entity['readonly']) ?>
				
				<?= notesIcon($entity['entityid'], $NotesCount1, Note::TYPE_FROM, $entity['status'] == Entity::STATUS_ARCHIVED) ?>
                <?= notesIcon($entity['entityid'], $NotesCount2, Note::TYPE_TO, $entity['status'] == Entity::STATUS_ARCHIVED) ?>
                <?= notesIcon($entity['entityid'], $NotesCount3, Note::TYPE_INTERNAL, $entity['status'] == Entity::STATUS_ARCHIVED,$countNewNotes) ?>
			</td>
	        <td>
			<?php //$shipper = $entity->getShipper();?>
				 <div class="kt-font-bold kt-font-primary shipper_name"><?= $entity['shipperfname'] ?> <?= $entity['shipperlname'] ?><br/></div>

                <?php if($entity['shippercompany']!=""){?><div class="shipper_company"><b><?= $entity['shippercompany']?></b><br /></div><?php }?>

                <?php if($entity['shipperphone1']!=""){?><div class="shipper_number"><?= formatPhone($entity['shipperphone1']) ?>
                    <?php }?>
                                        <?= $phone1_ext;?> 
                                        <?= $areaCodeStr;?><br/></div>
                <?php if($entity['shipperphone2']!=""){?><div class="shipper_number"><?= formatPhone($entity['shipperphone2']) ?>                                        <?php }?>
                                         <?= $phone2_ext;?> 
                                        <?= $areaCodeStr2;?></div>

                <?php if($entity['shipperemail']!=""){?><a href="mailto:<?= $entity['shipperemail'] ?>"><div class=" kt-font-bold kt-font-danger shipper_email"><?= $entity['shipperemail'] ?><br/></div></a><?php }?>

                <div class="shipper_referred">
                	<span class="kt-badge kt-badge--primary kt-badge--dot"><?php if($entity['referred_by'] != ""){?></span>

				  Referred By <b><?= $entity['referred_by'] ?></b><br>

				<?php }?></div>
			</td>
			<?php 
				$shipment_type = "--";
			   if($entity['shippershipment_type']==1)
			      $shipment_type = "Full load";
			   elseif($entity['shippershipment_type']==2)
			      $shipment_type = "Singles";
			   elseif($entity['shippershipment_type']==3)
			      $shipment_type = "Both";	
             ?>   
              <td><?= $entity['shipper_hours'] ?></td>
             <td><?= $shipment_type ?></td>
             <td><?= $entity['shippershipment_type'] ?></td>
               <td>
             <?php
			 if($entity['type']==2)
			    print "<b>Quote</b>";
			 elseif($entity['type']==3)
			    print "<b >Order</b>";
			 ?>
                 <?php print " on " . date("m/d/y", strtotime($entity['cdate_converted']));
				 ?>
                 <br />
                 <b>ID</b>: <a href="<?= SITE_IN ?>application/<?=$urlRedirect?>/show/id/<?= $entity['entityid'] ?>" class="kt-font-bold kt-font-primary"><?php print $entity['cnumber'];?></a>
                
             </td>   			 
			
	</tr>
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


<script type="text/javascript">
    $(document).ready(function() {
   $('#leads_convert').DataTable({
       "lengthChange": false,
       "paging": false,
       "bInfo" : false,
       'drawCallback': function (oSettings) {
           $('#leads_convert_wrapper').children('.row:first').children('.col-md-6:first').html('<div class="form-group row" style="margin-left:0;"><label class="col-form-label">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
           $('#leads_convert_wrapper').children('.row:last').children('.col-md-5').html($('.pager').clone()).addClass('text-left');
           $('#leads_convert_wrapper').children('.row:last').children('.col-md-7').html($('.table_b ').html()).addClass('text-right');
           $('.pages-div-custom').remove();
           
      }
   });
} );
</script>


<?php if ($this->status != Entity::STATUS_ARCHIVED) : ?>


<?php endif; ?>
@pager@

</div>
</div>


</div>