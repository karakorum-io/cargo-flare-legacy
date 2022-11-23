/* Vehicle types for autocomplete */
var vehicle_type_data = ['Coupe', 'Sedan Small', 'Sedan Midsize', 'Sedan Large', 'Convertible', 'Pickup Small', 'Pickup Crew Cab', 'Pickup Full-size', 'Pickup Extd. Cab', 'RV', 'Dually', 'SUV Small', 'SUV Mid-size', 'SUV Large', 'Travel Trailer', 'Van Mini', 'Van Full-size', 'Van Extd. Lenght', 'Van Pop-Top', 'Motorcycle', 'Boat','Other'];
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

function validateUsPhone(e,f){
	var len = f.value.length;
	var key = whichKeyCode(e);
	//alert(key);
	if(key>47 && key<58)
	{
		
		if( len==3 )f.value=f.value+'-'
		else if(len==7 )f.value=f.value+'-'
		else f.value=f.value;
		
	}
	else if(key==8 || key==46){
		  if(ShowSelection(f))
		  {
		   if( (len)==3 )f.value=f.value+'-'
		   else if((len)==7 )f.value=f.value+'-'
		  // else f.value=f.value;
		  }
		   
		}
	else{
		f.value = f.value.replace(/[^0-9-]/,'')
		f.value = f.value.replace('--','-')
	}
}
 
function whichKeyCode(e) {
	var code;
	if (!e) var e = window.event;
	if (e.keyCode) code = e.keyCode;
	else if (e.which) code = e.which;
	return code
//	return String.fromCharCode(code);
}

