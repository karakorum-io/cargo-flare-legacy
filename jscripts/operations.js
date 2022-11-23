const $operations = {

    // process payment
    processPayment: () => {
        let payment_type_selector = $("input:radio[name='payment_type_selector']:checked").val();
        let dispatchValues = "";

        if (payment_type_selector == "internally") {
            dispatchValues = $("#internally_form").serializeArray();
        } else if (payment_type_selector == "carrier") {
            dispatchValues = $("#carrier_form").serializeArray();
        } else if (payment_type_selector == "terminal") {
            dispatchValues = $("#terminal_form").serializeArray();
        } else if (payment_type_selector == "gateway") {
            dispatchValues = $("#payment_form").serializeArray();
        }

        dispatchValues.push({ 'name': 'action', 'value': 'payment' });
        dispatchValues.push({ 'name': 'entity_id', 'value': $("#entity_id_payment").val() });

        $("#payment_dialog").find(".modal-body").addClass('kt-spinner kt-spinner--lg kt-spinner--dark');

        $engine.asyncPost(BASE_PATH + "application/ajax/entities.php", dispatchValues, (response) => {
            if (response.success) {
                $("#payment_dialog").find(".modal-body").removeClass('kt-spinner kt-spinner--lg kt-spinner--dark');
                $engine.notify("Payment processed successfully");
            } else {
                $engine.notify("Something went wrong. Try again later.");
            }
            $("#payment_dialog").modal('hide');
            KTApp.unblockPage();
        });
    },

    // process refund payment
    refund: (gateway, transactionId, amount) => {
        $engine.confirm("Are you sure, you want to process refund?", action => {
            if (action === "confirmed") {
                if (gateway == 9) {
                    $operations.easyRefund(transactionId, amount);
                } else {
                    $engine.notify("Automatic refund not available for Paypal and Authorize.net");
                }
            }
        });
    },

    // process refund from easy pay
    easyRefund: (transactionId, amount) => {
        $engine.asyncPost(BASE_PATH + "application/ajax/payments.php", {
            action: "REFUND_EASY_PAY",
            transactionId: transactionId,
            amount: amount
        }, (response) => {
            if (response.success) {
                $engine.notify("Refund process initiated");
            }
        });
    },

    // process void payment
    void: (gateway, transactionId, amount) => {
        $engine.confirm("Are you sure, you want to process void?", action => {
            if (action === "confirmed") {
                if (gateway == 9) {
                    $operations.easyVoid(transactionId, amount);
                } else {
                    $engine.notify("Automatic void not available for Paypal and Authorize.net");
                }
            }
        });
    },

    // process refund from easy pay
    easyVoid: (transactionId, amount) => {
        $engine.asyncPost(BASE_PATH + "application/ajax/payments.php", {
            action: "VOID_EASY_PAY",
            transactionId: transactionId,
            amount: amount
        }, (response) => {
            if (response.success) {
                $engine.notify("Void process initiated");
            }
        });
    },

    // get credit cards saved
    getSavedCards: accountId => {
        if (accountId) {
            $engine.asyncPost(BASE_PATH + "application/ajax/accounts.php", {
                action: "AllCards",
                AccountID: accountId
            }, (response) => {
                console.log("Populating UI XXXX");
                let html = "";
                response.Cards.forEach(function(value){
                    console.log(value);
                    html += `
                        <option value="${value.Number}" CardId="${value.CardId}">xxxx-xxxx-xxxx-${ value.Number.substring(value.Number.length - 4)}</option>
                    `;
                    $("#available_cards").html(html);
                    $("#available_cards").val($("#cc_number").val());
                });
            });
        }
    },

    // dispatch order with new carrier
    dispatchWithNewCarrier: (orderId = null) => {
        if (orderId) {
            let entityId = orderId;
            location.href = BASE_PATH + "application/orders/dispatchnew/id/" + entityId;
        } else {
            let entityId = $(".order-checkbox:checked").val();
            location.href = BASE_PATH + "application/orders/dispatchnew/id/" + entityId;
        }
    },

    // dispatch order with existing carrier
    dispatchWithExistingCarrier: () => {
        if ($("input[name='acc_search_result_item']:checked").length == 0) {
            $engine.notify("Select one carrier");
            return;
        }

        let entityId = $(".order-checkbox:checked").val();

        if (window.location.href.split('/').slice(-2)[0] == "id") {
            entityId = window.location.href.substring(window.location.href.lastIndexOf('/') + 1);
        }

        let accountId = acc_data[$("input[name='acc_search_result_item']:checked").val()].id;

        location.href = BASE_PATH + "application/orders/dispatchnew/id/" + entityId + "/acc/" + accountId;
    },

    // undispatch
    cancelDispatch: (entityId = null) => {
        $engine.confirm("Are you sure, you want to cancel dispatch?", action => {
            if (action === "confirmed") {

                let orderId = null;

                if (entityId) {
                    orderId = entityId;
                } else {

                    if ($(".order-checkbox:checked").length == 0) {
                        $engine.notify("Order not selected");
                        return;
                    }

                    if ($(".order-checkbox:checked").length > 1) {
                        $engine.notify("Select exactly one order");
                        return;
                    }

                    orderId = $(".order-checkbox:checked").val();
                }

                $engine.asyncPost(BASE_PATH + "application/ajax/dispatch.php", {
                    action: "cancelNew",
                    id: orderId
                }, (response) => {
                    if (response.success) {
                        $engine.notify("Order successfully undispatched.");
                    } else {
                        $engine.notify("Can't cancel Dispatch Sheet");
                    }

                    location.reload();
                });
            }
        });
    },

    // uploadInvoice
    uploadInvoice: () => {
        // open invoice modal
        $("#UploadInvoiceForm").modal('show');
    },

    // handle pending dispatch action
    handlePendingDispatch: (ref, entity) => {
        
        if(ref.value == 0){
            return false;
        }

        if(ref.value == 'add'){
            $("#pending-dispatch-modal").modal('show');
        }

        if(ref.value == 'remove'){
            $operations.removeFromPendingDispatch(entity)
        }
    },

    // add to pending dispatch board
    addToPendingDispatch: (entity) => {
        let name = $("#pdName").val();
        let contact = $("#pdContact").val();
        let phone = $("#pdPhone").val();
        let email = $("#pdEmail").val();
        let comment = $("#pdComment").val();
        let entity_id = entity;

        if(comment == ""){
            $engine.notify("Comment cannot be left blank");
            return false;
        }

        $engine.asyncPost(BASE_PATH + "application/ajax/entities.php", {
            action: "MARK_PENDING_DISPATCH",
            id: entity_id,
            name: name,
            contact: contact,
            email: email,
            phone: phone,
            comment: comment
        }, (response) => {
            if (response.success) {
                $engine.notify("Added to pending dispatch");
                location.reload();
            } else {
                $engine.notify("Something went wrong");
            }
        });
    },

    removeFromPendingDispatch: (entity) => {
        $engine.confirm("Are you sure, you want to remove from pending dispatch?", action => {
            if (action === "confirmed") {

                $engine.asyncPost(BASE_PATH + "application/ajax/entities.php", {
                    action: "REMOVE_PENDING_DISPATCH",
                    id: entity,
                }, (response) => {
                    if (response.success) {
                        $engine.notify("Removed from pending dispatch");
                        location.reload();
                    } else {
                        $engine.notify("Something went wrong");
                    }
                });
            }
        });
    },

    searchCarrierWizard: () => {

    }
}