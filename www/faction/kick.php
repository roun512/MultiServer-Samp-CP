<?php
require_once 'init.php';

if($app->CNR == false) header('Location: /');

if($app->user->faction == -1)
{
	Header('Location: /');
	$app->error = "Invalid faction.";
}

$faction = $app->info->GetFactionInfoByID($app->user->faction);

$url = null;

$app->page->title = 'Kick|'.htmlentities($faction->name);

if(isset($_POST['member'])) {
	if($app->user->factionrank >= $faction->kickperm)
		$app->info->KickFromFaction($_POST['member']);
	else
		$app->error = "You don't have the right permissions to kick a member from the faction.";
}

require_once 'header.php';

include_once 'includes/cnr/cnr_faction_sidebar.html';

include_once 'includes/cnr/cnr_faction_kick.html';

include_once 'includes/server_sidebar.html';
require_once 'footer.php';
?>