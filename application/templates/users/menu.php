<div class="alert alert-light alert-elevate">
	<ul class="nav nav-tabs  nav-tabs-line nav-tabs-line-3x nav-tabs-line-success mb-0">
			<li class="nav-item custom_set" ><a  class="nav-link<?=($this->daffny->action == "users" && $_GET['users']!="history"?" active":""); ?>" href="<?=getLink("users", "active")?>">Users</a></li>
			<li class="nav-item custom_set" ><a  class="nav-link<?=($this->daffny->action == "users_groups"?" active":""); ?>" href="<?=getLink("users_groups")?>">Groups</a></li>
			<li  class="nav-item custom_set"><a  class="nav-link<?=($this->daffny->action == "users" && $_GET['users']=="history"?" active":""); ?>" href="<?=getLink("users", "history")?>">Users History</a></li>
	</ul>
</div>	

