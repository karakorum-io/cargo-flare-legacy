<? include(TPL_PATH . "myaccount/menu.php");?>

<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?=getLink("billing")?>">&nbsp;Back to the list</a>
</div>
<form action="<?=getLink("billing", "sms")?>" method="post">
    
    <?= formBoxStart("Assign Numbers To") ?>
        <table cellspacing="5" cellpadding="5" border="0" width="80%">
       
            <tr>
                <td valign="top">Assign to: &nbsp;&nbsp;<img src="<?= SITE_IN ?>images/icons/assign.png" width="16" height="16" alt="Assign" style="vertical-align:middle;" /></td>
                <td valign="top">
                   <input type="hidden" name="assign_type" value="distribute" />
                    <div style="max-height:400px; width:300px; overflow: auto; border: 1px solid #ccc; background-color: #f1f1f1; padding: 8px;">
                        <? if (!empty($this->daffny->tpl->assigns)) { ?>
                            <table cellpadding="3" cellspacing="0" border="0">
                                <tr>
                                    <td width="20" align="center">&nbsp;</td>
                                    <td align="center">User</td>
                                    
                                </tr>
                                <? foreach ($this->daffny->tpl->assigns as $key => $a) { ?>
                                    <tr>
                                        <td width="20" align="center"><input type="checkbox" id="assigns<?= $a['id'] ?>" name="assigns[<?= $a['id'] ?>]" value="<?= $a['id'] ?>" <?= $a['ch'] ?> /></td>
                                        <td><label for="assigns<?= $a['id'] ?>"><?= htmlspecialchars($a['contactname']); ?></label></td>
                                        
                                    </tr>
                                <? } ?>
                            </table>
                        <?
                        } else {
                            echo "No users.";
                        }
                        ?>
                    </div>
                </td>
                <td  valign="top">
                  <table cellspacing="5" cellpadding="5" border="0" width="100%">
                    <tr>
                        <td colspan="6">
                        <?= formBoxStart("My current license") ?>
                         <table cellspacing="5" cellpadding="5" border="0" width="100%">
                             <tr >
                                   <td valign="top">@area_code@</td>
                                   <td valign="top">@state@</td>
                              </tr>  
                              <tr><td valign="top" colspan="6" align="center">&nbsp; </td></tr>   
                              <tr >
                                   <td valign="top" colspan="6" align="center"><span id="submit_button-submit-btn" style="-webkit-user-select: none;"><input type="submit" id="submit_button" value="Search Available Numbers"  onclick="showNumbers();" style="-webkit-user-select: none;">
                                   <input type="hidden" name="submit" value="shownumbers"  />
                                   </span></td>
                               </tr>  
                            </table>
                            <?= formBoxEnd() ?>
                          </td>
                         </tr>       
                       <tr><td valign="top" colspan="6" align="center">&nbsp; </td></tr>
                       
                        <? if (!empty($this->daffny->tpl->phoneNumbers)) { ?>
                       <tr><td valign="top" colspan="6" align="left">List of phone numbers to purchase</td></tr>
                       
                         <tr>
                           <td valign="top" colspan="6" align="left">  
                           <div style="max-height:400px; width:100%; overflow: auto; border: 1px solid #ccc; background-color: #f1f1f1; padding: 8px;">
                            <table cellpadding="3" cellspacing="3" border="0" width="100%">
                                <tr>
                                    <td width="20" align="center">&nbsp;</td>
                                    <td align="center">Phone Numbers</td>
                                    <td align="center">Credits</td>
                                </tr>
                                <? 
								
								//print_r($this->daffny->tpl->creditsSelected);
								  $phoneNumbersSelected = $this->daffny->tpl->phoneNumbersSelected;
								  $creditsSelected = $this->daffny->tpl->creditsSelected;
								  
								  foreach ($this->daffny->tpl->phoneNumbers as $key => $value) {
									  $selected = "";
								     if(in_array($value->number,$phoneNumbersSelected))
									    $selected = " checked=checked";
								?>
                                    <tr>
                                        <td width="20" align="center"><input type="checkbox" id="phonenumbers<?= $value->number ?>" name="phonenumbers['<?= $value->number ?>']" value="<?= $value->number ?>" <?= $selected  ?> /></td>
                                        <td><label for="phonenumbers<?= $value->number ?>"><?= htmlspecialchars($value->number); ?></label></td>
                                        <td align="center"><input type="text" id="credits<?= $value->number ?>" name="credits['<?= $value->number ?>']" value="<?php print $creditsSelected["'".$value->number."'"];?>"  size="10" /></td>
                                    </tr>
                                <? } ?>
                                
                                
                            </table>
                            </div>
                        </td>
                       </tr> 
                       <tr><td valign="top" colspan="6" align="center">&nbsp; </td></tr>   
                                  <tr >
                                       <td valign="top" colspan="6" align="center"><span id="submit_button-submit-btn" style="-webkit-user-select: none;"><input type="submit" id="select_button" value="Submit"  onclick="showNumbers();" style="-webkit-user-select: none;">
                                       <input type="hidden" name="submit1" value="selectnumbers"  />
                                       </span></td>
                                   </tr>  
                        <?
                        } else {
                           ?>
						   <tr><td valign="top" colspan="6" align="center">&nbsp;</td></tr>
						   <tr><td valign="top" colspan="6" align="center">Phone Numbers Not Found. </td></tr>
                           <tr><td valign="top" colspan="6" align="center">&nbsp;</td></tr>
                            <?
                        }
                        ?>
                       
                  </table>
                </td>
            </tr>
            
            
        </table>
        <?= formBoxEnd() ?>
        <br />
    
</form>