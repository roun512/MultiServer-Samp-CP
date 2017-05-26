<?php

	require_once 'init.php';

	if($app->user->LoggedIn && $app->CNR) $app->page->title = "Factions list";
	else header('Location: /');

	$result = null;
	$url = "/factions.php";
	$page = 1;

	if(isset($_GET['page'])) $page = (is_numeric($_GET['page']) ? $_GET['page'] : 1);
	if(is_numeric($page) == false) $page = 1;
	if($page < 1) $page = 1;

	$result = $app->info->GetFactionsList();

	$total = count($result);
	$limit = $app->config['logs_limit'];

	$pages = ceil($total / $limit);
	if($pages == 0) $page = 1;
	elseif($page > $pages) header('Location: ' . $url);
	$offset = ($page*$limit)-$limit;

	require_once 'header.php';
	include_once 'includes/cnr/cnr_user_sidebar.html';
	include_once 'includes/factions_list.html';
	include_once 'includes/server_sidebar.html';

	require_once 'footer.php';


?>