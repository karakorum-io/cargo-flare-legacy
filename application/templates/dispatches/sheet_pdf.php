<table cellpadding="0" cellspacing="0" width="100%"  class="dispatch_table">
<tr>
                 <td  class="fake-th" valign="middle"    width="100%" height="35">
                   <span  style="width:100%;">COMPANY INFORMATION</span>
                  </td>
 </tr>
<tr>
 <td>
 <table cellpadding="0" cellspacing="0" width="100%"  >
                <tr><td colspan="2">&nbsp;</td></tr> 
               <tr>
                 <td  class="group" width="40%" valign="top" style="background-color:#ffffff;"  >
                    <table cellpadding="0" cellspacing="0"  width="420" >
                       
                        <tr><td>&nbsp;</td></tr>
                        <tr>
                         <td  class="group" style="padding-left:20px;" width="100%">
                           <span class="dis_column_text_small">@c_companyname@<br />@c_address1@<br />@c_city@,@c_state@,@c_zip_code@</span>
                         </td>
                        </tr>
                        
                     </table>    
                   
                 </td>
                 <td align="center">
                         <table cellpadding="0" cellspacing="0"  width="100%" >
                               <tr>
                                 <td  class="group" width="35%" align="left">
                                   <span class="dis_column_text_small">Dispatch ID</span>
                                 </td>
                                 <td  class="group" width="5%" align="center">
                                 <span class="dis_column_text_small">:</span>
                                 </td>
                                 <td  class="group" width="50%"  align="left">
                                   <span class="dis_column_text_small"><font color="black">@order_number@</font></span>
                                 </td>
                                </tr>
                                <tr>
                                 <td  class="group" width="35%" align="left">
                                   <span class="dis_column_text_small">Dispatch Contact</span>
                                 </td>
                                 <td  class="group" width="5%" align="center">
                                 <span class="dis_column_text_small">:</span>
                                 </td>
                                 <td  class="group" width="50%"  align="left">
                                   <span class="dis_column_text_small">@c_dispatch_contact@</span>
                                 </td>
                                </tr>
                                <tr>
                                 <td  class="group" width="35%" align="left">
                                   <span class="dis_column_text_small">Dispatch Phone</span>
                                 </td>
                                 <td  class="group" width="5%" align="center">
                                 <span class="dis_column_text_small">:</span>
                                 </td>
                                 <td  class="group" width="50%"  align="left">
                                   <span class="dis_column_text_small">@c_dispatch_phone@</span>
                                 </td>
                                </tr>
                                 <tr>
                                 <td  class="group" width="35%" align="left">
                                   <span class="dis_column_text_small">Dispatch Fax</span>
                                 </td>
                                 <td  class="group" width="5%" align="center">
                                 <span class="dis_column_text_small">:</span>
                                 </td>
                                 <td  class="group" width="50%"  align="left">
                                   <span class="dis_column_text_small">@c_dispatch_fax@</span>
                                 </td>
                                </tr>
                                <tr>
                                 <td  class="group" width="35%" align="left">
                                   <span class="dis_column_text_small">Accounting Fax</span>
                                 </td>
                                 <td  class="group" width="5%" align="center">
                                 <span class="dis_column_text_small">:</span>
                                 </td>
                                 <td  class="group" width="50%"  align="left">
                                   <span class="dis_column_text_small">@c_dispatch_accounting_fax@</span>
                                 </td>
                                </tr>
                                <tr>
                                 <td  class="group" width="35%" align="left">
                                   <span class="dis_column_text_small">Accounting Email</span>
                                 </td>
                                 <td  class="group" width="5%" align="center">
                                 <span class="dis_column_text_small">:</span>
                                 </td>
                                 <td  class="group" width="50%"  align="left">
                                   <span class="dis_column_text_small"></span>
                                 </td>
                                </tr>
                           </table>
                    </td>            
                 
                </tr> 
                
                <tr><td colspan="2">&nbsp;</td></tr>
  </table>
 </td>
</tr> 
<tr><td>&nbsp;</td></tr>
<tr>
                 <td  class="fake-th" valign="middle"   width="100%"  height="35">
                   <span  style="width:100%;">CARRIER INFORMATION</span>
                  </td>
                 </tr>
