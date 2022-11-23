<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en" xml:lang="en">
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>E-Sign Dispatch</title>
        
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
        <div class="container my-4">
            <div class="row">
                <div class="col-12 mb-2">
                    <h4>Dispatch Sheet for Order #<?php echo $this->entity->prefix?>-<?php echo $this->entity->number?></h4>
                </div>
                <div class="col-12 col-sm-9">
                    <div class="row row-wrapper no-padding">
                        <div class="col-12 col-sm-6 side-by border-right">
                            <div class="row">
                                <div class="header-row">
                                    <h4>Carrier Information</h4>
                                </div>
                                <div class="south-west-border">
                                    <div class="row location-row">
                                        <div class="col-4">Company Name</div>
                                        <div class="col-1">:</div>
                                        <div class="col-6">@carrier_company_name@</div>
                                    </div>
                                    <div class="row location-row">
                                        <div class="col-4">Contact Name</div>
                                        <div class="col-1">:</div>
                                        <div class="col-6">@carrier_print_name@</div>
                                    </div>
                                    <div class="row location-row">
                                        <div class="col-4">Phone 1</div>
                                        <div class="col-1">:</div>
                                        <div class="col-6">@carrier_phone_1@</div>
                                    </div>
                                    <div class="row location-row">
                                        <div class="col-4">Phone 2</div>
                                        <div class="col-1">:</div>
                                        <div class="col-6">@carrier_phone_2@</div>
                                    </div>
                                    <div class="row location-row">
                                        <div class="col-4">Fax</div>
                                        <div class="col-1">:</div>
                                        <div class="col-6">@carrier_fax@</div>
                                    </div>
                                    <div class="row location-row">
                                        <div class="col-4">Email</div>
                                        <div class="col-1">:</div>
                                        <div class="col-6">@carrier_email@</div>
                                    </div>
                                    <div class="row location-row">
                                        <div class="col-4">Driver Name</div>
                                        <div class="col-1">:</div>
                                        <div class="col-6"></div>
                                    </div>
                                    <div class="row location-row">
                                        <div class="col-4">Driver Phone</div>
                                        <div class="col-1">:</div>
                                        <div class="col-6"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 side-by border-left">
                            <div class="row">
                                <div class="header-row">
                                    <h4>Order Information</h4>
                                </div>
                                <div class="south-east-border">
                                    <div class="row location-row">
                                        <div class="col-4">Ship Via</div>
                                        <div class="col-1">:</div>
                                        <div class="col-6">@ship_via@</div>
                                    </div>
                                    <div class="row location-row">
                                        <div class="col-4">Origin</div>
                                        <div class="col-1">:</div>
                                        <div class="col-6">@origin@</div>
                                    </div>
                                    <div class="row location-row">
                                        <div class="col-4">Destination</div>
                                        <div class="col-1">:</div>
                                        <div class="col-6">@destination@</div>
                                    </div>
                                    <div class="row location-row">
                                        <div class="col-12">&nbsp;</div>
                                    </div>
                                    <div class="row location-row">
                                        <div class="col-12">&nbsp;</div>
                                    </div>
                                    <div class="row location-row">
                                        <div class="col-12">&nbsp;</div>
                                    </div>
                                    <div class="row location-row">
                                        <div class="col-12">&nbsp;</div>
                                    </div>
                                    <div class="row location-row">
                                        <div class="col-12">&nbsp;</div>
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
                            <h4>Dispatch Instructions</h4>
                        </div>
                        <div class="col-12 col-sm-12 info-section south-west-border border-right" style="max-height:calc(50vh - 170px);overflow:auto;">
                            <div class="row">
                                <div class="col-12 col-sm-12 text-justified">
                                    @instructions@
                                    <br/>
                                    <br/>
                                    @information@
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row row-wrapper">
                        <div class="col-12 col-sm-12 header-row">
                            <h4>Payments Terms</h4>
                        </div>
                        <div class="col-12 col-sm-12 info-section south-west-border border-right" style="max-height:calc(50vh - 170px);overflow:auto;">
                            <div class="row">
                                <div class="col-12 col-sm-12 text-justified">
                                    @payment_terms@
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row row-wrapper">
                        <div class="col-12 col-sm-12 header-row">
                            <h4>Terms and Conditions</h4>
                        </div>
                        <div class="col-12 col-sm-12 info-section south-west-border border-right" style="max-height:calc(50vh - 170px);overflow:auto;">
                            <div class="row">
                                <div class="col-12 col-sm-12 text-justified">
                                    @dispatch_terms@
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-3">
                    <div class="form_step_info mb-4 no-background no-padding no-border">
                        <div class="col-12 col-sm-12 header-row">
                            <h4>Payment Information</h4>
                        </div>
                        <div class="col-12 col-sm-12 info-section south-west-border border-right vehicle-table">
                            <div class="row">
                                <div class="col-7">Payment Amount:</div>
                                <div class="col-5 text-right">$@entity_coc@</div>
                            </div>
                            <div class="row">
                                <?php
                                    $Balance_Paid_By = "";
                                    if(in_array($this->entity->balance_paid_by, array(2, 3 , 16 , 17)))
                                        $Balance_Paid_By = "<b>COD</b>";
                                    
                                    if(in_array($this->entity->balance_paid_by, array(8, 9 , 18 , 19)))
                                        $Balance_Paid_By = "<b>COP</b>";
                                    
                                    if(in_array($this->entity->balance_paid_by, array(12, 13 , 20 , 21,24)))
                                        $Balance_Paid_By = "<b>Billing</b>";
                                    
                                    if(in_array($this->entity->balance_paid_by, array(14, 15 , 22 , 23)))
                                        $Balance_Paid_By = "<b>Billing</b>";
                                ?>
                                <div class="col-7">Balance Paid By:</div>
                                <div class="col-5 text-right"><?= $Balance_Paid_By; ?></div>
                            </div>
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

                    <button onclick="$eSign.signDispatch(this, '@sheet_id@')" class="btn btn-success custm-btn eSignNow mobile accept_reject">Accept</button>
                    <button onclick="$eSign.signDispatchReject(this, '@sheet_id@')" class="btn btn-danger custm-btn eSignNow mobile accept_reject red-btn">Reject</button>
                    
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
                    <div class="sign_name">
                        <label for="sign_name">Print Your Name:</label>
                        <input type="text" onkeyup="$eSign.typeName(this)" class="form-control" id="signature_mobile">
                    </div>
                    <div class="sign_preview">
                        <label for="sign_name">Leave us a Note:</label>
                        <textarea class="form-control" id="notes-mobile" row="5"></textarea>
                    </div>
                    <div class="accept_info mb-4">
                        <br/>
                        <label class="checkbox_main">I have read, and understand, the attached Terms and Conditions and I intend, and agree, to be bound by them.
                            <input type="checkbox" id="esign-terms-mobile" name="esign-terms">
                            <span class="checkmark"></span>
                        </label>
                    </div>
                    <button onclick="$eSign.signDispatchMobile(this, '@sheet_id@')" class="btn btn-primary eSignNow">Accept</button>
                    <button onclick="$eSign.signDispatchReject(this, '@sheet_id@')" class="btn btn-danger eSignNow">Reject</button>
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