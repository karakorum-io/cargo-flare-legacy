/**
 * File dealing with all the functionaity for tract and trace on client side
 * 
 * @author Charlie
 * @version 1.0
 */

/**
 * Function to hide custom popup
 * @return void
 */
function hideCustomPopup() {
    $(".location-sugg-div").hide();
}

/**
 * Function to fill State and City Automatically
 * 
 * @return void
 */
function add_location_values(prefix, city, state) {

    console.log("state");
    $("#state").val(state);
    $("#city").val(city);
    hideCustomPopup();
}

/**
 * fill state and city values while editing
 */
function add_location_values_edit(prefix, city, state){
    $("#edit-state").val(state);
    $("#edit-city").val(city);
    hideCustomPopup();
}

/**
 * Function to auto fill State and City while adding location
 * 
 * @returns void
 */
function populate_location(prefix, zip) {
    console.log("This is populate function" + BASE_PATH);

    $.ajax({
        type: 'POST',
        url: BASE_PATH + "application/ajax/ajax.php?action=getByZipLocal",
        dataType: 'json',
        data: {
            zip: zip
        },
        success: function (res) {
            if (res.success) {
                if (res.data.status == "ok") {
                    var contentData = "";
                    if (res.data.size > 1) {
                        contentData += "<table width='100%'>\n\
                                                            <tr>\n\
                                                                <td>\n\
                                                                    <span style='font-size:11px;'>Suggested cities </span>\n\
                                                                </td>\n\
                                                                <td align='right'>\n\
                                                                    <a href='javascript:void(0);' onclick='hideCustomPopup()'>close</a>\n\
                                                                </td>\n\
                                                            </tr>";
                        for (i = 0; i < res.data.size; i++) {
                            contentData += "<tr><td><a href ='javascript:void(0);' onclick=\"add_location_values('" + prefix + "','" + res.data.data[i]['city'] + "' , '" + res.data.data[i]['state'] + "');\">" + res.data.data[i]['city'] + " ";
                            contentData += ", " + zip + "";
                            contentData += ", " + res.data.data[i]['state'] + "";
                            contentData += "</a></td></tr>";
                        }
                        contentData += "</table>";
                        $(".location-sugg-div").show();
                        $("#location_suggestion").html("");
                        $("#location_suggestion").html(contentData);
                    } else {
                        $("input[name='" + prefix + "city']").val(res.data.data[0]['city']);
                        $("select[name='" + prefix + "state']").val(res.data.data[0]['state']);
                    }
                }

            } else {
                swal.fire('City for this zip code is not matching.');
            }
        }
    });
}

/**
 * Auto populate state and city while editing carrier location
 * @returns void
 */
function populate_location_edit(prefix, zip) {
    console.log("This is populate function edit" + BASE_PATH);

    $.ajax({
        type: 'POST',
        url: BASE_PATH + "application/ajax/ajax.php?action=getByZipLocal",
        dataType: 'json',
        data: {
            zip: zip
        },
        success: function (res) {
            if (res.success) {
                if (res.data.status == "ok") {
                    var contentData = "";
                    if (res.data.size > 1) {
                        contentData += "<table width='100%'>\n\
                                                            <tr>\n\
                                                                <td>\n\
                                                                    <span style='font-size:11px;'>Suggested cities </span>\n\
                                                                </td>\n\
                                                                <td align='right'>\n\
                                                                    <a href='javascript:void(0);' onclick='hideCustomPopup()'>close</a>\n\
                                                                </td>\n\
                                                            </tr>";
                        for (i = 0; i < res.data.size; i++) {
                            contentData += "<tr><td><a href ='javascript:void(0);' onclick=\"add_location_values_edit('" + prefix + "','" + res.data.data[i]['city'] + "' , '" + res.data.data[i]['state'] + "');\">" + res.data.data[i]['city'] + " ";
                            contentData += ", " + zip + "";
                            contentData += ", " + res.data.data[i]['state'] + "";
                            contentData += "</a></td></tr>";
                        }
                        contentData += "</table>";
                        $(".location-sugg-div").show();
                        $("#location_suggestion").html("");
                        $("#location_suggestion").html(contentData);
                    } else {
                        $("input[name='" + prefix + "edit-city']").val(res.data.data[0]['city']);
                        $("select[name='" + prefix + "edit-state']").val(res.data.data[0]['state']);
                    }
                }
            } else {
                swal.fire('City for this zip code is not matching.');
            }
        }
    });
}

