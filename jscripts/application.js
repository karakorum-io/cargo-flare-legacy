var final = '';
var vehicle_type_data = ['Coupe', 'Sedan Small', 'Sedan Midsize', 'Sedan Large', 'Convertible', 'Pickup Small', 'Pickup Crew Cab', 'Pickup Full-size', 'Pickup Extd. Cab', 'RV', 'Dually', 'SUV Small', 'SUV Mid-size', 'SUV Large', 'Travel Trailer', 'Van Mini', 'Van Full-size', 'Van Extd. Lenght', 'Van Pop-Top', 'Motorcycle', 'Boat', 'Other'];
var hide_notes = false;
var myLoadingParams = {
    loaderClass: "loading_bar"
};
var acc_location = null;
var dispatch_date_type = null;
var dispatch_sheet_id = null;

/*outerHTML for jQuery*/
jQuery.fn.outerHTML = function(s) {
    return s ? this.before(s).remove() : jQuery("<p>").append(this.eq(0).clone()).html();
};

function in_array(needle, haystack, strict) {
    var found = false,
        key, strict = !!strict;
    for (key in haystack) {
        if ((strict && haystack[key] === needle) || (!strict && haystack[key] == needle)) {
            found = true;
            break;
        }
    }
    return found;
}

function zebra() {
    var z = 0;
    $("#vehicles-grid tbody tr").each(function() {
        $(this).removeClass('even');
        if (z % 2) {
            $(this).addClass('even');
        }
        z++;
    });
}
/* System messages */
function checkSysMessages() {
    $.ajax({
        url: BASE_PATH + 'application/ajax/sysmessages.php',
        data: { action: "get" },
        type: 'POST',
        dataType: 'json',
        beforeSend: function() {},
        success: function(retData) {
            if ($("#sysmessage").size() > 0) {
                if (retData.message != "" && !$('#sysmessage').is(':visible')) {
                    $('#sysmessage').html(retData.message).show("drop");
                }
            }
        }
    });
}

function closeSysMessage(id) {
    $.ajax({
        url: BASE_PATH + 'application/ajax/sysmessages.php',
        data: { action: "close", id: id },
        type: 'POST',
        dataType: 'json',
        beforeSend: function() {},
        success: function(retData) {},
        complete: function() {
            $('#sysmessage').hide("drop");
        }
    });
}
/* Tasks */
function checkTasks() {
    $.ajax({
        type: "POST",
        url: BASE_PATH + 'application/ajax/tasks.php',
        data: { action: "get" },
        dataType: "json",
        success: function(response) {
            var found = false;
            if (response.success == true) {
                for (i in response.data) {
                    found = false;
                    for (j in tasks) {
                        if (tasks[j].id == response.data[i].id) {
                            found = true;
                        }
                    }
                    if (!found) {
                        tasks.push(response.data[i]);
                        if (tasks.length == 1) {
                            curtask = 0;
                            $("#todays_task").val(tasks[curtask].message);
                            $(".task-bar").show();
                        } else {
                            curtask++;
                        }
                    }
                }
            }
        }
    });
}

function ucfirst(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

jQuery.fn.extend({
    disableSelection: function() {
        this.each(function() {
            this.onselectstart = function() {
                return false;
            };
            this.unselectable = "on";
            jQuery(this).css('-moz-user-select', 'none');
            jQuery(this).css('-webkit-user-select', 'none');
        });
    },
    enableSelection: function() {
        this.each(function() {
            this.onselectstart = function() {};
            this.unselectable = "off";
            jQuery(this).css('-moz-user-select', 'auto');
            jQuery(this).css('-webkit-user-select', 'auto');
        });
    }
});

function validateUsPhone(e, f) {
    var len = f.value.length;
    var key = whichKeyCode(e);
    //Swal.fire(key);
    if (key > 47 && key < 58) {

        if (len == 3)
            f.value = f.value + '-'
        else if (len == 7)
            f.value = f.value + '-'
        else
            f.value = f.value;

    } else if (key == 8 || key == 46) {
        if (ShowSelection(f)) {
            if ((len) == 3)
                f.value = f.value + '-'
            else if ((len) == 7)
                f.value = f.value + '-'
                // else f.value=f.value;
        }

    } else {
        f.value = f.value.replace(/[^0-9-]/, '')
        f.value = f.value.replace('--', '-')
    }
}

function whichKeyCode(e) {
    var code;
    if (!e)
        var e = window.event;
    if (e.keyCode)
        code = e.keyCode;
    else if (e.which)
        code = e.which;
    return code
        //  return String.fromCharCode(code);
}

function ShowSelection(f) {
    var textComponent = f;
    var selectedText;
    // IE version
    if (document.selection != undefined) {
        textComponent.focus();
        var sel = document.selection.createRange();
        selectedText = sel.text;
    }
    // Mozilla version
    else if (textComponent.selectionStart != undefined) {
        var startPos = textComponent.selectionStart;
        var endPos = textComponent.selectionEnd;
        selectedText = textComponent.value.substring(startPos, endPos)
    }
    //Swal.fire("You selected: " + selectedText.length);
    if (selectedText.length > 0)
        return true;
    else
        return false;
}

$(document).ready(function() {
    $('#app_sms').keypress(function(e) {

        if (e.which == 13) {
            //Swal.fire('You pressed enter!');
            var textSMS = $("#app_sms").val();
            if (textSMS != "")
                sendChatSMS();
            return false;
        }
    });
    $('#app_chat').keypress(function(e) {

        if (e.which == 13) {
            //Swal.fire('You pressed enter!');
            var textChat = $("#app_chat").val();
            if (textChat != "")
                sendChatNew();
            return false;
        }
    });
    $(".multi-vehicles").click(function() {
        $(this).next().toggle();
    });
    $(".multi-vehicles-new").click(function() {

        //$(this).next().toggle();

    });
    $(".search-box-header").click(function() {

        $(this).next().toggle();

    });
    $(".viewsample").click(function() {
        $(this).next().toggle();
    });
    $(".payment-info-trigger").click(function() {
        $(this).next().toggle();
    });
    $(".form-box-buttons-new *, .form-box-buttons *, .ui-button *, #fd_alert *, .task-bar *").disableSelection();
    $(".lead-info p.block-title, .quote-info p.block-title, .order-info p.block-title, .dispatch-info p.block-title").click(function() {
        $(this).next().toggle();
    });
    $(".lead-info p, .quote-info p").disableSelection();

    getByZipNew('origin');
    getByZipNew('destination');
    getByZipNew('shipper');
    getByZipNew('carrier');
    getByZipNew('pickup');
    getByZipNew('deliver');


    $(".datepicker").datepicker();
    $('.import-toggle').click(function() {
        $('.import-hidden').toggle();
    });
    $('.elementname').focus(function() {
        // Swal.fire($(this).attr("elementname"));
        // $(this).addClass("focus_mandatory_element");
        if ($(this).attr("elementname") == 'input' || $(this).attr("elementname") == 'select') {
            //$(this).removeClass("form-box-textfield");
            $(this).addClass("focus_mandatory_element");

        }

    });
    $('.elementname').blur(function() {
        if ($(this).attr("elementname") == 'input' || $(this).attr("elementname") == 'select') {
            $(this).removeClass("focus_mandatory_element")
                //$(this).addClass("form-box-textfield");
        }


    });
    //for email trim
    $("#mail_to_new").change(function() {
        var new_trimemail = $.trim($("#mail_to_new").val());
        $('#mail_to_new').val(new_trimemail);
    });
    $("#mail_to").change(function() {
        var newtrimemail = $.trim($("#mail_to").val());
        $('#mail_to').val(newtrimemail);
    });
});

function Get_Account_() {
    if ($("input[name='acc_global_search_result_item']:checked").length == 0)
        return;
    if (window.applyGlobalSearch) {
        applyGlobalSearch($("input[name='acc_global_search_result_item']:checked").val());
    }
    $(this).modal('hide');
}

// live
function getByZipNew(prefix) {
    $("input[name='" + prefix + "_zip']").on("blur", function() {
        showziptooltiplocal(prefix);
    });
}

function showziptooltiplocal(prefix) {

    var widthW = 400;
    var leftL = 90;
    if (prefix == 'carrier' || prefix == 'pickup' || prefix == 'deliver') {
        widthW = 300;
        leftL = 350;
    }
    var position = $('#' + prefix + '_zip').position();
    $("#notes_container").css("left", position.left + leftL);
    $("#notes_container").css("top", position.top);
    $("#notes_container").css("width", widthW);


    var ajax_zip = $.trim($("input[name='" + prefix + "_zip']").val());
    ajax_zip = ajax_zip.replace(/-/gi, '');
    ajax_zip = ajax_zip.replace(/_/gi, '');

    if (ajax_zip != '') {
        // next, we want to check if our data has already been cached
        //if (origin.data('ajax') !== 'cached') {
        $.ajax({
            type: 'POST',
            url: BASE_PATH + "application/ajax/ajax.php?action=getByZipLocal",
            dataType: 'json',
            data: {
                zip: ajax_zip
            },
            success: function(res) {
                if (res.success) {

                    if (res.data.status == "ok") {
                        var contentData = "";
                        if (res.data.size > 1) {

                            for (i = 0; i < res.data.size; i++) {
                                contentData += "<tr><td><a href ='javascript:void(0);' onclick=\"selectStateCity('" + prefix + "','" + res.data.data[i]['city'] + "' , '" + res.data.data[i]['state'] + "');\">" + res.data.data[i]['city'] + " ";
                                contentData += ", " + ajax_zip + "";
                                contentData += ", " + res.data.data[i]['state'] + "";
                                contentData += "</a></td></tr>";
                            }

                            hide_notes = false;

                            $("#zipCode").modal('show');

                            $("#searchedZip").html("");
                            $("#searchedZip").html(contentData);


                        } else {
                            $("input[name='" + prefix + "_city']").val(res.data.data[0]['city']);
                            $("select[name='" + prefix + "_state']").val(res.data.data[0]['state']);
                        }
                    }

                } else
                    swal.fire('City for this zip code is not matching.');
            }
        });
    }

}

function selectStateCity(prefix, city, state) {

    $("input[name='" + prefix + "_city']").val(city);
    $("select[name='" + prefix + "_state']").val(state);

    $("#zipCode").modal('hide');
}

function getByZip(prefix) {
    $("input[name='" + prefix + "_zip']").live("blur", function() {

        var ajax_zip = $.trim($(this).val());
        ajax_zip = ajax_zip.replace(/-/gi, '');
        ajax_zip = ajax_zip.replace(/_/gi, '');

        if ($("select[name='" + prefix + "_country']").val() == '') {
            $("select[name='" + prefix + "_country']").val('US');
        }

        if ($("select[name='" + prefix + "_country']").val() != 'US' || $.trim($(this).val()) == '')
            return false;
        $.ajax({
            type: 'POST',
            url: BASE_PATH + "application/ajax/ajax.php?action=getByZip",
            dataType: 'json',
            data: {
                zip: ajax_zip
            },
            success: function(res) {
                if (res.success) {
                    $("input[name='" + prefix + "_city']").val(res.data['city']);
                    $("select[name='" + prefix + "_state']").val(res.data['state']);
                }
            }
        });
    });
}

function checkAllEntities() {
    $(".entity-checkbox").attr("checked", "checked");
}

function uncheckAllEntities() {
    $(".entity-checkbox").attr("checked", null);
}

function mapIt(entity_id) {
    $.ajax({
        type: 'POST',
        url: BASE_PATH + 'application/ajax/map.php',
        data: {
            action: 'getRoute',
            entity_id: entity_id
        },
        dataType: 'json',
        success: function(response) {
            if (response.success == true) {
                window.open(decodeURIComponent(response.data), "_blank");
            }
        }
    });
}

function showNotes(entity_id, notes_type) {
    if ($("#notes_add").css("display") != "none")
        return;
    hide_notes = false;
    var position = $("#notes_" + notes_type + "_" + entity_id).position();
    $("#notes_container").css("left", position.left + 30);
    $("#notes_container").css("top", position.top);
    $("#notes_container").css("width", 550);
    if ((notes[notes_type][entity_id]) == undefined) {
        $.ajax({
            type: 'POST',
            url: BASE_PATH + 'application/ajax/notes.php',
            data: {
                action: 'get',
                entity_id: entity_id,
                notes_type: notes_type
            },
            dataType: 'json',
            success: function(response) {
                if (response.success == true) {
                    notes[notes_type][entity_id] = response.data;
                    if (!hide_notes) {
                        displayNotes(notes_type, notes[notes_type][entity_id]);
                    }
                }
            }
        });
    } else {
        displayNotes(notes_type, notes[notes_type][entity_id]);
    }
}

function displayNotes(notes_type, notes_data) {
    if (notes_data.toString() == "")
        return;
    var title = "";
    switch (notes_type) {
        case 1:
            title = "Notes To Shipper";
            break;
        case 2:
            title = "Notes From Shipper";
            break;
        case 3:
            title = "Internal Notes";
            break;
    }
    $("#notes_container").html("");
    $("#notes_container").append("<p>" + title + "<\/p>");
    for (i in notes_data) {
        var container = "";
        var contactname = notes_data[i].sender;
        var bgcolor = '#ffffff';
        if (notes_data[i].system_admin == 1) {
            contactname = "System";
        }
        container += "<div><span class='note-from'>" + notes_data[i].created;
        if (notes_type != 2) {
            container += " " + contactname;
        }
        if (notes_data[i].priority == 2)
            bgcolor = '#FF0000';

        container += "<\/span><br/><span class='note-data' style='color:" + bgcolor + "'>";
        if (notes_data[i].system_admin == 1 || notes_data[i].priority == 2)
            container += "<b >";
        container += notes_data[i].text;
        if (notes_data[i].system_admin == 1 || notes_data[i].priority == 2)
            container += "<\/b>";
        container += "<\/span><\/div>";

        $("#notes_container").append(container);
    }
    $("#notes_container").show();
    if (($("#notes_container").height() + $("#notes_container").position().top) > ($(window).height() + $(window).scrollTop() - 20)) {
        $("#notes_container").css("top", $(window).height() + $(window).scrollTop() - $("#notes_container").height() - 20);
    }
}

function hideNotes() {
    hide_notes = true;
    $("#notes_container").hide();
}

function closeAddNotes() {
    $("#kt_modal_4").modal('hide');
}

function addQuickNote() {
    var textOld = $("#notes_add1 textarea").val();
    var str = textOld + " " + $("#notes_add1 select").val();
    $("#notes_add1 textarea").val(str);
}

function quoteaddQuickNote() {
    var quick_notes = $("#quick_notes option:selected").val();
    if (final != '') {
        final += ',' + quick_notes;
    } else {
        final += quick_notes;
    }
    $("#internal_note ").val(final);
}

var notesOpenFlag = 0;

function openAddNotes(entity_id, notes_type) {

    $("#kt_modal_4").modal();
    $("#kt_modal_4").find('.modal-body').find('textarea').val('');

    add_entity_id = entity_id;
    add_notes_type = notes_type;
    $("#notes_container").hide();
    var position = $("#notes_" + notes_type + "_" + entity_id).position();
    $("#notes_add").css("left", position.left + 30);
    $("#notes_add").css("top", position.top);

    switch (notes_type) {
        case 1:
            $("#notes_add p").text("Add Note To Shipper");
            break;
        case 2:
            $("#notes_add p").text("Add Note From Shipper");
            break;
        case 3:
            $("#notes_add p").text("Add Internal Note");
            break;
    }
    if ((notes[notes_type][entity_id]) == undefined) {
        $.ajax({
            type: 'POST',
            url: BASE_PATH + 'application/ajax/notes.php',
            data: {
                action: 'get',
                entity_id: entity_id,
                notes_type: notes_type

            },

            dataType: 'json',
            success: function(response) {
                if (response.success == true) {
                    notes[notes_type][entity_id] = response.data;
                    if (response.color == 1)
                        $("#notes_3_" + entity_id).removeClass("note-red").addClass("note-green");
                    if (!hide_notes) {
                        displayNotesNew(notes_type, notes[notes_type][entity_id]);

                    }
                }
            }
        });

    } else {
        displayNotesNew(notes_type, notes[notes_type][entity_id]);
    }
}

function displayNotesNew(notes_type, notes_data) {
    var title = "";
    switch (notes_type) {
        case 1:
            title = "Notes To Shipper";
            break;
        case 2:
            title = "Notes From Shipper";
            break;
        case 3:
            title = "Internal Notes";
            break;
    }
    $("#notes_add_title").html("");
    $("#notes_container_new").html("");
    var ENumber = '';
    for (i in notes_data) {

        var container = "";
        var contactname = notes_data[i].sender;
        var bgcolor = '#000000';
        if (notes_data[i].system_admin == 2) {
            contactname = "System";
        }

        container += "<div><span >" + notes_data[i].created + "</span>";
        if (notes_data[i].priority == 2)
            bgcolor = '#FF0000';

        var discardStr = '';
        if (notes_data[i].discard == 1)
            discardStr = 'text-decoration: line-through;';

        container += "";

        if (notes_data[i].system_admin == 1 || notes_data[i].system_admin == 2 || notes_data[i].priority == 2)
            container += "<b>";
        container += notes_data[i].text;

        if (notes_data[i].system_admin == 1 || notes_data[i].system_admin == 2 || notes_data[i].priority == 2)
            container += "<\/b>";
        container += "<\/span>";
        if (notes_type != 2) {
            container += "<span class='container'> Created by " + contactname + "</span>";
        }
        container += "<\/div><br>";

        $("#notes_container_new").append(container);
        ENumber = notes_data[i].number;
    }
    title = title + ' ' + ENumber;
    $("#notes_add_title").append("" + title + "<span onclick=\"$('#notes_add').hide();notesOpenFlag = 0;\" style='float:right; color:red;'></span>");

    $("#notes_add textarea").val("");
    $("#notes_add").show();
    notesOpenFlag = 1;
}

function addNote() {

    if (add_busy)
        return;
    add_busy = true;
    var note_text = $("#kt_modal_4").find('.modal-body').find('textarea').val();;

    var priority = $.trim($("#priority_notes").val());

    if (note_text != "") {
        $.ajax({
            type: 'POST',
            url: BASE_PATH + 'application/ajax/notes.php',
            dataType: 'json',
            data: {
                action: 'add',
                entity_id: add_entity_id,
                notes_type: add_notes_type,
                text: encodeURIComponent(note_text),
                priority: priority
            },
            success: function(response) {
                if (response.success == true) {
                    notes[add_notes_type][add_entity_id] = undefined;
                    $("#notes_" + add_notes_type + "_" + add_entity_id).html(parseInt($("#notes_" + add_notes_type + "_" + add_entity_id).text()) + 1);

                    if (response.showColor == 1) {
                        $("#notes_" + add_notes_type + "_" + add_entity_id).removeClass("note-grey");
                        $("#notes_" + add_notes_type + "_" + add_entity_id).addClass("note-red");
                    }
                }
            }
        });
    }

    $("#kt_modal_4").modal('hide')
    add_busy = false;
}

function selectShipper_1() {
    $("#acc_search_result").html("");
    $("#acc_search_string").val("");
    $("#tabPanel").show();
    $("#shipperPopupTableHeader").show();
    $("#searchHint2").show();
    $("#searchHint1").hide();
    acc_type = 2;
    $("#kt_modal_4").modal()
}

function deleteLogo(url) {
    if (confirm('Are you sure you want to delete?')) {
        $.post(url, {});
        var idObj = $('#logo-file');
        idObj.fadeOut(function() {
            idObj.remove();
        });
    }
    return false;
}

function deleteFile(url, id) {
    if (confirm('Are you sure you want to delete this file?')) {
        $.ajax({
            url: url,
            data: { id: id },
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success == true) {
                    var idObj = $('#file-' + id);
                    idObj.fadeOut(function() {
                        idObj.remove();
                    });
                } else {
                    Swal.fire("Can't delete item.");
                }
            }
        });
    }
    return false;
}

