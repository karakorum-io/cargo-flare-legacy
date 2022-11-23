<?
/***************************************************************************************************
* Newsletters subscribers CP Class                                                                 *
*                                                                              					   *
*                                                                                                  *
* Client: 	FreightDragon                                                                          *
* Version: 	1.0                                                                                    *
* Date:    	2011-10-03                                                                             *
* Author:  	C.A.W., Inc. dba INTECHCENTER                                                          *
* Address: 	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076                             *
* E-mail:	techsupport@intechcenter.com                                                           *
* CopyRight 2011 FreightDragon. - All Rights Reserved                                              *
****************************************************************************************************/
?>
@flash_message@
<form action="<?=getLink("blast")?>" method="post">
    <?=formBoxStart()?>
    <div align="center">
		<table cellpadding="0" cellspacing="5" border="0">
	        <tr>
	            <td>@category_id@</td>
	        </tr>
	        <tr>
	            <td>@template_id@</td>
	        </tr>
	        <tr>
	            <td>@from_name@</td>
	        </tr>
	        <tr>
	            <td>@subject@</td>
	        </tr>
	        <tr>
	            <td><?=submitButtons()?></td>
	        </tr>

	    </table>
    </div>
    <?=formBoxEnd()?>

</form>