<?php
require_once 'init.php';

if($app->COD == false) header('Location: /');

if($app->user->clan == -1 && isset($_GET['id']) == false)
{
	Header('Location: /');
	$app->error = "Invalid clan.";
}

$clan = $app->info->GetClanInfoByID($app->user->clan);

$url = null;

if(isset($_GET['id'])) {
	if($app->info->IsValidClan($_GET['id'])) {
		$clan = $app->info->GetClanInfoByID($_GET['id']);
		$url = '?id='.$_GET['id'];
	} else {
		$app->error = "Invalid clan.";
	}
}

$app->page->title = 'Members|'.htmlentities($clan->name);

require_once 'header.php';

include_once 'includes/cod/cod_clan_sidebar.html';

include_once 'includes/cod/cod_clan_members.html';

include_once 'includes/server_sidebar.html';
require_once 'footer.php';
?>