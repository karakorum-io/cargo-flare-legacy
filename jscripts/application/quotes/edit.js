function setEditBlock() {
	$.ajax({
		type: "POST",
		url: BASE_PATH+"application/ajax/entities.php",
		dataType: 'json',
		data: {
			action: 'setBlock',
			entity_id: entity_id
			},
			success: function(response) {
				if (response.success == false) {
					document.location.reload();
				}
			}
	});
}

function quickPrice() {
    var data = {
        origin_city: $('#origin_city').val(),
        origin_state: $('#origin_state').val(),
        origin_zip: $('#origin_zip').val(),
        origin_country: $('#origin_country').val(),
        destination_city: $('#destination_city').val(),
        destination_state: $('#destination_state').val(),
        destination_zip: $('#destination_zip').val(),
        destination_country: $('#destination_country').val(),
        shipping_est_date: $('#shipping_est_date').val(),
        shipping_ship_via: $('#shipping_ship_via').val(),
        quote_id: $('#quote_id').val()
    };
    if (data.origin_city == '' || data.origin_state == '' || data.origin_zip == '') {
        alert('Invalid Origin Information');
        return;
    }
    if (data.destination_city == '' || data.destination_state == '' || data.destination_zip == '') {
        alert('Invalid Destination Information');
        return;
    }
    if (data.shipping_est_date == '') {
        alert('Invalid Shipping Date');
        return;
    }
    if (data.shipping_ship_via == '') {
        alert('You should specify "Ship Via" field');
        return;
    }
    $("body").nimbleLoader("show");
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: BASE_PATH+'application/ajax/autoquote.php',
        data: data,
        success: function(res) {
            $("#carrier_pay").html(decodeURIComponent(res.carrier_pay));
            $("#total_deposit").html(decodeURIComponent(res.total_deposit));
            $("#total_tariff").html(decodeURIComponent(res.total_tariff));
            alert(res.message);
        },
        error: function() {
            alert('Failed to calculate Quick Price');
        },
        complete: function() {
            $("body").nimbleLoader("hide");
        }

    });
}

$(document).ready(function(){
	if (!entityBlocked) {
		setEditBlock();
		setInterval(setEditBlock, 60000);
	} else {
		alert("Someone editing this form right now. You have read-only access.")
	}

});