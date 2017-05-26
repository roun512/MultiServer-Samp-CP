<?php
require_once 'init.php';

if($app->user->LoggedIn) $app->page->title = "Name Changes";
else $app->page->title = "No permissions";

require_once 'header.php';

if(!$app->user->LoggedIn):
	include_once 'includes/no_permissions.html';
else:

	$result = null;
	$found = false;
	
	$url = "/namechanges.php";

	if(isset($_POST['submit']))
	{
		if(isset($_POST['name']))
		{
			$name = $_POST['name'];

			$result = $app->log->SearchNameChanges($_POST['name']);

			if(count($result) == 0)
				$app->error = "No results found on the term you searched.";
			else
				$found = true;

		} else {
			$app->error = "You left one or more of the fields empty.";
		}
	} else {
		$id = $app->user->id;

		if(isset($_GET['id']))
		{
			if($app->info->IsValidUserByID($id)) {
				$id = $_GET['id'];
				$url .= "?id=".$id;
			}
			else
				$app->error = "Invalid user id.";
		}

		$page = 1;

		if(isset($_GET['page'])) $page = (is_numeric($_GET['page']) ? $_GET['page'] : 1);

		if(is_numeric($page) == false) $page = 1;

		if($page < 1) $page = 1;

		$total = $app->log->GetNameChangesCount($id);

		$limit = $app->config['logs_limit'];

		$pages = ceil($total / $limit);
		if($pages == 0) $page = 1;
		elseif($page-1 > $pages) header('Location: ' . $url);
		$offset = ($page*$limit)-$limit;

		$result = $app->log->GetNameChanges($id, $page, $offset);

	}

	include_once 'includes/user_sidebar.html';
	include_once 'includes/forms/namechanges.html';
	include_once 'includes/server_sidebar.html';
endif;
require_once 'footer.php';
?>