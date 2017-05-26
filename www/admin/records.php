<?php
require_once 'init.php';

if($app->user->admin >= $app->config['records']) $app->page->title = "Admin records";
else $app->page->title = "No permissions";

if(!$app->user->LoggedIn) header('Location: /');

require_once 'header.php';

include_once 'includes/admin_sidebar.html';

if(!$app->user->LoggedIn || $app->user->admin < $app->config['aka']):
	include_once 'includes/no_permissions.html';
else:

	$result = null;
	if(isset($_GET['username'])) {
		$username = $_GET['username'];
		$result = $app->admin->GetPlayerRecords($username);
	} else {
		$app->error = "You left one or more of the fields empty.";
	}
	include_once 'includes/forms/records.html';
endif;
include_once 'includes/server_sidebar.html';
require_once 'footer.php';
?>