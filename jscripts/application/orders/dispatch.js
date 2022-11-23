function dispatchSheetHistory(dispatch_id) {
    $.ajax({
        type: "POST",
        url: BASE_PATH + "application/ajax/dispatch.php",
        dataType: 'json',
        data: {
            action: 'history',
            id: dispatch_id
        },
        success: function(res) {
            if (res.success) {
                $("#dispatch_sheet_history_dialog_content").html(res.html);
                $("#dispatch_sheet_history_dialog").modal("show");
                for (i in res.changes) {
                    $("#dispatch_sheet_history_dialog_content ." + res.changes[i]);
                }
            } else {
                alert("Can't get Dispatch Sheet history");
            }
        }
    });
}

function cancelDispatchSheet(dispatch_id) {
    $.ajax({
        type: "POST",
        url: BASE_PATH + "application/ajax/dispatch.php",
        dataType: 'json',
        data: {
            action: 'cancel',
            id: dispatch_id
        },
        success: function(res) {
            if (res.success) {
                document.location.reload();
            } else {
                alert("Can't cancel Dispatch Sheet");
            }
        }
    });
}