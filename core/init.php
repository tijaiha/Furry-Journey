<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE );

session_start();
spl_autoload_register(function($class){
	require_once 'classes/' . $class . '.php';
});

require_once 'functions/sanitize.php';