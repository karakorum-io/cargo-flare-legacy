function checkLogin(nEmail, nPass) {
    var vEmail = $('#'+nEmail).attr('value');
    var vPassw = $('#'+nPass).attr('value');

    error0 = vEmail == '' ? "- E-mail is required\n" : "";
    error1 = checkEmailAddress(vEmail) != true && error0 == '' ? "- E-mail is invalid" : "";
    error2 = vPassw == '' ? '- Password is required\n' : '';
    if (error0 == '' && error1 == '' && error2 == '') {
        return true;
    } else {
        alert('The following error(s) occurred:            \n\n' + error0 + error1 + error2);
        return false;
    }
}
$(function(){
    jQuery.preloadImages = function() {
        for (var i = 0; i<arguments.length; i++) {
            jQuery("<img>").attr("src", arguments[i]);
        }
    }
});


function eventsChangeTotal(price, val) {
    $('#total_price').text(price * val);
}

var eventsDialog;
function eventsRegister(eventUrl, id) {
    eventsDialog = $('<div></div>').load(eventUrl, {id: id}).dialog('destroy').dialog({
        title: 'Diversifying your Private Placement Portfolio',
        width: 400,
        height: 300,
        bgiframe: true,
        draggable: false,
        modal: true,
        close: function(event, ui) {
            $(this).remove();
        }
    });
}

function checkSubscribe() {
    var sEmail = $('#sEmail').attr('value');

    error0 = (sEmail == '' || sEmail== 'Your e-mail address') ? "- E-mail is required" : "";
    error1 = checkEmailAddress(sEmail) != true && error0 == '' ? "- E-mail is invalid" : "";
    if (error0 == '' && error1 == '') {
        return true;
    } else {
        alert('The following error(s) occurred:            \n\n' + error0 + error1);
        $('#sEmail').focus();
        return false;
    }
}

function statusText(url, rowId) {
    $.post(url);

    var obj = $(rowId);
    if (obj.text() == 'Active') {
        obj.removeClass('status-active').addClass('status-inactive').text('Inactive');
    } else {
        obj.removeClass('status-inactive').addClass('status-active').text('Active');
    }

    return false;
}

function passwordHint(email){
	$.ajax({
		url: BASE_PATH+'application/ajax/passwordhint.php',
		data: { email:email},
		type: 'POST',
		dataType: 'json',
		beforeSend: function() {},
		success: function(retData) {
				if (retData.message != ""){
					  alert(retData.message);
				}else{
						alert("No data");
				}
		}
	});
}