<script language="javascript">
function formsubmit(plan)
{
	  $('#plan').val(plan);
	 $('#RegistrationForm').submit();
}

</script>
<link rel="stylesheet" type="text/css" href="styles/plans.css" >
<form name="RegistrationForm" id="RegistrationForm" action="<?= getLink("registration","planinfo") ?>" method="post">
<div>
<ul class="products-grid">
  <li class="item span4"><!-- span5 first -->
    <h3 class="product-name-bg"><a href="#" title="Economy ">Packages </a></h3>
    <div class="price-box-bg">
      <div class="price-box">
        <h3 > Each Additional <br>
          User Storage </h3>
      </div>
    </div>
    <div class="desc_grid1">
      <ul>
        <li><span>Leads Management test</span> </li>
        <li><span>Quotes Management</span> </li>
        <li><span>Orders Management </span> </li>
        <li><span>Reports & Accounts Payable </span> </li>
        <li><span>Customer, Carrier, Location Database</span></li>
        <li><span>Customer, Carrier, Location Database</span></li>
        <li><span>CDocuments & e-Sign Management </span></li>
        <li><span>Auto Quoting</span></li>
        <li><span>Internal Chat</span></li>
<?php 
$planType = $this->planType;
foreach($planType as $pType) {
?>       
        <li><span><input type="checkbox" name="plantype[]" value="<?php print $pType['id'];?>" />&nbsp;&nbsp;<b><?php print $pType['name'];?></b></span><BR /><?php print $pType['discription'];?></li>
        
<?php }?>        
      </ul>
    </div>
  <li class="item span4">
    <h3 class="product-name-bg"><a href="#" title="Ultimate">Economy</a></h3>
    <div class="price-box-bg">
      <div class="price-box">
        <h1> $150 </h1>
        <h2> $25 <br>
          500MB </h2>
      </div>
    </div>
    <div class="desc_grid">
      <ul>
        <li><span> <img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""> </span> </li>
        <li><span><img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""></span></li>
        <li><span><img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""></span> </li>
        <li><span><img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""></span> </li>
        <li><span><img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""></span> </li>
        <li><span><img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""></span> </li>
        <li><span><img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""></span> </li>
        <li><span><img src="<?php echo SITE_IN; ?>images/icons/cross.png" alt=""></span> </li>
        <li><span><img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""></span> </li>
        <li><span><img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""></span> </li>
        <li><span><img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""></span> </li>
        <li><span><img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""></span> </li>
      </ul>
    </div>
    <!--a class="btn-naw" href="<?= getLink("registration","planinfo","plan",1) ?>" title="Ultimate"><span>Select Plan</span></a> </li-->
     <?= functionButtonPlan("Select Plan","formsubmit(1)");?>
  <li class="item span4">
    <h3 class="product-name-bg"><a href="#" title="Ultimate">Delux</a></h3>
    <div class="price-box-bg">
      <div class="price-box">
        <h1> $199.99 </h1>
        <h2> $25 <br>
          5GB </h2>
      </div>
    </div>
    <div class="desc_grid">
      <ul>
        <li><span> <img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""> </span> </li>
        <li><span><img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""></span></li>
        <li><span><img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""></span> </li>
        <li><span><img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""></span> </li>
        <li><span><img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""></span> </li>
        <li><span><img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""></span> </li>
        <li><span><img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""></span> </li>
        <li><span><img src="<?php echo SITE_IN; ?>images/icons/cross.png" alt=""></span> </li>
        <li><span><img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""></span> </li>
        <li><span><img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""></span> </li>
        <li><span><img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""></span> </li>
        <li><span><img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""></span> </li>
      </ul>
    </div>
    <!--a class="btn-naw" href="<?= getLink("registration","planinfo","plan",2) ?>" title="Ultimate"><span>Select Plan</span></a> </li-->
     <?= functionButtonPlan("Select Plan","formsubmit(2)");?>
  <li class="item span4 last">
    <h3 class="product-name-bg"><a href="#" title="Deluxe">Ultimate</a></h3>
    <div class="price-box-bg">
      <div class="price-box">
        <h1> $399.97 </h1>
        <h2> $25 <br>
          10GB </h2>
      </div>
    </div>
    <div class="desc_grid">
      <ul>
        <li><span> <img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""> </span> </li>
        <li><span><img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""></span></li>
        <li><span><img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""></span> </li>
        <li><span><img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""></span> </li>
        <li><span><img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""></span> </li>
        <li><span><img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""></span> </li>
        <li><span><img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""></span> </li>
        <li><span><img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""></span> </li>
        <li><span><img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""></span> </li>
        <li><span><img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""></span> </li>
        <li><span><img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""></span> </li>
        <li><span><img src="<?php echo SITE_IN; ?>images/icons/check.png" alt=""></span> </li>
      </ul>
    </div>
    <!--a class="btn-naw" href="<?= getLink("registration","planinfo","plan",3) ?>" title="Deluxe"><span>Select Plan</span></a> </li-->
    <?= functionButtonPlan("Select Plan","formsubmit(3)");?>
    <input type="hidden" name="plan" id="plan" value="" />
</ul>
</div>
</form>



