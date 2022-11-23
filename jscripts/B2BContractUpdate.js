/**
 * This is the file created for dealing with the functionality at client end in
 * B2B contract Update page
 * 
 * @author Chetu Inc.
 */

/* getting geolocations*/
getLocation();

/* Tab management functionality */
$(".loader").hide();
$('.toggle').click(function (e) {
    e.preventDefault();
    var $this = $(this);

    if ($this.next().hasClass('show')) {
        $this.next().removeClass('show');
        $this.next().slideUp(350);
    } else {
        $this.parent().parent().find('li .inner').removeClass('show');
        $this.parent().parent().find('li .inner').slideUp(350);
        $this.next().toggleClass('show');
        $this.next().slideToggle(350);
    }
});

/* payment method UI manupulations*/
$(".creditCardInfo").hide();
$(".ach").hide();
$("#payment_method").change(function () {
    /*  when credit card  is selected as a payment method*/
    if (Number($("#payment_method").val()) === 3) {
        /* display creditr card detail section */
        $(".creditCardInfo").show();
        $(".ach").hide();
    } else if (Number($("#payment_method").val()) === 4) {
        /* display ACH detail section */
        $(".ach").show();
        $(".creditCardInfo").hide();
    } else {
        /* display none for payment method */
        $(".creditCardInfo").hide();
        $(".ach").hide();
    }
});

/* functionality to hide error UI on click */
$("#loader").click(function () {
    $(".loader").hide("");
});

var sectionOneValidationFlag = false;
var sectionTwoValidationFlag = false;
var sectionThreeValidationFlag = false;
var sectionFourValidationFlag = false;

var errorHTMLSection1 = "";
var errorHTMLSection2 = "";
var errorHTMLSection3 = "";
var errorHTMLSection4 = "";

/* 
 * This function is used to validate 1st section of the B2B form
 *  
 *  @returns Error UI if there is error
 */
function validateSectionOne(){
    
    sectionOneValidationFlag = false;
    errorHTMLSection1 = "";
    
    if(checkEmpty("sfname") !== true ){
        sectionOneValidationFlag = true;
        errorHTMLSection1 += "<p>Shipper First Name cannot be empty.</p>";
    }
    
    if(checkEmpty("slname") !== true ){
        sectionOneValidationFlag = true;
        errorHTMLSection1 += "<p>Shipper Last Name cannot be empty.</p>";
    }
    
    if(checkEmpty("scompany") !== true ){
        sectionOneValidationFlag = true;
        errorHTMLSection1 += "<p>Shipper Company Name cannot be empty.</p>";
    }
    
    if(checkEmpty("shours") !== true ){
        sectionOneValidationFlag = true;
        errorHTMLSection1 += "<p>Shipper Hours of Operation cannot be empty.</p>";
    }
    
    if(checkEmpty("sphone") !== true ){
        sectionOneValidationFlag = true;
        errorHTMLSection1 += "<p>Shipper Phone cannot be empty.</p>";
    }
    
    if(checkEmpty("sEmail") !== true ){
        sectionOneValidationFlag = true;
        errorHTMLSection1 += "<p>Shipper Email cannot be empty.</p>";
    }
    
    /* check valid email */
    if(checkEmail("sEmail") !== true ){
        sectionOneValidationFlag = true;
        errorHTMLSection1 += "<p>Invalid Shipper Email.</p>";
    }
    
    /* check valid phone */
    if(checkPhone("sphone") !== true ){
        sectionOneValidationFlag = true;
        errorHTMLSection1 += "<p>Invalid Shipper Phone.</p>";
    }
}

/* 
 * This function is used to open the 2nd section of the B2B form after there is
 *  no validation errors
 *  
 *  @returns void 
 */
function openSecondSection(){
    
    /* calling section 1 validater function*/
    validateSectionOne();   
    
    /* when no validation errors */
    if(sectionOneValidationFlag === false){
        $("#tab2").trigger('click');
    }
    
    /* when error in section 1 show errors */
    if(sectionOneValidationFlag === true){        
        showErrors(errorHTMLSection1);
    }
}


/* 
 * This function is used to validate 2nd section of the B2B form
 *  
 *  @returns Error UI if there is error
 */
