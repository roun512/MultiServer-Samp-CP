<?php
require_once 'init.php';

if($app->user->admin >= $app->config['oban']) $app->page->title = "Offline ban";
else $app->page->title = "No permissions";

if(!$app->user->LoggedIn) header('Location: /');

require_once 'header.php';

include_once 'includes/admin_sidebar.html';

if(!$app->user->LoggedIn || $app->user->admin < $app->config['oban']):
	include_once 'includes/no_permissions.html';
else:
	if(isset($_POST['submit'])) {
		if(isset($_POST['name']) && isset($_POST['reason']) && isset($_POST['time'])) {
			$name = trim($_POST['name']);
			$reason = trim($_POST['reason']);
			$time = trim($_POST['time']);
			if(is_numeric($time) == false) $time = 0;
			$app->admin->OfflineBan($name, $reason, $time);
		} else {
			$app->error = "You left one or more of the fields empty.";
		}
	}
	include_once 'includes/forms/oban.html';
endif;
include_once 'includes/server_sidebar.html';
require_once 'footer.php';
?>