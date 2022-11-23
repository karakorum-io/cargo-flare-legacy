<style type="text/css">
li#accounts_previous {
    display: none;
}
li#accounts_next {
    display: none;
}
</style>
<div class="mt-5">
	</div>
<? include(TPL_PATH."accounts/accounts/menu.php"); ?>

<?php //print "========".$this->accountType;?>
<script type="text/javascript">
  function merge_accounts(type)
  {
	  if ($(".account-checkbox-assign:checked").length == 0) {

		  swal.fire("Assign To not selected");
		return false; 
	  }
	  
	  if ($(".account-checkbox-merge:checked").length == 0) {
		    swal.fire("Merge From not selected.");
		return false; 
	  }
	  
	  if ($(".account-checkbox-merge:checked").length > 1) {
			 swal.fire("Select Only One Merge From Account.");
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
			 swal.fire("Select Only One Merge From Account."+validateFlag);
			return false;        
        }
	Processing_show()
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
				   swal.fire('Done');
				   <?php if($this->accountType==2){?>
				     location.href =  BASE_PATH+"application/accounts/duplicateCarriers";
				   <?php }else{?>
				     location.href =  BASE_PATH+"application/accounts/duplicateShippers";
				   <?php }?>
					
                }
            },
			 complete: function (res) {

                        KTApp.unblockPage();

                    }
        });
  }
</script>


				
<h3>Accounts</h3>

<div class="row ">
	<div class="col-12">
<table id="accounts" class="table table-bordered">
	<thead>
    <tr >
        <th class="grid-head-left">Num</th>
        <th>Account</th>
        <th><?=$this->order->getTitle("A.company_name", "Company Name")?></th>
        <?php if($this->accType==1){?>
        
        <th>Contact Name1</th>
         <?php }elseif($this->accType==2){?>
         <th>Firstname</th>
         <th>Lastname</th>
          <?php }?>
          
        <th>Referred By</th>
        <th>Phone / Email</th>
        <th>Status</th>
        <th>City</th>
        <th>State</th>
        
        <th>Zip</th>
        <th>Last Order Date</th>
        <th>Order(s)</th>
       <th width="40" class="grid-head-right" >Assign/Delete</th>
       <th width="40" class="grid-head-right" >Merge To</th>
    </tr>
</thead>
    <?php
        $pageNum = isset($_GET['page']) ? $_GET['page'] : 1;
        $startNum = $_SESSION['per_page'] * ($pageNum - 1);
    

	$accountData = array();

	  $accountData = $this->accounts;
	
	if (count($accountData)>0){?>
	    <? foreach ($accountData as $i => $account) { 
		$accountObj = $account['accounts'];
		$orders = $account['number_of_count'];
		//print $accountObj->status;
		  $bgcolor = "";
		  
		?>
	    <tr class="grid-body<?=($i == 0 ? " " : "")?>" id="row-<?=$account['ID']?>">
	        <td class="grid-body-left" bgcolor="<?php print $bgcolor;?>"><a href="<?=getLink("accounts", "details", "id", $accountObj->id);?>" target="_blank"><?= $startNum + $i + 1?></a></td>
            <td bgcolor="<?php print $bgcolor;?>"><?=$account['ID'];?></td>
	        <td bgcolor="<?php print $bgcolor;?>"><?=htmlspecialchars($accountObj->company_name);?></td>
             <?php if($this->accType==1){?>
                    
                   <td bgcolor="<?php print $bgcolor;?>"><?=$accountObj->contact_name1;?></td>
			 <?php }elseif($this->accType==2){?>
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

				<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand">
					<input type="checkbox" name="account_assign_id" value="<?= $accountObj->id ?>" class="account-checkbox-assign"> 
					<span></span>
				</label>
            </td>
            <td bgcolor="<?php print $bgcolor;?>">
			<label class="kt-radio kt-radio--bold kt-radio--brand">
				<input type="radio" name="account_merge_id"  id="account_merge_id"  value="<?= $accountObj->id ?>" class="account-checkbox-merge"/> 
				<span></span>
			</label>
               
            </td>
	    </tr>
	    <? } ?>
	<?}else{?>
		<tr class="grid-body first-row" id="row-" >
	        <td align="center" colspan="9">No records found.</td>
	    </tr>
	<? } ?>
</table>
</div>
</div>


	<div style="float:right;">
	<table cellpadding="0" cellspacing="0" border="0">
	<tr><td colspan="4">&nbsp;</td></tr>
	<tr>

	<td style="padding-left: 15px;"><?php print functionButton('Merge Accounts', 'merge_accounts('.$this->accountType.')'); ?></td>
	</tr>
	</table>
	</div>

	<script type="text/javascript">
	$(document).ready(function() {
	$('#accounts').DataTable({
		"scrollX": true
	});
	})
	</script>

