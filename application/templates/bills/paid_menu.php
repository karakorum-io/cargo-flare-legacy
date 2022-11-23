<div class="alert alert-light alert-elevate mt-5 mb-0" role="alert">

	<ul class="nav">
		<li class="nav-item">

        	<a class="nav-link <?=(@$_GET['type'] == 'cleared_check') ? " active" : ""?>" href="<?=SITE_IN?>application/bills/paid/type/cleared_check">

                Clear Checks (<paidCheckClearCount><?php echo $this->daffny->tpl->count['paid_checks_cleared']; ?></paidCheckClearCount>)

            </a>

        </li>

        <li class="nav-item">

        	<a class="nav-link <?=(@$_GET['type'] == 'uncleared_check') ? " active" : ""?>" href="<?=SITE_IN?>application/bills/paid/type/uncleared_check">

                UnClear Checks (<paidCheckUnclearCount><?php echo $this->daffny->tpl->count['paid_checks_uncleared']; ?></paidCheckUnclearCount>)

            </a>

        </li>

        <li class="nav-item">

        	<a class="nav-link <?=(@$_GET['type'] == 'cleared_ach') ? " active" : ""?>" href="<?=SITE_IN?>application/bills/paid/type/cleared_ach">

                Clear ACH (<paidACHClearCount><?php echo $this->daffny->tpl->count['paid_ach_cleared']; ?></paidACHClearCount>)

            </a>

        </li>

        <li class="nav-item">

        	<a class="nav-link <?=(@$_GET['type'] == 'uncleared_ach') ? " last active" : ""?>" href="<?=SITE_IN?>application/bills/paid/type/uncleared_ach">

                UnClear ACH (<paidACHUnclearCount><?php echo $this->daffny->tpl->count['paid_ach_uncleared']; ?></paidACHUnclearCount>)

            </a>

        </li>

	</ul>

</div>

