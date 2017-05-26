<?php
require_once 'init.php';

if($app->user->admin >= $app->config['activity']) $app->page->title = "Snapshot";
else $app->page->title = "No permissions";

if(!$app->user->LoggedIn) header('Location: /');

require_once 'header.php';

include_once 'includes/admin_sidebar.html';

if(!$app->user->LoggedIn || $app->user->admin < $app->config['activity']):
	include_once 'includes/no_permissions.html';
else:

	if(isset($_POST['snapshot'])) $app->admin->TakeActivitySnapShot();
	include_once 'includes/forms/snapshot.html';
endif;

include_once 'includes/server_sidebar.html';
require_once 'footer.php';
?>