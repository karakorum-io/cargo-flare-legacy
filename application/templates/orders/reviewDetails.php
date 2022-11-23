<!---
/***************************************************************************************************
* Transportation Management Software
*
* Client:           FreightDragon
* Version:          1.0
* Start Date:                   2017-03-06
* Author:           Chetu Inc
*
* CopyRight 2017                FreightDragon. - All Rights Reserved
****************************************************************************************************/
--->

<style>
h3.details
{
    padding: 22px 0 0;
    width: 100%;
    font-size: 20px;
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.11.4/ckeditor.js"></script>


<!--begin::Modal-->
<div class="modal fade" id="maildivnew" tabindex="-1" role="dialog" aria-labelledby="maildivnew_model" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="maildivnew_model">Email message</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <div class="modal-body">
			
				<div style="float: left;">
					<ul style="margin-top: 26px;">
						<li style="margin-bottom: 14px;">Form Type <input value="1" id="attachPdf" name="attachTpe" type="radio"/><label for="attachPdf" style="margin-right: 2px; cursor:pointer;"> PDF</label><input value="0" id="attachHtml"  name="attachTpe" type="radio"/><label for="attachHtml" style="cursor:pointer"> HTML</label></li>
						<li style="margin-bottom: 11px;">Attachment(s): <span style="color:#24709F;" id="mail_att_new"></span></li>
					</ul>
				</div>
				<div style="text-align: right;">
					<div style="text-align: right;">
						<img src="/images/icons/add.gif"> <span style="margin-bottom: 3px;cursor:pointer; position: relative;bottom:4px; color:#24709F;" class="add_one_more_field_" >Add a Field</span>
						<ul>
							<li id="extraEmailsingle" style="margin-bottom: 6px;"><span>Email:<span style="color:red">*</span></span> <input type="text" id="mail_to_new" name="mail_to_new" class="form-box-combobox" ></li>
							<li style="margin-bottom: 6px;margin-top: 6px;margin-left: 292px; position:relative; display: none;" id="mailexttra"><input name="optionemailextra" class="form-box-combobox optionemailextra" type="text"><a href="#" style="position: absolute;margin-left: 2px;margin-top: 8px;" class="remove_2sd_field"><img id="singletop" style="width: 12px;height: 12px;" src="/images/icons/delete.png"></a></li>
							<li style="margin-bottom: 6px;"><span style="margin-right: 18px;">CC:</span> <input type="text" id="mail_cc_new" name="mail_cc_new" class="form-box-combobox" ></li>
							<li style="margin-bottom: 12px;"><span style="margin-right: 9px;">BCC:</span> <input type="text" id="mail_bcc_new" name="mail_bcc_new" class="form-box-combobox" ></li>
						</ul>
					</div>
					<div class="edit-mail-content" style="margin-bottom: 8px;">
						<div class="edit-mail-row" style="margin-bottom: 8px;">
							
							<div class="form-group" >
								<label class="edit-mail-label" >Subject:<span>*</span></label>
								<input type="text" id="mail_subject_new" class="form-box-textfield" maxlength="255" name="mail_subject_new" ></div>
						</div>
						<div class="edit-mail-row mail_body_section" style="">
							<div class="form-group" >
								<label class="">Body:<span>*</span></label>
								<textarea class="form-box-textfield form-control" name="mail_body_new" id="mail_body_new" style="height: 200px"></textarea></div>
						</div>
					</div>
					<input type="hidden" name="form_id" id="form_id"  value=""/>
					<input type="hidden" name="entity_id" id="entity_id"  value=""/>
					<input type="hidden" name="skillCount" id="skillCount" value="1">
				</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark btn-sm" data-dismiss="modal">Cancal</button>
                <button type="button" class="btn btn-primary btn-sm" onclick="maildivnew_send();">Submit</button>
            </div>
        </div>
    </div>
</div>




<script type="text/javascript">
    $('.add_one_more_field_').on('click', function () {
        $('#mailexttra').css('display', 'block');
        return false;
    });
    $('#singletop').on('click', function () {
        $('#mailexttra').css('display', 'none');
        $('.optionemailextra').val('');
    });
</script>

<?php
$mobileDevice = 0;
$mobileDevice = detectMobileDevice();
?>
<script type="text/javascript" src="/jscripts/jquery.rateyo.js"></script>
<link rel="stylesheet" href="/styles/jquery.rateyo.min.css"/>
<?php
if (is_array($_SESSION['searchData']) && $_SESSION['searchCount'] > 0) {
    $eid = $_GET['id'];
    $indexSearchData = array_search($eid, $_SESSION['searchData']);
    $nextSearch = $indexSearchData + 1;
    $_SESSION['searchShowCount'] = $indexSearchData;
    $prevSearch = $indexSearchData - 1;
    $entityPrev = $_SESSION['searchData'][$prevSearch];
    $entityNext = $_SESSION['searchData'][$nextSearch];
    ?>
<?php } ?>
<div>
    <?php include('order_menu.php'); ?>
</div>
<br>

	<div style="margin-top:-45px;margin-bottom:15px;">
		<h3 class="details">Order #<?= $this->entity->getNumber() ?></h3>
	</div>

	<h5 class="kt-widget14__title mb-4 mt-2">No Review</h5>
	
	<?php
		$assigned = $this->entity->getAssigned();
		$shipper = $this->entity->getShipper();
		$origin = $this->entity->getOrigin();
		$destination = $this->entity->getDestination();
		$vehicles = $this->entity->getVehicles();
	?>
	
	<div class="row">
	
		<?php //=====  Pickup Information  ===== ?>
		<div class="col-md-6 col-sm-12">
			<?php
			$phone1_ext = '';
			$phone2_ext = '';
			$phone3_ext = '';
			$phone4_ext = '';
			if ($origin->phone1_ext != '')
			$phone1_ext = " <b>X</b> " . $origin->phone1_ext;
			if ($origin->phone2_ext != '')
			$phone2_ext = " <b>X</b> " . $origin->phone2_ext;
			if ($origin->phone3_ext != '')
			$phone3_ext = " <b>X</b> " . $origin->phone3_ext;
			if ($origin->phone4_ext != '')
			$phone4_ext = " <b>X</b> " . $origin->phone4_ext;
			?>
			<div style="background:#fff;border:1px solid #ebedf2;">
				
				<h3 class="shipper_detail hide_show">Pickup Information</h3>
				
				<div style="padding-left:20px;padding-right:20px;">
					<div class="row">
					
						<div class="col-3">
							<ul class="kt-font-boldest">
								<li>Address</li>
								<li>City</li>
								<li>Location Type</li>
								<li>Hours</li>
							</ul>
						</div>

						<div class="col-1">
							<ul class="kt-font-boldest">
								<li>:</li>
								<li>:</li>
								<li>:</li>
								<li>:</li>
							</ul>
						</div>

						<div class="col-8">
							<ul>
								<li><?= $origin->address1; ?>,&nbsp;&nbsp;<?= $origin->address2; ?></li>
								<li><span class="like-link" onclick="window.open('<?= $origin->getLink() ?>')"><?= $origin->getFormatted() ?></span></li>
								<li><?= $origin->location_type; ?></li>
								<li><?= $origin->hours; ?></li>
							</ul>
						</div>
						
					</div>
				</div>	
				
			</div>
		</div>
		<?php //=====  END Pickup Information  ===== ?>

		<?php //=====  Drop off Information  ===== ?>
		<div class="col-md-6 col-sm-12">
			<div style="background:#fff;border:1px solid #ebedf2;">

				<?php
				$phone1_ext = '';
				$phone2_ext = '';
				$phone3_ext = '';
				$phone4_ext = '';
				if ($destination->phone1_ext != '')
				$phone1_ext = " <b>X</b> " . $destination->phone1_ext;
				if ($destination->phone2_ext != '')
				$phone2_ext = " <b>X</b> " . $destination->phone2_ext;
				if ($destination->phone3_ext != '')
				$phone3_ext = " <b>X</b> " . $destination->phone3_ext;
				if ($destination->phone4_ext != '')
				$phone4_ext = " <b>X</b> " . $destination->phone4_ext;
				?>
				
				<h3 class="shipper_detail hide_show">Drop off Information </h3>

				<div id="Drop_off" style="padding-left:20px;padding-right:20px;">
					<div class="row">
						<div class="col-3">
							<ul class="kt-font-boldest">
								<li>Address</li>
								<li>City</li>
								<li>Location Type</li>
								<li>Hours</li>
							</ul>
						</div>

						<div class="col-1">
							<ul class="kt-font-boldest">
								<li>:</li>
								<li>:</li>
								<li>:</li>
								<li>:</li>
							</ul>
						</div>
						
						<div class="col-8">
							<ul>
								<li><?= $destination->address1; ?>,&nbsp;&nbsp;<?= $destination->address2; ?></li>
								<li><span class="like-link" onclick="window.open('<?= $destination->getLink() ?>')"><?= $destination->getFormatted() ?></li>
								<li><?= $destination->location_type; ?></li>
								<li><?= $destination->hours; ?></li>
							</ul>
						</div>
						
					</div>
				</div>
			</div>
		</div>
	</div>


    <div class="kt-portlet__body">
        <div class="row">
			<div class="col-md-12 col-sm-12">
				<div class="card">
					<div class="hide_show">
						<h3 class="shipper_detail">Vehicle(s) Information</h3>
					</div>
					<div id="Vehicle">
						<div class="card-body">						
							<table class="table table-bordered">
								<thead>
									<tr>
										<th>S.No.</th>
										<th><?= Year ?></th>
										<th><?= Make ?></th>
										<th><?= Model ?></th>
										<th><?= Inop ?></th>
										<th><?= Type ?></th> 
										<th><?= Vin# ?></th>
										<th><?= Lot# ?></th>                            
									</tr>
								</thead>
								<tbody>
								<?php
								$vehiclecounter = 0;
								foreach ($vehicles as $vehicle) :
								$vehiclecounter = $vehiclecounter + 1;
								?>
								<tr>
									<td><?= $vehiclecounter ?></td>
									<td><?= $vehicle->year ?></td>
									<td><?= $vehicle->make ?></td>
									<td><?= $vehicle->model ?></td> 
									<td><?php print $vehicle->inop == 0 ? "No" : "Yes"; ?></td>
									<td><?= $vehicle->type ?></td>
									<td><?php print $vehicle->vin ?></td>
									<td><?php print $vehicle->lot ?></td>                                
								</tr>
								<?php endforeach; ?>
								</tbody>
							</table>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


	<div class="kt-portlet__body">
		<div class="row">
		
			<div class="col-lg-6 col-sm-12">
				<div class="card-header" id="headingTwo">
					<div class="card-title collapsed" data-toggle="collapse" data-target="#Overall">
						Overall Experience with Broker
					</div>
				</div>
				<div class="card">
					<div id="Overall" class="collapse"  style="">
						<div class="card-body">
							<h4><b>Rated at:</b></h4>
							<b>Comment:</b><br>
							<textarea  id="comment" class="form-control" readonly></textarea>
						</div>
					</div>
				</div> 
			</div>

			<div class="col-lg-6 col-sm-4">
				<div class="form-group">
					<div class="card" >
						<div class="card-header" id="headingTwo">
							<div class="card-title collapsed" data-toggle="collapse" data-target="#Product_Updates">
								Product Updates
							</div>
						</div>

						<div id="Product_Updates" class="collapse"  style="">
							<div class="card-body">
								<h3>Rated at:</h3>
								<b>Comment:</b><br>
								<textarea  id="car_comment" class="form-control" readonly></textarea>
							</div>
						</div>
					</div> 
				</div>
			</div>

		</div>
	</div>



<script>
    /*Rating star values setting*/

    $(document).ready(function () {
        var entity_id = '<?php echo $this->entity->id; ?>';
        $.ajax({
            type: "POST",
            url: "<?php echo SITE_IN; ?>review/ajax/entities.php",
            dataType: "json",
            data: {
                action: 'getEntityData',
                entity_id: entity_id
            },
            success: function (res) {

                if (res.success == 'FALSE') {
                    $("#message").html('<div style="background:#F2F2F2;width:97%;padding:10px;border-radius:5px; border:1px solid #cccccc;"><center><h2 style="color:red;">Customer has not given feedback yet!</h2></center></div>');
                } else {                    
                    $(function () {
                        $("#rateyo").rateYo({rating: res.data['ratings'], ratedFill: "#419111", readOnly: true})
                    });

                    $(function () {
                        $("#rateyo2").rateYo({rating: res.data['car_rating'], ratedFill: "#419111", readOnly: true});
                    });
                    
                    $("#comment").val(res.data['comment']);
                    $("#car_comment").val(res.data['car_comment']);
                    $("#ratedAt").html(res.data['created_at']);
                    $("#reviewHeading").html("Reviewed on "+res.createdAt);
                }
            }
        });
    });
</script>   

<script type="text/javascript">
    function reassign() {

        var Assign_val =$("#Assign_id").val();
         if(Assign_val != '')
         {
             
            var assign_id = $("#reassign_dialog select").val();

            $.ajax({
                type: "POST",
                url: "<?=SITE_IN?>application/ajax/entities.php",
                dataType: "json",
                data: {
                    action: 'reassign',
                    entity_id: <?=$this->entity->id?>,
                    assign_id: assign_id
                },
                success: function (result) {
                    if (result.success == true) {
                        window.location.reload();
                    } else {

                         $('#reassign_dialog').find(".error").html("Can't reassign order. Try again later, please.").css('display','block');
                          $("#reassign_dialog div.error").slideDown().delay(2000).slideUp();
                    }
                },
                error: function (result) {
                    /*$("#reassign_dialog div.error").html("<p>Can't reassign order. Try again later, please.</p>");*/
                     $('#reassign_dialog').find(".error").html("Can't reassign order. Try again later, please.").css('display','block');

                    $("#reassign_dialog div.error").slideDown().delay(2000).slideUp();
                }
            })


         }else{
       
        $("#reassign_dialog").modal();
        var Assign = 'Assign';
        $("#Assign_id").val(Assign);

         }

         $("#Close").click(function(){
            $("#Assign_id").val('');
         })

    }
</script>

<script type="text/javascript">
    function validateEmail(sEmail) {
        var res = "", res1 = "", i;
        var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
        for (i = 0; i < sEmail.length; i++) {
            if (filter.test(sEmail[i])) {
                res += sEmail[i];
            } else {
                res1 += sEmail[i];
            }
        }
        if (res1 !== '') {
            return false;
        }
    }
</script>