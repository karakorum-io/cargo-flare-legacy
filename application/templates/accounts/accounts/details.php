<style type="text/css">
    li#account_list_previous {
    display: none;
}
li#account_list_next {
    display: none;
}
</style>

<?php
/**
 * Chetu Inc. Modified View file for broken order detail in carrier account section
 * 
 * @author Chetu Inc.
 * @version 1.0
 */
include(TPL_PATH . "accounts/accounts/menu_details.php");


$shipperAccess = 0;
if ($_SESSION['member']['access_shippers'] == 1 || $_SESSION['member']['parent_id'] == $_SESSION['member_id']) {
    $shipperAccess = 1;
}
?>





<!-- <div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">

    <img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/back.png" alt="Back" width="16" height="16" /> 
    <?php
    if ($_SESSION['member']['parent_id'] == $_SESSION['member_id']) {
        ?>
        <a href="<?= getLink("accounts") ?>">&nbsp;Back to the list</a>
        <?php
    } else {
        ?>
        <a href="<?= getLink("accounts", "shippers") ?>">&nbsp;Back to the list</a>
        <?php
    }
    ?>
</div> -->

<div class="quote-info accordion_main_info_new">
    <div class="row">           
        <div class="col-12">
            <div class="kt-portlet kt-portlet--height-fluid">
                <div class="kt-portlet__head" id="accordion_title">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            Account 
                        </h3>
                    </div>
                </div>
                <div class="kt-portlet__body ">
                    <div class="row">
                        <div class="col-12 col-sm-6">
              
                    <?= formBoxStart("Account Information") ?>
                    <table  class="table table-bordered" >
                        <tr>
                            <td><strong>Rating:</strong></td>
                            <td>@rating@</td>
                        </tr>
                        <tr>
                            <td nowrap="nowrap"><strong>Company Name:</strong></td>
                            <td>@company_name@</td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>@status_name@</td>
                        </tr>
                        <tr>
                            <td><strong>Type:</strong></td>
                            <td>@type@</td>
                        </tr>
                        <tr>
                            <td><strong>Print on check As:</strong></td>
                            <td>@print_name@</td>
                        </tr>
                        <tr>
                            <td><strong>ICC MC Number:</strong></td>
                            <td>@insurance_iccmcnumber@</td>
                        </tr>
                        <tr>
                            <td valign="top"><strong>Hours of operation:</strong></td>
                            <td>@hours_of_operation@</td>
                        </tr>
                        <tr>
                            <td valign="top"><strong>Notes:</strong></td>
                            <td>@notes@</td>
                        </tr>
                        <tr>
                            <td valign="top"><strong>Referred By:</strong></td>
                            <td>@referred_by@</td>
                        </tr>

                    </table>
                    <?= formBoxEnd() ?>
             

                           
                        </div>
                        <div class="col-12 col-sm-6">

        <div class="col-12">
       <?= formBoxStart($this->accountType . " Information") ?>
        <div  class="row">
        <div  class="col-6">
             <table class="table table-bordered">
                        <tr>
                            <td ><strong><?php if ($this->isShipper == 1) { ?>First Name<?php } else { ?>Contact #1:<?php } ?></strong></td>
                            <td valign="top"><?php if ($this->isShipper == 1) { ?>@first_name@<?php } else { ?>@contact_name1@<?php } ?></td>
                        </tr>
                        <tr>
                            <td ><strong><?php if ($this->isShipper == 1) { ?>Last Name<?php } else { ?>Contact #2:<?php } ?></strong></td>
                            <td ><?php if ($this->isShipper == 1) { ?>@last_name@<?php } else { ?>@contact_name2@<?php } ?></td>
                        </tr>
                        <tr>
                            <td><strong>Phone 1:</strong></td>
                            <td >@phone1@</td>
                        </tr>
                        <tr>
                            <td ><strong>Phone 2:</strong></td>
                            <td >@phone2@</td>
                        </tr>
                        <tr>
                            <td ><strong>Cell Phone:</strong></td>
                            <td >@cell@</td>
                        </tr>
                        <tr>
                            <td ><strong>Fax:</strong></td>
                            <td >@fax@</td>
                        </tr>
                    </table>

        </div>

        <div  class="col-6">

            <table class="table table-bordered">
                        <tr>
                            <td valign="top"><strong>Email:</strong></td>
                            <td valign="top">@email@</td>
                        </tr>
                        <tr>
                            <td valign="top"><strong>Address:</strong></td>
                            <td valign="top">@address1@ @address2@</td>
                        </tr>
                        <tr>
                            <td valign="top"><strong>City:</strong></td>
                            <td valign="top">@city@</td>
                        </tr>
                        <tr>
                            <td valign="top"><strong>State:</strong></td>
                            <td valign="top">@state@@state_other@</td>
                        </tr>
                        <tr>
                            <td nowrap="nowrap" valign="top"><strong>Zip Code:</strong></td>
                            <td valign="top">@zip_code@</td>
                        </tr>
                        <tr>
                            <td valign="top"><strong>Country:</strong></td>
                            <td valign="top">@country_name@</td>
                        </tr>
                    </table>
            

        </div>
        </div>
        </div>
        <?= formBoxEnd() ?>
        <br />
        <?php print formBoxEnd(); ?>
        <br />
        <?= backButton(getLink("accounts")) ?>

              
    </div>


  



                         </div>
                        </div>
                        
                    </div>

  
    <?php if ($this->isCarrier == 1) { ?>
        <div class="row  ml-3 mr-3" >
            <div class="col-12">

             <h3>Orders</h3>
          
                        <table id="account_list" class="table-bordered table " >
                            <thead>
                                <tr >
                                    <th >Order ID</th>
                                    <th >Dispatch Date</th>
                                    <th >Origin/Destination</th>
                                    <th >Dispatch Link</th>
                                    <th >Amount</th>
                                </tr>
                            </thead>
                                    
                     
                          
                                <tbody>
                                    <?php if (count($this->commissionData) == 0) { ?>
                                        <tr >
                                            <td colspan="9" align="center" ><i>No records</i></td>
                                        </tr>
                                    <?php } else { ?>
                                        <?php
                                        foreach ($this->commissionData as $i => $entities) {
                                            $entity = $entities['entities'];
                                            $origin = $entity->getOrigin();
                                            $destination = $entity->getDestination();
                                            $file = $entities['files'][0];
                                            $fileArr = explode("-", $file['name_original']);
                                            $fileName = substr($fileArr[0], 0, -5);
                                            $fileDate = date("Y-m-d", strtotime($file['date_uploaded']));
                                            ?>
                                            <tr  align="center"> 
                                                <td>
                                                    <a href="<?= SITE_IN ?>application/orders/show/id/<?= $entity->id ?>">
                                                        <?= $entity->getNumber() ?>
                                                    </a>
                                                    <?php
                                                        if($entity->status == 6){
                                                    ?>
                                                    <br>
                                                    <a href="<?= SITE_IN ?>application/orders/track_n_trace/id/<?= $entity->id ?>">
                                                        Track & Trace
                                                    </a>
                                                    <?php }?>
                                                </td>
                                                <td><?= $entities['accepted'] ?></td>
                                                <td >
                                                    <span class="like-link" onclick="window.open('<?= $origin->getLink() ?>', '_blank')"><?= $origin->getFormatted() ?>
                                                    </span> / 
                                                    <span class="like-link" onclick="window.open('<?= $destination->getLink() ?>')"><?= $destination->getFormatted() ?></span>
                                                    <br/>
                                                </td>
                                                <td >
                                                    <a href="<?= getLink("orders", "getdocs", "id", $file['id']) ?>" target="_blank"><?= $file['img'] ?></a>
                                                </td>


                                                <td >
               
                                                <div class="row">
                                                <img src="<?= SITE_IN ?>images/icons/dollar.png" alt="Total Tariff" title="Total Tariff" width="16" height="16"/>
                                                <span class='<?= $Tcolor; ?>'><?= $entity->getTotalTariff() ?></span>
                                                </div>


                                                <div class="row">
                                                <?php if ($entities['carrier_company_name'] != "" && $entities['carrier_contact_name'] != "") { ?>
                                                <span class="viewsample like-link" style="border-bottom: none">
                                                <img src="<?= SITE_IN ?>images/icons/truck.png" alt="Carrier Pay" title="Carrier Pay" width="16" height="16"/>
                                                </span>
                                                <div class="sample-info carrier-info-popup">
                                                <strong>Company Name: </strong><?= $entities['carrier_company_name'] ?><br/>
                                                <strong>Contact Name: </strong><?= $entities['carrier_contact_name'] ?><br/>
                                                <strong>Phone 1: </strong><?= $entities['carrier_phone_1'] ?><br/>
                                                <strong>Phone 2: </strong><?= $entities['carrier_phone_2'] ?><br/>
                                                <strong>Fax: </strong><?= $entities['carrier_fax'] ?><br/>
                                                <strong>Email: </strong><?= $entities['carrier_email'] ?><br/>
                                                <?php $carrier = $entity->getCarrier(); ?>
                                                <?php if ($carrier instanceof Account) { ?>
                                                <strong>Hours of Operation: </strong><?= $carrier->hours_of_operation ?><br/>
                                                <?php } ?>
                                                <strong>Driver Name: </strong><?= $entities['carrier_driver_name'] ?><br/>
                                                <strong>Driver Phone: </strong><?= $entities['carrier_driver_phone'] ?><br/>
                                                </div>
                                                <?php } else { ?>
                                                <img src="<?= SITE_IN ?>images/icons/truck.png" alt="Carrier Pay" title="Carrier Pay" width="16" height="16"/>
                                                <?php } ?>
                                                <span class='<?= $Ccolor; ?>'><?= $entity->getCarrierPay() ?></span>
                                                </div>



                                                <div class="row">
                                                <img src="<?= SITE_IN ?>images/icons/person.png" alt="Deposit"title="Deposit" width="16" height="16"/>
                                                <span class='<?= $Dcolor; ?>'><?= $entity->getTotalDeposit() ?></span>
                                                </div>

                                                </td>




                                                </tr>
                                                <?php
                                                }
                                                }
                                                ?>
                                                </tbody>
                                                </table>

                                                </div>
                                                <?php } ?>

                                                <!--  -->

                                                </div>

                                            </div>




            </div>              
        </div>
    </div>
</div>
    
    
<script type="text/javascript">
$(document).ready(function() {
$('#account_list').DataTable();
})
</script>
    
    
    











