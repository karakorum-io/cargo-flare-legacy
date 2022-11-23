/*
JS for chat functions
*/
var windowFocus 		= true;
var username;
var chatHeartbeatCount 	= 0;
var minChatHeartbeat 	= 5000;
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


//**********************************************************************************************************
// deparam
//**********************************************************************************************************
jQuery.deparam = function (params) {
	var o = {};
	if (!params) return o;
	var a = params.split('&');
	for (var i = 0; i < a.length; i++) {
		var pair = a[i].split('=');
		o[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1]);
	}
	return o;
}


/************ Notification **************/
				
		  function onShowNotification (id,type) {
			  //alert(id);
                //console.log('notification is shown!');
				if(type==1){
							$.ajax({
							type: "POST",
							url: BASE_PATH+'application/ajax/chat.php',
							data: { action: "closeChatNotification", id: id},
							dataType: "json",
							success: function(response) {
								
								
							}
						});
				}
				else if(type==2){
						$.ajax({
							type: "POST",
							url: BASE_PATH+'application/ajax/sms.php',
							data: { action: "closeSMSNotification", id: id},
							dataType: "json",
							success: function(response) {
								
								
							}
						});

					
					}
					
            }

            function onCloseNotification () {
               // console.log('notification is closed!');
            }

            function onClickNotification () {
               // console.log('notification was clicked!');
			   
            }

            function onErrorNotification () {
              //  console.error('Error showing notification. You may need to request permission.');
				
            }

            function onPermissionGranted () {
               // console.log('Permission has been granted by the user');
                
            }

            function onPermissionDenied () {
                console.warn('Permission has been denied by the user');
            }

            function doNotification(title,bodytext,uniqueid,id,icon,type) {
				
                var myNotification = new Notify(title, {
                    body: bodytext,
                    tag: uniqueid,
					icon:icon,
                    notifyShow: onShowNotification(id,type),
                    notifyClose: onCloseNotification,
                    notifyClick: onClickNotification,
                    notifyError: onErrorNotification,
                    timeout: 5
                });

                myNotification.show();
            }
          /************ Notification **************/
			 if (!Notify.needsPermission) {
               // doNotification();
            } else if (Notify.isSupported()) {
                Notify.requestPermission(onPermissionGranted, onPermissionDenied);
            }

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
		/* console.log(chatboxtitle);
		$("#chatbox_"+chatboxtitle+" .online-status-icon").removeClass().addClass("online-status-icon "+class_status); */ 
		if ($("#chatbox-"+chatboxtitle).css('display') != 'none') {
			if (align == 0) {
				$("#chatbox-"+chatboxtitle).css('right', '280px');
			} else {
				width = (align)*(228)+280;
				$("#chatbox-"+chatboxtitle).css('right', width+'px');
			}
			align++;
		}
	}
}


 
//**********************************************************************************************************
// Get users details 
//**********************************************************************************************************
function getuserdetails(countNumber, ids){
	//Set various variables
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
		//Ajax call
		$.ajax({
			url: URL+"chat/getuserdetails",
			data: {ids:userId},
			dataType: "json",
			async: false,
			success: function(array) {
				/*
				$.each(array, function(i,item){
					//Set received variables 
					var userid			= item.id;
					var name			= item.name;
					var chatname		= "'"+item.chatname+"'";
					var onlinestatus	= item.onlinestatus;
					var status			= "";
					
					//Check every user's status and assign them respective variables					
					if(onlinestatus == '1'){		//If user is on-line
						countOnline++;
						status = "'online'";
						var userStatus	 = '';
						var historyLimit = '';
						var obj = { userStatus: 'online', historyLimit: '0' };
						$.cookie("user_"+item.chatname, $.param(obj), { path: '/'});
						
						//$.cookie('user-'+item.chatname+'-status','online', { path: '/' });
						//$.cookie('historyLimit_'+item.chatname,'0', { path: '/' });
						onlineUsers += '<div class="chatuserslist"><div class="online-status-icon onlinedot">&nbsp;</div><div class="chatboxcountertitle" onclick="chatWith('+userid+', '+chatname+','+status+');">'+name+'</div><br clear="all"/></div>';
					}else{							//If user is off-line
						status = "'offline'";
						var userStatus	 = '';
						var historyLimit = '';
						var obj = { userStatus: 'offline', historyLimit: '0' };
						$.cookie("user_"+item.chatname, $.param(obj), { path: '/'});
						
						//$.cookie('user-'+item.chatname+'-status','offline', { path: '/' });
						//$.cookie('historyLimit_'+item.chatname,'0', { path: '/' });
						offlineUsers += '<div class="chatuserslist"><div class="online-status-icon offlinedot">&nbsp;</div><div class="chatboxcountertitle" onclick="chatWith('+userid+', '+chatname+','+status+');">'+name+'</div><br clear="all"/></div>';
					}
					//Check cookie for, if chat-window is open and then call function 'chatWith'
					if ($.cookie('OpenChatWindow')){
						var ocwVal 	= $.cookie('OpenChatWindow');
						ocwValArr 	= ocwVal.split(/\|/);
						for (var i = 0; i < ocwValArr.length; i++) {
							if (ocwValArr[i] == userid) {
								if($("#chatbox-"+item.chatname).length == 0){
									chatWith(userid,item.chatname,onlinestatus);
								}		
							}
						}
					}
					
				});
				
				
				//If off-line user exist, include a highlight/divider
				if(offlineUsers	!= "")
					var divider = '<div class="chatboxdivider">------------ OFFLINE USERS ------------</div>';
				
				//Concatenate 3 variables. On-Line Users/Divider/Off-line Users 
				var contetnt = onlineUsers + divider + offlineUsers;
				
				//Create Users-listing-content inside User-listing-window
				if($("#userlists").length == 0) {		//If its not there, Create it
					$(" <div />" ).attr("id","userlists").addClass("counterbox").html(contetnt).appendTo($( "body" ));
					$("#userlists").attr('style','bottom: 0px; right: 0px; max-height: 262px; overflow-y: scroll; display:none;');
				} else {								//If its there, replace old data with fresh fetch-data
					$("#userlists").empty().html(contetnt);
				}
				
				*/
				
			},error: function(){
				//alert('There is an error. Reload the page-1!');
			}
		});
	}
	var content ="<table width='100%' cellspacing='1' style='background-color:#f4f4f4;border:1px solid #cccccc;'><tr><td align='center'><img src='"+BASEURL+"images/communicator/chat_sm.png' id='openChatNew'></td><td align='center'><img src='"+BASEURL+"images/communicator/sms_sm.png' id='openSms'></td><td align='center'><img src='"+BASEURL+"images/communicator/efax_sm.png'></td></tr></table>";
				$(" <div />" ).attr("id","userlists").addClass("counterbox").html(content).appendTo($( "body" ));
				$("#userlists").attr('style','bottom: 0px; right: 0px; max-height: 70px; overflow-y: scroll; display:none;');
				
	$(" <div />" ).attr("id","onlineUserCounter").addClass("counterbox").html('<div class="chatboxcounter"><div class="chatboxcountertitle">&nbsp;&nbsp;FreightDragon Communicator</div><div class="chatboxoptions"></div><br clear="all"/></div>').appendTo($( "body" ));
		$(" <div />" ).attr("class","bell-shell").html('<div class="bell-icon">&nbsp;</div>').appendTo($( "body" ));
	
	/*
	//Create Users-listing-window
	if($("#onlineUserCounter").length == 0) {		//If Users-listing-window not exist, create it 
		
		if($("#onlineUserCounter").length == 0) {		//If Users-listing-window not exist, create it 
		$(" <div />" ).attr("id","onlineUserCounter").addClass("counterbox").html('<div class="chatboxcounter"><div class="user-icon">&nbsp;</div><div class="online-status-icon onlinedot">&nbsp;</div><div class="chatboxcountertitle">Chat <span>(</span>'+countNumber+'<span>)</span></div><div id="openSms" style="float: left;font-size: 12px;font-weight: bold;">&nbsp;&nbsp; | &nbsp;<span >SMS (<span id="smsCount">0</span>)</span></div><div id="openChatNew" style="float: left;font-size: 12px;font-weight: bold;">&nbsp;&nbsp; | &nbsp;<span >Chat (<span id="chatCountNew">0</span>)</span></div><div class="chatboxoptions"></div><br clear="all"/></div>').appendTo($( "body" ));
		$(" <div />" ).attr("class","bell-shell").html('<div class="bell-icon">&nbsp;</div>').appendTo($( "body" ));
		
	} else {										//If Users-listing-window exist, replace contnet
		$("#onlineUserCounter .chatboxcountertitle").empty().html('Chat <span>(</span>'+countOnline+'<span>)</span>');
	}
	
	*/
	
	//Call 'this' same function again on defined/set time as re-occurring function 
	//var functionToCall 	= 'getuserdetails('+countNumber+','+idsForDetail+')';
	//setTimeout(functionToCall,OnlinePulsebeatTime);	
}



