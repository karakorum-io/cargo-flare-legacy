/**
 * Js Handler for Invoice Manager Functionality
 * @author Shahrukh
 * @version 1.0
 */
class InvoiceManager {
    constructor() {
        // When Class loading
    }

    getInvoiceData = (ref, searchFrom) => {
        let searchValue = ref;

        $.ajax({
            type: "POST",
            url: BASE_PATH + 'application/ajax/entities.php',
            data: {
                action: 'GetInvoiceData',
                searchValue: searchValue,
                searchFrom: searchFrom
            },
            dataType: "json",
            success: function(response) {
                if (response.Exists == 1) {
                    if (confirm("Bill already added for this order. Continue?")) {
                        $("#Account").val(response.AccountName);
                        $("#CarrierName").val(response.CarrierName);
                        $("#OrderID").val(response.OrderID);
                        $("#EntityID").val(response.EntityID);
                        $("#AccountID").val(response.AccountID);
                        $("#Amount").val(response.Amount);
                    } else {
                        return false;
                    }
                } else {
                    $("#Account").val(response.AccountName);
                    $("#CarrierName").val(response.CarrierName);
                    $("#OrderID").val(response.OrderID);
                    $("#EntityID").val(response.EntityID);
                    $("#AccountID").val(response.AccountID);
                    $("#Amount").val(response.Amount);
                }
            }
        });
    }

    triggerUploadInvoice = () => {
        let popupConfig = {
            title: "Upload Invoice",
            width: 400,
            modal: true,
            resizable: false,
            draggable: true,
            buttons: [{
                text: "Cancel",
                click: function() {
                    $("#Account").val("");
                    $("#CarrierName").val("");
                    $("#OrderID").val("");
                    $("#EntityID").val("");
                    $("#AccountID").val("");
                    $("#Amount").val("");
                    $(this).dialog('close');
                }
            }, {
                text: "Upload",
                click: function() {
                    let invoiceManager = new InvoiceManager();
                    invoiceManager.uploadInvoice();
                }
            }]
        };
        this.openPopup("UploadInvoiceForm", popupConfig);
    }

    triggerBatchPrint = () => {
        let invoiceIDs = [];
        $("input:checkbox[name=InvoiceID]:checked").each(function() {
            invoiceIDs.push($(this).val());
        });

        let popupConfig;

        if (invoiceIDs.length <= 0) {
            $("#printProceedBtn").attr("disabled", true);
            $("#bulkprintwrapper").html("").html("<center><h3>Please select atleast one bill to pay!</h3></center>");
        } else {
            $("#printProceedBtn").removeAttr("disabled");
            $("#bulkprintwrapper").html("").html("<center><h3>" + invoiceIDs.length + " Invoices ready to process, Proceed?</h3></center>");
        }

        this.openPopup();
    }

    validateEmptyCheckLedger = () => {
        let inVoiceToBeValidated = new Array();
        $.each($("input[name='InvoiceID']:checked"), function() {
            inVoiceToBeValidated.push($(this).val());
        });

        $("PrintProgress").html("Making Sure No Previous Check Payments...");

        $.ajax({
            type: "POST",
            url: BASE_PATH + 'application/ajax/entities.php',
            data: {
                action: 'VALIDATE_EMPTY_LEDGER',
                InvoiceIDs: inVoiceToBeValidated
            },
            dataType: "json",
            success: function(response) {
                if (response.success == true) {
                    var presence = response.Presence;
                    if (presence > 0) {
                        let progress = presence + " Check has been cut in the past from the selected checks";
                        progress += "<br><br><button onclick='invoiceManager.closeBulkPrintCheckPopup()'>Cancel</button> &nbsp; &nbsp;";
                        progress += "<button onclick='invoiceManager.skipPrintedChecksBeforPrinting()'>Skip Printed</button>";
                        $("PrintProgress").html(progress);
                    } else {
                        let progress = " No previous checks found for selected Orders... Please continue";
                        progress += "<br><br><button onclick='invoiceManager.closeBulkPrintCheckPopup()'>Cancel</button> &nbsp; &nbsp;";
                        progress += "<button onclick='invoiceManager.skipPrintedChecksBeforPrinting()'>Continue</button>";
                        $("PrintProgress").html(progress);
                    }
                }
            }
        });
    }

    skipPrintedChecksBeforPrinting = () => {

        let inVoiceToBeSkipped = new Array();
        $.each($("input[name='InvoiceID']:checked"), function() {
            inVoiceToBeSkipped.push($(this).val());
        });

        $.ajax({
            type: "POST",
            url: BASE_PATH + 'application/ajax/entities.php',
            data: {
                action: 'REMOVE_PAID_CHECK_ORDERS',
                InvoiceIDs: inVoiceToBeSkipped
            },
            dataType: "json",
            success: function(response) {
                if (response.success == true) {
                    let presence = response.toPrint.length;

                    if (presence == 0) {
                        let progress = "No New Check to Print";
                        $("PrintProgress").html(progress);
                    } else {
                        let invoiceManager = new InvoiceManager();
                        invoiceManager.generateCheckPDFs(response.toPrint);
                    }
                }
            }
        });
    }

    closeBulkPrintCheckPopup = () => {
        $("PrintProgress").html("Cancelling process!");
        setTimeout(
            function() {
                $("#BulkPrintCheckPopup").modal('hide');
            }, 1000
        )
    }

    triggerReprint = (InvocieID) => {
        if (InvocieID != null) {
            if (confirm("Are you sure you want to reprint selected Invoices?")) {
                this.getInvoiceType(InvocieID);
            }
        } else {
            alert("Some thing went wrong refresh and try again!");
        }

    }

