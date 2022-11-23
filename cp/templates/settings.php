<?php if (!empty($this->tplVarNames)): ?>
    @flash_message@
    <form action="<?php echo getLink("settings"); ?>" method="post">
	    <?=formBoxStart()?>
        <?php foreach ($this->tplVarNames as $varName): ?>
			<?php echo $varName; ?><br /><br />
        <?php endforeach; ?>
        <?=formBoxEnd()?>
        <br />
        <?php echo submitButtons(SITE_IN."cp"); ?>
    </form>

<?php else: ?>
    Settings not found.
<?php endif; ?>