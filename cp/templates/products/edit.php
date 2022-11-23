<br />
@flash_message@
<div align="center">
    <form action="<?= getLink('products/save') ?>" method="post" enctype="multipart/form-data" id="MainForm" name="MainForm" style="margin:0">
        <input name="id" type="hidden" value="@id@" />
        <table cellpadding="3" cellspacing="0" border="0" class="form-table">
						<td align="right">Is Online:</td>
						<td align="left">
								<input type="checkbox" name="is_online" id="is_online" @is_online@ value="1" />
								<label for="is_online">availability</label>
						</td>
						<tr valign="top">
                <td width="200" align="right"><span class="required">*</span> Type:</td>
                <td width="500" align="left">@type@</td>
            </tr>
            <tr valign="top">
                <td align="right"><label for="code"><span class="required">*</span> Code:</label></td>
                <td align="left"><input type="text" name="code" id="code" value="@code@" style="width:100px; text-align: right;" maxlength="10" /></td>
            </tr>
            <tr valign="top">
                <td align="right"><span class="required">*</span> Name:</td>
                <td align="left"><input type="text" name="name" id="name" value="@name@" style="width:300px" maxlength="100" /></td>
            </tr>
            <tr valign="top">
                <td align="right"><span class="required">*</span> Price:</td>
                <td align="left"><input type="text" name="price" id="price" value="@price@" style="width:100px; text-align: right;" maxlength="10" /></td>
            </tr>
            <tr valign="top">
                <td align="right">Description:</td>
                <td align="left"><textarea name="description" id="description" style="width: 300px; height: 60px;">@description@</textarea></td>
            </tr>
            <tr valign="top" id="not_addon_0">
                <td align="right"><span class="required">*</span> Period:</td>
                <td align="left">@period@</td>
            </tr>
            <tr class="renewal">
                <td align="right">Renewal Code:</td>
                <td align="left">
                    <input type="text" name="renewal_code" id="renewal_code" value="@renewal_code@" style="width:100px; text-align: right;" maxlength="10" />
                    Leave <b>Renewal Code</b> field blank if renewal not expected.
                </td>
            </tr>
            <tr valign="top">
                <td colspan="2">
										<br/><br/>
										<?= submitButtons(getLink("products")) ?>
								</td>
            </tr>
        </table>
    </form>
</div>
<br />
<br />