    getInvoiceType = (InvoiceID) => {
        $.ajax({
            type: "POST",
            url: BASE_PATH + 'application/ajax/entities.php',
            data: {
                action: 'GetInvoiceType',
                ID: InvoiceID
            },
            dataType: "json",
            success: function(response) {
                let invoiceM = new InvoiceManager();
                invoiceM.reprintInvoice(InvoiceID, response.type.PaymentType);
            }
        });
    }

    reprintInvoice = (ID, Type) => {

        let URL = "";
        let data = {};
        let folder = "";

        if (Type == 24) {
            URL = BASE_PATH + 'application/ajax/entities.php';
            data = {
                action: 'RePrintACHReceipts',
                IDs: ID
            };
            folder = "ACH";
        } else {
            URL = BASE_PATH + 'external/RePrintChecks.php';
            data = {
                selectedInvoices: ID
            }
            folder = "Checks";
        }

        $.ajax({
            type: "POST",
            url: URL,
            data: data,
            dataType: "json",
            success: function(response) {
                window.open(BASE_PATH + "uploads/Invoices/" + folder + "/" + response.URL);
                location.reload();
            }
        });
    }

    generateCheckPDFs = (InvoiceIDs) => {

        let selectedInvoices = new Array();
        for (let i = 0; i < InvoiceIDs.length; i++) {
            selectedInvoices.push(InvoiceIDs[i].ID);
        }

        console.log("Invoice That are undergoing PRINT:", selectedInvoices);

        let send_carrier_mail = 0;
        if ($("input:checkbox[name=NotifyCarrierPayment]").prop('checked') == true) {
            send_carrier_mail = 1;
        }

        let printType = $("#printType").val();
        let url = "";
        let data = "";

        if (printType == 0) {
            url = BASE_PATH + 'external/PrintChecks.php';
            data = {
                selectedInvoices: selectedInvoices,
                startNumber: $("#startNumber").val(),
                send_carrier_mail: send_carrier_mail
            }
        } else {
            url = BASE_PATH + 'application/ajax/entities.php';
            data = {
                action: 'GenerateCheckPDFs',
                selectedInvoices: selectedInvoices,
                startNumber: $("#startNumber").val(),
                send_carrier_mail: send_carrier_mail
            }
        }

        if (selectedInvoices.length > 0) {
            $.ajax({
                type: "POST",
                url: url,
                data: data,
                dataType: "json",
                success: function(response) {
                    location.reload();
                    window.open(BASE_PATH + "uploads/Invoices/Checks/" + response.URL);
                }
            });
        } else {
            $("#bulkprintwrapper").html("").html("<center><h3>No Invoice Selected!</h3></center>");
        }
    }

    bulkPrintChecks = () => {
        let InvoiceIDs = new Array();
        $.each($("input[name='InvoiceID']:checked"), function() {
            InvoiceIDs.push($(this).val());
        });
        if (InvoiceIDs.length > 0) {
            $("#bulkprintwrapper").html("");
            $.ajax({
                type: "POST",
                url: BASE_PATH + 'application/ajax/entities.php',
                data: {
                    action: 'BulkPrintCheckForBilling',
                    InvoiceIds: InvoiceIDs,
                    startNumber: $("#startNumber").val()
                },
                dataType: "json",
                success: function(response) {
                    if (response.printform == null || response.printform == "") {
                        $("#bulkprintwrapper").append("<center><h3>" + response.message + " <br> Page will be reloaded in 3 seconds</h3></center>");

                        for (let i = 0; i < response.entityIDs.length; i++) {
                            window.open(BASE_PATH + "external/print_check.php?ent=" + response.entityIDs[i], "", "toolbar=yes,scrollbars=yes, resizable=yes,HEIGHT=700,WIDTH=800");
                        }
                        setTimeout(() => {
                            location.reload();
                        }, 3000);
                    } else {
                        $("#bulkprintwrapper").html("").html("<center><h3>" + response.printform + "</h3></center>");
                    }
                }
            });
        } else {
            $("#bulkprintwrapper").html("").html("<center><h3>No Invoice Selected!</h3></center>");
        }
    }

    uploadInvoice = () => {
        $("#uploadInvoicesForm").submit();
    }

    openPopup = (element, config) => {
        $("#BulkPrintCheckPopup").modal('show');
    }

    validateEmpty = (label, value) => {
        if (Boolean(value.trim())) {
            return true;
        } else {
            $("#InvoiceMessage").html(label + " cannot be left blank.");
            setTimeout(() => {
                $("#InvoiceMessage").html("");
            }, 3000);
            return false;
        }
    }

    tabsManager = (flag) => {
        $(".tab").removeClass("active");
        $(".invoiceTabs").hide();
        if (flag == 1) {
            $(".upld1").addClass("active");
            $("#agedInvoices").show();
            $("#active_bill_tab").val(2);
        } else if (flag == 3) {
            $(".upld3").addClass("active");
            $("#ACHInvoices").show();
            $("#active_bill_tab").val(3);
        } else if (flag == 4) {
            $(".upld4").addClass("active");
            $("#HoldInvoices").show();
            $("#active_bill_tab").val(4);
        } else if (flag == 5) {
            $(".upld5").addClass("active");
            $("#PaidBills").show();
            $("#active_bill_tab").val(5);
        } else {
            $(".upld2").addClass("active");
            $("#uploadedInvoices").show();
            $("#active_bill_tab").val(1);
        }

        refresh_filter_records();
    }

    parseDate = str => {
        var mdy = str.split('/');
        return new Date(mdy[2], mdy[0] - 1, mdy[1]);
    }

    dateDifference = (first, second) => {
        return Math.round((second - first) / (1000 * 60 * 60 * 24));
    }

