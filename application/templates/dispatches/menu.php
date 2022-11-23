<div class="tab-panel-container">
    <ul class="tab-panel">
        <li class="tab first<?= ($_GET['dispatches'] == 'notsigned')?" active":"" ?>"onclick="location.href = '<?= SITE_IN ?>application/dispatches/notsigned'">Not Signed<span>(@notsigned_count@)</span></li>
        <li class="tab<?= ($_GET['dispatches'] == 'dispatched')?" active":"" ?>"onclick="location.href = '<?= SITE_IN ?>application/dispatches/dispatched'">Dispatched<span>(@dispatched_count@)</span></li>
        <li class="tab<?= ($_GET['dispatches'] == 'pickedup')?" active":"" ?>"onclick="location.href = '<?= SITE_IN ?>application/dispatches/pickedup'">Picked-Up<span>(@pickedup_count@)</span></li>
        <li class="tab<?= ($_GET['dispatches'] == 'delivered')?" active":"" ?>"onclick="location.href = '<?= SITE_IN ?>application/dispatches/delivered'">Delivered<span>(@delivered_count@)</span></li>
        <?/*<li class="tab<?= ($_GET['dispatches'] == 'cancelled')?" active":"" ?>"onclick="location.href = '<?= SITE_IN ?>application/dispatches/cancelled'">Cancelled<span>(@cancelled_count@)</span></li>*/?>
        <li class="tab<?= ($_GET['dispatches'] == 'archived')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/dispatches/archived'">Archived<span>(@archived_count@)</span></li>
        <?php if (isset($_POST['search_string'])) : ?>
        <li class="last tab active">Search Results<span>(@search_count@)</span></li>
        <?php endif; ?>
    </ul>
</div>
<div class="tab-panel-line"></div>