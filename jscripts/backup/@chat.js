/*
JS for chat functions
*/
var windowFocus 		= true;
var username;
var chatHeartbeatCount 	= 0;
var minChatHeartbeat 	= 3000;
var maxChatHeartbeat 	= 33000;
var chatHeartbeatTime 	= minChatHeartbeat;
var originalTitle;
var blinkOrder 			= 0;

var minOnlinePulsebeat	= 90000;
var OnlinePulsebeatTime = minOnlinePulsebeat;

var minOfflinePulsebeat	= 60000;
var OfflinePulsebeatTime = minOfflinePulsebeat;

var chatboxFocus 		= new Array();
var newMessages 		= new Array();
var newMessagesWin 		= new Array();
var chatBoxes 			= new Array();
var BASEURL				= "https://freightdragon.com/";
var URL					= "https://freightdragon.com/application/";


$(document).ready(function(){
	
	originalTitle = document.title;
	startChatSession();

	$([window, document]).blur(function(){
		windowFocus = false;
	}).focus(function(){
		windowFocus = true;
		document.title = originalTitle;
	});
	
	$("#onlineUserCounter").click(function(){
		$(this).toggleClass( "bottom250" );
		$( "#userlists" ).toggle();
	});
	
	$('.chatboxcontent').live('hover', function() {
		var parentId = $('.chatboxcontent').parent().attr('id');
		var userId = $('.chatboxcontent').siblings('span');
		userId	= userId.text();
		var el = $(this);
		var height = el.scrollTop();
		if (!el.data("has-scroll")) {
			el.data("has-scroll", true);
			el.scroll(function(){
				var scrollLimit = $.cookie('cookieScrollLimit');
				var d = el.scrollTop();
				if(d=='0' && (scrollLimit=='0' || scrollLimit== null)){
					$.cookie('cookieScrollLimit','1');
					chatHistory(parentId,userId,height);
				}
			});
		}
	});	
	
});



//**********************************************************************************************************
// restructureChatBoxes
//**********************************************************************************************************
function restructureChatBoxes(status) {
	align = 0;
	if(status == 'online' || status == '1'){
		var class_status = "onlinedot";
	} else {
		var class_status = "offlinedot";
	}		
	for (x in chatBoxes) {
		chatboxtitle = chatBoxes[x];
		
		$("#chatbox_"+chatboxtitle+" .online-status-icon").removeClass().addClass("online-status-icon "+class_status); 
		if ($("#chatbox_"+chatboxtitle).css('display') != 'none') {
			if (align == 0) {
				$("#chatbox_"+chatboxtitle).css('right', '250px');
			} else {
				width = (align)*(228)+250;
				$("#chatbox_"+chatboxtitle).css('right', width+'px');
			}
			align++;
		}
	}
}



