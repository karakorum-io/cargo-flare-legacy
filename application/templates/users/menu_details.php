



<div class="alert alert-light alert-elevate  ">
	<ul class="nav nav-tabs  nav-tabs-line nav-tabs-line-3x nav-tabs-line-success mb-0 ">
	<? if (isset($_GET['id']) && (int)$_GET['id'] > 0){?>
		<li class="nav-item custom_set" ><a  class="nav-link <?=(isset($_GET["users"]) && $_GET['users'] == "show"?" active":""); ?>" href="<?=getLink("users", "show", "id", $_GET['id']) ?>">Details</a></li>
		<li  class="nav-item custom_set"><a  class="nav-link <?=(isset($_GET["users"]) && $_GET['users'] == 'edit'?" active":""); ?>"href="<?=getLink("users", "edit", "id", $_GET['id'])?>">Edit</a></li>
		<li  class="nav-item custom_set"><a class="nav-link <?=(isset($_GET["users"]) && $_GET['users'] == 'privileges'?" active":""); ?>" href="<?=getLink("users", "privileges", "id", $_GET['id'])?>">Privileges</a></li>
		<li  class="nav-item custom_set"><a class="nav-link <?=(isset($_GET["users"]) && $_GET['users'] == 'restrictions'?" active":""); ?>" href="<?=getLink("users", "restrictions", "id", $_GET['id'])?>">Login Restrictions</a></li>
		<li class="nav-item custom_set" ><a  class="nav-link <?=(isset($_GET["users"]) && $_GET['users'] == 'loginhistory'?" active":""); ?>" href="<?=getLink("users", "loginhistory", "id", $_GET['id'])?>">Login History</a></li>
		<li  class="nav-item custom_set"><a class=" nav-link <?=(isset($_GET["users"]) && $_GET['users'] == 'userhistory'?" active":""); ?>" href="<?=getLink("users", "userhistory", "id", $_GET['id'])?>">User History</a></li>
	<? }else{ ?>
		<li  class="nav-item custom_set"><a class="nav-link mr-3 " href="<?=getLink("users", "active")?>">Users</a></li>
		<li  class="nav-item custom_set"><a  class=" nav-link mr-3 <?=(isset($_GET["users"]) && $_GET['users'] == 'edit'?" active":""); ?>" href="<?=getLink("users", "edit")?>">Add</a></li>
	<? } ?>
	</ul>
</div>

