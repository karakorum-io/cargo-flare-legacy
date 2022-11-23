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
					}else{
						alert("Can't update status.");
					}
				}
		});

    return false;
}

function aprovedText(url, rowId) {
	$.ajax({
			url: url,
			data: {},
			type: 'GET',
			dataType: 'json',
			success: function(response) {
				if (response.success == true) {
					var obj = $(rowId);
					    if (obj.text() == 'Aproved') {
					        obj.removeClass('status-active').addClass('status-inactive').text('Pending');
					    } else {
					        obj.removeClass('status-inactive').addClass('status-active').text('Aproved');
					    }
					}else{
						alert("Can't update status.");
					}
				}
		});

    return false;
}
