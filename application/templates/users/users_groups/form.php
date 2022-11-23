<? include(TPL_PATH."users/users_groups/menu.php"); ?>
<form action="<?=getLink("users_groups", "edit", "id", get_var("id"))?>" method="post">
	<div class="alert alert-light alert-elevate" >
    	<div class="row w-100">
    		<div class="col-12 mt-2">
				<?=formBoxStart(((int)get_var("id") > 0 ? "Group Information" : "Add New Group"))?>
				<div class="row">
					<div class="col-6">
						<div class="form-group">
							@name@
						</div>
					</div>
				</div>
				<?=formBoxEnd()?>
				<?=formBoxStart("Group Privileges")?>
					<div class="col-6">
						<div class="form-group">
							@access_leads@
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							@access_quotes@
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							@access_orders@
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							@access_accounts@
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							@access_dispatch@
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							@access_payments@
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							@access_lead_sources@
						</div>
					</div>
				<?=formBoxEnd()?>
				<br />
				<?=submitButtons(getLink("users_groups"))?>
			</div>
		</div>
	</div>
</form>