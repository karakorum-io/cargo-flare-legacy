<link rel="stylesheet" href="../../../styles/wallboard.css">
<script src="../../../jscripts/wallboard.js"></script>
<style>
    .timeCreated{
        color:red;
        font-size: 14px;
        font-weight: bolder;
    }

    .card {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        max-width: 273px;
        width: 273px;
        margin: auto;
        text-align: center;
        font-family: arial;
        float:left;
        padding:20px;
        margin-right:10px;
    }

    .title {
        color: grey;
        font-size: 18px;
    }

    button {
        border: none;
        outline: 0;
        display: inline-block;
        padding: 8px;
        color: white;
        background-color: #000;
        text-align: center;
        cursor: pointer;
        width: 100%;
        font-size: 18px;
    }

    a {
        text-decoration: none;
        font-size: 22px;
        color: black;
    }

    button:hover, a:hover {
        opacity: 0.7;
    }

    .container{
        width:100%;
    }
</style>

<h3 align="center" id="wallboard-title"><?php echo "Pending Dispatches"; ?></h3>
<h4 align="center">Listing is showing pending dispatched in descending order of time</h4>
<br/>
<div class="container" id="dispatches">
    <?php
        foreach ($this->daffny->data as $k => $v) {
    ?>
    <div class="card">
        <h1><?php echo $v['order_id']; ?></h1>
        <p class="title"><?php echo $v['carrier_name']; ?></p>
        <p><?php echo $v['carrier_email']; ?></p>
        <p><?php echo $v['carrier_phone']; ?></p>
        <p><?php echo $v['comment']; ?></p>
        <div>
            <p style="text-align: left; float:left;">Contact: <?php echo $v['carrier_contact']; ?></p>
            <p style="text-align: left; float:right;">Creator: <?php echo $v['creator_name']; ?></p>
        </div>
        <p><button id="timeCreated-<?php echo $k; ?>"><?php echo $v['created_at']; ?></button></p>
    </div>
    <?php
    }
    ?>
</div>
<script>
    setInterval(function(){

        let html = "";

        $.ajax({
            type: 'POST',
            url: BASE_PATH+'wallboards/udpate_pending_dispatch',
            dataType: 'json',
            data: {
                'parent':'<?php echo $this->daffny->parent;?>'
            },
            success: function(response) {
                for(var i=0; i<response.data.length; i++){

                    if(response.data[i]['creator_name'] == null){
                        response.data[i]['creator_name'] = "";
                    }

                    html += `
                        <div class="card">
                            <h1>`+response.data[i]['order_id']+`</h1>
                            <p class="title">`+response.data[i]['carrier_name']+`</p>
                            <p>`+response.data[i]['carrier_email']+`</p>
                            <p>`+response.data[i]['carrier_phone']+`</p>
                            <p>`+response.data[i]['comment']+`</p>
                            <div>
                                <p style="text-align: left; float:left;">Contact: `+response.data[i]['carrier_contact']+`</p>
                                <p style="text-align: left; float:right;">Creator: `+response.data[i]['creator_name']+`</p>
                            </div>
                            <p><button id="timeCreated-id="timeCreated-`+response.data[i]+`">`+response.data[i]['created_at']+`</button></p>
                        </div>
                    `;
                    $("#dispatches").html(html);
                }

                if(response.data.length == 0){
                    $("#dispatches").html("<tr><td colspan='7'>No Pending Dispatches</td></tr>");
                }
            },
            error: function(response) {
                //alert("Try again later");
            },
            complete: function(response) {
                //
            }
        });
    }, 1000);
</script>

<?php
    function format_phone_us($phone)
    {
        // note: making sure we have something
        if (!isset($phone{3})) {return '';}
        // note: strip out everything but numbers
        $phone = preg_replace("/[^0-9]/", "", $phone);
        $length = strlen($phone);
        switch ($length) {
            case 7:
                return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $phone);
                break;
            case 10:
                return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $phone);
                break;
            case 11:
                return preg_replace("/([0-9]{1})([0-9]{3})([0-9]{3})([0-9]{4})/", "$1($2) $3-$4", $phone);
                break;
            default:
                return $phone;
                break;
        }
    }
?>