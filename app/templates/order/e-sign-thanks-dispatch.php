<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en" xml:lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>E sign Thank You</title>
        <link rel="shortcut icon" href="<?php echo SITE_IN; ?>styles/favicon.ico" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.css" rel="stylesheet"/>
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/e-sign-thank-you.css"/>

        <script type="text/javascript">var BASE_PATH = '<?=SITE_PATH?>';</script>
        <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.min.js"></script>
    </head>
    <body>
        <div class="row header">
            <div class="col-12 col-sm-12 text-center">
                <img alt="Cargo Flare" src="@company_logo@" width="180">
            </div>
        </div>
        <div class="row vertical-center">
            <div class="col-12 col-sm-12 text-center middle-div">
                <h1 class="text-success">Thank You!</h1>
                <p>we have received your signed dispatch sheet and you are all set!</p>
                <p class="max-para-width">E-Signature has been confirmed on <?php echo date('m-d-Y h:i A');?> with the IP address: <?php echo $_SERVER['REMOTE_ADDR'];?> </p>
                <p class="max-para-width"><strong>To download your signed document from <?php echo $this->companyname;?> please use the button below.</strong></p>
                <br/>
                <a target="_blank" href="<?php print SITE_IN."/external/getdocs.php?id=" . $_GET['id'];?>">
                    <button class="btn btn-primary action-btn">Download Now</button>
                </a>
                <br/>
                <br/>
                <p class="max-para-width red-border">Unable to download a copy at this time? Don't you worry!<br/>We have sent you a copy of the signed document to the email on file for your convenience.</p>
            </div>
        </div>
        <div class="row footer">
            <div class="col-12 col-sm-12 text-center">
                <p class="gray-font">Powered By</p>
                <img alt="Cargo Flare" src="<?=SITE_PATH?>styles/cargo_flare/logo.png" width="100">
            </div>
        </div>
    </body>
</html>
