<!--Cancellation Popup UI Start-->
<CancellationPopup>


<!--Content Start-->
<content>

<!-- Modal -->
<div class="modal fade" id="cancellation-popup-pane" tabindex="-1" role="dialog" aria-labelledby="cancellation-popup-pane_model" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="cancellation-popup-pane_mode">Cancel This Task!?</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i class="fa fa-times" aria-hidden="true"></i>
				</button>
			</div>
			<div class="modal-body">
				<h2 class="details" style=" color: #19577a;
}">Why you want to cancel this order?</h2>
		<textarea class="form-box-textarea" id="cancel_reason" style="height: 200px"></textarea>
			</div>
			<div class="modal-footer">
				<button type="button" id="status" value="" class="btn btn_dark_green btn-sm" onclick="CancelThisOrder_progress(this.value)" >Proceed</button>
			</div>
		</div>
	</div>
</div>

<!-- end:: Content -->

</content>
<!--Content Ended-->

<!--Script Start-->



<script>




// function to cancel this order
function CancelThisOrder(status)
 {
 	
    $("#cancellation-popup-pane").modal();
    $('#status').val(status);

}


function  CancelThisOrder_progress(status)
{        
	            var cancel_reason = document.getElementById("cancel_reason").value;
				if( cancel_reason == "" ){
					swal.fire("Please provide valid reason!");
				} else {
					$.ajax({
						type: "POST",
						url: "<?=SITE_IN?>application/ajax/entities.php",
						dataType: "json",
						data: {
							action: 'CancelThisOrder',
							entity_id: <?php echo $this->entity->id; ?>,
							status: status,
							cancel_reason: cancel_reason
						},
						success: function (result) {
							console.log(result);
							if (result.success == true) {
								swal.fire("Order Cancelled!");
								window.location.reload();
							} else {
								swal.fire("Order action failed. Try again later, please.");
							}
						},
						error: function (result) {
							swal.fire("Order action failed. Try again later, please.");
						}
					});
				}

}
</script>
<!--Script Ended-->

</CancellationPopup>
<!--Cancellation Popup UI Ended-->