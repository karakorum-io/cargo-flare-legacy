@flash_message@
<?php if (!$this->show_form): ?>
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid">
    <tr class="grid-head">
        <td class="grid-head-left" style="width: 200px;">Title</td>
        <td>Content</td>
        <td style="width: 30px;" class="grid-head-right">Edit</td>
    </tr>
    <?php foreach ($this->data as $i => $data): ?>
    <tr class="grid-body<?php echo ($i == 0 ? " first-row" : ""); ?>" id="row-<?php echo $data['id']; ?>">
        <td class="grid-body-left"><?php echo $data['title']; ?></td>
        <td><?php echo $data['content']; ?></td>
        <td align="center" class="grid-body-right"><?php echo editIcon(getLink("content", "edit", "id", $data['id'])); ?></td>
    </tr>
    <?php endforeach; ?>
</table>
@pager@
<?php else: ?>
<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?=getLink("content")?>">&nbsp;Back to the list</a>
</div>
<form action="<?=getLink("content", "edit", "id", get_var("id"))?>" method="post">
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td valign="top">
                <table cellpadding="3" cellspacing="5" border="0" style="padding-bottom: 5px;">
                    <tr>
                        <td>@title@</td>
                    </tr>
                    <tr>
						<td valign="top"><label for="content">Content:</label></td>
						<td>@content@</td>
					</tr>
                </table>
            </td>
        </tr>
        <tr>
            <td><br /><div align="center"><?=submitButtons(getLink("content"))?></div></td>
        </tr>
    </table>
</form>
<?php endif; ?>