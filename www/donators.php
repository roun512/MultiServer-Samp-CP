<?php
	require_once 'init.php';

	if($app->user->LoggedIn) $app->page->title = "Donators";
	else {
		header('Location: /');
	}

	require_once 'header.php';
	include_once 'includes/user_sidebar.html';
	include_once 'includes/vips_list.html';
	include_once 'includes/server_sidebar.html';
	require_once 'footer.php';
?>