<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Dispatch Sheet</title>
    <link rel="shortcut icon" href="<?php echo SITE_IN ?>styles/favicon.ico" />
    <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/styles.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/application.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/default.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/jquery-ui.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/jquery.ui.timepicker.css"/>
    <script type="text/javascript">var BASE_PATH = '<?php echo SITE_IN ?>';</script>
    <script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false&language=en"></script>
    <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery-ui.js"></script>
    <script type="text/javascript" src="<?= SITE_IN ?>jscripts/ui.geo_autocomplete.js"></script>
    <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.maskMoney.js"></script>

    <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/signature_tool.css"/>
    <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.nimble.loader.js"></script>
    <!--script type="text/javascript" src="<?= SITE_IN ?>jscripts/raphael.js"></script-->
    <script type="text/javascript" src="<?= SITE_IN ?>jscripts/signature_tool.js"></script>
    <?php //include(ROOT_PATH . 'application/templates/signature_tool.php'); ?>
    <script type="text/javascript">
        signature_type = "dispatch_new";
    </script>

    <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/functions.js"></script>
    <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/application.js"></script>
    <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.maskedinput-1.3.min.js"></script>

<style>

.table_outer{ 
  /*background-color:#2C87B9;*/
  border:1px solid #2C87B9;
}
.table_inner td{ 
  /*background-color:#ffffff;*/
  border:1px solid #2C87B9;
  padding:5px !important;
  
}

.table_inner_less_padding td{ 
  /*background-color:#ffffff;*/
  border:1px solid #2C87B9;
  /*padding:5px !important;*/
  height:35px;
}

.dis_heading{ 
  color:#2C87B9; 
  font-size:18px; 
  font-weight:bold;
   
}
.dis_heading_small{ 
  color:#2C87B9; 
  font-size:13px; 
  font-weight:bold;
   
}

.dis_heading_vsmall{ 
  color:#2C87B9; 
  font-size:11px; 
  font-weight:bold;
   
}

.dis_column_text_small{ 
  font-size:13px; 
  font-weight:bold;
  padding-left:2px;
}
.dis_column_text{ 
  font-size:11px; 
  font-weight:bold;
  padding-left:5px;
}
.dis_large_red{ 
  font-size:20px; 
  font-weight:bold;
  padding-left:5px;
  color:#C00;
}

.dis_large_black1{ 
  font-size:18px; 
  font-weight:bold;
  padding-left:5px;
  color:#000000;
}

.dis_large_red_small{ 
  font-size:16px; 
  font-weight:bold;
  padding-left:5px;
  color:#C00;
}

.dis_large_black{ 
  font-size:20px; 
  font-weight:bold;
  padding-left:5px;
  color:#ffffff;
}

.dispatch_table td, .dispatch_table th {
    padding: 0;
}


</style>

</head>
<body>
<br/>

