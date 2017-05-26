<?php
	require_once 'init.php';

	if($app->user->LoggedIn) $app->page->title = "Bans list";
	else {
		header('Location: /');
	}

	$result = null;
	$search = null;
	$url = "/bans.php";
	if(isset($_GET['q'])) {
		$search = $_GET['q'];
		$url .= "?q=".$search;
	}

	$page = 1;
	if(isset($_GET['page'])) $page = (is_numeric($_GET['page']) ? $_GET['page'] : 1);

	if(is_numeric($page) == false) $page = 1;
	if($page < 1) $page = 1;

	$total = $app->info->GetBannedUsersCount($search);

	$limit = $app->config['logs_limit'];

	$pages = ceil($total / $limit);
	if($pages == 0) $page = 1;
	elseif($page > $pages) header('Location: ' . $url);
	$offset = ($page*$limit)-$limit;
	$result = $app->info->GetBannedUsers($search, $limit, $offset);

	require_once 'header.php';
	include_once 'includes/user_sidebar.html';
	include_once 'includes/bans_list.html';
	include_once 'includes/server_sidebar.html';
	require_once 'footer.php';
?>