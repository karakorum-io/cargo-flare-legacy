<style type="text/css">
    list-inline-item:not(:last-child) {
		margin-right: 0.5rem;
	}
	h1.details {
		width: 100%;
		color: #3b67a6;
		padding: 0 0 13px 20px;
	}
	li.list-inline-item  {
		background: #374afb;
		color: white !important;
		padding: 12px;
	}
	li.list-inline-item a {
	color: white !important;
	}
</style>
<div class="row">
	<div class="col-6">
		<?php
        if($_GET['task'] == 2){ ?>
			<button type="button" class=" btn btn_bright_blue btn-sm "  onclick="mark_incomplete();">
				Mark Incomplete
			</button>
			<button type="button" class="btn btn_bright_blue btn-sm"  onclick="add_tasks();">
				New Tasks
			</button>
			
		<?php } elseif ($_GET['task'] == 3) { ?>

			<button type="button" class=" btn  btn_bright_blue  btn-sm" style="width:150px;" onclick="undelete_task();">
				Un-Delete
			</button>
			<?php } else { ?>
			
			<button type="button" class=" btn btn_bright_blue btn-sm "  onclick="mark_complete();">
				Completed
			</button>
			<button type="button" class=" btn btn_bright_blue btn-sm"  onclick="add_tasks();">
				New Tasks
			</button>
		<?php } ?>
    </div>
	
	<div class="col-6 text-right">
	
		<ul class="list-inline-item ">
			<li class="btn btn_bright_blue btn-sm <?= (@$_GET['task'] == '' || @$_GET['task'] == 1) ? " first active" : "" ?>" >
				<a href="<?= SITE_IN ?>application/orders/show/id/<?php echo $_GET['id'];?>/task/1" style="color: white">Assigned Task</a>
			</li>
			<li class=" btn btn-sm btn_bright_blue <?= (@$_GET['task'] == '2') ? " active" : "" ?>" >
				<a href="<?= SITE_IN ?>application/orders/show/id/<?php echo $_GET['id'];?>/task/2" style="color: white">Completed Task</a>
			</li>
			<li class="btn btn-sm btn-dark<?= (@$_GET['task'] == '3') ? " last active" : "" ?>" >
				<a href="<?= SITE_IN ?>application/orders/show/id/<?php echo $_GET['id'];?>/task/3" style="color: white">Deleted</a>
			</li>
		</ul>

    </div>
</div>


