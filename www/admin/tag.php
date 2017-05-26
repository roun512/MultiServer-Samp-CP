<?php
require_once 'init.php';

if($app->user->admin >= $app->config['tag_info']) $app->page->title = "AG Tag Management";
else $app->page->title = "No permissions";

if(!$app->user->LoggedIn) header('Location: /');

require_once 'header.php';

if($app->CNR) {
	if($app->user->admin > 0)
		include_once 'includes/cnr/cnr_admin_sidebar.html';
	else
		include_once 'includes/cnr/cnr_user_sidebar.html';
} elseif($app->COD) {
	if($app->user->admin > 0)
		include_once 'includes/cod/cod_admin_sidebar.html';
	else
		include_once 'includes/cod/cod_user_sidebar.html';
}

if(!$app->user->LoggedIn || $app->user->admin < $app->config['tag_info']):
	include_once 'includes/no_permissions.html';
else:

	$result = null;
	if(isset($_POST['submit'])) {
		if(isset($_POST['name'])) {
			$name = $_POST['name'];
			$codid = $_POST['codid'];
			$cnrid = $_POST['cnrid'];
			$lsgwid = $_POST['lsgwid'];
			$forumid = $_POST['forumid'];
			$link = $_POST['link'];
			$notes = $_POST['notes'];
			if($app->log->AddAGTagUser($name, $codid, $cnrid, $lsgwid, $forumid, $link, $notes))
				$app->success = "Added " . htmlentities($name) . " successfully.";
			else
				$app->error = "Something went wrong!";
		} else {
			$app->error = "You left one or more of the fields empty.";
		}
	} elseif (isset($_GET['delete'])) {
		if($app->log->RemoveAGTagUser($_GET['delete']))
			$app->success = "Removed successfully.";
		else
			$app->error = "Something went wrong.";
	}
	$result = $app->log->GetAGTagInfo();
	$total = count($result);


	$url = "/admin/tag.php";
	$page = 1;

	if(isset($_GET['page'])) $page = (is_numeric($_GET['page']) ? $_GET['page'] : 1);
	if(is_numeric($page) == false) $page = 1;
	if($page < 1) $page = 1;

	$limit = $app->config['logs_limit'];

	$pages = ceil($total / $limit);
	if($pages == 0) $page = 1;
	elseif($page > $pages) header('Location: ' . $url);
	$offset = ($page*$limit)-$limit;


	include_once 'includes/forms/agtag.html';
endif;
include_once 'includes/server_sidebar.html';
require_once 'footer.php';
?>