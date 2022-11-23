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
	
$(document).ready(function () {
	$('#reassignUserDiv').hide();
}) 
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




<?= formBoxStart("") ?>
<div class="row">
	
	<div class="col-5">
		<div class="kt-portlet">
	
			<div class="kt-portlet__head">
				<div class="kt-portlet__head-label">
					<h4 style="color:#3B67A6">SMS Account Information</h4>
				</div>
			</div>
			
			<div class="kt-portlet__body">
				<?= formBoxStart("") ?>
				<div style="float:left;margin-bottom:20px;text-right;width:100%;text-align:right;">
				   <?= simpleButton("Add More Users",getLink("billing","sms"),'btn-sm btn_dark_green'); ?>
				</div>

				<table class="table table-bordered">
					<tbody>
						<tr>
							<td>Account status:</td>
							<td style="font-size:18px;">
								<div style="color:green;font-weight:bold;font-size:14px;">Active</div>
							</td>
						</tr>
						<tr>
							<td>Remaining Balance:</td>
							<td style="font-size:18px;"><strong style="color:#3B67A6;">$<?php print $this->creditValue;?></strong></td>
						</tr>
						<tr>
							<td>Credit Used:</td>
							<td style="font-size:18px;"><strong style="color:#3B67A6;">$<?php print $this->creditValueUsed;?></strong></td>
						</tr>
						<tr>
							<td>Last Payment Received:</td>
							<td>$<?php print $this->paymentDetail['credit'];?></td>
						</tr>
						<tr>
							<td>Last Payment Date:</td>
							<td><?php print $this->paymentDetail['transaction_date'];?></td>
						</tr>
						<tr>
							<td>Next Billing Date:</td>
							<td><strong>10/01/2015</strong></td>
						</tr>
						<tr>
							<td><?= simpleButton("Replenish Balance", getLink("billing", "onetime"),'btn-sm btn_dark_blue'); ?></td>
							<td><input class="form-control" type="text" name="amount" value="" placeholder="Enter Amount"/></td>
						</tr>
						<tr>
							<td></td>
							<td><input type="checkbox" name="amount" value="" />&nbsp;Auto Replenish</td>
						</tr>
					</tbody>
				</table>
				<?= formBoxEnd() ?>
				
			</div>
			
			
		</div>
	</div>
	
	
	<div class="col-7">
		
		<div class="kt-portlet">
	
			<div class="kt-portlet__head">
				<div class="kt-portlet__head-label">
					<?= formBoxStart("SMS User Information") ?>
				</div>
			</div>
			
			<div class="kt-portlet__body">
				<div style="width:100%;">
					
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>User Name</th>
								<th>Assigned Phone#</th>
								<th>Purchase Date</th>
								<th colspan="2">Actions</th>
							</tr>
						</thead>
						<tbody>
						<? if (count($this->smsUser) > 0) { ?>
							<? foreach ($this->smsUser as $i => $t) {
								$member = new Member($this->daffny->DB);
								$member->load($t['user_id']);
							?>
							<tr id="row-<?= $t['id'] ?>">
								<td><?= $member->contactname ?></td>
								<td><?= htmlspecialchars($t['phone']) ?></td>
								<td><?= colorBillingType($t['purchased']) ?></td>
								<td style="width:16px;">
									<img src="<?=SITE_IN?>/images/icons/reassign.png" title="Reassign" alt="Reassign" width="16" height="16" onclick="reassignDialog('<?php print $t['id'];?>');" style="cursor:pointer;">
								</td>									
								<td style="width: 16px;" class="grid-body-right">
									<?=deleteIcon(getLink("billing", "deleteSmsUser", "id", $t['id'],"ph", $t['phone']), "row-".$t['id'], "Delete", "")?>
								</td>
							</tr>
							<? } ?>
						<? } else { ?>
							<tr id="row-1">
								<td>&nbsp;</td>
								<td colspan="2" align="center">Records not found.</td>
								<td>&nbsp;</td>
							</tr>
						<? } ?>
						</tbody>
					</table>
				   
					<?= formBoxEnd() ?>
				</div>
			</div>
			
			
		</div>
	</div>
	
	
</div>
<?= formBoxEnd() ?>