<div style="padding:20px;width:1050px;margin: 0 auto;" class="ui-dialog">
    <h3>Dispatch Sheet for Order #<?= $this->entity->getNumber() ?></h3>
    <script type="text/javascript" src="<?= SITE_IN ?>jscripts/application/orders/dispatch.js"></script>
    <div id="dispatch_sheet_history_dialog" style="display: none;"></div>
    <br/>
	<?php if ( $this->entity->status == Entity::STATUS_DISPATCHED) { ?>
	                     <!--em style="color:#006600">Thank you to sign Dispatch Sheet.</em>
						<br>
					    <em style="color:#006600">Dispatch Sheet has been accepted.</em>
						<br-->
                        <h3>Thank you for accepting <?php print $this->entity->getShipper()->company;?> Dispatch Sheet.</h3>
						<?php if ( isset($this->files) && count($this->files) ) { ?>
									<? foreach ($this->files as $file) { ?>
                                    <li id="file-<?= $file['id'] ?>">&nbsp;&nbsp;<a <?=strtolower($file['type'])=='pdf'?"target=\"_blank\"":""?> href="<?=getLink("order", "getdocs", "id", $file['id'])?>"><img src="<?= SITE_IN ?>images/icons/download_pdf.jpg" /></a>
                                    </li>
										<!--li id="file-<?= $file['id'] ?>">
											<?=$file['img']?>
											<a href="<?=getLink("order", "getdocs", "id", $file['id'])?>"><?=$file['name_original']?></a>
											(<?=$file['size_formated']?>)
											
											&nbsp;&nbsp;<a <?=strtolower($file['type'])=='pdf'?"target=\"_blank\"":""?> href="<?=getLink("order", "getdocs", "id", $file['id'])?>">View</a>
										</li-->
									<?php } ?>
								<?php }?>
								<br><br>
								<table width="100%" cellspacing ="1" cellpadding="1">
												   <tr>
												   <td>
													<?php //print $this->dispatch->getHtmlNew($this,'pdf');?>
                                                    <?php print $this->dispatch->getHtmlNew($this);?>
													</td>
													</tr>
							    </table>						
				    <?php } else{?>
    <div class="order-info1">
	  <table width="100%" cellspacing ="1" cellpadding="1" class="dispatch_table">
	   <tr>
	   <td>
        <?php //print $this->dispatch->getHtmlNew($this,'pdf');?>
        <?php print $this->dispatch->getHtmlNew($this);?>
		</td>
		</tr>
		<?php /*if ( $this->entity->status == Entity::STATUS_NOTSIGNED) { ?>
         
         <tr>
           <th colspan="2">Electronic Signature</th>
          </tr> 
        <?php }*/?>
		<tr><td>
	   
		    <?php if ( $this->entity->status == Entity::STATUS_NOTSIGNED) { ?>
            <div class="order-info">
			    <table style="margin: 10px auto;" bgcolor="#FFFFFF" cellpadding="2" cellspacing="2" width="70%">
                   <tr>
                        <td colspan="2">
                        
                        <div id="signature_tool">
                            <div class="type_selector">
                                <table cellpadding="5" cellspacing="5" border="0">
                                    <tr>
                                        <td><input type="radio" name="sign_type" value="text" id="sign_type_text" checked="checked"/></td>
                                        <td><label for="sign_type_text">Signature</label></td>
										<td colspan="4"> </td>
                                        <!--td><input type="radio" name="sign_type" value="draw" id="sign_type_draw"/></td>
                                        <td><label for="sign_type_draw">Draw Signature</label></td-->
                                    </tr>
                                    <tr>
                                        <td colspan="4">
                                            <div class="sign-controls" id="sign_draw_controls" style="display:none">
                                                <button id="signature-undo">Undo</button>
                                                <button id="signature-clear">Clear</button>
                                                <button id="signature-save-draw">Save</button>
                                                <button onclick="<?php print "rejectDispatchSheet(" . $this->dispatch->id . ")";?>">Reject</button>
                                                <div id="paper"></div>
                                                    <div id="sign-result">
                                                        <img src="#" alt="Signature">
                                                    </div>
                                                </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="sign-controls" id="sign_write_controls">
                                <table cellpadding="5" cellspacing="5" border="0" width="610">
                                     
                                    <tr>
                                        <td><label for="sign_name">Enter Your Name:&nbsp;</label></td>
                                        <td><input type="text" maxlength="100" size="50" style="width:250px;" id="sign_name" class="form-box-textfield latin"/></td>
                                        <td>
                                            
                                            
                                        </td>
                                    </tr>
                                    <tr>
                                      <td><label for="sign_name">Notes</label></td>
                                      <td >
                                         <textarea cols="50" rows="5" id="notes" ></textarea>
                                      </td>
                                    </tr>
                                    <tr>
                                      <td colspan="2" align="center">
                                        <button id="signature-save-text" style="margin: 0 10px;">ACCEPT DISPATCH</button>
                                            <button onclick="<?php print "rejectDispatchSheet(" . $this->dispatch->id . ")";?>">REJECT DISPATCH</button>
                                      </td>
                                    </tr>  
                                </table>
                            </div>
                            
                        </div>
                        </td>
                     </tr>
				    <tr>
					    <td>
						<input type="hidden" id="dispatchID" name="dispatchID" value="<?php print $this->dispatch->id;?>"/>
						<?php //print functionButton("Accept", "acceptDispatchSheet(" . $this->dispatch->id . ")"); ?></td>
					    <td><?php //print functionButton("Reject", "rejectDispatchSheet(" . $this->dispatch->id . ")"); ?></td>
				    </tr>
			    </table>
		    <?php } else { ?>
			    <?php if ($this->dispatch->rejected != "") { ?>
				 <div style="text-align: center">
				    <em style="color:#BB0000">Dispatch Sheet has been rejected.</em>
				</div>
			    <? } else { ?>
				 <div style="text-align: center">
				    <?php if ( $this->entity->status == Entity::STATUS_DISPATCHED) { ?>
					    <em style="color:#006600">Dispatch Sheet has been accepted.</em>
						<br>
						<?php //print_r($this->files);
						if ( isset($this->files) && count($this->files) ) { ?>
									<? foreach ($this->files as $file) { ?>
										<li id="file-<?= $file['id'] ?>">
											<?=$file['img']?>
											<a href="<?=getLink("order", "getdocs", "id", $file['id'])?>"><?=$file['name_original']?></a>
											(<?=$file['size_formated']?>)
											
											&nbsp;&nbsp;<a <?=strtolower($file['type'])=='pdf'?"target=\"_blank\"":""?> href="<?=getLink("order", "getdocs", "id", $file['id'])?>">View</a>
										</li>
									<?php } ?>
								<?php }?>
				    <?php } elseif ( $this->entity->status == Entity::STATUS_PICKEDUP) { ?>
					    <em style="color:#006600">Dispatch Sheet has been picked up.</em>
				    <?php } elseif ( $this->entity->status == Entity::STATUS_DELIVERED) { ?>
					    <em style="color:#006600">Dispatch Sheet has been delivered.</em>
				    <?php } elseif ( $this->entity->status == Entity::STATUS_ARCHIVED) { ?>
					    <em style="color:#006600">Dispatch Sheet has been archived.</em>
				    <? } else { ?>
					    <em style="color:#006600">Dispatch Sheet has been signed.</em>
				    <? } ?>
                    </div>
			    <? } ?>
		    <? } ?>
            </div>
	    </div>
    
	     </td></tr>
		</table>  
	</div>

	<?php } ?>

    <div class="clear"></div>
</div>
</body>
</html>