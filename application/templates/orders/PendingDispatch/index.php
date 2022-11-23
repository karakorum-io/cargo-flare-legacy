<style>
    .form-box-combobox-m{
        width:285px;
        height:30px;
        background:none;
    }
</style>

<div id="pendingDispatchModal" style="display:none;">
    <h3>Carrier Details</h3>
    <form id="FormPendingDispatch">
        <table cellspacing="5" cellpadding="10" border="0">
            <tr>
                <td valign="top">
                    <input type="text" id="pdName" placeholder="Name" class="form-box-combobox form-box-combobox-m" name="name"/>
                </td>
                <td valign="top">
                    <input type="text" id="pdContact" placeholder="Contact" class="form-box-combobox form-box-combobox-m" name="contact"/>
                </td>
            </tr>
            <tr>
                <td valign="top">
                    <input type="text" id="pdPhone" placeholder="Phone" class="phone elementname form-box-combobox form-box-combobox-m" name="phone"/>
                </td>
                <td valign="top">
                    <input type="text" id="pdEmail" placeholder="Email" class="form-box-combobox form-box-combobox-m" name="email"/>
                </td>
            </tr>
            <tr>
                <td colspan="2" valign="top">
                    <select id="auto-comments" class="form-box-combobox form-box-combobox-m" onchange="setMessage()" style="width:100%;">
                        <option value="0">Quick Comments</option>
                        <option value="Waiting on Certification of Insurance">Waiting on Certification of Insurance</option>
                        <option value="Waiting on pick up information">Waiting on pick up information</option>
                        <option value="Waiting on drop off information">Waiting on drop off information</option>
                        <option value="Waiting on carrier to call back to see if they can do the run">Waiting on carrier to call back to see if they can do the run</option>
                    </select>
                </td>
            </tr>
        </table>
    </form>
    <br/>
    <h3>Comment</h3>
    <table cellspacing="2" cellpadding="0" border="0">
        <tr>
            <td valign="top">
                <textarea id="pdComment" name="ckeditor"></textarea>
            </td>
        </tr>
    </table>
</div>

<script>

    let setMessage = () => {
        let comment = $("#auto-comments").val();

        if(comment == 0){
            $("#pdComment").val("");
        } else {
            $("#pdComment").val(comment);
        }

        $("#pdComment").ckeditor();
    }

    let pendingDispatch = () => {
        
        refreshPendingDispatch();

        $("#pendingDispatchModal").dialog({
				modal: true,
				width: 600,
				title: "Mark Pending Dispatch",
				resizable: false,
				draggable: false,
				buttons: {
                    "Proceed": function () {
                        markPendingDispatch();
                    },
                    "Cancel": function () {
                        closePendingDispatch();
                    }
				}
			});
    }

    let closePendingDispatch = () => {
        refreshPendingDispatch();
        $("#pendingDispatchModal").dialog('close');
    }

    let refreshPendingDispatch = () => {
        let name = $("#pdName").val("");
        let contact = $("#pdContact").val("");
        let phone = $("#pdPhone").val("");
        let email = $("#pdEmail").val("");
        let comment = $("#pdComment").val("");
        $("#pdComment").ckeditor();
        $("#pendingDispatchModal").nimbleLoader('hide');
    }

    let markPendingDispatch = () => {
        let name = $("#pdName").val();
        let contact = $("#pdContact").val();
        let phone = $("#pdPhone").val();
        let email = $("#pdEmail").val();
        let comment = $("#pdComment").val();
        
        var entity_id = $(".order-checkbox:checked").val();

        if(comment == ""){
            alert("Pending Comment/ Reason cannot be left blank.");
            return false;
        }

        $("#pendingDispatchModal").nimbleLoader('show');

        $.ajax({
            type: 'POST',
            url: BASE_PATH+'application/ajax/entities.php',
            dataType: 'json',
            data: {
                action: 'MARK_PENDING_DISPATCH',
                id: <?php echo $_GET['id']?>,
                name: name,
                contact: contact,
                email: email,
                phone: phone,
                comment: comment
            },
            success: function(response) {
                if(!response.success){
                    alert(response.message);
                }
                closePendingDispatch();
                location.reload();
            },
            error: function(response) {
                alert("Try again later");
            },
            complete: function(response) {
                $("#pendingDispatchModal").nimbleLoader('hide');
            }
        });
    }

    let removePendingDispatch = () => {
        
        if(!confirm('Remove from pending dispatch')){
            return false;
        }

        $.ajax({
            type: 'POST',
            url: BASE_PATH+'application/ajax/entities.php',
            dataType: 'json',
            data: {
                action: 'REMOVE_PENDING_DISPATCH',
                id: <?php echo $_GET['id']?>
            },
            success: function(response) {
                if(!response.success){
                    alert("Try again later");
                } else {
                    location.reload();
                }
            },
            error: function(response) {
                alert("Try again later");
            },
            complete: function(response) {
                //
            }
        });
    }
</script>