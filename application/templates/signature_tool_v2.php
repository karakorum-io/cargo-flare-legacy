<div class="modal fade" id="signature_tool" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="type_selector">
                        <table cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td><input type="radio" name="sign_type" value="text" id="sign_type_text" checked="checked"/></td>
                                <td><label for="sign_type_text">Type Signature</label></td>
                                <td><input type="radio" name="sign_type" value="draw" id="sign_type_draw"/></td>
                                <td><label for="sign_type_draw">Draw Signature</label></td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <div class="sign-controls" id="sign_draw_controls" style="display:none">
                                        <button id="signature-undo">Undo</button>
                                        <button id="signature-clear">Clear</button>
                                        <button id="signature-save-draw">Save</button>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="sign-controls" id="sign_write_controls">
                        <table cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td><label for="sign_name">Enter Your Name:&nbsp;</label></td>
                                <td><input type="text" maxlength="64" id="sign_name" class="form-box-textfield latin"/></td>
                                <td>
                                    <button id="signature-save-text" style="margin: 0 10px;">Save</button>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div id="paper"></div>
                    <div id="sign-result">
                        <img src="#" alt="Signature">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>