    getInvoiceACHData = () => {

        var lastday = function(y, m) {
            return new Date(y, m + 1, 0).getDate();
        }

        var dateType = $("input[name='ACHptype']:checked").val();
        var startDate = "";
        var endDate = "";

        if (dateType == 1) {
            let time_period = $("#ACH_time_period").val();
            let currentDate = new Date();

            if (time_period == 1) {
                startDate = (currentDate.getMonth() + 1) + "/" + "1" + "/" + currentDate.getFullYear();
                endDate = (currentDate.getMonth() + 1) + "/" + currentDate.getDate() + "/" + currentDate.getFullYear();
            } else if (time_period == 2) {
                startDate = ((currentDate.getMonth()) == 0 ? 12 : (currentDate.getMonth())) + "/1/" + currentDate.getFullYear();
                endDate = (currentDate.getMonth()) + "/" + lastday(currentDate.getFullYear(), currentDate.getMonth()) + "/" + currentDate.getFullYear();
            } else if (time_period == 3) {
                startDate = ((currentDate.getMonth() - 3) == 0 ? 12 : (currentDate.getMonth() - 3)) + "/1/" + currentDate.getFullYear();
                endDate = (currentDate.getMonth() + 1) + "/" + currentDate.getDate() + "/" + currentDate.getFullYear();
            } else if (time_period == 4) {
                startDate = "1" + "/1/" + currentDate.getFullYear();
                endDate = "12" + "/31/" + currentDate.getFullYear();
            } else {
                startDate = "1" + "/1/" + "2015";
                endDate = "12" + "/31/" + currentDate.getFullYear();
            }
        } else {
            startDate = $("#invoiceACHStartDate").val();
            endDate = $("#invoiceACHEndDate").val();
        }

        $.ajax({
            type: "POST",
            url: BASE_PATH + 'application/ajax/entities.php',
            data: {
                action: 'GetACHInvoice',
                startDate: startDate,
                endDate: endDate
            },
            dataType: "json",
            success: function(response) {
                // When there is no invoice
                if (response.ACHInvoices.length == 0) {
                    alert("No Invoices are available for this Time slot");
                } else {
                    let UI = "";
                    for (var key in response.ACHInvoices) {
                        UI += '<tr class="grid-body" id="row-' + response.ACHInvoices[key].ID + '">';
                        UI += '<td align="left">';
                        UI += '<input type="checkbox" name="ACHInvoiceID" class="ACHInvoiceID" value="' + response.ACHInvoices[key].ID + '">&nbsp;';
                        UI += '<a href="/application/orders/show/id/' + response.ACHInvoices[key].EntityID + '" target="_blank">' + response.ACHInvoices[key].OrderID + '</a>';
                        UI += '</td>';
                        UI += '<td align="center">' + formatDate((response.ACHInvoices[key].CreatedAt).split(' ')[0]) + '</td>';
                        UI += '<td align="left">';
                        UI += '<a href="/application/accounts/details/id/' + response.ACHInvoices[key].CarrierID + '" target="_blank">' + response.ACHInvoices[key].CarrierID + '</a>';
                        UI += '</td>';
                        UI += '<td align="left">' + response.ACHInvoices[key].CarrierName + '</td>';
                        UI += '<td align="right">$' + response.ACHInvoices[key].Amount + '</td>';
                        UI += '<td align="center">';
                        UI += '<a href="/uploads/Invoices/' + response.ACHInvoices[key].Invoice + '" target="_blank">Download</a>';
                        UI += '</td>';
                        UI += '<td align="center">' + response.ACHInvoices[key].Term + '</td>';
                        UI += '<td align="left">' + response.ACHInvoices[key].Age + ' Days</td>';
                        UI += '<td align="center">' + formatDate((response.ACHInvoices[key].MaturityDate).split(' ')[0]) + '</td>';
                        UI += '<td align="center">' + response.ACHInvoices[key].UploaderName + '</td>';
                        UI += '<td align="center"><input type="text" class="addTransaction"></td>';
                        UI += '<td align="center">';

                        if (response.ACHInvoices[key].Hold == 0) {
                            UI += '<a href="/application/invoice/CarrierInvoiceStatusUpdate/InvoiceID/' + response.ACHInvoices[key].ID + '">';
                            UI += '<img src="/images/icons/on.png" width="16" height="16">';
                            UI += '</a>';
                        } else {
                            UI += '<a href="/application/invoice/CarrierInvoiceStatusUpdate/InvoiceID/' + response.ACHInvoices[key].ID + '">';
                            UI += '<img src="/images/icons/off.png" width="16" height="16">';
                            UI += '</a>';
                        }
                        UI += '<img src="/images/icons/edit.png" onclick="editPopup(' + response.ACHInvoices[key].ID + ')" width="16" height="16">';
                        UI += '<a href="/application/invoice/DeleteCarrierInvoice/InvoiceID/' + response.ACHInvoices[key].ID + '">';
                        UI += '<img src="/images/icons/delete.png" width="16" height="16">';
                        UI += '</a>';

                        UI += '</td>';
                        UI += '</tr>';
                    }

                    $("#FetchedACHInvoices").html("").html(UI);
                    $("achCount").html(response.ACHInvoices.length);
                }
            }
        });
    }

