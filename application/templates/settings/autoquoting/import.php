</br>
<? include(TPL_PATH . "settings/menu.php"); ?>
<h3>AQ Import Lanes</h3>
Choose <strong style="font-style:italic;">*.CSV</strong> file with lanes data and press "Upload".
<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
    <img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?= getLink("autoquoting", "lanes", "sid", (int) get_var("sid")) ?>">&nbsp;Back to the list</a>
</div>
<form action="<?= getLink("autoquoting", "import", "sid", (int) get_var("sid")) ?>" method="post" enctype="multipart/form-data">
    <?= formBoxStart("Choose *.CSV file") ?>
    <table cellspacing="5" cellpadding="5" border="0">
        <tr><td>@csv@</td><td><a href="<?php echo SITE_IN ?>formats/csv/lanes.csv">Download Sample</a></td></tr>
    </table>
    <?= formBoxEnd() ?>
    <br />
    <?php echo submitButtons(getLink("autoquoting"), "Upload"); ?>
</form>