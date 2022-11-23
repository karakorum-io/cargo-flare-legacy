<?php
/**************************************************************************************************
 * errors.php
 *
 * Version:		1.0
 * Date:		2012-04-26
 * Author:		C.A.W., Inc. dba INTECHCENTER
 * Address:		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * E-mail:		techsupport@intechcenter.com
 * CopyRight 2012 Intechcenter. - All Rights Reserved
 ***************************************************************************************************/
?>
<h2 style="text-align: center">You form contains the following errors!</h2>
@error@
<?=simpleButton("Back", $this->post_back)?>