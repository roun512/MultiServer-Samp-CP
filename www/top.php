<?php
	require_once 'init.php';

	if($app->user->LoggedIn) $app->page->title = "Top Statistics";
	else {
		header('Location: /');
	}

	require_once 'header.php';

	include_once 'includes/user_sidebar.html';

	if($app->CNR)
		include_once 'includes/cnr/cnr_top_list.html';
	elseif($app->COD)
		include_once 'includes/cod/cod_top_list.html';
	elseif($app->GW)
		include_once 'includes/gw/gw_top_list.html';

	include_once 'includes/server_sidebar.html';
	require_once 'footer.php';
?>