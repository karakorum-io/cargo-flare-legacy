<?php $taskType = str_replace("/","",$_REQUEST['url']); ?>
<div class="tab-panel-container">
	<ul class="tab-panel">
	
		<li class="tab <?= (@$taskType == '') ? " first active" : "" ?>"><a href="<?= SITE_IN ?>application/">Today Task</a></li>
		<li class="tab <?= (@$taskType == 'completed') ? " active" : "" ?>"><a href="<?= SITE_IN ?>application/completed/">Completed Task</a></li>
        <li class="tab <?= (@$taskType == 'deleted') ? " last active" : "" ?>"><a href="<?= SITE_IN ?>application/deleted/">Deleted</a></li>
	</ul>
</div>
