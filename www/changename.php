<?php
require_once 'init.php';

if($app->user->LoggedIn) $app->page->title = "Change Username";
else $app->page->title = "No permissions";

require_once 'header.php';

if(!$app->user->LoggedIn):
	include_once 'includes/no_permissions.html';
else:


	if(isset($_POST['submit'])) {
		if(isset($_POST['cur_pass']) && isset($_POST['username'])) {
			$curpass = $_POST['cur_pass'];
			$username = $_POST['username'];

			$app->info->ChangeUsername($curpass, $username);
		}
	}
	include_once 'includes/user_sidebar.html';
	include_once 'includes/forms/change_name.html';
	include_once 'includes/server_sidebar.html';
endif;
require_once 'footer.php';
?>