<tr>
 <td> 
  <table cellpadding="0" cellspacing="0" width="100%"  >
                
                 <tr><td colspan="2">&nbsp;</td></tr>  
               <tr>
                 <td  class="group" width="40%" valign="top" style="background-color:#ffffff;"  >
                    <table cellpadding="0" cellspacing="0"  width="420" >
                       <tr>
                         <td  class="group" align="center">
                           <?php //echo $this->c_logo_path ?>
                         </td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>
                        <tr>
                         <td  class="group" style="padding-left:20px;" width="100%">
                           <span class="dis_column_text_small">@carrier_company_name@</span>
						   <span class="dis_column_text"><br />@carrier_address@<br />@carrier_city@,@carrier_state@,@carrier_zip@</span>
                         </td>
                        </tr>
						<tr>
                         <td  class="group" style="padding-left:20px;" width="100%">
                           <span class="dis_column_text">Email: @carrier_email@</span>
                         </td>
                        </tr>
						<tr>
						   <td  class="group" style="padding-left:20px;" width="100%">
						   <span class="dis_column_text">Shipment Type: @ship_via@</span>
                         </td>
                        </tr>
						
                        
                     </table>    
                   
                 </td>
                 <td align="center">
                         <table cellpadding="0" cellspacing="0"  width="100%" >
                               <tr>
                                 <td  class="group" width="35%" align="left">
                                   <span class="dis_column_text_small">Carrier MC#</span>
                                 </td>
                                 <td  class="group" width="5%" align="center">
                                 <span class="dis_column_text_small">:</span>
                                 </td>
                                 <td  class="group" width="50%"  align="left">
                                   <span class="dis_column_text_small">@carrier_insurance_iccmcnumber@</span>
                                 </td>
                                </tr>
                                <tr>
                                 <td  class="group" width="35%" align="left">
                                   <span class="dis_column_text_small">Dispatch Contact</span>
                                 </td>
                                 <td  class="group" width="5%" align="center">
                                 <span class="dis_column_text_small">:</span>
                                 </td>
                                 <td  class="group" width="50%"  align="left">
                                   <span class="dis_column_text_small">@carrier_contact_name@</span>
                                 </td>
                                </tr>
                                <tr>
                                 <td  class="group" width="35%" align="left">
                                   <span class="dis_column_text_small">Dispatch Phone</span>
                                 </td>
                                 <td  class="group" width="5%" align="center">
                                 <span class="dis_column_text_small">:</span>
                                 </td>
                                 <td  class="group" width="50%"  align="left">
                                   <span class="dis_column_text_small">@carrier_phone_1@@carrier_phone_1_ext@</span>
                                 </td>
                                </tr>
                                 <tr>
                                 <td  class="group" width="35%" align="left">
                                   <span class="dis_column_text_small">Dispatch Fax</span>
                                 </td>
                                 <td  class="group" width="5%" align="center">
                                 <span class="dis_column_text_small">:</span>
                                 </td>
                                 <td  class="group" width="50%"  align="left">
                                   <span class="dis_column_text_small">@carrier_fax@</span>
                                 </td>
                                </tr>
                                <tr>
                                 <td  class="group" width="35%" align="left">
                                   <span class="dis_column_text_small">Driver's Name</span>
                                 </td>
                                 <td  class="group" width="5%" align="center">
                                 <span class="dis_column_text_small">:</span>
                                 </td>
                                 <td  class="group" width="50%"  align="left">
                                   <span class="dis_column_text_small">@carrier_driver_name@</span>
                                 </td>
                                </tr>
                                <tr>
                                 <td  class="group" width="35%" align="left">
                                   <span class="dis_column_text_small">Driver's Phone</span>
                                 </td>
                                 <td  class="group" width="5%" align="center">
                                 <span class="dis_column_text_small">:</span>
                                 </td>
                                 <td  class="group" width="50%"  align="left">
                                   <span class="dis_column_text_small">@carrier_driver_phone@</span>
                                 </td>
                                </tr>
                           </table>
                 </td>
                </tr> 
                <tr><td colspan="2">&nbsp;</td></tr>
                 
  </table>
 </td>
</tr> 

    <!----------------------->
<tr><td>&nbsp;</td></tr>
<tr>
                 <td  class="fake-th" valign="middle"   width="100%"  height="35">
                   <span  style="width:100%;">PICKUP INFORMATION</span>
                  </td>
                 </tr>
