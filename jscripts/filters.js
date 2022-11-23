
// live
$('.digit-only, .email, .latin, .alphanum, .decimal').on('keypress', function(e) {
	var code = ($.browser.msie)?e.keyCode:e.charCode;
	if ($(this).hasClass('digit-only')) {
		if (!code || /[\d]/.test(String.fromCharCode(code))) return true;
	}
	if ($(this).hasClass('email')) {
		if (!code || /[\w\d.@-]/.test(String.fromCharCode(code))) return true;
	}
	if ($(this).hasClass('latin')) {
		if (!code || /[\w\d\s',-\.]/.test(String.fromCharCode(code))) return true;
	}
	if ($(this).hasClass('alphanum')) {
		if (!code || /[\w\d\.]/.test(String.fromCharCode(code))) return true;
	}
	if ($(this).hasClass('decimal')) {
		if (!code || /[\d]/.test(String.fromCharCode(code)) || (/[.]/.test(String.fromCharCode(code)) && !/[.]/.test($(this).val()))) return true;
	}
	return false;
});