function updateView(id) {
    $.ajax({
        type: "POST",
        url: BASE_PATH + "application/ajax/ajax.php?action=updateView",
        data: "id=" + id,
        dataType: "json",
        success: function(result) {
            if (result.success == true) {
                window.location.reload();
            }
        }
    });
}

function statusText(url, rowId) {
    $.ajax({
        url: url,
        data: {},
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success == true) {
                var obj = $(rowId);
                if (obj.text() == 'Active') {
                    obj.removeClass('status-active').addClass('status-inactive').text('Inactive');
                } else {
                    obj.removeClass('status-inactive').addClass('status-active').text('Active');
                }
            } else {
                Swal.fire("Can't update status.");
            }
        }
    });

    return false;
}

function array_values(input) {
    var tmp_arr = new Array(),
        cnt = 0;
    for (key in input) {
        tmp_arr[cnt] = input[key];
        cnt++;
    }
    return tmp_arr;
}

function selectCarrier() {
    $("#acc_global_search_result").html("");
    $("#acc_search_string").val("");
    acc_type = 1;

    $("#acc_search_dialog").find('.modal-title').html('Select Carrier');
    $("#acc_search_dialog").modal();
}

function selectCarrierNewDispatch() {

    if ($("input[name='order_id']:checked").length == 0) {
        Swal.fire({
            type: 'error',
            title: 'Oops...',
            text: 'Order not selected!',
        })
        return false;
    }
    if ($("input[name='order_id']:checked").length > 1) {
        $(".alert-message").empty();
        $(".alert-message").text("Error: You may dispatch one order at a time.");
        $(".alert-pack").show();
        return false;
    }
    $("#dispatch_dialog .msg-error").hide();
    $("#acc_global_search_result_new_dispatch").html("");
    $("#acc_search_string").val("");
    acc_type = 1;
    checkEditDispatch();
    //$("#acc_search_dialog_new_dispatch").dialog({width: 550},{title:'Select Carrier'}).dialog('open');

}

function selectGlobalCarrier() {
    $("#acc_global_search_result").html("");
    acc_type = 1;
    $("#acc_global_search_dialog").find('modal-title').html('SelectCarrier');
    $("#acc_global_search_dialog").modal();
}

function selectShipper() {
    $("#acc_search_result").html("");
    $("#acc_search_string").val("");
    $("#tabPanel").show();
    $("#shipperPopupTableHeader").show();
    $("#searchHint2").show();
    $("#searchHint1").hide();
    acc_type = 2;
    $("#acc_search_dialog").modal({
        backdrop: 'static'
    });

}

function selectTerminal(location) {

    acc_location = location;
    $("#acc_search_result").html("");
    $("#tabPanel").hide();
    $("#shipperPopupTableHeader").hide();
    $("#acc_search_string").val("");
    $("#searchBar").css("width", "100%");
    acc_type = 3;
    $("#searchHint2").hide();
    $("#searchHint1").show();
    $("#acc_search_dialog").modal({
        backdrop: 'static'
    });
}

var acc_data = null;
var acc_type = null;

function getShipperOrders() {
    $("#shipperOrderQuotesListWrapper").show();
    $("#ushipperheading").html("Active Orders");
    var selectedShipperId = $("input:checkbox[name=selectdShipper]:checked").val();

    $("#orderQuotesList").show();
    $("#shipperData").hide();
    $("#popupLoader").show();
    $.ajax({
        type: 'POST',
        url: BASE_PATH + 'application/ajax/shipper.php',
        dataType: 'json',
        data: {
            action: 'getShipperOrders',
            shipperId: selectedShipperId
        },
        success: function(response) {
            $("#orderQuotesList").html(response.html);
            $("#popupLoader").hide();
        }
    });
}

function getShipperQuotes() {
    $("#shipperOrderQuotesListWrapper").show();
    $("#ushipperheading").html("Active Quotes");
    var selectedShipperId = $("input:checkbox[name=selectdShipper]:checked").val();

    $("#orderQuotesList").show();
    $("#shipperData").hide();
    $("#popupLoader").show();
    $.ajax({
        type: 'POST',
        url: BASE_PATH + 'application/ajax/shipper.php',
        dataType: 'json',
        data: {
            action: 'getShipperQuotes',
            shipperId: selectedShipperId
        },
        success: function(response) {
            if (response == "null" || response == null) {
                $("#popupLoader").hide();
            }
            $("#popupLoader").hide();
            $("#orderQuotesList").html(response.html);

        }
    });
}

function entity_id() {
    var entity_id = $(".replies").html();
    window.location.href = "show/id/" + entity_id;

}

function vehiclePopupHandler(noOfVehicles, entityId, vehicleId) {


    $("#vehiclePopup").modal();
    $(".replies").html(entityId);
    $("#vehicleNewLists").show();
    $("#editVehicleForm").hide();
    /* ajax to Get Vehicle data*/
    $.ajax({
        type: 'POST',
        url: BASE_PATH + 'application/ajax/vehicles.php',
        dataType: 'json',
        data: {
            action: "getVehicleList",
            id: vehicleId,
            entity_id: entityId,
            noOfVehicles: noOfVehicles
        },
        success: function(response) {

            $("#vehicleList").html(response.data);
            $("#netDeposite").html("$" + response.netDeposite);
            $("#netTariff").html("$" + response.netTariff);
            $("#netCarrierPay").html("$" + response.netCarrierPay);
            $("#vehicleEntityId").val(entityId);
            $("#orPrefix").html(response.prefix);
            $("#orNumber").html(response.number);
            $("#shipName").html(response.shipperfname + " " + response.shipperlname);
            $("#shipComp").html(response.shippercompany);
            $("#shipEmail").html(response.shipperemail);
        }
    });
}

function editVehicle(vehicleId, entity_id, row) {
    $("#editVehicleForm").show();
    $("#vehicleNewLists").hide();
    $("#editAddTrigger").html("");
    $("#editAddTrigger").html("Edit Vehicle Information");
    $("#year").val($("#years" + row).html());
    $("#vehicleMake").val($("#make" + row).html());
    $("#vehicleModel").val($("#model" + row).html());
    $("#vehicleType").val($("#vType" + row).html());
    $("#add_vehicle_vin").val($("#vin" + row).html());
    $("#add_vehicle_color").val($("#color" + row).html());
    $("#add_vehicle_plater").val($("#plate" + row).html());
    $("#add_vehicle_state").val($("#state" + row).html());
    $("#add_vehicle_lot").val($("#lot" + row).html());
    $("#add_vehicle_carrier_pay").val($("#tariff" + row).html());
    $("#add_vehicle_deposit").val($("#deposite" + row).html());
    $("#add_vehicle_inop").select($("#inop" + row).html());
    $("#vehicleId").val(vehicleId);
    $("#entityId").val(entity_id);
    $("#addSave").attr("functionality", "save");
    $("#addSave").html("Save Changes");
}

function addVehicle() {

    $("#editVehicleForm").show();
    $("#vehicleNewLists").hide();
    $("#editAddTrigger").html("");
    $("#editAddTrigger").html("Add Vehicle Information");
    $("#year").val("");
    $("#vehicleMake").val("");
    $("#vehicleModel").val("");
    $("#vehicleType").val("");
    $("#add_vehicle_vin").val("");
    $("#add_vehicle_color").val("");
    $("#add_vehicle_plater").val("");
    $("#add_vehicle_state").val("");
    $("#add_vehicle_lot").val("");
    $("#add_vehicle_carrier_pay").val("");
    $("#add_vehicle_deposit").val("");
    $("#add_vehicle_inop").val("");
    $("#vehicleId").val("");
    $("#entityId").val("");
    $("#addSave").attr("functionality", "add");
    $("#addSave").html("Add Vehicle");
}

function saveVehicleChanges() {
    $(".charliesLoaderContent").show();
    $('#vehiclePopup').find(".modal-body").addClass('')

    /* disabling save and close button */
    $(".ui-button .ui-widget .ui-state-default .ui-corner-all .ui-button-text-only").hide();
    var numItems = $('.vehiclePopupRow').length;
    /* Deleteing the vehicles */
    var arrLength = numItems;
    var vehicleIds = [];
    var i = 0;
    var entityId = "";

    $('.vehiclePopupRow').each(function() {
        var rowNumber = this.id[this.id.length - 1];
        vehicleIds.push($("#radio" + rowNumber).val());
        entityId = $("#radio" + rowNumber).attr("entity");
    });

    /* getting all vehicle Ids for this entity id*/
    deleteVehicles(vehicleIds, entityId);

    $('.vehiclePopupRow').each(function() {
        var rowNumber = this.id[this.id.length - 1];

        var vehicleId = $("#radio" + rowNumber).attr("value");
        var entityId = $("#radio" + rowNumber).attr("entity");

        var year = $("#year" + rowNumber).html();
        var model = $("#model" + rowNumber).html();
        var make = $("#make" + rowNumber).html();
        var vType = $("#vType" + rowNumber).html();
        var vin = $("#vin" + rowNumber).html();
        var inop = $("#inop" + rowNumber).html();
        if (inop === "Yes") {
            inop = 1;
        } else {
            inop = 0;
        }
        var tariff = $("#tariff" + rowNumber).html();
        var deposite = $("#deposite" + rowNumber).html();

        /* check existing if exists check for update */
        if (vehicleId === "") {
            addVehicletoDatabase(entityId, rowNumber);
        } else {
            checkExistingVehicle(vehicleId, entityId, rowNumber);
        }

    });

    /* update vehicle count and vehicle data in the order header table */

    setTimeout(function() {
        updateVehicleDataOrderHeader(entityId);
    }, 3000);
}

/* 
 * update vehicle count in app_order_header
 * 
 * @return  void
 */
function updateVehicleDataOrderHeader(entityId) {
    $.ajax({
        type: 'POST',
        url: BASE_PATH + 'application/ajax/vehicles.php',
        dataType: 'json',
        async: false,
        data: {
            action: 'getVehicleCount',
            entity_id: entityId,
        },
        success: function(response) {
            $.ajax({
                type: 'POST',
                url: BASE_PATH + 'application/ajax/vehicles.php',
                dataType: 'json',
                async: false,
                data: {
                    action: 'updateVehicleData',
                    entity_id: entityId,
                    numberOfVehicles: response.count
                },
                success: function(res) {
                    $("progressData").html("Vehicle Count Updated Succesfully! Reloading page!");
                    $(".ui-button .ui-widget .ui-state-default .ui-corner-all .ui-button-text-only").show();
                    setTimeout(function() {

                        $("progressData").html("");
                        location.reload();
                    }, 2000);
                },
                error: function(res) {
                    $("progressData").html("Something went wrong while updating number of Vehicles in Order Header");
                    setTimeout(function() {
                        $("progressData").html("");

                        location.reload();
                    }, 3000);
                }
            });
        },
        error: function(response) {
            $("progressData").html("Something went wrong while getting number of Vehicles in Order Header");
        }
    });
}

/*
 * delete vehicle from database
 * 
 * @return  void
 */
function deleteVehicles(vehicleIds, entityId) {
    /* get all existing vehicle ids for specific entity id*/
    $.ajax({
        type: 'POST',
        url: BASE_PATH + 'application/ajax/vehicles.php',
        dataType: 'json',
        async: false,
        data: {
            action: 'getAllIds',
            entity_id: entityId,
        },
        success: function(response) {
            var deletingIds = [];
            for (var i = 0; i < response.ids.length; i++) {
                //console.log($.inArray( response.ids[i], vehicleIds));
                if ($.inArray(response.ids[i], vehicleIds) === -1) {
                    deleteFromDatabaseVehicleAjax(response.ids[i], entityId);
                }
            }
        },
        error: function(response) {
            $("progressData").html("Something went wrong while deleting Vehicles");
        }
    });
}

/*
 * delete vehicle from database
 * 
 * @return  void
 */
function deleteFromDatabaseVehicleAjax(vehicleId, entity_id) {
    $.ajax({
        type: 'POST',
        async: false,
        url: BASE_PATH + 'application/ajax/vehicles.php',
        dataType: 'json',
        data: {
            action: "del",
            id: vehicleId,
            entity_id: entity_id
        },
        success: function(response) {
            $("progressData").html("Checking vehicle with id " + vehicleId + " to delete or not");
        }
    });
}

/*
 * This method adds a new vehicle in the database
 * 
 * @param {number} entityId
 * @param {number} rowNumber
 * @returns {void}
 */
function addVehicletoDatabase(entityId, rowNumber) {
    $.ajax({
        type: 'POST',
        url: BASE_PATH + 'application/ajax/vehicles.php',
        dataType: 'json',
        async: false,
        data: {
            action: 'add',
            id: 0,
            entity_id: entityId,
            vehicleEntityId: entityId,
            year: $("#year" + rowNumber).html(),
            make: $("#make" + rowNumber).html(),
            model: $("#model" + rowNumber).html(),
            type: $("#vType" + rowNumber).html(),
            color: '',
            plate: '',
            state: '',
            vin: $("#vin" + rowNumber).html(),
            lot: '',
            inop: $("#inop" + rowNumber).html(),
            carrier_pay: $("#tariff" + rowNumber).html(),
            deposit: $("#deposite" + rowNumber).html()
        },
        success: function(response) {
            $("progressData").html("New vehicle added");

        },
        error: function(response) {
            $("progressData").html("Something went wrong while adding new Vehicle");
        }
    });

}

/*
 * This function checks the exisitng vehicles on the basis of entity id
 * 
 * @param {number} vehicleId
 * @param {number} entityId
 * @param {number} rowNumber
 * @returns {void}
 */
function checkExistingVehicle(vehicleId, entityId, rowNumber) {

    $.ajax({
        type: 'POST',
        url: BASE_PATH + 'application/ajax/vehicles.php',
        dataType: 'json',
        async: false,
        data: {
            action: "checkVehicle",
            id: vehicleId,
            entity_id: entityId
        },
        success: function(response) {
            if (Number(response.existance) === 1) {
                updateExistingVehicle(vehicleId, entityId, rowNumber);
            }
        }
    });
}

/*
 * This function updates the existing vehicles on the basis of entity and vehicle id
 * 
 * @param {number} vehicleId
 * @param {number} entityId
 * @param {number} rowNumber
 * @returns {void}
 */
function updateExistingVehicle(vehicleId, entityId, rowNumber) {
    $.ajax({
        type: 'POST',
        url: BASE_PATH + 'application/ajax/vehicles.php',
        dataType: 'json',
        async: false,
        data: {
            action: 'save',
            id: vehicleId,
            entity_id: entityId,
            vehicleEntityId: entityId,
            year: $("#year" + rowNumber).html(),
            make: $("#make" + rowNumber).html(),
            model: $("#model" + rowNumber).html(),
            type: $("#vType" + rowNumber).html(),
            color: '',
            plate: '',
            state: '',
            vin: $("#vin" + rowNumber).html(),
            lot: '',
            inop: $("#inop" + rowNumber).html(),
            carrier_pay: $("#tariff" + rowNumber).html(),
            deposit: $("#deposite" + rowNumber).html()
        },
        success: function(response) {
            $("progressData").html("Vehicle with id " + vehicleId + " updated");
        },
        error: function(response) {
            $("progressData").html("Something went wrong while updating vehicle with id: " + vehicleId);
        }
    });
}