<tr>
 <td> 
  <table cellpadding="0" cellspacing="0" width="100%"  >
                
                 <tr><td colspan="2">&nbsp;</td></tr>  
               <tr>
                 <td  class="group" width="100%" valign="top" style="background-color:#ffffff;"  >
                    <table cellpadding="0" cellspacing="0"  width="750" >
                       <tr>
                             <td  class="group" width="18%" align="left">
                                       <span class="dis_column_text">Loading Date</span>
                             </td>
                             <td  class="group" width="2%" align="center">
                             <span class="dis_column_text">:</span>
                             </td>
                             <td  class="group"   align="left">
                               <span class="dis_column_text">@load_date_type@ @load_date@</span>
                             </td>
                        </tr>
                        <tr>
                             <td  class="group" width="18%" align="left">
                                       <span class="dis_column_text">Pickup Address</span>
                             </td>
                             <td  class="group" width="2%" align="center">
                             <span class="dis_column_text">:</span>
                             </td>
                             <td  class="group"  align="left">
                               <span class="dis_column_text">@from_address@ @from_address2@ ,@from_city@ @from_state@ @from_zip@ </span>
                             </td>
                        </tr>
                        <tr>
                             <td  class="group" width="18%" align="left">
                                       <span class="dis_column_text">Hours of Operations</span>
                             </td>
                             <td  class="group" width="2%" align="center">
                             <span class="dis_column_text">:</span>
                             </td>
                             <td  class="group"  align="left">
                               <span class="dis_column_text">@origin_hours@</span>
                             </td>
                        </tr>
                        
                     </table>    
                   
                 </td>
                 </tr>
                 <tr><td colspan="2">&nbsp;</td></tr>
                 <tr>
                  <td align="center">
                         <table cellpadding="0" cellspacing="0"  width="800" >
                               <tr>
                                 <td  class="group" width="15%" align="left">
                                   <span class="dis_column_text">Contact Name 1</span>
                                 </td>
                                 <td  class="group" width="2%" align="center">
                                 <span class="dis_column_text">:</span>
                                 </td>
                                 <td  class="group"  width="26%" align="left">
                                   <span class="dis_column_text">@from_name@</span>
                                 </td>
                                 
                                 <td  class="group" width="3%" align="center">&nbsp;</td>
                                 
                                 <td  class="group" width="15%" align="left">
                                   <span class="dis_column_text">Contact 1 Phone</span>
                                 </td>
                                 <td  class="group" width="2%" align="center">
                                 <span class="dis_column_text">:</span>
                                 </td>
                                 <td  class="group"  width="20%"  align="left">
                                   <span class="dis_column_text">@from_phone_1@@from_phone_1_ext@</span>
                                 </td>
                                 <td  class="group" width="3%" align="center">&nbsp;</td>
                                 <td  class="group" width="12%" align="left">
                                   <span class="dis_column_text">Mobile</span>
                                 </td>
                                 <td  class="group" width="2%" align="center">
                                 <span class="dis_column_text">:</span>
                                 </td>
                                 <td  class="group"   width="17%" align="left">
                                   <span class="dis_column_text">@from_phone_cell@</span>
                                 </td>
                                </tr>
                                
                                <tr>
                                 <td  class="group" width="15%" align="left">
                                   <span class="dis_column_text">Contact Name 2</span>
                                 </td>
                                 <td  class="group" width="2%" align="center">
                                 <span class="dis_column_text">:</span>
                                 </td>
                                 <td  class="group"   align="left">
                                   <span class="dis_column_text">@from_name2@</span>
                                 </td>
                                 
                                 <td  class="group" width="3%" align="center">&nbsp;</td>
                                 
                                 <td  class="group" width="15%" align="left">
                                   <span class="dis_column_text">Contact 2 Phone</span>
                                 </td>
                                 <td  class="group" width="2%" align="center">
                                 <span class="dis_column_text">:</span>
                                 </td>
                                 <td  class="group"   align="left">
                                   <span class="dis_column_text">@from_phone_2@@from_phone_2_ext@</span>
                                 </td>
                                 
                                 <td  class="group" width="3%" align="center">&nbsp;</td>
                                 
                                 <td  class="group" width="12%" align="left">
                                   <span class="dis_column_text">Mobile</span>
                                 </td>
                                 <td  class="group" width="2%" align="center">
                                 <span class="dis_column_text">:</span>
                                 </td>
                                 <td  class="group"   align="left">
                                   <span class="dis_column_text">@from_phone_cell2@</span>
                                 </td>
                                </tr>
                                
                                <tr>
                                 <td  class="group" width="15%" align="left">
                                   <span class="dis_column_text">Company Name</span>
                                 </td>
                                 <td  class="group" width="2%" align="center">
                                 <span class="dis_column_text">:</span>
                                 </td>
                                 <td  class="group"   align="left">
                                   <span class="dis_column_text">@from_company@</span>
                                 </td>
                                 
                                 <td  class="group" width="3%" align="center">&nbsp;</td>
                                 
                                 <td  class="group" width="15%" align="left">
                                   <span class="dis_column_text">Company Phone</span>
                                 </td>
                                 <td  class="group" width="2%" align="center">
                                 <span class="dis_column_text">:</span>
                                 </td>
                                 <td  class="group"   align="left">
                                   <span class="dis_column_text">@from_phone_3@@from_phone_3_ext@</span>
                                 </td>
                                 
                                 <td  class="group" width="3%" align="center">&nbsp;</td>
                                 
                                 <td  class="group" width="12%" align="left">
                                   <span class="dis_column_text">Fax</span>
                                 </td>
                                 <td  class="group" width="2%" align="center">
                                 <span class="dis_column_text">:</span>
                                 </td>
                                 <td  class="group"   align="left">
                                   <span class="dis_column_text">@from_fax@</span>
                                 </td>
                                </tr>
                                
                                <tr>
                                 <td  class="group" width="15%" align="left">
                                   <span class="dis_column_text">Auction Name</span>
                                 </td>
                                 <td  class="group" width="2%" align="center">
                                 <span class="dis_column_text">:</span>
                                 </td>
                                 <td  class="group"   align="left">
                                   <span class="dis_column_text">@from_auction_name@</span>
                                 </td>
                                 
                                 <td  class="group" width="3%" align="center">&nbsp;</td>
                                 
                                 <td  class="group" width="15%" align="left">
                                   <span class="dis_column_text">Auction Phone</span>
                                 </td>
                                 <td  class="group" width="2%" align="center">
                                 <span class="dis_column_text">:</span>
                                 </td>
                                 <td  class="group"   align="left">
                                   <span class="dis_column_text">@from_phone_4@@from_phone_4_ext@</span>
                                 </td>
                                 
                                 <td  class="group" width="3%" align="center">&nbsp;</td>
                                 
                                 <td  class="group" width="12%" align="left">
                                   <span class="dis_column_text">Fax</span>
                                 </td>
                                 <td  class="group" width="2%" align="center">
                                 <span class="dis_column_text">:</span>
                                 </td>
                                 <td  class="group"   align="left">
                                   <span class="dis_column_text">@from_fax2@</span>
                                 </td>
                                </tr>
                                
                                <tr>
                                 <td  class="group" width="15%" align="left">
                                   <span class="dis_column_text">Buyer Name</span>
                                 </td>
                                 <td  class="group" width="2%" align="center">
                                 <span class="dis_column_text">:</span>
                                 </td>
                                 <td  class="group"   align="left">
                                   <span class="dis_column_text">@from_buyer_number@</span>
                                 </td>
                                 
                                 <td  class="group" width="3%" align="center" colspan="8">&nbsp;</td>
                                </tr>
                                
                                <tr>
                                 <td  class="group" width="15%" align="left">
                                   <span class="dis_column_text">Booking Number</span>
                                 </td>
                                 <td  class="group" width="2%" align="center">
                                 <span class="dis_column_text">:</span>
                                 </td>
                                 <td  class="group"   align="left">
                                   <span class="dis_column_text">@from_booking_number@</span>
                                 </td>
                                 
                                 <td  class="group" width="3%" align="center" colspan="8">&nbsp;</td>
                                </tr>
                                
                           </table>
                 </td>
                </tr> 
                <tr><td colspan="2">&nbsp;</td></tr>
                
  </table>
 </td>
