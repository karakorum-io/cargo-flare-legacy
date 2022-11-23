<?php
/*
 * This file runs directly from the URL & login is required to access this file functionality
 *  get accessed
 * 
 * @author                          Chetu Inc.
 * @lastupdatedDate       27122017
 */

require_once 'config.php';

session_start();
$connection = mysqli_connect($CONF['my_host'], $CONF['my_user'], $CONF['my_pass'], $CONF['my_base']);

if (count($_SESSION['member']) <= 0) {
    header('Location: user/signin');
} else {

    /*
     * accessing ajax request and preparing ajax resoinse
     */

    if (isset($_POST['action']) && $_POST['action']=="findDuplicates") {

        $query = "SELECT  `id`,`shipper_type`,`email`,`referred_by`,`city`,`state`,`zip_code`,`company_name`,`first_name`,`last_name`,`phone1`,`location_type`, COUNT(*) `existance` FROM `app_accounts` WHERE `" . $_POST['is_account'] . "` =1 AND owner_id IN (SELECT id FROM `members` WHERE `parent_id` = " . $_SESSION['member']['parent_id'] . ") GROUP BY " . $_POST['groupBy'] . " HAVING `existance` > 1 ORDER BY `existance` DESC";
        $result = mysqli_query($connection, $query);
        
        $data = array();
        $i = 0;
        while ($rows = mysqli_fetch_assoc($result)) {
            $data[$i] = $rows;
            $i++;
        }        
        echo json_encode(array('message' => 'Found ' . $result->num_rows . ' matching records','groupBy'=>$_POST['groupBy'],'is_account'=>$_POST['is_account'], 'data' => $data));
        die;
    }
    
    if(isset($_POST['action']) && $_POST['action']=="detailListing"){
        $query = "SELECT `id`,".$_POST['field']." as `duplicate` FROM app_accounts WHERE ".$_POST['field']." = '".$_POST['duplicateValue']."' AND ".$_POST['is_account']."=1  AND owner_id IN (SELECT id FROM `members` WHERE `parent_id` = " . $_SESSION['member']['parent_id'] . ") ";
        $result = mysqli_query($connection, $query);
        
        $data = array();
        $i=0;
        while($rows = mysqli_fetch_assoc($result)){
            $data[$i] = $rows;
            $i++;
        }
        echo json_encode(array('data' => $data));
        die;
    }
    
    ?>
    <!--HTML DESIGNING-->
    <!DOCTYPE html>
    <html lang="en">
        <head>
            <title>Duplicate Accounts</title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
            <style>
                /* Custom Styling for this page specifically */
                #header{
                    padding-top: 20px;
                    padding-bottom: 30px;
                    background: #fff url(../images/bg.gif) repeat-x;
                }
                .btn-primary{
                    border-radius:0px;
                    background: #008ec2;
                    border: none;
                }
                #filter{
                    padding-top:20px;
                    padding-bottom:15px;
                    margin-bottom: 10px;
                    border:1px solid #ccc;
                }
                .form-control{
                    border-radius:0px;
                }
                #dataTable{
                    font-size: 12px;
                }
                #loader{
                    width:100%;
                    height:100%;
                    position:fixed;
                    background: #fff;
                    z-index:1;
                    display: none;
                }
                .model-layout{
                    width: 600px;
                    margin:  auto;
                    font-size:11px;
                    border-radius:0px;
                }
                #detailModelTable th{
                    text-align: center;
                }
                .modal-body{
                    max-height: 450px;
                    overflow-x: auto;
                }
            </style>
            <script>
                function detailDuplicateListing(i){
                    var duplicate = $("#dup"+i).val();
                    var is_account = $("#acc"+i).val();
                    var field = $("#field"+i).val();
                    
                    if(field == 1){
                        field = "CONCAT(`first_name`,' ',`last_name`)";
                    }
                    
                    $("#duplicateListHeading").html("Duplicate listing for "+duplicate);
                    
                    $.ajax({
                            url: '/duplicateCarrierShippers.php',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                action: 'detailListing',
                                duplicateValue: duplicate,
                                is_account: is_account,
                                field:field
                            },
                            success: function (response) {
                                var html="";
                                var sno=1;
                                for(var i=0; i<response.data.length; i++){
                                    
                                    html +="<tr>\n\
                                                        <td align='center'>"+sno+"</td>\n\
                                                        <td align='center'><a target='_blank' href='application/accounts/details/id/"+response.data[i].id+"'>"+response.data[i].id+"</a></td>\n\
                                                        <td>"+response.data[i].duplicate+"</td>\n\
                                    </tr>";
                                    sno++;
                                }
                                $("#dList").html("");
                                $("#dList").html(html);
                            }
                    });
                }
            </script>
        </head>
        <body>

            <div id="loader" class="text-center">
                <img src="https://cdn.dribbble.com/users/24711/screenshots/2713076/bumpy_loader_2x.gif">
                <h2 class="text-center">Loading, please wait...</h2>
            </div>

            <div id="header" class="container-fluid text-center">
                <img src="/images/logo_cp.png" alt="Freight Dragon™" width="210" height="75">
            </div>
            <div id="filter" class="container-fluid text-center">                
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-2">
                    <label for="accountType"><h4>Search duplicate in</h4></lable>
                </div>
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-2">
                    <select id="accountType" name="accountType" class="form-control">
                        <option value="0">--SELECT--</option>
                        <option value="1">Shipper</option>
                        <option value="2">Carrier</option>
                        <option value="3">Location</option>
                    </select>
                </div>
                <div class="col-lg-1 col-sm-1 col-md-1 col-xs-1 text-center">
                    <label for="accountType"><h4>for</h4></lable>
                </div>
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-2">
                    <select id="duplicateField" name="accountType" class="form-control">
                        <option value="0">--SELECT--</option>
                        <option value="1">First Name</option>
                        <option value="2">Last Name</option>
                        <option value="3">First & Last Name</option>
                        <option value="4">Company Name</option>
                        <option value="5">Shipper Type</option>
                        <option value="6">Email</option>
                        <option value="7">Phone</option>
                        <option value="8">Source</option>
                        <option value="9">City</option>
                        <option value="10">State</option>
                        <option value="11">Zip</option>
                        <option value="12">Location Type</option>
                    </select>
                </div>
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-2">
                    <label for="accountType"><h4>and Parent/Owner Id <?= $_SESSION['member']['parent_id']; ?></h4></lable>
                </div>
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-2">
                    <button id="search" class="btn btn-primary"><span class="glyphicon glyphicon-search"> </span> Find Duplicates </button>
                </div>
                <div class="col-lg-1 col-sm-1 col-md-1 col-xs-1">
                    <button id="print" class="btn btn-primary"><span class="glyphicon glyphicon-print"> </span> Print </button>
                </div>
            </div>
            <div class="container-fluid">
                <span id="message">&nbsp;</span>
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>SNo</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Company</th>
                            <th>Ship. Type</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Source</th>
                            <th>Loc. Type</th>
                            <th>Occurance</th>
                        </tr>
                    </thead>
                    <tbody id="dataRows">
                        <tr><td colspan="13" align="center">No record found</td></tr>
                    </tbody>
                </table>
            </div>
            <div id="modalRegister" class="modal fade" role="dialog">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content model-layout" id="detailModelTable">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title" id="duplicateListHeading">Duplicate listing for "abc@email.com"</h4>
                        </div>
                        <div class="modal-body">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>SNo</th>
                                        <th>Id</th>
                                        <th>Duplicate Field</th>
                                    </tr>
                                </thead>
                                <tbody id="dList">
                                    <tr><td colspan="3" align="center">Loading...</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Back</button>
                        </div>
                    </div>
                </div>
            </div>
        </body>
    </html>
    <!--HTML DESIGNING OVER-->
    <script>
        $(document).ready(function () {
            $("#search").click(function () {
                //$("#loader").show();
                if ($("#accountType").val() != 0) {

                    if ($("#duplicateField").val() != 0) {

                        /* making query variables */
                        if ($("#accountType").val() == 1) {
                            var is_account = "is_shipper";
                        }

                        if ($("#accountType").val() == 2) {
                            var is_account = "is_carrier";
                        }

                        if ($("#accountType").val() == 3) {
                            var is_account = "is_location";
                        }

                        if ($("#duplicateField").val() == 1) {
                            var groupByField = "first_name";
                        }

                        if ($("#duplicateField").val() == 2) {
                            var groupByField = "last_name";
                        }

                        if ($("#duplicateField").val() == 3) {
                            var groupByField = "CONCAT(`first_name`, '' ,`last_name`)";
                        }

                        if ($("#duplicateField").val() == 4) {
                            var groupByField = "company_name";
                        }

                        if ($("#duplicateField").val() == 5) {
                            var groupByField = "shipper_type";
                        }

                        if ($("#duplicateField").val() == 6) {
                            var groupByField = "email";
                        }

                        if ($("#duplicateField").val() == 7) {
                            var groupByField = "phone1";
                        }

                        if ($("#duplicateField").val() == 8) {
                            var groupByField = "referred_by";
                        }

                        if ($("#duplicateField").val() == 9) {
                            var groupByField = "city";
                        }

                        if ($("#duplicateField").val() == 10) {
                            var groupByField = "state";
                        }

                        if ($("#duplicateField").val() == 11) {
                            var groupByField = "zip_code";
                        }

                        if ($("#duplicateField").val() == 12) {
                            var groupByField = "location_type";
                        }

                        $.ajax({
                            url: '/duplicateCarrierShippers.php',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                action: 'findDuplicates',
                                groupBy: groupByField,
                                is_account: is_account
                            },
                            success: function (response) {                                
                                var html = "";
                               $("#message").html(response.message);
                                var sno = 1;
                                for (var i = 0; i < response.data.length; i++) {
                                    var locationType = "";
                                    var findBy="";
                                    var group = response.groupBy; 
                                    
                                    if(response.groupBy == "CONCAT(`first_name`, '' ,`last_name`)"){
                                            findBy = response.data[i].first_name+" "+ response.data[i].last_name;
                                            group = 1;
                                        } else if(response.groupBy == "first_name"){
                                            findBy = response.data[i].first_name
                                        }else if(response.groupBy == "last_name"){
                                            findBy = response.data[i].last_name
                                        }else if(response.groupBy == "company_name"){
                                            findBy = response.data[i].company_name
                                        }else if(response.groupBy == "shipper_type"){
                                            findBy = response.data[i].shipper_type
                                        }else if(response.groupBy == "phone1"){
                                            findBy = response.data[i].phone1
                                        }else if(response.groupBy == "email"){
                                            findBy = response.data[i].email
                                        }else if(response.groupBy == "referred_by"){
                                            findBy = response.data[i].referred_by
                                        }else if(response.groupBy == "city"){
                                            findBy = response.data[i].city
                                        }else if(response.groupBy == "state"){
                                            findBy = response.data[i].state
                                        }else if(response.groupBy == "zip_code"){
                                            findBy = response.data[i].zip_code
                                        }else if(response.groupBy == "location_type"){
                                            findBy = response.data[i].location_type
                                        }else{
                                            findBy = "Invalid Duplicate Field";
                                        }
                                                                               
                                    html += "<tr><td>" +sno+ "</td>\n\
                                    <td>" + response.data[i].first_name + "</td>\n\
                                    <td>" + response.data[i].last_name + "</td>\n\
                                    <td>" + response.data[i].company_name + "</td>\n\
                                    <td>" + response.data[i].shipper_type + "</td>\n\
                                    <td>" + response.data[i].email + "</td>\n\
                                    <td>" + response.data[i].phone1 + "</td>\n\
                                    <td>" + response.data[i].referred_by + "</td>\n\
                                    <td>" + response.data[i].location_type + "</td>\n\
                                    <td>\n\
                                            <input type='hidden' value='"+findBy+"' id='dup"+i+"'>\n\
                                            <input type='hidden' value='"+group+"' id='field"+i+"'>\n\
                                            <input type='hidden' value='"+response.is_account+"' id='acc"+i+"'>\n\
                                            <a href='#' onclick='detailDuplicateListing("+i+")' data-toggle='modal' data-target='#modalRegister'> View (" + response.data[i].existance + ")</a>\n\
                                    </td></tr>";
                                    sno++;
                                }
                                $("#dataRows").html(html);
                            }
                        });

                    } else {
                        alert("Select fiels for which duplicate is to be checked");
                        $("#loader").hide();
                    }

                } else {
                    alert("Select Account type");
                    $("#loader").hide();
                }
            });

            $("#print").click(function () {
                window.print();
            });
        });
    </script>
    <?php
}