//**********************************************************************************************************
// chatWith
//**********************************************************************************************************
function chatWith(userid,chatuser,status) {
	createChatBox(userid,chatuser,status);
	$("#chatbox-"+chatuser+" .chatboxtextarea").focus();
}



//**********************************************************************************************************
// createChatBox
//**********************************************************************************************************
function createChatBox(userid,chatboxtitle,status,minimizeChatBox) {
	//Set various variables
	var userid		= userid;
	var newCookie 	= userid;
	var name 		= chatboxtitle;
	var status 		= status;
	var content		= "";
	
	//Check if open-chat-window cookie exist
	if ($.cookie('OpenChatWindow')){
		var countIdExist 	= 0;
		var ocwVal		 	= $.cookie('OpenChatWindow');
		ocwValArr 			= ocwVal.split(/\|/);
		for (var i = 0; i < ocwValArr.length; i++) {
			//If called User-ID exist in open-chat-window cookie, increase counter value from 0(zero)
		 	if (ocwValArr[i] == userid){	  
				countIdExist++;		
			}
		}
		
		//If called User-ID exist in open-chat-window cookie, its ID will not concatenate again in open-chat-window cookie
		//If counter value is still 0(zero), it means called User-ID not exist in open-chat-window cookie and...
		//called User-ID will concatenate in open-chat-window cookie
		if(countIdExist==0){
			ocwVal += '|'+newCookie;
		}
		//Set updated value in 'OpenChatWindow' cookie 
		$.cookie('OpenChatWindow',ocwVal, { path: '/' });
	} else {	//If open-chat-window cookie not exist, then set called User-ID in open-chat-window cookie
		$.cookie('OpenChatWindow',newCookie, { path: '/' });
	}
	
	//Replace any under-score(_) from 'name' with white-space( ) 
	name 		= name.replace(/_/g, ' ');
	
	//If called user's chat-window is existing with in the page 
	if ($("#chatbox-"+chatboxtitle).length > 0) {
		//If called user's chat-window is open but hide with in the page, then make it appear/active and called function 'restructureChatBoxes'
		if ($("#chatbox-"+chatboxtitle).css('display') == 'none') {		
			$("#chatbox-"+chatboxtitle).css('display','block');
			restructureChatBoxes(status);
		}
		
		//Make cursor's focus on active chat-window 
		$("#chatbox-"+chatboxtitle+" .chatboxtextarea").focus();
		return;
	}
	
	//Check status and set class accordingly 
	if(status == 'online' || status == '1'){
		var class_status = "onlinedot";
	} else {
		var class_status = "offlinedot";
	}
	
	var classChatboxhead 	= 'chatboxhead';
	var classChatboxoptions = 'chatboxoptions';
	
	//Get and set fetch-records limit from called user's cookie-value
	var userCookieName 	= 'user_'+chatboxtitle;
	var userCookieVal 	= $.cookie(userCookieName);
	var processCookie 	= $.deparam(userCookieVal);
	var userStatusVal	= processCookie['userStatus'];
	var historyLimitVal = processCookie['historyLimit'];
	//console.log(userCookieName+" = "+processCookie['historyLimit']); 
	
	
	/*
	var cookieLimitName = 'user_'+chatboxtitle;
	chatboxtitle = chatboxtitle.replace("'", "");
	var cookieLimitName = 'historyLimit_'+chatboxtitle;
	var cookieLimit	= $.cookie(cookieLimitName);
	 if(cookieLimit == 'none' || cookieLimit == 'NaN'){
		var limit = 0;
		cookieLimit = 0;
	} else {
		var limit = cookieLimit;
	} */
	
	var limit = 0;
	var cLimit = 0;
	
	//If called user's chat-window is not exist/open in page 
	if ($("#chatbox-"+chatboxtitle).length <= 0) {
		//Set various variables
		var todayCount  = 0;
		var otherCount  = 0;
		
		//Set cookie for date-divider
		$.cookie('cookieDateDivider','0000-00-00', { path: '/'});
		
		//Ajax call to get last 10 messages as history in chat window
		$.ajax({
			url: URL+"chat/chathistory",
			data: {id:userid, limit:limit},
			cache: false,
			contentType: 'application/json; charset=utf-8',
			dataType: "json",
			async: false,
			success: function(data) {
				username = data.username;
				$.each(data.items, function(i,item){
					if (item)	{ // fix strange ie bug
						//Set various variables
						var	uid				= item.tid;
						var time 			= item.t;
						var msg 			= item.m;
						var chatbox_title 	= item.f;
						chatbox_title 		= chatbox_title.replace(/ /g, '_');
						var db_date			= item.date;
						var DateArr 		= db_date.split("-");
						var d 				= new Date();
						var Year			= d.getFullYear();
						var Month			= d.getMonth();
						var Dates			= ("0" + d.getDate()).slice(-2); 
						//var one				= 1;
						Month				= ("0" + (d.getMonth() + 1)).slice(-2);
						var today			= Year+'-'+Month+'-'+Dates; 
						var dateDivider 	= $.cookie('cookieDateDivider');
						//console.log(db_date+' != '+today+' && '+db_date+' != '+dateDivider);
						
						if(db_date == today && todayCount == 0){			//If fetched 'db_date' is of Today 
							content += '<div class="messageDateSeprate">---------- TODAY ----------</div>';
							todayCount++;
						} else if(db_date != today && db_date != dateDivider){		//If fetched 'db_date' is not of Today 
							content += '<div class="messageDateSeprate">---------- '+DateArr[1]+'/'+DateArr[2]+'/'+DateArr[0]+' ----------</div>';
							$.cookie('cookieDateDivider',db_date, { path: '/'});
						} else {
							//otherCount = 0;
						}
						
						//Create chat bubble  
						if(chatbox_title == username){		//If chat is from self
							content += '<div class="chatcontainer"><div class="chatboxmessage sky"><span class="chatboxmessagecontent">'+msg+'</span></div><div class="sendingtimeright">'+time+'</div></div>';
						} else {							//If chat is from other user
							content += '<div class="chatcontainer"><div class="chatboxmessage white"><span class="chatboxmessagecontent">'+msg+'</span></div><div class="sendingtimeleft">'+time+'</div></div>';
						}
					}
				});
				
				//Remove the old and Set fetch-records limit with increase by +10
				$.removeCookie(userCookieName, { path: '/' });
				var ten = 10;
				var obj = { userStatus: userStatusVal, historyLimit: ten };
				$.cookie(userCookieName, $.param(obj), { path: '/'});
				
				//Get UnixTimeStringLeft Cookie
				if($.cookie('UnixTimeStringLeft_'+chatboxtitle)){
					var UnixTimeStringLeft = $.cookie('UnixTimeStringLeft_'+chatboxtitle);
				} else {
					var UnixTimeStringLeft = 0;
				}
				
				//Get UnixTimeStringRight Cookie
				if($.cookie('UnixTimeStringRight_'+chatboxtitle)){
					var UnixTimeStringRight = $.cookie('UnixTimeStringRight_'+chatboxtitle);
				} else {
					var UnixTimeStringRight = 0;
				}
				
				//Create chat window with chat bubble content
				$(" <div />" ).attr("id","chatbox-"+chatboxtitle).addClass("chatbox").html('<span class="hiddenuserid">'+userid+'</span><div class="'+classChatboxhead+'"><div class="chatboxtitle" onclick="toggleChatBoxGrowth(\''+chatboxtitle+'\');"><div class="online-status-icon '+class_status+'">&nbsp;</div>'+name+'</div><div class="'+classChatboxoptions+'"><a href="javascript:void(0)" onclick="javascript:closeChatBox(\''+chatboxtitle+'\',\''+status+'\')">X</a></div><br clear="all"/></div><div class="chatboxcontent"><span class="chat-load"><img src="'+BASEURL+'images/chat-loader.gif"></span>'+content+'</div><div class="chatboxinput"><textarea class="chatboxtextarea" onkeydown="javascript:return checkChatBoxInputKey(event,this,\''+chatboxtitle+'\',\''+userid+'\');"></textarea><input type="hidden" id="UnixTimeStringLeft_'+chatboxtitle+'" value="'+UnixTimeStringLeft+'"><input type="hidden" id="UnixTimeStringRight_'+chatboxtitle+'" value="'+UnixTimeStringRight+'"></div>').appendTo($( "body" ));
				
				//Auto-scroll down to the bottom of chat window 
				$("#chatbox-"+chatboxtitle+" .chatboxcontent").scrollTop($("#chatbox-"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);
			},error: function(){
				//alert('There is an error. Reload the page-2!');
			}
		});
	}
	
	//Fix chat-window position to bottom of screen 
	$("#chatbox-"+chatboxtitle).css('bottom', '0px');
	
	//Set new variable counter
	chatBoxeslength = 0;
	
	//Check if any chat-window exist, then increment counter 'chatBoxeslength' from 0(zero)
	for (x in chatBoxes) {
		if ($("#chatbox-"+chatBoxes[x]).css('display') != 'none') {
			chatBoxeslength++;
		}
	}
	
	//Check if there is any chat-window exist on page
	if (chatBoxeslength == 0) {		//Means there is no chat-window, then set first chat-window location
		$("#chatbox-"+chatboxtitle).css('right', '280px');
	} else {						//Means there is chat-window exist, then set new chat-window after the existing chat-windows  
		width = (chatBoxeslength)*(228)+280;
		$("#chatbox-"+chatboxtitle).css('right', width+'px');
	}
	
	//Add called User in 'chatBoxes' array
	chatBoxes.push(chatboxtitle);
	
	//Define new array
	searchArr = new Array();
	
	//If 'chatbox_minimized' cookie exist
	if ($.cookie('chatbox_minimized')) {
		//Define new array and split 'chatbox_minimized' cookie on '|' in there
		minimizedChatBoxes = new Array();
		minimizedChatBoxes = $.cookie('chatbox_minimized').split(/\|/);
		
		//Defined counter 'minimizeCounter' and check called-User exist in 'chatbox_minimized' cookie
		minimizeCounter = 0;
		for (j=0;j<minimizedChatBoxes.length;j++) {
			//If called-User exist, then increment the set counter 'minimizeCounter' to 1(one)
			if (minimizedChatBoxes[j] == chatboxtitle) {
				minimizeCounter = 1;
			}
		}
		
		//If counter 'minimizeCounter' is set to 1, then minimized the same called-User window
		if (minimizeCounter == 1) {
			$('#chatbox-'+chatboxtitle+' .chatboxhead').removeClass().addClass('chatboxhead grey');
			$('#chatbox-'+chatboxtitle+' .chatboxoptions').removeClass().addClass('chatboxoptions black');
			$('#chatbox-'+chatboxtitle+' .chatboxcontent').css('display','none');
			$('#chatbox-'+chatboxtitle+' .chatboxinput').css('display','none');
			$('#chatbox-'+chatboxtitle+' .historyLink').css('display','none');	
		}
	}
	
	/* if (minimizeChatBox == 1) {
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
			$('#chatbox-'+chatboxtitle+' .chatboxcontent').css('display','none');
			$('#chatbox-'+chatboxtitle+' .chatboxinput').css('display','none');
			$('#chatbox-'+chatboxtitle+' .historyLink').css('display','none');	
		}
	} */

	chatboxFocus[chatboxtitle] = false;

	$("#chatbox-"+chatboxtitle+" .chatboxtextarea").blur(function(){
		chatboxFocus[chatboxtitle] = false;
		$("#chatbox-"+chatboxtitle+" .chatboxtextarea").removeClass('chatboxtextareaselected');
	}).focus(function(){
		chatboxFocus[chatboxtitle] = true;
		newMessages[chatboxtitle] = false;
		$('#chatbox-'+chatboxtitle+' .chatboxhead').removeClass('chatboxblink');
		$("#chatbox-"+chatboxtitle+" .chatboxtextarea").addClass('chatboxtextareaselected');
	});
	
	//Set mouse focus on clicked chat-window
	$("#chatbox-"+chatboxtitle).click(function() {
		if ($('#chatbox-'+chatboxtitle+' .chatboxcontent').css('display') != 'none') {
			$("#chatbox-"+chatboxtitle+" .chatboxtextarea").focus();
		}
	});
	
	//Show called User chat-window
	$("#chatbox-"+chatboxtitle).show();
}



//**********************************************************************************************************
// chatHistory
//**********************************************************************************************************
function chatHistory(parentid,userid,height){
	//Set variables
	var parentId	= parentid;
	var userid 		= userid;
	var itemsfound  = 0;
	var todayCount  = 0;
	var otherCount  = 0;			
	var contnet = "";
	
	var userCookieName 	= 'user_'+parentId;
	var userCookieVal 	= $.cookie(userCookieName);
	var processCookie 	= $.deparam(userCookieVal);
	var userStatusVal	= processCookie['userStatus'];
	var historyLimitVal	= processCookie['historyLimit'];
	
	/*var cookieLimitName = 'historyLimit_'+parentId;
	 console.log('---------------');
	console.log(cookieLimitName+" GET in chatHistory= "+$.cookie(cookieLimitName));
	console.log("#chatbox"+parentId+" .chat-load"); 
	var cookieLimit	= $.cookie(cookieLimitName);*/
	
	if(historyLimitVal > 0){
		var limit = historyLimitVal;
	} else {
		var limit = 0;
	}
	
	//Ajax call to get last 10 messages as history in chat window
	$.ajax({
		url: URL+"chat/chathistory",
		data: {id:userid, limit:limit},
		cache: false,
		contentType: 'application/json; charset=utf-8',
		dataType: "json",
		beforeSend:function(){
			//Show chat load indicator 
			$("#chatbox-"+parentId+" .chat-load").show();
		},
		complete:function(){
			//Hide chat load indicator
			$("#chatbox-"+parentId+" .chat-load").delay(2000).hide();
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
					
					if ($("#chatbox-"+chatboxtitle).css('display') == 'none') {
						$("#chatbox-"+chatboxtitle).css('display','block');
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
					} else if(db_date != today && db_date != dateDivider){
						contnet += '<div class="messageDateSeprate">---------- '+DateArr[1]+'/'+DateArr[2]+'/'+DateArr[0]+' ----------</div>';
						otherCount++;
						$.cookie('cookieDateDivider',db_date, { path: '/'});
					} else {
						//otherCount = 0;
					}
					
					if(chatboxtitle == username){
						contnet += '<div class="chatcontainer"><div class="chatboxmessage sky"><span class="chatboxmessagecontent">'+msg+'</span></div><div class="sendingtimeright">'+time+'</div></div>';
					} else {
					    contnet += '<div class="chatcontainer"><div class="chatboxmessage white"><span class="chatboxmessagecontent">'+msg+'</span></div><div class="sendingtimeleft">'+time+'</div></div>';
					}
				}
			});
			if(todayCount>0){
				$("#chatbox-"+parentId+" .messageDateSeprate").remove();
			}
			$("#chatbox-"+parentId+" span.chat-load").after(contnet);
			//$("#"+parentId+" .chatboxcontent").prepend('<span class="chat-load"><img src="'+BASEURL+'images/chat-loader.gif"></span>');
			
			//Remove the old and Set fetch-records limit with increase by +10
			$.removeCookie(userCookieName, { path: '/' });
			var ten = 10;
			historyLimitVal = +historyLimitVal + +ten;
			var obj = { userStatus: userStatusVal, historyLimit: historyLimitVal };
			$.cookie(userCookieName, $.param(obj), { path: '/'});
			
			$.cookie('cookieScrollLimit','0', { path: '/'});
			$("#chatbox-"+parentId+" .chatboxcontent").scrollTop('300');
			//$("#"+parentId+" .chatboxcontent").scrollTop($("#"+parentId+" .chatboxcontent")[0].scrollHeight);
		},error: function(){
			//alert('There is an error. Reload the page-4!');
		}
	});
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
				$('#chatbox-'+x+' .chatboxhead').toggleClass('chatboxblink');
				//$('#chatbox-'+x+' .chatboxoptions').toggleClass('black');
			}
		}
	}
	
	$.ajax({
		url: URL+"chat/chatheartbeat",
		cache: false,
		contentType: 'application/json; charset=utf-8',
		dataType: "json",
		success: function(data){
			username = data.username;
			
			
			//$('#smsCount').html(data.sms);
			$('#smsCountDown').html(data.sms);
			//alert('sms check'+data.sms);
			if(data.sms > 0){
				
				var sms_data = data.smsData;
				//alert(sms_data.length);
				var smsIcon = "https://freightdragon.com/images/communicator/sms_sm.png";
				for (i in sms_data) {
					doNotification('sms message from '+sms_data[i].FromPhone,sms_data[i].Message,i,sms_data[i].id,smsIcon,2);
				}
				
			 $("#smsCountDownStyle").css("background-color", '#C00') ;
			   
			   if ($("#chatbox-sms").css('display') == 'none' && $("#chatbox-sms-down").css('display') != 'block') {
					 $.cookie('smsWindowStatus',"0", { path: '/' });
					 createSMSBox(0);
					 
				}
			}
			else
			  $("#smsCountDownStyle").css("background-color", '#2D8ABC') ;
			
			$('#chatCountDown').html(data.chat);
			if(data.chat > 0){
				var chat_data = data.chatData;
				//alert(chat_data.length);
				var chatIcon = "https://freightdragon.com/images/communicator/chat_sm.png";
				for (i in chat_data) {
					doNotification('Chat message from '+chat_data[i].from,chat_data[i].message,i,chat_data[i].id,chatIcon,1);
				}
				
				/*
				var countChatData = data.chatData.length;
				//alert(countChatData);
			    if(countChatData>0){
				    $.each(data.items, function(i,item){
											});
				}
				*/
				//doNotification('New Chat Message','Number of Chat Messages '+data.chat,Math.random());
				
			 $("#chatCountDownStyle").css("background-color", '#C00') ;
			 
			    if ($("#chatbox-new").css('display') == 'none' && $("#chatbox-new-down").css('display') != 'block') {
					 $.cookie('chatWindowStatus',"0", { path: '/' });
					 createChatBoxNew(0);
					 
					  if(smsSendTntervalChatNew && smsSendTntervalChatNew !== "null" && smsSendTntervalChatNew!== "undefined"){
		
							  clearInterval(smsSendTntervalChatNew);
							  smsSendTntervalChatNew=null;
						}
	
		
                        smsSendTntervalChatNew = setInterval(function(){ checkChatSMSMessagesNew('','') }, 1000*3);
					 
				}
			   
			     
					
			}
			else
			  $("#chatCountDownStyle").css("background-color", '#2D8ABC') ;
			  
			  /****** Check in multiple windows open or closed *******/
			  var openCloseWindowStatus = $.cookie('chatWindowStatus');
			  windowStatusChat(openCloseWindowStatus);
			  if(openCloseWindowStatus !=2)
			    checkChatSMSMessagesNew('','');

			   //chatRepeatCall('','');
			  
			  openCloseWindowStatus = $.cookie('smsWindowStatus');
			  windowStatusSMS(openCloseWindowStatus);
			  if(openCloseWindowStatus !=2)
			    checkChatSMSMessages('');
				
				
				
				
			   
			   //smsRepeatCall('');
			  
			var countData = data.items.length;
			if(countData>0){
				$.each(data.items, function(i,item){
					if (item){ // fix strange ie bug
						var	userid	= item.tid;
						var time 	= item.t;
						var str 	= item.f;
						name 		= str;
						name 		= name.replace(/'/g, '');						
						chatboxtitle = name.replace(/ /g, '_');
						
						var msg = item.m;
						var status = item.online_status;
						
						var UnixTimeStringLeft = Math.floor(Date.now() / 1000|0);
						//Set recent ping time in hidden box and in cookie 
						$("#UnixTimeStringLeft_"+chatboxtitle).val(UnixTimeStringLeft);
						$.cookie('UnixTimeStringLeft_'+chatboxtitle,UnixTimeStringLeft, { path: '/'});
						
						//Set recent ping and its time in cookie
						$.cookie('recentPingLeft_'+chatboxtitle,msg, { path: '/'});
						$.cookie('recentPingTimeLeft_'+chatboxtitle,time, { path: '/'});
						
						
						if ($("#chatbox-"+chatboxtitle).length <= 0) {
							createChatBox(userid, chatboxtitle, status);
						} else {
							if (item.s == 2) {
								$("#chatbox-"+chatboxtitle+" .chatboxcontent").append('<div class="chatcontainer"><div class="chatboxmessage white"><span class="chatboxinfo">'+msg+'</span><div class="sendingtimeleft">'+time+'</div></div>');
							} else {
								newMessages[chatboxtitle] = true;
								newMessagesWin[chatboxtitle] = true;
								$("#chatbox-"+chatboxtitle+" .chatboxcontent").append('<div class="chatcontainer"><div class="chatboxmessage white"><span class="chatboxmessagecontent">'+msg+'</span></div><div class="sendingtimeleft">'+time+'</div></div>');
							}
							$("#chatbox-"+chatboxtitle+" .chatboxcontent").scrollTop($("#chatbox-"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);
						}
						if ($("#chatbox-"+chatboxtitle).css('display') == 'none') {
							$("#chatbox-"+chatboxtitle).css('display','block');
							restructureChatBoxes(status);
						}
						/* if($("#chatbox-"+chatboxtitle).length > 0){
							
						} */
						itemsfound += 1;
					} 
				});
			} else {
				$('.chatbox').each(function () {
					var myId = this.id;
					myId = myId.split(/\-/);
					myName = myId[1];
					
					//Get last hidden value for UnixTimeStringRight
					var hiddenUnixTimeStringRight = $("#UnixTimeStringRight_"+myName).val();
					//Get cookie value for UnixTimeStringRight
					var cookieUnixTimeStringRight = $.cookie('UnixTimeStringRight_'+myName);
					
					if((cookieUnixTimeStringRight>0) && (hiddenUnixTimeStringRight != cookieUnixTimeStringRight)){
						//Set value in hidden UnixTimeStringRight
						$("#UnixTimeStringRight_"+myName).val(cookieUnixTimeStringRight);
				
						//Get recent ping & time from cookie and send to the chat-box
						var cookieRecentPingRight 		= $.cookie('recentPingRight_'+myName);
						var cookierecentPingTimeRight 	= $.cookie('recentPingTimeRight_'+myName);
				
						$("#chatbox-"+myName+" .chatboxcontent").append('<div class="chatcontainer"><div class="chatboxmessage sky"><span class="chatboxmessagecontent">'+cookieRecentPingRight+'</span></div><div class="sendingtimeright">'+cookierecentPingTimeRight+'</div></div>');
						$("#chatbox-"+myName+" .chatboxcontent").scrollTop($("#chatbox-"+myName+" .chatboxcontent")[0].scrollHeight);
						itemsfound += 1;
					}
					
					//Get last hidden value for UnixTimeStringRight
					var hiddenUnixTimeStringLeft = $("#UnixTimeStringLeft_"+myName).val();
					//Get cookie value for UnixTimeStringRight
					var cookieUnixTimeStringLeft = $.cookie('UnixTimeStringLeft_'+myName);
					if((cookieUnixTimeStringLeft>0) && (hiddenUnixTimeStringLeft != cookieUnixTimeStringLeft)){
						//Set value in hidden UnixTimeStringLeft
						$("#UnixTimeStringLeft_"+myName).val(cookieUnixTimeStringLeft);
				
						//Get recent ping & time from cookie and send to the chat-box
						var cookieRecentPingLeft 		= $.cookie('recentPingLeft_'+myName);
						var cookierecentPingTimeLeft 	= $.cookie('recentPingTimeLeft_'+myName);
				
						$("#chatbox-"+myName+" .chatboxcontent").append('<div class="chatcontainer"><div class="chatboxmessage white"><span class="chatboxmessagecontent">'+cookieRecentPingLeft+'</span></div><div class="sendingtimeleft">'+cookierecentPingTimeLeft+'</div></div>');
						$("#chatbox-"+myName+" .chatboxcontent").scrollTop($("#chatbox-"+myName+" .chatboxcontent")[0].scrollHeight);
						itemsfound += 1;
					}
				});
				
			}
			chatHeartbeatCount++;
			if (itemsfound > 0) {
				chatHeartbeatTime = minChatHeartbeat;
				chatHeartbeatCount = 1;
			} else if (chatHeartbeatCount >= 10) {
				//chatHeartbeatTime *= 2;
				chatHeartbeatTime *= 1;
				chatHeartbeatCount = 1;
				if (chatHeartbeatTime > maxChatHeartbeat) {
					chatHeartbeatTime = maxChatHeartbeat;
				}
			}
			//console.log(chatHeartbeatTime);
			setTimeout('chatHeartbeat();',chatHeartbeatTime);
		},error: function(){
			//alert('There is an error. Reload the page-3!');
		}
	});
}



//**********************************************************************************************************
// closeChatBox
//**********************************************************************************************************
function closeChatBox(chatboxtitle,status) {
	//Hide the window which is clicked for close
	$('#chatbox-'+chatboxtitle).css('display','none');
	
	//Call function 'restructureChatBoxes' to reset the position of open windows 
	restructureChatBoxes(status);
	
	//Get 'Id' for closed window
	var closeId = $('#chatbox-'+chatboxtitle+' .hiddenuserid').text();
	
	//Check if 'open-chat-window' cookie exist
	if ($.cookie('OpenChatWindow')) {
		//Set variables
		var newCookie 		= closeId;
		var countIdExist 	= 1;
		var NCWCookieVal	= "";	
		var CWCookieVal 	= $.cookie('OpenChatWindow');
		searchArr = CWCookieVal.split(/\|/);
		
		//Remove the ID from cookie values and create new values 
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
		$.cookie('OpenChatWindow',NCWCookieVal, { path: '/'});
	} else{
		$.cookie('OpenChatWindow','', { path: '/'});
	}
	//togglechatbox cookie
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
			} else{
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
		$.cookie('chatbox_minimized',NChatMinimizedCookieVal, { path: '/'});
	}
	$.post(URL+"chat/closechat", { chatbox: chatboxtitle} , function(data){	});
}



