<?php
require_once 'init.php';
if($app->user->LoggedIn && $app->user->admin > 0) $app->page->title = "Home";
else header('Location: /');
require_once 'header.php';

include_once 'includes/admin_sidebar.html';

include_once 'includes/forms/admin_msg.html';

include_once 'includes/server_sidebar.html';
require_once 'footer.php';
?>