<script>



    let bills = [];

    let bill_amount = [];

    let entities = [];

    let payments_ids = [];

    let selected_bills = 0;

    let net_bill_amount = 0;



    let make_selection = (ref, InvoiceID, Amount, EntityID, PayementID) => {

        if(ref.checked){

            if(InvoiceID != "" && Amount != ""){

                bills.push(InvoiceID);

                entities.push(EntityID);

                payments_ids.push(PayementID);

                bill_amount.push(Amount);

                selected_bills = selected_bills + 1;

                net_bill_amount = net_bill_amount + Amount;

            }

        } else {

            bills.pop(InvoiceID);

            entities.pop(EntityID);

            payments_ids.pop(PayementID);

            bill_amount.pop(Amount);

            selected_bills = selected_bills - 1;

            net_bill_amount = net_bill_amount - Amount;

        }



        $("TotalChecks").html("").html(selected_bills);

        $("TotalCosts").html("").html("$"+net_bill_amount);

        $("VoidButton").html("").html("("+selected_bills+")");

        $("UnclearButton").html("").html("("+selected_bills+")");

    }



    let trigger_reprint = (InvocieID) => {

        if (InvocieID != null) {

            if (confirm("Are you sure you want to reprint selected Invoices?")) {

                getInvoiceType(InvocieID);

            }

        } else {

            alert("Some thing went wrong refresh and try again!");

        }

    }



    let getInvoiceType = (InvoiceID) => {

        $.ajax({

            type: "POST",

            url: BASE_PATH + 'application/ajax/entities.php',

            data: {

                action: 'GetInvoiceType',

                ID: InvoiceID

            },

            dataType: "json",

            success: function (response) {

                reprintInvoice(InvoiceID, response.type.PaymentType);

            }

        });

    }



    let reprintInvoice = (ID, Type) => {

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

            success: function (response) {

                window.open(BASE_PATH + "uploads/Invoices/" + folder + "/" + response.URL);

            }

        });

    }



    let void_check = () => {

        if (confirm("Are you sure you want to Void this check?")) {

            $.ajax({

                type: "POST",

                url: BASE_PATH + 'application/bills/void_bills',

                data: {

                    EntityID: entities,

                    PaymentID: payments_ids

                },

                dataType: "json",

                success: function (response) {

                    location.reload();

                }

            });

        }

    }



    let clear_bills = () => {

        if (confirm("Are you sure you want to Clear?")) {

            $.ajax({

                type: "POST",

                url: BASE_PATH + 'application/bills/clear_unclear',

                data: {

                    IDs: bills,

                    flag : 1

                },

                dataType: "json",

                success: function (response) {

                    printclear_unclear_recipts(1);

                }

            });

        }

    }



    let unclear_bills = () => {

        if (confirm("Are you sure you want to UnClear?")) {

            $.ajax({

                type: "POST",

                url: BASE_PATH + 'application/bills/clear_unclear',

                data: {

                    IDs: bills,

                    flag : 0

                },

                dataType: "json",

                success: function (response) {

                    printclear_unclear_recipts(0);

                }

            });

        }

    }



    let printclear_unclear_recipts = (flag) => {

        $.ajax({

            type: "POST",

            url: BASE_PATH + 'application/bills/printclear_unclear_recipts',

            data: {

                IDs: bills,

                flag : flag

            },

            dataType: "json",

            success: function (response) {

                window.open(response.URL);

                location.reload();

            }

        });

    }



    let select_all = () => {

        bills = [];

        bill_amount = [];

        entities = [];

        payments_ids = [];

        selected_bills = 0;

        net_bill_amount = 0;



        $("input:checkbox[name=InvoiceID]").each(function () {

            $(this).attr("checked",true);

            let InvoiceID = $(this).attr('InvoiceID');

            let Amount = $(this).attr('Amount');

            let EntityID = $(this).attr('EntityID');

            let PaymentID = $(this).attr('PaymentID');



            $("#select_unselect").attr("onclick","unselect_all()");

            $("#select_unselect").html("UnSelect All");



            if(InvoiceID != "" && Amount != ""){

                bills.push(InvoiceID);

                entities.push(EntityID);

                payments_ids.push(PaymentID);

                bill_amount.push(Amount);

                selected_bills = Number(selected_bills) + 1;

                net_bill_amount = net_bill_amount + Number(Amount);

            }

        });



        $("TotalChecks").html("").html(selected_bills);

        $("TotalCosts").html("").html("$"+net_bill_amount);

        $("VoidButton").html("").html("("+selected_bills+")");

        $("UnclearButton").html("").html("("+selected_bills+")");

    }



    let unselect_all = () => {

        bills = [];

        bill_amount = [];

        entities = [];

        payments_ids = [];

        selected_bills = 0;

        net_bill_amount = 0;



        $("input:checkbox[name=InvoiceID]").each(function () {

            $(this).removeAttr("checked");



            $("#select_unselect").attr("onclick","select_all()");

            $("#select_unselect").html("Select All");



        });



        $("TotalChecks").html("").html(selected_bills);

        $("TotalCosts").html("").html("$"+net_bill_amount);

        $("VoidButton").html("").html("("+selected_bills+")");

        $("UnclearButton").html("").html("("+selected_bills+")");

    }



    let sort_check_txn = "DESC";

    let sort_by_check_txn = () => {



        $("body").nimbleLoader('show');



        console.log("Starting sorting!");

        sorting("invoice-data-table",sort_check_txn, 6);



        if(sort_check_txn == "DESC"){

            sort_check_txn = "ASC";

        } else {

            sort_check_txn = "DESC";

        }



        console.log("Sorting Completed!");

        $("body").nimbleLoader('hide');

    }



    let sorting = (id,sort_check_txn,row_number) => {

        var table, rows, switching, i, x, y, shouldSwitch;

        table = document.getElementById(id);

        switching = true;

        while (switching) {

            switching = false;

            rows = table.rows;

            for (i = 1; i < (rows.length - 1); i++) {

                shouldSwitch = false;

                x = rows[i].getElementsByTagName("TD")[row_number];

                y = rows[i + 1].getElementsByTagName("TD")[row_number];

                if(sort_check_txn == "DESC"){

                    if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {

                        //if so, mark as a switch and break the loop:

                        shouldSwitch = true;

                        break;

                    }

                } else {

                    if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {

                        //if so, mark as a switch and break the loop:

                        shouldSwitch = true;

                        break;

                    }

                }

            }

            if (shouldSwitch) {

                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);

                switching = true;

            }

        }

    }

</script>