</tr> 


<tr>
                 <td  class="fake-th" valign="middle"   width="100%"  height="35">
                   <span  style="width:100%;">DELIVERY INFORMATION</span>
                  </td>
                 </tr>
<tr>
 <td> 
  <table cellpadding="0" cellspacing="0" width="100%"  >
                
                 <tr><td colspan="2">&nbsp;</td></tr>  
               <tr>
                 <td  class="group" width="100%" valign="top" style="background-color:#ffffff;"  >
                    <table cellpadding="0" cellspacing="0"  width="750" >
                       <tr>
                             <td  class="group" width="18%" align="left">
                                       <span class="dis_column_text">Delivery Date</span>
                             </td>
                             <td  class="group" width="2%" align="center">
                             <span class="dis_column_text">:</span>
                             </td>
                             <td  class="group"   align="left">
                               <span class="dis_column_text">@delivery_date_type@ @delivery_date@</span>
                             </td>
                        </tr>
                        <tr>
                             <td  class="group" width="18%" align="left">
                                       <span class="dis_column_text">Delivery Address</span>
                             </td>
                             <td  class="group" width="2%" align="center">
                             <span class="dis_column_text">:</span>
                             </td>
                             <td  class="group"  align="left">
                               <span class="dis_column_text">@to_address@ @to_address2@ ,@to_city@ @to_state@ @to_zip@ </span>
                             </td>
                        </tr>
                        <tr>
                             <td  class="group" width="18%" align="left">
                                       <span class="dis_column_text">Hours of Operations</span>
                             </td>
                             <td  class="group" width="2%" align="center">
                             <span class="dis_column_text">:</span>
                             </td>
                             <td  class="group"  align="left">
                               <span class="dis_column_text">@destination_hours@</span>
                             </td>
                        </tr>
                        
                     </table>    
                   
                 </td>
                 </tr>
                 <tr><td colspan="2">&nbsp;</td></tr>
                 <tr>
                  <td align="center">
                         <table cellpadding="0" cellspacing="0"  width="800" >
                               <tr>
                                 <td  class="group" width="15%" align="left">
                                   <span class="dis_column_text">Contact Name 1</span>
                                 </td>
                                 <td  class="group" width="2%" align="center">
                                 <span class="dis_column_text">:</span>
                                 </td>
                                 <td  class="group"  width="26%" align="left">
                                   <span class="dis_column_text">@to_name@</span>
                                 </td>
                                 
                                 <td  class="group" width="3%" align="center">&nbsp;</td>
                                 
                                 <td  class="group" width="15%" align="left">
                                   <span class="dis_column_text">Contact 1 Phone</span>
                                 </td>
                                 <td  class="group" width="2%" align="center">
                                 <span class="dis_column_text">:</span>
                                 </td>
                                 <td  class="group"  width="20%"  align="left">
                                   <span class="dis_column_text">@to_phone_1@@to_phone_1_ext@</span>
                                 </td>
                                 <td  class="group" width="3%" align="center">&nbsp;</td>
                                 <td  class="group" width="12%" align="left">
                                   <span class="dis_column_text">Mobile</span>
                                 </td>
                                 <td  class="group" width="2%" align="center">
                                 <span class="dis_column_text">:</span>
                                 </td>
                                 <td  class="group"   width="17%" align="left">
                                   <span class="dis_column_text">@to_phone_cell@</span>
                                 </td>
                                </tr>
                                
                                <tr>
                                 <td  class="group" width="15%" align="left">
                                   <span class="dis_column_text">Contact Name 2</span>
                                 </td>
                                 <td  class="group" width="2%" align="center">
                                 <span class="dis_column_text">:</span>
                                 </td>
                                 <td  class="group"   align="left">
                                   <span class="dis_column_text">@to_name2@</span>
                                 </td>
                                 
                                 <td  class="group" width="3%" align="center">&nbsp;</td>
                                 
                                 <td  class="group" width="15%" align="left">
                                   <span class="dis_column_text">Contact 2 Phone</span>
                                 </td>
                                 <td  class="group" width="2%" align="center">
                                 <span class="dis_column_text">:</span>
                                 </td>
                                 <td  class="group"   align="left">
                                   <span class="dis_column_text">@to_phone_2@@to_phone_2_ext@</span>
                                 </td>
                                 
                                 <td  class="group" width="3%" align="center">&nbsp;</td>
                                 
                                 <td  class="group" width="12%" align="left">
                                   <span class="dis_column_text">Mobile</span>
                                 </td>
                                 <td  class="group" width="2%" align="center">
                                 <span class="dis_column_text">:</span>
                                 </td>
                                 <td  class="group"   align="left">
                                   <span class="dis_column_text">@to_phone_cell2@</span>
                                 </td>
                                </tr>
                                
                                <tr>
                                 <td  class="group" width="15%" align="left">
                                   <span class="dis_column_text">Company Name</span>
                                 </td>
                                 <td  class="group" width="2%" align="center">
                                 <span class="dis_column_text">:</span>
                                 </td>
                                 <td  class="group"   align="left">
                                   <span class="dis_column_text">@to_company@</span>
                                 </td>
                                 
                                 <td  class="group" width="3%" align="center">&nbsp;</td>
                                 
                                 <td  class="group" width="15%" align="left">
                                   <span class="dis_column_text">Company Phone</span>
                                 </td>
                                 <td  class="group" width="2%" align="center">
                                 <span class="dis_column_text">:</span>
                                 </td>
                                 <td  class="group"   align="left">
                                   <span class="dis_column_text">@to_phone_3@@to_phone_3_ext@</span>
                                 </td>
                                 
                                 <td  class="group" width="3%" align="center">&nbsp;</td>
                                 
                                 <td  class="group" width="12%" align="left">
                                   <span class="dis_column_text">Fax</span>
                                 </td>
                                 <td  class="group" width="2%" align="center">
                                 <span class="dis_column_text">:</span>
                                 </td>
                                 <td  class="group"   align="left">
                                   <span class="dis_column_text">@to_fax@</span>
                                 </td>
                                </tr>
                                
                                <tr>
                                 <td  class="group" width="15%" align="left">
                                   <span class="dis_column_text">Auction Name</span>
                                 </td>
                                 <td  class="group" width="2%" align="center">
                                 <span class="dis_column_text">:</span>
                                 </td>
                                 <td  class="group"   align="left">
                                   <span class="dis_column_text">@to_auction_name@</span>
                                 </td>
                                 
                                 <td  class="group" width="3%" align="center">&nbsp;</td>
                                 
                                 <td  class="group" width="15%" align="left">
                                   <span class="dis_column_text">Auction Phone</span>
                                 </td>
                                 <td  class="group" width="2%" align="center">
                                 <span class="dis_column_text">:</span>
                                 </td>
                                 <td  class="group"   align="left">
                                   <span class="dis_column_text">@to_phone_4@@to_phone_4_ext@</span>
                                 </td>
                                 
                                 <td  class="group" width="3%" align="center">&nbsp;</td>
                                 
                                 <td  class="group" width="12%" align="left">
                                   <span class="dis_column_text">Fax</span>
                                 </td>
                                 <td  class="group" width="2%" align="center">
                                 <span class="dis_column_text">:</span>
                                 </td>
                                 <td  class="group"   align="left">
                                   <span class="dis_column_text">@to_fax2@</span>
                                 </td>
                                </tr>
                                
                                <tr>
                                 <td  class="group" width="15%" align="left">
                                   <span class="dis_column_text">Buyer Name</span>
                                 </td>
                                 <td  class="group" width="2%" align="center">
                                 <span class="dis_column_text">:</span>
                                 </td>
                                 <td  class="group"   align="left">
                                   <span class="dis_column_text">@to_buyer_number@</span>
                                 </td>
                                 
                                 <td  class="group" width="3%" align="center" colspan="8">&nbsp;</td>
                                </tr>
                                
                                <tr>
                                 <td  class="group" width="15%" align="left">
                                   <span class="dis_column_text">Booking Number</span>
                                 </td>
                                 <td  class="group" width="2%" align="center">
                                 <span class="dis_column_text">:</span>
                                 </td>
                                 <td  class="group"   align="left">
                                   <span class="dis_column_text">@to_booking_number@</span>
                                 </td>
                                 
                                 <td  class="group" width="3%" align="center" colspan="8">&nbsp;</td>
                                </tr>
                                
                           </table>
                 </td>
                </tr> 
                <tr><td colspan="2">&nbsp;</td></tr>
                
  </table>
 </td>
