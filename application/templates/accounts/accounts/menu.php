

<div class="alert alert-light alert-elevate" role="alert">
    <div class="row">
	<ul class="nav nav-tabs  nav-tabs-line nav-tabs-line-3x nav-tabs-line-success" role="tablist" style="margin-bottom: 0px">
<?php
	   if (!$_SESSION['member']['access_accounts']) 
	    {?>
		   <li  class="nav-item custom_set" ><a class="nav-link <?=(($_GET['accounts'] == 'shippers')?" active":""); ?>" href="<?=getLink("accounts", "shippers")?>">Shippers</a></li>
		<?php
        }
		else
		{
	  ?>
        <!-- Chetu put condition to satisfy privilege matching and showing menu accourdingly -->
        <li  class="nav-item custom_set"   ><a  class="nav-link<?=(($_GET['accounts'] == '')?" active":""); ?>" href="<?=getLink("accounts")?>">All Accounts</a></li>
        <!-- conditions added by chetu to show menus if privilege condition met -->
        <?php if($_SESSION['member']['access_carriers'] != 0): ?>
        <li  class="nav-item custom_set"  ><a class="nav-link <?=(($_GET['accounts'] == 'carriers' || $_GET['accounts'] == 'import')?" active":""); ?>"  href="<?=getLink("accounts", "carriers")?>">Carriers</a></li>
        <?php endif; ?>
        <li   class="nav-item custom_set" ><a class="nav-link <?=(($_GET['accounts'] == 'shippers')?" active":""); ?>"  href="<?=getLink("accounts", "shippers")?>">Shippers</a></li>
        <!-- conditions added by chetu to show menus if privilege condition met -->
        <?php if($_SESSION['member']['access_locations'] != 0): ?>
        <li class="nav-item custom_set" ><a  class="nav-link<?=(($_GET['accounts'] == 'locations')?" active":""); ?>" href="<?=getLink("accounts", "locations")?>">Locations</a></li>
        <?php endif; ?>
        <!-- Chetu commented the duplicate code  -->
        <?php /* ?><li class="tab <?=(($_GET['accounts'] == 'inactive')?" active":""); ?>"><a href="<?=getLink("accounts", "inactive")?>">Inactive</a></li> <?php */ ?>
        <?php if($_SESSION['member']['parent_id']==463){?>   
        <li class="nav-item custom_set" ><a   class="nav-link <?=(($_GET['accounts'] == 'inactive')?" active":""); ?>" href="<?=getLink("accounts", "inactive")?>">Inactive</a></li>
        <?php
            // Chetu added this code to implement privilege settings for 
            // duplicate carriers & shippers
            if($_SESSION['member']['access_duplicate_carriers']):
        ?>
        <li class="nav-item custom_set" ><a  class="nav-link <?=(($_GET['accounts'] == 'duplicateCarriers')?" active":""); ?>" href="<?=getLink("accounts", "duplicateCarriers")?>">Duplicate Carriers</a></li>
        <?php endif;?>
        <?php if($_SESSION['member']['access_duplicate_shippers']): ?>
        <li  class="nav-item custom_set"><a   class="nav-link<?=(($_GET['accounts'] == 'duplicateShippers')?" active":""); ?>" href="<?=getLink("accounts", "duplicateShippers")?>">Duplicate Shippers</a></li>
        <?php endif; ?>
        <?php }?>
  <?php }?>
	</ul>
   </div>
</div>