/*
 * Copy the vehicle on the screen temporarily
 * Added vehicle popup functionality
 * 
 * @return  JSON result
 */
function copyOnScreen() {
    var newRow = 0;
    var vehicleId = $("input[name=vehicleId]:checked").val();

    if (vehicleId == undefined) {
        Swal.fire({
                type: 'error',
                title: 'Oops...',
                text: 'Please select any row!.',
            })
            /*Swal.fire("Please select any row!");*/
    } else {
        var entityId = $("input[name=vehicleId]:checked").attr("entity");
        var LastRow = $(".vehiclePopupRow:last").attr("id")[$(".vehiclePopupRow:last").attr("id").length - 1];

        var row = $("input[name=vehicleId]:checked").attr("row");

        newRow = Number(LastRow) + 1;

        var html = "";

        $("#vehicleList").append("<tr class='vehiclePopupRow' id='rowid" + newRow + "'>\n\
    <td align='center'><input id='radio" + newRow + "' row='" + newRow + "' entity='" + entityId + "' name='vehicleId' type='radio' class='vehicleId' value=''></td>\n\
    <td>" + newRow + "</td>\n\
    <td id='year" + newRow + "'>" + $("#year" + row).html() + "</td>\n\
    <td id='model" + newRow + "'>" + $("#model" + row).html() + "</td>\n\
    <td id='make" + newRow + "'>" + $("#make" + row).html() + "</td>\n\
    <td id='vType" + newRow + "'>" + $("#vType" + row).html() + "</td>\n\
    <td id='vin" + newRow + "'>" + $("#vin" + row).html() + "</td>\n\
    <td id='inop" + newRow + "'>" + $("#inop" + row).html() + "</td>\n\
    <td id='tariff" + newRow + "'>" + $("#tariff" + row).html() + "</td>\n\
    <td id='deposite" + newRow + "'>" + $("#deposite" + row).html() + "</td>\n\
    <td align='center'>\n\
        <img onclick='fillEditForm(" + newRow + ")' src='/images/icons/edit.png' title='Edit' alt='Edit' width='16' height='16'>&nbsp;&nbsp;&nbsp;\n\
        <img onclick='deleteOnScreen(" + newRow + ")' src='/images/icons/delete.png' title='Delete' alt='Delete' class='deleteVehicle' width='16' height='16'></td>\n\
    </tr>");
    }

    /* adjust the total tarrif and carrier pay*/
    var netDeposite = $("#netDeposite").html();
    netDeposite = Number(netDeposite.substr(1));

    var netCarrierPay = $("#netCarrierPay").html();
    netCarrierPay = Number(netCarrierPay.substr(1));

    var netTariff = $("#netTariff").html();
    netTariff = Number(netTariff.substr(1));

    var carrierPay = Number($("#tariff" + row).html());
    var deposite = Number($("#deposite" + row).html());
    var tariff = carrierPay + deposite;

    netTariff = netTariff + tariff;
    netDeposite = netDeposite + deposite;
    netCarrierPay = netCarrierPay + carrierPay;

    $("#netTariff").html("$" + netTariff);
    $("#netDeposite").html("$" + netDeposite);
    $("#netCarrierPay").html("$" + netCarrierPay);

}

/*
 * Copy the vehicle on the screen temporarily
 * Added vehicle popup functionality
 * 
 * @return  JSON result
 */
function addeditOnScreen() {

    $("#addSave").attr("functionality", "add");
    $("#addSave").html("Add");

    $("#editVehicleForm").show();
    $("#vehicleNewLists").hide();

    $("#editAddTrigger").html("");
    $("#editAddTrigger").html("Add Vehicle");

    /* gathering values */

    var year = $("#year").val();
    var make = $("#vehicleMake").val();
    var model = $("#vehicleModel").val();
    var type = $("#vehicleType").val();
    var vin = $("#add_vehicle_vin").val();
    var color = $("#add_vehicle_color").val();
    var plate = $("#add_vehicle_plater").val();
    var state = $("#add_vehicle_state").val();
    var lot = $("#add_vehicle_lot").val();
    var carrier = $("#add_vehicle_carrier_pay").val();
    var deposite = $("#add_vehicle_deposit").val();
    var inop = $("#add_vehicle_inop").val();

    if (year == "" || make == "" || model == "" || type == "" || carrier == "" || deposite == "") {
        Swal.fire("Mandiatory Fields are empty");
    } else {

        var vehicleId = $("#vehicleId").val();
        var entityId = $(".vehiclePopupRow:last").attr("entity");
        Swal.fire(entityId);
        var newRow = 0;
        var LastRow = $(".vehiclePopupRow:last").attr("id")[$(".vehiclePopupRow:last").attr("id").length - 1];
        newRow = Number(LastRow) + 1;
        var html = "";
        $("#vehicleList").append("<tr class='vehiclePopupRow' id='rowid" + newRow + "'>\n\
                <td align='center'><input row='" + newRow + "' entity='" + entityId + "' name='vehicleId' type='radio' class='vehicleId' value=''></td>\n\
                <td id='year" + newRow + "'>" + year + "</td>\n\
                <td id='model" + newRow + "'>" + model + "</td>\n\
                <td id='make" + newRow + "'>" + make + "</td>\n\
                <td id='vType" + newRow + "'>" + type + "</td>\n\
                <td id='vin" + newRow + "'>" + vin + "</td>\n\
                <td id='inop" + newRow + "'>" + inop + "</td>\n\
                <td id='tariff" + newRow + "'>" + carrier + "</td>\n\
                <td id='deposite" + newRow + "'>" + deposite + "</td>\n\
                <td align='center'>\n\
                    <img onclick='editVehicle(''," + entityId + "," + newRow + ")' src/images/icons/edit.png' title='Edit' alt='Edit' width='16' height='16'>&nbsp;&nbsp;&nbsp;\n\
                    <img onclick='deleteOnScreen(" + newRow + ")' src='/images/icons/delete.png' title='Delete' alt='Delete' class='deleteVehicle' width='16' height='16'></td>\n\
                </tr>");
    }
}

/*
 * Functionality for copying vehicle in vehicle edit update popup
 *
 * @author  Chetu Inc.
 */
function copyVehicle() {

    var vehicleId = $("input[name=vehicleId]:checked").val();
    var rowNumber = $("input[name=vehicleId]:checked").attr("row");
    var entity = $("input[name=vehicleId]:checked").attr("entity");
    var tariff = $("#tariff" + rowNumber).html();
    var deposite = $("#deposite" + rowNumber).html();

    $.ajax({
        type: 'POST',
        url: BASE_PATH + 'application/ajax/vehicles.php',
        dataType: 'json',
        data: {
            action: "copy",
            id: vehicleId,
            entity_id: entity,
            tariff: tariff,
            deposit: deposite
        },
        success: function(response) {
            /* passing 2 because in this case there will be multiple vehicles always*/
            vehiclePopupHandler(2, entity, vehicleId);
            swal.fire('Vehicle copied Successfully');
            //location.reload(true);
        }
    });
}

/*
 * Functionality to close inner popup in shipper popup
 *
 * @author  Chetu Inc.
 */
function closeInnerPopup() {
    $("#shipperinfo").html("");
    $("#orderInfo").html("");
    $(".loadingMeassage").html("<center><img src='https://thumbs.gfycat.com/ImpoliteLivelyGenet-size_restricted.gif' height='300'></center>");
    $(".ui-dialog-buttonset").show();
    $("#outerPopup").show();
    $("#innerPopup").hide();
}

/*
 * Functionality to handle inner popup in shipper popup
 *
 * @param   int     id which is an account_id
 * @param   String  column which column to order by
 * @param   orderBy ASC / DESC
 * @author  Chetu Inc.
 */
function innerPHandler(id, column, orderBy) {
    var columnName = "";
    var orderByName = "";
    if (column == 1) {
        columnName = "created";
    }

    if (column == 2) {
        columnName = "avail_pickup_date";
    }

    if (orderBy == 2) {
        orderByName = "DESC";
    } else {
        orderByName = "ASC";
    }

    $("#outerPopup").hide();
    $("#innerPopup").show();
    $(".ui-dialog-buttonset").hide();
    $.ajax({
        type: 'POST',
        url: BASE_PATH + 'application/ajax/accounts.php',
        dataType: 'json',
        data: {
            action: 'shiiperInfo',
            shipperID: id,
            columnName: columnName,
            orderBy: orderByName
        },
        success: function(response) {

            if (response.success) {
                if (response.shipperInfo.length != 0) {
                    var ship_data = response.shipperInfo;
                    var order_data = response.orderData;
                    var shipInfo = "";
                    var orderInfo = "";
                    for (i in ship_data) {

                        var shipComp = "";
                        if (ship_data[i].company_name == "") {
                            shipComp = "";
                        } else {
                            shipComp = "<b>" + ship_data[i].company_name + "</b><br>";
                        }

                        shipInfo += ship_data[i].first_name + " " + ship_data[i].last_name + "<br>";
                        shipInfo += shipComp;
                        shipInfo += '<a href="javascript:void(0);" onclick="customPhoneSms(00,00);" style="color:#008ec2">' + ship_data[i].phone1 + "</a><br>";
                        shipInfo += '<a href="mailto:' + ship_data[i].email + '" title="' + ship_data[i].email + '" style="color:#008ec2">' + ship_data[i].email + "</a><br>";
                        shipInfo += '<b>Source:' + ship_data[i].referred_by + '</b>';
                        $("#shipperId").val(ship_data[i].id);
                    }

                    if (order_data.length > 0) {
                        for (i in order_data) {
                            var sV = order_data[i].ship_via;
                            var shipVia = "";
                            if (sV == 1) {
                                shipVia = "Open";
                            } else if (sV == 2) {
                                shipVia = "Enclosed";
                            } else {
                                shipVia = "Driveaway";
                            }
                            var carrierColor = response.orderData[i]['colorValue']['carrier'];
                            var depositColor = response.orderData[i]['colorValue']['deposit'];
                            var totalColor = response.orderData[i]['colorValue']['total'];

                            var createdDate = new Date(order_data[i].ordered);
                            createdDate = createdDate.toLocaleString();

                            if (createdDate == 'Invalid Date') {
                                createdDate = "";
                            }

                            var statusOrder = {
                                1: "My Order",
                                2: "On Hold",
                                3: "Cancelled",
                                4: "Posted",
                                5: "Not Signed",
                                6: "Dispatched",
                                7: "Issues",
                                8: "Picked Up",
                                9: "Delivered"
                            };
                            var orderStatus = statusOrder[order_data[i].status];

                            var B2Bimage = '';
                            var docHint = '';
                            if (order_data[i].docType == 'NOTHING') {
                                B2Bimage = '';
                                docHint = '';
                            } else if (order_data[i].docType == 'B2B') {
                                B2Bimage = '<img src="' + order_data[i].baseUrl + '/images/icons/b2b.png" />';
                                docHint = 'B2B Generated';
                            } else {
                                B2Bimage = '<img src="' + order_data[i].baseUrl + '/images/icons/esign_small.png" />';
                                docHint = 'eSigned Generated';
                            }

                            orderInfo += '<tr id="order_tr_166992" class="grid-body">\n\
                                        <td align="center" class="grid-body-left" bgcolor="#ffffff" width="6%">\n\
                                            \n\<a style="color:#008ec2" href="' + order_data[i].baseUrl + '/application/orders/show/id/' + order_data[i].entityid + '" target="_blank"><div class="order_id" style="color:#008ec2">' + order_data[i].prefix + '-' + order_data[i].number + '</div></a>\n\
                                               <a style="color:#008ec2" href="' + order_data[i].baseUrl + '/application/orders/history/id/' + order_data[i].entityid + '" target="_blank">History</a><br>' + orderStatus + '\n\
                                        </td>\n\
                                        <td valign="top" bgcolor="#ffffff" width="10%">\n\
                                           <a href="' + order_data[i].baseUrl + '/application/orders/getdocs/id/' + order_data[i].uploadId + '" target="_blank">\n\
                                                    <span style="font-weight: bold;" class="hint--bottom hint--rounded hint--bounce hint--success" data-hint="' + docHint + '">\n\
                                                            ' + B2Bimage + '\n\
                                                    </span>\n\
                                            </a>\n\
                                            <br>' + createdDate + '<br>Assigned to:<br> <strong>' + order_data[i].AssignedName + '</strong><br>\n\
                                        </td>\n\
                                                                <td bgcolor="#ffffff" width="13%">\n\
                                                                                                  ' + order_data[i].Vehicleyear + ' ' + order_data[i].Vehiclemake + ' ' + order_data[i].Vehiclemodel + '  <br>\n\
                                            ' + order_data[i].Vehicletype + ' ' + order_data[i].Vehiclevin + '&nbsp;<a href="http://www.google.com/search?tbm=isch&amp;hl=en&amp;q=+' + order_data[i].Vehiclemake + '+' + order_data[i].Vehiclemodel + '" onclick="window.open(this.href); return false;" title="Show It" style="color:#008ec2">[Show It]</a>                        <br>\n\
                                                                                <span style="color:red;weight:bold;">' + shipVia + '</span><br>\n\
                                                                                </td>\n\
                                                        <td bgcolor="#ffffff" width="13%">\n\
                                            <a href="https://maps.google.com/maps?q=' + order_data[i].Origincity + '+' + order_data[i].Originstate + '+' + order_data[i].Originzip + '" target="_blank"><span class="like-link">' + order_data[i].Origincity + ', ' + order_data[i].Originstate + ' ' + order_data[i].Originzip + '</span></a> /<br>\n\
                                            <a href="https://maps.google.com/maps?q=' + order_data[i].Destinationcity + '+' + order_data[i].Destinationstate + '+' + order_data[i].Destinationzip + '" target="_blank"><span class="like-link">' + order_data[i].Destinationcity + ' ' + order_data[i].Destinationstate + ', ' + order_data[i].Destinationzip + '</span><br>\n\
                                                            <span class="like-link" onclick="mapIt();">Map it</span>\n\
                                                            </td>\n\
                                                            <td valign="top" align="center" bgcolor="#ffffff" width="7%">\n\
                                                            ' + order_data[i].avail_pickup_date + '                       </td>\n\
                                                                <td valign="top" align="center" bgcolor="#ffffff" width="7%">\n\
                                                                       ' + order_data[i].delivery_date + ' </td>\n\
                                            <td class="grid-body-right" bgcolor="#ffffff" width="10%">\n\
                                            <table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">\n\
                                                <tbody><tr>\n\
                                                    <td width="10">\n\
                                                        <span style="font-weight: bold;" class="hint--left hint--rounded hint--bounce" data-hint="Total Cost">\n\
                                                            <img src="/images/icons/dollar.png" width="16" height="16">\n\
                                                        </span>\n\
                                                    </td>\n\
                                                    <td style="white-space: nowrap;">\n\
                                                        <span style="font-weight: bold;" class="hint--right hint--rounded hint--bounce" data-hint="Total Cost">\n\
                                                            <span class="' + totalColor + '">$ ' + order_data[i].total_tariff + '</span>\n\
                                                        </span>\n\
                                                    </td>\n\
                                                </tr>\n\
                                                <tr>\n\
                                                    <td width="10">\n\
                                                        <span style="font-weight: bold;" class="hint--left hint--rounded hint--bounce" data-hint="Carrier Information">\n\
                                                            <img src="/images/icons/truck.png" alt="" title="" width="16" height="16" onclick="">\n\
                                                        </span>\n\
                                                    </td>\n\
                                                    <td style="white-space: nowrap;">\n\
                                                        <span style="font-weight: bold;" class="hint--right hint--rounded hint--bounce" data-hint="Carrier Fee">\n\
                                                            <span class="' + carrierColor + '">\n\
                                                                $ ' + order_data[i].total_carrier_pay + '\
                                                            </span>\n\
                                                        </span>\n\
                                                        <br>\n\
                                                    </td>\n\
                                                </tr>\n\
                                                <tr>\n\
                                                    <td width="10">\n\
                                                        <span style="font-weight: bold;" class="hint--left hint--rounded hint--bounce" data-hint="Broker Fee">\n\
                                                            <img src="/images/icons/person.png" width="16" height="16">\n\
                                                        </span>\n\
                                                    </td>\n\
                                                    <td style="white-space: nowrap;">\n\
                                                        <span style="font-weight: bold;" class="hint--right hint--rounded hint--bounce" data-hint="Broker Fee">\n\
                                                            <span class="' + depositColor + '">\n\
                                                                $ ' + order_data[i].total_deposite + '\
                                                            </span>\n\
                                                        </span>\n\
                                                    </td>\n\
                                                </tr>\n\
                                            </tbody></table>\n\
                                        </td>\n\
                                    </tr>';
                        }
                    }
                }
            } else {
                orderInfo = "<td colspan='7'><center><br>Sorry, No related order for this Shipper.<br></center></td>";
            }
            $("#shipperinfo").html(shipInfo);
            $("#orderInfo").html(orderInfo);
            $(".loadingMeassage").html("");
        }
    });
}

