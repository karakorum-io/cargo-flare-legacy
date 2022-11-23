/* Vehicle types for autocomplete */
var vehicle_type_data = ['Coupe', 'Sedan Small', 'Sedan Midsize', 'Sedan Large', 'Convertible', 'Pickup Small', 'Pickup Crew Cab', 'Pickup Full-size', 'Pickup Extd. Cab', 'RV', 'Dually', 'SUV Small', 'SUV Mid-size', 'SUV Large', 'Travel Trailer', 'Van Mini', 'Van Full-size', 'Van Extd. Lenght', 'Van Pop-Top', 'Motorcycle', 'Boat'];
var hide_notes = false;
/* Freight Dragon Alert */
var myLoadingParams = {
	loaderClass        : "loading_bar"
};
var acc_location = null;
var dispatch_date_type = null;
var dispatch_sheet_id = null;
// outerHTML for jQuery
jQuery.fn.outerHTML = function(s) {
    return s
        ? this.before(s).remove()
        : jQuery("<p>").append(this.eq(0).clone()).html();
};

$.fn.nimbleLoader.setSettings(myLoadingParams);
function FdAlert(msg) {
	$("#fd_alert").html(msg);
	$("#fd_alert").dialog({
		modal: true,
		width: 300,
		height: 100,
		title: "Freight Dragon",
		hide: 'fade',
		resizable: false,
		draggable: false,
		autoOpen: true
	}).dialog("open");
}

window.alert = FdAlert;

function in_array(needle, haystack, strict) {
	var found = false, key, strict = !!strict;
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
		if (z%2) {
			$(this).addClass('even');
		}
		z++;
	});
}

/* System messages */
function checkSysMessages(){
	$.ajax({
		url: BASE_PATH+'application/ajax/sysmessages.php',
		data: { action: "get"},
		type: 'POST',
		dataType: 'json',
		beforeSend: function() {},
		success: function(retData) {
			if ($("#sysmessage").size() > 0) {
				if (retData.message != "" && !$('#sysmessage').is(':visible')  ){
					$('#sysmessage').html(retData.message).show("drop");
				}
			}
		}
	});
}