    getInvoiceHOLDData = () => {
        var lastday = function(y, m) {
            return new Date(y, m + 1, 0).getDate();
        }

        var dateType = $("input[name='HOLDptype']:checked").val();
        var startDate = "";
        var endDate = "";

        if (dateType == 1) {
            let time_period = $("#HOLD_time_period").val();
            let currentDate = new Date();

            if (time_period == 1) {
                startDate = (currentDate.getMonth() + 1) + "/" + "1" + "/" + currentDate.getFullYear();
                endDate = (currentDate.getMonth() + 1) + "/" + currentDate.getDate() + "/" + currentDate.getFullYear();
            } else if (time_period == 2) {
                startDate = ((currentDate.getMonth()) == 0 ? 12 : (currentDate.getMonth())) + "/1/" + currentDate.getFullYear();
                endDate = (currentDate.getMonth()) + "/" + lastday(currentDate.getFullYear(), currentDate.getMonth()) + "/" + currentDate.getFullYear();
            } else if (time_period == 3) {
                startDate = ((currentDate.getMonth() - 3) == 0 ? 12 : (currentDate.getMonth() - 3)) + "/1/" + currentDate.getFullYear();
                endDate = (currentDate.getMonth() + 1) + "/" + currentDate.getDate() + "/" + currentDate.getFullYear();
            } else if (time_period == 4) {
                startDate = "1" + "/1/" + currentDate.getFullYear();
                endDate = "12" + "/31/" + currentDate.getFullYear();
            } else {
                startDate = "1" + "/1/" + "2015";
                endDate = "12" + "/31/" + currentDate.getFullYear();
            }
        } else {
            startDate = $("#invoiceACHStartDate").val();
            endDate = $("#invoiceACHEndDate").val();
        }

        $.ajax({
            type: "POST",
            url: BASE_PATH + 'application/ajax/entities.php',
            data: {
                action: 'GetHoldInvoice',
                startDate: startDate,
                endDate: endDate
            },
            dataType: "json",
            success: function(response) {
                // When there is no invoice
                if (response.ACHInvoices.length == 0) {
                    alert("No Invoices are available for this Time slot");
                } else {
                    let UI = "";
                    for (var key in response.ACHInvoices) {
                        UI += '<tr class="grid-body" id="row-' + response.ACHInvoices[key].ID + '">';
                        UI += '<td align="center">';
                        UI += '<input type="checkbox" name="ACHInvoiceID" class="ACHInvoiceID" value="' + response.ACHInvoices[key].ID + '">&nbsp;';
                        UI += '<a href="/application/orders/show/id/' + response.ACHInvoices[key].EntityID + '" target="_blank">' + response.ACHInvoices[key].OrderID + '</a>';
                        UI += '</td>';
                        UI += '<td align="center">' + formatDate((response.ACHInvoices[key].CreatedAt).split(' ')[0]) + '</td>';
                        UI += '<td align="left">';
                        UI += '<a href="/application/accounts/details/id/' + response.ACHInvoices[key].CarrierID + '" target="_blank">' + response.ACHInvoices[key].CarrierID + '</a>';
                        UI += '</td>';
                        UI += '<td align="left">' + response.ACHInvoices[key].CarrierName + '</td>';
                        UI += '<td align="right">$' + response.ACHInvoices[key].Amount + '</td>';
                        UI += '<td align="center">';
                        UI += '<a href="/uploads/Invoices/' + response.ACHInvoices[key].Invoice + '" target="_blank">Download</a>';
                        UI += '</td>';
                        UI += '<td align="left">' + response.ACHInvoices[key].Term + '</td>';
                        UI += '<td align="left">' + response.ACHInvoices[key].Age + '</td>';
                        UI += '<td align="center">' + formatDate((response.ACHInvoices[key].MaturityDate).split(' ')[0]) + '</td>';
                        UI += '<td align="center">' + response.ACHInvoices[key].UploaderName + '</td>';
                        UI += '<td align="center">';

                        if (response.ACHInvoices[key].Hold == 0) {
                            UI += '<a href="/application/invoice/CarrierInvoiceStatusUpdate/InvoiceID/' + response.ACHInvoices[key].ID + '">';
                            UI += '<img src="/images/icons/on.png" width="16" height="16">';
                            UI += '</a>';
                        } else {
                            UI += '<a href="/application/invoice/CarrierInvoiceStatusUpdate/InvoiceID/' + response.ACHInvoices[key].ID + '">';
                            UI += '<img src="/images/icons/off.png" width="16" height="16">';
                            UI += '</a>';
                        }

                        UI += '<img src="/images/icons/edit.png" onclick="editPopup(' + response.ACHInvoices[key].ID + ')" width="16" height="16">';

                        UI += '</td>';
                        UI += '</tr>';
                    }

                    $("#holdedTableBody").html("").html(UI);
                    $("holdCount").html(response.ACHInvoices.length);
                }
            }
        });
    }

