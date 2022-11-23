<?php
    if (
        strpos($_SERVER['REDIRECT_SCRIPT_URI'], "user/signin") == true || 
        strpos($_SERVER['REDIRECT_SCRIPT_URI'], "user/forgot-password") == true
    ) {
?>
    <title>@title@</title>
    @content@
<?php
    } else {
?>
    <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title>@title@</title>
            <link rel="stylesheet" href="<?php echo SITE_IN; ?>styles/styles.css" type="text/css" />
            <link rel="stylesheet" href="<?php echo SITE_IN; ?>styles/default.css" type="text/css" />
            <link rel="stylesheet" href="<?php echo SITE_IN; ?>styles/jquery-ui.css" type="text/css" />
            <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,900" rel="stylesheet">

            <link rel="shortcut icon" href="<?php echo SITE_IN ?>styles/favicon.ico" />

            <!--  <link rel="stylesheet" href="<?php echo SITE_IN; ?>styles/menu_style.css" type="text/css" /> -->
            <link href="<?php echo SITE_IN; ?>styles/BreadCrumb.css" rel="stylesheet" type="text/css" />

            <!--------- metronic_v6.0.1    --->
            <link href="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" type="text/css" />
            <!--begin:: Global Optional Vendors -->
            <link href="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/tether/dist/css/tether.css" rel="stylesheet" type="text/css" />
            <link href="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css" rel="stylesheet" type="text/css" />

            <link href="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/owl.carousel/dist/assets/owl.theme.default.css" rel="stylesheet" type="text/css" />
            <link href="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/dropzone/dist/dropzone.css" rel="stylesheet" type="text/css" />
            <link href="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/summernote/dist/summernote.css" rel="stylesheet" type="text/css" />
            <link href="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/bootstrap-markdown/css/bootstrap-markdown.min.css" rel="stylesheet" type="text/css" />
            <link href="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/animate.css/animate.css" rel="stylesheet" type="text/css" />
            <link href="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/toastr/build/toastr.css" rel="stylesheet" type="text/css" />
            <link href="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/morris.js/morris.css" rel="stylesheet" type="text/css" />
            <link href="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/sweetalert2/dist/sweetalert2.css" rel="stylesheet" type="text/css" />
            <link href="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/socicon/css/socicon.css" rel="stylesheet" type="text/css" />
            <link href="<?php echo SITE_IN; ?>styles/new/assets/vendors/custom/vendors/line-awesome/css/line-awesome.css" rel="stylesheet" type="text/css" />
            <link href="<?php echo SITE_IN; ?>styles/new/assets/vendors/custom/vendors/flaticon/flaticon.css" rel="stylesheet" type="text/css" />
            <link href="<?php echo SITE_IN; ?>styles/new/assets/vendors/custom/vendors/flaticon2/flaticon.css" rel="stylesheet" type="text/css" />
            <link href="<?php echo SITE_IN; ?>styles/new/assets/vendors/custom/vendors/fontawesome5/css/all.min.css" rel="stylesheet" type="text/css" />
            <!--------- metronic_v6.0.1    --->
            <link href="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" type="text/css" />

            <link href="<?php echo SITE_IN; ?>styles/new_custom_.css" rel="stylesheet" type="text/css" />

            <!--begin::Global Theme Styles(used by all pages) -->
            <link href="<?php echo SITE_IN; ?>styles/new/assets/demo/default/base/style.bundle.css" rel="stylesheet" type="text/css" />
            <link href="<?php echo SITE_IN; ?>styles/new/assets/demo/default/skins/header/base/light.css" rel="stylesheet" type="text/css" />
            <link href="<?php echo SITE_IN; ?>styles/new/assets/demo/default/skins/header/menu/light.css" rel="stylesheet" type="text/css" />
            <link href="<?php echo SITE_IN; ?>styles/new/assets/demo/default/skins/brand/dark.css" rel="stylesheet" type="text/css" />
            <link href="<?php echo SITE_IN; ?>styles/new/assets/demo/default/skins/aside/dark.css" rel="stylesheet" type="text/css" />

            <style type="text/css">
                div#kt_header_menu_wrapper
                {
                    width: 100%;
                }
            </style>

            <script type="text/javascript">
                var BASE_PATH = '<?php echo SITE_IN; ?>';
            </script>
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
            <script type="text/javascript" src="<?php echo SITE_IN; ?>jscripts/jquery.scrollTo.js"></script>
            <script type="text/javascript" src="<?php echo SITE_IN; ?>jscripts/jquery.maskMoney.js"></script>
            <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.cookie.js"></script>
            <script type="text/javascript" src="<?php echo SITE_IN; ?>jscripts/jquery.maskedinput-1.3.min.js"></script>
            <script type="text/javascript" src="<?php echo SITE_IN; ?>jscripts/app.js"></script>
            <script type="text/javascript" src="<?php echo SITE_IN; ?>jscripts/jquery.jBreadCrumb.1.1.js"></script>

            <!--add metronic_v6.0.1 js  -->
            <!--begin:: Global Mandatory Vendors -->
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/jquery/dist/jquery.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/popper.js/dist/umd/popper.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/js-cookie/src/js.cookie.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/moment/min/moment.min.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/tooltip.js/dist/umd/tooltip.min.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/perfect-scrollbar/dist/perfect-scrollbar.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/sticky-js/dist/sticky.min.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/wnumb/wNumb.js" type="text/javascript"></script>

            <!--end:: Global Mandatory Vendors -->

            <!--begin:: Global Optional Vendors -->
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/jquery-form/dist/jquery.form.min.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/block-ui/jquery.blockUI.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/custom/components/vendors/bootstrap-datepicker/init.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/bootstrap-datetime-picker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/custom/components/vendors/bootstrap-timepicker/init.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/bootstrap-daterangepicker/daterangepicker.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/bootstrap-maxlength/src/bootstrap-maxlength.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/custom/vendors/bootstrap-multiselectsplitter/bootstrap-multiselectsplitter.min.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/bootstrap-select/dist/js/bootstrap-select.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/bootstrap-switch/dist/js/bootstrap-switch.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/custom/components/vendors/bootstrap-switch/init.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/select2/dist/js/select2.full.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/ion-rangeslider/js/ion.rangeSlider.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/typeahead.js/dist/typeahead.bundle.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/handlebars/dist/handlebars.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/inputmask/dist/jquery.inputmask.bundle.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>new/assets/vendors/general/inputmask/dist/inputmask/inputmask.date.extensions.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/inputmask/dist/inputmask/inputmask.numeric.extensions.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/nouislider/distribute/nouislider.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/owl.carousel/dist/owl.carousel.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/autosize/dist/autosize.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/clipboard/dist/clipboard.min.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/dropzone/dist/dropzone.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/summernote/dist/summernote.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/markdown/lib/markdown.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/bootstrap-markdown/js/bootstrap-markdown.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/custom/components/vendors/bootstrap-markdown/init.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/bootstrap-notify/bootstrap-notify.min.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/custom/components/vendors/bootstrap-notify/init.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/jquery-validation/dist/jquery.validate.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/jquery-validation/dist/additional-methods.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/custom/components/vendors/jquery-validation/init.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/toastr/build/toastr.min.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/raphael/raphael.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/morris.js/morris.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/chart.js/dist/Chart.bundle.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/custom/vendors/bootstrap-session-timeout/dist/bootstrap-session-timeout.min.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/custom/vendors/jquery-idletimer/idle-timer.min.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/waypoints/lib/jquery.waypoints.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/counterup/jquery.counterup.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/es6-promise-polyfill/promise.min.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/sweetalert2/dist/sweetalert2.min.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/custom/components/vendors/sweetalert2/init.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/jquery.repeater/src/lib.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/jquery.repeater/src/jquery.input.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/jquery.repeater/src/repeater.js" type="text/javascript"></script>
            <script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/dompurify/dist/purify.js" type="text/javascript"></script>

            <script>
                var KTAppOptions = {
                    "colors": {
                        "state": {
                            "brand": "#5d78ff",
                            "dark": "#282a3c",
                            "light": "#ffffff",
                            "primary": "#5867dd",
                            "success": "#34bfa3",
                            "info": "#36a3f7",
                            "warning": "#ffb822",
                            "danger": "#fd3995"
                        },
                        "base": {
                            "label": ["#c5cbe3", "#a1a8c3", "#3d4465", "#3e4466"],
                            "shape": ["#f0f3ff", "#d9dffa", "#afb4d4", "#646c9a"]
                        }
                    }
                };
            </script>

            <!--begin::Global App Bundle(used by all pages) -->
            <script src="<?php echo SITE_IN; ?>/new/assets/app/bundle/app.bundle.js" type="text/javascript"></script>
            <!--end metronic_v6.0.1 js  -->

            <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN; ?>styles/nivo-slider.css" />
            <!-- New Theme -->
            <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
            <script type="text/javascript" src="<?php echo SITE_IN; ?>jscripts/jquery.nivo.slider.pack.js"></script>

        </head>

        <body class="kt-header--fixed">
            <div class="clear">&nbsp;</div>
                <?php
        if (strpos($_SERVER['REQUEST_URI'], "B2BOrderTerms") == false) {
            ?>
                    <?php
        if (strpos($_SERVER['REQUEST_URI'], "acknoledgement") == false) {
                ?>
                    <?php
        /**
                 * Menu removal on wallboard detail page
                 */
                if (strpos($_SERVER['REQUEST_URI'], "wallboards") == false) {
                    ?>
                                @top_menu_block@

                                <?php
        }
                /**
                 * *************************************
                 */
                ?>


                        <?php }?>
                    <?php }?>
                    <div class="clear"></div>
                    <div class="content" style="background: #f9f9fc;">
                        @content@
                    </div>
                    <br /><br />
                <!-- </div> -->

                <?php
        if (strpos($_SERVER['REQUEST_URI'], "B2BOrderTerms") == false) {
            if (strpos($_SERVER['REQUEST_URI'], "acknoledgement") == false) {
                ?>
                        <?php
        /**
                 * Menu removal on wallboard detail page
                 */
                if (strpos($_SERVER['REQUEST_URI'], "wallboards") == false) {
                    ?>



                <!-- begin:: Footer -->
                <div class="kt-footer_new">
                    <div class="m-content">
                        <div class="row">
                            <div class="col-6 text-left">
                                <!-- <img width="150" src="<?php echo SITE_IN; ?>styles/default/logo/iphone.gif">
                                <img width="150" src="<?php echo SITE_IN; ?>styles/default/logo/on_android.gif"> -->
                            </div>

                            <div class="col-6 text-right">
                                <img src="<?php echo SITE_IN; ?>styles/default/logo/godaddy.gif">
                            </div>
                        </div>
                    </div>

                    <div style="background:#000;width:100%;" class="mt-4 pt-2 pb-2">
                        <div class="m-content">
                            <div class="row child_style_center">

                                <div class="col-3">
                                    <img src="<?php echo SITE_IN; ?>styles/default/logo/cards.gif" />
                                </div>

                                <div class="col-6 text-center">
                                    <p style="margin:0;padding:0;color:#fff !important;font-size:15px;">© Copyright CargoFlare.com  2011-2019. All Rights Reserved.</p>
                                </div>

                                <div class="col-3 text-right">
                                    <div class="">
                                        <a href="http://www.facebook.com/cargoflare.social" target="_blank"><img src="<?=SITE_IN?>images/icons/facebook.png" alt="Facebook" width="32" height="32" /></a>
                                        <a target="_blank" href="https://twitter.com/#!/cargo_flare"><img src="<?=SITE_IN?>images/icons/twitter.png" alt="Twitter" width="32" height="32" /></a>
                                        <a target="_blank" href="http://www.youtube.com/user/cargoflare"><img src="<?=SITE_IN?>images/icons/youtube.png" alt="You Tube" width="32" height="32" /></a>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
        </div>

        </div>
        <script>
        (function (i, s, o, g, r, a, m) {
        i['GoogleAnalyticsObject'] = r;
        i[r] = i[r] || function () {
        (i[r].q = i[r].q || []).push(arguments)
        }, i[r].l = 1 * new Date();
        a = s.createElement(o),
        m = s.getElementsByTagName(o)[0];
        a.async = 1;
        a.src = g;
        m.parentNode.insertBefore(a, m)
        })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

        ga('create', 'UA-47691867-1', 'freightdragon.com');
        ga('send', 'pageview');

        </script>
        <script type="text/javascript">
        var LHCChatOptions = {};
        LHCChatOptions.opt = {widget_height: 140, widget_width: 300, popup_height: 520, popup_width: 500};
        (function () {
        var po = document.createElement('script');
        po.type = 'text/javascript';
        po.async = true;
        var refferer = (document.referrer) ? encodeURIComponent(document.referrer) : '';
        var location = (document.location) ? encodeURIComponent(document.location) : '';
        po.src = '//cargoflare.com/livehelperchat/lhc_web/index.php/chat/getstatus/(click)/internal/(position)/bottom_right/(hide_offline)/true/(check_operator_messages)/true/(top)/350/(units)/pixels?r=' + refferer + '&l=' + location;
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(po, s);
        })();
        </script>

        <?php
        }
                /**
                 * *************************************
                 */
                ?>

                                                                <?php }?>
                                                            <?php }?>
                                                            </body>
    </html>
<?php } ?>