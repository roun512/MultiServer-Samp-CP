<?php
require_once 'init.php';

if($app->user->LoggedIn) $app->page->title = "Change Password";
else $app->page->title = "No permissions";

require_once 'header.php';

if(!$app->user->LoggedIn):
	include_once 'includes/no_permissions.html';
else:


	if(isset($_POST['submit'])) {
		if(isset($_POST['cur_pass']) && isset($_POST['new_pass']) && isset($_POST['new_pass2'])) {
			$curpass = $_POST['cur_pass'];
			$newpass = $_POST['new_pass'];
			$newpass2 = $_POST['new_pass2'];

			if($newpass === $newpass2) {
				if($app->COD || $app->GW) $curpass = $app->functions->EncryptPassword($curpass);
				$app->info->ChangePassword($curpass, $newpass);
			} else {
				$app->error = "The 2 passwords you entered doesn't match each other.";
			}
		} else {
			$app->error = "You left one or more of the fields empty.";
		}
	}
	include_once 'includes/user_sidebar.html';
	include_once 'includes/forms/change_pass.html';
	include_once 'includes/server_sidebar.html';
endif;
require_once 'footer.php';
?>