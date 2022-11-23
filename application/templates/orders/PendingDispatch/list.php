<div class="alert alert-light alert-elevate " style="margin: 0px 0px">
    <ul class="nav nav-tabs  nav-tabs-line nav-tabs-line-3x nav-tabs-line-success">
        <li class="nav-item custom_set">
            <a class="nav-link" href="<?= getLink("wallboards") ?>">Sales</a>
        </li>
        <li class="nav-item custom_set">
            <a  class="nav-link active" href="<?= getLink("wallboards","pending_dispatch") ?>">Pending Dispatch</a>
        </li>
    </ul>
</div>

<div class="row alert alert-light alert-elevate mt-4 ">
    <br/>
    <a target="_blank" href="<?php echo SITE_IN ?>wallboards/view_pending_dispatch/hash/<?php echo md5($_SESSION['member']['parent_id']) ?><?php echo md5($_SESSION['member']['parent_id']) ?><?php echo md5($_SESSION['member']['parent_id']) ?>-<?php echo $_SESSION['member']['parent_id'] ?>">
        <button class="btn btn-primary" style="width:150px;">ShowBoard</button>
    </a>
    <br/>
    <br/>
    <br/>

    <table class="table table-bordered">
        <tr>
            <td><?php echo $this->order->getTitle("entity_id", "#ID") ?></td>
            <td><?php echo $this->order->getTitle("carrier_name", "Name") ?></td>
            <td><?php echo $this->order->getTitle("carrier_email", "Email") ?></td>
            <td><?php echo $this->order->getTitle("carrier_phone", "Phone") ?></td>
            <td><?php echo $this->order->getTitle("carrier_contact", "Contact") ?></td>
            <td><?php echo $this->order->getTitle("comment", "Comment") ?></td>
            <td><?php echo $this->order->getTitle("comment", "Time") ?></td>
            <td colspan="4">Actions</td>
        </tr>
        <?php
            foreach ($this->data as $i => $data) {
        ?>
            <tr id="row-<?php echo $data['id'] ?>">
                <td align="center" class="grid-body-left">
                    <a href="<?php echo getLink("orders", "show", "id", $data['entity_id']) ?>"><?php echo $data['order_id'] ?></a>
                </td>
                <td>
                    <?php echo $data['carrier_name'] ?>
                </td>
                <td>
                    <?php echo $data['carrier_email'] ?>
                </td>
                <td>
                    <?php echo $data['carrier_phone'] ?>
                </td>
                <td>
                    <?php echo $data['carrier_contact'] ?>
                </td>
                <td>
                    <?php echo $data['comment'] ?>
                </td>
                <td>
                    <?php echo date('h', (strtotime(date("Y-m-d H:i:s")) - strtotime($data['created_at']))) ?> Hour(s)
                    <?php echo date('i', (strtotime(date("Y-m-d H:i:s")) - strtotime($data['created_at']))) ?> Minutes(s)
                </td>
                <td class="grid-body-right">
                    <center>
                    <a href="<?php echo getLink("wallboards", "delete_pending_dispatch", "id", $data['entity_id']) ?>">
                        <img src="/images/icons/delete.png" title="Delete" alt="Delete" class="pointer" width="16" height="16">
                    </a>
                    </center>
                </td>
            </tr>
        <?php }?>
    </table>
</div>
@pager@