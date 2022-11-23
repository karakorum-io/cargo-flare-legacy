<div class="alert alert-light alert-elevate ">
	<ul class="nav nav-tabs  nav-tabs-line nav-tabs-line-3x nav-tabs-line-success"  >
		<? if (isset($_GET['id']) && (int)$_GET['id'] > 0){?>
		<li class="nav-item custom_set" ><a  class="nav-link<?=(isset($_GET["users_groups"]) && $_GET['users_groups'] == 'edit'?" active":""); ?>" href="<?=getLink("users_groups", "edit", "id", $_GET['id'])?>">Edit Group Details</a></li>
		<? }else{ ?>
			<li class="nav-item custom_set" ><a  class="nav-link<?=(isset($_GET["users_groups"]) && $_GET['users_groups'] == 'edit'?" active":""); ?>" href="<?=getLink("users_groups", "edit")?>">Add New Group</a></li>
		<? } ?>
	</ul>
</div>