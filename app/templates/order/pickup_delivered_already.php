<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"

    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<link rel="shortcut icon" href="<?php echo SITE_IN ?>styles/favicon.ico" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>Thank You</title>
    <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/styles.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/application.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/default.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/jquery-ui.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/bootstrap.min.css"/>

    <style>
 body {
	margin:0; 
	padding: 0;
}
h3 {
	margin: 0;
	padding: 0;
	/*text-transform: uppercase;*/
	font-family: "Oswald",sans-serif !important;
	font-weight: 700 !important;
	margin-bottom: 20px !important;
	margin-top: 15px !important;
	/*padding-left: 15px !important;*/
	/*border-left: 8px solid #27aae1;*/
	color:#666 !important;
	font-size: 18px !important;
}
h1 {
	margin: 0;
	padding: 0;
	
	font-family: "Oswald",sans-serif !important;
	font-weight: 700 !important;
	margin-bottom: 20px !important;
	margin-top: 10px !important;
	/*padding-left: 15px !important;*/
	/*border-left: 8px solid #27aae1;*/
	color:#666 !important;
	font-size: 58px !important;
}
p, label {  
	font-size: 13px !important;
	font-family: "Raleway", sans-serif !important;
	color:#808285 !important;
	font-weight: normal;
}

form {
	font-size: 13px !important;
	font-family: "Raleway", sans-serif !important;
	color:#808285 !important;
}
th.transit_title {
	font-family: "Raleway", sans-serif !important;
}
.row {
	margin-left: 0 !important;
	padding: 0 !important;
}
.col-lg-12, .col-md-12, .col-lg-6, .col-md-6 {
	margin-left: 0 !important;
	padding-left: 0 !important;
}
.hf {
	margin: 0;
	padding: 0;
	display: block;
	background-color:#27aae1;
	height: 58px;
	width: 100%;
}
.hf img {
	line-height: 	55px;	
	display: inline;
	margin-top: -12px;
}
.hf h3 {

	font-family: "Oswald",sans-serif !important;
	font-weight: 700 !important;
	display: inline;
	line-height: 55px;
	margin-left: 10px;
	margin-top: 25px;
	margin-bottom: 0;
	color:#fff !important;
}
.main_body {
	height: 	100%;
	background-color: #f9f9f9;
}
.wrapper {
	width: 	90% !important;
	margin: 	0 auto !important;
}
.origin span {
	color:#025773 !important;
	text-transform: uppercase;
}
.inop, .destination span {
	color: #f15a29 !important;
	text-transform: uppercase;
}

.pricing, .transit {
	float: 	left;
}


.transit th {
	background-color:#27aae1 !important;]
	color: #fff !important;
	font-family: "Raleway", sans-serif !important;
}
.transit tr th {
  color: #fff !important;
  text-align: center !important;
  font-family: "Raleway", sans-serif !important;
}
.transit tr th:first-child, .transit tr td:first-child {
	border-right: 1px solid #808285 !important;
}
.transit td {
	color:#808285 !important;
}
.vehicle td:last-child {
	color: #f15a29 !important;
}
.vehicle td {
	color:#808285 !important;
	text-align: center !important;
}
.vehicle th {
	background-color: #e6e7e8 !important;
	color:#666 !important;
    text-align: center !important;
}
.first {
	margin-top: 35px;
	margin-bottom: 35px;
	  padding-right: 28px;
	  border-right: 1px dotted #808285;
}
.second {
	margin-top: 35px;
	margin-bottom: 45px;
	
}
.car span {
	color:#025773 !important;
	font-family: "Raleway", sans-serif !important;
	font-weight: bold;
}
.shipper span, .pricing span {
	color:#025773;
	font-family: "Raleway", sans-serif !important;
	font-weight: bold;
}

.terms_box {
	width: 100%;
	height: 200px;
	overflow: auto;
	border: 1px solid #808285;
	padding: 10px;
}
button {
	font-size: 12px !important;
	color: #fff !important;
	background-color:#27aae1 !important;
	padding: 7px 16px;
	border: none;
	
	display: inline;
	border-radius: 2px !important;
}
input[type="text"] {
  margin-right: 20px !important;
}
/*TABLE*/
@media screen and (max-width: 1140px) {
	.first{border-right:none !important;}
}
@media screen and (max-width: 768px) {
	h3 {
		font-size: 16px !important;
	}
	button {
	  margin-top: 20px !important;
	  background-color:#27aae1 !important;
}
}

 </style>
<script type="text/javascript" src="/jscripts/jquery-1.7.1.min.js"></script>
<script type="text/javascript">
	
$(document).ready(function() {
				  
    var contentHeight = $( window ).height();
	
    contentHeight = (contentHeight - 117);
	//alert(contentHeight);
	$("#contentTable").css("height", contentHeight+'px');
	

});
</script>


</head>

<body>

<div class="row hf">
	<div class="wrapper">	
	<table width="20%" cellspacing="1" cellpaddong="1">
    <tbody><tr><td valign="middle" align="center">
<img src="http://www.freightdragon.com/images/esign.png" alt="" width="34px" height="34px" style="padding-top:2px;">
      </td>
      <td valign="middle" align="left">
           <h3>Dispatch# <?=$this->entity->getNumber()?></h3>
      </td>
    </tr>
  </tbody></table>
	</div>
	</div>
	
	<div class="row main_body">
	<div class="wrapper">
      
            <table width="100%" cellpadding="1" cellspacing="1" id="contentTable"> <!--height="531515"-->
              <tr>
                <td>&nbsp; </td>
              </tr> 
              
              <tr>
                <td>&nbsp; </td>
              </tr> 
              <?php if($_GET['type']=="p"){?>
              <tr>
                <td style="font-family: "Raleway", sans-serif;" align="center"><font size="14"><b>This order has been updated by <?=$this->company->companyname?> on <?= $this->entity->getActualPickUpDate("m/d/Y") ?>.</b></font></td>
              </tr>
              <?php }elseif($_GET['type']=="d"){?>
               <tr>
                <td style="font-family: "Raleway", sans-serif;" align="center"><font size="14"><b>This order has been updated by <?=$this->company->companyname?> on <?= $this->entity->getDelivered("m/d/Y") ?>.</b></font></td>
              </tr>
              <?php }?>
              <tr>
                <td align="center">&nbsp; </td>
              </tr>
              <tr>
                <td>&nbsp; </td>
              </tr> 
              
              <tr>
                <td>&nbsp; </td>
              </tr>
              <tr>
                <td>&nbsp; </td>
              </tr> 
              <tr>
                <td >&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp; </td>
              </tr> 
              <tr>
                <td>&nbsp; </td>
              </tr> 
              <tr>
                <td>&nbsp; </td>
              </tr> 
            </table>
           
            
      </div>
  </div>
<div class="row hf">
<div class="wrapper"> 
  <table width="20%" cellspacing="1" cellpaddong="1">
    <tbody><tr><td valign="middle" align="center">
<img src="http://www.freightdragon.com/images/esign.png" alt="" width="34px" height="34px"  style="padding-top:2px;">
      </td>
      <td valign="middle" align="left">
           <h3>Dispatch# <?=$this->entity->getNumber()?></h3>
      </td>
    </tr>
  </tbody></table>
  
  </div>
</div>

 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
</body>

</html>