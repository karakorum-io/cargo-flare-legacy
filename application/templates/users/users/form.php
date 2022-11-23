
<? include(TPL_PATH."users/menu_details.php"); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js"></script>

<form action="<?=getLink("users", "edit", "id", get_var("id"))?>" method="post" enctype="multipart/form-data">
	<div class="alert alert-light alert-elevate" >
    	<div class="row w-100">
    		<div class="col-12 mt-2">
    			<?=formBoxStart((int)get_var("id") > 0?"Edit: <span class=\"lightblue\">@contactname_txt@ (@username_txt@)</span>":"Add New")?>
				<div class="row">
					<div class="col-6">
						<div class="form-group">
							@contactname@
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							@email@
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-6">
						<div class="form-group">
							@phone@
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							@lead_multiple@
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-6">
						<div class="form-group">
							@status@
						</div>
					</div>

					<div class="col-6">
						<div class="form-group">
							@username@
						</div>
					</div>
				</div>
				<?php if(get_var("id")<=0){?>
				<div class="row">
					<div class="col-6">
						<div class="form-group">
							@password@
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							@password_confirm@
						</div>	
					</div>
				</div>
				<?php }?>
				<div class="row">
					<div class="col-6">
						<div class="form-group">
							<?php if(get_var("id")>0){?><a href="<?= getLink("users", "changepassword", "id", get_var("id"));?>">Change Password</a><?php }?>
						</div>
					</div>
					<div class="col-6"></div>
				</div>
				<?=formBoxEnd()?>
				<br />
				<?=submitButtons(getLink("users"))?>
        	</div>
       </div>    
	</div>
</form>

<script type="text/javascript">
	$(document).ready(function(){
		$("#phone").attr("placeholder", "xxx-xxx-xxxx");
		$("#phone").mask("999-999-9999",{ placeholder:"xxx-xxx-xxxx"});
	});
</script>