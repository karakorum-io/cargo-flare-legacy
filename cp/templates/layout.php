<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ru">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>@title@</title>
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/styles.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/default.css"/>
		<link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/application.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/jquery-ui.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/jquery.autocomplete.css"/>
        <script type="text/javascript">var BASE_PATH = '<?php echo SITE_IN ?>';</script>
        <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.cookie.js"></script>
        <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery-ui.js"></script>
        <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.autocomplete.js"></script>
        <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/functions.js"></script>
        <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/cp.js"></script>
		<script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/filters.js"></script>
        <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.maskMoney.js"></script>
        <script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.maskedinput-1.3.min.js"></script>
        <script type="text/javascript" src="<?php echo SITE_IN; ?>jscripts/jquery.printarea.js"></script>
        
        
        <script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false&amp;language=en"></script>
        <!--[if lt IE 7]>
        <![if gte IE 5.5]>
        <script type="text/javascript" src="<?php echo SITE_IN; ?>jscripts/fixpng.js"></script>
        <style type="text/css">
        .iePNG, IMG { filter:expression(fixPNG(this)); }
        .iePNG A { position: relative; }
        </style>
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN; ?>styles/ie6.css" />
        <![endif]>
        <![endif]-->
        <!--[if IE 7]>
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN; ?>styles/ie7.css" />
        <![endif]-->
    </head>
    <body>
        <div class="centering">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td><a href="<?php echo getLink() ?>"><img src="<?php echo SITE_IN ?>images/logo_cp.png" alt="<?= $this->daffny->cfg['site_title']; ?>" width="210" height="75" /></a></td>
                    <td align="right" valign="top" style="padding-right: 3px">
                        <br/>
                        Main Site <a href="<?php echo BASE_PATH ?>" onclick="window.open(this.href); return false;"><?php echo BASE_PATH ?></a>
                        <br/><br/>
                        @hello_admin@
                    </td>
                </tr>
            </table>
            <?php $act = $this->daffny->action ?>
            <div class="headline">
                <ul class="menuapp">
                    <li class="first">&nbsp;</li>
                    <li><a href="<?= getLink(""); ?>">Home</a></li>
                    <li <?= (isset($_GET['admins'])) ? 'class="active"' : '' ?>><a href="<?= getLink("admins"); ?>">Users</a>
                        <ul>
							<?php if ($_SESSION['member']['group_id'] == 2) { ?>
                            <li><a href="<?php echo getLink("admins") ?>">Admins</a></li>
							<?php } ?>
                        </ul>
                    </li>
                    <li <?= (in_array($act, array("content", "faq", "news")) ? 'class="active"' : '') ?>><a href="<?= getLink("content"); ?>">Content</a>
                        <ul>
                            <li><a href="<?php echo getLink("content") ?>">Content</a></li>
                            <li><a href="<?php echo getLink("faq") ?>">F.A.Q.</a></li>
                            <li><a href="<?php echo getLink("news") ?>">News</a></li>
                        </ul>
                    </li>
                    <li <?= ($act == "forms" ? 'class="active"' : '') ?>><a href="<?= getLink("forms", "contactus"); ?>">Contacts</a>
                        <ul>
                            <li><a href="<?php echo getLink("forms", "feedback") ?>">Feedback</a></li>
                            <li><a href="<?php echo getLink("forms", "contactus") ?>">Contact Us</a></li>
                        </ul>
                    </li>
                    <li <?= (in_array($act, array("subscribers", "statistics", "blast", "newsletters")) ? 'class="active"' : '') ?>><a href="<?= getLink("newsletters"); ?>">Newsletters</a>
                        <ul>
                            <li><a href="<?php echo getLink("subscribers") ?>">Subscribers</a></li>
                            <li><a href="<?php echo getLink("newsletters") ?>">Newsletters</a></li>
                            <li><a href="<?php echo getLink("blast") ?>">Blast</a></li>
                            <li><a href="<?php echo getLink("statistics") ?>">Statistics</a></li>
                        </ul>
                    </li>
                    <li <?= (in_array($act, array("sysmessages", "documents", "members")) ? 'class="active"' : '') ?>><a href="<?= getLink("members"); ?>">Application</a>
                        <ul>
                            <li><a href="<?php echo getLink("members") ?>">Members</a></li>
                            <li><a href="<?php echo getLink("sysmessages") ?>">System messages</a></li>
                            <li><a href="<?php echo getLink("documents") ?>">Documents</a></li>
                            <li><a href="<?php echo getLink("payments") ?>">Payments</a></li>
                            <li><a href="<?php echo getLink("ratings") ?>">Ratings</a></li>
                        </ul>
                    </li>
					<li <?= in_array($act, array('products', 'coupons', 'orders', 'licenses', 'product_types'))?'class="active"':''?>>
						<a href="<?=getLink('products')?>">Shopping Cart</a>
						<ul>
							<li><a href="<?= getLink('products') ?>">Products</a></li>
							<li><a href="<?= getLink('coupons') ?>">Coupons</a></li>
						</ul>
					</li>
					<li <?=in_array($act, array('reports'))?'class="active"':''?> >
						<a href="<?=getLink('reports/sales')?>">Reports</a>
						<ul>
							<li><a href="<?=getLink('reports/sales')?>">Sales</a></li>
							<li><a href="<?=getLink('reports/users')?>">Users</a></li>
							<li><a href="<?=getLink('reports/licenses')?>">Licenses</a></li>
						</ul>
					</li>
                    <li><a href="<?= getLink("settings"); ?>">Settings</a>
                    </li>
                    <!--li class="fill">&nbsp;</li-->
                    <!--li class="last">&nbsp;</li-->
                </ul>
            </div>
            <div style="clear: both"></div>
            @content@
            <div class="hr_dlm"></div>
            <br /><br />
            <div class="footer">
                <div class="copyright">
                    &copy; Copyright <?= $this->daffny->cfg['site_title'] ?> <?= date("Y") == 2011 ? '2011' : '2011-' . date("Y") ?>. All Rights Reserved.<br />
                </div>
            </div>
        </div>
        <div class="foot-centering">&nbsp;</div>
    </body>
</html>