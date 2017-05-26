<?php
require_once 'init.php';

if($app->user->admin >= $app->config['aka']) $app->page->title = "User statistics";
else $app->page->title = "No permissions";

if(!$app->user->LoggedIn) header('Location: /');

require_once 'header.php';

include_once 'includes/admin_sidebar.html';


if(!$app->user->LoggedIn || $app->user->admin < $app->config['aka']):
	include_once 'includes/no_permissions.html';

else:
	if(isset($_POST['submit']) && isset($_POST['name']))
	{
		$found = false;
		if($app->info->IsValidUserByName($_POST['name'])) {
			$user = $app->admin->GetAllUserInfo($_POST['name']);
			$found = true;
		}
		else {
			$app->error = "Invalid username.";
		}

		if($found) {
			if($app->COD)
				include_once 'includes/cod/cod_admin_myinfo.html';
		} else {
			if($app->COD)
				include_once 'includes/forms/user_search.html';
		}
	} else {
		if($app->COD)
			include_once 'includes/forms/user_search.html';
	}

endif;
include_once 'includes/server_sidebar.html';
require_once 'footer.php';
?>