    getInvoicePaidData = () => {
        var lastday = function(y, m) {
            return new Date(y, m + 1, 0).getDate();
        }

        var dateType = $("input[name='Paidptype']:checked").val();
        var startDate = "";
        var endDate = "";

        if (dateType == 1) {
            let time_period = $("#Paid_time_period").val();
            let currentDate = new Date();

            if (time_period == 1) {
                startDate = (currentDate.getMonth() + 1) + "/" + "1" + "/" + currentDate.getFullYear();
                endDate = (currentDate.getMonth() + 1) + "/" + currentDate.getDate() + "/" + currentDate.getFullYear();
            } else if (time_period == 2) {
                startDate = ((currentDate.getMonth()) == 0 ? 12 : (currentDate.getMonth())) + "/1/" + currentDate.getFullYear();
                endDate = (currentDate.getMonth()) + "/" + lastday(currentDate.getFullYear(), currentDate.getMonth()) + "/" + currentDate.getFullYear();
            } else if (time_period == 3) {
                startDate = ((currentDate.getMonth() - 3) == 0 ? 12 : (currentDate.getMonth() - 3)) + "/1/" + currentDate.getFullYear();
                endDate = (currentDate.getMonth() + 1) + "/" + currentDate.getDate() + "/" + currentDate.getFullYear();
            } else if (time_period == 4) {
                startDate = "1" + "/1/" + currentDate.getFullYear();
                endDate = "12" + "/31/" + currentDate.getFullYear();
            } else {
                startDate = "1" + "/1/" + "2015";
                endDate = "12" + "/31/" + currentDate.getFullYear();
            }
        } else {
            startDate = $("#invoicePaidStartDate").val();
            endDate = $("#invoicePaidEndDate").val();
        }

        $.ajax({
            type: "POST",
            url: BASE_PATH + 'application/ajax/entities.php',
            data: {
                action: 'GET_PAID_INVOICE',
                startDate: startDate,
                endDate: endDate
            },
            dataType: "json",
            success: function(response) {
                // When there is no invoice
                if (response.Paid.length == 0) {
                    alert("No Invoices are available for this Time slot");
                } else {
                    let UI = "";
                    for (var key in response.Paid) {
                        UI += '<tr class="grid-body" id="row-' + response.Paid[key].ID + '">';
                        UI += '<td align="center">';
                        UI += '<input type="checkbox" name="ACHInvoiceID" class="ACHInvoiceID" value="' + response.Paid[key].ID + '">&nbsp;';
                        UI += '<a href="/application/orders/show/id/' + response.Paid[key].EntityID + '" target="_blank">' + response.Paid[key].OrderID + '</a>';
                        UI += '</td>';
                        UI += '<td align="center">' + formatDate((response.Paid[key].CreatedAt).split(' ')[0]) + '</td>';
                        UI += '<td align="left">';
                        UI += '<a href="/application/accounts/details/id/' + response.Paid[key].CarrierID + '" target="_blank">' + response.Paid[key].CarrierID + '</a>';
                        UI += '</td>';
                        UI += '<td align="left">' + response.Paid[key].CarrierName + '</td>';
                        UI += '<td align="right">$' + response.Paid[key].Amount + '</td>';
                        UI += '<td align="center">';
                        UI += '<a href="/uploads/Invoices/' + response.Paid[key].Invoice + '" target="_blank">Download</a>';
                        UI += '</td>';
                        UI += '<td align="center">' + (response.Paid[key].PaymentType == 13 ? "Check" : "ACH") + '</td>';
                        UI += '<td align="center">' + (response.Paid[key].PaymentType == 13 ? response.Paid[key].CheckID : response.Paid[key].TxnID) + '</td>';
                        UI += '<td align="center">' + response.Paid[key].Term + '</td>';
                        UI += '<td align="center">' + formatDate((response.Paid[key].PaidDate).split(' ')[0]) + '</td>';
                        UI += '<td align="center" id="ClearUnclear-' + response.Paid[key].ID + '">' + (response.Paid[key].Clear == 1 ? "<Cleared>Cleared</Cleared>" : "<Uncleared>Un-Cleared</Uncleared>") + '</td>';
                        UI += '<td align="center">' + response.Paid[key].UploaderName + '</td>';

                        UI += '<td align="center" id="ClearUnclearButton-' + response.Paid[key].ID + '">';
                        if (response.Paid[key].Clear == 1) {
                            UI += '<button onclick="clear_unclear(0, ' + response.Paid[key].ID + ')" style="color:#fff; background:red;" class="searchform-button searchform-buttonhover">Un-Clear</button>';
                        } else {
                            UI += '<button onclick="clear_unclear(1, ' + response.Paid[key].ID + ')" style="color:#fff; background:green;" class="searchform-button searchform-buttonhover">Clear</button>';
                        }
                        UI += '</td>';

                        UI += '<td align="center">';
                        if (response.Paid[key].PaymentType == 13) {
                            if (response.Paid[key].Void == 0) {
                                UI += '<button onclick="VoidBill(' + response.Paid[key].EntityID + ',' + response.Paid[key].PaymentID + ')" class="searchform-button searchform-buttonhover">Void</button>';
                            }
                        }
                        UI += '</td>';

                        UI += '<td align="center">';
                        if (response.Paid[key].PaymentType == 13) {
                            UI += '<button onclick="invoiceManager.triggerReprint(<' + response.Paid[key].ID + ')" class="searchform-button searchform-buttonhover">Re Print</button> ';
                        }
                        UI += '</td>';
                        UI += '</tr>';
                    }

                    $("#PaidBillList").html("").html(UI);
                    $("paidCount").html(response.Paid.length);
                }
            }
        });
    }