/*
 * Functionality to format the phone number as per US formatting
 *
 * @param   String     s which is a phone number unformatted
 * @author  Chetu Inc.
 */
function formatPhoneNumber(s) {

    var s2 = ("" + s).replace(/\D/g, '');
    var m = s2.match(/^(\d{3})(\d{3})(\d{4})$/);

    return (!m) ? null : "" + m[1] + "-" + m[2] + "-" + m[3];
}

function accountSearch() {

    $(".search_help").hide();
    $("#acc_search_result_leads").html("");
    $("#acc_search_result").html('');
    var searchText = $("#acc_search_string").val();

    if (searchText == '' || searchText.length < 2) {
        Swal.fire("Please enter minimum 2 Characters");
        return;
    }

    Processing_show();

    $('.blockPage').css('z-index', '1053');
    $('.blockOverlay').css('z-index', '1052');

    if (acc_type == 2) {
        var action = 'searchShipper';
    } else {
        var action = 'search';
    }

    $.ajax({
        type: 'POST',
        url: BASE_PATH + 'application/ajax/accounts.php',
        dataType: 'json',
        data: {
            action: action,
            text: searchText,
            type: acc_type
        },
        success: function(response) {
            console.log("Shipper search result");
            console.log(response);
            if (response.success) {
                acc_data = response.data;
                if (response.data.length != 0) {

                    // for search shipper
                    if (acc_type == 2) {
                        for (i in acc_data.shipper_leads_data) {
                            if (acc_data.shipper_leads_data[i].shippershipment_type == 2) {
                                var shipperType = "Singles";
                            } else if (acc_data.shipper_leads_data[i].shippershipment_type == 1) {
                                var shipperType = "Full Load";
                            } else {
                                var shipperType = "Both";
                            }

                            var assignedDate = new Date(acc_data.shipper_leads_data[i].assigned_date);
                            assignedDate = assignedDate.toLocaleString();
                            if (assignedDate == 'Invalid Date') {
                                assignedDate = "";
                            }

                            var lastActivityDate = new Date(acc_data.shipper_leads_data[i].last_activity_date);
                            lastActivityDate = lastActivityDate.toLocaleString();
                            if (lastActivityDate == 'Invalid Date') {
                                lastActivityDate = "";
                            }

                            var version = new Date(acc_data.shipper_leads_data[i].version);
                            version = version.toLocaleString();
                            if (version == 'Invalid Date') {
                                version = "";
                            }

                            var leadCom = "";
                            if (acc_data.shipper_leads_data[i].company == "") {
                                leadCom = "";
                            } else {
                                leadCom = '<div class="shipper_company"><b>' + acc_data.shipper_leads_data[i].company + '</b><br></div>';
                            }

                            AccRowsLeads = "";
                            AccRowsLeads += '<tr>\n\
                                        <td>\n\
                                               <input type="radio" name="acc_search_result_item_leads" class="form-box-radio radioLeads" id="acc_search_result_item_leads' + i + '" value="' + i + '"/>\n\
                                        </td>\n\
                                        <td>\n\
                                               <a href="/application/leads/show/id/' + acc_data.shipper_leads_data[i].entityid + '" target="_blank" style="color:#008ec2">' + acc_data.shipper_leads_data[i].number + '</a><br><a style="color:#008ec2" href="/application/orders/history/id/' + acc_data.shipper_leads_data[i].entityid + ' target="_blank">History</a>\n\
                                        </td>\n\
                                        <td>\n\
                                               ' + assignedDate + '<br>Assigned to:<br><b>' + acc_data.shipper_leads_data[i].AssignedName + '</b>\n\
                                        </td>\n\
                                        <td>\n\
                                                <div class="shipper_name">' + acc_data.shipper_leads_data[i].fName + ' ' + acc_data.shipper_leads_data[i].lName + '<br></div>\n\
                                                ' + leadCom + '\n\
                                                <div class="shipper_number">\n\
                                                        \n\
                                                        <b>' + acc_data.shipper_leads_data[i].state + '-' + acc_data.shipper_leads_data[i].country + '</b><br></div>\n\
                                                        <a style="color:#008ec2" href="mailto:' + acc_data.shipper_leads_data[i].email + '" title="00"><div class="shipper_email" style="color:#008ec2">' + acc_data.shipper_leads_data[i].email + '<br></div></a>\n\
                                                        <div class="shipper_referred">Source: <b>' + acc_data.shipper_leads_data[i].referredBy + '</b><br>\n\
                                                </div>\n\
                                        </td>\n\
                                        <td>\n\
                                                ' + shipperType + '\n\
                                        </td>\n\
                                        <td>' + acc_data.shipper_leads_data[i].accountContract + '<br>' + version + '<br>' + acc_data.shipper_leads_data[i].contractExpiry + '</td>\n\
                                        <td>\n\
                                            ' + lastActivityDate + '\n\
                                        </td>\n\
                                </tr>';
                            $("#acc_search_result_leads").append(AccRowsLeads);

                            setTimeout(function() {
                                $("#select_shipper_leads").DataTable();
                            })
                        }

                        for (j in acc_data.shipper_data) {

                            var assignedName = "";
                            var lastOrderDate = "";
                            var prefix = "";
                            var numberOrder = "";
                            var totalAmount = "";
                            var pendingAmount = "";
                            var shipperID = "";

                            if (acc_data.shipper_data[j].assignedName === undefined || acc_data.shipper_data[j].assignedName == null) {
                                assignedName = "NOT ASSIGNED";
                            } else {
                                assignedName = acc_data.shipper_data[j].assignedName;
                            }
                            if (acc_data.shipper_data[j].created === undefined || acc_data.shipper_data[j].created == null) {
                                lastOrderDate = "NO ORDER DATE";
                            } else {
                                lastOrderDate = acc_data.shipper_data[j].created;
                            }

                            if (acc_data.shipper_data[j].prefix === undefined || acc_data.shipper_data[j].prefix == null) {
                                prefix = "NO";
                            } else {
                                prefix = acc_data.shipper_data[j].prefix
                            }

                            if (acc_data.shipper_data[j].number === undefined || acc_data.shipper_data[j].number == null) {
                                numberOrder = "ORDER";
                            } else {
                                numberOrder = acc_data.shipper_data[j].number;
                            }

                            if (acc_data.shipper_data[j].totalAmount === undefined || acc_data.shipper_data[j].totalAmount == null) {
                                totalAmount = "0.00";
                            } else {
                                totalAmount = acc_data.shipper_data[j].totalAmount;
                                totalAmount = number_format(totalAmount, 2, ".", "");
                            }

                            if (acc_data.shipper_data[j].pendingAmount === undefined || acc_data.shipper_data[j].pendingAmount == null || acc_data.shipper_data[j].pendingAmount == 0) {
                                pendingAmount = "0.00";
                            } else {
                                pendingAmount = acc_data.shipper_data[j].pendingAmount;
                                pendingAmount = number_format(pendingAmount, 2, ".", "");
                            }

                            if (acc_data.shipper_data[j].shipperId === undefined || acc_data.shipper_data[j].shipperId == null) {
                                shipperID = "null";
                            } else {
                                shipperID = acc_data.shipper_data[j].shipperId;
                            }

                            var assignedDate;
                            if (acc_data.shipper_data[j].assignedDate === undefined) {
                                assignedDate = "";
                            } else {
                                assignedDate = new Date(acc_data.shipper_data[j].assignedDate);
                                assignedDate = assignedDate.toLocaleString();
                                if (assignedDate == 'N/A') {
                                    assignedDate = "";
                                }
                            }

                            var version = "";
                            if (acc_data.shipper_data[j].version === undefined) {
                                version = "";
                            } else {
                                version = new Date(acc_data.shipper_data[j].version);
                                version = version.toLocaleString();
                                if (version == 'N/A') {
                                    version = "";
                                }
                            }

                            if (acc_data.shipper_data[j].contractExpiry === undefined) {
                                acc_data.shipper_data[j].contractExpiry = "";
                            }

                            if (acc_data.shipper_data[j].accountContract === undefined) {
                                acc_data.shipper_data[j].accountContract = "N/A";
                            }

                            var ShipperCompanyName = "";
                            if (acc_data.shipper_data[j].company_name == "") {
                                ShipperCompanyName = "";
                            } else {
                                ShipperCompanyName = '<span class="shipper_name"><b>' + acc_data.shipper_data[j].company_name + '<b><br></span>';
                            }

                            var accessOrder = "";
                            var accessInnerPopup = "";
                            var lastOrderId = "";

                            lastOrderDate = new Date(lastOrderDate);
                            lastOrderDate = lastOrderDate.toLocaleString();
                            if (lastOrderDate == 'Invalid Date') {
                                lastOrderDate = "NO ORDER DATE";
                                lastOrderId = 'N/A';
                            } else {
                                lastOrderId = '<a style="color:#008ec2" href="/application/orders/show/id/' + acc_data.shipper_data[j].orderEntityId + '" target="_blank"><div class="order_id" style="color:#008ec2">' + prefix + '-' + numberOrder + '</div></a>';
                            }

                            if (acc_data.shipper_data[j].accessOrder == "haveAccess") {
                                accessOrder = lastOrderId;
                                accessInnerPopup = '<img onclick="innerPHandler(' + shipperID + ',1,2)" src="/images/icons/info.png" title="Info" alt="Info" width="16" height="16">';
                            } else {
                                if (lastOrderId == 'NO ORDER ID') {
                                    accessOrder = 'NO ORDER ID';
                                } else {
                                    accessOrder = '<span style="font-weight: bold;" class="hint--left hint--rounded hint--bounce hint--error" data-hint="Access Denied">' + prefix + '-' + numberOrder + '</span>';
                                }
                                accessInnerPopup = '<span style="font-weight: bold;" class="hint--left hint--rounded hint--bounce hint--error" data-hint="Access Denied"><img src="/images/icons/info.png" title="Info" alt="Info" width="16" height="16"></span>';
                            }


                            AccRows = '';
                            AccRows += '<tr>';
                            AccRows += '<td><input type="radio" name="acc_search_result_item" class="form-box-radio" id="acc_search_result_item' + j + '" value="' + j + '"/></td>';
                            AccRows += '<td>' + assignedName + '</td>';
                            AccRows += '<td>\n\
                                    <div class="shipper_name">' + acc_data.shipper_data[j].first_name + ' ' + acc_data.shipper_data[j].last_name + '<br></div>\n\
                                    ' + ShipperCompanyName + '\n\
                                    <div class="shipper_number"><a href="javascript:void(0);" onclick="customPhoneSms(00,00);" style="color:#008ec2">' + formatPhoneNumber(acc_data.shipper_data[j].phone1) + '</a>\n\
                                    </div>\n\
                                    <a href="mailto:' + acc_data.shipper_data[j].email + '" title="' + acc_data.shipper_data[j].email + '">\n\
                                        <div class="shipper_email" style="color:#008ec2">' + acc_data.shipper_data[j].email + '<br>\n\
                                        </div>\n\
                                    </a>\n\
                                    <div class="shipper_referred">Source: <b>' + acc_data.shipper_data[j].referred_by + '</b><br></div>\n\
                                </td>';

                            AccRows += '<td><span>' + acc_data.shipper_data[j].address1 + '<br>' + acc_data.shipper_data[j].zip_code + '</span></td>';
                            AccRows += '<td><br><br>' + lastOrderDate + '<br>' + accessOrder + '</td>';
                            AccRows += '<td>' + acc_data.shipper_data[j].accountContract + '<br>' + version + '<br>' + acc_data.shipper_data[j].contractExpiry + '</td>\n\
                                        <td class="text-center">\n\
                                        ' + accessInnerPopup + '\n\
                                        <table>\n\
                                            <tr><td><span class="black">Total:</span></td><td><span class="black">$' + totalAmount + '</span></td></tr>\n\
                                            <tr><td><span class="green">Credit:</span></td><td><span class="green">$0.00</span></tr>\n\
                                            <tr><td><span class="red">Used: <span></td><td><span class="red">$' + pendingAmount + '</span></tr>\n\
                                        </table>\n\
                                        </td>';
                            AccRows += '</tr>';
                            $("#acc_search_result").append(AccRows);

                            setTimeout(function() {
                                $("#select_shipper").DataTable();
                            })

                        }
                    }

                    // for search location
                    if (acc_type == 3 || acc_type == 1) {
                        var bgColor = '  ';
                        var AccRows = '<thead><tr><th ' + bgColor + '></th>';
                        if (acc_type == 2)
                            AccRows += '<th ' + bgColor + '>Name</th>';

                        AccRows += '<th ' + bgColor + '>Company</th>';

                        if (acc_type == 3) {
                            AccRows += '<th ' + bgColor + '>Contact Name</th><th ' + bgColor + '>Address</th><th ' + bgColor + '>Phone</th><th ' + bgColor + '>City</th>';
                        } else {
                            AccRows += '<th ' + bgColor + '>Address</th><th ' + bgColor + '>Phone</th><th ' + bgColor + '>Email</th>';
                        }
                        AccRows += '<th ' + bgColor + '>State</th></tr> </thead>';

                        console.log(acc_data);

                        for (i in acc_data) {
                            var bgColorRows = '';
                            if (acc_data[i].donot_dispatch == 1)
                                bgColorRows = ' bgcolor="#ff6600" ';

                            var cssClass = acc_data[i].expired ? "expired" : "";





                            AccRows += '<tr>\n\
                            <td ' + bgColorRows + '>\n\
                                <label class="kt-radio kt-radio--solid kt-radio--brand"> <input type="radio" name="acc_search_result_item" class="form-box-radio" id="acc_search_result_item' + i + '" value="' + i + '"/> <span></span> </label> <br><br>' + acc_data[i].id + '\n\
                            </td>\n\
                            <td ' + bgColorRows + '>';

                            if (acc_type == 2) {
                                if (acc_data[i].first_name != '')
                                    AccRows += acc_data[i].first_name;
                                if (acc_data[i].last_name != '')
                                    AccRows += " " + acc_data[i].last_name;

                                AccRows += '</td><td ' + bgColorRows + '>';
                            }

                            if (acc_data[i].company_name != '')
                                AccRows += acc_data[i].company_name;

                            AccRows += '</td>';
                            if (acc_type == 3) {
                                AccRows += "<td ' + bgColorRows + '>" + acc_data[i].contact_name1 + "</td>";
                            }
                            AccRows += '<td ' + bgColorRows + '>';

                            if (acc_data[i].address1 != '')
                                AccRows += acc_data[i].address1;

                            AccRows += '</td>\n\
                            <td ' + bgColorRows + '>';

                            if (acc_data[i].phone1 != '')
                                AccRows += acc_data[i].phone1;

                            AccRows += '</td>\n\
                            <td ' + bgColorRows + '>';

                            if (acc_type == 3) {
                                if (acc_data[i].city != '')
                                    AccRows += acc_data[i].city;
                            } else {
                                if (acc_data[i].email != '')
                                    AccRows += '<a href="javascript:void(0);" alt="' + acc_data[i].email + '" title="' + acc_data[i].email + '">View</a>';
                                //AccRows +=acc_data[i].email;
                            }

                            AccRows += '</td>\n\
                            <td ' + bgColorRows + '>';

                            if (acc_data[i].state != '')
                                AccRows += acc_data[i].state;

                            AccRows += '</td>\n\
                            </tr>';
                        }
                        var typeName = "";
                        if (acc_type == 1)
                            typeName = "Carrier";
                        else if (acc_type == 2)
                            typeName = "Shipper";
                        else if (acc_type == 3)
                            typeName = "Location";


                        $("#acc_search_result").html('<table id="sipper_search_result" class="table table-bordered" >' + AccRows + '</table><br><b style="color:red; ">Highlighted ' + typeName + ' is not allowed.</b><br>');

                        setTimeout(function() {
                            $("#sipper_search_result").DataTable();
                        })
                    }

                } else {
                    $("#acc_search_result").append('No records found.');
                }
            } else {

                Swal.fire("Can't load account data. Try again later, please");
            }
        },
        error: function(response) {
            swal.fire("Can't load account data. Try again later, please");
        },
        complete: function(response) {
            KTApp.unblockPage();
        }
    });
}

