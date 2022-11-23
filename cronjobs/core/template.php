<?php
    /**
     * This is the template static email template maker class
     * 
     * @author Chetu Inc.
     * @version 1.0
     */

     class TemplateEngine {

        /**
         * This is the constructor function created to perorm functionality at the
         * instance load of this class
         */
        function __construct(){
            
        }

        /**
         * Create the html template for matched carreir email to carrier
         * 
         * @author Chetu Inc.
         * @return String $html
         */
        function matchCarrierTemplate($entity,$vehicles,$origin,$destination,$company){
            if($entity->ship_via == 1){
                $shipVia = "Open";
            } elseif ($entity->ship_via == 2) {
                $shipVia = "Enclosed";
            } else {
                $shipVia = "Driveaway";
            }

            $html = '<div id=":15w" class="a3s aXjCH m161b451fde4c01ea">
            <p>
                <span style="font-size:18px">
                    We currently have a new load ready to go on&nbsp;<strong>'.$entity->avail_pickup_date.'</strong> to be shipped via 
                    <strong><span style="color:#ff0000"> '.$shipVia.' </span></strong> carrier.
                </span>
            </p>
            <h2><strong>Vehicle Information:&nbsp;</strong><strong style="font-size:12px">'.count($vehicles).' Vehicle</strong></h2>
            <p></p>
            <table cellspacing="0" cellpadding="3" border="0" class="m_-5607436284902006068vehicle-table">
                <tbody>
                    <tr>
                        <th style="border-bottom:#38b 1px solid;border-top:#38b 1px solid;border-left:#38b 1px solid;background-color:#eeeeee;font-weight:bold;text-align:center;padding:3px 20px">Year</th>
                        <th style="border-bottom:#38b 1px solid;border-left:#38b 1px solid;border-top:#38b 1px solid;background-color:#eeeeee;font-weight:bold;text-align:center;padding:3px 20px">Make</th>
                        <th style="border-bottom:#38b 1px solid;border-left:#38b 1px solid;border-top:#38b 1px solid;background-color:#eeeeee;font-weight:bold;text-align:center;padding:3px 20px">Model</th>
                        <th style="border-bottom:#38b 1px solid;border-left:#38b 1px solid;border-top:#38b 1px solid;border-right:#38b 1px solid;background-color:#eeeeee;font-weight:bold;text-align:center;padding:3px 20px">Inop</th>
                    </tr>';
            foreach($vehicles as $v){
            $html .= '<tr>
                        <td style="border-bottom:#38b 1px solid;border-left:#38b 1px solid;background-color:#ffffff;text-align:left;padding:3px 5px">'.$v['year'].'</td>
                        <td style="border-bottom:#38b 1px solid;border-left:#38b 1px solid;background-color:#ffffff;text-align:left;padding:3px 5px">'.$v['make'].'</td>
                        <td style="border-bottom:#38b 1px solid;border-left:#38b 1px solid;background-color:#ffffff;text-align:left;padding:3px 5px">'.$v['model'].'</td>
                        <td style="border-bottom:#38b 1px solid;border-left:#38b 1px solid;border-right:#38b 1px solid;background-color:#ffffff;text-align:left;padding:3px 5px">'.(($v['inop']) ? 'Yes' : 'No').'</td>
                    </tr>';
            }
            $html .= '</tbody>
            </table>
            <p></p>
            <h1>Carrier Pay :<span style="color:#008000"> $ '.$entity->carrier_pay_stored.'</span></h1>
            <h2><strong>Route:&nbsp;</strong><strong style="font-size:12px">Estimated Mileage '.$entity->distance.' miles</strong></h2>
            <table width="100%">
                <tbody>
                    <tr>
                        <td width="50%">
                            <table width="50%" cellpadding="1" border="0" style="border:#38b 1px solid;background-color:#f4f4f4">
                                <tbody>
                                    <tr>
                                        <td colspan="3" align="left" style="border:#cccccc 1px solid;background-color:#cccccc;font-weight:bold;text-align:center;padding:3px 20px">
                                            <b>Pickup Information</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="line-height:15px" width="20%"><strong>City</strong></td><td width="4%" align="center">
                                            <b>:</b>
                                        </td>
                                        <td>'.$origin[0]['city'].'</td>
                                    </tr>
                                    <tr>
                                        <td style="line-height:15px">
                                            <strong>State</strong>
                                        </td>
                                        <td width="4%" align="center">
                                            <b>:</b>
                                        </td>
                                        <td>'.$origin[0]['state'].'</td>
                                    </tr>
                                    <tr> 
                                        <td style="line-height:15px">
                                            <strong>Zip</strong>
                                        </td>
                                        <td width="4%" align="center">
                                            <b>:</b>
                                        </td>
                                        <td>'.$origin[0]['zip'].'</td>
                                    </tr>                                    
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            <table width="50%" cellpadding="1" border="0" style="border:#38b 1px solid;background-color:#f4f4f4">
                                <tbody>
                                    <tr>
                                        <td colspan="3" align="left" style="border:#cccccc 1px solid;background-color:#cccccc;font-weight:bold;text-align:center;padding:3px 20px">
                                            <b>Delivery Information</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="line-height:15px" width="20%">
                                            <strong>City</strong>
                                        </td>
                                        <td width="4%" align="center">
                                            <b>:</b>
                                        </td>
                                        <td>'.$destination[0]['city'].'</td>
                                    </tr>
                                    <tr>
                                        <td style="line-height:15px">
                                            <strong>State</strong>
                                        </td>
                                        <td width="4%" align="center">
                                            <b>:</b>
                                        </td>
                                        <td>'.$destination[0]['state'].'</td>
                                    </tr>
                                    <tr>
                                        <td style="line-height:15px">
                                            <strong>Zip</strong>
                                        </td>
                                        <td width="4%" align="center">
                                            <b>:</b>
                                        </td>
                                        <td>'.$destination[0]['zip'].'</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
            <h2><span style="font-size:12px">Interested in this load?</span></h2>
            <p>Call now and refer to load ID <strong>'.$entity->prefix.'-'.$entity->number.'</strong></p>
            <h1><strong>'.$company[0]['phone'].'</strong></h1>
            <p>&nbsp;</p>
            <p><strong>Regards</strong></p>
            <p>'.$company[0]['companyname'].' dispatch team</p>
            <p><strong>Email :</strong> '.$company[0]['email'].'</p>
            <p>&nbsp;</p>
            <p><strong>Do not wants to recieve email </strong> <a href="https://freightdragon.com/order/carrierUnsubscribed/hash/'.$entity->hash.'" target="_blank" data-saferedirecturl="https://www.google.com/url?hl=en&amp;q=https://freightdragon.com/order/carrierUnsubscribed/hash/'.$entity->hash.'&amp;source=gmail&amp;ust=1519727842527000&amp;usg=AFQjCNGJQuRNJY_YoaGdN6CMD07GaFkd-w">click here</a></p>
            <p>&nbsp;</p>
            <div class="yj6qo"></div>
            <div class="adL"></div>
        </div>';
        return $html;
        }

     }
?>