<?php
require_once 'init.php';

if($app->user->admin >= $app->config['notes']) $app->page->title = "Manage Notes";
else $app->page->title = "No permissions";

if(!$app->user->LoggedIn) header('Location: /');

require_once 'header.php';

include_once 'includes/admin_sidebar.html';


if(!$app->user->LoggedIn || $app->user->admin < $app->config['notes']):
	include_once 'includes/no_permissions.html';
else:
	if(isset($_POST['submit'])) {
		if(isset($_POST['content'])) {
			$content = trim($_POST['content']);
			$app->admin->ManageNotes($content);
		} else {
			$app->error = "You left one or more of the fields empty.";
		}
	}
	include_once 'includes/forms/notes.html';
endif;
include_once 'includes/server_sidebar.html';
require_once 'footer.php';
?>