@flash_message@
 <?php $plan = (int)$_POST['plan']; 
  $ptype = $_POST['plantype'];
  $ptypeSize = sizeof($ptype);
?> 
<div id="registration_errors" class="msg-error" style="display: none;">

    <ul class="msg-list"></ul>

</div>

<form action="<?= getLink("registration","planinfo") ?>" method="post">

<div id="registration_form">


<div>
<?php

$PlanInfo = "";
if($plan == 1)
   $PlanInfo = "Economy $150"; 
elseif($plan == 2)
   $PlanInfo = "Delux $199.99";
elseif($plan == 3)
   $PlanInfo = "Ultimate $399.97";   
?>
    <table cellpadding="0" cellspacing="3" border="0" id="information_table">

        <tr>

            <td><b>Plan:</b></td><td><b><?= $PlanInfo ?></b></td>

        </tr>
<?php   
        $planType = $this->planType;
		if($ptypeSize>0){
			foreach($planType as $pType) {
				for($i=0;$i<$ptypeSize;$i++)
				{
					if($ptype[$i]==$pType['id'])
					{
						?>
                        <tr>
                              <td><b><?php print $pType['name'];?>:</b></td><td><?php print $pType['discription'];?></td>
                        </tr>
                        <?php
					}
				}
			}
		}
?>
        <tr>
          <td>@contactname@</td>

        </tr>

        <tr>

            <td>@companyname@</td>

        </tr>
         <tr>

            <td>@email@</td>

        </tr>
        <tr>

            <td>@phone@</td>

        </tr>
        <tr>

            <td>@mcnumber@</td>

        </tr>
        <tr>

            <td>@type@</td>

        </tr>

        <!--tr>

            <td>@address@</td>

        </tr>

        <tr>

            <td>@city@</td>

        </tr>

        <tr>

            <td>@state@</td>

        </tr>

        <tr>

            <td>@zip@</td>

        </tr-->
       <tr>

            <td>@message@</td>

        </tr>
    </table>

    <input type="hidden" name="plan" value="<?php print $plan;?>" />
    <?php
        for($i=0;$i<$ptypeSize;$i++)
		{
				?>
				<input type="hidden" name="plantype[]" value="<?php print $ptype[$i];?>" />
				<?php
		}
	?>			
   
    <div class="button-center">

        <?= submitButtons("",'Save'); ?>

    </div>

</div>

</div>

</form>