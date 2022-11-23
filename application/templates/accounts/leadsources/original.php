<? include(TPL_PATH . "accounts/leadsources/menu_details.php"); ?>
<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
    <img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?= getLink("leadsources") ?>">&nbsp;Back to the list</a>
</div>
Either we do not yet work with <strong>@domain@</strong> , or they have a few different email formats. We'll need to see what the email you get from them looks like.
<br />
<br />
<form action="<?= getLink("leadsources", "original", "id", get_var("id")) ?>" method="post">
    <?= formBoxStart("Original Email") ?>
    <table cellpadding="0" cellspacing="10" border="0">
        <tr>
            <td valign="top">@original_email@</td>
            <td valign="top" style="padding-left:20px;">
                Example:<br /><br />
                <em>
                    Shipper First Name: John<br />
                    Shipper Last Name: Doe<br />
                    Shipper Email: test@example.com<br />
                    Shipper Phone 1: 808-000-0000<br />
                    Shipper Phone 2: 954-000-0000<br />
                    Shipper Fax: 000-000-0000<br />
                    Shipper Address: 101 Heron Bay<br />
                    Shipper City: Coral Springs<br />
                    Shipper State: FL<br />
                    Shipper Zip: 33076<br />
                    Shipper Country: US<br />

                    VEHICLE #1 INFORMATION<br />
                    Year: 2004<br />
                    Make: Mercedes-Benz<br />
                    Model: C-Class<br />
                    VIN: 002120211<br />
                    Lot: 4234004<br />
                    Plate:<br />
                    Color: red<br />
                    Vehicle Type: sedan<br />
                    Does the vehicle run?: Yes<br />

                    VEHICLE #2 INFORMATION<br />
                    Year2: 2001<br />
                    Make2: Volvo<br />
                    Model2: C-Class<br />
                    VIN2: 00012121<br />
                    Lot2: 444654<br />
                    Plate2: <br />
                    Color2: green<br />
                    Vehicle Type2: van<br />
                    Does the vehicle run2?: No<br />

                    PICKUP AND DELIVERY INFORMATION<br />
                    Pickup City: wahiawa<br />
                    Pickup State: HI<br />
                    Pickup Zip: 96786<br />
                    Pickup Country:<br /> 

                    Delivery City: el paso<br />
                    Delivery State: TX<br />
                    Delivery Zip: 65465<br />
                    Delivery Country: CA<br />

                    Moving Date: <?= date("d/m/Y") ?>
                </em>
            </td>
        </tr>

    </table>
    <?= formBoxEnd() ?>
    <br />
    <?= submitButtons(getLink("leadsources", "details", "id", (int) get_var("id")), "Submit") ?>
</form>