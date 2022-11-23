



<div class="alert alert-light alert-elevate " style="padding:  0px 28px ">
	<ul class="nav nav-tabs  nav-tabs-line nav-tabs-line-3x nav-tabs-line-success" role="tablist">
		<li class="nav-item custom_set" ><a class="nav-link<?=(($this->daffny->action == "leadsources" && $_GET['leadsources'] !="requests")?" active":""); ?>"  href="<?=getLink("leadsources")?>">Lead Sources</a></li>
		<li class="nav-item custom_set"><a class="nav-link <?=($this->daffny->action == "leadsources" && $_GET['leadsources'] == "requests" ?" active":""); ?>" href="<?=getLink("leadsources", "requests")?>">Requests</a></li>
	</ul>
</div>
