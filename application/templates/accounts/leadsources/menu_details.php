

<div class="alert alert-light alert-elevate " style="margin: 0px; padding:  0px 28px ">
	<ul class="nav nav-tabs  nav-tabs-line nav-tabs-line-3x nav-tabs-line-success">
	<? if (isset($_GET['id']) && (int)$_GET['id'] > 0){?>
		<li class="nav-item custom_set"><a class="nav-link<?=(isset($_GET["leadsources"]) && $_GET['leadsources'] == "details"?" active":""); ?>" href="<?=getLink("leadsources", "details", "id", $_GET['id']) ?>">Lead Source Details</a></li>
		<li class="nav-item custom_set"><a class=" nav-link <?=(isset($_GET["leadsources"]) && $_GET['leadsources'] == 'edit'?" active":""); ?>" href="<?=getLink("leadsources", "edit", "id", $_GET['id'])?>">Lead Source Edit</a></li>
		<li class="nav-item custom_set"><a class=" nav-link <?=(isset($_GET["leadsources"]) && $_GET['leadsources'] == 'accessAccount'?" active":""); ?>" href="<?=getLink("leadsources", "accessAccount", "id", $_GET['id'])?>">Account Access</a></li>
	<? }else{ ?>
		<li class="nav-item custom_set"><a  class="nav-link" href="<?=getLink("leadsources")?>">Lead Sources</a></li>
		<li class="nav-item custom_set" ><a class="nav-link <?=(isset($_GET["leadsources"]) && $_GET['leadsources'] == 'edit'?" active":""); ?>"  href="<?=getLink("leadsources", "edit")?>">Add New Lead Source</a></li>
	<? } ?>
	</ul>
</div>
<div style="clear:both">&nbsp;</div>