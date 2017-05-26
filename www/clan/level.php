<?php
require_once 'init.php';

if($app->COD == false) header('Location: /');

if($app->user->clan == -1)
{
	Header('Location: /');
	$app->error = "Invalid clan.";
}

$clan = $app->info->GetClanInfoByID($app->user->clan);

$url = null;

$app->page->title = 'Level|'.htmlentities($clan->name);

if(isset($_POST['member'])) {
	if($app->user->crank >= $clan->levelperm)
		$app->info->ChangeClanLevel($_POST['member'], $_POST['level']);
	else
		$app->error = "You don't have the right permissions to change the level of a member.";
}

require_once 'header.php';

include_once 'includes/cod/cod_clan_sidebar.html';

include_once 'includes/cod/cod_clan_level.html';

include_once 'includes/server_sidebar.html';
require_once 'footer.php';
?>