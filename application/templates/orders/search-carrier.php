<div class="modal fade" id="searchCarrierModal" role="dialog" aria-labelledby="searchCarrierModal" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Carrier</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times" aria-hidden="true"></i></button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-12 col-sm-8">
                        <input type="text" class="form-control" id="searchCarrierField" placeholder="Search String ...">
                    </div>
                    <div class="col-12 col-sm-2">
                        <select class="form-control" id="carrierFilter">
                            <option value="companyname">Company Name</option>
                            <option value="mc_number">MC Number</option>
                            <option value="us_dot">Us Dot</option>
                            <option value="email">Email</option>
                            <option value="phone1">Phone 1</option>
                            <option value="phone2">Phone 2</option>
                            <option value="address1">Address</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-2 text-right">
                        <button class="btn btn-primary" id="searchCarrierButton" style="width:100%;" onclick="searchCarrierAccount(this)">Search</button>
                    </div>
                </div>
                <br/>
                <br/>
                <div class="container">
                    <ul class="nav nav-tabs nav-tabs-line nav-tabs-line-3x nav-tabs-line-success">
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#mycarrier">My Carrier (<myCarrier>0</myCarrier>) </a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#system">System Carrier (<systemCarrier>0</systemCarrier>)</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#others">Others (<otherCarrier>0</otherCarrier>)</a></li>
                        <li class="nav-item fsmca-tabs"><a class="nav-link" data-toggle="tab" href="#fsmca">FSMCA Carriers (<fsmcaCarrier>0</fsmcaCarrier>)</a></li>
                    </ul>

                    <div class="tab-content">
                        <div id="mycarrier" class="tab-pane fade in"> 
                            <div class="row">
                                <table id="myCarrierResultsTable" class="table table-bordered table-bordered carrier-tables"></table>
                            </div>
                        </div>
                        <div id="system" class="tab-pane fade">
                            <div class="row">
                                <table id="systemCarrierResultsTable" class="table table-bordered table-bordered carrier-tables"></table>
                            </div>
                        </div>
                        <div id="others" class="tab-pane fade">
                            <div class="row">
                                <table id="otherResultsTable" class="table table-bordered table-bordered carrier-tables"></table>
                            </div>
                        </div>
                        <div id="fsmca" class="tab-pane fade fsmca-tabs">
                            <div class="row">
                                <table id="fsmcaResultsTable" class="table table-bordered table-bordered carrier-tables"></table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" onclick="dispatchWithNewCarrier(<?php echo $_GET['id']?>)">New Carrier</button>
                <button type="button" class="btn btn-primary useCarrier" onclick="dispatchOrder(<?php echo $_GET['id']?>)">Use Carrier</button>
            </div>
        </div>
    </div>
</div>

