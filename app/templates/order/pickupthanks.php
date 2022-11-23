<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Carrier Status Update</title>
	<link rel="shortcut icon" href="<?php echo SITE_IN ?>styles/favicon.ico" />
    <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/styles.css"/>
		<link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/application.css"/>
		<link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/default.css"/>
		<link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/jquery-ui.css"/>
		<link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/jquery.ui.timepicker.css"/>
		
		<script type="text/javascript">var BASE_PATH = '<?php echo SITE_IN ?>';</script>
		<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false&language=en"></script>
		<script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.min.js"></script>
		<script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery-ui.js"></script>
		<script type="text/javascript" src="<?= SITE_IN ?>jscripts/ui.geo_autocomplete.js"></script>
		<script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.maskMoney.js"></script>
		<script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/functions.js"></script>
		<script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/application.js"></script>
		<script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.maskedinput-1.3.min.js"></script>
        <script type="text/javascript">
	var busy = false;
	
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
				action: 'addExternal',
				text: encodeURIComponent(text),
				entity_id: <?= $this->entity->id ?>,
				notes_type: <?= Note::TYPE_INTERNAL ?>,
				priority: priority
			},
			success: function(result) {
				if (result.success == true) {
					alert("Note saved.");
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
	</script>
    
</head>
<body>
<div style="clear: both;margin-top:20px;"></div>
<div class="apl_centering">
    <br>
    <center><h2>Order ID <?=$this->entity->getNumber()?></h1></center>
    <div class="row main_body">
	<div class="wrapper">
      
            <table width="100%" cellpadding="1" cellspacing="1" id="contentTable"> <!--height="531515"-->
              <tr>
                <td>&nbsp; </td>
              </tr> 
              
              <tr>
                <td align="center"> <h2 style="font-size:20px;"><b>Status Update Request from <?php print $this->companyname;?></b></h2></td>
              </tr> 
              
              
              <tr>
                <td>&nbsp; </td>
              </tr> 
              <tr>
                <td  align="center"><h3>Please select date to update status of this shipment.</h3></td>
              </tr>
              <tr>
                <td>&nbsp; </td>
              </tr> 
            </table>
           
            
      </div>
  </div>
  <br /><br />
<div style="clear:left;"></div>
<form action="" method="post">
<table width="100%" cellpadding="1" cellpadding="1" border="0">
<tr>
<td width="49%" valign="top" >
<?php
$origin = $this->entity->getOrigin();
	$destination = $this->entity->getDestination();
	$vehicles = $this->entity->getVehicles();

	if($this->entity->status==Entity::STATUS_DISPATCHED)
	 {
?>
<div class="order-info"  style="width:95%; margin-bottom: 10px;">
	<p class="block-title">Pickup Information</p>
	<div>
		 <table width="100%" cellpadding="1" cellpadding="1" border="0"  >
		   <tr> <td width="23%"><strong>Address</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $origin->address1; ?>,&nbsp;&nbsp;<?= $origin->address2; ?></td></tr>
		   <tr> <td style="line-height:15px;"><strong>City</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><span class="like-link"onclick="window.open('<?= $origin->getLink() ?>')"><?= $origin->getFormatted() ?></span></td></tr>
         </table>  
	</div>
</div> 
<?php }elseif($this->entity->status==Entity::STATUS_PICKEDUP){?>
<div class="order-info"  style="width:95%; margin-bottom: 10px;">
	<p class="block-title">Dropoff Information</p>
	<div>
		 <table width="100%" cellpadding="1" cellpadding="1" border="0"  > 
                        <tr> <td width="23%"><strong>Address</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $destination->address1; ?>,&nbsp;&nbsp;<?= $destination->address2; ?></td></tr>
		   <tr> <td style="line-height:15px;"><strong>City</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><span class="like-link"onclick="window.open('<?= $destination->getLink() ?>')"><?= $destination->getFormatted() ?></span></td></tr>
		   
		</table>
	</div>
</div>
<?php }?>
</td> 
<td width="1%" valign="top" >&nbsp;  </td>
<td width="49%" valign="top" >

<div class="order-info" style="float:none;">
                <p class="block-title">Select Date</p>
                <div>
                    <table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table order-edit" style="white-space:nowrap;">
                        <tr>
                            <td>@pickup_delivered_date@</td>
                        </tr>
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
                           </tr>
						<?php endforeach; ?>
                        </table>
	</div>
</div>  
  <br/><br/>
  
<div class="order-info" style="margin-bottom:20px;" >       
	<p class="block-title">Notes</p>
   <div>
		<?php //if ($this->entity->status != Entity::STATUS_ARCHIVED) : ?>
            <textarea class="form-box-textarea" style="width: 920px; height: 52px;" maxlength="1000" id="internal_note" name="internal_note"></textarea>
    
    <br /> <br />
  </div>
</div>
<div style="clear:both;"></div>
 <br /> 
    <div style="width:100%;">
            <div style="float:left;color:#38baff;">
                * A second email will be sent for status update for Delivery.
            </div>
            <div style="float:right;">
                <?= submitButtons(SITE_IN."application/", "Update") ?>
            </div>
    </div> 
    <div style="clear:both;"></div>   
    <div style="float:right; margin:20px; color:#38baff;">
                Powered by CargoFlare
            </div>
</div>


</form>
<script type="text/javascript">
    $(document).ready(function(){
        //$("#avail_pickup_date").datepicker({dateFormat: 'mm/dd/yy'});
		$("#pickup_delivered_date").datepicker({
			dateFormat: 'yy-mm-dd',
			maxDate: new Date()
		}).attr('readonly','readonly');
		 
	});
 
</script>
</body>
</html>