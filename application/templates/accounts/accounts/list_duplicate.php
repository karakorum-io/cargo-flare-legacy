
<style type="text/css">
li#carriers_previous {
    display: none;
}
li#carriers_next {
    display: none;
}
</style>
<? include(TPL_PATH."accounts/accounts/menu.php"); ?>
<?php
$shipperAccess = 0;
if($_SESSION['member']['access_shippers']==1 || 
			   $_SESSION['member']['parent_id']==$_SESSION['member_id'] )
			{
				$shipperAccess = 1;
			}
?>
<div class="kt-portlet">

<?php
		if($this->accType == 2)
		{
		?>
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">Duplicate Shippers</h3>
				<div style="float: right !important">
					<a href="<?=getLink("accounts", "advDuplicateShippers");?>">Advanced search <?= $this->accountType?></a>
				</div>
			</div>
		</div>
        <?php
		}else{
		?>
        <div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">Duplicate Carriers</h3>
				<div style="float:right">
					<a href="<?=getLink("accounts", "advDuplicateCarriers");?>">Advanced search <?= $this->accountType?></a>
				</div>
			</div>
		</div>
        <?php
		}
		?>

<div class="kt-portlet__body">
<table id="carriers" class="table table-bordered">
	<thead>
    <tr >
        <th class="grid-head-left">Num</th>
        <th><?=$this->order->getTitle("A.company_name", "Company Name")?></th>
        <?php
		if($this->accType == 2)
		{
		?>
        <th>Firstname</th>
        <th>Lastname</th>
        <?php
		}?>
        <th>City</th>
        <th>State</th>
        
        <th>Zip</th>
        <th>Number of Duplicates</th>
       <th >Actions</th>
    </tr>
    </thead>
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
	    <tr class="grid-body<?=($i == 0 ? "  " : "")?>" id="row-<?=$account['ID']?>">
	        <td class="grid-body-left" bgcolor="<?php print $bgcolor;?>"><a href="<?=getLink("accounts", "details", "id", $account['ID']);?>"><?= $startNum + $i + 1?></a></td>
	        <td bgcolor="<?php print $bgcolor;?>"><?=htmlspecialchars($account['company_name']);?>
	        	
	        </td>
             <?php
		if($this->accType == 2)
		{
		?>
        <td><?=htmlspecialchars($account['first_name']);?></td>
        <td><?=htmlspecialchars($account['last_name']);?></td>
        <?php
		}?>
            <td bgcolor="<?php print $bgcolor;?>"><?=$account['city'];?></td>
            <td bgcolor="<?php print $bgcolor;?>"><?=$account['state'];?></td>
            <td bgcolor="<?php print $bgcolor;?>"><?=$account['zip_code'];?></td>
            <td bgcolor="<?php print $bgcolor;?>"><?=$account['number_of_count'];?></td>
	        <td ><a href="<?=getLink("accounts", "duplicateDetails", "id",$account['ID']);?>">View Account</a></td>
	    </tr>
	    <? } ?>
	<?}else{?>
		<tr class="grid-body" id="row-" >
	        <td align="center" colspan="9">No records found.</td>
	    </tr>
	<? } ?>
</table>

</div>
</div>

	<script type="text/javascript">
	$(document).ready(function() {
	$('#carriers').DataTable();
     })
	</script>
