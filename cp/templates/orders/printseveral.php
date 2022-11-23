<br /><br />

<div align="right" style="text-align: right;">
		<?= functionButton("Print", "$('#printme').printArea();") ?>
</div>
<br /><br />

<div id="printme">
 @invoices@
</div>
<?= backButton(getLink("reports", "sales")); ?>