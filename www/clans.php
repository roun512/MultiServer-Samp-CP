<?php

	require_once 'init.php';

	if($app->user->LoggedIn && $app->COD) $app->page->title = "Clans list";
	else header('Location: /');

	$result = null;
	$url = "/clans.php";
	$page = 1;

	if(isset($_GET['page'])) $page = (is_numeric($_GET['page']) ? $_GET['page'] : 1);
	if(is_numeric($page) == false) $page = 1;
	if($page < 1) $page = 1;

	$result = $app->info->GetClansList();

	$total = count($result);
	$limit = $app->config['logs_limit'];

	$pages = ceil($total / $limit);
	if($pages == 0) $page = 1;
	elseif($page > $pages) header('Location: ' . $url);
	$offset = ($page*$limit)-$limit;

	require_once 'header.php';
	include_once 'includes/cod/cod_user_sidebar.html';
	include_once 'includes/clans_list.html';
	include_once 'includes/server_sidebar.html';

	require_once 'footer.php';


?>