<!DOCTYPE html>
<html lang="en">
    <head>
        <title>CargoFlare :: Convert to Order</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="https://cargoflare.com/styles/revolution/style.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="content">
            <div class="row header">
                <img alt="Cargo Flare" src="https://cargoflare.com/styles/cargo_flare/logo.png" width="230">
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-7 col-md-7 col-sm-6 col-xs-12">
                        <div class="box">
                            <h3>Quote #<?php echo $this->entity->number?></h3>
                            <p class="quote-id">Lets get this done!</p>
                            <div class="error" onclick="editDetailsScreen()">
                                Select new <b style="cursor: pointer" id="fsd-scroll-to-btn">First Available Pick-up Date</b> because your current one is in the past.
                            </div>
                            <div class="success">
                                <b>$<b style="font-size:20px;">0</b> upfront payment required to book your shipment!</b>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 discounted-div">
                                    <div class="plans">
                                        <?php 
                                            $amount = $this->entity->total_tariff_stored - $this->entity->carrier_pay_stored;
                                        ?>
                                        <input type="radio" name="price" value="<?php echo $amount; ?>" id="discounted-radio" onclick="setPriceType('discounted')"/>
                                        <label class="amount">$<amount><?php echo $amount; ?></amount></label>
                                        <p class="price-label">DISCOUNTED PRICE</p>
                                        <div class="inner-div">
                                            <div class="method">
                                                PAYMENT METHOD &nbsp;&nbsp;&nbsp;
                                                <i class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip" title="Some information over here!"></i>
                                            </div>
                                            <p class="price-icons"><i class="fa fa-credit-card"></i> + <i class="fa fa-usd" aria-hidden="true"></i></p>
                                            <p class="price-icons-labels"><b>Card and Cash</b></p>
                                        </div>
                                        <div class="last-wrapper">
                                            <p><i class="fa fa-check" aria-hidden="true"></i> Free Cancellation</p>
                                            <p><i class="fa fa-check" aria-hidden="true"></i> Full Insurance Coverage</p>
                                            <p><i class="fa fa-check" aria-hidden="true"></i> Door to Door Transport</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 regular-div">
                                    <div class="plans">
                                        <?php 
                                            $cod = $this->entity->total_tariff_stored - $this->entity->carrier_pay_stored;
                                            $amount = $cod + $cod * (4/100); 
                                        ?>
                                        <input type="radio" name="price" value="<?php echo $amount; ?>" id="regular-radio" onclick="setPriceType('regular')"/>
                                        <label class="amount">
                                            $<amount><?php echo $amount; ?></amount>
                                        </label>
                                        <p class="price-label">REGULAR PRICE</p>
                                        <div class="inner-div">
                                            <div class="method">
                                                PAYMENT METHOD &nbsp;&nbsp;&nbsp;<i class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip" title="Some information over here!"></i>
                                            </div>
                                            <p class="price-icons"><i class="fa fa-credit-card"></i></p>
                                            <p class="price-icons-labels"><b>Cash only</b></p>
                                        </div>
                                        <div class="last-wrapper">
                                            <p><i class="fa fa-check" aria-hidden="true"></i> Free Cancellation</p>
                                            <p><i class="fa fa-check" aria-hidden="true"></i> Full Insurance Coverage</p>
                                            <p><i class="fa fa-check" aria-hidden="true"></i> Door to Door Transport</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12" id="edit-form" style="display:none;">
                        <div class="widget">
                            <h3><i class="fa fa-window-close text-danger" onclick="openPreview()"></i> Edit Shipping Details</h3>
                            <div class="row">
                                <div class="form-group">
                                    <label>FIRST AVAILABLE PICK-UP DATE</label>
                                    <input type="date" id="first_avail" class="form-control" value="<?php echo $this->entity->est_ship_date; ?>"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label>PICKUP CITY</label>
                                    <input type="text" id="from_city" class="form-control" value="<?php echo $this->origin->city; ?>" disabled/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label>PICKUP STATE</label>
                                    <input type="text" id="from_state" class="form-control" value="<?php echo $this->origin->state; ?>" disabled/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label>PICKUP ZIPCODE</label>
                                    <input type="text" id="from_zipcode" class="form-control" value="<?php echo $this->origin->zip; ?>" disabled/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label>DELIVERY CITY</label>
                                    <input type="text" id="to_city" class="form-control" value="<?php echo $this->dest->city; ?>" disabled/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label>DELIVERY STATE</label>
                                    <input type="text" id="to_state" class="form-control" value="<?php echo $this->dest->state; ?>" disabled/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label>DELIVERY ZIPCODE</label>
                                    <input type="text" id="to_zipcode" class="form-control" value="<?php echo $this->dest->zip; ?>" disabled/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group text-center">
                                    <button onclick="save()" class="btn btn-primary btn-lg">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12" id="preview-form">
                        <div class="widget">
                            <h3><i class="fa fa-pencil-square text-primary" onclick="editDetailsScreen()"></i> Shipping Details</h3>
                            <div class="summary-block">
                                <div class="summary-content">
                                    <div class="summary-head">
                                        <p class="red">FIRST AVAILABLE PICK-UP DATE <i class="gray-info-icon tippy" id="quote-info-fad-icon" data-tooltipped="" aria-describedby="tippy-tooltip-2" data-original-title="First Available Pick-up Date is the day that your vehicle is available for transport. Our standard pick-up window is between 1-4 business days starting from the First Available Pick-up Date."></i></p>
                                    </div>
                                    <div class="summary-price">
                                        <span class="red"><?php echo $this->entity->est_ship_date ? $this->entity->est_ship_date : "N/A"; ?></span>
                                    </div>
                                </div>
                                <div class="summary-content">
                                    <div class="summary-head">
                                        <p >TRANSPORT METHOD <i class="gray-info-icon tippy" id="quote-info-fad-icon" data-tooltipped="" aria-describedby="tippy-tooltip-2" data-original-title="First Available Pick-up Date is the day that your vehicle is available for transport. Our standard pick-up window is between 1-4 business days starting from the First Available Pick-up Date."></i></p>
                                    </div>
                                    <div class="summary-price">
                                        <span>
                                            <?php
                                                if($this->entity->ship_via == 1){
                                                    echo "Open";
                                                } elseif($this->entity->ship_via == 2) {
                                                    echo "Enclosed";
                                                } else {
                                                    echo "Driveaway";
                                                }
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="summary-content">
                                    <div class="summary-head">
                                        <p >PICKUP FROM <i class="gray-info-icon tippy" id="quote-info-fad-icon" data-tooltipped="" aria-describedby="tippy-tooltip-2" data-original-title="First Available Pick-up Date is the day that your vehicle is available for transport. Our standard pick-up window is between 1-4 business days starting from the First Available Pick-up Date."></i></p>
                                    </div>
                                    <div class="summary-price">
                                        <span><?php echo $this->origin->city?>, <?php echo $this->origin->state?> (<?php echo $this->origin->zip?>)</span>
                                    </div>
                                </div>
                                <div class="summary-content">
                                    <div class="summary-head">
                                        <p >DEILVER TO <i class="gray-info-icon tippy" id="quote-info-fad-icon" data-tooltipped="" aria-describedby="tippy-tooltip-2" data-original-title="First Available Pick-up Date is the day that your vehicle is available for transport. Our standard pick-up window is between 1-4 business days starting from the First Available Pick-up Date."></i></p>
                                    </div>
                                    <div class="summary-price">
                                        <span ><span><?php echo $this->dest->city?>, <?php echo $this->dest->state?> (<?php echo $this->dest->zip?>)</span></span>
                                    </div>
                                </div>
                                <div class="summary-content">
                                    <div class="summary-head">
                                        <p >DISTANCE <i class="gray-info-icon tippy" id="quote-info-fad-icon" data-tooltipped="" aria-describedby="tippy-tooltip-2" data-original-title="First Available Pick-up Date is the day that your vehicle is available for transport. Our standard pick-up window is between 1-4 business days starting from the First Available Pick-up Date."></i></p>
                                    </div>
                                    <div class="summary-price">
                                        <span ><?php echo $this->entity->distance?> Miles</span>
                                    </div>
                                </div>
                                <div class="summary-content">
                                    <div class="summary-head">
                                        <p >TIME TO DELIVER <i class="gray-info-icon tippy" id="quote-info-fad-icon" data-tooltipped="" aria-describedby="tippy-tooltip-2" data-original-title="First Available Pick-up Date is the day that your vehicle is available for transport. Our standard pick-up window is between 1-4 business days starting from the First Available Pick-up Date."></i></p>
                                    </div>
                                    <div class="summary-price">
                                        <span >4-6 Calender Days</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="widget">
                            <h3 >Vehicle Details</h3>
                            <div class="summary-block">
                                <div class="summary-content">
                                    <div class="summary-head">
                                        <p >VEHICLE <i class="gray-info-icon tippy" id="quote-info-fad-icon" data-tooltipped="" aria-describedby="tippy-tooltip-2" data-original-title="First Available Pick-up Date is the day that your vehicle is available for transport. Our standard pick-up window is between 1-4 business days starting from the First Available Pick-up Date."></i></p>
                                    </div>
                                    <div class="summary-price">
                                        <span>
                                        <?php
                                            foreach ($this->vehicles as $key => $vehicle) {
                                                echo $vehicle->year.", ".$vehicle->make.", ".$vehicle->model."<br/>";
                                            }
                                        ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="widget">
                            <div class="col-sm-4">
                                <h3 >Pay Now</h3>
                            </div>
                            <div class="col-sm-8">
                                <div class="summary-block">
                                    <div class="summary-content">
                                        <div class="summary-head" style="text-align:right; display:block;">
                                            <span class="green" style="font-size:45px;">$0</span><br>
                                            <span class="green">No upfront payment is required to book your shipment!</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-7 col-sm-6 col-xs-12">
                        <div class="text-center">
                            <a href="javascript:void(0);" onclick="convertToOrder()" class="btn btn-primary btn-lg mb30">NO COST BOOKING</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content instructions">
            <p class="inst">… or book via: <span>(864) 546-5038</span></p>
            <p class="inst-secondary">You can change your First Available Pick-up Date at any time. <i class="fa fa-info-circle" data-toggle="tooltip" title="Restrictions may apply!"></i></p>
        </div>
        <div class="content footer">
            <div class="row">
                <p><span><i class="fa fa-hashtag" aria-hidden="true"></i>34,816</span><br/>Total satisfied customers!</p>
            </div>
        </div>
    </body>
    <script>
        const editDetailsScreen = () => {
            $("#edit-form").show();
            $("#preview-form").hide();
        }
        const openPreview =() => {
            $("#preview-form").show();
            $("#edit-form").hide();
        }
        const save = () => {
            if($("#first_avail").val() == ""){
                $(".error").show();

                setTimeout(()=>{
                    $(".error").hide();
                },2000)
            } else {
                $(".summary-price span.red").html($("#first_avail").val());
            }

            openPreview();
        }

        const setPriceType = (priceType) => {
            if(priceType == 'discounted'){
                $(".regular-div .plans").removeClass('div-active')
                $(".discounted-div .plans").addClass('div-active')
            } else {
                $(".discounted-div .plans").removeClass('div-active')
                $(".regular-div .plans").addClass('div-active')
            }
        }

        const convertToOrder = () => {
            if($("#first_avail").val() == ""){
                $(".error").show();

                setTimeout(()=>{
                    $(".error").hide();
                },2000)
            } else {
                let price = $("input[name=price]:checked").val();

                $.ajax({
                    type: "POST",
                    url: "https://cargoflare.com/quote/make_order",
                    dataType: "json",
                    data: {
                        entity_id: <?php echo $this->entity->id?>,
                        price: price,
                        avail_pickup_date: $("#first_avail").val()
                    },
                    success: function (result) {
                        if(result.success){
                            location.href = "https://cargoflare.com/quote/convert_to_order_thanks";
                        } else {
                            alert(result.message);    
                        }
                    },
                    error: function (result) {
                        alert("Something went wrong cannot convert to order!");
                    }
                });
            }
        }

        $(document).ready(()=>{
            $("#discounted-radio").trigger('click');
            $(".error").hide();
        });
    </script>
</html>