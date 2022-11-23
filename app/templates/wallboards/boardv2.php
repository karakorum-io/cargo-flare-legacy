<?php
    $data = $this->daffny->tpl->data;

    $id = explode("/",$_GET['url']);
    $ID =$id[3];

    $arranged = $data;

    $jhat = [];
    foreach ($arranged as $key => $value) {

        $sunday = 0;
        $monday = 0;
        $tuesday = 0;
        $wednesday = 0;
        $thursday = 0;
        $friday = 0;
        $satrday = 0;

        foreach ($value as $k => $v) {
            $ts = strtotime($v['created_at']);
            $dow = date('w', $ts);
            
            if($dow == 1){
                $jhat[$key]['sunday'] = $sunday + 1;
            }
            
            if($dow == 2){
                $monday = $monday + 1;
                $jhat[$key]['monday'] = $monday;
            }

            if($dow == 3){
                $jhat[$key]['tuesday'] = $tuesday + 1;
            }

            if($dow == 4){
                $jhat[$key]['wednesday'] = $wednesday + 1;
            }

            if($dow == 5){
                $jhat[$key]['thursday'] = $thursday + 1;
            }

            if($dow == 6){
                $jhat[$key]['friday'] = $friday + 1;
            }

            if($dow == 7){
                $jhat[$key]['satrday'] = $satrday + 1;
            }
        }
    }

    print_r($jhat);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en" xml:lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Wallboard</title>
        <link rel="shortcut icon" href="<?php echo SITE_IN; ?>styles/favicon.ico" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.css" rel="stylesheet"/>
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/e-sign-thank-you.css"/>
        <script type="text/javascript">var BASE_PATH = '<?=SITE_PATH?>';</script>
        <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/export.js"></script>
        <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/wallboard.js"></script>
    </head>
    <body>
        <div class="row header">
            <div class="col-12 col-sm-12 text-center">
                <img alt="Cargo Flare" src="<?=SITE_PATH?>styles/cargo_flare/logo.png" width="180">
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 text-center middle-div">
                <h1 style="color:#f1752d">Sales <?php echo $data['title'];?></h1>
                <input type='hidden' value='<?php echo $ID;?>' id='hash'>
                <p>Last Updated : <span id="last-udpated">X</span> seconds ago</p>
                <p>Week date range: <?php echo $this->daffny->tpl->fromDate;?> to <?php echo $this->daffny->tpl->toDate;?></p>
                <input type="button" id="export-table" class ="export_button btn btn-primary" value="Export Table" />
                <br/><br/>
                <table id="detail-table" class="table table-bordered table-striped">
                    <thead>
                        <tr class="grid-head">
                            <td>Agent</td>
                            <td>Monday</td>
                            <td>Tuesday</td>
                            <td>Wednesday</td>
                            <td>Thursday</td>
                            <td>Friday</td>
                            <td>Saturday</td>
                            <td>Sunday</td>
                            <td>Total</td>
                        </tr>
                    </thead>
                    <tbody id='wallboard-table-body'>
                       <?php

                            foreach ($jhat as $key => $value) {
                                $q = "SELECT contactname FROM members WHERE id = ".$key;
                                $res = $this->daffny->DB->hardQuery($q);

                                $name = "";
                                while($r = mysqli_fetch_assoc($res)){
                                    $name = $r;
                                }

                                echo "<tr>";
                                echo "<td>".$name['contactname']."</td>";
                                
                                if($value['monday']){
                                    echo "<td style='text-align:right;'>".$value['monday']."</td>";
                                } else {
                                    echo "<td style='background:#F3CCC4;text-align:right;'>0</td>";
                                }

                                if($value['tuesday']){
                                    echo "<td style='text-align:right;'>".$value['tuesday']."</td>";
                                } else {
                                    echo "<td style='background:#F3CCC4;text-align:right;'>0</td>";
                                }

                                if($value['wednesday']){
                                    echo "<td style='text-align:right;'>".$value['wednesday']."</td>";
                                } else {
                                    echo "<td style='background:#F3CCC4;text-align:right;'>0</td>";
                                }

                                if($value['thursday']){
                                    echo "<td style='text-align:right;'>".$value['thursday']."</td>";
                                } else {
                                    echo "<td style='background:#F3CCC4;text-align:right;'>0</td>";
                                }

                                if($value['friday']){
                                    echo "<td style='text-align:right;'>".$value['friday']."</td>";
                                } else {
                                    echo "<td style='background:#F3CCC4;text-align:right;'>0</td>";
                                }

                                if($value['satrday']){
                                    echo "<td style='text-align:right;'>".$value['satrday']."</td>";
                                } else {
                                    echo "<td style='background:#F3CCC4;text-align:right;'>0</td>";
                                }

                                if($value['sunday']){
                                    echo "<td style='text-align:right;'>".$value['sunday']."</td>";
                                } else {
                                    echo "<td style='background:#F3CCC4;text-align:right;'>0</td>";
                                }
                                

                                echo "</tr>";
                            }
                       ?>
                    </tbody>
                </table>
                <script>
                   
                </script>
            </div>
        </div>
    </body>
</html>