function validateSectionTwo(){
    
    sectionTwoValidationFlag = false;
    errorHTMLSection2 = "";
    
    if(checkEmpty("fname1") !== true ){
        sectionTwoValidationFlag = true;
        errorHTMLSection2 += "<p>In additional information first name cannot be empty.</p>";
    }
    
    if(checkEmpty("lname1") !== true ){
        sectionTwoValidationFlag = true;
        errorHTMLSection2 += "<p>In additional information last name cannot be empty.</p>";
    }
    
    if(checkEmpty("title1") !== true ){
        sectionTwoValidationFlag = true;
        errorHTMLSection2 += "<p>In additional information title cannot be empty.</p>";
    }
    
    /* check valid email */
    if(checkEmail("email1") !== true ){
        sectionTwoValidationFlag = true;
        errorHTMLSection2 += "<p>Invalid additional information Email-1.</p>";
    }
}

/* 
 * This function is used to open the 3rd section of the B2B form after there is
 *  no validation errors
 *  
 *  @returns void 
 */
function openThirdSection(){
    
    /* calling section 1 validater function*/
    validateSectionTwo();   
    
    /* when no validation errors */
    if(sectionTwoValidationFlag === false){
        $("#tab3").trigger('click');
    }
        
    /* when error in section 3 show errors */
    if(sectionTwoValidationFlag === true){        
        showErrors(errorHTMLSection2);
    }
}

/* 
 * This function is used to validate 3rd section of the B2B form
 *  
 *  @returns Error UI if there is error
 */
function validateSectionThree(){
    
    sectionThreeValidationFlag = false;
    errorHTMLSection3 = "";
    
    if(checkEmpty("payment_method") !== true ){
        sectionThreeValidationFlag = true;
        errorHTMLSection3 += "<p>Payment Method cannot be empty.</p>";
    }
    
    /* when payment methos is ACH */
    if($("#payment_method").val() === "4"){
       
        if(checkEmpty("bName") !== true ){
            sectionThreeValidationFlag = true;
            errorHTMLSection3 += "<p>Banking Name cannot be empty.</p>";
        }
        if(checkEmpty("bAccountNumber") !== true ){
            sectionThreeValidationFlag = true;
            errorHTMLSection3 += "<p>Banking Account Number cannot be empty.</p>";
        }
        if(checkEmpty("bRouting") !== true ){
            sectionThreeValidationFlag = true;
            errorHTMLSection3 += "<p>Banking Routing Number cannot be empty.</p>";
        }
    }
    
    /* when payment methos is Credit card */
    if($("#payment_method").val() === "3"){
        
         if(checkEmpty("ccFname") !== true ){
            sectionThreeValidationFlag = true;
            errorHTMLSection3 += "<p>Credit card first name cannot be empty.</p>";
        }
        if(checkEmpty("ccLname") !== true ){
            sectionThreeValidationFlag = true;
            errorHTMLSection3 += "<p>Credit card last name cannot be empty.</p>";
        }
        if(checkEmpty("ccType") !== true ){
            sectionThreeValidationFlag = true;
            errorHTMLSection3 += "<p>Select credit card type.</p>";
        }
        if(checkEmpty("ccNumber") !== true ){
            sectionThreeValidationFlag = true;
            errorHTMLSection3 += "<p>Credit card number cannot be empty.</p>";
        }
        if(checkEmpty("ccCvv") !== true ){
            sectionThreeValidationFlag = true;
            errorHTMLSection3 += "<p>Credit card CVV cannot be empty.</p>";
        }
        if(checkEmpty("ccMonth") !== true ){
            sectionThreeValidationFlag = true;
            errorHTMLSection3 += "<p>Select credit card expiry month.</p>";
        }
        if(checkEmpty("ccYear") !== true ){
            sectionThreeValidationFlag = true;
            errorHTMLSection3 += "<p>Select credit card expiry year.</p>";
        }
    }
}

/* 
 * This function is used to open the 4th section of the B2B form after there is
 *  no validation errors
 *  
 *  @returns void 
 */
function openFourthSection(){
    
    /* calling section 1 validater function*/
    validateSectionThree();   
    
    /* when no validation errors */
    if(sectionThreeValidationFlag === false){
        $("#tab4").trigger('click');
    }
    
    /* when error in section 1 show errors */
    if(sectionThreeValidationFlag === true){        
        showErrors(errorHTMLSection3);
    }
}

