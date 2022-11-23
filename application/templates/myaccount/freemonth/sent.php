<? include(TPL_PATH."myaccount/menu.php");?>
<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?=getLink("freemonth")?>">&nbsp;Refer a Friend.</a>
</div>
<h3>Thank You</h3>
Your email has been sent.
<br /><br />
Thank you for referring a friend. You will see this referal on the "<a href="<?=getLink("freemonth", "referrals")?>">View Signed-Up Referrals</a>" list as soon as they follow the supplied link and complete the application.
<br /><br />
<!--<h3>Another Great Opportunity</h3>
Use the "Print Coupons" option to give a referral to each person you know who could benefit from using FreightDragon.
<br />
Earn a FREE Month for every company that becomes a paying member.-->
<br /><br />
<?php echo simpleButton("Refer more", getLink("freemonth")); ?>