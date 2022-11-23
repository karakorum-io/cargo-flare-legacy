<!---
/***************************************************************************************************
* Transportation Management Software
*
* Client:			FreightDragon
* Version:			1.0
* Start Date:                   2011-10-05
* Author:			Freight Genie LLC
* E-mail:			admin@freightdragon.com
*
* CopyRight 2011 FreightDragon. - All Rights Reserved
****************************************************************************************************/
--->
<?php
$mobileDevice = 0;
$mobileDevice = detectMobileDevice();
?>
<script type="text/javascript" src="/jscripts/jquery.rateyo.js"></script>
<link rel="stylesheet" href="/styles/jquery.rateyo.min.css"/>
<?php
if (is_array($_SESSION['searchData']) && $_SESSION['searchCount'] > 0) {
    $eid = $_GET['id'];
    $indexSearchData = array_search($eid, $_SESSION['searchData']);
    $nextSearch = $indexSearchData + 1;
    $_SESSION['searchShowCount'] = $indexSearchData;
    $prevSearch = $indexSearchData - 1;
    $entityPrev = $_SESSION['searchData'][$prevSearch];
    $entityNext = $_SESSION['searchData'][$nextSearch];
    ?>
<?php } ?>
<br>
<div>
    <h2 class="review-head-sec"><companyName>----</companyName></h2> 
	<br>
    <h3 class="review-head-sec">Please share your experience with Order #<?= $this->entity->getNumber() ?></h3>    
    <div class="main-sec-text" >

        <div class="pick-sec-text">
            <?php
            $assigned = $this->entity->getAssigned();

            $shipper = $this->entity->getShipper();
            $origin = $this->entity->getOrigin();
            $destination = $this->entity->getDestination();
            $vehicles = $this->entity->getVehicles();
            ?>
            <div style="clear:left;"></div>
            <div class="add-sec-strt">

                <div class="add-city-main-sec">
                    <?php
                    $phone1_ext = '';
                    $phone2_ext = '';
                    $phone3_ext = '';
                    $phone4_ext = '';
                    if ($origin->phone1_ext != '')
                        $phone1_ext = " <b>X</b> " . $origin->phone1_ext;
                    if ($origin->phone2_ext != '')
                        $phone2_ext = " <b>X</b> " . $origin->phone2_ext;
                    if ($origin->phone3_ext != '')
                        $phone3_ext = " <b>X</b> " . $origin->phone3_ext;
                    if ($origin->phone4_ext != '')
                        $phone4_ext = " <b>X</b> " . $origin->phone4_ext;
                    ?>
                    <div class="order-info order-info1">
                        <p class="block-title">Pickup Information</p>
                        <div class="add-inner-sec" > 
                            <div class="frm-group-sec">
                                <div class="inner-sec-add-text"><strong>Address</strong> <span>:</span></div>
                                <div class="add-right-side"><?= $origin->address1; ?>,&nbsp;&nbsp;<?= $origin->address2; ?></div>                                       
                            </div>
                            <div class="frm-group-sec">
                                <div class="inner-sec-add-text"><strong>City</strong> <span>:</span></div>
                                <div class="add-right-side" onclick="window.open('<?= $origin->getLink() ?>')"><?= $origin->getFormatted() ?></div>                                        
                            </div>

                            <div class="frm-group-sec" >
                                <div class="inner-sec-add-text"><strong>Location Type</strong><span>:</span></div>
                                <div class="add-right-side"><?= $origin->location_type; ?></div>
                            </div>
                            <div class="frm-group-sec">
                                <div class="inner-sec-add-text"><strong>Hours</strong><span>:</span></div>
                                <div class="add-right-side"><?= $origin->hours; ?></div>
                            </div>
                        </div>
                    </div>                        


                    <td width="49%" valign="top">
                        <?php
                        $phone1_ext = '';
                        $phone2_ext = '';
                        $phone3_ext = '';
                        $phone4_ext = '';
                        if ($destination->phone1_ext != '')
                            $phone1_ext = " <b>X</b> " . $destination->phone1_ext;
                        if ($destination->phone2_ext != '')
                            $phone2_ext = " <b>X</b> " . $destination->phone2_ext;
                        if ($destination->phone3_ext != '')
                            $phone3_ext = " <b>X</b> " . $destination->phone3_ext;
                        if ($destination->phone4_ext != '')
                            $phone4_ext = " <b>X</b> " . $destination->phone4_ext;
                        ?>
                        <div class="order-info order-info1">
                            <p class="block-title">Dropoff Information</p>
                            <div class="add-inner-sec" > 
                                <div class="frm-group-sec">
                                    <div class="inner-sec-add-text"><strong>Address</strong> <span>:</span></div>
                                    <div class="add-right-side"><?= $destination->address1; ?>,&nbsp;&nbsp;<?= $destination->address2; ?></div>                                       
                                </div>
                                <div class="frm-group-sec">
                                    <div class="inner-sec-add-text"><strong>City</strong> <span>:</span></div>
                                    <div class="add-right-side" onclick="window.open('<?= $destination->getLink() ?>')"><?= $destination->getFormatted() ?></div>                                        
                                </div>

                                <div class="frm-group-sec" >
                                    <div class="inner-sec-add-text"><strong>Location Type</strong><span>:</span></div>
                                    <div class="add-right-side"><?= $destination->location_type; ?></div>
                                </div>
                                <div class="frm-group-sec">
                                    <div class="inner-sec-add-text"><strong>Hours</strong><span>:</span></div>
                                    <div class="add-right-side"><?= $destination->hours; ?></div>
                                </div>
                            </div>

                        </div> 
                </div>

            </div>
            <div class="order-info vehcl-sec-text">
                <p class="block-title">Vehicle(s) Information</p>
                <div class="table-btm-scroll">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr bgcolor="#297eaf" >
                            <td  style="padding:3px;"><b><center><font color="white">S.No.</font></center></b></td>
                            <td  style="padding:3px;"><b><center><font color="white"><?= Year ?></font></center></b></td>
                            <td  style="padding:3px;"><b><center><font color="white"><?= Make ?></font></center></b></td>
                            <td  style="padding:3px;"><b><center><font color="white"><?= Model ?></font></center></b></td>
                            <td  style="padding:3px;"><b><center><font color="white"><?= Inop ?></font></center></b></td>
                            <td  style="padding:3px;"><b><center><font color="white"><?= Type ?></font></center></b></td> 
                            <td  style="padding:3px;"><b><center><font color="white"><?= Vin#       ?></font></center></b></td>
                            <td  style="padding:3px;"><b><center><font color="white"><?= Lot#       ?></font></center></b></td>                            
                        </tr>
                        <?php
                        $vehiclecounter = 0;
                        foreach ($vehicles as $vehicle) :
                            $vehiclecounter = $vehiclecounter + 1;
                            ?>
                            <tr>
                                <td align="center" bgcolor="#ffffff" style="padding:3px;"><?= $vehiclecounter ?></td>
                                <td align="center" bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->year ?></td>
                                <td align="center" bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->make ?></td>
                                <td align="center" bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->model ?></td> 
                                <td align="center" bgcolor="#ffffff" style="padding-left:5px;"> <?php print $vehicle->inop == 0 ? "No" : "Yes"; ?></td>
                                <td align="center" bgcolor="#ffffff" style="padding:3px;"><?= $vehicle->type ?></td>
                                <td align="center" bgcolor="#ffffff" style="padding:3px;"> <?php print $vehicle->vin ?></td>
                                <td align="center" bgcolor="#ffffff" style="padding:3px;"> <?php print $vehicle->lot ?></td>                                
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
            <?php
            if (isset($this->dispatchSheet)) {

                $phone1_ext = '';
                $phone2_ext = '';
                if ($this->dispatchSheet->carrier_phone1_ext != '')
                    $phone1_ext = " <b>X</b> " . $this->dispatchSheet->carrier_phone1_ext;
                if ($this->dispatchSheet->carrier_phone2_ext != '')
                    $phone2_ext = " <b>X</b> " . $this->dispatchSheet->carrier_phone2_ext;
                ?>
                <div class="quote-info" style="width:97%; margin-bottom: 10px;">
                    <div ><p class="block-title">Carrier Information<span style="float:right;color:#09C;" onclick="editCarrier('<?= $this->dispatchSheet->id ?>');">Edit</span></p></div>
                    <div id="carrier_value">
                        <table cellspacing="0" cellpadding="0" border="0" width="100%">
                            <tr>
                                <td style="vertical-align:top;" valign="top" width="50%">
                                    <table width="100%" cellpadding="1" cellpadding="1">

                                        <tr><td width="23%"><strong>Company Name</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $this->dispatchSheet->carrier_company_name ?></td></tr>
                                        <tr><td width="23%"><strong>Contact Name</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $this->dispatchSheet->carrier_contact_name ?></td></tr>
                                        <tr><td width="23%"><strong>Phone 1</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= formatPhone($this->dispatchSheet->carrier_phone_1); ?><?= $phone1_ext ?></td></tr>
                                        <tr><td width="23%"><strong>Phone 2</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= formatPhone($this->dispatchSheet->carrier_phone_2); ?><?= $phone2_ext ?></td></tr>
                                        <tr><td width="23%"><strong>Fax</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $this->dispatchSheet->carrier_fax ?></td></tr>
                                        <tr><td width="23%"><strong>Email</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><a href="mailto:<?= $this->dispatchSheet->carrier_email ?>"><?= $this->dispatchSheet->carrier_email ?></a></td></tr>
                                        <?php $carrier = $this->entity->getCarrier(); ?>
                                        <?php if ($carrier instanceof Account) { ?>
                                            <tr><td width="23%"><strong>Hours of Operation</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $carrier->hours_of_operation ?></td></tr>

                                        <?php } ?>

                                        <tr><td width="23%"><strong>Driver Name</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= $this->dispatchSheet->carrier_driver_name ?></td></tr>
                                        <tr><td width="23%"><strong>Driver Phone</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><?= formatPhone($this->dispatchSheet->carrier_driver_phone); ?></td></tr>
                                    </table>
                                </td>
                                <td style="vertical-align:top;">
                                    <table width="100%" cellpadding="1" cellspacing="1">
                                        <tr>
                                            <td width="23%"><b>Payment Terms:</b> </td>
                                            <td>
                                                <?php
                                                $payments_terms = $this->entity->payments_terms;
                                                if (in_array($this->entity->balance_paid_by, array(2, 3, 16, 17, 8, 9, 18, 19))) {
                                                    $payments_terms = "COD / COP";
                                                }
                                                if ($payments_terms != "") {
                                                    ?>
                                                    <?php print $payments_terms; ?>
                                                <?php } ?>
                                            </td>

                                        </tr>

                                        <?php
                                        if ($this->entity->carrier_id > 0) {

                                            $carrier = new Account($this->daffny->DB);

                                            try {
                                                if ($this->entity->carrier_id != 0 && $this->entity->carrier_id != '') {
                                                    $carrierObj = $carrier->load($this->entity->carrier_id);
                                                    if ($carrier->insurance_doc_id > 0) {
                                                        $insurance_expirationdate = date("m/d/y", strtotime($carrier->insurance_expirationdate));
                                                        ?>
                                                        <tr>
                                                            <td><b>Insurance Doc:</b> </td>
                                                            <td><a href="<?= SITE_IN ?>application/accounts/getdocs/id/<?= $carrier->insurance_doc_id ?>/type/1" title="Expire Date: <?= $insurance_expirationdate ?>"><img src="<?= SITE_IN ?>images/ins_doc.png" width="40" height="40"></a></td>
                                                        </tr>
                                                        <tr>
                                                            <td><b>Insurance Expire:</b> </td>
                                                            <td><?= $insurance_expirationdate ?></td>
                                                        </tr>

                                                        <?php
                                                    }
                                                }
                                            } catch (FDException $e) {
                                                //continue;
                                            }
                                        }
                                        ?>
                                    </table>
                                <td>
                            </tr>    
                        </table>            
                    </div>

                    <div id="carrier_edit" style="display:none;">
                        <table cellspacing="0" cellpadding="0" border="0" width="100%">
                            <tr>
                                <td style="vertical-align:top;" valign="top" width="50%">
                                    <table width="100%" cellpadding="1" cellpadding="1">

                                        <tr><td width="23%"><strong>Company Name</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><input type="text" size="50" name="carrier_company_name" id="carrier_company_name" value="<?= $this->dispatchSheet->carrier_company_name ?>" /></td></tr>
                                        <tr><td width="23%"><strong>Contact Name</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><input type="text" size="50" name="carrier_contact_name" id="carrier_contact_name" value="<?= $this->dispatchSheet->carrier_contact_name ?>" /></td></tr>
                                        <tr><td width="23%"><strong>Phone 1</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><input type="text" size="40" name="carrier_phone_1" id="carrier_phone_1" value="<?= $this->dispatchSheet->carrier_phone_1 ?>" /></td></tr>
                                        <tr><td width="23%"><strong>Phone 2</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><input type="text" size="40" name="carrier_phone_2" id="carrier_phone_2" value="<?= $this->dispatchSheet->carrier_phone_2 ?>" /></td></tr>
                                        <tr><td width="23%"><strong>Fax</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><input type="text" size="40" name="carrier_fax" id="carrier1_fax" value="<?= $this->dispatchSheet->carrier_fax ?>" /></td></tr>
                                        <tr><td width="23%"><strong>Email</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><input type="text" size="50" name="carrier_email" id="carrier1_email" value="<?= $this->dispatchSheet->carrier_email ?>" /></td></tr>
                                        <?php $carrier = $this->entity->getCarrier(); ?>
                                        <?php if ($carrier instanceof Account) { ?>
                                            <tr><td width="23%"><strong>Hours of Operation</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><input type="text" size="50" name="hours_of_operation" id="hours_of_operation" value="<?= $this->dispatchSheet->hours_of_operation ?>" /></td></tr>

                                        <?php } ?>

                                        <tr><td width="23%"><strong>Driver Name</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><input type="text" size="40" name="carrier_driver_phone" id="carrier1_driver_phone" value="<?= $this->dispatchSheet->carrier_driver_phone ?>" /></td></tr>
                                        <tr><td width="23%"><strong>Driver Phone</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><input type="text" size="50" name="carrier_driver_name" id="carrier_driver_name" value="<?= $this->dispatchSheet->carrier_driver_name ?>" /></td></tr>
                                        <tr><td colspan="3" align="center"><input type="button" name="CarrierUpdate" value="Update"  onclick="updateCarrier('<?= $this->dispatchSheet->id ?>');"/>
                                                &nbsp;&nbsp;&nbsp;<input type="button" name="CarrierCancel" value="Cancel"  onclick="cancelCarrier('<?= $this->dispatchSheet->id ?>');"/>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="vertical-align:top;">
                                    <?php
                                    $payments_terms = $this->entity->payments_terms;
                                    if (in_array($this->entity->balance_paid_by, array(2, 3, 16, 17, 8, 9, 18, 19))) {
                                        $payments_terms = "COD / COP";
                                    }
                                    if ($payments_terms != "") {
                                        ?>
                                        <b>Payment Terms:</b> <?php print $payments_terms; ?>
                                    <?php } ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div style="clear:right;"></div>
            <?php } ?>
            <div style="clear:left;"></div>
            <div class="order-info order-info1">
                <p class="block-title">Over all Experience with <companyName>----</companyName></p>
                <div class="add-inner-sec">
                    <center>
                        <br>
                        Please share your experience with <assignedName>----</assignedname> and rest of our team
                        <br><br>
						(Select your star rating below)<font color="red"><strong>*</strong></font>
                        <div id="rateyo" class="rateyo"></div>
                        <br>
                    <p style="text-align: left !important; margin-left:10%;">Comment:<font color="red"><strong>*</strong></font></p>
                    <textarea id="comment" style="width:80%; height:70px;" ></textarea>
                    </center>
                </div>
            </div>   
            <div class="order-info order-info1">
                <p class="block-title">Over all experience with truck driver?</p>
                <div class="add-inner-sec" >
                    <center>
                    <br>
                    Please rate your experience with the driver involved in this shipment.
                    <br><br>
					(select your star rating below)<font color="red"><strong>*</strong></font>
                    <div id="rateyo2" class="rateyo"></div>
                    <br>
                    <p style="text-align: left !important; margin-left:10%;">Comment:<font color="red"><strong>*</strong></font></p>
                    <textarea id="comment2" style="width:80%; height:70px;"></textarea>
                    </center>
                </div>
            </div>
            <input type="hidden" id="update" name="update" value="0">
            <button class="searchform-button searchform-buttonhover submitReview"><b>Rate Now</b></button>
            <script>
                var star;
                var star2;

                $(function () {
                    $("#rateyo").rateYo({ halfStar: true, ratedFill: "#419111"}).on("rateyo.change", function (e, data) {
                        star = data.rating;
                        console.log(star);
                    });
                });

                $(function () {

                    $("#rateyo2").rateYo({halfStar: true, ratedFill: "#419111"}).on("rateyo.change", function (e, data) {
                        star2 = data.rating;
                        console.log(star2);
                    });
                });

                $(".submitReview").click(function () {

                    var comment = $("#comment").val();
                    var comment2 = $("#comment2").val();
                    var update = $("#update").val();
                    if (star == 0 || comment == "" || star2 == 0 || comment2 == "") {
                        alert('Rating and Comment both are mandiatory!');
                    } else {
                        $.ajax({
                            type: "POST",
                            url: "<?php echo SITE_IN; ?>review/ajax/entities.php",
                            dataType: "json",
                            data: {
                                action: 'addComment',
                                entity_id: <?php echo $this->entity->id; ?>,
                                star: star,
                                star2: star2,
                                comment: comment,
                                comment2: comment2,
                                update: update
                            },
                            success: function (res) {
                                window.location.replace("<?php echo SITE_IN; ?>review/orders/response");
                            }
                        });
                    }
                });
                $(document).ready(function () {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo SITE_IN; ?>review/ajax/entities.php",
                        dataType: "json",
                        data: {
                            action: 'getComment',
                            entity_id: <?php echo $this->entity->id; ?>
                        },
                        success: function (res) {
                            if (res.id != null) {
                                if(res.id != null || res.id != ''){
                                    window.location.replace("<?php echo SITE_IN; ?>review/orders/response/id/1");
                                }
                            } else {
                                $('companyName').html(res.companyName);
                                $('assignedName').html(res.contactName);
                            }
                        }
                    });
                });
            </script>          
        </div>
    </div>
</div>
<?php
$_SESSION['member_chmod'] = 0;
$_SESSION['member_id'] = 0;
?>