function ShowSelection(f)
{
  var textComponent = f;
  var selectedText;
  // IE version
  if (document.selection != undefined)
  {
    textComponent.focus();
    var sel = document.selection.createRange();
    selectedText = sel.text;
  }
  // Mozilla version
  else if (textComponent.selectionStart != undefined)
  {
    var startPos = textComponent.selectionStart;
    var endPos = textComponent.selectionEnd;
    selectedText = textComponent.value.substring(startPos, endPos)
  }
  //alert("You selected: " + selectedText.length);
  if(selectedText.length>0)
    return true;
  else
    return false; 
}
/*********************************/
$(document).ready(function() {
	//checkSysMessages();
       
	 $('#app_sms').keypress(function(e) {
							  
			if(e.which == 13) {
				//alert('You pressed enter!');
				var textSMS = $("#app_sms").val();
				if(textSMS!="")
				  sendChatSMS();
				return false;
			}
		});	
	
	$('#app_chat').keypress(function(e) {
							  
			if(e.which == 13) {
				//alert('You pressed enter!');
				var textChat = $("#app_chat").val();
				if(textChat!="")
				  sendChatNew();
				return false;
			}
		});	
	
    $(".multi-vehicles").click(function(){
		$(this).next().toggle();
	});
	$(".multi-vehicles-new").click(function(){

		//$(this).next().toggle();
		
	});
	 
	 $(".search-box-header").click(function(){

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

/*
	getByZip('origin');
	getByZip('destination');
	getByZip('shipper');
	getByZip('carrier');
	getByZip('pickup');
	getByZip('deliver');
*/

    getByZipNew('origin');
	getByZipNew('destination');
	getByZipNew('shipper');
	getByZipNew('carrier');
	getByZipNew('pickup'); 
	getByZipNew('deliver');	

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
	
	
$("#acc_search_dialog_new_dispatch").dialog({
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
                $("#acc_search_result_new_dispatch").html('');
                $("#acc_search_dialog_new_dispatch input").val('');
            }
        },{
            text: 'Get Account Info',
            click: function(){
                if ($("input[name='acc_search_result_item']:checked").size() == 0) return;
				var order_id = $(".order-checkbox:checked").val();
				//alert($("input[name='acc_search_result_item']:checked").size()+'----'+$("input[name='acc_search_result_item']:checked").val());
				var acc_obj = acc_data[$("input[name='acc_search_result_item']:checked").val()];
				
				location.href =  BASE_PATH+"application/orders/dispatchnew/id/"+order_id+"/acc/"+acc_obj.id;
                /*
				if (window.applySearch) {
                    applySearch($("input[name='acc_search_result_item']:checked").val());
                }*/
                //$(this).dialog('close');
            }
        },{
            text: 'New Carrier',
            click: function(){
				
				var order_id = $(".order-checkbox:checked").val();
	   
	            location.href =  BASE_PATH+"application/orders/dispatchnew/id/"+order_id;
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
	
	$('.elementname').focus(function() {
										  // alert($(this).attr("elementname"));
										  // $(this).addClass("focus_mandatory_element");
		if($(this).attr("elementname") == 'input' || $(this).attr("elementname") == 'select')
		{
			//$(this).removeClass("form-box-textfield");
			$(this).addClass("focus_mandatory_element");
			
		}
		
	});
	 
	$('.elementname').blur(function() {
	   if($(this).attr("elementname") == 'input' || $(this).attr("elementname") == 'select'){
		  $(this).removeClass("focus_mandatory_element")
		  //$(this).addClass("form-box-textfield");
	   }
	   
	   
	});
	//for email trim        
	$("#mail_to_new").change(function(){
       var new_trimemail = $.trim($("#mail_to_new").val());
       $('#mail_to_new').val(new_trimemail);
    });
	$("#mail_to").change(function(){
       var newtrimemail = $.trim($("#mail_to").val());
       $('#mail_to').val(newtrimemail);
    });
});
//setInterval(checkSysMessages, 1000*30);
//setInterval(checkTasks, 1000*30);

function getByZipNew(prefix) {
	
	$("input[name='"+prefix+"_zip']").live("blur", function() {
				showziptooltiplocal(prefix);
		});													
}

function showziptooltiplocal(prefix)
{
	 var widthW = 400;
	 var leftL = 90;
	 if(prefix=='carrier' || prefix=='pickup' || prefix=='deliver'){  
	   widthW = 300;
	   leftL = 350;
     }
	var position = $('#'+prefix+'_zip').position();
	$("#notes_container").css("left", position.left + leftL);
	$("#notes_container").css("top", position.top);
	$("#notes_container").css("width", widthW);
	
	
	                   var ajax_zip = $.trim($("input[name='"+prefix+"_zip']").val());
					   ajax_zip = ajax_zip.replace(/-/gi, '');
		               ajax_zip = ajax_zip.replace(/_/gi, ''); 
						
						if(ajax_zip !=''){
						   // next, we want to check if our data has already been cached
							//if (origin.data('ajax') !== 'cached') {
								$.ajax({
									type: 'POST',
									url: BASE_PATH+"application/ajax/ajax.php?action=getByZipLocal",
									dataType: 'json',
									data: {
										zip: ajax_zip
									},
									success: function(res) {
										if (res.success) {
											
											if(res.data.status=="ok"){
												 var contentData = "";
												 if(res.data.size > 1)
												 {
													 // alert('success');
													  contentData += "<table width='100%'> <tr><td><span style='font-size:11px;'>Suggested cities </span></td><td align='right'>         <a href='javascript:void(0);' onclick='javascript:hideNotes();'>close</a></td></tr>";
													
													 for (i = 0; i < res.data.size; i++) {
														 
														 //postcode_localitiesV += postcode_localitiesArr[i];
														   contentData += "<tr><td><a href ='javascript:void(0);' onclick=\"selectStateCity('"+prefix+"','"+res.data.data[i]['city']+"' , '"+res.data.data[i]['state']+"');\">"+res.data.data[i]['city']+" ";
														   contentData += ", "+ajax_zip+"";
														   contentData += ", "+res.data.data[i]['state']+"";
														   
														  
														   contentData += "</a></td></tr>";
													 }
													contentData += "</table>";
													 
													  hide_notes = false;
													 $("#notes_container").html("");
													 $("#notes_container").append(contentData);
													 $("#notes_container").show();
													  
													  
												 }
												 else
												 {
													 $("input[name='"+prefix+"_city']").val(res.data.data[0]['city']);
	                                                 $("select[name='"+prefix+"_state']").val(res.data.data[0]['state']);
												 }
											}
											
										}
										else
										  alert('City for this zip code is not matching.');
									}
								});
							}
		
}

function selectStateCity(prefix,city,state){
	  
	  $("input[name='"+prefix+"_city']").val(city);
	  $("select[name='"+prefix+"_state']").val(state);
	  hideNotes();
}

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
	$("#notes_container").css("width", 550);
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
		var contactname = notes_data[i].sender;
		var bgcolor = '#ffffff';	
		if(notes_data[i].system_admin == 1){
			 contactname = "FreightDragon";
		  }
		container += "<div><span class='note-from'>"+notes_data[i].created;
		if (notes_type != 2) {
			container += " "+contactname;
		}
		if(notes_data[i].priority==2)
		   bgcolor = '#FF0000';
		   
		container += "<\/span><br/><span class='note-data' style='color:"+bgcolor+"'>";
		if(notes_data[i].system_admin == 1 || notes_data[i].priority==2)
		 container += "<b >";
		  container += notes_data[i].text;
		 if(notes_data[i].system_admin == 1 || notes_data[i].priority==2)
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
	$("#notes_add").hide();
}
function addQuickNote() {
	var textOld = $("#notes_add textarea").val();
	
	var str = textOld + " " + $("#notes_add select").val();
	$("#notes_add textarea").val(str);
} 
/*
function openAddNotes(entity_id, notes_type) {
	add_entity_id = entity_id;
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
*/

var notesOpenFlag = 0;

function openAddNotes(entity_id, notes_type) {
	
	if(notesOpenFlag ==1 ){
	   $("#notes_add").hide();
	   notesOpenFlag = 0;
	   return;  
	}

	add_entity_id = entity_id;
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

//alert(entity_id+"---"+notes[notes_type][entity_id]);
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
					if(response.color == 1)
					   $("#notes_3_"+entity_id).removeClass("note-red").addClass("note-green");
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

	//if (notes_data.toString() == "") return;
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
    $("#notes_add_title").html("");
	$("#notes_container_new").html("");
	
    
	//$("#notes_container_new").append("<p>"+title+"<\/p>");
    var ENumber = '';
	for (i in notes_data) {

		var container = "";
		var contactname = notes_data[i].sender;
		var bgcolor = '#000000';	
		if(notes_data[i].system_admin == 2){
			 contactname = "FreightDragon";
		  }

		container += "<div><span class='note-from'><span style='font-weight: bold;color: #008EC2;'>"+notes_data[i].created+"</span>";
		if(notes_data[i].priority==2)
		   bgcolor = '#FF0000';

		container += "<\/span><br/><span class='note-data' style='color:"+bgcolor+"'>";

		if(notes_data[i].system_admin == 1 || notes_data[i].system_admin == 2 || notes_data[i].priority==2)
		 container += "<b >";
		  container += notes_data[i].text;

		 if(notes_data[i].system_admin == 1 || notes_data[i].system_admin == 2 || notes_data[i].priority==2)
		 container += "<\/b>";
		container += "<\/span>";
		if (notes_type != 2) {
			container += "<span style='font-weight: bold;color: #008EC2;'> Created by "+contactname+"</span>";
		}
		container += "<\/div><br>";

		$("#notes_container_new").append(container);
        ENumber = notes_data[i].number;
	}
	title = title+' '+ENumber;
	$("#notes_add_title").append("<p>"+title+"<span onclick=\"$('#notes_add').hide();notesOpenFlag = 0;\" style='float:right; color:red;'><img src='/images/icons/delete.png'  class='pointer' width='16' height='16'></span><\/p>");
	
	$("#notes_add textarea").val("");
	$("#notes_add").show();
	notesOpenFlag = 1;
	if (($("#notes_add").height() + $("#notes_add").position().top) > ($(window).height() + $(window).scrollTop() - 20)) {
		$("#notes_add").css("top", $(window).height() + $(window).scrollTop() - $("#notes_add").height() - 20);
	}
}


function addNote() {
	if (add_busy) return;
	add_busy = true;
	var note_text =  $.trim($("#notes_add textarea").val());
	var priority = $.trim($("#priority_notes").val());
	if (note_text != "") {
		$.ajax({
			type: 'POST',
			url: BASE_PATH+'application/ajax/notes.php',
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
					$("#notes_"+add_notes_type+"_"+add_entity_id).html(parseInt($("#notes_"+add_notes_type+"_"+add_entity_id).text()) + 1);
					$("#notes_"+add_notes_type+"_"+add_entity_id).removeClass("note-grey");
					$("#notes_"+add_notes_type+"_"+add_entity_id).addClass("note-red");
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
    $("#acc_search_dialog").dialog({width: 550},{title:'Select Carrier'}).dialog('open');
}

function selectCarrierNewDispatch() {
	if ($("input[name='order_id']:checked").size() == 0) {
            $(".alert-message").empty();
			$(".alert-message").text("Select Order to Dispatch");
			$(".alert-pack").show();
			return false;        
        }
		if ($("input[name='order_id']:checked").size() > 1) {
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
    $("#acc_global_search_dialog").dialog('option', 'title', 'SelectCarrier').dialog('open');
}

function selectShipper() {
    $("#acc_search_result").html("");
    $("#acc_search_string").val("");
    acc_type = 2;
    $("#acc_search_dialog").dialog( {title:'Select Shipper'},{width: 550}).dialog('open');
}


function selectTerminal(location) {
    acc_location = location;
    $("#acc_search_result").html("");
    $("#acc_search_string").val("");
    acc_type = 3;
    $("#acc_search_dialog").dialog({width: 550},{title:'Select Terminal'}).dialog('open');
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
				
				
				var bgColor = ' bgcolor="#cccccc" ';
				
				
				   
				var AccRows = '<tr><td '+bgColor+'></td>';
				if(acc_type==2) 
				    AccRows += '<td '+bgColor+'>Name</td>';
				
				 AccRows += '<td '+bgColor+'>Company</td><td '+bgColor+'>Address</td><td '+bgColor+'>Phone</td>';
				        
						if(acc_type==3) {
					         AccRows +='<td '+bgColor+'>City</td>';
						 }
						else
						 {
				           AccRows +='<td '+bgColor+'>Email</td>';
						 }
				AccRows +='<td '+bgColor+'>State</td></tr>';
				
					for (i in acc_data) {
						var bgColorRows = '';
						if(acc_data[i].donot_dispatch == 1)
				           bgColorRows = ' bgcolor="#ff6600" ';
				   
                        var cssClass = acc_data[i].expired ? "expired" : "";
						
						AccRows += '<tr><td '+bgColorRows+'><input type="radio" name="acc_search_result_item" class="form-box-radio" id="acc_search_result_item'+i+'" value="'+i+'"/></td><td '+bgColorRows+'>';
						
					if(acc_type==2) 
				     {
						if(acc_data[i].first_name!='')
						  AccRows += acc_data[i].first_name;
						if(acc_data[i].last_name!='')
						  AccRows += " "+acc_data[i].last_name;
						  
						AccRows += '</td><td '+bgColorRows+'>';
					 }
						if(acc_data[i].company_name!='')
						  AccRows += acc_data[i].company_name;
						  
						AccRows += '</td><td '+bgColorRows+'>';  
						
						if(acc_data[i].address1!='') 
						  AccRows += acc_data[i].address1;
						  
						 AccRows += '</td><td '+bgColorRows+'>';
						 
						if(acc_data[i].phone1!='')   
						  AccRows +=acc_data[i].phone1;
						  
						 AccRows += '</td><td '+bgColorRows+'>';
						
						if(acc_type==3) {
							if(acc_data[i].city!='')   
						      AccRows +=acc_data[i].city;
						}
						else
						{
						  if(acc_data[i].email!='')   
						     AccRows +='<a href="javascript:void(0);" alt="'+acc_data[i].email+'" title="'+acc_data[i].email+'">View</a>'; 
						    //AccRows +=acc_data[i].email; 
						}
						
						AccRows += '</td><td '+bgColorRows+'>';
						
						if(acc_data[i].state!='')   
						  AccRows +=acc_data[i].state;   
						  
						AccRows += '</td><tr>';
						/*
                       // var label = '<label class="' + cssClass + '" for="acc_search_result_item'+i+'">'+acc_data[i].company_name+'</label>';
						 var label = '<label class="' + cssClass + '" for="acc_search_result_item'+i+'">';
						 
						if(acc_data[i].first_name!=null)
						  label += acc_data[i].first_name + ' | '; 
						if(acc_data[i].company_name!='')
						  label += acc_data[i].company_name+' | ';
						if(acc_data[i].address1!='') 
						  label +=acc_data[i].address1+' | ';
						if(acc_data[i].phone1!='')   
						  label +=acc_data[i].phone1+'';
						label += ' </label>';
						
						$("#acc_search_result").append('<li><input type="radio" name="acc_search_result_item" class="form-box-radio" id="acc_search_result_item'+i+'" value="'+i+'"/>' + label + '</li>');
					    */
					}
					var typeName = "";
					if(acc_type == 1)
					   typeName = "Carrier";
					else if(acc_type == 2)
					   typeName = "Shipper"; 
					else if(acc_type == 3)
					   typeName = "Location";   
					   
					//$("#acc_search_dialog").dialog({title:'Carrier List > '+searchText+' Match Results'});
					$("#acc_search_result").append('<table width="100%" style="border:1px solid #cccccc;">'+AccRows+'</table><br><b style="color:#ff6600;">Highlighted '+typeName+' is not allowed.</b><br>');
					//$("#acc_search_text").append('<b>Search Term:</b>'+searchText+'');
				} else {
					//$("#acc_search_dialog").dialog({title:'Carrier List > '+searchText+' Match Results'});
					//$("#acc_search_text").append('<b>Search Term:</b>'+searchText+'');
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


function accountSearchNewDispatch() {
    $("#acc_search_result_new_dispatch").html('');
    var searchText = $.trim($("#acc_search_string_new_dispatch").val());
	//alert(searchText);
    if (searchText == '') return;
    $(".acc_search_dialog_new_dispatch").nimbleLoader('show');
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
				
				var bgColor = ' bgcolor="#cccccc" ';
				var AccRows = '<tr><td '+bgColor+'></td>';
				if(acc_type==2) 
				    AccRows += '<td '+bgColor+'>Name</td>';
				
				 AccRows += '<td '+bgColor+'>Company</td><td '+bgColor+'>Address</td><td '+bgColor+'>Phone</td>';
				        
						if(acc_type==3) {
					         AccRows +='<td '+bgColor+'>City</td>';
						 }
						else
						 {
				           AccRows +='<td '+bgColor+'>Email</td>';
						 }
				AccRows +='<td '+bgColor+'>State</td></tr>';
				
					for (i in acc_data) {
						var bgColorRows = '';
						if(acc_data[i].donot_dispatch == 1)
				           bgColorRows = ' bgcolor="#ff6600" ';
						   
                        var cssClass = acc_data[i].expired ? "expired" : "";
						
						AccRows += '<tr><td '+bgColorRows+'><input type="radio" name="acc_search_result_item" class="form-box-radio" id="acc_search_result_item'+i+'" value="'+i+'"/></td><td '+bgColorRows+'>';
						
					if(acc_type==2) 
				     {
						if(acc_data[i].first_name!='')
						  AccRows += acc_data[i].first_name;
						if(acc_data[i].last_name!='')
						  AccRows += " "+acc_data[i].last_name;
						  
						AccRows += '</td><td '+bgColorRows+'>';
					 }
						if(acc_data[i].company_name!='')
						  AccRows += acc_data[i].company_name;
						  
						AccRows += '</td><td '+bgColorRows+'>';  
						
						if(acc_data[i].address1!='') 
						  AccRows += acc_data[i].address1;
						  
						 AccRows += '</td><td '+bgColorRows+'>';
						 
						if(acc_data[i].phone1!='')   
						  AccRows +=acc_data[i].phone1;
						  
						 AccRows += '</td><td '+bgColorRows+'>';
						
						if(acc_type==3) {
							if(acc_data[i].city!='')   
						      AccRows +=acc_data[i].city;
						}
						else
						{
						  if(acc_data[i].email!='')   
						     AccRows +='<a href="javascript:void(0);" alt="'+acc_data[i].email+'" title="'+acc_data[i].email+'">View</a>'; 
						    //AccRows +=acc_data[i].email; 
						}
						
						AccRows += '</td><td '+bgColorRows+'>';
						
						if(acc_data[i].state!='')   
						  AccRows +=acc_data[i].state;   
						  
						AccRows += '</td><tr>';
						
					}
					$("#acc_search_result_new_dispatch").append('<table width="100%" style="border:1px solid #cccccc;">'+AccRows+'</table>');
				} else {
					$("#acc_search_result_new_dispatch").append('No records found.');
				}
            } else {
                alert("Can't load account data. Try again later, please");
            }
        },
        error: function(response){
            alert("Can't load account data. Try again later, please");
        },
        complete: function(response){
            $(".acc_search_dialog_new_dispatch").nimbleLoader('hide');
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
function postToFBMultiple() {
	changeOrderStatusMultiple(4);
}

function unpostFromFB() {
	changeOrderStatus(1);
}

function setPickedUpStatusAndDate(status, pickupDate) {
	changeOrderStatusAndDate(status,pickupDate);
}
function setPickedUpStatusAndDateMultiple(status, pickupDate) {
	changeOrderStatusAndDateMultiple(status,pickupDate);
}

function setPickedUpStatusAndDateByEntity(status, pickupDate,entity_id) {
	changeOrderStatusAndDateByEntity(status,pickupDate,entity_id);
}

function setPickedUpStatus() {
	changeOrderStatus(8);
}

function setDeliveredStatus() {
	changeOrderStatus(9);
}

function changeOrderStatus(status) {
	if ($(".order-checkbox:checked").size() == 0) {
		$(".alert-message").empty();
		$(".alert-message").text("Order not selected");
		$(".alert-pack").show();
		return false;
	}
	
	var entity_id = "";        
	var entity_ids = [];       
	$(".order-checkbox:checked").each(function(){
		entity_id = $(this).val();
		entity_ids.push(entity_id);  
	});
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
				if(response.data!='')
				  alert(response.data);
				else
				  alert("Can't post order to Freight Board. Try again later, please");
			}
		},
		complete: function(response) {
			$("body").nimbleLoader('hide');
		}
	});
}

function changeOrderStatusMultiple(status) {
	if ($(".order-checkbox:checked").size() == 0) {
		$(".alert-message").empty();
		$(".alert-message").text("Order not selected");
		$(".alert-pack").show();
		return false;
	}
	
	var entity_id = "";        
	var entity_ids = [];       
	$(".order-checkbox:checked").each(function(){
		entity_id = $(this).val();
		entity_ids.push(entity_id);  
	});
	$("body").nimbleLoader('show');
	$.ajax({
		type: 'POST',
		url: BASE_PATH+"application/ajax/entities.php",
		dataType: 'json',
		data: {
			action: 'setStatusMultiple',
			entity_ids: entity_ids.join(","),
			status: status
		},
		success: function(response) {
			if (response.success) {
				
				if(status == 4)
                  $("#acc_entity_dialog_confirm").dialog('option', 'title', 'Post Loads confirmation').dialog('open');
				 //alert(status); 
				window.location.reload();
			} else {
				if(response.data!='')
				  alert(response.data);
				else
				  alert("Can't post order to Freight Board. Try again later, please");
			}
		},
		complete: function(response) {
			$("body").nimbleLoader('hide');
		}
	});
}

function changeOrderStatusAndDate(status,pickdate) {
	if (status == '') {
		alert("Order status not set for this button.");
		return;
	}
	
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
			action: 'setStatusAndDate',
			entity_id: entity_id,
			status: status,
			pickdate: pickdate
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

function changeOrderStatusAndDateMultiple(status,pickdate) {
	if (status == '') {
		alert("Order status not set for this button.");
		return;
	}
	
	if ($(".order-checkbox:checked").size() == 0) {
		$(".alert-message").empty();
		$(".alert-message").text("Order not selected");
		$(".alert-pack").show();
		return false;
	}
	
	var entity_id = "";        
	var entity_ids = [];       
	$(".order-checkbox:checked").each(function(){
		entity_id = $(this).val();
		entity_ids.push(entity_id);  
	});
	
	$("body").nimbleLoader('show');
	$.ajax({
		type: 'POST',
		url: BASE_PATH+"application/ajax/entities.php",
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
				alert("Try again later, please");
			}
		},
		complete: function(response) {
			$("body").nimbleLoader('hide');
		}
	});
}


function changeOrderStatusAndDateByEntity(status,pickdate,entity_id) {
	if (status == '') {
		alert("Order status not set for this button.");
		return;
	}
	
	if (entity_id == '') {
		alert("Order id not set for this button.");
		return;
	}
	
	$("body").nimbleLoader('show');
	$.ajax({
		type: 'POST',
		url: BASE_PATH+"application/ajax/entities.php",
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
					//alert('done');
                }
            }
        });
    }
}

