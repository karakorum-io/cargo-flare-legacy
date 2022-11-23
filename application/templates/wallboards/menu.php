<?php
/**
 * View file responsible for header menus in wallboard section
 * 
 * @author Chetu Inc,
 * @verison 1.0 
 */
?>
<link rel="stylesheet" href="<?php echo SITE_IN ?>styles/wallboard.css">
<script src="<?php echo SITE_IN ?>jscripts/wallboard.js"></script>



<div class="alert alert-light alert-elevate " style="margin: 0px 0px">
    <ul class="nav nav-tabs  nav-tabs-line nav-tabs-line-3x nav-tabs-line-success mb-0">
        <li class="nav-item custom_set">
            <a class="nav-link active" href="<?= getLink("wallboards") ?>">Sales</a>
        </li>
        <li class="nav-item custom_set">
            <a  class="nav-link" href="<?= getLink("wallboards") ?>">Dispatch [Under Development!]</a>
        </li>
    </ul>
</div>