//**********************************************************************************************************
// Get users details 
//**********************************************************************************************************
function getuserdetails(countNumber, ids){
	var countNumber 	= countNumber;
	var idsnow			= ids;
	var idsForDetail 	= JSON.stringify(idsnow);
	var countuser 		= ids.userid.length;
	var userId	 		= "";
	var counter			= 1;
	var onlineUsers		= "";
	var offlineUsers	= "";
	var userIdArray		= new Array();
	var countOnline		= 0;
				

	//Get on-line users details
	if(countuser>0){
		for (i = 0; i < countuser; i++) {
			userIdArray[i] = ids.userid[i];
			userId					+= ids.userid[i];
			if(counter<countuser){	
				userId	+= ",";
				counter++;
			}
		}
		
		$.ajax({
			url: URL+"chat/getuserdetails",
			data: {ids:userId},
			dataType: "json",
			async: false,
			success: function(array) {
				$.each(array, function(i,item){
					var userid			= item.id;
					var name			= item.name;
					var chatname		= "'"+item.chatname+"'";
					var onlinestatus	= item.onlinestatus;
					var status			= "";
					
					if ($.cookie('chatWindowCookie')){
						var CWCookieVal 	= $.cookie('chatWindowCookie');
						searchArr = CWCookieVal.split(/\|/);
						for (var i = 0; i < searchArr.length; i++) {
							if (searchArr[i] == userid) {
								if($("#chatbox_"+item.chatname).length == 0){
									chatWith(userid,item.chatname,onlinestatus);
								}		
							}
						}
					}
					
					if(onlinestatus=='1'){
						countOnline++;
						status = "'online'";
						$.cookie('user-'+item.chatname+'-status','online');
						onlineUsers += '<div class="chatuserslist"><div class="online-status-icon onlinedot">&nbsp;</div><div class="chatboxcountertitle" onclick="chatWith('+userid+', '+chatname+','+status+');">'+name+'</div><br clear="all"/></div>';
					}else{
						status = "'offline'";
						$.cookie('user-'+item.chatname+'-status','offline');
						offlineUsers += '<div class="chatuserslist"><div class="online-status-icon offlinedot">&nbsp;</div><div class="chatboxcountertitle" onclick="chatWith('+userid+', '+chatname+','+status+');">'+name+'</div><br clear="all"/></div>';
					}
					$.cookie('chatbox_'+item.chatname+'-limit','0');
				});
				if(offlineUsers	!= "")
					var divider = '<div class="chatboxdivider">------------ OFFLINE USERS ------------</div>';
				
				var contetnt = onlineUsers + divider + offlineUsers;
				if($("#userlists").length == 0) {
					$(" <div />" ).attr("id","userlists").addClass("counterbox").html(contetnt).appendTo($( "body" ));
					$("#userlists").attr('style','bottom: 0px; right: 0px; max-height: 200px; overflow-y: scroll; display:none;');
				} else {
					$("#userlists").empty().html(contetnt);
				}
				
					
			},error: function(){
				alert('There is an error. Reload the page!');
			}
		});
	}
	
	if($("#onlineUserCounter").length == 0) {
		$(" <div />" ).attr("id","onlineUserCounter")
		.addClass("counterbox")
		.html('<div class="chatboxcounter"><div class="bell-icon">&nbsp;</div><div class="user-icon">&nbsp;</div><div class="online-status-icon onlinedot">&nbsp;</div><div class="chatboxcountertitle">Chat <span>(</span>'+countNumber+'<span>)</span></div><div class="chatboxoptions"></div><br clear="all"/></div>')
		.appendTo($( "body" ));
	} else {
		$("#onlineUserCounter .chatboxcountertitle").empty().html('Chat <span>(</span>'+countOnline+'<span>)</span>');
	}
	var functionToCall 	= 'getuserdetails('+countNumber+','+idsForDetail+')';
	setTimeout(functionToCall,OnlinePulsebeatTime); 
}



//**********************************************************************************************************
// chatWith
//**********************************************************************************************************
function chatWith(userid,chatuser,status) {
	createChatBox(userid,chatuser,status);
	$("#chatbox_"+chatuser+" .chatboxtextarea").focus();
}



