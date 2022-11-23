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


<script type="text/javascript">
  function merge_accounts(type)
  {
	  if ($(".account-checkbox-assign:checked").size() == 0) {
		$(".alert-message").empty();
		$(".alert-message").text("Assign To not selected.");
		$(".alert-pack").show();
		return false; 
	  }
	  
	  if ($(".account-checkbox-merge:checked").size() == 0) {
		$(".alert-message").empty();
		$(".alert-message").text("Merge From not selected.");
		$(".alert-pack").show();
		return false; 
	  }
	  
	  if ($(".account-checkbox-merge:checked").size() > 1) {
            $(".alert-message").empty();
			$(".alert-message").text("Select Only One Merge From Account.");
			$(".alert-pack").show();
			return false;        
        }
		
	var account_merge_id = $(".account-checkbox-merge:checked").val();
	
	var account_ids = [];
	var validateFlag = 0;
        $(".account-checkbox-assign:checked").each(function(){
			if($(this).val() == account_merge_id)	
			  validateFlag = $(this).val();
            account_ids.push($(this).val());
        });
		
		
	if ( validateFlag > 0) {
            $(".alert-message").empty();
			$(".alert-message").text("You should not select Assign To and Merge From both checkboxes for Account : "+validateFlag);
			$(".alert-pack").show();
			return false;        
        }
	$("body").nimbleLoader('show');
     $.ajax({
            type: 'POST',
            url: BASE_PATH+'application/ajax/accounts.php',
            dataType: 'json',
            data: {
                action: 'deleteDuplicate',
                account_merge_id: account_merge_id,
                account_assign_ids: account_ids,
				type:type
            },
            success: function(response) {
                if (response.success == true) {
                   // window.location.reload();
				   alert('Done');
				   <?php if($this->accountType==2){?>
				     //location.href =  BASE_PATH+"application/accounts/duplicateCarriers";
				   <?php }else{?>
				     //location.href =  BASE_PATH+"application/accounts/duplicateShippers";
				   <?php }?>
					
                }
            },
			 complete: function (res) {

                        $("body").nimbleLoader('hide');

                    }
        });
  }
</script>
				
<h3>Accounts</h3>

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
                 <td width="100%">
                    <table cellspacing="2" cellpadding="0" border="0" width="100%">
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
                  <!--td width="10%" align="center">
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
                            <?php //print submitButtons(SITE_IN."application/accounts/advancedDuplicateAccountsSubmit", "Locate Accounts") ?>
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
                  </td--> 
                 </tr>
                
                  <tr><td>&nbsp;</td></tr>
                   <tr>
                            <td align="center">&nbsp;
                            <table width="70%" cellpadding="1" cellspacing="1">
                              <tr>
                                <td width="50%">
                            <?php print submitButtons(SITE_IN."application/accounts/advancedDuplicateAccountsSubmit", "Locate Accounts") ?>
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
<div style="clear:both">&#8203;</div>

<table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid">
    <tr class="grid-head">
        <td class="grid-head-left">Num</td>
        <td>Account</td>
        <td><?=$this->order->getTitle("A.company_name", "Company Name")?></td>
        <?php if($this->accType==2){?>
        
        <td>Contact Name1</td>
         <?php }elseif($this->accType==1){?>
         <td>Firstname</td>
         <td>Lastname</td>
          <?php }?>
          
        <td>Referred By</td>
        <td>Phone / Email</td>
        <td>Status</td>
        <td>City</td>
        <td>State</td>
        
        <td>Zip</td>
        <td>Last Order Date</td>
        <td>Order(s)</td>
       <td width="40" class="grid-head-right" >Assign/Delete</td>
       <td width="40" class="grid-head-right" >Merge To</td>
    </tr>
    <?php
        $pageNum = isset($_GET['page']) ? $_GET['page'] : 1;
        $startNum = $_SESSION['per_page'] * ($pageNum - 1);
    ;

	$accountData = array();

	  $accountData = $this->accounts;
	
	if (count($accountData)>0){?>
	    <? foreach ($accountData as $i => $account) { 
		$accountObj = $account['accounts'];
		$orders = $account['number_of_count'];
		//print $accountObj->status;
		  $bgcolor = "";
		  
		?>
	    <tr class="grid-body<?=($i == 0 ? " first-row" : "")?>" id="row-<?=$account['ID']?>">
	        <td class="grid-body-left" bgcolor="<?php print $bgcolor;?>"><a href="<?=getLink("accounts", "details", "id", $accountObj->id);?>" target="_blank"><?= $startNum + $i + 1?></a></td>
            <td bgcolor="<?php print $bgcolor;?>"><?=$account['ID'];?></td>
	        <td bgcolor="<?php print $bgcolor;?>"><?=htmlspecialchars($accountObj->company_name);?></td>
             <?php if($this->accType==2){?>
                    
                   <td bgcolor="<?php print $bgcolor;?>"><?=$accountObj->contact_name1;?></td>
			 <?php }elseif($this->accType==1){?>
                  <td bgcolor="<?php print $bgcolor;?>"><?=htmlspecialchars($accountObj->first_name);?></td>
                  <td bgcolor="<?php print $bgcolor;?>"><?=$accountObj->last_name;?></td>
              <?php }?>
            <td bgcolor="<?php print $bgcolor;?>"><?=htmlspecialchars($accountObj->referred_by);?></td>
            <td bgcolor="<?php print $bgcolor;?>"><?=$accountObj->phone1;?><br /><?=$accountObj->email;?></td>
            
            <td bgcolor="<?php print $bgcolor;?>">
	        	<?php print statusText(getLink("accounts", "status", "id", $accountObj->id), Account::$status_name[$accountObj->status]);?>
	        </td>
            <td bgcolor="<?php print $bgcolor;?>"><?=$accountObj->city;?></td>
            <td bgcolor="<?php print $bgcolor;?>"><?=$accountObj->state;?></td>
            <td bgcolor="<?php print $bgcolor;?>"><?=$accountObj->zip_code;?></td>
            <td bgcolor="<?php print $bgcolor;?>"><?= date("m/d/y", strtotime($accountObj->last_order_date));?></td>
            <td bgcolor="<?php print $bgcolor;?>"><?=$orders;?></td>
	        <td bgcolor="<?php print $bgcolor;?>">
               <input type="checkbox" name="account_assign_id" value="<?= $accountObj->id ?>" class="account-checkbox-assign"/>
            </td>
            <td bgcolor="<?php print $bgcolor;?>">
               <input type="radio" name="account_merge_id"  id="account_merge_id"  value="<?= $accountObj->id ?>" class="account-checkbox-merge"/>
            </td>
	    </tr>
	    <? } ?>
	<?}else{?>
		<tr class="grid-body first-row" id="row-" >
	        <td align="center" colspan="9">No records found.</td>
	    </tr>
	<? } ?>
</table>

<div style="float:right;">
		    <table cellpadding="0" cellspacing="0" border="0">
               <tr><td colspan="4">&nbsp;</td></tr>
			    <tr>
				    
				    <td style="padding-left: 15px;"><?php print functionButton('Merge Accounts', 'merge_accounts('.$this->accType.')'); ?></td>
			    </tr>
		    </table>
	    </div>