function closeSysMessage(id){
	$.ajax({
		url: BASE_PATH+'application/ajax/sysmessages.php',
		data: { action: "close", id:id},
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
		url: BASE_PATH+'application/ajax/tasks.php',
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
    disableSelection : function() {
            this.each(function() {
                    this.onselectstart = function() { return false; };
                    this.unselectable = "on";
                    jQuery(this).css('-moz-user-select', 'none');
					jQuery(this).css('-webkit-user-select', 'none');
            });
    },
    enableSelection : function() {
            this.each(function() {
                    this.onselectstart = function() {};
                    this.unselectable = "off";
                    jQuery(this).css('-moz-user-select', 'auto');
					jQuery(this).css('-webkit-user-select', 'auto');
            });
    }
});

$(document).ready(function() {
	checkSysMessages();
        
        $(".multi-vehicles").click(function(){
		$(this).next().toggle();
	});
	$(".viewsample").click(function(){
		$(this).next().toggle();
	});
	$(".payment-info-trigger").click(function(){
		$(this).next().toggle();
	});
	$(".form-box-buttons-new *, .form-box-buttons *, .ui-button *, #fd_alert *, .task-bar *").disableSelection();
	$(".lead-info p.block-title, .quote-info p.block-title, .order-info p.block-title, .dispatch-info p.block-title").click(function() {
		$(this).next().toggle();
	});
	$(".lead-info p, .quote-info p").disableSelection();


	getByZip('origin');
	getByZip('destination');
	getByZip('shipper');
	getByZip('carrier');
	getByZip('pickup');
	getByZip('deliver');

    /*
	$(".geo-city").geo_autocomplete({
		geocoder_types: 'locality, colloquial_area',
		geocoder_region: 'North America',
		autoFocus: true
	});
	$(".geo-state").geo_autocomplete({
		geocoder_types: 'administrative_area_level_1, administrative_area_level_2, administrative_area_level_3',
		geocoder_region: 'North America',
		autoFocus: true
	});
	*/

    $("#acc_search_dialog").dialog({
        autoOpen: false,
        dialogClass: 'acc_search_dialog',
        modal: true,
        width: 400,
        resizable: false,
        draggable: true,
        buttons: [{
            text: 'Cancel',
            click: function(){
                $(this).dialog('close');
                $("#acc_search_result").html('');
                $("#acc_search_dialog input").val('');
            }
        },{
            text: 'Get Account Info',
            click: function(){
                if ($("input[name='acc_search_result_item']:checked").size() == 0) return;
                if (window.applySearch) {
                    applySearch($("input[name='acc_search_result_item']:checked").val());
                }
                $(this).dialog('close');
            }
        }]
    });

    $("#acc_global_search_dialog").dialog({
        modal: true,
        autoOpen: false,
        resizable: false,
        draggable: true,
        width: 400,
        dialogClass: 'acc_global_search_dialog',
        buttons: [{
            text: 'Cancel',
            click: function(){
                $(this).dialog('close');
                $("#acc_global_search_result").html('');
                $("#acc_global_search_dialog input").val('');
            }
        },{
            text: 'Get Account Info',
            click: function() {
                if ($("input[name='acc_global_search_result_item']:checked").size() == 0) return;
                if (window.applyGlobalSearch) {
                    applyGlobalSearch($("input[name='acc_global_search_result_item']:checked").val());
                }
                $(this).dialog('close');
            }
        }]
    });
	$(".datepicker").datepicker();
    $('.import-toggle').click(function() {
        $('.import-hidden').toggle();
    });
});
setInterval(checkSysMessages, 1000*30);
setInterval(checkTasks, 1000*30);

function getByZip(prefix) {
	$("input[name='"+prefix+"_zip']").live("blur", function() {

		var ajax_zip = $.trim($(this).val());
		ajax_zip = ajax_zip.replace(/-/gi, '');
		ajax_zip = ajax_zip.replace(/_/gi, '');

        if ($("select[name='"+prefix+"_country']").val() == ''){
            $("select[name='"+prefix+"_country']").val('US');
        }

		if ($("select[name='"+prefix+"_country']").val() != 'US' || $.trim($(this).val()) == '') return false;
		$.ajax({
			type: 'POST',
			url: BASE_PATH+"application/ajax/ajax.php?action=getByZip",
			dataType: 'json',
			data: {
				zip: ajax_zip
			},
			success: function(res) {
				if (res.success) {
					$("input[name='"+prefix+"_city']").val(res.data['city']);
					$("select[name='"+prefix+"_state']").val(res.data['state']);
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
		url: BASE_PATH+'application/ajax/map.php',
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
	if ($("#notes_add").css("display") != "none") return;
	hide_notes = false;
	var position = $("#notes_"+notes_type+"_"+entity_id).position();
	$("#notes_container").css("left", position.left + 30);
	$("#notes_container").css("top", position.top);
	if ((notes[notes_type][entity_id]) == undefined) {
		$.ajax({
			type: 'POST',
			url: BASE_PATH+'application/ajax/notes.php',
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
	if (notes_data.toString() == "") return;
	var title = "";
	switch(notes_type) {
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
	$("#notes_container").append("<p>"+title+"<\/p>");
	for (i in notes_data) {
		var container = "";
		container += "<div><span class='note-from'>"+notes_data[i].created;
		if (notes_type != 2) {
			container += " "+notes_data[i].sender;
		}
		container += "<\/span><br/>"+notes_data[i].text;
		container += "<\/div>";
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
	$("#notes_add").hide();
}

function openAddNotes(entity_id, notes_type) {
	 console.log("Dd");
	add_notes_type = notes_type;
	$("#notes_container").hide();
	var position = $("#notes_"+notes_type+"_"+entity_id).position();
	$("#notes_add").css("left", position.left + 30);
	$("#notes_add").css("top", position.top);
	switch(notes_type) {
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
	$("#notes_add textarea").val("");
	$("#notes_add").show();
	if (($("#notes_add").height() + $("#notes_add").position().top) > ($(window).height() + $(window).scrollTop() - 20)) {
		$("#notes_add").css("top", $(window).height() + $(window).scrollTop() - $("#notes_add").height() - 20);
	}
}

function addNote() {
	if (add_busy) return;
	add_busy = true;
	var note_text =  $.trim($("#notes_add textarea").val());
	if (note_text != "") {
		$.ajax({
			type: 'POST',
			url: BASE_PATH+'application/ajax/notes.php',
			dataType: 'json',
			data: {
				action: 'add',
				entity_id: add_entity_id,
				notes_type: add_notes_type,
				text: encodeURIComponent(note_text)
			},
			success: function(response) {
				if (response.success == true) {
					notes[add_notes_type][add_entity_id] = undefined;
					$("#notes_"+add_notes_type+"_"+add_entity_id).html(parseInt($("#notes_"+add_notes_type+"_"+add_entity_id).text()) + 1);
					$("#notes_"+add_notes_type+"_"+add_entity_id).removeClass("note-grey");
					$("#notes_"+add_notes_type+"_"+add_entity_id).addClass("note-green");
				}
			}
		});
	}
	$("#notes_add").hide();
	add_busy = false;
}

function deleteLogo(url) {
    if (confirm('Are you sure you want to delete?')) {
        $.post(url, {});
            var idObj = $('#logo-file');
            idObj.fadeOut(function(){
                idObj.remove();
            });
    }
    return false;
}

function deleteFile(url, id) {
    if (confirm('Are you sure you want to delete this file?')) {
        $.ajax({
			url: url,
			data: {id:id},
			type: 'GET',
			dataType: 'json',
			success: function(response) {
				if (response.success == true) {
					var idObj = $('#file-'+id);
		            idObj.fadeOut(function(){
		                idObj.remove();
		            });
				}else{
					alert("Can't delete item.");
				}
			}
		});
    }
    return false;
}

function updateView(id) {
	$.ajax({
		type: "POST",
		url: BASE_PATH+"application/ajax/ajax.php?action=updateView",
		data: "id="+id,
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
                    alert("Can't update status.");
            }
        }
    });

    return false;
}

function array_values( input ) {
	var tmp_arr = new Array(), cnt = 0;
	for ( key in input ) {
		tmp_arr[cnt] = input[key];
		cnt++;
	}
	return tmp_arr;
}

function selectCarrier() {
    $("#acc_global_search_result").html("");
    $("#acc_search_string").val("");
    acc_type = 1;
    $("#acc_search_dialog").dialog('option', 'title', 'Select Carrier').dialog('open');
}

function selectGlobalCarrier() {
    $("#acc_global_search_result").html("");
    acc_type = 1;
    $("#acc_global_search_dialog").dialog('option', 'title', 'SelectCarrier').dialog('open');
}

function selectShipper() {
    $("#acc_search_result").html("");
    $("#acc_search_string").val("");
    acc_type = 2;
    $("#acc_search_dialog").dialog('option', 'title', 'Select Shipper').dialog('open');
}


function selectTerminal(location) {
    acc_location = location;
    $("#acc_search_result").html("");
    $("#acc_search_string").val("");
    acc_type = 3;
    $("#acc_search_dialog").dialog('option', 'title', 'Select Terminal').dialog('open');
}

var acc_data = null;
var acc_type = null;

function accountSearch() {
    $("#acc_search_result").html('');
    var searchText = $.trim($("#acc_search_string").val());
    if (searchText == '') return;
    $(".acc_search_dialog").nimbleLoader('show');
    $.ajax({
        type: 'POST',
        url: BASE_PATH+'application/ajax/accounts.php',
        dataType: 'json',
        data: {
            action: 'search',
            text: searchText,
            type: acc_type
        },
        success: function(response){
            if (response.success) {
                acc_data = response.data;
				if (response.data.length != 0) {
					for (i in acc_data) {
                        var cssClass = acc_data[i].expired ? "expired" : "";
                        var label = '<label class="' + cssClass + '" for="acc_search_result_item'+i+'">'+acc_data[i].company_name+'</label>';
						$("#acc_search_result").append('<li><input type="radio" name="acc_search_result_item" class="form-box-radio" id="acc_search_result_item'+i+'" value="'+i+'"/>' + label + '</li>');
					}
				} else {
					$("#acc_search_result").append('No records found.');
				}
            } else {
                alert("Can't load account data. Try again later, please");
            }
        },
        error: function(response){
            alert("Can't load account data. Try again later, please");
        },
        complete: function(response){
            $(".acc_search_dialog").nimbleLoader('hide');
        }
    });
}

function globalAccountSearch() {
    $("#acc_global_search_result").html('');
    var searchCompany = $.trim($("#acc_search_string").val());
    if (searchCompany == '') return;
    $(".acc_global_search_dialog").nimbleLoader('show');
    $.ajax({
        type: "POST",
        url: BASE_PATH+"application/ajax/accounts.php",
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
                    $("#acc_global_search_result").append('<li><input type="radio" name="acc_global_search_result_item" class="form-box-radio" id="acc_global_search_result_item_'+i+'" value="'+i+'"/><label for="acc_global_search_result_item_'+i+'">'+acc_data[i].company_name+'</label></li>');
                }
            } else {
                alert("Can't load account data. Try again later, please");
            }
        },
        error: function(response) {
            alert("Can't load account data. Try again later, please");
        },
        complete: function(response) {
            $(".acc_global_search_dialog").nimbleLoader('hide');
        }
    });
}

function postToFB() {
	changeOrderStatus(4);
}

function unpostFromFB() {
	changeOrderStatus(1);
}

function setPickedUpStatus() {
	changeOrderStatus(8);
}

function setDeliveredStatus() {
	changeOrderStatus(9);
}

function changeOrderStatus(status) {
	if ($(".order-checkbox:checked").size() == 0) {
		alert("Order not selected");
		return;
	}
	var entity_id = $(".order-checkbox:checked").val();
	$("body").nimbleLoader('show');
	$.ajax({
		type: 'POST',
		url: BASE_PATH+"application/ajax/entities.php",
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
				alert("Can't post order to Freight Board. Try again later, please");
			}
		},
		complete: function(response) {
			$("body").nimbleLoader('hide');
		}
	});
}

function printDispatchSheet(html) {
    var printWindow = window.open('', 'Dispatch Sheet', 'heaight=400,width=600');
    printWindow.document.write('<html><head><title>Dispatch Sheet</title>');
    printWindow.document.write('<link rel="stylesheet" href="'+BASE_PATH+'styles/application_print.css" type="text/css" />');
    printWindow.document.write('</head><body>'+html+'</body></html>');
    printWindow.print();
    printWindow.document.close();
}

function printQuote(html) {
    var printWindow = window.open('', 'Quote', 'heaight=400,width=600');
    printWindow.document.write('<html><head><title>Quote</title>');
    printWindow.document.write('<link rel="stylesheet" href="'+BASE_PATH+'styles/application_print.css" type="text/css" />');
    printWindow.document.write('</head><body>'+html+'</body></html>');
    printWindow.print();
    printWindow.document.close();
}

function printOrder(html) {
    var printWindow = window.open('', 'Order', 'heaight=400,width=600');
    printWindow.document.write('<html><head><title>Order</title>');
    printWindow.document.write('<link rel="stylesheet" href="'+BASE_PATH+'styles/application_print.css" type="text/css" />');
    printWindow.document.write('</head><body>'+html+'</body></html>');
    printWindow.print();
    printWindow.document.close();
}

function acceptDispatchSheet(dispatch_id) {
	if (confirm("Are you sure you want to accept this dispatch?")) {
		sg_id = dispatch_id;
		$("#signature_tool").dialog('open');
	}
}

function rejectDispatchSheet(dispatch_id) {
	if (confirm("Are you sure you want to reject this dispatch?")) {
	    dispatchSheetAction('reject', dispatch_id);
	}
}

function dispatchSheetAction(action, dispatch_id) {
    $("body").nimbleLoader('show');
    $.ajax({
        type: "POST",
        url: BASE_PATH+"application/ajax/dispatch.php",
        dataType: 'json',
        data: {
            action: action,
            id: dispatch_id
        },
        success: function(response) {
            if (response.success) {
                window.location.reload();
            } else {
                alert("Can't "+action+" Dispatch. Try again later, please");
            }
        },
        complete: function(response) {
            $("body").nimbleLoader('hide');
        }
    });
}

function setDispatchedDate(type) {
	dispatch_date_type = type;
	switch(type) {
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
    if (parseInt(dispatch_id) == 0) return;
	dispatch_sheet_id = dispatch_id;
    $("body").nimbleLoader('show');
    $.ajax({
        type: "POST",
        url: BASE_PATH+"application/ajax/dispatch.php",
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
                alert("Can't get Dispatch Sheet. Try again later, please");
            }
        },
        complete: function(reponse) {
            $("body").nimbleLoader('hide');
        }
    });
}

function setDispatchStatus(dispatch_id, status) {
    if (confirm("Are you sure wan't to change status?")) {
        $("body").nimbleLoader('show');
        $.ajax({
            url: BASE_PATH+'application/ajax/entities.php',
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
                    alert("Can't change status. Try again later, please.")
                }
            },
            complete: function(response) {
                $("body").nimbleLoader('hide');
            }
        });
    }
}

function changeStatus(status) {
    if ($(".entity-checkbox:checked").size() == 0) {
        alert("You have no selected items.");
    } else {
        var entity_ids = [];
        $(".entity-checkbox:checked").each(function(){
            entity_ids.push($(this).val());
        });
        $.ajax({
            type: 'POST',
            url: BASE_PATH+'application/ajax/entities.php',
            dataType: 'json',
            data: {
                action: 'changeStatus',
                status: status,
                entity_ids: entity_ids.join(",")
            },
            success: function(response) {
                if (response.success == true) {
                    window.location.reload();
                }
            }
        });
    }
}

function changeStatusOrders(status) {
    if ($(".order-checkbox:checked").size() == 0) {
        alert("Order not selected");
        return;
    }
    var entity_id = $(".order-checkbox:checked").val();
    var entity_ids = [];
    entity_ids.push(entity_id);
    $.ajax({
        type: 'POST',
        url: BASE_PATH+'application/ajax/entities.php',
        dataType: 'json',
        data: {
            action: 'changeStatus',
            status: status,
            entity_ids: entity_ids.join(",")
        },
        success: function(response) {
            if (response.success == true) {
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
	console.log("Ddd");
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
    member_id = $("#company_members_"+sel).val();
    if (member_id == 0) {
        alert("You must select member to assign");
        return;
    }
    if ($(".entity-checkbox:checked").size() == 0) {
        alert("You have no selected items.");
    } else {
        var entity_ids = [];
        $(".entity-checkbox:checked").each(function() {
            entity_ids.push($(this).val());
        });
        $.ajax({
            type: 'POST',
            url: BASE_PATH+'application/ajax/entities.php',
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
                    alert("Reassign failed. Try again later, please.");
                }
            },
            error: function(response) {
                alert("Reassign failed. Try again later, please.");
            }
        });
    }
}

var mail_file_id = '';
var mail_flds = ["mail_to", "mail_subject", "mail_body"];

function sendFile(id, file_name) {
    clearMailForm();
    mail_file_id = id;
    $("#mail_file_name").html(file_name);
    $("#maildiv").dialog("open");
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
    if ( $('#mail_to').val() !=""){
        if (!checkEmailAddress($("#mail_to").val())){
            $('#mail_to').addClass("ui-state-error");
            ret = false;
        }
    }

    return ret;
}