//**********************************************************************************************************
// createChatBox
//**********************************************************************************************************
function createChatBox(userid,chatboxtitle,status,minimizeChatBox) {
	var userid	= userid;
	var name 	= chatboxtitle;
	var status 	= status;
	var content	= "";
	
	//$.cookie('chatWindowCookie','');
	//console.log("START_CB= "+$.cookie('chatWindowCookie'));
	 
	var newCookie 		= userid;
	if ($.cookie('chatWindowCookie')){
		var countIdExist 	= 0;
		var CWCookieVal 	= $.cookie('chatWindowCookie');
		searchArr = CWCookieVal.split(/\|/);
		for (var i = 0; i < searchArr.length; i++) {
		 	if (searchArr[i] == userid) {
				countIdExist++;		
			}
		}
		
		if(countIdExist==0){
			CWCookieVal += '|'+newCookie;
		}
		$.cookie('chatWindowCookie',CWCookieVal);
	} else {
		$.cookie('chatWindowCookie',newCookie);
	}
	
	//console.log("END_CB= "+$.cookie('chatWindowCookie'));
	
	name 		= name.replace(/_/g, ' ');
	if ($("#chatbox_"+chatboxtitle).length > 0) {
		if ($("#chatbox_"+chatboxtitle).css('display') == 'none') {
			$("#chatbox_"+chatboxtitle).css('display','block');
			restructureChatBoxes();
		}
		$("#chatbox_"+chatboxtitle+" .chatboxtextarea").focus();
		return;
	}
	
	if(status == 'online' || status == '1'){
		var class_status = "onlinedot";
	} else {
		var class_status = "offlinedot";
	}
	var classChatboxhead 	= 'chatboxhead';
	var classChatboxoptions = 'chatboxoptions';
			
	var cookieLimitName = 'chatbox_'+chatboxtitle+'-limit';
	var cookieLimit	= $.cookie(cookieLimitName);
	if(cookieLimit > 0){
		var limit = cookieLimit;
	} else {
		var limit = 0;
	}
	
	//get last 6 message
	if ($("#chatbox_"+chatboxtitle).length <= 0) {
		var todayCount  = 0;
		var otherCount  = 0;		
		$.cookie('cookieDateDivider','0000-00-00');
		
		$.ajax({
			url: URL+"chat/chathistory",
			data: {id:userid, limit:limit},
			cache: false,
			dataType: "json",
			async: false,
			success: function(data) {
				username = data.username;
				$.each(data.items, function(i,item){
					if (item)	{ // fix strange ie bug
						var	uid	= item.tid;
						var time 	= item.t;
						var chatbox_title 		= item.f;
						chatbox_title = chatbox_title.replace(/ /g, '_');
						var msg = item.m;
						
						var db_date	= item.date;
						var DateArr = db_date.split("-");
						var d 		= new Date();
						var Year	= d.getFullYear();
						var Month	= d.getMonth();
						var Dates	= ("0" + d.getDate()).slice(-2); 
						var one		= 1;
						Month		= ("0" + (d.getMonth() + 1)).slice(-2);
						
						var today	= Year+'-'+Month+'-'+Dates; 
						var dateDivider = $.cookie('cookieDateDivider');
						if(db_date == today && todayCount == 0){
							content += '<div class="messageDateSeprate">---------- TODAY ----------</div>';
							todayCount++;
						} else if(db_date != dateDivider && otherCount == 0){
							content += '<div class="messageDateSeprate">---------- '+DateArr[1]+'/'+DateArr[2]+'/'+DateArr[0]+' ----------</div>';
							otherCount++;
							$.cookie('cookieDateDivider',db_date);
						} else {
							otherCount = 0;
						}
						
						if(chatbox_title == username){
							content += '<div class="chatcontainer"><div class="chatboxmessage sky"><span class="chatboxmessagecontent">'+msg+'</span></div><div class="sendingtimeright">'+time+'</div></div>';
						} else {
							content += '<div class="chatcontainer"><div class="chatboxmessage white"><span class="chatboxmessagecontent">'+msg+'</span></div><div class="sendingtimeleft">'+time+'</div></div>';
						}
					}
				});
				cookieLimit = (cookieLimit + 10);
				$.cookie(cookieLimitName,cookieLimit);
				
				$(" <div />" ).attr("id","chatbox_"+chatboxtitle).addClass("chatbox").html('<span class="hiddenuserid">'+userid+'</span><div class="'+classChatboxhead+'"><div class="chatboxtitle" onclick="toggleChatBoxGrowth(\''+chatboxtitle+'\');"><div class="online-status-icon '+class_status+'">&nbsp;</div>'+name+'</div><div class="'+classChatboxoptions+'"><a href="javascript:void(0)" onclick="javascript:closeChatBox(\''+chatboxtitle+'\')">x</a></div><br clear="all"/></div><div class="chatboxcontent"><span class="chat-load"><img src="'+BASEURL+'images/chat-loader.gif"></span>'+content+'</div><div class="chatboxinput"><textarea class="chatboxtextarea" onkeydown="javascript:return checkChatBoxInputKey(event,this,\''+chatboxtitle+'\',\''+userid+'\');"></textarea></div>').appendTo($( "body" ));
				
				$("#chatbox_"+chatboxtitle+" .chatboxcontent").scrollTop($("#chatbox_"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);
			},error: function(){
				alert('There is some error!');
			}
		});
	}
	$("#chatbox_"+chatboxtitle).css('bottom', '0px');
	
	chatBoxeslength = 0;

	for (x in chatBoxes) {
		if ($("#chatbox_"+chatBoxes[x]).css('display') != 'none') {
			chatBoxeslength++;
		}
	}
	
	if (chatBoxeslength == 0) {
		$("#chatbox_"+chatboxtitle).css('right', '250px');
	} else {
		width = (chatBoxeslength)*(228)+250;
		$("#chatbox_"+chatboxtitle).css('right', width+'px');
	}
	
	chatBoxes.push(chatboxtitle);
	
	searchArr = new Array();
	if ($.cookie('chatbox_minimized')) {
		minimizedChatBoxes = new Array();
		minimizedChatBoxes = $.cookie('chatbox_minimized').split(/\|/);
		
		minimize = 0;
		for (j=0;j<minimizedChatBoxes.length;j++) {
			if (minimizedChatBoxes[j] == chatboxtitle) {
				minimize = 1;
			}
		}

		if (minimize == 1) {
			$('#chatbox_'+chatboxtitle+' .chatboxhead').removeClass().addClass('chatboxhead grey');
			$('#chatbox_'+chatboxtitle+' .chatboxoptions').removeClass().addClass('chatboxoptions black');
			$('#chatbox_'+chatboxtitle+' .chatboxcontent').css('display','none');
			$('#chatbox_'+chatboxtitle+' .chatboxinput').css('display','none');
			$('#chatbox_'+chatboxtitle+' .historyLink').css('display','none');	
		}
	}
	
	if (minimizeChatBox == 1) {
		minimizedChatBoxes = new Array();

		if ($.cookie('chatbox_minimized')) {
			minimizedChatBoxes = $.cookie('chatbox_minimized').split(/\|/);
		}
		
		minimize = 0;
		for (j=0;j<minimizedChatBoxes.length;j++) {
			if (minimizedChatBoxes[j] == chatboxtitle) {
				minimize = 1;
			}
		}

		if (minimize == 1) {
			$('#chatbox_'+chatboxtitle+' .chatboxcontent').css('display','none');
			$('#chatbox_'+chatboxtitle+' .chatboxinput').css('display','none');
			$('#chatbox_'+chatboxtitle+' .historyLink').css('display','none');	
		}
	}

	chatboxFocus[chatboxtitle] = false;

	$("#chatbox_"+chatboxtitle+" .chatboxtextarea").blur(function(){
		chatboxFocus[chatboxtitle] = false;
		$("#chatbox_"+chatboxtitle+" .chatboxtextarea").removeClass('chatboxtextareaselected');
	}).focus(function(){
		chatboxFocus[chatboxtitle] = true;
		newMessages[chatboxtitle] = false;
		$('#chatbox_'+chatboxtitle+' .chatboxhead').removeClass('chatboxblink');
		$("#chatbox_"+chatboxtitle+" .chatboxtextarea").addClass('chatboxtextareaselected');
	});

	$("#chatbox_"+chatboxtitle).click(function() {
		if ($('#chatbox_'+chatboxtitle+' .chatboxcontent').css('display') != 'none') {
			$("#chatbox_"+chatboxtitle+" .chatboxtextarea").focus();
		}
	});

	$("#chatbox_"+chatboxtitle).show();
	///////////////
	//$("#chatbox_"+chatboxtitle).draggable();
	//$(".chatbox_").draggable();
	///////////
}



//**********************************************************************************************************
// chatHeartbeat
//**********************************************************************************************************
function chatHeartbeat(){
	var itemsfound = 0;
	if (windowFocus == false) {
		var blinkNumber = 0;
		var titleChanged = 0;
		for (x in newMessagesWin) {
			if (newMessagesWin[x] == true) {
				++blinkNumber;
				if (blinkNumber >= blinkOrder) {
					document.title = x+' says...';
					titleChanged = 1;
					break;	
				}
			}
		}
		
		if (titleChanged == 0) {
			document.title = originalTitle;
			blinkOrder = 0;
		} else {
			++blinkOrder;
		}

	} else {
		for (x in newMessagesWin) {
			newMessagesWin[x] = false;
		}
	}

	for (x in newMessages) {
		if (newMessages[x] == true) {
			if (chatboxFocus[x] == false) {
				//FIXME: add toggle all or none policy, otherwise it looks funny
				$('#chatbox_'+x+' .chatboxhead').toggleClass('chatboxblink');
				//$('#chatbox_'+x+' .chatboxoptions').toggleClass('black');
			}
		}
	}
	
	$.ajax({
		url: URL+"chat/chatheartbeat",
		cache: false,
		dataType: "json",
		success: function(data) {
		username = data.username;
		$.each(data.items, function(i,item){
			if (item){ // fix strange ie bug
				var	userid	= item.tid;
				var time 	= item.t;
				var str 	= item.f;
				name 		= str;
				chatboxtitle = name.replace(/ /g, '_');
				
				var msg = item.m;
				var status = item.online_status;
				
				if ($("#chatbox_"+chatboxtitle).length <= 0) {
					createChatBox(userid, chatboxtitle, status);
				}
				if ($("#chatbox_"+chatboxtitle).css('display') == 'none') {
					$("#chatbox_"+chatboxtitle).css('display','block');
					restructureChatBoxes(status);
				}
				if($("#chatbox_"+chatboxtitle).length > 0){
					if (item.s == 2) {
						$("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatcontainer"><div class="chatboxmessage white"><span class="chatboxinfo">'+msg+'</span><div class="sendingtimeleft">'+time+'</div></div>');
					} else {
						newMessages[chatboxtitle] = true;
						newMessagesWin[chatboxtitle] = true;
						$("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatcontainer"><div class="chatboxmessage white"><span class="chatboxmessagecontent">'+msg+'</span></div><div class="sendingtimeleft">'+time+'</div></div>');
					}
					
					$("#chatbox_"+chatboxtitle+" .chatboxcontent").scrollTop($("#chatbox_"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);
					
				}
				
				
				/*if (item.s == 1) {
					item.f = username;
					item.f = "You";
				}*/
				
				itemsfound += 1;
			}
		});

		chatHeartbeatCount++;

		if (itemsfound > 0) {
			chatHeartbeatTime = minChatHeartbeat;
			chatHeartbeatCount = 1;
		} else if (chatHeartbeatCount >= 10) {
			chatHeartbeatTime *= 2;
			chatHeartbeatCount = 1;
			if (chatHeartbeatTime > maxChatHeartbeat) {
				chatHeartbeatTime = maxChatHeartbeat;
			}
		}
		
		setTimeout('chatHeartbeat();',chatHeartbeatTime);
	},error: function(){
		alert('error!');
	}});
}



//**********************************************************************************************************
// Chat History
//**********************************************************************************************************
function chatHistory(parentid,userid,height){
	var parentId	= parentid;
	var userid 		= userid;
	var itemsfound  = 0;
	var todayCount  = 0;
	var otherCount  = 0;			
	var contnet = "";
	
	var cookieLimitName = parentId+'-limit';
	var cookieLimit	= $.cookie(cookieLimitName);
	if(cookieLimit > 0){
		var limit = cookieLimit;
	} else {
		var limit = 0;
	}
	
	$.ajax({
		url: URL+"chat/chathistory",
		data: {id:userid, limit:limit},
		cache: false,
		dataType: "json",
		beforeSend:function(){
			//console.log('SHOW INDICATOR');
			$(".chat-load").show();
		},
		complete:function(){
			//console.log('HIDE INDICATOR');
			$(".chat-load").delay(2000).hide();
		},
		success: function(data) {
			username = data.username;
			$.each(data.items, function(i,item){
				if (item)	{ // fix strange ie bug
					var	userid	= item.tid;
					var time 	= item.t;
					var str 	= item.f;
					name 		= str;
					chatboxtitle = name.replace(/ /g, '_');
					
					var msg = item.m;
					
					if ($("#chatbox_"+chatboxtitle).css('display') == 'none') {
						$("#chatbox_"+chatboxtitle).css('display','block');
						restructureChatBoxes();
					}
					
					var db_date	= item.date;
					var DateArr = db_date.split("-");
					var d 		= new Date();
					var Year	= d.getFullYear();
					var Month	= d.getMonth();
					var Dates	= ("0" + d.getDate()).slice(-2); 
					var one		= 1;
					Month		= ("0" + (d.getMonth() + 1)).slice(-2);
					var today	= Year+'-'+Month+'-'+Dates; 
					var dateDivider = $.cookie('cookieDateDivider');
						
					if(db_date == today && todayCount == 0){
						contnet += '<div class="messageDateSeprate">---------- TODAY ----------</div>';
						//$("#"+parentId+" span.chat-load").after('<div class="messageDateSeprate">---------- TODAY ----------</div>');
						todayCount++;
					} else if(db_date != dateDivider && otherCount == 0){
						contnet += '<div class="messageDateSeprate">---------- '+DateArr[1]+'/'+DateArr[2]+'/'+DateArr[0]+' ----------</div>';
						otherCount++;
						$.cookie('cookieDateDivider',db_date);
					} else {
						otherCount = 0;
					}
					
					if(chatboxtitle == username){
						contnet += '<div class="chatcontainer"><div class="chatboxmessage sky"><span class="chatboxmessagecontent">'+msg+'</span></div><div class="sendingtimeright">'+time+'</div></div>';
					} else {
					    contnet += '<div class="chatcontainer"><div class="chatboxmessage white"><span class="chatboxmessagecontent">'+msg+'</span></div><div class="sendingtimeleft">'+time+'</div></div>';
					}
				}
			});
			$("#"+parentId+" span.chat-load").after(contnet);
			//$("#"+parentId+" .chatboxcontent").prepend('<span class="chat-load"><img src="'+BASEURL+'images/chat-loader.gif"></span>');
			var ten = 10;
			cookieLimit = +cookieLimit + +ten;
			$.cookie(cookieLimitName,cookieLimit);
			$.cookie('cookieScrollLimit','0');
			$("#"+parentId+" .chatboxcontent").scrollTop('300');
			//$("#"+parentId+" .chatboxcontent").scrollTop($("#"+parentId+" .chatboxcontent")[0].scrollHeight);
		},error: function(){
			alert('error!');
		}
	});
}



//**********************************************************************************************************
// closeChatBox
//**********************************************************************************************************
function closeChatBox(chatboxtitle) {
	$('#chatbox_'+chatboxtitle).css('display','none');
	restructureChatBoxes();
	

	var closeId = $('#chatbox_'+chatboxtitle+' .hiddenuserid').text();
	
	if ($.cookie('chatWindowCookie')) {
		var newCookie 		= closeId;
		var countIdExist 	= 1;
		var NCWCookieVal	="";	
		var CWCookieVal 	= $.cookie('chatWindowCookie');
		searchArr = CWCookieVal.split(/\|/);
		for (var i = 0; i < searchArr.length; i++) {
		 	if (searchArr[i] == closeId) {
				countIdExist++;
				continue;
			} else {
				NCWCookieVal += 	searchArr[i];
				if(countIdExist<searchArr.length){
					NCWCookieVal += '|';
				}
				countIdExist++;				
			}
		}
		$.cookie('chatWindowCookie',NCWCookieVal);
	} else{
		$.cookie('chatWindowCookie','');
	}
	//togglechatbox cookie
	if ($.cookie('chatbox_minimized')) {
		
			console.log("minimized");
			var newCookie= chatboxtitle;
			var countIdExist 	= 1;
			var NChatMinimizedCookieVal	="";	
			//var ChatMinimizedCookieVal 	= $.cookie('chatbox_minimized');
			var minimizedChatBoxes = $.cookie('chatbox_minimized').split(/\|/);
			for (var i=0;i<minimizedChatBoxes.length;i++) {
			if (minimizedChatBoxes[i] == chatboxtitle) {
			countIdExist++
			continue;
			}
			else
			{
			NChatMinimizedCookieVal += 	minimizedChatBoxes[i];
				if(countIdExist<minimizedChatBoxes.length){
				 NChatMinimizedCookieVal += '|';
				//NChatMinimizedCookieVal = ;
					//NChatMinimizedCookieVal += '|';
					
				}
				countIdExist++
			}
		}
		//NChatMinimizedCookieVal = NChatMinimizedCookieVal.slice(0, -1)
		$.cookie('chatbox_minimized',NChatMinimizedCookieVal);
		}
	$.post(URL+"chat/closechat", { chatbox: chatboxtitle} , function(data){	
	});
}



//**********************************************************************************************************
// toggleChatBoxGrowth
//**********************************************************************************************************
function toggleChatBoxGrowth(chatboxtitle) {
	$('#chatbox_'+chatboxtitle+' .chatboxhead').toggleClass('grey');
	$('#chatbox_'+chatboxtitle+' .chatboxoptions').toggleClass('black');
	
	if ($('#chatbox_'+chatboxtitle+' .chatboxcontent').css('display') == 'none') {  
		//console.log('if part');
		//var minimizedChatBoxes = new Array();
		
		if ($.cookie('chatbox_minimized')) {
			var newCookie= chatboxtitle;
			var countIdExist 	= 1;
			var NChatMinimizedCookieVal	="";	
			//var ChatMinimizedCookieVal 	= $.cookie('chatbox_minimized');
			var minimizedChatBoxes = $.cookie('chatbox_minimized').split(/\|/);
			for (var i=0;i<minimizedChatBoxes.length;i++) {
				if (minimizedChatBoxes[i] == chatboxtitle) {
					countIdExist++
					continue;
				}else{
					NChatMinimizedCookieVal += 	minimizedChatBoxes[i];
					if(countIdExist<minimizedChatBoxes.length){
						 NChatMinimizedCookieVal += '|';
						//NChatMinimizedCookieVal = ;
						//NChatMinimizedCookieVal += '|';
					}
					countIdExist++
				}
			}
			//NChatMinimizedCookieVal = NChatMinimizedCookieVal.slice(0, -1)
			$.cookie('chatbox_minimized',NChatMinimizedCookieVal);
		} else{
			$.cookie('chatbox_minimized', newCookie);
		}
		$('#chatbox_'+chatboxtitle+' .chatboxcontent').css('display','block');
		$('#chatbox_'+chatboxtitle+' .chatboxinput').css('display','block');
		$('#chatbox_'+chatboxtitle+' .historyLink').css('display','block');		
		$("#chatbox_"+chatboxtitle+" .chatboxcontent").scrollTop($("#chatbox_"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);
	} else {
		var newCookie = chatboxtitle;
		if ($.cookie('chatbox_minimized')) {
			var countIdExist 	= 0;
			var ChatMinimizedCookieVal 	= $.cookie('chatbox_minimized');
			searchArr = ChatMinimizedCookieVal.split(/\|/);
			for (var i = 0; i < searchArr.length; i++) {
				if (searchArr[i] == chatboxtitle) {
					countIdExist++;		
				}
			}
			if(countIdExist==0){
				newCookie += '|'+$.cookie('chatbox_minimized');
			}
			
			$.cookie('chatbox_minimized',ChatMinimizedCookieVal);
			//newCookie += '|'+$.cookie('chatbox_minimized');
		}

		$.cookie('chatbox_minimized',newCookie);
		
		$('#chatbox_'+chatboxtitle+' .chatboxcontent').css('display','none');
		$('#chatbox_'+chatboxtitle+' .chatboxinput').css('display','none');
		$('#chatbox_'+chatboxtitle+' .historyLink').css('display','none');
	}
	
}



//**********************************************************************************************************
// checkChatBoxInputKey
//**********************************************************************************************************
function checkChatBoxInputKey(event,chatboxtextarea,chatboxtitle,userid) {
	if(event.keyCode == 13 && event.shiftKey == 0)  {
		message = $(chatboxtextarea).val();
		message = message.replace(/^\s+|\s+$/g,"");

		$(chatboxtextarea).val('');
		$(chatboxtextarea).focus();
		$(chatboxtextarea).css('height','30px');
		if (message != '') {
			$.post(URL+"chat/sendChat", {userid: userid, to: chatboxtitle, message: message} , function(data){
				message = message.replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/\"/g,"&quot;");
				
				$("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatcontainer"><div class="chatboxmessage sky"><span class="chatboxmessagecontent">'+message+'</span></div><div class="sendingtimeright">'+data+'</div></div>');
				$("#chatbox_"+chatboxtitle+" .chatboxcontent").scrollTop($("#chatbox_"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);
			});
		}
		chatHeartbeatTime = minChatHeartbeat;
		chatHeartbeatCount = 1;
		return false;
	}

	var adjustedHeight = chatboxtextarea.clientHeight;
	var maxHeight = 94;

	if (maxHeight > adjustedHeight) {
		adjustedHeight = Math.max(chatboxtextarea.scrollHeight, adjustedHeight);
		if (maxHeight)
			adjustedHeight = Math.min(maxHeight, adjustedHeight);
		if (adjustedHeight > chatboxtextarea.clientHeight)
			$(chatboxtextarea).css('height',adjustedHeight+8 +'px');
	} else {
		$(chatboxtextarea).css('overflow','auto');
	}
	 
}



//**********************************************************************************************************
// startChatSession
//**********************************************************************************************************
function startChatSession(){  
	$.ajax({
	  url: URL+"chat/startchatsession",
	  cache: false,
	  dataType: "json",
	  success: function(data) {
		username = data.username;
		
		$.each(data.items, function(i,item){
			if (item){ // fix strange ie bug
				var	userid	= item.tid;
				var str 	= item.f;
				name 		= str;
				
				chatboxtitle = str;
				chatboxtitle = name.replace(/ /g, '_');
				name_std = name.replace(/_/g, ' ')/*.replace(/[^a-zA-Z ]/g, "")*/;
				
				var msg = item.m;
				/*msg = msg.replace(/[^a-zA-Z ]/g, "");*/
				
				var cookieName = 'user-'+str+'-status';
				var userStatus = $.cookie(cookieName);
				
				if ($("#chatbox_"+chatboxtitle).length <= 0) {
					createChatBox(userid, chatboxtitle,userStatus,1);
				}
				
				if (item.s == 1) {
					/*item.f = username;*/
					item.f = "You";
				}
				
				var time = item.t;
				
				if (item.s == 2) {
					$("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatcontainer"><div class="chatboxmessage sky"><span class="chatboxinfo">'+item.m+'</span></div><div class="sendingtimeright">'+time+'</div></div>');
				} else {
					$("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatcontainer"><div class="chatboxmessage sky"><span class="chatboxmessagecontent">'+msg+'</span></div><div class="sendingtimeright">'+time+'</div></div>');
				}
			}
		});
			
		for (i=0;i<chatBoxes.length;i++) {
			chatboxtitle = chatBoxes[i];
			$("#chatbox_"+chatboxtitle+" .chatboxcontent").scrollTop($("#chatbox_"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);
			setTimeout('$("#chatbox_"+chatboxtitle+" .chatboxcontent").scrollTop($("#chatbox_"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);', 100); // yet another strange ie bug
		}
		// smith
			//$("#chatbox_"+chatboxtitle).draggable();
			//$(".chatbox_").draggable();
		////////////////
		setTimeout('chatHeartbeat();',chatHeartbeatTime);
	},error: function(){
    alert('error!');
  }});
}



//**********************************************************************************************************
// Check for on-line users
//**********************************************************************************************************
function offlinePulse(ids){
	var idsnow			= ids;
	//var ids 			= JSON.parse(ids);
	var idsForDetail	= JSON.stringify(ids);
	var countuser 		= ids.userid.length;
	var userId	 		= "";
	var counter			= 1;
	var onlineUsers		= "";
	var offlineUsers	= "";
	var userIdArray		= new Array();
	
	//Get on-line users details
	if(countuser>0){
		for (i = 0; i < countuser; i++) {
			userIdArray[i] = ids.userid[i];
			userId					+= ids.userid[i];
			if(counter<countuser){	
				userId	+= ",";
				counter++;
			}
		}
		
		$.ajax({
			url: URL+"chat/offlinePulse",
			data: {ids:userId},
			dataType: "html",
			async: false,
			success: function(msg) {
				//if(data	== "done")
			},error: function(){
				alert('There is an error. Reload the pagecccccc!');
			}
		});
	}
	
	//var idsForDetail 	= JSON.stringify(idsnow);
	var functionToCall 	= 'offlinePulse('+idsForDetail+')';
	setTimeout(functionToCall,OfflinePulsebeatTime);
}


/**
 * Cookie plugin
 *
 * Copyright (c) 2006 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

$.cookie = function(name, value, options) {
    if (typeof value != 'undefined') { // name and value given, set cookie
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        // CAUTION: Needed to parenthesize options.path and options.domain
        // in the following expressions, otherwise they evaluate to undefined
        // in the packed version for some reason...
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = $.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};