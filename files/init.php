<?php   

    ini_set('session_gc_maxlifetime', 86400);
    session_set_cookie_params(86400);

	session_start();
	error_reporting(E_ALL);
    ini_set("display_errors", "stdout");

    date_default_timezone_set('Europe/Paris');

    spl_autoload_register(function ($class) {
        @include_once 'class.'.$class.'.php';
    });

    try {
    	$app = new app();
    } catch(Exception $e) {
    	die($e->getMessage());
    }
?>