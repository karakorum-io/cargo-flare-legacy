<br />
<? include(TPL_PATH . "myaccount/menu.php"); ?>

<div id="reassignUserDiv">

    <table cellspacing="2" cellpadding="0" border="0">
        <tr>
            <td valign="top">
                <select class="form-box-combobox" id="company_members">
                   <option value=""><?php print "Select One"; ?></option>

                    <?php foreach($this->company_members_sms as $member) : ?>

                          <?php //if($member->status == "Active"){
                            $activemember .="<option value= '".$member['id']."'>" .$member['contactname'] ."</option>";
			               //}
			            /* else {
                               $inactivemember .="<option value= '".$member->id."'>" .$member->contactname ."</option>";
			              }*/
						?>
						<?php endforeach;?>
						<optgroup label="Active User">
						<?php echo $activemember; ?>
						</optgroup>
						
                </select>
              <input type="hidden" id="reassignId" value="" />
            </td>
        </tr>
 </table>
</div>

<script type="text/javascript">
function reassignDialog(id){

	  if (id == 0) 
		{            

			$(".alert-message").empty();
			$(".alert-message").text("	SMS User Information not selected");
			$(".alert-pack").show();
			return false;        
		} 
      $("#reassignId").val(id);
	  $("#reassignUserDiv").dialog("open");

}


$("#reassignUserDiv").dialog({

	modal: true,
	width: 300,
	height: 140,
	title: "Reassign phone to user",
	hide: 'fade',
	resizable: false,
	draggable: false,
	autoOpen: false,
	buttons: {
		"Submit": function () {
			var reassignId = $("#reassignId").val();
			var member_id = $("#company_members").val();
			 //$("body").nimbleLoader('show');
               $.ajax({
					type: "POST",
					url: BASE_PATH+'application/ajax/sms.php',
					data: { action: "reassignSmsUser", reassignId:reassignId,member_id: member_id},
					dataType: "json",
					success: function(response) {
						if (response.success == true) {
							//alert(response.data);
							alert("User successfully reassign.");
							window.location.reload();  
						}
						else{
							//$("#sms_content").nimbleLoader('hide');
						  alert("Can't send email. Try again later, please");
						}
					}
				});
                
		},

		"Cancel": function () {
			$(this).dialog("close");

		}

	}

});	 
</script>


<div style="float:left; width:1160px; padding-right:10px;">

<?= formBoxStart("") ?>

<table width="100%" cellpadding="2" cellspacing="2">
<tr>
  <td align="center" width="480" valign="top" style="font-size:24px;"><strong>SMS Account Information</strong></td>
  <td align="center"  valign="top" style="font-size:24px;"><strong>SMS User Information</strong></td>
</tr>

<tr>
  <td valign="top">
<div style="float:left; width:480px; padding-right:10px; vertical-align:top;">
<?= formBoxStart("") ?>
<div style="float:left;">
   <div style="float:left; width:150px;vertical-align:middle;"><h4>Account status</h4></div>
   <div style="float:left; width:130px; text-align:center;color:green; vertical-align:middle; font-weight:bold;font-size:14px;">Active</div>
   <div style="float:right; width:150px; "><?= simpleButton("Add More Users",getLink("billing","sms")); ?></div>
</div>

<table cellspacing="5" cellpadding="5" border="0" class="grid" width="100%">
    <tr class="grid-body">
        <td>Remaining Balance:</td>
        <td style="font-size:24px;"><strong style="color:#3B67A6;">$ <?php print $this->creditValue;?></strong></td>
    </tr>
    <tr class="grid-body">
        <td>Credit Used:</td>
        <td style="font-size:24px;"><strong style="color:#3B67A6;">$ <?php print $this->creditValueUsed;?></strong></td>
    </tr>
    <tr class="grid-body">
        <td>Last Payment Received:</td>
        <td>$<?php print $this->paymentDetail['credit'];?></td>
    </tr>
    <tr class="grid-body">
        <td>Last Payment Date:</td>
        <td><?php print $this->paymentDetail['transaction_date'];?></td>
    </tr>
    <tr class="grid-body">
        <td>Next Billing Date:</td>
        <td ><strong>10/01/2015</strong></td>
    </tr>
    <tr>
        <td><?= simpleButton("Replenish Balance", getLink("billing", "onetime")); ?></td>
        <td  class="grid-body"><input type="text" name="amount" value="" placeholder="Enter Amount"/></td>
    </tr>
    <tr>
        <td></td>
        <td><input type="checkbox" name="amount" value="" />&nbsp;Auto Replenish</td>
    </tr>
</table>
<?= formBoxEnd() ?>
</div>

  </td>
  <td valign="top">
<div style="float:left; width:620px;">
    <?= formBoxStart("SMS User Information") ?>
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid">
        <tr class="grid-head">
            <td class="grid-head-left">User Name</td>
            <td>Assigned Phone#</td>
            <td>Purchase Date</td>
            <td class="grid-head-right"  colspan="2">Actions</td>
        </tr>
        <? if (count($this->smsUser) > 0) { ?>
            <? foreach ($this->smsUser as $i => $t) { 
			     $member = new Member($this->daffny->DB);
				 $member->load($t['user_id']);
			?>
                <tr class="grid-body<?= ($i == 0 ? " first-row" : "") ?>" id="row-<?= $t['id'] ?>">
                    <td class="grid-body-left"><?= $member->contactname ?></td>
                    <td><?= htmlspecialchars($t['phone']) ?></td>
                    <td><?= colorBillingType($t['purchased']) ?></td>
                    <td style="width: 16px;" class="grid-body-right">
					<img src="/images/icons/reassign.png" title="Reassign" alt="Reassign" width="16" height="16" onclick="reassignDialog('<?php print $t['id'];?>');" style="cursor:pointer;"></td>
					<?php //print editClickIcon("reassignDialog('".$t['id']."');");?>
                    <td style="width: 16px;" class="grid-body-right"><?=deleteIcon(getLink("billing", "deleteSmsUser", "id", $t['id'],"ph", $t['phone']), "row-".$t['id'], "Delete", "")?></td>
                </tr>
            <? } ?>
        <? } else { ?>
            <tr class="grid-body first-row" id="row-1">
                <td class="grid-body-left">&nbsp;</td>
                <td colspan="2" align="center">Records not found.</td>
                <td class="grid-body-right">&nbsp;</td>
            </tr>
        <? } ?>
    </table>
   
    <?= formBoxEnd() ?>
</div>
  </td>
</tr>
</table>
<?= formBoxEnd() ?>
</div>
<div style="clear:both;">&nbsp;</div>