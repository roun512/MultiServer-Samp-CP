<?php
require_once 'init.php';

if($app->user->admin >= $app->config['logs']) $app->page->title = "Administrative Logs";
else $app->page->title = "No permissions";

if(!$app->user->LoggedIn) header('Location: /');

require_once 'header.php';

include_once 'includes/admin_sidebar.html';

if(!$app->user->LoggedIn || $app->user->admin < $app->config['setname']):
	include_once 'includes/no_permissions.html';
else:
	$page = 1;
	$url = "/admin/logs.php";
	if(isset($_GET['page'])) $page = (is_numeric($_GET['page']) ? $_GET['page'] : 1);

	if(is_numeric($page) == false) $page = 1;
	if($page < 1) $page = 1;

	$total = $app->log->GetAdminLogsCount();

	$limit = $app->config['logs_limit'];

	$pages = ceil($total / $limit);
	if($page > $pages) header('Location: /admin/logs.php');
	$offset = ($page*$limit)-$limit;

	include_once 'includes/forms/logs.html';
endif;
include_once 'includes/server_sidebar.html';
require_once 'footer.php';
?>