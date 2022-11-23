</br>
<? include(TPL_PATH . "settings/menu.php"); ?>


<div align="right" style="clear:both; padding-bottom:15px;">
    <img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?= getLink("autoquoting") ?>">&nbsp;Back to the list</a>
</div>


<div class="kt-portlet">
	
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h3><?= formBoxStart(((int) get_var("id") > 0 ? "Edit Season" : "Add New Season")) ?></h3>
		</div>
	</div>
	
	<div class="kt-portlet__body">
		
		<div class="row">
			<div class="col-12">
				<form action="<?= getLink("autoquoting", "editseason", "id", get_var("id")) ?>" method="post">
					<div class="row">
						<div class="col-3">
							<div class="form-group">
								@name@
							</div>
						</div>
					
						<div class="col-3">
							<div class="form-group">
								@start_date@
							</div>
						</div>

						<div class="col-3">
							<div class="form-group">
								@end_date@
							</div>
						</div>
					
						<div class="col-3">
							<div class="form-group">
								@status@
							</div>
						</div>
					</div>

					<?= formBoxEnd() ?>
					<br />
					<?= submitButtons(getLink("autoquoting"), "Save") ?>
				</form>

			</div>
		</div>
	</div>
</div>





<script type="text/javascript">//<![CDATA[
    $(function(){
        $('#start_date').datepicker(datepickerSettings);
        $('#end_date').datepicker(datepickerSettings);
    });
    //]]></script>