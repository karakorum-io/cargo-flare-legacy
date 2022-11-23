<div class="internal" style="padding: 15px 0 0 0">
	<?php if (!empty(CpAction::$actionLinks)): ?>
	<div style="float: right; padding-right: 5px"><?php echo CpAction::$actionLinks ?></div>
	<?php endif; ?>
    <h2 style="padding: 0 0 10px 0; float: left">@title@</h2>
	<div style="clear: both"></div>
    @content@
</div>