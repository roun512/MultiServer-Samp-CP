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

$app->page->title = 'Kick|'.htmlentities($clan->name);

if(isset($_POST['member'])) {
	if($app->user->crank >= $clan->levelperm)
		$app->info->KickFromClan($_POST['member']);
	else
		$app->error = "You don't have the right permissions to kick a member from the clan.";
}

require_once 'header.php';

include_once 'includes/cod/cod_clan_sidebar.html';

include_once 'includes/cod/cod_clan_kick.html';

include_once 'includes/server_sidebar.html';
require_once 'footer.php';
?>