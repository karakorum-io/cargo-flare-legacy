const $engine = {

    // reset search filters
    resetSearch: () => {
        $engine.confirm("Are you sure, you want to clear search?", action => {
            if (action === "confirmed") {
                location.reload();
            }
        });
    },

    // apply search filters
    applyShipperSearch: ref => {
        ref.innerHTML = "Appling filters ...";

        if ($("input[name='acc_search_result_item_leads']").is(':checked') == true) {
            if ($("input[name='acc_search_result_item_leads']:checked").length == 0) {
                return;
            }
            applySearchLeads($("input[name='acc_search_result_item_leads']:checked").val());
        }

        if ($("input[name='acc_search_result_item']").is(':checked') == true) {
            if ($("input[name='acc_search_result_item']:checked").length == 0) {
                return;
            }
            applySearch($("input[name='acc_search_result_item']:checked").val());
        }

        $("#acc_search_dialog").modal('hide');
    },

    // confirmation box
    confirm: (message, result) => {
        Swal.fire({
            title: "Are you sure?",
            text: message,
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes!'
        }).then((action) => {
            if (action.value) {
                result('confirmed');
            } else {
                result('not-confirmed');
            }
        });
    },

    // notification
    notify: message => {
        swal.fire(message);
    },

    // notification
    alert: message => {
        Swal.fire(message);
    },

    // reassign entity
    reassign: (id, selection) => {

        if (!selection) {
            this.notify("Invalid Member");
            return false;
        }

        if (!id) {
            this.notify("Invalid Entity ID");
            return false;
        }

        let memberId = $("#company_members_" + selection).val();

        $.ajax({
            type: 'POST',
            url: BASE_PATH + 'application/ajax/entities.php',
            dataType: "json",
            data: {
                action: 'reassign',
                assign_id: memberId,
                entity_id: id
            },
            success: function(response) {
                if (response.success == true) {
                    window.location.reload();
                } else {
                    this.notify("Reassign failed. Try again later, please.");
                }
            },
            error: function(response) {
                this.notify("Reassign failed. Try again later, please.");
            }
        });
    },

    // async request
    asyncPost: (url, data, callback) => {
        $.ajax({
            type: "POST",
            url: url,
            dataType: "JSON",
            data: data,
            success: function(result) {
                callback(result);
            }
        });
    }
}