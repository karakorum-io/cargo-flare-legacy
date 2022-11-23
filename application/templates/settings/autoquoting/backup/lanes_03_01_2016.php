<? include(TPL_PATH . "settings/menu.php"); ?>
<h3>@season_name@ <span style="color:#444; font-size:12px; font-weight:normal">(@season_start_date@ - @season_end_date@)</span></h3>
<div style="text-align: left; clear:both; padding-bottom:5px; padding-top:5px;">
    <table width="100%" border="0">
        <tr>
            <td align="left">
                <img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/add.gif" alt="Add" width="16" height="16" /> &nbsp;<a href="<?= getLink("autoquoting", "editlane", "id", 0, "sid", (int) get_var("sid")); ?>">Add New Lane</a>
                &nbsp;&nbsp;&nbsp;
                <img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/import.png" alt="Import" width="16" height="16" /> &nbsp;<a href="<?= getLink("autoquoting", "import", "sid", (int) get_var("sid")); ?>">Import Lanes</a>
            </td>
            <td align="right"><img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/back.png" alt="Back" width="16" height="16" /> &nbsp;<a href="<?= getLink("autoquoting"); ?>">Back to the seasons list</a></td>
        </tr>
    </table>
</div>
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid">
    <tr class="grid-head">
        <td class="grid-head-left"><?= $this->order->getTitle("name", "Lane") ?></td>
        <td>Base/CPM price</td>
        <td><?= $this->order->getTitle("modified", "Last Modified") ?></td>
        <td>Status</td>
        <td class="grid-head-right" colspan="3">Actions</td>
    </tr>
    <? if (count($this->data) > 0) { ?>
        <? foreach ($this->data as $i => $lane) {
            /** @var $lane AutoQuotingLane */
            ?>
            <tr class="grid-body<?= ($i == 0 ? " first-row" : "") ?>" id="row-<?= $lane->id ?>">
                <td class="grid-body-left"><?= htmlspecialchars($lane->name); ?></td>
                <td align="center">$<?= $lane->getBaseOrCPMPrice() ?></td>
                <td align="center"><?= $lane->getModified() ?></td>
                <td align="center"><?= statusText(getLink("autoquoting", "statusseason", "id", $lane->id), $lane->status, "sid", (int) get_var("sid")) ?></td>
                <td style="width: 16px;"><?= editIcon(getLink("autoquoting", "editlane", "id", $lane->id, "sid", (int) get_var("sid"))) ?></td>
                <td style="width: 16px;"><?= copyIcon(getLink("autoquoting", "copylane", "id", $lane->id, "sid", (int) get_var("sid"))) ?></td>
                <td style="width: 16px;" class="grid-body-right"><?= deleteIcon(getLink("autoquoting", "deletelane", "id", $lane->id, "sid", (int) get_var("sid")), "row-" . $lane->id) ?></td>
            </tr>
        <? } ?>
    <? } else { ?>
        <tr class="grid-body first-row" id="row-">
            <td align="center" colspan="7" class="grid-body-left grid-action-right"><i>No records found.</i></td>
        </tr>
    <? } ?>
</table>
@pager@