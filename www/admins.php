<?php
	require_once 'init.php';

	if($app->user->LoggedIn) $app->page->title = "Administrators";
	else {
		header('Location: /');
	}

	require_once 'header.php';
	include_once 'includes/user_sidebar.html';
	include_once 'includes/admins_list.html';

	include_once 'includes/server_sidebar.html';
	require_once 'footer.php';
?>