function accountSearch_1() {

    $(".search_help").hide();
    $("#acc_search_result_leads").html("");
    $("#acc_search_result").html('');

    var searchText = $.trim($("#acc_search_string").val());
    if (searchText == '' || searchText.length < 2) {

        return;
    }
    $(".acc_search_dialog").nimbleLoader('show');

    if (acc_type == 2) {
        var action = 'searchShipper';
    } else {
        var action = 'search';
    }

    $.ajax({
        type: 'POST',
        url: BASE_PATH + 'application/ajax/accounts.php',
        dataType: 'json',
        data: {
            action: action,
            text: searchText,
            type: acc_type
        },
        success: function(response) {
            if (response.success) {
                acc_data = response.data;
                if (response.data.length != 0) {
                    if (acc_type == 2) {
                        for (i in acc_data.shipper_leads_data) {
                            if (acc_data.shipper_leads_data[i].shippershipment_type == 2) {
                                var shipperType = "Singles";
                            } else if (acc_data.shipper_leads_data[i].shippershipment_type == 1) {
                                var shipperType = "Full Load";
                            } else {
                                var shipperType = "Both";
                            }

                            var assignedDate = new Date(acc_data.shipper_leads_data[i].assigned_date);
                            assignedDate = assignedDate.toLocaleString();
                            if (assignedDate == 'Invalid Date') {
                                assignedDate = "";
                            }

                            var lastActivityDate = new Date(acc_data.shipper_leads_data[i].last_activity_date);
                            lastActivityDate = lastActivityDate.toLocaleString();
                            if (lastActivityDate == 'Invalid Date') {
                                lastActivityDate = "";
                            }

                            var version = new Date(acc_data.shipper_leads_data[i].version);
                            version = version.toLocaleString();
                            if (version == 'Invalid Date') {
                                version = "";
                            }

                            var leadCom = "";
                            if (acc_data.shipper_leads_data[i].company == "") {
                                leadCom = "";
                            } else {
                                leadCom = '<div class="shipper_company"><b>' + acc_data.shipper_leads_data[i].company + '</b><br></div>';
                            }

                            AccRowsLeads = "";
                            AccRowsLeads += '<tr class="grid-body">\n\
                                        <td bgcolor="#ffffff" width="4%" align="center">\n\
                                               <input type="radio" name="acc_search_result_item_leads" class="form-box-radio radioLeads" id="acc_search_result_item_leads' + i + '" value="' + i + '"/>\n\
                                        </td>\n\
                                        <td bgcolor="#ffffff" width="4%">\n\
                                               <a href="/application/leads/show/id/' + acc_data.shipper_leads_data[i].entityid + '" target="_blank" style="color:#008ec2">' + acc_data.shipper_leads_data[i].number + '</a><br><a style="color:#008ec2" href="/application/orders/history/id/' + acc_data.shipper_leads_data[i].entityid + ' target="_blank">History</a>\n\
                                        </td>\n\
                                        <td bgcolor="#ffffff" width="16%">\n\
                                               ' + assignedDate + '<br>Assigned to:<br><b>' + acc_data.shipper_leads_data[i].AssignedName + '</b>\n\
                                        </td>\n\
                                        <td bgcolor="#ffffff" width="13%">\n\
                                                <div class="shipper_name">' + acc_data.shipper_leads_data[i].fName + ' ' + acc_data.shipper_leads_data[i].lName + '<br></div>\n\
                                                ' + leadCom + '\n\
                                                <div class="shipper_number">\n\
                                                        \n\
                                                        <b>' + acc_data.shipper_leads_data[i].state + '-' + acc_data.shipper_leads_data[i].country + '</b><br></div>\n\
                                                        <a style="color:#008ec2" href="mailto:' + acc_data.shipper_leads_data[i].email + '" title="00"><div class="shipper_email" style="color:#008ec2">' + acc_data.shipper_leads_data[i].email + '<br></div></a>\n\
                                                        <div class="shipper_referred">Source: <b>' + acc_data.shipper_leads_data[i].referredBy + '</b><br>\n\
                                                </div>\n\
                                        </td>\n\
                                        <td valign="top" align="center" bgcolor="#ffffff">\n\
                                                ' + shipperType + '\n\
                                        </td>\n\
                                        <td align="center">' + acc_data.shipper_leads_data[i].accountContract + '<br>' + version + '<br>' + acc_data.shipper_leads_data[i].contractExpiry + '</td>\n\
                                        <td valign="top" align="center" bgcolor="#ffffff" width="7%">\n\
                                            ' + lastActivityDate + '\n\
                                        </td>\n\
                                </tr>';
                            $("#acc_search_result_leads").append(AccRowsLeads);
                        }
                        for (j in acc_data.shipper_data) {

                            //console.log(acc_data.shipper_data[j].id);
                            var assignedName = "";
                            var lastOrderDate = "";
                            var prefix = "";
                            var numberOrder = "";
                            var totalAmount = "";
                            var pendingAmount = "";
                            var shipperID = "";

                            if (acc_data.shipper_data[j].assignedName === undefined || acc_data.shipper_data[j].assignedName == null) {
                                assignedName = "NOT ASSIGNED";
                            } else {
                                assignedName = acc_data.shipper_data[j].assignedName;
                            }
                            if (acc_data.shipper_data[j].created === undefined || acc_data.shipper_data[j].created == null) {
                                lastOrderDate = "NO ORDER DATE";
                            } else {
                                lastOrderDate = acc_data.shipper_data[j].created;
                            }

                            if (acc_data.shipper_data[j].prefix === undefined || acc_data.shipper_data[j].prefix == null) {
                                prefix = "NO";
                            } else {
                                prefix = acc_data.shipper_data[j].prefix
                            }

                            if (acc_data.shipper_data[j].number === undefined || acc_data.shipper_data[j].number == null) {
                                numberOrder = "ORDER";
                            } else {
                                numberOrder = acc_data.shipper_data[j].number;
                            }

                            if (acc_data.shipper_data[j].totalAmount === undefined || acc_data.shipper_data[j].totalAmount == null) {
                                totalAmount = "0.00";
                            } else {
                                totalAmount = acc_data.shipper_data[j].totalAmount;
                                totalAmount = number_format(totalAmount, 2, ".", "");
                            }

                            if (acc_data.shipper_data[j].pendingAmount === undefined || acc_data.shipper_data[j].pendingAmount == null || acc_data.shipper_data[j].pendingAmount == 0) {
                                pendingAmount = "0.00";
                            } else {
                                pendingAmount = acc_data.shipper_data[j].pendingAmount;
                                pendingAmount = number_format(pendingAmount, 2, ".", "");
                            }

                            if (acc_data.shipper_data[j].shipperId === undefined || acc_data.shipper_data[j].shipperId == null) {
                                shipperID = "null";
                            } else {
                                shipperID = acc_data.shipper_data[j].shipperId;
                            }

                            var assignedDate;
                            if (acc_data.shipper_data[j].assignedDate === undefined) {
                                assignedDate = "";
                            } else {
                                assignedDate = new Date(acc_data.shipper_data[j].assignedDate);
                                assignedDate = assignedDate.toLocaleString();
                                if (assignedDate == 'Invalid Date') {
                                    assignedDate = "";
                                }
                            }

                            var version = "";
                            if (acc_data.shipper_data[j].version === undefined) {
                                version = "";
                            } else {
                                version = new Date(acc_data.shipper_data[j].version);
                                version = version.toLocaleString();
                                if (version == 'Invalid Date') {
                                    version = "";
                                }
                            }

                            if (acc_data.shipper_data[j].contractExpiry === undefined) {
                                acc_data.shipper_data[j].contractExpiry = "";
                            }

                            if (acc_data.shipper_data[j].accountContract === undefined) {
                                acc_data.shipper_data[j].accountContract = "No Contract";
                            }

                            var ShipperCompanyName = "";
                            if (acc_data.shipper_data[j].company_name == "") {
                                ShipperCompanyName = "";
                            } else {
                                ShipperCompanyName = '<div class="shipper_name"><b>' + acc_data.shipper_data[j].company_name + '<b><br></div>';
                            }

                            var accessOrder = "";
                            var accessInnerPopup = "";
                            var lastOrderId = "";

                            lastOrderDate = new Date(lastOrderDate);
                            lastOrderDate = lastOrderDate.toLocaleString();
                            if (lastOrderDate == 'Invalid Date') {
                                lastOrderDate = "NO ORDER DATE";
                                lastOrderId = 'NO ORDER ID';
                            } else {
                                lastOrderId = '<a style="color:#008ec2" href="/application/orders/show/id/' + acc_data.shipper_data[j].orderEntityId + '" target="_blank"><div class="order_id" style="color:#008ec2">' + prefix + '-' + numberOrder + '</div></a>';
                            }

                            if (acc_data.shipper_data[j].accessOrder == "haveAccess") {
                                accessOrder = lastOrderId;
                                accessInnerPopup = '<img onclick="innerPHandler(' + shipperID + ',1,2)" src="/images/icons/info.png" title="Info" alt="Info" width="16" height="16">';
                            } else {
                                if (lastOrderId == 'NO ORDER ID') {
                                    accessOrder = 'NO ORDER ID';
                                } else {
                                    accessOrder = '<span style="font-weight: bold;" class="hint--left hint--rounded hint--bounce hint--error" data-hint="Access Denied">' + prefix + '-' + numberOrder + '</span>';
                                }
                                accessInnerPopup = '<span style="font-weight: bold;" class="hint--left hint--rounded hint--bounce hint--error" data-hint="Access Denied"><img src="/images/icons/info.png" title="Info" alt="Info" width="16" height="16"></span>';
                            }


                            AccRows = '';
                            AccRows += '<tr class="grid-body">';
                            AccRows += '<td align="center"><input type="radio" name="acc_search_result_item" class="form-box-radio" id="acc_search_result_item' + j + '" value="' + j + '"/></td>';
                            AccRows += '<td valign="top" bgcolor="#ffffff" width="10%"><br>' + assignedName + '</td>';
                            AccRows += '<td bgcolor="#ffffff">\n\
                                    <div class="shipper_name">' + acc_data.shipper_data[j].first_name + ' ' + acc_data.shipper_data[j].last_name + '<br></div>\n\
                                    ' + ShipperCompanyName + '\n\
                                    <div class="shipper_number"><a href="javascript:void(0);" onclick="customPhoneSms(00,00);" style="color:#008ec2">' + formatPhoneNumber(acc_data.shipper_data[j].phone1) + '</a>\n\
                                    </div>\n\
                                    <a href="mailto:' + acc_data.shipper_data[j].email + '" title="' + acc_data.shipper_data[j].email + '">\n\
                                        <div class="shipper_email" style="color:#008ec2">' + acc_data.shipper_data[j].email + '<br>\n\
                                        </div>\n\
                                    </a>\n\
                                    <div class="shipper_referred">Source: <b>' + acc_data.shipper_data[j].referred_by + '</b><br></div>\n\
                                </td>';

                            AccRows += '<td bgcolor="#ffffff"><span>' + acc_data.shipper_data[j].address1 + '<br>' + acc_data.shipper_data[j].zip_code + '</span></td>';
                            AccRows += '<td align="center" bgcolor="#ffffff"><br><br>' + lastOrderDate + '<br>' + accessOrder + '</td>';
                            AccRows += '<td align="center">' + acc_data.shipper_data[j].accountContract + '<br>' + version + '<br>' + acc_data.shipper_data[j].contractExpiry + '</td>\n\
                                        <td class="grid-body-right" bgcolor="#ffffff" width="10%">\n\
                                        <table width="100%" style="border:none;">\n\
                                            <tr>\n\
                                                <td align="right" style="border:0px solid red;">\n\
                                                    <table style="border:none;">\n\
                                                        <tr><td align="right" style="border:0px solid red;"><span class="black">Total:</span></td><td style="border:0px solid red;" align="right"><span class="black">$' + totalAmount + '</span></td></tr>\n\
                                                        <tr><td align="right" style="border:0px solid red;"><span class="green">Credit:</span></td><td style="border:0px solid red;" align="right"><span class="green">$0.00</span></tr>\n\
                                                        <tr><td align="right" style="border:0px solid red;"><span class="red">Used: <span></td><td style="border:0px solid red;" align="right"><span class="red">$' + pendingAmount + '</span></tr>\n\
                                                    </table>\n\
                                                </td>\n\
                                                <td style="border:0px solid red;" align="left">\n\
                                                    ' + accessInnerPopup + '\n\
                                                </td>\n\
                                            </tr>\n\
                                        </table>\n\
                                        </td>';
                            AccRows += '</tr>';
                            $("#acc_search_result").append(AccRows);

                        }
                    }
                    if (acc_type == 3 || acc_type == 1) {
                        var bgColor = ' bgcolor="#cccccc" ';
                        var AccRows = '<thead><tr><th ' + bgColor + '></th>';
                        if (acc_type == 2)
                            AccRows += '<th ' + bgColor + '>Name</th>';

                        AccRows += '<th ' + bgColor + '>Company</th>';

                        if (acc_type == 3) {
                            AccRows += '<th ' + bgColor + '>Contact Name</th><th ' + bgColor + '>Address</th><th ' + bgColor + '>Phone</th><th ' + bgColor + '>City</th>';
                        } else {
                            AccRows += '<th ' + bgColor + '>Address</th><th ' + bgColor + '>Phone</th><th ' + bgColor + '>Email</th>';
                        }
                        AccRows += '<th ' + bgColor + '>State</th></tr></thead>';
                        for (i in acc_data) {
                            var bgColorRows = '';
                            if (acc_data[i].donot_dispatch == 1)
                                bgColorRows = ' bgcolor="#ff6600" ';

                            var cssClass = acc_data[i].expired ? "expired" : "";

                            AccRows += '<tr>\n\
                            <td ' + bgColorRows + '>\n\
                                <input type="radio" name="acc_search_result_item" class="form-box-radio" id="acc_search_result_item' + i + '" value="' + i + '"/><br>' + acc_data[i].id + '\n\
                            </td>\n\
                            <td ' + bgColorRows + '>';

                            if (acc_type == 2) {
                                if (acc_data[i].first_name != '')
                                    AccRows += acc_data[i].first_name;
                                if (acc_data[i].last_name != '')
                                    AccRows += " " + acc_data[i].last_name;

                                AccRows += '</td><td ' + bgColorRows + '>';
                            }

                            if (acc_data[i].company_name != '')
                                AccRows += acc_data[i].company_name;

                            AccRows += '</td>';
                            if (acc_type == 3) {
                                AccRows += "<td ' + bgColorRows + '>" + acc_data[i].contact_name1 + "</td>";
                            }
                            AccRows += '<td ' + bgColorRows + '>';

                            if (acc_data[i].address1 != '')
                                AccRows += acc_data[i].address1;

                            AccRows += '</td>\n\
                            <td ' + bgColorRows + '>';

                            if (acc_data[i].phone1 != '')
                                AccRows += acc_data[i].phone1;

                            AccRows += '</td>\n\
                            <td ' + bgColorRows + '>';

                            if (acc_type == 3) {
                                if (acc_data[i].city != '')
                                    AccRows += acc_data[i].city;
                            } else {
                                if (acc_data[i].email != '')
                                    AccRows += '<a href="javascript:void(0);" alt="' + acc_data[i].email + '" title="' + acc_data[i].email + '">View</a>';
                                //AccRows +=acc_data[i].email;
                            }

                            AccRows += '</td>\n\
                            <td ' + bgColorRows + '>';

                            if (acc_data[i].state != '')
                                AccRows += acc_data[i].state;

                            AccRows += '</td>\n\
                            <tr>';
                        }
                        var typeName = "";
                        if (acc_type == 1)
                            typeName = "Carrier";
                        else if (acc_type == 2)
                            typeName = "Shipper";
                        else if (acc_type == 3)
                            typeName = "Location";

                        $("#acc_search_result").append('<table width="100%" style="border:1px solid #cccccc;">' + AccRows + '</table><br><b style="color:red; ">Highlighted ' + typeName + ' is not allowed.</b><br>');
                    }

                } else {
                    $("#acc_search_result").append('No records found.');
                }
            } else {
                Swal.fire("Can't load account data. Try again later, please");
            }
        },
        error: function(response) {
            Swal.fire("Can't load account data. Try again later, please");
        },
        complete: function(response) {
            /* $(".acc_search_dialog").nimbleLoader('hide');*/
        }
    });
}

