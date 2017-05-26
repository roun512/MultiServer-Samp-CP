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

$app->page->title = 'Leave|'.htmlentities($faction->name);

if(isset($_POST['sure']))
	if(strtolower($_POST['sure']) == "yes") $app->info->LeaveFaction();
	

require_once 'header.php';

include_once 'includes/cnr/cnr_faction_sidebar.html';

include_once 'includes/cnr/cnr_faction_leave.html';

include_once 'includes/server_sidebar.html';
require_once 'footer.php';
?>