    getInvoicesToBePaid = (startDate, endDate) => {

        var lastday = function(y, m) {
            return new Date(y, m + 1, 0).getDate();
        }

        var dateType = $("input[name='ptype']:checked").val();
        var startDate = "";
        var endDate = "";

        if (dateType == 1) {
            let time_period = $("#time_period").val();
            let currentDate = new Date();

            if (time_period == 1) {
                startDate = (currentDate.getMonth() + 1) + "/" + "1" + "/" + currentDate.getFullYear();
                endDate = (currentDate.getMonth() + 1) + "/" + currentDate.getDate() + "/" + currentDate.getFullYear();
            } else if (time_period == 2) {
                startDate = ((currentDate.getMonth()) == 0 ? 12 : (currentDate.getMonth())) + "/1/" + currentDate.getFullYear();
                endDate = (currentDate.getMonth()) + "/" + lastday(currentDate.getFullYear(), currentDate.getMonth()) + "/" + currentDate.getFullYear();
            } else if (time_period == 3) {
                startDate = ((currentDate.getMonth() - 3) == 0 ? 12 : (currentDate.getMonth() - 3)) + "/1/" + currentDate.getFullYear();
                endDate = (currentDate.getMonth() + 1) + "/" + currentDate.getDate() + "/" + currentDate.getFullYear();
            } else if (time_period == 4) {
                startDate = "1" + "/1/" + currentDate.getFullYear();
                endDate = "12" + "/31/" + currentDate.getFullYear();
            } else {
                startDate = "1" + "/1/" + "2015";
                endDate = "12" + "/31/" + currentDate.getFullYear();
            }
        } else {
            startDate = $("#invoiceStartDate").val();
            endDate = $("#invoiceEndDate").val();
        }

        console.log("Start Date", startDate, "EndDate", endDate);

        $.ajax({
            type: "POST",
            url: BASE_PATH + 'application/ajax/entities.php',
            data: {
                action: 'GetInvoiceToBePaid',
                startDate: startDate,
                endDate: endDate
            },
            dataType: "json",
            success: function(response) {
                let UI = "";
                for (var key in response.AgedInvoice) {
                    // check if the property/key is defined in the object itself, not in parent
                    if (response.AgedInvoice.hasOwnProperty(key)) {
                        UI += "<tr class='grid-body'>";
                        UI += '<td align="left">';
                        UI += '<input type="checkbox" name="InvoiceID" class="InvoiceID" value="' + response.AgedInvoice[key].ID + '">&nbsp;';
                        UI += "<a target='_blank' href='" + BASE_PATH + "application/orders/show/id/" + response.AgedInvoice[key].EntityID + "'>" + response.AgedInvoice[key].OrderID + "</a>";
                        UI += '</td>';
                        UI += "<td align='center'>" + formatDate((response.AgedInvoice[key].CreatedAt).split(' ')[0]) + "</td>";
                        UI += "<td align='left'>" + response.AgedInvoice[key].CarrierID + "</td>";
                        UI += "<td align='left'>" + response.AgedInvoice[key].CarrierName + "</td>";
                        UI += "<td align='right'>$" + response.AgedInvoice[key].Amount + "</td>";
                        UI += '<td align="center"><a href="/uploads/Invoices/' + response.AgedInvoice[key].Invoice + '" target="_blank">Download</a></td>';
                        UI += "<td align='center'>" + response.AgedInvoice[key].Term + "</td>";
                        UI += "<td align='center'>" + response.AgedInvoice[key].Age + "</td>";
                        UI += "<td align='center'>" + formatDate((response.AgedInvoice[key].MaturityDate).split(' ')[0]) + "</td>";
                        UI += "<td align='center'>" + response.AgedInvoice[key].UploaderName + "</td>";
                        UI += "<td align='center'>";


                        UI += '<a href="/application/invoice/CarrierInvoiceStatusUpdate/InvoiceID/' + response.AgedInvoice[key].ID + '">';

                        if (response.AgedInvoice[key].Hold == 0) {
                            UI += '<img src="/images/icons/on.png" width="16" height="16">';
                        } else {
                            UI += '<img src="/images/icons/off.png" width="16" height="16">';
                        }

                        UI += '</a>';
                        UI += '<img src="/images/icons/edit.png" onclick="editPopup(' + response.AgedInvoice[key].ID + ')" width="16" height="16">';
                        UI += '<a href="/application/invoice/DeleteCarrierInvoice/InvoiceID/' + response.AgedInvoice[key].ID + '">';
                        UI += '<img src="/images/icons/delete.png" width="16" height="16">';
                        UI += '</a>';
                        UI += "</td>";
                        UI += "</tr>";
                    }
                }
                $("#matureTableBody").html("");
                $("#matureTableBody").html(UI);
                $("checkCount").html(response.AgedInvoice.length);
            }
        });

    }

    getInvoiceAgedInvoiceData = (ageLimit) => {

        var dateType = $("input[name='ptype']:checked").val();
        var startDate = "";
        var endDate = "";

        if (dateType == 1) {
            let time_period = $("#time_period").val();
            let currentDate = new Date();

            if (time_period == 1) {
                startDate = (currentDate.getMonth() + 1) + "/" + "1" + "/" + currentDate.getFullYear();
                endDate = (currentDate.getMonth() + 1) + "/" + currentDate.getDate() + "/" + currentDate.getFullYear();
            } else if (time_period == 2) {
                startDate = ((currentDate.getMonth()) == 0 ? 12 : (currentDate.getMonth())) + "/1/" + currentDate.getFullYear();
                endDate = (currentDate.getMonth() + 1) + "/" + currentDate.getDate() + "/" + currentDate.getFullYear();
            } else if (time_period == 3) {
                startDate = ((currentDate.getMonth() - 3) == 0 ? 12 : (currentDate.getMonth() - 3)) + "/1/" + currentDate.getFullYear();
                endDate = (currentDate.getMonth() + 1) + "/" + currentDate.getDate() + "/" + currentDate.getFullYear();
            } else if (time_period == 4) {
                startDate = "1" + "/1/" + currentDate.getFullYear();
                endDate = "12" + "/31/" + currentDate.getFullYear();
            } else {
                startDate = "1" + "/1/" + "2000";
                endDate = (currentDate.getMonth() + 1) + "/" + currentDate.getDate() + "/" + currentDate.getFullYear();
            }
        } else {
            startDate = $("#invoiceStartDate").val();
            endDate = $("#invoiceEndDate").val();
        }

        let difference = this.dateDifference(this.parseDate(startDate), this.parseDate(endDate));

        console.log("Start Date: " + startDate);
        console.log("End Date: " + endDate);
        console.log("Date Difference: " + difference);

        if (ageLimit > difference) {
            alert("You can see miniumum " + ageLimit + "day(s) older invoice.");
            return false;
        }

        let maturity = difference;

        $.ajax({
            type: "POST",
            url: BASE_PATH + 'application/ajax/entities.php',
            data: {
                action: 'GetCarrierInvoiceAged',
                maturity: maturity
            },
            dataType: "json",
            success: function(response) {
                let UI = "";
                for (var key in response.AgedInvoice) {

                    // check if the property/key is defined in the object itself, not in parent
                    if (response.AgedInvoice.hasOwnProperty(key)) {
                        console.log(key, response.AgedInvoice[key]);
                        UI += "<tr>";
                        UI += '<td align="left">';
                        UI += '<input type="checkbox" name="InvoiceID" class="InvoiceID" value="' + response.AgedInvoice[key].ID + '">&nbsp;';
                        UI += "<a target='_blank' href='" + BASE_PATH + "application/orders/show/id/" + response.AgedInvoice[key].EntityID + "'>" + response.AgedInvoice[key].OrderID + "</a>";
                        UI += '</td>';
                        UI += "<td align='left'>" + response.AgedInvoice[key].CarrierID + "</td>";
                        UI += "<td align='left'>" + response.AgedInvoice[key].CarrierName + "</td>";
                        UI += "<td align='right'>$" + response.AgedInvoice[key].Amount + "</td>";
                        UI += '<td align="center"><a href="/uploads/Invoices/' + response.AgedInvoice[key].Invoice + '" target="_blank">Download</a></td>';
                        UI += "<td align='center'>" + response.AgedInvoice[key].Age + "</td>";
                        UI += "<td align='center'>" + response.AgedInvoice[key].CreatedAt + "</td>";
                        UI += "<td align='center'>" + response.AgedInvoice[key].UploaderName + "</td>";
                        UI += "<td align='center'>";
                        UI += '<a href="/application/invoice/DeleteCarrierInvoice/InvoiceID/' + response.AgedInvoice[key].ID + '">';
                        UI += '<img src="/images/icons/delete.png" width="16" height="16">';
                        UI += '</a> &nbsp;<img src="/images/icons/edit.png" onclick="editPopup(' + response.AgedInvoice[key].ID + ')" width="16" height="16">&nbsp;';
                        UI += '<a href="/application/invoice/CarrierInvoiceStatusUpdate/InvoiceID/' + response.AgedInvoice[key].ID + '">';

                        if (response.AgedInvoice[key].Hold == 0) {
                            UI += '<img src="/images/icons/on.png" width="16" height="16">';
                        } else {
                            UI += '<img src="/images/icons/off.png" width="16" height="16">';
                        }

                        UI += '</a>';
                        UI += "</td>";
                        UI += "</tr>";
                    }
                }
                $("#matureTableBody").html("").html(UI);
            }
        });
    }

