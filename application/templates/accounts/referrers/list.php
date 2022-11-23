<? include(TPL_PATH."accounts/referrers/menu.php"); ?>
<script language="javascript">
function checkMandatory(cb)
{
	var referrerStatus = 0;
	  if(cb.checked == true) {
		referrerStatus = 1
	 } else {
		referrerStatus = 0
	 }	
	 
	       $.ajax({
                    url: BASE_PATH + 'application/ajax/referrer.php',
                    data: {
                        action: "referral_check",
                        referrerStatus: referrerStatus
                    },
                    type: 'POST',
                    dataType: 'json',
                   success: function (response) {
                        
                        if (response.success == true) {
                           swal.fire(response.message);

                        }
                        
                    }
                });
}
</script>


<div class="alert alert-light alert-elevate " >
<div class="row" style="width: 100%">
<div  class="col-12">
<h3>Referrers</h3>
Below is a list of referrers. Click the name of any referrer to deactivate or edit details.


<div class="row" >
	<div class="col-12 mb-3">
<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/add.gif" alt="Add" width="16" height="16" /> &nbsp;<a href="<?=getLink("referrers", "edit")?>" >Add New Referrer</a> &nbsp;&nbsp;<input type="checkbox" name="referral_check" id="referral_check" value="1"   onclick="checkMandatory(this);" <?php if($this->referrer_status==1){print " checked ";}?>/>&nbsp;&nbsp;Mandatory&nbsp;&nbsp;<span style="color:red;">* if mandatory referrer must be added</span>
 </div>
</div>



<table id="referrers" class="table table-bordered" >
	<thead>
    <tr >
        <th class="grid-head-left"><?=$this->order->getTitle("name", "Name")?></th>
        <th class="grid-head-left"><?php print "SalesRep";//$this->order->getTitle("salesrep", "SalesRep")?></th>
        <th>Status</th>
        <th >Actions</th>
    </tr>
    </thead>
    <? if (count($this->referrers)>0){?>
	    <? foreach ($this->referrers as $i => $referrer) { 
		  $salesrep = "";
		   if(!is_null($referrer->salesrep) && $referrer->salesrep!="" && $referrer->salesrep!=0){
             $m = new Member($this->daffny->DB);
             $m->load($referrer->salesrep);
			 $salesrep = $m->contactname;
		   }

		?>
	    <tr class="grid-body<?=($i == 0 ? " " : "")?>" id="row-<?=$referrer->id?>">
	        <td class="grid-body-left"><a href="<?= getLink("referrers", "edit", "id", $referrer->id);?>"><?=htmlspecialchars($referrer->name);?></a></td>
            <td class="grid-body-left"><?=$salesrep;?></td>
	        <td align="center" class="grid-body-left"><?=statusText(getLink("referrers", "status", "id", $referrer->id), Referrer::$status_name[$referrer->status])?></td>

	        <td>
	        	<div class="row">
	        		<div class="col-2">
	        			<?=editIcon(getLink("referrers", "edit", "id", $referrer->id))?>
	        		</div>
	        		 <div class="col-6">
	        			<?=deleteIcon(getLink("referrers", "delete", "id", $referrer->id), "row-".$referrer->id)?>
	        		</div>
	        		
	        	</div>
	        	
				
	        </td>
	    </tr>
	    
	    <? } ?>
	<?}else{?>
		<tr class="grid-body" id="row-">
	        <td align="center" colspan="4">No records found.</td>
	    </tr>
	<? } ?>
</table>


</div>
</div>
</div>
@pager@

     <script type="text/javascript">
        $(document).ready(function() {
        $('#referrers').DataTable({
        "lengthChange": false,
        "paging": false,
        "bInfo" : false,
        'drawCallback': function (oSettings) {

        $("#referrers_wrapper").children('.row:first').children('.col-sm-12:first').html('<div class="form-group row"><label class="col-form-label">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
        $("#referrers_wrapper").children('.row:nth-child(3)').children('.col-sm-12:first').html($('.pager').clone());
        $('.pages_div').remove();


        }
        });
        } );
        </script>