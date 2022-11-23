					<script type="text/javascript">
						function hideAll() {
			<?php
			foreach ($installs as $install) {
			?>
							document.getElementById("<?php echo $install[1]; ?>").style.display = "none";
			<?php
			}
			?>
						}
						function showDiv(sel) {
						  hideAll();
						  if (sel.selectedIndex != -1)
							{document.getElementById(sel.options[sel.selectedIndex].value).style.display = "block";}
						}
						$(document).ready(function() {
							$(".int_button_label").click(function() {
								$(".int_button_li").css("border", "5px solid #fff");
								$(this).parent().css("border", "5px solid #017fd6");
								$(".step2").removeClass("disabled");
								$(".database_input").removeAttr("disabled");
								$("#server").focus();
								var tmpStr = $("#server").val();
								$("#server").val('');
								$("#server").val(tmpStr);
							});
							$(".int_button_label").mouseover(function() {
								if ( ! $(this).children("input[type=radio]").is(":checked")) {
									$(this).parent().css("border", "5px solid #cecece");
								}
							});
							$(".int_button_label").mouseout(function() {
								if ( ! $(this).children("input[type=radio]").is(":checked")) {
									$(this).parent().css("border", "5px solid #fff");
								} else {
									$(this).parent().css("border", "5px solid #017fd6");
								}
							});
							$("#master-slave-select").change(function() {
								if ($(this).val() == "1") {
									$("#master-slave").show();
									$(".master-slave-trs").show();
								} else {
									$("#master-slave").hide();
									$(".master-slave-trs").hide();
								}
							});
							var slaves_count = <?php if (isset($_SESSION['slaves_number'])) echo $_SESSION['slaves_number']; else echo "1"; ?>;
							$("#add-slave").click(function() {
								slaves_count++;
								$('#slaves_number').val(slaves_count);
								$(".form-table2").append('<tr class="no-border master-slave-trs '+slaves_count+'"><th scope="row"><label for="phpver">Slave Host</label></th><td class="col2"><span class="formwrap" style="width:calc(100% - 20px)"><input type="text" class="database_input" name="slave_server_'+slaves_count+'" value="localhost" /></span></td><td class="col3">Your slave\'s database server (e.g. localhost).</td></tr><tr class="no-border master-slave-trs '+slaves_count+'"><th scope="row"><label for="configwrite">Slave Name</label></th><td class="col2"><span class="formwrap" style="width:calc(100% - 20px)"><input type="text" class="database_input" name="slave_dbname_'+slaves_count+'" value="" /></span></td><td class="col3">The name of your existing user database in the slave.</td></tr><tr class="no-border master-slave-trs '+slaves_count+'"><th scope="row"><label for="configwrite">Username</label></th><td class="col2"><span class="formwrap" style="width:calc(100% - 20px)"><input type="text" class="database_input" name="slave_dbusername_'+slaves_count+'" value="" /></span></td><td class="col3">Your username</td></tr><tr class="master-slave-trs '+slaves_count+'"><th scope="row"><label for="configwrite">Password</label></th><td class="col2"><span class="formwrap" style="width:calc(100% - 20px)"><input type="password" class="database_input" name="slave_dbpassword_'+slaves_count+'" value="" /></span></td><td class="col3">...and password.</td></tr>');
								$(".master-slave-trs").show();
								$("#remove-slave").show('inline-block');
							});
							$("#remove-slave").click(function() {
								if (slaves_count > 1) {
									$('tr.'+slaves_count).remove();
									slaves_count--;
									$('#slaves_number').val(slaves_count);
								}
								if (slaves_count <= 1) {
									$('#slaves_number').val('1');
									$("#remove-slave").hide();
								}
							});
						});
					</script>
					<p class="subtext">Please choose your installation type first.  Then, fill out the required information so that arrowchat can connect to your database.  If you are unsure about these details, please contact your host.</p>
					<p>&nbsp;</p>
					<p>&nbsp;</p>
					<div class="step">
						<img src="./images/step1.png" alt="" /><strong> Choose an installation type</strong>
					</div>
					<form method="post" id="database" action="<?php echo $_SERVER['PHP_SELF']; ?>?mode=database">
					<div class="integrations">
						<ul>
							<li style="width:781px;height:69px;" class="int_button_li"><label style="padding-top: 10px; height:58px;" class="int_button_label"><input type="radio" name="version" id="standalone" value="standalone" /><div style="margin:0 auto;width:390px;"><div style="float:left;"><img src="./images/img-custom.png" alt="Custom Website" /></div><div style="float:left;line-height:1em;margin-left:15px;margin-top:12px;text-align:left;"><b>Not Listed</b><br /><span style="font-size:11px;color:#9c9c9c;">(Custom website, additional programming may be required)</span><input type="hidden" name="version_number_standalone" value="v1" /></div></div></label></li>
			<?php
				foreach ($installs as $install) {
					if ($install[1] != "standalone") {
						if ($install[1] == $_POST['version'])
							$s = "selected";
						else
							$s = "";
			?>
							<li class="int_button_li">
								<label class="int_button_label"><input type="radio" name="version" id="<?php echo $install[1]; ?>" value="<?php echo $install[1]; ?>" /><img src="./images/img-<?php echo $install[1]; ?>.png" alt="<?php echo $install[0]; ?>" /></label>
								<div class="version_select">
									<select id="version_number_<?php echo $install[1]; ?>" name="version_number_<?php echo $install[1]; ?>">
									<?php
										foreach($install[4] as $key => $versions) {
									?>
										<option value="<?php echo $key; ?>"><?php echo $versions[0]; ?></option>
									<?php
										}
									?>
									</select>
								</div>
							</li>
			<?php
						}
					}
			?>
						</ul>
						<br />
					</div>
					<div class="clear"></div>
					<div class="step2 disabled">
					<div class="step">
						<img src="./images/step2.png" alt="" /><strong> Complete your database details</strong>
					</div>
					<div style="padding-left: 32px">
				<p class="subtext"><span class="boldtext">Do not create a separate database for ArrowChat.</span> Complete the details below to connect to the same database that your users are currently located within.</p>
				<style>
					.master-slave-trs{display:<?php if ($_SESSION['db_slave'] == 1) echo 'table-row'; else echo 'none'; ?>;background-color:#fbfbfb}
					#remove-slave{display:<?php if (isset($_SESSION['slaves_number']) AND $_SESSION['slaves_number'] > 1) echo 'inline-block'; else echo 'none'; ?>}
				</style>
				<table class="form-table2"> 
					<tr class="no-border"> 
						<th scope="row"><label for="configwrite">System</label></th> 
						<td class="col2" style="padding-bottom:5px"><span class="formwrap" style="width:calc(100% - 20px)"><select disabled="disabled" class="database_input" name="dbtype" style="width:100%;height:40px"><option value="0">MySQL / MySQLi</option><option value="1" <?php if ($_SESSION['db_type'] == 1) echo 'selected="selected"'; ?>>MSSQL</option></select></span></td> 
						<td class="col3">Select the type of database you are using.</td> 
					</tr> 
				<?php
					if (ARROWCHAT_EDITION == "business" OR ARROWCHAT_EDITION == "enterprise") {
				?>
					<tr> 
						<th scope="row"><label for="configwrite">Configuration</label></th> 
						<td class="col2"><span class="formwrap" style="width:calc(100% - 20px)"><select disabled="disabled" class="database_input" id="master-slave-select" name="dbslave" style="width:100%;height:40px"><option value="0">Single Database</option><option value="1" <?php if ($_SESSION['db_slave'] == 1) echo 'selected="selected"'; ?>>Master/Slave Databases</option></select></span></td> 
						<td class="col3">Select the configuration of your database(s).</td> 
					</tr> 
				<?php
					}
				?>
					<tr class="no-border"> 
						<th scope="row"><label for="phpver">Database Host</label></th> 
						<td class="col2"><span class="formwrap" style="width:calc(100% - 20px)"><input disabled="disabled" type="text" id="server" class="database_input" name="server" value="<?php if(isset($_SESSION['db_host'])) echo $_SESSION['db_host']; else echo "localhost"; ?>" /></span></td> 
						<td class="col3">Your database server (e.g. localhost).</td> 
					</tr> 
					<tr class="no-border"> 
						<th scope="row"><label for="configwrite">Database Name</label></th> 
						<td class="col2"><span class="formwrap" style="width:calc(100% - 20px)"><input disabled="disabled" type="text" class="database_input" name="dbname" value="<?php echo $_SESSION['db_name']; ?>" /></span></td> 
						<td class="col3">The name of your existing user database.</td> 
					</tr>  
					<tr class="no-border"> 
						<th scope="row"><label for="configwrite">Username</label></th> 
						<td class="col2"><span class="formwrap" style="width:calc(100% - 20px)"><input disabled="disabled" type="text" class="database_input" name="dbusername" value="<?php echo $_SESSION['db_username']; ?>" /></span></td> 
						<td class="col3">Your username</td> 
					</tr>  
					<tr> 
						<th scope="row"><label for="configwrite">Password</label></th> 
						<td class="col2"><span class="formwrap" style="width:calc(100% - 20px)"><input disabled="disabled" type="password" class="database_input" name="dbpassword" value="<?php echo $_SESSION['db_password']; ?>" /></span></td> 
						<td class="col3">...and password.</td> 
					</tr>  
					<tr class="no-border master-slave-trs"> 
						<th scope="row"><label for="phpver">Slave Host</label></th> 
						<td class="col2"><span class="formwrap" style="width:calc(100% - 20px)"><input disabled="disabled" type="text" class="database_input" name="slave_server_1" value="<?php if(isset($_SESSION['slave_host_1'])) echo $_SESSION['slave_host_1']; else echo "localhost"; ?>" /></span></td> 
						<td class="col3">Your slave's database server (e.g. localhost).</td> 
					</tr> 
					<tr class="no-border master-slave-trs"> 
						<th scope="row"><label for="configwrite">Slave Name</label></th> 
						<td class="col2"><span class="formwrap" style="width:calc(100% - 20px)"><input disabled="disabled" type="text" class="database_input" name="slave_dbname_1" value="<?php if(isset($_SESSION['slave_name_1'])) echo $_SESSION['slave_name_1']; ?>" /></span></td> 
						<td class="col3">The name of your existing user database in the slave.</td> 
					</tr>  
					<tr class="no-border master-slave-trs"> 
						<th scope="row"><label for="configwrite">Username</label></th> 
						<td class="col2"><span class="formwrap" style="width:calc(100% - 20px)"><input disabled="disabled" type="text" class="database_input" name="slave_dbusername_1" value="<?php if(isset($_SESSION['slave_username_1'])) echo $_SESSION['slave_username_1']; ?>" /></span></td> 
						<td class="col3">Your username</td> 
					</tr>  
					<tr class="master-slave-trs"> 
						<th scope="row"><label for="configwrite">Password</label></th> 
						<td class="col2"><span class="formwrap" style="width:calc(100% - 20px)"><input disabled="disabled" type="password" class="database_input" name="slave_dbpassword_1" value="<?php if(isset($_SESSION['slave_password_1'])) echo $_SESSION['slave_password_1']; ?>" /></span></td> 
						<td class="col3">...and password.</td> 
					</tr>
				<?php
					if (isset($_SESSION['slaves_number']) AND $_SESSION['slaves_number'] > 1)
					{
						for ($i = 2; $i <= $_SESSION['slaves_number']; $i++)
						{
				?>
					<tr class="no-border master-slave-trs <?php echo $i; ?>"> 
						<th scope="row"><label for="phpver">Slave Host</label></th> 
						<td class="col2"><span class="formwrap" style="width:calc(100% - 20px)"><input disabled="disabled" type="text" class="database_input" name="slave_server_<?php echo $i; ?>" value="<?php if(isset($_SESSION['slave_host_'.$i])) echo $_SESSION['slave_host_'.$i]; else echo "localhost"; ?>" /></span></td> 
						<td class="col3">Your slave's database server (e.g. localhost).</td> 
					</tr> 
					<tr class="no-border master-slave-trs <?php echo $i; ?>"> 
						<th scope="row"><label for="configwrite">Slave Name</label></th> 
						<td class="col2"><span class="formwrap" style="width:calc(100% - 20px)"><input disabled="disabled" type="text" class="database_input" name="slave_dbname_<?php echo $i; ?>" value="<?php if(isset($_SESSION['slave_name_'.$i])) echo $_SESSION['slave_name_'.$i]; ?>" /></span></td> 
						<td class="col3">The name of your existing user database in the slave.</td> 
					</tr>  
					<tr class="no-border master-slave-trs <?php echo $i; ?>"> 
						<th scope="row"><label for="configwrite">Username</label></th> 
						<td class="col2"><span class="formwrap" style="width:calc(100% - 20px)"><input disabled="disabled" type="text" class="database_input" name="slave_dbusername_<?php echo $i; ?>" value="<?php if(isset($_SESSION['slave_username_'.$i])) echo $_SESSION['slave_username_'.$i]; ?>" /></span></td> 
						<td class="col3">Your username</td> 
					</tr>  
					<tr class="master-slave-trs <?php echo $i; ?>"> 
						<th scope="row"><label for="configwrite">Password</label></th> 
						<td class="col2"><span class="formwrap" style="width:calc(100% - 20px)"><input disabled="disabled" type="password" class="database_input" name="slave_dbpassword_<?php echo $i; ?>" value="<?php if(isset($_SESSION['slave_password_'.$i])) echo $_SESSION['slave_password_'.$i]; ?>" /></span></td> 
						<td class="col3">...and password.</td> 
					</tr>
				<?php
						}
					}
				?>
				</table>
				<div id="master-slave" class="floatr" style="display:<?php if ($_SESSION['db_slave'] == 1) echo 'block'; else echo 'none'; ?>;float:left;margin-top:18px;">
					<a id="add-slave" class="fwdbutton" style="">
						<span>Add Another Slave</span>
					</a>
					<a id="remove-slave" class="fwdbutton" style="background-color:#fd4d4d">
						<span>Remove a Slave</span>
					</a>
				</div>
				<input type="hidden" id="slaves_number" name="slaves_number" value="<?php if (isset($_SESSION['slaves_number'])) echo $_SESSION['slaves_number']; else echo "1"; ?>" />
				<input type="hidden" name="form_submitted" value="1" />
				</div>
				</form>
			</div>
			<?php
				if (!empty($_SESSION['version'])) {
					$realid = explode("_", $_SESSION['version']);
			?>
				<script type="text/javascript">
					$(document).ready(function() {
						$(".step2").removeClass("disabled");
						$(".database_input").removeAttr("disabled");
						$("#<?php echo $realid[0]; ?>").attr("checked", "checked");
						$("#<?php echo $realid[0]; ?>").parent().parent().css("border", "5px solid #017fd6");
						$("#version_number_<?php echo $realid[0]; ?>").val('<?php echo $_SESSION['version_number']; ?>');
					});
				</script>
			<?php
				}
			?>