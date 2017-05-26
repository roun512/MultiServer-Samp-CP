<?php
require_once 'init.php';

if($app->user->admin >= $app->config['setlevel']) $app->page->title = "Set Level";
else $app->page->title = "No permissions";

if(!$app->user->LoggedIn) header('Location: /');

require_once 'header.php';

include_once 'includes/admin_sidebar.html';

if(!$app->user->LoggedIn || $app->user->admin < $app->config['setlevel']):
	include_once 'includes/no_permissions.html';
else:
	if(isset($_POST['submit'])) {
		if(isset($_POST['name']) && isset($_POST['level'])) {
			$name = trim($_POST['name']);
			$level = $_POST['level'];
			if(is_numeric($level))
				$app->admin->SetLevel($name, $level);
			else
				$app->error = "The admin level you entered is invalid.";
		} else {
			$app->error = "You left one or more of the fields empty.";
		}
	}
	if($app->COD)
		include_once 'includes/forms/cod_setlevel.html';
	else
		include_once 'includes/forms/gw_setlevel.html';
endif;
include_once 'includes/server_sidebar.html';
require_once 'footer.php';
?>