//**********************************************************************************************************
// toggleChatBoxGrowth
//**********************************************************************************************************
function toggleChatBoxGrowth(chatboxtitle) {
	$('#chatbox-'+chatboxtitle+' .chatboxhead').toggleClass('grey');
	$('#chatbox-'+chatboxtitle+' .chatboxoptions').toggleClass('black');
	
	if ($('#chatbox-'+chatboxtitle+' .chatboxcontent').css('display') == 'none') {  
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
			$.cookie('chatbox_minimized',NChatMinimizedCookieVal, { path: '/'});
		} else{
			$.cookie('chatbox_minimized', newCookie, { path: '/'});
		}
		$('#chatbox-'+chatboxtitle+' .chatboxcontent').css('display','block');
		$('#chatbox-'+chatboxtitle+' .chatboxinput').css('display','block');
		$('#chatbox-'+chatboxtitle+' .historyLink').css('display','block');		
		$("#chatbox-"+chatboxtitle+" .chatboxcontent").scrollTop($("#chatbox-"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);
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
			
			$.cookie('chatbox_minimized',ChatMinimizedCookieVal, { path: '/'});
			//newCookie += '|'+$.cookie('chatbox_minimized');
		}

		$.cookie('chatbox_minimized',newCookie, { path: '/'});
		
		$('#chatbox-'+chatboxtitle+' .chatboxcontent').css('display','none');
		$('#chatbox-'+chatboxtitle+' .chatboxinput').css('display','none');
		$('#chatbox-'+chatboxtitle+' .historyLink').css('display','none');
	}
	
}