function changeStatusLeads(status) {
    if ($(".entity-checkbox:checked").size() == 0) {
        alert("You have no selected items.");
    } else {
        var entity_ids = [];
        $(".entity-checkbox:checked").each(function(){
            entity_ids.push($(this).val());
        });
		$("#nimble_dialog_button").nimbleLoader('show');
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
					//alert('done');
                }
            },
			error: function(response) {
				$("#nimble_dialog_button").nimbleLoader('hide');
				alert("Try again later, please");
			},
			complete: function(response) {
				$("#nimble_dialog_button").nimbleLoader('hide');
			}
        });
    }
}

function changeStatusOrders(status) {
    if ($(".order-checkbox:checked").size() == 0) {
		$(".alert-message").empty();
		$(".alert-message").text("Order not selected");
		$(".alert-pack").show();
		return false;        
    }
    var entity_ids = [];

	$(".order-checkbox:checked").each(function(){
		var entity_id = $(this).val();
		entity_ids.push(entity_id);
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
var mail_flds_new = ["mail_to_new", "mail_subject_new", "mail_body_new"];


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
    if ( $('#mail_to_new').val() !=""){
        if (!checkEmailAddress($("#mail_to_new").val())){
            $('#mail_to_new').addClass("ui-state-error");
            ret = false;
        }
    }

    return ret;
}



function postOrderToFB(entity_id) {
	changeOrderStatusToFB(entity_id,4);
}

function unpostOrderFromFB(entity_id) {
	changeOrderStatusToFB(entity_id,1);
}

function changeOrderStatusToFB(entity_id,status) {
	if (entity_id == 0) {
		alert("Order not selected");
		return;
	}
	
	 
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
				if(response.data!='')
				  alert(response.data);
				else
				  alert("Can't post order to Freight Board. Try again later, please");
			}
		},
		complete: function(response) {
			$("body").nimbleLoader('hide');
		}
	});
}


