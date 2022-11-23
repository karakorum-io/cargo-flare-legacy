<? include(TPL_PATH . "settings/menu.php"); ?>
<h3>Automated quoting</h3>
Below is a list of quoting seasons. Click on a season to view or edit details. To change your automated quoting settings, click the button below.
<div style="text-align: left; clear:both; padding-bottom:5px; padding-top:5px;">
    <img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/add.gif" alt="Add" width="16" height="16" /> &nbsp;<a href="<?= getLink("autoquoting", "editseason") ?>">Add New Season</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/build.png" alt="AC Settings" width="16" height="16" /> &nbsp;<a href="<?= getLink("autoquoting", "settings") ?>">AQ Settings</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/external.png" alt="External" width="16" height="16" /> &nbsp;<a href="<?= getLink("externalforms", "build") ?>">External Quote Form</a>
</div>
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid">
    <tr class="grid-head">
        <td class="grid-head-left"><?= $this->order->getTitle("name", "Name") ?></td>
        <td>Lanes</td>
        <td><?= $this->order->getTitle("start_date", "Starts") ?></td>
        <td><?= $this->order->getTitle("end_date", "Ends") ?></td>
        <td>Status</td>
        <td class="grid-head-right" colspan="4">Actions</td>
    </tr>
    <? if (count($this->data) > 0) { ?>
        <? foreach ($this->data as $i => $season) { ?>
            <tr class="grid-body<?= ($i == 0 ? " first-row" : "") ?>" id="row-<?= $season->id ?>">
                <td class="grid-body-left"><?= htmlspecialchars($season->name); ?></td>
                <td align="center"><?= $season->getLanesCount() ?></td>
                <td align="center"><?= $season->getStartDate() ?></td>
                <td align="center"><?= $season->getEndDate() ?></td>
                <td align="center"><?= statusText(getLink("autoquoting", "statusseason", "id", $season->id), $season->status) ?></td>
                <td style="width: 16px;"><?= infoIcon(getLink("autoquoting", "lanes", "sid", $season->id)) ?></td>
                <td style="width: 16px;"><?= editIcon(getLink("autoquoting", "editseason", "id", $season->id)) ?></td>
                <td style="width: 16px;"><?= copyIcon(getLink("autoquoting", "copyseason", "id", $season->id)) ?></td>
                <td style="width: 16px;" class="grid-body-right"><?= deleteIcon(getLink("autoquoting", "deleteseason", "id", $season->id), "row-" . $season->id) ?></td>
            </tr>
        <? } ?>
    <? } else { ?>
        <tr class="grid-body first-row" id="row-">
            <td align="center" colspan="8">No records found.</td>
        </tr>
    <? } ?>
</table>
@pager@