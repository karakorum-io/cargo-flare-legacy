<?php $taskType = str_replace("/", "", $_REQUEST['url']); ?>
<br>
<hr>
<br>
<br>

<div class="row">
	
    <div class="col-5">
        <form method="POST">
            <div class="form-group" style="position:relative;z-index:1;">
                <div class="input-group">
                    <input name="search_task_widget" type="text" maxlength="255" class="form-control" id="search_task_widget" value="<?php echo $_POST['search_task_widget'] == NULL ? "" : $_POST['search_task_widget']; ?>" placeholder = "Search your tasks here" />
                    <div class="input-group-append">
                        <input type="submit" value="Search" class="btn btn_bright_blue" /  id="submit_save">
                    </div>
                </div>
            </div>
        </form>
    </div>
	
    <div class="col-4"></div>
	
    <div class="col-3 pull-right text-right">
        <?php
        if ($taskType == "completed") {
            ?>
            <button type="button" class="btn btn_light_green active btn-nav-upd btn-sm"  onclick="mark_incomplete();">Mark Incomplete</button>
            <button type="button" class="btn btn_dark_blue btn-sm btn-nav-upd"  onclick="add_tasks();">New Tasks</button>
            <?php
        } elseif ($taskType == "history") {
			// no buttons on history section
		?>
		<?php
        } elseif ($taskType == "deleted") { ?>
            <button type="button" class="btn btn_light_green active btn-sm btn-nav-upd"  onclick="undelete_task();">Un-Delete</button>
            <?php
        } else { ?>
			<button type="button" class="btn btn_dark_green btn-sm"  onclick="mark_complete();">Completed</button>
            <button type="button" class="btn btn_dark_blue btn-sm"  onclick="add_tasks();">New Tasks</button>
		<?php } ?>
    </div>
	
</div>

<div class="clearfix"></div>

<div class="alert alert-light alert-elevate">
	<ul class="nav nav-tabs nav-tabs-line nav-tabs-line-3x nav-tabs-line-success" role="tablist" style="margin-bottom:0">
		<li class="nav-item">
			<a class="nav-link <?= (@$taskType == '') ? "active" : "" ?>" href="<?= SITE_IN ?>application/">Today Task</a>
		</li>
		<li class="nav-item">
			<a class="nav-link <?= (@$taskType == 'completed') ? "active" : "" ?>" href="<?= SITE_IN ?>application/completed/">Completed Task</a>
		</li>
		<li class="nav-item">
			<a class="nav-link <?= (@$taskType == 'deleted') ? "active" : "" ?>" href="<?= SITE_IN ?>application/deleted/">Deleted</a>
		</li>
		<li class="nav-item">
			<a class="nav-link <?= (@$taskType == 'history') ? "active" : "" ?>" href="<?= SITE_IN ?>application/history/">History</a>
		</li>
	</ul>
</div>


<script type="text/javascript">
   $("#submit_save").click(function(){
    
    Processing_show();
   })
</script>