
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="ru">
<head>
    <title>@site_title@</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style type="text/css">
    body, p, td {
        font: 13px 'Lucida Grande', Lucida Grande, Helvetica, Arial, sans-serif;
        line-height: 18px;
    }
    a {
        color: #0084B4;
    }
    a:hover {
        color: #215e88;
    }
    h1, h2, h3 {
        margin: 0 0 15px 0;
        color: #000;
    }
    h2 {
        font: 20px Georgia, serif;
    }
    h3 {
        margin: 14px 0 0 0;
        color: #5978dd;
        font: bold 15px 'Lucida Grande', Lucida Grande, Helvetica, Arial, sans-serif;
        line-height: 20px;
        margin-bottom: 5px;
    }
    h4 {
        background-color: #eceff8;
        margin: 5px 0;
        padding-left: 5px;
    }
    p, ul {
        margin: 4px 0 15px 0;
    }
    .line {
        border-top: 1px solid #afafaf;
    }
    .copyright {
        color: #949494;
        font-family: "Trebuchet MS",Tahoma,Arial,"MS Sans Serif";
        font-size: 11px;
    }
    ul {
        list-style: none;
        margin: 0;
        padding: 0 0 0 3px;
    }
    </style>
</head>
<body>
    <table width="100%" cellpadding="10" cellspacing="0" border="0" bgcolor="#eceff8">
        <tr>
            <td align="left"><a href="@site_url@"><img src="@site_url@images/logo.png" alt="" border="0" /></a></td>
        </tr>
    </table>
    <br />
    <h2>@letter_title@</h2>
    @content@
    <br />
    <div class="line"></div>
    <p class="copyright">&copy; Copyright <?php echo date("Y"); ?> @site_title@. All Rights Reserved.</p>
</body>
</html>