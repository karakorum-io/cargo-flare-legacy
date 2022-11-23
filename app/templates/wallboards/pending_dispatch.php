<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en" xml:lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Pending Dispatches</title>
        <link rel="shortcut icon" href="<?php echo SITE_IN; ?>styles/favicon.ico" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.css" rel="stylesheet"/>
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/e-sign-thank-you.css"/>

        <script type="text/javascript">var BASE_PATH = '<?=SITE_PATH?>';</script>
        <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/export.js"></script>
    </head>
    <body>
        <div class="row header">
            <div class="col-12 col-sm-12 text-center">
                <img alt="Cargo Flare" src="<?=SITE_PATH?>styles/cargo_flare/logo.png" width="180">
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 text-center middle-div">
                <h1 style="color:#f1752d">Pending Dispatches</h1>
                <p>Listing is showing pending dispatched in descending order of time</p>
                <br/><br/>
                <table id="detail-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <td>#ID</td>
                            <td>Creator</td>
                            <td>Comment</td>
                            <td>Carrier Name</td>
                            <td>Contact</td>
                            <td>Email</td>
                            <td>Phone</td>
                            <td>Time Passed</td>
                        </tr>
                    </thead>
                    <tbody id="dispatches">
                    <?php
                        foreach ($this->daffny->data as $k => $v) {
                    ?>
                    <tr>
                        <td align="center"><?php echo $v['order_id']; ?></td>
                        <td align="left"><?php echo $v['creator_name']; ?></td>
                        <td align="left"><?php echo $v['comment']; ?></td>
                        <td align="left"><?php echo $v['carrier_name']; ?></td>
                        <td align="left"><?php echo $v['carrier_contact']; ?></td>
                        <td align="center"><?php echo $v['carrier_email']; ?></td>
                        <td align="center"><?php echo format_phone_us($v['carrier_phone']); ?></td>
                        <td align="right" class="timeCreated" id="timeCreated-<?php echo $k; ?>"><?php echo $v['created_at']; ?></td>
                    </tr>
                    <?php
                        }
                    ?>
                    </tbody>
                </table>
               
            </div>
        </div>
        <script>
            setInterval(function(){

                let html = "";

                $.ajax({
                    type: 'POST',
                    url: BASE_PATH+'wallboards/udpate_pending_dispatch',
                    dataType: 'json',
                    data: {
                        'parent':'<?php echo $this->daffny->parent;?>'
                    },
                    success: function(response) {
                        for(var i=0; i<response.data.length; i++){

                            if(response.data[i]['creator_name'] == null){
                                response.data[i]['creator_name'] = "";
                            }

                            html += `
                                <tr>
                                    <td align="center">`+response.data[i]['order_id']+`</td>
                                    <td align="left">`+response.data[i]['creator_name']+`</td>
                                    <td align="left">`+response.data[i]['comment']+`</td>
                                    <td align="left">`+response.data[i]['carrier_name']+`</td>
                                    <td align="center">`+response.data[i]['carrier_contact']+`</td>
                                    <td align="left">`+response.data[i]['carrier_email']+`</td>
                                    <td align="left">`+response.data[i]['carrier_phone']+`</td>
                                    <td align="right" class="timeCreated" id="timeCreated-`+response.data[i]+`">`+response.data[i]['created_at']+`</td>
                                </tr>
                            `;
                            $("#dispatches").html(html);
                        }

                        if(response.data.length == 0){
                            $("#dispatches").html("<tr><td colspan='8'>No Pending Dispatches</td></tr>");
                        }
                    },
                    error: function(response) {
                        //alert("Try again later");
                    },
                    complete: function(response) {
                        //
                    }
                });
            }, 1000);
        </script>

        <?php
            function format_phone_us($phone)
            {
                // note: making sure we have something
                if (!isset($phone{3})) {return '';}
                // note: strip out everything but numbers
                $phone = preg_replace("/[^0-9]/", "", $phone);
                $length = strlen($phone);
                switch ($length) {
                    case 7:
                        return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $phone);
                        break;
                    case 10:
                        return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $phone);
                        break;
                    case 11:
                        return preg_replace("/([0-9]{1})([0-9]{3})([0-9]{3})([0-9]{4})/", "$1($2) $3-$4", $phone);
                        break;
                    default:
                        return $phone;
                        break;
                }
            }
        ?>
    </body>
</html>
