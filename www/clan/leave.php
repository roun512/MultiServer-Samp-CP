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

$app->page->title = 'Leave|'.htmlentities($clan->name);

if(isset($_POST['sure']))
	if(strtolower($_POST['sure']) == "yes") $app->info->LeaveClan();
	

require_once 'header.php';

include_once 'includes/cod/cod_clan_sidebar.html';

include_once 'includes/cod/cod_clan_leave.html';

include_once 'includes/server_sidebar.html';
require_once 'footer.php';
?>