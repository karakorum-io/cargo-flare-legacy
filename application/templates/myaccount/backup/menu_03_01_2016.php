<div class="tab-panel-container">
	<ul class="tab-panel">
		<li class="tab first<?=(($this->daffny->action == "companyprofile" && $_GET['companyprofile'] =="")?" active":""); ?>"><a href="<?=getLink("companyprofile")?>">Profile</a></li>
		<li class="tab <?=($this->daffny->action == "billing" && $_GET['billing'] ==""?" active":""); ?>"><a href="<?=getLink("billing")?>">Billing</a></li>
		<li class="tab <?=($this->daffny->action == "ratings" ?" active":""); ?>"><a href="<?=getLink("ratings")?>">Ratings</a></li>
		<li class="tab <?=($this->daffny->action == "documents" ?" active":""); ?>"><a href="<?=getLink("documents")?>">Documents</a></li>
		<? /*<li class="tab <?=($this->daffny->action == "companyprofile" && $_GET['companyprofile'] == "contract" ?" active":""); ?>"><a href="<?=getLink("companyprofile", "contract")?>">Contract</a></li>*/?>
		<li class="tab <?=($this->daffny->action == "freemonth" ?" active":""); ?>"><a href="<?=getLink("freemonth")?>">Referral</a></li>
        <li class="tab <?=($this->daffny->action == "billing" 
						   && ($_GET['billing'] =="sms1" || $_GET['billing'] =="sms" || $_GET['billing'] =="sms_order_confirmation") ?" active":""); ?>"><a href="<?=getLink("billing/sms1")?>">SMS</a></li>
	</ul>
</div>
<div class="tab-panel-line"></div>
<div style="clear:both">&nbsp;</div>