<?php
	
	$config['name'] = "Advance Gaming";
	$config['domain'] = "http://ucp.advance-gaming.com/";

	/*
	* Database information
	*/

	$config['db']['host'] = "127.0.0.1";
	$config['db']['username'] = "root";
	$config['db']['password'] = "";
	$config['db']['cod_database'] = "coddb";
	$config['db']['cnr_database'] = "CNRDB";
	$config['db']['gw_database'] = "GWDB";
	$config['db']['web_database'] = "WEBDB";
	

	/*
	* Cookies related stuff ~ DO NOT TOUCH THIS (SPECIALLY VADNETTA)
	*/

	// Generate random hash for cookies for each server and add it here.

	$config['hash']['cod'] = "";
	$config['hash']['cnr'] = "";
	$config['hash']['gw'] = "";

	/*
	* Administrative permissions
	*/

	$config['aka'] = 1;
	$config['records'] = 2;
	$config['unban'] = 2;
	$config['oban'] = 3;
	$config['otban'] = 3;
	$config['setname'] = 4;
	$config['setstats'] = 4;
	$config['tag_info'] = 4;
	$config['setpass'] = 5;
	$config['setlevel'] = 5;
	$config['setvip'] = 5;
	$config['notes'] = 5;
	$config['logs'] = 5;
	$config['activity'] = 5;


	/*
	* Limit of logs to show in each page. Default is 25.
	*/
	$config['logs_limit'] = 25;

?>
