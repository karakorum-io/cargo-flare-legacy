<script language="javascript" type="text/javascript">
	
	$(document).ready(function(){
			
	$('#add_order').keypress(function(e) {
								  
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
/*
function clear_batch_value()
{
		  
	$("#shipper_company").val('');
	$("#shipper_company_id").val('');
	$("#orders_list option:selected").remove();
	$("#batch_order_ids").val('');
}*/

function clear_batch_value()
{
		  
	$("#shipper_company").val('');
	$("#shipper_company_id").val('');
	//$("#orders_list option:selected").remove();
	
	$("#orders_list option:selected").each(function () {
	   var $this = $(this);
	   if ($this.length) {
		  var selText = $this.text();
		  //console.log(selText+'----'+$("#batch_order_ids").val()+'=='+$("#batch_order_ids").val().indexOf(','+selText));
		  if($("#batch_order_ids").val().indexOf(','+selText) !=-1)
		   $("#batch_order_ids").val( $("#batch_order_ids").val().replace(','+selText, "") );  
		  else if($("#batch_order_ids").val().indexOf(selText) !=-1){
		    $("#batch_order_ids").val( $("#batch_order_ids").val().replace(selText+',', "") );
			$("#batch_order_ids").val( $("#batch_order_ids").val().replace(selText, "") );
		  }
		//console.log(selText+'----'+$("#batch_order_ids").val());	
			$this.remove();
	   }
	});
	$('#orders_list option').prop('selected', true);
	//$("#orders_list option:selected").remove();
	//$("#batch_order_ids").val('');
}

function redirect_to_func()
{
	document.location.href = "/application/accounts/batchnew";
}		
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
<br /><br />
<? include(TPL_PATH."accounts/accounts/menu.php"); ?>
<?php
$shipperAccess = 0;
if($_SESSION['member']['access_shippers']==1 || 
			   $_SESSION['member']['parent_id']==$_SESSION['member_id'] )
			{
				$shipperAccess = 1;
			}
?>
				
<h3>Accounts</h3>
<?php if($this->accountType=="carrier"){?>
<div style="float:right"><a href="<?=getLink("accounts", "advDuplicateCarriers");?>">Advanced search <?= $this->accountType?></a></div>
<?php }elseif($this->accountType=="shipper"){?>
<div style="float:right"><a href="<?=getLink("accounts", "advDuplicateShippers");?>">Advanced search <?= $this->accountType?></a></div>
<?php }?>
<div style="clear:both">&#8203;</div>
<table width="100%" cellspacing="0" cellpadding="0" border="0">

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
    <h4 style="color:#3B67A6">Search Account</h4>
    
                <form method="post"  action="<?php print SITE_IN."application/accounts/advancedDuplicateAccountsSubmit";?>" name="create_form" id="create_form">
                <input type="hidden" name="accountType" id="accountType" value="<?= $this->accountType;?>"/>
                <table width="100%" cellspacing="1" cellpadding="1">
                
                <tr>
                 <td width="40%">
                    <table cellspacing="2" cellpadding="0" border="0">
                        <tr>
                            <td><span class="required">*</span>Company:</td>
                            <td valign="top">
                            <input type="text" id="add_order" maxlength="100" name="add_order" tabindex="41" class="zip form-box-textfield" style="width:170px;margin-left:5px;" kl_virtual_keyboard_secure_input="on">
                            
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
								
								//$orders_list = $batch_idsarr;
							    //$orders_list_size = count($batch_idsarr);
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
                          <input type="hidden" name="batch_order_ids" id="batch_order_ids" value=""/>
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
                                <input type="hidden" name="shipper_company_id" id="shipper_company_id" /></td>
                        </tr>
                        
                        <tr><td colspan="2">&nbsp;</td></tr>
                        <tr>
                            <td colspan="2" align="center">&nbsp;
                            <table width="100%" cellpadding="1" cellspacing="1">
                              <tr>
                                <td width="80%">
                            <?php print submitButtons(SITE_IN."application/accounts/advancedDuplicateAccountsSubmit", "Locate Accounts") ?>
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
<br />
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid">
    <tr class="grid-head">
        <td class="grid-head-left">Num</td>
        <td><?=$this->order->getTitle("A.company_name", "Company Name")?></td>
        
        
        <td>City</td>
        <td>State</td>
        
        <td>Zip</td>
        <td>Number of Duplicates</td>
       <td width="60" class="grid-head-right" colspan="3">Actions</td>
    </tr>
    <?php
        $pageNum = isset($_GET['page']) ? $_GET['page'] : 1;
        $startNum = $_SESSION['per_page'] * ($pageNum - 1);
    ;

	$accountData = array();

	  $accountData = $this->accounts;
	
	if (count($accountData)>0){?>
	    <? foreach ($accountData as $i => $account) { 
		
		//print $accountObj->status;
		  $bgcolor = "";
		  
		?>
	    <tr class="grid-body<?=($i == 0 ? " first-row" : "")?>" id="row-<?=$account['ID']?>">
	        <td class="grid-body-left" bgcolor="<?php print $bgcolor;?>"><a href="<?=getLink("accounts", "details", "id", $account['ID']);?>"><?= $startNum + $i + 1?></a></td>
	        <td bgcolor="<?php print $bgcolor;?>"><?=htmlspecialchars($account['company_name']);?></td>
            <td bgcolor="<?php print $bgcolor;?>"><?=$account['city'];?></td>
            <td bgcolor="<?php print $bgcolor;?>"><?=$account['state'];?></td>
            <td bgcolor="<?php print $bgcolor;?>"><?=$account['zip_code'];?></td>
            <td bgcolor="<?php print $bgcolor;?>"><?=$account['number_of_count'];?></td>
	        <td bgcolor="<?php print $bgcolor;?>" colspan="3"><a href="<?=getLink("accounts", "duplicateDetails", "id",$account['ID']);?>">View Account</a></td>
	    </tr>
	    <? } ?>
	<?}else{?>
		<tr class="grid-body first-row" id="row-" >
	        <td align="center" colspan="9">No records found.</td>
	    </tr>
	<? } ?>
</table>