//**********************************************************************************************************
// checkChatBoxInputKey
//**********************************************************************************************************
function checkChatBoxInputKey(event,chatboxtextarea,chatboxtitle,userid) {
	var UnixTimeStringRight = Math.floor(Date.now() / 1000);
	if(event.keyCode == 13 && event.shiftKey == 0){
		message = $(chatboxtextarea).val();
		message = message.replace(/^\s+|\s+$/g,"");

		$(chatboxtextarea).val('');
		$(chatboxtextarea).focus();
		$(chatboxtextarea).css('height','30px');
		if (message != '') {
			/* $.post(URL+"chat/sendChat", {userid: userid, to: chatboxtitle, message: message} , function(data){
				message = message.replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/\"/g,"&quot;");
				//Set recent ping time in hidden box and in cookie 
				$("#UnixTimeStringRight_"+chatboxtitle).val(UnixTimeStringRight);
				$.cookie('UnixTimeStringRight_'+chatboxtitle,UnixTimeStringRight, { path: '/'});
				
				//Set recent ping and its time in cookie
				$.cookie('recentPingRight_'+chatboxtitle,message, { path: '/'});
				$.cookie('recentPingTimeRight_'+chatboxtitle,data, { path: '/'});
				
				$("#chatbox-"+chatboxtitle+" .chatboxcontent").append('<div class="chatcontainer"><div class="chatboxmessage sky"><span class="chatboxmessagecontent">'+message+'</span></div><div class="sendingtimeright">'+data+'</div></div>');
				$("#chatbox-"+chatboxtitle+" .chatboxcontent").scrollTop($("#chatbox-"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);
			}); */
			$.ajax({
				url: URL+"chat/sendChat",
				data: {userid: userid, to: chatboxtitle, message: message},
				contentType: 'application/json; charset=utf-8',
				dataType: "html",
				success: function(data) {
					message = message.replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/\"/g,"&quot;");
					//Set recent ping time in hidden box and in cookie 
					$("#UnixTimeStringRight_"+chatboxtitle).val(UnixTimeStringRight);
					$.cookie('UnixTimeStringRight_'+chatboxtitle,UnixTimeStringRight, { path: '/'});
					
					//Set recent ping and its time in cookie
					$.cookie('recentPingRight_'+chatboxtitle,message, { path: '/'});
					$.cookie('recentPingTimeRight_'+chatboxtitle,data, { path: '/'});
					
					$("#chatbox-"+chatboxtitle+" .chatboxcontent").append('<div class="chatcontainer"><div class="chatboxmessage sky"><span class="chatboxmessagecontent">'+message+'</span></div><div class="sendingtimeright">'+data+'</div></div>');
					$("#chatbox-"+chatboxtitle+" .chatboxcontent").scrollTop($("#chatbox-"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);
				}
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
		contentType: 'application/json; charset=utf-8',
		success: function(data) {
			username = data.username;
			$.each(data.items, function(i,item){
				if (item){ // fix strange ie bug
					var	userid	= item.tid;
					var str 	= item.f;
					name 		= str;
					
					chatboxtitle = str;
					chatboxtitle = name.replace(/ /g, '_');
					name_std = name.replace(/_/g, ' ');
					
					var msg = item.m;
					
					var userCookieName 	= 'user_'+str;
					var userCookieVal 	= $.cookie(userCookieName);
					var processCookie 	= $.deparam(userCookieVal);
					var userStatusVal	= processCookie['userStatus'];
					var historyLimitVal = processCookie['historyLimit'];
					
					if ($("#chatbox-"+chatboxtitle).length <= 0) {
						createChatBox(userid,chatboxtitle,userStatusVal,1);
					}
					
					if (item.s == 1) {
						item.f = "You";
					}
					
					var time = item.t;
					
					if (item.s == 2) {
						$("#chatbox-"+chatboxtitle+" .chatboxcontent").append('<div class="chatcontainer"><div class="chatboxmessage sky"><span class="chatboxinfo">'+item.m+'</span></div><div class="sendingtimeright">'+time+'</div></div>');
					} else {
						$("#chatbox-"+chatboxtitle+" .chatboxcontent").append('<div class="chatcontainer"><div class="chatboxmessage sky"><span class="chatboxmessagecontent">'+msg+'</span></div><div class="sendingtimeright">'+time+'</div></div>');
					}
				}
			});
				
			for (i=0;i<chatBoxes.length;i++) {
				chatboxtitle = chatBoxes[i];
				$("#chatbox-"+chatboxtitle+" .chatboxcontent").scrollTop($("#chatbox-"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);
				setTimeout('$("#chatbox-"+chatboxtitle+" .chatboxcontent").scrollTop($("#chatbox-"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);', 100); 
			} 
			setTimeout('chatHeartbeat();',chatHeartbeatTime);
		},error: function(){
			//alert('There is an error. Reload the page-5!');
		}
	});
}



//**********************************************************************************************************
// Check for on-line users
//**********************************************************************************************************
function offlinePulse(ids){
	var idsnow			= ids;
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
			contentType: 'application/json; charset=utf-8',
			async: false,
			success: function(msg) {
				//if(data	== "done")
			},error: function(){
				//alert('There is an error. Reload the pagecccccc!');
			}
		});
	}
	
	//var idsForDetail 	= JSON.stringify(idsnow);
	//var functionToCall 	= 'offlinePulse('+idsForDetail+')';
	//setTimeout(functionToCall,OfflinePulsebeatTime);
}



$(document).ready(function(){
	originalTitle = document.title;
	startChatSession();

	$([window, document]).blur(function(){
		windowFocus = false;
	}).focus(function(){
		windowFocus = true;
		document.title = originalTitle;
	});
	
	$("#openSms").click(function(){
		$.cookie('smsWindowStatus',"1", { path: '/' });					 
		createSMSBox($.cookie('smsWindowStatus'));
	});
	
	$("#openChatNew").click(function(){
	    $.cookie('chatWindowStatus',"1", { path: '/' });
		createChatBoxNew($.cookie('chatWindowStatus'));
		if(smsSendTntervalChatNew && smsSendTntervalChatNew !== "null" && smsSendTntervalChatNew!== "undefined"){
		        clearInterval(smsSendTntervalChatNew);
				smsSendTntervalChatNew=null;
			}
	    smsSendTntervalChatNew = setInterval(function(){ checkChatSMSMessagesNew('','') }, 1000*3);
	});
	
	$("#onlineUserCounter").click(function(){
		$(this).toggleClass( "bottom80" );
		$( "#userlists" ).toggle();
		
		  
		
	});
	
	
	$("#sms_smiley_button").click(function(){
			if ($("#sms_more_popout").css('display') == 'none') {		
				$("#sms_more_popout").css('display','block');
			}
			else
			  $("#sms_more_popout").css('display','none');
	});
	
	
	$("#chat_smiley_button").click(function(){
			if ($("#chat_more_popout").css('display') == 'none') {		
				$("#chat_more_popout").css('display','block');
			}
			else
			  $("#chat_more_popout").css('display','none');
	});
	
	
	/*
	
	$('.chatboxcontent').live('hover', function() {
		var el = $(this);
		var parentId = el.parent().attr('id');
		var userId = el.siblings('span');
		userId	= userId.text();
		
		parentId = parentId.split(/\-/);
		parentId = parentId[1];
		
		var height = el.scrollTop();
		if (!el.data("has-scroll")) {
			el.data("has-scroll", true);
			el.scroll(function(){
				var scrollLimit = $.cookie('cookieScrollLimit');
				var d = el.scrollTop();
				if(d=='0' && (scrollLimit=='0' || scrollLimit== null)){
					$.cookie('cookieScrollLimit','1', { path: '/'});
					chatHistory(parentId,userId,height);
				}
			});
		}
	});	
	*/
	$('#chatbox-sms').draggable();
	$('#chatbox-new').draggable();
	
});






//**********************************************************************************************************
// createSMSBox
//**********************************************************************************************************
function createSMSBox(openCloseWindowStatus) {
	//Set various variables
	if ($("#chatbox-sms").css('display') == 'none') {
		  windowStatusSMS(openCloseWindowStatus);
		}
	
   $("#numberblock").nimbleLoader('show');
   $("#chatbox-sms-content").nimbleLoader('show');
   $.ajax({
		type: "POST",
		url: BASE_PATH+'application/ajax/sms.php',
		data: { action: "getChatSMSData"},
		dataType: "json",
		success: function(response) {
			//$("#sms_content").html('');
			//$("#infoblock").html('');
			if (response.success == true) {
				//alert(response.data);
				$("#numberblock").nimbleLoader('hide');
				  $("#numberblock").html(response.info);
				$("#chatbox-sms-content").nimbleLoader('hide');
				 $("#chatbox-sms-content").html(response.data);
				
			}
			else{
				$("#chatbox-sms-content").nimbleLoader('hide');
			  alert(response.data);
			}
		}
	});
	    
	
}

function closeSmsBox()
{
	if ($("#chatbox-sms").css('display') == 'block') {
			$('#chatbox-sms').css('display','none');
		}
		
    $.cookie('smsWindowStatus',"2", { path: '/' });
		  
	$('#chatbox-sms-down').css('display','none');
	
   if(smsSendTntervalChat && smsSendTntervalChat !== "null" && smsSendTntervalChat!== "undefined"){
		
		  clearInterval(smsSendTntervalChat);
		  smsSendTntervalChat=null;
	}
}

//**********************************************************************************************************
// toggleChatBoxGrowth
//**********************************************************************************************************
function toggleSmsBox(chatboxtitle) {
	
	if ($('#chatbox-sms').css('display') == 'none') {
		   $('#chatbox-sms').css('display','block');
		   $('#chatbox-sms-down').css('display','none');
		    $.cookie('smsWindowStatus',"1", { path: '/' });
			smsRepeatCall('','');
	
		} else {
			$('#chatbox-sms').css('display','none');
		    $('#chatbox-sms-down').css('display','block') ;
		    $.cookie('smsWindowStatus',"0", { path: '/' });
			smsAjaxClose();
	}
	
}


//**********************************************************************************************************
// createSMSBox
//**********************************************************************************************************
function createChatBoxNew(openCloseWindowStatus) {
	//Set various variables
	if ($("#chatbox-new").css('display') == 'none') {
		  windowStatusChat(openCloseWindowStatus);
	}
	
   $("#chatUserBlock").nimbleLoader('show');
   $("#chatbox-new-content").nimbleLoader('show');
   $.ajax({
		type: "POST",
		url: BASE_PATH+'application/ajax/chat.php',
		data: { action: "getChatUserData"},
		dataType: "json",
		success: function(response) {
			//$("#sms_content").html('');
			//$("#infoblock").html('');
			if (response.success == true) {
				//alert(response.data);
				$("#chatboxtitlenew").html(response.name);
				$("#chatUserBlock").nimbleLoader('hide');
				  $("#chatUserBlock").html(response.info);
				 $("#chatbox-new-content").nimbleLoader('hide');
				 $("#chatbox-new-content").html(response.data);
				
			}
			else{
				$("#chatbox-new-content").nimbleLoader('hide');
			  //alert(response.data);
			}
		}
	});
	    
	
}

function closeChatBoxNew()
{
	if ($("#chatbox-new").css('display') == 'block') {
			$('#chatbox-new').css('display','none');
		}
	$.cookie('chatWindowStatus',"2", { path: '/' });
	$('#chatbox-new-down').css('display','none');	
	/*
   if(smsSendTntervalChat && smsSendTntervalChat !== "null" && smsSendTntervalChat!== "undefined"){
		
		  clearInterval(smsSendTntervalChat);
		  smsSendTntervalChat=null;
	}
	*/
	if(smsSendTntervalChatNew && smsSendTntervalChatNew !== "null" && smsSendTntervalChatNew!== "undefined"){
		
		  clearInterval(smsSendTntervalChatNew);
		  smsSendTntervalChatNew=null;
	}
	
}

//**********************************************************************************************************
// toggleChatBoxGrowth
//**********************************************************************************************************
function toggleChatBoxGrowthNew(chatboxtitle) {
	//$('#chatbox-whole-table').toggleClass('grey');
	//alert($('#chatbox-new').css('display'));
	if ($('#chatbox-new').css('display') == 'none') {
		   $('#chatbox-new').css('display','block');
		   $('#chatbox-new-down').css('display','none');
		   $.cookie('chatWindowStatus',"1", { path: '/' });
		   
		   chatRepeatCall('','');
		} else {
			$('#chatbox-new').css('display','none');
		  $('#chatbox-new-down').css('display','block') ;
		  $.cookie('chatWindowStatus',"0", { path: '/' });
		  chatAjaxClose();
		 
	}
	
}

function windowStatusChat(WindowStatus)
{
				 
	  if(WindowStatus==2)
	  {
		$('#chatbox-new').css('display','none');
		$('#chatbox-new-down').css('display','none');
		
	  }
	  else if(WindowStatus==1){
		$('#chatbox-new').css('display','block');
		$('#chatbox-new-down').css('display','none'); 
	   }
	   else{
		$('#chatbox-new').css('display','none');
		$('#chatbox-new-down').css('display','block');
		
	   }
					
}

function windowStatusSMS(WindowStatus)
{
				 
	  if(WindowStatus==2)
	  {
		$('#chatbox-sms').css('display','none');
		$('#chatbox-sms-down').css('display','none');  
		
		//smsAjaxClose();
	  }
	  else if(WindowStatus==1){
		$('#chatbox-sms').css('display','block');
	    $('#chatbox-sms-down').css('display','none');
	   }
	   else{
		$('#chatbox-sms').css('display','none');
		$('#chatbox-sms-down').css('display','block');
		//smsAjaxClose();
	   }
					
}

function chatRepeatCall(id,name){
	                if(smsSendTntervalChatNew && smsSendTntervalChatNew !== "null" && smsSendTntervalChatNew!== "undefined"){
		
							  clearInterval(smsSendTntervalChatNew);
							  smsSendTntervalChatNew=null;
						}
	                     
                        smsSendTntervalChatNew = setInterval(function(){ checkChatSMSMessagesNew(id,name) }, 1000*3);
}

function smsRepeatCall(phone){
	if(smsSendTntervalChat && smsSendTntervalChat !== "null" && smsSendTntervalChat!== "undefined"){
		
		  clearInterval(smsSendTntervalChat);
		  smsSendTntervalChat=null;
	}
	
	smsSendTntervalChat = setInterval(function(){ checkChatSMSMessages(phone) }, 1000*3);
}

function chatAjaxClose()
{
	
	           if(smsSendTntervalChatNew && smsSendTntervalChatNew !== "null" && smsSendTntervalChatNew!== "undefined"){
		                      clearInterval(smsSendTntervalChatNew);
							  smsSendTntervalChatNew=null;
							  //alert('calose');
						}
}
function smsAjaxClose()
{
	       if(smsSendTntervalChat && smsSendTntervalChat !== "null" && smsSendTntervalChat!== "undefined"){
		         clearInterval(smsSendTntervalChat);
		         smsSendTntervalChat=null;
	       }
}