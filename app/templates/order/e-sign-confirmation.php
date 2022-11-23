<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en" xml:lang="en">
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>E-Sign Confirmation</title>
        
        <link rel="shortcut icon" href="<?php echo SITE_IN; ?>styles/favicon.ico" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.css" rel="stylesheet"/>
        
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/e-sign-form.css"/>
        <link href="<?php echo SITE_IN ?>styles/new/assets/vendors/general/sweetalert2/dist/sweetalert2.css" rel="stylesheet" type="text/css"/>

        <script type="text/javascript">var BASE_PATH = '<?=SITE_PATH?>';</script>
        <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.min.js"></script>
        
        <script type="text/javascript" src="<?=SITE_IN?>jscripts/application-v2.js"></script>
        <script type="text/javascript" src="<?=SITE_IN?>jscripts/e-sign.js"></script>
        <script src="<?php echo SITE_IN ?>styles/new/assets/vendors/general/sweetalert2/dist/sweetalert2.min.js" type="text/javascript"></script>
        <script src="<?php echo SITE_IN ?>styles/new/assets/vendors/custom/components/vendors/sweetalert2/init.js" type="text/javascript"></script>
    </head>
    <body>
        <header class="py-2">
            <div class="">
                <div class="row w-100">
                    <div class="col-12 text-center" id="logo">
                        <img alt="Cargo Flare" src="@company_logo@" width="230">
                    </div>
                </div>
            </div>
        </header>
        <?php
            $assigned = $this->entity->getAssigned();
            $shipper = $this->entity->getShipper();
            $origin = $this->entity->getOrigin();
            $destination = $this->entity->getDestination();
        ?>
        <div class="container my-4">
            <div class="row">
                <div class="col-12 mb-2">
                    <h4>Please review and sign the following document</h4>
                </div>
                <div class="col-12 col-sm-9">
                    <div class="content_main">
                        <div class="row row-wrapper">
                            <div class="col-12 col-sm-12 header-row">
                                <h4>Order Information</h4>
                            </div>
                            <div class="col-12 col-sm-6 south-west-border">
                                <h5 class="sub-heading">Shipper Details</h5>
                                <div class="row location-row">
                                    <div class="col-4">Account ID</div>
                                    <div class="col-1">:</div>
                                    <div class="col-6"><?= $this->entity->account_id?></div>
                                </div>
                                <div class="row location-row">
                                    <div class="col-4">Name</div>
                                    <div class="col-1">:</div>
                                    <div class="col-6"><?= $shipper->fname ?> <?= $shipper->lname ?></div>
                                </div>
                                <div class="row location-row">
                                    <div class="col-4">Company</div>
                                    <div class="col-1">:</div>
                                    <div class="col-6"><?= $shipper->company; ?></div>
                                </div>
                                <div class="row location-row">
                                    <div class="col-4">Email</div>
                                    <div class="col-1">:</div>
                                    <div class="col-6"><?= $shipper->email ?></div>
                                </div>
                                <div class="row location-row">
                                    <div class="col-4">Phone 1</div>
                                    <div class="col-1">:</div>
                                    <div class="col-6"><?= formatPhone($shipper->phone1) ?></div>
                                </div>
                                <div class="row location-row">
                                    <div class="col-4">Phone 2</div>
                                    <div class="col-1">:</div>
                                    <div class="col-6"><?php print $phone2;?></div>
                                </div>
                                <div class="row location-row">
                                    <div class="col-4">Mobile</div>
                                    <div class="col-1">:</div>
                                    <div class="col-6"><?= formatPhone($shipper->mobile); ?></div>
                                </div>
                                <div class="row location-row">
                                    <div class="col-4">Fax</div>
                                    <div class="col-1">:</div>
                                    <div class="col-6"><?= formatPhone($shipper->fax); ?></div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 south-east-border">
                                <h5 class="sub-heading">Order Details</h5>
                                <div class="row location-row">
                                    <div class="col-4">1st Avail. Pickup</div>
                                    <div class="col-1">:</div>
                                    <div class="col-6"><?= $this->entity->getFirstAvail("m/d/y") ?></div>
                                </div>
                                <div class="row location-row">
                                    <div class="col-4">Payment Method</div>
                                    <div class="col-1">:</div>
                                    <div class="col-6"><font color="red"><?= $this->entity->getPaymentOption($this->entity->customer_balance_paid_by); ?></font></div>
                                </div>
                                <div class="row location-row">
                                    <?php
                                        $Balance_Paid_By = "";
                                        if(in_array($this->entity->balance_paid_by, array(2, 3 , 16 , 17)))
                                            $Balance_Paid_By = "<b>COD</b>";
                                        
                                        if(in_array($this->entity->balance_paid_by, array(8, 9 , 18 , 19)))
                                            $Balance_Paid_By = "COP";
                                        
                                        if(in_array($this->entity->balance_paid_by, array(12, 13 , 20 , 21,24)))
                                            $Balance_Paid_By = "Billing";
                                        
                                        if(in_array($this->entity->balance_paid_by, array(14, 15 , 22 , 23)))
                                            $Balance_Paid_By = "Billing";
                                    ?>
                                    <div class="col-4">Carrier Paid By</div>
                                    <div class="col-1">:</div>
                                    <div class="col-6"><?= $Balance_Paid_By; ?></div>
                                </div>
                                <div class="row location-row">
                                    <div class="col-4">Ship Via</div>
                                    <div class="col-1">:</div>
                                    <div class="col-6"><?= $this->entity->getShipVia() ?></div>
                                </div>
                                <?php if (is_numeric($this->entity->distance) && ($this->entity->distance > 0)) : ?>
                                <div class="row location-row">
                                    <div class="col-4">Mileage</div>
                                    <div class="col-1">:</div>
                                    <div class="col-6"><?= number_format($this->entity->distance, 0, "", "") ?> mi($ <?= number_format(($this->entity->getCarrierPay(false) / $this->entity->distance), 2, ".", ",") ?>/mi)</div>
                                </div>
                                <?php endif; ?>
                                <div class="row location-row">
                                    <div class="col-4">Assigned to</div>
                                    <div class="col-1">:</div>
                                    <div class="col-6"><?= $assigned->contactname ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="row row-wrapper no-padding">
                            <div class="col-12 col-sm-6 side-by border-right">
                                <div class="row">
                                    <div class="header-row">
                                        <h4>Pickup Information</h4>
                                    </div>
                                    <?php
                                        $phone1_ext = '';
                                        $phone2_ext = '';
                                        $phone3_ext = '';
                                        $phone4_ext = '';
                                        if($origin->phone1_ext != '')
                                        $phone1_ext = " <b>X</b> ".$origin->phone1_ext;
                                        if($origin->phone2_ext != '')
                                        $phone2_ext = " <b>X</b> ".$origin->phone2_ext;
                                        if($origin->phone3_ext != '')
                                        $phone3_ext = " <b>X</b> ".$origin->phone3_ext;
                                        if($origin->phone4_ext != '')
                                        $phone4_ext = " <b>X</b> ".$origin->phone4_ext;
                                    ?>
                                    <div class="south-west-border">
                                        <div class="row location-row">
                                            <div class="col-4">Address</div>
                                            <div class="col-1">:</div>
                                            <div class="col-6"><?= $origin->address1; ?>,&nbsp;&nbsp;<?= $origin->address2; ?></div>
                                        </div>
                                        <div class="row location-row">
                                            <div class="col-4">City</div>
                                            <div class="col-1">:</div>
                                            <div class="col-6"><?= $origin->getFormatted() ?></div>
                                        </div>
                                        <div class="row location-row">
                                            <div class="col-4">Location Type</div>
                                            <div class="col-1">:</div>
                                            <div class="col-6"><?= $origin->location_type; ?></div>
                                        </div>
                                        <div class="row location-row">
                                            <div class="col-4">Hours</div>
                                            <div class="col-1">:</div>
                                            <div class="col-6"><?= $origin->hours; ?></div>
                                        </div>
                                        <div class="row location-row">
                                            <div class="col-4">Contact Name 1</div>
                                            <div class="col-1">:</div>
                                            <div class="col-6"><?= $origin->name; ?></div>
                                        </div>
                                        <div class="row location-row">
                                            <div class="col-4">Contact Name 2</div>
                                            <div class="col-1">:</div>
                                            <div class="col-6"><?= $origin->name2; ?></div>
                                        </div>
                                        <div class="row location-row">
                                            <div class="col-4">Company Name</div>
                                            <div class="col-1">:</div>
                                            <div class="col-6"><?= $origin->company; ?></div>
                                        </div>
                                        <div class="row location-row">
                                            <div class="col-4">Company Phone</div>
                                            <div class="col-1">:</div>
                                            <div class="col-6"><?= formatPhone($origin->phone3); ?><?= $phone3_ext ?></div>
                                        </div>
                                        <div class="row location-row">
                                            <div class="col-4">Company Fax</div>
                                            <div class="col-1">:</div>
                                            <div class="col-6"><?= formatPhone($origin->fax); ?></div>
                                        </div>
                                        <div class="row location-row">
                                            <div class="col-4">Auction Name</div>
                                            <div class="col-1">:</div>
                                            <div class="col-6"><?= $origin->auction_name; ?></div>
                                        </div>
                                        <div class="row location-row">
                                            <div class="col-4">Auction Phone</div>
                                            <div class="col-1">:</div>
                                            <div class="col-6"><?= formatPhone($origin->phone4); ?><?= $phone4_ext ?></div>
                                        </div>
                                        <div class="row location-row">
                                            <div class="col-4">Auction Fax</div>
                                            <div class="col-1">:</div>
                                            <div class="col-6"><?= formatPhone($origin->fax2); ?></div>
                                        </div>
                                        <div class="row location-row">
                                            <div class="col-4">Buyer Number</div>
                                            <div class="col-1">:</div>
                                            <div class="col-6"><?= $origin->buyer_number; ?></div>
                                        </div>
                                        <div class="row location-row">
                                            <div class="col-4">Booking Number</div>
                                            <div class="col-1">:</div>
                                            <div class="col-6"><?= $origin->booking_number; ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 side-by border-left">
                                <div class="row">
                                    <div class="header-row">
                                        <h4>Dropoff Information</h4>
                                    </div>
                                    <?php
                                        $phone1_ext ='';
                                        $phone2_ext ='';
                                        $phone3_ext ='';
                                        $phone4_ext ='';
                                        if($destination->phone1_ext!='')
                                            $phone1_ext = " <b>X</b> ".$destination->phone1_ext;
                                        if($destination->phone2_ext!='')
                                            $phone2_ext = " <b>X</b> ".$destination->phone2_ext;
                                        if($destination->phone3_ext!='')
                                            $phone3_ext = " <b>X</b> ".$destination->phone3_ext;
                                        if($destination->phone4_ext!='')
                                            $phone4_ext = " <b>X</b> ".$destination->phone4_ext;
                                    ?>
                                    <div class="south-east-border">
                                        <div class="row location-row">
                                            <div class="col-4">Address</div>
                                            <div class="col-1">:</div>
                                            <div class="col-6"><?= $destination->address1; ?>,&nbsp;&nbsp;<?= $destination->address2; ?></div>
                                        </div>
                                        <div class="row location-row">
                                            <div class="col-4">City</div>
                                            <div class="col-1">:</div>
                                            <div class="col-6"><?= $destination->getFormatted() ?></div>
                                        </div>
                                        <div class="row location-row">
                                            <div class="col-4">Location Type</div>
                                            <div class="col-1">:</div>
                                            <div class="col-6"><?= $destination->location_type; ?></div>
                                        </div>
                                        <div class="row location-row">
                                            <div class="col-4">Hours</div>
                                            <div class="col-1">:</div>
                                            <div class="col-6"><?= $destination->hours; ?></div>
                                        </div>
                                        <div class="row location-row">
                                            <div class="col-4">Contact Name 1</div>
                                            <div class="col-1">:</div>
                                            <div class="col-6"><?= $destination->name; ?></div>
                                        </div>
                                        <div class="row location-row">
                                            <div class="col-4">Contact Name 2</div>
                                            <div class="col-1">:</div>
                                            <div class="col-6"><?= $destination->name2; ?></div>
                                        </div>
                                        <div class="row location-row">
                                            <div class="col-4">Company Name</div>
                                            <div class="col-1">:</div>
                                            <div class="col-6"><?= $destination->company; ?></div>
                                        </div>
                                        <div class="row location-row">
                                            <div class="col-4">Company Phone</div>
                                            <div class="col-1">:</div>
                                            <div class="col-6"><?= formatPhone($destination->phone3); ?><?= $phone3_ext ?></div>
                                        </div>
                                        <div class="row location-row">
                                            <div class="col-4">Company Fax</div>
                                            <div class="col-1">:</div>
                                            <div class="col-6"><?= formatPhone($destination->fax); ?></div>
                                        </div>
                                        <div class="row location-row">
                                            <div class="col-4">Auction Name</div>
                                            <div class="col-1">:</div>
                                            <div class="col-6"><?= $destination->auction_name; ?></div>
                                        </div>
                                        <div class="row location-row">
                                            <div class="col-4">Auction Phone</div>
                                            <div class="col-1">:</div>
                                            <div class="col-6"><?= formatPhone($destination->phone4); ?><?= $phone4_ext ?></div>
                                        </div>
                                        <div class="row location-row">
                                            <div class="col-4">Auction Fax</div>
                                            <div class="col-1">:</div>
                                            <div class="col-6"><?= formatPhone($destination->fax2); ?></div>
                                        </div>
                                        <div class="row location-row">
                                            <div class="col-4">Buyer Number</div>
                                            <div class="col-1">:</div>
                                            <div class="col-6"><?= $destination->buyer_number; ?></div>
                                        </div>
                                        <div class="row location-row">
                                            <div class="col-4">Booking Number</div>
                                            <div class="col-1">:</div>
                                            <div class="col-6"><?= $destination->booking_number; ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row row-wrapper">
                            <div class="col-12 col-sm-12 header-row">
                                <h4>Vehicle(s) Information</h4>
                            </div>
                            <div class="col-12 col-sm-12 info-section south-west-border border-right vehicle-table">
                                @vehicle_table@
                            </div>
                        </div>
                        <div class="row row-wrapper">
                            <div class="col-12 col-sm-12 header-row">
                                <h4>Terms and Conditions</h4>
                            </div>
                            <div class="col-12 col-sm-12 info-section south-west-border border-right" style="max-height:calc(50vh - 170px);overflow:auto;">
                                <div class="row">
                                    <div class="col-12 col-sm-12 text-justified">
                                        @company_order_terms@
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-3">
                    <div class="form_step_info mb-4 no-background no-padding no-border">
                        <div class="col-12 col-sm-12 header-row">
                            <h4>Pricing</h4>
                        </div>
                        <div class="col-12 col-sm-12 info-section south-west-border border-right vehicle-table">
                            <?php
                                if($this->entity->balance_paid_by == 2 || $this->entity->balance_paid_by == 3 || $this->entity->balance_paid_by == 8 || $this->entity->balance_paid_by == 9){
                            ?>
                            <div class="row">
                                <div class="col-7">Carrier Fee:</div>
                                <div class="col-5 text-right">@entity_carrier_pay@</div>
                            </div>
                            <div class="row">
                                <div class="col-7">Dispatch Fee:</div>
                                <div class="col-5 text-right">@entity_total_deposit@</div>
                            </div>
                            <div class="row">
                                <div class="col-7">Total Cost:</div>
                                <div class="col-5 text-right">@entity_total_tariff@</div>
                            </div>
                            <?php
                                } else {
                            ?>
                            <div class="row">
                                <div class="col-7">Total Cost:</div>
                                <div class="col-5 text-right">@entity_total_tariff@</div>
                            </div>
                            <?php
                                }
                            ?>
                            
                        </div>
                    </div>

                    <div class="form_step_info mb-4 mobile">
                        <h6>I am authorized to sign on behalf of: <br><a href="javascript:void(0);" id="typedName"></a></h6>
                        <div class="sign_name">
                            <label for="sign_name">Print Your Name:</label>
                            <input type="text" onkeyup="$eSign.typeName(this)" class="form-control" id="signature">
                        </div>
                        <div class="sign_preview">
                            <label for="sign_name">Leave us a Note:</label>
                            <textarea class="form-control" id="notes" rows="5"></textarea>
                        </div>
                        <div class="accept_info mb-4">
                            <br/>
                            <label class="checkbox_main">I have read, and understand, the attached Terms and Conditions and I intend, and agree, to be bound by them.
                                <input type="checkbox" id="esign-terms" name="esign-terms">
                                <span class="checkmark"></span>
                            </label>
                        </div>
                    </div>

                    <button onclick="$eSign.sign(this, '<?php echo $_GET['hash']?>', '<?php echo $this->entity->id;?>', 'order')" class="btn btn-danger custm-btn eSignNow mobile red-btn">Click To E-Sign</button>
                    <!---
                    <div class="form_step_info mb-4">
                        <ol>
                            <li>The wise man therefore always holds</li>
                            <li>But in certain circumstances and owing to the claims</li>
                            <li>Which is the same as saying through shrinking</li>
                            <li>Amnis voluptas assumenda est, omnis dolor rep</li>
                        </ol>
                    </div>--->
                    
                    <div class="doucment_access_info">
                        <h6 class="text-center">Document Accessed from</h6>
                        <strong>Browser</strong>
                        <p><?php echo $_SERVER['HTTP_USER_AGENT'];?></p>
                        <strong>IP Address</strong>
                        <p><?php echo $_SERVER['REMOTE_ADDR'];?></p>
                    </div>
                    <br/>
                    <div class="form_step_info mb-4 text-center">
                        <br/>
                        <p>Powered By</p>
                        <img alt="Cargo Flare" src="<?=SITE_PATH?>styles/cargo_flare/logo.png" width="200">
                        <br/>
                        <br/>
                        <p>www.cargoflare.com</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trigger/Open The Modal -->
        <button id="myBtn" class="float desktop red-btn">
            <span class="fa fa-plus my-float">Click here to eSign</span>
        </button>
        <!-- The Modal -->
        <div id="myModal" class="modal">
            <!-- Modal content -->
            <div class="modal-content">
                <div class="text-right">
                    <span class="close">&times;</span>
                </div>
                <div>
                    <p>I am authorized to sign on behalf of: <br><a href="javascript:void(0);" id="typedName"></a></p>
                    <div class="sign_name">
                        <label for="sign_name">Print Your Name:</label>
                        <input type="text" onkeyup="$eSign.typeName(this)" class="form-control" id="signature">
                    </div>
                    <div class="sign_preview">
                        <label for="sign_name">Leave us a Note:</label>
                        <textarea class="form-control" id="notes-mobile" rows="5"></textarea>
                    </div>
                    <div class="accept_info mb-4">
                        <br/>
                        <label class="checkbox_main">I have read, and understand, the attached Terms and Conditions and I intend, and agree, to be bound by them.
                            <input type="checkbox" id="esign-terms-mobile" name="esign-terms">
                            <span class="checkmark"></span>
                        </label>
                    </div>
                    <button onclick="$eSign.signMobile(this, '<?php echo $_GET['hash']?>', '<?php echo $this->entity->id;?>', 'order')" class="btn btn-primary eSignNow">Click To E-Sign</button>
                </div>
            </div>
        </div>
        
        <script>
            // Get the modal
            var modal = document.getElementById("myModal");

            // Get the button that opens the modal
            var btn = document.getElementById("myBtn");

            // Get the <span> element that closes the modal
            var span = document.getElementsByClassName("close")[0];

            // When the user clicks on the button, open the modal
            btn.onclick = function() {
                modal.style.display = "block";
            }

            // When the user clicks on <span> (x), close the modal
            span.onclick = function() {
                modal.style.display = "none";
            }

            // When the user clicks anywhere outside of the modal, close it
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }

            setTimeout(() => {
                console.log("Checked");
                $("#esign-terms-mobile").attr("checked",true);
                $("#esign-terms").attr("checked",true);    
            }, 200);
        </script>
    </body>
</html>