function OLD_emailSelectedOrderFormNew() {
		
		if ($(".order-checkbox:checked").size() == 0) {
		   alert("Order not selected");
		   return;
	    }
	    var entity_id = $(".order-checkbox:checked").val();

        form_id = $("#email_templates").val();
        if (form_id == "") {
            alert("Please choose email template");
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
                    success: function (res) {
                        if (res.success) {
                            
							
							 $("#form_id").val(form_id);
							 $("#entity_id").val(entity_id);
							 $("#mail_to_new").val(res.emailContent.to);
							 $("#mail_subject_new").val(res.emailContent.subject);
							 $("#mail_body_new").val(res.emailContent.body);
							 
							  //$("#mail_file_name").html(file_name);
							 $("#maildivnew").dialog("open");
							
                        } else {
                            alert("Can't send email. Try again later, please");
                        }
                    },
                    complete: function (res) {
                        $("body").nimbleLoader('hide');
                    }
                });


        }
    }
	
function emailSelectedOrderFormNew() {
	if ($(".order-checkbox:checked").size() == 0) {
		$(".alert-message").empty();
		$(".alert-message").text("Order not selected.");
		$(".alert-pack").show();
		return false; 
	}

	var action  	= "";
	var entity_id 	= "";        
	var entity_ids 	= [];
	var sel_id		= "";
	var oids		= [];
	var shipper_name= [];
	var shipper_company= [];
	var shipper_number= [];
	var shipper_email= [];
	$(".order-checkbox:checked").each(function(){
		entity_id = $(this).val();
		entity_ids.push(entity_id);
		sel_id = $(this).parent().parent().attr('id');
		oids.push($("#"+sel_id+" .order_id").text());
		shipper_name.push($("#"+sel_id+" .shipper_name").text());
		shipper_company.push($("#"+sel_id+" .shipper_company").text());
		shipper_number.push($("#"+sel_id+" .shipper_number").text());
		shipper_email.push($("#"+sel_id+" .shipper_email").text());
	});
	console.log(oids);
	
	var tot_ids = entity_ids.length;

	if(tot_ids<2){
		entity_ids = $(".order-checkbox:checked").val();
		action = "emailOrderSingle";
	} else {
		entity_ids.join(',');
		action = "emailOrderBulk";
	}

	var form_id = $("#email_templates").val();
	if (form_id == ""){
		$(".alert-message").empty();
		$(".alert-message").text("Please choose email template.");
		$(".alert-pack").show();
		return false; 
	} else {
		$("body").nimbleLoader('show');
		$.ajax({
			type: "POST",
			url: BASE_PATH + "application/ajax/newentry.php",
			dataType: "json",
			data:{
				action: action,
				form_id: form_id,
				entity_id: entity_ids
			},
			success: function (res) {
				if(res.success=="single"){
					$("#form_id").val(form_id);
					$("#entity_id").val(entity_id);
					$("#mail_to_new").val(res.emailContent.to);
					$("#mail_subject_new").val(res.emailContent.subject);
					$("#mail_body_new").val(res.emailContent.body);
					 
					  //$("#mail_file_name").html(file_name);
					$("#maildivnew").dialog({width: 470},'option', 'title', 'Send Email').dialog("open");
					return false;
				} else if (res.success=="countEmails") {
					var countEmails = res.countEmails;
					
					//Mails content
					var mailcontent = "";
					var v 		= JSON.stringify(res.content, null, 4);
					var json 	= JSON.parse(v);
					
					for (j = 1; j <= countEmails; j++) {
						var count = j-1;
						var mail_to 		= json[count].to;
						var mail_subject 	= json[count].subject;
						var mail_from 		= json[count].from;
						var mail_fromname 	= json[count].fromname;
						var mail_bcc 		= json[count].bcc;
						var mail_body 		= json[count].body;
						
						mailcontent += '<div class="edit-mail-container" id="edit-mail-show_'+j+'"><div class="edit-mail-content"><div class="edit-mail-row"><div class="edit-mail-label">Email:<span>*</span></div><div class="edit-mail-field"><input type="text" id="mail_to_'+j+'" class="form-box-textfield" maxlength="255" name="mail_to" style="width:280px;" value="'+mail_to+'"></div><div class="edit-mail-extra">Add more</div></div><div class="edit-mail-row"><div class="edit-mail-label">Subject:<span>*</span></div><div class="edit-mail-field"><input type="text" id="mail_subject_'+j+'" class="form-box-textfield" maxlength="255" name="mail_subject" style="width:280px;" value="'+mail_subject+'"></div></div><div class="edit-mail-row"><div class="edit-mail-label">Body:<span>*</span></div><div class="edit-mail-field"><textarea name="mail_body" id="mail_body_'+j+'">'+mail_body+'</textarea></div></div></div><div class="edit-mail-button"><div class="edit-mail-action update">Update</div><div class="edit-mail-action cancel">Cancel</div></div></div>';
					}
					$(".editmail").empty();
					$(".editmail").html(mailcontent);
					
					//List mails with edit link
					var content = "";
					content = '<table cellspacing="0" cellpadding="0" border="0" width="100%"><tbody>';
					for (i = 1; i <= countEmails; i++) {
						var lesscount = i-1;
						if(i%2==0)
							var trclass = "grid-body-grey";
						else
							var trclass = "grid-body";
						content += '<tr class="'+trclass+'"><td class="order-id id-column" style="border-left:1px solid #d3d3d5;">'+oids[lesscount]+'</td><td class="shipper-column"><div>'+shipper_name[lesscount]+'<div><div>'+shipper_company[lesscount]+'<div><div>'+shipper_number[lesscount]+'<div><div>'+shipper_email[lesscount]+'<div></td><td class="action-column"><div class="go-to-edit" id="mail-edit-link_'+i+'">Edit</div></td></tr>';
					}
						
					content += '</tbody></table>';
					//content += '<div class="list-container"><div class="email-counter">Email </div><div class="go-to-edit" id="mail-edit-link_'+i+'">Edit</div></div>';
					content += '<div class="edit-mail-container2"><div class="edit-mail-button"><div class="edit-mail-action send-mail"><input type="hidden" id="count-total-mail" value="'+countEmails+'">Send Mail</div></div></div>';
					
					$(".repeat-column").empty();
					$(".repeat-column").html(content);
					$(".mail-list-label").show();
					$(".repeat-column").show();
					$("#listmails").dialog({width: 500}).dialog("open");
					
					/*var countEmails = res.countEmails;
					
					$("#listmails").dialog("open");
					$(".alert-message").empty();
					$(".alert-message").text(res.message);
					$(".alert-pack").show().delay("5000", function(){
						window.location.reload();
					});*/
					return false;
				} else {
					$(".alert-message").empty();
					$(".alert-message").text("Can't send email. Try again later.");
					$(".alert-pack").show();
					return false;
				}
			},
			complete: function (res) {
				$("body").nimbleLoader('hide');
			}
		});
	}
}

