var datepickerSettings = {
    changeMonth: true,
    dateFormat: 'mm/dd/yy',
    changeYear: true,
    duration: '',
    buttonImage: BASE_PATH + 'images/icons/calendar.gif',
    buttonImageOnly: true,
    showOn: 'both'
};

function checkEmailAddress(email) {
    // the following expression must be all on one line...
    var goodEmail = email.match(/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/);
    return goodEmail;
}

function loadAnchorContent(id){
	parts = window.location.href.split("#");
	if(parts.length == 2){
		params = parts[1].split("/");
		params = params[1];
	}else{
		params = 'Biography';
	}

	loadContent(id, params);
}

function are_you_sure() {
	return confirm("Are you sure?");
}

function loadContent(id, obj){
	if (typeof obj == 'object'){
		p = $(obj).attr('href').split("/");
		p = p[1];
	}else{
		p = obj;
	}

	$.ajax({
		url: BASE_PATH + 'attorneys/getInfo',
		data: {
			member_id: id,
			info: p
		},
		type: 'POST',
		dataType: 'json',
		success: function(response){
			$('#content_title').html(p);
			$('#content1').html(response.content);
		},
		beforeSend: function (){
			showLoadingPage();
		},
		complete: function(){
			hideLoading();
		}
	});

	return false;
}
/* Start loading indicator */
var OverlayTpl = '<div id="ajax-overlay"></div>';
var OverlayObj = null;
var LoadingTpl = '<div id="ajax-loading"><table><tr><td><img src="'+ BASE_PATH + 'images/loading.gif" width="16" height="16" alt="..." /></td><td>@text@</td></tr></table></div>';
var LoadingObj = null;
function showLoading(fromLeft, text, pObj1) {
	if (!text) {
		text = 'Loading...';
	}
	var wObj = pObj1;
	pPos = pObj1.position();
	LoadingObj = $(LoadingTpl.replace('@text@', text)).remove().appendTo('body');
	LoadingObj.css({'top': ((wObj.height() - LoadingObj.outerHeight()) / 2 + pPos.top), 'left': (wObj.width() - LoadingObj.outerWidth() + fromLeft) / 2 + pPos.left});
}
function showLoadingPage(text) {
	if (typeof(text) != 'string') {
		text = 'Loading...';
	}

	pObj = $('#content1');
	pPos = pObj.position();

	var sukaIE = pObj.height() + parseInt(pObj.css('paddingTop'));
	if (sukaIE > 0) {
		sukaIE--;
		sukaIE--;
	}

	var OverlayCss = {
		'width': pObj.width() - 2,
		'height': sukaIE,
		'top': pPos.top + 1,
		'left': pPos.left + 1,
		'opacity': 0.8
	};

	OverlayObj = $(OverlayTpl).remove().appendTo(pObj).css(OverlayCss);
	showLoading(0, text,pObj);
}
function hideLoading() {
	if (LoadingObj) {
		LoadingObj.remove();
		LoadingObj = null;
	}
	if (OverlayObj) {
		OverlayObj.remove();
		OverlayObj = null;
	}
}
/* End loading indicator */
function number_format(number, decimals, dec_point, thousands_sep) {
    var n = number, prec = decimals;
    n = !isFinite(+n) ? 0 : +n;
    prec = !isFinite(+prec) ? 0 : Math.abs(prec);
    var sep = (typeof thousands_sep == "undefined") ? ',' : thousands_sep;
    var dec = (typeof dec_point == "undefined") ? '.' : dec_point;

    var s = (prec > 0) ? n.toFixed(prec) : Math.round(n).toFixed(prec); //fix for IE parseFloat(0.55).toFixed(0) = 0;

    var abs = Math.abs(n).toFixed(prec);
    var _, i;

    if (abs >= 1000) {
        _ = abs.split(/\D/);
        i = _[0].length % 3 || 3;

        _[0] = s.slice(0,i + (n < 0)) +
              _[0].slice(i).replace(/(\d{3})/g, sep+'$1');

        s = _.join(dec);
    } else {
        s = s.replace('.', dec);
    }

    if (s == 0 || s == '0') {
        s = '';
    }

    return s;
}
function fromMoney(val, retEmptyIfZero) {
    if (!val) {
        if (retEmptyIfZero) {
            return '';
        }
        return 0;
    }

    var value = val.toString().replace(/[^\d^.]*/g, '');
    if (retEmptyIfZero) {
        return value == 0 ? '' : value;
    }

    return value;
}
function toMoney(val, decimals) {
    if (!val) {
        return '';
    }
    var value = val.toString().replace(/,/g, '');

    if (!decimals && decimals != 0) {
        decimals = 2;
    }

    return number_format(value, decimals);
}

function redirect(url) {
    document.location.href = url;
}

function afterDelete(rowObj) {
    rowObj.css('backgroundColor', '#ff7878');
    rowObj.fadeOut('slow', function(){
        rowObj.remove();
        var rTotalObj = $('#pager-rtotal');
        var totalRecords = parseInt(rTotalObj.text());
        rTotalObj.text('' + (totalRecords-1) + '');

        if (totalRecords == 1) {
            $('#grid-pager').remove();
        }

		$('.grid tr').removeClass('first-row');
		$('.grid tr:first-child').next().addClass('first-row');
    });
}

function deleteItem(url, rowId, reload) {
	
    if (confirm('Are you sure you want to delete this record?')) {
	    $.ajax({
			url: url,
			data: {},
			type: 'GET',
			dataType: 'json',
			success: function(response) {
				if (response.success == true) {
					if (reload == "updatecc"){
                    	reloadCombo();
					}
					if (reload == 'reload') {
						window.location.reload();
					}
					afterDelete($('#'+rowId));
				}else{
					alert("Can't delete item.");
				}
			}
		});
    }
    return false;
}





function deleteFile(url, id, inv_id) {
    if (confirm('Вы уверены?')) {
        $.post(url, {id: id, inv_id: inv_id});
            var idObj = $('#file-'+id);
            idObj.fadeOut(function(){
                idObj.remove();
            });
    }
    return false;
}


$(document).ready(function() {
	$(".phone").mask("(999) 999-9999? x99999");
	//$(".creditcard").mask("9999999999999?999");
	$(".cvv").mask("999?9");
	$(".zip").mask("99999?-9999");
	$(".money").maskMoney({thousands:"", decimal:".", allowZero: true});
	$('.dateformat').datepicker(datepickerSettings);
});