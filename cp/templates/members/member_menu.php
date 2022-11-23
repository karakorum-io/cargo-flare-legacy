<div class="tab-panel-container">
	<ul class="tab-panel">
	    <li class="tab first<?= (@$_GET['members'] == '') ? " active" : "" ?>"><a href="<?= SITE_IN ?>cp/members/">Members</a></li>
		<li class="tab <?= (@$_GET['members'] == 'applied') ? " last active" : "" ?>"><a href="<?= SITE_IN ?>cp/members/applied/">Applied Members</a></li>
       <li style="padding-top:6px;"><a href="<?= SITE_IN ?>cp/registration/" style="margin-left:30px;padding-top:10px;"><img width="16" height="16" alt="Add" src="/images/icons/add.png" style="vertical-align:middle;">&nbsp;&nbsp;Add New Member</a></li>
	</ul>
</div>