$('.go-to-edit').live('click' ,function(){
	 var selected_id = $(this).attr('id');
	 selected_id = selected_id.split("_");;
	 var id_count = selected_id[1];
	 $(".mail-list-label").hide();
	 $(".repeat-column").hide();
	 $("#ui-dialog-title-listmails").empty().text('Update message');
	 $(".editmail").show();
	 $("#edit-mail-show_"+id_count).show();
});

$('.cancel').live('click' ,function(){
	$(".editmail").hide();
	$(".edit-mail-container").hide();
	$("#ui-dialog-title-listmails").empty().text('Email List');
	$(".mail-list-label").show();
	$(".repeat-column").show();
});

$('.update').live('click' ,function(){
	$(".editmail").hide();
	$(".edit-mail-container").hide();
	$("#ui-dialog-title-listmails").empty().text('Email List');
	$(".mail-list-label").show();
	$(".repeat-column").show();
});

$('.send-mail').live('click' ,function(){
	var form_id = $("#email_templates").val();
	var count_total_mail = $("#count-total-mail").val();
	var mail_to = "";
	var content= [];
	for(i = 1; i <= count_total_mail; i++ ){
		var ad =  {'mail_to':$("#mail_to_"+i).val(), 'mail_subject':$("#mail_subject_"+i).val(), 'mail_body':$("#mail_body_"+i).val()};
		content.push(ad);
	}
	
	var entity_id = "";        
	var entity_ids = [];       
	$(".order-checkbox:checked").each(function(){
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
		success: function (response) {
			if(response.success){
				$(".alert-message").empty();
				$(".alert-message").text(response.message);
				$(".alert-pack").show().delay("5000", function(){
					window.location.reload();
				});
			}
		}
	});
});