function accountSearchNewDispatch() {

    $("#acc_search_result_new_dispatch").html('');
    var searchText = $.trim($("#acc_search_string_new_dispatch").val());
    if (searchText == '')
        return;

    $('#carrier').css("display", "block");
    $.ajax({
        type: 'POST',
        url: BASE_PATH + 'application/ajax/accounts.php',
        dataType: 'json',
        data: {
            action: 'search',
            text: searchText,
            type: acc_type
        },
        success: function(response) {
            if (response.success) {
                $('#carrier').css("display", "none");
                acc_data = response.data;

                if (response.data.length != 0) {

                    $('#carrier').css("display", "none");

                    var bgColor = ' bgcolor="#cccccc" ';
                    var AccRows = ' <thead><tr><th ' + bgColor + '></th>';
                    if (acc_type == 2)
                        AccRows += '<th ' + bgColor + '>Name</th>';

                    AccRows += '<th ' + bgColor + '>Company</th><th ' + bgColor + '>Address</th><th ' + bgColor + '>Phone</th>';

                    if (acc_type == 3) {
                        AccRows += '<th ' + bgColor + '>City</th>';
                    } else {
                        AccRows += '<th ' + bgColor + '>Email</th>';
                    }
                    AccRows += '<th ' + bgColor + '>State</th><th ' + bgColor + '>Insurance Type</th><th ' + bgColor + '>Insurance</th><th ' + bgColor + '>Expire</th></tr> </thead>';

                    for (i in acc_data) {
                        var bgColorRows = '';
                        if (acc_data[i].donot_dispatch == 1)
                            bgColorRows = ' bgcolor="#ff6600" ';

                        var cssClass = acc_data[i].expired ? "expired" : "";

                        bgColor = '';
                        colorClass = '';
                        if (acc_data[i].rowcolor != '') {
                            bgColor = ' bgcolor="' + acc_data[i].rowcolor + '" ';
                            colorClass = 'row-white';
                        }


                        AccRows += '<tr class="' + colorClass + '"><td class="ColorRows" ' + bgColorRows + ' ' + bgColor + '><label class="kt-radio kt-radio--solid kt-radio--brand"><input type="radio" name="acc_search_result_item" class="form-box-radio" id="acc_search_result_item' + i + '" value="' + i + '"/><span></span></label> <br> <br>' + acc_data[i].id + '</td><td ' + bgColorRows + ' ' + bgColor + '>';

                        if (acc_type == 2) {
                            if (acc_data[i].first_name != '')
                                AccRows += acc_data[i].first_name;
                            if (acc_data[i].last_name != '')
                                AccRows += " " + acc_data[i].last_name;

                            AccRows += '</td><td ' + bgColorRows + ' ' + bgColor + '>';
                        }
                        if (acc_data[i].company_name != '')
                            AccRows += acc_data[i].company_name;

                        AccRows += '</td><td ' + bgColorRows + ' ' + bgColor + '>';

                        if (acc_data[i].address1 != '')
                            AccRows += acc_data[i].address1;

                        AccRows += '</td><td ' + bgColorRows + ' ' + bgColor + '>';

                        if (acc_data[i].phone1 != '')
                            AccRows += acc_data[i].phone1;

                        AccRows += '</td><td ' + bgColorRows + ' ' + bgColor + '>';

                        if (acc_type == 3) {
                            if (acc_data[i].city != '')
                                AccRows += acc_data[i].city;
                        } else {
                            if (acc_data[i].email != '')
                                AccRows += '<a href="javascript:void(0);" alt="' + acc_data[i].email + '" title="' + acc_data[i].email + '">View</a>';
                            //AccRows +=acc_data[i].email;
                        }

                        AccRows += '</td><td ' + bgColorRows + ' ' + bgColor + '>';

                        if (acc_data[i].state != '')
                            AccRows += acc_data[i].state;
                        AccRows += '</td><td ' + bgColorRows + ' ' + bgColor + '>';
                        if (acc_data[i].insurance_type == null)
                            AccRows += '--';
                        else
                            AccRows += acc_data[i].insurance_type;
                        AccRows += '</td><td ' + bgColorRows + ' ' + bgColor + '>';

                        if (acc_data[i].insurance_doc_id != '' && acc_data[i].insurance_doc_id > 0)
                            AccRows += '<a href="' + BASE_PATH + 'application/accounts/getdocs/id/' + acc_data[i].insurance_doc_id + '/type/1" title="Expire Date: ' + acc_data[i].insurance_expirationdate + '"><img src="' + BASE_PATH + 'images/ins_doc.png" width="40" height="40"></a>';
                        else
                            AccRows += '<img src="' + BASE_PATH + 'images/no_ins_doc.jpg" width="40" height="40"  title="Insurance doc not found.">';

                        AccRows += '</td><td ' + bgColor + '>';

                        AccRows += acc_data[i].insurance_expirationdate;
                        console.log(acc_data[i].insurance_expirationdate);
                        AccRows += '</td></tr>';




                    }


                    $("#acc_search_result_new_dispatch").append('<table id="acc_search_result_new" class="table table-bordered" >' + AccRows + '</table>');
                    $("#colorCod").show();
                    $("#acc_search_result_new").DataTable();
                } else {
                    $("#acc_search_result_new_dispatch").append('No records found.');
                }
            } else {
                swal.fire("Can't load account data. Try again later, please");
            }
        },
        error: function(response) {
            swal.fire("Can't load account data. Try again later, please");
        },
        complete: function(response) {

        }
    });
}

function globalAccountSearch() {
    $("#acc_global_search_result").html('');
    var searchCompany = $.trim($("#acc_search_string").val());
    if (searchCompany == '')
        return;
    $("#Search").addClass('kt-spinner kt-spinner--right kt-spinner--md kt-spinner--light');

    $.ajax({
        type: "POST",
        url: BASE_PATH + "application/ajax/accounts.php",
        dataType: 'json',
        data: {
            action: 'globalSearch',
            type: acc_type,
            company: searchCompany
        },
        success: function(response) {
            if (response.success) {
                acc_data = response.data;
                for (i in acc_data) {
                    $("#acc_global_search_result").append('<li><input type="radio" name="acc_global_search_result_item" class="form-box-radio" id="acc_global_search_result_item_' + i + '" value="' + i + '"/><label for="acc_global_search_result_item_' + i + '">' + acc_data[i].company_name + '</label></li>');
                }
            } else {
                Swal.fire("Can't load account data. Try again later, please");
            }
        },
        error: function(response) {
            Swal.fire("Can't load account data. Try again later, please");
        },
        complete: function(response) {
            /*$(".acc_global_search_dialog").nimbleLoader('hide');*/
            $("#Search").removeClass('kt-spinner kt-spinner--right kt-spinner--md kt-spinner--light');
        }
    });
}

function postToFB() {
    changeOrderStatus(4);
}

function postToFBMultiple() {

    changeOrderStatusMultiple(4);
}

function unpostFromFB() {
    changeOrderStatus(1);
}

function setPickedUpStatusAndDate(status, pickupDate) {
    changeOrderStatusAndDate(status, pickupDate);
}

function setPickedUpStatusAndDateMultiple(status, pickupDate) {
    changeOrderStatusAndDateMultiple(status, pickupDate);
}

function setPickedUpStatusAndDateByEntity(status, pickupDate, entity_id) {
    changeOrderStatusAndDateByEntity(status, pickupDate, entity_id);
}

function setPickedUpStatus() {
    changeOrderStatus(8);
}

function setDeliveredStatus() {
    changeOrderStatus(9);
}

function changeOrderStatus(status) {
    if ($(".order-checkbox:checked").length == 0) {

        Swal.fire({
            type: 'error',
            title: 'Oops...',
            text: 'Order not selected',
        })
        return false;
    }

    var entity_id = "";
    var entity_ids = [];
    $(".order-checkbox:checked").each(function() {
        entity_id = $(this).val();
        entity_ids.push(entity_id);
    });

    $.ajax({
        type: 'POST',
        url: BASE_PATH + "application/ajax/entities.php",
        dataType: 'json',
        data: {
            action: 'setStatus',
            entity_id: entity_id,
            status: status
        },
        success: function(response) {
            if (response.success) {
                window.location.reload();
            } else {
                swal.fire("Can't post order to Freight Board. Try again later, please");
            }
        },
        complete: function(response) {

        }
    });
}

function Processing_show() {

    KTApp.blockPage({
        overlayColor: '#000000',
        type: 'v2',
        state: 'primary',
        message: ''
    });

}

function changeOrderStatusMultiple(status) {
    if ($(".order-checkbox:checked").length == 0) {

        Swal.fire({
            type: 'error',
            title: 'Oops...',
            text: 'Order not selected!',
        })


        return false;
    }

    var entity_id = "";
    var entity_ids = [];
    $(".order-checkbox:checked").each(function() {
        entity_id = $(this).val();
        entity_ids.push(entity_id);
    });

    Processing_show();
    $.ajax({
        type: 'POST',
        url: BASE_PATH + "application/ajax/entities.php",
        dataType: 'json',
        data: {
            action: 'setStatusMultiple',
            entity_ids: entity_ids.join(","),
            status: status
        },
        success: function(response) {
            if (response.success) {

                if (status == 4)
                /*$("#acc_entity_dialog_confirm").dialog('option', 'title', 'Post Loads confirmation').dialog('open');*/
                //Swal.fire(status);
                    Swal.fire("This order has been posted Successfully.")
                window.location.reload();
            } else {

                Swal.fire("Can't post order to Freight Board. Try again later, please");
            }
        },
        complete: function(response) {
            KTApp.unblockPage();
        }
    });
}

function changeOrderStatusAndDate(status, pickdate) {
    if (status == '') {
        Swal.fire("Order status not set for this button.");
        return;
    }

    if ($(".order-checkbox:checked").length == 0) {
        Swal.fire("Order not selected");
        return;
    }
    var entity_id = $(".order-checkbox:checked").val();
    Processing_show();
    $.ajax({
        type: 'POST',
        url: BASE_PATH + "application/ajax/entities.php",
        dataType: 'json',
        data: {
            action: 'setStatusAndDate',
            entity_id: entity_id,
            status: status,
            pickdate: pickdate
        },
        success: function(response) {
            if (response.success) {
                window.location.reload();

            } else {
                Swal.fire("Can't post order to Freight Board. Try again later, please");
            }
        },
        complete: function(response) {
            KTApp.unblockPage();
        }
    });
}

function changeOrderStatusAndDateMultiple(status, pickdate) {
    if (status == '') {
        Swal.fire("Order status not set for this button.");
        return;
    }

    if ($(".order-checkbox:checked").length == 0) {
        $(".alert-message").empty();
        $(".alert-message").text("Order not selected");
        $(".alert-pack").show();
        return false;
    }

    var entity_id = "";
    var entity_ids = [];
    $(".order-checkbox:checked").each(function() {
        entity_id = $(this).val();
        entity_ids.push(entity_id);
    });

    $.ajax({
        type: 'POST',
        url: BASE_PATH + "application/ajax/entities.php",
        dataType: 'json',
        data: {
            action: 'setStatusAndDateMultiple',
            entity_ids: entity_ids.join(","),
            status: status,
            pickdate: pickdate
        },
        success: function(response) {
            if (response.success) {
                window.location.reload();

            } else {
                Swal.fire("Try again later, please");
            }
        },
        complete: function(response) {

        }
    });
}

function changeOrderStatusAndDateByEntity(status, pickdate, entity_id) {
    if (status == '') {
        Swal.fire("Order status not set for this button.");
        return;
    }

    if (entity_id == '') {
        Swal.fire("Order id not set for this button.");
        return;
    }

    Processing_show();
    $.ajax({
        type: 'POST',
        url: BASE_PATH + "application/ajax/entities.php",
        dataType: 'json',
        data: {
            action: 'setStatusAndDate',
            entity_id: entity_id,
            status: status,
            pickdate: pickdate
        },
        success: function(response) {
            if (response.success) {
                window.location.reload();

            } else {
                Swal.fire("Unable to mark dispatch date");
            }
        },
        complete: function(response) {
            KTApp.unblockPage();
        }
    });
}

/**
 * Chetu modified function to print dispatch sheet
 * 
 * @author Chetu Inc.
 * @param String html
 * @returns void
 * @patched 28042018
 */
function printDispatchSheet(html) {
    var mywindow = window.open();
    var is_chrome = Boolean(mywindow.chrome);
    mywindow.document.write(html);
    if (is_chrome) {
        $(".loading_bar").css('display', 'block');
        setTimeout(function() { // wait until all resources loaded
            mywindow.document.close(); // necessary for IE >= 10
            mywindow.focus(); // necessary for IE >= 10
            mywindow.print(); // change window to winPrint
            mywindow.close(); // change window to winPrint
        }, 300);
    } else {
        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10

        mywindow.print();
        mywindow.close();
    }
    //var printWindow = window.open('', 'Dispatch Sheet', 'heaight=400,width=600');
    //printWindow.document.write('<html><head><title>Dispatch Sheet</title>');
    //printWindow.document.write('<link rel="stylesheet" href="' + BASE_PATH + 'styles/application_print.css" type="text/css" />');
    //printWindow.document.write('</head><body>' + html + '</body></html>');
    //printWindow.print();
    //printWindow.document.close();
}

function printQuote(html) {
    var printWindow = window.open('', 'Quote', 'heaight=400,width=600');
    printWindow.document.write('<html><head><title>Quote</title>');
    printWindow.document.write('<link rel="stylesheet" href="' + BASE_PATH + 'styles/application_print.css" type="text/css" />');
    printWindow.document.write('</head><body>' + html + '</body></html>');
    printWindow.print();
    printWindow.document.close();
}

function printOrder(html) {
    //chetu added code
    var mywindow = window.open();
    var is_chrome = Boolean(mywindow.chrome);
    mywindow.document.write(html);
    if (is_chrome) {
        $(".loading_bar").css('display', 'block');
        setTimeout(function() { // wait until all resources loaded
            mywindow.document.close(); // necessary for IE >= 10
            mywindow.focus(); // necessary for IE >= 10
            mywindow.print(); // change window to winPrint
            mywindow.close(); // change window to winPrint
        }, 300);
    } else {
        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10

        mywindow.print();
        mywindow.close();
    }

    //    var printWindow = window.open('', 'Order', 'heaight=400,width=600');
    //    printWindow.document.write('<html><head><title>Order</title>');
    //    printWindow.document.write('<link rel="stylesheet" href="'+BASE_PATH+'styles/application_print.css" type="text/css" />');
    //    printWindow.document.write('</head><body>'+html+'</body></html>');
    //    printWindow.print();
    //    printWindow.document.close();
}

function acceptDispatchSheet(dispatch_id) {
    if (confirm("Are you sure you want to accept this dispatch?")) {
        sg_id = dispatch_id;
        $("#signature_tool").modal('show');
    }
}

function rejectDispatchSheet(dispatch_id) {
    if (confirm("Are you sure you want to reject this dispatch?")) {
        dispatchSheetAction('reject', dispatch_id);
    }
}

function dispatchSheetAction(action, dispatch_id) {
    Processing_show();
    $.ajax({
        type: "POST",
        url: BASE_PATH + "application/ajax/dispatch.php",
        dataType: 'json',
        data: {
            action: action,
            id: dispatch_id
        },
        success: function(response) {
            if (response.success) {
                window.location.reload();
            } else {
                Swal.fire("Can't " + action + " Dispatch. Try again later, please");
            }
        },
        complete: function(response) {
            KTApp.unblockPage();
        }
    });
}

function setDispatchedDate(type) {
    dispatch_date_type = type;
    switch (type) {
        case 'actual_pickup_date':
            $("#dispatch_date_dialog").dialog("option", "title", "Pickup Date");
            $("#dispatch_date_dialog").dialog("open");
            break;
        case 'actual_ship_date':
            $("#dispatch_date_dialog").dialog("option", "title", "Delivery Date");
            $("#dispatch_date_dialog").dialog("open");
            break;
    }
}

function viewDispatchSheet(dispatch_id) {
    if (parseInt(dispatch_id) == 0)
        return;
    dispatch_sheet_id = dispatch_id;

    $.ajax({
        type: "POST",
        url: BASE_PATH + "application/ajax/dispatch.php",
        dataType: 'json',
        data: {
            action: 'getHtml',
            id: dispatch_id
        },
        success: function(response) {
            if (response.success) {
                $("#dispatch_sheet_dialog").html(response.html);
                $("#dispatch_sheet_dialog").dialog('open');
            } else {
                Swal.fire("Can't get Dispatch Sheet. Try again later, please");
            }
        },
        complete: function(reponse) {}
    });
}

function setDispatchStatus(dispatch_id, status) {
    if (confirm("Are you sure wan't to change status?")) {

        $.ajax({
            url: BASE_PATH + 'application/ajax/entities.php',
            type: "POST",
            dataType: 'json',
            data: {
                action: 'setDispatchStatus',
                status: status,
                id: dispatch_id
            },
            success: function(response) {
                if (response.success) {
                    document.location.reload();
                } else {
                    Swal.fire("Can't change status. Try again later, please.")
                }
            },
            complete: function(response) {}
        });
    }
}

function changeStatus(status) {

    if ($(".entity-checkbox:checked").length == 0) {
        /* Swal.fire("You have no selected items.");*/
        Swal.fire({
            type: 'error',
            title: 'Oops...',
            text: 'You have no selected items.',
        })


    } else {
        var entity_ids = [];
        $(".entity-checkbox:checked").each(function() {
            entity_ids.push($(this).val());
        });

        $.ajax({
            type: 'POST',
            url: BASE_PATH + 'application/ajax/entities.php',
            dataType: 'json',
            data: {
                action: 'changeStatus',
                status: status,
                entity_ids: entity_ids.join(",")
            },
            success: function(response) {
                if (response.success == true) {
                    window.location.reload();
                    swal.fire('done');
                }
            }
        });
    }
}

function changeStatusLeads(status) {
    if ($(".entity-checkbox:checked").length == 0) {
        Swal.fire('You have no selected items');

    } else {
        var entity_ids = [];
        $(".entity-checkbox:checked").each(function() {
            entity_ids.push($(this).val());
        });

        Processing_show();
        $.ajax({
            type: 'POST',
            url: BASE_PATH + 'application/ajax/entities.php',
            dataType: 'json',
            data: {
                action: 'changeStatus',
                status: status,
                entity_ids: entity_ids.join(",")
            },
            success: function(response) {
                if (response.success == true) {
                    window.location.reload();
                    //Swal.fire('done');
                }
            },
            error: function(response) {
                KTApp.unblockPage();
                Swal.fire('Try again later, please');
                // Swal.fire("Try again later, please");
            },
            complete: function(response) {
                KTApp.unblockPage();
            }
        });
    }
}

function changeStatusOrders(status) {

    if ($(".order-checkbox:checked").length == 0) {
        Swal.fire({
            type: 'error',
            title: 'Oops...',
            text: 'Order not selected',
        })
        return false;
    }
    var entity_ids = [];

    $(".order-checkbox:checked").each(function() {
        var entity_id = $(this).val();
        entity_ids.push(entity_id);
    });

    Processing_show();
    $.ajax({
        type: 'POST',
        url: BASE_PATH + 'application/ajax/entities.php',
        dataType: 'json',
        data: {
            action: 'changeStatus',
            status: status,
            entity_ids: entity_ids.join(",")
        },
        success: function(response) {
            if (response.success == true) {
                KTApp.unblockPage();
                window.location.reload();
            }
        }
    });
}

