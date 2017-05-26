<?php

require_once 'init.php';

$st = $app->coddb->prepare("SELECT (u.activity - a.activity) as activity, u.name, u.id FROM users u LEFT JOIN activity a ON u.id = a.id WHERE u.adminlevel > 4 ORDER BY activity DESC");
$st->execute();
$result = $st->fetchAll();

foreach ($result as $admin) {
	echo $admin->name . " - " . $app->functions->CalculateActivity($admin->activity) . "<br/>";
}

?>