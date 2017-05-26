<?php
require_once 'init.php';

if($app->user->admin >= $app->config['setvip']) $app->page->title = "Set VIP";
else $app->page->title = "No permissions";

if(!$app->user->LoggedIn) header('Location: /');

require_once 'header.php';

include_once 'includes/admin_sidebar.html';

if(!$app->user->LoggedIn || $app->user->admin < $app->config['setvip']):
	include_once 'includes/no_permissions.html';
else:
	if(isset($_POST['submit'])) {
		if(isset($_POST['name']) && isset($_POST['level'])) {
			$name = trim($_POST['name']);
			$level = $_POST['level'];
			$months = (isset($_POST['months']) ? ($_POST['months']) : (0));

			if(is_numeric($level))
				$app->admin->SetVIP($name, $level, $months);
			else
				$app->error = "The VIP level you entered is invalid.";
		} else {
			$app->error = "You left one or more of the fields empty.";
		}
	}
	if($app->COD)
		include_once 'includes/forms/cod_setvip.html';
	else
		include_once 'includes/forms/gw_setvip.html';

endif;
include_once 'includes/server_sidebar.html';
require_once 'footer.php';
?>