function placeOnHold() {
    changeStatus(2); // OnHold = 2
}

function placeOnHoldOrders() {
    changeStatusOrders(2); // OnHold = 2
}

function restore() {
    changeStatus(1); // Active = 1
}

function restoreOrders() {
    changeStatusOrders(1); // Active = 1
}

function cancel() {
    changeStatus(3); // Archived = 3
}

function cancelOrders() {
    changeStatusOrders(3); // Archived = 3
}

function reassign(sel) {
    var member_id = 0;
    member_id = $("#company_members_" + sel).val();
    if (member_id == 0) {
        Swal.fire("You must select member to assign");
        return;
    }
    if ($(".entity-checkbox:checked").length == 0) {
        Swal.fire("You have no selected items.");
    } else {
        var entity_ids = [];
        $(".entity-checkbox:checked").each(function() {
            entity_ids.push($(this).val());
        });
        $.ajax({
            type: 'POST',
            url: BASE_PATH + 'application/ajax/entities.php',
            dataType: "json",
            data: {
                action: 'reassign',
                assign_id: member_id,
                entity_ids: entity_ids.join(',')
            },
            success: function(response) {
                if (response.success == true) {
                    window.location.reload();
                } else {
                    Swal.fire("Reassign failed. Try again later, please.");
                }
            },
            error: function(response) {
                Swal.fire("Reassign failed. Try again later, please.");
            }
        });
    }
}

var mail_file_id = '';
var mail_flds = ["mail_to", "mail_subject", "mail_body"];
var mail_flds_new = ["mail_to_new", "mail_subject_new", "mail_body_new"];

function sendFile(id, file_name) {
    clearMailForm();
    mail_file_id = id;
    $("#mail_file_name").html(file_name);
    $("#maildiv").modal();
}

function maildiv() {
    $.ajax({
        url: BASE_PATH + 'application/ajax/send_document.php',
        data: {
            action: "accounts",
            file_id: mail_file_id,
            mail_to: $('#mail_to').val(),
            mail_subject: $('#mail_subject').val(),
            mail_body: $('#mail_body').val()
        },
        type: 'POST',
        dataType: 'json',
        beforeSend: function() {
            if (!validateMailForm()) {
                return false;
            } else {
                // // $("body").nimbleLoader("show");
            }
        },
        success: function(response) {
            // $("body").nimbleLoader("hide");
            if (response.success == true) {
                $("#maildiv").modal('hide');
                clearMailForm();
            }
            Swal.fire(response.message);
        },
        complete: function() {
            // $("body").nimbleLoader("hide");
        }
    });
}

function clearMailForm() {
    mail_file_id = '';
    for (x in mail_flds) {
        $('#' + mail_flds[x]).val('');
        $('#' + mail_flds[x]).removeClass("ui-state-error");
    }
}

function validateMailForm() {
    ret = true;
    for (x in mail_flds) {
        if ($('#' + mail_flds[x]).val() == "") {
            $('#' + mail_flds[x]).addClass("ui-state-error");
            ret = false;
        } else {
            $('#' + mail_flds[x]).removeClass("ui-state-error");
        }
    }
    if ($('#mail_to').val() != "") {
        if (!checkEmailAddress($("#mail_to").val())) {
            $('#mail_to').addClass("ui-state-error");
            ret = false;
        }
    }

    return ret;
}

function validateMailFormNew() {
    ret = true;
    for (x in mail_flds_new) {
        if ($('#' + mail_flds_new[x]).val() == "") {
            $('#' + mail_flds_new[x]).addClass("ui-state-error");
            ret = false;
        } else {
            $('#' + mail_flds_new[x]).removeClass("ui-state-error");
        }
    }
    if ($('#mail_to_new').val() != "") {
        if (!checkEmailAddress($("#mail_to_new").val())) {
            $('#mail_to_new').addClass("ui-state-error");
            ret = false;
        }
    }

    return ret;
}

function postOrderToFB(entity_id) {
    changeOrderStatusToFB(entity_id, 4);
}

function unpostOrderFromFB(entity_id) {
    changeOrderStatusToFB(entity_id, 1);
}

function changeOrderStatusToFB(entity_id, status) {
    if (entity_id == 0) {
        swal.fire("Order not selected");
        return;
    }
    Processing_show();

    $.ajax({
        type: 'POST',
        url: BASE_PATH + "application/ajax/entities.php",
        dataType: 'json',
        data: {
            action: 'setStatus',
            entity_id: entity_id,
            status: status
        },
        success: function(response) {
            if (response.success) {
                window.location.reload();
            } else {
                if (response.data != '')
                    swal.fire(response.data);
                else
                    swal.fire("Can't post order to Freight Board. Try again later, please");
            }
        },
        complete: function(response) {
            KTApp.unblockPage();

        }
    });
}

function repostOrderFromFB(entity_id) {
    if (entity_id == 0) {
        swal.fire("Order not selected");
        return;
    }
    Processing_show()
    $.ajax({
        type: 'POST',
        url: BASE_PATH + "application/ajax/entities.php",
        dataType: 'json',
        data: {
            action: 'rePostToCD',
            entity_id: entity_id
        },
        success: function(response) {
            if (response.success) {
                swal.fire("RePosted order to Freight Board.");
            } else {

                swal.fire("Can't post order to Freight Board. Try again later, please");
            }
        },
        complete: function(response) {
            KTApp.unblockPage();
        }
    });
}

function OLD_emailSelectedOrderFormNew() {

    if ($(".order-checkbox:checked").length() == 0) {
        Swal.fire("Order not selected");
        return;
    }
    var entity_id = $(".order-checkbox:checked").val();

    form_id = $("#email_templates").val();
    if (form_id == "") {
        Swal.fire("Please choose email template");
    } else {

        $("body").nimbleLoader('show');
        $.ajax({
            type: "POST",
            url: BASE_PATH + "application/ajax/entities.php",
            dataType: "json",
            data: {
                action: "emailOrderNew",
                form_id: form_id,
                entity_id: entity_id
            },
            success: function(res) {
                if (res.success) {


                    $("#form_id").val(form_id);
                    $("#entity_id").val(entity_id);
                    $("#mail_to_new").val(res.emailContent.to);
                    $("#mail_subject_new").val(res.emailContent.subject);
                    $("#mail_body_new").val(res.emailContent.body);

                    //$("#mail_file_name").html(file_name);
                    $("#maildivnew").dialog("open");

                } else {
                    Swal.fire("Can't send email. Try again later, please");
                }
            },
            complete: function(res) {
                $("body").nimbleLoader('hide');
            }
        });


    }
}

function emailSelectedOrderFormNew() {
    console.log("Making UI");
    if ($(".order-checkbox:checked").length == 0) {
        Swal.fire({
            type: 'error',
            title: 'Oops...',
            text: 'Order not selected.!',
        })
        return false;
    }

    var action = "";
    var entity_id = "";
    var entity_ids = [];
    var sel_id = "";
    var oids = [];
    var shipper_name = [];
    var shipper_company = [];
    var shipper_number = [];
    var shipper_email = [];
    $(".order-checkbox:checked").each(function() {
        entity_id = $(this).val();
        entity_ids.push(entity_id);
        sel_id = $(this).parents('tr').attr('id');
        oids.push($("#" + sel_id).find(".order_id").text());
        shipper_name.push($("#" + sel_id).find(".shipper_name").text());
        shipper_company.push($("#" + sel_id).find(".shipper_company").text());
        shipper_number.push($("#" + sel_id).find(".shipper_number").text());
        shipper_email.push($("#" + sel_id).find(".shipper_email").text());
    });


    var tot_ids = entity_ids.length;

    if (tot_ids < 2) {
        entity_ids = $(".order-checkbox:checked").val();
        action = "emailOrderSingle";
    } else {
        entity_ids.join(',');
        action = "emailOrderBulk";
    }
    var form_id = $("#email_templates").val();
    if (form_id == "") {
        $(".alert-message").empty();
        $(".alert-message").text("Please choose email template.");
        $(".alert-pack").show();
        return false;
    } else {
        /* Processing_show()*/
        $.ajax({
            type: "POST",
            url: BASE_PATH + "application/ajax/newentry.php",
            dataType: "json",
            data: {
                action: action,
                form_id: form_id,
                entity_id: entity_ids
            },
            success: function(res) {
                if (res.success == "single") {

                    /*order list page single selection*/
                    $(".mail_body_section").empty();
                    var html = '<div class="edit-mail-row mail_body_section" style=""><div class="edit-mail-label">Body:<span>*</span></div><div class="edit-mail-field" style="width: 87%;"><textarea class="form-box-textfield ckeditor" style="width: 100%;" name="mail_body_new" id="mail_body_new"></textarea></div></div>';
                    $(".mail_body_section").html(html);

                    $("#form_id").val(form_id);
                    $("#entity_id").val(entity_id);
                    $("#mail_to_new").val(res.emailContent.to);
                    $("#mail_subject_new").val(res.emailContent.subject);
                    $("#mail_body_new").val(res.emailContent.body);

                    //Calling CKEDITOR instance #Chetu
                    ckRefresher('new');
                    $("#mail_att_new").html(res.emailContent.att);
                    if (res.emailContent.atttype > 0) {
                        $("#attachPdf").attr('checked', 'checked');
                    } else {
                        $("#attachHtml").attr('checked', 'checked');
                    }
                    //$("#mail_file_name").html(file_name);
                    //$("#maildivnew").dialog({ width: 566 }, 'option', 'title', 'Send Email').dialog("open");

                    $("#maildivnew").modal();

                    return false;
                } else if (res.success == "countEmails") {
                    var countEmails = res.countEmails;

                    //Mails content
                    var mailcontent = "";
                    var v = JSON.stringify(res.content, null, 4);
                    var json = JSON.parse(v);

                    for (j = 1; j <= countEmails; j++) {
                        var count = j - 1;
                        var mail_to = json[count].to;
                        var mail_subject = json[count].subject;
                        var mail_from = json[count].from;
                        var mail_fromname = json[count].fromname;
                        var mail_cc = json[count].cc;
                        var mail_bcc = json[count].bcc;
                        var mail_body = json[count].body;
                        var mail_att = json[count].att;
                        var mail_atttype = json[count].atttype;

                        if (mail_atttype == 0) {
                            var attachHtml = 'checked=checked';
                        } else if (mail_atttype > 0) {
                            var attachPdf = 'checked=checked';
                        }

                        mailcontent += '\
            <div class="edit-mail-container" id="edit-mail-show_' + j + '">\n\
                <div style="float: left;">\n\
                    <ul>\n\
                        <li style="margin-bottom: 16px;padding-top: 5px; color: forestgreen;font-weight: bold">Sending Options</li>\n\
                        <li style="margin-bottom: 14px;">Form Type <input value="1" id="attachPdf_' + j + '" ' + attachPdf + ' name="attachT' + j + '" type="radio"/>\n\
                            <label for="attachPdf_' + j + '" style="margin-right: 2px; cursor:pointer";> PDF</label>\n\
                            <input value="0" id="attachHtml_' + j + '" ' + attachHtml + ' name="attachT' + j + '" type="radio"/>\n\
                            <label for="attachHtml_' + j + '" style="cursor:pointer"> HTML</label></li><li style="margin-bottom: 11px;">Attachment(s):<span style="color:#24709F;"> ' + mail_att + '</span>\n\
                        </li>\n\
                    </ul>\n\
                </div>\n\
                <div style="text-align: right;">\n\
                    <ul>\n\
                        <li style="margin-bottom: 6px;"><span>Email:<span style="color:red">*</span></span> \n\
                            <input type="text" id="mail_to_' + j + '" name="mail_to" value="' + mail_to + '" class="form-box-combobox" >\n\
                        </li>\n\
                        <li style="margin-bottom: 6px;"><span style="margin-right: 18px;">CC:</span>\n\
                            <input type="text" id="mail_cc_' + j + '" name="mail_cc" value="' + mail_cc + '" class="form-box-combobox" >\n\
                        </li>\n\
                        <li style="margin-bottom: 12px;"><span style="margin-right: 9px;">BCC:</span> \n\
                            <input type="text" id="mail_bcc_' + j + '" name="mail_bcc" value="' + mail_bcc + '" class="form-box-combobox" >\n\
                        </li>\n\
                    </ul>\n\
                </div>\n\
                <div class="edit-mail-content" style="margin-bottom: 8px;">\n\
                    <div class="edit-mail-row" style="margin-bottom: 8px;">\n\
                        <div class="">Subject:<span>*</span></div>\n\
                        <div class="form-group" >\n\
                            <input type="text" id="mail_subject_' + j + '" class="form-box-textfield" maxlength="255" name="mail_subject" style="width: 100%;" value="' + mail_subject + '">\n\
                        </div>\n\
                    </div>\n\
                    <div class="edit-mail-row">\n\
                        <div class="">Body:<span>*</span></div>\n\
                        <div class="form-group" style="width: 100%;">\n\
                            <textarea class="form-box-textfield from-control" style="border:1px solid green; width: 100%;" name="mail_body" id="mail_body_' + j + '">' + mail_body + '</textarea>\n\
                            <script>ckRefresher(' + j + ');</script>\n\
                        </div>\n\
                    </div>\n\
                </div>\n\
                <div class="">\n\
                    <div class="edit-mail-action btn-sm btn_light_green update">Update</div>\n\
                        <div class="edit-mail-action btn-dark btn-sm cancel">Cancel</div>\n\
                    </div>\n\
                </div>';
                    }
                    $(".editmail").empty();
                    $(".editmail").html(mailcontent);

                    //List mails with edit link
                    var content = "";
                    content = '<table class="table-bordered table"><tbody>';
                    for (i = 1; i <= countEmails; i++) {
                        var lesscount = i - 1;
                        if (i % 2 == 0)
                            var trclass = "grid-body-grey";
                        else
                            var trclass = "grid-body";
                        content += '<tr class="' + trclass + '"><td class="order-id id-column" >' + oids[lesscount] + '</td><td class="shipper-column" style="width: 229px;"><div>' + shipper_name[lesscount] + '<div><div>' + shipper_company[lesscount] + '<div><div style=" word-wrap: break-word;">' + shipper_number[lesscount] + '<div><div>' + shipper_email[lesscount] + '<div></td><td style="padding: 5px 10px;text-align: center;width: 90px;">' + mail_att + '</td><td class="action-column" style="width: 29px;"><div class="go-to-edit  " id="mail-edit-link_' + i + '">Edit</div></td></tr>';
                    }

                    content += '</tbody></table>';
                    //content += '<div class="list-container"><div class="email-counter">Email </div><div class="go-to-edit" id="mail-edit-link_'+i+'">Edit</div></div>';
                    content += '<div class="edit-mail-container2"><div class=""><div class="edit-mail-action btn-sm btn_light_green  send-mail"><input type="hidden" id="count-total-mail" value="' + countEmails + '">Send Mail</div></div></div>';

                    $(".repeat-column").empty();
                    $(".repeat-column").html(content);
                    $(".mail-list-label").show();
                    $(".repeat-column").show();
                    $("#listmails").modal();

                    /*var countEmails = res.countEmails;

                     $("#listmails").dialog("open");
                     $(".alert-message").empty();
                     $(".alert-message").text(res.message);
                     $(".alert-pack").show().delay("5000", function(){
                     window.location.reload();
                     });*/
                    return false;
                } else {
                    Swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: "Can't send email. Try again later.",

                    })


                    return false;
                }
            },
            complete: function(res) {
                KTApp.unblockPage();
            }
        });
    }
}

/*
 * Functionality to make new instance for CKEditor
 *
 * @param   int     id which is instance id for ckeditor
 * @author  Chetu Inc.
 */
function ckRefresher(id) {
    $(".ckeditor").ckeditor();
    $("#mail_body_" + id).ckeditor({
        extraAllowedContent:'*{*}'
    });
}

$(document).on('click', '.edit_mail, .go-to-edit', function() {

    var selected_id = $(this).attr('id');
    selected_id = selected_id.split("_");
    var id_count = selected_id[1];
    $(".mail-list-label").hide();
    $(".repeat-column").hide();
    $("#ui-dialog-title-listmails").empty().text('Update message');
    $(".editmail").show();
    $("#edit-mail-show_" + id_count).show();

});


$(document).on('click', '.cancel', function() {
    $(".editmail").hide();
    $(".edit-mail-container").hide();
    $("#ui-dialog-title-listmails").empty().text('Email List');
    $(".mail-list-label").show();
    $(".repeat-column").show();
});

$(document).on('click', '.update', function() {
    $(".editmail").hide();
    $(".edit-mail-container").hide();
    $("#ui-dialog-title-listmails").empty().text('Email List');
    $("listmails").find('.modal-title').html('Email List')
    $(".mail-list-label").show();
    $(".repeat-column").show();
});

function validateEmailmultiple(sEmail) {
    var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    if (filter.test(sEmail)) {
        return true;
    } else {
        return false;
    }
}

