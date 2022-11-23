<? include(TPL_PATH."users/menu_details.php"); ?>
<form action="<?=getLink("users", "changepassword", "id", get_var("id"))?>" method="post" enctype="multipart/form-data">
   <?=formBoxStart("Change Password")?>
<div class="row">

<div class="alert alert-light alert-elevate w-100  ">

	<div class="col-4 mt-3">
	
		<div class="row">
			@password@
		</div>
		<div class="row mb-3">
			@password_confirm@
		</div>
		<?=formBoxEnd()?>
        <?=submitButtons(getLink("users"))?>
	</div>
	
</div>
</div>
   
    
</form>