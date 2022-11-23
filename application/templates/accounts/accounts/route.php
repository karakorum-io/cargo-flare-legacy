<?php 
$shipperAccess = 0;
if($_SESSION['member']['access_shippers']==1 || 
			   $_SESSION['member']['parent_id']==$_SESSION['member_id'] )
			{
				$shipperAccess = 1;
			}

?>



<!--begin::Modal-->
<div class="modal fade" id="carrierdiv" tabindex="-1" role="dialog" aria-labelledby="carrierdiv_model" aria-hidden="true">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">
    <h5 class="modal-title" id="carrierdiv_model">Carrier Route In Radius</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    </button>
</div>
<div class="modal-body">

    <div id="carrier_data"> </div>
    
</div>
<div class="modal-footer">
   
</div>
</div>
</div>
</div>

<!--end::Modal-->



<?
if (isset($_GET['id']) && $_GET['id'] > 0) {
    include(TPL_PATH . "accounts/accounts/menu_details.php");
} else {
    include(TPL_PATH . "accounts/accounts/menu.php");
}
?>

<script language="javascript" type="text/javascript">


function showroute(route_id) {

		

        if (route_id == "") {

            swal.fire("Route not found");

        } else {



              Processing_show();

                $.ajax({

                    type: "POST",

                    url: BASE_PATH + "application/ajax/getcarrier.php",

                    dataType: "json",

                    data: {

                        action: "getroute",
						route_id: route_id
						

                    },

                    success: function (res) {

						//alert('===='+res.success);

                        if (res.success) {

                          // alert(res.carrierData);

							 

							 $("#carrier_data").html(res.carrierData);

							  //$("#mail_file_name").html(file_name);

							  $("#carrierdiv").modal();

                        } else {

                            swal.fire("Try again later, please");

                        }

                    },

                    complete: function (res) {

                        KTApp.unblockPage();

                    }

                });





        }

    }
</script>
Complete the form below and click "Save Account" when finished.
<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
    <img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/back.png" alt="Back" width="16" height="16" /> 
    <?php
			if($_SESSION['member']['parent_id']==$_SESSION['member_id'])
			{
			  ?>
              <a href="<?= getLink("accounts") ?>">&nbsp;Back to the list</a>
              <?php
		     }else
			 {
			?>
              <a href="<?= getLink("accounts","shippers") ?>">&nbsp;Back to the list</a>
            <?php	 
			 }
            ?>
</div>

<form action="<?= getLink("accounts", "route", "id", get_var("id")) ?>" method="post" >
<?= formBoxStart("Account Route Information") ?>

    <div class="row">
        <div class="col-8">

            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                    @ocity@
                    </div>
                </div>

                <div class="col-6">
                     <div class="form-group">
                     @dcity@
                 </div>
                </div>

            </div>

            <div class="row">
                <div class="col-6">
               <div class="form-group">
                @ostate@
                 </div>
                </div>

                <div class="col-6">
               <div class="form-group">
                @dstate@
                 </div>
                </div>

            </div>


            <div class="row">
                <div class="col-6">
                <div class="form-group">
                 @ozip_code@
                  </div>
                </div>

                <div class="col-6">
               <div class="form-group">
                 @dzip_code@

             </div>
                </div>

            </div>


            <div class="row">
                <div class="col-6">
                 <div class="form-group">
                 @radius@
                 </div>
                </div>

                 <div class="col-6 mt-4">
                  <div class="row mt-1">
                    <div class="col-6">
                      <?php if($this->accountType == "Shipper"){?> 
                      <?= functionButton("Reassign Account", "manageSalesrep()") ?>
                      <?php } ?>
                    </div>

                    
                  

                    <?= formBoxEnd() ?>
                    <br />
                   

                    <div class="col-6">
                       <?=submitButtons(getLink("accounts","route"), "Save")?>
                    </div>
                  </div>
                </div>

            </div>

    </div>
    
</form>




<div  class="row w-100 mt-4">
    <div class="col-12 ">

    <?=formBoxStart("Account Route Information")?>    
    <table id="account_route" class="table table-bordered">
        <thead>
    <tr >
        <th >Route ID</th>
        <th >Origin City</th>
        <th >Origin State</th>
        <th >Origin Zip</th>
        <th >Destination City</th>
        <th >Destination State</th>
        <th >Destination Zip</th>
        <th >Radius</th>
        <th >Actions</th>
    </tr>
 </thead>
   
    <? if (count($this->routeData)>0){?>
	    <? foreach ($this->routeData as $i => $commission) { ?>
        
       
	    <tr class="grid-body<?=($i == 0 ? " " : "")?>" id="row-<?=$commission['id']?>">
	        <td>
	        	<?= $i+1;?>
	        </td>
            <td>
	        	<?= $commission['ocity'];?>
	        </td>
            <td>
	        	<?= $commission['ostate'];?>
	        </td>
	        <td>
	        	<?= $commission['ozip'];?>    
	        </td>
            <td>
	        	<?= $commission['dcity'];?>
	        </td>
            <td>
	        	<?= $commission['dstate'];?>
	        </td>
	        <td>
	        	<?= $commission['dzip'];?>    
	        </td>
            
	        <td><?=htmlspecialchars($commission['radius']);?></td>
	         <td >
             <div class="row">
                <div class="col-6">
                    <img src="<?= SITE_IN ?>/images/icons/info.png" title="Info" alt="Info" width="16" height="16" onclick="showroute('<?=$commission['id']?>');">

                </div>
                 <div class="col-6">
                    <?php print deleteIcon(getLink("accounts", "routeDelete", "id", $commission['id']), "row-".$commission['id'])?>
                </div>
             </div>
         </td>

	    </tr>
	    <? } ?>
	<?}else{?>
		<tr class="grid-body " id="row-">
	        <td align="center" colspan="9">No records found.</td>
	    </tr>
	<? } ?>
</table>

</div>
</div>

<?=formBoxEnd()?>


  <script type="text/javascript">
  $(document).ready(function() {
  $('#account_route').DataTable();
  });
  </script>