    printACHReceipts = () => {
        let invoiceIDSelected = [];
        $("input:checkbox[name=ACHInvoiceID]:checked").each(function() {
            invoiceIDSelected.push($(this).val());
        });

        // when no ID is selected
        if (invoiceIDSelected.length == 0) {
            alert("Please select atleast one Order");
            return false;
        }

        $.ajax({
            type: "POST",
            url: BASE_PATH + 'application/ajax/entities.php',
            data: {
                action: 'PrintACHReceipts',
                IDs: invoiceIDSelected
            },
            dataType: "json",
            success: function(response) {
                location.reload();
                window.open(BASE_PATH + "uploads/Invoices/ACH/" + response.URL);
            }
        });
    }
}

// function handling delete Invoice from Order detail and Invoice Manager Screen
function editPopup(id) {
    $.ajax({
        type: "POST",
        url: BASE_PATH + 'application/ajax/entities.php',
        data: {
            action: 'GetInvoiceDataByID',
            ID: id
        },
        dataType: "json",
        success: function(response) {
            $(".form-box-textfield").val("");
            // setting values
            $("#CarInvoiceName").val(response.InvoiceData[0].CarrierName);
            $("#CarInvoiceAmount").val(response.InvoiceData[0].Amount);
            $("#CarPayType").val(response.InvoiceData[0].PaymentType);
            $("#CarInvoiceAge").val(response.InvoiceData[0].Age);
            $("#CarInvoiceCreated").val(response.InvoiceData[0].CreatedAt);
            $("#InvoiceDocPreview").attr("href", window.location.origin + "/uploads/Invoices/" + response.InvoiceData[0].Invoice);
            $("#UpdateInvoiceID").val(response.InvoiceData[0].ID);
            $("#UpdateInvoiceEntityID").val(response.InvoiceData[0].EntityID);

            if (response.success == true) {
                $("#editInvoicePopup").dialog({
                    title: "Edit Invoice Data",
                    width: 350,
                    maxHeight: 600,
                    modal: true,
                    resizable: false,
                    draggable: true,
                    buttons: [{
                        text: "Cancel",
                        click: function() { $(this).dialog('close'); }
                    }, {
                        text: "Update",
                        click: function() {
                            $("#UpdateCarrierInvoiceValue").submit();
                        }
                    }]
                }).dialog('open');
            } else {
                alert("Unable to edit, Please try again later!");
            }
        }
    });
}

// voiding bill and making it to be payment ready again
function VoidBill(entityID, paymentID) {
    if (confirm("Are you sure you want to Void this check?")) {
        $.ajax({
            type: "POST",
            url: BASE_PATH + 'application/ajax/entities.php',
            data: {
                action: 'VoidInvoice',
                EntityID: entityID,
                PaymentID: paymentID
            },
            dataType: "json",
            success: function(response) {
                if (response.success == true) {
                    //location.reload();
                    refresh_filter_records();
                }
            }
        });
    }
}

// update clear and unclear status
function clear_unclear(Flag, InvoiceID) {

    $.ajax({
        type: "POST",
        url: BASE_PATH + 'application/ajax/entities.php',
        data: {
            action: 'InvoiceCheckClearUnclear',
            InvoiceID: InvoiceID,
            Flag: Flag
        },
        dataType: "json",
        success: function(response) {
            refresh_filter_records();
        }
    });
}

// update transaction id for ACH paymnets
function updateTxnID(InvoiceID, TxnID) {
    if (TxnID !== "") {
        $.ajax({
            type: "POST",
            url: BASE_PATH + 'application/ajax/entities.php',
            data: {
                action: 'UPDATE_ACH_TXN_ID',
                InvoiceID: InvoiceID,
                TxnID: TxnID
            },
            dataType: "json",
            success: function(response) {
                console.log(response.Message);
            }
        });
    } else {
        alert("Transaction ID cannot be left blank");
    }
}

