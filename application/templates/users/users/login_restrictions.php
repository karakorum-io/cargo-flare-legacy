<? include(TPL_PATH."users/menu_details.php"); ?>






    <div class="alert alert-light alert-elevate  ">
<div  class="row w-100">
	<form action="<?=getLink("users", "restrictions", "id", get_var("id"))?>" method="post">
    <?=formBoxStart("Login restrictions: <span class='kt-font-boldest mt-2 mt-2'>@contactname@ </span>")?>
	<div class="col-8 ml-2">
		<div  class="row">
			<div class="col-6">
				<div class="box_input">
				<span class="kt-link kt-font-boldest mt-2 mb-2"> Check "Enable login restrictions" and use the options below to limit user login times</span></br>
				@loginr_enable@
		     	</div>

		     	<div class="box_input">
                 <em class="kt-link kt-link--state kt-link--success mb-2"><strong class="kt-link kt-link--state kt-link--success">Allow</strong> login during the following days:</em>
		     	</div>

		     	 <div class="box_input">
                 @loginr_day1@
		     	</div>

		     	 <div class="box_input">
                 @loginr_day2@
		     	</div>

		     	 <div class="box_input">
                 @loginr_day3@
		     	</div>


		     	 <div class="box_input">
                 @loginr_day4@
		     	</div>

		     	 <div class="box_input">
                 @loginr_day5@
		     	</div>

		     	 <div class="box_input">
                 @loginr_day6@
		     	</div>

		     	 <div class="box_input">
                 @loginr_day7@
		     	</div>

			</div>
			<div class="col-6 mt-5 pt-4">
					<em  class="kt-link kt-link--state kt-link--success"><strong class="kt-link kt-link--state kt-link--success mt-2">Allow</strong> login during the following hours:</em></td>
					<div  class="row ">
						<div class="col-6 ">
							@loginr_time_from@
						</div>
						<div class="col-6">
							@loginr_time_to@
						</div>
					</div>
			</div>
		</div>
		<?=formBoxEnd()?>
<br />
<?=submitButtons(getLink("users"), "Update");?>
	</div>
</div>
 </div> 


</form>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript">
	$(function () {
	    $('#loginr_time_from').datetimepicker({
	        format: 'LT'
	    });

	     $('#loginr_time_to').datetimepicker({
	        format: 'LT'
	    });
	});
	</script>

<!-- <script>
	$(document).ready(function(){
	$('.form-check-input').before("<div class='chekbox_name'>");
	 var dat =  $('.form-check-input').clone($('.form-check-input'));
		/*$('.form-check-input').remove();*/
		  jQuery.each(dat, function(i, val ) {
		 var  idparent = val.id;
		 console.log(idparent);
		$("#chekbox_name").html(val);

		});
	
});
</script> -->