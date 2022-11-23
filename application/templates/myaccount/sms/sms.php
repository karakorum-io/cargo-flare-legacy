<? include(TPL_PATH . "myaccount/menu.php");?>

<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?=getLink("billing")?>">&nbsp;Back to the list</a>
</div>

    
    <?= formBoxStart("Assign Numbers To") ?>
        <table cellspacing="5" cellpadding="5" border="0" width="60%">
       
            <tr>
                <td valign="top">&nbsp;</td>
                
                <td  valign="top">
                  <table cellspacing="5" cellpadding="5" border="0" width="100%">
                    <tr>
                        <td colspan="6">
                        <form action="<?=getLink("billing", "sms")?>" method="post">
                        <?= formBoxStart("Search SMS") ?>
                         <table cellspacing="5" cellpadding="5" border="0" width="100%">
                             <tr >
                                   <td valign="top">@area_code@</td>
                                   <td valign="top">@state@</td>
                              </tr>  
                              <tr><td valign="top" colspan="6" align="center">&nbsp; </td></tr>   
                              <tr >
                                   <td valign="top" colspan="6" align="center"><span id="submit_button-submit-btn" style="-webkit-user-select: none;"><!--input type="submit" id="submit_button" value="Search Available Numbers"  onclick="showNumbers();" style="-webkit-user-select: none;"-->
                                   <input type="hidden" name="submit1" value="shownumbers"  />
                                       
                                   <?= submitButtons( "",  "Search Numbers", "submit_button", "submit", "")?>
                                   </span></td>
                               </tr>  
                            </table>
                            
                            <?= formBoxEnd() ?>
                            </form>
                          </td>
                         </tr>       
                       <tr><td valign="top" colspan="6" align="center">&nbsp; </td></tr>
                       
                        <? if (!empty($this->daffny->tpl->phoneNumbers)) { ?>
                       <tr><td valign="top" colspan="6" align="left">List of phone numbers to purchase</td></tr>
                       
                         <tr><td valign="top" colspan="6" align="left">
                            <form action="<?=getLink("billing", "sms")?>" method="post">
                             <table cellspacing="5" cellpadding="5" border="0" width="100%">
                             <tr>
                               <td valign="top" colspan="2" align="left">Credits</td>
                               <td colspan="4">
                                         <select name="credit" id="credit">
                                               <option value="">--Select Credit--</option>
                                                     <? for($i=10;$i<=100;$i=$i+10) { 
                                                               $selected = "";
                                                               //if($usersSelected["'".$value->number."'"] == $a['id'])
                                                                  // $selected = " selected=selected ";
                                                          ?>
                                                                 <option value="<?= $i ?>" <?= $selected ?>>$ <?= $i; ?></option>
                                                          <? }  ?>
                                           </select>               
                               </td>
                            </tr>
                             <tr>
                               <td valign="top" colspan="6" align="left">
                                 
                               <div style="max-height:400px; width:100%; overflow: auto; border: 1px solid #ccc; background-color: #f1f1f1; padding: 8px;">
                               
                                <table cellpadding="3" cellspacing="3" border="0" width="100%">
                                    <tr>
                                        <td width="20" align="center">&nbsp;</td>
                                        <td align="center">Phone Numbers</td>
                                        <td align="center">Users</td>
                                    </tr>
                                    <? 
                                    
                                    //print_r($this->daffny->tpl->creditsSelected);
                                      $phoneNumbersSelected = $this->daffny->tpl->phoneNumbersSelected;
                                      $usersSelected = $this->daffny->tpl->usersSelected;
                                      
                                      foreach ($this->daffny->tpl->phoneNumbers as $key => $value) {
                                          $selected = "";
                                         if(in_array($value->number,$phoneNumbersSelected))
                                            $selected = " checked=checked";
                                    ?>
                                        <tr>
                                            <td width="20" align="center"><input type="checkbox" id="phonenumbers<?= $value->number ?>" name="phonenumbers['<?= $value->number ?>']" value="<?= $value->number ?>" <?= $selected  ?> /></td>
                                            <td><label for="phonenumbers<?= $value->number ?>"><?= htmlspecialchars($value->number); ?></label></td>
                                            <td align="center"><!--input type="text" id="credits<?= $value->number ?>" name="credits['<?= $value->number ?>']" value="<?php print $usersSelected["'".$value->number."'"];?>"  size="10" /-->
                                            <select name="users['<?= $value->number ?>']" id="users<?= $value->number ?>">
                                                
                                            <? if (!empty($this->daffny->tpl->assigns)) { ?>
                                                <option value="">--Select User--</option>
                                                      <? foreach ($this->daffny->tpl->assigns as $key => $a) { 
                                                           $selected = "";
                                                           if($usersSelected["'".$value->number."'"] == $a['id'])
                                                               $selected = " selected=selected ";
                                                      ?>
                                                             <option value="<?= $a['id'] ?>" <?= $selected ?>><?= htmlspecialchars($a['contactname']); ?></option>
                                                      <? } 
                                                } else {
                                                    ?>
                                                    <option value="">--No User--</option>
                                                    <?php
                                                }
                                                ?>
                                              </select>
                                            </td>
                                        </tr>
                                    <? } ?>
                                    
                                    
                                </table>
                                 
                                </div>
                            </td>
                           </tr> 
                           <tr><td valign="top" colspan="6" align="center">&nbsp; </td></tr>   
                                      <tr >
                                           <td valign="top" colspan="6" align="center"><span id="submit_button-submit-btn" style="-webkit-user-select: none;"><!--input type="submit" id="select_button" value="Submit"  onclick="showNumbers();" style="-webkit-user-select: none;"-->
                                           <input type="hidden" name="submit1" value="selectnumbers"  />
                                           <?= submitButtons( "",  "Submit", "submit_button", "submit", "")?>
                                           </span></td>
                                       </tr>
                             </table>
                             </form>  
                             </td>
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
    
