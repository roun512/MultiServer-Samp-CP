<?php
require_once 'init.php';

if($app->CNR == false) header('Location: /');

if($app->user->faction == -1 && isset($_GET['id']) == false)
{
	Header('Location: /');
	$app->error = "Invalid faction.";
}

$faction = $app->info->GetFactionInfoByID($app->user->faction);

$url = null;

if(isset($_GET['id'])) {
	if($app->info->IsValidFaction($_GET['id'])) {
		$faction = $app->info->GetFactionInfoByID($_GET['id']);
		$url = '?id='.$_GET['id'];
	} else {
		$app->error = "Invalid faction.";
	}
}

$app->page->title = 'Members|'.htmlentities($faction->name);

require_once 'header.php';

include_once 'includes/cnr/cnr_faction_sidebar.html';

include_once 'includes/cnr/cnr_faction_members.html';

include_once 'includes/server_sidebar.html';
require_once 'footer.php';
?>