/* 
 * This function is used to validate 4th section of the B2B form
 *  
 *  @returns Error UI if there is error
 */
function validateSectionFour(){
    
    sectionFourValidationFlag = false;
    errorHTMLSection4 = "";
    
    if(checkEmpty("ein") !== true ){
        sectionFourValidationFlag = true;
        errorHTMLSection4 += "<p>EIN cannot be empty.</p>";
    }
}

/* 
 * This function is used to open the 5th section of the B2B form after there is
 *  no validation errors
 *  
 *  @returns void 
 */
function openFiveSection(){
    
    /* calling section 1 validater function*/
    validateSectionFour();   
    
    /* when no validation errors */
    if(sectionFourValidationFlag === false){
        $("#tab5").trigger('click');
    }
    
    /* when error in section 1 show errors */
    if(sectionFourValidationFlag === true){        
        showErrors(errorHTMLSection4);
    }
}

/* 
 * This function is used to open the 6th section of the B2B form after there is
 *  no validation errors
 *  
 *  @returns void 
 */
function openSixSection(){
    $("#tab6").trigger('click');
}

/*
 * This function is to open the previous tab on the basis of ID sent in the parameter
 * 
 * @param number id
 * @returns void
 */
function openPrevious(id){
    $("#tab"+id).trigger('click');
}

/*
 * This function is used to check the empty value of a field in the form
 * 
 * @param string id 
 * @returns true
 */
function checkEmpty(id){
    if($("#"+id).val() !== ""){
        return true;
    }
}

/**
 * This function is to validate the Email format
 * 
 * @param id
 * @returns true
 */
function checkEmail(id) {
    var email = $("#"+id).val();
    var atpos = email.indexOf("@");
    var dotpos = email.lastIndexOf(".");
    if ( !(atpos<1 || dotpos<atpos+2 || dotpos+2>=email.length) ) {
        return true;
    }
}

/**
 * This function is to validate the Email format
 * 
 * @param id
 * @returns true
 */
function checkPhone(id){
    var phone = $("#"+id).val();
    if(phone.length > 6 && phone.length < 11)
    {  
        return true;  
    }
}

/*
 * This function is created to show the error in the Error UI from all sections in
 *  the form
 *  
 *  @returns void 
 */
function showErrors(html){
    $("#loader").show();
    $(".loader").show();
    $("#loader").html(html);
    setTimeout( clearErrors, 3000 );
}

/* 
 * This function is used to unset the Error UI and variables
 * 
 * @returns void  
 */
function clearErrors(){
    $(".loader").hide();
    $("#loader").hide();
    errorHTMLSection1 = "";
}

/*
 * This functionality is to calculate the geolocation
 * 
 * @returns void 
 */
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition);
    } else {
        var x = document.getElementById("locations");
        x.innerHTML = "Geolocation is not supported by this browser.";
    }
}

/*
 * This functionality is to display the geolocation on the B2B contract update page
 * 
 * @returns void 
 */
function showPosition(position) {
    var google_map_position = new google.maps.LatLng( position.coords.latitude, position.coords.longitude );
    var google_maps_geocoder = new google.maps.Geocoder();
    google_maps_geocoder.geocode(
        { 'latLng': google_map_position },
        function( results, status ) {
            var x = document.getElementById("locations");
            x.innerHTML = results[0].formatted_address;
        }
    );
}

/* Functionality to check the duplicate shipper in database */
$("#sEmail").blur(function () {
    checkUniqueShipperData("email", $("#sEmail").val());
});

/*
 * This function chceck the Duplicate values for the shipper on the basis of key and value
 * 
 * @param String key
 * @param String value  
 * @returns alert message 
 */
function checkUniqueShipperData(key, value) {
    $("#shipperData").show();
    $("#orderQuotesList").html("");
    $("#orderQuotesList").hide();
    $.ajax({
        type: 'POST',
        url: BASE_PATH + 'application/ajax/shipper.php',
        dataType: 'json',
        data: {
            action: 'validateUniqueShipper',
            key: key,
            value: value
        },
        success: function (response) {
            if (response.exists > 0) {
                alert("Email Already Exists");
                $("#sEmail").val("");
            }
        }
    });
}