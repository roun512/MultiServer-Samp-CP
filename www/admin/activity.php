<?php
require_once 'init.php';

if($app->user->admin >= $app->config['aka']) $app->page->title = "Activity";
else $app->page->title = "No permissions";

if(!$app->user->LoggedIn) header('Location: /');

require_once 'header.php';

include_once 'includes/admin_sidebar.html';

if(!$app->user->LoggedIn || $app->user->admin < $app->config['aka']):
	include_once 'includes/no_permissions.html';
else:
	
	$result = $app->admin->GetActivity();
	include_once 'includes/forms/activity.html';
endif;

include_once 'includes/server_sidebar.html';
require_once 'footer.php';
?>