<script>

    $(".fsmca-tabs").hide();

    $("#carrierFilter").change(()=>{
        if($("#carrierFilter").val() == "mc_number" ||  $("#carrierFilter").val() == "us_dot") {
            console.log("HERE")
            $(".fsmca-tabs").show();
        } else {
            $(".fsmca-tabs").hide();
        }
    });

    $("#searchCarrierField").keyup((event)=>{
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){
            searchCarrierAccount(document.getElementById('searchCarrierButton'));
        }
    });

    let searchCarrierWizard = () => {
        $(".useCarrier").hide();
        $("#searchCarrierModal").modal('show');
    }

    let searchCarrierAccount = (ref) => {
        let text = $("#searchCarrierField").val().trim();

        $("#myCarrierResults").html("");
        $("#systemCarrierResults").html("");
        $("#otherResults").html("");
        $(".carrier-tables").html("");

        $("myCarrier").html(0);
        $("systemCarrier").html(0);
        $("otherCarrier").html(0);
        $("fsmcaCarrier").html(0);

        if(text == ""){
            $engine.notify("Type something to search...");
            return false;
        }

        ref.innerHTML = "Database lookup...";
        $engine.asyncPost(BASE_PATH + "application/ajax/accounts.php", {
            action: "searchCarrier",
            text: text,
            filter: $("#carrierFilter").val()
        }, (response) => {
            if (response.success) {
                $(".useCarrier").show();
                
                fetchFSCMACarriers(ref, $("#carrierFilter").val(), text, response);

            } else {
                $engine.notify("Something went wrong");
            }
        });
    }

    let fetchFSCMACarriers = (ref, type, text, response) => {

        ref.innerHTML = "Search";

        if(response.data.myCarriers != undefined){
            if (response.data.myCarriers.length != 0) {
                renderResult(response.data.myCarriers,"myCarrierResultsTable");
                $("myCarrier").html(response.data.myCarriers.length);
            }
        }

        if(response.data.systemCarrier != undefined){
            if (response.data.systemCarrier.length != 0) {
                renderResult(response.data.systemCarrier,"systemCarrierResultsTable", true);
                $("systemCarrier").html(response.data.systemCarrier.length);
            }
        }

        if(response.data.otherCarriers != undefined){
            if (response.data.otherCarriers.length != 0) {
                renderResult(response.data.otherCarriers,"otherResultsTable");
                $("otherCarrier").html(response.data.otherCarriers.length);
            }
        }

        if(type == "mc_number"){
            ref.innerHTML = "API lookup...";
            $.ajax({
                type: "GET",
                url: "https://saferwebapi.com/v2/mcmx/snapshot/"+ text,
                dataType: "JSON",
                beforeSend: function(xhr){
                    xhr.setRequestHeader('x-api-key', '169ebdecadf6464f9aa24b49638877d4');
                },
                success: function(result) {
                    renderResultFSMCA(result, "fsmcaResultsTable", type);
                }
            });
        }

        if(type == "us_dot"){
            ref.innerHTML = "API lookup...";
            $.ajax({
                type: "GET",
                url: "https://saferwebapi.com/v2/usdot/snapshot/"+ text,
                dataType: "JSON",
                beforeSend: function(xhr){
                    xhr.setRequestHeader('x-api-key', '169ebdecadf6464f9aa24b49638877d4');
                },
                success: function(result) {
                    renderResultFSMCA(result, "fsmcaResultsTable", type, text);
                }
            });
        }
    }

    let renderResult = (data, id, isSystem=false) => {

        let html = `
            <thead>
                <th>#</th>
                <th>Company</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Email</th>
                <th>State</th>
                <th>MC-Num</th>
                <th>Insurance Type</th>
                <th>Insurance</th>
                <th>Expire</th>
            </thead>
            <tbody>
        `;

        data.forEach( item => {
            let bg = "";
            if(item.expired) {
                bg = "";
            }

            let doc = "";
            if(item.insurance_doc_id == 0) {
                doc = `<img src="${BASE_PATH}images/no_ins_doc.jpg" width="40" height="40">`;
            } else {
                doc = `
                    <a href="${BASE_PATH}/application/accounts/getdocs/id/${item.insurance_doc_id}/type/1" title="Expire Date: ${item.insurance_expirationdate}">
                        <img src="${BASE_PATH}images/ins_doc.png" width="40" height="40">
                    </a>
                `;
            }

            html += `
                <tr style="background:${bg}">
                    <td class="text-center">
                        <label class="kt-radio kt-radio--solid kt-radio--brand">
                            <input type="radio" name="acc_search_result_item" carrierType="${isSystem ? "member" : "account"}" class="form-box-radio" id="acc_search_result_item${item.id}" value="${item.id}"><span></span>
                        </label>
                        <br/>
                        <br/>
                        ${item.id ? item.id : ""}
                    </td>
                    <td>${item.company_name}</td>
                    <td>${item.address1}</td>
                    <td>${item.phone1}</td>
                    <td><a href="javascript:void(0);" alt="${item.email}" title="${item.email}">View</a></td>
                    <td>${item.state}</td>
                    <td>${item.insurance_iccmcnumber ? item.insurance_iccmcnumber : ""}</td>
                    <td>${item.insurance_type ? item.insurance_type : "--"}</td>
                    <td>${doc}</td>
                    <td>${item.insurance_expirationdate}</td>
                </tr>
            `;
        });

        html += `</tbody>`;
        $("#"+id).html(html);
    }

    let renderResultFSMCA = (item, id, type, usDot=null) => {

        let html = `
            <thead>
                <th>#</th>
                <th>Company</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Email</th>
                <th>State</th>
                <th>MC-Num</th>
                <th>Insurance Type</th>
                <th>Insurance</th>
                <th>Expire</th>
            </thead>
            <tbody>
        `;

        if(type == "companyname"){
            
            if(item.length > 0){
                
                item.forEach(el => {
                    html += `
                        <tr>
                            <td class="text-center">
                                <label class="kt-radio kt-radio--solid kt-radio--brand">
                                    <input type="radio" name="acc_search_result_item" carrierType="fsmca" class="form-box-radio"><span></span>
                                </label>
                                <br/>
                                <br/>
                            </td>
                            <td>${el.name}</td>
                            <td>${el.location.split(", ")[0]}</td>
                            <td>${el.usdot}</td>
                            <td><a href="javascript:void(0);" alt="${el.email ? el.email : ""}" title="${el.email ? el.email : ""}">View</a></td>
                            <td>${el.location.split(", ")[1]}</td>
                            <td>${el.mc_mx_ff_numbers ? el.mc_mx_ff_numbers : ""}</td>
                            <td>--</td>
                            <td>---</td>
                            <td>${el.mcs_150_form_date ? el.mcs_150_form_date : ""}</td>
                        </tr>
                    `;

                });

                $("fsmcaCarrier").html(item.length);
            }
        }

        if(type == "mc_number"){
            if(!item.message){

                let exploded = [];
                exploded = item.mailing_address.split(" ");
                let fullZip = exploded[exploded.length -1];
                let state = exploded[exploded.length -3];
                let zip = fullZip.split("-")[0];
                let address = item.mailing_address.split(",")[0];

                html += `
                    <tr>
                        <td class="text-center">
                            <label class="kt-radio kt-radio--solid kt-radio--brand">
                                <input type="radio" name="acc_search_result_item" mcNumber="${item.mc_mx_ff_numbers ? item.mc_mx_ff_numbers.split("-")[1] : "--"}" carrierType="fsmca" class="form-box-radio"><span></span>
                            </label>
                            <br/>
                            <br/>
                        </td>
                        <td>${item.legal_name}</td>
                        <td>${address}</td>
                        <td>${item.phone}</td>
                        <td><a href="javascript:void(0);" alt="${item.email}" title="${item.email}">View</a></td>
                        <td>${state}</td>
                        <td>${item.mc_mx_ff_numbers ? item.mc_mx_ff_numbers : ""}</td>
                        <td>--</td>
                        <td>--</td>
                        <td>${item.mcs_150_form_date}</td>
                    </tr>
                `;

                $("fsmcaCarrier").html(1);
            }
        }

        if(type == "us_dot"){
            if(!item.message){

                let exploded = [];
                exploded = item.mailing_address.split(" ");
                let fullZip = exploded[exploded.length -1];
                let state = exploded[exploded.length -3];
                let zip = fullZip.split("-")[0];
                let address = item.mailing_address.split(",")[0];

                html += `
                    <tr>
                        <td class="text-center">
                            <label class="kt-radio kt-radio--solid kt-radio--brand">
                                <input type="radio" name="acc_search_result_item" usDot="${usDot}" mcNumber="${item.mc_mx_ff_numbers ? item.mc_mx_ff_numbers.split("-")[1] : "--"}" carrierType="fsmca" class="form-box-radio"><span></span>
                            </label>
                            <br/>
                            <br/>
                        </td>
                        <td>${item.legal_name}</td>
                        <td>${address}</td>
                        <td>${item.phone}</td>
                        <td><a href="javascript:void(0);" alt="${item.email}" title="${item.email}">View</a></td>
                        <td>${state}</td>
                        <td>${item.mc_mx_ff_numbers ? item.mc_mx_ff_numbers : ""}</td>
                        <td>--</td>
                        <td>--</td>
                        <td>${item.mcs_150_form_date}</td>
                    </tr>
                `;

                $("fsmcaCarrier").html(1);
            }
        }

        html += `</tbody>`;

        $("#"+id).html(html);
        $("#searchCarrierButton").html("Search");
    }

    let dispatchOrder = (id) => {

        if ($("input[name='acc_search_result_item']:checked").length == 0) {
            $engine.notify("Select one carrier");
            return;
        }

        if( $("input[name='acc_search_result_item']:checked").attr("carrierType") == "member" ){
            dispatchWithSystemCarrier(id);
        }

        if( $("input[name='acc_search_result_item']:checked").attr("carrierType") == "account" ){
            dispatchWithExistingCarrier(id);
        }

        if( $("input[name='acc_search_result_item']:checked").attr("carrierType") == "fsmca" ){
            dispatchWithFMCACarrier(id);
        }
    }

    let dispatchWithNewCarrier = (id) => {
        location.href = BASE_PATH + "application/orders/dispatchnew/id/" + id;
    }

    let dispatchWithSystemCarrier = (orderId = null) => {
        
        $engine.asyncPost(BASE_PATH + "application/ajax/accounts.php", {
            action: "ValidateExistingCarrier",
            text: $("#searchCarrierField").val(),
        }, (response) => {
            if (response.success) {
                $engine.confirm("You already have this carrier in your account, use that instead?", action => {
                    if (action === "confirmed") {
                        location.href = BASE_PATH + "application/orders/dispatchnew/id/" + orderId + "/acc/" + response.account_id;
                    }
                });
            } else {
                let memberId = $("input[name='acc_search_result_item']:checked").val();

                if (orderId) {
                    let entityId = orderId;
                    location.href = BASE_PATH + "application/orders/dispatchnew/id/" + entityId + "/member/" + memberId;
                } else {
                    let entityId = $(".order-checkbox:checked").val();
                    location.href = BASE_PATH + "application/orders/dispatchnew/id/" + entityId + "/member/" + memberId;
                }
            }
        });
    }

    let dispatchWithExistingCarrier = (orderId = null) => {

        if ($("input[name='acc_search_result_item']:checked").length == 0) {
            $engine.notify("Select one carrier");
            return;
        }

        let accountId = $("input[name='acc_search_result_item']:checked").val();

        if (orderId) {
            let entityId = orderId;
            location.href = BASE_PATH + "application/orders/dispatchnew/id/" + entityId + "/acc/" + accountId;
        } else {
            let entityId = $(".order-checkbox:checked").val();
            location.href = BASE_PATH + "application/orders/dispatchnew/id/" + entityId + "/acc/" + accountId;
        }
    }

    let dispatchWithFMCACarrier = (orderId = null) => {

        if ($("input[name='acc_search_result_item']:checked").length == 0) {
            $engine.notify("Select one carrier");
            return;
        }

        let mcNumber = $("input[name='acc_search_result_item']:checked").attr("mcNumber");
        let usDot = $("input[name='acc_search_result_item']:checked").attr("usDot");

        $(".useCarrier").html("Checking Duplicates...");

        if(usDot){
            $engine.asyncPost(BASE_PATH + "application/ajax/accounts.php", {
                action: "ValidateExistingMemberAndCarrier",
                usDot: usDot,
                filter: "us_dot"
            }, (response) => {
                if (response.success) {
                    let entityId = "";

                    if (orderId) {
                        entityId = orderId;
                    } else {
                        entityId = $(".order-checkbox:checked").val();
                    }

                    let path = BASE_PATH + "application/orders/dispatchnew/id/" + entityId;

                    if(response.account_id) {
                        $engine.confirm("You already have this carrier in your account, use that instead?", action => {
                            if (action === "confirmed") {
                                location.href = BASE_PATH + "application/orders/dispatchnew/id/" + entityId + "/acc/" + response.account_id;
                            }
                        });
                    }

                    if(response.member_id) {
                        $engine.confirm("You already have this carrier in our system, use that instead?", action => {
                            if (action === "confirmed") {
                                location.href = BASE_PATH + "application/orders/dispatchnew/id/" + entityId + "/member/" + response.member_id;
                            }
                        });
                    }
                    
                    if (!response.account_id && !response.member_id) {
                        location.href = BASE_PATH + "application/orders/dispatchnew/id/" + entityId + "/dot/" + usDot;
                    }
                    
                } else {
                    $engine.notify("Something went wrong");
                }
                $(".useCarrier").html("Use Carrier");
            });
        } else {
            $engine.asyncPost(BASE_PATH + "application/ajax/accounts.php", {
                action: "ValidateExistingMemberAndCarrier",
                mcNumber: mcNumber,
                filter: "mc_number"
            }, (response) => {
                if (response.success) {
                    let entityId = "";

                    if (orderId) {
                        entityId = orderId;
                    } else {
                        entityId = $(".order-checkbox:checked").val();
                    }

                    let path = BASE_PATH + "application/orders/dispatchnew/id/" + entityId;

                    if(response.account_id) {
                        $engine.confirm("You already have this carrier in your account, use that instead?", action => {
                            if (action === "confirmed") {
                                location.href = BASE_PATH + "application/orders/dispatchnew/id/" + entityId + "/acc/" + response.account_id;
                            }
                        });
                    }

                    if(response.member_id) {
                        $engine.confirm("You already have this carrier in our system, use that instead?", action => {
                            if (action === "confirmed") {
                                location.href = BASE_PATH + "application/orders/dispatchnew/id/" + entityId + "/member/" + response.member_id;
                            }
                        });
                    }
                    
                    if (!response.account_id && !response.member_id) {
                        location.href = BASE_PATH + "application/orders/dispatchnew/id/" + entityId + "/fsmca/" + mcNumber;
                    }
                    
                } else {
                    $engine.notify("Something went wrong");
                }
                $(".useCarrier").html("Use Carrier");
            });
        }
    }
</script>