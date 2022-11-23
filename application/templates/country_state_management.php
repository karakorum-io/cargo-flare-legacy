<script>
    $(document).ready(()=>{
        // default selected
        $("#shipper_country").val("US");
        $("#origin_country").val("US");
        $("#destination_country").val("US");
        $("#carrier_country").val("US");
        $("#pickup_country").val("US");
        $("#deliver_country").val("US");
        $("#e_cc_country").val("US");

        $("#shipper_country").change(()=>{
            handleStateField("shipper",$("#shipper_country").val());
        });

        $("#origin_country").change(()=>{
            handleStateField("origin",$("#origin_country").val());
        });

        $("#destination_country").change(()=>{
            handleStateField("destination",$("#destination_country").val());
        });

        $("#carrier_country").change(()=>{
            handleStateField("carrier",$("#carrier_country").val());
        });

        $("#pickup_country").change(()=>{
            handleStateField("pickup",$("#pickup_country").val());
        });

        $("#deliver_country").change(()=>{
            handleStateField("deliver",$("#deliver_country").val());
        });

        $("#e_cc_country").change(()=>{
            handleStateField("e_cc",$("#e_cc_country").val());
        });

        let handleStateField = (target,country) => {
            console.log("Changing : " + target + "_state");
            
            Processing_show();
            $.ajax({
                type: "POST",
                url: BASE_PATH + "application/ajax/entities.php",
                dataType: "json",
                data: {
                    action: "ENTITY_STATE",
                    country:country,				
                    target:target
                },
                success: function (res) {
                    $("#"+target+"_state_div").html(res);
                },
                complete: function (res) {
                    KTApp.unblockPage();
                }
            });
        }
    });
</script>