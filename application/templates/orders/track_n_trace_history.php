    <style type="text/css">
        li#tracks_history_previous
        {
         display: none;
        }
        li#tracks_history_next
        {
           display: none;
        }
    </style>

<?php
/**
 * HTML view file for track and trace functionality
 * 
 * @author Chetu Inc.
 * @version 1.0
 */
/**
 * Controller Variable Management
 */
$tracks = $this->daffny->tpl->tracks;
?>
<!-- Loading external JS file-->
<script src="<?= SITE_IN ?>jscripts/track_n_trace.js"></script>
<!-- Loading external CSS file-->
<!-- Adding Order Menus-->
<div style="padding-top:15px;">
    <?php include('order_menu.php'); ?>
</div>
<div class="add-location-form-div mt-4 mb-3">
    <h3>Carrier Location History</h3>
    <table class="table table-bordered" id="tracks_history">
        <thead>
            <tr>
                <th class="grid-head-left">ID</th>
                <th class="grid-head-left">Track ID</th>
                <th>Entity Id</th>
                <th>Activity</th>
                <th>Old Values</th>
                <th>New Values</th>
                <th>Action By</th>
                <th>Time Captured</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (count($tracks) > 0) {
                for ($i = 0; $i < count($tracks); $i++) {
                    ?>
                    <tr>
                        <td  class="grid-body-left grid-body-right"><?php echo $tracks[$i]['id'] ?></td>
                        <td  class="grid-body-left grid-body-right"><?php echo $tracks[$i]['track_id'] ?></td>
                        <td><?php echo $tracks[$i]['entity_id'] ?></td>
                        <td><?php echo $tracks[$i]['activity'] ?></td>
                        <td><?php echo $tracks[$i]['old_values'] ?></td>
                        <td><?php echo $tracks[$i]['new_values'] ?></td>
                        <td><?php echo $tracks[$i]['logged_by_name'] ?></td>
                        <td><?php echo $tracks[$i]['created_at'] ?></td>                    
                    </tr>
                    <?php
                }
            } else {
                echo "<tr class='grid-body'>"
                . "<td class='grid-body-left grid-body-right' colspan='7' align='center'>No Track Records Found!</td>"
                . "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>


<script type="text/javascript">
    $(document).ready(function() {
     $('#tracks_history').DataTable();
    });
</script>
