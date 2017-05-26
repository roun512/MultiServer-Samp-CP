<?php
require_once 'init.php';

if(isset($_GET['r']))
{
	if($_GET['r'] == 'players')
	{
		echo $app->functions->GetGWPlayerCount() + $app->functions->GetCODPlayerCount();
		die();
	}
}
echo "Invalid request.";
?>