<?php
	require_once 'init.php';
	if($app->user->LoggedIn) $app->page->title = "Home";
	else $app->page->title = "Sign in";
	require_once 'header.php';

	if(!$app->user->LoggedIn):
?>
<div class="login">
	<div class="login_box">
		<form method="POST" action="?login" role="form">
			<div class="login_form">
			<?php if($app->error != null): ?>
			<small style="color:red;"><?=$app->error;?></small>
			<?php endif; ?>
			<?php if($app->success != null): ?>
			<small style="color:green;"><?=$app->success;?></small>
			<?php endif; ?>
				<div class="row">
					<div class="form-group">
						<input type="text" name="username" class="field" placeholder="Enter your in-game username.." value="" />
						<input type="password" name="password" class="field" placeholder="Enter your in-game password.." value="" />
					</div>
				</div>
				<div class="row">
					<div class="form-group">
						<select class="form-control" name="server">
							<option value="cod">Call of Duty Global Warfare</option>
							<option value="cnr">Advance Cops 'n' Robbers</option>
							<option value="gw">Los Santos Gang Wars</option>
						</select>
					</div>
				</div>
				<div class="row">
					<input type="submit" name="submit" class="submit" value="Login"></input>
				</div>
			</div>
		</form>
	</div>
</div>
<?php else:
	$user = $app->info->GetUserInfoByID($app->user->id);
	if(isset($_GET['u'])) {
		if($app->info->IsValidUserByID($_GET['u'])) {
			$user = $app->info->GetUserInfoByID($_GET['u']);
		}
		else {
			$user = $app->info->GetUserInfoByID($app->user->id);
			$app->error = "Invalid user ID.";
		}
	} elseif(isset($_GET['name'])) {
		if($app->info->IsValidUserByName($_GET['name'])) {
			$user = $app->info->GetUserInfoByName($_GET['name']);
		}
		else {
			$user = $app->info->GetUserInfoByID($app->user->id);
			$app->error = "Invalid username.";
		}
	}

	if(isset($_GET['404'])) $app->error .= "The page you are trying to access is not found.";

	include_once 'includes/user_sidebar.html';

	if($app->CNR){
		include_once 'includes/cnr/cnr_myinfo.html';
	} elseif($app->COD){
		include_once 'includes/cod/cod_myinfo.html';
	} elseif($app->GW){
		include_once 'includes/gw/gw_myinfo.html';
	}
	include_once 'includes/server_sidebar.html';

	require_once 'footer.php';
	endif;
?>