$(document).on('click', '.send-mail', function() {

    var optionemail = "";
    optioncc = "";
    optionbcc = "";
    var combine = 0;
    if ($('.optionemail').val().length > 0) {
        var sEmail = $('.optionemail').val();
        if (validateEmailmultiple(sEmail) == false) {
            swal.fire('Invalid Email Address');
            return false;
        }
    }
    if ($('.optionemailextramultiple').length > 0 && $('.optionemailextramultiple').val().length > 0) {
        var sEmail = $('.optionemailextramultiple').val();
        if (validateEmailmultiple(sEmail) == false) {
            swal.fire('Invalid Email Address');

            return false;
        }
    }
    if ($('.optioncc').val().length > 0) {
        var sEmail = $('.optioncc').val();
        if (validateEmailmultiple(sEmail) == false) {
            swal.fire('Invalid Email Address');

            return false;
        }
    }
    if ($('.optionbcc').val().length > 0) {
        var sEmail = $('.optionbcc').val();
        if (validateEmailmultiple(sEmail) == false) {
            swal.fire('Invalid Email Address');

            return false;
        }
    }

    if ($('.optionemailextramultiple').length === 0 || $('.optionemailextramultiple').val().length === 0) {
        var optionemail = $('.optionemail').val();
    } else {
        var optionemail = $('.optionemail').val() + "," + $('.optionemailextramultiple').val();
    }
    var optioncc = $('.optioncc').val();
    var optionbcc = $('.optionbcc').val();

    if ($('#PDF').is(':checked')) {
        var attach_type = $('#PDF').val();
    } else {
        var attach_type = $('#HTML').val();
    };
    if ($('#combine').is(':checked')) {
        combine = 1;
    }
    var form_id = $("#email_templates").val();
    var count_total_mail = $("#count-total-mail").val();
    var mail_to = "";
    var content = [];

    if (optionemail != '') {
        for (i = 1; i <= count_total_mail; i++) {
            var ad = { 'mail_to': optionemail, 'mail_cc': optioncc, 'mail_bcc': optionbcc, 'mail_subject': $("#mail_subject_" + i).val(), 'mail_body': $("#mail_body_" + i).val(), 'attach_type': attach_type, 'combine': combine };
            content.push(ad);
        }
    } else {
        for (i = 1; i <= count_total_mail; i++) {
            if ($('#attachPdf_' + i).is(':checked')) {
                var attach_type = $('#attachPdf_' + i).val();
            } else {
                var attach_type = $('attachHtml_' + i).val();
            };
            var ad = { 'mail_to': $("#mail_to_" + i).val(), 'mail_cc': $("#mail_cc_" + i).val(), 'mail_bcc': $("#mail_bcc_" + i).val(), 'mail_subject': $("#mail_subject_" + i).val(), 'mail_body': $("#mail_body_" + i).val(), 'attach_type': attach_type };
            content.push(ad);
        }
    }

    var entity_id = "";
    var entity_ids = [];
    $(".order-checkbox:checked").each(function() {
        entity_id = $(this).val();
        entity_ids.push(entity_id);
    });
    entity_ids.join(',');

    $.ajax({
        url: BASE_PATH + 'application/ajax/newentry.php',
        data: {
            action: "emailOrderBulkSend",
            form_id: form_id,
            entity: entity_ids,
            content: content
        },
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {

                Swal.fire(
                    'Success!',
                    response.message,
                    'success'
                )
                window.location.reload();

            }
        }
    });
});

function emailSelectedLeadFormNew(nameObj) {
    //nameObj = 'orders';  //

    if ($("." + nameObj + "-checkbox:checked").length == 0) {
        // $(".alert-message").empty();
        Swal.fire('Order not selected');
        return false;
    }

    var action = "";
    var entity_id = "";
    var entity_ids = [];
    var sel_id = "";
    var oids = [];
    var shipper_name = [];
    var shipper_company = [];
    var shipper_number = [];
    var shipper_email = [];

    $("." + nameObj + "-checkbox:checked").each(function() {
        entity_id = $(this).val();
        entity_ids.push(entity_id);
        sel_id = $(this).parents('tr').attr('id');
        oids.push($("#" + sel_id).find(".order_id").text());
        shipper_name.push($("#" + sel_id).find(".shipper_name").text());
        shipper_company.push($("#" + sel_id).find(".shipper_company").text());
        shipper_number.push($("#" + sel_id).find(".shipper_number").text());
        shipper_email.push($("#" + sel_id).find(".shipper_email").text());
    });

    var tot_ids = entity_ids.length;

    if (tot_ids < 2) {
        entity_ids = $("." + nameObj + "-checkbox:checked").val();
        action = "emailOrderSingle";
    } else {
        entity_ids.join(',');
        action = "emailOrderBulk";
    }

    var form_id = $("#email_templates").val();
    if (form_id == "") {

        Swal.fire({
            type: 'error',
            title: 'Oops...',
            text: 'lease choose email template',
        })
        return false;
    } else {

        // Processing_show();

        $.ajax({
            type: "POST",
            url: BASE_PATH + "application/ajax/newentry.php",
            dataType: "json",
            data: {
                action: action,
                form_id: form_id,
                entity_id: entity_ids
            },
            success: function(res) {
                if (res.success == "single") {
                    $("#maildivnew").modal();
                    $("#form_id").val(form_id);
                    /*chetu Added code */
                    $(".mail_body_section").empty();
                    var html = '<div class="edit-mail-row mail_body_section" style=""><div class="edit-mail-label">Body:<span>*</span></div><div class="edit-mail-field" style="width: 87%;"><textarea class="form-box-textfield ckeditor" style="width: 100%;" name="mail_body_new" id="mail_body_new"></textarea></div></div>';
                    $(".mail_body_section").html(html);

                    $("#entity_id").val(entity_id);
                    $("#mail_to_new").val(res.emailContent.to);
                    $("#mail_subject_new").val(res.emailContent.subject);
                    $("#mail_body_new").val(res.emailContent.body);

                    $(".ckeditor").ckeditor();

                    $("#mail_att_new").html(res.emailContent.att);
                    if (res.emailContent.atttype > 0) {
                        $("#attachPdf").attr('checked', 'checked');
                    } else {
                        $("#attachHtml").attr('checked', 'checked');
                    }

                    $("#maildivnew").modal();
                    //$("#mail_file_name").html(file_name);
                    /* $("#maildivnew").dialog({ width: 566 }, 'option', 'title', 'Send1Email').dialog("open");*/
                    /*$("#maildivnew").modal();*/
                    return false;
                } else if (res.success == "countEmails") {
                    var countEmails = res.countEmails;

                    //Mails content
                    var mailcontent = "";
                    var v = JSON.stringify(res.content, null, 4);
                    var json = JSON.parse(v);

                    for (j = 1; j <= countEmails; j++) {

                        var count = j - 1;


                        var mail_to = json[count].to;
                        var mail_subject = json[count].subject;
                        var mail_from = json[count].from;
                        var mail_fromname = json[count].fromname;
                        var mail_cc = json[count].cc;
                        var mail_bcc = json[count].bcc;
                        var mail_body = json[count].body;
                        var mail_att = json[count].att;
                        var mail_atttype = json[count].atttype;

                        if (mail_atttype == 0) {
                            var attachHtml = 'checked=checked';
                        } else if (mail_atttype > 0) {
                            var attachPdf = 'checked=checked';
                        }
                        /*chetu added code*/
                        mailcontent += '\
            <div class="edit-mail-container" id="edit-mail-show_' + j + '">\n\
                <div style="float: left;">\n\
                    <ul>\n\
                        <li style="margin-bottom: 16px;padding-top: 5px; color: forestgreen;font-weight: bold">Sending Options</li>\n\
                        <li style="margin-bottom: 14px;">Form Type <input value="1" id="attachPdf_' + j + '" ' + attachPdf + ' name="attachT' + j + '" type="radio"/>\n\
                            <label for="attachPdf_' + j + '" style="margin-right: 2px; cursor:pointer";> PDF</label>\n\
                            <input value="0" id="attachHtml_' + j + '" ' + attachHtml + ' name="attachT' + j + '" type="radio"/>\n\
                            <label for="attachHtml_' + j + '" style="cursor:pointer"> HTML</label>\n\
                        </li>\n\
                        <li style="margin-bottom: 11px;">Attachment(s):<span style="color:#24709F;"> ' + mail_att + '</span>\n\
                        </li>\n\
                    </ul>\n\
                </div>\n\
                <div style="text-align: right;">\n\
                    <ul>\n\
                        <li style="margin-bottom: 6px;"><span>Email:<span style="color:red">*</span></span> \n\
                            <input type="text" id="mail_to_' + j + '" name="mail_to" value="' + mail_to + '" class="form-box-combobox" >\n\
                        </li>\n\
                        <li style="margin-bottom: 6px;"><span style="margin-right: 18px;">CC:</span> \n\
                            <input type="text" id="mail_cc_' + j + '" name="mail_cc" value="' + mail_cc + '" class="form-box-combobox" >\n\
                        </li>\n\
                        <li style="margin-bottom: 12px;"><span style="margin-right: 9px;">BCC:</span> \n\
                            <input type="text" id="mail_bcc_' + j + '" name="mail_bcc" value="' + mail_bcc + '" class="form-box-combobox" >\n\
                        </li>\n\
                    </ul>\n\
                </div>\n\
                <div class="" style="margin-bottom: 8px;">\n\
                    <div class="edit-mail-row" style="margin-bottom: 8px;">\n\
                        <div class="edit-mail-label">Subject:<span>*</span></div>\n\
                        <div class="form-group" >\n\
                            <input type="text" id="mail_subject_' + j + '" class="form-box-textfield" maxlength="255" name="mail_subject" style="width: 100%;" value="' + mail_subject + '">\n\
                        </div>\n\
                    </div>\n\
                <div class="edit-mail-row">\n\
                    <div class="">Body:<span>*</span></div>  \n\
                    <div class="form-group" >\n\
                        <textarea class="form-box-textfield from-control ckeditor"  name="mail_body" id="mail_body_' + j + '">' + mail_body + '</textarea>\n\
                        <script>ckRefresher(' + j + ');</script>\n\
                    </div>\n\
                </div>\n\
            </div>\n\
            <div class="">\n\
                <div class="edit-mail-action update btn-sm btn btn_dark_green">Update</div>\n\
                <div class="edit-mail-action cancel btn-sm btn-dark btn">Cancel</div>\n\
            </div>\n\
            </div>';
                    }

                    $(".editmail").empty();
                    $(".editmail").html(mailcontent);

                    //List mails with edit link

                    var content = "";
                    content = '<table class="table-bordered table " ><tbody>';
                    for (i = 1; i <= countEmails; i++) {
                        var lesscount = i - 1;
                        if (i % 2 == 0)
                            var trclass = "";
                        else
                            var trclass = "grid-body";
                        content += '<tr class="' + trclass + '"><td class="order-id id-column" >' + oids[lesscount] + '</td><td class="shipper-column" style="width: 229px;"><div>' + shipper_name[lesscount] + '<div><div>' + shipper_company[lesscount] + '<div><div style=" word-wrap: break-word;">' + shipper_number[lesscount] + '<div><div>' + shipper_email[lesscount] + '<div></td><td style="padding: 5px 10px;text-align: center;width: 90px;">' + mail_att + '</td><td class="action-column" style="width: 29px;"><div class="go-to-edit edit_mail" id="mail-edit-link_' + i + '">Edit</div></td></tr>';
                    }

                    content += '</tbody></table>';
                    //content += '<div class="list-container"><div class="email-counter">Email </div><div class="go-to-edit" id="mail-edit-link_'+i+'">Edit</div></div>';
                    content += '<div class="edit-mail-container2"><div class=""><div class="edit-mail-action send-mail-lead btn btn-sm btn_dark_green"><input type="hidden" id="count-total-mail" value="' + countEmails + '">Send Mail</div></div></div>';

                    $(".repeat-column").empty();
                    $(".repeat-column").html(content);
                    $(".mail-list-label").show();
                    $(".repeat-column").show();
                    $("#listmails").modal();

                    var countEmails = res.countEmails;

                    return false;
                } else {
                    $(".alert-message").empty();
                    $(".alert-message").text("Can't send email. Try again later.");
                    $(".alert-pack").show();
                    return false;
                }
            },
            complete: function(res) {
                KTApp.unblockPage();
            }
        });
    }
}

function emailSelectedLeadFormNewsend() {
    var sEmail = [$('#mail_to_new').val(), $('.optionemailextra').val(), $('#mail_cc_new').val(), $('#mail_bcc_new').val()];
    if (validateEmail(sEmail) == false) {
        swal.fire('Invalid Email Address');
        return false;
    }
    if ($('#attachPdf').is(':checked')) {
        attach_type = $('#attachPdf').val();
    } else {
        attach_type = $('#attachHtml').val();
    };
    $.ajax({
        url: BASE_PATH + 'application/ajax/entities.php',
        data: {
            action: "emailQuoteNewSend",
            form_id: $('#form_id').val(),
            entity_id: $('#entity_id').val(),
            mail_to: $('#mail_to_new').val(),
            mail_cc: $('#mail_cc_new').val(),
            mail_bcc: $('#mail_bcc_new').val(),
            mail_extra: $('.optionemailextra').val(),
            mail_subject: $('#mail_subject_new').val(),
            mail_body: $('#mail_body_new').val(),
            attach_type: attach_type
        },
        type: 'POST',
        dataType: 'json',
        beforeSend: function() {

            $("#maildivnew").find('.modal-body').addClass('kt-spinner kt-spinner--lg kt-spinner--dark');
            if ($('#mail_to_new').val() == "" || $('#mail_subject_new').val() == "" || $('#mail_body_new').val() == "") {
                swal.fire('Empty Field(s)');
                return false;
            };
        },
        success: function(response) {
            // $("body").nimbleLoader("hide");
            if (response.success == true) {

                $("#maildivnew").modal('hide');
                clearMailForm();
            }

        },
        complete: function() {
            $("#maildivnew").find('.modal-body').removeClass('kt-spinner kt-spinner--lg kt-spinner--dark');
            $("#maildivnew").modal('hide');
        }
    });
}

/*here use the hide and show*/
$(document).ready(function() {
    $('.hide_show').click(function() {
        $(this).next().toggle();
    })

    $('.block-title').click(function() {
        $(this).next().toggle();
    })
});

//  here use live 
$(document).on('click', '.send-mail-lead', function() {
    var optionemail = "";
    optioncc = "";
    optionbcc = "";
    var combine = 0;
    if ($('.optionemail').val().length > 0) {
        var sEmail = $('.optionemail').val();
        if (validateEmailmultiple(sEmail) == false) {
            swal.fire('Invalid Email Address');
            return false;
        }
    }
    if ($('.optionemailextramultiple').length > 0 && $('.optionemailextramultiple').val().length > 0) {
        var sEmail = $('.optionemailextramultiple').val();
        if (validateEmailmultiple(sEmail) == false) {
            swal.fire('Invalid Email Address');

            return false;
        }
    }
    if ($('.optioncc').val().length > 0) {
        var sEmail = $('.optioncc').val();
        if (validateEmailmultiple(sEmail) == false) {
            swal.fire('Invalid Email Address');

            return false;
        }
    }
    if ($('.optionbcc').val().length > 0) {
        var sEmail = $('.optionbcc').val();
        if (validateEmailmultiple(sEmail) == false) {
            swal.fire('Invalid Email Address');

            return false;
        }
    }

    if ($('.optionemailextramultiple').length === 0 || $('.optionemailextramultiple').val().length === 0) {
        var optionemail = $('.optionemail').val();
    } else {
        var optionemail = $('.optionemail').val() + "," + $('.optionemailextramultiple').val();
    }
    var optioncc = $('.optioncc').val();
    var optionbcc = $('.optionbcc').val();

    if ($('#PDF').is(':checked')) {
        var attach_type = $('#PDF').val();
    } else {
        var attach_type = $('#HTML').val();
    };
    if ($('#combine').is(':checked')) {
        combine = 1;
    }
    var form_id = $("#email_templates").val();
    var count_total_mail = $("#count-total-mail").val();
    var mail_to = "";
    var content = [];



    var form_id = $("#email_templates").val();
    var count_total_mail = $("#count-total-mail").val();
    var mail_to = "";
    var content = [];
    if (optionemail != '') {
        for (i = 1; i <= count_total_mail; i++) {
            var ad = { 'mail_to': optionemail, 'mail_cc': optioncc, 'mail_bcc': optionbcc, 'mail_subject': $("#mail_subject_" + i).val(), 'mail_body': $("#mail_body_" + i).val(), 'attach_type': attach_type, 'combine': combine };
            content.push(ad);
        }
    } else {
        for (i = 1; i <= count_total_mail; i++) {
            if ($('#attachPdf_' + i).is(':checked')) {
                var attach_type = $('#attachPdf_' + i).val();
            } else {
                var attach_type = $('attachHtml_' + i).val();
            };
            var ad = { 'mail_to': $("#mail_to_" + i).val(), 'mail_cc': $("#mail_cc_" + i).val(), 'mail_bcc': $("#mail_bcc_" + i).val(), 'mail_subject': $("#mail_subject_" + i).val(), 'mail_body': $("#mail_body_" + i).val(), 'attach_type': attach_type };
            content.push(ad);
        }
    }

    var entity_id = "";
    var entity_ids = [];
    $(".entity-checkbox:checked").each(function() {
        entity_id = $(this).val();
        entity_ids.push(entity_id);
    });
    entity_ids.join(',');

    $.ajax({
        url: BASE_PATH + 'application/ajax/newentry.php',
        data: {
            action: "emailOrderBulkSend",
            form_id: form_id,
            entity: entity_ids,
            content: content
        },
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {

                Swal.fire(
                    'Good job!',
                    response.message,
                    'success'
                )
                window.location.reload();

            }
        }
    });
});