<?php
require_once 'init.php';

if($app->user->admin >= $app->config['aka']) $app->page->title = "AKA Search";
else $app->page->title = "No permissions";

if(!$app->user->LoggedIn) header('Location: /');

require_once 'header.php';

include_once 'includes/admin_sidebar.html';

if(!$app->user->LoggedIn || $app->user->admin < $app->config['aka']):
	include_once 'includes/no_permissions.html';
else:

	$count = 0;
	$result = null;
	if(isset($_POST['submit'])) {
		if(isset($_POST['term'])) {
			$term = $_POST['term'];
			$result = $app->admin->AKASearch($term);
			$count = count($result);
		} else {
			$app->error = "You left one or more of the fields empty.";
		}
	}
	include_once 'includes/forms/aka.html';
endif;
include_once 'includes/server_sidebar.html';
require_once 'footer.php';
?>