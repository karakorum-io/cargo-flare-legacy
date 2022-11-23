<div class="tab-panel-container">
	<ul class="tab-panel">
			<li class="tab first<?=($this->daffny->action == "companyprofile"?" active":""); ?>"><a href="<?=getLink("companyprofile")?>">Company Profile</a></li>
			<li class="tab<?=($this->daffny->action == "defaultsettings"?" active":""); ?>"><a href="<?=getLink("defaultsettings")?>">Default Settings</a></li>
		<?php if ($_SESSION['is_broker']) { ?>
			<li class="tab<?=($this->daffny->action == "autoquoting"?" active":""); ?>"><a href="<?=getLink("autoquoting")?>">Automated Quoting</a></li>
			<li class="tab<?=($this->daffny->action == "externalforms"?" active":""); ?>"><a href="<?=getLink("externalforms")?>">External Forms</a></li>
			<li class="tab<?=($this->daffny->action == "emailtemplates"?" active":""); ?>"><a href="<?=getLink("emailtemplates")?>">Email Templates</a></li>
			<li class="tab<?=($this->daffny->action == "formtemplates"?" active":""); ?>"><a href="<?=getLink("formtemplates")?>">Form Templates</a></li>
		<?php } ?>
	</ul>
</div>
<div class="tab-panel-line"></div>
<div style="clear:both">&nbsp;</div>