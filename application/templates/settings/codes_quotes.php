<table cellspacing="0" cellpadding="0" width="100%">
<tr>
<td valign="top" style="padding-right:20px;">
<table cellspacing="0" cellpadding="0" class="grids" width="100%">
<tr>
    <th>Field</th>
    <th>Email Code</th>
</tr>
<tr>
    <td colspan="2"><h3>Special Fields</h3></td>
</tr>
<tr>
    <td width="180">Quote Link</td>
    <td>@quote_link@</td>
</tr>
<tr>
    <td>Note to Shipper</td>
    <td>@note_to_shipper@</td>
</tr>
<tr>
    <td colspan="2"><h3>Quote Information</h3></td>
</tr>
<tr>
    <td>Today's Date</td>
    <td>@today@</td>
</tr>
<tr>
    <td>Quote Number</td>
    <td>@quote_number@</td>
</tr>
<tr>
    <td>Date Created</td>
    <td>@date_created@</td>
</tr>
<tr>
    <td>Order Terms</td>
    <td>@order_terms@</td>
</tr>
<tr>
    <td colspan="2"><h3>Pricing Information</h3></td>
</tr>
<tr>
    <td>Tariff</td>
    <td>@tariff@</td>
</tr>
<tr>
    <td>Deposit Required</td>
    <td>@deposit_required@</td>
</tr>
<tr>
    <td>Current Balance Due</td>
    <td>@current_balance@</td>
</tr>
<tr>
    <td colspan="2"><h3>Shipper Contact</h3></td>
</tr>
<tr>
    <td>First Name</td>
    <td>@first_name@</td>
</tr>
<tr>
    <td>Last Name</td>
    <td>@last_name@</td>
</tr>
<tr>
    <td>Company Name</td>
    <td>@companyname@</td>
</tr>
<tr>
    <td>Email</td>
    <td>@email@</td>
</tr>
<tr>
    <td>Phone</td>
    <td>@phone@</td>
</tr>
<tr>
    <td>Phone 2</td>
    <td>@phone2@</td>
</tr>
<tr>
    <td>Phone (cell)</td>
    <td>@phone_cell@</td>
</tr>
<tr>
    <td>Phone (fax)</td>
    <td>@phone_fax@</td>
</tr>
<tr>
    <td colspan="2"><h3>Shipper Address</h3></td>
</tr>
<tr>
    <td>Address</td>
    <td>@address@</td>
</tr>
<tr>
    <td>Address 2</td>
    <td>@address2@</td>
</tr>
<tr>
    <td>City</td>
    <td>@city@</td>
</tr>
<tr>
    <td>State</td>
    <td>@state_code@</td>
</tr>
<tr>
    <td>Zip</td>
    <td>@zip@</td>
</tr>
<tr>
    <td>Country</td>
    <td>@country@</td>
</tr>
<tr>
    <td colspan="2"><h3>Vehicle Information (First vehicle only)</h3></td>
</tr>
<tr>
    <td>Vehicle Year</td>
    <td>@vehicle_year@</td>
</tr>
<tr>
    <td>Vehicle Make</td>
    <td>@vehicle_make@</td>
</tr>
<tr>
    <td>Vehicle Model</td>
    <td>@vehicle_model@</td>
</tr>
<tr>
    <td>Vehicle Type</td>
    <td>@vehicle_type_id@</td>
</tr>
<tr>
    <td>Vehicle Runs</td>
    <td>@vehicle_runs@</td>
</tr>
<tr>
    <td colspan="2"><h3>Multiple Vehicle Information</h3></td>
</tr>
<tr>
    <td>Vehicle List
        <span class="like-link viewsample">Sample</span>

        <div class="sample-info">
            <h3>Sample of @vehicle_list@</h3>
            <br/>
            Text: 2000 Chevy Tahoe, 2006 Chrysler LeBaron<br/>
            HTML: 2000 Chevy Tahoe, 2006 Chrysler LeBaron
            <br/><br/>
        </div></td>
    <td>@vehicle_list@</td>
</tr>
<tr>
    <td>Vehicle List (Formatted)
        <span class="like-link viewsample">Sample</span>

        <div class="sample-info">
            <h3>Sample of @vehicle_list_format@</h3>
            <br/>
            Text:<br/>
            2000 Chevy Tahoe<br/>
            2006 Chrysler LeBaron<br/>
            <br/>
            HTML:<br/>
            <table cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td>Year</td>
                    <td>Make</td>
                    <td>Model</td>
                </tr>
                <tr>
                    <td>2000</td>
                    <td>Chevy</td>
                    <td>Tahoe</td>
                </tr>
                <tr>
                    <td>2006</td>
                    <td>Chrysler</td>
                    <td>LeBaron</td>
                </tr>
            </table>
            <br/>
        </div></td>
    <td>@vehicle_list_format@</td>
</tr>
<tr>
    <td>Vehicle List + Price
        <span class="like-link viewsample">Sample</span>

        <div class="sample-info">
            <h3>Sample of @vehicle_list_price@</h3>
            <br/>
            Text: 2000 Chevy Tahoe ($1000.00/$150.00 dep.), 2006 Chrysler LeBaron ($1000.00/$150.00 dep.)<br/><br/>
            HTML: 2000 Chevy Tahoe ($1000.00/$150.00 dep.), 2006 Chrysler LeBaron ($1000.00/$150.00 dep.)
            <br/><br/>
        </div></td>
    <td>@vehicle_list_price@</td>
