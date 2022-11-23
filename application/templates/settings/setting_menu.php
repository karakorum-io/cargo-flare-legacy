<?php $taskType = str_replace("/","",$_REQUEST['url']); ?>
<div class="tab-panel-container">
	<ul class="tab-panel">
	
		<!--li class="tab <?= (@$taskType == '') ? " first active" : "" ?>"><a href="<?= SITE_IN ?>application/">Residential</a></li>
		<li class="tab <?= (@$taskType == 'completed') ? "  last active" : "" ?>"><a href="<?= SITE_IN ?>application/completed/">Commercial</a></li-->
        <li class="tab <?= (@$taskType == '') ? " first active" : "" ?>"  onclick="resid_comm_tabs(1);" id="resi"><a href="javascript:voi(0);" onclick="resid_comm_tabs(1);">Residential</a></li>
		<li class="tab <?= (@$taskType == 'completed') ? "  last active" : "" ?>"  onclick="resid_comm_tabs(0);" id="comm"><a href="javascript:voi(0);"  onclick="resid_comm_tabs(0);">Commercial</a></li>
       	</ul>
</div>
