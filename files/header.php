<!DOCTYPE html>
<html>
<head>
<link rel="shortcut icon" href="/files/images/favicon.ico" type="image/x-icon">
	<title><?=$app->page->title;?> - <?=$app->config['name'];?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	<?php foreach ($app->page->css as $css): ?>
<link rel='stylesheet' href="<?=$app->config['domain'];?>files/css/<?=$css;?>"><?="\n";?>
	<?php endforeach; ?>
<script src="http://code.jquery.com/jquery-1.12.1.js"></script>
<script src="http://cdn.rawgit.com/twbs/bootstrap/v4-dev/dist/js/bootstrap.js"></script>
	<?php foreach ($app->page->js as $js): ?>
<script src="<?=$app->config['domain'];?>files/js/<?=$js;?>"></script><?="\n";?>
	<?php endforeach; ?>
</head>
<body>
<?php if($app->user->LoggedIn) require_once 'page_header.php'; ?>