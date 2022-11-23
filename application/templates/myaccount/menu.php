<div class="alert alert-light alert-elevate"><!-- today-bar -->
	<div class="row">
		<div class="col-12">
			<ul class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-success mb-2">
				<li class="nav-item">
					<a class="nav-link <?=(($this->daffny->action == "companyprofile" && $_GET['companyprofile'] =="")?"active":""); ?>" href="<?=getLink("companyprofile")?>">Profile</a>
				</li>
				<li class="nav-item">
					<a class="nav-link <?=($this->daffny->action == "billing" && $_GET['billing'] ==""?" active":""); ?>" href="<?=getLink("billing")?>">Billing</a>
				</li>				
				<li class="nav-item">
					<a class="nav-link <?=($this->daffny->action == "ratings" ?" active":""); ?>" href="<?=getLink("ratings")?>">Ratings</a>
				</li>
				
				<li class="nav-item">
					<a class="nav-link <?=($this->daffny->action == "documents" ?" active":""); ?>" href="<?=getLink("documents")?>">Documents</a>
				</li>
				
				<li class="nav-item">
					<a class="nav-link <?=($this->daffny->action == "freemonth" ?" active":""); ?>" href="<?=getLink("freemonth")?>">Referral</a>
				</li>
				
				<li class="nav-item">
					<a class="nav-link <?=($this->daffny->action == "billing"  && ($_GET['billing'] =="sms1" || $_GET['billing'] =="sms" || $_GET['billing'] =="sms_order_confirmation") ?" active":""); ?>" href="<?=getLink("billing/sms1")?>">SMS</a>
				</li>
				
			</ul>
		</div>
	</div>
</div>