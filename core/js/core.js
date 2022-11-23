/**
 * Javascript file to perform several certain functionality, for the freightdragon 
 * web application such as ajax call and other functionality
 * 
 * @author Chetu Inc.
 * @version 1.0
 */

/**
 * Global Variables
 */
var AJAXURL = '../../../../core/ajax/handler.php';
var REQUSET_POST = 'POST';
var DATA_TYPE = 'JSON';
var ERRORS = true;
var ERROR_MESSAGE = "Mandatory fields are empty";

/**
 * Function to send the force popup email to the shipper
 * 
 * @author Chetu Inc.
 * @version 1.0
 */
function sendContractUpdateEmail() {
    var responseHTML;
    $.ajax({
        url: AJAXURL,
        type: REQUSET_POST,
        dataType: DATA_TYPE,
        data: {
            receiver: 'shahrukhk@chetu.com',
            action: 'sendContractUpdateEmail',
            mailBody: $("#contentInnerDiv").html(),
            emailSubject: $("#emailSubject").val()
        },
        success: function (data) {
            responseHTML = '<center>\n\
                                <br><br><br><br><br><br><br><br><br><br><br><br>\n\
                                <br><br><br><br>\n\
                                <h1>Mail Sent Successfully!</h1>,\n\
                                <h3>Let the contract to be update.</h3>\n\
                            </center>';
            $("#forcePopupContainer").html(responseHTML);
        }
    });
}

/**
 * Auto Quote imported leads from leads detail page under imported leads
 * 
 * @param entityId
 * @author Chetu Inc.
 * @returns Void
 */
function autoQuoteFromDetailLeads(entityId){
    console.log("D");
    var url = "https://cargoflare.com/application/ajax/accounts.php";        
    var data = {entity:entityId};
    var action = "autoAuoteFromDetailLeads";
    triggerCustomAjax(action,data,url,successOnAutoQuotingLeadsPage);
}

/**
 * Success function for leads auto quoting from detail imported leads page
 * 
 * @returns void
 */
function successOnAutoQuotingLeadsPage(){
    location.reload();
}

/**
 * Function to auto quote imported leads from leads listing
 * 
 * @author Chetu Inc.
 * @returns void
 */
function autoQuoteImportedLeads(){
    var entityId = [];
    var i = 0;
    $("#lead_check tbody [type='checkbox']:checked").each(function () {
        entityId[i] = $(this).val();
        i++;
    });
    if(entityId.length < 1 ){
            Swal.fire({
            type: 'error',
            title: 'Oops...',
            text: 'Please select atleast one Leads!'
            })
           
    } else {        
        var url = "https://cargoflare.com/application/ajax/accounts.php";        
        var data = {entity:entityId};
        var action = "getAutoQuotingParameters";
        triggerCustomAjax(action,data,url,setAutoQuotingLeadsImported);        
    }
}

/**
 * AutoAuotes function hitting autoQuotesAPI
 * 
 * @author Chetu Inc.
 * @returns Array Array contaning quotation price from the auto quote API
 */
function AutoQuoting() {
    var carrier = $("#shipping_ship_via").val();

    /**
     * makign carrier value API ready
     */
    if (carrier === "1") {
        carrier = "Open";
        ERRORS = false;
    } else if (carrier === "2") {
        carrier = "enclosed";
        ERRORS = false;
    } else if (carrier === "3") {
        carrier = "Driveaway";
        ERRORS = false;
    } else {
        alert("Select Carrier Type");
        ERRORS = true;
    }

    var Origin = {
        City: checkEmpty($("#origin_city").val()),
        State: checkEmpty($("#origin_state").val()),
        Zipcode: checkEmpty($("#origin_zip").val())
    };

    var Destination = {
        City: checkEmpty($("#destination_city").val()),
        State: checkEmpty($("#destination_state").val()),
        Zipcode: checkEmpty($("#destination_zip").val())
    };   
    
    var year = [];
    var i = 0;
    $("input[name='year[]']").each(function () {
        year[i] = $(this).val();
        i++;
    });
    
    var make = [];
    var i = 0;
    $("input[name='make[]']").each(function () {
        make[i] = $(this).val();
        i++;
    });
    
    var model = [];
    var i = 0;
    $("input[name='model[]']").each(function () {
        model[i] = $(this).val();
        i++;
    });
    
    
    var Vehicles = {
        Year:year,
        Make:make,
        Model:model
    };
    
    var data = {
        Transport:{
            Carrier: carrier,
            Origin: Origin,
            Destination: Destination,
            Vehicles:Vehicles
        },
        Additional:{
            order_deposit: $("#order_deposit").val(),
            order_deposit_type: $("#order_deposit_type").val(),
            auto_quote_api_pin: $("#auto_quote_api_pin").val(),
            auto_quote_api_key: $("#auto_quote_api_key").val()
        }
    };
    
    /**
     * When no validation errors than Trigger AJAX
     */
    if (ERRORS === false) {
        triggerAjax('requestAutoQuotes',data,1);        
    } else {
        alert("Mandiatory fields are empty");
    }

}

/**
 * Function to quote vechiles individually from AuotQuoting API
 * 
 * @author Chetu Inc. 
 * @returns Array Array containing quotation price from the auto quote API
 */
