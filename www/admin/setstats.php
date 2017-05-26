<?php
require_once 'init.php';

if($app->user->admin >= $app->config['setstats']) $app->page->title = "Set Stats";
else $app->page->title = "No permissions";

if(!$app->user->LoggedIn) header('Location: /');

require_once 'header.php';

include_once 'includes/admin_sidebar.html';

if(!$app->user->LoggedIn || $app->user->admin < $app->config['setstats']):
	include_once 'includes/no_permissions.html';
else:

	$result = null;
	if(isset($_POST['get_stats'])) {
		if(isset($_POST['username'])) {
			$username = $_POST['username'];
			if($app->info->IsValidUserByName($username))
				$result = $app->info->GetUserInfoByName($username);
			else
				$app->error = "The username you entered is doesn't exist.";
		} else {
			$app->error = "You left one or more of the fields empty.";
		}
	} elseif (isset($_POST['submit'])) {
		if($app->COD)
			$app->admin->SetStats($_POST['username'], $_POST['score'], $_POST['money'], $_POST['kills'], $_POST['deaths']);
		elseif($app->CNR)
			$app->admin->SetStats($_POST['username'], $_POST['pscore'], $_POST['cscore'], $_POST['money'], $_POST['bmoney'], $_POST['arrests'], $_POST['arrested']);
	}
	if($app->CNR){
		include_once 'includes/forms/cnr_setstats.html';
	}
	elseif($app->COD) {
		include_once 'includes/forms/cod_setstats.html';
	} elseif($app->GW) {
		include_once 'includes/forms/gw_setstats.html';
	}
endif;
include_once 'includes/server_sidebar.html';
require_once 'footer.php';
?>