</tr> 

    <!------------------------>
<tr>
     <td  class="fake-th" valign="middle"   width="100%"  height="35">
       <span  style="width:100%;"></span>
      </td>
     </tr>  
        
       <tr><td></td></tr>
        
        <tr>
         <td  bgcolor="#ffffff" style="border-bottom:2px solid #2C87B9;">
            
                 <table cellpadding="5" cellspacing="5"   width="800" style="background-color:#ffffff;">
                 <tr><td >&nbsp;</td></tr>
                 
                   <tr>
                    <td  class="group" >
                    
                            <span class="dis_large_red">Disclaimer: </span>
                            <span class="dis_column_text_middle">@c_companyname@ has authorized @carrier_company_name@ to transport Dispatch ID <font color="black">@order_number@</font>. This dispatch sheet is not authorized to be used as a Bill of Lading. If you have any questions please
contact @c_companyname@ at your earliest convenience @c_dispatch_phone@.</span>
                     </td>
                    </tr>
                    <tr><td >&nbsp;</td></tr>
                  
                  </table>
                   
         </td>
        </tr>
        
        <tr>
                 <td  class="fake-th" valign="middle"    width="100%"  height="35">
                   <span  style="width:100%;">Vehicle(s) Information</span>
                  </td>
                 </tr>  
                 <tr><td>&nbsp;</td></tr> 
        <tr>
                 <td style="font-size:16px;" width="100%" align="center">
            
                   @vehicles@
                
         </td>
        </tr>
        
        
        <tr><td>&nbsp;</td></tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
                 <td  class="fake-th" valign="middle"    width="100%"  height="35">
                   <span  style="width:100%;">Carrier Payment Terms</span>
                  </td>
                 </tr> 
        <tr>
         <td style="height:150px;" bgcolor="#ffffff">
            
                 <table cellpadding="5" cellspacing="5"   width="800" style="background-color:#ffffff;">
                 <tr><td >&nbsp;</td></tr>
                 <tr><td >&nbsp;</td></tr>
                   <tr>
                    <td  class="group" >
                    
                            <span class="dis_large_red">Carrier Payment Terms: </span><span class="dis_column_text_small">@payments_terms@<br />@text_paid_by@</span>
                     </td>
                    </tr>
                    <tr><td >&nbsp;</td></tr>
                    <tr> 
                     <td>
                        
                             &nbsp;&nbsp;&nbsp;&nbsp;<span class="dis_column_text_small">@text_paid_by_next@</span>
                      </td>
                              
                          
                    </tr> 
                    <tr><td >&nbsp;</td></tr>
                  </table>
                   
         </td>
        </tr>
        
        <tr><td></td></tr> 
        
        <tr>
                 <td  class="fake-th"  valign="middle"    width="100%"  height="35">
                   <span  style="width:100%;">Special Dispatch Instruction</span>
                  </td>
                 </tr>
       <tr><td >&nbsp;</td></tr>
       
        <tr>
         <td >
            <table cellpadding="0" cellspacing="0"    width="100%"  style="background-color:#ffffff;">
            <tr><td >&nbsp;</td></tr>
               <tr>
                 <td style="height:330px;">
                  
                 <p><span class="dis_column_text_middle">@instructions@</span></p></td>
                </tr> 
                <tr><td >&nbsp;</td></tr>
              </table>
         </td>
        </tr>
        <tr><td></td></tr> 
 </table> 
 <div style="width:100%;border-left:1px solid #2C87B9;border-right:1px solid #2C87B9;border-bottom:1px solid #2C87B9;">
<div class="fake-td">
<br/><br/>
    Authority to transport this vehicle is hereby assigned to <strong>@carrier_company_name@</strong>.
    By accepting this agreement <strong>@carrier_company_name@</strong> certifies that they have the proper legal authority and insurance to carry the above described vehicle, only on trucks owned by <strong>@carrier_company_name@</strong>.
    All invoices must be accompanied by a signed delivery receipt and faxed to <strong>@c_companyname@</strong>.
    The above agreed upon price includes any and all surcharges.
    <br/><br/>
    Notwithstanding anything to the contrary, the agreement between <strong>@carrier_company_name@</strong> and <strong>@c_companyname@</strong>, as described in this dispatch sheet, is solely between <strong>@carrier_company_name@</strong> and <strong>@c_companyname@</strong>. <strong>FreightDragon.com</strong> is not a party to such agreement, has no obligation under such agreement and expressly disclaims all liability whatsoever arising out of, or in connection with such agreement.
</div>
<br /><br/>
<div class="fake-th">DISPATCH TERMS AND CONDITIONS </div>
<div class="fake-td">@dispatch_terms@</div>
<!--div style="height:393px;"></div-->

<?php //echo $this->signature ?>
</div>