/**
 * function to clear the carrier add location form
 * 
 * @returns void
 */
function reset_form() {
    $("#state").val("");
    $("#city").val("");
    $("#zip").val("");
}

/**
 * Function to edit existing carreir location
 * 
 * @param Integer track_id
 * @author Chetu Inc.
 * @version 1.0
 */
function modify_location(id, entity_id, row_id) {

    /**
     * Clearing previous values
     */
    $("#edit-state").val();
    $("#edit-city").val();
    $("#edit-zip-code").val();
    /**
    * Setting new values in popup fields
    */
    $("#edit-state").val($("#state-" + row_id).html());
    $("#edit-city").val($("#city-" + row_id).html());
    $("#edit-zip-code").val($("#zip-code-" + row_id).html()); 
    $("#edit_location").val(entity_id);
    $("#id_location").val(id);
    $("#edit-location").modal();
   
}

function edit_loction_update(entity_id)  
{
    if ($("#edit-state").val() == "") {
        swal.fire("State cannot be empty!");
        error = true;
    }
    if ($("#edit-city").val() == "") {
        swal.fire("City cannot be empty!");
        error = true;
    }
           
    if (error == false) {
        error = true;

        var lat = "";
        var lng = "";
        /**
         * getting lat lng from pincode
         */
        var key = 'AIzaSyB6dx80YTn7l6imjRElosj-yAH7LsXBmrU';
        var url = "https://maps.googleapis.com/maps/api/geocode/json?address="
                + $("#edit-city").val() + "," + $("#edit-city").val() + "," + $("#edit-zip-code").val()+"&key="+key;
        $.ajax({
            url: url,
            dataType: 'json',
            success: function (results) {
                console.log('dsadda');
                if (results.status == "OK") {
                    lat = results.results[0].geometry.location.lat;
                    lng = results.results[0].geometry.location.lng;
                    /**
                    * Triggering AJAX
                    */
                    $.ajax({
                        type: 'POST',
                        url: '../../../ajax/track_n_trace.php',
                        dataType: 'json',
                        data: {
                            track_id: $("#id_location").val(),
                            entity_id: entity_id,
                            state: $("#edit-state").val(),
                            city: $("#edit-city").val(),
                            zip_code: $("#edit-zip-code").val(),
                            lat: lat,
                            lng: lng,
                            action: 'edit-carrier-location'
                        },
                        success: function (response) {
                            if (response.result == true) {
                                $("#state-" + row_id).html($("#edit-state").val());
                                $("#city-" + row_id).html($("#edit-city").val());
                                $("#zip-code-" + row_id).html($("#edit-zip-code").val());
                                $("#zip-code-" + row_id).html($("#edit-zip-code").val());
                                $("#edit-location").modal("hide");
                            } else {
                                swal.fire("Cant update, Try again later!");
                            }
                        },
                        error: function (response)
                        {
                             swal.fire("Can't update location data. Try again later.");
                        }
                        });
                        } else {
                             swal.fire("Google API Error!");
                }
            }
        });
    } else {
        error = true;
    }

}

/**
 * Function to soft delete carrier location
 * 
 * @param Integer id
 * @returns void
 */
function delete_location(id, entity_id, row_id) {

    console.log(entity_id,id);
    if (confirm("Are you sure you want to delete location?")) {
        $.ajax({
            type: 'POST',
            url: '../../../ajax/track_n_trace.php',
            dataType: 'json',
            data: {
                track_id: id,
                entity_id: entity_id,
                action: 'delete-carrier-location'
            },
            success: function (response) {
                if (response.result == true) {
                    $("#list-" + row_id).remove();
                   // location.reload();
                } else {
                    swal.fire("Cant update, Try again later!");
                }
            },
            error: function (response) {
                swal.fire("Can't update lcoation data. Try again later.");
            }
        });
    }
}