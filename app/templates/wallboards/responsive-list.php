<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<style>
    @font-face{
        font-family:'digital-clock-font';
        src: url('../../../font/digital.ttf');
    }
    .centering{
        width:auto;
        margin: auto;
    }

    .card{
        margin-bottom:30px;
        background-color: #F8F8F8;
    }

    .card p{
        color:#666;
    }

    .card button{
        font-size: 30px;
    }

    .card .comment{
        font-size: 14px;
        height:50px;
        color:#000;
        font-weight: bolder;
        /* overflow-y: auto; */
    }

    /* .comment::-webkit-scrollbar {
        width: 5px;
    }
    
    .comment::-webkit-scrollbar-track {
        box-shadow: inset 0 0 2px #369;
    }
    
    .comment::-webkit-scrollbar-thumb {
        background-color: #369;
        outline: 1px solid #369;
    } */

    .carrier-info div{
        font-weight: bolder;
        margin: 0px;
        line-height: 0px;
        height: 17px;
        margin-top: -19px;
        padding-top: 6px;
        overflow: hidden;
    }
</style>
<div class="container-fluid">
    <h3 class="text-center" style="margin-top:20px; margin-bottom: 20px;">Pending Dispatches</h3>
</div>
<div class="container-fluid" style="padding-left:8%; padding-right: 8%;">
  <div class="row" id="dispatches">
    <?php
        foreach ($this->daffny->data as $k => $v) {
    ?>
    <div class="col-sm-3">
        <div class="card">
            <div class="card-body">
                <div>
                    <h3><?php echo $v['order_id']; ?></h3>
                    <strong><?php echo $v['creator_name']; ?></strong>
                </div>
                <br/>
                <div class="text-center carrier-info">
                    <div>
                        <?php echo ucwords($v['carrier_name'] == "" ? "Name: Not Available" : $v['carrier_name']); ?>
                    </div><br/>
                    <div>
                        <?php echo $v['carrier_email']  == "" ? "Email: Not Available" : $v['carrier_email']; ?>
                    </div><br>
                    <div>
                        <?php echo $v['carrier_phone'] == "" ? "Phone: Not Available" : $v['carrier_phone']; ?>
                    </div><br/>
                    <div>
                        <?php echo $v['carrier_contact'] == "" ? "Contact: Not Available" : $v['carrier_contact']; ?>
                    </div>
                </div>
                Comment:
                <br/>
                <div class="comment"><?php echo $v['comment']; ?></div>
                <button class="btn btn-primary" style="background: #000; margin-top: 10px; width: 100%; font-family:digital-clock-font;"><?php echo $v['created_at']; ?></button>
            </div>
        </div>
    </div>
    <?php
    }
    ?>
  </div>
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
                        <div class="col-sm-3">
                            <div class="card">
                                <div class="card-body">
                                    <div>
                                        <h3>`+response.data[i]['order_id']+`</h3>
                                        <strong>`+response.data[i]['creator_name']+`</strong>
                                    </div>
                                    <br/>
                                    <div class="text-center carrier-info">
                                        <div>
                                        `+ (response.data[i]['carrier_name'] == "" ? "Name: N/A" : response.data[i]['carrier_name'])+`
                                        </div><br/>
                                        <div>`+(response.data[i]['carrier_email'] == "" ? "Email: N/A" : response.data[i]['carrier_email'])+`</div><br>
                                        <div>`+(response.data[i]['carrier_phone'] == "" ? "Phone: N/A" : response.data[i]['carrier_phone'])+`</div><br>
                                        <div>`+(response.data[i]['carrier_contact'] == "" ? "Contact: N/A" : response.data[i]['carrier_contact'])+`</div>
                                    </div>
                                    Comment:
                                    <br/>
                                    <div class="comment">`+response.data[i]['comment']+`</div>
                                    <button class="btn btn-primary" style="background: #000; margin-top: 10px; width: 100%; font-family:digital-clock-font;">`+response.data[i]['created_at']+`</button>
                                </div>
                            </div>
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