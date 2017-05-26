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

$app->page->title = 'Level|'.htmlentities($faction->name);

if(isset($_POST['member'])) {
	if($app->user->factionrank >= $faction->levelperm)
		$app->info->ChangeFactionLevel($_POST['member'], $_POST['level']);
	else
		$app->error = "You don't have the right permissions to change the level of a member.";
}

require_once 'header.php';

include_once 'includes/cnr/cnr_faction_sidebar.html';

include_once 'includes/cnr/cnr_faction_level.html';

include_once 'includes/server_sidebar.html';
require_once 'footer.php';
?>