function AutoQuoteIndividual(){
    ERROR_MESSAGE = "Mandatory fields are empty";
    
    var carrier = $("#shipping_ship_via").val();
    
    /**
     * makign carrier value API ready
     */
    if (carrier === "1") {
        carrier = "Open";
        ERRORS = false;
    } else if (carrier === "2") {
        carrier = "enclosed";
        ERRORS = false;
    } else if (carrier === "3") {
        carrier = "Driveaway";
        ERRORS = true;
        ERROR_MESSAGE = "Select valid carrier type";
    } else {
        ERRORS = true;
        ERROR_MESSAGE = "Select valid carrier type";
    }
    
    var Origin = {
        City: checkEmpty($("#origin_city").val()),
        State: checkEmpty($("#origin_state").val()),
        Zipcode: checkEmpty($("#origin_zip").val())
    };

    var Destination = {
        City: checkEmpty($("#destination_city").val()),
        State: checkEmpty($("#destination_state").val()),
        Zipcode: checkEmpty($("#destination_zip").val())
    };
    
    var Vehicles = {
        0:{
            v_year:checkEmpty($("#vehicle_form").find("#year").val()),
            v_make:checkEmpty($("#make").val()),
            v_model:checkEmpty($("#model").val()),
            veh_op:1
        }
    };
    
    var data = {
        Transport:{
            Carrier: carrier,
            Origin: Origin,
            Destination: Destination,
            Vehicles:Vehicles
        },
        Additional:{
            order_deposit: $("#order_deposit").val(),
            order_deposit_type: $("#order_deposit_type").val(),
            auto_quote_api_pin: $("#auto_quote_api_pin").val(),
            auto_quote_api_key: $("#auto_quote_api_key").val()
        }
    };
    
    
    
    /**
     * When no validation errors than Trigger AJAX
     */
    // if (ERRORS === false) {
    //     triggerAjax('requestAutoQuotesIndividual',data,0);
    // } else {
    //     Swal.fire(ERROR_MESSAGE);
    // }
    triggerAjax('requestAutoQuotesIndividual',data,0);
}

/**
 * Function to handle the Auto Auote
 * @param id
 * @returns void
 */
function AutoQuoteIndividualFromGrid(id){
    
    var carrier = $("#shipping_ship_via").val();

    /**
     * makign carrier value API ready
     */
    if (carrier === "1") {
        carrier = "Open";
        ERRORS = false;
    } else if (carrier === "2") {
        carrier = "enclosed";
        ERRORS = false;
    } else if (carrier === "3") {
        carrier = "Driveaway";
        ERRORS = false;
    } else {
        swal.fire("Select Carrier Type");
        ERRORS = true;
    }
    
    var Origin = {
        City: checkEmpty($("#origin_city").val()),
        State: checkEmpty($("#origin_state").val()),
        Zipcode: checkEmpty($("#origin_zip").val())
    };

    var Destination = {
        City: checkEmpty($("#destination_city").val()),
        State: checkEmpty($("#destination_state").val()),
        Zipcode: checkEmpty($("#destination_zip").val())
    };
    
    var Vehicles = {
        0:{
            v_year:checkEmpty($("#year"+id).val()),
            v_make:checkEmpty($("#make"+id).val()),
            v_model:checkEmpty($("#model"+id).val()),
            veh_op:1
        }
    };
    
    var data = {
        Transport:{
            Carrier: carrier,
            Origin: Origin,
            Destination: Destination,
            Vehicles:Vehicles
        },
        Additional:{
            order_deposit: $("#order_deposit").val(),
            order_deposit_type: $("#order_deposit_type").val(),
            auto_quote_api_pin: $("#auto_quote_api_pin").val(),
            auto_quote_api_key: $("#auto_quote_api_key").val()
        }
    };
    
    /**
     * When no validation errors than Trigger AJAX
     */
    if (ERRORS === false) {
        triggerAjax('requestAutoQuotesIndividual',data,2,id);        
    } else {
        swal.fire("Mandiatory fields are empty");
    }
}

/**
 * Function triggering AJAX at URL defined in file
 * 
 * @param action
 * @param data
 * @param flag
 * @returns void
 */
function triggerAjax(action,data,flag,id) {
    $.ajax({
        url: AJAXURL,
        type: REQUSET_POST,
        dataType: DATA_TYPE,
        data: {
            action:action,
            requested:data
        },
        success: function (response) {
            if(flag === 0){
                
                /**
                 * Individual Vehicle Quoting from add vehicle popup
                 */
                $("#vehicle_form").find("#add_vehicle_carrier_pay").val(Number(response.response.Tariff).toFixed(2));
                $("#vehicle_form").find("#add_vehicle_deposit").val(Number(response.response.Deposite).toFixed(2));
                
            } else if (flag === 2) {
                
                /**
                 * Individual vehicle quoting from vehicle grid
                 */
                $("#tariff"+id).val(Number(response.response.Tariff).toFixed(2));
                $("#deposite"+id).val(Number(response.response.Deposite).toFixed(2));
                
            } else {
                /**
                 * Combined Vehicle Quoting
                 */
                $("Tariff").html(Number(response.response.Tariff).toFixed(2));
                $("Deposit").html(Number(response.response.Deposite).toFixed(2));
            }            
        }
    });
}

/**
 * Function triggering AJAX with custom parameters
 * 
 * @param action
 * @param data
 * @param URL 
 * @returns void
 */
function triggerCustomAjax(action,data,url,successFunction){
    
      Processing_show();
    $.ajax({
        url: url,
        type: REQUSET_POST,
        dataType: DATA_TYPE,
        data: {
            action:action,
            requested:data
        },
        success: function (response) {
          KTApp.unblockPage();
            successFunction();   

        }
    });
}

/**
 * Function to perform UI adjustments after successfull ajax request
 * 
 * @returns void
 */
function setAutoQuotingLeadsImported(){
    /**
     * Reload page after auto auoting
     */
    location.reload();
    $(".charliesLoaderContent").show();    
}

/**
 * Function to check the empty value in the input fields
 * 
 * @param value
 * @returns value
 * @author Chetu Inc.
 * @version 1.0
 */
function checkEmpty(value){
    if(value===""){
        ERRORS = true;
        return value;
    } else {
        return value;
    }
}