</tr>
<tr>
    <td>Vehicle List + Price (Formatted)
        <span class="like-link viewsample">Sample</span>

        <div class="sample-info">
            <h3>Sample of @vehicle_list_price_format@</h3>
            <br/>
            Text:<br/>
            2000 Chevy Tahoe ($1000.00/$150.00 dep.)<br/>
            2006 Chrysler 300 ($1000.00/$150.00 dep.)
            <br/><br/>
            HTML:
            <table cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td>Year</td>
                    <td>Make</td>
                    <td>Model</td>
                    <td>Price/Deposit</td>
                </tr>
                <tr>
                    <td>2000</td>
                    <td>Chevy</td>
                    <td>Tahoe</td>
                    <td>$1000.00/$150.00</td>
                </tr>
                <tr>
                    <td>2006</td>
                    <td>Chrysler</td>
                    <td>LeBaron</td>
                    <td>$1000.00/$150.00</td>
                </tr>
            </table>
            <br/>
        </div></td>
    <td>@vehicle_list_price_format@</td>
</tr>
<tr>
    <td>Vehicle Table (Formatted)</td>
    <td>@vehicle_table@</td>
</tr>
<tr>
    <td colspan="2"><h3>Shipping Information</h3></td>
</tr>
<tr>
    <td>Estimated Ship Date</td>
    <td>@estimated_ship_date@</td>
</tr>
<tr>
    <td>Ship Via Open/Enclosed</td>
    <td>@ship_via@</td>
</tr>
<tr>
    <td>Operable/Inop</td>
    <td>@operable_inop@</td>
</tr>
<tr>
    <td>Shipper Comment</td>
    <td>@shipper_comment@</td>
</tr>
<tr>
    <td colspan="2"><h3>Origin</h3></td>
</tr>
<tr>
    <td>City</td>
    <td>@origin_city@</td>
</tr>
<tr>
    <td>State/Province</td>
    <td>@origin_state_code@</td>
</tr>
<tr>
    <td>Zip</td>
    <td>@origin_zip@</td>
</tr>
<tr>
    <td>Country</td>
    <td>@origin_country@</td>
</tr>
<tr>
    <td colspan="2"><h3>Destination</h3></td>
</tr>
<tr>
    <td>City</td>
    <td>@destination_city@</td>
</tr>
<tr>
    <td>State/Province</td>
    <td>@destination_state_code@</td>
</tr>
<tr>
    <td>Zip</td>
    <td>@destination_zip@</td>
</tr>
<tr>
    <td>Country</td>
    <td>@destination_country@</td>
</tr>
</table>
</td>
<td valign="top">
    <table cellspacing="0" cellpadding="0" class="grids" width="100%">
        <tr>
            <th>Field</th>
            <th>Email Code</th>
        </tr>
        <tr>
            <td colspan="2"><h3>Company Information</h3></td>
        </tr>
        <tr>
            <td width="200">
                Company Name
            </td>
            <td>@company_name@
            </td>
        </tr>
        <tr>
            <td>Website
            </td>
            <td>@company_website@
            </td>
        </tr>
        <tr>
            <td>Description
            </td>
            <td>@company_description@
            </td>
        </tr>
        <tr>
            <td colspan="2"><h3>Contact Information</h3></td>
        </tr>
        <tr>
            <td>Owner
            </td>
            <td>@company_owner_name@
            </td>
        </tr>
        <tr>
            <td>Address 1
            </td>
            <td>@company_address1@
            </td>
        </tr>
        <tr>
            <td>Address 2
            </td>
            <td>@company_address2@
            </td>
        </tr>
        <tr>
            <td>City
            </td>
            <td>@company_city@
            </td>
        </tr>
        <tr>
            <td>State/Province
            </td>
            <td>@company_state_code@
            </td>
        </tr>
        <tr>
            <td>Zip
            </td>
            <td>@company_zip@
            </td>
        </tr>
        <tr>
            <td>Phone (local)
            </td>
            <td>@company_phone_local@
            </td>
        </tr>
        <tr>
            <td>Phone (toll-free)
            </td>
            <td>@company_phone_tollfree@
            </td>
        </tr>
        <tr>
            <td>Phone (cell)
            </td>
            <td>@company_phone_cell@
            </td>
        </tr>
        <tr>
            <td>Fax
            </td>
            <td>@company_phone_fax@
            </td>
        </tr>
        <tr>
            <td>Email
            </td>
            <td>@company_email@
            </td>
        </tr>
        <tr>
            <td colspan="2"><h3>Dispatch Information</h3></td>
        </tr>
        <tr>
            <td>Phone
            </td>
            <td>@company_phone_dispatch@
            </td>
        </tr>
        <tr>
            <td>Fax
            </td>
            <td>@company_dispatch_fax@
            </td>
        </tr>
        <tr>
            <td>Email
            </td>
            <td>@company_dispatch_email@
            </td>
        </tr>
        <tr>
            <td colspan="2"><h3>User Information</h3></td>
        </tr>
        <tr>
            <td>Name
            </td>
            <td>@u_name@
            </td>
        </tr>
        <tr>
            <td>Email
            </td>
            <td>@u_email@
            </td>
        </tr>
        <tr>
            <td>Phone
            </td>
            <td>@u_phone@
            </td>
        </tr>
    </table>
</td>
</tr>
</table>