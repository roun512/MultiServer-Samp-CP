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

	$config['hash']['cod'] = "6f868fc4d8ad60d852bf576b69f776d0a33da92a";
	$config['hash']['cnr'] = "c6828196733b3616a34fc90677f7beb0e703b058";
	$config['hash']['gw'] = "742bcdd116239a66a8755f48a3b70f740eaa85eb";

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
