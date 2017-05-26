<?php
require_once 'init.php';

if($app->user->admin >= $app->config['setpass']) $app->page->title = "Set password";
else $app->page->title = "No permissions";

if(!$app->user->LoggedIn) header('Location: /');

require_once 'header.php';

include_once 'includes/admin_sidebar.html';
if(!$app->user->LoggedIn || $app->user->admin < $app->config['setpass']):
	include_once 'includes/no_permissions.html';
else:
	if(isset($_POST['submit'])) {
		if(isset($_POST['name']) && isset($_POST['password'])) {
			$name = trim($_POST['name']);
			$password = trim($_POST['password']);
			$app->admin->SetPass($name, $password);
		} else {
			$app->error = "You left one or more of the fields empty.";
		}
	}
	include_once 'includes/forms/setpass.html';
endif;
include_once 'includes/server_sidebar.html';
require_once 'footer.php';
?>