// format date utility function 
function formatDate(inputDate) {
    var date = new Date(inputDate);
    if (!isNaN(date.getTime())) {
        // Months use 0 index.
        return date.getMonth() + 1 + '/' + date.getDate() + '/' + date.getFullYear();
    }
}

// refrehing filtered records
function refresh_filter_records() {
    let search_tab = $("#active_bill_tab").val();
    let search_string = $(".searchBill").val();
    let action = "";

    switch (search_tab) {
        case "1":
            action = "GET_UPLOADED_BILLS";
            break;
        case "2":
            action = "GET_INVOICES_TO_BE_PAID";
            break;
        case "3":
            action = "GET_ACH_TO_BE_PAID";
            break;
        case "4":
            action = "GET_HOLD_BILLS";
            break;
        case "5":
            action = "GET_PAID_BILLS";
            break;
        default:
            console.log("Invalid Search!");
            break;
    }

    let dates = get_start_end_dates();

    // vaildate empty dates
    if (dates[0] == undefined || dates[0] == "") {
        dates[0] = "1/1/2015";
    }
    if (dates[1] == undefined || dates[1] == "") {
        dates[1] = "12/31/2019";
    }

    let data_fetch = {
        action: action,
        rowcount: "",
        "pagination_setting": $("#pagination-setting").val(),
        "offset": $(".numberOfRecordsPerPage").val(),
        search_string: search_string,
        startDate: dates[0],
        endDate: dates[1]
    };

    get_filtered_result("ajax/invoices.php", data_fetch);
}

// search uploaded
function filter_records(url = "ajax/invoices.php") {

    let search_tab = $("#active_bill_tab").val();
    let search_string = $(".searchBill").val();
    let action = "";

    switch (search_tab) {
        case "1":
            action = "GET_UPLOADED_BILLS";
            break;
        case "2":
            action = "GET_INVOICES_TO_BE_PAID";
            break;
        case "3":
            action = "GET_ACH_TO_BE_PAID";
            break;
        case "4":
            action = "GET_HOLD_BILLS";
            break;
        case "5":
            action = "GET_PAID_BILLS";
            break;
        default:
            console.log("Invalid Search!");
            break;
    }

    let dates = get_start_end_dates();

    // vaildate empty dates
    if (dates[0] == undefined || dates[0] == "") {
        dates[0] = "1/1/2015";
    }
    if (dates[1] == undefined || dates[1] == "") {
        dates[1] = "12/31/2019";
    }

    let data_fetch = {
        action: action,
        rowcount: $("#rowcount").val() == undefined ? "" : $("#rowcount").val(),
        "pagination_setting": $("#pagination-setting").val(),
        "offset": $(".numberOfRecordsPerPage").val(),
        search_string: search_string,
        startDate: dates[0],
        endDate: dates[1]
    };

    get_filtered_result(url, data_fetch);
}

// function to get filtered data
function get_filtered_result(url, data) {
    console.log(data);
    $.ajax({
        url: url,
        type: "GET",
        data: data,
        beforeSend: function() { $("body").nimbleLoader("show"); },
        success: function(UI) {
            $(".pagination-result").html(UI);

            let search_tab = $("#active_bill_tab").val();
            switch (search_tab) {
                case "1":
                    $("uploadedCount").text($("#filtered_tab_counts").val());
                    break;
                case "2":
                    $("checkCount").text($("#filtered_tab_counts").val());
                    break;
                case "3":
                    $("achCount").text($("#filtered_tab_counts").val());
                    break;
                case "4":
                    $("holdCount").text($("#filtered_tab_counts").val());
                    break;
                case "5":
                    $("paidCount").text($("#filtered_tab_counts").val());
                    break;
                default:
                    console.log("Invalid Search!");
                    break;
            }
            $("body").nimbleLoader("hide");
        },
        error: function() {
            console.log("ERROR : Unable to get Pagination DATA");
        }
    });
}

// get start date & end date
function get_start_end_dates() {
    var lastday = function(y, m) {
        return new Date(y, m + 1, 0).getDate();
    }

    let dateType = $("input[name='ptype']:checked").val();

    let startDate = "";
    let endDate = "";

    if (dateType == 1) {
        let time_period = $("#bill_time_period").val();
        let currentDate = new Date();

        if (time_period == 1) {
            startDate = (currentDate.getMonth() + 1) + "/" + "1" + "/" + currentDate.getFullYear();
            endDate = (currentDate.getMonth() + 1) + "/" + currentDate.getDate() + "/" + currentDate.getFullYear();
        } else if (time_period == 2) {
            startDate = ((currentDate.getMonth()) == 0 ? 12 : (currentDate.getMonth())) + "/1/" + currentDate.getFullYear();
            endDate = (currentDate.getMonth()) + "/" + lastday(currentDate.getFullYear(), currentDate.getMonth()) + "/" + currentDate.getFullYear();
        } else if (time_period == 3) {
            startDate = ((currentDate.getMonth() - 3) == 0 ? 12 : (currentDate.getMonth() - 3)) + "/1/" + currentDate.getFullYear();
            endDate = (currentDate.getMonth() + 1) + "/" + currentDate.getDate() + "/" + currentDate.getFullYear();
        } else if (time_period == 4) {
            startDate = "1" + "/1/" + currentDate.getFullYear();
            endDate = "12" + "/31/" + currentDate.getFullYear();
        } else {
            startDate = "1" + "/1/" + "2015";
            endDate = "12" + "/31/" + currentDate.getFullYear();
        }
    } else {
        startDate = $("#bill_start_date").val();
        endDate = $("#bill_end_date").val();
    }

    return [startDate, endDate];
}

// change pagination style
function change_pagination_type(option, url) {
    if (option != "") {
        filter_records(url);
    }
}