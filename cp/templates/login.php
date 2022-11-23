<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en">
<head>
    <title>Administration Login</title>
    <link type="text/css" rel="stylesheet" href="<?=SITE_IN?>styles/styles.css" />
    <link type="text/css" rel="stylesheet" href="<?=SITE_IN?>styles/default.css" />
    <script type="text/javascript" src="<?=SITE_IN?>jscripts/jquery.js"></script>
    <style type="text/css">html, body {margin: 0px; padding: 0px; height: 100%;}</style>
</head>
<body>
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="height: 100%;">
    <tr>
        <td align="center">
            <table>
                <tr>
                    <td>
                        <?=formBoxStart("Administration Login")?>
                        <div id="cp-login-error">@error@</div>
                        <form action="<?=getLink("login")?>" method="post" onsubmit="return checkLogin();">
                            <table width="100%" cellpadding="3" cellspacing="10" border="0">
                                <tr>
                                    <td>@email@</td>
                                </tr>
                                <tr>
                                    <td>@password@</td>
                                </tr>
                                <tr>
                                    <td><a href="<?=SITE_IN?>">Back to Home</a></td>
                                    <td style="text-align: right;"><div class="form-box-buttons"><input type="submit" name="submit" value="Login" tabindex="3" /></div></td>
                                </tr>
                            </table>
                        </form>
                        <?=formBoxEnd()?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<script type="text/javascript">
//<![CDATA[
$(function(){ $('#login').focus(); });
function checkLogin() {
    var errObj = $('#cp-login-error');
    var emailObj = $('#email');
    var passwObj = $('#password');

    if (emailObj.val() == '' || passwObj.val() == '') {
        errObj.text('Please enter E-mail and Password.');
        return false;
    }
    return true;
}
//]]>
</script>
</body>
</html>