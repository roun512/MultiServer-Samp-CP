<?php
require_once 'init.php';

if($app->user->admin >= $app->config['setname']) $app->page->title = "Set name";
else $app->page->title = "No permissions";

if(!$app->user->LoggedIn) header('Location: /');

require_once 'header.php';

include_once 'includes/admin_sidebar.html';

if(!$app->user->LoggedIn || $app->user->admin < $app->config['setname']):
	include_once 'includes/no_permissions.html';
else:
	if(isset($_POST['submit'])) {
		if(isset($_POST['oldname']) && isset($_POST['newname'])) {
			$oldname = trim($_POST['oldname']);
			$newname = trim($_POST['newname']);
			$app->admin->SetName($oldname, $newname);
		} else {
			$app->error = "You left one or more of the fields empty.";
		}
	}
	include_once 'includes/forms/setname.html';
endif;
include_once 'includes/server_sidebar.html';
require_once 'footer.php';
?>