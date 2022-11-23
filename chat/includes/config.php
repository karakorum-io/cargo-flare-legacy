<?php 

	/*
	|| #################################################################### ||
	|| #                             ArrowChat                            # ||
	|| # ---------------------------------------------------------------- # ||
	|| #    Copyright ©2010-2020 ArrowSuites LLC. All Rights Reserved.    # ||
	|| # This file may not be redistributed in whole or significant part. # ||
	|| # ---------------- ARROWCHAT IS NOT FREE SOFTWARE ---------------- # ||
	|| #   http://www.arrowchat.com | http://www.arrowchat.com/license/   # ||
	|| #################################################################### ||
	*/

	// Require any necessary external files for retrieving the user's session

		
	/**
	 * The database information (Master Server)
	 *
	 * Your existing users and information should already be in this database.  Do NOT create
	 * a new database for ArrowChat.
	*/
	define('DB_SERVER','66.175.236.177'); 
	define('DB_USERNAME','cargo_flare'); 
	define('DB_PASSWORD','+QXr[Ovg%5Qe_zx2QB'); 
	define('DB_NAME','cargo_flare_2');	
		
	/**
	 * The slave database information (Advanced Users Only)
	 *
	 * The connection information for your slave database if you are using a master/slave setup.
	 * Leave this information blank if you plan on only using one server.  You can set up more
	 * than 1 slave by adding another set of defines and incrementing the last number by +1 each
	 * time.  Ex: DB_SLAVE_SERVER_1, DB_SLAVE_SERVER_2, DB_SLAVE_SERVER_3, etc.  You must also
	 * change the SLAVE_DATABASE and SLAVE_NUMBER config options below.
	*/
	define('DB_SERVER_SLAVE_1','');  
	define('DB_USERNAME_SLAVE_1',''); 
	define('DB_PASSWORD_SLAVE_1',''); 
	define('DB_NAME_SLAVE_1','');	
		
	/**
	 * The table prefix can be left blank. A quick example of what you should input here:
	 *
	 * Example - Pretend the following list are tables:
	 * phpbb_friends
	 * phpbb_threads
	 * phpbb_users
	 *
	 * In the example above, the prefix would be phpbb_ because everything starts with it.
	 *
	 * Example - Pretend the following list are tables:
	 * friends
	 * threads
	 * users
	 *
	 * In the example above, the prefix would be blank.
	*/
	define('TABLE_PREFIX','');
	
	/**
	 * These variables will help automatically connect your existing website with ArrowChat.  Please
	 * review the descriptions below to better understand them. DO NOT INCLUDE THE PREFIX WITH THESE
	 * VALUES!
	 *
	 * DB_USERTABLE		   		= The name of the user's table
	 * DB_USERTABLE_USERID 		= The field for the user ID in the user's table
	 * DB_USERTABLE_NAME   		= The field for the username in the user's table
	 * DB_USERTABLE_AVATAR 		= The field for the avatar (input the user ID field if none exists)
	 *
	 * DB_FRIENDSTABLE	   		= (Optional) The name of the friend's table
	 * DB_FRIENDSTABLE_USERID	= (Optional) The field for the user ID in the friend's table
	 * DB_FRIENDSTABLE_FRIENDID	= (Optional) The field for the relationship/friend ID in the firned's table
	 * DB_FRIENDSTABLE_FRIENDS	= (Optional) The field to check if the users are friends
	 *
	 * All the friends stuff is optional.  If your site does not have a friend's system, leave the
	 * values blank and change the no friend system value.
	 */
	define('DB_USERTABLE','app_arrowchat_members'); 
	define('DB_USERTABLE_NAME','username'); 
	define('DB_USERTABLE_USERID','member_id'); 
	define('DB_USERTABLE_AVATAR','member_id'); 
	
	define('DB_FRIENDSTABLE','app_arrowchat_friends'); 
	define('DB_FRIENDSTABLE_USERID', 'member_id'); 
	define('DB_FRIENDSTABLE_FRIENDID', 'friend_member_id'); 
	define('DB_FRIENDSTABLE_FRIENDS', 'handshake');
	
	/**
	 * Friend System
	 *
	 * If your website does not have a friend system (ex: you want to display all online users) then
	 * change the value below from 0 to 1.
	*/
	define('NO_FREIND_SYSTEM', '0');
	
	/**
	 * MSSQL Database
	 *
	 * If your database is MSSQL then change the value below from 0 to 1.
	*/
	define('MSSQL_DATABASE', '0');
	
	/**
	 * Emoji Support
	 *
	 * WARNING: You cannot change this value because your database will be using the wrong collation
	 * type still.  You must re-run the install folder.
	*/
	define('EMOJI_SUPPORT', '1');
		
	/**
	 * Master/Slave Database
	 *
	 * Set SLAVE_DATABASE value to 1 if you are using a master/slave database.  The slave database must 
	 * be configured above.  Also, set SLAVE_NUMBER to the number of slaves you are using.
	*/
	define('SLAVE_DATABASE', '0');
	define('SLAVE_NUMBER', '1');

?>