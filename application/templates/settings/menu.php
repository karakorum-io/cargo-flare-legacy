<div class="alert alert-light alert-elevate">
    <div class="row">
		<div class="col-12">
			<ul class="nav nav-tabs nav-tabs-line nav-tabs-line-3x nav-tabs-line-success" role="tablist" style="margin-bottom:0">
				<li class="nav-item">
					<a class="nav-link <?=($this->daffny->action == "companyprofile"?" active":""); ?>" href="<?=getLink("companyprofile")?>">Company Profile</a>
				</li>
				<li class="nav-item">
					<a class="nav-link <?=($this->daffny->action == "defaultsettings"?" active":""); ?>" href="<?=getLink("defaultsettings")?>">Default Settings</a>
				</li>
				<?php if ($_SESSION['is_broker']) { ?>
				<li class="nav-item">
					<a class="nav-link <?=($this->daffny->action == "autoquoting"?" active":""); ?>" href="<?=getLink("autoquoting")?>">Automated Quoting</a>
				</li>
				<li class="nav-item">
					<a class="nav-link <?=($this->daffny->action == "emailtemplates"?" active":""); ?>" href="<?=getLink("emailtemplates") ?>">Email Templates</a>
				</li>
				<li class="nav-item">
					<a class="nav-link <?=($this->daffny->action == "formtemplates"?" active":""); ?>" href="<?=getLink("formtemplates") ?>">Form Templates</a>
				</li>
				<?php } ?>
			</ul>
		</div>
    </div>
</div>