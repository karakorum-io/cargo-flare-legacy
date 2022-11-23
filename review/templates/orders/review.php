<?php
/**************************************************************************************************
 * thankyou.php
 *
 * Version:		1.0
 * Date:		2017-03-06
 * Author:		Chetu Inc
 * CopyRight 2017       Freight Dragon 
 ***************************************************************************************************/
?>

<h2 style="text-align: center">Thank You!</h2>
<h3 style="text-align: center"><?php echo $this->review['message'];?></h3>
<?php 
$_SESSION['member_chmod'] = 0;
$_SESSION['member_id'] = 0;
?>
<script>
    $(document).ready(function(){
       $('#headerImage').hide();
    });
</script>