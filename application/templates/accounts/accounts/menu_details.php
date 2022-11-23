
<style type="text/css">
    .nav-pills, .nav-tabs {
    margin: 0 0 0px 0;
}
</style>



<div class="alert alert-light alert-elevate" role="alert">


      <?php if (isset($_GET['id']) && (int) $_GET['id'] > 0) { ?>
    <ul class="nav nav-tabs  nav-tabs-line nav-tabs-line-3x nav-tabs-line-success" role="tablist">
        <li class="nav-item">
            <a class="nav-link<?=(isset($_GET["accounts"]) && $_GET['accounts'] == "details" ? " active" : "");?> "  href="<?=getLink("accounts", "details", "id", $_GET['id'])?>" >Account Details</a>
        </li>

        <li class="nav-item<?=(isset($_GET["accounts"]) && $_GET['accounts'] == 'edit' ? " active" : "");?>">
            <a class="nav-link<?=(isset($_GET["accounts"]) && $_GET['accounts'] == 'edit' ? " active" : "");?>"  href="<?=getLink("accounts", "edit", "id", $_GET['id'])?>">Edit Account</a>
        </li>


        <li class="nav-item">
            <a class="nav-link<?=(isset($_GET["accounts"]) && $_GET['accounts'] == 'accounthistory' ? " active" : "");?>"  href="<?=getLink("accounts", "accounthistory", "id", $_GET['id'])?>">Account History</a>
        </li>



        <li class="nav-item">
            <a class="nav-link<?=(isset($_GET["accounts"]) && $_GET['accounts'] == 'accountConract' ? " active" : "");?>"  href="<?=getLink("accounts", "accountConract", "id", $_GET['id'])?>">Account Contract</a>
        </li>




        <li class="nav-item">
            <a class="nav-link<?=(isset($_GET["accounts"]) && $_GET['accounts'] == 'uploads' ? " active" : "");?>"  href="<?=getLink("accounts", "uploads", "id", $_GET['id'])?>">Documents</a>
        </li>


        <li class="nav-item">
            <a class="nav-link<?=(isset($_GET["accounts"]) && $_GET['accounts'] == 'route' ? " active" : "");?>"  href="<?=getLink("accounts", "route", "id", $_GET['id'])?>">Route</a>
        </li>



        <li class="nav-item">
            <a class="nav-link<?=(isset($_GET["accounts"]) && $_GET['accounts'] == 'SavedCards' ? " active" : "");?>"  href="<?=getLink("accounts", "SavedCards", "id", $_GET['id'])?>">Saved Cards</a>
        </li>

  <?php
        } else {
    ?>

    <li class="nav-item">
    <a class="nav-link<?=(isset($_GET["accounts"]) && $_GET['accounts'] == 'SavedCards' ? " active" : "");?>"  href="<?=getLink("accounts", "SavedCards", "id", $_GET['id'])?>">Saved Cards</a>
    </li>


        <li class="nav-item">
            <a class="nav-link"  href="<?=getLink("accounts", "active")?>">Saved Cards</a>
        </li>

         <li class="nav-item">
            <a class="nav-link<?=(isset($_GET["accounts"]) && $_GET['accounts'] == 'edit' ? " active" : "");?>"  href="<?=getLink("accounts", "edit")?>">Add</a>
        </li>

<?php } ?>
    </ul>

    
</div>




